---
title: Template-Operator ne
audience: developer, admin
category: examples
summary: "Der Template-Operator ne: keine benannten Parameter, registriert von eZTemplateLogicOperator."
keywords: Template-Operator, Operator, ne, template operator, operator, tpl, eZTemplateLogicOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator ne

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateLogicOperator` (`lib/eztemplate/classes/eztemplatelogicoperator.php`).
Dieselbe Klasse registriert auch: [lt](lt.md), [gt](gt.md), [le](le.md), [ge](ge.md), [eq](eq.md), [null](null.md), [not](not.md), [true](true.md), [false](false.md), [or](or.md), [and](and.md), [choose](choose.md).

## Parameter

Keine.

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|ne}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/ajaxuploader/browse.tpl` Zeile 3:

```tpl
{if $browse_start.node_id|ne( 1 )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatelogicoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
