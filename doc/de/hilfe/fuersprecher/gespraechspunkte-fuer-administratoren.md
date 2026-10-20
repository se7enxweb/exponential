---
title: Gesprächspunkte für Administratoren
audience: advocate
category: advocacy
order: 13
summary: Gesprächspunkte für Systemadministratoren und Hosting-Fachleute zu Installation, Datenbanken, Betriebssystemen, Engines, Härtung, Logs, dem Prüfprotokoll, Upgrades und der Upgrade-Prüfung, jeweils mit Quelle und einem Befehl oder Bildschirm zum Zeigen.
keywords: Gesprächspunkte, Administratoren, Systemadministration, Hosting, Installation, SQLite, PostgreSQL, Datenbanken, Betriebssysteme, Velocity, FrankenPHP, PHP-FPM, Härtung, Logs, Prüfprotokoll, Upgrade-Prüfung, Backups, talking points, administrators, sysadmin, hosting, install, databases, operating systems, hardening, logs, audit trail, upgrade check, backups
type: help
---
# Gesprächspunkte für Administratoren

Diese Seite ist für Fürsprecher, die mit den Menschen sprechen, die Websites installieren, betreiben und absichern:
Systemadministratoren, Hosting-Personal, DevOps-Leute. Sie wollen wissen, was es braucht, was schiefgehen kann und wie
sie es sehen werden.

## Inhalt

1. [Was Administratoren wissen wollen](#was-administratoren-wissen-wollen)
2. [Die Punkte](#die-punkte)
3. [Befehle und Bildschirme zum Zeigen](#befehle-und-bildschirme-zum-zeigen)
4. [Fragen, die Administratoren stellen](#fragen-die-administratoren-stellen)
5. [Sagen Sie nicht](#sagen-sie-nicht)

## Was Administratoren wissen wollen

- „Was braucht es, und läuft es auf dem, was wir haben?“
- „Wie installiere, aktualisiere und sichere ich es?“
- „Wie härte ich es und sehe Angriffe?“
- „Was tue ich um 3 Uhr nachts, wenn es kaputtgeht?“

## Die Punkte

| Sagen Sie | Quelle |
|---|---|
| „Ein Befehl installiert eine vollständige Website auf SQLite, ohne Datenbankserver.“ | [Exponential mit einem Befehl installieren](../../../en/us/features/6.0/install-in-one-command.md) |
| „Sechs Datenbanken: SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle und MySQL, in dieser offiziellen Reihenfolge.“ | [Datenbankunterstützung](../../../en/us/help/database-support.md#the-supported-databases-in-order) |
| „PHP 8.0 ist die älteste unterstützte Version, sodass das mitgelieferte PHP von Red Hat Enterprise Linux 9 es ausführt.“ | [PHP-Versionen](../../../en/us/help/who-is-the-exponential-foundation.md#php-versions) |
| „Betriebssysteme in vier dokumentierten Stufen, von getestet bis theoretisch.“ | [Betriebssystemunterstützung](../../../en/us/help/os-support.md#the-four-tiers) |
| „Velocity ist die empfohlene Engine; FrankenPHP ist die produktionsreife Alternative; Apache oder nginx mit PHP-FPM liefern es ebenfalls aus.“ | [Velocity](../../../en/us/features/6.0/velocity-persistent-worker-server.md#why-you-would-use-it), [Webserver](../../../en/us/help/os-support.md#web-servers) |
| „Eine Anleitung zur Härtung, Bedrohung für Bedrohung, mit einer Checkliste.“ | [Sicherheitshärtung](../../../en/us/help/security-hardening.md#checklist) |
| „Eine neue Installation erzeugt ein zufälliges Administratorpasswort, wenn keines oder ein allgemein bekanntes angegeben wird.“ | [Das Standardpasswort des Administrators](../../../en/us/help/default-administrator-password.md) |
| „Sondierungen und Angriffe werden in ein Sicherheitslog eingeordnet, mit maskierten Adressen.“ | [Request-Logs](../../../en/us/features/6.0/request-logs.md) |
| „Ein manipulationssicheres Prüfprotokoll, standardmäßig eingeschaltet.“ | [Das Prüfprotokoll](../../../en/us/features/6.0/audit-trail.md) |
| „Ein Log-Viewer in der Administration und Fehlerreferenzen, die man nachschlagen kann.“ | [Log-Viewer](../../../en/us/features/6.0/log-viewer.md), [Einen Fehler über seine Referenz finden](../../../en/us/guides/finding-an-error-by-its-reference.md) |
| „**Setup > Upgrade-Prüfung** vergleicht die Dateien mit dem Release-Manifest und die Datenbank mit dem Release-Schema.“ | [Die Upgrade-Prüfung](../../../en/us/guides/upgrade-check.md) |
| „Große Löschungen und Verschiebungen laufen als Hintergrundjobs, sodass sie nicht in eine Zeitüberschreitung laufen.“ | [Inhaltsjobs](../../../en/us/features/6.0/content-jobs.md) |
| „Standard-Dumps pro Datenbank und dokumentierte Wege, eine Website zwischen Datenbanken umzuziehen.“ | [Backup und Wiederherstellung](../../../en/us/help/database-support.md#backup-and-restore), [Eine Website umziehen](../../../en/us/help/database-support.md#moving-a-site-to-another-database) |

## Befehle und Bildschirme zum Zeigen

```bash
./console exp:install --dry-run         # what an install would do, nothing written
./console exp:velocity status           # every engine, its state and URL
./console exp:audit status              # the audit trail on the command line
```

Bildschirme: **Setup > Systeminformationen** (die Datenbank, PHP und die Engine), **Setup > Logdateien**,
**Setup > Upgrade-Prüfung**, der Reiter **Audit**.

Prüfen Sie die Befehlsnamen auf der Installation, die Sie verwenden; `--help` bei jedem Befehl listet seine Optionen
([Velocity](../../../en/us/features/6.0/velocity-persistent-worker-server.md#start-check-stop),
[Das Prüfprotokoll, Auf der Kommandozeile](../../../en/us/features/6.0/audit-trail.md#from-the-command-line)).

## Fragen, die Administratoren stellen

| Frage | Antwort | Quelle |
|---|---|---|
| „Läuft es auf Shared Hosting?“ | Apache oder LiteSpeed mit `.htaccess` können es ausliefern; ohne Shell-Zugang installiert es der Setup-Assistent. Velocity kann dort meist nicht laufen. | [Container, Cloud und Hosting-Panels](../../../en/us/help/os-support.md#containers-cloud-and-hosting-panels) |
| „Welche Datenbank sollen wir nehmen?“ | SQLite 3 ist der Standard; PostgreSQL ist der empfohlene Server | [Datenbankunterstützung](../../../en/us/help/database-support.md) |
| „Wie werden Sicherheitskorrekturen ausgeliefert?“ | Auf `main`, veröffentlicht mit dem nächsten Release 6.0.x | [Sicherheitskorrekturen](../../../en/us/help/who-is-the-exponential-foundation.md#security-fixes) |
| „Dürfen wir Cron als root laufen lassen?“ | Die Anleitung zur Härtung behandelt Cron und Kommandozeilenskripte als root | [Sicherheitshärtung](../../../en/us/help/security-hardening.md#cron-and-command-line-scripts-as-root) |
| „Wie aktualisieren wir sicher?“ | Zuerst sichern, nie einen Installer über eine bestehende Website laufen lassen; Dateien je Datenbank aktualisieren | [Upgrade](../../../en/us/install/11-upgrading.md) |
| „Kann eine WAF davor sitzen?“ | request-shield von CJW Network ist als Begleitprojekt dokumentiert | [Ein Schutzschild davor](../../../en/us/help/security-hardening.md#a-shield-in-front-request-shield) |

## Sagen Sie nicht

- „Es ist sicher.“ Sagen Sie, wie der Prozess ist und wie man ihn prüft ([Gesprächspunkte: Sicherheit](gespraechspunkte-sicherheit.md)).
- „Es läuft auf jedem Hosting.“ Nennen Sie die Stufen und die Grenzen von Shared Hosting.
- Eine Zahl zu Reaktionszeit oder Verfügbarkeit: Es ist keine dokumentiert ([Aussagen, die Sie nicht machen dürfen](aussagen-die-sie-nicht-machen-duerfen.md)).

## Verwandte Seiten

- [Gesprächspunkte: Hosting, Datenbanken und Freiheit](gespraechspunkte-hosting-datenbanken-freiheit.md)
- [Gesprächspunkte: Sicherheit](gespraechspunkte-sicherheit.md)
- [Für Sicherheit eintreten](fuer-sicherheit-eintreten.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
