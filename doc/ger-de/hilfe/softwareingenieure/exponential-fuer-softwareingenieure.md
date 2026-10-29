---
title: Exponential für Softwareingenieure
audience: software-engineer, developer
category: software-engineering
order: 10
summary: Das ganze System auf einer Seite für Ingenieure jeder Sprachherkunft - Laufzeit, Stack, Prozessmodell, Datenspeicher, Konfiguration, Erweiterungsmodell, Qualitäts-Gates und Release-Regeln, jeweils mit der Datei oder dem Dokument, das maßgeblich ist.
keywords: Überblick, Stack, PHP, Composer, Laufzeit, Prozessmodell, Kernel, Erweiterungen, Datenbanken, Template-Engine, Konfiguration, INI, Qualität, Release, Softwareingenieur, overview, runtime, process model, extensions, databases, template engine, configuration, quality, software engineer
type: help
---
# Exponential für Softwareingenieure

Diese Seite richtet sich an Ingenieure, die das ganze System in einer Ansicht brauchen, bevor sie die Details lesen. Sie
ist für Menschen jeder Sprachherkunft geschrieben: Wo Exponential etwas anders macht als die Frameworks, die Sie
vielleicht kennen, sagt die Seite das. Jede Zeile nennt die Datei oder das Dokument, das maßgeblich ist.

## Inhalt

1. [Der Stack](#der-stack)
2. [Was für ein System es ist](#was-für-ein-system-es-ist)
3. [Wo es sich von anderen Frameworks unterscheidet](#wo-es-sich-von-anderen-frameworks-unterscheidet)
4. [Einschränkungen, die Sie übernehmen](#einschränkungen-die-sie-übernehmen)
5. [Wo die Wahrheit liegt](#wo-die-wahrheit-liegt)

## Der Stack

| Thema | Was Exponential verwendet | Quelle |
|---|---|---|
| Sprache | PHP 8.0 bis 8.5 (PHP 7.4 ohne Support nutzbar) (`"php": "^7.4 \|\| ... \|\| ^8.8"`) | `composer.json` |
| Pakete | Composer; das Hauptpaket ist `se7enxweb/exponential`, Typ `ezpublish-legacy` | `composer.json` |
| Lizenz | GNU GPL, Version 2 oder später | `LICENSE`, `composer.json` |
| Datenbanken | SQLite 3 (Standard), PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | [Datenbankunterstützung](../datenbankunterstuetzung.md) |
| Konfiguration | INI-Dateien, über `eZINI` gelesen und in fester Reihenfolge zusammengeführt | [Siteaccesses und Konfiguration](siteaccesses-und-konfiguration.md) |
| Templates | eine eigene Template-Sprache (`.tpl`), von `eZTemplate` zu PHP kompiliert | [Die Template-Engine und die Design-Kette](template-engine-und-design-kette.md) |
| Server | Apache oder nginx mit PHP-FPM, Exponential Velocity (Engine `qbix`), FrankenPHP | [Anwendungsserver](anwendungsserver.md) |
| Tests | PHPUnit (`phpunit.xml`), die meisten Suiten ohne Datenbank | [Teststrategie](teststrategie.md) |
| Statische Prüfungen | PHPStan Stufe 1 mit einer Baseline, PHP_CodeSniffer mit eigenen Sniffs des Projekts | [Statische Analyse und Codestil](statische-analyse-und-codestil.md) |
| CI | GitHub Actions: Quality, PHPUnit, Performance, CI health | [CI-Gates](ci-gates.md) |

## Was für ein System es ist

- **Ein Inhalts-Repository mit einem Anwendungs-Framework drumherum.** Im Zentrum steht ein Inhaltsmodell, das
  Administratoren zur Laufzeit festlegen (Inhaltsklassen mit typisierten Attributen), versionierte Objekte und ein Baum
  von Platzierungen. Der meiste Code, den Sie schreiben, liest oder ändert dieses Modell
  ([Das Inhaltsmodell für Ingenieure](inhaltsmodell-fuer-ingenieure.md)).
- **Ein modularer Monolith.** Eine Installation ist eine PHP-Anwendung: Kernel, Bibliotheken und Erweiterungen laufen im
  selben Prozess und teilen eine Datenbank. Erweiterungen sind Verzeichnisse, keine Dienste.
- **Konfigurationsgesteuert.** Module, Datentypen, Workflow-Events, Template-Operatoren, Cronjob-Teile, Caches und
  Handler werden alle über INI-Einstellungen gefunden; die meisten Erweiterungspunkte bestehen also darin, "eine Zeile
  in eine Einstellungsdatei und eine Klasse in Ihre Erweiterung zu schreiben"
  ([Events, Erweiterungspunkte und Plugins](events-erweiterungspunkte-und-plugins.md)).
- **Zuerst serverseitig gerendert, API-fähig.** Seiten werden von Templates gerendert; JSON-Endpunkte (ezjscore), eine
  REST-API (`ezprestapi`) und entfernte Dienste gibt es für Headless und Apps
  ([API-Design](api-design-rest-ezjscore-headless.md)).

## Wo es sich von anderen Frameworks unterscheidet

| Sie erwarten vielleicht | In Exponential |
|---|---|
| Überall PSR-4-Namensräume | Der Kernel behält seine Klassennamen (`eZContentObject`, `eZINI`) und eine erzeugte Klassenzuordnung; neuerer Code verwendet das Präfix `exp` und für die ausführbaren Klassen Namensräume wie `Exponential\Runnable` |
| Eine Routendatei | Modul- und View-Definitionen in `module.php`, URL-Aliase in der Datenbank, Siteaccess-Zuordnung aus `site.ini` |
| Ein ORM mit Migrationen | `eZPersistentObject` (eine Klasse je Tabelle, ein Array `definition()`) und SQL-Update-Dateien je Treiber, von Hand angewendet |
| Umgebungsvariablen für die Konfiguration | INI-Dateien in einer Kaskade; umgebungsspezifische Einstellungen werden unterstützt ([Umgebungseinstellungen](../../funktionen/6.0/umgebungseinstellungen.md)) |
| Twig oder Blade | die Template-Sprache von Exponential mit Operatoren, Funktionen und Fetch-Funktionen; Symfony und Twig nur über die Legacy Bridge |
| Typdeklarationen | Der Kernel-Stil verbietet skalare und Rückgabe-Typdeklarationen von PHP 7+, weil der Code auf PHP 8.0 laufen und seine Signaturen behalten muss ([Codestil](../../funktionen/6.0/qualitaetspruefungen.md#code-stil)) |

## Einschränkungen, die Sie übernehmen

- **Das älteste unterstützte PHP ist 8.0.** Die CI prüft jede PHP-Datei mit PHP 8.0 und startet den Kernel darauf; keine
  Syntax und keine Funktion neuer als 8.0 ohne Absicherung ([CI-Gates](ci-gates.md)).
- **Bezeichner werden nicht umbenannt.** Vorhandene Klassen, Tabellen, Einstellungen und Pfade `eZ*` und `ezp*`
  bleiben; neuer Code verwendet `exp` ([Regeln zu Schichtung und Benennung](../../standards/architektur/schichten-und-benennung.md)).
- **Langlebige Worker.** Code muss in einem Prozess sicher sein, der viele Anfragen bedient
  ([Nebenläufigkeit, Statics und langlebige Prozesse](nebenlaeufigkeit-statics-und-langlebige-prozesse.md)).
- **Sechs Datenbanken.** Kernel-Code darf sich ohne Rückfall nicht auf die Funktionen eines einzelnen Treibers verlassen
  ([Portables SQL schreiben](portables-sql-schreiben.md)).
- **Dauerhafte Releases.** Tags und Releases sind unveränderlich
  ([Versionsnummern und unveränderliche Tags](versionsnummern-und-unveraenderliche-tags.md)).

## Wo die Wahrheit liegt

| Frage | Dokument |
|---|---|
| Wie ist der Kernel gebaut? | [Kernel-Architektur](../../standards/architektur/kernel-architektur.md) |
| Welche Schicht darf welche aufrufen? | [Regeln zu Schichtung und Benennung](../../standards/architektur/schichten-und-benennung.md) |
| Was ist ein Bruch, wie wird veröffentlicht und bereitgestellt? | [Kompatibilität, Releases und Bereitstellung](../../standards/architektur/kompatibilitaet-releases-deployment.md) |
| Wie werden Commits und Releases gemacht? | [Versionsverwaltung und Releases](../../standards/versionsverwaltung-und-releases.md) |
| Welche Muster verwendet der Code? | [Katalog der Entwurfsmuster](../../standards/muster/README.md) |
| Was hat sich in einem Release geändert? | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md) und die Upgrade-Hinweise in `doc/eng-us/bc/6.0/` |

## Verwandte Seiten

- [Softwareingenieure: hier beginnen](softwareingenieure-hier-beginnen.md)
- [Architekturüberblick](architekturueberblick.md)
- [Exponential bewerten](exponential-bewerten.md)
- [PHP-Entwickler: hier beginnen](../../../eng-us/help/php-developers/php-developers-start-here.md), [Erweiterungsentwicklung](../erweiterungsentwicklung/README.md) und [Anwendungsentwickler: hier beginnen](../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
