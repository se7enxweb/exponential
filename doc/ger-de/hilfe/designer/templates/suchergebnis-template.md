---
title: Suchergebnis-Template
audience: designer, developer
category: templates
order: 700
summary: Die Suche der Website gestalten - das Template content/search.tpl und die Variablen, die es verwendet, der Such-Fetch, Ergebnisse mit einer Suchansicht gezeichnet, der Navigator mit dem Suchtext in seinen Links, ein barrierefreies Suchformular, keine Ergebnisse und die erweiterte Suche.
keywords: Such-Template, content/search.tpl, Suchergebnisse, searchresult.tpl, SearchText, fetch content search, Suchformular, keine Ergebnisse, erweiterte Suche, Gestaltung der Suchseite, search template, search results, search form, no results, advanced search, search page design
type: help
---
# Suchergebnis-Template

Diese Seite ist für Designer, die die Suchseite der Website gestalten. Die Such-View des Moduls content stellt
`content/search.tpl` dar; Ihr Design kopiert es und ändert das Markup.

## Inhalt

1. [Was das mitgelieferte Template tut](#was-das-mitgelieferte-template-tut)
2. [Ihre Fassung](#ihre-fassung)
3. [Das Formular](#das-formular)
4. [Keine Ergebnisse](#keine-ergebnisse)
5. [Andere Suchseiten](#andere-suchseiten)

## Was das mitgelieferte Template tut

`design/standard/templates/content/search.tpl`:

1. ruft die Ergebnisse selbst ab, wenn `$use_template_search` gesetzt ist:
   `fetch( 'content', 'search', hash( 'text', $search_text, 'section_id', ..., 'subtree_array', ..., 'offset', $view_parameters.offset, 'limit', $page_limit ) )`,
   und liest `SearchResult`, `SearchCount` und `StopWordArray` aus dem Ergebnis;
2. gibt das Formular aus, die Zahl der Treffer, die Ergebnisse über `content/searchresult.tpl` und den Navigator mit dem
   Suchtext in den Seitenlinks (`page_uri_suffix`).

Die Variablen, die es verwendet: `$search_text`, `$search_result`, `$search_count`, `$stop_word_array`, `$view_parameters`.

## Ihre Fassung

Kopieren Sie es nach `design/mytheme/templates/content/search.tpl`, behalten Sie die Variablen und den Fetch und ändern Sie das
Markup. Zeichnen Sie jeden Treffer mit einem Ansichtsmodus, damit Klassen ihr eigenes Aussehen bekommen:

```tpl
<h1>{'Search'|i18n( 'design/mytheme/search' )}</h1>
{if $search_text}
    <p role="status">{'%count results for "%query"'|i18n( 'design/mytheme/search',, hash( '%count', $search_count, '%query', $search_text|wash ) )}</p>
{/if}
<ol class="search-results">
{foreach $search_result as $hit}
    <li>{node_view_gui content_node=$hit view='line'}</li>
{/foreach}
</ol>
```

Maskieren Sie `$search_text` überall, wo es ausgegeben wird: Es stammt vom Besucher.

## Das Formular

```tpl
<form role="search" action={'/content/search'|ezurl} method="get">
    <label for="search-text">{'Search for'|i18n( 'design/mytheme/search' )}</label>
    <input id="search-text" type="search" name="SearchText" value="{$search_text|wash}">
    <button type="submit" name="SearchButton">{'Search'|i18n( 'design/mytheme/search' )}</button>
</form>
```

Der Feldname `SearchText` ist das, was die Such-View liest.

## Keine Ergebnisse

Sagen Sie es in Worten, wiederholen Sie die Anfrage und bieten Sie Hilfe an: die Schreibweise prüfen, weniger Wörter, ein Link
zur Startseite. Das mitgelieferte Template listet auch Stoppwörter auf, die ignoriert wurden.

## Andere Suchseiten

`content/advancedsearch.tpl` für das erweiterte Formular (das Design media gestaltet es mit `exp-advanced-search.css`). Ist eine
Suchmaschinen-Erweiterung wie ezfind installiert, können deren eigene Templates diese ersetzen; prüfen Sie den Design-Ordner der
Erweiterung.

## Verwandte Seiten

- [Seitenumbruch](seitenumbruch.md)
- [Barrierefreie Formulare](../theme-design/barrierefreie-formulare.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
