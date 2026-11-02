// Task 20: live search suggestions using fetch() (AJAX), no page reload
(function () {
    const box  = document.getElementById('search-box');
    const list = document.getElementById('suggestions');
    if (!box || !list) return;

    let timer = null;
    let controller = null;

    function hide() {
        list.hidden = true;
        list.innerHTML = '';
    }

    function render(items) {
        list.innerHTML = '';
        if (!items.length) { hide(); return; }

        items.forEach(function (item) {
            const li = document.createElement('li');
            // textContent (not innerHTML) so product names can never inject HTML/JS
            li.textContent = item.name + '  —  ' + item.category;
            li.addEventListener('mousedown', function (ev) {
                ev.preventDefault();
                box.value = item.name;
                hide();
                box.form.submit();
            });
            list.appendChild(li);
        });
        list.hidden = false;
    }

    box.addEventListener('input', function () {
        clearTimeout(timer);
        const q = box.value.trim();
        if (q === '') { hide(); return; }

        // debounce: wait 250 ms after the last keystroke
        timer = setTimeout(function () {
            if (controller) controller.abort(); // cancel the previous in-flight request
            controller = new AbortController();

            fetch('suggest.php?q=' + encodeURIComponent(q), { signal: controller.signal })
                .then(function (res) {
                    if (!res.ok) throw new Error('HTTP ' + res.status);
                    return res.json();
                })
                .then(render)
                .catch(function (err) {
                    if (err.name !== 'AbortError') hide();
                });
        }, 250);
    });

    box.addEventListener('blur', hide);
    document.addEventListener('keydown', function (ev) {
        if (ev.key === 'Escape') hide();
    });
})();
