---
title: Weg von einer alten Datenbank
audience: admin, dba, decision-maker
category: install-upgrade
order: 13
summary: Warum und wie Sie eine bestehende Exponential-Website von einer Datenbank lösen, die sie zurückhält - ein altes MySQL mit MyISAM-Tabellen oder einem Drei-Byte-Zeichensatz, ein Server ohne Herstellerpflege, SQLite auf einer Website, die einem Rechner entwachsen ist - hin zu PostgreSQL 13 bis 19, aktuellem MariaDB oder MySQL, Oracle, MongoDB oder Microsoft SQL Server und Azure SQL, mit den Befehlen, die es für jeden Schritt gibt.
keywords: Datenbank umziehen, Datenbank migrieren, Datenbank wechseln, Engine wechseln, alte Datenbank, MySQL 5, MyISAM, InnoDB, utf8mb4, Emoji, Ende der Pflege, SQLite nach PostgreSQL, MySQL nach PostgreSQL, MySQL nach SQL Server, Azure SQL, Oracle, MongoDB, Clustering, Lesereplikate, ezsqldumpschema, ezsqlinsertschema, ezconvertmysqltabletype, ezconvertdbcharset, exp:cluster:enable, move database, migrate database, switch engine, old database, legacy database
featured: true
context: setup/info
type: help
---
# Weg von einer alten Datenbank

Diese Seite richtet sich an Administratoren und Entscheider, deren Exponential-Website noch auf der Datenbank läuft, auf
der sie vor Jahren installiert wurde. Exponential 6.0.x läuft auf sieben Datenbank-Engines, von SQLite bis Microsoft
SQL Server, jeweils in aktuellen Versionen ([Unterstützte Datenbanken](datenbankunterstuetzung.md#unterstützungsmatrix)).
Die Inhalte einer Website hängen nicht von der Engine ab, in der sie gespeichert sind: Dieselben Objekte, Versionen,
Übersetzungen, URLs und Layouts ziehen mit um. Die Datenbank, mit der eine Website begonnen hat, ist also eine Wahl,
die Sie ändern können, keine Grenze, mit der Sie leben müssen.

## Inhalt

1. [Zeichen, dass die Datenbank die Website zurückhält](#zeichen-dass-die-datenbank-die-website-zurückhält)
2. [Was eine aktuelle Engine Ihnen gibt](#was-eine-aktuelle-engine-ihnen-gibt)
3. [Wohin Sie wechseln](#wohin-sie-wechseln)
4. [Zuerst an Ort und Stelle beheben: altes MySQL und MariaDB](#zuerst-an-ort-und-stelle-beheben-altes-mysql-und-mariadb)
5. [Der Umzug Schritt für Schritt](#der-umzug-schritt-für-schritt)
6. [Nach dem Umzug: Clustering und Replikate](#nach-dem-umzug-clustering-und-replikate)
7. [Verwandte Seiten](#verwandte-seiten)

## Zeichen, dass die Datenbank die Website zurückhält

| Was Sie sehen | Die Ursache | Wohin es führt |
|---|---|---|
| Emoji und manche Zeichen anderer Schriften gehen verloren oder werden abgewiesen | der alte Drei-Byte-Zeichensatz `utf8` von MySQL | `utf8mb4` auf demselben Server oder eine andere Engine |
| Ein fehlgeschlagenes Veröffentlichen hinterlässt halb geschriebene Inhalte; Tabellen sind während Importen gesperrt | MyISAM-Tabellen, die keine Transaktionen haben und ganze Tabellen sperren | InnoDB auf demselben Server oder eine andere Engine |
| Der Server erhält keine Sicherheitsupdates mehr | MySQL 5.x, ein altes MariaDB, PostgreSQL 12 oder älter, Oracle 18c oder älter, SQL Server 2016 oder älter | eine aktuelle Version derselben Engine oder eine andere Engine |
| Die Website muss auf einem zweiten Webserver laufen | SQLite hält die ganze Datenbank in einer Datei auf einem Rechner | PostgreSQL, MariaDB, MySQL, Oracle, MongoDB oder SQL Server, die beliebig viele Webserver bedienen |
| Die IT betreibt eine Datenbankplattform, und Ihre ist es nicht | die Website wurde auf dem installiert, was gerade da war | die Engine, die Ihre Organisation bereits betreibt, sichert und überwacht |
| Lesezugriffe bremsen die Website zu Spitzenzeiten | ein Datenbankserver erledigt jeden Lesezugriff | Lesereplikate: PostgreSQL, MariaDB, MySQL, Oracle und SQL Server |

## Was eine aktuelle Engine Ihnen gibt

- **Transaktionen.** Auf InnoDB, PostgreSQL, Oracle und SQL Server wird ein Veröffentlichen als Ganzes festgeschrieben
  oder zurückgerollt.
- **Jedes Zeichen.** Volles Unicode in Inhalten, URLs und Suche: `utf8mb4` auf MariaDB und MySQL, `UTF8` auf
  PostgreSQL, `AL32UTF8` auf Oracle, UTF-8-Kollationen auf SQL Server 2019 und neuer.
- **Mehrere Webserver an einer Datenbank**, wobei der DFS-Cluster-Datei-Handler seine Metadaten auf derselben Engine
  hält: MySQL und MariaDB, PostgreSQL 13 bis 19, Oracle, SQL Server und Azure SQL, MongoDB
  ([Clustering in Exponential 6.0](../funktionen/6.0/clustering-ueberblick.md)).
- **Lesereplikate** für anonymen Verkehr: Streaming-Standbys von PostgreSQL, Replikate von MariaDB und MySQL, Oracle
  und Always-On-Verfügbarkeitsgruppen von SQL Server.
- **Verwaltete Datenbanken.** Azure SQL Database und Managed Instance sowie die verwalteten PostgreSQL-, MySQL- und
  MariaDB-Dienste der Cloud-Anbieter nehmen Ihnen Backups, Patches und Failover ab.
- **Bei jedem Push getestet.** PostgreSQL 13, 15, 18 und 19, MySQL 8.0 und SQL Server 2017 bis 2025 werden in der
  Continuous Integration des Projekts geprüft ([Testabdeckung](datenbankunterstuetzung.md#testabdeckung-ci)).

## Wohin Sie wechseln

| Sie wollen | Wechseln Sie zu |
|---|---|
| Den vom Projekt empfohlenen Datenbankserver | **PostgreSQL** 13 bis 19 ([PostgreSQL 18 und 19](../funktionen/6.0/postgresql-18-und-19.md)) |
| Bei dem bleiben, was Sie kennen, aber aktuell | **MariaDB** oder **MySQL** mit InnoDB und `utf8mb4` (oft ganz ohne Umzug: [unten](#zuerst-an-ort-und-stelle-beheben-altes-mysql-und-mariadb)) |
| Die Plattform von Microsoft, im eigenen Haus oder in Azure | **SQL Server** 2017 bis 2025 oder **Azure SQL** ([SQL-Server-Treiber](../funktionen/6.0/sqlserver-datenbanktreiber.md)) |
| Oracle, wo die Organisation es betreibt | **Oracle** 19c und neuer |
| Einen Dokumentenspeicher, den die Organisation bereits betreibt | **MongoDB** 6.0 und neuer ([MongoDB als Datenbank](../funktionen/6.0/mongodb-datenbankunterstuetzung.md)) |
| Einen Rechner und gar keinen Datenbankserver | **SQLite** 3, der Standard |

Ein ausführlicherer Vergleich: [Eine Engine wählen](datenbankadministratoren/eine-engine-waehlen.md).

## Zuerst an Ort und Stelle beheben: altes MySQL und MariaDB

Viele alte MySQL- und MariaDB-Websites brauchen keine neue Engine, sondern nur einen aktuellen Server und zwei
Umwandlungen, die der Kernel als Skripte mitliefert:

```bash
# MyISAM nach InnoDB: die Tabellentypen auflisten, dann umwandeln
php bin/php/ezconvertmysqltabletype.php --host=localhost --user=exponential --password='<Passwort>' --database=exponential --list
php bin/php/ezconvertmysqltabletype.php --host=localhost --user=exponential --password='<Passwort>' --database=exponential --newtype=innodb
# die Tabellen der Website nach UTF-8 (zuerst mit --help aufrufen)
php bin/php/ezconvertdbcharset.php --help
```

Sichern Sie die Datenbank vorher und führen Sie beides in einem Wartungsfenster aus. Aktualisieren Sie danach den
Server selbst auf eine Version, die Ihr Anbieter pflegt, auf dem Upgrade-Weg des Anbieters.

## Der Umzug Schritt für Schritt

Das vollständige Verfahren mit jedem Befehl und jeder Option steht unter
[Eine Website auf eine andere Engine umziehen](datenbankadministratoren/zwischen-engines-wechseln.md). Kurz:

1. **Planen** Sie ein Wartungsfenster und proben Sie den ganzen Umzug an einer Kopie der Website.
2. **Bereiten Sie das Ziel vor**: die Datenbank, ihr Konto und ihren Zeichensatz, wie die Einrichtungsseite der Engine
   es beschreibt.
3. **Schema und Daten kopieren.**
   - Zwischen SQL-Engines (SQLite, PostgreSQL, MariaDB, MySQL und Oracle mit `ezoracle`): mit
     `bin/php/ezsqldumpschema.php --format=generic --output-array --output-types=all` in das engine-neutrale Format
     `.dba` exportieren und mit `bin/php/ezsqlinsertschema.php --insert-types=all --type=<Engine>` laden. Die
     Umwandlung von MySQL nach PostgreSQL ist auf PostgreSQL 18 und 19 Byte für Byte geprüft
     ([PostgreSQL 18 und 19](../funktionen/6.0/postgresql-18-und-19.md)).
   - Nach Oracle: `ext:ezoracle:mysql2oracle-schema` und `ext:ezoracle:mysql2oracle-data`, danach
     `ext:ezoracle:sequence-sync`.
   - Nach MongoDB: die Skripte in `bin/mongodb/` (`export_mysql.sh`, `mysql2ndjson.py`, `import_all.sh`).
4. **Stellen Sie die Einstellungen um**: `DatabaseImplementation` und die Verbindungsschlüssel in
   `settings/override/site.ini.append.php`, dann jeden Cache leeren und die PHP-Worker neu starten.
5. **Prüfen Sie vor dem Wiederfreigeben**: Die Zeilenzahlen von `ezcontentobject`, `ezcontentobject_tree` und
   `ezcontentobject_attribute` stimmen auf beiden Seiten überein, die Prüfung der Datenbankkonsistenz (Setup >
   Upgrade-Prüfung) ist sauber, und ein Testobjekt lässt sich veröffentlichen.
6. **Halten Sie die alte Datenbank eine Weile schreibgeschützt** und legen Sie sie dann mit ihren Zugangsdaten still.

Spätere Upgrades verwenden die Update-Dateien der Engine, auf der die Website jetzt läuft.

## Nach dem Umzug: Clustering und Replikate

Auf einem Datenbankserver kann die Website über einen Webserver hinauswachsen:

```bash
./console exp:cluster:enable --share=/mnt/exp-dfs/example.com             # der Plan, nichts geändert
./console exp:cluster:enable --share=/mnt/exp-dfs/example.com --apply     # ausführen
```

`exp:cluster:enable` legt den Share an, erstellt die Cluster-Tabellen in der Datenbank der Website (`--backend` wählt
`mysqli`, `postgresql`, `oracle`, `sqlserver`, `mongodb` oder `sqlite`; `auto` folgt der Engine der Website), schreibt
die Einstellungen und verschiebt die Dateien hinein
([Ein DFS-Cluster mit einem Knoten in einem Befehl](../funktionen/6.0/cluster-befehle.md)). Fügen Sie die weiteren
Webserver und ein Lesereplikat hinzu, wie [Clustering in Exponential 6.0](../funktionen/6.0/clustering-ueberblick.md)
zeigt.

## Verwandte Seiten

- [Unterstützte Datenbanken](datenbankunterstuetzung.md): jede Engine, mit der Unterstützungsmatrix
- [Eine Website auf eine andere Engine umziehen](datenbankadministratoren/zwischen-engines-wechseln.md): das vollständige Verfahren
- [Eine Engine wählen](datenbankadministratoren/eine-engine-waehlen.md)
- [Clustering in Exponential 6.0](../funktionen/6.0/clustering-ueberblick.md)
- [Exponential unter Windows](exponential-unter-windows.md)
- [Betriebssystemunterstützung](betriebssystemunterstuetzung.md)
