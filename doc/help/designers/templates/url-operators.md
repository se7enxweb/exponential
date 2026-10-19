---
title: URL operators
audience: designer, developer
category: templates
order: 240
summary: Building links and asset URLs that work under every siteaccess and installation path: ezurl for site links, ezroot for root paths, ezdesign and ezimage for design files, the quote parameter (double, single, no) and full server URLs, plus exturl and ezsys.
keywords: ezurl, ezroot, ezdesign, ezimage, exturl, ezsys, links, url, quotes, full url, absolute url, siteaccess prefix, index.php, canonical link
type: help
---
# URL operators

This page is for designers who write links. Never write `/news/article` or `/extension/mytheme/...` by hand: the site
may run under a path prefix, a siteaccess in the URL or a different host. The URL operators of
`kernel/common/ezurloperator.php` build correct URLs.

## Contents

1. [The operators](#the-operators)
2. [Quotes](#quotes)
3. [Full URLs](#full-urls)
4. [Examples](#examples)
5. [ezsys and exturl](#ezsys-and-exturl)

## The operators

| Operator | For | Adds the siteaccess path? |
|---|---|---|
| `ezurl` | links to pages of the site (`'/content/search'`, a node's `url_alias`) | yes |
| `ezroot` | paths from the installation root (`var/...` storage files, image alias URLs) | no |
| `ezdesign` | any file inside a design, found through the cascade (`'stylesheets/print.css'`) | no |
| `ezimage` | a file in a design's `images/` folder, found through the cascade | no |

## Quotes

By default the operators return the URL **with double quotes**, so you write the attribute without quotes:

```tpl
<a href={$node.url_alias|ezurl}>...</a>
```

The first parameter changes that: `'single'` for single quotes, `'no'` for none:

```tpl
<a href="{$node.url_alias|ezurl( 'no' )}#comments">...</a>
srcset="{$img.url|ezroot( 'no' )} 480w"
```

With `'no'` you are responsible for the quotes around the attribute.

## Full URLs

The second parameter `'full'` adds the scheme and host (from `eZSys::serverURL()`), for canonical links, Open Graph tags
and mails:

```tpl
<link rel="canonical" href={$node.url_alias|ezurl( 'double', 'full' )}>
<meta property="og:url" content={$node.url_alias|ezurl( 'double', 'full' )}>
```

## Examples

```tpl
<a href={'/'|ezurl}>{'Home'|i18n( 'design/mytheme' )}</a>
<a href={'/user/login'|ezurl}>{'Sign in'|i18n( 'design/mytheme' )}</a>
<form action={'/content/search'|ezurl} method="get">...</form>
<img src={'logo.svg'|ezimage} alt="{$site.title|wash}">
<img src={$node.data_map.image.content['medium'].url|ezroot} alt="...">
<link rel="manifest" href={'images/favicon/site.webmanifest'|ezdesign}>
```

A link to a node: `$node.url_alias|ezurl`. A link to a view with parameters:
`concat( $node.url_alias, '/(offset)/', $offset )|ezurl`.

## ezsys and exturl

- `ezsys( 'wwwdir' )`, `ezsys( 'indexdir' )`, `ezsys( 'indexfile' )`, `ezsys( 'sitedir' )`, `ezsys( 'querystring' )`
  return facts about the installation and request (the attributes `eZSys::attributes()` lists). Another name is a
  template warning.
- `exturl` registers an external URL in the kernel's URL table (the list of links the administration can check) and
  returns it. For an ordinary external link, write the URL directly (`href="https://..."`) and wash it if it comes from
  content.

## Related pages

- [Images in a design](../theme-design/images-in-a-design.md)
- [Wash and escaping](wash-and-escaping.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
