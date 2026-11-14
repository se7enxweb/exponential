---
title: Webserver-Rezepte für auf Abruf erzeugte Bild-Aliase
audience: admin, hosting, developer
category: examples
order: 15
summary: Die Rewrite-Regeln, die ein fehlendes Bild der Ablage und content/image an index_image.php geben, für Apache (.htaccess_root und ein virtueller Host mit AllowOverride None), nginx (auch vor Apache), LiteSpeed und OpenLiteSpeed, FrankenPHP und Caddy, Exponential Velocity und einen Datenbank-Cluster, dazu der Typ für AVIF und das Bildbudget des Request Shield.
keywords: Bild-Aliase, auf Abruf, Rewrite-Regeln, Umschreiberegeln, virtueller Host, Cluster-Gateway, Bildbudget, Platzhalter, index_image.php, content/image, try_files, Apache, AllowOverride None, nginx, Plesk, LiteSpeed, OpenLiteSpeed, FrankenPHP, Caddy, Caddyfile, Velocity, eZDFS, index_cluster.php, CLUSTER_IMAGE_ALIAS_ON_DEMAND, AVIF, WebP, AddType, Request Shield, EXP-IMAGES, image aliases, on demand, rewrite rules, docroot protect, proxy, cluster, request shield
type: example
---
# Webserver-Rezepte für auf Abruf erzeugte Bild-Aliase

Exponential 6.0.15 erzeugt Bild-Aliase, wenn der Browser nach ihnen fragt
([die Funktion](../../funktionen/6.0/bild-aliase-auf-abruf.md)). Zwei Arten von Anfragen müssen das leichte
Einstiegsskript `index_image.php` im Wurzelverzeichnis der Installation erreichen, das den Alias erzeugt (oder ihn auf
einer Staging-Website holt) oder mit einem Platzhalter antwortet, ohne Sitzung und ohne den Seitenkernel:

1. ein **Bild der Ablage, das nicht da ist** (`var/<site>/storage/images/...` oder `images-versioned/...`): ein noch
   nicht erzeugter Alias. Vorhandene Dateien liefert weiterhin der Webserver aus wie bisher;
2. **`content/image/...`**, die Adresse, die ein Template für einen noch nicht erzeugten Alias bekommt (mit oder ohne
   URI-Siteaccess davor, `/eng/content/image/...`).

`index_image.php` liest die ursprüngliche `REQUEST_URI`, deshalb braucht es nur ein Umschreiben (keine
Weiterleitung). Es beantwortet auch `/index_image.php/<storage path>` für einen Server ohne Rewrite-Regeln.

Ohne diese Regeln funktioniert die Website mit dem Standard `OnDemandURL=token` trotzdem: `content/image` beantwortet
`index.php` (ein voller Start des Kernels je Bild, langsamer), und ein fehlender Pfad in der Ablage ist ein einfaches
404. Nur `OnDemandURL=storage` hängt von der Regel für die Ablage ab. Hinweise zur Aktualisierung:
[Aktualisierung auf 6.0.15: auf Abruf erzeugte Bild-Aliase](../../bc/6.0/bild-aliase-auf-abruf.md).

## Inhalt

1. [Apache mit .htaccess](#apache-mit-htaccess)
2. [Virtueller Apache-Host mit AllowOverride None](#virtueller-apache-host-mit-allowoverride-none)
3. [Listen erlaubter Skripte im Wurzelverzeichnis](#listen-erlaubter-skripte-im-wurzelverzeichnis)
4. [nginx mit PHP-FPM](#nginx-mit-php-fpm)
5. [nginx vor Apache](#nginx-vor-apache)
6. [LiteSpeed und OpenLiteSpeed](#litespeed-und-openlitespeed)
7. [FrankenPHP und Caddy](#frankenphp-und-caddy)
8. [Exponential Velocity](#exponential-velocity)
9. [Das Cluster-Gateway](#das-cluster-gateway)
10. [Die Typen für AVIF und WebP](#die-typen-für-avif-und-webp)
11. [Das Bildbudget des Request Shield](#das-bildbudget-des-request-shield)
12. [Prüfen](#prüfen)

## Apache mit .htaccess

`.htaccess_root` und `.htaccess_root_static` enthalten die Regeln, aktiv, direkt vor der Durchreichung der Ablage.
Eine Website, die `.htaccess_root` vor 6.0.15 nach `.htaccess` kopiert hat, kopiert es erneut
(`cp .htaccess_root .htaccess`, nachdem sie eigene Zeilen gesichert hat):

```apache
# An image of the storage that is not there (an image alias not made yet, see
# settings/image.ini [AliasSettings]) goes to index_image.php, which makes the
# alias or answers a placeholder; the files that exist are served as they are.
# content/image, the address of an image alias made on demand, is answered by
# the same script, without the page kernel.
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^var/([^/]+/)?storage/images(-versioned)?/.* index_image.php [L]
RewriteRule ^([^/]+/)?content/image/ index_image.php [L]
RewriteRule ^index_image\.php - [L]
RewriteRule ^var/([^/]+/)?storage/images(-versioned)?/.* - [L]
```

Die `RewriteCond` gehört nur zur ersten Regel; halten Sie die vier Zeilen zusammen und in dieser Reihenfolge, vor
`RewriteRule ^var/([^/]+/)?storage/images(-versioned)?/.* - [L]`. Die ganze Datei, Regel für Regel:
[Apache .htaccess](apache-htaccess.md).

## Virtueller Apache-Host mit AllowOverride None

Ein virtueller Host, der kein `.htaccess` liest (`AllowOverride None`, schneller, die empfohlene Einrichtung),
braucht die Regeln in seiner eigenen Konfiguration.

- **In einem `<Directory>`-Block** für das Document Root funktionieren dieselben Zeilen wie in `.htaccess`
  unverändert. So läuft alpha.se7enx.com (Plesk, `vhost_ssl.conf` und `vhost.conf`, nach der Regel für das Baummenü).
- **Im Serverkontext** (direkt in `<VirtualHost>`) beginnen Pfade mit einem Schrägstrich, und `%{REQUEST_FILENAME}`
  ist noch nicht zu einer Datei aufgelöst, deshalb nennt die Bedingung die Datei selbst:

```apache
        # Image aliases made on demand
        RewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI} !-f
        RewriteRule ^/var/([^/]+/)?storage/images(-versioned)?/.* /index_image.php [L]
        RewriteRule ^/([^/]+/)?content/image/ /index_image.php [L]
        RewriteRule ^/index_image\.php - [L]
        RewriteRule ^/var/([^/]+/)?storage/images(-versioned)?/.* - [L]
```

Der vollständige virtuelle Host: [Apache, virtueller Host](apache-virtueller-host.md). Prüfen Sie mit
`apachectl configtest` und laden Sie Apache dann neu.

## Listen erlaubter Skripte im Wurzelverzeichnis

Eine Konfiguration, die die Skripte auflistet, die im Wurzelverzeichnis laufen dürfen, muss `index_image.php` nennen,
sonst antwortet die umgeschriebene Anfrage dort mit 404. Auf alpha.se7enx.com ist das die gemeinsame
`/etc/httpd/conf/exponential-docroot-protect.conf`:

```apache
RewriteCond %{REQUEST_URI} !^/(index(_rest|_treemenu|_cluster|_webdav|_soap|_image)?\.php|soap\.php|webdav\.php|robots\.txt|favicon\.ico|sw\.js)$
```

Dasselbe gilt für eine `web.config` von IIS, die Liste der PHP-Einstiegspunkte eines Hosting-Panels oder eine
WAF-Regel, die nur `index.php` erlaubt.

## nginx mit PHP-FPM

Im Server-Block von [nginx.conf](../../../eng-us/examples/web-servers/nginx.conf): Die Location der Ablage fällt auf
`index_image.php` zurück statt auf `=404`, und zwei Locations schicken `content/image` und das Einstiegsskript an PHP.
Geprüft mit `nginx -t` und einem echten nginx 1.28 vor PHP-FPM.

```nginx
# content/image, the address of an image alias made on demand, is answered by
# index_image.php, without the page kernel (REQUEST_URI stays the original).
location ~ ^/([^/]+/)?content/image/ {
    rewrite ^ /index_image.php last;
}
location = /index_image.php {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/index_image.php;
    fastcgi_param SCRIPT_NAME /index_image.php;
    fastcgi_pass unix:/run/php-fpm/www.sock;
}

# A storage image that is not there (an image alias not made yet) goes to
# index_image.php, which makes it or answers a placeholder.
location ~ ^/var/([^/]+/)?storage/images(-versioned)?/ {
    try_files $uri /index_image.php$is_args$args;
}
```

`fastcgi_params` übergibt `REQUEST_URI` als `$request_uri`, die ursprüngliche Adresse, die `index_image.php` liest.
Die SVG-Location, die die Sandbox-Header setzt, steht davor und darf `try_files $uri =404;` behalten (SVG-Bilder haben
keine Aliase). Mit einem Datenbank-Cluster geht die Location der Ablage stattdessen an `index_cluster.php` (siehe
[Das Cluster-Gateway](#das-cluster-gateway)).

## nginx vor Apache

Plesk und viele Hosting-Panels setzen nginx vor Apache und lassen nginx statische Dateien selbst ausliefern. Seine
Location der Ablage beantwortet ein fehlendes Bild dann mit 404, bevor Apache es sieht. Lassen Sie sie auf Apache
zurückfallen, dessen Regeln das Bild an `index_image.php` geben. Auf alpha.se7enx.com in den „Zusätzlichen
nginx-Direktiven“ von Plesk (`vhost_nginx.conf`), vor der gemeinsamen statischen Location (die erste passende
Regex-Location gewinnt):

```nginx
location ~ ^/var/([^/]+/)?storage/images(-versioned)?/(?!.*/\.).+\.(png|jpe?g|gif|webp|avif)$ {
    expires 1y;
    access_log off;
    try_files $uri @exp_image_alias;
}
location @exp_image_alias {
    proxy_read_timeout 120;
    proxy_pass https://plesk_apache_ssl;
    proxy_hide_header upgrade;
    proxy_ssl_server_name on;
    proxy_ssl_name $host;
    proxy_ssl_session_reuse on;
    proxy_set_header Host             $host;
    proxy_set_header X-Real-IP        $remote_addr;
    proxy_set_header X-Forwarded-For  $proxy_add_x_forwarded_for;
    proxy_set_header X-Accel-Internal /internal-nginx-static-location;
    access_log off;
}
```

`plesk_apache_ssl` ist der Name des Upstreams, den Plesk in der `nginx.conf` der Domain erzeugt; übernehmen Sie für
ein anderes Panel den Namen des Upstreams und die Proxy-Header von dort. Wiederholen Sie alle Sicherheits-Header, die
die statische Location setzt (`add_header ... always;`), da eine Location mit eigenem `add_header` keine erbt.
`content/image` braucht auf der Seite von nginx nichts: nginx reicht es wie jede Seite an Apache weiter. Prüfen Sie mit
`nginx -t`, laden Sie nginx neu und stellen Sie sicher, dass die Liste der Endungen der statischen Location `avif`
enthält, sonst liefert Apache die AVIF-Varianten mit dem falschen Typ aus (siehe
[Die Typen für AVIF und WebP](#die-typen-für-avif-und-webp)).

## LiteSpeed und OpenLiteSpeed

- **LiteSpeed Web Server (Enterprise)** liest `.htaccess` wie Apache: Die Regeln aus `.htaccess_root` funktionieren,
  wie sie sind.
- **OpenLiteSpeed** liest kein `.htaccess`; die Regeln gehören in den Rewrite-Block des virtuellen Hosts, im Stil
  `^/?` von [openlitespeed-vhost.conf](../../../eng-us/examples/web-servers/openlitespeed-vhost.conf), vor die
  Durchreichung der Ablage:

```apache
RewriteCond %{DOCUMENT_ROOT}%{REQUEST_URI} !-f
RewriteRule ^/?var/([^/]+/)?storage/images(-versioned)?/.* /index_image.php [L]
RewriteRule ^/?([^/]+/)?content/image/ /index_image.php [L]
RewriteRule ^/?index_image\.php - [L]
RewriteRule ^/?var/([^/]+/)?storage/images(-versioned)?/.* - [L]
```

Ergänzen Sie `index_image.php` überall dort, wo der virtuelle Host die ausführbaren Skripte im Wurzelverzeichnis
auflistet, und `image/avif avif` in den MIME-Typen (Server Configuration > General > MIME Settings), falls es fehlt.
Diese Regeln liefen nicht auf einem LiteSpeed-Server (keiner war verfügbar); es sind die unter Apache getesteten
Apache-Regeln. Mehr: [LiteSpeed](litespeed.md).

## FrankenPHP und Caddy

Über die Engine frankenphp von Velocity hat das erzeugte Caddyfile (`var/vc/frankenphp/run/Caddyfile`) den Block
bereits; starten Sie Velocity nach der Aktualisierung neu, damit es erneut geschrieben wird. Ein eigenes Caddyfile
braucht ihn vor `file_server @static` und `index_image.php` in der Ausnahme von `@front`:

```caddyfile
		@image {
			path_regexp image ^/var/([^/]+/)?storage/images(-versioned)?/
			not file
		}
		rewrite @image /index_image.php
		@imageview path_regexp imageview ^/([^/]+/)?content/image/
		rewrite @imageview /index_image.php
		...
		@front not path /index_rest.php /index_treemenu.php /index_image.php
		rewrite @front /index.php
```

Caddy übergibt PHP nach einem `rewrite` die ursprüngliche Anfrage-URI. Geprüft mit FrankenPHP 1.12.7
(`frankenphp validate` und ein laufender Server); derselbe Block funktioniert in einem Caddy, der PHP an PHP-FPM
weitergibt. Die vollständigen Dateien:
[Caddyfile.example](../../../eng-us/examples/web-servers/Caddyfile.example),
[frankenphp.Caddyfile.example](../../../eng-us/examples/web-servers/frankenphp.Caddyfile.example). Der Worker-Modus
von FrankenPHP wurde nicht getestet.

## Exponential Velocity

Nichts einzustellen. Die Front-Controller-Routen des Kernels (`expVelocity::FRONT_CONTROLLERS`) schicken ein Bild der
Ablage, das die Engine nicht als statische Datei gefunden hat, und jede Anfrage an `content/image` an
`index_image.php`, das im dauerhaften Worker läuft (Anfragebereich zurückgesetzt, der Siteaccess am Host ohne den Port
erkannt, der Request Shield so ausgeführt, wie `index.php` ihn ausführt). Velocity schreibt die Routen beim Start in
seine Serverkonfiguration, starten Sie es also nach der Aktualisierung einmal neu:

```bash
./console exp:velocity deploy --kernel
```

Die eigene php-Engine von Velocity (der Router `bin/php/velocity-router.php`) leitet sie genauso weiter. Der
Antwort-Cache von Velocity beachtet `Vary` nicht, deshalb wird eine ausgehandelte WebP-/AVIF-Antwort, die direkt
gesendet wird, als `private` gesendet; Weiterleitungen und einfache Aliase werden normal zwischengespeichert. Nach dem
Entfernen von Aliasen wird der Antwort-Cache geleert.

## Das Cluster-Gateway

Ein Datenbank-Cluster (eZDFS oder eZDB, `file.ini [ClusteringSettings]`) hält die Dateien der Ablage anderswo, und
seine Bilder beantwortet `index_cluster.php`. Kommentieren Sie das Cluster-Paar in `.htaccess_root` ein; es muss
**vor** der Regel für `index_image.php` stehen:

```apache
RewriteRule ^var/([^/]+/)?storage/images(-versioned)?/.* index_cluster.php [L]
RewriteRule ^index_cluster\.php - [L]
```

Für nginx: `location ~ ^/var/([^/]+/)?storage/images(-versioned)?/ { rewrite ^ /index_cluster.php last; }` plus ein
FastCGI-Block `location = /index_cluster.php` wie der für `index_image.php`.

Wenn das Gateway keine Metadaten zu einem Bild der Ablage hat (ein noch nicht erzeugter Alias), schließt es seine
Verbindung und führt `index_image.php` in derselben Anfrage aus, das den Alias im Cluster erzeugt. Jede andere fehlende
Datei bleibt ein 404. Um die Übergabe auszuschalten, in `config.cluster.php`:

```php
define( 'CLUSTER_IMAGE_ALIAS_ON_DEMAND', false );
```

Auf einem Cluster sendet `Delivery=auto` die Datei direkt, statt weiterzuleiten, da der Webserver die Ablage nicht
selbst ausliefern kann; die Sperre je Alias ist der Eintrag `.generating` des Handlers in der gemeinsamen Datenbank.
Die Übergabe wurde mit einem Gateway ohne Metadaten auf eZFS-Ablage getestet, nicht auf einem echten Cluster.

## Die Typen für AVIF und WebP

Die WebP- und AVIF-Varianten eines Alias sind nach ihm benannt: `foo_large.jpg.webp`, `foo_large.jpg.avif`. Ein
Server, der `.avif` nicht kennt, bestimmt den Typ einer solchen Datei nach ihrem inneren `.jpg` und sendet
`image/jpeg` (die `/etc/mime.types` von RHEL 9 hat zum Beispiel kein `avif`). `.htaccess_root` enthält:

```apache
<IfModule mod_mime.c>
    AddType image/webp .webp
    AddType image/avif .avif
</IfModule>
```

Ein virtueller Host mit `AllowOverride None` braucht `AddType image/avif .avif` in seiner eigenen Konfiguration
(alpha.se7enx.com: `vhost_ssl.conf` und `vhost.conf`). Die `mime.types` von nginx 1.28 hat beide; älteren Versionen
kann `avif` fehlen (`image/avif avif;` im `types`-Block von `mime.types`). Bei nginx vor Apache ergänzen Sie `avif` in
der Liste der Endungen der statischen Location, sonst gibt nginx diese Dateien an Apache weiter.

## Das Bildbudget des Request Shield

Eine Seite mit Hunderten neuer Bilder fragt `content/image` (oder `index_image.php`) einmal je Bild an, alle auf
einmal. Das Standard-Tempolimit des Request Shield zählt jede Anfrage über PHP als Seitenaufruf und verlangt nach 120
in einer Minute die Browserprüfung, sodass eine solche Seite einige hundert Antworten 429 bekommt und nie vollständig
wird. Die Standardregeln von `exp_request_shield` nach 0.0.1.3 (`rules/exponential.rules`, Version 2026-11-14.1)
nehmen die Bilder aus dem Tempolimit heraus und geben ihnen ein eigenes Budget:

```
no-limit    requests
[EXP-PACE]      limit pages 300/min challenge-at 120 on-exceeded challenge at regex ^(?!(/[a-z0-9_]+)?/content/image/|/index_image\.php|/var/([^/]+/)?storage/images(-versioned)?/)
[EXP-IMAGES]    limit images 3000/min on-exceeded challenge at regex ^((/[a-z0-9_]+)?/content/image/|/index_image\.php|/var/([^/]+/)?storage/images(-versioned)?/)
expect GET /content/image/abc/x.jpg  times 600     answered
```

Eine Website mit einer älteren Kopie der Regeln ergänzt dieselben Zeilen in ihrer eigenen Regeldatei (mit eigenen IDs,
zum Beispiel `SITE-PAGES` und `SITE-IMAGES`) und prüft und testet sie dann:

```bash
php extension/exp_request_shield/bin/request-shield check settings/request-shield/request-shield.rules
php extension/exp_request_shield/bin/request-shield test  settings/request-shield/request-shield.rules
```

Eine Regeldatei der Website, die das Tempolimit so neu formuliert hat und dann die neue Kopie von `exponential.rules`
übernimmt, muss ihre eigenen Zeilen streichen: Zwei Budgets namens `pages` weist `check` zurück. Leitfaden:
[Den Request Shield einrichten](../../anleitungen/request-shield.md).

## Prüfen

Mit einem Bild, dessen Alias noch nicht erzeugt ist (Setup > Bild-Aliase > Testwerkzeuge > Ein Bild zeigt, welcher,
und die Adresse, die ein Template bekommt):

```bash
# the storage path of a missing alias: made, 200 with the image type (or 200 SVG placeholder while it is being made)
curl -sI https://www.example.org/var/site/storage/images/.../1234-1-eng-GB/photo_large.jpg
# again: now a static file, without X-Image-Placeholder
curl -sI https://www.example.org/var/site/storage/images/.../1234-1-eng-GB/photo_large.jpg
# an address that names nothing: 404 with the SVG placeholder and X-Image-Placeholder: 1
curl -sI https://www.example.org/var/site/storage/images/nothing/here_large.jpg
# an AVIF variant: Content-Type image/avif
curl -sI https://www.example.org/var/site/storage/images/.../photo_large.jpg.avif
```

Eine 404-HTML-Seite der Website (nicht der SVG-Platzhalter) bei der ersten Anfrage bedeutet, dass die Regel fehlt oder
nach der Durchreichung der Ablage steht; ein 403 oder 404 vom Server bedeutet, dass einer Liste erlaubter Skripte
`index_image.php` fehlt. Adressen von `content/image` antworten nie mit `Set-Cookie`.
`./console exp:image:alias status` listet die Prüfungen auf, die Aufmerksamkeit brauchen.
