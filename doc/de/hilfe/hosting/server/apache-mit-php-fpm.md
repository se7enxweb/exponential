---
title: Apache mit PHP-FPM
audience: hosting, admin
category: hosting
order: 30
summary: Wie ein Hoster Exponential mit Apache httpd 2.4 und einem PHP-FPM-Pool pro Website ausliefert - die mitgelieferten Rewrite-Regeln, der virtuelle Host, HTTPS, der richtige FPM-Master zum Neuladen, Plesk und die Prüfungen vor dem Go-Live.
keywords: Apache, httpd, PHP-FPM, mod_rewrite, mod_proxy_fcgi, htaccess, .htaccess_root, .htaccess_root_static, virtueller Host, AllowOverride, mod_ssl, mod_http2, Plesk, plesk-php85-fpm, Pool, Shared Hosting, virtual host, pool, shared hosting
type: help
---
# Apache mit PHP-FPM

Diese Seite ist für Systemadministratoren bei Hosting-Unternehmen, die Exponential mit Apache httpd 2.4 und PHP-FPM ausliefern, der
klassischen Einrichtung und der üblichen auf Shared Hosting und Control Panels. Sie beantwortet: welche Module und Regeln nötig sind, wie
der virtuelle Host aussieht, wie HTTPS und der PHP-FPM-Pool hineinpassen, welchen FPM-Master Sie nach einer Änderung neu laden und wie Sie
das Ergebnis prüfen. Die Quelle ist [Kapitel 8, Apache mit PHP-FPM](../../../../en/us/install/08-serving-the-site.md#85-apache-with-php-fpm).

## Inhalt

1. [Warum Apache](#warum-apache)
2. [Schritt für Schritt](#schritt-für-schritt)
3. [Regeln in .htaccess oder im virtuellen Host](#regeln-in-htaccess-oder-im-virtuellen-host)
4. [Der PHP-FPM-Pool](#der-php-fpm-pool)
5. [Das richtige PHP-FPM neu laden](#das-richtige-php-fpm-neu-laden)
6. [Plesk](#plesk)
7. [Auslagerung des statischen Caches](#auslagerung-des-statischen-caches)
8. [Was schiefgehen kann](#was-schiefgehen-kann)
9. [Checkliste](#checkliste)

## Warum Apache

- Es braucht keinen eigenen langlebigen Prozess des Kunden und passt also zu Shared Hosting.
- Die Rewrite-Regeln werden mit Exponential als `.htaccess_root` ausgeliefert; Apache (und LiteSpeed) lesen sie direkt.
- [Unterstützung von Betriebssystemen](../../../../en/us/help/os-support.md#web-servers) führt Apache httpd 2.4 mit PHP-FPM (oder mod_php) als Stufe 1.
  PHP-FPM wird mod_php vorgezogen, das PHP an das Prefork-Modell von Apache bindet ([PHP-SAPIs](../../../../en/us/help/os-support.md#php-sapis)).
- Es betreibt Exponential auf PHP 8.0 und neuer und ist damit die Antwort auf Maschinen mit dem Standard-PHP 8.0 von Red Hat Enterprise
  Linux 9 und seinen Nachbauten.

## Schritt für Schritt

1. **Schalten Sie die Rewrite-Regeln ein.** Kopieren, nicht verschieben, damit ein Update eine neue Version bringen kann:

   ```bash
   cp .htaccess_root .htaccess
   ```

   Nutzen Sie stattdessen `.htaccess_root_static` nur, wenn die Website einen erzeugten statischen Cache ausliefert
   ([Statischer Cache](../leistung/statischer-cache.md)).

2. **Laden Sie die Module:** `mod_rewrite`, `mod_proxy` und `mod_proxy_fcgi` (Debian: `a2enmod rewrite proxy_fcgi`); für HTTPS
   `mod_ssl`, für HTTP/2 `mod_http2`.

3. **Legen Sie den virtuellen Host an** (Pfade und PHP-FPM-Socket anpassen):

   ```apache
   <VirtualHost *:80>
       ServerName example.com
       ServerAlias www.example.com
       # The installation root, where index.php is
       DocumentRoot /var/www/vhosts/example.com/httpdocs
       DirectoryIndex index.php

       <Directory /var/www/vhosts/example.com/httpdocs>
           Options FollowSymLinks
           # Lets Apache read the .htaccess created in step 1
           AllowOverride All
           Require all granted
       </Directory>

       # Every PHP script goes to the PHP-FPM pool of this site
       <FilesMatch \.php$>
           SetHandler "proxy:unix:/run/php-fpm/example.com.sock|fcgi://localhost"
       </FilesMatch>
   </VirtualHost>
   ```

4. **HTTPS.** Fügen Sie einen virtuellen Host `*:443` mit demselben Inhalt hinzu, plus:

   ```apache
       SSLEngine on
       SSLCertificateFile    /etc/ssl/example.com/fullchain.pem
       SSLCertificateKeyFile /etc/ssl/example.com/privkey.pem
       Protocols h2 http/1.1
   ```

   Beziehen Sie das Zertifikat mit certbot oder über das Panel. Sobald HTTPS funktioniert, leiten Sie Port 80 im Host `*:80` um
   (`Redirect permanent / https://example.com/`).

5. **Prüfen und neu laden:**

   ```bash
   apachectl configtest           # Syntax OK
   systemctl reload apache2       # httpd on Red Hat style systems
   curl -sI https://example.com/ | head -1
   ```

6. **Sicherheitsheader.** Der Kernel sendet sie auf seinen Seiten (`site.ini [HTTPHeaderSettings] SecurityHeaders[]`). Für statische
   Dateien fügen Sie dieselben mit `mod_headers` hinzu, wenn Sie sie auch dort wollen
   ([Kapitel 13](../../../../en/us/install/13-security-hardening.md)).

## Regeln in .htaccess oder im virtuellen Host

| | `.htaccess` | Regeln im virtuellen Host |
|---|---|---|
| Braucht | `AllowOverride All` | `AllowOverride None` |
| Kosten | eine Dateisuche pro Verzeichnis | `AllowOverride None` spart diese Suche |
| Kunde kann es ändern | ja, was zu Shared Hosting passt | nein, nur der Hoster |
| Upgrade | die neue `.htaccess_root` über `.htaccess` kopieren | die geänderten Regeln in den vhost kopieren |

Innerhalb von `<Directory>` behalten die Regeln von `.htaccess_root` ihre Form. Im Server-Kontext (außerhalb von `<Directory>`) braucht
jedes Muster einen führenden Schrägstrich, wie in [doc/en/us/examples/ezpublish.conf](../../../../en/us/examples/ezpublish.conf). Die Regelliste
dieses Beispiels ist älter als die aktuelle `.htaccess_root`; nehmen Sie die Regeln also aus `.htaccess_root` selbst
([Kapitel 8, Schritt 3](../../../../en/us/install/08-serving-the-site.md#851-step-by-step)).

> **Warnung**
> Vergleichen Sie nach jedem Upgrade Ihre kopierten Regeln mit der neuen `.htaccess_root`. Eine fehlende Regel kann Dateien offenlegen, die
> nie gesendet werden dürfen; eine hinzugefügte Regel kann genau dafür die Korrektur sein.

## Der PHP-FPM-Pool

Der Pool führt PHP als Benutzer der Website aus. Die Einstellungen, die Kapitel 8 für Exponential nennt (in der Pool-Datei oder der
`php.ini`):

| Einstellung | Empfehlung aus den Quellen |
|---|---|
| `memory_limit` | mindestens 256M in der Produktion; mehr für große Importe. Die Untergrenze des Setup-Assistenten ist 64M ([Kapitel 2](../../../../en/us/install/02-requirements.md#25-phpini-settings)) |
| `max_execution_time` | die Untergrenze des Assistenten ist 30; das Produktionsbeispiel in Kapitel 2 nutzt 60 |
| `upload_max_filesize`, `post_max_size` | der größte Upload des Kunden |
| `date.timezone` | setzen; ein fehlender Wert lässt den kritischen Test des Assistenten scheitern |
| OPcache | an, mit eingeschalteter Prüfung der Zeitstempel ([OPcache](../leistung/opcache.md)) |

Ein Pool pro Kunde, der als Benutzer dieses Kunden läuft, ist die Einheit der Trennung auf einer geteilten Maschine. Die Größe des Pools
(Prozessmanager, Anzahl der Kindprozesse, Timeouts) steht auf [PHP-FPM abstimmen](../leistung/abstimmung-von-php-fpm.md); Panels und Pools auf
[PHP-Auswahl und FPM-Pools](../control-panels/php-auswahl-und-fpm-pools.md).

## Das richtige PHP-FPM neu laden

Nach einer Änderung einer PHP-Klasse, eines Operators oder einer INI **laden Sie den FPM-Master neu, der die Website ausliefert**. Auf
Maschinen mit mehreren PHP-Versionen ist das oft nicht der, der `php-fpm` heißt.

```bash
systemctl reload php-fpm                                                   # the distribution's single PHP
php bin/php/console exp:velocity deploy --no-velocity --allow-root-user    # finds the right one, clears caches in order
php bin/php/console exp:velocity deploy --no-velocity --dry-run --allow-root-user   # shows what it found and why
```

`exp:velocity deploy` funktioniert auch auf Websites nur mit Apache: `--no-velocity` überspringt die Schritte für Velocity.
`[DeploySettings] PhpFpmService=auto` (in `settings/velocity.ini`) sucht den Pool, der nach der Hosting-Domain der Installation benannt ist
oder ihr Verzeichnis nennt, in `/opt/plesk/php/*/etc/php-fpm.d`, `/etc/php-fpm.d`, `/etc/php/*/fpm/pool.d` und
`/etc/opt/remi/php*/php-fpm.d` ([Velocity-Engines, Welches PHP-FPM](../../../../en/us/bc/6.0/velocity-engines.md#which-php-fpm)).

> **Hinweis**
> Ein Neuladen bleibt nicht auf eine Website beschränkt. Der Master bittet jeden Kindprozess jedes Pools, fertig zu werden, und Anfragen an
> andere Websites derselben PHP-Version warten währenddessen. Deshalb wartet `[DeploySettings] PhpFpmReload=auto` darauf, dass OPcache die
> geänderten Dateien neu prüft, statt neu zu laden, wenn die OPcache-Einstellungen des Pools es erlauben
> ([Velocity-Engines](../../../../en/us/bc/6.0/velocity-engines.md#deploying-a-php-change-expvelocity-deploy)).

## Plesk

Plesk erzeugt den virtuellen Host selbst; bearbeiten Sie seine Dateien nicht ([Kapitel 8, Plesk](../../../../en/us/install/08-serving-the-site.md#853-plesk)):

- Richten Sie den Document Root der Domain auf das Wurzelverzeichnis der Installation.
- Wählen Sie in den PHP-Einstellungen „FPM application served by Apache“ und die PHP-Version.
- Behalten Sie `.htaccess` (aus `.htaccess_root` kopiert); der Apache von Plesk liest sie.
- Installieren Sie das Zertifikat in Plesk und schalten Sie die Umleitung auf HTTPS ein.

Plesk benennt jeden Pool nach der Domain in `/opt/plesk/php/<version>/etc/php-fpm.d/<domain>.conf`, ausgeliefert von der Unit
`plesk-php<XY>-fpm` (zum Beispiel `plesk-php85-fpm`). `systemctl restart php-fpm` startet einen anderen Master neu, den keine
Plesk-Website nutzt. Mehr auf [Plesk](../control-panels/plesk.md).

## Auslagerung des statischen Caches

Mit `.htaccess_root_static` oder den Regeln aus [doc/en/us/examples/staticcache_offload.conf](../../../../en/us/examples/staticcache_offload.conf)
im vhost der **öffentlichen** Website beantwortet Apache eine erzeugte statische Seite aus ihrer Datei, ohne PHP zu starten. Fügen Sie
diese Regeln nie dem vhost der Administration hinzu. Siehe [Statischer Cache](../leistung/statischer-cache.md).

## Was schiefgehen kann

| Symptom | Ursache | Abhilfe |
|---|---|---|
| 404 für jede Seite | `.htaccess` wird nicht gelesen (`AllowOverride`) oder `mod_rewrite` ist aus | Overrides erlauben, `mod_rewrite` laden |
| 404 für jedes Asset unter `design/` | der Document Root ist nicht das Wurzelverzeichnis der Installation | `DocumentRoot` auf das Verzeichnis mit `index.php` richten |
| 500 | ein PHP- oder Berechtigungsfehler | die Fehlerlogs von Apache und PHP-FPM lesen, dann `var/log/error.log` |
| 200 für `/.git/config` oder einen `.php`-Pfad des Kernels | Regeln nicht aktiv | beheben, bevor die Website öffentlich ist |
| Eine Änderung scheint nicht zu wirken | der neu geladene FPM-Master ist nicht der, der die Website ausliefert | `exp:velocity deploy --no-velocity --dry-run` |
| Dateien in `var/` gehören root | ein Konsolenskript wurde als root ausgeführt | Skripte als Benutzer der Website ausführen ([Kapitel 8, Ein Benutzer oder eine Gruppe](../../../../en/us/install/08-serving-the-site.md#872-one-user-or-one-group)) |

## Checkliste

- [ ] `.htaccess` aus `.htaccess_root` kopiert (oder die Regeln im vhost), `mod_rewrite` und `mod_proxy_fcgi` geladen.
- [ ] Document Root ist das Wurzelverzeichnis der Installation.
- [ ] Ein PHP-FPM-Pool pro Website, als Benutzer der Website; OPcache an.
- [ ] HTTPS mit einem CA-Zertifikat; Port 80 umgeleitet, sobald HTTPS funktioniert.
- [ ] Die vier Prüfungen des Routings aus [Webserver und Engines](README.md#was-jede-einrichtung-gemeinsam-hat) bestehen.
- [ ] Der Support weiß, welche FPM-Unit die Website ausliefert (`exp:velocity deploy --no-velocity --dry-run`).

**Demomodus mit Apache und PHP-FPM.** Für eine öffentliche Demo erreicht `SetEnv EXP_DEMO_MODE 1` in ihrem virtuellen Host oder ihrer `.htaccess` PHP-FPM als FastCGI-Parameter über `mod_proxy_fcgi`, den `getenv()` liest; `env[EXP_DEMO_MODE] = 1` im Pool der Demo tut dasselbe. [Beispiele für den Demomodus](../../../../en/us/examples/demo-mode/README.md), [Demomodus für Hoster](../sicherheit/demomodus-fuer-hoster.md).

## Verwandte Seiten

- [Einen Webserver wählen](einen-webserver-waehlen.md)
- [nginx mit PHP-FPM](nginx-mit-php-fpm.md)
- [PHP-FPM abstimmen](../leistung/abstimmung-von-php-fpm.md)
- [Benutzer und Pools pro Kunde](../sicherheit/benutzer-und-pools-pro-kunde.md)
- [Kapitel 8: Die Website ausliefern](../../../../en/us/install/08-serving-the-site.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
