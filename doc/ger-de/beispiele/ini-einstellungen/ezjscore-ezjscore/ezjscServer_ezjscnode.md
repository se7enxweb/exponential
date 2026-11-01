---
title: ezjscore.ini [ezjscServer_ezjscnode]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ezjscServer_ezjscnode] in ezjscore.ini (ezjscore): Class, Functions, RequirePolicy, HardLimit"
keywords: Einstellung, ezjscore.ini, ezjscServer_ezjscnode, INI, setting, Class, Functions, RequirePolicy, HardLimit
type: generated
generated: exp:help:examples
---
# ezjscore.ini [ezjscServer_ezjscnode]

Der Block `[ezjscServer_ezjscnode]` von `extension/ezjscore/settings/ezjscore.ini` (Erweiterung ezjscore), mit 4 Einstellung(en).

> File=extension/ezjscore/classes/ezjscserverfunctionsjs.php  

Bearbeiten Sie `extension/ezjscore/settings/ezjscore.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezjscore.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezjscore.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezjscore.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

> URL zum Testen dieser Serverfunktion (gibt eine Knotenliste zurück):  
> <root>/ezjscore/call/ezjscnode::subtree::2  

Standard: `ezjscServerFunctionsNode`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[ezjscServer_ezjscnode]
Class=ezjscServerFunctionsNode
*/ ?>
```

## Functions

> File=extension/ezjscore/classes/ezjscserverfunctionsnode.php  
> Die Knotendaten (Name, IDs, Remote-ID, Eigentümer, Sektion, Daten, Klasse, Übersetzungen, Pfade) kann  
> jeder Benutzer lesen, der den Knoten lesen darf, anonyme Besucher eingeschlossen, außer bei  
> RequirePolicy=enabled: Dann braucht ein Benutzer außerdem ezjscore/call mit der Funktionsliste ezjscnode  
> (Einschränkung FunctionList). Neue Installationen schreiben RequirePolicy=enabled in  
> settings/override/ezjscore.ini.append.php und gewähren der Rolle Editor diese Richtlinie; eine aktualisierte  
> Website behält disabled, bis sie umschaltet. Anleitung: doc/eng-us/bc/6.0/backend\_ezjscore\_services.md  

Standard: eine Liste mit 1: `ezjscnode`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[ezjscServer_ezjscnode]
# ein leeres "Functions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Functions[]
Functions[]=ezjscnode
*/ ?>
```

## RequirePolicy

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[ezjscServer_ezjscnode]
RequirePolicy=disabled
*/ ?>
```

## HardLimit

> Die meisten Einträge, die eine Anfrage an subtree (und an die Zeilen von expsubitems) liefert, welches Limit  
> sie auch verlangt: eine Anfrage nach einer Million Kindern eines großen Ordners lüde sie alle. Ein größeres  
> Limit wird durch dieses ersetzt, ein kleineres bleibt: mit 500 bleibt ein Limit von 30 bei 30, und  
> 1000 wird zu 500. 500 ist die größte Seitengröße, die die Unterelemente-Liste der Administration anbietet.  
> false (oder 0) schaltet die Grenze ab, wie vor 6.0.15.  

Standard: `500`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[ezjscServer_ezjscnode]
HardLimit=500
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezjscore.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/settings/ezjscore.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
