---
title: Shared Hosting
audience: hosting
category: hosting
order: 30
summary: Wie Sie Exponential auf Shared Hosting anbieten - was laut den Quellen dort funktioniert (Apache oder LiteSpeed mit .htaccess, der Setup-Assistent, SQLite oder die Datenbank des Panels), was meist nicht funktioniert (Velocity), und die Fragen zu Einstellungen, Abschottung und Cron, die ein Hoster beantworten muss.
keywords: Shared Hosting, Apache, LiteSpeed, .htaccess, .htaccess_root, PHP-FPM-Pool, Setup-Assistent, FTP, keine Shell, open_basedir, SQLite, MariaDB, MySQL, Cron, Abschottung, Velocity, shared hosting, PHP-FPM pool, setup wizard, no shell, isolation
type: help
---
# Shared Hosting

Diese Seite ist für Hosting-Anbieter, die Exponential auf Shared Hosting anbieten möchten: viele Kundenkonten auf einer
Maschine, über ein Panel verwaltet, meist mit FTP und ohne Shell für den Kunden. Sie beantwortet, was dort laut den Quellen
des Projekts funktioniert, was meist nicht, wie eine Website ohne Shell installiert wird und was pro Konto zu konfigurieren ist.

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

1. [Was die Quellen sagen](#was-die-quellen-sagen)
2. [Auslieferung: Apache oder LiteSpeed mit .htaccess](#auslieferung-apache-oder-litespeed-mit-htaccess)
3. [Installation ohne Shell](#installation-ohne-shell)
4. [PHP-Einstellungen pro Konto](#php-einstellungen-pro-konto)
5. [Datenbanken auf Shared Hosting](#datenbanken-auf-shared-hosting)
6. [Cronjobs](#cronjobs)
7. [Abschottung zwischen Konten](#abschottung-zwischen-konten)
8. [Checkliste](#checkliste)

## Was die Quellen sagen

- Ihr Hosting lässt Sie keinen lang laufenden Prozess ausführen. Shared Hosting tut das meist nicht. Apache oder LiteSpeed
  mit `.htaccess` ist dort die Antwort (`README.md` im Wurzelverzeichnis, Abschnitt dazu, wann ein klassischer Server noch
  die richtige Antwort ist).
- Velocity passt meist nicht zu Shared Hosting, weil er einen lang laufenden Prozess braucht; Apache mit PHP-FPM passt
  ([Kapitel 8](../../../installation/08-die-website-ausliefern.md#81-die-wahl-in-einer-tabelle)).
- Shared Hosting ohne Shell-Zugang: der Assistent. Er braucht nur einen Browser und FTP-Zugang
  ([Kapitel 4](../../../installation/04-installationsweg-waehlen.md#45-welche-methode-für-welche-situation)).
- Hosts mit cPanel und DirectAdmin sind Stufe 3: Shared Hosting kann lang laufende Prozesse verbieten, sodass Velocity
  womöglich nicht erlaubt ist; nutzen Sie Apache oder LiteSpeed mit `.htaccess` ([Stufe 3](../../betriebssystemunterstuetzung.md#stufe-3-voraussichtlich-lauffähig)).
- Seit 6.0.15 arbeitet der Setup-Assistent nur mit Ihrer eigenen Datenbank: Er führt kein `SHOW DATABASES` mehr aus und
  öffnet die Systemdatenbank `mysql` nicht mehr, was Rechte bräuchte, die ein Shared Host nicht vergibt
  ([Eine saubere Installation, die zu Shared Hosting passt](../../../funktionen/6.0/standardwerte-einer-sauberen-installation.md)).

## Auslieferung: Apache oder LiteSpeed mit .htaccess

Die Rewrite-Regeln liegen im Wurzelverzeichnis der Installation als `.htaccess_root`. Der Kunde (oder Ihre Bereitstellung)
kopiert sie, sodass ein Update eine neue Version bringen kann:

```bash
cp .htaccess_root .htaccess
# oder, wo das Konto eine Shell hat, als Benutzer des Kontos:
php bin/php/console exp:setup:htaccess --apply
```

Der Konsolenbefehl übernimmt den Modus der Vorlage, weigert sich, eine abweichende `.htaccess` zu überschreiben (er
zeigt den Diff; `--force --apply` ersetzt sie nach einer Sicherung in `var/backup/setup/<zeit>/`) und sagt ohne
`--apply`, was er tun würde. `php bin/php/console exp:setup:config --apply` bringt `config.php` auf dieselbe Weise an
ihren Platz. Ohne Shell legt der Dateimanager oder der FTP-Client die Kopie an. Siehe
[.htaccess und config.php von der Konsole einrichten](../../../anleitungen/setup-dateien-von-der-konsole.md).

Nutzen Sie stattdessen `.htaccess_root_static` (`exp:setup:htaccess --static --apply`) nur, wenn die Website einen erzeugten statischen Cache ausliefert. Für Apache
muss der virtuelle Host `.htaccess` wirken lassen (`AllowOverride All` auf dem Document Root), und `mod_rewrite` muss an
sein; ein 404 für jede Seite bedeutet, dass `.htaccess` nicht gelesen wird (`AllowOverride`) oder `mod_rewrite` aus ist
([8.5 Apache mit PHP-FPM](../../../installation/08-die-website-ausliefern.md#85-apache-mit-php-fpm)). LiteSpeed liest die
Rewrite-Regeln von Apache in den meisten Fällen und ist Stufe 3 ([Webserver](../../betriebssystemunterstuetzung.md#webserver)).

> **Hinweis**
> Der Document Root des Kontos muss das Wurzelverzeichnis der Installation sein, das Verzeichnis, das `index.php` enthält.
> Die Sicherheit kommt vom Routing, nicht vom Verzeichnisaufbau: Nur eine feste Liste von Asset-Pfaden wird als Dateien
> ausgeliefert; alles andere geht an `index.php`
> ([13.2](../../../installation/13-sicherheitshaertung.md#132-was-der-webserver-niemals-ausliefern-darf)). Eine allgemeine
> Konfiguration eines Panels „Datei ausliefern, wenn sie existiert“ würde `settings/*.ini` und die SQLite-Datenbank
> ausliefern; prüfen Sie einmal pro Server-Image, dass eine Anfrage nach `/settings/site.ini` eine HTML-Seite liefert, nie
> INI-Text.

Mehr: [Apache mit PHP-FPM](../server/apache-mit-php-fpm.md), [LiteSpeed](../server/litespeed-und-openlitespeed.md).

## Installation ohne Shell

Ohne Shell kann der Kunde Composer nicht ausführen, und ohne Composer, oder ohne dass es ausgeführt wird, fehlt `vendor/`,
und die Website kann nicht starten ([2.7 Composer](../../../installation/02-voraussetzungen.md#27-composer)). Daraus ergeben sich zwei
praktische Wege:

1. **Der Hoster stellt den Code bereit.** Ihre Bereitstellung (ein Ein-Klick-Installer, ein Vorlagenkonto) legt einen Baum an,
   in dem `composer install` bereits gelaufen ist, und dann öffnet der Kunde den Setup-Assistenten im Browser. Siehe
   [Ein-Klick-Installer](../bereitstellung/ein-klick-installer-fuer-exponential.md).
2. **Der Kunde bereitet den Code anderswo vor.** Der Kunde führt Composer auf einer anderen Maschine mit derselben
   PHP-Version wie das Konto aus und lädt den ganzen Baum, einschließlich `vendor/`, per FTP hoch.

Dann erledigt der Setup-Assistent den Rest im Browser: Seine Seite **Systemtest** prüft PHP, die Erweiterungen, die
beschreibbaren Verzeichnisse und die `php.ini`-Werte und gibt für jeden Fehler die Abhilfe aus
([Der Setup-Assistent auf Shared Hosting](../bereitstellung/der-setup-assistent-auf-shared-hosting.md),
[Kapitel 5](../../../installation/05-setup-assistent.md)).

> **Warnung**
> Ein langer Schritt des Assistenten kann an die Zeitgrenze für Anfragen von PHP-FPM des Kontos stoßen. Die Notizen zur
> MongoDB-Portierung beschreiben, wie der Assistent durch das `request_terminate_timeout` des Pools abgebrochen wird, das
> `set_time_limit()` von PHP selbst nicht überschreiben kann
> ([MongoDB-Unterstützung im Kernel](../../../bc/6.0/mongodb-kernel-unterstuetzung.md)). Wenn die Installationen auf
> Ihrer Plattform lange laufen, erhöhen Sie die Grenze für die Installation und senken Sie sie danach wieder.

## PHP-Einstellungen pro Konto

Der Setup-Assistent prüft diese ([2.5 php.ini-Einstellungen](../../../installation/02-voraussetzungen.md#25-einstellungen-in-der-phpini)):

| Einstellung | Wert, den der Assistent prüft | Hinweis für Shared Hosting |
|---|---|---|
| `memory_limit` | mindestens `64M` | eine Untergrenze; Importe und Bildarbeit brauchen mehr |
| `max_execution_time` | mindestens `30` | |
| `date.timezone` | gesetzt | |
| `file_uploads` | `On` | mit einem beschreibbaren Upload-Verzeichnis |
| `allow_url_fopen` | `On` | |
| `variables_order` | enthält `E` | optionaler Test |
| `open_basedir` | leer | optionaler Test; er warnt nur, dass `open_basedir` in Gebrauch ist und wegen Fehlern in manchen PHP-Versionen Probleme beim Betrieb von Exponential verursachen kann |
| `upload_max_filesize`, `post_max_size` | nicht getestet | so groß wie die Dateien, die Redakteure hochladen |

Panels setzen `open_basedir` und `disable_functions` oft pro Konto. Der Assistent warnt nur bei `open_basedir`. Die Seite
Cronjobs in der Administration braucht `proc_open`, um einen Job zu starten, `exec`, um die Crontab zu lesen, und
`posix_kill`, um einen Job anzuhalten; ist eine davon deaktiviert, öffnet sich die Seite trotzdem und sagt, welche Funktion
fehlt ([Cronjobs, Bevor Sie beginnen](../../../anleitungen/cronjobs.md#2-bevor-sie-beginnen)).

Details: [PHP-Einstellungen für Exponential](../grundlagen/php-einstellungen-fuer-exponential.md),
[PHP-Auswahl und FPM-Pools](../control-panels/php-auswahl-und-fpm-pools.md).

## Datenbanken auf Shared Hosting

In der offiziellen Reihenfolge: 1. SQLite 3 (der Standard) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL.
Microsoft SQL Server ist derzeit bekanntermaßen mit zusätzlicher Arbeit möglich.

- **1. SQLite 3** braucht keinen Datenbankserver und kein Datenbankkonto: Die Datenbank ist eine Datei unter
  `var/storage/sqlite3/`. Auf Shared Hosting entfällt damit ein ganzer Bereitstellungsschritt. Es passt zu einer Website,
  die von einer Maschine ausgeliefert wird, was ein Shared-Konto ist.
- **Ein Datenbankserver aus dem Panel** (meist 4. MariaDB oder 6. MySQL, manchmal 2. PostgreSQL): Legen Sie Datenbank und
  Benutzer vor der Installation an; die Installer legen weder Datenbanken noch Benutzer an
  ([2.6 Datenbanken](../../../installation/02-voraussetzungen.md#26-datenbanken)). Legen Sie die Datenbank für MariaDB und MySQL in
  `utf8mb4` an. Der Benutzer braucht nur Rechte auf die eine Datenbank
  ([9.3 MySQL und MariaDB](../../../installation/09-datenbanken.md#93-mysql-und-mariadb)).

Siehe [Datenbanken und Benutzer in Control Panels](../control-panels/datenbanken-und-benutzer.md).

## Cronjobs

Geplantes Veröffentlichen, Benachrichtigungen und Bereinigung hängen von `runcronjobs.php` ab. Auf Shared Hosting plant der
Kunde es meist über die Cron-Seite des Panels. Der Befehl muss dieselbe PHP-Version wie die Website nutzen; auf Maschinen mit
mehreren PHP-Versionen heißt das den vollständigen Pfad zur richtigen Binärdatei
([Kapitel 10, Cronjobs](../../../installation/10-nach-der-installation.md#103-cronjobs)):

```text
*/5 * * * * cd /var/www/vhosts/<domain>/<docroot> && /path/to/php runcronjobs.php -q -s site frequent >/dev/null 2>&1
```

Siehe [Cron und Shell-Zugang](../control-panels/cron-und-shell-zugang.md).

## Abschottung zwischen Konten

Die Warnung aus dem README gilt auch für Shared Hosting: Ein Prozess, der mehrere Websites ausliefert, kann jede Datei lesen,
die die anderen enthalten. Geben Sie jedem Konto einen eigenen Benutzer des Betriebssystems und einen eigenen PHP-FPM-Pool, der
als dieser Benutzer läuft; das empfiehlt Kapitel 8, wenn mehrere Kunden auf einer Maschine voneinander auf Ebene des
Betriebssystems abgeschottet sein müssen. Innerhalb des Kontos sollte alles, was `var/` schreibt (der Pool, Cron, die Skripte
des Kunden), als dieser eine Benutzer laufen
([8.7.2 Ein Benutzer oder eine Gruppe](../../../installation/08-die-website-ausliefern.md#872-ein-benutzer-oder-eine-gruppe)). Siehe
[Trennung von Mandanten](../sicherheit/trennung-von-mandanten.md).

## Checkliste

- [ ] PHP 8.0 oder später mit den erforderlichen Erweiterungen in der PHP-Version des Kontos (auch Velocity läuft unter 8.0).
- [ ] Document Root = Wurzelverzeichnis der Installation; `.htaccess` aus `.htaccess_root` kopiert; `mod_rewrite` an.
- [ ] Die `php.ini`-Werte oben pro Konto gesetzt; Entscheidung zu `open_basedir` dokumentiert.
- [ ] `vendor/` vorhanden, bevor der Assistent startet.
- [ ] Eine Datenbank bereit (SQLite braucht nichts), in UTF-8 angelegt.
- [ ] Eine Cron-Zeile für `runcronjobs.php` mit dem PHP des Kontos.
- [ ] Ein Benutzer und ein PHP-FPM-Pool pro Konto.
- [ ] Backups der Datenbank und von `var/storage`, `settings/override`, `settings/siteaccess`.

## Verwandte Seiten

- [Ein Hosting-Modell wählen](ein-hosting-modell-waehlen.md)
- [Control Panels: Überblick](../control-panels/README.md)
- [Der Setup-Assistent auf Shared Hosting](../bereitstellung/der-setup-assistent-auf-shared-hosting.md)
- [.htaccess und config.php von der Konsole einrichten](../../../anleitungen/setup-dateien-von-der-konsole.md)
- [Kapitel 8: Die Website ausliefern](../../../installation/08-die-website-ausliefern.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
