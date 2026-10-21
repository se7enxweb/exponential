---
title: Template-Operator rot13
audience: developer, admin
category: examples
summary: "Der Template-Operator rot13: keine benannten Parameter, registriert von eZTemplateDigestOperator."
keywords: Template-Operator, Operator, rot13, template operator, operator, tpl, eZTemplateDigestOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator rot13

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateDigestOperator` (`lib/eztemplate/classes/eztemplatedigestoperator.php`).
Dieselbe Klasse registriert auch: [crc32](crc32.md), [md5](md5.md).

## Parameter

Die Klasse beschreibt ihre Parameter nicht (kein `namedParameterList()`); was sie liest, steht in ihrem Quelltext.

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|rot13}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatedigestoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
