---
title: Foreach-Schleifen
audience: designer, developer
category: templates
order: 60
summary: Mit foreach über Arrays und Fetch-Ergebnisse laufen - Schlüssel und Einträge, offset, max und reverse, sequence für wechselnde Klassen, delimiter zwischen Einträgen, break, continue und skip und Schleifengewohnheiten, die Templates schnell halten.
keywords: foreach, Schleife, durchlaufen, as, Schlüssel und Wert, offset, max, reverse, sequence, delimiter, break, continue, skip, Zebrazeilen, Listenausgabe, loop, iterate, key value, zebra rows, list output
type: help
---
# Foreach-Schleifen

Diese Seite ist für Designer, die Listen ausgeben. `{foreach}` ist die Schleife, die Sie am häufigsten verwenden.

## Inhalt

1. [Syntax](#syntax)
2. [Schlüssel und Einträge](#schlüssel-und-einträge)
3. [Offset, max und reverse](#offset-max-und-reverse)
4. [Delimiter](#delimiter)
5. [Sequence](#sequence)
6. [break, continue, skip](#break-continue-skip)
7. [Gewohnheiten](#gewohnheiten)

## Syntax

Aus dem Syntaxkommentar von `lib/eztemplate/classes/eztemplateforeachfunction.php`:

```text
{foreach <array> as [$keyVar =>] $itemVar
         [sequence <array> as $sequenceVar]
         [offset <offset>]
         [max <max>]
         [reverse]}
    [{delimiter}...{/delimiter}]
    [{break}]
    [{continue}]
    [{skip}]
{/foreach}
```

## Schlüssel und Einträge

```tpl
{foreach $children as $child}
    <li>{$child.name|wash}</li>
{/foreach}

{foreach $settings as $name => $value}
    <dt>{$name|wash}</dt><dd>{$value|wash}</dd>
{/foreach}
```

## Offset, max und reverse

```tpl
{foreach $articles as $article offset 1 max 3}  {* items 2 to 4 *}
{foreach $steps as $step reverse}               {* last to first *}
```

Kommt die Liste aus einem Fetch, verwenden Sie lieber `offset` und `limit` im Fetch: Dann erledigt die Datenbank die Arbeit.

## Delimiter

Wird zwischen Einträgen ausgegeben, nicht nach dem letzten:

```tpl
{foreach $tags as $tag}{$tag|wash}{delimiter}, {/delimiter}{/foreach}
```

## Sequence

Wechselnde Werte, zum Beispiel Zeilenklassen:

```tpl
{foreach $rows as $row sequence array( 'odd', 'even' ) as $row_class}
    <tr class="{$row_class}">...</tr>
{/foreach}
```

(Für Zebratabellen ist eine CSS-Regel `:nth-child` meist einfacher.)

## break, continue, skip

- `{break}` verlässt die Schleife.
- `{continue}` geht zum nächsten Eintrag.
- `{skip}` geht zum nächsten Eintrag, ohne den Delimiter auszugeben.

```tpl
{foreach $children as $child}
    {if $child.is_hidden}{skip}{/if}
    {node_view_gui content_node=$child view='line'}
{/foreach}
```

## Gewohnheiten

- Kein Fetch innerhalb einer Schleife: einmal vorher abrufen ([Fetch-Leistung](fetch-leistung.md)).
- Hilfsvariablen der Schleife danach mit `{undef}` entfernen, wenn das Template anderswo eingebunden wird.
- Einen leeren Zustand ausgeben, wenn die Liste leer ist: `{if $list|count}...{else}...{/if}`.

## Verwandte Seiten

- [For-Schleifen](for-schleifen.md)
- [While- und do-Schleifen](while-und-do-schleifen.md)
- [Knoten und Listen abrufen](knoten-und-listen-abrufen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
