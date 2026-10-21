---
title: "TPL-Syntax: Grundlagen"
audience: designer, developer
category: templates
order: 20
summary: Die Regeln der Template-Sprache auf einer Seite - Tags in geschweiften Klammern, Variablen mit $, Werte (Zeichenketten, Zahlen, Arrays, Hashes, boolesche Werte), Operatoren mit Pipe- oder Aufrufsyntax, Funktionen mit Parametern, Kommentare, Leerraum und die Klammern, die Sie maskieren müssen.
keywords: TPL-Syntax, Template-Syntax, geschweifte Klammern, Tags, Variablen, Zeichenketten, Zahlen, true false, array, hash, Pipe-Operator, Funktionsparameter, Kommentare, ldelim, rdelim, tpl syntax, template syntax, braces, tags, variables, strings, numbers, pipe operator, function parameters, comments
featured: true
type: help
---
# TPL-Syntax: Grundlagen

Diese Seite ist für Designer, die Templates lesen oder schreiben. Sie listet die Regeln der Template-Sprache einmal auf,
damit die anderen Seiten kurz bleiben können.

## Inhalt

1. [Text und Tags](#text-und-tags)
2. [Werte](#werte)
3. [Variablen](#variablen)
4. [Operatoren](#operatoren)
5. [Funktionen](#funktionen)
6. [Kommentare](#kommentare)
7. [Klammern in der Ausgabe](#klammern-in-der-ausgabe)

## Text und Tags

Alles außerhalb der geschweiften Klammern wird unverändert ausgegeben. Alles innerhalb von `{ }` ist ein Template-Tag:

| Tag | Bedeutung |
|---|---|
| `{$variable}` | einen Wert ausgeben |
| `{$value\|operator}` | einen Wert ausgeben, der durch Operatoren geleitet wird |
| `{function param=value}` ... `{/function}` | eine Funktion mit Inhalt |
| `{function param=value}` | eine Funktion ohne Inhalt |
| `{* ... *}` | ein Kommentar, nie ausgegeben |

## Werte

| Art | Geschrieben als |
|---|---|
| Zeichenkette | `'einfache'` oder `"doppelte"` Anführungszeichen |
| Ganzzahl, Gleitkommazahl | `42`, `3.5` |
| boolescher Wert | `true()`, `false()` (Operatoren, mit Klammern geschrieben) |
| Array | `array( 'a', 'b', 'c' )` |
| Hash | `hash( 'key', 'value', 'other', 2 )` |
| Prüfung auf nichts | `$value\|null` (wahr, wenn der Wert null ist), `is_set( $value )` |

## Variablen

Variablen beginnen mit `$`. Sie werden mit `{def}` angelegt, mit `{set}` geändert und mit `{undef}` entfernt
([Variablen und def](variablen-und-def.md)). Attribute von Objekten und Schlüssel von Hashes werden mit einem Punkt gelesen:
`$node.name`, `$node.data_map.title`, `$hash.key`; Einträge von Arrays mit einer Zahl: `$list.0`
([Attribute und Punktnotation](attribute-und-punktnotation.md)).

## Operatoren

Zwei gleichwertige Arten, einen Operator aufzurufen:

```tpl
{$text|upcase}                {* pipe: the value on the left is the input *}
{upcase( $text )}             {* call *}
{$text|shorten( 80 )}         {* pipe with parameters *}
{concat( 'a', $b, 'c' )}      {* call with several values *}
```

Operatoren lassen sich verketten: `{$text|strip_tags|shorten( 120 )|wash}`. Vergleiche und Arithmetik sind ebenfalls
Operatoren: `eq( $a, 1 )`, `gt( $count, 10 )`, `sum( $a, 2 )` ([Operatoren im Überblick](operatoren-ueberblick.md)).

## Funktionen

Funktionen steuern das Template: `{if}`, `{foreach}`, `{def}`, `{include}`, `{cache-block}`. Parameter sind Paare `name=value`
oder bei manchen Funktionen eine feste Syntax:

```tpl
{include uri='design:parts/teaser.tpl' node=$child}
{foreach $items as $item} ... {/foreach}
```

Siehe die [Funktionsreferenz](funktionsreferenz.md).

## Kommentare

```tpl
{* A comment: may span several lines, never reaches the browser. *}
```

Verwenden Sie für Notizen zum Template Template-Kommentare, nicht HTML-Kommentare (`<!-- -->`): HTML-Kommentare werden an
jeden Besucher geschickt.

## Klammern in der Ausgabe

Ein wörtliches `{` oder `}` in der Ausgabe (CSS oder JavaScript in einem Template, was Designs ohnehin vermeiden) wird als
`{ldelim}` und `{rdelim}` geschrieben, oder der Text wird in `{literal}` ... `{/literal}` eingeschlossen
([Literal und Begrenzer](literal-und-begrenzer.md)).

## Verwandte Seiten

- [Templates im Überblick](templates-ueberblick.md)
- [Variablen und def](variablen-und-def.md)
- [Operatoren im Überblick](operatoren-ueberblick.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
