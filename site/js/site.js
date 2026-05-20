(function () {
  'use strict';

  var root = document.documentElement;
  var appOrigin = (root.getAttribute('data-app-origin') || '').replace(/\/$/, '');
  var devBase = (root.getAttribute('data-dev-login-base') || '').replace(/\/$/, '');

  document.querySelectorAll('[data-href-app]').forEach(function (el) {
    var path = el.getAttribute('data-href-app') || '/login';
    if (!path.startsWith('/')) {
      path = '/' + path;
    }
    var base = appOrigin || devBase;
    if (base) {
      el.setAttribute('href', base + path);
      return;
    }
    if (window.location.protocol === 'file:') {
      el.setAttribute('href', 'login.html');
      return;
    }
    el.setAttribute('href', path);
  });

  var nav = document.querySelector('[data-site-nav]');
  var toggle = document.querySelector('[data-nav-toggle]');
  if (nav && toggle) {
    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    nav.querySelectorAll('a[href^="#"]').forEach(function (a) {
      a.addEventListener('click', function () {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      });
    });
  }

  document.querySelectorAll('.lp-filter').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var cat = btn.getAttribute('data-filter') || 'all';
      document.querySelectorAll('.lp-filter').forEach(function (b) {
        b.classList.toggle('is-active', b === btn);
      });
      document.querySelectorAll('.lp-car').forEach(function (card) {
        var c = card.getAttribute('data-category') || '';
        if (cat === 'all' || cat === c) {
          card.classList.remove('is-hidden');
        } else {
          card.classList.add('is-hidden');
        }
      });
    });
  });

  var y = document.getElementById('lp-year');
  if (y) {
    y.textContent = String(new Date().getFullYear());
  }

  // Local de devolução diferente
  var sameReturn = document.querySelector('input[name="mesmo_local"]');
  var returnBox = document.getElementById('lp-return-location');
  if (sameReturn && returnBox) {
    var syncReturn = function () {
      var show = !sameReturn.checked;
      returnBox.classList.toggle('lp-return-location--visible', show);
      if (!show) {
        var input = returnBox.querySelector('input');
        if (input) {
          input.value = '';
        }
      }
    };
    sameReturn.addEventListener('change', syncReturn);
    syncReturn();
  }

  if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    var io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting) {
            e.target.classList.add('is-inview');
          }
        });
      },
      { rootMargin: '0px 0px -8% 0px', threshold: 0.08 }
    );
    document.querySelectorAll('[data-reveal]').forEach(function (el) {
      io.observe(el);
    });
  } else {
    document.querySelectorAll('[data-reveal]').forEach(function (el) {
      el.classList.add('is-inview');
    });
  }
})();
