---
title: Podcaster glossary
audience: podcaster
category: podcasting-streaming
order: 47
summary: The terms of podcasting and streaming and the Exponential terms behind them - from enclosure, guid and RSS export to media datatype, siteaccess, object state and workflow - each explained briefly with the page that goes further.
keywords: glossary, terms, definitions, enclosure, guid, RSS export, feed, channel, item, media datatype, class, siteaccess, section, object state, workflow, cronjob, CBR, bitrate
type: help
---
# Podcaster glossary

Terms from podcasting and streaming, and the Exponential terms you meet when you publish a show. The general
glossary of the documentation is [Glossary](../../glossary.md).

## Contents

1. [Podcasting and streaming](#podcasting-and-streaming)
2. [Exponential terms](#exponential-terms)

## Podcasting and streaming

| Term | Meaning | More |
|---|---|---|
| **Artwork** | the show's cover image; in the feed an address in `itunes:image href` | [Artwork and episode images](artwork-and-episode-images.md) |
| **Bitrate** | how many bits per second the audio uses; with the length it decides the file size | [Storage and bandwidth](storage-and-bandwidth.md) |
| **CBR, VBR** | constant and variable bitrate; Exponential works out the duration only for CBR MP3 | [The podcast feed explained](the-podcast-feed-explained.md) |
| **Channel** | the show-level part of a feed: title, description, artwork, owner, category | [Channel fields reference](channel-fields-reference.md) |
| **Captions** | on-screen text of what is said in a video | [Transcripts and captions](transcripts-and-captions.md) |
| **Directory** | a catalog of podcasts that apps search, such as Apple Podcasts | [Submitting to podcast directories](submitting-to-podcast-directories.md) |
| **Enclosure** | the element of a feed item that points to the episode file, with its length and type | [Episode mapping reference](episode-mapping-reference.md) |
| **Episodic, serial** | how apps present a show: newest first, or in order from the first episode (`itunes:type`) | [Channel fields reference](channel-fields-reference.md) |
| **Feed** | the RSS document apps subscribe to | [Building a podcast feed](building-a-podcast-feed.md) |
| **guid** | an item's permanent identifier; apps use it to know whether an episode is new | [The podcast feed explained](the-podcast-feed-explained.md) |
| **Item** | one episode in a feed | [Episode mapping reference](episode-mapping-reference.md) |
| **MIME type** | the name of a file's format, such as `audio/mpeg` | [Uploading audio](uploading-audio.md) |
| **OPML** | a document that lists feeds, used to share a set of subscriptions | [Feeds for readers and networks](feeds-for-readers-and-networks.md) |
| **Range request** | a request for part of a file, used to seek and resume | [The media field and the file field](media-field-and-file-field.md) |
| **Show notes** | the text that goes with an episode | [Writing show notes](writing-show-notes.md) |
| **Transcript** | the full text of what is said in an episode | [Transcripts and captions](transcripts-and-captions.md) |

## Exponential terms

| Term | Meaning | More |
|---|---|---|
| **Class** | a type of content item with its fields, such as Folder, Video or your Episode | [Choosing a class for episodes](choosing-a-class-for-episodes.md) |
| **Attribute, field** | one field of a class, with a datatype | [Building an episode class](building-an-episode-class.md) |
| **Datatype** | the kind of a field: text line, rich text, media, file, image, relation ... | [Choosing datatypes](../creators/website-creators/choosing-datatypes.md) |
| **Media datatype (`ezmedia`)** | a field that stores audio or video and renders a player | [The media field and the file field](media-field-and-file-field.md) |
| **File datatype (`ezbinaryfile`)** | a field that stores any file and counts downloads | [The media field and the file field](media-field-and-file-field.md) |
| **Content tree, location, node** | where items live; one item can have several locations | [How a show maps to content](how-a-show-maps-to-content.md) |
| **Remote id** | an object's permanent identifier; the podcast feed uses it as `guid` | [The podcast feed explained](the-podcast-feed-explained.md) |
| **Version, draft, pending** | each edit is a new version; a draft is unpublished; pending waits for a workflow | [Publishing, versions and the trash](../publishing-and-versions.md) |
| **RSS export** | a feed the site publishes, at `/rss/feed/<access URL>` | [RSS feeds](../../guides/rss-feeds.md) |
| **Source** | the location, class and mapping an export reads | [Building a podcast feed](building-a-podcast-feed.md) |
| **Access URL** | the last part of a feed's address | [Building a podcast feed](building-a-podcast-feed.md) |
| **Siteaccess** | one way into the site, with its own design, languages and address | [Multilingual shows](multilingual-shows.md) |
| **Section** | a grouping of content used for permissions | [Members-only episodes](members-only-episodes.md) |
| **Role, policy** | what a user group may do | [Roles and policies](../../guides/roles-and-policies.md) |
| **Object state** | a label per state group, usable in policies | [Production stages with object states](production-stages-with-object-states.md) |
| **Workflow, trigger, event** | actions run around publishing, such as approval or waiting for a date | [Scheduling a release](scheduling-a-release.md) |
| **Cronjob, cronjob part** | scheduled scripts; `workflow.php` in part `frequent` resumes waiting workflows | [Cronjobs](../../guides/cronjobs.md) |
| **Layouts editor, block** | the page builder of Exponential Layouts and its building pieces | [The layouts editor](../layouts-editor.md) |
| **Override** | a settings file that changes a shipped setting | [Working with your administrator](working-with-your-administrator.md) |

## Related pages

- [Glossary](../../glossary.md)
- [Glossary for journalists](../press/glossary-for-journalists.md)
- [Podcaster FAQ](podcaster-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
