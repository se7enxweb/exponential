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
| „Ein Befehl installiert eine vollständige Website auf SQLite, ohne Datenbankserver.“ | [Exponential mit einem Befehl installieren](../../funktionen/6.0/installation-mit-einem-befehl.md) |
| „Sechs Datenbanken: SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle und MySQL, in dieser offiziellen Reihenfolge.“ | [Datenbankunterstützung](../datenbankunterstuetzung.md#die-unterstützten-datenbanken-in-ihrer-reihenfolge) |
| „PHP 8.0 ist die älteste unterstützte Version, sodass das mitgelieferte PHP von Red Hat Enterprise Linux 9 es ausführt.“ | [PHP-Versionen](../wer-ist-die-exponential-foundation.md#php-versionen) |
| „Betriebssysteme in vier dokumentierten Stufen, von getestet bis theoretisch.“ | [Betriebssystemunterstützung](../betriebssystemunterstuetzung.md#die-vier-stufen) |
| „Velocity ist die empfohlene Engine; FrankenPHP ist die produktionsreife Alternative; Apache oder nginx mit PHP-FPM liefern es ebenfalls aus.“ | [Velocity](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md#wozu-sie-es-verwenden), [Webserver](../betriebssystemunterstuetzung.md#webserver) |
| „Eine Anleitung zur Härtung, Bedrohung für Bedrohung, mit einer Checkliste.“ | [Sicherheitshärtung](../sicherheitshaertung.md#checkliste) |
| „Eine neue Installation erzeugt ein zufälliges Administratorpasswort, wenn keines oder ein allgemein bekanntes angegeben wird.“ | [Das Standardpasswort des Administrators](../standard-administratorpasswort.md) |
| „Sondierungen und Angriffe werden in ein Sicherheitslog eingeordnet, mit maskierten Adressen.“ | [Request-Logs](../../funktionen/6.0/anfrageprotokolle.md) |
| „Ein manipulationssicheres Prüfprotokoll, standardmäßig eingeschaltet.“ | [Das Prüfprotokoll](../../funktionen/6.0/pruefprotokoll.md) |
| „Ein Log-Viewer in der Administration und Fehlerreferenzen, die man nachschlagen kann.“ | [Log-Viewer](../../funktionen/6.0/log-betrachter.md), [Einen Fehler über seine Referenz finden](../../anleitungen/fehler-anhand-der-referenz-finden.md) |
| „**Setup > Upgrade-Prüfung** vergleicht die Dateien mit dem Release-Manifest und die Datenbank mit dem Release-Schema.“ | [Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md) |
| „Große Löschungen und Verschiebungen laufen als Hintergrundjobs, sodass sie nicht in eine Zeitüberschreitung laufen.“ | [Inhaltsjobs](../../funktionen/6.0/inhaltsauftraege.md) |
| „Standard-Dumps pro Datenbank und dokumentierte Wege, eine Website zwischen Datenbanken umzuziehen.“ | [Backup und Wiederherstellung](../datenbankunterstuetzung.md#backup-und-wiederherstellung), [Eine Website umziehen](../datenbankunterstuetzung.md#eine-site-auf-eine-andere-datenbank-umziehen) |

## Befehle und Bildschirme zum Zeigen

```bash
./console exp:install --dry-run         # what an install would do, nothing written
./console exp:velocity status           # every engine, its state and URL
./console exp:audit status              # the audit trail on the command line
```

Bildschirme: **Setup > Systeminformationen** (die Datenbank, PHP und die Engine), **Setup > Logdateien**,
**Setup > Upgrade-Prüfung**, der Reiter **Audit**.

Prüfen Sie die Befehlsnamen auf der Installation, die Sie verwenden; `--help` bei jedem Befehl listet seine Optionen
([Velocity](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md#starten-prüfen-stoppen),
[Das Prüfprotokoll, Auf der Kommandozeile](../../funktionen/6.0/pruefprotokoll.md#von-der-kommandozeile)).

## Fragen, die Administratoren stellen

| Frage | Antwort | Quelle |
|---|---|---|
| „Läuft es auf Shared Hosting?“ | Apache oder LiteSpeed mit `.htaccess` können es ausliefern; ohne Shell-Zugang installiert es der Setup-Assistent. Velocity kann dort meist nicht laufen. | [Container, Cloud und Hosting-Panels](../betriebssystemunterstuetzung.md#container-cloud-und-hosting-panels) |
| „Welche Datenbank sollen wir nehmen?“ | SQLite 3 ist der Standard; PostgreSQL ist der empfohlene Server | [Datenbankunterstützung](../datenbankunterstuetzung.md) |
| „Wie werden Sicherheitskorrekturen ausgeliefert?“ | Auf `main`, veröffentlicht mit dem nächsten Release 6.0.x | [Sicherheitskorrekturen](../wer-ist-die-exponential-foundation.md#sicherheitskorrekturen) |
| „Dürfen wir Cron als root laufen lassen?“ | Die Anleitung zur Härtung behandelt Cron und Kommandozeilenskripte als root | [Sicherheitshärtung](../sicherheitshaertung.md#cron--und-kommandozeilenskripte-als-root) |
| „Wie aktualisieren wir sicher?“ | Zuerst sichern, nie einen Installer über eine bestehende Website laufen lassen; Dateien je Datenbank aktualisieren | [Upgrade](../../installation/11-aktualisieren.md) |
| „Kann eine WAF davor sitzen?“ | request-shield von CJW Network ist als Begleitprojekt dokumentiert | [Ein Schutzschild davor](../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield) |

## Sagen Sie nicht

- „Es ist sicher.“ Sagen Sie, wie der Prozess ist und wie man ihn prüft ([Gesprächspunkte: Sicherheit](gespraechspunkte-sicherheit.md)).
- „Es läuft auf jedem Hosting.“ Nennen Sie die Stufen und die Grenzen von Shared Hosting.
- Eine Zahl zu Reaktionszeit oder Verfügbarkeit: Es ist keine dokumentiert ([Aussagen, die Sie nicht machen dürfen](aussagen-die-sie-nicht-machen-duerfen.md)).

## Verwandte Seiten

- [Gesprächspunkte: Hosting, Datenbanken und Freiheit](gespraechspunkte-hosting-datenbanken-freiheit.md)
- [Gesprächspunkte: Sicherheit](gespraechspunkte-sicherheit.md)
- [Für Sicherheit eintreten](fuer-sicherheit-eintreten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
