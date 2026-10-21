---
title: Ladereihenfolge der Erweiterungen
audience: developer, app-creator, admin
category: extension-development
order: 50
summary: Welche Erweiterung gewinnt, wenn zwei dieselbe Einstellung oder dasselbe Template liefern - die geschriebene Reihenfolge von ActiveExtensions, wie deklarierte Abhängigkeiten sie umsortieren, zusätzliche Erweiterungsordner und wie Sie die tatsächliche Reihenfolge sehen.
keywords: Ladereihenfolge, Reihenfolge, Priorität, Abhängigkeiten, wer gewinnt, load order, loading order, priority, ActiveExtensions, ExtensionOrdering, requires, extends, ActiveAccessExtensions, AdditionalExtensionDirectories, extension_src, which wins
type: help
---
# Ladereihenfolge der Erweiterungen

Teil 3, Kapitel 5. Wenn zwei Erweiterungen dieselbe Einstellung, denselben Template-Pfad oder dasselbe Override
mitbringen, gewinnt eine von beiden. Welche, hängt von der **Ladereihenfolge** ab. Dieses Kapitel erklärt, wie die
Reihenfolge entsteht und wie Sie sie steuern.

## Die Regel

Eine Erweiterung, die in der Ladereihenfolge **weiter vorn** steht, hat die **höhere** Priorität: Ihre Einstellungen
gewinnen, und ihre Designordner werden zuerst durchsucht. Die Erweiterung, die gewinnen soll, steht also zuerst.

## Woher die Reihenfolge kommt

1. **Die geschriebene Reihenfolge**: `ActiveExtensions[]` in der Reihenfolge, in der die Einstellungsdateien sie
   auflisten, gefolgt von `ActiveAccessExtensions[]` des Siteaccess.
2. **Deklarierte Abhängigkeiten**: Mit `site.ini [ExtensionSettings] ExtensionOrdering=enabled` (die Vorgabe)
   sortiert der Kernel die Liste nach den `<dependencies>` jeder `extension.xml` (`eZExtension::extensionOrdering()`):

   | Ihre `extension.xml` sagt | Wirkung |
   |---|---|
   | `<requires><extension name="x"/></requires>` | `x` wird vor Ihrer geladen |
   | `<uses><extension name="x"/></uses>` | `x` wird vor Ihrer geladen |
   | `<extends><extension name="x"/></extends>` | Ihre wird vor `x` geladen und gewinnt damit gegen sie |

   Erweiterungen, die nichts miteinander zu tun haben, behalten ihre geschriebene Reihenfolge. Ein Kreis von
   Abhängigkeiten lässt sich nicht sortieren; dann wird die geschriebene Reihenfolge verwendet.

## Praktische Folgen

| Sie wollen | Tun Sie Folgendes |
|---|---|
| Die Einstellungserweiterung Ihrer Website soll gegen alles gewinnen | tragen Sie sie in `ActiveExtensions[]` zuerst ein |
| Ihr Theme soll die Templates eines anderen Themes überschreiben | deklarieren Sie in Ihrer `extension.xml` `<extends>` darauf, oder tragen Sie Ihres früher ein |
| Eine Bibliothekserweiterung soll vor Ihrer verfügbar sein | deklarieren Sie `<requires>` |

## Die tatsächliche Reihenfolge sehen

```bash
./console exp:ini get site.ini/ExtensionSettings/ActiveExtensions
```

gibt die geschriebene Reihenfolge aus. **Setup > Extensions** zeigt auf jeder Karte "Geladen als" Nummer *n*, wenn die
tatsächliche Position von der geschriebenen abweicht, und listet Probleme mit Abhängigkeiten auf. In PHP gibt
`eZExtension::activeExtensions()` die Reihenfolge zurück, die der Kernel verwendet.

Die Designkette zeigt die Auswirkung auf Templates direkt; das Skript in
[Die Designkette](../teil-2-builder/die-design-kette.md) gibt sie aus.

## Erweiterungen in mehr als einem Ordner

Seit Juli 2026 können Erweiterungen in mehr als einem Ordner liegen:

```ini
[ExtensionSettings]
AdditionalExtensionDirectories[]
AdditionalExtensionDirectories[]=extension_src
```

`extension/` bleibt der erste Ordner; die zusätzlichen Ordner werden danach durchsucht. Eine typische Aufteilung hält
installierte Erweiterungen in `extension/` und Ihre eigenen in `extension_src/`. Aktivierung und Ladereihenfolge
ändern sich dadurch nicht: Eine Erweiterung wird über ihren Ordnernamen benannt, wo immer sie liegt. Siehe
[Erweiterungen in mehr als einem Ordner](../../../../eng-us/features/6.0/additional-extension-directories.md).

## Verwandte Seiten

- Zurück: [Eine Erweiterung aktivieren](eine-erweiterung-aktivieren.md)
- Weiter: [composer.json für eine Erweiterung](composer-json-fuer-erweiterungen.md)
- [Ladereihenfolge der Erweiterungen und sicheres Speichern](../../../../eng-us/features/6.0/extension-loading-order.md)
- [extension.xml: Metadaten und Abhängigkeiten](extension-xml.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
