<!DOCTYPE html>
<?php $currentPage = 'services'; ?>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      rel="icon"
      type="image/png"
      href="/images/sortoutInnovation-icon/sortout-innovation-only-s.gif"
    />
    <link rel="icon" type="image/png" href="/images/sortoutInnovation-icon/sortout-innovation-only-s.gif" />
    <title>Services</title>

    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />

    <!-- Bootstrap JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Google Fonts -->
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap"
      rel="stylesheet"
    />

    <link rel="stylesheet" href="/pages/our-services-page/services.css" />
    <link rel="stylesheet" href="/CSS/floating-social-media.css" />
   
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
      rel="stylesheet"
    />
  </head>
  <body>
    <?php include '../../components/navbar/navbar.php'; ?>

    <!-- HERO SECTION -->
    <section class="hero-section">
      <div class="container">
        <div class="row align-items-center">
          <!-- Left Content -->
          <div class="col-lg-6 col-md-12">
            <h1 class="hero-title">
              Empowering Your <span>Business</span> <br />With Expert Services
            </h1>
            <p class="hero-subtitle">
              Your success is our priority. We deliver solutions tailored to
              your needs.
            </p>
            <div class="hero-buttons">
              <a href="#service" class="btn btn-primary">Explore Services</a>
              <!-- <a href="https://wa.me/919818559036" class="btn btn-outline-light"
                >Get in Touch</a
              > -->
            </div>
          </div>
          <!-- Right Image -->
          <div class="col-lg-6 col-md-12">
            <div class="hero-illustration">
              <img
                src="https://demo.bazatheme.com/wp-content/uploads/2021/09/vffvv.png"
                alt="Hero Illustration"
                class="hero-image"
              />
            </div>
          </div>
        </div>
      </div>
      <!-- Background Decorations -->
      <div class="hero-bg">
        <div class="circle-red"></div>
        <div class="circle-white"></div>
      </div>
    </section>

    <section id="about" class="who-we-are-section py-5">
      <div class="container">
        <div class="row align-items-center">
          <!-- Left: Content -->
          <div class="col-lg-6">
            <h2 class="section-heading">Who We Are</h2>
            <p class="section-content">
              At Sortout Innovation, we are committed to delivering innovative and
              customer-centric solutions that empower businesses to thrive. With
              over a decade of experience, our team combines creativity,
              technology, and strategy to achieve remarkable results for our
              clients.
            </p>
            <ul class="key-stats">
              <li><strong>10+ Years</strong> of Experience</li>
              <li><strong>500+</strong> Happy Clients</li>
              <li><strong>1,200+</strong> Successful Projects</li>
              <li><strong>98%</strong> Customer Satisfaction Rate</li>
            </ul>
          </div>

          <!-- Right: Image -->
          <div class="col-lg-6 text-center">
            <div class="image-wrapper">
              <img
                src="/images/services-imgs/CA/about-us-refactor.webp"
                alt="Who We Are"
                class="who-we-are-image"
              />
            </div>
            <!-- Stats Below Image -->
            <div class="image-stats">
              <div class="stat-item">
                <h3>10+</h3>
                <p>Years in Business</p>
              </div>
              <div class="stat-item">
                <h3>500+</h3>
                <p>Happy Clients</p>
              </div>
              <div class="stat-item">
                <h3>1,200+</h3>
                <p>Projects Delivered</p>
              </div>
              <div class="stat-item">
                <h3>98%</h3>
                <p>Satisfaction Rate</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="service" class="elevated-services-section py-5">
      <div class="container">
        <!-- Section Heading -->
        <div class="section-header text-center mb-5">
          <h2 class="section-title">
            Discover Our Full Range of <span>Services</span>
          </h2>
          <p class="section-description">
            From branding to advanced IT solutions, our services are tailored to
            elevate your business.
          </p>
        </div>

        <!-- Services Row -->
        <div class="row g-4">
          <!-- Service Card 1 -->
          <div class="col-lg-3 col-md-6">
            <div class="service-card">
              <div class="service-image-wrapper">
                <a href="/pages/services/socialMediaInfluencers.html">
                  <img
                    src="/images/services-imgs/Social_media.png"
                    alt="Digital Marketing Services"
                    class="service-image"
                  />
                </a>
                <div class="service-icon-overlay">
                  <i class="fas fa-share-alt"></i>
                </div>
              </div>
              <div class="service-content">
                <h3 class="service-title">Digital Marketing Services</h3>
                <p class="service-description">
                  Boost your brand's presence and engagement across platforms.
                </p>
                <a
                  href="/pages/services/social-media-page/social-media-page.html"
                  class="btn-view-more"
                  >View More</a
                >
              </div>
            </div>
          </div>

          <!-- Service Card 11 -->
          <div class="col-lg-3 col-md-6">
            <div class="service-card">
              <div class="service-image-wrapper">
                <a href="/pages/services/live-streaming/liveStreaming.html">
                  <img
                    src="/images/services-imgs/live-streaming-logo.webp"
                    alt="Web and App Development"
                    class="service-image"
                  />
                  <div class="service-icon-overlay">
                    <i class="fas fa-laptop-code"></i>
                  </div>
                </a>
              </div>
              <div class="service-content">
                <h3 class="service-title">Live Streaming Service</h3>
                <p class="service-description">
                  Develops websites and apps to meet business needs with
                  seamless functionality and modern design.
                </p>
                <a
                  href="/pages/services/live-streaming/liveStreaming.html"
                  class="btn-view-more"
                  >View More</a
                >
              </div>
            </div>
          </div>

          <!-- Service Card 12 -->
          <div class="col-lg-3 col-md-6">
            <div class="service-card">
              <div class="service-image-wrapper">
                <img
                  src="/images/services-imgs/infulener_s.png"
                  alt="Find Talent"
                  class="service-image"
                />
                <div class="service-icon-overlay">
                  <i class="fas fa-users"></i>
                </div>
              </div>
              <div class="service-content">
                <h3 class="service-title">Find Talent</h3>
                <p class="service-description">
                  Helps businesses hire the right, skilled professionals for
                  growth and success.
                </p>
                <a href="../../registration.php" class="btn-view-more"
                  >View More</a
                >
              </div>
            </div>
          </div>

          <!-- Service Card 2 -->
          <div class="col-lg-3 col-md-6">
            <div class="service-card">
              <div class="service-image-wrapper">
                <a href="/pages/services/itServices.html">
                  <img
                    src="/images/services-imgs/It_services.png"
                    alt="IT Support"
                    class="service-image"
                  />
                </a>
                <div class="service-icon-overlay">
                  <i class="fas fa-laptop-code"></i>
                </div>
              </div>
              <div class="service-content">
                <h3 class="service-title">Information Technology Support</h3>
                <p class="service-description">
                  Reliable tech support to keep your business running smoothly.
                </p>
                <a href="/pages/services/itServices.html" class="btn-view-more"
                  >View More</a
                >
              </div>
            </div>
          </div>

          <!-- Service Card 3 -->
          <div class="col-lg-3 col-md-6">
            <div class="service-card">
              <div class="service-image-wrapper">
                <a href="/pages/services/caServices.html">
                  <img
                    src="/images/services-imgs/CA_s.png"
                    alt="CA Services"
                    alt="CA Services"
                    class="service-image"
                  />
                </a>
                <div class="service-icon-overlay">
                  <i class="fas fa-calculator"></i>
                </div>
              </div>
              <div class="service-content">
                <h3 class="service-title">Chartered Accountant Services</h3>
                <p class="service-description">
                  Expert financial consulting to manage taxes and finances.
                </p>
                <a href="/pages/services/caServices.html" class="btn-view-more"
                  >View More</a
                >
              </div>
            </div>
          </div>

          <!-- Service Card 4 -->
          <div class="col-lg-3 col-md-6">
            <div class="service-card">
              <div class="service-image-wrapper">
                <a href="/pages/services/hrServices.html">
                  <img
                    src="/images/services-imgs/HR_Solutions S.png"
                    alt="HR Solutions"
                    class="service-image"
                  />
                </a>
                <div class="service-icon-overlay">
                  <i class="fas fa-users"></i>
                </div>
              </div>
              <div class="service-content">
                <h3 class="service-title">Human Resources Solutions</h3>
                <p class="service-description">
                  Simplify employee recruitment and management.
                </p>
                <a href="/pages/services/hrServices.html" class="btn-view-more"
                  >View More</a
                >
              </div>
            </div>
          </div>

          <!-- Service Card 5 -->
          <div class="col-lg-3 col-md-6">
            <div class="service-card">
              <div class="service-image-wrapper">
                <a href="/pages/services/courierServices.html">
                  <img
                    src="/images/services-imgs/courier_services.png"
                    alt="Courier Services"
                    class="service-image"
                  />
                </a>
                <div class="service-icon-overlay">
                  <i class="fas fa-truck"></i>
                </div>
              </div>
              <div class="service-content">
                <h3 class="service-title">Courier Services</h3>
                <p class="service-description">
                  Fast, reliable, secure, and highly efficient delivery of
                  important packages.
                </p>
                <a
                  href="/pages/services/courierServices.html"
                  class="btn-view-more"
                  >View More</a
                >
              </div>
            </div>
          </div>

          <!-- Service Card 6 -->
          <div class="col-lg-3 col-md-6">
            <div class="service-card">
              <div class="service-image-wrapper">
                <a href="/pages/services/shipping.html">
                  <img
                    src="/images/services-imgs/shipping_s.png"
                    alt="Shipping and Fulfillment"
                    class="service-image"
                  />
                </a>
                <div class="service-icon-overlay">
                  <i class="fas fa-box"></i>
                </div>
              </div>
              <div class="service-content">
                <h3 class="service-title">Shipping and Fulfillment Services</h3>
                <p class="service-description">
                  Handles packing and shipping of products to customers.
                </p>
                <a href="/pages/services/shipping.html" class="btn-view-more"
                  >View More</a
                >
              </div>
            </div>
          </div>

          <!-- Service Card 7 -->
          <div class="col-lg-3 col-md-6">
            <div class="service-card">
              <div class="service-image-wrapper">
                <a href="/pages/services/stationeryServices.html">
                  <img
                    src="/images/services-imgs/stationery_services.webp"
                    alt="Stationary Services"
                    class="service-image"
                  />
                </a>
                <div class="service-icon-overlay">
                  <i class="fas fa-pencil-alt"></i>
                </div>
              </div>
              <div class="service-content">
                <h3 class="service-title">Stationery Services</h3>
                <p class="service-description">
                  Supplies essential office materials for smooth and efficient
                  daily operations.
                </p>
                <a
                  href="/pages/services/stationeryServices.html"
                  class="btn-view-more"
                  >View More</a
                >
              </div>
            </div>
          </div>

          <!-- Service Card 8 -->
          <div class="col-lg-3 col-md-6">
            <div class="service-card">
              <div class="service-image-wrapper">
                <a href="/pages/services/propertyServices.html">
                  <img
                    src="/images/services-imgs/property_services.png"
                    alt="Real Estate and Property"
                    class="service-image"
                  />
                </a>
                <div class="service-icon-overlay">
                  <i class="fas fa-building"></i>
                </div>
              </div>
              <div class="service-content">
                <h3 class="service-title">Real Estate and Property Services</h3>
                <p class="service-description">
                  Assists with buying, selling, and managing properties.
                </p>
                <a
                  href="/pages/services/propertyServices.html"
                  class="btn-view-more"
                  >View More</a
                >
              </div>
            </div>
          </div>

          <!-- Service Card 9 -->
          <div class="col-lg-3 col-md-6">
            <div class="service-card">
              <div class="service-image-wrapper">
                <a href="/pages/services/event-managementServices.html">
                  <img
                    src="/images/services-imgs/Event_services.png"
                    alt="Event Management"
                    class="service-image"
                  />
                </a>
                <div class="service-icon-overlay">
                  <i class="fas fa-calendar-alt"></i>
                </div>
              </div>
              <div class="service-content">
                <h3 class="service-title">Event Management Services</h3>
                <p class="service-description">
                  Plans and executes seamless, memorable, and impactful events.
                </p>
                <a
                  href="/pages/services/event-managementServices.html"
                  class="btn-view-more"
                  >View More</a
                >
              </div>
            </div>
          </div>

          <!-- Service Card 10 -->
          <div class="col-lg-3 col-md-6">
            <div class="service-card">
              <div class="service-image-wrapper">
                <a href="/pages/services/designAndCreative.html">
                  <img
                    src="/images/services-imgs/design_S.png"
                    alt="Design and Creative"
                    class="service-image"
                  />
                </a>
                <div class="service-icon-overlay">
                  <i class="fas fa-paint-brush"></i>
                </div>
              </div>
              <div class="service-content">
                <h3 class="service-title">Design and Creative Services</h3>
                <p class="service-description">
                  Offers creative solutions for branding and design.
                </p>
                <a
                  href="/pages/services/designAndCreative.html"
                  class="btn-view-more"
                  >View More</a
                >
              </div>
            </div>
          </div>
          <!-- Service Card 13 -->
          <div class="col-lg-3 col-md-6">
            <div class="service-card">
              <div class="service-image-wrapper">
                <a
                  href="/pages/services/corporate_insurance_services/corporate_insurance_services.html"
                >
                  <img
                    src="/images/services-imgs/insurance_services.webp"
                    alt="Design and Creative"
                    class="service-image"
                  />
                </a>
                <div class="service-icon-overlay">
                  <i class="fas fa-paint-brush"></i>
                </div>
              </div>
              <div class="service-content">
                <h3 class="service-title">Corporate Insurance Services</h3>
                <p class="service-description">
                  Protect Your Business with Comprehensive Corporate Insurance
                </p>
                <a
                  href="/pages/services/corporate_insurance_services/corporate_insurance_services.html"
                  class="btn-view-more"
                  >View More</a
                >
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="why-choose-us">
      <div class="container">
        <h2 class="section-heading">Why Choose Us?</h2>
        <h3 class="section-subheading">
          Empowering Businesses with Innovation & Quality
        </h3>
        <p class="section-description">
          We don't just offer services – we create solutions that drive success.
          Discover why businesses trust us for their digital and operational
          needs.
        </p>

        <div class="timeline">
          <!-- Step 1 -->
          <div class="timeline-item">
            <div class="timeline-icon"><i class="fas fa-award"></i></div>
            <div class="timeline-content">
              <h4>Years of Proven Experience</h4>
              <p>
                Decades of industry expertise delivering top-notch solutions.
              </p>
            </div>
          </div>

          <!-- Step 2 -->
          <div class="timeline-item">
            <div class="timeline-icon"><i class="fas fa-cubes"></i></div>
            <div class="timeline-content">
              <h4>Comprehensive Service Range</h4>
              <p>From design to logistics, we cover all business needs.</p>
            </div>
          </div>

          <!-- Step 3 -->
          <div class="timeline-item">
            <div class="timeline-icon"><i class="fas fa-rocket"></i></div>
            <div class="timeline-content">
              <h4>Cutting-Edge Technology</h4>
              <p>Stay ahead with AI-driven innovation and automation.</p>
            </div>
          </div>

          <!-- Step 4 -->
          <div class="timeline-item">
            <div class="timeline-icon"><i class="fas fa-headset"></i></div>
            <div class="timeline-content">
              <h4>Dedicated Support Team</h4>
              <p>24/7 assistance to ensure smooth operations.</p>
            </div>
          </div>

          <!-- Step 5 -->
          <div class="timeline-item">
            <div class="timeline-icon"><i class="fas fa-user-cog"></i></div>
            <div class="timeline-content">
              <h4>Custom-Tailored Solutions</h4>
              <p>We shape our services to fit your unique goals and needs.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="process-section">
      <div class="container">
        <h2 class="section-title">Our Streamlined Process</h2>
        <p class="section-subtitle">
          From idea to execution, we ensure seamless service every step of the
          way.
        </p>

        <div class="process-steps">
          <!-- Step 1 -->
          <div class="process-item">
            <div class="step-number">01</div>
            <div class="step-icon">
              <i class="fas fa-comments"></i>
            </div>
            <div class="step-content">
              <h3>Understanding Your Needs</h3>
              <p>
                We begin by analyzing your requirements and crafting a tailored
                strategy.
              </p>
            </div>
          </div>

          <!-- Step 2 -->
          <div class="process-item">
            <div class="step-number">02</div>
            <div class="step-icon">
              <i class="fas fa-lightbulb"></i>
            </div>
            <div class="step-content">
              <h3>Strategic Planning</h3>
              <p>
                Our team develops a structured plan, ensuring alignment with
                your vision.
              </p>
            </div>
          </div>

          <!-- Step 3 -->
          <div class="process-item">
            <div class="step-number">03</div>
            <div class="step-icon">
              <i class="fas fa-cogs"></i>
            </div>
            <div class="step-content">
              <h3>Execution & Implementation</h3>
              <p>
                We bring the plan to life using innovative solutions and precise
                execution.
              </p>
            </div>
          </div>

          <!-- Step 4 -->
          <div class="process-item">
            <div class="step-number">04</div>
            <div class="step-icon">
              <i class="fas fa-chart-line"></i>
            </div>
            <div class="step-content">
              <h3>Optimization & Review</h3>
              <p>
                We continuously analyze performance and optimize for better
                results.
              </p>
            </div>
          </div>

          <!-- Step 5 -->
          <div class="process-item">
            <div class="step-number">05</div>
            <div class="step-icon">
              <i class="fas fa-check-circle"></i>
            </div>
            <div class="step-content">
              <h3>Final Delivery & Support</h3>
              <p>
                We ensure a seamless delivery and provide post-service support.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="testimonials-section">
      <div class="container">
        <h2 class="testimonials-title">What Our Clients Say</h2>
        <p class="testimonials-subtitle">
          Hear from our satisfied clients about their experience with us.
        </p>

        <div class="testimonials-slider">
          <!-- Testimonial 1 -->
          <div class="testimonial-item active">
            <p class="testimonial-text">
              "They created a stunning website and branding for our business.
              The results were beyond our expectations!"
            </p>
            <h4 class="testimonial-author">— Sunny, CEO, Josh</h4>
          </div>

          <!-- Testimonial 2 -->
          <div class="testimonial-item">
            <p class="testimonial-text">
              "Their designs helped us stand out in a competitive market. Highly
              recommended!"
            </p>
            <h4 class="testimonial-author">
              — Ali, Marketing Head, E-Commerce Co.
            </h4>
          </div>

          <!-- Testimonial 3 -->
          <div class="testimonial-item">
            <p class="testimonial-text">
              "From packaging to animations, they deliver excellence every
              single time."
            </p>
            x
            <h4 class="testimonial-author">— Ajay, Product Manager</h4>
          </div>
        </div>
      </div>
    </section>

    <section class="faq-section">
      <div class="container">
        <h2 class="faq-title">Frequently Asked Questions</h2>
        <p class="faq-subtitle">
          Find answers to common queries about our services.
        </p>

        <div class="faq-container">
          <!-- FAQ Item 1 -->
          <div class="faq-item">
            <div class="faq-question">
              <h4>What types of design services do you offer?</h4>
              <i class="fas fa-plus toggle-icon"></i>
            </div>
            <div class="faq-answer">
              <p>
                We provide a wide range of services, including branding, graphic
                design, web design, packaging, motion graphics, and more.
              </p>
            </div>
          </div>

          <!-- FAQ Item 2 -->
          <div class="faq-item">
            <div class="faq-question">
              <h4>Do you offer custom solutions?</h4>
              <i class="fas fa-plus toggle-icon"></i>
            </div>
            <div class="faq-answer">
              <p>
                Yes, every service is tailored to your unique needs and brand
                identity.
              </p>
            </div>
          </div>

          <!-- FAQ Item 3 -->
          <div class="faq-item">
            <div class="faq-question">
              <h4>What is your typical turnaround time?</h4>
              <i class="fas fa-plus toggle-icon"></i>
            </div>
            <div class="faq-answer">
              <p>
                Our timelines depend on the project scope but are always
                delivered on time with uncompromising quality.
              </p>
            </div>
          </div>

          <!-- FAQ Item 4 -->
          <div class="faq-item">
            <div class="faq-question">
              <h4>Can I request revisions?</h4>
              <i class="fas fa-plus toggle-icon"></i>
            </div>
            <div class="faq-answer">
              <p>
                Absolutely! We offer revisions to ensure the final product
                exceeds your expectations.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="cta" class="cta-section">
      <div class="container">
        <div class="cta-content">
          <h2 class="cta-title">Ready to Bring Your Vision to Life?</h2>
          <p class="cta-text">
            Let's create designs that set your brand apart. Connect with us
            today to start your creative journey.
          </p>
          <div class="cta-buttons">
            <a href="#service" class="btn btn-primary">Get Started Today</a>
            <a
              href="https://wa.me/919818559036"
              target="_blank"
              rel="noopener noreferrer"
              class="btn btn-outline"
              >Contact Us</a
            >
          </div>
        </div>
      </div>
    </section>

    <script>
      document.querySelectorAll(".faq-item").forEach((item) => {
        item.addEventListener("click", () => {
          item.classList.toggle("active");
        });
      });
    </script>

    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const features = document.querySelectorAll(".key-features li");

        window.addEventListener("scroll", () => {
          let triggerBottom = window.innerHeight * 0.85;

          features.forEach((feature) => {
            let featureTop = feature.getBoundingClientRect().top;
            if (featureTop < triggerBottom) {
              feature.classList.add("fade-in");
            }
          });
        });
      });
    </script>

    <?php include '../../components/footer/footer.php'; ?>

  </body>
</html>


