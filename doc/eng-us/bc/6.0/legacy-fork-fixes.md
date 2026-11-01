# Fixes from the Opencontent and Mugo Web forks: what to check when you upgrade (6.0.15)

6.0.15 takes over fixes of the Opencontent and Mugo Web forks (pull request #345, contributed by Ekkehard Dörre of
CJW) and closes the items that review left open. Most of them only remove warnings and errors. Five change what a
site does; this page lists them with what to check and how to keep the earlier behavior. The features page explains
each in full: [Fixes from the Opencontent and Mugo Web forks](../../features/6.0/legacy-fork-fixes.md).

## URL aliases deeper than 30 path elements are not found

`eZURLAliasML::translate()` and `fetchByPath()` look up at most `site.ini [URLTranslator] MaxPathDepth` path
elements (new, default `30`). A URL with more elements answers "not found" without a database query; before, it
ran a query with one join of `ezurlalias_ml` per element.

- **Who is affected:** a site with content nested more than 30 levels deep (the path prefix of a siteaccess counts as
  one level), or with custom aliases of more than 30 elements. View parameters (`/(offset)/10`) do not count.
- **What to do:** nothing on almost every site. Otherwise set a higher value in
  `settings/override/site.ini.append.php`:

  ```ini
  [URLTranslator]
  MaxPathDepth=40
  ```

  A value below 1 means 30.

## Every eZLog entry is one line

`eZLog::write()` and `eZLog::writeStorageLog()` replace each line break of the message with a space, so a logged
value cannot forge further entries.

- **Who is affected:** code that writes a message meant to span several lines with `eZLog::write()` (a stack trace,
  a dump), and scripts that parse such a log and expect those lines.
- **What to do:** write multi-line text with one `eZLog::write()` call per line, or with `eZDebug::writeError()`.
  There is no setting to switch it off.

## More upload extensions are refused

`file.ini [FileSettings] FileExtensionBlackList[]` adds `php4`, `php5`, `php7`, `php8`, `phps`, `shtml`, `cgi`, `pl`,
`asp`, `aspx`, `jsp` and `htaccess` to `php`, `php3`, `phar`, `phpt`, `pht`, `phtml` and `pgif`. The binary file,
media and image datatypes refuse an upload with one of these extensions ("A valid file is required. The following
file extensions are blacklisted: ...").

- **Who is affected:** a site whose editors upload Perl scripts (`.pl`) or other listed files as downloads.
- **What to do:** pack such files in an archive (`.zip`), or replace the list in
  `settings/override/file.ini.append.php` with an empty `FileExtensionBlackList[]` first and the extensions you
  want refused after it. A site that replaces the list already keeps its own; compare it with the new one.
- Files stored before the upgrade stay where they are.

## ezjscnode::subtree returns at most 500 items

`ezjscore.ini [ezjscServer_ezjscnode] HardLimit` is `500` instead of `false`. A request to
`ezjscore/call/ezjscnode::subtree` or `expsubitems::rows` that asks for more than 500 items gets 500.

- **Who is affected:** a script or front end of your own that fetches more than 500 children of a node in one
  request. The administration's sub-items list asks for 500 at most.
- **What to do:** page through the children with the offset argument, or set `HardLimit=false` (or a higher number)
  in `settings/override/ezjscore.ini.append.php`.

## An absolute URL given as an alias is stored as its path

`eZURLAliasML::storePath()` stores `https://www.example.com/news` as `news` instead of the nested placeholder
aliases `https/noname2/www-example-com/news`.

- **Who is affected:** only the way a pasted address is stored. Aliases created before the upgrade, including such
  nested ones, stay as they are; remove unwanted ones on the URL alias pages of the administration.
- **What to do:** nothing.

## Unchanged, but worth knowing

- **publishNode():** the empty `if ( !$existingNode );` in `eZContentOperationCollection::publishNode()` is fixed so
  that the node found by an assignment's remote ID is used only when it is the object's node below the assignment's
  parent; otherwise `findNode()` decides, as it always did. A publication gives the same nodes as before. The fix of
  Mugo Web, removing only the semicolon, would have let the first publication of a copied object take over the
  original's node, because a copied assignment keeps the original's remote ID.
- **REST listeners:** `site.ini [Event] Listeners[]` now also run for requests to the REST API. A listener that should
  not act on REST requests checks the request itself, for example its URI.
- **The Flash player of the Online Editor:** `moxieplayer.swf` is removed. A site that enabled the TinyMCE media plugin
  and relied on its Flash fallback for MP4 or FLV videos names a player of its own in `flash_video_player_url`; every
  current browser plays MP4 in the `<video>` element the plugin writes.

## Code and tests

- `tests/tests/kernel/classes/expLegacyForkFixesTest.php` (DB-less): each fix and each follow-up, each failing without
  its change.
- `tests/tests/kernel/content/eZContentOperationCollectionPublishNodeTest.php` (database): the publish flow for a new
  object, a new version, a new location and a copied assignment.
