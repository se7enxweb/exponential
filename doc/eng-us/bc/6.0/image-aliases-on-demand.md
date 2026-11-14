# Upgrading to 6.0.15: image aliases made on demand

Exponential 6.0.15 makes image aliases when the browser asks for them, not while the page renders. This page says what
changes for an existing installation that moves to the 6.0.15 line, what you have to do yourself, and how to go back.

Feature reference: [Image aliases made on demand](../../features/6.0/image-aliases-on-demand.md) · Web server rules:
[Web server recipes for image aliases made on demand](../../examples/web-servers/image-aliases-on-demand.md) · Help:
[Image aliases](../../help/image-aliases.md)

## In short

- **On-demand is the default after the upgrade.** `settings/image.ini [AliasSettings] AliasGeneration=on-demand`:
  a template call computes a missing alias (URL, size) instead of converting it; the alias is made when the browser
  asks for it. Templates need no change, the database no migration.
- **Going back is one line**: `AliasGeneration=render` in `settings/override/image.ini.append.php`, then clear the INI
  cache. It can be switched both ways at any time.
- **Add the rewrite rules** that send a missing storage image and `content/image` to the new entry script
  `index_image.php` (Apache: copy `.htaccess_root` again, or put the four lines into the virtual host; nginx,
  LiteSpeed, Caddy: the recipes). Without them the site works, but slower.
- **Add `index_image.php`** to any allow-list of runnable root scripts, and `AddType image/avif .avif` where the
  server does not know AVIF.
- **Request shield**: give `content/image`, `index_image.php` and storage misses a budget of their own, or pages with
  many new images get answers 429.
- **Themes with their own pagelayout** load `exp_image_swap.js`, or placeholders stay until a reload.
- New: Setup > Image aliases with the policies `setup/aliases` and `setup/managealiases`, the command
  `exp:image:alias`, three job types for expqueue, the log `imagealias.log`, the secret `image_alias_signing`.

## Contents

1. [What changes](#what-changes)
2. [What you do](#what-you-do)
3. [Going back to render mode](#going-back-to-render-mode)
4. [Things to check after the upgrade](#things-to-check-after-the-upgrade)

## What changes

| Area | Before 6.0.15 | From 6.0.15 |
|---|---|---|
| A template asks for a missing alias | converted while the page renders | computed; made when the browser asks (`content/image` or `index_image.php`) |
| The URL of a missing alias | its storage path | `/content/image/<signed token>/<file name>` (`OnDemandURL=token`); an alias that exists keeps its storage URL |
| A missing storage image | 404 from the web server | `index_image.php` makes it (when the path names a configured alias of a published image) or answers a placeholder |
| A slow or failed image | a broken image, or a slow page | a neutral SVG placeholder of the right size; `exp_image_swap.js` swaps in the real image |
| Command line scripts | convert while rendering | unchanged: always render mode |
| Velocity | as PHP-FPM | as PHP-FPM, on demand (its workers follow the setting) |
| Publishing an image | no alias made | the new images' aliases are made in the background when expqueue is installed (`PreGenerate=enabled`) |
| Waiting for another process's alias | without limit | at most `[ImageConverterSettings] LockTimeout` seconds (60), one lock per alias file instead of per original |
| Parallel alias writes of one image | the last writer could drop the others' entries | entries stored meanwhile are merged |
| Removing an object | aliases of other siteaccesses could stay on disk | every alias of the image is removed |

New files and names:

- `index_image.php` in the installation root (the light entry script), the view `content/image` (no policy check, no
  session; in `site.ini [RoleSettings] PolicyOmitList[]` and `[SiteAccessSettings] AnonymousAccessList[]`).
- `design/standard/javascript/exp_image_swap.js`, listed in `design.ini [JavaScriptSettings] FrontendJavaScriptList[]`.
- `settings/expqueue.ini` with the job types `image.alias.generate`, `image.alias.bulk`, `image.alias.resync`.
- **Setup > Image aliases** (`setup/aliases`, right after Cache in the Setup menu), the feed `setup/aliasesfeed`, the
  policies `setup/aliases` and `setup/managealiases`, and **Advanced > Image aliases** in the content tree's context
  menu.
- `./console exp:image:alias` (`bin/php/expimagealias.php`).
- `imagealias.log` in the site's log directory.
- The secret `image_alias_signing` (Setup > Secrets), created on first use; `image_alias_signing_previous` after a
  rotation.
- Cache directories `var/<site>/cache/imagealias-locks`, `imagealias-slots`, `imagealias-staging`.
- `config.cluster.php` may define `CLUSTER_IMAGE_ALIAS_ON_DEMAND` (default on).
- New settings in `image.ini [AliasSettings]` (OnDemandURL, Delivery, MaxWait, DraftTokenLifetime, SigningKeyGrace,
  PreferredConverter, Negotiate, Placeholder, SwapScript, PreGenerate, PreGenerateAliasList, PreGenerateFormats,
  MaxParallel, BulkNice, CacheMaxAge), `[AliasStaging]`, `[AliasSource_pro]`, and `Formats[]` per alias block.

## What you do

1. **Update the code** as usual (`./console exp:upgrade --apply --to=v6.0.15`), then regenerate the autoloads and clear
   the caches; under Velocity, `./console exp:velocity deploy --kernel` (it restarts Velocity, which writes the new
   routes).
2. **Install the rewrite rules** for your web server:
   - Apache with `.htaccess`: save your own lines, then `cp .htaccess_root .htaccess` again.
   - Apache with `AllowOverride None`: add to the virtual host, before the storage passthrough
     `RewriteRule ^var/([^/]+/)?storage/images(-versioned)?/.* - [L]` (in a `<Directory>` block):

     ```apache
     RewriteCond %{REQUEST_FILENAME} !-f
     RewriteRule ^var/([^/]+/)?storage/images(-versioned)?/.* index_image.php [L]
     RewriteRule ^([^/]+/)?content/image/ index_image.php [L]
     RewriteRule ^index_image\.php - [L]
     ```

   - nginx, nginx in front of Apache, LiteSpeed/OpenLiteSpeed, a Caddyfile of your own, a database cluster:
     [the recipes](../../examples/web-servers/image-aliases-on-demand.md).
   - Velocity: nothing beyond the restart.
3. **Allow the script**: add `_image` to an allow-list of root scripts (for example
   `index(_rest|_treemenu|_cluster|_webdav|_soap|_image)?\.php`), as for `index_webdav.php`.
4. **AVIF**: `AddType image/avif .avif` in a virtual host that reads no `.htaccess`; `avif` in the extension list of an
   nginx static location in front of Apache.
5. **Request shield**: take `rules/exponential.rules` of the next `exp_request_shield` release (budget `EXP-IMAGES`), or
   add an image budget to your own rule file now; then `request-shield check` and `test`
   ([how](../../examples/web-servers/image-aliases-on-demand.md#the-request-shields-image-budget)).
6. **Your theme**: if its pagelayout writes its own script tags, load `exp_image_swap.js` (for example
   `{ezscript( array( 'exp_image_swap.js' ) )}` at the bottom of the page). If a template operator builds image URLs
   itself from the alias list, let it take `$handler->imageAlias( $alias )['url']`.
7. **expqueue** (recommended): with the job queue running, publishing makes the new aliases in the background and slow
   visitor misses are finished there. Without it everything is made on demand.
8. **Tune** for pages with many images: `MaxParallel` about half the CPU cores, `Formats[]` only on large aliases,
   `PreGenerateAliasList[]` if publishing makes too much work
   ([Performance and tuning](../../features/6.0/image-aliases-on-demand.md#performance-and-tuning)).
9. Open **Setup > Image aliases** (or `./console exp:image:alias status`) and look at the checks.

## Going back to render mode

```ini
# settings/override/image.ini.append.php
[AliasSettings]
AliasGeneration=render
```

Then `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`, the content view cache, and under Velocity
`./console exp:velocity deploy` (its workers keep the settings of their warm-up). Pages render as before 6.0.15: the
template call converts a missing alias. Aliases made on demand stay and are used; WebP/AVIF variants stay on disk until
their alias is removed. The rewrite rules may stay: `index_image.php` still serves existing files and placeholders.
Switch back by removing the line.

## Things to check after the upgrade

- A page with images that were never shown: every image appears (placeholders first on a slow server), and
  `imagealias.log` has `event=generate` lines.
- A storage path of an alias not made yet answers the image (the rule is in place), an address that names nothing the
  SVG placeholder with 404 and `X-Image-Placeholder: 1` (the commands are in
  [Check it](../../examples/web-servers/image-aliases-on-demand.md#check-it)).
- No `Set-Cookie` on image answers; no 429 from the request shield on a page with many images.
- An `.avif` variant is sent as `image/avif`.
- A root-owned `settings/override/image.ini.append.php` cannot be saved from Setup > Image aliases: give it to the web
  server's user.
