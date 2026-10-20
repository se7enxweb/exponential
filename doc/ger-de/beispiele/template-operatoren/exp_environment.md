---
title: Template-Operator exp_environment
audience: developer, admin
category: examples
summary: "Der Template-Operator exp_environment: keine benannten Parameter, registriert von expEnvironmentOperator."
keywords: Template-Operator, Operator, exp_environment, template operator, operator, tpl, expEnvironmentOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator exp_environment

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `expEnvironmentOperator` (`kernel/common/expenvironmentoperator.php`).
Dieselbe Klasse registriert auch: [exp_environment_badge](exp_environment_badge.md).

## Parameter

Keine.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|exp_environment}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/expenvironmentoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
