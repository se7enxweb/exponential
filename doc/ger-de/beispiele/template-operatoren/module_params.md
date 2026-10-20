---
title: Template-Operator module_params
audience: developer, admin
category: examples
summary: "Der Template-Operator module_params: keine benannten Parameter, registriert von eZModuleParamsOperator."
keywords: Template-Operator, Operator, module_params, template operator, operator, tpl, eZModuleParamsOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator module_params

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `eZModuleParamsOperator` (`kernel/common/ezmoduleparamsoperator.php`).

## Parameter

Keine.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|module_params}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/parts/content/menu.tpl` Zeile 3:

```tpl
{if ne(module_params().module_name, 'error')}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/ezmoduleparamsoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
