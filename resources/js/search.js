const MIN_QUERY_LENGTH = 2;
const DEBOUNCE_MS = 250;
const SKELETON_ROWS = 3;

function debounce(callback, wait) {
    let timeout;

    const debounced = (...args) => {
        window.clearTimeout(timeout);
        timeout = window.setTimeout(() => callback(...args), wait);
    };

    debounced.cancel = () => window.clearTimeout(timeout);

    return debounced;
}

function appendHighlighted(element, text, term) {
    const index = text.toLowerCase().indexOf(term.toLowerCase());

    if (index === -1) {
        element.textContent = text;

        return;
    }

    const match = document.createElement('mark');

    match.className = 'rounded bg-emerald-100 px-0.5 font-semibold text-emerald-900';
    match.textContent = text.slice(index, index + term.length);

    element.append(
        document.createTextNode(text.slice(0, index)),
        match,
        document.createTextNode(text.slice(index + term.length)),
    );
}

function createRow({ key, url, initials, title, meta, trailing, onActivate }, term) {
    const link = document.createElement('a');

    link.href = url;
    link.id = `search-option-${key}`;
    link.setAttribute('role', 'option');
    link.className = 'flex items-center gap-3 rounded-xl px-3 py-2 transition-colors hover:bg-emerald-50';
    link.dataset.searchOption = '';

    if (initials) {
        const avatar = document.createElement('span');

        avatar.setAttribute('aria-hidden', 'true');
        avatar.className = 'flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-stone-100 text-base font-bold text-stone-500';
        avatar.textContent = initials;

        link.append(avatar);
    }

    const body = document.createElement('span');

    body.className = 'min-w-0 flex-1';

    const heading = document.createElement('span');

    heading.className = 'block truncate text-sm font-medium text-stone-800';

    appendHighlighted(heading, title, term);

    body.append(heading);

    if (meta) {
        const subheading = document.createElement('span');

        subheading.className = 'block truncate text-xs text-stone-500';
        subheading.textContent = meta;

        body.append(subheading);
    }

    link.append(body);

    if (trailing) {
        const badge = document.createElement('span');

        badge.className = 'shrink-0 whitespace-nowrap text-xs font-semibold text-stone-500';
        badge.textContent = trailing;

        link.append(badge);
    }

    link.addEventListener('click', onActivate);

    return link;
}

function createGroup(label, rows) {
    const group = document.createElement('div');

    group.setAttribute('role', 'group');
    group.setAttribute('aria-label', label);

    const heading = document.createElement('p');

    heading.className = 'px-3 pb-1 pt-3 text-xs font-semibold uppercase tracking-wide text-stone-400';
    heading.textContent = label;

    group.append(heading, ...rows);

    return group;
}

function createSearch(root) {
    const endpoint = root.dataset.searchEndpoint;
    const input = root.querySelector('[data-search-input]');
    const panel = root.querySelector('[data-search-panel]');
    const list = root.querySelector('[data-search-list]');
    const status = root.querySelector('[data-search-status]');
    const allResults = root.querySelector('[data-search-all]');
    const clearButton = root.querySelector('[data-search-clear]');

    let options = [];
    let activeIndex = -1;
    let pending = null;
    let latest = 0;

    const open = () => {
        panel.hidden = false;
        input.setAttribute('aria-expanded', 'true');
    };

    const close = () => {
        panel.hidden = true;
        input.setAttribute('aria-expanded', 'false');
        setActive(-1);
    };

    const setActive = (index) => {
        const previous = options[activeIndex];

        if (previous) {
            previous.removeAttribute('aria-selected');
            previous.classList.remove('bg-emerald-50');
        }

        activeIndex = index;

        const current = options[index];

        if (!current) {
            input.removeAttribute('aria-activedescendant');

            return;
        }

        current.setAttribute('aria-selected', 'true');
        current.classList.add('bg-emerald-50');
        input.setAttribute('aria-activedescendant', current.id);
        current.scrollIntoView({ block: 'nearest' });
    };

    const renderMessage = (message) => {
        const empty = document.createElement('p');

        empty.className = 'px-3 py-6 text-center text-sm text-stone-500';
        empty.textContent = message;

        list.replaceChildren(empty);
    };

    const renderSkeleton = () => {
        const placeholders = Array.from({ length: SKELETON_ROWS }, () => {
            const placeholder = document.createElement('div');

            placeholder.className = 'shimmer-bg mx-3 my-2 h-11 rounded-xl';

            return placeholder;
        });

        list.replaceChildren(...placeholders);
    };

    const render = (payload) => {
        const term = payload.query;
        const products = payload.products.map((product) => createRow({
            key: `product-${product.id}`,
            url: product.url,
            initials: product.name.charAt(0).toUpperCase(),
            title: product.name,
            meta: [product.category, product.brand].filter(Boolean).join(' \u00b7 '),
            trailing: product.price,
            onActivate: close,
        }, term));
        const suggestions = (items) => items.map((suggestion) => createRow({
            key: `${suggestion.type}-${suggestion.label}`,
            url: suggestion.url,
            title: suggestion.label,
            meta: suggestion.meta,
            trailing: suggestion.type,
            onActivate: close,
        }, term));
        const categories = suggestions(payload.categories);
        const brands = suggestions(payload.brands);
        const tags = suggestions(payload.tags);

        options = [...products, ...categories, ...brands, ...tags];

        setActive(-1);

        if (options.length === 0) {
            allResults.hidden = true;
            renderMessage(payload.tooShort
                ? `Keep typing \u2014 ${MIN_QUERY_LENGTH} characters or more.`
                : `No matches for \u201c${term}\u201d.`);
            status.textContent = payload.tooShort ? '' : `No matches for ${term}.`;
            open();

            return;
        }

        list.replaceChildren(
            ...[
                ['Products', products],
                ['Categories', categories],
                ['Brands', brands],
                ['Tags', tags],
            ]
                .filter(([, rows]) => rows.length > 0)
                .map(([label, rows]) => createGroup(label, rows)),
        );

        allResults.textContent = `See all results for \u201c${term}\u201d`;
        allResults.href = payload.resultsUrl;
        allResults.hidden = false;

        status.textContent = `${options.length} result${options.length === 1 ? '' : 's'} for ${term}.`;

        open();
    };

    const reset = () => {
        search.cancel();
        latest += 1;

        if (pending) {
            pending.abort();
            pending = null;
        }

        list.replaceChildren();
        options = [];
        setActive(-1);
        close();
        allResults.hidden = true;
        status.textContent = '';
    };

    const search = debounce((term) => {
        if (pending) {
            pending.abort();
            pending = null;
        }

        const token = (latest += 1);

        if (term.length < MIN_QUERY_LENGTH) {
            render({ query: term, tooShort: true, products: [], categories: [], brands: [], tags: [] });

            return;
        }

        pending = new AbortController();
        renderSkeleton();
        open();

        window.axios
            .get(endpoint, { params: { q: term }, signal: pending.signal })
            .then(({ data }) => {
                if (token === latest) {
                    render(data);
                }
            })
            .catch((error) => {
                if (window.axios.isCancel(error) || token !== latest) {
                    return;
                }

                renderMessage('Search is unavailable right now.');
                status.textContent = 'Search is unavailable right now.';
                allResults.hidden = true;
            })
            .finally(() => {
                if (token === latest) {
                    pending = null;
                }
            });
    }, DEBOUNCE_MS);

    input.addEventListener('input', () => {
        const term = input.value.trim();

        clearButton.hidden = term === '';

        if (term === '') {
            reset();

            return;
        }

        search(term);
    });

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            close();

            return;
        }

        if (event.key === 'Enter' && activeIndex > -1 && options[activeIndex]) {
            event.preventDefault();
            options[activeIndex].click();

            return;
        }

        if (options.length === 0) {
            return;
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            setActive((activeIndex + 1) % options.length);
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            setActive(activeIndex <= 0 ? options.length - 1 : activeIndex - 1);
        }
    });

    document.addEventListener('click', (event) => {
        if (!root.contains(event.target)) {
            close();
        }
    });

    clearButton.addEventListener('click', () => {
        input.value = '';
        input.focus();
        reset();
    });

    if (input.value.trim() !== '') {
        clearButton.hidden = false;
    }
}

export function initSearch() {
    document.querySelectorAll('[data-search]').forEach(createSearch);
}
