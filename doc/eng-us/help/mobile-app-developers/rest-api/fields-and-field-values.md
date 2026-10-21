---
title: Fields and field values
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 160
summary: Read and parse the fields of Exponential content in an app - the field routes, the five keys of a field object, how the value is written for rich text, images, blacklisted datatypes and every other datatype, and how to map fields to your app's model safely.
keywords: fields, field values, attributes, datatype, ezstring, ezxmltext, ezimage, ezuser, toString, DatatypeBlackListForExternal, FieldValues, identifier, parsing, model mapping, REST, mobile app
type: help
---
# Fields and field values

This page is for app developers who turn REST answers into the app's own data model. It explains the field routes,
the shape of a field and, most important, how each value is written, so the app parses it correctly.

## Contents

1. [The field routes](#the-field-routes)
2. [The field object](#the-field-object)
3. [How values are written](#how-values-are-written)
4. [Mapping fields to your model](#mapping-fields-to-your-model)
5. [When a field is not enough](#when-a-field-is-not-enough)

## The field routes

```bash
# every field of a node's object
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/node/<node id>/fields
# one field
curl -s -H "Authorization: Bearer $EXP_API_KEY" https://example.com/api/ezp/v2/content/object/<object id>/field/title
```

The same fields come with `?ResponseGroups=Fields` on a node, object or list read
([Response groups](response-groups.md)). The field routes default to the `FieldValues` group and add `metadata` only
with `?ResponseGroups=Metadata`.

## The field object

Each field, under its identifier in the `fields` map, has five keys (`ezpRestContentModel::attributeOutputData()`):

| Key | Meaning |
|---|---|
| `type` | the datatype string, for example `ezstring`, `ezxmltext`, `ezimage` |
| `identifier` | the field identifier of the class, for example `title` |
| `value` | the value, see below |
| `id` | the numeric id of this field of this object |
| `classattribute_id` | the numeric id of the field definition in the class |

## How values are written

| Datatype | `value` |
|---|---|
| `ezxmltext` (rich text) | the field's rendered HTML with every tag removed: plain text, with no paragraphs, links or embedded images |
| `ezimage` | the path of the original image file as stored by the image datatype (the part before `\|` of its string form); the alternative text is cut off |
| any datatype in `site.ini [ContentSettings] DatatypeBlackListForExternal[]` | `null`; the shipped list holds `ezuser`, so user accounts never leave through REST |
| every other datatype | the datatype's `toString()` representation |

The `toString()` form is the datatype's own simple export format. It differs per datatype (numbers, dates, booleans,
relation lists, selections), so test each datatype your classes use against a real answer before you write the
parser, and treat every value as a string that you convert. Do not guess formats from the field name.

Two consequences for apps:

- Rich text needs another source if the app shows formatting: [Rich text in apps](../content-modeling/rich-text-in-apps.md).
- Images need an alias URL for screen sizes: [Images in REST answers](../media-and-images/images-in-rest-answers.md).

## Mapping fields to your model

- **Key by `identifier`, check `type`.** A class can change: an editor's administrator may add a field or change a
  datatype. Ignore unknown identifiers, and fail soft when `type` is not what the app expects.
- **Treat `null` and empty strings as "no value".** Optional fields often come empty.
- **Keep `classIdentifier` with the item.** It tells the app which screen or cell to use
  ([Content classes for apps](../content-modeling/content-classes-for-apps.md)).
- **Do not depend on `id`.** A field's `id` belongs to one object; it is not useful as a key across items. Use
  `classattribute_id` or `identifier` for the field definition.

## When a field is not enough

| Need | Source |
|---|---|
| A field in another language | `?Translation=<locale>` ([Multilingual content](../content-modeling/multilingual-content.md)) |
| Attribute values as the remote services export them | `expnode::dataMap::<node id>` or `expnode::attribute::<node id>::<identifier>` ([Calling expservices from an app](../json-endpoints/calling-expservices-from-an-app.md)) |
| A computed or combined value | a server function written for the app ([ezjscore server functions for your app](../json-endpoints/ezjscore-server-functions-for-your-app.md)) |

## Related pages

- [Reading a node](reading-a-node.md)
- [Choosing datatypes](../../creators/website-creators/choosing-datatypes.md)
- [How Exponential content reaches an app](../how-exponential-content-reaches-an-app.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
