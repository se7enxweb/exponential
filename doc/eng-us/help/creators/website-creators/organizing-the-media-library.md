---
title: Organizing the media library
audience: website-creator, editor
category: website-creation
order: 340
summary: Set up the media library so editors find and reuse images and files - folder structure, where uploads land (upload.ini), multi-upload, naming, permissions, and cleaning up unused media.
keywords: media library, media, folders, upload, upload.ini, MimeClassMap, multi-upload, ezmultiupload, image class, file class, reuse, permissions, cleanup, website creator
type: help
---
# Organizing the media library

This page is for website creators who set up where images and files live. The media library is a part of the content
tree (`MediaRootNode=43` in `settings/content.ini`, section Media) for content that is used by other content: images,
files, videos. A clear structure there saves editors hours.

## Contents

1. [Media are content objects](#media-are-content-objects)
2. [A folder structure that scales](#a-folder-structure-that-scales)
3. [Where uploads land](#where-uploads-land)
4. [Uploading many files at once](#uploading-many-files-at-once)
5. [Permissions](#permissions)
6. [Keeping it clean](#keeping-it-clean)

## Media are content objects

An uploaded image is an object of the `image` class, a file an object of the `file` class. That means media have
versions, translations of their names and alternative texts, sections, and relations: an article that embeds an image
relates to it, and the administration warns before you remove an image something still uses
([Reverse relation check](../../../features/6.0/reverse-relation-check.md)).

## A folder structure that scales

| Structure | Good for |
|---|---|
| By topic or site area (`Products`, `Team`, `Events`) | sites where editors think in topics |
| By year and month (`2026/10`) | news sites with many images |
| By site (`Site A`, `Site B`) | multisite installations, with roles limited per folder |
| A `Shared` folder | logos, icons and images used everywhere |

![The media library with its folders Images, Files and Multimedia as sub items](../../images/creators/media-folders.webp)

Keep it at most three levels deep; the upload dialog browses at most `MaxDepth=3` levels (`settings/upload.ini`).

## Where uploads land

`settings/upload.ini` decides what an upload becomes and where:

| Setting | Shipped | Meaning |
|---|---|---|
| `[CreateSettings] MimeClassMap[image]` | `image` | images become Image objects |
| `MimeClassMap[video/mp4]` | `video` | MP4 files become Video objects |
| `DefaultClass` | `file` | everything else becomes a File object |
| `[image_ClassSettings] NamePattern` | `<original_filename_base>` | the object's name is the file name without its extension |
| `[LocationSettings] RootNode` | `media` | the upload dialog starts in the media library |
| `[LocationSettings] ClassList[]` | `folder` | the classes shown as places to upload into |

Override them per siteaccess in `upload.ini.append.php`, for example to map a class of your own.

## Uploading many files at once

The `ezmultiupload` extension uploads many files in one go into a folder
([ezmultiupload](../../../features/6.0/extensions/ezmultiupload.md)); editors find the steps in
[Media and uploads](../../media-and-uploads.md).

## Permissions

- Visitors need `content/read` for Section Media only if media are shown on their own pages; embedded images are served
  as files and do not need it.
- Editors need `content/create` in the media folders for the `image` and `file` classes.
- On a multisite, limit each team's create rights to its own media folder by subtree.

## Keeping it clean

- [ ] Name images by what they show, not by camera file names; the name becomes the alternative-text suggestion and the
      address.
- [ ] Require alternative texts on image fields ([The content model and editing](../../../guides/content-model-and-editing.md)).
- [ ] Remove unused media regularly; the reverse relations tab shows whether anything still uses an item.
- [ ] Check files that went missing on disk: [Missing files](../../../guides/missing-files.md).
- [ ] Include `var/<site>/storage` in backups ([Backups and restores](backups-and-restores.md)).

## Related pages

- [Images and image variations](images-and-image-variations.md)
- [Files and downloads](files-and-downloads.md)
- [Media and uploads (for editors)](../../media-and-uploads.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
