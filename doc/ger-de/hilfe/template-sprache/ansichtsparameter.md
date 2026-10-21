---
title: Ansichtsparameter
audience: developer, designer, app-creator
category: development
order: 320
summary: Der Teil /(name)/value einer URL - wie eZURI ihn zerlegt, wie eZModule::run() ihn den ungeordneten Parametern und UserParameters einer Ansicht zuordnet, was content/view daraus macht ($view_parameters, eine Variable pro Name, content_info.offset) und wie er den Schlüssel des Content-View-Cache ändert - auf alpha belegt.
keywords: Ansichtsparameter, Benutzerparameter, $view_parameters, unordered_params, UserParameters, (offset), offset, Seitenblättern, Paginierung, eZURI, Schlüssel des View-Cache, namefilter, view parameters, user parameters, pagination, view cache key
type: help
---
# Ansichtsparameter

Eine URL wie `/content/view/full/144/(offset)/3/(color)/red` trägt nach den eigenen Parametern der Ansicht Paare aus einem Namen in Klammern
und einem Wert. Sie heißen Benutzerparameter (in PHP) oder Ansichtsparameter (in Templates), und mit ihnen fragt eine Seite nach „Seite 2“
oder „nur die roten Einträge“, ohne Query-String. Diese Seite folgt ihnen von der URL bis zum Template.

## Inhalt

1. [Die URL zerlegen](#die-url-zerlegen)
2. [Von der URL zur Ansicht](#von-der-url-zur-ansicht)
3. [Was content/view daraus macht](#was-contentview-daraus-macht)
4. [Sie in einem Template nutzen](#sie-in-einem-template-nutzen)
5. [Links mit ihnen bauen](#links-mit-ihnen-bauen)
6. [Ansichtsparameter und der View-Cache](#ansichtsparameter-und-der-view-cache)
7. [Verwandte Seiten](#verwandte-seiten)

## Die URL zerlegen

`eZURI` (`lib/ezutils/classes/ezuri.php`) zerlegt die URI. Ein Element, das auf `^\([a-zA-Z0-9_]+\)` passt, beginnt einen Parameter; das
nächste Element ist sein Wert; weitere Elemente bis zum nächsten Parameter werden mit `/` an den Wert angehängt. Der Kernel übergibt das
Ergebnis, `$this->uri->userParameters()`, an das Modul (`ezpKernelWeb`). Ein nur lesendes CLI-Skript auf alpha zerlegte einige Beispiel-URIs
mit `new eZURI( $uri )`:

```text
content/view/full/96/(offset)/10
  elements:        content/view/full/96
  user parameters: {"offset":"10"}
content/view/full/96/(offset)/10/(color)/red
  elements:        content/view/full/96
  user parameters: {"offset":"10","color":"red"}
content/view/full/96/(tag)/strength/training
  elements:        content/view/full/96
  user parameters: {"tag":"strength\/training"}
content/view/full/96/(empty)
  elements:        content/view/full/96
  user parameters: {"empty":""}
content/view/full/96/(bad-name)/x
  elements:        content/view/full/96/(bad-name)/x
  user parameters: []
```

- Der Parameterteil wird aus dem Pfad entfernt, den das Modul sieht (`elements`).
- Ein Wert darf Schrägstriche enthalten: `(tag)/strength/training` ergibt `strength/training`.
- Ein Name ohne Wert ergibt eine leere Zeichenkette.
- Ein Name mit einem Zeichen außerhalb von `a-z`, `A-Z`, `0-9` und `_` (hier ein Bindestrich) ist überhaupt kein Parameter; er bleibt im Pfad,
  und die Ansicht sieht ihn als positionsabhängigen Parameter.

## Von der URL zur Ansicht

Eine Ansicht gibt in `module.php` an, welche Namen sie als **ungeordnete Parameter** versteht; `content/view` in `kernel/content/module.php`:

```php
$ViewList['view'] = array(
    'functions' => array( 'read' ),
    'default_navigation_part' => 'ezcontentnavigationpart',
    'script' => 'view.php',
    'params' => array( 'ViewMode', 'NodeID' ),
    'unordered_params' => array( 'language' => 'Language',
                                 'offset' => 'Offset',
                                 'year' => 'Year',
                                 'month' => 'Month',
                                 'day' => 'Day' ) );
```

`eZModule::run()` (`lib/ezutils/classes/ezmodule.php`) dann:

1. setzt jeden ungeordneten Parameter auf `false` oder auf den Wert, der im **Pfad** auf seinen Namen folgt: Ein ungeordneter Name funktioniert
   auch ohne Klammern, wie in `/content/view/full/96/offset/20`;
2. kopiert jeden Benutzerparameter (die in Klammern) nach `$Params['UserParameters']` und, wenn sein Name ein ungeordneter Parameter ist, auch
   in diese Variable (`(offset)/10` setzt `$Params['Offset']`).

## Was content/view daraus macht

`content/view` (`kernel/private/classes/views/content/view.php`) wandelt `Offset`, `Year`, `Month` und `Day` in ganze Zahlen um und baut dann
die Ansichtsparameter:

```php
$viewParameters = array(
    'offset' => $Offset,
    'year' => $Year,
    'month' => $Month,
    'day' => $Day,
    'namefilter' => false,
    '_custom' => $UserParameters
);
$viewParameters = array_merge( $viewParameters, $UserParameters );
```

Das nur lesende Ausführen der Ansicht auf alpha (wie auf [Templates von Modulansichten](templates-von-modulansichten.md)) zeigte die Wirkung. Mit den
Benutzerparametern `offset=10` und `color=red`:

```text

content/view/full/96 user parameters {"offset":"10","color":"red"}
exit status: 1 (STATUS_OK)
$Result: array(11)
  content: string (7085 bytes)
  view_parameters: array(6)
    offset: '10'
    year: false
    month: false
    day: false
    namefilter: false
    color: 'red'
...
  content_info: array(25)
    ...
    offset: 10
```

und stattdessen mit `/offset/20` im Pfad:

```text
content/view/full/96/offset/20
$Result: array(11)
  content: string (7085 bytes)
  view_parameters: array(5)
    offset: 20
...
  content_info: array(25)
    ...
    offset: 20
```

Aus dem `array_merge()` folgen zwei Dinge:

- Jeder Benutzerparameter steht in `$view_parameters`, gleich welchen Namens (`color` oben), und ein Benutzerparameter überschreibt den
  umgewandelten Wert: `(offset)/10` lässt die **Zeichenkette** `'10'` in `$view_parameters.offset`, während `content_info.offset` die ganze Zahl
  hält. Wandeln Sie in Templates mit `|int` um.
- `_custom` wird vor dem Rendern wieder entfernt, und jeder seiner Einträge wird zu einer eigenen Template-Variablen
  (`eZNodeviewfunctions::generateNodeViewData()`), sodass das Knoten-Template auch `$color` hat.

## Sie in einem Template nutzen

Durch die Kinder eines Knotens blättern, wobei der Hash, den `content/view` für `/content/view/full/144/(offset)/3/(color)/red` baut, von Hand
definiert ist (in einem echten Knoten-Template setzt ihn der Kernel):

```tpl
{* the hash content/view sets for /content/view/full/144/(offset)/3/(color)/red *}
{def $view_parameters = hash( 'offset', '3', 'year', false(), 'month', false(), 'day', false(), 'namefilter', false(), 'color', 'red' )
     $node   = fetch( 'content', 'node', hash( 'node_id', 144 ) )
     $limit  = 3
     $offset = first_set( $view_parameters.offset, 0 )|int
     $total  = fetch( 'content', 'list_count', hash( 'parent_node_id', $node.node_id ) )}
{$node.name|wash}: {$total} children, page {sum( div( $offset, $limit )|floor, 1 )} of {div( $total, $limit )|ceil}

{foreach fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'offset', $offset, 'limit', $limit, 'sort_by', $node.sort_array ) ) as $child}
- {$child.name|wash}

{/foreach}
color: {first_set( $view_parameters.color, 'none' )}
```

Ausgabe auf alpha:

```text
Recipes: 18 children, page 2 of 6
- Low Carb Almond Breakfast Muffins
- Overnight Oatmeal
- Hearty Vegetable Soup
color: red
```

`first_set()` liefert einen Standardwert, wenn der Parameter fehlt; `|int` macht aus der Zeichenkette eine Zahl. Die Seite für Designer
[Seitenblättern](../designer/vorlagen/seitenumbruch.md) zeigt das Navigator-Template, das die Seitenlinks baut.

## Links mit ihnen bauen

Hängen Sie die Paare vor `ezurl` an den URL-Alias des Knotens an:

```tpl
<a href={concat( $node.url_alias, '/(offset)/', sum( $offset, $limit ) )|ezurl}>Next</a>
```

Nicht auf alpha ausgeführt: Ein Link zeigt seinen Nutzen erst in einem Browser; die Seite des Zerlegens ist oben belegt. Nutzen Sie nur Namen
aus Buchstaben, Ziffern und Unterstrichen, sonst erkennt `eZURI` sie nicht als Parameter.

## Ansichtsparameter und der View-Cache

`eZNodeviewfunctions::generateViewCacheFile()` fügt die Ansichtsparameter dem Cache-Schlüssel des Content-View-Cache hinzu: Für jeden Parameter,
nach Namen sortiert und ohne leere Namen und `_custom`, hängt es `vp:<name>=<value>` an. Jede eigene Kombination bekommt also eine eigene
Cache-Datei; das lässt das Blättern mit `(offset)` bei eingeschaltetem View-Cache funktionieren und bedeutet auch, dass ein Crawler, der
Parameter erfindet, Cache-Dateien anlegt. Die Anpassung `ignore_viewparameters` in `site.ini [ContentSettings] ViewCacheTweaks[<node id>]` oder
`ViewCacheTweaks[global]` lässt sie aus dem Schlüssel heraus, für Knoten, deren Templates sie nie lesen.

## Verwandte Seiten

- [Templates von Modulansichten](templates-von-modulansichten.md)
- [Referenz des Modulergebnisses](referenz-des-modulergebnisses.md)
- [Seitenblättern](../designer/vorlagen/seitenumbruch.md)
- [Template-Caches](template-caches.md)
- [Referenz der Moduldefinition](../ersteller/anwendungsentwickler/referenz-der-moduldefinition.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
