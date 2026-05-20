(() => {
  const cfg = window.__RES_FORM;
  if (!cfg) return;

  const form = document.getElementById('resForm');
  if (!form) return;

  const carSel = document.getElementById('car_id');
  const daily = document.getElementById('daily_rate');
  const discount = document.getElementById('discount');
  const pickupD = document.getElementById('pickup_date');
  const returnD = document.getElementById('return_date');
  const pickupT = document.getElementById('pickup_time');
  const returnT = document.getElementById('return_time');
  const totalEl = document.getElementById('total_preview');
  const conflictEl = document.getElementById('conflict_msg');
  const preview = document.getElementById('carPreview');
  const custSearch = document.getElementById('custSearch');
  const custSuggest = document.getElementById('custSuggest');
  const custSel = document.getElementById('customer_id');
  const modal = document.getElementById('quickCustModal');

  const csrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  function fmtBrl(n) {
    return n.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
  }

  function daysInclusive(a, b) {
    const d1 = new Date(a + 'T00:00:00');
    const d2 = new Date(b + 'T00:00:00');
    if (Number.isNaN(d1.getTime()) || Number.isNaN(d2.getTime())) return 1;
    const diff = Math.round((d2 - d1) / 86400000);
    return Math.max(1, diff + 1);
  }

  function recalc() {
    const rate = parseFloat(daily?.value || '0') || 0;
    const disc = parseFloat(discount?.value || '0') || 0;
    const days = daysInclusive(pickupD?.value || '', returnD?.value || '');
    const total = Math.max(0, rate * days - disc);
    if (totalEl) totalEl.textContent = fmtBrl(total);
  }

  function syncCar() {
    const opt = carSel?.selectedOptions?.[0];
    if (!opt || !daily) return;
    const rate = opt.getAttribute('data-rate');
    if (rate && !form.dataset.rateTouched) {
      daily.value = rate;
    }
    if (preview) {
      preview.textContent = opt.getAttribute('data-label') || '';
    }
    recalc();
    scheduleConflict();
  }

  let tmr;
  function scheduleConflict() {
    clearTimeout(tmr);
    tmr = setTimeout(checkConflict, 350);
  }

  async function checkConflict() {
    if (!cfg.conflictUrl || !carSel || !pickupD || !returnD || !pickupT || !returnT) return;
    const params = new URLSearchParams({
      car_id: carSel.value,
      pickup_date: pickupD.value,
      pickup_time: pickupT.value,
      return_date: returnD.value,
      return_time: returnT.value,
    });
    if (cfg.excludeId) params.set('exclude_id', String(cfg.excludeId));
    try {
      const res = await fetch(`${cfg.conflictUrl}?${params.toString()}`, { headers: { Accept: 'application/json' } });
      const data = await res.json();
      if (!conflictEl) return;
      if (data.conflict) {
        conflictEl.textContent = cfg.conflictText || 'Conflito';
        conflictEl.classList.remove('hidden');
      } else {
        conflictEl.classList.add('hidden');
      }
    } catch {
      /* ignore */
    }
  }

  carSel?.addEventListener('change', () => {
    form.dataset.rateTouched = '';
    syncCar();
  });
  daily?.addEventListener('input', () => {
    form.dataset.rateTouched = '1';
    recalc();
    scheduleConflict();
  });
  discount?.addEventListener('input', recalc);
  [pickupD, returnD, pickupT, returnT].forEach((el) => el?.addEventListener('change', () => {
    if (pickupD && returnD && pickupD.value && returnD.value && returnD.value < pickupD.value) {
      returnD.value = pickupD.value;
    }
    recalc();
    scheduleConflict();
  }));

  let stmr;
  custSearch?.addEventListener('input', () => {
    clearTimeout(stmr);
    stmr = setTimeout(async () => {
      const q = custSearch.value.trim();
      if (!cfg.searchUrl || q.length < 2) {
        custSuggest.style.display = 'none';
        return;
      }
      const res = await fetch(`${cfg.searchUrl}?q=${encodeURIComponent(q)}`);
      const json = await res.json();
      custSuggest.innerHTML = '';
      (json.data || []).forEach((c) => {
        const div = document.createElement('div');
        div.className = 'suggest-item';
        div.textContent = `${c.full_name} — ${c.document}`;
        div.addEventListener('click', () => {
          custSel.innerHTML = '';
          const opt = document.createElement('option');
          opt.value = c.id;
          opt.textContent = `${c.full_name} — ${c.document}`;
          opt.selected = true;
          custSel.appendChild(opt);
          custSuggest.style.display = 'none';
          custSearch.value = '';
        });
        custSuggest.appendChild(div);
      });
      custSuggest.style.display = json.data?.length ? 'block' : 'none';
    }, 250);
  });

  document.getElementById('openQuickCust')?.addEventListener('click', () => modal?.classList.remove('hidden'));
  document.getElementById('qc_close')?.addEventListener('click', () => modal?.classList.add('hidden'));
  document.getElementById('qc_save')?.addEventListener('click', async () => {
    const body = {
      _csrf: csrf(),
      type: 'individual',
      full_name: document.getElementById('qc_name')?.value || '',
      document: document.getElementById('qc_doc')?.value || '',
      phone: document.getElementById('qc_phone')?.value || '',
      email: document.getElementById('qc_email')?.value || '',
    };
    const res = await fetch(cfg.quickUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
      body: JSON.stringify(body),
    });
    const json = await res.json();
    if (!json.ok) return;
    const c = json.customer;
    custSel.innerHTML = '';
    const opt = document.createElement('option');
    opt.value = c.id;
    opt.textContent = `${c.full_name} — ${c.document}`;
    opt.selected = true;
    custSel.appendChild(opt);
    modal?.classList.add('hidden');
  });

  form?.addEventListener('submit', (e) => {
    if (!custSel?.value || custSel.value === '') {
      e.preventDefault();
      custSearch?.focus();
    }
  });

  syncCar();
  recalc();
  checkConflict();
})();
