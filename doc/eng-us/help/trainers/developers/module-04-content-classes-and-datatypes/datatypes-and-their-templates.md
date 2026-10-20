---
title: "Lesson 4.2: Datatypes and their templates"
audience: trainer, developer
category: training
order: 42
summary: Learners find the view and edit templates of a datatype, show a field with attribute_view_gui, and override the view of one attribute for one class with the attribute_identifier and class_identifier keys. 45 minutes.
keywords: developers course, datatype, ezstring, ezxmltext, ezimage, content/datatype/view, content/datatype/edit, attribute_view_gui, attribute_edit_gui, override, attribute_identifier, class_identifier, data_type_string, content
type: help
---
# Lesson 4.2: Datatypes and their templates

Every attribute is shown by the template of its datatype: a text line by `ezstring.tpl`, rich text by its own,
an image by `ezimage.tpl`. This lesson shows learners where those templates are, what the `content` of an attribute
is for different datatypes, and how to change the view of one attribute of one class with an override instead of
changing the datatype template for the whole site.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 4: Content classes and datatypes](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Developers and template builders |
| Prerequisites | [Lesson 4.1](classes-attributes-and-identifiers.md) and [Lesson 3.2](../module-03-designs-overrides-and-debugging/override-rules.md) |

## Learning objectives

After this lesson, learners can:

- find a datatype's templates in `content/datatype/view/` and `content/datatype/edit/`;
- read an attribute's `data_type_string`, `has_content` and `content` in a template;
- explain why `content` is a string for `ezstring` but an object for `ezxmltext` or `ezimage`;
- override the view template of one attribute with `Match[attribute_identifier]` and `Match[class_identifier]`.

## Trainer's notes

### How to present it

Sources: `kernel/common/eztemplateautoload.php` (the definition of `attribute_view_gui`) and
`design/standard/templates/content/datatype/`.

1. **Where the templates are** (3 minutes). `ls design/standard/templates/content/datatype/view/` and
   `.../edit/`: one file per datatype string, `ezstring.tpl`, `ezimage.tpl` and so on. `attribute_view_gui` uses
   `view/`, `attribute_edit_gui` (in the edit form) `edit/`. An information collector attribute uses `collect/`.
2. **What `content` is** (4 minutes). `$attribute.content` is whatever the datatype returns: a string for `ezstring`,
   an integer for `ezinteger`, an object for `ezxmltext` or `ezimage`. `$attribute.has_content` says whether
   something is stored. Datatype templates know their content; generic code should not.
3. **The override keys of an attribute** (4 minutes). `attribute_view_gui` sets the keys `attribute_identifier`,
   `attribute`, `class_identifier` and `class`. An override with
   `Source=content/datatype/view/ezstring.tpl`, `Match[class_identifier]=note` and
   `Match[attribute_identifier]=title` changes only the title of notes.
4. **Do not edit the kernel's datatype templates** (2 minutes). The override touches one field; editing
   `ezstring.tpl` in your design would change every text line of the site.
5. **`has_content` before printing** (2 minutes). `{if $node.data_map.image.has_content}` avoids empty markup.

### Common questions

**How do I show an image in a given size?** The image datatype's template takes an `image_class` parameter, for
example `{attribute_view_gui attribute=$node.data_map.image image_class='medium'}`; the sizes are image aliases in
`image.ini`.

**Can I pass my own parameters to a datatype template?** Yes, any extra parameter of `attribute_view_gui` becomes a
variable in the template.

**Where is the edit form's template?** In `content/datatype/edit/<datatype>.tpl`, used by `attribute_edit_gui` in
the content edit form.

### Pitfalls

- **Printing `content` of a rich text field.** `{$node.data_map.body.content}` prints an object, not HTML. Use
  `attribute_view_gui`.
- **The identifier of the wrong class.** `attribute_identifier=title` matches every class with a `title` unless the
  rule also has `Match[class_identifier]`.
- **Forgetting the caches.** Like every new override: `ini`, `template-override`, `template` and `content`.

## Hands-on exercise

On the development installation, with the **Note** class from lesson 4.1 and one note published. Use the playground
from lesson 2.1, changing the node ID in `play.php` to the note's node ID.

1. List the datatype templates: `ls design/standard/templates/content/datatype/view/`.
2. In `play.tpl`, print for every attribute its identifier, datatype and whether it has content:

   ```
   {foreach $node.data_map as $identifier => $attribute}
   {$identifier}: {$attribute.data_type_string} {cond( $attribute.has_content, 'filled', 'empty' )}
   {/foreach}
   ```

3. Print the title's content directly and through its datatype template, and compare:

   ```
   [{$node.data_map.title.content|wash}]
   [{attribute_view_gui attribute=$node.data_map.title}]
   ```

4. Open `design/standard/templates/content/datatype/view/ezstring.tpl` and read it.
5. Create the override template
   `extension/mysite_hello/design/<design>/override/templates/datatype/note_title.tpl`:

   ```
   <span class="note-title">{$attribute.content|wash|upcase}</span>
   ```

6. Add the rule in the extension's siteaccess scope with `exp:ini set` as in lesson 3.2: block `mysite_note_title`,
   `Source=content/datatype/view/ezstring.tpl`, `MatchFile=datatype/note_title.tpl`, `Subdir=templates`,
   `Match[class_identifier]=note`, `Match[attribute_identifier]=title`.
7. Clear the `ini`, `template-override`, `template` and `content` caches and open the note on the site.
8. Open an article: its title is unchanged.
9. Commit the template and the settings file.

## Expected result

- Step 2 prints `title: ezstring filled` and `body: eztext filled` (or `empty`).
- Step 3 prints the same text twice; the second from `ezstring.tpl`.
- Step 7: the note's title is in capitals inside `<span class="note-title">`; with path comments on, the comment
  shows `(content/datatype/view/ezstring.tpl)`.
- Step 8: articles are unchanged.
- The trainer checks one learner's settings file for the two `Match` lines.

## Quiz

1. In which folder does `attribute_view_gui` look for the template of an `ezimage` attribute?
2. Which two keys limit an override to the title of notes?
   A. `node` and `object` B. `class_identifier` and `attribute_identifier` C. `section` and `viewmode` D. `class` and `depth`
3. What does `$attribute.has_content` tell you?
4. Why should `ezstring.tpl` not be copied into your design to change one class's title?
5. What is `$node.data_map.body.content` for a rich text field?

<details>
<summary>Answers</summary>

1. `content/datatype/view/ezimage.tpl` in the design chain.
2. **B.** `Match[class_identifier]=note` and `Match[attribute_identifier]=title`.
3. Whether a value is stored in the attribute.
4. It would change every text line of the site; an override limits the change.
5. An object that holds the structured rich text, not HTML; its datatype template renders it.

</details>

## Further reading

- [The content model and editing content](../../../../guides/content-model-and-editing.md): datatypes in the editor.
- [Rich text: supplementary characters and TeX source](../../../../features/6.0/rich-text-supplementary-characters-and-tex-source.md):
  how rich text is stored.
- [Templates and design](../../../../guides/templates-and-design.md): overrides.

## Related pages

- [Module 4: Content classes and datatypes](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
