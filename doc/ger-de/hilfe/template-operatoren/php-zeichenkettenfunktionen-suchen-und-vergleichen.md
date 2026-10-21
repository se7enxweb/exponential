---
title: "PHP-Zeichenkettenfunktionen: suchen und vergleichen"
audience: developer, designer, app-creator
category: development
order: 160
summary: Die Operatoren von eZTemplateStringsOperator zum Suchen, Vergleichen und Bestimmen der Ähnlichkeit (strlen, strpos, stripos, strrpos, strripos, strstr, stristr, strrchr, strpbrk, strspn, strcspn, substr_count, str_contains, str_starts_with, str_ends_with, strcmp, strcasecmp, strncmp, strncasecmp, strnatcmp, strnatcasecmp, strcoll, substr_compare, levenshtein, similar_text, soundex, metaphone), wie ihre Parameter auf PHP abgebildet werden, mit auf alpha gerenderten Beispielen.
keywords: PHP-Zeichenkettenfunktionen, Zeichenkette suchen, Zeichenketten vergleichen, Ähnlichkeit, Position, Teilzeichenkette, natürliche Sortierung, PHP string functions, search, compare, strlen, strpos, stripos, strrpos, strripos, strstr, stristr, strrchr, strpbrk, strspn, strcspn, substr_count, str_contains, str_starts_with, str_ends_with, strcmp, strcasecmp, strncmp, strncasecmp, strnatcmp, strnatcasecmp, strcoll, substr_compare, levenshtein, similar_text, soundex, metaphone, eZTemplateStringsOperator
type: help
---
# PHP-Zeichenkettenfunktionen: suchen und vergleichen

`eZTemplateStringsOperator` (`lib/eztemplate/classes/eztemplatestringsoperator.php`) stellt 60 PHP-Zeichenkettenfunktionen unter
ihren PHP-Namen als Template-Operatoren bereit. Diese Seite behandelt die 27, die Zeichenketten messen, durchsuchen und vergleichen.
Die übrigen finden Sie unter [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md) und
[PHP-Zeichenkettenfunktionen: kodieren und formatieren](php-zeichenkettenfunktionen-kodieren-und-formatieren.md).

## Inhalt

1. [Wie die PHP-Funktionen abgebildet werden](#wie-die-php-funktionen-abgebildet-werden)
2. [Länge und Position](#länge-und-position)
3. [Teilzeichenketten finden](#teilzeichenketten-finden)
4. [Enthält, beginnt mit, endet mit](#enthält-beginnt-mit-endet-mit)
5. [Vergleichen](#vergleichen)
6. [Ähnlichkeit](#ähnlichkeit)
7. [Verwandte Seiten](#verwandte-seiten)

## Wie die PHP-Funktionen abgebildet werden

Der Kommentar der Klasse nennt die Regel, und `modify()` folgt ihr:

- Die **Eingabe über die Pipe** ist das Haupt-Zeichenkettenargument der PHP-Funktion (in eine Zeichenkette umgewandelt).
- Die **Template-Parameter** sind die übrigen PHP-Argumente in ihrer Reihenfolge, jeweils in den Typ umgewandelt, den PHP erwartet.
- Es gibt keine benannten Parameter: Jedes Argument ist positionsbezogen (`operatorTemplateHints()` deklariert keine).
- Eine Funktion, die für „nicht gefunden“ `false` zurückgibt, gibt auch im Template `false` zurück. Prüfen Sie das mit `choose`,
  `eq( false() )` oder `is_boolean`, denn Position 0 ist ebenfalls eine gültige Antwort.
- Ein fehlendes erforderliches Argument protokolliert eine Warnung wie „Missing required parameter: needle“ und lässt die Eingabe
  unverändert.

Registriert werden nur die Namen in `lib/eztemplate/classes/eztemplateautoload.php`. Funktionen, die bereits einen eigenen
Template-Operator haben (`trim`, `nl2br`, `md5` und weitere, die im Kommentar der Klasse aufgeführt sind), werden hier nicht
doppelt angeboten.

In den Beispielen gibt `choose( 'false', 'true' )` einen Boolean-Wert als Wort aus.

## Länge und Position

### strlen

PHP `strlen()`: die Länge in **Bytes**. Für Zeichen verwenden Sie [count_chars](zeichenketten-operatoren.md#count_chars).
Keine Parameter. Gibt eine Ganzzahl zurück. Generierte Referenz: [strlen](../../../eng-us/examples/template-operators/strlen.md).

### strpos, stripos, strrpos, strripos

Die Position des ersten (`strpos`, ohne Beachtung der Groß- und Kleinschreibung `stripos`) oder letzten (`strrpos`, ohne Beachtung
der Groß- und Kleinschreibung `strripos`) Vorkommens einer Suchzeichenkette, in Bytes ab 0 gezählt, oder `false`.

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| needle | Zeichenkette | ja | keiner |
| offset | Ganzzahl | nein | PHPs 0 |

Gibt eine Ganzzahl oder `false` zurück. Generierte Referenzen: [strpos](../../../eng-us/examples/template-operators/strpos.md),
[stripos](../../../eng-us/examples/template-operators/stripos.md), [strrpos](../../../eng-us/examples/template-operators/strrpos.md),
[strripos](../../../eng-us/examples/template-operators/strripos.md).

```tpl
{def $s = 'Fit & Healthy: healthy eating'}
strlen = {$s|strlen}
strpos( 'healthy' ) = {$s|strpos( 'healthy' )}
stripos( 'healthy' ) = {$s|stripos( 'healthy' )}
strrpos( 'h' ) = {$s|strrpos( 'h' )}
strripos( 'HEALTHY' ) = {$s|strripos( 'HEALTHY' )}
strpos( 'healthy', 20 ) = [{$s|strpos( 'healthy', 20 )|choose( 'false', 'true' )}] (false: not found after 20)
```

Ausgabe auf alpha:

```text
strlen = 29
strpos( 'healthy' ) = 15
stripos( 'healthy' ) = 6
strrpos( 'h' ) = 20
strripos( 'HEALTHY' ) = 15
strpos( 'healthy', 20 ) = [false] (false: not found after 20)
```

## Teilzeichenketten finden

### strstr und stristr

Der Teil der Eingabe ab dem ersten Vorkommen der Suchzeichenkette (`strstr`; ohne Beachtung der Groß- und Kleinschreibung
`stristr`) oder, wenn der zweite Parameter wahr ist, der Teil **davor**. `false`, wenn die Suchzeichenkette nicht gefunden wird.

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| needle | Zeichenkette | ja | keiner |
| before_needle | Boolean | nein | PHPs `false` |

Generierte Referenzen: [strstr](../../../eng-us/examples/template-operators/strstr.md), [stristr](../../../eng-us/examples/template-operators/stristr.md).

### strrchr

Der Teil ab dem **letzten** Vorkommen eines Zeichens oder `false`. Parameter: needle (Zeichenkette, erforderlich).
Generierte Referenz: [strrchr](../../../eng-us/examples/template-operators/strrchr.md).

### strpbrk

Der Teil ab dem ersten Zeichen, das in der Liste vorkommt, oder `false`. Parameter: char_list (Zeichenkette, erforderlich).
Generierte Referenz: [strpbrk](../../../eng-us/examples/template-operators/strpbrk.md).

### strspn und strcspn

`strspn`: die Länge des Anfangsstücks, das nur aus Zeichen der Maske besteht. `strcspn`: die Länge des Anfangsstücks, das aus
Zeichen besteht, die **nicht** in der Maske vorkommen.

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| mask | Zeichenkette | ja | keiner |
| offset | Ganzzahl | nein | PHPs 0 |
| length | Ganzzahl | nein | PHPs `null` (bis zum Ende) |

Gibt eine Ganzzahl zurück. Generierte Referenzen: [strspn](../../../eng-us/examples/template-operators/strspn.md),
[strcspn](../../../eng-us/examples/template-operators/strcspn.md).

### substr_count

Wie oft die Suchzeichenkette vorkommt (nicht überlappend).

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| needle | Zeichenkette | ja | keiner |
| offset | Ganzzahl | nein | PHPs 0 |
| length | Ganzzahl | nein | PHPs `null` |

Gibt eine Ganzzahl zurück. Generierte Referenz: [substr_count](../../../eng-us/examples/template-operators/substr_count.md).

```tpl
{def $s = 'recipes/pasta/almond-french-toast'}
strstr( '/' ) = {$s|strstr( '/' )}
strstr( '/', true() ) = {$s|strstr( '/', true() )}
stristr( 'PASTA' ) = {$s|stristr( 'PASTA' )}
strrchr( '/' ) = {$s|strrchr( '/' )}
strpbrk( '-/' ) = {$s|strpbrk( '-/' )}
strspn( 'abcdefghijklmnopqrstuvwxyz' ) = {$s|strspn( 'abcdefghijklmnopqrstuvwxyz' )}
strcspn( '/' ) = {$s|strcspn( '/' )}
substr_count( '-' ) = {$s|substr_count( '-' )}
```

Ausgabe auf alpha:

```text
strstr( '/' ) = /pasta/almond-french-toast
strstr( '/', true() ) = recipes
stristr( 'PASTA' ) = pasta/almond-french-toast
strrchr( '/' ) = /almond-french-toast
strpbrk( '-/' ) = /pasta/almond-french-toast
strspn( 'abcdefghijklmnopqrstuvwxyz' ) = 7
strcspn( '/' ) = 7
substr_count( '-' ) = 2
```

## Enthält, beginnt mit, endet mit

### str_contains, str_starts_with, str_ends_with

PHP 8s `str_contains()`, `str_starts_with()` und `str_ends_with()`: Prüfungen auf Bytes, die Groß- und Kleinschreibung
unterscheiden. Die Klasse hat einen Ersatz mit `strpos()`/`substr()` für ein PHP, dem sie fehlen. Eine leere Suchzeichenkette wird
immer gefunden.

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| needle | Zeichenkette | ja | keiner |

Gibt einen Boolean-Wert zurück. Die Array-Operatoren `contains`, `begins_with` und `ends_with` tun dasselbe für Zeichenketten mit
`mb_*`-Funktionen ([Array-Operatoren](array-operatoren.md)). Generierte Referenzen:
[str_contains](../../../eng-us/examples/template-operators/str_contains.md),
[str_starts_with](../../../eng-us/examples/template-operators/str_starts_with.md),
[str_ends_with](../../../eng-us/examples/template-operators/str_ends_with.md).

```tpl
{def $file = 'Healthy-Breakfast.JPG'}
str_contains( 'Breakfast' ) = {$file|str_contains( 'Breakfast' )|choose( 'false', 'true' )}
str_starts_with( 'Healthy' ) = {$file|str_starts_with( 'Healthy' )|choose( 'false', 'true' )}
str_ends_with( '.jpg' ) = {$file|str_ends_with( '.jpg' )|choose( 'false', 'true' )}
str_ends_with( '.JPG' ) = {$file|str_ends_with( '.JPG' )|choose( 'false', 'true' )}
```

Ausgabe auf alpha:

```text
str_contains( 'Breakfast' ) = true
str_starts_with( 'Healthy' ) = true
str_ends_with( '.jpg' ) = false
str_ends_with( '.JPG' ) = true
```

## Vergleichen

Alle Vergleichsfunktionen geben 0 zurück, wenn die Zeichenketten gleich sind, eine negative Zahl, wenn die Eingabe zuerst
einsortiert wird, und eine positive Zahl, wenn sie danach einsortiert wird. Sie sind die Operatoren, mit denen Sie Text ordnen,
denn die logischen Operatoren `lt` und `gt` vergleichen Zeichenketten nach ihrer Länge
([Logische Operatoren](logische-operatoren.md#lt-gt-le-und-ge)).

### strcmp und strcasecmp

Binärer Vergleich, mit (`strcmp`) oder ohne (`strcasecmp`) Unterscheidung der Groß- und Kleinschreibung. Parameter: string2
(Zeichenkette, erforderlich). Generierte Referenzen: [strcmp](../../../eng-us/examples/template-operators/strcmp.md),
[strcasecmp](../../../eng-us/examples/template-operators/strcasecmp.md).

### strncmp und strncasecmp

Dasselbe, nur für die ersten n Bytes.

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| string2 | Zeichenkette | ja | keiner |
| n | Ganzzahl | ja | keiner |

Fehlt einer der beiden, protokolliert der Operator „Missing required parameters: string2 and n“. Generierte Referenzen:
[strncmp](../../../eng-us/examples/template-operators/strncmp.md), [strncasecmp](../../../eng-us/examples/template-operators/strncasecmp.md).

### strnatcmp und strnatcasecmp

Vergleich in „natürlicher Reihenfolge“, der Ziffernfolgen als Zahlen behandelt: `img2` kommt vor `img10`. Parameter: string2
(Zeichenkette, erforderlich). Generierte Referenzen: [strnatcmp](../../../eng-us/examples/template-operators/strnatcmp.md),
[strnatcasecmp](../../../eng-us/examples/template-operators/strnatcasecmp.md).

### strcoll

Vergleich nach dem aktuellen Locale (`LC_COLLATE`). Parameter: string2 (Zeichenkette, erforderlich).
Generierte Referenz: [strcoll](../../../eng-us/examples/template-operators/strcoll.md).

### substr_compare

Vergleicht die Eingabe ab einem Offset mit einer anderen Zeichenkette.

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| string | Zeichenkette | ja | keiner |
| offset | Ganzzahl | ja | keiner |
| length | Ganzzahl | nein | PHPs `null` |
| case_insensitive | Boolean | nein | PHPs `false` |

Generierte Referenz: [substr_compare](../../../eng-us/examples/template-operators/substr_compare.md).

```tpl
'apple'|strcmp( 'banana' ) = {'apple'|strcmp( 'banana' )}
'Apple'|strcasecmp( 'apple' ) = {'Apple'|strcasecmp( 'apple' )}
'img12'|strcmp( 'img10' ) = {'img12'|strcmp( 'img10' )}
'img2'|strcmp( 'img10' ) = {'img2'|strcmp( 'img10' )}
'img2'|strnatcmp( 'img10' ) = {'img2'|strnatcmp( 'img10' )}
'IMG2'|strnatcasecmp( 'img10' ) = {'IMG2'|strnatcasecmp( 'img10' )}
'Healthy'|strncmp( 'Health', 6 ) = {'Healthy'|strncmp( 'Health', 6 )}
'HEALTHY'|strncasecmp( 'health', 6 ) = {'HEALTHY'|strncasecmp( 'health', 6 )}
'b'|strcoll( 'a' ) = {'b'|strcoll( 'a' )}
'Hello world'|substr_compare( 'world', 6 ) = {'Hello world'|substr_compare( 'world', 6 )}
'Hello WORLD'|substr_compare( 'world', 6, 5, true() ) = {'Hello WORLD'|substr_compare( 'world', 6, 5, true() )}
```

Ausgabe auf alpha:

```text
'apple'|strcmp( 'banana' ) = -1
'Apple'|strcasecmp( 'apple' ) = 0
'img12'|strcmp( 'img10' ) = 2
'img2'|strcmp( 'img10' ) = 1
'img2'|strnatcmp( 'img10' ) = -1
'IMG2'|strnatcasecmp( 'img10' ) = -1
'Healthy'|strncmp( 'Health', 6 ) = 0
'HEALTHY'|strncasecmp( 'health', 6 ) = 0
'b'|strcoll( 'a' ) = 1
'Hello world'|substr_compare( 'world', 6 ) = 0
'Hello WORLD'|substr_compare( 'world', 6, 5, true() ) = 0
```

Eine Prüfung auf „dasselbe Wort, ohne Beachtung der Groß- und Kleinschreibung“:

```tpl
{def $a = 'Running'
     $b = 'running'}
{if $a|strcasecmp( $b )|eq( 0 )}"{$a}" and "{$b}" are the same word, ignoring case.{/if}
```

Ausgabe auf alpha:

```text
"Running" and "running" are the same word, ignoring case.
```

Ohne seinen erforderlichen Parameter lässt ein Vergleich die Eingabe unverändert und protokolliert eine Warnung:

```tpl
[{'abc'|strcmp}]
```

Ausgabe auf alpha:

```text
[abc]
```

Von der Template-Engine protokolliert:

```text
WARNING strcmp: Missing required parameter: string2
```

## Ähnlichkeit

### levenshtein

Der Editierabstand zwischen der Eingabe und einer anderen Zeichenkette. Parameter: string2 (Zeichenkette, erforderlich). Gibt eine
Ganzzahl zurück. Generierte Referenz: [levenshtein](../../../eng-us/examples/template-operators/levenshtein.md).

### similar_text

Die Anzahl übereinstimmender Zeichen (PHP `similar_text()` ohne sein Prozent-Argument). Parameter: string2 (Zeichenkette,
erforderlich). Gibt eine Ganzzahl zurück. Generierte Referenz: [similar_text](../../../eng-us/examples/template-operators/similar_text.md).

### soundex und metaphone

Phonetische Schlüssel für englische Wörter: `soundex` (keine Parameter) und `metaphone` (optional max_phonemes, Ganzzahl). Geben
Zeichenketten zurück. Generierte Referenzen: [soundex](../../../eng-us/examples/template-operators/soundex.md),
[metaphone](../../../eng-us/examples/template-operators/metaphone.md).

```tpl
'recipe'|levenshtein( 'recipes' ) = {'recipe'|levenshtein( 'recipes' )}
'healthy'|similar_text( 'wealthy' ) = {'healthy'|similar_text( 'wealthy' )}
'Robert'|soundex = {'Robert'|soundex}, 'Rupert'|soundex = {'Rupert'|soundex}
'Thompson'|metaphone = {'Thompson'|metaphone}
'Thompson'|metaphone( 2 ) = {'Thompson'|metaphone( 2 )}
```

Ausgabe auf alpha:

```text
'recipe'|levenshtein( 'recipes' ) = 1
'healthy'|similar_text( 'wealthy' ) = 6
'Robert'|soundex = R163, 'Rupert'|soundex = R163
'Thompson'|metaphone = 0MPSN
'Thompson'|metaphone( 2 ) = 0M
```

## Verwandte Seiten

- [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md)
- [PHP-Zeichenkettenfunktionen: kodieren und formatieren](php-zeichenkettenfunktionen-kodieren-und-formatieren.md)
- [Zeichenketten-Operatoren](zeichenketten-operatoren.md): `wash`, `shorten`, `upcase` und die anderen nativen Operatoren
- [Zeichenketten-Operatoren für Designer](../../../eng-us/help/designers/templates/string-template-operators.md)
- [Alle Template-Operatoren (generiert)](../../../eng-us/examples/template-operators/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
