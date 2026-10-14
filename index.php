<?php
require_once __DIR__ . '/config.php';
$recent = [];
try {
    $stmt = $pdo->query("SELECT id, filename, created_at FROM photos ORDER BY id DESC LIMIT 12");
    $recent = $stmt->fetchAll();
} catch (Throwable $e) { /* SQL import may not have been completed yet. */ }
function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>SnapStudio Photobooth</title><link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar"><a class="brand" href="#"><span class="brand-icon">✦</span> SnapStudio</a><span class="top-note">YOUR MOMENT. YOUR STRIP.</span></header>
<main class="app">
  <section class="intro"><div><p class="eyebrow">INSTANT PHOTO EXPERIENCE</p><h1>Make a little<br><em>memory.</em></h1><p class="sub">Gather your people, strike a pose, and create a photo strip worth keeping.</p></div><div class="sticker">SMILE!<span>☺</span></div></section>
  <section class="studio">
    <div class="camera-panel">
      <div class="section-head"><div><span class="step">01</span><h2>Camera studio</h2></div><span id="cameraStatus" class="status"><i></i> Camera not started</span></div>
      <div class="camera-wrap">
        <video id="video" autoplay playsinline muted></video>
        <div id="cameraPlaceholder" class="placeholder"><div class="camera-emoji">📸</div><h3>Your camera is waiting</h3><p>Allow camera access to get started.</p><button id="startCamera" class="btn primary">Enable camera</button></div>
        <div id="countdown" class="countdown hidden"></div>
        <div class="live-tag"><i></i> LIVE PREVIEW</div>
      </div>
      <div class="camera-controls">
        <label class="field">Countdown <select id="delay"><option value="3">3 seconds</option><option value="5" selected>5 seconds</option><option value="10">10 seconds</option></select></label>
        <label class="field">Shots <select id="shotCount"><option value="3">3 photos</option><option value="4" selected>4 photos</option><option value="strip2">2 photos</option></select></label>
        <button id="capture" class="btn primary" disabled>● Start photo session</button>
      </div>
      <p class="hint">Tip: Allow camera access in your browser. Photos are captured from this device.</p>
    </div>
    <aside class="settings-panel">
      <div class="section-head"><div><span class="step">02</span><h2>Make it yours</h2></div></div>
      <p class="label-title">FILTER</p>
      <div class="filters">
        <button class="filter active" data-filter="none"><span class="filter-swatch natural"></span>Original</button>
        <button class="filter" data-filter="grayscale(1)"><span class="filter-swatch mono"></span>Mono</button>
        <button class="filter" data-filter="sepia(.75)"><span class="filter-swatch retro"></span>Retro</button>
        <button class="filter" data-filter="contrast(1.15) saturate(1.45)"><span class="filter-swatch pop"></span>Pop</button>
        <button class="filter" data-filter="brightness(1.12) saturate(.8)"><span class="filter-swatch soft"></span>Soft</button>
      </div>
      <p class="label-title">PHOTO STRIP LAYOUT</p>
      <div class="layouts">
        <button class="layout-choice active" data-layout="classic"><span class="layout-preview classic-preview"><i></i><i></i><i></i><i></i></span><span>Classic 4</span></button>
        <button class="layout-choice" data-layout="three"><span class="layout-preview three-preview"><i></i><i></i><i></i></span><span>Mini 3</span></button>
        <button class="layout-choice" data-layout="grid"><span class="layout-preview grid-preview"><i></i><i></i><i></i><i></i></span><span>Grid 2×2</span></button>
      </div>
      <label class="field full">Strip color
        <select id="stripColor"><option value="#fffaf4">Warm white</option><option value="#f8dfe5">Blush pink</option><option value="#dcebe3">Sage green</option><option value="#242424">Midnight</option><option value="#e4e8ff">Lavender</option></select>
      </label>
      <label class="field full">Caption (optional)<input id="caption" maxlength="45" placeholder="e.g. Best day ever ✨"></label>
      <div class="tip-card"><span>✧</span><div><strong>Good things take a few shots.</strong><p>Your finished strip will appear below. You can download or print it.</p></div></div>
    </aside>
  </section>
  <section class="result-section">
    <div class="section-head"><div><span class="step">03</span><h2>Your photo strip</h2></div><span id="shotProgress" class="progress">0 photos captured</span></div>
    <div class="result-grid">
      <div class="strip-stage"><div id="stripEmpty" class="empty-state"><span>✦</span><h3>Your strip goes here</h3><p>Take your first photo session to see the magic.</p></div><canvas id="stripCanvas" class="hidden"></canvas></div>
      <div class="result-actions"><h3>Keep the memory</h3><p>Happy with your photos? Save the strip to your device or print a copy.</p><button id="download" class="btn primary" disabled>↓ Download photo strip</button><button id="print" class="btn secondary" disabled>▣ Print strip</button><button id="retake" class="btn text-btn" disabled>↻ Retake session</button><div id="saveMessage" class="save-message" role="status"></div></div>
    </div>
  </section>
  <section class="gallery-section"><div class="section-head"><div><span class="step">04</span><h2>Recent memories</h2></div><span class="progress">Saved on this computer</span></div>
    <div id="gallery" class="gallery"><?php if (!$recent): ?><div class="gallery-empty">Your saved sessions will show up here.</div><?php else: foreach($recent as $photo): ?><a class="gallery-item" href="uploads/<?=e($photo['filename'])?>" target="_blank"><img src="uploads/<?=e($photo['filename'])?>" alt="Saved photo strip"><span><?=e(date('M j, Y · g:i a', strtotime($photo['created_at'])))?></span></a><?php endforeach; endif; ?></div>
  </section>
</main>
<footer>SNAPSTUDIO <span>•</span> MADE FOR THE MOMENTS THAT MATTER</footer>
<script src="assets/app.js"></script>
</body></html>