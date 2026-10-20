---
title: Technische Checkliste für Hosting-Partner
audience: partner
category: partnership
order: 390
summary: Eine technische Checkliste für Hosting-Partner, die eine Plattform für Exponential 6 vorbereiten - PHP und Erweiterungen, php.ini, Datenbanken, Engines, Dateiberechtigungen, Cronjobs, Caches, TLS und Header, Backups, Überwachung, Updates - jeder Punkt mit der Dokumentation verlinkt, die ihn erklärt.
keywords: Hosting, Checkliste, PHP, Erweiterungen, php.ini, Datenbank, Velocity, PHP-FPM, Berechtigungen, Cron, Cache, TLS, Header, Backup, Überwachung, Updates, hosting, checklist, extensions, database, permissions, monitoring
type: help
---
# Technische Checkliste für Hosting-Partner

Diese Seite ist für Systemadministratoren bei Hosting-Partnern. Nutzen Sie sie, wenn Sie ein Hosting-Produkt für
Exponential bauen oder ein bestehendes prüfen. Jeder Abschnitt nennt das Kapitel der Dokumentation, das ihn erklärt.

## PHP

Quelle: `doc/en/us/install/02-requirements.md`.

- [ ] Eine unterstützte PHP-Version: 8.0 bis 8.5 für die Linie 6.0.15 (Velocity braucht 8.1 oder später)
- [ ] 64-Bit-Build von PHP
- [ ] Benötigte Erweiterungen sowohl im Web-PHP als auch im CLI-PHP vorhanden: `dom`, `simplexml`, `xsl`, `mbstring`,
      `iconv`, `intl`, `zlib` und mindestens eine Datenbankerweiterung
- [ ] `php -v` und `php -m` auf der Kommandozeile stimmen mit den PHP-Informationen des Webservers überein
- [ ] php.ini: `memory_limit` 64M oder mehr, `max_execution_time` 30 oder mehr, `date.timezone` gesetzt, `file_uploads`
      an, `allow_url_fopen` an

## Datenbank

Quelle: [Datenbankunterstützung](../../../en/us/help/database-support.md).

- [ ] Die Datenbank in der Reihenfolge gewählt, die das Projekt bevorzugt: SQLite 3, PostgreSQL, MongoDB, MariaDB,
      Oracle, MySQL
- [ ] Die passende PHP-Erweiterung: `sqlite3`, `pgsql`, `mongodb` (plus die Bibliothek `mongodb/mongodb`), `mysqli`
      oder `oci8`
- [ ] Zeichensatz, wie ihn die Datenbankseite verlangt
- [ ] Verfahren für Backup und Wiederherstellung getestet

## Engine und Webserver

Quellen: `doc/en/us/install/08-serving-the-site.md`, [Sicherheitshärtung](../../../en/us/help/security-hardening.md).

- [ ] Engine gewählt: Velocity, FrankenPHP oder ein Webserver mit PHP-FPM
- [ ] Nur die öffentlichen Einstiegspunkte sind erreichbar; interne Verzeichnisse werden nicht ausgeliefert
- [ ] Der Setup-Assistent und die Installer-Dateien sind nach der Installation nicht erreichbar
- [ ] Der Webbenutzer und jeder CLI-/Cron-Benutzer können beide ohne Berechtigungskonflikte in `var/` schreiben

## Geplante Aufgaben

- [ ] Cronjobs der Installation laufen als Benutzer der Website, nicht als root
      ([Sicherheitshärtung](../../../en/us/help/security-hardening.md#cron-and-command-line-scripts-as-root))
- [ ] Logs der Cron-Läufe werden aufbewahrt und rotiert

## Sicherheit

Quelle: [Sicherheitshärtung](../../../en/us/help/security-hardening.md), `doc/en/us/install/13-security-hardening.md`.

- [ ] TLS auf jedem Hostnamen, mit automatischer Erneuerung
- [ ] Sicherheits-Header gesetzt; die Content Security Policy der Administration nicht abgeschwächt
- [ ] Debug-Ausgabe im Produktivbetrieb aus
- [ ] Standardpasswörter geändert ([Standardpasswort des Administrators](../../../en/us/help/default-administrator-password.md))
- [ ] Getrennte Benutzer pro Kunden-Website

## Backups und Wiederherstellung

- [ ] Datenbank und `var/storage` (hochgeladene Dateien) nach Zeitplan gesichert
- [ ] Kopie an einem anderen Standort
- [ ] Mindestens jedes Quartal eine Wiederherstellung getestet, mit notierter Dauer

## Überwachung

- [ ] Verfügbarkeitsprüfung der Startseite und der Anmeldeseite der Administration
- [ ] Alarme für den Speicherplatz von `var/`
- [ ] Fehlerlog überwacht ([Wenn etwas schiefgeht](../../../en/us/help/when-something-goes-wrong.md))

## Updates

- [ ] Sicherheitsupdates für Betriebssystem und PHP nach Zeitplan eingespielt
- [ ] Eine Staging-Kopie jeder Kunden-Website zum Testen von Exponential-Releases ([Releases testen](releases-testen.md))
- [ ] Ein dokumentierter Weg zur nächsten PHP-Version, bevor der Support der aktuellen endet

**Demomodus.**

- [ ] Öffentliche Demos laufen im Demomodus (`EXP_DEMO_MODE=1` im eigenen Pool, virtuellen Host, Unit und Crontab der Demo); keine Kunden-Website hat die Variable, und Zeilen `demo/mode` im `security.log` eines Kunden lösen einen Alarm aus ([Demomodus für Hoster](../hosting/sicherheit/demomodus-fuer-hoster.md)).

## Verwandte Seiten

- [Hosting-Partner](hosting-partner.md)
- [Betriebssystemunterstützung](../../../en/us/help/os-support.md)
- [Sicherheitspflichten von Partnern](sicherheitspflichten.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
