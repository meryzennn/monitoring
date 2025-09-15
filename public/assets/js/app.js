(() => {
  const BASE_URL = document.querySelector('meta[name="base-url"]').getAttribute('content');

  // ===== Notifikasi (SSE + fallback polling) =====
  const notifCountEl = document.getElementById('notifCount');
  const notifListEl  = document.getElementById('notifList');

  function renderNotif(count, items) {
    // Badge
    if (count > 0) {
      notifCountEl.classList.remove('d-none');
      notifCountEl.textContent = count;
    } else {
      notifCountEl.classList.add('d-none');
    }

    // List
    notifListEl.innerHTML = '';
    const header = document.createElement('li');
    header.className = 'dropdown-header';
    header.textContent = 'Laporan Masuk';
    notifListEl.appendChild(header);

    const hr = document.createElement('li');
    hr.innerHTML = '<hr class="dropdown-divider">';
    notifListEl.appendChild(hr);

    if (!items || items.length === 0) {
      const empty = document.createElement('li');
      empty.className = 'px-3 text-muted small';
      empty.textContent = 'Tidak ada laporan baru.';
      notifListEl.appendChild(empty);
    } else {
      items.forEach(it => {
        const li = document.createElement('li');
        li.innerHTML = `
          <a class="dropdown-item d-flex flex-column" href="${BASE_URL}laporan/${it.id ?? ''}">
            <span class="fw-semibold">${(it.judul || 'Laporan Baru')}</span>
            <span class="text-muted small">${it.waktu || it.created_at || ''} — ${it.status || ''}</span>
          </a>`;
        notifListEl.appendChild(li);
      });
    }
  }

  async function fetchLatest() {
    try {
      const res = await fetch(`${BASE_URL}notifications/latest`);
      const data = await res.json();
      renderNotif(data.count || 0, data.items || []);
    } catch (e) {
      console.error('notif latest error', e);
    }
  }

  function startPolling() {
    fetchLatest();
    setInterval(fetchLatest, 15000);
  }

  function startSSE() {
    if (!window.EventSource) {
      startPolling();
      return;
    }
    const es = new EventSource(`${BASE_URL}notifications/stream`);
    es.addEventListener('laporan', (ev) => {
      try {
        const data = JSON.parse(ev.data);
        renderNotif(data.count || 0, data.items || []);
      } catch (e) {
        console.error('SSE parse error', e);
      }
    });
    es.onerror = () => {
      // fallback ke polling jika koneksi bermasalah
      es.close();
      startPolling();
    };
  }

  // init on load
  document.addEventListener('DOMContentLoaded', () => {
    startSSE();
  });

})();
