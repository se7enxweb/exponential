---
title: Webserver einrichten
audience: dxp-developer
category: dxp-development
order: 13
summary: Ein DXP-Projekt mit Apache 2.4 oder Nginx und PHP-FPM ausliefern, oder für die Entwicklung mit der Symfony CLI - das Document Root, die Umleitung auf public/index.php, die Einstellungen APP_ENV, APP_DEBUG und APP_HTTP_CACHE, HTTPS und die Befehle zum Neuladen.
keywords: Webserver, Apache, Nginx, PHP-FPM, Symfony CLI, virtueller Host, Document Root, public/index.php, APP_HTTP_CACHE, HTTPS, certbot, neu laden, web server, virtual host, document root, reload
type: help
---
# Webserver einrichten

Diese Seite richtet sich an Entwickler und Administratoren, die ein DXP-Projekt hinter einen Webserver stellen. Die
Beispiele stammen aus der Anleitung des Skeletons (Abschnitt 6), gekürzt; passen Sie Hostnamen und Pfade an.

## Inhalt

1. [Was jede Einrichtung braucht](#was-jede-einrichtung-braucht)
2. [Apache 2.4](#apache-24)
3. [Nginx](#nginx)
4. [Symfony CLI für die Entwicklung](#symfony-cli-für-die-entwicklung)
5. [HTTPS](#https)
6. [Nach einer Änderung neu laden](#nach-einer-änderung-neu-laden)

## Was jede Einrichtung braucht

- Das Document Root ist `public/`, nie das Hauptverzeichnis des Projekts.
- Jede Anfrage, die keine vorhandene Datei ist, geht an `public/index.php`.
- `APP_ENV=prod`, `APP_DEBUG=0` in der Produktion. `APP_HTTP_CACHE=1` schaltet den HTTP-Cache von Symfony ein; setzen
  Sie es auf `0`, wenn Varnish davor zwischenspeichert ([HTTP-Cache und Varnish](http-cache-und-varnish.md)).
- Keine andere `.php`-Datei unter `public/` wird ausgeführt.

## Apache 2.4

Aktivieren Sie die Module: `a2enmod rewrite deflate headers expires`. Betreiben Sie Apache mit dem Event-MPM und
PHP-FPM.

```apache
<VirtualHost *:80>
    ServerName exponential.local
    DocumentRoot /var/www/exponential/public
    DirectoryIndex index.php

    SetEnv APP_ENV "prod"
    SetEnv APP_DEBUG "0"
    SetEnv APP_HTTP_CACHE "1"

    <Directory /var/www/exponential/public>
        AllowOverride None
        Require all granted
        FallbackResource /index.php
    </Directory>
</VirtualHost>
```

## Nginx

```nginx
server {
    listen 80;
    server_name exponential.local;
    root /var/www/exponential/public;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
        fastcgi_param APP_ENV prod;
        fastcgi_param APP_DEBUG 0;
        fastcgi_param APP_HTTP_CACHE 1;
        internal;
    }

    location ~ \.php$ {
        return 404;
    }
}
```

Testen und neu laden: `nginx -t && systemctl reload nginx`.

## Symfony CLI für die Entwicklung

```bash
symfony server:start          # HTTPS on https://127.0.0.1:8000
symfony server:start -d       # in the background
symfony server:log
symfony server:stop
```

Verwenden Sie sie nicht in der Produktion.

## HTTPS

Liefern Sie die Produktion nur über HTTPS aus. Die Anleitung verwendet Certbot als einen Weg zu einem Zertifikat
(`certbot --apache -d <your host>`). Setzen Sie `TRUSTED_PROXIES`, wenn ein Proxy oder Varnish TLS vor der Anwendung
beendet, damit Symfony korrekte URLs bildet.

## Nach einer Änderung neu laden

| Geändert | Befehl |
|---|---|
| Konfiguration von Apache | `systemctl reload apache2` |
| Konfiguration von Nginx | `systemctl reload nginx` |
| Einstellungen von PHP-FPM oder PHP-Code mit OPcache | `systemctl reload php8.3-fpm` (die Unit Ihrer Version) |
| Anwendungscode | den Symfony-Cache leeren und vorwärmen ([Befehle für den Alltag](befehle-fuer-den-alltag.md)) |

## Verwandte Seiten

- [Bereitstellen](dxp-bereitstellen.md)
- [Dateiberechtigungen](dateiberechtigungen.md)
- [Sicherheitshärtung (Exponential 6)](../sicherheitshaertung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
