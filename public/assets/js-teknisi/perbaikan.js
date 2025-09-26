(function(){
  const $ = s=>document.querySelector(s);
  const setText = (id,v)=>{ const el=document.getElementById(id); if (el) el.textContent=v; };
  function badgeClass(st){
    switch((st||'').toUpperCase()){
      case 'NORMAL': return 'text-bg-success';
      case 'MENUNGGU_PERBAIKAN': return 'text-bg-warning';
      case 'DALAM_PERBAIKAN': return 'text-bg-primary';
      default: return 'text-bg-secondary';
    }
  }
  function splitTipeModel(s){
    if(!s) return {merek:'—', model:'—'};
    const p=s.trim().split(/\s+/); if(p.length===1) return {merek:p[0], model:'—'};
    return {merek:p[0], model:p.slice(1).join(' ')};
  }
  function absUrl(path){
    if (!path) return null;
    try { if(/^https?:\/\//i.test(path)) return path; if(path.startsWith('/')) return location.origin+path; return location.origin+'/'+path.replace(/^\/+/,''); } catch { return path; }
  }

  async function fetchDetail(token){
    const res=await fetch(`${location.origin}/ac/${encodeURIComponent(token)}?format=json`, {
      headers:{'X-Requested-With':'XMLHttpRequest'}
    });
    if(!res.ok) throw new Error(await res.text()||`Gagal (${res.status})`);
    const js=await res.json(); if(!js||!js.ok||!js.ac) throw new Error('Payload invalid');
    return js.ac;
  }

  function renderHeader(ac, token){
    setText('acName', ac.nomor_unik || ac.kode_qr || 'Perangkat');
    setText('acKode', ac.kode_qr || '—');
    setText('acLokasi', ac.lokasi || '—');

    const b = document.getElementById('acStatus');
    if (b){ b.className = `badge rounded-pill ${badgeClass(ac.status_ac)}`; b.textContent = ac.status_ac || 'NORMAL'; }

    const img = document.getElementById('acThumb');
    if (ac.foto_url){
      const src=absUrl(ac.foto_url);
      img.onload=()=> img.classList.remove('d-none');
      img.onerror=()=>{};
      img.src=src;
    } else {
      img.classList.add('d-none');
    }

    const back = document.getElementById('btnBackDetail');
    if (back) back.href = `${location.origin}/ac/${encodeURIComponent(token)}`;
  }

  function wirePreview(){
    const file = document.getElementById('fotoAfter');
    const box = document.getElementById('afterPreviewBox');
    const img = document.getElementById('afterPreview');
    file?.addEventListener('change', ()=>{
      const f=file.files?.[0]; if(!f) { box.classList.add('d-none'); img.removeAttribute('src'); return; }
      const r=new FileReader();
      r.onload=()=>{ img.src=r.result; box.classList.remove('d-none'); };
      r.readAsDataURL(f);
    });
  }

  function formHasCsrf(form){
    return form.querySelector('input[name^="csrf_"]') !== null;
  }

  async function submitForm(token){
    const form = document.getElementById('formPerbaikan');
    if (!form.checkValidity()){ form.classList.add('was-validated'); return; }

    const fd = new FormData(form);
    // jika layout tidak auto-injek CSRF, pastikan ada csrf_field() di view
    if (!formHasCsrf(form)) {
      console.warn('CSRF field not found in form. Ensure <?= csrf_field() ?> is inside the form.');
    }

    const btn = form.querySelector('button[type="submit"]');
    btn?.setAttribute('disabled','disabled');

    try{
      const res = await fetch(`${location.origin}/ac/${encodeURIComponent(token)}/perbaikan`, {
        method: 'POST',
        body: fd,
        headers: { 'X-Requested-With':'XMLHttpRequest' }
      });
      let js=null; try{ js=await res.json(); }catch{}
      if (!res.ok || !js || !js.ok) {
        const msg = (js && (js.error||JSON.stringify(js.detail||{}))) || `Gagal (${res.status})`;
        throw new Error(msg);
      }
      // sukses
      document.getElementById('alertDone')?.classList.remove('d-none');
      // optional: update badge jadi NORMAL
      setText('acStatus', 'NORMAL');
      const b = document.getElementById('acStatus'); if (b) b.className = 'badge rounded-pill text-bg-success';
      // reset minimal
      form.reset();
      document.getElementById('afterPreviewBox')?.classList.add('d-none');
    }catch(err){
      alert('Gagal kirim: '+(err.message||''));
    }finally{
      btn?.removeAttribute('disabled');
    }
  }

  document.addEventListener('DOMContentLoaded', async ()=>{
    const holder = document.getElementById('__page');
    let token = holder?.dataset?.token || '';
    if (!token){
      const seg=(location.pathname||'/').split('/').filter(Boolean);
      const idx=seg.indexOf('ac'); if(idx>=0 && seg[idx+1]) token=decodeURIComponent(seg[idx+1]);
    }
    if(!token) return;

    try{
      const ac = await fetchDetail(token);
      renderHeader(ac, token);
    }catch(e){
      console.error(e);
    }

    wirePreview();

    const form = document.getElementById('formPerbaikan');
    form?.addEventListener('submit', (e)=>{ e.preventDefault(); submitForm(token); });
  });
})();
