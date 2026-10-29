---
title: Apache, virtueller Host
audience: admin, developer
category: examples
order: 30
summary: Ein vollständiger virtueller Host für Apache 2.4 und Exponential 6 mit einer Weiterleitung von HTTP zu HTTPS, HTTP/2, PHP-FPM über mod_proxy_fcgi, den Regeln von .htaccess_root im Serverkontext, Sicherheits-Headern und Browser-Caching.
keywords: Apache, virtueller Host, Serverkontext, Sicherheits-Header, Browser-Caching, Beispiel, virtual host, VirtualHost, ezpublish.conf, server context, mod_rewrite, mod_proxy_fcgi, PHP-FPM, HTTPS, mod_ssl, HTTP/2, Protocols h2, Let's Encrypt, certbot, AllowOverride None, security headers, example
type: example
---
# Apache, virtueller Host

[`doc/eng-us/examples/ezpublish.conf`](../../../eng-us/examples/ezpublish.conf) ist ein vollständiger virtueller Host
für eine Installation: Port 80 leitet auf HTTPS weiter (und liefert Challenge-Dateien für Zertifikate aus), Port 443
liefert die Site über HTTP/2 mit PHP-FPM aus, und die Routing-Regeln von `.htaccess_root` stehen im virtuellen Host
selbst. Apache liest dann überhaupt keine `.htaccess` (`AllowOverride None`), was bei jeder Anfrage eine Dateisuche pro
Verzeichnis spart. Die Datei behält ihren historischen Namen.

## Installieren

1. Laden Sie die Module `rewrite`, `proxy`, `proxy_fcgi`, `ssl`, `headers`, `expires` und für HTTP/2 `http2`
   (Debian: `a2enmod rewrite proxy_fcgi ssl http2 headers expires`; Systeme im Red-Hat-Stil laden die meisten davon
   standardmäßig und liefern `mod_http2` und `mod_ssl` als eigene Pakete aus).
2. Kopieren Sie die Datei nach `/etc/apache2/sites-available/example.com.conf` (Debian, danach `a2ensite example.com`)
   oder nach `/etc/httpd/conf.d/example.com.conf` (Red-Hat-Stil) und ersetzen Sie `example.com`,
   `/var/www/example.com`, den PHP-FPM-Socket und die Zertifikatspfade.
3. Legen Sie den PHP-FPM-Pool an, zu dem der Socket gehört ([PHP-FPM-Pool](php-fpm.md)).
4. Besorgen Sie das Zertifikat. Mit certbot, bevor der HTTPS-Host existiert: `certbot certonly --webroot -w /var/www/example.com
   -d example.com -d www.example.com`; genau dafür liefert der Host für Port 80 `/.well-known/acme-challenge/` aus.
5. Prüfen und neu laden:

   ```bash
   apachectl configtest           # Syntax OK
   systemctl reload apache2       # httpd on Red Hat style systems
   curl -sI https://example.com/ | head -1
   ```

Sie verwenden ein Hosting-Panel wie Plesk? Es schreibt den virtuellen Host selbst: Bearbeiten Sie seine Dateien nicht,
bleiben Sie stattdessen bei `.htaccess` ([Apache .htaccess](apache-htaccess.md)) und lesen Sie
[Kapitel 8, Abschnitt 8.5.3](../../installation/08-die-website-ausliefern.md#853-plesk).

## Die Datei

Die ersten Versionen dieser Datei kamen mit eZ Publish, der Software, aus der Exponential hervorgegangen ist
(1999 - 2014). Ihre Rewrite-Regeln sind die von `.htaccess_root` und gehören zur Software (GNU GPL Version 2 oder
später); das dokumentierte Beispiel um sie herum ist freie Dokumentation (GFDL 1.3 oder später), wie diese Seite.

```apache
# Apache virtual host for Exponential 6 (PHP-FPM, HTTPS, HTTP/2)
# ---------------------------------------------------------------------------
# What this is: a complete virtual host for one Exponential installation,
# with the routing rules of .htaccess_root written in server context, so
# Apache needs no .htaccess (AllowOverride None saves a file lookup per
# directory on every request). The rules are the ones of .htaccess_root of
# this release: compare them with .htaccess_root after every upgrade and
# merge new rules. Explained in doc/eng-us/examples/web-servers/apache-virtual-host.md.
#
# Replace before use:
#   example.com, www.example.com      the names of the site
#   /var/www/example.com              the installation root (where index.php is)
#   /run/php-fpm/www.sock             the socket of this site's PHP-FPM pool
#   /etc/ssl/example.com/*.pem        the certificate and its key
#
# Needs the modules: rewrite, proxy, proxy_fcgi, ssl, http2, headers,
# expires (Debian: a2enmod rewrite proxy_fcgi ssl http2 headers expires).
# Check with "apachectl configtest", reload with "systemctl reload apache2"
# (httpd on Red Hat style systems).
#
# In server context (outside <Directory>) a RewriteRule pattern is matched
# against the URL path with its leading slash, and %{REQUEST_FILENAME} is not
# yet a file name: that is why every pattern below starts with "^/" and why
# the script rule tests %{DOCUMENT_ROOT}%{REQUEST_URI}.
#
# License: the first versions of this file came with the software Exponential
# grew out of, eZ Publish, 1999 - 2014. The rewrite rules are those of
# .htaccess_root, part of the software (GNU General Public License version 2
# or later, like the rest of the code). This documented example, its comments
# and optional sections, is free documentation under the GNU Free
# Documentation License 1.3 or later (doc/eng-us/README-LICENSE.md).

# Plain HTTP: only the redirect to HTTPS, and the ACME challenge files a
# certificate client (certbot --webroot) places below .well-known.
<VirtualHost *:80>
    ServerName example.com
    ServerAlias www.example.com
    DocumentRoot /var/www/example.com

    <Directory /var/www/example.com>
        Options None
        AllowOverride None
        Require all granted
    </Directory>

    RewriteEngine On
    RewriteCond %{REQUEST_URI} !^/\.well-known/acme-challenge/[A-Za-z0-9_-]+$
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]

    ErrorLog logs/example.com-error.log
    CustomLog logs/example.com-access.log combined
</VirtualHost>

<VirtualHost *:443>
    ServerName example.com
    ServerAlias www.example.com

    # The installation root, where index.php is
    DocumentRoot /var/www/example.com
    DirectoryIndex index.php

    Protocols h2 http/1.1
    SSLEngine on
    SSLCertificateFile    /etc/ssl/example.com/fullchain.pem
    SSLCertificateKeyFile /etc/ssl/example.com/privkey.pem

    ErrorLog logs/example.com-ssl-error.log
    CustomLog logs/example.com-ssl-access.log combined

    # Uploads: Apache's own limit (0 = none); PHP's upload_max_filesize and
    # post_max_size in the pool decide (doc/eng-us/examples/web-servers/php-fpm-pool.conf)
    LimitRequestBody 0

    <Directory /var/www/example.com>
        Options FollowSymLinks
        # No .htaccess is read: the rules are below
        AllowOverride None
        Require all granted
    </Directory>

    # Every PHP script goes to the PHP-FPM pool of this site. Only the front
    # controllers ever get here: the rules below send nothing else to a .php.
    <FilesMatch "\.php$">
        SetHandler "proxy:unix:/run/php-fpm/www.sock|fcgi://localhost"
    </FilesMatch>

    # The rules of .htaccess_root, in its order, in server context
    <IfModule mod_rewrite.c>
        RewriteEngine On
        # No dot file or dot directory (.git, .env, .htaccess ...) is served or
        # run, whatever folder it lies in; .well-known stays reachable.
        RewriteRule (^|/)\.(?!well-known(/|$)) - [R=404,L]
        # No script runs because its path was asked for, except the front
        # controllers at the root: a .php below them (in the package store,
        # an extension, var/) answers 404 instead of being executed.
        RewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI} -f
        RewriteRule ^/.+/[^/]+\.(php[0-9]?|phtml|phar)$ - [R=404,L]
        # REST API
        RewriteRule ^/api/ /index_rest.php [L]
        RewriteRule ^/index_rest\.php - [L]
        # SOAP server (soap.ini EnableSOAP=true): index_soap.php since
        # Exponential 6.0.15, soap.php only forwards to it. Uncomment with SOAP on:
        #RewriteRule ^/index_soap\.php - [L]
        RewriteRule ^/([^/]+/)?content/treemenu.* /index_treemenu.php [L]
        RewriteRule ^/var/([^/]+/)?storage/images(-versioned)?/.* - [L]
        # The stored files of PDF exports generated once (Setup > PDF export)
        RewriteRule ^/var/([^/]+/)?storage/pdf/[A-Za-z0-9][A-Za-z0-9._-]*\.pdf$ - [L]
        # Of the originals only the images; other uploads stay behind
        # content/download and its permissions.
        RewriteRule ^/var/([^/]+/)?storage/original/image/.+\.(png|jpe?g|gif|webp|svg)$ - [L]
        RewriteRule ^/var/([^/]+/)?cache/(texttoimage|public)/.* - [L]
        # With site.ini [FileSettings] CacheVarDir=var_cache (multi-site hosting):
        #RewriteRule ^/var_cache/([^/]+/)?cache/(texttoimage|public)/.* - [L]
        RewriteRule ^/design/[^/]+/(stylesheets|images|javascript|fonts)/.* - [L]
        RewriteRule ^/share/icons/.* - [L]
        # Icon themes of extensions (icon.ini [ExtensionSettings] IconExtensions[]): images with exactly one extension
        RewriteRule ^/extension/[^/]+/icons/[A-Za-z0-9_/-]+\.(svg|png|gif)$ - [L]
        RewriteRule ^/extension/[^/]+/design/[^/]+/(stylesheets|flash|images|lib|javascripts?|fonts|vendor|media)/.* - [L]
        RewriteRule ^/packages/styles/.+/(stylesheets|images|javascript)/[^/]+/.* - [L]
        RewriteRule ^/packages/styles/.+/thumbnail/.* - [L]
        # Only the preview images of imported packages
        RewriteRule ^/var/storage/packages/.+\.(png|jpe?g|gif|webp)$ - [L]
        RewriteRule ^/favicon\.ico - [L]
        RewriteRule ^/design/standard/images/favicon\.ico - [L]
        RewriteRule ^/robots\.txt - [L]
        # The site's service workers, served from the root
        RewriteRule ^/index\.js$ - [L]
        RewriteRule ^/sw\.js$ - [L]
        RewriteRule ^/w3c/p3p\.xml - [L]
        # Everything else, settings/, var/, vendor/, kernel/ and composer.json
        # included, is answered by index.php, never sent as a file.
        RewriteRule .* /index.php [L]
    </IfModule>

    <IfModule mod_headers.c>
        # SVG images are sanitized on upload; served from the storage they also
        # run nothing and load nothing when opened directly.
        <FilesMatch "(?i)\.svgz?$">
            Header always set Content-Security-Policy "sandbox; default-src 'none'; style-src 'unsafe-inline'"
            Header always set X-Content-Type-Options "nosniff"
        </FilesMatch>
        # The kernel sends these on its pages (site.ini [HTTPHeaderSettings]
        # SecurityHeaders[]); the same values for the files Apache serves.
        # "setifempty" keeps a header that index.php already sent.
        Header setifempty X-Content-Type-Options "nosniff"
        Header setifempty X-Frame-Options "SAMEORIGIN"
        Header setifempty Referrer-Policy "strict-origin-when-cross-origin"
        Header setifempty Permissions-Policy "camera=(), microphone=(), payment=(), usb=()"
        # HSTS once every name is served over HTTPS for good; start short
        # (doc/eng-us/install/13-security-hardening.md, 13.9). Here or in site.ini
        # SecurityHeaders[Strict-Transport-Security], not both.
        #Header always set Strict-Transport-Security "max-age=300"
    </IfModule>

    # Optional: browser caching of images and design files
    <IfModule mod_expires.c>
        <LocationMatch "^/var/([^/]+/)?storage/images(-versioned)?/">
            # The image URL changes when the image changes
            ExpiresActive On
            ExpiresDefault "access plus 1 year"
        </LocationMatch>
        <LocationMatch "^/(extension/[^/]+/)?design/[^/]+/(stylesheets|images|javascripts?|fonts|lib)/">
            # Design files keep their names: keep this short while a design changes
            ExpiresActive On
            ExpiresDefault "access plus 7 days"
        </LocationMatch>
        <LocationMatch "^/(share|extension/[^/]+)/icons/">
            ExpiresActive On
            ExpiresDefault "access plus 7 days"
        </LocationMatch>
        # Packed scripts and styles carry their modification time in the name
        # only with ezjscore.ini [Packer] AppendLastModifiedTime=enabled:
        #<LocationMatch "^/var/([^/]+/)?cache/public/">
        #    ExpiresActive On
        #    ExpiresDefault "access plus 30 days"
        #</LocationMatch>
    </IfModule>

    # A generated static cache (Setup > Cache > Static content cache) can be
    # served without starting PHP: add the rules of
    # doc/eng-us/examples/staticcache_offload.conf, translated to server context,
    # BEFORE the RewriteRule .* /index.php line.
</VirtualHost>
```

## Was sich von .htaccess unterscheidet

Die Regeln sind die von `.htaccess_root`, in derselben Reihenfolge, mit zwei Änderungen, die der Serverkontext
erfordert:

- **Jedes Muster beginnt mit `^/`.** Außerhalb von `<Directory>` sieht `RewriteRule` den URL-Pfad mit seinem führenden
  Schrägstrich.
- **Die Skriptregel prüft `%{DOCUMENT_ROOT}%{REQUEST_URI}`.** Im Serverkontext ist `%{REQUEST_FILENAME}` noch kein
  Dateiname; die Regel darf trotzdem nur bei einem vorhandenen Skript greifen, damit ein URL-Alias, der auf `.php`
  endet, weiter funktioniert.

Und eine Folge daraus: `[L]` beendet hier die Verarbeitung, daher erreicht `/content/treemenu/...` direkt
`index_treemenu.php` (unter `.htaccess` endet die Anfrage bei `index.php`, siehe
[Apache .htaccess](apache-htaccess.md#die-regeln-im-einzelnen)).

Alles, was nicht auf der Asset-Liste steht, `settings/`, `var/log/`, `vendor/`, `kernel/` und `composer.json`
eingeschlossen, wird auf `/index.php` umgeschrieben und niemals gesendet. Der Handler `<FilesMatch "\.php$">` übergibt
`/index.php`, `/index_rest.php` und `/index_treemenu.php` an PHP-FPM; kein anderes Skript wird je erreicht, weil die
Regeln nichts anderes an eine `.php`-Datei weiterleiten. Mit SOAP an nehmen Sie das Kommentarzeichen vor der Zeile für
`index_soap.php` weg: Der SOAP-Server heißt seit Exponential 6.0.15 `index_soap.php` (vorher `soap.php`, jetzt ein
Shim, das dorthin weiterleitet). Was zu ändern und wie es zu testen ist:
[Das SOAP-Einstiegsskript heißt jetzt index_soap.php](../../bc/6.0/soap-einstiegsskript-index-soap.md).

## Header und Caching

- `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy` und `Permissions-Policy` mit den Werten, die der
  Kernel auf seinen Seiten sendet (`site.ini [HTTPHeaderSettings] SecurityHeaders[]`), gesetzt mit `setifempty`, sodass
  eine Seite die des Kernels behält. HSTS ist auskommentiert: Schalten Sie es ein, sobald jeder Name über HTTPS
  ausgeliefert wird, hier oder in `site.ini`, nicht an beiden Stellen
  ([Kapitel 13, Abschnitt 13.9](../../installation/13-sicherheitshaertung.md#139-sicherheits-header-und-https)).
- Gespeicherte Bildvarianten werden ein Jahr lang gecacht (ihre URL ändert sich, wenn sich das Bild ändert),
  Design-Dateien und Icons eine Woche lang.

## Hinter einem Proxy oder Load Balancer

Endet TLS an einem Proxy, behalten Sie nur den Host für Port 80 (ohne seine Weiterleitung, mit dem `<Directory>`-Block,
dem Handler und den Regeln des Hosts für 443), lassen Sie den Proxy `X-Forwarded-Proto` und den ursprünglichen `Host`
senden und tragen Sie die Adresse des Proxys in `site.ini [HTTPHeaderSettings] TrustedProxies[]` ein. Siehe
[Kapitel 8, Abschnitt 8.8](../../installation/08-die-website-ausliefern.md#88-hinter-einem-reverse-proxy).

## Prüfen

```bash
curl -sI http://example.com/some/page | grep -i -E '^(HTTP|location)'     # 301 to https://
for p in /.git/config /.env /kernel/classes/ezini.php /vendor/autoload.php; do
  printf '%-30s %s\n' "$p" "$(curl -s -o /dev/null -w '%{http_code}' https://example.com$p)"   # 404 each
done
curl -s https://example.com/settings/site.ini | grep -c '^\[DatabaseSettings\]'   # 0: a page, not the file
curl -sI https://example.com/design/admin4/stylesheets/components.css | grep -i -E '^(HTTP|x-content-type)'
```

Wie dieses Beispiel geprüft wurde: `httpd -t`, danach beantwortete ein Wegwerf-Apache 2.4 mit dieser Datei (Ports,
Pfade, Socket und ein selbstsigniertes Zertifikat ersetzt) jede Anfrage der [Testliste](README.md#welche-konfiguration)
über HTTPS wie erwartet, leitete reines HTTP weiter und lieferte auf Port 80 eine ACME-Challenge-Datei aus.

## Verwandte Seiten

- [Apache .htaccess](apache-htaccess.md): die Regeln einzeln erklärt.
- [PHP-FPM-Pool](php-fpm.md): der Pool hinter dem Socket.
- [Kapitel 8, Abschnitt 8.5: Apache mit PHP-FPM](../../installation/08-die-website-ausliefern.md#85-apache-mit-php-fpm).
- [Kapitel 13: Sicherheitshärtung](../../installation/13-sicherheitshaertung.md).
- [`../staticcache_offload.conf`](../../../eng-us/examples/staticcache_offload.conf): den statischen Inhalts-Cache ohne PHP ausliefern.
- [Webserver-Beispiele](README.md) und [alle Beispiele](../README.md).
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite darf frei kopiert, übersetzt und verbessert werden.
