        document.addEventListener('DOMContentLoaded', function() {
            const track = document.getElementById('testimonial-track');
            const cards = document.querySelectorAll('.testimonial-card');
            const prevBtn = document.getElementById('prev');
            const nextBtn = document.getElementById('next');
            const dots = document.querySelectorAll('.indicator-dot');
            
            let currentIndex = 0;
            const cardWidth = cards[0].offsetWidth;
            const visibleCards = window.innerWidth >= 1024 ? 3 : window.innerWidth >= 768 ? 2 : 1;
            const totalCards = cards.length;
            let autoSlideInterval;

            // Initialize carousel
            function updateCarousel() {
                const offset = -currentIndex * cardWidth;
                track.style.transform = `translateX(${offset}px)`;
                
                // Update active dot
                dots.forEach((dot, index) => {
                    dot.classList.toggle('active-dot', index === currentIndex);
                });
                
                // Animate cards
                cards.forEach(card => {
                    card.classList.remove('animate-fade');
                    void card.offsetWidth; // Trigger reflow
                    card.classList.add('animate-fade');
                });
            }

            // Next slide
            function nextSlide() {
                currentIndex = (currentIndex + 1) % (totalCards - visibleCards + 1);
                updateCarousel();
            }

            // Previous slide
            function prevSlide() {
                currentIndex = (currentIndex - 1 + (totalCards - visibleCards + 1)) % (totalCards - visibleCards + 1);
                updateCarousel();
            }

            // Auto slide
            function startAutoSlide() {
                autoSlideInterval = setInterval(prevSlide, 15000);
            }

            // Event listeners
            nextBtn.addEventListener('click', () => {
                nextSlide();
                resetAutoSlide();
            });

            prevBtn.addEventListener('click', () => {
                prevSlide();
                resetAutoSlide();
            });

            dots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    currentIndex = index;
                    updateCarousel();
                    resetAutoSlide();
                });
            });

            // Reset auto slide timer
            function resetAutoSlide() {
                clearInterval(autoSlideInterval);
                startAutoSlide();
            }

            // Pause on hover
            const carousel = document.getElementById('carousel');
            carousel.addEventListener('mouseenter', () => {
                clearInterval(autoSlideInterval);
            });

            carousel.addEventListener('mouseleave', startAutoSlide);

            // Responsive adjustments
            window.addEventListener('resize', () => {
                const newVisibleCards = window.innerWidth >= 1024 ? 3 : window.innerWidth >= 768 ? 2 : 1;
                if (newVisibleCards !== visibleCards) {
                    currentIndex = 0;
                    updateCarousel();
                }
            });

            // Initialize
            updateCarousel();
            startAutoSlide();
        });