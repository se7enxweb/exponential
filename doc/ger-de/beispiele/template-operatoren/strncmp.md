---
title: Template-Operator strncmp
audience: developer, admin
category: examples
summary: "Der Template-Operator strncmp: keine benannten Parameter, registriert von eZTemplateStringsOperator."
keywords: Template-Operator, Operator, strncmp, template operator, operator, tpl, eZTemplateStringsOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator strncmp

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateStringsOperator` (`lib/eztemplate/classes/eztemplatestringsoperator.php`).
Dieselbe Klasse registriert auch: [addslashes](addslashes.md), [bin2hex](bin2hex.md), [convert_uudecode](convert_uudecode.md), [convert_uuencode](convert_uuencode.md), [hex2bin](hex2bin.md), [lcfirst](lcfirst.md), [quoted_printable_decode](quoted_printable_decode.md), [quoted_printable_encode](quoted_printable_encode.md), [quotemeta](quotemeta.md), [soundex](soundex.md), [str_shuffle](str_shuffle.md), [stripcslashes](stripcslashes.md), [strlen](strlen.md), [stripslashes](stripslashes.md), [addcslashes](addcslashes.md), [chunk_split](chunk_split.md), [hebrev](hebrev.md), [html_entity_decode](html_entity_decode.md), [htmlentities](htmlentities.md), [htmlspecialchars_decode](htmlspecialchars_decode.md), [levenshtein](levenshtein.md), [ltrim](ltrim.md), [metaphone](metaphone.md), [number_format](number_format.md), [rtrim](rtrim.md), [similar_text](similar_text.md), [sprintf](sprintf.md), [str_contains](str_contains.md), [str_ends_with](str_ends_with.md), [str_getcsv](str_getcsv.md), [str_split](str_split.md), [str_starts_with](str_starts_with.md), [str_word_count](str_word_count.md), [strcasecmp](strcasecmp.md), [strcmp](strcmp.md), [strcoll](strcoll.md), [strcspn](strcspn.md), [strip_tags](strip_tags.md), [stripos](stripos.md), [stristr](stristr.md), [strnatcasecmp](strnatcasecmp.md), [strnatcmp](strnatcmp.md), [strncasecmp](strncasecmp.md), [strpbrk](strpbrk.md), [strpos](strpos.md), [strrchr](strrchr.md), [strripos](strripos.md), [strrpos](strrpos.md), [strspn](strspn.md), [strstr](strstr.md), [strtok](strtok.md), [strtr](strtr.md), [substr](substr.md), [substr_compare](substr_compare.md), [substr_count](substr_count.md), [substr_replace](substr_replace.md), [vsprintf](vsprintf.md), [ristring](ristring.md), [rstring](rstring.md).

## Parameter

Die Klasse beschreibt ihre Parameter nicht (kein `namedParameterList()`); was sie liest, steht in ihrem Quelltext.

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|strncmp}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatestringsoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
