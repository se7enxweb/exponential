---
title: Template-Operator transfer_tool
audience: developer, admin
category: examples
summary: "Der Template-Operator transfer_tool: 1 Parameter, registriert von expKernelTransferOperator."
keywords: Template-Operator, Operator, transfer_tool, template operator, operator, tpl, expKernelTransferOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator transfer_tool

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `expKernelTransferOperator` (`kernel/common/expkerneltransferoperator.php`).
Dieselbe Klasse registriert auch: [transfer_tools](transfer_tools.md), [transfer_actions](transfer_actions.md), [transfer_related](transfer_related.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `tool` | string | ja | `''` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|transfer_tool( 'tool' )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/expkerneltransferoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
