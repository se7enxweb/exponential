---
title: "Lesson 6.1: Skins and output formats"
audience: trainer, editor, designer
category: training
order: 61
summary: Learners compare the four shipped skins in the skin preview, explain the HTML and text parts of a newsletter mail, and use placeholders such as the subscriber's name and the unsubscribe link correctly. 45 minutes.
keywords: newsletters with cjw newsletter, skin, skin preview, output format, html part, text part, placeholders, unsubscribe_url, personalize, default, company, news, shop
type: help
---
# Lesson 6.1: Skins and output formats

A *skin* is the look of a newsletter mail: its layout, colors and the way articles are arranged. Every mail has an
HTML part and a plain text part, and the skin makes both. In this lesson learners compare the shipped skins, see why
the text part matters, and learn the placeholders that make a mail personal and let people leave.

> **Note**
> Previews send nothing. The test send in step 9 writes files, because `TransportMethodPreview=file` on the training
> installation (lesson 1.2), and goes only to addresses at `example.invalid`.

## At a glance

| | |
|---|---|
| Course | [Newsletters with CJW Newsletter](../README.md) |
| Module | [Module 6: Templates and skins](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Editors who choose the look, designers |
| Prerequisites | [Module 4](../module-04-sending/README.md): an edition to preview |

## Learning objectives

After this lesson, learners can:

- open **Preview the skins** and compare the skins with their **HTML part** and **Text part**;
- explain why every mail carries a plain text part;
- choose the skins a list allows and the skin of one send;
- use the placeholders for the name and the personal links, and know which need personalization;
- explain why the shipped skins use tables and inline styles.

## Trainer's notes

### How to present it

Sources: the rendering guide of the extension (linked from the
[feature page](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420)) and
[Subscribe forms on the site](../../../creators/website-creators/newsletters-with-cjw-newsletter.md#subscribe-forms-on-the-site).

1. **The skins** (5 minutes). **Preview the skins** (dashboard, **Skins, languages and interests** block) shows
   *Default*, *Company*, *News site* and *Shop*, each with **HTML part** and **Text part**. `default` is the skin of
   4.1; `company` is a calm corporate letter, `news` a news digest, `shop` cards with buttons.
2. **Why tables and inline styles** (3 minutes). The 4.2 skins are 600 pixel wide table layouts with every style
   inline, no web fonts and no background images, so they read the same in the common mail programs and with images
   blocked.
3. **The text part** (3 minutes). Some readers and filters only see text. `default` converts its text part from the
   HTML; the other skins write it with plain text views.
4. **Skins per list and per send** (3 minutes). The list form names the skins allowed (none ticked means all), and the
   send page's **Skin of this send** chooses one. The list's own skin is always allowed.
5. **Placeholders** (6 minutes). Written in the edition's text, replaced per subscriber:

   | Placeholder | Value | Needs the list's personalization |
   |---|---|---|
   | `[[first_name]]`, `[[last_name]]`, `[[name]]` | the subscriber's name | yes |
   | `[[list_name]]` | the list's name | no |
   | `[[unsubscribe_url]]` | the unsubscribe page of this subscription | no |
   | `[[configure_url]]` | the subscriber's newsletter settings page | no |

   Every value is escaped in the HTML part.

### Common questions

**Why does my placeholder stay unchanged in a test?** Name placeholders need **Personalize newsletter if data are
available?** on the list and a subscriber with that data; test addresses are not subscribers. Use
**Preview as a subscriber**.

**Can I add my own colors?** Each skin has a settings group `[Skin_<name>]` with an accent color. Changing a skin's
templates is lesson 6.2.

**Does a skin change the edition's content?** No; it changes how the same content is laid out.

### Pitfalls

- **Placeholders typed with one bracket.** `[first_name]` is printed as it is. Copy them from the table.
- **Removing the unsubscribe link.** Every edition must carry one (consent checklist, lesson 3.4).
- **Judging the HTML only.** Always look at the **Text part** too.

## Hands-on exercise

Use the training installation. Replace `<name>` with your first name.

1. On the newsletter dashboard, find **Skins, languages and interests** and press **Preview the skins**.
2. For each of the four skins, open the **HTML part**, then the **Text part**. Note one strength of each.
3. Open your list and press **Edit**. Find where the allowed skins are chosen; tick `default` and `news`. Publish.
4. Make a copy of `Tips for week 2` and name it `Tips for week 4`.
5. In its **Description**, add a first line: `Hello [[first_name]],`.
6. At the end of the **Description**, add: `To leave this list, open [[unsubscribe_url]]`. Publish.
7. Press **Send Newsletter** on the edition (do not send). Choose **News site** in **Skin of this send** and use
   **Preview as a subscriber** for Dora.
8. Leave the send page with **Cancel**.
9. Send a test of `Tips for week 4` with **Send Test Newsletter** to `test-<name>@example.invalid`, and open the file
   in `var/log/mail`.
10. In the file, find the unsubscribe address and check whether `[[first_name]]` was replaced. Explain why.

## Expected result

- The learner can name the four skins and one strength of each.
- The list allows `default` and `news`; the send page offered only those (and the list's own skin).
- In the preview as Dora, the greeting reads *Hello Dora,* and the unsubscribe link is filled.
- In the test file the unsubscribe link is filled; the name may stay empty because the test address is not a
  subscriber with data.
- The trainer checks one learner's preview on the projector.

## Quiz

1. Name the four shipped skins.
2. Which placeholder gives the subscriber's personal unsubscribe link?
   A. `[[configure_url]]` B. `[[unsubscribe_url]]` C. `[[list_name]]` D. `[unsubscribe]`
3. Why do the 4.2 skins use tables and inline styles?
4. What does a list need for `[[first_name]]` to be filled?
5. Why does every mail carry a text part?

<details>
<summary>Answers</summary>

1. default, company, news, shop (rendering guide, linked from the [feature page](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).
2. **B.** (rendering guide).
3. So they read the same in Outlook, Gmail and Apple Mail and with images blocked (rendering guide).
4. Personalization switched on in the list (and the subscriber's data) (rendering guide; lesson 2.2).
5. Some mail programs, readers and filters only use the text part.

</details>

## Further reading

- [cjw_newsletter: newsletters in the admin](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420):
  the rendering guide with every placeholder.
- [Newsletters with cjw_newsletter](../../../creators/website-creators/newsletters-with-cjw-newsletter.md#subscribe-forms-on-the-site):
  skins and where they live.
- [Newsletter lists and subscriptions](../../../creators/website-creators/newsletter-lists-and-subscriptions.md#consent):
  the unsubscribe link in every edition.

## Related pages

- [Module 6: Templates and skins](README.md)
- [Newsletters with CJW Newsletter: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
