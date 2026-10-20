---
title: layout.ini
audience: developer, admin
category: examples
order: 10
summary: Die Blöcke und Einstellungen von layout.ini.
keywords: Beispiele, Referenz, layout.ini, examples, reference
type: generated
generated: exp:help:examples
---
# layout.ini

Die ausgelieferten Einstellungen von `settings/layout.ini`: 3 Blöcke, 3 Einstellungen.

> Exponential configuration file for layout module  
>
> NOTE: It is not recommended to edit this files directly, instead  
>       a file in override should be created for setting the  
>       values that is required for your site. Either create  
>       a file called settings/override/layout.ini.append or  
>       settings/override/layout.ini.append.php for more security  
>       in non-virtualhost modes (the .php file may already be present  
>       and can be used for this purpose).  
>
> Consists of groups which are the layout name  
> A layout group can have the followin variables  
> - ContentType   - Sets 'Content-Type' to use for output on a specific layout.  
>                   example: ContentType=text/xml  
> - PageLayout    - Sets the pagelayout to use for a specific layout.  
> - UseAccessPass - Include or exclude the layout path in links.  
>                   example : UseAccessPass=false  
> - UseFullUrl    - Turns usage of full urls on links ON or OFF.  
>                   example : UseFullUrl=true  

| Block | Einstellungen |
|---|---|
| [fullscreen](fullscreen.md) | 1 |
| [popup](popup.md) | 1 |
| [print](print.md) | 1 |

- [Alle Beispiele](../../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/layout.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
