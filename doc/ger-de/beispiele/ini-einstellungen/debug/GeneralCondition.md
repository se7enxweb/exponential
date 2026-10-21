---
title: debug.ini [GeneralCondition]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [GeneralCondition] in debug.ini: workflow-event, workflow-process, workflow-type, kernel-workflow-approve, kernel-content-view, kernel-content-view-cache, kernel-content-class, kernel-content-treenode ..."
keywords: Einstellung, debug.ini, GeneralCondition, INI, setting, workflow-event, workflow-process, workflow-type, kernel-workflow-approve, kernel-content-view, kernel-content-view-cache, kernel-content-class, kernel-content-treenode, kernel-content-object-copy, kernel-content-object-related-objects, kernel-content-object-limitation, kernel-content-edit, kernel-content-publish, kernel-persistent-object-cache, kernel-urltranslator, kernel-search-ezsearch, kernel-search-openfts, kernel-policy-limitation, kernel-role-edit, kernel-siteaccess, kernel-db-postgresql, kernel-user, kernel-notification, kernel-ssl-zone, kernel-clustering, kernel-ezpackage-store, module-run, collaboration-group-tree, collaboration-item-list, eztemplate-tree-cache
type: generated
generated: exp:help:examples
---
# debug.ini [GeneralCondition]

Der Block `[GeneralCondition]` von `settings/debug.ini`, mit 42 Einstellung(en).

Bearbeiten Sie `settings/debug.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/debug.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/debug.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/debug.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## workflow-event

> Bestimmte Bedingungen aktiviert/deaktiviert  
> Standard ist, alles aus zu haben und es in  
> Override-Dateien einzuschalten.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
workflow-event=disabled
*/ ?>
```

## workflow-process

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
workflow-process=disabled
*/ ?>
```

## workflow-type

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
workflow-type=disabled
*/ ?>
```

## kernel-workflow-approve

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-workflow-approve=disabled
*/ ?>
```

## kernel-content-view

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-content-view=disabled
*/ ?>
```

## kernel-content-view-cache

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-content-view-cache=disabled
*/ ?>
```

## kernel-content-class

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-content-class=disabled
*/ ?>
```

## kernel-content-treenode

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-content-treenode=disabled
*/ ?>
```

## kernel-content-object-copy

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-content-object-copy=disabled
*/ ?>
```

## kernel-content-object-related-objects

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-content-object-related-objects=disabled
*/ ?>
```

## kernel-content-object-limitation

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-content-object-limitation=disabled
*/ ?>
```

## kernel-content-edit

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-content-edit=disabled
*/ ?>
```

## kernel-content-publish

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-content-publish=disabled
*/ ?>
```

## kernel-persistent-object-cache

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-persistent-object-cache=disabled
*/ ?>
```

## kernel-urltranslator

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-urltranslator=disabled
*/ ?>
```

## kernel-search-ezsearch

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-search-ezsearch=disabled
*/ ?>
```

## kernel-search-openfts

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-search-openfts=disabled
*/ ?>
```

## kernel-policy-limitation

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-policy-limitation=disabled
*/ ?>
```

## kernel-role-edit

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-role-edit=disabled
*/ ?>
```

## kernel-siteaccess

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-siteaccess=disabled
*/ ?>
```

## kernel-db-postgresql

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-db-postgresql=disabled
*/ ?>
```

## kernel-user

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-user=disabled
*/ ?>
```

## kernel-notification

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-notification=disabled
*/ ?>
```

## kernel-ssl-zone

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-ssl-zone=disabled
*/ ?>
```

## kernel-clustering

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-clustering=disabled
*/ ?>
```

## kernel-ezpackage-store

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
kernel-ezpackage-store=disabled
*/ ?>
```

## module-run

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
module-run=disabled
*/ ?>
```

## collaboration-group-tree

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
collaboration-group-tree=disabled
*/ ?>
```

## collaboration-item-list

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
collaboration-item-list=disabled
*/ ?>
```

## eztemplate-tree-cache

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
eztemplate-tree-cache=disabled
*/ ?>
```

## eztemplate-compile

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
eztemplate-compile=disabled
*/ ?>
```

## i18n-tstranslator

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
i18n-tstranslator=disabled
*/ ?>
```

## ezfile-create

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
ezfile-create=disabled
*/ ?>
```

## lib-ezfile-seek

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
lib-ezfile-seek=disabled
*/ ?>
```

## lib-ezfile-openclose

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
lib-ezfile-openclose=disabled
*/ ?>
```

## lib-ezfile-readwrite

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
lib-ezfile-readwrite=disabled
*/ ?>
```

## lib-ezfile-copy

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
lib-ezfile-copy=disabled
*/ ?>
```

## lib-ezfile-gziplibz

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
lib-ezfile-gziplibz=disabled
*/ ?>
```

## lib-ezimage-shell

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
lib-ezimage-shell=disabled
*/ ?>
```

## lib-dbschema-transformation

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
lib-dbschema-transformation=disabled
*/ ?>
```

## lib-ezutil-web

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
lib-ezutil-web=disabled
*/ ?>
```

## lib-ezutils-dir

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/debug.ini.append.php
[GeneralCondition]
lib-ezutils-dir=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von debug.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/debug.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
