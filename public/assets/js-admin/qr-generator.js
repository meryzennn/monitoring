function randomHex(bytes=16){
  if (crypto?.getRandomValues){ const a=new Uint8Array(bytes); crypto.getRandomValues(a);
    return Array.from(a,b=>b.toString(16).padStart(2,'0')).join(''); }
  let s=''; for(let i=0;i<bytes;i++) s+=Math.floor(Math.random()*256).toString(16).padStart(2,'0'); return s;
}
function badgeClass(s){ return ({normal:'success',rusak:'danger',maintenance:'warning',diproses:'info'}[(s||'').toLowerCase()]||'secondary'); }
function setText(id,v){ const el=document.getElementById(id); if(el) el.textContent=v; }
function sanitizeKode(k){ return (k||'').trim().toUpperCase(); }
function makeURL(base,mode,token){ const clean=(base||'').replace(/\/+$/,''); return mode==='teknisi' ? `${clean}/teknisi/?t=${token}` : `${clean}/p/${token}`; }

document.addEventListener('DOMContentLoaded',()=>{
  const baseInput=document.getElementById('baseUrl'); if(!baseInput.value) baseInput.value=location.origin;
  const form=document.getElementById('formQR'); const alertBox=document.getElementById('alertBox');

  form.addEventListener('submit',e=>{
    e.preventDefault(); if(!form.checkValidity()){ form.classList.add('was-validated'); return; }

    const fd=new FormData(form);
    const nama=(fd.get('nama')||'').toString().trim();
    const merek=(fd.get('merek')||'').toString().trim();
    const model=(fd.get('model')||'').toString().trim();
    const serial=(fd.get('serial_no')||'').toString().trim();
    const lokasi=(fd.get('lokasi')||'').toString().trim();
    const status=(fd.get('status')||'normal').toString();
    const kode=sanitizeKode(fd.get('kode_qr'));
    const mode=(fd.get('mode')||'p').toString();
    const base=(fd.get('base')||location.origin).toString().trim();

    const token=randomHex(16);
    const url=makeURL(base,mode,token);

    setText('pvNama', nama||'—');
    setText('pvKode', kode||token);
    setText('pvMerek', merek||'—');
    setText('pvModelSn', `${model||'—'}${serial?` / ${serial}`:''}`);
    setText('pvLokasi', lokasi||'—');
    setText('pvUrl', url);

    const b=document.getElementById('pvBadge'); b.className=`badge text-bg-${badgeClass(status)}`; b.textContent=status.charAt(0).toUpperCase()+status.slice(1);

    const box=document.getElementById('qrcode'); box.innerHTML='';
    new QRCode(box,{text:url,width:256,height:256,correctLevel:QRCode.CorrectLevel.M});

    setText('prNama', nama||'—'); setText('prKode', kode||token); setText('prLokasi', lokasi||'—'); setText('prUrl', url);
    const p=document.getElementById('printQR'); p.innerHTML='';
    new QRCode(p,{text:url,width:180,height:180,correctLevel:QRCode.CorrectLevel.M});

    alertBox.textContent='QR berhasil dibuat. Silakan unduh/cetak label.'; alertBox.classList.remove('d-none');

    sessionStorage.setItem('lastQR', JSON.stringify({token,url,nama,merek,model,serial,lokasi,status,kode,mode,base}));
  });

  document.getElementById('btnCopy').addEventListener('click', async ()=>{
    const url=document.getElementById('pvUrl').textContent||''; if(!url) return; try{ await navigator.clipboard.writeText(url);}catch{}
  });
  document.getElementById('btnDownload').addEventListener('click',()=>{
    const img=document.querySelector('#qrcode img')||document.querySelector('#qrcode canvas'); if(!img)return;
    const a=document.createElement('a'); a.href=img.src||img.toDataURL('image/png'); a.download='qr-perangkat.png'; a.click();
  });
  document.getElementById('btnPrint').addEventListener('click',()=>window.print());
  document.getElementById('btnJson').addEventListener('click',()=>{
    const raw=sessionStorage.getItem('lastQR'); if(!raw)return; const d=JSON.parse(raw);
    const payload={token:d.token,kode_qr:d.kode||null,nama:d.nama,merek:d.merek||null,model:d.model||null,serial_no:d.serial||null,lokasi:d.lokasi||null,status:d.status||'normal',url:d.url};
    const blob=new Blob([JSON.stringify(payload,null,2)],{type:'application/json'});
    const a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download=(payload.kode_qr||payload.token||'perangkat')+'.json'; a.click();
    setTimeout(()=>URL.revokeObjectURL(a.href),2000);
  });
  document.getElementById('btnReset').addEventListener('click',()=>{
    ['qrcode','printQR'].forEach(id=>document.getElementById(id).innerHTML='');
    ['pvNama','pvKode','pvMerek','pvModelSn','pvLokasi','pvUrl','prNama','prKode','prLokasi','prUrl'].forEach(id=>setText(id,'—'));
    const bb=document.getElementById('pvBadge'); bb.className='badge text-bg-secondary'; bb.textContent='Status';
    document.getElementById('alertBox').classList.add('d-none'); sessionStorage.removeItem('lastQR');
  });
});
