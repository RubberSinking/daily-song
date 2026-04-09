<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['entry_id']) && isset($_POST['note'])) {
    $entry_id = basename($_POST['entry_id']);
    $note = $_POST['note'];
    $entries_dir = __DIR__ . '/entries/';
    $file = $entries_dir . $entry_id . '.json';
    $found = false;
    if (file_exists($file)) {
        $data = json_decode(file_get_contents($file), true);
        if ($data) {
            if (empty($note)) {
                unset($data['note']);
            } else {
                $data['note'] = $note;
            }
            file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
            $found = true;
        }
    }
    echo json_encode(['success' => $found]);
    exit;
}

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
        $data['file_path'] = $file;
        $data['entry_id'] = pathinfo($file, PATHINFO_FILENAME);
        $entries[] = $data;
    }
}
usort($entries, fn($a,$b) => strcmp($b['date'], $a['date']));

$seeds = [];
$seeds_path = __DIR__ . '/SONGS.yaml';
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
.wrap{max-width:920px;margin:0 auto;padding:32px 20px 80px}.hero{margin-bottom:36px}.eyebrow{text-transform:uppercase;letter-spacing:.12em;font-size:.78rem;color:#f9a8d4;margin-bottom:10px}h1{margin:0 0 10px;font-size:clamp(2rem,4vw,3.4rem);line-height:1.05}.sub{max-width:720px;color:#cbd5e1;font-size:1.05rem}.hero-links{margin-top:10px;display:flex;gap:14px;flex-wrap:wrap}.hero-links a{color:#93c5fd;text-decoration:none;font-weight:500}.hero-links a:hover{text-decoration:underline}.entry{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:20px;padding:20px;margin:0 0 24px;box-shadow:0 18px 45px rgba(0,0,0,.25)}.meta{display:flex;gap:10px;align-items:center;margin-bottom:12px;flex-wrap:wrap}.date{color:#94a3b8;font-size:.9rem}.title{margin:0 0 8px;font-size:1.6rem}.caption{margin:0 0 16px;color:#fce7f3;font-family:Lora,serif;font-size:1.06rem}.links a,.radio-links a{color:#93c5fd;text-decoration:none;font-weight:500}.links a:hover,.radio-links a:hover{text-decoration:underline}audio{width:100%;margin:10px 0 8px}.empty{color:#94a3b8;border:1px dashed rgba(255,255,255,.15);border-radius:18px;padding:24px}.overlay{display:none;position:fixed;inset:0;background:rgba(2,6,23,.82);z-index:1000;align-items:center;justify-content:center;padding:20px}.overlay.open{display:flex}#overlay{z-index:2200}#seeds-overlay{z-index:1600}#radio-overlay{z-index:1400}.overlay-card{max-width:760px;width:100%;max-height:85vh;overflow:auto;background:linear-gradient(180deg,#111827,#0f172a);border:1px solid rgba(255,255,255,.1);border-radius:18px;padding:24px;box-shadow:0 18px 45px rgba(0,0,0,.45)}.overlay-head{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}.overlay-title{font-size:1.2rem;font-weight:600}.overlay-close{background:rgba(255,255,255,.08);border:none;color:#fff;font-size:1.4rem;width:38px;height:38px;border-radius:50%;cursor:pointer}.overlay-close:hover{background:rgba(255,255,255,.16)}.overlay-body{white-space:pre-wrap}.overlay-body.lyrics{font-family:Lora,serif;line-height:1.8;font-size:1.06rem;color:#e2e8f0}.overlay-body.prompt{font-family:Inter,sans-serif;line-height:1.7;color:#e2e8f0}.overlay-card.seeds{max-width:900px}.seeds-list{display:grid;gap:12px}.seed{border:1px solid rgba(255,255,255,.08);border-radius:14px;padding:14px 16px;background:rgba(255,255,255,.03)}.seed-top{display:flex;justify-content:space-between;gap:16px;align-items:center;flex-wrap:wrap}.seed-title{font-weight:600;color:#f8fafc}.seed-artist{color:#94a3b8;font-size:.95rem}.seed-toggle{color:#93c5fd;background:none;border:none;padding:0;cursor:pointer;font:inherit}.seed-desc{display:none;margin-top:10px;color:#cbd5e1;line-height:1.7}.seed.open .seed-desc{display:block}.radio-layout{display:grid;grid-template-columns:minmax(180px,240px) 1fr;gap:18px;align-items:start}.radio-links{margin:0 0 12px 0;color:#94a3b8}.radio-links a{display:inline-block}.radio-links .sep{color:#64748b;margin:0 6px}.radio-note-display{margin:0 0 12px}.radio-note-display:empty{display:none}.radio-note-display .song-note{margin-top:0}.like-btn{background:none;border:none;color:#94a3b8;cursor:pointer;font-size:1.2rem;padding:0;transition:color .2s}.like-btn.liked{color:#f472b6}@media (max-width: 640px){.radio-layout{grid-template-columns:1fr}.overlay-card{padding:18px}.radio-mobile-meta{margin-top:12px}.radio-note-display .song-note{font-size:.98rem;line-height:1.6;padding:10px 12px}}
.note-btn{background:none;border:none;color:#93c5fd;cursor:pointer;padding:0;font-size:1rem;margin-left:8px;opacity:0.6}
.note-btn:hover{opacity:1}
.song-note{font-size:.9rem;color:#e5edf8;background:rgba(147,197,253,.10);border:1px solid rgba(147,197,253,.18);padding:8px 12px;border-radius:10px;margin:8px 0 0 0;font-style:italic}
.note-input{width:100%;box-sizing:border-box;padding:8px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.15);color:#fff;border-radius:8px;margin:10px 0}
.note-actions{display:flex;gap:10px}
.note-actions button{padding:6px 12px;cursor:pointer;border-radius:6px;border:none}
.save-note{background:#93c5fd;color:#0f172a}
.del-note{background:transparent;color:#f87171}
</style></head><body><div class="wrap"><div class="hero"><div class="eyebrow">Daily tiny record label</div><h1>Daily Song with Chloe</h1><p class="sub">A daily original song archive: little folk-pop postcards, reflective sketches, and the occasional oddball melody.</p><div class="hero-links"><a href="#" id="open-seeds">Song Seeds</a><a href="#" id="open-radio">Radio</a><a href="#" id="show-only-liked">Show Only Liked</a></div></div><?php if (empty($entries)): ?><div class="empty">No songs yet. The studio is still tuning the guitar.</div><?php else: ?><?php foreach ($entries as $i => $entry): ?><article class="entry" data-id="<?= htmlspecialchars($entry['entry_id']) ?>"><div class="meta"><span class="date"><?= htmlspecialchars($entry['display_date'] ?? $entry['date']) ?></span><button class="like-btn" data-id="<?= htmlspecialchars($entry['date']) ?>">♥</button></div><h2 class="title"><?= htmlspecialchars($entry['title']) ?> <button class="note-btn" data-entry-id="<?= htmlspecialchars($entry['entry_id']) ?>" title="Add note">📝</button></h2><p class="caption"><?= htmlspecialchars($entry['caption'] ?? '') ?></p><audio id="audio-<?= $i ?>" controls preload="none"><source src="audio/<?= htmlspecialchars($entry['audio']) ?>">Your browser does not support audio.</audio><p class="links"><?php if (!empty($entry['lyrics'])): ?><a href="#" class="open-overlay" data-kind="lyrics" data-title="<?= htmlspecialchars($entry['title'], ENT_QUOTES) ?> — Lyrics" data-src="lyrics/<?= htmlspecialchars($entry['lyrics']) ?>">Read lyrics</a><?php endif; ?><?php if (!empty($entry['prompt_file'])): ?> · <a href="#" class="open-overlay" data-kind="prompt" data-title="<?= htmlspecialchars($entry['title'], ENT_QUOTES) ?> — Prompt" data-src="prompts/<?= htmlspecialchars($entry['prompt_file']) ?>">Prompt</a><?php endif; ?><?php if (!empty($entry['model'])): ?> · <span>Model: <?= htmlspecialchars($entry['model']) ?></span><?php endif; ?></p></article><?php endforeach; ?><?php endif; ?></div><div class="overlay" id="overlay"><div class="overlay-card"><div class="overlay-head"><div class="overlay-title" id="overlay-title"></div><button class="overlay-close" id="overlay-close">&times;</button></div><div class="overlay-body" id="overlay-body"></div></div></div><div class="overlay" id="seeds-overlay"><div class="overlay-card seeds"><div class="overlay-head"><div class="overlay-title">Song Seeds</div><button class="overlay-close" id="seeds-close">&times;</button></div><div class="overlay-body prompt"><div class="seeds-list"><?php foreach ($seeds as $idx => $seed): ?><div class="seed" id="seed-<?= $idx ?>"><div class="seed-top"><div><div class="seed-title"><?= htmlspecialchars($seed['title']) ?></div><div class="seed-artist"><?= htmlspecialchars($seed['artist']) ?></div></div><button class="seed-toggle" data-seed="seed-<?= $idx ?>">Show description</button></div><div class="seed-desc"><?= htmlspecialchars($seed['description']) ?></div></div><?php endforeach; ?></div></div></div></div><div class="overlay" id="radio-overlay"><div class="overlay-card"><div class="overlay-head"><div class="overlay-title">Daily Song Radio</div><button class="overlay-close" id="radio-close">&times;</button></div><div class="overlay-body prompt"><div class="radio-layout"><div><div id="radio-caption" style="color:#cbd5e1;margin-top:10px;line-height:1.6;"></div></div><div class="radio-mobile-meta"><div id="radio-now" style="font-weight:600;font-size:1.05rem;margin-bottom:10px;">Starting radio…</div><div id="radio-note-display" class="radio-note-display"></div><div id="radio-meta" style="color:#94a3b8;margin-bottom:12px;">Shuffling the archive</div><div id="radio-links" class="radio-links"></div><audio id="radio-audio" controls autoplay preload="none" style="width:100%;margin-bottom:12px;"></audio><button id="radio-next" style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.14);color:#fff;padding:10px 14px;border-radius:10px;cursor:pointer;">Skip to next</button></div></div></div></div></div><script>
const tracks = <?php echo json_encode(array_map(fn($e) => ['title' => $e['title'], 'date' => ($e['display_date'] ?? $e['date']), 'audio' => 'audio/' . $e['audio'], 'image' => (!empty($e['image']) ? 'images/' . $e['image'] : ''), 'caption' => ($e['caption'] ?? ''), 'lyrics' => (!empty($e['lyrics']) ? 'lyrics/' . $e['lyrics'] : ''), 'prompt' => (!empty($e['prompt_file']) ? 'prompts/' . $e['prompt_file'] : ''), 'entry_id' => $e['entry_id'], 'note' => ($e['note'] ?? '')], $entries)); ?>;
let liked = JSON.parse(localStorage.getItem('likedSongs') || '[]');
const songNotes = tracks.reduce((acc, t) => { if(t.note) acc[t.entry_id] = t.note; return acc; }, JSON.parse(localStorage.getItem('songNotes') || '{}'));
const render = () => {
    document.querySelectorAll('.entry').forEach(el => {
        const entryId = el.dataset.id;
        let noteEl = el.querySelector('.song-note');
        if(songNotes[entryId]){
            if(!noteEl) {
                noteEl = document.createElement('p');
                noteEl.className = 'song-note';
                el.insertBefore(noteEl, el.querySelector('audio'));
            }
            noteEl.textContent = 'Note: ' + songNotes[entryId];
        } else if(noteEl) {
            noteEl.remove();
        }
    });
};
render();

document.querySelectorAll('.note-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const entryId = btn.dataset.entryId;
        const current = songNotes[entryId] || '';
        overlayTitle.textContent = 'Edit Note';
        overlayBody.className = 'overlay-body';
        overlayBody.innerHTML = `<input type="text" class="note-input" id="note-val" value="${current}" placeholder="Add a note..."><div class="note-actions"><button class="save-note" id="save-note">Save</button><button class="del-note" id="del-note">Delete</button></div>`;
        overlay.classList.add('open');
        document.getElementById('save-note').addEventListener('click', async () => {
            const val = document.getElementById('note-val').value;
            const r = await fetch(window.location.href, { method: 'POST', body: new URLSearchParams({entry_id: entryId, note: val}) });
            if((await r.json()).success) {
                if(val) songNotes[entryId] = val; else delete songNotes[entryId];
                localStorage.setItem('songNotes', JSON.stringify(songNotes));
                render();
                closeOverlay();
            }
        });
        document.getElementById('del-note').addEventListener('click', async () => {
            const r = await fetch(window.location.href, { method: 'POST', body: new URLSearchParams({entry_id: entryId, note: ''}) });
            if((await r.json()).success) {
                delete songNotes[entryId];
                localStorage.setItem('songNotes', JSON.stringify(songNotes));
                render();
                closeOverlay();
            }
        });
    });
});
/* Removed duplicate 'liked' declaration */
const updateLikes = () => {
  document.querySelectorAll('.like-btn').forEach(btn => {
    btn.classList.toggle('liked', liked.includes(btn.dataset.id));
  });
};
document.querySelectorAll('.like-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const id = btn.dataset.id;
    if (liked.includes(id)) liked = liked.filter(x => x !== id);
    else liked.push(id);
    localStorage.setItem('likedSongs', JSON.stringify(liked));
    updateLikes();
  });
});
document.getElementById('show-only-liked').addEventListener('click', (e) => {
  e.preventDefault();
  const filter = document.getElementById('show-only-liked');
  const isFiltering = filter.textContent === 'Show Only Liked';
  filter.textContent = isFiltering ? 'Show All' : 'Show Only Liked';
  document.querySelectorAll('.entry').forEach(entry => {
    entry.style.display = (!isFiltering || liked.includes(entry.dataset.id)) ? 'block' : 'none';
  });
});
updateLikes();
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
const radioNoteDisplay=document.getElementById('radio-note-display');
const radioMeta=document.getElementById('radio-meta');
const radioImage=document.getElementById('radio-image');
const radioCaption=document.getElementById('radio-caption');
const radioLinks=document.getElementById('radio-links');
let radioQueue=[]; let radioIndex=0;
function shuffle(arr){ const a=[...arr]; for(let i=a.length-1;i>0;i--){ const j=Math.floor(Math.random()*(i+1)); [a[i],a[j]]=[a[j],a[i]]; } return a; }
function buildRadioQueue(){ radioQueue = shuffle(tracks); radioIndex = 0; }
function playRadioIndex(){ if(!radioQueue.length) return; if(radioIndex >= radioQueue.length){ buildRadioQueue(); radioMeta.textContent='Reshuffled the archive'; }
  const t = radioQueue[radioIndex]; radioAudio.src = t.audio; radioNow.textContent = t.title; radioMeta.textContent = `${t.date} · ${radioIndex+1} of ${radioQueue.length} in current shuffle`; 
  const note = songNotes[t.entry_id] || t.note || '';
  radioNoteDisplay.innerHTML = note ? `<p class="song-note">Note: ${note}</p>` : '';

  const radioEditBtn = document.createElement('button');
  radioEditBtn.className = 'note-btn';
  radioEditBtn.textContent = '📝';
  radioEditBtn.title = 'Edit note';
  radioEditBtn.addEventListener('click', () => {
    const current = songNotes[t.entry_id] || '';
    overlayTitle.textContent = 'Edit Note';
    overlayBody.className = 'overlay-body';
    overlayBody.innerHTML = `<input type="text" class="note-input" id="note-val" value="${current}" placeholder="Add a note..."><div class="note-actions"><button class="save-note" id="save-note">Save</button><button class="del-note" id="del-note">Delete</button></div>`;
    overlay.classList.add('open');
    document.getElementById('save-note').addEventListener('click', async () => {
        const val = document.getElementById('note-val').value;
        const r = await fetch(window.location.href, { method: 'POST', body: new URLSearchParams({entry_id: t.entry_id, note: val}) });
        if((await r.json()).success) {
            if(val) songNotes[t.entry_id] = val; else delete songNotes[t.entry_id];
            localStorage.setItem('songNotes', JSON.stringify(songNotes));
            render();
            // Update the note display in the radio UI directly
            if (val) {
                radioNoteDisplay.innerHTML = `<p class="song-note">Note: ${val}</p>`;
            } else {
                radioNoteDisplay.innerHTML = '';
            }
            closeOverlay();
        }
    });
    document.getElementById('del-note').addEventListener('click', async () => {
        const r = await fetch(window.location.href, { method: 'POST', body: new URLSearchParams({entry_id: t.entry_id, note: ''}) });
        if((await r.json()).success) {
            delete songNotes[t.entry_id];
            localStorage.setItem('songNotes', JSON.stringify(songNotes));
            render();
            // Update the note display in the radio UI directly
            radioNoteDisplay.innerHTML = '';
            closeOverlay();
        }
    });
  });

  radioNow.appendChild(radioEditBtn);

  radioCaption.textContent = t.caption;
  if(t.image){radioImage.src=t.image; radioImage.style.display='block';}else{radioImage.style.display='none'; radioImage.src='';} radioLinks.innerHTML = (t.lyrics ? `<a href="#" class="radio-open" data-kind="lyrics" data-title="${t.title} — Lyrics" data-src="${t.lyrics}">Lyrics</a>` : '') + (t.prompt ? ` · <a href="#" class="radio-open" data-kind="prompt" data-title="${t.title} — Prompt" data-src="${t.prompt}">Prompt</a>` : ''); radioLinks.querySelectorAll('.radio-open').forEach(a=>a.addEventListener('click', async e=>{ e.preventDefault(); overlayTitle.textContent=a.dataset.title||''; overlayBody.className='overlay-body '+(a.dataset.kind||''); overlayBody.textContent='Loading…'; overlay.classList.add('open'); try{ const r=await fetch(a.dataset.src,{cache:'no-store'}); const txt=await r.text(); overlayBody.textContent=txt; } catch(err){ overlayBody.textContent='Could not load.'; } })); radioAudio.play(); }
document.getElementById('open-radio').addEventListener('click',e=>{ e.preventDefault(); radioOverlay.classList.add('open'); if(!radioQueue.length) buildRadioQueue(); playRadioIndex(); });
document.getElementById('radio-close').addEventListener('click',()=>{ radioOverlay.classList.remove('open'); radioAudio.pause(); });
radioOverlay.addEventListener('click',e=>{ if(e.target===radioOverlay){ radioOverlay.classList.remove('open'); radioAudio.pause(); }});
document.getElementById('radio-next').addEventListener('click',()=>{ radioIndex += 1; playRadioIndex(); });
radioAudio.addEventListener('ended',()=>{ radioIndex += 1; playRadioIndex(); });
</script></body></html>
