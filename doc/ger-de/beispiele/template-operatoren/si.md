---
title: Template-Operator si
audience: developer, admin
category: examples
summary: "Der Template-Operator si: 5 Parameter, registriert von eZTemplateUnitOperator."
keywords: Template-Operator, Operator, si, template operator, operator, tpl, eZTemplateUnitOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator si

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateUnitOperator` (`lib/eztemplate/classes/eztemplateunitoperator.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `unit` | string | ja | `false` |
| `prefix` | string | nein | `'auto'` |
| `decimal_count` | integer | nein | `false` |
| `decimal_symbol` | string | nein | `false` |
| `thousands_separator` | string | nein | `false` |

Der Operator arbeitet mit seiner Eingabe: dem Wert vor `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|si( 'unit', 'auto', 1, 'decimal_symbol', 'thousands_separator' )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/audit/archives.tpl` Zeile 33:

```tpl
<td>{$c.live_count} ({$c.live_bytes|si( byte )})</td>
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplateunitoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
