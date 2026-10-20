---
title: site.ini [DebugSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DebugSettings] in site.ini: DebugOutput, AlwaysLog, DeprecatedLog, ScriptDebugOutput, DebugByIP, DebugIPList, DebugByUser, DebugUserIDList ..."
keywords: Einstellung, site.ini, DebugSettings, INI, setting, DebugOutput, AlwaysLog, DeprecatedLog, ScriptDebugOutput, DebugByIP, DebugIPList, DebugByUser, DebugUserIDList, Debug, DebugRedirection, DisplayDebugWarnings, DebugLogOnly, DebugToolbar, DisplayIncludedFiles, QuickSettingsList, UsedTemplateEditUrl
type: generated
generated: exp:help:examples
---
# site.ini [DebugSettings]

Der Block `[DebugSettings]` von `settings/site.ini`, mit 16 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DebugOutput

> Master switch for debug, if disabled no debug will be shown  
> If enabled it's up to the DebugByIP and DebugIPList to decide what to do.  
> Use either enabled or disabled  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
DebugOutput=disabled
*/ ?>
```

## AlwaysLog

> Controls which debug types to always log.  
> This ensures logging even if debug output is off  
>
> Available debug types are:  
> error - For major errors, very important to log  
> warning - Smaller errors, important for development but not for live site  
> debug - Temporary debug, usually only important to see in the web browser  
> notice - General notices, enabling this can fill up the log files quickly  
> strict - Strict notices, suggests changes to your code which will ensure the best interoperability and forward compatibility  
> deprecated - PHP deprecations (with DeprecatedLog=enabled), each distinct message once per request  

Standard: eine Liste mit 1: `error`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
# ein leeres "AlwaysLog[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AlwaysLog[]
AlwaysLog[]=error
*/ ?>
```

## DeprecatedLog

> PHP deprecations (E\_DEPRECATED, E\_USER\_DEPRECATED, eZDebug::writeDeprecated()).  
> enabled: written to var/log/deprecated.log, each distinct message once per  
> request, and only counted in the debug output (the debug bar's summary and  
> the warning count); none is kept in memory or listed with the messages.  
> disabled: warnings in warning.log and the debug output, as before.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
DeprecatedLog=enabled
*/ ?>
```

## ScriptDebugOutput

> Debug switch for scripts, will only take place if DebugOutput is set to enabled  
> By default it is off to not give debug for scripts.  
> Use either enabled or disabled  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
ScriptDebugOutput=disabled
*/ ?>
```

## DebugByIP

> Whether debug is set per IP address. If set to enabled it will user  
> DebugIPList for valid ips otherwise all ips get debug.  
> Use either enabled or disabled  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
DebugByIP=disabled
*/ ?>
```

## DebugIPList

> A array with IP addresses or networks which will get debug information  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
# ein leeres "DebugIPList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DebugIPList[]
*/ ?>
```

## DebugByUser

> Examples  
> DebugIPList\[\]=1.2.3.4  
> DebugIPList\[\]=192.0.0.42  
> DebugIPList\[\]=192.0.0.0/27  
> Whether debug is set per UserID. If set to enabled only users with  
> IDs (in DebugUserIDList) can get debug.  
> Use either enabled or disabled  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
DebugByUser=disabled
*/ ?>
```

## DebugUserIDList

> An array with UserIDs which will get debug information  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
# ein leeres "DebugUserIDList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DebugUserIDList[]
*/ ?>
```

## Debug

> Examples  
> DebugUserIDList\[\]=14  
> Controls how debug is displayed, inline means that the debug is displayed in  
> page itself while popup will create a new popup window with the debug.  
> Popup window might not work in all browsers.  
> Use either disabled, inline or popup  
> If you use popup you must make sure that a rewrite rules exists for .html files  
> or that your site is running in non-virtual host mode. Also some browsers  
> have problems with this feature.  

Standard: `inline`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
Debug=inline
*/ ?>
```

## DebugRedirection

> Controls if redirects should be debuggable, set to enable to get a redirect  
> page with debug info.  
> This is useful for development while production sites should have it off  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
DebugRedirection=disabled
*/ ?>
```

## DisplayDebugWarnings

> Whether debug warnings/errors should be displayed on the page or not.  
> If not they will appear in debug log only.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
DisplayDebugWarnings=disabled
*/ ?>
```

## DebugLogOnly

> Whether to skip showing debug strings in the debug output.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
DebugLogOnly=disabled
*/ ?>
```

## DebugToolbar

> Developer toolbar with clear cache and quick settings features  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
DebugToolbar=enabled
*/ ?>
```

## DisplayIncludedFiles

> Display list of php included files used to render the current page  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
DisplayIncludedFiles=disabled
*/ ?>
```

## QuickSettingsList

> Quick settings list for the developer toolbar  
> You can use any enabled/disabled setting  

Standard: eine Liste mit 6: `DebugSettings;DebugOutput;site.ini;Debug output`, `DebugSettings;DebugRedirection;site.ini;Debug redirection`, `TemplateSettings;Debug;site.ini;Template debug`, `TemplateSettings;ShowXHTMLCode;site.ini;Inline template debug`, `TemplateSettings;ShowUsedTemplates;site.ini;List of used templates`, `DatabaseSettings;SQLOutput;site.ini;SQL debug output`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
# ein leeres "QuickSettingsList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
QuickSettingsList[]
QuickSettingsList[]=DebugSettings;DebugOutput;site.ini;Debug output
QuickSettingsList[]=DebugSettings;DebugRedirection;site.ini;Debug redirection
QuickSettingsList[]=TemplateSettings;Debug;site.ini;Template debug
QuickSettingsList[]=TemplateSettings;ShowXHTMLCode;site.ini;Inline template debug
QuickSettingsList[]=TemplateSettings;ShowUsedTemplates;site.ini;List of used templates
QuickSettingsList[]=DatabaseSettings;SQLOutput;site.ini;SQL debug output
*/ ?>
```

## UsedTemplateEditUrl

> The link of each template's "Edit" icon in the debug output's list of used  
> templates (TemplateSettings ShowUsedTemplates), as a sprintf() format:  
>  %1$s the address of visual/templateedit in the current siteaccess followed  
>       by the template file, e.g. /visual/templateedit/design/standard/templates/page.tpl  
>  %2$s the current siteaccess  
>  %3$s the template file, for example design/standard/templates/page.tpl  
> Empty (the default): %1$s/(siteAccess)/%2$s, the link the list always had.  
> Example, the editor of the admin siteaccess from a public siteaccess:  
> UsedTemplateEditUrl=/admin/visual/templateedit/%3$s/(siteAccess)/%2$s  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[DebugSettings]
UsedTemplateEditUrl=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
