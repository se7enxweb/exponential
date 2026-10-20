---
title: Template-Operator node_encode
audience: developer, admin
category: examples
summary: "Der Template-Operator node_encode: 3 Parameter, registriert von ezjscEncodingTemplateFunctions."
keywords: Template-Operator, Operator, node_encode, template operator, operator, tpl, ezjscEncodingTemplateFunctions
type: generated
generated: exp:help:examples
---
# Der Template-Operator node_encode

Registriert in `extension/ezjscore/autoloads/eztemplateautoload.php` von der Klasse `ezjscEncodingTemplateFunctions` (`extension/ezjscore/autoloads/ezjscencodingtemplatefunctions.php`).
Dieselbe Klasse registriert auch: [json_encode](json_encode.md), [xml_encode](xml_encode.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `node` | object | ja | `array()` |
| `params` | hash | nein | `array()` |
| `type` | string | nein | `'json'` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|node_encode( $node, hash(), 'json' )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/autoloads/eztemplateautoload.php`, `extension/ezjscore/autoloads/ezjscencodingtemplatefunctions.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
