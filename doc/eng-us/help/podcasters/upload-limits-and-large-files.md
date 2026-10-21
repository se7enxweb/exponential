---
title: Upload limits and large files
audience: podcaster
category: podcasting-streaming
order: 40
summary: Which limits decide how large an episode file can be on an Exponential site - PHP's upload_max_filesize and post_max_size, the media or file field's maximum size, the upload page's MaxFileSize - the messages each one gives, and what to do with files that are too large.
keywords: upload limit, file size, large files, upload_max_filesize, post_max_size, max_execution_time, MaxFileSize, ezmultiupload, max file size, MB, too big, video files, timeout
type: help
---
# Upload limits and large files

This page is for producers whose episodes are large, and for administrators who set the limits. Several limits apply
at once; the smallest one wins. Knowing which one refused a file saves a lot of guessing.

## Contents

1. [The limits](#the-limits)
2. [Which message means which limit](#which-message-means-which-limit)
3. [Choosing the limits](#choosing-the-limits)
4. [When a file is too large](#when-a-file-is-too-large)
5. [Checklist](#checklist)

## The limits

| Limit | Where | Shipped or suggested value | Applies to |
|---|---|---|---|
| `upload_max_filesize` | PHP (`php.ini` or the FPM pool) | `64M` in the suggested production set | every upload |
| `post_max_size` | PHP | `64M` in the suggested production set; must be at least as large as the upload | every upload |
| **Max file size** of the class attribute | **Setup > Classes**, media or file field, in MB | 0: no limit of its own | that field |
| `MaxFileSize` | `extension/ezmultiupload/settings/ezmultiupload.ini [MultiUploadSettings]`, on installations with the upload page | `64M` | the upload page |
| `max_execution_time`, `request_terminate_timeout` | PHP, the FPM pool | `60` suggested; FPM's off unless set | the time a request may take |

The PHP values are from [PHP settings for Exponential](../hosting/basics/php-settings-for-exponential.md#a-production-set-for-a-pool).
The upload page's own comment says it plainly: "PHP's upload_max_filesize and post_max_size apply as well: the smallest
of the three is the limit."

The upload page shows its limit, for example *At most 64 MB per file*
([Images, media and uploads](../media-and-uploads.md#upload-files)).

## Which message means which limit

| Message or sign | Limit | Source |
|---|---|---|
| "The size of the uploaded file exceeds the limit set by upload_max_filesize directive in php.ini. Please contact the site administrator." | PHP `upload_max_filesize` | `kernel/classes/datatypes/ezmedia/ezmediatype.php` |
| "The size of the uploaded file exceeds the limit set by the upload_max_filesize directive in php.ini.", on a file field | PHP `upload_max_filesize` | `kernel/classes/datatypes/ezbinaryfile/ezbinaryfiletype.php` |
| "The size of the uploaded file exceeds site maximum: %1 bytes." (media field) or "... exceeds the maximum upload size: %1 bytes." (file field) | the class attribute's **Max file size** | the same files |
| The upload page marks the row "too big" before sending | `MaxFileSize` of the upload page | `ezmultiupload` |
| The whole form comes back empty, as if nothing was posted | `post_max_size` exceeded: PHP drops the whole request | PHP |
| The upload runs, then the page fails after a long wait | a time limit of PHP, FPM or the web server | the server |

## Choosing the limits

- Take your largest expected file: a 90-minute video can be several gigabytes, a two-hour MP3 at 128 kbit/s about
  115 MB.
- Set `upload_max_filesize` above it, and `post_max_size` a little above that.
- Raise the upload page's `MaxFileSize` to match, in an override of `ezmultiupload.ini`.
- Use the class attribute's **Max file size** to keep files in check per class, for example 200 MB for audio
  episodes, so a wrong file (an uncompressed WAV) is refused at once.
- Check time limits for slow connections: a 500 MB upload at 10 Mbit/s takes about seven minutes.
- PHP also needs disk space for the temporary upload; the temporary directory must hold the largest file
  ([Storage layout](../hosting/operations/storage-layout.md)).

## When a file is too large

1. **Make it smaller**: export at the bitrate listeners need. Speech is usually well served by a lower bitrate than
   music; Exponential does not re-encode, so the file you upload is what every listener downloads.
2. **Split it**: a very long recording can be two episodes.
3. **Raise the limit**: ask the administrator, with the size you need ([Working with your administrator](working-with-your-administrator.md)).
4. **Host it elsewhere**: for very large video, use a video service and embed it
   ([Embedding a video or a stream](embedding-a-video-or-stream.md)). A podcast feed's enclosure must be a file on
   your site, so for audio podcasts this is not an option without changes
   ([CDN and external media hosting](cdn-and-external-media-hosting.md)).

## Checklist

- [ ] `upload_max_filesize` and `post_max_size` exceed the largest episode.
- [ ] The upload page's `MaxFileSize` matches.
- [ ] Each media field has a sensible **Max file size**.
- [ ] Timeouts allow the slowest expected upload.
- [ ] Disk space covers the temporary copy and the stored file.

## Related pages

- [Uploading audio](uploading-audio.md)
- [PHP settings for Exponential](../hosting/basics/php-settings-for-exponential.md)
- [Files and downloads](../creators/website-creators/files-and-downloads.md#large-files)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
