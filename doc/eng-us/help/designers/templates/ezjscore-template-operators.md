---
title: ezjscore template operators
audience: designer, developer
category: templates
order: 310
summary: The template operators the ezjscore extension adds: the packer operators for stylesheets and scripts, json_encode, xml_encode and node_encode for handing data to scripts, and has_access_to_limitation.
keywords: ezjscore operators, json_encode, xml_encode, node_encode, ezcss_load, ezscript_load, has_access_to_limitation, ezjscore, data for javascript
type: help
---
# ezjscore template operators

This page is for designers who use the operators of the ezjscore extension, the JavaScript and CSS foundation of
Exponential. They are registered in `extension/ezjscore/autoloads/eztemplateautoload.php`.

## Contents

1. [Packer operators](#packer-operators)
2. [Encoding for scripts](#encoding-for-scripts)
3. [has_access_to_limitation](#has_access_to_limitation)

## Packer operators

`ezscript`, `ezscript_require`, `ezscript_load`, `ezscriptfiles`, `ezcss`, `ezcss_require`, `ezcss_load`, `ezcssfiles`:
see [The ezcss and ezscript operators](../theme-design/ezcss-and-ezscript-operators.md) and
[Loading stylesheets](../theme-design/loading-stylesheets.md).

## Encoding for scripts

| Operator | Returns |
|---|---|
| `json_encode` | the value as JSON |
| `xml_encode` | the value as XML |
| `node_encode` | a node as a simple structure (for scripts that need node data) |

The CSP-safe way to give a script data is a JSON block:

```tpl
<script type="application/json" id="mytheme-gallery">{json_encode( hash(
    'title', $node.name,
    'count', $node.children_count ) )}</script>
```

The media design passes its video consent texts this way (`pagelayout/head/script.tpl`). A block of type
`application/json` is data, not code, so a strict Content Security Policy allows it.

## has_access_to_limitation

Tells whether the current user has a policy with a given limitation; used by ezjscore-based admin interfaces. For
designs, the user fetch functions are simpler ([Role and policy operators](role-and-policy-operators.md)).

## Related pages

- [No inline JavaScript](../theme-design/no-inline-javascript.md)
- [ezjscore](../../../features/6.0/extensions/ezjscore.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
