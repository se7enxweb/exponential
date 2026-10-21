---
title: Lesepfad für Anwendungsentwickler
audience: app-creator, developer
category: app-creation
order: 20
summary: Die Seiten des Bereichs für Anwendungsentwickler in der Reihenfolge, in der eine Anwendung entsteht - sechs Stufen von der ersten Erweiterung bis zum skalierten Produktivsystem - jeweils mit dem, was Sie nach jeder Stufe können sollten.
keywords: Lesepfad, Lernpfad, Reihenfolge, Stufen, Anwendungsentwickler, Kurs, Lehrplan, Erweiterungsentwicklung, Schritt für Schritt, reading path, learning path, order, stages, application creator, course, curriculum, extension development, step by step
featured: true
type: help
---
# Lesepfad für Anwendungsentwickler

Diese Seite ist für Anwendungsentwickler, die der Reihe nach lernen möchten. Sechs Stufen führen Sie von einer ersten
Erweiterung bis zu einer Anwendung im Produktivbetrieb. Jede Stufe endet mit dem, was Sie dann können sollten.

## Stufe 1: Orientierung

1. [Exponential als Anwendungsplattform](exponential-als-anwendungsplattform.md)
2. [Architektur für Anwendungsentwickler](architektur-fuer-anwendungsentwickler.md)
3. [Eine Entwicklungsumgebung einrichten](entwicklungsumgebung.md)
4. [Mit Git entwickeln](../../mit-git-entwickeln.md)

Sie können: eine lokale Installation betreiben und erklären, wie eine Anfrage zu einer Ansicht gelangt.

## Stufe 2: die erste Erweiterung

1. [Ihre erste Erweiterung](ihre-erste-erweiterung.md)
2. [Aufbau einer Erweiterung](aufbau-einer-erweiterung.md)
3. [Einstellungen und INI-Dateien in Ihrer Anwendung](einstellungen-und-ini-dateien.md)
4. [Autoloads und Klassenbenennung](autoloads-und-klassenbenennung.md)
5. [RAD-Werkzeuge: schneller bauen](rad-werkzeuge.md)

Sie können: eine Erweiterung mit einem Modul, einem Template und Einstellungen anlegen, aktivieren und ändern.

## Stufe 3: Bildschirme und Templates

1. [Eigene Module und Ansichten](eigene-module-und-ansichten.md) und [Referenz zu module.php](referenz-der-moduldefinition.md)
2. [Ergebnisse von Ansichten, Templates und das Pagelayout](ergebnisse-von-ansichten-und-das-pagelayout.md)
3. [Richtlinienfunktionen und Einschränkungen](richtlinienfunktionen-und-einschraenkungen.md)
4. [Formulare, CSRF-Schutz und HTTP-Eingaben](formulare-csrf-und-http-eingaben.md)
5. [Fetch-Funktionen](fetch-funktionen.md), [Eine eigene Fetch-Funktion schreiben](eine-eigene-fetch-funktion-schreiben.md)
6. [Template-Operatoren schreiben](template-operatoren-schreiben.md), [Template-Funktionen, Includes und Cache-Blöcke](template-funktionen-und-includes.md)

Sie können: Bildschirme mit Formularen und Rechten bauen und Templates Ihre Daten zur Verfügung stellen.

## Stufe 4: Daten und Abläufe

1. [Die Content-API in PHP](die-content-api.md), [Inhalte im Code anlegen und aktualisieren](inhalte-im-code-anlegen.md)
2. [Persistente Objekte und eigene Tabellen](persistente-objekte-und-eigene-tabellen.md), [Datenbankzugriff und SQL](datenbankzugriff.md), [Datenbankschema und .dba-Dateien](schema-und-dba-dateien.md)
3. [Einen eigenen Datentyp schreiben](eigene-datentypen.md), [Der Lebenszyklus eines Datentyps](lebenszyklus-eines-datentyps.md)
4. [Workflows, Trigger und Ereignisse](workflows-und-ereignisse.md), [Einen Workflow-Ereignistyp schreiben](einen-workflow-ereignistyp-schreiben.md)
5. [Cronjobs für Anwendungen](cronjobs-fuer-anwendungen.md), [Ein Cronjob-Skript schreiben](einen-cronjob-teil-schreiben.md), [Konsolenbefehle](konsolenbefehle.md), [CLI-Skripte](cli-skripte.md)
6. [Warteschlangen mit expqueue](warteschlangen-mit-expqueue.md), [Inhalts-Jobs](inhalts-jobs.md), [Hintergrundarbeit](muster-fuer-hintergrundarbeit.md)

Sie können: Daten sicher speichern und ändern, sich in das Veröffentlichen einklinken und Arbeit im Hintergrund
ausführen.

## Stufe 5: Integration

1. [REST-API v1 und v2](rest-api.md), [OAuth und API-Schlüssel](oauth-und-api-schluessel.md)
2. [JSON-Endpunkte mit ezjscore](json-endpunkte-mit-ezjscore.md), [Entfernte Dienste mit expservices](entfernte-dienste-expservices.md)
3. [Headless Exponential](headless-exponential.md)
4. [Benachrichtigungen und Mail aus Anwendungen](benachrichtigungen-und-mail.md)
5. [Exponential Intelligence für Anwendungen](exponential-intelligence.md), [Exponential Intelligence erweitern](exponential-intelligence-erweitern.md)

Sie können: andere Programme und KI-Unterstützung an Ihre Anwendung anbinden.

## Stufe 6: Qualität, Release und Produktivbetrieb

1. [Sicherheit für Anwendungen](sicherheit-fuer-anwendungen.md), [Content Security Policy für Ihren Code](content-security-policy.md), [Eingabeprüfung und Escaping der Ausgabe](eingabepruefung-und-escaping.md), [Zugriffskontrolle im Code](zugriffskontrolle-im-code.md)
2. [Anwendungen testen](anwendungen-testen.md), [PHPUnit-Tests ohne Datenbank](phpunit-tests-ohne-datenbank.md), [Continuous Integration für Erweiterungen](ci-fuer-erweiterungen.md), [Fehlersuche und Logs](fehlersuche-und-logs.md)
3. [Ihre Anwendung als Erweiterung paketieren](als-erweiterung-paketieren.md), [Composer-Pakete und Packagist](composer-und-packagist.md), [Versionen und Releases](versionen-und-releases.md), [Inhaltspakete](pakete-und-ezpkg.md)
4. [Mit Velocity ausliefern](mit-velocity-ausliefern.md), [Eine Deployment-Pipeline](deployment-pipeline.md), [Umgebungseinstellungen und Geheimnisse](umgebungseinstellungen-und-geheimnisse.md)
5. [Anwendungen skalieren](anwendungen-skalieren.md), [Caching in Anwendungen](caching-in-anwendungen.md), [Clustering und gemeinsamer Speicher](clustering-und-gemeinsamer-speicher.md), [Profiling und Leistung](profiling-und-leistung.md)
6. [Symfony und die Legacy Bridge](symfony-und-die-legacy-bridge.md), wenn Ihr Projekt beide Stacks umfasst

Sie können: eine getestete, sichere, versionierte Erweiterung ausliefern und im Produktivbetrieb betreiben.

## Weiterführendes

Die [Standards](../../../standards/README.md) beschreiben die Regeln und Muster der Codebasis; der Bereich für
Website-Ersteller behandelt den Bau von Websites; Beiträge zum Projekt beginnen mit [Mit Git entwickeln](../../mit-git-entwickeln.md)
und [Mit der Community arbeiten](../../fuehrung/mit-der-community-arbeiten.md).

## Verwandte Seiten

- [Anwendungsentwickler: hier beginnen](anwendungsentwickler-hier-beginnen.md)
- [FAQ für Anwendungsentwickler](faq-fuer-anwendungsentwickler.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
