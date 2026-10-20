---
title: Template-Operator icon_info
audience: developer, admin
category: examples
summary: "Der Template-Operator icon_info: keine benannten Parameter, registriert von eZWordToImageOperator."
keywords: Template-Operator, Operator, icon_info, template operator, operator, tpl, eZWordToImageOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator icon_info

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `eZWordToImageOperator` (`kernel/common/ezwordtoimageoperator.php`).
Dieselbe Klasse registriert auch: [wordtoimage](wordtoimage.md), [mimetype_icon](mimetype_icon.md), [class_icon](class_icon.md), [classgroup_icon](classgroup_icon.md), [action_icon](action_icon.md), [icon](icon.md), [flag_icon](flag_icon.md).

## Parameter

Die Klasse beschreibt ihre Parameter nicht (kein `namedParameterList()`); was sie liest, steht in ihrem Quelltext.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|icon_info}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/ezwordtoimageoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
