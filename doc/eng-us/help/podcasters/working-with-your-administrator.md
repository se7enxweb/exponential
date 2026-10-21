---
title: Working with your administrator
audience: podcaster
category: podcasting-streaming
order: 10
summary: What a show needs from the site's administrator, and the exact settings behind each request - feed permissions, audio formats, upload class mapping, download behavior, scheduling cronjobs, upload limits and embed permissions - so producers can ask precisely and administrators can act quickly.
keywords: administrator, settings, request, rss/edit, mime.ini, upload.ini, file.ini, PassThroughSettings, ContentDisposition, site.ini, RSSSettings, CacheTime, cronjob, workflow, ContentSecurityPolicy, AdditionalSources, php.ini, upload_max_filesize
type: help
---
# Working with your administrator

This page is for producers who need something set up, and for the administrators who do it. Most of what a show
needs is configuration, not code. Each row names the setting and the file in the shipped `settings/` directory;
changes go into an **override** (`settings/override/<file>.append.php` or a siteaccess), never into the shipped
file itself, as the header of every shipped settings file says.

## Contents

1. [Access you need](#access-you-need)
2. [Settings a show may need](#settings-a-show-may-need)
3. [Examples](#examples)
4. [Things the administrator should tell you](#things-the-administrator-should-tell-you)
5. [How to ask](#how-to-ask)

## Access you need

| Task | What it needs |
|---|---|
| Create and edit episodes | Content policies to create the episode class below the show folders |
| Create and edit feeds (**Setup > RSS**) | The `rss/edit` policy; the editor siteaccess does not offer the RSS module at all ([RSS feeds](../../guides/rss-feeds.md#1-opening-the-page)) |
| Build pages with the layouts editor | Access to the layouts editor ([The layouts editor](../layouts-editor.md)) |
| Approve episodes | Being the approver named in the *Approve* workflow event |

Roles and policies: [Roles and policies](../../guides/roles-and-policies.md).

## Settings a show may need

| Need | File and setting | Shipped |
|---|---|---|
| Podcast export format offered | `site.ini [RSSSettings] AvailableVersionList[]=ITUNES` | on |
| How long a feed is cached | `site.ini [RSSSettings] CacheTime` | 1200 seconds |
| How many items a feed can hold | `site.ini [RSSSettings] NumberOfObjectsList[]` | 5 to 1000 |
| An audio format recognized (m4a, aac, opus) | `mime.ini`, a block per suffix with `Types[]` | not listed |
| Uploaded MP3 becomes an episode | `upload.ini [CreateSettings] MimeClassMap[audio/mpeg]` and `[<class>_ClassSettings]` | not mapped: becomes *File* |
| Opening an audio link plays in the browser | `file.ini [PassThroughSettings] ContentDisposition[audio/mpeg]=inline` | `attachment` for audio |
| Publish at a date | a workflow with *Wait until date* on `content / publish / before`, and the `frequent` cronjob part running `workflow.php` | not set up |
| Hide or remove at a date | `content.ini [HideSettings]`, `[UnpublishSettings]` and the cronjobs `hide.php`, `unpublish.php` | lists empty |
| Larger uploads | PHP `upload_max_filesize`, `post_max_size`, `max_execution_time` | depends on the host |
| Embedded players allowed under an enforced policy | `site.ini [ContentSecurityPolicy] AdditionalSources[]=frame-src <host>` | `Mode=auto`: no policy on public siteaccesses |

## Examples

A recognized M4A file (the type name is the administrator's choice; `audio/mp4` and `audio/x-m4a` are both in use):

```ini
# settings/override/mime.ini.append.php
[m4a]
Types[]=audio/mp4
```

Audio links that play in the browser:

```ini
# settings/override/file.ini.append.php
[PassThroughSettings]
ContentDisposition[audio/mpeg]=inline
```

Players of a streaming service allowed by an enforced Content Security Policy:

```ini
# settings/override/site.ini.append.php
[ContentSecurityPolicy]
AdditionalSources[]=frame-src https://player.example.com
```

After a settings change the administrator clears the settings cache, and for feeds also the RSS cache
([Caches](../../guides/caches.md)).

## Things the administrator should tell you

- Which classes you may create, and where.
- Whether scheduled publishing is set up, which date field counts, and how often the `frequent` cronjob part runs.
- The largest file you can upload (the upload page also shows it).
- Whether the public site enforces a Content Security Policy.
- Where backups of uploaded media are kept, and how long ([Storage and bandwidth](storage-and-bandwidth.md)).

## How to ask

Name the show, the exact need and a test case. For example: "Show *Example*: uploaded MP3 files should become
*Audio* items in the folder *Podcasts/Example*. Test: upload `test.mp3`, expect an *Audio* item with the file in
**File**." A request with a test is done once and checked once.

## Related pages

- [What is not built in](what-is-not-built-in.md)
- [Settings page](../../guides/settings-page.md)
- [Cronjobs](../../guides/cronjobs.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
