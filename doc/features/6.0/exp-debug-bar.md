# The Exp Debug bar

This page is for developers and administrators who debug a page: slow, wrong, or full of warnings. The classic debug
report at the bottom of a page became a pinned bar with tabs, a live summary, and controls for the debug settings and
the caches, so you can switch debugging on, look, and switch it off again without opening an INI file. Added
2026-10-02. Reference with every server function and test: [debug bar guide](../../bc/6.0/debug-bar.md).

## Switch it on for yourself only

In `settings/override/site.ini.append.php`, show the debug output only to your own address (see the switches at the
end of this page):

```ini
[DebugSettings]
DebugOutput=enabled
DebugByIP=enabled
DebugIPList[]=203.0.113.10
```

Clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`) and reload a page: the bar is pinned
at the bottom.

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

## The classic debug block: heading, section ids and phone-sized output (6.0.7)

The classic debug output (the block at the bottom of a page when debugging is on) became easier to use in January 2025 (release 6.0.7) without changing what it reports. The debug bar above builds on it: every section of the classic report keeps the ids listed here.

### What changed

1. **The heading tells you why you see debug.** The heading of the block now
   names the switch that enabled it: `(By User)` when
   `[DebugSettings] DebugByUser` is on, `(By IP Address)` when `DebugByIP` is
   on. On a shared team server you see at once that the output is only for you
   and not for every visitor.
2. **Every section has an id.** Each part of the block is wrapped in a `div`
   with a stable id, so a stylesheet can hide, reorder or restyle it:

   | Id | Section |
   |---|---|
   | `main-debug-table` | the table of notices, warnings and errors |
   | `main-resources` | "Main resources" (time, memory) |
   | `timing-points` | "Timing points" |
   | `time-accumulators` | "Time accumulators" |
   | `included-files` | "Included files" (inner table `debug_includes`) |
   | `ezjscpackerusagecontainer` | the CSS and JS files loaded through the packer (from the `ezjscore` extension) |

3. **A responsive stylesheet.** `design/standard/stylesheets/debug.css` lets the
   block scroll inside the page (`#debug { overflow: auto; }`) and wrap its
   content, so debugging on a phone or a narrow window does not widen the
   page.

### Examples

Show only the error table and the timing, hide the rest:

```css
#debug #main-resources,
#debug #time-accumulators,
#debug #included-files,
#debug #ezjscpackerusagecontainer { display: none; }
```

Move the timing points above the error table (when `#debug` is a flex
container):

```css
#debug { display: flex; flex-direction: column; }
#debug #timing-points { order: -1; }
```

Add the rules to a stylesheet of your design extension.

### The switches (reminder)

All in `settings/site.ini`, block `[DebugSettings]`:

| Key | Default | Meaning |
|---|---|---|
| `DebugOutput` | `disabled` | Master switch. Nothing is shown when disabled. |
| `DebugByIP` | `disabled` | When enabled only the addresses in `DebugIPList[]` get the output. |
| `DebugIPList[]` | empty | Addresses or networks (`192.0.0.0/27`). |
| `DebugByUser` | `disabled` | When enabled only the user ids in `DebugUserIDList[]` get the output. |
| `DebugUserIDList[]` | empty | User (content object) ids. |
| `ScriptDebugOutput` | `disabled` | Output for scripts. |
| `AlwaysLog[]` | `error` | Debug types always written to the log, with or without output. |

Never enable `DebugOutput` for everybody on a production site: the block shows
SQL, file names and settings. Use `DebugByIP` or `DebugByUser`.

## Related pages

- [Debug bar guide](../../bc/6.0/debug-bar.md), [runnable classes](../../specifications/6.0/runnable-commands-cronjobs-views.md)
- [The INI command](exp-ini-command.md) (same editor class), [the audit trail](audit-trail.md) (setting changes are audited), [template path comments](template-path-comments.md)
- [Upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md)
- Changelogs: [6.0.4](../../changelogs/6.0/6.0.4.md), [6.0.7](../../changelogs/6.0/6.0.7.md), [6.0.10](../../changelogs/6.0/6.0.10.md), [6.0.15](../../changelogs/6.0/6.0.15.md)
- History: [October 2026](../../history/2026/2026-10.md), [August 2025](../../history/2025/2025-08.md), [January 2025](../../history/2025/2025-01.md) (heading and ids, 6.0.7), [August 2024](../../history/2024/2024-08.md) (responsive block, 6.0.4)
