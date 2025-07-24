<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sortout Innovation - Business Solutions</title>

  <!-- SEO Meta Description -->
  <meta name="description" content="Sortout Innovation provides top-tier solutions in Digital Marketing, IT, HR, Courier, Real Estate, Event Management, and more. Your success is our priority." />

  <!-- Open Graph Meta Tags (For Social Media and Google) -->
  <meta property="og:type" content="website" />
  <meta property="og:title" content="Sortout Innovation - Business Solutions" />
  <meta property="og:description" content="Your success is our priority with top-tier business solutions across multiple industries." />
  <meta property="og:url" content="https://sortoutinnovation.com/" />
  <meta property="og:image" content="https://sortoutinnovation.com/logo.png" />
  <meta property="og:site_name" content="Sortout Innovation" />

  <!-- Twitter Card for Better Indexing -->
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Sortout Innovation - Business Solutions" />
  <meta name="twitter:description" content="Top-tier solutions in Digital Marketing, IT, HR, and more." />
  <meta name="twitter:image" content="https://sortoutinnovation.com/logo.png" />

  <!-- JSON-LD Structured Data for Google -->
  <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Organization",
      "name": "Sortout Innovation - Business Solutions",
      "url": "https://sortoutinnovation.com/",
      "logo": "https://sortoutinnovation.com/logo.png"
    }
  </script>

  <!-- Favicon & App Icons -->
  <link rel="icon" type="image/png" sizes="32x32" href="https://sortoutinnovation.com/favicon-32x32.png" />
  <link rel="icon" type="image/png" sizes="192x192" href="https://sortoutinnovation.com/favicon-192x192.png" />
  <link rel="apple-touch-icon" href="https://sortoutinnovation.com/apple-touch-icon.png" />

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" />

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;800&display=swap" rel="stylesheet" />

      <!-- Custom CSS -->
  <link rel="stylesheet" href="/CSS/styles.css?v=2.0" />


</head>

  <body>
    <?php 
    // Set current page for navbar active state
    $currentPage = 'home';
    
    // Include navbar
    include 'components/navbar/navbar.php'; 
    ?>

    <!-- Hero Section -->
    <section class="hero-section py-5 min-vh-100 d-flex align-items-center overflow-hidden position-relative bg-gradient">
        <div class="container">
            <div class="row align-items-center gy-5">
                <!-- Left Content -->
                <div class="col-lg-6 order-2 order-lg-1">
                    <div class="hero-content text-center text-lg-start">
                        <h1 class="display-3 fw-bold mb-3">
                            One <span class="text-primary px-3 py-1 rounded bg-primary bg-opacity-10" style="color: #d10000 !important; background-color: rgba(209, 0, 0, 0.1) !important;">Source,</span> 
                            <br class="d-none d-lg-block">
                            Infinite Solutions
                        </h1>
                        <p class="lead text-secondary mb-4 fw-normal">
                            At Sortout Innovation, we provide top-tier solutions in Digital Marketing, IT, HR, Courier, Real Estate, Event Management, and more. Your success is our priority.
                        </p>
                        <div class="d-flex gap-3 flex-column flex-sm-row justify-content-center justify-content-lg-start">
                            <a href="/pages/our-services-page/service.html" 
                               class="btn btn-lg btn-primary rounded-pill d-inline-flex align-items-center px-4 py-3">
                                <span>Explore Our Services</span>
                                <i class="fas fa-arrow-right ms-2 btn-icon"></i>
                            </a>
                            <a href="#about" 
                               class="btn btn-lg btn-outline-primary rounded-pill d-inline-flex align-items-center px-4 py-3">
                                <span>Get Started</span>
                                <i class="fas fa-chevron-right ms-2 btn-icon"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Right Image -->
                <div class="col-lg-6 order-1 order-lg-2">
                    <div class="hero-image text-center position-relative">
                        <img src="/images/update/home-page/hero-section/sortout-innovation-hero-image.webp" 
                             alt="Sortout Innovation Solutions" 
                             class="img-fluid floating-animation">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Banner Section Using Bootstrap -->
    <section class="banner-section">
      <div
        id="bannerCarousel"
        class="carousel slide"
        data-bs-ride="carousel"
        data-bs-interval="4000"
      >
        <!-- Indicators -->
        <div class="carousel-indicators">
          <button
            type="button"
            data-bs-target="#bannerCarousel"
            data-bs-slide-to="0"
            class="active"
            aria-current="true"
            aria-label="Slide 1"
          ></button>
          <button
            type="button"
            data-bs-target="#bannerCarousel"
            data-bs-slide-to="1"
            aria-label="Slide 2"
          ></button>
          <button
            type="button"
            data-bs-target="#bannerCarousel"
            data-bs-slide-to="2"
            aria-label="Slide 3"
          ></button>
          <button
            type="button"
            data-bs-target="#bannerCarousel"
            data-bs-slide-to="3"
            aria-label="Slide 4"
          ></button>
        </div>

        <!-- Carousel Items -->
        <div class="carousel-inner">
          <div class="carousel-item active">
            <img
              src="/images/banners/one.webp"
              class="d-block w-100"
              alt="Banner 1"
            />
          </div>
          <div class="carousel-item">
            <img
              src="/images/banners/two.webp"
              class="d-block w-100"
              alt="Banner 2"
            />
          </div>
          <div class="carousel-item">
            <img
              src="/images/banners/three.webp"
              class="d-block w-100"
              alt="Banner 3"
            />
          </div>
          <div class="carousel-item">
            <img
              src="/images/banners/four.webp"
              class="d-block w-100"
              alt="Banner 4"
            />
          </div>
        </div>

        <!-- Navigation Buttons -->
        <button
          class="carousel-control-prev"
          type="button"
          data-bs-target="#bannerCarousel"
          data-bs-slide="prev"
        >
          <span class="carousel-control-prev-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Previous</span>
        </button>
        <button
          class="carousel-control-next"
          type="button"
          data-bs-target="#bannerCarousel"
          data-bs-slide="next"
        >
          <span class="carousel-control-next-icon" aria-hidden="true"></span>
          <span class="visually-hidden">Next</span>
        </button>
      </div>
    </section>

    <script>
      // Check if elements exist before using them
      const slider = document.querySelector(".banner-slider");
      const slides = document.querySelectorAll(".banner-slide");
      const prevBtn = document.querySelector(".prev-btn");
      const nextBtn = document.querySelector(".next-btn");

      // Only run slider code if elements exist
      if (slider && slides.length > 0 && prevBtn && nextBtn) {
        let currentIndex = 0;
        const totalSlides = slides.length;
        const slideDuration = 4000; // 4 seconds

        // Function to Update Slide Position Smoothly
        function updateSlide() {
          if (slider) {
            slider.style.transition = "transform 1s ease-in-out";
            slider.style.transform = `translateX(-${currentIndex * 100}%)`;
          }
        }

        // Move to Next Slide
        function nextSlide() {
          if (currentIndex >= totalSlides - 1) {
            currentIndex = 0; // Reset to first slide
          } else {
            currentIndex++;
          }
          updateSlide();
        }

        // Move to Previous Slide
        function prevSlide() {
          if (currentIndex <= 0) {
            currentIndex = totalSlides - 1; // Go to last slide
          } else {
            currentIndex--;
          }
          updateSlide();
        }

        // Auto-Slide Every 4 Seconds
        let autoSlide = setInterval(nextSlide, slideDuration);

        // Restart Auto-Slide When Clicking Buttons
        function resetAutoSlide() {
          clearInterval(autoSlide);
          autoSlide = setInterval(nextSlide, slideDuration);
        }

        // Next Button Click
        if (nextBtn) {
          nextBtn.addEventListener("click", () => {
            nextSlide();
            resetAutoSlide();
          });
        }

        // Previous Button Click
        if (prevBtn) {
          prevBtn.addEventListener("click", () => {
            prevSlide();
            resetAutoSlide();
          });
        }

        // Initial Setup
        updateSlide();
      }
    </script>

    <!-- Our Partners Brands Section -->
    <?php include 'components/partners/partners.php'; ?>

    <!-- About Section -->
    <section id="about" class="py-5 bg-light">
      <div class="container">
        <div class="row align-items-center gy-4">
          <!-- Left Side: Image -->
          <div class="col-lg-6" data-aos="fade-right">
            <div class="position-relative overflow-hidden rounded-4 shadow-lg">
              <img
                src="/images/services-imgs/CA/about-us.jpg"
                alt="About Sortout Innovation"
                class="img-fluid w-100 rounded-4"
                loading="lazy"
              />
            </div>
          </div>

          <!-- Right Side: Content -->
          <div class="col-lg-6" data-aos="fade-left">
            <div class="ps-lg-4">
              <h2 class="fw-bold text-danger mb-2">
                Empowering Businesses with Innovation & Excellence
              </h2>
              <p class="lead text-secondary mb-4">
                Sortout Innovation is your trusted partner in delivering
                tailored solutions across multiple industries. From IT to
                digital marketing, HR to logistics, we help businesses thrive in
                a competitive world.
              </p>

              <!-- Stats Section -->
              <div class="row g-4 mt-2">
                <div class="col-sm-4">
                  <div class="p-4 bg-white rounded-4 shadow-sm text-center h-100 border-start border-5 border-primary hover-lift" style="border-left-color: #d10000 !important;">
                    <h3 class="display-6 fw-bold text-primary mb-2" style="color: #d10000 !important;">10+</h3>
                    <p class="text-secondary mb-0 fw-medium">Years of Experience</p>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="p-4 bg-white rounded-4 shadow-sm text-center h-100 border-start border-5 border-primary hover-lift" style="border-left-color: #d10000 !important;">
                    <h3 class="display-6 fw-bold text-primary mb-2" style="color: #d10000 !important;">500+</h3>
                    <p class="text-secondary mb-0 fw-medium">Successful Projects</p>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="p-4 bg-white rounded-4 shadow-sm text-center h-100 border-start border-5 border-primary hover-lift" style="border-left-color: #d10000 !important;">
                    <h3 class="display-6 fw-bold text-primary mb-2" style="color: #d10000 !important;">300+</h3>
                    <p class="text-secondary mb-0 fw-medium">Trusted Clients</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    
    <section class="industries-section py-5">
      <div class="container text-center">
        <div class="section-title">
          <h2>Industries We Serve</h2>
          <p>
            Empowering businesses across multiple industries with tailored
            solutions.
          </p>
        </div>

        <div class="industries-grid">
          <!-- Industry 1 -->
          <div class="industry-box">
            <div class="icon"><i class="fas fa-building"></i></div>
            <h4>Corporate & Business</h4>
          </div>

          <!-- Industry 2 -->
          <div class="industry-box">
            <div class="icon"><i class="fas fa-shopping-cart"></i></div>
            <h4>Retail & E-Commerce</h4>
          </div>

          <!-- Industry 3 -->
          <div class="industry-box">
            <div class="icon"><i class="fas fa-stethoscope"></i></div>
            <h4>Healthcare & Pharmaceuticals</h4>
          </div>

          <!-- Industry 4 -->
          <div class="industry-box">
            <div class="icon"><i class="fas fa-truck"></i></div>
            <h4>Manufacturing & Logistics</h4>
          </div>

          <!-- Industry 5 -->
          <div class="industry-box">
            <div class="icon"><i class="fas fa-chart-line"></i></div>
            <h4>Finance & Banking</h4>
          </div>

          <!-- Industry 6 -->
          <div class="industry-box">
            <div class="icon"><i class="fas fa-hotel"></i></div>
            <h4>Hospitality & Tourism</h4>
          </div>
        </div>
      </div>
    </section>

    <section class="featured-services">
      <div class="container">
        <!-- Section Title -->
        <div class="section-heading text-center">
          <h2 class="services-title">Explore Our Expert Solutions</h2>
          <p class="services-subtitle">
            We provide a wide range of innovative services to help your business
            thrive.
          </p>
        </div>

        <!-- Services Grid -->
        <div class="row">
          <!-- Service 5 -->
          <div class="col-lg-3 col-md-6 mb-4">
            <div class="service-card">
              <i class="fas fa-calendar-alt service-icon"></i>
              <h4 class="service-title">Event Management</h4>
              <p class="service-description">
                Plans and executes seamless, memorable, and impactful events.
              </p>
              <a
                href="/pages/services/event-managementServices.html"
                class="service-btn"
                >View Details</a
              >
            </div>
          </div>

          <!-- Service 6 -->
          <div class="col-lg-3 col-md-6 mb-4">
            <div class="service-card">
              <i class="fas fa-paint-brush service-icon"></i>
              <h4 class="service-title">Design & Creative Services</h4>
              <p class="service-description">
                Custom branding and graphic solutions.
              </p>
              <a
                href="/pages/services/designAndCreative.html"
                class="service-btn"
                >View Details</a
              >
            </div>
          </div>

          <!-- Service 7 -->
          <div class="col-lg-3 col-md-6 mb-4">
            <div class="service-card">
              <i class="fas fa-code service-icon"></i>
              <h4 class="service-title">Web & App Development</h4>
              <p class="service-description">
                Building modern, responsive websites & apps.
              </p>
              <a
                href="/pages/services/designAndCreative.html"
                class="service-btn"
                >View Details</a
              >
            </div>
          </div>

          <!-- Service 8 -->
          <div class="col-lg-3 col-md-6 mb-4">
            <div class="service-card">
              <i class="fas fa-users service-icon"></i>
              <h4 class="service-title">Find Talent</h4>
              <p class="service-description">
                Helps businesses hire the right, skilled professionals for
                growth and success.
              </p>
              <a href="/pages/talent.page/talent.html" class="service-btn"
                >View Details</a
              >
            </div>
          </div>

          <!-- Service 1 -->
          <div class="col-lg-3 col-md-6 mb-4">
            <div class="service-card">
              <i class="fas fa-shipping-fast service-icon"></i>
              <h4 class="service-title">Courier Services</h4>
              <p class="service-description">
                Fast, reliable, secure, and highly efficient delivery of
                important packages.
              </p>
              <a href="/pages/services/courierServices.html" class="service-btn"
                >View Details</a
              >
            </div>
          </div>

          <!-- Service 2 -->
          <div class="col-lg-3 col-md-6 mb-4">
            <div class="service-card">
              <i class="fas fa-box-open service-icon"></i>
              <h4 class="service-title">Shipping & Fulfillment</h4>
              <p class="service-description">
                Seamless global logistics and inventory management.
              </p>
              <a href="/pages/services/shipping.html" class="service-btn"
                >View Details</a
              >
            </div>
          </div>

          <!-- Service 3 -->
          <div class="col-lg-3 col-md-6 mb-4">
            <div class="service-card">
              <i class="fas fa-pencil-ruler service-icon"></i>
              <h4 class="service-title">Stationery Services</h4>
              <p class="service-description">
                Supplies essential office materials for smooth and efficient
                daily operations.
              </p>
              <a
                href="/pages/services/stationeryServices.html"
                class="service-btn"
                >View Details</a
              >
            </div>
          </div>

          <!-- Service 4 -->
          <div class="col-lg-3 col-md-6 mb-4">
            <div class="service-card">
              <i class="fas fa-building service-icon"></i>
              <h4 class="service-title">Real Estate & Property</h4>
              <p class="service-description">
                Find and invest in commercial properties.
              </p>
              <a
                href="/pages/services/propertyServices.html"
                class="service-btn"
                >View Details</a
              >
            </div>
          </div>
        </div>

        <!-- View More Button -->
        <div class="text-center mt-4">
          <a
            href="/pages/our-services-page/service.html"
            class="view-more-services-btn"
            >View More Services</a
          >
        </div>
      </div>
    </section>

    <section class="brand-potential-section">
      <div class="container">
        <h2 class="brand-heading">
          Unleashing the Vital Potential of <span>Your Brand</span>
        </h2>
        <p class="brand-subheading">
          Empowering businesses with
          <strong>innovation, creativity, and technology</strong> to fuel
          unstoppable growth.
        </p>

        <!-- Updated Grid Layout -->
        <div class="brand-grid">
          <!-- Floating Service Cards -->
          <div class="floating-card">
            <i class="fas fa-lightbulb"></i>
            <h3>Strategic Branding</h3>
            <p>
              We craft compelling brand identities that leave lasting
              impressions.
            </p>
          </div>

          <div class="floating-card">
            <i class="fas fa-code"></i>
            <h3>Digital Innovation</h3>
            <p>
              Harnessing the latest technology to optimize digital experiences.
            </p>
          </div>

          <!-- Center Glowing Core -->
          <div class="glowing-core">
            <div class="pulse-effect"></div>
          </div>

          <div class="floating-card">
            <i class="fas fa-paint-brush"></i>
            <h3>Creative Excellence</h3>
            <p>Designing visually stunning and impactful creatives.</p>
          </div>

          <div class="floating-card">
            <i class="fas fa-chart-line"></i>
            <h3>Market Leadership</h3>
            <p>
              Helping businesses establish authority and dominate their
              industry.
            </p>
          </div>
        </div>
      </div>
    </section>

    <section class="who-we-serve">
      <div class="container-fluid">
        <div class="row align-items-center">
          <!-- Left Side (White Background) -->
          <div class="col-lg-6 left-content">
            <h2>Who Do We Serve?</h2>
            <p class="subheading">
              Empowering businesses, organizations, and individuals with
              cutting-edge digital solutions.
            </p>
          </div>

          <!-- Right Side (Red Background) -->
          <div class="col-lg-6 right-content">
            <div class="service-category">
              <i class="fas fa-bullhorn"></i>
              <h4>Brand Branding</h4>
              <p>Elevate your brand identity with strategic positioning.</p>
            </div>
            <div class="service-category">
              <i class="fas fa-building"></i>
              <h4>Organizations & Digital Media</h4>
              <p>Advanced digital transformation solutions for enterprises.</p>
            </div>
            <div class="service-category">
              <i class="fas fa-user-edit"></i>
              <h4>Individuals & Digital Publishing</h4>
              <p>
                Empowering freelancers, content creators, and professionals.
              </p>
            </div>
            <div class="service-category">
              <i class="fas fa-globe"></i>
              <h4>Where Do We Serve?</h4>
              <p>Global reach, delivering excellence across industries.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- <section class="testimonials py-5">
      <div class="container">
        <div class="section-title text-center mb-4">
          <h2>What Our Clients Say</h2>
          <p>Trusted by businesses and professionals worldwide.</p>
        </div>

        <div
          id="testimonialCarousel"
          class="carousel slide"
          data-bs-ride="carousel"
        >
          <div class="carousel-inner">
            <div class="carousel-item active">
              <div class="testimonial-box">
                <div class="testimonial-content">
                  <p>
                    "SortOut Innovation transformed our brand. Their expertise
                    and dedication are unmatched!"
                  </p>
                  <h4>Lisa T.</h4>
                  <span>CEO, Retail Ventures</span>
                </div>
              </div>
            </div>

            <div class="carousel-item">
              <div class="testimonial-box">
                <div class="testimonial-content">
                  <p>
                    "Their digital solutions have significantly boosted our
                    engagement and conversions!"
                  </p>
                  <h4>John M.</h4>
                  <span>Marketing Head, E-Commerce Co.</span>
                </div>
              </div>
            </div>

            <div class="carousel-item">
              <div class="testimonial-box">
                <div class="testimonial-content">
                  <p>
                    "From branding to website design, they exceeded our
                    expectations at every step."
                  </p>
                  <h4>Sarah L.</h4>
                  <span>Product Manager</span>
                </div>
              </div>
            </div>
          </div>

          <button
            class="carousel-control-prev"
            type="button"
            data-bs-target="#testimonialCarousel"
            data-bs-slide="prev"
          >
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button
            class="carousel-control-next"
            type="button"
            data-bs-target="#testimonialCarousel"
            data-bs-slide="next"
          >
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>

        <div class="carousel-indicators mt-3">
          <button
            type="button"
            data-bs-target="#testimonialCarousel"
            data-bs-slide-to="0"
            class="active"
          ></button>
          <button
            type="button"
            data-bs-target="#testimonialCarousel"
            data-bs-slide-to="1"
          ></button>
          <button
            type="button"
            data-bs-target="#testimonialCarousel"
            data-bs-slide-to="2"
          ></button>
        </div>
      </div>
    </section> -->

    <section class="our-process">
      <div class="container">
        <div class="section-title text-center">
          <h2>Our Process</h2>
          <p>
            We follow a structured, results-driven approach to deliver
            excellence.
          </p>
        </div>

        <div class="process-flow">
          <!-- Step 1 -->
          <div class="process-row">
            <div class="process-icon"><i class="fas fa-comments"></i></div>
            <div class="process-content">
              <h4>Consultation</h4>
              <p>
                Understanding client goals & needs to create a tailored
                strategy.
              </p>
            </div>
          </div>

          <!-- Step 2 -->
          <div class="process-row right">
            <div class="process-icon"><i class="fas fa-lightbulb"></i></div>
            <div class="process-content">
              <h4>Strategy & Planning</h4>
              <p>
                Creating a roadmap that aligns with your business objectives.
              </p>
            </div>
          </div>

          <!-- Step 3 -->
          <div class="process-row">
            <div class="process-icon"><i class="fas fa-cogs"></i></div>
            <div class="process-content">
              <h4>Implementation</h4>
              <p>Bringing ideas to life with expert execution & precision.</p>
            </div>
          </div>

          <!-- Step 4 -->
          <div class="process-row right">
            <div class="process-icon"><i class="fas fa-check-circle"></i></div>
            <div class="process-content">
              <h4>Quality Assurance</h4>
              <p>Ensuring top-tier results through rigorous quality checks.</p>
            </div>
          </div>

          <!-- Step 5 -->
          <div class="process-row">
            <div class="process-icon"><i class="fas fa-handshake"></i></div>
            <div class="process-content">
              <h4>Delivery & Support</h4>
              <p>On-time completion with post-delivery support & assistance.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="cta" class="cta-section">
      <div class="container">
        <div class="cta-content">
          <h2>Take Your Business to the Next Level</h2>
          <p>
            Partner with Sortout Innovation for expert solutions tailored to
            your needs.
          </p>
          <div class="cta-buttons">
            <a
              href="/pages/our-services-page/service.html"
              class="btn-cta btn-primary-cta"
              >Explore Our Services</a
            >
            <a href="https://wa.me/919818559036" class="btn-cta btn-outline-cta"
              >Contact Us</a
            >
          </div>
        </div>
      </div>
    </section>

    <?php 
    // Include the footer component
    include 'components/footer/footer.php'; 
    ?>

    <!-- Bootstrap Accordion Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
      var myCarousel = document.querySelector("#testimonialCarousel");
      if (myCarousel && typeof bootstrap !== 'undefined') {
        var carousel = new bootstrap.Carousel(myCarousel, {
          interval: 5000, // Change slide every 5 seconds
          wrap: true,
        });
      }
    </script>

    <script>
      // Optional: Adds a glowing effect to the center element dynamically
      document.addEventListener("DOMContentLoaded", function () {
        const glowingCore = document.querySelector(".glowing-core");

        if (glowingCore) {
          setInterval(() => {
            glowingCore.style.boxShadow = `0 0 ${
              Math.random() * 50 + 30
            }px rgba(255, 75, 75, 0.8)`;
          }, 1000);
        }
      });
    </script>

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        let logoContainer = document.querySelector(".logos");
        if (logoContainer) {
          let clonedLogos = logoContainer.innerHTML; // Clone inner content

          // Append cloned logos only once to avoid duplicates
          logoContainer.innerHTML += clonedLogos;
        }
      });
    </script>

    <script>
      function toggleServices(id) {
        let serviceList = document.getElementById(id);
        if (serviceList.style.display === "block") {
          serviceList.style.display = "none";
        } else {
          serviceList.style.display = "block";
        }
      }
    </script>

    <!-- Force Red Colors Script - Fallback for Hostinger -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        // Force red color for all primary text elements
        const primaryTextElements = document.querySelectorAll('.text-primary');
        primaryTextElements.forEach(function(element) {
          element.style.color = '#d10000';
        });

        // Force red background for all primary background elements
        const primaryBgElements = document.querySelectorAll('.bg-primary');
        primaryBgElements.forEach(function(element) {
          element.style.backgroundColor = '#d10000';
        });

        // Force red border for all primary border elements
        const primaryBorderElements = document.querySelectorAll('.border-primary, .border-start-primary');
        primaryBorderElements.forEach(function(element) {
          element.style.borderColor = '#d10000';
        });

        // Force red for primary buttons
        const primaryButtons = document.querySelectorAll('.btn-primary');
        primaryButtons.forEach(function(element) {
          element.style.backgroundColor = '#d10000';
          element.style.borderColor = '#d10000';
          element.style.color = '#fff';
        });

        // Force red for outline primary buttons
        const outlinePrimaryButtons = document.querySelectorAll('.btn-outline-primary');
        outlinePrimaryButtons.forEach(function(element) {
          element.style.color = '#d10000';
          element.style.borderColor = '#d10000';
        });

        // Force red background with opacity
        const bgOpacityElements = document.querySelectorAll('.bg-primary.bg-opacity-10');
        bgOpacityElements.forEach(function(element) {
          element.style.backgroundColor = 'rgba(209, 0, 0, 0.1)';
        });

        console.log('Red color override script executed');
      });
    </script>
  </body>
</html>
