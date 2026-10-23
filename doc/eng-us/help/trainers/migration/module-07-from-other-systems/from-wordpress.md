---
title: "Lesson 7.1: From WordPress"
audience: trainer, admin, developer
category: training
order: 71
summary: Learners plan a WordPress migration end to end - what WordPress exports (WXR, WP-CLI, the REST API, the uploads directory), the mapping of posts, pages, media, taxonomies, shortcodes and SEO fields, the nine steps of the import, permalinks and redirects, and a delta import before the cut-over. 60 minutes.
keywords: migrating to exponential, WordPress, WXR, WP-CLI, wp export, REST API, uploads, posts, pages, attachments, categories, tags, shortcodes, permalinks, redirects, delta import, password reset
type: help
---
# Lesson 7.1: From WordPress

WordPress is the source trainers meet most often. This lesson follows the WordPress guide of the documentation: what
WordPress can export, how its content maps, the nine steps from export to cut-over, and the addresses that must keep
working. It applies the frame of modules 1 to 3 to one real source.

> **Note**
> Practise on a copy of a WordPress site or on a WXR file from a test installation. Never export a live site's users
> into a training installation; test accounts use addresses at `example.invalid`.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 7: From other systems](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Intermediate |
| Audience | Developers, administrators and site owners moving from WordPress |
| Prerequisites | [Module 2](../module-02-mapping-content/README.md) and [Module 3](../module-03-import-and-export-tools/README.md) |

## Learning objectives

After this lesson, learners can:

- name the four export sources of WordPress and say which one is the frozen source of the import;
- map posts, pages, attachments, categories, tags, custom fields and SEO fields to Exponential;
- put the nine steps of the WordPress guide in order;
- decide for each kind of WordPress address how it keeps working;
- explain the delta import before the cut-over.

## Trainer's notes

### How to present it

Source: [WordPress and Exponential](../../../../install/migration/wordpress.md).

1. **Exports** (4 minutes). Tools > Export and `wp export` give WXR (posts, pages, custom post types, comments,
   custom fields, terms, users), without site configuration and without the files. The REST API gives JSON with
   pagination. The `wp-content/uploads` directory holds the files. WXR is the frozen source; the REST API serves the
   delta of the last days.
2. **Mapping** (6 minutes). Show the mapping table: posts to `article` objects below a blog folder; pages as a tree
   following `post_parent`; `post_content` to `ezxmltext` with block comments stripped and images as embeds; featured
   image explicitly from `_thumbnail_id`; categories as folders or `eztags`; shortcodes converted or rendered; SEO
   plugin fields to `xrowmetadata`. Passwords: WordPress formats are not read; plan resets.
3. **Nine steps** (5 minutes). Inventory, classes, export, convert, media first, inner links, redirects and SEO,
   delta, cut-over.
4. **Addresses** (5 minutes). Permalinks become redirecting custom aliases from WXR `<link>`; `/?p=123` goes to the web
   server; feeds to an RSS export; `/wp-content/uploads/...` to the media objects or kept at that path; the login
   addresses get 410 or a redirect.

### Common questions

**Our site uses a page builder plugin.** Its layout lives in post meta or shortcodes: give it its own conversion, or
fall back to rendered HTML (fetch the published page and take the content area). Test the most complex pages first.

**How do we find the shortcodes?** The guide's example: `grep -o '\[[a-z_]*'` over the content, then convert or render
each.

**Can we go the other way later?** Yes: section 5 of the guide describes Exponential to WordPress with xrowextract
and the WordPress Importer (lesson 8.1).

**Is there an importer that reads WXR directly?** The extension 7x SQLI Import does, where it is installed: its
format *WordPress export (WXR)* reads posts and pages (with author, date, categories, tags, excerpt and featured
image), or the media, categories, tags and authors, one kind per import. Import the media first, then the posts. It
takes the place of the conversion script, shows a dry run first, and for the delta import before the cut-over you run
it again with the newer export: items it made before are updated, not duplicated
([7x SQLI Import: start here](../../../sqli-import/README.md)).

### Pitfalls

- **Forgetting the files.** WXR has attachment URLs, not files; copy the uploads directory.
- **Shortcodes left in text.** They show as `[gallery ...]` on the new site.
- **Telling users about the password reset on launch day.** Tell them before.

## Hands-on exercise

A planning exercise in groups of three, with the WordPress guide open. The scenario: a WordPress site with 1 200
posts, 60 pages in three levels, 4 000 attachments, categories and tags, an SEO plugin, a contact form plugin, the
permalink structure `/%year%/%monthnum%/%postname%/`, and 25 editor accounts.

1. Write the inventory commands and checks of step 1 of
   [section 3 of the guide](../../../../install/migration/wordpress.md#3-from-wordpress-to-exponential-step-by-step),
   including `wp post list --post_type=post --format=count`.
2. Write the export command for a frozen WXR without a size limit, and what else you copy.
3. Fill in a mapping table for posts, pages, attachments, categories, tags, the SEO plugin's fields and the contact
   form, using [section 2](../../../../install/migration/wordpress.md#2-mapping-wordpress-to-exponential).
4. Write the remote id pattern for posts, pages and attachments.
5. Put the import in order: comments, pages, media, posts. Say why.
6. Write a redirect table of six rows covering the permalink structure, `/?p=`, a category archive, a feed, an upload
   and `/wp-login.php`, with the Exponential tool for each
   ([section 4](../../../../install/migration/wordpress.md#4-addresses-and-redirects)).
7. Write how the delta before the cut-over is exported and why it does not create duplicates.
8. Write the four risks of [section 6](../../../../install/migration/wordpress.md#6-risks-and-checklist) that apply
   and the counter-measure of each.

## Expected result

- Step 2: `wp export --dir=<dir> --max_file_size=-1` (or Tools > Export), plus a copy of `wp-content/uploads`.
- Step 3: posts to articles below a blog folder, pages as a tree, attachments as image or file objects in a media
  folder, categories as folders or tags, tags as `ezkeyword` or `eztags`, SEO fields to `xrowmetadata`, the form
  rebuilt with information collection and its submissions archived.
- Step 5: media, pages (in parent order), posts, comments: content that relates comes after what it relates to.
- Step 6: permalinks as redirecting custom aliases generated from `<link>`, `/?p=` in the web server.
- Step 7: the WXR date filter or the REST API with `modified_after`; remote ids make it an update.
- The trainer checks steps 5 to 7 with each group.

## Quiz

1. What does a WXR file **not** contain, according to WP-CLI's documentation?
   A. Posts B. Comments C. The attachment files themselves D. Custom fields
2. Where does a WordPress featured image come from in the export, and why map it explicitly?
3. How is `/?p=123` redirected, and why not with a URL alias?
4. Which source serves the delta import of the last days before the cut-over?
5. Can WordPress users keep their passwords?

<details>
<summary>Answers</summary>

1. **C**, and no site configuration ([WordPress guide, section 1](../../../../install/migration/wordpress.md#1-what-wordpress-can-export)).
2. From the `_thumbnail_id` post meta; otherwise featured images get lost
   ([section 2](../../../../install/migration/wordpress.md#2-mapping-wordpress-to-exponential) and section 6).
3. In the web server from the redirect table; query strings never reach URL aliases
   ([section 4](../../../../install/migration/wordpress.md#4-addresses-and-redirects)).
4. The WXR date filter or the REST API with `modified_after`
   ([section 3](../../../../install/migration/wordpress.md#3-from-wordpress-to-exponential-step-by-step)).
5. No; WordPress stores its own hash formats, which Exponential does not read: plan resets (section 2).

</details>

## Further reading

- [WordPress and Exponential](../../../../install/migration/wordpress.md): both directions with sources.
- [Mapping content to Exponential](../../../../install/migration/content-model-mapping.md): the building blocks.
- [Exponential's import and export tools, section 4](../../../../install/migration/exponential-tools.md#4-a-custom-import-script-the-skeleton):
  the script skeleton the conversion feeds.

## Related pages

- [Module 7: From other systems](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
