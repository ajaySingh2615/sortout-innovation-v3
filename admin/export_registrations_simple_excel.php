<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database connection
require_once __DIR__ . '/../includes/db_connect.php';

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'super_admin')) {
    header("Location: ../auth/login.php");
    exit();
}

try {
    // Get filter parameters
    $search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
    $status = mysqli_real_escape_string($conn, $_GET['status'] ?? '');
    $professional = mysqli_real_escape_string($conn, $_GET['professional'] ?? '');
    $category = mysqli_real_escape_string($conn, $_GET['category'] ?? '');
    $city = mysqli_real_escape_string($conn, $_GET['city'] ?? '');
    $dateRange = mysqli_real_escape_string($conn, $_GET['date_range'] ?? '');
    $influencerCategory = mysqli_real_escape_string($conn, $_GET['influencer_category'] ?? '');
    $influencerType = mysqli_real_escape_string($conn, $_GET['influencer_type'] ?? '');
    $workType = mysqli_real_escape_string($conn, $_GET['work_type'] ?? '');
    $paymentRange = mysqli_real_escape_string($conn, $_GET['payment_range'] ?? '');

    // Build the query with same logic as fetch_registrations.php
    $query = "SELECT 
                id, name, age, gender, phone, email, city, professional, 
                category, role, approval_status, created_at, experience,
                followers, language, current_salary, is_visible,
                influencer_category, influencer_type, expected_payment, work_type_preference,
                instagram_profile, resume_url
              FROM clients WHERE 1=1";
    
    $conditions = [];

    // Add search condition
    if ($search) {
        $conditions[] = "(
            name LIKE '%$search%' OR 
            phone LIKE '%$search%' OR 
            email LIKE '%$search%' OR 
            city LIKE '%$search%' OR
            category LIKE '%$search%' OR 
            role LIKE '%$search%'
        )";
    }

    // Add filters (same logic as fetch_registrations.php)
    if ($status) {
        $conditions[] = "approval_status = '$status'";
    }
    if ($professional) {
        $conditions[] = "professional = '$professional'";
    }
    if ($category) {
        $conditions[] = "category = '$category'";
    }
    if ($city) {
        $conditions[] = "city = '$city'";
    }
    if ($influencerCategory) {
        $conditions[] = "influencer_category = '$influencerCategory'";
    }
    if ($influencerType) {
        $conditions[] = "influencer_type = '$influencerType'";
    }
    if ($workType) {
        $conditions[] = "work_type_preference = '$workType'";
    }
    if ($paymentRange) {
        $conditions[] = "expected_payment = '$paymentRange'";
    }

    // Add date range filter
    if ($dateRange) {
        switch ($dateRange) {
            case 'since_last_visit':
                // Get admin's last seen timestamp
                $admin_id = $_SESSION['user_id'] ?? $_SESSION['username'];
                $lastSeenQuery = "SELECT last_seen_timestamp FROM admin_alert_tracking WHERE admin_id = ?";
                $lastSeenStmt = $conn->prepare($lastSeenQuery);
                $lastSeenStmt->bind_param("s", $admin_id);
                $lastSeenStmt->execute();
                $lastSeenResult = $lastSeenStmt->get_result();
                $lastSeen = $lastSeenResult->fetch_assoc();
                
                if ($lastSeen) {
                    $conditions[] = "created_at > '" . $lastSeen['last_seen_timestamp'] . "'";
                } else {
                    $conditions[] = "created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
                }
                break;
            case 'today':
                $conditions[] = "DATE(created_at) = CURDATE()";
                break;
            case 'yesterday':
                $conditions[] = "DATE(created_at) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
                break;
            case 'last_24h':
                $conditions[] = "created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
                break;
            case 'last_7_days':
                $conditions[] = "created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
                break;
            case 'last_30_days':
                $conditions[] = "created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
                break;
            case 'this_month':
                $conditions[] = "YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())";
                break;
            case 'last_month':
                $conditions[] = "YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH))";
                break;
        }
    }

    // Combine conditions
    if (!empty($conditions)) {
        $query .= " AND " . implode(" AND ", $conditions);
    }

    // Add ordering
    $query .= " ORDER BY created_at DESC";

    // Execute query
    $result = mysqli_query($conn, $query);

    if (!$result) {
        throw new Exception("Database query failed: " . mysqli_error($conn));
    }

    // Generate filename
    $filename = 'registrations_export_' . date('Y-m-d_H-i-s') . '.xls';
    
    // Add filter description to filename if filters are applied
    $filterParts = [];
    if ($status) $filterParts[] = $status;
    if ($professional) $filterParts[] = $professional;
    if ($dateRange) $filterParts[] = $dateRange;
    
    if (!empty($filterParts)) {
        $filename = 'registrations_' . implode('_', $filterParts) . '_' . date('Y-m-d_H-i-s') . '.xls';
    }

    // Set headers for Excel download
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    header('Cache-Control: max-age=1');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: cache, must-revalidate');
    header('Pragma: public');

    // Start Excel content
    echo '<?xml version="1.0"?>';
    echo '<?mso-application progid="Excel.Sheet"?>';
    echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
        xmlns:o="urn:schemas-microsoft-com:office:office"
        xmlns:x="urn:schemas-microsoft-com:office:excel"
        xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
        xmlns:html="http://www.w3.org/TR/REC-html40">';
    
    echo '<DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
        <Title>Registrations Export</Title>
        <Author>SortOut Innovation</Author>
        <LastAuthor>Admin Dashboard</LastAuthor>
        <Created>' . date('Y-m-d\TH:i:s\Z') . '</Created>
        <Company>SortOut Innovation</Company>
        <Version>16.00</Version>
    </DocumentProperties>';

    echo '<OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">
        <AllowPNG/>
    </OfficeDocumentSettings>';

    echo '<ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
        <WindowHeight>13020</WindowHeight>
        <WindowWidth>25600</WindowWidth>
        <WindowTopX>120</WindowTopX>
        <WindowTopY>120</WindowTopY>
        <ProtectStructure>False</ProtectStructure>
        <ProtectWindows>False</ProtectWindows>
    </ExcelWorkbook>';

    // Define styles
    echo '<Styles>
        <Style ss:ID="Default" ss:Name="Normal">
            <Alignment ss:Vertical="Bottom"/>
            <Borders/>
            <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
            <Interior/>
            <NumberFormat/>
            <Protection/>
        </Style>
        <Style ss:ID="Header">
            <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
            <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1"/>
                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1"/>
            </Borders>
            <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="12" ss:Color="#FFFFFF" ss:Bold="1"/>
            <Interior ss:Color="#2563EB" ss:Pattern="Solid"/>
        </Style>
        <Style ss:ID="Data">
            <Alignment ss:Vertical="Center"/>
            <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
            </Borders>
            <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11"/>
        </Style>
        <Style ss:ID="StatusApproved">
            <Alignment ss:Vertical="Center"/>
            <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
            </Borders>
            <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11"/>
            <Interior ss:Color="#D1FAE5" ss:Pattern="Solid"/>
        </Style>
        <Style ss:ID="StatusRejected">
            <Alignment ss:Vertical="Center"/>
            <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
            </Borders>
            <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11"/>
            <Interior ss:Color="#FEE2E2" ss:Pattern="Solid"/>
        </Style>
        <Style ss:ID="StatusPending">
            <Alignment ss:Vertical="Center"/>
            <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
            </Borders>
            <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11"/>
            <Interior ss:Color="#FEF3C7" ss:Pattern="Solid"/>
        </Style>
        <Style ss:ID="AlternateRow">
            <Alignment ss:Vertical="Center"/>
            <Borders>
                <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
                <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E5E7EB"/>
            </Borders>
            <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11"/>
            <Interior ss:Color="#F8FAFC" ss:Pattern="Solid"/>
        </Style>
    </Styles>';

    echo '<Worksheet ss:Name="Registrations Export">
        <Table ss:ExpandedColumnCount="21" ss:ExpandedRowCount="' . (mysqli_num_rows($result) + 5) . '" x:FullColumns="1" x:FullRows="1" ss:DefaultColumnWidth="60" ss:DefaultRowHeight="15">';

    // Set column widths
    echo '<Column ss:Index="1" ss:AutoFitWidth="0" ss:Width="50"/>'; // ID
    echo '<Column ss:Index="2" ss:AutoFitWidth="0" ss:Width="120"/>'; // Name
    echo '<Column ss:Index="3" ss:AutoFitWidth="0" ss:Width="50"/>'; // Age
    echo '<Column ss:Index="4" ss:AutoFitWidth="0" ss:Width="70"/>'; // Gender
    echo '<Column ss:Index="5" ss:AutoFitWidth="0" ss:Width="100"/>'; // Phone
    echo '<Column ss:Index="6" ss:AutoFitWidth="0" ss:Width="150"/>'; // Email
    echo '<Column ss:Index="7" ss:AutoFitWidth="0" ss:Width="100"/>'; // City
    echo '<Column ss:Index="8" ss:AutoFitWidth="0" ss:Width="100"/>'; // Professional Type
    echo '<Column ss:Index="9" ss:AutoFitWidth="0" ss:Width="150"/>'; // Category/Role
    echo '<Column ss:Index="10" ss:AutoFitWidth="0" ss:Width="80"/>'; // Experience
    echo '<Column ss:Index="11" ss:AutoFitWidth="0" ss:Width="120"/>'; // Languages
    echo '<Column ss:Index="12" ss:AutoFitWidth="0" ss:Width="100"/>'; // Approval Status
    echo '<Column ss:Index="13" ss:AutoFitWidth="0" ss:Width="130"/>'; // Registration Date
    echo '<Column ss:Index="14" ss:AutoFitWidth="0" ss:Width="80"/>'; // Followers
    echo '<Column ss:Index="15" ss:AutoFitWidth="0" ss:Width="100"/>'; // Current Salary
    echo '<Column ss:Index="16" ss:AutoFitWidth="0" ss:Width="150"/>'; // Influencer Category
    echo '<Column ss:Index="17" ss:AutoFitWidth="0" ss:Width="150"/>'; // Influencer Type
    echo '<Column ss:Index="18" ss:AutoFitWidth="0" ss:Width="120"/>'; // Expected Payment
    echo '<Column ss:Index="19" ss:AutoFitWidth="0" ss:Width="150"/>'; // Work Type Preference
    echo '<Column ss:Index="20" ss:AutoFitWidth="0" ss:Width="200"/>'; // Instagram Profile
    echo '<Column ss:Index="21" ss:AutoFitWidth="0" ss:Width="100"/>'; // Visibility Status

    // Header row
    echo '<Row ss:AutoFitHeight="0" ss:Height="20">
        <Cell ss:StyleID="Header"><Data ss:Type="String">ID</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Name</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Age</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Gender</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Phone</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Email</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">City</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Professional Type</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Category/Role</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Experience</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Languages</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Approval Status</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Registration Date</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Followers</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Current Salary</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Influencer Category</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Influencer Type</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Expected Payment</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Work Type Preference</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Instagram Profile</Data></Cell>
        <Cell ss:StyleID="Header"><Data ss:Type="String">Visibility Status</Data></Cell>
    </Row>';

    // Data rows
    $rowNum = 2;
    while ($client = mysqli_fetch_assoc($result)) {
        $isAlternate = ($rowNum % 2 == 0);
        
        // Determine status style
        $statusStyle = 'Data';
        switch ($client['approval_status']) {
            case 'approved':
                $statusStyle = 'StatusApproved';
                break;
            case 'rejected':
                $statusStyle = 'StatusRejected';
                break;
            case 'pending':
                $statusStyle = 'StatusPending';
                break;
        }
        
        $rowStyle = $isAlternate ? 'AlternateRow' : 'Data';
        
        echo '<Row ss:AutoFitHeight="0">';
        echo '<Cell ss:StyleID="' . $rowStyle . '"><Data ss:Type="Number">' . htmlspecialchars($client['id']) . '</Data></Cell>';
        echo '<Cell ss:StyleID="' . $rowStyle . '"><Data ss:Type="String">' . htmlspecialchars($client['name']) . '</Data></Cell>';
        echo '<Cell ss:StyleID="' . $rowStyle . '"><Data ss:Type="Number">' . htmlspecialchars($client['age']) . '</Data></Cell>';
        echo '<Cell ss:StyleID="' . $rowStyle . '"><Data ss:Type="String">' . htmlspecialchars($client['gender']) . '</Data></Cell>';
        echo '<Cell ss:StyleID="' . $rowStyle . '"><Data ss:Type="String">' . htmlspecialchars($client['phone']) . '</Data></Cell>';
        echo '<Cell ss:StyleID="' . $rowStyle . '"><Data ss:Type="String">' . htmlspecialchars($client['email'] ?: 'N/A') . '</Data></Cell>';
        echo '<Cell ss:StyleID="' . $rowStyle . '"><Data ss:Type="String">' . htmlspecialchars($client['city']) . '</Data></Cell>';
        echo '<Cell ss:StyleID="' . $rowStyle . '"><Data ss:Type="String">' . htmlspecialchars($client['professional']) . '</Data></Cell>';
        echo '<Cell ss:StyleID="' . $rowStyle . '"><Data ss:Type="String">' . htmlspecialchars($client['professional'] === 'Artist' ? ($client['category'] ?: 'N/A') : ($client['role'] ?: 'N/A')) . '</Data></Cell>';
        echo '<Cell ss:StyleID="' . $rowStyle . '"><Data ss:Type="String">' . htmlspecialchars($client['experience'] ?: 'N/A') . '</Data></Cell>';
        echo '<Cell ss:StyleID="' . $rowStyle . '"><Data ss:Type="String">' . htmlspecialchars($client['language'] ?: 'N/A') . '</Data></Cell>';
        echo '<Cell ss:StyleID="' . $statusStyle . '"><Data ss:Type="String">' . htmlspecialchars(ucfirst($client['approval_status'])) . '</Data></Cell>';
        echo '<Cell ss:StyleID="' . $rowStyle . '"><Data ss:Type="String">' . htmlspecialchars(date('Y-m-d H:i:s', strtotime($client['created_at']))) . '</Data></Cell>';
        echo '<Cell ss:StyleID="' . $rowStyle . '"><Data ss:Type="String">' . htmlspecialchars($client['followers'] ?: 'N/A') . '</Data></Cell>';
        echo '<Cell ss:StyleID="' . $rowStyle . '"><Data ss:Type="String">' . htmlspecialchars($client['current_salary'] ?: 'N/A') . '</Data></Cell>';
        echo '<Cell ss:StyleID="' . $rowStyle . '"><Data ss:Type="String">' . htmlspecialchars($client['influencer_category'] ?: 'N/A') . '</Data></Cell>';
        echo '<Cell ss:StyleID="' . $rowStyle . '"><Data ss:Type="String">' . htmlspecialchars($client['influencer_type'] ?: 'N/A') . '</Data></Cell>';
        echo '<Cell ss:StyleID="' . $rowStyle . '"><Data ss:Type="String">' . htmlspecialchars($client['expected_payment'] ?: 'N/A') . '</Data></Cell>';
        echo '<Cell ss:StyleID="' . $rowStyle . '"><Data ss:Type="String">' . htmlspecialchars($client['work_type_preference'] ?: 'N/A') . '</Data></Cell>';
        echo '<Cell ss:StyleID="' . $rowStyle . '"><Data ss:Type="String">' . htmlspecialchars($client['instagram_profile'] ?: 'N/A') . '</Data></Cell>';
        echo '<Cell ss:StyleID="' . $rowStyle . '"><Data ss:Type="String">' . htmlspecialchars($client['is_visible'] ? 'Visible' : 'Hidden') . '</Data></Cell>';
        echo '</Row>';
        
        $rowNum++;
    }

    // Add summary information
    $totalRows = $rowNum - 2;
    echo '<Row></Row>'; // Empty row
    echo '<Row>
        <Cell ss:StyleID="Data"><Data ss:Type="String">Export Summary:</Data></Cell>
    </Row>';
    echo '<Row>
        <Cell ss:StyleID="Data"><Data ss:Type="String">Total Records: ' . $totalRows . '</Data></Cell>
    </Row>';
    echo '<Row>
        <Cell ss:StyleID="Data"><Data ss:Type="String">Export Date: ' . date('Y-m-d H:i:s') . '</Data></Cell>
    </Row>';
    echo '<Row>
        <Cell ss:StyleID="Data"><Data ss:Type="String">Exported By: ' . htmlspecialchars($_SESSION['username'] ?? 'Admin') . '</Data></Cell>
    </Row>';

    echo '</Table>
        <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
            <PageSetup>
                <Header x:Margin="0.3"/>
                <Footer x:Margin="0.3"/>
                <PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
            </PageSetup>
            <FitToPage/>
            <Print>
                <FitWidth>1</FitWidth>
                <FitHeight>32767</FitHeight>
            </Print>
            <Selected/>
            <FreezePanes/>
            <FrozenNoSplit/>
            <SplitHorizontal>1</SplitHorizontal>
            <TopRowBottomPane>1</TopRowBottomPane>
            <ActivePane>2</ActivePane>
            <Panes>
                <Pane>
                    <Number>3</Number>
                </Pane>
                <Pane>
                    <Number>2</Number>
                    <ActiveRow>0</ActiveRow>
                </Pane>
            </Panes>
            <ProtectObjects>False</ProtectObjects>
            <ProtectScenarios>False</ProtectScenarios>
        </WorksheetOptions>
    </Worksheet>';

    echo '</Workbook>';

} catch (Exception $e) {
    error_log("Excel export error: " . $e->getMessage());
    
    // Fallback to CSV export
    header("Location: export_registrations.php?" . $_SERVER['QUERY_STRING']);
    exit();
} finally {
    // Close database connection
    if (isset($conn) && $conn) {
        mysqli_close($conn);
    }
}
?> 