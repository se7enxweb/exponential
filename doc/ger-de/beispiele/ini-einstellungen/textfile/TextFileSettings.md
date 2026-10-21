---
title: textfile.ini [TextFileSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TextFileSettings] in textfile.ini: TextFileEnabled, FileName, FilePath, FileFieldSeparator, DefaultUserGroupType, DefaultUserGroup, LoginAttribute, PasswordAttribute ..."
keywords: Einstellung, textfile.ini, TextFileSettings, INI, setting, TextFileEnabled, FileName, FilePath, FileFieldSeparator, DefaultUserGroupType, DefaultUserGroup, LoginAttribute, PasswordAttribute, FirstNameAttribute, LastNameAttribute, EmailAttribute
type: generated
generated: exp:help:examples
---
# textfile.ini [TextFileSettings]

Der Block `[TextFileSettings]` von `settings/textfile.ini`, mit 11 Einstellung(en).

Bearbeiten Sie `settings/textfile.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/textfile.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/textfile.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/textfile.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TextFileEnabled

> Auf true setzen, wenn der Login-Handler textfile verwendet wird  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/textfile.ini.append.php
[TextFileSettings]
TextFileEnabled=true
*/ ?>
```

## FileName

> Name der Textdatei  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/textfile.ini.append.php
[TextFileSettings]
FileName=
*/ ?>
```

## FilePath

> Wo die Textdatei liegt; kann jedes Verzeichnis unter dem Wurzelverzeichnis von Exponential  
> sein. Ist FilePath die Wurzel, muss die Datei im  
> Wurzelverzeichnis von Exponential gespeichert sein.  
> Beispiel: FilePath=var  

Standard: `root`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/textfile.ini.append.php
[TextFileSettings]
FilePath=root
*/ ?>
```

## FileFieldSeparator

> Kann ein Tabulator, ';', ',', '\\' oder ein anderes Trennzeichen sein  
> Beispiel: FileFieldSeparator=;  

Standard: `tab`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/textfile.ini.append.php
[TextFileSettings]
FileFieldSeparator=tab
*/ ?>
```

## DefaultUserGroupType

> Kann id oder name sein  

Standard: `id`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/textfile.ini.append.php
[TextFileSettings]
DefaultUserGroupType=id
*/ ?>
```

## DefaultUserGroup

> Standardort für Benutzer aus der Textdatei. Kann die Objekt-ID oder der Gruppenname sein,  
> je nach DefaultUserGroupType.  

Standard: `11`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/textfile.ini.append.php
[TextFileSettings]
DefaultUserGroup=11
*/ ?>
```

## LoginAttribute

> Spaltennummer des Feldes für das Login.  

Standard: `1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/textfile.ini.append.php
[TextFileSettings]
LoginAttribute=1
*/ ?>
```

## PasswordAttribute

> Spaltennummer des Feldes für das Passwort.  

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/textfile.ini.append.php
[TextFileSettings]
PasswordAttribute=3
*/ ?>
```

## FirstNameAttribute

> Spaltennummer des Feldes für den Vornamen.  

Standard: `4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/textfile.ini.append.php
[TextFileSettings]
FirstNameAttribute=4
*/ ?>
```

## LastNameAttribute

> Spaltennummer des Feldes für den Nachnamen.  

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/textfile.ini.append.php
[TextFileSettings]
LastNameAttribute=5
*/ ?>
```

## EmailAttribute

> Spaltennummer des Feldes für die E-Mail  

Standard: `2`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/textfile.ini.append.php
[TextFileSettings]
EmailAttribute=2
*/ ?>
```

## Siehe auch

- [Alle Blöcke von textfile.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/textfile.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
