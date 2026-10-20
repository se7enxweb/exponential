---
title: Lexikalische Struktur
audience: developer, designer, app-creator
category: development
order: 60
summary: Wie der Parser ein Template liest - die Begrenzer, die Arten von Tags (Ausgabe-Tags, Funktions-Tags, End-Tags, Einzel-Tags), Kommentare, literal-Blöcke, ldelim und rdelim, eine Klammer in einem Tag maskieren, die Regel, die einen Zeilenumbruch um jedes Funktions-Tag entfernt, und wie Parserfehler Zeile und Spalte melden.
keywords: lexikalische Struktur, Parser, Begrenzer, Klammern, Tags, Funktions-Tag, End-Tag, Einzel-Tag, Kommentare, literal, ldelim, rdelim, Leerraum, Zeilenumbruch, Parserfehler, Zeile, Spalte, lexical structure, delimiters, braces, function tag, end tag, single tag, comments, whitespace, newline, parser error
type: help
---
# Lexikalische Struktur

Bevor irgendetwas ausgewertet wird, zerlegt der Parser in `lib/eztemplate/classes/eztemplatemultipassparser.php` das Template in drei Durchgängen:
Textelemente und Tags (`parseIntoTextElements()`), Entfernen von Leerraum um Tags (`parseWhitespaceRemoval()`) und der Knotenbaum
(`parseIntoTree()`). Diese Seite beschreibt die ersten beiden, die entscheiden, was Text ist, was ein Tag ist und welche Zeilenumbrüche die
Ausgabe erreichen.

## Inhalt

1. [Begrenzer](#begrenzer)
2. [Arten von Tags](#arten-von-tags)
3. [Kommentare](#kommentare)
4. [Wörtlicher Text, ldelim und rdelim](#wörtlicher-text-ldelim-und-rdelim)
5. [Eine Klammer in einem Tag](#eine-klammer-in-einem-tag)
6. [Leerraum um Tags](#leerraum-um-tags)
7. [Einzel-Tags mit Schrägstrich](#einzel-tags-mit-schrägstrich)
8. [Parserfehler: Zeile und Spalte](#parserfehler-zeile-und-spalte)
9. [Unbekannte Funktions-Tags](#unbekannte-funktions-tags)
10. [Verwandte Seiten](#verwandte-seiten)

## Begrenzer

Ein Tag beginnt bei `{` und endet bei der nächsten `}`. Die Begrenzer werden im Konstruktor von `eZTemplate` gesetzt (`$this->LDelim = "{"`,
`$this->RDelim = "}"`); es gibt keine Einstellung, um sie zu ändern.

## Arten von Tags

Der Parser sieht sich die ersten Zeichen innerhalb der Klammern an:

| Das Tag beginnt mit | Es ist | Beispiel |
|---|---|---|
| `$` | ein Ausgabe-Tag: eine Variable, wahlweise mit Attributen und Operatoren | `{$node.name\|wash}` |
| `'` oder `"` | ein Ausgabe-Tag mit einer Zeichenkette | `{'text'\|upcase}` |
| einer Ziffer, oder `-` und einer Ziffer | ein Ausgabe-Tag mit einer Zahl | `{42}`, `{-7}` |
| Kleinbuchstaben, Ziffern, `_` oder `-`, gefolgt von `(` | ein Ausgabe-Tag mit einem Operatoraufruf | `{concat( 'a', 'b' )}` |
| `*` | ein Kommentar, bis `*}` | `{* note *}` |
| `/` | ein End-Tag | `{/if}` |
| allem anderen | ein Funktions-Tag; es ist ein Einzel-Tag, wenn es mit `/` endet | `{if ...}`, `{include ... /}` |

Jede Art in einem Template:

```tpl
{def $who = 'world'}
text | {$who} | {'literal string'} | {42} | {$who|upcase} | {concat( 'a', 'b' )} | {if true()}block{/if}
```

Ausgabe auf alpha:

```text
text | world | literal string | 42 | WORLD | ab | block
```

## Kommentare

`{* ... *}` darf über mehrere Zeilen gehen. Der Kommentar selbst wird verworfen, der Text um ihn herum bleibt aber unberührt: Die
Zeilenumbrüche vor und nach einem Kommentar bleiben, anders als die um Funktions-Tags.

```tpl
line 1
{* a comment
   over two lines *}
line 3 {* inline *} still line 3
```

Ausgabe auf alpha:

```text
line 1

line 3  still line 3
```

## Wörtlicher Text, ldelim und rdelim

`{literal}` ist als literal-Tag registriert (`registerLiteral( 'literal' )` im Konstruktor). Alles bis `{/literal}` ist reiner Text,
Klammern eingeschlossen. `{ldelim}` und `{rdelim}` (`eZTemplateDelimitFunction`) geben eine einzelne Klammer aus. Beides wird auf
[der Seite zu den Unterschieden](smarty-unterschiede-im-detail.md#klammern-in-css-und-javascript) gezeigt, die auch zeigt, was eine ungeschützte
CSS-Regel mit einem Template macht.

## Eine Klammer in einem Tag

Das Tag endet bei der ersten `}`, vor der kein Backslash steht. Innerhalb eines Tags ist `\}` eine wörtliche Klammer. Ohne den Backslash wird
die Zeichenkette abgeschnitten, das Tag endet zu früh und der Rest wird als Text ausgegeben:

```tpl
[{'a\}b'}]
[{'a}b'}]
```

Ausgabe auf alpha:

```text
[a}b]
[ab'}]
```

## Leerraum um Tags

Der zweite Durchgang entfernt **einen** Zeilenumbruch direkt vor einem Funktions-Tag (normales Tag, Einzel-Tag oder End-Tag) und **einen**
Zeilenumbruch direkt danach. Text und Ausgabe-Tags (`{$x}`, `{'...'}`) bleiben unberührt. In diesem Template steht jedes Funktions-Tag in
einer eigenen Zeile, also verschwinden die Zeilenumbrüche um `{def}`, `{if}` und `{/if}`, während die um `{$x}` bleiben:

```tpl
{def $x = 'X'}
A
{if true()}
B
{/if}
C
{$x}
D
```

Ausgabe auf alpha:

```text
ABC
X
D
```

Auf jeder Seite wird nur ein Zeilenumbruch entfernt. Eine Leerzeile vor oder nach einem Funktions-Tag hinterlässt daher noch einen
Zeilenumbruch:

```tpl
A

{if true()}

B

{/if}

C
```

Ausgabe auf alpha:

```text
A

B

C
```

Leerzeichen und Tabulatoren werden nie entfernt. Eine Einrückung vor einem Funktions-Tag bleibt in der Ausgabe.

## Einzel-Tags mit Schrägstrich

Ein Tag, das mit `/` endet, ist ein Einzel-Tag: Es hat kein End-Tag. Funktionen mit einer besonderen Syntax (`if`, `elseif`, `foreach`,
`def`, `undef`, `set`, `let`, `default`, `set-block`, `append-block`, `section`, `while`, `for`) werden nur in der normalen Form mit ihrer
eigenen Grammatik gelesen. Mit Schrägstrich geschrieben, geht `{set $n = 2 /}` an den allgemeinen Parameterparser, der es nicht annimmt, und
die Variable behält ihren Wert:

```tpl
{def $n = 1}[{set $n = 2 /}{$n}]
```

Ausgabe auf alpha:

```text
[1]
```

Von der Template-Engine protokolliert:

```text
ERROR : parser error @ tl-lex/08-single-tag-slash.tpl:1[15]
Invalid parameter characters in function 'set': '$n = 2'
```

Schreiben Sie diese Funktionen ohne Schrägstrich. Der Schrägstrich funktioniert bei Funktionen, die einfache Parameter `name=value` nehmen,
etwa `include`:

```tpl
[{include uri='file:var/tmp/examples/tl-smarty/parts/greeting.tpl' who='single tag' /}]
```

Ausgabe auf alpha:

```text
[<p>Hello, single tag!</p>
]
```

## Parserfehler: Zeile und Spalte

Parserfehler nennen das Template, die Zeile und in eckigen Klammern die Spalte, dann das Problem. Ein nicht abgeschlossenes Tag:

```tpl
first line
second {$broken
```

Ausgabe auf alpha:

```text
first line
second {$broken
```

Von der Template-Engine protokolliert:

```text
ERROR : parser error @ tl-lex/06-unterminated-tag.tpl:1[0]
Unterminated tag, needs a } to end the tag.
{$broken
```

Der Text vom kaputten Tag bis zum Ende der Datei wird so ausgegeben, wie er ist. Die Position ist die, an der der Parser aufgegeben hat, was
bei einem nicht abgeschlossenen Tag der Anfang des Texts davor ist; sehen Sie sich das zitierte Tag nach der Meldung an.

## Unbekannte Funktions-Tags

Ein Funktions-Tag mit einem Namen, den die Engine nicht kennt, erzeugt eine Warnung (`Function ... is not registered`) und keine Ausgabe. Weil
ein normales Tag einen Block öffnet, gehört alles bis zu seinem End-Tag, oder bis zum Ende der Datei, wenn es keines gibt, zu diesem Block und
geht mit ihm verloren:

```tpl
[before {nosuchfunction param=1} after]
```

Ausgabe auf alpha:

```text
[before
```

Von der Template-Engine protokolliert:

```text
WARNING : Function "nosuchfunction" is not registered
```

Ein falsch geschriebenes `{fi}` oder `{foreahc ...}` verschluckt daher den Rest des Templates. Wenn eine Seite zu früh endet, suchen Sie in
der Debug-Ausgabe nach einer solchen Warnung.

## Verwandte Seiten

- Zurück: [Ein Template von Smarty umziehen](ein-template-von-smarty-umziehen.md)
- Weiter: [Werte und Literale](werte-und-literale.md)
- [Grundlagen der TPL-Syntax](../../../en/us/help/designers/templates/tpl-syntax-basics.md)
- [Kommentare](../../../en/us/help/designers/templates/comments.md)
- [Literal und Begrenzer](../../../en/us/help/designers/templates/literal-and-delimiters.md)
- [Fehlersuche in Templates](../../../en/us/help/designers/templates/template-debugging.md)
- [Freie Dokumentation](../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
