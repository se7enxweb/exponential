---
title: exphelp.ini [AudienceSettings]
audience: developer, admin
category: examples
summary: "The settings of [AudienceSettings] in exphelp.ini: Audiences, AudienceNames, AudienceIcons, PathRules, TypeDefaults"
keywords: exphelp.ini, AudienceSettings, INI, setting, Audiences, AudienceNames, AudienceIcons, PathRules, TypeDefaults
type: generated
generated: exp:help:examples
---
# exphelp.ini [AudienceSettings]

The block `[AudienceSettings]` of `settings/exphelp.ini`, with 5 setting(s).

Never edit `settings/exphelp.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/exphelp.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in an extension (read when the extension is active).

## Audiences

> editor: writes and publishes content; admin: runs the site; developer: builds on it; user: a site's visitor or  
> member; project-manager: plans and leads projects built on it; sales-marketing: presents, sells and promotes it  
> and grows its community; partner: a company that builds, hosts or sells with Exponential; advocate: a community  
> advocate (street team); designer: designs themes and templates, builds, ships and maintains extensions;  
> hosting: a hosting company; app-creator / website-creator: builds applications / websites with it; supporter:  
> helps the project grow; sponsor: funds it; then trainer, press, businesses, government and customer, and from  
> community to student the audiences that people, organizations and builders of every kind find themselves in (the  
> dashboard groups their cards) (doc/eng-us/help/<folder>/ of each in PathRules\[\] below). Who a page is for:  
> its front matter (audience: editor, admin), else the first PathRules\[\]  
> that matches, else its file name (...-developer.md) or opening lines ("for editors"), else TypeDefaults\[<type>\].  
> The order here is the order of the dashboard's audience cards and the search filters.  

Default: a list of 41: `editor`, `admin`, `developer`, `user`, `project-manager`, `sales-marketing`, `partner`, `advocate`, `designer`, `hosting`, `app-creator`, `website-creator` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[AudienceSettings]
# an empty "Audiences[]" first replaces the shipped list; without it the items are added
Audiences[]
Audiences[]=editor
Audiences[]=admin
Audiences[]=developer
Audiences[]=user
Audiences[]=project-manager
Audiences[]=sales-marketing
Audiences[]=partner
Audiences[]=advocate
Audiences[]=designer
Audiences[]=hosting
Audiences[]=app-creator
Audiences[]=website-creator
Audiences[]=supporter
Audiences[]=sponsor
Audiences[]=trainer
Audiences[]=press
Audiences[]=businesses
Audiences[]=government
Audiences[]=customer
Audiences[]=community
# ... 21 more in the shipped file
*/ ?>
```

## AudienceNames

Default: a hash of 41: `editor=Editors`, `admin=Administrators`, `developer=Developers`, `user=Users`, `project-manager=Project managers`, `sales-marketing=Sales & marketing`, `partner=Partners`, `advocate=Advocates`, `designer=Designers`, `hosting=Hosting companies`, `app-creator=Application creators`, `website-creator=Website creators` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[AudienceSettings]
# an empty "AudienceNames[]" first replaces the shipped list; without it the items are added
AudienceNames[]
AudienceNames[editor]=Editors
AudienceNames[admin]=Administrators
AudienceNames[developer]=Developers
AudienceNames[user]=Users
AudienceNames[project-manager]=Project managers
AudienceNames[sales-marketing]=Sales & marketing
AudienceNames[partner]=Partners
AudienceNames[advocate]=Advocates
AudienceNames[designer]=Designers
AudienceNames[hosting]=Hosting companies
AudienceNames[app-creator]=Application creators
AudienceNames[website-creator]=Website creators
AudienceNames[supporter]=Supporters
AudienceNames[sponsor]=Sponsors
AudienceNames[customer]=Customers
AudienceNames[government]=Governments
AudienceNames[businesses]=Businesses
AudienceNames[press]=Press, media and influencers
AudienceNames[trainer]=Trainers
AudienceNames[community]=Community
# ... 21 more in the shipped file
*/ ?>
```

## AudienceIcons

> Icon names; each must have an .a4-bi-<name> class in stylesheets/components.css  

Default: a hash of 41: `editor=pencil`, `admin=shield-lock`, `developer=code-square`, `user=person`, `project-manager=diagram-3`, `sales-marketing=graph-up-arrow`, `partner=building`, `advocate=people`, `designer=palette`, `hosting=database`, `app-creator=phone`, `website-creator=globe` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[AudienceSettings]
# an empty "AudienceIcons[]" first replaces the shipped list; without it the items are added
AudienceIcons[]
AudienceIcons[editor]=pencil
AudienceIcons[admin]=shield-lock
AudienceIcons[developer]=code-square
AudienceIcons[user]=person
AudienceIcons[project-manager]=diagram-3
AudienceIcons[sales-marketing]=graph-up-arrow
AudienceIcons[partner]=building
AudienceIcons[advocate]=people
AudienceIcons[designer]=palette
AudienceIcons[hosting]=database
AudienceIcons[app-creator]=phone
AudienceIcons[website-creator]=globe
AudienceIcons[supporter]=star
AudienceIcons[sponsor]=piggy-bank
AudienceIcons[customer]=person-check
AudienceIcons[government]=bank
AudienceIcons[businesses]=briefcase
AudienceIcons[press]=megaphone
AudienceIcons[trainer]=easel
AudienceIcons[community]=chat-left-dots
# ... 21 more in the shipped file
*/ ?>
```

## PathRules

> <pattern>;<audience,audience>, first match wins ("\*" also matches "/")  

Default: a list of 100: `doc/eng-us/help/manual/*;editor,admin,user`, `doc/eng-us/help/leadership/*;project-manager`, `doc/eng-us/help/business/*;sales-marketing`, `doc/eng-us/help/partners/*;partner`, `doc/eng-us/help/advocates/*;advocate`, `doc/eng-us/help/designers/*;designer`, `doc/eng-us/help/hosting/*;hosting`, `doc/eng-us/help/creators/app-creators/*;app-creator`, `doc/eng-us/help/creators/website-creators/*;website-creator`, `doc/eng-us/help/creators/*;app-creator,website-creator`, `doc/eng-us/help/supporters/*;supporter`, `doc/eng-us/help/sponsors/*;sponsor` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[AudienceSettings]
# an empty "PathRules[]" first replaces the shipped list; without it the items are added
PathRules[]
PathRules[]=doc/eng-us/help/manual/*;editor,admin,user
PathRules[]=doc/eng-us/help/leadership/*;project-manager
PathRules[]=doc/eng-us/help/business/*;sales-marketing
PathRules[]=doc/eng-us/help/partners/*;partner
PathRules[]=doc/eng-us/help/advocates/*;advocate
PathRules[]=doc/eng-us/help/designers/*;designer
PathRules[]=doc/eng-us/help/hosting/*;hosting
PathRules[]=doc/eng-us/help/creators/app-creators/*;app-creator
PathRules[]=doc/eng-us/help/creators/website-creators/*;website-creator
PathRules[]=doc/eng-us/help/creators/*;app-creator,website-creator
PathRules[]=doc/eng-us/help/supporters/*;supporter
PathRules[]=doc/eng-us/help/sponsors/*;sponsor
PathRules[]=doc/eng-us/help/customers/*;customer
PathRules[]=doc/eng-us/help/government/*;government
PathRules[]=doc/eng-us/help/businesses/*;businesses
PathRules[]=doc/eng-us/help/press/*;press
PathRules[]=doc/eng-us/help/trainers/*;trainer
PathRules[]=doc/eng-us/help/community/*;community
PathRules[]=doc/eng-us/help/ecosystem/*;ecosystem
PathRules[]=doc/eng-us/help/php-developers/*;php-developer
# ... 80 more in the shipped file
*/ ?>
```

## TypeDefaults

> <type> => <audience,audience>, when nothing else says  
> an active extension's own documentation (extension/<name>/doc, its README ...)  

Default: a hash of 14: `help=editor,user`, `guide=admin`, `feature=admin,developer`, `bc=developer,admin`, `install=admin,developer`, `changelog=admin,developer`, `specification=developer`, `history=developer`, `example=developer,admin`, `generated=developer,admin`, `reference=developer,admin`, `project=developer,admin` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[AudienceSettings]
# an empty "TypeDefaults[]" first replaces the shipped list; without it the items are added
TypeDefaults[]
TypeDefaults[help]=editor,user
TypeDefaults[guide]=admin
TypeDefaults[feature]=admin,developer
TypeDefaults[bc]=developer,admin
TypeDefaults[install]=admin,developer
TypeDefaults[changelog]=admin,developer
TypeDefaults[specification]=developer
TypeDefaults[history]=developer
TypeDefaults[example]=developer,admin
TypeDefaults[generated]=developer,admin
TypeDefaults[reference]=developer,admin
TypeDefaults[project]=developer,admin
TypeDefaults[catalogue]=developer
TypeDefaults[extension]=editor,admin,developer
*/ ?>
```

## Related

- [All blocks of exphelp.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/exphelp.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
