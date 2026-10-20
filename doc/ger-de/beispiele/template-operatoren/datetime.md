---
title: Template-Operator datetime
audience: developer, admin
category: examples
summary: "Der Template-Operator datetime: 2 Parameter, registriert von eZTemplateLocaleOperator."
keywords: Template-Operator, Operator, datetime, template operator, operator, tpl, eZTemplateLocaleOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator datetime

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateLocaleOperator` (`lib/eztemplate/classes/eztemplatelocaleoperator.php`).
Dieselbe Klasse registriert auch: [l10n](l10n.md), [locale](locale.md), [currentdate](currentdate.md), [maketime](maketime.md), [makedate](makedate.md), [gettime](gettime.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `class` | string | ja | `false` |
| `data` | mixed | nein | `false` |

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|datetime( 'class', $data )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/exphelp/audience.tpl` Zeile 127:

```tpl
{if and( is_set( $change.time ), $change.time|gt( 0 ) )}<time datetime="{$change.time|datetime( 'custom', '%Y-%m-%dT%H:%i:%s' )}">{$change.time|l10n( 'shortdate' )}</time>{/if}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatelocaleoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
