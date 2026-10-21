---
title: package.ini [UploadSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [UploadSettings] in package.ini: MaxArchiveSize, MaxUnpackedSize, MaxEntries, AllowedSuffixes"
keywords: Einstellung, package.ini, UploadSettings, INI, setting, MaxArchiveSize, MaxUnpackedSize, MaxEntries, AllowedSuffixes
type: generated
generated: exp:help:examples
---
# package.ini [UploadSettings]

Der Block `[UploadSettings]` von `settings/package.ini`, mit 4 Einstellung(en).

> Die Grenzen eines Paketarchivs, das über package/upload, den Setup-Assistenten oder ezpm import hochgeladen wird  
> (eZPackageUploadInspector). Ein Archiv wird außerdem abgelehnt, egal was diese sagen, wenn ein Eintrag aus dem  
> Paket hinauszeigt ("..", ein absoluter Pfad), ein Link oder ein Gerät ist oder seine package.xml keine wohlgeformte Definition ist.  
> Anleitung: doc/eng-us/guides/packages.md  

Bearbeiten Sie `settings/package.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/package.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/package.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/package.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MaxArchiveSize

> Das größte akzeptierte Archiv, in Bytes (256 MiB). Zuerst gelten upload\_max\_filesize und post\_max\_size von PHP.  

Standard: `268435456`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[UploadSettings]
MaxArchiveSize=268435456
*/ ?>
```

## MaxUnpackedSize

> Wie viel seine Einträge entpackt höchstens ergeben dürfen, in Bytes (2 GiB)  

Standard: `2147483648`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[UploadSettings]
MaxUnpackedSize=2147483648
*/ ?>
```

## MaxEntries

> Die meisten Einträge (Dateien und Verzeichnisse), die ein Archiv haben darf  

Standard: `50000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[UploadSettings]
MaxEntries=50000
*/ ?>
```

## AllowedSuffixes

> Die Endungen, die der Name einer hochgeladenen Datei haben darf  

Standard: eine Liste mit 3: `ezpkg`, `tar.gz`, `tgz`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[UploadSettings]
# ein leeres "AllowedSuffixes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowedSuffixes[]
AllowedSuffixes[]=ezpkg
AllowedSuffixes[]=tar.gz
AllowedSuffixes[]=tgz
*/ ?>
```

## Siehe auch

- [Alle Blöcke von package.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/package.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
