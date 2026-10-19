---
title: Datatype edit templates
audience: designer, developer
category: templates
order: 490
summary: The edit form templates of each datatype under content/datatype/edit: when a public design needs them (front-end editing, registration, user profiles), field names and ids that must be kept, accessibility of labels and errors, and no inline styles.
keywords: datatype edit templates, content/datatype/edit, edit form, attribute_edit_gui, field names, ezcoa id, front-end editing, user registration form, form markup, accessible edit form
type: help
---
# Datatype edit templates

This page is for designers whose public design shows edit forms: front-end editing, user registration, profile pages.
Each datatype has an edit template in `content/datatype/edit/<datatype>.tpl`, drawn by `{attribute_edit_gui}`.

## Contents

1. [When designs touch them](#when-designs-touch-them)
2. [What must stay](#what-must-stay)
3. [Labels and errors](#labels-and-errors)
4. [Styling](#styling)

## When designs touch them

The edit templates of `design/standard` serve the administration too: the admin designs have no edit templates of their
own for most datatypes. So a change in `design/standard` would change the administration; never edit it. A public design
meets them when visitors register (`user/register`), edit their profile, or when editors use front-end editing on the
public site; your design's copies then apply to your siteaccess only.

## What must stay

- **Field names.** The kernel reads the posted values by name, for example
  `{$attribute_base}_ezstring_data_text_{$attribute.id}` in `edit/ezstring.tpl`. Changing a name breaks saving.
- **Ids.** `ezcoa-<class attribute id>_<identifier>` ids are what labels point to.
- **Hidden inputs** the original prints.

Copy the original into your design and change markup and classes around these.

## Labels and errors

- The edit page template (`content/edit.tpl` and `content/edit_attribute.tpl`) prints the field labels; keep each label
  tied to its input ([Accessible forms](../theme-design/accessible-forms.md)).
- Validation messages come from `content/edit_validation.tpl`; place them where users see them and focus the summary.

## Styling

`tests/tests/design/StandardDatatypeEditNoStyleAttributeTest.php` checks that the standard datatype edit templates carry
no `style=""` attributes. Keep your copies free of inline styles as well; style them from your stylesheet by class.

## Related pages

- [Datatype view templates](datatype-view-templates.md)
- [Forms in templates](forms-in-templates.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
