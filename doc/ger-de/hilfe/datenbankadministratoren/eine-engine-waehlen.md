---
title: Eine Engine wählen
audience: dba
category: database-administration
order: 4
summary: Eine Entscheidungshilfe für Datenbankadministratoren - welche der sechs unterstützten Datenbanken zu einer bestimmten Exponential-Website passt, von einer Maschine mit SQLite bis zu mehreren Webservern auf PostgreSQL, MariaDB oder MySQL, und wann MongoDB oder Oracle sinnvoll sind, mit den zu stellenden Fragen und den Kosten jeder Wahl.
keywords: Datenbank wählen, welche Datenbank, SQLite oder PostgreSQL, MySQL oder MariaDB, MongoDB, Oracle, Entscheidung, mehrere Webserver, Replikation, redaktionelle Last, Schreibzugriffe, Nebenläufigkeit, Vergleich, choose database, which database, decision, several web servers, replication, concurrency, comparison
type: help
---
# Eine Engine wählen

Diese Seite richtet sich an Datenbankadministratoren, die die Datenbank einer neuen Exponential-Website auswählen oder
freigeben müssen. Sie beantwortet: welche Fragen die Wahl entscheiden, was jede Engine im Betrieb kostet und wo jede nicht
mehr passt. Sie fasst [9.7 Vergleich](../../installation/09-datenbanken.md#97-vergleich) und die Abschnitte zu Grenzen in
[Datenbankunterstützung](../datenbankunterstuetzung.md) zusammen.

## Inhalt

1. [Fünf Fragen](#fünf-fragen)
2. [Der Vergleich](#der-vergleich)
3. [Typische Antworten](#typische-antworten)
4. [Wann SQLite nicht mehr passt](#wann-sqlite-nicht-mehr-passt)
5. [Bevor Sie MongoDB oder Oracle wählen](#bevor-sie-mongodb-oder-oracle-wählen)
6. [Später umentscheiden](#später-umentscheiden)

## Fünf Fragen

1. **Wie viele Maschinen schreiben in die Datenbank?** Eine Maschine (mit beliebig vielen PHP-Prozessen) kann SQLite
   verwenden. Mehrere Webserver brauchen einen Datenbankserver: PostgreSQL, MariaDB, MySQL, MongoDB oder Oracle.
2. **Wie viel wird gleichzeitig geschrieben?** SQLite reiht Schreiber nacheinander ein
   ([die Schreibwarteschlange](sqlite-schreibwarteschlange-und-sperren.md)); die Server-Engines sperren Zeilen.
3. **Was betreibt und sichert Ihre Organisation bereits?** Eine Engine, die Ihr Team bereits überwacht, patcht und
   wiederherstellt, ist mehr wert als ein kleiner technischer Vorteil.
4. **Brauchen Sie Replikation, Wiederherstellung zu einem Zeitpunkt oder Datenbankbenutzer und Rechte?** SQLite hat
   nichts davon selbst.
5. **Welche Erweiterungen wird die Website verwenden?** Erweiterungen mit eigenem SQL wurden meist für MySQL geschrieben;
   sie können auf PostgreSQL oder Oracle Arbeit brauchen und auf MongoDB einen eigenen Weg
   ([SQL von Erweiterungen für jede Engine prüfen](portables-sql-fuer-erweiterungen.md)).

## Der Vergleich

| | SQLite 3 | MySQL / MariaDB | PostgreSQL | MongoDB | Oracle |
|---|---|---|---|---|---|
| Zu betreibender Server | keiner | ja | ja | ja | ja |
| Gleichzeitige Schreiber | einer nach dem anderen, in der Warteschlange | Zeilensperren (InnoDB) | Zeilensperren (MVCC) | auf Dokumentebene | Zeilensperren |
| Mehrere Anwendungsserver | nein (ein Host) | ja | ja | ja | ja |
| Lesereplikate | nein | eingebaut (`UseSlaveServer`) | extern | Replica Sets | extern (Data Guard) |
| SQL-Abfrage-Cache | ja | ja | ja | nein | ja |
| Benutzer und Rechte | Dateisystem | ja | ja | ja | ja |
| Bekannte Lücken | ein Host; Schreibzugriffe in der Warteschlange | 4-Byte-Zeichen über die Verbindung `utf8` | | Grenzen des Adapters | `''` ist NULL; Groß-/Kleinschreibung zählt |

Quelle: [9.7 Vergleich](../../installation/09-datenbanken.md#97-vergleich).

## Typische Antworten

| Situation | Übliche Wahl | Warum |
|---|---|---|
| Eine Website auf einem Server, ein Redaktionsteam, beliebig viele Leser | SQLite 3 | Kein Server zu betreiben; WAL lässt Leser während Schreibzugriffen weiterarbeiten; die meisten Seitenaufrufe beantworten Caches |
| Eine Website auf mehreren Webservern, oder viele Redakteure, die den ganzen Tag große Änderungen veröffentlichen | PostgreSQL | Der empfohlene Server des Projekts: striktes SQL, Sperren auf Zeilenebene, eigene Replikation und Wiederherstellung zu einem Zeitpunkt, in der CI getestet |
| Eine Organisation, die auf MySQL oder MariaDB standardisiert ist, oder viele reine MySQL-Erweiterungen | MariaDB oder MySQL | Die Engine, für die die meisten Erweiterungen geschrieben wurden; eingebaute Lesereplikate |
| Eine Organisation, die auf MongoDB standardisiert ist | MongoDB | Über den Adapter unterstützt; lesen Sie zuerst seine Grenzen |
| Eine Organisation, die auf Oracle standardisiert ist | Oracle 19c oder neuer mit `ezoracle` | Verbindungspooling (DRCP), Failover und Tracing für ein Oracle-Haus |
| Eine öffentliche Demo, die alle paar Stunden neu gebaut wird | SQLite 3 | Zwischen den Builds ist nichts zu löschen ([9.10](../../installation/09-datenbanken.md#910-microsoft-sql-server)) |

## Wann SQLite nicht mehr passt

Aus [Grenzen, und wann Sie eine andere Engine wählen](../../installation/09-datenbanken.md#grenzen-und-wann-eine-andere-engine-die-bessere-wahl-ist):

- **Mehr als eine Maschine** muss in dieselbe Datenbank schreiben. WAL braucht gemeinsamen Speicher auf einem Host; ein
  Netzwerkdateisystem ist kein Ersatz.
- **Viele Redakteure veröffentlichen gleichzeitig große Änderungen**, den ganzen Tag, sodass die Warteschlange regelmäßig
  länger ist als `SQLiteTransactionWait`.
- Sie brauchen **Replikation**, einen Hot Standby oder eine Wiederherstellung zu einem Zeitpunkt aus der Datenbank selbst.
- Sie brauchen **Datenbankbenutzer und Rechte**.

Die Größe der Datenbank ist selten der Grund. SQLite bewältigt Datenbanken, die weit größer sind als die Inhalte einer
Exponential-Website.

## Bevor Sie MongoDB oder Oracle wählen

- **MongoDB**: Der Adapter führt nie echtes SQL aus; handgeschriebenes SQL, das er nicht übersetzen kann, liefert ein leeres
  Ergebnis und protokolliert `MONGO TODO`. Die tiefe Neuerzeugung von URL-Aliasen ist unvollständig, `eztags` ist nicht
  verwendbar, der Shop-Checkout und `ezflow` sind nicht durchgängig getestet, es gibt keine SQL-Update-Dateien und der
  SQL-Abfrage-Cache gilt nicht ([MongoDB: Grenzen und Betrieb](mongodb-grenzen-und-betrieb.md)).
- **Oracle**: braucht Exponential 6.0.15 oder neuer, Oracle 19c oder neuer in `AL32UTF8`, `oci8` gegen einen Oracle-Client
  gebaut und Aufmerksamkeit für `''` als NULL und Vergleiche mit Beachtung der Groß-/Kleinschreibung. Oracle ist nicht in
  der CI des Projekts ([Oracle mit ezoracle einrichten](oracle-einrichten.md)).

## Später umentscheiden

Eine Website kann über das Format `.dba` zwischen den SQL-Engines umziehen, mit den Übertragungsbefehlen der Erweiterung
nach Oracle und mit den Skripten in `bin/mongodb/` nach MongoDB. Planen Sie ein Wartungsfenster ein
([Eine Website auf eine andere Engine umziehen](zwischen-engines-wechseln.md)).

## Verwandte Seiten

- [Unterstützte Engines und Versionen](unterstuetzte-engines-und-versionen.md)
- [Leistung und Skalierung](leistung-und-skalierung.md)
- [Datenbanken in der offiziellen Reihenfolge](../hosting/grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md)
- [Datenbankunterstützung](../datenbankunterstuetzung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
