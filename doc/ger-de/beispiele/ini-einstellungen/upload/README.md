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

> Exponential-Konfigurationsdatei für das Durchsuchen von Inhalten.  
>
> HINWEIS: Es wird nicht empfohlen, diese Datei direkt zu bearbeiten; legen Sie stattdessen  
>       eine Datei in override an, um die Werte zu setzen,  
>       die Ihre Website braucht. Legen Sie entweder  
>       eine Datei settings/override/upload.ini.append oder  
>       settings/override/upload.ini.append.php an, für mehr Sicherheit  
>       im Modus ohne virtuellen Host (die .php-Datei ist eventuell schon vorhanden  
>       und kann dafür verwendet werden).  
> Einstellungen der Upload-Seite.  
> Für Startknoten und Knoten der obersten Ebene können Text-Aliase verwendet werden  
> Sie können die folgenden Aliase verwenden:  
>  content - steht für den Wurzelknoten der Inhalte, definiert in NodeSettings/RootNode in content.ini, meist 2  
>  users   - steht für den Wurzelknoten der Benutzer, definiert in NodeSettings/UserRootNode in content.ini, meist 5  
> Weitere Aliase sind möglich, wenn Sie sie unten in AliasList definieren  

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
