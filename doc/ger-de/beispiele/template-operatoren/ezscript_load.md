---
title: Template-Operator ezscript_load
audience: developer, admin
category: examples
summary: "Der Template-Operator ezscript_load: 5 Parameter, registriert von ezjscPackerTemplateFunctions."
keywords: Template-Operator, Operator, ezscript_load, template operator, operator, tpl, ezjscPackerTemplateFunctions
type: generated
generated: exp:help:examples
---
# Der Template-Operator ezscript_load

Registriert in `extension/ezjscore/autoloads/eztemplateautoload.php` von der Klasse `ezjscPackerTemplateFunctions` (`extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php`).
Dieselbe Klasse registriert auch: [ezscript](ezscript.md), [ezscript_require](ezscript_require.md), [ezscriptfiles](ezscriptfiles.md), [ezcss](ezcss.md), [ezcss_require](ezcss_require.md), [ezcss_load](ezcss_load.md), [ezcssfiles](ezcssfiles.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `script_array` | array | nein | `array()` |
| `type` | string | nein | `'text/javascript'` |
| `language` | string | nein | `''` |
| `charset` | string | nein | `'utf-8'` |
| `pack_level` | integer | nein | `2` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|ezscript_load( array(), 'text/javascript', 'language', 'utf-8', 2 )}
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/page_head_script.tpl` Zeile 23:

```tpl
{ezscript_load( $admin_backend_script_list )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/autoloads/eztemplateautoload.php`, `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
