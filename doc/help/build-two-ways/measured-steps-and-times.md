---
title: Measured steps and times
audience: designer, website-creator, admin, developer, editor
category: build-two-ways
order: 30
summary: How the steps and times quoted in these pages were measured - on a throwaway copy of a real Exponential 6.0.15+ installation - and the raw numbers, so anyone can repeat them on their own site.
keywords: measured, speed, steps, timing, benchmark, exp:ini, exp:cache, page render time, page cache, method, repeatable
type: help
---
# Measured steps and times

The numbers on these pages come from one test, described here so it can be repeated. They are not a benchmark of the
software: they show how many steps a task takes and how long the waiting parts last on an ordinary installation.

## The setup

- A copy of a live Exponential 6.0.15+ installation with the Fit & Healthy demo site (SQLite database), on the same
  shared server, served by PHP's built-in web server with six workers (`PHP_CLI_SERVER_WORKERS=6`).
- The server was busy with other work: a load average of about 29 during the measurements.
- A throwaway administrator account, removed with the copy afterwards.
- Times taken with the shell's clock around each command (`date +%s%N` before and after), three runs each.

## Steps

An **action** is one click, one choice in a list, or one typed entry.

| Task | Way | Actions or commands |
|---|---|---|
| Add a page to the header menu | Administration: Setup, Ini settings, choose `menu.ini`, choose `site`, Show settings, Edit, type `=192`, Save, choose Template & content, Clear | 10 actions |
| Add a page to the header menu | Console: `exp:ini add ...`, `exp:cache clear --tag=template,content` | 2 commands |
| Add a link to the footer's second line | Content: Media, Configuration, Footer links, Create new, Menu item, OK, type the title, type the URL, Send for publishing | 9 actions |

## Times

| Measured | Run 1 | Run 2 | Run 3 |
|---|---|---|---|
| `./console exp:ini add menu.ini/SiteInfo/FooterMenuID[] 164 siteaccess:site --no-backup` | 1512 ms | 1258 ms | 2127 ms |
| `./console exp:cache clear --tag=template,content` | 1983 ms | 2177 ms | 2470 ms |
| First request of `/fitness` after that clear (templates compiled again) | 3413 ms | 3300 ms | 2921 ms |
| Second request of `/fitness` (from the page cache) | 28 ms | 22 ms | 102 ms |

## Repeat it

On a copy of your own site (never on the live site for writes):

```bash
a=$(date +%s%N); ./console exp:ini add menu.ini/SiteInfo/FooterMenuID[] <id> siteaccess:<sa> --dry-run; b=$(date +%s%N)
echo $(( (b - a) / 1000000 )) ms
a=$(date +%s%N); ./console exp:cache clear --tag=template,content --dry-run; b=$(date +%s%N)
echo $(( (b - a) / 1000000 )) ms
curl -s -o /dev/null -w '%{time_total}\n' https://www.example.com/
```

`--dry-run` makes both commands safe to time on any installation; they then write and clear nothing.

## Related pages

- [Two ways to build](two-ways-to-build.md)
- [Caches after a change](caches-after-a-change.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
