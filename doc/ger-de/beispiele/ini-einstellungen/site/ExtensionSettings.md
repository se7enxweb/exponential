---
title: site.ini [ExtensionSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ExtensionSettings] in site.ini: ExtensionDirectory, AdditionalExtensionDirectories, ExtensionOrdering, ActiveExtensions, ActiveAccessExtensions"
keywords: Einstellung, site.ini, ExtensionSettings, INI, setting, ExtensionDirectory, AdditionalExtensionDirectories, ExtensionOrdering, ActiveExtensions, ActiveAccessExtensions
type: generated
generated: exp:help:examples
---
# site.ini [ExtensionSettings]

Der Block `[ExtensionSettings]` von `settings/site.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ExtensionDirectory

Standard: `extension`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ExtensionSettings]
ExtensionDirectory=extension
*/ ?>
```

## AdditionalExtensionDirectories

> Weitere Orte, an denen Erweiterungen liegen können, durchsucht nach ExtensionDirectory oben.  
> Eine zweite Wurzel trennt, was dieses Projekt geschrieben hat, von dem, was es von  
> anderswo übernommen hat, sodass sich der Eigentümer eines Pakets an der Verzeichnisstruktur ablesen lässt  
> statt an einer Liste, die jemand pflegen muss.  
>
> Eine Erweiterung gleichen Namens in einer späteren Wurzel verdeckt die davor. So  
> wird ein Paket geforkt, ohne es zu bearbeiten (hinüberkopieren und dort  
> ändern), und so wird eines auch versehentlich verdeckt.  
>
> Das Verzeichnis muss existieren, und der Autoload-Generator durchsucht jede konfigurierte  
> Wurzel; nach dem Hinzufügen einer also:  
>
>     php bin/php/ezpgenerateautoloads.php  
>
> Sonst nichts: Ein Paket zwischen Wurzeln zu verschieben ist eine Kopie, ohne Manifest zum  
> Bearbeiten und ohne composer dump-autoload.  
>
> Code sollte nie einen Pfad aus "extension/" und einem Namen zusammensetzen. Verwenden Sie  
> eZExtension::extensionPath() und eZExtension::expandedPathList(), die  
> über alle Wurzeln auflösen. eZExtension::filterExtensionRootDirectories() hat  
> das letzte Wort, für eine Wurzel, die zur Laufzeit ermittelt werden muss.  
>
> Siehe doc/eng-us/bc/6.0/AdditionalExtensionDirectories.md  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ExtensionSettings]
# ein leeres "AdditionalExtensionDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AdditionalExtensionDirectories[]
*/ ?>
```

## ExtensionOrdering

> AdditionalExtensionDirectories\[\]=extension\_src  
> Umsortieren der Erweiterungen verwenden, um Probleme zu vermeiden, weil ActiveExtensions und  
> ActiveAccessExtensions nicht korrekt definiert sind  
> Bekannte Probleme:  
>   - Zerstört Cluster-Einstellungen in Siteaccess und die Unterstützung von Erweiterungen  
>     Einzige bekannte Umgehung: diese Einstellung deaktivieren und von Hand sortieren  
>   - Lädt eine Erweiterung auch dann, wenn eine Erweiterung, von der sie abhängt, nicht geladen ist  
>   - Behebt keine Reihenfolgeprobleme zwischen den beiden Erweiterungseinstellungen  
>     ( ActiveExtensions und ActiveAccessExtensions )  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ExtensionSettings]
ExtensionOrdering=enabled
*/ ?>
```

## ActiveExtensions

> Eine Liste der aktiven Erweiterungen; fügen Sie neue hinzu, um sie zu aktivieren  
> Das settings-Verzeichnis der Erweiterung wird dann gelesen (falls vorhanden),  
> und jede weitere Konfiguration geschieht automatisch.  

Standard: eine Liste mit 1: `xrowmetadata`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ExtensionSettings]
# ein leeres "ActiveExtensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ActiveExtensions[]
ActiveExtensions[]=xrowmetadata
*/ ?>
```

## ActiveAccessExtensions

> Wie ActiveExtensions, wird aber geladen, nachdem der Siteaccess gewählt ist.  
> Damit lassen sich in verschiedenen Siteaccesses verschiedene Erweiterungen aktivieren  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ExtensionSettings]
# ein leeres "ActiveAccessExtensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ActiveAccessExtensions[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
