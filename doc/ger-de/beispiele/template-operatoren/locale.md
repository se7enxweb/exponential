---
title: Template-Operator locale
audience: developer, admin
category: examples
summary: "Der Template-Operator locale: keine benannten Parameter, registriert von eZTemplateLocaleOperator."
keywords: Template-Operator, Operator, locale, template operator, operator, tpl, eZTemplateLocaleOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator locale

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateLocaleOperator` (`lib/eztemplate/classes/eztemplatelocaleoperator.php`).
Dieselbe Klasse registriert auch: [l10n](l10n.md), [datetime](datetime.md), [currentdate](currentdate.md), [maketime](maketime.md), [makedate](makedate.md), [gettime](gettime.md).

## Parameter

Keine.

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|locale}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/content/view/versionview.tpl` Zeile 65:

```tpl
{set $locale_object = $locale_code|locale()}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatelocaleoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
