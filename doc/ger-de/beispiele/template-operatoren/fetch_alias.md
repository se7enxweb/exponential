---
title: Template-Operator fetch_alias
audience: developer, admin
category: examples
summary: "Der Template-Operator fetch_alias: 2 Parameter, registriert von eZTemplateExecuteOperator."
keywords: Template-Operator, Operator, fetch_alias, template operator, operator, tpl, eZTemplateExecuteOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator fetch_alias

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateExecuteOperator` (`lib/eztemplate/classes/eztemplateexecuteoperator.php`).
Dieselbe Klasse registriert auch: [fetch](fetch.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `function_name` | string | ja | `false` |
| `function_parameters` | array | nein | `array()` |

Der Operator nimmt keine Eingabe: Er wird für sich aufgerufen, nicht nach `|`.

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{fetch_alias( 'function_name', array() )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/setup/templateoperator.tpl` Zeile 70:

```tpl
<span class="a4-help">{'{fetch_alias( news_list )\} — a fetch with its arguments fixed in an ini.'|i18n( 'design/admin/setup/rad/template' )}</span>
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplateexecuteoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
