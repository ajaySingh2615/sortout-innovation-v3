<?php
require '../includes/db_connect.php';

// Get parameters from request
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Calculate offset
$offset = ($page - 1) * $limit;

// Check if the blog_categories table exists
$tableCheckQuery = "SHOW TABLES LIKE 'blog_categories'";
$tableResult = $conn->query($tableCheckQuery);
$useJoin = ($tableResult && $tableResult->num_rows > 0);

// First, let's check the structure of the blogs table
$structureQuery = "DESCRIBE blogs";
$structureResult = $conn->query($structureQuery);
$hasCategories = false;

if ($structureResult) {
    while ($field = $structureResult->fetch_assoc()) {
        if ($field['Field'] === 'categories') {
            $hasCategories = true;
            break;
        }
    }
}

// Base query depending on table structure
if ($useJoin) {
    // If blog_categories table exists, use a join
    $query = "
        SELECT b.id, b.title, b.content, b.excerpt, b.image_url, b.seo_title, 
               b.meta_description, b.slug, b.focus_keyword, b.created_at,
               GROUP_CONCAT(bc.category_name) as categories
        FROM blogs b
        LEFT JOIN blog_categories bc ON b.id = bc.blog_id
        WHERE 1=1
    ";
    
    // Add search condition if search term is provided
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $query .= " AND (b.title LIKE '%$search%' OR b.content LIKE '%$search%' OR b.excerpt LIKE '%$search%')";
    }
    
    // Add category filter if provided
    if (!empty($category)) {
        $category = $conn->real_escape_string($category);
        $query .= " AND bc.category_name = '$category'";
    }
    
    // Group by blog ID to avoid duplicates
    $query .= " GROUP BY b.id";
    
    // Count total rows for pagination (without LIMIT)
    $countQuery = "SELECT COUNT(DISTINCT b.id) as total FROM blogs b LEFT JOIN blog_categories bc ON b.id = bc.blog_id WHERE 1=1";
    if (!empty($search)) {
        $countQuery .= " AND (b.title LIKE '%$search%' OR b.content LIKE '%$search%' OR b.excerpt LIKE '%$search%')";
    }
    if (!empty($category)) {
        $countQuery .= " AND bc.category_name = '$category'";
    }
} else {
    // If no join table, use direct query on blogs table
    $query = "SELECT * FROM blogs WHERE 1=1";
    
    // Add search condition if search term is provided
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $query .= " AND (title LIKE '%$search%' OR content LIKE '%$search%'";
        
        // Check if excerpt column exists
        if ($structureResult->num_rows > 0) {
            $structureResult->data_seek(0);
            while ($field = $structureResult->fetch_assoc()) {
                if ($field['Field'] === 'excerpt') {
                    $query .= " OR excerpt LIKE '%$search%'";
                    break;
                }
            }
        }
        
        $query .= ")";
    }
    
    // Add category filter if provided and if categories column exists
    if (!empty($category) && $hasCategories) {
        $category = $conn->real_escape_string($category);
        // Assuming categories are stored as JSON or comma-separated string
        $query .= " AND (categories LIKE '%$category%')";
    }
    
    // Count total rows for pagination (without LIMIT)
    $countQuery = "SELECT COUNT(*) as total FROM blogs WHERE 1=1";
    if (!empty($search)) {
        $countQuery .= " AND (title LIKE '%$search%' OR content LIKE '%$search%'";
        
        // Check if excerpt column exists
        if ($structureResult->num_rows > 0) {
            $structureResult->data_seek(0);
            while ($field = $structureResult->fetch_assoc()) {
                if ($field['Field'] === 'excerpt') {
                    $countQuery .= " OR excerpt LIKE '%$search%'";
                    break;
                }
            }
        }
        
        $countQuery .= ")";
    }
    
    if (!empty($category) && $hasCategories) {
        $category = $conn->real_escape_string($category);
        $countQuery .= " AND (categories LIKE '%$category%')";
    }
}

// Order by creation date (newest first)
$query .= " ORDER BY created_at DESC";

// Execute count query
$countResult = $conn->query($countQuery);
if (!$countResult) {
    // Handle database error
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $conn->error
    ]);
    exit;
}

$countRow = $countResult->fetch_assoc();
$totalRows = $countRow['total'];

// Add LIMIT and OFFSET to the main query
$query .= " LIMIT $limit OFFSET $offset";

// Execute main query
$result = $conn->query($query);
if (!$result) {
    // Handle database error
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $conn->error
    ]);
    exit;
}

// Prepare data for response
$blogs = [];
while ($row = $result->fetch_assoc()) {
    // Format date if created_at exists
    if (isset($row['created_at'])) {
        $date = new DateTime($row['created_at']);
        $row['formatted_date'] = $date->format('M d, Y');
    } else {
        $row['formatted_date'] = 'N/A';
    }
    
    // Handle categories
    if (isset($row['categories'])) {
        if ($useJoin) {
            // For joined tables, categories come as comma-separated string
            $row['categories'] = $row['categories'] ? explode(',', $row['categories']) : [];
        } else if ($hasCategories) {
            // For direct table, need to check format (JSON or comma-separated)
            if (strpos($row['categories'], '[') === 0) {
                // JSON format
                $row['categories'] = json_decode($row['categories'], true) ?: [];
            } else {
                // Comma-separated format
                $row['categories'] = explode(',', $row['categories']);
            }
        }
    } else {
        $row['categories'] = [];
    }
    
    // Generate excerpt if not present
    if (!isset($row['excerpt']) && isset($row['content'])) {
        $row['excerpt'] = substr(strip_tags($row['content']), 0, 150) . '...';
    }
    
    // Add to blogs array
    $blogs[] = $row;
}

// Calculate pagination data
$totalPages = ceil($totalRows / $limit);

// Prepare response
$response = [
    'success' => true,
    'blogs' => $blogs,
    'pagination' => [
        'current_page' => $page,
        'total_pages' => $totalPages,
        'limit' => $limit,
        'total_rows' => $totalRows
    ]
];

// Send response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
