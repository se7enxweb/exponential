---
title: Template-Funktion def
audience: developer, admin
category: examples
summary: Die Template-Funktion def, registriert von eZTemplateDefFunction.
keywords: Template-Funktion, def, template function, tpl, eZTemplateDefFunction
type: generated
generated: exp:help:examples
---
# Die Template-Funktion def

Registriert in `lib/eztemplate/classes/eztemplateautoload.php` von der Klasse `eZTemplateDefFunction` (`lib/eztemplate/classes/eztemplatedeffunction.php`).
Dieselbe Klasse verarbeitet auch: [undef](undef.md).

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/override/templates/admin_preview/multiprice_product.tpl` Zeile 31:

```tpl
{def $product_category_attribute=ezini( 'VATSettings', 'ProductCategoryAttribute', 'shop.ini' )}
```

## Siehe auch

- [Alle Template-Funktionen](README.md)
- [Template-Operatoren](../template-operatoren/README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `lib/eztemplate/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplatedeffunction.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
