<!DOCTYPE html>
<?php $currentPage = 'about'; ?>
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
    <title>About Us - Sortout Innovation</title>

    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />

    <!-- Font Awesome for Icons -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
    />

    <!-- Google Fonts -->
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap"
      rel="stylesheet"
    />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/pages/about-page/about.css" />
    <link rel="stylesheet" href="/CSS/floating-social-media.css" />
  </head>
  <body>
    <?php include '../../components/navbar/navbar.php'; ?>

    <!-- HERO SECTION -->
    <section class="hero-section">
      <div class="container">
        <div class="row align-items-center">
          <!-- Left Column: Text Content -->
          <div class="col-lg-6 text-center text-lg-start">
            <h1 class="hero-title">
              Empowering Businesses with Smart Solutions
            </h1>
            <p class="hero-subtitle">
              From digital marketing to IT support, we provide cutting-edge
              solutions that help your business grow and thrive in a competitive
              market.
            </p>
            <div class="mt-4">
              <a
                href="/pages/our-services-page/service.html"
                class="btn btn-primary-custom"
                >Explore Our Services</a
              >
              <a
                href="https://wa.me/919818559036"
                target="_blank"
                rel="noopener noreferrer"
                class="btn btn-outline-custom"
                >Get in Touch</a
              >
            </div>
          </div>

          <!-- Right Column: Hero Image -->
          <div class="col-lg-6 text-center">
            <img
              src="/images/about-us-page/main-hero-image.png"
              alt="Empowering Businesses"
              class="hero-img img-fluid"
            />
          </div>
        </div>
      </div>
    </section>

    <section class="our-story-section">
      <div class="container">
        <div class="row align-items-center">
          <!-- Left Image -->
          <div class="col-lg-6 text-center">
            <img
              src="/images/about-us-page/about-us-main-img.png"
              alt="Our Story"
              class="story-image"
            />
          </div>

          <!-- Right Content -->
          <div class="col-lg-6 text-content">
            <h2 class="story-heading">Our Journey to Innovation</h2>
            <p class="story-subheading">
              From a small startup to a trusted global partner.
            </p>
            <p class="story-description">
              At Sortout Innovation, we started with a vision: to simplify
              business operations by offering a one-stop solution for all
              essential services.
            </p>
            <ul class="story-list">
              <li>
                <i class="fas fa-check-circle"></i> Cutting-edge digital
                solutions
              </li>
              <li>
                <i class="fas fa-check-circle"></i> Reliable IT infrastructure &
                support
              </li>
              <li>
                <i class="fas fa-check-circle"></i> End-to-end logistics and
                fulfillment
              </li>
              <li>
                <i class="fas fa-check-circle"></i> HR & recruitment solutions
                that build great teams
              </li>
              <li>
                <i class="fas fa-check-circle"></i> Creative branding & web
                development
              </li>
            </ul>
            <p class="story-footer">
              Whether you're a startup, SME, or large enterprise, we tailor our
              services to your unique business goals.
            </p>
          </div>
        </div>
      </div>
    </section>

    <section class="philosophy-section">
      <div class="container">
        <div class="philosophy-content">
          <h2 class="philosophy-heading">
            <span>Creativity is not just an art,</span> it's our driving force.
          </h2>
          <p class="philosophy-text">
            At <span class="brand-highlight">Sortout Innovation</span>, we break
            the ordinary. We don't follow trends—we create them. Our
            approach is rooted in bold ideas, fearless execution, and
            groundbreaking solutions that redefine industries.
          </p>
          <div class="philosophy-underline"></div>
        </div>

        <!-- <div class="abstract-design">
          <span class="quote">"</span>
          <p>**We see creativity not as a skill, but as a mindset.**</p>
          <span class="quote">"</span>
        </div> -->
      </div>
    </section>

    <section class="creative-process">
      <div class="container">
        <h2 class="process-title">Our Creative Process</h2>
        <p class="process-subtitle">
          We don't just provide services; we craft experiences that drive
          growth and innovation.
        </p>

        <div class="process-steps">
          <!-- Step 1 -->
          <div class="process-step">
            <div class="step-icon"><i class="fas fa-search"></i></div>
            <div class="step-content">
              <h3>Step 1: Understanding Your Needs</h3>
              <p>
                We analyze your business challenges and goals to craft a
                personalized solution.
              </p>
            </div>
          </div>

          <!-- Step 2 -->
          <div class="process-step">
            <div class="step-icon"><i class="fas fa-lightbulb"></i></div>
            <div class="step-content">
              <h3>Step 2: Brainstorming & Ideation</h3>
              <p>
                Creative minds come together to generate innovative ideas
                tailored to your needs.
              </p>
            </div>
          </div>

          <!-- Step 3 -->
          <div class="process-step">
            <div class="step-icon"><i class="fas fa-cogs"></i></div>
            <div class="step-content">
              <h3>Step 3: Execution & Implementation</h3>
              <p>
                We execute the plan with precision, ensuring a seamless
                experience from start to finish.
              </p>
            </div>
          </div>

          <!-- Step 4 -->
          <div class="process-step">
            <div class="step-icon"><i class="fas fa-chart-line"></i></div>
            <div class="step-content">
              <h3>Step 4: Optimization & Growth</h3>
              <p>
                Continuous monitoring and refining to ensure long-term
                success and scalability.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="mission-vision">
      <div class="container">
        <div class="section-header">
          <h2 class="section-title">
            Our <span class="highlight">Mission, Vision & Values</span>
          </h2>
          <p class="section-subtitle">
            We don't just provide services—we create solutions that drive
            businesses forward. Our core beliefs shape our work and commitment
            to excellence.
          </p>
        </div>

        <div class="mv-container">
          <!-- 🚀 Mission Block -->
          <div class="mv-block">
            <div class="mv-icon"><i class="fas fa-rocket"></i></div>
            <h3 class="mv-heading">Our Mission</h3>
            <p class="mv-text">
              To <strong>empower businesses</strong> with innovative, scalable,
              and customized solutions that drive
              <strong>growth and efficiency</strong>.
            </p>
            <ul class="mv-list">
              <li>
                <i class="fas fa-bolt icon"></i> Innovation-First Approach
              </li>
              <li>
                <i class="fas fa-users icon"></i> Customer-Centric Solutions
              </li>
              <li>
                <i class="fas fa-chart-line icon"></i> Scalable Growth
                Strategies
              </li>
              <li>
                <i class="fas fa-globe icon"></i> Global Standards, Local
                Expertise
              </li>
            </ul>
          </div>

          <!-- 👁️ Vision Block -->
          <div class="mv-block">
            <div class="mv-icon"><i class="fas fa-eye"></i></div>
            <h3 class="mv-heading">Our Vision</h3>
            <p class="mv-text">
              To become the
              <span class="highlight"
                >#1 all-in-one business solutions provider</span
              >, helping brands thrive in a competitive digital era.
            </p>
            <ul class="mv-list">
              <li>
                <i class="fas fa-lightbulb icon"></i> Leading Digital
                Transformation
              </li>
              <li>
                <i class="fas fa-handshake icon"></i> Building Long-Term
                Partnerships
              </li>
              <li>
                <i class="fas fa-cogs icon"></i> Innovative Business Models
              </li>
              <li>
                <i class="fas fa-seedling icon"></i> Sustainable & Ethical
                Growth
              </li>
            </ul>
          </div>

          <!-- 💎 Core Values Block -->
          <div class="mv-block">
            <div class="mv-icon"><i class="fas fa-gem"></i></div>
            <h3 class="mv-heading">Our Core Values</h3>
            <p class="mv-text">
              We believe in
              <strong
                >excellence, transparency, and a results-driven approach</strong
              >
              in everything we do.
            </p>
            <ul class="mv-list">
              <li>
                <i class="fas fa-trophy icon"></i> Excellence & Quality First
              </li>
              <li>
                <i class="fas fa-shield-alt icon"></i> Integrity & Transparency
              </li>
              <li>
                <i class="fas fa-users-cog icon"></i> Collaboration & Teamwork
              </li>
              <li>
                <i class="fas fa-chart-pie icon"></i> Data-Driven Decisions
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <section class="our-services">
      <div class="container">
        <!-- Section Heading -->
        <div class="service-intro">
          <h2 class="section-title">
            Our <span class="highlight">Services</span>
          </h2>
          <p class="section-subtitle">
            One company. Many solutions.
            <strong>Infinite possibilities.</strong>
            We offer a full range of services tailored to meet your business
            needs.
          </p>
        </div>

        <!-- Services Grid -->
        <div class="service-grid">
          <div class="service-box red-bg">
            <div class="service-icon"><i class="fas fa-bullhorn"></i></div>
            <h3 class="service-title">Digital Marketing</h3>
            <p class="service-text">Social media, SEO, paid ads & more.</p>
          </div>

          <div class="service-box white-bg">
            <div class="service-icon"><i class="fas fa-laptop-code"></i></div>
            <h3 class="service-title">IT Support & Web Solutions</h3>
            <p class="service-text">
              Web & app development, tech infrastructure.
            </p>
          </div>

          <div class="service-box red-bg">
            <div class="service-icon"><i class="fas fa-calculator"></i></div>
            <h3 class="service-title">CA & Financial Services</h3>
            <p class="service-text">Bookkeeping, tax compliance & advisory.</p>
          </div>

          <div class="service-box white-bg">
            <div class="service-icon"><i class="fas fa-users"></i></div>
            <h3 class="service-title">HR & Recruitment</h3>
            <p class="service-text">Talent acquisition & payroll management.</p>
          </div>

          <div class="service-box red-bg">
            <div class="service-icon"><i class="fas fa-shipping-fast"></i></div>
            <h3 class="service-title">Courier & Shipping</h3>
            <p class="service-text">
              Fast, reliable, and global delivery solutions.
            </p>
          </div>

          <div class="service-box white-bg">
            <div class="service-icon"><i class="fas fa-building"></i></div>
            <h3 class="service-title">Real Estate & Property</h3>
            <p class="service-text">Rental & commercial space solutions.</p>
          </div>

          <div class="service-box red-bg">
            <div class="service-icon">
              <i class="fas fa-calendar-check"></i>
            </div>
            <h3 class="service-title">Event Management</h3>
            <p class="service-text">Corporate events, branding & logistics.</p>
          </div>

          <div class="service-box white-bg">
            <div class="service-icon"><i class="fas fa-paint-brush"></i></div>
            <h3 class="service-title">Creative Design & Branding</h3>
            <p class="service-text">Logos, packaging & marketing materials.</p>
          </div>
        </div>

        <!-- Explore More Button -->
        <div class="explore-more-container">
          <a
            href="/pages/our-services-page/service.html"
            class="explore-more-btn"
            >Explore More</a
          >
        </div>
      </div>
    </section>

    <section class="experience-section">
      <div class="container">
        <!-- Section Heading -->
        <div class="experience-header">
          <h2 class="section-title">
            Every Project is <span>Completed with Experience</span>
          </h2>
          <p class="section-subtitle">
            We take pride in delivering quality, innovation, and results.
            <strong>Driven by data, powered by creativity.</strong>
          </p>
        </div>

        <!-- Experience List -->
        <ul class="experience-list">
          <li>
            <i class="fas fa-award"></i>
            <span><strong>10+ Years</strong> of Industry Expertise</span>
          </li>
          <li>
            <i class="fas fa-users"></i>
            <span><strong>500+ Businesses</strong> Trust Our Services</span>
          </li>
          <li>
            <i class="fas fa-user-tie"></i>
            <span><strong>100+ Skilled</strong> Professionals in Our Team</span>
          </li>
          <li>
            <i class="fas fa-chart-line"></i>
            <span
              ><strong>Data-Driven</strong> Innovation Meets Creativity</span
            >
          </li>
        </ul>
      </div>
    </section>

    <section class="stats-section">
      <div class="container">
        <!-- Section Title -->
        <div class="stats-header">
          <h2 class="section-title">Our <span>Stats</span> Speak for Us</h2>
          <p class="section-subtitle">
            Numbers don't lie.
            <strong>Results-driven strategies, guaranteed success.</strong>
          </p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
          <div class="stat-item">
            <i class="fas fa-box"></i>
            <h3>5,000+</h3>
            <p>Orders Fulfilled (Logistics & Courier)</p>
          </div>

          <div class="stat-item">
            <i class="fas fa-globe"></i>
            <h3>200+</h3>
            <p>Global Clients (Digital & IT Solutions)</p>
          </div>

          <div class="stat-item">
            <i class="fas fa-chart-line"></i>
            <h3>98%</h3>
            <p>Client Satisfaction Rate</p>
          </div>

          <div class="stat-item">
            <i class="fas fa-trophy"></i>
            <h3>Awarded 2024</h3>
            <p>Emerging Business Leader</p>
          </div>
        </div>
      </div>
    </section>

    <section class="founder-section">
      <div class="container text-center">
        <h2 class="founder-heading">A Message from Our Founder</h2>
        <p class="founder-quote">
          🚀 "At <b>Sortout Innovation</b>, we are more than just a service
          provider—we are your growth partners. Our mission is to simplify
          business operations by offering a complete ecosystem of solutions
          under one roof. From digital transformation to logistics, we ensure
          seamless execution, so you can focus on what truly matters—success.
          <br /><br />
          Innovation is our foundation, excellence is our commitment, and
          results are our priority."
        </p>
        <h4 class="founder-name">— Savita Verma</h4>
        <p class="founder-title">Founder & CEO, Sortout Innovation</p>
      </div>
    </section>

     <!-- Our Partners Brands Section -->
     <section class="our-partners-section py-5">
      <div class="container">
        <div class="section-title text-center mb-4">
          <h2>Our Trusted Partners</h2>
          <p>We collaborate with industry leaders across banking, retail, technology and e-commerce</p>
          <div class="title-underline"></div>
        </div>
        
        <div class="partner-logos-container">
          <div class="partner-logos">
            <div class="logos-track logos-track-1">
              <img src="/images/our-brands-logo/amazon.png" alt="Amazon" class="partner-logo">
              <img src="/images/our-brands-logo/sbi-bank.png" alt="SBI Bank" class="partner-logo">
              <img src="/images/our-brands-logo/flipkart.png" alt="Flipkart" class="partner-logo">
              <img src="/images/our-brands-logo/paytm.png" alt="Paytm" class="partner-logo">
              <img src="/images/our-brands-logo/hdfc.png" alt="HDFC Bank" class="partner-logo">
              <img src="/images/our-brands-logo/ICICI.png" alt="ICICI Bank" class="partner-logo">
              <img src="/images/our-brands-logo/bajaj.png" alt="Bajaj" class="partner-logo">
              <img src="/images/our-brands-logo/Reliance-Retail.png" alt="Reliance Retail" class="partner-logo">
              <img src="/images/our-brands-logo/nykaa.png" alt="Nykaa" class="partner-logo">
              <img src="/images/our-brands-logo/big-basket.png" alt="Big Basket" class="partner-logo">
              <img src="/images/our-brands-logo/google-pay.png" alt="Google Pay" class="partner-logo">
            </div>
            <div class="logos-track logos-track-1">
              <img src="/images/our-brands-logo/amazon.png" alt="Amazon" class="partner-logo">
              <img src="/images/our-brands-logo/sbi-bank.png" alt="SBI Bank" class="partner-logo">
              <img src="/images/our-brands-logo/flipkart.png" alt="Flipkart" class="partner-logo">
              <img src="/images/our-brands-logo/paytm.png" alt="Paytm" class="partner-logo">
              <img src="/images/our-brands-logo/hdfc.png" alt="HDFC Bank" class="partner-logo">
              <img src="/images/our-brands-logo/ICICI.png" alt="ICICI Bank" class="partner-logo">
              <img src="/images/our-brands-logo/bajaj.png" alt="Bajaj" class="partner-logo">
              <img src="/images/our-brands-logo/Reliance-Retail.png" alt="Reliance Retail" class="partner-logo">
              <img src="/images/our-brands-logo/nykaa.png" alt="Nykaa" class="partner-logo">
              <img src="/images/our-brands-logo/big-basket.png" alt="Big Basket" class="partner-logo">
              <img src="/images/our-brands-logo/google-pay.png" alt="Google Pay" class="partner-logo">
            </div>
          </div>
          
          <div class="partner-logos mt-4">
            <div class="logos-track logos-track-2">
              <img src="/images/our-brands-logo/phone-pay.png" alt="Phone Pay" class="partner-logo">
              <img src="/images/our-brands-logo/axis-bank.png" alt="Axis Bank" class="partner-logo">
              <img src="/images/our-brands-logo/congnizant.png" alt="Cognizant" class="partner-logo">
              <img src="/images/our-brands-logo/hcl.png" alt="HCL" class="partner-logo">
              <img src="/images/our-brands-logo/ibm.png" alt="IBM" class="partner-logo">
              <img src="/images/our-brands-logo/techMahindra.png" alt="Tech Mahindra" class="partner-logo">
              <img src="/images/our-brands-logo/capmine.png" alt="Capmine" class="partner-logo">
              <img src="/images/our-brands-logo/infoses.png" alt="Infosys" class="partner-logo">
              <img src="/images/our-brands-logo/tata.png" alt="Tata" class="partner-logo">
              <img src="/images/our-brands-logo/wiproLogo.png" alt="Wipro" class="partner-logo">
            </div>
            <div class="logos-track logos-track-2">
              <img src="/images/our-brands-logo/phone-pay.png" alt="Phone Pay" class="partner-logo">
              <img src="/images/our-brands-logo/axis-bank.png" alt="Axis Bank" class="partner-logo">
              <img src="/images/our-brands-logo/congnizant.png" alt="Cognizant" class="partner-logo">
              <img src="/images/our-brands-logo/hcl.png" alt="HCL" class="partner-logo">
              <img src="/images/our-brands-logo/ibm.png" alt="IBM" class="partner-logo">
              <img src="/images/our-brands-logo/techMahindra.png" alt="Tech Mahindra" class="partner-logo">
              <img src="/images/our-brands-logo/capmine.png" alt="Capmine" class="partner-logo">
              <img src="/images/our-brands-logo/infoses.png" alt="Infosys" class="partner-logo">
              <img src="/images/our-brands-logo/tata.png" alt="Tata" class="partner-logo">
              <img src="/images/our-brands-logo/wiproLogo.png" alt="Wipro" class="partner-logo">
            </div>
          </div>
        </div>
        
        <div class="partner-corner partner-corner-top-left"></div>
        <div class="partner-corner partner-corner-top-right"></div>
        <div class="partner-corner partner-corner-bottom-left"></div>
        <div class="partner-corner partner-corner-bottom-right"></div>
      </div>
    </section>

    <style>
    .our-partners-section {
      background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
      padding: 80px 0;
      overflow: hidden;
      position: relative;
      border-radius: 10px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.05);
      margin: 30px 0;
    }

    .our-partners-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d10000' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
      opacity: 0.5;
      z-index: 0;
    }

    .partner-icon {
      display: inline-block;
      font-size: 2rem;
      color: #d10000;
      margin-bottom: 15px;
    }

    .our-partners-section h2 {
      font-size: 2.8rem;
      font-weight: 700;
      color: #d10000;
      margin-bottom: 15px;
      position: relative;
      display: inline-block;
    }

    .title-underline {
      height: 3px;
      width: 0;
      background: linear-gradient(90deg, transparent, #d10000, transparent);
      margin: 15px auto 30px;
      transition: width 1s ease;
    }

    .section-title.animate .title-underline {
      width: 150px;
    }

    .our-partners-section p {
      color: #666;
      font-size: 1.1rem;
      margin-bottom: 40px;
      max-width: 700px;
      margin-left: auto;
      margin-right: auto;
    }

    .partner-logos-container {
      width: 100%;
      overflow: hidden;
      position: relative;
      padding: 30px 0;
      z-index: 1;
    }

    .partner-logos {
      display: flex;
      width: 100%;
      overflow: hidden;
      position: relative;
    }

    .logos-track {
      display: flex;
      white-space: nowrap;
      align-items: center;
    }

    .logos-track-1 {
      animation: scroll 35s linear infinite;
    }

    .logos-track-2 {
      animation: scroll-reverse 40s linear infinite;
    }

    .partner-logo {
      height: 70px;
      width: auto;
      margin: 0 25px;
      object-fit: contain;
      filter: grayscale(0%);
      transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
      transform: translateY(0);
      box-shadow: 0 5px 15px rgba(0,0,0,0);
      padding: 10px;
      border-radius: 8px;
    }

    .partner-logo:hover {
      filter: grayscale(100%);
      transform: scale(1.15) translateY(-5px) rotate(2deg);
      box-shadow: 0 15px 30px rgba(0,0,0,0.1);
      z-index: 10;
    }

    @keyframes scroll {
      0% {
        transform: translateX(0);
      }
      100% {
        transform: translateX(-100%);
      }
    }

    @keyframes scroll-reverse {
      0% {
        transform: translateX(-100%);
      }
      100% {
        transform: translateX(0);
      }
    }

    .partner-corner {
      position: absolute;
      width: 30px;
      height: 30px;
      border-color: #d10000;
      border-style: solid;
      border-width: 0;
      z-index: 2;
    }

    .partner-corner-top-left {
      top: 20px;
      left: 20px;
      border-top-width: 3px;
      border-left-width: 3px;
      border-top-left-radius: 8px;
    }

    .partner-corner-top-right {
      top: 20px;
      right: 20px;
      border-top-width: 3px;
      border-right-width: 3px;
      border-top-right-radius: 8px;
    }

    .partner-corner-bottom-left {
      bottom: 20px;
      left: 20px;
      border-bottom-width: 3px;
      border-left-width: 3px;
      border-bottom-left-radius: 8px;
    }

    .partner-corner-bottom-right {
      bottom: 20px;
      right: 20px;
      border-bottom-width: 3px;
      border-right-width: 3px;
      border-bottom-right-radius: 8px;
    }

    /* Add animation for logos */
    .partner-logos:hover .logos-track {
      animation-play-state: paused;
    }

    /* Create pulsing effect for random logos */
    @keyframes pulse {
      0% {
        transform: scale(1);
        box-shadow: 0 5px 15px rgba(0,0,0,0);
      }
      50% {
        transform: scale(1.08);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
      }
      100% {
        transform: scale(1);
        box-shadow: 0 5px 15px rgba(0,0,0,0);
      }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .our-partners-section {
        padding: 50px 0;
      }
      
      .our-partners-section h2 {
        font-size: 2.2rem;
      }
      
      .partner-logo {
        height: 50px;
        margin: 0 15px;
      }
      
      .partner-corner {
        width: 20px;
        height: 20px;
      }
    }
    </style>

    <script>
    // Animate title underline when scrolled into view
    document.addEventListener('DOMContentLoaded', function() {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('animate');
          }
        });
      }, { threshold: 0.3 });

      const sectionTitle = document.querySelector('.our-partners-section .section-title');
      if (sectionTitle) {
        observer.observe(sectionTitle);
      }
      
      // Add pulsing animation to random logos
      const partnerLogos = document.querySelectorAll('.partner-logo');
      if (partnerLogos.length > 0) {
        setInterval(() => {
          // Get random logo
          const randomLogo = partnerLogos[Math.floor(Math.random() * partnerLogos.length)];
          // Apply animation
          randomLogo.style.animation = 'pulse 2s ease';
          
          // Remove animation after it's done
          setTimeout(() => {
            randomLogo.style.animation = '';
          }, 2000);
        }, 3000);
      }
    });
    </script>

    <section id="cta-section" class="cta-section">
      <div class="container">
        <div class="cta-content">
          <h2 class="cta-heading">
            Let's Build Something <span>Great Together!</span>
          </h2>
          <p class="cta-text">
            Your business needs a partner who understands growth, innovation,
            and execution.
          </p>

          <!-- Contact Details -->
          <div class="cta-contact">
            <p>
              <i class="fas fa-phone-alt"></i> Call Us:
              <a
                href="tel:+919818559036"
                target="_blank"
                rel="noopener noreferrer"
                >+91 9818559036</a
              >
            </p>
            <p>
              <i class="fas fa-envelope"></i> Email:
              <a href="mailto:hello@sortoutinnovation.com"
                >hello@sortoutinnovation.com</a
              >
            </p>
          </div>

          <!-- CTA Buttons -->
          <div class="cta-buttons">
            <a
              href="tel:+919818559036"
              target="_blank"
              rel="noopener noreferrer"
              class="btn btn-primary"
              >🔴 Get in Touch</a
            >
            <a
              href="/pages/our-services-page/service.html"
              class="btn btn-secondary"
              >⚪ Explore Our Services</a
            >
          </div>
        </div>
      </div>
    </section>

    <?php 
    // Include the footer component
    include '../../components/footer/footer.php';
    ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>


