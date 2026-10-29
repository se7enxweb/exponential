# Das WebDAV-Einstiegsskript heißt jetzt index_webdav.php

Ab 6.0.15 ist der WebDAV-Server von Exponential das Skript **`index_webdav.php`** im Wurzelverzeichnis der
Installation. Bis 6.0.14 hieß es `webdav.php`. `webdav.php` wird weiter mitgeliefert, aber nur noch als
weiterleitender Platzhalter (Shim): Es bindet `index_webdav.php` ein und schreibt einmal am Tag eine Warnung. **Es wird
in einer späteren Hauptversion entfernt.**

Lesen Sie diese Seite, wenn WebDAV irgendwo eingeschaltet ist (`settings/webdav.ini [GeneralSettings]
EnableWebDAV=true`) oder wenn ein virtueller Host, eine Rewrite-Regel, eine Firewall-Regel, eine Überwachung oder ein
Lesezeichen eines Clients `webdav.php` nennt. Ist WebDAV überall aus und nennt nichts das Skript, ändert sich für Sie
nichts.

## Inhalt

1. [Was sich geändert hat](#was-sich-geändert-hat)
2. [Warum](#warum)
3. [Kompatibilität: webdav.php leitet bis zu einer späteren Hauptversion weiter](#kompatibilität-webdavphp-leitet-bis-zu-einer-späteren-hauptversion-weiter)
4. [Was Sie ändern müssen](#was-sie-ändern-müssen)
5. [Apache: .htaccess](#apache-htaccess)
6. [Apache: die virtuellen Hosts, HTTP und HTTPS](#apache-die-virtuellen-hosts-http-und-https)
7. [Plesk: Zusätzliche Apache-Anweisungen](#plesk-zusätzliche-apache-anweisungen)
8. [nginx](#nginx)
9. [Microsoft IIS: web.config](#microsoft-iis-webconfig)
10. [Velocity und FrankenPHP](#velocity-und-frankenphp)
11. [WebDAV-Clients](#webdav-clients)
12. [Testen](#testen)
13. [Prüfen, dass der alte Name nicht mehr verwendet wird](#prüfen-dass-der-alte-name-nicht-mehr-verwendet-wird)
14. [Die Anmeldung ändert sich nicht](#die-anmeldung-ändert-sich-nicht)
15. [Für Entwickler](#für-entwickler)

## Was sich geändert hat

| | Bis 6.0.14 | Ab 6.0.15 |
|---|---|---|
| Der WebDAV-Server | `webdav.php` | `index_webdav.php` |
| `webdav.php` | der Server | ein Shim: `require __DIR__ . '/index_webdav.php';`, eine Warnung am Tag in `warning.log` |
| Adresse auf dem Haupthost | `https://www.example.com/webdav.php/<siteaccess>/` | `https://www.example.com/index_webdav.php/<siteaccess>/` |
| Adresse auf einem eigenen Host | `https://webdav.example.com/<siteaccess>/` | unverändert; nur die Rewrite-Regel dahinter ändert sich |
| `.htaccess_root`, `.htaccess_root_static` | keine WebDAV-Regel | eine auskommentierte Regel `#RewriteRule ^index_webdav\.php - [L]` zum Einschalten |

Alles andere bleibt, wie es war: `settings/webdav.ini`, das Inhalts-Backend in `kernel/private/classes/webdav/`, die
Berechtigungen, das Protokoll `var/log/webdav.log` (mit `[GeneralSettings] Logging=enabled`).

## Warum

Jedes andere Einstiegsskript im Wurzelverzeichnis heißt `index_<was>.php`: `index.php`, `index_rest.php`,
`index_treemenu.php`, `index_cluster.php`. Nur WebDAV und SOAP wichen davon ab. Ein Muster heißt eine Regel für die
Freigabelisten des Webservers, die entscheiden, welche PHP-Dateien im Wurzelverzeichnis überhaupt laufen dürfen:
`index(_[a-z]+)?\.php` deckt jeden Web-Einstiegspunkt ab, und eine Härtungsregel für die Front-Controller vergisst
WebDAV nicht mehr und lässt es auch nicht versehentlich durch. (Der Vorgänger von Exponential nannte das Skript
ebenfalls `index_webdav.php`, bevor Version 3.2 es umbenannte.)

## Kompatibilität: webdav.php leitet bis zu einer späteren Hauptversion weiter

- `webdav.php` enthält nur eine Konstante, die es selbst nennt, und `require __DIR__ . '/index_webdav.php';`. Eine
  Anfrage an `webdav.php` wird genau wie bisher beantwortet, mit denselben Pfaden (`/webdav.php/<siteaccess>/...`),
  sodass bestehende virtuelle Hosts, Rewrite-Regeln und eingebundene Laufwerke nach dem Upgrade weiter funktionieren.
- Die erste Anfrage über `webdav.php` an einem Tag schreibt eine Zeile in `warning.log` (das Protokollverzeichnis der
  Site, `var/log/` oder `var/<site>/log/`):

  ```text
  WebDAV request through webdav.php, the old name of the WebDAV entry script. webdav.php only forwards to
  index_webdav.php and will be removed in a later major version: change the virtual host and rewrite rules ...
  ```

  Der Tag steht in `webdav-entry-script-deprecated.stamp` neben dem Protokoll, sodass eine viel genutzte WebDAV-Freigabe
  das Protokoll nicht füllt. Die Zeile wird nur geschrieben, solange WebDAV eingeschaltet ist.
- **`webdav.php` wird in einer späteren Hauptversion entfernt.** Danach schickt eine Regel, die es noch nennt,
  WebDAV-Anfragen an eine Datei, die es nicht gibt. Ändern Sie die Regeln jetzt, solange der Shim noch antwortet.

## Was Sie ändern müssen

Gehen Sie diese Liste auf **jedem** Server und in **jeder** Konfigurationsschicht durch. Die Rewrite-Regeln werden am
häufigsten vergessen, und von ihnen der HTTPS-Host: Viele Einrichtungen haben einen `<VirtualHost *:80>`, der nur
weiterleitet, und einen `<VirtualHost *:443>`, der die eigentliche Arbeit macht, und nur der erste wird bearbeitet.

1. `.htaccess` im Wurzelverzeichnis der Installation (wenn Apache sie liest).
2. Der virtuelle Apache-Host für HTTP (`*:80`) **und** der für HTTPS (`*:443`), auch ein eigener WebDAV-Host wie
   `webdav.example.com`.
3. Plesk: **Zusätzliche Apache-Anweisungen** für HTTP **und** für HTTPS (`vhost.conf` und `vhost_ssl.conf`) und jede
   Datei, die sie einbinden (etwa eine gemeinsame Schutzdatei für das Wurzelverzeichnis mit einer Freigabeliste der
   Skripte).
4. nginx: die `location`, die das Skript trifft, und ihre `fastcgi_param SCRIPT_FILENAME` und `SCRIPT_NAME`.
5. IIS: die Rewrite-Regeln in `web.config`.
6. Alles andere, das die Datei nennt: eine Firewall- oder WAF-Regel, ein Health-Check des Load Balancers, eine
   Überwachung, ein Ausschluss in der Sicherung, eine Freigabeliste in einer Härtungsdatei.
7. WebDAV-Clients, die die Adresse des Haupthosts verwenden (`/webdav.php/...`).

Dann [testen](#testen) und [prüfen, dass der alte Name nicht mehr verwendet wird](#prüfen-dass-der-alte-name-nicht-mehr-verwendet-wird).

## Apache: .htaccess

`.htaccess_root` (die Datei, die Sie nach `.htaccess` kopieren) schickt jede Anfrage, die kein aufgeführtes Asset ist,
an `index.php`. Eine Regel für WebDAV hatte sie nie, WebDAV auf dem Haupthost brauchte also eine eigene Regel. Ab
6.0.15 trägt die Datei sie mit, auskommentiert, direkt nach der Regel für das Baummenü:

```apache
# Vorher (eine selbst hinzugefügte Regel)
RewriteRule ^webdav\.php - [L]

# Nachher (die mitgelieferte Zeile in .htaccess einkommentieren)
RewriteRule ^index_webdav\.php - [L]
```

Die Regel muss **vor** der letzten Regel stehen (`RewriteRule .* index.php`), wie die Regel für `index_rest.php`.
Führen Sie `.htaccess_root` nach dem Upgrade wie gewohnt mit `.htaccess` zusammen, und behalten Sie eine WebDAV-Regel
nur, wenn WebDAV auf diesem Host verwendet wird.

## Apache: die virtuellen Hosts, HTTP und HTTPS

Im Serverkontext (ein `<VirtualHost>` ohne `<Directory>`) beginnt das Muster einer `RewriteRule` mit einem
Schrägstrich.

**WebDAV auf dem Haupthost**, neben der Site. Im Block `<VirtualHost *:443>`, vor der Regel, die alles an `index.php`
schickt:

```apache
# Vorher
RewriteRule ^/webdav\.php - [L]

# Nachher
RewriteRule ^/index_webdav\.php - [L]
```

In einem Block `<Directory /var/www/example.com>` (wie im mitgelieferten
[Beispiel für einen virtuellen Apache-Host](../../beispiele/webserver/apache-virtueller-host.md)) hat das Muster
keinen führenden Schrägstrich: `RewriteRule ^index_webdav\.php - [L]`.

**WebDAV auf einem eigenen Host** (empfohlen: Der WebDAV-Host braucht dann keine andere Regel und lässt sich nach
Adressen beschränken). Jede Anfrage geht an das Skript; der Pfad des Clients (`/<siteaccess>/...`) bleibt in
`REQUEST_URI` erhalten:

```apache
# Vorher
<VirtualHost *:443>
    ServerName webdav.example.com
    DocumentRoot /var/www/example.com
    SSLEngine on
    SSLCertificateFile    /etc/ssl/example.com/fullchain.pem
    SSLCertificateKeyFile /etc/ssl/example.com/privkey.pem
    RewriteEngine On
    RewriteRule . /webdav.php [L]
    <FilesMatch "\.php$">
        SetHandler "proxy:unix:/run/php-fpm/www.sock|fcgi://localhost/"
    </FilesMatch>
</VirtualHost>

# Nachher: nur das Ziel der Umschreibung ändert sich
<VirtualHost *:443>
    ServerName webdav.example.com
    DocumentRoot /var/www/example.com
    SSLEngine on
    SSLCertificateFile    /etc/ssl/example.com/fullchain.pem
    SSLCertificateKeyFile /etc/ssl/example.com/privkey.pem
    RewriteEngine On
    RewriteRule . /index_webdav.php [L]
    <FilesMatch "\.php$">
        SetHandler "proxy:unix:/run/php-fpm/www.sock|fcgi://localhost/"
    </FilesMatch>
</VirtualHost>
```

Antwortet derselbe Host auch auf Port 80 mit WebDAV (nur in einem geschlossenen Netz: Basic-Authentifizierung sendet
das Passwort lesbar), ändern Sie den Block `<VirtualHost *:80>` ebenso. Leitet Port 80 nur auf HTTPS weiter, gibt es
darin nichts zu ändern, und **der Block `*:443` ist der entscheidende.**

Entscheidet eine Freigabeliste, welche Dateien im Wurzelverzeichnis laufen dürfen (eine Regel wie
`RewriteCond %{REQUEST_URI} !^/(index(_rest|_treemenu|_cluster)?\.php|soap\.php|webdav\.php)$`), nehmen Sie
`index_webdav.php` auf: `index(_rest|_treemenu|_cluster|_webdav)?\.php`. Lassen Sie `webdav\.php` in der Liste, bis
jeder Client und jede Regel umgestellt ist, und nehmen Sie es dann heraus.

Prüfen mit `apachectl configtest` (`httpd -t`), neu laden mit `systemctl reload apache2` (`httpd` auf Systemen im
Red-Hat-Stil).

## Plesk: Zusätzliche Apache-Anweisungen

Plesk schreibt das Feld **Zusätzliche Anweisungen für HTTP** nach `vhost.conf` und **Zusätzliche Anweisungen für
HTTPS** nach `vhost_ssl.conf` (Websites & Domains > die Domain > Apache- & nginx-Einstellungen). Die beiden Felder sind
getrennt: Eine Regel im HTTP-Feld bewirkt nichts für `https://`-Anfragen. Ändern Sie die WebDAV-Regel in **beiden**
Feldern, an derselben Stelle wie bei einem einfachen Apache (vor der Regel, die alles an `index.php` schickt):

```apache
# Vorher
RewriteRule ^webdav\.php - [L]

# Nachher
RewriteRule ^index_webdav\.php - [L]
```

(Diese Felder gelten innerhalb des `<Directory>` der Domain, daher hat das Muster keinen führenden Schrägstrich.)
Binden die Felder eine gemeinsame Datei mit `Include` ein, ändern Sie auch diese. Läuft unter Plesk nginx als Proxy
vor Apache, muss auf der nginx-Seite nichts geändert werden, außer **Zusätzliche nginx-Anweisungen** nennen
`webdav.php`.

## nginx

Das Skript wird zweimal genannt: in der `location`, die es trifft, und in den Parametern für PHP-FPM. Ändern Sie
beides.

```nginx
# Vorher
location ^~ /webdav.php {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/webdav.php;
    fastcgi_param SCRIPT_NAME /webdav.php;
    fastcgi_pass unix:/run/php-fpm/www.sock;
}

# Nachher
location ^~ /index_webdav.php {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/index_webdav.php;
    fastcgi_param SCRIPT_NAME /index_webdav.php;
    fastcgi_pass unix:/run/php-fpm/www.sock;
}
```

Für einen eigenen WebDAV-Host (`server_name webdav.example.com;`) geht jede Anfrage an das Skript:

```nginx
location / {
    include fastcgi_params;
    fastcgi_param SCRIPT_FILENAME $document_root/index_webdav.php;
    fastcgi_param SCRIPT_NAME /index_webdav.php;
    fastcgi_pass unix:/run/php-fpm/www.sock;
}
```

`SCRIPT_FILENAME` muss die Datei nennen, die wirklich läuft: Exponential ermittelt daraus das Wurzelverzeichnis der
Installation. Ein `SCRIPT_FILENAME`, das noch `webdav.php` sagt, funktioniert über den Shim weiter (und schreibt die
tägliche Warnung); eines, das `index_webdav.php` nennt, während `SCRIPT_NAME` `webdav.php` sagt, ist widersprüchlich:
Ändern Sie beide zusammen. Prüfen mit `nginx -t`, neu laden mit `systemctl reload nginx`.

## Microsoft IIS: web.config

Das mitgelieferte [IIS-Beispiel](../../beispiele/webserver/iis.md) hat keine WebDAV-Regel. Haben Sie eine
hinzugefügt, ändern Sie ihr Muster und ihr Ziel:

```xml
<!-- Vorher -->
<rule name="exp-webdav" stopProcessing="true">
  <match url="^webdav\.php" />
  <action type="None" />
</rule>

<!-- Nachher -->
<rule name="exp-webdav" stopProcessing="true">
  <match url="^index_webdav\.php" />
  <action type="None" />
</rule>
```

Auf einer eigenen WebDAV-Site schickt die Regel alles an das Skript: `<match url=".*" />` mit
`<action type="Rewrite" url="index_webdav.php" />`. Das eigene WebDAV-Modul von IIS muss für diese Site aus bleiben,
sonst beantwortet es die WebDAV-Methoden, bevor PHP sie sieht.

## Velocity und FrankenPHP

Die Exponential-Voreinstellung von Velocity führt nur `index.php`, `index_rest.php` und `index_treemenu.php` aus; eine
Anfrage nach einer anderen `.php`-Datei beantwortet `index.php`. Das galt für `webdav.php` und gilt für
`index_webdav.php`: **Weder Velocity noch die FrankenPHP-Konfiguration, die `exp:velocity` schreibt, liefert WebDAV
aus.** Liefern Sie WebDAV über Apache oder nginx mit PHP-FPM aus, am besten auf einem eigenen Host wie
`webdav.example.com`, neben Velocity für die Site. Nichts in `settings/velocity.ini` nennt das WebDAV-Skript, dort ist
also nichts zu ändern.

## WebDAV-Clients

- Ein Client, der auf einem eigenen Host eingebunden ist (`https://webdav.example.com/<siteaccess>/`), behält seine
  Adresse.
- Ein Client, der auf dem Haupthost eingebunden ist (`https://www.example.com/webdav.php/<siteaccess>/`), funktioniert
  über den Shim weiter. Binden Sie ihn unter `https://www.example.com/index_webdav.php/<siteaccess>/` neu ein, bevor
  `webdav.php` entfernt wird. Die Adressen in den Antworten des Servers (`href`) folgen dem Namen, den der Client
  verwendet hat.

## Testen

Führen Sie diese Befehle von einem Rechner außerhalb des Servers aus, gegen die Adresse, die die Clients verwenden.
Ersetzen Sie `www.example.com`, den Siteaccess (unten `site`) und den Benutzer. Auf einem eigenen Host lassen Sie
`/index_webdav.php` weg.

**1. OPTIONS: Der Server antwortet als WebDAV.**

```bash
curl -i -X OPTIONS https://www.example.com/index_webdav.php/
```

Erwartet: `HTTP/1.1 200 OK` mit den Kopfzeilen `DAV: 1` und
`Allow: GET, HEAD, PROPFIND, PROPPATCH, OPTIONS, DELETE, COPY, MOVE, MKCOL, PUT, LOCK, UNLOCK`. Eine HTML-Seite oder
eine fehlende `DAV`-Kopfzeile heißt, dass die Anfrage `index.php` erreicht hat: Die Rewrite-Regel fehlt oder steht nach
der Auffangregel. `404` mit dem Text `WebDAV functionality is disabled!` heißt, dass die Regel greift und
`EnableWebDAV` für diese Installation nicht `true` ist.

**2. PROPFIND auf die Wurzel: die Liste der Siteaccesses, ohne Anmeldung.**

```bash
curl -i -X PROPFIND -H 'Depth: 1' https://www.example.com/index_webdav.php/
```

Erwartet: `HTTP/1.1 207 Multi-Status` und ein XML-`<D:multistatus>`, dessen `<D:href>`-Werte mit
`https://www.example.com/index_webdav.php/` beginnen.

**3. PROPFIND auf einen Siteaccess: Die Anmeldung wird verlangt.**

```bash
curl -i -X PROPFIND -H 'Depth: 1' https://www.example.com/index_webdav.php/site/
```

Erwartet: `HTTP/1.1 401 Unauthorized` mit `WWW-Authenticate: Basic realm="..."`. `404` heißt, dass der Siteaccess
nicht in `site.ini [SiteSettings] SiteList[]` steht.

**4. PROPFIND mit einem Benutzer: der Inhalt.**

```bash
curl -i -u 'webdav-user' -X PROPFIND -H 'Depth: 1' https://www.example.com/index_webdav.php/site/
```

curl fragt nach dem Passwort. Erwartet: `207 Multi-Status` mit den Ordnern unter dem WebDAV-Startknoten. `403` heißt,
dass dem Benutzer `user/login` für diesen Siteaccess fehlt.

**5. Ein echter Client.** Binden Sie die Adresse in dem Client ein, den Ihre Benutzer verwenden, und öffnen, speichern
und löschen Sie eine Testdatei:

- `cadaver https://www.example.com/index_webdav.php/site/` (Linux-Kommandozeile), dann `ls`, `put test.txt`,
  `rm test.txt`;
- Windows Explorer: **Netzlaufwerk verbinden** mit `https://www.example.com/index_webdav.php/site/`;
- macOS Finder: **Gehe zu > Mit Server verbinden** mit derselben Adresse;
- GNOME Dateien und KDE Dolphin: `davs://www.example.com/index_webdav.php/site/`.

Wiederholen Sie Test 1 einmal gegen `https://www.example.com/webdav.php/`: Solange die alten Regeln da sind, sollte er
gleich antworten (über den Shim); nachdem Sie sie bewusst entfernt haben, sollte er WebDAV nicht mehr erreichen.

## Prüfen, dass der alte Name nicht mehr verwendet wird

1. **Das Zugriffsprotokoll des Webservers**: Nach der Änderung keine neuen WebDAV-Anfragen an `webdav.php`.

   ```bash
   grep -c 'webdav\.php' /var/log/apache2/example.com-ssl_access.log
   grep 'webdav\.php' /var/log/nginx/access.log | tail
   ```

   Sehen Sie auch in das HTTPS-Protokoll, nicht nur in das für HTTP. Anfragen, die noch ankommen, zeigen Ihnen, welcher
   Client (Adresse, User-Agent) neu eingebunden werden muss.
2. **`warning.log` von Exponential**: keine neue Zeile `WebDAV request through webdav.php`.

   ```bash
   grep 'old name of the WebDAV entry script' var/log/warning.log | tail -3
   ```

   Mit einem Protokollverzeichnis pro Site (`site.ini [FileSettings] UseGlobalLogDir=disabled`) ist die Datei
   `var/<site>/log/warning.log`.
3. **Die Stempeldatei** `webdav-entry-script-deprecated.stamp` im selben Verzeichnis enthält den letzten Tag, an dem
   eine Anfrage über `webdav.php` kam. Ein Datum vor Ihrer Änderung heißt, dass seitdem nichts den alten Namen
   verwendet hat.

Bleiben alle drei eine Weile ruhig, nehmen Sie `webdav.php` aus jeder Freigabeliste und Regel heraus.

## Die Anmeldung ändert sich nicht

Die Umbenennung ändert keine Anmelderegel. WebDAV meldet mit Basic-Authentifizierung gegen die Exponential-Benutzer an
(nur über HTTPS ausliefern), die Richtlinien des Benutzers gelten, und `user/login` für den Siteaccess ist nötig.
WebDAV fragt standardmäßig nie nach einem zweiten Anmeldeschritt (Zwei-Faktor-Authentifizierung) und nie nach
„Identität bestätigen“: Der zweite Schritt wird nur verlangt, wo eine Site WebDAV ausdrücklich einbezieht
(`Guard[]=webdav`), siehe [zweiter Anmeldeschritt auf anderen Anmeldewegen](../../funktionen/6.0/zweiter-schritt-auf-anderen-anmeldewegen.md).
Eine Anmeldung über einen der beiden Namen wird als WebDAV-Sitzung erfasst.

## Für Entwickler

- `expWebDAVEntryScript` (`kernel/private/classes/webdav/expwebdaventryscript.php`) kennt beide Namen: `current()`
  gibt das Skript zurück, über das die Anfrage kam (nötig für `eZSys::init()`, das das Wurzelverzeichnis aus
  `SCRIPT_FILENAME` ermittelt), `isEntryScript()` und `isRootRequest()` nehmen beide an, `noteDeprecatedEntry()`
  schreibt die tägliche Warnung.
- Der Shim definiert `EXP_WEBDAV_ENTRY_SCRIPT` als `webdav.php`, bevor er `index_webdav.php` einbindet.
- `expUserSessionTracker::detectMethod()` erfasst eine Anmeldung über `index_webdav.php` oder `webdav.php` als
  `webdav`.
- Tests: `tests/tests/kernel/private/expWebDAVEntryScriptTest.php` (ohne Datenbank).

## Verwandte Seiten

- [WebDAV und SOAP](../../hilfe/integratoren/webdav-und-soap.md): WebDAV einschalten, Berechtigungen, bekannte Lücken
- [Wie arbeite ich über WebDAV mit Inhalten?](../../hilfe/handbuch/wie-arbeite-ich-ueber-webdav-mit-inhalten.md)
- [Die Website ausliefern](../../installation/08-die-website-ausliefern.md) und die [Webserver-Beispiele](../../beispiele/webserver/README.md)
- [Verhaltensänderungen vom 1. und 2. Oktober 2026 und später](verhaltensaenderungen-2026-10.md)
- [Exponential 6.0.15](../../../changelogs/6.0/6.0.15.md), Abschnitt Upgrading
