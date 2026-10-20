---
title: error.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von error.ini.
keywords: Beispiele, Referenz, error.ini, examples, reference
type: generated
generated: exp:help:examples
---
# error.ini

Die ausgelieferten Einstellungen von `settings/error.ini`: 7 Blöcke, 22 Einstellungen.

> Exponential configuration file for error handling  
>
> NOTE: It is not recommended to edit these files directly, instead  
>       a file in override should be created for setting the  
>       values that is required for your site. Either create  
>       a file called settings/override/error.ini.append or  
>       settings/override/error.ini.append.php for more security  
>       in non-virtualhost modes (the .php file may already be present  
>       and can be used for this purpose).  
> The following error codes for kernel is currently defined  
> 1  - Access denied  
> 2  - Object not found  
> 3  - Object not available  
> 4  - Object has moved  
> 5  - Language not found  
> 6  - Form token refused (a POST without, or with a wrong, form token);  
>      always shown on the spot as HTTP 403 (design:error/kernel/6.tpl),  
>      ErrorHandler/RedirectURL/RerunURL do not apply to it  
> 20 - Module not found  
> 21 - Module view not found  
> 22 - Module or view disabled  
> 50 - No DB connection  

| Block | Einstellungen |
|---|---|
| [ErrorSettings](ErrorSettings.md) | 4 |
| [ErrorSettings-kernel](ErrorSettings-kernel.md) | 5 |
| [ErrorSettings-shop](ErrorSettings-shop.md) | 5 |
| [HTTPError-404](HTTPError-404.md) | 2 |
| [HTTPError-301](HTTPError-301.md) | 2 |
| [HTTPError-401](HTTPError-401.md) | 2 |
| [HTTPError-403](HTTPError-403.md) | 2 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/error.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
