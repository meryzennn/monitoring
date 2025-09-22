// ===== utils dasar =====
function randomHex(bytes=16){
  if (crypto?.getRandomValues){
    const a=new Uint8Array(bytes); crypto.getRandomValues(a);
    return Array.from(a,b=>b.toString(16).padStart(2,'0')).join('');
  }
  let s=''; for(let i=0;i<bytes;i++) s+=Math.floor(Math.random()*256).toString(16).padStart(2,'0'); return s;
}
function setText(id,v){ const el=document.getElementById(id); if(el) el.textContent=v; }
function sanitizeKode(k){ return (k||'').trim().toUpperCase(); }
function makeURL(base, token){
  const clean=(base||'').replace(/\/+$/,'');
  return `${clean}/ac/${token}`;   // default AC
}
// baca file -> dataURL
async function fileToDataURL(file){ return new Promise((res,rej)=>{ const r=new FileReader(); r.onload=()=>res(r.result); r.onerror=rej; r.readAsDataURL(file); }); }
// kompres dataURL biar ringan
async function compressDataURL(dataUrl, maxW=900, maxH=900, quality=0.85){
  return new Promise((res)=>{
    const img=new Image(); img.onload=()=>{
      const ratio=Math.min(maxW/img.width, maxH/img.height, 1);
      const w=Math.round(img.width*ratio), h=Math.round(img.height*ratio);
      const c=document.createElement('canvas'); c.width=w; c.height=h;
      c.getContext('2d').drawImage(img,0,0,w,h);
      const type = (dataUrl.startsWith('data:image/png')) ? 'image/png' : 'image/jpeg';
      res(c.toDataURL(type, quality));
    }; img.src=dataUrl;
  });
}

document.addEventListener('DOMContentLoaded',()=>{
  const baseInput=document.getElementById('baseUrl');
  if(!baseInput.value) baseInput.value=location.origin;

  const form=document.getElementById('formQR');
  const alertBox=document.getElementById('alertBox');
  const btnOpen=document.getElementById('btnOpen');

  // foto widgets
  const dz = document.getElementById('dzFoto');
  const fileInput = document.getElementById('fotoAc');
  const btnPick = document.getElementById('btnPick');
  const btnGanti = document.getElementById('btnGanti');
  const btnHapus = document.getElementById('btnHapus');
  const dzEmpty = document.getElementById('dzEmpty');
  const dzPreviewBox = document.getElementById('dzPreviewBox');
  const dzPreview = document.getElementById('dzPreview');

  // preview containers
  const pvPhotoBox=document.getElementById('pvPhotoBox');
  const pvImg=document.getElementById('pvImg');
  const prPhotoBox=document.getElementById('prPhotoBox');
  const prImg=document.getElementById('prImg');

  let photoDataUrl=null;

  // --- dropzone handlers ---
  btnPick.addEventListener('click', (e)=>{ e.preventDefault(); fileInput.click(); });
  btnGanti.addEventListener('click', (e)=>{ e.preventDefault(); fileInput.click(); });
  btnHapus.addEventListener('click', (e)=>{ e.preventDefault(); clearPhoto(); });

  ['dragenter','dragover'].forEach(ev=>dz.addEventListener(ev, e=>{ e.preventDefault(); dz.classList.add('dragover'); }));
  ['dragleave','drop'].forEach(ev=>dz.addEventListener(ev, e=>{ e.preventDefault(); dz.classList.remove('dragover'); }));
  dz.addEventListener('drop', async (e)=>{
    const f = e.dataTransfer?.files?.[0]; if (f) await handleFile(f);
  });
  fileInput.addEventListener('change', async (e)=>{
    const f = e.target.files?.[0]; if (f) await handleFile(f);
  });

  async function handleFile(file){
    if (!file.type.startsWith('image/')) return;
    // baca & kompres
    const raw = await fileToDataURL(file);
    photoDataUrl = await compressDataURL(raw, 1200, 1200, 0.85);
    // tampilkan di dropzone
    dzPreview.src = photoDataUrl;
    dzEmpty.classList.add('d-none');
    dzPreviewBox.classList.remove('d-none');
    // tampilkan di preview kartu & print
    pvImg.src = photoDataUrl; pvPhotoBox.classList.remove('d-none');
    prImg.src = photoDataUrl; prPhotoBox.classList.remove('d-none');
  }
  function clearPhoto(){
    photoDataUrl=null;
    fileInput.value='';
    dzPreview.removeAttribute('src');
    dzPreviewBox.classList.add('d-none');
    dzEmpty.classList.remove('d-none');
    pvImg.removeAttribute('src'); pvPhotoBox.classList.add('d-none');
    prImg.removeAttribute('src'); prPhotoBox.classList.add('d-none');
  }

  form.addEventListener('submit',e=>{
    e.preventDefault();
    if(!form.checkValidity()){ form.classList.add('was-validated'); return; }

    const fd=new FormData(form);
    const nama=(fd.get('nama')||'').toString().trim();
    const merek=(fd.get('merek')||'').toString().trim();
    const model=(fd.get('model')||'').toString().trim();
    const serial=(fd.get('serial_no')||'').toString().trim();
    const lokasi=(fd.get('lokasi')||'').toString().trim();
    const kode=sanitizeKode(fd.get('kode_qr'));
    const base=(fd.get('base')||location.origin).toString().trim();

    // token + URL /ac/{TOKEN}
    const token=randomHex(16);
    const url=makeURL(base, token);

    // preview teks
    setText('pvNama', nama||'—');
    setText('pvKode', kode||token);
    setText('pvMerek', merek||'—');
    setText('pvModelSn', `${model||'—'}${serial?` / ${serial}`:''}`);
    setText('pvLokasi', lokasi||'—');
    setText('pvUrl', url);

    // QR utama
    const box=document.getElementById('qrcode'); box.innerHTML='';
    new QRCode(box,{text:url,width:256,height:256,correctLevel:QRCode.CorrectLevel.M});
    btnOpen.href=url;

    // print
    setText('prNama', nama||'—');
    setText('prKode', kode||token);
    setText('prLokasi', lokasi||'—');
    setText('prUrl', url);
    const p=document.getElementById('printQR'); p.innerHTML='';
    new QRCode(p,{text:url,width:180,height:180,correctLevel:QRCode.CorrectLevel.M});

    alertBox.textContent='QR berhasil dibuat. Silakan unduh/cetak label.';
    alertBox.classList.remove('d-none');

    // simpan terakhir (termasuk foto)
    sessionStorage.setItem('lastQR', JSON.stringify({
      token,url,nama,merek,model,serial,lokasi,status:'normal',kode,base, photo: photoDataUrl
    }));
  });

  document.getElementById('btnDownload').addEventListener('click',()=>{
    const img=document.querySelector('#qrcode img')||document.querySelector('#qrcode canvas'); if(!img)return;
    const a=document.createElement('a'); a.href=img.src||img.toDataURL('image/png'); a.download='qr-perangkat.png'; a.click();
  });
  document.getElementById('btnPrint').addEventListener('click',()=>window.print());
  document.getElementById('btnJson').addEventListener('click',()=>{
    const raw=sessionStorage.getItem('lastQR'); if(!raw)return; const d=JSON.parse(raw);
    const payload={ token:d.token, kode_qr:d.kode||null, nama:d.nama, merek:d.merek||null, model:d.model||null,
      serial_no:d.serial||null, lokasi:d.lokasi||null, status:'normal', url:d.url, foto: d.photo || null };
    const blob=new Blob([JSON.stringify(payload,null,2)],{type:'application/json'});
    const a=document.createElement('a'); a.href=URL.createObjectURL(blob);
    a.download=(payload.kode_qr || payload.token || 'perangkat') + '.json'; a.click();
    setTimeout(()=>URL.revokeObjectURL(a.href),2000);
  });
  document.getElementById('btnReset').addEventListener('click',()=>{
    ['qrcode','printQR'].forEach(id=>document.getElementById(id).innerHTML='');
    ['pvNama','pvKode','pvMerek','pvModelSn','pvLokasi','pvUrl','prNama','prKode','prLokasi','prUrl']
      .forEach(id=>setText(id,'—'));
    document.getElementById('pvBadge').className='badge text-bg-success';
    document.getElementById('pvBadge').textContent='Normal';
    document.getElementById('alertBox').classList.add('d-none');
    btnOpen.href='#';
    clearPhoto();
    sessionStorage.removeItem('lastQR');
  });
});
