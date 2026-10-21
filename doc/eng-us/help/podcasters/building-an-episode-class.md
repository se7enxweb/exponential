---
title: Building an episode class
audience: podcaster
category: podcasting-streaming
order: 8
summary: A suggested content class for podcast or video episodes, built only from datatypes Exponential ships - title, show notes, audio or video, artwork, guests, release date, transcript, license - with the settings that make it work with uploads, scheduling and the podcast feed.
keywords: episode class, content class, class editor, datatypes, ezmedia, ezxmltext, ezobjectrelationlist, ezdatetime, ezbinaryfile, ezauthor, ezkeyword, upload.ini, ClassSettings, max file size, administrator
type: help
---
# Building an episode class

This page is for administrators and site builders who create an episode class for a show, and for producers who
want to tell them what the show needs. Classes are edited under **Setup > Classes** by a user with the class
policies ([Content classes for websites](../creators/website-creators/content-classes-for-websites.md)). Every
datatype named below is in `kernel/classes/datatypes/` unless noted.

## Contents

1. [A suggested class](#a-suggested-class)
2. [Choices that matter later](#choices-that-matter-later)
3. [Uploads into the class](#uploads-into-the-class)
4. [Wiring it to the rest](#wiring-it-to-the-rest)
5. [A test before the first episode](#a-test-before-the-first-episode)

## A suggested class

Class name **Episode**, identifier `episode`, object name pattern `<title>`, not a container.

| Attribute | Identifier | Datatype | Required | Why |
|---|---|---|---|---|
| Title | `title` | Text line (`ezstring`) | yes | the episode title, also in the feed |
| Episode number | `episode_number` | Integer (`ezinteger`) | no | for lists and sorting on the site; the feed has no field for it |
| Short description | `teaser` | Text block (`eztext`) | no | one or two sentences for lists and the feed description |
| Show notes | `show_notes` | Rich text (`ezxmltext`) | no | links, chapters, credits, sponsor disclosure |
| Audio | `audio` | Media (`ezmedia`), player type **Html5 Audio** | yes | the episode file and the feed enclosure |
| Artwork | `image` | Image (`ezimage`) | no | shown on the episode page |
| Guests | `guests` | Object relations (`ezobjectrelationlist`) | no | links to person items |
| Hosts and credits | `credits` | Authors (`ezauthor`) | no | names and e-mail addresses of contributors |
| Release date | `release_date` | Date and time (`ezdatetime`) | no | for the *Wait until date* event |
| Transcript | `transcript` | Rich text (`ezxmltext`) | no | the accessible text of the episode |
| Transcript file | `transcript_file` | File (`ezbinaryfile`) | no | a downloadable transcript, if you have one |
| Keywords | `keywords` | Keywords (`ezkeyword`) | no | topics; or eztags on installations with that extension |
| License | `license` | Selection (`ezselection`) | no | the license you publish under ([Rights and licensing](rights-and-licensing.md)) |

For a video show, use Media with player type **Html5 Video** instead of Html5 Audio.

## Choices that matter later

- **The media field's maximum size** is set on the class attribute in MB (`ContentClass_ezmedia_max_filesize_*`,
  up to five digits). Leave it at 0 to rely on PHP's limits alone ([Upload limits and large files](upload-limits-and-large-files.md)).
- **Translatable fields.** Each attribute can be translatable. A translatable media field means each language can
  carry its own audio file; make it not translatable if all languages share one file ([Multilingual shows](multilingual-shows.md)).
- **Searchable.** Show notes and transcript should be searchable, so the site search finds episodes by what was
  said. Media and file fields are not searched for their content.
- **Identifiers are permanent in practice.** Feeds, templates and the upload mapping name them; renaming one later
  means changing all of these.

## Uploads into the class

If episodes should be created by uploading files on the upload page, the administrator maps the type to the class in
an override of `settings/upload.ini`:

```ini
[CreateSettings]
MimeClassMap[audio/mpeg]=episode

[episode_ClassSettings]
FileAttribute=audio
NameAttribute=title
NamePattern=<original_filename_base>
```

The shipped `[file_ClassSettings]` and `[video_ClassSettings]` blocks follow the same pattern. Without the mapping,
an uploaded MP3 becomes a *File* item ([Choosing a class for episodes](choosing-a-class-for-episodes.md)).

## Wiring it to the rest

| Part | What to set | Page |
|---|---|---|
| The feed | A source per show with class **Episode**; map Title to `title`, Description to `teaser`, Enclosure to `audio` | [Building a podcast feed](building-a-podcast-feed.md) |
| Scheduling | The *Wait until date* event with **Release date** as its attribute | [Scheduling a release](scheduling-a-release.md) |
| Templates | A full view for the class in your design, if the default view is not enough | [Template overrides](../creators/website-creators/template-overrides.md) |
| Permissions | Editors may create *Episode* below the show folders | [Roles and policies](../../guides/roles-and-policies.md) |

## A test before the first episode

1. Create a test episode with a short MP3 and publish it.
2. Open the episode page signed out: the player shows and plays.
3. Open the feed: the item is there with an `enclosure` whose `type` is `audio/mpeg` and whose `length` is the file
   size.
4. Remove the test episode (to the trash, then empty it) before you submit the feed anywhere, so no app ever sees it.

## Related pages

- [Choosing a class for episodes](choosing-a-class-for-episodes.md)
- [Designing your content model](../creators/website-creators/designing-your-content-model.md)
- [Choosing datatypes](../creators/website-creators/choosing-datatypes.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
