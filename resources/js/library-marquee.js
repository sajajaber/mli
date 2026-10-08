// "The Library" marquee.
//
// - Shows that fit on screen (set width <= visible width): static, centred, no motion.
// - Shows that overflow: seamless infinite loop. The track holds two identical sets and
//   slides by exactly one set width, so the second set is always in view where the first
//   one leaves. This only works when one set is at least as wide as the viewport, which is
//   exactly the case this code switches the loop on for, so no empty space can appear.

const SPEED_PX_PER_SECOND = 55;
const MIN_DURATION_SECONDS = 12;

function setupLibraryMarquee(marquee) {
    const viewport = marquee.querySelector(".mli-library-marquee__viewport");
    const track = marquee.querySelector(".mli-library-marquee__track");
    const items = Array.from(
        marquee.querySelectorAll(
            ".mli-library-marquee__item:not([data-marquee-clone])",
        ),
    );

    if (!viewport || !track || items.length === 0) return;

    const update = () => {
        // Width of ONE set, measured on the originals only. Differences of bounding rects
        // are unaffected by the running animation's translateX. The marquee is forced to
        // direction: ltr in CSS, so left/right maths is the same for English and Arabic.
        const first = items[0].getBoundingClientRect();
        const last = items[items.length - 1].getBoundingClientRect();
        const setWidth = last.right - first.left;
        const visibleWidth = viewport.clientWidth;

        if (setWidth <= 0 || visibleWidth <= 0) return;

        if (setWidth - visibleWidth <= 0.5) {
            marquee.dataset.marquee = "static";
            marquee.style.removeProperty("--marquee-shift");
            marquee.style.removeProperty("--marquee-duration");
            return;
        }

        marquee.style.setProperty("--marquee-shift", `${setWidth}px`);
        marquee.style.setProperty(
            "--marquee-duration",
            `${Math.max(setWidth / SPEED_PX_PER_SECOND, MIN_DURATION_SECONDS)}s`,
        );
        marquee.dataset.marquee = "loop";
    };

    update();

    if ("ResizeObserver" in window) {
        const observer = new ResizeObserver(update);
        observer.observe(viewport);
        items.forEach((item) => observer.observe(item));
    } else {
        window.addEventListener("resize", update);
    }

    window.addEventListener("load", update);

    if (document.fonts && document.fonts.ready) {
        document.fonts.ready.then(update);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    document
        .querySelectorAll("[data-library-marquee]")
        .forEach(setupLibraryMarquee);
});
