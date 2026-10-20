---
title: override.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von override.ini.
keywords: Beispiele, Referenz, override.ini, examples, reference
type: generated
generated: exp:help:examples
---
# override.ini

Die ausgelieferten Einstellungen von `settings/override.ini`: 0 Blöcke, 0 Einstellungen.

> Exponential configuration file for template overrides.  
>
> NOTE: It is not recommended to edit this files directly, instead  
>       a file in override should be created for setting the  
>       values that is required for your site. Either create  
>       a file called settings/override/override.ini.append or  
>       settings/override/override.ini.append.php for more security  
>       in non-virtualhost modes (the .php file may already be present  
>       and can be used for this purpose).  
> Template Override configuration file  
> Place your own override configuration file  
> in settings/siteaccess/myaccess/override.ini.append  
>
> This file should only be used for overrides that are  
> common for all siteaccesses  
> Example syntax  
> \[article\]  
> Source=node/view/full.tpl  
> MatchFile=article.tpl  
> Subdir=templates  
> Match\[section\]=1  
> Match\[class\]=2  

| Block | Einstellungen |
|---|---|

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/override.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
