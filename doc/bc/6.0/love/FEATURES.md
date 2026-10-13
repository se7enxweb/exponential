# Features

Every LoveStack feature brought into Exponential 6.0.15, with what an **upgraded** installation
gets and what a **new** installation gets. All of them are available in 6.0.15. The
[overview](../lovestack.md#feature-by-feature) has the details of each: who it is for, its
settings, how to switch it on, its test and the LoveStack commit.

"On" means active without any setting; "off" means available, but inactive until you switch it
on.

## Security and sessions

| ID | Feature | For | Upgrade | New installation |
|---|---|---|---|---|
| [L01](../lovestack.md#l01--node-data-calls-can-require-a-policy) | The node data calls `ezjscnode::load`, `::subtree`, `::updatepriority` can require the policy *ezjscore / call* with the function `ezjscnode` (`RequirePolicy`) | administrators | off (`RequirePolicy=disabled`) | on; the Editor role gets the policy |
| [L02](../lovestack.md#l02--registration-can-require-a-policy) | Registering can require the *User / Register* policy (`PolicyOmitListExclude[]`); login pages show the register link only to those who may register | administrators | off (registration open, as before) | on (`PolicyOmitListExclude[]=user/register`) |
| [L03](../lovestack.md#l03--logging-out-ends-the-session-and-its-cookie) | Logging out ends the session and expires its cookie | everyone | on | on |
| [L04](../lovestack.md#l04--one-account-per-login) | One account per login: `exp:user-login-check` reports duplicates, `--fix-index` adds the unique index | administrators | report available; index added on request | index in the schema |
| [L05](../lovestack.md#l05--uploaded-images-are-checked-by-their-content) | Uploaded images checked by their content against `AllowedMIMETypes[]` (SVG not listed) | everyone | on | on |
| [L06](../lovestack.md#l06--package-thumbnails-are-checked-the-same-way) | The same check for package thumbnails and style package images | administrators | on | on |
| [SameSite](../lovestack.md#lovestacks-session-samesite-setting-is-read-too) | LoveStack's `[Session] SameSite` read when `CookieSameSite` is not set | administrators | on | on |

## Diagnostics and logging

| ID | Feature | For | Upgrade | New installation |
|---|---|---|---|---|
| [L07](../lovestack.md#l07--php-deprecations-get-their-own-log) | PHP deprecations get their own debug level and `var/log/deprecated.log`, counted, not kept in memory | administrators, developers | on (`DeprecatedLog=enabled`) | on |
| [L08](../lovestack.md#l08--log-file-size-in-siteini) | Log file size and rotation in `site.ini [FileSettings]` | administrators | 2 MB, 3 files | 2 MB, 3 files |
| [L09](../lovestack.md#l09--a-warning-for-an-operator-parameter-without-a-default) | A warning names the template operator and parameter without a default value | developers | on | on |
| [L10](../lovestack.md#l10--edit-links-in-the-list-of-used-templates) | The edit link format of the debug output's list of used templates (`UsedTemplateEditUrl`) | developers | the usual link | the usual link |
| [L11](../lovestack.md#l11--database-errors-carry-the-drivers-error-number) | Database exceptions carry the driver's error number | developers | on | on |
| [L12](../lovestack.md#l12--a-warning-when-a-change-clears-every-view-cache) | A warning when the cache threshold turns a clear into "clear all" | administrators | on | on |

## Content, datatypes and the administration

| ID | Feature | For | Upgrade | New installation |
|---|---|---|---|---|
| [L13](../lovestack.md#l13--audit-records-for-location-changes) | Audit events for adding, removing and changing the main location | administrators | remove on (as before); add and main change off | the same |
| [L14](../lovestack.md#l14--site-data-in-templates-and-in-the-administration) | Site data in templates for listed names; Setup > Site data | developers, administrators | nothing listed; admin list there | the same |
| [L15](../lovestack.md#l15--a-copy-button-on-the-admin-full-view) | A Copy button on the admin full view | editors | on | on |
| [L16](../lovestack.md#l16--required-alternative-text-for-image-fields) | Alternative text can be required per image attribute | editors, administrators | off for every class | off for every class |
| [L17](../lovestack.md#l17--trim-the-stored-value-for-text-line-fields) | A text line attribute can trim its stored value | editors, administrators | off for every class | off for every class |
| [L18](../lovestack.md#l18--names-made-by-a-php-class) | A PHP class can make object names and URL alias names (`{MyResolver}`) | developers | off until a class is listed | the same |
| [L19](../lovestack.md#l19--class-filter-default-for-fetches) | Class filter type default for fetches (`DefaultClassFilterType`) | developers | empty (as before) | empty |
| [L20](../lovestack.md#l20--the-locations-window-keeps-a-hidden-location-hidden) | The Locations window no longer forces "Visible" | editors | on | on |
| [L21](../lovestack.md#l21--selection-and-matrix-fields-without-stray-line-breaks) | Selection and matrix values without stray line breaks | designers | on | on |

## Rich text editor

| ID | Feature | For | Upgrade | New installation |
|---|---|---|---|---|
| [L22](../lovestack.md#l22--hidden-custom-tags) | Custom tags that exist but are not offered for new tags (`IsHidden[]`) | administrators, editors | none hidden | none hidden |
| [L23](../lovestack.md#l23--custom-tag-controls-in-the-rich-text-editor) | Controls on a focused custom tag (move, edit, remove, keys), labels and previews (`CustomTagControls`) | editors | off | on |
| [L24](../lovestack.md#l24--the-subsup-dialog-closes-on-ok) | The sub/sup custom tag dialog closes on OK | editors | on (was already the case) | on |

## Developer APIs and settings

| ID | Feature | For | Upgrade | New installation |
|---|---|---|---|---|
| [L25](../lovestack.md#l25--ezuribuild-builds-a-site-url-in-one-call) | `eZURI::build()` | developers | available | available |
| [L26](../lovestack.md#l26--one-message-with-another-mail-transport) | `eZMailTransport::send( $mail, $transportType )` | developers | available | available |
| [L27](../lovestack.md#l27--rawurlencode-and-rawurldecode-template-operators) | `rawurlencode` and `rawurldecode` template operators | developers | available | available |
| [L28](../lovestack.md#l28--include--inside-settings-files) | `[Include <pattern>]` inside settings files | administrators, developers | only where a file has such a line | the same |
| [L29](../lovestack.md#l29--response-headers-for-error-pages) | Response headers per HTTP error page (`HeaderList[]`), also from the view cache | administrators | none set | none set |
| [L30](../lovestack.md#l30--the-order-of-search-results-in-the-editors-dialogs) | The ezjscore search sorts by `ezfind.ini [SearchSettings] SortBy[]` | administrators | the same order as before | the same |
| [L31](../lovestack.md#l31--templates-can-tell-layout-views-apart) | The `layout` design key for node views; `setKeyValue()` | designers, developers | on | on |

## URLs and search

| ID | Feature | For | Upgrade | New installation |
|---|---|---|---|---|
| [L32](../lovestack.md#l32--request-addresses-decoded-once) | Request addresses decoded once (`DecodeOnce`) | administrators | off | off |
| [L33](../lovestack.md#l33--the-double-quote-in-iri-url-aliases) | The double quote cleaned from IRI URL aliases (`TrailingCleanup`) | administrators | off | off |
| [L34](../lovestack.md#l34--very-large-numbers-no-longer-stop-the-search-index) | The built-in search clamps integers to the column range | administrators | on | on |
| [L35](../lovestack.md#l35--relations-of-related-objects-stay-out-of-the-search-index) | Relation fields of related objects left out of the search index | administrators | on (Exponential's behaviour since 2018) | on |
| [L36](../lovestack.md#l36--the-hide-cronjob-and-the-search-index) | The hide cronjob updates the search index; the `content/cache` event carries object ids | administrators, developers | on | on |

## PHP 8 review

LoveStack's fixes for PHP 7.2 to 8.3 were reproduced one by one on PHP 8.5 and 8.1, each as a
regression test. Most were already handled in Exponential; the ones that still failed are fixed
for every site. Site by site: [PHP8-REVIEW.md](PHP8-REVIEW.md).

## Already in Exponential, and not brought over

See the overview: [Already in Exponential](../lovestack.md#already-in-exponential) and
[Not brought over, and why](../lovestack.md#not-brought-over-and-why).
