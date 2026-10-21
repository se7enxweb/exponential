---
title: Template-Operator simpletags
audience: developer, admin
category: examples
summary: "Der Template-Operator simpletags: 1 Parameter, registriert von eZSimpleTagsOperator."
keywords: Template-Operator, Operator, simpletags, template operator, operator, tpl, eZSimpleTagsOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator simpletags

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `eZSimpleTagsOperator` (`kernel/common/ezsimpletagsoperator.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `listname` | string | nein | `false` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|simpletags( 'listname' )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/base/override/templates/full/forum_reply.tpl` Zeile 57:

```tpl
{$node.data_map.message.content|simpletags|wordtoimage|autolink}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/ezsimpletagsoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
