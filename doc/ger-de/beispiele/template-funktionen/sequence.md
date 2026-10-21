---
title: Template-Funktion sequence
audience: developer, admin
category: examples
summary: Die Template-Funktion sequence, registriert von eZTemplateSequenceFunction.
keywords: Template-Funktion, sequence, template function, tpl, eZTemplateSequenceFunction
type: generated
generated: exp:help:examples
---
# Die Template-Funktion sequence

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateSequenceFunction` (`lib/eztemplate/classes/eztemplatesequencefunction.php`).

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/standard/templates/workflow/workflowlist.tpl` Zeile 18:

```tpl
{sequence name=WorkflowSequence loop=array(bglight,bgdark)}
```

## Siehe auch

- [Alle Template-Funktionen](README.md)
- [Template-Operatoren](../template-operatoren/README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatesequencefunction.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
