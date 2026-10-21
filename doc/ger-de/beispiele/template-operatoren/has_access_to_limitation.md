---
title: Template-Operator has_access_to_limitation
audience: developer, admin
category: examples
summary: "Der Template-Operator has_access_to_limitation: 4 Parameter, registriert von ezjscAccessTemplateFunctions."
keywords: Template-Operator, Operator, has_access_to_limitation, template operator, operator, tpl, ezjscAccessTemplateFunctions
type: generated
generated: exp:help:examples
---
# Der Template-Operator has_access_to_limitation

Registriert in `extension/ezjscore/autoloads/eztemplateautoload.php` von der Klasse `ezjscAccessTemplateFunctions` (`extension/ezjscore/autoloads/ezjscaccesstemplatefunctions.php`).

## Parameter

| Parameter | Typ | Erforderlich | Standard |
|---|---|---|---|
| `module` | string | ja | `''` |
| `function` | string | ja | `''` |
| `limitations` | array | ja | `array()` |
| `debug` | bool | nein | `false` |

## Beispiel

Aus der Parameterliste oben gebildet; die Werte sind Platzhalter (die Template-Hinweise des Operators entscheiden, ob er eine Eingabe nimmt).

```tpl
{$value|has_access_to_limitation( 'module', 'function', array(), true() )}
```

## Siehe auch

- [Alle Template-Operatoren](README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)
- [Ein eigener Template-Operator](../kochbuch/eigener-template-operator.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/autoloads/eztemplateautoload.php`, `extension/ezjscore/autoloads/ezjscaccesstemplatefunctions.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
