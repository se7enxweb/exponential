---
title: Rich text in apps
audience: mobile-app-developer, app-creator, developer, designer
category: mobile-apps
order: 440
summary: Show article bodies (ezxmltext rich text) in an app - why the REST value is plain text, the three options (plain text, rendered XHTML in a web view, a structured export from a server endpoint), links and embedded images inside rich text, and how to choose.
keywords: rich text, ezxmltext, XML text, plain text, strip_tags, OutputFormat, xhtml, renderedOutput, web view, HTML, links, embedded images, structured export, article body, mobile app
type: help
---
# Rich text in apps

This page is for app developers who show articles, news or any content with formatted body text. Rich text fields
(the datatype `ezxmltext`) are the part of the content model that needs a decision before you build.

## Contents

1. [What the REST value is](#what-the-rest-value-is)
2. [Three options](#three-options)
3. [Links and embedded content](#links-and-embedded-content)
4. [Choosing](#choosing)

## What the REST value is

For an `ezxmltext` field, the REST answer's `value` is the field's rendered HTML with every tag removed
(`strip_tags()` in `ezpRestContentModel::attributeOutputData()`). Paragraph breaks, headings, lists, bold text,
links and embedded images are gone. That is fine for a teaser line and not enough for an article body.

## Three options

| Option | How | Gives | Costs |
|---|---|---|---|
| Plain text | the REST field value | text only | nothing; loses all formatting |
| Rendered XHTML | `?OutputFormat=xhtml` on a node read, with `rest` templates on the server | `renderedOutput`, HTML of the whole node | the site team maintains two templates; the app needs a web view ([Rendered XHTML output](../rest-api/rendered-xhtml-output.md)) |
| A structured export | a server endpoint written for the app that turns the field into a list of blocks (paragraph, heading, image, link) | native rendering, full control | PHP work on the server ([ezjscore server functions for your app](../json-endpoints/ezjscore-server-functions-for-your-app.md)) |

The remote services also export attribute values (`expnode::dataMap`, `expnode::attribute`); check what they give
for your rich text fields on a test installation before you choose them for article bodies.

## Links and embedded content

Rich text can link to other content and embed images and objects. In rendered HTML, those links point to addresses
of the site. An app that wants to open them natively resolves the path to a node
([Node ids, remote ids and deep links](node-ids-remote-ids-and-deep-links.md#resolving-a-url-path)); "map them to your
front end's routes by URL alias or node ID" is the advice of [Headless Exponential](../../creators/app-creators/headless-exponential.md#rich-text-images-and-links).
Embedded images in rendered HTML use the image aliases of the site's templates.

## Choosing

- **Teasers and short texts:** plain text, and ask for a dedicated plain teaser field
  ([Content classes for apps](content-classes-for-apps.md#fields-that-help-apps)).
- **A news or magazine app with long articles:** rendered XHTML is the quickest route if the site team can own the
  `rest` templates; a structured export if the app must render natively (accessibility settings, dark mode, fonts).
- **Writing rich text from the app:** REST create passes rich text through the online editor's input parser, so
  simple HTML such as `<p>Text</p>` is accepted ([Creating and removing content](../rest-api/creating-and-removing-content.md)).

## Related pages

- [Fields and field values](../rest-api/fields-and-field-values.md)
- [Rendered XHTML output](../rest-api/rendered-xhtml-output.md)
- [Content modeling for apps](README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
