/*
 * An order's receipt (shop/orderreceipt.tpl): the print button, and the printing itself. A file of its own, so the
 * page runs under a Content Security Policy without 'unsafe-inline' (doc/en/us/features/6.0/content-security-policy.md).
 *
 * Printing: the receipt is lifted out to be a direct child of <body> and everything else is hidden, whatever the
 * site design's layout (fixed headers, positioned wrappers); put back afterwards. Without scripts the print styles
 * alone still hide the site around it.
 */
(function () {
    var receipt = document.querySelector('.order-receipt'), home = null, next = null;
    if (!receipt) return;
    var button = receipt.querySelector('.order-receipt-print');
    if (button) {
        button.addEventListener('click', function () {
            window.print();
        });
    }
    window.addEventListener('beforeprint', function () {
        home = receipt.parentNode; next = receipt.nextSibling;
        document.body.appendChild(receipt);
        document.body.classList.add('order-receipt-printing');
    });
    window.addEventListener('afterprint', function () {
        document.body.classList.remove('order-receipt-printing');
        if (home) home.insertBefore(receipt, next);
    });
})();
