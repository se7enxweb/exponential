---
title: Suchfeld und Ergebnisseite
audience: government, website-creator
category: public-sector
order: 40
summary: Wie Sie mit Exponential ein barrierefreies Suchfeld und eine Ergebnisseite für eine öffentliche Site bauen - das Formular, das SearchText an content/search sendet, die Beschränkung auf einen Teil der Site mit SubTreeArray, das Ergebnis-Template, leere Ergebnisse und Punkte zur Barrierefreiheit.
keywords: Suchfeld, Suchformular, Ergebnisseite, barrierefreie Suche, keine Ergebnisse, search box, search form, results page, content/search, SearchText, SubTreeArray, search.tpl, accessible search, no results, role search
type: help
---
# Suchfeld und Ergebnisseite

Diese Seite richtet sich an Site-Ersteller. Das Suchfeld ist ein Formular, das `SearchText` an `content/search` sendet;
die Ergebnisseite ist das Template `content/search.tpl` Ihres Designs
([Site-Suche](../../../../../eng-us/help/creators/website-creators/site-search.md)).

## Inhalt

1. [Das Suchfeld](#das-suchfeld)
2. [Einen Teil der Site durchsuchen](#einen-teil-der-site-durchsuchen)
3. [Die Ergebnisseite](#die-ergebnisseite)
4. [Wenn nichts gefunden wird](#wenn-nichts-gefunden-wird)
5. [Barrierefreiheit](#barrierefreiheit)

## Das Suchfeld

```text
<form role="search" method="get" action={'content/search'|ezurl}>
  <label for="site-search">{'Search'|i18n( 'design/mysite' )}</label>
  <input type="search" id="site-search" name="SearchText">
  <button type="submit">{'Search'|i18n( 'design/mysite' )}</button>
</form>
```

Übersetzen Sie die Beschriftungen mit `|i18n`, damit jeder Sprach-Siteaccess sie in seiner Sprache zeigt.

## Einen Teil der Site durchsuchen

Die View `content/search` liest einen Parameter `SubTreeArray` und beschränkt die Ergebnisse auf diesen Teilbaum
(`kernel/private/classes/views/content/search.php`). Ein Suchfeld im Offenlegungsprotokoll oder im Datenkatalog kann
ihn als verstecktes Feld senden:

```text
<input type="hidden" name="SubTreeArray" value="{$node.node_id}">
```

## Die Ergebnisseite

Überschreiben Sie `content/search.tpl` in Ihrem Design
([Template-Overrides](../../../../../eng-us/help/creators/website-creators/template-overrides.md)). Es erhält die Ergebnisse und den
Suchtext. Zeigen Sie:

- den Suchtext und die Zahl der Ergebnisse;
- jedes Ergebnis mit seinem Titel (ein Link), einem kurzen Text, seinem Typ (Seite, Dokument, Beschluss) und Datum;
- Blättern, wenn es mehr Ergebnisse gibt, als eine Seite fasst.

Verwenden Sie für die Ergebnisse die Zeilenansicht jeder Klasse, damit ein Dokument sein Format und seine Größe und ein
Beschluss sein Aktenzeichen zeigt.

## Wenn nichts gefunden wird

Sagen Sie es klar, wiederholen Sie den Suchtext und bieten Sie an, was man versuchen kann: andere Wörter, das A bis Z
der Leistungen, die Kontaktseite. Begriffe ohne Treffer helfen Ihnen **Setup > Suchstatistiken** zu sammeln (siehe
„Suchstatistik und Datenschutz“).

## Barrierefreiheit

- Eine sichtbare Beschriftung, nicht nur ein Platzhalter.
- `role="search"` am Formular (wie oben), damit assistive Technologien es finden.
- Die Überschrift der Ergebnisse erhält den Fokus oder kündigt die Zahl der Ergebnisse an.
- Ergebnislinks, die für sich allein verständlich sind (der Titel).
- Prüfen Sie die Seite nur mit der Tastatur und bei 200 % Zoom
  ([Barrierefreiheit für Site-Ersteller](../../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)).

## Verwandte Seiten

- [Site-Suche](../../../../../eng-us/help/creators/website-creators/site-search.md)
- [Eingebaute Suche für öffentliche Sites](eingebaute-suche-fuer-oeffentliche-sites.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
