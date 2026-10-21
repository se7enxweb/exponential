---
title: Storage and bandwidth
audience: podcaster
category: podcasting-streaming
order: 39
summary: What hosting a podcast or video show on an Exponential site costs in disk space and data transfer - where media files are stored, how old versions keep files, how every download passes through PHP with range support, how to estimate traffic, and what to agree with your host about backups and limits.
keywords: storage, disk space, bandwidth, traffic, data transfer, hosting, var storage, original/audio, versions, DefaultVersionHistoryLimit, PHP passthrough, Range, backups, cluster, estimate
type: help
---
# Storage and bandwidth

This page is for producers and site owners who plan the hosting of a show, and for the hosts they talk to. Audio and
video are the largest files a site carries, and a popular feed makes podcast apps download every new episode many
times. Plan both disk and traffic before the first episode.

## Contents

1. [Where files are stored](#where-files-are-stored)
2. [Versions keep files](#versions-keep-files)
3. [How downloads are served](#how-downloads-are-served)
4. [Estimating disk and traffic](#estimating-disk-and-traffic)
5. [Backups](#backups)
6. [What to agree with your host](#what-to-agree-with-your-host)

## Where files are stored

Media and file fields store uploads below the site's storage directory, in `original/<mime group>`: audio in
`original/audio`, video in `original/video` (`kernel/classes/datatypes/ezmedia/ezmediatype.php`). The storage
directory is `var/storage/` or `var/<site>/storage/`, depending on the installation's settings
([Storage layout](../hosting/operations/storage-layout.md#the-var-directory)). On a cluster with the DFS file handler,
the files live on the shared storage instead ([Clustering and shared storage](../hosting/operations/clustering-and-shared-storage.md)).

## Versions keep files

Every edit of an item makes a new version, and older versions are kept up to `settings/content.ini
[VersionManagement] DefaultVersionHistoryLimit` (45 as shipped; `VersionHistoryClass[]` can set it per class). When
you replace an episode's audio, the old file stays on disk as long as an old version still names it; the media
datatype deletes a file only when no version or copy refers to it any more (`deleteStoredObjectAttribute()`).

For a show that replaces audio often, count that in, or ask the administrator for a lower version limit for the
episode class. `bin/php/cleanupversions.php` removes versions beyond a lowered limit (comment in `settings/content.ini`).

## How downloads are served

Players and podcast apps fetch files from `/content/download/...`. The kernel checks that the visitor may read the
item, then the PHP passthrough handler sends the file
(`kernel/classes/binaryhandlers/ezfilepassthrough/ezfilepassthroughhandler.php`):

- every download occupies a PHP worker for as long as the transfer runs; a slow listener on a large file keeps it
  busy;
- `Range` requests are answered with `206 Partial Content`, so players seek and apps resume instead of starting
  over;
- the answer carries `Expires` ten minutes ahead and an empty `Cache-Control` (`eZFile::downloadHeaders()` in
  `lib/ezfile/classes/ezfile.php`), so browsers and proxies may keep it only briefly.

The number of PHP workers therefore limits how many downloads run at the same moment; size the pool with that in mind
([PHP-FPM tuning](../hosting/performance/php-fpm-tuning.md)).

## Estimating disk and traffic

| Figure | How to estimate |
|---|---|
| Disk per episode | file size: bitrate (kbit/s) x length (s) / 8. A 60-minute MP3 at 128 kbit/s is about 57.6 MB |
| Disk per year | episodes per year x file size, plus replaced files kept in versions, plus backups |
| Traffic per episode | file size x downloads; many apps download new episodes automatically for every subscriber |
| Peak | the hours after a release, when apps fetch the new episode |

Example: 52 episodes of 57.6 MB are about 3 GB a year. With 2,000 automatic downloads each, the year's episode traffic
is about 6 TB, most of it in the first days after each release. These are your own numbers to replace; measure real
downloads in your web server logs ([Downloads and statistics](downloads-and-statistics.md)).

## Backups

Media files are part of the files half of a backup: the storage directory must be backed up with the database, at the
same time ([Backups: what to back up](../hosting/operations/backups-what-to-back-up.md)). A show's archive is often
the largest part of a site's backup; agree on retention and test a restore of an episode.

## What to agree with your host

- [ ] Disk space for the next year's episodes, versions and backups.
- [ ] Data transfer included, and the price beyond it.
- [ ] Upload limits for editors ([Upload limits and large files](upload-limits-and-large-files.md)).
- [ ] The number of PHP workers, and the timeouts for long downloads.
- [ ] Whether a CDN is offered, and what it may cache ([CDN and external media hosting](cdn-and-external-media-hosting.md)).
- [ ] Backup frequency and retention of the storage directory.

## Related pages

- [Sizing memory, CPU and disk](../hosting/basics/sizing-memory-cpu-and-disk.md)
- [Hosting for a website](../creators/website-creators/hosting-for-a-website.md)
- [Disk full](../hosting/runbooks/disk-full.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
