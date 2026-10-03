# Behaviour changes of 1-2 October 2026 (the 6.0.15 line): what to check when you upgrade

A checklist of the changes of these two days that an operator or an extension author can notice. Each item says
what changed, how to check it and what to do. Features are described in the linked pages;
the month's story is in the [October 2026 chronicle](../../history/2026/2026-10.md).

After pulling these changes run, in this order (a PHP class, INI and template-path change all happened):

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezpgenerateautoloads.php -k --exclude='\.claude'
php bin/php/ezcache.php --clear-all --allow-root-user
```

then reload the PHP-FPM that serves the site, and restart Exponential Velocity if you use it (its workers keep
the classes they loaded at warm-up); clear the content view cache and Velocity's response cache last.

## Access and security

| Change | Check | Action |
|---|---|---|
| Poll votes (`expPoll::vote`), form submissions (`expInfoCollection::submit`) and the VAT country choice (`expVat::setUserCountry`) of the remote services need `content/read`, not "open to everyone" | an anonymous visitor who has no `content/read` can no longer vote or submit through the services | give the Anonymous role `content/read` (it has it on a public site) |
| The debug bar's settings, change log, IP test and cache list need `setup/setup` or `setup/managecache` | a visitor who sees the debug report could read every debug setting before | give the policy to the roles that debug |
| The admin dashboard, menus and top tabs show a link only to a user who can open it | editors no longer see Design, Newsletter, Export tabs, Users, Upload files, Tags, Layouts, Trash entries, "Change password" without the policy | none; check a custom role |
| `ezoe/upload` refuses a file whose type is not in `UploadFileExtensions[]` and any name with an executable extension anywhere (`shell.php.jpg`) when the user's engine is not TinyMCE 3 (`UploadExtensionCheck=engine`) | upload a `.zip` or `.pdf` with the TinyMCE 8 engine | add the type to `ezoe.ini [EditorSettings] UploadFileExtensions[]` |
| Request rules exist and `Enabled=true`, but `RuleList[]` is empty | nothing changes until you list a rule | see [request rules](../../features/6.0/request-rules.md); `php bin/php/ezrequestrules.php -s <siteaccess> --check` |

## Audit

The [audit trail](../../features/6.0/audit-trail.md) is **on by default** in every installation (the 4.x releases had
it off). Records go to `var/<site>/log/audit/`. On an existing installation create the index tables once:
`php update/common/scripts/6.0/createaudittables.php`. Run the `frequent` cronjob group.
The 4.x `eZAudit::writeAudit()` calls keep working. Only Administrator holds `audit/read` and `audit/manage`.

## Front end and designs

| Change | Action |
|---|---|
| YUI removed from the admin designs and ezjscore 1.5.0 (`ezjsc::yui2`, `ezjsc::yui3`, `ezjsc::yui3io` load nothing) | [yui-removal.md](yui-removal.md); move code to jQuery 4 / Exponential UI |
| `ezjsc::jquery` is jQuery 4.0.0 with Migrate 4.0.2, `ezjsc::jqueryUI` jQuery UI 1.14.2 | old templates that named jQuery 1.x features work through Migrate; to see what they use set `LocalScripts[jqueryMigrate]=jquery-migrate-4.0.2.js` (the reporting build) in `ezjscore.ini` |
| New admin design `admin4`, new `editor` design and siteaccess for new installations | opt in with `SiteDesign=admin4` ([admin4](../../features/6.0/admin4-design.md)); an old installation gets no editor siteaccess |
| Logos of the kernel designs show the Exponential logo; the debug output is headed "Exp Debug" | custom CSS or tests that match the old heading text must change |
| A change to a `.css` of the admin needs the template-block cache cleared (the packed `_<mtime>_all.css` link is cached in the page head); the `exp:velocity deploy --packer` option does it | `php bin/php/ezcache.php --clear-tag=template --allow-root-user` plus the content and template-block caches |

## Code structure

Every kernel command, cronjob part and module view is now a class
([specification](../../specifications/6.0/runnable-commands-cronjobs-views.md)); entry points keep their paths.
Code that read the old files for logic must look in `kernel/private/classes/`. Extensions can re-implement a
class by `site.ini [RunnableSettings] Implementation[<class>]=<subclass>`.

## Data and jobs

| Change | Check |
|---|---|
| "Empty trash" purges in batches of 100, each in its own transaction, with a one second pause | large trashes take longer in the browser but no longer time out; use `bin/php/trashpurge.php` for the largest |
| "Remove timed out sessions" also removes the shop baskets of those sessions | none |
| The `clusterpurge` cronjob part purges files expired for 30 days (it used 30 seconds) | on a clustered installation expired cache files now live their grace period |
| A copied object with an image owns its own image files; removing a draft removes all of its image files | none |
| Removing a media attribute deletes the file only when no other media row names it | none |
| Removing a subtree that holds every location of an object removes the object | none |
| Removing, copying or moving above 50 nodes offers a [content job](../../features/6.0/content-jobs.md) first | set `content.ini [ContentJobSettings] SynchronousLimit` |
| Static cache refresh on publish fetches each page once, in parallel | [static cache generator](../../features/6.0/static-cache-generator.md); `staticcache.ini [CacheSettings] FetchConcurrency` |
| SQLite transactions queue for the write lock | [sqlite-transactions.md](sqlite-transactions.md) |

## Missing libraries

A missing `vendor/` now shows a page with a repair procedure instead of PHP's raw output:
[repair from the browser](../../features/6.0/repair-from-the-browser.md), [repair.md](repair.md).
