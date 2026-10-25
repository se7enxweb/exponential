---
title: Template-Operator floor
audience: developer, admin
category: examples
summary: "Der Template-Operator floor: keine benannten Parameter, registriert von eZTemplateArithmeticOperator."
keywords: Template-Operator, Operator, floor, template operator, operator, tpl, eZTemplateArithmeticOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator floor

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateArithmeticOperator` (`lib/eztemplate/classes/eztemplatearithmeticoperator.php`).
Dieselbe Klasse registriert auch: [sum](sum.md), [sub](sub.md), [inc](inc.md), [dec](dec.md), [div](div.md), [mod](mod.md), [mul](mul.md), [max](max.md), [min](min.md), [abs](abs.md), [ceil](ceil.md), [round](round.md), [int](int.md), [float](float.md), [count](count.md), [roman](roman.md), [rand](rand.md).

## Parameter

Keine.

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|floor}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/datatable/syncs.tpl` Zeile 48:

```tpl
<td>{switch match=$s.run_mode}{case match='once'}{'when started'|i18n( 'design/admin/datatable' )}{/case}{case match='live'}{'live'|i18n( 'design/admin/datatable' )}{/case}{case}{'every %minutes minutes'|i18n( 'design/admin/datatable',, hash( '%minutes', div( $s.interval_seconds, 60 )|floor ) )|wash}{/case}{/switch}</td>
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatearithmeticoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
