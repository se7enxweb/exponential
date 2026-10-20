---
title: ezjscore.ini [ezjscServer_ezjsc]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ezjscServer_ezjsc] in ezjscore.ini (ezjscore): Class"
keywords: Einstellung, ezjscore.ini, ezjscServer_ezjsc, INI, setting, Class
type: generated
generated: exp:help:examples
---
# ezjscore.ini [ezjscServer_ezjsc]

Der Block `[ezjscServer_ezjsc]` von `extension/ezjscore/settings/ezjscore.ini` (Erweiterung ezjscore), mit 1 Einstellung(en).

> FunctionList\[\]=ezjsckeyword  
> FunctionList\[\]=ezjscrating\_rate  
> Settings for setting up a server functions  
> These are also supported by ezjscPacker, the class used in ezcss and ezscript  
> Here is an example of setting up such a function:  
>
> \[ezjscServer\_<custom\_server>\]  
> # Optional, uses <custom\_server> as class name if not set  
> Class=MyCustomJsGeneratorClass  
> # Optional, defines if a template is to be executed instead of a php class function  
> # In this case request will go to /templates/<class>/<function>.tpl  
> TemplateFunction=true  
> # Optional, uses autoload system if not defined  
> File=extension/ezjscore/classes/mycustomjsgenerator.php  
> # Optional, List of \[ezjscServer\]FunctionList functions user needs to have access to, Default: none  
> Functions\[\]=ezjsc  
> # Optional, If pr function, then function name will be  appended to Function name like  
> # <FunctionList\_name>\_<funtion\_name>, warning will be thrown if not set in FunctionList\[\].  
> # Default: disabled  
> PermissionPrFunction=enabled  
> # Optional, whether Functions\[\] is checked. Not set: checked, and a refused call is answered  
> # with its error text (as before). disabled: not checked. enabled: checked, and a request whose  
> # calls are all refused is answered with 403 Forbidden.  
> RequirePolicy=enabled  
>
> Definition of use in template:  
> {ezscript('<custom\_server>::<funtion\_name>\[::arg1\[::arg2\]\]')}  

Bearbeiten Sie `extension/ezjscore/settings/ezjscore.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezjscore.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezjscore.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezjscore.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

> Url to test this server function(return current time):  
> <root>/ezjscore/call/ezjsc::time  

Standard: `ezjscServerFunctionsJs`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[ezjscServer_ezjsc]
Class=ezjscServerFunctionsJs
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezjscore.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/settings/ezjscore.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
