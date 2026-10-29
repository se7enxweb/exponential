---
title: Apache .htaccess
audience: admin, developer
category: examples
order: 20
summary: Die vollständige .htaccess für Apache (und LiteSpeed): die ausgelieferte .htaccess_root Regel für Regel, wie Sie sie installieren und prüfen, und optionale Blöcke für eine HTTPS-Weiterleitung, Sicherheits-Header und Browser-Caching.
keywords: Apache, .htaccess, .htaccess_root, Rewrite-Regeln, Punktdateien, Front-Controller, HTTPS-Weiterleitung, Sicherheits-Header, Browser-Caching, Beispiel, mod_rewrite, rewrite rules, AllowOverride, RewriteRule, dot files, front controller, index.php, index_rest.php, index_treemenu.php, mod_headers, mod_expires, HTTPS redirect, security headers, example
type: example
---
# Apache .htaccess

Der vorgesehene Weg, Exponential mit Apache auszuliefern, ist eine `.htaccess`-Datei im Wurzelverzeichnis der
Installation. Die Datei wird als `.htaccess_root` ausgeliefert; das Beispiel unten,
[`doc/eng-us/examples/htaccess`](../../../eng-us/examples/htaccess), enthält wörtlich dieselben Regeln, mit einem
Kopfkommentar, der sie erklärt, und drei optionalen Blöcken danach. Verwenden Sie es dort, wo Sie den virtuellen Host
nicht bearbeiten können (oder wollen). Mit Zugriff auf den virtuellen Host setzt das Beispiel
[Apache, virtueller Host](apache-virtueller-host.md) dieselben Regeln in den Serverkontext und erspart Apache bei jeder
Anfrage eine Dateisuche pro Verzeichnis.

## Installieren

```bash
cd /var/www/example.com          # the installation root, where index.php is
cp .htaccess_root .htaccess      # copy, do not move: an update can bring a new version
apachectl configtest             # Syntax OK
systemctl reload httpd           # apache2 on Debian and Ubuntu
```

Apache braucht `mod_rewrite` (Debian: `a2enmod rewrite`), und der virtuelle Host muss ihm erlauben, die Datei zu lesen:

```apache
<Directory /var/www/example.com>
    Options FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

Für die Rewrite-Regeln genügt `AllowOverride FileInfo`. Der optionale Header-Block braucht ebenfalls `FileInfo`, der
optionale Caching-Block `Indexes`; ist keines davon erlaubt, beantwortet Apache jede Anfrage mit 500, und sein
Fehlerprotokoll nennt die Zeile.

Um die optionalen Blöcke zu nutzen, kopieren Sie `doc/eng-us/examples/htaccess` statt `.htaccess_root` und aktivieren
Sie, was Sie brauchen. Vergleichen Sie nach jedem Upgrade Ihre `.htaccess` mit der neuen `.htaccess_root` und übernehmen
Sie neue Regeln.

## Die Datei

Die ersten Versionen dieser Datei kamen mit eZ Publish, der Software, aus der Exponential hervorgegangen ist
(1999 - 2014). Ihre Rewrite-Regeln sind die von `.htaccess_root` und gehören zur Software (GNU GPL Version 2 oder
später); das dokumentierte Beispiel um sie herum ist freie Dokumentation (GFDL 1.3 oder später), wie diese Seite.

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
# The SOAP server (settings/soap.ini EnableSOAP=true) is index_soap.php since
# Exponential 6.0.15; soap.php, its old name, only forwards to it. Uncomment
# when SOAP is switched on, and let the SOAP clients call index_soap.php:
#RewriteRule ^index_soap\.php - [L]
RewriteRule ^([^/]+/)?content/treemenu.* index_treemenu.php [L]
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

## Die Regeln im Einzelnen

| Regel | Was sie tut | Warum |
|---|---|---|
| `DirectoryIndex index.php` | `/` führt `index.php` aus | die Startseite |
| `RewriteRule (^\|/)\.(?!well-known(/\|$)) - [R=404,L]` | jeder Pfad mit einem Segment, das mit einem Punkt beginnt, beantwortet 404, außer `.well-known` | `.git/config`, `.env`, der `.cache` eines Pakets, die eigene `.htaccess` einer Erweiterung |
| `RewriteCond %{REQUEST_FILENAME} -f` + `RewriteRule ^.+/[^/]+\.(php[0-9]?\|phtml\|phar)$ - [R=404,L]` | ein vorhandenes Skript unterhalb des Wurzelverzeichnisses beantwortet 404 | keine Bibliothek, kein Installer, keine Paketeinstellung und kein verirrtes Skript wird ausgeführt, nur weil sein Pfad angefragt wurde; nur die Front-Controller im Wurzelverzeichnis laufen |
| `RewriteRule ^api/ index_rest.php [L]`, `^index_rest\.php - [L]` | die REST-API | `index_rest.php` ist der REST-Front-Controller |
| `#RewriteRule ^index_soap\.php - [L]` (auskommentiert) | der SOAP-Server, wenn `soap.ini [GeneralSettings] EnableSOAP=true` gilt | `index_soap.php` ist seit 6.0.15 der SOAP-Front-Controller; `soap.php` leitet nur dorthin weiter. Nehmen Sie mit SOAP an das Kommentarzeichen weg, sonst enden SOAP-Anfragen bei `index.php`; siehe [Das SOAP-Einstiegsskript heißt jetzt index_soap.php](../../bc/6.0/soap-einstiegsskript-index-soap.md) |
| `RewriteRule ^([^/]+/)?content/treemenu.* index_treemenu.php [L]` | das Baummenü der Administration, mit oder ohne vorangestellten Siteaccess | ein schlanker Front-Controller für das JSON des Baums |
| `^var/([^/]+/)?storage/images(-versioned)?/.*` | gespeicherte Bildvarianten werden als Datei gesendet | Bilder in Inhalten |
| der Block `<FilesMatch "(?i)\.svgz?$">` | ein SVG wird mit `Content-Security-Policy: sandbox ...` gesendet | ein direkt geöffnetes SVG führt nichts aus und lädt nichts |
| `^var/([^/]+/)?storage/pdf/...\.pdf$` | gespeicherte PDF-Exporte | Setup > PDF-Export |
| `^var/([^/]+/)?storage/original/image/...` | nur die Bild-Originale | andere Uploads liefert `content/download` aus, das die Berechtigungen prüft |
| `^var/([^/]+/)?cache/(texttoimage\|public)/.*` | die öffentlichen Caches (gepackte Skripte und Styles) | der Packer von `ezjscore` schreibt dorthin |
| `^var_cache/...` (auskommentiert) | die öffentlichen Caches in einem eigenen Baum | nur mit `site.ini [FileSettings] CacheVarDir=var_cache` |
| `^design/...`, `^share/icons/.*`, `^extension/[^/]+/design/...` | Stylesheets, Bilder, Skripte und Schriften der Designs | nur die jeweils gelisteten Verzeichnisarten; bei einer Erweiterung auch `vendor` und `media` |
| `^extension/[^/]+/icons/[A-Za-z0-9_/-]+\.(svg\|png\|gif)$` | die Icon-Themes der Erweiterungen (`icon.ini [ExtensionSettings] IconExtensions[]`) | nur SVG-, PNG- und GIF-Bilder mit genau einer Dateiendung: ein Name wie `a.php.svg` könnte sonst über ein `AddHandler`-Mapping an PHP gehen |
| `^packages/styles/...`, `^var/storage/packages/.+\.(png\|jpe?g\|gif\|webp)$` | Paket-Styles und Vorschaubilder | XML, SQL und Einstellungen eines Pakets sind nicht fürs Web bestimmt; SVG ist nicht gelistet, es kann Skript enthalten |
| `favicon.ico`, `robots.txt`, `index.js`, `sw.js`, `w3c/p3p.xml` | aus dem Wurzelverzeichnis gesendet | der Service Worker muss aus dem Wurzelverzeichnis ausgeliefert werden |
| `RewriteRule .* index.php` | alles andere führt `index.php` aus | URL-Aliase und Module; auch `settings/site.ini`, `composer.json`, `var/log/...`, die `index.php` mit einer Seite oder einem 404 beantwortet, niemals mit der Datei |

`var/`, `settings/`, `vendor/`, `.git`, `composer.*` und `.env` brauchen also keine eigenen Regeln: Die Punktregel und
„nicht auf der Liste, also `index.php`“ decken sie ab.

Zwei Details des Verzeichniskontexts (`.htaccess`) sollten Sie kennen:

- Nach einem Rewrite durchläuft Apache die Regeln erneut mit dem neuen Pfad. `index_rest.php` hat für diese zweite
  Runde eine Durchlassregel (`^index_rest\.php - [L]`); `index_treemenu.php` hat keine, daher endet die Anfrage für das
  Baummenü unter `.htaccess` bei `index.php`, das das Baummenü ebenfalls ausliefert, nur etwas langsamer. Im
  Serverkontext (dem Beispiel für den virtuellen Host) beendet `[L]` die Verarbeitung, und `index_treemenu.php`
  antwortet.
- `.well-known` ist von der Punktregel ausgenommen, steht aber nicht auf der Asset-Liste, daher erreicht
  `/.well-known/...` `index.php`. Ein Zertifikats-Client, der dort Challenge-Dateien ablegt (`certbot --webroot`),
  braucht einen Host für reines HTTP, der das Verzeichnis ausliefert, wie es der Host für Port 80 im
  [Beispiel für den virtuellen Host](apache-virtueller-host.md) tut.

## Die optionalen Blöcke

1. **HTTP zu HTTPS.** Besser im virtuellen Host für Port 80 erledigt. Wenn Sie nur `.htaccess` haben, verschieben Sie
   die zwei Zeilen direkt unter `RewriteEngine On`. Hinter einem Proxy, der TLS terminiert, ist `%{HTTPS}` immer aus:
   Prüfen Sie stattdessen `X-Forwarded-Proto`, und zwar nur, wenn der Proxy den Header immer setzt, sonst läuft jede
   Anfrage in eine Schleife.
2. **Sicherheits-Header für statische Dateien.** Der Kernel sendet `X-Content-Type-Options`, `X-Frame-Options`,
   `Referrer-Policy` und `Permissions-Policy` auf seinen Seiten (`site.ini [HTTPHeaderSettings] SecurityHeaders[]`);
   Apache sendet bei Dateien, die er selbst ausliefert, nichts davon. Der Block ergänzt dieselben Werte mit
   `setifempty`, sodass eine Seite die des Kernels behält. HSTS bleibt auskommentiert: Setzen Sie es, sobald jeder Name
   über HTTPS ausgeliefert wird, hier oder in `site.ini`, nicht an beiden Stellen.
3. **Browser-Caching.** Die URLs gespeicherter Bilder ändern sich, wenn sich das Bild ändert, daher ist ein Jahr
   unbedenklich. Design-Dateien behalten ihre Namen: Halten Sie ihre Lebensdauer kurz, solange sich das Design ändert.

## Prüfen

```bash
for p in /.git/config /.env /kernel/classes/ezini.php /extension/ezjscore/ezinfo.php; do
  printf '%-34s %s\n' "$p" "$(curl -s -o /dev/null -w '%{http_code}' https://example.com$p)"     # 404 each
done
curl -s https://example.com/settings/site.ini | grep -c '^\[DatabaseSettings\]'   # 0: a page, not the file
curl -sI https://example.com/design/admin4/stylesheets/components.css | head -1   # 200
```

Ein `200` für `.git/config` bedeutet, dass die Regeln nicht aktiv sind: `AllowOverride` lässt sie nicht zu,
`mod_rewrite` ist aus, oder das Document Root ist nicht das Wurzelverzeichnis der Installation. Halten Sie an und
beheben Sie das, bevor die Site von außen erreichbar ist.

Wie dieses Beispiel geprüft wurde: `httpd -t` mit der Datei innerhalb eines `<Directory>`-Blocks, und ein Wegwerf-Apache
2.4, der sie als `.htaccess` für ein Test-Document-Root verwendete und jede Anfrage der
[Testliste](README.md#welche-konfiguration) wie erwartet beantwortete, einschließlich der Header des optionalen Blocks.

## Verwandte Seiten

- [Apache, virtueller Host](apache-virtueller-host.md): dieselben Regeln im Serverkontext, mit HTTPS und PHP-FPM.
- [Webserver-Beispiele](README.md) und [alle Beispiele](../README.md).
- [Kapitel 8, Abschnitt 8.5: Apache mit PHP-FPM](../../installation/08-die-website-ausliefern.md#85-apache-mit-php-fpm).
- [Kapitel 13, Abschnitt 13.2: Was der Webserver niemals ausliefern darf](../../installation/13-sicherheitshaertung.md#132-was-der-webserver-niemals-ausliefern-darf).
- [LiteSpeed](litespeed.md): LiteSpeed Enterprise liest diese Datei wie Apache.
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite darf frei kopiert, übersetzt und verbessert werden.
