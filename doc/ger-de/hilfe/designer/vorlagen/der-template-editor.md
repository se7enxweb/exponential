---
title: Der Template-Editor
audience: designer, admin
category: templates
order: 760
summary: Mit Templates und Overrides in der Administration arbeiten - Design > Template-Editor (visual/templatelist), einen Override mit Bedingungen anlegen, die Liste der Reihenfolge und Drag-and-drop, eine Template-Datei im Browser bearbeiten, die nötige Berechtigung und wann Dateien in einer Theme-Erweiterung vorzuziehen sind.
keywords: Template-Editor, visual/templatelist, Reiter Design, Override anlegen, Template im Browser bearbeiten, Override-Priorität, Overrides per Drag-and-drop, Modul visual, templateview, template editor, design tab, create override, edit template in browser, override priority, drag and drop overrides, visual module
type: help
---
# Der Template-Editor

Diese Seite ist für Designer, die Overrides in der Administration statt in INI-Dateien verwalten. Die vollständige Beschreibung
ist die Funktionsseite [Template-Editor: Overrides anlegen, ordnen und bearbeiten](../../../funktionen/6.0/template-editor-ueberschreibungen.md).

## Inhalt

1. [Wo er ist](#wo-er-ist)
2. [Was er tut](#was-er-tut)
3. [Editor oder Dateien?](#editor-oder-dateien)

## Wo er ist

Reiter **Design**, linkes Menü **Template-Editor** (`/visual/templatelist`, `settings/menu.ini [Leftmenu_design]`
`Links[template_editor]`). Ein Benutzer braucht die Richtlinie `visual/templatelist`.

## Was er tut

- Listet die Templates eines Siteaccess und pro Template seine Overrides in der Auswertungsreihenfolge.
- Legt einen Override an: das Template wählen, die Bedingungen (Klasse, Knoten, Sektion ...), den neuen Dateinamen; ein neuer
  Override wird mit `Priority=0` geschrieben, sodass er sofort wirkt.
- Ordnet Overrides per Drag-and-drop um und speichert `Priority`-Werte (10, 20, 30 ...) für die Regeln dieses Templates.
- Bearbeitet Template-Dateien im Browser.

## Editor oder Dateien?

| | Template-Editor | Dateien in einer Theme-Erweiterung |
|---|---|---|
| Schnelle Korrektur auf einer Website | gut | langsamer |
| Versionsverwaltung, Review, Ausrollen auf mehrere Websites | nein | ja |
| Übersteht eine Neuinstallation oder einen Umzug auf einen anderen Server | nur mit den Einstellungen der Website | ja, mit der Erweiterung |

Für ein Theme, das Sie pflegen, halten Sie Overrides und Templates in den Dateien Ihrer Erweiterung ([Override-Grundlagen](override-grundlagen.md))
und verwenden Sie den Editor, um die Reihenfolge anzusehen und auf einem Test-Siteaccess zu experimentieren.

## Verwandte Seiten

- [Override-Reihenfolge](override-reihenfolge.md)
- [Override-Grundlagen](override-grundlagen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
