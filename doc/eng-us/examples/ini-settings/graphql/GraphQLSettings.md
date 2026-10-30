---
title: graphql.ini [GraphQLSettings]
audience: developer, admin
category: examples
summary: "The settings of [GraphQLSettings] in graphql.ini: Enabled, SiteAccessList, LibraryPath, NamingStyle, ClassList, ExcludedClassList, HiddenFieldList, HiddenDatatypeList ..."
keywords: graphql.ini, GraphQLSettings, INI, setting, Enabled, SiteAccessList, LibraryPath, NamingStyle, ClassList, ExcludedClassList, HiddenFieldList, HiddenDatatypeList, Introspection, MaxDepth, MaxComplexity, MaxListLimit, DefaultListLimit, ImageAliasList, Debug
type: generated
generated: exp:help:examples
---
# graphql.ini [GraphQLSettings]

The block `[GraphQLSettings]` of `settings/graphql.ini`, with 15 setting(s).

Never edit `settings/graphql.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/graphql.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/graphql.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/graphql.ini.append.php` in an extension (read when the extension is active).

## Enabled

> enabled or disabled (the default). Off until it is switched on, for every siteaccess or in the settings of one  
> (settings/siteaccess/<siteaccess>/graphql.ini.append.php). Disabled: /api/graphql answers 404, the session end  
> point graphql/query answers 404, nothing else changes.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
Enabled=disabled
*/ ?>
```

## SiteAccessList

> The siteaccesses that answer when Enabled=enabled; empty: every siteaccess. A request is answered by the  
> siteaccess it matches (host, path or the X-Siteaccess header, as every REST request).  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# an empty "SiteAccessList[]" first replaces the shipped list; without it the items are added
SiteAccessList[]
*/ ?>
```

## LibraryPath

> Where webonyx/graphql-php 15 is loaded from when the installation's Composer autoload has no GraphQL\\GraphQL: a  
> directory with vendor/autoload.php (a Composer directory that requires webonyx/graphql-php), or the package  
> itself (the directory holding src/GraphQL.php). Relative to the installation root, or absolute. The environment  
> variable GRAPHQL\_LIBRARY\_PATH is read the same way when this is empty.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
LibraryPath=
*/ ?>
```

## NamingStyle

> How content classes and their attributes are named in the schema:  
>   identifier  (the default) as in the administration: the class ng\_article is the type ng\_article, its attribute  
>               short\_title the field short\_title  
>   pascal      types in PascalCase (NgArticle), fields in camelCase (shortTitle)  
>   map         as identifier, but the names of \[GraphQLNaming\] TypeNames\[\] and FieldNames\[\] win  
> A name that is not a valid GraphQL name (a digit first, a dash) has every other character replaced by \_ and a \_  
> in front when it starts with a digit. An attribute named like a field every content has (id, name, children ...)  
> gets the suffix \_field (name\_field).  

Default: `identifier`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
NamingStyle=identifier
*/ ?>
```

## ClassList

> The classes the schema shows. Empty: every class except those of ExcludedClassList\[\]. Policies always apply:  
> a class in the schema is still read only by who may read its objects.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# an empty "ClassList[]" first replaces the shipped list; without it the items are added
ClassList[]
*/ ?>
```

## ExcludedClassList

> Never in the schema (unless ClassList\[\] names them): the user accounts, the user groups and the classes of  
> settings.  

Default: a list of 4: `user`, `user_group`, `common_ini_settings`, `template_look`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# an empty "ExcludedClassList[]" first replaces the shipped list; without it the items are added
ExcludedClassList[]
ExcludedClassList[]=user
ExcludedClassList[]=user_group
ExcludedClassList[]=common_ini_settings
ExcludedClassList[]=template_look
*/ ?>
```

## HiddenFieldList

> Attributes never in the schema, <class>/<attribute> or \*/<attribute> for every class.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# an empty "HiddenFieldList[]" first replaces the shipped list; without it the items are added
HiddenFieldList[]
*/ ?>
```

## HiddenDatatypeList

> Datatypes never in the schema: the user account (login, e-mail, password hash) and the password field.  

Default: a list of 2: `ezuser`, `ezpassword`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# an empty "HiddenDatatypeList[]" first replaces the shipped list; without it the items are added
HiddenDatatypeList[]
HiddenDatatypeList[]=ezuser
HiddenDatatypeList[]=ezpassword
*/ ?>
```

## Introspection

> Introspection (the schema read by a client, as the playground does):  
>   signed-in  (the default) for a request signed in by a token, a personal API key or a session; refused for  
>              anonymous requests  
>   enabled    for every request  
>   disabled   for none (the console commands still print the schema)  

Default: `signed-in`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
Introspection=signed-in
*/ ?>
```

## MaxDepth

> The deepest nesting of fields a query may have, and the highest complexity (every field counts 1, a list field  
> counts its limit or first argument times the fields below it). A query over the limit is refused before it runs  
> (extensions.code DEPTH\_LIMIT or COMPLEXITY\_LIMIT).  

Default: `12`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
MaxDepth=12
*/ ?>
```

## MaxComplexity

Default: `1000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
MaxComplexity=1000
*/ ?>
```

## MaxListLimit

> The most items one list field returns (limit, first); a bigger value is lowered to it.  

Default: `100`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
MaxListLimit=100
*/ ?>
```

## DefaultListLimit

> The items a list field returns when the query does not say.  

Default: `25`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
DefaultListLimit=25
*/ ?>
```

## ImageAliasList

> Image aliases (settings/image.ini) the field variations lists when the query names none; url(alias:) takes any  
> alias of image.ini.  

Default: a list of 3: `small`, `medium`, `large`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
# an empty "ImageAliasList[]" first replaces the shipped list; without it the items are added
ImageAliasList[]
ImageAliasList[]=small
ImageAliasList[]=medium
ImageAliasList[]=large
*/ ?>
```

## Debug

> enabled or disabled (the default). Enabled: an error carries the PHP exception and its trace in  
> extensions.debug. Only for a development siteaccess: a trace tells paths and code to whoever asks.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLSettings]
Debug=disabled
*/ ?>
```

## Related

- [All blocks of graphql.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/graphql.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
