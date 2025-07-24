<?php
require '../auth/auth.php';
require '../includes/db_connect.php';

// ✅ Pagination Setup
$limit = 6; // Devices per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// ✅ Fetch Devices with Filters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$categoryFilter = isset($_GET['category']) ? trim($_GET['category']) : '';

// ✅ Base Query
$query = "SELECT * FROM devices WHERE 1";
$params = [];
$types = "";

// ✅ Apply Filters
if (!empty($search)) {
    $query .= " AND device_name LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

if (!empty($categoryFilter)) {
    $query .= " AND category = ?";
    $params[] = $categoryFilter;
    $types .= "s";
}

// ✅ Apply Pagination (Fixed: Remove `?` for `LIMIT`)
$query .= " ORDER BY created_at DESC LIMIT $start, $limit";
$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// ✅ Count Total Devices (Fixed: Add Filters)
$countQuery = "SELECT COUNT(*) as total FROM devices WHERE 1";
$countParams = [];
$countTypes = "";

if (!empty($search)) {
    $countQuery .= " AND device_name LIKE ?";
    $countParams[] = "%$search%";
    $countTypes .= "s";
}

if (!empty($categoryFilter)) {
    $countQuery .= " AND category = ?";
    $countParams[] = $categoryFilter;
    $countTypes .= "s";
}

$countStmt = $conn->prepare($countQuery);
if (!empty($countParams)) {
    $countStmt->bind_param($countTypes, ...$countParams);
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalDevices = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalDevices / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Dashboard</title>

    <!-- ✅ Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /* ✅ General Styling */
        body {
            background: radial-gradient(circle, rgba(255,255,255,1) 0%, rgba(230,230,230,1) 100%);
        }

         /* ✅ Table Responsive */
         .table-responsive {
            overflow-x: auto;
        }


        /* ✅ Table Styling */
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }

        /* ✅ Navbar */
        .navbar {
            background: linear-gradient(135deg, #d90429, #ef233c);
        }

        /* ✅ Footer */
        footer {
            background: black;
            color: white;
            padding: 20px;
            text-align: center;
            margin-top: 20px;
        }

        /* ✅ Loading Spinner */
        .loading-spinner {
            display: none;
        }

        /* ✅ Responsive Fixes */
        @media (max-width: 768px) {
            .btn-sm {
                width: 100%;
                margin-bottom: 5px;
            }

            .pagination-btn {
                display: block;
                width: 100%;
                margin-bottom: 5px;
            }
        }
    </style>
</head>

<!-- ✅ Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand text-white fw-bold" href="#">Admin Panel</a>
        
        <!-- ✅ Button Wrapper -->
        <div class="d-flex gap-2">
    <button class="btn btn-light text-danger fw-bold" data-bs-toggle="modal" data-bs-target="#addDeviceModal">
        ➕ Add Device
    </button>

    
    <button class="btn btn-warning fw-bold" onclick="window.location.href='../devices.php'">
        Devices
    </button>

    <a class="nav-link fw-semibold px-3" href="./main_dashboard.php"><button class="btn btn-warning fw-bold"">
        Main Dashboard
    </button></a>
</div>

    </div>
</nav>


<!-- ✅ Spacing for Navbar -->
<div style="height: 80px;"></div>

<!-- ✅ Filters -->
<div class="container my-4">
    <div class="row g-2">
        <div class="col-md-4 col-12">
            <input type="text" id="search" class="form-control" placeholder="🔎 Search by Name">
        </div>
        <div class="col-md-4 col-12">
            <select id="categoryFilter" class="form-select">
            <option value="">📂 Filter by Category</option>
    <option value="Laptop">💻 Laptop</option>
    <option value="Desktop">🖥️ Desktop</option>
    <option value="Smartphone">📱 Smartphone</option>
    <option value="CCTV Camera">📷 CCTV Camera</option>
    <option value="Biometric">🔐 Biometric</option>
    <option value="Printer">🖨️ Printer</option>
            </select>
        </div>
        <div class="col-md-4 col-12">
            <button id="resetFilters" class="btn btn-secondary w-100">🔄 Reset</button>
        </div>
    </div>
</div>

<!-- ✅ Device List Table -->
<!-- ✅ Device List Table (Responsive) -->
<div class="container my-5">
    <h2 class="text-center text-danger fw-bold">📋 Device List</h2>
    <div class="table-responsive">
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>📷 Image</th>
                    <th>📝 Name</th>
                    <th>📂 Category</th>
                    <th>💰 Price</th>
                    <th>📞 Contact</th>
                    <th>🔧 Actions</th>
                </tr>
            </thead>
    <tbody id="device-list">
        <!-- Devices will be loaded here dynamically -->
    </tbody>
        </table>
    </div>

</div>

<!-- ✅ Pagination (Fixed: Pass Filters) -->
<div class="container text-center">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <button class="btn btn-dark pagination-btn" data-page="<?= $i; ?>">
            <?= $i; ?>
        </button>
    <?php endfor; ?>
</div>

<!-- ✅ Add Device Modal -->
<div class="modal fade" id="addDeviceModal" tabindex="-1" aria-labelledby="addDeviceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">➕ Add New Device</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="device-form">
                <div class="modal-body">
                    <label class="form-label">Device Name:</label>
                    <input type="text" name="device_name" class="form-control" required>

                    <label class="form-label">Category:</label>
                    <select name="category" class="form-select" required>
                        <option value="Laptop">💻 Laptop</option>
                        <option value="Desktop">🖥️ Desktop</option>
                        <option value="Smartphone">📱 Smartphone</option>
                        <option value="CCTV Camera">📷 CCTV Camera</option>
                        <option value="Biometric">🔐 Biometric</option>
                        <option value="Printer">🖨️ Printer</option>
                    </select>

                    <label class="form-label">Price (₹):</label>
                    <input type="number" name="price" class="form-control" required>

                    <label class="form-label">Description:</label>
                    <textarea name="description" class="form-control" required></textarea>

                    <label class="form-label">Contact Number (WhatsApp):</label>
                    <input type="text" name="contact_number" class="form-control" required>

                    <label class="form-label">Upload Image:</label>
                    <input type="file" name="image" class="form-control" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="submit-btn" class="btn btn-danger">
                        ➕ Add Device <span id="loader" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ✅ Edit Device Modal -->
<div class="modal fade" id="editDeviceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">✏️ Edit Device</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="edit-device-form">
                <div class="modal-body">
                    <input type="hidden" id="edit_device_id" name="id">
                    
                    <label class="form-label">Device Name:</label>
                    <input type="text" id="edit_device_name" name="device_name" class="form-control" required>

                    <label class="form-label">Category:</label>
                    <select id="edit_category" name="category" class="form-select" required>
                        <option value="Laptop">💻 Laptop</option>
                        <option value="Desktop">🖥️ Desktop</option>
                        <option value="Smartphone">📱 Smartphone</option>
                        <option value="CCTV Camera">📷 CCTV Camera</option>
                        <option value="Biometric">🔐 Biometric</option>
                        <option value="Printer">🖨️ Printer</option>
                    </select>

                    <label class="form-label">Price (₹):</label>
                    <input type="number" id="edit_price" name="price" class="form-control" required>

                    <label class="form-label">Description:</label>
                    <textarea id="edit_description" name="description" class="form-control" required></textarea>

                    <label class="form-label">Contact Number:</label>
                    <input type="text" id="edit_contact_number" name="contact_number" class="form-control" required>

                    <!-- ✅ Display Existing Image -->
                    <label class="form-label">Current Image:</label>
                    <img id="edit_device_image" src="" class="img-fluid mb-2 rounded" width="100">

                    <label class="form-label">Upload New Image:</label>
                    <input type="file" name="image" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">✏️ Update Device</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- ✅ Footer -->
<footer>© 2025 Admin Panel | All Rights Reserved.</footer>

<!-- ✅ Bootstrap & Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
    function fetchDevices(search = "", category = "", page = 1) {
        $.ajax({
            url: "fetch_devices.php",
            type: "GET",
            data: { search: search, category: category, page: page },
            success: function (response) {
                $("#device-list").html(response);
            },
            error: function () {
                alert("❌ Error fetching devices.");
            }
        });
    }

    // ✅ Initial Load of Devices
    fetchDevices();

    // ✅ Add Device Form Submission
    $("#device-form").on("submit", function (event) {
        event.preventDefault();
        $("#submit-btn").attr("disabled", true);
        $("#loader").removeClass("d-none");

        let formData = new FormData(this);
        $.ajax({
            url: "upload_device.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
                $("#submit-btn").attr("disabled", false);
                $("#loader").addClass("d-none");

                if (response.status === "success") {
                    $("#addDeviceModal").modal("hide");
                    $("#successModal").modal("show");
                    fetchDevices();
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                $("#submit-btn").attr("disabled", false);
                $("#loader").addClass("d-none");
                alert("❌ Error submitting form. Please try again.");
            }
        });
    });


    // ✅ Search Filter (Live Search)
    let debounceTimer;
    $("#search").on("keyup", function () {
        clearTimeout(debounceTimer);
        let searchValue = $(this).val();
        debounceTimer = setTimeout(() => {
            fetchDevices(searchValue, $("#categoryFilter").val());
        }, 500);
    });

    // ✅ Category Filter
    $("#categoryFilter").on("change", function () {
        fetchDevices($("#search").val(), $(this).val());
    });

    // ✅ Reset Filters
    $("#resetFilters").on("click", function () {
        $("#search").val("");
        $("#categoryFilter").val("");
        fetchDevices();
    });

    // ✅ Delete Device
    $(document).on("click", ".delete-device", function () {
        let deviceId = $(this).data("id");
        if (confirm("Are you sure you want to delete this device?")) {
            $.ajax({
                url: "delete_device.php",
                type: "POST",
                data: { id: deviceId },
                dataType: "json",
                success: function (response) {
                    if (response.status === "success") {
                        fetchDevices();
                    } else {
                        alert(response.message);
                    }
                },
                error: function () {
                    alert("❌ Error deleting device. Please try again.");
                }
            });
        }
    });

    // ✅ Open Edit Modal & Fetch Data
    $(document).on("click", ".edit-device", function () {
        let deviceId = $(this).data("id");
        $("#editDeviceModal").modal("show");

        $.ajax({
            url: "fetch_device_by_id.php",
            type: "GET",
            data: { id: deviceId },
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    $("#edit_device_id").val(response.data.id);
                    $("#edit_device_name").val(response.data.device_name);
                    $("#edit_category").val(response.data.category);
                    $("#edit_price").val(response.data.price);
                    $("#edit_description").val(response.data.description);
                    $("#edit_contact_number").val(response.data.contact_number);
                    $("#edit_device_image").attr("src", response.data.image_url);
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert("❌ Error fetching device data.");
            }
        });
    });

    // ✅ Submit Edited Device
    $("#edit-device-form").on("submit", function (event) {
        event.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url: "edit_device.php",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    $("#editDeviceModal").modal("hide");
                    fetchDevices();
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert("❌ Error updating device.");
            }
        });
    });
    
    // ✅ Pagination Click
    $(document).on("click", ".pagination-btn", function (e) {
        e.preventDefault();
        let page = $(this).data("page");
        fetchDevices($("#search").val(), $("#categoryFilter").val(), page);
    });
});

</script>

</body>
</html>
