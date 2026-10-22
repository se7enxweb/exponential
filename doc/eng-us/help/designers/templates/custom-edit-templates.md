---
title: Custom edit templates
audience: designer, developer
category: templates
order: 727
summary: Give one content class its own edit form on the public site with an override of content/edit.tpl - the override rule, the variables the edit template receives, the form fields and buttons it must keep, and a short example for a comment form.
keywords: edit template, content/edit.tpl, edit form, override, class_identifier, attribute_edit_gui, content_attributes_data_map, PublishButton, DiscardButton, StoreButton, front-end editing, RedirectURIAfterPublish
type: help
---
# Custom edit templates

This page is for designers who want a simpler edit form on the public site for one class, for example a comment
form with three fields instead of the general form with its side panel. The administration keeps its own form; the
change applies to the siteaccess whose override settings you change.

## Contents

- [The override rule](#the-override-rule)
- [What the edit template receives](#what-the-edit-template-receives)
- [What the form must keep](#what-the-form-must-keep)
- [An example](#an-example)
- [Things to keep in mind](#things-to-keep-in-mind)

## The override rule

The edit view renders `content/edit.tpl`. An override rule in the public siteaccess replaces it for one class:

```ini
# settings/siteaccess/<public siteaccess>/override.ini.append.php
[edit_comment]
Source=content/edit.tpl
MatchFile=edit/comment.tpl
Subdir=templates
Match[class_identifier]=comment
```

The file then lives in your design at `override/templates/edit/comment.tpl`. Besides `class_identifier`, the edit
view offers the keys `object`, `class`, `remote_id`, `class_group`, `section`, `section_identifier`, and
`parent_class` / `parent_class_identifier` (the class of the parent of the main location), so a form can also be
chosen by place.

## What the edit template receives

| Variable | Holds |
|---|---|
| `$object` | the object being edited |
| `$class` | its content class |
| `$edit_version` | the number of the draft |
| `$edit_language` | the language code of the draft |
| `$content_attributes` | the attributes of the draft, in class order |
| `$content_attributes_data_map` | the same, keyed by attribute identifier |
| `$attribute_base` | the prefix of the attribute field names, to pass on to `attribute_edit_gui` |

`{attribute_edit_gui attribute=...}` prints the input of one attribute with the edit template of its datatype, so
the form stays correct for text, images, relations and every other type.

## What the form must keep

- **The action address.** The form posts back to the edit view of this draft:
  `content/edit/<object ID>/<version>/<language>`, built as in the example below (the language part is left out when
  `$edit_language` is empty).
- **`enctype="multipart/form-data"`** when any attribute takes a file.
- **The validation messages.** Include `design:content/edit_validation.tpl`, so a refused field is explained.
- **The buttons** by their names:

| Button name | Does |
|---|---|
| `PublishButton` | publishes the draft |
| `StoreButton` | stores the draft and keeps the form open |
| `DiscardButton` | throws the draft away |

- **The redirection fields.** Include `design:content/edit_redirection.tpl` to carry `RedirectURIAfterPublish` and
  `RedirectIfDiscarded` from the session into the form, or set them yourself as hidden fields.

## An example

```
{* override/templates/edit/comment.tpl *}
<form class="comment-edit" enctype="multipart/form-data" method="post"
      action={concat( 'content/edit/', $object.id, '/', $edit_version, '/', $edit_language|not|choose( concat( $edit_language, '/' ), '' ) )|ezurl}>

    <h2>{'Write a comment'|i18n( 'design/mysite/comment' )}</h2>

    {include uri='design:content/edit_validation.tpl'}

    {foreach array( 'subject', 'author', 'message' ) as $identifier}
        {def $attribute = $content_attributes_data_map[$identifier]}
        <div class="form-field">
            <label>{$attribute.contentclass_attribute.name|wash}</label>
            {attribute_edit_gui attribute_base=$attribute_base attribute=$attribute}
        </div>
        {undef $attribute}
    {/foreach}

    <div class="form-actions">
        <button type="submit" name="PublishButton" value="1">{'Publish comment'|i18n( 'design/mysite/comment' )}</button>
        <button type="submit" name="DiscardButton" value="1">{'Cancel'|i18n( 'design/mysite/comment' )}</button>
    </div>

    {include uri='design:content/edit_redirection.tpl'}
</form>
```

To send the user back to the article after publishing, put `RedirectURIAfterPublish` into the form that creates the
comment ([Edit, remove and create buttons on the site](edit-remove-and-create-buttons.md)): the value travels with
the draft.

## Things to keep in mind

- **Leave out only what may stay as it is.** The shipped datatypes read only the fields that are sent, so an
  attribute you do not print keeps its value, or stays empty on a new object. A required attribute that is left out
  is not checked either; print every required attribute.
- **Translations.** An attribute with translation disabled shows its value instead of an input when the user edits a
  translation; the general `content/edit_attribute.tpl` handles that case, and you can include it instead of a loop
  of your own.
- **Check the result in every language the class is edited in**, and with a user who has only the rights a visitor
  who writes comments has.
- **Clear the template override cache** after adding the rule and the new file, or the general form keeps appearing
  ([Template compilation and cache](template-compilation-and-cache.md)).

## Related pages

- [Edit, remove and create buttons on the site](edit-remove-and-create-buttons.md)
- [Datatype edit templates](datatype-edit-templates.md)
- [Override basics](override-ini-basics.md)
- [Override match keys](override-match-keys.md)
- [Forms in templates](forms-in-templates.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
