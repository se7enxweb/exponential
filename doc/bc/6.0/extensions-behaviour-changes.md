# Behaviour changes of the legacy extensions (September and October 2026)

Between 22 September and 2 October 2026 every legacy extension, design and theme shipped with or beside Exponential 6.0.15 was brought up to the same
standard. Most changes are invisible. This page lists the ones that are not, in the order you should check them when you upgrade a site, with how to
check and how to fix. Each extension's own page under [Extensions](../../features/6.0/extensions/README.md) lists its releases.

Related guides: [YUI removed from Exponential](yui-removal.md), [Commands, cronjob parts and module views as classes](cli_cronjob_view_abstractions.md),
[PHP 8](php8.md), [Velocity engines](velocity-engines.md), [Hardening](hardening.md).

## Quick checklist

1. Update the extensions you use to their current release (see [release notes](../../changelogs/extensions/README.md)); keep `se7enxweb/expui` installed.
2. `php bin/php/ezpgenerateautoloads.php -e` then `php bin/php/ezcache.php --clear-all --allow-root-user`.
3. Search your own extensions and designs for the patterns in sections 1, 4 and 6 below.
4. Check roles for renamed policies (section 7).
5. Rebuild or refresh anything that caches packed scripts and styles (`ezjscore` 1.5, `--clear-tag=template,content`).

## 1. Persistent PHP workers: module views no longer declare at file level without a guard

**What changed (22 September 2026).** Exponential includes a module view on every request. A function or class declared at the top level of that file is declared
a second time on the second include, which is fatal. Under one process per request this never happens; under a persistent worker (Exponential Velocity,
FrankenPHP) it kills the worker on the second request and the client gets a 502. In `explayouts_ui`, `explayouts_ui_api`, `cjw_newsletter`, `eztags`, `ezupdate`,
`ezflow`, `ezodf`, `ezpm`, `xrowmetadata`, `nxc_powercontent`, `syndication`, `sevenx_dse` and `xrowextract` every declaration is now wrapped in `function_exists()` or
`class_exists()`, and top-level declarations are moved to the head of their file (PHP binds an unconditional top-level declaration before the first line runs; a guarded
one exists only when execution reaches it, so a file that called a function above its own declaration would otherwise break). Behaviour under Apache and PHP-FPM is unchanged.

**Check your own code.**

```bash
grep -rln "^function \|^class " extension/*/modules extension/*/classes/runnable/views 2>/dev/null
```

**Fix.** Wrap each declaration: `if ( !function_exists( 'myHelper' ) ) { function myHelper() { ... } }` and `if ( !class_exists( 'MyClass', false ) ) { class MyClass { ... } }`,
or better, make the view a class as described in [Commands, cronjob parts and module views as classes](cli_cronjob_view_abstractions.md). Also avoid state in function statics and
`static` variables that must reset between requests (see `ezstarrating` 6.0.3, whose rating statistics cache lived in a function static; `sevenx_dse` 1.1.1, whose security-policy nonce did).

## 2. Forgot password pages no longer tell whether an address has an account

**What changed.** The kernel's `user/forgotpassword` stopped answering "There is no registered user with that email address" for a valid address, because that lets anyone
test addresses one by one. The designs and extension that carried their own copy of the page did the same on 27 September 2026: `ezwebin` 6.0.7, `ezdemo` 6.0.4 and
`ezmbpaex` (the `userpaex/forgotpassword` view) 6.0.2. Now an unknown but valid address gets the same page as a known one ("if an account is registered with the address, a mail has been
sent to it"), input that is not an e-mail address gets "Please enter a valid email address.", an address posted as an array is treated as not given, and every address the page prints is escaped
with `|wash`. The messages use the kernel's translation context `design/standard/user/forgotpassword`, so existing translations apply. `ezmbpaex` 6.0.3 also makes the reset key 128 random bits
from the system's secure source (it was an `md5` of the time and `mt_rand()`); keys and links keep their shape, existing links work until they expire.

**Check.** If your site design overrides `user/forgotpassword.tpl` or the `userpaex` template, compare it with the kernel's. Tests or documentation that expect the old message must change.

## 3. Error pages show their own error in the page title

**What changed.** Every error page reaches the page layout with the same URI, so whichever error was cached first gave its title and path to all later ones: a "Page not found" could be titled
with the access denied error. The head, path and menu cache blocks of `ezwebin` 6.0.8, `ezdemo` 6.0.5 and `sevenx_themes_simple` 1.0.15 now add the error type and number to the URI key when the page is an
error page. Every other page keeps exactly the same cache key.

**Check.** If your own `pagelayout.tpl` has `{cache-block keys=array( $module_result.uri, ... )}` around the head, path or menus, key error pages by error type and number too, as the
extensions do:

```
{def $uri_cache_key = $module_result.uri}
{if is_set( $module_result.errorType )}
    {set $uri_cache_key = concat( $module_result.uri, '|error|', $module_result.errorType, '|', first_set( $module_result.errorNumber, '' ) )}
{/if}
{cache-block keys=array( $uri_cache_key, $user_hash, $extra_cache_key )}
    ...
{/cache-block}
```

## 4. YUI is gone; jQuery 4 and Exponential UI replace it

See [YUI removed from Exponential](yui-removal.md) for the whole picture and the replacement table. For the extensions:

| Extension | Release | Breaking part |
|---|---|---|
| `ezjscore` | 1.5.0 | `ezjsc::yui2`, `ezjsc::yui3`, `ezjsc::yui3io`, the YUI libraries and `ezjscore.ini [YUI3]` removed; `PreferredLibrary` is `jquery` |
| `ezautosave` | 6.0.6, 6.0.7 | `ezautosubmit.js`, `ezcontentpreview.js` (admin) and the front-end YUI autosave removed; use `exp::autosave` |
| `ezmultiupload` | 6.0.6 | `design/standard/javascript/ezmultiupload.js` and the YUI 3 uploader removed; `upload.tpl` uses `exp::io`, `exp::dialog`, `exp::upload` |
| `ezstarrating` | 6.0.6 | `ezstarrating_yui3.js` removed; `ezstarrating_jquery.js` is always used |
| `eztags` | 2.4.9 | `eztags_children_yui.tpl` renamed `eztags_children_table.tpl`; `ezjsc::yui2` no longer added to every admin page; `$.fn.eZTagsChildren` is the old name of the Exponential UI table |
| `ezflow` | 6.1.4 | YUI widgets and skin, the Prototype library and YUI's sprite copy removed; tab classes `.ezpage-tabs*` |
| `ezwebin`, `ezwt`, `ezdemo` | 6.0.15, 6.0.8, 6.0.8 | date fields use `exp::datepicker`; the toolbar sort page and the demo galleries run on jQuery |
| `cjw_newsletter` | 4.1.9 | the admin2 children list no longer loads a script that no longer exists |
| `sevenx_themes_simple` | 1.0.20 | YUI settings and calendar styles removed |

These extensions require `se7enxweb/expui ^1.0.0.1` where they use its modules (`eztags`, `ezwebin`, `ezwt`, `ezmultiupload`, `ezautosave`). jQuery 4 itself removed helpers (`$.isArray`, `$.trim`,
`$.proxy`, `$.browser`, the `.click()`, `.bind()`, `.unbind()` shorthands and boolean `.attr()`): `eztags`, `ezie`, `ezstarrating`, `cjw_newsletter`, `ezwebin`, `ezdemo`, `xrowmetadata` and the simple
theme's Magnific Popup were ported (`.on()`, `.off()`, `.prop()`, `Array.isArray`).

**Check your own scripts** for `YUI(`, `YAHOO.`, `ezjsc::yui`, `$.browser`, `$.isArray`, `.bind(`, `.unbind(` and `.load(function`.

## 5. HTML5 markup in the designs

`ezwebin` 6.0.3, its installer package `ezwebin-ezpackage`, `ezwt` 6.0.3 and `sevenx_themes_simple` 1.0.12 (July 2026) write HTML5: XHTML self-closing slashes (`<br />`, `<input />`, `<img />`, `<meta />`, `<link />`)
and obsolete `type` and `language` attributes on `<script>`, `<style>` and stylesheet `<link>` tags are gone; the toolbar stylesheet dropped vendor prefixes. `ezdemo`, `ezodf` and `ezflow` followed with `<br>`
(27 September). If your design overrides these templates, a style or script that selects on the old attributes or on `xhtml` output may need a change. `bcwebsitestatistics` 1.0.6 does the same for the analytics tags.

## 6. Code that must run on every database

A series of fixes (29 and 30 September 2026) made queries portable. Custom code should follow the same rules; the extensions' fixes show each:

| Problem | Where | Use instead |
|---|---|---|
| `LIMIT n` appended to SQL, `DELETE ... ORDER BY ... LIMIT` | `cjw_newsletter` export preview (Oracle ORA-03049), `ezflow` block pool trimming | the `limit` parameter of `eZDB::arrayQuery()`, and delete by key |
| `%` or `MOD()` for bit tests | `eztags` (`language_mask`), SQLite and Oracle | `eZDB::bitAnd( column, 1 )` |
| Double-quoted strings (`LIKE "%x%"`) | `cjw_newsletter` user search, `eztags` tree filter | single-quoted strings; double quotes are identifiers outside MySQL |
| Backticks and trailing semicolons | `cjw_newsletter` send abort | plain identifiers, no semicolon |
| `SELECT DISTINCT` over CLOB columns | `cjw_newsletter` user filter | `EXISTS` |
| `AS` before a table alias | `ezflow` `eZPageBlock::fetch()` (Oracle ORA-00933) | `table alias` without `AS` |
| `FROM_UNIXTIME()` only | `ezwebin` `ezarchive()` | a database specific branch (Oracle computes dates from 1970-01-01) |
| Joins and `GROUP BY` on MongoDB | `eztags` (2.3.4), `explayouts_ui` lists | read collections separately and match in PHP |

## 7. Names, tabs and policies that changed

| Extension | Change | What to do |
|---|---|---|
| `git_manager` 2.0.4 | the view `git_manager/dump` and the policy function `dump` became `backup`; `/git_manager/dump` redirects | roles that grant `dump` keep working; rename them with `php extension/git_manager/bin/php/upgrade-policy-dump-to-backup.php --dry-run`, then without `--dry-run` |
| `git_manager` 2.0.6 to 2.0.8 | new policy functions `push`, `remotes`, `submodules` | grant them to the roles that may push, change remotes or submodules; a role with every function of the module has them |
| `xrowextract` 2.5.3 | tabs renamed **One class of content export**, **Multi class of content export**, **Import content file**, **Import content package** (the views `csv`, `archive`, `import`, `package` keep their addresses and policies); new policy functions `jobs`, `all_jobs`, `schedule`, `destinations`, `history`, `password_hash` | grant the new functions where wanted |
| `explayouts_ui` | the tab is **Layouts**; "rules" are **Layout mappings**; target type `content_node` shows as `node`; the class condition is called `class`; `ibexa_*` condition names are no longer offered (existing ones keep working) | none |
| `eztags` 2.3.5, 2.4.1 | menu entry and tooltip say **Tags** | none |
| `bccie` 1.1.4 | menu name **CIE** | none |
| `sevenx_dse` 1.1.2 | navigation part `dsenavigationpart` (it used `ezupdatenavigationpart`) | if you overrode the old identifier, change it |
| `ezupdate` 1.1.3 | the `dump` and `show` views and the *Dump assets* link, which belonged to `git_manager`, are removed | use `git_manager` |
| `ezie` 6.0.6 | the blur, levels and saturation tools (they never worked) are removed | none |

## 8. The layout editor API: tokens, drafts and locked zones

`explayouts_ui_api` 1.3.3 refuses every non-GET request that does not carry the session's form token (`X-CSRF-Token` header or `ezxform_token` field) with HTTP 403 and a JSON body
`{"error":{"code":403,"reason":"form_token_missing"}}`; 1.3.4 refuses block writes unless the block belongs to a draft; 1.2.1 refuses writes to blocks of a published shared layout and into linked zones.
Scripts that drove the API must fetch the token from `GET /explayouts_ui_api/app/api/config` (`csrf_token`), work on drafts and publish. See the
[API specification](../../specifications/6.0/explayouts-ui-api.md).

## 9. Security fixes that may need action

| Extension | Release | Fix | Action |
|---|---|---|---|
| `ezie` | 6.0.6 | the watermark name could read any image (`../`); the working folder came from a browser key and could be deleted recursively; permissions were checked only in `prepare` | update; nothing else |
| `git_manager` | 2.0.8 | backups were world-readable; the database password was on the `mysqldump` command line; branch names and hashes were not quoted; the AGPL dump kept secrets and form answers | update; **re-check the permissions of existing backups** under `var/site/backups/captions` (they are created owner-only from now on) and delete backups you no longer need |
| `cjw_newsletter` | 4.1.15 | object injection through a hidden form field, open redirects, guessable hashes, unchecked input | update |
| `xrowextract` | 2.5.3, 2.5.4 | imported file URLs restricted to the site's own scheme, host and port; ids checked before queries; package archives accepted only with plain files; escaped package names | update |
| `ezmbpaex` | 6.0.3 | guessable reset key | update; old links keep working until they expire |
| `explayouts_ui_api` | 1.3.3, 1.3.4 | form token on every write; drafts only | see section 8 |
| `owsimpleoperator` | (setting) | the shipped `PermittedFunctionList` contains `file_get_contents`, which lets a template read any file the web server can read | remove it from production settings if you do not need it |
| `bcwebsitestatistics` | (setting) | the shipped `Urchin` value is an example tracking id, not yours | set your own in `settings/override/bcwebsitestatistics.ini.append.php` |
| `AdminAid` | (setting) | user switching without a password | enable `LimitByIP`, restrict `UserSwitchIDLimit`, remove the extension from production |

## 10. Metadata, translations and integrity manifests

* Every extension now has an `ezinfo.php` and `extension.xml` that both state name, version, copyright, license and website, so **Setup > About** and the upgrade checks show them. A few showed the directory name
  as the name; they now show their real names (for example "Enhanced eZBinary File Type", "Xrow Meta Data", "Exponential Layouts UI API").
* Tab names, tooltips, navigation parts and left menu entries of extensions are translated in the contexts `design/admin/pagelayout`, `kernel/navigationpart` and `design/admin/parts/<menu>/menu`; add your language there.
* `cjw_newsletter`'s `share/filelist.md5` (the manifest behind **Setup > System Upgrade > File consistency check**) now matches the files; before 4.1.5 the check reported files nobody had touched.
* The copyright notices in the extensions name "1998 - 2026 7x & Exponential Foundation" first, above the original eZ Systems notices, which stay as the GPL requires.

## Related

* [Release notes of all extensions](../../changelogs/extensions/README.md)
* [History of the extensions](../../history/extensions/README.md)
