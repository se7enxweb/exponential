---
title: Layouts Core in der DXP
audience: dxp-developer
category: dxp-development
order: 44
summary: Was se7enxweb/layouts-core einem DXP-Projekt gibt und was nicht - den Kern für Modell und Ausgabe von Layouts ohne Editor, Administration oder Standardblöcke -, warum es den Fork gibt (PHP 8.4 und Twig) und wie Sie zu einem vollständigen Seitenbaukasten kommen.
keywords: Layouts, layouts-core, se7enxweb/layouts-core, Netgen Layouts, Seitenbaukasten, Editor, Zonen, Blöcke, Regeln, PHP 8.4, private(set), Twig, Nexus, page builder, editor, zones, blocks, rules
type: help
---
# Layouts Core in der DXP

Diese Seite richtet sich an Entwickler, die `se7enxweb/layouts-core` in einem DXP-Projekt sehen und sich fragen, ob die
DXP einen Seitenbaukasten hat. Sie liefert keinen mit; sie liefert den Kern, auf dem ein Seitenbaukasten aufbaut.

## Inhalt

1. [Was das Paket ist](#was-das-paket-ist)
2. [Was die DXP nicht enthält](#was-die-dxp-nicht-enthält)
3. [Warum der Fork](#warum-der-fork)
4. [Wenn Sie einen Seitenbaukasten brauchen](#wenn-sie-einen-seitenbaukasten-brauchen)

## Was das Paket ist

Aus [Layouts auf der Plattform](../../funktionen/6.0/plattform-layouts-core-fork.md#was-es-ist): Das Core-Paket "holds the
model (layouts, zones, blocks, collections, rules, targets, conditions), the parameter system, the block definitions
and the rendering". Es ist der Fork von `netgen/layouts-core`, getaggt als `2.0.0-se7enx.1` auf Basis des Upstream
`2.0.0`, und braucht PHP 8.4 (`^8.4`).

## Was die DXP nicht enthält

Das Metapaket verlangt nur den Core. Diese Pakete von Netgen Layouts sind nicht Teil der DXP:

| Nicht enthalten | Was es hinzufügen würde |
|---|---|
| Die Editor-App und die Administration von Layouts | den visuellen Editor (`/nglayouts/app`) und die Layout-Administration (`/nglayouts/admin`) auf Nexus |
| Die Standardblöcke | fertige Blocktypen |
| Die Integration mit dem Inhalts-Repository und der Site API | Inhaltselemente als Blockelemente, inhaltsbewusste Regeln |

Redakteure eines einfachen DXP-Projekts können also keine Seiten mit Blöcken anordnen. Sagen Sie das in Angeboten
([Aussagen, die Sie vermeiden sollten](aussagen-die-sie-vermeiden-sollten.md)).

## Warum der Fork

PHP 8.4 hat asymmetrische Sichtbarkeit (`private(set)`) hinzugefügt. Twig prüft `ReflectionProperty::isInitialized()`,
bevor es nach einem Getter sucht; bei solchen Eigenschaften schlägt die Prüfung fehl, und ein Block-Template, das einen
Parameter liest, bricht mit "Call to undefined method" ab. Der Fork ergänzt ausdrückliche Getter an
`Netgen\Layouts\Parameters\Parameter` (`getName()`, `getParameterDefinition()`, `getValue()`, `isEmpty()`,
`getValueObject()`) und deklariert `replace: netgen/layouts-core`, sodass jedes Paket von Netgen Layouts, das den
Upstream-Namen verlangt, den Fork erhält.

## Wenn Sie einen Seitenbaukasten brauchen

| Möglichkeit | Was Sie erhalten |
|---|---|
| Exponential Platform Nexus (1.3 oder der v5-Starter) | Netgen Layouts 2.0 mit seinem Editor, Standardblöcken und einer Demo-Website ([Nexus-Starter](../../funktionen/6.0/plattform-nexus-starter.md)) |
| Exponential 6 | Exponential Layouts, die Portierung auf den Kernel von Exponential 6 ([Der Layout-Editor](../layout-editor.md)) |
| Ihr eigenes DXP-Projekt plus die fehlenden Pakete von Netgen Layouts | grundsätzlich möglich; testen Sie es an einer Kopie, mit PHP 8.4 oder neuer, und prüfen Sie jedes Paket gegen die Tabelle der Forks |

Die Begriffe sind überall dieselben: "layout types with named zones, blocks with parameters, collections of items, and
rules with targets that pick the layout of a page. If you know one, you know the other."

## Verwandte Seiten

- [Layouts auf der Plattform: der Fork von layouts-core](../../funktionen/6.0/plattform-layouts-core-fork.md)
- [Was die DXP mitliefert und was nicht](was-die-dxp-mitliefert-und-was-nicht.md)
- [Gesprächspunkte: Exponential Layouts](../fuersprecher/gespraechspunkte-exponential-layouts.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
