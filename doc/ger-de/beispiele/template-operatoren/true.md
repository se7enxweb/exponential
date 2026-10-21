---
title: Template-Operator true
audience: developer, admin
category: examples
summary: "Der Template-Operator true: keine benannten Parameter, registriert von eZTemplateLogicOperator."
keywords: Template-Operator, Operator, true, template operator, operator, tpl, eZTemplateLogicOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator true

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateLogicOperator` (`lib/eztemplate/classes/eztemplatelogicoperator.php`).
Dieselbe Klasse registriert auch: [lt](lt.md), [gt](gt.md), [le](le.md), [ge](ge.md), [eq](eq.md), [ne](ne.md), [null](null.md), [not](not.md), [false](false.md), [or](or.md), [and](and.md), [choose](choose.md).

## Parameter

Keine.

Der Operator nimmt keine Eingabe: Er wird für sich aufgerufen, nicht nach `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{true}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/children.tpl` Zeile 56:

```tpl
{set can_remove=true()}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatelogicoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
