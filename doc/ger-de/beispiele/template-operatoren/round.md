---
title: Template-Operator round
audience: developer, admin
category: examples
summary: "Der Template-Operator round: keine benannten Parameter, registriert von eZTemplateArithmeticOperator."
keywords: Template-Operator, Operator, round, template operator, operator, tpl, eZTemplateArithmeticOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator round

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateArithmeticOperator` (`lib/eztemplate/classes/eztemplatearithmeticoperator.php`).
Dieselbe Klasse registriert auch: [sum](sum.md), [sub](sub.md), [inc](inc.md), [dec](dec.md), [div](div.md), [mod](mod.md), [mul](mul.md), [max](max.md), [min](min.md), [abs](abs.md), [ceil](ceil.md), [floor](floor.md), [int](int.md), [float](float.md), [count](count.md), [roman](roman.md), [rand](rand.md).

## Parameter

Keine.

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|round}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/audit/charts.tpl` Zeile 57:

```tpl
<span class="a4-audit-track" role="img" aria-label="{$d.day|wash}: {$d.sum}">{foreach $d.segments as $s}{if $s.n}<span class="a4-audit-seg a4-audit-s{$s.slot} a4-pct-{max( $s.pct|round, 1 )}" title="{$d.day|wash} &middot; {$s.channel|wash}: {$s.n}" tabindex="0"></span>{/if}{/foreach}</span>
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatearithmeticoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
