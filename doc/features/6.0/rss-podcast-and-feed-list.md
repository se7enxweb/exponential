# RSS: Apple Podcasts feeds, a paged feed list and safer exports

Setup of what the RSS module gained in 6.0 during September 2026. The OPML
format has its own page, [OPML exports](../../bc/6.0/opml.md).

## Apple Podcasts as a feed format

An RSS export can be written as **Apple Podcasts (RSS 2.0 + iTunes)**: RSS 2.0
with the iTunes namespace, so a show published here can be submitted to Apple
without a hand written feed.

### Create one

1. **Setup > RSS > Exports**, create or edit an export.
2. Choose the version *Apple Podcasts (RSS 2.0 + iTunes)*. The *Apple Podcasts*
   section of the form appears.
3. Fill the channel fields (below).
4. Add a content source for the episodes and map its class attributes. The
   *enclosure* mapping must name a media or file attribute holding the audio.
5. Publish the export and open its feed address.

### Channel fields

| Field | Required | Written as |
|---|---|---|
| Author | no | `itunes:author` |
| Owner name, Owner email | yes | `itunes:owner` |
| Artwork address | yes | `itunes:image` (`href`) |
| Category, Subcategory | category yes | nested `itunes:category` |
| Show type (default `episodic`) | no | `itunes:type` |
| Language, Copyright | no | channel elements |
| Subtitle, Summary | no | `itunes:subtitle`, `itunes:summary` |
| Contains explicit content (default off) | no | `itunes:explicit` |
| The show is finished | no | `itunes:complete` |
| Keep the show out of the directory | no | `itunes:block` |
| Moved to | no | `itunes:new-feed-url` |

The fields are stored as JSON in the new column `ezrss_export.podcast_head`.

### Episodes

Each node of the source becomes an item with `title`, `itunes:title`, `link`,
`description`, `pubDate`, an `enclosure` (url, byte length and MIME type read
from the file), a `guid` equal to the object's remote id (it never changes, so
readers do not see old episodes as new), `itunes:author` (the creator),
and `itunes:duration`. Duration is worked out from the file only for constant
bitrate MPEG audio, because the media datatypes store size and type, not
length. An episode with no playable enclosure is left out, since Apple rejects
it. Hidden nodes are skipped unless the site shows invisible nodes.

### Setting

`site.ini` `[RSSSettings]` lists the formats; the shipped list now contains
`AvailableVersionList[]=ITUNES`.

### Upgrade

Existing installations: apply the 6.0.0 to 6.0.15 database update
(`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`), which adds
`ALTER TABLE ezrss_export ADD COLUMN podcast_head longtext;`. A clean install
has it from `share/db_schema.dba`.

## The feed list shows one page at a time

`rss/list` fetches and draws one page of exports and one of imports, so an
installation with thousands of feeds can open the page. Headings sort; the
paging keeps the sort. Page sizes: `content.ini [RSSListSettings]
ItemsPerPageList[]` (25, 50, 250), chosen with the *items per page* control. The sorting
headings are the same classes the locations tab uses, so a sorted column is
marked the same way on both ([Locations tab paging](../../bc/6.0/locations-tab-paging-and-sorting.md)).

## Feed addresses use the public site

A feed advertised itself under `localhost` or the administration host because
the export address came from whichever siteaccess was executing. It is now
resolved from the feed's own siteaccess, falling back to the default access.
OPML outline links are built against the document's own site for the same
reason. Re-open the feed and check its `<link>` once after upgrading.

## Safer output

What the module writes can no longer be turned against whoever reads it:
values are escaped on their way into the documents, and the admin's
consistency checks no longer report a problem nobody can act on. Details and the
test list are in the Security section of [OPML exports](../../bc/6.0/opml.md).

## Cleaning up what an import created

An RSS import adds one object per item and never removes any. See
[RSS import cleanup](../../bc/6.0/cleanuprss.md), a script and a cronjob part.

## Settings at a glance

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/site.ini` | `RSSSettings` | `AvailableVersionList[]` | `1.0`, `2.0`, `ATOM`, `OPML`, `ITUNES` | global |
| `settings/site.ini` | `RSSSettings` | `OPMLMaxOutlines` | `5000` (never above 50000) | global |
| `settings/content.ini` | `RSSListSettings` | `ItemsPerPageList[]` | `25`, `50`, `250` | global |

Check: `grep -n "AvailableVersionList\|OPMLMaxOutlines" settings/site.ini` and `grep -n -A6 RSSListSettings settings/content.ini`.

## See also

- [OPML exports](../../bc/6.0/opml.md) and [RSS import cleanup](../../bc/6.0/cleanuprss.md)
- [Syndication specification](../../specifications/6.0/syndication.md)
- [September 2026, first half: 14 September](../../history/2026/2026-09a.md#14-september-pdf-rss-and-the-rad-tools)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
