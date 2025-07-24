<?php
/**
 * Reusable Partners Component
 * Displays partner logos with Bootstrap classes and minimal custom CSS
 */
?>

<!-- Our Partners Brands Section -->
<section class="partners-section py-5 bg-light">
  <div class="container">
    <div class="text-center mb-4">
      <h2 class="fw-bold text-danger mb-2">Our Trusted Partners</h2>
      <p class="text-muted">We collaborate with industry leaders across banking, retail, technology and e-commerce</p>
      <div class="title-underline mx-auto"></div>
    </div>
    
    <div class="partner-logos-container">
      <!-- First row of logos -->
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
      
      <!-- Second row of logos -->
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
  </div>
</section>

<!-- Minimal inline CSS for partner logos animation -->
<style>
/* Title underline animation */
.title-underline {
  height: 3px;
  width: 150px;
  background: linear-gradient(90deg, transparent, #d10000, transparent);
  margin-top: 15px;
}

/* Partner logos container */
.partner-logos-container {
  width: 100%;
  overflow: hidden;
  position: relative;
  padding: 30px 0;
}

.partner-logos {
  display: flex;
  width: 100%;
  overflow: hidden;
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
  /* filter: grayscale(100%); */
  transition: all 0.3s ease;
  padding: 10px;
  border-radius: 8px;
}

.partner-logo:hover {
  /* filter: grayscale(0%); */
  transform: scale(1.1);
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

/* Responsive adjustments */
@media (max-width: 768px) {
  .partner-logo {
    height: 50px;
    margin: 0 15px;
  }
}

/* Pause animation on hover */
.partner-logos:hover .logos-track {
  animation-play-state: paused;
}
</style>

<script>
// Add animation for random logos to create visual interest
document.addEventListener('DOMContentLoaded', function() {
  const partnerLogos = document.querySelectorAll('.partner-logo');
  if (partnerLogos.length > 0) {
    setInterval(() => {
      // Highlight random logo
      const randomLogo = partnerLogos[Math.floor(Math.random() * partnerLogos.length)];
      randomLogo.style.filter = 'grayscale(0%)';
      randomLogo.style.transform = 'scale(1.1)';
      
      // Reset after animation
      setTimeout(() => {
        randomLogo.style.filter = '';
        randomLogo.style.transform = '';
      }, 2000);
    }, 3000);
  }
});
</script> 