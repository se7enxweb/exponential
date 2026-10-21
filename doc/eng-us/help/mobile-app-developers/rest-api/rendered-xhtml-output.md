---
title: Rendered XHTML output
audience: mobile-app-developer, app-creator, developer, designer
category: mobile-apps
order: 170
summary: What OutputFormat=xhtml does in the REST API - the node rendered in the view mode "rest" inside the template named in rest.ini, the placeholder template that ships, the two templates a site must provide, and how an app shows renderedOutput in a web view.
keywords: OutputFormat, xhtml, renderedOutput, rest_pagelayout.tpl, view mode rest, node/view/rest.tpl, template, web view, rich text, REST, mobile app
type: help
---
# Rendered XHTML output

This page is for app developers who want formatted content (articles with headings, links and images) and for the
designer or developer who prepares the server for it. It explains `OutputFormat=xhtml`, what ships and what a site
has to add.

## Contents

1. [What the parameter does](#what-the-parameter-does)
2. [The shipped template is a placeholder](#the-shipped-template-is-a-placeholder)
3. [Preparing the server](#preparing-the-server)
4. [Showing it in the app](#showing-it-in-the-app)
5. [When to use it](#when-to-use-it)

## What the parameter does

```bash
curl -s -H "Authorization: Bearer $EXP_API_KEY" \
     "https://example.com/api/ezp/v2/content/node/<node id>?OutputFormat=xhtml"
```

The answer gains `renderedOutput`, a string. The kernel's renderer (`ezpContentXHTMLRenderer`, registered in
`rest.ini [OutputSettings] RendererClass[xhtml]`) renders the node's main node in the **view mode `rest`** and passes
the result as `module_result` to the template named in
`rest.ini [ezp7xRestContentController_viewContent_OutputSettings] Template` (default `rest_pagelayout.tpl`).

The guide notes that `renderedOutput` was empty for reads through `ezp7xRestContentController` up to `ezprestapi`
1.2.5, and is filled again since 1.2.6 with the 6.0.15 kernel
([Configuration](../../../guides/rest-api-v1-and-v2.md#5-configuration)).

## The shipped template is a placeholder

`design/standard/templates/rest_pagelayout.tpl` contains a short HTML page that says to read the REST documentation
on how to configure the `OutputFormat` templates. It does not print the node. Until the site provides its own
templates, `renderedOutput` is that placeholder page.

## Preparing the server

This is server-side work for a designer or application creator:

1. A page layout for REST output, under the name in the `Template` setting, that prints `{$module_result.content}`
   and nothing else the app does not need (no menus, no scripts).
2. A node view template for the view mode `rest` (`node/view/rest.tpl`, or override rules per class), that renders
   the fields the app shows.
3. Clear the template caches and, under Velocity, deploy as usual.

How overrides work is in [Template overrides](../../creators/website-creators/template-overrides.md); the settings in
[REST API v1 and v2: configuration](../../../guides/rest-api-v1-and-v2.md#5-configuration).

## Showing it in the app

- Load `renderedOutput` into a web view with a base URL of the site, so relative links and image addresses resolve.
- Give the web view a strict content policy: no scripts unless you need them, and open external links in the
  system browser.
- Style it with a small stylesheet shipped in the app, or let the `rest` templates add classes your app styles.

## When to use it

| Use it when | Prefer something else when |
|---|---|
| Articles must keep headings, lists, links and embedded images | The app needs native text views: then ask for a structured export from a server function |
| The site team can own the `rest` templates | Nobody can maintain templates: then show plain text from the field value |
| The same formatting should appear on the site and in the app | The app needs data, not markup |

## Related pages

- [Rich text in apps](../content-modeling/rich-text-in-apps.md)
- [Fields and field values](fields-and-field-values.md)
- [Headless Exponential: rich text, images and links](../../creators/app-creators/headless-exponential.md#rich-text-images-and-links)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
