document.addEventListener('DOMContentLoaded', function() {
    var searchForm = document.getElementById('searchForm');
    var searchInput = searchForm.querySelector('input[name="search"]');

    searchInput.addEventListener('keydown', function(event) {
        if (event.keyCode === 13) { // Enter key code is 13
            event.preventDefault(); // Prevent form submission
            searchForm.submit(); // Trigger form submission
        }
    });
});