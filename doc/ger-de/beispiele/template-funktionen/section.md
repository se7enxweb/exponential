---
title: Template-Funktion section
audience: developer, admin
category: examples
summary: Die Template-Funktion section, registriert von eZTemplateSectionFunction.
keywords: Template-Funktion, section, template function, tpl, eZTemplateSectionFunction
type: generated
generated: exp:help:examples
---
# Die Template-Funktion section

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateSectionFunction` (`lib/eztemplate/classes/eztemplatesectionfunction.php`).

## Innere Funktionen

Innerhalb des Blocks verwendet: `{delimiter}`, `{section-exclude}`, `{section-include}`, `{section-else}`.

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `benchmarks/eztemplate/mark.tpl` Zeile 9:

```tpl
{section show=$a}
```

## Siehe auch

- [Alle Template-Funktionen](README.md)
- [Template-Operatoren](../template-operatoren/README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatesectionfunction.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
