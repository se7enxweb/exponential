---
title: Ein Template von Smarty umziehen
audience: developer, designer, app-creator
category: templates
order: 50
summary: Eine Anleitung zum Portieren - ein typisches Listen-Template aus Smarty in drei Schritten in die Template-Sprache übertragen (Variablen, Daten aus fetch, die Schleife mit Bedingungen, Links, Daten und Maskierung), jeder Schritt auf alpha gerendert, plus eine Checkliste der vorzunehmenden Ersetzungen.
keywords: Smarty, portieren, migrieren, umwandeln, übertragen, Anleitung zum Portieren, Checkliste, fetch, foreach, sequence, cycle, ezurl, wash, datetime, port, migrate, convert, porting guide, checklist
type: help
---
# Ein Template von Smarty umziehen

Diese Seite portiert ein kleines Template aus Smarty Schritt für Schritt in die Template-Sprache und endet mit einer Checkliste für Ihre
eigenen Templates. Jeder Schritt wurde auf alpha gegen die Startseite „Fit & Healthy“ (Knoten 89) gerendert.

## Inhalt

1. [Das Template aus Smarty](#das-template-aus-smarty)
2. [Schritt 1: Variablen](#schritt-1-variablen)
3. [Schritt 2: Die Daten kommen aus fetch](#schritt-2-die-daten-kommen-aus-fetch)
4. [Schritt 3: Schleife, Links, Daten und Maskierung](#schritt-3-schleife-links-daten-und-maskierung)
5. [Checkliste](#checkliste)
6. [Verwandte Seiten](#verwandte-seiten)

## Das Template aus Smarty

Eine Liste der neuesten Seiten einer Sektion, wie sie in Smarty aussehen könnte. Das PHP, das sie rendert, weist `$site` und `$pages` zu. Das
ist Smarty, zum Vergleich; es wurde hier nicht ausgeführt:

```smarty
{assign var=limit value=3}
<h2>{$site.name|escape}</h2>
{if $pages|@count == 0}
<p>No pages yet.</p>
{else}
<ul>
{foreach from=$pages item=page}
  <li class="{cycle values='odd,even'}"><a href="{$page.url}">{$page.name|truncate:30|escape}</a>
      {$page.published|date_format:'%Y-%m-%d'}</li>
{/foreach}
</ul>
{/if}
```

## Schritt 1: Variablen

Aus `{assign}` wird `{def}`. Mehrere Variablen lassen sich in einem Tag definieren, über mehrere Zeilen. Statt `$site` vom PHP zu bekommen,
holt das Template den Knoten selbst. Aus `escape` wird `wash`:

```tpl
{def $site = fetch( 'content', 'node', hash( 'node_id', 89 ) )
     $limit = 3}
<h2>{$site.name|wash}</h2>
```

Ausgabe auf alpha:

```text
<h2>Fit &amp; Healthy</h2>
```

## Schritt 2: Die Daten kommen aus fetch

Die Liste ist ein Fetch `content/list` mit dem Elternknoten, dem Limit und der Sortierung. Die Prüfung der Anzahl nutzt den Operator `count`;
es gibt kein `@` und kein `==`:

```tpl
{def $site = fetch( 'content', 'node', hash( 'node_id', 89 ) )
     $limit = 3
     $pages = fetch( 'content', 'list', hash( 'parent_node_id', $site.node_id,
                                              'limit', $limit,
                                              'sort_by', array( 'published', false() ) ) )}
<h2>{$site.name|wash}</h2>
<p>{$pages|count} of {$site.children_count} pages</p>
```

Ausgabe auf alpha:

```text
<h2>Fit &amp; Healthy</h2>
<p>3 of 16 pages</p>
```

## Schritt 3: Schleife, Links, Daten und Maskierung

Das fertige Template. Was sich geändert hat:

- Aus `{if $pages|@count == 0}` wird `{if $pages|count|eq( 0 )}`.
- Aus `{foreach from=$pages item=page}` wird `{foreach $pages as $page}`.
- Aus `{cycle values='odd,even'}` wird der Schleifenparameter `sequence array( 'odd', 'even' ) as $row`.
- Ein Link ist der `url_alias` des Knotens, durch `ezurl` geleitet, das den Pfad des Siteaccess und die Anführungszeichen hinzufügt:
  `href={$page.url_alias|ezurl}`.
- Aus `truncate:30` wird `shorten( 30 )`, und aus `escape` wird `wash` (nach dem Kürzen, damit keine Entität abgeschnitten wird).
- Aus `date_format` wird `datetime( 'custom', ... )` auf dem Zeitstempel `published` des Objekts.
- `{undef}` am Ende entfernt die Variablen; in einem Template einer vollständigen Ansicht werden mit `{def}` angelegte Variablen am Ende der
  Datei ohnehin entfernt.

```tpl
{def $site = fetch( 'content', 'node', hash( 'node_id', 89 ) )
     $limit = 3
     $pages = fetch( 'content', 'list', hash( 'parent_node_id', $site.node_id,
                                              'limit', $limit,
                                              'sort_by', array( 'published', false() ) ) )}
<h2>{$site.name|wash}</h2>
{if $pages|count|eq( 0 )}
<p>No pages yet.</p>
{else}
<ul>
{foreach $pages as $page sequence array( 'odd', 'even' ) as $row}
  <li class="{$row}"><a href={$page.url_alias|ezurl}>{$page.name|shorten( 30 )|wash}</a>
      {$page.object.published|datetime( 'custom', '%Y-%m-%d' )}</li>
{/foreach}
</ul>
{/if}
{undef $site $limit $pages}
```

Ausgabe auf alpha:

```text
<h2>Fit &amp; Healthy</h2><ul>  <li class="odd"><a href="/slash-quotes">Slash Quotes</a>
      2026-10-17</li>  <li class="even"><a href="/exponential-live">Tour</a>
      2026-10-05</li>  <li class="odd"><a href="/fit-healthy-site-info">Fit &amp; Healthy site info - [...</a>
      2026-08-31</li></ul>
```

Die Liste zeigt, was auf alpha zuletzt veröffentlicht war, als diese Seite entstand; auf Ihrer Website werden die Namen also andere sein.

## Checkliste

| Smarty | Template-Sprache |
|---|---|
| `{assign var=x value=...}`, `{$x = ...}` | `{def $x = ...}`, später `{set $x = ...}` |
| `{$a + $b}`, `{$a * 2}` | `{sum( $a, $b )}`, `{$a\|mul( 2 )}` |
| `==`, `!=`, `>`, `<`, `>=`, `<=` | `eq`, `ne`, `gt`, `lt`, `ge`, `le` |
| `&&`, `\|\|`, `!` | `and( ... )`, `or( ... )`, `not( ... )` |
| `\|default:'x'` | `first_set( $var, 'x' )` |
| `\|escape`, `\|escape:'url'` | `\|wash`, `\|rawurlencode` |
| `\|truncate:n` | `\|shorten( n )` |
| `\|upper`, `\|lower`, `\|capitalize` | `\|upcase`, `\|downcase`, `\|upword` |
| `\|date_format:'...'` | `\|datetime( 'custom', '...' )` oder `\|l10n( 'shortdate' )` |
| `\|@count` | `\|count` |
| `{foreach from=$a item=i key=k}` | `{foreach $a as $k => $i}` |
| `{foreachelse}` | `{if $a\|count}...{else}...{/if}` um die Schleife |
| `{cycle values='a,b'}` | `sequence array( 'a', 'b' ) as $s` |
| `{section name=i loop=$a}` | `{foreach}`, oder `{for 0 to ... as $i}` für Bereiche |
| `{include file='x.tpl'}` | `{include uri='design:x.tpl'}` |
| `{capture name=x}` | `{set-block variable=x}` |
| `{extends}`, `{block}` | Overrides und das Pagelayout |
| `{php}` | ein Operator oder eine Fetch-Funktion in einer Erweiterung |
| `{$smarty.get.x}` | `ezhttp( 'x', 'get' )`, vor der Ausgabe gewaschen |

Rendern Sie das Template nach dem Portieren mit eingeschalteter Debug-Ausgabe und lesen Sie die Warnungen: Ein Operator, der nicht existiert,
und ein `set` auf eine nie definierte Variable scheitern beide stillschweigend in der Seite (siehe
[Smarty und die Template-Sprache: die Unterschiede im Detail](smarty-unterschiede-im-detail.md)).

## Verwandte Seiten

- Zurück: [Smarty und die Template-Sprache: die Unterschiede im Detail](smarty-unterschiede-im-detail.md)
- Weiter: [Lexikalische Struktur](lexikalische-struktur.md)
- [Knoten und Listen holen](../../../eng-us/help/designers/templates/fetching-nodes-and-lists.md)
- [URL-Operatoren](../../../eng-us/help/designers/templates/url-operators.md)
- [Daten, Zahlen und Lokalisierung](../../../eng-us/help/designers/templates/dates-numbers-and-l10n.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
