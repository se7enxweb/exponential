---
title: Template-Operator view_parameter
audience: developer, admin
category: examples
summary: "Der Template-Operator view_parameter: keine benannten Parameter, registriert von expViewParameterOperator."
keywords: Template-Operator, Operator, view_parameter, template operator, operator, tpl, expViewParameterOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator view_parameter

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `expViewParameterOperator` (`kernel/common/expviewparameteroperator.php`).

## Parameter

Keine.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|view_parameter}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/contentfinder/confirm.tpl` Zeile 75:

```tpl
<p><a href={concat( 'contentfinder/search/(type)/object/(text)/', $plan.object_id|view_parameter )|ezurl}>{'Back to the finder'|i18n( 'kernel/contentfinder' )}</a></p>
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/expviewparameteroperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
