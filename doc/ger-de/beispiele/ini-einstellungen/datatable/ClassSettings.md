---
title: datatable.ini [ClassSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ClassSettings] in datatable.ini: Connection"
keywords: Einstellung, datatable.ini, ClassSettings, INI, setting, Connection
type: generated
generated: exp:help:examples
---
# datatable.ini [ClassSettings]

Der Block `[ClassSettings]` von `settings/datatable.ini`, mit 1 Einstellung(en).

> Deklarierte Verbindungen: ein Block pro Verbindung, \[Connection\_<identifier>\], in der Administration nur lesbar.  
>
> \[Connection\_archive\]  
> Name=Archive database  
> Engine=mysql                 sqlite, mysql, postgresql, oracle, sqlserver oder mongodb  
> Server=db2.example.com  
> Port=3306  
> Database=archive             bei sqlite die Datei, relativ zur Installation (in SQLiteDirectories)  
> User=archive  
> Secret=env:ARCHIVE\_DB\_PASSWORD  
> Charset=utf8mb4  
> ReadOnly=false  

Bearbeiten Sie `settings/datatable.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/datatable.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/datatable.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/datatable.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Connection

> Die Verbindung, die eine persistente Klasse nutzt, wenn ihre definition() keine nennt ('connection' => '<identifier>'):  
> Connection\[<Klassenname>\]=<identifier>  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/datatable.ini.append.php
[ClassSettings]
# ein leeres "Connection[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Connection[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von datatable.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/datatable.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
