---
title: Stand von Microsoft SQL Server
audience: hosting
category: hosting
order: 130
summary: Der Stand von Microsoft SQL Server für Exponential 6.0.x, so wie ein Hoster ihn nennen sollte - derzeit bekanntermaßen mit zusätzlicher Arbeit möglich, ohne Treiber, Schema-Handler oder Installer-Eintrag in 6.0.x; was es gibt, was fehlt und was Sie einem Kunden sagen, der fragt.
keywords: Microsoft SQL Server, SQL Server, MSSQL, sqlsrv, pdo_sqlsrv, Windows-Hosting, Datenbankunterstützung, Treiber, eZDBInterface, Schema-Handler, nicht unterstützt, mit zusätzlicher Arbeit möglich, Kundenfrage, Hosting-Angebot, Windows hosting, database support, driver, schema handler, not supported, possible with additional work, customer question, hosting offer
type: help
---
# Stand von Microsoft SQL Server

Diese Seite ist für Produktmanager, Vertriebs- und Support-Mitarbeitende bei einem Hosting-Anbieter, besonders bei einem
mit einem Angebot rund um Windows oder Microsoft, die gefragt werden, ob Exponential auf Microsoft SQL Server läuft. Sie
beantwortet: was die Aussage des Projekts ist, was es heute gibt, was fehlt und was ein Hoster ehrlich stattdessen
anbieten kann. Die Quelle ist [Datenbankunterstützung, Microsoft SQL Server](../../datenbankunterstuetzung.md#microsoft-sql-server-möglich-mit-zusätzlichem-aufwand).

## Inhalt

1. [Die Aussage](#die-aussage)
2. [Was es heute gibt](#was-es-heute-gibt)
3. [Was fehlt](#was-fehlt)
4. [Was Sie einem Kunden sagen](#was-sie-einem-kunden-sagen)
5. [Was ein Hoster stattdessen anbieten kann](#was-ein-hoster-stattdessen-anbieten-kann)
6. [Verwandte Seiten](#verwandte-seiten)

## Die Aussage

> **Microsoft SQL Server ist derzeit bekanntermaßen mit zusätzlicher Arbeit möglich.**

In Exponential 6.0.x wird es nicht unterstützt: Es gibt keinen Treiber, keinen Schema-Handler und keinen Eintrag im
Installer dafür. Die Dokumentation des Projekts sagt ausdrücklich, dass nichts davon als Versprechen eines Release mit
einem solchen Treiber gelesen werden soll. Die unterstützten Datenbanken in der offiziellen Reihenfolge sind 1. SQLite 3
(Standard) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL
([Datenbanken in der offiziellen Reihenfolge](datenbanken-in-der-offiziellen-reihenfolge.md)).

> **Warnung**
> Führen Sie SQL Server in einem Tarif, einem Bestellformular oder Marketingmaterial für Exponential nicht als
> verfügbare Datenbank auf.

## Was es heute gibt

Aus [Datenbankunterstützung](../../datenbankunterstuetzung.md#microsoft-sql-server-möglich-mit-zusätzlichem-aufwand):

| Teil | Was es bedeutet |
|---|---|
| Eine kleine Treiberschnittstelle | Jede Datenbank wird über `eZDBInterface` (`lib/ezdb/classes/ezdbinterface.php`) erreicht; fünf vorhandene Treiber implementieren sie, von einer Dateidatenbank bis zu einem Dokumentenspeicher |
| Ein von der Engine unabhängiges Schema | `share/db_schema.dba` und `share/db_data.dba`, von einem Schema-Handler in die DDL einer Engine übersetzt |
| Austauschbare Schema-Handler | `settings/dbschema.ini` ordnet einem Typ eine Handler-Klasse zu |
| Austauschbare Treiber-Aliase | `site.ini [DatabaseSettings] ImplementationAlias[]` wird von einer Erweiterung erweitert, so wie `ezoracle` seine Aliase hinzufügt |
| Werkzeuge, die SQL Server lesen | der Datenbankmanager der Erweiterung `sevenx_dse` bringt einen MS-SQL-Treiber zum Durchsehen von Tabellen mit; weder er noch ein anderes Werkzeug ist ein Weg, eine Website auf SQL Server zu betreiben |
| PHP-Treiber | Microsoft veröffentlicht `sqlsrv` und `pdo_sqlsrv` für PHP; nicht mit Exponential getestet |

## Was fehlt

Die Arbeitsliste, zusammengefasst von derselben Seite:

| Teil | Was es umfasst |
|---|---|
| Eine Treiberklasse | `eZDBInterface` auf `sqlsrv`: Verbindungen, Abfragen mit Offset und Limit, Transaktionen, Identitätsspalten, Hilfsfunktionen für Zeichenketten und Hashes |
| Die Aggregatfunktion `BIT_OR` | SQL Server hat keine; die Inhalts-Engine faltet damit Sprachmasken |
| `UPDATE ... JOIN` | SQL Server schreibt es als `UPDATE ... FROM ... JOIN` |
| Ein Schema-Handler | `.dba` zu DDL von SQL Server und zurück, für Installationen und die Upgrade-Prüfung |
| SQL-Dateien | das erzeugte Schema und die Update-Dateien jedes späteren Release |
| Einträge im Installer | der Setup-Assistent, die Prüfung der Erweiterungen und der Alias `--db` von `exp:install` |
| Groß-/Kleinschreibung und Sortierung | eine UTF-8-Sortierung oder Spalten vom Typ `NVARCHAR` |
| Erweiterungen | jede Erweiterung mit eigenem SQL muss geprüft werden |
| Tests und CI | ein CI-Job mit einem SQL-Server-Container |

Wie groß diese Arbeit ist, wurde nicht gemessen, und das Projekt nennt keine Schätzung. Wer sie übernehmen möchte, ist
eingeladen, sich im [Software-Tracker](https://github.com/se7enxweb/exponential/issues) abzustimmen, mit der Erweiterung
`ezoracle` als Vorbild für einen Treiber, der außerhalb des Kernels ausgeliefert wird.

## Was Sie einem Kunden sagen

Eine kurze Antwort, die der Support verwenden kann:

```text
Exponential 6.0.x does not run on Microsoft SQL Server today. The project states that it is
known to be possible with additional work, but there is no driver in the current release.
The supported databases, in the project's order, are SQLite 3 (the default), PostgreSQL,
MongoDB, MariaDB, Oracle and MySQL. We can host your site on <the databases of your plan>.
```

Auf Deutsch etwa: „Exponential 6.0.x läuft heute nicht auf Microsoft SQL Server. Das Projekt sagt, dass es mit
zusätzlicher Arbeit bekanntermaßen möglich ist, aber im aktuellen Release gibt es keinen Treiber. Die unterstützten
Datenbanken sind in der Reihenfolge des Projekts SQLite 3 (der Standard), PostgreSQL, MongoDB, MariaDB, Oracle und
MySQL. Wir können Ihre Website auf <den Datenbanken Ihres Tarifs> hosten.“

Wenn der Grund des Kunden ist, dass seine Organisation für ihre eigenen Daten auf SQL Server standardisiert, ist eine
Website-Datenbank einer anderen Engine daneben meist akzeptabel; fragen Sie, was die Richtlinie tatsächlich verlangt.

## Was ein Hoster stattdessen anbieten kann

- **SQLite 3** braucht überhaupt keinen Datenbankserver, was Kunden entgegenkommt, die vor allem kein zweites
  Datenbankprodukt betreiben möchten ([SQLite für Hoster](sqlite-fuer-hoster.md)).
- **PostgreSQL** ist der empfohlene Datenbankserver ([PostgreSQL für Hoster](postgresql-fuer-hoster.md)).
- **Auf Windows-Servern** ist Windows in Stufe 2 (funktioniert bekanntermaßen), mit den offiziellen PHP-Builds für
  Windows und Apache oder IIS mit FastCGI; lesen Sie zuerst, was dort fehlt
  ([Betriebssysteme für Hoster](betriebssysteme-fuer-hoster.md), [Betriebssystemunterstützung: Windows](../../betriebssystemunterstuetzung.md#windows)).

## Verwandte Seiten

- [Datenbanken in der offiziellen Reihenfolge](datenbanken-in-der-offiziellen-reihenfolge.md)
- [Datenbankunterstützung: Microsoft SQL Server](../../datenbankunterstuetzung.md#microsoft-sql-server-möglich-mit-zusätzlichem-aufwand)
- [Kapitel 9.10: Microsoft SQL Server](../../../installation/09-datenbanken.md#910-microsoft-sql-server)
- [Mit dem Projekt und 7x zusammenarbeiten](../geschaeft/mit-dem-projekt-und-7x-zusammenarbeiten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
