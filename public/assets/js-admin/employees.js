// public/assets/js-admin/employees.js  (v1.1.0)
(function () {
    const modalEl = document.getElementById('empModal');
    const modal   = modalEl ? new bootstrap.Modal(modalEl) : null;
    const form    = document.getElementById('empForm');
    const titleEl = document.getElementById('empModalTitle');
    const tbody   = document.getElementById('empTbody');
    const totalEl = document.getElementById('empTotal');
    const infoEl  = document.getElementById('liveInfo');
    const pagerEl = document.getElementById('empPager');
    const qInput  = document.getElementById('qInput');
    const perSel  = document.getElementById('perPageSelect');
    let currentId = null;
    let state     = { q: (qInput?.value||''), perPage: parseInt(perSel?.value||'10',10), page: 1, pageCount: 1 };
  
    // ==== helpers ====
    function clearErrors() {
      form.querySelectorAll('.is-invalid').forEach(e => e.classList.remove('is-invalid'));
      form.querySelectorAll('[data-err]').forEach(e => e.textContent = '');
    }
    function showErrors(errs) {
      Object.entries(errs || {}).forEach(([k, v]) => {
        const i = form.querySelector('[name="' + k + '"]');
        const h = form.querySelector('[data-err="' + k + '"]');
        if (i) i.classList.add('is-invalid');
        if (h) h.textContent = v;
      });
    }
    function fillForm(d) {
      ['kode_pegawai', 'nama', 'email', 'no_telp', 'is_active'].forEach(k => {
        const el = form.querySelector('[name="' + k + '"]');
        if (el) el.value = (d && d[k] !== undefined ? d[k] : '');
      });
    }
    function debounce(fn, ms){ let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), ms); }; }
  
    // ==== Live Fetch ====
    async function fetchList() {
      const u = new URL(window.APP?.pegawaiSearch || '/pegawai/search', window.location.origin);
      u.searchParams.set('q', state.q || '');
      u.searchParams.set('perPage', String(state.perPage||10));
      u.searchParams.set('page', String(state.page||1));
  
      infoEl && (infoEl.textContent = 'Memuat…');
      try {
        const res = await fetch(u.toString(), { headers: { 'Accept':'application/json' } });
        const json = await res.json();
        if (!res.ok || !json.success) throw new Error(json.message || 'Gagal memuat data');
  
        // render rows
        const rows = json.rows || [];
        tbody.innerHTML = rows.length ? rows.map(r => `
          <tr>
            <td>${r.id}</td>
            <td><code>${escapeHtml(r.kode_pegawai)}</code></td>
            <td>${escapeHtml(r.nama)}</td>
            <td>${escapeHtml(r.email)}</td>
            <td>${escapeHtml(r.no_telp)}</td>
            <td>${r.is_active ? '<span class="badge bg-success">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>'}</td>
            <td class="text-end">
              <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-primary btn-edit" data-id="${r.id}"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-outline-danger btn-delete" data-id="${r.id}" data-url="${r.delete_url}" data-name="${escapeHtml(r.nama)}"><i class="bi bi-trash"></i></button>
              </div>
            </td>
          </tr>
        `).join('') : `<tr><td colspan="7" class="text-center text-muted">Tidak ada data.</td></tr>`;
  
        // update total & pager
        totalEl && (totalEl.textContent = json.total);
        state.page      = json.page;
        state.pageCount = json.pageCount;
        renderPager();
  
        // re-bind tombol edit/delete setelah render ulang
        bindRowActions();
        infoEl && (infoEl.textContent = (json.total ? `Menampilkan ${(rows.length?((state.page-1)*state.perPage+1):0)}–${(rows.length?((state.page-1)*state.perPage+rows.length):0)} dari ${json.total}` : ''));
      } catch (e) {
        infoEl && (infoEl.textContent = e.message || 'Gagal memuat');
      }
    }
  
    function renderPager(){
      if (!pagerEl) return;
      const p = state.page, n = state.pageCount;
      if (n <= 1) { pagerEl.innerHTML = ''; return; }
      let html = `<ul class="pagination mb-0 justify-content-end">`;
      const btn = (label, page, disabled=false, active=false) =>
        `<li class="page-item ${disabled?'disabled':''} ${active?'active':''}">
           <a class="page-link" href="#" data-page="${page}">${label}</a>
         </li>`;
      html += btn('&laquo;', Math.max(1,p-1), p===1);
      const range = pageRange(p, n, 5);
      range.forEach(pg => {
        html += btn(pg, pg, false, pg===p);
      });
      html += btn('&raquo;', Math.min(n,p+1), p===n);
      html += `</ul>`;
      pagerEl.innerHTML = html;
  
      pagerEl.querySelectorAll('a.page-link').forEach(a => a.addEventListener('click', (e)=>{
        e.preventDefault();
        const pg = parseInt(a.dataset.page,10);
        if (!isNaN(pg) && pg>=1 && pg<=state.pageCount && pg!==state.page){
          state.page = pg;
          fetchList();
        }
      }));
    }
    function pageRange(current, total, width){
      const half = Math.floor(width/2);
      let start = Math.max(1, current - half);
      let end   = Math.min(total, start + width - 1);
      start     = Math.max(1, end - width + 1);
      const arr = [];
      for (let i=start;i<=end;i++) arr.push(i);
      return arr;
    }
    function escapeHtml(s){ return (s||'').replace(/[&<>"']/g, c=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c])); }
  
    // events: ketik & perPage
    qInput && qInput.addEventListener('input', debounce(()=>{
      state.q = qInput.value || '';
      state.page = 1;
      fetchList();
    }, 250));
  
    perSel && perSel.addEventListener('change', ()=>{
      state.perPage = parseInt(perSel.value||'10',10);
      state.page = 1;
      fetchList();
    });
  
    // ==== CRUD (modal) – sama seperti sebelumnya ====
    document.getElementById('btnAdd')?.addEventListener('click', () => {
      currentId = null;
      titleEl.textContent = 'Tambah Pegawai';
      form.reset();
      form.querySelector('#_method').value = 'POST';
      clearErrors();
      modal?.show();
    });
  
    function bindRowActions(){
      // Edit
      document.querySelectorAll('.btn-edit').forEach(btn => btn.onclick = async () => {
        currentId = btn.dataset.id;
        titleEl.textContent = 'Edit Pegawai';
        form.querySelector('#_method').value = 'PUT';
        clearErrors();
        try {
          const url = (window.APP?.pegawai || '/pegawai') + '/' + currentId;
          const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
          const json = await res.json();
          if (json.csrf) window.CSRF.hash = json.csrf;
          if (!res.ok || !json.success) throw new Error(json.message || 'Gagal memuat data');
          fillForm(json.data);
          modal?.show();
        } catch (e) { alert(e.message || 'Terjadi kesalahan'); }
      });
  
      // Delete (nama)
      document.querySelectorAll('.btn-delete').forEach(btn => btn.onclick = (e) => {
        e.preventDefault();
        const url  = btn.dataset.url;
        const name = btn.dataset.name || (btn.closest('tr')?.children[2]?.textContent?.trim()) || 'pegawai';
        if (!url) return;
        if (confirm('Hapus pegawai ' + name + ' ?')) {
          const f = document.getElementById('deleteForm');
          f.setAttribute('action', url);
          f.submit();
        }
      });
    }
  
    // panggil sekali utk bind awal (server-rendered rows)
    bindRowActions();
  
    // Submit create/update
    form?.addEventListener('submit', async (e) => {
      e.preventDefault();
      clearErrors();
  
      const fd = new FormData(form);
      fd.set(window.CSRF.name, window.CSRF.hash);
  
      let url = window.APP?.pegawai || '/pegawai';
      if (form.querySelector('#_method').value === 'PUT' && currentId) {
        url = url + '/' + currentId;
        fd.set('_method', 'PUT');
      }
  
      try {
        const res  = await fetch(url, { method: 'POST', body: fd, headers: { 'Accept': 'application/json' } });
        const json = await res.json();
  
        if (json.csrf) {
          window.CSRF.hash = json.csrf;
          document.querySelectorAll('input[name="' + window.CSRF.name + '"]').forEach(i => i.value = window.CSRF.hash);
        }
  
        if (!res.ok || !json.success) {
          if (json.errors) showErrors(json.errors);
          return alert(json.message || 'Validasi gagal');
        }
  
        // refresh list via AJAX agar tetap di halaman yang sama
        modal?.hide();
        fetchList();
      } catch (err) {
        alert(err.message || 'Terjadi kesalahan');
      }
    });
  
    // initial info (opsional)
    infoEl && (infoEl.textContent = '');
  })();
  