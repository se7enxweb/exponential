---
title: Platform as a Service
audience: hosting
category: hosting
order: 80
summary: Was ein Angebot als Platform as a Service für Exponential bereitstellen muss (Stufe 3 in den Quellen), warum ein schreibgeschütztes oder flüchtiges Dateisystem var/ auf persistentem Speicher und einen Datenbankserver braucht, wie sich VarDir und die Pfade für Cache und Logs verhalten, und die Fragen an eine PaaS, bevor Sie Exponential darauf anbieten.
keywords: Platform as a Service, PaaS, Buildpack, schreibgeschütztes Dateisystem, flüchtiges Dateisystem, persistente Festplatte, VarDir, CacheDir, LogDir, CacheVarDir, LogVarDir, ExpiryDir, Datenbankserver, lang laufender Prozess, Cron, Velocity, PHP-FPM, platform as a service, buildpack, read-only file system, ephemeral file system, persistent disk, database server, long-running process
type: help
---
# Platform as a Service

Diese Seite ist für Hosting-Anbieter, die eine Platform as a Service (PaaS) betreiben oder weiterverkaufen und Exponential
darauf anbieten möchten. Eine PaaS legt die Laufzeitumgebung fest: Der Kunde schiebt Code hoch, die Plattform baut und
betreibt ihn, und das Dateisystem, das die Anwendung sieht, ist vielleicht schreibgeschützt oder geht bei jedem Neustart
verloren. Die Seite beantwortet, was die Quellen des Projekts über solche Plattformen sagen, welche Verzeichnisse Exponential
schreibt und wie sie verlegt werden können, und welche Fragen Sie einer Plattform stellen, bevor Sie Exponential darauf
anbieten.

## Inhalt

1. [Was die Quellen sagen](#was-die-quellen-sagen)
2. [Was Exponential zur Laufzeit schreibt](#was-exponential-zur-laufzeit-schreibt)
3. [Schreibgeschützte oder flüchtige Dateisysteme](#schreibgeschützte-oder-flüchtige-dateisysteme)
4. [Die Datenbank](#die-datenbank)
5. [Prozesse: Web, Cron und lang laufende Server](#prozesse-web-cron-und-lang-laufende-server)
6. [Bauen und veröffentlichen](#bauen-und-veröffentlichen)
7. [Fragen an eine Plattform](#fragen-an-eine-plattform)

## Was die Quellen sagen

Angebote als Platform as a Service, die PHP 8 mit einer beschreibbaren persistenten Festplatte ausführen, funktionieren
erwartungsgemäß (Stufe 3). Angebote mit einem schreibgeschützten oder flüchtigen Dateisystem brauchen `var/` auf
persistentem Speicher und einen Datenbankserver
([Container, Cloud und Hosting-Panels](../../../../en/us/help/os-support.md#containers-cloud-and-hosting-panels)).

Stufe 3 bedeutet eine begründete Erwartung, kein Testergebnis ([Die vier Stufen](../../../../en/us/help/os-support.md#the-four-tiers)): Die
Quellen nennen und testen kein bestimmtes PaaS-Produkt. Führen Sie eine vollständige Testinstallation auf der Plattform aus,
bevor Sie sie anbieten.

## Was Exponential zur Laufzeit schreibt

Der Test `directory_permissions` des Setup-Assistenten listet die Verzeichnisse auf, die beschreibbar sein müssen: `design`,
`extension`, `settings`, `settings/override`, `settings/siteaccess`, `settings/siteaccess/admin`, `var`, `var/cache` und seine
Unterverzeichnisse, `var/log`, `var/storage` und seine Unterverzeichnisse, `var/autoload`
([2.4 Die Systemprüfung des Setup-Assistenten](../../../../en/us/install/02-requirements.md#24-the-setup-wizards-system-check)).

| Pfad | Geschrieben, wenn | Muss erhalten bleiben? |
|---|---|---|
| `var/storage` | bei jedem Upload und jeder Bildvariante; die SQLite-Datenbank in `var/storage/sqlite3/` | ja |
| `var/cache`, `var/<site>/cache` | bei jeder Anfrage, die einen Cache füllt | nein, aber ein Verlust kostet einen kalten Start |
| `var/log`, `var/<site>/log` | Fehler, Anfrage-Logs, Cron-Logs | einsammeln oder behalten |
| `var/autoload` | wenn Autoloads erzeugt werden (auch durch `composer install`) | wird beim Bauen neu erzeugt |
| `settings/override`, `settings/siteaccess` | vom Installer und wenn Administratoren Einstellungen im Browser speichern | ja |
| `design/`, `extension/` | nur, solange Pakete aus der Administration installiert werden | nein, wenn zur Laufzeit nichts Pakete installiert |

Nach der Installation können Sie den Schreibzugriff auf `design/` und `extension/` wieder entziehen, wenn nichts Pakete aus der
Administration installiert; `settings/` bleibt beschreibbar, wenn Administratoren Einstellungen im Browser ändern
([8.7.1](../../../../en/us/install/08-serving-the-site.md#871-what-must-be-writable)).

> **Tipp**
> Auf einer PaaS gehören Einstellungen in das Code-Repository. Der Schalter `[ConfigurationSettings] ManagedByDeployment=enabled`
> sorgt dafür, dass die Konfiguration dieses Siteaccess nur aus dem Ausrollen (der Versionsverwaltung) kommt: Die
> Administration schreibt keine `*.ini.append.php`- oder `.tpl`-Dateien mehr auf den Server
> ([Konfiguration über das Ausrollen verwaltet](../../../../en/us/features/6.0/managed-by-deployment.md)).

## Schreibgeschützte oder flüchtige Dateisysteme

Das Verzeichnis `var` wird durch `site.ini [FileSettings] VarDir` festgelegt (mitgeliefert `var`). Der Kommentar in
`settings/site.ini` ist eindeutig: Nur Verzeichnisse relativ zum Wurzelverzeichnis von Exponential werden unterstützt;
absolute Verzeichnisse werden NICHT UNTERSTÜTZT. **Hängen Sie den persistenten Speicher daher unter `var/` innerhalb der
Installation ein**, statt `VarDir` anderswohin zeigen zu lassen.

Innerhalb von `var/` bieten die Quellen feinere Steuerung (`site.ini [FileSettings]`,
[Logs, INI-Cache und Ablaufzeitstempel pro Website](../../../../en/us/features/6.0/multi-site-log-and-cache-paths.md)):

| Einstellung | Was sie erlaubt |
|---|---|
| `LogDir` | ein absoluter Pfad wird unterstützt: Die Logs werden nie ausgeliefert |
| `CacheDir` | ein absoluter Pfad wird akzeptiert, funktioniert aber mit manchen Funktionen nicht und wird NICHT UNTERSTÜTZT: Die öffentlichen Caches darin werden nicht ausgeliefert. Um den Cache auf einem anderen Dateisystem zu halten, hängen Sie dieses unter VarDir/CacheDir ein |
| `LogVarDir`, `CacheVarDir` | ein Baum für die Logs oder Caches aller Websites |
| `ExpiryDir` | hält `expiry.php`, dessen Zeitstempel entscheiden, ob Caches und Bild-Aliase gültig sind, wenn das Cache-Verzeichnis bei einem Neustart geleert wird |

> **Warnung**
> Wenn das Cache-Verzeichnis auf einem Speicher liegt, der bei einem Neustart geleert wird, setzen Sie `ExpiryDir` auf ein
> Verzeichnis auf dem persistenten Volume. Sonst gehen die Ablaufzeitstempel mit dem Cache verloren.

## Die Datenbank

In der offiziellen Reihenfolge: 1. SQLite 3 (der Standard) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL.
Microsoft SQL Server ist derzeit bekanntermaßen mit zusätzlicher Arbeit möglich.

Mit einem schreibgeschützten oder flüchtigen Dateisystem verlangen die Quellen einen Datenbankserver. Auch mit einer
persistenten Festplatte kann eine Plattform, die vielleicht mehr als eine Instanz der Anwendung betreibt, keine SQLite-Datei
zwischen ihnen teilen. Nutzen Sie die verwaltete Datenbank der Plattform, zuerst **2. PostgreSQL**, wenn sie mehrere anbietet
([Datenbankunterstützung](../../../../en/us/help/database-support.md#the-supported-databases-in-order)).

## Prozesse: Web, Cron und lang laufende Server

- **Web.** Wenn die Plattform PHP hinter ihrem eigenen Webserver ausführt (nach Art von PHP-FPM), müssen die Rewrite-Regeln
  aus `.htaccess_root` für diesen Server übersetzt werden: Nur eine feste Liste von Asset-Pfaden wird als Dateien
  ausgeliefert; alles andere geht an `index.php`
  ([13.2](../../../../en/us/install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out)). Eine allgemeine
  Konfiguration „Datei ausliefern, wenn sie existiert“ würde `settings/*.ini` ausliefern.
- **Velocity** braucht einen lang laufenden Prozess und PHP 8.1 mit `sockets`. Eine Plattform, die einen Prozess pro
  Container ausführt und HTTP an einen Port leitet, kann es betreiben; eine, die PHP nur pro Anfrage ausführt, nicht.
- **Cron.** `runcronjobs.php` muss nach Zeitplan laufen, einmal pro Website, mit demselben Code und denselben Einstellungen
  ([Cronjobs](../betrieb/cronjobs.md)). Fragen Sie, ob die Plattform einen Planer hat.
- **Sitzungen.** Mit mehr als einer Instanz müssen Sitzungen geteilt werden; siehe [Kubernetes, Sitzungen](kubernetes.md#sitzungen).

## Bauen und veröffentlichen

- Bauen: `composer install` (das über das Skript `legacy-scripts` auch `bin/php/ezpgenerateautoloads.php` ausführt)
  ([2.7 Composer](../../../../en/us/install/02-requirements.md#27-composer)).
- Einmal installieren, gegen die verwaltete Datenbank, mit `exp:install` und Geheimnissen aus der Umgebung
  (`EXP_INSTALL_DB_PASSWORD`) ([4.6](../../../../en/us/install/04-choosing-an-install-method.md#46-unattended-installs-ci-and-containers)).
- Jedes spätere Release: neuer Code, dasselbe persistente `var/` und dieselbe Datenbank, die Schema-Update-Dateien, wenn ein
  Upgrade sie verlangt, dann ein Leeren des Caches ([Änderungen ausrollen](../betrieb/aenderungen-ausrollen.md)).

## Fragen an eine Plattform

| Frage | Warum |
|---|---|
| Welche PHP-Versionen und Erweiterungen, und kann ich `intl`, `xsl` und eine Datenbankerweiterung hinzufügen? | die erforderliche Ausstattung ([2.3](../../../../en/us/install/02-requirements.md#23-php-extensions)) |
| Gibt es eine persistente Festplatte, und kann sie unter `var/` innerhalb der Anwendung eingehängt werden? | `VarDir` muss relativ sein |
| Wie viele Instanzen dürfen gleichzeitig laufen? | ein Schreiber für SQLite; sonst gemeinsame Sitzungen und gemeinsamer Speicher |
| Gibt es einen Planer? | `runcronjobs.php` |
| Darf ein lang laufender Prozess an einem Port lauschen? | Velocity |
| Kann ich die Rewrite-Regeln des Webservers konfigurieren? | die Asset-Liste aus `.htaccess_root` |
| Welche Datenbanken werden als verwaltete Dienste angeboten? | die offizielle Reihenfolge |
| Wie werden Backups der Festplatte und der Datenbank erstellt und wiederhergestellt? | der Backup-Satz: Datenbank, `var/storage`, `settings/override`, `settings/siteaccess` |

## Verwandte Seiten

- [Ein Hosting-Modell wählen](ein-hosting-modell-waehlen.md), [Container: Docker und Podman](container-docker-und-podman.md)
- [Beschreibbare Verzeichnisse und Berechtigungen](../grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md)
- [Aufbau des Speichers](../betrieb/aufbau-des-speichers.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
