# Site Cache Preloader — `bin/php/preload.php`

**Introduced:** Exponential CMS 6.0.15  
**Location:** `bin/php/preload.php`  
**Also available as:** `php bin/php/console exp:preload`  
**Type:** PHP CLI script — site cache warmer

---

## What is it?

`preload.php` warms your Exponential CMS page caches from the command line.

When Exponential generates a page for the first time it is slower than usual
because the cache is empty. This script fetches pages on your behalf — before
any real visitor arrives — so the cache is already warm and every page loads
instantly.

**When to run it:**

- After clearing all caches (`exp:ezcache --clear-all`)
- After a new deployment or code update
- After a scheduled cache expiry
- On a cron job to keep caches continuously warm on high-traffic sites

---

## Prerequisites

Run all commands from the **Exponential CMS root directory**:

```bash
# You should see index.php in the listing
ls index.php
```

You need:

| Tool | Check with | Why |
|---|---|---|
| PHP 7.4+ | `php -v` | Runs the script |
| curl | `curl --version` | Phase 1 — fetches section pages |
| wget | `wget --version` | Phase 2 — spiders the full site |

Your Exponential site must be **running and reachable** over HTTP/HTTPS. The
script reads `SiteURL` from `settings/site.ini` to know where to connect.

---

## Quick-start

```bash
# Warm caches for the default siteaccess configured in site.ini
php bin/php/preload.php

# Or run it through the console (identical result)
php bin/php/console exp:preload
```

That is all you need for most sites.

---

## Specifying a siteaccess

A siteaccess is a named configuration profile in Exponential CMS (for example a
front-end user site, an admin site, or a mobile site). If you have more than one
you can target a specific one:

```bash
php bin/php/preload.php --siteaccess=sevenx_site_user

# Same command via the console dispatcher
php bin/php/console exp:preload --siteaccess=sevenx_site_user
```

Not sure what your siteaccess names are? Check:

```bash
grep AccessPath settings/siteaccess.ini
# or look in
ls settings/siteaccess/
```

---

## What the script does — step by step

### Phase 1 — Warm section pages

The script reads `[SiteSettings] URLTranslationKeyword` from `site.ini` to
build a list of section URLs (e.g. `/articles/`, `/products/`, `/blog/`).
Each URL is fetched once with `curl`. For every page it prints:

```
  [1/4]  https://example.com/articles/
         [ 200 OK ]  wall: 312ms  ok  size: 84.2 kB
         db        queries: 14  time: 28ms
         kernel    init: 42ms  run: 245ms  get-content: 190ms  total: 310ms
         memory    peak: 18.4 MB
```

- **200 OK** — page fetched and cached successfully (green)
- **3xx** — redirect (cyan)
- **4xx** — not found or permission denied (yellow warning)
- **5xx** — server error (red, first few error lines shown inline)

The **wall** time is how long your browser would have waited for this page.
After the first fetch that number drops dramatically because the cache is warm.

### Phase 2 — Spider the full site

The script launches `wget` in spider mode (it visits pages but does not save
them) with recursion depth 3, starting from your site root. This touches every
page linked from your home page, up to three levels deep.

For every URL crawled:

```
  ✓  /articles/my-post                 14.2 kB  #47
  ▸  print  /articles/my-post          3.1 kB   #48
  ⬇  download  /content/download/…           #49
```

| Icon | Meaning |
|---|---|
| `✓` | Regular content page — fully cached |
| `↻` | Page delivered via a redirect chain |
| `▸ print` | Print-layout variant (`/layout/set/print/…`) |
| `⬇ download` | Binary file download (`/content/download/…`) |
| `⊘` | Auth-protected resource — skipped (summarised) |
| `✗ BROKEN LINK` | Link found on a page but the target returned an error |

### Final summary

```
  ┌─────────────────────────────────────────┐
  │  Pages cached      204                  │
  │  Auth skipped        3 resources        │
  │  Broken links        1                  │
  └─────────────────────────────────────────┘
```

---

## Reading the output

### Page speed tiers

The **wall time** column tells you how fast a page responded before caching:

| Color | Label | Time | What it means |
|---|---|---|---|
| Green | `blazing` | < 200 ms | Excellent — already cached or trivially fast |
| Green | `fast` | 200–400 ms | Good — well within acceptable range |
| Yellow | `ok` | 400–700 ms | Acceptable — room for improvement |
| Yellow | `slow` | 700–1200 ms | Needs attention |
| Red | `SLOW!` | > 1200 ms | Investigate caching configuration or queries |

After the preload completes, re-fetching any of these pages should consistently
show `blazing` or `fast` because the rendered HTML is now cached.

### Broken links

A broken link means one of your pages contains a hyperlink whose target does not
exist or returns an error. These are worth fixing but do not prevent caching —
the rest of the site is still fully warmed.

---

## Running on a cron job

To keep caches continuously warm, run the preloader on a schedule. Edit your
crontab:

```bash
crontab -e
```

Add a line like this (runs every 6 hours, logs output):

```
0 */6 * * * cd /path/to/exponential-root && php bin/php/preload.php --siteaccess=sevenx_site_user >> /var/log/ezpreload.log 2>&1
```

Replace `/path/to/exponential-root` with the actual path to your site and
`sevenx_site_user` with your front-end siteaccess name.

---

## Running after a cache clear

The most common workflow:

```bash
# 1. Clear all caches
php bin/php/console exp:ezcache --clear-all --allow-root-user

# 2. Immediately re-warm them
php bin/php/console exp:preload --siteaccess=sevenx_site_user --allow-root-user
```

---

## Tips

**Redirect to a log file** (useful in CI/CD or when running unattended):

```bash
php bin/php/preload.php --siteaccess=sevenx_site_user > /tmp/preload.log 2>&1
```

Colors are automatically suppressed when stdout is not a terminal — the log
file will contain clean plain text.

**See only broken links** from a previous run:

```bash
grep "BROKEN LINK" /tmp/preload.log
```

**Run just Phase 1** (section pages only, no full spider):  
There is no separate flag for this yet. To skip Phase 2, interrupt the script
with **Ctrl+C** after Phase 1 completes — Phase 1 output is flushed
immediately so no work is lost.

---

## Troubleshooting

**`Cannot determine site URL: SiteSettings.SiteURL is not set`**  
Open `settings/override/site.ini.append.php` (or the equivalent for your
siteaccess) and confirm that `[SiteSettings]` contains a `SiteURL` line:

```ini
[SiteSettings]
SiteURL=example.com
```

**`Failed to launch wget — is it installed and on $PATH?`**  
Install wget:

```bash
# Debian / Ubuntu
apt-get install wget

# RHEL / CentOS / Rocky
yum install wget
```

**`Running scripts as root may be dangerous`**  
Exponential's startup system warns when you run as `root`. Append
`--allow-root-user` to acknowledge:

```bash
php bin/php/preload.php --allow-root-user
```

**Phase 2 produces only a few URLs**  
Check that `wget` can actually reach your site from the server and that no
firewall rule blocks outbound HTTP/HTTPS connections from the web server itself:

```bash
curl -I https://your-site.com/
wget --spider https://your-site.com/
```

**Lots of `⊘ auth-protected` lines**  
Pages behind HTTP Basic Auth or Exponential's login system cannot be spidered
without credentials. The preloader skips them gracefully. To warm
authenticated pages you would need to handle authentication separately (beyond
the scope of this script).

---

## Technical details

- **Phase 1** uses `curl` with `-sk` (silent, ignore SSL certificate errors) and
  captures `http_code`, `time_total`, and `size_download` inline. It also
  extracts Exponential's optional `X-Debug-*` HTML comments for kernel timing
  and database metrics if your site emits them.
- **Phase 2** uses `wget --spider --recursive --level=3` which visits pages
  without saving them. Output is read line by line with `popen()` so progress
  appears in real time rather than after the full crawl finishes.
- Binary files (images, fonts, videos, PDFs, archives) are excluded from the
  wget spider pass via `--reject` so the crawl stays focused on HTML pages.
- Auth-protected resource lines (`Username/Password Authentication Failed`) are
  collapsed into a single summary line instead of flooding the terminal.
- All PHP output is flushed immediately (`flush()`) so you see progress in real
  time even in long-running Phase 2.
