jQuery(document).ready(function ($) {
    $('#city-search').on('keyup', function (e) {
        const query = $(this).val().trim();

        if (query.length < 3) {
            $('#results').html('<p>Please enter at least 3 characters to search.</p>');
            return;
        }

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'search_cities',
                query: query,
            },
            success: function (response) {
                $('#results').html(response);
            },
            error: function () {
                $('#results').html('<p>An error occurred while searching.</p>');
            },
        });

        e.preventDefault();
    });
});
