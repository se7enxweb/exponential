---
title: dbschema.ini [LintChecker]
audience: developer, admin
category: examples
summary: "The settings of [LintChecker] in dbschema.ini: TableLimit, FieldLimit, IndexLimit, IgnoredTables, IgnoredFields, IgnoredFieldSyntax, IgnoredIndexes, NameMap"
keywords: dbschema.ini, LintChecker, INI, setting, TableLimit, FieldLimit, IndexLimit, IgnoredTables, IgnoredFields, IgnoredFieldSyntax, IgnoredIndexes, NameMap
type: generated
generated: exp:help:examples
---
# dbschema.ini [LintChecker]

The block `[LintChecker]` of `settings/dbschema.ini`, with 8 setting(s).

Never edit `settings/dbschema.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/dbschema.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/dbschema.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/dbschema.ini.append.php` in an extension (read when the extension is active).

## TableLimit

> Limitations on names and identifiers in the database  
> In general they should not exceed 30 which is the limit of Oracle  
> Table name limit, is 30 minus 4, this reserves room for suffixes  

Default: `26`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dbschema.ini.append.php
[LintChecker]
TableLimit=26
*/ ?>
```

## FieldLimit

Default: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dbschema.ini.append.php
[LintChecker]
FieldLimit=30
*/ ?>
```

## IndexLimit

Default: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dbschema.ini.append.php
[LintChecker]
IndexLimit=30
*/ ?>
```

## IgnoredTables

> A list of tables which do not get their names checked.  
> These are tables which are already in Exponential and  
> cannot be changed in the 3.x series.  
> They all have workarounds for the various DB handlers.  
>
> Each entry is the table name  
> e.g ezuser  

Default: a list of 5: `ezcollab_item_participant_link`, `ezgeneral_digest_user_settings`, `eznotificationcollection_item`, `ezproductcollection_item_opt`, `ezsubtree_notification_rule`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dbschema.ini.append.php
[LintChecker]
# an empty "IgnoredTables[]" first replaces the shipped list; without it the items are added
IgnoredTables[]
IgnoredTables[]=ezcollab_item_participant_link
IgnoredTables[]=ezgeneral_digest_user_settings
IgnoredTables[]=eznotificationcollection_item
IgnoredTables[]=ezproductcollection_item_opt
IgnoredTables[]=ezsubtree_notification_rule
*/ ?>
```

## IgnoredFields

> A list of table + fields which do not get their names checked.  
> These are fields which are already in Exponential and  
> cannot be changed in the 3.x series.  
> They all have workarounds for the various DB handlers.  
>
> Each entry is the table name, a dot and the field name  
> e.g ezuser.login  

Default: a list of 2: `ezenumobjectvalue.contentobject_attribute_version`, `ezurl_object_link.contentobject_attribute_version`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dbschema.ini.append.php
[LintChecker]
# an empty "IgnoredFields[]" first replaces the shipped list; without it the items are added
IgnoredFields[]
IgnoredFields[]=ezenumobjectvalue.contentobject_attribute_version
IgnoredFields[]=ezurl_object_link.contentobject_attribute_version
*/ ?>
```

## IgnoredFieldSyntax

> A list of table + fields which do not get their syntax checked.  
> These are fields which are already in Exponential and  
> cannot be changed in the 3.x series.  
> They all have workarounds for the various DB handlers.  
>
> Each entry is the table name, a dot and the field name  
> e.g ezuser.login  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dbschema.ini.append.php
[LintChecker]
# an empty "IgnoredFieldSyntax[]" first replaces the shipped list; without it the items are added
IgnoredFieldSyntax[]
*/ ?>
```

## IgnoredIndexes

> A list of indexes which do not get their names checked.  
> These are indexes which are already in Exponential and  
> cannot be changed in the 3.x series.  
> They all have workarounds for the various DB handlers.  
>
> Each entry is the index name  
> e.g ezuser\_role  

Default: a list of 16: `ezcontentobject_attribute_co_id_ver_lang_code`, `ezcontentobject_attribute_language_code`, `ezcontentobject_tree_path_ident`, `ezcobj_trash_path_ident`, `ezenumvalue_co_cl_attr_id_co_class_att_ver`, `ezmodule_run_workflow_process_id_s`, `ezoperation_memento_memento_key_main`, `ezproductcollection_item_contentobject_id`, `ezproductcollection_item_productcollection_id`, `ezproductcollection_item_opt_item_id`, `ezsearch_object_word_link_frequency`, `ezsearch_object_word_link_identifier` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dbschema.ini.append.php
[LintChecker]
# an empty "IgnoredIndexes[]" first replaces the shipped list; without it the items are added
IgnoredIndexes[]
IgnoredIndexes[]=ezcontentobject_attribute_co_id_ver_lang_code
IgnoredIndexes[]=ezcontentobject_attribute_language_code
IgnoredIndexes[]=ezcontentobject_tree_path_ident
IgnoredIndexes[]=ezcobj_trash_path_ident
IgnoredIndexes[]=ezenumvalue_co_cl_attr_id_co_class_att_ver
IgnoredIndexes[]=ezmodule_run_workflow_process_id_s
IgnoredIndexes[]=ezoperation_memento_memento_key_main
IgnoredIndexes[]=ezproductcollection_item_contentobject_id
IgnoredIndexes[]=ezproductcollection_item_productcollection_id
IgnoredIndexes[]=ezproductcollection_item_opt_item_id
IgnoredIndexes[]=ezsearch_object_word_link_frequency
IgnoredIndexes[]=ezsearch_object_word_link_identifier
IgnoredIndexes[]=ezsearch_object_word_link_integer_value
IgnoredIndexes[]=ezsearch_object_word_link_object
IgnoredIndexes[]=ezsubtree_notification_rule_user_id
IgnoredIndexes[]=ezwaituntildateevalue_wf_ev_id_wf_ver
*/ ?>
```

## NameMap

> Mapping of long words to short words  
> This will be used whenever an identifier or name is to long  
> each entry will be tried in order until the string is short enough.  
> e.g. NameMap\[notification\]=notif  
> combined words that are really shortened  
> General word shortening  

Default: a hash of 26: `enumobjectvalue=enov`, `enumvalue=env`, `waituntildateeval=wude`, `co_cl_attr_id_co_class_att_ver=ccai_ccav`, `contentobject=cobj`, `content=co`, `object=obj`, `attribute=attr`, `version=ver`, `notification=notif`, `collection=coll`, `workflow=wflw` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dbschema.ini.append.php
[LintChecker]
# an empty "NameMap[]" first replaces the shipped list; without it the items are added
NameMap[]
NameMap[enumobjectvalue]=enov
NameMap[enumvalue]=env
NameMap[waituntildateeval]=wude
NameMap[co_cl_attr_id_co_class_att_ver]=ccai_ccav
NameMap[contentobject]=cobj
NameMap[content]=co
NameMap[object]=obj
NameMap[attribute]=attr
NameMap[version]=ver
NameMap[notification]=notif
NameMap[collection]=coll
NameMap[workflow]=wflw
NameMap[product]=prod
NameMap[participant]=part
NameMap[process]=prcs
NameMap[operation]=op
NameMap[memento]=mem
NameMap[search]=src
NameMap[integer]=int
NameMap[frequency]=frq
# ... 6 more in the shipped file
*/ ?>
```

## Related

- [All blocks of dbschema.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/dbschema.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
