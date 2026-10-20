---
title: Template-Operator ezmodule
audience: developer, admin
category: examples
summary: "Der Template-Operator ezmodule: 1 Parameter, registriert von eZModuleOperator."
keywords: Template-Operator, Operator, ezmodule, template operator, operator, tpl, eZModuleOperator
type: generated
generated: exp:help:examples
---
# Der Template-Operator ezmodule

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `eZModuleOperator` (`kernel/common/ezmoduleoperator.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `uri` | string | nein | `false` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|ezmodule( 'uri' )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/class/view.tpl` Zeile 36:

```tpl
{if ezmodule( 'scriptmonitor/view' )}<a href={concat( 'scriptmonitor/view/', $scheduled_script_id )|ezurl}>{'Background process monitor'|i18n( 'design/admin/class/view' )}</a>{/if}</p>
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/ezmoduleoperator.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
