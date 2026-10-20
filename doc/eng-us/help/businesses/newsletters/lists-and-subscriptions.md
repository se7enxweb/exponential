---
title: Lists and subscriptions
audience: businesses, sales-marketing, editor
category: business-solutions
order: 30
summary: How newsletter systems, lists and subscribers fit together in cjw_newsletter, what each subscription status means for a business, how to bring subscribers in and take them out with CSV, and who should be allowed to see subscriber data.
keywords: newsletter list, subscribers, subscription status, newsletter system, CSV import, CSV export, interests, virtual list, subscriber data, permissions
type: help
---
# Lists and subscriptions

This page is for the people who own a newsletter audience: marketing staff, membership officers, managers. It explains
how the `cjw_newsletter` extension organizes subscribers, what the statuses mean in practice, how to move subscribers
in and out, and who should have access. The editor-level details are in
[Newsletter lists and subscriptions](../../creators/website-creators/newsletter-lists-and-subscriptions.md).

## Contents

1. [Systems, lists and subscribers](#systems-lists-and-subscribers)
2. [What the statuses mean](#what-the-statuses-mean)
3. [Where you manage subscribers](#where-you-manage-subscribers)
4. [Importing and exporting](#importing-and-exporting)
5. [Interests and languages](#interests-and-languages)
6. [Who may see subscriber data](#who-may-see-subscriber-data)
7. [A structure that stays simple](#a-structure-that-stays-simple)

## Systems, lists and subscribers

| Level | What it is | Business example |
|---|---|---|
| Newsletter system | Content (class `cjw_newsletter_system`) that groups lists | "Customer newsletters" |
| List | Content (class `cjw_newsletter_list`) that people subscribe to | "Monthly offers", "Trade customers" |
| Newsletter user | One person (an e-mail address) across all lists | The subscriber |
| Subscription | One person on one list, with a status | "Subscribed to Monthly offers, confirmed" |

Systems and lists live in the content tree below the node set in `[NewsletterSettings] RootFolderNodeId`
([cjw_newsletter, The parts](../../../features/6.0/extensions/cjw_newsletter.md#the-parts)). Because they are content,
you can place them, rename them and set permissions on them like any other content.

## What the statuses mean

| Status | Meaning | Receives editions? |
|---|---|---|
| Pending | Subscribed, confirmation mail sent, not yet confirmed | No |
| Confirmed | Clicked the confirmation link | Yes |
| Approved | Confirmed and, where the list requires it, approved by an administrator | Yes |
| Removed by the user or by an administrator | Left the list | No |
| Soft or hard bounced | Mail could not be delivered; after `BounceThresholdValue` bounces (default 3) the user stops receiving | No |
| Blacklisted | The address never gets mail | No |

"Only confirmed or approved subscribers receive editions"
([Newsletter lists and subscriptions](../../creators/website-creators/newsletter-lists-and-subscriptions.md#statuses)).
For a business this means the number that matters is confirmed plus approved, not the total of all rows.

> **Tip**
> A large number of pending subscriptions usually means the confirmation mail does not arrive. Check the transport
> and the spam folder of a test address before blaming the form
> ([Newsletter mail delivery](newsletter-mail-delivery.md)).

## Where you manage subscribers

| Task | Administration view |
|---|---|
| See the subscriptions of one list | `newsletter/subscription_list` |
| Find a person across lists, edit or create one | `newsletter/user_list`, `user_view`, `user_edit`, `user_create` |
| Block an address for good | `newsletter/blacklist_item_list`, `blacklist_item_add` |
| Bounce mailboxes and their messages | `newsletter/mailbox_list`, `mailbox_item_list` |

Since 4.1.17 the user list pages, sorts and filters (before, it showed the first ten users only); the blacklist, the
imports and the bounces have the same filter, sort and paging
([cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md#dashboard-commands-and-background-runs-4117)).

## Importing and exporting

**Import.** Release 4.2.0 imports CSV files with a column mapping, a preview and a dry run. A real import requires a
**consent source**, a sentence that says where and how the people agreed, for example a sign-up form at an event.
The double opt-in mail is not sent for imported people, because the consent was given where the addresses were
collected, and you are responsible for being able to show that consent. Each imported subscription is written to the
site's consent log with the source `import`, and the finished import writes the audit event `data.import.csv`
without the addresses themselves
([importexport.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/importexport.md)).

**Export.** `newsletter/subscriber_export/<list content object id>` exports subscribers with filters on status and
dates. Cells that start with `=`, `+`, `-` or `@` get a leading apostrophe so a spreadsheet does not run them as
formulas, and every download is recorded as the audit event `data.export.csv` (same guide).

> **Warning**
> An export holds personal data. Keep it only as long as the task that needed it, and store it like any other
> personal data. See [Security and data protection](../security/README.md).

**Coming from the old eznewsletter extension.** The command `ext:cjw_newsletter:import-eznewsletter` migrates lists and
subscribers from it (same guide, section "Migration from eznewsletter").

## Interests and languages

A list can let subscribers choose **interests**, and one edition can be sent in several languages, each subscriber
receiving their own language. Subscribers choose both on their preference page
([rendering.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/rendering.md)). For a small organization,
interests are often a better choice than many separate lists: one list, one edition, a block that differs per reader.

For sites in several languages, see also [Multilingual sites](../multilingual/README.md).

## Who may see subscriber data

The newsletter module has its own policy functions in `modules/newsletter/module.php` of the extension, for example
`subscribe`, `configure` and `unsubscribe` for the public forms, `subscription_list` and `user_list` for the
administration, and `import_export` for the import and export of 4.2.0. The extension's guide says to give
`newsletter/import_export` "only to the roles that may see and change subscriber data"
([importexport.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/importexport.md)).

- [ ] Visitors get only the public functions (subscribe, configure, unsubscribe).
- [ ] Writers get what they need to write and test editions, not the subscriber lists.
- [ ] One or two named people get the subscriber lists, import and export.
- [ ] Review these roles when people change jobs ([Access reviews](../security/access-reviews.md)).

How roles and policies work in general: [Roles, policies and unactivated users](../../../guides/roles-and-policies.md).

## A structure that stays simple

- Start with one system and one list per promise to subscribers.
- Use interests rather than many near-identical lists.
- Name lists the way subscribers would describe them; the name appears on the subscribe and preference pages.
- Archive a list that is no longer sent rather than reusing it for a different purpose; subscribers agreed to the old
  purpose.

## Related pages

- [Newsletters for businesses](README.md)
- [Opt-in and consent](opt-in-and-consent.md)
- [Bounces and list hygiene](bounces-and-list-hygiene.md)
- [Newsletter lists and subscriptions (website creators)](../../creators/website-creators/newsletter-lists-and-subscriptions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
