---
title: i18n.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von i18n.ini.
keywords: Beispiele, Referenz, i18n.ini, examples, reference
type: generated
generated: exp:help:examples
---
# i18n.ini

Die ausgelieferten Einstellungen von `settings/i18n.ini`: 2 Blöcke, 9 Einstellungen.

> Exponential configuration file for internationalization (i18n).  
> Used for settings defaults for internal encoding and codepage conversion.  
>
> NOTE: It is not recommended to edit this files directly, instead  
>       a file in override should be created for setting the  
>       values that is required for your site. Either create  
>       a file called settings/override/i18n.ini.append or  
>       settings/override/i18n.ini.append.php for more security  
>       in non-virtualhost modes (the .php file may already be present  
>       and can be used for this purpose).  
> # This file should not change it's default encoding which is iso-8859-1.  
>
> Charset defines the internal charset for the site,  
>  use UTF-8 for multilingual sites or a specific charset for monolingual sites  
> Set HTTPCharset to something if you know that the internal charset is not  
> supported by browser or has a wrong name(for instance UTF8 is a wrong unicode name  
>                                          for browsers while UTF-8 is correct)  
> MBStringExtension defines whether to use the mbstring extension in PHP,  
>  the extension will only be used if it's available and this variable is set to enabled  

| Block | Einstellungen |
|---|---|
| [CharacterSettings](CharacterSettings.md) | 3 |
| [TranslationSettings](TranslationSettings.md) | 6 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/i18n.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
