---
title: Bounces and list hygiene
audience: businesses, admin, sales-marketing
category: business-solutions
order: 60
summary: Why a clean newsletter list protects a business's sender reputation, and how cjw_newsletter keeps it clean - hard and soft bounces, retries, the bounce threshold, the blacklist, the site's suppression list, complaints, the suppression import and a monthly hygiene routine.
keywords: bounce, hard bounce, soft bounce, complaint, blacklist, suppression list, list hygiene, sender reputation, bounce mailbox, BounceThresholdValue, retries
type: help
---
# Bounces and list hygiene

This page is for whoever is responsible for a newsletter's results: marketing managers, administrators and hosting
partners. It explains in plain terms why returned mail matters, what `cjw_newsletter` does with it automatically, and
the small routine that keeps a list healthy.

## Contents

1. [Why hygiene matters to a business](#why-hygiene-matters-to-a-business)
2. [The words](#the-words)
3. [How returned mail reaches the newsletter](#how-returned-mail-reaches-the-newsletter)
4. [What happens to each kind](#what-happens-to-each-kind)
5. [The blacklist and the suppression list](#the-blacklist-and-the-suppression-list)
6. [Importing addresses that must never be mailed](#importing-addresses-that-must-never-be-mailed)
7. [The dashboard and the repair command](#the-dashboard-and-the-repair-command)
8. [A monthly hygiene routine](#a-monthly-hygiene-routine)

## Why hygiene matters to a business

Mail providers judge a sender by how it behaves. Repeatedly mailing addresses that do not exist, or people who mark the
mail as spam, makes it more likely that your next newsletter, and possibly your order confirmations from the same
domain, land in spam folders. The website creator's troubleshooting table lists "Mail is marked as spam" with the
causes "sender domain records, content, and the sending server's reputation", and "Many bounces" with the action
"check the bounce mailbox; clean the list"
([Sending newsletters safely](../../creators/website-creators/sending-newsletters.md#when-something-goes-wrong)).

## The words

| Word | Meaning |
|---|---|
| Bounce | A mail that came back as undeliverable |
| Hard bounce | Permanent: the address or the domain does not exist |
| Soft bounce | Temporary: a full mailbox, a server that is down, a delay, a policy refusal |
| Complaint | The recipient marked the mail as spam (a feedback loop report) |
| Blacklist | The newsletter's own list of addresses that never get mail |
| Suppression list | The site-wide list of addresses that get no optional mail at all (Exponential 6.0.15 and later) |

Definitions of hard, soft and complaint follow the extension's guide
([deliverability.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/deliverability.md)).

## How returned mail reaches the newsletter

Returned mail arrives in a mailbox. The newsletter reads it in one of two ways (same guide):

- through a **newsletter mail account** (Newsletter > Mail accounts, the cron part `cjw_newsletter_mailbox`);
- through the **bounce reader of the e-mail preferences** (`exp:mail:bounces`, cron part `mailbounces`), which hands
  every message to the newsletter as well. The reader "stays off until its mailbox is set in a settings override".

Either way, someone has to set up the mailbox and the cron part. If nobody does, bounces are never read, and the list
slowly fills with dead addresses. The cron lines are in
[Newsletters with cjw_newsletter](../../creators/website-creators/newsletters-with-cjw-newsletter.md#setting-it-up).

## What happens to each kind

| Kind | What the extension does (4.2.0) |
|---|---|
| Hard bounce | The address goes on the suppression list with the reason `bounce`; the consent log records it |
| Complaint | The same, with the reason `complaint` |
| Soft bounce | The user's bounce count rises; the mail is sent again, at most `SoftBounceMaxRetries` times (2), first after `SoftBounceRetryDelay` (3600 seconds), each further retry waiting twice as long |
| Too many bounces | After `[BounceSettings] BounceThresholdValue` bounces (default 3) the user stops receiving |

Sources: [deliverability.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/deliverability.md) and the
shipped `cjw_newsletter.ini` (`[DeliverabilitySettings]`). A delivered mail sets the soft-bounce count back to 0. No
retry is made for a user who is bounced, blacklisted, removed or suppressed.

> **Note**
> Putting hard bounces on the suppression list (`SuppressHardBounces=enabled`, the shipped value) blocks every optional
> mail of the site to that address, not only the newsletter. That is usually what you want: the address does not
> exist.

## The blacklist and the suppression list

- The **blacklist** (`newsletter/blacklist_item_list`) holds addresses that must never get the newsletter; "it wins
  over every list" ([Newsletter lists and subscriptions](../../creators/website-creators/newsletter-lists-and-subscriptions.md#bounces-and-the-blacklist)).
- The **suppression list** (Setup > E-mail preferences > Suppression list) holds hard bounces, complaints, "stop all
  optional e-mail" requests, legal requests, entries made by an administrator and the newsletter blacklist. "Only a hash
  of the address is stored, so the list cannot be read; you find an entry by typing the address"
  ([The suppression list](../../../guides/mail-preferences-administrator.md#7-the-suppression-list)).
- A lifted suppression entry also comes off the newsletter blacklist
  ([deliverability.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/deliverability.md)).

Essential mail (password reset, orders) still goes to suppressed addresses (same administrator's guide).

## Importing addresses that must never be mailed

Newsletter > Suppression import takes a CSV file or pasted addresses into the suppression list, with a reason
(`legal`, `admin`, `bounce`, `complaint` or `unsubscribe_all`). The guide names typical sources: "a do-not-contact
list (Robinson list), the bounces of an earlier mail system, legal requests". **Check** counts and changes nothing, so
you can see what an import would do first
([deliverability.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/deliverability.md)).

When you move from another newsletter tool, import its bounces and unsubscribes this way before the first send, so
people who left there are not mailed again here.

## The dashboard and the repair command

The dashboard at `/newsletter/index` lists problems "with what to do about each (for example '3 subscriptions belong
to newsletter users that no longer exist', with a **Remove them** button)". From the shell,
`./console ext:cjw_newsletter:repair --dry-run` shows the same kind of repairs without changing anything
([cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md#dashboard-commands-and-background-runs-4117)).

## A monthly hygiene routine

- [ ] Open the dashboard and work through its list of problems.
- [ ] Check that the bounce cron part ran recently (the dashboard shows when each cron part ran last).
- [ ] Look at the number of hard bounces and complaints of the last sends; a sudden rise needs an explanation.
- [ ] Review pending subscriptions older than a few weeks; many of them point to a confirmation mail problem.
- [ ] Remove lists nobody sends to any more, after exporting what you must keep.
- [ ] Delete exports and CSV files that served their purpose.
- [ ] Record what you did in your operations log (Monthly operations checklist).

## Related pages

- [Newsletters for businesses](README.md)
- [Newsletter mail delivery](newsletter-mail-delivery.md)
- [Lists and subscriptions](lists-and-subscriptions.md)
- [E-mail preferences: the administrator's guide](../../../guides/mail-preferences-administrator.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
