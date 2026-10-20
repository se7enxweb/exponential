---
title: Image processing and media
audience: hosting, admin
category: hosting
order: 180
summary: What a host needs to provide for Exponential's image handling: GD and ImageMagick and their order, the image aliases and where variations are stored, the upload types accepted and validated, SVG sanitizing, Velocity's on-request resizing, upload limits, permissions and the commands to clear variations.
keywords: images, image processing, media, GD, ImageMagick, convert, image aliases, variations, image.ini, ImageConverters, AliasList, AllowedOutputFormat, ImageUploadSettings, AllowedMIMETypes, fileinfo, SVG, SanitizeSVG, WebP, AVIF, var/storage, upload_max_filesize, imagealias, Velocity, hosting
type: help
---
# Image processing and media

This page is for system administrators and support staff at a hosting company. It answers: which image libraries and
programs an Exponential server needs, how images and their variations are stored, which uploads are accepted, what
Velocity adds, and which PHP and permission settings affect media. The settings are in `settings/image.ini`; the
operator's summary is [chapter 10.8](../../../install/10-after-installing.md#108-search-indexing-and-image-handling).

## Contents

1. [What the server needs](#what-the-server-needs)
2. [The converters and their order](#the-converters-and-their-order)
3. [Image aliases and storage](#image-aliases-and-storage)
4. [Uploads: types, validation and limits](#uploads-types-validation-and-limits)
5. [Velocity's image pipeline](#velocitys-image-pipeline)
6. [Permissions for media](#permissions-for-media)
7. [Commands](#commands)
8. [Checklist](#checklist)
9. [Related pages](#related-pages)

## What the server needs

| Item | Needed for | Checked by |
|---|---|---|
| GD (`gd` extension, `imagegd2()`) or ImageMagick (`convert`) | image variations; at least one is required | critical test `image_conversion` (`Require=one`) |
| Both GD and ImageMagick | GD for the usual formats in process, ImageMagick for formats GD cannot read | recommended ([chapter 2](../../../install/02-requirements.md#21-the-checklist)) |
| GD with FreeType (`imagettftext()`, `imagettfbbox()`) | the text-to-image template operator | optional test `texttoimage_functions` |
| `fileinfo` | reading an uploaded image's type from its content | `[ImageUploadSettings]`; System information warns when it is missing |
| `system()` not disabled | the ImageMagick converter runs `convert` through it | see [PHP settings for Exponential](php-settings-for-exponential.md#functions-some-features-call) |

The setup wizard looks for `convert` in `/bin`, `/sbin`, `/usr/bin`, `/usr/sbin`, `/usr/local/bin` and
`/usr/local/sbin`, and switches the ImageMagick converter off when it finds none. Check with `convert -version` and
`php --ri gd`.

## The converters and their order

`settings/image.ini`, `[ImageConverterSettings] ImageConverters[]` lists the converters in order; the first that can
handle an image is used:

```ini
ImageConverters[]
ImageConverters[]=GD
ImageConverters[]=ImageMagick
```

- **GD comes first**: it is built into PHP, converts in process without starting a program per image, and reads and
  writes JPEG, PNG, GIF and WBMP.
- **ImageMagick is the fallback** for formats GD cannot read (for example PSD, TIFF, PDF or WebP) and for filters only
  it supports. It takes over completely when GD is missing or `[GD] IsEnabled=false`.
- `[ImageMagick] ExecutablePath` (empty: the search path) and `Executable` (`convert`) find the program.

To prefer ImageMagick, override the list in `settings/override/image.ini.append.php` with ImageMagick first.

`[ImageConverterSettings] LockTimeout` (shipped `60`) is how many seconds a process waits while another generates the
same alias; after that the waiting process ends the other one and generates the alias itself.

## Image aliases and storage

- Uploaded images live in the site's storage directory, `var/<site var dir>/storage/images`
  (`image.ini [FileSettings] PublishedImages=images`; drafts and archived versions in `images-versioned`).
- Their variations, the **aliases**, are generated on first use. `[AliasSettings] AliasList[]` names them
  (`reference`, `small`, `tiny`, `medium`, `large`, `rss` and those of the site's design).
- `[OutputSettings] AllowedOutputFormat[]` lists the formats pages may show: JPEG, PNG, WebP and GIF.
- Temporary image files go to the `imagetmp` subdirectory of the cache (`[FileSettings] TemporaryDir`).

For a host this means: image storage grows with uploads **and** with the number of aliases a design defines, and the
first view of a new page can cost CPU while its variations are made.

## Uploads: types, validation and limits

`image.ini [ImageUploadSettings]` decides which uploaded files are accepted as images, for every way in (the edit
form, the upload module, the rich text editor, the AJAX uploader, WebDAV, imports):

| Key | Shipped | Meaning |
|---|---|---|
| `ValidateMIMEType` | `enabled` | the type is read from the file's content with `fileinfo`, not from its name |
| `AllowedMIMETypes[]` | JPEG, PNG, GIF, WebP, AVIF, TIFF, BMP, SVG | a refused file is not stored |
| `SanitizeSVG` | `enabled` | an SVG is stored as the output of the sanitizer, without script, event attributes, `foreignObject` or external references; SVG aliases are never scaled |

Serve SVG files with the headers in [SVG images](../../../bc/6.0/svg-images.md).

**Upload size** is PHP's: `upload_max_filesize` and `post_max_size` must be as large as the largest file customers
upload; Exponential does not test them. State the limit in the plan and set it in the pool
([PHP settings for Exponential](php-settings-for-exponential.md#a-production-set-for-a-pool)).

## Velocity's image pipeline

When Velocity serves the site, it answers static files itself and can resize images on request
([Velocity static files and images](../../../features/6.0/velocity-static-files-and-images.md)):

- `?w=400` or `?h=300` on an image URL resizes it, never above the source's size; AVIF or WebP is chosen for browsers
  that accept them; `Save-Data: on` lowers the quality.
- Results are cached; `Q.images.cache.maxSize` (shipped 268435456 bytes) bounds the cache.
- It needs PHP's GD (without it the original is served), WebP support in GD for WebP, and `imageavif()` (PHP 8.1 with
  libavif) for AVIF.

This is separate from the kernel's image aliases above, which work under every web server.

## Permissions for media

`image.ini [FileSettings]` has its own modes for image directories and files, shipped world-writable:

```ini
# settings/override/image.ini.append.php
[FileSettings]
DirPermissions=0770
ImagePermissions=0660
```

`image.ini` itself prefers `0770` and `0660` with the right user and group. Every process that may generate an alias
(the web PHP, Velocity's workers, cron and console) must be able to write the storage directory; see
[Writable directories and permissions](writable-directories-and-permissions.md).

## Commands

After an alias definition changes, remove the generated variations so they are made again:

```bash
php bin/php/cache.php imagealias clear --allow-root-user
```

The same is available as `exp:cache imagealias` in the console. Images and their variations are part of
`var/storage`, which every backup must include ([What to back up](../operations/backups-what-to-back-up.md)).

## Checklist

- [ ] GD (with FreeType) and ImageMagick installed; `convert -version` and `php --ri gd` answer.
- [ ] `fileinfo` loaded in the web and the command line PHP.
- [ ] `system` not in `disable_functions` where ImageMagick is used.
- [ ] Upload limits set and stated in the plan.
- [ ] `image.ini [FileSettings]` modes hardened.
- [ ] `var/storage` included in backups and in disk monitoring.

## Related pages

- [Chapter 10.8: Search indexing and image handling](../../../install/10-after-installing.md#108-search-indexing-and-image-handling)
- [Media and uploads](../../media-and-uploads.md) (for editors)
- [Velocity static files and images](../../../features/6.0/velocity-static-files-and-images.md)
- [Sizing memory, CPU and disk](sizing-memory-cpu-and-disk.md)
- [CDN](../performance/cdn.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
