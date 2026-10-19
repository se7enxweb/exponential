---
title: Store launch checklist
audience: businesses, project-manager, website-creator
category: business-solutions
order: 220
summary: Everything to decide, configure and test before an Exponential store takes its first real order - catalog, prices and VAT, shipping, checkout, payment, mail and receipts, staff and permissions, security, performance and launch day.
keywords: store launch, go-live, launch checklist, webshop launch, test order, payment test, opening a store, pre-launch
type: help
---
# Store launch checklist

This page is for the business owner and the project manager who open a store. Work through it with the person who
built the store, a few weeks before launch. Each line names the page with the details. The general website checklist is
[The launch checklist](../../creators/website-creators/launch-checklist.md); this page adds what a store needs.

## Contents

1. [Decisions on paper](#decisions-on-paper)
2. [Catalog](#catalog)
3. [Prices tax and shipping](#prices-tax-and-shipping)
4. [Checkout and customers](#checkout-and-customers)
5. [Payment](#payment)
6. [Mail and receipts](#mail-and-receipts)
7. [Staff and permissions](#staff-and-permissions)
8. [Security and data](#security-and-data)
9. [Performance and operations](#performance-and-operations)
10. [Test orders](#test-orders)
11. [Launch day](#launch-day)
12. [The first week](#the-first-week)

## Decisions on paper

- [ ] The planning worksheet is filled in ([Store planning for small businesses](store-planning-for-small-businesses.md#a-planning-worksheet)).
- [ ] Terms of sale, shipping, returns and privacy texts are written and approved by whoever advises you legally.
- [ ] Tax rates confirmed by your tax adviser.
- [ ] Missing features (stock, invoices, labels, coupons) have an answer: extension, integration or manual process
  ([What the built-in shop does](what-the-built-in-shop-does.md#what-is-not-included)).

## Catalog

- [ ] Every product has a name, product number, price, image and description ([Product classes and catalog](product-classes-and-catalog.md)).
- [ ] The dashboard shows no products without a price ([Store dashboard and reports](store-dashboard-and-reports.md)).
- [ ] Categories, menus and tags work; search finds products by name and number ([Store search and navigation](store-search-and-navigation.md)).

## Prices tax and shipping

- [ ] The shop currency is set in an override; other currencies active only if wanted ([Prices and currencies](prices-and-currencies.md)).
- [ ] Rounding gives the price endings you expect.
- [ ] VAT types named and correct; VAT rules for each country where the rate differs ([VAT rules and VAT types](vat-rules-and-vat-types.md)).
- [ ] The dashboard shows no unexpected 0 percent VAT type.
- [ ] Shipping cost appears on the confirmation page ([Shipping and handling](shipping-and-handling.md)).
- [ ] The country list ranks your markets first ([Country groups and regions](country-groups-and-regions.md)).

## Checkout and customers

- [ ] The shop account handler matches your choice of guest or account checkout ([Customer accounts and guest checkout](customer-accounts-and-guest-checkout.md)).
- [ ] The checkout form asks for every address field your carrier needs.
- [ ] `RedirectAfterAddToBasket` and the basket's address name are what you want ([Basket and checkout flow](basket-and-checkout-flow.md)).
- [ ] Basket, checkout and account pages are not cached by any CDN or proxy ([Store performance at peak times](store-performance-at-peak-times.md#pages-that-cannot)).
- [ ] Basket, checkout and account pages are kept out of search engines ([robots.txt](../../creators/website-creators/robots-txt.md)).

## Payment

- [ ] The gateway is active and connected to the `shop_checkout` trigger; the dashboard does not warn about a missing
  payment step ([Payment gateways for businesses](payment-gateways-for-businesses.md)).
- [ ] The gateway sets the order status you use for paid orders ([PayPal payments](paypal-payments.md#order-status-after-payment)).
- [ ] The payment notification address is reachable from the internet without a login.
- [ ] With an on-site card form: card data handling reviewed ([Card payments and PCI basics](card-payments-and-pci-basics.md)).

## Mail and receipts

- [ ] `SendOrderEmail` enabled; `AdminEmail` and `EmailSender` set to real addresses ([Order receipts and confirmation mail](order-receipts-and-confirmation-mail.md)).
- [ ] The order mail template has your contact details and terms.
- [ ] Permanent receipts turned on if guests buy (`OrderLinkView=orderreceipt`).
- [ ] Test mail arrives in the inbox, not in spam ([Mail delivery](../../hosting/operations/mail-delivery.md)).

## Staff and permissions

- [ ] Each staff member has a personal account with two-step sign-in.
- [ ] Roles follow the shop functions: `buy`, `administrate`, `setup`, `setstatus`
  ([Orders and order statuses](orders-and-order-statuses.md#who-may-change-a-status)).
- [ ] The status path is written down and everyone has seen it.
- [ ] Someone is named for orders on every day the store is open, including weekends.

## Security and data

- [ ] The [Store security checklist](store-security-checklist.md) is completed.
- [ ] Privacy notice published; marketing opt-in is separate from buying ([Opt-in and consent](../newsletters/opt-in-and-consent.md)).
- [ ] Backups run and a restore has been tested ([Backups that restore](../reliability/backups-that-restore.md)).

## Performance and operations

- [ ] Cron jobs run, including basket cleanup ([Cron jobs](../../../guides/cronjobs.md)).
- [ ] A benchmark baseline is saved ([Store performance at peak times](store-performance-at-peak-times.md#measure-before-the-peak)).
- [ ] Monitoring checks the home page, a product page and the basket ([Health checks and uptime](../../hosting/operations/health-checks-and-uptime.md)).
- [ ] Maintenance mode is known and tested, for planned work later ([Maintenance mode](../../../features/6.0/maintenance-mode.md)).

## Test orders

Place these in the payment provider's test mode, on a desktop and on a phone
([Basket, checkout and orders](../../creators/website-creators/basket-checkout-and-orders.md#test-order-checklist)):

- [ ] Guest order, one product, standard country.
- [ ] Signed-in customer, several products with options, a second country.
- [ ] Order in a second currency, if you offer one.
- [ ] Customer in a discount group; the discount appears.
- [ ] Canceled payment; the customer can try again.
- [ ] Cancel on the confirmation page; the basket is kept.
- [ ] Each order appears in Store with the right status; mails arrive; the receipt link works and prints.
- [ ] Staff move one order through the whole status path.

## Launch day

1. Switch the payment gateway from test to live (server address, account, test mode off).
2. Remove test orders from the active list (archive them) so the dashboard starts clean.
3. Place one small real order with a real payment, then refund it in the provider's account and set the order to
   Refunded.
4. Watch the dashboard, the order mail inbox and the logs for the first hours.
5. Announce the store ([Promoting your store](promoting-your-store.md)).

## The first week

- [ ] Every order handled within the time you promised.
- [ ] Dashboard "Next steps" reviewed daily.
- [ ] Customer questions collected for the FAQ ([Store FAQ](store-faq.md)).
- [ ] Day-to-day routine agreed ([Running a store day to day](running-a-store-day-to-day.md)).

A general go-live template is in Go-live checklist.

## Related pages

- [The launch checklist](../../creators/website-creators/launch-checklist.md)
- [Going live: hosts and HTTPS](../../creators/website-creators/going-live-hosts-https.md)
- [New customer site go-live](../../hosting/checklists/new-customer-site-go-live.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
