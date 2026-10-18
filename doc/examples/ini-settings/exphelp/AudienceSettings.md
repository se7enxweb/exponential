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
> and grows its community. Who a page is for: its front matter (audience: editor, admin), else the first PathRules\[\]  
> that matches, else its file name (...-developer.md) or opening lines ("for editors"), else TypeDefaults\[<type>\].  
> The order here is the order of the dashboard's audience cards and the search filters.  

Default: a list of 6: `editor`, `admin`, `developer`, `user`, `project-manager`, `sales-marketing`.

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
*/ ?>
```

## AudienceNames

Default: a hash of 6: `editor=Editors`, `admin=Administrators`, `developer=Developers`, `user=Users`, `project-manager=Project managers`, `sales-marketing=Sales & marketing`.

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
*/ ?>
```

## AudienceIcons

> Icon names; each must have an .a4-bi-<name> class in stylesheets/components.css  

Default: a hash of 6: `editor=pencil`, `admin=shield-lock`, `developer=code-square`, `user=person`, `project-manager=diagram-3`, `sales-marketing=graph-up-arrow`.

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
*/ ?>
```

## PathRules

> <pattern>;<audience,audience>, first match wins ("\*" also matches "/")  

Default: a list of 58: `doc/help/leadership/*;project-manager`, `doc/help/business/*;sales-marketing`, `doc/LICENSE-GFDL-1.3.txt;editor,admin,developer,user`, `doc/glossary.md;editor,admin,developer,user`, `doc/languages.md;admin,developer`, `doc/guides/README.md;editor,admin,developer`, `doc/guides/getting-started.md;admin,developer`, `doc/guides/content-model-and-editing.md;editor,developer`, `doc/guides/bookmarks.md;editor`, `doc/guides/collected-information.md;editor,admin`, `doc/guides/urls-and-aliases.md;editor,admin`, `doc/guides/content-history.md;editor,admin` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[AudienceSettings]
# an empty "PathRules[]" first replaces the shipped list; without it the items are added
PathRules[]
PathRules[]=doc/help/leadership/*;project-manager
PathRules[]=doc/help/business/*;sales-marketing
PathRules[]=doc/LICENSE-GFDL-1.3.txt;editor,admin,developer,user
PathRules[]=doc/glossary.md;editor,admin,developer,user
PathRules[]=doc/languages.md;admin,developer
PathRules[]=doc/guides/README.md;editor,admin,developer
PathRules[]=doc/guides/getting-started.md;admin,developer
PathRules[]=doc/guides/content-model-and-editing.md;editor,developer
PathRules[]=doc/guides/bookmarks.md;editor
PathRules[]=doc/guides/collected-information.md;editor,admin
PathRules[]=doc/guides/urls-and-aliases.md;editor,admin
PathRules[]=doc/guides/content-history.md;editor,admin
PathRules[]=doc/guides/drafts-and-pending.md;editor
PathRules[]=doc/guides/pdf-exports.md;editor,admin
PathRules[]=doc/guides/rss-feeds.md;admin
PathRules[]=doc/guides/content-languages.md;editor,admin
PathRules[]=doc/guides/test-language-lorem-ipsum.md;admin,developer
PathRules[]=doc/guides/ui-switch-design.md;admin
PathRules[]=doc/guides/ui-switch-settings.md;admin
PathRules[]=doc/guides/two-factor-login.md;user,admin
# ... 38 more in the shipped file
*/ ?>
```

## TypeDefaults

> <type> => <audience,audience>, when nothing else says  

Default: a hash of 11: `help=editor,user`, `guide=admin`, `feature=admin,developer`, `bc=developer,admin`, `install=admin,developer`, `changelog=admin,developer`, `specification=developer`, `history=developer`, `example=developer,admin`, `generated=developer,admin`, `reference=developer,admin`.

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
*/ ?>
```

## Related

- [All blocks of exphelp.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/exphelp.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
