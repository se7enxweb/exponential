---
title: Template-Funktion include
audience: developer, admin
category: examples
summary: Die Template-Funktion include, registriert von eZTemplateIncludeFunction.
keywords: Template-Funktion, include, template function, tpl, eZTemplateIncludeFunction
type: generated
generated: exp:help:examples
---
# Die Template-Funktion include

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateIncludeFunction` (`lib/eztemplate/classes/eztemplateincludefunction.php`).

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/override/templates/template_look_edit.tpl` Zeile 6:

```tpl
{include uri='design:content/edit_menu.tpl'}
```

## Siehe auch

- [Alle Template-Funktionen](README.md)
- [Template-Operatoren](../template-operatoren/README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplateincludefunction.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
