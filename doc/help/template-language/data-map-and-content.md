---
title: Data map and content
audience: developer, designer, app-creator
category: development
order: 240
summary: Reading the fields of a content object in templates - data_map, has_content, content, the raw data_text and data_int, and attribute_view_gui - proven field by field on a real article on alpha (ezstring, ezxmltext, ezimage, ezobjectrelationlist, eztags, dates).
keywords: data_map, has_content, content, data_text, data_int, attribute_view_gui, ezstring, ezxmltext, output_text, ezimage, image alias, ezobjectrelationlist, relation_list, eztags, datetime
type: help
---
# Data map and content

`data_map` is the door to the fields of a content object: a hash from the class attribute identifier to an
`eZContentObjectAttribute`. This page reads the fields of one real article on alpha, node 96 "Why Should You Choose
Strength Training?" (class `ng_article`), and shows what each kind of field gives a template. The attribute
tables are on the [Content attribute and version reference](content-attribute-reference.md).

## Contents

1. [The fields of a class](#the-fields-of-a-class)
2. [content, data_text and value](#content-data_text-and-value)
3. [Rich text (ezxmltext)](#rich-text-ezxmltext)
4. [Images (ezimage)](#images-ezimage)
5. [Relations (ezobjectrelationlist)](#relations-ezobjectrelationlist)
6. [Tags (eztags)](#tags-eztags)
7. [Dates](#dates)
8. [attribute_view_gui or content?](#attribute_view_gui-or-content)
9. [Empty fields](#empty-fields)
10. [Related pages](#related-pages)

## The fields of a class

Looping over `data_map` lists the identifiers, datatypes and whether each field holds something:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 96 ) )}
{$node.name|wash} ({$node.class_identifier})

{foreach $node.data_map as $identifier => $attribute}
- {$identifier}: {$attribute.data_type_string}, has_content: {cond( $attribute.has_content, 'yes', 'no' )}

{/foreach}
```

Output on alpha:

```text
Why Should You Choose Strength Training? (ng_article)
- title: ezstring, has_content: yes
- teaser_title: ezstring, has_content: yes
- authors: ezobjectrelationlist, has_content: yes
- image: ezimage, has_content: yes
- teaser_image: ezimage, has_content: yes
- full_intro: ezxmltext, has_content: yes
- teaser_intro: ezxmltext, has_content: no
- body: ezxmltext, has_content: yes
- related_content: ezobjectrelationlist, has_content: no
- related_multimedia: ezobjectrelationlist, has_content: no
- main_topic: eztags, has_content: yes
- tags: eztags, has_content: yes
- sponsored_content_disclosure: ezstring, has_content: no
- css_class: ezstring, has_content: no
- url_text: ezstring, has_content: no
- metadata: xrowmetadata, has_content: yes
- additional_image: ezimage, has_content: no
- icon_title: ezimage, has_content: no
```

The order is the order of the class attributes. A field that the class has but that is empty is still in the map;
`has_content` asks the datatype whether there is anything in it.

## content, data_text and value

`content` is what the datatype makes of the stored data; `data_text`, `data_int` and `data_float` are the stored data
itself; `value` is `content` unless a form has just posted a value for the field. For a text line they agree:

```tpl
{def $title = fetch( 'content', 'node', hash( 'node_id', 96 ) ).data_map.title}
content:   {$title.content|wash}
data_text: {$title.data_text|wash}
value:     {$title.value|wash}
sort key:  {$title.sort_key_string|wash}
```

Output on alpha:

```text
content:   Why Should You Choose Strength Training?
data_text: Why Should You Choose Strength Training?
value:     Why Should You Choose Strength Training?
sort key:  why should you choose strength training?
```

`sort_key_string` is the lower-cased form the kernel sorts by (`sort_by` on `attribute`). Prefer `content` in
templates: `data_text` is a storage detail that differs from one datatype to the next.

## Rich text (ezxmltext)

The content of an `ezxmltext` field is an `eZXMLText` object. `output` gives the output handler for the current
siteaccess and `output_text` the rendered HTML; `data_text` holds the stored XML:

```tpl
{def $intro = fetch( 'content', 'node', hash( 'node_id', 96 ) ).data_map.full_intro}
{$intro.content|get_class}
{$intro.content.output|get_class}
{$intro.content.output.output_text|strip_tags|trim|shorten( 120 )}
Stored XML starts with: {$intro.data_text|shorten( 60 )|wash}
```

Output on alpha:

```text
ezxmltext
explblockxhtmlxmloutput
If you only had 1 hour a week to exercise, that hour should be spent strength training.
Stored XML starts with: &lt;section xmlns:image=&quot;http://ez.no/namespaces/ezpublish3/...
```

On alpha the output handler is `ExplBlockXHTMLXMLOutput` (lower-cased by `get_class`), a subclass of the kernel's
`eZXHTMLXMLOutput` that the explayouts extension registers in `ezxml.ini` (`HandlerClass`). In a page you normally write
`{attribute_view_gui attribute=$node.data_map.body}`, which renders the same `output_text` through the datatype's
view template (see [Rich text templates](../designers/templates/rich-text-templates.md)).

## Images (ezimage)

The content of an `ezimage` field is an `eZImageAliasHandler`. Index it with an alias name from `image.ini` to get
that variation, or read `original`:

```tpl
{def $image = fetch( 'content', 'node', hash( 'node_id', 96 ) ).data_map.image
     $large = $image.content['large']}
{$image.content|get_class}
original: {$image.content.original.width}x{$image.content.original.height} {$image.content.original.mime_type}
large:    {$large.width}x{$large.height} {$large.url}
```

Output on alpha:

```text
ezimagealiashandler
original: 1920x1080 image/jpeg
large:    1200x675 var/site/storage/images/fit-healthy/fitness/why-should-you-choose-strength-training/583-1-eng-US/why-should-you-choose-strength-training_large.jpg
```

Reading an alias does more than read. `eZImageAliasHandler::imageAlias()`
(`kernel/classes/datatypes/ezimage/ezimagealiashandler.php`) creates the variation the first time it is asked for:
it scales the file, records it in the `ezimagefile` table and stores the alias in the attribute's XML. That is how
variations come to exist on a site, and it means a template that names an alias nobody uses yet makes the first
visitor pay for the scaling. The example uses `large`, which already existed for this image.

## Relations (ezobjectrelationlist)

The content of an `ezobjectrelationlist` field is an array with one key, `relation_list`, a list of hashes. Each hash
identifies a related object; fetch the object or node to read it:

```tpl
{def $authors = fetch( 'content', 'node', hash( 'node_id', 96 ) ).data_map.authors}
{$authors.content|get_type}, keys: {foreach $authors.content as $key => $value}{$key}{delimiter}, {/delimiter}{/foreach}


{foreach $authors.content.relation_list as $relation}
- object {$relation.contentobject_id}, node {$relation.node_id}: {fetch( 'content', 'object', hash( 'object_id', $relation.contentobject_id ) ).name|wash}

{/foreach}
```

Output on alpha:

```text
array[1], keys: relation_list
- object 185, node 187: John Doe
```

The keys of one item, as alpha returned them:

```tpl
{def $relation = fetch( 'content', 'node', hash( 'node_id', 96 ) ).data_map.authors.content.relation_list.0}
{foreach $relation as $key => $value}{$key}={$value}{delimiter}, {/delimiter}{/foreach}
```

Output on alpha:

```text
identifier=, priority=1, in_trash=, contentobject_id=185, is_modified=, contentobject_version=1, node_id=187, parent_node_id=186, contentclass_id=57, contentclass_identifier=ng_person, contentobject_remote_id=media-o-1013
```

`contentclass_identifier` lets a template choose how to show an item before fetching it, and `node_id` is the main
node of the related object, ready for `fetch( 'content', 'node', hash( 'node_id', ... ) )` or for
`node_view_gui`.

## Tags (eztags)

The `eztags` datatype (extension `eztags`) returns an `eZTags` object; `tags` is the list of tag objects and
`keyword_string` the keywords joined as one string:

```tpl
{def $tags = fetch( 'content', 'node', hash( 'node_id', 96 ) ).data_map.tags}
{$tags.content|get_class}
{foreach $tags.content.tags as $tag}{$tag.keyword|wash} (id {$tag.id}){delimiter}, {/delimiter}{/foreach}
```

Output on alpha:

```text
eztagsStrength training (id 49)
```

```tpl
{def $dm = fetch( 'content', 'node', hash( 'node_id', 96 ) ).data_map}
main_topic: {foreach $dm.main_topic.content.tags as $tag}{$tag.keyword|wash} (parent {$tag.parent_id}){/foreach}

tag keywords as a string: {$dm.tags.content.keyword_string|wash}
body is empty: {cond( $dm.body.content.is_empty, 'yes', 'no' )}
```

Output on alpha:

```text
main_topic: Strength training (parent 36)
tag keywords as a string: Strength training
body is empty: no
```

`is_empty` on an `eZXMLText` content is another way to ask whether a rich text field has text.

## Dates

`ng_article` keeps no date field of its own; the publishing and modification times of the object are Unix
timestamps, formatted with the locale operators:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 96 ) )}
published: {$node.object.published} = {$node.object.published|datetime( 'custom', '%Y-%m-%d' )}
modified:  {$node.object.modified|l10n( 'shortdate' )}
```

Output on alpha:

```text
published: 1532345053 = 2018-07-23
modified:  09/24/2020
```

`l10n( 'shortdate' )` uses the format of the siteaccess locale (`eng-US` here); `datetime( 'custom', ... )` takes
a format string. An `ezdatetime` field's content is an `eZDateTime` object whose `timestamp` attribute goes through
the same operators.

## attribute_view_gui or content?

`attribute_view_gui` renders a field with the view template of its datatype (`content/datatype/view/<datatype>.tpl`,
or an override); reading `content` gives you the value to format yourself:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 96 ) )}
[{attribute_view_gui attribute=$node.data_map.title}]
[{attribute_view_gui attribute=$node.data_map.full_intro}]
```

Output on alpha:

```text
[
Why Should You Choose Strength Training?]
[
<p>If you only had 1 hour a week to exercise, that hour should be spent strength training.</p>
]
```

Use `attribute_view_gui` when the datatype's own markup is what you want (rich text, images, files, matrices): the
datatype template handles escaping, aliases and links. Read `content` when you need the value inside your own
markup, a condition or a calculation, and escape text with `wash` yourself (see
[Wash and escaping](../designers/templates/wash-and-escaping.md)).

## Empty fields

Test `has_content` before showing a field, so empty fields leave no empty wrappers behind:

```tpl
{def $dm = fetch( 'content', 'node', hash( 'node_id', 96 ) ).data_map}
teaser: {if $dm.teaser_intro.has_content}{attribute_view_gui attribute=$dm.teaser_intro}{else}empty, use full_intro{/if}

related: {if $dm.related_content.has_content}listed below{else}none{/if}
```

Output on alpha:

```text
teaser: empty, use full_intro
related: none
```

Note the empty line in the template between the two lines: the parser removes the line break right after `{/if}`,
so a blank line is needed to keep one (see the whitespace rule in [Errors and debugging](errors-and-debugging.md)).

## Related pages

- [The object model in templates](object-model.md)
- [Content attribute and version reference](content-attribute-reference.md)
- [Attribute view gui](../designers/templates/attribute-view-gui.md)
- [Datatype view templates](../designers/templates/datatype-view-templates.md)
- [Datatypes with examples](../../examples/datatypes/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
