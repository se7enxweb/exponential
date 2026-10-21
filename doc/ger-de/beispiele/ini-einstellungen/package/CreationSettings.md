---
title: package.ini [CreationSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CreationSettings] in package.ini: HandlerList, HandlerAlias"
keywords: Einstellung, package.ini, CreationSettings, INI, setting, HandlerList, HandlerAlias
type: generated
generated: exp:help:examples
---
# package.ini [CreationSettings]

Der Block `[CreationSettings]` von `settings/package.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/package.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/package.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/package.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/package.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## HandlerList

> Eine Liste der Erweiterungen, die Handler zum Erstellen von Paketen unterstützen  
> Die Erweiterung muss die Handler zum Erstellen von Paketen im  
> Unterverzeichnis packagecreators ablegen.  
> Die Reihenfolge hier ist die, in der die Seite Paket erstellen die Assistenten auflistet:  
> zuerst Inhalte, dann ihre Klassen, dann Erweiterungen, dann Website-Styles.  

Standard: eine Liste mit 4: `ezcontentobject`, `ezcontentclass`, `ezextension`, `ezstyle`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[CreationSettings]
# ein leeres "HandlerList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HandlerList[]
HandlerList[]=ezcontentobject
HandlerList[]=ezcontentclass
HandlerList[]=ezextension
HandlerList[]=ezstyle
*/ ?>
```

## HandlerAlias

Standard: ein Hash mit 4: `ezcontentclass=eZContentClassPackageCreator`, `ezstyle=eZStylePackageCreator`, `ezcontentobject=eZContentObjectPackageCreator`, `ezextension=eZExtensionPackageCreator`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[CreationSettings]
# ein leeres "HandlerAlias[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HandlerAlias[]
HandlerAlias[ezcontentclass]=eZContentClassPackageCreator
HandlerAlias[ezstyle]=eZStylePackageCreator
HandlerAlias[ezcontentobject]=eZContentObjectPackageCreator
HandlerAlias[ezextension]=eZExtensionPackageCreator
*/ ?>
```

## Siehe auch

- [Alle Blöcke von package.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/package.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
