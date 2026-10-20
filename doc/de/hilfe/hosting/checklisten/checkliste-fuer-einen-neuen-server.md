---
title: Checkliste für einen neuen Server
audience: hosting, admin
category: hosting
order: 20
summary: Eine Checkliste für den Aufbau eines Servers, der Exponential-Websites hosten wird - Betriebssystem, PHP und Erweiterungen, php.ini, Datenbank, Engine oder Webserver, TLS, Benutzer und Berechtigungen, Cron, Mail, Backups, Überwachung und eine Testinstallation.
keywords: Checkliste, neuer Server, Aufbau, Bereitstellung, Betriebssystem, PHP, Erweiterungen, php.ini, OPcache, Datenbank, Velocity, PHP-FPM, Apache, nginx, TLS, Firewall, Benutzer, Berechtigungen, Cron, Mail, Backup, Überwachung, checklist, new server, build, provisioning, operating system, extensions, database, users, permissions, monitoring
type: help
---
# Checkliste für einen neuen Server

Diese Checkliste ist für Systemadministratoren, die eine Maschine aufbauen, die Exponential-Websites hosten wird. Arbeiten
Sie sie von oben nach unten ab; jeder Punkt verlinkt die Seite mit den Details. Die Reihenfolge zählt: Die
Testinstallation am Ende prüft das meiste, was davor kommt.

```text
Server: <name>          Built by: <name>          Date: <date>          Plan(s): <plans this server serves>
```

## Betriebssystem

- [ ] Ein System der Stufe 1 oder 2 aus [Betriebssystemunterstützung](../../../../en/us/help/os-support.md) (zum Beispiel AlmaLinux 9,
      Red Hat Enterprise Linux 9 oder 10 und Nachbauten, Debian 12 oder 13, Ubuntu 22.04 oder 24.04).
- [ ] Sicherheitsupdates eingespielt; über automatische Sicherheitsupdates entschieden.
- [ ] Zeitsynchronisation läuft; Zeitzone gesetzt.
- [ ] Firewall: nur die Ports, die die Engine oder der Webserver, SSH und Ihre Überwachung brauchen.
- [ ] SSH: nur Schlüssel, keine Passwortanmeldung für root.

## PHP

- [ ] PHP 8.0 oder später; 8.1 oder später, wenn Velocity Websites ausliefern wird; die Empfehlung ist das neueste 8.x,
      das das System anbietet ([PHP-Versionen und Erweiterungen](../grundlagen/php-versionen-und-erweiterungen.md)).
- [ ] Die erforderlichen Erweiterungen installiert; die Systemprüfung des Setup-Assistenten oder der Schritt
      `SystemCheck` von `exp:install` meldet keine fehlende ([Kapitel 2](../../../../en/us/install/02-requirements.md#23-php-extensions)).
- [ ] Für Velocity: `sockets`, dazu `pcntl` und `openssl`; `php bin/php/console exp:velocity ext check --allow-root-user`
      listet auf, was fehlt.
- [ ] `php.ini`: `memory_limit` mindestens 64M, `max_execution_time` 30 oder mehr, `date.timezone` gesetzt, `file_uploads`
      an ([PHP-Einstellungen für Exponential](../grundlagen/php-einstellungen-fuer-exponential.md)).
- [ ] OPcache für das Web-PHP an ([OPcache](../leistung/opcache.md)).
- [ ] 64-Bit-PHP (für die Unterstützung von mehr als 30 Sprachen, [Betriebssystemunterstützung](../../../../en/us/help/os-support.md)).
- [ ] Composer 2 installiert ([Composer und Git auf Servern](../grundlagen/composer-und-git-auf-servern.md)).
- [ ] GD oder `convert` von ImageMagick, beides empfohlen ([Bildverarbeitung und Medien](../grundlagen/bildverarbeitung-und-medien.md)).

## Datenbank

- [ ] Die Engine in der offiziellen Reihenfolge gewählt: SQLite 3 (kein Server nötig), PostgreSQL, MongoDB, MariaDB,
      Oracle, MySQL ([Datenbanken in der offiziellen Reihenfolge](../grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md)).
- [ ] Für eine Server-Engine: eine Version auf oder über dem praktischen Minimum auf
      [Datenbankunterstützung](../../../../en/us/help/database-support.md#versions-checked-against-the-code).
- [ ] Eine Datenbank und ein Benutzer pro Kunden-Website; kein gemeinsamer Superuser.
- [ ] Das eigene Backup-Werkzeug der Datenbank getestet ([Datenbank-Backups je Engine](../betrieb/datenbank-backups-je-engine.md)).

## Engine oder Webserver

- [ ] Entschieden: Velocity, FrankenPHP oder Apache/nginx mit PHP-FPM ([Einen Webserver wählen](../server/einen-webserver-waehlen.md)).
- [ ] Mit PHP-FPM: ein Pool pro Website, der als Systembenutzer der Website läuft ([Abstimmung von PHP-FPM](../leistung/abstimmung-von-php-fpm.md)).
- [ ] Mit Velocity: die Engine für die Website auf `qbix` gesetzt (die mitgelieferte Standard-Engine ist der eingebaute
      Server von PHP, für die Entwicklung) ([Velocity für Hoster](../server/velocity-fuer-hoster.md)).
- [ ] Nur die Asset-Pfade werden als Dateien ausgeliefert; die Rewrite-Regeln aus dem Installationsbuch sind eingerichtet
      ([Kapitel 8](../../../../en/us/install/08-serving-the-site.md)).

## TLS

- [ ] Zertifikate werden automatisch ausgestellt und erneuert ([TLS und HTTPS](../sicherheit/tls-und-https.md)).
- [ ] HTTP leitet auf HTTPS um.

## Benutzer und Berechtigungen

- [ ] Ein Systembenutzer pro Kunden-Website ([Benutzer und Pools pro Kunde](../sicherheit/benutzer-und-pools-pro-kunde.md)).
- [ ] Beschreibbare Verzeichnisse so, wie die Prüfung `directory_permissions` des Setups sie auflistet, nicht mehr
      ([Beschreibbare Verzeichnisse und Berechtigungen](../grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md)).

## Cron, Mail, Logs

- [ ] Eine Crontab pro Website, die `runcronjobs.php` ausführt ([Cronjobs](../betrieb/cronjobs.md)).
- [ ] Ausgehende Mail mit einer Absenderdomain, die SPF- und DKIM-Prüfungen besteht ([Mailzustellung](../betrieb/mailzustellung.md)).
- [ ] Über die Log-Rotation entschieden ([Log-Viewer und Log-Rotation](../betrieb/log-viewer-und-log-rotation.md)).

## Backups und Überwachung

- [ ] Backups der Datenbank und von `var/storage`, `settings/override`, `settings/siteaccess`, außerhalb der Maschine
      gespeichert ([Was gesichert werden muss](../betrieb/backups-was-gesichert-werden-muss.md)).
- [ ] Externe Verfügbarkeitsprüfung und Alarme für Festplatte, Speicher und Last ([Überblick über die Überwachung](../betrieb/ueberblick-ueber-die-ueberwachung.md)).

## Testinstallation

- [ ] `exp:install` (oder der Setup-Assistent) läuft auf diesem Server durch ([Ein-Klick-Installer](../bereitstellung/ein-klick-installer-fuer-exponential.md)).
- [ ] Startseite und Administration laden über HTTPS.
- [ ] Ein Upload, eine Veröffentlichung und eine Suche funktionieren.
- [ ] Ein Backup der Test-Website wird auf einer anderen Maschine wiederhergestellt.
- [ ] Die Testinstallation ist entfernt oder bleibt als nicht öffentliche Referenz-Website erhalten.

**Demomodus.**

- [ ] Kein serverweites `EXP_DEMO_MODE` (`/etc/environment`, ein globales `SetEnv`, der Standard-Pool von PHP-FPM, ein Basis-Image): Der Demomodus wird nur pro öffentlicher Demo gesetzt ([Demomodus für Hoster](../sicherheit/demomodus-fuer-hoster.md)).

## Verwandte Seiten

- [Anforderungen auf einen Blick](../grundlagen/anforderungen-auf-einen-blick.md)
- [Härtung für Hoster](../sicherheit/haertung-fuer-hoster.md)
- [Go-Live einer neuen Kunden-Website](checkliste-fuer-den-start-einer-kunden-website.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
