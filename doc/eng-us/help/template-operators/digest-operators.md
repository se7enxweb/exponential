---
title: Digest operators
audience: developer, designer, app-creator
category: development
order: 230
summary: crc32, md5 and rot13 from eZTemplateDigestOperator, plus sha1, which the class offers but which only works after another digest operator has loaded it; what they are for in templates and what not, with examples rendered on alpha.
keywords: crc32, md5, sha1, rot13, hash, checksum, digest, eZSys::ezcrc32, 64bitCompatibilityMode, eZTemplateDigestOperator
type: help
---
# Digest operators

Checksums and hashes of a string, for building ids, cache keys or class names. They are registered by
`eZTemplateDigestOperator` (`lib/eztemplate/classes/eztemplatedigestoperator.php`) through
`lib/eztemplate/classes/eztemplateautoload.php`. All work on the piped input and take no parameters.

## Contents

1. [The operators](#the-operators)
2. [sha1 and the autoload list](#sha1-and-the-autoload-list)
3. [What they are not for](#what-they-are-not-for)
4. [Related pages](#related-pages)

## The operators

### md5

PHP `md5()`: 32 hex digits. Generated reference: [md5](../../examples/template-operators/md5.md).

### crc32

`eZSys::ezcrc32()` (`lib/ezutils/classes/ezsys.php`): PHP `crc32()`, printed as an unsigned number when
`[SiteSettings] 64bitCompatibilityMode=enabled` in `site.ini`, so the value is the same on 32-bit and 64-bit PHP.
Generated reference: [crc32](../../examples/template-operators/crc32.md).

### rot13

PHP `str_rot13()`: every letter moved 13 places, other characters kept. It hides nothing; it only stops text from being
read at a glance. Generated reference: [rot13](../../examples/template-operators/rot13.md).

### sha1

PHP `sha1()`: 40 hex digits. See the next section before using it.

All four: input the string (piped), no parameters, return a string (`crc32` a number).

```tpl
'Fit & Healthy'|md5 = {'Fit & Healthy'|md5}
'Fit & Healthy'|crc32 = {'Fit & Healthy'|crc32}
'Fit & Healthy'|rot13 = {'Fit & Healthy'|rot13}
'Fit & Healthy'|sha1 = {'Fit & Healthy'|sha1}
```

Output on alpha:

```text
'Fit & Healthy'|md5 = 230d0ad476984ff3dba19ec739b09733
'Fit & Healthy'|crc32 = 2208743097
'Fit & Healthy'|rot13 = Svg & Urnygul
'Fit & Healthy'|sha1 = 2441fb64e95685d5f0064f97e86d06048e57a499
```

A short, stable id for an element, from a node of alpha (node 89) and its current version:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )}
id-{concat( $node.node_id, '-', $node.object.current_version )|md5|extract_left( 8 )}
```

Output on alpha:

```text
id-0c7e77db
```

## sha1 and the autoload list

The class adds `sha1` to its operator list when PHP has the function, but the `operator_names` entry in
`lib/eztemplate/classes/eztemplateautoload.php` lists only `crc32`, `md5` and `rot13`. The engine loads an operator
class at the first use of one of its listed names, and loading it registers every name the class offers. So `sha1`
works after `md5`, `crc32` or `rot13` has been used in the same request, as in the first example, and is "not
registered" before that, leaving the value unchanged:

```tpl
[{'Fit & Healthy'|sha1}]
```

Output on alpha:

```text
[Fit & Healthy]
```

Logged by the template engine:

```text
WARNING : Operator 'sha1' is not registered
```

Use `md5` when you need a hash in a template, or register an operator of your own
([Registering operators](registering-operators.md)).

## What they are not for

These are not for passwords, signatures or anything an attacker must not forge: MD5 and SHA-1 are broken for those
purposes, and a template is the wrong place for secrets anyway. Use them for ids and cache keys only.

The examples gave the same output interpreted and compiled on alpha.

## Related pages

- [Text operators](text-operators.md): `concat` to build the string to hash
- [Cache blocks](../designers/templates/cache-block.md): keys for cache-block
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
