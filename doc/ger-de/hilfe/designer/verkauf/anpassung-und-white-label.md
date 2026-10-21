---
title: "Anpassung und White-Label-Arbeit"
audience: designer, sales-marketing
category: theme-sales
order: 160
summary: Ein bestehendes Theme an die Marke eines Kunden anpassen oder Themes bauen, die Agenturen unter eigenem Namen weiterverkaufen - wie Anpassungen upgradefähig bleiben, was entfernt werden darf und was nicht, und wie Nennungen ehrlich bleiben.
keywords: Anpassung, White Label, Umbenennung, Agentur, Kundenmarke, Child-Theme, Override, upgradefähig, Nennungen, customization, white label, rebrand, agency, client branding, child theme, override, upgradeable, credits
type: help
---
# Anpassung und White-Label-Arbeit

Diese Seite ist für Designer, die Themes für Kunden anpassen oder für Agenturen, die sie weiterverkaufen.

## Anpassungen upgradefähig halten

[Die Design-Kaskade](../theme-design/die-design-kaskade.md) lässt eine kleine Erweiterung ein Basis-Theme überschreiben,
ohne es zu bearbeiten (Die Design-Kaskade):

1. Lassen Sie das Basis-Theme unberührt.
2. Legen Sie eine Kunden-Erweiterung an, die nur die geänderten Templates, Stylesheets und Einstellungen enthält.
3. Deklarieren Sie in ihrer `extension.xml`, dass sie das Basis-Theme erweitert, damit sie zuerst lädt
   ([Abhängigkeiten deklarieren](../erweiterungen/abhaengigkeiten-in-extension-xml.md)).
4. Aktualisieren Sie das Basis-Theme mit Composer; die Kunden-Erweiterung behält ihre Änderungen.

## White Label: was geändert werden darf

| Darf geändert werden | Muss bleiben |
|---|---|
| Der sichtbare Name des Themes auf der Website des Kunden | Copyright- und Lizenzhinweise in den Dateien und in `LICENSE` |
| Farben, Logos, Typografie | Die Lizenz des GPL-Codes (der Wiederverkäufer gibt dieselben Rechte weiter) |
| Der Produktname im Angebot des Wiederverkäufers | Hinweise Dritter zu Schriften, Icons, Bibliotheken |

Die Hinweise des Autors zu entfernen, ist kein „White-Labeling“; es verletzt die Lizenz
([Danksagungen und Namensnennung](../lebenszyklus/danksagungen-und-namensnennung.md)).

## Schritt für Schritt bei einem Geschäft mit einer Agentur

1. Vereinbaren Sie schriftlich, wer den Endkunden unterstützt und wie Fehler zu Ihnen gelangen.
2. Vereinbaren Sie, wie der Wiederverkäufer das Theme nennt ([Namen und Marken](namen-und-marken.md)).
3. Liefern Sie über einen Kanal, den die Agentur kontrolliert ([Updates liefern](updates-an-kunden-liefern.md)).
4. Führen Sie ein Changelog, das die Agentur weitergeben kann.

## Verwandte Seiten

- [Zusammenarbeit mit Agenturen und Partnern](zusammenarbeit-mit-agenturen-und-partnern.md)
- [Einen Fork fair umbenennen](../lebenszyklus/einen-fork-umbenennen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
