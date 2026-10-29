---
title: Apache .htaccess
audience: admin, developer
category: examples
order: 20
summary: The complete .htaccess for Apache (and LiteSpeed): the shipped .htaccess_root rule by rule, how to install and check it, and optional blocks for an HTTPS redirect, security headers and browser caching.
keywords: Apache, .htaccess, .htaccess_root, mod_rewrite, rewrite rules, AllowOverride, RewriteRule, dot files, front controller, index.php, index_rest.php, index_treemenu.php, mod_headers, mod_expires, HTTPS redirect, security headers, example
type: example
---
# Apache .htaccess

The shipped way to serve Exponential with Apache is a `.htaccess` file in the installation root. The file ships as
`.htaccess_root`; the example below, [`doc/eng-us/examples/htaccess`](../htaccess), is the same rules verbatim, with a header
that explains them and three optional blocks after them. Use it where you cannot (or do not want to) edit the virtual
host. With access to the virtual host, the [Apache virtual host](apache-virtual-host.md) example puts the same rules in
server context and saves Apache a file lookup per directory on every request.

## Install it

```bash
cd /var/www/example.com          # the installation root, where index.php is
cp .htaccess_root .htaccess      # copy, do not move: an update can bring a new version
apachectl configtest             # Syntax OK
systemctl reload httpd           # apache2 on Debian and Ubuntu
```

Apache needs `mod_rewrite` (Debian: `a2enmod rewrite`), and the virtual host must let it read the file:

```apache
<Directory /var/www/example.com>
    Options FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

`AllowOverride FileInfo` is enough for the rewrite rules. The optional header block needs `FileInfo` too, the optional
caching block `Indexes`; with neither allowed, Apache answers 500 for every request, and its error log names the line.

To use the optional blocks, copy `doc/eng-us/examples/htaccess` instead of `.htaccess_root` and enable what you need. After
every upgrade, compare your `.htaccess` with the new `.htaccess_root` and take over new rules.

## The file

The first versions of this file came with eZ Publish, the software Exponential grew out of (1999 - 2014). Its rewrite
rules are those of `.htaccess_root`, part of the software (GNU GPL version 2 or later); the documented example around
them is free documentation (GFDL 1.3 or later), like this page.

```apache
# Apache .htaccess for Exponential 6
# ---------------------------------------------------------------------------
# What this is: the rewrite rules that make Apache serve an Exponential
# installation safely when the document root is the installation root (the
# directory with index.php). Only a fixed list of asset paths is sent as a
# file; every other request (URL aliases, modules, and also settings/, var/,
# vendor/, kernel/, composer.json) is answered by index.php, which never hands
# out the file. Dot files (.git, .env, .htaccess) answer 404 anywhere, and no
# .php below the root runs because its path was asked for.
#
# How to install it: the shipped file is .htaccess_root in the installation
# root. Copy it, do not move it, so an update can bring a new version:
#
#     cp .htaccess_root .htaccess
#
# The section "Rules" below is a verbatim copy of .htaccess_root of this
# release, for reading and for copying into a <Directory> block. The section
# "Optional" after it is not in .htaccess_root: HTTPS redirect, headers on
# static files and browser caching, each switched off or guarded until you
# turn it on. Never delete a rule of the "Rules" section to make something
# work; find the cause instead (doc/eng-us/install/13-security-hardening.md, 13.2).
#
# Needs: mod_rewrite, and AllowOverride All (or at least FileInfo) for the
# directory. Check with "apachectl configtest", then these requests:
#   /.git/config, /.env, /kernel/classes/ezini.php  -> 404
#   /settings/site.ini, /composer.json               -> a page from index.php, never the file
#
# Explained rule by rule: doc/eng-us/examples/web-servers/apache-htaccess.md
# The same rules for a virtual host (server context): doc/eng-us/examples/ezpublish.conf
#
# License: the first versions of this file came with the software Exponential
# grew out of, eZ Publish, 1999 - 2014. The rewrite rules are those of
# .htaccess_root, part of the software (GNU General Public License version 2
# or later, like the rest of the code). This documented example, its comments
# and optional sections, is free documentation under the GNU Free
# Documentation License 1.3 or later (doc/eng-us/README-LICENSE.md).

# ===========================================================================
# Rules (verbatim .htaccess_root)
# ===========================================================================

# Copy this file to a new file called .htaccess in your Exponential root
# to secure your installation by turning on .htaccess based virtual host mode.

DirectoryIndex index.php

RewriteEngine On
# No dot file or dot directory (.git, .env, .htaccess ...) is served or
# run, whatever folder it lies in; .well-known stays reachable.
RewriteRule (^|/)\.(?!well-known(/|$)) - [R=404,L]
# No script runs because its path was asked for, except the front
# controllers at the root: a .php below them (in the package store,
# an extension, var/) answers 404 instead of being executed.
RewriteCond %{REQUEST_FILENAME} -f
RewriteRule ^.+/[^/]+\.(php[0-9]?|phtml|phar)$ - [R=404,L]
RewriteRule ^api/ index_rest.php [L]
RewriteRule ^index_rest\.php - [L]
RewriteRule ^([^/]+/)?content/treemenu.* index_treemenu.php [L]
# WebDAV (settings/webdav.ini [GeneralSettings] EnableWebDAV=true): the server is
# index_webdav.php, reached as https://www.example.com/index_webdav.php/<siteaccess>/.
# Uncomment to let it answer on this host, or give it a host of its own that sends
# every request to index_webdav.php. webdav.php, its old name, only forwards to it
# and will be removed in a later major version: name index_webdav.php in new rules.
#RewriteRule ^index_webdav\.php - [L]
RewriteRule ^var/([^/]+/)?storage/images(-versioned)?/.* - [L]
# SVG images are sanitized on upload; served from the storage they also run
# nothing and load nothing when opened directly (needs mod_headers).
<IfModule mod_headers.c>
    <FilesMatch "(?i)\.svgz?$">
        Header always set Content-Security-Policy "sandbox; default-src 'none'; style-src 'unsafe-inline'"
        Header always set X-Content-Type-Options "nosniff"
    </FilesMatch>
</IfModule>
# The stored files of PDF exports generated once (Setup > PDF export): only
# names of letters, digits, ".", "_" and "-" ending in .pdf, as the export writes them.
RewriteRule ^var/([^/]+/)?storage/pdf/[A-Za-z0-9][A-Za-z0-9._-]*\.pdf$ - [L]
# Of the originals only the images (SVG images are shown from there);
# other uploads stay behind content/download and its permissions.
RewriteRule ^var/([^/]+/)?storage/original/image/.+\.(png|jpe?g|gif|webp|svg)$ - [L]
RewriteRule ^var/([^/]+/)?cache/(texttoimage|public)/.* - [L]
# With site.ini [FileSettings] CacheVarDir=var_cache (the caches of the sites in a
# tree of their own, multi-site hosting) their public caches are served from there:
#RewriteRule ^var_cache/([^/]+/)?cache/(texttoimage|public)/.* - [L]
RewriteRule ^design/[^/]+/(stylesheets|images|javascript|fonts)/.* - [L]
RewriteRule ^share/icons/.* - [L]
# Icon themes shipped by extensions (icon.ini [ExtensionSettings] IconExtensions[],
# extension/<name>/icons/). Only images with exactly one extension: a name like
# a.php.svg could otherwise be handed to PHP by an AddHandler mapping.
RewriteRule ^extension/[^/]+/icons/[A-Za-z0-9_/-]+\.(svg|png|gif)$ - [L]
RewriteRule ^extension/[^/]+/design/[^/]+/(stylesheets|flash|images|lib|javascripts?|fonts|vendor|media)/.* - [L]
RewriteRule ^packages/styles/.+/(stylesheets|images|javascript)/[^/]+/.* - [L]
RewriteRule ^packages/styles/.+/thumbnail/.* - [L]
# Only the preview images of imported packages: their XML, SQL and
# settings are not for the web.
RewriteRule ^var/storage/packages/.+\.(png|jpe?g|gif|webp)$ - [L]

#  Makes it possible to placed your favicon at the root of your
#  Exponential instance. It will then be served directly.
RewriteRule ^favicon\.ico - [L]
#  Uncomment the line below if you want you favicon be served from the standard design.
#  You can customize the path to favicon.ico by replacing design/standard/images/favicon.ico
#  by the adequate path.
#RewriteRule ^favicon\.ico /design/standard/images/favicon.ico [L]
RewriteRule ^design/standard/images/favicon\.ico - [L]

# Give direct access to robots.txt for use by crawlers (Google, Bing, Spammers..)
RewriteRule ^robots\.txt - [L]

# The site's service workers, which have to be served from the root: index.js, the
# Exponential Service Workers Index, and sw.js, its old name, kept for browsers registered before.
RewriteRule ^index\.js$ - [L]
RewriteRule ^sw\.js$ - [L]

# Uncomment the following lines when using popup style debug.
# RewriteRule ^var/cache/debug\.html.* - [L]
# RewriteRule ^var/[^/]+/cache/debug\.html.* - [L]

# Platform for Privacy Preferences Project ( P3P ) related files for Internet Explorer
# More info here : http://en.wikipedia.org/wiki/P3p
RewriteRule ^w3c/p3p\.xml - [L]

RewriteRule .* index.php

# ===========================================================================
# Optional (not in .htaccess_root). Read each block before enabling it.
# ===========================================================================
#
# Nothing below changes which paths are served or routed. Blocks 2 and 3 only
# add response headers; block 1 is a redirect and is the only one that must
# move (see its comment). mod_headers needs AllowOverride FileInfo, mod_expires
# AllowOverride Indexes; without them Apache answers 500.

# --- 1. Redirect plain HTTP to HTTPS -----------------------------------------
# Off by default. Better done in the port 80 virtual host
# (doc/eng-us/examples/ezpublish.conf). If you can only use .htaccess, move these two
# lines directly below "RewriteEngine On" above and remove the "#".
# Behind a proxy or load balancer that terminates TLS, %{HTTPS} is always
# "off" here: test X-Forwarded-Proto instead, and only if the proxy always
# sets it (else every request loops):
#   RewriteCond %{HTTP:X-Forwarded-Proto} !https
#RewriteCond %{HTTPS} off
#RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]

# --- 2. Security headers on static files ------------------------------------
# The kernel sends these on its own pages (site.ini [HTTPHeaderSettings]
# SecurityHeaders[]); Apache sends nothing extra on files it serves itself.
# These are the same values as the kernel's defaults. "setifempty" keeps a
# header that index.php already sent.
<IfModule mod_headers.c>
    Header setifempty X-Content-Type-Options "nosniff"
    Header setifempty X-Frame-Options "SAMEORIGIN"
    Header setifempty Referrer-Policy "strict-origin-when-cross-origin"
    Header setifempty Permissions-Policy "camera=(), microphone=(), payment=(), usb=()"
    # HSTS: only once every name of the site is served over HTTPS for good.
    # Start short (300) and raise it to a year later; a browser remembers it
    # for the whole period (doc/eng-us/install/13-security-hardening.md, 13.9).
    # Set it here or in site.ini SecurityHeaders[Strict-Transport-Security],
    # not in both places.
    #Header always set Strict-Transport-Security "max-age=300" "expr=%{HTTPS} == 'on'"
</IfModule>

# --- 3. Browser caching of images and design files ---------------------------
# Image URLs change when the image changes (the version is in the path), so
# they can be cached for a long time. Design files keep their names: keep
# their lifetime short, or use the packer's file names with the modification
# time (ezjscore.ini [Packer] AppendLastModifiedTime=enabled).
<IfModule mod_expires.c>
    <If "%{REQUEST_URI} =~ m#^/var/([^/]+/)?storage/images(-versioned)?/#">
        ExpiresActive On
        ExpiresDefault "access plus 1 year"
    </If>
    <ElseIf "%{REQUEST_URI} =~ m#^/(extension/[^/]+/)?design/[^/]+/(stylesheets|images|javascripts?|fonts|lib)/#">
        ExpiresActive On
        ExpiresDefault "access plus 7 days"
    </ElseIf>
    <ElseIf "%{REQUEST_URI} =~ m#^/(share|extension/[^/]+)/icons/#">
        ExpiresActive On
        ExpiresDefault "access plus 7 days"
    </ElseIf>
</IfModule>
```

## The rules, one by one

| Rule | What it does | Why |
|---|---|---|
| `DirectoryIndex index.php` | `/` runs `index.php` | the front page |
| `RewriteRule (^\|/)\.(?!well-known(/\|$)) - [R=404,L]` | any path with a segment that starts with a dot answers 404, except `.well-known` | `.git/config`, `.env`, a package's `.cache`, an extension's own `.htaccess` |
| `RewriteCond %{REQUEST_FILENAME} -f` + `RewriteRule ^.+/[^/]+\.(php[0-9]?\|phtml\|phar)$ - [R=404,L]` | an existing script below the root answers 404 | no library, installer, package setting or stray script runs because its path was asked for; only the front controllers at the root run |
| `RewriteRule ^api/ index_rest.php [L]`, `^index_rest\.php - [L]` | the REST API | `index_rest.php` is the REST front controller |
| `RewriteRule ^([^/]+/)?content/treemenu.* index_treemenu.php [L]` | the administration's tree menu, with or without a siteaccess in front | a light front controller for the tree's JSON |
| `#RewriteRule ^index_webdav\.php - [L]` (commented) | WebDAV on this host, once uncommented | `index_webdav.php` is the WebDAV server since 6.0.15; `webdav.php` only forwards to it ([what to change](../../bc/6.0/webdav-entry-script-index-webdav.md)) |
| `^var/([^/]+/)?storage/images(-versioned)?/.*` | stored image variations are sent as files | images in content |
| the `<FilesMatch "(?i)\.svgz?$">` block | an SVG is sent with `Content-Security-Policy: sandbox ...` | an SVG opened directly runs nothing and loads nothing |
| `^var/([^/]+/)?storage/pdf/...\.pdf$` | stored PDF exports | Setup > PDF export |
| `^var/([^/]+/)?storage/original/image/...` | only image originals | other uploads are delivered by `content/download`, which checks permissions |
| `^var/([^/]+/)?cache/(texttoimage\|public)/.*` | the public caches (packed scripts and styles) | `ezjscore`'s packer writes there |
| `^var_cache/...` (commented) | the public caches in a tree of their own | only with `site.ini [FileSettings] CacheVarDir=var_cache` |
| `^design/...`, `^share/icons/.*`, `^extension/[^/]+/design/...` | stylesheets, images, scripts, fonts of the designs | each listed directory type only; an extension's `vendor` and `media` too |
| `^extension/[^/]+/icons/[A-Za-z0-9_/-]+\.(svg\|png\|gif)$` | the icon themes of extensions (`icon.ini [ExtensionSettings] IconExtensions[]`) | SVG, PNG and GIF images with exactly one extension only: a name like `a.php.svg` could otherwise be handed to PHP by an `AddHandler` mapping |
| `^packages/styles/...`, `^var/storage/packages/.+\.(png\|jpe?g\|gif\|webp)$` | package styles and preview images | a package's XML, SQL and settings are not for the web; SVG is not listed, it can carry script |
| `favicon.ico`, `robots.txt`, `index.js`, `sw.js`, `w3c/p3p.xml` | sent from the root | the service worker must be served from the root |
| `RewriteRule .* index.php` | everything else runs `index.php` | URL aliases and modules; also `settings/site.ini`, `composer.json`, `var/log/...`, which `index.php` answers with a page or a 404, never the file |

So `var/`, `settings/`, `vendor/`, `.git`, `composer.*` and `.env` need no rules of their own: the dot rule and "not on
the list, so `index.php`" cover them.

Two details of directory context (`.htaccess`) are worth knowing:

- After a rewrite, Apache runs the rules again with the new path. `index_rest.php` has a pass-through rule
  (`^index_rest\.php - [L]`) for that second round; `index_treemenu.php` has none, so under `.htaccess` the tree menu
  request ends at `index.php`, which serves the tree menu too, a little more slowly. In server context (the virtual
  host example) `[L]` ends the processing and `index_treemenu.php` answers.
- `.well-known` is exempt from the dot rule but not on the asset list, so `/.well-known/...` reaches `index.php`. A
  certificate client that writes challenge files there (`certbot --webroot`) needs a plain-HTTP host that serves the
  directory, as the port 80 host of the [virtual host example](apache-virtual-host.md) does.

## The optional blocks

1. **HTTP to HTTPS.** Better done in the port 80 virtual host. With `.htaccess` only, move the two lines directly below
   `RewriteEngine On`. Behind a proxy that terminates TLS, `%{HTTPS}` is always off: test `X-Forwarded-Proto` instead,
   and only when the proxy always sets it, or every request loops.
2. **Security headers on static files.** The kernel sends `X-Content-Type-Options`, `X-Frame-Options`,
   `Referrer-Policy` and `Permissions-Policy` on its pages (`site.ini [HTTPHeaderSettings] SecurityHeaders[]`); Apache
   sends nothing on files it serves itself. The block adds the same values with `setifempty`, so a page keeps the
   kernel's own. HSTS stays commented: set it once every name is served over HTTPS, here or in `site.ini`, not both.
3. **Browser caching.** Stored image URLs change when the image changes, so a year is safe. Design files keep their
   names: keep their lifetime short while the design changes.

## Check it

```bash
for p in /.git/config /.env /kernel/classes/ezini.php /extension/ezjscore/ezinfo.php; do
  printf '%-34s %s\n' "$p" "$(curl -s -o /dev/null -w '%{http_code}' https://example.com$p)"     # 404 each
done
curl -s https://example.com/settings/site.ini | grep -c '^\[DatabaseSettings\]'   # 0: a page, not the file
curl -sI https://example.com/design/admin4/stylesheets/components.css | head -1   # 200
```

A `200` for `.git/config` means the rules are not active: `AllowOverride` does not allow them, `mod_rewrite` is off,
or the document root is not the installation root. Stop and fix that before the site is reachable from outside.

How this example was checked: `httpd -t` with the file inside a `<Directory>` block, and a throwaway Apache 2.4 that
used it as `.htaccess` for a test document root and answered every request of the
[test list](README.md#which-one) as expected, including the headers of the optional block.

## Related pages

- [Apache virtual host](apache-virtual-host.md): the same rules in server context, with HTTPS and PHP-FPM.
- [Web server examples](README.md) and [all examples](../README.md).
- [Chapter 8, section 8.5: Apache with PHP-FPM](../../install/08-serving-the-site.md#85-apache-with-php-fpm).
- [Chapter 13, section 13.2: What the web server must never hand out](../../install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out).
- [LiteSpeed](litespeed.md): LiteSpeed Enterprise reads this file as Apache does.
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
