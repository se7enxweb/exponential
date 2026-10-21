---
title: Setting up the show structure
audience: podcaster
category: podcasting-streaming
order: 9
summary: Step by step - create the folders for a show, its seasons and its guests in the content tree, describe the show on its folder, decide where the audio files live, and check that the structure matches the feed you will build.
keywords: set up show, folder, season folder, content tree, guests folder, media library, structure, create folder, show page, podcast setup
type: help
---
# Setting up the show structure

This page is for producers and editors who set up a new show on the site. It assumes the episode class exists
([Choosing a class for episodes](choosing-a-class-for-episodes.md)). The background is on
[How a show maps to content](how-a-show-maps-to-content.md). You need permission to create folders where the show will
live; if a step is not offered to you, ask your administrator.

## Contents

1. [Before you start](#before-you-start)
2. [Create the show folder](#create-the-show-folder)
3. [Create season folders](#create-season-folders)
4. [Create a guests folder](#create-a-guests-folder)
5. [Decide where files live](#decide-where-files-live)
6. [Check the result](#check-the-result)

## Before you start

Write down, on paper or in your planning document:

- the show's name as it appears on the site and in apps;
- one paragraph that describes the show;
- whether you number seasons, and how;
- which class episodes use;
- where on the site the show belongs (for example below a "Podcasts" folder in the main menu).

## Create the show folder

1. In the administration, open **Content structure** and go to the place where the show belongs.
2. Under **Sub items**, press **Create new** (or choose **Create here** in the item's menu in the tree) and choose
   **Folder**.
3. Fill in the name of the show and a short description. Add an image if your folder class has an image field.
4. Press **Send for publishing**.

The general steps for creating items are on [Creating and editing content](../editing-content.md#create-a-new-item).

> **Note**
> The folder's description is what visitors read on the show page. The podcast apps read the export's own
> **Description** and **Summary** instead; you fill those in when you build the feed.

## Create season folders

Skip this for a show without seasons.

1. Open the show folder.
2. Create a **Folder** inside it for each season: "Season 1", "Season 2" and so on.
3. Order the folders as you want them on the site. The order of the feed does not depend on it: the feed sorts
   episodes by their published date.

When you later build the feed, tick **Subnodes** on the source, so that episodes in all season folders are listed.

## Create a guests folder

If guests get their own pages, create a folder for them, for example inside the show folder or once for all shows.
Person items go there; episodes relate to them ([Guests and credits](guests-and-credits.md)). On installations with
the media site package the *Person* class (`ng_person`) exists for this.

## Decide where files live

Two ways work; pick one and write it into your team's guidelines:

| Way | How it works | When it fits |
|---|---|---|
| The file inside the episode | The episode's media field holds the file | most shows: one item per episode, simple feed mapping |
| The file in the media library | Files are separate items in **Media**; the episode relates to them | when files are reused or managed by another team |

The podcast feed needs the file in a media or file field **of the episode item itself**: the enclosure mapping names a
field of the class listed in the feed, and a related item is not followed (`podcastItemList()` in
`kernel/classes/ezrssexport.php`). For a feed, the first way is the one that works without template or code changes.

## Check the result

- [ ] The show folder appears on the site where you expected, with its description.
- [ ] Season folders, if any, sit inside the show folder.
- [ ] Your editors can create items of the episode class inside the show (or season) folders.
- [ ] The guests folder exists if you need one.
- [ ] Everyone knows where audio files go.

Next: [Uploading audio](uploading-audio.md) or [Publishing an episode](publishing-an-episode.md), then
[Building a podcast feed](building-a-podcast-feed.md).

## Related pages

- [How a show maps to content](how-a-show-maps-to-content.md)
- [Organizing the media library](../creators/website-creators/organizing-the-media-library.md)
- [Menus and navigation](../creators/website-creators/menus-and-navigation.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
