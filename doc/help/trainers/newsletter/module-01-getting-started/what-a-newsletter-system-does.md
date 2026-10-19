---
title: "Lesson 1.1: What a newsletter system does"
audience: trainer, editor, admin
category: training
order: 11
summary: Learners can name the parts of CJW Newsletter (systems, lists, editions, subscriptions, sending, bounces) and explain how an edition travels from the content tree to a subscriber. 30 minutes.
keywords: newsletters with cjw newsletter, cjw_newsletter, newsletter system, newsletter list, edition, subscription, double opt-in, mail queue, bounce, blacklist, overview
type: help
---
# Lesson 1.1: What a newsletter system does

Before anyone creates a list or presses a send button, learners need a picture of the whole machine. This lesson
gives that picture: what the parts of CJW Newsletter are, where each lives, and how one edition becomes many mails.
Everything later in the course hangs on this map.

> **Note**
> This lesson is a conversation and a tour; nothing is created or sent. Use a training installation for the tour,
> never a live site.

## At a glance

| | |
|---|---|
| Course | [Newsletters with CJW Newsletter](../README.md) |
| Module | [Module 1: Getting started](README.md) |
| Duration | 30 minutes (20 presenting, 10 exercise) |
| Level | Beginner |
| Audience | Editors who will write newsletters, administrators who will run them |
| Prerequisites | An admin account on the training installation; basic editing as taught in Editors 101 |

## Learning objectives

After this lesson, learners can:

- name the four content classes of the newsletter (system, list, edition, article) and their order in the tree;
- explain what a subscription is and why it has a status;
- describe the path of an edition: send form, mail queue, cronjob, transport;
- say what bounces and the blacklist are for;
- find the newsletter area of the administration.

## Trainer's notes

### How to present it

Source: [cjw_newsletter: newsletters in the admin](../../../../features/6.0/extensions/cjw_newsletter.md) and
[Newsletters with cjw_newsletter](../../../creators/website-creators/newsletters-with-cjw-newsletter.md).

1. **One sentence first** (2 minutes). "Editions are content, subscribers are records, and cron does the sending."
   Write it on the board and come back to it at the end.
2. **The content side** (6 minutes). Draw the tree:

   ```text
   Newsletter root (RootFolderNodeId)
     Newsletter system            CJW Newsletter System
       Newsletter list            CJW Newsletter List
         Edition                  CJW Newsletter Edition
           Article                CJW Newsletter Article
   ```

   Systems group lists, visitors subscribe to lists, and an edition is one issue of one list. Articles are the items
   inside an edition. All four are ordinary content: they have drafts, versions and translations.
3. **The people side** (4 minutes). A *newsletter user* is an address with a name; a *subscription* links a
   newsletter user to a list and has a status: pending, confirmed, approved, removed by the user or by an
   administrator, soft or hard bounced, blacklisted. Only confirmed or approved subscribers receive editions.
4. **The sending side** (5 minutes). The send form creates a *send*. The cronjob part
   `cjw_newsletter_mailqueue_create` makes one queue item per subscriber, and `cjw_newsletter_mailqueue_process`
   hands each item to a *transport*: `file`, `smtp` or `sendmail`. In this course the transport is always `file`.
5. **Keeping lists clean** (3 minutes). A bounce is a mail that came back. After `BounceThresholdValue` bounces
   (3 by default) a user stops receiving. The blacklist holds addresses that never get mail, whatever list they are on.

### Common questions

**Is the newsletter part of the kernel?** No, it is the extension `cjw_newsletter`. It uses kernel parts such as the
e-mail preferences, the collaboration inbox and cron.

**Can a visitor subscribe to more than one list?** Yes. A newsletter user can have several subscriptions, one per
list, each with its own status.

**Why does sending need cron?** A send to thousands of people takes time. The request that presses the button only
schedules it; cron (or **Send now** on the dashboard, which runs the same work in the background) does the rest.

**Where did this extension come from?** It was written by the CJW Network and partners between 2007 and 2015 and
taken over for PHP 8.5 and Exponential 6 by the se7enxweb project in August 2026, according to the
[feature page](../../../../features/6.0/extensions/cjw_newsletter.md).

### Pitfalls

- **Mixing up list and edition.** Learners often call an edition "the newsletter". Insist on the words: the *list*
  is what people subscribe to, the *edition* is what they receive.
- **Talking about real mail.** Someone always asks to "just try a real send". Say no, and explain that the course
  writes every mail to a file on purpose (lesson 1.2).
- **Too much detail.** Statistics, SMS and A/B tests exist, but keep them for later modules.

## Hands-on exercise

Use the shared training installation and your admin training account. You only look; you change nothing.

1. Sign in to the administration of the training installation.
2. Open **Newsletter** from the top menu (or the **Newsletter** tile on the dashboard, *Lists, editions,
   statistics*). The address is `/newsletter/index`.
3. In the left menu, find **Newsletter system**, **Dashboard**, **Users**, **Blacklists**, **Bounces**,
   **Mail accounts**, **Imports**, **Subscription form** and **INI Settings**. Write the list down.
4. Press **Newsletter system** in the left menu. Note the class of the item and how many sub items it has.
5. Open one list below it. Find the heading **Newsletter editions** and count the editions.
6. On the list page, find the field labels **Email sender:** and **Newsletter output formats:**.
7. Open one edition. Note the headings **Statistics** and **Newsletter processing info**.
8. Draw the tree you saw on paper, with the class names, and compare it with your neighbor's.

## Expected result

- Learners have the left menu entries written down and a drawing of system, list, edition and article.
- They can point at the list page as "what people subscribe to" and the edition page as "what they receive".
- The trainer checks by asking two learners to explain their drawing to the room.

## Quiz

1. Put these in tree order, top to bottom: edition, system, article, list.
2. What do visitors subscribe to?
   A. a newsletter system B. a newsletter list C. an edition D. an article
3. Which subscribers receive an edition?
4. What does the cronjob part `cjw_newsletter_mailqueue_create` do?
5. True or false: an address on the blacklist still gets mail from lists it was subscribed to before.

<details>
<summary>Answers</summary>

1. System, list, edition, article ([Newsletters with cjw_newsletter](../../../creators/website-creators/newsletters-with-cjw-newsletter.md#the-parts)).
2. **B.** A list; systems only group lists.
3. Confirmed or approved subscribers only ([Newsletter lists and subscriptions](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#statuses)).
4. It builds the mail queue: one entry per subscriber of each send that is due ([specification](../../../../specifications/6.0/cjw_newsletter.md#cronjob-parts)).
5. **False.** The blacklist wins over every list.

</details>

## Further reading

- [cjw_newsletter: newsletters in the admin](../../../../features/6.0/extensions/cjw_newsletter.md): the parts and
  what changed in the Exponential 6 releases.
- [Newsletters with cjw_newsletter](../../../creators/website-creators/newsletters-with-cjw-newsletter.md): the
  website creator's view of the same parts.
- [Specification: the cjw_newsletter extension](../../../../specifications/6.0/cjw_newsletter.md): every view,
  command and cronjob part.

## Related pages

- [Module 1: Getting started](README.md)
- [Newsletters with CJW Newsletter: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
