(() => {
  const cfg = window.__CAL;
  if (!cfg) return;
  const root = document.getElementById('calendarRoot');
  const loading = document.getElementById('calLoading');
  const monthInput = document.getElementById('calMonth');
  const fCar = document.getElementById('fCar');
  const fOp = document.getElementById('fOp');
  const fStatus = document.getElementById('fStatus');
  const tabs = document.getElementById('calTabs');
  let view = 'month';

  function monthBounds(ym) {
    const [y, m] = ym.split('-').map(Number);
    const start = new Date(y, m - 1, 1);
    const end = new Date(y, m, 0);
    const pad = (n) => String(n).padStart(2, '0');
    return {
      start: `${y}-${pad(m)}-01`,
      end: `${y}-${pad(m)}-${pad(end.getDate())}`,
      jsStart: start,
      firstDow: start.getDay(),
      daysInMonth: end.getDate(),
    };
  }

  async function loadEvents() {
    const ym = monthInput?.value || '';
    const { start, end } = monthBounds(ym);
    const params = new URLSearchParams({ start, end });
    if (fCar?.value) params.set('car_id', fCar.value);
    if (fOp?.value) params.set('operator_id', fOp.value);
    if (fStatus?.value) params.set('status', fStatus.value);
    if (loading) loading.style.opacity = '1';
    const res = await fetch(`${cfg.eventsUrl}?${params.toString()}`);
    const json = await res.json();
    if (loading) loading.style.opacity = '0.35';
    return json.data || [];
  }

  function eventsByDay(events, ym) {
    const map = {};
    const { daysInMonth } = monthBounds(ym);
    for (let d = 1; d <= daysInMonth; d += 1) {
      map[d] = [];
    }
    events.forEach((ev) => {
      const ps = new Date(`${ev.pickup_date}T00:00:00`);
      const rs = new Date(`${ev.return_date}T00:00:00`);
      for (let d = 1; d <= daysInMonth; d += 1) {
        const cur = new Date(monthBounds(ym).jsStart);
        cur.setDate(d);
        if (cur >= ps && cur <= rs) {
          map[d].push(ev);
        }
      }
    });
    return map;
  }

  function renderMonth(events) {
    const ym = monthInput.value;
    const { firstDow, daysInMonth } = monthBounds(ym);
    const byDay = eventsByDay(events, ym);
    const rows = [];
    rows.push('<div class="cal-month">');
    const dows = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
    dows.forEach((d) => rows.push(`<div class="cal-dow">${d}</div>`));
    for (let i = 0; i < firstDow; i += 1) rows.push('<div class="cal-cell"></div>');
    for (let day = 1; day <= daysInMonth; day += 1) {
      rows.push('<div class="cal-cell">');
      rows.push(`<div class="cal-daynum">${day}</div>`);
      (byDay[day] || []).slice(0, 3).forEach((ev) => {
        const label = `${ev.brand} ${ev.model} — ${(ev.customer_name || '').slice(0, 18)}`;
        rows.push(
          `<div class="cal-event st-${ev.status}" data-id="${ev.id}" style="border-left-color:${ev.color_hex || '#4f9eff'}">${label}</div>`,
        );
      });
      rows.push('</div>');
    }
    rows.push('</div>');
    root.innerHTML = rows.join('');
    root.querySelectorAll('.cal-event').forEach((el) => {
      el.addEventListener('click', () => {
        const id = el.getAttribute('data-id');
        if (id) window.location.href = `${window.APP_BASE_URL}/reservations/${id}`;
      });
    });
  }

  function renderWeek(events) {
    const ym = monthInput.value;
    const b = monthBounds(ym);
    const start = new Date(b.jsStart);
    const dow = start.getDay();
    start.setDate(start.getDate() - dow);
    const days = [];
    for (let i = 0; i < 7; i += 1) {
      const d = new Date(start);
      d.setDate(start.getDate() + i);
      days.push(d);
    }
    const counts = days.map((d) => {
      const key = d.toISOString().slice(0, 10);
      return events.filter((ev) => key >= ev.pickup_date && key <= ev.return_date).length;
    });
    let html = '<div class="week-grid"><div></div>';
    days.forEach((d) => {
      html += `<div class="cal-dow">${d.toLocaleDateString()}</div>`;
    });
    for (let h = 8; h <= 18; h += 1) {
      html += `<div class="week-hour">${h}h</div>`;
      for (let i = 0; i < 7; i += 1) {
        const c = counts[i] && h === 10 ? counts[i] : '';
        html += `<div class="week-cell">${c || ''}</div>`;
      }
    }
    html += '</div><p class="muted" style="margin-top:.5rem">Semana: contagem de reservas no slot 10h (grade simplificada).</p>';
    root.innerHTML = html;
  }

  function renderDay(events) {
    const ym = monthInput.value;
    const day = `${ym}-01`;
    const list = events.filter((e) => day >= e.pickup_date && day <= e.return_date);
    root.innerHTML = `<div class="day-list">${list
      .map(
        (e) => `<div class="day-item st-${e.status}"><strong>${e.code}</strong> — ${e.brand} ${e.model} — ${e.customer_name}<br>
        <span class="muted">${e.pickup_date} ${String(e.pickup_time).slice(0,5)} → ${e.return_date} ${String(e.return_time).slice(0,5)} · ${e.operator_name}</span></div>`,
      )
      .join('')}</div>`;
  }

  function renderVehicle(events) {
    const ym = monthInput.value;
    const b = monthBounds(ym);
    const start = b.jsStart.getTime();
    const end = new Date(b.jsStart.getFullYear(), b.jsStart.getMonth() + 1, 0).getTime();
    const span = end - start || 1;
    const carsMap = {};
    events.forEach((e) => {
      if (!carsMap[e.car_id]) carsMap[e.car_id] = { label: `${e.brand} ${e.model}`, plate: e.license_plate, hex: e.color_hex, items: [] };
      carsMap[e.car_id].items.push(e);
    });
    let html = '<div class="gantt">';
    Object.values(carsMap).forEach((row) => {
      html += '<div class="gantt-row"><div>';
      html += `<span class="swatch" style="background:${row.hex || '#ccc'}"></span><strong>${row.label}</strong><div class="mono muted">${row.plate}</div></div>`;
      html += '<div class="gantt-track">';
      row.items.forEach((e) => {
        const ps = new Date(`${e.pickup_date}T${String(e.pickup_time).padEnd(8, '0')}`).getTime();
        const rs = new Date(`${e.return_date}T${String(e.return_time).padEnd(8, '0')}`).getTime();
        const left = Math.max(0, ((ps - start) / span) * 100);
        const width = Math.max(2, ((rs - ps) / span) * 100);
        html += `<div class="gantt-bar st-${e.status}" style="left:${left}%;width:${width}%">${e.code}</div>`;
      });
      html += '</div></div>';
    });
    html += '</div>';
    root.innerHTML = html;
  }

  async function render() {
    const events = await loadEvents();
    if (view === 'month') renderMonth(events);
    else if (view === 'week') renderWeek(events);
    else if (view === 'day') renderDay(events);
    else renderVehicle(events);
    if (loading) loading.style.opacity = '0';
  }

  tabs?.querySelectorAll('.tab').forEach((btn) => {
    btn.addEventListener('click', () => {
      tabs.querySelectorAll('.tab').forEach((b) => b.classList.remove('active'));
      btn.classList.add('active');
      view = btn.getAttribute('data-view') || 'month';
      render();
    });
  });
  [monthInput, fCar, fOp, fStatus].forEach((el) => el?.addEventListener('change', render));

  render();
})();
