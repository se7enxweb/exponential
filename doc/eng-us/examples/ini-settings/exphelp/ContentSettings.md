---
title: exphelp.ini [ContentSettings]
audience: developer, admin
category: examples
summary: "The settings of [ContentSettings] in exphelp.ini: Enabled, ParentNode, Audiences, PublicAudiences, Platforms, Areas, AudienceMap, ContextHelpIds ..."
keywords: exphelp.ini, ContentSettings, INI, setting, Enabled, ParentNode, Audiences, PublicAudiences, Platforms, Areas, AudienceMap, ContextHelpIds, AnonymousContextHelp
type: generated
generated: exp:help:examples
---
# exphelp.ini [ContentSettings]

The block `[ContentSettings]` of `settings/exphelp.ini`, with 9 setting(s).

Never edit `settings/exphelp.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/exphelp.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in an extension (read when the extension is active).

## Enabled

> Help maintained as content (doc/en/us/features/6.0/help-content.md): topics, manuals, figures and the glossary as content  
> of the site, created by ./console exp:help:content. Nothing here matters until that command ran.  
> disabled: exphelp/topic, exphelp/manual and exphelp/context are 404 and the help shows no topics  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
Enabled=enabled
*/ ?>
```

## ParentNode

> Where the folder "Help" (remote\_id help-root) is created: media (default), content, users, or a remote\_id. Not in  
> the site's content tree: help must not show up in its menus, listings and feeds.  

Default: `media`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
ParentNode=media
*/ ?>
```

## Audiences

> The audience options of the help\_\* classes, in their stored order. ONLY APPEND: ezselection stores the position of  
> an option, so a reordered list gives every topic another audience. The command appends missing ones.  

Default: a list of 4: `Visitors`, `Members`, `Editors`, `Administrators`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
# an empty "Audiences[]" first replaces the shipped list; without it the items are added
Audiences[]
Audiences[]=Visitors
Audiences[]=Members
Audiences[]=Editors
Audiences[]=Administrators
*/ ?>
```

## PublicAudiences

> The audiences whose topics are public (section help\_public, readable without login when Released)  

Default: a list of 1: `Visitors`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
# an empty "PublicAudiences[]" first replaces the shipped list; without it the items are added
PublicAudiences[]
PublicAudiences[]=Visitors
*/ ?>
```

## Platforms

> The platform options (only append)  

Default: a list of 3: `Web`, `Admin`, `Mobile`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
# an empty "Platforms[]" first replaces the shipped list; without it the items are added
Platforms[]
Platforms[]=Web
Platforms[]=Admin
Platforms[]=Mobile
*/ ?>
```

## Areas

> The areas of help\_ids (area.topic\[.variant\]); the validator warns about any other  

Default: a list of 8: `account`, `content`, `media`, `search`, `users`, `setup`, `privacy`, `help`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
# an empty "Areas[]" first replaces the shipped list; without it the items are added
Areas[]
Areas[]=account
Areas[]=content
Areas[]=media
Areas[]=search
Areas[]=users
Areas[]=setup
Areas[]=privacy
Areas[]=help
*/ ?>
```

## AudienceMap

> The help's audience (editor, admin, developer, user) of each audience option, in lower case  

Default: a hash of 4: `visitors=user`, `members=user`, `editors=editor`, `administrators=admin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
# an empty "AudienceMap[]" first replaces the shipped list; without it the items are added
AudienceMap[]
AudienceMap[visitors]=user
AudienceMap[members]=user
AudienceMap[editors]=editor
AudienceMap[administrators]=admin
*/ ?>
```

## ContextHelpIds

> Context help by help\_id: the "?" Help control of these pages opens the Released topic (keys as \[ContextHelp\] Map\[\])  

Default: a hash of 2: `content/edit=content.publish-article`, `content/history=content.versions`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
# an empty "ContextHelpIds[]" first replaces the shipped list; without it the items are added
ContextHelpIds[]
ContextHelpIds[content/edit]=content.publish-article
ContextHelpIds[content/history]=content.versions
*/ ?>
```

## AnonymousContextHelp

> enabled: the roles step of exp:help:content also gives Anonymous exphelp/context, so the JSON of public, Released  
> topics can be read without login. Off by default: a security-relevant choice of each site.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
AnonymousContextHelp=disabled
*/ ?>
```

## Related

- [All blocks of exphelp.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/exphelp.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
