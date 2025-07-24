<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$currentPage = 'career';
require_once '../includes/db_connect.php';

// Initialize variables
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : '';
$selectedJobType = isset($_GET['job_type']) ? $_GET['job_type'] : '';
$searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
$currentPage = isset($_GET['page']) ? intval($_GET['page']) : 1;
$recordsPerPage = 10;

// Function to get distinct job categories
function getJobCategories($conn) {
    $categories = [];
    $sql = "SELECT DISTINCT category FROM jobs WHERE category IS NOT NULL ORDER BY category";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row['category'];
        }
    }
    
    return $categories;
}

// Function to get job count
function getJobsCount($conn, $searchTerm = '', $category = '', $jobType = '') {
    $conditions = ["is_verified = 1"];
    $params = [];
    $types = "";
    
    if (!empty($searchTerm)) {
        $conditions[] = "(title LIKE ? OR company_name LIKE ? OR location LIKE ?)";
        $params[] = "%$searchTerm%";
        $params[] = "%$searchTerm%";
        $params[] = "%$searchTerm%";
        $types .= "sss";
    }
    
    if (!empty($category)) {
        $conditions[] = "category = ?";
        $params[] = $category;
        $types .= "s";
    }
    
    if (!empty($jobType)) {
        $conditions[] = "job_type = ?";
        $params[] = $jobType;
        $types .= "s";
    }
    
    $whereClause = implode(' AND ', $conditions);
    $sql = "SELECT COUNT(*) as count FROM jobs WHERE $whereClause";
    
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row['count'];
}

// Get all job categories
$categories = getJobCategories($conn);

// Get total job count
$totalJobs = getJobsCount($conn, $searchTerm, $selectedCategory, $selectedJobType);

// Calculate total pages
$totalPages = ceil($totalJobs / $recordsPerPage);
if ($currentPage > $totalPages && $totalPages > 0) {
    $currentPage = $totalPages;
}

// Calculate the SQL LIMIT clause
$offset = ($currentPage - 1) * $recordsPerPage;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Listings | Find Your Next Career Opportunity</title>
    <link rel="icon" type="image/png" href="/images/sortoutInnovation-icon/sortout-innovation-only-s.gif" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            background-color: #fff;
            font-family: 'Poppins', sans-serif;
        }
        
        /* Hero Section - Red and White Design */
        .hero-section {
            position: relative;
            padding: 140px 0 200px;
            overflow: hidden;
            background: linear-gradient(125deg, #8B0000 0%, #d50000 40%, #ff1744 100%);
            color: white;
            clip-path: polygon(0 0, 100% 0, 100% 92%, 0 100%);
        }
        
        .hero-title {
            font-size: 4.2rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 30px;
            background: linear-gradient(to right, #ffffff, #f5f5f5);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            display: inline-block;
            position: relative;
            z-index: 5;
        }
        
        .hero-title .accent {
            color: #fff;
            position: relative;
            display: inline-block;
            -webkit-text-fill-color: #fff;
            z-index: 5;
        }
        
        .hero-title .accent::after {
            content: '';
            position: absolute;
            bottom: 5px;
            left: 0;
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.3);
            z-index: -1;
        }
        
        .hero-lead {
            font-size: 1.3rem;
            font-weight: 400;
            line-height: 1.6;
            margin-bottom: 40px;
            max-width: 600px;
            opacity: 0.9;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 5;
        }
        
        .hero-cta {
            position: relative;
            z-index: 5;
            display: flex;
            gap: 15px;
            margin-bottom: 50px;
        }
        
        .hero-btn {
            padding: 16px 32px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .hero-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.6s ease;
        }
        
        .hero-btn:hover::before {
            left: 100%;
        }
        
        .hero-btn-primary {
            background: #fff;
            color: #d50000;
            box-shadow: 0 10px 20px rgba(213, 0, 0, 0.2);
        }
        
        .hero-btn-primary:hover {
            background: #f5f5f5;
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(213, 0, 0, 0.3);
            color: #b71c1c;
        }
        
        .hero-btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(5px);
        }
        
        .hero-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-3px);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
        }
        
        .hero-stats {
            display: flex;
            gap: 60px;
            position: relative;
            z-index: 5;
        }
        
        .hero-stat {
            text-align: center;
            position: relative;
        }
        
        .hero-stat::after {
            content: '';
            position: absolute;
            top: 10px;
            right: -30px;
            width: 1px;
            height: calc(100% - 20px);
            background: linear-gradient(to bottom, transparent, rgba(255, 255, 255, 0.3), transparent);
        }
        
        .hero-stat:last-child::after {
            display: none;
        }
        
        .hero-stat-number {
            font-size: 2.8rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 5px;
            line-height: 1;
        }
        
        .hero-stat-label {
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
        }
        
        /* Visual Elements */
        .hero-shape {
            position: absolute;
            z-index: 1;
        }
        
        .hero-shape-1 {
            top: -100px;
            right: -50px;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 38% 62% 63% 37% / 41% 44% 56% 59%;
            animation: morph 15s linear infinite alternate;
            opacity: 0.6;
        }
        
        .hero-shape-2 {
            bottom: -150px;
            left: -100px;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 53% 47% 39% 61% / 33% 47% 53% 67%;
            animation: morph 18s linear infinite alternate-reverse;
            opacity: 0.5;
        }
        
        .hero-shape-3 {
            top: 20%;
            left: 20%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;
            animation: float 8s ease-in-out infinite;
            opacity: 0.4;
        }
        
        .hero-dots {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background-image: radial-gradient(rgba(255, 255, 255, 0.15) 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.3;
            z-index: 1;
        }
        
        .hero-particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 2;
        }
        
        .particle {
            position: absolute;
            display: block;
            background-color: rgba(255, 255, 255, 0.5);
            width: 6px;
            height: 6px;
            border-radius: 50%;
            animation: rise 15s linear infinite;
            opacity: 0;
        }
        
        @keyframes rise {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-1000px) rotate(720deg);
                opacity: 0;
            }
        }
        
        @keyframes morph {
            0% {
                border-radius: 40% 60% 60% 40% / 40% 40% 60% 60%;
            }
            100% {
                border-radius: 40% 60%;
            }
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-20px);
            }
        }
        
        /* Responsive Adjustments */
        @media (max-width: 991px) {
            .hero-section {
                padding: 100px 0 160px;
            }
            
            .hero-title {
                font-size: 3.5rem;
            }
            
            .hero-stats {
                gap: 40px;
            }
        }
        
        @media (max-width: 767px) {
            .hero-section {
                padding: 80px 0 140px;
            }
            
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-lead {
                font-size: 1.1rem;
            }
            
            .hero-cta {
                flex-direction: column;
                gap: 10px;
            }
            
            .hero-stats {
                flex-wrap: wrap;
                justify-content: space-around;
                gap: 30px 10px;
            }
            
            .hero-stat {
                width: 45%;
            }
            
            .hero-stat::after {
                display: none;
            }
            
            .hero-stat-number {
                font-size: 2.2rem;
            }
        }
        
        /* Search Box - Red and White Design */
        .search-box {
            background-color: white;
            border-radius: 16px;
            padding: 40px;
            margin-top: -80px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 20;
            border: none;
            transform: translateY(0);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .search-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 8px;
            height: 60px;
            background: linear-gradient(to bottom, #b71c1c, #e53935);
            border-radius: 4px;
            margin: 40px 0 0 -4px;
        }
        
        .search-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        .search-box .form-control {
            padding: 14px 20px;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            font-size: 1rem;
            transition: all 0.3s ease;
            font-weight: 500;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
            height: auto;
        }
        
        .search-box .form-control:focus {
            border-color: #d50000;
            box-shadow: 0 2px 15px rgba(213, 0, 0, 0.15);
        }
        
        .search-box .input-group-text {
            border-radius: 12px 0 0 12px;
            background: white;
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-right: none;
            padding: 0 20px;
            color: #d50000;
        }
        
        .search-box .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }
        
        .search-box .form-select {
            padding: 14px 20px;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            font-size: 1rem;
            transition: all 0.3s ease;
            font-weight: 500;
            background-color: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.02);
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23d50000' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 16px 12px;
            appearance: none;
            height: auto;
        }
        
        .search-box .form-select:focus {
            border-color: #d50000;
            box-shadow: 0 2px 15px rgba(213, 0, 0, 0.15);
        }
        
        .search-box .btn-primary {
            background: linear-gradient(45deg, #b71c1c, #e53935);
            border: none;
            padding: 14px 22px;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 15px rgba(213, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
            height: 100%;
            color: white;
        }
        
        .search-box .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.6s ease;
        }
        
        .search-box .btn-primary:hover {
            background: linear-gradient(45deg, #9e1b1b, #d32f2f);
            transform: translateY(-2px);
            box-shadow: 0 12px 20px rgba(213, 0, 0, 0.3);
        }
        
        .search-box .btn-primary:hover::before {
            left: 100%;
        }
        
        /* Stats Container - Modern update */
        .stats-container {
            background-color: white;
            border-radius: 16px;
            padding: 24px 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.06);
            margin: 35px 0;
            position: relative;
            transition: all 0.3s ease;
            overflow: hidden;
            border-left: 5px solid #d50000;
        }
        
        .stats-container h4 {
            color: #333;
            font-weight: 700;
            font-size: 1.2rem;
            margin-bottom: 4px;
            letter-spacing: -0.3px;
        }
        
        .stats-container p {
            color: #666;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .btn-outline-secondary {
            border: 1px solid rgba(0, 0, 0, 0.1);
            color: #555;
            transition: all 0.3s ease;
            border-radius: 10px;
            font-weight: 500;
            padding: 8px 15px;
            background-color: white;
        }
        
        .btn-outline-secondary:hover, 
        .btn-outline-secondary.active {
            background-color: rgba(213, 0, 0, 0.08);
            border-color: #d50000;
            color: #d50000;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        /* Job Cards - Modern Red and White Design */
        .job-card {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            transition: all 0.4s ease;
            height: 100%;
            cursor: pointer;
            overflow: hidden;
            position: relative;
            border: 1px solid rgba(0, 0, 0, 0.03);
            padding: 28px;
        }
        
        .job-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            border-color: rgba(213, 0, 0, 0.15);
        }
        
        .job-card-high-demand {
            border-top: none;
            position: relative;
        }
        
        .job-card-high-demand::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(to right, #b71c1c, #e53935);
            border-radius: 3px 3px 0 0;
        }
        
        .high-demand-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: linear-gradient(45deg, #b71c1c, #e53935);
            color: white;
            font-size: 0.75rem;
            padding: 6px 14px;
            border-radius: 0 3px 0 12px;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(213, 0, 0, 0.25);
            display: flex;
            align-items: center;
            gap: 5px;
            letter-spacing: 0.3px;
            z-index: 5;
        }
        
        .high-demand-badge i {
            font-size: 0.7rem;
        }
        
        .company-logo {
            width: 70px;
            height: 70px;
            background-color: rgba(213, 0, 0, 0.08);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 2rem;
            font-weight: 600;
            color: #d50000;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(213, 0, 0, 0.1);
        }
        
        .company-logo::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(213, 0, 0, 0.1), rgba(213, 0, 0, 0), rgba(213, 0, 0, 0));
            top: 0;
            left: 0;
        }
        
        .job-title {
            font-weight: 700;
            font-size: 1.25rem;
            color: #333;
            margin-bottom: 5px;
            line-height: 1.3;
            transition: color 0.3s ease;
        }
        
        .job-card:hover .job-title {
            color: #d50000;
        }
        
        .company-name {
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 20px !important;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        
        .job-detail {
            font-size: 0.95rem;
            color: #666;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
        }
        
        .job-detail i {
            width: 24px;
            text-align: center;
            margin-right: 10px;
            color: #d50000 !important;
            font-size: 0.95rem;
            opacity: 0.85;
        }
        
        .job-salary {
            font-weight: 700;
            color: #d50000 !important;
        }
        
        .job-type {
            display: inline-block;
            background-color: rgba(213, 0, 0, 0.08);
            color: #d50000;
            font-size: 0.8rem;
            padding: 7px 16px;
            border-radius: 30px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .job-card:hover .job-type {
            background-color: #d50000;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(213, 0, 0, 0.2);
        }
        
        .job-type.contract {
            background-color: rgba(213, 0, 0, 0.05);
            color: #d50000;
        }
        
        .job-card:hover .job-type.contract {
            background-color: #d50000;
            color: white;
        }
        
        .job-type.part-time {
            background-color: rgba(213, 0, 0, 0.05);
            color: #d50000;
        }
        
        .job-card:hover .job-type.part-time {
            background-color: #d50000;
            color: white;
        }
        
        small.text-muted {
            background-color: #f8f8f8;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            color: #777 !important;
            transition: all 0.3s ease;
        }
        
        .job-card:hover small.text-muted {
            background-color: #f0f0f0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        /* No jobs found styling */
        .col-12.text-center.py-5 {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 60px 0 !important;
            margin: 30px 0;
        }
        
        .col-12.text-center.py-5 i {
            color: #d50000;
            opacity: 0.2;
            margin-bottom: 20px;
        }
        
        .col-12.text-center.py-5 h4 {
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            font-size: 1.8rem;
        }
        
        .col-12.text-center.py-5 p {
            color: #666;
            max-width: 450px;
            margin: 0 auto 25px;
            font-size: 1.05rem;
        }
        
        .btn-outline-primary {
            border: 2px solid #d50000;
            color: #d50000;
            background: transparent;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary:hover {
            background: #d50000;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(213, 0, 0, 0.2);
            border-color: #d50000;
        }
        
        /* Pagination - Modern styled with red */
        .pagination {
            margin-top: 50px;
            margin-bottom: 50px;
        }
        
        .pagination .page-link {
            color: #555;
            border: none;
            font-weight: 600;
            padding: 12px 18px;
            margin: 0 5px;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }
        
        .pagination .page-link:hover {
            background-color: rgba(213, 0, 0, 0.08);
            color: #d50000;
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }
        
        .pagination .page-item.active .page-link {
            background: linear-gradient(45deg, #b71c1c, #e53935);
            color: white;
            box-shadow: 0 8px 15px rgba(213, 0, 0, 0.3);
        }
        
        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            padding: 12px 15px;
        }
        
        /* Animation for job cards */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .job-card {
            animation: fadeInUp 0.6s ease forwards;
            opacity: 0;
            animation-delay: calc(var(--index, 0) * 0.1s);
        }
        
        /* Responsive adjustments */
        @media (max-width: 991px) {
            .search-box {
                padding: 30px;
                margin-top: -60px;
            }
            
            .search-box .form-control,
            .search-box .form-select,
            .search-box .btn-primary {
                padding: 12px 16px;
            }
            
            .job-card {
                padding: 22px;
            }
            
            .company-logo {
                width: 60px;
                height: 60px;
                font-size: 1.6rem;
            }
            
            .job-title {
                font-size: 1.1rem;
            }
        }
        
        @media (max-width: 767px) {
            .search-box {
                padding: 25px 20px;
                margin-top: -50px;
                border-radius: 16px;
            }
            
            .stats-container {
                padding: 20px;
            }
            
            .job-card {
                padding: 20px;
                margin-bottom: 15px;
            }
            
            .high-demand-badge {
                font-size: 0.65rem;
                padding: 4px 10px;
            }
            
            .company-logo {
                width: 50px;
                height: 50px;
                font-size: 1.4rem;
                margin-right: 15px;
            }
            
            .pagination .page-link {
                padding: 10px 15px;
                margin: 0 3px;
            }
            
            .job-title {
                font-size: 1rem;
                padding-right: 65px; /* Add space for the badge */
                word-wrap: break-word;
            }
        }
        
        .search-box-sticky {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(0) !important;
            width: 94%;
            max-width: 1140px;
            z-index: 1000;
            padding: 20px !important;
            transition: all 0.3s ease;
            border-radius: 15px !important;
            animation: slideDown 0.3s forwards;
        }
        
        @keyframes slideDown {
            from {
                transform: translateX(-50%) translateY(-100px);
                opacity: 0;
            }
            to {
                transform: translateX(-50%) translateY(0);
                opacity: 1;
            }
        }
        
        @media (max-width: 991px) {
            .search-box-sticky {
                width: 90%;
            }
        }
        
        @media (max-width: 767px) {
            .search-box-sticky {
                padding: 15px !important;
                top: 10px;
            }
        }
        
        /* Enhance job card layout */
        .job-card {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .job-content {
            flex-grow: 1;
        }
        
        .job-header {
            margin-bottom: 20px;
        }
        
        .job-details {
            margin-bottom: 20px;
        }
        
        .job-footer {
            margin-top: auto;
        }

        /* Footer Section - Not modifying as requested */
    </style>
</head>
<body>

<?php include '../components/navbar/navbar.php'; ?>

<!-- Modern Hero Section -->
<div class="modern-hero">
    <div class="container position-relative">
        <div class="row hero-row">
            <div class="col-lg-7 hero-content-col">
                <div class="hero-content">
                    <div class="hero-badge">
                        <span><i class="fas fa-bolt"></i> Career Portal</span>
                    </div>
                    <h1 class="hero-heading">
                        Discover Your <br>
                        <span class="text-gradient">Next Opportunity</span>
                    </h1>
                    <p class="hero-subheading">
                        Find the perfect job that matches your skills and career goals from thousands of verified opportunities.
                    </p>
                    
                    <div class="hero-stats-container">
                        <div class="hero-stat">
                            <div class="stat-value">5,200+</div>
                            <div class="stat-label">Active Jobs</div>
                        </div>
                        <div class="hero-stat">
                            <div class="stat-value">3,400+</div>
                            <div class="stat-label">Companies</div>
                        </div>
                        <div class="hero-stat">
                            <div class="stat-value">8M+</div>
                            <div class="stat-label">Candidates</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-5 d-none d-lg-flex hero-image-col">
                <div class="hero-image">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=776&q=80" alt="Career professionals" class="hero-img">
                    <div class="image-shape"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Hero Styles */
.modern-hero {
    position: relative;
    padding: 140px 0 180px;
    background-color: #fff;
    overflow: hidden;
}

.hero-bg-elements {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 1;
}

.bg-circle {
    position: absolute;
    border-radius: 50%;
    z-index: -1;
}

.circle-1 {
    width: 500px;
    height: 500px;
    background: radial-gradient(circle, rgba(213, 0, 0, 0.03) 0%, rgba(213, 0, 0, 0) 70%);
    top: -200px;
    right: -100px;
}

.circle-2 {
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(213, 0, 0, 0.04) 0%, rgba(213, 0, 0, 0) 70%);
    bottom: -300px;
    left: -200px;
}

.circle-3 {
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(213, 0, 0, 0.05) 0%, rgba(213, 0, 0, 0) 70%);
    top: 30%;
    left: 15%;
}

.floating-icon {
    position: absolute;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #d50000;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
    animation: float 6s infinite ease-in-out;
}

.icon-1 {
    top: 20%;
    right: 10%;
    animation-delay: 0s;
}

.icon-2 {
    top: 60%;
    right: 20%;
    animation-delay: 1.5s;
}

.icon-3 {
    top: 30%;
    left: 10%;
    animation-delay: 1s;
}

.icon-4 {
    top: 70%;
    left: 20%;
    animation-delay: 2s;
}

@keyframes float {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-20px);
    }
}

.hero-row {
    min-height: 500px;
    position: relative;
    z-index: 2;
}

.hero-content-col {
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.hero-content {
    max-width: 580px;
}

.hero-badge {
    display: inline-block;
    margin-bottom: 24px;
}

.hero-badge span {
    background-color: rgba(213, 0, 0, 0.07);
    color: #d50000;
    padding: 8px 16px;
    border-radius: 30px;
    font-weight: 600;
    font-size: 14px;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
}

.hero-badge i {
    margin-right: 8px;
}

.hero-heading {
    font-size: 3.8rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 24px;
    color: #333;
}

.text-gradient {
    background: linear-gradient(90deg, #d50000, #ff1744);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    color: transparent;
    display: inline-block;
    position: relative;
}

.text-gradient::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 8px;
    background-color: rgba(213, 0, 0, 0.08);
    bottom: 5px;
    left: 0;
    z-index: -1;
    border-radius: 4px;
}

.hero-subheading {
    font-size: 1.2rem;
    color: #666;
    margin-bottom: 40px;
    line-height: 1.7;
    max-width: 520px;
}

.hero-search-container {
    margin-bottom: 50px;
}

.hero-search-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    background-color: white;
    border-radius: 100px;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    padding: 8px 8px 8px 24px;
    transition: all 0.3s ease;
}

.hero-search-wrapper:hover {
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.search-icon {
    color: #d50000;
    margin-right: 16px;
}

.hero-search-input {
    flex: 1;
    border: none;
    background: transparent;
    padding: 12px 0;
    font-size: 16px;
    font-weight: 500;
    color: #333;
}

.hero-search-input:focus {
    outline: none;
}

.hero-search-input::placeholder {
    color: #999;
}

.hero-search-btn {
    background: linear-gradient(90deg, #d50000, #ff1744);
    color: white;
    border: none;
    border-radius: 100px;
    padding: 14px 30px;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s ease;
}

.hero-search-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(213, 0, 0, 0.2);
}

.hero-stats-container {
    display: flex;
    gap: 50px;
}

.hero-stat {
    position: relative;
}

.hero-stat:not(:last-child)::after {
    content: '';
    position: absolute;
    right: -25px;
    top: 50%;
    transform: translateY(-50%);
    width: 1px;
    height: 40px;
    background: linear-gradient(to bottom, transparent, rgba(0, 0, 0, 0.1), transparent);
}

.stat-value {
    font-size: 2.2rem;
    font-weight: 700;
    color: #d50000;
    margin-bottom: 8px;
    line-height: 1;
}

.stat-label {
    font-size: 14px;
    color: #777;
    font-weight: 500;
}

.hero-image-col {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: flex-end;
}

.hero-image {
    position: relative;
    width: 100%;
    max-width: 450px;
    z-index: 3;
}

.hero-img {
    width: 100%;
    height: auto;
    border-radius: 16px;
    box-shadow: 0 30px 60px rgba(0, 0, 0, 0.2);
    position: relative;
    z-index: 2;
}

.image-shape {
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 16px;
    background: linear-gradient(45deg, rgba(213, 0, 0, 0.7), rgba(255, 23, 68, 0.7));
    top: 20px;
    left: 20px;
    z-index: 1;
}

/* Responsive Styles */
@media (max-width: 1199px) {
    .hero-heading {
        font-size: 3.2rem;
    }
    
    .hero-image {
        max-width: 400px;
    }
}

@media (max-width: 991px) {
    .modern-hero {
        padding: 100px 0 160px;
    }
    
    .hero-heading {
        font-size: 2.8rem;
    }
    
    .hero-subheading {
        font-size: 1.1rem;
    }
    
    .hero-content {
        max-width: 100%;
        margin: 0 auto;
        text-align: center;
    }
    
    .hero-stats-container {
        justify-content: center;
    }
    
    .floating-icon {
        display: none;
    }
}

@media (max-width: 767px) {
    .modern-hero {
        padding: 80px 0 140px;
    }
    
    .hero-heading {
        font-size: 2.4rem;
    }
    
    .hero-subheading {
        font-size: 1rem;
    }
    
    .hero-stats-container {
        flex-wrap: wrap;
        gap: 30px;
    }
    
    .hero-stat:not(:last-child)::after {
        display: none;
    }
}

@media (max-width: 575px) {
    .hero-heading {
        font-size: 2rem;
    }
    
    .hero-search-wrapper {
        flex-wrap: wrap;
        padding: 15px;
    }
    
    .search-icon {
        margin-right: 10px;
    }
    
    .hero-search-input {
        width: calc(100% - 30px);
        margin-bottom: 10px;
    }
    
    .hero-search-btn {
        width: 100%;
        margin-top: 5px;
    }
}
</style>

<!-- Search and Filter Section -->
<div class="container">
    <div class="search-box mb-5">
        <form action="" method="GET" id="searchForm">
            <div class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Search jobs, companies, or locations" name="search" value="<?= htmlspecialchars($searchTerm) ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select filter-dropdown" name="category" id="categoryFilter">
                        <option value="">All Categories</option>
                        <option value="IT & Software Development" <?= ($selectedCategory == 'IT & Software Development') ? 'selected' : '' ?>>IT & Software Development</option>
                        <option value="Marketing & Advertising" <?= ($selectedCategory == 'Marketing & Advertising') ? 'selected' : '' ?>>Marketing & Advertising</option>
                        <option value="Sales & Business Development" <?= ($selectedCategory == 'Sales & Business Development') ? 'selected' : '' ?>>Sales & Business Development</option>
                        <option value="Human Resources & Recruitment" <?= ($selectedCategory == 'Human Resources & Recruitment') ? 'selected' : '' ?>>Human Resources & Recruitment</option>
                        <option value="Finance & Accounting" <?= ($selectedCategory == 'Finance & Accounting') ? 'selected' : '' ?>>Finance & Accounting</option>
                        <option value="Operations & Supply Chain Management" <?= ($selectedCategory == 'Operations & Supply Chain Management') ? 'selected' : '' ?>>Operations & Supply Chain Management</option>
                        <option value="Education & Training" <?= ($selectedCategory == 'Education & Training') ? 'selected' : '' ?>>Education & Training</option>
                        <option value="Healthcare & Medical" <?= ($selectedCategory == 'Healthcare & Medical') ? 'selected' : '' ?>>Healthcare & Medical</option>
                        <option value="Banking & Insurance" <?= ($selectedCategory == 'Banking & Insurance') ? 'selected' : '' ?>>Banking & Insurance</option>
                        <option value="Media, Arts & Entertainment" <?= ($selectedCategory == 'Media, Arts & Entertainment') ? 'selected' : '' ?>>Media, Arts & Entertainment</option>
                        <option value="Customer Service & Support" <?= ($selectedCategory == 'Customer Service & Support') ? 'selected' : '' ?>>Customer Service & Support</option>
                        <option value="Manufacturing & Engineering" <?= ($selectedCategory == 'Manufacturing & Engineering') ? 'selected' : '' ?>>Manufacturing & Engineering</option>
                        <option value="Hospitality & Travel" <?= ($selectedCategory == 'Hospitality & Travel') ? 'selected' : '' ?>>Hospitality & Travel</option>
                        <option value="Legal & Compliance" <?= ($selectedCategory == 'Legal & Compliance') ? 'selected' : '' ?>>Legal & Compliance</option>
                        <option value="Other" <?= ($selectedCategory == 'Other') ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select filter-dropdown" name="job_type" id="jobTypeFilter">
                        <option value="">All Types</option>
                        <option value="Full Time" <?= ($selectedJobType == 'Full Time') ? 'selected' : '' ?>>Full Time</option>
                        <option value="Part Time" <?= ($selectedJobType == 'Part Time') ? 'selected' : '' ?>>Part Time</option>
                        <option value="Contract" <?= ($selectedJobType == 'Contract') ? 'selected' : '' ?>>Contract</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Find Jobs</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Stats Section -->
    <div class="stats-container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h4 class="mb-0 fw-semibold">Job Listings</h4>
                <p class="text-muted mb-0 small">Showing <?= min($totalJobs, $offset + 1) ?>-<?= min($totalJobs, $offset + $recordsPerPage) ?> of <?= $totalJobs ?> jobs</p>
            </div>
            
            <div class="col-md-6 text-md-end">
                <button class="btn btn-outline-secondary btn-sm me-2" id="resetFilters">
                    <i class="fas fa-redo-alt me-1"></i> Reset Filters
                </button>
                <div class="btn-group" role="group">
                    <a href="#" class="btn btn-outline-secondary btn-sm active">
                        <i class="fas fa-th-large"></i>
                    </a>
                    <a href="#" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-list"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Job Listings -->
    <div class="row g-4 mb-5" id="jobListings">
        <?php
        // Build query to fetch jobs with filtering
        $conditions = ["is_verified = 1"];
        $params = [];
        $types = "";
        
        if (!empty($searchTerm)) {
            $conditions[] = "(title LIKE ? OR company_name LIKE ? OR location LIKE ?)";
            $params[] = "%$searchTerm%";
            $params[] = "%$searchTerm%";
            $params[] = "%$searchTerm%";
            $types .= "sss";
        }
        
        if (!empty($selectedCategory)) {
            $conditions[] = "category = ?";
            $params[] = $selectedCategory;
            $types .= "s";
        }
        
        if (!empty($selectedJobType)) {
            $conditions[] = "job_type = ?";
            $params[] = $selectedJobType;
            $types .= "s";
        }
        
        $whereClause = implode(' AND ', $conditions);
        $sql = "SELECT * FROM jobs WHERE $whereClause ORDER BY high_demand DESC, created_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $conn->prepare($sql);
        
        // Add limit and offset to parameters
        $params[] = $recordsPerPage;
        $params[] = $offset;
        $types .= "ii";
        
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            while($job = $result->fetch_assoc()) {
                $jobTypeClass = strtolower(str_replace(' ', '-', $job['job_type']));
                $formattedMinSalary = number_format($job['min_salary']);
                $formattedMaxSalary = number_format($job['max_salary']);
                
                // Get first letter of company name for logo placeholder
                $companyInitial = strtoupper(substr($job['company_name'], 0, 1));
                
                // Calculate days ago
                $createdDate = new DateTime($job['created_at']);
                $now = new DateTime();
                $interval = $now->diff($createdDate);
                $daysAgo = $interval->days;
                $timeAgo = $daysAgo > 0 ? $daysAgo . " day" . ($daysAgo > 1 ? "s" : "") . " ago" : "Today";
        ?>
        
        <div class="col-md-6 col-lg-4">
            <a href="job-detail.php?id=<?= $job['id'] ?>" class="text-decoration-none">
                <div class="job-card <?= $job['high_demand'] ? 'job-card-high-demand' : '' ?>">
                    <?php if($job['high_demand']): ?>
                    <span class="high-demand-badge"><i class="fas fa-fire me-1"></i> High Demand</span>
                    <?php endif; ?>
                    
                    <div class="job-content">
                        <div class="job-header d-flex align-items-start">
                            <div class="company-logo">
                                <?= $companyInitial ?>
                            </div>
                            <div>
                                <h5 class="job-title"><?= htmlspecialchars($job['title']) ?></h5>
                                <p class="company-name mb-0">
                                    <?= htmlspecialchars($job['company_name']) ?>
                                    <i class="fas fa-check-circle ms-2" style="color: #4CAF50; font-size: 0.8rem;"></i>
                                </p>
                            </div>
                        </div>
                        
                        <div class="job-details">
                            <p class="job-detail">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($job['location']) ?>
                            </p>
                            <p class="job-detail">
                                <i class="fas fa-briefcase"></i>
                                <?= htmlspecialchars($job['experience']) ?>
                            </p>
                            <p class="job-detail job-salary">
                                <i class="fas fa-rupee-sign"></i>
                                <?= $formattedMinSalary ?> - <?= $formattedMaxSalary ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="job-footer d-flex justify-content-between align-items-center">
                        <span class="job-type <?= $jobTypeClass ?>"><?= htmlspecialchars($job['job_type']) ?></span>
                        <small class="text-muted"><i class="far fa-clock me-1"></i> <?= $timeAgo ?></small>
                    </div>
                </div>
            </a>
        </div>
        
        <?php
            }
        } else {
        ?>
        <div class="col-12 text-center py-5">
            <i class="fas fa-search fa-3x mb-3 text-muted"></i>
            <h4>No jobs found</h4>
            <p class="text-muted">Try adjusting your search or filter criteria</p>
            <button class="btn btn-outline-primary" id="clearFilters">Clear all filters</button>
        </div>
        <?php
        }
        $stmt->close();
        ?>
    </div>

    <!-- Pagination -->
    <?php if($totalPages > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if($currentPage > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($searchTerm) ?>&category=<?= urlencode($selectedCategory) ?>&job_type=<?= urlencode($selectedJobType) ?>">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
            <?php endif; ?>
            
            <?php
            $startPage = max(1, $currentPage - 2);
            $endPage = min($totalPages, $startPage + 4);
            
            if ($endPage - $startPage < 4) {
                $startPage = max(1, $endPage - 4);
            }
            
            for ($i = $startPage; $i <= $endPage; $i++):
            ?>
            <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($searchTerm) ?>&category=<?= urlencode($selectedCategory) ?>&job_type=<?= urlencode($selectedJobType) ?>">
                    <?= $i ?>
                </a>
            </li>
            <?php endfor; ?>
            
            <?php if($currentPage < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($searchTerm) ?>&category=<?= urlencode($selectedCategory) ?>&job_type=<?= urlencode($selectedJobType) ?>">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<!-- Include Footer -->
<?php 
    // Go up one directory to reach the root, then into components
    require_once __DIR__ . '/../components/footer/footer.php';
?>

<!-- Bootstrap JS and other scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Reset filters button
        document.getElementById('resetFilters').addEventListener('click', function(e) {
            e.preventDefault();
            
            // Clear all input fields
            searchInput.value = '';
            categoryFilter.value = '';
            jobTypeFilter.value = '';
            
            // Use AJAX to fetch all jobs without filters
            fetchJobs({
                'search': '',
                'category': '',
                'job_type': '',
                'page': '1'
            });
        });
        
        // Clear filters button (on no results page)
        const clearFiltersBtn = document.getElementById('clearFilters');
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Clear all input fields
                searchInput.value = '';
                categoryFilter.value = '';
                jobTypeFilter.value = '';
                
                // Use AJAX to fetch all jobs without filters
                fetchJobs({
                    'search': '',
                    'category': '',
                    'job_type': '',
                    'page': '1'
                });
            });
        }
        
        // AJAX filtering for search form
        const searchForm = document.getElementById('searchForm');
        const jobListings = document.getElementById('jobListings');
        const categoryFilter = document.getElementById('categoryFilter');
        const jobTypeFilter = document.getElementById('jobTypeFilter');
        const searchInput = document.querySelector('input[name="search"]');
        
        // Function to update URL parameters without page reload
        function updateUrlParams(params) {
            const url = new URL(window.location);
            
            // Update or add each parameter
            for (const [key, value] of Object.entries(params)) {
                if (value) {
                    url.searchParams.set(key, value);
                } else {
                    url.searchParams.delete(key);
                }
            }
            
            // Update browser history without reload
            window.history.pushState({}, '', url);
        }
        
        // Function to fetch and update job listings
        function fetchJobs(params = {}) {
            // Show loading state
            jobListings.innerHTML = '<div class="col-12 text-center py-5"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="mt-3">Loading jobs...</p></div>';
            
            // Create the URL with parameters
            const url = new URL(window.location.pathname, window.location.origin);
            
            // Add current search parameters
            const currentParams = new URLSearchParams(window.location.search);
            for (const [key, value] of currentParams.entries()) {
                if (!params.hasOwnProperty(key)) {
                    url.searchParams.append(key, value);
                }
            }
            
            // Add or update with new parameters
            for (const [key, value] of Object.entries(params)) {
                if (value) {
                    url.searchParams.set(key, value);
                } else {
                    url.searchParams.delete(key);
                }
            }
            
            // Set page to 1 when filters change
            if (Object.keys(params).length > 0 && !params.hasOwnProperty('page')) {
                url.searchParams.set('page', '1');
            }
            
            // Make the AJAX request
            fetch(url.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                // Create a temporary element to parse the HTML
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Extract job listings
                const newJobListings = doc.getElementById('jobListings');
                if (newJobListings) {
                    jobListings.innerHTML = newJobListings.innerHTML;
                    
                    // Reinitialize animations and card effects
                    const jobCards = document.querySelectorAll('.job-card');
                    jobCards.forEach((card, index) => {
                        card.style.setProperty('--index', index);
                    });
                    
                    addCardHoverEffects();
                }
                
                // Update pagination
                const pagination = document.querySelector('nav[aria-label="Page navigation"]');
                const newPagination = doc.querySelector('nav[aria-label="Page navigation"]');
                if (pagination && newPagination) {
                    pagination.innerHTML = newPagination.innerHTML;
                    // Re-attach pagination click handlers
                    attachPaginationHandlers();
                } else if (!newPagination && pagination) {
                    pagination.style.display = 'none';
                } else if (newPagination && !pagination) {
                    const paginationContainer = document.createElement('nav');
                    paginationContainer.setAttribute('aria-label', 'Page navigation');
                    paginationContainer.innerHTML = newPagination.innerHTML;
                    jobListings.after(paginationContainer);
                    attachPaginationHandlers();
                }
                
                // Update stats count
                const statsText = doc.querySelector('.stats-container p.text-muted');
                if (statsText) {
                    document.querySelector('.stats-container p.text-muted').innerHTML = statsText.innerHTML;
                }
                
                // Update URL parameters
                updateUrlParams(params);
            })
            .catch(error => {
                console.error('Error fetching jobs:', error);
                jobListings.innerHTML = '<div class="col-12 text-center py-5"><i class="fas fa-exclamation-circle fa-3x text-danger"></i><p class="mt-3">Error loading jobs. Please try again.</p></div>';
            });
        }
        
        // Filter change event handlers
        categoryFilter.addEventListener('change', function() {
            fetchJobs({ 'category': this.value });
        });
        
        jobTypeFilter.addEventListener('change', function() {
            fetchJobs({ 'job_type': this.value });
        });
        
        // Handle form submission
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            fetchJobs({ 
                'search': searchInput.value,
                'category': categoryFilter.value,
                'job_type': jobTypeFilter.value
            });
        });
        
        // Function to add hover effects to job cards
        function addCardHoverEffects() {
            const jobCards = document.querySelectorAll('.job-card');
            jobCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    const otherCards = document.querySelectorAll('.job-card:not(:hover)');
                    otherCards.forEach(otherCard => {
                        otherCard.style.opacity = '0.7';
                        otherCard.style.transform = 'scale(0.98)';
                    });
                });
                
                card.addEventListener('mouseleave', function() {
                    const allCards = document.querySelectorAll('.job-card');
                    allCards.forEach(c => {
                        c.style.opacity = '1';
                        c.style.transform = '';
                    });
                });
            });
        }
        
        // Function to attach pagination click handlers
        function attachPaginationHandlers() {
            const paginationLinks = document.querySelectorAll('.pagination .page-link');
            paginationLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = new URL(this.href);
                    const page = url.searchParams.get('page');
                    fetchJobs({ 'page': page });
                    
                    // Scroll to top of job listings
                    window.scrollTo({
                        top: document.querySelector('.stats-container').offsetTop - 120,
                        behavior: 'smooth'
                    });
                });
            });
        }   
        
        // Initialize pagination handlers
        attachPaginationHandlers();
        
        // Add animation to job cards with staggered delay
        const jobCards = document.querySelectorAll('.job-card');
        jobCards.forEach((card, index) => {
            card.style.setProperty('--index', index);
        });
        
        // Initialize hover effects
        addCardHoverEffects();
        
        // Grid/List view toggle
        const gridViewBtn = document.querySelector('.btn-group .btn-outline-secondary:first-child');
        const listViewBtn = document.querySelector('.btn-group .btn-outline-secondary:last-child');
        
        gridViewBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Update active state
            gridViewBtn.classList.add('active');
            listViewBtn.classList.remove('active');
            
            // Change layout to grid
            const jobColumns = document.querySelectorAll('#jobListings > div');
            jobColumns.forEach(col => {
                col.className = 'col-md-6 col-lg-4';
            });
            
            // Store preference
            localStorage.setItem('jobViewPreference', 'grid');
        });
        
        listViewBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Update active state
            listViewBtn.classList.add('active');
            gridViewBtn.classList.remove('active');
            
            // Change layout to list
            const jobColumns = document.querySelectorAll('#jobListings > div');
            jobColumns.forEach(col => {
                col.className = 'col-12';
                
                // Adjust job card layout for list view
                const card = col.querySelector('.job-card');
                if (card) {
                    card.style.display = 'flex';
                    card.style.flexDirection = 'row';
                    card.style.alignItems = 'center';
                    
                    const header = card.querySelector('.job-header');
                    if (header) {
                        header.style.marginBottom = '0';
                        header.style.width = '30%';
                    }
                    
                    const details = card.querySelector('.job-details');
                    if (details) {
                        details.style.display = 'flex';
                        details.style.alignItems = 'center';
                        details.style.width = '40%';
                        details.style.margin = '0 20px';
                        
                        const jobDetails = details.querySelectorAll('.job-detail');
                        jobDetails.forEach(detail => {
                            detail.style.marginBottom = '0';
                            detail.style.marginRight = '15px';
                        });
                    }
                    
                    const footer = card.querySelector('.job-footer');
                    if (footer) {
                        footer.style.width = '30%';
                        footer.style.margin = '0';
                    }
                }
            });
            
            // Store preference
            localStorage.setItem('jobViewPreference', 'list');
        });
        
        // Load saved view preference
        const savedViewPreference = localStorage.getItem('jobViewPreference');
        if (savedViewPreference === 'list') {
            listViewBtn.click();
        }

        // Hero search form handling
        const heroSearchForm = document.getElementById('heroSearchForm');
        const mainSearchForm = document.getElementById('searchForm');
        const mainSearchInput = document.querySelector('#searchForm input[name="search"]');
        
        if (heroSearchForm) {
            heroSearchForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Get the search value from hero form
                const heroSearchValue = this.querySelector('input[name="search"]').value;
                
                // Update the main search form input
                mainSearchInput.value = heroSearchValue;
                
                // Scroll to the main search form
                document.querySelector('#searchForm').scrollIntoView({
                    behavior: 'smooth'
                });
                
                // Submit the main form after a short delay (to allow scrolling)
                setTimeout(() => {
                    mainSearchForm.dispatchEvent(new Event('submit'));
                }, 800);
            });
        }
    });
</script>

</body>
</html> 