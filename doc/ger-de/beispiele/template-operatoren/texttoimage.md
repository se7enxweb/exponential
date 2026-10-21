---
title: Template-Operator texttoimage
audience: developer, admin
category: examples
summary: "Der Template-Operator texttoimage: 12 Parameter, registriert von eZTemplateImageOperator."
keywords: Template-Operator, Operator, texttoimage, template operator, operator, tpl, eZTemplateImageOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator texttoimage

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateImageOperator` (`lib/eztemplate/classes/eztemplateimageoperator.php`).
Dieselbe Klasse registriert auch: [image](image.md), [imagefile](imagefile.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `class` | string | nein | `'default'` |
| `family` | string | nein | `null` |
| `pointsize` | integer | nein | `null` |
| `angle` | integer | nein | `null` |
| `bgcolor` | mixed | nein | `null` |
| `textcolor` | mixed | nein | `null` |
| `x` | integer | nein | `null` |
| `y` | integer | nein | `null` |
| `w` | integer | nein | `null` |
| `h` | integer | nein | `null` |
| `usecache` | boolean | nein | `null` |
| `storeimage` | boolean | nein | `true` |

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|texttoimage( 'default', 'family', 1, 1, $bgcolor, $textcolor, 1, 1, 1, 1, true(), true() )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplateimageoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
