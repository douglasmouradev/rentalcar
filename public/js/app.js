(() => {
  const shell = document.getElementById('appShell');
  const sb = document.getElementById('sidebar');
  const toggle = document.getElementById('sidebarToggle');
  const bd = document.getElementById('sidebarBackdrop');

  const syncSidebar = () => {
    if (!shell || !sb) return;
    const open = sb.classList.contains('open');
    shell.classList.toggle('sidebar-open', open);
    if (bd) bd.setAttribute('aria-hidden', open ? 'false' : 'true');
  };

  if (sb && toggle) {
    toggle.addEventListener('click', () => {
      sb.classList.toggle('open');
      syncSidebar();
    });
  }

  if (bd && sb) {
    bd.addEventListener('click', () => {
      sb.classList.remove('open');
      syncSidebar();
    });
  }

  window.addEventListener('resize', () => {
    if (window.matchMedia('(min-width: 961px)').matches && sb) {
      sb.classList.remove('open');
      syncSidebar();
    }
  });

  const path = window.location.pathname.replace(/\/$/, '') || '/';
  let basePath = '';
  try {
    const bu = typeof window.APP_BASE_URL === 'string' ? window.APP_BASE_URL : '';
    if (bu) basePath = new URL(bu, window.location.origin).pathname.replace(/\/$/, '');
  } catch (_) {
    /* ignore */
  }
  let rel = path;
  if (basePath && path.startsWith(basePath)) {
    rel = path.slice(basePath.length) || '/';
  }

  const navLinks = [...document.querySelectorAll('.sidebar .nav-link')];
  const candidates = [];
  navLinks.forEach((a) => {
    try {
      const u = new URL(a.getAttribute('href') || '', window.location.origin);
      let p = u.pathname.replace(/\/$/, '') || '/';
      if (basePath && p.startsWith(basePath)) {
        p = p.slice(basePath.length) || '/';
      }
      if (rel === p || (p !== '/' && rel.startsWith(`${p}/`))) {
        candidates.push({ a, len: p.length });
      }
    } catch (_) {
      /* ignore */
    }
  });
  candidates.sort((x, y) => y.len - x.len);
  if (candidates[0]) {
    candidates[0].a.classList.add('active');
  }

  document.querySelectorAll('.toast').forEach((t) => {
    setTimeout(() => {
      t.style.opacity = '0';
      t.style.transition = 'opacity .4s ease';
      setTimeout(() => t.remove(), 450);
    }, 4500);
  });
})();
