(function () {
  'use strict';

  var KEY = 'titanium_lgpd_cookie_notice';
  var el = document.getElementById('cookie-notice');
  if (!el) {
    return;
  }

  function accepted() {
    try {
      return window.localStorage.getItem(KEY) === '1';
    } catch (e) {
      return false;
    }
  }

  if (accepted()) {
    el.parentNode.removeChild(el);
    return;
  }

  el.hidden = false;
  el.removeAttribute('hidden');
  el.classList.add('cookie-notice--visible');

  var btn = el.querySelector('[data-cookie-accept]');
  if (btn) {
    btn.addEventListener('click', function () {
      try {
        window.localStorage.setItem(KEY, '1');
      } catch (e) {
        /* private mode */
      }
      el.classList.remove('cookie-notice--visible');
      window.setTimeout(function () {
        if (el.parentNode) {
          el.parentNode.removeChild(el);
        }
      }, 200);
    });
  }
})();
