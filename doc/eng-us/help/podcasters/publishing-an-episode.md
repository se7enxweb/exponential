---
title: Publishing an episode
audience: podcaster
category: podcasting-streaming
order: 13
summary: The whole path of one episode from draft to podcast apps - create it in the right folder, fill in title, audio and show notes, preview, publish now or at a date, and check the page and the feed - with what happens behind each step.
keywords: publish episode, new episode, release, draft, preview, send for publishing, feed, podcast apps, episode checklist, show notes, audio
type: help
---
# Publishing an episode

This page is for producers and editors who publish an episode, from the empty draft to the moment podcast apps can
fetch it. It assumes the show structure and the feed exist ([Setting up the show structure](setting-up-the-show-structure.md),
[Building a podcast feed](building-a-podcast-feed.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Create the draft](#create-the-draft)
3. [Fill in the episode](#fill-in-the-episode)
4. [Preview](#preview)
5. [Publish now or later](#publish-now-or-later)
6. [After publishing](#after-publishing)

## Before you start

Have ready: the final audio file (MP3, constant bitrate), the title, a one- or two-sentence description, the show
notes ([Show notes template](show-notes-template.md)), guest names and links, and an image if your class has one.

## Create the draft

1. In **Content structure**, open the show folder, or the season folder if the show has seasons.
2. Under **Sub items**, press **Create new** and choose the episode class.

The episode must be created **below the folder the feed reads**. An episode created elsewhere is on the site, but not
in the feed ([How a show maps to content](how-a-show-maps-to-content.md)).

## Fill in the episode

| Field (names vary by class) | What to enter |
|---|---|
| Title | The episode title as apps should show it; include the number if you use numbers ("42: ...") |
| Description or teaser | One or two sentences; often what the feed shows as the episode description |
| Audio | The file, under **New file for upload** ([Uploading audio](uploading-audio.md)) |
| Show notes | Links, chapters, credits ([Writing show notes](writing-show-notes.md)) |
| Guests | Relate the person items ([Guests and credits](guests-and-credits.md)) |
| Release date | Only if your site schedules releases ([Scheduling a release](scheduling-a-release.md)) |

**Store draft and exit** keeps the draft for later; it is in **My drafts**
([Publishing, versions and the trash](../publishing-and-versions.md#my-drafts)).

## Preview

Press **Preview** beside the edit form to see the draft as it will look
([The edit form](../editing-content.md#the-edit-form)). Check that the player appears, the show notes'
links work and the image has an alternative text. The feed is not part of the preview: a draft is never in the feed.

## Publish now or later

Press **Send for publishing**.

- On most sites the episode is published at once.
- If your site uses an approval, the version becomes **pending** until the approver accepts it
  ([Approvals before release](approvals-before-release.md)).
- If your site uses the *Wait until date* event and the release date is in the future, the version waits until the
  first run of the workflow cronjob after that date ([Scheduling a release](scheduling-a-release.md)).

Pending versions are listed in **My pending items**.

## After publishing

What happens next, and when:

| When | What |
|---|---|
| At once | The episode page is on the site; lists and menus that show the folder include it |
| Within the feed's cache time (1200 seconds as shipped) | The feed lists the new item at the top (`settings/site.ini [RSSSettings] CacheTime`) |
| On each app's own schedule | Podcast apps and directories fetch the feed and show the episode |

Check, signed out:

- [ ] the episode page opens and the audio plays;
- [ ] the feed lists the episode with an `enclosure` ([Checking your feed](checking-your-feed.md));
- [ ] the item's published date is the one you expect: it decides the order in the feed.

If you find a mistake, **edit the episode**; do not remove and recreate it. A new item gets a new `guid`, and apps
would show the episode twice ([The podcast feed explained](the-podcast-feed-explained.md)).

## Related pages

- [Podcaster checklists](podcaster-checklists.md)
- [Creating and editing content](../editing-content.md)
- [Publishing, versions and the trash](../publishing-and-versions.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
