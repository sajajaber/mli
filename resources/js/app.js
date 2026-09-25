import Alpine from "alpinejs";

window.Alpine = Alpine;

const motionQuery = window.matchMedia("(prefers-reduced-motion: reduce)");
const reduceMotion = motionQuery.matches;

document.documentElement.classList.add("mli-motion-ready");

document.addEventListener("DOMContentLoaded", () => {
    const root = document.documentElement;
    const isPublicHome = document.body.classList.contains("home-page-body");

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

    const revealItems = document.querySelectorAll("[data-reveal]");

    const counters = document.querySelectorAll("[data-counter]");

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

    counters.forEach((counter) => animateCounter(counter));

    if (isPublicHome && !reduceMotion) {
        root.classList.add("mli-home-motion-enabled");

        const sections = Array.from(
            document.querySelectorAll("main > section:not(:first-child)")
        );

        sections.forEach((section, index) => {
            section.style.setProperty(
                "--home-section-delay",
                `${Math.min(index, 4) * 70}ms`
            );
            section.classList.add("mli-home-scroll-target");
        });

        const revealObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    entry.target.classList.toggle("is-visible", entry.isIntersecting);
                });
            },
            {
                threshold: 0.12,
                rootMargin: "0px 0px -8% 0px",
            }
        );

        revealItems.forEach((item) => revealObserver.observe(item));

        const sectionObserver = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    entry.target.classList.toggle(
                        "mli-section-visible",
                        entry.isIntersecting
                    );
                });
            },
            {
                threshold: 0.10,
                rootMargin: "0px 0px -8% 0px",
            }
        );

        sections.forEach((section) => sectionObserver.observe(section));

        const depthTargets = [
            ...document.querySelectorAll("[data-depth]"),
            ...document.querySelectorAll(
                ".mli-opening__copy, .mli-opening__feature"
            ),
        ];

        let scrollFrame = null;

        const updateScrollMotion = () => {
            scrollFrame = null;

            const viewportCenter = window.innerHeight * 0.5;

            sections.forEach((section) => {
                const rect = section.getBoundingClientRect();
                const sectionCenter = rect.top + rect.height * 0.5;
                const distance = Math.max(
                    -1,
                    Math.min(1, (sectionCenter - viewportCenter) / window.innerHeight)
                );
                const shift = distance * -10;

                section.style.setProperty(
                    "--mli-scroll-shift",
                    `${shift.toFixed(2)}px`
                );
            });

            depthTargets.forEach((element) => {
                const rect = element.getBoundingClientRect();
                const center = rect.top + rect.height * 0.5;
                const normalized = Math.max(
                    -1,
                    Math.min(1, (center - viewportCenter) / window.innerHeight)
                );

                const depth = Number(element.dataset.depth || 0.06);
                const shift = normalized * depth * -95;

                if (element.matches(".mli-opening__copy")) {
                    element.style.setProperty(
                        "--mli-copy-scroll-y",
                        `${shift.toFixed(2)}px`
                    );
                } else if (element.matches(".mli-opening__feature")) {
                    element.style.setProperty(
                        "--mli-feature-scroll-y",
                        `${shift.toFixed(2)}px`
                    );
                } else {
                    element.style.setProperty(
                        "--mli-depth-y",
                        `${shift.toFixed(2)}px`
                    );
                }
            });
        };

        const requestScrollFrame = () => {
            if (scrollFrame === null) {
                scrollFrame = requestAnimationFrame(updateScrollMotion);
            }
        };

        window.addEventListener("scroll", requestScrollFrame, { passive: true });
        window.addEventListener("resize", requestScrollFrame, { passive: true });
        requestScrollFrame();
    } else {
        revealItems.forEach((item) => item.classList.add("is-visible"));
    }

    if (!reduceMotion) {
        document.querySelectorAll("img").forEach((image) => {
            const parent = image.parentElement;

            if (!parent || parent.classList.contains("mli-image-reveal-host")) {
                return;
            }

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
