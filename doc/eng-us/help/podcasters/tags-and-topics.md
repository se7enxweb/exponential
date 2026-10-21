---
title: Tags and topics
audience: podcaster
category: podcasting-streaming
order: 37
summary: How to organize episodes by topic on an Exponential site - the Keywords datatype of the kernel, eztags on installations with that extension, topic pages and lists, and how a keyword field can fill the category of each item in the podcast feed.
keywords: tags, topics, keywords, ezkeyword, eztags, categories, taxonomy, topic pages, main topic, feed category, browse by topic, search
type: help
---
# Tags and topics

This page is for producers with a growing back catalog who want listeners to find episodes by topic, guest type or
theme. Exponential offers two tools: the **Keywords** datatype in the kernel, and **eztags**, an extension with a
managed tag tree, on installations that have it.

## Contents

1. [Keywords or tags](#keywords-or-tags)
2. [Keywords](#keywords)
3. [eztags](#eztags)
4. [Topics in the feed](#topics-in-the-feed)
5. [Good tagging for a show](#good-tagging-for-a-show)

## Keywords or tags

| | Keywords (`ezkeyword`) | eztags |
|---|---|---|
| Where | kernel, `kernel/classes/datatypes/ezkeyword/` | the eztags extension, where installed |
| Entry | free text, comma separated | chosen from a tag tree, or added to it |
| Spelling control | none: "e-mail" and "email" are two keywords | one tag, used everywhere |
| Translations of the tag | no | yes, tags have translations |
| Used by | the *Video* class (**Tags**) | the *NG Video* class (**Main topic**, **Tags**) on media site installations |

For more than a handful of topics, eztags keeps the catalog consistent; see
[Tagging content with eztags](../creators/website-creators/tagging-with-eztags.md).

## Keywords

Add a **Keywords** field to the episode class ([Building an episode class](building-an-episode-class.md)) and type
topics separated by commas. Keep a shared list of the spellings your team uses. A design can list episodes with the
same keyword; the site search finds episodes by their keywords when the field is searchable.

## eztags

On installations with eztags:

1. An administrator creates a tag tree for the show, for example *Topics*, *Formats* (interview, panel, solo),
   *Guests' fields* ([Set it up](../creators/website-creators/tagging-with-eztags.md#set-it-up)).
2. The episode class gets an eztags field limited to that branch.
3. Editors pick tags while editing; new tags can be allowed or not, by permission
   ([Permissions](../creators/website-creators/tagging-with-eztags.md#permissions)).
4. The design shows tag pages that list every episode with a tag
   ([Showing tags on a site](../creators/website-creators/tagging-with-eztags.md#showing-tags-on-a-site)).

With Exponential Layouts, tag-based lists can feed a block of a page, so a show's page can carry "More on this topic"
(`extension/explayouts_tags_query` on installations that have it).

## Topics in the feed

A podcast feed item can carry a `category` element. It comes from the field mapped as **Category** for the source
([Episode mapping reference](episode-mapping-reference.md)). A **Keywords** field works directly: the feed writes
the keywords as one string (`podcastText()` in `kernel/classes/ezrssexport.php` handles `eZKeyword`). An eztags field
is not one of the types `podcastText()` handles, so it writes nothing there; map a keyword or text field instead.

This item category is separate from the show's **Category** in the Apple Podcasts section, which must be one of
Apple's categories ([Channel fields reference](channel-fields-reference.md)).

## Good tagging for a show

- Agree on a small set of topics first; add new ones on purpose, not while typing.
- Tag the format (interview, panel, live) as well as the subject, if listeners ask for it.
- Do not tag guests' names as topics if they have person pages; relate the person instead ([Guests and credits](guests-and-credits.md)).
- Review tags once per season: merge duplicates, remove tags with one episode.

## Related pages

- [Tagging content with eztags](../creators/website-creators/tagging-with-eztags.md)
- [Searching](../searching.md)
- [How a show maps to content](how-a-show-maps-to-content.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
