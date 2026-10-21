---
title: Template-Operator ezpreference
audience: developer, admin
category: examples
summary: "Der Template-Operator ezpreference: 1 Parameter, registriert von eZKernelOperator."
keywords: Template-Operator, Operator, ezpreference, template operator, operator, tpl, eZKernelOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator ezpreference

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `eZKernelOperator` (`kernel/common/ezkerneloperator.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `name` | string | ja | `false` |

Der Operator nimmt keine Eingabe: Er wird für sich aufgerufen, nicht nach `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{ezpreference( 'name' )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/override/templates/template_look_edit.tpl` Zeile 97:

```tpl
{if ezpreference( 'admin_edit_show_locations' )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/ezkerneloperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
