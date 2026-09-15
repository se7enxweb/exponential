# RSS import cleanup

An RSS import adds an object for every item in the feed and never takes one
away. A site importing a busy feed therefore grows without limit: the
destination folder reaches tens of thousands of children, the administration
interface slows to a crawl on it, the search index fills with items nobody will
read again, and the nightly backup carries all of it.

Trimming that is a job every site importing a feed eventually has to do, which
is why this is in the kernel rather than left to each site to arrange.

Ported from the **bccleanuprss** extension by Brookins Consulting
(<https://github.com/se7enxweb/bccleanuprss>), GPL v2 or later. The original's
method is kept — see [What was kept, and what changed](#what-was-kept-and-what-changed).

---

## In one line

Keep the newest *N* items of each active RSS import; remove the rest.

---

## It does nothing until you tell it what to remove

This removes content. Three separate conditions must hold before it will touch
anything, and none of them holds on an installation that has not been set up
for it:

1. `Enabled=true`
2. `ClassIdentifiers[]` names at least one class
3. `KeepPerFeed` is at least 1

Any one of them unmet and the run stops and says which:

```
$ php bin/php/cleanuprss.php
Nothing was removed: RSSImportCleanupSettings/Enabled is not true in content.ini.
```

That is not an error — an installation that has not asked for this is the
normal case. Both the script and the cronjob exit cleanly, so the part can be
scheduled before it is configured.

---

## Settings

`settings/content.ini`, beside the import's own settings. Override per
siteaccess like anything else.

```ini
[RSSImportCleanupSettings]
Enabled=false
KeepPerFeed=100
ClassIdentifiers[]
CleanupUser=admin
LogFile=rsscleanup.log
MoveToTrash=true
```

| Setting | Meaning |
|---|---|
| `Enabled` | Off until the site says otherwise. |
| `KeepPerFeed` | How many items to keep per feed, newest first. At least 1. |
| `ClassIdentifiers[]` | The classes that may be removed. **Nothing outside this list is touched however old it is** — a folder or a page that happens to sit in the destination is safe. Empty means nothing is removed. |
| `CleanupUser` | Removing is permission checked, so the run becomes this user. A missing account stops the run with a message. |
| `LogFile` | Where the run is recorded, under `var/<var dir>/log/`. Empty writes no file. |
| `MoveToTrash` | Whether removed items go to the trash, where they can be recovered, or are deleted outright. |

A working configuration:

```ini
[RSSImportCleanupSettings]
Enabled=true
KeepPerFeed=200
ClassIdentifiers[]
ClassIdentifiers[]=article
MoveToTrash=true
```

### About `MoveToTrash`

`true` is the default because an automated, scheduled, permanent deletion of
content with no way back is not a reasonable default. Note that the trash then
grows instead — pair it with the `trashpurge.php` cronjob, or set it to `false`
once you trust the class list.

---

## Running it

### By hand

```
php bin/php/cleanuprss.php --dry-run
php bin/php/cleanuprss.php
php bin/php/cleanuprss.php --keep=50
php bin/php/cleanuprss.php -s my_siteaccess
```

| Option | |
|---|---|
| `--dry-run` | List what would be removed, and remove nothing. |
| `--keep=N` | Keep this many per feed instead of the configured number. Does not lift the other two conditions. |

Plus the ordinary script options (`-s`, `-q`, `-d`, `--help`).

**Always dry-run first on a site you have not run this on before.** It prints
every item it would remove, by name and node id.

### As a cronjob

```
php runcronjobs.php cleanuprss
```

The part is declared in `settings/cronjob.ini`:

```ini
[CronjobPart-cleanuprss]
Scripts[]=cleanuprss.php
```

It is a named part rather than one of the default `Scripts[]`, so it runs only
when asked for. Nightly is usually right — it is the import that needs
throttling, not the clock.

---

## What it actually does

For each **active** import (`eZRSSImport::fetchActiveList()`):

1. Take the import's `destination_node_id`. Skip the feed if it has none, or if
   the node is gone — that is reported, not fatal.
2. Ask for that node's children, of the configured classes, published status,
   depth 1, **sorted newest first and offset by `KeepPerFeed`**.
3. Whatever comes back is by definition the surplus; remove it in one call.

Step 2 is the whole trick, and it is the original's. Because the keep count is
the query's offset, there is no second pass deciding what to drop and no window
in which the newest items could be returned — the items to keep are skipped by
the database.

`Depth => 1` means only direct children. An item's own children go with it when
it is removed, but a sub-folder of the destination is not searched.

---

## From a template or PHP

```php
$cleanup = new expCleanupRSS( array( 'dry-run' => true ) );

if ( $cleanup->isEnabled() )
{
    $cleanup->cleanup();
    $counts = $cleanup->counts();   // feeds, examined, removed
    $perFeed = $cleanup->perFeed(); // name, node, surplus
}
else
{
    echo $cleanup->reason();
}
```

Constructor options: `dry-run`, `keep`, `quiet`.

---

## What was kept, and what changed

The selection method — newest-first sorted, offset by the keep count — is the
original's and is why this port exists rather than a rewrite.

Everything around it changed, and most of the changes were not cosmetic:

| | Was | Now |
|---|---|---|
| **Constructor** | `public function BCCleanupRSS()`, a PHP 4 constructor | `__construct()` |
| **Default class list** | `RSSClasses[]=16`, `RSSClasses[]=1` — numeric ids, which on a stock installation are *article* and *folder* | empty, and identifiers rather than ids |
| **Enabled by default** | yes | no |
| **Removal** | `removeSubtrees( ..., false )` — permanent, not configurable | `MoveToTrash`, defaulting to the trash |
| **Dry run** | none | `--dry-run` |
| **Missing cleanup user** | fatal error on the next line | stops the run and says so |
| **Removal calls** | one `removeSubtrees()` per item, each clearing caches and reindexing | one call per feed |
| **Terminal output** | `global $cli`, set only if the caller made one | `eZCLI::instance()` |
| **Settings** | `bccleanuprss.ini` | `content.ini [RSSImportCleanupSettings]`, next to `[RSSImportSettings]` |

The PHP 4 constructor is worth spelling out: **PHP 8 removed them**, so
`new BCCleanupRSS()` no longer ran that method. Every property it set —
`rssLimit`, `rssClasses`, `logFile` — stayed undefined, and the class could not
work at all on any supported PHP version.

The default class list is worth spelling out too. `RSSClasses[]=1` is *folder*
on a stock installation. Enabled by default, against a destination containing
folders, that removes folders.

### Naming

`bc`-prefixed names are gone, as this is no longer a vendor extension:
`BCCleanupRSS` → `expCleanupRSS`, `bccleanuprss.ini` → a block in `content.ini`.
Brookins Consulting's copyright lines are kept in every ported file — the
branding goes, the attribution does not.

---

## Files

| File | |
|---|---|
| `kernel/classes/expcleanuprss.php` | `expCleanupRSS` |
| `bin/php/cleanuprss.php` | the command line script |
| `cronjobs/cleanuprss.php` | the cronjob part |
| `settings/content.ini` | `[RSSImportCleanupSettings]` |
| `settings/cronjob.ini` | `[CronjobPart-cleanuprss]` |
| `autoload/ezp_kernel.php` | the class map entry |

---

## Tests

```
php ai/bin/one/test_cleanuprss.php
```

Builds its own feed rather than touching a real one: a folder, an active
`eZRSSImport` pointing at it, twelve articles published an hour apart, and one
folder among them that must survive because its class is not on the list. Then
it checks that each of the three conditions refuses on its own, that a dry run
finds the surplus and removes nothing, that a real run leaves exactly the
newest five and that they are the right five, that the unnamed class is
untouched, that a second run finds nothing, and that what was removed is
recoverable from the trash. It tears the fixture down afterwards.

15 assertions, all passing.
