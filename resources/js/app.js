import Alpine from "alpinejs";

window.Alpine = Alpine;

const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

document.documentElement.classList.add("mli-motion-ready");

document.addEventListener("DOMContentLoaded", () => {
    const root = document.documentElement;
    const body = document.body;
    const isPublicHome = Boolean(document.querySelector(".mli-opening"));

    if (isPublicHome) {
        root.classList.add("mli-public-home");
    }

    if (reduceMotion) {
        root.classList.add("mli-reduced-motion");
    } else {
        requestAnimationFrame(() => {
            root.classList.add("mli-page-entered");
        });
    }

    const revealItems = isPublicHome ? [] : document.querySelectorAll("[data-reveal]");

    revealItems.forEach((item, index) => {
        if (!item.style.getPropertyValue("--reveal-delay")) {
            item.style.setProperty("--reveal-delay", `${Math.min(index % 8, 7) * 65}ms`);
        }
    });

    if ("IntersectionObserver" in window && !reduceMotion) {
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                entry.target.classList.toggle("is-visible", entry.isIntersecting);
            });
        }, {
            threshold: 0.08,
            rootMargin: "0px 0px -70px 0px",
        });

        revealItems.forEach((item) => revealObserver.observe(item));
    } else {
        revealItems.forEach((item) => item.classList.add("is-visible"));
    }

    const counters = isPublicHome ? [] : document.querySelectorAll("[data-counter]");

    const animateCounter = (counter) => {
        const target = Number(counter.dataset.counter || 0);

        if (!Number.isFinite(target)) {
            counter.textContent = "0";
            return;
        }

        if (reduceMotion) {
            counter.textContent = Math.round(target).toLocaleString("en-US");
            return;
        }

        const duration = 1400;
        const startTime = performance.now();

        counter.textContent = "0";

        const tick = (now) => {
            const progress = Math.min((now - startTime) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 4);
            const value = Math.round(target * eased);

            counter.textContent = value.toLocaleString("en-US");

            if (progress < 1) {
                requestAnimationFrame(tick);
            }
        };

        requestAnimationFrame(tick);
    };

    if ("IntersectionObserver" in window && !reduceMotion) {
        const counterObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;

                animateCounter(entry.target);
                observer.unobserve(entry.target);
            });
        }, {
            threshold: 0.6,
        });

        counters.forEach((counter) => {
            counter.textContent = "0";
            counterObserver.observe(counter);
        });
    } else {
        counters.forEach((counter) => animateCounter(counter));
    }

    const sections = isPublicHome ? [] : document.querySelectorAll("main > section");

    if ("IntersectionObserver" in window && !reduceMotion) {
        const sectionObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                entry.target.classList.toggle("mli-section-visible", entry.isIntersecting);
            });
        }, {
            threshold: 0.05,
            rootMargin: "0px 0px -40px 0px",
        });

        sections.forEach((section) => sectionObserver.observe(section));
    } else {
        sections.forEach((section) => section.classList.add("mli-section-visible"));
    }

    if (!reduceMotion && !isPublicHome) {
        let ticking = false;

        const updateScrollMotion = () => {
            const scrollY = window.scrollY || window.pageYOffset || 0;
            const viewportHeight = window.innerHeight || 1;
            const documentHeight = Math.max(document.documentElement.scrollHeight - viewportHeight, 1);
            const progress = Math.min(Math.max(scrollY / documentHeight, 0), 1);

            root.style.setProperty("--mli-scroll-progress", progress.toFixed(4));

            document.querySelectorAll("[data-depth]").forEach((element) => {
                const depth = Number(element.dataset.depth || 0.08);
                const rect = element.getBoundingClientRect();
                const center = rect.top + rect.height / 2;
                const distance = (center - viewportHeight / 2) / viewportHeight;
                const translate = Math.max(-18, Math.min(18, distance * depth * -42));

                element.style.setProperty("--mli-depth-y", `${translate.toFixed(2)}px`);
            });

            ticking = false;
        };

        const requestScrollMotion = () => {
            if (ticking) return;
            ticking = true;
            requestAnimationFrame(updateScrollMotion);
        };

        updateScrollMotion();
        window.addEventListener("scroll", requestScrollMotion, { passive: true });
        window.addEventListener("resize", requestScrollMotion, { passive: true });
    }

    // Image masks create a more intentional "uncover" instead of a generic fade.
    if (!reduceMotion) {
        document.querySelectorAll("img").forEach((image) => {
            const parent = image.parentElement;
            if (!parent || parent.classList.contains("mli-image-reveal-host")) return;

            parent.classList.add("mli-image-reveal-host");

            const revealImage = () => {
                parent.classList.add("mli-image-revealed");
            };

            if (image.complete) {
                requestAnimationFrame(revealImage);
            } else {
                image.addEventListener("load", revealImage, { once: true });
                image.addEventListener("error", revealImage, { once: true });
            }
        });
    }
});

Alpine.start();
