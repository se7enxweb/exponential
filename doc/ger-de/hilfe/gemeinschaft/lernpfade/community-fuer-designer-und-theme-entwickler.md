---
title: Die Community für Designer und Theme-Entwickler
audience: community, designer
category: community
order: 66
summary: Was die Exponential-Community Designern und Theme-Autoren bietet und wie sie beitragen - die Bereiche für Designer, die Stilregeln der Administration, Barrierefreiheit und dunkler Modus, Themes teilen und Rückmeldungen zum Design der Administration.
keywords: Designer, Themes, Theme-Entwickler, Design, Styling der Administration, Tokens, Komponenten, Barrierefreiheit, dunkler Modus, Theme-Erweiterungen, Beitrag, designers, themers, administration styling, components, accessibility, dark mode, theme extensions, contribution
type: help
---
# Die Community für Designer und Theme-Entwickler

Designer prägen, wie jede Exponential-Website und die Administration aussehen und sich anfühlen. Die Hilfe hat einen
ganzen Bereich für sie; diese Seite zeigt, wo die Arbeit in der Community hineinpasst.

## Inhalt

1. [Wo Designer lernen](#wo-designer-lernen)
2. [Zum Design der Administration beitragen](#zum-design-der-administration-beitragen)
3. [Themes teilen](#themes-teilen)
4. [Rückmeldungen zum Design](#rückmeldungen-zum-design)

## Wo Designer lernen

| Ressource | Seite |
|---|---|
| Der Bereich für Designer | [Designer: hier beginnen](../../designer/designer-hier-beginnen.md) |
| Theme-Design: Tokens, dunkler Modus, Barrierefreiheit, rechts nach links, Checklisten | [Theme-Design](../../designer/theme-design/design-tokens.md) und die Seiten daneben |
| Theme-Erweiterungen, Veröffentlichung, Verkauf und der Lebenszyklus eines Themes | [Theme-Erweiterungen](../../designer/erweiterungen/theme-erweiterungen-hier-beginnen.md), [Veröffentlichung](../../designer/veroeffentlichung/ein-theme-veroeffentlichen-hier-beginnen.md), [Verkauf](../../designer/verkauf/verkaufen-hier-beginnen.md), [Lebenszyklus](../../designer/lebenszyklus/lebenszyklus-hier-beginnen.md) |
| Kurse für Theme-Designer | [Trainer: hier beginnen](../../trainer/trainer-hier-beginnen.md) |

## Zum Design der Administration beitragen

Templates und Stylesheets der Administrationsdesigns folgen strengen Regeln (`CONTRIBUTING.md`, „Making Changes“;
[Die Administration gestalten](../../../bc/6.0/admin4-gestaltung.md)):

- kein `<style>`-Block und kein `style=""`-Attribut in einem Template;
- die gemeinsamen Komponenten und die Design-Tokens, mit einem Wert für den dunklen Modus für jedes neue Token;
- modulspezifisches CSS nur in `stylesheets/modules/`;
- im hellen und dunklen Modus geprüft.

Ein Test, `tests/tests/design/AdminStylingRulesTest.php`, prüft die Regeln. Siehe
[Test der Stilregeln der Administration](../../designer/theme-design/der-test-der-admin-gestaltungsregeln.md) und
[Admin4-Komponenten](../../designer/theme-design/admin4-komponenten.md).

## Themes teilen

Ein Theme wird meist als Design-Erweiterung in einem eigenen Repository und Composer-Paket geteilt
([Erweiterungen und Themes teilen](../mitwirken/erweiterungen-und-themes-teilen.md),
[Design-Erweiterungen](../../designer/theme-design/design-erweiterungen.md)). Bevor Sie es teilen, gehen Sie die
[Checkliste für den Start eines Themes](../../designer/theme-design/checkliste-theme-start.md) und die
[Checkliste zur Barrierefreiheit](../../designer/theme-design/checkliste-barrierefreiheit.md) durch.

## Rückmeldungen zum Design

Designer bemerken Probleme, die andere übersehen: Kontrast, Fokuszustände, Abstände bei starkem Zoom, dunkler Modus.
Melden Sie sie mit einem Bildschirmfoto, dem Namen des Designs, dem Browser und der Zoomstufe
([Einen Fehler melden](../mitwirken/einen-fehler-melden.md)). Kontrast- und Barrierefreiheitsprobleme betreffen echte
Nutzer; beschreiben Sie, wer betroffen ist.

## Verwandte Seiten

- [Wege durch die Community](README.md)
- [Designer: hier beginnen](../../designer/designer-hier-beginnen.md)
- [Barrierefreie Folien und Visualisierungen](../../fuersprecher/barrierefreie-folien-und-grafiken.md)
- [Für Barrierefreiheit eintreten](../../fuersprecher/fuer-barrierefreiheit-eintreten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
