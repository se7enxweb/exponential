---
title: Template-Funktion shop_account_view_gui
audience: developer, admin
category: examples
summary: Die Template-Funktion shop_account_view_gui, registriert von eZObjectForwarder.
keywords: Template-Funktion, shop_account_view_gui, template function, tpl, eZObjectForwarder
type: generated
generated: exp:help:examples
---
# Die Template-Funktion shop_account_view_gui

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `eZObjectForwarder` (`kernel/common/ezobjectforwarder.php`).
Dieselbe Klasse verarbeitet auch: [attribute_edit_gui](attribute_edit_gui.md), [attribute_view_gui](attribute_view_gui.md), [attribute_result_gui](attribute_result_gui.md), [attribute_pdf_gui](attribute_pdf_gui.md), [attribute_diff_gui](attribute_diff_gui.md), [related_view_gui](related_view_gui.md), [node_view_gui](node_view_gui.md), [content_view_gui](content_view_gui.md), [content_pdf_gui](content_pdf_gui.md), [content_version_view_gui](content_version_view_gui.md), [collaboration_view_gui](collaboration_view_gui.md), [collaboration_icon](collaboration_icon.md), [collaboration_simple_message_view](collaboration_simple_message_view.md), [collaboration_participation_view](collaboration_participation_view.md), [event_edit_gui](event_edit_gui.md), [event_view_gui](event_view_gui.md), [class_attribute_view_gui](class_attribute_view_gui.md), [class_attribute_edit_gui](class_attribute_edit_gui.md).

## Wie sie ihr Template findet

Aus der Weiterleitungsregel in `eZObjectForwardInit()` (`kernel/common/eztemplateautoload.php`):

| Schlüssel | Wert |
|---|---|
| `template_root` | `'shop/accounthandlers'` |
| `input_name` | `'order'` |
| `output_name` | `'order'` |
| `namespace` | `'ShopAccount'` |
| `use_views` | `'view'` |

```tpl
{shop_account_view_gui order=$order view=view}
```

Der Variablenname ist der `input_name` der Regel; der Wert ist ein Platzhalter.

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/shop/confirmorder.tpl` Zeile 21:

```tpl
{shop_account_view_gui view=html order=$order}
```

## Siehe auch

- [Alle Template-Funktionen](README.md)
- [Template-Operatoren](../template-operatoren/README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/ezobjectforwarder.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
