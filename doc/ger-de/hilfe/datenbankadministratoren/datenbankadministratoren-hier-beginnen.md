---
title: "Datenbankadministratoren: hier beginnen"
audience: dba
category: database-administration
order: 1
summary: Wo Datenbankadministratoren mit Exponential beginnen. Die sechs unterstützten Engines in einem Absatz, die ersten fünfzehn Minuten, die Regeln, die jeder Datenbankadministrator einhält, und eine Übersicht aller Seiten für Datenbankadministratoren, von Einrichtung der Engine, Konten und Zeichensätzen bis zu Sicherungen, Upgrades, Leistung, Überwachung und Fehlerbehebung.
keywords: Datenbankadministrator, DBA, Datenbank, hier beginnen, Überblick, Index, SQLite, PostgreSQL, MongoDB, MariaDB, MySQL, Oracle, Sicherungen, Upgrades, Leistung, Überwachung, Sicherheit, database administrator, start here, overview, backups, performance, monitoring, security
featured: true
type: help
---
# Datenbankadministratoren: hier beginnen

Dieser Abschnitt der Hilfe richtet sich an **Datenbankadministratoren**: die Menschen, die die Datenbank einer
Exponential-Website anlegen, absichern, sichern, aktualisieren, abstimmen und überwachen, ob sie eine Website betreiben
oder viele, auf SQLite, PostgreSQL, MongoDB, MariaDB, Oracle oder MySQL. Er beschreibt die Datenbankschicht genau so, wie
der Kernel 6.0.x sie umsetzt, mit den Einstellungen, Skripten und Dateien, die Sie verwenden werden, und sagt klar, wo
etwas eine Erweiterung braucht oder nicht existiert.

Jede Angabe auf diesen Seiten wurde am Code, an den Einstellungsdateien und an der Dokumentation des Projekts geprüft, vor
allem an [Datenbankunterstützung](../datenbankunterstuetzung.md) und [Kapitel 9 der Installationsanleitung](../../installation/09-datenbanken.md).
Jede Seite nennt ihre Quellen.

## Inhalt

1. [Die Datenbank von Exponential in einem Absatz](#die-datenbank-von-exponential-in-einem-absatz)
2. [Ihre ersten fünfzehn Minuten](#ihre-ersten-fünfzehn-minuten)
3. [Sechs Regeln für jede Datenbank](#sechs-regeln-für-jede-datenbank)
4. [Übersicht der Seiten für Datenbankadministratoren](#übersicht-der-seiten-für-datenbankadministratoren)
5. [Seiten anderer Zielgruppen, die Sie verwenden werden](#seiten-anderer-zielgruppen-die-sie-verwenden-werden)
6. [Diese Seiten wiederfinden](#diese-seiten-wiederfinden)

## Die Datenbank von Exponential in einem Absatz

**Exponential** ist ein freies Open-Source-Content-Management-System in PHP; sein Inhalts-Repository liegt in einer
Datenbank, die über die Kernel-Klasse `eZDB` und einen von fünf Treibern erreicht wird. Sechs Datenbanken werden
unterstützt, in dieser Reihenfolge der Präferenz: **1. SQLite 3** (der Standard, eine Datei, für eine Website auf einer
Maschine) · **2. PostgreSQL** (der empfohlene Server) · **3. MongoDB** (über einen Adapter, mit dokumentierten Grenzen) ·
**4. MariaDB** · **5. Oracle** (19c und später, über die Erweiterung `ezoracle`) · **6. MySQL**. Microsoft SQL Server ist
derzeit bekanntermaßen mit zusätzlicher Arbeit möglich. Das Schema wird in einem engine-neutralen Format `.dba` geführt
und durch Update-Dateien je Engine geändert. Hochgeladene Dateien, Einstellungen und Caches liegen nicht in der Datenbank.

## Ihre ersten fünfzehn Minuten

1. **Die Engine finden** (2 Minuten): **Setup > Systeminformationen**, Karte Datenbank, oder
   `php bin/php/console exp:ini get site.ini/DatabaseSettings/DatabaseImplementation --allow-root-user`.
2. **Die Schicht lesen** (5 Minuten): [Die Datenbankschicht auf einer Seite](die-datenbankschicht-auf-einer-seite.md).
3. **Die Einrichtungsseite Ihrer Engine lesen** (5 Minuten): aus der Übersicht unten.
4. **Die Sicherungen prüfen** (3 Minuten): [Sicherungen je Engine](sicherungen-je-engine.md).

Folgen Sie dann dem [Lesepfad](lesepfad-fuer-datenbankadministratoren.md).

## Sechs Regeln für jede Datenbank

1. **Bearbeiten Sie nie mitgelieferte Dateien.** Einstellungen gehören nach `settings/override/`; Schemaänderungen kommen
   aus Update-Dateien.
2. **Ein Konto je Website, minimale Rechte, nie `root`.** ([Konten](datenbankkonten-und-rechte.md))
3. **Überall UTF-8**: `utf8mb4`, `UTF8`, `AL32UTF8`. ([Zeichensätze](zeichensaetze-und-sortierfolgen.md))
4. **Sichern Sie die Datenbank und die Dateien gemeinsam, und testen Sie die Wiederherstellung.** ([Sicherungen](sicherungen-je-engine.md))
5. **Leeren Sie nach jedem Schreibzugriff außerhalb von Exponential die Caches**, den SQL-Abfrage-Cache eingeschlossen.
   ([Abfrage-Cache](sql-abfrage-cache.md))
6. **Leeren Sie nach einer Einstellungsänderung den INI-Cache und starten Sie die Worker von Velocity neu.**
   ([Einstellungen ändern](datenbankeinstellungen-lesen-und-aendern.md))

## Übersicht der Seiten für Datenbankadministratoren

### Orientierung

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Die Datenbankschicht auf einer Seite](die-datenbankschicht-auf-einer-seite.md) | eZDB, die Treiber, die Schema-Handler verstehen und was wo gespeichert wird |
| [Unterstützte Engines und Versionen](unterstuetzte-engines-und-versionen.md) | unterstützte Versionen angeben und wissen, was die CI testet |
| [Eine Engine wählen](eine-engine-waehlen.md) | die richtige Datenbank für eine Website wählen |
| [PHP-Erweiterungen je Engine](php-erweiterungen-je-engine.md) | sicherstellen, dass jedes PHP die Treibererweiterung hat |
| [Die Referenz zu DatabaseSettings](referenz-databasesettings.md) | jede Verbindungseinstellung nachschlagen |
| [Datenbankeinstellungen lesen und ändern](datenbankeinstellungen-lesen-und-aendern.md) | den wirksamen Wert lesen und sicher ändern |

### Engines

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [SQLite 3 für Datenbankadministratoren](sqlite-fuer-datenbankadministratoren.md) | den Treiber, seine Dateien, PRAGMAs und WAL verstehen |
| [SQLite: die Schreibwarteschlange und Sperren](sqlite-schreibwarteschlange-und-sperren.md) | gleichzeitiges Veröffentlichen und Sperr-Timeouts handhaben |
| [SQLite-Wartung](sqlite-wartung.md) | ANALYZE, VACUUM, WAL-Checkpoints und Integritätsprüfungen ausführen |
| [SQLite-Dateien, Eigentümer und Rechte](sqlite-dateien-eigentuemer-und-rechte.md) | die Dateien so einrichten, dass jeder Prozess sie schreiben kann |
| [PostgreSQL einrichten](postgresql-einrichten.md), [PostgreSQL betreiben](postgresql-betrieb.md) | die Datenbank, pgcrypto, Sequenzen und Groß-/Kleinschreibung einrichten |
| [MySQL und MariaDB einrichten](mysql-und-mariadb-einrichten.md) | die Datenbank und das Konto anlegen |
| [MySQL und MariaDB: utf8mb4, der Verbindungszeichensatz und InnoDB](mysql-und-mariadb-zeichensatz-und-innodb.md) | Zeichensätze und Tabellentypen richtig setzen |
| [Lesereplikate für MySQL und MariaDB](mysql-lesereplikate.md) | Lesezugriffe an Replikate senden |
| [MongoDB einrichten](mongodb-einrichten.md), [MongoDB: Grenzen und Betrieb](mongodb-grenzen-und-betrieb.md) | MongoDB betreiben und wissen, was der Adapter nicht kann |
| [Oracle mit ezoracle einrichten](oracle-einrichten.md) | ein Oracle-Schema vorbereiten |
| [Oracle: persistente Verbindungen, DRCP und Tuning](oracle-verbindungen-und-tuning.md) | Oracle-Sessions bündeln und abstimmen |
| [Wartungsbefehle und Cronjobs für Oracle](oracle-wartungsbefehle.md) | die Befehle ext:ezoracle und die Cronjobs verwenden |
| [Microsoft SQL Server: Stand für Datenbankadministratoren](stand-von-microsoft-sql-server.md) | die Frage nach SQL Server richtig beantworten |

### Sicherheit und Schema

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Zeichensätze und Sortierfolgen](zeichensaetze-und-sortierfolgen.md) | UTF-8-Datenbanken anlegen und mit Groß-/Kleinschreibung umgehen |
| [Datenbankkonten und Rechte](datenbankkonten-und-rechte.md) | die minimalen Rechte vergeben, die funktionieren |
| [Die Datenbank absichern](die-datenbank-absichern.md) | Zugangsdaten, Netzwerkzugang und Sicherungen schützen |
| [Das .dba-Schemaformat und die mitgelieferten Schemadateien](das-dba-schemaformat.md) | wissen, woher das Schema kommt |
| [Schemawerkzeuge: Dump, Insert und Diff](schemawerkzeuge-dump-insert-diff.md) | ezsqldumpschema, ezsqlinsertschema und ezsqldiff verwenden |
| [Die Tabellen, die ein Datenbankadministrator kennen sollte](tabellen-die-sie-kennen-sollten.md) | sich in den Tabellen des Kernels zurechtfinden |

### Sicherungen, Upgrades und Umzüge

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Sicherungen je Engine](sicherungen-je-engine.md) | jede Engine konsistent sichern |
| [Eine Datenbank wiederherstellen](eine-datenbank-wiederherstellen.md) | wiederherstellen und Wiederherstellungen testen |
| [Upgrades und die SQL-Update-Dateien](upgrades-und-sql-update-dateien.md) | die richtigen Update-Dateien in der richtigen Reihenfolge anwenden |
| [Das Datenbank-Update auf 6.0.15 anwenden](das-datenbank-update-6-0-15-anwenden.md) | eine 6.0.x-Datenbank auf 6.0.15 bringen |
| [Das Schema nach einem Upgrade prüfen](das-schema-nach-einem-upgrade-pruefen.md) | das Schema mit der Upgrade-Prüfung und ezsqldiff verifizieren |
| [Eine Website auf eine andere Engine umziehen](zwischen-engines-wechseln.md) | die Datenbank-Engine einer laufenden Website wechseln |

### Betrieb und Leistung

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Sessions in der Datenbank](sessions-in-der-datenbank.md) | entscheiden, wo Sessions liegen, und sie aufräumen |
| [Die Tabellen des eingebauten Suchindex](tabellen-des-suchindex.md) | den Suchindex und seine Warteschlange pflegen |
| [Aufräumen: Cronjobs und Warteschlangentabellen](aufraeumen-cronjobs-und-warteschlangen.md) | Warteschlangen in Bewegung und Tabellen ordentlich halten |
| [Der SQL-Abfrage-Cache](sql-abfrage-cache.md) | den Abfrage-Cache sicher verwenden |
| [Langsame Abfragen finden](langsame-abfragen-finden.md) | die Anweisungen finden, die eine Seite langsam machen |
| [Leistung und Skalierung](leistung-und-skalierung.md) | die Hebel in der richtigen Reihenfolge ansetzen |
| [Clustering: DFS und seine Metadatendatenbank](clustering-dfs-und-die-datenbank.md) | die Datenbankseite eines Clusters mit mehreren Servern betreiben |
| [Die Datenbank überwachen](die-datenbank-ueberwachen.md) | Prüfungen und Alarme einrichten |

### Fehlerbehebung und Referenz

| Seite | Wobei sie Ihnen hilft |
|---|---|
| [Datenbankfehler beheben](datenbankfehler-beheben.md) | vom Symptom zur Lösung kommen |
| [Fehlercodes und Meldungen der Datenbank](fehlercodes-und-meldungen.md) | Fehlernummern und Meldungen lesen |
| [SQL von Erweiterungen für jede Engine prüfen](portables-sql-fuer-erweiterungen.md) | eine Erweiterung für eine Website ohne MySQL freigeben |
| [Häufige Fragen zur Datenbank](haeufige-fragen-zur-datenbank.md) | schnelle Antworten erhalten |
| [Checklisten für Datenbankadministratoren](checklisten-fuer-datenbankadministratoren.md) | einer Routine Schritt für Schritt folgen |
| [Glossar für Datenbankadministratoren](glossar-fuer-datenbankadministratoren.md) | einen Begriff nachschlagen |
| [Ein Lesepfad für Datenbankadministratoren](lesepfad-fuer-datenbankadministratoren.md) | die Seiten in einer sinnvollen Reihenfolge lernen |

## Seiten anderer Zielgruppen, die Sie verwenden werden

| Seite | Warum ein Datenbankadministrator sie liest |
|---|---|
| [Datenbankunterstützung](../datenbankunterstuetzung.md) | die Unterstützungsaussage des Projekts für jede Engine |
| [Kapitel 9, Datenbanken](../../installation/09-datenbanken.md) | die Betreiberreferenz hinter diesen Seiten |
| [Rundgang durch die Administration](../rundgang-durch-die-administration.md), [In der Administration anmelden](../anmelden-in-der-administration.md) | Setup > Systeminformationen, Upgrade-Prüfung, Sitzungen und Cronjobs finden |
| [Sicherheitshärtung](../sicherheitshaertung.md) | die Sicherheit der ganzen Website |
| [Eine Website betreiben](../../anleitungen/betrieb-einer-website.md), [Aktualisieren](../../anleitungen/aktualisieren.md), [Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md), [Systeminformationen](../../anleitungen/systeminformationen.md), [Sessions](../../anleitungen/sitzungen.md), [Cronjobs](../../anleitungen/cronjobs.md), [Caches](../../anleitungen/caches.md) | die Anleitungen für Administratoren, die Ihre Arbeit berührt |
| [Hosting-Unternehmen: hier beginnen](../hosting/hosting-anbieter-hier-beginnen.md) | die Hosting-Seite: Server, Panels, Sicherungen, Betriebsanleitungen |
| [Datenbanken in der offiziellen Reihenfolge](../hosting/grundlagen/datenbanken-in-der-offiziellen-reihenfolge.md), [Datenbanksicherungen je Engine](../hosting/betrieb/datenbank-backups-je-engine.md), [Die Datenbank-Engine wechseln](../hosting/migrationen/die-datenbank-engine-einer-kunden-website-wechseln.md), [Clustering und geteilter Speicher](../hosting/betrieb/clustering-und-gemeinsamer-speicher.md) | dieselben Themen aus Sicht eines Hosters |
| [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md) | wo Sie Hilfe erhalten |
| [Freie Dokumentation](../freie-dokumentation.md) | was Sie mit diesen Seiten tun dürfen |

## Diese Seiten wiederfinden

- Filtern Sie in der Hilfe nach der Zielgruppe **Datenbankadministratoren** oder der Kategorie **Datenbankadministration**,
  oder öffnen Sie **Alle Seiten** und den Ordner `datenbankadministratoren`.
- Jede Seite hat **Auf GitHub bearbeiten**. Ist eine Angabe hier falsch oder veraltet, sagen Sie es uns dort oder im
  [Software-Tracker](https://github.com/se7enxweb/exponential/issues).

## Verwandte Seiten

- [Die Datenbankschicht auf einer Seite](die-datenbankschicht-auf-einer-seite.md)
- [Ein Lesepfad für Datenbankadministratoren](lesepfad-fuer-datenbankadministratoren.md)
- [Häufige Fragen zur Datenbank](haeufige-fragen-zur-datenbank.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
