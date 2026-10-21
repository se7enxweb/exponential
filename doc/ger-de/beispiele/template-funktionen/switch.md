---
title: Template-Funktion switch
audience: developer, admin
category: examples
summary: Die Template-Funktion switch, registriert von eZTemplateSwitchFunction.
keywords: Template-Funktion, switch, template function, tpl, eZTemplateSwitchFunction
type: generated
generated: exp:help:examples
---
# Die Template-Funktion switch

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateSwitchFunction` (`lib/eztemplate/classes/eztemplateswitchfunction.php`).

## Innere Funktionen

Innerhalb des Blocks verwendet: `{case}`.

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `benchmarks/eztemplate/mark.tpl` Zeile 29:

```tpl
{switch match=$a}
```

## Siehe auch

- [Alle Template-Funktionen](README.md)
- [Template-Operatoren](../template-operatoren/README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplateswitchfunction.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
