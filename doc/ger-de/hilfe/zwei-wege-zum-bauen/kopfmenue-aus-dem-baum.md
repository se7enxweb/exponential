---
title: Ein Kopfmenü, das dem Baum folgt
audience: designer, developer, website-creator
category: build-two-ways
order: 510
summary: Der Weg über Templates zu einem dynamischen Kopfmenü; main_menu.tpl in Ihrer Design-Erweiterung überschreiben, damit das Menü die Kinder der Wurzel der Site nach Klasse listet, dann einen Ordner veröffentlichen und zusehen, wie er erscheint. Getesteter Code, Einstellungen und Bildschirmfotos.
keywords: dynamisches Menü, Kopfmenü, Inhaltsbaum, main_menu.tpl, Template-Override, fetch content list, Klassenfilter, RootNode, mysite, Ordner veröffentlichen, Menü folgt dem Baum, dynamic menu, header menu, content tree, template override, class filter, publish folder, menu follows tree
type: help
---
# Ein Kopfmenü, das dem Baum folgt

Das Kopfmenü von Fit & Healthy ist eine feste Liste in der `menu.ini` ([Einen Eintrag zum Kopfmenü hinzufügen](../../../eng-us/help/build-two-ways/add-a-header-menu-item.md)).
Wenn Redakteure Bereiche einfach durch Veröffentlichen zum Menü hinzufügen sollen, muss das Menü-Template seine Einträge aus
dem Baum holen. Diese Seite macht das in einer Design-Erweiterung, `mysite` ([Ihre eigene Design-Erweiterung](eigene-design-erweiterung.md)).

## Inhalt

1. [Die Einstellungen](#die-einstellungen)
2. [Das Template](#das-template)
3. [Aktivieren und leeren](#aktivieren-und-leeren)
4. [Einen Ordner veröffentlichen und im Menü sehen](#einen-ordner-veröffentlichen-und-im-menü-sehen)
5. [Was zu entscheiden ist](#was-zu-entscheiden-ist)

## Die Einstellungen

`extension/mysite/settings/menu.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[MySiteMenu]
# Classes of the children of the root node that become header menu items
ClassFilter[]
ClassFilter[]=ng_landing_page
ClassFilter[]=ng_category
ClassFilter[]=ng_topic
ClassFilter[]=folder
# At most this many items
Limit=12

*/ ?>
```

## Das Template

`extension/mysite/design/media/templates/pagelayout/header/main_menu.tpl` (derselbe Pfad wie der des Themes, es ersetzt
es also). Es behält das Markup und die Klassen des Themes, sodass CSS und Skript des Themes weiterhin greifen:

```smarty
{def $mm_root = ezini( 'NodeSettings', 'RootNode', 'content.ini' )
     $mm_root_node = fetch( 'content', 'node', hash( 'node_id', $mm_root ) )
     $mm_items = array()
     $mm_current_node = false()}
{if is_object( $mm_root_node )}
    {set $mm_items = fetch( 'content', 'list', hash( 'parent_node_id', $mm_root,
                                                      'sort_by', $mm_root_node.sort_array,
                                                      'class_filter_type', 'include',
                                                      'class_filter_array', ezini( 'MySiteMenu', 'ClassFilter', 'menu.ini' ),
                                                      'limit', ezini( 'MySiteMenu', 'Limit', 'menu.ini' ) ) )}
{/if}
{if and( is_set( $node ), is_object( $node ) )}
    {set $mm_current_node = $node}
{elseif and( is_set( $module_result.content_info.node_id ), $module_result.content_info.node_id|gt( 0 ) )}
    {set $mm_current_node = fetch( 'content', 'node', hash( 'node_id', $module_result.content_info.node_id ) )}
{/if}
<nav class="main-navigation" id="mainNavigation" role="navigation">
{if $mm_items|count|gt( 0 )}
    <ul class="nav navbar-nav">
    {foreach $mm_items as $mm_index => $mm_item}
        <li id="menu-item-main_menu-location-id-{$mm_item.node_id}"{if and( $mm_current_node, $mm_current_node.path_array|contains( $mm_item.node_id ) )} class="active"{/if} data-location-id="{$mm_item.node_id}">
            <a href={$mm_item.url_alias|ezurl}>{$mm_item.name|wash}</a>
        </li>
    {/foreach}
    </ul>
{/if}
</nav>
{undef $mm_root $mm_root_node $mm_items $mm_current_node}
```

`RootNode` des Siteaccess `site` ist 89 (Fit & Healthy), das Menü listet also dessen Kinder. Die Liste ist so sortiert,
wie der Wurzelknoten seine Kinder in der Administration sortiert.

## Aktivieren und leeren

`mysite` muss in `ActiveExtensions[]` vor `sevenx_themes_media` stehen (siehe
[Ihre eigene Design-Erweiterung](eigene-design-erweiterung.md)). Dann:

```text
./console exp:cache clear --tag=template,content,ini
```

Auf der Testkopie zeigte die Kopfzeile danach die Kinder von Fit & Healthy mit diesen Klassen:

![Die Kopfzeile mit dem aus dem Baum gebauten Menü: Fitness, Healthy eating, Recipes, Running, Video, Health, HIIT, Showcase, Workout, Tour, Slash Quotes](../../../eng-us/help/build-two-ways/images/tt-01-dynamic-menu-before-folder.webp)

## Einen Ordner veröffentlichen und im Menü sehen

Öffnen Sie in der **Inhaltsstruktur** **Fit & Healthy**, **Neu erstellen > Folder**, nennen Sie ihn *Events* und klicken
Sie auf **Zur Veröffentlichung schicken**.

![Das Bearbeitungsformular eines neuen Ordners namens Events](../../../eng-us/help/build-two-ways/images/tt-02-new-folder-form.webp)

Der nächste Seitenaufruf hatte den neuen Eintrag am Ende, ohne dass ein Cache von Hand geleert wurde:

![Die Kopfzeile mit dem neuen Eintrag Events am Ende](../../../eng-us/help/build-two-ways/images/tt-03-dynamic-menu-with-events.webp)

## Was zu entscheiden ist

- **Länge.** Zwölf Einträge drängten auf der Testkopie das Logo aus der Kopfzeile: Ein Menü, das mit dem Baum wächst,
  braucht ein `Limit`, das das Design aufnehmen kann, oder einen engeren `ClassFilter`.
- **Reihenfolge.** Sie folgt der Sortierung des Wurzelknotens; stellen Sie sie auf Priorität und geben Sie den Bereichen
  Prioritäten, um sie zu steuern.
- **Ausnahmen.** Seiten, die existieren, aber nicht ins Menü sollen, brauchen eine Klasse, die nicht in `ClassFilter[]`
  steht, oder einen weiteren Filter im Template (zum Beispiel ein Feld „im Menü zeigen“).
- **Beides.** Die beiden Wege lassen sich mischen: Behalten Sie `MainMenuID[]` für einige feste Einträge und fügen Sie die
  geholten im selben Template danach an.

## Verwandte Seiten

- [Einen Eintrag zum Kopfmenü hinzufügen](../../../eng-us/help/build-two-ways/add-a-header-menu-item.md)
- [Das Kopfmenü und der Inhaltsbaum](kopfmenue-und-inhaltsbaum.md)
- [Ihre eigene Design-Erweiterung](eigene-design-erweiterung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
