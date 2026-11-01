# Fixes from the Opencontent and Mugo Web forks: limits, one-line logs and safer defaults

Since 6.0.15 Exponential carries the fixes that the two active forks of its predecessor, Opencontent
(`Opencontent/ezpublish-legacy`) and Mugo Web (`mugoweb/ezpublish-legacy`), made to the same kernel and that
Exponential did not have yet. Ekkehard Dörre (CJW) compared both forks with Exponential, wrote each missing fix anew
for this tree and contributed them as pull request #345. On top of that, 6.0.15 closes the items his review listed
as open: an empty `if` in the publish flow, the subtree hard limit of ezjscore, the upload blacklist, the Flash player
of the Online Editor and absolute URLs as URL aliases.

This page is for administrators who configure a site and for developers who rely on the changed classes. What you
must check when you upgrade is in the [upgrade notes](../../bc/6.0/legacy-fork-fixes.md).

## What changes for administrators

### URL aliases: at most 30 path elements are looked up

Each element of a URL is one self-join of the table `ezurlalias_ml` when the URL is translated to a node. A crawler
that requests endless paths (`/a/b/c/d/...`, often built from relative links on a misconfigured page) made the
database run joins of dozens of tables for every request. `eZURLAliasML::translate()` and `fetchByPath()` now look up
at most `site.ini [URLTranslator] MaxPathDepth` elements; a deeper URL is "not found" without a query.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[URLTranslator]
# the default; a value below 1 means 30 as well
MaxPathDepth=30
*/ ?>
```

- The path prefix of a siteaccess (`PathPrefix`) counts as an element when it is added.
- View parameters such as `/(offset)/10` are taken off the address before the lookup and do not count.
- A site with aliases nested deeper than 30 levels raises the value. The deepest node alias is the
  `path_identification_string` of `ezcontentobject_tree` with the most slashes; custom aliases are listed on the
  URL alias pages of the administration.

### Log entries stay on one line

`eZLog::write()` (every log an extension or the kernel writes with it, such as `common.log`) and
`eZLog::writeStorageLog()` (`storage.log`) replace a line break (CR LF, CR or LF) in the message by a space. A line
break in a logged value (a user name at a failed sign-in, a URL, a file name) could otherwise start a line that looks
like a separate entry, with a date and a siteaccess of the attacker's choosing. The new `eZLog::oneLine()` does the
replacement for code that writes log lines of its own. The logs of `eZDebug` (`error.log`, `warning.log`,
`notice.log`, `debug.log`) are written by other code and are not part of this change.

A message that was meant to span several lines (a stack trace written with `eZLog::write()`) now arrives as one long
line. Write such text with `eZDebug::writeError()`, or one `eZLog::write()` call per line.

### Uploads: a longer list of refused file extensions

`file.ini [FileSettings] FileExtensionBlackList[]` lists the extensions the binary file, media and image datatypes
refuse. It used to hold `php`, `php3`, `phar`, `phpt`, `pht`, `phtml` and `pgif`, the list both forks ship. It now
names every extension a web server may run as code:

| Group | Extensions |
|---|---|
| PHP, as the handlers of PHP-FPM, mod_php and the hosting panels map it | `php`, `php3`, `php4`, `php5`, `php7`, `php8`, `phps`, `phtml`, `pht`, `phar`, `phpt`, `pgif` |
| server-side includes | `shtml` |
| CGI scripts | `cgi`, `pl` |
| other platforms' pages | `asp`, `aspx`, `jsp` |
| web server configuration | `htaccess` |

The extension of the file name is compared in lower case (`shell.PHP5` is refused as `php5`), so write the values in
lower case. To refuse more, add to the list in an override; to replace it, start with an empty
`FileExtensionBlackList[]`:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[FileSettings]
# adds to the shipped list
FileExtensionBlackList[]=svgz
*/ ?>
```

The list is one layer. The web server must still never run a file below the storage directories: see
[Security hardening](../../help/security-hardening.md#file-upload-abuse).

### ezjscore: a subtree request returns at most 500 items

`ezjscore/call/ezjscnode::subtree::<node>::<limit>::<offset>` lists the children of a node for every user who may
read it, and the administration's sub-items list asks for its rows through it (and through `expsubitems::rows`).
`ezjscore.ini [ezjscServer_ezjscnode] HardLimit` shipped as `false`, so a request with a limit of one million loaded
one million nodes. The shipped value is now `500`:

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[ezjscServer_ezjscnode]
# a larger limit is cut to this one, a smaller one is kept; false or 0 switches it off
HardLimit=500
*/ ?>
```

Mugo Web ships `100`. Exponential takes `500` because the sub-items list offers pages of 10, 25, 50, 100, 200 and
500 items, and a lower limit would cut the larger pages an editor chooses. A site whose editors never use the larger
pages may set `100`.

### URL alias form: an absolute URL becomes its path

An address pasted into a URL alias form of the administration as the alias itself (`https://www.example.com/news`) was split at every
slash and stored as the nested alias `https/noname2/www-example-com/news`, with three placeholder entries.
`eZURLAliasML::storePath()` now stores the path of an absolute URL only (`news`); the scheme, the host, the port, the
query and the fragment are dropped, and the form shows the alias it stored, as it does for any alias it had to clean
up. An absolute URL without a path is stored as before, because the empty path is the root node's entry.

Opencontent refuses such a path instead (status `ACTION_INVALID`). Exponential keeps the path because the alias form
shows no message for that status: the editor would see the form come back without a word.

### The Online Editor ships no Flash player

The TinyMCE 3 media plugin bundled with ezoe carried `moxieplayer.swf`, the Flash fallback of an MP4 or FLV video.
Flash reached its end of life in 2020 and no current browser runs it. The file is removed, and the plugin's
`flash_video_player_url` defaults to `false`, so a video stays an HTML5 `<video>` element without a Flash fallback.
The media plugin is not in `ezoe.ini [EditorSettings] Plugins[]` by default.

## What changes for developers

| Where | Change | Found by |
|---|---|---|
| `ezpKernelRest` | calls `ezpEvent::getInstance()->registerEventListeners()` as the web kernel does, so `site.ini [Event] Listeners[]` run for REST requests too. The call replaces the set an earlier request registered, so a persistent worker does not register a listener twice. | Opencontent |
| `ezpContentFieldSet` | iterating the fields in a language the object has no translation in gives no fields instead of a warning and a `TypeError` | Opencontent |
| `eZPersistentObject::fetchObjectList()` | a `$limit` with `offset` and no `length` no longer reads an undefined array key | Mugo Web |
| `clusterize.php` | rows of `ezbinaryfile`, `ezmedia` and `ezimagefile` without a file name are skipped instead of storing the directory | Mugo Web |
| `index_cluster.php` | without a cluster configuration it answers with its error page instead of `Error: Undefined constant "CLUSTER_ENABLE_DEBUG"` | Mugo Web |
| ezoe `browse` | an empty folder gives a `count` of 0 instead of `count( false )` (a `TypeError` on PHP 8) | Mugo Web |
| `texttoimage` operator | a null or numeric input is taken as a string | Mugo Web |
| `eZINI::parameterSet()` | without a root directory it looks in `settings/`; it set the *file name* to `settings` before | Mugo Web |
| `eZSession` | `session_name()` is not called for a session that is already active (a warning) | Mugo Web |
| `pdf/ezxmltags/object_denied.tpl` | the PDF of an embedded object the reader may not view no longer prints the object's name, as the HTML templates never did | Mugo Web |
| `benchmarks/classes/ezbenchmarkclirunner.php` | loads again (`display()` was declared incompatibly with its parent, a fatal error) | Mugo Web |
| `eZContentOperationCollection::publishNode()` | the node found by a node assignment's remote ID is taken only when it is the object's node below the assignment's parent (see below) | Mugo Web, made safe in Exponential |
| `eZURLAliasML` | new `maxPathDepth()` and `stripURLOrigin()` | Opencontent, Exponential |
| `eZLog` | new `oneLine()`; `writeStorageLog()` writes one line | Opencontent, extended in Exponential |

### publishNode() and the remote ID of a node assignment

`publishNode()` looks for the node an assignment means first by the assignment's `parent_remote_id` (which holds the
remote ID of the node the assignment creates), then by `findNode()` (the object's node below the parent). The code
read `if ( !$existingNode );` with an empty statement, so the `findNode()` lookup always ran and always decided.

Mugo Web removed the semicolon. Exponential does more, because the remote ID alone is not enough:
`eZNodeAssignment::cloneNodeAssignment()` keeps `parent_remote_id` when it copies an assignment to another object, so
the first version of a copy carries the remote ID of the original's node. With only the semicolon removed, publishing
the copy would have taken over the original's node: no node for the copy, and the original's node rewritten. Now the
remote ID's node is taken only when its `contentobject_id` is the object and its `parent_node_id` is the assignment's
parent; in every other case `findNode()` runs as before. Where both find a node it is the same node, so the result
of a publication is unchanged; the remote ID lookup now saves the second query instead of being thrown away.

The database tests in `tests/tests/kernel/content/eZContentOperationCollectionPublishNodeTest.php` publish a new
object, a new version, a new location and an assignment that carries another object's node remote ID, and check the
nodes after each. The search engine (`ezsearchengine.php`) had the same empty `if` in its handling of `www.`
addresses; it is fixed too, and a test walks `kernel/` and `lib/` for any `if` or `elseif` whose body is an empty
statement.

## Looked at and not adopted

- **Skipping a re-parenting `UPDATE` of `ezurlalias_ml`** when the new element is the old parent: the statement then
  changes no row. Skipping it saves one statement in a rare case and fixes no defect.
- **A `trim()` of the CSS packer's output**: leading and trailing white space does not change how a style sheet
  applies, and the packed files are cached by name.

## Proposals, not in 6.0.15

These features of the forks are useful but optional. They are recorded here so that they can be taken up later:

- **INI values from environment variables** (Opencontent, `EZINI_...`): a setting such as the database password read
  from the environment instead of `settings/override/`. It would change how every INI file is read and needs a
  decision on precedence over overrides and on caching.
- **`i18n.ini` `OverrideDefault` for `eng-GB`** (Opencontent): lets a site replace the built-in English texts with a
  translation file of its own.
- **A flag for Greek** (`share/icons/flags/ell-GR.gif`): the locale `ell-GR` exists, the flag does not. The flags are
  one 18 by 12 pixel GIF set; a Greek flag should come from the same set, not be drawn anew.
- **The locales `moc-IT` and `lad-IT`** (Opencontent): two regional languages of Italy.

## Credits

Reported and contributed by Ekkehard Dörre (CJW) in pull request #345 of `se7enxweb/exponential`. The fixes were found
by Opencontent and Mugo Web, who maintain the two active forks of the kernel that Exponential grew from; each one is
written anew for Exponential, and the table above names the fork it comes from.

## Related pages

- [Upgrade notes: fixes from the Opencontent and Mugo Web forks](../../bc/6.0/legacy-fork-fixes.md)
- [Security hardening](../../help/security-hardening.md)
- [site.ini [URLTranslator]](../../examples/ini-settings/site/URLTranslator.md),
  [file.ini [FileSettings]](../../examples/ini-settings/file/FileSettings.md),
  [ezjscore.ini [ezjscServer_ezjscnode]](../../examples/ini-settings/ezjscore-ezjscore/ezjscServer_ezjscnode.md)
- [The 6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)
