(() => {
const video = document.getElementById('video');
const placeholder = document.getElementById('cameraPlaceholder');
const startBtn = document.getElementById('startCamera');
const captureBtn = document.getElementById('capture');
const countdownEl = document.getElementById('countdown');
const statusEl = document.getElementById('cameraStatus');
const canvas = document.getElementById('stripCanvas');
const ctx = canvas.getContext('2d');
const empty = document.getElementById('stripEmpty');
const progress = document.getElementById('shotProgress');
const downloadBtn = document.getElementById('download');
const printBtn = document.getElementById('print');
const retakeBtn = document.getElementById('retake');
const message = document.getElementById('saveMessage');
let stream = null, filter = 'none', layout = 'classic', shots = [], sessionBusy = false;

document.querySelectorAll('.filter').forEach(btn => btn.addEventListener('click', () => {
  document.querySelectorAll('.filter').forEach(b => b.classList.remove('active'));
  btn.classList.add('active'); filter = btn.dataset.filter; video.style.filter = filter;
}));
document.querySelectorAll('.layout-choice').forEach(btn => btn.addEventListener('click', () => {
  document.querySelectorAll('.layout-choice').forEach(b => b.classList.remove('active'));
  btn.classList.add('active'); layout = btn.dataset.layout;
  if (shots.length) renderStrip();
}));
document.getElementById('stripColor').addEventListener('change', () => { if (shots.length) renderStrip(); });
document.getElementById('caption').addEventListener('input', () => { if (shots.length) renderStrip(); });

async function startCamera() {
  if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
    alert('Camera access is not available. Open this page through http://localhost/photobooth_system/ in Chrome or Edge.');
    return;
  }
  try {
    stream = await navigator.mediaDevices.getUserMedia({video:{facingMode:'user', width:{ideal:1280}, height:{ideal:960}}, audio:false});
    video.srcObject = stream;
    await video.play();
    placeholder.classList.add('hidden');
    statusEl.innerHTML = '<i></i> Camera connected';
    statusEl.classList.add('online');
    captureBtn.disabled = false;
  } catch (err) {
    alert('Could not open camera. Allow camera permission in your browser and close other apps using the webcam.');
  }
}
startBtn.addEventListener('click', startCamera);

const wait = ms => new Promise(resolve => setTimeout(resolve, ms));
async function doCountdown(seconds) {
  countdownEl.classList.remove('hidden');
  for (let i = seconds; i > 0; i--) { countdownEl.textContent = i; await wait(1000); }
  countdownEl.textContent = 'SMILE!';
  await wait(350);
  countdownEl.classList.add('hidden');
}
function captureFrame() {
  const w = video.videoWidth || 1280, h = video.videoHeight || 960;
  const c = document.createElement('canvas'); c.width = w; c.height = h;
  const cctx = c.getContext('2d');
  cctx.translate(w, 0); cctx.scale(-1, 1);
  cctx.filter = filter;
  cctx.drawImage(video, 0, 0, w, h);
  cctx.filter = 'none';
  return c;
}
captureBtn.addEventListener('click', async () => {
  if (sessionBusy || !stream) return;
  sessionBusy = true; captureBtn.disabled = true; message.textContent = '';
  shots = [];
  const selected = document.getElementById('shotCount').value;
  const count = selected === 'strip2' ? 2 : Number(selected);
  for (let i = 0; i < count; i++) {
    await doCountdown(Number(document.getElementById('delay').value));
    shots.push(captureFrame());
    progress.textContent = `${shots.length} of ${count} photos captured`;
    renderStrip();
    if (i < count - 1) await wait(450);
  }
  sessionBusy = false; captureBtn.disabled = false;
  downloadBtn.disabled = false; printBtn.disabled = false; retakeBtn.disabled = false;
  await saveStrip();
});

function renderStrip() {
  if (!shots.length) return;
  empty.classList.add('hidden'); canvas.classList.remove('hidden');
  const bg = document.getElementById('stripColor').value;
  const dark = bg === '#242424';
  const caption = document.getElementById('caption').value.trim();
  const pad = 28, gap = 12, photoW = 420, photoH = 315;
  let positions = [], width, height;
  if (layout === 'grid') {
    width = pad*2 + photoW*2 + gap; height = pad*2 + photoH*2 + gap + (caption ? 46 : 0);
    shots.slice(0,4).forEach((s,i)=>positions.push({x:pad+(i%2)*(photoW+gap),y:pad+Math.floor(i/2)*(photoH+gap),w:photoW,h:photoH,s}));
  } else {
    const n = layout === 'three' ? Math.min(3, shots.length) : shots.length;
    width = photoW + pad*2; height = pad*2 + n*photoH + (n-1)*gap + (caption ? 52 : 0);
    shots.slice(0,n).forEach((s,i)=>positions.push({x:pad,y:pad+i*(photoH+gap),w:photoW,h:photoH,s}));
  }
  canvas.width = width; canvas.height = height;
  ctx.fillStyle = bg; ctx.fillRect(0,0,width,height);
  positions.forEach(p=>{
    ctx.save();
    ctx.fillStyle = dark ? '#353535' : '#fff';
    ctx.fillRect(p.x-3,p.y-3,p.w+6,p.h+6);
    ctx.drawImage(p.s,p.x,p.y,p.w,p.h);
    ctx.restore();
  });
  if (caption) {
    ctx.fillStyle = dark ? '#fffaf4' : '#39312c';
    ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
    ctx.font = '600 22px Arial, sans-serif';
    ctx.fillText(caption, width/2, height-27, width-30);
  }
  progress.textContent = `${shots.length} photo${shots.length===1?'':'s'} captured`;
}
downloadBtn.addEventListener('click', () => {
  if (!canvas.width) return;
  const a = document.createElement('a'); a.download = `snapstudio-${Date.now()}.png`; a.href = canvas.toDataURL('image/png'); a.click();
});
printBtn.addEventListener('click', () => window.print());
retakeBtn.addEventListener('click', () => {
  shots=[]; canvas.classList.add('hidden'); empty.classList.remove('hidden');
  downloadBtn.disabled = true; printBtn.disabled = true; retakeBtn.disabled = true;
  progress.textContent='0 photos captured'; message.textContent='';
  captureBtn.scrollIntoView({behavior:'smooth',block:'center'});
});
async function saveStrip() {
  try {
    const response = await fetch('save_photo.php', {
      method:'POST', headers:{'Content-Type':'application/json'},
      body:JSON.stringify({image:canvas.toDataURL('image/png')})
    });
    const result = await response.json();
    if (!response.ok || !result.ok) throw new Error(result.message || 'Save failed');
    message.textContent = 'Saved to this computer and added to Recent Memories.';
    const gallery = document.getElementById('gallery');
    const emptyGallery = gallery.querySelector('.gallery-empty');
    if (emptyGallery) emptyGallery.remove();
    const a = document.createElement('a'); a.className='gallery-item'; a.href=result.url; a.target='_blank';
    const img = document.createElement('img'); img.src=result.url; img.alt='Saved photo strip';
    const span = document.createElement('span'); span.textContent='Just now';
    a.append(img,span); gallery.prepend(a);
  } catch (err) {
    message.textContent = 'Download and print work, but gallery saving failed. Check database.sql and uploads folder permissions.';
  }
}
window.addEventListener('beforeunload', () => { if (stream) stream.getTracks().forEach(t=>t.stop()); });
})();
