---
title: Template-Operator image
audience: developer, admin
category: examples
summary: "Der Template-Operator image: keine benannten Parameter, registriert von eZTemplateImageOperator."
keywords: Template-Operator, Operator, image, template operator, operator, tpl, eZTemplateImageOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator image

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateImageOperator` (`lib/eztemplate/classes/eztemplateimageoperator.php`).
Dieselbe Klasse registriert auch: [texttoimage](texttoimage.md), [imagefile](imagefile.md).

## Parameter

Keine.

Der Operator nimmt keine Eingabe: Er wird für sich aufgerufen, nicht nach `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{image}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplateimageoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
