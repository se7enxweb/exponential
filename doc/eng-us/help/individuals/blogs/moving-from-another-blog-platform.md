---
title: Moving from another blog platform
audience: individual, website-creator
category: personal-sites
order: 1626
summary: An honest guide for individuals who want to move a blog from WordPress or another platform to Exponential - there is no one-click importer; the documented path is an export, a conversion script and an import tool - with three realistic options for one person, effort drivers and a checklist.
keywords: migration, move blog, import, WordPress, WXR, Blogger, Medium, site builder, export, xrowextract, ezcsvimport, RSS import, redirects, permalinks, comments, images, importer
type: help
---
# Moving from another blog platform

This page is for individuals whose blog lives elsewhere today, on WordPress, a hosted blogging service or a site
builder, and who want to move it to Exponential.

> **Note**
> There is **no one-click importer** for WordPress or any other blog platform in Exponential. Moving a blog is a small
> project: export, convert, import, redirect. The steps are documented; the conversion script is something you or a
> helper write. This page tells you what that means for one person.

## Contents

1. [What exists](#what-exists)
2. [Three options for one person](#three-options-for-one-person)
3. [What makes it more or less work](#what-makes-it-more-or-less-work)
4. [Keeping your old addresses](#keeping-your-old-addresses)
5. [Comments, images and authors](#comments-images-and-authors)
6. [Checklist](#checklist)

## What exists

| Tool | What it does | Source |
|---|---|---|
| The WordPress migration guide | The full path: WXR export, mapping, conversion, import, redirects, cut-over | [WordPress and Exponential](../../../install/migration/wordpress.md) |
| `xrowextract` | Imports CSV, XML or JSON files into content, with a dry run first | [Exponential's import and export tools](../../../install/migration/exponential-tools.md) |
| `ezcsvimport.php` | The kernel's CSV import script | same |
| The ZIP upload of `ezmultiupload` | Brings many images in at once | [Uploading many images](../portfolios/uploading-many-images.md) |
| RSS import (kernel) | Reads another site's feed into content, with a cronjob | [RSS feeds for your site](../../creators/website-creators/rss-feeds-for-your-site.md#importing-a-feed) |
| Site builders' exports | Some builders export WordPress XML | [Site builders](../../../install/migration/site-builders.md) |
| The extension 7x SQLI Import | Reads the WordPress export file (WXR) itself: posts and pages with author, date, categories, tags, excerpt and featured image, and the media; you map them to your classes in the administration and see a dry run first | [7x SQLI Import: start here](../../sqli-import/README.md) |

What does not exist in a plain installation: a ready converter from WordPress's export file (WXR) to Exponential
content. Where your host or helper installs the extension 7x SQLI Import, it is that converter: import the media
first, then the posts, so the images are kept. Still missing either way: an import of WordPress users with their
passwords (Exponential cannot read WordPress password hashes), and automatic conversion of shortcodes and page-builder
layouts.

## Three options for one person

| Option | How | Effort | Suits |
|---|---|---|---|
| **Copy by hand** | Recreate posts in the editor, newest or best first | Grows with every post | Up to a few dozen posts; a chance to prune |
| **RSS import for recent posts** | Point an RSS import at your old blog's feed; it creates objects for the items the feed contains | Short to set up; needs the `rssimport` cronjob | Pulling in the latest posts; a feed carries only the items its publisher puts in it, usually the newest ones |
| **Scripted migration** | Follow the WordPress guide: export WXR, convert to CSV or JSON with a script, import with `xrowextract` | A weekend or more if you script; a defined job for a helper | Hundreds of posts, or when you want everything |
Many people combine them: script or RSS-import the archive, then fix the twenty most-read posts by hand.

When the extension 7x SQLI Import is installed, the scripted migration needs no script: create a new import of the
format *WordPress export (WXR)*, upload the file, map the posts to your article class, look at the dry run, then run
it. It takes an afternoon rather than a weekend, and a run that went wrong can be undone.

If scripting is not for you, this is a well-defined task to hand to a partner
([When to hire help](../running-it/when-to-hire-help.md)).

## What makes it more or less work

| Easier | Harder |
|---|---|
| Plain text posts with a few images | Page builders, shortcodes, embedded widgets |
| A few hundred posts | Thousands of posts and attachments |
| Categories and tags only | Custom fields, plugins that store content |
| Comments left behind | Comments to be kept |
| One author | Several authors with accounts |

## Keeping your old addresses

Readers and search engines know your old addresses. Keep them working:

- If the domain stays the same, map old permalinks to the new pages with redirects
  ([Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md)).
- Addresses with query strings (`/?p=123`) must be redirected by the web server, not by Exponential
  ([WordPress and Exponential, Addresses and redirects](../../../install/migration/wordpress.md#4-addresses-and-redirects)).
- The old feed address (`/feed/`) should point to your new RSS export so subscribers stay
  ([RSS feeds for your blog](rss-feeds-for-your-blog.md)).
- Check the 404 errors after the move and add the missing redirects.

## Comments, images and authors

- **Comments** can be imported as *Comment* objects below the posts in a scripted migration, or left on an archived
  copy of the old blog. Remember they contain other people's personal data.
- **Images**: import them first, then the posts, so links can point to the new image objects.
- **Authors**: if a friend wrote for you, create their account and ask them to set a new password; their old one
  cannot be carried over.

## Checklist

- [ ] Export of the old blog saved, including uploads.
- [ ] Option chosen: by hand, RSS import, scripted, or a mix.
- [ ] *Blog* and classes ready in Exponential ([Starting a blog](starting-a-blog.md)).
- [ ] Test with ten posts, including your most complicated one.
- [ ] Images imported before posts.
- [ ] Redirect list from old to new addresses.
- [ ] Old feed address redirected.
- [ ] Old blog kept read-only until the new one has run for a few weeks.

## Related pages

- [Blogs](README.md)
- [WordPress and Exponential](../../../install/migration/wordpress.md)
- [Migrating to and from Exponential](../../../install/migration/README.md)
- [Switching to Exponential](../../businesses/choosing/switching-to-exponential.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
