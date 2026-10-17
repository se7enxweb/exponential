# The log viewer

Read this page if you want to read the log files of an installation from the administration, follow one as it is
written, find the lines of a failed request or the error behind a reference an error page showed, or if you configure,
secure or extend the viewer.

Related: [Finding an error by its reference](../../guides/finding-an-error-by-its-reference.md) (guide),
[The audit trail](audit-trail.md), [Cronjobs console](cronjobs-console.md),
[System information](../../guides/system-information.md).

## In short

- **Setup > Log files** (`setup/logs`, in the **System** group of the Setup menu and in admin4's mega menu) shows
  the log files of the installation: `var/log/error.log`, `warning.log`, `notice.log`, `debug.log`, `strict.log`,
  `deprecated.log`, `auth.log`, the cronjob logs, the logs of every site (`var/<site>/log/*.log`), the logs extensions
  write (`expmcp.log`, the `expai` logs ...) and their rotations `.1` .. `.N`, and compressed rotations (`.gz`, read only).
- The left menu holds the Setup menu and a **navigator** of every file, grouped **Kernel**, **Site**, **Extensions**
  and **Rotated archives**, with its size, the time it was last written and how many lines a minute it gains.
- A file is **followed live** with short requests (one poll a second while lines arrive, slower and slower while
  nothing does), with **Pause** and **Resume**. Nothing holds a server process open.
- **Search** a file, every kernel log or every file, optionally with their rotations: a text or a regular expression
  (bounded, with a time limit), levels, a time range, the siteaccess, part of the request, the client address, an error
  reference. Long searches go on with **More results**.
- Lines are **parsed** (`[ time ] [client] [siteaccess] [request] message`), **colored by level**, grouped by request,
  and **linked**: the lines of the same request, the error reference, the audit records of the same minute, nodes,
  objects, users, classes, requests and templates in the administration.
- **Privacy first**: e-mail addresses, IP addresses, passwords, tokens, keys, session ids and cookies are masked by
  default; the values of secret settings are never shown.
- **Read only by default.** Download (a range, masked and compressed) is on; **Rotate now**, **Empty** and
  **Remove archived rotation** are switched off until `logviewer.ini [ActionSettings]` switches them on. Every view and
  every action is audited.
- The same from a shell: `./console exp:logs` (`bin/php/explogs.php`).

## Who may use it

Three functions of the `setup` module, held by **Administrator only** in a new installation (that role holds every
policy); give them to another role on **Roles and policies** when needed:

| Function | Allows |
| --- | --- |
| `setup/logs` | Open Setup > Log files, follow and search the files, download a range (when `[ActionSettings] Download=enabled`). The Setup overview opens with it too. |
| `setup/managelogs` | Rotate now, empty a file, remove an archived rotation, each only when switched on in `[ActionSettings]`. |
| `setup/unmasklogs` | The **Show unmasked** switch: e-mail addresses, IP addresses and secrets shown as written, for this viewer only and only while switched on. The values of secret settings stay hidden. |

A user without `setup/logs` (an editor) is refused on the page, the feed and the actions, and does not see the link.

## The page

The page shows the file chosen in the navigator (or `?file=var/log/error.log`; without one,
`[LogViewerSettings] DefaultFile`, else the first kernel log). It opens at the end of the file: the last
`[TailSettings] InitialBytes` bytes, from the first whole line. Those lines are part of the page itself, so it reads
without JavaScript; with it, the page's script takes the list over and follows the file.

Each entry shows its time, its level, its source (the label eZDebug wrote, such as `eZTemplate` or
`kernel/content/view.php`, or `cli` for a command line), the siteaccess, the client address and the request, and the
message below. Levels are colored (error, warning, notice, debug) in light and dark, with a text label and an icon as
well as the color, at a contrast of at least 4.5:1, and with forced colors (Windows high contrast) the level stays
readable from its label and border. Entries of the same request (same request address and client) share a marker, so
a request reads as one block.

The page keeps at most `[TailSettings] MaxLinesKept` entries; older ones leave the page (never the file). The list is
**virtualized**: only the entries in view and a margin around them are in the document, so a list of thousands of
entries scrolls as fast as one of ten.

### The navigator

Every file of the allowed directories (see [Which files](#which-files)), grouped:

- **Kernel**: the files named in `[LogViewerSettings] KernelLogs[]` (error, warning, notice, debug, strict, deprecated,
  auth, cronjob, storage, setup, mail, trash purge) in the global log directory or eZDebug's.
- **Site**: the logs of a site's own log directory (`var/<site>/log`).
- **Extensions**: any other file, such as `expmcp.log` or `expai-models.log`.
- **Rotated archives**: `.1` .. `.N` and `.gz`, after their file, by number.

Each with its size, the time it was last written and its line rate. The rate is the growth between two looks at
least 30 seconds apart, divided by the file's average line length (measured once from its last 16 KB); it reads
"-" until there are two looks. Listing costs one `scandir()` per directory and one `stat()` per file; the samples are
kept in one small file in `<cache dir>/logviewer/stats.json`.

## Following a file live

The page polls `setup/logsfeed?action=tail&file=...&offset=<n>&ino=<n>`:

- **A cheap `stat()` first.** A file that has not grown since the offset is answered without opening it.
- **Only what is new is read**, from the byte offset the page already has, at most `[TailSettings] MaxBytesPerPoll`
  per poll; a file that grew more is caught up over the next polls, at once (the answer says `more`). A file is never
  read whole.
- **A chunk ends at the last whole line.** A line still being written waits for the next poll.
- **A chunk cache on the server.** The last chunk read of each file is kept for `ChunkCacheTTL` seconds with the file's
  inode, size and time, so a second viewer of the same file, or a poll that asks again, is answered from it; only the
  bytes after the cached ones are read. The cache is written to a temporary file and renamed, so nobody reads half of it.
- **No file locks**, neither on the log nor on the cache; the writers append without locks too.
- **Backoff when idle.** The page polls every `PollInterval` milliseconds while lines arrive; after each empty poll
  the interval doubles, up to `MaxPollInterval`; new lines bring it back. A hidden browser tab polls at the slowest rate.
- **Rotation and emptying.** A file with another inode (rotated) or smaller than the offset (emptied) is read again
  from its start, and the page marks the break.
- **Pause and Resume.** Pause stops polling (the button, or Space while the log has the focus); Resume goes on from
  where it paused, so nothing is missed.
- An archive (a rotation, a `.gz`) is shown, never followed.

**Why short polls and not a stream.** A Server-Sent Events stream keeps a request open for as long as the page is
open. Under PHP-FPM that is one child per open page; under Exponential Velocity it is one worker per open page, taken
from a fixed pool, for minutes or hours. A poll is one request of a few milliseconds: the worker is free again at
once, whoever leaves a page open. The cronjobs console streams because a run ends; a log has no end.

### Measured

Measured on a test server on 17 October 2026 (PHP 8.5, load average 10 to 13 on a shared server), with a synthetic log of
50 MB (280,000 entries) that grew by 20 entries (3.7 KB) a second, polled once a second.

The log work of one poll, in the process (getrusage and /proc/self/io;
60 s, 54 growing and 6 idle polls):

| | Wall time | CPU | Bytes read |
|---|---|---|---|
| Opening the file (its last 64 KB, 349 entries masked and linked) | 78 ms | 78 ms | 200 KB |
| A poll with 20 new entries | 6.1 ms | 5.0 ms (max 9.6) | 7.9 KB |
| The same poll by a second viewer (from the chunk cache) | 5.9 ms | 4.4 ms | 7.9 KB, 54 of 54 cache hits |
| An idle poll (nothing new: one stat(), no file opened) | 0.25 ms | 0.4 ms | 0.1 KB |
| For comparison: reading the whole file once | 59 ms | 59 ms | 50 MB |
| A literal search through the whole file | 484 ms | 437 ms | stops at 32 MB (`MaxBytesPerRequest`), "More results" goes on |
| A time range of 10 s in the middle of the file | 41 ms | 41 ms | 288 KB (binary search on the times) |
| A catastrophic regular expression, `(\w+\s?)+$` | 4.4 ms | 4.4 ms | stopped with "regex_limit" |

Most of a growing poll's CPU is masking and linking its entries (about 0.25 ms each); the file IO is what grew plus
the small chunk cache file.

The whole request over HTTPS, as an administrator, 30 polls each (`server_ms` is
the time PHP spent on the request, kernel start, session and policy check included):

| | Server time, mean (p95) | Round trip, mean (p95) |
|---|---|---|
| Apache + PHP-FPM, growing | 57 ms (69) | 81 ms (104) |
| Apache + PHP-FPM, idle | 56 ms (66) | 80 ms (95) |
| Velocity (:8080), growing | 47 ms (67) | 85 ms (120) |
| Velocity (:8080), idle | 37 ms (51) | 74 ms (100) |

So a poll costs what any small administration request costs; the log itself adds a few milliseconds at most. A page
that follows a quiet file backs off from 1 s to 15 s (`MaxPollInterval`), so an idle viewer makes about 4 requests a
minute; a hidden tab does not poll at all. Each request holds a PHP-FPM child or a Velocity worker for those
milliseconds only, never for as long as the page is open.

## Searching

The search form (above the list) searches:

- **This file**, **every kernel log** or **every file**, and with **Include rotated files** their `.1` .. `.N` and `.gz`
  too (`[SearchSettings] Rotations`). Files are searched newest first (the file, then `.1`, `.2` ...); within a file the
  entries come in the order they were written.
- **Text**: a literal (default) or a **regular expression**, case sensitive or not. A regular expression longer than
  `MaxPatternLength`, or one that does not compile, is refused with a message.
- **Levels**: error, warning, notice, debug.
- **Time range**: from and to (a date alone means the whole day). A range starts each plain file at the first entry of
  the range with a binary search on the times (about 30 small reads for a file of 1 GB), so a narrow window in a big
  file costs little; a file is left as soon as its entries are well past the end of the range.
- **Siteaccess**, **request** (part of the address), **client address** (its start), **error reference**.

Every search request is bounded: at most `MaxBytesPerRequest` bytes read, `TimeLimit` milliseconds and `MaxResults`
entries. A search that stops at a bound says so and offers **More results**, which goes on from where it stopped (a
cursor `<file>:<offset>`). A regular expression runs with `pcre.backtrack_limit` set to `BacktrackLimit` and without
the JIT (which ignores that limit); a pattern that needs more on one line (`(a+)+$` and the like) stops the search with
"The regular expression is too expensive" instead of holding the server.

With masking on, the text must also match the line as it is shown: a hidden value cannot be found by searching for
it. The client address filter compares the address as written (an operator searching for an address from an incident
report), and the results still show it masked.

## The line format and cross-references

A line that starts an entry is read in one of these formats, the rest of the entry being the lines up to the next one:

| Writer | Format |
| --- | --- |
| eZDebug (`error.log`, `warning.log` ...) | `[ Oct 17 2026 01:54:58 ] [client] [siteaccess] [request or command line] Label:` and the message on the next lines; older files without siteaccess and request |
| eZLog (`storage.log`, extension logs) | `[ Oct 17 2026 01:54:58 ][ siteaccess ][ request ] message` or `[ time ] message` |
| ISO time | `2026-10-17T01:54:58+02:00 message`, `[2026-10-17 01:54:58] message` |
| JSON lines | one object per line with `time`/`ts`/`timestamp`, `level`, `message`/`msg`, `ip`, `url`/`uri` |

The **level** is the one the message names (`Error:`, `PHP Warning`, `[debug]` ...), else the file's
(`[LogViewerSettings] FileLevels[]`), else notice.

**Cross-references** from an entry:

- **Same request**: the entries of every kernel log (and the first rotation) with the same request address and client
  within `[SearchSettings] RequestWindow` seconds, in time order. This is also the **debug output** of the request where
  it was logged: the notices and debug lines of `notice.log` and `debug.log` for it.
- **Error reference**: an error page shows a reference such as `ERR-0123456789`, written beside the details in
  `error.log`. Searching for it (the reference field, or a click on a reference in a line) finds the entry in the kernel
  logs and their rotations and shows the lines of its request.
- **Audit**: a link to the audit console filtered to the minute around the entry
  (`audit/console/(from)/.../(to)/...`), for whoever may read the audit.

## Links into the administration

Values in a message become links, after masking (a masked value is never a link):

| Found | Opens |
| --- | --- |
| a node id (`node_id=123`, `NodeID: 123`, `/content/view/full/123`) | `content/view/full/123` |
| an object id or a user id | the content finder for that object |
| a class identifier (`class 'article'`) | the class (`class/view/<id>`), when the class exists |
| a request address of this site | the address itself (only of the same origin) |
| a template path (`design/<design>/templates/....tpl`) | `setup/templateview/...`, only for users who may open it |
| an error reference | the search for it |

Links are built by the page's script with DOM methods; no text from a log is ever inserted as HTML.

## Privacy: what is masked

The organization rule for this project is that personal data of customers is never exposed, so the viewer masks by
default (`[RedactionSettings]`):

- **E-mail addresses** (`MaskEmails`): `jane.doe@example.com` becomes `j***@***.com`.
- **IP addresses** (`MaskIPs`): `203.0.113.7` becomes `203.0.x.x`, an IPv6 address keeps its first two groups. The
  client column is masked the same way. Version numbers such as `6.0.15.1` are left alone.
- **Secrets** (`MaskSecrets`): passwords in addresses (`mysql://user:***@host`), the values of `password=`, `token=`,
  `api_key=`, `secret=` and similar pairs, `Bearer`/`Basic` credentials, JSON web tokens, session ids and cookies, long
  runs of letters and digits (keys, hashes).
- **Secret settings, always**: the values of `SecretSettings[]` (by default the database password and the mail
  transport password) and of every variable of `SecretGroups[]` whose name says it holds a secret are replaced by `***`
  in every line, masked or not, and so is a password in an address.

A user with `setup/unmasklogs` can switch **Show unmasked** on for themselves; it applies to the page, its polls and
its downloads, and the audit records that the file was read unmasked. Downloads are masked the same way as the page.
Screenshots and reports about a log should be made with masking on.

## Actions

| Action | Needs | Switched on by | Audit |
| --- | --- | --- | --- |
| Download a range (bytes from, to; default the last `MaxDownloadBytes`), masked, compressed with gzip | `setup/logs` | `[ActionSettings] Download=enabled` (default) | `system.log.download` |
| Rotate now: `.N-1` becomes `.N` (the oldest beyond `site.ini [FileSettings] LogFileRotateCount` is removed), the file becomes `.1`; the next line starts a new file | `setup/managelogs` | `Rotate=enabled` | `system.log.rotate` |
| Empty a file (its size becomes 0) | `setup/managelogs` | `Truncate=enabled` | `system.log.truncate` |
| Remove an archived rotation (`.1` .. `.N`, `.gz`; never a current file) | `setup/managelogs` | `DeleteArchives=enabled` | `system.log.delete` |

Every action is a POST to `setup/logsaction` with the form token; rotate, empty and remove first show a confirmation
page that names the file, its size and what will happen. Opening a file records `system.log.read` once per file and
session per `[ViewerAuditSettings] ReadInterval` seconds, so following a file does not write a record per poll. The
audit names are in the catalogue of [the audit trail](../../bc/6.0/audit.md) (channel `system`).

## Which files

The viewer opens only files it lists, and it lists only these (`expLogFileLocator`):

- the files **directly in** a directory of `[LogViewerSettings] Directories[]` (relative to the installation root;
  `{log_dir}` is the directory eZDebug writes to, `*` one directory name, so `var/*/log` is every site's log
  directory); subdirectories are never walked, so the audit trail in `var/log/audit` stays with the audit console;
- whose name matches `FilePatterns[]` (`*.log`, `*.log.N`, `*.log.N.gz`, `*.log.gz`) and no `ExcludePatterns[]`.

A file is asked for by its path relative to the root (`?file=var/log/error.log`). That path is refused when it has
`..`, `.`, a backslash, a control character or a leading slash; its directory must **resolve** (`realpath`) to one of
the allowed directories, and those must resolve inside the installation root, so a symbolic link to a directory
elsewhere is not followed. A file that is a symbolic link is opened only when its target is a log file in an allowed
directory. Anything else answers "not allowed", the same for a file that does not exist as for one outside, so the
viewer cannot be used to probe the disk.

## Rate limit

Every request of the feed (polls, searches, related lines) counts against the session: at most
`[RateLimitSettings] Requests` in `Window` seconds. A request over the limit is answered `429` with `Retry-After`, and
the page waits that long. The counter is a small file per session in `<cache dir>/logviewer/rate`, named by a hash of
the session id; nothing is written to the session, so a poll costs no session write.

## Keyboard and accessibility

- **Space** (while the log has the focus): pause or resume.
- **End**: jump to the newest entry (and follow again); **Home**: the oldest kept; **Up** and **Down** move between entries, **Enter** opens one (its request, debug output and audit).
- **Enter** / **Shift+Enter** in the find field, or **n** / **N** in the log: next / previous match.
- The log is a focusable region with a label; new entries are announced in a polite live region, at most one
  announcement every few seconds ("12 new entries"), never one per line.
- Every control has a visible label and focus ring; colors are never the only sign of a level.

The page works in the designs admin4, admin4l (and adminui, which falls back to them) and the classic admin, with the
shared components and tokens (`.a4-setup-log*` in `stylesheets/modules/setup.css`), no inline CSS, light and dark, at
phone width.

## On the command line

`./console exp:logs` (`bin/php/explogs.php`) uses the same settings, files, masking and limits. The command line is
the server's operator: no policy is checked, and one `system.log.read` is recorded per run that reads a file.

```
./console exp:logs --list                                   the files, by group, with size, time and lines a minute
./console exp:logs --tail=var/log/error.log --lines=50      the last 50 entries
./console exp:logs --tail=var/log/error.log --follow        follow it, with the page's backoff (Ctrl-C ends)
./console exp:logs --search=timeout --file=var/log/error.log --rotations --level=error,warning
./console exp:logs --search='content/view/full/\d+' --regex --scope=kernel --from='2026-10-17 01:00' --to='2026-10-17 02:00'
./console exp:logs --reference=ERR-0123456789               where the reference is, and the lines of its request
./console exp:logs --related=var/log/error.log:123456       the lines of the request of the entry at that offset
```

More options: `--case`, `--scope=file|kernel|all`, `--sa=<siteaccess>`, `--uri=<part>`, `--ip=<start>`,
`--cursor=<cursor>` (printed as "More: ..." when a search stopped at a bound), `--limit=<n>`, `--interval=<ms>`,
`--unmask`, `--json`, `--color=auto|always|never`. Exit code: 0 something listed or found, 1 nothing found, 2 an error
(a file the viewer may not open, an invalid regular expression ...).

## Settings

`settings/logviewer.ini`; override it in `settings/override/logviewer.ini.append.php` or in the administration
siteaccess.

| Setting | Default | Meaning |
| --- | --- | --- |
| `[LogViewerSettings] LogViewer` | `enabled` | `disabled`: the page and its feed answer 404 |
| `Directories[]` | `var/log`, `{log_dir}`, `var/*/log` | the directories whose files are listed (see [Which files](#which-files)) |
| `FilePatterns[]` | `*.log`, `*.log.[0-9]`, `*.log.[0-9][0-9]`, `*.log.[0-9].gz`, `*.log.[0-9][0-9].gz`, `*.log.gz` | the file names listed |
| `ExcludePatterns[]` | (none) | file names never listed |
| `KernelLogs[]` | `error.log`, `warning.log`, `notice.log`, `debug.log`, `strict.log`, `deprecated.log`, `auth.log`, `cronjob*.log`, `storage.log`, `setup.log`, `mail.log`, `trashpurge.log` | the names of the Kernel group |
| `DefaultFile` | `var/log/error.log` | the file opened without `?file=` |
| `FileLevels[]` | `error.log*;error`, `*error*.log*;error`, `warning.log*;warning`, `strict.log*;warning`, `deprecated.log*;notice`, `notice.log*;notice`, `debug.log*;debug` | the level of lines that name none, by file name |
| `[TailSettings] InitialBytes` | `65536` | what the page shows when opened |
| `MaxBytesPerPoll` | `262144` | the most one poll reads |
| `PollInterval` | `1000` | ms between polls while lines arrive |
| `MaxPollInterval` | `15000` | the longest interval of the backoff |
| `MaxLinesKept` | `5000` | the entries the page keeps |
| `ChunkCacheTTL` | `10` | seconds the last chunk of a file is kept; 0 switches the cache off |
| `ChunkCacheMaxBytes` | `262144` | the most bytes of a cached chunk |
| `[SearchSettings] MaxBytesPerRequest` | `33554432` | the most one search request reads |
| `TimeLimit` | `2000` | ms one search request reads |
| `MaxResults` | `200` | entries one search request returns |
| `MaxPatternLength` | `200` | the longest search text |
| `BacktrackLimit` | `100000` | `pcre.backtrack_limit` of a regular expression search |
| `Rotations` | `enabled` | rotations may be searched |
| `MaxGzipBytes` | `67108864` | the most of a `.gz` one request unpacks |
| `RequestWindow` | `2` | seconds around an entry that count as the same request |
| `[RedactionSettings] MaskEmails`, `MaskIPs`, `MaskSecrets` | `enabled` | the masks (see [Privacy](#privacy-what-is-masked)) |
| `SecretSettings[]` | `site.ini;DatabaseSettings;Password`, `site.ini;MailSettings;TransportPassword` | values never shown |
| `SecretGroups[]` | `site.ini;DatabaseSettings`, `site.ini;MailSettings` | groups whose secret-named values are never shown |
| `[ActionSettings] Download` | `enabled` | download a range |
| `Rotate`, `Truncate`, `DeleteArchives` | `disabled` | rotate now, empty, remove archived rotations |
| `MaxDownloadBytes` | `52428800` | the largest range of one download |
| `[RateLimitSettings] Requests`, `Window` | `120`, `60` | feed requests per session and window (seconds) |
| `[ViewerAuditSettings] ReadInterval` | `600` | seconds between two `system.log.read` of the same file and session |

Numeric settings are kept within safe bounds whatever the file says (for example `PollInterval` at least 250 ms,
`MaxBytesPerPoll` at most 4 MB).

## For developers

- Classes in `kernel/classes/logviewer/`: `expLogViewerConfig` (settings), `expLogFileLocator` (allow-list, groups),
  `expLogTail` (offset reads, chunk cache, `.gz`), `expLogParser` (entries, levels, links), `expLogSearch` (bounded
  search, time seek), `expLogRedactor` (masks), `expLogRateLimit`, `expLogFileStats` (line rate), `expLogActions`
  (download, rotate, empty, remove) and the facade `expLogViewer` (policies, audit, the feed's answers).
- Views: `setup/logs` (the page), `setup/logsfeed` (JSON: `action=tail|search|related|reference|files`),
  `setup/logsaction` (POST). Template fetch: `fetch( 'setup', 'log_files' )`, the navigator (empty without
  `setup/logs`).
- A log of your own extension appears in **Extensions** when it is written to an allowed directory with a matching
  name (`var/log/myextension.log` through `eZLog::write()`); give it a level with `FileLevels[]`, or write
  `Error:` / `Warning:` at the start of its messages.
- Tests (no database): `tests/tests/kernel/logviewer/`: the parser, offset tailing and the chunk cache, masking, path
  safety, the regular expression limit, the search, the rate limit and the actions on throwaway files.
