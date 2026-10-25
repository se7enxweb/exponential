---
title: exphelp.ini [ContentSettings]
audience: developer, admin
category: examples
summary: "The settings of [ContentSettings] in exphelp.ini: Enabled, CachePhpBinary, CacheSiteAccessList, ParentNode, Audiences, PublicAudiences, Platforms, Areas ..."
keywords: exphelp.ini, ContentSettings, INI, setting, Enabled, CachePhpBinary, CacheSiteAccessList, ParentNode, Audiences, PublicAudiences, Platforms, Areas, AudienceMap, ContextHelpIds, AnonymousContextHelp, EbooksOnRelease, ReviewMaxDays, ReviewListLimit
type: generated
generated: exp:help:examples
---
# exphelp.ini [ContentSettings]

The block `[ContentSettings]` of `settings/exphelp.ini`, with 14 setting(s).

Never edit `settings/exphelp.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/exphelp.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in an extension (read when the extension is active).

## Enabled

> Help maintained as content (doc/eng-us/features/6.0/help-content.md): topics, manuals, figures and the glossary as content  
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

## CachePhpBinary

> The rows of help content are kept in var/<site>/cache/exphelp/content. A web request never reads every help object  
> (an emptied cache): it starts "php bin/php/exphelpcontent.php cache -s <siteaccess>" in the background and shows no  
> help content until that is done. The PHP command line binary for it; empty: found next to the running PHP.  

Default: empty.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
CachePhpBinary=
*/ ?>
```

## CacheSiteAccessList

> The siteaccesses whose help cache exp:velocity deploy fills, besides those that have used it  
> (var/<site>/cache/exphelp/content/siteaccesses.json)  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
# an empty "CacheSiteAccessList[]" first replaces the shipped list; without it the items are added
CacheSiteAccessList[]
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
> The other audiences of the help (\[AudienceSettings\] AudienceNames\[\]), so the manuals built from the Markdown help  
> (exp:help:content build-manuals) keep who each page is for  

Default: a list of 42: `Visitors`, `Members`, `Editors`, `Administrators`, `Developers`, `Project managers`, `Sales & marketing`, `Partners`, `Advocates`, `Designers`, `Hosting companies`, `Application creators` ....

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
Audiences[]=Developers
Audiences[]=Project managers
Audiences[]=Sales & marketing
Audiences[]=Partners
Audiences[]=Advocates
Audiences[]=Designers
Audiences[]=Hosting companies
Audiences[]=Application creators
Audiences[]=Website creators
Audiences[]=Supporters
Audiences[]=Sponsors
Audiences[]=Customers
Audiences[]=Governments
Audiences[]=Businesses
Audiences[]=Press, media and influencers
Audiences[]=Trainers
# ... 22 more in the shipped file
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
> the pages of the Markdown help as topics (exp:help:content build-manuals): doc.<path of the page>  

Default: a list of 9: `account`, `content`, `media`, `search`, `users`, `setup`, `privacy`, `help`, `doc`.

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
Areas[]=doc
*/ ?>
```

## AudienceMap

> The help's audience (editor, admin, developer, user ...) of each audience option, in lower case. build-manuals  
> gives a page of the help audience X the first option that maps to X.  

Default: a hash of 42: `visitors=user`, `members=user`, `editors=editor`, `administrators=admin`, `developers=developer`, `project managers=project-manager`, `sales & marketing=sales-marketing`, `partners=partner`, `advocates=advocate`, `designers=designer`, `hosting companies=hosting`, `application creators=app-creator` ....

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
AudienceMap[developers]=developer
AudienceMap[project managers]=project-manager
AudienceMap[sales & marketing]=sales-marketing
AudienceMap[partners]=partner
AudienceMap[advocates]=advocate
AudienceMap[designers]=designer
AudienceMap[hosting companies]=hosting
AudienceMap[application creators]=app-creator
AudienceMap[website creators]=website-creator
AudienceMap[supporters]=supporter
AudienceMap[sponsors]=sponsor
AudienceMap[customers]=customer
AudienceMap[governments]=government
AudienceMap[businesses]=businesses
AudienceMap[press, media and influencers]=press
AudienceMap[trainers]=trainer
# ... 22 more in the shipped file
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

> The help\_ids other clients open (a mobile app, a site's JavaScript), so "exp:help:content check" reports one that no  
> topic has or whose topic is not Released (client.help-id-unknown, client.help-id-not-released). None by default.  
> ClientHelpIds\[\]  
> ClientHelpIds\[\]=account.log-in  
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

## EbooksOnRelease

> enabled: when an object of the help is set to Released, the e-books (EPUB, PDF) of every Released manual that  
> contains it are built again and stored as file objects below E-books (remote\_id help-ebooks), a new version when  
> the book changed (expHelpContentEbooks::onStateAssign(), called when states are assigned).  
> disabled: only ./console exp:help:content ebooks stores them.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
EbooksOnRelease=enabled
*/ ?>
```

## ReviewMaxDays

> Overdue reviews, listed on the help's dashboard to who can edit the topic or assign its state, and by  
> ./console exp:help:content review: a topic whose review date has passed, or that has been in Review longer than  
> ReviewMaxDays days since it last changed (the kernel keeps no date of a state change).  

Default: `14`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
ReviewMaxDays=14
*/ ?>
```

## ReviewListLimit

> At most this many overdue topics on the dashboard (the rest are counted)  

Default: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[ContentSettings]
ReviewListLimit=10
*/ ?>
```

## Related

- [All blocks of exphelp.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/exphelp.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
