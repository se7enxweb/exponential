---
title: Template-Funktion default
audience: developer, admin
category: examples
summary: Die Template-Funktion default, registriert von eZTemplateSetFunction.
keywords: Template-Funktion, default, template function, tpl, eZTemplateSetFunction
type: generated
generated: exp:help:examples
---
# Die Template-Funktion default

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateSetFunction` (`lib/eztemplate/classes/eztemplatesetfunction.php`).
Dieselbe Klasse verarbeitet auch: [set](set.md), [let](let.md).

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `benchmarks/eztemplate/mark.tpl` Zeile 42:

```tpl
{default d=42}
```

## Siehe auch

- [Alle Template-Funktionen](README.md)
- [Template-Operatoren](../template-operatoren/README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatesetfunction.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
