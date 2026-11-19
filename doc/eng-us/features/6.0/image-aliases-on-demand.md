# Image aliases made on demand

Read this page if you want to know how Exponential 6.0.15 makes image aliases: why a page no longer converts images
while it renders, which addresses the images get, what a visitor sees while an alias is being made, how WebP and AVIF
are offered, how aliases are made ahead of time after publishing, how a development or staging site fetches the
images it is missing from production, and how to run and watch all of it from the administration and the command
line. The last sections give the measured performance and the limits.

Related: [Web server recipes for image aliases made on demand](../../examples/web-servers/image-aliases-on-demand.md)
(examples), [Upgrading to 6.0.15: image aliases made on demand](../../bc/6.0/image-aliases-on-demand.md) (upgrade
notes), [Image aliases](../../help/image-aliases.md) (help for Setup > Image aliases),
[`exp:image:alias`](../../examples/commands/expimagealias.md) (generated command reference),
[image.ini \[AliasSettings\]](../../examples/ini-settings/image/AliasSettings.md),
[\[AliasStaging\]](../../examples/ini-settings/image/AliasStaging.md) and
[\[AliasSource_pro\]](../../examples/ini-settings/image/AliasSource_pro.md) (generated settings reference).

## In short

- An **image alias** is a variation of an uploaded image defined in `settings/image.ini` (`small`, `large`, the
  theme's `i160` and so on). Up to 6.0.14 the template call `$attribute.content[alias]` converted every missing
  alias **while the page rendered**: a page with 200 new images after a cache clear waited for 200 conversions.
- In **on-demand mode**, the 6.0.15 default, the template call only **computes** the alias: its URL, file name, MIME
  type, width and height, without converting anything. The browser asks for the image, and the alias is made then,
  by the view `content/image` or by the light entry script `index_image.php`. No template changes.
- **One setting switches back**: `image.ini [AliasSettings] AliasGeneration=render` is the original behavior. The
  switch works both ways at any time; no data is migrated, and aliases made in one mode are used by the other.
- The address a template gets for an alias not made yet is **signed**, so the request needs no session, no user and
  no database until the alias is made. Unsigned addresses only serve files that exist.
- While an alias is being made, the browser gets a **placeholder**: a neutral SVG of the right size, no text, no
  logo, following the browser's dark mode. A small script, `exp_image_swap.js`, asks again and swaps in the real
  image.
- Aliases can carry **WebP and AVIF variants**, chosen by the browser's `Accept` header. Use them on large aliases
  only.
- After publishing, the new images' aliases are made **in the background** by the job queue (expqueue), so the
  first visitor finds them ready.
- A **development or staging site** fetches the originals and aliases it is missing from production over HTTPS,
  so it needs no copy of `var/storage`. A circuit breaker and an alert mail protect it from a failing source.
- **Setup > Image aliases** (`setup/aliases`) shows the state and controls everything; `./console exp:image:alias`
  does the same on the command line. Every event is written to `imagealias.log`.
- **Performance, honestly**: a page of 500 images whose aliases exist loads in **1.7 s** (Apache/PHP-FPM) and
  **1.9 s** (Velocity). A page of 500 **new** images is complete in 4.6 s on Apache/PHP-FPM (was 20.7 s) and 6.6 s
  on Velocity (was 40.7 s) on a heavily loaded server, once in 2.6 s, and its real images follow within about half a
  minute; converting 500 full-size
  originals alone takes more CPU time than 3 seconds of a 12-core server. Pre-generation after publishing is what
  makes long image lists fast; see [Performance and tuning](#performance-and-tuning).

## Contents

1. [Why on demand](#why-on-demand)
2. [The mode switch](#the-mode-switch)
3. [How an alias is made](#how-an-alias-is-made)
4. [The addresses and their signature](#the-addresses-and-their-signature)
5. [Placeholders and the swap script](#placeholders-and-the-swap-script)
6. [WebP and AVIF](#webp-and-avif)
7. [The queue, pre-generation and conversion slots](#the-queue-pre-generation-and-conversion-slots)
8. [Staging from production](#staging-from-production)
9. [Web servers](#web-servers)
10. [Setup > Image aliases](#setup--image-aliases)
11. [The command line: exp:image:alias](#the-command-line-expimagealias)
12. [The log](#the-log)
13. [All settings](#all-settings)
14. [Performance and tuning](#performance-and-tuning)
15. [Limits and what was not tested](#limits-and-what-was-not-tested)

## Why on demand

Converting an image takes tens of milliseconds (GD decodes the whole original, often 1600 to 2000 pixels wide). In
render mode those milliseconds add up inside one page request: measured in process on the test server, a template
that shows 500 missing `i160` aliases spends **24.8 seconds** converting before the first byte leaves the server.
After a cache clear, a new theme or a changed alias, every page with images pays that, one visitor at a time.

On demand, the same page has its HTML out in **0.4 to 0.6 seconds**; the aliases are made by the image requests the
browser sends in parallel, a few at a time per server, and the browser shows a placeholder for an image that takes
longer than `MaxWait`.

## The mode switch

```ini
# settings/override/image.ini.append.php
[AliasSettings]
# on-demand (the default) or render (the original: the template call converts while rendering)
AliasGeneration=on-demand
```

- **on-demand** (default for new and upgraded sites): templates get computed entries. An alias that already exists
  keeps its storage URL (a static file); only a missing or outdated one gets an on-demand address.
- **render**: the original behavior, unchanged code path. Use it to go back, for example when the web server rules
  of [Web servers](#web-servers) cannot be installed yet. Switch back to `on-demand` at any time.
- **Command line scripts always work in render mode** (cronjobs, mailers, exports, PDF and feed generators read the
  files they ask for), whatever the setting says. Exponential Velocity's workers are not command line scripts in this
  sense: they follow the setting like PHP-FPM.
- The computed size equals the size of the file that is made: it is computed per conversion step with the
  converter's own arithmetic (GD truncates, ImageMagick rounds), focal point and crop included. An alias made on
  demand is byte for byte the file render mode makes with the same converter (checked for 330 aliases of real
  images). Aliases made by a converter without size arithmetic (for example a theme's own Imagine handler) have no
  computed width and height.

## How an alias is made

1. The template asks for an alias. In on-demand mode the handler returns the computed entry: `url`, `filename`,
   `mime_type`, `width`, `height`, `full_path` (the storage path the file will have). Nothing is converted, nothing
   is written, and the database is not asked whether the file exists.
2. The browser requests the URL. `content/image` (or `index_image.php` for a storage path the web server did not
   find) checks the signature, then:
   - the file exists: a **302 redirect** to the storage URL (the web server sends the file) or, where the web server
     cannot serve the storage (a database cluster without a gateway, drafts), the file itself (`Delivery`);
   - the file is missing: the alias is made, with the same converter chain, focal point and crop as render mode, the
     same writes to the attribute's alias list and to `ezimagefile`, then answered as above;
   - every conversion slot is taken (a page full of new images): the **placeholder** (200) at once, and the alias
     goes to the **backlog**, made right after the answer (`Backlog`, see
     [The backlog](#the-backlog-made-right-after-the-answer));
   - making it takes longer than `MaxWait` (default 2 seconds; where there is no backlog): the **placeholder**
     (200), and the alias is queued for the job queue so it is ready when the browser asks again.
3. Locks are **per alias file**, not per original: the aliases of one image are made in parallel, and two requests
   for the same alias produce one conversion. `[ImageConverterSettings] LockTimeout` (default 60 seconds) bounds the
   wait for another process; on a single server the lock is a file lock in `var/<site>/cache/imagealias-locks`, on a
   database cluster (eZDFS) the `.generating` entry of the alias file.
4. A conversion that has started is always finished (GD and ImageMagick cannot be interrupted without losing the
   work); after `MaxWait` no new conversion starts in that request. The worst case for one request is `MaxWait` plus
   one conversion.

Real aliases are sent with `Cache-Control: public, max-age=CacheMaxAge, immutable` (one year by default) and may be
kept by Velocity's response cache and the HTTP cache. Placeholders are sent with `no-store` and are never cached.
`content/image` starts **no session**, also when the visitor has a session cookie.

## The addresses and their signature

`[AliasSettings] OnDemandURL` chooses the address a template gets for an alias that is not made yet:

| Value | Address | Makes a missing alias |
|---|---|---|
| `token` (default) | `/content/image/<token>/<file name>` | yes: the signed token carries attribute, version, language, alias and the original's storage path, so the request needs no database until the alias is made |
| `ids-token` | `/content/image/<attribute>/<version>/<language>/<alias>/<token>/<file name>` | yes (signed) |
| `ids` | `/content/image/<attribute>/<version>/<language>/<alias>/<file name>` | no: serves an alias that exists, else a 404 placeholder |
| `node` | `/content/image/<node>/<attribute identifier>/<alias>` | no, as `ids` |
| `storage` | the plain storage path, as render mode gives it | yes, when the path names a configured alias of a published image; a miss reaches `index_image.php` through the web server rule |

The arguments are module view parameters, never a query string. A URI siteaccess in front works
(`/eng/content/image/...`).

**Signature rules:**

- The key is the secret `image_alias_signing` (Setup > Secrets). It is created on first use (32 random bytes) and is
  never replaced because a read failed.
- A **valid signature counts as read permission**: no user, session or policy lookup, and it may make or fetch the
  alias.
- A missing, wrong or old signature still **serves a file that exists**; it never makes one (404 placeholder).
- **Key rotation**: Setup > Image aliases > Dashboard, **Rotate the key**. The previous key is kept as
  `image_alias_signing_previous` and accepted for `SigningKeyGrace` seconds (default 604800, a week), so pages cached
  with the old URLs keep working. Velocity workers read the key again at most every 300 seconds.
- **Drafts and previews** (`images-versioned`) get signatures that expire after `DraftTokenLifetime` seconds
  (default 3600). Published images get URLs that do not expire.

## Placeholders and the swap script

`Placeholder=enabled` (default): instead of a broken image, a missing image answers a neutral SVG of the computed
size, in a soft tone that follows the browser's dark mode, with no text and no logo. It applies to **any** missing
image, not only aliases. A pending image (signed, being made) answers 200; a refused or unknown one answers 404. Both
carry the header `X-Image-Placeholder: 1` and `Server-Timing: exp-image-placeholder`, and `Cache-Control: no-store`.

`SwapScript=enabled` (default) adds `design/standard/javascript/exp_image_swap.js` to `design.ini
[JavaScriptSettings] FrontendJavaScriptList[]`. It finds the images that came back as placeholders (by the
`Server-Timing` marker) and swaps `src`/`srcset` when the real image is there. It asks in rounds: the waiting images
in page order, four at a time, stopping at the first one that is still a placeholder (the server makes them in the
order they were asked for, so the ones after it are not ready either). The pause between rounds starts at 0.5 s and
grows to 3 s while nothing comes; after a minute without any image made it gives up. A page of 500 new images costs
about one request per image made plus one per round.

### Page placeholders: a page with many new images

`PagePlaceholders=enabled` (default). Every image not made yet is a request to PHP (`content/image`), and on a page of
hundreds of new images those requests, not the conversions, decide when the page has loaded. So the first
`PagePlaceholderAfter` (default 24) images not made yet of a page are linked through `content/image` as always, and
the rest are linked to a **static placeholder** of their size, which the web server answers without PHP and the
browser asks for once per size:

```
var/<site>/cache/public/imagealias-placeholder/160x106.svg#exp-src=<the content/image address>&exp-depth=6
```

- The fragment (`#...`) is never sent to the server. It names the real image for the swap script, which asks for it
  in page order and puts it in place once it is made; `exp-depth` tells the script how many path segments the
  placeholder lies below the installation's root, so a relative address resolves as the template's `ezroot` did.
- The aliases go to the backlog while the page is made, in page order, and the drainers start right after the page's
  answer, so they are being made before the browser has the page.
- A page that links such placeholders is kept in the view cache for at most `PagePlaceholderTime` seconds (default
  60), so the next visitors get the real addresses.
- Without the swap script these images keep the placeholder until the page is loaded again: a theme with a pagelayout
  of its own must load it (see above).
- Only where the backlog applies (a server that answers first) and only for aliases that are not made yet; an
  outdated alias keeps its address (its old file shows until the new one is made). `disabled`: every image not made
  yet is linked through `content/image`, as before.

**A theme with a pagelayout of its own must load the script itself**, or the placeholders stay until the next page
view. The kernel's designs load `FrontendJavaScriptList[]`; a theme that writes its own `<script>` tags does not. The
media theme (`sevenx_themes_media`) does it at the bottom of its page, in
`design/media/templates/pagelayout/script_bottom.tpl`:

```smarty
{if ezini( 'AliasSettings', 'SwapScript', 'image.ini' )|ne( 'disabled' )}{ezscript( array( 'exp_image_swap.js' ) )}{/if}
```

A theme operator that reads the alias list and builds image URLs itself must take the URL from the handler
(`$handler->imageAlias( $alias )['url']`), not check for a file and fall back to the original; otherwise its pages
get neither on-demand nor negotiated URLs. The media theme's `ng_image_alias` operator was changed this way.

## WebP and AVIF

An alias block may list extra formats for the browsers that accept them:

```ini
# settings/override/image.ini.append.php
[i1320]
Formats[]=webp
Formats[]=avif
```

- `content/image` answers with a variant when the `Accept` header names the type explicitly (`image/webp`,
  `image/avif`; `image/*` and `*/*` do not count; `q=0` refuses) and `Negotiate=enabled`. The order of `Formats[]`
  decides between equal preferences. Every answer for such an alias carries `Vary: Accept`.
- A variant is stored next to its alias as `<alias file>.<format>` (`foo_large.jpg.webp`), made from the alias file,
  and made again when the alias is newer. It is in neither the attribute's XML nor `ezimagefile`, so the database
  stays exactly as render mode writes it; it is removed with its alias. GIF (animation) and SVG aliases get no
  variants.
- The converter for variants is `PreferredConverter` (`auto`: the imagick extension, then the ImageMagick command
  while `[ImageMagick] IsEnabled=true`, then GD; a named one is tried first, then the others). A format no converter
  can write is skipped. The aliases of `AliasList[]` themselves keep using `[ImageConverterSettings]
  ImageConverters[]` in both modes, so their bytes do not change.
- Quality: the alias block's `Quality[]=image/webp;80`, else `[MIMETypeSettings] Quality[]`, else 80 for WebP and 60
  for AVIF.
- The storage path of a variant also works on its own (`index_image.php` makes it), for web servers that negotiate
  themselves.

**Use `Formats[]` only on large aliases** shown a few times per page (hero, article image, full width). An alias with
formats to negotiate is always linked through `content/image`, even when it is made, so every view of it is a PHP
request plus a redirect. On a list of 500 thumbnails that costs seconds on every page view (see
[Performance and tuning](#performance-and-tuning)). For thumbnails, leave formats off or let the web server
negotiate (for example an nginx `map $http_accept` on the variant's storage path).

The web server must send `.avif` and `.webp` with their types; a variant named `foo.jpg.avif` is otherwise sent as
`image/jpeg` after its inner `.jpg`. `.htaccess_root` carries `AddType image/avif .avif`; see the
[recipes](../../examples/web-servers/image-aliases-on-demand.md#avif-and-webp-types).

## The queue, pre-generation and conversion slots

The kernel ships three job types for the job queue (expqueue) in `settings/expqueue.ini`: `image.alias.generate`
(visitor misses and pre-generation), `image.alias.bulk` (generate, regenerate or purge over a scope) and
`image.alias.resync` (staging). A site overrides their class and timeouts in
`settings/override/expqueue.ini.append.php`. Without expqueue everything still works: nothing is queued, bulk runs
run in the request or on the command line, and a missing alias is made when it is asked for.

**Priorities.** Visitor misses go ahead of everything (class interactive, priority 80), then publishing (editorial,
60), then the administration's runs (interactive, 60), then bulk work (bulk, 40). A class a site does not have falls
back to the next fitting one.

**Pre-generation after publishing** (`PreGenerate=enabled`, on-demand mode only): when an object with images is
published, one job makes the aliases of the new images.

- By default every alias of the **union** of `AliasList[]` of the current siteaccess and its
  `RelatedSiteAccessList[]`, each made as the siteaccess that lists it defines it (a site-only alias is made with the
  site's `image.ini`), plus the `Formats[]` variants.
- `PreGenerateAliasList[]` limits it to some aliases (`PreGenerateAliasList[]=i1320`). A name no related siteaccess
  defines is skipped, with a warning and one `imagealias.log` line per publication
  (`event=queue result=unknown-alias`). Every other alias is still made on demand.
- `PreGenerateFormats=disabled` makes the aliases without their WebP/AVIF variants.
- Without the queue nothing is converted while publishing; `imagealias.log` says `result=no-queue`.
- On the test server a new image had 35 aliases (26 of the admin siteaccess, 9 of the site only) and 6 variants:
  about 2 to 3.6 seconds of worker time per image.

**Conversion slots** (`MaxParallel`, default 2): at most this many conversions (aliases and variants) run at once per
server, counted over every process: PHP-FPM, Velocity and the command line. A slot is a lock file in
`<cache dir>/imagealias-slots/<server name>/`. A request that finds every slot taken answers the placeholder at once
and leaves the alias to the backlog (below); without the backlog it waits within `MaxWait`, then answers the
placeholder and queues the alias at the visitors' priority. Slots apply in on-demand mode only.

### The backlog: made right after the answer

`Backlog=enabled` (default). A page with hundreds of new images asks for all of them at once. A request never waits
for a slot: with one free it makes its alias at once; otherwise it answers the placeholder in a few milliseconds,
without a database query, and writes the alias to the backlog of the server, one small file per alias in
`<cache dir>/imagealias-backlog/<server name>/`. Once its answer has gone out (PHP-FPM, LiteSpeed and FrankenPHP
finish the request first; Velocity runs it after sending the answer) a request that gets a free slot makes the
backlog, oldest first, for at most `BacklogTime` seconds (default 30), then gives the slot back. So at most
`MaxParallel` processes work on the backlog at once, all others are free for the next request at once, and no job is
written to the database for each image.

- Each alias is made as a job of the queue makes it: the published image, the `image.ini` of the siteaccess the page
  was shown in, the same lock and slots, so the files and the database writes are those of render mode.
- The cronjob part `imagealiasbacklog.php` (group `frequent`) makes what no request has taken (a server that gets no
  more requests) and gives back an entry whose process died (after 5 minutes).
- **Drainer processes** (`BacklogDrainer=process`, default): after its answer the request does not make the backlog
  itself; it starts a drainer, `bin/php/expimagealiasdrain.php --slot=<n>`, detached (its own session, output
  discarded), and is free for the next request at once. One drainer per four waiting aliases, at most `MaxParallel`
  per server (a lock file each in `<backlog directory>/.drainers/`); each makes the backlog in a conversion slot until
  it is empty or `BacklogTime` seconds have passed. Started by root (Velocity), a drainer becomes the owner of `var/`
  first. Why: Velocity hands a worker its next request as soon as it has answered, so a worker that made the backlog
  itself kept that request waiting up to `BacklogTime` (measured: 19 to 30 s for one image during a cold page);
  PHP-FPM's children were held the same way. Needs `proc_open()` and the PHP command line; without them, and with
  `BacklogDrainer=request`, the request makes the backlog after its answer as before.
- The Apache module (`mod_php`) and the command line cannot answer first: there the request waits within `MaxWait`
  and queues a job, as with `Backlog=disabled`.
- With staging (`[AliasStaging] SyncFrom`) a request still fetches within `MaxWait`; only a request that finds every
  slot taken after that uses the backlog.

**Bulk runs** lower their worker's priority to the nice value `BulkNice` (default 10), so they give way to the web
server, and can be **paused and resumed** for every server at once (Setup > Image aliases, or
`exp:image:alias status --pause` / `--resume`); a paused run goes on where it stopped. Removing aliases (purge,
regenerate) also clears Velocity's response cache.

## Staging from production

A site that is not the production site can fetch the images it is missing from another installation, so a
development or staging copy needs the database but **no copy of `var/storage`**.

```ini
# settings/override/image.ini.append.php on the staging site
[AliasStaging]
Environment=stage
SyncFrom=pro

[AliasSource_pro]
BaseURL=https://www.example.org
# the name of a secret, never the value: Setup > Secrets, or env:<VARIABLE>
Credential=image_staging_pro
```

- **Environments**: `alpha` (early test of new code), `dev` (a developer's installation), `beta` (preview for testers
  and editors), `stage` (pre-production, the last check before a release), `pro` (production: the source of the
  others, **never fetches**), `post` (a copy after a release, for checks and hotfixes), `arc` (a frozen archive).
  Default `pro`; an unknown value counts as `pro`. Most sites only need a `dev` or `stage` installation that syncs
  from `pro`.
- **Sources**: one `[AliasSource_<environment>]` block per environment that can be a source, with `BaseURL` (http or
  https, no user name, password or query in it) and `Credential`. `SyncFrom` names the block to use.
- **Credentials** live in **expSecrets** (Setup > Secrets), or in an environment variable (`env:<VARIABLE>`): the
  secret holds `user:password` (basic authentication) or `Bearer <token>`. The value is never written to a setting,
  the log, a mail or the page; a named secret that is missing sends no request at all.
- **Path map**: `PathMap[]=<local prefix>;<source prefix>` for a source whose storage paths differ (the source prefix
  a path below `BaseURL` or a whole URL; the longest local prefix wins).
- **On a page view**: a missing alias is fetched from the source first; when the source does not have it either, its
  original is fetched and the alias is made here. A missing original asked for by its storage path is fetched. All
  within `MaxWait`; after that the placeholder, and the browser asks again. Only published images are staged, never
  drafts. A fetched file is checked (200 only, the right image type, no HTML error page, at most `MaxFileSize`, 64 MB
  by default) and stored through the cluster file handler. Fetched aliases are recorded in the staging site's own
  alias list as render mode would record them.
- **Resync** fetches the files of many images at once (Setup > Image aliases > Bulk runs, or
  `exp:image:alias resync`). Scopes: the whole site, a node or a subtree, classes, attributes, aliases (`original` for
  the originals) and "changed since". Per run: **missing only** (default) or **overwrite** files that differ (size,
  then SHA-256), and a dry run. A resync fetches what the stored alias lists name; it never writes the database.
  Only on a staging installation.
- **Circuit breaker**: after `FailuresBeforePause` failures in a row (default 5: no answer, a timeout, 401/403, an
  invalid answer such as an HTML error page, a file the source has neither as alias nor as original) the source is
  paused for `PauseSeconds` (default 300): missing images get the placeholder at once, nobody waits for the source.
  One file that comes through ends the count. A single alias the source does not have is not a failure (its original
  may come). Setup > Image aliases shows the pause, the reason and the last error, and can reset it.
- **Alert mail**: when a source is paused, `AlertEmail=enabled` sends a mail to `AlertRecipients[]` (else `site.ini
  [MailSettings] AdminEmail`), at most once an hour per source, through the site's own mail transport.

Never let two installations share one database with staging switched on: the staging site records the aliases it
fetched or made in the database, and production would then link files it does not have.

## Web servers

A missing storage image and `content/image` must reach `index_image.php`, the light entry script (no session, no page
kernel, no template engine). Every recipe is on the page
[Web server recipes for image aliases made on demand](../../examples/web-servers/image-aliases-on-demand.md):

- **Apache**: `.htaccess_root` and `.htaccess_root_static` carry the rules, active. A virtual host with
  `AllowOverride None` needs them in its own configuration, and an allow-list of root scripts needs `_image`.
- **nginx**: `try_files $uri /index_image.php$is_args$args;` on the storage location, a location for `content/image`,
  and a FastCGI location for `index_image.php`. nginx in front of Apache falls back to Apache.
- **LiteSpeed** reads `.htaccess`; OpenLiteSpeed gets the same rules in its virtual host.
- **FrankenPHP/Caddy**: the Caddyfile Velocity's frankenphp engine writes has them; a hand-written one needs the same
  block.
- **Exponential Velocity**: nothing to do; its front-controller routes send both to `index_image.php` in the worker.
- **Database cluster (eZDFS/eZDB)**: the cluster gateway hands an image it has no file for on to `index_image.php`
  (`CLUSTER_IMAGE_ALIAS_ON_DEMAND`, on by default).
- **Request shield**: `content/image`, `index_image.php` and storage misses need a budget of their own, or a page of
  hundreds of new images trips the page-view pace.

Without the rules the site still works in on-demand mode with the default `OnDemandURL=token`: missing aliases are
linked as `content/image` URLs, which `index.php` answers too (with a full kernel boot per image, so slower). Only
`OnDemandURL=storage` depends on the storage-miss rule.

## Setup > Image aliases

In the administration: **Setup > Image aliases** (`setup/aliases`), in the System group right after **Cache**. Seeing
the page needs the policy `setup/aliases`; every action needs `setup/managealiases`. Administrators have
both. The page has five parts:

- **Dashboard**: mode, address form, delivery, `MaxWait`, conversion slots in use, negotiation, placeholder and swap
  script, pre-generation (with the alias list when set), the number of aliases; the converters of the format variants
  in try order with WebP/AVIF; environment and source (breaker state, failures, last error, a reset button); jobs
  waiting, running and failed per job type, and whether bulk runs are paused; the last 24 hours from the log
  (generated, placeholders, failures, deferred to a job, staged, runs, each a link into the history; an alias
  deferred to a job is no failure and raises no warning); the signing key's age, the previous key's validity and
  **Rotate the key**; checks that need attention; "How it works".
- **Bulk runs**: generate, regenerate or purge (and resync on a staging installation) over the whole site, a node or a
  subtree, with classes, attributes, aliases, "changed since" and a limit; dry run; overwrite for resync. Runs are
  queued as jobs with a live progress card (cancel, pause, resume); without a queue at most 50 images run in the
  request. The latest ten runs are listed.
- **Test tools**: **One image** by node or object id: per image field and language the original and every alias with
  the address a template gets now, size, type, whether the file is there or made on demand, and the variants;
  **Make** or **Check** one alias, with its time. **Connection to a source**: a request to the source's base address
  or a storage path with the credential sent (never shown): status, type, size, time, verdict.
- **Run history**: the newest lines of `imagealias.log`, filtered by event, run id, alias or path text; follow mode
  refreshes every 3 seconds.
- **Settings**: every key of `[AliasSettings]` and `[AliasStaging]` and `BaseURL`/`Credential` of the source blocks,
  with help text and default. **Preview the changes** shows what saving would change; saving writes
  `settings/override/image.ini.append.php` through the INI editor (with a backup and an audit entry) after "verify
  it's you". A credential is the name of a secret, never its value.

The content tree's context menu has **Advanced > Image aliases**: generate, regenerate or remove the aliases of this
item or its subtree, as a job (with `setup/managealiases`). Help for the page: [Image aliases](../../help/image-aliases.md).

## The command line: exp:image:alias

`./console exp:image:alias <subcommand> [options]` (also `php bin/php/expimagealias.php`) does what the page does.
Started as root, it first becomes the owner of `var/`, so the files it writes belong to the web server's user.

| Subcommand | What it does |
|---|---|
| `status` (default) | mode, settings, converters, staging source, queue, pause, the last 24 hours of the log (failures and aliases deferred to a job apart; json: `dashboard.counts.failures` and `.deferred`), checks and the latest runs. `--job=<id>` one job with its progress; `--pause[=<reason>]` and `--resume` pause or let go on the bulk runs of every server; `--cancel=<id>` cancels a job of image aliases |
| `generate` | make the aliases that are missing |
| `regenerate` | remove the aliases and make them again |
| `purge` | remove the aliases (the originals stay); a page view makes them again on demand |
| `resync` | fetch missing originals and aliases from the staging source (staging installations only) |
| `test` | `--node=<id>` or `--object=<id>`: per alias the address a template gets, size, type, file there or made on demand, variants; `--alias=<name>` makes it now (`--dry-run`: only checks); `--source[=<env>]` tests the connection to a staging source, `--path=<p>` with a storage path |

| Option | Meaning |
|---|---|
| `--node=<id>`, `--subtree=<id>` | the images of the object at this node, or of every object below it (the node included) |
| `--object=<id>` | `test`: the images of this object |
| `--class=a,b`, `--attribute=a,b` | only objects of these classes, only these image attributes (identifiers) |
| `--alias=a,b` | only these aliases (`resync`: `original` for the originals); `test`: the alias to make or check |
| `--since=<when>` | only objects changed since a date (`2026-11-01`, `"2026-11-01 14:30"`) or a time ago (`2d`, `12h`, `30m`, `"1 week"`) |
| `--limit=<n>` | at most this many images |
| `--dry-run` | generate, regenerate, purge: count the images only; resync: say what would be fetched; `test --alias`: report only |
| `--overwrite` | resync: also replace files that differ from the source |
| `--queue` | queue the run as a job and print its id instead of running it here |
| `--wait` | a run here waits while bulk runs are paused, instead of stopping |
| `--all` | regenerate, purge: allow a run over every image of the installation |
| `--format=text\|json` | text (default) or the machine output `exp-image-alias/1` |

Without a scope a run covers every image; `regenerate` and `purge` then need `--all`. The aliases are those of the
siteaccess (`-s`) and its `RelatedSiteAccessList[]`. A run in the process makes aliases exactly as the jobs do (the
same locks, slots, variants, stored data and log). **Exit codes**: 0 done; 1 a run or test had failures; 2 wrong
options; 3 not available (no job queue for `--queue`, not a staging installation, no such image or job); 4 stopped
because bulk runs are paused.

```bash
./console exp:image:alias status
./console exp:image:alias generate --subtree=93 --dry-run
./console exp:image:alias generate --subtree=93
./console exp:image:alias regenerate --node=93 --alias=i160,i320 --queue
./console exp:image:alias status --job=12898
./console exp:image:alias purge --class=image --since=2d
./console exp:image:alias resync --subtree=2 --since=1w --overwrite
./console exp:image:alias test --node=93
./console exp:image:alias status --pause="nightly import"
```

`exp:cache imagealias` (clear every alias at once) and Setup > Cache stay as they were; their help points here.

## The log

Every event is one line in `imagealias.log` in the site's log directory (`var/<site>/log/` or `var/log/`), written
through the kernel's log, so it rotates under `site.ini [FileSettings] LogFileMaxSize`, `LogFileRotateCount` and
`LogFileCompress`:

```
[ Nov 13 2026 09:12:01 ][ site ][ <request> ] event=generate result=ok ms=84 alias=small run=- path=var/site/storage/...
```

Events: `generate`, `fail`, `defer`, `placeholder`, `stage`, `queue`, `run-start`, `run-end`, `pause`, `resume` (and
`serve`, which is not written: one line per image request would flood the log). `defer` (`result=timeout`) means no
conversion slot or lock came free within the wait: the request answered the placeholder and a job makes the alias or
variant, so it is no failure. Logs written before `defer` existed have `event=fail result=timeout` for this; the
dashboard and the run history read those lines as `defer` (the file is not changed). A staging fetch that timed out
names its source (`source=`) and stays a `fail`. A bulk run's `run-start` and `run-end` carry
the run id that the page and the command print; `run-end` carries the counts. Lines of a conversion carry its slot,
how long it waited for the slot and how long it held it. The run history of Setup > Image aliases and Setup > Log files
read it live.

A busy page of new images fills the default 2 MB of a log file in about a minute; raise `LogFileMaxSize` if the
history should cover more than the last minutes on such a site.

## All settings

`settings/image.ini`, overridden in `settings/override/image.ini.append.php` (or per siteaccess). Every key is
documented in the file and in the generated reference
([\[AliasSettings\]](../../examples/ini-settings/image/AliasSettings.md),
[\[AliasStaging\]](../../examples/ini-settings/image/AliasStaging.md),
[\[AliasSource_pro\]](../../examples/ini-settings/image/AliasSource_pro.md)).

| Setting | Default | What it does |
|---|---|---|
| `[AliasSettings] AliasGeneration` | `on-demand` | `on-demand` or `render` (the original behavior) |
| `OnDemandURL` | `token` | `token`, `ids-token`, `ids`, `node` or `storage` ([addresses](#the-addresses-and-their-signature)) |
| `Delivery` | `auto` | `auto` (redirect when the web server serves the storage, else the file), `redirect`, `stream` |
| `MaxWait` | `2` | seconds a request may spend making or fetching an alias, waits included; then the placeholder |
| `DraftTokenLifetime` | `3600` | seconds a signed URL of a draft or preview image stays valid |
| `SigningKeyGrace` | `604800` | seconds the previous signing key is still accepted after a rotation |
| `PreferredConverter` | `auto` | converter of the WebP/AVIF variants: `auto`, `imagick`, `imagemagick`, `gd` |
| `Negotiate` | `enabled` | answer with a WebP/AVIF variant when the browser accepts it and the alias lists it |
| `Placeholder` | `enabled` | a sized neutral SVG while an image is missing |
| `SwapScript` | `enabled` | the script that swaps placeholders for the real image |
| `PreGenerate` | `enabled` | make the new images' aliases in the background after publishing |
| `PreGenerateAliasList[]` | empty (all) | limit pre-generation to these aliases |
| `PreGenerateFormats` | `enabled` | pre-generation also makes the WebP/AVIF variants |
| `MaxParallel` | `2` | conversions at once per server, over every process |
| `Backlog` | `enabled` | a request that finds every slot taken answers at once; the alias is made right after the answer |
| `BacklogTime` | `30` | seconds a request goes on making the backlog after its answer (1 to 600) |
| `BacklogDrainer` | `process` | who makes the backlog after the answer: drainer processes, or `request` (the request itself) |
| `PagePlaceholders` | `enabled` | past `PagePlaceholderAfter` images not made yet, a page links the rest to a static placeholder |
| `PagePlaceholderAfter` | `24` | images not made yet a page links through `content/image` first (0 to 10000) |
| `PagePlaceholderTime` | `60` | seconds a page with such placeholders may stay in the view cache (10 to 3600) |
| `BulkNice` | `10` | nice value of a bulk run's worker (0 leaves it) |
| `CacheMaxAge` | `31536000` | `Cache-Control` max-age of a real alias (placeholders: `no-store`) |
| `LogFileMaxSize` | `20971520` | Bytes `var/log/imagealias.log` may reach before it is rotated: its own size, not site.ini `LogFileMaxSize` for all logs, so a bulk run keeps its history (1 MB to 1 GB) |
| `[<alias>] Formats[]` | none | `webp`, `avif`: variants for negotiation |
| `[ImageConverterSettings] LockTimeout` | `60` | seconds a process waits for another process's lock on one alias file (0: no wait) |
| `[AliasStaging] Environment` | `pro` | `alpha`, `dev`, `beta`, `stage`, `pro`, `post`, `arc`; `pro` never fetches |
| `SyncFrom` | empty | the environment to fetch from; empty: no staging |
| `PathMap[]` | none | `<local prefix>;<source prefix>` |
| `Timeout` | `5` | seconds one download may take (never more than what is left of `MaxWait` on a page) |
| `MaxFileSize` | `67108864` | the largest file fetched, in bytes |
| `FailuresBeforePause` | `5` | failures in a row before the source is paused |
| `PauseSeconds` | `300` | how long a paused source is not asked |
| `AlertEmail` | `enabled` | mail when a source is paused (at most once an hour per source) |
| `AlertRecipients[]` | none | the addresses; none: `site.ini [MailSettings] AdminEmail` |
| `[AliasSource_<env>] BaseURL` | empty | the source installation, e.g. `https://www.example.org` |
| `Credential` | empty | the name of a secret (or `env:<VARIABLE>`) holding `user:password` or `Bearer <token>` |

`config.cluster.php`: `define( 'CLUSTER_IMAGE_ALIAS_ON_DEMAND', false );` stops the cluster gateway from handing
missing images to `index_image.php` (default: it does).

## Performance and tuning

**Measured again on 19 November 2026**, after the page's backlog entries were written after its answer (they cost a
page of 500 new images about 0.3 s before its first byte) and drainers that run out of time with entries waiting
start the next ones. Same page, `MaxParallel=6`, three runs each, load 18 to 35 on 12 cores, the browser on the same
server:

| Page | Server | State | Load (median) | Complete | Real | First byte |
|---|---|---|---|---|---|---|
| 500 x i160 | Apache/PHP-FPM | cold | **2.8 s** | 2.6 s | 32.6 s | 0.8 s (1.1 to 1.3 s before) |
| 500 x i160 | Velocity | cold | 4.2 s | 4.1 s | 37.3 s | 1.2 s |
| 500 x i160 | Apache/PHP-FPM | warm | 2.7 s | 2.7 s | 2.7 s | 0.2 s |
| 500 x i160 | Velocity | warm | 4.3 s | 4.3 s | 4.3 s | 0.5 s |

- The target (complete in under 3 s cold) is met on Apache/PHP-FPM. On Velocity a warm page of 500 stored images is
  slower than the cold one, which asks for a few dozen placeholder files: its HTTP/2 path for many streams on one
  connection is the limit there, not the aliases.
- About 1.1 to 1.5 s of each load is the browser laying out 500 images; the alias code takes a few milliseconds of it.

**Measured again on 17 November 2026**, after two changes: page placeholders (past 24 images not made yet a page
links the rest to a static placeholder, no PHP request; see
[Page placeholders](#page-placeholders-a-page-with-many-new-images)) and drainer processes (the backlog is made by
processes of their own, not by the web server's; on Velocity a worker that drained kept the next request it was given
waiting up to 30 s). Same server and page, `MaxParallel=6`, medians of three runs; the server was far busier than on
15 November (load 85 to 124 on 12 cores during these runs, 13 to 36 during the runs before the changes, the same day):

| Page | Server | State | Load before (same day) | Load now | Complete now | Real now |
|---|---|---|---|---|---|---|
| 500 x i160 | Apache/PHP-FPM | cold | 18.2 s | 4.6 s | 4.3 s | 50.6 s |
| 500 x i160 | Velocity | cold | 30.7 s | 6.6 s | 6.3 s | over 60 s |
| 500 x i160 | Apache/PHP-FPM | warm (HTML from the cold visit) | 3.1 s | 5.3 s | 5.3 s | 5.3 s |
| 500 x i160 | Velocity | warm (HTML from the cold visit) | 2.8 s | 3.5 s | 3.5 s | 3.5 s |

- One cold run on Apache/PHP-FPM, at a lower load the same morning, was complete in **2.6 s**. The warm figures are
  slower only through the load: nothing on the warm path changed.
- The page now costs 24 PHP requests and one static request per placeholder size, instead of 500 PHP requests; the
  drainers make the aliases at about 15 to 30 a second on this server, and the swap script shows them in page order.
- "Real" is bound by the conversions (about 40 s of CPU for 500 full-size originals), more so at this load; it is what
  pre-generation after publishing is for.

**Measured again on 15 November 2026**, after three changes: the backlog (a request never waits for a slot), the
signing keys kept between PHP-FPM requests (no database for an image request) and the extension directory read once
per request (`index_image.php` answered about 30 requests a second on PHP-FPM before, about 150 after). Same server,
same page of 500 `i160` images, `MaxParallel=6`, load 10 to 38 from other work, medians of three runs (before: two):

| Page | Server | State | Load before | Load now | Complete now | Real now |
|---|---|---|---|---|---|---|
| 500 x i160 | Apache/PHP-FPM | cold | 25.2 s | 8.2 s | 7.4 s | 32.7 s |
| 500 x i160 | Velocity | cold | 30.8 s | 24.4 s | 24.4 s | 48.7 s |
| 500 x i160 | Apache/PHP-FPM | warm (HTML from the cold visit) | 2.8 s | 1.7 s | 1.7 s | 1.7 s |
| 500 x i160 | Velocity | warm (HTML from the cold visit) | 10.4 s | 1.9 s | 1.9 s | 1.9 s |
| 500 x i160 | Apache/PHP-FPM | warm, fresh HTML | | 2.1 s | 2.1 s | 2.1 s |
| 500 x i160 | Velocity | warm, fresh HTML | | 2.5 s | 2.5 s | 2.5 s |

So the 3-second target is met for a page whose aliases exist, on both servers, and not for a page of 500 new images:

- **The conversions themselves**: 500 `i160` from originals of 800 to 1920 pixels take about 40 seconds of CPU time
  (GD decodes the whole original); 6 slots made 13 to 21 per second here. "Real" cannot come in 3 seconds on 12
  cores, whatever the server does; it is what pre-generation is for.
- **500 requests to PHP**: every placeholder is still a PHP request. Apache/PHP-FPM behind nginx answered about 75
  to 180 a second over one HTTP/2 connection here, depending on the load; the page is complete when the last one is
  answered.
- **Velocity over HTTP/2**: one HTTP/2 connection to Velocity answered 37 to 84 image requests a second here (Apache
  behind nginx 76 to 177 at the same moments), while 32 separate connections get over 200. That is in the engine's
  HTTP/2 path, not in the kernel, and is most of the difference between the two servers.

The measurements of 13 November, before these changes (`MaxParallel=2` at first):

Measured on 13 November 2026 on the test server alpha.se7enx.com: 12 CPU cores, load 7 to 30 from other work during
every run (the numbers are pessimistic for an idle server), SQLite database, a page of 500 image objects (copies of
204 real originals, 800 to 1920 pixels wide), Chromium without cache, medians of three runs, settings as shipped
(`MaxParallel=2`, `MaxWait=2`, `i160` with WebP and AVIF). "Complete" means every image shows something (a
placeholder counts); "real" means every image shows the real image.

| Page | Server | State | First byte | Complete | Real |
|---|---|---|---|---|---|
| 500 x i160 | Apache/PHP-FPM | cold | 0.59 s | 20.7 s | 21.1 s |
| 500 x i160 | Velocity | cold | 1.10 s | 40.6 s | 53.9 s |
| 500 mixed aliases | Apache/PHP-FPM | cold | 0.74 s | 21.1 s | 30.2 s |
| 500 mixed aliases | Velocity | cold | 1.88 s | 35.3 s | 42.4 s |
| 110 x i160 | Apache/PHP-FPM | cold | 0.21 s | 4.52 s | 4.62 s |
| 500 x i160 | Apache/PHP-FPM | warm | 0.40 s | 6.7 s | 6.7 s |
| 500 x i160 | Velocity | warm | 0.40 s | 22.1 s | 22.1 s |
| 500 mixed aliases | Apache/PHP-FPM | warm | 0.28 s | 4.39 s | 4.39 s |
| 500 mixed aliases | Velocity | warm | 0.44 s | 4.47 s | 4.47 s |
| 110 x i160 | Apache/PHP-FPM | warm | 0.37 s | 1.76 s | 1.76 s |

For comparison, in render mode the same 500 missing aliases take 24.8 seconds inside the page request, before the
first byte. **The goal of 3 seconds for a page of 500 new images is not met** on either server, cold or warm. A page
of 110 images meets it warm on Apache/PHP-FPM (1.8 s). A signed hit of an existing alias costs 0.05 ms in process, no
database query and no session; over HTTPS 70 to 80 ms each, mostly TLS and the kernel boot.

What limits it, in order of effect, and what helps:

1. **`MaxParallel`**: 2 conversions at once cap a cold page at about 20 to 35 images per second. Set it to about half
   the CPU cores of a server whose pages show many new images (the test server now runs 6 of 12). Measured
   throughput of `i160` plus WebP: 19 per second with 1 or 2 slots, 33 with 4, 45 with 12. Writing each alias to the
   database (SQLite: one writer at a time) also limits it.
2. **`Formats[]` on thumbnails**: a negotiated alias is a PHP request and a redirect on every view, warm or cold. 500
   negotiated thumbnails warm: 6.7 s (Apache), 22 s (Velocity); with 84 of 500 negotiated: 4.4 s. Put `Formats[]` on
   large aliases only (the test server moved them from `i160` to `i770`, `i1320` and `i1920`).
3. **Pre-generation**: aliases made after publishing are storage URLs, static files, on the first view. On demand is
   the safety net, not the plan for pages of 500 images. Keep `PreGenerate=enabled` and the job queue running; limit
   `PreGenerateAliasList[]` to the aliases the pages really show if publishing produces too much work.
4. **The `index_image.php` rules**: without them `content/image` runs through `index.php` (a full kernel boot per
   image). Install them, and give PHP-FPM enough children: requests queued before PHP starts are outside `MaxWait`,
   so the page waits instead of getting placeholders.
5. **The request shield** must give images a budget of their own (see the recipes); otherwise a page of hundreds of
   new images gets answers 429 and never completes.
6. **The swap script** must be on the page, or placeholders stay until a reload.
7. **Velocity with many images on one HTTP/2 connection** answers about 23 image requests a second when the browser
   multiplexes them on one connection (180 a second over 32 connections); its numbers above suffer from that. This
   is a matter of the Velocity engine, not of the kernel.

## Limits and what was not tested

- **Only the aliases defined in `image.ini`** are made. There are no free sizes (`/content/image/.../300x200`): a
  name that is not configured is refused.
- Converting cannot be interrupted: one slow conversion can make a request take `MaxWait` plus that conversion.
- The swap script gives up after a minute without any image made; an image still not made then stays a
  placeholder until the next page view.
- Aliases made by a converter without size arithmetic (for example the Imagine handler of `cjw_themes_jumper`) have
  no computed width and height.
- Velocity's response cache ignores `Vary`; a negotiated answer that is streamed is therefore sent `private`, and
  only the redirects are cached.
- **Not tested**: a real eZDFS/eZDB cluster (the gateway hand-over was tested with a gateway without metadata on
  eZFS storage; the locks use the handler's `.generating` entries by construction); LiteSpeed and OpenLiteSpeed (no
  server available; the rules are those tested under Apache); FrankenPHP in worker mode; nginx in front of Apache was
  configured and checked on the test server, not measured; a staging site served by PHP-FPM or Velocity (the
  staging tests ran on PHP's built-in server with the kernel's router); the alert mail through SMTP or sendmail
  (file transport only); a site without expqueue (the no-queue paths are unit tested only); the cancellation of a
  running bulk job.
