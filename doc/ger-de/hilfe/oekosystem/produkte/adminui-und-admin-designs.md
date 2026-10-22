---
title: AdminUI und die Admin-Designs
audience: ecosystem, admin, editor, designer
category: ecosystem
order: 15
summary: Die Oberflächen der Administration im Ökosystem von Exponential - admin4, das aus Layouts gebaute admin4l, das von Netgen Admin UI portierte AdminUI, admin3 und die klassischen Designs -, wie ein Benutzer zwischen ihnen wechselt und die gemeinsamen Gestaltungsregeln dahinter.
keywords: Admin-Designs, admin4, admin4l, AdminUI, exp_adminui, admin3, klassische Administration, UI-Wechsel, uiswitch/design, Gestaltungsregeln, Tokens, Dunkelmodus, admin designs, classic admin, UI switch, styling rules, tokens, dark mode
type: help
---
# AdminUI und die Admin-Designs

Die Administration von Exponential gibt es in mehreren Designs. Sie zeigen dieselbe Administration mit
unterschiedlichem Aussehen und Aufbau, und ein Benutzer kann zwischen ihnen wechseln.

## Inhalt

1. [Die Designs](#die-designs)
2. [Designs wechseln](#designs-wechseln)
3. [Gemeinsame Regeln](#gemeinsame-regeln)
4. [Woher sie kommen](#woher-sie-kommen)

## Die Designs

| Design | Was es ist | Seite |
|---|---|---|
| Admin 4 (`admin4`) | "a complete administration design in the look of the setup wizard, with a light and a dark mode", ein vollständiges Design der Administration im Stil des Einrichtungsassistenten, mit hellem und dunklem Modus | [Das Design admin4](../../../funktionen/6.0/admin4-design.md) |
| Admin 4 (Layouts) (`admin4l`) | admin4, "whose page is assembled by Exponential Layouts", dessen Seite von Exponential Layouts zusammengesetzt wird | [Das Design admin4l](../../../funktionen/6.0/admin4l.md) |
| AdminUI (`adminui`) | "the Netgen Admin UI look and layout for the Exponential administration", Aussehen und Aufbau von Netgen Admin UI für die Administration, aus der Erweiterung `exp_adminui`, als eigener Siteaccess | [Exponential Admin UI](../../../funktionen/6.0/exp-adminui.md) |
| Admin 3 (`admin3`) | Ein responsives Design der Administration | [admin3, die responsive Administration](../../../funktionen/6.0/admin3-responsives-admin.md) |
| Klassisch (`admin`) und Klassisch, serverseitig gerendert (`admin_classic`) | Die klassische Administration | [Das Design der Administration wählen](../../../funktionen/6.0/ui-umschalter-design.md) |

## Designs wechseln

"A signed-in user who holds the policy `uiswitch/design` can choose which administration design they work in [...]
from the right toolbar. The choice changes the interface for that user only, and only for the siteaccess where it was
made", ein angemeldeter Benutzer mit der Richtlinie `uiswitch/design` wählt also in der rechten Werkzeugleiste das
Design, in dem er arbeitet; die Wahl gilt nur für ihn und nur für den Siteaccess, in dem sie getroffen wurde
([Das Design der Administration für Ihre Sitzung wählen](../../../funktionen/6.0/ui-umschalter-design.md)).

## Gemeinsame Regeln

Jedes Admin-Design folgt denselben Gestaltungsregeln: kein Block `<style>` und kein Attribut `style=""` in Templates,
gemeinsame Komponenten und Design-Tokens mit Werten für den Dunkelmodus, CSS für Module in `stylesheets/modules/`
([Die Administration gestalten](../../../bc/6.0/admin4-gestaltung.md); Seiten für Designer:
[Überblick über die Admin-Designs](../../designer/theme-design/admin-designs-ueberblick.md)).

## Woher sie kommen

- Die Admin-Designs sind Teil des Kernel-Repositorys (`design/`).
- AdminUI ist "A feature complete port of the legendary Netgen AdminUI Administration UI. This solution depends on
  Exponential 6.0.15* or greater", eine vollständige Portierung der legendären Netgen AdminUI, die Exponential 6.0.15 oder
  neuer voraussetzt (Beschreibung des Repositorys `se7enxweb/exp_adminui`, zitiert unter
  [AdminUI](../../wer-ist-die-exponential-foundation.md#adminui)), ein weiteres Beispiel dafür, wie das Ökosystem Arbeit
  aus einem benachbarten PHP-Projekt portiert.
- Die Symfony-Platform-Linie hat eigene Forks von Admin-UIs ([Das Ökosystem der Exponential-Plattform](../../../geschichte/oekosystem.md)).

## Verwandte Seiten

- [Produkte](README.md)
- [Rundgang durch die Administration](../../rundgang-durch-die-administration.md)
- [Themes und Designs](../pakete/themes-und-designs.md)
- [Designer: hier beginnen](../../designer/designer-hier-beginnen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
