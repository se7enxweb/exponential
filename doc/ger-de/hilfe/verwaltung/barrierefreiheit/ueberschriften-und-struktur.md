---
title: Überschriften und Struktur
audience: government, editor, designer
category: public-sector
order: 170
summary: Für Redaktion und Design - wie Überschriften im Rich Text von Exponential funktionieren (Überschriftenebene 1 wird im Standarddesign zu h2 unter dem Seitentitel), warum die Reihenfolge für Nutzerinnen und Nutzer von Screenreadern wichtig ist und wie Sie Listen, Absätze und Seitentitel strukturiert halten.
keywords: Überschriften, Überschriftenebenen, Struktur, Rich Text, Online-Editor, Listen, Seitentitel, Screenreader, Gliederung, Redaktion, headings, heading levels, h1, h2, structure, rich text, online editor, lists, page title, screen reader, outline, WCAG 1.3.1, 2.4.6, 2.4.2, editors
type: help
---
# Überschriften und Struktur

Diese Seite richtet sich an die Redaktion, die Seiten schreibt, und an das Design, das Seiten-Templates baut. Wer
einen Screenreader nutzt, bewegt sich anhand der Überschriften durch eine Seite; eine sinnvolle Gliederung ist das
Nützlichste, was die Redaktion diesen Menschen geben kann. WCAG 2.2 behandelt das unter 1.3.1 Info und Beziehungen
und 2.4.6 Überschriften und Beschriftungen (<https://www.w3.org/TR/WCAG22/>).

## Inhalt

1. [Wie Überschriften aus Exponential herauskommen](#wie-überschriften-aus-exponential-herauskommen)
2. [Regeln für die Redaktion](#regeln-für-die-redaktion)
3. [Regeln für das Design](#regeln-für-das-design)
4. [Seitentitel](#seitentitel)
5. [Verwandte Seiten](#verwandte-seiten)

## Wie Überschriften aus Exponential herauskommen

- Das **Feld Name oder Titel des Objekts** gibt das Template der Vollansicht meist als `h1` der Seite aus.
- Rich Text wird als XML gespeichert. Eine Überschrift im Rich Text hat eine Ebene; im Standarddesign macht das
  Template `design/standard/templates/content/datatype/view/ezxmltags/header.tpl` aus **Ebene 1 ein `h2`**, aus Ebene 2
  ein `h3` und so weiter, weil die Seite ihr `h1` bereits hat.
- Jede Überschrift im Rich Text erhält außerdem einen Anker (`eztoc...`), auf den ein Inhaltsverzeichnis verlinken kann.

Prüfen Sie, was Ihr eigenes Design tut: Ein Design kann `header.tpl` überschreiben.

## Regeln für die Redaktion

1. Verwenden Sie für Überschriften die Überschriftenformate des Editors, nie Fettschrift oder eine größere Schrift.
2. Beginnen Sie mit der ersten Überschriftenebene und überspringen Sie nach unten keine Ebenen (eine Überschrift der
   Ebene 3 gehört unter eine der Ebene 2).
3. Formulieren Sie Überschriften aussagekräftig: „Öffnungszeiten“ statt „Informationen“.
4. Verwenden Sie für Listen die Listenschaltflächen des Editors, nicht von Hand getippte Gedankenstriche.
5. Behandeln Sie ein Thema je Abschnitt; lange Seiten mit klaren Überschriften sind leichter zu benutzen als viele kurze
   Seiten.

## Regeln für das Design

- Ein `h1` je Seite (der Name der Seite); Landmarks (`header`, `nav`, `main`, `footer`); Überschriften in
  Block-Templates, die sich in die Gliederung der Seite einfügen
  ([Barrierefreiheit für Website-Ersteller](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)).
- Verwenden Sie Überschriftenelemente in Blöcken nicht für visuelle Effekte (ein Teaser-Titel in einer Liste kann je
  nach Gliederung ein `h2` oder `h3` sein, nicht nach seinem Aussehen).
- Mit dem Layout-Editor gebaute Landingpages: Prüfen Sie, dass die Block-Templates eine logische Gliederung ergeben
  ([Landingpages mit Layouts](../../../../eng-us/help/creators/website-creators/landing-pages-with-layouts.md)).

## Seitentitel

WCAG 2.4.2 verlangt beschreibende Seitentitel. Den `<title>` baut Ihr Pagelayout aus dem Namen des Inhalts und dem
Namen der Website ([Das Pagelayout](../../../../eng-us/help/creators/website-creators/the-pagelayout.md)). Den ersten Teil steuert die
Redaktion: Geben Sie jedem Objekt einen klaren, eindeutigen Namen. Die eigenen Titel der Administration baut
`page_head.tpl`.

## Verwandte Seiten

- [Barrierefreie Inhalte für die Redaktion](barrierefreie-inhalte-fuer-die-redaktion.md)
- [Inhalte bearbeiten](../../inhalte-bearbeiten.md)
- [Das Pagelayout](../../../../eng-us/help/creators/website-creators/the-pagelayout.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
