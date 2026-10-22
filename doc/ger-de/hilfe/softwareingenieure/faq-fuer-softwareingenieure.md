---
title: Häufige Fragen von Softwareingenieuren
audience: software-engineer, developer
category: software-engineering
order: 980
summary: Kurze Antworten auf die Fragen, die Softwareingenieure zuerst zu Exponential stellen - Sprache und Versionen, Frameworks und PSRs, ORM und Migrationen, Datenbanken, Server, Statics, Caches, Tests, Releases, Sicherheitsmeldungen, Lizenz und Beitragen -, jeweils mit der Seite, die die vollständige Antwort enthält.
keywords: FAQ, häufige Fragen, Fragen, Antworten, PHP-Version, Symfony, PSR, ORM, Migrationen, Datenbank, Velocity, FrankenPHP, Statics, Caching, Tests, CI, Versionierung, Tags, Sicherheit, GPL, CLA, Softwareingenieur, questions, answers, migrations, database, versioning, security
type: help
---
# Häufige Fragen von Softwareingenieuren

Kurze Antworten, jeweils mit der Seite, die die Details enthält.

## Inhalt

1. [Plattform](#plattform)
2. [Daten](#daten)
3. [Laufzeit](#laufzeit)
4. [Qualität und Releases](#qualität-und-releases)
5. [Menschen und Lizenzen](#menschen-und-lizenzen)

## Plattform

**Welche PHP-Versionen?** PHP 8.0 bis 8.5. Die CI prüft jede Datei mit PHP 8.0 und startet den Kernel damit; PHPUnit
läuft auf 8.1 bis 8.5 ([CI-Gates](ci-gates.md)).

**Baut es auf Symfony oder Laravel auf?** Nein. Der Kernel ist ein eigenes Framework. Die Symfony-Plattformprodukte binden
ihn über die Legacy Bridge ein ([Symfony und die Legacy Bridge](../ersteller/anwendungsentwickler/symfony-und-die-legacy-bridge.md)).

**Folgt es PSR-4 und PSR-12?** Nein, bewusst nicht: erzeugte Klassenzuordnungen statt PSR-4 und ein eigener Codestil,
geprüft von `phpcs.xml.dist` ([Abhängigkeitsverwaltung mit Composer](abhaengigkeitsverwaltung-mit-composer.md#psr-stand)).

**Warum keine Typdeklarationen im Kernel-Code?** Der Code muss auf PHP 8.0 laufen und seine Signaturen behalten, die
Erweiterungen überschreiben ([Statische Analyse und Codestil](statische-analyse-und-codestil.md#warum-keine-typdeklarationen)).

**Wohin gehört mein Code?** In eine Erweiterung, fast immer ([Kernel, lib und Erweiterungen: die Schichtung](kernel-lib-und-erweiterungen.md)).

## Daten

**Gibt es ein ORM?** Einen Active-Record-Mapper, `eZPersistentObject`, eine Klasse je Tabelle; keine Unit of Work, keine
verzögert geladenen Beziehungen ([Persistenz und die Datenbankschicht](persistenz-und-die-datenbankschicht.md)).

**Gibt es Migrationen?** Schemadateien (`.dba`) und SQL-Update-Dateien je Treiber, von Hand angewendet; kein Läufer hält
fest, was ausgeführt wurde ([Schemaänderungen und Migrationen](schemaaenderungen-und-migrationen.md)).

**Welche Datenbank sollten wir verwenden?** SQLite 3 für eine Maschine, PostgreSQL für mehrere Knoten oder den am besten
getesteten Server; die offizielle Reihenfolge ist SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL
([Datenbanken: eine wählen und betreiben](datenbanken-und-die-wahl-einer-datenbank.md)).

**Werden Prepared Statements verwendet?** Anweisungen werden als Zeichenketten gebaut; jede Zeichenkette wird mit
`escapeString()` maskiert und jede Zahl umgewandelt ([Portables SQL schreiben](portables-sql-schreiben.md)).

## Laufzeit

**Welcher Server sollte es ausliefern?** Das Projekt empfiehlt Velocity; FrankenPHP ist die produktionsreife Alternative,
und PHP-FPM hinter einem beliebigen Webserver funktioniert weiterhin ([Anwendungsserver](anwendungsserver.md)).

**Sind Statics unter Velocity sicher?** Statische Eigenschaften werden nach jeder Anfrage wiederhergestellt, aber
Warm-up-Zustand, bewusst behaltene Globals, Ressourcen und angesammelte Registrierungen sind Ihre Sache
([Nebenläufigkeit, Statics und langlebige Prozesse](nebenlaeufigkeit-statics-und-langlebige-prozesse.md)).

**Warum ist meine Änderung nicht sichtbar?** Meist die Reihenfolge: Der Code muss geladen sein (PHP-FPM, Neustart von
Velocity), bevor die Seiten-Caches geleert werden ([Eine Änderung bereitstellen](eine-aenderung-bereitstellen.md)).

**Gibt es einen Health-Endpunkt?** Keinen einzelnen; prüfen Sie die Startseite und die Anmeldung der Administration von
außen und die Statusbefehle auf dem Server ([Beobachtbarkeit](beobachtbarkeit-logs-debug-und-audit.md#zustand-und-status)).

**Kann es auf mehreren Webknoten laufen?** Ja, mit einem Datenbankserver, dem DFS-Cluster-Datei-Handler für Dateien und
einem geteilten Session-Handler ([Skalierung und Clustering](skalierung-und-clustering.md)).

## Qualität und Releases

**Wie führe ich die Tests aus?** `php vendor/bin/phpunit` führt jede Suite ohne Datenbank aus
([Tests ohne Datenbank und Testsuiten](tests-ohne-datenbank-und-testsuiten.md)).

**Warum ist die CI bei meinem Pull-Request rot, obwohl mein Branch grün war?** Sie testet den Merge mit `main`; führen Sie
`main` in Ihren Branch zusammen ([CI-Gates](ci-gates.md#eigenschaften-die-jeder-ingenieur-kennen-sollte)).

**Was kommt nach 0.0.4.9?** `0.0.4.10` ([Versionsnummern und unveränderliche Tags](versionsnummern-und-unveraenderliche-tags.md)).

**Lässt sich ein fehlerhafter Tag reparieren?** Nein. Veröffentlichen Sie die nächste Version und sagen Sie in ihren
Hinweisen, was falsch war.

**Wo finde ich, was sich geändert hat?** `doc/changelogs/` und die Upgrade-Hinweise in `doc/eng-us/bc/6.0/`
([Changelogs und Upgrade-Hinweise](changelogs-und-bc-hinweise.md)).

## Menschen und Lizenzen

**Wer pflegt es?** 7x führt die Entwicklung; die Exponential Foundation betreibt und finanziert das Projekt
([Wer ist 7x?](../wer-ist-7x.md), [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md)).

**Welche Lizenz?** Die GNU GPL, Version 2 oder später, für den Code; die GNU FDL 1.3 für die Dokumentation.

**Was verlangt ein Beitrag zum Kernel?** Eine unterschriebene CLA, die das Urheberrecht am Beitrag an 7x überträgt; Ihre
eigenen Erweiterungen sind nicht betroffen ([Beiträge an das Projekt](upstream-beitragen.md)).

**Wie melde ich eine Schwachstelle?** Vertraulich, an die Adresse in `SECURITY.md`
([Sicherheitstechnik im Überblick](sicherheitstechnik-im-ueberblick.md#schwachstellen-melden-und-beheben)).

## Verwandte Seiten

- [Glossar für Softwareingenieure](glossar-fuer-softwareingenieure.md)
- [Häufige Fragen von Anwendungsentwicklern](../ersteller/anwendungsentwickler/faq-fuer-anwendungsentwickler.md)
- [Softwareingenieure: hier beginnen](softwareingenieure-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
