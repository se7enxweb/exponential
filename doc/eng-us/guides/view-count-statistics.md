# View count statistics: page views from the access logs, for one site or many

This guide explains how Exponential counts how often each page was viewed, why the counter was rebuilt for 6.0.15,
and how to set it up for a single site, for siteaccesses that each have their own access log, and for many
siteaccesses that share one log. It also covers running the counter as a cronjob and from the console, what it
remembers between runs, log rotation, robots, and upgrading from the earlier cronjob.

It is written for administrators and developers who run more than one site from one installation, or who want view
counts they can trust. Every setting and option below was checked against the code (`kernel/classes/viewcount/`,
`kernel/classes/ezviewcounter.php`, `kernel/private/classes/cronjobs/updateviewcount.php`,
`kernel/private/classes/commands/expviewcountupdate.php`, `settings/logfile.ini`). The figures in section 12 were
printed on the demonstration server on 3 November 2026 by the commands shown.

[Guides](README.md) · Related: [Cronjobs](cronjobs.md) · Behavior changes: [View counter 6.0.15](../bc/6.0/view-count-multi-site.md) ·
Settings: [logfile.ini](../examples/ini-settings/logfile/README.md) · Command: [exp:viewcount:update](../examples/commands/expviewcountupdate.md)

## In short

- The view counter reads the web server's **access log**, keeps the lines that are page views, finds the node each
  one showed and adds the views to the table `ezview_counter`. Templates read them as a node's `view_count`, the
  fetch `content/view_top_list` lists the most viewed nodes, and the sub-items list has a **Views** column.
- It runs as the cronjob script `updateviewcount.php` (not active by default) or as
  `./console exp:viewcount:update`, which adds `--dry-run`, `--top`, `--format=json` and the upkeep of its state.
- **Single site**: set `StorageDir` and `LogFileName` in `logfile.ini [AccessLogFileSettings]`, as before. Nothing
  else is needed.
- **One log per siteaccess**: put `[AccessLogFileSettings]` into each siteaccess's
  `settings/siteaccess/<siteaccess>/logfile.ini.append.php` and list the siteaccesses in
  `[ViewCountSettings] SiteAccessList[]` (or set `AllSiteAccesses=enabled`). One run counts them all; each keeps
  its own place in its own log.
- **Many siteaccesses in one log**: the log is read once and each line goes to the siteaccess the kernel's own
  siteaccess matching (`MatchOrder`: uri, host, host_uri ...) finds for it, so `/bold/News` counts for `bold` and
  `/News` for the site of the host.
- Each run continues at the **byte where the last one stopped**, per log and per siteaccess, and notices rotation
  and truncation. Robots, errors, redirects, files and module pages are not counted.

## 1. Why the counter was rebuilt

The cronjob that 6.0.14 and every earlier version shipped was written for one site with one log. Installations that
serve several sites from one database ran into these problems, all of which the rebuilt counter solves:

| Problem of the earlier cronjob | What happened | What 6.0.15 does |
|---|---|---|
| **One shared resume file.** Where the last run stopped was kept in one file, `var/<site>/log/updateview.log`, whatever log was read. | Two siteaccesses with their own access logs (two virtual hosts) overwrote each other's marker on every run. Each run then looked in its log for the other log's last line, did not find it and counted nothing, or found nothing to start from and counted the whole log again. | One state file per log, and in it one place per siteaccess (section 7). |
| **Resuming by the text of the last line.** The run searched the log for the line it had last read, and only kept the first 1023 bytes of a line. | Two identical lines (the same visitor reloading in the same second), a line longer than 1023 bytes (long user agents and referers) or a rotated log made the search fail: nothing was counted until the log rotated again. | Resume by inode and byte offset, with a hash of the log's first line to recognize it. |
| **"/" leaked to the content root.** The home page's hits were looked up as the URL alias "" (empty), and with a `PathPrefix` as "prefix/". | The tree root (node 2) or the prefix's top node got the home page's views, not the page the siteaccess shows at "/". | "/" is the siteaccess's `site.ini [SiteSettings] IndexPage`. |
| **PathPrefix sites lost hits.** With `PathPrefix[]` set, a path was only looked up with a prefix in front. | Pages outside the prefix (shared folders, `PathPrefixExclude[]`) were never counted. | The prefixes first, then the path as it is; `PathPrefixExclude[]` is respected; the siteaccess's own `PathPrefix` is used when `logfile.ini` names none. |
| **A shared virtual-host log mixed the sites.** Each siteaccess's run counted every line of the log. | With one Apache log for several siteaccesses (uri matching, `/bold/...`, or several hosts in one `vhost_combined` log) the views of every site were counted for each, so each view was counted several times, and paths were looked up with the wrong siteaccess's prefix and languages. | Lines are given to siteaccesses by the kernel's siteaccess matching, and `Hosts[]` limits a siteaccess to its hosts. |
| **Rotation lost or doubled lines.** | A log rotated between two runs: the lines written after the last run and before the rotation were never read; with `copytruncate` the new log was searched for a line that was not there. | The rest of the rotated copy (`access_log.1`, `access_log.1.gz`) is read first, then the new log from its start. |
| **Everything counted.** | Robots, `POST` requests, `404` and `301` responses, and the redirect and the page it led to were counted alike. | Only `GET` with `200` or `304`, no robots, files or module pages; each rule can be changed or switched off (section 8). |

The cronjob's description also said it would "flush the in-memory view count buffer to the database". There has
never been such a buffer: page requests do not count views, only this cronjob does, from the access log. The
description now says what it does.

The per-log resume file, the "/" of the siteaccess's `IndexPage` and the fallback to the path without `PathPrefix`
were first tried in a prototype a site operator wrote for a multi-site installation; 6.0.15 builds on those ideas
and adds the rest.

## 2. What a run does

1. **Which siteaccesses, which logs.** The siteaccesses of `--siteaccess-list`, `--all-siteaccesses`,
   `[ViewCountSettings] SiteAccessList[]` or `AllSiteAccesses=enabled`, else the siteaccess the run is for (`-s`).
   Each reads its own `logfile.ini [AccessLogFileSettings]`; siteaccesses that name the same file form one log.
   Administrations are left out of the lists (the rule of the siteaccess menu: `AdminSiteAccessList[]`, else an
   admin design or a `FrontSiteAccess`).
2. **Lock.** Each log has a lock file next to its state; a second run waits up to `LockTimeout` seconds, then
   leaves that log alone and ends with exit code 3.
3. **Read the new lines.** From each siteaccess's place in the log, complete lines only (the line the server is
   still writing waits for the next run).
4. **Keep the page views** (section 8) and give each line to its siteaccess (section 5).
5. **Find the node.** "/" is the `IndexPage`; `/content/view/<view mode>/<node>` is that node; anything else is a
   URL alias, looked up in the siteaccess's languages (`SiteLanguageList[]`), with its path prefixes first. View
   parameters (`/(offset)/10`) and the query string are not part of the path. Each path is looked up once per run.
6. **Write.** Nodes that no longer exist are left out; the views of the whole run are added to `ezview_counter` in
   one transaction (`count = count + n`, so nothing counted in parallel is lost).
7. **Remember.** Only after the commit is each log's state written. A run that fails writes nothing, and the next run
   reads the same lines again.

## 3. A single site (nothing changes)

```ini
# settings/override/logfile.ini.append.php
[AccessLogFileSettings]
StorageDir=/var/log/httpd
LogFileName=access_log
```

Run it once by hand, then add it to a cronjob part:

```bash
./console exp:viewcount:update --dry-run --top=10 -s site
./console exp:viewcount:update -s site
```

```ini
# settings/override/cronjob.ini.append.php
[CronjobPart-viewcount]
Scripts[]=updateviewcount.php
```

`php runcronjobs.php -s site viewcount` then counts every hour, or as often as the part's schedule says
([Cronjobs](cronjobs.md)). `SitePrefix[]` and `PathPrefix[]` keep their meaning. When the cronjob runs for an
administration (`-s admin`, as many installations do), the log of its settings is read, and "/" and the URL aliases
are those of `DefaultAccess`.

## 4. One log per siteaccess

Two sites on two virtual hosts, each with its own log:

```ini
# settings/siteaccess/site/logfile.ini.append.php
[AccessLogFileSettings]
StorageDir=/var/www/vhosts/example.com/logs
LogFileName=access_ssl_log
```

```ini
# settings/siteaccess/shop/logfile.ini.append.php
[AccessLogFileSettings]
StorageDir=/var/www/vhosts/shop.example.com/logs
LogFileName=access_ssl_log
PathPrefix[]=Shop
```

```ini
# settings/override/logfile.ini.append.php
[ViewCountSettings]
SiteAccessList[]
SiteAccessList[]=site
SiteAccessList[]=shop
```

One run (`./console exp:viewcount:update`, or the cronjob for any siteaccess) reads both logs, each from its own
place. Running the cronjob once per siteaccess (`-s site`, `-s shop`) without `SiteAccessList[]` works as well: each
run counts its own siteaccess and keeps its own place; the earlier cronjob could not do that.

## 5. Many siteaccesses in one log

A single virtual host whose siteaccesses are told apart by the path (`MatchOrder=uri;host`, `/bold/...`,
`/starter/...`) writes one log for all of them. Set the log once, for everyone, and count every site:

```ini
# settings/override/logfile.ini.append.php
[AccessLogFileSettings]
StorageDir=/var/www/vhosts/example.com/logs
LogFileName=access_ssl_log
# the host of the requests, as the lines of the combined format do not carry it
Host=www.example.com

[ViewCountSettings]
AllSiteAccesses=enabled
```

The log is read once. Each line goes to the siteaccess the kernel's matching finds for its host and path
(`eZSiteAccess::matchAddress()`, which is `match()` without the redirect of `RedirectOnNormalize` and without the
browser's language), the siteaccess's part of the path is removed, and the rest is looked up with that siteaccess's
`IndexPage`, `PathPrefix` and languages. Lines that belong to a siteaccess that is not counted (the administration,
`/admin/...`) are skipped as `other-siteaccess`.

Several hosts in one log: use Apache's `vhost_combined` format (`LogFormat "%v:%p %h %l %u %t \"%r\" %>s %O
\"%{Referer}i\" \"%{User-Agent}i\""`); the counter reads the host of each line and the host matching works per line.
`Hosts[]` limits a siteaccess to some hosts, for example when a log also carries a host that is not this
installation's.

`MatchSiteAccess` decides when lines are matched: `auto` (the default) when more than one counted siteaccess reads a
log, `enabled` always (one siteaccess counted out of a log of several), `disabled` never.

## 6. Running it

### The cronjob

`cronjobs/updateviewcount.php` does what `./console exp:viewcount:update` does without options; its output goes to
the cronjob's log. It is in `cronjob.ini` but not active (`#Scripts[]=updateviewcount.php`); add it to a part as in
section 3.

### The command

| Option | What it does |
|---|---|
| `--dry-run` | Reads and reports; writes neither counts nor state. |
| `--top=N` | Lists the N most viewed nodes of the run and the N most requested paths that are no node (a missing `PathPrefix`, a module page to add to `SkipPathPrefixes[]`). |
| `--all-siteaccesses` | Every siteaccess of `AvailableSiteAccessList[]` with a log, except the administrations. |
| `--siteaccess-list=site,shop` | These siteaccesses. |
| `--log-file=PATH` | One log for every counted siteaccess, in place of their settings: a trial on a copy, or an older rotated log (`.gz` is read too). |
| `--state-dir=PATH` | The state somewhere else, for such a trial. |
| `--from-start` | With `--dry-run` only: read the logs from their first line, ignoring the state. |
| `--skip-to-end` | Count nothing, mark the logs as read: start counting from now. |
| `--reset-state` | Count nothing, forget the state: the next run reads the logs from their first line. |
| `--format=json` | The report as JSON, format `exp-viewcount/1`. |

Exit codes: `0` fine (also when there was nothing new), `1` an error (a log missing or unreadable, the database
refused the counts, the state could not be written), `2` a usage error, `3` another run held a log's lock.

The report names, per log, the siteaccesses, what happened to the log since the last run (`new`, `resumed`,
`rotated`, `truncated`, `migrated`), the bytes and lines read, the views, and why the other lines were not counted:

| Reason | Lines |
|---|---|
| `malformed` | not an access log line, or a request that is not `METHOD target` |
| `too-long` | longer than `MaxLineLength` |
| `method`, `status` | another method or status than `Methods[]`, `Statuses[]` |
| `bot` | a user agent with one of `BotUserAgents[]`, or none (`SkipEmptyUserAgent`) |
| `asset`, `excluded` | a file (`SkipExtensions[]`) or a path of `SkipPathPrefixes[]` |
| `other-host`, `other-siteaccess` | not one of the siteaccess's `Hosts[]`; a siteaccess that is not counted |
| `before-resume` | counted already (the earlier cronjob's state, section 10) |
| `not-content`, `no-node` | a path that is no node; a node that no longer exists |

It holds no client addresses and no user agents.

## 7. Where it stops, and log rotation

The state is one JSON file per log in `[ViewCountSettings] StateDir` (default `var/<site>/viewcount/`), named after
the log and a hash of its path, with a lock file next to it:

```json
{
    "format": "exp-viewcount-state/1",
    "log": "/var/www/vhosts/example.com/logs/access_ssl_log",
    "sites": {
        "site": { "inode": 2363541, "device": 2052, "offset": 1048576, "head": "9c1f…", "lines": 4210, "updated": "2026-11-03T10:00:02-08:00" },
        "bold": { "inode": 2363541, "device": 2052, "offset": 1048576, "head": "9c1f…", "lines": 4210, "updated": "2026-11-03T10:00:02-08:00" }
    }
}
```

It is written to a temporary file and renamed, so it is never half written. Each siteaccess has its own place: a
siteaccess added later reads the log from the start, the others carry on where they were, and each line is still
read once.

On each run the log is compared with the state:

- **resumed**: the same file (inode and first line) and at least as long: read on from the offset.
- **rotated**: the log was renamed (`create`) or copied and emptied (`copytruncate`): the copy named by
  `RotatedSuffixes[]` (default `.1`, `.1.gz`, `.0`) that is the old file (same inode, or same first line, so a
  compressed copy is found too) is read from the offset to its end, then the new log from its start.
- **truncated**: shorter than the offset and no copy found: the new log from its start. The lines written between
  the last run and the truncation are lost, and the report warns about it. Run the counter more often than the log
  is rotated, or name the copy in `RotatedSuffixes[]`.

**Plesk** appends the lines of `access_log` and `access_ssl_log` to `access_log.processed` and
`access_ssl_log.processed` when it builds its statistics, and empties the live log. The `.processed` file is a
different file, so it is not a rotated copy of the live log. Read the `.processed` file instead
(`LogFileName=access_ssl_log.processed`): it only grows, and its own rotated copies (`.processed.1.gz`) are found
by the default `RotatedSuffixes[]`. The views then arrive after each statistics run. Or read the live log and run
the counter more often than the statistics run, accepting that the lines between the last run and the
statistics run are lost.

## 8. What counts as a page view

The defaults in `logfile.ini [ViewCountSettings]`:

- `Methods[]=GET`; `Statuses[]=200` and `304` (a browser revalidating a page it has is a view; a `301` is not, its
  target is counted when it is requested).
- `BotUserAgents[]`: parts of the user agent, in any case: `bot`, `crawl`, `spider`, `curl/`, `python`, `headless`,
  `warmer` (cache warmers) and others. `SkipEmptyUserAgent=enabled`.
- `SkipExtensions[]`: style sheets, scripts, images, fonts, media, archives.
- `SkipPathPrefixes[]`: `/var/`, `/design/`, `/extension/`, `/user/`, `/content/search`, `/content/edit/`,
  `/api/`, `/ezjscore/` and others, compared with the path as requested and without the siteaccess's part.
- `ViewModes[]`: empty, so `/content/view/<any view mode>/<node>` counts; `ViewModes[]=full` limits it.

An empty list switches its check off. As for every INI list, entries in an override are added to the shipped list;
an empty line `Methods[]` first clears it, so the entries after it replace the defaults:

```ini
# settings/override/logfile.ini.append.php: count HEAD requests too, and no status check at all
[ViewCountSettings]
Methods[]
Methods[]=GET
Methods[]=HEAD
Statuses[]
```

Other log formats: the common, combined and `vhost_combined` formats are recognized line by line (Apache, nginx,
Plesk's `plesklog`, Velocity, also with fields after the user agent and without a client address). A format of your
own takes `LineRegexp`, a regular expression with the named groups `time`, `request` and `status`, and optional
`host`, `ip`, `agent`, `referer`.

## 9. Settings reference

`[AccessLogFileSettings]`, per siteaccess:

| Setting | Meaning |
|---|---|
| `StorageDir`, `LogFileName` | The access log (a relative `StorageDir` is below the installation). |
| `SitePrefix[]` | A first path element to remove, a whole element only (`mysite` does not cut `mysite2`); not used when lines are matched. |
| `PathPrefix[]` | Prefixes tried first; empty: the siteaccess's `site.ini PathPrefix`. |
| `Host`, `Port` | The host and port of the log's requests when the lines carry none (for the matching). |
| `Hosts[]` | Only lines of these hosts count for the siteaccess. |

`[ViewCountSettings]`, for the run: `SiteAccessList[]`, `AllSiteAccesses`, `MatchSiteAccess`, `StateDir`,
`LockTimeout`, `RotatedSuffixes[]`, `MaxLineLength`, `LineRegexp`, `ViewModes[]`, `Methods[]`, `Statuses[]`,
`SkipExtensions[]`, `SkipPathPrefixes[]`, `BotUserAgents[]`, `SkipEmptyUserAgent`. Each is described in
`settings/logfile.ini` and on the generated page [logfile.ini [ViewCountSettings]](../examples/ini-settings/logfile/ViewCountSettings.md).

## 10. Upgrading from the earlier cronjob

- The settings it read keep their meaning; nothing has to change for a single site.
- Its state file is taken over: when a log has no state yet, the counter looks in the log directory for
  `updateview-<log name>.log` (the per-log name the prototype used) and `updateview.log`, and starts after the line
  recorded there. When that line is no longer in the log, it counts only lines newer than that line's time
  (`before-resume` in the report). The old file is left in place and no longer written.
- Counts can be lower than before, deliberately: robots, errors, redirects and module pages are no longer counted,
  and the home page counts for its own node instead of the tree root. To count as before, empty the lists of
  section 8.
- Details and the reasons: [View counter 6.0.15](../bc/6.0/view-count-multi-site.md).

## 11. Troubleshooting

| Symptom | Cause and fix |
|---|---|
| "no siteaccess has an access log to count" | `StorageDir` or `LogFileName` is empty for every counted siteaccess. Set them (sections 3 to 5). |
| "the access log … does not exist or cannot be read" | Wrong path, or the cronjob runs as a user who cannot read the web server's logs (Plesk: the subscription's user can, `root` can). |
| Many `not-content` lines | `--dry-run --top=20` lists the paths. Pages that no longer exist, a missing `PathPrefix[]`, or module pages to add to `SkipPathPrefixes[]`. |
| Everything is `other-siteaccess` | The host of the lines is not the host of the siteaccesses: set `Host` (combined format) or use `vhost_combined`. |
| Views of one site counted for another | Two siteaccesses read one log with `MatchSiteAccess=disabled`; use `auto`. |
| "truncated" warnings | The log is emptied between runs (Plesk statistics, `copytruncate`): run more often, or add the copy's suffix to `RotatedSuffixes[]`. |
| Exit code 3 | Another run was reading the log; it finishes on its own. Raise `LockTimeout` when runs overlap on purpose. |
| Start counting now, not from the old log | `./console exp:viewcount:update --skip-to-end` once. |

## 12. Checked on the demonstration server

Every siteaccess of the demonstration installation (eleven sites and nine administrations, `MatchOrder=uri;host`)
read the logs in a dry run:

```bash
./console exp:viewcount:update -s site --all-siteaccesses --dry-run --from-start \
    --log-file=/var/www/vhosts/<host>/logs/access_ssl_log.processed.1.gz --top=5
```

- An Apache log of June 2026 (50 570 lines): 616 views of 5 nodes, 579 of them for the site's front page
  (its `IndexPage` node, not the tree root); not counted: 30 601 robots, 11 163 other statuses, 4 722 files, 3 132
  other methods, 298 paths of pages removed since.
- Velocity's log of September 2026 (75 906 lines, mostly a cache warmer): 39 views; 65 065 robot lines.
- The administrations were left out by the menu's rule.
- One real run into a copy of the database (made with SQLite's backup, the live file opened read-only) added
  exactly the 616 views; a second run over the same log added none; the live table was unchanged.
