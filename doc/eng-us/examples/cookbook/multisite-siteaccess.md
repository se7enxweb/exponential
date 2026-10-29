---
title: "Several sites: siteaccesses"
audience: admin, developer
category: examples
order: 100
summary: Serve two public sites and one administration from one installation, matched by host name, each with its own design, start node, language and var directory, with the settings of each siteaccess and the checks that prove the match.
keywords: siteaccess, multisite, multi-site, AvailableSiteAccessList, RelatedSiteAccessList, MatchOrder, HostMatchType, HostMatchMapItems, HostMatchMethod, URIMatchType, HostUriMatchMapItems, settings/siteaccess, VarDir, CacheVarDir, LogVarDir, RootNode, PathPrefix, SiteDesign, DefaultAccess
type: example
---
# Several sites: siteaccesses

A **siteaccess** is a named set of settings that applies to a request: which design, which part of the content tree,
which language, whether visitors must sign in. One installation, one database, many siteaccesses. You set up:

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../web-servers/nginx.md), [LiteSpeed](../web-servers/litespeed.md),
> [Caddy](../web-servers/caddy.md), [IIS](../web-servers/iis.md), [Velocity](../web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../../help/hosting/panels/plesk.md), [cPanel](../../help/hosting/panels/cpanel.md), [DirectAdmin](../../help/hosting/panels/directadmin.md), [ISPConfig](../../help/hosting/panels/ispconfig.md).

| Siteaccess | Host | What it is |
|---|---|---|
| `site_a` | `www.example.com` | the first public site, the subtree *Site A* |
| `site_b` | `www.example.org` | the second public site, the subtree *Site B*, in German |
| `admin` | `admin.example.com` | the administration of both |

Prerequisites: a working installation with the siteaccess `admin`; the three host names point to the server and the
web server answers for all of them with the same document root ([Web server examples](../README.md)); two folders
*Site A* and *Site B* below the root of the content tree (note their node ids, here 60 and 61).

## 1. The list and the match

Siteaccess selection is installation-wide, so it goes in the global override, `settings/override/site.ini.append.php`
(never committed: it also holds the database password):

```ini
<?php /* #?ini charset="utf-8"?

[SiteSettings]
DefaultAccess=site_a

[SiteAccessSettings]
AvailableSiteAccessList[]
AvailableSiteAccessList[]=site_a
AvailableSiteAccessList[]=site_b
AvailableSiteAccessList[]=admin
# Siteaccesses that share this database (the administration shows and clears their caches)
RelatedSiteAccessList[]
RelatedSiteAccessList[]=site_a
RelatedSiteAccessList[]=site_b
RelatedSiteAccessList[]=admin
# Choose the siteaccess by the host name only
MatchOrder=host
HostMatchType=map
HostMatchMapItems[]
HostMatchMapItems[]=www.example.com;site_a
HostMatchMapItems[]=www.example.org;site_b
HostMatchMapItems[]=admin.example.com;admin

*/ ?>
```

- `MatchOrder` lists the probes tried in order: `uri` (the first segment of the path, `/site_b/...`), `host`, `host_uri`
  (host and segment together, `HostUriMatchMapItems[]=example.com;de;site_de`), `port`, `servervar`. The shipped
  default is `uri;host;port`.
- With nothing matched, `DefaultAccess` answers.
- `HostMatchMethod=start` (or `;start` as a fourth field of an entry) also matches test hosts that begin with the
  name, such as `www.example.com.test.local`; see
  [Siteaccess by host prefix](../../features/6.0/siteaccess-host-and-language-matching.md).

## 2. The settings of each siteaccess

Each siteaccess has a directory `settings/siteaccess/<name>/`. What differs per site goes there.

`settings/siteaccess/site_a/site.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[SiteSettings]
SiteName=Example A
SiteURL=www.example.com
DefaultPage=/content/view/full/60
IndexPage=/content/view/full/60

[SiteAccessSettings]
RequireUserLogin=false
# The URL alias of Site A's folder is not repeated in every address
PathPrefix=Site-A

[DesignSettings]
SiteDesign=mysite
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=base

[RegionalSettings]
Locale=eng-US
ContentObjectLocale=eng-US
SiteLanguageList[]
SiteLanguageList[]=eng-US

[FileSettings]
VarDir=var/site_a

*/ ?>
```

`settings/siteaccess/site_a/content.ini.append.php` (the root of menus and of `fetch` with no parent):

```ini
<?php /* #?ini charset="utf-8"?

[NodeSettings]
RootNode=60

*/ ?>
```

`settings/siteaccess/site_b/` is the same with `SiteName=Example B`, `SiteURL=www.example.org`, node 61,
`PathPrefix=Site-B`, `VarDir=var/site_b`, and German first:

```ini
[RegionalSettings]
Locale=ger-DE
ContentObjectLocale=ger-DE
SiteLanguageList[]
SiteLanguageList[]=ger-DE
SiteLanguageList[]=eng-US
```

`SiteLanguageList[]` is the order in which content languages are shown; the languages must exist
(**Setup > Languages**). The siteaccess `admin` keeps its own settings; add `SiteLanguageList[]` for both languages there.

**VarDir per site** keeps the caches and the storage of each site apart. To put all their logs and caches in trees of
their own with one line each, use `LogVarDir`, `CacheVarDir` and `INICacheDir=site` in the global override:
[Logs, INI cache and expiry timestamps per site](../../features/6.0/multi-site-log-and-cache-paths.md). With
`CacheVarDir`, the web server must also serve the public caches below it: enable the commented `var_cache` rule of
`.htaccess_root` (or the matching rule of your server's example). Do that in your copy, `.htaccess` (put into place
with `./console exp:setup:htaccess --apply`), never in the template. From then on your `.htaccess` differs from
`.htaccess_root`: after an upgrade, `./console exp:setup:htaccess` shows that line among the differences and refuses to
copy without `--force`; after `--force --apply`, enable the rule again
([Setting up .htaccess and config.php from the console](../../guides/setup-files-from-the-console.md)).

**Settings carried by an extension.** A site extension can ship `extension/<ext>/settings/siteaccess/<name>/` instead,
so the settings are in version control; see
[Per-site settings inside extensions](../../features/6.0/multi-site-ini-overrides.md).

## 3. Clear and check

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
./console exp:ini get site.ini/SiteSettings/SiteName -s site_b --allow-root-user
curl -sI https://www.example.org/ | head -1
```

Expected: `Example B`, and `HTTP/... 200` (or a redirect to the start page). Each site shows its own subtree, in its
own language; `https://admin.example.com/` asks for a sign-in. Under Velocity, restart it after a siteaccess change
(`./console exp:velocity restart --allow-root-user`): its workers hold the settings in memory.

On the command line every script takes the siteaccess with `-s` (`--siteaccess`):
`php runcronjobs.php -s site_b frequent --allow-root-user`.

Checked for this page (2026-10-18): every setting named exists in the shipped `settings/site.ini` or
`settings/content.ini` with the meaning described; the host map and its methods are those of
`doc/eng-us/features/6.0/siteaccess-host-and-language-matching.md`. The configuration was not applied to a site for this page.

## Common mistakes

| Symptom | Cause |
|---|---|
| Every host shows the same site | `MatchOrder` does not contain `host`, or the INI cache; the web server rewrites the host |
| A redirect loop on the administration host | the host is not in `HostMatchMapItems[]`; `DefaultAccess` answers and asks for a sign-in somewhere else |
| A siteaccess is ignored | it is not in `AvailableSiteAccessList[]`, or its directory name differs |
| Links point to the other host | `SiteURL` of the siteaccess; for links between sites, `ezurl` with a full address or the language switcher |
| `exp:ini get ... -s` shows the global value | the setting is in the wrong directory, or in the override file, which wins over the siteaccess |

## Derived from

- `settings/site.ini` (`[SiteAccessSettings]`, `[SiteSettings]`, `[DesignSettings]`, `[RegionalSettings]`, `[FileSettings]`)
- `settings/content.ini` (`[NodeSettings] RootNode`), `settings/siteaccess/mysite/site.ini.append`
- `doc/eng-us/features/6.0/siteaccess-host-and-language-matching.md`, `doc/eng-us/features/6.0/multi-site-log-and-cache-paths.md`

## Related pages

- [Cookbook](README.md)
- [Settings override placements](../../specifications/6.0/ini-override-placements.md),
  [Change settings from the command line](../../features/6.0/exp-ini-command.md)
- [INI settings](../ini-settings/README.md): `site.ini` block by block, with defaults
- [Translations](translations.md), [Caching](caching.md)
- The coding rules and the pattern catalogue: [the standards](../../standards/README.md)
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
