<?php
// Process export request
if (isset($_POST['export_csv'])) {
    // Get filters from form
    $filters = [];
    
    // Status filter
    if (isset($_POST['status']) && !empty($_POST['status'])) {
        $filters['status'] = $_POST['status'];
    }
    
    // Category filter
    if (isset($_POST['talent_category']) && !empty($_POST['talent_category'])) {
        $filters['talent_category'] = $_POST['talent_category'];
    }
    
    // Date range filter
    if (isset($_POST['date_range']) && !empty($_POST['date_range'])) {
        $dateRange = generateDateRange($_POST['date_range']);
        if (!empty($dateRange['from'])) {
            $filters['date_from'] = $dateRange['from'];
            $filters['date_to'] = $dateRange['to'];
        }
    } else {
        // Custom date range
        if (isset($_POST['date_from']) && !empty($_POST['date_from'])) {
            $filters['date_from'] = $_POST['date_from'];
        }
        
        if (isset($_POST['date_to']) && !empty($_POST['date_to'])) {
            $filters['date_to'] = $_POST['date_to'];
        }
    }
    
    // Get data with filters (no pagination for export)
    $registrations = getTalentRegistrations($filters);
    
    if (count($registrations) > 0) {
        // Clear any previous output
        ob_clean();
        
        // Generate filename with date
        $filename = 'talent_registrations_' . date('Y-m-d_His') . '.xls';
        
        // Set headers for Excel-friendly download (Excel 97-2003 format)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Pragma: public');
        
        // Add Excel XML header to make it more Excel-friendly
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
        echo '<Worksheet ss:Name="Talent Registrations">' . "\n";
        echo '<Table>' . "\n";
        
        // Get column names from form
        $columns = isset($_POST['columns']) ? $_POST['columns'] : ['id', 'full_name', 'contact_number', 'gender', 'talent_category', 'app_name', 'status', 'created_at'];
        
        // Define column headers
        $columnHeaders = [
            'id' => 'ID',
            'full_name' => 'Full Name',
            'contact_number' => 'Contact Number',
            'gender' => 'Gender',
            'talent_category' => 'Talent Category',
            'app_name' => 'App Name',
            'app_id' => 'App ID',
            'status' => 'Status',
            'notes' => 'Notes',
            'created_at' => 'Registration Date',
            'updated_at' => 'Last Updated'
        ];
        
        // Output header row
        echo '<Row>' . "\n";
        foreach ($columns as $column) {
            echo '<Cell><Data ss:Type="String">' . htmlspecialchars($columnHeaders[$column]) . '</Data></Cell>' . "\n";
        }
        echo '</Row>' . "\n";
        
        // Output data rows
        foreach ($registrations as $row) {
            echo '<Row>' . "\n";
            foreach ($columns as $column) {
                $value = '';
                if ($column === 'created_at' || $column === 'updated_at') {
                    $value = date('Y-m-d H:i:s', strtotime($row[$column]));
                    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($value) . '</Data></Cell>' . "\n";
                } else {
                    $value = $row[$column] ?? '';
                    echo '<Cell><Data ss:Type="String">' . htmlspecialchars($value) . '</Data></Cell>' . "\n";
                }
            }
            echo '</Row>' . "\n";
        }
        
        // Close Excel XML
        echo '</Table>' . "\n";
        echo '</Worksheet>' . "\n";
        echo '</Workbook>';
        
        exit;
    } else {
        showError("No data found for export!");
    }
}

// Get all talent categories for filtering
$categories = getTalentCategories(true);
?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Export Registrations</h1>
        <a href="?page=registrations" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Registrations
        </a>
    </div>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Export to Excel</h6>
        </div>
        <div class="card-body">
            <form action="" method="post" class="row g-3">
                <!-- Status Filter -->
                <div class="col-md-4">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                
                <!-- Category Filter -->
                <div class="col-md-4">
                    <label for="talent_category" class="form-label">Talent Category</label>
                    <select name="talent_category" id="talent_category" class="form-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['name']; ?>">
                                <?php echo $category['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Date Range Filter -->
                <div class="col-md-4">
                    <label for="date_range" class="form-label">Date Range Preset</label>
                    <select name="date_range" id="date_range" class="form-select">
                        <option value="">Custom Date Range</option>
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month">This Month</option>
                        <option value="year">This Year</option>
                    </select>
                </div>
                
                <!-- Date From Filter -->
                <div class="col-md-6">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" name="date_from" id="date_from" class="form-control">
                </div>
                
                <!-- Date To Filter -->
                <div class="col-md-6">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" name="date_to" id="date_to" class="form-control">
                </div>
                
                <!-- CSV Columns -->
                <div class="col-12 mt-4">
                    <label class="form-label fw-bold">Select Columns to Include</label>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="columns[]" id="col_id" value="id" checked>
                                <label class="form-check-label" for="col_id">ID</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="columns[]" id="col_full_name" value="full_name" checked>
                                <label class="form-check-label" for="col_full_name">Full Name</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="columns[]" id="col_contact_number" value="contact_number" checked>
                                <label class="form-check-label" for="col_contact_number">Contact Number</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="columns[]" id="col_gender" value="gender" checked>
                                <label class="form-check-label" for="col_gender">Gender</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="columns[]" id="col_talent_category" value="talent_category" checked>
                                <label class="form-check-label" for="col_talent_category">Talent Category</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="columns[]" id="col_app_name" value="app_name" checked>
                                <label class="form-check-label" for="col_app_name">App Name</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="columns[]" id="col_app_id" value="app_id">
                                <label class="form-check-label" for="col_app_id">App ID</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="columns[]" id="col_status" value="status" checked>
                                <label class="form-check-label" for="col_status">Status</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="columns[]" id="col_notes" value="notes">
                                <label class="form-check-label" for="col_notes">Notes</label>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-center">
                            <div>
                                <button type="button" id="selectAllColumns" class="btn btn-sm btn-outline-primary mb-2">Select All</button>
                                <button type="button" id="deselectAllColumns" class="btn btn-sm btn-outline-secondary">Deselect All</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="col-12 mt-4">
                    <button type="submit" name="export_csv" class="btn btn-success">
                        <i class="fas fa-file-excel me-2"></i> Export to Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Export Templates Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Saved Export Templates</h6>
        </div>
        <div class="card-body">
            <div class="list-group">
                <a href="javascript:void(0)" onclick="loadTemplate('all')" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">All Registrations</h5>
                        <small>Default Template</small>
                    </div>
                    <p class="mb-1">Export all registrations with all columns</p>
                </a>
                <a href="javascript:void(0)" onclick="loadTemplate('pending')" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">Pending Registrations</h5>
                        <small>Status Template</small>
                    </div>
                    <p class="mb-1">Export all pending registrations</p>
                </a>
                <a href="javascript:void(0)" onclick="loadTemplate('today')" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">Today's Registrations</h5>
                        <small>Date Template</small>
                    </div>
                    <p class="mb-1">Export all registrations from today</p>
                </a>
                <a href="javascript:void(0)" onclick="loadTemplate('contact')" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">Contact List</h5>
                        <small>Column Template</small>
                    </div>
                    <p class="mb-1">Export only name and contact information</p>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Scheduler Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Schedule Automated Reports</h6>
        </div>
        <div class="card-body">
            <p class="text-muted mb-4">Set up automated reports to be sent to your email on a recurring basis.</p>
            
            <form action="#" method="post" class="row g-3">
                <div class="col-md-6">
                    <label for="schedule_email" class="form-label">Email Address</label>
                    <input type="email" name="schedule_email" id="schedule_email" class="form-control" placeholder="Enter email address">
                </div>
                
                <div class="col-md-6">
                    <label for="schedule_frequency" class="form-label">Frequency</label>
                    <select name="schedule_frequency" id="schedule_frequency" class="form-select">
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
                
                <div class="col-md-6">
                    <label for="schedule_time" class="form-label">Send Time</label>
                    <input type="time" name="schedule_time" id="schedule_time" class="form-control" value="08:00">
                </div>
                
                <div class="col-md-6">
                    <label for="schedule_format" class="form-label">Format</label>
                    <select name="schedule_format" id="schedule_format" class="form-select">
                        <option value="csv">CSV</option>
                        <option value="excel">Excel</option>
                        <option value="pdf">PDF</option>
                    </select>
                </div>
                
                <div class="col-12 mt-4">
                    <button type="submit" name="schedule_report" class="btn btn-primary">
                        <i class="fas fa-calendar-alt me-2"></i> Schedule Report
                    </button>
                    <div class="form-text mt-2">
                        <i class="fas fa-info-circle"></i> This feature will be available in a future update.
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Toggle date range fields based on preset selection
        $('#date_range').change(function() {
            if ($(this).val() === '') {
                $('#date_from, #date_to').prop('disabled', false);
            } else {
                $('#date_from, #date_to').prop('disabled', true);
            }
        });
        
        // Select/Deselect all columns
        $('#selectAllColumns').click(function() {
            $('input[name="columns[]"]').prop('checked', true);
        });
        
        $('#deselectAllColumns').click(function() {
            $('input[name="columns[]"]').prop('checked', false);
        });
    });
    
    // Load saved template
    function loadTemplate(template) {
        // Reset all form fields
        $('select[name="status"]').val('');
        $('select[name="talent_category"]').val('');
        $('select[name="date_range"]').val('');
        $('input[name="date_from"]').val('').prop('disabled', false);
        $('input[name="date_to"]').val('').prop('disabled', false);
        $('input[name="columns[]"]').prop('checked', false);
        
        // Apply template-specific settings
        switch (template) {
            case 'all':
                // Select all columns
                $('input[name="columns[]"]').prop('checked', true);
                break;
                
            case 'pending':
                // Set status to pending and select basic columns
                $('select[name="status"]').val('pending');
                $('#col_id, #col_full_name, #col_contact_number, #col_talent_category, #col_created_at').prop('checked', true);
                break;
                
            case 'today':
                // Set date range to today and select basic columns
                $('select[name="date_range"]').val('today');
                $('#date_from, #date_to').prop('disabled', true);
                $('#col_id, #col_full_name, #col_contact_number, #col_talent_category, #col_status').prop('checked', true);
                break;
                
            case 'contact':
                // Select only contact-related columns
                $('#col_full_name, #col_contact_number, #col_gender').prop('checked', true);
                break;
        }
    }
</script> 