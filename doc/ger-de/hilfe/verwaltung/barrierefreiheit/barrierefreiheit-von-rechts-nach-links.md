---
title: Barrierefreiheit von rechts nach links
audience: government, admin, designer, developer
category: public-sector
order: 260
summary: Für öffentliche Stellen, die auf Arabisch, Hebräisch, Persisch, Urdu oder in anderen Sprachen von rechts nach links veröffentlichen - wie Exponential Schreibrichtung und Sprache in der Administration und in Inhalten setzt, wie Namen isoliert werden und was Ihr öffentliches Design tun muss.
keywords: rechts nach links, RTL, bidirektionaler Text, Arabisch, Hebräisch, Persisch, Urdu, dir-Attribut, lang-Attribut, Schreibrichtung, Locale, Inhaltssprachen, mehrsprachige öffentliche Dienste, right to left, bidirectional text, bidi, Arabic, Hebrew, Persian, dir attribute, lang attribute, bdi, TextDirection, locale, content languages, WCAG 1.3.2, 3.1.1, 3.1.2, multilingual public services
type: help
---
# Barrierefreiheit von rechts nach links

Diese Seite richtet sich an Administration, Design und Entwicklung in öffentlichen Stellen, die Gemeinschaften in
Sprachen von rechts nach links bedienen. Schreibrichtung und Sprache sind Fragen der Barrierefreiheit: Ein Screenreader
liest arabischen Text mit der falschen Stimme, wenn `lang` falsch ist, und Satzzeichen springen an das falsche Ende,
wenn die Schreibrichtung nicht isoliert ist. Die folgenden Fakten stammen aus
[Sprachen von rechts nach links in der Administration](../../../funktionen/6.0/rechts-nach-links.md).

## Inhalt

1. [Woher die Schreibrichtung kommt](#woher-die-schreibrichtung-kommt)
2. [In der Administration](#in-der-administration)
3. [In Inhalten](#in-inhalten)
4. [In Ihrem öffentlichen Design](#in-ihrem-öffentlichen-design)
5. [Testen](#testen)
6. [Verwandte Seiten](#verwandte-seiten)

## Woher die Schreibrichtung kommt

Die einzige Quelle ist die Locale-Datei: `share/locale/<locale>.ini`, `[LanguageSettings] TextDirection=rtl`; 43
Locale-Dateien enthalten diesen Eintrag. Templates können `$site.text_direction` abfragen oder das
`is_right_to_left` eines Locale.

## In der Administration

- Ist die **Oberflächensprache** eine Sprache von rechts nach links, schreibt jedes Pagelayout der Administration
  `dir="rtl"` an `<html>`; Seitenspalten tauschen die Plätze, und richtungsabhängige Symbole zeigen in die andere
  Richtung.
- Von der Redaktion eingegebene Namen (Knotennamen, Benutzernamen) werden mit `dir="auto"` oder `<bdi>` isoliert, wo die
  Administration sie in ihren Text einfügt, sodass Text mit gemischter Schreibrichtung seine Satzzeichen an der
  richtigen Stelle behält.
- Eine Seite von links nach rechts bleibt Byte für Byte unverändert.
- Abgesichert durch `tests/tests/design/AdminRightToLeftTest.php`; das Projekt zählt dies zu WCAG 1.3.2
  Bedeutungstragende Reihenfolge ([Standards zur Barrierefreiheit](../../../standards/barrierefreiheit.md#wcag-22)).

## In Inhalten

Unabhängig von der Oberflächensprache trägt Text in einer **Inhaltssprache** von rechts nach links `lang` und
`dir="rtl"`, wo er angezeigt und bearbeitet wird:

- `design/standard/templates/parts/exp_content_direction.tpl` gibt für ein Inhalts-Locale von rechts nach links zum
  Beispiel ` lang="he-IL" dir="rtl"` aus; Ihr Design kann es einbinden;
- die Bearbeitungsformulare der Designs standard, admin, admin4 und AdminUI;
- der Online-Editor wechselt seine Schreibrichtung (`directionality` von TinyMCE 3 und 8, `contentDirection` des
  Editors tiptap).

Die Inhaltssprachen selbst werden eingerichtet, wie unter [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)
beschrieben.

## In Ihrem öffentlichen Design

- Schreiben Sie `lang` und `dir` an `<html>` aus dem Locale des Siteaccess, wie es die Administration tut.
- Verwenden Sie logische CSS-Eigenschaften (`margin-inline-start` statt `margin-left`), damit ein Stylesheet beide
  Richtungen bedient.
- Binden Sie `parts/exp_content_direction.tpl` ein, wo innerhalb einer Seite Inhalte der anderen Schreibrichtung
  erscheinen (ein Zitat, eine übersetzte Zusammenfassung).
- Spiegeln Sie richtungsabhängige Symbole (Pfeile, Winkel), nicht Logos oder Mediensteuerungen.
- Die Sicht der Website-Ersteller finden Sie unter
  [Websites von rechts nach links](../../ersteller/website-ersteller/websites-von-rechts-nach-links.md).

## Testen

- Stellen Sie die Oberflächensprache eines Testbenutzers auf eine Sprache von rechts nach links um und führen Sie die
  wichtigsten redaktionellen Aufgaben aus.
- Prüfen Sie auf der öffentlichen Website jeden Siteaccess von rechts nach links mit einem Screenreader, der auf diese
  Sprache eingestellt ist: Die Stimme muss mit `lang` wechseln.
- Prüfen Sie gemischten Text: einen englischen Namen in einem arabischen Satz, eine Telefonnummer, ein Datum.

## Verwandte Seiten

- [Sprachen von rechts nach links in der Administration](../../../funktionen/6.0/rechts-nach-links.md)
- [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)
- [Sprachen](../../sprachen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
