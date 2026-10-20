---
title: GDPR requests
audience: customer, admin, businesses
category: customer-success
order: 200
summary: How a store team handles requests from people about their personal data - see, correct, export, erase, stop marketing - with the tools Exponential offers for each, who does what, and a routine that keeps proof. Not legal advice.
keywords: GDPR, data subject request, access request, erasure, right to be forgotten, data export, correction, objection, marketing, privacy, store customers, not legal advice
type: help
---
# GDPR requests

This page is for the owner, or the person who answers privacy requests, in an organization that sells with
Exponential. It gives a routine for requests from customers about their personal data, and for each kind of request
the tools the software offers. The outline of the regulation is on [GDPR basics](../../businesses/security/gdpr-basics.md),
and the map of where data is stored on [Personal data in Exponential](../../businesses/security/personal-data-in-exponential.md).

> **Warning**
> Not legal advice. Whether and how you must answer a request, in what time, and what you may keep, is for your
> lawyer or data protection officer to decide. This page only says what the software can do.

## Who does what

| Step | Who |
|---|---|
| Receives the request and logs it | The person named in your privacy notice |
| Confirms the person is who they say | That person, by a method you agreed with your adviser |
| Decides what is answered, kept or erased | Owner, with the adviser |
| Finds and exports or erases the data | Administrator or partner, with the right permissions |
| Answers the person and closes the log entry | The person who received it |

Use the [GDPR request log template](gdpr-request-log-template.md) for every request.

## The kinds of request and the tools

| Request | What the software offers | Source |
|---|---|---|
| See their data | For mail data: the person's own download, or `./console exp:mail:preferences export`; audit records about a user: `exp:audit export --subject-user=<id>`; orders: **Store > Customers** and the customer order view; receipts can be downloaded as a file | [Compliance and GDPR](../../hosting/security/compliance-and-gdpr.md), `kernel/shop/module.php` |
| Correct their data | The customer edits their own account; staff edit the user in **Users** | [Accounts and sign-in](../../accounts-and-sign-in.md) |
| Take their data elsewhere | "Download my e-mail data" in JSON and CSV for mail data | [GDPR basics](../../businesses/security/gdpr-basics.md) |
| Erase their data | Mail data: `exp:mail:preferences erase`; the account: removing the user (final, and erases mail preferences); form data: **Setup > Collected information**; orders: archive or remove | [Data export and erasure](data-export-and-erasure.md) |
| Stop marketing | The "stop all optional e-mail" link, or `exp:mail:suppression add` | [Consent and forms](../../businesses/security/consent-and-forms.md) |

Order data, newsletter subscriber records in `cjw_newsletter`, and copies in backups are not covered by a single
command ([GDPR basics](../../businesses/security/gdpr-basics.md)). Plan each of them with your adviser and host.

## Orders are special

Orders hold the customer's name, e-mail address and the address entered at checkout
([Personal data in Exponential](../../businesses/security/personal-data-in-exponential.md)). At the same time,
bookkeeping and tax rules may require you to keep order records for years. Decide with your adviser, before the
first request arrives, what you do with orders when someone asks for erasure:

- keep them for the period your bookkeeping rules require, and say so in the answer;
- or archive them (`shop/archiveorder`) so they leave the daily lists;
- or remove them (`shop/removeorder`) when no rule requires keeping them.

## A routine

1. **Log** the request on the day it arrives, with the date and the kind of request.
2. **Confirm** the person's identity in the way you agreed.
3. **Find** the data: account, orders (by e-mail in the customer order view), form submissions, mail preferences,
   newsletter records, and anything your own content classes add.
4. **Decide** with the owner and, if needed, the adviser.
5. **Act**: export, correct or erase as decided.
6. **Answer** the person.
7. **Clean up**: delete export files once the request is done; they hold personal data
   ([Data retention and deletion](../../businesses/security/data-retention-and-deletion.md)).
8. **Close** the log entry with the date and what was done.

> **Tip**
> Give the permissions for exports and audit reading, `mailpreferences/export` and `audit/read`, to few people, as the
> hosts' compliance page advises ([Compliance and GDPR](../../hosting/security/compliance-and-gdpr.md)).

## The audit trail

The audit trail is a hash chain, so single records are not deleted; personal fields in its index are pseudonymized
after a set time ([Data retention and deletion](../../businesses/security/data-retention-and-deletion.md)). Whether
that answers an erasure request in your case is a legal question for your adviser.

## Related pages

- [GDPR request log template](gdpr-request-log-template.md)
- [Data export and erasure](data-export-and-erasure.md)
- [GDPR basics](../../businesses/security/gdpr-basics.md)
- [Personal data in Exponential](../../businesses/security/personal-data-in-exponential.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
