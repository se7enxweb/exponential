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
  `deprecated.log`, `auth.log`, the request logs `404.log` and `security.log` ([Request logs](request-logs.md)), the
  cronjob logs, the logs of every site (`var/<site>/log/*.log`), the logs extensions
  write (`expmcp.log`, the `expai` logs ...) and their rotations `.1` .. `.N`, and compressed rotations (`.gz`, read only).
- The left menu holds the Setup menu and a **navigator** of every file, grouped **Kernel**, **Site**, **Extensions**,
  **Web server** and **Rotated archives**, with its size, the time it was last written and how many lines a minute it
  gains.
- Every view has a **permalink** without a query string, by the log's key: `/setup/logs/view/error`,
  `/setup/logs/view/debug`, `/setup/logs/view/webserver-access`, with its filters, search and position as view
  parameters, e.g. `/setup/logs/view/error/(level)/error/(search)/timeout/(follow)/0`. **Copy permalink** puts it on
  the clipboard. See [Permalinks](#permalinks).
- The **web server's logs** (access and error) are shown when `logviewer.ini` names them by key; they are off by
  default and never guessed. See [Web server logs](#web-server-logs).
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

The page shows the log chosen in the navigator, by its permalink (`/setup/logs/view/<key>`, see
[Permalinks](#permalinks)); `/setup/logs` alone opens `[LogViewerSettings] DefaultKey` (`error`), else
`DefaultFile`, else the first kernel log. It opens at the end of the file: the last
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
  auth, cronjob, storage, setup, mail, trash purge, the request logs 404 and security) in the global log directory or
  eZDebug's; a log with a `LogLabel[<key>]` is shown by its label.
- **Site**: the logs of a site's own log directory (`var/<site>/log`).
- **Extensions**: any other file, such as `expmcp.log` or `expai-models.log`.
- **Web server**: the logs configured by key in `LogList[]` (see [Web server logs](#web-server-logs)), shown by their
  label (`LogLabel[]`), with the file name beside it.
- **Rotated archives**: `.1` .. `.N` and `.gz`, after their file, by number.

Every entry links to its permalink (`/setup/logs/view/<key>`, a rotation with `/(rotation)/<n>`).

Each with its size, the time it was last written and its line rate. The rate is the growth between two looks at
least 30 seconds apart, divided by the file's average line length (measured once from its last 16 KB); it reads
"-" until there are two looks. Listing costs one `scandir()` per directory and one `stat()` per file; the samples are
kept in one small file in `<cache dir>/logviewer/stats.json`.

## Permalinks

Every view of a log has an address without a query string, short enough to type and safe to bookmark or send:

```
/setup/logs/view/<key>[/(name)/<value>]...
```

**The key** names a log, never a path (`expLogFileLocator::keyFor()`, `resolveKey()`):

| Log | Key | Permalink |
| --- | --- | --- |
| `var/log/<name>.log` | `<name>` | `/setup/logs/view/error`, `/setup/logs/view/debug`, `/setup/logs/view/expmcp` |
| `var/<site>/log/<name>.log` | `<site>-<name>` | `/setup/logs/view/site-storage` |
| a file in another directory of `Directories[]` | the directory with `-` for `/`, then `-<name>` | `/setup/logs/view/var-other-logs-app` |
| a log configured in `LogList[]` | its key | `/setup/logs/view/webserver-access` |

A key is letters, digits, `.`, `_` and `-` (at most 64, starting with a letter or digit); any other character of a
file name becomes `-`. A rotation is the same key with `/(rotation)/<n>` (`-1` for a compressed file without a
number). A key only ever finds a file the navigator lists: anything else, `..` and slashes included, answers the
kernel's "not found" page (404), the same for a key that does not exist as for one that is not allowed.

**The view parameters**, in the order a link writes them (`expLogPermalink::PARAMS`); a parameter at its default is
left out, so a plain view is just `/setup/logs/view/<key>`:

| Parameter | Value | Default | Meaning |
| --- | --- | --- | --- |
| `(rotation)` | a number | `0` | `.1` .. `.N` of the log (`-1`: a bare `.gz`) |
| `(level)` | `error,warning,notice,debug` (a comma list) | all | the levels searched |
| `(search)` | text | | the search text |
| `(regex)` | `1` / `0` | `0` | the search text is a regular expression |
| `(case)` | `1` / `0` | `0` | case sensitive |
| `(from)`, `(to)` | a time, e.g. `2026-10-17T01~3A00` | | the time range |
| `(siteaccess)` | a name | | the siteaccess |
| `(uri)` | text | | part of the request address |
| `(ip)` | the start of an address | | the client |
| `(reference)` | `ERR-0123456789` | | an error reference |
| `(scope)` | `file`, `kernel`, `all` | `file` | which logs are searched |
| `(rotations)` | `1` / `0` | `0` | their rotations too |
| `(cursor)` | `<file>:<offset>` | | where a search goes on ("More results") |
| `(offset)` | a byte offset | | the entry in focus |
| `(follow)` | `1` / `0` | `1` | `0`: the view opens paused |
| `(wrap)` | `1` / `0` | the viewer's preference | long lines wrapped on this one page; a shared link never carries it (see "Wrapping long lines") |
| `(unmask)` | `1` / `0` | `0` | shown unmasked (only with `setup/unmasklogs`; never part of a copied permalink) |
| `(done)`, `(ok)` | an action, `1` / `0` | | the outcome of rotate, empty or remove (set by the action, not copied) |

**The encoding of a value.** Letters, digits and `_ . , -` are written as they are; every other byte is `~` and its
two hexadecimal digits, so a value never holds a `/` or a `(` that would end it: a space is `~20`, a slash `~2F`, a
colon `~3A`, a `~` itself `~7E`, and a character outside ASCII one `~XX` per byte of its UTF-8 (`ü` is `~C3~BC`).
Examples:

```
/setup/logs/view/error/(level)/error/(search)/timeout/(follow)/0
/setup/logs/view/error/(level)/error,warning/(from)/2026-10-17/(to)/2026-10-17
/setup/logs/view/error/(reference)/ERR-0123456789/(scope)/kernel/(rotations)/1
/setup/logs/view/webserver-access/(search)/~2Fadmin~2Fsetup/(level)/error
/setup/logs/view/debug/(rotation)/1/(offset)/204800/(follow)/0
```

The page keeps its address in step with the view as it changes (paused or following, the entry in focus), so
the address bar and **Copy permalink** always give a link that opens the same view. The search form works without
JavaScript too: it is sent with the key and the fields, and the page answers `303` with the permalink.

The link **Copy permalink** gives (and the field above it shows) carries the request and client address filters as
the masks of the settings show them, whoever copies it: `(ip)/203.0.113.7` becomes `(ip)/203.0.x.x`, `(ip)/203.0.113`
becomes `(ip)/203.0.x`, an e-mail address in `(uri)` becomes `j***@***.com`. A viewer who searched unmasked for a
whole address therefore never shares it; whoever opens the link finds every entry shown with that masked address
(which may be more than one client). The address bar keeps the filters as typed, for the viewer's own reloads. With
`MaskIPs=disabled` the address is shown to everyone and stays in the link. In the template, `$log_share_permalink`
holds that link (`$log_permalink` is the view's own); a template override without it falls back to `$log_permalink`.

### Wrapping long lines

Long lines are wrapped by default. The **Wrap lines** switch stores each viewer's choice as the user preference
`admin_logviewer_wrap` (`1` or `0`, written through `user/preferences/set_and_exit` like the other administration
preferences), so it stays the same for every log, every permalink and the next visit, and the page draws it before
anything shows (no flash). Without a stored choice, `[TailSettings] WrapLines` decides (default `enabled`). Following
or paused, and the entry in focus, belong to a view and travel in its permalink; wrapping is a preference and does not.

**View parameters only.** The options of the page and of its feed are view parameters, `/(name)/value`; a query
string (`?file=`, `?key=`, `?q=` ...) is never read and never redirected. The search form and the file chooser without
JavaScript are sent with POST (with the form token): `key` (and `rotation`) or `file`, and the search fields (`q`,
`regex`, `case`, `levels[]`, `from`, `to`, `siteaccess`, `uri`, `ip`, `reference`, `scope`, `rotations`), answered
`303 See Other` with the permalink of the same file and search. A file it may not open answers 404. The feed
(`setup/logsfeed`) and the actions (`setup/logsaction`) name a file by its internal id (`var/log/error.log`; a
configured log `@<key>/<file name>`); the actions answer with the permalink.

## Web server logs

The web server's access and error logs are shown when they are named by key in `logviewer.ini`. They are **off by
default and never guessed**: the keys `webserver-access` and `webserver-error` are listed with no path, and a key
without a path is not shown.

```
[LogViewerSettings]
LogList[]=webserver-access
LogList[]=webserver-error
LogPath[webserver-access]=/var/www/vhosts/<domain>/logs/access_ssl_log
LogPath[webserver-error]=/var/www/vhosts/<domain>/logs/error_log
```

(the paths of a Plesk server; elsewhere e.g. `/var/log/apache2/access.log` and `error.log`, or the logs of the site's
own virtual host).

- `LogList[]` lists the configured keys, `LogPath[<key>]` the file (absolute, or relative to the installation root; it
  may be outside the installation), `LogLabel[<key>]` the name the navigator shows (defaults: "Web server access log",
  "Web server error log"), `LogGroup[<key>]` its group: `kernel`, `site`, `extensions` or `webserver` (the default,
  also for an unknown value).
- A configured log can name any other file the same way, e.g. a log a program writes outside `var/`. Only the keys
  listed are ever opened: a permalink, a feed request or an action cannot name another path.
- The rotations beside the file are listed too: `<name>.N` and `<name>.N.gz` (`access_ssl_log.1`, `error_log.1.gz`);
  other files of the directory never are (Plesk's `access_ssl_log.processed` is not a rotation). A rotation that is a
  symbolic link leading out of the directory is refused.
- **Read and download only.** The web server owns these files: rotate, empty and remove are never offered for a
  configured log, and `setup/logsaction` refuses them whatever `[ActionSettings]` says.
- **The user that runs PHP must be able to read the file** (PHP-FPM's pool user, or the user of Exponential Velocity).
  On Plesk the files in `/var/www/vhosts/<domain>/logs/` belong to the site's user; `/var/www/vhosts/system/<domain>/logs/`
  does not let it in. A file that cannot be read is listed as such and not opened; `open_basedir` must allow the path.
- The lines are read as the web server writes them: the **access log** in the common or combined format
  (`client - user [17/Oct/2026:01:00:00 +0200] "GET /path HTTP/1.1" 200 1234 "referer" "agent"`), with the status as
  the level (5xx error, 4xx warning, else notice), the client and the path linked into the request grouping; the
  **error log** of Apache 2.4 (`[Fri Oct 17 01:00:00.123456 2026] [module:level] [pid N] [client ip:port] message`),
  with its level. Masking applies as everywhere: client addresses and e-mail addresses are masked by default.

## Following a file live

The page polls `setup/logsfeed/(action)/tail/(file)/<id>/(offset)/<n>/(ino)/<n>`, each value written as a permalink
writes it (`~2F` for `/`, `expLogPermalink::encode()`); a search asks `setup/logsfeed/(action)/search/(file)/<id>/(q)/...`
with the criteria of the form and `(cursor)` for the next results. A query string is never read:

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

A search finds only what the viewer is shown: the text, the request and the client address must also match as they
are shown, so a hidden value cannot be found by searching for it, one guess at a time. With masking on, the client
address filter compares the masked address: `203.0.` finds `203.0.113.7` (shown as `203.0.x.x`), `203.0.113` finds
nothing. To search for a whole address from an incident report, a viewer with `setup/unmasklogs` switches **Show
unmasked** on. The values of secret settings and a password in an address are hidden from every viewer, so they are
never found, masked or not.

The request and client address filters are compared with the value as the viewer is shown it, and only with that, so
a value copied from the page is a filter too: `203.0.x.x` (an `x` is accepted in the client address), `2001:db8:x:x`,
`2001::x` and `::x` for compressed IPv6 addresses, `token=***` in the request. Case does not matter (`2001:DB8:`). A
filter that holds a mark of the masks (`x` in an address, `***` in a request) also finds, for an unmasked viewer, the
entries a masked viewer would find with it, so a link made from a masked view works for both. This holds on every path
that filters: the kernel logs, `404.log` and `security.log` (whose addresses are written masked already, so the
masked form is the only one that finds them, for every viewer), the archives (`.N` and `.N.gz`, also with
**rotations**), the page's script, the error digest's links and the permalinks.

A compressed IPv6 address with one group before the `::` (`2001::7334`, `fe80::1`) is shown as `2001::x`, as the
request logs write it; before 6.0.15 the viewer showed it unmasked and the request logs wrote `x`.

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

Rotate, empty and remove are never offered for a log configured by key (the web server's): its program owns it. After
an action the page opens the permalink of the log with `(done)/<action>/(ok)/1` (or `0`), which shows the outcome.

Every action is a POST to `setup/logsaction` with the form token; rotate, empty and remove first show a confirmation
page that names the file, its size and what will happen. Opening a file records `system.log.read` once per file and
session per `[ViewerAuditSettings] ReadInterval` seconds, so following a file does not write a record per poll. The
audit names are in the catalogue of [the audit trail](../../bc/6.0/audit.md) (channel `system`).

## Managing logs

**Setup > Log files > Manage logs** (`setup/managelogs`; `/setup/logsmanage`, or `/setup/logsmanage/<key>` for one log)
lists every log the viewer may open with its size, an estimate of its lines, its last write, its growth (bytes an
hour since the last rotation), its archives (`.1` .. `.N`, each with its size and whether it is compressed) and the
limits that apply to it. Per log:

| Action | What it does | Switched on by |
| --- | --- | --- |
| Rotate now | `.N-1` becomes `.N`, the oldest beyond the limit `keep` is removed, the file becomes `.1` | `[ActionSettings] Rotate` |
| Empty | the file is copied to `.1` first (archive first), then emptied in place (same inode, the writers go on) | `Truncate` |
| Compress archives | each plain archive `.N` becomes `.N.gz` | `Compress` |
| Remove old archives | the archives older than N days, and those beyond `keep` | `DeleteArchives` |
| Download | the current log or an archive, masked, as gzip (the viewer's download, `setup/logsaction`) | `Download` |

**Rotate all** rotates every log over its size limit; **Prune all** removes the old archives of every log. Every
action, one log or all, first shows its **preview**: each step it would take (rename, copy, compress, remove, empty)
with the files and their sizes, and changes nothing. Its **Confirm** posts it again: the plan is applied under a lock
per log (a log being rotated elsewhere answers "locked"), and each step is checked against the disk again, so a plan
that went stale is skipped step by step (a target that appeared since is never overwritten). Every POST carries the
form token. A log configured by key (the web server's) is listed but never managed: its program owns it.

The limits of a log, first found wins:
1. `logviewer.ini [RotationSettings]` `MaxSize[<key>]`, `Keep[<key>]`, `Compress[<key>]`, `MaxAgeDays[<key>]`;
2. `site.ini [FileSettings]` `LogFileMaxSize`, `LogFileRotateCount`, `LogFileCompress`, `LogFileMaxAge`;
3. 2 MB, 3 archives, not compressed, no age limit.

The page shows where each limit comes from (file, section, setting). The work is done by the log manager
(`expLogManagerFactory::get()`: `expLogManager` when it is installed, else a built-in fallback with the same API).
Scheduled rotation comes with the log manager: the cronjob part `logs` (`cronjobs/explogrotate.php`,
`[CronjobPart-logs]`), which applies the size and age limits and is in no part that runs by default.

Audit: a rename or copy as `system.log.rotate` (compressing with `after.action` `compress`), emptying as
`system.log.truncate`, removing as `system.log.delete`.

### Downloading every log

**Download all logs** (an orange button at the start of the page, `[ActionSettings] Download`) sends one archive of
every log the viewer lists, the web server's included, named `logs-<host>-<YYYYMMDD-HHMM>.zip` (a `.tar.gz` where the
PHP zip extension is missing). With **Include archives** (ticked by default) their rotations come along. Paths inside
are relative: `var/log/error.log`, `var/<site>/log/storage.log`, and `webserver/access_ssl_log` for a log configured by
key. A `MANIFEST.txt` lists each file with its size, last write (UTC) and sha256 of what is stored.

- The contents are masked line by line as the viewer shows them; a compressed rotation is unpacked to be masked.
  **Unredacted**, offered only with `setup/unmasklogs` and shown with a warning, stores the files as written (a
  compressed rotation as it is).
- It is a POST with the form token on a page that needs `setup/managelogs`, and it is recorded in the audit as
  `system.log.download` with `after.bundle`, the number of files, the bytes and whether it was unredacted.
- The logs read may sum to at most `[ActionSettings] MaxBundleBytes` (default 256 MB); over it the page says how much
  there is and nothing is built. The archive is built as a stream (memory stays flat) in its own directory under
  `var/tmp/logbundle`, sent in pieces and removed afterwards, also when the visitor leaves early.

## Log health

**Setup > Log files > Log health** (`setup/logs`; `/setup/logshealth`) shows at a glance:

- the disk all logs and their archives use, against `[HealthSettings] DiskBudget` (default 256 MB; a warning from
  80 %, an alert over it; 0: no budget);
- the fastest growing logs, and the logs over their size limit (with a link to manage them, for `setup/managelogs`);
- the files the web server's user cannot write, with a warning when the logs have mixed owners (for example some
  written by root from the command line and some by the web server's user), which stops rotation and new lines;
- the newest error of each log, masked, linked to its line (the log's permalink with `(offset)/N`, paused);
- read only: the rotation settings in effect per log (maximum size, archives kept, compression, age), each with the
  INI file, section and setting it comes from.

## Error digest

**Setup > Log files > Error digest** (`setup/logs`; `/setup/logsdigest`, `/setup/logsdigest/<key>` for one log,
`/(hours)/N` for another window, 1 to 168) lists the distinct errors and warnings of the last `[DigestSettings] Hours`
(24) of the kernel logs: each message masked, then normalized (numbers, hexadecimal values, ids, quoted values, paths
and `ERR-` references set aside), grouped and counted, most frequent first, at most `MaxMessages` (50) per log, with
its most severe level and when it was last seen. Each group links to the log's view filtered by level, a search for
its text and the start of the window; its time links to the newest such line. Each log is entered at the start of the
window by a binary search on its times and read for at most `MaxBytesPerFile` (8 MB); a log read only in part says so.

**Mark as known** hides a group for the viewer (a POST with the form token; the user preference
`admin_logviewer_known`, at most 200 groups); `/(known)/1` lists the known ones too, with **List again**.

**Per log**, `[DigestSettings] LogLevels[<key>]` lists other levels (a comma list; `LogLevels[404]=error,warning,notice`
lists the not-found answers, which are notices) and `GroupBy[<key>]` groups by something other than the message:
`category-rule` (the first word, `<category>/<rule>`: `security.log`), `reason` (the `reason=` field with ids and
paths set aside: `404.log`), `reason-kind`, `uri` (the method and the path without the query) or `message` (the
default). A log grouped another way says so above its table. See [Request logs](request-logs.md).

A group of a log grouped by `uri` also links with the request filter: the group's path up to its first placeholder,
masked as the group is (`GET /blog/#` links with `(uri)/~2Fblog~2F`), which the search compares with the request as
the viewer is shown it. A path of no more than `/` adds no filter. `expLogDigest::link( $group, $since, $grouping )`
takes the log's grouping; without it a link is built as before.

## Archives

A rotation opens in the same viewer, read only, under its own permalink: `/setup/logs/view/<key>/(archive)/N` (`.N`,
or `.N.gz` when there is no plain one). `(rotation)/N` is still read as the same. An archive is never followed (no
polling); it can be searched, downloaded and, with `DeleteArchives`, removed, never rotated or emptied.

## Copy messages

**Copy messages** (the first button of the row under the file chooser) copies the lines the viewer shows as plain
text, as a selection of them by hand would: in display order, one entry a line (time, level, file, source, request,
message; a wrapped message keeps its line breaks, an unwrapped one shows them as ↵), masked as shown. While following,
it copies every line kept; while paused, the lines on screen. It uses the clipboard of the browser (with a fallback for
pages that are not served over HTTPS) and says "12 lines copied" in a short message and to screen readers.

## Which files

The viewer opens only files it lists, and it lists only these (`expLogFileLocator`):

- the files **directly in** a directory of `[LogViewerSettings] Directories[]` (relative to the installation root;
  `{log_dir}` is the directory eZDebug writes to, `*` one directory name, so `var/*/log` is every site's log
  directory); subdirectories are never walked, so the audit trail in `var/log/audit` stays with the audit console;
- whose name matches `FilePatterns[]` (`*.log`, `*.log.N`, `*.log.N.gz`, `*.log.gz`) and no `ExcludePatterns[]`.

and the logs configured by key in `LogList[]` (see [Web server logs](#web-server-logs)) with their rotations.

The page asks for a log by its key ([Permalinks](#permalinks)), which only finds a file of that list. The feed and the
actions ask for a file by its id: the path relative to the root (`var/log/error.log`), or `@<key>/<file name>` for a
configured log (only its file and its rotations). A path is refused when it has
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
| `KernelLogs[]` | `error.log`, `warning.log`, `notice.log`, `debug.log`, `strict.log`, `deprecated.log`, `auth.log`, `cronjob*.log`, `storage.log`, `setup.log`, `mail.log`, `trashpurge.log`, `404.log`, `security.log` | the names of the Kernel group |
| `DefaultKey` | `error` | the log `/setup/logs` opens (a key, see [Permalinks](#permalinks)) |
| `DefaultFile` | `var/log/error.log` | the file opened when `DefaultKey` finds none |
| `LogList[]` | `webserver-access`, `webserver-error` | the logs configured by key (see [Web server logs](#web-server-logs)) |
| `LogPath[<key>]` | (none: off) | the file of a configured log; a key without one is not shown |
| `LogLabel[<key>]` | `Web server access log`, `Web server error log`, `Not found requests (404)` (404), `Security (request classifier)` (security) | the name the navigator and the digest show, translated in the context `kernel/logviewer`; it names a configured log or any log of the log directories by its key |
| `LogGroup[<key>]` | `webserver` | the group: `kernel`, `site`, `extensions`, `webserver` |
| `FileLevels[]` | `error.log*;error`, `*error*.log*;error`, `warning.log*;warning`, `strict.log*;warning`, `deprecated.log*;notice`, `notice.log*;notice`, `debug.log*;debug`, `404.log*;notice`, `security.log*;warning` | the level of lines that name none, by file name |
| `[TailSettings] InitialBytes` | `65536` | what the page shows when opened |
| `MaxBytesPerPoll` | `262144` | the most one poll reads |
| `PollInterval` | `1000` | ms between polls while lines arrive |
| `MaxPollInterval` | `15000` | the longest interval of the backoff |
| `MaxLinesKept` | `5000` | the entries the page keeps |
| `ChunkCacheTTL` | `10` | seconds the last chunk of a file is kept; 0 switches the cache off |
| `ChunkCacheMaxBytes` | `262144` | the most bytes of a cached chunk |
| `WrapLines` | `enabled` | long lines wrapped for a viewer who has not chosen (the preference `admin_logviewer_wrap`) |
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
| `Compress` | `disabled` | compress archived rotations (see [Managing logs](#managing-logs)) |
| `[RotationSettings] MaxSize[<key>]`, `Keep[<key>]`, `Compress[<key>]`, `MaxAgeDays[<key>]` | (none: `site.ini [FileSettings]`) | the limits of one log (see [Managing logs](#managing-logs)) |
| `[HealthSettings] DiskBudget` | `268435456` | the disk all logs may use together (see [Log health](#log-health)); 0: none |
| `[DigestSettings] Hours`, `MaxMessages`, `MaxBytesPerFile`, `Levels[]` | `24`, `50`, `8388608`, `error`, `warning` | the error digest (see [Error digest](#error-digest)) |
| `[DigestSettings] LogLevels[<key>]`, `GroupBy[<key>]` | `LogLevels[404]=error,warning,notice`; `GroupBy[404]=reason`, `GroupBy[security]=category-rule` | per log: the levels listed and the grouping (`message`, `category-rule`, `reason`, `reason-kind`, `uri`) |
| `MaxDownloadBytes` | `52428800` | the largest range of one download |
| `MaxBundleBytes` | `268435456` | the most bytes of logs one "Download all logs" archive holds |
| `[RateLimitSettings] Requests`, `Window` | `120`, `60` | feed requests per session and window (seconds) |
| `[ViewerAuditSettings] ReadInterval` | `600` | seconds between two `system.log.read` of the same file and session |

Numeric settings are kept within safe bounds whatever the file says (for example `PollInterval` at least 250 ms,
`MaxBytesPerPoll` at most 4 MB).

## For developers

- Classes in `kernel/classes/logviewer/`: `expLogViewerConfig` (settings), `expLogFileLocator` (allow-list, groups,
  keys, configured logs), `expLogPermalink` (the permalinks: `build()`, `state()`, `encode()`, `decode()`),
  `expLogTail` (offset reads, chunk cache, `.gz`), `expLogParser` (entries, levels, links), `expLogSearch` (bounded
  search, time seek), `expLogRedactor` (masks), `expLogRateLimit`, `expLogFileStats` (line rate), `expLogActions`
  (download, rotate, empty, remove) and the facade `expLogViewer` (policies, audit, the feed's answers).
- Views: `setup/logs` (the page; `setup/logs/view/<key>` with the view parameters of [Permalinks](#permalinks)),
  `setup/logsfeed` (JSON: `/(action)/tail|search|related|reference|files`, every option a view parameter),
  `setup/logsaction` (POST). Template fetch: `fetch( 'setup', 'log_files' )`, the navigator (empty without
  `setup/logs`).
- A log of your own extension appears in **Extensions** when it is written to an allowed directory with a matching
  name (`var/log/myextension.log` through `eZLog::write()`); give it a level with `FileLevels[]`, or write
  `Error:` / `Warning:` at the start of its messages.
- Tests (no database): `tests/tests/kernel/logviewer/`: the parser, offset tailing and the chunk cache, masking, path
  safety, the regular expression limit, the search, the rate limit and the actions on throwaway files; the keys and
  the configured logs (`expLogKeyTest`), the permalinks and the old links (`expLogPermalinkTest`), the web server's
  formats (`expLogWebServerFormatsTest`).

## Demo mode lines

On a public demo (environment variable `EXP_DEMO_MODE=1`) every process writes one `demo/mode on pid=<pid> sapi=<sapi> ...` line to `security.log`, in the request classifier's format, so **Setup > Log files** groups them under `demo/mode`. On a real site such a line means the variable reached it. [Demo mode](../../bc/6.0/demo-mode.md).

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
