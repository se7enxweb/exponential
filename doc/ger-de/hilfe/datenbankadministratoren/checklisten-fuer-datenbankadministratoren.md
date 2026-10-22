---
title: Checklisten für Datenbankadministratoren
audience: dba
category: database-administration
order: 47
summary: Druckbare Checklisten für die Datenbank einer Exponential-Website - vor einer Installation, Go-live, täglicher, wöchentlicher und monatlicher Betrieb, vor und nach einem Upgrade, eine Wiederherstellung, ein Engine-Wechsel und eine Sicherheitsprüfung - jeder Punkt verlinkt auf die Seite, die ihn erklärt.
keywords: Checkliste, Checklisten, Go-live, Installation, täglich, wöchentlich, monatlich, Upgrade, Wiederherstellung, Migration, Sicherheitsprüfung, Betrieb, DBA, checklist, install, daily, weekly, monthly, restore, migration, security review, operations
type: help
---
# Checklisten für Datenbankadministratoren

Diese Seite richtet sich an Datenbankadministratoren, die die Schritte jeder Routine auf einer Seite haben möchten. Kopieren Sie
eine Liste in Ihr Ticket oder Ihre Betriebsanleitung; jeder Punkt verlinkt auf die Seite, die ihn erklärt.

## Inhalt

1. [Vor einer Installation](#vor-einer-installation)
2. [Go-live](#go-live)
3. [Täglich](#täglich)
4. [Wöchentlich](#wöchentlich)
5. [Monatlich und vierteljährlich](#monatlich-und-vierteljährlich)
6. [Vor und nach einem Upgrade](#vor-und-nach-einem-upgrade)
7. [Wiederherstellung](#wiederherstellung)
8. [Engine-Wechsel](#engine-wechsel)
9. [Sicherheitsprüfung](#sicherheitsprüfung)

## Vor einer Installation

- [ ] Engine gewählt ([Eine Engine wählen](eine-engine-waehlen.md)); Version auf oder über dem praktischen Minimum ([Versionen](unterstuetzte-engines-und-versionen.md)).
- [ ] PHP-Erweiterung im PHP des Webservers, im PHP der Kommandozeile und im PHP von Velocity geladen ([PHP-Erweiterungen](php-erweiterungen-je-engine.md)).
- [ ] Datenbank in UTF-8 angelegt ([Zeichensätze](zeichensaetze-und-sortierfolgen.md)).
- [ ] Konto mit den minimalen Rechten angelegt, die funktionieren ([Konten](datenbankkonten-und-rechte.md)).
- [ ] PostgreSQL: `pgcrypto` verfügbar. MongoDB: Bibliothek installiert. Oracle: `oci8` und der Client gefunden.
- [ ] Netzwerkzugang nur von den Webservern ([Absichern](die-datenbank-absichern.md#netzwerkzugang)).

## Go-live

- [ ] **Setup > Systeminformationen** zeigt die erwartete Engine, Version und UTF-8.
- [ ] Kein `root` oder Superuser in `settings/override/site.ini.append.php`; die Datei hat Modus 640 und ist nicht in der Versionsverwaltung.
- [ ] SQLite: Eigentümer eingerichtet, die Datei nicht herunterladbar ([SQLite-Rechte](sqlite-dateien-eigentuemer-und-rechte.md)).
- [ ] MongoDB: `create_indexes.js` ausgeführt ([MongoDB einrichten](mongodb-einrichten.md)).
- [ ] Sicherungen eingeplant und eine Wiederherstellung getestet ([Sicherungen](sicherungen-je-engine.md), [Wiederherstellen](eine-datenbank-wiederherstellen.md)).
- [ ] Cronjobs eingeplant; `session_gc.php` bei Datenbank-Sessions hinzugefügt ([Aufräumen](aufraeumen-cronjobs-und-warteschlangen.md)).
- [ ] Überwachung und Alarme eingerichtet ([Überwachung](die-datenbank-ueberwachen.md)).
- [ ] Debug-Ausgabe für die Öffentlichkeit aus.

## Täglich

- [ ] Sicherung gelaufen; ihre Größe ist plausibel; die SQLite-Sicherung besteht `integrity_check`.
- [ ] `var/log/error.log` hat keine neuen Datenbankfehler.
- [ ] `ezpending_actions` wächst nicht.
- [ ] MongoDB: kein neues `MONGO TODO`.

## Wöchentlich

- [ ] SQLite: `PRAGMA optimize`, `quick_check`, Größe von `-wal` ([SQLite-Wartung](sqlite-wartung.md)).
- [ ] Statistiken der Engine aktuell (Autovacuum bei PostgreSQL, `gather-stats` bei Oracle).
- [ ] Speicherplatz für Datenbank, Logs und Sicherungen.
- [ ] **Setup > Cronjobs** zeigt Läufe ohne Probleme.

## Monatlich und vierteljährlich

- [ ] Tabellengrößen geprüft ([Tabellen](tabellen-die-sie-kennen-sollten.md#welche-tabellen-wachsen)); alte Versionen beschnitten.
- [ ] Engine und PHP-Erweiterung noch in der Sicherheitsunterstützung des Herstellers.
- [ ] Wiederherstellung auf einer anderen Maschine getestet (vierteljährlich).
- [ ] Konten und Netzwerkregeln geprüft.

## Vor und nach einem Upgrade

- [ ] Versionszeile gelesen ([Upgrades](upgrades-und-sql-update-dateien.md#auf-welcher-version-steht-die-datenbank)).
- [ ] Sicherung erstellt; Wartungsmodus an.
- [ ] Update-Dateien Ihrer Engine und Ihres Weges der Reihe nach angewendet, jede einmal; kein `6.12/`, `7.2/`, `7.3/`, `unstable/`.
- [ ] Cluster-Update-Dateien, wo der Cluster verwendet wird.
- [ ] 6.0.15: `createaudittables.php`, `movetrashrecords.php` ([Update auf 6.0.15](das-datenbank-update-6-0-15-anwenden.md)).
- [ ] `exp:checkdbfiles` gibt nichts aus; die Upgrade-Prüfung listet keine fehlende Tabelle ([Das Schema prüfen](das-schema-nach-einem-upgrade-pruefen.md)).
- [ ] Caches nach dem Neuladen von PHP-FPM oder dem Neustart von Velocity geleert; Testveröffentlichung erledigt; Wartungsmodus aus.

## Wiederherstellung

- [ ] Wartungsmodus an; Velocity angehalten.
- [ ] Datenbank und Dateien vom selben Zeitpunkt wiederhergestellt.
- [ ] Alle Caches geleert, Abfrage-Cache und Antwort-Cache von Velocity eingeschlossen.
- [ ] Sequenzen, Suchindex, MongoDB-Indizes geprüft.
- [ ] Versionszeile, Zeilenzahlen, Anmeldung und Testveröffentlichung geprüft; Wartungsmodus aus.

## Engine-Wechsel

- [ ] Probe auf einer Kopie; Fenster vereinbart ([Engine wechseln](zwischen-engines-wechseln.md)).
- [ ] Erweiterungen für die Ziel-Engine geprüft ([SQL von Erweiterungen](portables-sql-fuer-erweiterungen.md)).
- [ ] Dump und Insert abgeschlossen; Einstellungen umgestellt; Caches geleert; Worker neu gestartet.
- [ ] Zeilenzahlen stimmen überein; Konsistenzprüfung sauber; Suchindex neu aufgebaut.
- [ ] Sicherungen und Überwachung auf die neue Engine umgestellt.

## Sicherheitsprüfung

- [ ] Ein Konto je Website, keine administrativen Konten ([Konten](datenbankkonten-und-rechte.md)).
- [ ] Zugangsdaten nur in `settings/override/` (und den Cluster-Dateien); nicht in Historie, Crontabs oder Skripten.
- [ ] Datenbankport nur von den Webservern erreichbar; TLS, wo er Maschinengrenzen überschreitet.
- [ ] Sicherungen verschlüsselt und zugriffsgeschützt.
- [ ] Debug-Ausgabe auf die Adressen von Administratoren beschränkt.

## Verwandte Seiten

- [Datenbankadministratoren: hier beginnen](datenbankadministratoren-hier-beginnen.md)
- [Monatlicher Betrieb](../hosting/checklisten/checkliste-fuer-den-monatlichen-betrieb.md), [Sicherheitsprüfung](../hosting/checklisten/checkliste-fuer-die-sicherheitspruefung.md) (für Hosting-Unternehmen)
- [Häufige Fragen zur Datenbank](haeufige-fragen-zur-datenbank.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
