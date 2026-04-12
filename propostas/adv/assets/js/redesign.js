document.addEventListener('DOMContentLoaded', () => {
  const reveals = document.querySelectorAll('[data-reveal]');

  if (!reveals.length) {
    return;
  }

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) {
          return;
        }

        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      });
    },
    {
      threshold: 0.18,
      rootMargin: '0px 0px -48px 0px',
    },
  );

  reveals.forEach((element, index) => {
    element.style.transitionDelay = `${Math.min(index * 70, 280)}ms`;
    observer.observe(element);
  });
});
