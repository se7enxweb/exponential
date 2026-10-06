# Preload Sites (administration interface)

> **Since 6 October 2026** the page is redesigned: one run at a time, live progress, the last runs with their
> failures, a dry run and an image check. The current guide is [Preloading caches](../../guides/preloading-caches.md).

This page is for administrators who want a site's caches warm after a deploy or a cache clear, and who want a list of
broken links as a bonus. **Setup > Preload Sites** crawls every page of a site from the admin and shows the progress
live. It is the browser counterpart of the command line preloader described in
[Site cache preloader](../../bc/6.0/preload.md), and it uses the same crawler.

## Use it

1. Open **Setup > Preload Sites** (`/setup/preload`).
2. Choose the **Site to warm**.
3. Set the **Page limit** and **Link depth**.
4. Press **Start preloading**. Progress is written live into the output panel. **Stop** ends the run.
5. At the end, read the summary and the broken-link report.

| Field | Default | Meaning |
|---|---|---|
| Site to warm | the current siteaccess | Which siteaccess to crawl |
| Page limit | 250 | The most pages one run fetches. A run that hits the limit says so, and how many pages were still queued. |
| Link depth | 3 | How far from the site root links are followed |

The command line equivalent:

```bash
php bin/php/preload.php --help --allow-root-user
```

To check a run from the shell while it is going:

```bash
ls -t var/*/preload/ | head
```

## Access

Give a role the policy **setup / preload** (the `preload` function of the `setup` module). The views are `preload`
(the page), `preloadjob` (starts and stops the background run) and `preloadstream` (the older progress stream). All are
views of the same module, so they go through the same siteaccess and policy checks as the page. The menu entry is
`settings/menu.ini` `Links[preload]=setup/preload` (label `Preload Sites`, `PolicyList_preload[]=setup/preload`).

## Behaviour worth knowing

- **It warms the site you chose and stays inside it.** One host can serve several siteaccesses. The crawler works out
  the URL prefix that selects the chosen siteaccess (from `site.ini [SiteAccessSettings] MatchOrder` with
  `HostMatchMapItems` or `HostUriMatchMapItems`, or the first URL segment) and follows only pages under it. Before
  this, choosing a second site warmed the default site and reported success.
- **Broken links name their pages.** A broken target is reported once, with the pages that link to it. A link in the
  site menu or a template is on every page, so the report keeps a bounded number of referrers and says how many more
  there were. A reference to something that is not a page (an anchor, a fragment) is no longer reported as broken.
- **A caller can keep the pages it fetches.** The static cache generator uses this to store them (see
  [Static cache generator](static-cache-generator.md)).

## It runs in the background

Since 28 September 2026, **Start preloading** starts the run as a background process instead of holding one web
request open for the whole crawl. The old way read the progress as a stream. A pooled application server such as
Velocity hands an answer over only when it is complete, and ends a request after its time limit, so the stream brought
nothing and ended in a 504 after 30 seconds; the button did nothing visible. A proxy that buffers streamed answers
behaved the same. The new way works behind any web server and proxy.

- `setup/preloadjob` starts `bin/php/preloadjob.php` detached (`setsid`) with the pages, depth and siteaccess you
  chose, answers its events after a given line as JSON, and stops a run on request (it ends after the page it is
  fetching). Starting and stopping need the form token, like any change; a start without it is refused.
- Each run keeps its events in `var/<var dir>/preload/<id>.jsonl`; the newest 20 runs are kept. The run is handed only
  pipes, and the page says so when a run stopped without finishing.
- The console polls once a second and shows the events exactly as before. **Stop** ends the run itself, not only the
  page's view of it.
- The server needs `proc_open`, `setsid` (`/usr/bin/setsid` or `/bin/setsid`) and the PHP command line. Without them
  the console says so (see `kernel/classes/exppreloadjob.php`).
- The console now shows the broken-link report and the summary at the end of a run. (A JavaScript variable named `tr`
  hid the translation helper and stopped the console just before them.) An event the page cannot show is written as an
  error, and polling carries on.
- `setup/preloadstream` stays for anything that uses it.

## Related pages

- [Site cache preloader](../../bc/6.0/preload.md) (reading the output, cron use, troubleshooting)
- [HTTP/2 and cache warming](../../bc/6.0/http2-and-cache-warming.md), [static cache generator](static-cache-generator.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
- History: [September 2026, first half: Preload Sites](../../history/2026/2026-09a.md#13-september-caches-you-can-see-cronjobs-you-can-run), [the 15 September fixes](../../history/2026/2026-09a.md#15-september-paging-everywhere)
