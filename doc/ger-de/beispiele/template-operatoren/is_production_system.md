---
title: Template-Operator is_production_system
audience: developer, admin
category: examples
summary: "Der Template-Operator is_production_system: keine benannten Parameter, registriert von ExpInstallationOperator."
keywords: Template-Operator, Operator, is_production_system, template operator, operator, tpl, ExpInstallationOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator is_production_system

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `ExpInstallationOperator` (`kernel/classes/expinstallationoperator.php`).
Dieselbe Klasse registriert auch: [installation_name](installation_name.md).

## Parameter

Keine.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|is_production_system}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/classes/expinstallationoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
