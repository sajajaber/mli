import Alpine from "alpinejs";

window.Alpine = Alpine;

const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

document.documentElement.classList.add("mli-motion-ready");

document.addEventListener("DOMContentLoaded", () => {
    const root = document.documentElement;
    const body = document.body;
    const isPublicHome = Boolean(document.querySelector(".home-page-body"));

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

    // Scroll-triggered reveals are intentionally disabled on the public homepage.\n    const revealItems = document.querySelectorAll("[data-reveal]");\n    revealItems.forEach((item) => item.classList.add("is-visible"));\n\n    const counters = document.querySelectorAll("[data-counter]");

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

    // Counters are no longer triggered by scrolling; they animate on page load.\n    counters.forEach((counter) => animateCounter(counter));\n\n create a more intentional "uncover" instead of a generic fade.
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
