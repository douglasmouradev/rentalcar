(() => {
  const root = document.getElementById('langSwitch');
  if (!root) return;
  const base = (document.documentElement.getAttribute('data-dev-login-base') || '').trim();
  root.querySelectorAll('[data-lang]').forEach((a) => {
    a.addEventListener('click', (e) => {
      e.preventDefault();
      const lang = a.getAttribute('data-lang');
      if (!lang) return;
      if (base) {
        const u = new URL(base.replace(/\/$/, '') + '/');
        u.searchParams.set('lang', lang);
        window.location.assign(u.toString());
        return;
      }
      const u = new URL(window.location.href);
      u.searchParams.set('lang', lang);
      window.location.assign(u.toString());
    });
  });
})();
