import Alpine from "alpinejs";

window.Alpine = Alpine;

document.addEventListener("DOMContentLoaded", () => {
    const revealItems = document.querySelectorAll("[data-reveal]");
    if ("IntersectionObserver" in window) {
        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("is-visible");
                    obs.unobserve(entry.target);
                }
            });
        }, { threshold: 0.14, rootMargin: "0px 0px -40px 0px" });
        revealItems.forEach((item) => observer.observe(item));
    } else {
        revealItems.forEach((item) => item.classList.add("is-visible"));
    }
});

Alpine.start();

document.addEventListener("DOMContentLoaded", () => {
    const sections = document.querySelectorAll("main > section:not(:first-child)");

    if (!sections.length) return;

    if ("IntersectionObserver" in window) {
        const sectionObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("mli-section-visible");
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.08,
            rootMargin: "0px 0px -50px 0px",
        });

        sections.forEach((section) => sectionObserver.observe(section));
    } else {
        sections.forEach((section) => section.classList.add("mli-section-visible"));
    }
});
