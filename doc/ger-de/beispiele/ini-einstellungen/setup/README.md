---
title: setup.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von setup.ini.
keywords: Beispiele, Referenz, setup.ini, examples, reference
type: generated
generated: exp:help:examples
---
# setup.ini

Die ausgelieferten Einstellungen von `settings/setup.ini`: 24 Blöcke, 55 Einstellungen.

> Exponential-Konfigurationsdatei für eZ Setup  
>
> HINWEIS: Es wird nicht empfohlen, diese Datei direkt zu bearbeiten; legen Sie stattdessen  
>       eine Datei in override an, um die Werte zu setzen,  
>       die Ihre Website braucht. Legen Sie entweder  
>       eine Datei settings/override/setup.ini.append oder  
>       settings/override/setup.ini.append.php an, für mehr Sicherheit  
>       im Modus ohne virtuellen Host (die .php-Datei ist eventuell schon vorhanden  
>       und kann dafür verwendet werden).  
>
> Diese Datei enthält Einstellungen für alle Tests, die  
> das Setup ausführt, und einige Einstellungen für das  
> Setup-System selbst.  

| Block | Einstellungen |
|---|---|
| [RegistrationSettings](RegistrationSettings.md) | 1 |
| [phpversion](phpversion.md) | 3 |
| [ezcversion](ezcversion.md) | 3 |
| [php_session](php_session.md) | 2 |
| [memory_limit](memory_limit.md) | 1 |
| [execution_time](execution_time.md) | 1 |
| [directory_permissions](directory_permissions.md) | 1 |
| [settings_permission](settings_permission.md) | 1 |
| [open_basedir](open_basedir.md) | 0 |
| [file_upload](file_upload.md) | 1 |
| [database_extensions](database_extensions.md) | 2 |
| [database_all_extensions](database_all_extensions.md) | 2 |
| [image_conversion](image_conversion.md) | 2 |
| [texttoimage_functions](texttoimage_functions.md) | 2 |
| [zlib_extension](zlib_extension.md) | 2 |
| [dom_extension](dom_extension.md) | 2 |
| [curl_extension](curl_extension.md) | 2 |
| [iconv_extension](iconv_extension.md) | 2 |
| [xsl_extension](xsl_extension.md) | 2 |
| [intl_extension](intl_extension.md) | 2 |
| [imagemagick_program](imagemagick_program.md) | 6 |
| [DatabaseSettings](DatabaseSettings.md) | 11 |
| [DemoSettings](DemoSettings.md) | 1 |
| [SiteTemplates](SiteTemplates.md) | 3 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/setup.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
