---
title: Template-Operator ezcssfiles
audience: developer, admin
category: examples
summary: "Der Template-Operator ezcssfiles: 3 Parameter, registriert von ezjscPackerTemplateFunctions."
keywords: Template-Operator, Operator, ezcssfiles, template operator, operator, tpl, ezjscPackerTemplateFunctions
type: generated
generated: exp:help:examples
---
# Der Template-Operator ezcssfiles

Registriert in `extension/ezjscore/autoloads/eztemplateautoload.php` von der Klasse `ezjscPackerTemplateFunctions` (`extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php`).
Dieselbe Klasse registriert auch: [ezscript](ezscript.md), [ezscript_require](ezscript_require.md), [ezscript_load](ezscript_load.md), [ezscriptfiles](ezscriptfiles.md), [ezcss](ezcss.md), [ezcss_require](ezcss_require.md), [ezcss_load](ezcss_load.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `css_array` | array | ja | `array()` |
| `pack_level` | integer | nein | `3` |
| `ignore_loaded` | bool | nein | `false` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|ezcssfiles( array(), 3, true() )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `extension/ezoe/design/standard/templates/content/datatype/edit/ezxmltext_ezoe.tpl` Zeile 125:

```tpl
theme_ez_editor_css : '{ezcssfiles( $editor_css_list, 3, true() )|implode(',')}',
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/autoloads/eztemplateautoload.php`, `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
