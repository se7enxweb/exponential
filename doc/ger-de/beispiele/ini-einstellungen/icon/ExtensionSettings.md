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

> Hier können Sie Größen überschreiben oder neue hinzufügen; Sie müssen aber  
> sicherstellen, dass alle verwendeten Themes diese Größen definieren.  
> Jede Größe bezieht sich auf den Namen des Unterverzeichnisses  
> des Symbol-Themes. Enthält der Name zwei Zahlen mit einem  
> x dazwischen, gelten sie als Breite und Höhe  
> des Symbols, sonst wird keine Größe angegeben  
>
> Wenn Sie ein Theme anlegen, muss diese Variable vorhanden sein  
> Erlaubte Schreibweisen sind:  
> Hier ist der Unterpfad die Einstellung für Breite und Höhe  
> Sizes\[<identifier>\]=<width>x<height>  
> z. B.  
> Sizes\[normal\]=32x32  
>
> Hier wird der Unterpfad zum Holen des Symbols verwendet,  
> Breite und Höhe für die Größe  
> Sizes\[<identifier>\]=<width>x<height>;<subpath>  
> z. B.  
> Sizes\[normal\]=32x32;normal-icons  
>
> Sizes\[\]  
> Sizes\[normal\]=32x32  
> Sizes\[small\]=16x16  
> Sizes\[large\]=48x48  
> \[MimeIcons\]  
> Das Theme für MIME-Symbole, überschreibt den Standard  
> Theme=crystal  
> Die Größe für MIME-Symbole, überschreibt den Standard  
> Size=normal  
> Zuordnung eines MIME-Typs zu seiner Symboldatei, der ganze Dateiname  
> muss angegeben werden  
> Nur die Gruppe des MIME-Typs anzugeben ist auch möglich  
>
> Jedes Theme definiert diese Zuordnungsvariable  
> MimeMap\[\]  
> \[ClassIcons\]  
> Das Theme für Klassensymbole, überschreibt den Standard  
> Theme=crystal  
> Die Größe für Klassensymbole, überschreibt den Standard  
> Size=normal  
> Zuordnung eines Klassenbezeichners zu seiner Symboldatei,  
> der ganze Dateiname muss angegeben werden  
>
> Jedes Theme definiert diese Zuordnungsvariable  
> ClassMap\[\]  
> \[ClassGroupIcons\]  
> Das Theme für Symbole von Klassengruppen, überschreibt den Standard  
> Theme=crystal  
> Die Größe für Symbole von Klassengruppen, überschreibt den Standard  
> Size=normal  
> Zuordnung eines Bezeichners einer Klassengruppe zu seiner Symboldatei,  
> der ganze Dateiname muss angegeben werden  
>
> Jedes Theme definiert diese Zuordnungsvariable  
> ClassGroupMap\[\]  
> \[Icons\]  
> Das Theme für Standardsymbole, überschreibt den Standard  
> Theme=crystal  
> Die Größe für Standardsymbole, überschreibt den Standard  
> Size=normal  
> Zuordnung eines Symbolbezeichners zu seiner Symboldatei,  
> der ganze Dateiname muss angegeben werden  
>
> Jedes Theme definiert diese Zuordnungsvariable  
> IconMap\[\]  
> \[ActionIcons\]  
> Das Theme für Aktionssymbole, überschreibt den Standard  
> Theme=ezadmin  
> Die Größe für Standardsymbole, überschreibt den Standard  
> Size=normal  
> Zuordnung eines Symbolbezeichners zu seiner Symboldatei,  
> der ganze Dateiname muss angegeben werden  
>
> Jedes Theme definiert diese Zuordnungsvariable  
> ActionMap\[\]  

Bearbeiten Sie `settings/icon.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/icon.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/icon.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/icon.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## IconExtensions

> Eine Liste der Erweiterungen, die Symbol-Themes enthalten (unter extension/<name>/icons).  
> Ist eine Erweiterung hier aufgeführt, durchsuchen die Symbol-Operatoren des Kernels sie,  
> bevor sie auf das Standard-Repository share/icons zurückfallen.  

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
