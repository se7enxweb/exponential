---
title: Zeichenketten-Operatoren
audience: designer, developer
category: templates
order: 180
summary: Die klassischen Zeichenketten-Operatoren der Template-Bibliothek (upcase, downcase, upfirst, upword, trim, shorten, wrap, pad, simplify, count_chars, count_words, nl2br, concat, indent, break, chr, ord) mit Beispielen und wo die neueren, auf PHP beruhenden Zeichenketten-Operatoren dokumentiert sind.
keywords: Zeichenketten-Operatoren, upcase, downcase, upfirst, upword, trim, shorten, wrap, pad, simplify, count_words, count_chars, nl2br, concat, indent, Textoperatoren, string operators, text operators
type: help
---
# Zeichenketten-Operatoren

Diese Seite ist für Designer, die in Templates Text formen. Diese Operatoren stammen aus `eZTemplateStringOperator`,
`eZTemplateTextOperator` und `eZTemplateNl2BrOperator` (`lib/eztemplate/classes/`). Seit 6.0.13 sind auch etwa sechzig
PHP-Zeichenkettenfunktionen Operatoren ([PHP-Zeichenkettenfunktionen als Operatoren](php-zeichenkettenfunktionen-als-operatoren.md)).

## Inhalt

1. [Groß- und Kleinschreibung](#groß--und-kleinschreibung)
2. [Länge und Kürzen](#länge-und-kürzen)
3. [Verbinden und Layout](#verbinden-und-layout)
4. [Zählen](#zählen)
5. [Maskierung kommt zuletzt](#maskierung-kommt-zuletzt)

## Groß- und Kleinschreibung

| Operator | Ergebnis für `'hello world'` |
|---|---|
| `upcase` | `HELLO WORLD` |
| `downcase` | `hello world` |
| `upfirst` | `Hello world` |
| `upword` | `Hello World` |

Ziehen Sie für rein optische Schreibweise CSS `text-transform` vor: Screenreader und Kopieren behalten den ursprünglichen Text.

## Länge und Kürzen

```tpl
{$node.name|shorten( 60 )}                 {* at most 60 characters, '...' added when cut *}
{$text|shorten( 120, '…' )}                {* own ending *}
{$text|trim}                               {* spaces removed at both ends *}
{$text|simplify}                           {* runs of spaces reduced to one *}
{$code|pad( 6, '0' )}                      {* str_pad: '42' becomes '420000' *}
{$text|wrap( 72 )}                         {* wordwrap at 72 characters *}
```

Standardwerte von `shorten`: Länge 80, Folge `...`, rechts abgeschnitten (aus dem Code des Operators). Kürzen Sie bei Rich Text
den reinen Text: `{$node.data_map.intro.content.output.output_text|strip_tags|shorten( 160 )|wash}`.

## Verbinden und Layout

```tpl
{concat( $first, ' ', $last )}
{$text|nl2br}            {* line breaks to <br>: wash first *}
{$text|break}            {* the same as nl2br in the string operator *}
{'x'|indent( 4 )}        {* indentation *}
```

`chr` und `ord` wandeln zwischen Zeichen und Codes um.

## Zählen

```tpl
{$text|count_words}
{$text|count_chars}       {* multibyte aware: mb_strlen when available *}
```

## Maskierung kommt zuletzt

Erst formen, dann maskieren: `{$text|shorten( 100 )|wash}`. Bei `nl2br` maskieren Sie davor, damit das `<br>` erhalten bleibt:
`{$text|wash|nl2br}` ([Wash und Maskierung](wash-und-maskierung.md)).

## Verwandte Seiten

- [PHP-Zeichenkettenfunktionen als Operatoren](php-zeichenkettenfunktionen-als-operatoren.md)
- [Wash und Maskierung](wash-und-maskierung.md)
- [Operatorreferenz](operatorreferenz.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
