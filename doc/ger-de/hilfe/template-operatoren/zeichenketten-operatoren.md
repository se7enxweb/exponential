---
title: Zeichenketten-Operatoren
audience: developer, designer, app-creator
category: development
order: 150
summary: Die 15 Operatoren von eZTemplateStringOperator (upcase, downcase, upfirst, upword, count_words, count_chars, trim, simplify, pad, shorten, wrap, break, wash, ord, chr), jeder wash-Typ (xhtml, email, javascript, pdf) und auf alpha ausgegebene Beispiele.
keywords: Zeichenkette, Text, Groß- und Kleinschreibung, Kürzen, Zeilenumbruch, Maskieren, Escaping, string, text, upcase, downcase, upfirst, upword, count_words, count_chars, trim, simplify, pad, shorten, wrap, break, wash, escaping, xhtml, email, javascript, pdf, ord, chr, eZTemplateStringOperator
type: help
---
# Zeichenketten-Operatoren

Die Textoperatoren, die ein Template am häufigsten verwendet, darunter `wash`, der Maskierungsoperator, den jedes
Template braucht, das Inhalte von Benutzern ausgibt. Alle 15 registriert `eZTemplateStringOperator`
(`lib/eztemplate/classes/eztemplatestringoperator.php`) über `lib/eztemplate/classes/eztemplateautoload.php`.

Alle arbeiten mit der **Eingabe über die Pipe**. Eine Eingabe `null` (ein nicht gesetztes Attribut) wird als leere
Zeichenkette behandelt. Wo ein Operator eine Multibyte-Variante hat (`mb_strtoupper`, `mb_strlen`, `mb_substr`), wird
sie verwendet, wenn die Erweiterung `mbstring` geladen ist, sodass Buchstaben mit Akzent als ein Zeichen behandelt
werden.

Die PHP-Zeichenkettenfunktionen wie `strlen`, `substr` oder `sprintf` sind eine andere Familie:
[PHP-Zeichenkettenfunktionen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md).

## Inhalt

1. [Groß- und Kleinschreibung: upcase, downcase, upfirst und upword](#groß--und-kleinschreibung-upcase-downcase-upfirst-und-upword)
2. [Zählen: count_words und count_chars](#zählen-count_words-und-count_chars)
3. [Bereinigen: trim, simplify und pad](#bereinigen-trim-simplify-und-pad)
4. [shorten](#shorten)
5. [Zeilenumbrüche: wrap und break](#zeilenumbrüche-wrap-und-break)
6. [wash](#wash)
7. [Codepunkte: ord und chr](#codepunkte-ord-und-chr)
8. [Verwandte Seiten](#verwandte-seiten)

## Groß- und Kleinschreibung: upcase, downcase, upfirst und upword

### upcase

Die Eingabe in Großbuchstaben (`mb_strtoupper`). Keine Parameter. Gibt eine Zeichenkette zurück.
Erzeugte Referenz: [upcase](../../../eng-us/examples/template-operators/upcase.md).

### downcase

Die Eingabe in Kleinbuchstaben (`mb_strtolower`). Keine Parameter. Gibt eine Zeichenkette zurück.
Erzeugte Referenz: [downcase](../../../eng-us/examples/template-operators/downcase.md).

### upfirst

Das erste Zeichen groß, der Rest unverändert. Mit `[CharacterSettings] MBStringExtension=enabled` in `i18n.ini`
arbeitet er mit `mb_*`-Funktionen auf dem internen Zeichensatz, sonst mit `ucfirst()`. Keine Parameter.
Erzeugte Referenz: [upfirst](../../../eng-us/examples/template-operators/upfirst.md).

### upword

Das erste Zeichen jedes Wortes (Wörter an einzelnen Leerzeichen getrennt) groß, mit derselben Multibyte-Regel. Keine
Parameter. Erzeugte Referenz: [upword](../../../eng-us/examples/template-operators/upword.md).

```tpl
'fit & healthy'|upcase = {'fit & healthy'|upcase|wash}
'ÜBER Exponential'|downcase = {'ÜBER Exponential'|downcase}
'élan vital'|upfirst = {'élan vital'|upfirst}
'healthy eating for runners'|upword = {'healthy eating for runners'|upword}
```

Ausgabe auf alpha:

```text
'fit & healthy'|upcase = FIT &amp; HEALTHY
'ÜBER Exponential'|downcase = über exponential
'élan vital'|upfirst = Élan vital
'healthy eating for runners'|upword = Healthy Eating For Runners
```

## Zählen: count_words und count_chars

### count_words

Die Anzahl der Wörter, gezählt als Folgen von Wortzeichen (`preg_match_all( "#(\w+)#" )`), daher ist `&` kein Wort.
Keine Parameter. Gibt eine Ganzzahl zurück.
Erzeugte Referenz: [count_words](../../../eng-us/examples/template-operators/count_words.md).

### count_chars

Die Anzahl der Zeichen (`mb_strlen`). Anders als das arithmetische `count`, das bei einer Zeichenkette die Länge in
Bytes zurückgibt, zählt `count_chars` ein Multibyte-Zeichen einmal. Keine Parameter. Gibt eine Ganzzahl zurück.
Erzeugte Referenz: [count_chars](../../../eng-us/examples/template-operators/count_chars.md).

```tpl
'Fit & Healthy recipes'|count_words = {'Fit & Healthy recipes'|count_words}
'Grüße'|count_chars = {'Grüße'|count_chars}
'Grüße'|count = {'Grüße'|count} (the arithmetic count: bytes)
```

Ausgabe auf alpha:

```text
'Fit & Healthy recipes'|count_words = 3
'Grüße'|count_chars = 5
'Grüße'|count = 7 (the arithmetic count: bytes)
```

## Bereinigen: trim, simplify und pad

### trim

PHP `trim()`: entfernt Leerraum oder die Zeichen in `chars_to_remove` an beiden Enden.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `chars_to_remove` | Zeichenkette | nein | `false` (Leerraum) |

Erzeugte Referenz: [trim](../../../eng-us/examples/template-operators/trim.md).

### simplify

Ersetzt Folgen von zwei oder mehr gleichen Zeichen durch eines. Ohne Parameter fasst er Leerzeichen zusammen. Der
Parameter wird unverändert in einen regulären Ausdruck eingesetzt, übergeben Sie also ein einfaches Zeichen und keines,
das in einem Muster eine Bedeutung hat.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `char` | Zeichenkette | nein | `false` (ein Leerzeichen) |

Erzeugte Referenz: [simplify](../../../eng-us/examples/template-operators/simplify.md).

### pad

PHP `str_pad()` nach rechts, aber nur, wenn die Eingabe kürzer als `desired_length` ist (in Bytes gemessen).

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `desired_length` | Ganzzahl | nein | `80` |
| `pad_sequence` | Zeichenkette | nein | `' '` |

Erzeugte Referenz: [pad](../../../eng-us/examples/template-operators/pad.md).

```tpl
[{'   padded   '|trim}]
[{'--title--'|trim( '-' )}]
[{'too    many     spaces'|simplify}]
[{'a---b--c'|simplify( '-' )}]
[{'7'|pad( 3, '0' )}]
[{'Name'|pad( 10 )}]
[{'already long'|pad( 4 )}]
```

Ausgabe auf alpha:

```text
[padded]
[title]
[too many spaces]
[a-b-c]
[700]
[Name      ]
[already long]
```

## shorten

### shorten

Kürzt einen Text, der länger als `chars_to_keep` Zeichen ist, sodass das Ergebnis einschließlich `str_to_append`
höchstens diese Länge hat. Mit `trim_type` `right` (Standard) schneidet er das Ende ab und entfernt vor dem Anhängen
Leerraum am Ende; mit `middle` behält er Anfang und Ende und setzt `str_to_append` dazwischen. Ein Text, der kurz
genug ist, wird unverändert zurückgegeben.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `chars_to_keep` | Ganzzahl | nein | `80` |
| `str_to_append` | Zeichenkette | nein | `'...'` |
| `trim_type` | Zeichenkette (`right` oder `middle`) | nein | `'right'` |

Gibt eine Zeichenkette zurück. `shorten` zählt Zeichen, keine Wörter, und kann ein Wort oder ein HTML-Tag in der Mitte
zerschneiden: Kürzen Sie reinen Text und wenden Sie danach `wash` an.
Erzeugte Referenz: [shorten](../../../eng-us/examples/template-operators/shorten.md).

Die erste Zeile verwendet den Namen des Artikels Privacy Policy auf alpha (Knoten 90):

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 90 ) )}
{$node.name|shorten( 10 )}
{'A long title that does not fit'|shorten( 20 )}
{'A long title that does not fit'|shorten( 20, ' [more]' )}
{'A long title that does not fit'|shorten( 20, '...', 'middle' )}
{'Short'|shorten( 20 )}
```

Ausgabe auf alpha:

```text
Privacy...
A long title that...
A long title [more]
A long t...s not fit
Short
```

## Zeilenumbrüche: wrap und break

### wrap

PHP `wordwrap()`. Jeder Parameter wird nur übergeben, wenn der vorherige angegeben und nicht leer ist.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `wrap_at_position` | Ganzzahl | nein | `false` (75 von PHP) |
| `break_sequence` | Zeichenkette | nein | `false` (`"\n"` von PHP) |
| `cut` | boolescher Wert | nein | `false` |

Erzeugte Referenz: [wrap](../../../eng-us/examples/template-operators/wrap.md).

### break

PHP `nl2br()`: ein HTML-Zeilenumbruch vor jedem Zeilenwechsel. Keine Parameter. `break` maskiert nicht: Wenden Sie
zuerst `wash` an, `$text|wash|break`, sonst geht das HTML der Eingabe durch.
Erzeugte Referenz: [break](../../../eng-us/examples/template-operators/break.md).

```tpl
{'The quick brown fox jumps over the lazy dog'|wrap( 15 )}
---
{'The quick brown fox'|wrap( 10, '<br />' )}
---
{'Supercalifragilistic'|wrap( 6, '|', true() )}
---
{"line one
line two"|break}
```

Ausgabe auf alpha:

```text
The quick brown
fox jumps over
the lazy dog
---
The quick<br />brown fox
---
Superc|alifra|gilist|ic
---
line one<br />
line two
```

## wash

### wash

Macht Text für eine bestimmte Art von Ausgabe sicher. Der Typ ist der einzige Parameter.

| Parameter | Typ | Pflicht | Standard |
|---|---|---|---|
| `type` | Zeichenkette | nein | `'xhtml'` |

| Typ | Was er tut (`eZTemplateStringOperator::wash()`) |
|---|---|
| `xhtml` | `htmlspecialchars()` mit `ENT_QUOTES`: `<`, `>`, `&`, `"` und `'` werden zu Entitäten |
| `email` | die Maskierung von `xhtml`, dann werden `.` und `@` durch `[WashSettings] EmailDotText` und `EmailAtText` der `template.ini` ersetzt, um Adressen vor Sammlern zu verbergen |
| `javascript` | `eZTemplateStringOperator::javascriptString()`: der Inhalt eines JavaScript-Zeichenkettenliterals; Backslash, beide Anführungszeichen, `<`, `>`, `&`, Zeilenumbrüche und die Unicode-Zeilen- und Absatztrenner werden maskiert |
| `pdf` | für den PDF-Export: HTML-Entitäten werden dekodiert, dann werden Leerzeichen, Tabulatoren und Zeilenwechsel zu den Callbacks `<C:callSpace>`, `<C:callTab>` und `<C:callNewLine>` der PDF-Bibliothek |

Jeder andere Typ gibt die Eingabe **unverändert** zurück, ohne Maskierung und ohne Warnung, ein Tippfehler im Typ ist
also ein Sicherheitsproblem. Auf alpha ist `EmailAtText` auf ein `spamfilter`-Span gesetzt, wie das Beispiel zeigt.

Ein kompiliertes Template macht aus `wash` mit einem konstanten Typ Inline-PHP (`washTransformation()`); der
kompilierte Zweig für `pdf` ersetzt nur Leerraum und dekodiert vorher keine Entitäten.
Erzeugte Referenz: [wash](../../../eng-us/examples/template-operators/wash.md).

```tpl
xhtml:      {'<b>"Fit" & \'Healthy\'</b>'|wash}
email:      {'info@example.com'|wash( 'email' )}
javascript: {'He said "hi" </script>'|wash( 'javascript' )}
pdf:        {'85 g&nbsp;spinach	end'|wash( 'pdf' )}
unknown:    {'<b>raw</b>'|wash( 'no-such-type' )}
```

Ausgabe auf alpha:

```text
xhtml:      &lt;b&gt;&quot;Fit&quot; &amp; &#039;Healthy&#039;&lt;/b&gt;
email:      info<span class="spamfilter">SPAMFILTER</span>@example.com
javascript: He said \042hi\042 \x3C/script\x3E
pdf:        85<C:callSpace>g spinach<C:callTab>end
unknown:    <b>raw</b>
```

## Codepunkte: ord und chr

### ord

Wandelt eine Zeichenkette über den Codec `unicode` von `eZTextCodec` in eine Liste von Unicode-Codepunkten um. Keine
Parameter. Gibt ein Array von Ganzzahlen zurück. Erzeugte Referenz: [ord](../../../eng-us/examples/template-operators/ord.md).

### chr

Die Umkehrung: eine Liste von Codepunkten zu einer Zeichenkette im internen Zeichensatz. Keine Parameter. Gibt eine
Zeichenkette zurück. Erzeugte Referenz: [chr](../../../eng-us/examples/template-operators/chr.md).

```tpl
{def $codes = 'Añ€'|ord}
'Añ€'|ord = {$codes|implode( ', ' )}
back with chr = {$codes|chr}
array( 72, 105 )|chr = {array( 72, 105 )|chr}
```

Ausgabe auf alpha:

```text
'Añ€'|ord = 65, 241, 8364
back with chr = Añ€
array( 72, 105 )|chr = Hi
```

## Verwandte Seiten

- [Zeichenketten-Operatoren für Designer](../../../eng-us/help/designers/templates/string-operators.md)
- [Wash und Maskierung](../../../eng-us/help/designers/templates/wash-and-escaping.md): welcher Typ wohin gehört
- [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md)
- [Text-Operatoren](text-operatoren.md): `concat` und `indent`
- [Alle Template-Operatoren (erzeugt)](../../../eng-us/examples/template-operators/README.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
