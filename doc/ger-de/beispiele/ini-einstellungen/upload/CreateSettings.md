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

> Ordnet einem MIME-Typ oder einer MIME-Gruppe einen Klassenbezeichner zu  

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

> Ordnet einem MIME-Typ den Namen eines Upload-Handlers zu  
> Wird für den angegebenen MIME-Typ ein Handler gefunden, wird er  
> statt des normalen attributbasierten Uploads verwendet.  
> Die Syntax ist  
> MimeUploadHandlerMap\[<mime-type>\]=<handler\_identifier>  
> Der <handler\_identifier> wird sowohl als Klassenname als auch  
> als Dateiname der PHP-Datei verwendet.  
> Der <mime\_type> lässt sich entweder mit dem vollen MIME-Typ angeben, etwa  
> MimeUploadHandlerMap\[image/jpeg\]  
> oder nur mit der Gruppe  
> MimeUploadHandlerMap\[image\]  
> Mit nur der Gruppe kann der Handler eine ganze Reihe von MIME-Typen übernehmen  

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

> Beispiel für die Behandlung von OpenOffice.org  
> MimeUploadHandlerMap\[application/vnd.sun.xml.writer\]=ezopenofficeupload  
> Das System sucht dann nach ezopenofficeupload.php  
> Die Klasse für alle Dateien, die nicht erkannt werden.  
> Standard ist die Klasse file  

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
