---
title: Page caching with LiteSpeed and OpenLiteSpeed
audience: admin, hosting, developer
summary: Let LiteSpeed Web Server (Enterprise) or OpenLiteSpeed keep whole pages of an Exponential site and answer anonymous visitors and crawlers from them without running PHP, with no plugin and no code - the commented block of .htaccess_root explained line by line, the same in a LiteSpeed virtual host, Plesk, cPanel, DirectAdmin, CyberPanel and the OpenLiteSpeed WebAdmin, how to check hits, how to purge, troubleshooting and security.
keywords: LiteSpeed, OpenLiteSpeed, OLS, LSWS, LSCache, LiteSpeed Cache, page cache, full page cache, without plugin, no plugin, rewrite rules, .htaccess, .htaccess_root, CacheLookup, CacheEnable, CacheEngine, CacheRoot, cache-control, E=cache-control, max-age, X-LiteSpeed-Cache, hit, miss, X-LiteSpeed-Purge, purge, X-LiteSpeed-Cache-Control, eZSESSID, PHPSESSID, is_logged_in, session cookie, anonymous, crawler, bots, HTTPS performance, Plesk, Additional directives for HTTPS, vhost_ssl.conf, cPanel, WHM, DirectAdmin, CyberPanel, WebAdmin, enableCache, checkPublicCache, qsCache, reqCookieCache, respCookieCache, ignoreRespCacheCtrl, expireInSeconds, storagePath, autoLoadHtaccess
type: guide
---
# Page caching with LiteSpeed and OpenLiteSpeed

This guide is for administrators and hosting companies whose Exponential sites are served over HTTPS by **LiteSpeed
Web Server** (the commercial Enterprise edition) or **OpenLiteSpeed**, its open source edition. Both servers have a
page cache built in (LiteSpeed calls it LSCache). This guide switches it on for an Exponential site **without any
plugin and without changing a line of code**: a few rewrite rules (shipped, commented out, in `.htaccess_root`) or the
same rules in the virtual host. After that, a page an anonymous visitor or a crawler asks for is answered by the server
from a stored copy, and PHP, the database and the kernel do not run at all for it.

Everything about Exponential below was checked against the code of this repository (the files are named in
[References](#references)). Everything about LiteSpeed was checked against LiteSpeed's and OpenLiteSpeed's own
documentation, which is linked where it is used. [What was verified, and what was not](#what-was-verified-and-what-was-not)
says plainly which parts were not run on a LiteSpeed server here.

[Guides](README.md) · Related: [Caches](caches.md) · [Preloading caches](preloading-caches.md) ·
[LiteSpeed and OpenLiteSpeed (hosting)](../help/hosting/servers/litespeed.md) ·
[LiteSpeed and OpenLiteSpeed (example configuration)](../examples/web-servers/litespeed.md) ·
[HTTP cache headers](../help/hosting/performance/http-cache-headers.md) ·
[Caching layers](../help/hosting/performance/caching-layers.md)

## Contents

1. [In short](#in-short)
2. [When it helps, and when it does not](#when-it-helps-and-when-it-does-not)
3. [What Exponential does that the cache must know](#what-exponential-does-that-the-cache-must-know)
4. [Switch it on with .htaccess](#switch-it-on-with-htaccess)
5. [The block, line by line](#the-block-line-by-line)
6. [LiteSpeed Enterprise: the server and the virtual host](#litespeed-enterprise-the-server-and-the-virtual-host)
7. [Plesk](#plesk)
8. [cPanel and WHM](#cpanel-and-whm)
9. [DirectAdmin](#directadmin)
10. [OpenLiteSpeed and CyberPanel](#openlitespeed-and-cyberpanel)
11. [Check that it works](#check-that-it-works)
12. [How it sits with the caches of Exponential](#how-it-sits-with-the-caches-of-exponential)
13. [Choosing max-age, and what editors see](#choosing-max-age-and-what-editors-see)
14. [Purging](#purging)
15. [What must never be cached](#what-must-never-be-cached)
16. [Troubleshooting](#troubleshooting)
17. [Security notes](#security-notes)
18. [Measuring the gain](#measuring-the-gain)
19. [Crawlers and bots](#crawlers-and-bots)
20. [What was verified, and what was not](#what-was-verified-and-what-was-not)
21. [References](#references)
22. [Related pages](#related-pages)

## In short

- **What:** the LiteSpeed server keeps the HTML of a page for a fixed time (`max-age`, 120 seconds in the shipped
  block) and answers the same address from that copy. A hit costs about as much as sending a static file.
- **Who gets stored pages:** only a `GET` or `HEAD` request without a query string, from a visitor who has **no
  session cookie and no `is_logged_in` cookie**, for an address that is not an administration, user, shop or editing
  view. Everybody else (editors, signed-in members, a visitor with a basket, every form) reaches PHP as before.
- **How to switch it on:** in the site's `.htaccess` (a copy of `.htaccess_root`), remove the first `#` of each line of
  the block that starts `## >>> LiteSpeed page cache`. One `sed` command does it ([section 4](#switch-it-on-with-htaccess)).
  On OpenLiteSpeed the cache module has to be on for the virtual host as well ([section 10](#openlitespeed-and-cyberpanel)).
- **Apache and nginx ignore it.** The block sits inside `<IfModule LiteSpeed>`, which only LiteSpeed servers enter.
- **Check:** `curl -sI https://www.example.com/ | grep -i x-litespeed-cache` twice: `miss`, then `hit`.
- **The trade:** after an editor publishes, anonymous visitors can see the old page for up to `max-age` seconds.
  Exponential does not tell LiteSpeed to drop a page; keep `max-age` short, or add the purge listener of
  [section 14](#purging) yourself.

## When it helps, and when it does not

The page cache answers **the same page for everybody who has no session**. It helps most where that is most of the
traffic:

| Site | Effect |
|---|---|
| Public content sites: news, magazines, documentation, corporate and government sites | most requests are anonymous: most pages become hits |
| Traffic peaks (a newsletter, a link from a large site, a campaign) | the first request renders, the next ones within `max-age` are hits |
| Crawler and bot bursts (search engines, AI crawlers, monitoring, scrapers) | they never sign in and rarely send cookies: hits ([section 19](#crawlers-and-bots)) |
| Shared hosting with few PHP processes per account | hits do not take a PHP process, so the processes stay free for the requests that need one |

It helps little, or must be kept narrow, where pages differ per visitor:

| Site | Why |
|---|---|
| Mostly signed-in users (intranets, member areas, communities) | every signed-in request carries a session cookie and is never stored |
| Shops | the basket lives in the session; browsing anonymously is cacheable, but the shop views are not ([section 15](#what-must-never-be-cached)) |
| A siteaccess chosen by the browser's language or device | one address answers differently per visitor ([section 15](#what-must-never-be-cached)) |
| Pages that show the visitor's own data without a session (a geolocated block, a random teaser per request) | everyone gets the copy that was stored first |

## What Exponential does that the cache must know

These are facts of the kernel, not of LiteSpeed. The block of `.htaccess_root` is built on them.

**Anonymous visitors have no session.** `site.ini [Session] ForceStart=disabled` is shipped: the kernel starts a
session only when the visitor already sends a session cookie, or when something writes to the session (signing in,
putting something in the basket, a form that keeps state). Browsing pages anonymously starts none and sets no cookie
(`eZSession::lazyStart()` in `lib/ezsession/classes/ezsession.php`; the "last visited page" bookkeeping of
`ezpKernelWeb` runs only when a session has already started). So an anonymous page answer carries no `Set-Cookie`,
and a stored copy holds nothing private. If your site sets `ForceStart=enabled`, every visitor gets a session cookie on
the first request, every later request carries it, and the block caches almost nothing: keep `ForceStart=disabled`.

**The session cookie has one of two names.** It depends on `site.ini [Session] SessionNameHandler`:

| `SessionNameHandler` | Name of the session cookie |
|---|---|
| `default` (shipped) | the name PHP gives it, `session.name` of `php.ini`: `PHPSESSID` unless your host changed it |
| `custom` | `SessionNamePrefix` (`eZSESSID`), followed by the md5 of the siteaccess name while `SessionNamePerSiteAccess=enabled` (shipped): `eZSESSID3f1c...` |

A cookie condition is a substring test on the `Cookie:` request header, so `eZSESSID` matches every per-siteaccess
name. The block tests both names. If `php.ini` gives the session another name, add it.

**Signed-in users carry `is_logged_in=true`.** The kernel sets this cookie for every signed-in user "for use by http
cache solutions" and removes it on sign-out (`ezpKernelWeb`, with the path, domain, lifetime and `Secure` flag of the
session cookie, `eZSession::siteCookieOptions()`). It is the most reliable signal that a request belongs to somebody,
and the block tests it too. Exponential Velocity's own response cache skips exactly the same two cookies
(`expVelocity`), for the same reasons.

**There is no basket cookie.** The shop's basket is kept in the session. A visitor who puts something in the basket
gets a session cookie and from then on bypasses the cache. A cookie list like `basket` from other systems' examples
matches nothing in Exponential; it does no harm, but it protects nothing either.

**Pages say "do not store" by default.** Every page response carries `Cache-Control: no-cache, must-revalidate`,
`Expires` in 1997 and `Pragma: no-cache`, unless `site.ini [HTTPHeaderSettings]` is configured otherwise
([HTTP cache headers](../help/hosting/performance/http-cache-headers.md#the-defaults)). Whether a LiteSpeed server
lets that header stop it from storing a page is a setting of the server ("Ignore Response Cache-Control", on
OpenLiteSpeed `ignoreRespCacheCtrl`); see [Troubleshooting](#always-miss) and
[Telling LiteSpeed the same thing from Exponential](#telling-litespeed-the-same-thing-from-exponential).

**Some responses are private on purpose.** A siteaccess chosen by the browser's languages
(`DefaultHostUriMatchMapItems`) answers with `Vary: Accept-Language` and `Cache-Control: private, no-cache` (`ezpKernelWeb`);
the maintenance page is a `503` with `Cache-Control: no-store, no-cache, must-revalidate, private, max-age=0`
(`expMaintenance`). Section 15 says what to do about the first.

## Switch it on with .htaccess

This is the shortest way on LiteSpeed Enterprise, which reads `.htaccess` like Apache. On OpenLiteSpeed read
[section 10](#openlitespeed-and-cyberpanel) first: it reads `.htaccess` only with `autoLoadHtaccess` on and only after a
restart.

1. **The site serves `.htaccess` from `.htaccess_root`.** If it does not yet: `cp .htaccess_root .htaccess`, or as the
   site's user `./console exp:setup:htaccess --apply`
   ([Setting up .htaccess and config.php from the console](setup-files-from-the-console.md)). If your `.htaccess` is
   older than the block, `./console exp:setup:htaccess` shows the difference; the block is new in 6.0.15.
2. **Your host has given LiteSpeed a cache directory.** On LiteSpeed Enterprise that is a `CacheRoot` at server level;
   on OpenLiteSpeed the cache module's `storagePath`. Ask your host if you are not the administrator of the server;
   sections 6 to 10 show where it is set.
3. **Adjust the block if needed** (each point is explained in [the next section](#the-block-line-by-line)):
   - your administration siteaccess is matched by URI and is not called `admin`: put its name in the list;
   - it has a host name of its own: uncomment the host line and put its name in;
   - `php.ini` gives the session cookie a name other than `PHPSESSID`, or your site sets cookies of its own that
     change what a page shows: add them to the cookie list;
   - change `max-age=120` if two minutes is the wrong delay for your editors.
4. **Uncomment the block.** In the installation root:

   ```bash
   sed -i '/^## >>> LiteSpeed page cache/,/^## <<< LiteSpeed page cache/ s/^#//' .htaccess
   ```

   This removes the first `#` of every line from the `>>>` marker to the `<<<` marker. Directive lines become active;
   explanation lines (`##`) keep one `#` and stay comments. Run it once. You can also edit the lines by hand.
5. **Check it** as in [section 11](#check-that-it-works). LiteSpeed Enterprise reads the changed `.htaccess` on the
   next request; no restart.

To switch it off again, put a `#` back in front of the `<IfModule LiteSpeed>` line and every line up to
`</IfModule>`, or copy `.htaccess_root` over `.htaccess` again. Pages already stored expire after `max-age`.

## The block, line by line

This is the block as it is in `.htaccess_root` once uncommented (the explanation lines are left out here). It stands
after the rules that send assets and the REST and tree menu entry scripts their own way, and directly before the last
rule, `RewriteRule .* index.php`, so it only ever sees requests that go to `index.php`.

```apache
<IfModule LiteSpeed>
CacheLookup on
RewriteCond %{REQUEST_METHOD} ^(GET|HEAD)$
RewriteCond %{QUERY_STRING} ^$
RewriteCond %{HTTP_COOKIE} !(eZSESSID|PHPSESSID|is_logged_in) [NC]
RewriteCond %{REQUEST_URI} !^/([^/]+/)?((admin|user|shop|notification|layout|ezjscore|setup)(/|$)|content/(?!view/)) [NC]
#RewriteCond %{HTTP_HOST} !^admin\. [NC]
RewriteRule .* - [E=cache-control:max-age=120]
</IfModule>
```

| Line | What it does | Why |
|---|---|---|
| `<IfModule LiteSpeed>` | only LiteSpeed Enterprise and OpenLiteSpeed enter the block | Apache and nginx skip it: the same `.htaccess` stays safe everywhere |
| `CacheLookup on` | look the request up in the page cache | LiteSpeed's documentation for rewrite-rule caching starts with it ("LSCache without a plugin"); it implies nothing is stored by itself |
| `REQUEST_METHOD ^(GET\|HEAD)$` | only reads | a `POST` (every form: sign-in, comments, search forms, collected information, the basket) always reaches PHP |
| `QUERY_STRING ^$` | only addresses without `?` | searches (`content/search?SearchText=`), filters, paging by query and tracking parameters (`?utm_source=`) never fill the cache with one copy per variant; Exponential's own view parameters (`/(offset)/10`) are part of the path and are cached as separate pages |
| `HTTP_COOKIE !(eZSESSID\|PHPSESSID\|is_logged_in)` | nobody with a session or signed in | see [the previous section](#what-exponential-does-that-the-cache-must-know); a request that carries one of these is neither answered from nor stored in the cache |
| `REQUEST_URI !^/([^/]+/)?(...)` | not the views that differ per visitor or change something | `user/` (sign-in, register, password, preferences), `shop/`, `notification/`, `layout/` (stores a visitor's choice), `ezjscore/` (AJAX calls), `setup/`, the administration (`admin`), and every `content/` view except `content/view/` (edit, action, search, download, collected information, tree menu, browse ...); `([^/]+/)?` also matches them behind a siteaccess name in the URL (`/eng/user/login`) |
| `HTTP_HOST !^admin\.` (commented) | not the administration's own host name | for sites whose editors work on `admin.example.com` instead of `/admin` |
| `RewriteRule .* - [E=cache-control:max-age=120]` | tell LiteSpeed: keep this answer 120 seconds | `-` changes nothing in the address; the environment variable `cache-control` is LiteSpeed's documented way to set the cache policy from a rewrite rule |

**Why not `CacheEnable public /`.** Many short examples use `CacheEnable public /`. It switches caching on for every
address below `/`, with the lifetime of the server's policy (LiteSpeed's documentation notes that a rewrite rule can
set a lifetime with `max-age`, while `CacheEnable` cannot). Which pages are kept then depends on the server's defaults
instead of on the conditions above. The shipped block gives a lifetime only to requests that pass every condition, and
nothing else is ever stored.

**Variants of the cache-control value**, all documented by LiteSpeed for the environment variable: `no-cache` (do not
store this one), `private` (a copy per visitor, keyed on their cookies), `max-age=N`, and combinations in quotes,
`[E="cache-control:public,max-age=300"]`. The shipped block uses the plain public form.

**The REST, SOAP, WebDAV and tree menu entry scripts** are never touched: `/api/` is rewritten to `index_rest.php` and
`content/treemenu` to `index_treemenu.php` by rules above the block, with `[L]`; `index_soap.php` and `index_webdav.php`
have pass-through rules there when you switch them on; and the URI condition excludes `content/` anyway. Static files
(designs, images, stored files) are sent by the asset rules above the block and never reach it.

## LiteSpeed Enterprise: the server and the virtual host

LiteSpeed Enterprise reads Apache's configuration, so it takes the cache settings in Apache syntax inside
`<IfModule LiteSpeed>` (LiteSpeed's documentation writes `Litespeed` and `LiteSpeed`; both are accepted).

**Server level, once per machine.** The cache needs a directory with enough space. LiteSpeed's cPanel guide puts it in
the server-level configuration:

```apache
<IfModule LiteSpeed>
CacheRoot /home/lscache/
</IfModule>
```

On Red Hat style systems with Plesk this often lives in its own file below `/etc/httpd/conf.d/` (for example
`lscache.conf`); on Debian style systems below `/etc/apache2/conf.d/` or `/etc/apache2/conf-enabled/`. A host can switch
the engine off for the whole server with `CacheEngine off` after the `CacheRoot` line; then a virtual host switches it
back on with `CacheEngine on`. LiteSpeed's documentation is explicit that `CacheEngine` belongs in the server or
virtual host configuration and **not** in `.htaccess`.

**Virtual host level, per site, instead of `.htaccess`.** The same block works in the HTTPS virtual host, which is
faster than `.htaccess` (no file to look for on every request; see the warning on
[Setting up .htaccess and config.php from the console](setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)).
In server context a rewrite pattern sees the path with its leading slash, so the patterns accept it with `^/?`. The
ready-made file is [`litespeed-page-cache.conf`](../examples/web-servers/litespeed-page-cache.conf):

```apache
<IfModule LiteSpeed>
    # Only needed when the server-level configuration says "CacheEngine off"
    #CacheEngine on
    CacheLookup on
    RewriteEngine On
    RewriteCond %{REQUEST_METHOD} ^(GET|HEAD)$
    RewriteCond %{QUERY_STRING} ^$
    RewriteCond %{HTTP_COOKIE} !(eZSESSID|PHPSESSID|is_logged_in) [NC]
    RewriteCond %{REQUEST_URI} !^/([^/]+/)?((admin|user|shop|notification|layout|ezjscore|setup)(/|$)|content/(?!view/)) [NC]
    #RewriteCond %{HTTP_HOST} !^admin\. [NC]
    RewriteRule ^/?(.*)$ - [E=cache-control:max-age=120]
</IfModule>
```

Put it in the HTTPS virtual host (`<VirtualHost *:443>`) **before** the rule that sends everything to `index.php`, and
restart LiteSpeed: LiteSpeed's documentation says changes to the Apache configuration need a restart, changes to
`.htaccess` do not. Use the block either in `.htaccess` or in the virtual host, not in both.

## Plesk

On a Plesk server with LiteSpeed, LiteSpeed reads the Apache configuration that Plesk generates. There are two places
for the block:

1. **The site's `.htaccess`** ([section 4](#switch-it-on-with-htaccess)). Nothing in Plesk has to change, as long as the
   server has a `CacheRoot`.
2. **Websites & Domains > the domain > Apache & nginx Settings > Additional Apache directives > "Additional directives
   for HTTPS"**: paste the virtual host block of [section 6](#litespeed-enterprise-the-server-and-the-virtual-host)
   (with `CacheEngine on` uncommented if the server's file switches the engine off for all sites) and press OK. Plesk
   writes the field into the domain's configuration and reloads the web server, which here is LiteSpeed.

**Do not edit `vhost_ssl.conf` by hand.** The file `/var/www/vhosts/system/<domain>/conf/vhost_ssl.conf` is where those
directives end up, and it is tempting to edit it directly. But Plesk keeps the field's content in its own database and
writes the configuration files again whenever the domain's web configuration is regenerated: a change in the panel, a
PHP handler switch, `plesk repair web`, a Plesk update. A hand-made `vhost_ssl.conf` is replaced by what the panel holds
at that moment, and the cache rules disappear without a message. Whatever is entered in the panel field survives every
regeneration. If the field is already in use (for example for the SOAP pass-through, see
[Plesk](../help/hosting/panels/plesk.md)), add the block below what is there.

A typical Plesk symptom is a server that has the server-level `CacheRoot` (in a file such as
`/etc/httpd/conf.d/lscache.conf`) but no site that tells LiteSpeed what to cache: every page answers without any
`X-LiteSpeed-Cache` header. The block, in either place, is the per-site switch that is missing.

## cPanel and WHM

- **Server level:** LiteSpeed's cPanel guide puts `CacheRoot /home/lscache/` (or a partition with enough space) in
  `/etc/apache2/conf.d/includes/pre_main_global.conf`, inside `<IfModule LiteSpeed>`, and recommends leaving the
  server-level cache policy "Not Set". The LiteSpeed plugin in WHM manages this on most servers.
- **Per site:** the account's `.htaccess` is the usual place ([section 4](#switch-it-on-with-htaccess)); account
  holders can do it themselves in the File Manager.
- **Per site, in the virtual host:** cPanel includes per-domain files from
  `/etc/apache2/conf.d/userdata/ssl/2_4/<user>/<domain>/` into the HTTPS virtual host. Put the virtual host block of
  section 6 in a file there (for example `lscache.conf`), then `/scripts/rebuildhttpdconf` and restart the web server.
  Files placed there survive cPanel's rebuilds; a hand edit of `httpd.conf` does not.

## DirectAdmin

- **Per site:** the domain's `.htaccess` in `public_html` (or wherever the installation root is), as in
  [section 4](#switch-it-on-with-htaccess).
- **Per site, in the virtual host:** Server Manager > Custom HTTPD Configurations > the domain lets the administrator
  add lines to the domain's generated configuration, which survive DirectAdmin's rewrites; paste the virtual host block
  of section 6 there.
- **Server level:** the `CacheRoot` is part of the LiteSpeed setup DirectAdmin installs; check it in LiteSpeed's
  WebAdmin console (port 7080) or in the server-level configuration.

## OpenLiteSpeed and CyberPanel

OpenLiteSpeed differs from LiteSpeed Enterprise in three ways that matter here:

1. **The cache is a module configured in WebAdmin**, not with `CacheRoot` and `CacheEnable`. Server Configuration >
   Modules > `cache` sets the defaults; Virtual Hosts > the site > Modules > `cache` overrides them per site.
2. **It reads only the rewrite rules of `.htaccess`, only when `autoLoadHtaccess` is on, and only after a restart** when
   the file changes. Other directives in `.htaccess` are ignored ("Unsupported directives will be ignored by
   OpenLiteSpeed"). So `CacheLookup` in `.htaccess` does nothing there; the module's `checkPublicCache` does its job.
3. **`RewriteBase` is not supported**; the shipped rules do not use it.

**The cache module of the virtual host.** In `vhconf.conf` (or the same fields in WebAdmin):

```apache
module cache {
  ls_enabled              1
  # Do not cache every page by policy: only what the rewrite rule marks
  enableCache             0
  enablePrivateCache      0
  # Look requests up in the public cache
  checkPublicCache        1
  checkPrivateCache       0
  # Addresses with a query string are never stored (the rule says so too)
  qsCache                 0
  # Requests with cookies may be answered from the cache; the rule decides
  # which cookies keep a request out (analytics cookies do not)
  reqCookieCache          1
  # Never store a response that sets a cookie
  respCookieCache         0
  ignoreReqCacheCtrl      1
  # 0: the response's own Cache-Control counts (see the guide, section 12)
  ignoreRespCacheCtrl     0
  expireInSeconds         120
  storagePath             $VH_ROOT/lscache
}
```

The parameters are those of OpenLiteSpeed's cache module documentation. `enableCache 0` with `checkPublicCache 1`
leaves the decision to the rule: only requests that it marks with `cache-control` are stored. `reqCookieCache 0` would
be stricter (no request with any cookie is answered from the cache), but then a visitor with only an analytics cookie
never gets a stored page. `respCookieCache 0` keeps any response that sets a cookie out of the cache, which is the
right default for Exponential: a page that sets a cookie belongs to one visitor. `ignoreRespCacheCtrl 0` makes the
module obey the page's own `Cache-Control`; with Exponential's default `no-cache` that means nothing is stored until
the kernel sends a cacheable header to anonymous visitors
([Telling LiteSpeed the same thing from Exponential](#telling-litespeed-the-same-thing-from-exponential)). Set it to
`1` only if you do not want to change the kernel's headers, and read [section 15](#what-must-never-be-cached) first.

**The rules.** The [OpenLiteSpeed virtual host example](../examples/web-servers/openlitespeed-vhost.conf) has the
rewrite rules of `.htaccess_root` in its `rewrite` block, with the cache rules commented out just before the last rule.
Uncomment them there and restart. If you prefer `.htaccess`: set Virtual Hosts > the site > Rewrite > "Auto Load from
.htaccess" to Yes (`autoLoadHtaccess 1`), uncomment the block as in section 4 and restart OpenLiteSpeed
(`/usr/local/lsws/bin/lswsctrl restart` or WebAdmin > Graceful Restart) after **every** change to the file.

**CyberPanel** runs OpenLiteSpeed. Its website page has a "vHost Conf" editor (the `vhconf.conf` above, where the
`module cache` block goes) and a "Rewrite Rules" editor that writes the site's `.htaccess`. After saving rewrite rules,
make sure OpenLiteSpeed was restarted; until then it serves with the old rules.

## Check that it works

Use an address that the block caches (a content page, not `/user/login`), from a shell **without** any cookie:

```bash
curl -sI https://www.example.com/ | grep -i -E '^x-litespeed-cache|^cache-control|^set-cookie'
curl -sI https://www.example.com/ | grep -i -E '^x-litespeed-cache|^cache-control|^set-cookie'
```

| What you see | Meaning |
|---|---|
| `X-LiteSpeed-Cache: miss` on the first request, `hit` on the second | it works: the first answer was stored, the second came from the cache |
| `hit` on both | the page was already stored by an earlier request |
| no `X-LiteSpeed-Cache` header at all | the block is not active, or the server has no cache directory, or the request was excluded by a condition |
| `miss` every time | see [Always miss](#always-miss) |
| a `Set-Cookie` header | this page starts a session or sets a cookie: it must not be cached ([Troubleshooting](#a-page-sets-a-cookie)) |

LiteSpeed's documentation lists these header values: `X-LiteSpeed-Cache: miss` (not cached yet, stored now),
`X-LiteSpeed-Cache: hit` (served from the cache), `hit,private` for the private cache, and
`X-LiteSpeed-Cache-Control: no-cache` for a page the application marked as not cacheable. LiteSpeed also runs an
online checker at [check.lscache.io](https://check.lscache.io/).

**The other direction matters as much.** A signed-in request must never be a hit:

```bash
curl -sI -H 'Cookie: is_logged_in=true' https://www.example.com/ | grep -i x-litespeed-cache   # nothing
curl -sI -X POST https://www.example.com/ | grep -i x-litespeed-cache                         # nothing
curl -sI 'https://www.example.com/?x=1' | grep -i x-litespeed-cache                           # nothing
curl -sI https://www.example.com/user/login | grep -i x-litespeed-cache                       # nothing
```

Then sign in to the site in a browser, open the developer tools (Network), reload a content page and look at its
response headers: there must be no `X-LiteSpeed-Cache: hit`.

**Response times.** `curl` measures the time to the first byte:

```bash
for i in 1 2 3; do curl -s -o /dev/null -w '%{http_code} %{time_starttransfer}s\n' https://www.example.com/; done
```

The first figure after a purge or an expiry is the rendered page, the following ones are hits.

## How it sits with the caches of Exponential

The LiteSpeed page cache sits **in front of** everything Exponential has. A hit never reaches PHP; a miss is an
ordinary request, and on its way the kernel's own caches work as always. They complement each other:

| Layer | Where | Who gets it | Cleared by |
|---|---|---|---|
| LiteSpeed page cache | the web server | anonymous visitors, crawlers | `max-age`, a purge ([section 14](#purging)) |
| Role-aware HTTP cache (`httpcache.ini`, off by default) | before the kernel starts | anonymous and signed-in users by permission context | the kernel, on publish |
| Content view cache, template-block cache | the kernel | everybody | the kernel, on publish ([Caches](caches.md)) |
| Static cache (off by default) | files served by rewrite rules | anonymous visitors | regenerated on publish |
| Browser cache | the visitor's browser | each visitor | `Cache-Control` of the response |

- **View cache and template-block cache stay on.** They make every miss, every signed-in request and every page outside
  the block fast. The LiteSpeed cache does not replace them.
- **The role-aware HTTP cache** ([HTTP cache headers](../help/hosting/performance/http-cache-headers.md#the-role-aware-http-cache))
  can run as well: a LiteSpeed miss is then often answered by its early exit, and signed-in users, whom LiteSpeed never
  serves, get its role-shared pages. It purges itself when content changes; LiteSpeed's copy expires after `max-age`.
  A stored page keeps the headers it was stored with, so a LiteSpeed hit can show `X-Exp-Cache: HIT` from the moment
  it was stored. Read `X-LiteSpeed-Cache` to know who answered.
- **The static cache** ([Static cache](../help/hosting/performance/static-cache.md)) does for anonymous visitors what
  the LiteSpeed cache does, with files that are regenerated on publish instead of expiring. Use one of the two. The
  block is in `.htaccess_root`, not in `.htaccess_root_static`.
- **Exponential Velocity** has its own response cache and does not read `.htaccess`. This guide is only about sites
  served by LiteSpeed or OpenLiteSpeed.
- **The preloader** ([Preloading caches](preloading-caches.md)) requests the pages of a site over its address
  (`site.ini [SiteSettings] SiteURL`) as an anonymous visitor. Through a LiteSpeed server, a run fills the LiteSpeed
  cache too, for `max-age` seconds.

### Telling LiteSpeed the same thing from Exponential

Exponential can send cacheable headers to anonymous visitors only, so the application and the server agree. In
`settings/override/site.ini.append.php`
([HTTP cache headers](../help/hosting/performance/http-cache-headers.md#cacheable-pages-for-anonymous-visitors)):

```ini
[HTTPHeaderSettings]
CustomHeader=enabled
OnlyForAnonymous=enabled
OnlyForContent=enabled

HeaderList[]
HeaderList[]=Cache-Control

Cache-Control[]
Cache-Control[/]=public, max-age=120
```

Then `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`. Anonymous content pages now say
`public, max-age=120`; signed-in users keep `no-cache, must-revalidate`, and the kernel drops `Pragma: no-cache` when it
overrides `Cache-Control`.

This is a second line of defense: on a server that obeys the response's `Cache-Control` (OpenLiteSpeed with
`ignoreRespCacheCtrl 0`), a page rendered for a signed-in user can never be stored, even if a rewrite condition were
wrong, and the language-chosen siteaccess keeps its `private`. It also lets browsers keep the page for `max-age`
seconds, so a visitor who signs in may see their browser's anonymous copy of a page they just viewed until it expires;
keep the value short. If you only want shared caches to keep pages, `public, max-age=0, s-maxage=120` says so to
standard caches; check on your server that LiteSpeed reads `s-maxage` from the response before relying on it.

## Choosing max-age, and what editors see

Nothing in Exponential tells LiteSpeed that a page changed. So `max-age` is the longest time an anonymous visitor can
see a page as it was before a publish.

| `max-age` | Good for |
|---|---|
| 60 to 120 seconds | sites whose editors publish often and check the public site right away; still absorbs any burst |
| 300 to 900 seconds | magazines and corporate sites that publish a few times a day |
| 3600 seconds and more | archives and documentation that rarely change, or with the purge listener of [section 14](#purging) |

Even 60 seconds removes almost all of the load of a crawler burst or a traffic peak: a page asked for 1,000 times a
minute is rendered once a minute.

**What editors see.** Editors are signed in, carry a session cookie and always get the live page. Only when they look
at the public site in another browser or a private window (as an anonymous visitor) do they see the stored copy until
it expires. Tell them that number.

## Purging

**What exists today.** Exponential sends nothing to LiteSpeed when content is published. The kernel clears its own
caches (view cache, template-block cache, the role-aware HTTP cache); the LiteSpeed copy expires after `max-age`.

**Ways to remove pages earlier:**

1. **Wait for `max-age`.** The simple, safe default.
2. **A purge header from your own extension.** LiteSpeed removes pages when a response carries
   `X-LiteSpeed-Purge` (LiteSpeed's developer guide, "Basic Controls"): `X-LiteSpeed-Purge: *` purges every public page
   of the site, `X-LiteSpeed-Purge: url=/news/my-article` one exact address (no wildcards). The response that carries
   it does not need to be cacheable. The kernel notifies `content/cache` (node ids, object ids) whenever it clears the
   view cache of content, and `content/cache/all` when all of it is cleared (`eZContentCacheManager`, `eZCache`). A
   listener in an extension of your own can turn that into the header. This is an example, **not part of the kernel
   and not tested on a LiteSpeed server here**:

   ```php
   <?php
   // extension/mysite/classes/mysitelitespeedpurge.php (run "php bin/php/ezpgenerateautoloads.php -e")
   class mySiteLiteSpeedPurge
   {
       /**
        * site.ini [Event] Listeners[]=content/cache@mySiteLiteSpeedPurge::purge
        *          Listeners[]=content/cache/all@mySiteLiteSpeedPurge::purge
        */
       public static function purge()
       {
           // Only a web request has a response that LiteSpeed reads
           if ( !isset( $_SERVER['REQUEST_METHOD'] ) || headers_sent() )
               return;
           header( 'X-LiteSpeed-Purge: *' );
       }
   }
   ```

   Register both listeners in `settings/override/site.ini.append.php`, under `[Event]`, so they apply to the siteaccess
   editors publish in. Limits: the purge reaches the cache of the site that answers the editor's request, so it works
   when editors publish on the same host and virtual host as the public site (`/admin` matched by URI). When the
   administration has a host name or virtual host of its own, the header is sent there and the public site's pages
   expire by `max-age` as before. A publish that runs outside a web request (a cronjob, asynchronous publishing, the
   console) has no response to carry the header. Purging everything is coarse but safe; per-address purges would need
   the node's URL aliases on every siteaccess and are left to you.
3. **Empty the cache directory.** The cache files are below the `CacheRoot` (LiteSpeed Enterprise) or the module's
   `storagePath` (OpenLiteSpeed). With a per-site `storagePath` such as `$VH_ROOT/lscache`, removing the files below it
   empties that site's cache. A server-level `CacheRoot` is shared by every site on the server: do not empty it on a
   shared server; ask the host.
4. **Lower `max-age` ahead of time**, for a migration or a design change: set it to a few seconds, wait until the old
   value has passed, make the change, then set it back.

## What must never be cached

The shipped block already keeps out everything in the first rows. The others depend on how a site is built.

| What | How it stays out |
|---|---|
| Editors, administrators, signed-in members | session cookie and `is_logged_in` cookie |
| Forms, sign-in, comments, collected information, the basket | `POST`; the `user/`, `shop/` and `content/` (other than `view`) addresses |
| The administration siteaccess | its URI (`admin`) or, when it has its own host name, the `HTTP_HOST` line; editors carry a session cookie anyway |
| REST (`/api/`, `index_rest.php`), SOAP (`index_soap.php`), WebDAV (`index_webdav.php`), the tree menu | rules above the block, with `[L]`; the `content/` condition |
| Searches and anything with a query string | `QUERY_STRING ^$` |
| Debug output (`site.ini [DebugSettings] DebugOutput=enabled` with `DebugByIP`) | **not automatic**: a page rendered for a debug address carries the debug report, and if it is stored, everybody gets it. Never switch debug output on for a production site that uses the page cache, or add `RewriteCond %{REMOTE_ADDR} !^203\.0\.113\.10$` with your address to the block |
| A siteaccess chosen by the browser's languages (`DefaultHostUriMatchMapItems`) | **not automatic**: the address answers per `Accept-Language`. With `ignoreRespCacheCtrl 0` and the kernel's `private` header it is not stored; otherwise exclude those addresses (usually only the bare host name, `/`), or give each language its own URI or host, which caches correctly |
| Mobile device detection (`site.ini [SiteAccessSettings] DetectMobileDevice=enabled`) | **not automatic**: the kernel redirects mobile browsers and sets the `eZMobileDeviceDetect` cookie. Keep it `disabled` (shipped) with the page cache, or add LiteSpeed's `vary=ismobile` rules matching the kernel's detection exactly |
| Blocks personalized without a session (by IP, geolocation, a random choice per request) | **not automatic**: everybody gets the stored variant; exclude those pages by URI or load the block by AJAX from an excluded address (`ezjscore/`) |
| Cookies your own extension sets that change a page | add their names to the cookie condition |

## Troubleshooting

### Always miss

1. **The page sets a cookie.** `curl -sI <address> | grep -i set-cookie` must print nothing. If it prints a session
   cookie, something on the page writes to the session for anonymous visitors (an extension, a template that stores a
   preference, `ForceStart=enabled`); fix that or exclude the address. LiteSpeed does not store a response that sets a
   cookie when its policy says so (OpenLiteSpeed: `respCookieCache 0`), and must not.
2. **The page says `no-cache` and the server obeys it.** With OpenLiteSpeed's `ignoreRespCacheCtrl 0`, or LiteSpeed
   Enterprise's "Ignore Response Cache-Control" set to No, the kernel's default `Cache-Control: no-cache` keeps the page
   out. Switch on the anonymous headers of [section 12](#telling-litespeed-the-same-thing-from-exponential); that is
   the safe fix.
3. **A query string.** Every `?` keeps the request out, including tracking parameters that campaigns add.
4. **A cookie in the condition.** Request without any cookie (`curl` sends none). A browser that was ever signed in
   still has a session cookie: test in a private window.
5. **OpenLiteSpeed did not read the rules.** It reads `.htaccess` only with `autoLoadHtaccess 1` and only after a
   restart; `CacheLookup` is ignored there, the module's `checkPublicCache 1` replaces it, and the module needs
   `ls_enabled 1` for the virtual host.
6. **No cache directory.** Without a `CacheRoot` (Enterprise) or a writable `storagePath` (OpenLiteSpeed) nothing is
   stored. Look in LiteSpeed's error log.

### No X-LiteSpeed-Cache header at all

The server is not LiteSpeed (then `<IfModule LiteSpeed>` is skipped, as intended), the block is still commented, a
`CacheEngine off` at server level is not switched back on for the site, or the address is excluded by a condition.
`curl -sI <address> | grep -i ^server` names the server.

### Stale pages

A page is up to `max-age` seconds old after a publish. Lower `max-age`, purge ([section 14](#purging)), or check that
editors look at the public site signed in. If a page stays old much longer than `max-age`, check whether something in
front (a CDN, a proxy) or the browser keeps it as well: `Cache-Control` and `Age` of the response tell.

### Signed-in users see cached pages

This must never happen. Check, in this order:

1. The cookie condition names your session cookie: sign in, look at the cookies of the site in the browser and compare
   the names with the condition (`PHPSESSID`, `eZSESSID...`, or the name `php.ini` gives).
2. The session cookie's `path` and `domain` (`site.ini [Session] CookiePath`, `CookieDomain`) cover the public site;
   a cookie limited to `/admin` is not sent to `/`, so the visitor looks anonymous there and correctly gets the public
   page. That is not a leak: it is the anonymous page.
3. The page was not stored from a signed-in response: with the anonymous headers of section 12 and a server that obeys
   them, it cannot be.

### Wrong language or wrong device version

See the language and mobile rows of [section 15](#what-must-never-be-cached).

### A page sets a cookie

A response that sets a cookie must not be stored: either the cookie would be lost for that visitor (LiteSpeed drops
`Set-Cookie` from stored pages) or it would belong to somebody else. Find out who sets it
(`curl -sI <address> | grep -i set-cookie`) and exclude that address, or stop setting the cookie for anonymous
visitors.

## Security notes

- **The only thing that keeps private pages private is the set of conditions.** Keep the cookie and address
  conditions; never remove one to get more hits. Test the signed-in direction of [section 11](#check-that-it-works)
  after every change.
- **Keep the safe defaults of Exponential:** `ForceStart=disabled`, the anonymous `Cache-Control` only with
  `OnlyForAnonymous=enabled`, debug output off in production.
- **Never cache the administration.** Exclude it by URI and by host name; editors carry a session cookie anyway.
- **Form tokens:** the `ezformtoken` extension protects only users who have a session and are signed in, and those never
  get stored pages; anonymous pages carry no token that a stored copy could leak.
- **A purge header is a write.** If you build the purge listener, send it only after content was changed, never on an
  ordinary page view, so a visitor cannot empty the cache by requesting pages.
- **Shared servers:** a server-level `CacheRoot` holds every site's pages. File permissions are the host's job; a
  per-site `storagePath` keeps each site's cache in its own directory.

## Measuring the gain

Measure on your own site; no figure here would be true for it. A fair comparison:

1. Pick five addresses the block caches (front page, a section page, an article, a listing, a page with images).
2. With the block off, request each ten times and note the median time to first byte (the `curl -w` loop of
   [section 11](#check-that-it-works)), or use `exp:benchmark`
   ([Benchmarking](benchmarking.md)).
3. Switch the block on, request each address once (the miss), then ten times again.
4. Under load (only on a site you are allowed to load): the same with a load tool and a fixed concurrency, comparing
   requests per second and PHP processes in use.

What to expect in kind: a hit is answered without PHP, close to the time of a static file of the same size; the PHP
processes that served those requests before stay free; the database sees only the misses and the signed-in requests.
How much of the traffic becomes hits is the share of anonymous `GET` requests without query string to cached addresses:
read it from the access log (`X-LiteSpeed-Cache` can be logged) after a day.

## Crawlers and bots

Crawlers are why many sites suddenly become slow: search engines, AI crawlers, SEO tools and monitoring request
hundreds of pages within a minute, in parallel, all anonymous. Without a page cache each request renders a page, takes
a PHP process and runs database queries; with the block, a crawler that walks the site gets the first copy of every
page rendered once per `max-age`, and every other visitor and crawler in that time gets hits.

- Crawlers send no cookies, so they always pass the cookie condition.
- Crawlers that add query strings (`?utm_...`, sorting parameters) are not helped; `robots.txt` can ask them not to
  follow such links, and canonical addresses in the templates tell them which address to use.
- A crawler that walks thousands of distinct pages still causes a miss per page; the [preloader](preloading-caches.md)
  warms the kernel's caches (and LiteSpeed's, for `max-age`) ahead of time, and the view cache makes those misses fast.
- If a site is slow under crawlers, the [Slow site](../help/hosting/runbooks/slow-site.md) runbook leads here.

## What was verified, and what was not

- **Verified against the code of this repository:** the session cookie names and when a session starts, the
  `is_logged_in` cookie, the default page headers, the private header of language-chosen siteaccesses, the maintenance
  page's headers, the `content/cache` and `content/cache/all` events, the order of the rules in `.htaccess_root`.
- **Verified against LiteSpeed's and OpenLiteSpeed's documentation:** `CacheLookup`, `CacheEnable`, `CacheEngine`,
  `CacheRoot`, the `cache-control` environment variable and its values, the `X-LiteSpeed-Cache` header values,
  `X-LiteSpeed-Purge`, the cache module parameters of OpenLiteSpeed and how OpenLiteSpeed reads `.htaccess`.
- **Not run here:** the block was not run on a LiteSpeed Enterprise or OpenLiteSpeed server for this guide (there is
  none on the machine the documentation is checked on), the purge listener is an untested example, and the defaults of
  the servers' "Ignore Response Cache-Control" and "Cache Response with Cookie" policies are not stated in the
  documentation consulted. Check your own server with [section 11](#check-that-it-works) before relying on it; that
  check tells you in two minutes whether it works and whether it is safe.

## References

- Kernel: `lib/ezsession/classes/ezsession.php` (`registerFunctions()`, `lazyStart()`, `siteCookieOptions()`),
  `kernel/private/classes/ezpkernelweb.php` (default headers, `is_logged_in`, `Vary` and `private` for
  language-chosen siteaccesses), `kernel/classes/expmaintenance.php` (the `503`), `kernel/classes/ezcontentcachemanager.php`
  and `kernel/classes/ezcache.php` (`content/cache`, `content/cache/all`), `kernel/private/classes/ezpevent.php`
  (`[Event] Listeners[]`), `kernel/classes/expvelocity.php` (Velocity's cookie list), `extension/ezformtoken/event/ezxformtoken.php`.
- Settings: `settings/site.ini` `[Session]`, `[HTTPHeaderSettings]`, `[DebugSettings]`, `[SiteAccessSettings]`, `[Event]`.
- The block: `.htaccess_root`; for a virtual host: [`litespeed-page-cache.conf`](../examples/web-servers/litespeed-page-cache.conf);
  for OpenLiteSpeed: [`openlitespeed-vhost.conf`](../examples/web-servers/openlitespeed-vhost.conf).
- LiteSpeed: [LSCache without a plugin](https://docs.litespeedtech.com/lscache/noplugin/),
  [its installation and checks](https://docs.litespeedtech.com/lscache/noplugin/installation/),
  [its rewrite rule settings](https://docs.litespeedtech.com/lscache/noplugin/settings/),
  [getting started (cache root and engine)](https://docs.litespeedtech.com/lscache/start/),
  [the developer guide's basic controls (purge, vary, cache-control headers)](https://docs.litespeedtech.com/lscache/devguide/controls/),
  [LSCache on cPanel](https://docs.litespeedtech.com/lsws/cp/cpanel/lscache/).
- OpenLiteSpeed: [the cache module](https://docs.openlitespeed.org/config/lscache/),
  [rewrite rules and .htaccess](https://docs.openlitespeed.org/config/rewriterules/).

## Related pages

- [LiteSpeed and OpenLiteSpeed](../help/hosting/servers/litespeed.md): hosting Exponential on LiteSpeed.
- [LiteSpeed and OpenLiteSpeed (example configuration)](../examples/web-servers/litespeed.md) and
  [Apache .htaccess](../examples/web-servers/apache-htaccess.md): the rules of `.htaccess_root` explained.
- [HTTP cache headers](../help/hosting/performance/http-cache-headers.md) and
  [Caching layers](../help/hosting/performance/caching-layers.md).
- [Varnish and reverse proxies](../help/hosting/performance/varnish-and-reverse-proxies.md): the same idea with a
  proxy in front.
- [Caches](caches.md), [Preloading caches](preloading-caches.md), [Benchmarking](benchmarking.md).
- [Plesk](../help/hosting/panels/plesk.md), [cPanel](../help/hosting/panels/cpanel.md),
  [DirectAdmin](../help/hosting/panels/directadmin.md).
- [Slow site](../help/hosting/runbooks/slow-site.md).
- [Free documentation](../help/free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../help/partners/partners-start-here.md) and [How to apply](../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
