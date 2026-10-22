---
title: Die Datenbank überwachen
audience: dba
category: database-administration
order: 42
summary: Was Sie an der Datenbank einer Exponential-Website überwachen und wo Sie es ablesen - die Karte Datenbank unter Setup > Systeminformationen, exp:system:info für Skripte und Alarme, die Logdateien und die wichtigen Muster, die Warteschlangentabellen, Gesundheitsprüfungen je Engine (SQLite-PRAGMAs, der Gesundheits-Cronjob von Oracle) und die eigene Überwachung der Engine.
keywords: Überwachung, Monitoring, Gesundheitsprüfung, Systeminformationen, exp:system:info, --json, --sizes, Exit-Code, error.log, warning.log, exp:logs, database is busy, Database implementation not supported, MONGO TODO, could not set the connection charset, ezoraclehealth, quick_check, Verfügbarkeit, Alarmierung, monitoring, health check, System information, exit code, uptime, alerting
type: help
---
# Die Datenbank überwachen

Diese Seite richtet sich an Datenbankadministratoren, die Überwachung und Alarmierung für die Datenbank einer
Exponential-Website einrichten. Sie beantwortet: was die Anwendung selbst über ihre Datenbank meldet, wie Sie es aus einem
Skript lesen, welche Logzeilen einen Alarm verdienen und welche Prüfungen zur Engine gehören. Die Sicht der Anwendung und die
Sicht des Servers ergänzen sich: Die erste sagt Ihnen, ob die Website die Datenbank nutzen kann, die zweite, wie es dem Server
geht.

## Inhalt

1. [Setup > Systeminformationen](#setup--systeminformationen)
2. [Aus einem Skript: exp:system:info](#aus-einem-skript-expsysteminfo)
3. [Logdateien und Muster](#logdateien-und-muster)
4. [Warteschlangen](#warteschlangen)
5. [Je Engine](#je-engine)
6. [Checkliste](#checkliste)

## Setup > Systeminformationen

Die Seite braucht die Richtlinie `setup/system_info` und liest nur ([Systeminformationen](../../anleitungen/systeminformationen.md)):

- **Karte Datenbank**: Engine und Serverversion, Treiberklasse, die Datei (SQLite) oder Server und Datenbankname, Zeichensatz,
  Zahl der Tabellen, Größe (SQLite immer; MySQL und PostgreSQL mit **Größen messen**).
- Teil **Datenbankverbindung**: Typ, Server, Socket, Datenbankname, Zeichensatz, Lesereplikat.
- Feld **Datenbankabfragen**: Modus des SQL-Abfrage-Cache, Trefferquote und der Schalter des SQL-Profilers.
- **Zustandsprüfungen** zuerst, Fehler und Warnungen vor Hinweisen; eine Warnung, wenn der Zeichensatz der Verbindung nicht
  UTF-8 ist.
- **Bericht herunterladen** (Text oder JSON) für eine Supportanfrage; Passwörter und der Datenbankbenutzer stehen nie darin.

Öffnen Sie sie über jeden Server, der die Website ausliefert (Apache mit PHP-FPM, der Port von Velocity): Jeder hat sein eigenes
PHP und kann anders berichten.

## Aus einem Skript: exp:system:info

```bash
./console exp:system:info --allow-root-user            # the text report
./console exp:system:info --json --allow-root-user     # JSON for your monitoring system
./console exp:system:info --sizes --allow-root-user    # with directory and database sizes
```

Es berichtet über das PHP der Kommandozeile und **endet mit Exit-Code 1, wenn eine Prüfung scheitert**, sodass es einen Alarm
speisen oder einen Deploy absichern kann. `exp:expfetch` (die Systemkarte) zeigt Version, Engine und Datenbank auf einem
Bildschirm mit Hinweisen.

## Logdateien und Muster

Die Logs liegen in `var/log/` (`error.log`; `warning.log` nur mit `[DebugSettings] AlwaysLog[]=warning`). `exp:logs` listet,
verfolgt und durchsucht sie ([Log-Betrachter und Rotation](../hosting/betrieb/log-viewer-und-log-rotation.md)).

| Muster | Bedeutung | Alarm |
|---|---|---|
| `Database implementation not supported` | falsch geschriebenes `DatabaseImplementation`; jede Seite scheitert | sofort |
| `eZDBNoConnectionException`, "database cannot be reached" | falscher Host, falsche Zugangsdaten oder der Server ist ausgefallen | sofort |
| `database is busy: the transaction could not start` | Timeout der SQLite-Schreibwarteschlange ([Schreibwarteschlange](sqlite-schreibwarteschlange-und-sperren.md)) | bei Wiederholung |
| `attempt to write a readonly database` | Eigentümer der SQLite-Dateien ([Rechte](sqlite-dateien-eigentuemer-und-rechte.md)) | sofort |
| `could not set the connection charset` | der Verbindungszeichensatz von MySQL ist zurückgefallen | bei der Einrichtung |
| `MONGO TODO` | SQL, das der MongoDB-Adapter nicht übersetzen konnte | täglich prüfen |
| `ORA-` | ein Oracle-Fehler | nach Nummer |

```bash
grep -c "database is busy" var/log/error.log
grep -rn "MONGO TODO" var/log/ | tail
```

## Warteschlangen

| Prüfung | Gesund |
|---|---|
| `SELECT action, COUNT(*), MIN(created) FROM ezpending_actions GROUP BY action;` | kleine Zahlen, jüngstes `MIN(created)` |
| abgelaufene Zeilen in `ezsession` (Datenbank-Sessions) | wenige |
| **Setup > Cronjobs**, jüngste Läufe | keine Probleme, letzter Lauf kürzlich |

Siehe [Aufräumen: Cronjobs und Warteschlangentabellen](aufraeumen-cronjobs-und-warteschlangen.md).

## Je Engine

| Engine | Aus Exponential | Aus der Engine |
|---|---|---|
| SQLite | `PRAGMA quick_check`, `PRAGMA journal_mode` (`wal`), Größe von `-wal`, Speicherplatz, Eigentümer der Dateien ([SQLite-Wartung](sqlite-wartung.md#integritätsprüfungen)) | |
| PostgreSQL | Systeminformationen | Verbindungen, Sperren, Replikationsverzögerung, Autovacuum (Views `pg_stat_*`) |
| MySQL, MariaDB | Systeminformationen; verwendetes Replikat | Verbindungen, InnoDB-Status, Verzögerung der Replikate |
| MongoDB | Zahl der `MONGO TODO` | Serverstatus, Zustand des Replica Sets |
| Oracle | `ext:ezoracle:health`, der stündliche Cronjob `ezoraclehealth`, der bei Warnungen an `HealthMailReceivers[]` mailt ([Wartungsbefehle und Cronjobs für Oracle](oracle-wartungsbefehle.md)) | AWR, OEM |

Überwachen Sie auch die Sicherungen selbst: ihr Alter, ihre Größe und den letzten Wiederherstellungstest
([Sicherungen je Engine](sicherungen-je-engine.md)).

## Checkliste

- [ ] `exp:system:info --json` wird abgefragt; Exit-Code 1 löst einen Alarm aus.
- [ ] Alarme für die oben als sofort markierten Logmuster.
- [ ] Warteschlangentabellen werden täglich geprüft.
- [ ] Überwachung auf Seiten der Engine für Verbindungen, Sperren, Replikation und Festplatte.
- [ ] Alter der Sicherungen überwacht.

## Verwandte Seiten

- [Überwachung im Überblick](../hosting/betrieb/ueberblick-ueber-die-ueberwachung.md), [Gesundheitsprüfungen und Verfügbarkeit](../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md)
- [Datenbankfehler beheben](datenbankfehler-beheben.md)
- [Systeminformationen](../../anleitungen/systeminformationen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
