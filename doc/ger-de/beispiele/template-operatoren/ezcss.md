---
title: Template-Operator ezcss
audience: developer, admin
category: examples
summary: "Der Template-Operator ezcss: 6 Parameter, registriert von ezjscPackerTemplateFunctions."
keywords: Template-Operator, Operator, ezcss, template operator, operator, tpl, ezjscPackerTemplateFunctions
type: generated
generated: exp:help:examples
---
# Der Template-Operator ezcss

Registriert in `extension/ezjscore/autoloads/eztemplateautoload.php` von der Klasse `ezjscPackerTemplateFunctions` (`extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php`).
Dieselbe Klasse registriert auch: [ezscript](ezscript.md), [ezscript_require](ezscript_require.md), [ezscript_load](ezscript_load.md), [ezscriptfiles](ezscriptfiles.md), [ezcss_require](ezcss_require.md), [ezcss_load](ezcss_load.md), [ezcssfiles](ezcssfiles.md).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `css_array` | array | ja | `array()` |
| `media` | string | nein | `'all'` |
| `type` | string | nein | `'text/css'` |
| `rel` | string | nein | `'stylesheet'` |
| `charset` | string | nein | `''` |
| `pack_level` | integer | nein | `3` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|ezcss( array(), 'all', 'text/css', 'stylesheet', 'charset', 3 )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../../../eng-us/examples/cookbook/custom-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/autoloads/eztemplateautoload.php`, `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
