---
title: Template-Operator ezscript
audience: developer, admin
category: examples
summary: "Der Template-Operator ezscript: 5 Parameter, registriert von ezjscPackerTemplateFunctions."
keywords: Template-Operator, Operator, ezscript, template operator, operator, tpl, ezjscPackerTemplateFunctions
type: generated
generated: exp:help:examples
---
# Der Template-Operator ezscript

Registriert in `extension/ezjscore/autoloads/eztemplateautoload.php` von der Klasse `ezjscPackerTemplateFunctions` (`extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php`).
Dieselbe Klasse registriert auch: [ezscript_require](ezscript_require.md), [ezscript_load](ezscript_load.md), [ezscriptfiles](ezscriptfiles.md), [ezcss](ezcss.md), [ezcss_require](ezcss_require.md), [ezcss_load](ezcss_load.md), [ezcssfiles](ezcssfiles.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `script_array` | array | ja | `array()` |
| `type` | string | nein | `'text/javascript'` |
| `language` | string | nein | `''` |
| `charset` | string | nein | `'utf-8'` |
| `pack_level` | integer | nein | `2` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|ezscript( array(), 'text/javascript', 'language', 'utf-8', 2 )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/content/datatype/edit/ezdate.tpl` Zeile 26:

```tpl
{ezscript( array( 'page/content_datepicker.js' ) )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/autoloads/eztemplateautoload.php`, `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
