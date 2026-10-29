---
title: Smarty und die Template-Sprache nebeneinander
audience: developer, designer, app-creator
category: templates
order: 30
summary: Eine Tabelle, die die Syntax von Smarty und die Template-Sprache von Exponential nebeneinanderstellt - Ausgabe, Modifikatoren und Operatoren, Zuweisung, Standardwerte, Bedingungen, Schleifen, Wechsel, include, literal, Begrenzer, Kommentare, capture, Maskierung, Daten, Caching und Vererbung - mit jedem Beispiel der Template-Sprache auf einer Entwicklungsinstallation gerendert.
keywords: Smarty, Vergleich, nebeneinander, Syntax, Modifikatoren, Operatoren, Zuweisung, assign, def, capture, set-block, cycle, sequence, include, literal, ldelim, escape, wash, date_format, datetime, comparison, side by side, modifiers
type: help
---
# Smarty und die Template-Sprache nebeneinander

Wenn Sie Smarty kennen, können Sie die meisten Templates von Exponential sofort lesen: Die geschweiften Klammern, das `$`, die Pipe und die
Block-Tags folgen derselben Idee. Die Einzelheiten unterscheiden sich, und an den Einzelheiten scheitert ein Template. Diese Seite stellt beide
nebeneinander. Die Spalte zu Smarty dient nur dem Vergleich und wurde hier nicht ausgeführt; jedes Beispiel der Template-Sprache wurde auf
einer Entwicklungsinstallation gerendert.

## Inhalt

1. [Die Tabelle](#die-tabelle)
2. [Ausgabe und Modifikatoren](#ausgabe-und-modifikatoren)
3. [Zuweisung und Standardwerte](#zuweisung-und-standardwerte)
4. [Bedingungen und Schleifen](#bedingungen-und-schleifen)
5. [Include, literal, Begrenzer und Kommentare](#include-literal-begrenzer-und-kommentare)
6. [Capture](#capture)
7. [Daten und Anzahlen](#daten-und-anzahlen)
8. [Verwandte Seiten](#verwandte-seiten)

## Die Tabelle

| Aufgabe | Smarty, zum Vergleich (hier nicht ausgeführt) | Template-Sprache |
|---|---|---|
| Eine Variable ausgeben | `{$title}` | `{$title}` |
| Array-Schlüssel / Objekteigenschaft | `{$user.name}`, `{$user->name}` | `{$user.name}` für beides (siehe [Zugriff auf Attribute](zugriff-auf-attribute.md)) |
| Modifikator / Operator | `{$title\|upper}` | `{$title\|upcase}` |
| Modifikator mit Argumenten | `{$title\|truncate:16}` | `{$title\|shorten( 16 )}` |
| Verkettung | `{$title\|trim\|upper}` | `{$title\|trim\|upcase}` |
| Aufruf in Funktionsform | `{counter}`, Plugins | `{sum( 1, 2 )}`: Jeder Operator lässt sich auch aufrufen |
| Arithmetik | `{$a + 1}` | `{$a\|sum( 1 )}` oder `{sum( $a, 1 )}`: keine Operatoren in Infixschreibweise |
| Vergleich | `{if $a > 1}` | `{if $a\|gt( 1 )}` |
| Logik | `{if $a && !$b}` | `{if and( $a, not( $b ) )}` |
| Zuweisung | `{assign var=x value=1}`, `{$x = 1}` | `{def $x = 1}` zum Anlegen, `{set $x = 2}` zum Ändern |
| Standardwert | `{$nick\|default:'none'}` | `{first_set( $nick, 'none' )}` |
| Ternärer Ausdruck | `{($a) ? 'y' : 'n'}` | `{cond( $a, 'y', 'n' )}` |
| If | `{if}{elseif}{else}{/if}` | `{if}{elseif}{else}{/if}` |
| Schleife mit Schlüssel | `{foreach $arr as $k => $v}` | `{foreach $arr as $k => $v}` |
| Schleife über nichts | `{foreachelse}` | vorher prüfen: `{if $arr\|count}` |
| Abwechselnde Zeilenklassen | `{cycle values="odd,even"}` | `sequence array( 'odd', 'even' ) as $row` an der Schleife |
| Zahlenbereich | `{for $i=1 to 3}` | `{for 1 to 3 as $i}` |
| Include | `{include file='part.tpl' who='a'}` | `{include uri='design:part.tpl' who='a'}` |
| Wörtlicher Text mit Klammern | `{literal}...{/literal}` | `{literal}...{/literal}` |
| Eine einzelne Klammer | `{ldelim}`, `{rdelim}` | `{ldelim}`, `{rdelim}` |
| Kommentar | `{* ... *}` | `{* ... *}` |
| Ausgabe auffangen | `{capture name=x}...{/capture}` | `{set-block variable=x}...{/set-block}` |
| Für HTML maskieren | `{$s\|escape}` (oder automatische Maskierung, wenn eingeschaltet) | `{$s\|wash}`: nie automatisch |
| Ein Datum formatieren | `{$t\|date_format:'%Y-%m-%d'}` | `{$t\|datetime( 'custom', '%Y-%m-%d' )}` |
| Anzahl | `{$arr\|count}` | `{$arr\|count}` |
| Rohes PHP | `{php}` | keines: Logik gehört in eine Operatorklasse |
| Einen Teil der Seite cachen | `{nocache}`, Caching pro Template | `{cache-block keys=...}...{/cache-block}` |
| Ein Layout wiederverwenden | `{extends}` und `{block}` | das Pagelayout plus Overrides; `$module_result.content` ist der Rumpf der Seite |

## Ausgabe und Modifikatoren

Operatoren treten an die Stelle von Modifikatoren. Sie werden mit Klammern und durch Kommas getrennten Argumenten aufgerufen, nicht mit
Doppelpunkten:

```tpl
{def $title = '  The Exponential template language  '}
[{$title}]
[{$title|trim|upcase}]
[{$title|trim|shorten( 16 )}]
[{$title|trim|count_chars}]
[{'<b>bold</b> & more'|wash}]
```

Ausgabe:

```text
[  The Exponential template language  ]
[THE EXPONENTIAL TEMPLATE LANGUAGE]
[The Exponenti...]
[33]
[&lt;b&gt;bold&lt;/b&gt; &amp; more]
```

`shorten( 16 )` zählt die drei angehängten Punkte zu den 16 Zeichen. `count_chars` ist die Länge einer Zeichenkette.

## Zuweisung und Standardwerte

`{def}` legt eine Variable an und `{set}` ändert eine vorhandene; `assign` von Smarty tut beides. Ein Standardwert kommt von `first_set`, dem
ersten gesetzten Parameter; `cond` ist der ternäre Ausdruck der Template-Sprache:

```tpl
{def $name = 'Ada' $visits = 3}
{set $visits = $visits|inc}
{$name} has visited {$visits} times.
Nickname: {first_set( $nickname, 'none given' )}
Greeting: {cond( $visits|gt( 3 ), 'welcome back', 'hello' )}
```

Ausgabe:

```text
Ada has visited 4 times.
Nickname: none given
Greeting: welcome back
```

## Bedingungen und Schleifen

`if` und `foreach` lesen sich fast gleich. Das Durchwechseln von Werten ist ein Parameter der Schleife (`sequence`), und ein Bereich wird
`for <erster> to <letzter> as $var` geschrieben:

```tpl
{def $count = 2}
{if $count|eq( 0 )}none{elseif $count|eq( 1 )}one{else}{$count} items{/if}
{foreach hash( 'a', 'apple', 'b', 'banana', 'c', 'cherry' ) as $key => $fruit sequence array( 'odd', 'even' ) as $row}
<li class="{$row}">{$key}: {$fruit}</li>
{/foreach}
{for 1 to 3 as $i}[{$i}]{/for}
```

Ausgabe:

```text
2 items<li class="odd">a: apple</li><li class="even">b: banana</li><li class="odd">c: cherry</li>[1][2][3]
```

Die Zeilenumbrüche um jedes Funktions-Tag sind aus der Ausgabe verschwunden: Der Parser entfernt einen Zeilenumbruch vor und einen nach jedem
Funktions-Tag (siehe [Lexikalische Struktur](lexikalische-struktur.md#leerraum-um-tags)). Smarty behält sie.

## Include, literal, Begrenzer und Kommentare

`include` nimmt eine `uri`, meist `design:...`, die die Design-Ressource über die Design-Basen und das Override-System auflöst. Dieses Beispiel
bindet eine kleine eigene Datei mit der Ressource `file:` ein, sodass es außerhalb eines Designs läuft; die eingebundene Datei ist:

```tpl
<p>Hello, {$who|wash}!</p>
```

und das Template, das sie einbindet, mit einem literal-Block, den beiden Begrenzerfunktionen und einem Kommentar:

```tpl
{include uri='file:var/tmp/examples/tl-smarty/parts/greeting.tpl' who='designer'}
{* a comment is never output *}
{literal}<style>.box { color: red; }</style>{/literal}
{ldelim}not a tag{rdelim}
```

Ausgabe:

```text
<p>Hello, designer!</p>

<style>.box { color: red; }</style>{not a tag}
```

## Capture

`set-block` rendert seinen Inhalt in eine Variable, wie `capture` von Smarty. Das Ergebnis ist eine gewöhnliche Zeichenkette, mit der
Operatoren arbeiten können:

```tpl
{set-block variable=badge}<span class="badge">{'new'|upcase}</span>{/set-block}
Captured {$badge|count_chars} characters: {$badge}
```

Ausgabe:

```text
Captured 30 characters: <span class="badge">NEW</span>
```

## Daten und Anzahlen

Daten sind Zeitstempel; `datetime( 'custom', <format> )` formatiert sie mit den Platzhaltern `%` des Locale-Codes, und `l10n( 'shortdate' )`
nutzt das kurze Datumsformat der aktuellen Locale:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 90 ) )}
Published: {$node.object.published|datetime( 'custom', '%Y-%m-%d' )}
Short date: {$node.object.published|l10n( 'shortdate' )}
Fields: {$node.data_map|count}
```

Ausgabe:

```text
Published: 2023-01-25
Short date: 01/25/2023
Fields: 18
```

## Verwandte Seiten

- Zurück: [Geschichte der Template-Sprache](geschichte-der-template-sprache.md)
- Weiter: [Smarty und die Template-Sprache: die Unterschiede im Detail](smarty-unterschiede-im-detail.md)
- [Ein Template von Smarty umziehen](ein-template-von-smarty-umziehen.md)
- [Grundlagen der TPL-Syntax](../designer/vorlagen/tpl-syntax-grundlagen.md)
- [Set-block und append-block](../designer/vorlagen/set-block-und-append-block.md)
- [Wash und Maskierung](../designer/vorlagen/wash-und-maskierung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
