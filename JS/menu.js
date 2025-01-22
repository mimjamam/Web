$(document).ready(function() {
    // Handle form submission
    $('#search-form').on('submit', function(e) {
        e.preventDefault(); // Prevent form from refreshing the page

        // Fetch form data
        const formData = $(this).serialize();

        // Send AJAX request to searchitem.php
        $.ajax({
            url: 'searchitem.php', // Ensure this PHP file processes the request
            type: 'GET', // Send the form data as a GET request
            data: formData,
            success: function(response) {
                // Update the results list with the server response
                $('#results-list').html(response);
            },
            error: function() {
                // Display an error message if the request fails
                $('#results-list').html('<p>Error loading search results. Please try again.</p>');
            }
        });
    });

    // Handle Clear Button Click
    $('#clear-form').on('click', function() {
        // Reset all form fields
        $('#search-form')[0].reset();

        // Clear the search results dynamically
        $('#results-list').html('');
    });

    // Function to load food items
    function loadFoodItems(type, canteen) {
        $.ajax({
            url: 'fetch_food.php',
            type: 'GET',
            data: {
                type: type,
                canteen: canteen
            },
            success: function(response) {
                $('#food-items').html(response); // Update food items dynamically
            },
            error: function() {
                $('#food-items').html('<p>Error loading food items. Please try again.</p>');
            }
        });
    }

    // Load all foods by default
    loadFoodItems('all', 'all');

    // Handle navigation clicks
    $('.menu-link').on('click', function(e) {
        e.preventDefault();
        const type = $(this).data('type');
        const canteen = $(this).data('canteen'); // Get canteen filter
        loadFoodItems(type, canteen); // Load food items based on selected type and canteen
    });
});
