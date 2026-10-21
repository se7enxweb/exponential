---
title: Template-Operator topmenu
audience: developer, admin
category: examples
summary: "Der Template-Operator topmenu: 2 Parameter, registriert von eZTopMenuOperator."
keywords: Template-Operator, Operator, topmenu, template operator, operator, tpl, eZTopMenuOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator topmenu

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `eZTopMenuOperator` (`kernel/common/eztopmenuoperator.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `context` | string | ja | `'content'` |
| `filter_on_access` | bool | nein | `false` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|topmenu( 'content', true() )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/override/templates/template_look_edit.tpl` Zeile 112:

```tpl
{section var=Menu loop=topmenu('default')}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/eztopmenuoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
