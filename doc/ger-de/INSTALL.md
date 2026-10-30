# Exponential 6.0 installieren

Diese Anleitung führt Sie von null bis zu einer funktionierenden Website mit Exponential 6.0: was Sie brauchen, wie Sie
den Code erhalten, die drei Installationswege (der Einrichtungsassistent im Browser, der Kickstarter, die Installation
mit einem einzigen Konsolenbefehl), wie Sie die Website ausliefern und was danach zu tun ist. Was Exponential ist und
wie es ausgeliefert wird, lesen Sie zuerst in der [README](../../README.md) des Projekts. Eine geführte erste Stunde
nach der Installation finden Sie unter [Erste Schritte](anleitungen/erste-schritte.md).

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](anleitungen/setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](beispiele/webserver/nginx.md), [LiteSpeed](beispiele/webserver/litespeed.md), [Caddy](beispiele/webserver/caddy.md), [IIS](beispiele/webserver/iis.md),
> [Velocity](beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](hilfe/hosting/control-panels/plesk.md), [cPanel](hilfe/hosting/control-panels/cpanel.md), [DirectAdmin](hilfe/hosting/control-panels/directadmin.md), [ISPConfig](hilfe/hosting/control-panels/ispconfig.md).
> **Unter LiteSpeed oder OpenLiteSpeed** kann zusätzlich der Seiten-Cache des Servers anonyme Besucher und Crawler ohne PHP bedienen: [Seiten-Caching mit LiteSpeed und OpenLiteSpeed](anleitungen/seiten-cache-mit-litespeed.md).

Jeder Befehl wird im Wurzelverzeichnis der Installation ausgeführt: dem Verzeichnis, das `index.php`, `autoload.php`,
`bin/` und `settings/` enthält. Die Befehlskonsole ist `bin/php/console`. Viele Installationen legen im Wurzelverzeichnis
eine Verknüpfung darauf an (`ln -s bin/php/console console`), sodass `./console` und `php bin/php/console` dasselbe
Programm sind. Wenn Sie ein Skript als `root` ausführen, ergänzen Sie `--allow-root-user`; das Skript weist Sie darauf
hin, wenn es nötig ist.

> **Das vollständige Buch.** Diese Seite ist die Kurzanleitung. Zu jedem der folgenden Abschnitte gibt es ein Kapitel im
> [Installationsbuch](installation/README.md) mit allen Einzelheiten, kommentierten Beispielen und Referenzen:
> der Einrichtungsassistent Seite für Seite, jeder Schlüssel der kickstart.ini, Exponential Velocity, das HTTPS ohne
> separaten Webserver ausliefert, SQLite 3 unter hoher Produktionslast, das Betriebshandbuch, Fehlerbehebung und
> Sicherheitshärtung.

## Inhalt

1. [Was Sie erhalten, und der Schnellstart](#1-was-sie-erhalten-und-der-schnellstart)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Den Code beschaffen](#3-den-code-beschaffen)
4. [Eine Installationsmethode wählen](#4-eine-installationsmethode-wählen)
5. [Installation mit dem Einrichtungsassistenten](#5-installation-mit-dem-einrichtungsassistenten)
6. [Installation mit dem Kickstarter](#6-installation-mit-dem-kickstarter)
7. [Installation mit einem Konsolenbefehl](#7-installation-mit-einem-konsolenbefehl)
8. [Die Website ausliefern: Webserver, HTTPS und Berechtigungen](#8-die-website-ausliefern-webserver-https-und-berechtigungen)
9. [SQLite im Produktivbetrieb](#9-sqlite-im-produktivbetrieb)
10. [Nach der Installation](#10-nach-der-installation)
11. [Aktualisierung von einer älteren Version](#11-aktualisierung-von-einer-älteren-version)
12. [Fehlerbehebung](#12-fehlerbehebung)

## 1. Was Sie erhalten, und der Schnellstart

*Vollständiges Kapitel: [01-introduction](installation/01-einfuehrung.md)*

Eine Installation besteht aus dem Exponential-Kernel, den in `composer.json` aufgeführten Extensions, einem Site-Paket mit
seinen Inhalten und seinem Design sowie drei Siteaccesses. Ihre Namen lauten immer `site`, `admin` und `editor`; was Sie
bei der Installation wählen, ist der URL-Pfad, der Port oder der Hostname, der zu jedem von ihnen führt (der Match-Wert):

| Siteaccess | Für | Standard-Match-Wert mit `exp:install` |
|---|---|---|
| `site` | Besucher | der Pfad `/site` (oder Port 8080, oder der von Ihnen angegebene Host) |
| `admin` | Administratoren | der Pfad `/admin` (oder Port 8081, oder der von Ihnen angegebene Admin-Host) |
| `editor` | Redakteure: die Administration nur für Inhalte, ohne Setup, Design und Entwicklerwerkzeuge | der Pfad `/editor` (oder der öffentliche Port + 2, oder `edit.<public host without www.>`) |

Der kürzeste funktionierende Weg braucht nur PHP und Composer. Er installiert auf SQLite (kein Datenbankserver) und
liefert die Website mit dem PHP-Server aus, den Exponential für Sie starten kann:

```bash
composer create-project se7enxweb/exponential exponential
cd exponential
php bin/php/console exp:install --apply --random-password --url=http://localhost:8087
php bin/php/console exp:velocity start --engine=php
```

Öffnen Sie `http://localhost:8087/site/` für die Website und `http://localhost:8087/admin/user/login` für die
Administration. Der Benutzer ist `admin`; das Passwort wird am Ende der Installation einmal ausgegeben und in
`var/log/initial-admin-password` geschrieben (nur für den Eigentümer lesbar). Ändern Sie es nach der ersten Anmeldung und
löschen Sie diese Datei. Beenden Sie den Server mit `php bin/php/console exp:velocity stop --engine=php`.

Die Engine `php` ist der eingebaute Server von PHP und für die Entwicklung gedacht. Für eine echte Website verwenden Sie
die Engine `qbix` von Exponential Velocity oder Apache/nginx mit PHP-FPM
([Abschnitt 8](#8-die-website-ausliefern-webserver-https-und-berechtigungen)).

**Bauen Sie eine Website, statt eine auszuprobieren?** Holen Sie den Code stattdessen als Git-Klone mit ihrer
vollständigen Historie: Dieselben zwei Installationsbefehle funktionieren auch in einem Klon, und
[Abschnitt 3](#3-den-code-beschaffen) zeigt die Befehle, mit denen der Kernel und jede Extension zu einem Klon werden, den Sie
verfolgen, ändern und von dem aus Sie Beiträge zurückgeben können.

## 2. Voraussetzungen

*Vollständiges Kapitel: [02-requirements](installation/02-voraussetzungen.md)*

### PHP

| | |
|---|---|
| Version | **PHP 8.0 bis 8.5**. `composer.json` deklariert `^8.0` (und akzeptiert spätere 8.x-Versionen). PHP 8.0 ist die älteste unterstützte Version, sodass das mitgelieferte PHP von Red Hat Enterprise Linux 9 (und AlmaLinux 9, Rocky Linux 9) Exponential unter Apache, PHP-FPM oder Velocity ausführt. Verwenden Sie das neueste PHP, das Ihr System anbietet. Siehe [Unterstützung für PHP 8.0](bc/6.0/php-8.0-unterstuetzung.md). |
| Exponential Velocity | läuft ab seinem Release 0.0.4.54 auf **PHP 8.0 bis 8.5** (bis dahin brauchte es 8.1), mit der Extension `sockets` (`pcntl` und `openssl` für den Worker-Pool und HTTPS). Unter RHEL 9 führt das mitgelieferte PHP 8.0 es aus; siehe [Unterstützung für PHP 8.0](bc/6.0/php-8.0-unterstuetzung.md#exponential-velocity-unter-php-80). |
| Release-Tags | `v6.0.8` bis `v6.0.14` erfordern PHP 8.1; unter PHP 8.0 installieren Sie die Linie 6.0.15 (`dev-main`, die Composer auch als `6.0.x-dev` kennt), bis deren Tag veröffentlicht ist. |
| 64-Bit-PHP | nötig für mehr als 30 Sprachen (`composer.json`, `suggest`). |
| `memory_limit` | mindestens `64M` (Setup-Prüfung `memory_limit`). Starten Sie den Webserver oder PHP-FPM nach dem Bearbeiten von `php.ini` neu. |
| `max_execution_time` | mindestens `30` Sekunden (Setup-Prüfung `execution_time`). |
| `date.timezone` | muss in `php.ini` (oder in `.htaccess`) gesetzt sein; siehe die [Liste der Zeitzonen](https://www.php.net/manual/en/timezones.php). |
| Datei-Uploads | `file_uploads` eingeschaltet (Setup-Prüfung `file_upload`). |

PHP-Extensions:

| Benötigt von | Extensions |
|---|---|
| `composer.json` (`require`) | `dom`, `libxml`, `mbstring`, `pcre`, `json`, `iconv`, `reflection`, `session`, `spl`, `simplexml` |
| den kritischen Prüfungen des Einrichtungsassistenten (`settings/site.ini [SetupSettings] CriticalTests`) | zusätzlich `zlib`, `intl`, `xsl`; eine Datenbank-Extension (`sqlite3`, `mysqli`, `pgsql`, `mongodb` oder `oci8`); `gd` oder das ImageMagick-Programm `convert` für Bilder |
| empfohlen (`composer.json`, `suggest`) | `curl` (Kommunikation mit anderen Servern, Pakete über SSL), `gd`, `openssl` (sichere Zufallsbytes), `pcntl` (asynchrones Veröffentlichen) |

Die Systemprüfung des Einrichtungsassistenten testet all diese Punkte und sagt Ihnen, was fehlt. Auf einer laufenden
Installation listet `php bin/php/console exp:velocity ext check` jede Extension auf, die die Installation benötigt,
markiert, was fehlt, und gibt den Installationsbefehl für Ihr System aus.

### Composer

Composer 2.x. Er installiert die Zeta Components und die Exponential-Extensions, die `composer.json` aufführt.

### Datenbank

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Präferenz:** 1. SQLite 3 (der Standard) ·
> 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit bekanntermaßen mit
> zusätzlichem Aufwand möglich. Einzelheiten: [Datenbankunterstützung](hilfe/datenbankunterstuetzung.md).

| Datenbank | Treiber (`DatabaseImplementation`) | PHP-Extension | Hinweise |
|---|---|---|---|
| **1. SQLite 3** | `sqlite3` | `sqlite3` | Der Standard. Kein Datenbankserver: Die Datenbank ist eine Datei unter `var/storage/sqlite3/`. Eine vollwertige Produktionsdatenbank, siehe [Abschnitt 9](#9-sqlite-im-produktivbetrieb). |
| 2. PostgreSQL | `pgsql` (auch `ezpostgresql`, `postgresql`) | `pgsql` | Benötigt `pgcrypto` in der Datenbank. |
| 3. MongoDB | `mongodb` | `mongodb` und die Bibliothek `mongodb/mongodb` | PHP 8.2 oder neuer. |
| 4. MariaDB | `mysqli` (auch `ezmysqli`, `mysql`) | `mysqli` | UTF-8 (`utf8mb4`) ist erforderlich. |
| 5. Oracle 19c oder neuer | über die Extension `ezoracle` | `oci8` | `ezoracle` muss in `extension/` liegen; sie ist eine Abhängigkeit in `composer.json`. |
| 6. MySQL | `mysqli` (auch `ezmysqli`, `mysql`) | `mysqli` | UTF-8 (`utf8mb4`) ist erforderlich. |

Die Namen sind die Aliase in `settings/site.ini`, Block `[DatabaseSettings]`, `ImplementationAlias[]`. Die minimalen
Serverversionen, die der Assistent prüft, stehen in `kernel/setup/ezsetupcommon.php` (MySQL 4.1.1, PostgreSQL 8.0,
SQLite 3.0.1, MongoDB 4.0, Oracle 19); die praktischen Mindestversionen, die die aktuellen Treiber brauchen, liegen
höher (SQLite 3.33, PostgreSQL 13, MongoDB 6.0), siehe
[Datenbankunterstützung](hilfe/datenbankunterstuetzung.md#versionen-am-code-überprüft). Exponential läuft auf fast jedem
Betriebssystem mit PHP 8.0 oder neuer: siehe [Unterstützung von Betriebssystemen](hilfe/betriebssystemunterstuetzung.md).

### Webserver

Einer der folgenden, gewählt in [Abschnitt 8](#8-die-website-ausliefern-webserver-https-und-berechtigungen):

- **Exponential Velocity**, der empfohlene Anwendungsserver: Er liefert HTTP und HTTPS selbst aus, mit eigenen
  Zertifikaten, und benötigt keinen weiteren Webserver.
- **Apache** mit `mod_rewrite` und PHP-FPM (oder mod_php). Die Rewrite-Regeln werden in `.htaccess_root` mitgeliefert.
- Jeder andere Server, der eine URL auf `index.php` umschreiben kann (nginx, LiteSpeed, Caddy und andere, aufgeführt in
  der [README](../../README.md#the-traditional-options)). Diese Anleitung enthält keine Konfiguration für sie.

## 3. Den Code beschaffen

*Vollständiges Kapitel: [03-getting-the-code](installation/03-den-code-beschaffen.md)*

Wählen Sie einen Weg. Alle ergeben dieselbe Installation.

**Git-Klone mit Historie** (empfohlen für alle, die Websites bauen): Der Kernel und jede se7enxweb-Extension kommen als
Git-Klone an, sodass Sie sehen, was sich geändert hat, die Entwicklung verfolgen, eigene Änderungen auf einem Branch
halten und Korrekturen als Pull Requests zurückschicken können. [Mit Git entwickeln](hilfe/mit-git-entwickeln.md) erklärt es
Schritt für Schritt.

```bash
composer config --global 'preferred-install.se7enxweb/*' source
git clone https://github.com/se7enxweb/exponential.git
cd exponential
git switch -c my-site
composer install
```

Die erste Zeile ist einmal pro Benutzerkonto nötig: Sie veranlasst Composer, jedes `se7enxweb/*`-Paket zu klonen, statt
sein Release-Archiv herunterzuladen.

**Composer-Projekt** (am schnellsten für eine Website, die Sie nur betreiben; Release-Archive ohne Historie):

```bash
cd www_root_directory
composer create-project se7enxweb/exponential exponential
cd exponential
```

Fügen Sie eine Version hinzu, um ein Release festzulegen, zum Beispiel
`composer create-project se7enxweb/exponential:v6.0.14 exponential`. Fügen Sie `--prefer-source --keep-vcs` hinzu, um
auch mit `create-project` Git-Klone zu erhalten.

**Composer require in ein leeres Verzeichnis** (das Paket von
[Packagist](https://packagist.org/packages/se7enxweb/exponential)):

```bash
mkdir exponential
cd exponential
composer require se7enxweb/exponential
```

Prüfen Sie anschließend, ob die Konsole läuft:

```bash
php bin/php/console --version
```

Die erste Zeile nennt die Version, zum Beispiel `console (Exponential) 6.0.15stable`.

**Exponential Velocity.** `composer.json` verlangt es in `~0.0.4.57`, sodass `composer install` es auf jedem
unterstützten PHP installiert, auch auf 8.0. Bis zu seinem Release 0.0.4.54 brauchte es PHP 8.1 und wurde nur
vorgeschlagen; eine in dieser Zeit erstellte Installation fügt es hinzu mit:

```bash
composer require se7enxweb/exponential-velocity:~0.0.4.57
```

## 4. Eine Installationsmethode wählen

*Vollständiges Kapitel: [04-choosing-an-install-method](installation/04-installationsweg-waehlen.md)*

Alle drei Methoden führen dieselben Einrichtungsschritte aus (`kernel/setup/steps/`) und erzeugen dieselbe Art von
Installation.

| | Einrichtungsassistent | Kickstarter | Konsoleninstallation |
|---|---|---|---|
| Gestartet durch | Öffnen der Website im Browser | `php bin/php/console exp:kickstarter run --apply --force` | `php bin/php/console exp:install --apply` |
| Antworten kommen aus | den Formularen, eine Seite pro Schritt | `kickstart.ini` im Wurzelverzeichnis der Installation | Kommandozeilenoptionen, jede mit einem Standardwert |
| Am besten für | eine erste Installation, das Ausprobieren von Exponential | wiederholbare, skriptgesteuerte oder unbeaufsichtigte Installationen | die schnellste Installation; Skripte und CI |
| Benötigt während der Installation einen Webserver | ja | nein | nein |
| Probelauf | nein | `run --dry-run` | `--dry-run`, `--print` |
| Schützt eine bestehende Installation | nein (er startet nur, solange `CheckValidity=true` gilt) | benötigt `--force` für den Schritt, der die Datenbank schreibt | verweigert ohne `--force` |

Wie die Website ausgeliefert wird, wählen Sie getrennt davon ([Abschnitt 8](#8-die-website-ausliefern-webserver-https-und-berechtigungen)):

| | Exponential Velocity (`qbix`) | Apache oder nginx mit PHP-FPM | FrankenPHP (über Velocity) |
|---|---|---|---|
| Weiterer Webserver nötig | nein | ja | nein |
| HTTPS | eingebaut: ein vorhandenes Zertifikat, Let's Encrypt oder jede ACME-CA, selbstsigniertes Ersatzzertifikat | das des Webservers, Zertifikat aus Ihrem eigenen Werkzeug | eingebaut, selbstsigniert, wenn kein Zertifikat gesetzt ist |
| PHP | 8.0+ | 8.0+ | das eigene des Binärprogramms |
| Rolle | **empfohlen**, in jeder Phase von der Entwicklung bis zur Produktion | die klassische Einrichtung, Shared Hosting | produktionsreife Alternative |

## 5. Installation mit dem Einrichtungsassistenten

*Vollständiges Kapitel: [05-setup-wizard](installation/05-setup-assistent.md)*

### Wie er startet

`settings/site.ini` wird mit `[SiteAccessSettings] CheckValidity=true` ausgeliefert. Solange der Wert `true` ist, wird
jede Anfrage statt an die Website an den Einrichtungsassistenten geschickt (Modul `setup`, Funktion `init`). Der letzte
Schritt schreibt `CheckValidity=false` in `settings/override/site.ini.append.php`, und von da an wird die Website
ausgeliefert.

1. Liefern Sie die Installation mit einem Webserver aus ([Abschnitt 8](#8-die-website-ausliefern-webserver-https-und-berechtigungen)),
   zum Beispiel mit `php bin/php/console exp:velocity start --engine=php`.
2. Öffnen Sie die Adresse der Website in einem Browser. Die erste Seite des Assistenten erscheint.

Während der Assistent läuft, kommt nur der Browser durch, der ihn gestartet hat (er trägt das Cookie
`exp_setup_wizard`); jeder andere Besucher sieht eine Seite „The site is being set up" (HTTP 503). Diese Sperre endet mit
der letzten Seite des Assistenten oder 30 Minuten nach seiner letzten Anfrage (`expMaintenance::WIZARD_LEASE`).

Liegt eine `kickstart.ini` im Wurzelverzeichnis der Installation, liest der Assistent sie: Schritte, deren Abschnitt
`Continue=true` enthält, werden übersprungen, die anderen sind vorausgefüllt ([Abschnitt 6](#6-installation-mit-dem-kickstarter)).
Entfernen oder verschieben Sie `kickstart.ini`, wenn Sie jede Seite selbst beantworten möchten.

### Die Schritte

Die Reihenfolge ist die Schritttabelle in `kernel/setup/steps/ezstep_data.php`.

| # | Seite | Was Sie eingeben |
|---|---|---|
| 1 | Willkommen | Nichts; fahren Sie fort. |
| 2 | Systemtest | Die kritischen Prüfungen: Verzeichnisberechtigungen, PHP-Version, eine Datenbank-Extension, Bildkonvertierung, Speicherlimit, Ausführungszeit, `allow_url_fopen`, Sitzungen, Datei-Uploads, `zlib`, `dom`, `iconv`, `mbstring`, `intl`, `xsl`, die Zeitzone und die Zeta Components. Zu jedem Problem wird die Lösung angezeigt, bei Berechtigungen die genauen `chmod`-/`chown`-Befehle. Beheben Sie sie und klicken Sie auf **Weiter**, um die Prüfung erneut auszuführen. Ein Test kann mit seinem Kontrollkästchen ignoriert werden (nicht empfohlen). |
| 3 | Feineinstellung des Systems | Optionale Prüfungen (`curl`, GD, ImageMagick, die anderen Datenbank-Extensions, TrueType-Textfunktionen, `open_basedir`). Beheben Sie sie und klicken Sie auf **Feineinstellungen**, oder klicken Sie auf **Weiter**, um fortzufahren. |
| 4 | Postausgang | Sendmail/MTA oder SMTP mit Server, Benutzer und Passwort. |
| 5 | Datenbanksystem auswählen | SQLite steht an erster Stelle und ist vorausgewählt (`settings/setup.ini [DatabaseSettings] DefaultType=sqlite3`). Fehlt die Extension `sqlite3`, wird das erste verfügbare System gewählt, und die Seite weist darauf hin. |
| 6 | Initialisierung der Datenbank | Server, Port, Datenbankname, Benutzer, Passwort, Socket. Bei SQLite nur der Dateiname (Standard `sqlite.db`, abgelegt in `var/storage/sqlite3/`); das Feld Datenbank unter Seitendetails bietet später die bereits vorhandenen SQLite-Dateien an. |
| 7 | Sprachunterstützung | Die Hauptsprache und weitere Sprachen. |
| 8 | Site Paket | Das zu installierende Site-Paket; das mitgelieferte ist vorausgewählt. |
| 9 | Sprachoptionen | Wird angezeigt, wenn das Paket Sprachen enthält, die Sie nicht gewählt haben: Legen Sie jede als eigene Sprache an (vorausgewählt), ordnen Sie sie einer Ihrer Sprachen zu, oder überspringen Sie ihre Inhalte. |
| 10 | Konfiguration der Siteaccesses | Wie die Siteaccesses unterschieden werden: nach URL-Pfad, nach Port oder nach Hostname. |
| 11 | Seitendetails | Titel, URL, die Match-Werte von `site`, `admin` und `editor` (URL-Pfade sind mit dem Paketbezeichner, *identifier*`_admin` und `editor` vorausgefüllt; Ports `8080`, `8081`, `8082`; Hostnamen *identifier*`.`*host*, *identifier*`-admin.`*host*, `edit.`*host*). Der Assistent lehnt `admin` und `user` als Werte ab. Außerdem der Absender der optionalen E-Mails der Website und was mit einer Datenbank geschehen soll, die bereits Daten enthält. |
| 12 | Administrator der Seite | Vorname, Nachname, E-Mail-Adresse und Passwort des Benutzers `admin`. Das Passwort muss mindestens 10 Zeichen lang sein (`site.ini [UserSettings] MinPasswordLength`); ein leeres wird abgelehnt. |
| 13 | Seitensicherheit | Hinweise, wenn die Website nicht im Virtual-Host-Modus läuft: Kopieren Sie die Regeln mit `./console exp:setup:htaccess --apply` (oder `cp .htaccess_root .htaccess`). |
| 14 | Registrierung | Nichts einzugeben: eine Seite über die Community. Der Assistent sendet keine Registrierungs-E-Mail. |
| — | Sites anlegen | Keine Seite: Hier werden das Datenbankschema und die Daten, die Pakete und die Dateien in `settings/siteaccess/` geschrieben. |
| 15 | Abgeschlossen | Die Adressen der öffentlichen, der Admin- und der Redakteurs-Website. |

Der Assistent protokolliert seinen Fortschritt in `var/log/setup.log`. Mehr dazu:
[Einrichtungsassistent und Siteaccess editor](funktionen/6.0/einrichtungsassistent-und-editor-siteaccess.md),
[Standardwerte einer sauberen Installation](funktionen/6.0/standardwerte-einer-sauberen-installation.md),
[Installer-Protokolle und Startdaten](spezifikationen/6.0/installer-protokolle-und-startdaten.md).

## 6. Installation mit dem Kickstarter

*Vollständiges Kapitel: [06-kickstarter](installation/06-kickstarter.md)*

Der Kickstarter (`bin/php/kickstarter.php`, Konsole `exp:kickstarter`) führt die Schritte des Einrichtungsassistenten
ohne Browser aus und liest jede Antwort aus `kickstart.ini` im Wurzelverzeichnis der Installation. Die kommentierte
Vorlage ist `kickstart.ini-dist`.

### Die Befehle

```bash
php bin/php/console exp:kickstarter ini            # write kickstart.ini in an interactive editor
php bin/php/console exp:kickstarter ini --yes      # write it with the built-in defaults, no questions
php bin/php/console exp:kickstarter run --dry-run  # check kickstart.ini, the database and the packages
php bin/php/console exp:kickstarter run --apply --force    # install
```

`php bin/php/kickstarter.php` akzeptiert dieselben Unterbefehle und Optionen.

| Unterbefehl und Option | Bedeutung |
|---|---|
| `ini` | `kickstart.ini` aus `kickstart.ini-dist` in einem interaktiven Editor schreiben oder bearbeiten (benötigt ein Terminal). |
| `ini --yes`, `-y` | Die eingebauten Standardwerte übernehmen und ohne Rückfrage mit dem Modus `0600` schreiben: SQLite in `exponential.db`, `DatabaseAction=ignore` (installiert in eine leere Datenbank, löscht nichts). Prüfen Sie vor dem Ausführen die mit `# REVIEW` markierten Zeilen, vor allem `URL`, das leer ist. |
| `ini --defaults`, `-d` | Die Beispielwerte aus `kickstart.ini-dist` übernehmen. |
| `ini --from-installed` | Datenbankserver, -name und -benutzer (niemals das Passwort) eines installierten Siteaccess als Standardwerte übernehmen. Die Datei verweist dann auf eine aktive Datenbank. |
| `run` | Die Schritte von `Welcome` bis `Final` ausführen. |
| `run --apply` | Immer erforderlich, wenn die Schritte `CreateSites` enthalten, den Schritt, der Datenbank und Einstellungen schreibt. Ohne diese Option bricht `run` mit „Re-run with --force to confirm you want to install the site package." ab. |
| `run --dry-run` | `kickstart.ini` validieren, `DatabaseChoice` bis `SiteDetails` ausführen, das Site-Paket und seine Abhängigkeiten in ein temporäres `var/storage/packages/dryrun/` herunterladen (das wieder entfernt wird) und anhalten. Die Datenbank muss erreichbar sein; es wird nichts hineingeschrieben, kein Passwort erzeugt und keine E-Mail gesendet. |
| `run --list-steps` | Die Schritte auflisten und beenden. Ändert nichts. |
| `run --start-step=<step>`, `--stop-step=<step>` | Einen Teil der Schritte ausführen, zur Diagnose. Ein Lauf kann einen früheren nicht fortsetzen: Ein Startschritt, dessen spätere Schritte die Ergebnisse früherer benötigen, wird abgelehnt, mit Angabe des Schritts, bei dem begonnen werden muss. Beheben Sie nach einem Fehler die Ursache und führen Sie die gesamte Folge erneut aus. |

Während `run --apply` die Datenbank neu aufbaut, beantwortet die Website jeden Besucher mit der Wartungsseite; ein
bereits geöffnetes Wartungsfenster bleibt bestehen, und nach einem fehlgeschlagenen Schritt bleibt die Seite stehen, bis
Sie erneut ausführen oder `php bin/php/maintenance.php off` aufrufen. Jeder Lauf wird mit maskierten Passwörtern in
`var/log/kickstart.log` protokolliert (frühere Läufe werden als `kickstart.log.1` bis `.9` aufbewahrt;
`EXP_KICKSTART_LOG=0` schaltet das Protokoll ab). Das Site-Paket wird aus `var/storage/packages/` genommen, wenn es dort
bereits importiert ist; andernfalls wird es mit den Paketen, die es benötigt, aus dem in `settings/package.ini`
konfigurierten Paketindex heruntergeladen.

### kickstart.ini

Ein Abschnitt pro Schritt des Assistenten. Lassen Sie vor einem Abschnitt oder einem Schlüssel keine Leerzeichen.
`Continue=true` überspringt den Schritt und verwendet die Werte; `Continue=false` (oder kein `Continue`) zeigt den
Schritt vorausgefüllt an. Ein vollständiges Beispiel für MySQL:

```ini
[email_settings]
Continue=true
Type=mta

[database_choice]
Continue=true
Type=mysqli

[database_init]
Continue=true
Server=localhost
Port=
Database=exponential
User=exponential
Password=CHANGE_ME
Socket=

[language_options]
Continue=true
Primary=eng-GB
Languages[]=eng-US

[site_types]
Continue=true
Site_package=sevenx_multisite

[site_access]
Continue=true
Access=url

[site_details]
Continue=true
Title=My Exponential site
URL=https://www.example.com
Access=site
AdminAccess=admin
EditorAccess=editor
Database=exponential
DatabaseAction=remove

[site_admin]
Continue=true
FirstName=Site
LastName=Administrator
Email=webmaster@example.com
Password=

[security]
Continue=true

[registration]
Continue=true
Send=false
```

| Abschnitt | Schlüssel |
|---|---|
| `email_settings` | `Type` (`mta` oder `smtp`), `Server`, `User`, `Password` |
| `database_choice` | `Type`: `sqlite3` (Standard), `mysqli` (oder `mysql`), `pgsql` (oder `postgresql`), `mongodb`, `oci8` (oder `oracle`; benötigt `extension/ezoracle`) |
| `database_init` | `Server`, `Port`, `Database`, `User`, `Password`, `Socket`. Bei Oracle ist `Database` der Connect-String (zum Beispiel `127.0.0.1:1521/FREEPDB1`) oder ein TNS-Alias. |
| `language_options` | `Primary`, `Languages[]` |
| `site_types` | `Site_package` |
| `site_access` | `Access`: `url`, `port` oder `hostname` |
| `site_details` | `Title`, `URL`, `OrganisationName`, `OrganisationAddress`, `Access`, `AdminAccess`, `EditorAccess`, `AccessPort`, `AdminAccessPort`, `EditorAccessPort`, `AccessHostname`, `AdminAccessHostname`, `EditorAccessHostname`, `Database`, `DatabaseAction` |
| `site_admin` | `FirstName`, `LastName`, `Email`, `Password` (leer: Es wird eines erzeugt und in `var/log/initial-admin-password` geschrieben) |
| `security` | nur `Continue` |
| `registration` | `Send` (Standard `false`; `true` sendet einen Bericht nur an `settings/setup.ini [RegistrationSettings] Receiver`, das standardmäßig leer ist, sodass nichts gesendet wird), `UserData[...]` |

Jede Sprache muss eine Locale in `share/locale` haben, sonst bricht der Lauf in `LanguageOptions` ab und nennt die
Sprache. Sprachen des Site-Pakets, die Sie nicht aufgeführt haben, werden als eigene Sprachen angelegt, sodass ihre
Inhalte erreichbar bleiben.

### DatabaseAction: Lesen Sie dies vor dem Ausführen

> **Warnung.** `DatabaseAction=remove` **leert die angegebene Datenbank** und installiert in sie hinein. Alles in dieser
> Datenbank geht verloren. Lassen Sie `Database` auf eine Datenbank zeigen, die nichts enthält, was Sie brauchen, und
> erstellen Sie vorher eine Sicherung.

| Wert | Was mit der Datenbank geschieht |
|---|---|
| `remove` | Vorhandene Einträge werden entfernt, dann werden Schema, Daten und Pakete installiert. Eine saubere Installation. |
| `ignore` | Einträge werden hinzugefügt, ohne aufzuräumen. |
| `skip` | Es werden weder Schema noch Daten eingefügt. Verwenden Sie dies, um die Siteaccess- und INI-Dateien gegen eine bestehende Datenbank neu zu erzeugen. |

### Erneut ausführen

- Führen Sie mit derselben `kickstart.ini` erneut aus, um eine identische Installation zu erhalten. Mit
  `DatabaseAction=remove` ersetzt dies die Datenbank erneut.
- Beheben Sie nach einem Fehler die Ursache (der fehlgeschlagene Schritt und seine Meldung stehen in der Ausgabe und in
  `var/log/setup.log`) und führen Sie die gesamte Folge erneut aus; mit `DatabaseAction=remove` ersetzt der zweite Lauf,
  was der erste hinterlassen hat.
- `ini` und `run` löschen die zwischengespeicherten Dateien `var/cache/ini/kickstart-*.php` selbst. Wenn Sie
  `kickstart.ini` von Hand bearbeiten und alte Werte sehen, löschen Sie diese Dateien.
- Wenn Sie auf eine andere Datenbank-Engine installieren als die, die die Installation zuvor verwendet hat, setzen Sie
  zuerst `DatabaseImplementation` in `settings/override/site.ini.append.php` auf die neue Engine: Der Installer behält
  den Wert, den er dort vorfindet.
- `kickstart.ini` enthält das Datenbankpasswort. Halten Sie die Datei aus der Versionsverwaltung heraus, und entfernen
  oder verschieben Sie sie nach der Installation, wenn der Web-Assistent sie nicht lesen darf.

Vollständige Referenz: [Kickstarter CLI](bc/6.0/kickstartercli.md), [Kickstarter](funktionen/6.0/kickstarter-cli.md).

## 7. Installation mit einem Konsolenbefehl

*Vollständiges Kapitel: [07-console-install](installation/07-installation-ueber-die-konsole.md)*

`exp:install` (`bin/php/install.php`) erstellt die Kickstart-Konfiguration aus seinen Optionen, führt dieselben Schritte
im selben Prozess aus und stellt eine vorher vorhandene `kickstart.ini` wieder her. Alles hat einen Standardwert, sodass
allein dieser Befehl das Paket `sevenx_multisite` auf SQLite mit dem Benutzer `admin` und einem erzeugten Passwort
installiert:

```bash
php bin/php/console exp:install --apply
```

Andere Datenbanken (halten Sie das Passwort mit `EXP_INSTALL_DB_PASSWORD` aus der Shell-Historie heraus):

```bash
EXP_INSTALL_DB_PASSWORD='secret' php bin/php/console exp:install --apply --db=mysql --db-host=127.0.0.1 \
    --db-name=mysite --db-user=mysite --url=https://www.example.com --random-password
```

| Option | Standard | Bedeutung |
|---|---|---|
| `--db=` | `sqlite` | `sqlite`, `mysql` (auch `mariadb`), `pgsql`, `mongodb`, `oracle` |
| `--db-host=`, `--db-port=`, `--db-socket=` | `localhost`; 3306, 5432, 27017, 1521 | wo der Datenbankserver läuft |
| `--db-name=` | `exponential` (`exponential.db` für SQLite, `FREEPDB1` für Oracle) | die Datenbank |
| `--db-user=`, `--db-password=` | `root` (mysql), `postgres` (pgsql), sonst keiner | Zugangsdaten; oder `EXP_INSTALL_DB_PASSWORD` |
| `--db-action=` | `remove` | `remove`, `ignore` oder `skip`, wie `DatabaseAction` oben. **`remove` leert die Datenbank zuerst.** |
| `--package=` | `sevenx_multisite` | Site-Paket |
| `--language=`, `--languages=` | `eng-US` | Hauptsprache; weitere als kommagetrennte Liste |
| `--title=` | `Exponential` | Name der Website |
| `--url=` | `http://localhost` (`http://<--host>` mit `--access=host`) | wo die Website erreichbar ist; geben Sie die echte Adresse an, sie landet in jedem Link, den die Website in E-Mails schreibt |
| `--access=` | `url` | `url`, `host` oder `port` |
| `--site-access=`, `--admin-access=`, `--editor-access=` | `site`, `admin`, `editor` | die URL-Pfade der drei Siteaccesses (ihre Namen bleiben `site`, `admin`, `editor`) |
| `--host=`, `--admin-host=`, `--editor-host=` | —, —, `edit.<--host without www.>` | Hostnamen (implizieren `--access=host`; `--host` und `--admin-host` sind beide erforderlich) |
| `--port=`, `--admin-port=`, `--editor-port=` | `8080`, `8081`, `--port` + 2 | Ports für `--access=port` |
| `--organisation-name=`, `--organisation-address=` | der Name der Website, leer | der Absender der optionalen E-Mails der Website und seine Postanschrift |
| `--email=`, `--first-name=`, `--last-name=` | | der Administrator |
| `--password=` | erzeugt | wird nur übernommen, wenn es mindestens 10 Zeichen hat (`site.ini [UserSettings] MinPasswordLength`) und kein allgemein bekanntes ist; andernfalls durch ein erzeugtes ersetzt |
| `--random-password` | | ein Passwort mit 24 Zeichen erzeugen, einmal anzeigen und in `var/log/initial-admin-password` schreiben |
| `--force` | | über eine bestehende Installation installieren (ersetzt deren Datenbank und Einstellungen) |
| `--dry-run` | | Konfiguration und Pakete prüfen, nichts installieren |
| `--print` | | die Konfiguration anzeigen, die verwendet würde, und anhalten |
| `--help` | | alle Optionen |

Die Zusammenfassung am Ende nennt die Adressen der Website, der Admin-Anmeldung und des Redakteurszugangs sowie Benutzer
und Passwort. Die verwendete Konfiguration wird mit maskierten Passwörtern in `var/log/exp-install-<date>.ini`
aufbewahrt. Mehr dazu: [Installation mit einem Befehl](funktionen/6.0/installation-mit-einem-befehl.md).

## 8. Die Website ausliefern: Webserver, HTTPS und Berechtigungen

*Vollständige Kapitel: [08-serving-the-site](installation/08-die-website-ausliefern.md) und [13-security-hardening](installation/13-sicherheitshaertung.md)*

Das Document Root ist immer das Wurzelverzeichnis der Installation.

### Option A: Exponential Velocity (empfohlen)

Velocity ist ein Anwendungsserver, der für Exponential mitgeliefert wird. Er lädt die Anwendung einmal, hält sie in
persistenten Workern und hat einen eigenen Antwort-Cache. Er liefert HTTP und HTTPS selbst aus, mit eigenen
Zertifikaten, sodass weder Apache noch nginx noch PHP-FPM nötig sind. Installieren Sie ihn mit Composer
([Abschnitt 3](#3-den-code-beschaffen)), dann:

```bash
php bin/php/console exp:velocity config set --apply ServerSettings Engine qbix
php bin/php/console exp:velocity start
php bin/php/console exp:velocity status
```

`config set` schreibt `settings/override/velocity.ini.append.php`; die mitgelieferten Standardwerte bleiben in
`settings/velocity.ini`. Die mitgelieferte Standard-Engine ist `php` (Entwicklung); setzen Sie für jede echte Website
`qbix`. Die Engine `qbix` liefert einfaches HTTP auf `[ServerSettings] Port` (8088) und HTTPS auf `HTTPSPort` (8080)
aus, gebunden an `Host` (`127.0.0.1`). Um ins Netzwerk auszuliefern:

```bash
php bin/php/console exp:velocity config set --apply ServerSettings Host 0.0.0.0
php bin/php/console exp:velocity config set --apply ServerSettings Port 80
php bin/php/console exp:velocity config set --apply ServerSettings HTTPSPort 443
php bin/php/console exp:velocity restart
```

Ports unter 1024 benötigen root. Als root gestartet, behält der Serverprozess root, um die Ports zu binden und das
Zertifikat zu lesen, und die Worker laufen als `[ServerSettings] User` und `Group` (wenn leer: `VC_RUN_USER` und
`VC_RUN_GROUP` aus der Umgebung oder aus `/etc/vc/envvars`, sonst der Eigentümer des Document Root). Worker laufen nie
als root, außer bei `AllowRootWorkers=enabled`.

**HTTPS mit einem vorhandenen Zertifikat** (eine PEM-Datei kann beides enthalten):

```bash
php bin/php/console exp:velocity config set --apply HTTPSSettings Certificate /etc/ssl/example.com/fullchain.pem
php bin/php/console exp:velocity config set --apply HTTPSSettings Key /etc/ssl/example.com/privkey.pem
php bin/php/console exp:velocity config set --apply HTTPSSettings Enabled true
php bin/php/console exp:velocity restart
php bin/php/console exp:velocity ssl show
```

HTTPS ist eingeschaltet, wenn `Enabled` den Wert `true` hat und beide Dateien existieren. Das Zertifikat wird vor der
Verwendung geprüft (lässt sich parsen, ist nicht abgelaufen, der Schlüssel passt); ein selbstsigniertes Zertifikat
springt ein, solange das echte unbrauchbar ist (`fallback`, Standard `self-signed`), und ein erneuertes Zertifikat wird
ohne Neustart eingewechselt. Das Zertifikatssubsystem der Engine bezieht und erneuert außerdem Zertifikate von
Let's Encrypt (oder jeder ACME-CA) und liest Archive und PKCS#12-Bündel; es wird unter `Q.web.https` konfiguriert und in
[Velocity: HTTPS und Zertifikate](funktionen/6.0/velocity-https-zertifikate.md) beschrieben.
`[HTTPSSettings] HSTSMaxAge` (ausgeliefert mit 300 Sekunden) setzt Strict-Transport-Security; erhöhen Sie den Wert erst,
wenn jeder Name der Website dauerhaft auf HTTPS läuft.

**Konfigurationsaufbau.** Die Konfiguration von Velocity folgt Debians `/etc/apache2`: `/etc/vc/vc.conf`,
`ports.conf`, `envvars` sowie `sites-available`/`sites-enabled`, `conf-available`/`conf-enabled`,
`mods-available`/`mods-enabled` (eine Überlagerung über `/etc/qbix` der Engine, das ebenfalls gelesen wird). Die
Site-Datei `sites-available/<site>.conf` wird bei jedem `start` und `restart` oder mit `exp:velocity layout migrate` aus
`velocity.ini` erzeugt. Schalten Sie Sites, Snippets und Module wie mit `a2ensite` um:

```bash
php bin/php/console exp:velocity layout                 # show every file the server uses
php bin/php/console exp:velocity site enable --apply <name>
php bin/php/console exp:velocity site disable --apply <name>
```

**Im Alltag:**

| Befehl | Wirkung |
|---|---|
| `exp:velocity start`, `stop`, `status`, `restart` | wie benannt; `status --json` für das Monitoring |
| `exp:velocity graceful` | führt den Server neu aus und behält dabei den lauschenden Socket: keine verlorenen Anfragen |
| `exp:velocity cache clear` | leert den Antwort-Cache |
| `exp:velocity deploy` | nach einer Änderung an PHP, INI oder einer Klasse: Autoloads, Caches, PHP-FPM-Reload, Velocity-Neustart, in der richtigen Reihenfolge (`--dry-run` zeigt die Schritte) |

Worker halten die Anwendung im Speicher: Starten Sie nach einer Änderung an einer Klasse oder einer Einstellung neu (oder
verwenden Sie `deploy`); das Leeren der Caches allein erreicht sie nicht. Velocity hat keinen Befehl, um einen
Systemdienst für den Bootvorgang zu installieren; das Engine-Paket liefert eine beispielhafte systemd-Unit in
`vendor/se7enxweb/exponential-velocity/service/` mit. Wenn Velocity die Website allein ausliefert, setzen Sie
`[DeploySettings] PhpFpmService` auf `disabled`.

Mehr dazu: [Velocity-Engines](bc/6.0/velocity-engines.md), [Aufbau auf der Festplatte](bc/6.0/velocity-verzeichnisstruktur.md),
[persistente Worker](funktionen/6.0/velocity-server-mit-dauerhaften-workern.md), [Deployment](anleitungen/bereitstellung.md).

**FrankenPHP** ist die produktionsreife alternative Engine, die mit denselben Befehlen gesteuert wird:
`exp:velocity config set
ServerSettings Engine frankenphp`, `exp:velocity install` (lädt das Binärprogramm herunter und prüft es),
`exp:velocity start`. Sie liefert standardmäßig HTTPS aus (`[FrankenPHPSettings] HTTPS=enabled`, Port 8444), mit Ihrem
`[HTTPSSettings]
Certificate` und `Key` oder einem selbstsignierten Zertifikat, das sie selbst erstellt und erneuert.

### Option B: Apache mit PHP-FPM

Aktivieren Sie die mitgelieferten Rewrite-Regeln:

```bash
./console exp:setup:htaccess --apply      # oder von Hand: cp .htaccess_root .htaccess
```

Der Befehl kopiert `.htaccess_root` nach `.htaccess` mit dem Modus der Vorlage (und, als root mit
`--allow-root-user`, mit ihrem Eigentümer), zeigt einen Diff und lehnt ab, wenn bereits eine abweichende `.htaccess`
vorhanden ist, und sichert die alte Datei, bevor `--force --apply` sie ersetzt. `--static` kopiert stattdessen
`.htaccess_root_static`. Ebenso legt `./console exp:setup:config --apply` die Datei `config.php-RECOMMENDED` als
`config.php` an; siehe [.htaccess und config.php von der Konsole aus einrichten](anleitungen/setup-dateien-von-der-konsole.md).

Sie schalten den Virtual-Host-Modus auf Basis von `.htaccess` ein: Punktdateien, `.git` und Skripte unterhalb der
Front-Controller antworten mit 404, `/api/` geht an `index_rest.php`, statische Design-Dateien werden direkt
ausgeliefert, und alles andere geht an `index.php`. Entfernen Sie daraus keine Sicherheitsregeln, um etwas zum Laufen zu
bringen. Ein minimaler Virtual Host:

```apache
<VirtualHost *:80>
    ServerName example.com
    DocumentRoot /var/www/example.com
    DirectoryIndex index.php

    <Directory /var/www/example.com>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    <FilesMatch \.php$>
        SetHandler "proxy:unix:/run/php-fpm/www.sock|fcgi://localhost"
    </FilesMatch>
</VirtualHost>
```

`mod_rewrite`, `mod_proxy` und `mod_proxy_fcgi` müssen geladen sein. Prüfen Sie mit `apachectl configtest` und laden Sie
Apache neu. Für HTTPS fügen Sie einen Virtual Host `*:443` mit `SSLEngine on`, `SSLCertificateFile` und
`SSLCertificateKeyFile` hinzu, sowie ein Zertifikat aus Ihrem üblichen Werkzeug. Laden Sie nach einer PHP-Änderung das
PHP-FPM neu, das die Website ausliefert; auf Servern mit mehreren PHP-Versionen ist das nicht immer dasjenige namens
`php-fpm` (`[DeploySettings] PhpFpmService=auto` lässt `exp:velocity deploy` es finden). Einzelheiten:
[Deployment](anleitungen/bereitstellung.md).

### Berechtigungen

Der Benutzer, unter dem der Webserver oder die Velocity-Worker laufen, muss in diese Verzeichnisse schreiben können
(die Setup-Prüfung `directory_permissions` in `settings/setup.ini`): `design`, `extension`, `settings`,
`settings/override`, `settings/siteaccess`, `var` und alles darunter, was Exponential schreibt (`var/cache`, `var/log`,
`var/storage`, `var/autoload`). Die Systemprüfung gibt die Befehle für Ihren Rechner in dieser Form aus:

```bash
sudo chmod -R ug+rwx design extension settings var
sudo chown -R <web user>:<web group> design extension settings var
```

Wenn Kommandozeilenskripte und Cronjobs unter einem anderen Benutzer laufen als der Webserver, geben Sie ihnen eine
gemeinsame Gruppe, die dieselben Dateien schreiben darf.

### Hinter einem Reverse Proxy oder Load Balancer

Weitergeleiteten Headern (`X-Forwarded-For`, `X-Forwarded-Proto`, `X-Forwarded-Host`) wird nur von den Adressen in
`site.ini [HTTPHeaderSettings] TrustedProxies[]` vertraut, standardmäßig `127.0.0.1` und `::1`. Ein Proxy auf einem
anderen Rechner muss dort hinzugefügt werden (zum Beispiel `TrustedProxies[]=10.0.0.0/8` in
`settings/override/site.ini.append.php`), sonst erzeugt die Website hinter einem HTTPS-Proxy `http://`-Links und sieht
mit `ClientIpByCustomHTTPHeader=X-Forwarded-For` die Adresse des Proxys statt der des Besuchers. Einzelheiten:
[Kapitel 8](installation/08-die-website-ausliefern.md) und
[Weitergeleiteten Headern wird nur von konfigurierten Proxys vertraut](bc/6.0/vertrauenswuerdige-proxys.md).

## 9. SQLite im Produktivbetrieb

*Vollständiges Kapitel: [09-databases](installation/09-datenbanken.md)*

SQLite ist nicht nur zum Ausprobieren von Exponential da. Der Kernel-Treiber (`lib/ezdb/classes/ezsqlite3db.php`, Klasse
`eZSQLite3DB`) ist für eine Website unter Last gebaut.

**Was der Treiber tut:**

| | |
|---|---|
| WAL | Der Treiber schaltet die Datenbank auf `journal_mode=WAL` um (einmal gesetzt, in der Datei gespeichert). Leser blockieren den Schreiber nicht, und der Schreiber blockiert keine Leser, sodass Lesezugriffe parallel laufen. |
| Verbindungsoptimierung | `synchronous=NORMAL` (absturzsicher mit WAL), `cache_size=-65536` (64 MB), `mmap_size=268435456` (256 MB), `temp_store=MEMORY`, `busy_timeout=5000` (5 s pro Anweisung). |
| Schreibzugriffe in der Warteschlange | SQLite hat jeweils nur einen Schreiber. Jede Transaktion beginnt mit `BEGIN IMMEDIATE`, nachdem sie eine exklusive Sperre auf `<database file>.writer-lock` neben der Datenbank erhalten hat. Schreiber warten dort bis zu `SQLiteTransactionWait` Sekunden, bis sie an der Reihe sind, statt mit „database is locked" zu scheitern. Das Warten ist sicher: Es wurde noch nichts geschrieben, und eine begonnene Transaktion kann nicht an einer Sperre scheitern. Die Sperre wird beim Commit oder Rollback freigegeben, und vom Betriebssystem, wenn der Prozess stirbt. |
| Zeitüberschreitung | Nur eine Transaktion, die länger als `SQLiteTransactionWait` wartet, scheitert, und zwar mit „database is busy: the transaction could not start within N s, another write held the lock all that time; nothing was written". |

**Einstellungen** (`settings/site.ini`, Block `[DatabaseSettings]`; Überschreibung in
`settings/override/site.ini.append.php`):

```ini
[DatabaseSettings]
DatabaseImplementation=sqlite3
# a name under var/storage/sqlite3/, or an absolute path
Database=exponential.db
# seconds a transaction waits for the writers ahead of it (default 25);
# keep it below the request timeout: Velocity's Q.webserver.requestTimeout
# (30 s unless changed), or PHP-FPM's request_terminate_timeout
SQLiteTransactionWait=25
# PRAGMAs over the driver's defaults, one per line as name=value
SQLitePragmas[]
#SQLitePragmas[]=cache_size=-131072
```

Leeren Sie nach einer Änderung den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini`.

**Was die Last trägt.** Die meisten Anfragen erreichen die Datenbank nie: der Cache der Inhaltsansichten, der
Template-Block-Cache und, mit Velocity, der Antwort-Cache, der eine wiederholte Anfrage beantwortet, ohne die Anwendung
zu starten. Unter den persistenten Workern von Velocity hält jeder Worker die Anwendung geladen und teilt sich die eine
Datenbankdatei; Lesezugriffe laufen nebeneinander, und Schreibzugriffe reihen sich an der Schreibsperre ein (der Treiber
öffnet die Sperre in jedem geforkten Worker neu). In Lasttests dieses Treibers skalierten parallele Leser mit der Zahl
der Prozesse, kurze Schreibzugriffe blieben bei 16 parallelen Schreibern mit der seriellen Rate fehlerfrei, und lange
Transaktionen, die mit dem 5-Sekunden-Limit des früheren Treibers scheiterten, wurden fehlerfrei abgeschlossen und
warteten lediglich länger. Ein Team, in dem viele Redakteure im selben Moment große Änderungen veröffentlichen, ist mit
MySQL, MariaDB oder PostgreSQL dennoch besser bedient.

**Eigentümer und Berechtigungen.** SQLite muss die Datenbankdatei, die Dateien `-wal` und `-shm` daneben und das
Verzeichnis, das sie enthält, schreiben können; der Treiber legt `var/storage/sqlite3/` mit dem Modus `0775` an, setzt
aber weder Modus noch Eigentümer der Dateien, sodass diese die umask des erzeugenden Prozesses erhalten. Wenn
Apache/PHP-FPM, Velocity-Worker und Cronjobs unter verschiedenen Benutzern laufen, geben Sie ihnen eine Gruppe, die
`var/storage/sqlite3/` und jede Datei darin lesen und schreiben darf. Ein von einem anderen Benutzer erzeugtes
`.writer-lock` funktioniert trotzdem: Der Treiber öffnet es nur lesend, wenn er es nicht zum Schreiben öffnen kann.
Liefern Sie die Datenbankdatei niemals über HTTP aus; prüfen Sie das mit
`curl -sI https://www.example.com/var/storage/sqlite3/exponential.db` (alles außer 200 ist richtig).

**Sicherungen.** Verwenden Sie die Online-Sicherung von SQLite, niemals eine einfache Kopie einer Datei, die gerade in
Benutzung ist:

```bash
sqlite3 var/storage/sqlite3/exponential.db ".backup backup.db"
```

Stellen Sie wieder her, indem Sie `backup.db` zurückkopieren, während die Website im Wartungsmodus ist
(`php bin/php/maintenance.php on`, danach `off`). Führen Sie nach einer Installation oder einem großen Import einmal
`ANALYZE` aus, damit der Query-Planer Statistiken hat (`sqlite3 var/storage/sqlite3/exponential.db ANALYZE`).

Mehr dazu: [SQLite-Datenbankunterstützung](funktionen/6.0/sqlite-datenbank.md),
[SQLite-3-Treiber](spezifikationen/6.0/sqlite3-datenbanktreiber.md),
[SQLite-Transaktionen](bc/6.0/sqlite-transaktionen.md).

## 10. Nach der Installation

*Vollständiges Kapitel: [10-after-installing](installation/10-nach-der-installation.md)*

1. **Melden Sie sich** unter der Admin-Adresse, die der Installer ausgegeben hat (zum Beispiel `/admin/user/login`),
   als `admin` an. Ändern Sie das Passwort (**Passwort ändern** im Benutzermenü) und löschen Sie
   `var/log/initial-admin-password`. Ein verlorenes Passwort wird als root mit
   `php bin/php/resetuserpassword.php --allow-root-user -u admin -g` zurückgesetzt (ohne root autorisieren Sie sich mit
   einem anderen Administrator: `-a <login> -ap <password>`).
2. **Prüfen Sie die Startseite und die Protokolle.** Öffnen Sie die Adresse der Website. Lesen Sie bei einem Fehler
   `var/log/error.log` und [Eine Installation reparieren](bc/6.0/reparatur.md).
3. **Richten Sie die Cronjobs ein.** Sie veröffentlichen geplante Inhalte, versenden Benachrichtigungen und Newsletter
   und räumen auf. Listen Sie die Teile auf und fügen Sie sie dann der Crontab des Website-Benutzers hinzu:

   ```bash
   php runcronjobs.php --list
   ```

   ```
   */5 * * * * cd /path/to/installation && php runcronjobs.php --siteaccess=site frequent >> var/log/cron-frequent.log 2>&1
   17 * * * *  cd /path/to/installation && php runcronjobs.php --siteaccess=site infrequent >> var/log/cron-infrequent.log 2>&1
   ```

   Verwenden Sie den Namen Ihres öffentlichen Siteaccess. Dieselben Teile finden Sie im Admin unter
   **Setup > Cronjobs**, und der [Scheduler](funktionen/6.0/velocity-zeitplaner.md) von Velocity kann sie ohne den
   System-Cron ausführen.
4. **Kennen Sie die Cache-Befehle.** Leeren Sie das Kleinste, das das Problem behebt:

   | Sie haben geändert | Befehl |
   |---|---|
   | eine Einstellung (`.ini`) | `php bin/php/ezcache.php --clear-tag=ini` |
   | ein Template (`.tpl`) | `php bin/php/ezcache.php --clear-tag=template` |
   | Inhalte wirken veraltet | `php bin/php/ezcache.php --clear-tag=content` |
   | ein Template oder eine Extension hinzugefügt | `php bin/php/ezcache.php --clear-id=template-override` |
   | unsicher | `php bin/php/ezcache.php --clear-all` |

   Mit Velocity zusätzlich `php bin/php/console exp:velocity cache clear --apply`; nach einer Änderung an einer Klasse oder
   einer INI-Datei erledigt `exp:velocity
   deploy` all dies in der richtigen Reihenfolge.
5. **Erzeugen Sie die Autoloads neu**, nachdem Sie in einer Extension eine Klasse hinzugefügt oder umbenannt haben:
   `php bin/php/ezpgenerateautoloads.php -e` (`-k` für den Kernel).
6. **Sichern Sie** die Datenbank sowie `var/storage`, `settings/override`, `settings/siteaccess`
   ([Eine Website betreiben](anleitungen/betrieb-einer-website.md#6-sichern-und-wiederherstellen)).
7. **Die Handbücher der Hilfe.** Der Kickstarter und `exp:install` haben den Aufbau der Handbücher aus der
   Markdown-Hilfe in die Warteschlange gestellt (die letzten Zeilen ihrer Ausgabe): Ein Hintergrundjob legt sie in den
   folgenden 20 bis 30 Minuten an, und `./console exp:help:content report -s admin` zeigt "Handbücher werden erstellt
   (n von 52)", bis er fertig ist. Führen Sie nach dem Setup-Assistenten, oder wenn das Einreihen ein Problem gemeldet
   hat, `./console exp:help:content build-manuals --apply -s admin --queue` aus. Führen Sie
   `./console exp:help:content build-manuals --apply -s admin --release` nach jeder Aktualisierung aus
   ([Handbücher aus der Markdown-Hilfe](funktionen/6.0/hilfeinhalte.md#handbücher-aus-der-markdown-hilfe)).
8. **Optional: die Job-Warteschlange und Exponential Intelligence.** `composer.json` bringt die Job-Warteschlange
   `expqueue` und den Schreibassistenten `expai` nach `extension/`, beide inaktiv. Um sie zu verwenden:

   ```ini
   # settings/override/site.ini.append.php: the job queue, for every siteaccess
   [ExtensionSettings]
   ActiveExtensions[]=expqueue
   ```

   ```ini
   # settings/siteaccess/<each admin siteaccess>/site.ini.append.php: the assistant, for the administration only
   [ExtensionSettings]
   ActiveAccessExtensions[]=expai
   ```

   ```bash
   ./console ext:expqueue:install-schema
   php extension/expai/bin/php/expaischema.php --install -s admin
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/console exp:velocity deploy --packer    # or clear the caches and reload PHP-FPM
   ```

   Tragen Sie den Schlüssel des Anbieters in `settings/override/expai.ini.append.php` ein (`[AISettings] Enabled=true`,
   `ApiKey=...`; niemals in die Versionsverwaltung), geben Sie Redakteuren die Richtlinie `expai/write` und fügen Sie zwei
   Cron-Zeilen hinzu, beide mit dem Admin-Siteaccess, weil expai nur dort aktiv ist:

   ```
   * * * * *   cd /path/to/installation && php runcronjobs.php -q -s admin expqueue
   */5 * * * * cd /path/to/installation && php runcronjobs.php -q -s admin expai
   ```

   Was sie tun und jeden weiteren Schritt finden Sie unter [Exponential Intelligence](funktionen/6.0/erweiterungen/expai.md)
   und [die Job-Warteschlange](funktionen/6.0/erweiterungen/expqueue.md).

Fahren Sie dann mit den Anleitungen fort:

- [Erste Schritte](anleitungen/erste-schritte.md): erste Anmeldung, erste Inhalte, erste Template-Änderung.
- [Das Inhaltsmodell und das Bearbeiten von Inhalten](anleitungen/inhaltsmodell-und-bearbeitung.md): Klassen, Objekte, der Online-Editor, der Papierkorb und Inhalts-Jobs.
- [Templates und Design](anleitungen/templates-und-design.md): das Template finden, das eine Seite geschrieben hat, es überschreiben, das Admin-Design wechseln.
- [Extensions](anleitungen/extensions.md): eine Extension finden, installieren, konfigurieren, bauen und veröffentlichen.
- [Entfernte Dienste und Apps](anleitungen/entfernte-dienste-und-apps.md): Exponential aus der Shell, aus Python und mit einem API-Token aufrufen.
- [Deployment](anleitungen/bereitstellung.md): die Website mit Apache und PHP-FPM, Velocity oder FrankenPHP über HTTPS ausliefern.
- [Eine Website betreiben](anleitungen/betrieb-einer-website.md): Caches, Cronjobs, Sicherungen, Protokolle und Reparaturen.
- [Sicherheit und Prüfprotokoll](anleitungen/sicherheit-und-audit.md): die Härtung prüfen, Rollen und Richtlinien, das Prüfprotokoll.
- Alle Anleitungen: [der Lernpfad](anleitungen/README.md).

## 11. Aktualisierung von einer älteren Version

*Vollständiges Kapitel: [11-upgrading](installation/11-aktualisieren.md)*

Führen Sie den Installer nicht über einer bestehenden Website aus. Folgen Sie [Aktualisieren](anleitungen/aktualisieren.md): von
4.x, 5.x oder einer früheren 6.0.x auf die aktuelle Linie, mit der Datenbankaktualisierung und den Checklisten pro
Release. Eine Installation von 6.0 bringen Sie mit dem kurzen Vorgehen von
[Exponential 6.0 aktualisieren](UPDATE.md) auf den aktuellen Stand. Vergleichen Sie nach jeder Aktualisierung
`.htaccess` und `config.php` mit den neuen Vorlagen: `./console exp:setup:status`, `./console exp:setup:htaccess`
(der Diff) und `./console exp:config:list` (Einstellungen, die neu in `config.php-RECOMMENDED` sind, erscheinen als
`(not set)`). Um eine Website umzuziehen oder zu entfernen,
exportieren Sie zuerst alles mit [Exponential 6.0 deinstallieren](UNINSTALL.md).

Das Online-Handbuch enthält weitere Hinweise zur Installation:
https://exponential.doc.exponential.earth/Exponential/Technical-manual/6.x/Installation.html

## 12. Fehlerbehebung

*Vollständiges Kapitel: [12-troubleshooting](installation/12-fehlerbehebung.md)*

| Symptom | Ursache und Lösung |
|---|---|
| Jede Adresse zeigt den Einrichtungsassistenten | `CheckValidity` ist noch `true`: Die Installation wurde nicht abgeschlossen. Schließen Sie den Assistenten ab oder führen Sie den Kickstarter oder `exp:install` aus. |
| „The site is being set up" (503) | In einem anderen Browser läuft ein Assistent (die Sperre endet mit seiner letzten Seite oder 30 Minuten nach seiner letzten Anfrage), oder ein Kickstarter-Lauf baut die Datenbank neu auf. Wenn nichts läuft: `php bin/php/maintenance.php status`, dann `off`. |
| Der Assistent überspringt Seiten oder zeigt Werte, die Sie nicht eingegeben haben | Im Wurzelverzeichnis der Installation liegt eine `kickstart.ini`. Verschieben Sie sie. |
| Die Systemprüfung scheitert an Verzeichnisberechtigungen | Führen Sie die `chmod`-/`chown`-Befehle aus, die die Seite ausgibt ([Berechtigungen](#berechtigungen)), und klicken Sie dann auf **Weiter**. |
| `The CreateSites step will modify the database and site settings` | Der Kickstarter benötigt `--force`, um zu installieren. |
| `exp:install`: „This directory already holds an installation" | Fügen Sie `--force` nur hinzu, wenn Sie die Installation ersetzen wollen; `--dry-run` prüft, ohne etwas zu ändern. |
| Werte aus `kickstart.ini` werden ignoriert | Leerzeichen vor Abschnitten oder Schlüsseln, ein fehlendes `Continue=true` oder eine veraltete `var/cache/ini/kickstart-*.php`. |
| Nach einem Wechsel der Datenbank-Engine lädt keine Seite | `DatabaseImplementation` in `settings/override/site.ini.append.php` nennt noch die alte Engine. Setzen Sie es auf die neue. |
| Kickstarter: „The database file name is not valid" | Eine vor dem 5. Oktober 2026 mit `ini --yes` geschriebene `kickstart.ini` enthält `Database=ezp`, und SQLite braucht einen Namen, der auf `.db`, `.db3`, `.sqlite` oder `.sqlite3` endet. Setzen Sie `[database_init] Database` und `[site_details] Database` zum Beispiel auf `exponential.db` und `DatabaseAction` für eine neue Datenbank auf `ignore`. |
| Kickstarter: „--start-step=... cannot work" | Ein Lauf kann einen früheren nicht fortsetzen. Beheben Sie die Ursache und führen Sie ab `Welcome` aus (der Standard). |
| SQLite: „database is busy: the transaction could not start within N s" | Ein anderer Schreibzugriff hielt die Sperre während der gesamten `SQLiteTransactionWait`. Es wurde nichts geschrieben; versuchen Sie es erneut oder erhöhen Sie die Einstellung (unterhalb der Zeitüberschreitung für Anfragen). |
| 404 für jede Seite unter Apache | `.htaccess` wird nicht gelesen (`AllowOverride All`) oder `mod_rewrite` ist aus; gibt es überhaupt eine `.htaccess`? `./console exp:setup:status` sagt es, `./console exp:setup:htaccess --apply` kopiert sie. |
| `FAIL: .htaccess exists and differs ...` oder `FAIL: config.php exists and differs ...` | Die Datei enthält eigene Änderungen. Lesen Sie den Diff (`-v` zeigt ihn vollständig); `--force --apply` ersetzt sie nach einer Sicherung in `var/backup/setup/<time>/`. Siehe [.htaccess und config.php von der Konsole aus einrichten](anleitungen/setup-dateien-von-der-konsole.md). |
| `Warning: config.php does not compile` | Korrigieren Sie die genannte Zeile, oder `./console exp:setup:config --force --apply` (die defekte Datei wird vorher gesichert). |
| Eine weiße Seite oder 500 | Lesen Sie `var/log/error.log`; prüfen Sie `php -v` und ob `vendor/` existiert (`composer install`). |
| Eine Änderung erscheint unter Velocity nicht | Die Worker halten den alten Code: `exp:velocity restart` (oder `deploy`), dann `exp:velocity cache clear`. |
| Das Administratorpasswort ist verloren | Als root: `php bin/php/resetuserpassword.php --allow-root-user -u admin -g`; andernfalls autorisieren Sie sich mit einem anderen Administrator (`-a <login> -ap <password>`). Siehe [Ein Benutzerpasswort zurücksetzen](funktionen/6.0/benutzerpasswort-zuruecksetzen.md). |

Mehr dazu: [Eine Installation reparieren](bc/6.0/reparatur.md), [Wartungsmodus](funktionen/6.0/wartungsmodus.md).

## Öffentliche Demos: Demo-Modus

Eine öffentliche Demo, deren Administrator-Anmeldung veröffentlicht ist, läuft mit der Umgebungsvariable `EXP_DEMO_MODE=1`. Jeder Installer (Einrichtungsassistent, Kickstarter, `exp:install`) installiert dann das veröffentlichte Administratorpasswort so, wie es angegeben ist, auch ein allgemein bekanntes oder kurzes wie `publish` oder `publishing` (niemals ein leeres), und danach kann niemand ein Administratorpasswort ändern oder zurücksetzen, solange die Variable gesetzt ist. Setzen Sie sie niemals für eine echte Website. Siehe [Demo-Modus](bc/6.0/demomodus.md) und die [Beispiele zum Demo-Modus](beispiele/demomodus/README.md).
