---
title: Was das Hosting von Exponential umfasst
audience: hosting
category: hosting
order: 20
summary: Die Teile, die ein Hosting-Anbieter für eine Exponential-Website bereitstellt (PHP für Web und Kommandozeile, eine Datenbank, einen Webserver oder Velocity, Cron, Mail, beschreibbaren Speicher, Backups und TLS), was dauerhaft läuft und die Möglichkeiten, die Arbeit zwischen Hoster und Kunde aufzuteilen.
keywords: Hosting, Exponential-Hosting, was nötig ist, Komponenten, PHP, PHP-FPM, Velocity, Datenbank, Cron, runcronjobs, Mail, Speicher, Backups, TLS, Zuständigkeiten, Managed Hosting, Shared Hosting, Angebot, hosting, Exponential hosting, what is needed, components, storage, responsibilities, managed hosting, shared hosting, offer
type: help
---
# Was das Hosting von Exponential umfasst

Diese Seite ist für Produktmanager und Systemadministratoren bei einem Hosting-Anbieter, die demnächst Hosting für
Exponential anbieten und zuerst das Gesamtbild wollen. Sie beantwortet: was Exponential aus Sicht eines Servers ist,
welche Teile der Hoster bereitstellt, was nach der Installation weiterläuft und wie die Arbeit zwischen Hoster und Kunde
aufgeteilt werden kann. Die Details zu jedem Teil stehen auf den anderen Seiten der [Grundlagen des Hostings](README.md).

## Inhalt

1. [Exponential aus Sicht des Servers](#exponential-aus-sicht-des-servers)
2. [Die Teile, die ein Hoster bereitstellt](#die-teile-die-ein-hoster-bereitstellt)
3. [Was nach der Installation weiterläuft](#was-nach-der-installation-weiterläuft)
4. [Wie eine Website installiert wird](#wie-eine-website-installiert-wird)
5. [Die Arbeit aufteilen: Möglichkeiten](#die-arbeit-aufteilen-möglichkeiten)
6. [Checkliste](#checkliste)
7. [Verwandte Seiten](#verwandte-seiten)

## Exponential aus Sicht des Servers

Exponential ist ein quelloffenes Content-Management-System, in PHP geschrieben und unter der GNU GPL veröffentlicht
(`composer.json`: `"license": "GPL-2.0-or-later"`). Es wird von 7x gepflegt ([Wer ist 7x?](../../wer-ist-7x.md)) und führt
eine Codebasis fort, deren ursprüngliches Produkt sein Hersteller nach fast 18 Jahren eingestellt hat.

Für einen Server ist eine Installation:

- **Reine PHP-Dateien** in einem Verzeichnis (der Kernel, `lib/`, `extension/`, `design/`, `vendor/`), mit einem
  Front-Controller, `index.php`, durch den jede Seitenanfrage geht
  ([Kapitel 3](../../../installation/03-den-code-beschaffen.md#36-ein-rundgang-durch-das-installationsverzeichnis)).
- **Eine Datenbank**: eine von sechs unterstützten Engines, standardmäßig SQLite 3, das überhaupt keinen Datenbankserver
  braucht ([Datenbanken in der offiziellen Reihenfolge](datenbanken-in-der-offiziellen-reihenfolge.md)).
- **Ein beschreibbarer Laufzeitbaum**, `var/`, mit Caches, Logs, hochgeladenen Dateien und Bildvarianten und (mit SQLite)
  der Datenbankdatei, dazu `settings/override/` und `settings/siteaccess/` mit den eigenen Einstellungen der Installation.
- **Kommandozeilenwerkzeuge**: die Konsole `bin/php/console`, die Skripte in `bin/php/` und `runcronjobs.php`, das die
  regelmäßigen Jobs ausführt.

Es hat keinen eigenen kompilierten Code und läuft daher überall, wo ein unterstütztes PHP läuft
([Betriebssysteme für Hoster](betriebssysteme-fuer-hoster.md)).

## Die Teile, die ein Hoster bereitstellt

| Teil | Was Exponential braucht | Seite |
|---|---|---|
| PHP für das Web | 8.0 oder später (8.1 oder später für Velocity), mit den erforderlichen Erweiterungen | [PHP-Versionen und Erweiterungen](php-versionen-und-erweiterungen.md) |
| PHP auf der Kommandozeile | dieselbe Version und dieselben Erweiterungen wie das Web-PHP | [PHP-Versionen und Erweiterungen](php-versionen-und-erweiterungen.md) |
| `php.ini` | die Werte, die der Installer prüft, bemessen für Uploads und Importe | [PHP-Einstellungen für Exponential](php-einstellungen-fuer-exponential.md) |
| Ein Webserver | Exponential Velocity (empfohlen), oder Apache, nginx oder ein anderer Server mit PHP-FPM | [Einen Webserver wählen](../server/einen-webserver-waehlen.md) |
| Eine Datenbank | SQLite 3 braucht nur die Erweiterung `sqlite3`; die anderen brauchen einen Server und ein Konto | [Datenbanken in der offiziellen Reihenfolge](datenbanken-in-der-offiziellen-reihenfolge.md) |
| Composer | um den Code und seine Erweiterungen zu installieren und zu aktualisieren | [Composer und Git auf Servern](composer-und-git-auf-servern.md) |
| Beschreibbarer Speicher | `var/` und `settings/` für den Benutzer der Website beschreibbar | [Beschreibbare Verzeichnisse und Berechtigungen](beschreibbare-verzeichnisse-und-berechtigungen.md) |
| Cron | `runcronjobs.php` in regelmäßigen Abständen | [Cronjobs](../betrieb/cronjobs.md) |
| Mail | ein Weg zum Versenden: sendmail oder SMTP | [Mailzustellung](../betrieb/mailzustellung.md) |
| TLS | ein Zertifikat für jede Adresse der Website | [TLS und HTTPS](../sicherheit/tls-und-https.md) |
| Backups | die Datenbank und die Dateien, außerhalb der Maschine aufbewahrt | [Was gesichert werden muss](../betrieb/backups-was-gesichert-werden-muss.md) |

> **Hinweis**
> Während einer Installation über die Kommandozeile (der Kickstarter oder `exp:install`) ist kein Webserver nötig; der
> Setup-Assistent braucht einen, weil er im Browser läuft ([Kapitel 2](../../../installation/02-voraussetzungen.md#28-webserver)).

## Was nach der Installation weiterläuft

Eine Exponential-Website ist nicht fertig, wenn der Installer endet. Diese Dinge laufen das ganze Leben der Website lang:

| Was | Warum | Wo es beschrieben ist |
|---|---|---|
| **Cronjobs** | Benachrichtigungen, Suchindizierung bei verzögerter Indizierung, Linkprüfungen, Bereinigung von Caches und Sitzungen | [Kapitel 10, Cronjobs](../../../installation/10-nach-der-installation.md#103-cronjobs) |
| **Velocity**, wenn es die Website ausliefert | ein lang laufender PHP-Serverprozess mit einem Pool von Workern | [Velocity für Hoster](../server/velocity-fuer-hoster.md) |
| **PHP-FPM**, wenn Apache oder nginx die Website ausliefert | ein Pool pro Website ist üblich | [Abstimmung von PHP-FPM](../leistung/abstimmung-von-php-fpm.md) |
| **Ein Datenbankserver**, außer bei SQLite | PostgreSQL, MongoDB, MariaDB, Oracle oder MySQL | die Seite der jeweiligen Datenbank |
| **Logs** | `var/log/` und die eigenen Logs des Webservers wachsen und brauchen Rotation | [Log-Viewer und Log-Rotation](../betrieb/log-viewer-und-log-rotation.md) |
| **Caches** | `var/cache/` wächst und wird nach Änderungen geleert | [Caches leeren und vorwärmen](../leistung/caches-leeren-und-vorwaermen.md) |

Die Beispiel-Crontab in [Kapitel 10](../../../installation/10-nach-der-installation.md#eine-crontab) führt den Teil `frequent` alle
5 Minuten aus, den Standard-Teil alle 15 Minuten, `infrequent` stündlich und die Bereinigungsteile einmal pro Nacht, alle
aus der Crontab des Benutzers der Website.

> **Warnung**
> Hosting, das lang laufende Prozesse verbietet, kann Velocity nicht ausführen; dort liefert Apache oder nginx mit
> PHP-FPM die Website aus. Hosting ohne Cron kann die regelmäßigen Jobs nicht ausführen, und Benachrichtigungen und
> verzögerte Suchindizierung finden dann nie statt. Entscheiden Sie beides, bevor Sie ein Angebot gestalten.

## Wie eine Website installiert wird

Jede Installationsmethode führt dieselben Schritte des Installers aus; sie unterscheiden sich nur darin, woher die
Antworten kommen ([Kapitel 4](../../../installation/04-installationsweg-waehlen.md#41-ein-installer-drei-frontends)):

| Methode | Antworten aus | Passt zu |
|---|---|---|
| Setup-Assistent | Formularen im Browser | Shared Hosting ohne Shell-Zugang |
| Kickstarter | einer Datei `kickstart.ini` | wiederholbaren Installationen, Bereitstellungsskripten |
| Installation über die Konsole (`exp:install`) | Kommandozeilenoptionen | Automatisierung, Containern, Installationen mit einem Befehl |

Alle drei führen zuerst dieselben kritischen Systemtests aus und halten mit Ursache und Abhilfe an, wenn die Maschine
nicht bereit ist. Für einen Hosting-Anbieter sind der Kickstarter und `exp:install` die natürlichen Werkzeuge für die
automatische Bereitstellung; siehe [Ein-Klick-Installer](../bereitstellung/ein-klick-installer-fuer-exponential.md) und
[exp:install](../bereitstellung/exp-install-fuer-hoster.md).

## Die Arbeit aufteilen: Möglichkeiten

Das Repository schreibt nicht vor, wie ein Hoster und seine Kunden die Arbeit teilen. Dies sind die üblichen
Möglichkeiten; wählen Sie pro Tarif, und schreiben Sie die Wahl in die Beschreibung des Tarifs.

| Aufgabe | Der Hoster übernimmt sie | Der Kunde übernimmt sie |
|---|---|---|
| Betriebssystem, PHP, Webserver, TLS | üblich in jedem Modell | nur auf nicht verwalteten Servern |
| Datenbankserver und Konten | üblich | auf einem eigenen Server des Kunden |
| Exponential installieren | als Bereitstellungsoption | mit dem Setup-Assistenten oder der Konsole |
| Exponential und Erweiterungen aktualisieren | als verwaltete Option ([automatische Updates](../betrieb/automatische-updates-mit-ezupdate.md)) | mit Composer oder der Erweiterung `ezupdate` |
| Cron | der Hoster richtet die Crontab ein | der Kunde, wo das Panel es erlaubt |
| Backups | üblich, mit Wiederherstellungstests | zusätzlich, für die eigene Kopie |
| Inhalte, Templates, eigene Erweiterungen | selten | üblich |

> **Tipp**
> Halten Sie für jeden Tarif fest, welche dieser Zeilen wem gehört. Die meisten Support-Fragen zu Exponential-Websites
> kommen aus einer Zeile, die niemand für seine hielt: ein Cron, der nie eingerichtet wurde, oder ein PHP-Upgrade, das
> das PHP der Kommandozeile hinter dem Web-PHP zurückließ.

## Checkliste

- [ ] PHP 8.0 oder später für Web und Kommandozeile, in beiden dieselbe Version und dieselben Erweiterungen.
- [ ] PHP 8.1 oder später mit `sockets`, `pcntl` und `openssl`, wo Velocity angeboten wird.
- [ ] Ein Datenbankangebot aus der offiziellen Reihenfolge gewählt.
- [ ] `var/` und `settings/` für den Benutzer der Website beschreibbar; ein Benutzer oder eine Gruppe für jeden Prozess.
- [ ] Cron verfügbar, um `runcronjobs.php` auszuführen.
- [ ] Mailversand konfiguriert (sendmail oder SMTP).
- [ ] TLS für jede Adresse der Website.
- [ ] Backups der Datenbank und der Dateien, mit einer getesteten Wiederherstellung.
- [ ] Die Aufteilung der Arbeit in jedem Tarif festgehalten.

## Verwandte Seiten

- [Grundlagen des Hostings](README.md) und [Hosting-Anbieter: hier beginnen](../hosting-anbieter-hier-beginnen.md)
- [Anforderungen auf einen Blick](anforderungen-auf-einen-blick.md)
- [Ein Hosting-Modell wählen](../modelle/ein-hosting-modell-waehlen.md)
- [Kapitel 2: Anforderungen](../../../installation/02-voraussetzungen.md) und
  [Kapitel 10: Nach der Installation](../../../installation/10-nach-der-installation.md)
- [Hosting, Datenbank und Betriebssystem wählen](../../fuehrung/hosting-datenbank-und-betriebssystem-waehlen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
