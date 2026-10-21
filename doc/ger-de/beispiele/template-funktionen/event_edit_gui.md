---
title: Template-Funktion event_edit_gui
audience: developer, admin
category: examples
summary: Die Template-Funktion event_edit_gui, registriert von eZObjectForwarder.
keywords: Template-Funktion, event_edit_gui, template function, tpl, eZObjectForwarder
type: generated
generated: exp:help:examples
---
# Die Template-Funktion event_edit_gui

Registriert in `kernel/common/eztemplateautoload.php` von der Klasse `eZObjectForwarder` (`kernel/common/ezobjectforwarder.php`).
Dieselbe Klasse verarbeitet auch: [attribute_edit_gui](attribute_edit_gui.md), [attribute_view_gui](attribute_view_gui.md), [attribute_result_gui](attribute_result_gui.md), [attribute_pdf_gui](attribute_pdf_gui.md), [attribute_diff_gui](attribute_diff_gui.md), [related_view_gui](related_view_gui.md), [node_view_gui](node_view_gui.md), [content_view_gui](content_view_gui.md), [content_pdf_gui](content_pdf_gui.md), [shop_account_view_gui](shop_account_view_gui.md), [content_version_view_gui](content_version_view_gui.md), [collaboration_view_gui](collaboration_view_gui.md), [collaboration_icon](collaboration_icon.md), [collaboration_simple_message_view](collaboration_simple_message_view.md), [collaboration_participation_view](collaboration_participation_view.md), [event_view_gui](event_view_gui.md), [class_attribute_view_gui](class_attribute_view_gui.md), [class_attribute_edit_gui](class_attribute_edit_gui.md).

## Wie sie ihr Template findet

Aus der Weiterleitungsregel in `eZObjectForwardInit()` (`kernel/common/eztemplateautoload.php`):

| Schlüssel | Wert |
|---|---|
| `template_root` | `'workflow/eventtype/edit'` |
| `input_name` | `'event'` |
| `output_name` | `'event'` |
| `namespace` | `'WorkflowEvent'` |
| `use_views` | `false` |

```tpl
{event_edit_gui event=$event}
```

Der Variablenname ist der `input_name` der Regel; der Wert ist ein Platzhalter.

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/workflow/edit.tpl` Zeile 104:

```tpl
{event_edit_gui event=$Events.item selectedClass=$selectedClass}
```

## Siehe auch

- [Alle Template-Funktionen](README.md)
- [Template-Operatoren](../template-operatoren/README.md)
- [Templates und Design](../../anleitungen/templates-und-design.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/common/eztemplateautoload.php`, `kernel/common/ezobjectforwarder.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
