# The Exp Debug bar

Read this page if you use the debug report, if you restrict debug output by IP address, or if your scripts or
stylesheets work with the classic report. From Exponential 6.0.15 the debug report at the bottom of a page is the
**Exp Debug bar**: a pinned bar with a live summary of the request and a panel with tabs over the report's sections.
Its Settings tab shows every debug setting, the value in effect and the file it comes from, and changes it in the
scope you choose (global override, a siteaccess or an extension). Every change is written through the exp:ini engine
([console-exp-ini.md](console-exp-ini.md)), logged, and can be undone from the bar.

## In short

| | |
|---|---|
| What changed | The HTML debug report is the Exp Debug bar (`lib/ezutils/classes/expdebugbarreport.php`). New registry `settings/debugbar.ini`, server functions `expdebugbar::*`, write log `var/<site>/log/debugbar.log`. `DebugIPList[]` entries may carry a label and an expiry, and CIDR ranges of both address families match. |
| Who is affected | Sites that list a **plain IPv6 address** in `DebugIPList[]`: before, any IPv6 client got debug output as soon as the list had one; now that entry means that one address. Users who change debug settings from the bar: they need `setup/setup` (cache controls: `setup/managecache`). Custom CSS or tests that match the old heading text (it now reads "Exp Debug"). |
| How to check | `grep -n "Settings\[\]" settings/debugbar.ini \| head` and `php vendor/bin/phpunit tests/tests/kernel/classes/debugbar/` |
| How to fix | Add every IPv6 address or network that needs debug output to `DebugIPList[]` explicitly. Give `setup/setup` to roles that debug. Section ids are unchanged, so scripts written for the classic report keep working. |

Text reports (`Debug=inline` on the command line) and the popup window are unchanged. An engine archive without
`expdebugbarreport.php` prints the classic report.

This page describes the server side first (the registry, values and scopes, writes, the log and undo, the IP list,
the summary, the server functions, access and the RAD survey), then [the bar](#the-bar) itself.

| Part | Where |
|---|---|
| Registry | `settings/debugbar.ini`, and `extension/<ext>/settings/debugbar.ini.append.php` |
| Settings registry | `expDebugBarRegistry` (`kernel/classes/debugbar/expdebugbarregistry.php`) |
| Values, writes, undo, presets | `expDebugBarSettings` (`kernel/classes/debugbar/expdebugbarsettings.php`) |
| IP list engine | `expDebugBarIPList` (`kernel/classes/debugbar/expdebugbariplist.php`), used by `eZDebug::isAllowedByCurrentIP()` |
| Write log | `expDebugBarLog` (`kernel/classes/debugbar/expdebugbarlog.php`), `var/<site>/log/debugbar.log` |
| Summary | `expDebugBarSummary` (`kernel/classes/debugbar/expdebugbarsummary.php`) |
| Server functions | `expDebugBarServerFunctions`, `[ezjscServer_expdebugbar]` in `extension/ezjscore/settings/ezjscore.ini` |
| Tests | `tests/tests/kernel/classes/debugbar/` |

## The settings

`settings/debugbar.ini` lists the settings in `[DebugBarSettings] Settings[]`, and describes each in a block of
its own:

```ini
[Setting_debug_output]
File=site.ini
Block=DebugSettings
Variable=DebugOutput
Type=bool
Label=Debug output
Group=output
Help=Shows the debug report (this bar) at the bottom of every page for those who get debug.
```

| Key | |
|---|---|
| `File`, `Block`, `Variable` | the setting |
| `Type` | `bool`, `enum` (with `Values[]`), `list` (optionally `Values[]`: what may be added), `text`, `iplist`, `userlist` |
| `Label`, `Group`, `Help` | what the bar shows; `Group` is one of `Groups[]` (`GroupNames[<id>]` names it) |
| `On`, `Off` | bool only: the words written. Without them, the words of the value in effect are used (`enabled`/`disabled`, `true`/`false`, keeping `Enabled` or `TRUE`) |

The kernel registers 35 settings in nine groups: debug output (DebugOutput, Debug inline/popup, DebugRedirection,
DisplayDebugWarnings, DebugLogOnly, DisplayIncludedFiles, AlwaysLog[], ScriptDebugOutput, DebugToolbar, REST
Debug), who gets debug (DebugByIP, DebugIPList[], DebugByUser, DebugUserIDList[]), templates (Debug,
ShowXHTMLCode, ShowUsedTemplates, ShowTemplatePathComments, ShowMethodDebug, DevelopmentMode, TemplateCompile,
TemplateCache, CompileAccumulators, CompileTimingPoints, CompileComments), database (SQLOutput,
QueryAnalysisOutput, SlowQueriesOutput, DebugTransactions), translations (RegionalSettings Debug and
DevelopmentMode), mail (DebugSending, DebugReceiverEmail), debug conditions (debug.ini ConditionDebug), caching
(ViewCaching).

Two kinds of settings are found rather than registered:

- **`DiscoverBlocks[]=<file>;<block>;<group>`**: every bool variable of the block, as it is in effect. The kernel
  lists `debug.ini;GeneralCondition;conditions`, so every debug condition (`kernel-content-view`,
  `kernel-urltranslator`, ... and the ones extensions add) is a switch, with the id `cond_<variable>`.
- **Extension debug switches** (`ExtensionDiscovery=enabled`): in every active extension's `settings/*.ini`,
  `*.ini.append.php` and `*.ini.append`, a variable whose name matches `DiscoveryPattern` (`/debug/i`) and whose
  value is a bool word. Its id is `ext_<ext>_<file>_<block>_<variable>`, its group `extensions`.
  `DiscoveryExclude[]=<ext>` or `<ext>;<file>` leaves some out. A setting the registry has already is not found
  twice.

On a reference installation the bar has 82 settings: 35 registered, 43 debug conditions, 4 extension switches
(cjw_newsletter, owsimpleoperator, bccie).

### An extension's settings

An extension registers its own in `extension/<ext>/settings/debugbar.ini.append.php`; eZINI merges it into the
kernel file, and the setting is marked as the extension's:

```ini
<?php /* #?ini charset="utf-8"?
[DebugBarSettings]
Settings[]=myext_trace

[Setting_myext_trace]
File=myext.ini
Block=TraceSettings
Variable=Trace
Type=bool
Label=Trace calls
Group=extensions
Help=Writes every call of the myext API to var/log/myext.log.
*/ ?>
```

### Presets

`Presets[]` names `[Preset_<id>]` blocks: `Name`, `Description`, `Values[<setting id>]=<value>` (for a bool `on`
or `off`). The kernel has **Template work**, **SQL tuning**, **Everything** and **Off**. A user saves presets of
their own from the bar (stored in their preferences, `expdebugbar_presets`), either with values given or as a
snapshot of the bool and enum settings in effect.

Applying a preset writes each value that differs from the one in effect as a write of its own, all in one group,
so the group can be undone at once. If one of the writes needs a confirmation (below), nothing is written.

## Values in effect, origin and scopes

For a siteaccess (the current one by default), `expDebugBarSettings::describe()` gives:

- **effective**: eZINI's own value, from an uncached eZINI with that siteaccess's override directories
  (`expIniLocator::iniFor()`), so a write is seen at once, before the ini cache is cleared;
- **files**: every file of the load order that sets the variable, with its scope;
- **origin**: the last of them, the file whose value wins;
- **default**: the value of `settings/<file>.ini`;
- **scopes**: the value each scope's file gives (`null`: it does not set it).

The scopes the bar offers are `global` (`settings/override`), every siteaccess, every active extension, and the
extension siteaccess directories of the siteaccess. The default scope is the siteaccess. The shipped defaults
(`settings/*.ini`) are never written. Describing all 82 settings reads each INI file once and takes about 140 ms.

## Writes

`expDebugBarSettings::write( $id, $op, $value, $scope, $options )`:

| op | for | does |
|---|---|---|
| `set` | bool, enum, text | bool takes `on/off/1/0/true/false/enabled/disabled/yes/no` and writes the setting's own words |
| `toggle` | bool | flips the value in effect |
| `unset` | all | removes the variable from that scope's file (the next file in the load order then wins) |
| `add`, `remove` | lists | one value; for an IP list `remove` takes the address and finds the line with its label |
| `replace` | lists | `Var[]` (the reset line) and the values |

The editor (`expIniEditor`) changes only the lines of the setting, keeps the file's owner, group and mode, writes
atomically and copies the old file to `var/backup/ini/<stamp>/<path>` first. After the write the ini cache tag is
cleared. PHP-FPM sees the change on its next request; Velocity's workers keep the settings of their warm-up and see
it after `exp:velocity restart`, which the answer says.

`dry_run` returns the diff and writes nothing. A value that the editor refuses (a line break, `##`, `*/` in a PHP
wrapped file) is refused with its reason.

### Never locking yourself out

A write to `DebugByIP`, `DebugIPList[]`, `DebugByUser`, `DebugUserIDList[]` or `DebugOutput` is checked against
the values in effect **after** it, computed by replaying the load order with the new file. It is refused with
`needs_confirm` when:

- **lockout**: DebugByIP would be enabled and the list would not include the current request's address (an
  expired entry does not count), while it does get debug output now; or DebugByUser would be enabled and the user
  list would not include the current user;
- **open**: the list would get an active `/0` entry with DebugByIP on, or DebugByIP would be switched off while
  DebugOutput is on and DebugByUser off, so every visitor would get debug output.

Sent again with `confirm=1`, the write goes ahead.

## The IP list

`DebugIPList[]` entries keep their plain format and may carry a label and an expiry:

```ini
DebugIPList[]=203.0.113.7
DebugIPList[]=10.0.0.0/8
DebugIPList[]=2001:db8::/32 ; VPN
DebugIPList[]=203.0.113.7/32 ; Laptop ; expires=2026-10-02T15:00
DebugIPList[]=commandline
```

- Addresses and networks of both families, CIDR prefixes at any bit (`198.51.100.64/26`,
  `2001:db8:abcd:12::/63`). A client that a dual-stack server reports as `::ffff:203.0.113.9` matches IPv4 entries.
- A label is any text without `;`, `##`, `*/` or a line break (`label=` before it is optional).
- `expires=` takes `YYYY-MM-DD`, `YYYY-MM-DDTHH:MM[:SS]`, with `Z` or an offset; without one, the server's time
  zone. The bar writes `+1h`, `+30m`, `+2d`, `today` (23:59) and `tomorrow` as that date. An expired entry no
  longer matches, and stays in the list until someone removes it. An expiry that cannot be read never matches.

`eZDebug::isAllowedByCurrentIP()` asks `expDebugBarIPList::match()` whenever the class is loadable, so the bar's
test and the real check are the same code, and keeps the entry that matched in `$GLOBALS['eZDebugIPMatch']` for
the bar. A process that cannot load the class (a Velocity worker started before the class existed) takes the
address part of each line and leaves expired entries out, so labels and expiries never break the old check.

One behaviour changed with the engine: the old check let **every** IPv6 client in as soon as the list had any
plain IPv6 address (it compared the entry with itself). A plain IPv6 entry now means that one address.

`expdebugbar::iptest` shows, for an address (default: the request's), whether the list lets it in, which entry
matched, every entry parsed, the address as the server sees it (`REMOTE_ADDR`) and as the kernel takes it
(`eZSys::clientIP()`, which reads `ClientIpByCustomHTTPHeader` such as X-Forwarded-For only when that setting is
set), the entries to suggest (`/32` and `/24`, `/128` and `/64`), and warnings: `invalid`, `expired`, `open` (a
`/0` entry), `empty` (no active entry) and `lockout` (the list does not include the address).

## The write log and undo

Every write is one JSON line in `var/<site>/log/debugbar.log` (`LogFile` in debugbar.ini): id, time, user id,
login, address, op, setting, file, block, variable, scope, path, the value written, the value in that file
before and after, the value in effect before and after, the backup, what the write created (file, block,
variable), and the sha1 of the file before and after. Cache clearing from the bar is logged too (op `cache`).
A log the bar creates gets the owner and group of the log directory and mode 0640.

`undo( <entry id> )` applies the inverse through the editor: the old value set again, the variable removed again,
a list given back in its old order, an added value removed, and a block the write created removed when it is
empty. The answer says whether the file is now byte for byte its backup (`byte_identical`), which it is whenever
the writes after it were undone first. An undo is refused when the setting in that file is no longer what the
write left (another write changed it; `force=1` reverts it anyway), when the entry was undone already, or when it
changed a secret (the log has no secret values). The undo is logged with `undoes` naming the entry.
`undoGroup( <group> )` undoes a preset, newest write first.

## The summary

`expDebugBarSummary::collect()` gives what eZDebug measured: the page time, the SQL statements and their time (the
`*_query` accumulators of the database drivers, or eZDB's counter), the peak memory and the limit, the templates
used, messages per level, included files, the accumulators and the timing points, who gets debug and the IP entry
that matched, the engine (Velocity or the SAPI), the siteaccess, and the SQL query cache of the request
(`querycache`: `eZDBQueryCache::debugSnapshot()` with the stale statements replaced by their number, `stale_count`;
null while the cache is off). Every figure has a level, `ok`, `warn`
or `high`, against `Thresholds[]` in debugbar.ini (`time_ms`, `sql_count`, `sql_ms`, `memory_mb`, `templates`, each
`<warn>;<high>`; `querycache_hit_rate` is a hit rate in percent and turns the level up **below** its values, see
`expDebugBarReport::queryCacheLevel()`, the one place of these rules: `expDebugBarSummary::queryCache()` and the bar's own fallback both build the figures with `expDebugBarReport::queryCacheFigures()`). `expDebugBarSummary::json()` is the same as JSON that is safe inside a `<script>` element.

## Server functions

`/ezjscore/call/expdebugbar::<function>` (with `?ContentType=json`). ezjscore answers
`{"error_text": "", "content": ...}`; a refusal is in `error_text`.

| Function | Request | Needs |
|---|---|---|
| `settings[::<siteaccess>]` | GET | setup/setup (nothing about the configuration goes to anybody else) |
| `set` | POST `setting`, `op`, `value`, `scope` [`siteaccess`, `confirm`, `dry_run`] | setup/setup |
| `undo` | POST `entry` or `group` [`force`] | setup/setup |
| `preset` | POST `preset` + `scope` (apply), `save` [+ `values` JSON or `snapshot=1`], or `delete` | setup/setup |
| `cache` | GET `action=list`; POST `action=clear`, `by=all\|tag\|id\|node\|velocity\|opcache\|querycache_counters`, `names`, `node_id` | setup/managecache (list and clear) |
| `iptest` | `address`, `list` (JSON array) | setup/setup |
| `log[::<limit>]` | GET | setup/setup |
| `summary` | GET | debug output for the request (the report itself; a visitor gets no IP list entry in it) |
| `panelheight` | POST `height` (pixels, `0` for the default) | a logged-in user (never an anonymous visitor) with debug output for the request: stores the user preference `expdebugbar_panel_height` (see [The panel's height](#the-panels-height)) |

Every POST must carry the form token (`ezxform_token`, or the header `X-CSRF-Token`); the `settings` answer has
it. Someone who only sees the debug report gets the settings without the IP and user lists, the log or the
cache list: who gets debug output and who changed what are for those who may change them. Secret values are
masked as in exp:ini.

`cache` with `by=node` clears the view cache of one node, the page the bar is on
(`eZContentCacheManager::clearNodeViewCacheArray()`); `velocity` clears Velocity's response cache; `opcache`
resets the OPcache of the process that answers; `querycache_counters` zeroes this server's query cache counters (the
cached results themselves are the cache id `querycache`). The list gives every cache with how it is cleared
(`expCacheManager`), the tags, when each was last cleared from the bar, Velocity's cache status, the OPcache state
and the SQL query cache (`querycache`: `eZDBQueryCache::inventory()`, settings, this server's counters, the APCu
memory, the entries grouped by the tables they read with rows, size and hits, and the largest entries; an entry
carries no statement text, only the start of its key). The inventory reads the entries of the cache in APCu with their rows (at
most 5,000 and for at most one second; it says when it stopped early), so it runs only when the Cache tab is opened.
The summary (`expdebugbar::summary`, the page's JSON) carries only the number of writes that made everything stale
(`stale_count`); the statements are in the report of the SQL tab. `inventory()` leaves out the state file's path itself. After a clear or a reset of the counters the section is read again. The query cache is no longer listed a second time under "By ID".

Example (replace the host names and YOUR_USER:YOUR_PASSWORD with your own; the second call is made without signing in):

```
$ curl -s -u YOUR_USER:YOUR_PASSWORD 'https://admin.example.com/ezjscore/call/expdebugbar::iptest?ContentType=json'
{"error_text":"","content":{"address":"...","valid":true,"family":4,"remote_addr":"...","client_ip":"...",
 "trusts_proxy":false,"proxy_header":null,"suggest":{"self":".../32","network":".../24","family":4},
 "debug_by_ip":false,"allowed":false,"matched":null,...}}
$ curl -s 'https://www.example.com/ezjscore/call/expdebugbar::log?ContentType=json'
{"error_text":"This needs the policy setup\/setup or setup\/managecache","content":""}
```

## RAD

The extension point survey (`setup/radsurvey`) reads two registries out of debugbar.ini: **Debug bar settings**
(every `[Setting_<id>]`, checked for a valid `Type`) and **Debug bar presets** (every `[Preset_<id>]`, checked
for a name), kernel and extension files alike. Their entries name no class, so they are added to the total once.
The survey also carries the bar's own figures, which are not added again: `debugbar_settings`,
`debugbar_registered`, `debugbar_extension_registered`, `debugbar_discovered`, `debugbar_presets`,
`debugbar_problems` (`expRADSurvey::debugBar()`, which is `expDebugBarRegistry::survey()`).

## Tests

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/debugbar/
```

| Test | Covers |
|---|---|
| `expDebugBarReportPanelHeightTest` (`tests/tests/lib/ezutils/`) | the panel height: the preference name and limits, `panelHeight()` on whole numbers, text, fractions, negatives and values out of range, `savedPanelHeight()` with a reader passed in and never asking it for an anonymous visitor, the data attributes on the rendered panel (no `style` attribute), the handle's styles and keys, the server function's checks. No database |
| `expDebugBarIPListTest` | addresses and CIDR of both families, IPv4-mapped clients, entries with labels and expiries, writing entries, the match, the warnings, the suggestions. Needs no kernel |
| `expDebugBarSettingsTest` | the registry and discovery, the values in effect against `expIniLocator::where()`, writes of every type and op with the log and undo, dry runs, refusals, a siteaccess scope, presets as a group, the lock-out and open checks, eZDebug's IP check and its fallback, the summary, the server functions (registration, answers, POST, token and policy, what a visitor without policy sees) |
| `RadSurveyDebugBarTest` | the two registries, the total, the bar's figures, the new problem kinds |

`expDebugBarSettingsTest` starts the kernel once on the admin siteaccess with the installation's own database, as
the subitems column tests do; no test database is created. Its writes go to a probe block
(`[ExpDebugBarTestProbe]` of site.ini) in `settings/override` and `settings/siteaccess/admin`, with the ini cache
left alone, and every test undoes its writes and checks the files byte for byte against their state before the
test (and puts them back if they differ). Its log is a file of its own under `var/tmp/debugbar-test/`.

## The bar

| Part | Where |
|---|---|
| Report markup, summary, data block | `expDebugBarReport` (`lib/ezutils/classes/expdebugbarreport.php`), called by `eZDebug::printReportInternal()` for HTML reports |
| Tabs, filter, settings, IP list, presets, cache, change log | `design/standard/javascript/expdebugbar.js` |
| Styles (light and dark) | `design/standard/stylesheets/expdebugbar.css` |
| Words | context `design/standard/debugbar` in `share/translations/*/translation.ts` (eng-US, ger-DE) |

The report links the script and the stylesheet itself, so every design gets the bar: admin, admin2, admin3, admin4
and the public designs, with or without `debug.css`. The links are plain paths, with no `?v=` or other version
query (owner rule); after a change to one of the files a browser that still shows the old one is reloaded by hand.
Text reports (`Debug=inline` on the command line) and the popup window are unchanged. Without
`expdebugbarreport.php` (an older engine archive) `eZDebug` prints the classic report.

### No inline CSS

Neither report prints a `<style>` element or a `style=""` attribute (AGENTS.md "Admin design and template styling
rules"); the open and close of the pinned bar stay an inline script.

- **The bar**: every rule, the pinned bar and the panel above it included, is in `expdebugbar.css` and takes its
  colours from the bar's `--xd-*` tokens, each with a value for `prefers-color-scheme: dark` and for admin4's
  `html[data-a4-theme="dark"]`. The report links the stylesheet *before* the bar, so the browser has the rules
  before it draws the bar; no pre-paint rule is needed. The two heights the script measures or restores at run time
  are custom properties on the root element (`--exp-debug-bar-h`, `--exp-debug-panel-h`), not style attributes in
  the markup.
- **The classic report**: its rules are in `debug.css`, which the report links itself (or the design loads, with
  `eZDebug::setUseExternalCSS( true )`), scoped to `#debug:not([data-exp-debug-bar])` with a small copy of the
  tokens. Its bar follows the colour scheme; its panel keeps the light rows of the classic table rules in both.
- **The tables of both**: the figures of the timing points and the time accumulators are `td.num` (right aligned in
  equal digits), the time of a message heading `td.debugtime`, the heading `td.debuglabel`, an accumulator group's
  name `td.groupname`, and the count rows of the included files and the template usage `tr.summary`; no `<b>` or
  `<i>`.
- `eZDebug::$UseDebugBar` (default true) set to false prints the classic HTML report; the test below uses it.

`expDebugReportNoInlineCssTest` (`tests/tests/lib/ezutils/`, no database) renders the bar and the classic report
with messages, timing points and accumulators, and fails on any `style=""` attribute, any `<style>` element and any
asset URL with a `?v=` query; it also checks the query cache report's source, the plain-text report, and that every
`--xd-*` token has its dark values.

### The pinned bar

The bar stays at the bottom of the window wherever the page is scrolled; a click on "Exp Debug" opens the panel
above it, which scrolls on its own. No page ever has to be scrolled to use it. "Keep open on reload" remembers the
open panel in the browser (`localStorage` keys `exp-debug-keep`, `exp-debug-open`), and the last tab
(`exp-debug-tab`). Escape closes the panel.

### The panel's height

The open panel is full width, so only its height changes. The strip on its top edge (the edge facing the page, the
panel opens upward from the bar) is a handle: drag it with the mouse, a pen or a finger, or focus it (it is a
`role="separator"` with `aria-valuenow`, `aria-valuemin` and `aria-valuemax`) and use the keys:

| Key | Does |
|---|---|
| Arrow up / down | 16 px higher / lower |
| Shift + arrow up / down | 64 px higher / lower |
| Home / End | the lowest / the highest the window allows |
| Double-click on the handle | back to the default height (60 % of the window) |

The height stays between 120 px and the window's height minus the pinned bar, also when the window changes size (a
height kept from a taller window comes back when the window is tall enough again). The stylesheet holds the same
limit (`height: min(var(--exp-debug-panel-h, 60vh), calc(100vh - var(--exp-debug-bar-h)))`), so a saved height never
pushes the panel's top out of the window.

The height is saved once at the end of a drag, or 0.6 s after the last key press:

- **Logged in**: in the user preference `expdebugbar_panel_height` (eZPreferences, through
  `expdebugbar::panelheight`), so it follows the user to any browser. `0` means the default height. The report reads
  it back (`expDebugBarReport::savedPanelHeight()`) and prints it on the panel as `data-exp-debug-panel-height`; the
  report's inline script sets `--exp-debug-panel-h` from it before the panel opens, so it opens at that height
  without a jump.
- **Anonymous** (the bar shown by IP): no preference is read or saved and no request is sent, since eZPreferences
  keep an anonymous user's values in the session and the debug bar must never start a session
  (`savedPanelHeight()` returns 0 without asking, and `expdebugbar::panelheight` refuses an anonymous visitor
  before the token check). The height is kept in the browser instead: `localStorage` key `exp-debug-panel-height`,
  every access in `try`/`catch` (blocked storage means the default height), read back and held between 120 and
  4000 px by the report's inline script before the panel opens; anything but a whole number of pixels is ignored.
  It stays in that browser only; another browser or a private window starts at the default.
  On a siteaccess that requires a sign-in (the admin), `ezjscore/call` needs a signed-in user since 6.0.15, so on
  an anonymous admin page (the sign-in page) the panels that ask the server (cache, log, summary) show "Could not
  load"; the panel height works as before, since it sends no request.

A logged-in user's preference is authoritative: localStorage is not looked at for a logged-in user. The panel says
which applies in `data-exp-debug-panel-store` (`preference` or `browser`).

A page that carries the debug report is never kept by the page cache (`X-Exp-Cache: BYPASS (debug report)`, see
[What is never cached](httpcache.md#what-is-never-cached)): the report and the saved height are rendered for each
request. Before 2026-10-14 a signed-in user's front page came from that cache with the report of an earlier request in
it, so a reload showed the height from before the last resize.

A stored value is a whole number of pixels: `expDebugBarReport::panelHeight()` turns anything else (text, a
fraction, a negative number, a unit) into "not a height" (the server function refuses it, the report uses the
default) and holds a number between 120 and 4000 (`PANEL_HEIGHT_MIN`, `PANEL_HEIGHT_MAX`).

The summary in the bar shows the request at a glance; each value opens its tab:

| Value | Tab | Amber / red from |
|---|---|---|
| page time | Timing | `[DebugBarSettings] Thresholds[time_ms]` (default 1 s / 3 s without the server side) |
| SQL count / time | SQL | `Thresholds[sql_count]`, `Thresholds[sql_ms]` |
| peak memory | Memory | `Thresholds[memory_mb]` (else 50 % / 80 % of `memory_limit`) |
| templates used (only with `ShowUsedTemplates`) | Templates | `Thresholds[templates]` |
| SQL query cache (`QC 83 % · -186 ms (186 %)`: hit rate, database time saved, and that time in percent of the page's time; only while the cache is on) | SQL | `Thresholds[querycache_hit_rate]` (amber below 50 %, red below 10 %; amber when a write made everything stale, red in `shared` without APCu) |
| warnings, errors | Messages (filtered to that level) | any warning is amber, any error red |
| deprecations (`3 depr`) | Messages | any deprecation is amber |

PHP deprecations are only counted in the bar: with `site.ini [DebugSettings] DeprecatedLog=enabled` (the default)
each distinct message is written once per request to `var/log/deprecated.log`, and none is kept in memory or listed
in the Messages tab. They are still part of the warning count of the page (the "Some general warnings occurred" box
of `DisplayDebugWarnings`). The Settings tab switches `DeprecatedLog` ("Deprecations to deprecated.log"); off,
deprecations are warnings again, listed one by one. See
[Deprecations and log sizes](../../guides/operating-a-site.md#php-deprecations-and-log-file-sizes).

The levels come from `expDebugBarSummary` when it is loaded, so the thresholds in `debugbar.ini` apply; otherwise
the report's own defaults.

### The tabs

| Tab | Shows |
|---|---|
| Messages | the notices, warnings, errors, debug and timing messages, with buttons per level; the SQL statements are moved to the SQL tab |
| Settings | the settings UI below, and under "Classic controls" the classic toolbar (`design:setup/debug_toolbar.tpl`, or a site's override of it, rendered as before) |
| Cache | the cache controls below; without the server functions a form that clears this page, content, template, INI or all caches through `setup/cachetoolbar` |
| Timing | the timing points and the time accumulators, as before (`#timingpoints`, `#timeaccumulators`) |
| SQL | number of queries, time, share of the page, average, the database's accumulators, and the statements (`SQLOutput`) with "Slowest first" |
| Templates | the templates used (`ShowUsedTemplates`) and the CSS/JS packer report, or how to turn them on |
| Included files | the included PHP files (`DisplayIncludedFiles`), or their number and how to list them |
| Memory | the classic "Main resources" table, memory now, peak, limit and share, and the five steps that took the most |
| Velocity | where the request entered and which process rendered the report, in one sentence (see [below](#velocity-where-the-request-entered-and-what-rendered-it)), the facts behind it, OPcache status, and Velocity's response cache |
| Other | reports appended by extensions with `eZDebug::appendBottomReport()` that belong to no tab (only when there are any) |

Every section keeps its id, so scripts and stylesheets written for the classic report still find it.

The filter box above the tabs filters the rows of every tab at once (messages, timing rows, accumulators,
statements, files, settings, caches); a tab with no match is dimmed and says so. Escape in the box clears it.

### Settings

Drawn from `expdebugbar::settings` when the tab is first opened. Per group (Debug output, Who gets debug,
Templates, Database, ... and the extension switches), each setting shows its name, file, block and variable, its
help, a control for its type (switch, list of values, text, one entry per line), the value in effect and where it
comes from (default, global override, siteaccess, extension; the file is in the tooltip), and a "Write to" picker
with every writable scope and the value that scope's file gives. Apply writes the value to the chosen scope,
Reset removes the variable from that scope. A write that would lock you out or open debug to everyone asks first.

"siteaccess" at the top changes whose values in effect are shown. Presets (Template work, SQL tuning, Everything,
Off, and the user's own) are applied to the scope picked next to them and can be reverted at once; "Save current
as preset" stores the values in effect under a name.

The change log lists the last writes (who, which setting, old and new value, scope) with Undo; an undo that finds
the file changed since asks before forcing it. Without setup/setup the Settings tab holds only the note "Sign in with setup access to change debug settings" (no values, no IP or user lists, no log, no file paths); without setup/managecache the Cache tab holds only the note "Sign in with cache access to manage caches".

### Who gets debug: the IP list and the user list

The IP list (`DebugIPList[]`) shows the address the server sees for this request (and REMOTE_ADDR when the site
trusts a proxy header), the entry this request matched, and each entry with its label, expiry and whether it
matched. "Add my IPv4 address" / "Add my IPv6 address" and "Add my /24" / "Add my /64" add the request's own
address or network; the CIDR field takes IPv4 and IPv6 addresses and ranges and says at once what is wrong
(prefix over 32 or 128, malformed address, already listed); each entry gets a label and an expiry (1 hour, today,
until removed). "Test an address" asks the server whether an address would match. Warnings say when the list
would lock you out, is empty while "Debug by IP" is on, or opens debug to every address.

The user list (`DebugUserIDList[]`) takes one user ID per line, "Add me" adds the signed-in user.

### Cache

Drawn from `expdebugbar::cache`: "This page only" (the view cache of the node the page shows), Velocity's
response cache, OPcache of the serving process, every cache tag and every cache ID with its description and when
it was last cleared, and all caches. Each clear says what it did.

### Velocity: where the request entered and what rendered it

The tab opens with one sentence that says where the request entered and which process rendered the report, then
the facts behind it (`expDebugBarReport::engineFacts()`, from the server variables and `engineProbe()`):

| Rendered by | Sentence (example) |
|---|---|
| a Velocity worker | Received by Velocity on port 8080 and rendered by a Velocity worker. |
| the Velocity parent (no pool) | Received by Velocity on port 8080 and rendered by the Velocity parent process (no worker). |
| php-cgi, started by Velocity for a script under `Q.webserver.cgi.patterns` | Received by Velocity on port 8080 and rendered by php-cgi, which Velocity starts for this script (Q.webserver.cgi.patterns). |
| PHP-FPM behind Apache (or nginx) | Served by Apache / PHP-FPM (port 443), not by Velocity. |
| mod_php, FrankenPHP, another SAPI | Served by Apache with mod_php (port 80), not by Velocity. |

The rows: **Entered** (scheme, port, and the forwarded headers seen, such as `X-Real-IP` and `X-Forwarded-For` from
a proxy in front), **Received by** (Exponential Velocity and its version, or the server named by
`SERVER_SOFTWARE`), **Rendered by**, PHP and its SAPI, **Process** and **Host** (only for a user with setup or cache
access), **Rendered at**, and **Cached**: a page with the report is rendered for each request and kept by no cache.

Only signals a client cannot set decide: `QBIX_SERVER_VERSION` (defined only in a Velocity process), the SAPI, and
`SERVER_SOFTWARE` and `SERVER_PORT` as the server set them. A worker is told from the parent by its parent process,
which is the Velocity parent or its zygote (`qbixserver`). Forwarded headers are listed as seen and decide nothing,
since a client can send them too. Velocity answers every request itself; it never hands one on to PHP-FPM or Apache,
so a report rendered by PHP-FPM always belongs to a request that went to Apache (for example nginx in front of Apache on 443).

**A report that came out of a cache.** The page carries the report of the request that rendered it. Pages with the
report are sent `Cache-Control: private, no-store` (`eZDebugReportNoStore()` in `eZDisplayDebug()`), so neither
Velocity's response cache nor the role-aware page cache (`X-Exp-Cache: BYPASS (debug report)`) keeps them. Should one
still arrive from somewhere (a page stored before 2026-10-14, a browser or service worker replaying it), the script
compares the report's port (`data-entry-port`) with the address in the browser and its time (`data-rendered-at`)
with the time the page was loaded, and says so above the facts: "This report was rendered at 04:08:02 by PHP-FPM
244909 for a request on port 443, 9 min before this page was loaded on port 8080: the page came from a cache, not
from this request." (`data-exp-debug-replayed`; 120 s of room for a clock that differs).

Why the tab changed (2026-10-14): on :8080 the tab said "Not running on Velocity", engine Apache (fpm-fcgi). The page
was the signed-in front page from the role-aware page cache, stored at 04:08:02 by PHP-FPM (process 244909) for a
request on :443 and answered on :8080 by Velocity's parent straight from that shared store (`Q.web.appCache`), with
PHP-FPM's report in it. Velocity's own response cache did the same for anonymous pages: one rendered for a listed
address was kept with its report and answered to every visitor of :8080 (`X-Cache: HIT`).

The OPcache box says why it has nothing to show: `opcache_get_status()` switched off for that PHP (a pool's
`disable_functions`) or OPcache not loaded.

Test: `expDebugBarReportEngineTest` (no database; server variables and the probe passed in).

### Tests

The bar is tested in a browser at 960 px wide and device scale factor 2, on the admin, admin2 and admin4 designs
and the public site, light and dark: the pinned bar and panel, keep open on reload, the tabs by mouse and keyboard,
the summary against the report, the filter, the IP validation, no page errors. On the admin page also a setting
written to the global override and undone, a preset applied and reverted (each settings file compared byte for
byte; on a failure the test writes the files back), the IP list controls, and "This page only". Screenshots go to
`var/tmp/debug-bar-<engine>-<page>-<tab>.png`. A check of the words makes sure every
string of the bar is translated.

## Related pages

- [Exponential debug bar (feature)](../../features/6.0/exp-debug-bar.md)
- [exp:ini, the INI engine the bar writes through](console-exp-ini.md)
- [Cache console (`exp:cache`)](cache-console.md)
- [Behaviour changes of 1 and 2 October 2026](behaviour-changes-2026-10.md)
- [August 2024](../../history/2024/2024-08.md), [January 2025](../../history/2025/2025-01.md), [August 2025](../../history/2025/2025-08.md)
