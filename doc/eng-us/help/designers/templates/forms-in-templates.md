---
title: Forms in templates
audience: designer, developer
category: templates
order: 720
summary: Forms on a public site built from content: information collection (feedback and contact forms) with ActionCollectInformation, the collect templates, validation messages, the result page and mail templates, the form token, and accessible markup.
keywords: forms, feedback form, contact form, information collection, ActionCollectInformation, collect templates, collectedinfo, validation, form token, ezformtoken, form templates
type: help
---
# Forms in templates

This page is for designers who build contact and feedback forms. In Exponential a form is usually content: a class with
fields marked as **information collectors**. Editors create the form as a page; the design draws it.

## Contents

1. [How a feedback form works](#how-a-feedback-form-works)
2. [The form template](#the-form-template)
3. [After submitting](#after-submitting)
4. [Security](#security)
5. [Accessibility](#accessibility)

## How a feedback form works

1. The full view of the form object prints a `<form>` that posts to `/content/action`.
2. Each collector field is drawn with `attribute_view_gui`, which uses `content/datatype/collect/` and prints an input.
3. The submit button is named `ActionCollectInformation`.
4. The kernel validates, stores the answers and shows a result page (`content/collectedinfo/<class>.tpl`) and can mail
   them (`content/collectedinfomail/<class>.tpl`).

## The form template

```tpl
{* override/templates/full/feedback_form.tpl *}
<h1>{$node.name|wash}</h1>
{include uri='design:content/collectedinfo_validation.tpl' class='message-warning' validation=$validation collection_attributes=$collection_attributes}
<form method="post" action={'content/action'|ezurl}>
    {foreach $node.data_map as $field}
        {if $field.contentclass_attribute.is_information_collector}
            <div class="form-field">
                <label for="ezcoa-{$field.contentclassattribute_id}_{$field.contentclass_attribute_identifier}">{$field.contentclass_attribute.name|wash}</label>
                {attribute_view_gui attribute=$field}
            </div>
        {/if}
    {/foreach}
    <input type="hidden" name="ContentNodeID" value="{$node.node_id}">
    <input type="hidden" name="ContentObjectID" value="{$node.contentobject_id}">
    <input type="hidden" name="ViewMode" value="full">
    <button type="submit" name="ActionCollectInformation">{'Send'|i18n( 'design/mytheme/form' )}</button>
</form>
```

Compare with the feedback form template of the design you build on (ezwebin's `full/feedback_form.tpl`, the media
design's forms) for the exact hidden fields and validation include your installation uses. The shipped collect
templates print inputs without ids; give your copies ids so the labels above work
([Accessible forms](../theme-design/accessible-forms.md)).

## After submitting

- `content/collectedinfo/<class>.tpl` (for example `feedback.tpl`, `form.tpl`, `poll.tpl` in `design/standard`) is the
  thank-you page; override it for your look.
- `content/collectedinfomail/<class>.tpl` builds the mail; its subject comes from `{set-block scope=root variable=subject}`.
- `collect.ini` holds the collection settings per class (where answers are mailed, whether they are stored).

## Security

- The form token extension (ezformtoken) adds a token to forms that post to the site; keep the hidden fields your
  design's original template prints.
- Validate on the server (the kernel does); do not rely on browser validation alone.
- Never print collected answers back unwashed.

## Accessibility

Labels, required marks in text, error summary with links to fields, `autocomplete` on personal fields
([Accessible forms](../theme-design/accessible-forms.md)).

## Related pages

- [attribute_view_gui](attribute-view-gui.md)
- [Datatype edit templates](datatype-edit-templates.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
