---
title: upload.ini [CreateSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CreateSettings] in upload.ini: MimeClassMap, MimeUploadHandlerMap, DefaultClass"
keywords: Einstellung, upload.ini, CreateSettings, INI, setting, MimeClassMap, MimeUploadHandlerMap, DefaultClass
type: generated
generated: exp:help:examples
---
# upload.ini [CreateSettings]

Der Block `[CreateSettings]` von `settings/upload.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/upload.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/upload.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/upload.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/upload.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MimeClassMap

> Maps from a mime-type or mime-group to a class identifier  

Standard: ein Hash mit 7: `image=image`, `video/quicktime=quicktime`, `video/x-msvideo=windows_media`, `video/vnd.rn-realvideo=real_video`, `application/vnd.rn-realmedia=real_video`, `application/x-shockwave-flash=flash`, `video/mp4=video`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upload.ini.append.php
[CreateSettings]
# ein leeres "MimeClassMap[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MimeClassMap[]
MimeClassMap[image]=image
MimeClassMap[video/quicktime]=quicktime
MimeClassMap[video/x-msvideo]=windows_media
MimeClassMap[video/vnd.rn-realvideo]=real_video
MimeClassMap[application/vnd.rn-realmedia]=real_video
MimeClassMap[application/x-shockwave-flash]=flash
MimeClassMap[video/mp4]=video
*/ ?>
```

## MimeUploadHandlerMap

> Maps from MIME-Type to the name of an upload handler  
> If the handler is found for the given MIME-Type it will be  
> used instead of the normal attribute based upload.  
> The syntax is  
> MimeUploadHandlerMap\[<mime-type>\]=<handler\_identifier>  
> The <handler\_identifier> will be used for the class name as well  
> as the filename of the PHP file.  
> The <mime\_type> can either be specified by the full MIME-Type like  
> MimeUploadHandlerMap\[image/jpeg\]  
> with just the group  
> MimeUploadHandlerMap\[image\]  
> By using just the group the handler can take care of a whole set of mime-types  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upload.ini.append.php
[CreateSettings]
# ein leeres "MimeUploadHandlerMap[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MimeUploadHandlerMap[]
*/ ?>
```

## DefaultClass

> Example of OpenOffice.org handling  
> MimeUploadHandlerMap\[application/vnd.sun.xml.writer\]=ezopenofficeupload  
> The system will then look for ezopenofficeupload.php  
> The class for all files that are not recognized.  
> The default is to use the file class  

Standard: `file`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upload.ini.append.php
[CreateSettings]
DefaultClass=file
*/ ?>
```

## Siehe auch

- [Alle Blöcke von upload.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/upload.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
