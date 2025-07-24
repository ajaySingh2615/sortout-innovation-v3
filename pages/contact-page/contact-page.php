<!DOCTYPE html>
<?php $currentPage = 'contact'; ?>
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
    <title>Contact Us - Sortout Innovation</title>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    />
    <!-- Font Awesome Icons -->
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="/pages/contact-page/contact-page.css" />
    <link rel="stylesheet" href="/CSS/floating-social-media.css" />
    <!-- External CSS -->
    <script
      src="https://kit.fontawesome.com/a076d05399.js"
      crossorigin="anonymous"
    ></script>
    <!-- FontAwesome Icons -->
  </head>
  <body>
    <?php include '../../components/navbar/navbar.php'; ?>

    <!-- Hero Section -->
    <section class="hero-section d-flex align-items-center">
      <div class="container text-center">
        <h1 class="hero-title">
          <span>Sortout Innovation</span> - Let's Connect
        </h1>
        <p class="hero-subtitle">
          We empower businesses with innovative solutions. Contact us and let's
          create something impactful together.
        </p>
        <a
          href="https://wa.me/919818559036"
          target="_blank"
          rel="noopener noreferrer"
          class="btn btn-custom"
        >
          <i class="fas fa-phone-alt"></i> Get in Touch
        </a>
      </div>
    </section>

    <!-- Contact Information Section -->
    <section class="contact-info-section">
      <div class="container">
        <div class="section-title text-center">
          <h2>Contact <span>Us</span></h2>
          <p>Reach out to us for any inquiries. We're here to help!</p>
        </div>

        <div class="row justify-content-center">
          <!-- Address -->
          <div class="col-md-4">
            <a
              href="https://maps.app.goo.gl/wy615ztUayBxidr77"
              target="_blank"
              class="contact-card"
            >
              <div class="icon-box">
                <i class="fas fa-map-marker-alt contact-icon"></i>
              </div>
              <h4>Visit Our Office</h4>
              <p>Spaze i-Tech Park, Gurugram, India</p>
            </a>
          </div>

          <!-- Phone -->
          <div class="col-md-4">
            <a href="tel:+919818559036" class="contact-card">
              <div class="icon-box">
                <i class="fas fa-phone-alt contact-icon"></i>
              </div>
              <h4>Call Us</h4>
              <p>+91 98185 59102</p>
            </a>
          </div>

          <!-- Email -->
          <div class="col-md-4">
            <a href="mailto:info@sortoutinnovation.com" class="contact-card">
              <div class="icon-box">
                <i class="fas fa-envelope contact-icon"></i>
              </div>
              <h4>Email Us</h4>
              <p>info@sortoutinnovation.com</p>
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact CTA Section -->
    <section class="contact-cta-section">
      <div class="container text-center">
        <h2 class="cta-title">Let's Connect & Collaborate</h2>
        <p class="cta-subtitle">
          Reach out to us and explore our services. Click below to get started!
        </p>
        <a
          href="https://linktr.ee/sortoutmedia?utm_source=qr_code"
          target="_blank"
          class="btn btn-custom"
          >Connect Now</a
        >
      </div>
    </section>

    <!-- Google Map Section -->
    <section class="map-section">
      <div class="container">
        <div class="section-title text-center">
          <h2>Find Us On <span>Google Maps</span></h2>
          <p>Visit our office at Spaze i-Tech Park, Gurugram, India.</p>
        </div>

        <div class="map-container">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d876546.123456789!2d77.032123456!3d28.40854321!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x123456789abcdef%3A0xabcdef123456789!2sSpaze%20i-Tech%20Park!5e0!3m2!1sen!2sin!4v1700000000000"
            width="100%"
            height="450"
            style="border: 0"
            allowfullscreen=""
            loading="lazy"
          >
          </iframe>
        </div>
      </div>
    </section>

    <?php 
    // Include the footer component
    include '../../components/footer/footer.php';
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>


