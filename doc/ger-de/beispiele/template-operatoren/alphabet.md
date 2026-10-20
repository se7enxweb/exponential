---
title: Template-Operator alphabet
audience: developer, admin
category: examples
summary: "Der Template-Operator alphabet: keine benannten Parameter, registriert von eZAlphabetOperator."
keywords: Template-Operator, Operator, alphabet, template operator, operator, tpl, eZAlphabetOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator alphabet

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `eZAlphabetOperator` (`kernel/common/ezalphabetoperator.php`).

## Parameter

Die Klasse beschreibt ihre Parameter nicht (kein `namedParameterList()`); was sie liest, steht in ihrem Quelltext.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|alphabet}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/ezalphabetoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
