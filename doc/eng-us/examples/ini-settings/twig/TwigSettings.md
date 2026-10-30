---
title: twig.ini [TwigSettings]
audience: developer, admin
category: examples
summary: "The settings of [TwigSettings] in twig.ini: LibraryPath, AutoReload, Debug, StrictVariables, Autoescape, TemplateSuffixList, WhileLoopLimit, FunctionList ..."
keywords: twig.ini, TwigSettings, INI, setting, LibraryPath, AutoReload, Debug, StrictVariables, Autoescape, TemplateSuffixList, WhileLoopLimit, FunctionList, ParameterOperatorList, Profile, Security, SecurityAllowedTagList, SecurityAllowedFilterList, SecurityAllowedFunctionList, SecurityAllowedMethodList, SecurityAllowedPropertyList
type: generated
generated: exp:help:examples
---
# twig.ini [TwigSettings]

The block `[TwigSettings]` of `settings/twig.ini`, with 16 setting(s).

Never edit `settings/twig.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/twig.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/twig.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/twig.ini.append.php` in an extension (read when the extension is active).

## LibraryPath

> Where Twig 3 is loaded from when the installation's Composer autoload has no  
> twig/twig: a directory with vendor/autoload.php (a Composer directory that  
> requires twig/twig), or the twig/twig package itself (the directory holding  
> src/Environment.php). Relative to the installation root, or absolute.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
LibraryPath=
*/ ?>
```

## AutoReload

> Twig's auto\_reload: a changed .twig is compiled again. disabled saves a  
> file check per template on production systems (clear the template caches  
> after a deploy).  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
AutoReload=enabled
*/ ?>
```

## Debug

> Twig's debug mode (the dump() function, debug information in compiled code).  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
Debug=disabled
*/ ?>
```

## StrictVariables

> disabled: an undefined variable or attribute prints nothing, as in a .tpl.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
StrictVariables=disabled
*/ ?>
```

## Autoescape

> disabled: nothing is escaped unless the template says so (|wash), as in a  
> .tpl, so converted templates print what the .tpl printed. A siteaccess may  
> enable it (html) for hand-written templates; see the security guide.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
Autoescape=disabled
*/ ?>
```

## TemplateSuffixList

> Template file endings Twig renders (expTemplateEngines matches the longest  
> first). The same list as site.ini \[TemplateBridgeSettings\] EngineSuffixList\[\].  

Default: a list of 2: `.twig`, `.html.twig`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
# an empty "TemplateSuffixList[]" first replaces the shipped list; without it the items are added
TemplateSuffixList[]
TemplateSuffixList[]=.twig
TemplateSuffixList[]=.html.twig
*/ ?>
```

## WhileLoopLimit

> The most rounds a converted {while} or {do} loop runs ({% expwhile %});  
> a loop that reaches it stops with a debug notice.  

Default: `10000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
WhileLoopLimit=10000
*/ ?>
```

## FunctionList

> Template functions callable as Twig functions with a hash of named  
> parameters, {{ attribute\_view\_gui({attribute: a}) }}. Every other template  
> function without children is bridged too; functions with children become tags  
> ({% explblock name='x' %}...{% endexplblock %}).  

Default: a list of 11: `attribute_view_gui`, `attribute_edit_gui`, `attribute_result_gui`, `attribute_pdf_gui`, `node_view_gui`, `content_view_gui`, `content_version_view_gui`, `class_attribute_view_gui`, `class_attribute_edit_gui`, `related_view_gui`, `shop_account_view_gui`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
# an empty "FunctionList[]" first replaces the shipped list; without it the items are added
FunctionList[]
FunctionList[]=attribute_view_gui
FunctionList[]=attribute_edit_gui
FunctionList[]=attribute_result_gui
FunctionList[]=attribute_pdf_gui
FunctionList[]=node_view_gui
FunctionList[]=content_view_gui
FunctionList[]=content_version_view_gui
FunctionList[]=class_attribute_view_gui
FunctionList[]=class_attribute_edit_gui
FunctionList[]=related_view_gui
FunctionList[]=shop_account_view_gui
*/ ?>
```

## ParameterOperatorList

> Operators without template hints that read their parameters only, never  
> their input (in addition to site.ini \[TemplateBridgeSettings\]  
> ParameterOperatorList\[\], the list for every engine).  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
# an empty "ParameterOperatorList[]" first replaces the shipped list; without it the items are added
ParameterOperatorList[]
*/ ?>
```

## Profile

> enabled (and debug output on): the debug bar's accumulators show where a page's Twig time  
> goes (renders, compiled-template loads, operators, template functions, includes, wrapping).  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
Profile=disabled
*/ ?>
```

## Security

> The sandbox (Twig's SandboxExtension with the policy of expTwigSecurity).  
> enabled: a template may only use the tags, filters and functions allowed  
> here; no method of an object can be called and no property read (values of  
> the template system are read through their attributes, which is allowed).  
> Compiled apart from the templates compiled without it (<cache>/twig/compile-secure).  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
Security=enabled
*/ ?>
```

## SecurityAllowedTagList

> Tags allowed (empty: Twig's own tags below, and every exp\* tag of lib/twig).  

Default: a list of 16: `if`, `for`, `set`, `apply`, `block`, `extends`, `include`, `embed`, `macro`, `import`, `from`, `use` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
# an empty "SecurityAllowedTagList[]" first replaces the shipped list; without it the items are added
SecurityAllowedTagList[]
SecurityAllowedTagList[]=if
SecurityAllowedTagList[]=for
SecurityAllowedTagList[]=set
SecurityAllowedTagList[]=apply
SecurityAllowedTagList[]=block
SecurityAllowedTagList[]=extends
SecurityAllowedTagList[]=include
SecurityAllowedTagList[]=embed
SecurityAllowedTagList[]=macro
SecurityAllowedTagList[]=import
SecurityAllowedTagList[]=from
SecurityAllowedTagList[]=use
SecurityAllowedTagList[]=with
SecurityAllowedTagList[]=verbatim
SecurityAllowedTagList[]=guard
SecurityAllowedTagList[]=types
*/ ?>
```

## SecurityAllowedFilterList

> Twig filters allowed in addition to every bridged operator.  

Default: a list of 31: `abs`, `batch`, `capitalize`, `column`, `date`, `default`, `e`, `escape`, `first`, `format`, `join`, `json_encode` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
# an empty "SecurityAllowedFilterList[]" first replaces the shipped list; without it the items are added
SecurityAllowedFilterList[]
SecurityAllowedFilterList[]=abs
SecurityAllowedFilterList[]=batch
SecurityAllowedFilterList[]=capitalize
SecurityAllowedFilterList[]=column
SecurityAllowedFilterList[]=date
SecurityAllowedFilterList[]=default
SecurityAllowedFilterList[]=e
SecurityAllowedFilterList[]=escape
SecurityAllowedFilterList[]=first
SecurityAllowedFilterList[]=format
SecurityAllowedFilterList[]=join
SecurityAllowedFilterList[]=json_encode
SecurityAllowedFilterList[]=keys
SecurityAllowedFilterList[]=last
SecurityAllowedFilterList[]=length
SecurityAllowedFilterList[]=lower
SecurityAllowedFilterList[]=merge
SecurityAllowedFilterList[]=nl2br
SecurityAllowedFilterList[]=number_format
SecurityAllowedFilterList[]=raw
# ... 11 more in the shipped file
*/ ?>
```

## SecurityAllowedFunctionList

> Twig functions allowed in addition to every bridged operator and template function.  

Default: a list of 8: `attribute`, `block`, `cycle`, `max`, `min`, `parent`, `random`, `range`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
# an empty "SecurityAllowedFunctionList[]" first replaces the shipped list; without it the items are added
SecurityAllowedFunctionList[]
SecurityAllowedFunctionList[]=attribute
SecurityAllowedFunctionList[]=block
SecurityAllowedFunctionList[]=cycle
SecurityAllowedFunctionList[]=max
SecurityAllowedFunctionList[]=min
SecurityAllowedFunctionList[]=parent
SecurityAllowedFunctionList[]=random
SecurityAllowedFunctionList[]=range
*/ ?>
```

## SecurityAllowedMethodList

> Methods allowed as Class::method (none by default: values are read through attributes).  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
# an empty "SecurityAllowedMethodList[]" first replaces the shipped list; without it the items are added
SecurityAllowedMethodList[]
*/ ?>
```

## SecurityAllowedPropertyList

> Properties allowed as Class::property (none by default).  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/twig.ini.append.php
[TwigSettings]
# an empty "SecurityAllowedPropertyList[]" first replaces the shipped list; without it the items are added
SecurityAllowedPropertyList[]
*/ ?>
```

## Related

- [All blocks of twig.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/twig.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
