---
title: "Ihre Overrides mit den Standard-Templates vergleichen"
audience: designer, developer
category: theme-lifecycle
order: 110
summary: Herausfinden, ob sich die Standard-Templates, die Sie in Ihr Theme kopiert haben, im Original geändert haben - Ihre Kopien auflisten, sie mit dem neuen Release vergleichen und Korrekturen (besonders zu Maskierung und Barrierefreiheit) in Ihre Fassungen übernehmen.
keywords: Overrides, Template-Abweichung, diff, Standard-Templates, design/standard, Änderungen im Original, zusammenführen, Korrekturen der Maskierung, overrides, template drift, standard templates, upstream changes, merge, escaping fixes
type: help
---
# Ihre Overrides mit den Standard-Templates vergleichen

Diese Seite ist für Designer, deren Theme Kopien von Standard-Templates enthält (aus `design/standard` oder einem anderen
Theme), die es ändert. Wird das Original korrigiert, behält Ihre Kopie die alte Fassung, bis Sie die Korrektur übernehmen.

## Schritt für Schritt

1. **Listen Sie Ihre Kopien auf**: jedes Template in Ihrem Design, das ein Gegenstück in `design/standard/templates/` (oder
   im Basis-Theme) hat:

   ```bash
   cd extension/mytheme/design/mytheme/templates
   find . -name '*.tpl' | while read f; do [ -f "../../../../../design/standard/templates/$f" ] && echo "$f"; done
   ```

2. **Sehen Sie, was sich im Original geändert hat**, zwischen altem und neuem Release, für jede Datei:

   ```bash
   git -C /path/to/exponential log --oneline v6.0.13..v6.0.14 -- design/standard/templates/node/view/full.tpl
   ```

3. **Lesen Sie jede Änderung** und entscheiden Sie, ob Ihre Kopie sie braucht. Korrekturen der Maskierung (`|wash`), der
   Barrierefreiheit und an Markup, das der Kernel jetzt erwartet, sind meist nötig.
4. **Übernehmen Sie** die nötigen Änderungen von Hand in Ihre Kopie und behalten Sie Ihre eigenen Änderungen.
5. **Testen Sie** die Seiten, die das Template verwenden.

## Halten Sie die Liste kurz

Je weniger Templates Sie kopieren, desto weniger gibt es zu vergleichen. Überschreiben Sie lieber ein Template für eine
Klasse, statt allgemeine zu kopieren, und verwenden Sie wo möglich die Design-Kaskade statt Kopien
([Die Design-Kaskade](../theme-design/die-design-kaskade.md)).

## Verwandte Seiten

- [Upgrade-Test Schritt für Schritt](upgrade-test-schritt-fuer-schritt.md)
- [Override-Grundlagen](../vorlagen/override-grundlagen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
