---
title: Tabellen in Inhalten
audience: government, editor, designer
category: public-sector
order: 190
summary: Für Redaktion und Design - wann Sie eine Tabelle verwenden, wie Sie ihr im Rich Text von Exponential eine Kopfzeile, eine Beschriftung und einen Bezugsbereich für Kopfzellen geben, wie das Standarddesign sie ausgibt und wie Tabellen auf Telefonen und bei 200 % Zoom lesbar bleiben.
keywords: Tabellen, Datentabellen, Kopfzellen, Beschriftung, Layouttabellen, Rich Text, Online-Editor, Umbruch, responsive Tabellen, Redaktion, tables, data tables, header cells, th, scope, caption, summary, layout tables, rich text, online editor, WCAG 1.3.1, reflow, responsive tables, editors
type: help
---
# Tabellen in Inhalten

Diese Seite richtet sich an die Redaktion, die Zahlen und Zeitpläne veröffentlicht, und an das Design, das sie
gestaltet. Tabellen sind barrierefrei, wenn ihre Struktur im Markup steht: Kopfzellen, die sagen, was jede Spalte oder
Zeile ist, und eine Beschriftung, die sagt, worum es in der Tabelle geht. WCAG 2.2 behandelt das unter 1.3.1 Info und
Beziehungen (<https://www.w3.org/TR/WCAG22/>).

## Inhalt

1. [Wann Sie eine Tabelle verwenden](#wann-sie-eine-tabelle-verwenden)
2. [Was der Rich Text von Exponential erhält](#was-der-rich-text-von-exponential-erhält)
3. [Schritte für die Redaktion](#schritte-für-die-redaktion)
4. [Regeln für das Design](#regeln-für-das-design)
5. [Verwandte Seiten](#verwandte-seiten)

## Wann Sie eine Tabelle verwenden

- **Ja** für Daten mit Zeilen und Spalten: Gebühren, Fahrpläne, Ergebnisse, Haushalte.
- **Nein** für das Layout (Text nebeneinander stellen) oder für eine Liste, die nur eine Spalte mit Bedeutung hat.
- Halten Sie Tabellen einfach: eine Kopfzeile und, wenn nötig, eine Kopfspalte. Teilen Sie eine komplexe Tabelle in
  mehrere auf.

## Was der Rich Text von Exponential erhält

Der Rich Text wird als XML gespeichert und von Templates ausgegeben. Im Standarddesign:

| Element | Template | Ausgegeben als |
|---|---|---|
| Tabelle, mit optionaler Beschriftung, Zusammenfassung, Titel | `design/standard/templates/content/datatype/view/ezxmltags/table.tpl` | `<table>`, `<caption>`, wenn gesetzt |
| Kopfzelle, mit optionalem Bezugsbereich und Abkürzung | `.../ezxmltags/th.tpl` | `<th scope="..." abbr="...">`, wenn gesetzt |
| Datenzelle | `.../ezxmltags/td.tpl` | `<td>` |

Die Informationen zur Barrierefreiheit, die die Redaktion eingibt, gelangen also bis auf die Seite. Ob und wie der
Online-Editor die Redaktion Kopfzellen, Beschriftung und Bezugsbereich setzen lässt, hängt von der Engine ab; prüfen
Sie es in Ihrem Editor (siehe „Der Online-Editor und die Barrierefreiheit“).

## Schritte für die Redaktion

1. Fügen Sie die Tabelle mit der Tabellenfunktion des Editors ein.
2. Machen Sie die erste Zeile (und, wo es hilft, die erste Spalte) zu **Kopfzellen**, nicht zu fett formatierten
   normalen Zellen.
3. Fügen Sie eine **Beschriftung** hinzu, die sagt, was die Tabelle zeigt: „Gebühren für Baugenehmigungen, 2026“.
4. Verbinden Sie Zellen nur, wenn es sein muss; verbundene Zellen sind mit einem Screenreader schwer zu verfolgen.
5. Lassen Sie Zellen nicht leer, um Platz zu schaffen; schreiben Sie „keine“ oder „-“ mit Bedeutung.
6. Sehen Sie sich die Seite in der Vorschau in Telefonbreite an.

## Regeln für das Design

- Gestalten Sie Kopfzellen und Beschriftungen sichtbar; verbergen Sie die Beschriftung nicht.
- Lassen Sie breite Tabellen auf schmalen Bildschirmen in ihrem eigenen Container scrollen, damit die Seite selbst nicht
  seitlich scrollt (WCAG 1.4.10 Umbruch erlaubt für Datentabellen zweidimensionales Scrollen).
- Halten Sie den Kontrast von Zebrastreifen und Rändern in jedem Farbschema ausreichend.
- In admin4 trägt jede Kopfzelle `scope="col"`; folgen Sie derselben Praxis in Ihren eigenen Listen-Templates.

## Verwandte Seiten

- [Barrierefreie Inhalte für die Redaktion](barrierefreie-inhalte-fuer-die-redaktion.md)
- [Barrierefreiheit für Website-Ersteller](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)
- [Inhalte bearbeiten](../../inhalte-bearbeiten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
