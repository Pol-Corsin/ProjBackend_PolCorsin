(function(){
    // Helper: create suggestions container
    function createSuggestionsContainer(input) {
        let container = document.createElement('div');
        container.className = 'search-suggestions';
        container.style.position = 'absolute';
        container.style.zIndex = '9999';
        container.style.minWidth = (input.offsetWidth) + 'px';
        container.style.background = 'white';
        container.style.border = '1px solid rgba(0,0,0,0.12)';
        container.style.borderRadius = '6px';
        container.style.boxShadow = '0 6px 18px rgba(0,0,0,0.08)';
        container.style.maxHeight = '260px';
        container.style.overflow = 'auto';
        container.style.padding = '6px 0';
        container.style.fontSize = '0.95rem';
        container.style.display = 'none';
        document.body.appendChild(container);
        return container;
    }

    function positionContainer(input, container) {
        const rect = input.getBoundingClientRect();
        container.style.left = (window.scrollX + rect.left) + 'px';
        container.style.top = (window.scrollY + rect.bottom + 6) + 'px';
        container.style.minWidth = rect.width + 'px';
    }

    function escapeHtml(s){
        return s.replace(/[&<>"']/g, function(c){
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":"&#39;"}[c];
        });
    }

    // Generic live search for items in a container
    function setupLiveSearch(options){
        const input = document.querySelector(options.inputSelector);
        if (!input) return;

        // build items index from DOM unless ajaxUrl is provided
        const items = [];
        const isAjax = !!options.ajaxUrl;
        if (!isAjax) {
            document.querySelectorAll(options.itemSelector).forEach(el => {
                const title = (options.titleGetter ? options.titleGetter(el) : el.textContent || '').trim();
                const subtitle = (options.subtitleGetter ? options.subtitleGetter(el) : '');
                items.push({el, title, subtitle});
            });
        }

        // suggestions container
        const container = createSuggestionsContainer(input);

        function renderSuggestions(list, query){
            container.innerHTML = '';
            if (list.length === 0) {
                container.style.display = 'none';
                return;
            }
            list.forEach(item => {
                const row = document.createElement('div');
                row.className = 'search-suggestion-item';
                row.style.padding = '6px 10px';
                row.style.cursor = 'pointer';
                row.style.display = 'flex';
                row.style.flexDirection = 'column';

                const titleHtml = escapeHtml(item.title).replace(new RegExp('('+escapeRegExp(query)+')','ig'), '<strong>$1</strong>');

                // Show only the title in suggestions (do not display subtitle)
                row.innerHTML = '<span>'+titleHtml+'</span>';
                row.addEventListener('click', function(e){
                    container.style.display = 'none';
                    // Fill the input with the suggestion and apply filter -- do NOT navigate or highlight
                    try {
                        input.value = item.title || '';
                    } catch (err) {
                        // fallback: do nothing
                    }
                    // If a filterItems callback exists, call it immediately with the suggestion
                    if (options.filterItems) {
                        options.filterItems(item.title || '');
                    }
                    // Trigger an input event so any listeners update (debounced handlers will run as well)
                    try {
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                    } catch (e) {
                        // older browsers
                        var ev = document.createEvent('Event'); ev.initEvent('input', true, true); input.dispatchEvent(ev);
                    }
                });
                container.appendChild(row);
            });
            container.style.display = 'block';
        }

        function escapeRegExp(s){
            return s.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        // filter logic (supports ajaxUrl)
        let lastQuery = '';
        const debounce = (fn, wait)=>{
            let t;
            return function(...args){ clearTimeout(t); t = setTimeout(()=> fn.apply(this,args), wait); };
        };

        const onInput = debounce(function(){
            const q = input.value.trim();
            lastQuery = q;
            if (q.length === 0){
                renderSuggestions([], q);
                if (options.filterItems) options.filterItems('');
                return;
            }

            if (isAjax) {
                const url = options.ajaxUrl + '?q=' + encodeURIComponent(q) + '&limit=' + (options.maxResults || 8);
                fetch(url, {credentials: 'same-origin'}).then(r=>r.json()).then(data=>{
                    if (!data || !data.success) return;
                    const list = data.data.map(it=>({
                        id: it.id,
                        title: it.title,
                        subtitle: it.snippet || ''
                    }));
                    renderSuggestions(list, q);
                    // no client-side filterItems required when using ajax, but still call if provided
                    if (options.filterItems) options.filterItems(q);
                    positionContainer(input, container);
                }).catch(err=>{ console.error('Search fetch error', err); });
            } else {
                const qLow = q.toLowerCase();
                let matches;
                if (options.titleOnly) {
                    matches = items.filter(it => it.title && it.title.toLowerCase().indexOf(qLow) !== -1);
                } else {
                    matches = items.filter(it => (it.title && it.title.toLowerCase().indexOf(qLow) !== -1) || (it.subtitle && it.subtitle.toLowerCase().indexOf(qLow) !== -1));
                }
                const results = matches.slice(0, options.maxResults || 8);
                renderSuggestions(results, q);
                if (options.filterItems) options.filterItems(q);
                positionContainer(input, container);
            }
        }, options.debounce || 250);

        input.addEventListener('input', onInput);

        // close on click outside
        document.addEventListener('click', function(e){
            if (!container.contains(e.target) && e.target !== input) {
                container.style.display = 'none';
            }
        });

        // reposition on resize/scroll
        window.addEventListener('resize', ()=> positionContainer(input, container));
        window.addEventListener('scroll', ()=> positionContainer(input, container));
    }

    // Setup for articles on home
    document.addEventListener('DOMContentLoaded', function(){
        // Articles search
        setupLiveSearch({
            inputSelector: '#iSearch',
            itemSelector: '.articles-list .tarja-article',
            titleGetter: (el)=>{
                const h = el.querySelector('h3');
                return h ? h.textContent.trim() : '';
            },
            subtitleGetter: (el)=>{
                const p = el.querySelector('p');
                return p ? p.textContent.trim().slice(0,160) : '';
            },
            maxResults: 8,
            ajaxUrl: 'api/search_articles.php',
            titleOnly: true,
            filterItems: function(q){
                // Show/hide articles based on title only
                document.querySelectorAll('.articles-list .tarja-article').forEach(el=>{
                    const h = el.querySelector('h3');
                    const title = h ? (h.textContent||'').toLowerCase() : '';
                    if (!q || title.indexOf(q.toLowerCase())!==-1) el.style.display = '';
                    else el.style.display = 'none';
                });
            }
        });

        // Users search on user_management page
        setupLiveSearch({
            inputSelector: '#iUserSearch',
            itemSelector: '.users-table tbody tr',
            titleGetter: (el)=> el.querySelectorAll('td')[1] ? el.querySelectorAll('td')[1].textContent.trim() : '',
            subtitleGetter: (el)=> el.querySelectorAll('td')[2] ? el.querySelectorAll('td')[2].textContent.trim() : '',
            maxResults: 10,
            onSelect: function(item){
                // highlight the row and scroll to it
                item.el.scrollIntoView({behavior:'smooth', block:'center'});
                item.el.style.transition = 'background 0.3s';
                const prev = item.el.style.background;
                item.el.style.background = 'rgba(16,128,255,0.08)';
                setTimeout(()=>{ item.el.style.background = prev; }, 900);
            },
            filterItems: function(q){
                document.querySelectorAll('.users-table tbody tr').forEach(el=>{
                    const text = (el.textContent||'').toLowerCase();
                    if (!q || text.indexOf(q.toLowerCase())!==-1) el.style.display = '';
                    else el.style.display = 'none';
                });
            }
        });
    });
})();
