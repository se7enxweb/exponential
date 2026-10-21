---
title: Template-Operator eztoc
audience: developer, admin
category: examples
summary: "Der Template-Operator eztoc: 1 Parameter, registriert von eZTOCOperator."
keywords: Template-Operator, Operator, eztoc, template operator, operator, tpl, eZTOCOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator eztoc

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `eZTOCOperator` (`kernel/common/eztocoperator.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `dom` | object | ja | `0` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|eztoc( 0 )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/eztocoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
