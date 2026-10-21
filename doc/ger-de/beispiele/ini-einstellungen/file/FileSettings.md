---
title: file.ini [FileSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [FileSettings] in file.ini: SystemUserCheck, RequiredSystemUser, Handlers, FileExtensionBlackList"
keywords: Einstellung, file.ini, FileSettings, INI, setting, SystemUserCheck, RequiredSystemUser, Handlers, FileExtensionBlackList
type: generated
generated: exp:help:examples
---
# file.ini [FileSettings]

Der Block `[FileSettings]` von `settings/file.ini`, mit 4 Einstellung(en).

> Repositories\[\]=kernel/classes  
> Eine Liste der Erweiterungen, die Binary-Handler haben  
> Üblicherweise legt man in der Erweiterung eine Datei settings/file.ini.append.php  
> an und trägt den Namen der Erweiterung ein, um automatisch  
> die Handler der Erweiterung zu erhalten, wenn sie eingeschaltet ist.  
> Die Handler müssen im Unterverzeichnis "binaryhandlers" der Erweiterung liegen.  
> ExtensionRepositories\[\]  

Bearbeiten Sie `settings/file.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/file.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/file.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/file.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## SystemUserCheck

> (Nur UNIX) Ob geprüft wird, ob der Benutzer, der ein eZScript ausführt, derselbe  
> ist wie in der Einstellung SystemUser. Stimmen die Benutzer nicht überein, wird die  
> Ausführung des Skripts angehalten.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[FileSettings]
SystemUserCheck=disabled
*/ ?>
```

## RequiredSystemUser

Standard: `www-data`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[FileSettings]
RequiredSystemUser=www-data
*/ ?>
```

## Handlers

> Allgemeiner gzip-Handler, verwendet für die Arbeit die zlib- oder Shell-Handler  
> Bestimmter gzip-Handler, entweder mit der Erweiterung zlib oder dem Shell-Befehl gzip  

Standard: ein Hash mit 3: `gzip=ezgzipcompressionhandler`, `gzipzlib=ezgzipzlibcompressionhandler`, `gzipshell=ezgzipshellcompressionhandler`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[FileSettings]
# ein leeres "Handlers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Handlers[]
Handlers[gzip]=ezgzipcompressionhandler
Handlers[gzipzlib]=ezgzipzlibcompressionhandler
Handlers[gzipshell]=ezgzipshellcompressionhandler
*/ ?>
```

## FileExtensionBlackList

> Die Sperrliste der Dateiendungen dient zur Prüfung hochgeladener Dateien.  
> Dateitypen auf der Sperrliste werden abgelehnt, aus Sicherheits- oder anderen Gründen.  

Standard: eine Liste mit 7: `php`, `php3`, `phar`, `phpt`, `pht`, `phtml`, `pgif`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/file.ini.append.php
[FileSettings]
# ein leeres "FileExtensionBlackList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
FileExtensionBlackList[]
FileExtensionBlackList[]=php
FileExtensionBlackList[]=php3
FileExtensionBlackList[]=phar
FileExtensionBlackList[]=phpt
FileExtensionBlackList[]=pht
FileExtensionBlackList[]=phtml
FileExtensionBlackList[]=pgif
*/ ?>
```

## Siehe auch

- [Alle Blöcke von file.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/file.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
