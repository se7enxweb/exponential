---
title: Media library upkeep
audience: customer, editor, admin
category: customer-success
order: 14
summary: How a site owner keeps the media library of an Exponential site usable over the years - folder rules, who uploads where, the quarterly look at unused images and files with the content checks, missing files and storage, and what to ask the administrator for.
keywords: media library, images, files, upkeep, unused files, unreferenced objects, storage, missing files, folder structure, clean-up, quarterly
type: help
---
# Media library upkeep

This page is for the owner and the person on the team who looks after images and files. The media library is a part
of the content tree kept for content that other content uses: images, files, videos. Every upload is a content item
of its own ([Images, media and uploads](../../media-and-uploads.md)). Without a few rules it fills with duplicates
and files nobody uses; with them, editors find what they need in seconds.

## Contents

1. [Rules to agree once](#rules-to-agree-once)
2. [Who does what](#who-does-what)
3. [The quarterly clean-up](#the-quarterly-clean-up)
4. [What to ask your administrator](#what-to-ask-your-administrator)

## Rules to agree once

| Rule | Why | Source |
|---|---|---|
| One folder structure, at most three levels deep | the upload dialog browses at most three levels (`MaxDepth=3` in `settings/upload.ini`) | [Organizing the media library](../../creators/website-creators/organizing-the-media-library.md) |
| Folders by topic, by year and month, or by site | pick the one your editors think in | same |
| A `Shared` folder for logos and icons | used everywhere, owned by one person | same |
| Name images by what they show | the name becomes the item's title and the suggestion for the alternative text | same |
| Every image with an alternative text | for screen readers and when an image cannot load | [Images, media and uploads](../../media-and-uploads.md) |
| Upload once, reuse by relation | a second copy is a second item to keep up to date | [Relations and content reuse](../../creators/website-creators/relations-and-reuse.md) |

Write the rules into your editor guide and the [Image and file guidelines](image-and-file-guidelines.md).

## Who does what

| Task | Who | How often |
|---|---|---|
| Upload into the right folder, with title and alternative text | every editor | each upload |
| Look at the newest uploads in the media library | media owner | weekly |
| Run the clean-up below | media owner with the administrator | quarterly |
| Decide what to do with large or old files | owner | when the clean-up finds them |

The upload page marks a file with the same name and size already in the location, and lets you skip it
([Images, media and uploads](../../media-and-uploads.md)). Encourage editors to skip rather than "upload anyway".

## The quarterly clean-up

The content checks find what is unused or broken
([The content checks](../../../features/6.0/content-checks.md)). They are in the Setup menu and in the content tree's
context menu (**Advanced**), and only Administrator has them on a new installation, so do this with your
administrator or ask for the policies.

1. **Unreferenced objects in Media.** The published images, files and media below a node that nothing uses: no
   other object, layout, setting, template, role, shop or newsletter entry. Review the list and move what is truly
   unused to the trash.
2. **Broken relations from the content root.** Visible content that points to something deleted, hidden, in the
   trash or never published, with a link to edit the source. Fix each source.
3. **Missing files.** Images and files whose file is gone from the storage
   ([Missing files](../../../guides/missing-files.md)). Upload them again or remove the item.
4. **Storage files.** Files on disk no content uses. They can be moved to a quarantine and deleted from there later;
   nothing is deleted from the storage directly.
5. **Empty the trash only later.** Leave trashed media in the trash for a while in case something still needed them
   ([Archiving and the trash](archiving-and-the-trash.md)).

> **Tip**
> Before removing an image, open its reverse relations: the administration warns before you remove an image that
> something still uses ([Reverse relation check](../../../features/6.0/reverse-relation-check.md)).

## What to ask your administrator

- The policies `contentcheck/unreferenced` and `contentcheck/brokenrelations` for the media owner, if you want them to
  run the checks themselves.
- Which file types and sizes uploads accept. The upload page shows the largest file it accepts; the allowed image
  types are set in `settings/image.ini` ([SVG and image formats](../../creators/website-creators/svg-and-image-formats.md)).
- That `var/<site>/storage` is in the backups ([Backups and restores](../../creators/website-creators/backups-and-restores.md)).
- That the content check report can run from the command line (`./console exp:content-check`) if you want it monthly.

## Related pages

- [Images, media and uploads](../../media-and-uploads.md)
- [Image and file guidelines](image-and-file-guidelines.md)
- [Broken links and content checks](../../creators/website-creators/link-and-content-checks.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
