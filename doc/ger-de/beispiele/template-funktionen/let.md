---
title: Template-Funktion let
audience: developer, admin
category: examples
summary: Die Template-Funktion let, registriert von eZTemplateSetFunction.
keywords: Template-Funktion, let, template function, tpl, eZTemplateSetFunction
type: generated
generated: exp:help:examples
---
# Die Template-Funktion let

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateSetFunction` (`lib/eztemplate/classes/eztemplatesetfunction.php`).
Dieselbe Klasse verarbeitet auch: [set](set.md), [default](default.md).

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `benchmarks/eztemplate/mark.tpl` Zeile 4:

```tpl
{let a=2    b=3 c=array( 1, 2, 3 )}
```

## Siehe auch

- [Alle Template-Funktionen](README.md)
- [Template-Operatoren](../template-operatoren/README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatesetfunction.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
