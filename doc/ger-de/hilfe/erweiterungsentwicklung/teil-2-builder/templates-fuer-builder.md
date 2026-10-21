---
title: Templates für Builder
audience: developer, designer, app-creator
category: extension-development
order: 80
summary: Die Template-Sprache in einem Kapitel - Ausgabe und Escaping, Variablen mit def, set und undef, Arrays und Hashes, if und foreach, Operatoren und Pipes, Includes, Kommentare - mit von der Engine dargestellten Beispielen.
keywords: Template-Sprache, tpl, Variablen, Bedingungen, Schleifen, Escaping, Operatoren, Kommentare, template language, def, set, undef, if, elseif, foreach, include, wash, array, hash, operators, pipe, comments, smarty-like
type: help
---
# Templates für Builder

Teil 2, Kapitel 8. Jede Seite, die Exponential sendet, wird von Templates gezeichnet: `.tpl`-Dateien in einem Design,
geschrieben in einer kleinen Sprache mit Variablen, Bedingungen, Schleifen, Operatoren und Includes. Sie sieht aus wie
Smarty, und wer schon HTML geschrieben hat, lernt sie an einem Nachmittag. Dieses Kapitel lehrt den Kern. Jedes
Beispiel auf dieser Seite wurde von der Template-Engine des Kernels dargestellt, bevor es aufgeschrieben wurde, und die
Ausgabe wird gezeigt.

## Ausgabe und Escaping

Alles in geschweiften Klammern ist Template-Code; alles andere wird gesendet, wie es ist.

```text
<h1>{$node.name|wash}</h1>
```

`{$node.name}` gibt eine Variable aus; `|wash` escaped sie für HTML. **Escapen Sie alles, was von einer Person kommen
kann**: Namen, Titel, Suchbegriffe, View-Parameter. Ein nicht escapter Wert ist eine Cross-Site-Scripting-Lücke. Vom
Kernel erzeugte Zahlen (`$node.node_id`) sind so, wie sie sind, sicher.

## Kommentare

```text
{* A comment: never sent to the browser *}
```

## Variablen

```text
{def $title = 'Hello'
     $items = array( 'one', 'two', 'three' )
     $person = hash( 'name', 'Ada', 'year', 1815 )}
<h1>{$title|wash}</h1>
{set $title = concat( $title, ', ', $person.name )}
<p>{$title|wash}</p>
{undef $title $items $person}
```

ergibt

```text
<h1>Hello</h1>
<p>Hello, Ada</p>
```

- `{def}` definiert neue Variablen (mehrere auf einmal), `{set}` ändert sie, `{undef}` entfernt sie.
- `array( ... )` erzeugt eine Liste, `hash( key, value, ... )` eine Zuordnung.
- `$person.name` und `$person['year']` lesen einen Schlüssel; die Punktform ist die übliche. Bei einem Knoten folgen
  `$node.name`, `$node.object`, `$node.data_map.title.content` den Attributen.

Entfernen Sie mit `{undef}` immer, was Sie in einem Template definiert haben, das oft eingebunden wird, damit Variablen
nicht in das nächste Include durchsickern.

## Bedingungen

```text
{if $items|count|gt( 2 )}
<p>More than two items.</p>
{elseif $items|count|eq( 0 )}
<p>No items.</p>
{else}
<p>A few items.</p>
{/if}
```

ergibt für die drei Elemente oben `<p>More than two items.</p>`. Vergleiche sind Operatoren: `eq`, `ne`, `gt`, `ge`,
`lt`, `le`; die Logik ist `and( a, b )`, `or( a, b )`, `not( a )`. Für eine Auswahl innerhalb eines Ausdrucks verwenden
Sie `cond()`:

```text
<p>{cond( $items|count|gt( 5 ), 'many', 'few' )}</p>
```

ergibt `<p>few</p>`.

## Schleifen

```text
<ul>
{foreach $items as $index => $item}
    <li>{$index|inc}. {$item|upfirst|wash}</li>
{/foreach}
</ul>
```

ergibt

```text
<ul>    <li>1. One</li>    <li>2. Two</li>    <li>3. Three</li></ul>
```

`{foreach}` nimmt außerdem `offset <n>`, `max <n>`, `reverse` und `sequence <array> as $var` (ohne `=` geschrieben);
`{delimiter}...{/delimiter}` darin gibt etwas zwischen den Elementen aus, und `{break}`, `{continue}`, `{skip}` steuern
die Schleife:

```text
[{foreach $items as $i offset 1 max 3}{$i}{delimiter},{/delimiter}{/foreach}]
[{foreach $items as $i sequence array( 'odd', 'even' ) as $class}{$class} {/foreach}]
```

ergibt `[b,c,d]` und `[odd even odd even odd ]` für die Elemente `a` bis `e`.

## Operatoren und Pipes

Ein Operator kann wie eine Funktion aufgerufen werden, `count( $items )`, oder mit einer Pipe, `$items|count`. Pipes
lassen sich verketten:

```text
{$items|count|gt( 2 )}
```

liest sich als „zähle die Elemente, dann vergleiche mit 2“. Die nützlichsten Operatoren sind in
[Die Template-Operatoren, die Builder am häufigsten nutzen](die-wichtigsten-template-operatoren-fuer-builder.md) gesammelt; jeder
eingebaute steht in [Eingebaute Template-Operatoren](../teil-8-referenz/referenz-der-template-operatoren.md).

## Inhalte abrufen

Templates lesen Inhalte mit `fetch()`:

```text
{def $children = fetch( 'content', 'list', hash( 'parent_node_id', 2,
                                                  'limit', 3,
                                                  'sort_by', array( 'published', false() ) ) )}
{foreach $children as $child}
- {$child.name|wash} {$child.url_alias|ezurl( 'no' )}
{/foreach}
```

gibt die drei neuesten Unterknoten des obersten Knotens aus, jeden mit seiner Adresse. Fetch hat ein eigenes Kapitel:
[Inhalte in Templates abrufen](fetch-fuer-builder.md).

## Includes

```text
{include uri='design:parts/teaser.tpl' node=$child}
```

`design:` bedeutet „finde diesen Pfad über die Design-Kette“ ([Die Design-Kette](die-design-kette.md)); die anderen
Parameter werden zu Variablen im eingebundenen Template. Templates, die oft eingebunden werden, gehören in einen Ordner
`parts/` Ihres Designs.

## Links und Bilder

```text
<a href={$node.url_alias|ezurl}>{$node.name|wash}</a>
<link rel="stylesheet" href={'stylesheets/site.css'|ezdesign} />
```

`ezurl` erzeugt einen Link innerhalb des aktuellen Siteaccess und gibt ihn **mit Anführungszeichen** aus; `ezurl( 'no' )`
ohne. `ezdesign` findet eine Datei des Designs. Schreiben Sie Adressen der Website nie von Hand: Sie brechen, wenn die
Website umzieht oder ein Siteaccess hinzukommt.

## Wo Templates liegen

| Template | Pfad in einem Design |
|---|---|
| Der Seitenrahmen | `templates/pagelayout.tpl` |
| Eine Inhaltsseite in voller Ansicht | `templates/node/view/full.tpl` oder ein Override |
| Ein Listeneintrag | `templates/node/view/line.tpl` oder ein Override |
| Ein Feld (Attribut) | `templates/content/datatype/view/<datatype>.tpl` |
| Die eigene Seite eines Moduls | `templates/<module>/<view>.tpl` |

## Regeln für gute Templates

- Escapen Sie die Ausgabe (`|wash`), es sei denn, Sie wissen, dass der Wert sicher ist.
- Kein Inline-`<script>` und kein `style=""`: Skripte und Styles gehören in Dateien des Designs und werden über die
  Einstellungen des Designs geladen (eine Content Security Policy verbietet Inline-Code in der Administration). Siehe
  [Theme-Assets, der Packer und CSP](../teil-6-theme-entwickler/theme-assets-der-packer-und-csp.md).
- Einmal abrufen, außerhalb von Schleifen.
- Halten Sie die Logik klein. Braucht ein Template eine Seite voller Logik, gehört die Logik in einen Operator oder
  eine Fetch-Funktion ([Wenn Einstellungen nicht reichen](wenn-einstellungen-nicht-reichen.md)).

## Verwandte Seiten

- Zurück: [.ini.append.php-Dateien und sichere Einstellungen](append-php-dateien-und-sicherheit.md)
- Weiter: [Die Design-Kette: wo ein Template gefunden wird](die-design-kette.md)
- [Templates und Design](../../../anleitungen/templates-und-design.md)
- [Eingebaute Template-Funktionen](../teil-8-referenz/referenz-der-template-funktionen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
