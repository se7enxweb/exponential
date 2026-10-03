# Preload Sites (administration interface)

Warm every page of a site from the administration interface and watch it
happen. It is the browser counterpart of the command line preloader described in
[Site cache preloader](../../bc/6.0/preload.md), and it uses the same crawler.

## Use it

1. Open **Setup > Preload Sites** (`/setup/preload`).
2. Choose the **Site to warm**.
3. Set the **Page limit** and **Link depth**.
4. Press **Start preloading**. Progress is written live into the output panel;
   **Stop** ends the run.

| Field | Default | Meaning |
|---|---|---|
| Site to warm | the current siteaccess | Which siteaccess to crawl. |
| Page limit | 250 | The most pages one run fetches. A run that hits the limit says so and how many pages were still queued. |
| Link depth | 3 | How far from the site root links are followed. |

Command line equivalent:

```bash
php bin/php/preload.php --help --allow-root-user
```

## Behaviour worth knowing

- **It warms the site you chose and stays inside it.** One host can serve
  several siteaccesses. The crawler works out the URL prefix that selects the
  chosen siteaccess (from `site.ini [SiteAccessSettings] MatchOrder` with
  `HostMatchMapItems` or `HostUriMatchMapItems`, or the first URL segment) and
  follows only pages under it. Before this, choosing a second site warmed the
  default site and reported success.
- **Broken links name their pages.** A broken target is reported once, with the
  pages that link to it. A link in the site menu or a template is on every page,
  so the report keeps a bounded number of referrers and says how many more
  there were. A reference to something that is not a page (an anchor, a
  fragment) is no longer reported as broken.
- **A caller can keep the pages it fetches**, which the static cache generator
  uses to store them (see [Static cache generator](static-cache-generator.md)).

## Runs in the background

Since 28 September 2026 **Start preloading** starts the run as a background
process instead of holding one web request open for the whole crawl. The old way
read the progress as a stream; a pooled application server such as Velocity
hands an answer over only when it is complete and ends a request after its time
limit, so the stream brought nothing and ended in a 504 after 30 seconds, and the
button did nothing visible. A proxy that buffers streamed answers behaves the
same. The new way works behind any web server and proxy.

- `setup/preloadjob` starts `bin/php/preloadjob.php` detached (`setsid`) with the
  pages, depth and siteaccess you chose, answers its events after a given line
  as JSON, and stops a run on request (it ends after the page it is fetching).
  Starting and stopping need the form token, like any change; a start without it
  is refused.
- Each run keeps its events in `var/<var dir>/preload/<id>.jsonl`; the newest 20
  runs are kept. The run is handed only pipes, and the page says so when a run
  stopped without finishing.
- The console polls once a second and shows the events exactly as before.
  **Stop** ends the run itself, not only the page's view of it.
- The server needs `proc_open`, `setsid` (`/usr/bin/setsid` or `/bin/setsid`) and
  the PHP command line; without them the console says so (see
  `kernel/classes/exppreloadjob.php`).
- The console now shows the broken-link report and the summary at the end of a
  run (a JavaScript variable named `tr` hid the translation helper and stopped the
  console just before them); an event the page cannot show is written as an error
  and polling carries on.
- `setup/preloadstream` stays for anything that uses it.

Check a run from the shell while it is going: `ls -t var/*/preload/ | head`.

## Access

The view is the `preload` function of the `setup` module; give a role the
policy *setup / preload*. The progress stream (`setup/preloadstream`) is a
view of the same module, so it goes through the same siteaccess and policy
checks as the page. The menu entry is `settings/menu.ini`
`Links[preload]=setup/preload` (label `Preload Sites`, `PolicyList_preload[]=setup/preload`). The views are `preload`, `preloadjob` (starts and stops the background run) and `preloadstream`.

## Related

- [September 2026, first half: Preload Sites](../../history/2026/2026-09a.md#13-september-caches-you-can-see-cronjobs-you-can-run) and [the 15 September fixes](../../history/2026/2026-09a.md#15-september-paging-everywhere)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
- [Site cache preloader](../../bc/6.0/preload.md) (reading the output, cron use, troubleshooting)
- [HTTP/2 and cache warming](../../bc/6.0/http2-and-cache-warming.md)
