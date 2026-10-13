# LoveStack features in Exponential 6

LoveStack is Mugo Web's distribution of the legacy kernel originally published by eZ Systems
(`mugoweb/ezpublish-legacy` on GitHub and Packagist). Over the years Mugo Web added a number of
valuable features and fixes to it. Exponential 6.0.15 brings 36 of them into the Exponential
distribution, each one rebuilt in Exponential's own APIs, documented and covered by tests, so
that every Exponential site can use them together with Exponential's own features. With
thanks to Mugo Web, whose work this is built on.

This page is the overview: what each feature does, who it is for, how to switch it on, its
settings and defaults, what changes on an upgrade and on a new installation, the test that
covers it and the LoveStack commit it comes from. The detailed pages are listed in
[More documents](#more-documents).

**In one sentence:** nothing changes on an existing site unless you switch it on, except plain
bug fixes, a few new warnings and log settings, and the security checks for logging out (L03)
and for uploaded images (L05, L06).

**If you run Exponential and never ran LoveStack**, there is nothing to do: the features are in
6.0.15, and the ones that could change how a site behaves are off until you turn them on.
**If you run LoveStack**, see [Moving a site from LoveStack](#moving-a-site-from-lovestack).

---

## Source reference (where the features come from, and when)

The LoveStack features brought into Exponential were taken from Mugo Web's repository
**mugoweb/ezpublish-legacy** as it stood on the day of the port, **12 October 2026**. Every
feature, test and comparison in this document refers to that exact commit, so the work can be
reproduced and checked against the same source at any time.

| | |
|---|---|
| Repository | <https://github.com/mugoweb/ezpublish-legacy> (default branch `master`, not archived) |
| Commit used for the port | [`e61e9fdf0ca464197b79935ef784ed459721aa87`](https://github.com/mugoweb/ezpublish-legacy/commit/e61e9fdf0ca464197b79935ef784ed459721aa87), "Merge pull request #238 from mugoweb/php8-fix" |
| Source tree at that commit | <https://github.com/mugoweb/ezpublish-legacy/tree/e61e9fdf0ca464197b79935ef784ed459721aa87> |
| Date of that commit | 23 February 2026, 08:49:45 UTC (still the latest commit on `master` when checked on 12 October 2026) |
| Latest release then | [`v2025.03.1-1`](https://github.com/mugoweb/ezpublish-legacy/releases/tag/v2025.03.1-1), 1 March 2025 |
| Common ancestor with Exponential | [`3429aa91bba018f518fcb41757c9d627a0a73c0f`](https://github.com/mugoweb/ezpublish-legacy/commit/3429aa91bba018f518fcb41757c9d627a0a73c0f), 9 March 2016 (upstream ezsystems/ezpublish-legacy) |
| Mugo Web's work compared | `3429aa91bb..e61e9fdf0c`: [compare view](https://github.com/mugoweb/ezpublish-legacy/compare/3429aa91bba018f518fcb41757c9d627a0a73c0f...e61e9fdf0ca464197b79935ef784ed459721aa87) |
| Time since the last change, at the time of the port | 231 days (7 months and 19 days) since the last commit; 590 days (over 19 months) since the last release |

To reproduce: `git clone https://github.com/mugoweb/ezpublish-legacy && git -C ezpublish-legacy checkout e61e9fdf0ca464197b79935ef784ed459721aa87`.
The commit ids named for each feature (L01–L36) below are commits in that repository and open
as `https://github.com/mugoweb/ezpublish-legacy/commit/<id>`.

---

## The features in plain terms

What the 36 features are, grouped by what they do to an existing site after the upgrade.

### On for every site: fixes and safety checks

| | Feature | What you'd see |
|---|---|---|
| [L03](#l03--logging-out-ends-the-session-and-its-cookie) | Logging out really ends the session | after logout the session and its cookie are gone, not just given a new id |
| [L05](#l05--uploaded-images-are-checked-by-their-content) | Uploaded images are checked by their content | a file that is not a listed image type (a renamed file, HEIC, SVG) is refused |
| [L06](#l06--package-thumbnails-are-checked-the-same-way) | Package thumbnails are checked the same way | a fake image as a package thumbnail or style image is refused |
| [L11](#l11--database-errors-carry-the-drivers-error-number) | Database errors carry the driver's error number | developers can tell "duplicate key" from "table missing" in code |
| [L12](#l12--a-warning-when-a-change-clears-every-view-cache) | A warning when a change clears every view cache | one log line saying a big change cleared all view caches |
| [L20](#l20--the-locations-window-keeps-a-hidden-location-hidden) | The Locations window in the edit form no longer forces "Visible" | a hidden location stays hidden when you add another |
| [L21](#l21--selection-and-matrix-fields-without-stray-line-breaks) | No stray line breaks in selection and matrix fields | cleaner output |
| [L24](#l24--the-subsup-dialog-closes-on-ok) | The sub/superscript dialog closes when you press OK | was already the case in Exponential |
| [L31](#l31--templates-can-tell-layout-views-apart) | Templates can tell layout views apart again | bug fix for designers |
| [L34](#l34--very-large-numbers-no-longer-stop-the-search-index) | Very large numbers no longer stop the search index | bug fix |
| [L35](#l35--relations-of-related-objects-stay-out-of-the-search-index) | Relations of related objects stay out of the search index | was already the case in Exponential |
| [L36](#l36--the-hide-cronjob-and-the-search-index) | Hiding content by cronjob also updates the search index | hidden content stops showing up in search |
| [PHP 8](#php-8-review-and-other-fixes) | PHP 8 fixes from LoveStack, reproduced and fixed where still needed | fewer deprecations and errors on PHP 8.1 to 8.5 |

### On for every site: small visible changes

| | Feature | What you'd see |
|---|---|---|
| [L07](#l07--php-deprecations-get-their-own-log) | PHP deprecations get their own debug level and log | `var/log/deprecated.log`; the debug bar counts them instead of listing them |
| [L08](#l08--log-file-size-in-siteini) | Log file size is a setting, 2 MB by default | fewer, longer log files (was 200 KB) |
| [L09](#l09--a-warning-for-an-operator-parameter-without-a-default) | A warning when a template operator parameter has no default | for template developers |
| [L15](#l15--a-copy-button-on-the-admin-full-view) | A Copy button on the admin full view | until now Copy was only in the right-click menu |
| [L27](#l27--rawurlencode-and-rawurldecode-template-operators) | Template operators `rawurlencode` and `rawurldecode` | for template developers |
| [SameSite](#lovestacks-session-samesite-setting-is-read-too) | LoveStack's `[Session] SameSite` setting is read too | settings written for LoveStack keep working |

### New options, off until you switch them on

| | Feature | What you'd see |
|---|---|---|
| [L10](#l10--edit-links-in-the-list-of-used-templates) | An "edit" link format for the debug output's list of used templates | for template developers |
| [L13](#l13--audit-records-for-location-changes) | Audit records for adding a location and changing the main location | new entries in the audit console (removing a location is recorded already) |
| [L14](#l14--site-data-in-templates-and-in-the-administration) | Site data readable in templates, plus an admin list | for developers and administrators |
| [L16](#l16--required-alternative-text-for-image-fields) | "Alternative text is required" for an image field, per class | editors can't save an image without alt text where it's on |
| [L17](#l17--trim-the-stored-value-for-text-line-fields) | "Trim the stored value" for text line fields, per class | leading and trailing spaces removed on save |
| [L18](#l18--names-made-by-a-php-class) | Your own PHP class can make an object's name or URL alias | for developers |
| [L19](#l19--class-filter-default-for-fetches) | Class filters can default to "include" | changes fetch results, so off by default |
| [L22](#l22--hidden-custom-tags) | Hidden custom tags | custom tags that exist but are not offered in the editor's list |
| [L29](#l29--response-headers-for-error-pages) | Extra headers per error page (for example `Cache-Control` on 404) | set in `error.ini` |
| [L30](#l30--the-order-of-search-results-in-the-editors-dialogs) | ezjscore search result order from the search settings | ships with the order it always had |
| [L32](#l32--request-addresses-decoded-once) | Request addresses decoded once (`%2B` stays `+`) | some view parameters change, so off |
| [L33](#l33--the-double-quote-in-iri-url-aliases) | The double quote is cleaned from IRI URL aliases | new aliases can differ from older ones, so off |

### On for new installations, off for upgrades

| | Feature | Why it needs care | How it is handled |
|---|---|---|---|
| [L01](#l01--node-data-calls-can-require-a-policy) | `ezjscnode` (node data as JSON/XML) can require a policy | front-end JavaScript reading it for anonymous visitors would stop working; today anyone who may read a node can read its metadata this way | new installations on, upgrades off; the [list of callers](love/L01-callers.md) says who uses it |
| [L02](#l02--registration-can-require-a-policy) | Registration needs a policy | sites relying on open registration would lose the "register" link | new installations only |
| [L04](#l04--one-account-per-login) | Logins become unique in the database | adding the index fails if duplicate logins exist; LDAP or SSO may create duplicates | new installations have the index; upgrades get a duplicate report first and the index only on request |
| [L23](#l23--custom-tag-controls-in-the-rich-text-editor) | Custom tag controls in the rich text editor | the editor looks different | new installations on, upgrades off |

### New APIs for developers (additions only, nothing existing changes)

| | Feature |
|---|---|
| [L18](#l18--names-made-by-a-php-class) | `{MyClass}` in a class's name pattern: your own PHP class computes the name or URL alias |
| [L25](#l25--ezuribuild-builds-a-site-url-in-one-call) | `eZURI::build( $url, $full )`: build a site URL in one call |
| [L26](#l26--one-message-with-another-mail-transport) | `eZMailTransport::send( $mail, $transport )`: choose the mail transport per message |
| [L28](#l28--include--inside-settings-files) | `[Include path/*.ini]` inside settings files, to split large settings |
| [L31](#l31--templates-can-tell-layout-views-apart) | `eZTemplateDesignResource::setKeyValue()` |

### In short

The visible new features are required alt text, trimmed text fields, a Copy button, location
audit records, custom tag controls in the editor, extra error-page headers and settings-file
includes. The security changes are `ezjscnode` behind a policy, registration that can be
closed, a real logout, unique logins and image uploads checked by their content. Everything
else is small bug fixes and developer helpers.

---

## Feature by feature

Each entry says who it is for (**editor**, **administrator**, **developer**), what it does,
its settings with their shipped defaults, what a new installation and an upgraded site get,
how to switch it on, the test that covers it and the LoveStack commit it comes from. Settings
you change go into an override (`settings/override/<file>.append.php`) or a siteaccess, never
into the shipped file; clear the INI cache afterwards
(`php bin/php/ezcache.php --clear-tag=ini`). Test files are under `tests/tests/` and run on
their own: `php vendor/bin/phpunit tests/tests/<path>`.

### Security and sessions

#### L01 — Node data calls can require a policy

For **administrators**. The ezjscore calls `ezjscnode::load`, `::subtree` and
`::updatepriority` return a node's name, ids, remote id, owner, section, dates, class and
translations as JSON or XML to every user who may read the node, anonymous visitors included.
With `RequirePolicy=enabled` a user also needs the policy *ezjscore / call* with the function
list `ezjscnode`.

| | |
|---|---|
| Settings | `extension/ezjscore/settings/ezjscore.ini [ezjscServer_ezjscnode] Functions[]=ezjscnode`, `RequirePolicy=disabled`. `RequirePolicy` works for every `[ezjscServer_<name>]` block: not set = `Functions[]` checked as before (a refused call answers with its error text); `disabled` = not checked; `enabled` = checked, and a request whose calls are all refused for that reason is answered with **403 Forbidden** |
| New installation | `RequirePolicy=enabled` (written to `settings/override/ezjscore.ini.append.php`); the Editor role gets *ezjscore / call* with `ezjscnode`, which the sub-items list and the image size preview of the rich text editor use. Administrators have it through full access |
| Upgrade | unchanged (`RequirePolicy=disabled`) |
| Switch on | give *ezjscore / call* with the function list `ezjscnode` to every role whose users use the administration without full access, then set `RequirePolicy=enabled` in `settings/override/ezjscore.ini.append.php`. The shipped designs never call `ezjscnode` for anonymous visitors; see [who calls ezjscnode](love/L01-callers.md) |
| Test | `extension/ezjscore/ezjscServerRouterPolicyTest.php` |
| LoveStack | [`aa307a7ff3`](https://github.com/mugoweb/ezpublish-legacy/commit/aa307a7ff3110121006b1b97f19fdcbdeab41190) (2024) |
| Guide | [Roles and policies, section 8](../../guides/roles-and-policies.md#8-decide-who-may-register-and-who-may-read-node-data) |

#### L02 — Registration can require a policy

For **administrators**. Registering a new account (`user/register`) can be taken out of the
list of views everyone may open, so it needs the policy *User / Register*. The login pages of
admin and admin4, the standard design and its login toolbar, the media and the starter designs
show the register link or the Sign Up button only to a user who may open `user/register`, so a
site without open registration shows no dead link.

| | |
|---|---|
| Settings | `site.ini [RoleSettings] PolicyOmitListExclude[]` (new, empty) takes single entries out of `PolicyOmitList[]` after every settings file is read, so the entries extensions add stay. `user/register` stays in the shipped `PolicyOmitList[]` |
| New installation | `PolicyOmitListExclude[]=user/register`: registration needs the policy. For open registration, give the Anonymous role *User / Register* |
| Upgrade | unchanged: registration and its link stay open |
| Switch on | add `PolicyOmitListExclude[]=user/register` under `[RoleSettings]` in `settings/override/site.ini.append.php`; give the Anonymous role *User / Register* where registration should stay open |
| For developers | `expPolicyOmitList::entries()`, `::excluded()`, `::omits( $module, $view )`; in templates `fetch( 'user', 'can_open', hash( 'uri', 'user/register' ) )` |
| Tests | `kernel/classes/expPolicyOmitListTest.php`, `kernel/content/eZLoginTemplatesRegisterLinkTest.php` |
| LoveStack | [`98854ab935`](https://github.com/mugoweb/ezpublish-legacy/commit/98854ab9352eecbfd00e42b067d0cb3b7d26e6f7) (2021) |
| Guide | [Roles and policies, section 8](../../guides/roles-and-policies.md#8-decide-who-may-register-and-who-may-read-node-data) |

#### L03 — Logging out ends the session and its cookie

For **everyone**. Logging out removes the session and its contents instead of giving it a new
id with its contents kept. Every session handler sends the session cookie expired with the same
path, domain, `Secure`, `HttpOnly` and `SameSite` it was set with, so the browser drops exactly
that cookie, and the old session id no longer signs anyone in. A session started later in the
same request gets a new id, under PHP-FPM and Velocity alike.

| | |
|---|---|
| Settings | none |
| New installation and upgrade | on |
| Note for developers | code of your own that reads session variables after `eZUser::logoutCurrent()` finds the session empty. New: `eZSession::expiredCookie()`, `eZSession::expireCookie()` |
| Test | `kernel/classes/eZUserLogoutTest.php` |
| LoveStack | [`13ace6c9c8`](https://github.com/mugoweb/ezpublish-legacy/commit/13ace6c9c87ace0869ab234672aeffb377412e29) (2019) |
| Guide | [Security and audit, section 1](../../guides/security-and-audit.md#1-check-the-hardening-that-is-already-on-5-minutes) |

#### L04 — One account per login

For **administrators**. A login belongs to one account. A console command lists the logins
more than one account has (also those that differ only in case) and adds a unique index on
`ezuser.login` once there are none. A first version of a user account whose form did not
validate is not stored, and a draft, a package import, the content API's account string and a
first LDAP sign-in no longer give an account a login another account has; two requests
creating the same account at once end with one account.

| | |
|---|---|
| Commands | `./console exp:user-login-check` (report only; `--show-logins`, `--json`) and `./console exp:user-login-check --fix-index` (adds the index after a clean report, refuses otherwise). Exit code 0 clean or index added, 1 duplicates found or index refused, 2 error |
| New installation | the kernel schema of every engine has the unique index |
| Upgrade | the database update does **not** add the index, because it would fail on duplicates. Run the report, resolve what it lists, then `--fix-index` |
| For developers | `expUserLoginIntegrity::duplicates()`, `indexStatus()`, `addUniqueIndex()`, `loginTakenBy()`, `storeUser()` |
| Test | `kernel/classes/expUserLoginIntegrityTest.php` |
| LoveStack | [`826e1d4647`](https://github.com/mugoweb/ezpublish-legacy/commit/826e1d4647060b30ed6af1be5dff3233ced17840) (2020) |
| Guide | [Security and audit, section 7](../../guides/security-and-audit.md#7-one-account-per-login-2-minutes) |

#### L05 — Uploaded images are checked by their content

For **everyone**. PHP's fileinfo reads the type of an uploaded image from its content, and the
type must be on an allow-list. A refused file is not stored. This applies to the image datatype
in the edit form, `content/upload`, the rich text editor's upload dialog, the AJAX uploader,
WebDAV and imports; `eZContentUpload` refuses such a file before it creates an object.

| | |
|---|---|
| Settings | `image.ini [ImageUploadSettings] ValidateMIMEType=enabled`, `AllowedMIMETypes[]` = `image/jpeg`, `image/png`, `image/gif`, `image/webp`, `image/avif`, `image/tiff`, `image/bmp`. SVG is not on the list |
| New installation and upgrade | on: a file that is not a listed image by content (a renamed file, HEIC, SVG) is refused with "A valid image file is required." or "Images of the type %1 are not accepted." |
| To accept another type | add it to `AllowedMIMETypes[]` in an override. `ValidateMIMEType=disabled` keeps only the `getimagesize()` check. Without the fileinfo extension that check decides, and one warning per process says so |
| For developers | `expImageUploadValidator::validate( $path, $originalName = null )` returns `array( 'valid' => bool, 'mime' => string )` |
| Tests | `kernel/classes/expImageUploadValidatorTest.php`, `extension/ezjscore/ezjscServerFunctionsAjaxUploaderImageTest.php` |
| LoveStack | [`c8e45fced7`](https://github.com/mugoweb/ezpublish-legacy/commit/c8e45fced70f3b9f3a03c1b4329f3eb863c72188) (2017) |
| Guide | [Behaviour changes of October 2026](behaviour-changes-2026-10.md#access-and-security) |

#### L06 — Package thumbnails are checked the same way

For **administrators**. The package wizard checks the thumbnail of a new package and the
images of a style package with the same content check and allow-list as L05.

| | |
|---|---|
| Settings | those of L05 |
| New installation and upgrade | on |
| Test | `kernel/classes/packages/eZStylePackageCreatorTest.php` |
| LoveStack | [`04b3b6985a`](https://github.com/mugoweb/ezpublish-legacy/commit/04b3b6985a0d8b07f1d0f32d0d9840c1b308fb62) (2018) |
| Guide | [Behaviour changes of October 2026](behaviour-changes-2026-10.md#access-and-security) |

#### LoveStack's session `SameSite` setting is read too

For **administrators** of sites coming from LoveStack. `site.ini [Session] SameSite=`, the name
LoveStack uses for the SameSite attribute of the session cookie, is read when Exponential's
`CookieSameSite` is not set (`CookieSameSite` wins when both are), and noted once per request at
the deprecated level of the debug output. Nothing changes for visitors; rename the setting to
`CookieSameSite` to silence the note.

| | |
|---|---|
| Test | `lib/ezsession/eZSessionSameSiteAliasTest.php` |
| LoveStack | [`e56cb305f0`](https://github.com/mugoweb/ezpublish-legacy/commit/e56cb305f0d83548bf3ec8ac44668be7ff4bc796) (2025) |
| Guide | [Security and audit, section 1](../../guides/security-and-audit.md#1-check-the-hardening-that-is-already-on-5-minutes) |

### Diagnostics and logging

#### L07 — PHP deprecations get their own log

For **administrators** and **developers**. PHP deprecations have a debug level of their own.
They are written to `var/log/deprecated.log`, each distinct message once per request, and only
counted: the debug bar shows their number (`depr`), they stay in the page's warning count, and
none is kept in memory or listed with the other messages. On PHP 8 this keeps the debug output
readable and its memory use low.

| | |
|---|---|
| Settings | `site.ini [DebugSettings] DeprecatedLog=enabled`. Add `AlwaysLog[]=deprecated` to write them while debug output is off as well |
| New installation and upgrade | on; `DeprecatedLog=disabled` keeps them warnings as before |
| For developers | `eZDebug::LEVEL_DEPRECATED`, `eZDebug::SHOW_DEPRECATED` (part of `SHOW_ALL`), `eZDebug::writeDeprecated()`, `eZDebug::deprecatedCount()` |
| Test | `lib/ezutils/eZDebugDeprecatedTest.php` |
| LoveStack | [`fbbd333a85`](https://github.com/mugoweb/ezpublish-legacy/commit/fbbd333a85350123a2234db1357fc5fd9478ca3b), [`e20570a7ad`](https://github.com/mugoweb/ezpublish-legacy/commit/e20570a7ad846b31f3e60609dec0b9c0f7e44086), [`b7832d110e`](https://github.com/mugoweb/ezpublish-legacy/commit/b7832d110e4790edc4510328353f24acfb7df19b) (2025) |
| Guide | [PHP deprecations and log file sizes](../../guides/operating-a-site.md#php-deprecations-and-log-file-sizes) |

#### L08 — Log file size in site.ini

For **administrators**. The size at which log files are rotated, and how many old files are
kept, are settings, read by eZLog and eZDebug.

| | |
|---|---|
| Settings | `site.ini [FileSettings] LogFileMaxSize=2097152` (2 MB), `LogFileRotateCount=3`. `eZLog::setMaxLogSize()`, `setLogrotateFiles()` and the constants in `config.php` still win over them |
| New installation and upgrade | log files are rotated at 2 MB instead of 200 KB; `LogFileMaxSize=204800` keeps the old size |
| Test | `lib/ezfile/eZLogMaxSizeTest.php` |
| LoveStack | [`e39dde30ff`](https://github.com/mugoweb/ezpublish-legacy/commit/e39dde30ff3213bc32e7423b0827ef29f07d55af) (2021) |
| Guide | [PHP deprecations and log file sizes](../../guides/operating-a-site.md#php-deprecations-and-log-file-sizes) |

#### L09 — A warning for an operator parameter without a default

For **template developers**. A template operator parameter that the template leaves out and the
operator gives no default value for is `null`, with the warning `Operator '<op>' has no default
value for parameter '<name>'`, once per operator and parameter in a request (a compiled template
is warned once, when it is compiled).

| | |
|---|---|
| New installation and upgrade | on; only the warning is new |
| Test | `lib/eztemplate/eZTemplateOperatorDefaultWarningTest.php` |
| LoveStack | [`0bb0bac618`](https://github.com/mugoweb/ezpublish-legacy/commit/0bb0bac618273b2eea8ba139864677b666c0b382) (2016) |
| Guide | [An operator parameter without a default value](../../guides/templates-and-design.md#an-operator-parameter-without-a-default-value) |

#### L10 — Edit links in the list of used templates

For **template developers**. The "Edit" links in the debug output's list of used templates can
point to an address of your choice, for example the template editor of the administration.

| | |
|---|---|
| Settings | `site.ini [DebugSettings] UsedTemplateEditUrl=`, a `sprintf()` format: `%1$s` the editor address with the file, `%2$s` the siteaccess, `%3$s` the template file. Empty (shipped) = the link the list always had |
| New installation and upgrade | unchanged until set |
| Test | `kernel/common/eZTemplatesStatisticsReporterEditUrlTest.php` |
| LoveStack | [`fb20bee0d2`](https://github.com/mugoweb/ezpublish-legacy/commit/fb20bee0d2be87187f992284971abe0347798106) (2019) |
| Guide | [Edit links in the list of used templates](../../guides/templates-and-design.md#edit-links-in-the-list-of-used-templates) |

#### L11 — Database errors carry the driver's error number

For **developers**. `eZDBException::getCode()` is the driver's error number of the failed
statement on every driver, as `errorNumber()`: MySQL's number, PostgreSQL's SQLSTATE (a string),
SQLite's result code, Oracle's ORA number; MongoDB keeps the server's code in `errorNumber()`.
The "database unavailable" page (HTTP 503) also recognises the error numbers that say the
connection went away or could not be made.

| | |
|---|---|
| New installation and upgrade | on. PostgreSQL's `errorNumber()` is the SQLSTATE instead of `1`; with the exception handling on, MySQL on PHP 8.1 and later throws `eZDBException` instead of `mysqli_sql_exception` |
| Test | `lib/ezdb/eZDBExceptionCodeTest.php` |
| LoveStack | [`3d18f47551`](https://github.com/mugoweb/ezpublish-legacy/commit/3d18f475510fa3a81a4b0984854d87ace79fd269) (2017) |
| Guide | [Know your database driver](../../guides/operating-a-site.md#7-know-your-database-driver) |

#### L12 — A warning when a change clears every view cache

For **administrators**. When a change would clear the view caches of
`site.ini [ContentSettings] CacheThreshold` nodes or more and all view caches are expired
instead, a warning in the log names the number of nodes and the threshold (it was a debug
message).

| | |
|---|---|
| New installation and upgrade | on |
| Test | `kernel/classes/eZContentCacheManagerThresholdWarningTest.php` |
| LoveStack | [`f9691221a3`](https://github.com/mugoweb/ezpublish-legacy/commit/f9691221a3d3999722544285a9be7da52a1f44d6) (2018) |
| Guide | [Caches, usual questions](../../guides/caches.md#6-usual-questions) |

### Content, datatypes and the administration

#### L13 — Audit records for location changes

For **administrators**. Location changes are events of Exponential's audit trail (channel
`content`): `content.node.add` once per added location, `content.node.remove` with whether the
removed location was the main one, and `content.node.main` when an editor chooses another main
location or the next location takes the place of a removed main one. The audit console shows
them as Location add, Location remove and Main location change. LoveStack's names
`location-assign`, `location-remove` and `main-node-update` are mapped to these events, so
`eZAudit::writeAudit()` calls and audit files of those names keep working.

| | |
|---|---|
| Settings | `audit.ini [AuditEventSettings] Enabled[]`; `[AuditCompatSettings] Map[]` and `[AuditSettings] AuditFileNames[]` hold the LoveStack names |
| New installation and upgrade | `content.node.remove` on, as before; `content.node.add` and `content.node.main` off |
| Switch on | `Enabled[]=content.node.add` and `Enabled[]=content.node.main` under `[AuditEventSettings]` in `settings/override/audit.ini.append.php` |
| Test | `kernel/content/eZAuditLocationTest.php` (needs a database; rolls back what it writes) |
| LoveStack | [`9c931192e0`](https://github.com/mugoweb/ezpublish-legacy/commit/9c931192e06022a7be9929f62d4c851a91fe25b7) (2017) |
| Guide | [Audit reference: location changes](audit.md#location-changes-who-added-removed-or-chose-a-location) |

#### L14 — Site data in templates and in the administration

For **developers** and **administrators**. A template can read one row of `ezsite_data` by name,
for names you list; **Setup > Site data** (`setup/sitedata`, policy `setup/system_info`) lists
the rows with a filter and paging, in admin, admin3 and admin4.

| | |
|---|---|
| Settings | `site.ini [SiteDataSettings] TemplateReadable[]` (empty: nothing is readable from templates) |
| Template | `{def $row = fetch( 'content', 'site_data', hash( 'name', 'my-flag' ) )}` gives the eZSiteData object, or false when the name is not listed or not stored |
| New installation and upgrade | nothing readable until a name is listed; the admin list is there |
| For developers | `eZSiteData::createNew()`, `templateReadableNames()`, `isTemplateReadable()`, `fetchList()`, `fetchListCount()` |
| Tests | `kernel/content/eZContentFunctionCollectionSiteDataTest.php`, `kernel/classes/eZSiteDataCreateTest.php` |
| LoveStack | [`55029bd473`](https://github.com/mugoweb/ezpublish-legacy/commit/55029bd473e47e73b86f748f0d2d264a5e14843a), [`a679a13176`](https://github.com/mugoweb/ezpublish-legacy/commit/a679a131768b3bbdb2337c82c16d15ec9e58c854), [`94b7b6c239`](https://github.com/mugoweb/ezpublish-legacy/commit/94b7b6c239f7abbf58cbefed07ac28784933c997) (2017–2019) |
| Guides | [Site data in templates](../../guides/templates-and-design.md#9-site-data-in-templates), [Site data in System information](../../guides/system-information.md#9-site-data) |

#### L15 — A Copy button on the admin full view

For **editors**. The full view of the administration (admin, admin3, admin4) has a **Copy**
button next to Move, active with create access at the item's parent as in the context menu. It
opens the same copy steps as the context menu.

| | |
|---|---|
| New installation and upgrade | on |
| Test | `kernel/content/eZAdminFullViewCopyButtonTest.php` |
| LoveStack | [`4d56593614`](https://github.com/mugoweb/ezpublish-legacy/commit/4d565936146cd970051a51f27350abb30ecfa853) (2019) |
| Guide | [Copy from the full view](../../guides/content-model-and-editing.md#copy-from-the-full-view) |

#### L16 — Required alternative text for image fields

For **editors** and **administrators** (accessibility). An image attribute can require an
alternative text. The edit form, the online editor's upload dialog (also used by Tiptap) and the
AJAX uploader of relation fields then refuse an image without one; the AJAX uploader has an
alternative text field for classes with an image attribute. Packages carry the option.

| | |
|---|---|
| Settings | the class attribute option **Alternative text is required** on the class edit page (`eZImageType::ALT_TEXT_REQUIRED_FIELD`, `data_int2`) |
| New installation and upgrade | off for every class |
| Switch on | per image attribute, on the class edit page |
| Tests | `kernel/classes/datatypes/eZImageTypeAltTextTest.php`, `extension/ezjscore/ezjscServerFunctionsAjaxUploaderAltTextTest.php` |
| LoveStack | [`a28863d9c0`](https://github.com/mugoweb/ezpublish-legacy/commit/a28863d9c08e8ad417d1e1abcbbd664b777c743c) (2017) |
| Guide | [Alternative text is required](../../guides/content-model-and-editing.md#alternative-text-is-required-image-fields) |

#### L17 — Trim the stored value for text line fields

For **editors** and **administrators**. A text line attribute can remove spaces at the start
and the end of its value, in the edit form, information collection, imports and uploads.

| | |
|---|---|
| Settings | the class attribute option **Trim the stored value** (`eZStringType::TRIM_FIELD`, `data_int2`) |
| New installation and upgrade | off for every class |
| Switch on | per text line attribute, on the class edit page |
| Test | `kernel/classes/datatypes/eZStringTypeTrimTest.php` |
| LoveStack | [`6a5c7dd23e`](https://github.com/mugoweb/ezpublish-legacy/commit/6a5c7dd23efffa349d255f65bc03b7de55483915) (2020) |
| Guide | [Trim the stored value](../../guides/content-model-and-editing.md#trim-the-stored-value-text-line-fields) |

#### L18 — Names made by a PHP class

For **developers**. A class's object name pattern or URL alias pattern that starts with
`{MyResolver}` is resolved by your PHP class `MyResolver`, when it is listed and implements
`expNamePatternResolverInterface`. Any other name is logged and the pattern is resolved from its
tokens as usual.

| | |
|---|---|
| Settings | `content.ini [NamePatternSettings] ResolverClasses[]` (empty) |
| New installation and upgrade | nothing changes until a class is listed |
| For developers | `expNamePatternResolverInterface::resolveNamePattern( $object, $version, $translation, $length, $sequence )`; `eZNamePatternResolver::resolve()`, `customResolver()`, `limitName()`. The constructor and `resolveNamePattern()` are unchanged |
| Tests | `kernel/classes/eZNamePatternResolverClassTest.php`, `kernel/classes/eZNamePatternResolverLimitTest.php` |
| LoveStack | [`95d86f5990`](https://github.com/mugoweb/ezpublish-legacy/commit/95d86f5990ba83be39e4871bd88c2ecf120e8099) (2020) |
| Guide | [Names made by a PHP class](../../guides/content-model-and-editing.md#names-made-by-a-php-class-developers) |

#### L19 — Class filter default for fetches

For **template developers**. The class filter type the fetch functions `list`, `tree`,
`list_count`, `tree_count` and `calendar` use when a fetch gives `class_filter_array` without
`class_filter_type`.

| | |
|---|---|
| Settings | `content.ini [FetchSettings] DefaultClassFilterType=` (empty: today's results, the array is ignored; `include`: filter by the array) |
| New installation and upgrade | empty |
| Test | `kernel/content/eZContentFunctionCollectionClassFilterTypeTest.php` |
| LoveStack | [`9c0ff40a78`](https://github.com/mugoweb/ezpublish-legacy/commit/9c0ff40a784057bc2cd5c0eb2600510ce8c7ba1c) (2018) |
| Guide | [Class filter default for fetches](../../guides/content-model-and-editing.md#class-filter-default-for-fetches-template-developers) |

#### L20 — The Locations window keeps a hidden location hidden

For **editors**. In the edit form's Locations window an existing location starts at
"Unchanged", not "Visible", so publishing no longer reveals a hidden location; a new location
starts as it was given (EZP-26501).

| | |
|---|---|
| New installation and upgrade | on (a fix) |
| Test | `kernel/content/eZEditLocationsVisibilityTest.php` |
| LoveStack | [`f1b7f056b4`](https://github.com/mugoweb/ezpublish-legacy/commit/f1b7f056b48ae296d9b0cee7b340dc5bd8879011) (2016) |
| Guide | [The Locations window](../../guides/content-model-and-editing.md#the-locations-window-keeps-a-hidden-location-hidden) |

#### L21 — Selection and matrix fields without stray line breaks

For **designers** and visitors. The view templates of the selection and matrix datatypes write
no stray `<br />`; several selected options are separated by one `<br />` each.

| | |
|---|---|
| New installation and upgrade | on, for every site that does not override these templates |
| Test | `kernel/datatypes/eZSelectionMatrixViewTemplateTest.php` |
| LoveStack | [`eee7f69fc3`](https://github.com/mugoweb/ezpublish-legacy/commit/eee7f69fc3aa06243b5e9d1dc5f5a3c5c948bdcc), [`67eec8f972`](https://github.com/mugoweb/ezpublish-legacy/commit/67eec8f972ffb023d588dafef0a91beea1be3e80), [`492a946963`](https://github.com/mugoweb/ezpublish-legacy/commit/492a946963082c988b69d24023854d60dd8bac62) |
| Guide | [Selection and matrix fields](../../guides/content-model-and-editing.md#selection-and-matrix-fields-without-stray-line-breaks) |

### Rich text editor

#### L22 — Hidden custom tags

For **administrators** and **editors**. Custom tags you list are not offered for new tags in the
editors (TinyMCE 3, TinyMCE 8, Tiptap). Where a hidden tag is already in a text it is still
shown, rendered, editable and saved back unchanged.

| | |
|---|---|
| Settings | `content.ini [CustomTagSettings] IsHidden[]` (none hidden) |
| New installation and upgrade | none hidden |
| For developers | `eZOEXMLInput::hiddenCustomTags()`, `offeredCustomTags()`, the keys `hidden` and `preview` of `getCustomTagDefinitions()` |
| Test | `extension/ezoe/expOECustomTagListTest.php` |
| LoveStack | [`e21ef46691`](https://github.com/mugoweb/ezpublish-legacy/commit/e21ef466914d44d384666fd024080f06be4c426c) (2019) |
| Guide | [Hidden custom tags](../../guides/content-model-and-editing.md#hidden-custom-tags) |

#### L23 — Custom tag controls in the rich text editor

For **editors**. A focused custom tag gets a bar to move it up or down, edit it and remove it
(its content stays), also with Alt+Shift+Up, Alt+Shift+Down, Alt+Shift+E and Alt+Shift+Delete;
Backspace in an empty block custom tag removes it; every block custom tag shows its name as a
label; a tag's dialog shows `images/editor/custom_tags_thumbs/<name>.png` from a design as an
example. TinyMCE 3 gets the plugin `exp_customtag_controls` (built on TinyMCE 3's plugin API and
ezjscore's jQuery, no YUI), TinyMCE 8 the buttons in its custom tag toolbar, Tiptap the same in
exp_oe_tiptap 0.1.2.

| | |
|---|---|
| Settings | `ezoe.ini [EditorSettings] CustomTagControls=disabled`; stylesheet `ezoe_customtags.css` (acts only when on) |
| New installation | `CustomTagControls=enabled` |
| Upgrade | unchanged (`disabled`) |
| Switch on | `CustomTagControls=enabled` in `settings/override/ezoe.ini.append.php`, then clear the INI cache and the packed scripts: `php bin/php/ezcache.php --clear-tag=ini --clear-id=ezjscore-packer` |
| Test | `extension/ezoe/expOECustomTagAssetsTest.php` |
| LoveStack | [`63334f1681`](https://github.com/mugoweb/ezpublish-legacy/commit/63334f16818083e9d545cc7a99190d8d2ca9f109) (2019; its `custom_tags_controls` plugin carries "Copyright 2014, Mugo Web") |
| Guide | [Custom tag controls](../../guides/content-model-and-editing.md#custom-tag-controls) |

#### L24 — The sub/sup dialog closes on OK

For **editors**. The sub and sup custom tag dialog of TinyMCE 3 closes when you press OK.
Exponential's `tag_custom.tpl` already had this fix, so nothing changed; it was checked in the
browser on TinyMCE 3 and Tiptap (TinyMCE 8 has its own sub and sup buttons).

| | |
|---|---|
| Test | browser check only (a JavaScript dialog) |
| LoveStack | [`3a8c14bc0c`](https://github.com/mugoweb/ezpublish-legacy/commit/3a8c14bc0c61699d69ff5aa175ac717cbaf6f51a) (2019) |

### Developer APIs and settings

#### L25 — `eZURI::build()` builds a site URL in one call

For **developers**. `eZURI::build( string $url, bool $full = false, bool $htmlEscape = true )`
gives the same link as the `ezurl` template operator (index file and siteaccess in front; with
`$full` scheme and host). Use `$htmlEscape = false` for a URL in a header or a JSON answer.

| | |
|---|---|
| Test | `lib/ezutils/eZURIBuildTest.php` |
| LoveStack | [`f2284944b2`](https://github.com/mugoweb/ezpublish-legacy/commit/f2284944b27f0ba38e2f41876804ae9400fd25ce) (2020) |
| Guide | [URLs, for developers](../../guides/urls-and-aliases.md#for-developers) |

#### L26 — One message with another mail transport

For **developers**. `eZMailTransport::send( eZMail $mail, ?string $transportType = null )` sends
one message with another transport of `site.ini [MailSettings] TransportAlias[]` (for example
`file`) without changing the setting; the mail gate applies as before.

| | |
|---|---|
| Test | `lib/ezutils/eZMailTransportSendTest.php` |
| LoveStack | [`df07cb1ef5`](https://github.com/mugoweb/ezpublish-legacy/commit/df07cb1ef5de68eaedc0ba86e2dbc91cfccea5d2) (2019) |
| Guide | [Testing without sending mail](../../guides/mail-preferences-developer.md#8-testing-without-sending-mail) |

#### L27 — `rawurlencode` and `rawurldecode` template operators

For **template developers**. RFC 3986 encoding (`%20` for a space) in templates:
`{$value|rawurlencode}`. `urlencode` and `urldecode` are unchanged. All four operators give an
empty text for a value that is not set.

| | |
|---|---|
| Settings | `template.ini [PHP] PHPOperatorList[rawurlencode]`, `PHPOperatorList[rawurldecode]` (shipped) |
| New installation and upgrade | available |
| Test | `kernel/common/eZTemplateRawURLEncodeOperatorTest.php` |
| LoveStack | [`627004e5c4`](https://github.com/mugoweb/ezpublish-legacy/commit/627004e5c4ab08917804382cca0b6c67afc68c47) (2021) |
| Guide | [Encoding a value for a URL](../../guides/templates-and-design.md#10-encoding-a-value-for-a-url-rawurlencode-and-rawurldecode) |

#### L28 — `[Include …]` inside settings files

For **administrators** and **developers**. A line `[Include site.d/*.ini]` in a settings file
reads other files at that place: the pattern is relative to the including file, wildcards are
allowed, the files are read in name order, includes nest, a loop is read once with a warning and
a pattern without a match is a notice. The included files are part of the INI cache, so
`php bin/php/ezcache.php --clear-tag=ini` and `exp:velocity deploy` pick up their changes. The
settings page, the classic settings editor and `exp:ini` keep the line as written and do not
expand it.

| | |
|---|---|
| New installation and upgrade | only active where a file has such a line |
| For developers | `eZINI::includedFiles()` |
| Test | `lib/ezutils/eZINIIncludeTest.php` |
| LoveStack | [`f7c382bc2b`](https://github.com/mugoweb/ezpublish-legacy/commit/f7c382bc2bb4abcbeeb521653f08ca2617eb277f) (2018) |
| Guide | [Splitting a large settings file](../../guides/settings-page.md#splitting-a-large-settings-file-with-include-) |

#### L29 — Response headers for error pages

For **administrators**. Each HTTP error page can send extra response headers, after its status
lines; they are stored with the page in the view cache and sent again from it. An invalid header
name or a value with a line break is left out with a warning.

| | |
|---|---|
| Settings | `error.ini [HTTPError-<code>] HeaderList[<name>]=<value>`, for example `HeaderList[Cache-Control]=no-store` under `[HTTPError-404]` (empty by default) |
| New installation and upgrade | nothing changes until an entry is added |
| Test | `kernel/error/ErrorHeaderListTest.php` |
| LoveStack | [`97690e9c93`](https://github.com/mugoweb/ezpublish-legacy/commit/97690e9c93b156f8c49724b13b69a0de699fed49) (2017) |
| Guide | [Response headers of error pages](../../guides/templates-and-design.md#12-response-headers-of-error-pages) |

#### L30 — The order of search results in the editors' dialogs

For **administrators**. The ezjscore search (used by the editor's embed and link dialogs and the
relation search) sorts eZ Find results by the search settings instead of always newest first.

| | |
|---|---|
| Settings | `ezfind.ini [SearchSettings] SortBy[]`; ezjscore ships `SortBy[published]=desc`, the order it always used |
| New installation and upgrade | unchanged order; without eZ Find or the setting the order stays newest first |
| Test | `extension/ezjscore/ezjscServerFunctionsJsSearchSortByTest.php` |
| LoveStack | [`3d2d337b0d`](https://github.com/mugoweb/ezpublish-legacy/commit/3d2d337b0dd44de0be91fc703f21dbd96622f35e), [`3af1ec3aee`](https://github.com/mugoweb/ezpublish-legacy/commit/3af1ec3aeef93ae9da652c13828ccdc92513302d) (2025) |
| Guide | [The order of search results](../../guides/content-model-and-editing.md#the-order-of-search-results-in-the-editors-dialogs) |

#### L31 — Templates can tell layout views apart

For **designers** and **developers**. Every node view sets the override key `layout` (empty
outside `layout/set`, the layout of `layout/set` kept), so a template override can match on it
again. New: `eZTemplateDesignResource::setKeyValue( string $key, $value, bool $override = true )`.

| | |
|---|---|
| New installation and upgrade | on |
| Test | `kernel/common/eZTemplateDesignResourceSetKeyValueTest.php` |
| LoveStack | [`856a95e240`](https://github.com/mugoweb/ezpublish-legacy/commit/856a95e240b4f2422c60d5253f9730ed99e033af) (2016) |
| Guide | [The layout key](../../guides/templates-and-design.md#11-telling-a-layout-view-apart-the-layout-key) |

### URLs and search

#### L32 — Request addresses decoded once

For **administrators**. The address of a request is decoded once instead of twice, so
`/(tag)/bar%2Bbar` gives the view parameter `bar+bar` instead of `bar bar`. PHP-FPM and Velocity
hand the kernel the same raw address and the response cache keys are made from it, so they do
not change.

| | |
|---|---|
| Settings | `site.ini [URLTranslator] DecodeOnce=disabled` |
| New installation and upgrade | off |
| Switch on | `DecodeOnce=enabled` in `settings/override/site.ini.append.php`, after checking links of your own that put an encoded `+` or `%` into a view parameter |
| For developers | `new eZURI( $uri, $decode )`, `setURIString( $uri, $fullInitialize, $decode )` (`null`: follow the setting), `eZURI::decodeOnce()` |
| Test | `lib/ezutils/eZSysRequestURIDecodeOnceTest.php` |
| LoveStack | [`f435c2f91e`](https://github.com/mugoweb/ezpublish-legacy/commit/f435c2f91eaeba686d8f3bda4a6f8c41bb3e7329) (2018) |
| Guide | [Decoding and cleaning addresses](../../guides/urls-and-aliases.md#8-decoding-and-cleaning-addresses-two-optional-settings) |

#### L33 — The double quote in IRI URL aliases

For **administrators**. LoveStack's URL alias cleanup strips `!`, dots and separators at the end
of an alias, and treats `"` as a reserved character. Exponential has stripped those trailing
characters since 2016; what it adds is the double quote: in the IRI transformation
(`urlalias_iri`) it becomes a separator and is stripped at the ends like the others.

| | |
|---|---|
| Settings | `site.ini [URLTranslator] TrailingCleanup=disabled` |
| New installation and upgrade | off |
| Switch on | `TrailingCleanup=enabled` in `settings/override/site.ini.append.php`. Existing aliases stay until their object is published again; the old alias then redirects |
| For developers | `eZCharTransform::trailingCleanup()` |
| Test | `lib/ezi18n/eZCharTransformTrailingCleanupTest.php` |
| LoveStack | [`7517c0e925`](https://github.com/mugoweb/ezpublish-legacy/commit/7517c0e925178e5f932ac653439a39203ec03cb0) (2017) |
| Guide | [Decoding and cleaning addresses](../../guides/urls-and-aliases.md#8-decoding-and-cleaning-addresses-two-optional-settings) |

#### L34 — Very large numbers no longer stop the search index

For **administrators**. A number in a text beyond the range of the built-in search index's
integer column is stored as the limit of the column instead of stopping the indexing of the
object. Each driver states its range (`MAX_INT`, `MIN_INT`: signed 32-bit for MySQL, PostgreSQL
and other drivers, signed 64-bit for SQLite and MongoDB), and `$db->clampInteger( $value )` keeps
a value within it, also one written as a float.

| | |
|---|---|
| New installation and upgrade | on |
| Test | `kernel/search/eZSearchEngineIntegerClampTest.php` |
| LoveStack | [`3774a54ead`](https://github.com/mugoweb/ezpublish-legacy/commit/3774a54ead854dd3e6d525e08b9d77b9853bfef6) (2018) |
| Guide | [Numbers in the built-in search index](../../guides/operating-a-site.md#numbers-in-the-built-in-search-index) |

#### L35 — Relations of related objects stay out of the search index

For **administrators**. The search index leaves out the relation fields of related objects, and
the relation datatypes need no recursion guard. Exponential has done the same since 2018
(EZP-27101), so there is nothing to switch and no setting; a site coming from LoveStack gets the
same search results.

| | |
|---|---|
| Test | `kernel/datatypes/eZObjectRelationMetaDataTest.php` (documents the behaviour, a cycle of relations included) |
| LoveStack | [`5c089f6250`](https://github.com/mugoweb/ezpublish-legacy/commit/5c089f6250645e2354342deda3a510fed63dcc7f) (2017) |
| Guide | [Numbers in the built-in search index](../../guides/operating-a-site.md#numbers-in-the-built-in-search-index) |

#### L36 — The hide cronjob and the search index

For **administrators** and **developers**. The hide cronjob (`hide.php`) tells the search engine
about each node it hides (in Exponential since 2018), and the `content/cache` event gives its
listeners `( $nodeIDs, $objectIDs )` from every sender in the kernel, now also from the node's URL
aliases page; a listener that takes only the node ids keeps working.

| | |
|---|---|
| New installation and upgrade | on |
| Tests | `kernel/classes/runnable/HideCronjobSearchIndexTest.php`, `kernel/classes/eZContentCacheEventSendersTest.php` |
| LoveStack | [`3298b53584`](https://github.com/mugoweb/ezpublish-legacy/commit/3298b535848044a39e7211f9413711c1b62f97f3), [`89a5a73877`](https://github.com/mugoweb/ezpublish-legacy/commit/89a5a738777f83ec1b219638d06ee24b96c91195) (2018) |
| Guide | [Cronjobs](../../guides/cronjobs.md#1-parts-scripts-and-the-global-part) |

### PHP 8 review and other fixes

LoveStack carries about forty fixes for PHP 7.2 to 8.3. Each named site was reproduced on PHP 8.5
and 8.1 as a regression test; most were already handled in Exponential (which runs on PHP 8.1 to
8.5) and stay as tests that document it. Those that still failed are fixed, for every
installation, with nothing to do on an upgrade:

- the `get_class` and `is_class` template operators accept a value that is not an object without
  template compilation too (LoveStack [`e828c54709`](https://github.com/mugoweb/ezpublish-legacy/commit/e828c54709a8ed4916f43042d6d1b01c1b8c89b8));
- `wash(email)` of an empty value, the classic HTML debug report, the debug bar and `ezurl` /
  `eZURI` of a number or of null give no "Passing null" deprecation or warning (LoveStack
  [`4065ed16b3`](https://github.com/mugoweb/ezpublish-legacy/commit/4065ed16b39e7d7c25725599b478f3c526af6e43));
- `eZHTTPTool` copes with `$_POST` set to null (LoveStack
  [`75cbb1daa2`](https://github.com/mugoweb/ezpublish-legacy/commit/75cbb1daa2804b129f1a3b395dd44d79ebcf667b));
- the 5.4 update script `cleanupfieldvaluerelations.php` skips empty relation data, and
  `eZPostgreSQLDB::escapeString( null )` gives `''` (LoveStack
  [`1bd250d4c6`](https://github.com/mugoweb/ezpublish-legacy/commit/1bd250d4c6cdfeda4d99a44d56bc1cd801ebf935));
- a customer's orders are found from the shop's order lists, archive, customer list and
  dashboard when the e-mail address has a "+" (LoveStack
  [`5a17c08bfc`](https://github.com/mugoweb/ezpublish-legacy/commit/5a17c08bfc761df3fc0d9ce6d117f3cbc9cfc129)). A design of your own that links to
  `shop/customerorderview` can add `|rawurlencode` to the address in the same way.

The spell checker part of LoveStack
[`cb8a3a1c58`](https://github.com/mugoweb/ezpublish-legacy/commit/cb8a3a1c587c99676e8ffd94c2bd47b4d1562eb2) was already in Exponential and is now
kept by a test. Site by site, with the test of each: [PHP 8 review](love/PHP8-REVIEW.md).

---

## Moving a site from LoveStack

A LoveStack database is close to Exponential's. Two console commands do the work that is
specific to it:

```sh
./console exp:lovestack check            # read only: what the installation brings along, and what to do with it
./console exp:lovestack migrate          # dry run: prints what it would write
./console exp:lovestack migrate --apply  # records the database as Exponential's
```

- `check` lists the database version row `lovestack`, LoveStack's `db-schema-version` row, a
  unique index on `ezuser.login`, a `data_text NOT NULL DEFAULT ''` column, an active
  `legacy_2018` extension, LoveStack-only settings in `settings/override`, the siteaccesses and
  the extensions (with the Exponential name where there is one), and the
  `mugoweb/ezpublish-legacy` package in `composer.json`. Exit code 0: nothing to do; 1: something
  needs attention. `--json` for a machine-readable report.
- `migrate` sets `ezsite_data` `ezpublish-version` from `lovestack` to this release's version
  (`6.0.15stable`) and records LoveStack's schema version as `lovestack-schema-version`
  (LoveStack's own row stays). It then prints the settings and extensions to change by hand. It
  never changes content or settings, a second run changes nothing, and a database that does not
  come from LoveStack is refused with a message saying so.
- Tests: `kernel/classes/expLoveStackCheckTest.php` and `kernel/classes/expLoveStackMigrateTest.php`,
  on a LoveStack-shaped database built from Exponential's schema and LoveStack's schema steps 1
  to 4. The same content operations (publishing, new versions, adding, moving and removing
  locations, copying, a new class attribute on a class with objects, the trash) give the same
  content as on an Exponential database, so LoveStack's `data_text NOT NULL` column and unique
  login index can stay as they are.

The full path, from backups to your own code: [Moving a site from LoveStack](love/MIGRATION.md),
and the upgrade guide's [Coming from LoveStack](../../guides/upgrading.md#coming-from-lovestack).

---

## Already in Exponential

These LoveStack features were already in Exponential, the same or in Exponential's own way:

| LoveStack feature | In Exponential |
|---|---|
| SameSite session cookies (2025) | `site.ini [Session] CookieSameSite`, default `Lax`, `None` only with `Secure`; LoveStack's `SameSite` name is read too ([above](#lovestacks-session-samesite-setting-is-read-too)) |
| Passwords never silently stored as MD5 | PHP's default algorithm, `HashType=php_default` |
| Longer minimum and generated passwords | 10 and 16 characters |
| Increased password security (EZP-24744) | yes |
| The date an object was trashed, and sorting the trash by it (EZP-28881) | yes, same database column |
| WebP images | yes |
| SQLite databases | Exponential's own SQLite3 driver and schema handler |
| Log lines with siteaccess and URL | yes, also under the Velocity engine |
| Escaped values on the database error page | the page is rendered by `eZExecution::renderErrorPage()`, values escaped |
| `eZSiteData::create()` | yes |
| "Published version" in the object information | yes |
| Object states (EZP-25089), the rich text editor on PHP 7 | yes |
| Empty user draft values not stored (EZP-29096) | yes; a draft becomes an account only with a free login |
| Version details in the administration | Setup > System information and Info > About |
| PHP 8 support | PHP 8.1 to 8.5 |
| Dynamic property deprecations (PHP 8.2) | handled with `#[AllowDynamicProperties]` |
| Audit on by default | Exponential's own audit trail is on in every installation (`settings/audit.ini Audit=enabled`) |

---

## Not brought over, and why

Some of LoveStack's changes fit LoveStack's own way of working and were not needed in
Exponential, or Exponential already solves the same problem differently. None of this is a
judgement on the work; each item simply has a different answer here.

| LoveStack item | Why Exponential does it differently |
|---|---|
| The `legacy_2018` extension (moves `ezenum` out of the kernel, keeps YUI 2.7.0 and the old example designs) | Exponential keeps `ezenum` in the kernel and has replaced YUI with its own UI API. A site coming from LoveStack disables `legacy_2018` (two `eZEnumType` classes would collide) |
| `site.ini [HTTPHeaderSettings] SetContentLengthHeader` | Velocity, FrankenPHP and the HTTP cache already send `Content-Length` for what they serve, after compression |
| `update/run.php` and the `lovestack` schema counter | Exponential has its upgrade SQL per release in `update/database/<engine>/`; `exp:lovestack migrate` records a LoveStack database instead |
| Removing the third `eZSearch::search()` parameter `$searchTypes` | would change an API that third-party search plugins use |
| `eZPersistentObject` writing `''` for values that are not strings | Exponential's null-safe handling keeps numbers as numbers |
| Rewording of debug labels, constants as array keys | wording and style; Exponential changed the same files its own way |
| Removing the example siteaccesses | Exponential's installer manages its own siteaccesses |
| Old browser CSS, Travis, composer and README changes | specific to LoveStack's own packaging |
| The CSS optimiser's leading-newline trim | Exponential's packer works differently |
| JavaScript placeholder text replaced by HTML `placeholder` | covered by Exponential's YUI replacement |
| Font rewrite rule, Apache sample configuration | Exponential documents its own web server setup |
| The `data_text NOT NULL DEFAULT ''` column rebuild | a heavy change on the largest table that Exponential does not need; a database that already has it keeps working |
| ezjscore cache clearing by age | Exponential's cache tools (`exp:cache`) cover packed files |
| Accepting an author without an e-mail address (`ezauthor`) | a change of behaviour rather than a PHP 8 fix; for now Exponential keeps refusing it, as every earlier release did |

---

## How it was tested

Every feature above is covered in Exponential's PHPUnit suite. Each test file runs on its own,
and apart from a few tests marked as needing a database (which roll back what they write), they
run without one. The tests a change needs can be run alone: see
[run only the tests of a change](../../features/6.0/quality-checks.md#run-only-the-tests-of-a-change).
`kernel/classes/expShippedFeatureDefaultsTest.php` checks that an upgraded site keeps the
defaults this page names.

LoveStack's own `tests/` tree was compared with Exponential's too: every test case Mugo Web added
or changed since the common ancestor that covers behaviour Exponential has is in the suite,
rebuilt for PHPUnit and credited. The full list is in [Tests, feature by feature](love/TESTING.md)
and [LoveStack's tests in Exponential's suite](love/TESTS-MAPPING.md).

---

## Licence and credits

- LoveStack is licensed under the GNU GPL version 2 (its `LICENSE`, with the eZ Systems
  copyright; `composer.json`: `GPL-2.0`). Exponential is licensed under the GNU GPL version 2
  or later ([GPL-2.0-or-later](https://spdx.org/licenses/GPL-2.0-or-later.html)). Mugo Web's
  changes are under the same GPL version 2, so they may be combined with Exponential; the parts
  derived from them are distributed under the terms of GPL version 2.
- Every feature on this page is treated as derived from Mugo Web's work, even where it was
  rebuilt rather than copied. Each file with derived code keeps every existing copyright notice
  and adds the line
  `Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit <sha>), Copyright (C) Mugo Web, GPL v2`;
  the LoveStack commit is named in the commit message and in the
  [6.0.15 changelog](../../changelogs/6.0/6.0.15.md#lovestack-features-from-2026-10-12).
- The custom tag controls (L23) follow the design of LoveStack's `custom_tags_controls` editor
  plugin, "Copyright 2014, Mugo Web"; that notice moves with the derived parts.
- The dual-licence wording of the original eZ Systems README can only ever be granted by eZ
  Systems for eZ Systems' code; it gives no rights to Mugo Web's or Exponential's code.
- "LoveStack" is Mugo Web's name for its distribution. Exponential uses it only to describe where
  a feature comes from; no class, setting or product in Exponential carries it, apart from the
  `exp:lovestack` command for sites moving from it. Nothing here implies that Mugo Web endorses
  Exponential.

**Thank you to Mugo Web** for years of careful work on the legacy kernel, for documenting their
schema steps so precisely that a LoveStack database can be recognised and moved, and for
publishing it all as open source. This page exists because of that work.

---

## More documents

| Document | For |
|---|---|
| [love/README.md](love/README.md) | the index of the detailed pages |
| [love/GETTING_STARTED.md](love/GETTING_STARTED.md) | everyone: does this concern me, and where to start |
| [love/FEATURES.md](love/FEATURES.md) | every feature with its default on an upgrade and on a new installation, in one table per area |
| [love/API.md](love/API.md) | classes, methods, settings, template functions and console commands, for developers |
| [love/MIGRATION.md](love/MIGRATION.md) | moving a LoveStack site to Exponential, step by step |
| [love/TESTING.md](love/TESTING.md) | the tests of each feature and how to run them |
| [love/TESTS-MAPPING.md](love/TESTS-MAPPING.md) | LoveStack's own test cases and where they are in Exponential's suite |
| [love/PHP8-REVIEW.md](love/PHP8-REVIEW.md) | LoveStack's PHP 7.2 to 8.3 fixes, site by site |
| [love/L01-callers.md](love/L01-callers.md) | who calls `ezjscnode`, and what switching on L01 changes |
| [love/FAQ.md](love/FAQ.md) | common questions |
| [love/ROADMAP.md](love/ROADMAP.md) | how the work was done, and what comes next |
| [love/CHANGELOG.md](love/CHANGELOG.md) | the releases that carry these features |
