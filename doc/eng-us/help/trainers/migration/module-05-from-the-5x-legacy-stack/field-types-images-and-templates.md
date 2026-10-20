---
title: "Lesson 5.3: Field types, image aliases and Twig templates"
audience: trainer, developer
category: training
order: 53
summary: Developers compare the field types of a 5.x site with Exponential's datatypes, decide what to do with RichText, turn YAML image variations into image.ini aliases, and rewrite Twig templates and view rules as TPL templates and override rules. 60 minutes.
keywords: migrating to exponential, 5.x, field types, datatypes, ezrichtext, DocBook, ezxmltext, image_variations, image aliases, image.ini, Twig to TPL, override.ini, location_view, attribute_view_gui
type: help
---
# Lesson 5.3: Field types, image aliases and Twig templates

The content of a 5.x site carries over, but three things around it need work: field types that only existed on the
Symfony side, image variations that the bridge took from YAML, and the Twig templates that often were the whole
public front end. This lesson teaches each with the tables of chapter 15.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 5: From the 5.x legacy stack](README.md) |
| Duration | 60 minutes (25 presenting, 35 exercise) |
| Level | Advanced |
| Audience | Developers and theme designers |
| Prerequisites | [Lesson 5.2](extracting-legacy-and-writing-ini.md); Twig and TPL basics; shell access to a training installation |

## Learning objectives

After this lesson, learners can:

- list the datatypes a site uses and compare them with the datatypes Exponential has;
- choose, per `ezrichtext` attribute, between re-entering and a scripted conversion;
- write a YAML image variation as an `image.ini` alias;
- rewrite a Twig view rule as an `override.ini` block and five Twig expressions as TPL.

## Trainer's notes

### How to present it

Sources: [chapter 15.8](../../../../install/15-migrating-from-5x-legacy.md#158-field-types-against-datatypes),
[15.10](../../../../install/15-migrating-from-5x-legacy.md#1510-image-variations-and-image-aliases) and
[15.12](../../../../install/15-migrating-from-5x-legacy.md#1512-templates-twig-to-tpl).

1. **Same identifiers** (4 minutes). The Symfony field types of 5.x implemented the legacy datatypes over the same
   rows, with the same identifiers in `data_type_string`. Show the table of 15.8: `ezxmltext`, `ezpage`,
   `ezsrrating`, `ezgmaplocation`, `ezcomcomments` all exist; `ezrichtext` has no datatype.
2. **RichText back to XmlText** (6 minutes). Exponential ships no converter from `ezrichtext` to `ezxmltext`. Per
   attribute: re-enter the content in an `ezxmltext` attribute of a class copy, or convert the stored DocBook with a
   script and store the result through the kernel. Show the element mapping table (`<para>` to `<paragraph>`,
   `<emphasis role="strong">` to `<strong>`, links with `ezlocation://` to `node_id`). Validate every converted value
   in the online editor.
3. **Image variations** (5 minutes). The bridge set `AliasList` to exactly the YAML variation names; aliases that
   existed only in `image.ini` were not in effect on the old site. Write each variation as an INI alias with
   `Reference` and `Filters[]`.
4. **Twig to TPL** (10 minutes). The `.tpl` templates of legacy designs keep working; only Twig needs rewriting. Show
   the table of 15.12: a `location_view` rule becomes an `override.ini` block; `ez_render_field` becomes
   `attribute_view_gui`; `path(location)` becomes `$node.url_alias|ezurl`; `|escape` becomes `|wash`.

### Common questions

**Can we render Twig from TPL during the transition?** `ngsymfonytools` does, but only inside a Symfony application
through the bridge; it does not help a standalone site (15.12).

**Where do custom tags go?** They stay `ezxmltext` custom tags, with every `ezoe_attributes.ini` type (15.8).

**How do we find which template wrote some markup?** Template path comments; see the further reading.

### Pitfalls

- **Converting RichText without validation.** Open every converted object in the online editor and publish it once.
- **Forgetting `|wash`.** Twig escaped by default; TPL output without `|wash` is raw. Every output that comes from
  content or visitors is washed.
- **Assuming the old `image.ini` aliases were live.** On the old site only the YAML variations were in effect.

## Hands-on exercise

Use your own training installation; parts 1 and 2 only read, part 3 is written in your notes.

1. List the datatypes the classes of your installation use:

   ```bash
   sqlite3 -readonly var/storage/sqlite3/exponential.db "SELECT data_type_string, COUNT(DISTINCT contentclass_id) FROM ezcontentclass_attribute WHERE version = 0 GROUP BY data_type_string ORDER BY data_type_string;"
   ```

2. Compare them with the datatypes Exponential has:

   ```bash
   php bin/php/console exp:ini get content.ini/DataTypeSettings/AvailableDataTypes
   ```

   Note any identifier from step 1 that is missing in step 2 (on a training site usually none: extension datatypes
   are registered by their extensions).
3. Write this YAML variation as `image.ini` INI, using the example of chapter 15.10:

   ```yaml
   image_variations:
       teaser:
           reference: ~
           filters:
               - { name: geometry/scaledownonly, params: [400, 300] }
   ```

4. Write this view rule as an `override.ini.append.php` block for the siteaccess `site`, using chapter 15.12:
   a full view of locations whose content type is `article`, with the template `full/article.tpl`.
5. Rewrite these Twig expressions in TPL:
   - `{{ ez_content_name(content) }}`
   - `{{ ez_render_field(content, 'body') }}`
   - `{{ path(location) }}`
   - `{% if not ez_is_field_empty(content, 'image') %}`
   - `{{ 'Read more'|trans }}`
6. Convert this DocBook fragment to `ezxmltext` with the mapping table of chapter 15.8:
   `<para>See <emphasis role="strong">our offer</emphasis> on <link xlink:href="ezlocation://42">this page</link>.</para>`

## Expected result

- Steps 1 and 2: every datatype in use is available.
- Step 3: `[AliasSettings] AliasList[]=teaser` and a `[teaser]` block with `Reference=` and a `Filters[]` line for
  `geometry/scaledownonly=400;300`.
- Step 4: a block with `Source=node/view/full.tpl`, `MatchFile=full/article.tpl`, `Subdir=templates` and
  `Match[class_identifier]=article`.
- Step 5: `{$node.name|wash}`, `{attribute_view_gui attribute=$node.data_map.body}`, `{$node.url_alias|ezurl}`,
  `{if $node.data_map.image.has_content}`, `{'Read more'|i18n('context')}`.
- Step 6: `<paragraph>See <strong>our offer</strong> on <link node_id="42">this page</link>.</paragraph>`.
- The trainer checks steps 4 and 6.

## Quiz

1. Why do most 5.x field types need no conversion?
2. Which datatype has no counterpart and needs a decision per attribute?
   A. `ezxmltext` B. `ezpage` C. `ezrichtext` D. `ezsrrating`
3. On the old 5.x site, which image aliases were really in effect?
4. What is the TPL counterpart of Twig's `|escape`?
5. True or false: Exponential ships a converter from `ezrichtext` to `ezxmltext`.

<details>
<summary>Answers</summary>

1. They implemented the legacy datatypes over the same rows, with the same identifiers
   ([chapter 15.8](../../../../install/15-migrating-from-5x-legacy.md#158-field-types-against-datatypes)).
2. **C** (chapter 15.8).
3. Exactly the YAML `image_variations`; the bridge replaced the legacy list
   ([chapter 15.10](../../../../install/15-migrating-from-5x-legacy.md#1510-image-variations-and-image-aliases)).
4. `|wash` ([chapter 15.12](../../../../install/15-migrating-from-5x-legacy.md#1512-templates-twig-to-tpl)).
5. **False.** Re-enter the content or convert it with a script of your own (chapter 15.8).

</details>

## Further reading

- [Chapter 15.8: Field types against datatypes](../../../../install/15-migrating-from-5x-legacy.md#158-field-types-against-datatypes):
  the DocBook mapping table.
- [Chapter 17.5: Templates, TPL and Twig](../../../../install/17-migration-reference.md#175-templates-tpl-and-twig):
  the full lookup table.
- [Templates and design](../../../../guides/templates-and-design.md): how templates are found, overridden and
  debugged.

## Related pages

- [Module 5: From the 5.x legacy stack](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
