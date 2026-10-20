---
title: logviewer.ini [ActionSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ActionSettings] in logviewer.ini: Download, Rotate, Truncate, DeleteArchives, Compress, MaxDownloadBytes, MaxBundleBytes"
keywords: Einstellung, logviewer.ini, ActionSettings, INI, setting, Download, Rotate, Truncate, DeleteArchives, Compress, MaxDownloadBytes, MaxBundleBytes
type: generated
generated: exp:help:examples
---
# logviewer.ini [ActionSettings]

Der Block `[ActionSettings]` von `settings/logviewer.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/logviewer.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/logviewer.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/logviewer.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/logviewer.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Download

> The actions offered next to viewing. Download needs setup/logs, the others setup/managelogs; each one is  
> audited (system.log.download, system.log.rotate, system.log.truncate, system.log.delete).  
> enabled | disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[ActionSettings]
Download=enabled
*/ ?>
```

## Rotate

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[ActionSettings]
Rotate=disabled
*/ ?>
```

## Truncate

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[ActionSettings]
Truncate=disabled
*/ ?>
```

## DeleteArchives

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[ActionSettings]
DeleteArchives=disabled
*/ ?>
```

## Compress

> Compress archived rotations (.N to .N.gz) on Setup > Log files > Manage logs (setup/logsmanage). Audited as  
> system.log.rotate with after.action compress.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[ActionSettings]
Compress=disabled
*/ ?>
```

## MaxDownloadBytes

> The largest range one download sends, in bytes (compressed with gzip on the way).  

Standard: `52428800`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[ActionSettings]
MaxDownloadBytes=52428800
*/ ?>
```

## MaxBundleBytes

> "Download all logs" (Setup > Manage logs): the most bytes of logs (and archives) one archive may hold, counted before  
> it is built; over it the page says so. The archive is built in var/tmp/logbundle and removed once it is sent.  

Standard: `268435456`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[ActionSettings]
MaxBundleBytes=268435456
*/ ?>
```

## Siehe auch

- [Alle Blöcke von logviewer.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/logviewer.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
