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

> Erlaubt eine harte Grenze für die Methode subtree, um das Laden sehr großer Teilbäume zu verhindern,  
>  was die Serverlast und das Risiko von DoS-Angriffen senkt.  
>  false deaktiviert die Funktion, jede Ganzzahl setzt die harte Grenze.  
>  Zum Beispiel: Eine harte Grenze von 100 erlaubt eine Begrenzung auf 30 oder 40, ersetzt aber eine  
>               Begrenzung von 150.  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[ezjscServer_ezjscnode]
HardLimit=false
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezjscore.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/settings/ezjscore.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
