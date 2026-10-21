---
title: Template-Operator wash
audience: developer, admin
category: examples
summary: "Der Template-Operator wash: 1 Parameter, registriert von eZTemplateStringOperator."
keywords: Template-Operator, Operator, wash, template operator, operator, tpl, eZTemplateStringOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator wash

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateStringOperator` (`lib/eztemplate/classes/eztemplatestringoperator.php`).
Dieselbe Klasse registriert auch: [upcase](upcase.md), [downcase](downcase.md), [count_words](count_words.md), [count_chars](count_chars.md), [trim](trim.md), [break](break.md), [wrap](wrap.md), [upfirst](upfirst.md), [upword](upword.md), [simplify](simplify.md), [trim](trim.md), [chr](chr.md), [ord](ord.md), [shorten](shorten.md), [pad](pad.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `type` | string | nein | `'xhtml'` |

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|wash( 'xhtml' )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/override/templates/admin_preview/article.tpl` Zeile 5:

```tpl
<h1>{$node.data_map.title.content|wash()}</h1>
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatestringoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
