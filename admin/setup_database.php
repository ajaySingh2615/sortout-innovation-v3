<?php
require '../includes/db_connect.php';

echo "<h1>Database Setup</h1>";

// Create blogs table with the simplified structure
$blogs_table = "
CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    excerpt TEXT,
    categories TEXT,
    seo_title VARCHAR(255),
    meta_description TEXT,
    focus_keyword VARCHAR(255),
    slug VARCHAR(255),
    image_url VARCHAR(255),
    image_alt VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Create users table
$users_table = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin', 'super_admin') DEFAULT 'user',
    status ENUM('pending', 'active', 'inactive') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Execute queries
$success = true;
$errors = [];

if (!$conn->query($blogs_table)) {
    $success = false;
    $errors[] = "Error creating blogs table: " . $conn->error;
}

if (!$conn->query($users_table)) {
    $success = false;
    $errors[] = "Error creating users table: " . $conn->error;
}

// Insert sample data if tables were created successfully
if ($success) {
    // Check if blogs table is empty
    $result = $conn->query("SELECT COUNT(*) as count FROM blogs");
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        // Sample categories
        $categoriesJson = json_encode(['Digital Marketing', 'Business Strategy']);
        
        // Insert sample blog
        $sampleBlog = "INSERT INTO blogs (
            title, 
            content, 
            excerpt, 
            categories,
            seo_title, 
            meta_description, 
            focus_keyword, 
            slug, 
            image_url
        ) VALUES (
            'Welcome to Our Blog', 
            '<p>This is our first blog post. Welcome to our website!</p><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam auctor, nisl ac ultricies lacinia, nisl nisl aliquam nisl, eget aliquam nisl nisl eu nisl. Nullam auctor, nisl ac ultricies lacinia, nisl nisl aliquam nisl, eget aliquam nisl nisl eu nisl.</p>', 
            'This is our first blog post. Welcome to our website!',
            '$categoriesJson',
            'Welcome to Our Company Blog - Latest Updates & News',
            'Stay up to date with the latest news, updates, and insights from our team on our official company blog.',
            'welcome,blog,company',
            'welcome-to-our-blog',
            '/images/blog/sample.jpg'
        )";
        
        if (!$conn->query($sampleBlog)) {
            echo "<div>Failed to insert sample blog: " . $conn->error . "</div>";
        } else {
            echo "<div>Added sample blog post</div>";
        }
        
        // Insert second sample blog
        $categoriesJson2 = json_encode(['Innovation', 'Industry News']);
        
        $sampleBlog2 = "INSERT INTO blogs (
            title, 
            content, 
            excerpt, 
            categories,
            seo_title, 
            meta_description, 
            focus_keyword, 
            slug, 
            image_url
        ) VALUES (
            'How to Improve Your Digital Marketing Strategy', 
            '<p>In today's competitive digital landscape, having an effective digital marketing strategy is crucial for business success. This blog post explores key tactics to enhance your online presence.</p><p>From SEO optimization to social media engagement, we cover essential strategies that can help your business thrive in the digital world.</p>', 
            'Discover effective ways to improve your digital marketing strategy and boost your online presence.',
            '$categoriesJson2',
            'Top Digital Marketing Strategies to Boost Your Online Presence in 2023',
            'Learn about the most effective digital marketing strategies to enhance your brand visibility, engage customers, and drive conversions in 2023.',
            'digital marketing,strategy,online presence',
            'improve-digital-marketing-strategy',
            '/images/blog/marketing.jpg'
        )";
        
        if (!$conn->query($sampleBlog2)) {
            echo "<div>Failed to insert second sample blog: " . $conn->error . "</div>";
        } else {
            echo "<div>Added second sample blog post</div>";
        }
        
        // Insert super admin user
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $adminUser = "INSERT INTO users (username, email, password, role, status) 
                  VALUES ('admin', 'admin@example.com', '$hashed_password', 'super_admin', 'active')";
        
        if (!$conn->query($adminUser)) {
            echo "<div>Failed to insert admin user: " . $conn->error . "</div>";
        } else {
            echo "<div>Added admin user</div>";
        }
    } else {
        echo "<div>Blogs table already contains data. Skipped sample data insertion.</div>";
    }
}

// Output results
if ($success) {
    echo "<h2>Database Setup Complete</h2>";
    echo "<p>Tables have been created successfully.</p>";
} else {
    echo "<h2>Database Setup Encountered Issues</h2>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
}

// Display table structure
echo "<h3>Blogs Table Structure</h3>";
$structure = $conn->query("DESCRIBE blogs");
if ($structure) {
    echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($field = $structure->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $field['Field'] . "</td>";
        echo "<td>" . $field['Type'] . "</td>";
        echo "<td>" . $field['Null'] . "</td>";
        echo "<td>" . $field['Key'] . "</td>";
        echo "<td>" . $field['Default'] . "</td>";
        echo "<td>" . $field['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Display sample data
echo "<h3>Sample Blog Data</h3>";
$blogs = $conn->query("SELECT id, title, excerpt, categories FROM blogs LIMIT 5");
if ($blogs) {
    if ($blogs->num_rows > 0) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Excerpt</th><th>Categories</th></tr>";
        while ($blog = $blogs->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $blog['id'] . "</td>";
            echo "<td>" . $blog['title'] . "</td>";
            echo "<td>" . $blog['excerpt'] . "</td>";
            echo "<td>" . $blog['categories'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No blogs found in the database.</p>";
    }
}

echo "<div style='margin-top: 20px;'>";
echo "<a href='main_dashboard.php' style='padding: 10px; background: #2196F3; color: white; text-decoration: none;'>Go to Dashboard</a>";
echo "</div>";
?> 