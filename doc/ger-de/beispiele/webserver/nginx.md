---
title: nginx
audience: admin, developer
category: examples
order: 40
summary: Eine vollständige nginx-Konfiguration für Exponential 6 mit PHP-FPM, HTTPS, HTTP/2 und Sicherheits-Headern: die Regeln von .htaccess_root Location für Location übertragen, geprüft mit nginx -t und einem Wegwerf-Server.
keywords: nginx, Server-Block, Umschreiberegeln, Sicherheits-Header, Beispiel, server block, location, try_files, fastcgi_pass, PHP-FPM, HTTPS, HTTP/2, add_header, map, rewrite rules, .htaccess_root, Let's Encrypt, example
type: example
---
# nginx

Exponential liefert keine eigene nginx-Konfiguration aus; [`nginx.conf`](../../../eng-us/examples/web-servers/nginx.conf)
in diesem Verzeichnis überträgt `.htaccess_root` (dieser Version) Regel für Regel in nginx-Direktiven. Sie erweitert den
Server-Block aus [Kapitel 8, Abschnitt 8.6](../../installation/08-die-website-ausliefern.md#86-nginx-mit-php-fpm) um
HTTPS, HTTP/2, eine Weiterleitung auf Port 80 und die Sicherheits-Header. Vergleichen Sie sie nach jedem Upgrade mit
`.htaccess_root`.

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](nginx.md), [LiteSpeed](litespeed.md), [Caddy](caddy.md), [IIS](iis.md),
> [Velocity](velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../../hilfe/hosting/control-panels/plesk.md), [cPanel](../../hilfe/hosting/control-panels/cpanel.md), [DirectAdmin](../../hilfe/hosting/control-panels/directadmin.md), [ISPConfig](../../hilfe/hosting/control-panels/ispconfig.md).
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../anleitungen/seiten-cache-mit-litespeed.md).

nginx liest `.htaccess` nie, deshalb schützt `./console exp:setup:htaccess --apply` eine nginx-Site nicht: Das tun die
Regeln unten. Nützlich ist die Konsole trotzdem: `./console exp:setup:config --apply` bringt `config.php` an ihren
Platz, und nach einem Upgrade zeigt `./console exp:setup:htaccess` an einer mit `exp:setup:htaccess` kopierten
`.htaccess` als Diff, welche Regeln von `.htaccess_root` sich geändert haben. Siehe
[.htaccess und config.php von der Konsole einrichten](../../anleitungen/setup-dateien-von-der-konsole.md).

## Installieren

1. Kopieren Sie die Datei nach `/etc/nginx/conf.d/example.com.conf`. Sie wird innerhalb des Blocks `http {}` gelesen:
   Sie enthält Blöcke `map` ebenso wie Blöcke `server`. (Die Dateien in `sites-available` unter Debian werden ebenfalls
   dort gelesen.)
2. Ersetzen Sie `example.com`, `/var/www/example.com`, den Socket von PHP-FPM (`/run/php-fpm/www.sock`, fünfmal) und die
   Zertifikatspfade.
3. Legen Sie den PHP-FPM-Pool an ([PHP-FPM-Pool](php-fpm.md)); der Benutzer von nginx muss dessen Socket öffnen können.
4. Besorgen Sie das Zertifikat, zum Beispiel mit `certbot certonly --webroot -w /var/www/example.com -d example.com -d
   www.example.com`; der Server auf Port 80 liefert dafür `/.well-known/acme-challenge/` aus.
5. `nginx -t`, dann `systemctl reload nginx`.

`http2 on;` braucht nginx 1.25.1 oder neuer; schreiben Sie in älteren Versionen stattdessen `listen 443 ssl http2;`.

## Die Datei

```nginx
# Exponential 6: nginx with PHP-FPM, HTTPS and HTTP/2
#
# Free documentation: GFDL 1.3 or later, see doc/eng-us/README-LICENSE.md; rules derived from .htaccess_root (GPL v2 or later).
#
# Derived, not shipped by the kernel: a translation of .htaccess_root (this
# release) into nginx directives, rule for rule. Compare it with
# .htaccess_root after every upgrade. Explained in nginx.md next to this file.
#
# Install: copy to /etc/nginx/conf.d/example.com.conf (it is read inside the
# http {} block: it holds "map" and "server" blocks), replace example.com,
# /var/www/example.com, the PHP-FPM socket and the certificate paths, then
# "nginx -t" and "systemctl reload nginx".

# Security headers on what nginx serves itself, with the values of the
# kernel's site.ini [HTTPHeaderSettings] SecurityHeaders[]. The kernel sends
# them on its pages; each map yields an empty value (no header) when PHP
# already sent it, so a page never gets the header twice.
map $upstream_http_x_content_type_options $exp_xcto {
    ""      "nosniff";
    default "";
}
map $upstream_http_x_frame_options $exp_xfo {
    ""      "SAMEORIGIN";
    default "";
}
map $upstream_http_referrer_policy $exp_rp {
    ""      "strict-origin-when-cross-origin";
    default "";
}
map $upstream_http_permissions_policy $exp_pp {
    ""      "camera=(), microphone=(), payment=(), usb=()";
    default "";
}

# Plain HTTP: the ACME challenge files, and a redirect for everything else
server {
    listen 80;
    listen [::]:80;
    server_name example.com www.example.com;
    root /var/www/example.com;

    location ^~ /.well-known/acme-challenge/ {
        try_files $uri =404;
    }
    location / {
        return 301 https://$host$request_uri;
    }
}

server {
    listen 443 ssl;
    listen [::]:443 ssl;
    http2 on;
    server_name example.com www.example.com;

    ssl_certificate     /etc/ssl/example.com/fullchain.pem;
    ssl_certificate_key /etc/ssl/example.com/privkey.pem;

    # The installation root, where index.php is
    root /var/www/example.com;
    index index.php;

    # Uploads: nginx's default limit is 1 MB
    client_max_body_size 64m;

    add_header X-Content-Type-Options $exp_xcto always;
    add_header X-Frame-Options $exp_xfo always;
    add_header Referrer-Policy $exp_rp always;
    add_header Permissions-Policy $exp_pp always;
    # HSTS once every name is served over HTTPS for good; start short. Here or
    # in site.ini SecurityHeaders[Strict-Transport-Security], not both.
    #add_header Strict-Transport-Security "max-age=300" always;

    # 1. No dot file or dot directory, anywhere; .well-known stays reachable
    location ~ (^|/)\.(?!well-known(/|$)) {
        return 404;
    }

    # 2. No script below the root runs because its path was asked for
    #    (.htaccess_root answers 404 only when the file exists; this answers 404 always)
    location ~ ^/.+/[^/]+\.(php[0-9]?|phtml|phar)$ {
        return 404;
    }

    # 3. Front controllers
    location ^~ /api/ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index_rest.php;
        fastcgi_param SCRIPT_NAME /index_rest.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }
    location = /index_rest.php {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index_rest.php;
        fastcgi_param SCRIPT_NAME /index_rest.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }
    # The SOAP server (soap.ini EnableSOAP=true) is index_soap.php since
    # Exponential 6.0.15; soap.php only forwards to it. Uncomment with SOAP on:
    #location = /index_soap.php {
    #    include fastcgi_params;
    #    fastcgi_param SCRIPT_FILENAME $document_root/index_soap.php;
    #    fastcgi_param SCRIPT_NAME /index_soap.php;
    #    fastcgi_pass unix:/run/php-fpm/www.sock;
    #}
    location ~ ^/([^/]+/)?content/treemenu {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index_treemenu.php;
        fastcgi_param SCRIPT_NAME /index_treemenu.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }

    # 4. Files served as they are (the "- [L]" rules of .htaccess_root);
    #    a listed path that does not exist is a 404, as under Apache.
    #    Uploaded SVG images first: they run nothing and load nothing when opened
    #    directly (.htaccess_root's FilesMatch for .svg; here for the storage,
    #    where the uploads are). A location with add_header of its own does not
    #    inherit the server's, so the other headers are repeated.
    location ~* ^/var/([^/]+/)?storage/(images(-versioned)?/.+\.svgz?|original/image/.+\.svg)$ {
        add_header Content-Security-Policy "sandbox; default-src 'none'; style-src 'unsafe-inline'" always;
        add_header X-Content-Type-Options "nosniff" always;
        add_header X-Frame-Options "SAMEORIGIN" always;
        add_header Referrer-Policy "strict-origin-when-cross-origin" always;
        try_files $uri =404;
    }
    location ~ ^/var/([^/]+/)?storage/images(-versioned)?/                                    { try_files $uri =404; }
    location ~ ^/var/([^/]+/)?storage/original/image/.+\.(png|jpe?g|gif|webp|svg)$             { try_files $uri =404; }
    location ~ ^/var/([^/]+/)?storage/pdf/[A-Za-z0-9][A-Za-z0-9._-]*\.pdf$                      { try_files $uri =404; }
    location ~ ^/var/([^/]+/)?cache/(texttoimage|public)/                                       { try_files $uri =404; }
    # With site.ini [FileSettings] CacheVarDir=var_cache (multi-site hosting):
    #location ~ ^/var_cache/([^/]+/)?cache/(texttoimage|public)/                               { try_files $uri =404; }
    location ~ ^/design/[^/]+/(stylesheets|images|javascript|fonts)/                            { try_files $uri =404; }
    location ~ ^/share/icons/                                                                   { try_files $uri =404; }
    location ~ ^/extension/[^/]+/icons/[A-Za-z0-9_/-]+\.(svg|png|gif)$                          { try_files $uri =404; }
    location ~ ^/extension/[^/]+/design/[^/]+/(stylesheets|flash|images|lib|javascripts?|fonts|vendor|media)/ { try_files $uri =404; }
    location ~ ^/packages/styles/.+/(stylesheets|images|javascript)/[^/]+/                      { try_files $uri =404; }
    location ~ ^/packages/styles/.+/thumbnail/                                                  { try_files $uri =404; }
    location ~ ^/var/storage/packages/.+\.(png|jpe?g|gif|webp)$                                 { try_files $uri =404; }
    location = /favicon.ico  { try_files $uri =404; }
    location = /robots.txt   { try_files $uri =404; }
    location = /index.js     { try_files $uri =404; }
    location = /sw.js        { try_files $uri =404; }
    location = /w3c/p3p.xml  { try_files $uri =404; }

    # 5. Everything else goes to index.php, with the original REQUEST_URI:
    #    settings/, var/, vendor/, kernel/ and composer.json included, which
    #    index.php answers with a page or a 404, never with the file
    location / {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index.php;
        fastcgi_param SCRIPT_NAME /index.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }
}
```

## Wie die Regeln einander entsprechen und warum sie so geordnet sind

- nginx wählt eine Location nach seiner eigenen Rangfolge, nicht nach der Reihenfolge in der Datei: zuerst exakte
  Treffer (`=`), dann das längste Präfix mit `^~`, dann **reguläre Ausdrücke in der Reihenfolge, in der sie stehen**,
  dann das längste einfache Präfix. Die Regeln für Punkt-Pfade und Skripte sind deshalb die ersten regulären Ausdrücke,
  so wie sie die ersten Regeln von `.htaccess_root` sind; `location /` fängt den Rest auf, wie die abschließende
  `RewriteRule .* index.php`.
- Die Front-Controller werden in `SCRIPT_FILENAME` genannt; die Anfrage wird nicht umgeschrieben, daher trägt
  `REQUEST_URI` (aus `fastcgi_params`) weiterhin die Adresse, die der Besucher angefordert hat, wie bei Apache.
- Der SOAP-Server heißt seit Exponential 6.0.15 `index_soap.php` (vorher `soap.php`, jetzt ein Shim, das dorthin
  weiterleitet). Seine Location ist auskommentiert, weil SOAP standardmäßig aus ist; nehmen Sie die Kommentarzeichen
  weg, wenn Sie SOAP einschalten, und ändern Sie eine `location = /soap.php` einer älteren Konfiguration entsprechend.
  Den Test mit `curl` finden Sie unter
  [Das SOAP-Einstiegsskript heißt jetzt index_soap.php](../../bc/6.0/soap-einstiegsskript-index-soap.md#testen).
- Die Locations für statische Dateien haben kein `fastcgi_pass`, dort läuft also kein PHP, und die Skript-Regel davor
  sorgt dafür, dass eine `.php` unterhalb eines Verzeichnisses für statische Dateien auch nicht als Text ausgeliefert
  wird. Die Skript-Regel antwortet für jeden solchen Pfad mit 404, ob er existiert oder nicht; das ist etwas strenger
  als `.htaccess_root`.
- `settings/`, `var/log/`, `vendor/`, `kernel/`, `composer.json` und jeder andere Pfad, der nicht auf der Liste steht,
  landen bei `location /` und werden von `index.php` beantwortet, nie ausgeliefert.
- **Header.** `add_header` wird nur von Locations ohne eigenes `add_header` geerbt, und es kann nicht prüfen, ob PHP
  einen Header schon gesendet hat. Die Blöcke `map` lösen das zweite Problem: Jeder liefert einen leeren Wert, den nginx
  nicht sendet, wenn die Antwort von PHP den Header bereits enthält. Die SVG-Location wiederholt die Header, weil sie ein
  eigenes `add_header` hat.
- `fastcgi_params` aus den nginx-Paketen übergibt bei einer TLS-Verbindung `HTTPS`, und das prüft Exponential zuerst.

## Hinter einem Proxy

Endet TLS an einem Load Balancer vor nginx, behalten Sie einen `server` auf dem Port, mit dem der Load Balancer spricht
(mit den Locations des Servers auf Port 443). nginx gibt jeden Anfrage-Header an PHP weiter (`X-Forwarded-Proto` kommt
als `HTTP_X_FORWARDED_PROTO` an); der Kernel glaubt ihm nur, wenn er von einer Adresse kommt, die in
`site.ini [HTTPHeaderSettings] TrustedProxies[]` steht
([Kapitel 8, Abschnitt 8.8](../../installation/08-die-website-ausliefern.md#88-hinter-einem-reverse-proxy)).

## WebDAV

Diese Datei leitet WebDAV nicht weiter (standardmäßig aus). Um es auszuliefern, fügen Sie vor der Auffangregel eine
`location ^~ /index_webdav.php` mit `fastcgi_param SCRIPT_FILENAME $document_root/index_webdav.php` und
`SCRIPT_NAME /index_webdav.php` hinzu, oder einen eigenen `server` für einen WebDAV-Host. Seit 6.0.15 heißt das Skript
`index_webdav.php`; ein Block für `webdav.php` funktioniert über einen weiterleitenden Shim bis zu einer späteren
Hauptversion weiter, ändern Sie aber **beides**, die `location` und `SCRIPT_FILENAME`. Vorher/nachher und der Test
(`curl -X PROPFIND -H 'Depth: 1'`, erwartet: `207 Multi-Status`):
[Das WebDAV-Einstiegsskript heißt jetzt index_webdav.php](../../bc/6.0/webdav-einstiegsskript-index-webdav.md#nginx).

## Prüfen

```bash
nginx -t
for p in /.git/config /.env /kernel/classes/ezini.php /extension/ezjscore/ezinfo.php; do
  printf '%-34s %s\n' "$p" "$(curl -s -o /dev/null -w '%{http_code}' https://example.com$p)"     # 404 each
done
curl -s https://example.com/settings/site.ini | grep -c '^\[DatabaseSettings\]'   # 0: a page, not the file
curl -sI https://example.com/design/admin4/stylesheets/components.css | grep -i -E '^(HTTP|x-content-type)'
```

So wurde dieses Beispiel geprüft: `nginx -t` mit nginx 1.28, danach beantwortete ein Wegwerf-nginx mit dieser Datei
(Ports, Pfade, Socket und ein selbstsigniertes Zertifikat ersetzt) jede Anfrage der [Testliste](README.md#welche-konfiguration)
über HTTPS wie erwartet, einschließlich der Sicherheits-Header und der SVG-Sandbox, leitete einfaches HTTP weiter und
lieferte auf Port 80 eine ACME-Challenge-Datei aus.

## Verwandte Seiten

- [Kapitel 8, Abschnitt 8.6: nginx mit PHP-FPM](../../installation/08-die-website-ausliefern.md#86-nginx-mit-php-fpm).
- [Kapitel 13, Abschnitt 13.2: Was der Webserver niemals ausliefern darf](../../installation/13-sicherheitshaertung.md#132-was-der-webserver-niemals-ausliefern-darf).
- [PHP-FPM-Pool](php-fpm.md), [Apache .htaccess](apache-htaccess.md) (die Regeln, eine nach der anderen erklärt).
- [.htaccess und config.php von der Konsole einrichten](../../anleitungen/setup-dateien-von-der-konsole.md): welche Server `.htaccess` lesen, und `config.php`.
- [Webserver-Beispiele](README.md) und [alle Beispiele](../README.md).
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite dürfen Sie frei kopieren, übersetzen und verbessern.
