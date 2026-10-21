---
title: shop.ini [ShippingSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ShippingSettings] in shop.ini: RepositoryDirectories, ExtensionDirectories"
keywords: Einstellung, shop.ini, ShippingSettings, INI, setting, RepositoryDirectories, ExtensionDirectories
type: generated
generated: exp:help:examples
---
# shop.ini [ShippingSettings]

Der Block `[ShippingSettings]` von `settings/shop.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/shop.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/shop.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/shop.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/shop.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RepositoryDirectories

> Gibt den Klassen-/Dateinamen des Versand-Handlers an. Ist der Wert zum Beispiel  
> "ezcustom", muss die Handler-Klasse  
> eZCustomShippingHandler heißen (Groß-/Kleinschreibung egal) und in der Datei  
> ezcustomshippinghandler.php in einem Verzeichnis liegen, das die Einstellungen  
> "RepositoryDirectories" und "ExtensionDirectories" angeben.  
> Handler=ezdefault  
> Verzeichnisse, in denen nach Versand-Handlern gesucht wird.  

Standard: eine Liste mit 1: `kernel/classes/shippinghandlers`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[ShippingSettings]
# ein leeres "RepositoryDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RepositoryDirectories[]
RepositoryDirectories[]=kernel/classes/shippinghandlers
*/ ?>
```

## ExtensionDirectories

> Wenn Sie Ihren Versand-Handler in einer Erweiterung umsetzen wollen,  
> fügen Sie den Namen der Erweiterung der Liste unten hinzu.  
> Dann werden Versand-Handler im folgenden  
> (fest eingestellten) Verzeichnis gesucht:  
> extension/<your\_extension>/shippinghandlers  
> neben den Verzeichnissen aus der Einstellung 'RepositoryDirectories'.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[ShippingSettings]
# ein leeres "ExtensionDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionDirectories[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von shop.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/shop.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
