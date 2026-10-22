---
title: Lesereplikate für MySQL und MariaDB
audience: dba
category: database-administration
order: 16
summary: Wie Exponential Leseabfragen mit UseSlaveServer und den Schlüsseln SlaveServer und SlaverServer an Replikate von MySQL oder MariaDB sendet, wie ein Replikat gewählt wird, was Replikationsverzögerung für Redakteure und Besucher bedeutet und was die anderen Engines stattdessen bieten.
keywords: Replikat, Lesereplikat, Replikation, UseSlaveServer, SlaveServerArray, SlaveServerPort, SlaverServerUser, SlaverServerPassword, SlaverServerDatabase, Aufteilung von Lesezugriffen, Replikationsverzögerung, MySQL, MariaDB, Lesezugriffe skalieren, replica, read replica, replication, read splitting, replication lag, scaling reads
type: help
---
# Lesereplikate für MySQL und MariaDB

Diese Seite richtet sich an Datenbankadministratoren, die MySQL oder MariaDB mit Replikaten betreiben und möchten, dass
Exponential sie verwendet. Sie beantwortet: welche Einstellungen Lesezugriffe an ein Replikat senden, wie das Replikat
gewählt wird, was Verzögerung für die Website bedeutet und wie die anderen Engines im Vergleich abschneiden. MySQL und
MariaDB sind die einzigen Engines mit eingebauter Aufteilung von Lesezugriffen in Exponential. Quelle:
[Replikate](../../installation/09-datenbanken.md#replikate) und `settings/site.ini`.

## Inhalt

1. [Die Einstellungen](#die-einstellungen)
2. [Wie ein Replikat gewählt wird](#wie-ein-replikat-gewählt-wird)
3. [Replikationsverzögerung](#replikationsverzögerung)
4. [Konten auf den Replikaten](#konten-auf-den-replikaten)
5. [Brauchen Sie überhaupt Replikate?](#brauchen-sie-überhaupt-replikate)
6. [Andere Engines](#andere-engines)
7. [Checkliste](#checkliste)

## Die Einstellungen

Die Schlüssel stehen in `site.ini [DatabaseSettings]`; die mitgelieferte Datei hat sie auskommentiert:

```ini
# settings/override/site.ini.append.php
[DatabaseSettings]
UseSlaveServer=enabled
SlaveServerArray[]
SlaveServerArray[]=replica1.example.internal
SlaveServerArray[]=replica2.example.internal
SlaveServerPort[]
SlaveServerPort[]=3306
SlaveServerPort[]=3306
SlaverServerUser[]
SlaverServerUser[]=exponential_ro
SlaverServerUser[]=exponential_ro
SlaverServerPassword[]
SlaverServerPassword[]=<the password>
SlaverServerPassword[]=<the password>
SlaverServerDatabase[]
SlaverServerDatabase[]=exponential
SlaverServerDatabase[]=exponential
```

Die Schlüssel für Benutzer, Passwort und Datenbank werden wirklich `Slaver...` geschrieben; das ist der Name, den der Code
liest. Die Listen sind parallel: Der zweite Eintrag jeder Liste gehört zum zweiten Replikat. Die leere Zeile `Key[]` am
Anfang setzt die Liste zurück, sodass nur die Einträge dieser Datei zählen.

Leeren Sie nach einer Änderung den INI-Cache, laden Sie PHP-FPM neu und starten Sie Velocity neu
([Datenbankeinstellungen lesen und ändern](datenbankeinstellungen-lesen-und-aendern.md)).

## Wie ein Replikat gewählt wird

Mit `UseSlaveServer=enabled` wählt `eZDB::instance()` beim Aufbau der Verbindung ein Replikat zufällig (`mt_rand()` in
`lib/ezdb/classes/ezdb.php`): unter PHP-FPM je Anfrage. Lesezugriffe gehen an dieses Replikat; Schreibzugriffe gehen immer
an `Server`. **Setup > Systeminformationen**, Teil **Datenbankverbindung**, zeigt das verwendete Lesereplikat
([Systeminformationen](../../anleitungen/systeminformationen.md)).

## Replikationsverzögerung

Eine Seite kann einige Sekunden alte Inhalte zeigen, während ein Replikat aufholt. In der Praxis:

- Ein Redakteur, der veröffentlicht und die Seite sofort ansieht, sieht womöglich die vorherige Version, solange die
  Verzögerung anhält;
- die Caches (View-Cache, Antwort-Cache von Velocity) können diese ältere Darstellung dann bis zu ihrer nächsten
  Erneuerung behalten.

Halten Sie die Verzögerung niedrig und überwachen Sie sie auf den Replikaten mit Ihren üblichen Werkzeugen (zum Beispiel
`SHOW REPLICA STATUS` auf MySQL 8.0.22 und neuer, `SHOW SLAVE STATUS` auf älteren Releases und MariaDB). Exponential selbst
misst keine Verzögerung und nimmt ein verzögertes Replikat nicht aus der Rotation; das ist Aufgabe Ihrer
Replikationsüberwachung oder eines Proxys vor den Replikaten.

## Konten auf den Replikaten

Ein Replikat-Konto braucht Leserechte; der Kernel legt für einige Leseoperationen temporäre Tabellen an, geben Sie ihm also
auch `CREATE TEMPORARY TABLES`, oder testen Sie die Lesewege, auf die Sie sich verlassen (Suche, Listen), auf Staging mit
einem streng nur lesenden Konto. Bewahren Sie das Replikat-Passwort wie das Hauptpasswort in `settings/override/` auf.

## Brauchen Sie überhaupt Replikate?

Die meisten Seitenaufrufe erreichen die Datenbank nicht: Der Antwort-Cache von Velocity, der Cache der Inhaltsansichten und
der Template-Block-Cache antworten zuerst, und der SQL-Abfrage-Cache kann wiederholte `SELECT`s beantworten
([Leistung und Skalierung](leistung-und-skalierung.md)). Messen Sie zuerst mit `exp:benchmark` und dem SQL-Profiler
([Langsame Abfragen finden](langsame-abfragen-finden.md)). Replikate lohnen ihren Betrieb, wenn ungecachte Lesezugriffe
(angemeldete Redakteure, Suche, die ersten Aufrufe nach dem Leeren) die Primärdatenbank wirklich belasten.

## Andere Engines

| Engine | Skalierung von Lesezugriffen |
|---|---|
| PostgreSQL | Replikation ist Sache von PostgreSQL; Exponential hat dafür keine Aufteilung von Lesezugriffen. Ein Proxy vor PostgreSQL ist eine serverseitige Entscheidung. |
| MongoDB | Replica Sets sind Sache von MongoDB. |
| Oracle | Extern (Data Guard); `ezoracle.ini` bietet Failover über mehrere Listener. |
| SQLite | Keine Replikation; ein Host. |

Quelle: [9.7 Vergleich](../../installation/09-datenbanken.md#97-vergleich).

## Checkliste

- [ ] Die Listen der Hosts, Ports, Benutzer, Passwörter und Datenbanken sind gleich lang.
- [ ] Das Replikat-Konto kann jede Tabelle lesen und temporäre Tabellen anlegen.
- [ ] Die Verzögerung wird außerhalb von Exponential überwacht.
- [ ] Redakteure wissen, dass eine frische Veröffentlichung mit kurzer Verzögerung erscheinen kann.

## Verwandte Seiten

- [MySQL und MariaDB einrichten](mysql-und-mariadb-einrichten.md)
- [Mehrere Webknoten](../hosting/betrieb/mehrere-web-knoten.md)
- [Die Referenz zu DatabaseSettings](referenz-databasesettings.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
