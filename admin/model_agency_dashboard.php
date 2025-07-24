<?php
// ✅ Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Ensure Correct Path for Database Connection
require_once __DIR__ . '/../includes/db_connect.php';

// ✅ Ensure Only Admins & Super Admins Can Access
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'super_admin')) {
    header("Location: ../auth/login.php");
    exit();
}

// ✅ Debugging: Check if session variables are set properly
// Uncomment this line if you need to debug session values
// echo "<pre>"; print_r($_SESSION); echo "</pre>"; exit();

// ✅ Fetch pending clients
$pendingQuery = "SELECT * FROM clients WHERE approval_status = 'pending'";
$pendingResult = mysqli_query($conn, $pendingQuery);

// ✅ Fetch approved clients
$approvedQuery = "SELECT * FROM clients WHERE approval_status = 'approved'";
$approvedResult = mysqli_query($conn, $approvedQuery);

// Get total counts
$pendingCount = $conn->query("SELECT COUNT(*) as count FROM clients WHERE approval_status = 'pending'")->fetch_assoc()['count'];
$approvedCount = $conn->query("SELECT COUNT(*) as count FROM clients WHERE approval_status = 'approved'")->fetch_assoc()['count'];
$rejectedCount = $conn->query("SELECT COUNT(*) as count FROM clients WHERE approval_status = 'rejected'")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" href="/images/sortoutInnovation-icon/sortout-innovation-only-s.gif" />

    <title>Agency Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .dashboard-card {
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            transition: transform 0.3s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            font-weight: 600;
            color: white;
            transition: background-color 0.3s;
        }
        .btn-approve { background-color: #10b981; }
        .btn-approve:hover { background-color: #059669; }
        .btn-reject { background-color: #ef4444; }
        .btn-reject:hover { background-color: #dc2626; }
        .btn-edit { background-color: #3b82f6; }
        .btn-edit:hover { background-color: #2563eb; }
        .btn-delete { background-color: #ef4444; }
        .btn-delete:hover { background-color: #dc2626; }
        .table-container {
            overflow-x: auto;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin: 1.5rem 0;
        }
        .table-header {
            background-color: #f3f4f6;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: #4b5563;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .table-cell {
            padding: 1rem 1.5rem;
            white-space: nowrap;
            font-size: 0.875rem;
            color: #1f2937;
        }
        .pagination-btn {
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #4b5563;
            background-color: white;
            border: 1px solid #d1d5db;
        }
        .pagination-btn:hover {
            background-color: #f3f4f6;
        }
        .pagination-btn.active {
            background-color: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
        .alert {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        .alert-error {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        /* Image lightbox styles */
        .client-image {
            cursor: pointer;
            transition: transform 0.2s;
        }
        .client-image:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .image-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
            justify-content: center;
            align-items: center;
        }
        .image-modal.active {
            display: flex;
            opacity: 1;
        }
        .modal-content {
            max-width: 90%;
            max-height: 90%;
            background: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
            position: relative;
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }
        .image-modal.active .modal-content {
            transform: scale(1);
        }
        .modal-image {
            max-width: 100%;
            max-height: 80vh;
            display: block;
            border-radius: 4px;
        }
        .modal-close {
            position: absolute;
            top: 10px;
            right: 15px;
            color: #333;
            font-size: 24px;
            cursor: pointer;
            z-index: 1001;
            background: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .modal-close:hover {
            background-color: #f3f4f6;
        }
        .modal-caption {
            margin-top: 10px;
            color: #333;
            text-align: center;
            padding: 5px;
            font-weight: 500;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Image Modal -->
    <div id="imageModal" class="image-modal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeImageModal()">&times;</span>
            <img id="modalImage" class="modal-image" src="" alt="Client Image">
            <div id="modalCaption" class="modal-caption"></div>
        </div>
    </div>
    
    <!-- Top Navigation -->
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold text-gray-900">Agency Dashboard</h1>
                </div>
                <div class="flex items-center">
                    <span class="text-gray-700 mr-4">Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></span>
                    <a href="../auth/logout.php" class="btn bg-red-500 hover:bg-red-600">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Alerts -->
        <div id="alertContainer" class="hidden"></div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="dashboard-card">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-500">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Pending Approvals</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= $pendingCount ?></p>
                    </div>
                </div>
            </div>
            <div class="dashboard-card">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-500">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Approved Clients</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= $approvedCount ?></p>
                    </div>
                </div>
            </div>
            <div class="dashboard-card">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-500">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Rejected Clients</p>
                        <p class="text-2xl font-semibold text-gray-900"><?= $rejectedCount ?></p>
                    </div>
                </div>
        </div>
</div>

        <!-- Pending Approvals Section -->
    <div class="table-container">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900">Pending Approvals</h2>
                <div class="flex gap-4">
                    <input type="text" id="pendingSearch" placeholder="Search..." class="px-4 py-2 border rounded-lg">
                    <select id="pendingFilter" class="px-4 py-2 border rounded-lg">
                        <option value="">All Types</option>
                        <option value="Artist">Artist</option>
                        <option value="Employee">Employee</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="table-header px-6 py-3">Name</th>
                            <th class="table-header px-6 py-3">Age</th>
                            <th class="table-header px-6 py-3">Gender</th>
                            <th class="table-header px-6 py-3">Professional</th>
                            <th class="table-header px-6 py-3">Category/Role</th>
                            <th class="table-header px-6 py-3">City</th>
                            <th class="table-header px-6 py-3">Phone</th>
                            <th class="table-header px-6 py-3">Email</th>
                            <th class="table-header px-6 py-3">Influencer Category</th>
                            <th class="table-header px-6 py-3">Influencer Type</th>
                            <th class="table-header px-6 py-3">Instagram</th>
                            <th class="table-header px-6 py-3">Expected Payment</th>
                            <th class="table-header px-6 py-3">Work Type</th>
                            <th class="table-header px-6 py-3">Followers</th>
                            <th class="table-header px-6 py-3">Experience</th>
                            <th class="table-header px-6 py-3">Languages</th>
                            <th class="table-header px-6 py-3">Current Salary</th>
                            <th class="table-header px-6 py-3">Image</th>
                            <th class="table-header px-6 py-3">Resume</th>
                            <th class="table-header px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="pendingTableBody">
                        <?php if ($pendingResult && mysqli_num_rows($pendingResult) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($pendingResult)): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="table-cell"><?= htmlspecialchars($row['name']) ?></td>
                                    <td class="table-cell"><?= $row['age'] ?></td>
                                    <td class="table-cell"><?= htmlspecialchars($row['gender']) ?></td>
                                    <td class="table-cell"><?= htmlspecialchars($row['professional']) ?></td>
                                    <td class="table-cell">
                                        <?= $row['professional'] === 'Artist' ? 
                                            htmlspecialchars($row['category']) : 
                                            htmlspecialchars($row['role']) ?>
                                    </td>
                                    <td class="table-cell"><?= htmlspecialchars($row['city']) ?></td>
                                    <td class="table-cell">
                                        <?php if (!empty($row['phone'])): ?>
                                            <a href="https://wa.me/91<?= htmlspecialchars($row['phone']) ?>" 
                                               target="_blank" 
                                               class="text-green-600 hover:text-green-800 font-medium flex items-center">
                                                <i class="fab fa-whatsapp mr-1"></i>
                                                <?= htmlspecialchars($row['phone']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-gray-400">No Phone</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="table-cell"><?= $row['professional'] === 'Artist' ? htmlspecialchars($row['email'] ?? 'N/A') : 'N/A' ?></td>
                                    <td class="table-cell"><?= $row['professional'] === 'Artist' ? htmlspecialchars($row['influencer_category'] ?? 'N/A') : 'N/A' ?></td>
                                    <td class="table-cell"><?= $row['professional'] === 'Artist' ? htmlspecialchars($row['influencer_type'] ?? 'N/A') : 'N/A' ?></td>
                                    <td class="table-cell">
                                        <?php if ($row['professional'] === 'Artist' && !empty($row['instagram_profile'])): ?>
                                            <a href="<?= htmlspecialchars($row['instagram_profile']) ?>" target="_blank" class="text-blue-500 hover:text-blue-700">
                                                <i class="fab fa-instagram mr-1"></i>View
                                            </a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td class="table-cell"><?= $row['professional'] === 'Artist' ? htmlspecialchars($row['expected_payment'] ?? 'N/A') : 'N/A' ?></td>
                                    <td class="table-cell">
                                        <?php if ($row['professional'] === 'Artist' && !empty($row['work_type_preference'])): ?>
                                            <span class="text-xs bg-gray-100 px-2 py-1 rounded" title="<?= htmlspecialchars($row['work_type_preference']) ?>">
                                                <?= strlen($row['work_type_preference']) > 20 ? substr(htmlspecialchars($row['work_type_preference']), 0, 20) . '...' : htmlspecialchars($row['work_type_preference']) ?>
                                            </span>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td class="table-cell"><?= htmlspecialchars($row['followers']) ?></td>
                                    <td class="table-cell"><?= htmlspecialchars($row['experience']) ?></td>
                                    <td class="table-cell"><?= htmlspecialchars($row['language']) ?></td>
                                    <td class="table-cell"><?= $row['professional'] === 'Employee' ? htmlspecialchars($row['current_salary'] ?? 'N/A') : 'N/A' ?></td>
                                    <td class="table-cell">
                                        <img src="<?= htmlspecialchars($row['image_url']) ?>" 
                                             alt="Client Image" 
                                             class="w-12 h-12 rounded-full object-cover client-image"
                                             onclick="openImageModal('<?= htmlspecialchars($row['image_url']) ?>', '<?= htmlspecialchars($row['name']) ?>')">
                                    </td>
                                    <td class="table-cell">
                                        <?php if (!empty($row['resume_url'])): ?>
                                            <?php 
                                                $file_extension = pathinfo($row['resume_url'], PATHINFO_EXTENSION);
                                                $icon_class = 'fa-file-pdf';
                                                $btn_class = 'bg-blue-500 hover:bg-blue-600';
                                                
                                                // Assign appropriate icon based on file type
                                                if (in_array($file_extension, ['doc', 'docx'])) {
                                                    $icon_class = 'fa-file-word';
                                                    $btn_class = 'bg-blue-600 hover:bg-blue-700';
                                                } elseif (in_array($file_extension, ['xls', 'xlsx'])) {
                                                    $icon_class = 'fa-file-excel';
                                                    $btn_class = 'bg-green-600 hover:bg-green-700';
                                                } elseif (in_array($file_extension, ['ppt', 'pptx'])) {
                                                    $icon_class = 'fa-file-powerpoint';
                                                    $btn_class = 'bg-red-500 hover:bg-red-600';
                                                } elseif (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                                    $icon_class = 'fa-file-image';
                                                    $btn_class = 'bg-purple-500 hover:bg-purple-600';
                                                } elseif ($file_extension === 'txt') {
                                                    $icon_class = 'fa-file-alt';
                                                    $btn_class = 'bg-gray-600 hover:bg-gray-700';
                                                }
                                            ?>
                                            <a href="<?= htmlspecialchars('../' . $row['resume_url']) ?>" 
                                               target="_blank"
                                               class="btn <?= $btn_class ?> text-white px-3 py-1 rounded inline-flex items-center">
                                                <i class="fas <?= $icon_class ?> mr-1"></i> 
                                                View <?= strtoupper($file_extension) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-gray-400">No Resume</span>
                                        <?php endif; ?>
                    </td>
                                    <td class="table-cell">
                                        <div class="flex space-x-2">
                                            <button onclick="approveClient(<?= $row['id'] ?>)" 
                                                    class="action-btn bg-green-500 hover:bg-green-600">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button onclick="rejectClient(<?= $row['id'] ?>)" 
                                                    class="action-btn bg-red-500 hover:bg-red-600">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="20" class="table-cell text-center py-8">
                                    <i class="fas fa-inbox text-gray-400 text-4xl mb-2"></i>
                                    <p class="text-gray-500">No pending clients</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
        </table>
                <!-- Add pagination container -->
                <div id="pendingPagination" class="flex justify-center gap-2 mt-4"></div>
            </div>
    </div>

        <!-- Approved Clients Section -->
    <div class="table-container">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-900">Approved Clients</h2>
                <div class="flex gap-4">
                    <input type="text" 
                           id="approvedSearch" 
                           placeholder="Search..." 
                           class="px-4 py-2 border rounded-lg">
                    
                    <!-- Professional Type Filter -->
                    <select id="approvedProfessionalFilter" 
                            class="px-4 py-2 border rounded-lg" 
                            title="Filter by Professional Type">
                        <option value="">All Professionals</option>
                        <option value="Artist">Artist</option>
                        <option value="Employee">Employee</option>
                    </select>

                    <!-- Artist Category Filter -->
                    <select id="approvedCategoryFilter" 
                            class="px-4 py-2 border rounded-lg" 
                            title="Filter by Artist Category">
                        <option value="">All Categories</option>
                        <option value="Dating App Host">Dating App Host</option>
                        <option value="Video Live Streamers">Video Live Streamers</option>
                        <option value="Voice Live Streamers">Voice Live Streamers</option>
                        <option value="Singer">Singer</option>
                        <option value="Dancer">Dancer</option>
                        <option value="Actor / Actress">Actor / Actress</option>
                        <option value="Model">Model</option>
                        <option value="Artist / Painter">Artist / Painter</option>
                        <option value="Social Media Influencer">Social Media Influencer</option>
                        <option value="Content Creator">Content Creator</option>
                        <option value="Vlogger">Vlogger</option>
                        <option value="Gamer / Streamer">Gamer / Streamer</option>
                        <option value="YouTuber">YouTuber</option>
                        <option value="Anchor / Emcee / Host">Anchor / Emcee / Host</option>
                        <option value="DJ / Music Producer">DJ / Music Producer</option>
                        <option value="Photographer / Videographer">Photographer / Videographer</option>
                        <option value="Makeup Artist / Hair Stylist">Makeup Artist / Hair Stylist</option>
                        <option value="Fashion Designer / Stylist">Fashion Designer / Stylist</option>
                        <option value="Fitness Trainer / Yoga Instructor">Fitness Trainer / Yoga Instructor</option>
                        <option value="Motivational Speaker / Life Coach">Motivational Speaker / Life Coach</option>
                        <option value="Chef / Culinary Artist">Chef / Culinary Artist</option>
                        <option value="Child Artist">Child Artist</option>
                        <option value="Pet Performer / Pet Model">Pet Performer / Pet Model</option>
                        <option value="Instrumental Musician">Instrumental Musician</option>
                        <option value="Director / Scriptwriter / Editor">Director / Scriptwriter / Editor</option>
                        <option value="Voice Over Artist">Voice Over Artist</option>
                        <option value="Magician / Illusionist">Magician / Illusionist</option>
                        <option value="Stand-up Comedian">Stand-up Comedian</option>
                        <option value="Mimicry Artist">Mimicry Artist</option>
                        <option value="Poet / Storyteller">Poet / Storyteller</option>
                        <option value="Language Trainer / Public Speaking Coach">Language Trainer / Public Speaking Coach</option>
                        <option value="Craft Expert / DIY Creator">Craft Expert / DIY Creator</option>
                        <option value="Travel Blogger / Explorer">Travel Blogger / Explorer</option>
                        <option value="Astrologer / Tarot Reader">Astrologer / Tarot Reader</option>
                        <option value="Educator / Subject Matter Expert">Educator / Subject Matter Expert</option>
                        <option value="Tech Reviewer / Gadget Expert">Tech Reviewer / Gadget Expert</option>
                        <option value="Unboxing / Product Reviewer">Unboxing / Product Reviewer</option>
                        <option value="Business Coach / Startup Mentor">Business Coach / Startup Mentor</option>
                        <option value="Health & Wellness Coach">Health & Wellness Coach</option>
                        <option value="Event Anchor / Wedding Host">Event Anchor / Wedding Host</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="table-header px-6 py-3">Name</th>
                            <th class="table-header px-6 py-3">Age</th>
                            <th class="table-header px-6 py-3">Gender</th>
                            <th class="table-header px-6 py-3">Professional</th>
                            <th class="table-header px-6 py-3">Category/Role</th>
                            <th class="table-header px-6 py-3">City</th>
                            <th class="table-header px-6 py-3">Phone</th>
                            <th class="table-header px-6 py-3">Email</th>
                            <th class="table-header px-6 py-3">Influencer Category</th>
                            <th class="table-header px-6 py-3">Influencer Type</th>
                            <th class="table-header px-6 py-3">Instagram</th>
                            <th class="table-header px-6 py-3">Expected Payment</th>
                            <th class="table-header px-6 py-3">Work Type</th>
                            <th class="table-header px-6 py-3">Followers</th>
                            <th class="table-header px-6 py-3">Experience</th>
                            <th class="table-header px-6 py-3">Languages</th>
                            <th class="table-header px-6 py-3">Current Salary</th>
                            <th class="table-header px-6 py-3">Image</th>
                            <th class="table-header px-6 py-3">Resume</th>
                            <th class="table-header px-6 py-3">Actions</th>
            </tr>
                    </thead>
                    <tbody id="approvedTableBody">
                        <?php if ($approvedResult && mysqli_num_rows($approvedResult) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($approvedResult)): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="table-cell"><?= htmlspecialchars($row['name']) ?></td>
                                    <td class="table-cell"><?= $row['age'] ?></td>
                                    <td class="table-cell"><?= htmlspecialchars($row['gender']) ?></td>
                                    <td class="table-cell"><?= htmlspecialchars($row['professional']) ?></td>
                                    <td class="table-cell">
                                        <?= $row['professional'] === 'Artist' ? 
                                            htmlspecialchars($row['category']) : 
                                            htmlspecialchars($row['role']) ?>
                                    </td>
                                    <td class="table-cell"><?= htmlspecialchars($row['city']) ?></td>
                                    <td class="table-cell">
                                        <?php if (!empty($row['phone'])): ?>
                                            <a href="https://wa.me/91<?= htmlspecialchars($row['phone']) ?>" 
                                               target="_blank" 
                                               class="text-green-600 hover:text-green-800 font-medium flex items-center">
                                                <i class="fab fa-whatsapp mr-1"></i>
                                                <?= htmlspecialchars($row['phone']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-gray-400">No Phone</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="table-cell"><?= $row['professional'] === 'Artist' ? htmlspecialchars($row['email'] ?? 'N/A') : 'N/A' ?></td>
                                    <td class="table-cell"><?= $row['professional'] === 'Artist' ? htmlspecialchars($row['influencer_category'] ?? 'N/A') : 'N/A' ?></td>
                                    <td class="table-cell"><?= $row['professional'] === 'Artist' ? htmlspecialchars($row['influencer_type'] ?? 'N/A') : 'N/A' ?></td>
                                    <td class="table-cell">
                                        <?php if ($row['professional'] === 'Artist' && !empty($row['instagram_profile'])): ?>
                                            <a href="<?= htmlspecialchars($row['instagram_profile']) ?>" target="_blank" class="text-blue-500 hover:text-blue-700">
                                                <i class="fab fa-instagram mr-1"></i>View
                                            </a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td class="table-cell"><?= $row['professional'] === 'Artist' ? htmlspecialchars($row['expected_payment'] ?? 'N/A') : 'N/A' ?></td>
                                    <td class="table-cell">
                                        <?php if ($row['professional'] === 'Artist' && !empty($row['work_type_preference'])): ?>
                                            <span class="text-xs bg-gray-100 px-2 py-1 rounded" title="<?= htmlspecialchars($row['work_type_preference']) ?>">
                                                <?= strlen($row['work_type_preference']) > 20 ? substr(htmlspecialchars($row['work_type_preference']), 0, 20) . '...' : htmlspecialchars($row['work_type_preference']) ?>
                                            </span>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                    <td class="table-cell"><?= htmlspecialchars($row['followers']) ?></td>
                                    <td class="table-cell"><?= htmlspecialchars($row['experience']) ?></td>
                                    <td class="table-cell"><?= htmlspecialchars($row['language']) ?></td>
                                    <td class="table-cell"><?= $row['professional'] === 'Employee' ? htmlspecialchars($row['current_salary'] ?? 'N/A') : 'N/A' ?></td>
                                    <td class="table-cell">
                                        <img src="<?= htmlspecialchars($row['image_url']) ?>" 
                                             alt="Client Image" 
                                             class="w-12 h-12 rounded-full object-cover client-image"
                                             onclick="openImageModal('<?= htmlspecialchars($row['image_url']) ?>', '<?= htmlspecialchars($row['name']) ?>')">
                                    </td>
                                    <td class="table-cell">
                                        <?php if (!empty($row['resume_url'])): ?>
                                            <?php 
                                                $file_extension = pathinfo($row['resume_url'], PATHINFO_EXTENSION);
                                                $icon_class = 'fa-file-pdf';
                                                $btn_class = 'bg-blue-500 hover:bg-blue-600';
                                                
                                                // Assign appropriate icon based on file type
                                                if (in_array($file_extension, ['doc', 'docx'])) {
                                                    $icon_class = 'fa-file-word';
                                                    $btn_class = 'bg-blue-600 hover:bg-blue-700';
                                                } elseif (in_array($file_extension, ['xls', 'xlsx'])) {
                                                    $icon_class = 'fa-file-excel';
                                                    $btn_class = 'bg-green-600 hover:bg-green-700';
                                                } elseif (in_array($file_extension, ['ppt', 'pptx'])) {
                                                    $icon_class = 'fa-file-powerpoint';
                                                    $btn_class = 'bg-red-500 hover:bg-red-600';
                                                } elseif (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                                    $icon_class = 'fa-file-image';
                                                    $btn_class = 'bg-purple-500 hover:bg-purple-600';
                                                } elseif ($file_extension === 'txt') {
                                                    $icon_class = 'fa-file-alt';
                                                    $btn_class = 'bg-gray-600 hover:bg-gray-700';
                                                }
                                            ?>
                                            <a href="<?= htmlspecialchars('../' . $row['resume_url']) ?>" 
                                               target="_blank"
                                               class="btn <?= $btn_class ?> text-white px-3 py-1 rounded inline-flex items-center">
                                                <i class="fas <?= $icon_class ?> mr-1"></i> 
                                                View <?= strtoupper($file_extension) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-gray-400">No Resume</span>
                                        <?php endif; ?>
                    </td>
                                    <td class="table-cell">
                                        <div class="flex space-x-2">
                                            <button onclick="editClient(<?= $row['id'] ?>)" 
                                                    class="action-btn bg-blue-500 hover:bg-blue-600">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="deleteClient(<?= $row['id'] ?>)" 
                                                    class="action-btn bg-red-500 hover:bg-red-600">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <button onclick="toggleVisibility(<?= $row['id'] ?>, this)" 
                                                    class="action-btn <?= isset($row['is_visible']) && $row['is_visible'] ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-500 hover:bg-gray-600' ?>"
                                                    title="<?= isset($row['is_visible']) && $row['is_visible'] ? 'Visible on frontend' : 'Hidden from frontend' ?>">
                                                <i class="fas <?= isset($row['is_visible']) && $row['is_visible'] ? 'fa-eye' : 'fa-eye-slash' ?>"></i>
                                            </button>
                                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="20" class="table-cell text-center py-8">
                                    <i class="fas fa-inbox text-gray-400 text-4xl mb-2"></i>
                                    <p class="text-gray-500">No approved clients</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
        </table>
                <!-- Add pagination container -->
                <div id="approvedPagination" class="flex justify-center gap-2 mt-4"></div>
            </div>
        </div>
    </div>

    <script>
        // Show alert function
        function showAlert(message, type = 'success') {
            const alertContainer = document.getElementById('alertContainer');
            alertContainer.innerHTML = `
                <div class="alert ${type === 'success' ? 'alert-success' : 'alert-error'}">
                    <div class="flex items-center">
                        <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                        <span>${message}</span>
                    </div>
                </div>
            `;
            alertContainer.classList.remove('hidden');
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                alertContainer.classList.add('hidden');
            }, 5000);
        }

        // Utility functions
        function createPagination(totalPages, currentPage, containerId, onPageChange) {
            const container = document.getElementById(containerId);
            container.innerHTML = '';
            
            // Add previous button
            if (currentPage > 1) {
                const prevButton = document.createElement('button');
                prevButton.className = 'pagination-btn mx-1';
                prevButton.innerHTML = '<i class="fas fa-chevron-left"></i>';
                prevButton.onclick = () => onPageChange(currentPage - 1);
                container.appendChild(prevButton);
            }
            
            // Calculate page range
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);
            
            if (endPage - startPage < 4) {
                startPage = Math.max(1, endPage - 4);
            }
            
            // Add page buttons
            for (let i = startPage; i <= endPage; i++) {
                const button = document.createElement('button');
                button.className = `pagination-btn mx-1 ${i === currentPage ? 'active' : ''}`;
                button.textContent = i;
                button.onclick = () => onPageChange(i);
                container.appendChild(button);
            }
            
            // Add next button
            if (currentPage < totalPages) {
                const nextButton = document.createElement('button');
                nextButton.className = 'pagination-btn mx-1';
                nextButton.innerHTML = '<i class="fas fa-chevron-right"></i>';
                nextButton.onclick = () => onPageChange(currentPage + 1);
                container.appendChild(nextButton);
            }
        }

        // Load clients function
        async function loadClients(status, page = 1, search = '', professionalFilter = '', categoryFilter = '') {
            try {
                let queryParams = `status=${status}&page=${page}&search=${search}`;
                
                // Add professional filter if provided
                if (professionalFilter) {
                    queryParams += `&professional=${encodeURIComponent(professionalFilter)}`;
                }
                
                // Add category filter if provided
                if (categoryFilter) {
                    queryParams += `&category=${encodeURIComponent(categoryFilter)}`;
                }
                
                const response = await fetch(`fetch_clients.php?${queryParams}`);
                const data = await response.json();
                
                const tbody = document.getElementById(`${status}TableBody`);
                
                if (data.clients.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="20" class="table-cell text-center py-8">
                                <i class="fas fa-info-circle text-gray-400 text-4xl mb-2"></i>
                                <p class="text-gray-500">No ${status} clients found</p>
                            </td>
                        </tr>
                    `;
                    document.getElementById(`${status}Pagination`).innerHTML = '';
                    return;
                }
                
                tbody.innerHTML = '';
                
                data.clients.forEach(client => {
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-gray-50';
                    
                    // Format image URL correctly
                    let imageUrl = client.image_url;
                    if (!imageUrl.startsWith('http')) {
                        imageUrl = '../uploads/' + imageUrl;
                    }

                    // Format resume URL correctly
                    let resumeHtml = '';
                    if (client.resume_url) {
                        const resumePath = client.resume_url.startsWith('http') ? 
                            client.resume_url : 
                            '../' + client.resume_url;
                            
                        // Determine file type and set appropriate icon
                        const fileExt = resumePath.split('.').pop().toLowerCase();
                        let iconClass = 'fa-file-pdf';
                        let btnClass = 'bg-blue-500 hover:bg-blue-600';
                        
                        if (['doc', 'docx'].includes(fileExt)) {
                            iconClass = 'fa-file-word';
                            btnClass = 'bg-blue-600 hover:bg-blue-700';
                        } else if (['xls', 'xlsx'].includes(fileExt)) {
                            iconClass = 'fa-file-excel';
                            btnClass = 'bg-green-600 hover:bg-green-700';
                        } else if (['ppt', 'pptx'].includes(fileExt)) {
                            iconClass = 'fa-file-powerpoint';
                            btnClass = 'bg-red-500 hover:bg-red-600';
                        } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExt)) {
                            iconClass = 'fa-file-image';
                            btnClass = 'bg-purple-500 hover:bg-purple-600';
                        } else if (fileExt === 'txt') {
                            iconClass = 'fa-file-alt';
                            btnClass = 'bg-gray-600 hover:bg-gray-700';
                        }
                        
                        resumeHtml = `
                            <a href="${resumePath}" target="_blank" class="btn ${btnClass} text-white px-3 py-1 rounded inline-flex items-center">
                                <i class="fas ${iconClass} mr-1"></i> View ${fileExt.toUpperCase()}
                            </a>
                        `;
                    } else {
                        resumeHtml = `<span class="text-gray-400">No Resume</span>`;
                    }
                    
                    // Helper function to create Instagram link
                    const instagramHtml = client.professional === 'Artist' && client.instagram_profile ? 
                        `<a href="${client.instagram_profile}" target="_blank" class="text-blue-500 hover:text-blue-700">
                            <i class="fab fa-instagram mr-1"></i>View
                         </a>` : 'N/A';
                    
                    // Helper function to create work type preference display
                    const workTypeHtml = client.professional === 'Artist' && client.work_type_preference ? 
                        `<span class="text-xs bg-gray-100 px-2 py-1 rounded" title="${client.work_type_preference}">
                            ${client.work_type_preference.length > 20 ? client.work_type_preference.substring(0, 20) + '...' : client.work_type_preference}
                         </span>` : 'N/A';
                    
                    tr.innerHTML = `
                        <td class="table-cell">${client.name}</td>
                        <td class="table-cell">${client.age}</td>
                        <td class="table-cell">${client.gender}</td>
                        <td class="table-cell">${client.professional}</td>
                        <td class="table-cell">${client.professional === 'Artist' ? (client.category || '-') : (client.role || '-')}</td>
                        <td class="table-cell">${client.city || '-'}</td>
                        <td class="table-cell">
                            ${client.phone ? 
                                `<a href="https://wa.me/91${client.phone}" target="_blank" class="text-green-600 hover:text-green-800 font-medium flex items-center">
                                    <i class="fab fa-whatsapp mr-1"></i>${client.phone}
                                 </a>` : 
                                '<span class="text-gray-400">No Phone</span>'
                            }
                        </td>
                        <td class="table-cell">${client.professional === 'Artist' ? (client.email || 'N/A') : 'N/A'}</td>
                        <td class="table-cell">${client.professional === 'Artist' ? (client.influencer_category || 'N/A') : 'N/A'}</td>
                        <td class="table-cell">${client.professional === 'Artist' ? (client.influencer_type || 'N/A') : 'N/A'}</td>
                        <td class="table-cell">${instagramHtml}</td>
                        <td class="table-cell">${client.professional === 'Artist' ? (client.expected_payment || 'N/A') : 'N/A'}</td>
                        <td class="table-cell">${workTypeHtml}</td>
                        <td class="table-cell">${client.followers || '-'}</td>
                        <td class="table-cell">${client.experience || '-'}</td>
                        <td class="table-cell">${client.language || '-'}</td>
                        <td class="table-cell">${client.professional === 'Employee' ? (client.current_salary || 'N/A') : 'N/A'}</td>
                        <td class="table-cell">
                            <img src="${imageUrl}" alt="Profile" class="h-12 w-12 rounded-full object-cover client-image"
                                 onclick="openImageModal('${imageUrl}', '${client.name}')">
                        </td>
                        <td class="table-cell">${resumeHtml}</td>
                        <td class="table-cell">
                            ${status === 'pending' ? `
                                <button onclick="approveClient(${client.id})" class="btn btn-approve mr-2">
                                    <i class="fas fa-check mr-1"></i> Approve
                                </button>
                                <button onclick="rejectClient(${client.id})" class="btn btn-reject">
                                    <i class="fas fa-times mr-1"></i> Reject
                                </button>
                            ` : `
                                <div class="flex space-x-2">
                                    <button onclick="editClient(${client.id})" class="btn btn-edit">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </button>
                                    <button onclick="deleteClient(${client.id})" class="btn btn-delete">
                                        <i class="fas fa-trash mr-1"></i> Delete
                                    </button>
                                    <button onclick="toggleVisibility(${client.id}, this)" 
                                            class="btn ${client.is_visible ? 'bg-green-500 hover:bg-green-600' : 'bg-gray-500 hover:bg-gray-600'}"
                                            title="${client.is_visible ? 'Visible on frontend' : 'Hidden from frontend'}">
                                        <i class="fas ${client.is_visible ? 'fa-eye' : 'fa-eye-slash'} mr-1"></i>
                                    </button>
                                </div>
                            `}
                        </td>
                    `;
                    tbody.appendChild(tr);
                });

                createPagination(
                    Math.ceil(data.total / 10),
                    page,
                    `${status}Pagination`,
                    (newPage) => loadClients(status, newPage, search, professionalFilter, categoryFilter)
                );
            } catch (error) {
                console.error('Error loading clients:', error);
                const tbody = document.getElementById(`${status}TableBody`);
                tbody.innerHTML = `
                    <tr>
                        <td colspan="20" class="table-cell text-center py-8">
                            <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-2"></i>
                            <p class="text-red-500">Error loading clients. Please try again.</p>
                        </td>
                    </tr>
                `;
            }
        }

        // Client action functions
        async function approveClient(id) {
            if (!confirm('Are you sure you want to approve this client?')) return;
            
            try {
                const response = await fetch(`approve_client.php?id=${id}`);
                const data = await response.json();
                
                if (data.status === 'success') {
                    showAlert(data.message);
                    loadClients('pending');
                    loadClients('approved');
                    
                    // Update counts
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showAlert(data.message, 'error');
                }
            } catch (error) {
                console.error('Error approving client:', error);
                showAlert('Error approving client. Please try again.', 'error');
            }
        }

        async function rejectClient(id) {
            if (!confirm('Are you sure you want to reject this client?')) return;
            
            try {
                const response = await fetch(`reject_client.php?id=${id}`);
                const data = await response.json();
                
                if (data.status === 'success') {
                    showAlert(data.message);
                    loadClients('pending');
                    
                    // Update counts
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showAlert(data.message, 'error');
                }
            } catch (error) {
                console.error('Error rejecting client:', error);
                showAlert('Error rejecting client. Please try again.', 'error');
            }
        }

        function editClient(id) {
            window.location.href = `edit_client_form.php?id=${id}`;
        }

        async function deleteClient(id) {
            if (!confirm('Are you sure you want to delete this client? This action cannot be undone.')) return;
            
            try {
                const response = await fetch(`delete_client.php?id=${id}`);
                const data = await response.json();
                
                if (data.status === 'success') {
                    showAlert(data.message);
                    loadClients('approved');
                    
                    // Update counts
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showAlert(data.message, 'error');
                }
            } catch (error) {
                console.error('Error deleting client:', error);
                showAlert('Error deleting client. Please try again.', 'error');
            }
        }

        async function toggleVisibility(id, button) {
            if (!confirm('Are you sure you want to toggle visibility of this client?')) return;
            
            try {
                const response = await fetch(`toggle_visibility.php?id=${id}`);
                const data = await response.json();
                
                console.log('Toggle visibility response:', data); // Debug line to check response
                
                if (data.status === 'success') {
                    showAlert(data.message);
                    
                    // Update button appearance based on new visibility status
                    if (data.is_visible) {
                        button.classList.remove('bg-gray-500', 'hover:bg-gray-600');
                        button.classList.add('bg-green-500', 'hover:bg-green-600');
                        button.querySelector('i').classList.remove('fa-eye-slash');
                        button.querySelector('i').classList.add('fa-eye');
                        button.title = 'Visible on frontend';
                    } else {
                        button.classList.remove('bg-green-500', 'hover:bg-green-600');
                        button.classList.add('bg-gray-500', 'hover:bg-gray-600');
                        button.querySelector('i').classList.remove('fa-eye');
                        button.querySelector('i').classList.add('fa-eye-slash');
                        button.title = 'Hidden from frontend';
                    }
                    
                    // Force reload of the approved clients to ensure UI is in sync with the database
                    setTimeout(() => {
                        loadClients('approved');
                    }, 500);
                } else {
                    showAlert(data.message, 'error');
                }
            } catch (error) {
                console.error('Error toggling visibility:', error);
                showAlert('Error toggling visibility. Please try again.', 'error');
            }
        }

        // Search and filter handlers
        let pendingSearchTimeout, approvedSearchTimeout;

        document.getElementById('pendingSearch').addEventListener('input', (e) => {
            clearTimeout(pendingSearchTimeout);
            pendingSearchTimeout = setTimeout(() => {
                loadClients('pending', 1, e.target.value, document.getElementById('pendingFilter').value);
            }, 300);
        });

        document.getElementById('pendingFilter').addEventListener('change', (e) => {
            loadClients('pending', 1, document.getElementById('pendingSearch').value, e.target.value);
        });

        document.getElementById('approvedSearch').addEventListener('input', (e) => {
            clearTimeout(approvedSearchTimeout);
            approvedSearchTimeout = setTimeout(() => {
                loadClients(
                    'approved',
                    1,
                    e.target.value,
                    document.getElementById('approvedProfessionalFilter').value,
                    document.getElementById('approvedCategoryFilter').value
                );
            }, 300);
        });

        document.getElementById('approvedProfessionalFilter').addEventListener('change', (e) => {
            loadClients(
                'approved',
                1,
                document.getElementById('approvedSearch').value,
                e.target.value,
                document.getElementById('approvedCategoryFilter').value
            );
        });

        document.getElementById('approvedCategoryFilter').addEventListener('change', (e) => {
            loadClients(
                'approved',
                1,
                document.getElementById('approvedSearch').value,
                document.getElementById('approvedProfessionalFilter').value,
                e.target.value
            );
        });

        // Initial load
        document.addEventListener('DOMContentLoaded', () => {
            loadClients('pending');
            loadClients('approved');
            
            // Add filter dependency logic
            const professionalFilter = document.getElementById('approvedProfessionalFilter');
            const categoryFilter = document.getElementById('approvedCategoryFilter');
            
            professionalFilter.addEventListener('change', function() {
                if (this.value !== 'Artist') {
                    categoryFilter.value = '';
                    categoryFilter.disabled = true;
                } else {
                    categoryFilter.disabled = false;
                }
            });
        });
        
        // Image Modal Functions
        function openImageModal(imageSrc, clientName) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            const modalCaption = document.getElementById('modalCaption');
            
            // Format image URL correctly if needed
            if (!imageSrc.startsWith('http') && !imageSrc.startsWith('../')) {
                imageSrc = '../' + imageSrc;
            }
            
            modalImg.src = imageSrc;
            modalCaption.textContent = clientName || 'Client Image';
            
            // Show modal with animation
            modal.classList.add('active');
            
            // Close modal when clicking outside image
            modal.addEventListener('click', function(event) {
                if (event.target === modal) {
                    closeImageModal();
                }
            });
            
            // Close modal on ESC key
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape') {
                    closeImageModal();
                }
            });
        }
        
        function closeImageModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.remove('active');
        }
    </script>
</body>
</html>
