document.addEventListener('DOMContentLoaded', function () {
    const filterInputs = document.querySelectorAll('.column-filter-text');
    let debounceTimer;

    filterInputs.forEach(input => {
        const eventName = input.dataset.event ?? 'input';
        const minLength = parseInt(input.dataset.minLength ?? '1', 10);

        input.addEventListener(eventName, function () {
            clearTimeout(debounceTimer);
            const focusedInput = this;

            debounceTimer = setTimeout(() => {
                const filterValue = this.value.trim();
                const columnName = this.dataset.column;

                const url = new URL(window.location.href);
                const searchParams = new URLSearchParams(url.search);

                if (filterValue.length >= minLength) {
                    searchParams.set(`filters[${columnName}]`, filterValue);
                } else {
                    searchParams.delete(`filters[${columnName}]`);
                }

                url.search = searchParams.toString();

                fetch(url.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                })
                    .then(response => response.text())
                    .then(html => {
                        const doc = new DOMParser().parseFromString(html, 'text/html');

                        const newTbody = doc.querySelector('tbody');
                        const newPagination = doc.querySelector('.mt-3');
                        const currentTbody = document.querySelector('tbody');
                        const currentPagination = document.querySelector('.mt-3');

                        if (newTbody) {
                            currentTbody.innerHTML = newTbody.innerHTML;
                        }

                        if (newPagination && currentPagination) {
                            currentPagination.innerHTML = newPagination.innerHTML;
                        }

                        window.history.replaceState({}, '', url.toString());
                        focusedInput.focus();
                    });
            }, 300);
        });
    });
});
