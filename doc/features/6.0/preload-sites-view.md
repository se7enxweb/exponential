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
