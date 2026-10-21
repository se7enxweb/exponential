---
title: PHP-Zeichenkettenfunktionen als Operatoren
audience: designer, developer
category: templates
order: 300
summary: Die rund sechzig PHP-Zeichenkettenfunktionen, die seit 6.0.13 als Template-Operatoren verfügbar sind (strlen, substr, str_contains, sprintf, number_format, strip_tags, ...), wie der per Pipe übergebene Wert dem ersten Argument von PHP entspricht, und Beispiele für Designer.
keywords: PHP-Zeichenketten-Operatoren, strlen, substr, str_contains, str_starts_with, sprintf, number_format, strip_tags, ucfirst, lcfirst, strpos, str_replace, eZTemplateStringsOperator, php string operators
type: help
---
# PHP-Zeichenkettenfunktionen als Operatoren

Diese Seite ist für Designer, die eine Textfunktion brauchen, die die klassischen Operatoren nicht haben. Seit Release 6.0.13
stellt die Klasse `eZTemplateStringsOperator` (`lib/eztemplate/classes/eztemplatestringsoperator.php`) etwa sechzig
PHP-Zeichenkettenfunktionen in Templates bereit. Die vollständige Liste und die Einzelheiten stehen auf der Funktionsseite
[PHP-Zeichenkettenfunktionen als Template-Operatoren](../../../../eng-us/features/6.0/string-template-operators.md).

## Inhalt

1. [Wie die Zuordnung funktioniert](#wie-die-zuordnung-funktioniert)
2. [Nützlich für Designer](#nützlich-für-designer)
3. [Hinweise](#hinweise)

## Wie die Zuordnung funktioniert

Der per Pipe übergebene Wert ist das erste Argument von PHP; die Template-Parameter folgen in der Reihenfolge von PHP:

```tpl
{def $text = 'Hello, Exponential'}
{$text|strlen}                    {* 18 *}
{$text|substr( 0, 5 )}            {* Hello *}
{$text|str_contains( 'Expo' )}    {* true *}
{$text|lcfirst}                   {* hello, Exponential *}
```

(Beispiele von der Funktionsseite).

## Nützlich für Designer

| Bedarf | Operator |
|---|---|
| reiner Text aus Rich Text | `strip_tags` (dann `shorten` und `wash`) |
| erste Zeichen | `substr( 0, 1 )` für einen Anfangsbuchstaben |
| beginnt oder endet ein Text mit | `str_starts_with`, `str_ends_with` |
| Zahl mit Trennzeichen | `number_format( 2, ',', '.' )` (oder `l10n( 'number' )` für das Format der Locale) |
| formatierter Text | `sprintf( '%02d', $n )` |
| Position einer Teilzeichenkette | `strpos`, `stripos` |
| in Zeichen aufteilen | `str_split` |

## Hinweise

- Ziehen Sie für Zahlen und Datumsangaben, die Besuchern gezeigt werden, das Locale-bewusste `l10n` vor ([Datum, Zahlen und l10n](datum-zahlen-und-l10n.md)).
- Diese Operatoren geben rohen Text zurück: Maskieren Sie vor der Ausgabe.
- Einige Funktionen gibt es nur in neueren PHP-Versionen; die Funktionsseite sagt, welche.

## Verwandte Seiten

- [Zeichenketten-Operatoren](zeichenketten-operatoren.md)
- [Operatorreferenz](operatorreferenz.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
