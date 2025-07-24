<?php
/**
 * City Normalization Script
 * 
 * This script helps clean up and standardize city names in the database
 * Run this once after changing the city field from ENUM to VARCHAR
 */

require_once '../includes/db_connect.php';

// Function to normalize city names
function normalizeCity($city) {
    // Trim whitespace
    $city = trim($city);
    
    // Convert to proper case (first letter capital)
    $city = ucwords(strtolower($city));
    
    // Handle common variations
    $replacements = [
        'New Delhi' => 'Delhi',
        'Bengaluru' => 'Bangalore',
        'Mumbai City' => 'Mumbai',
        'Hyderabad City' => 'Hyderabad',
        'Chennai City' => 'Chennai',
        'Kolkata City' => 'Kolkata',
        'Gurgaon' => 'Gurugram',
        'Pondicherry' => 'Puducherry'
    ];
    
    return isset($replacements[$city]) ? $replacements[$city] : $city;
}

try {
    // Get all unique cities from the database
    $query = "SELECT DISTINCT city FROM clients WHERE city IS NOT NULL AND city != ''";
    $result = $conn->query($query);
    
    if ($result) {
        $updates = [];
        
        while ($row = $result->fetch_assoc()) {
            $originalCity = $row['city'];
            $normalizedCity = normalizeCity($originalCity);
            
            if ($originalCity !== $normalizedCity) {
                $updates[] = [
                    'original' => $originalCity,
                    'normalized' => $normalizedCity
                ];
            }
        }
        
        if (empty($updates)) {
            echo "✅ No city names need normalization.\n";
        } else {
            echo "🔄 Found " . count($updates) . " cities that need normalization:\n\n";
            
            foreach ($updates as $update) {
                echo "'{$update['original']}' → '{$update['normalized']}'\n";
                
                // Update the database
                $updateQuery = "UPDATE clients SET city = ? WHERE city = ?";
                $stmt = $conn->prepare($updateQuery);
                $stmt->bind_param("ss", $update['normalized'], $update['original']);
                
                if ($stmt->execute()) {
                    $affectedRows = $stmt->affected_rows;
                    echo "   ✅ Updated {$affectedRows} records\n";
                } else {
                    echo "   ❌ Failed to update: " . $stmt->error . "\n";
                }
                $stmt->close();
            }
        }
    }
    
    echo "\n🎉 City normalization completed!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

$conn->close();
?> 