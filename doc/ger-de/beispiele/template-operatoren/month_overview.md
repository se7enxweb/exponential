---
title: Template-Operator month_overview
audience: developer, admin
category: examples
summary: "Der Template-Operator month_overview: 3 Parameter, registriert von eZDateOperatorCollection."
keywords: Template-Operator, Operator, month_overview, template operator, operator, tpl, eZDateOperatorCollection
type: generated
generated: exp:help:examples
---
# Der Template-Operator month_overview

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `eZDateOperatorCollection` (`kernel/common/ezdateoperatorcollection.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `field` | string | ja | `false` |
| `date` | integer | ja | `false` |
| `optional` | array | nein | `false` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|month_overview( 'field', 1, array() )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/ezdateoperatorcollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
