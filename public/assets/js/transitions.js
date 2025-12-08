/**
 * Page Transitions & Smooth Interactivity
 * Handles fade-in on load and fade-out on link click.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Fade In Animation
    document.body.classList.add('page-loaded');

    // 2. Intercept Links for Fade Out
    const links = document.querySelectorAll('a:not([target="_blank"]):not([href^="#"]):not([href^="javascript"])');

    links.forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');

            // Allow default for special keys (ctrl/cmd click)
            if (e.metaKey || e.ctrlKey) return;

            e.preventDefault();

            // Add Exit Class
            document.body.classList.remove('page-loaded');
            document.body.classList.add('page-exiting');

            // Wait for animation then navigate
            setTimeout(() => {
                window.location.href = href;
            }, 300); // 300ms matches CSS transition
        });
    });
});
