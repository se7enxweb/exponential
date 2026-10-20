---
title: shop.ini [BasketInfoSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [BasketInfoSettings] in shop.ini: Handler, RepositoryDirectories, ExtensionDirectories"
keywords: Einstellung, shop.ini, BasketInfoSettings, INI, setting, Handler, RepositoryDirectories, ExtensionDirectories
type: generated
generated: exp:help:examples
---
# shop.ini [BasketInfoSettings]

Der Block `[BasketInfoSettings]` von `settings/shop.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/shop.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/shop.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/shop.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/shop.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Handler

> Gibt den Klassen-/Dateinamen des Basketinfo-Handlers an. Ist der Wert zum Beispiel  
> "ezcustom", muss die Handler-Klasse  
> eZCustomBasketInfoHandler heißen (Groß-/Kleinschreibung egal) und in der Datei  
> ezcustombasketinfohandler.php in einem Verzeichnis liegen, das die Einstellungen  
> "RepositoryDirectories" und "ExtensionDirectories" angeben.  
> ezdefault ist der Standard-Handler für Warenkorbinformationen, der grundlegende  
> berechnete Informationen über die Einträge im Warenkorb liefert.  

Standard: `ezdefault`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[BasketInfoSettings]
Handler=ezdefault
*/ ?>
```

## RepositoryDirectories

> Verzeichnisse, in denen nach Basketinfo-Handlern gesucht wird.  

Standard: eine Liste mit 1: `kernel/classes/basketinfohandlers`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[BasketInfoSettings]
# ein leeres "RepositoryDirectories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RepositoryDirectories[]
RepositoryDirectories[]=kernel/classes/basketinfohandlers
*/ ?>
```

## ExtensionDirectories

> Wenn Sie Ihren Basketinfo-Handler in einer Erweiterung umsetzen wollen,  
> fügen Sie den Namen der Erweiterung der Liste unten hinzu.  
> Dann werden Basketinfo-Handler im folgenden  
> (fest eingestellten) Verzeichnis gesucht:  
> extension/<your\_extension>/basketinfohandlers  
> neben den Verzeichnissen aus der Einstellung 'RepositoryDirectories'.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/shop.ini.append.php
[BasketInfoSettings]
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
