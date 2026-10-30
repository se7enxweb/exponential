---
title: Template-Operator wrap
audience: developer, admin
category: examples
summary: "Der Template-Operator wrap: 3 Parameter, registriert von eZTemplateStringOperator."
keywords: Template-Operator, Operator, wrap, template operator, operator, tpl, eZTemplateStringOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator wrap

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateStringOperator` (`lib/eztemplate/classes/eztemplatestringoperator.php`).
Dieselbe Klasse registriert auch: [upcase](upcase.md), [downcase](downcase.md), [count_words](count_words.md), [count_chars](count_chars.md), [trim](trim.md), [break](break.md), [upfirst](upfirst.md), [upword](upword.md), [simplify](simplify.md), [trim](trim.md), [wash](wash.md), [chr](chr.md), [ord](ord.md), [shorten](shorten.md), [pad](pad.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `wrap_at_position` | integer | nein | `false` |
| `break_sequence` | string | nein | `false` |
| `cut` | boolean | nein | `false` |

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|wrap( 1, 'break_sequence', true() )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/content/edit_locations.tpl` Zeile 37:

```tpl
{* the table scrolls inside its column instead of overflowing it, and its headings wrap (modules/content.css, as in admin4) *}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatestringoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
