---
title: Template-Funktion if
audience: developer, admin
category: examples
summary: Die Template-Funktion if, registriert von eZTemplateIfFunction.
keywords: Template-Funktion, if, template function, tpl, eZTemplateIfFunction
type: generated
generated: exp:help:examples
---
# Die Template-Funktion if

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateIfFunction` (`lib/eztemplate/classes/eztemplateiffunction.php`).

## Innere Funktionen

Innerhalb des Blocks verwendet: `{elseif}`, `{else}`.

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/override/templates/admin_preview/article.tpl` Zeile 8:

```tpl
{if $node.data_map.author.has_content}
```

## Siehe auch

- [Alle Template-Funktionen](README.md)
- [Template-Operatoren](../template-operatoren/README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplateiffunction.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
