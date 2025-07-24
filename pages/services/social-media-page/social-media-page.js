// Initialize AOS animations
AOS.init({
    duration: 800,
    easing: 'ease-out',
    once: true
});

// Enhanced Results Badge Animation
document.addEventListener('DOMContentLoaded', function() {
    const resultsBadge = document.getElementById('resultsBadge');
    const engagementStat = document.getElementById('engagementStat');
    const conversionStat = document.getElementById('conversionStat');
    const engagementValue = document.getElementById('engagementValue');
    const conversionValue = document.getElementById('conversionValue');
    
    if(!resultsBadge || !engagementStat || !conversionStat) return;
    
    // Initial appearance animation with smoother elastic motion
    gsap.fromTo(resultsBadge, 
        { opacity: 0, scale: 0.8, y: 20, rotation: -2 },
        { 
            opacity: 1, 
            scale: 1, 
            y: 0, 
            rotation: 0,
            duration: 1.5, 
            ease: "elastic.out(1, 0.5)", 
            delay: 0.5 
        }
    );
    
    // Create counter animations for percentage values with smoother easing
    // Engagement counter with smooth counting
    let engCount = { val: 0 };
    gsap.to(engCount, {
        val: 200,
        duration: 2.8,
        delay: 1.2,
        ease: "power3.out",
        onUpdate: function() {
            if(engagementValue) {
                engagementValue.textContent = Math.floor(engCount.val) + '%';
            }
        },
        onComplete: function() {
            // Enhanced highlight effect on completion
            gsap.to(engagementStat, {
                backgroundColor: "rgba(255, 0, 0, 0.1)",
                boxShadow: "0 0 20px rgba(255, 0, 0, 0.15)",
                duration: 0.7,
                yoyo: true,
                repeat: 1,
                ease: "power1.inOut"
            });
        }
    });
    
    // Conversion counter with smooth counting
    let convCount = { val: 0 };
    gsap.to(convCount, {
        val: 58,
        duration: 2.8,
        delay: 1.8,
        ease: "power3.out",
        onUpdate: function() {
            if(conversionValue) {
                conversionValue.textContent = Math.floor(convCount.val) + '%';
            }
        },
        onComplete: function() {
            // Enhanced highlight effect on completion
            gsap.to(conversionStat, {
                backgroundColor: "rgba(255, 0, 0, 0.1)",
                boxShadow: "0 0 20px rgba(255, 0, 0, 0.15)",
                duration: 0.7,
                yoyo: true,
                repeat: 1,
                ease: "power1.inOut"
            });
        }
    });
    
    // Create a more natural, organic floating animation using a complex timeline
    const floatTimeline = gsap.timeline({
        repeat: -1,
        delay: 3,
        defaults: { ease: "sine.inOut" }
    });
    
    floatTimeline
        .to(resultsBadge, { y: "-=15", rotation: 1, scale: 1.02, duration: 2.5 })
        .to(resultsBadge, { y: "+=10", rotation: -0.5, scale: 1.01, duration: 2 })
        .to(resultsBadge, { y: "-=8", rotation: 0.2, scale: 1.015, duration: 1.8 })
        .to(resultsBadge, { y: "+=13", rotation: 0, scale: 1, duration: 2.2 });
    
    // Periodic highlight sequence for the badge stats with improved visual effects
    const highlightStats = () => {
        // Enhanced glow effect for the badge itself
        gsap.to(resultsBadge, {
            boxShadow: "0 10px 35px rgba(213, 0, 0, 0.2)",
            duration: 2,
            yoyo: true,
            repeat: 1,
            ease: "sine.inOut"
        });
        
        // Highlight engagement stat with improved animation
        gsap.timeline({delay: 0.5})
            .to(engagementStat, {
                backgroundColor: "rgba(255, 0, 0, 0.1)",
                boxShadow: "0 0 18px rgba(255, 0, 0, 0.15)",
                scale: 1.05,
                duration: 0.8,
                ease: "sine.inOut"
            })
            .to(engagementValue, {
                color: "var(--primary-red-hover)",
                textShadow: "0 0 10px var(--primary-red-glow)",
                scale: 1.12,
                duration: 0.6,
                ease: "sine.inOut"
            }, "-=0.6")
            .to(engagementStat, {
                backgroundColor: "rgba(255, 0, 0, 0)",
                boxShadow: "0 0 0px rgba(255, 0, 0, 0)",
                scale: 1,
                duration: 0.8,
                ease: "power1.out"
            }, "+=0.6")
            .to(engagementValue, {
                color: "var(--primary-red)",
                textShadow: "none",
                scale: 1,
                duration: 0.6,
                ease: "power1.out"
            }, "-=0.6");
        
        // Highlight conversion stat with improved animation and delay
        gsap.timeline({delay: 3})
            .to(conversionStat, {
                backgroundColor: "rgba(255, 0, 0, 0.1)",
                boxShadow: "0 0 18px rgba(255, 0, 0, 0.15)",
                scale: 1.05,
                duration: 0.8,
                ease: "sine.inOut"
            })
            .to(conversionValue, {
                color: "var(--primary-red-hover)",
                textShadow: "0 0 10px var(--primary-red-glow)",
                scale: 1.12,
                duration: 0.6,
                ease: "sine.inOut"
            }, "-=0.6")
            .to(conversionStat, {
                backgroundColor: "rgba(255, 0, 0, 0)",
                boxShadow: "0 0 0px rgba(255, 0, 0, 0)",
                scale: 1,
                duration: 0.8,
                ease: "power1.out"
            }, "+=0.6")
            .to(conversionValue, {
                color: "var(--primary-red)",
                textShadow: "none",
                scale: 1,
                duration: 0.6,
                ease: "power1.out"
            }, "-=0.6");
    };
    
    // Start the highlight sequence after initial animations
    setTimeout(highlightStats, 5000);
    
    // Repeat the highlight sequence periodically
    setInterval(highlightStats, 15000);
});

// Smooth scroll for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Parallax effect for background elements
if (window.innerWidth > 768) {
    window.addEventListener('scroll', function() {
        const scrolled = window.scrollY;
        
        // Apply parallax to hero background
        const heroBg = document.querySelector('.hero-bg-gradient');
        if (heroBg) {
            heroBg.style.transform = `translateY(${scrolled * 0.15}px)`;
        }
        
        // Apply subtle parallax to features background
        const featuresBg = document.querySelector('.features-bg');
        if (featuresBg) {
            featuresBg.style.transform = `translateY(${(scrolled - 800) * 0.08}px)`;
        }
    });
}

// Initialize dynamic content effects
document.addEventListener('DOMContentLoaded', function() {
    // Animated icon for the pulse button
    const pulseButton = document.querySelector('.pulse-btn');
    if (pulseButton) {
        const icon = pulseButton.querySelector('i');
        
        pulseButton.addEventListener('mouseenter', function() {
            gsap.to(icon, {
                x: 5,
                duration: 0.3,
                ease: "power2.out"
            });
        });
        
        pulseButton.addEventListener('mouseleave', function() {
            gsap.to(icon, {
                x: 0,
                duration: 0.3,
                ease: "power2.out"
            });
        });
    }
    
    // Tilt effect for content frame on desktop
    const frame = document.querySelector('.content-frame');
    
    if (frame && window.innerWidth > 992) {
        document.addEventListener('mousemove', function(e) {
            const rect = frame.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;
            
            const maxRotation = 5; // Maximum rotation in degrees
            const xAxis = ((e.clientX - centerX) / (rect.width / 2)) * maxRotation;
            const yAxis = ((e.clientY - centerY) / (rect.height / 2)) * maxRotation;
            
            gsap.to(frame, {
                rotationY: -xAxis,
                rotationX: yAxis,
                duration: 0.5,
                ease: "power1.out"
            });
        });
        
        // Reset rotation when mouse leaves the area
        frame.addEventListener('mouseleave', function() {
            gsap.to(frame, {
                rotationY: 0,
                rotationX: 0,
                duration: 0.7,
                ease: "power1.inOut"
            });
        });
    }
    
    // Enhanced Features section animations
    const featureCards = document.querySelectorAll('.feature-card');
    
    if (featureCards.length > 0) {
        // Create staggered entrance animation for feature cards
        gsap.set(featureCards, { y: 50, opacity: 0 });
        
        // Setup ScrollTrigger for features section
        ScrollTrigger.create({
            trigger: '.features-section',
            start: 'top 80%',
            onEnter: () => {
                gsap.to(featureCards, {
                    y: 0,
                    opacity: 1,
                    duration: 1.2,
                    stagger: 0.15,
                    ease: "power3.out"
                });
            },
            once: true
        });
        
        // Add hover effects for feature cards
        featureCards.forEach(card => {
            const iconWrapper = card.querySelector('.feature-icon-wrapper');
            const iconBg = card.querySelector('.feature-icon-bg');
            const title = card.querySelector('.feature-title');
            const lordIcon = card.querySelector('lord-icon');
            
            card.addEventListener('mouseenter', function() {
                // Animate icon background
                gsap.to(iconBg, {
                    rotation: 0,
                    scale: 1.15,
                    backgroundColor: "rgba(255, 0, 0, 0.12)",
                    duration: 0.4,
                    ease: "power2.out"
                });
                
                // Trigger lord-icon animation
                if (lordIcon) {
                    lordIcon.setAttribute('trigger', 'loop');
                }
                
                // Animate title
                gsap.to(title, {
                    color: "var(--primary-red)",
                    x: 5,
                    duration: 0.4,
                    ease: "power2.out"
                });
                
                // Animate the stats
                const statValue = card.querySelector('.stat-value');
                if (statValue) {
                    gsap.to(statValue, {
                        scale: 1.1,
                        color: "var(--primary-red-hover)",
                        duration: 0.4,
                        ease: "power2.out"
                    });
                }
            });
            
            card.addEventListener('mouseleave', function() {
                // Reset icon background
                gsap.to(iconBg, {
                    rotation: -5,
                    scale: 1,
                    backgroundColor: "rgba(255, 0, 0, 0.05)",
                    duration: 0.4,
                    ease: "power2.out"
                });
                
                // Reset lord-icon animation
                if (lordIcon) {
                    lordIcon.setAttribute('trigger', 'hover');
                }
                
                // Reset title
                gsap.to(title, {
                    color: "var(--text-dark)",
                    x: 0,
                    duration: 0.4,
                    ease: "power2.out"
                });
                
                // Reset stats
                const statValue = card.querySelector('.stat-value');
                if (statValue) {
                    gsap.to(statValue, {
                        scale: 1,
                        color: "var(--primary-red)",
                        duration: 0.4,
                        ease: "power2.out"
                    });
                }
            });
        });
        
        // Setup stat counter animations with ScrollTrigger
        const statValues = document.querySelectorAll('.stat-value');
        
        statValues.forEach(statElement => {
            const originalText = statElement.textContent;
            let suffix = '';
            let prefix = '';
            
            // Determine if there's a suffix like % or x
            if (originalText.includes('%')) {
                suffix = '%';
            } else if (originalText.includes('x')) {
                suffix = 'x';
            }
            
            // Extract the numeric value
            const numericValue = parseFloat(originalText.replace(/[^0-9.]/g, ''));
            
            // Clear the element for the animation
            statElement.textContent = '0' + suffix;
            
            ScrollTrigger.create({
                trigger: statElement,
                start: 'top 90%',
                onEnter: () => {
                    let counter = { value: 0 };
                    gsap.to(counter, {
                        value: numericValue,
                        duration: 2,
                        ease: "power2.out",
                        onUpdate: function() {
                            // Format the counter value
                            if (numericValue % 1 === 0) {
                                statElement.textContent = prefix + Math.round(counter.value) + suffix;
                            } else {
                                statElement.textContent = prefix + counter.value.toFixed(1) + suffix;
                            }
                        },
                        onComplete: function() {
                            // Add a little bounce at the end
                            gsap.to(statElement, {
                                scale: 1.2,
                                duration: 0.2,
                                yoyo: true,
                                repeat: 1,
                                ease: "power1.out"
                            });
                        }
                    });
                },
                once: true
            });
        });
    }
    
    // Features CTA button animation
    const featuresCta = document.querySelector('.btn-features-cta');
    if (featuresCta) {
        const ctaIcon = featuresCta.querySelector('i');
        
        featuresCta.addEventListener('mouseenter', function() {
            gsap.to(ctaIcon, {
                x: 5,
                duration: 0.3,
                ease: "power2.out"
            });
        });
        
        featuresCta.addEventListener('mouseleave', function() {
            gsap.to(ctaIcon, {
                x: 0,
                duration: 0.3,
                ease: "power2.out"
            });
        });
        
        // Create a subtle animation loop for the CTA
        const ctaPulse = gsap.timeline({repeat: -1, repeatDelay: 2});
        ctaPulse
            .to(featuresCta, {
                boxShadow: '0 15px 40px rgba(255, 0, 0, 0.4)',
                scale: 1.05,
                duration: 0.8,
                ease: "power1.inOut"
            })
            .to(featuresCta, {
                boxShadow: '0 8px 25px rgba(255, 0, 0, 0.3)',
                scale: 1,
                duration: 0.8,
                ease: "power1.inOut"
            });
    }
    
    // Special animation for the featured card
    const featuredCard = document.querySelector('.featured-card');
    if (featuredCard) {
        const featuredTag = featuredCard.querySelector('.featured-tag');
        
        // Make the featured tag pulse
        gsap.to(featuredTag, {
            scale: 1.1,
            duration: 0.8,
            repeat: -1,
            yoyo: true,
            ease: "sine.inOut"
        });
        
        // Add a special glow effect on hover
        featuredCard.addEventListener('mouseenter', function() {
            gsap.to(featuredCard, {
                boxShadow: '0 20px 50px rgba(255, 0, 0, 0.2)',
                duration: 0.5,
                ease: "power2.out"
            });
        });
        
        featuredCard.addEventListener('mouseleave', function() {
            gsap.to(featuredCard, {
                boxShadow: '0 15px 40px rgba(255, 0, 0, 0.1)',
                duration: 0.5,
                ease: "power2.out"
            });
        });
    }
    
    // Engagement section animations
    const engagementSection = document.querySelector('.engagement-section');
    if (engagementSection) {
        // Animate decorative elements
        const decoCircle1 = document.querySelector('.circle-1');
        const decoCircle2 = document.querySelector('.circle-2');
        const decoLine1 = document.querySelector('.line-1');
        const decoLine2 = document.querySelector('.line-2');
        
        if (decoCircle1 && decoCircle2) {
            // Continuous rotation for the circles
            gsap.to(decoCircle1, {
                rotation: 360,
                duration: 40,
                repeat: -1,
                ease: "none",
                transformOrigin: "center center"
            });
            
            gsap.to(decoCircle2, {
                rotation: -360,
                duration: 60,
                repeat: -1,
                ease: "none",
                transformOrigin: "center center"
            });
        }
        
        // Create floating effect for the engagement bubbles
        const floatingElements = document.querySelectorAll('.floating-engagement');
        
        floatingElements.forEach((element, index) => {
            // Create a unique floating animation for each bubble
            const timeline = gsap.timeline({
                repeat: -1,
                yoyo: true,
                defaults: { ease: "sine.inOut" }
            });
            
            // Randomize the animation slightly for each bubble
            const yAmount = 10 + Math.random() * 10;
            const xAmount = 5 + Math.random() * 5;
            const duration = 2 + Math.random() * 1.5;
            const delay = index * 0.3;
            
            timeline
                .to(element, { y: `-=${yAmount}`, x: `+=${xAmount}`, duration: duration, delay: delay })
                .to(element, { y: `+=${yAmount/2}`, x: `-=${xAmount/2}`, duration: duration * 0.7 })
                .to(element, { y: `-=${yAmount/3}`, x: `+=${xAmount/3}`, duration: duration * 0.5 })
                .to(element, { y: `+=${yAmount}`, x: `-=${xAmount}`, duration: duration });
            
            // Add pulse effect on hover
            element.addEventListener('mouseenter', function() {
                gsap.to(this, {
                    scale: 1.1,
                    duration: 0.3,
                    boxShadow: '0 10px 30px rgba(0, 0, 0, 0.15)',
                    ease: "power1.out"
                });
            });
            
            element.addEventListener('mouseleave', function() {
                gsap.to(this, {
                    scale: 1,
                    duration: 0.3,
                    boxShadow: '0 8px 25px rgba(0, 0, 0, 0.1)',
                    ease: "power1.out"
                });
            });
        });
        
        // Animate the metric items on scroll
        const metricItems = document.querySelectorAll('.metric-item');
        
        ScrollTrigger.batch(metricItems, {
            onEnter: batch => {
                gsap.from(batch, {
                    y: 30,
                    opacity: 0,
                    stagger: 0.15,
                    duration: 0.8,
                    ease: "power2.out"
                });
                
                // Animate the metric values
                batch.forEach(item => {
                    const metricValue = item.querySelector('.metric-value');
                    if (metricValue) {
                        const originalText = metricValue.textContent;
                        let suffix = '';
                        
                        // Determine if there's a suffix like % or x
                        if (originalText.includes('%')) {
                            suffix = '%';
                        } else if (originalText.includes('x')) {
                            suffix = 'x';
                        }
                        
                        // Extract the numeric value
                        const numericValue = parseFloat(originalText.replace(/[^0-9.]/g, ''));
                        
                        // Animate the counter
                        let obj = { count: 0 };
                        gsap.to(obj, {
                            count: numericValue,
                            duration: 2,
                            delay: 0.2,
                            ease: "power2.out",
                            onUpdate: function() {
                                if (numericValue % 1 === 0) {
                                    metricValue.textContent = Math.round(obj.count) + suffix;
                                } else {
                                    metricValue.textContent = obj.count.toFixed(1) + suffix;
                                }
                            }
                        });
                    }
                });
            },
            once: true
        });
        
        // Engagement CTA button effects
        const ctaButton = document.querySelector('.btn-engagement');
        if (ctaButton) {
            const ctaIcon = ctaButton.querySelector('i');
            
            ctaButton.addEventListener('mouseenter', function() {
                gsap.to(ctaIcon, {
                    x: 5,
                    duration: 0.3,
                    ease: "power2.out"
                });
            });
            
            ctaButton.addEventListener('mouseleave', function() {
                gsap.to(ctaIcon, {
                    x: 0,
                    duration: 0.3,
                    ease: "power2.out"
                });
            });
        }
        
        // Engagement image animation
        const engagementImage = document.querySelector('.engagement-image');
        if (engagementImage) {
            // Subtle float animation for the image
            gsap.to(engagementImage, {
                y: -15,
                duration: 3,
                repeat: -1,
                yoyo: true,
                ease: "power1.inOut"
            });
            
            // Add entry animation
            ScrollTrigger.create({
                trigger: '.engagement-image-container',
                start: 'top 80%',
                onEnter: () => {
                    gsap.from(engagementImage, {
                        scale: 0.8,
                        opacity: 0,
                        duration: 1,
                        ease: "back.out(1.7)"
                    });
                },
                once: true
            });
        }
    }
    
    // Services section animations
    const servicesSection = document.querySelector('.services-section');
    if (servicesSection) {
        // Services cards animations
        const serviceCards = document.querySelectorAll('.service-card');
        
        // Register the ScrollTrigger for staggered entrance
        ScrollTrigger.batch(serviceCards, {
            start: 'top 85%',
            onEnter: batch => {
                gsap.from(batch, {
                    y: 50,
                    opacity: 0,
                    scale: 0.9,
                    stagger: 0.1,
                    duration: 0.8,
                    ease: "power2.out"
                });
            },
            once: true
        });
        
        // Add interactive animations to service cards
        serviceCards.forEach(card => {
            // Get elements
            const cardInner = card.querySelector('.service-card-inner');
            const iconWrapper = card.querySelector('.service-icon-wrapper');
            const lordIcon = card.querySelector('lord-icon');
            const title = card.querySelector('.service-title');
            
            // Add hover effects
            card.addEventListener('mouseenter', function() {
                // Change lord-icon animation trigger to loop
                if (lordIcon) {
                    lordIcon.setAttribute('trigger', 'loop');
                }
                
                // Animate the icon wrapper
                gsap.to(iconWrapper, {
                    scale: 1.1,
                    backgroundColor: 'rgba(255, 0, 0, 0.12)',
                    duration: 0.4,
                    ease: "power2.out"
                });
                
                // Animate the title
                gsap.to(title, {
                    color: 'var(--primary-red)',
                    x: 3,
                    duration: 0.4,
                    ease: "power2.out"
                });
                
                // Add a subtle scale to the entire card
                gsap.to(cardInner, {
                    y: -10,
                    boxShadow: '0 15px 50px rgba(0, 0, 0, 0.1)',
                    duration: 0.4,
                    ease: "power2.out"
                });
                
                // Animate the accent line
                gsap.to(cardInner, {
                    '--accent-height': '100%',
                    duration: 0.6,
                    ease: "power2.inOut"
                });
            });
            
            card.addEventListener('mouseleave', function() {
                // Change lord-icon animation trigger back to hover
                if (lordIcon) {
                    lordIcon.setAttribute('trigger', 'hover');
                }
                
                // Reset the icon wrapper
                gsap.to(iconWrapper, {
                    scale: 1,
                    backgroundColor: 'rgba(255, 0, 0, 0.05)',
                    duration: 0.4,
                    ease: "power2.out"
                });
                
                // Reset the title
                gsap.to(title, {
                    color: 'var(--text-dark)',
                    x: 0,
                    duration: 0.4,
                    ease: "power2.out"
                });
                
                // Reset the card scale
                gsap.to(cardInner, {
                    y: 0,
                    boxShadow: '0 10px 30px rgba(0, 0, 0, 0.05)',
                    duration: 0.4,
                    ease: "power2.out"
                });
            });
        });
        
        // Featured service card special animation
        const featuredService = document.querySelector('.featured-service');
        if (featuredService) {
            const featuredLabel = featuredService.querySelector('.featured-label');
            
            // Add pulse animation to the featured label
            gsap.to(featuredLabel, {
                scale: 1.1,
                duration: 0.8,
                repeat: -1,
                yoyo: true,
                ease: "sine.inOut"
            });
            
            // Add a subtle shadow animation
            const shadowPulse = gsap.timeline({ repeat: -1, repeatDelay: 2 });
            shadowPulse
                .to(featuredService.querySelector('.service-card-inner'), {
                    boxShadow: '0 15px 50px rgba(255, 0, 0, 0.15)',
                    duration: 1.5,
                    ease: "sine.inOut"
                })
                .to(featuredService.querySelector('.service-card-inner'), {
                    boxShadow: '0 15px 40px rgba(255, 0, 0, 0.08)',
                    duration: 1.5,
                    ease: "sine.inOut"
                });
        }
        
        // Services CTA button animation
        const servicesCta = document.querySelector('.btn-services-cta');
        if (servicesCta) {
            const ctaIcon = servicesCta.querySelector('i');
            
            // Arrow hover animation
            servicesCta.addEventListener('mouseenter', function() {
                gsap.to(ctaIcon, {
                    x: 5,
                    duration: 0.3,
                    ease: "power2.out"
                });
            });
            
            servicesCta.addEventListener('mouseleave', function() {
                gsap.to(ctaIcon, {
                    x: 0,
                    duration: 0.3,
                    ease: "power2.out"
                });
            });
            
            // Add attention-grabbing animation
            const ctaPulse = gsap.timeline({ repeat: -1, repeatDelay: 3 });
            ctaPulse
                .to(servicesCta, {
                    scale: 1.05,
                    boxShadow: '0 15px 35px rgba(255, 0, 0, 0.4)',
                    duration: 0.8,
                    ease: "power1.inOut"
                })
                .to(servicesCta, {
                    scale: 1,
                    boxShadow: '0 8px 25px rgba(255, 0, 0, 0.3)',
                    duration: 0.8,
                    ease: "power1.inOut"
                });
        }
    }
    
    // Initialize pricing animations
    initPricingAnimations();
    
    // Initialize modal functionality
    initModalFunctionality();
    
    // Initialize pricing buttons
    initPricingButtons();
});

// GSAP Animations
document.addEventListener('DOMContentLoaded', function() {
    // Subtle hover effect for the image frame
    const contentFrame = document.querySelector('.content-frame');
    
    if(contentFrame) {
        contentFrame.addEventListener('mousemove', function(e) {
            const rect = contentFrame.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width - 0.5) * 5;
            const y = ((e.clientY - rect.top) / rect.height - 0.5) * 5;
            
            gsap.to(contentFrame, {
                rotateY: x,
                rotateX: -y,
                duration: 0.5,
                ease: "power1.out"
            });
        });
        
        contentFrame.addEventListener('mouseleave', function() {
            gsap.to(contentFrame, {
                rotateY: 0,
                rotateX: 0,
                duration: 0.5,
                ease: "power1.out"
            });
        });
    }
    
    // Animate counter numbers for metrics
    function animateCounter(elementId, targetValue) {
        let obj = { count: 0 };
        const element = document.getElementById(elementId);
        
        if(element) {
            gsap.to(obj, {
                count: targetValue,
                duration: 2,
                delay: 1,
                ease: "power1.inOut",
                onUpdate: function() {
                    element.textContent = Math.round(obj.count);
                }
            });
        }
    }
    
    animateCounter('likeCounter', 1200);
    animateCounter('commentCounter', 438);
    animateCounter('shareCounter', 86);
    
    // Enhanced Results Badge Animation
    
    // 1. Initial Appearance Animation
    const resultsTimeline = gsap.timeline({delay: 0.5});
    
    resultsTimeline
        .fromTo('#resultsBadge', {
            autoAlpha: 0,
            scale: 0.8,
            y: 20
        }, {
            autoAlpha: 1,
            scale: 1,
            y: 0,
            duration: 0.8,
            ease: "back.out(1.7)"
        });
    
    // 2. Percentage Counter Animations with Better Timing
    function animatePercentage(elementId, targetValue, prefix = "", suffix = "%") {
        let obj = { count: 0 };
        const element = document.getElementById(elementId);
        
        if(element) {
            gsap.to(obj, {
                count: targetValue,
                duration: 1.5,
                delay: 1.2,
                ease: "power2.out",
                onUpdate: function() {
                    element.textContent = prefix + Math.round(obj.count) + suffix;
                },
                onComplete: function() {
                    // Add highlight animation after counter completes
                    gsap.fromTo(element, 
                        {color: "var(--primary-red)", scale: 1},
                        {color: "#ff6b6b", scale: 1.1, duration: 0.3, yoyo: true, repeat: 1}
                    );
                }
            });
        }
    }
    
    animatePercentage('engagementValue', 200);
    animatePercentage('conversionValue', 58);
    
    // 3. Continuous Subtle Floating Animation
    gsap.to('#resultsBadge', {
        y: "-=10",
        duration: 2,
        repeat: -1,
        yoyo: true,
        ease: "sine.inOut",
        delay: 3
    });
    
    // 4. Badge Stats Highlight Sequence
    const statHighlight = gsap.timeline({delay: 3.5, repeat: -1, repeatDelay: 5});
    
    statHighlight
        .to('#engagementStat', {
            backgroundColor: "rgba(255, 0, 0, 0.08)",
            boxShadow: "inset 0 0 0 1px rgba(255, 0, 0, 0.2)",
            duration: 0.5,
            ease: "sine.inOut"
        })
        .to('#engagementStat', {
            backgroundColor: "transparent",
            boxShadow: "none",
            duration: 0.5,
            ease: "sine.inOut",
            delay: 1
        })
        .to('#conversionStat', {
            backgroundColor: "rgba(255, 0, 0, 0.08)",
            boxShadow: "inset 0 0 0 1px rgba(255, 0, 0, 0.2)",
            duration: 0.5,
            ease: "sine.inOut",
            delay: 0.5
        })
        .to('#conversionStat', {
            backgroundColor: "transparent",
            boxShadow: "none",
            duration: 0.5,
            ease: "sine.inOut",
            delay: 1
        });
    
    // Features section animations
    const featureCards = document.querySelectorAll('.feature-card');
    
    // Set up scroll trigger for feature stat counters
    ScrollTrigger.batch('.stat-value', {
        onEnter: batch => {
            batch.forEach(element => {
                const value = element.textContent;
                let prefix = '';
                let suffix = '';
                
                // Extract prefix and suffix
                if (value.includes('%')) {
                    suffix = '%';
                } else if (value.includes('x')) {
                    suffix = 'x';
                }
                
                // Extract the numeric value
                const numericValue = parseFloat(value.replace(/[^0-9.]/g, ''));
                element.textContent = '0';
                
                // Animate the counter
                let obj = { count: 0 };
                gsap.to(obj, {
                    count: numericValue,
                    duration: 2,
                    ease: "power2.out",
                    onUpdate: function() {
                        let displayValue;
                        if (Number.isInteger(numericValue)) {
                            displayValue = Math.round(obj.count);
                        } else {
                            displayValue = obj.count.toFixed(1);
                        }
                        element.textContent = prefix + displayValue + suffix;
                    }
                });
            });
        },
        once: true
    });
    
    // Button highlight animation
    const btnTimeline = gsap.timeline({repeat: -1, repeatDelay: 3});
    
    btnTimeline
        .to('.pulse-btn', {
            boxShadow: '0 0 0 0 rgba(255, 0, 0, 0.5)',
            scale: 1,
            duration: 0.2
        })
        .to('.pulse-btn', {
            boxShadow: '0 0 0 10px rgba(255, 0, 0, 0)',
            scale: 1.05,
            duration: 0.8,
            ease: "elastic.out(1, 0.3)"
        })
        .to('.pulse-btn', {
            boxShadow: '0 0 0 0 rgba(255, 0, 0, 0)',
            scale: 1,
            duration: 0.5
        });
    
    // Animated gradient background
    gsap.to('.hero-bg-gradient', {
        backgroundPosition: '100% 100%',
        duration: 10,
        repeat: -1,
        yoyo: true,
        ease: "sine.inOut"
    });
    
    // Features CTA animation
    const featuresCta = document.querySelector('.btn-features-cta');
    if (featuresCta) {
        featuresCta.addEventListener('mouseenter', function() {
            gsap.to(this.querySelector('i'), {
                x: 5,
                duration: 0.3,
                ease: "power2.out"
            });
        });
        
        featuresCta.addEventListener('mouseleave', function() {
            gsap.to(this.querySelector('i'), {
                x: 0,
                duration: 0.3,
                ease: "power2.out"
            });
        });
    }

    // Initialize pricing buttons and modal functionality
    initPricingButtons();
});

