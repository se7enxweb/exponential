---
title: Templates nebeneinander - Exponential-Templates, Twig und Smarty
audience: developer, designer, app-creator
category: templates
order: 35
summary: Ein Spickzettel, der dasselbe Template in die drei Engines stellt, die Exponential rendert - Exponential-Templates (.tpl), Twig (.twig) und Smarty (.stpl) - für Ausgabe, Attribute, Variablen, Schleifen, Bedingungen, Cache-Blöcke, fetch, Übersetzungen, URLs, Includes, Sections, Sequenzen und Maskierung, jede Zeile von den Konvertern des Kernels umgewandelt und mit gleicher Ausgabe gerendert, dazu, wie jeder Operator und jede Template-Funktion in Twig geschrieben wird.
keywords: Twig, Smarty, tpl, stpl, twig, Spickzettel, Rosetta-Stein, nebeneinander, Vergleich, Syntax, Operatoren, Filter, Funktionen, exp_-Präfix, fetch, i18n, ezurl, include, expinclude, cache-block, expcacheblock, section, sequence, wash, cheat sheet, side by side, comparison
type: help
---
# Templates nebeneinander: Exponential-Templates, Twig und Smarty

Exponential rendert drei Template-Sprachen im selben Template-System: seine eigene (`.tpl`), Twig 3 (`.twig`) und
Smarty 5 (`.stpl`). Diese Seite ist ein Spickzettel für Entwickler, die eine davon kennen und eine andere lesen oder
schreiben: dieselbe Aufgabe in allen dreien, nebeneinander.

Jedes Beispiel wurde als `.tpl` geschrieben, von den eigenen Konvertern des Kernels (`exp:template:convert`) nach `.stpl`
und `.twig` umgewandelt, und alle drei wurden über das Template-System in einer Entwicklungsinstallation mit denselben
Variablen gerendert (`list` = `['a', 'b', 'c']`, `user_name` = `'Ada'`, `price` = `12.5`, `html` = `'<b>bold</b>'`,
`items` = drei Hashes mit `name` und `price`, `node` = der Artikel „Why Track Your Workouts?“). In jeder Zeile mit einer
Ausgabe waren die drei Ausgaben gleich; Zeilen ohne Ausgabe zeigen Formen, die mehr Seite um sich herum brauchen (einen
Kindknoten, einen Layout-Block) und für diese Seite nicht gerendert wurden. Die Twig- und Smarty-Formen sind die Ausgabe
der Konverter, aufgeräumt, wo der Konverter zur Sicherheit Zeilenumbrüche schreibt; ein handgeschriebenes Template kann
die kürzeren nativen Formen aus den Hinweisen benutzen.

## Inhalt

1. [Ausgabe und Attribute](#ausgabe-und-attribute)
2. [Variablen](#variablen)
3. [Bedingungen](#bedingungen)
4. [Schleifen](#schleifen)
5. [Operatoren](#operatoren)
6. [Daten, Übersetzungen und URLs](#daten-übersetzungen-und-urls)
7. [Includes, Funktionen und Blöcke](#includes-funktionen-und-blöcke)
8. [Wörtlicher Text und Kommentare](#wörtlicher-text-und-kommentare)
9. [Jeder Operator und jede Funktion in Twig](#jeder-operator-und-jede-funktion-in-twig)
10. [Verwandte Seiten](#verwandte-seiten)

## Ausgabe und Attribute

| Aufgabe | `.tpl` | `.twig` | `.stpl` | Ausgabe |
|---|---|---|---|---|
| eine Variable drucken | `{$user_name}` | `{{ user_name }}` | `{$user_name}` | `Ada` |
| ein Attribut, maskiert | `{$node.name\|wash}` | `{{ node.name\|wash }}` | `{$node.name\|wash}` | `Why Track Your Workouts?` |
| der Inhalt eines Feldes | `{$node.data_map.title.content\|wash}` | `{{ node.data_map.title.content\|wash }}` | `{$node.data_map.title.content\|wash}` | `Why Track Your Workouts?` |
| über den Index | `{$list[1]}` | `{{ list[1] }}` | `{$list.1}` | `b` |
| Index und Schlüssel | `{$list.0} {$items[1]['name']}` | `{{ list[0] }} {{ items[1].name }}` | `{$list.0} {$items.1.name}` | `a Kale` |
| für HTML und JavaScript maskieren | `{$html\|wash} {$html\|wash('javascript')}` | `{{ html\|wash }} {{ html\|wash('javascript') }}` | `{$html\|wash} {$html\|wash:'javascript'}` | `&lt;b&gt;bold&lt;/b&gt; \x3Cb\x3Ebold\x3C/b\x3E` |

Keine der drei maskiert standardmäßig von selbst: In allen ist es `wash`. Twig kann alles automatisch maskieren
(`[TwigSettings] Autoescape=enabled`); dann druckt `|raw` einen Wert, wie er ist, und `wash` würde doppelt maskieren
([Twig-Templates nativ schreiben](../../anleitungen/twig-templates-nativ-schreiben.md#escaping-wash-autoescape-und-raw)).

## Variablen

| Aufgabe | `.tpl` | `.twig` | `.stpl` |
|---|---|---|---|
| definieren, ändern, entfernen | `{def $total=0}` `{set $total=...}` `{undef $total}` | `{% set total = 0 %}` `{% set total = ... %}` `{% set total = null %}` | `{$total = 0}` `{$total = ...}` `{$total = null}` |
| Ausgabe einfangen | `{set-block variable=greeting}Hello {$user_name}{/set-block}` | `{% set greeting %}Hello {{ user_name }}{% endset %}` | `{capture assign=greeting}Hello {$user_name}{/capture}` |
| eine Variable eines anderen Bereichs | `{set-block scope=global variable=x}...{/set-block}` | `{% expsetblock variable='x' scope='global' %}...{% endexpsetblock %}` | `{expsetblock variable='x' scope='global'}...{/expsetblock}` |
| ein Standardwert | `{first_set($missing, 'fallback')}` | `{{ first_set(missing, 'fallback') }}` oder `{{ missing ?? 'fallback' }}` | `{first_set($missing, 'fallback')}` |

Einen Preis in einer Schleife summieren, das ganze Template in jeder Engine (Ausgabe in jeder `9.75`):

```
.tpl   {def $total=0}{foreach $items as $item}{set $total=$total|sum($item.price)}{/foreach}{$total}{undef $total}
.twig  {% set total = 0 %}{% for item in items %}{% set total = total|sum(item.price) %}{% endfor %}{{ total }}{% set total = null %}
.stpl  {$total = 0}{foreach $items as $item}{$total = $total|sum:$item.price}{/foreach}{$total}{$total = null}
```

In einem handgeschriebenen `.twig` ist `total + item.price` dasselbe.

## Bedingungen

| Aufgabe | `.tpl` | `.twig` | `.stpl` | Ausgabe |
|---|---|---|---|---|
| if, elseif, else | `{if $price\|gt(10)}expensive{elseif $price\|gt(5)}fair{else}cheap{/if}` | `{% if price\|gt(10) %}expensive{% elseif price\|gt(5) %}fair{% else %}cheap{% endif %}` | `{if $price\|gt:10}expensive{elseif $price\|gt:5}fair{else}cheap{/if}` | `expensive` |
| und, oder, nicht | `{if and($price\|gt(10), $user_name\|eq('Ada'))}both{/if}` | `{% if exp_and(price\|gt(10), user_name\|eq('Ada')) %}both{% endif %}` | `{if and($price\|gt:10, $user_name\|eq:'Ada')}both{/if}` | `both` |
| Auswahl in der Zeile | `{cond($price\|gt(10), 'expensive', 'cheap')}` | `{{ cond(price\|gt(10), 'expensive', 'cheap') }}` | `{cond($price\|gt:10, 'expensive', 'cheap')}` | `expensive` |
| switch | `{switch match=$user_name}{case match='Ada'}hello Ada{/case}{case}someone{/case}{/switch}` | `{% set __switch1 = user_name %}{% if __switch1 == 'Ada' %}hello Ada{% else %}someone{% endif %}` | `{$__switch1 = $user_name}{if $__switch1 == 'Ada'}hello Ada{else}someone{/if}` | `hello Ada` |

`and`, `or` und `not` sind Wörter von Twigs eigener Syntax, die Operatoren heißen also `exp_and`, `exp_or`, `exp_not`; ein
handgeschriebenes Template schreibt `price > 10 and user_name == 'Ada'` und `price > 10 ? 'expensive' : 'cheap'`.

## Schleifen

| Aufgabe | `.tpl` | `.twig` | `.stpl` | Ausgabe |
|---|---|---|---|---|
| Schlüssel und Wert, mit Trenner | `{foreach $list as $i => $v}{$i}:{$v}{delimiter}, {/delimiter}{/foreach}` | `{% for i, v in list %}{% if not loop.first %}, {% endif %}{{ i }}:{{ v }}{% endfor %}` | `{foreach $list as $i => $v}{if !$v@first}, {/if}{$i}:{$v}{/foreach}` | `0:a, 1:b, 2:c` |
| offset und max | `{foreach $items as $item offset 1 max 2}{$item.name} {/foreach}` | `{% for item in expslice(items, 1, 2, false) %}{{ item.name }} {% endfor %}` | `{$__loop1 = $items\|expslice:1:2:false}{foreach $__loop1 as $item}{$item.name} {/foreach}` | `Kale Tea` |
| ein Zahlenbereich | `{for 1 to 3 as $i}{$i}{/for}` | `{% for i in exprange(1, 3) %}{{ i }}{% endfor %}` | `{foreach exprange(1, 3) as $i}{$i}{/foreach}` | `123` |
| eine Folge von Werten | `{foreach $list as $v sequence array('odd','even') as $class}{$class} {/foreach}` | `{% for v in list %}{{ expsequence(['odd', 'even'], loop.index0) }} {% endfor %}` | `{$__s = ['odd','even']}{foreach $list as $v}{$__s\|expsequence:$v@index} {/foreach}` | `odd even odd` |
| die alte Section | `{section loop=$list}{$:item}{delimiter}-{/delimiter}{/section}` | `{% for key, item in expsectionloop(list, 0, null, false) %}{% if not loop.first %}-{% endif %}{{ item }}{% endfor %}` | `{$__loop1 = $list\|expsectionloop:0:null:false}{foreach $__loop1 as $key => $item}{if !$item@first}-{/if}{$item}{/foreach}` | `a-b-c` |

Nativ in Twig: `{% for i in 1..3 %}`, `{% for item in items|slice(1, 2) %}`, `{{ cycle(['odd', 'even'], loop.index0) }}`,
`loop.index`, `loop.first`, `loop.last`. Twig hat kein `break` und kein `continue`: Der Konverter schreibt Flags; ein
handgeschriebenes Template filtert mit `{% if %}` in der Schleife.

## Operatoren

| Aufgabe | `.tpl` | `.twig` | `.stpl` | Ausgabe |
|---|---|---|---|---|
| verketten | `{concat('a', $user_name, 'b')}` | `{{ concat('a', user_name, 'b') }}` | `{concat('a', $user_name, 'b')}` | `aAdab` |
| eine Liste verbinden | `{$list\|implode(', ')}` | `{{ list\|implode(', ') }}` | `{$list\|exp_implode:', '}` | `a, b, c` |
| enthält | `{$list\|contains('b')\|choose('no', 'yes')}` | `{{ (list\|contains('b'))\|choose('no', 'yes') }}` | `{($list\|contains:'b')\|choose:'no':'yes'}` | `yes` |
| zählen, zusammenführen, trimmen, runden | `{$list\|count} {$list\|merge(array('d'))\|implode('')} {'  x  '\|trim} {$price\|round}` | `{{ list\|count }} {{ (list\|exp_merge(['d']))\|implode('') }} {{ '  x  '\|exp_trim }} {{ price\|exp_round }}` | `{$list\|exp_count} {($list\|merge:(['d']))\|exp_implode:''} {'  x  '\|trim} {$price\|exp_round}` | `3 abcd x 13` |
| umkehren, ausschneiden, rechnen, Großschreibung | `{$list\|reverse\|implode(',')} {$list\|extract_left(2)\|implode(',')} {$price\|mul(2)} {$price\|sub(2.5)} {$user_name\|upcase}` | `{{ (list\|exp_reverse)\|implode(',') }} {{ (list\|extract_left(2))\|implode(',') }} {{ price\|mul(2) }} {{ price\|sub(2.5) }} {{ user_name\|upcase }}` | `{($list\|reverse)\|exp_implode:','} {($list\|extract_left:2)\|exp_implode:','} {$price\|mul:2} {$price\|sub:2.5} {$user_name\|upcase}` | `c,b,a a,b 25 10 ADA` |

Das Muster: Ein `.tpl`-Operator mit Eingabe ist ein Twig-Filter (`x|op(a)`) und ein Smarty-Modifikator (`$x|op:a`); ohne
Eingabe ist er eine Twig-Funktion (`op(a, b)`) und ein Smarty-Funktionsaufruf. Wo die andere Engine einen eigenen Namen
hat (Twig: `merge`, `reverse`, `round`, `trim`, `join` ...; Smarty: `count`, `implode` ...), bekommt der Operator in dieser
Engine das Präfix `exp_`.

## Daten, Übersetzungen und URLs

| Aufgabe | `.tpl` | `.twig` | `.stpl` | Ausgabe |
|---|---|---|---|---|
| ein Fetch | `{fetch('content', 'list_count', hash('parent_node_id', 2))}` | `{{ fetch('content', 'list_count', {'parent_node_id': 2}) }}` | `{fetch('content', 'list_count', ['parent_node_id' => 2])}` | `3` |
| Fetch und Schleife | `{def $children=fetch('content', 'list', hash('parent_node_id', $node.parent_node_id, 'limit', 2))}{foreach $children as $child}{$child.name\|wash};{/foreach}` | `{% set children = fetch('content', 'list', {'parent_node_id': node.parent_node_id, 'limit': 2}) %}{% for child in children %}{{ child.name\|wash }};{% endfor %}` | `{$children = fetch('content', 'list', ['parent_node_id' => $node.parent_node_id, 'limit' => 2])}{foreach $children as $child}{$child.name\|wash};{/foreach}` | `7 Keystone Habits to Maximize Your Mornings;The BiPolar Learning Curve;` |
| übersetzen | `{'Close'\|i18n('design/standard/content')}` | `{{ 'Close'\|i18n('design/standard/content') }}` | `{'Close'\|i18n:'design/standard/content'}` | `Close` |
| übersetzen mit Argument | `{'%count items'\|i18n('design/standard/shop',, hash('%count', 3))}` | `{{ '%count items'\|i18n('design/standard/shop', expvoid(), {'%count': 3}) }}` | `{'%count items'\|i18n:'design/standard/shop':expvoid():(['%count' => 3])}` | `3 items` |
| ein Link, mit und ohne Anführungszeichen | `{'content/view/full/2'\|ezurl} {'content/view/full/2'\|ezurl('no')}` | `{{ 'content/view/full/2'\|ezurl }} {{ 'content/view/full/2'\|ezurl('no') }}` | `{'content/view/full/2'\|ezurl} {'content/view/full/2'\|ezurl:'no'}` | `"/content/view/full/2" /content/view/full/2` |
| eine Datei der Installation | `{'favicon.ico'\|ezroot}` | `{{ 'favicon.ico'\|ezroot }}` | `{'favicon.ico'\|ezroot}` | `"/favicon.ico"` |
| eine Datei des Designs | `{'stylesheets/core.css'\|ezdesign('no')}` | `{{ 'stylesheets/core.css'\|ezdesign('no') }}` | `{'stylesheets/core.css'\|ezdesign:'no'}` | `/extension/ezwebin/design/ezwebin/stylesheets/core.css` |
| ein Bild des Designs | `{'images/logo.png'\|ezimage}` | `{{ 'images/logo.png'\|ezimage }}` | `{'images/logo.png'\|ezimage}` | die Adresse im ersten Design, das es hat |
| ein Datum | `{$node.object.published\|l10n('shortdate')}` | `{{ node.object.published\|l10n('shortdate') }}` | `{$node.object.published\|l10n:'shortdate'}` | `07/23/2018` |
| eine Einstellung | `{ezini('SiteSettings', 'SiteName')}` | `{{ ezini('SiteSettings', 'SiteName') }}` | `{ezini('SiteSettings', 'SiteName')}` | `Fit & Healthy` |

`hash( 'a', 1 )` ist in Twig `{a: 1}` und in Smarty `['a' => 1]`; `array( 1, 2 )` ist in beiden `[1, 2]`. Ein leerer
Parameter eines `.tpl` (`i18n( 'ctx',, ... )`) ist `expvoid()`; ein handgeschriebenes `.twig` darf `''` schreiben, wo der
Operator einen leeren Text wie einen fehlenden Parameter behandelt, wie `i18n` es tut.

## Includes, Funktionen und Blöcke

| Aufgabe | `.tpl` | `.twig` | `.stpl` |
|---|---|---|---|
| Include über das Design (Override-Regeln gelten) | `{include uri='design:parts/cookie_settings_link.tpl'}` | `{% expinclude uri='design:parts/cookie_settings_link.tpl' %}` | `{expinclude uri='design:parts/cookie_settings_link.tpl'}` |
| eine Template-Funktion | `{attribute_view_gui attribute=$node.data_map.title}` | `{{ attribute_view_gui({'attribute': node.data_map.title}) }}` | `{attribute_view_gui attribute=$node.data_map.title}` |
| ein Knoten in einem Ansichtsmodus | `{node_view_gui content_node=$child view='line'}` | `{{ node_view_gui({content_node: child, view: 'line'}) }}` | `{node_view_gui content_node=$child view='line'}` |
| ein Cache-Block | `{cache-block keys=array($node.node_id)}...{/cache-block}` | `{% expcacheblock id=1 keys=[node.node_id] %}...{% endexpcacheblock %}` | `{expcacheblock id=1 keys=[$node.node_id]}...{/expcacheblock}` |
| einmal je Seite | `{run-once}...{/run-once}` | `{% exprunonce id=1 %}...{% endexprunonce %}` | `{exprunonce id=1}...{/exprunonce}` |
| eine Blockfunktion einer Extension | `{explblock name='x'}...{/explblock}` | `{% explblock name='x' %}...{% endexplblock %}` | `{explblock name='x'}...{/explblock}` |
| Template-Vererbung | (keine: Override-Regeln) | `{% extends 'layout/base.twig' %}{% block body %}...{% endblock %}` | `{extends file='layout/base.stpl'}{block name='body'}...{/block}` |
| wiederverwendbare Schnipsel | (ein Include mit Parametern) | `{% macro badge(text) %}...{% endmacro %}` | `{function name='badge'}...{/function}{call name='badge' text='New'}` |

Das Include über das Design druckte in allen dreien
`<a href="/cookie-policy#cookie-settings" class="js-open-ng-cc d-block my-2">Cookie settings</a>`. Die `id` eines
Cache-Blocks nummeriert die Blöcke eines Templates; der Konverter fügt sie hinzu, ein handgeschriebenes Template sollte
jedem Block eine eigene geben. Die letzten beiden Zeilen haben keine `.tpl`-Form und werden nicht umgewandelt. Sie sind
von Hand geschrieben: Die Twig-Formen und Smartys `{function}` mit `{call}` wurden gerendert (das Smarty-Beispiel druckte
`<span class="badge">New</span>`), die Smarty-Zeile mit `{extends}` wurde hier nicht ausgeführt. Siehe
[Twig-Templates nativ schreiben](../../anleitungen/twig-templates-nativ-schreiben.md).

## Wörtlicher Text und Kommentare

| Aufgabe | `.tpl` | `.twig` | `.stpl` | Ausgabe |
|---|---|---|---|---|
| ein Kommentar | `{* a comment *}` | `{# a comment #}` | `{* a comment *}` | nichts |
| eine Klammer | `{ldelim}literal{rdelim}` | `{{ '{' }}literal}` | `{ldelim}literal{rdelim}` | `{literal}` |
| ein wörtlicher Block | `{literal}{not parsed}{/literal}` | `{not parsed}` (Twig liest einfache Klammern nicht) oder `{% verbatim %}...{% endverbatim %}` | `{ldelim}not parsed}` | `{not parsed}` |

CSS und JavaScript mit Klammern brauchen in Twig nichts: Nur `{{`, `{%` und `{#` beginnen ein Tag.

## Jeder Operator und jede Funktion in Twig

Jeder Template-Operator der Installation und ihrer aktiven Extensions ist sowohl ein Twig-Filter als auch eine
Twig-Funktion; jede Template-Funktion ist eine Twig-Funktion (ohne Rumpf) oder ein Tag (mit Rumpf). Die Regeln:

| `.tpl` | `.twig` | Wann |
|---|---|---|
| `{$x\|op( a )}` | `{{ x\|op(a) }}` | immer: Der Operator bekommt `x` als Eingabe |
| `{op( a, b )}` | `{{ op(a, b) }}` | immer: Der Operator hat keine Eingabe |
| `{$x\|op}`, wo Twig einen Filter `op` hat | `{{ x\|exp_op }}` | Twigs Filter und Funktionen behalten ihre Namen: `merge`, `reverse`, `round`, `trim`, `join`, `date`, `max` ... |
| `{and( ... )}`, `{or( ... )}`, `{not( ... )}`, `{true()}`, `{false()}` | `{{ exp_and(...) }}` ... oder Twigs `and`, `or`, `not`, `true`, `false` | Wörter von Twigs Syntax |
| `{some_function name=value}` | `{{ some_function({name: value}) }}` | eine Template-Funktion ohne Rumpf |
| `{some_block name=value}...{/some_block}` | `{% some_block name=value %}...{% endsome_block %}` | eine Template-Funktion mit Rumpf |

**Der schnelle Weg.** Diese Operatoren laufen direkt in PHP, als statische Aufrufe in das Template kompiliert, mit genau
den Ergebnissen des Template-Systems (die Paritätstests der Engines halten sie daran): `eq`, `ne`, `lt`, `gt`, `le`,
`ge`, `and`, `or`, `not`, `count`, `sum`, `sub`, `mul`, `div`, `mod`, `inc`, `dec`, `cond`, `first_set`, `wash` (für
xhtml), `implode`, `contains` und `concat`. Eine Extension, die einen Operator eines dieser Namen registriert, behält
ihren eigenen: Der direkte Weg wird nur genommen, solange die Klasse des Kernels den Namen hält.

**Jeder andere Operator wird gebrückt.** Das kompilierte Template ruft den eigenen Operator-Code des Template-Systems auf
(`eZTemplate::processOperator()` über einen statischen Brückenaufruf), er verhält sich also wie in einem `.tpl`, auch die
Operatoren einer Extension und ihre Berechtigungen. Ein Operator, den eine Extension später registriert, braucht keinen
eigenen Twig-Code ([Writing operators and functions](../../../eng-us/guides/twig-templates-plugins.md), Englisch).

**Ein unbekannter Name.** Ein Name, der weder ein Operator noch einer von Twig ist, ist ein Kompilierfehler
(`Unknown "wahs" filter.`). `exp_<name>` mit einem Namen, der kein Operator ist, kompiliert, reicht seine Eingabe durch und
schreibt `Unknown operator <name>` in die Debug-Ausgabe, wie es ein unbekannter Operator in einem `.tpl` tut;
`exp:template:check --level=strict` meldet es (`TWIG-STRICT-UNKNOWN-OPERATOR`).

Die vollständige Liste, jeder Operator des Kernels, der Template-Bibliothek und der Standard-Extensions mit seiner
Filter- und Funktionsform, ist die [Twig operator reference](../../../eng-us/guides/twig-templates-operator-reference.md)
(Englisch); die Smarty-Formen stehen in der
[Smarty operator reference](../../../eng-us/guides/smarty-templates-operator-reference.md) (Englisch).

## Verwandte Seiten

- [Smarty und die Template-Sprache nebeneinander](smarty-nebeneinander.md)
- [Von Exponential-Templates (.tpl) zu Twig-Templates (.twig) umziehen](von-tpl-zu-twig-umziehen.md)
- [Zwischen Template-Engines umwandeln](zwischen-template-engines-umwandeln.md)
- [Geschwindigkeit der drei Template-Engines](../../anleitungen/template-engines-geschwindigkeit.md)
- [Twig-Templates: Rezepte](../../anleitungen/twig-templates-rezepte.md)
