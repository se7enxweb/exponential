# The subitems list: Table options and its columns

The list of sub items under every node in the admin (`content/view/full/<node id>`, the table with
the checkboxes) shows the columns each user chooses in **Table options**. Exponential 6.0.15 turns that
choice from 15 fixed columns into a catalogue of more than a hundred, groups them, lets users sort,
reorder, save presets and export what they see as CSV, and lets site developers add their own column
with a few lines of INI and a PHP function or a template.

Everything a column shows is computed on the server, only for the columns the user made visible and
only for the rows of the page on screen. A column the user may not see (its `Policy[]`) is not offered,
not computed and not sent.

## What users see

**Table options** (the button above the list) has:

- **Number of items per page**: 10, 25, 50, 100 (`subitems.ini [SubitemsSettings] PageSizes[]`), or a
  custom number.
- **Find a column**: a filter box; type part of a name ("alias", "count") and only the matching columns
  stay in the list.
- **The columns by group**: Basic, Node, Object, Version, Location, URLs, SEO, Dates, People, Relations,
  Translations, Workflow, Users, Media, Technical, Custom, and one group "Attributes: <class>" per class
  among the children. A checkbox per column; the column's description is its tooltip.
- **Order of the visible columns**: drag a column to move it, or use its arrow buttons.
- **Column presets**: choose one (SEO, Editorial, Technical, Translations ship with the system), **Save
  current as...** to keep the current columns under a name of your own, **Delete preset** for your own.
- **Export CSV**: downloads every child (up to `CSVLimit`) with the visible columns, in their order.

In the table itself, a click on a sortable column's header sorts the list by it, on the server (so the
order is right across pages). A cell of a column marked `Copy=true` (IDs, paths, URLs, the page title
...) copies its value on a click and says "Copied".

The choice is saved for the user, per part of the admin (the content tree, the media library and the
users keep their own), on the server: it follows the user to every browser. The columns chosen earlier
in the `eZSubitemColumns` cookie are taken over the first time.

The list works this way in every admin design: admin and admin4 have their own `children_detailed.tpl`, the other admin designs (admin2, admin3, sevenx_site_admin) use the one of the design they fall back on. Without the server
functions (an old ezjscore.ini) it falls back to the classic 15 columns.

## The column catalogue

129 columns ship in `settings/subitemscolumns.ini`; on top of them come the automatic attribute
columns (below). "Sort" names the field the list sorts by when the header is clicked (empty: not
sortable); "Policy" the functions a user needs to be offered the column at all. "Kind" is how the value
is made: `built-in` (rendered by the list from the node, as before), a class and its `Field=`, a
`Handler` or a `Template`.

| Group | Columns |
|---|---|
| Basic | 5 (built-in) |
| Node | 16 (2 built-in) |
| Object | 14 (3 built-in) |
| Version | 7 |
| Location | 5 |
| URLs | 10 |
| SEO | 5 |
| Dates | 10 (2 built-in) |
| People | 5 (1 built-in) |
| Relations | 6 |
| Translations | 6 (1 built-in) |
| Workflow | 12 (1 built-in) |
| Users | 9 |
| Media | 12 |
| Technical | 4 |
| Custom | 3 |

### Basic (5)

| Key | Name | Kind | What it shows | Sort | Policy |
|---|---|---|---|---|---|
| `thumbnail` | Thumbnail | built-in | A small preview of the item's image, or its class icon. |  |  |
| `name` | Name | built-in | The item's name, linked to its page in the admin. | name |  |
| `visibility` | Visibility | built-in | Visible, Hidden or Hidden by superior. | visibility |  |
| `type` | Type | built-in | The name of the item's content class. | class_name |  |
| `priority` | Priority | built-in | The priority within the parent; editable in place when the parent sorts by priority. | priority |  |

### Node (16)

| Key | Name | Kind | What it shows | Sort | Policy |
|---|---|---|---|---|---|
| `nodeid` | Node ID | built-in | The ID of the location (node). | node_id |  |
| `noderemoteid` | Node remote ID | built-in | The remote ID of the location, stable across installations. |  |  |
| `parentnodeid` | Parent node ID | expSubitemsNodeColumn `parent_node_id` | The node ID of the item's parent location. |  |  |
| `depth` | Depth | expSubitemsNodeColumn `depth` | How deep the location is in the tree (the content root is 1). | depth |  |
| `pathstring` | Path string | expSubitemsNodeColumn `path_string` | The node IDs from the top of the tree down to the item: /1/2/89/120/. | path |  |
| `pathidentification` | Path identification string | expSubitemsNodeColumn `path_identification` | The location's path as identifiers (ezcontentobject_tree.path_identification_string). | path_string |  |
| `mainnodeid` | Main node ID | expSubitemsNodeColumn `main_node_id` | The node ID of the object's main location (the same as Node ID when this is it). |  |  |
| `childsortfield` | Children sorted by | expSubitemsNodeColumn `sort_field` | How the item sorts its own children: published, name, priority, modified ... |  |  |
| `childsortorder` | Children sort order | expSubitemsNodeColumn `sort_order` | Ascending or descending, for the item's own children. |  |  |
| `hidden` | Hidden | expSubitemsNodeColumn `is_hidden` | Hidden by an editor: the item itself was hidden (not one of its parents). |  |  |
| `invisible` | Invisible | expSubitemsNodeColumn `is_invisible` | Not shown to visitors: hidden itself or below a hidden location. | visibility |  |
| `iscontainer` | Container | expSubitemsNodeColumn `is_container` | Whether the item's class can have children. |  |  |
| `childrencount` | Node children count | expSubitemsNodeColumn `children_count` | The direct children you may read. One count query per row. |  |  |
| `subtreecount` | Subtree size | expSubitemsNodeColumn `subtree_count` | Every item below, all depths, that you may read. One count query per row over the whole subtree: slower for big trees. |  |  |
| `childclasses` | Child classes | expSubitemsNodeColumn `child_classes` | The classes of the direct children with their numbers, most frequent first. One grouped query per row; counts every child. |  |  |
| `newestchildname` | Newest child | expSubitemsNodeColumn `newest_child_name` | The name of the most recently published child you may read. One limited query per row. |  |  |

### Object (14)

| Key | Name | Kind | What it shows | Sort | Policy |
|---|---|---|---|---|---|
| `section` | Section | built-in | The name of the item's section. | section |  |
| `objectid` | Object ID | built-in | The ID of the content object. | contentobject_id |  |
| `objectremoteid` | Object remote ID | built-in | The remote ID of the content object, stable across installations. |  |  |
| `classidentifier` | Class identifier | expSubitemsObjectColumn `class_identifier` | The identifier of the item's class, as templates and fetches use it. | class_identifier |  |
| `classid` | Class ID | expSubitemsObjectColumn `class_id` | The ID of the item's class. |  |  |
| `classgroups` | Class groups | expSubitemsObjectColumn `class_groups` | The class groups the item's class belongs to (one query per class, not per row). |  |  |
| `sectionidentifier` | Section identifier | expSubitemsObjectColumn `section_identifier` | The identifier of the item's section (sorting orders by section ID). | section |  |
| `sectionid` | Section ID | expSubitemsObjectColumn `section_id` | The ID of the item's section. | section |  |
| `wordcount` | Word count | expSubitemsObjectColumn `word_count` | Words in the main text: the first of body, description, intro, short_description, else the first rich text, XML or text block with content. Reads the data map. |  |  |
| `textlength` | Text length | expSubitemsObjectColumn `text_length` | Characters in the main text, markup left out. Reads the data map. |  |  |
| `readingtime` | Reading time | expSubitemsReadingTimeColumn | Minutes a reader needs for the main text (see Word count), at WordsPerMinute words a minute. Reads the data map. |  |  |
| `namelength` | Name length | expSubitemsObjectColumn `name_length` | Characters in the item's name. |  |  |
| `rating` | Rating | expSubitemsRelationColumn `rating` | The average star rating (needs the ezstarrating extension); empty when never rated. |  |  |
| `ratingcount` | Ratings | expSubitemsRelationColumn `rating_count` | How many star ratings were given (needs the ezstarrating extension). |  |  |

### Version (7)

| Key | Name | Kind | What it shows | Sort | Policy |
|---|---|---|---|---|---|
| `version` | Object version | expSubitemsVersionColumn `version` | The number of the current (published) version. |  |  |
| `versioncount` | Versions | expSubitemsVersionColumn `version_count` | Every stored version: published, archived, drafts and the rest. |  |  |
| `draftcount` | Drafts | expSubitemsVersionColumn `draft_count` | Open drafts that are not published yet. |  |  |
| `draftauthors` | Draft authors | expSubitemsVersionColumn `draft_authors` | Who has an open draft of the item. |  |  |
| `latestdraft` | Latest draft | expSubitemsVersionColumn `latest_draft` | When the most recent open draft was last changed. |  |  |
| `archivedcount` | Archived versions | expSubitemsVersionColumn `archived_count` | Earlier versions kept in the history. |  |  |
| `versioncreated` | Version created | expSubitemsVersionColumn `version_created` | When the current version was started (Modified says when it was published). |  |  |

### Location (5)

| Key | Name | Kind | What it shows | Sort | Policy |
|---|---|---|---|---|---|
| `parentname` | Parent | expSubitemsNodeColumn `parent_name` | The name of the parent location (fetched once per page). |  |  |
| `ismain` | Main location | expSubitemsNodeColumn `is_main` | Whether this is the object's main location. |  |  |
| `locationcount` | Locations | expSubitemsURLColumn `location_count` | How many locations the object has. One query per row. |  |  |
| `otherlocations` | Other locations | expSubitemsURLColumn `other_locations` | The URL aliases of the object's other locations. |  |  |
| `mainlocation` | Main location path | expSubitemsURLColumn `main_location` | The URL alias of the main location, when this row is not it. |  |  |

### URLs (10)

| Key | Name | Kind | What it shows | Sort | Policy |
|---|---|---|---|---|---|
| `urlalias` | URL alias | expSubitemsURLColumn `url_alias` | The item's URL alias as the admin uses it (the path from the content root); / for the node at the site root. | path_string |  |
| `allaliases` | All URL aliases | expSubitemsURLColumn `all_aliases` | Every own URL alias of the item, in all its languages (/ is the site root). Shares the alias query with the other alias columns. |  |  |
| `systemurl` | System URL | expSubitemsURLColumn `system_url` | content/view/full/<node id>: the address that always works, whatever the aliases. |  |  |
| `publicpath` | Public path | expSubitemsURLColumn `public_path` | The path visitors use on the public siteaccess: the URL alias without that siteaccess's PathPrefix. |  |  |
| `publicurl` | Public URL | expSubitemsURLColumn `public_url` | The full address on the public site: the public siteaccess's SiteURL and the public path. |  |  |
| `urlslug` | URL slug | expSubitemsURLColumn `url_slug` | The last element of the URL alias. |  |  |
| `aliascount` | URL aliases | expSubitemsURLColumn `alias_count` | The item's own URL aliases, one per translation normally. One query per row, shared with the other alias columns. |  |  |
| `customaliases` | Custom URL aliases | expSubitemsURLColumn `custom_aliases` | The extra aliases made in Manage URL aliases. |  |  |
| `customaliascount` | Custom URL alias count | expSubitemsURLColumn `custom_alias_count` | How many extra aliases were made in Manage URL aliases. |  |  |
| `historycount` | Old URLs | expSubitemsURLColumn `history_count` | Former addresses (after renames and moves) that still redirect to the item. |  |  |

### SEO (5)

| Key | Name | Kind | What it shows | Sort | Policy |
|---|---|---|---|---|---|
| `pagetitle` | Page title in the browser | expSubitemsSEOColumn `page_title` | The public site's title for the item's page, worked out as its pagelayout does, without rendering the page. |  |  |
| `titlelength` | Page title length | expSubitemsSEOColumn `title_length` | Characters in the page title; search engines show about 60. |  |  |
| `metatitle` | Meta title | expSubitemsSEOColumn `meta_title` | The title set in the item's metadata attribute (xrowmetadata); empty when none. |  |  |
| `metadescription` | Meta description | expSubitemsSEOColumn `meta_description` | The description set in the item's metadata attribute (xrowmetadata). |  |  |
| `metakeywords` | Meta keywords | expSubitemsSEOColumn `meta_keywords` | The keywords set in the item's metadata attribute (xrowmetadata). |  |  |

### Dates (10)

| Key | Name | Kind | What it shows | Sort | Policy |
|---|---|---|---|---|---|
| `modified` | Modified | built-in | When the current version was published. | modified |  |
| `published` | Published | built-in | When the item was first published. | published |  |
| `modifiedsubnode` | Subtree modified | expSubitemsNodeColumn `modified_subnode` | When anything in the item's subtree last changed. | modified_subnode |  |
| `publishedage` | Published (age) | expSubitemsDateColumn `published_age` | How long ago the item was first published: 3 days ago. | published |  |
| `modifiedage` | Modified (age) | expSubitemsDateColumn `modified_age` | How long ago the current version was published. | modified |  |
| `dayssincepublished` | Days since published | expSubitemsDateColumn `days_since_published` | Whole days since the item was first published. | published |  |
| `dayssincemodified` | Days since modified | expSubitemsDateColumn `days_since_modified` | Whole days since the current version was published. | modified |  |
| `publishediso` | Published (ISO 8601) | expSubitemsDateColumn `published_iso` | The publishing time as 2026-10-02T09:30:00+00:00, for copying. | published |  |
| `modifiediso` | Modified (ISO 8601) | expSubitemsDateColumn `modified_iso` | The modification time as 2026-10-02T09:30:00+00:00, for copying. | modified |  |
| `newestchild` | Newest child published | expSubitemsNodeColumn `newest_child` | When the most recently published child you may read was published. One limited query per row. |  |  |

### People (5)

| Key | Name | Kind | What it shows | Sort | Policy |
|---|---|---|---|---|---|
| `modifier` | Modifier | built-in | Who published the current version. |  |  |
| `owner` | Owner | expSubitemsObjectColumn `owner` | The user who created the object (its owner). |  |  |
| `ownerid` | Owner ID | expSubitemsObjectColumn `owner_id` | The object ID of the owner. |  |  |
| `initialcreator` | First author | expSubitemsVersionColumn `initial_creator` | Who wrote the first version still stored. |  |  |
| `contributors` | Contributors | expSubitemsVersionColumn `contributors` | Everyone who wrote a version, in the order of their first version. |  |  |

### Relations (6)

| Key | Name | Kind | What it shows | Sort | Policy |
|---|---|---|---|---|---|
| `relatedcount` | Related objects | expSubitemsRelationColumn `related_count` | Objects the item relates to (all relation types). One count query per row. |  |  |
| `relatednames` | Related object names | expSubitemsRelationColumn `related_names` | The names of the first related objects you may read, by name. |  |  |
| `reverserelatedcount` | Reverse related objects | expSubitemsRelationColumn `reverse_related_count` | Objects that relate to the item: what would point nowhere if it were removed. One count query per row. |  |  |
| `reverserelatednames` | Reverse related names | expSubitemsRelationColumn `reverse_related_names` | The names of the first objects that relate to the item and that you may read. |  |  |
| `tags` | Tags | expSubitemsRelationColumn `tags` | The tags on the item (needs the eztags extension and a tags attribute). |  |  |
| `tagcount` | Tag count | expSubitemsRelationColumn `tag_count` | How many tags the item has (needs the eztags extension). |  |  |

### Translations (6)

| Key | Name | Kind | What it shows | Sort | Policy |
|---|---|---|---|---|---|
| `translations` | Translations | built-in | The flags of the languages the item is translated into. |  |  |
| `initiallanguage` | Initial language | expSubitemsObjectColumn `initial_language` | The language the item was first written in. |  |  |
| `languagecount` | Languages | expSubitemsObjectColumn `language_count` | How many languages the item is translated into. |  |  |
| `languagecodes` | Language codes | expSubitemsObjectColumn `language_codes` | The locales of the translations, e.g. eng-GB, ger-DE. |  |  |
| `missingtranslations` | Missing translations | expSubitemsObjectColumn `missing_translations` | The installation's languages the item is not translated into. |  |  |
| `alwaysavailable` | Always available | expSubitemsObjectColumn `always_available` | Shown in every language, also those it has no translation in. |  |  |

### Workflow (12)

| Key | Name | Kind | What it shows | Sort | Policy |
|---|---|---|---|---|---|
| `objectstate` | Object state | built-in | The item's object states, as group/state identifiers. |  |  |
| `statusbadge` | Status | Template | One badge: Visible, Hidden, Hidden by a parent or Locked (a template column). |  |  |
| `locked` | Locked | expSubitemsObjectColumn `locked` | In the "locked" state of the Lock (ez_lock) state group. |  |  |
| `pendingcount` | Pending versions | expSubitemsVersionColumn `pending_count` | Versions waiting in a workflow, an approval for instance. |  |  |
| `rejectedcount` | Rejected versions | expSubitemsVersionColumn `rejected_count` | Versions an approver rejected. |  |  |
| `workflowprocesses` | Workflow processes | expSubitemsVersionColumn `workflow_processes` | Workflow processes running for the item (an approval, a delayed publication). One count query per row. |  |  |
| `canedit` | You can edit | expSubitemsPermissionColumn `can_edit` | Whether you may edit the item. |  |  |
| `canremove` | You can remove | expSubitemsPermissionColumn `can_remove` | Whether you may remove the item. |  |  |
| `canmove` | You can move | expSubitemsPermissionColumn `can_move` | Whether you may move the item. |  |  |
| `canhide` | You can hide | expSubitemsPermissionColumn `can_hide` | Whether you may hide and reveal the item. |  |  |
| `cantranslate` | You can translate | expSubitemsPermissionColumn `can_translate` | Whether you may translate the item. |  |  |
| `cancreate` | You can create below | expSubitemsPermissionColumn `can_create` | Whether you may create content below the item; empty when it is not a container. |  |  |

### Users (9)

| Key | Name | Kind | What it shows | Sort | Policy |
|---|---|---|---|---|---|
| `userlogin` | Login | expSubitemsUserColumn `login` | The user's login name. |  | role/read |
| `useremail` | E-mail | expSubitemsUserColumn `email` | The user's e-mail address. Only for users who may assign roles. |  | role/assign |
| `userenabled` | Account enabled | expSubitemsUserColumn `enabled` | Whether the account may log in. |  | role/read |
| `userlocked` | Locked out | expSubitemsUserColumn `locked` | Locked out after too many failed logins (site.ini [UserSettings] MaxNumberOfFailedLogin). |  | role/read |
| `userlastvisit` | Last visit | expSubitemsUserColumn `last_visit` | When the user's last visit began; empty when they never logged in. |  | role/read |
| `userlogincount` | Logins | expSubitemsUserColumn `login_count` | How many times the user has logged in. |  | role/read |
| `userfailedlogins` | Failed logins | expSubitemsUserColumn `failed_logins` | Failed login attempts since the last successful one. |  | role/assign |
| `userroles` | Roles | expSubitemsUserColumn `roles` | The roles that apply to the user, directly or through their groups. Two queries per row. |  | role/read |
| `userrolecount` | Role count | expSubitemsUserColumn `role_count` | How many roles apply to the user. |  | role/read |

### Media (12)

| Key | Name | Kind | What it shows | Sort | Policy |
|---|---|---|---|---|---|
| `hasimage` | Has image | expSubitemsMediaColumn `has_image` | Whether the item has an image; empty when its class has no image attribute. |  |  |
| `imagecount` | Images | expSubitemsMediaColumn `image_count` | Image attributes that hold an image. |  |  |
| `imagedimensions` | Image dimensions | expSubitemsMediaColumn `image_dimensions` | Width x height of the original image, in pixels. |  |  |
| `imagewidth` | Image width | expSubitemsMediaColumn `image_width` | The width of the original image, in pixels. |  |  |
| `imageheight` | Image height | expSubitemsMediaColumn `image_height` | The height of the original image, in pixels. |  |  |
| `imagesize` | Image file size | expSubitemsMediaColumn `image_size` | The size of the original image file (in bytes in the CSV). |  |  |
| `imagemime` | Image type | expSubitemsMediaColumn `image_mime` | The MIME type of the original image. |  |  |
| `imagealt` | Image alternative text | expSubitemsMediaColumn `image_alt` | The image's alternative text; empty when it has none, which is worth fixing. |  |  |
| `filename` | File name | expSubitemsMediaColumn `file_name` | The name the file (binary file or media) was uploaded with. |  |  |
| `filesize` | File size | expSubitemsMediaColumn `file_size` | The size of the stored file (in bytes in the CSV). |  |  |
| `filemime` | File type | expSubitemsMediaColumn `file_mime` | The MIME type of the file. |  |  |
| `filedownloads` | Downloads | expSubitemsMediaColumn `file_downloads` | How often the file was downloaded through content/download. |  |  |

### Technical (4)

| Key | Name | Kind | What it shows | Sort | Policy |
|---|---|---|---|---|---|
| `searchwords` | Search index words | expSubitemsPermissionColumn `search_words` | Word positions the built-in search engine indexed for the item (0: not indexed). One count query per row; empty with another search engine. |  |  |
| `viewcount` | View count | expSubitemsNodeColumn `view_count` | Views counted in ezview_counter (filled by the updateviewcount cronjob); empty when never counted. |  |  |
| `languagemask` | Language mask | expSubitemsObjectColumn `language_mask` | The object's language bit mask (bit 0: always available). |  |  |
| `attributecount` | Attributes | expSubitemsObjectColumn `attribute_count` | Attributes of the current version in the shown language. Reads the data map. |  |  |

### Custom (3)

| Key | Name | Kind | What it shows | Sort | Policy |
|---|---|---|---|---|---|
| `teaser` | Teaser | Template | The first words of the intro, description or body (a template column). |  |  |
| `editlink` | Edit link | Handler | The address of the item's edit page, when you may edit it (a Handler column). |  |  |
| `daysonline` | Days online | Handler | Whole days since the item was first published (a Handler column). | published |  |

### How some columns work it out

- **Page title in the browser** (`pagetitle`) is the `<title>` the public site prints for the item's
  page, worked out from the node and the public siteaccess's settings, without rendering the page. The
  public siteaccess is `SiteAccess=` in the block, else `site.ini [SiteSettings] DefaultAccess` (on
  alpha: `site`). `TitleFormat=` picks the rule of the design that siteaccess uses:
  - `name` (the default; the media design, `extension/sevenx_themes_media/design/media/templates/pagelayout/head/title.tpl`):
    `<page title> - <SiteName>`, where the page title is the meta title of the item's xrowmetadata
    attribute when it has one, else the item's name; the page title alone when it equals SiteName.
    So on alpha the media root is "Media - Fit & Healthy", and the front page node "Fit & Healthy" is
    just "Fit & Healthy".
  - `path` (`design/standard` and ezwebin `page_head.tpl`): the names of the item and its ancestors,
    the item first, joined by " / ", then " - <SiteName>": "Banners / Media - Bold Agency" (the
    example under "The shipped families").

  SiteName is the public siteaccess's `[SiteSettings] SiteName`; names are taken in its first
  `SiteLanguageList[]` language when the item has it. The value is the plain text (with `&`, not
  `&amp;`), as the browser shows it. A meta title is used as it is stored; the xrowmetadata
  `metadata()` operator leaves `[name]` style placeholders in place, and so does the column.
- **URL alias** (`urlalias`) is the alias the admin uses (`url_alias` of the node). The kernel answers
  an empty alias in two cases, and the column resolves both from the alias table instead of showing an
  empty cell: the node that owns the root element of `ezurlalias_ml` (the row with parent 0 and empty
  text) is the page at the site root and shows `/`; the content root (`[NodeSettings] RootNode`), whose
  `pathWithNames()` is always empty, shows its own alias when the root element belongs to another node.
  On alpha the root element belongs to node 89 ("Fit & Healthy", the front page), which also has the
  named alias `fit-healthy` its children hang below, and node 2 is reached at `websites`. **All URL
  aliases** (`allaliases`) lists every own alias of the item.
- **Public path / Public URL** take the public siteaccess's `PathPrefix` off the alias (unless
  `PathPrefixExclude[]` keeps it, as `urlAlias()` does) and put its `SiteURL` in front, with
  `Scheme=https` unless SiteURL has a scheme.
- **Hidden** is the item hidden by an editor; **Invisible** is "not shown to visitors", hidden itself or
  below a hidden node. They differ for everything below a hidden node.
- **Node children count** counts the children the current user may read; **Child classes** counts every
  child per class (numbers only, no names).
- **Word count**, **Text length** and **Reading time** read the main text: the first of `body`,
  `description`, `intro`, `short_description` (or `Attributes[]` in the block) that is a rich text, XML
  or text block with content, else the first such attribute at all; markup is left out.
- **Users** columns are empty for anything that is not a user account.
- **Rating**, **Tags** need the ezstarrating and eztags extensions and an attribute of that kind; without
  them they are empty, never an error.

Every column gives an empty cell (null) when it does not apply to an item's class, and never stops the
list: a column that fails gives an empty cell and a debug warning.

## Adding a column in 3 minutes

A column is one `[Column_<key>]` block. Put it in `settings/override/subitemscolumns.ini.append.php`, or
better in an extension: `extension/<yours>/settings/subitemscolumns.ini.append.php` (with the
extension listed in `ActiveExtensions[]`). The key is lowercase letters, digits and `_`. Clear the INI
cache afterwards (`php bin/php/ezcache.php --clear-tag=ini`); reload the PHP-FPM that serves the admin
when a PHP class was added (and regenerate the autoloads). A **new template** also needs the template
override cache cleared (`--clear-id=template-override`), or the cell stays empty.

The three examples below are the ones `tests/tests/kernel/classes/subitems/columns/fixtures/subitemscolumns.ini.append.php`
holds; `expSubitemsGuideExamplesTest` loads that file into a registry and computes the columns as the
rows server function does.

### 1. A Handler: one function

```ini
[Column_mydaysonline]
Name=Days online
Group=Custom
Type=number
Handler=expSubitemsColumnHandlers::daysOnline
SortField=published
Align=right
Order=5000
Description=Whole days since the item was first published.
```

```php
class expSubitemsColumnHandlers
{
    public static function daysOnline( eZContentObjectTreeNode $node, array $settings, expSubitemsColumn $column )
    {
        $object = $node->attribute( 'object' );
        if ( !$object instanceof eZContentObject || (int)$object->attribute( 'published' ) <= 0 )
            return null;
        return (int)floor( ( time() - (int)$object->attribute( 'published' ) ) / 86400 );
    }
}
```

The function gets the row's node, the block as an array and the column object, and returns the value:
null, a scalar, or a list of scalars. `Type=` decides how the cell and the CSV show it (a number here).
`SortField=published` makes the header sort the list by publishing date. The shipped
`kernel/classes/subitems/columns/expsubitemscolumnhandlers.php` has this one and `editLink`.

### 2. A Class: a column of your own

```ini
[Column_myreadingtime]
Name=Reading time
Group=Custom
Type=number
Class=expSubitemsReadingTimeColumn
WordsPerMinute=250
Align=right
Order=5010
Description=Minutes a reader needs for the main text.
```

```php
class expSubitemsReadingTimeColumn extends expSubitemsColumn
{
    public function value( eZContentObjectTreeNode $node )
    {
        $words = new expSubitemsObjectColumn( $this->key, array( 'Field' => 'word_count' ) + $this->settings );
        $count = $words->value( $node );
        if ( !$count )
            return null;
        $perMinute = max( 1, (int)$this->setting( 'WordsPerMinute', 200 ) );
        return max( 1, (int)ceil( $count / $perMinute ) );
    }

    public function html( eZContentObjectTreeNode $node, $value )
    {
        return $value === null ? '' : self::escape( $value . ' ' . ezpI18n::tr( 'design/admin/node/view/full', 'min' ) );
    }
}
```

A class extends `expSubitemsColumn` and implements `value()`; it may override `html()` (the cell; escape
everything with `self::escape()`), `text()` (the CSV), `sortBy()` and `isAvailable()`. It reads its own
settings with `$this->setting( 'Name', $default )`, so one class can serve several blocks. This is the
shipped `readingtime` column (`kernel/classes/subitems/columns/expsubitemsreadingtimecolumn.php`).

### 3. A Template: markup without PHP

```ini
[Column_mystatus]
Name=Status
Group=Custom
Type=html
Template=design:subitems/columns/statusbadge.tpl
Order=5020
Description=What visitors see of the item.
```

```smarty
{def $exp_badge = 'visible'}
{if $node.is_hidden}{set $exp_badge = 'hidden'}
{elseif $node.is_invisible}{set $exp_badge = 'invisible'}
{elseif $node.object.state_identifier_array|contains( 'ez_lock/locked' )}{set $exp_badge = 'locked'}{/if}
{switch match=$exp_badge}
{case match='hidden'}<span class="exp-subitems-badge exp-subitems-badge-hidden">{'Hidden'|i18n( 'design/admin/node/view/full' )}</span>{/case}
...
{case}<span class="exp-subitems-badge exp-subitems-badge-visible">{'Visible'|i18n( 'design/admin/node/view/full' )}</span>{/case}
{/switch}
{undef $exp_badge}
```

The template gets `$node` (the row), `$column` (the block as a hash: `$column.Length`,
`$column.Attributes`) and `$key`. Its output is the cell; its text without tags ("Visible") is the value
and the CSV text. Escape what you print (`|wash`). The shipped `design/standard/templates/subitems/columns/statusbadge.tpl`
and `teaser.tpl` are both examples; `teaser.tpl` shows reading block settings and an XML text block.

### The shipped families

Most shipped columns are one class per family with `Field=` choosing the column, so a block of your own
can reuse them with other settings (another `SiteAccess=` for the page title, `Limit=` for relation
names, `Attributes[]` for the main text or the image):

| Class | Fields |
|---|---|
| `expSubitemsNodeColumn` | parent_node_id, parent_name, depth, path_string, path_identification, main_node_id, is_main, sort_field, sort_order, is_hidden, is_invisible, is_container, children_count, subtree_count, child_classes, newest_child, newest_child_name, modified_subnode, view_count |
| `expSubitemsObjectColumn` | class_identifier, class_id, class_groups, section_identifier, section_id, owner, owner_id, initial_language, language_count, language_codes, missing_translations, always_available, language_mask, state_identifiers, locked, word_count, text_length, name_length, attribute_count |
| `expSubitemsVersionColumn` | version, version_count, draft_count, draft_authors, latest_draft, archived_count, pending_count, rejected_count, initial_creator, contributors, version_created, workflow_processes |
| `expSubitemsURLColumn` | url_alias, all_aliases, system_url, public_path, public_url, url_slug, alias_count, custom_aliases, custom_alias_count, history_count, location_count, other_locations, main_location |
| `expSubitemsSEOColumn` | page_title, title_length, meta_title, meta_description, meta_keywords |
| `expSubitemsDateColumn` | published_age, modified_age, days_since_published, days_since_modified, published_iso, modified_iso |
| `expSubitemsUserColumn` | login, email, enabled, locked, last_visit, login_count, failed_logins, roles, role_count |
| `expSubitemsMediaColumn` | has_image, image_count, image_dimensions, image_width, image_height, image_size, image_mime, image_alt, file_name, file_size, file_mime, file_downloads |
| `expSubitemsRelationColumn` | related_count, related_names, reverse_related_count, reverse_related_names, tags, tag_count, rating, rating_count |
| `expSubitemsPermissionColumn` | can_edit, can_remove, can_move, can_hide, can_translate, can_create, search_words |

For example, the title of the `bold` siteaccess, by the path rule (design/standard style):

```ini
[Column_pagetitle_bold]
Name=Page title (bold)
Group=SEO
Type=text
Class=expSubitemsSEOColumn
Field=page_title
SiteAccess=bold
TitleFormat=path
Copy=true
Order=701
Description=The <title> of the bold siteaccess.
```

On alpha the media root's first child then shows "Banners / Media - Bold Agency". A `SiteAccess=` that is
not in `AvailableSiteAccessList[]` gives an empty cell.

## Automatic attribute columns

With `subitems.ini [SubitemsSettings] AttributeColumns=enabled`, Table options also offers one column
per attribute of every class among the parent's children, in a group "Attributes: <class name>", key
`attr:<class identifier>/<attribute identifier>` (one query finds the classes). The value is the
attribute's title, else its text without tags (at most 300 characters); the column sorts when the
datatype has a sort key, and sorting by it limits the list to that class (subTree's attribute sort
leaves the other classes out; the count stays right). `AttributeColumnsExcludedDataTypes[]` keeps
datatypes out (`ezuser` by default: its text holds the password hash), and
`AttributeColumnsDataTypePolicy[<datatype>]=module/function` gives a datatype's columns a policy.

## Policies

`Policy[]=module/function` entries in a block must all be granted (any limitation counts as granted) or
the column does not exist for that user: not in Table options, not computed, not in the rows or the
CSV, and a saved choice or preset that names it simply leaves it out. Several entries can also be
written in one line separated by `;`. The rows themselves always respect `content/read`: the list and
the CSV only show children the user may read, and the rows function requires read access to the parent.

The shipped policies: the **Users** group needs `role/read` (the people who see the role setup), and
**E-mail** and **Failed logins** need `role/assign`. Columns that list names of other objects (related
objects) leave out those the user may not read.

## Defaults per subtree and class

What a user sees before saving a choice of their own comes from `subitems.ini`: the first
`[Defaults_<id>]` block whose `Subtree[]` holds the parent or one of its ancestors, or whose
`ParentClassIdentifiers[]` holds the parent's class, wins, in file order; else `DefaultColumns[]`. The
shipped blocks keep the list's former defaults for the media library (`thumbnail;name;visibility;type`)
and the users tree.

```ini
[Defaults_fitness]
Subtree[]
Subtree[]=92
ParentClassIdentifiers[]
ParentClassIdentifiers[]=ng_blog
Columns[]
Columns[]=name
Columns[]=published
Columns[]=publishedage
Columns[]=pagetitle
```

On alpha this gives `name, published, publishedage, pagetitle` under "Fitness" (node 92) and under every
node below it. Blocks added by an override file come after the shipped ones, so to change the media
library's or the users' defaults, override `Columns[]` of `[Defaults_media]` / `[Defaults_users]` instead of
adding a block for the same place.

## Presets

`[Preset_<id>]` blocks in `subitems.ini` (id: `a-z`, `0-9`, `_`, `-`) are offered to everybody: `Name=`
and `Columns[]` in order. Shipped: `seo`, `editorial`, `technical`, `translations`. Users save their own
next to them ("Save current as..."), at most 20, and cannot take an INI preset's id. A preset's columns
the user may not see are left out for that user.

## INI reference

### settings/subitemscolumns.ini

| Setting | Meaning |
|---|---|
| `[Column_<key>]` | one column; the key is used in presets, defaults, saved choices, `columns=` |
| `Builtin=true` | one of the 15 columns the list renders itself; only Name, Group, Order, Description, Copy, Align, Policy[] can be changed (their defaults live in `expSubitemsBuiltinColumn::definitions()`) |
| `Class=` | a subclass of `expSubitemsColumn`, constructed with (key, settings) |
| `Handler=class::method` | a static method (node, settings, column) returning the value |
| `Template=design:...tpl` | a template; `$node`, `$column`, `$key`; output = cell, text = value |
| `Field=` | for the shipped family classes: which column of the family |
| `Name=` | the column's name, translated in the context `design/admin/node/view/full` |
| `Group=` | Basic, Node, Object, Version, Location, URLs, SEO, Dates, People, Relations, Translations, Workflow, Users, Media, Technical, Custom |
| `Type=` | text, html (trusted markup), number, date, datetime, bool, link (http(s), // or / only), code, list, image |
| `SortField=` | path, path_string, published, modified, section, depth, class_identifier, class_name, priority, name, modified_subnode, node_id, contentobject_id, visibility, or `attribute:<class>/<attribute>`; empty: not sortable |
| `Policy[]` | module/function entries the user must all have |
| `Description=` | the tooltip in Table options |
| `Copy=true` | a click on a cell copies the value |
| `Align=right` | right aligned |
| `Order=` | position in Table options and among defaults |
| `SiteAccess=` | url and SEO columns: the public siteaccess (empty: DefaultAccess) |
| `Scheme=` | `public_url`: https (default) or http when SiteURL has none |
| `TitleFormat=` | `page_title`, `title_length`: name or path |
| `Limit=` | related name lists: the most names (default 10, at most 100) |
| `Attributes[]` | main text, image and file fields, and the teaser template: identifiers tried first |
| `Length=` | the teaser template: characters |
| `WordsPerMinute=` | `readingtime`: words a minute (default 200) |

### settings/subitems.ini

| Setting | Meaning |
|---|---|
| `[SubitemsSettings] DefaultColumns[]` | the visible columns where no `[Defaults_*]` matches (`name;published;translations;priority`) |
| `AttributeColumns=` | enabled/disabled: the automatic attribute columns |
| `AttributeColumnsExcludedDataTypes[]` | datatypes never offered as attribute columns (`ezuser`) |
| `AttributeColumnsDataTypePolicy[<datatype>]` | policies an attribute column of that datatype needs (`;` separated) |
| `CSVExport=` | enabled/disabled: Export CSV and the view `content/subitemsexport` |
| `CSVLimit=` | the most rows one export writes (5000) |
| `PageSizes[]` | the page sizes offered (10, 25, 50, 100) |
| `[Preset_<id>] Name=, Columns[]` | a preset everybody can choose |
| `[Defaults_<id>] Subtree[], ParentClassIdentifiers[], Columns[]` | defaults for a place |

## The server API

Three ezjscore functions, `[ezjscServer_expsubitems]` in `extension/ezjscore/settings/ezjscore.ini`
(class `expSubitemsServerFunctions`), and one content view. Every call needs a logged-in user with
access to the admin and read access to the parent; keys are checked against the registry, and nothing
from the request is ever used as a class, function or template name. ezjscore wraps every answer as
`{ "error_text": "", "content": ... }`.

- `ezjscore/call/expsubitems::columns::<parent>`: the columns the user may see (key, name, group, type,
  sortable, align, description, copy, builtin, order), `defaults`, `presets` (`source`: ini or user),
  the saved `preference` (`visible`, `preset`, `page_size`, `saved`), `page_sizes`, `csv_export`. On
  alpha, as the admin, the media root offers 138 columns (129 from the INI, 9 attribute columns):

  ```json
  {"key":"pagetitle","name":"Page title in the browser","group":"SEO","type":"text","sortable":false,"align":"",
   "description":"The public site's title for the item's page, worked out as its pagelayout does, without rendering the page.",
   "copy":true,"builtin":false,"order":700}
  ```
- `ezjscore/call/expsubitems::rows::<parent>::<limit>::<offset>::<sort key>::<order 0|1>[::<name filter>]`
  with `columns=a,b,c` (POST or GET): the `ezjscnode::subtree` answer, every item with
  `columns: { <key>: { v: <value>, h: "<html>" } }` for the requested non-built-in keys. The sort key is a
  column key whose column sorts, or one of the old names (`published_date`, `modified_date`, `node_id`
  ...); anything else sorts as the parent does. Two children of the media root on alpha, with
  `columns=parentnodeid,urlalias,pagetitle,childrencount,version,readingtime`:

  ```json
  {"parentnodeid":{"v":43,"h":"43"},"urlalias":{"v":"media/banners","h":"<code>media/banners</code>"},
   "pagetitle":{"v":"Banners - Fit & Healthy","h":"Banners - Fit &amp; Healthy"},"childrencount":{"v":4,"h":"4"},
   "version":{"v":1,"h":"1"},"readingtime":{"v":null,"h":""}}
  {"parentnodeid":{"v":43,"h":"43"},"urlalias":{"v":"media/clients-partners","h":"<code>media/clients-partners</code>"},
   "pagetitle":{"v":"Clients & Partners - Fit & Healthy","h":"Clients &amp; Partners - Fit &amp; Healthy"},
   "childrencount":{"v":12,"h":"12"},"version":{"v":1,"h":"1"},"readingtime":{"v":null,"h":""}}
  ```
- `ezjscore/call/expsubitems::savepreference[::<parent>]`, POST `preference=<json>` (with the form token):
  `{ "visible": [...], "preset": "seo"|null, "page_size": 25, "presets": { "<id>": { "name", "columns" } } }`,
  every key optional; stored for the parent's navigation part.
- `content/subitemsexport/<parent>?columns=a,b,c&sort=<key>&order=0|1`: the CSV (below).

PHP code can use the same pieces: `expSubitemsColumnRegistry::instance()` (`availableColumns( $parent )`,
`column( $key, $parent )`, `resolveColumns( $keys, $parent )`, `defaults()`, `presets()`, `sortFor()`), and
`expSubitemsServerFunctions::columnValues( $nodes, $columns )` for the cells.

## Performance

Only the visible columns are computed, and only for the rows of the page (the page size), never for the
whole subtree. The 15 built-ins cost nothing extra: they come with the node, as before. Most other
columns read the loaded node or object and cost no query; the rest cost one or two small queries per row
and say so in their description (counts, alias rows, version rows, the data map). Columns of one row that
read the same data share it: all version columns use one version query per object, all alias columns
one alias query per node, the text, image and file columns one data map. Lookups that are the same for
every row (the parent's name, a class's groups, a section, the public siteaccess's settings, a user's
name) are done once per request. The heaviest are **Subtree size** (a count over the whole subtree) and
**Teaser** (it renders an XML text block); keep them for small pages.

Measured on alpha (SQLite, as the admin): all 114 non-built-in columns at once for 10 children of the
media root, 113 ms on the command line.

## CSV export

**Export CSV** in Table options, or `content/subitemsexport/<parent node id>?columns=a,b,c&sort=<key>&order=0|1`:
every child the user may read (up to `CSVLimit`, fetched 100 at a time), the given columns (without
`columns=`: the user's saved choice, else the defaults), in order; the header row holds the column names.
UTF-8 with BOM, comma separated, CRLF; cells starting with `=`, `+`, `-` or `@` get a leading `'` so a
spreadsheet does not run them. Built-ins are included (except the thumbnail), lists are joined with `, `,
dates written as `Y-m-d H:i:s`, bools as 1/0, sizes in bytes. The file is named after the parent.
`CSVExport=disabled` removes the button and the view.

## Where the choice is stored

The user's choice is one eZPreferences value, `admin_subitems_table`, a compact JSON document:

```json
{"v":1,"parts":{"ezcontentnavigationpart":{"visible":["name","urlalias","pagetitle"],"preset":null}},
 "page_size":25,"presets":{"mine":{"name":"Mine","columns":["name","nodeid"]}}}
```

The visible columns and the chosen preset are kept per navigation part (`*` when saved without a
parent), the page size and the own presets once. It is kept under 4000 bytes on every database (the
Oracle limit); a choice that does not fit is refused, not cut. The old rows-per-page preference
(`admin_list_limit`) is still used while no page size was saved.

## Where the code lives

| What | Where |
|---|---|
| The column base class | `kernel/classes/subitems/expsubitemscolumn.php` (`expSubitemsColumn`) |
| Registry, built-ins, Handler/Template/attribute wrappers, preference, server functions, CSV | `kernel/classes/subitems/*.php` |
| The shipped column classes | `kernel/classes/subitems/columns/*.php` (`expSubitemsFieldColumn` is the family base) |
| The column catalogue | `settings/subitemscolumns.ini` |
| Defaults, presets, CSV, attribute columns | `settings/subitems.ini` |
| The template columns | `design/standard/templates/subitems/columns/*.tpl` |
| The table and Table options | `design/<admin design>/javascript/ezajaxsubitems_expdatatable.js`, `design/<admin design>/templates/children_detailed.tpl`, `extension/expui/design/standard/javascript/exp/datatable.js` |
| The CSV view | `kernel/content/subitemsexport.php` |

## Tests

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/subitems/            # everything (96 tests)
php vendor/bin/phpunit tests/tests/kernel/classes/subitems/columns/    # the catalogue (60 tests)
```

The catalogue tests start the kernel on the admin siteaccess with the installation's own database and
check every column against real nodes: the content root, the media root, the users root, the admin user,
and nodes found by what they hold (an image, a file, tags, an XML text, relations, a rating). They check
that every block builds, names a known group, type and field, that its sort field is accepted, that every
column gives a JSON-safe value and one-line CSV text for four very different nodes, that columns that do
not apply give null, that policies decide availability, that no node ever shows an empty URL alias, and
the three worked examples above. A test that needs data the database does not have is skipped, not failed.
