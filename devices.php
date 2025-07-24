<?php
require 'includes/db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devices | Used Laptop/Mobile Devices</title>

    <!-- ✅ Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ✅ Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

<!-- Font Awesome Icons -->
<link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
      rel="stylesheet"
    />


    <style>
        /* ✅ General Styling */
body {
    font-family: 'Poppins', sans-serif;
    background: #fff;
    color: #333;
}

.navbar {
    background-color: white !important;
}
.navbar-brand {
    font-size: 1.5rem;
    font-weight: bold;
    color: black !important;
    display: flex;
    align-items: center;
}
.logo-img {
    height: 40px;
    max-width: 120px;
    object-fit: contain;
}
.nav-link {
    color: black !important;
    font-size: 1.1rem;
    transition: 0.3s;
}
.nav-link:hover {
    color: #ffcc00 !important;
}
.navbar-toggler {
    border: none;
}
.navbar-toggler:focus {
    box-shadow: none;
}
.navbar-dark .navbar-toggler-icon {
    filter: invert(1);
}
.dropdown-menu {
    background-color: white !important;
    border: 1px solid #ddd;
}
.dropdown-item {
    color: black !important;
}
.dropdown-item:hover {
    background-color: #f8f9fa !important;
}
.search-box {
    width: 250px;
}
@media (max-width: 768px) {
    .search-box {
        width: 100%;
    }
    .logo-img {
        height: 35px;
    }
}

        /* ✅ Make Hero Section Full-Screen */
#heroCarousel {
    height: 100vh;
    position: relative;
}

/* ✅ Ensure Images Cover Entire Screen */
.hero-img {
    height: 100vh;
    object-fit: cover;
    width: 100%;
}

/* ✅ Dark Gradient Overlay for Text Visibility */
.hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to bottom, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.5));
    z-index: 1;
}

/* ✅ Centered & Styled Text */
.carousel-caption {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    z-index: 2;
    width: 80%;
}

/* ✅ Stylish Title */
.hero-title {
    font-size: 4.5rem;
    font-weight: 800;
    font-family: 'Playfair Display', serif;
    color: white;
    text-shadow: 3px 3px 10px rgba(0, 0, 0, 0.8);
    line-height: 1.2;
    margin-bottom: 15px;
}

/* ✅ Subtitle */
.hero-subtitle {
    font-size: 1.6rem;
    color: #f8f9fa;
    font-family: 'Poppins', sans-serif;
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.6);
    margin-bottom: 20px;
}

/* ✅ Call-to-Action Button */
.hero-btn {
    font-size: 1.3rem;
    padding: 15px 40px;
    border-radius: 50px;
    background: #ff3d00;
    color: white;
    font-weight: bold;
    text-transform: uppercase;
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.3);
    transition: 0.3s ease-in-out;
}

.hero-btn:hover {
    background: #cc2900;
    transform: scale(1.05);
}


        /* ✅ Filters Section */
.filters-container {
    background: #fff;
    padding: 15px 20px;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
}

.filters-container .form-select,
.filters-container .form-control {
    max-width: 180px;
    flex-grow: 1;
    font-size: 1rem;
    padding: 10px;
}

/* ✅ Reset Button */
#resetFilters {
    font-size: 1rem;
    font-weight: bold;
    padding: 10px;
    background: #ff3d00;
    border: none;
    border-radius: 8px;
    color: white;
    transition: 0.3s;
}

#resetFilters:hover {
    background: #cc2900;
}

/* ✅ Device Card */
.device-card {
    background: #fff;
    border-radius: 12px;
    padding: 18px;
    /* box-shadow: 0px 6px 14px rgba(0, 0, 0, 0.12); */
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
    text-align: center;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    /* border: 2px solid #f8f9fa; */
    position: relative;
}

.device-card:hover {
    transform: scale(1.05);
    box-shadow: 0px 8px 18px rgba(0, 0, 0, 0.18);
}

/* ✅ Device Image */
.device-img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: 10px;
    /* border: 2px solid #e0e0e0; */
    transition: transform 0.3s ease-in-out;
}

.device-img:hover {
    transform: scale(1.02);
}

/* ✅ Device Title */
.device-title {
    font-size: 1.1rem;
    font-weight: bold;
    color: #333;
    margin-top: 12px;
    text-align: center;
    word-wrap: break-word;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

/* ✅ Star Rating */
.device-rating {
    font-size: 1rem;
    color: #ffcc00;
    margin-bottom: 8px;
}

/* ✅ Price */
.device-price {
    font-size: 1.4rem;
    font-weight: bold;
    color: #d9534f;
    margin: 10px 0;
}

/* ✅ Stock Status */
.device-stock-status {
    font-size: 1rem;
    font-weight: bold;
    margin-bottom: 8px;
}

/* ✅ Stock Indicators */
.in-stock {
    color: green;
    font-weight: bold;
}

.out-of-stock {
    color: red;
    font-weight: bold;
}

/* ✅ Contact & Description Buttons */
.device-footer {
    margin-top: auto; /* Pushes the description button to the bottom */
    display: flex;
    flex-direction: column;
}

.device-footer .btn {
    font-size: 1rem;
    padding: 12px;
    border-radius: 8px;
    font-weight: bold;
    transition: transform 0.3s ease-in-out;
    width: 100%;
}

.device-footer .btn:hover {
    transform: scale(1.05);
}

/* ✅ Description Button */
.description-btn {
    background: #007bff;
    color: white;
    border-radius: 6px;
    text-align: center;
    transition: background 0.3s ease-in-out;
}

.description-btn:hover {
    background: #0056b3;
}

/* ✅ Pagination Styling */
.pagination .page-item.active .page-link {
    background: #ff3d00;
    border-color: #ff3d00;
    color: #fff;
}

.pagination .page-link {
    color: #333;
    font-weight: bold;
    padding: 10px 15px;
    transition: 0.3s;
}

.pagination .page-link:hover {
    background: #ff3d00;
    color: white;
}

/* ✅ Description Modal */
#descriptionModal .modal-content {
    border-radius: 12px;
}

#descriptionModal .modal-header {
    background: #17a2b8;
    color: white;
    border-radius: 12px 12px 0 0;
}

/* ✅ Footer Styling */
.footer {
            background-color: #343a40;
            color: white;
            padding: 50px 0;
        }
        .footer a {
            color: white;
            text-decoration: none;
            transition: 0.3s;
        }
        .footer a:hover {
            color: #ffcc00;
        }
        .footer .social-icons a {
            font-size: 1.5rem;
            margin-right: 15px;
            display: inline-block;
            transition: 0.3s;
        }
        .footer .social-icons a:hover {
            transform: scale(1.2);
        }
        .footer hr {
            border-color: rgba(255, 255, 255, 0.2);
        }
        @media (max-width: 768px) {
            .footer .col-md-4 {
                text-align: center;
                margin-bottom: 20px;
            }
        }

        /* 🔽 Search Suggestions Dropdown */
#search-suggestions {
    top: 40px;
    max-height: 250px;
    overflow-y: auto;
    border-radius: 5px;
    box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.2);
    z-index: 1050;
}

#search-suggestions .dropdown-item {
    cursor: pointer;
    padding: 10px 15px;
    font-size: 1rem;
    transition: background 0.3s;
}

#search-suggestions .dropdown-item:hover {
    background: #ffcc00;
    color: black;
}

    </style>
</head>
<body>

<!-- ✅ Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <!-- 🌟 Logo (Replace 'logo.png' with your actual logo file) -->
        <a class="navbar-brand" href="index.php">
            <img src="/logo.png" alt="Brand Logo" class="logo-img">
        </a>

        <!-- 📱 Mobile Menu Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- 🌎 Navigation Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php"> Home</a>
                </li>

                <!-- 📂 Dropdown Menu -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="categoryDropdown" role="button" data-bs-toggle="dropdown">
                        Categories
                    </a>

                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#deviceContainer">Laptops</a></li>
                        <li><a class="dropdown-item" href="#deviceContainer">Desktops</a></li>
                        <li><a class="dropdown-item" href="#deviceContainer">Smartphone</a></li>
                        <li><a class="dropdown-item" href="#deviceContainer">CCTV Cameras</a></li>
                        <li><a class="dropdown-item" href="#deviceContainer">Biometric Devices</a></li>
                        <li><a class="dropdown-item" href="#deviceContainer">Printers</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="/pages/about-page/about.html">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/pages/contact-page/contact-page.html">Contact</a>
                </li>
            </ul>

          <!-- 🔎 Search Box with Dropdown -->
          <form id="navbar-search-form" class="d-flex position-relative">
    <input id="search-box" class="form-control search-box me-2" type="search" placeholder="Search for devices..." autocomplete="off">
    <button class="btn btn-danger" type="submit"><i class="fas fa-search"></i></button>

    <!-- 🔽 Dropdown for Search Suggestions -->
    <ul id="search-suggestions" class="dropdown-menu w-100 position-absolute d-none"></ul>
</form>





        </div>
    </div>
</nav>


<!-- ✅ Full-Screen Hero Section -->
<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <!-- 🌟 Slide 1: Laptops & Devices -->
        <div class="carousel-item active">
            <div class="hero-overlay"></div> 
            <img src="/public/bannerForDevices/laptop.webp" class="d-block w-100 hero-img" alt="Banner 1">
            <div class="carousel-caption">
                <h1 class="hero-title">Upgrade Your Work Setup with <br> Premium</h1>
                <p class="hero-subtitle">Top-notch refurbished laptops, desktops, and accessories at unbeatable prices.</p>
                <a href="#deviceContainer" class="btn hero-btn">🛒 Explore Devices</a>
            </div>
        </div>

        <!-- 🌟 Slide 2: Smartphones -->
        <div class="carousel-item">
            <div class="hero-overlay"></div>
            <img src="/public/bannerForDevices/smartphone.webp" class="d-block w-100 hero-img" alt="Banner 3">
            <div class="carousel-caption">
                <h1 class="hero-title">Compact & Smart Moblies</h1>
                <p class="hero-subtitle">Perfect SmartPhone solutions for businesses and professionals.</p>
                <a href="#deviceContainer" class="btn hero-btn">🖨️ View SmartPhones</a>
            </div>
        </div>

        <!-- 🌟 Slide 3: Security Solutions -->
        <div class="carousel-item">
            <div class="hero-overlay"></div>
            <img src="/public/bannerForDevices/cctv.webp" class="d-block w-100 hero-img" alt="Banner 2">
            <div class="carousel-caption">
                <h1 class="hero-title">Secure Your Space with <br> CCTV & Biometric Solutions</h1>
                <p class="hero-subtitle">Protect your home and office with high-quality security devices.</p>
                <a href="#deviceContainer" class="btn hero-btn">🔍 Browse Security Devices</a>
            </div>
        </div>

        <!-- 🌟 Slide 4: Printers & Accessories -->
        <div class="carousel-item">
            <div class="hero-overlay"></div>
            <img src="/public/bannerForDevices/printer.webp" class="d-block w-100 hero-img" alt="Banner 3">
            <div class="carousel-caption">
                <h1 class="hero-title">High-Quality Printers & Accessories</h1>
                <p class="hero-subtitle">Perfect printing solutions for businesses and professionals.</p>
                <a href="#deviceContainer" class="btn hero-btn">🖨️ View Printers</a>
            </div>
        </div>
    </div>

    <!-- 🔄 Carousel Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
</div>




<!-- ✅ Filters Section -->
<div class="container my-4">
    <div class="filters-container">
        <input type="text" id="search" class="form-control" placeholder="🔎 Search by Name">
        <select id="categoryFilter" class="form-select">
            <option value="">📂 All Categories</option>
            <option value="Laptop">💻 Laptop</option>
            <option value="Desktop">🖥️ Desktop</option>
            <option value="Smartphone">📱 Smartphone</option>
            <option value="CCTV Camera">📷 CCTV Camera</option>
            <option value="Biometric">🔐 Biometric</option>
            <option value="Printer">🖨️ Printer</option>
        </select>
        <select id="priceFilter" class="form-select">
            <option value="">💰 Price Range</option>
            <option value="below_10000">Below ₹10,000</option>
            <option value="10000_30000">₹10,000 - ₹30,000</option>
            <option value="30000_50000">₹30,000 - ₹50,000</option>
            <option value="above_50000">Above ₹50,000</option>
        </select>
        <button id="resetFilters" class="btn btn-danger">🔄 Reset</button>
    </div>
</div>

<!-- ✅ Device Cards -->
<div class="container">
    <div class="row" id="deviceContainer"></div>
</div>

<!-- ✅ Pagination -->
<div class="container text-center my-4">
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center" id="paginationContainer"></ul>
    </nav>
</div>


<!-- ✅ Description Modal -->
<div class="modal fade" id="descriptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Device Description</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="deviceDescription"></div>
        </div>
    </div>
</div>

<!-- ✅ Footer -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <!-- 🌟 Company Info -->
            <div class="col-md-4">
                <img src="/public/logo.png" alt="MyStore Logo" class="footer-logo" width="150">
                <p>Providing top-quality refurbished devices with warranty and support.</p>
            </div>

            <!-- 🔗 Quick Links -->
            <div class="col-md-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="/pages/about-page/about.html">About Us</a></li>
                    <li><a href="#deviceContainer">Products</a></li>
                    <li><a href="/pages/contact-page/contact-page.html">Contact</a></li>
                </ul>
            </div>

            <!-- 📞 Contact Details -->
            <div class="col-md-4">
                <h5>Contact Us</h5>
                <p>Spaze i-Tech Park, Gurugram, India</p>
                <p>+91 9818559036</p>
                <p>info@sortoutinnovation.com</p>
            </div>
        </div>

        <hr>

        <!-- ⚖️ Copyright -->
        <div class="text-center">
            <p>&copy; <?php echo date("Y"); ?> Sortout Innovation Store. All rights reserved.</p>
        </div>
    </div>
</footer>



<!-- ✅ Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>




<script>
document.addEventListener("DOMContentLoaded", function () {
    const searchBox = document.getElementById("search-box");
    const suggestionsList = document.getElementById("search-suggestions");

    // ✅ Fetch Devices (Used for Search, Filters, and Pagination)
    function fetchDevices(filters = {}) {
        let url = new URL("fetch_devices.php", window.location.origin);

        Object.keys(filters).forEach(key => url.searchParams.append(key, filters[key]));

        fetch(url)
            .then(response => response.json())
            .then(data => {
                renderDevices(data.devices);
                renderPagination(data.totalPages, data.currentPage);
            })
            .catch(error => console.error("Error fetching devices:", error));
    }

    // ✅ Render Device Cards
    function renderDevices(devices) {
        let container = document.getElementById("deviceContainer");
        container.innerHTML = "";

        if (devices.length === 0) {
            container.innerHTML = "<p class='text-center text-muted'>No devices found.</p>";
            return;
        }

        devices.forEach(device => {
            let stockStatus = device.in_stock ? `<span class="out-of-stock">🔴 Out of Stock</span>` 
                                              : `<span class="in-stock">🟢 In Stock</span>`;

            let deviceHTML = `
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="device-card">
                        <img src="${device.image_url}" class="device-img" alt="${device.device_name}">
                        
                        <h5 class="device-title">${device.device_name}</h5>
                        
                        <p class="device-price text-danger fw-bold mt-2">₹${device.price}</p>
                        
                        <p class="device-stock-status">${stockStatus}</p>

                        <div class="device-footer">
                            <a href="https://wa.me/${device.contact_number}" target="_blank" class="btn btn-success w-100">📞 Contact</a>
                            <button class="btn description-btn mt-2" data-description="${device.description}">ℹ️ See Description</button>
                        </div>
                    </div>
                </div>
            `;
            container.innerHTML += deviceHTML;
        });

        attachDescriptionEvent();
    }

    // ✅ Pagination Logic
    function renderPagination(totalPages, currentPage) {
        let paginationContainer = document.querySelector(".pagination");
        paginationContainer.innerHTML = "";

        if (totalPages <= 1) return;

        paginationContainer.innerHTML += `
            <li class="page-item ${currentPage <= 1 ? 'disabled' : ''}">
                <a class="page-link pagination-btn" data-page="${currentPage - 1}">Previous</a>
            </li>
        `;

        for (let i = 1; i <= totalPages; i++) {
            paginationContainer.innerHTML += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link pagination-btn" data-page="${i}">${i}</a>
                </li>
            `;
        }

        paginationContainer.innerHTML += `
            <li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}">
                <a class="page-link pagination-btn" data-page="${currentPage + 1}">Next</a>
            </li>
        `;

        attachPaginationEvents();
    }

    // ✅ Handle Device Description Modal
    function attachDescriptionEvent() {
        document.querySelectorAll('.description-btn').forEach(button => {
            button.addEventListener('click', function () {
                document.getElementById('deviceDescription').innerText = this.getAttribute('data-description');
                new bootstrap.Modal(document.getElementById('descriptionModal')).show();
            });
        });
    }

    // ✅ Attach Pagination Events
    function attachPaginationEvents() {
        document.querySelectorAll(".pagination-btn").forEach(button => {
            button.addEventListener("click", function (event) {
                event.preventDefault();
                let page = this.getAttribute("data-page");
                fetchDevices({ page });
            });
        });
    }

    // ✅ Attach Search Functionality with Dropdown Suggestions
    searchBox.addEventListener("input", function () {
        let query = this.value.trim();

        if (query.length < 2) {
            suggestionsList.classList.add("d-none");
            return;
        }

        fetch(`search.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                suggestionsList.innerHTML = "";
                if (data.length === 0) {
                    suggestionsList.classList.add("d-none");
                    return;
                }

                data.forEach(device => {
                    let listItem = document.createElement("li");
                    listItem.className = "dropdown-item";
                    listItem.innerHTML = `<strong>${device.device_name}</strong>`;
                    listItem.addEventListener("click", () => {
                        searchBox.value = device.device_name;
                        suggestionsList.classList.add("d-none");

                        // ✅ Use `fetchDevices()` to filter results dynamically
                        fetchDevices({ search: device.device_name });
                    });
                    suggestionsList.appendChild(listItem);
                });

                suggestionsList.classList.remove("d-none");
            })
            .catch(error => console.error("Error fetching search results:", error));
    });

    // ✅ Hide Dropdown When Clicking Outside
    document.addEventListener("click", function (e) {
        if (!searchBox.contains(e.target) && !suggestionsList.contains(e.target)) {
            suggestionsList.classList.add("d-none");
        }
    });

    // ✅ Add Event Listeners for Filters
    document.getElementById('search').addEventListener('input', function () {
        fetchDevices({ search: this.value });
    });

    document.getElementById('categoryFilter').addEventListener('change', function () {
        fetchDevices({ category: this.value });
    });

    document.getElementById('priceFilter').addEventListener('change', function () {
        fetchDevices({ price_range: this.value });
    });

    document.getElementById('resetFilters').addEventListener('click', function () {
        fetchDevices({});
    });

    // ✅ Fetch Initial Devices on Page Load
    fetchDevices();
});
</script>


</body>
</html>
