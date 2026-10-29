---
title: "Anwendungsentwickler: hier beginnen"
audience: app-creator, developer
category: app-creation
order: 10
summary: Die Einstiegsseite für Anwendungsentwickler - was Exponential einer Anwendung bietet, ein Plan für die erste Woche und jede Seite dieses Bereichs nach Aufgaben geordnet, von der ersten Erweiterung bis zu APIs, KI, Warteschlangen, Tests, Deployment, Sicherheit und Skalierung.
keywords: Anwendungsentwickler, hier beginnen, Anwendungen bauen, Erweiterungsentwicklung, Module, Schnittstellen, Warteschlangen, Tests, Deployment, Sicherheit, Skalierung, application creator, start here, build applications, extension development, modules, APIs, REST, expai, expqueue, testing, deployment, Velocity, security, scaling
featured: true
type: help
---
# Anwendungsentwickler: hier beginnen

Diese Seite ist für Entwickler, die Anwendungen auf Exponential bauen: eigene Module und Bildschirme, Integrationen,
Headless-Backends, Werkzeuge für Abläufe, KI-gestützte Funktionen. Sie zeigt, was dieser Bereich bietet, was Sie in
Ihrer ersten Woche tun und wo jede Antwort steht. Exponential ist das quelloffene Content-Management-System und
Anwendungs-Framework, das in dieser Administration läuft; es führt die Codebasis von eZ Publish fort, einem Produkt,
das sein Hersteller nach fast 18 Jahren eingestellt hat, und wird von 7x gemeinsam mit der Exponential Foundation
geleitet ([Wer ist 7x?](../../wer-ist-7x.md), [Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md)).

## Inhalt

1. [Ihre erste Woche](#ihre-erste-woche)
2. [Grundlagen](#grundlagen)
3. [Module, Ansichten und Templates](#module-ansichten-und-templates)
4. [Daten](#daten)
5. [Abläufe und Hintergrundarbeit](#abläufe-und-hintergrundarbeit)
6. [APIs und KI](#apis-und-ki)
7. [Paketierung und Releases](#paketierung-und-releases)
8. [Tests, Deployment und Betrieb](#tests-deployment-und-betrieb)
9. [Sicherheit](#sicherheit)
10. [Skalierung](#skalierung)
11. [Weitere Dokumentation, die Sie brauchen werden](#weitere-dokumentation-die-sie-brauchen-werden)

## Ihre erste Woche

| Tag | Aufgabe | Seite |
|---|---|---|
| 1 | Lesen, was die Plattform Ihnen bietet und wie eine Anfrage verläuft | [Exponential als Anwendungsplattform](exponential-als-anwendungsplattform.md), [Architektur](architektur-fuer-anwendungsentwickler.md) |
| 2 | Eine lokale Installation einrichten | [Eine Entwicklungsumgebung einrichten](entwicklungsumgebung.md) |
| 3 | Die erste Erweiterung bauen, dann eine weitere von Setup > RAD bauen lassen | [Ihre erste Erweiterung](ihre-erste-erweiterung.md), [RAD-Werkzeuge](rad-werkzeuge.md) |
| 4 | Eine Ansicht mit einem Formular und einer Richtlinie hinzufügen | [Eigene Module und Ansichten](eigene-module-und-ansichten.md), [Formulare, CSRF-Schutz und HTTP-Eingaben](formulare-csrf-und-http-eingaben.md) |
| 5 | Inhalte in PHP lesen und schreiben; einen Test schreiben | [Die Content-API](die-content-api.md), [PHPUnit-Tests ohne Datenbank](phpunit-tests-ohne-datenbank.md) |

Der [Lesepfad](lesepfad-fuer-anwendungsentwickler.md) bringt alle folgenden Seiten in eine Reihenfolge.

## Grundlagen

- [Exponential als Anwendungsplattform](exponential-als-anwendungsplattform.md)
- [Architektur für Anwendungsentwickler](architektur-fuer-anwendungsentwickler.md)
- [Eine Entwicklungsumgebung einrichten](entwicklungsumgebung.md)
- [Ihre erste Erweiterung](ihre-erste-erweiterung.md)
- [Aufbau einer Erweiterung](aufbau-einer-erweiterung.md)
- [Einstellungen und INI-Dateien in Ihrer Anwendung](einstellungen-und-ini-dateien.md)
- [Autoloads und Klassenbenennung](autoloads-und-klassenbenennung.md)
- [RAD-Werkzeuge: schneller bauen](rad-werkzeuge.md)

## Module, Ansichten und Templates

- [Eigene Module und Ansichten](eigene-module-und-ansichten.md)
- [Referenz zu module.php: Ansichten, Parameter und Funktionen](referenz-der-moduldefinition.md)
- [Ergebnisse von Ansichten, Templates und das Pagelayout](ergebnisse-von-ansichten-und-das-pagelayout.md)
- [Richtlinienfunktionen und Einschränkungen](richtlinienfunktionen-und-einschraenkungen.md)
- [Formulare, CSRF-Schutz und HTTP-Eingaben](formulare-csrf-und-http-eingaben.md)
- [Fetch-Funktionen: Daten in Templates lesen](fetch-funktionen.md)
- [Eine eigene Fetch-Funktion schreiben](eine-eigene-fetch-funktion-schreiben.md)
- [Template-Operatoren schreiben](template-operatoren-schreiben.md)
- [Template-Funktionen, Includes und Cache-Blöcke](template-funktionen-und-includes.md)

## Daten

- [Die Content-API in PHP](die-content-api.md)
- [Inhalte im Code anlegen und aktualisieren](inhalte-im-code-anlegen.md)
- [Persistente Objekte und eigene Tabellen](persistente-objekte-und-eigene-tabellen.md)
- [Datenbankzugriff und SQL](datenbankzugriff.md)
- [Datenbankschema und .dba-Dateien](schema-und-dba-dateien.md)
- [Einen eigenen Datentyp schreiben](eigene-datentypen.md)
- [Der Lebenszyklus eines Datentyps](lebenszyklus-eines-datentyps.md)

## Abläufe und Hintergrundarbeit

- [Workflows, Trigger und Ereignisse](workflows-und-ereignisse.md)
- [Einen Workflow-Ereignistyp schreiben](einen-workflow-ereignistyp-schreiben.md)
- [Cronjobs für Anwendungen](cronjobs-fuer-anwendungen.md)
- [Ein Cronjob-Skript schreiben](einen-cronjob-teil-schreiben.md)
- [Konsolenbefehle](konsolenbefehle.md)
- [CLI-Skripte mit eZScript](cli-skripte.md)
- [Inhalts-Jobs](inhalts-jobs.md)
- [Warteschlangen mit expqueue](warteschlangen-mit-expqueue.md)
- [Hintergrundarbeit: Warteschlangen, Cronjobs und Inhalts-Jobs](muster-fuer-hintergrundarbeit.md)

## APIs und KI

- [REST-API v1 und v2 für Anwendungen](rest-api.md)
- [JSON-Endpunkte mit ezjscore](json-endpunkte-mit-ezjscore.md)
- [OAuth und API-Schlüssel](oauth-und-api-schluessel.md)
- [Entfernte Dienste mit expservices](entfernte-dienste-expservices.md)
- [Headless Exponential](headless-exponential.md)
- [Benachrichtigungen und Mail aus Anwendungen](benachrichtigungen-und-mail.md)
- [Exponential Intelligence (expai) für Anwendungen](exponential-intelligence.md)
- [Exponential Intelligence erweitern](exponential-intelligence-erweitern.md)

## Paketierung und Releases

- [Ihre Anwendung als Erweiterung paketieren](als-erweiterung-paketieren.md)
- [Composer-Pakete und Packagist](composer-und-packagist.md)
- [Versionen und Releases Ihrer Erweiterung](versionen-und-releases.md)
- [Inhaltspakete (.ezpkg)](pakete-und-ezpkg.md)

## Tests, Deployment und Betrieb

- [Anwendungen testen](anwendungen-testen.md)
- [PHPUnit-Tests ohne Datenbank](phpunit-tests-ohne-datenbank.md)
- [Continuous Integration für Erweiterungen](ci-fuer-erweiterungen.md)
- [Fehlersuche und Logs](fehlersuche-und-logs.md)
- [Mit Velocity ausliefern](mit-velocity-ausliefern.md)
- [Eine Deployment-Pipeline](deployment-pipeline.md)
- [Umgebungseinstellungen und Geheimnisse](umgebungseinstellungen-und-geheimnisse.md)

## Sicherheit

- [Sicherheit für Anwendungen](sicherheit-fuer-anwendungen.md)
- [Content Security Policy für Ihren Code](content-security-policy.md)
- [Eingabeprüfung und Escaping der Ausgabe](eingabepruefung-und-escaping.md)
- [Zugriffskontrolle im Code](zugriffskontrolle-im-code.md)

## Skalierung

- [Anwendungen skalieren](anwendungen-skalieren.md)
- [Caching in Anwendungen](caching-in-anwendungen.md)
- [Clustering und gemeinsamer Speicher](clustering-und-gemeinsamer-speicher.md)
- [Profiling und Leistung](profiling-und-leistung.md)
- [Symfony und die Legacy Bridge](symfony-und-die-legacy-bridge.md)
- [FAQ für Anwendungsentwickler](faq-fuer-anwendungsentwickler.md)

## Weitere Dokumentation, die Sie brauchen werden

| Für | Lesen Sie |
|---|---|
| Regeln und Muster der Codebasis | [Standards](../../../standards/README.md), [Muster](../../../standards/muster/README.md) |
| Jeden Erweiterungspunkt und wie er registriert wird | [Erweiterungspunkte](../../../bc/6.0/rad-erweiterungspunkte.md) |
| Bau von Websites (Templates, Layouts, SEO) | [Website-Ersteller: hier beginnen](../website-ersteller/website-ersteller-hier-beginnen.md) |
| Arbeit mit Git und Beiträge zum Projekt | [Mit Git entwickeln](../../mit-git-entwickeln.md) |
| Installation und Auslieferung | [Installationshandbuch](../../../installation/README.md) |
| Das Buch für Erweiterungsentwickler | `doc/eng-us/help/extension-development/`, sofern es in Ihrer Installation vorhanden ist |

> **Tipp**
> Die Suche der Hilfe findet jede Seite dieses Bereichs: Filtern Sie nach der Zielgruppe **Anwendungsentwickler**.

## Verwandte Seiten

- [Lesepfad für Anwendungsentwickler](lesepfad-fuer-anwendungsentwickler.md)
- [Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-see-also-645 -->
**Siehe auch:** [PHP-Entwickler](../../php-entwickler/php-entwickler-hier-beginnen.md), [Mobile-App-Entwickler](../../mobile-app-entwickler/mobile-app-entwickler-hier-beginnen.md).

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
