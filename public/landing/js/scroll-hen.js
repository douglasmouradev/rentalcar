/**
 * Hen³ Portfolio — scroll intro, progresso e parallax.
 * https://webflow.com/made-in-webflow/website/hencubed
 */
(function () {
  'use strict';

  var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var intro = document.getElementById('lp-hen-intro');
  var header = document.querySelector('.lp-header');
  var progressFill = document.querySelector('.lp-hen-progress-fill');

  if (reduced) {
    if (intro) {
      intro.style.setProperty('--hen-p', '1');
    }
    return;
  }

  document.documentElement.classList.add('lp-hen-on');

  function splitTitle(el) {
    if (!el || el.dataset.henSplitDone === '1') {
      return;
    }
    var text = (el.textContent || '').trim();
    if (!text) {
      return;
    }
    el.dataset.henSplitDone = '1';
    el.setAttribute('aria-label', text);
    el.textContent = '';
    var words = text.split(/\s+/);
    words.forEach(function (word, i) {
      var span = document.createElement('span');
      span.className = 'lp-hen-word';
      span.setAttribute('aria-hidden', 'true');
      span.style.setProperty('--hen-word-i', String(i));
      span.textContent = word;
      el.appendChild(span);
      if (i < words.length - 1) {
        el.appendChild(document.createTextNode(' '));
      }
    });
  }

  document.querySelectorAll('[data-hen-split]').forEach(splitTitle);

  function updateIntro() {
    if (!intro) {
      return;
    }
    var track = intro.querySelector('.lp-hen-intro-track');
    if (!track) {
      return;
    }
    var rect = track.getBoundingClientRect();
    var scrollable = track.offsetHeight - window.innerHeight;
    var p = scrollable <= 0 ? 1 : Math.min(1, Math.max(0, -rect.top / scrollable));
    intro.style.setProperty('--hen-p', String(p));
    if (header) {
      header.classList.toggle('lp-header--over-hen', p < 0.92 && rect.bottom > 0);
    }
  }

  function updateProgress() {
    if (!progressFill) {
      return;
    }
    var max = document.documentElement.scrollHeight - window.innerHeight;
    var p = max <= 0 ? 0 : Math.min(1, window.scrollY / max);
    progressFill.style.transform = 'scaleY(' + p + ')';
  }

  function updateParallax() {
    document.querySelectorAll('[data-hen-parallax]').forEach(function (block) {
      var r = block.getBoundingClientRect();
      var center = r.top + r.height * 0.5 - window.innerHeight * 0.5;
      var shift = Math.max(-32, Math.min(32, center * -0.05));
      block.style.setProperty('--hen-parallax-y', shift + 'px');
    });
  }

  function onScroll() {
    updateIntro();
    updateProgress();
    updateParallax();
  }

  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onScroll, { passive: true });
  onScroll();
})();
