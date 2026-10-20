---
title: URL aliases and nice URLs
audience: website-creator, admin
category: website-creation
order: 420
summary: How Exponential makes readable addresses from content names - the URLTranslator settings, word separators, transformation groups, length limits, custom aliases per node, rebuilding aliases and linking correctly in templates.
keywords: URL alias, nice URLs, readable addresses, URLTranslator, WordSeparator, TransformationGroup, urlalias_iri, UrlAliasNameLimit, updateniceurls, ezurl, url_alias, website creator
type: help
---
# URL aliases and nice URLs

This page is for website creators who care how addresses look. Every node gets a **URL alias** made from the names
of its path: the article "Opening hours" in the folder "About us" is at `/About-us/Opening-hours`. The internal address
`/content/view/full/<node>` keeps working but should never appear in links.

## Contents

1. [How aliases are made](#how-aliases-are-made)
2. [The settings](#the-settings)
3. [Custom aliases for one node](#custom-aliases-for-one-node)
4. [Linking in templates](#linking-in-templates)
5. [Rebuilding aliases](#rebuilding-aliases)
6. [Checklist](#checklist)

## How aliases are made

- From the object's name, or from the class's **URL alias name pattern** when it has one
  ([Content classes for websites](content-classes-for-websites.md)).
- Characters are transformed by the transformation group; words are joined by the word separator.
- Each translation has its own alias in its language.
- When a node is renamed or moved, the new alias is made and the old one stays as a redirect to it
  ([Redirects and moved content](redirects-and-moved-content.md)).

## The settings

`site.ini [URLTranslator]`:

| Key | Shipped | Meaning |
|---|---|---|
| `Translation` | `enabled` | addresses are resolved by aliases |
| `WordSeparator` | `dash` | `dash`, `underscore` or `space` |
| `TransformationGroup` | `urlalias` | `urlalias` (ASCII), `urlalias_iri` (keeps accents and other letters), `urlalias_compat` (old style) |
| `UrlAliasNameLimit` | `255` | the limit per path element; `0` disables it |
| `LoadOnPartialAliasPath` | `/` | what an incomplete alias path loads |
| `DecodeOnce`, `TrailingCleanup` | `disabled` | two optional address cleanups ([8. Decoding and cleaning addresses](../../../guides/urls-and-aliases.md#8-decoding-and-cleaning-addresses-two-optional-settings)) |

Decide `TransformationGroup` before launch: for a site in German or French, `urlalias_iri` keeps `Über-uns` readable;
`urlalias` makes it `Uber-uns`. Changing it later only affects objects when they are published again or the aliases are
rebuilt.

## Custom aliases for one node

The node's **URL aliases** tab adds more aliases to the node (a short campaign address, an old address from a previous
site). Global aliases for module views and wildcards for patterns are in **Setup > URL translator** and
**Setup > URL wildcards** ([URL wildcards and custom aliases](url-wildcards.md)).

![The Locations tab of an article: the places in the tree where it appears, each with its own address](../../images/creators/locations-tab.webp)

![The URL aliases of one item: the generated address and the form to add a custom alias in a language](../../images/creators/url-alias-tab.webp)

## Linking in templates

```text
<a href={$node.url_alias|ezurl}>{$node.name|wash}</a>
```

`ezurl` adds the siteaccess path when the siteaccess is in the address and escapes the result; it is written without
quotes in an attribute. `|ezurl( , 'full' )` gives the full address with host, for mail and feeds. In PHP,
`eZURI::build()` gives the same ([For developers](../../../guides/urls-and-aliases.md#for-developers)).

## Rebuilding aliases

After an import, a change of `TransformationGroup` or a repair, rebuild every alias:

```bash
php bin/php/updateniceurls.php --allow-root-user
```

On large sites this takes a while; run it outside busy hours, with a backup. Then clear the `urlalias` cache.

## Checklist

- [ ] The transformation group chosen for the site's languages.
- [ ] Folder and article names make good addresses (short, no dates unless wanted).
- [ ] Templates link with `url_alias|ezurl`, never `content/view/full/...`.
- [ ] Campaign addresses made as aliases, not as new pages.

## Related pages

- [Redirects and moved content](redirects-and-moved-content.md)
- [Links, URL aliases, wildcards and search statistics](../../../guides/urls-and-aliases.md)
- [SEO basics for Exponential sites](seo-basics.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
