# Debug output you can read at a glance and style

The classic debug output (the block at the bottom of a page when debugging is
on) became easier to use in January 2025 (release 6.0.7), without changing what
it reports.

## What changed

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

## Examples

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

## The switches (reminder)

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

## Related

[Debug bar](../../bc/6.0/debug-bar.md) (the newer tool that replaces reading this
block), [Exponential debug bar](exp-debug-bar.md),
[Chronicle: January 2025](../../history/2025/2025-01.md).
