---
title: "Lesson 2.6: include, node_view_gui and attribute_view_gui"
audience: trainer, developer
category: training
order: 26
summary: Learners split templates with include and named parameters, show nodes in a view mode with node_view_gui, and show content fields with their datatype templates through attribute_view_gui. 50 minutes.
keywords: developers course, template language, include, design:, node_view_gui, view mode, line, full, attribute_view_gui, data_map, datatype template, content_view_gui
type: help
---
# Lesson 2.6: include, node_view_gui and attribute_view_gui

Real templates are small and reuse each other. This lesson teaches the three ways one template calls another:
`{include}` for a part you write yourself, `{node_view_gui}` to show a node in a view mode such as `line`, and
`{attribute_view_gui}` to show one field of a content item with the template of its datatype. Together they are
how every page of a site is assembled.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 2: The template language](README.md) |
| Duration | 50 minutes (20 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Developers and template builders |
| Prerequisites | [Lesson 2.5](links-and-design-files.md) |

## Learning objectives

After this lesson, learners can:

- include a template with `{include uri='design:...'}` and pass it named parameters;
- show a node in a view mode with `{node_view_gui content_node=$child view='line'}`;
- show a field with `{attribute_view_gui attribute=$node.data_map.<identifier>}`;
- find the template each function used, in `node/view/` and `content/datatype/view/`;
- explain why rich text is shown with `attribute_view_gui` and not printed directly.

## Trainer's notes

### How to present it

Sources: [Templates and design, part 6](../../../../guides/templates-and-design.md#6-the-template-language-in-ten-minutes)
and the function definitions in `kernel/common/eztemplateautoload.php`.

1. **`include`** (4 minutes). `{include uri='design:parts/header.tpl' title=$node.name}`. `design:` means "a path
   inside the design chain"; the extra parameters become variables in the included template. The included
   template sees its parameters, not the caller's other variables.
2. **`node_view_gui`** (5 minutes). `{node_view_gui content_node=$child view='line'}` renders the node with
   `node/view/line.tpl` of the design chain, or with an override that matches (module 3). Show
   `ls design/standard/templates/node/view/`: `full`, `line`, `listitem`, `embed`, `text_linked` and more.
3. **`data_map`** (3 minutes). `$node.data_map` is a hash of the item's fields by attribute identifier:
   `$node.data_map.title`, `$node.data_map.body`.
4. **`attribute_view_gui`** (5 minutes). `{attribute_view_gui attribute=$node.data_map.body}` picks the template
   of the field's datatype from `content/datatype/view/`, for example `ezstring.tpl` or the rich text template.
   Show `ls design/standard/templates/content/datatype/view/`.
5. **Why not print it?** (3 minutes). A rich text field's content is structured data, not HTML. Only its datatype
   template turns it into safe HTML. An image field needs its alias and alternative text. Let the datatype do it.

### Common questions

**Can I pass a whole node to an included template?** Yes: `{include uri='design:parts/teaser.tpl' item=$child}`.

**Where does `view='line'` come from?** From the view mode templates in `node/view/`. A site can add its own view
modes; `[ContentSettings]` and the override rules decide which templates exist (module 3).

**What is `content_view_gui`?** The same idea for a content object instead of a node: `{content_view_gui
content_object=$object view='embed'}`, with templates in `content/view/`.

### Pitfalls

- **A missing included template.** `{include}` of a file that no design has prints nothing; the debug output
  names the template that was not found. Check the path after `design:`.
- **An identifier that does not exist.** `$node.data_map.titel` (a typo) gives nothing to show. Look the identifier
  up in the class (module 4) or with `{$node.data_map|attribute( 'show' )}`.
- **Calling `node_view_gui` with `view='full'` inside a list.** It renders a whole page per item. Lists use
  `line`, `listitem` or a view mode of your own.

## Hands-on exercise

Use the playground from lesson 2.1 and run it after each step.

1. Create `extension/mysite_hello/scripts/teaser.tpl`:

   ```
   <article>
       <h2><a href={$item.url_alias|ezurl}>{$item.name|wash}</a></h2>
       <p>{$label|wash}</p>
   </article>
   ```

2. Include it from `play.tpl` by its file path, passing two parameters:

   ```
   {include uri='file:extension/mysite_hello/scripts/teaser.tpl' item=$node label='Included'}
   ```

3. Run the playground. Then include it for the first three children inside a `foreach`.
4. Show the first child in the `line` view mode:

   ```
   {foreach $node.children as $child max 1}
       {node_view_gui content_node=$child view='line'}
   {/foreach}
   ```

5. List the identifiers of the node's fields: `{foreach $node.data_map as $identifier => $attribute}{$identifier} {/foreach}`.
6. Show one field with its datatype template, using an identifier from step 5 (on a folder, `name`):
   `{attribute_view_gui attribute=$node.data_map.name}`.
7. Show the datatype of each field: `{foreach $node.data_map as $identifier => $attribute}{$identifier}={$attribute.data_type_string} {/foreach}`.
8. Find the templates used in steps 4 and 6 in `design/standard/templates/node/view/` and
   `design/standard/templates/content/datatype/view/` (the site's design may override them).
9. Commit both templates.

## Expected result

- Step 3 prints one `<article>` for node 2 and one for each of three children.
- Step 4 prints the HTML of the line view of the first child.
- Step 5 lists identifiers such as `name`, `short_description` or `description`, depending on the class.
- Step 7 prints pairs such as `name=ezstring`.
- The trainer checks that each learner can name the template file behind step 6.

## Quiz

1. What does `design:` mean in `{include uri='design:parts/header.tpl'}`?
2. Which function shows a node in the `line` view mode?
   A. `include` B. `attribute_view_gui` C. `node_view_gui` D. `content_view_gui`
3. What is `$node.data_map`?
4. Where does `attribute_view_gui` find the template for a field of the datatype `ezstring`?
5. Why should a rich text field not be printed with `{$attribute.content}`?

<details>
<summary>Answers</summary>

1. A path inside the design chain: the first design that has `templates/parts/header.tpl` answers.
2. **C.** `{node_view_gui content_node=$child view='line'}`.
3. A hash of the item's fields (content object attributes) by attribute identifier.
4. In `content/datatype/view/ezstring.tpl` of the design chain, unless an override matches.
5. Its content is structured data; only its datatype template turns it into safe HTML.

</details>

## Further reading

- [Templates and design](../../../../guides/templates-and-design.md): template lookup and overrides.
- [The content model and editing content](../../../../guides/content-model-and-editing.md): classes, attributes and
  datatypes behind `data_map`.
- [Version preview view modes](../../../../features/6.0/version-preview-view-modes.md): view modes in the preview.

## Related pages

- [Module 2: The template language](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
