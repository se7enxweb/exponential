---
title: Eine Datenbank in der offiziellen Reihenfolge wählen
audience: government, hosting, admin, project-manager
category: public-sector
order: 50
summary: Für Architektinnen, Architekten und IT-Teams im öffentlichen Sektor, die die Datenbank einer Exponential-Website wählen. Die sechs unterstützten Datenbanken in der offiziellen Reihenfolge des Projekts, wie sie sich bei Verfügbarkeit, Datensicherungen und Beschaffung unterscheiden und wie Sie die Wahl dem Datenbankstandard Ihrer Organisation zuordnen.
keywords: Datenbank, offizielle Reihenfolge, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, Microsoft SQL Server, DatabaseImplementation, Datenbankstandard, Beschaffung, Replikation, Hochverfügbarkeit, Sicherungswerkzeuge, database, official order, database standard, procurement, replication, high availability, backup tools
type: help
---
# Eine Datenbank in der offiziellen Reihenfolge wählen

Diese Seite richtet sich an Architektinnen, Architekten und IT-Teams in öffentlichen Stellen, die entscheiden, welche
Datenbank eine Exponential-Website betreibt. Sie beantwortet, welche Datenbanken unterstützt werden, in welcher
Reihenfolge das Projekt sie empfiehlt, was jede für Verfügbarkeit, Datensicherungen und Beschaffung bedeutet und wie
Sie die Wahl an einen Datenbankstandard anpassen, den Ihre Organisation vielleicht schon hat. Die Aussage zur
Unterstützung steht in [Datenbankunterstützung](../../../datenbankunterstuetzung.md); die Sicht des Hostings in
[Datenbanken in der offiziellen Reihenfolge](../../../hosting/grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md).

## Inhalt

1. [Die offizielle Reihenfolge](#die-offizielle-reihenfolge)
2. [Gegenüberstellung für eine öffentliche Stelle](#gegenüberstellung-für-eine-öffentliche-stelle)
3. [Ihren Datenbankstandard treffen](#ihren-datenbankstandard-treffen)
4. [Welche Datenbank für welche Situation](#welche-datenbank-für-welche-situation)
5. [Herausfinden, worauf eine Website läuft](#herausfinden-worauf-eine-website-läuft)
6. [Später wechseln](#später-wechseln)
7. [Checkliste](#checkliste)

## Die offizielle Reihenfolge

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Präferenz:** 1. SQLite 3 (der Standard) ·
> 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit bekanntermaßen mit
> zusätzlichem Aufwand möglich.

Die Reihenfolge ist die Reihenfolge der Empfehlung für neue Websites; sie bedeutet nicht, dass die späteren weniger
gut funktionieren ([Datenbanken in der offiziellen Reihenfolge](../../../hosting/grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md)).
Microsoft SQL Server hat in 6.0.x keinen Treiber ([Stand von Microsoft SQL Server](../../../hosting/grundlagen/stand-von-microsoft-sql-server.md));
betreibt Ihre Organisation nur SQL Server, planen Sie diesen Aufwand ein oder wählen Sie für diese Anwendung eine
andere Engine.

## Gegenüberstellung für eine öffentliche Stelle

| | 1. SQLite 3 | 2. PostgreSQL | 3. MongoDB | 4. MariaDB | 5. Oracle | 6. MySQL |
|---|---|---|---|---|---|---|
| Zu betreibender Datenbankserver | keiner (eine Datei) | ja | ja | ja | ja | ja |
| Mehrere Webserver an einer Datenbank | nein | ja | ja | ja | ja | ja |
| Eigene Replikation | keine | ja, mit Wiederherstellung zu einem Zeitpunkt | siehe Hersteller | siehe Hersteller | siehe Hersteller | siehe Hersteller |
| Von Exponential genutzte Lese-Replikate | nein | nein | nein | ja (`UseSlaveServer`) | nein | ja (`UseSlaveServer`) |
| Sicherungswerkzeug | `sqlite3 .backup` | `pg_dump` | `mongodump` | `mariadb-dump` | `ext:ezoracle:datapump`, `expdp` | `mysqldump` |
| In der CI des Projekts getestet | Unit-Tests des Treibers | ja | Unit-Tests des Adapters | über den MySQL-Job | nein | ja |

Quellen: [Datenbankunterstützung](../../../datenbankunterstuetzung.md) und die Hosting-Seiten der einzelnen Engines.
Lese-Replikate sind nur im MySQL-Treiber eingebaut, den MariaDB mitnutzt; siehe [Lese-Replikate](../verfuegbarkeit/lese-replikate.md).

## Ihren Datenbankstandard treffen

Viele öffentliche Stellen haben einen von der zentralen IT festgelegten Datenbankstandard, mit
Datenbankadministratoren, Lizenzen und Sicherungswerkzeugen, die bereits vorhanden sind. Das entscheidet oft mehr als
technische Vorlieben:

| Ihre Organisation standardisiert auf | Erwägen Sie | Hinweis |
|---|---|---|
| PostgreSQL | PostgreSQL | der empfohlene Datenbankserver; braucht `pgcrypto` ([PostgreSQL für Hosts](../../../hosting/grundlagen/postgresql-fuer-hoster.md)) |
| MariaDB oder MySQL | MariaDB oder MySQL | derselbe Treiber; `utf8mb4` und InnoDB ([MariaDB für Hosts](../../../hosting/grundlagen/mariadb-fuer-hoster.md)) |
| Oracle | Oracle 19c oder neuer | über die Erweiterung `ezoracle` und `oci8` ([Oracle für Hosts](../../../hosting/grundlagen/oracle-fuer-hoster.md)) |
| MongoDB | MongoDB | lesen Sie zuerst die dokumentierten Grenzen ([MongoDB für Hosts](../../../hosting/grundlagen/mongodb-fuer-hoster.md)) |
| kein Datenbankstandard, eine Maschine | SQLite 3 | nichts zusätzlich zu betreiben, zu lizenzieren oder zu patchen |

Wenn Sie die Engine wählen, die Ihr Datenbankteam bereits betreibt, bringt das meist Überwachung, Datensicherungen und
Rufbereitschaft mit sich. Das ist mehr wert als kleine Unterschiede im Funktionsumfang.

## Welche Datenbank für welche Situation

| Situation | Wahl | Warum |
|---|---|---|
| Eine Website, eine Maschine, kleines Team | SQLite 3 | kein Datenbankserver; Datensicherungen sind eine Dateioperation mit dem eigenen Werkzeug von SQLite |
| Mehr als ein Webserver muss die Website ausliefern | ein Datenbankserver, an erster Stelle PostgreSQL | SQLite braucht jeden Prozess auf einer Maschine |
| Viele Redakteurinnen und Redakteure veröffentlichen den ganzen Tag große Änderungen | ein Datenbankserver | SQLite reiht schreibende Prozesse für eine Sperre in eine Warteschlange |
| Eine Kontinuitätspflicht, die eine Bereitschaftsdatenbank verlangt | PostgreSQL oder Ihre Standard-Server-Engine mit deren Replikation | SQLite hat keine eigene Replikation |
| Benutzer und Rechte auf Datenbankebene sind durch Richtlinien vorgeschrieben | ein Datenbankserver | die Zugriffskontrolle von SQLite ist die des Dateisystems |

## Herausfinden, worauf eine Website läuft

Eine Einstellung entscheidet über die Engine:

```bash
grep -rn "DatabaseImplementation" settings/override settings/siteaccess/*/site.ini.append.php settings/site.ini
```

Die Werte sind `sqlite3`, `ezpostgresql` (oder `pgsql`), `mongodb`, `ezmysqli` und, mit ihrer Erweiterung, `ezoracle`
([Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md)). Die Seite **Setup > Systeminformationen** zeigt die
Datenbank ebenfalls an ([Systeminformationen](../../../../anleitungen/systeminformationen.md)).

## Später wechseln

Eine Website kann auf eine andere Engine umziehen; die Hosting-Seite
[Die Datenbank-Engine wechseln](../../../hosting/migrationen/die-datenbank-engine-einer-kunden-website-wechseln.md) beschreibt, wie. Behandeln
Sie das als geplante Änderung mit eigener Datensicherung und Test-Wiederherstellung. Mit SQLite zu beginnen und zu
PostgreSQL zu wechseln, sobald ein zweiter Webserver nötig ist, ist ein dokumentierter Weg.

## Checkliste

- [ ] Die gewählte Engine ist eine der sechs; es wird nicht angenommen, dass Microsoft SQL Server funktioniert.
- [ ] Sie entspricht dem Datenbankstandard Ihrer Organisation, oder die Ausnahme ist dokumentiert.
- [ ] Die Anforderungen an die Verfügbarkeit (mehrere Webserver, eine Bereitschaftsdatenbank) sind mit der Engine vereinbar.
- [ ] Das Sicherungswerkzeug der Engine ist Teil Ihres Sicherungsplans.
- [ ] Das Datenbankteam kennt die Mindestversionen in [Datenbankunterstützung](../../../datenbankunterstuetzung.md).

## Verwandte Seiten

- [Hosting](README.md): Übersicht dieses Ordners
- [Hochverfügbarkeit der Datenbank](../verfuegbarkeit/hochverfuegbarkeit-der-datenbank.md)
- [Datensicherungen je Datenbank-Engine](../datensicherung-und-wiederherstellung/datensicherungen-je-datenbank-engine.md)
- [9 Datenbanken](../../../../installation/09-datenbanken.md) im Installationshandbuch
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
