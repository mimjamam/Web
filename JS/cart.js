let discountRate = 0;

function updateTotals() {
    let subtotal = 0;
    $('.cart-item').each(function () {
        const price = parseFloat($(this).find('.item-price').text());
        const quantity = parseInt($(this).find('.item-quantity').val());
        const itemTotal = price * quantity;

        $(this).find('.item-total').text(itemTotal.toFixed(2));
        subtotal += itemTotal;
    });
    const discountedTotal = subtotal - (subtotal * discountRate);
    $('#subtotal').text(subtotal.toFixed(2));
    $('#total').text(discountedTotal.toFixed(2));
    $('#total-input').val(discountedTotal.toFixed(2)); // Update the hidden input field
}

// Apply voucher
$('#apply-voucher').on('click', function () {
    const voucherCode = $('#voucher-code').val();
    $.ajax({
        url: 'validate_voucher.php',
        type: 'POST',
        data: { code: voucherCode },
        success: function (response) {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                discountRate = res.discount / 100;
                $('#discount-message').text(`Voucher applied! ${res.discount}% discount.`);
            } else {
                discountRate = 0;
                $('#discount-message').text(res.message);
            }
            updateTotals(); // Update totals after applying the voucher
        }
    });
});

// Initial calculation
updateTotals();

$(document).ready(function() {
    let discountRate = 0;

    function updateCartSummary() {
        let subtotal = 0;
        $('.cart-item').each(function () {
            const price = parseFloat($(this).find('.item-price').text());
            const quantity = parseInt($(this).find('.item-quantity').val());
            const itemTotal = price * quantity;

            $(this).find('.item-total').text(itemTotal.toFixed(2));
            subtotal += itemTotal;
        });
        const discountedTotal = subtotal - (subtotal * discountRate);
        $('#subtotal').text(subtotal.toFixed(2));
        $('#total').text(discountedTotal.toFixed(2));
    }

    // Apply voucher
    $('#apply-voucher').on('click', function () {
        const voucherCode = $('#voucher-code').val();
        $.ajax({
            url: 'validate_voucher.php',
            type: 'POST',
            data: { code: voucherCode },
            success: function (response) {
                const res = JSON.parse(response);
                if (res.status === 'success') {
                    discountRate = res.discount / 100;
                    $('#discount-message').text(`Voucher applied! ${res.discount}% discount.`);
                } else {
                    discountRate = 0;
                    $('#discount-message').text(res.message);
                }
                updateCartSummary();
            },
            error: function () {
                $('#discount-message').text('Failed to validate voucher.');
            }
        });
    });

    // Ensure no duplicate event handlers
    $(document).on('click', '.increase-quantity', function () {
        const $quantityInput = $(this).siblings('.item-quantity');
        const currentQuantity = parseInt($quantityInput.val()) || 0;
        const newQuantity = currentQuantity + 1;

        $quantityInput.val(newQuantity).trigger('change');
    });

    $(document).on('click', '.decrease-quantity', function () {
        const $quantityInput = $(this).siblings('.item-quantity');
        const currentQuantity = parseInt($quantityInput.val()) || 0;
        const newQuantity = Math.max(0, currentQuantity - 1);

        $quantityInput.val(newQuantity).trigger('change');
    });

    $(document).on('change', '.item-quantity', function () {
        const $cartItem = $(this).closest('.cart-item');
        const itemId = $cartItem.data('id');
        const newQuantity = parseInt($(this).val());

        // Ensure only valid numeric quantities are processed
        if (isNaN(newQuantity) || newQuantity < 0) {
            alert('Invalid quantity!');
            return;
        }

        // Send AJAX request to update the cart on the server
        $.ajax({
            url: 'cart.php',
            type: 'POST',
            data: { id: itemId, quantity: newQuantity },
            success: function (response) {
                const res = JSON.parse(response);
                if (res.status === 'redirect') {
                    window.location.href = res.url;
                } else if (res.status === 'success') {
                    updateCartSummary();
                    if ($('.cart-item').length === 0) {
                        window.location.href = 'loghome.php';
                    }
                }
            },
            error: function () {
                console.error('Failed to update quantity.');
            }
        });
    });

    $(document).on('click', '.remove-item', function () {
        const $cartItem = $(this).closest('.cart-item');
        const itemId = $cartItem.data('id');

        $.ajax({
            url: 'cart.php',
            type: 'POST',
            data: { id: itemId, quantity: 0 },
            success: function (response) {
                const res = JSON.parse(response);
                if (res.status === 'redirect') {
                    window.location.href = res.url;
                } else if (res.status === 'success') {
                    $cartItem.remove();
                    updateCartSummary();
                    if ($('.cart-item').length === 0) {
                        window.location.href = 'loghome.php';
                    }
                }
            },
            error: function () {
                console.error('Failed to remove item.');
            }
        });
    });
});
