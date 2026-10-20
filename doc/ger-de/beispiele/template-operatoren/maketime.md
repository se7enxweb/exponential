---
title: Template-Operator maketime
audience: developer, admin
category: examples
summary: "Der Template-Operator maketime: 7 Parameter, registriert von eZTemplateLocaleOperator."
keywords: Template-Operator, Operator, maketime, template operator, operator, tpl, eZTemplateLocaleOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator maketime

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateLocaleOperator` (`lib/eztemplate/classes/eztemplatelocaleoperator.php`).
Dieselbe Klasse registriert auch: [l10n](l10n.md), [locale](locale.md), [datetime](datetime.md), [currentdate](currentdate.md), [makedate](makedate.md), [gettime](gettime.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `hour` | integer | nein | `false` |
| `minute` | integer | nein | `false` |
| `second` | integer | nein | `false` |
| `month` | integer | nein | `false` |
| `day` | integer | nein | `false` |
| `year` | integer | nein | `false` |
| `dst` | integer | nein | `false` |

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|maketime( 1, 1, 1, 1, 1, 1, 1 )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatelocaleoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
