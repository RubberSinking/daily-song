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
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daily Song with Chloe</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Lora:ital,wght@0,400;1,400&display=swap" rel="stylesheet">
<style>
body{margin:0;font-family:Inter,sans-serif;background:linear-gradient(180deg,#1f2937,#0f172a);color:#e5e7eb;line-height:1.6}
.wrap{max-width:920px;margin:0 auto;padding:32px 20px 80px}.hero{margin-bottom:36px}.eyebrow{text-transform:uppercase;letter-spacing:.12em;font-size:.78rem;color:#f9a8d4;margin-bottom:10px}h1{margin:0 0 10px;font-size:clamp(2rem,4vw,3.4rem);line-height:1.05}.sub{max-width:720px;color:#cbd5e1;font-size:1.05rem}.entry{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:20px;padding:20px;margin:0 0 24px;box-shadow:0 18px 45px rgba(0,0,0,.25)}.meta{display:flex;gap:10px;align-items:center;margin-bottom:12px;flex-wrap:wrap}.date{color:#94a3b8;font-size:.9rem}.title{margin:0 0 8px;font-size:1.6rem}.caption{margin:0 0 16px;color:#fce7f3;font-family:Lora,serif;font-size:1.06rem}.thumb{width:100%;max-height:420px;object-fit:cover;border-radius:14px;background:#111827;margin-bottom:14px}.links a{color:#93c5fd;text-decoration:none;font-weight:500}.links a:hover{text-decoration:underline}audio{width:100%;margin:10px 0 8px}.empty{color:#94a3b8;border:1px dashed rgba(255,255,255,.15);border-radius:18px;padding:24px}
</style></head><body><div class="wrap"><div class="hero"><div class="eyebrow">Daily tiny record label</div><h1>Daily Song with Chloe</h1><p class="sub">A daily original song archive: little folk-pop postcards, reflective sketches, and the occasional oddball melody.</p></div><?php if (empty($entries)): ?><div class="empty">No songs yet. The studio is still tuning the guitar.</div><?php else: ?><?php foreach ($entries as $entry): ?><article class="entry"><div class="meta"><span class="date"><?= htmlspecialchars($entry['display_date'] ?? $entry['date']) ?></span></div><h2 class="title"><?= htmlspecialchars($entry['title']) ?></h2><p class="caption"><?= htmlspecialchars($entry['caption'] ?? '') ?></p><?php if (!empty($entry['image'])): ?><img class="thumb" src="images/<?= htmlspecialchars($entry['image']) ?>" alt="<?= htmlspecialchars($entry['title']) ?> thumbnail"><?php endif; ?><audio controls preload="none"><source src="audio/<?= htmlspecialchars($entry['audio']) ?>">Your browser does not support audio.</audio><p class="links"><?php if (!empty($entry['lyrics'])): ?><a href="?lyrics=<?= urlencode($entry['lyrics']) ?>">Read lyrics</a><?php endif; ?><?php if (!empty($entry['prompt_file'])): ?> · <a href="?prompt=<?= urlencode($entry['prompt_file']) ?>">Prompt</a><?php endif; ?><?php if (!empty($entry['model'])): ?> · <span>Model: <?= htmlspecialchars($entry['model']) ?></span><?php endif; ?></p></article><?php endforeach; ?><?php endif; ?></div></body></html>
