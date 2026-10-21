---
title: dbschema.ini [LintChecker]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [LintChecker] in dbschema.ini: TableLimit, FieldLimit, IndexLimit, IgnoredTables, IgnoredFields, IgnoredFieldSyntax, IgnoredIndexes, NameMap"
keywords: Einstellung, dbschema.ini, LintChecker, INI, setting, TableLimit, FieldLimit, IndexLimit, IgnoredTables, IgnoredFields, IgnoredFieldSyntax, IgnoredIndexes, NameMap
type: generated
generated: exp:help:examples
---
# dbschema.ini [LintChecker]

Der Block `[LintChecker]` von `settings/dbschema.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/dbschema.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/dbschema.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/dbschema.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/dbschema.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TableLimit

> Grenzen für Namen und Bezeichner in der Datenbank  
> Im Allgemeinen sollten sie 30 nicht überschreiten, die Grenze von Oracle  
> Grenze für Tabellennamen ist 30 minus 4, das lässt Platz für Suffixe  

Standard: `26`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dbschema.ini.append.php
[LintChecker]
TableLimit=26
*/ ?>
```

## FieldLimit

Standard: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dbschema.ini.append.php
[LintChecker]
FieldLimit=30
*/ ?>
```

## IndexLimit

Standard: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dbschema.ini.append.php
[LintChecker]
IndexLimit=30
*/ ?>
```

## IgnoredTables

> Eine Liste von Tabellen, deren Namen nicht geprüft werden.  
> Das sind Tabellen, die es in Exponential schon gibt und die  
> in der Serie 3.x nicht geändert werden können.  
> Für alle gibt es Umgehungen in den verschiedenen Datenbank-Handlern.  
>
> Jeder Eintrag ist der Tabellenname  
> z. B. ezuser  

Standard: eine Liste mit 5: `ezcollab_item_participant_link`, `ezgeneral_digest_user_settings`, `eznotificationcollection_item`, `ezproductcollection_item_opt`, `ezsubtree_notification_rule`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dbschema.ini.append.php
[LintChecker]
# ein leeres "IgnoredTables[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
IgnoredTables[]
IgnoredTables[]=ezcollab_item_participant_link
IgnoredTables[]=ezgeneral_digest_user_settings
IgnoredTables[]=eznotificationcollection_item
IgnoredTables[]=ezproductcollection_item_opt
IgnoredTables[]=ezsubtree_notification_rule
*/ ?>
```

## IgnoredFields

> Eine Liste von Tabellen und Feldern, deren Namen nicht geprüft werden.  
> Das sind Felder, die es in Exponential schon gibt und die  
> in der Serie 3.x nicht geändert werden können.  
> Für alle gibt es Umgehungen in den verschiedenen Datenbank-Handlern.  
>
> Jeder Eintrag ist der Tabellenname, ein Punkt und der Feldname  
> z. B. ezuser.login  

Standard: eine Liste mit 2: `ezenumobjectvalue.contentobject_attribute_version`, `ezurl_object_link.contentobject_attribute_version`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dbschema.ini.append.php
[LintChecker]
# ein leeres "IgnoredFields[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
IgnoredFields[]
IgnoredFields[]=ezenumobjectvalue.contentobject_attribute_version
IgnoredFields[]=ezurl_object_link.contentobject_attribute_version
*/ ?>
```

## IgnoredFieldSyntax

> Eine Liste von Tabellen und Feldern, deren Syntax nicht geprüft wird.  
> Das sind Felder, die es in Exponential schon gibt und die  
> in der Serie 3.x nicht geändert werden können.  
> Für alle gibt es Umgehungen in den verschiedenen Datenbank-Handlern.  
>
> Jeder Eintrag ist der Tabellenname, ein Punkt und der Feldname  
> z. B. ezuser.login  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dbschema.ini.append.php
[LintChecker]
# ein leeres "IgnoredFieldSyntax[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
IgnoredFieldSyntax[]
*/ ?>
```

## IgnoredIndexes

> Eine Liste von Indizes, deren Namen nicht geprüft werden.  
> Das sind Indizes, die es in Exponential schon gibt und die  
> in der Serie 3.x nicht geändert werden können.  
> Für alle gibt es Umgehungen in den verschiedenen Datenbank-Handlern.  
>
> Jeder Eintrag ist der Name des Index  
> z. B. ezuser\_role  

Standard: eine Liste mit 16: `ezcontentobject_attribute_co_id_ver_lang_code`, `ezcontentobject_attribute_language_code`, `ezcontentobject_tree_path_ident`, `ezcobj_trash_path_ident`, `ezenumvalue_co_cl_attr_id_co_class_att_ver`, `ezmodule_run_workflow_process_id_s`, `ezoperation_memento_memento_key_main`, `ezproductcollection_item_contentobject_id`, `ezproductcollection_item_productcollection_id`, `ezproductcollection_item_opt_item_id`, `ezsearch_object_word_link_frequency`, `ezsearch_object_word_link_identifier` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dbschema.ini.append.php
[LintChecker]
# ein leeres "IgnoredIndexes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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

> Zuordnung langer Wörter zu kurzen Wörtern  
> Wird verwendet, wann immer ein Bezeichner oder Name zu lang ist;  
> jeder Eintrag wird der Reihe nach versucht, bis die Zeichenkette kurz genug ist.  
> z. B. NameMap\[notification\]=notif  
> zusammengesetzte Wörter, die stark gekürzt werden  
> Allgemeine Wortkürzungen  

Standard: ein Hash mit 26: `enumobjectvalue=enov`, `enumvalue=env`, `waituntildateeval=wude`, `co_cl_attr_id_co_class_att_ver=ccai_ccav`, `contentobject=cobj`, `content=co`, `object=obj`, `attribute=attr`, `version=ver`, `notification=notif`, `collection=coll`, `workflow=wflw` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dbschema.ini.append.php
[LintChecker]
# ein leeres "NameMap[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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
# ... 6 weitere in der ausgelieferten Datei
*/ ?>
```

## Siehe auch

- [Alle Blöcke von dbschema.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/dbschema.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
