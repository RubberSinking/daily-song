# Daily Song with Chloe

## Purpose
A daily original song generated with Gemini Lyria, published to web-lab and optionally sent to Telegram.

## Archive structure
- `index.php` — archive page
- `entries/` — one JSON file per song
- `audio/` — MP3/WAV assets
- `images/` — thumbnail/poster images
- `lyrics/` — plain text lyric files

## Entry JSON schema
```json
{
  "date": "2026-04-01",
  "display_date": "April 1, 2026",
  "title": "Small Mercies",
  "caption": "A quiet alt-folk song about the ordinary things that hold a day together.",
  "audio": "2026-04-01-small-mercies.mp3",
  "image": "2026-04-01-small-mercies.jpg",
  "lyrics": "2026-04-01-small-mercies.txt",
  "model": "lyria-3-pro-preview",
  "prompt": "short human-readable prompt summary",
  "duration_seconds": 175.9
}
```

## UX requirements
- Show image thumbnail
- Show title, caption, and date
- Native HTML audio player with play button
- Link to a dedicated lyrics view via `?lyrics=...`
- Reverse chronological ordering
- Validate required fields: `date`, `title`, `audio`
