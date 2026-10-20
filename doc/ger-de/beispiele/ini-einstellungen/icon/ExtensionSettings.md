---
title: icon.ini [ExtensionSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ExtensionSettings] in icon.ini: IconExtensions"
keywords: Einstellung, icon.ini, ExtensionSettings, INI, setting, IconExtensions
type: generated
generated: exp:help:examples
---
# icon.ini [ExtensionSettings]

Der Block `[ExtensionSettings]` von `settings/icon.ini`, mit 1 Einstellung(en).

> Here you can override or add new sizes, allthough you must make  
> sure all used themes have these sizes defined.  
> Each size refers to the name of the subdirectory  
> of the icon theme. If the name contains two numbers with an  
> x in between it will be considered to be the width and height  
> of the icon, if not no size is will be given  
>
> If you create a theme this variable must be present  
> Allowed syntaxes are:  
> Here the subpath will be the width and height setting  
> Sizes\[<identifier>\]=<width>x<height>  
> e.g.  
> Sizes\[normal\]=32x32  
>
> Here the subpath will be used for fetching the icon  
> while width and height are used for size  
> Sizes\[<identifier>\]=<width>x<height>;<subpath>  
> e.g.  
> Sizes\[normal\]=32x32;normal-icons  
>
> Sizes\[\]  
> Sizes\[normal\]=32x32  
> Sizes\[small\]=16x16  
> Sizes\[large\]=48x48  
> \[MimeIcons\]  
> The theme for mime-icons, overrides default  
> Theme=crystal  
> The size for mime-icons, overrides default  
> Size=normal  
> Mapping from a mimetype to it's icon file, the whole filename  
> must be included  
> Specifying just the group of the mimetype is also possible  
>
> Each theme will define this mapping variable  
> MimeMap\[\]  
> \[ClassIcons\]  
> The theme for class-icons, overrides default  
> Theme=crystal  
> The size for class-icons, overrides default  
> Size=normal  
> Mapping from a class identifier to it's icon file,  
> the whole filename must be included  
>
> Each theme will define this mapping variable  
> ClassMap\[\]  
> \[ClassGroupIcons\]  
> The theme for classgroup-icons, overrides default  
> Theme=crystal  
> The size for classgroup-icons, overrides default  
> Size=normal  
> Mapping from a class group identifier to it's icon file,  
> the whole filename must be included  
>
> Each theme will define this mapping variable  
> ClassGroupMap\[\]  
> \[Icons\]  
> The theme for standard icons, overrides default  
> Theme=crystal  
> The size for standard icons, overrides default  
> Size=normal  
> Mapping from a icon identifier to it's icon file,  
> the whole filename must be included  
>
> Each theme will define this mapping variable  
> IconMap\[\]  
> \[ActionIcons\]  
> The theme for action icons, overrides default  
> Theme=ezadmin  
> The size for standard icons, overrides default  
> Size=normal  
> Mapping from a icon identifier to it's icon file,  
> the whole filename must be included  
>
> Each theme will define this mapping variable  
> ActionMap\[\]  

Bearbeiten Sie `settings/icon.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/icon.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/icon.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/icon.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## IconExtensions

> A list of extensions which contain icon themes (under extension/<name>/icons).  
> When an extension is listed here the kernel icon operators will search it  
> before falling back to the default share/icons repository.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/icon.ini.append.php
[ExtensionSettings]
# ein leeres "IconExtensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
IconExtensions[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von icon.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/icon.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
