$(document).ready(function() {
    // Function to dynamically load the cart content
    function loadCart() {
        $.ajax({
            url: 'cart.php', // PHP script to fetch cart content
            type: 'GET',
            success: function(response) {
                $('#cart-container').html(response); // Load response into the cart container
            },
            error: function() {
                $('#cart-container').html('<p>Error loading cart. Please try again.</p>');
            }
        });
    }

    // Handle Add to Cart button click
    $(document).on('click', '.add-to-cart', function(e) {
        e.preventDefault();

        const itemId = $(this).data('id'); // Get item ID from the button

        $.ajax({
            url: 'add_to_cart.php', // PHP script to handle adding items to the cart
            type: 'POST',
            data: {
                id: itemId
            },
            success: function(response) {
                const res = JSON.parse(response);
                if (res.status === 'success') {
                    alert(res.message); // Show success message
                    loadCart(); // Dynamically reload the cart
                } else {
                    alert(res.message); // Show error message
                }
            },
            error: function() {
                alert('Failed to add item to cart. Please try again.');
            }
        });
    });

    // Handle quantity changes dynamically
    $(document).on('click', '.quantity-controls button', function() {
        const $parent = $(this).closest('.cart-item');
        const $quantityInput = $parent.find('.item-quantity');
        const itemId = $parent.data('id');
        let quantity = parseInt($quantityInput.val());

        // Increment or decrement quantity
        if ($(this).hasClass('increase-quantity')) {
            quantity += 1;
        } else if ($(this).hasClass('decrease-quantity') && quantity > 0) {
            quantity -= 1;
        }

        $quantityInput.val(quantity);

        // Update the cart in the session
        $.ajax({
            url: 'cart.php', // Use the same cart.php to handle updates
            type: 'POST',
            data: {
                id: itemId,
                quantity: quantity
            },
            success: function() {
                loadCart(); // Refresh the cart content after updating
            },
            error: function() {
                alert('Failed to update cart. Please try again.');
            }
        });
    });

    // Load cart content dynamically on page load
    loadCart();
});

/**
 * Show the specified section and hide all others
 * @param {string} sectionId - The ID of the section to show
 */
function showSection(sectionId) {
    // Get all content sections
    const sections = document.querySelectorAll('.content-section');

    // Hide all sections
    sections.forEach(section => {
        section.classList.remove('active');
    });

    // Show the selected section
    document.getElementById(sectionId).classList.add('active');
}
