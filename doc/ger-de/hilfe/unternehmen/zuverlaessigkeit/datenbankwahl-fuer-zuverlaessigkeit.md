---
title: Datenbankwahl für Zuverlässigkeit
audience: businesses, project-manager, admin, hosting
category: business-solutions
order: 150
summary: Auf welcher Datenbank ein Unternehmen Exponential betreiben sollte, nach der offiziellen Reihenfolge des Projekts, und was jede Wahl für Schreibzugriffe unter Last, mehrere Webserver, Backups, Tests und die Fähigkeiten bedeutet, die Ihr Team braucht.
keywords: Datenbank, Datenbankwahl, Zuverlässigkeit, Backups, Replikation, mehrere Webserver, database, database choice, SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL, reliability, backups, replication, several web servers, CI
type: help
---
# Datenbankwahl für Zuverlässigkeit

Diese Seite richtet sich an Inhaber, Führungskräfte und Projektleitungen, die die Datenbank hinter einer
Exponential-Website wählen oder übernehmen. Sie erklärt die offizielle Reihenfolge des Projekts, was jede Wahl für die
Zuverlässigkeit eines Unternehmens bedeutet und wie Sie entscheiden. Der vollständige technische Vergleich steht unter
[Datenbankunterstützung](../../datenbankunterstuetzung.md); das Kapitel für den Betrieb ist
[Kapitel 9 des Installationsbuchs](../../../installation/09-datenbanken.md).

## Inhalt

1. [Die offizielle Reihenfolge](#die-offizielle-reihenfolge)
2. [Worauf sich die Wahl auswirkt](#worauf-sich-die-wahl-auswirkt)
3. [Jede Datenbank aus geschäftlicher Sicht](#jede-datenbank-aus-geschäftlicher-sicht)
4. [Wie gut jede getestet ist](#wie-gut-jede-getestet-ist)
5. [Backups je Datenbank](#backups-je-datenbank)
6. [Die Wahl für Ihr Unternehmen](#die-wahl-für-ihr-unternehmen)
7. [Später wechseln](#später-wechseln)

## Die offizielle Reihenfolge

„**Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Präferenz:** 1. SQLite 3 (Standard) ·
2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit bekanntermaßen mit
zusätzlichem Aufwand möglich“ ([Datenbankunterstützung](../../datenbankunterstuetzung.md#die-unterstützten-datenbanken-in-ihrer-reihenfolge),
ebenso in der [README, „Requirements“](https://github.com/se7enxweb/exponential/blob/main/README.md#requirements)).

Die Reihenfolge ist eine Empfehlung für neue Websites. Alle sechs werden unterstützt; die Seite merkt an, dass MySQL „am
Ende der Reihenfolge steht, nicht weil es schlechter funktioniert, sondern weil das Projekt für neue Websites zuerst die
anderen empfiehlt“.

## Worauf sich die Wahl auswirkt

| Belang | Warum er für ein Unternehmen wichtig ist |
|---|---|
| Schreibzugriffe unter Last | Viele Redakteure, die veröffentlichen, viele Bestellungen an der Kasse, viele Anmeldungen gleichzeitig |
| Mehrere Webserver | Ob Sie später Server hinzufügen können ([Clustering und Hochverfügbarkeit](clustering-und-hochverfuegbarkeit.md)) |
| Backups und zeitpunktgenaue Wiederherstellung | Wie viele Daten Sie verlieren können und wie schnell Sie wiederherstellen ([Backups, die sich wiederherstellen lassen](backups-die-sich-wiederherstellen-lassen.md)) |
| Tests | Wie viel der eigenen Tests des Projekts Ihre Wahl abdeckt |
| Fähigkeiten | Ob Ihr Team oder Ihr Hosting-Partner sie betreiben und reparieren kann |
| Hosting | Ob Ihr Hoster sie anbietet und verwaltet |

## Jede Datenbank aus geschäftlicher Sicht

Aus [Datenbankunterstützung](../../datenbankunterstuetzung.md#die-unterstützten-datenbanken-in-ihrer-reihenfolge) und
[Leistung und Skalierung](../../datenbankunterstuetzung.md#performance-und-skalierung):

| Datenbank | Aus geschäftlicher Sicht |
|---|---|
| **1. SQLite 3** | Der Standard. Kein Datenbankserver: „Das ganze Content-Repository ist eine Datei.“ Eine vollwertige Produktionsdatenbank für eine Website, die von **einem Rechner** ausgeliefert wird. Lesezugriffe skalieren; Schreibzugriffe stehen für eine einzige Sperre an, daher: „Für viele Redakteure, die gleichzeitig veröffentlichen, verwenden Sie MySQL oder PostgreSQL“ ([Betrieb einer Site](../../../anleitungen/betrieb-einer-website.md#7-den-eigenen-datenbanktreiber-kennen)) |
| **2. PostgreSQL** | „Der empfohlene Datenbankserver: striktes SQL, Sperren auf Zeilenebene, eigene Replikation und zeitpunktgenaue Wiederherstellung, und er wird bei jedem Push in der CI getestet“ |
| **3. MongoDB** | Über einen Adapter unterstützt; „die Wahl für Organisationen, die auf MongoDB standardisiert sind“. Lesen Sie zuerst seine [Grenzen](../../datenbankunterstuetzung.md#grenzen-von-mongodb) |
| **4. MariaDB** | „Der MySQL-kompatible Server, den das Projekt bevorzugt“; derselbe Treiber wie MySQL |
| **5. Oracle** | Unterstützt (19c und neuer) über die Erweiterung `ezoracle`, die in jeder Installation vorhanden ist |
| **6. MySQL** | Unterstützt mit demselben Treiber wie MariaDB und in der CI getestet |

Microsoft SQL Server hat in Exponential 6.0.x keinen Treiber; er ist mit zusätzlichem Aufwand „bekanntermaßen möglich“
([Datenbankunterstützung](../../datenbankunterstuetzung.md#microsoft-sql-server-möglich-mit-zusätzlichem-aufwand)).

Mehrere Webserver an einer Datenbank: Jede Engine außer SQLite unterstützt das
([Datenbankunterstützung, auf einen Blick](../../datenbankunterstuetzung.md#auf-einen-blick)).

## Wie gut jede getestet ist

Was die Continuous Integration des Projekts bei jedem Push ausführt
([Datenbankunterstützung, Testabdeckung](../../datenbankunterstuetzung.md#testabdeckung-ci)):

| Datenbank | In der CI des Projekts |
|---|---|
| SQLite 3 | Unit-Tests des Treibers, unter PHP 8.1 bis 8.5 |
| PostgreSQL | Datenbanktests gegen PostgreSQL 15 |
| MongoDB | Unit-Tests des Adapters ohne Server |
| MariaDB | Nicht direkt; indirekt durch den MySQL-Job abgedeckt (derselbe Treiber) |
| Oracle | Nein |
| MySQL | Datenbanktests gegen MySQL 8.0 |

Für ein Unternehmen, das möglichst viele Tests des Projekts hinter seinem Datenbankserver haben möchte, sind PostgreSQL
und MySQL die Engines mit Live-Datenbanktests bei jedem Push. Proben Sie bei den anderen Aktualisierungen besonders
sorgfältig an einer Kopie Ihrer eigenen Website ([Aktualisierungen ohne Überraschungen](../../../../eng-us/help/businesses/reliability/upgrades-without-surprises.md)).

## Backups je Datenbank

Jede Engine hat ihr eigenes Werkzeug für Backup und Wiederherstellung; die Befehle stehen unter
[Datenbankunterstützung, Backup und Wiederherstellung](../../datenbankunterstuetzung.md#backup-und-wiederherstellung). Aus geschäftlicher
Sicht:

- **SQLite**: ein konsistentes Online-Backup mit `.backup`; „kopieren Sie nie eine Datenbankdatei, die gerade benutzt
  wird“.
- **PostgreSQL**: Dumps sowie eine eigene zeitpunktgenaue Wiederherstellung für einen feineren Wiederherstellungspunkt.
- **MariaDB und MySQL**: Dumps mit `--single-transaction` sind für InnoDB-Tabellen konsistent, ohne sie zu sperren.
- **MongoDB**: `mongodump` und `mongorestore`.
- **Oracle**: Data Pump, über `ext:ezoracle:datapump` oder die eigenen Werkzeuge von Oracle.

Gleich welche Engine: Das Datenbank-Backup ist nur die eine Hälfte; die Dateien sind die andere.

## Die Wahl für Ihr Unternehmen

| Ihre Situation | Ein sinnvoller Ausgangspunkt |
|---|---|
| Eine kleine Website, ein Server, wenige Redakteure | SQLite, der Standard; am einfachsten zu betreiben und zu sichern |
| Ein Shop oder eine Mitgliederwebsite mit vielen gleichzeitigen Schreibzugriffen oder mehreren Redakteuren, die den ganzen Tag veröffentlichen | PostgreSQL oder MariaDB bzw. MySQL, wenn Ihr Hoster sich damit am besten auskennt |
| Sie erwarten, mehrere Webserver zu brauchen | Jede Engine außer SQLite |
| Ihre Organisation ist auf MongoDB oder Oracle standardisiert | Diese Engine, nachdem Sie ihren Abschnitt in [Datenbankunterstützung](../../datenbankunterstuetzung.md) gelesen haben |
| Ihr Hosting-Partner beherrscht eine Engine sehr gut | Gewichten Sie dieses Können stark: Eine gut betriebene Datenbank schlägt eine bessere, die niemand kennt |

> **Tipp**
> Fragen Sie Ihren Hosting-Partner, welche Engine er am häufigsten sichert, überwacht und wiederherstellt. Seine
> Erfahrung ist Teil Ihrer Zuverlässigkeit.

## Später wechseln

Eine Website lässt sich auf eine andere Datenbank umziehen; das Vorgehen steht unter
[Datenbankunterstützung, eine Website auf eine andere Datenbank umziehen](../../datenbankunterstuetzung.md#eine-site-auf-eine-andere-datenbank-umziehen)
und, für Hoster, unter [Die Datenbank-Engine einer Kunden-Website wechseln](../../hosting/migrationen/die-datenbank-engine-einer-kunden-website-wechseln.md).
Behandeln Sie es als Projekt: zuerst sichern, an einer Kopie proben, danach die Inhalte vergleichen und ein
Wartungsfenster einplanen.

Welche Datenbank eine Website heute nutzt, sehen Sie unter **Setup > Systeminformationen**; die Karte zur Datenbank
zeigt die Engine ([Systeminformationen](../../../anleitungen/systeminformationen.md)).

## Verwandte Seiten

- [Datenbankunterstützung](../../datenbankunterstuetzung.md)
- [Datenbanken in der offiziellen Reihenfolge (Hosting)](../../hosting/grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md)
- [Hosting auswählen](../auswahl/hosting-auswaehlen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
