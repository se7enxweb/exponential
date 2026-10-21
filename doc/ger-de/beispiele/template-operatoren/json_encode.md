---
title: Template-Operator json_encode
audience: developer, admin
category: examples
summary: "Der Template-Operator json_encode: 1 Parameter, registriert von ezjscEncodingTemplateFunctions."
keywords: Template-Operator, Operator, json_encode, template operator, operator, tpl, ezjscEncodingTemplateFunctions
type: generated
generated: exp:help:examples
---
# Der Template-Operator json_encode

Registriert in `extension/ezjscore/autoloads/eztemplateautoload.php` von der Klasse `ezjscEncodingTemplateFunctions` (`extension/ezjscore/autoloads/ezjscencodingtemplatefunctions.php`).
Dieselbe Klasse registriert auch: [xml_encode](xml_encode.md), [node_encode](node_encode.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `hash` | hash | ja | `array()` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|json_encode( hash() )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin4/templates/children_detailed.tpl` Zeile 220:

```tpl
data-create-groups="{json_encode( $subitems_create_groups )|wash}"
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/autoloads/eztemplateautoload.php`, `extension/ezjscore/autoloads/ezjscencodingtemplatefunctions.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
