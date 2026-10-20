---
title: Template-Operator language_switcher
audience: developer, admin
category: examples
summary: "Der Template-Operator language_switcher: 1 Parameter, registriert von ezpLanguageSwitcherOperator."
keywords: Template-Operator, Operator, language_switcher, template operator, operator, tpl, ezpLanguageSwitcherOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator language_switcher

Registriert in `kernel/private/eztemplate/eztemplateautoload.php` von der Klasse `ezpLanguageSwitcherOperator` (`kernel/private/eztemplate/ezplanguageswitcheroperator.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `destination` | string | nein | `''` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|language_switcher( 'destination' )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/private/eztemplate/eztemplateautoload.php`, `kernel/private/eztemplate/ezplanguageswitcheroperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
