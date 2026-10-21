---
title: Troubleshooting feeds and media
audience: podcaster
category: podcasting-streaming
order: 44
summary: Symptoms, causes and fixes for the problems shows meet most often on an Exponential site - empty podcast feeds, wrong enclosure types, missing durations, feeds pointing at the wrong host, changes that do not show, audio that downloads instead of playing, refused uploads, dead embeds and scheduled episodes that never appear.
keywords: troubleshooting, problems, empty feed, no episodes, enclosure type, application/octet-stream, duration, wrong host, cache, download instead of play, upload too big, embed blocked, scheduled episode missing, cronjob
type: help
---
# Troubleshooting feeds and media

This page is for producers and administrators when something about a show does not work. Find the symptom, check the
likely cause, apply the fix. Most fixes are on the page linked in the row.

## Contents

1. [The podcast feed](#the-podcast-feed)
2. [Players and files](#players-and-files)
3. [Uploads](#uploads)
4. [Embeds and streams](#embeds-and-streams)
5. [Scheduling and approval](#scheduling-and-approval)
6. [Before asking for help](#before-asking-for-help)

## The podcast feed

| Symptom | Likely cause | Fix |
|---|---|---|
| The feed has a channel but no items | **Enclosure (media)** not mapped, or mapped to a field that is not media or file | map the audio field ([Episode mapping reference](episode-mapping-reference.md)) |
| Some episodes are missing | their audio field is empty; they are in season folders and **Subnodes** is off; they are hidden; they are beyond **Number of objects** | upload the file, tick **Subnodes**, unhide, raise the number ([How a show maps to content](how-a-show-maps-to-content.md)) |
| No episode at all, source shows "(missing)" | the source folder was removed or moved to the trash | choose the show folder again ([Checking your feed](checking-your-feed.md)) |
| `type="application/octet-stream"` on an enclosure | the file's suffix (m4a, aac, opus) is not in `settings/mime.ini` | add the suffix in an override, then upload again ([Working with your administrator](working-with-your-administrator.md)) |
| No `itunes:duration` | the file is not a constant bitrate MP3 | export CBR MP3, or accept it: the element is optional |
| Links and enclosures point to the administration host or `localhost` | **Site URL** set wrongly, or the siteaccess has no public address | empty or correct **Site URL**; check the RSS card's **Links point to** |
| Links use `http://` behind a proxy or CDN | the proxy is not trusted, so the site thinks the request is plain HTTP | set **Site URL** to `https://...`, and have the host configure trusted proxies ([CDN](../hosting/performance/cdn.md#trusting-the-cdn)) |
| A change does not show | the feed cache (`CacheTime`, 1200 seconds) | wait, or clear `rss_cache` ([Checking your feed](checking-your-feed.md#when-a-change-does-not-show)) |
| Category or artwork is empty after saving | the value was dropped by the checks: a category Apple does not list, an address with another scheme | choose from the list; enter a full `https://` address ([Channel fields reference](channel-fields-reference.md)) |
| The feed address answers with an error | the export is inactive or has no access URL | tick **Active**, set **Access URL** |
| Apps show an episode twice | it was deleted and created again, so its `guid` changed | hide the duplicate; in future, edit instead of recreating |
| Item author is a technical account | `itunes:author` of an item is the creating user | create episodes with personal accounts ([Guests and credits](guests-and-credits.md)) |

## Players and files

| Symptom | Likely cause | Fix |
|---|---|---|
| The page shows no player, or an empty box | the media field uses an old plugin player type | set the class attribute's player type to Html5 Audio or Html5 Video ([The media field and the file field](media-field-and-file-field.md)) |
| A link to the audio downloads it instead of playing | `file.ini [PassThroughSettings]` sends audio as `attachment` (the shipped default) | add `ContentDisposition[audio/mpeg]=inline` ([Working with your administrator](working-with-your-administrator.md)) |
| Seeking does not work | a proxy or CDN in front does not pass `Range` requests | have the host check; the site itself answers with `206` ([Checking your feed](checking-your-feed.md#on-the-command-line)) |
| The file plays for editors but not for visitors | the item is in a section visitors may not read | move it to a public section, or that is intended ([Members-only episodes](members-only-episodes.md)) |
| The MP3 became a *File* item without a player | `upload.ini` maps no class for `audio/mpeg` | create episodes in the episode class, or map the type ([Choosing a class for episodes](choosing-a-class-for-episodes.md)) |

## Uploads

| Symptom | Likely cause | Fix |
|---|---|---|
| "exceeds the limit set by upload_max_filesize" | PHP limit | raise it, or make the file smaller ([Upload limits and large files](upload-limits-and-large-files.md)) |
| "exceeds site maximum" or "exceeds the maximum upload size" | the class attribute's **Max file size** | raise it in the class, or make the file smaller |
| The form comes back empty | `post_max_size` exceeded | raise it above the file size |
| The upload page refuses the file before sending | its `MaxFileSize` | raise it in an override of `ezmultiupload.ini` |
| The upload fails after a long time | time limits of PHP, FPM or the web server | raise them for large uploads |

## Embeds and streams

| Symptom | Likely cause | Fix |
|---|---|---|
| "No video configured." | wrong id, or an address that is refused (not http or https, or your own site) | check the id or paste the player address ([Embedding a video or a stream](embedding-a-video-or-stream.md)) |
| The frame stays empty after **Load video** | an enforced Content Security Policy does not allow the host | add it with `AdditionalSources[]=frame-src ...` ([Privacy and third-party players](privacy-and-third-party-players.md#content-security-policy)) |
| Embed code pasted into rich text disappears | rich text cannot store iframes | use the External video block ([Media in rich text](media-in-rich-text.md)) |

## Scheduling and approval

| Symptom | Likely cause | Fix |
|---|---|---|
| A scheduled episode never appears | the `frequent` cronjob part does not run `workflow.php` | the administrator schedules it; the cronjobs page shows each part's state ([Scheduling a release](scheduling-a-release.md)) |
| A scheduled episode appeared at once | the date field was empty or in the past, or the workflow is not on the trigger | check the field and **Setup > Triggers** |
| An approved episode is not published | the workflow cronjob has not run since the approval | wait for it, or have it run ([Approvals before release](approvals-before-release.md)) |
| A hidden or removed episode is still in the feed | the feed cache | wait up to `CacheTime`, or clear `rss_cache` |

## Before asking for help

Write down: the feed address, the episode's address, what you expected, what happened, the time, and the exact
message. The administrator can then look up the error in the logs ([Finding an error by its reference](../../guides/finding-an-error-by-its-reference.md),
[When something goes wrong](../when-something-goes-wrong.md)).

## Related pages

- [Checking your feed](checking-your-feed.md)
- [What is not built in](what-is-not-built-in.md)
- [Podcaster FAQ](podcaster-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
