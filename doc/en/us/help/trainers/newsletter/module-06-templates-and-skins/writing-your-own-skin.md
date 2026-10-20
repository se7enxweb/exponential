---
title: "Lesson 6.2: Writing your own skin"
audience: trainer, designer, developer
category: training
order: 62
summary: Designers and developers create a skin in their own design (html.tpl and text.tpl), register it in AvailableSkinArray, clear the caches, and check it in the skin preview and a test send written to a file. 60 minutes.
keywords: newsletters with cjw newsletter, skin, template, html.tpl, text.tpl, outputformat, AvailableSkinArray, design override, subject, set-block, template cache
type: help
---
# Lesson 6.2: Writing your own skin

Most organizations want their newsletter to look like their brand. A skin is two templates in a design: one for the
HTML part and one for the text part. In this lesson designers make a small skin of their own, starting from a shipped
one, and check it without sending a single real mail.

> **Note**
> Work on your own training installation. The test send of step 10 writes a file, because `TransportMethodPreview=file`
> (lesson 1.2), and goes only to an address at `example.invalid`.

## At a glance

| | |
|---|---|
| Course | [Newsletters with CJW Newsletter](../README.md) |
| Module | [Module 6: Templates and skins](README.md) |
| Duration | 60 minutes (15 presenting, 45 exercise) |
| Level | Advanced |
| Audience | Designers and developers who know the template language |
| Prerequisites | [Lesson 6.1](skins-and-output-formats.md); a design of their own on the training installation; shell access |

## Learning objectives

After this lesson, learners can:

- name the two template files of a skin and where they live in a design;
- set the mail's subject from a skin template;
- register a new skin in `[NewsletterSettings] AvailableSkinArray[]` in an override;
- clear the right caches and check the skin in the preview and in a test file;
- follow the rules for mail HTML (tables, inline styles, no web fonts).

## Trainer's notes

### How to present it

Sources: the rendering guide of the extension ("Writing a skin", linked from the
[feature page](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420)),
[Templates and design](../../../../guides/templates-and-design.md) and [Caches](../../../../guides/caches.md).

1. **The files** (3 minutes). A skin `<name>` is
   `design:newsletter/skin/<name>/outputformat/html.tpl` and `.../text.tpl`, in any design of the siteaccess that
   renders the list. Put yours in your own design, never in the extension's folder.
2. **The subject** (2 minutes). Both templates set the subject with
   `{set-block variable=$subject scope=root}...{/set-block}`.
3. **The variables** (3 minutes). The templates get `$contentobject` (the edition version), `$newsletter_list`,
   `$newsletter_language`, `$newsletter_skin` and `$newsletter_site_url`. The rendering guide lists the helper
   operators (for example `cjwnl_abs_url` for absolute addresses).
4. **Registering** (2 minutes). Add `AvailableSkinArray[]=<name>` in `settings/override/cjw_newsletter.ini.append.php`;
   then the list form and the send page offer it.
5. **Mail HTML rules** (3 minutes). A 600 pixel table layout, inline styles, no web fonts, no background images,
   absolute addresses for links and images. Copy a shipped skin rather than starting from nothing.
6. **Caches** (2 minutes). New template files need the template and template override caches cleared; settings need
   the INI cache.

### Common questions

**Can I override a shipped skin instead?** Yes: put `newsletter/skin/default/outputformat/html.tpl` in your design and
it wins over the extension's. A new name is safer, because the shipped skin stays available for comparison.

**Why are images broken in the mail?** Relative addresses do not work in a mail. Build absolute ones from the site URL.

**How do I write the text part?** With `TextFormat=plain` in the skin's `[Skin_<name>]` group, the text template writes
it itself; see the company skin and the rendering guide. Line breaks next to block tags need `{"\n"}`.

### Pitfalls

- **Editing the extension's templates.** An update of the extension overwrites them. Always use your design.
- **The wrong design.** The list renders with its siteaccess; the skin must be in a design of that siteaccess.
- **Template cache.** A new template is not found until the caches are cleared; the preview shows the old skin.
- **Linking external fonts or scripts.** Mail programs ignore or block them.

## Hands-on exercise

Use your own training installation, in its root directory. Replace `<mydesign>` with your design's folder and
`<name>` with your first name (lowercase letters only for the skin name, for example `brand_anna`).

1. Copy the shipped default skin into your design under a new name:

   ```bash
   mkdir -p design/<mydesign>/templates/newsletter/skin/brand_<name>/outputformat
   cp extension/cjw_newsletter/design/standard/templates/newsletter/skin/default/outputformat/*.tpl \
      design/<mydesign>/templates/newsletter/skin/brand_<name>/outputformat/
   ```

   (If your design lives in an extension, use that extension's `design/<mydesign>/` folder instead.)
2. Open the new `html.tpl`. Find the `set-block` that sets `$subject` and add your organization's short name in front
   of the title.
3. Change one visible style inline, for example the color of the headline, keeping the table layout.
4. Open `text.tpl` and make the same subject change.
5. Register the skin in `settings/override/cjw_newsletter.ini.append.php`:

   ```ini
   [NewsletterSettings]
   AvailableSkinArray[]=default
   AvailableSkinArray[]=brand_<name>
   ```

6. Clear the caches:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-tag=template
   php bin/php/ezcache.php --clear-id=content
   ```

7. Open **Preview the skins** and find `brand_<name>`; look at its **HTML part** and **Text part**.
8. Edit your list and allow `brand_<name>`. Publish.
9. Open **Send Newsletter** on an unsent edition, choose `brand_<name>` in **Skin of this send**, look at
   **Preview as a subscriber**, then **Cancel**.
10. Send a test of the edition to `test-<name>@example.invalid` with **Send Test Newsletter** and open the file in
    `var/log/mail`: check the subject line and the changed style.

## Expected result

- The skin `brand_<name>` exists in the learner's design, is listed in the skin preview and offered on the send page.
- The test file's subject starts with `[Test]` and then the organization's short name.
- The extension's own folder is unchanged.
- The trainer checks one learner's test file and the location of their templates.

## Quiz

1. Which two files make a skin?
2. Where must your skin's templates live?
   A. in the extension's design folder B. in a design of the siteaccess that renders the list C. in `var/` D. in
   `settings/override/`
3. How does a skin template set the subject of the mail?
4. Which setting makes a new skin selectable?
5. Why do images need absolute addresses in a newsletter?

<details>
<summary>Answers</summary>

1. `outputformat/html.tpl` and `outputformat/text.tpl` below `newsletter/skin/<name>/` (rendering guide, linked from the
   [feature page](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420)).
2. **B.** In your own design, never the extension's ([Templates and design](../../../../guides/templates-and-design.md)).
3. With `{set-block variable=$subject scope=root}...{/set-block}` (rendering guide).
4. `[NewsletterSettings] AvailableSkinArray[]` in an override ([feature page](../../../../features/6.0/extensions/cjw_newsletter.md#settings-you-will-use)).
5. A mail is read outside the site; relative addresses have nothing to be relative to.

</details>

## Further reading

- [Templates and design](../../../../guides/templates-and-design.md): designs, overrides and the template language.
- [Caches](../../../../guides/caches.md): which caches to clear after a template change.
- [cjw_newsletter: newsletters in the admin](../../../../features/6.0/extensions/cjw_newsletter.md#the-features-of-420):
  the rendering guide with variables and operators.

## Related pages

- [Module 6: Templates and skins](README.md)
- [Newsletters with CJW Newsletter: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
