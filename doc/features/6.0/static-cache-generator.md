# Static cache generator

The static cache turns pages into plain files that the web server can send
without starting Exponential at all. In 6.0 the generator crawls your site the
way a visitor would, shows progress while it works, and the shipped defaults
now produce a working cache instead of silently producing nothing.

## What you get

- **Setup > Cache > Static content cache**: choose a site, set a page limit and
  a link depth, press *Create new* and watch every page being stored. A stop
  button ends the run early.
- **`bin/php/makestaticcache.php`**: the same crawl from the command line.
- **Web server rules** that answer a request from the stored file, so a cached
  page costs one file read.

## Generate from the administration interface

1. Open **Setup > Cache**.
2. In the *Static content cache* section pick the site to generate. Only
   siteaccesses that can be served to everybody are offered: one that requires a
   login is left out, because its pages are per user.
3. Set the limits (below) and press *Create new*.
4. Read the live list of stored pages. Press *Stop* to end the run.

| Field | Default | Bounds | Meaning |
|---|---|---|---|
| Page limit | 2500 | 1 to 20000 | The most pages one run stores. |
| Link depth | 12 | 0 to 30 | How many links away from the site root the crawl follows. |

A run empties the site's stored pages first (the stream's `purge` parameter, on by
default), so pages you deleted do not linger. The view runs under the existing `managecache` policy of `setup/cache`; nobody
who cannot clear caches can generate one.

### Why it crawls

An older approach expanded `CachedURLArray` against the URL alias table of the
whole database. On an installation with several sites that offers every alias to
every siteaccess, so one site is asked for another site's pages and misses its
own. The crawl starts at the site's own root and follows only links the site
itself writes, so it visits exactly the addresses a visitor can request, in the
form the visitor requests them. Each page fetched is stored, so no page is
requested twice.

## Generate from the command line

```bash
php bin/php/makestaticcache.php --help --allow-root-user
php bin/php/makestaticcache.php --site=user --allow-root-user
php bin/php/makestaticcache.php --site=user --site=intranet --max-pages=500 --max-depth=6 --allow-root-user
```

`--allow-root-user` is needed only when you run as root. `--site` names a
siteaccess and may be repeated; leave it out to generate every public
siteaccess. `--max-pages` (default 2500) and `--max-depth` (default 12) take the
same bounds as the administration form. `--keep` adds to what is stored instead
of replacing it; `-f`/`--force` is accepted for compatibility (a run always
replaces what it generates unless `--keep` is given). The `--help` output above
was run on this installation and lists exactly these options.

## Serve the files without starting the CMS

Generating files is half the work. The other half is telling the web server to
use them.

- **Apache with `.htaccess`**: copy `.htaccess_root_static` to `.htaccess`
  instead of `.htaccess_root`. It is `.htaccess_root` plus the offload rules,
  so switching back is copying the other file over it. Check the one line
  `RewriteRule ^ - [E=EZ_VAR_SITE:site]`: the value is `site.ini`
  `[FileSettings] VarDir` without the leading `var/`; a default installation
  (`var/site`) needs no change.
- **Apache or other virtual host**: a vhost does not read `.htaccess`; use the
  same rules from `doc/examples/staticcache_offload.conf`.

A request is answered from a file only when it is a plain `GET` or `HEAD`, has
no query string and carries no session. Forms, searches and anyone logged in
always get the live page.

## Settings (`settings/staticcache.ini`, block `[CacheSettings]`)

| Key | Default | Scope | Meaning |
|---|---|---|---|
| `HostName` | empty | global | Deprecated. Leave empty so each siteaccess is fetched from its own `site.ini [SiteSettings] SiteURL`. It used to ship as `localhost`, which sent every fetch to whatever answers for that name. |
| `SourceProtocol` | `http` | global | `http` or `https`; anything else means `http`. The fetch follows redirects, so `http` works on a site that redirects to https. Use `https` where plain http is refused. |
| `StaticStorageDir` | `static` | global | Where pages are written. A relative name resolves below the var directory of the siteaccess, so `static` becomes `var/<site>/static`. An absolute path, or one already starting with `var/`, is used as given. |
| `MaxCacheDepth` | `12` | global | URLs deeper than this many segments are neither cached nor refreshed on publish. Was `3`. |
| `CachedURLArray[]` | `/` and `/*` | global | URLs to cache; `*` takes a subtree, a bare `/*` the whole site. Was `/`, `/news*`, `/weblog*`. |
| `CachedSiteAccesses[]` | empty | global | Siteaccesses to generate. Empty means every entry of `site.ini [SiteAccessSettings] RelatedSiteAccessList` (or `AvailableSiteAccessList`) except those with `RequireUserLogin=true` or a `SiteURL` that is still `example.com`. |

Three more keys of the shipped file are older and unchanged:

| Key | Default | Scope | Meaning |
|---|---|---|---|
| `AlwaysUpdateArray[]` | `/` | global | URLs refreshed on every publish, whatever was published. |
| `CronjobCacheClear` | `disabled` | global | `enabled` lets the `staticcache_cleanup` cronjob part do the clearing instead of the publish. |
| `AppendGeneratedTime` | `true` | global | Appends a `<!-- Generated: ... -->` comment to each stored page. |

Override in `settings/override/staticcache.ini.append.php` or per siteaccess.

## Refreshing on publish: once each, in parallel (2026-10-01)

With the static cache on, a publish refreshes the pages it touches. A no-change publish of one product used to
spend 4975 ms of a 6 s request refreshing 24 pages: each fetched twice over HTTP (a HEAD check, which the site
renders in full just the same, then the GET), one after the other, and ten of them were 404s on every publish.
`eZStaticCache::fetchPages()` now fetches each page once, several at a time, and only a 2xx answer counts. The same
publish refreshes 14 pages in about 0.5 s and stores the same 32 files. Without curl the old check-and-get is used.

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/staticcache.ini` | `CacheSettings` | `FetchConcurrency` | `8` in code (not listed in the shipped file; the fastest of 1 to 24 measured) | global |

`executeActions()` and the `staticcache_cleanup` cronjob part fetch through the same method. A siteaccess with a
`PathPrefix` stores only its front page, the pages under the prefix and pages that `PathPrefixExclude` names: a page
of one site is no longer asked for under another site's prefix.

## Upgrading

The defaults changed; see [bc: static cache defaults](../../bc/6.0/static-cache-defaults.md).

## Related

- [September 2026, first half: caches you can see](../../history/2026/2026-09a.md#13-september-caches-you-can-see-cronjobs-you-can-run)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
- [Cache from the console](../../bc/6.0/cache-console.md) (`exp:cache` static cache group)
- [Site cache preloader](preload-sites-view.md)
- [HTTP caching](../../bc/6.0/http-caching.md)

See also (October 2026): [6.0.15 changelog](../../changelogs/6.0/6.0.15.md), [upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md), [October 2026 chronicle](../../history/2026/2026-10.md), [Velocity and the opcode cache](velocity-opcode-cache-and-profile.md).

## Related pages

- [Site cache preloader command](../../bc/6.0/preload.md)
