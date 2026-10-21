---
title: Das Prinzip Robust
audience: government, developer, designer
category: public-sector
order: 330
summary: WCAG-Prinzip 4, robust, für eine öffentliche Stelle, die Exponential betreibt - Name, Rolle und Wert für jedes Bedienelement, Statusmeldungen, gültiges strukturiertes Markup und wie die ARIA-Muster von admin4 und die Verarbeitung des Rich Text helfen, Ihre Templates mit assistiven Technologien kompatibel zu halten.
keywords: robust, WCAG-Prinzip 4, Name Rolle Wert, Statusmeldungen, ARIA, assistive Technologie, Live-Regionen, Syntaxanalyse, Templates, Rich-Text-XML, WCAG principle 4, name role value, status messages, WAI-ARIA 1.2, APG, assistive technology, live regions, parsing, 4.1.1, 4.1.2, 4.1.3, templates, rich text XML
type: help
---
# Das Prinzip Robust

Diese Seite richtet sich an Entwicklung und Design. Das vierte Prinzip von WCAG besagt, dass Inhalte robust genug sein
müssen, um von einer Vielzahl von Benutzeragenten, einschließlich assistiver Technologien, interpretiert zu werden
(<https://www.w3.org/TR/WCAG22/>). In WCAG 2.2 hat es zwei Kriterien der Stufen A und AA: 4.1.2 Name, Rolle, Wert und
4.1.3 Statusmeldungen; 4.1.1 Syntaxanalyse wurde in 2.2 gestrichen.

## Inhalt

1. [4.1.2 Name, Rolle, Wert](#412-name-rolle-wert)
2. [4.1.3 Statusmeldungen](#413-statusmeldungen)
3. [Warum strukturierte Inhalte helfen](#warum-strukturierte-inhalte-helfen)
4. [Verwandte Seiten](#verwandte-seiten)

## 4.1.2 Name, Rolle, Wert

- **Native Elemente zuerst.** Das Projekt folgt der ersten Regel von ARIA: Native Elemente werden bevorzugt; ein
  `title` zählt nicht als Name, und `aria-label` darf eine sichtbare Beschriftung mit Text nicht ersetzen
  (`AdminFormLabelsTest.php`;
  [WAI-ARIA 1.2](../../../../eng-us/standards/accessibility.md#wai-aria-12-and-the-aria-authoring-practices)).
- **Muster.** Wo eigene Widgets nötig sind, folgt admin4 den ARIA Authoring Practices: Reiter, Baumansicht,
  aufklappbare Navigation (das optionale Mega-Menü), Menüs, Umschaltflächen mit `aria-pressed`. WAI-ARIA 1.2 finden Sie
  unter <https://www.w3.org/TR/wai-aria-1.2/>.
- **Ihre Templates.** Jedes eigene Bedienelement in Ihrem öffentlichen Design (ein Menüknopf, ein Akkordeon, ein Filter)
  braucht einen Namen, die richtige Rolle und seinen Zustand (`aria-expanded`, `aria-selected`, `aria-current`).

Der Status des Projekts für WAI-ARIA ist **Teilweise erfüllt** für admin4, sonst **Nicht geprüft**; seine Tests prüfen Markup
und Skript-Einstiegspunkte, nicht das Laufzeitverhalten in einem Screenreader.

## 4.1.3 Statusmeldungen

- admin4 kündigt Toasts über ein Paar von Live-Regionen an und Suchergebnisse der Hilfe über `aria-live`;
  Modulmeldungen tragen `role="alert"` oder eine Statusrolle (`AdminNoticesTest.php`).
- In Ihrem Design: „Element hinzugefügt“, „Formular gesendet“, „keine Ergebnisse“ müssen ohne Verschieben des Fokus
  angekündigt werden, mit `role="status"` oder einer Region `aria-live`.

## Warum strukturierte Inhalte helfen

Exponential speichert Rich Text als XML und gibt ihn über Templates aus, statt freies HTML zu speichern
([Barrierefreiheit für Website-Ersteller](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md#exponential-features-that-help)).
Das bedeutet:

- eine Korrektur an einem Template ändert das Markup jeder Überschrift, Tabelle oder jedes Links auf einmal;
- was die Redaktion schreibt, wird in den Tags gespeichert, die das Rich-Text-Format erlaubt, nicht als beliebiges
  Markup mit eigenen Rollen;
- die Maskierung der Ausgabe in Templates (`|wash`) verhindert, dass gespeicherte Werte das Markup zerstören.

## Verwandte Seiten

- [Standards zur Barrierefreiheit](../../../../eng-us/standards/accessibility.md)
- [Das Design admin4](../../../../eng-us/features/6.0/admin4-design.md)
- [WCAG 2.2 AA auf Exponential abgebildet](wcag-2-2-zuordnung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
