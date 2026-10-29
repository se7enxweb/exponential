# Das SOAP-Einstiegsskript heißt jetzt index_soap.php

Lesen Sie diese Seite, wenn eine Installation SOAP-Anfragen beantwortet: `settings/soap.ini [GeneralSettings]
EnableSOAP=true`, die Erweiterung `syndication`, die Inhalte an andere Installationen exportiert, oder ein
Middleware-Produkt, das Funktionen aufruft, die eine Erweiterung registriert hat. Ist SOAP aus (die Voreinstellung),
ändert sich für Sie nichts; die Regeln dieser Seite sind trotzdem die richtigen für den Tag, an dem Sie es einschalten.

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](../../anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../../beispiele/webserver/nginx.md), [LiteSpeed](../../beispiele/webserver/litespeed.md), [Caddy](../../beispiele/webserver/caddy.md), [IIS](../../beispiele/webserver/iis.md),
> [Velocity](../../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../../hilfe/hosting/control-panels/plesk.md), [cPanel](../../hilfe/hosting/control-panels/cpanel.md), [DirectAdmin](../../hilfe/hosting/control-panels/directadmin.md), [ISPConfig](../../hilfe/hosting/control-panels/ispconfig.md).

Geändert am 29. Oktober 2026, in der Linie 6.0.15.

## Inhalt

1. [Was sich geändert hat](#was-sich-geändert-hat)
2. [Warum](#warum)
3. [Was vorerst weiter funktioniert](#was-vorerst-weiter-funktioniert)
4. [Was Sie ändern müssen](#was-sie-ändern-müssen)
5. [Apache: .htaccess und der HTTPS-Virtual-Host](#apache-htaccess-und-der-https-virtual-host)
6. [Plesk](#plesk)
7. [nginx](#nginx)
8. [Andere Server und Exponential Velocity](#andere-server-und-exponential-velocity)
9. [Die SOAP-Clients](#die-soap-clients)
10. [Testen](#testen)
11. [Für Entwickler](#für-entwickler)

## Was sich geändert hat

| Vorher (6.0.14 und älter) | Jetzt (6.0.15) |
|---|---|
| `soap.php` im Wurzelverzeichnis der Installation war der SOAP-Server | `index_soap.php` ist der SOAP-Server |
| | `soap.php` ist ein Shim: Es schreibt höchstens einmal am Tag einen Hinweis und bindet `index_soap.php` ein, sonst nichts |
| `https://www.example.com/soap.php` | `https://www.example.com/index_soap.php` |

Der Server selbst hat sich nicht geändert: dieselben Einstellungen (`soap.ini`), dieselben Erweiterungen
(`[ExtensionSettings] SOAPExtensions[]`, `extension/<name>/soap/initialize.php`), dieselbe Anmeldung mit
HTTP-Basic-Zugangsdaten, dieselben Antworten. Nur sein Dateiname ist neu.

Ein zweiter Anmeldeschritt (Zwei-Faktor-Authentifizierung) gilt für SOAP weiterhin nie: Ein Konto, das einen braucht,
meldet sich über SOAP wie bisher mit einem App-Passwort an. Siehe
[den zweiten Schritt auf den anderen Anmeldewegen](../../funktionen/6.0/zweiter-schritt-auf-anderen-anmeldewegen.md).

## Warum

- **Ein Namensmuster für alle Front-Controller.** Die Skripte, die der Webserver ausführen darf, sind `index.php`,
  `index_rest.php`, `index_treemenu.php` und `index_cluster.php`. `soap.php` fiel aus der Reihe, also brauchte jeder
  Satz Rewrite-Regeln, jede nginx-`location`, jede Liste erlaubter Skripte eine Ausnahme dafür, und viele
  Installationen, die SOAP einschalteten, vergaßen sie schlicht: Ihre SOAP-Anfragen endeten bei `index.php`, das mit
  einer Seite oder einem 404 antwortete.
- **Gehärtete Regeln bleiben einfach.** Die mitgelieferten Regeln führen keine `.php`-Datei aus, nur weil ihr Pfad
  angefragt wurde, außer den Front-Controllern im Wurzelverzeichnis. Heißt der SOAP-Server wie sie, beschreibt eine
  Regel wie `^index(_[a-z]+)?\.php$` alle, und ein Scanner oder ein Audit erkennt einen Front-Controller an seinem
  Namen.
- **Der WebDAV-Server folgt demselben Muster** in derselben Version (`webdav.php` wird zu `index_webdav.php`); siehe
  [Das WebDAV-Einstiegsskript heißt jetzt index_webdav.php](webdav-einstiegsskript-index-webdav.md).

## Was vorerst weiter funktioniert

- **`soap.php` antwortet weiterhin.** Es bindet nur `index_soap.php` ein, also bekommt ein Client oder eine Regel, die
  es noch nennt, dieselbe Antwort wie bisher. Die URIs und der Siteaccess werden relativ zu dem Namen gelesen, den die
  Anfrage verwendet hat.
- **Einmal am Tag** schreibt eine Anfrage über `soap.php` einen Hinweis nach `var/log/warning.log`:

  ```text
  [ Oct 29 2026 09:12:03 ] [192.0.2.10] soap.php:
  A request came through soap.php, the deprecated name of the SOAP entry script. It still answers, but change the
  web server rules and the SOAP clients to index_soap.php (Exponential 6.0.15). Requested URI: /soap.php
  ```

  Der Tag wird als Änderungszeit von `var/log/soap-php-deprecated.stamp` festgehalten; löschen Sie diese Datei, um den
  Hinweis bei der nächsten Anfrage erneut zu bekommen. Gibt es `var/log/` nicht oder ist es nicht beschreibbar,
  schreibt das Shim nichts und antwortet trotzdem.
- **Das Shim wird in einer späteren Hauptversion entfernt.** Bauen Sie nichts Neues auf `soap.php` auf.

## Was Sie ändern müssen

1. **Aktualisieren Sie den Code** wie gewohnt auf 6.0.15 (oder neuer). `index_soap.php` und das Shim kommen zusammen.
2. **Lassen Sie `index_soap.php` im Webserver durch**, an jeder Stelle, die heute `soap.php` durchlässt: die
   `.htaccess` der Installation, der HTTPS-Virtual-Host (`*:443`) und, wo beide existieren, der für einfaches HTTP
   (`*:80`), die zusätzlichen Direktiven in Plesk, der nginx-Serverblock. Die Abschnitte unten nennen die Zeilen.
3. **Richten Sie jeden SOAP-Client auf `index_soap.php` aus**: die Import-Einstellungen der Erweiterung `syndication`
   auf jeder importierenden Installation, Middleware, Skripte, Überwachungs-Checks. Siehe
   [Die SOAP-Clients](#die-soap-clients).
4. **Laden Sie den Webserver neu** und [testen](#testen) Sie die neue Adresse und, bis alle Clients umgestellt sind,
   die alte.
5. **Beobachten Sie `var/log/warning.log`** (und das Zugriffsprotokoll des Webservers) auf `soap.php`. Wenn es eine
   Weile niemand mehr aufgerufen hat, können Sie es am Webserver ablehnen (`RewriteRule ^soap\.php$ - [F,L]`, nginx
   `location = /soap.php { return 403; }`), damit ein vergessener Client deutlich scheitert, statt einen Namen zu
   benutzen, der verschwinden wird.

Wo der Webserver jede vorhandene Datei durchlässt (ein `RewriteCond %{REQUEST_FILENAME} !-f` vor der Regel, die alles an
`index.php` schickt), wird `index_soap.php` ohne eigene Regel erreicht. Testen Sie es trotzdem.

## Apache: .htaccess und der HTTPS-Virtual-Host

Die mitgelieferte `.htaccess_root` (kopiert nach `.htaccess`) und `.htaccess_root_static` enthalten die Zeile,
auskommentiert, direkt nach den Regeln der REST-API. Nehmen Sie das Kommentarzeichen weg, wenn SOAP an ist:

```apache
RewriteRule ^api/ index_rest.php [L]
RewriteRule ^index_rest\.php - [L]
# The SOAP server (settings/soap.ini EnableSOAP=true) is index_soap.php since
# Exponential 6.0.15; soap.php, its old name, only forwards to it. Uncomment
# when SOAP is switched on, and let the SOAP clients call index_soap.php:
RewriteRule ^index_soap\.php - [L]
```

Hat Ihre `.htaccess` eine Zeile für den alten Namen, etwa `RewriteRule ^soap\.php - [L]`, ändern Sie sie in die Zeile
oben (und behalten Sie die alte nur, solange Clients noch `soap.php` aufrufen).

In einem Virtual Host stehen die Regeln im Serverkontext, die Muster beginnen also mit `/`. Im Block
`<VirtualHost *:443>` (und in `<VirtualHost *:80>`, falls SOAP auch über einfaches HTTP ausgeliefert wird, was es
nicht sollte):

```apache
<VirtualHost *:443>
    # ...
    RewriteEngine On
    RewriteRule ^/api/ /index_rest.php [L]
    RewriteRule ^/index_rest\.php - [L]
    RewriteRule ^/index_soap\.php - [L]
    # solange alte Clients noch soap.php aufrufen:
    RewriteRule ^/soap\.php - [L]
    # ...
    RewriteRule . /index.php [L]
</VirtualHost>
```

Ein Virtual Host, der PHP nur für eine Liste von Skripten an PHP-FPM übergibt
(`<FilesMatch "^(index|index_rest|index_treemenu)\.php$">` mit `SetHandler "proxy:unix:...|fcgi://localhost"`),
braucht `index_soap` ebenfalls in dieser Liste, sonst sendet Apache den Quelltext von `index_soap.php` als Datei:

```apache
<FilesMatch "^(index|index_rest|index_treemenu|index_soap)\.php$">
    SetHandler "proxy:unix:/run/php-fpm/www.sock|fcgi://localhost"
</FilesMatch>
```

Eine Beschränkung auf die Adressen der importierenden Installationen, die die
[Syndizierungsseite](../../hilfe/integratoren/syndizierung-zwischen-installationen.md) empfiehlt, zieht mit dem Namen um:

```apache
<Location "/index_soap.php">
    Require ip 192.0.2.10 198.51.100.0/24
</Location>
```

Konfiguration prüfen und neu laden: `apachectl configtest && systemctl reload httpd` (`apache2` unter Debian).

## Plesk

Plesk hält die Regeln einer Domain unter **Websites & Domains > (Domain) > Apache- & nginx-Einstellungen > Zusätzliche
Direktiven für HTTPS** (und **für HTTP**). Es schreibt sie in die `vhost_ssl.conf` der Domain (`vhost.conf` für HTTP)
unter `/var/www/vhosts/system/<domain>/conf/`; bearbeiten Sie sie im Panel oder in diesen Dateien mit anschließendem
`plesk repair web <domain> -y`, nicht in der erzeugten `httpd.conf`.

- In **Zusätzliche Direktiven für HTTPS** ergänzen oder ändern Sie überall, wo `soap.php` vorkommt (eine
  `RewriteRule`, ein `<Location>`, ein `<Files>` oder `<FilesMatch>`, das die Skripte für PHP-FPM nennt), dieselbe
  Zeile für `index_soap.php`, wie im Virtual Host oben.
- Mit eingeschaltetem **nginx-Proxy-Modus** ergänzen Sie auch den nginx-Teil in **Zusätzliche nginx-Direktiven**, falls
  er `soap.php` nennt (siehe [nginx](#nginx)); im Proxy-Modus erreichen die Anfragen Apache, sofern nginx sie nicht
  selbst beantwortet.
- Der PHP-Handler von Plesk für die Domain (PHP-FPM über Apache oder über nginx) führt jede `.php`-Datei aus, die die
  Regeln durchlassen; dort ist nichts zu ändern.

## nginx

nginx hat keine `.htaccess`. Ein Serverblock nach dem Muster des mitgelieferten Beispiels
([nginx](../../beispiele/webserver/nginx.md), `doc/eng-us/examples/web-servers/nginx.conf`) führt jeden Front-Controller
in einer eigenen `location`; das Beispiel enthält die für SOAP auskommentiert. Mit SOAP an:

```nginx
location = /index_soap.php {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/index_soap.php;
    fastcgi_param SCRIPT_NAME /index_soap.php;
    fastcgi_pass unix:/run/php-fpm/www.sock;
}
```

Ändern Sie eine vorhandene `location = /soap.php` (oder ein `location ~ ^/(index|index_rest|soap)\.php$`) genauso:
`location`, `SCRIPT_FILENAME` und `SCRIPT_NAME` nennen alle `index_soap.php`. Ein `SCRIPT_FILENAME`, das noch auf
`soap.php` zeigt, funktioniert über das Shim weiter, schreibt aber den täglichen Hinweis. Behalten Sie eine
`location = /soap.php` mit denselben drei Zeilen (alle mit `soap.php`) nur, solange alte Clients sie noch aufrufen, und
beantworten Sie sie dann mit `return 403;`. Eine `allow`/`deny`-Liste für die importierenden Installationen gehört in
die `location` von `index_soap.php`.

Prüfen und neu laden: `nginx -t && systemctl reload nginx`.

## Andere Server und Exponential Velocity

- **LiteSpeed und OpenLiteSpeed** lesen die Apache-Regeln: Ergänzen Sie `RewriteRule ^/?index_soap\.php - [L]` neben
  der Regel für `index_rest.php` (das
  [OpenLiteSpeed-Beispiel](../../../eng-us/examples/web-servers/openlitespeed-vhost.conf) enthält sie auskommentiert).
- **Caddy und FrankenPHP**: Ergänzen Sie `/index_soap.php` bei den Pfaden, die der Matcher `@front` in Ruhe lässt
  (`@front not path /index_rest.php /index_treemenu.php /index_soap.php`).
- **IIS**: eine Regel, die auf `^index_soap\.php` passt, mit der Aktion **Keine**, neben `exp-rest-direct`.
- **Exponential Velocity** führt nur `index.php`, `index_rest.php` und `index_treemenu.php` aus (`webserver.scripts`
  des Servers); `soap.php` führte es auch nie aus. Liefern Sie SOAP über das Apache- oder nginx-Frontend der
  Installation aus, das es an PHP-FPM übergibt, und lassen Sie die SOAP-Clients bei dieser Adresse.

## Die SOAP-Clients

Jedes System, das den SOAP-Server aufruft, nennt dessen Adresse; ändern Sie sie von `.../soap.php` in
`.../index_soap.php`:

- **Die Erweiterung `syndication`**, auf jeder importierenden Installation: **Syndication > Imports > (Import) >
  Bearbeiten**, das Feld für die Serveradresse, zum Beispiel `https://export.example.com/index_soap.php`. Ab
  `syndication` 1.3.14 nennt der Assistent `index_soap.php` als Beispiel, und das Dashboard und die Seite eines Feeds
  der exportierenden Installation zeigen `index_soap.php`, wenn ihr Kernel es hat (`soap.php` bei einem älteren). Eine
  exportierende Installation, die noch 6.0.14 oder älter betreibt, wird unter `soap.php` aufgerufen.
- **Middleware und Skripte**: die Endpunktadresse in ihrer Konfiguration.
- **Überwachung**: ein Check, der an den SOAP-Server sendet.

Bis alle umgestellt sind, antwortet `soap.php` weiter; der tägliche Hinweis zeigt Ihnen, dass es noch jemand aufruft.

## Testen

Führen Sie diese Befehle von einem Rechner aus, der den SOAP-Server erreichen darf, gegen den HTTPS-Namen der Website.
Ersetzen Sie `www.example.com` durch diesen Namen.

1. **Ein GET antwortet, kein 404.** Der SOAP-Server von Exponential hat keine WSDL; ein GET (mit `?wsdl` oder ohne)
   bekommt eine kurze Meldung, die beweist, dass die Anfrage den SOAP-Server erreicht hat und nicht `index.php`:

   ```bash
   curl -sS -i 'https://www.example.com/index_soap.php?wsdl'
   ```

   Erwartet: `HTTP/1.1 200` (oder `HTTP/2 200`) und der Text `Error: this web page does only understand POST methods`.
   Mit `EnableSOAP=false` ist die Antwort `200` mit leerem Inhalt. Ein `404`, die Startseite der Website oder ihr
   HTML „Seite nicht gefunden“ heißt, dass die Rewrite-Regel fehlt und die Anfrage an `index.php` ging. Der
   PHP-Quelltext im Inhalt heißt, dass das Skript nicht an PHP übergeben wurde (siehe das `FilesMatch` oben).

2. **Ein POST eines minimalen SOAP-Umschlags antwortet mit einem SOAP-Fault**, hier für eine Funktion, die es nicht
   gibt, er ändert also nichts:

   ```bash
   curl -sS -i -X POST 'https://www.example.com/index_soap.php' \
        -H 'Content-Type: text/xml; charset=utf-8' -H 'SOAPAction: ""' \
        --data-binary '<?xml version="1.0" encoding="UTF-8"?><SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"><SOAP-ENV:Body><exp:ping xmlns:exp="http://example.com/exp"/></SOAP-ENV:Body></SOAP-ENV:Envelope>'
   ```

   Erwartet: `200`, `Content-Type: text/xml` und ein Inhalt mit `<SOAP-ENV:Fault>` und `Method not found`. Das ist die
   richtige Antwort: Der Server hat den Umschlag gelesen und die Funktion nachgeschlagen. Mit Zugangsdaten
   (`-u login:passwort`) eines Kontos, das einen zweiten Anmeldeschritt braucht, ohne App-Passwort, ist die Antwort
   `401` mit `WWW-Authenticate: Basic realm="Exponential SOAP"` und einem Fault, der den Grund nennt.

3. **Der alte Name antwortet noch**, solange Sie ihn brauchen: Wiederholen Sie 1 und 2 mit `soap.php`. Die Antworten
   sind dieselben, und `var/log/warning.log` bekommt einen Hinweis `soap.php:` für den Tag.

4. **Niemand ruft den alten Namen mehr auf.** Nachdem die Clients umgestellt sind:

   ```bash
   grep -c 'soap\.php' /var/log/httpd/ssl_access_log      # Apache; nginx: /var/log/nginx/access.log
   grep -A1 'soap\.php:$' var/log/warning.log | tail -4
   ```

   Unter Plesk ist das Zugriffsprotokoll einer Domain `/var/www/vhosts/system/<domain>/logs/access_ssl_log`. Anfragen
   an `/soap.php` nach der Umstellung sind Clients, die Sie noch nicht umgestellt haben; die Zeile zeigt auch ihre
   Adresse. Die Anfragen an `/index_soap.php` sind die umgestellten.

5. **Ein echter Aufruf.** Öffnen Sie auf einer importierenden Installation den Import der Erweiterung `syndication` und
   holen Sie die Liste der exportierten Inhalte (oder führen Sie den Cronjob des Imports aus): Sie sollte die Feeds der
   exportierenden Seite wie bisher auflisten.

## Für Entwickler

- `index_soap.php` ist das Skript; `soap.php` enthält keine Logik. Ändern Sie `index_soap.php`.
- `eZSys::init()` bekommt den Namen des Skripts, das die Anfrage genannt hat (`index_soap.php` oder `soap.php` über das
  Shim), also werden die URIs und die Siteaccess-Erkennung relativ dazu gelesen.
- `expUserSessionTracker::detectMethod()` zählt eine Anmeldung über beide Namen als `soap`.
- Tests: `tests/tests/kernel/classes/expSoapEntryScriptTest.php` (ohne Datenbank) führt das Shim in einem eigenen
  PHP-Prozess neben einem Platzhalter-`index_soap.php` aus: Es leitet weiter, schreibt einen Hinweis am Tag und
  antwortet auch ohne `var/log/`.

## Verwandte Seiten

- [Das WebDAV-Einstiegsskript heißt jetzt index_webdav.php](webdav-einstiegsskript-index-webdav.md)
- [WebDAV und SOAP](../../hilfe/integratoren/webdav-und-soap.md)
- [Syndizierung zwischen Installationen](../../hilfe/integratoren/syndizierung-zwischen-installationen.md)
- [Apache .htaccess](../../beispiele/webserver/apache-htaccess.md) und [nginx](../../beispiele/webserver/nginx.md)
- [Änderungsprotokoll von Exponential 6.0.15](../../../changelogs/6.0/6.0.15.md#2026-10-29-the-soap-entry-script-is-index_soapphp)
