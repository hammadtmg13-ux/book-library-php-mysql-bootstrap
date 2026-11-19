// BookNest - client-side helpers

function confirmDelete() {
    return confirm('Are you sure you want to delete this book from your library?');
}

// Keep the search box focused behaviour simple: when typing a search,
// reset the genre and status filters so results are not hidden.
document.addEventListener('DOMContentLoaded', function () {
    var searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            var genreSelect = searchInput.form.querySelector('select[name="genre"]');
            var statusSelect = searchInput.form.querySelector('select[name="status"]');
            if (genreSelect) genreSelect.value = 'all';
            if (statusSelect) statusSelect.value = 'all';
        });
    }
});
