---
title: upload.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von upload.ini.
keywords: Beispiele, Referenz, upload.ini, examples, reference
type: generated
generated: exp:help:examples
---
# upload.ini

Die ausgelieferten Einstellungen von `settings/upload.ini`: 12 Blöcke, 32 Einstellungen.

> Exponential configuration file for content browsing.  
>
> NOTE: It is not recommended to edit this files directly, instead  
>       a file in override should be created for setting the  
>       values that is required for your site. Either create  
>       a file called settings/override/upload.ini.append or  
>       settings/override/upload.ini.append.php for more security  
>       in non-virtualhost modes (the .php file may already be present  
>       and can be used for this purpose).  
> Settings for the upload page.  
> For start nodes and top level nodes it is possible to use text aliases  
> You can use the following aliases:  
>  content - Represents the content root node defined NodeSettings/RootNode in content.ini, usually 2  
>  users   - Represents the users root node defined NodeSettings/UserRootNode in content.ini, usually 5  
> Other aliases are also possible by defining them in AliasList below  

| Block | Einstellungen |
|---|---|
| [UploadSettings](UploadSettings.md) | 2 |
| [LocationSettings](LocationSettings.md) | 4 |
| [CreateSettings](CreateSettings.md) | 3 |
| [image_ClassSettings](image_ClassSettings.md) | 3 |
| [file_ClassSettings](file_ClassSettings.md) | 3 |
| [quicktime_ClassSettings](quicktime_ClassSettings.md) | 3 |
| [real_video_ClassSettings](real_video_ClassSettings.md) | 3 |
| [windows_media_ClassSettings](windows_media_ClassSettings.md) | 3 |
| [flash_ClassSettings](flash_ClassSettings.md) | 3 |
| [video_ClassSettings](video_ClassSettings.md) | 3 |
| [RelatedObjectUpload](RelatedObjectUpload.md) | 1 |
| [BugFileUpload](BugFileUpload.md) | 1 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/upload.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
