# Preloading caches: warm a site before visitors arrive

This guide explains the cache preloader of Exponential: **Setup > Preload** (`/setup/preload`) and the command
`php bin/php/preload.php`. They are the same run. It requests the pages of a site as an anonymous visitor would, so
the caches are warm before the first visitor arrives, and it lists the links that are broken with the pages that link
to them.

It is written for administrators and operators. Every page, field, option and file below was checked against the code
of this repository on 6 October 2026; the files are named in [References](#references).

[Guides](README.md) · Related: [Operating a site](operating-a-site.md) · [Cronjobs](cronjobs.md) ·
[Deploying](deploying.md) · [Site cache preloader (upgrade notes)](../bc/6.0/preload.md)

## In short

- A run starts at the **section pages** of a site (its root and the sections of `site.ini [SiteSettings]
  URLTranslationKeyword`), then follows the links of the site, up to a **page limit** and a **link depth**.
- It warms what rendering a page makes: the **page view cache**, the **image aliases** the page shows and the
  **compiled templates**. These are files of the installation, shared by Apache with PHP-FPM and by Exponential
  Velocity.
- **One run at a time.** A run holds a lock for as long as it runs, whether it was started from the page, the shell or
  cron. A second one is refused and says so.
- From the page a run **never runs inside the web request**: it is started in the background, and the page follows it.
- Every run is **recorded**: duration, pages warmed, image aliases made, broken links with their addresses, status
  codes and the pages that link to them. The page lists the last runs from the page, the shell and cron alike.
- A **dry run** shows the address, the starting pages and the limits, and requests nothing.

## 1. The page

Open **Setup > Preload Sites** (`/setup/preload`). The page has, from top to bottom:

| Part | What it shows |
|---|---|
| Status bar | **Running** with the site, when and from where it started, a progress bar (pages warmed of the page limit), the broken links so far and the address being requested; or **Idle** with the last run and how it ended. **Stop** ends a run after the page it is requesting. |
| Output | While a run goes on, its output line by line, then the figures of the run (pages warmed, image aliases made, broken links, images checked, seconds) and the table of broken links. |
| Start a preload | The form: site, page limit, link depth, what to warm, **Start preloading** and **Dry run**. |
| Dry run | After **Dry run**: the siteaccess, the address, the prefix that selects the siteaccess, the limits, the starting pages and the same command for the shell. |
| Which caches it warms | Which server answers the run's requests, and what that means for Apache/PHP-FPM and Velocity. |
| From the shell or cron | The command for the choices in the form, a crontab line, and where runs are kept. |
| Last runs | The last ten runs, newest first. A run with failures has a row that opens to the addresses that failed, their status codes and the pages that link to them. |

The page works without JavaScript: Start, Dry run and Stop post the form, and **Refresh** shows how a run is doing.
With JavaScript the page follows a run by itself, once a second, and you can leave it and come back: a run that is
going on is picked up again when the page is opened.

### The form

| Field | Default | Meaning |
|---|---|---|
| Site to warm | the first of `RelatedSiteAccessList` | The siteaccesses of `site.ini [SiteAccessSettings] RelatedSiteAccessList`, without the one the page is served from (an administration siteaccess warms nothing a visitor sees). Each is shown with the address the run starts at. |
| Page limit | 250 | The most pages one run requests, 1 to 5000. A run that reaches it says how many addresses were still queued. |
| Link depth | 3 | How many links from a starting page are followed, 0 to 10. 0 requests the starting pages only. |
| Also check the images on each page | off | After the pages, request every image the warmed pages show, once (a `HEAD` request), and list the missing ones with the pages that show them. |

Only a siteaccess the installation serves can be chosen, and the limits are whole numbers within their range; nothing
else from the form reaches a command line. The command is started with an argument list, never through a shell.

## 2. What a run warms, and on which server

A run requests each page at the **address of the site**: `SiteURL` of the siteaccess, with the prefix that selects the
siteaccess on that host (section 4). The server that answers that address renders the page: on alpha, Apache with
PHP-FPM.

- The page view cache (`var/<var dir>/cache/content`), the image aliases (`var/<var dir>/storage/images`) and the
  compiled templates are files of the installation. Apache/PHP-FPM and Velocity share them, so a run warms them for
  both, whichever server answers.
- Exponential Velocity keeps a response cache of its own, in front of its workers. It keeps an anonymous page for
  `velocity.ini [CacheSettings] DefaultTtl` seconds (30 by default), so warming it ahead of visitors does not last.
  The page says when Velocity is running and how long its cache keeps a page.
- Pages for signed-in users are not warmed: the run is an anonymous visitor.
- Only the page's own markup is read: links and images inside `<script>`, `<template>`, `<style>` or comments,
  and values that hold a quote, a JavaScript or template expression or whitespace, are not addresses and are skipped.
- Requests to addresses outside the site are never made: links to another host or to another siteaccess on the same
  host are not followed, and neither are module views (`/content/edit`, `/setup`, `/user/login` ...), Velocity's
  own panel (`/Q/...`) or files
  (images, scripts, documents).

**Image aliases made** is the number of image files that appeared in the storage while the run went on: the aliases
the rendered pages created. It is counted on disk, so it is shown as *not counted* with a clustered file handler
(`file.ini [ClusteringSettings] FileHandler` other than `eZFSFileHandler` or `eZFS2FileHandler`). A site that was
warm already makes none.

## 3. From the shell and from cron

```bash
# what a run would do, without requesting anything
php bin/php/preload.php --siteaccess=site --dry-run

# warm the site of the siteaccess "site"
php bin/php/preload.php --siteaccess=site

# a small run: the starting pages and the pages they link to, at most 20
php bin/php/preload.php --siteaccess=site --max-pages=20 --max-depth=1

# with the image check
php bin/php/preload.php --siteaccess=site --images
```

| Option | Meaning |
|---|---|
| `--siteaccess=<name>` | The siteaccess whose site is warmed (the standard option of every command). |
| `--target=<name>` | Another siteaccess than the one the command runs as. |
| `--max-pages=<n>` | The page limit, default **1000** from the shell (250 on the page), at most 5000. |
| `--max-depth=<n>` | The link depth, default 3. |
| `--images` | Also check the images the warmed pages show. |
| `--dry-run` | Print the address, the starting pages and the limits; request nothing. |
| `-q` | Print nothing. The run is still recorded and listed on the page. |

The command prints each page as it is requested and the broken links at the end. Its **last three lines** say what the
run did, for a log that keeps only its tail:

```
Preload finished in 182.4s: run 1c9f49b30d7a51ac, listed in Setup > Preload.
Pages warmed 167, broken 0, denied 0, skipped 3.
Image aliases made 480.
```

Exit codes: 0 when the run finished or was stopped (broken links are a finding, not a failure), 1 when it failed (no
`SiteURL`, an unknown siteaccess, an error), 2 when another preload was running and this one did not start.

For cron, after the nightly cache clear for example:

```
30 4 * * * cd /path/to/exponential && php bin/php/preload.php --siteaccess=site -q
```

The page shows this line for the choices in its form. The command needs the PHP command line and the curl extension;
it no longer needs `wget` or the `curl` program, and writes nothing to `/tmp`.

## 4. The address of a siteaccess

A run has to reach the siteaccess that was chosen, not just its host. The address is worked out from `site.ini`
alone, the way a request is matched (`expPreloadAddress`):

1. The base is `SiteURL` of the siteaccess (its own `settings/siteaccess/<name>/site.ini.append.php`, else the global
   one), with `https://` added when it has no scheme.
2. The address without a prefix is tried first. Then each prefix the matching offers: `/<name>` for
   `URIMatchType=element`, the uri of a `URIMatchMapItems[]` entry for `URIMatchType=map`, the uri of a
   `HostUriMatchMapItems[]` entry.
3. Each address is matched as a request would be: the methods of `[SiteAccessSettings] MatchOrder` in turn, then
   `DefaultAccess`. The first address that reaches the siteaccess is used.

Host map entries are read with their optional third field, `HostMatchMapItems[]=host;siteaccess;method`, where the
method is `strict`, `start`, `end` or `part`, and `HostMatchMethod` (default `strict`) applies to an entry without one.
So `example.com;site;start` sends a test host `example.com.test.local` to `site` for the preloader as for a visitor.

When no address reaches the siteaccess, the run uses `/<name>` (when `uri` is in `MatchOrder`) as before, and the dry
run and the output warn that the pages warmed may be those of another siteaccess. Check the `SiteURL` and the match
settings of that siteaccess.

A `SiteURL` that already ends in the prefix (`demo.example/site`) does not get it again. The site a run stays in is
everything below the path of `SiteURL` plus the prefix (`/site/...` there, `/bold/...` for a uri prefix, the whole
host but other siteaccesses' prefixes for a site matched by host); links to `/admin/...` and other siteaccesses are not
followed.

The section pages of phase 1 are the root, the sections of `URLTranslationKeyword`, and the top level pages the
starting pages link to (`/site/fitness`, `/fitness`).

## 5. Runs, the lock and the files

Runs are kept in `var/<var dir>/preload/`:

| File | Content |
|---|---|
| `<id>.json` | The status of the run: state, siteaccess, address, limits, process, start and end, duration, counts, image aliases made, the address being requested, the first 50 failures. |
| `<id>.jsonl` | The events of the run, one JSON line each: what the output shows. |
| `<id>.stop` | Asks the run to stop; it is looked at after each request. |
| `run.lock` | The lock. A run holds an exclusive lock on it while it runs. |

The last 20 runs are kept; a run that is going on is never removed. The lock goes with the process: a run that dies
leaves no lock behind and nothing has to be removed by hand. A run whose process is gone is listed as **Ended without
finishing**, and one whose process never started as **Failed** after a minute.

States: **Starting**, **Running**, **Finished**, **Stopped** (from the page), **Failed** (with the reason) and
**Ended without finishing**.

## 6. Access

Give a role the policy **setup / preload**. The views are `setup/preload` (the page), `setup/preloadjob` (the JSON
the page reads: a run's progress and status, the list of runs, and start, stop and dry run for scripts) and
`setup/preloadstream` (the older progress stream, kept; it starts nothing). Every `POST` carries the form token.

`setup/preloadjob` answers:

| Request | Answer |
|---|---|
| `POST Action=start, SiteAccess, MaxPages, MaxDepth[, Images]` | `{"id": "..."}`, 409 while a run is going on, 400 for an unknown siteaccess |
| `POST Action=dryrun, SiteAccess, MaxPages, MaxDepth[, Images]` | `{"plan": {...}}` |
| `POST Action=stop, JobID` | `{"stopping": true|false}` |
| `GET setup/preloadjob/status` | `{"running": status or false, "runs": [...]}` |
| `GET setup/preloadjob/<id>/<offset>` | `{"events": [...], "offset": n, "done": bool, "status": {...}}` |

## 7. When something does not behave as expected

| What you see | Why, and what to do |
|---|---|
| **A preload is already running** | One runs at a time. Wait, or press **Stop**. A run from the shell or cron counts too. |
| The run warms the wrong site | The dry run shows the address. If it warns that the matching does not reach the siteaccess, correct `SiteURL` or the match settings of that siteaccess. |
| **Failed**: the preloader cannot start | The web server cannot start the PHP command line (`proc_open`, `setsid` or the PHP binary is missing or hidden by `open_basedir`). Run the command from the shell instead. |
| **Ended without finishing** | The process was killed (a restart of the machine, an out-of-memory kill). Start it again. |
| Many **denied** pages | 401 and 403 are counted apart: pages behind a login. They are not broken links. |
| Image aliases made: *not counted* | A clustered file handler keeps the files elsewhere; the run still warms them. |
| A run is slow | Each page is rendered by the server; a cold site takes as long as its pages take to render. Lower the page limit or the depth, or run it from cron at a quiet hour. |

## References

- `kernel/classes/exppreloadjob.php` (`expPreloadJob`): starting a run in the background, the run itself
  (`execute()`), the dry run (`plan()`), the sites offered, the image alias count, the servers.
- `kernel/classes/exppreloadaddress.php` (`expPreloadAddress`): the address and prefix of a siteaccess, the matching,
  the starting pages.
- `kernel/classes/exppreloadlock.php` (`expPreloadLock`), `kernel/classes/exppreloadhistory.php`
  (`expPreloadHistory`): the lock and the runs.
- `kernel/setup/exppreloadrunner.php` (`expPreloadRunner`): the crawler, also used by the static cache generator.
- `kernel/private/classes/views/setup/preload.php`, `preloadjob.php`, `preloadstream.php`: the views.
- `kernel/private/classes/commands/preload.php`, `preloadjob.php`: `bin/php/preload.php` and the background run.
- `design/admin/templates/setup/preload.tpl`, `preload_exp_style.tpl` (and the same in `design/admin4`).
- Tests: `tests/tests/kernel/classes/preload/` (no database), `expPreloadRunnerLinksTest`, `ExpPreloadStartUrlsTest`.
