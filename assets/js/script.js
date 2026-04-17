/**
 * Enamak Makina - Ana Frontend JS
 */
(function() {
    'use strict';

    // === Mobil Menü ===
    window.toggleMobileMenu = function() {
        const menu = document.getElementById('mobileMenu');
        const toggle = document.querySelector('.mobile-toggle');
        if (menu) {
            menu.classList.toggle('active');
            document.body.classList.toggle('no-scroll');
            if (toggle) toggle.setAttribute('aria-expanded', menu.classList.contains('active'));
        }
    };

    // === Header scroll efekti ===
    const header = document.querySelector('.site-header');
    if (header) {
        let lastScroll = 0;
        window.addEventListener('scroll', function() {
            const y = window.scrollY || document.documentElement.scrollTop;
            if (y > 40) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
            lastScroll = y;
        }, { passive: true });
    }

    // === Fade-in animasyonu (IntersectionObserver) ===
    const faders = document.querySelectorAll('.fade-in');
    if (faders.length && 'IntersectionObserver' in window) {
        const obs = new IntersectionObserver(function(entries) {
            entries.forEach(function(en) {
                if (en.isIntersecting) {
                    en.target.classList.add('visible');
                    obs.unobserve(en.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -50px 0px' });

        faders.forEach(function(el) { obs.observe(el); });
    } else {
        faders.forEach(function(el) { el.classList.add('visible'); });
    }

    // === Cookie banner ===
    window.kabulEtCookie = function() {
        try {
            document.cookie = 'enamak_cookie_kabul=1; path=/; max-age=31536000; SameSite=Lax';
        } catch (e) {}
        const banner = document.getElementById('cookieBanner');
        if (banner) banner.style.display = 'none';
    };

    // Gösterip gizleme
    (function() {
        if (document.cookie.indexOf('enamak_cookie_kabul=1') === -1) {
            const banner = document.getElementById('cookieBanner');
            if (banner) setTimeout(function() { banner.classList.add('show'); }, 1200);
        }
    })();

    // === Slider ===
    const slides = document.querySelectorAll('.hero-slider .slide');
    const dots = document.querySelectorAll('.slider-dots .slider-dot');
    let sliderIndex = 0;
    let sliderInterval = null;

    function goTo(i) {
        if (!slides.length) return;
        slides[sliderIndex].classList.remove('active');
        if (dots[sliderIndex]) dots[sliderIndex].classList.remove('active');
        sliderIndex = (i + slides.length) % slides.length;
        slides[sliderIndex].classList.add('active');
        if (dots[sliderIndex]) dots[sliderIndex].classList.add('active');
    }

    window.sliderNext = function() { goTo(sliderIndex + 1); resetAuto(); };
    window.sliderPrev = function() { goTo(sliderIndex - 1); resetAuto(); };
    window.sliderGo = function(i) { goTo(i); resetAuto(); };

    function resetAuto() {
        if (sliderInterval) clearInterval(sliderInterval);
        startAuto();
    }
    function startAuto() {
        if (slides.length > 1) {
            sliderInterval = setInterval(function() { goTo(sliderIndex + 1); }, 6000);
        }
    }
    if (slides.length > 1) startAuto();

    // Touch swipe
    if (slides.length > 1) {
        let touchStartX = 0;
        const wrap = document.querySelector('.hero-slider');
        if (wrap) {
            wrap.addEventListener('touchstart', function(e) { touchStartX = e.changedTouches[0].screenX; }, { passive: true });
            wrap.addEventListener('touchend', function(e) {
                const dx = e.changedTouches[0].screenX - touchStartX;
                if (Math.abs(dx) > 50) {
                    if (dx < 0) window.sliderNext();
                    else window.sliderPrev();
                }
            }, { passive: true });
        }
    }

    // === Form validation (basit) ===
    document.querySelectorAll('form.form-wrap').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const req = form.querySelectorAll('[required]');
            let ok = true;
            req.forEach(function(el) {
                if (!el.value || (el.type === 'checkbox' && !el.checked)) {
                    el.classList.add('error');
                    ok = false;
                } else {
                    el.classList.remove('error');
                }
            });
            if (!ok) {
                e.preventDefault();
                alert('Lütfen zorunlu alanları doldurun.');
            }
        });
    });

    // === Smooth scroll ===
    document.querySelectorAll('a[href^="#"]').forEach(function(a) {
        a.addEventListener('click', function(e) {
            const href = a.getAttribute('href');
            if (href.length > 1) {
                const el = document.querySelector(href);
                if (el) {
                    e.preventDefault();
                    el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });

    // === Counter animation (stats) ===
    const counters = document.querySelectorAll('[data-count]');
    if (counters.length && 'IntersectionObserver' in window) {
        const cobs = new IntersectionObserver(function(entries) {
            entries.forEach(function(en) {
                if (en.isIntersecting) {
                    const el = en.target;
                    const target = parseInt(el.getAttribute('data-count'), 10);
                    const suffix = (el.textContent.match(/[^0-9]+$/) || [''])[0];
                    let cur = 0;
                    const step = Math.max(1, Math.floor(target / 50));
                    const tick = setInterval(function() {
                        cur += step;
                        if (cur >= target) {
                            el.textContent = target + suffix;
                            clearInterval(tick);
                        } else {
                            el.textContent = cur + suffix;
                        }
                    }, 30);
                    cobs.unobserve(el);
                }
            });
        }, { threshold: 0.4 });
        counters.forEach(function(c) { cobs.observe(c); });
    }

})();
