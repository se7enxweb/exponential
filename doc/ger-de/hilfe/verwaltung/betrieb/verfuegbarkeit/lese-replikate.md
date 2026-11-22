---
title: Lese-Replikate
audience: government, hosting, admin
category: public-sector
order: 60
summary: Für Datenbankteams in öffentlichen Stellen, die Exponential unter PostgreSQL, MySQL, MariaDB oder Oracle betreiben. Wie Exponential Leseabfragen mit UseSlaveServer an Replikate sendet, die genauen Einstellungen, was eine Replikationsverzögerung für Redakteurinnen und Redakteure bedeutet und wann Replikate helfen und wann nicht.
keywords: Lese-Replikat, Replikat, Slave, UseSlaveServer, SlaveServerArray, SlaverServerUser, Replikationsverzögerung, MySQL, MariaDB, Skalierung der Lesezugriffe, Datenbanklast, read replica, replica, slave, replication lag, read scaling, database load
type: help
---
# Lese-Replikate

Diese Seite richtet sich an Datenbankadministratorinnen und -administratoren in öffentlichen Stellen, deren
Exponential-Website unter PostgreSQL, MySQL, MariaDB oder Oracle läuft und die Leselast vom Primärserver verlagern möchten. Sie
beantwortet, wie Exponential Replikate nutzt, welche Einstellungen dies einschalten, was eine Replikationsverzögerung
für Redaktion und Besucher bedeutet und wann sich Replikate lohnen. Die Quelle ist
[9.3 MySQL und MariaDB, Replikate](../../../../installation/09-datenbanken.md#replikate).

## Inhalt

1. [Was Exponential mit Replikaten macht](#was-exponential-mit-replikaten-macht)
2. [Die Einstellungen](#die-einstellungen)
3. [Replikationsverzögerung](#replikationsverzögerung)
4. [Wann Replikate helfen](#wann-replikate-helfen)
5. [Checkliste](#checkliste)

## Was Exponential mit Replikaten macht

Mit `UseSlaveServer=enabled` sendet Exponential Leseabfragen an ein Replikat, das je Anfrage zufällig gewählt wird;
Schreibvorgänge gehen an `Server`. Seit 6.0.15 folgen PostgreSQL, MySQL, MariaDB und Oracle (ezoracle) einer Regel:
nur einfache SELECTs anonymer GET- und HEAD-Anfragen, die noch nichts geschrieben haben, und nur von einem Replikat,
das aktuell ist; angemeldete Redakteure, Formulare, Cronjobs und Skripte lesen vom Primärserver
([Lesereplikate](../../../../bc/6.0/postgresql-lesereplikat.md)). MongoDB nutzt über diese Einstellung keine
Replikate für Lesezugriffe, und SQLite hat keine.

Replikate sind eine Leistungsfunktion, keine Ausfallumschaltung: Fällt der Primärserver aus, scheitern
Schreibvorgänge. Zur Verfügbarkeit siehe [Hochverfügbarkeit der Datenbank](hochverfuegbarkeit-der-datenbank.md).

## Die Einstellungen

In `settings/override/site.ini.append.php`, Block `[DatabaseSettings]`. Die Schlüssel für Benutzer, Passwort und
Datenbank werden tatsächlich `Slaver...` geschrieben:

```ini
[DatabaseSettings]
UseSlaveServer=enabled
SlaveServerArray[]
SlaveServerArray[]=db-replica-1.example.gov
SlaveServerPort[]
SlaveServerPort[]=3306
SlaverServerUser[]
SlaverServerUser[]=<read user>
SlaverServerPassword[]
SlaverServerPassword[]=<password>
SlaverServerDatabase[]
SlaverServerDatabase[]=<database>
```

Der Hostname und die Platzhalter sind Beispiele. Die ausgelieferte `settings/site.ini` führt diese Schlüssel
auskommentiert auf, mit `UseSlaveServer=disabled`. Leeren Sie nach der Änderung den INI-Cache und lassen Sie die
Engine ihn lesen:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
./console exp:velocity deploy --allow-root-user
```

Geben Sie dem Replikat-Benutzer nur Leserechte; er muss nie schreiben. Halten Sie das Passwort aus der
Versionsverwaltung heraus ([Konfiguration unter Versionsverwaltung](../hosting/konfiguration-unter-versionskontrolle.md)).

## Replikationsverzögerung

Vor 6.0.15 galt: „Eine Replikationsverzögerung bedeutet, dass eine Seite Inhalte zeigen kann, die einige Sekunden
alt sind“ ([9.3](../../../../installation/09-datenbanken.md#replikate)). Seit 6.0.15 wird ein Replikat, das
zurückliegt, nicht gelesen: MySQL und MariaDB lesen es erst, wenn es angewendet hat, was der Primärserver beim Start
der Anfrage festgeschrieben hatte (GTID), und höchstens `SlaveServerMaxLag` Sekunden zurückliegt; Oracle prüft den
Anwendungsrückstand des Standby; PostgreSQL liest nur einen Standby, der streamt, und schließt das Fenster mit
synchronem Anwenden. Redakteure lesen immer vom Primärserver und sehen ihre Arbeit sofort. Überwachen Sie die
Verzögerung trotzdem: Ein Replikat, das ständig zurückliegt, wird einfach nie gelesen, und der Primärserver trägt
die ganze Last.

## Wann Replikate helfen

| Situation | Helfen Replikate? |
|---|---|
| Viele anonyme Besucher | meist nicht viel: Der Antwort-Cache von Velocity und der HTTP-Cache beantworten sie ohne die Datenbank ([Velocity-Antwort-Cache und HTTP-Cache](velocity-antwortcache-und-http-cache.md)) |
| Nicht gecachte Seiten und Suchen anonymer Besucher | ja, wenn die Datenbank der Engpass ist |
| Viele angemeldete Benutzer | nein: seit 6.0.15 lesen angemeldete Benutzer immer vom Primärserver |
| Der Primärserver ist wegen Schreibvorgängen langsam | nein; Replikate übernehmen nur Lesezugriffe |
| Sie brauchen eine Bereitschaftsdatenbank für die Kontinuität | nicht für sich allein; siehe [Hochverfügbarkeit der Datenbank](hochverfuegbarkeit-der-datenbank.md) |

Messen Sie, bevor Sie ein Replikat hinzufügen: `exp:benchmark` und die Ausgabe langsamer Abfragen
(`[DatabaseSettings] SlowQueriesOutput` mit `SQLOutput=enabled`) zeigen, wohin die Zeit geht
([Kapazitätsplanung](kapazitaetsplanung.md)).

## Checkliste

- [ ] Die Website läuft unter PostgreSQL, MySQL, MariaDB oder Oracle (ezoracle).
- [ ] MySQL/MariaDB: Die Anmeldung für das Replikat hat `REPLICATION CLIENT` (MySQL) oder `REPLICA MONITOR`
      (MariaDB) für die Statusprüfung; Oracle: `SELECT` auf `v_$database` und `v_$dataguard_stats`; PostgreSQL:
      `pg_read_all_stats`.
- [ ] Der Replikat-Benutzer hat nur Leserechte; sein Passwort steht nicht in der Versionsverwaltung.
- [ ] Die Einstellungen liegen in `settings/override`, der INI-Cache ist geleert und die Engine neu geladen.
- [ ] Die Replikationsverzögerung wird überwacht, und die Redaktion weiß von der kurzen Verzögerung.
- [ ] Eine Messung zeigt, dass die Datenbank der Engpass war.

## Verwandte Seiten

- [Verfügbarkeit](README.md): Übersicht dieses Ordners
- [MySQL für Hoster](../../../hosting/grundlagen/mysql-fuer-hoster.md), [MariaDB für Hoster](../../../hosting/grundlagen/mariadb-fuer-hoster.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
