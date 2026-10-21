---
title: CDN and external media hosting
audience: podcaster
category: podcasting-streaming
order: 41
summary: What a content delivery network can and cannot do for a show on an Exponential site, why episode downloads are not simple static files, what Exponential does not offer for offloading media to object storage or a podcast host, and the options that remain.
keywords: CDN, content delivery network, edge cache, media hosting, podcast host, object storage, S3, offload, content/download, permission check, Cache-Control, Expires, static files, external hosting
type: help
---
# CDN and external media hosting

This page is for producers and site owners whose show outgrows one server's bandwidth, and for the hosts they work
with. It explains what a CDN gains for an Exponential site, why episode files are a special case, and what is and is
not available for hosting media somewhere else.

## Contents

1. [What a CDN does well](#what-a-cdn-does-well)
2. [Why episode files are different](#why-episode-files-are-different)
3. [Caching downloads at the edge](#caching-downloads-at-the-edge)
4. [Hosting media elsewhere](#hosting-media-elsewhere)
5. [Choosing](#choosing)

## What a CDN does well

The hosting section's [CDN](../hosting/performance/cdn.md) page is the reference. In short:

| Content | Cacheable at the edge |
|---|---|
| Stylesheets, scripts, fonts, design images | yes, with the lifetime the server sends |
| Content images (`var/*/storage/images`) | yes: an image's address changes when it is replaced |
| Anonymous pages | only if you switch on cacheable headers for anonymous visitors |
| Signed-in pages, administration, forms | no |

## Why episode files are different

Episode files are not served from a static path. They go through `/content/download/...`, where the kernel first
checks that the visitor may read the item and then sends the file through PHP
(`kernel/classes/binaryhandlers/ezfilepassthrough/ezfilepassthroughhandler.php`). The answer carries an empty
`Cache-Control`, no `Last-Modified`, and `Expires` ten minutes ahead (`eZFile::downloadHeaders()` in
`lib/ezfile/classes/ezfile.php`). The storage path itself (`var/.../storage/original/...`) should not be linked or
exposed, because that bypasses the permission check
([Files and downloads](../creators/website-creators/files-and-downloads.md#protected-downloads)).

## Caching downloads at the edge

A CDN that honors the origin's headers keeps a download for at most about ten minutes. A host can configure the CDN to
cache `/content/download/` longer for public episodes. Before doing so, consider:

- **Permissions**: a cached file is served to anyone. Only public episodes may be cached; members-only files must
  never be ([Members-only episodes](members-only-episodes.md)).
- **Replaced files**: the download address contains the object id, the attribute id and the file name. If you replace
  an episode's audio with a file of the same name, the address stays the same and the edge may keep the old file
  until it expires or is purged.
- **Range requests**: the CDN must pass or answer `Range` requests correctly, or players cannot seek.
- **Counting**: requests answered by the edge never reach the site, so the file field's download counter does not see
  them ([Downloads and statistics](downloads-and-statistics.md)).

This is configuration of the CDN, outside Exponential; the provider's documentation describes how.

## Hosting media elsewhere

What Exponential does **not** offer, in the kernel or the shipped extensions:

- storing uploaded media on object storage (S3 and similar) instead of the server's disk;
- moving files automatically to a podcast host or a media CDN;
- a feed whose enclosures point to files hosted elsewhere: the podcast export always writes
  `<site>/content/download/...` for the mapped media or file field (`podcastItemList()` in
  `kernel/classes/ezrssexport.php`).

What remains:

| Option | Effect |
|---|---|
| A cluster with shared storage (`eZDFSFileHandler`) | several web servers share the files; still your own servers ([Clustering and shared storage](../hosting/operations/clustering-and-shared-storage.md)) |
| A podcast hosting service for audio and feed, the site for pages | the service hosts files and feed; the site embeds its player and links its feed. The site's podcast export is then not used |
| A video service for video | embed with the External video block ([Embedding a video or a stream](embedding-a-video-or-stream.md)) |
| Custom development | an extension can provide another feed writer or a file handler; this is development work, not configuration |

## Choosing

- A small or medium show on adequate hosting: keep everything on the site; it is the simplest and keeps the feed in
  your hands ([Storage and bandwidth](storage-and-bandwidth.md)).
- A show with large peaks: add a CDN for static files and, carefully, for public episode downloads.
- Very large audiences or video: use a specialized service for the media and keep the site for pages, notes and
  archive.

## Related pages

- [CDN](../hosting/performance/cdn.md)
- [HTTP caching and CDNs](../creators/website-creators/http-caching-and-cdns.md)
- [Storage and bandwidth](storage-and-bandwidth.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
