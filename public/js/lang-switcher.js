(() => {
  const root = document.getElementById('langSwitch');
  if (!root) return;
  root.querySelectorAll('[data-lang]').forEach((a) => {
    a.addEventListener('click', (e) => {
      e.preventDefault();
      const lang = a.getAttribute('data-lang');
      if (!lang) return;
      const u = new URL(window.location.href);
      u.searchParams.set('lang', lang);
      window.location.assign(u.toString());
    });
  });
})();
