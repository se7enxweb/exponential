---
title: site.ini [FileSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [FileSettings] in site.ini: RenameBeforeDelete, RenameExpiredCaches, TemporaryDir, TemporaryPermissions, StorageDir, StorageDirPermissions, StorageFilePermissions, LogFilePermissions ..."
keywords: Einstellung, site.ini, FileSettings, INI, setting, RenameBeforeDelete, RenameExpiredCaches, TemporaryDir, TemporaryPermissions, StorageDir, StorageDirPermissions, StorageFilePermissions, LogFilePermissions, LogFileMaxSize, LogFileRotateCount, LogFileCompress, LogFileMaxAge, DirDepth, VarDir, CacheDir, LogDir, UseGlobalLogDir, LogVarDir, CacheVarDir, ExpiryDir, INICacheDir, AllowedDeletionDirs
type: generated
generated: exp:help:examples
---
# site.ini [FileSettings]

Der Block `[FileSettings]` von `settings/site.ini`, mit 22 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RenameBeforeDelete

> Das Leeren eines Caches, der von der Festplatte gelöscht wird (kompilierte Templates, Override,  
> INI und andere), benennt sein Verzeichnis zuerst in .cleanup-trash im Cache-  
> verzeichnis um, benannt nach dem Cache mit angehängter Zeit, und löscht es erst  
> nach dem letzten Cache des Leerens. Die Umbenennung geschieht  
> sofort, sodass nichts, was während des Löschens erzeugt wird, davon erfasst wird.  
> disabled löscht wie bisher Datei für Datei. Nur für Verzeichnisse auf dem lokalen  
> Dateisystem; ein in einer Cluster-Datenbank gehaltener Cache ist nicht betroffen.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
RenameBeforeDelete=enabled
*/ ?>
```

## RenameExpiredCaches

> View-Cache, Cache-Block, Benutzerinformationen und Übersetzungs-Cache werden über einen  
> Zeitstempel in expiry.php geleert: Ihre Dateien werden nicht mehr ausgeliefert, bleiben aber auf der Festplatte, bis  
> der Cronjob cache\_cleanup sie entfernt. enabled verschiebt ihre Verzeichnisse ebenfalls in  
> .cleanup-trash, wenn ein solcher Cache in der Administrations-  
> oberfläche oder mit bin/php/ezcache.php geleert wird, und löscht sie nach dem letzten Cache  
> des Leerens, sodass ein Leeren nichts zurücklässt. Der Zeitstempel wird weiterhin zuerst  
> gesetzt. Braucht RenameBeforeDelete: Sie in der Anfrage Datei für Datei zu löschen ist genau,  
> was der Zeitstempel vermeiden soll; ist es deaktiviert, wird also nichts entfernt.  
> Das Veröffentlichen von Inhalten verschiebt wie bisher nur die Zeitstempel.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
RenameExpiredCaches=enabled
*/ ?>
```

## TemporaryDir

> Das Verzeichnis, das Exponential für temporäre Dateien verwendet,  
> z. B. beim Erzeugen einer Bildvariante  

Standard: `/tmp/`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
TemporaryDir=/tmp/
*/ ?>
```

## TemporaryPermissions

> Die Berechtigungen unten werden durch EZP\_DIR\_MODE\_MAX / EZP\_FILE\_MODE\_MAX in  
> config.php begrenzt, wo gesetzt (doc/eng-us/bc/6.0/file-modes.md): Ein Modus hier ist nie weiter  
> als diese.  
> Berechtigung für temporäre Dateien; 0777 bedeutet, dass jeder  
> die Dateien lesen und schreiben kann, was als Sicherheitsrisiko gelten kann.  
> Besser ist 0770, wobei Sie sicherstellen, dass der Webserver den richtigen  
> Benutzer-/Gruppenzugriff hat.  

Standard: `0777`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
TemporaryPermissions=0777
*/ ?>
```

## StorageDir

> Wo neue Dateien gespeichert werden, relativ zum var-Verzeichnis  

Standard: `storage`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
StorageDir=storage
*/ ?>
```

## StorageDirPermissions

> Berechtigung für Speicherverzeichnisse; 0777 bedeutet, dass jeder  
> die Verzeichnisse lesen und schreiben kann, was als Sicherheitsrisiko gelten kann.  
> Besser ist 0770, wobei Sie sicherstellen, dass der Webserver den richtigen  
> Benutzer-/Gruppenzugriff hat.  

Standard: `0777`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
StorageDirPermissions=0777
*/ ?>
```

## StorageFilePermissions

> Berechtigung für Speicherdateien; 0666 bedeutet, dass jeder  
> die Dateien lesen und schreiben kann, was als Sicherheitsrisiko gelten kann.  
> Besser ist 0660, wobei Sie sicherstellen, dass der Webserver den richtigen  
> Benutzer-/Gruppenzugriff hat.  

Standard: `0666`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
StorageFilePermissions=0666
*/ ?>
```

## LogFilePermissions

> Berechtigung für Logdateien; 0666 bedeutet, dass jeder  
> die Dateien lesen und schreiben kann, was als Sicherheitsrisiko gelten kann.  
> Besser ist 0660, wobei Sie sicherstellen, dass der Webserver den richtigen  
> Benutzer-/Gruppenzugriff hat.  

Standard: `0666`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
LogFilePermissions=0666
*/ ?>
```

## LogFileMaxSize

> Die Größe in Bytes, bei der eine Logdatei rotiert wird (die Debug-Logs error.log,  
> warning.log, deprecated.log ... und die mit eZLog geschriebenen Logs, etwa  
> storage.log und die Cronjob-Logs): Sie wird in .1 umbenannt, ältere rücken weiter.  
> Die Konstanten EZPUBLISH\_LOG\_MAX\_FILE\_SIZE und CUSTOM\_LOG\_MAX\_FILE\_SIZE in  
> config.php haben Vorrang. 2097152 sind 2 MB (früher 200 KB).  

Standard: `2097152`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
LogFileMaxSize=2097152
*/ ?>
```

## LogFileRotateCount

> Wie viele rotierte Dateien jedes Logs behalten werden (.1 bis .3). 0 schaltet die  
> Rotation ab. Die Konstanten EZPUBLISH\_LOG\_ROTATE\_FILES und  
> CUSTOM\_LOG\_ROTATE\_FILES in config.php haben Vorrang.  

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
LogFileRotateCount=3
*/ ?>
```

## LogFileCompress

> enabled: Die rotierten Dateien ab .2 werden zu .2.gz, .3.gz ... komprimiert  
> (.1 nie: Eine Anfrage, die das Log vor der Rotation geöffnet hat, kann noch  
> darin schreiben). Der Log-Viewer liest auch komprimierte Rotationen.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
LogFileCompress=disabled
*/ ?>
```

## LogFileMaxAge

> Das Alter in Tagen, nach dem "./console exp:logs --prune" (und die Bereinigungsaktion des Log-  
> Viewers) eine rotierte Datei entfernt, egal welche Nummer sie hat. 0: keine  
> Altersgrenze, nur LogFileRotateCount zählt. Eine Rotation entfernt nie nach Alter.  
> doc/eng-us/features/6.0/log-rotation.md  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
LogFileMaxAge=0
*/ ?>
```

## DirDepth

> Verzeichnistiefe für das Speichern von Dateien; das bedeutet, dass aus den ersten n Zeichen  
> Verzeichnisse angelegt werden, damit nicht zu  
> viele Dateien in einem Verzeichnis liegen. Zum Beispiel wird eine Datei namens  
> newfile.png in n/e/w/newfile.png abgelegt.  

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
DirDepth=3
*/ ?>
```

## VarDir

> Das Wurzelverzeichnis, in dem alle Logdateien, Cache-Dateien  
> und anderen gespeicherten Dateien angelegt werden.  
> Nur Verzeichnisse relativ zum Wurzelverzeichnis von Exponential werden unterstützt. Absolute Verzeichnisse werden NICHT UNTERSTÜTZT  

Standard: `var`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
VarDir=var
*/ ?>
```

## CacheDir

> Der Name des Cache-Verzeichnisses, als Unterverzeichnis von VarDir  
> Ein absolutes Verzeichnis wird akzeptiert, funktioniert aber mit manchen Funktionen nicht und wird NICHT UNTERSTÜTZT:  
> Die Caches werden dort geschrieben, aber die öffentlichen Caches darin (gepackte Skripte und  
> Stylesheets, Text als Bild) liefern weder der Webserver noch Velocity aus.  
> Um den Cache auf einem anderen Dateisystem zu halten, hängen Sie dieses unter VarDir/CacheDir ein oder  
> verwenden Sie CacheVarDir unten mit einem Verzeichnis innerhalb der Installation.  

Standard: `cache`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
CacheDir=cache
*/ ?>
```

## LogDir

> Der Name des Log-Verzeichnisses, ein Unterverzeichnis von VarDir. Ein absoluter Pfad legt die  
> Logs der Website auf ein anderes Dateisystem (Multisite-Hosting); die Logs werden  
> nie ausgeliefert, daher wird dies anders als bei CacheDir unterstützt.  

Standard: `log`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
LogDir=log
*/ ?>
```

## UseGlobalLogDir

> Wohin die Debug-Logs (error.log, warning.log, ...) und die Standard-Logs von  
> eZLog gehen. enabled: var/log für jede Website, wie bisher. disabled: das Log-  
> verzeichnis der Website (VarDir/LogDir), sobald ihr Siteaccess  
> bekannt ist; Meldungen, die vorher geschrieben werden, gehen weiterhin nach var/log.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
UseGlobalLogDir=enabled
*/ ?>
```

## LogVarDir

> Ein eigener Baum für die Logs oder die Caches jeder Website einer Multisite-  
> Installation, einmal für alle gesetzt: Das erste Verzeichnis von VarDir wird  
> ersetzt. Mit LogVarDir=var\_log protokolliert eine Website mit VarDir=var/example nach  
> var\_log/example/log, mit CacheVarDir=var\_cache liegt ihr Cache in  
> var\_cache/example/cache (ein Speicher-Dateisystem für alle Websites; setzen Sie dann  
> ExpiryDir). LogVarDir darf ein absoluter Pfad sein (Logs werden nie ausgeliefert).  
> CacheVarDir ist ein Verzeichnis innerhalb der Installation: Velocity liefert die  
> öffentlichen Caches darunter aus (es liest beim Start CacheVarDir jedes Siteaccess);  
> für Apache und nginx aktivieren Sie die Regel var\_cache in .htaccess\_root oder  
> der Serverkonfiguration. Ein absolutes CacheVarDir wird verwendet, aber seine öffentlichen  
> Caches werden nicht ausgeliefert, wie bei einem absoluten CacheDir. Leer: innerhalb von VarDir, wie  
> bisher. Audit-Einträge bleiben in VarDir (audit.ini \[AuditSettings\] LogDir).  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
LogVarDir=
*/ ?>
```

## CacheVarDir

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
CacheVarDir=
*/ ?>
```

## ExpiryDir

> Wo expiry.php gehalten wird: Ihre Zeitstempel entscheiden, ob die Caches und die  
> Bild-Aliase im Speicher noch gültig sind. Leer: im Cache-Verzeichnis,  
> wie bisher. Ein Verzeichnis innerhalb von VarDir (z. B. expiry) oder ein absoluter Pfad behält  
> sie, wenn das Cache-Verzeichnis auf einem Dateisystem liegt, das bei einem Neustart geleert wird  
> (ein unter var/<site>/cache eingehängtes Speicher-Dateisystem).  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
ExpiryDir=
*/ ?>
```

## INICacheDir

> Wo die INI-Dateien der Website gecacht werden. global: var/cache/ini, gemeinsam für  
> jede Website, wie bisher. site: das Cache-Verzeichnis der Website  
> (VarDir/CacheDir/ini), sodass das Leeren des INI-Caches einer Website einer Multisite-  
> Installation die anderen in Ruhe lässt. Die INI-Dateien, die gelesen werden, bevor der  
> Siteaccess bekannt ist, bleiben in var/cache/ini.  

Standard: `global`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
INICacheDir=global
*/ ?>
```

## AllowedDeletionDirs

> Verzeichnisse, aus denen eZDir löschen darf (siehe eZDir::recursiveDelete()).  
> Standardmäßig kann nur innerhalb des Wurzelverzeichnisses von Exponential gelöscht werden.  
> Aufgeführte Verzeichnisse werden als erlaubte Verzeichnispräfixe hinzugefügt.  
> Die Werte müssen absolute Pfade sein; einer, der nicht existiert, erlaubt nichts.  
> Innerhalb der Cache-, Log-, Ablauf- und INI-Cache-Verzeichnisse der Website und im  
> Cache-Verzeichnis selbst ist es immer erlaubt, auch wenn CacheDir, LogDir,  
> CacheVarDir, LogVarDir oder ExpiryDir sie außerhalb der Wurzel legen (nie eines,  
> das die Installation enthält).  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[FileSettings]
# ein leeres "AllowedDeletionDirs[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowedDeletionDirs[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
