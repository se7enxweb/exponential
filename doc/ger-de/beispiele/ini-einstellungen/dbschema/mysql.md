---
title: dbschema.ini [mysql]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [mysql] in dbschema.ini: IndexTranslation"
keywords: Einstellung, dbschema.ini, mysql, INI, setting, IndexTranslation
type: generated
generated: exp:help:examples
---
# dbschema.ini [mysql]

Der Block `[mysql]` von `settings/dbschema.ini`, mit 1 Einstellung(en).

> Regeln zur Schema-Umwandlung je RDBMS  
>
> Syntax:  
> \[RDBMSName\]  
>
> # Column name transformation:  
> # maps the schema field name to a different column name, to avoid identifier length or reserved keywords issues  
> ColumnNameTranslation\[table.column\]=transformedColumnName  
>
> # Column type transformation:  
> # overrides the default field type mapping, so that a different type mapping can be used when required,  
> # for instance longtext;varchar2(4000) for oracle if GROUP BY is required on a field  
> ColumnTypeTranslation\[table.column\]=sourceType;targetType  
>
> # Column options  
> # Only NULL is supported now, for ezoracle. Removes the NOT NULL option Oracle does not support if set.  
> ColumnOptionTranslations\[table.column\]=options  
>
> # Index name translation  
> # Maps a different index name, to avoid index identifiers length limitation issues on some RDBMS.  
> IndexNameTranslation\[table.indexName\]=newIndexName  
> Umwandlungsregeln speziell für MySQL  

Bearbeiten Sie `settings/dbschema.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/dbschema.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/dbschema.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/dbschema.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## IndexTranslation

> Da wir noch keine Längenangaben von Feldern in Indizes speichern,  
> müssen wir sie für MySQL hinzufügen/entfernen.  

Standard: ein Hash mit 17: `ezimagefile.ezimagefile_file=filepath.mysql:length/200`, `eztrigger.eztrigger_fetch=name.mysql:length/25;module_name.mysql:length/50;function_name.mysql:length/50`, `ezurlalias.ezurlalias_desturl=destination_url.mysql:length/200`, `ezurlalias.ezurlalias_source_url=source_url.mysql:length/255`, `ezcontentobject_tree.ezcontentobject_tree_path_ident=path_identification_string.mysql:length/50`, `ezcontentobject_trash.ezcobj_trash_path_ident=path_identification_string.mysql:length/50`, `ezimagefile.ezimagefile_file=filepath.mysql:length/200`, `eztrigger.eztrigger_fetch=name.mysql:length/25;module_name.mysql:length/50;function_name.mysql:length/50`, `eztrigger.eztrigger_def_id=module_name.mysql:length/50;function_name.mysql:length/50`, `ezurlalias.ezurlalias_desturl=destination_url.mysql:length/200`, `ezurlalias.ezurlalias_source_url=source_url.mysql:length/255`, `ezurlalias_ml.ezurlalias_ml_text=text.mysql:length/32` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dbschema.ini.append.php
[mysql]
# ein leeres "IndexTranslation[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
IndexTranslation[]
IndexTranslation[ezimagefile.ezimagefile_file]=filepath.mysql:length/200
IndexTranslation[eztrigger.eztrigger_fetch]=name.mysql:length/25;module_name.mysql:length/50;function_name.mysql:length/50
IndexTranslation[ezurlalias.ezurlalias_desturl]=destination_url.mysql:length/200
IndexTranslation[ezurlalias.ezurlalias_source_url]=source_url.mysql:length/255
IndexTranslation[ezcontentobject_tree.ezcontentobject_tree_path_ident]=path_identification_string.mysql:length/50
IndexTranslation[ezcontentobject_trash.ezcobj_trash_path_ident]=path_identification_string.mysql:length/50
IndexTranslation[ezimagefile.ezimagefile_file]=filepath.mysql:length/200
IndexTranslation[eztrigger.eztrigger_fetch]=name.mysql:length/25;module_name.mysql:length/50;function_name.mysql:length/50
IndexTranslation[eztrigger.eztrigger_def_id]=module_name.mysql:length/50;function_name.mysql:length/50
IndexTranslation[ezurlalias.ezurlalias_desturl]=destination_url.mysql:length/200
IndexTranslation[ezurlalias.ezurlalias_source_url]=source_url.mysql:length/255
IndexTranslation[ezurlalias_ml.ezurlalias_ml_text]=text.mysql:length/32
IndexTranslation[ezurlalias_ml.ezurlalias_ml_text_lang]=text.mysql:length/32
IndexTranslation[ezurlalias_ml.ezurlalias_ml_par_lnk_txt]=text.mysql:length/32
IndexTranslation[ezurlalias_ml.ezurlalias_ml_par_act_id_lnk]=action.mysql:length/32
IndexTranslation[ezurlalias_ml.ezurlalias_ml_act_org]=action.mysql:length/32
IndexTranslation[ezurl.ezurl_url]=url.mysql:length/255
*/ ?>
```

## Siehe auch

- [Alle Blöcke von dbschema.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/dbschema.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
