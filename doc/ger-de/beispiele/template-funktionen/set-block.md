---
title: Template-Funktion set-block
audience: developer, admin
category: examples
summary: Die Template-Funktion set-block, registriert von eZTemplateBlockFunction.
keywords: Template-Funktion, set-block, template function, tpl, eZTemplateBlockFunction
type: generated
generated: exp:help:examples
---
# Die Template-Funktion set-block

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateBlockFunction` (`lib/eztemplate/classes/eztemplateblockfunction.php`).
Dieselbe Klasse verarbeitet auch: [append-block](append-block.md), [run-once](run-once.md).

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/collaboration/handlers/view/full/ezapprove.tpl` Zeile 12:

```tpl
{set-block variable=contentobject_link}
```

## Siehe auch

- [Alle Template-Funktionen](README.md)
- [Template-Operatoren](../template-operatoren/README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplateblockfunction.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
