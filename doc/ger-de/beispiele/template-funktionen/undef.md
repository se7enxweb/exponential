---
title: Template-Funktion undef
audience: developer, admin
category: examples
summary: Die Template-Funktion undef, registriert von eZTemplateDefFunction.
keywords: Template-Funktion, undef, template function, tpl, eZTemplateDefFunction
type: generated
generated: exp:help:examples
---
# Die Template-Funktion undef

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateDefFunction` (`lib/eztemplate/classes/eztemplatedeffunction.php`).
Dieselbe Klasse verarbeitet auch: [def](def.md).

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/override/templates/admin_preview/link.tpl` Zeile 26:

```tpl
{undef $link_url $link_scheme}
```

## Siehe auch

- [Alle Template-Funktionen](README.md)
- [Template-Operatoren](../template-operatoren/README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatedeffunction.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
