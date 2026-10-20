---
title: Template-Operator installation_name
audience: developer, admin
category: examples
summary: "Der Template-Operator installation_name: 1 Parameter, registriert von ExpInstallationOperator."
keywords: Template-Operator, Operator, installation_name, template operator, operator, tpl, ExpInstallationOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator installation_name

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `ExpInstallationOperator` (`kernel/classes/expinstallationoperator.php`).
Dieselbe Klasse registriert auch: [is_production_system](is_production_system.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `hide_on_prod` | boolean | nein | `false` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|installation_name( true() )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/classes/expinstallationoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
