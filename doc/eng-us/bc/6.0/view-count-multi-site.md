# View counter 6.0.15: multi-site logs, resume state and what counts as a view

Read this page if you run the cronjob `updateviewcount.php`, or if templates or extensions show `view_count` or use
the fetch `content/view_top_list`. The cronjob was rebuilt for 6.0.15 so that installations with several sites can
count their views correctly; the guide [View count statistics](../../guides/view-count-statistics.md) explains why
and how to set it up.

## In short

| | |
|---|---|
| What changed | The cronjob `updateviewcount.php` is now the classes `expViewCount*` (`kernel/classes/viewcount/`), also run as `./console exp:viewcount:update`. New settings `logfile.ini [ViewCountSettings]` and `[AccessLogFileSettings] Host`, `Port`, `Hosts[]`; new methods `eZViewCounter::addCounts()` and `eZSiteAccess::matchAddress()`. |
| Who is affected | Installations that run the cronjob (it is not active by default). Nobody else. |
| What to do | Nothing for a single site. To count several siteaccesses in one run, see the guide, sections 4 and 5. |
| How to check | `./console exp:viewcount:update --dry-run --top=10 -s <siteaccess>` |

## Behavior that changed

- **The resume state** is no longer `var/<site>/log/updateview.log` with the last line read. It is
  `var/<site>/viewcount/<log>-<hash>.json` (`[ViewCountSettings] StateDir`), one file per log, with each siteaccess's
  inode and byte offset in it, and a `.lock` file next to it. The first run on a log without state takes over the
  old file (`updateview-<log name>.log`, else `updateview.log`): it starts after the line recorded there, or, when
  that line is gone, counts only newer lines. The old file is left in place and no longer written; it can be removed
  after the first run.
- **"/"** counts for the node of the siteaccess's `site.ini [SiteSettings] IndexPage`, no longer for the tree root
  (or a `PathPrefix`'s top node). An `IndexPage` that is no node (`user/login`) means "/" is not counted.
- **A path with a `PathPrefix`** is looked up with each prefix first, then as it is; `PathPrefixExclude[]` is
  respected. With `logfile.ini PathPrefix[]` empty, the siteaccess's own `PathPrefix` is used.
- **`SitePrefix[]`** removes a whole first path element only (`mysite` no longer applies to `/mysite2/...`).
- **Only page views count**: `GET` with status `200` or `304`, no robots (`BotUserAgents[]`, an empty user agent),
  no files (`SkipExtensions[]`), no module and service paths (`SkipPathPrefixes[]`). The earlier cronjob counted
  every line whose path was content, whatever its method, status or user agent, so counts are lower for the same
  traffic. Empty lists restore the earlier behavior for that check.
- **`/content/view/<view mode>/<node>`** counts for every view mode, not only `full` (`ViewModes[]` limits it), and
  view parameters (`/(offset)/10`) and URL encoding (`%C3%BC`) no longer make a path miss its node.
- **Several counted siteaccesses reading one log** get each line once, by the kernel's siteaccess matching; the
  earlier cronjob counted every line for every siteaccess run.
- **Rotation**: the rest of the rotated copy is read before the new log (`RotatedSuffixes[]`); a truncated log is
  read from its start with a warning.
- **One transaction per run**: all views of a run are written together, and the state only after the commit. Nodes
  that do not exist are left out with one query per 500 nodes instead of one per node.
- **Lines longer than 1023 bytes** are read whole (up to `MaxLineLength`, default 64 KiB).
- **Output**: the cronjob prints a report per log (lines read, views, reasons lines were not counted, the state)
  instead of one line per node.
- **The cronjob's description** no longer speaks of an "in-memory view count buffer"; none exists. Setup > Cronjobs
  shows the new text.

## New API

- `eZViewCounter::addCounts( array $counts )`: node id => views, added in one transaction (`count = count + n` for
  rows that exist, new rows for the rest); returns false when the database refused the transaction.
- `eZSiteAccess::matchAddress( $uriString, $host, $port = 80 )`: the siteaccess that served an address, as
  `match()` finds it, without the redirect of `RedirectOnNormalize` and without the browser's language; `unmatched`
  is set where only `DefaultAccess` applied.
- `expViewCountUpdater`, `expViewCountConfiguration`, `expViewCountLogReader`, `expViewCountLogParser`,
  `expViewCountRequestFilter`, `expViewCountPathResolver`, `expViewCountState`, `expViewCountSite`: the counter's
  parts, each usable on its own (an extension can count from another source with its own `expViewCountSite` list).

## Credits

The per-log resume file, the front page counted for the siteaccess's `IndexPage` and the fallback to the path
without `PathPrefix` come from a prototype a site operator wrote for a multi-site installation.
