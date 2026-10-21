---
title: "Swark-Operatoren: Arrays, Zeichenketten und URLs"
audience: developer, designer, app-creator
category: development
order: 431
summary: Alle Array-, Zeichenketten- und URL-Operatoren der Erweiterung swark - sort, rsort, asort, arsort, ksort, krsort, shuffle, array_search, range, set_array_element, remove_array_element, split_by_length, shortenw, preg_match, preg_replace, strpos, strrpos, substr, ltrim, rtrim, str_replace, json_encode, uri_path_segment, add_view_parameters, modify_view_parameter - mit Parametern, Rückgabewerten und Beispielen, die auf alpha ausgegeben wurden.
keywords: swark, Sortieren, Arrays, Zeichenketten, URLs, View-Parameter, sort, rsort, asort, arsort, ksort, krsort, shuffle, array_search, range, set_array_element, remove_array_element, split_by_length, shortenw, preg_match, preg_replace, uri_path_segment, add_view_parameters, modify_view_parameter, strings, view parameters
type: help
---
# Swark-Operatoren: Arrays, Zeichenketten und URLs

Die Klassen liegen in `extension/swark/autoloads/classes/`, eine je Operator; wie sie registriert werden und warum sich
manche Namen auf alpha anders verhalten, erklärt [Die swark-Operatoren](swark-operatoren.md). Standardwerte der
Parameter sind Zeichenketten, da die Basisklasse sie aus `name=value` liest. Alle diese Operatoren nehmen ihre Eingabe
aus dem Wert vor `|` und geben einen neuen Wert zurück; keiner von ihnen ändert etwas außerhalb des Templates.

## Inhalt

1. [Sortieren](#sortieren): [sort](#sort), [rsort](#rsort), [asort](#asort), [arsort](#arsort), [ksort](#ksort),
   [krsort](#krsort), [shuffle](#shuffle)
2. [Arrays](#arrays): [array_search](#array_search), [range](#range), [set_array_element](#set_array_element),
   [remove_array_element](#remove_array_element), [split_by_length](#split_by_length)
3. [Zeichenketten](#zeichenketten): [shortenw](#shortenw), [preg_match](#preg_match), [preg_replace](#preg_replace),
   [strpos](#strpos), [strrpos](#strrpos), [substr](#substr), [ltrim](#ltrim), [rtrim](#rtrim),
   [str_replace](#str_replace), [json_encode](#json_encode)
4. [URLs und View-Parameter](#urls-und-view-parameter): [uri_path_segment](#uri_path_segment),
   [add_view_parameters](#add_view_parameters), [modify_view_parameter](#modify_view_parameter)

## Sortieren

Keiner der Sortieroperatoren nimmt Parameter. Die Eingabe ist ein Array; zurückgegeben wird die sortierte Kopie.

### sort

PHP `sort()`: Werte aufsteigend, Schlüssel neu nummeriert.

### rsort

PHP `rsort()`: Werte absteigend, Schlüssel neu nummeriert.

### asort

PHP `asort()`: Werte aufsteigend, Schlüssel bleiben erhalten.

### arsort

PHP `arsort()`: Werte absteigend, Schlüssel bleiben erhalten.

### ksort

PHP `ksort()`: Schlüssel aufsteigend.

### krsort

PHP `krsort()`: Schlüssel absteigend.

### shuffle

PHP `shuffle()`, wenn die Eingabe ein Array ist (andere Eingaben werden unverändert zurückgegeben). Verwenden Sie ihn
nie in gecachter Ausgabe: Die Reihenfolge ist eingefroren, bis der Cache abläuft.

```tpl
{def $h = hash( 'b', 2, 'c', 3, 'a', 1 )
     $l = array( 'pear', 'apple', 'fig' )}
sort:    {$l|sort|implode( ' ' )}
rsort:   {$l|rsort|implode( ' ' )}
asort:   {foreach $h|asort as $k => $v}{$k}={$v} {/foreach}

arsort:  {foreach $h|arsort as $k => $v}{$k}={$v} {/foreach}

ksort:   {foreach $h|ksort as $k => $v}{$k}={$v} {/foreach}

krsort:  {foreach $h|krsort as $k => $v}{$k}={$v} {/foreach}

shuffle keeps {$l|shuffle|count} items
```

Ausgabe auf alpha:

```text
sort:    apple fig pear
rsort:   pear fig apple
asort:   a=1 b=2 c=3 
arsort:  c=3 b=2 a=1 
ksort:   a=1 b=2 c=3 
krsort:  c=3 b=2 a=1 
shuffle keeps 3 items
```

## Arrays

### array_search

| Parameter | Pflicht | Standard |
|---|---|---|
| `search` | ja | |
| `not_found_key` | nein | `-1` |

Gibt den Schlüssel des ersten Elements zurück, das gleich `search` ist (PHP `array_search()`, loser Vergleich), oder
`not_found_key`.

### range

| Parameter | Pflicht | Standard |
|---|---|---|
| `min` | ja | |
| `max` | ja | |
| `step` | nein | `1` |

Keine Eingabe. Gibt die Zahlen von `min` bis `max` zurück; das Vorzeichen von `step` wird an die Richtung angepasst,
und ein `step` von 0 oder ein `min` gleich `max` ergibt `array( min )`.

### set_array_element

| Parameter | Pflicht |
|---|---|
| `key` | ja |
| `value` | ja |

Gibt das Eingabe-Array zurück, in dem `key` auf `value` gesetzt ist.

### remove_array_element

| Parameter | Pflicht |
|---|---|
| `key` | ja |

Gibt das Eingabe-Array ohne `key` zurück.

### split_by_length

| Parameter | Pflicht | Standard |
|---|---|---|
| `length` | nein | `1` |

Zerschneidet eine Zeichenkette in Stücke von `length` Bytes. Ein Array von Längen wird der Reihe nach verwendet
(`array( 4, 2, 2 )` teilt eine datumsartige Zeichenkette). Geschnitten wird nach Bytes (PHP `substr()`), daher kann
ein Multibyte-Zeichen geteilt werden.

```tpl
{def $l = array( 'pear', 'apple', 'fig' )
     $h = hash( 'a', 1, 'b', 2 )}
1 array_search apple: {$l|array_search( 'apple' )}, kiwi: {$l|array_search( 'kiwi' )}, kiwi with default: {$l|array_search( 'kiwi', 'none' )}
2 range: {range( 1, 10, 3 )|implode( ',' )} / {range( 5, 1 )|implode( ',' )} / {range( 0, 1, 0.25 )|implode( ',' )}
3 set_array_element: {foreach $h|set_array_element( 'c', 3 ) as $k => $v}{$k}={$v} {/foreach}

4 remove_array_element: {foreach $h|remove_array_element( 'a' ) as $k => $v}{$k}={$v} {/foreach}

5 split_by_length: {'2026101912'|split_by_length( array( 4, 2, 2 ) )|implode( '-' )} / {'abcdef'|split_by_length( 2 )|implode( ' ' )}
```

Ausgabe auf alpha:

```text
1 array_search apple: 1, kiwi: -1, kiwi with default: none
2 range: 1,4,7,10 / 5,4,3,2,1 / 0,0.25,0.5,0.75,1
3 set_array_element: a=1 b=2 c=3 
4 remove_array_element: b=2 
5 split_by_length: 2026-10-19-12 / ab cd ef
```

## Zeichenketten

### shortenw

| Parameter | Pflicht | Standard |
|---|---|---|
| `length` | ja | |
| `ellipsis` | nein | `...` |

Kürzt auf höchstens `length` Bytes einschließlich der Auslassungszeichen und schneidet bis zum letzten Leerzeichen
zurück, sodass kein Wort getrennt wird. Anders als `shorten` aus dem Kernel schneidet er nie innerhalb eines Wortes
(sofern es ein Leerzeichen gibt, an dem geschnitten werden kann).

### preg_match

| Parameter | Pflicht |
|---|---|
| `pattern` | ja |

Gibt die **Anzahl der Treffer** zurück (er ruft `preg_match_all()` auf), nicht die Treffer selbst.

### preg_replace

| Parameter | Pflicht |
|---|---|
| `search` | ja |
| `replace` | ja |

PHP `preg_replace( search, replace, input )`. Das Muster wird so verwendet, wie es angegeben ist: Bauen Sie es nie aus
Eingaben der Anfrage.

```tpl
{def $text = 'The quick brown fox jumps over the lazy dog'}
1 shortenw: {$text|shortenw( 20 )} / {$text|shortenw( 20, ' [more]' )}
2 preg_match counts: {$text|preg_match( '/o/' )}
3 preg_replace: {$text|preg_replace( '/\s+/', '_' )}
4 uri_path_segment: {'/recipes/salads/green-salad?x=1'|uri_path_segment} / {'/recipes/salads/green-salad'|uri_path_segment( 0 )} / {'https://example.com/a/b/'|uri_path_segment( -2 )}
5 add_view_parameters: {'/recipes/'|add_view_parameters( hash( 'offset', 20, 'tag', 'vegan' ) )}
6 modify_view_parameter: {'/recipes/(offset)/20/(tag)/vegan'|modify_view_parameter( 'offset', 40 )} / {'/recipes/(offset)/20'|modify_view_parameter( 'offset', false() )} / {'/recipes'|modify_view_parameter( 'tag', 'soup' )}
```

Ausgabe auf alpha:

```text
1 shortenw: The quick brown... / The quick [more]
2 preg_match counts: 4
3 preg_replace: The_quick_brown_fox_jumps_over_the_lazy_dog
4 uri_path_segment: green-salad / recipes / a
5 add_view_parameters: /recipes/(offset)/20/(tag)/vegan
6 modify_view_parameter: /recipes/(offset)/40/(tag)/vegan / /recipes / /recipes/(tag)/soup
```

### strpos

Parameter `needle` (Pflicht) und `offset` (Standard `0`). Gibt die Position zurück oder **-1**, wenn nichts gefunden
wird. Auf alpha antwortet oft stattdessen `strpos` aus dem Kernel (das `false` zurückgibt); siehe
[Ownership can change during a request](swark-operatoren.md#der-besitzer-kann-während-einer-anfrage-wechseln).

### strrpos

Parameter `needle`. Die letzte Position oder -1. Derselbe Vorbehalt wie bei `strpos`.

### substr

Parameter `start` (Pflicht) und `length` (Standard `0`, das heißt „bis zum Ende“). Derselbe Vorbehalt.

### ltrim

Parameter `charlist` (Standard leer: Leerraum). Derselbe Vorbehalt.

### rtrim

Parameter `charlist` (Standard leer: Leerraum). Derselbe Vorbehalt.

### str_replace

Parameter `search` und `replace`: PHP `str_replace()` auf die Eingabe. Auf alpha gehört der Name zu owsimpleoperator,
der später registriert wird.

### json_encode

Parameter `value`: die Eingabe, oder `value`, wenn es keine Eingabe gibt, als JSON (PHP `json_encode()` oder ein
eigener Encoder, wo diese Funktion fehlt). Auf alpha gehört der Name zu ezjscore; siehe
[ezjscore-Operatoren im Detail](ezjscore-operatoren.md#json_encode).

## URLs und View-Parameter

### uri_path_segment

| Parameter | Pflicht | Standard |
|---|---|---|
| `index` | nein | `-1` (das letzte Segment) |

Gibt ein Segment des Pfades einer URL oder eines Pfades zurück (Schema, Host, Query und Fragment werden ignoriert);
ein negativer Index zählt vom Ende. `false`, wenn es kein solches Segment gibt.

### add_view_parameters

| Parameter | Pflicht |
|---|---|
| `view_parameters` | ja |

Hängt für jeden Eintrag eines Hashes `/(key)/value` an den Eingabepfad an (ein abschließender Schrägstrich wird
vorher entfernt). Die Werte werden nicht kodiert: Wenden Sie vorher `wash` oder `urlencode` auf Benutzerwerte an.

### modify_view_parameter

| Parameter | Pflicht |
|---|---|
| `parameter` | ja |
| `value` | ja |

Setzt einen View-Parameter in einem Pfad: ersetzt seinen Wert, wenn er vorhanden ist, hängt ihn an, wenn nicht, und
entfernt ihn, wenn `value` gleich `false()` ist. Eine leere Eingabe wird zu `/`. Nützlich für Blätter- und Filterlinks,
die aus der aktuellen URL gebaut werden.

Die Beispiele dieser drei sind die Zeilen 4 bis 6 des Zeichenketten-Beispiels oben.

## Verwandte Seiten

- [Die swark-Operatoren](swark-operatoren.md)
- [Swark-Operatoren: Anfrage und System](swark-operatoren-anfrage-und-system.md)
- [Array-Operatoren für Designer](../designer/vorlagen/array-operatoren.md)
- [Seitenweise Anzeige](../designer/vorlagen/seitenumbruch.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
