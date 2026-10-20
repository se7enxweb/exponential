---
title: debug.ini [GeneralCondition]
audience: developer, admin
category: examples
summary: "The settings of [GeneralCondition] in debug.ini: workflow-event, workflow-process, workflow-type, kernel-workflow-approve, kernel-content-view, kernel-content-view-cache, kernel-content-class, kernel-content-treenode ..."
keywords: debug.ini, GeneralCondition, INI, setting, workflow-event, workflow-process, workflow-type, kernel-workflow-approve, kernel-content-view, kernel-content-view-cache, kernel-content-class, kernel-content-treenode, kernel-content-object-copy, kernel-content-object-related-objects, kernel-content-object-limitation, kernel-content-edit, kernel-content-publish, kernel-persistent-object-cache, kernel-urltranslator, kernel-search-ezsearch, kernel-search-openfts, kernel-policy-limitation, kernel-role-edit, kernel-siteaccess, kernel-db-postgresql, kernel-user, kernel-notification, kernel-ssl-zone, kernel-clustering, kernel-ezpackage-store, module-run, collaboration-group-tree, collaboration-item-list, eztemplate-tree-cache
type: generated
generated: exp:help:examples
---
# debug.ini [GeneralCondition]

The block `[GeneralCondition]` of `settings/debug.ini`, with 42 setting(s).

Never edit `settings/debug.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/debug.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/debug.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/debug.ini.append.php` in an extension (read when the extension is active).

## workflow-event

> Enabled/disabled specific conditions  
> Default is to have everything off and to turn it on  
> in override files.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
workflow-event=disabled
*/ ?>
```

## workflow-process

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
workflow-process=disabled
*/ ?>
```

## workflow-type

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
workflow-type=disabled
*/ ?>
```

## kernel-workflow-approve

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-workflow-approve=disabled
*/ ?>
```

## kernel-content-view

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-content-view=disabled
*/ ?>
```

## kernel-content-view-cache

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-content-view-cache=disabled
*/ ?>
```

## kernel-content-class

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-content-class=disabled
*/ ?>
```

## kernel-content-treenode

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-content-treenode=disabled
*/ ?>
```

## kernel-content-object-copy

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-content-object-copy=disabled
*/ ?>
```

## kernel-content-object-related-objects

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-content-object-related-objects=disabled
*/ ?>
```

## kernel-content-object-limitation

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-content-object-limitation=disabled
*/ ?>
```

## kernel-content-edit

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-content-edit=disabled
*/ ?>
```

## kernel-content-publish

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-content-publish=disabled
*/ ?>
```

## kernel-persistent-object-cache

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-persistent-object-cache=disabled
*/ ?>
```

## kernel-urltranslator

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-urltranslator=disabled
*/ ?>
```

## kernel-search-ezsearch

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-search-ezsearch=disabled
*/ ?>
```

## kernel-search-openfts

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-search-openfts=disabled
*/ ?>
```

## kernel-policy-limitation

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-policy-limitation=disabled
*/ ?>
```

## kernel-role-edit

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-role-edit=disabled
*/ ?>
```

## kernel-siteaccess

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-siteaccess=disabled
*/ ?>
```

## kernel-db-postgresql

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-db-postgresql=disabled
*/ ?>
```

## kernel-user

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-user=disabled
*/ ?>
```

## kernel-notification

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-notification=disabled
*/ ?>
```

## kernel-ssl-zone

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-ssl-zone=disabled
*/ ?>
```

## kernel-clustering

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-clustering=disabled
*/ ?>
```

## kernel-ezpackage-store

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-ezpackage-store=disabled
*/ ?>
```

## module-run

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
module-run=disabled
*/ ?>
```

## collaboration-group-tree

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
collaboration-group-tree=disabled
*/ ?>
```

## collaboration-item-list

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
collaboration-item-list=disabled
*/ ?>
```

## eztemplate-tree-cache

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
eztemplate-tree-cache=disabled
*/ ?>
```

## eztemplate-compile

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
eztemplate-compile=disabled
*/ ?>
```

## i18n-tstranslator

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
i18n-tstranslator=disabled
*/ ?>
```

## ezfile-create

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
ezfile-create=disabled
*/ ?>
```

## lib-ezfile-seek

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
lib-ezfile-seek=disabled
*/ ?>
```

## lib-ezfile-openclose

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
lib-ezfile-openclose=disabled
*/ ?>
```

## lib-ezfile-readwrite

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
lib-ezfile-readwrite=disabled
*/ ?>
```

## lib-ezfile-copy

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
lib-ezfile-copy=disabled
*/ ?>
```

## lib-ezfile-gziplibz

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
lib-ezfile-gziplibz=disabled
*/ ?>
```

## lib-ezimage-shell

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
lib-ezimage-shell=disabled
*/ ?>
```

## lib-dbschema-transformation

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
lib-dbschema-transformation=disabled
*/ ?>
```

## lib-ezutil-web

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
lib-ezutil-web=disabled
*/ ?>
```

## lib-ezutils-dir

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
lib-ezutils-dir=disabled
*/ ?>
```

## Related

- [All blocks of debug.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/debug.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
