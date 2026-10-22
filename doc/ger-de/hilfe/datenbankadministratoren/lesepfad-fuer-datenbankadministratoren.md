---
title: Ein Lesepfad für Datenbankadministratoren
audience: dba
category: database-administration
order: 49
summary: Die Reihenfolge, in der ein Datenbankadministrator, der neu bei Exponential ist, diese Seiten liest - eine erste Stunde, ein erster Tag, dann die Seiten für Ihre Engine und die Seiten für Betrieb, Upgrades und Störungen - mit der Zeit, die jeder Schritt braucht.
keywords: Lesepfad, Lernpfad, Einarbeitung, erster Tag, Reihenfolge, wo anfangen, DBA, Datenbankadministrator, Lehrplan, reading path, learning path, onboarding, first day, where to start, database administrator, curriculum
type: help
---
# Ein Lesepfad für Datenbankadministratoren

Diese Seite richtet sich an Datenbankadministratoren, die die Datenbank einer Exponential-Website übernehmen und sie in
einer sinnvollen Reihenfolge kennenlernen möchten. Folgen Sie den Schritten der Reihe nach; überspringen Sie die Engines,
die Sie nicht betreiben.

## Inhalt

1. [Die erste Stunde](#die-erste-stunde)
2. [Ihre Engine](#ihre-engine)
3. [Der erste Tag](#der-erste-tag)
4. [Vor dem ersten Upgrade](#vor-dem-ersten-upgrade)
5. [Vor der ersten Störung](#vor-der-ersten-störung)
6. [Wenn die Website wächst](#wenn-die-website-wächst)

## Die erste Stunde

1. [Datenbankadministratoren: hier beginnen](datenbankadministratoren-hier-beginnen.md) (5 Minuten).
2. [Die Datenbankschicht auf einer Seite](die-datenbankschicht-auf-einer-seite.md) (10 Minuten).
3. [Unterstützte Engines und Versionen](unterstuetzte-engines-und-versionen.md) (10 Minuten).
4. [Die Referenz zu DatabaseSettings](referenz-databasesettings.md) und
   [Datenbankeinstellungen lesen und ändern](datenbankeinstellungen-lesen-und-aendern.md) (15 Minuten).
5. Öffnen Sie **Setup > Systeminformationen** auf Ihrer Website und suchen Sie die Karte Datenbank
   ([Systeminformationen](../../anleitungen/systeminformationen.md)) (5 Minuten).
6. [Die Datenbank absichern](die-datenbank-absichern.md) (15 Minuten).

## Ihre Engine

| Engine | Lesen |
|---|---|
| SQLite 3 | [SQLite 3 für Datenbankadministratoren](sqlite-fuer-datenbankadministratoren.md), [Schreibwarteschlange](sqlite-schreibwarteschlange-und-sperren.md), [Wartung](sqlite-wartung.md), [Rechte](sqlite-dateien-eigentuemer-und-rechte.md) |
| PostgreSQL | [PostgreSQL einrichten](postgresql-einrichten.md), [PostgreSQL betreiben](postgresql-betrieb.md) |
| MongoDB | [MongoDB einrichten](mongodb-einrichten.md), [Grenzen und Betrieb](mongodb-grenzen-und-betrieb.md) |
| MariaDB, MySQL | [Einrichten](mysql-und-mariadb-einrichten.md), [utf8mb4 und InnoDB](mysql-und-mariadb-zeichensatz-und-innodb.md), [Lesereplikate](mysql-lesereplikate.md) |
| Oracle | [Oracle einrichten](oracle-einrichten.md), [Verbindungen und Tuning](oracle-verbindungen-und-tuning.md), [Wartungsbefehle](oracle-wartungsbefehle.md) |

Etwa eine Stunde je Engine.

## Der erste Tag

1. [Datenbankkonten und Rechte](datenbankkonten-und-rechte.md) und
   [Zeichensätze und Sortierfolgen](zeichensaetze-und-sortierfolgen.md).
2. [Sicherungen je Engine](sicherungen-je-engine.md) und [Eine Datenbank wiederherstellen](eine-datenbank-wiederherstellen.md); testen
   Sie danach eine Wiederherstellung.
3. [Aufräumen: Cronjobs und Warteschlangentabellen](aufraeumen-cronjobs-und-warteschlangen.md) und
   [Sessions in der Datenbank](sessions-in-der-datenbank.md).
4. [Die Datenbank überwachen](die-datenbank-ueberwachen.md); richten Sie die Alarme ein.
5. [Die Tabellen, die ein Datenbankadministrator kennen sollte](tabellen-die-sie-kennen-sollten.md).

## Vor dem ersten Upgrade

1. [Upgrades und die SQL-Update-Dateien](upgrades-und-sql-update-dateien.md).
2. [Das Datenbank-Update auf 6.0.15 anwenden](das-datenbank-update-6-0-15-anwenden.md).
3. [Das Schema nach einem Upgrade prüfen](das-schema-nach-einem-upgrade-pruefen.md).
4. [Das .dba-Schemaformat](das-dba-schemaformat.md) und [Schemawerkzeuge](schemawerkzeuge-dump-insert-diff.md).
5. Die Anleitung für Administratoren [Aktualisieren](../../anleitungen/aktualisieren.md), um zu wissen, was der Rest des Teams tut.

## Vor der ersten Störung

1. [Datenbankfehler beheben](datenbankfehler-beheben.md).
2. [Fehlercodes und Meldungen der Datenbank](fehlercodes-und-meldungen.md).
3. [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md).
4. [Checklisten für Datenbankadministratoren](checklisten-fuer-datenbankadministratoren.md): Drucken Sie die Liste zur Wiederherstellung aus.

## Wenn die Website wächst

1. [Leistung und Skalierung](leistung-und-skalierung.md) und [Langsame Abfragen finden](langsame-abfragen-finden.md).
2. [Der SQL-Abfrage-Cache](sql-abfrage-cache.md) und [Die Tabellen des eingebauten Suchindex](tabellen-des-suchindex.md).
3. [Eine Engine wählen](eine-engine-waehlen.md) und [Eine Website auf eine andere Engine umziehen](zwischen-engines-wechseln.md).
4. [Clustering: DFS und seine Metadatendatenbank](clustering-dfs-und-die-datenbank.md).
5. [SQL von Erweiterungen für jede Engine prüfen](portables-sql-fuer-erweiterungen.md).

Halten Sie [Häufige Fragen zur Datenbank](haeufige-fragen-zur-datenbank.md) und das [Glossar](glossar-fuer-datenbankadministratoren.md) griffbereit.

## Verwandte Seiten

- [Datenbankadministratoren: hier beginnen](datenbankadministratoren-hier-beginnen.md)
- [Hosting-Unternehmen: hier beginnen](../hosting/hosting-anbieter-hier-beginnen.md)
- [Datenbankunterstützung](../datenbankunterstuetzung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
