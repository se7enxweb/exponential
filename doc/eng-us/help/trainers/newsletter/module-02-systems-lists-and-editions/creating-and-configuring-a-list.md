---
title: "Lesson 2.2: Creating and configuring a list"
audience: trainer, editor, admin
category: training
order: 22
summary: Learners create a newsletter list in their system and fill in its configuration (siteaccess, output formats, skin, sender, test receiver, personalization) with safe training values. 60 minutes.
keywords: newsletters with cjw newsletter, newsletter list, cjw_newsletter_list, newsletter list configuration, sender, test receiver, output format, skin, siteaccess, personalize, approve subscription
type: help
---
# Lesson 2.2: Creating and configuring a list

A list is what people subscribe to, and its configuration decides how every edition of it is sent: from which
address, in which formats, rendered by which siteaccess and skin, and to whom test mails go. In this lesson each
learner creates a list and fills in the configuration with values that are safe for training.

> **Note**
> All addresses in this lesson end in `@example.invalid`. Never type a real address into a training list, not even
> your own.

## At a glance

| | |
|---|---|
| Course | [Newsletters with CJW Newsletter](../README.md) |
| Module | [Module 2: Systems, lists and editions](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Beginner |
| Audience | Editors and administrators |
| Prerequisites | [Lesson 2.1](newsletter-systems-and-the-tree.md): the learner has a newsletter system |

## Learning objectives

After this lesson, learners can:

- create a **CJW Newsletter List** below their system;
- explain each part of **Newsletter list configuration** and choose safe training values;
- explain the difference between the sender, the reply-to and the return-path addresses;
- read the configuration back on the list's page and find its subscription counts.

## Trainer's notes

### How to present it

Sources: [Newsletters with cjw_newsletter](../../../creators/website-creators/newsletters-with-cjw-newsletter.md#the-parts)
and the [feature page](../../../../features/6.0/extensions/cjw_newsletter.md). Field labels below are the ones the
edit form of a standard installation shows; check your own form once before the class.

1. **The fields** (5 minutes). A list has **Title** (required), **Short title**, **Short description** and the
   required **Newsletter list configuration**. The configuration is one field with many parts.
2. **Rendering** (5 minutes). The list names the **Siteaccess** that renders its editions (and its main siteaccess,
   site URL and locale shown on the list page), the available output formats (HTML and text), and the
   **Newsletter skin name**. The rendered links point at that siteaccess's site URL, which is why
   `[SiteSettings] SiteURL` must be right on a real site.
3. **Addresses** (5 minutes). **Newsletter sender email** and **Newsletter sender name** are the *From* of every
   edition. **Email reply-to** is where replies go; **Email return-path** is where bounces go (module 5). The
   **Newsletter default test receiver email** is the address a test send goes to (lesson 4.1).
4. **Options** (5 minutes). **Personalize newsletter if data are available?** fills placeholders such as the name
   (lesson 6.1). **Automatically approve subscription after user registration?** approves subscriptions of
   registered users without a confirmation step. Leave the 4.2 options (approval, article pool, recurring sends,
   skins allowed, languages, interests, statistics, SMS) at their defaults for now; statistics stay off.

### Common questions

**Why does the dashboard warn about the default sender address?** The shipped `EmailSender` is
`newsletter@example.com`. The list's own sender overrides it; on a real site set both
([specification](../../../../specifications/6.0/cjw_newsletter.md#problems-the-dashboard-reports)).

**Can one edition go to two lists?** No. An edition belongs to one list. Make a second edition (copying helps).

**What does "Can subscribe" mean?** It controls whether the list is offered on the public subscribe form. Check its
exact behavior on your installation by looking at the subscribe form after changing it.

**What is the "Create filter here" button?** It belongs to virtual lists, which select subscribers by filters. This
course does not cover them.

### Pitfalls

- **A real address typed by habit.** Learners type their own address as test receiver. Check every form before
  publishing.
- **Wrong siteaccess.** A list rendered by the admin siteaccess produces admin links in the mail. Choose the site's
  public siteaccess.
- **No output format ticked.** An edition with no format cannot be rendered; tick HTML and text.

## Hands-on exercise

Use the training installation and your admin training account. Replace `<name>` with your first name.

1. Open your system `TS <name>` from lesson 2.1.
2. Under **Sub items**, press **Create new** and choose **CJW Newsletter List**.
3. In **Title**, type `Weekly tips <name>`. Leave **Short title** empty.
4. In **Short description**, type one sentence that a visitor would read on the subscribe form.
5. In **Newsletter list configuration**, choose the public siteaccess of the training site (ask the trainer which).
6. Tick both available output formats (HTML and text).
7. Choose the skin `default`.
8. **Newsletter sender email**: `newsletter@example.invalid`. **Newsletter sender name**: `Training newsletter`.
9. **Email reply-to**: `reply@example.invalid`. **Email return-path**: `bounces@example.invalid`.
10. **Newsletter default test receiver email**: `test-<name>@example.invalid`.
11. Tick **Personalize newsletter if data are available?**. Leave the other options as they are.
12. Press **Send for publishing**.
13. On the list's page, read the configuration back and find the links **Subscriptions**, **Pending**,
    **Confirmed**, **Approved**, **Bounced**, **Removed** and **Blacklisted** with their counts.

## Expected result

- The list `Weekly tips <name>` is published in the learner's system, with zero subscriptions.
- Its page shows the sender `newsletter@example.invalid`, the test receiver at `example.invalid`, both output formats
  and the skin `default`.
- The list appears in the **Lists** table of the newsletter dashboard.
- The trainer checks each list page for addresses that are not at `example.invalid`.

## Quiz

1. Which address of a list receives the bounces?
   A. sender B. reply-to C. return-path D. test receiver
2. Why must the list name a public siteaccess?
3. What does **Personalize newsletter if data are available?** allow?
4. You see the dashboard hint "Default sender address". What is still set to an example address?
5. Can one edition be sent to two lists?

<details>
<summary>Answers</summary>

1. **C.** The return-path is where mail servers send undeliverable mail back ([feature page](../../../../features/6.0/extensions/cjw_newsletter.md#settings-you-will-use)).
2. Because the edition is rendered there, and its links and images point at that siteaccess's site URL.
3. Filling placeholders such as the subscriber's name when the data exist (see lesson 6.1 and the rendering guide
   linked from the [feature page](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).
4. `EmailSender` in the settings, or the sender of a list ([specification](../../../../specifications/6.0/cjw_newsletter.md#problems-the-dashboard-reports)).
5. No. An edition belongs to one list.

</details>

## Further reading

- [Newsletter lists and subscriptions](../../../creators/website-creators/newsletter-lists-and-subscriptions.md):
  statuses and where subscribers are managed.
- [Sending newsletters safely](../../../creators/website-creators/sending-newsletters.md): sender domain and
  transports for real sending.
- [cjw_newsletter: newsletters in the admin](../../../../features/6.0/extensions/cjw_newsletter.md#settings-you-will-use):
  the settings behind the list defaults.

## Related pages

- [Module 2: Systems, lists and editions](README.md)
- [Newsletters with CJW Newsletter: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
