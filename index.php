<?php
if (!empty($_GET['lyrics'])) {
    $slug = basename($_GET['lyrics']);
    $path = __DIR__ . '/lyrics/' . $slug;
    if (!file_exists($path) || !str_ends_with($slug, '.txt')) {
        http_response_code(404); die('Not found.');
    }
    $raw = htmlspecialchars(file_get_contents($path));
    ?>
    <!DOCTYPE html><html lang="en"><head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lyrics</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Lora:wght@400;600&display=swap" rel="stylesheet">
    <style>
      body { margin: 0; background: linear-gradient(180deg,#111827,#0f172a); color: #e5e7eb; font-family: Inter, sans-serif; }
      .wrap { max-width: 760px; margin: 0 auto; padding: 40px 20px 80px; }
      a { color: #93c5fd; text-decoration: none; } a:hover { text-decoration: underline; }
      pre { white-space: pre-wrap; font-family: Lora, serif; line-height: 1.8; font-size: 1.08rem; color: #e2e8f0; background: rgba(255,255,255,0.04); padding: 24px; border-radius: 18px; border: 1px solid rgba(255,255,255,0.08); }
    </style>
    </head><body><div class="wrap"><p><a href="./">← Back to Daily Song</a></p><pre><?= $raw ?></pre></div></body></html>
    <?php
    exit;
}

if (!empty($_GET['prompt'])) {
    $slug = basename($_GET['prompt']);
    $path = __DIR__ . '/prompts/' . $slug;
    if (!file_exists($path) || !str_ends_with($slug, '.txt')) {
        http_response_code(404); die('Not found.');
    }
    $raw = htmlspecialchars(file_get_contents($path));
    ?>
    <!DOCTYPE html><html lang="en"><head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prompt</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Lora:wght@400;600&display=swap" rel="stylesheet">
    <style>
      body { margin: 0; background: linear-gradient(180deg,#111827,#0f172a); color: #e5e7eb; font-family: Inter, sans-serif; }
      .wrap { max-width: 760px; margin: 0 auto; padding: 40px 20px 80px; }
      a { color: #93c5fd; text-decoration: none; } a:hover { text-decoration: underline; }
      pre { white-space: pre-wrap; font-family: Inter, sans-serif; line-height: 1.8; font-size: 1rem; color: #e2e8f0; background: rgba(255,255,255,0.04); padding: 24px; border-radius: 18px; border: 1px solid rgba(255,255,255,0.08); }
    </style>
    </head><body><div class="wrap"><p><a href="./">← Back to Daily Song</a></p><pre><?= $raw ?></pre></div></body></html>
    <?php
    exit;
}

$entries_dir = __DIR__ . '/entries/';
$entries = [];
if (is_dir($entries_dir)) {
    foreach (glob($entries_dir . '*.json') as $file) {
        $data = json_decode(file_get_contents($file), true);
        if (!$data || empty($data['date']) || empty($data['title']) || empty($data['audio'])) continue;
        $entries[] = $data;
    }
}
usort($entries, fn($a,$b) => strcmp($b['date'], $a['date']));

$seeds = [];
$seeds_path = getenv('HOME') . '/projects/scripts/SONGS.yaml';
if (file_exists($seeds_path)) {
    $yaml = file_get_contents($seeds_path);
    if (preg_match_all('/- title: "([^"]+)"\s+artist: "([^"]+)"\s+description: "([\s\S]*?)"(?=\n\s*- title:|\z)/', $yaml, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $m) {
            $seeds[] = [
                'title' => $m[1],
                'artist' => $m[2],
                'description' => trim($m[3]),
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daily Song with Chloe</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Lora:ital,wght@0,400;1,400&display=swap" rel="stylesheet">
<style>
body{margin:0;font-family:Inter,sans-serif;background:linear-gradient(180deg,#1f2937,#0f172a);color:#e5e7eb;line-height:1.6}
.wrap{max-width:920px;margin:0 auto;padding:32px 20px 80px}.hero{margin-bottom:36px}.eyebrow{text-transform:uppercase;letter-spacing:.12em;font-size:.78rem;color:#f9a8d4;margin-bottom:10px}h1{margin:0 0 10px;font-size:clamp(2rem,4vw,3.4rem);line-height:1.05}.sub{max-width:720px;color:#cbd5e1;font-size:1.05rem}.hero-links{margin-top:10px;display:flex;gap:14px;flex-wrap:wrap}.hero-links a{color:#93c5fd;text-decoration:none;font-weight:500}.hero-links a:hover{text-decoration:underline}.entry{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:20px;padding:20px;margin:0 0 24px;box-shadow:0 18px 45px rgba(0,0,0,.25)}.meta{display:flex;gap:10px;align-items:center;margin-bottom:12px;flex-wrap:wrap}.date{color:#94a3b8;font-size:.9rem}.title{margin:0 0 8px;font-size:1.6rem}.caption{margin:0 0 16px;color:#fce7f3;font-family:Lora,serif;font-size:1.06rem}.thumb{width:100%;max-height:420px;object-fit:cover;border-radius:14px;background:#111827;margin-bottom:14px;cursor:pointer;transition:transform .15s ease, box-shadow .15s ease}.thumb:hover{transform:scale(1.01);box-shadow:0 10px 24px rgba(0,0,0,.25)}.links a{color:#93c5fd;text-decoration:none;font-weight:500}.links a:hover{text-decoration:underline}audio{width:100%;margin:10px 0 8px}.empty{color:#94a3b8;border:1px dashed rgba(255,255,255,.15);border-radius:18px;padding:24px}.overlay{display:none;position:fixed;inset:0;background:rgba(2,6,23,.82);z-index:1000;align-items:center;justify-content:center;padding:20px}.overlay.open{display:flex}.overlay-card{max-width:760px;width:100%;max-height:85vh;overflow:auto;background:linear-gradient(180deg,#111827,#0f172a);border:1px solid rgba(255,255,255,.1);border-radius:18px;padding:24px;box-shadow:0 18px 45px rgba(0,0,0,.45)}.overlay-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}.overlay-title{font-size:1.2rem;font-weight:600}.overlay-close{background:rgba(255,255,255,.08);border:none;color:#fff;font-size:1.4rem;width:38px;height:38px;border-radius:50%;cursor:pointer}.overlay-close:hover{background:rgba(255,255,255,.16)}.overlay-body{white-space:pre-wrap}.overlay-body.lyrics{font-family:Lora,serif;line-height:1.8;font-size:1.06rem;color:#e2e8f0}.overlay-body.prompt{font-family:Inter,sans-serif;line-height:1.7;color:#e2e8f0}.overlay-card.seeds{max-width:900px}.seeds-list{display:grid;gap:12px}.seed{border:1px solid rgba(255,255,255,.08);border-radius:14px;padding:14px 16px;background:rgba(255,255,255,.03)}.seed-top{display:flex;justify-content:space-between;gap:16px;align-items:center;flex-wrap:wrap}.seed-title{font-weight:600;color:#f8fafc}.seed-artist{color:#94a3b8;font-size:.95rem}.seed-toggle{color:#93c5fd;background:none;border:none;padding:0;cursor:pointer;font:inherit}.seed-desc{display:none;margin-top:10px;color:#cbd5e1;line-height:1.7}.seed.open .seed-desc{display:block}
</style></head><body><div class="wrap"><div class="hero"><div class="eyebrow">Daily tiny record label</div><h1>Daily Song with Chloe</h1><p class="sub">A daily original song archive: little folk-pop postcards, reflective sketches, and the occasional oddball melody.</p><div class="hero-links"><a href="#" id="open-seeds">Song Seeds</a><a href="#" id="open-radio">Radio</a></div></div><?php if (empty($entries)): ?><div class="empty">No songs yet. The studio is still tuning the guitar.</div><?php else: ?><?php foreach ($entries as $i => $entry): ?><article class="entry"><div class="meta"><span class="date"><?= htmlspecialchars($entry['display_date'] ?? $entry['date']) ?></span></div><h2 class="title"><?= htmlspecialchars($entry['title']) ?></h2><p class="caption"><?= htmlspecialchars($entry['caption'] ?? '') ?></p><?php if (!empty($entry['image'])): ?><img class="thumb" data-audio-id="audio-<?= $i ?>" src="images/<?= htmlspecialchars($entry['image']) ?>" alt="<?= htmlspecialchars($entry['title']) ?> thumbnail"><?php endif; ?><audio id="audio-<?= $i ?>" controls preload="none"><source src="audio/<?= htmlspecialchars($entry['audio']) ?>">Your browser does not support audio.</audio><p class="links"><?php if (!empty($entry['lyrics'])): ?><a href="#" class="open-overlay" data-kind="lyrics" data-title="<?= htmlspecialchars($entry['title'], ENT_QUOTES) ?> — Lyrics" data-src="lyrics/<?= htmlspecialchars($entry['lyrics']) ?>">Read lyrics</a><?php endif; ?><?php if (!empty($entry['prompt_file'])): ?> · <a href="#" class="open-overlay" data-kind="prompt" data-title="<?= htmlspecialchars($entry['title'], ENT_QUOTES) ?> — Prompt" data-src="prompts/<?= htmlspecialchars($entry['prompt_file']) ?>">Prompt</a><?php endif; ?><?php if (!empty($entry['model'])): ?> · <span>Model: <?= htmlspecialchars($entry['model']) ?></span><?php endif; ?></p></article><?php endforeach; ?><?php endif; ?></div><div class="overlay" id="overlay"><div class="overlay-card"><div class="overlay-head"><div class="overlay-title" id="overlay-title"></div><button class="overlay-close" id="overlay-close">&times;</button></div><div class="overlay-body" id="overlay-body"></div></div></div><div class="overlay" id="seeds-overlay"><div class="overlay-card seeds"><div class="overlay-head"><div class="overlay-title">Song Seeds</div><button class="overlay-close" id="seeds-close">&times;</button></div><div class="overlay-body prompt"><div class="seeds-list"><?php foreach ($seeds as $idx => $seed): ?><div class="seed" id="seed-<?= $idx ?>"><div class="seed-top"><div><div class="seed-title"><?= htmlspecialchars($seed['title']) ?></div><div class="seed-artist"><?= htmlspecialchars($seed['artist']) ?></div></div><button class="seed-toggle" data-seed="seed-<?= $idx ?>">Show description</button></div><div class="seed-desc"><?= htmlspecialchars($seed['description']) ?></div></div><?php endforeach; ?></div></div></div></div><div class="overlay" id="radio-overlay"><div class="overlay-card"><div class="overlay-head"><div class="overlay-title">Daily Song Radio</div><button class="overlay-close" id="radio-close">&times;</button></div><div class="overlay-body prompt"><div id="radio-now" style="font-weight:600;font-size:1.05rem;margin-bottom:10px;">Starting radio…</div><div id="radio-meta" style="color:#94a3b8;margin-bottom:12px;">Shuffling the archive</div><audio id="radio-audio" controls autoplay preload="none" style="width:100%;margin-bottom:12px;"></audio><button id="radio-next" style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);color:#fff;padding:10px 14px;border-radius:10px;cursor:pointer;">Skip to next</button></div></div></div><script>
const tracks = <?php echo json_encode(array_map(fn($e) => ['title' => $e['title'], 'date' => ($e['display_date'] ?? $e['date']), 'audio' => 'audio/' . $e['audio']], $entries)); ?>;
document.querySelectorAll('.thumb').forEach(img=>{img.addEventListener('click',()=>{const audio=document.getElementById(img.dataset.audioId);if(!audio)return;if(audio.paused){audio.play();}else{audio.pause();}})});
const overlay=document.getElementById('overlay');
const overlayTitle=document.getElementById('overlay-title');
const overlayBody=document.getElementById('overlay-body');
function closeOverlay(){overlay.classList.remove('open');overlayBody.textContent='';overlayBody.className='overlay-body';}
document.getElementById('overlay-close').addEventListener('click',closeOverlay);
overlay.addEventListener('click',e=>{if(e.target===overlay)closeOverlay();});
document.addEventListener('keydown',e=>{if(e.key==='Escape'&&overlay.classList.contains('open'))closeOverlay();});
document.querySelectorAll('.open-overlay').forEach(a=>{a.addEventListener('click',async e=>{e.preventDefault();overlayTitle.textContent=a.dataset.title||'';overlayBody.className='overlay-body '+(a.dataset.kind||'');overlayBody.textContent='Loading…';overlay.classList.add('open');try{const r=await fetch(a.dataset.src,{cache:'no-store'});const t=await r.text();overlayBody.textContent=t;}catch(err){overlayBody.textContent='Could not load.';}})});
const seedsOverlay=document.getElementById('seeds-overlay');
document.getElementById('open-seeds').addEventListener('click',e=>{e.preventDefault();seedsOverlay.classList.add('open');});
document.getElementById('seeds-close').addEventListener('click',()=>seedsOverlay.classList.remove('open'));
seedsOverlay.addEventListener('click',e=>{if(e.target===seedsOverlay)seedsOverlay.classList.remove('open');});
document.querySelectorAll('.seed-toggle').forEach(btn=>{btn.addEventListener('click',()=>{const el=document.getElementById(btn.dataset.seed); const open=el.classList.toggle('open'); btn.textContent=open?'Hide description':'Show description';});});
const radioOverlay=document.getElementById('radio-overlay');
const radioAudio=document.getElementById('radio-audio');
const radioNow=document.getElementById('radio-now');
const radioMeta=document.getElementById('radio-meta');
let radioQueue=[]; let radioIndex=0;
function shuffle(arr){ const a=[...arr]; for(let i=a.length-1;i>0;i--){ const j=Math.floor(Math.random()*(i+1)); [a[i],a[j]]=[a[j],a[i]]; } return a; }
function buildRadioQueue(){ radioQueue = shuffle(tracks); radioIndex = 0; }
function playRadioIndex(){ if(!radioQueue.length) return; if(radioIndex >= radioQueue.length){ buildRadioQueue(); radioMeta.textContent='Reshuffled the archive'; }
  const t = radioQueue[radioIndex]; radioAudio.src = t.audio; radioNow.textContent = t.title; radioMeta.textContent = `${t.date} · ${radioIndex+1} of ${radioQueue.length} in current shuffle`; radioAudio.play(); }
document.getElementById('open-radio').addEventListener('click',e=>{ e.preventDefault(); radioOverlay.classList.add('open'); if(!radioQueue.length) buildRadioQueue(); playRadioIndex(); });
document.getElementById('radio-close').addEventListener('click',()=>{ radioOverlay.classList.remove('open'); radioAudio.pause(); });
radioOverlay.addEventListener('click',e=>{ if(e.target===radioOverlay){ radioOverlay.classList.remove('open'); radioAudio.pause(); }});
document.getElementById('radio-next').addEventListener('click',()=>{ radioIndex += 1; playRadioIndex(); });
radioAudio.addEventListener('ended',()=>{ radioIndex += 1; playRadioIndex(); });
</script></body></html>
