---
title: Template-Funktion while
audience: developer, admin
category: examples
summary: Die Template-Funktion while, registriert von eZTemplateWhileFunction.
keywords: Template-Funktion, while, template function, tpl, eZTemplateWhileFunction
type: generated
generated: exp:help:examples
---
# Die Template-Funktion while

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateWhileFunction` (`lib/eztemplate/classes/eztemplatewhilefunction.php`).

## Innere Funktionen

Innerhalb des Blocks verwendet: `{delimiter}`, `{break}`, `{continue}`, `{skip}`.

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/base/override/templates/full/image.tpl` Zeile 27:

```tpl
{while is_set( $siblings[$index] )}
```

## Siehe auch

- [Alle Template-Funktionen](README.md)
- [Template-Operatoren](../template-operatoren/README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatewhilefunction.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
