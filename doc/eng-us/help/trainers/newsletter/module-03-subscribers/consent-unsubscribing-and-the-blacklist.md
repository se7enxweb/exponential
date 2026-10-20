---
title: "Lesson 3.4: Consent, unsubscribing and the blacklist"
audience: trainer, editor, admin
category: training
order: 34
summary: Learners unsubscribe a test subscriber through the public page, add an address to the blacklist, and explain how the blacklist and the suppression list of the e-mail preferences work together. 45 minutes.
keywords: newsletters with cjw newsletter, unsubscribe, consent, blacklist, suppression list, e-mail preferences, removed by user, gdpr, privacy
type: help
---
# Lesson 3.4: Consent, unsubscribing and the blacklist

A newsletter is only as good as the trust of its readers. Leaving must be as easy as joining, and an address that
must never get mail must never get it, whichever list it is on. This lesson covers the unsubscribe page, the
blacklist, and the suppression list of the kernel's e-mail preferences that the newsletter shares since 4.2.

> **Note**
> Use the test subscribers at `example.invalid` from lessons 3.1 to 3.3. Any mail a step writes goes to a file in
> `var/log/mail` (lesson 1.2).

## At a glance

| | |
|---|---|
| Course | [Newsletters with CJW Newsletter](../README.md) |
| Module | [Module 3: Subscribers](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Editors and administrators; data protection contacts are welcome |
| Prerequisites | [Lesson 3.3](csv-import-and-export.md): Anna, Ben, Carla, Dora, Emil and Fay on the learner's list |

## Learning objectives

After this lesson, learners can:

- name the consent checks a newsletter needs (double opt-in, unsubscribe link, a clear form, short-lived exports);
- unsubscribe a subscriber on the public unsubscribe page and find the status *removed by user*;
- add an address to the blacklist with a note, and explain that it wins over every list;
- explain how the blacklist and the kernel suppression list are connected.

## Trainer's notes

### How to present it

Sources: [Consent](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#consent),
[Bounces and the blacklist](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#bounces-and-the-blacklist),
[E-mail preferences: setting them up and running them](../../../../guides/mail-preferences-administrator.md) and the
deliverability guide of the extension (linked from the [feature page](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).

1. **The consent checklist** (5 minutes). Double opt-in for every public subscription; an unsubscribe link in every
   edition; a subscribe form that says what subscribers get and how often; exports kept only as long as needed.
2. **Unsubscribing** (5 minutes). Every edition carries a personal unsubscribe link (the skins print one; skins use
   the placeholder for it, lesson 6.1). The page `newsletter/unsubscribe/<hash>` asks once; **Unsubscribe** sets the
   subscription to *removed by user*. A removed person is never subscribed again by an import.
3. **The blacklist** (5 minutes). **Blacklists** in the left menu: **Find an address**, **Filter**,
   **Add email address to blacklist** (fields **Email** and **Note**, button **Add to Blacklist**), and
   **Remove selected**, which asks first. A blacklisted address gets no mail from any list.
4. **The suppression list** (5 minutes). The e-mail preferences of Exponential keep a suppression list for every
   optional mail of the site. Since 4.2, a hard bounce or complaint puts an address there, and the newsletter puts it
   on its blacklist. Lifting a suppression in Setup also takes the address off the newsletter blacklist.

### Common questions

**Unsubscribe or blacklist: which one for a person who asks to leave?** Unsubscribe, so the person can come back
later. Blacklist addresses that must never be mailed, for example on a legal request or for a role address that
complained.

**Does deleting a user help with the right to be forgotten?** The e-mail preferences have an erasure path that also
removes the newsletter user's data; see [E-mail preferences](../../../../features/6.0/mail-preferences.md). Follow your
organization's procedure.

**Can a person manage all their mail settings in one place?** Yes, on the e-mail preference page of the site; the
newsletter category shows there.

### Pitfalls

- **Unsubscribing in the admin view of the person.** Use the public page with the link from a mail file, as a
  subscriber would.
- **Blacklisting a learner's real address.** Only `example.invalid` in class.
- **Removing blacklist entries.** **Remove selected** asks first; in class, remove only the entry you added.

## Hands-on exercise

Use the training installation. Replace `<name>` with your first name.

1. Find the confirmation file of Anna from lesson 3.1 in `var/log/mail` and copy its `/newsletter/configure/` address
   again. (Edition mails carry a separate unsubscribe link; you will see it in module 4.)
2. Open the address in a private browser window. The page says to select the newsletters you wish to subscribe to
   and to deselect to unsubscribe.
3. Untick your list `Weekly tips <name>` and press **Confirm**.
4. In the administration, open your list's subscriptions and press **Removed**. Anna is listed.
5. Press **Blacklists** in the left menu.
6. Press **Add email address to blacklist**. **Email**: `emil-<name>@example.invalid`.
   **Note**: `Training: asked never to be contacted`. Press **Add to Blacklist**.
7. Use **Find an address** and **Filter** to find Emil's entry.
8. Open your list's subscriptions and press **Blacklisted**. Note whether Emil is listed.
9. On the **Users** page, set **Status** to *Blacklisted* and search.
10. Discuss in pairs: for each of Anna and Emil, why was this the right tool?

## Expected result

- Anna's subscription is removed; she is listed under **Removed** (check the exact status wording on your installation).
- Emil is on the blacklist with the note; the **Users** page lists him as blacklisted.
- Each pair can say when to unsubscribe and when to blacklist.
- The trainer checks the blacklist page for entries outside `example.invalid` and removes any after asking.

## Quiz

1. Name two items of the consent checklist.
2. A subscriber clicks the unsubscribe link and confirms. What status does the subscription get?
   A. blacklisted B. removed by user C. bounced D. pending
3. Which wins: a confirmed subscription or a blacklist entry for the same address?
4. Since 4.2, what puts an address on the suppression list automatically?
5. When should you prefer unsubscribing over blacklisting?

<details>
<summary>Answers</summary>

1. Any two of: double opt-in, an unsubscribe link in every edition, a clear subscribe form, short-lived exports
   ([Consent](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#consent)).
2. **B.** ([Statuses](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#statuses)).
3. The blacklist; it wins over every list ([Bounces and the blacklist](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#bounces-and-the-blacklist)).
4. A hard bounce or a complaint (deliverability guide, linked from the [feature page](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).
5. When the person may want to come back; the blacklist is for addresses that must never get mail.

</details>

## Further reading

- [Newsletter lists and subscriptions](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#consent):
  the consent checklist.
- [E-mail preferences: setting them up and running them](../../../../guides/mail-preferences-administrator.md): the
  suppression list and consent log.
- [Compliance and GDPR](../../../hosting/security/compliance-and-gdpr.md): the wider privacy picture.

## Related pages

- [Module 3: Subscribers](README.md)
- [Newsletters with CJW Newsletter: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
