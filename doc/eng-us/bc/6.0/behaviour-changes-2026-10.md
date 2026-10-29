# Behaviour changes of 1 and 2 October 2026 (6.0.15 line): what to check when you upgrade

Read this page before you update an installation to the changes of 1 and 2 October 2026. It is a checklist for
operators and extension authors: each item says what changed, how to check it and what to do. The features are
described on the linked pages; the story of the month is in the [October 2026 chronicle](../../history/2026/2026-10.md).

## Deploy order

PHP classes, INI files and template paths all changed. After pulling the changes, run these in order:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezpgenerateautoloads.php -k
php bin/php/ezcache.php --clear-all --allow-root-user
```

`-k` walks the whole installation. If you keep working copies of the installation inside it, list them in
`.autoloadignore` or pass `--exclude=<dir>`, or every kernel class is mapped into the copy.

Then:

1. Reload the PHP-FPM that serves the site.
2. Restart Exponential Velocity if you use it. Its workers keep the classes they loaded at warm-up.
3. Clear the content view cache and Velocity's response cache last.

## Access and security

| Change | How to check | What to do |
|---|---|---|
| Poll votes (`expPoll::vote`), form submissions (`expInfoCollection::submit`) and the VAT country choice (`expVat::setUserCountry`) of the remote services need `content/read`; they were open to everyone. | An anonymous visitor without `content/read` can no longer vote or submit through the services. | Give the Anonymous role `content/read` (a public site already has it). |
| A read of a user's roles, of the policies of a role, of the limitations of a policy or of their values that fails (the database answers with an error) denies what it could not read: the role grants nothing, the policy is left out. Before, a policy whose limitations could not be read was unlimited (`*`). Such an access array is used for that request only and not kept in the user cache; one error per build is logged (`eZRole::accessArrayByUserID`). | `eZAccessArrayFailClosedTest`; with a healthy database nothing changes. | Nothing. An extension that reads `eZPolicy::limitationList()` sees a policy whose limitations could not be read as disabled (`Disabled`) and a limitation whose values could not be read with `ReadFailed` set. |
| The debug bar's settings, change log, IP test and cache list need `setup/setup` or `setup/managecache`. | Before, a visitor who saw the debug report could read every debug setting. | Give the policy to the roles that debug. |
| The admin dashboard, menus and top tabs show a link only to a user who can open it. | Editors no longer see Design, Newsletter, Export tabs, Users, Upload files, Tags, Layouts, Trash entries or "Change password" without the policy. | Nothing; check your custom roles. |
| Uploaded images are checked by their content (added 12 October 2026). The image datatype (edit form, `content/upload`, the rich text editor's upload dialog and its "From a URL" tab, the AJAX uploader, WebDAV, imports) and the package wizard's thumbnails and style images read the type with PHP's fileinfo and accept only a type on `image.ini [ImageUploadSettings] AllowedMIMETypes[]` (JPEG, PNG, GIF, WebP, AVIF, TIFF, BMP). A refused file is not stored, and `eZContentUpload` refuses it before any object is created. On for every site, upgrades included. | Upload a text file renamed `.jpg`, or a HEIC or SVG image: "A valid image file is required." or "Images of the type %1 are not accepted." Without fileinfo (`php -m \| grep fileinfo`), getimagesize() decides and one warning per process says so. | Add a type the site needs to `AllowedMIMETypes[]` in an override (from 15 October 2026 SVG is on the list and stored sanitized, see [SVG images](svg-images.md)); `ValidateMIMEType=disabled` keeps only the older check. API: `expImageUploadValidator::validate( $path )`. |
| `ezoe/upload` refuses a file whose type is not in `UploadFileExtensions[]`, and any name with an executable extension anywhere (`shell.php.jpg`), when the user's editor engine is not TinyMCE 3 (`UploadExtensionCheck=engine`). | Upload a `.zip` or `.pdf` with the TinyMCE 8 engine. | Add the type to `ezoe.ini [EditorSettings] UploadFileExtensions[]`. |
| Request rules exist and `Enabled=true`, but `RuleList[]` is empty. | Nothing changes until you list a rule. | See [request rules](../../features/6.0/request-rules.md); check with `php bin/php/ezrequestrules.php -s <siteaccess> --check`. |
| `content/history` opens for who may edit the object, not only for who may read it; its policy functions are `read or edit` (they were `read` and `edit`). A reader still needs an edit policy for some content, as before. Added 6 October 2026. | A role with an edit policy and no read policy for some content now lists the versions of that content (number, status, translation, creator, dates). | Nothing, unless such a role must not see versions; then limit its edit policy as its read policy. See [the versions of a draft](draft-edit-access.md). |
| The history compares, copies and links to the version view only for versions whose content the user may see: published, archived and own versions, a rejected version for its editors, and someone else's draft or pending version only with `content/versionread`. Removing takes only versions of the object in the statuses the page offers. Added 6 October 2026. | A reader with `content/diff` and without `content/versionread` no longer compares other users' drafts; a crafted request to remove a pending version is refused with a message. | Give `content/versionread` to roles that must compare or copy other users' drafts. A template override of `content/history.tpl` should use `$content_versions` (see [the versions of a draft](draft-edit-access.md)); the view refuses either way. |
| A content policy limitation the kernel does not know, without a handler (`site.ini [RoleSettings] LimitationHandlers[]`), gives its policy no access in list and tree fetches too; fetches ignored it and listed objects whose pages `checkAccess()` refused. Added 6 October 2026. | Look in **Users > Roles and policies** (or the table `ezpolicy_limitation`) for limitations other than Class, Section, Owner, Group, Node, Subtree, Language, Status and StateGroup_. | Remove them, or install the extension that handles them. See [content policy limitations of extensions](../../features/6.0/content-limitation-handlers.md). |
| `versionread` and `versionremove` stop at the first limitation of a policy that denies (a `Language` that does not match, a limitation the kernel does not know); the next limitation could allow the policy again. Subtree notifications check the `Group` limitation of `content/read`. Added 6 October 2026. | A role with such a policy reads fewer versions; a subscriber outside the owner's groups gets no mail through a "Self group" policy. | Nothing, unless a role relied on it. |
| The role editor and the policy editor store only the values they offered for a limitation; a value made up in the request is left out and logged. A content limitation handler that throws denies instead of ending the request, and a string condition from `permissionSQL()` that is not self-contained (unbalanced quotes or parentheses, a `;` or a comment outside quoted strings) gives its policy no access in fetches. `eZUser::accessUser()` gives no user for a disabled account. Added 6 October 2026. | A handler whose SQL closed its own parentheses, or whose exceptions were relied on to stop a request, now denies. Checks for another user refuse a disabled account. | Return a self-contained condition or the column form (`array( 'column' => ..., 'values' => ... )`); check a user with `./console exp:access:check`. See [content policy limitations of extensions](../../features/6.0/content-limitation-handlers.md). |
| `role/view` lists the users and groups of a role a page at a time (`site.ini [RoleSettings] AssignmentsPerPage`, default 50). `$user_array` holds one page; every assignment is `$assignment_total`, those the name filter keeps `$assignment_count`. `$user_array` entries carry `user_name` and a `null` `user_object` for a gone object. Added 6 October 2026. | A design that overrides `role/view.tpl` shows only the first 50 assignments and counts `$user_array`. | Add a pager on `(assignment_offset)` as `design/admin/templates/role/view.tpl` does ([role assignment paging](../../features/6.0/role-assignment-paging.md)), or set `AssignmentsPerPage=0` to list all. |
| New policy function `content/publish_without_notification` and `notification.ini [NotificationSettings] PublishWithoutNotification` (`disabled`). The publish operation takes `notify` (default `true`); the filter `content/notification/create` can get `false`. With asynchronous publishing, a publication without notification is published at once, not queued. | Nothing changes until the setting is enabled. A listener of `content/notification/create` that returns `true` whatever it gets overrides the editor. A role with `content/*` includes the new function. | Enable the setting where wanted and grant the function; pass on `$create` in listeners. See [Publish without notification](../../features/6.0/publish-without-notification.md). |
| A user who may edit roles can no longer give more than they have (`site.ini [RoleSettings] PreventPrivilegeEscalation=enabled`, new). The role editor, the policy wizard, the policy editor, `role/copy` and `role/assign` refuse a policy, a copy or an assignment that the editor's own access does not cover, with a message naming the policies. Users with every function of every module are not affected. Added 6 October. | Sign in as a user whose roles include the `role` module but not full access, and add a policy they do not hold: the editor says "The policy was not added". | Give role editors the access they are meant to hand out, or let administrators with full access do it. `PreventPrivilegeEscalation=disabled` gives back the old behaviour. See [roles and policies](../../guides/roles-and-policies.md#who-may-do-this). |
| Activating an account (`user/activate`, and Activate on `user/unactivated`) no longer restarts the registration when none is pending: such an account was disabled again and mailed a new activation link. A pending registration resumes as before. Activate on `user/unactivated` sends the "registration approved" mail again, as the user's own link does (`site.ini [UserSettings] ActivationByAdministratorSendsApprovalMail=enabled`, new). The publish step of an activation compares the object's version as a number: on SQLite it was cancelled, so no activation published the user object or sent the approval mail. Added 6 October. | Activate a registration by hand: the user gets the approval mail (unless `EmailRegistrationInfo=disabled`). | Set `ActivationByAdministratorSendsApprovalMail=disabled` to activate by hand without mail. |
| A user without any `content/read` policy (access word `no`), which is also what a user gets when every read of the access array fails, is listed no node by list, tree, count and calendar fetches, search, trash, the keyword lists and the child counts of the admin: `eZContentObjectTreeNode::createPermissionCheckingSQL( false )` gives `0 = 1`. Before it gave no condition, and such a user was listed every node (whose pages `checkAccess()` then refused). Full access (`yes`, an empty list) still gives no condition. Added 6 October 2026. | On the demonstration server three users in a group without any role were listed all 332 nodes and are now listed none; every other user, anonymous included, gets the same lists as before. | Give `content/read` to the roles of users who must see lists. An extension that calls `createPermissionCheckingSQL( false )` to mean "no limitation" must pass an empty array. |
| The AJAX uploader's location form carries a one-time token for the stored file instead of its path (added 14 October 2026). The token is bound to the user and the upload target and lives an hour; a refused location keeps it, so another one can be chosen. | Upload through a relation attribute's "Upload" dialog and choose a location. | An override of `ajaxuploader/location.tpl` must post `UploadToken` (`{$upload_token\|wash()}`) instead of `UploadFile` and `UploadOriginalFilename`. |
| Setup > Extensions downloads a package through a button of its form; the address `/setup/extensions/<name>/<format>` shows the list and no longer downloads (added 14 October 2026). | Press a download button under **Details**. | Bookmarks and scripts that fetched the address: use the button, or `php ezpm.php` on the command line. |
| No package takes an extension that holds a secret value or key material: Setup > Extensions, the package wizard and `ezpm.php` refuse it and name the files and settings (added 14 October 2026). Packages made before keep what they hold. | Download an extension whose settings hold a password. | Remove the secret from the extension (it belongs in `settings/override`), or, for a known example, list it in the extension's `package_check.ini` ([the secret check](extension-package-secret-check.md)). |

## Audit trail

The [audit trail](../../features/6.0/audit-trail.md) is **on by default** in every installation (4.x had it off).

- Records go to `var/<site>/log/audit/`.
- On an existing installation, create the index tables once:
  `php update/common/scripts/6.0/createaudittables.php`
- Run the `frequent` cronjob group.
- Old `eZAudit::writeAudit()` calls keep working.
- Only the Administrator role holds `audit/read` and `audit/manage`.

Details: [Audit trail upgrade](audit.md).

## Front end and designs

| Change | What to do |
|---|---|
| YUI is removed from the admin designs and from ezjscore 1.5.0 (`ezjsc::yui2`, `ezjsc::yui3` and `ezjsc::yui3io` load nothing). | Read [YUI removal](yui-removal.md); move the code to jQuery 4 or Exponential UI. |
| `ezjsc::jquery` is jQuery 4.0.0 with Migrate 4.0.2; `ezjsc::jqueryUI` is jQuery UI 1.14.2. | Old templates that use jQuery 1.x features work through Migrate. To see what they use, set `LocalScripts[jqueryMigrate]=jquery-migrate-4.0.2.js` (the reporting build) in `ezjscore.ini`. |
| New admin design `admin4`; new `editor` design and siteaccess for new installations. | Opt in with `SiteDesign=admin4` ([admin4](../../features/6.0/admin4-design.md)). An existing installation gets no editor siteaccess. |
| A signed-in user with the policy `uiswitch/design` can choose another administration design (admin4l, admin4, AdminUI, the classic design, or an extension design such as `admin_classic`) for their own session, from the right toolbar tool `admin_ui_switch` (added 15 October 2026). The design-base cache file name of the current siteaccess includes the UI switch variant key while a choice is applied; unchanged otherwise. | Grant `uiswitch/design` to the roles that may switch. Make sure every offered design renders `admin_ui_switch`; a siteaccess whose own design is the classic one needs `Tool[]=admin_ui_switch` in `[Toolbar_admin_right]`. Remove the kernel overrides of `sevenx_themes_admin_classic` before 0.2.5. See [the design switch](ui-switch-design.md) and [its guide](../../guides/ui-switch-design.md). |
| The kernel designs show the Exponential logo; the debug output is headed "Exp Debug". | Change custom CSS or tests that match the old heading text. |
| Removing RSS exports or imports (`rss/list`) and workflow groups (`workflow/grouplist`) shows a confirmation first; only a second post with `ConfirmRemoveButton` and the same ids removes. Removing a workflow group keeps a workflow that is also in another group. | A script or an overridden `rss/list.tpl` or `workflow/grouplist.tpl` that posts `RemoveExportButton`, `RemoveImportButton` or `DeleteGroupButton` now lands on the confirmation (`rss/confirmremove.tpl`, `workflow/confirmremovegroup.tpl`); add `ConfirmRemoveButton=1` to remove at once. See [RSS feeds](../../guides/rss-feeds.md) and [Workflows](../../guides/workflows.md#12-the-workflow-group-list-page). |
| A change to an admin `.css` file needs the template-block cache cleared: the packed `_<mtime>_all.css` link is cached in the page head. `exp:velocity deploy --packer` does it. | `php bin/php/ezcache.php --clear-tag=template --allow-root-user`, plus the content and template-block caches. |

## Code structure

Every kernel command, cronjob part and module view is now a class
([specification](../../specifications/6.0/runnable-commands-cronjobs-views.md)). Entry points keep their paths.

- Code that read the old files for their logic must look in `kernel/private/classes/`.
- An extension can replace a class with its own subclass:
  `site.ini [RunnableSettings] Implementation[<class>]=<subclass>`.
- `checkAccess()` of `eZContentObject`, `eZContentObjectTreeNode` and `eZContentObjectVersion` takes the user to check
  for as a sixth argument, `$userID = false` (added 6 October 2026). A class that overrides one of them must accept it,
  or PHP 8 stops with a fatal error when the class is loaded.

## Data and jobs

| Change | What to check or do |
|---|---|
| The trash rows record who moved an object to the trash and from where, in the new columns `ezcontentobject_trash.trashed_by` and `trashed_via`; `<VarDir>/trash/trashed.json` is no longer written once they exist. Added 9 October 2026. | Run the database update of your engine (`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`); **Setup > System upgrade** lists the missing columns. Until it has run the kernel works with the old table and keeps writing the file, so code and update can come in either order. Then copy what the file recorded with `php update/common/scripts/6.0/movetrashrecords.php --remove-file` (on each web server of a cluster). See [the trash view](trash.md#where-trashed-by-is-kept). |
| "Empty trash" purges in batches of 100, each in its own transaction, with a one second pause. | Large trashes take longer in the browser but no longer time out. Use `bin/php/trashpurge.php` for the largest. |
| "Remove timed out sessions" also removes the shop baskets of those sessions. | Nothing. |
| The `clusterpurge` cronjob part purges files that expired 30 days ago (it used 30 seconds). | On a clustered installation, expired cache files now live their grace period. |
| A copied object with an image owns its own image files; removing a draft removes all of its image files. | Nothing. |
| Removing a media attribute deletes the file only when no other media row names it. | Nothing. |
| Removing a subtree that holds every location of an object removes the object. | Nothing. |
| Removing, copying or moving more than 50 nodes offers a [content job](../../features/6.0/content-jobs.md) first. | Tune `content.ini [ContentJobSettings] SynchronousLimit` (default `50`). |
| The static cache refresh on publish fetches each page once, in parallel. | See the [static cache generator](../../features/6.0/static-cache-generator.md); tune `staticcache.ini [CacheSettings] FetchConcurrency` (default `8`, not listed in the shipped file). |
| `runcronjobs.php` leaves out a part paused at **Setup > Cronjobs**, including one that an extension left paused (`<cache>/cronjob_control/cronpart_deactivated_<part>.txt` holding `1`). Added 15 October 2026. | After the update, look at **Setup > Cronjobs** and resume what should run; or set `cronjob.ini [PauseSettings] Pause=disabled`. See [pausing cronjob parts](../../features/6.0/cronjob-pause.md). |
| A part paused while it runs finishes the script that runs and `runcronjobs.php` leaves out the scripts after it; every run a pause leaves out is written to `<log directory>/cronjob_skipped.log`; pauses and resumes are audited (`system.cronjob.pause`, `system.cronjob.resume`). Added 15 October 2026. | Read `cronjob_skipped.log` after a release to see what a pause held back. On several servers, set `[PauseSettings] Directory` to a shared directory, see [pausing cronjob parts](../../features/6.0/cronjob-pause.md). |
| **Setup > Cronjobs** lists the parts in a compact table by default; **Detailed** shows the cards. The switch writes the user preference `admin_cronjobs_view` by a POST. Added 15 October 2026. | Choose **Detailed** once to keep the cards. |
| SQLite transactions queue for the write lock. | See [SQLite transactions](sqlite-transactions.md). |

## Missing libraries

A missing `vendor/` directory now shows a page with a repair procedure instead of PHP's raw error output. See
[repair from the browser](../../features/6.0/repair-from-the-browser.md) and [the repair page upgrade](repair.md).

Since 9 October 2026, `eZSOAPClient` refuses a call over HTTPS when the PHP extension curl is not loaded: `send()`
returns `0`, `ErrorString` says that HTTPS needs the PHP extension curl, and the error log has
"No SOAP call to <server>: HTTPS needs the PHP extension curl, which is not loaded". Before, such a call went as plain
text to the TLS port, which no HTTPS server answers with a SOAP response. Check with `php -m | grep curl` (and in the
PHP of the web server) that curl is loaded wherever SOAP calls over HTTPS are made. Calls over HTTP are unchanged.
See [SOAP calls over HTTPS](../../features/6.0/soap-client-tls.md).

## Composer requirements, image editor and tests (24 and 25 October 2026)

| Change | How to check | What to do |
|---|---|---|
| `composer.json` no longer requires `zetacomponents/authentication-database-tiein`, `zetacomponents/mvc-authentication-tiein` (both `require`) and `zetacomponents/php-generator` (`require-dev`). No code of the kernel or of the packages it requires uses them. `zetacomponents/configuration` stays required while 7x SQLI Import 2.0.x throws one of its exceptions; `se7enxweb/ezie` and `zetacomponents/image-conversion` stay required as well. | `grep -rE 'ezc(AuthenticationDatabase\|MvcAuthentication\|PhpGenerator)' extension/ <your code>` | Code of your own that uses one of the three requires it in your project's `composer.json` (`composer require zetacomponents/php-generator`); the next `composer update` removes it otherwise. |
| The setup wizard and the kickstarter activate `expimageeditor` for a new installation where they activated `ezie`. Existing installations keep their `ActiveExtensions`; ezie stays installed. | `ActiveExtensions[]` of a new installation's `settings/override/site.ini.append.php`. | Nothing. To switch an existing site to expimageeditor: `ActiveExtensions[]=expimageeditor` in place of `ezie`, then `php bin/php/addeditorpolicies.php --apply --only=expimageeditor/edit` ([expimageeditor](../../features/6.0/extensions/expimageeditor.md)). |
| The content job log writes its numbers with a dot whatever the locale `config.php` sets ("in 0.01 s, peak 30.0 MB"; it was "0,01" under a German locale). | The log of a job at **Setup > Content jobs**. | A script that read the comma form reads the dot. |
| With the MySQLi cluster backend (`eZDFSFileHandlerMySQLiBackend`) on PHP 8.1 and later, a cache file another process is generating is waited for again instead of generated a second time: `startCacheGeneration()` returns the remaining generation time and a timed-out generation is taken over. A failed `SELECT ... FOR UPDATE` at the end of a generation rolls back and reports failure instead of a `TypeError`. | Clustered sites under load: fewer duplicate generations of the same view cache. | Nothing. |
| The tests of the group `database` run on throwaway MySQL/MariaDB databases `exp_test_<pid>_<hex>` and the live tests use `EXP_TEST_SITEACCESS` or `DefaultAccess` (they used `admin`). `tests/runtests.php --db-per-test --dsn=…` empties only a database with `test`, `tests` or `testdb` as a word of its name. | `vendor/bin/phpunit --group database` without an account with `CREATE` and `DROP` on `exp\_test\_%` skips with the reason. | For the test runs: `EXP_TEST_DB_USER` and `EXP_TEST_DB_PASSWORD`, see [TESTING.md](love/TESTING.md). |

## Upgrade commands (28 October 2026)

| Change | How to check | What to do |
|---|---|---|
| `bin/php/addeditorpolicies.php` and `bin/php/addmembermanagement.php` only show what they would change unless `--apply` is given; they wrote by default and took `--dry-run`. `--dry-run` is still accepted and means the same as no flag; `--apply` together with `--dry-run` is a usage error (exit 2). The setup step of a new installation is not affected: it calls the classes, not the commands. | Run either command without options: it lists what it would change and ends with `PASS: ... (dry run, nothing changed; --apply writes)`. | Add `--apply` to a script or runbook that ran a command without `--dry-run` to make the change. A call with `--dry-run` alone stays a dry run. See [Editor role policies](editor-role-policies.md#what-to-do-when-you-upgrade) and [Member management](member-management.md#what-to-do-on-an-installation-that-exists). |

## Commands only change things with --apply (28 October 2026)

The commands below change an installation that exists: its roles, workflows, content, logs, mail, crontab, the
Velocity configuration or a whole database. Until 28 October 2026 a run without options made the change, so a run
typed to look at the plan already changed the installation. Each of them now only shows what it would change and
ends with `PASS: ... (dry run, nothing changed; --apply writes)`; `--apply` makes the change and checks it. A
`--dry-run` the command had is still accepted and means the same as no option; `--apply` together with `--dry-run` is
a usage error and ends the command with exit code 2 before it reads the database. The setup wizard, the kickstarter
and the cronjob parts call the classes, not the commands, and are not affected.

| Commands | What only `--apply` does now |
|---|---|
| `addeditorpolicies`, `addmembermanagement`, `addanonymouspublicpolicies`, `addexphelpreadpolicy` (`exp:help-grant`), `addoauthauthorizepolicy`, `adduserapproval` (both modes, group and section), `cleanuppolicies`, `adddefaultstates` | Add, grant, remove or assign policies, roles, workflow events and sections; give objects their default states. `adddefaultstates` had no dry run before. |
| `install` (`exp:install`), `kickstarter run` | Install. The dry run checks `kickstart.ini`, the database and the remote packages and shows the plan, including "would DROP every table" for `DatabaseAction=remove`. `--force` alone is now a usage error (exit 2); it is accepted together with `--apply`. |
| `audit` (rotate, archive, restore, purge, reindex, pseudonymise, import, checkpoint, `key rotate`, `sinks flush`, cron), `explogs` (`--rotate`, `--rotate-all`, `--prune`, `--compress`, `--truncate`), `contentcheck` (`--fix`, `--quarantine`, `--fix-permissions`), `exphelpcontent` (install, tables, classes, tree, roles, `check --fix`, seed-glossary, seed-topics, build-manuals, ebooks, `feedback --prune`, uninstall) | The writing actions; the reading ones (status, tail, search, report, verify, export ...) are as before. |
| `exploremipsumfill`, `collaborationsampledata` (also `--remove`), `cleanuprss`, `clusterpurge`, `ezsubtreecopy`, `ezsubtreeremove`, `trashpurge`, `cleanupversions` | Fill, create, remove, purge or copy content and files. |
| `mailbounces`, `mailconsent cleanup`, `notificationevents cleanup`, `notificationsubscriptions remove-missing`, `notificationrun` (`exp:notification:run`) | Suppress addresses, remove rows, send notification mail. A dry run never sends mail. |
| `installcrontab --add`, `--remove` | Write the crontab block; the dry run shows the block and the lines that would change. `--show` is as before. |
| `preload` (`exp:preload`); `velocity` `config set` and `unset`, `cache clear`, `layout migrate`, `site`, `conf` and `mod` `enable` and `disable`, `ssl renew`, `watchdog install` and `remove`, `install` of the FrankenPHP engine; `webserver configure`; `frankenphp install` | Crawl the site; write the Velocity settings and the `/etc/vc` links, clear its response cache, install the watchdog units or the engine. |

Deliberately not changed:

| Command | Why |
|---|---|
| `ezpgenerateautoloads`, `cache` (`exp:cache`) and `bin/php/ezcache.php` | They work like build tools: they regenerate files that can always be regenerated, and composer, the deploy (`exp:velocity deploy`), CI and countless notes run them. They still act at once. |
| `velocity` and `webserver` `start`, `stop`, `graceful`, `restart`, `kill`, `status`, `watchdog run`, `ctl`, `ext`; `exp:velocity deploy` | Process control, which installed systemd units and the watchdog call; `deploy` keeps its own `--dry-run`. |
| `dfscleanup` | It already writes only with `-D`. |
| `resetuserpassword`, `userpasswordchangerequired` | The write is what they are called for. |
| `updatesearchindex`, `updateniceurls`, `makestaticcache`, `ezgeneratetranslationcache`, `warm`, `preloadjob` | They rebuild caches and indexes that can be rebuilt; the cache page, the setup wizard and cron call them. |
| The one-time upgrade converters (`convertezenumtoezselection`, `convertprice2multiprice`, `ezconvert2isbn13`, `updateisbn13`, `ezconvertdbcharset`, `ezconvertmysqltabletype`, `ezflowupgrade`, `ezwebininstall`, `ezwebinupgrade`) | Each is one step of a documented upgrade; they change with those steps. |
| Imports and schema tools (`ezimportdbafile`, `ezsqlinsertschema`, `ezcsvimport`, `publish_content`, `ezcreatedeactivatedusergroup`, `flatten`, `ezsessiongc`) | Their job is the write; installers, admin pages and cron call them. |
| `ini set`, `expcontentjob`, `maintenance`, `services`, `i18n`, `solr`, `phar` | Commands of subcommands that admin pages, the deploy and cron call; not changed in this round. |

| How to check | What to do |
|---|---|
| Run one of the commands without options: it lists what it would change and ends with `PASS: ... (dry run, nothing changed; --apply writes)`. | Add `--apply` to every script, crontab line and runbook that runs one of these commands to make the change; a call with `--dry-run` alone stays a dry run. In particular: a demo or test rebuild script that runs `exp:install`, `collaborationsampledata` or `preload` (the examples in [demo mode](../../examples/demo-mode/README.md) pass `--apply` and stop when nothing was installed), and an operator's crontab line that calls a command directly (the crontab block of an installation runs only `runcronjobs.php` and is not affected). Run a script against an older version without `--apply`: before 28 October 2026 the commands refuse it as an unknown option. |

## The WebDAV entry script is index_webdav.php (29 October 2026)

> **Change your vhost and rewrite rules.** Full before/after rules for Apache, Plesk, nginx and IIS, and the test
> procedure: [The WebDAV entry script is now index_webdav.php](webdav-entry-script-index-webdav.md).

| Change | How to check | What to do |
|---|---|---|
| The WebDAV server is `index_webdav.php`, named like `index_rest.php`, `index_treemenu.php` and `index_cluster.php`. `webdav.php` only includes it, writes one warning a day to `warning.log`, and will be removed in a later major version. `.htaccess_root` and `.htaccess_root_static` carry a commented rule `#RewriteRule ^index_webdav\.php - [L]`. | `grep -rn 'webdav\.php'` over the virtual hosts (HTTP **and** HTTPS), `.htaccess`, Plesk's additional directives, nginx and IIS configurations; `grep 'old name of the WebDAV entry script' var/log/warning.log`. | Name `index_webdav.php` in every rule (`RewriteRule`, nginx `location` and `fastcgi_param SCRIPT_FILENAME`, IIS rules, allowlists of root scripts), then test: `curl -i -X OPTIONS` (`DAV: 1`) and `curl -i -X PROPFIND -H 'Depth: 1'` (`207 Multi-Status`) against `/index_webdav.php/`. Clients mounted at `/webdav.php/...` keep working through the shim; mount them again at `/index_webdav.php/...`. WebDAV still never asks for a second sign-in step by default. |

## Related pages

- [The WebDAV entry script is now index_webdav.php](webdav-entry-script-index-webdav.md)
- [October 2026 chronicle](../../history/2026/2026-10.md)
- [Behaviour changes of 16 to 30 September 2026](behaviour-changes-2026-09b.md)
- [Behaviour changes of July and August 2026](behaviour-changes-2026-07-08.md)
- [Extensions: behaviour changes](extensions-behaviour-changes.md)
- [Upgrading guide](../../guides/upgrading.md)
