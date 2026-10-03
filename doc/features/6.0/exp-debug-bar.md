# The Exp Debug bar

The classic debug report at the bottom of a page became a pinned bar with tabs, a live summary, and controls for
the debug settings and the caches, so you can switch debugging on, look, and switch it off again without opening
an INI file.

Added 2026-10-02. Reference with every server function and test:
[doc/bc/6.0/debug-bar.md](../../bc/6.0/debug-bar.md).

## What you see

- **The pinned bar** shows page time, SQL count and time, peak memory, templates used, warnings and errors (amber or
  red past the thresholds). Each value opens its tab. The title "Exp Debug" and the logo sit at the right end, the
  page summary on the left.
- **The panel** above the bar (at most 60% of the window, scrolling on its own) has the tabs Messages, Settings,
  Cache, Timing, SQL, Templates, Included files, Memory and Velocity. Every section of the classic report is still
  there with its old id.
- **Minimise**: click the Exponential symbol at the left end (or press Enter or Space on it) and the whole bar
  becomes a 40 px tab in the bottom right corner showing only the symbol and the error count.
- **Keep open on reload**: the bar remembers whether it was open (browser `localStorage` keys `exp-debug-keep` and
  `exp-debug-open`); unticked, the details start closed on each page.
- Readable in light and dark mode; no empty strip under the copyright any more.

## Change settings from the bar

The **Settings** tab reads and changes every debug setting per scope, logs each change and can undo it. The
kernel registers 35 settings in nine groups (debug output, who gets debug, templates, database, translations,
mail, debug conditions, caching, extensions); `settings/debugbar.ini` describes them and four presets
(Template work, SQL tuning, Everything, Off). Extensions add settings with their own `debugbar.ini` blocks, and
the debug conditions of `debug.ini` are discovered automatically.

The **Cache** tab lists the caches and clears them through the cache manager, showing when each was last cleared
(or "never").

## Who may use it

Visitors who get the debug report can see the page summary, but the settings, change log, IP test and cache list
need the policies `setup/setup` (settings, log, IP test) or `setup/managecache` (cache list). Before this was
tightened on the same day, any visitor shown the report could read every debug setting, with file paths.

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/debugbar.ini` | `DebugBarSettings` | `Settings[]` | the 35 kernel settings | installation |
| `settings/debugbar.ini` | `Setting_<id>` | `File`, `Block`, `Variable`, `Type`, `Label`, `Group`, `Help` | per setting | installation |
| `settings/debugbar.ini` | `Preset_<id>` | `Name`, setting values | four presets | installation |

Related: [the INI command](exp-ini-command.md) (same editor class), [the audit trail](audit-trail.md) (setting
changes are audited), [October 2026 chronicle](../../history/2026/2026-10.md).

See also (October 2026): [6.0.15 changelog](../../changelogs/6.0/6.0.15.md), [upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md), [October 2026 chronicle](../../history/2026/2026-10.md), [debug bar bc guide](../../bc/6.0/debug-bar.md), [runnable classes](../../specifications/6.0/runnable-commands-cronjobs-views.md).

See also: [Debug output you can read at a glance and style](debug-output-improvements.md), the January 2025 improvements to the classic debug block that the debug bar builds on.
