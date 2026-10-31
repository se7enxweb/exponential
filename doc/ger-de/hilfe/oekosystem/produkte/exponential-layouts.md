---
title: Exponential Layouts im Ökosystem
audience: ecosystem, editor, designer, developer
category: ecosystem
order: 13
summary: Der Platz von Exponential Layouts im Ökosystem - der visuelle Seitenbaukasten, der das Konzept der Upstream-Layouts-Pakete in den Kernel von Exponential bringt, seine Pakete, für wen er ist, wie er sich zu Nexus und der DXP verhält und wo Sie mehr lesen.
keywords: Exponential Layouts, Seitenbaukasten, Layouts, Zonen, Blöcke, Regeln, explayouts, Nexus, DXP, Redakteure, Designer, page builder, zones, blocks, rules, editors, designers
type: help
---
# Exponential Layouts im Ökosystem

**"A visual page builder for Exponential. It brings the concept of the upstream layouts packages to the Exponential kernel: a layout is
a saved arrangement of blocks in zones, and a rule decides which pages get it"**, ein visueller Seitenbaukasten, der
das Konzept der Upstream-Layouts-Pakete in den Kernel bringt: Ein Layout ist eine gespeicherte Anordnung von Blöcken in Zonen,
und eine Regel entscheidet, welche Seiten es bekommen (`README.md`).

## Inhalt

1. [Sein Platz im Ökosystem](#sein-platz-im-ökosystem)
2. [Die Pakete](#die-pakete)
3. [Quer durch die Produktfamilie](#quer-durch-die-produktfamilie)
4. [Wo Sie mehr lesen](#wo-sie-mehr-lesen)

## Sein Platz im Ökosystem

- Seit 6.0.15 in `composer.json` verlangt
  ([Die Produktfamilie im Überblick](../../wer-ist-die-exponential-foundation.md#die-produktfamilie-im-überblick)).
- Er portiert ein Konzept aus anderen PHP-Paketen, den Upstream-Layouts-Paketen, mit Nennung
  ([Wie das Projekt wächst](../../wer-ist-die-exponential-foundation.md#wie-das-projekt-wächst)).
- Er ist für "Editors who rearrange pages without template work, and designers", Redakteure, die Seiten ohne Arbeit an
  Templates umstellen, und Designer ([Exponential Layouts](../../wer-ist-die-exponential-foundation.md#exponential-layouts)).

## Die Pakete

`se7enxweb/explayouts` und die elf Pakete `explayouts-*`: "Exponential Layouts, the visual page builder, its editor,
API, content browser and queries", also der Seitenbaukasten, sein Editor, die API, der Inhaltsbrowser und die Abfragen
([Wer ist 7x?](../../wer-ist-7x.md#das-ökosystem-der-erweiterungen)).

## Quer durch die Produktfamilie

| Produkt | Layouts |
|---|---|
| Exponential 6 | Exponential Layouts (`explayouts`) |
| Exponential Platform Nexus | Liefert den Layout-Editor: Seine `composer.json` verlangt `netgen/layouts-core`, `netgen/layouts-standard` und `netgen/layouts-ui` |
| Exponential Platform DXP | Verlangt `se7enxweb/layouts-core`, liefert aber keinen Layout-Editor (keine Layouts-Oberfläche und keine Standardblöcke) |

Quelle: [Die Produkte im Einzelnen](../../wer-ist-die-exponential-foundation.md#die-produkte-im-einzelnen).

## Wo Sie mehr lesen

- Für Redakteure: [Der Layout-Editor](../../layout-editor.md).
- Für Designer: [Blockdefinitionen](../../designer/theme-design/blockdefinitionen.md) und
  [Ansichts-Templates für Blöcke](../../designer/theme-design/block-ansichtstemplates.md).
- Für Fürsprecher: [Argumente: Exponential Layouts](../../fuersprecher/gespraechspunkte-exponential-layouts.md).

## Verwandte Seiten

- [Produkte](README.md)
- [Der Layout-Editor](../../layout-editor.md)
- [Landingpages mit Layouts](../../kunden/wachstum/landingpages-mit-layouts.md)
- [Upstream-Projekte und Forks](../php-stack/upstream-projekte-und-forks.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
