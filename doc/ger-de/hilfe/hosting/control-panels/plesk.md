---
title: Plesk
audience: hosting, admin
category: hosting
order: 20
summary: Exponential auf Plesk betreiben, dem Panel des eigenen Entwicklungsservers des Projekts (AlmaLinux 9, Stufe 1) - die PHP-Builds von Plesk und die PHP-FPM-Pools pro Domain, die Dienste plesk-phpXY-fpm, die Einstellungen der Domain, nginx vor Apache, Zertifikate, Logs, Cron mit dem PHP der Domain und Velocity neben Plesk.
keywords: Plesk, AlmaLinux 9, /opt/plesk/php, plesk-php85-fpm, PHP-FPM-Pool, FPM application served by Apache, Tools & Settings, PHP Settings, Additional configuration directives, Let's Encrypt, /usr/local/psa/var/certificates, access_ssl_log, mod_remoteip, Firewall, Velocity, exp:velocity deploy, PHP-FPM pool, firewall
type: help
---
# Plesk

Diese Seite ist für Hosting-Anbieter und Administratoren, die Exponential auf Servern betreiben, die von Plesk verwaltet werden. Plesk
ist das Panel, das das Projekt selbst nutzt: Sein Entwicklungsserver ist AlmaLinux 9 mit Plesk, was Plesk auf AlmaLinux 9 zu
**Stufe 1** macht, jeden Tag getestet ([Stufe 1](../../betriebssystemunterstuetzung.md#stufe-1-hier-getestet)). Die Seite beantwortet, wie PHP und Pools
von Plesk aufgebaut sind, wie Sie eine Domain für Exponential einrichten, was Sie nach einer Änderung neu laden, wie Zertifikate, Logs
und Cron dort funktionieren und wie Velocity neben Plesk läuft.

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
> [Plesk](plesk.md), [cPanel](cpanel.md), [DirectAdmin](directadmin.md), [ISPConfig](ispconfig.md).
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../../anleitungen/seiten-cache-mit-litespeed.md).

Jedes Detail zu Plesk auf dieser Seite stammt aus den im Text zitierten Quellen von Exponential. Für alles andere in Plesk lesen Sie
in der Dokumentation von Plesk nach.

## Inhalt

1. [Was auf dem Entwicklungsserver läuft](#was-auf-dem-entwicklungsserver-läuft)
2. [Das PHP von Plesk: Versionen, Erweiterungen, Kommandozeile](#das-php-von-plesk-versionen-erweiterungen-kommandozeile)
3. [Eine Domain einrichten](#eine-domain-einrichten)
4. [PHP-FPM-Pools und der richtige Dienst](#php-fpm-pools-und-der-richtige-dienst)
5. [nginx vor Apache](#nginx-vor-apache)
6. [Zertifikate](#zertifikate)
7. [Logs](#logs)
8. [Cron](#cron)
9. [Velocity neben Plesk](#velocity-neben-plesk)
10. [Bekannte Auswirkungen von Plesk-Einstellungen](#bekannte-auswirkungen-von-plesk-einstellungen)
11. [Checkliste](#checkliste)

## Was auf dem Entwicklungsserver läuft

AlmaLinux 9 (x86_64, RHEL-9-Familie): der Entwicklungsserver des Projekts, mit Apache 2.4 und PHP-FPM 8.5 aus dem Plesk-Panel und
Exponential Velocity auf derselben Installation; dort laufen Websites mit SQLite, MariaDB, MongoDB und Oracle (in einem Container),
und ein PostgreSQL-Server steht zur Verfügung ([Stufe 1](../../betriebssystemunterstuetzung.md#stufe-1-hier-getestet)). Plesk-Hosts auf den anderen
Distributionen der Stufe 2 sind Stufe 2 ([Stufe 2](../../betriebssystemunterstuetzung.md#stufe-2-bekanntermaßen-lauffähig)).

## Das PHP von Plesk: Versionen, Erweiterungen, Kommandozeile

Aus [Kapitel 2, Plesk](../../../installation/02-voraussetzungen.md#plesk):

- Plesk installiert jede PHP-Version unter `/opt/plesk/php/<version>/` (zum Beispiel `/opt/plesk/php/8.5/bin/php`) neben dem PHP des
  Systems. Eine Domain nutzt die Version, die in ihren Hosting-Einstellungen gewählt ist; das `php` der Kommandozeile ist das des
  Systems. Führen Sie Composer und die Konsole mit dem PHP der Domain aus.
- Die gebündelten Erweiterungen kommen mit dem PHP von Plesk und werden unter **Tools & Settings > PHP Settings** eingeschaltet;
  PECL-Erweiterungen werden mit `/opt/plesk/php/<version>/bin/pecl install <extension>` installiert.

```bash
/opt/plesk/php/8.5/bin/php -v
/opt/plesk/php/8.5/bin/php -m
/opt/plesk/php/8.5/bin/php bin/php/console exp:velocity ext check
```

> **Warnung**
> Das `php` im `PATH` ist das PHP des Systems, nicht das der Domain. Composer, mit dem falschen PHP ausgeführt, wählt Paketversionen,
> die das Web-PHP nicht ausführen kann, und eine fehlende Erweiterung zeigt sich dann als weiße Seite
> ([Kommandozeile und Web-PHP](../../../installation/02-voraussetzungen.md#php-auf-der-kommandozeile-und-im-web)). Rufen Sie für Composer, die Konsole,
> die Installer und Cron immer das PHP der Domain über seinen Pfad auf.

## Eine Domain einrichten

Aus [8.5.3 Plesk](../../../installation/08-die-website-ausliefern.md#853-plesk): Plesk erzeugt den virtuellen Host selbst; bearbeiten Sie seine
Dateien nicht. In Plesk:

- richten Sie den Document Root der Domain auf das Wurzelverzeichnis der Installation (das Verzeichnis mit `index.php`);
- wählen Sie in den PHP-Einstellungen „FPM application served by Apache“ und die PHP-Version;
- behalten Sie `.htaccess` (aus `.htaccess_root` kopiert); das Apache von Plesk liest sie;
- installieren Sie das Zertifikat in Plesk (seine Let's-Encrypt-Erweiterung oder Ihr eigenes) und schalten Sie die Umleitung auf
  HTTPS ein.

```bash
cd /var/www/vhosts/<domain>/<docroot>
cp .htaccess_root .htaccess
```

Oder lassen Sie die Konsole kopieren und bringen Sie `config.php` auf dieselbe Weise an ihren Platz. Im **SSH-Terminal**
von Plesk (oder per SSH-Anmeldung) als Systembenutzer des Abonnements, mit dem PHP der Domain:

```bash
cd /var/www/vhosts/<domain>/<docroot>
/opt/plesk/php/<version>/bin/php bin/php/console exp:setup:status
/opt/plesk/php/<version>/bin/php bin/php/console exp:setup:htaccess --apply    # .htaccess_root -> .htaccess
/opt/plesk/php/<version>/bin/php bin/php/console exp:setup:config --apply      # config.php-RECOMMENDED -> config.php
```

Die Dateien gehören dann dem Benutzer des Abonnements, wie alles andere im Docroot. Als root braucht jeder Befehl
`--allow-root-user`; eine neue Datei erhält den Eigentümer der Vorlage (niemals root), und
`--owner=<abonnement-benutzer>:psacln` setzt ihn ausdrücklich. Ohne `--apply` wird nichts geschrieben, und eine
vorhandene, abweichende Datei wird als Diff gezeigt und behalten, solange Sie nicht `--force` angeben (die alte kommt
vorher nach `var/backup/setup/<zeit>/`). Der **Dateimanager** von Plesk kann die einfache Kopie ebenfalls anlegen,
übernimmt aber den Modus nicht und zeigt keinen Diff. Alles dazu:
[.htaccess und config.php von der Konsole einrichten](../../../anleitungen/setup-dateien-von-der-konsole.md).

Die PHP-Werte, die der Setup-Assistent prüft (`memory_limit` 64M oder mehr, `max_execution_time` 30 oder mehr, `date.timezone`,
`file_uploads`, `allow_url_fopen`), werden in den PHP-Einstellungen der Domain gesetzt
([2.5](../../../installation/02-voraussetzungen.md#25-einstellungen-in-der-phpini)). Die Notizen zur MongoDB-Portierung setzen FPM-Direktiven wie
`request_terminate_timeout` in den PHP-Einstellungen der Domain, im Feld **Additional configuration directives** (Plesk → Domains →
Ihre Domain → PHP Settings) ([MongoDB-Unterstützung im Kernel](../../../bc/6.0/mongodb-kernel-unterstuetzung.md)).

## PHP-FPM-Pools und der richtige Dienst

- Jede Domain hat ihren eigenen PHP-FPM-Pool in `/opt/plesk/php/<version>/etc/php-fpm.d/<domain>.conf`, betrieben vom Dienst
  `plesk-php<XY>-fpm` (zum Beispiel `plesk-php85-fpm`), nicht von `php-fpm`. Laden Sie nach einer Änderung an PHP diesen neu
  ([Kapitel 2, Plesk](../../../installation/02-voraussetzungen.md#plesk)).
- `systemctl restart php-fpm` startet einen anderen Master neu, den keine Plesk-Website nutzt
  ([8.5.3](../../../installation/08-die-website-ausliefern.md#853-plesk)).

```bash
ls /opt/plesk/php/*/etc/php-fpm.d/ | grep <domain>     # which PHP version serves the domain
systemctl reload plesk-php85-fpm                        # graceful, no dropped requests
```

`exp:velocity deploy` findet den richtigen Dienst selbst: `settings/velocity.ini [DeploySettings] PhpFpmService=auto` erkennt die
Pool-Konfiguration, die nach der Hosting-Domain der Installation benannt ist (`/var/www/vhosts/<domain>/...`, wie Plesk Pools benennt);
`--dry-run` zeigt, was gefunden wurde und warum ([Engines von Velocity](../../../bc/6.0/velocity-engines.md)). Um ihn ausdrücklich
festzulegen:

```ini
# settings/override/velocity.ini.append.php
[DeploySettings]
PhpFpmService=plesk-php85-fpm
```

Mehr zu Pools: [PHP-Auswahl und FPM-Pools](php-auswahl-und-fpm-pools.md).

## nginx vor Apache

Plesk stellt nginx vor Apache, und `mod_remoteip` von Apache (`RemoteIPHeader X-Forwarded-For`, `RemoteIPInternalProxy 127.0.0.1`)
setzt `REMOTE_ADDR` auf den Besucher; nginx spricht über HTTPS mit Apache, sodass Apache `HTTPS=on` setzt. Der Kernel sieht dann den
Besucher als Gegenstelle und braucht keinen weitergeleiteten Header: nichts zu konfigurieren, und `ClientIpByCustomHTTPHeader` kann
auf `false` bleiben ([Vertrauenswürdige Proxys](../../../bc/6.0/vertrauenswuerdige-proxys.md)).

## Zertifikate

- Installieren Sie das Zertifikat der Domain in Plesk (seine Let's-Encrypt-Erweiterung oder Ihr eigenes), und schalten Sie die
  Umleitung auf HTTPS ein ([8.5.3](../../../installation/08-die-website-ausliefern.md#853-plesk)).
- Wenn Velocity oder FrankenPHP dieselbe Domain auf einem anderen Port ausliefert, können sie das Zertifikat nutzen, das Plesk hält:
  Die Notizen zu FrankenPHP richten `[HTTPSSettings] Certificate` und `Key` auf eine Kopie des Let's-Encrypt-Zertifikats, das Plesk
  für den Host hält, exportiert aus `/usr/local/psa/var/certificates/` außerhalb des Document Root.
- Plesk erneuert das Let's-Encrypt-Zertifikat nach eigenem Zeitplan und schreibt dabei eine **neue Datei unter einem neuen Namen**;
  die exportierte Kopie veraltet dann. Der Export muss nach jeder Erneuerung wiederholt werden, gefolgt von einem Neuladen
  ([FrankenPHP, Erneuerung](../../../bc/6.0/frankenphp.md)). Velocity prüft seine Zertifikatsdateien alle 60 Sekunden und tauscht ein
  geändertes, gültiges Paar live aus ([8.3.9](../../../installation/08-die-website-ausliefern.md#839-https-direkt-von-velocity)), aber nur,
  wenn der Pfad, den es liest, die neue Datei bekommt.

Siehe [TLS-Zertifikate in Panels](tls-zertifikate-in-panels.md).

## Logs

Die eigenen Logs von Exponential liegen unter `var/log` und `var/<site>/log`. Um die Logs des Webservers von Plesk in
**Setup > Logdateien** zu zeigen, nennen Sie sie in `logviewer.ini` ([Log-Viewer](../../../funktionen/6.0/log-betrachter.md)):

```ini
# settings/override/logviewer.ini.append.php
[LogViewerSettings]
LogPath[webserver-access]=/var/www/vhosts/<domain>/logs/access_ssl_log
LogPath[webserver-error]=/var/www/vhosts/<domain>/logs/error_log
```

Der Benutzer des Webservers muss die Datei lesen können (`settings/logviewer.ini`). Die Schlüssel `webserver-access` und
`webserver-error` stehen in der mitgelieferten `LogList[]`.

## Cron

Die Seite Cronjobs in der Administration braucht ein PHP für die Kommandozeile; auf einem Host mit mehreren PHP-Versionen setzen Sie
`PhpCliPath` auf die, auf der die Website läuft, zum Beispiel `/opt/plesk/php/8.5/bin/php`
([Cronjobs, Bevor Sie beginnen](../../../anleitungen/cronjobs.md#2-bevor-sie-beginnen)):

```bash
/opt/plesk/php/8.5/bin/php bin/php/console exp:ini set cronjob.ini/AdminSettings/PhpCliPath /opt/plesk/php/8.5/bin/php global
```

Eine Crontab-Zeile mit dem PHP der Domain, wie die Seite sie vorschlägt:

```text
17 * * * * cd /var/www/vhosts/<domain>/<docroot> && /opt/plesk/php/8.5/bin/php runcronjobs.php -s site infrequent >/dev/null 2>&1
```

Richten Sie die Zeilen in der Crontab des Systembenutzers der Domain ein, damit die Dateien, die die Jobs schreiben, für den Pool
beschreibbar bleiben ([Cron und Shell-Zugang](cron-und-shell-zugang.md)).

## Velocity neben Plesk

Der Entwicklungsserver betreibt Velocity neben dem Apache von Plesk auf derselben Installation. Zu klärende Punkte:

- **Ports.** Apache und nginx von Plesk belegen 80 und 443; Velocity behält seine eigenen Ports (8088 und 8080 in der Auslieferung)
  oder sitzt hinter dem nginx von Plesk ([8.3.5](../../../installation/08-die-website-ausliefern.md#835-adresse-ports-benutzer-und-gruppe)).
- **Firewall.** Manche Plesk-Installationen steuern firewalld oder nftables über die Plesk-Erweiterung Firewall. Wenn eine Regel, die
  Sie mit `firewall-cmd` hinzufügen, beim nächsten Anwenden durch Plesk verschwindet, fügen Sie sie stattdessen in Plesk hinzu
  (Tools & Settings → Firewall) ([FrankenPHP](../../../bc/6.0/frankenphp.md)).
- **Ein Benutzer.** Die Worker von Velocity laufen als Systembenutzer der Domain (`[ServerSettings] User`, `Group`), derselbe Benutzer
  wie der Pool von Plesk ([8.7.2](../../../installation/08-die-website-ausliefern.md#872-ein-benutzer-oder-eine-gruppe)).
- **Ausrollen.** `exp:velocity deploy` lädt `plesk-php<XY>-fpm` neu und startet Velocity in einem Lauf neu.

## Bekannte Auswirkungen von Plesk-Einstellungen

| Symptom | Ursache in den Quellen | Was zu tun ist |
|---|---|---|
| **Setup > Systeminformationen** meldet, dass Statistiken zu OPcache nicht verfügbar sind | Plesk kann `opcache_get_status` für eine Domain in `disable_functions` setzen; der Cache funktioniert, nur seine Kennzahlen lassen sich aus PHP nicht lesen ([Systeminformationen](../../../anleitungen/systeminformationen.md)) | nichts, oder erlauben Sie die Funktion für die Domain |
| Eine Änderung an PHP wirkt nicht | der falsche Master wurde neu geladen ([Kapitel 12](../../../installation/12-fehlerbehebung.md)) | `plesk-php<XY>-fpm` neu laden |
| Ein Test scheitert im Browser, aber `php -m` sieht gut aus | die beiden PHPs unterscheiden sich: Installieren Sie die Erweiterung für das Web-PHP (auf Plesk die PHP-Version der Domain) | in der PHP-Version der Domain installieren und neu laden |
| ezupdate findet kein Composer des Systems | `open_basedir` (Standard bei Plesk) verbirgt die Systemordner, sodass ezupdate zuerst unterhalb des Wurzelverzeichnisses der Installation sucht ([Einstellungen von ezupdate](../../../funktionen/6.0/erweiterungen/ezupdate.md)) | `[ComposerSettings] Path`/`Binary` setzen, oder mit *Get Composer* ein geprüftes `composer.phar` nach `var/ezupdate/` herunterladen lassen |
| Ein langer Schritt des Setup-Assistenten wird abgebrochen | das `request_terminate_timeout` des Pools | für die Installation erhöhen, danach wieder senken |

## Checkliste

- [ ] PHP-Version der Domain 8.0 oder später (auch für Velocity); „FPM application served by Apache“.
- [ ] Document Root = Wurzelverzeichnis der Installation; `.htaccess` aus `.htaccess_root` kopiert (`exp:setup:status` bestätigt es).
- [ ] Mit SOAP an: `index_soap.php` in **Zusätzliche Direktiven für HTTPS** (oder `.htaccess`) durchgelassen, nicht nur
      das alte `soap.php` ([Das SOAP-Einstiegsskript heißt jetzt index_soap.php](../../../bc/6.0/soap-einstiegsskript-index-soap.md#plesk)).
- [ ] Composer, Konsole und Cron nutzen `/opt/plesk/php/<version>/bin/php`.
- [ ] `PhpCliPath` gesetzt; Cron-Zeilen in der Crontab des Benutzers der Domain.
- [ ] `plesk-php<XY>-fpm` nach Änderungen an PHP neu laden; `PhpFpmService` in `exp:velocity deploy --dry-run` richtig.
- [ ] Zertifikat in Plesk, Umleitung auf HTTPS an; der Export wird nach jeder Erneuerung erneuert, wenn Velocity oder FrankenPHP ihn nutzt.
- [ ] Logs des Webservers zum Log-Viewer hinzugefügt.

**Eine öffentliche Demo auf Plesk.** Setzen Sie `EXP_DEMO_MODE=1` nur für das Abonnement der Demo: `SetEnv EXP_DEMO_MODE 1` in seiner `.htaccess` (Apache oder LiteSpeed) oder in seinen **Additional Apache directives**, oder `env[EXP_DEMO_MODE] = 1` über die PHP-FPM-Einstellungen der Domain, und eine Zeile `EXP_DEMO_MODE=1` in seinen **Scheduled Tasks**. Plesk betreibt jede PHP-Version mit ihrem eigenen FPM-Master: Laden Sie den der Version der Domain neu. Schritt für Schritt: [Rezept: eine öffentliche Demo auf LiteSpeed und Plesk betreiben](../server/rezept-eine-oeffentliche-demo-auf-litespeed-und-plesk-betreiben.md).

**Der Seiten-Cache von LiteSpeed unter Plesk.** Mit LiteSpeed als Webserver beantwortet sein Seiten-Cache anonyme Besucher und Crawler ohne PHP. Schalten Sie ihn je Website mit dem auskommentierten Block aus `.htaccess_root` ein, oder fügen Sie [`litespeed-page-cache.conf`](../../../../eng-us/examples/web-servers/litespeed-page-cache.conf) in **Additional directives for HTTPS** ein. Bearbeiten Sie `/var/www/vhosts/system/<domain>/conf/vhost_ssl.conf` nie von Hand: Plesk schreibt die Datei aus dem Feld des Panels neu, wann immer es die Konfiguration der Domain neu erzeugt. Schritt für Schritt: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](../../../anleitungen/seiten-cache-mit-litespeed.md#plesk).

## Verwandte Seiten

- [Control Panels: Überblick](README.md)
- [Apache mit PHP-FPM](../server/apache-mit-php-fpm.md), [Velocity neben Apache oder nginx](../server/velocity-neben-apache-oder-nginx.md)
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
