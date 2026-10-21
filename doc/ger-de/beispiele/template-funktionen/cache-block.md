---
title: Template-Funktion cache-block
audience: developer, admin
category: examples
summary: Die Template-Funktion cache-block, registriert von eZTemplateCacheFunction.
keywords: Template-Funktion, cache-block, template function, tpl, eZTemplateCacheFunction
type: generated
generated: exp:help:examples
---
# Die Template-Funktion cache-block

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateCacheFunction` (`lib/eztemplate/classes/eztemplatecachefunction.php`).

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/contentstructuremenu/content_structure_menu_dynamic.tpl` Zeile 55:

```tpl
*}{cache-block keys=array( $root_node_id, $access_type, $csm_perm_key ) expiry=0}{*
```

## Siehe auch

- [Alle Template-Funktionen](README.md)
- [Template-Operatoren](../template-operatoren/README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatecachefunction.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
