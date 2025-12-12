@extends('layouts.app')

@section('title', 'Bible')

@section('content')
    <div style="margin-bottom: 2rem; display: flex; justify-content: flex-end;">
        <a href="{{ route('favorites.index') }}" class="btn" style="background: var(--accent); color: white; display: inline-flex; align-items: center; gap: 0.5rem; border-radius: 999px; padding: 0.5rem 1.25rem;">
            <span style="font-size: 1.2rem;">&hearts;</span> View Favorite Verses
        </a>
    </div>

    <h2>WEB Translation</h2>

    <div id="books" style="background: var(--bg-surface); padding: 1.5rem; border-radius: var(--radius-md); box-shadow: var(--shadow-sm); margin-bottom: 3rem; display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
        <label for="bookSelect" style="font-weight: 600;">Book:</label>
        <select id="bookSelect" style="padding: 0.5rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); min-width: 150px;"></select>

        <label for="chapterSelect" style="font-weight: 600;">Chapter:</label>
        <select id="chapterSelect" style="padding: 0.5rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);"></select>

        <button id="loadChapter" class="btn" style="margin-left: auto;">Load</button>
    </div>

    <div id="chapterContent" style="max-width: 800px; margin: 0 auto;">
        <h2 id="chapterTitle" style="text-align: center; margin-bottom: 2rem; font-size: 2rem;"></h2>
        <div id="verses"></div>

        <!-- Navigation buttons for previous/next chapter -->
        <div id="chapterNav" style="display:flex; gap:1rem; justify-content:center; margin-top: 1.5rem;">
            <button id="prevChapter" class="btn" style="padding: 0.5rem 1rem;" disabled>Previous Chapter</button>
            <button id="nextChapter" class="btn" style="padding: 0.5rem 1rem;" disabled>Next Chapter</button>
        </div>
    </div>

    <script>
        const translation = 'WEB';
        const CANONICAL_ORDER = [
            'Genesis','Exodus','Leviticus','Numbers','Deuteronomy','Joshua','Judges','Ruth','1 Samuel','2 Samuel','1 Kings','2 Kings','1 Chronicles','2 Chronicles','Ezra','Nehemiah','Esther','Job','Psalms','Proverbs','Ecclesiastes','Song of Solomon','Isaiah','Jeremiah','Lamentations','Ezekiel','Daniel','Hosea','Joel','Amos','Obadiah','Jonah','Micah','Nahum','Habakkuk','Zephaniah','Haggai','Zechariah','Malachi','Matthew','Mark','Luke','John','Acts','Romans','1 Corinthians','2 Corinthians','Galatians','Ephesians','Philippians','Colossians','1 Thessalonians','2 Thessalonians','1 Timothy','2 Timothy','Titus','Philemon','Hebrews','James','1 Peter','2 Peter','1 John','2 John','3 John','Jude','Revelation'
        ];

        function normalize(name) {
            return name.toLowerCase().trim().replace(/[^a-z0-9]+/g, ' ').replace(/\s+/g, ' ');
        }

        function reorderToCanonical(arr) {
            const pos = {};
            CANONICAL_ORDER.forEach((b, i) => pos[normalize(b)] = i);

            const mapped = arr.map(item => {
                const n = normalize(item.book);
                const p = (pos[n] !== undefined) ? pos[n] : Number.POSITIVE_INFINITY;
                return { orig: item, norm: n, pos: p };
            });

            mapped.sort((a, b) => {
                if (a.pos !== b.pos) return a.pos - b.pos;
                return a.orig.book.localeCompare(b.orig.book, undefined, { sensitivity: 'base' });
            });

            return mapped.map(m => m.orig);
        }

        function getLastVisited() {
            try {
                const raw = localStorage.getItem('bible_last');
                if (!raw) return null;
                const parsed = JSON.parse(raw);
                if (parsed && parsed.book && parsed.chapter) return parsed;
            } catch (e) {
                // ignore
            }
            return null;
        }

        function setLastVisited(book, chapter) {
            try {
                localStorage.setItem('bible_last', JSON.stringify({ book, chapter: Number(chapter) }));
            } catch (e) {
                // ignore
            }
        }

        // favorites state: map 'Book|Chapter|Verse' => favoriteId
        const favoritesMap = new Map();
        let isAuthenticated = false;

        // Global index of books (populated by loadIndex)
        let bibleIndex = [];

        async function loadFavorites() {
            try {
                const res = await fetch('{{ route('favorites.index') }}', { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return;
                const favs = await res.json();
                favs.forEach(f => {
                    const key = `${f.book}|${f.chapter}|${f.verse}`;
                    favoritesMap.set(key, f.id);
                });
                isAuthenticated = true;
            } catch (e) {
                // likely guest or network error — keep isAuthenticated false
            }
        }

        async function toggleFavorite(book, chapter, verse, el) {
            const key = `${book}|${chapter}|${verse}`;
            const existingId = favoritesMap.get(key);
            if (existingId) {
                // delete
                const res = await fetch(`{{ url('/favorites') }}/${existingId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                });
                if (res.ok) {
                    favoritesMap.delete(key);
                    markFavorite(el, false);
                } else {
                    alert('Could not remove favorite');
                }
                return;
            }

            // create
            const res = await fetch('{{ route('favorites.store') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: JSON.stringify({ translation: translation, book: book, chapter: chapter, verse: verse })
            });

            if (res.ok) {
                const data = await res.json();
                favoritesMap.set(key, data.favorite.id);
                markFavorite(el, true);
            } else if (res.status === 401) {
                // not authenticated — redirect to login
                window.location = '{{ route('login') }}';
            } else {
                alert('Could not add favorite');
            }
        }

        function markFavorite(el, filled) {
            if (!el) return;
            el.dataset.favorited = filled ? '1' : '0';
            if (filled) {
                el.style.background = '#fff4c2';
                el.style.borderRadius = '6px';
            } else {
                el.style.background = 'transparent';
                el.style.borderRadius = '';
            }
        }

        async function loadIndex() {
            const url = `/bible/api/${translation}/index?_=${Date.now()}`;
            const res = await fetch(url, { cache: 'no-store' });
            if (!res.ok) {
                document.getElementById('books').innerText = 'Bible index not available.';
                return;
            }
            let data = await res.json();

            data = reorderToCanonical(data);

            // store globally for nav logic
            bibleIndex = data;

            const bookSelect = document.getElementById('bookSelect');
            bookSelect.innerHTML = '';

            // Filter out invalid or placeholder entries (some feeds may include a stray "Books" entry)
            // Create a set of normalized canonical book names to whitelist valid books.
            const canonicalSet = new Set(CANONICAL_ORDER.map(n => normalize(n)));

            const excluded = [];
            const validData = data.filter(b => {
                if (!b || !b.book) {
                    excluded.push(b);
                    return false;
                }
                const rawName = String(b.book);
                const nameNorm = normalize(rawName);
                if (!nameNorm) {
                    excluded.push(b);
                    return false;
                }
                // Only include entries that match our canonical book names (preserves '1 Samuel', etc.)
                if (!canonicalSet.has(nameNorm)) {
                    excluded.push(b);
                    return false;
                }
                // ensure chapter_count is a positive integer
                const count = Number(b.chapter_count);
                if (!Number.isFinite(count) || count < 1) {
                    excluded.push(b);
                    return false;
                }
                return true;
            });

            // Diagnostic: log excluded entries (helps track stray 'Books' entries)
            if (excluded.length) {
                try { console.debug('bible: excluded index entries', excluded); } catch (e) { /* ignore */ }
            }

            if (validData.length === 0) {
                document.getElementById('books').innerText = 'Bible index not available.';
                // clear and disable selects/buttons
                document.getElementById('bookSelect').innerHTML = '';
                document.getElementById('chapterSelect').innerHTML = '';
                document.getElementById('loadChapter').disabled = true;
                document.getElementById('prevChapter').disabled = true;
                document.getElementById('nextChapter').disabled = true;
                return;
            }

            validData.forEach(book => {
                const display = String(book.book || '').trim();
                if (!display) return;
                // defensive: skip entries that are literally 'book' or 'books'
                if (/^books?$/i.test(display)) return;
                const count = Number(book.chapter_count) || 0;
                if (!Number.isFinite(count) || count < 1) return;

                const opt = document.createElement('option');
                opt.value = display.replace(/\s+/g, '_');
                // show only the book name in the dropdown (remove chapter count)
                opt.textContent = display;
                opt.dataset.chapterCount = count;
                bookSelect.appendChild(opt);
            });
            populateChapters();

            // Remove any residual invalid options (defensive, in case of caching/malformed data)
            const removedOptions = [];
            Array.from(bookSelect.options).forEach(o => {
                const txt = String(o.textContent || '').trim();
                const normTxt = normalize(txt);
                const cnt = Number(o.dataset.chapterCount) || 0;
                if (!txt || cnt < 1 || !normTxt || normTxt.startsWith('book')) {
                    removedOptions.push({ text: txt, norm: normTxt, count: cnt });
                    o.remove();
                }
            });
            if (removedOptions.length) {
                try { console.debug('bible: removed invalid book options', removedOptions); } catch (e) { /* ignore */ }
            }

            // Ensure the first available option is valid (has at least 1 chapter). If not, remove invalids.
            const remainingOptions = Array.from(bookSelect.options);
            let firstValid = remainingOptions.find(o => Number(o.dataset.chapterCount) >= 1);
            if (!firstValid) {
                // No valid books left
                document.getElementById('books').innerText = 'Bible index not available.';
                document.getElementById('bookSelect').innerHTML = '';
                document.getElementById('chapterSelect').innerHTML = '';
                document.getElementById('loadChapter').disabled = true;
                document.getElementById('prevChapter').disabled = true;
                document.getElementById('nextChapter').disabled = true;
                return;
            }

            // If the currently selected option is invalid or missing, select the first valid one
            if (![...bookSelect.options].some(o => Number(o.dataset.chapterCount) >= 1)) {
                bookSelect.value = firstValid.value;
            }

            // Re-populate chapters based on final selection
            populateChapters();

            await loadFavorites();

            const last = getLastVisited();
            let initialBook;
            let initialChapter;
            if (last) {
                initialBook = last.book;
                initialChapter = last.chapter;
            } else {
                initialBook = bookSelect.options[0]?.value || 'Genesis';
                initialChapter = 1;
            }

            // If stored initialBook isn't present (or was a removed placeholder), fall back to first valid option
            if (![...bookSelect.options].some(o => o.value === initialBook)) {
                initialBook = bookSelect.options[0]?.value || initialBook;
            }

            bookSelect.value = initialBook;
            populateChapters();
            const chapterSelect = document.getElementById('chapterSelect');
            const chapterCount = chapterSelect.options.length ? Number(chapterSelect.options[chapterSelect.options.length - 1].value) : 0;

            if (!initialChapter || initialChapter < 1 || initialChapter > chapterCount) {
                initialChapter = 1;
            }

            chapterSelect.value = initialChapter;

            await loadChapter(initialBook, initialChapter);
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

        // Navigation helpers
        function getBookOptionsArray() {
            return Array.from(document.getElementById('bookSelect').options).map(o => ({ value: o.value, chapterCount: Number(o.dataset.chapterCount) }));
        }

        function getPrevPosition(bookValue, chapter) {
            const options = getBookOptionsArray();
            const idx = options.findIndex(o => o.value === bookValue);
            if (idx === -1) return null;
            if (chapter > 1) return { book: bookValue, chapter: Number(chapter) - 1 };
            // need previous book
            const prevBook = options[idx - 1];
            if (!prevBook) return null;
            return { book: prevBook.value, chapter: prevBook.chapterCount };
        }

        function getNextPosition(bookValue, chapter) {
            const options = getBookOptionsArray();
            const idx = options.findIndex(o => o.value === bookValue);
            if (idx === -1) return null;
            const currBookCount = options[idx].chapterCount;
            if (Number(chapter) < currBookCount) return { book: bookValue, chapter: Number(chapter) + 1 };
            // need next book
            const nextBook = options[idx + 1];
            if (!nextBook) return null;
            return { book: nextBook.value, chapter: 1 };
        }

        function updateNavButtons(bookValue, chapter) {
            const prevBtn = document.getElementById('prevChapter');
            const nextBtn = document.getElementById('nextChapter');
            const prevPos = getPrevPosition(bookValue, chapter);
            const nextPos = getNextPosition(bookValue, chapter);

            prevBtn.disabled = !prevPos;
            nextBtn.disabled = !nextPos;

            // set titles for accessibility
            prevBtn.title = prevPos ? `Go to ${prevPos.book.replace(/_/g,' ')} ${prevPos.chapter}` : 'No previous chapter';
            nextBtn.title = nextPos ? `Go to ${nextPos.book.replace(/_/g,' ')} ${nextPos.chapter}` : 'No next chapter';
        }

        document.getElementById('bookSelect').addEventListener('change', populateChapters);
        document.getElementById('loadChapter').addEventListener('click', async () => {
            const book = document.getElementById('bookSelect').value;
            const chapter = document.getElementById('chapterSelect').value;
            try { window.scrollTo({ top: 0, behavior: 'smooth' }); } catch (e) { /* ignore */ }
            await loadChapter(book, chapter);
        });

        document.getElementById('prevChapter').addEventListener('click', async () => {
            const book = document.getElementById('bookSelect').value;
            const chapter = Number(document.getElementById('chapterSelect').value);
            const prev = getPrevPosition(book, chapter);
            if (!prev) return;
            // update selects then load
            document.getElementById('bookSelect').value = prev.book;
            populateChapters();
            document.getElementById('chapterSelect').value = prev.chapter;
            try { window.scrollTo({ top: 0, behavior: 'smooth' }); } catch (e) { /* ignore */ }
            await loadChapter(prev.book, prev.chapter);
        });

        document.getElementById('nextChapter').addEventListener('click', async () => {
            const book = document.getElementById('bookSelect').value;
            const chapter = Number(document.getElementById('chapterSelect').value);
            const next = getNextPosition(book, chapter);
            if (!next) return;
            document.getElementById('bookSelect').value = next.book;
            populateChapters();
            document.getElementById('chapterSelect').value = next.chapter;
            try { window.scrollTo({ top: 0, behavior: 'smooth' }); } catch (e) { /* ignore */ }
            await loadChapter(next.book, next.chapter);
        });

        async function loadChapter(book, chapter) {
            // ensure selects reflect the requested book/chapter
            const bookSelect = document.getElementById('bookSelect');
            const chapterSelect = document.getElementById('chapterSelect');
            if (bookSelect.value !== book) {
                // if the requested book exists in select, set it; otherwise leave as-is
                if ([...bookSelect.options].some(o => o.value === book)) {
                    bookSelect.value = book;
                    populateChapters();
                }
            }
            if ([...chapterSelect.options].some(o => o.value === String(chapter))) {
                chapterSelect.value = chapter;
            }

            const res = await fetch(`/bible/api/${translation}/${book}/${chapter}`);
            if (!res.ok) {
                document.getElementById('chapterContent').innerText = 'Chapter not found.';
                // update nav buttons conservatively
                updateNavButtons(book, Number(chapter));
                // scroll to top so user sees the message / title area
                try { window.scrollTo({ top: 0, behavior: 'smooth' }); } catch (e) { /* ignore */ }
                return;
            }
            const data = await res.json();
            document.getElementById('chapterTitle').textContent = `${data.book} ${data.chapter}`;
            const versesDiv = document.getElementById('verses');
            versesDiv.innerHTML = '';
            data.verses.forEach(v => {
                const wrapper = document.createElement('div');
                wrapper.dataset.book = data.book;
                wrapper.dataset.chapter = data.chapter;
                wrapper.dataset.verse = v.verse;
                wrapper.style.cursor = 'pointer';
                // keep default spacing; do not add extra padding so verse layout remains unchanged

                const p = document.createElement('p');
                p.innerHTML = `<sup>${v.verse}</sup> ${v.text}`;

                const key = `${data.book}|${data.chapter}|${v.verse}`;
                const filled = favoritesMap.has(key);
                markFavorite(wrapper, filled);

                wrapper.addEventListener('click', async (e) => {
                    e.preventDefault();
                    if (!isAuthenticated) {
                        // redirect to login
                        window.location = '{{ route('login') }}';
                        return;
                    }
                    await toggleFavorite(data.book, data.chapter, v.verse, wrapper);
                });

                wrapper.appendChild(p);
                versesDiv.appendChild(wrapper);
            });

            setLastVisited(book, data.chapter);

            // update navigation buttons
            try {
                // book here is the select-value form (underscores), ensure we pass that
                updateNavButtons(book, data.chapter);
            } catch (e) {
                // ignore nav errors
            }

            // After loading content, scroll to top so the user sees the title/controls
            try { window.scrollTo({ top: 0, behavior: 'smooth' }); } catch (e) { /* ignore */ }

            // handle scroll to verse requested from favorites page
            try {
                const scrollTo = localStorage.getItem('bible_scroll');
                if (scrollTo) {
                    const el = Array.from(document.querySelectorAll('#verses > div')).find(d => d.dataset.verse === scrollTo);
                    if (el) {
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        // briefly highlight
                        el.style.transition = 'background-color 0.4s ease';
                        const prevBg = el.style.background;
                        const prevRadius = el.style.borderRadius;
                        el.style.background = '#dff0d8';
                        el.style.borderRadius = '6px';
                        setTimeout(() => {
                            el.style.background = prevBg;
                            el.style.borderRadius = prevRadius || '';
                        }, 2500);
                    }
                    localStorage.removeItem('bible_scroll');
                }
            } catch (e) {
                // ignore
            }
        }

        loadIndex();
    </script>

@endsection

