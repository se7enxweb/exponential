---
title: Vollansicht
audience: designer, developer
category: templates
order: 510
summary: Die Vollansicht, das Template einer Inhaltsseite - was es erhält ($node, $view_parameters, $language_code), ein vollständiges Artikelbeispiel, die eine h1, Unterelemente mit Blättern, Werte an das Pagelayout übergeben, Regeln für das Caching und Vollansichten pro Klasse.
keywords: Vollansicht, node/view/full.tpl, Seiten-Template, Artikelseite, $node, view_parameters, language_code, cache_ttl, persistent_variable, Override der Vollansicht, full view, page template, article page, full view override
type: help
---
# Vollansicht

Diese Seite ist für Designer, die das Haupt-Template von Inhaltsseiten schreiben. Öffnet ein Besucher eine Seite, stellt das
Modul content den Knoten im Ansichtsmodus `full` dar; das Pagelayout rahmt dann das Ergebnis.

## Inhalt

1. [Was das Template erhält](#was-das-template-erhält)
2. [Ein vollständiges Beispiel](#ein-vollständiges-beispiel)
3. [Kinder und Blättern](#kinder-und-blättern)
4. [Mit dem Pagelayout sprechen](#mit-dem-pagelayout-sprechen)
5. [Regeln für das Caching](#regeln-für-das-caching)
6. [Eine Vollansicht pro Klasse](#eine-vollansicht-pro-klasse)

## Was das Template erhält

Aus `kernel/classes/eznodeviewfunctions.php`: `$node`, `$viewmode`, `$language_code`, `$view_parameters` (die Teile
`(name)/value` der URL, zum Beispiel `offset`), `$node_path`, `$collection_attributes` und `$validation` (für Formulare des
Information Collectors) und `$persistent_variable`.

## Ein vollständiges Beispiel

```tpl
{* override/templates/full/article.tpl *}
<article class="article"{include uri='design:parts/exp_content_direction.tpl' exp_locale_code=$language_code}>
    <header>
        <h1>{$node.name|wash}</h1>
        <p class="article-meta">
            <time datetime="{$node.object.published|datetime( 'custom', '%Y-%m-%d' )}">{$node.object.published|l10n( 'shortdate' )}</time>
        </p>
    </header>
    {if $node.data_map.intro.has_content}
        <div class="article-intro">{attribute_view_gui attribute=$node.data_map.intro}</div>
    {/if}
    {if $node.data_map.image.has_content}
        <figure>{attribute_view_gui attribute=$node.data_map.image image_class='large'}</figure>
    {/if}
    {attribute_view_gui attribute=$node.data_map.body}
</article>
```

(Prüfen Sie die Attributbezeichner Ihrer Klassen. `parts/exp_content_direction.tpl` ist der Teil des Kernels, der aus dem
Locale-Code in `exp_locale_code` ` lang="..." dir="rtl"` für Inhalte von rechts nach links ausgibt und sonst nichts; siehe
[Rechts-nach-links-Designs](../theme-design/rechts-nach-links-designs.md).)

## Kinder und Blättern

```tpl
{def $limit = 10
     $offset = first_set( $view_parameters.offset, 0 )|int
     $children = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'sort_by', $node.sort_array,
                                                 'offset', $offset, 'limit', $limit ) )
     $total = fetch( 'content', 'list_count', hash( 'parent_node_id', $node.node_id ) )}
{foreach $children as $child}{node_view_gui content_node=$child view='line'}{/foreach}
{include uri='design:navigator/google.tpl' page_uri=$node.url_alias item_count=$total view_parameters=$view_parameters item_limit=$limit}
```

([Seitenumbruch](seitenumbruch.md)). `offset` ist Teil des Schlüssels des View-Cache, sodass jede Seite der Liste getrennt
gecacht wird.

## Mit dem Pagelayout sprechen

Die Vollansicht läuft vor dem Pagelayout. Um ihm etwas mitzuteilen (die Seitenspalte ausblenden, eine Klasse für body
hinzufügen), setzen Sie die [persistent variable](die-persistent-variable.md); um ein Stylesheet nur hier zu verlangen,
`ezcss_require`.

## Regeln für das Caching

Die Vollansicht wird im View-Cache gespeichert (`CachedViewModes` enthält `full`):

- nichts Besucherspezifisches darin (Namen, Warenkörbe, „Sie sind angemeldet“);
- auf `currentdate()` beruhende Ausgabe ist bis zur nächsten Veröffentlichung eingefroren; setzen Sie bei Bedarf ein kurzes
  `cache_ttl` ([Caching für Designer](../theme-design/caching-fuer-designer.md)).

## Eine Vollansicht pro Klasse

Das Standard-`node/view/full.tpl` ist allgemein; geben Sie jeder Klasse mit Overrides ihre eigene
([Tutorial: eine Vollansicht überschreiben](../theme-design/tutorial-eine-vollansicht-ueberschreiben.md)).

## Verwandte Seiten

- [Ansichtsmodi](ansichtsmodi.md)
- [Module result und content info](module-result-und-content-info.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
