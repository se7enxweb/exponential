---
title: Design glossary
audience: designer
category: theme-design
order: 190
summary: Short definitions of the words designers meet in Exponential: design, siteaccess, cascade, pagelayout, override, view mode, node, object, class, datatype, cache-block, packer, token, layout, zone, block and more, each with a link to its page.
keywords: glossary, terms, definitions, vocabulary, design words, siteaccess, override, view mode, node, object, class, datatype, packer, token, zone, block
type: help
---
# Design glossary

This page is for designers who meet a word in this help and want its meaning in one or two sentences. Terms are in
alphabetical order; each links to the page that explains it.

## Contents

1. [A to F](#a-to-f)
2. [G to O](#g-to-o)
3. [P to Z](#p-to-z)

## A to F

**Attribute.** One field of a content object (the title, the body, the image). In templates
`$node.data_map.<identifier>`; drawn with `{attribute_view_gui attribute=...}`. See
attribute_view_gui.

**Block.** One item in a zone of a layout (text, image, list, hero ...), drawn by a block template. See
[Block definitions](block-definitions.md).

**Cache-block.** A `{cache-block}` ... `{/cache-block}` region of a template whose output is stored and reused. See
[Cache-block](../templates/cache-block.md).

**Cascade (design cascade).** The ordered list of design folders the kernel searches for a file. See
[The design cascade](the-design-cascade.md).

**Class (content class).** The type of a content object, with its fields: article, folder, image. Templates often
differ per class (`class_identifier`).

**Component (admin4).** A shared `.a4-*` CSS class of the administration (`.a4-card`, `.a4-btn`). See
[admin4 components](admin4-components.md).

**Content Security Policy (CSP).** A response header that tells the browser which scripts and styles it may run; a
strict policy blocks inline code. See [CSP-safe design](csp-safe-design.md).

**Datatype.** The kind of a field (text line, rich text, image, date). Each has view and edit templates under
`content/datatype/`. See Datatype view templates.

**Design.** A named folder of templates, stylesheets, scripts and images. See [What a design is](what-a-design-is.md).

**Design extension.** An extension that carries designs and lists itself in `DesignExtensions[]`. See
[Design extensions](design-extensions.md).

**Fetch function.** A template call that reads data: `fetch( 'content', 'list', hash( ... ) )`. See
[Fetch basics](../templates/fetch-basics.md).

## G to O

**i18n.** The template operator that translates a text: `'Read more'|i18n( 'design/mytheme' )`. See
[The i18n operator](../templates/i18n-operator.md).

**Layout (layouts editor).** A named arrangement of zones with blocks, chosen for a page by a rule. See
[Layouts for designers](layouts-for-designers.md).

**Module result.** What a module view produced, in `$module_result`; the pagelayout prints its `content`. See
[Pagelayout variables](pagelayout-variables.md).

**Node.** A location of a content object in the content tree. An object can have several nodes. Templates of views
receive `$node`. See Nodes, objects and attributes.

**Object (content object).** One piece of content with its fields and versions.

**Operator.** A template function used with a pipe or call syntax: `$text|wash`, `concat( 'a', 'b' )`. See
[Operators overview](../templates/operators-overview.md).

**Override.** A rule in `override.ini` that uses another template for a page that matches conditions. See
Override basics.

## P to Z

**Packer.** The part of ezjscore that merges stylesheets and scripts into one cached file. See [The packer](the-packer.md).

**Pagelayout.** The outermost template, `pagelayout.tpl`, which frames every page. See [The pagelayout](the-pagelayout.md).

**Persistent variable.** A hash a view template fills for the pagelayout. See
The persistent variable.

**Rule (layouts).** The mapping from requests to a layout, with targets, conditions and a priority. See
[Layout rules](layout-rules.md).

**Siteaccess.** One way into an installation, with its own settings and designs. See
[Designs and siteaccesses](designs-and-siteaccesses.md).

**StandardDesign.** The last design of every cascade, `standard` by default.

**Template.** A `.tpl` file in the template language. See [Templates overview](../templates/templates-overview.md).

**Token (design token).** A named CSS custom property for a color, size or font, with a value per theme. See
[Design tokens](design-tokens.md).

**View cache.** The stored HTML of full views, cleared when content is published. See
[Caching for designers](caching-for-designers.md).

**View mode.** The way a node is shown: `full`, `line`, `embed`, `embed-inline` ... See
View modes.

**Wash.** The operator that escapes text for HTML output. See [Wash and escaping](../templates/wash-and-escaping.md).

**Zone.** A named region of a layout (`main`, `sidebar`, `footer`) that holds blocks. See
[Layout types and zones](layout-types-and-zones.md).

## Related pages

- [Designers: start here](../designers-start-here.md)
- [What a design is](what-a-design-is.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
