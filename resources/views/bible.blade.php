@extends('layouts.app')

@section('title', 'Bible')

@section('content')
    <h2>WEB Translation</h2>

    <div id="books">
        <label for="bookSelect">Book:</label>
        <select id="bookSelect"></select>

        <label for="chapterSelect">Chapter:</label>
        <select id="chapterSelect"></select>

        <button id="loadChapter">Load</button>
    </div>

    <div id="chapterContent">
        <h2 id="chapterTitle"></h2>
        <div id="verses"></div>
    </div>

    <script>
        const translation = 'WEB';

        // canonical order used client-side as a fallback/safety to ensure correct dropdown sorting
        const CANONICAL_ORDER = [
            'Genesis','Exodus','Leviticus','Numbers','Deuteronomy','Joshua','Judges','Ruth','1 Samuel','2 Samuel','1 Kings','2 Kings','1 Chronicles','2 Chronicles','Ezra','Nehemiah','Esther','Job','Psalms','Proverbs','Ecclesiastes','Song of Solomon','Isaiah','Jeremiah','Lamentations','Ezekiel','Daniel','Hosea','Joel','Amos','Obadiah','Jonah','Micah','Nahum','Habakkuk','Zephaniah','Haggai','Zechariah','Malachi','Matthew','Mark','Luke','John','Acts','Romans','1 Corinthians','2 Corinthians','Galatians','Ephesians','Philippians','Colossians','1 Thessalonians','2 Thessalonians','1 Timothy','2 Timothy','Titus','Philemon','Hebrews','James','1 Peter','2 Peter','1 John','2 John','3 John','Jude','Revelation'
        ];

        function normalize(name) {
            return name.toLowerCase().trim().replace(/[^a-z0-9]+/g, ' ').replace(/\s+/g, ' ');
        }

        function reorderToCanonical(arr) {
            const pos = {};
            CANONICAL_ORDER.forEach((b, i) => pos[normalize(b)] = i);

            // Map each item to a sorting key: canonical position or large number, then fallback to book name
            const mapped = arr.map(item => {
                const n = normalize(item.book);
                const p = (pos[n] !== undefined) ? pos[n] : Number.POSITIVE_INFINITY;
                return { orig: item, norm: n, pos: p };
            });

            mapped.sort((a, b) => {
                if (a.pos !== b.pos) return a.pos - b.pos;
                return a.orig.book.localeCompare(b.orig.book, undefined, { sensitivity: 'base' });
            });

            const ordered = mapped.map(m => m.orig);
            return ordered;
        }

        async function loadIndex() {
            // cache-busting timestamp to avoid stale cached responses
            const url = `/bible/api/${translation}/index?_=${Date.now()}`;
            const res = await fetch(url, { cache: 'no-store' });
            if (!res.ok) {
                document.getElementById('books').innerText = 'Bible index not available.';
                return;
            }
            let data = await res.json();

            // client-side enforce canonical ordering as a safety net
            data = reorderToCanonical(data);

            const bookSelect = document.getElementById('bookSelect');
            bookSelect.innerHTML = '';
            data.forEach(book => {
                const opt = document.createElement('option');
                opt.value = book.book.replace(/\s+/g, '_');
                opt.textContent = `${book.book} (${book.chapter_count})`;
                opt.dataset.chapterCount = book.chapter_count;
                bookSelect.appendChild(opt);
            });
            populateChapters();
        }

        function populateChapters() {
            const bookSelect = document.getElementById('bookSelect');
            const chapterSelect = document.getElementById('chapterSelect');
            chapterSelect.innerHTML = '';
            const count = parseInt(bookSelect.selectedOptions[0]?.dataset.chapterCount || 0, 10);
            for (let i = 1; i <= count; i++) {
                const opt = document.createElement('option');
                opt.value = i;
                opt.textContent = i;
                chapterSelect.appendChild(opt);
            }
        }

        document.getElementById('bookSelect').addEventListener('change', populateChapters);
        document.getElementById('loadChapter').addEventListener('click', async () => {
            const book = document.getElementById('bookSelect').value;
            const chapter = document.getElementById('chapterSelect').value;
            await loadChapter(book, chapter);
        });

        async function loadChapter(book, chapter) {
            const res = await fetch(`/bible/api/${translation}/${book}/${chapter}`);
            if (!res.ok) {
                document.getElementById('chapterContent').innerText = 'Chapter not found.';
                return;
            }
            const data = await res.json();
            document.getElementById('chapterTitle').textContent = `${data.book} ${data.chapter}`;
            const versesDiv = document.getElementById('verses');
            versesDiv.innerHTML = '';
            data.verses.forEach(v => {
                const p = document.createElement('p');
                p.innerHTML = `<sup>${v.verse}</sup> ${v.text}`;
                versesDiv.appendChild(p);
            });
        }

        // initial load
        loadIndex();
    </script>

@endsection

