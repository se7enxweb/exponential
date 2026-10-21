---
title: Template-Operator exp_environment_badge
audience: developer, admin
category: examples
summary: "Der Template-Operator exp_environment_badge: keine benannten Parameter, registriert von expEnvironmentOperator."
keywords: Template-Operator, Operator, exp_environment_badge, template operator, operator, tpl, expEnvironmentOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator exp_environment_badge

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `expEnvironmentOperator` (`kernel/common/expenvironmentoperator.php`).
Dieselbe Klasse registriert auch: [exp_environment](exp_environment.md).

## Parameter

Keine.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|exp_environment_badge}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin4/templates/page_topmenu.tpl` Zeile 5:

```tpl
{def $a4_brand_env = exp_environment_badge().level|ne( '' )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/expenvironmentoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
