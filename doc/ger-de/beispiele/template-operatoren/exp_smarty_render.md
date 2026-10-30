---
title: Template-Operator exp_smarty_render
audience: developer, admin
category: examples
summary: "Der Template-Operator exp_smarty_render: 1 Parameter, registriert von expSmartyOperator."
keywords: Template-Operator, Operator, exp_smarty_render, template operator, operator, tpl, expSmartyOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator exp_smarty_render

Registriert in `lib/smarty/classes/eztemplateautoload.php` von der Klasse `expSmartyOperator` (`lib/smarty/classes/expsmartyoperator.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `file` | string | ja | `''` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|exp_smarty_render( 'file' )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/smarty/classes/eztemplateautoload.php`, `lib/smarty/classes/expsmartyoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
