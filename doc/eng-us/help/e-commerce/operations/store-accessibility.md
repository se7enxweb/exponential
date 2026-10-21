---
title: Store accessibility
audience: ecommerce, website-creator, designer
category: e-commerce
order: 72
summary: Making an Exponential store usable for everyone - where the shop's markup comes from, accessible product pages, option fields, basket, checkout forms, error messages, gateway selection and order mail, with a checklist and how to test it.
keywords: accessibility, accessible store, checkout accessibility, forms, labels, error messages, keyboard, screen reader, contrast, product page, basket, shop templates, WCAG, testing accessibility
type: help
---
# Store accessibility

This page is for designers, developers and merchants who want everyone to be able to buy from their store, including
people who use a keyboard, a screen reader or magnification. A checkout that cannot be completed is a lost sale and,
in many places, a legal problem. This page names the parts of the shop's markup you control and what to check in each;
the general guidance is in [Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md).

## Contents

1. [Where the markup comes from](#where-the-markup-comes-from)
2. [Product pages](#product-pages)
3. [Basket](#basket)
4. [Checkout forms](#checkout-forms)
5. [Errors and status messages](#errors-and-status-messages)
6. [Payment and receipts](#payment-and-receipts)
7. [Checklist](#checklist)

## Where the markup comes from

All shop pages are templates you can override in your design
([Template overrides](../../creators/website-creators/template-overrides.md)):

| Page | Template (standard design) |
|---|---|
| Product full view with add to basket | your design's product template |
| Basket | `shop/basket.tpl` (or `shop/<BasketViewName>.tpl`) |
| Customer data | `shop/userregister.tpl`, `shop/register.tpl` |
| Confirmation | `shop/confirmorder.tpl` |
| Gateway selection | `workflow/selectgateway.tpl` |
| Checkout problems | `shop/checkoutagain.tpl`, `shop/cancelcheckout.tpl` |
| Order and receipt | `shop/orderview.tpl`, `shop/orderreceipt.tpl` |
| Order mail | `shop/orderemail.tpl` |
| Country select | `shop/country/edit.tpl` |

Check the templates your theme actually uses, not only the standard ones; the shipped themes carry their own shop
templates ([Running a shop: overview](../../creators/website-creators/shop-overview.md)).

## Product pages

- The price is text, with the currency, not only a graphic.
- The **Add to basket** control is a real button with a text label.
- Option fields (size, color) each have a visible label tied to the control; extra prices are in the label text.
- Product images have text alternatives that describe the product; decorative images have empty alternatives.

## Basket

- The basket is a table with header cells for product, quantity, price and total.
- Quantity fields have labels naming the product ("Quantity of Blue shirt").
- Remove controls say what they remove.
- A change to the basket count in the page header is announced, or the customer is taken to the basket
  (`RedirectAfterAddToBasket=basket`), so the change is not silent.

## Checkout forms

- Every field has a visible label; required fields are marked in text, not only by color.
- Fields have the right input types and autocomplete hints for name, e-mail and address.
- The country select is usable with a keyboard; the shipped select lists the most used countries first
  ([Country groups and regions](../../businesses/stores/country-groups-and-regions.md)).
- A custom shop account handler's form follows the same rules ([Shop account handlers](../extending/shop-account-handlers.md)).

## Errors and status messages

- Errors such as an invalid quantity or removed items (the `removed_items` list of the basket) are shown in text near
  the top, with focus moved there or announced.
- `shop/checkoutagain.tpl` and `shop/cancelcheckout.tpl` explain in plain words what happened and what to do next.

## Payment and receipts

- On the gateway selection page each gateway is a labeled choice.
- A redirect to the provider is announced on the button ("Continue to PayPal").
- The receipt has a **Print** view of the receipt alone and a **Download** as a self-contained HTML file
  ([Permanent order receipts](../../../features/6.0/order-receipts.md)); check both with a screen reader.
- An HTML order mail has a sensible reading order and alternatives for images.

## Checklist

- [ ] The full path, product to receipt, with the keyboard only.
- [ ] The same path with a screen reader.
- [ ] Zoom to 200 percent: nothing overlaps or needs horizontal scrolling.
- [ ] Contrast of prices, buttons and error messages.
- [ ] Error messages for every form field.
- [ ] Tested with the methods in [Testing accessibility](../../creators/website-creators/testing-accessibility.md).

## Related pages

- [Operating a store](README.md)
- [Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)
- [Testing a store](testing-a-store.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
