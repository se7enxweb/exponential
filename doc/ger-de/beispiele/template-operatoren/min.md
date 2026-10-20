---
title: Template-Operator min
audience: developer, admin
category: examples
summary: "Der Template-Operator min: keine benannten Parameter, registriert von eZTemplateArithmeticOperator."
keywords: Template-Operator, Operator, min, template operator, operator, tpl, eZTemplateArithmeticOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator min

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateArithmeticOperator` (`lib/eztemplate/classes/eztemplatearithmeticoperator.php`).
Dieselbe Klasse registriert auch: [sum](sum.md), [sub](sub.md), [inc](inc.md), [dec](dec.md), [div](div.md), [mod](mod.md), [mul](mul.md), [max](max.md), [abs](abs.md), [ceil](ceil.md), [floor](floor.md), [round](round.md), [int](int.md), [float](float.md), [count](count.md), [roman](roman.md), [rand](rand.md).

## Parameter

Keine.

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|min}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/audit/export.tpl` Zeile 25:

```tpl
<p class="a4-audit-note">{'The file starts at once and is sent while it is read; %count events.'|i18n( 'design/admin/audit',, hash( '%count', min( $total, $max ) ) )}</p>
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatearithmeticoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
