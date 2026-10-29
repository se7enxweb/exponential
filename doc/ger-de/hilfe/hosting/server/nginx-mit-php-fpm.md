---
title: nginx mit PHP-FPM
audience: hosting, admin
category: hosting
order: 40
summary: Wie ein Hoster Exponential mit nginx und PHP-FPM ausliefert - der Server-Block, Regel für Regel aus .htaccess_root abgeleitet, warum seine Reihenfolge zählt, HTTPS, Uploads und die Prüfungen, die belegen, dass nichts nach außen dringt.
keywords: nginx, PHP-FPM, Server-Block, location, try_files, fastcgi_pass, fastcgi_params, client_max_body_size, http2, htaccess_root, Rewrite, abgeleitete Konfiguration, server block, rewrite, derived configuration
type: help
---
# nginx mit PHP-FPM

Diese Seite ist für Systemadministratoren bei Hosting-Unternehmen, die nginx betreiben und Exponential damit ausliefern wollen. Sie
beantwortet: welche Konfiguration es für nginx gibt, wie der Server-Block aufgebaut ist, worauf Sie nach einem Upgrade achten und wie Sie
belegen, dass das Routing stimmt. Die Quelle ist
[Kapitel 8, nginx mit PHP-FPM](../../../installation/08-die-website-ausliefern.md#86-nginx-mit-php-fpm).

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../../beispiele/webserver/nginx.md), [LiteSpeed](../../../beispiele/webserver/litespeed.md), [Caddy](../../../beispiele/webserver/caddy.md), [IIS](../../../beispiele/webserver/iis.md),
> [Velocity](../../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../control-panels/plesk.md), [cPanel](../control-panels/cpanel.md), [DirectAdmin](../control-panels/directadmin.md), [ISPConfig](../control-panels/ispconfig.md).

## Inhalt

1. [Was das Projekt für nginx ausliefert](#was-das-projekt-für-nginx-ausliefert)
2. [Der Server-Block](#der-server-block)
3. [Warum er so geordnet ist](#warum-er-so-geordnet-ist)
4. [HTTPS und HTTP/2](#https-und-http2)
5. [Die Seite von PHP-FPM](#die-seite-von-php-fpm)
6. [Prüfungen vor dem Go-Live](#prüfungen-vor-dem-go-live)
7. [Checkliste](#checkliste)

## Was das Projekt für nginx ausliefert

**Nichts ausgeliefert; ein abgeleiteter Block dokumentiert.** Exponential liefert in seinem Repository keine Konfiguration für nginx aus.
Der Server-Block in Kapitel 8 ist eine Übersetzung von `.htaccess_root` in nginx-Direktiven, Regel für Regel. Kapitel 8 sagt es deutlich:
Testen Sie ihn auf Ihrer Website, bevor Sie sich darauf verlassen, und vergleichen Sie ihn nach jedem Upgrade mit `.htaccess_root`.
[Unterstützung von Betriebssystemen](../../betriebssystemunterstuetzung.md#webserver) führt nginx mit PHP-FPM als Stufe 2: funktioniert bekanntermaßen.

> **Warnung**
> Behandeln Sie den Block unten als Ausgangspunkt, für den Sie zuständig sind. Wenn ein neues Release `.htaccess_root` ändert, ändert sich
> Ihr nginx-Block nicht mit. Setzen Sie den Vergleich der nginx-Regeln mit `.htaccess_root` auf die Upgrade-Checkliste jedes Tarifs mit
> nginx.

nginx liest `.htaccess` nie, `./console exp:setup:htaccess --apply` schützt hier also nichts. Beim Vergleich hilft der
Befehl trotzdem: Behalten Sie eine damit kopierte `.htaccess`, dann gibt `./console exp:setup:htaccess` nach einem
Upgrade den Diff zwischen dieser Kopie und der neuen `.htaccess_root` aus (Exit-Code 1, solange sie abweichen), also
die Liste der Regeln, die in den Server-Block zu übernehmen sind. `config.php` wird auf jedem Server gleich
eingerichtet: `./console exp:setup:config --apply` und `./console exp:config:set`
([.htaccess und config.php von der Konsole einrichten](../../../anleitungen/setup-dateien-von-der-konsole.md)).

## Der Server-Block

Passen Sie die Wurzel und den PHP-FPM-Socket an (ein Socket pro Pool eines Kunden auf einer geteilten Maschine). Das ist der Block aus
Kapitel 8:

```nginx
server {
    listen 80;
    server_name example.com www.example.com;

    # The installation root, where index.php is
    root /var/www/example.com;
    index index.php;

    # Uploads: nginx's default limit is 1 MB
    client_max_body_size 64m;

    # 1. No dot file or dot directory, anywhere; .well-known stays reachable
    location ~ (^|/)\.(?!well-known(/|$)) {
        return 404;
    }

    # 2. No script below the root runs because its path was asked for
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
    location ~ ^/([^/]+/)?content/treemenu {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index_treemenu.php;
        fastcgi_param SCRIPT_NAME /index_treemenu.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }

    # 4. Files served as they are; a listed path that does not exist is a 404
    location ~ ^/var/([^/]+/)?storage/images(-versioned)?/                                    { try_files $uri =404; }
    location ~ ^/var/([^/]+/)?storage/original/image/.+\.(png|jpe?g|gif|webp|svg)$             { try_files $uri =404; }
    location ~ ^/var/([^/]+/)?storage/pdf/[A-Za-z0-9][A-Za-z0-9._-]*\.pdf$                      { try_files $uri =404; }
    location ~ ^/var/([^/]+/)?cache/(texttoimage|public)/                                       { try_files $uri =404; }
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

    # 5. Everything else goes to index.php, with the original REQUEST_URI
    location / {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root/index.php;
        fastcgi_param SCRIPT_NAME /index.php;
        fastcgi_pass unix:/run/php-fpm/www.sock;
    }
}
```

Für eine Installation mit mehreren Websites mit `site.ini [FileSettings] CacheVarDir=var_cache` fügt Kapitel 8 eine auskommentierte
Location für `^/var_cache/([^/]+/)?cache/(texttoimage|public)/` hinzu; siehe
[Logs, INI-Cache und Ablaufzeitstempel pro Website](../../../funktionen/6.0/log-und-cache-pfade-fuer-mehrere-websites.md).

## Warum er so geordnet ist

Aus Kapitel 8:

- nginx wählt eine Location nach seinem eigenen Vorrang, nicht nach der Reihenfolge in der Datei: zuerst genaue Treffer (`=`), dann das
  längste Präfix mit `^~`, dann **reguläre Ausdrücke in der Reihenfolge, in der sie stehen**, dann das längste einfache Präfix. Die Regeln
  für Dateien mit Punkt und für Skripte sind deshalb die ersten regulären Ausdrücke, so wie sie die ersten Regeln von `.htaccess_root`
  sind.
- Der Front-Controller wird in `SCRIPT_FILENAME` genannt und die Anfrage wird nicht umgeschrieben, sodass `REQUEST_URI` weiterhin die
  Adresse trägt, die der Besucher angefragt hat, genau wie bei Apache.
- Ein `.php` in der Wurzel außer den Front-Controllern (zum Beispiel `/config.php`) erreicht `location /` und wird von `index.php`
  beantwortet, nie ausgeführt.
- Die statischen Locations haben kein `fastcgi_pass`, sodass dort kein PHP läuft; die Skriptregel davor stellt sicher, dass ein `.php`
  unterhalb eines Asset-Verzeichnisses auch nicht als Text gesendet wird.
- `.htaccess_root` antwortet für ein Skript unterhalb der Wurzel nur dann mit 404, wenn die Datei existiert; dieser Block antwortet immer
  mit 404.
- `fastcgi_params` aus den nginx-Paketen gibt `HTTPS` weiter, wenn die Verbindung TLS ist, und das prüft Exponential zuerst.

## HTTPS und HTTP/2

Fügen Sie `listen 443 ssl;` (und `http2 on;` ab nginx 1.25.1), `ssl_certificate` und `ssl_certificate_key` hinzu, und einen eigenen
`server` auf Port 80, der `301 https://$host$request_uri` zurückgibt. Prüfen Sie mit `nginx -t` und laden Sie mit
`systemctl reload nginx` neu. Sicherheitsheader für statische Dateien: `add_header ... always;`
([Kapitel 13](../../../installation/13-sicherheitshaertung.md)). Der Kernel sendet seine eigenen Sicherheitsheader auf Seiten
(`site.ini [HTTPHeaderSettings] SecurityHeaders[]`).

Zertifikate für viele Domains von Kunden: siehe [TLS und HTTPS](../sicherheit/tls-und-https.md).

## Die Seite von PHP-FPM

Auf der Seite von PHP-FPM ist alles wie unter Apache: ein Pool pro Website als Benutzer der Website, OPcache an mit Prüfung der
Zeitstempel, Uploads passend zum Bedarf des Kunden (und `client_max_body_size` in nginx entsprechend) und nach einer PHP-Änderung ein
Neuladen **des** FPM-Masters, der die Website ausliefert. `exp:velocity deploy --no-velocity` findet diesen Master und leert die Caches in
der richtigen Reihenfolge. Siehe [Apache mit PHP-FPM](apache-mit-php-fpm.md#das-richtige-php-fpm-neu-laden) und
[PHP-FPM abstimmen](../leistung/abstimmung-von-php-fpm.md).

## Prüfungen vor dem Go-Live

Ersetzen Sie die Adresse durch Ihre eigene. Das sind die Prüfungen aus Kapitel 8:

```bash
nginx -t
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/.git/config                      # 404
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/kernel/classes/ezcontentobject.php  # 404
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/robots.txt                        # 200 when the file exists
curl -s https://example.com/settings/site.ini | grep -c '^\[DatabaseSettings\]'                # 0
```

Was schiefgehen kann:

| Symptom | Ursache | Abhilfe |
|---|---|---|
| ein 200 für die Anfrage an `.git` oder den Kernel | der Block ist nicht dieser, oder eine andere `location` gewinnt zuerst | die Regeln für Dateien mit Punkt und für Skripte an den Anfang der regulären Ausdrücke setzen |
| 404 für jedes Asset unter `design/` | `root` ist nicht das Wurzelverzeichnis der Installation | `root` auf das Verzeichnis mit `index.php` richten |
| Uploads über 1 MB scheitern | `client_max_body_size` steht auf dem Standard von nginx | zusammen mit `upload_max_filesize` und `post_max_size` erhöhen |
| Links und Umleitungen gehen hinter einem TLS-Terminator auf `http://` | dem Terminator wird nicht vertraut, oder er sendet kein `X-Forwarded-Proto` | [Varnish und Reverse Proxys](../leistung/varnish-und-reverse-proxys.md) |

## Checkliste

- [ ] Server-Block aus der aktuellen `.htaccess_root` abgeleitet, auf Staging getestet.
- [ ] Die Regeln für Dateien mit Punkt und für Skripte sind die ersten Locations mit regulären Ausdrücken.
- [ ] `client_max_body_size` passt zu den Upload-Grenzen von PHP.
- [ ] HTTPS an, Port 80 umgeleitet, `nginx -t` ohne Fehler.
- [ ] Die Prüfungen oben bestehen.
- [ ] Die Upgrade-Checkliste enthält den Vergleich des Blocks mit `.htaccess_root`.

**Demomodus mit nginx.** nginx gibt nichts aus seiner eigenen Umgebung an PHP weiter: Fügen Sie für eine öffentliche Demo `fastcgi_param EXP_DEMO_MODE 1;` dort hinzu, wo ihr Server-Block Anfragen an PHP-FPM übergibt, oder `env[EXP_DEMO_MODE] = 1` in ihrem Pool. [`nginx-fastcgi-demo.conf`](../../../../eng-us/examples/demo-mode/nginx-fastcgi-demo.conf), [Demomodus für Hoster](../sicherheit/demomodus-fuer-hoster.md).

## Verwandte Seiten

- [Apache mit PHP-FPM](apache-mit-php-fpm.md)
- [Velocity neben Apache oder nginx](velocity-neben-apache-oder-nginx.md)
- [Einen Webserver wählen](einen-webserver-waehlen.md)
- [Kapitel 8: Die Website ausliefern](../../../installation/08-die-website-ausliefern.md)
- [.htaccess und config.php von der Konsole einrichten](../../../anleitungen/setup-dateien-von-der-konsole.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
