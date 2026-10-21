---
title: "Bootcamp 3.3: Das dreitägige Entwickler-Bootcamp"
audience: trainer, developer
category: training
order: 33
summary: Drei Tage für PHP-Entwickler, Tag 1 Git-Installation, Inhaltsmodell und Klassen, Tag 2 die Template-Sprache, Overrides und Debugging, Tag 3 Erweiterungen, die Konsole, INI-Einstellungen, Velocity und ein Pull-Request.
keywords: drei tage, bootcamp, entwickler, git, inhaltsklassen, templates, overrides, erweiterungen, konsole, ini, velocity, pull-request, three days, developers, content classes, extensions, console, pull request
type: help
---
# Bootcamp 3.3: Das dreitägige Entwickler-Bootcamp

Drei Tage reichen, um eine kleine Website von Anfang bis Ende zu bauen: Inhaltsklassen, Templates, Overrides und eine
Erweiterung, auf einer Installation aus Git-Klonen. Am Ende des Bootcamps kann jeder Entwickler ein echtes Projekt
beginnen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Workshops und Bootcamps](../README.md) |
| Modul | [Modul 3: Dreitägige Bootcamps](README.md) |
| Dauer | 3 Tage mit je 6 Stunden Einheiten |
| Niveau | Fortgeschrittene |
| Zielgruppe | PHP-Entwickler, 4 bis 12 Teilnehmende |
| Voraussetzungen | PHP, Git und Composer auf jedem Laptop; ein GitHub-Konto |

## Lernziele

Am Ende des Bootcamps können die Teilnehmenden:

- in einer Installation aus Git-Klonen auf einem eigenen Branch arbeiten und sie aktuell halten;
- Inhaltsklassen für eine Website entwerfen;
- Templates in der Template-Sprache schreiben und sie pro Klasse oder Knoten per Override ersetzen;
- eine Erweiterung mit eigenen Einstellungen bauen, aktivieren und debuggen;
- eine PHP-Änderung unter Velocity ausrollen und eine Änderung als Pull-Request an das Projekt schicken.

## Der Ablauf

| Tag | Vormittag (3 Std.) | Nachmittag (3 Std.) |
|---|---|---|
| 1 | Git-Installation ([Mit Git entwickeln](../../../mit-git-entwickeln.md)); Kernkonzepte ([Die Kernkonzepte unterrichten](../../die-kernkonzepte-unterrichten.md)) | Inhaltsklassen und Inhalte ([Das Inhaltsmodell und das Bearbeiten von Inhalten](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md), [Klassengruppen](../../../../anleitungen/klassengruppen.md)) |
| 2 | Die Template-Sprache ([Templates und Design, 6](../../../../anleitungen/templates-und-design.md#6-die-template-sprache-in-zehn-minuten)) | Overrides und Debugging ([Templates und Design, 4 und 7](../../../../anleitungen/templates-und-design.md#4-eine-änderung-mit-einem-override-vornehmen)) |
| 3 | Erweiterungen und die RAD-Werkzeuge ([Erweiterungen](../../../../anleitungen/extensions.md), [RAD-Werkzeuge](../../../../funktionen/6.0/rad-werkzeuge.md)); INI ([exp:ini](../../../../funktionen/6.0/exp-ini-befehl.md)) | Die Konsole; Velocity und Ausrollen ([Die Website ausliefern, 8.3.14](../../../../installation/08-die-website-ausliefern.md#8314-eine-änderung-bereitstellen-expvelocity-deploy)); ein Pull-Request |

Pausen: jeden Tag 15 Minuten am Vormittag und am Nachmittag, 45 Minuten Mittagspause.

## Hinweise für Trainer

- **Das Abschlussprojekt läuft über alle drei Tage**: ein Website-Bereich „Events“, der jeden Nachmittag ein Stück
  weiter ausgebaut wird.
- **Template-Syntax**: Prüfen Sie Templates mit der Syntaxprüfung aus
  [Templates und Design, 5](../../../../anleitungen/templates-und-design.md#5-die-syntax-prüfen-bevor-sie-in-den-browser-schauen),
  bevor Sie den Browser öffnen.
- **Velocity**: Eine geänderte Klasse erreicht die Worker von Velocity erst nach einem Neustart oder Deploy; zeigen
  Sie das einmal an einem sichtbaren Beispiel.

## Praktische Übung: das Abschlussprojekt des Bootcamps

1. Tag 1: eine Inhaltsklasse „Event“ (Titel, Datum, Ort, Text) und fünf Veranstaltungen in einem Ordner „Events“.
2. Tag 2: ein Override der Vollansicht für Veranstaltungen und eine Zeilenansicht, die in der Liste des Ordners
   verwendet wird.
3. Tag 3: eine Erweiterung `bikeevents` (mit einem RAD-Assistenten erstellt), die den Override und seine
   Einstellungen enthält; aktiviert, Autoloads neu erzeugt, Caches geleert; eine kleine Korrektur der Dokumentation,
   als Pull-Request an einen Fork geschickt.

## Erwartetes Ergebnis

- Der Ordner Events listet fünf Veranstaltungen mit der eigenen Zeilenansicht; jede Veranstaltung zeigt die eigene
  Vollansicht.
- Der Override liegt in der Erweiterung, nicht in `settings/siteaccess/`.
- Ein Link zum Pull-Request (an einen Fork oder an das Projekt, wenn die Änderung echt ist).

## Quiz (Ende des Bootcamps)

1. Wohin gehören die Overrides eines Projekts: in die Siteaccess-Einstellungen oder in eine Erweiterung?
2. Was führen Sie aus, nachdem Sie einer Erweiterung eine Klasse hinzugefügt haben?
3. Warum ist eine PHP-Änderung unter Velocity nicht sofort sichtbar?
4. Was bewirkt `|wash` in einem Template?

<details>
<summary>Antworten</summary>

1. In eine Erweiterung, damit sie mit dem Code mitwandern und unter Versionskontrolle stehen.
2. `php bin/php/ezpgenerateautoloads.php -e`, dann die Caches leeren.
3. Die Worker von Velocity halten die Anwendung im Speicher; starten Sie neu oder führen Sie einen Deploy aus
   ([Die Website ausliefern, 8.3.14](../../../../installation/08-die-website-ausliefern.md#8314-eine-änderung-bereitstellen-expvelocity-deploy)).
4. Es maskiert einen Wert für die sichere Ausgabe in HTML.

</details>

## Weiterführende Informationen

- [Entwicklung von Erweiterungen](../../../erweiterungsentwicklung/README.md)
- [Templates und Design](../../../../anleitungen/templates-und-design.md)
- [REST-API v1 und v2](../../../../anleitungen/rest-api-v1-und-v2.md)

## Verwandte Seiten

- [Modul 3: Dreitägige Bootcamps](README.md)
- [Workshops und Bootcamps: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
