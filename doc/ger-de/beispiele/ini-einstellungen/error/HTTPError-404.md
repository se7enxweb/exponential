---
title: error.ini [HTTPError-404]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [HTTPError-404] in error.ini: HTTPName, HeaderList"
keywords: Einstellung, error.ini, HTTPError-404, INI, setting, HTTPName, HeaderList
type: generated
generated: exp:help:examples
---
# error.ini [HTTPError-404]

Der Block `[HTTPError-404]` von `settings/error.ini`, mit 2 Einstellung(en).

> Definition des HTTP-Fehlercodes 404.  
> Weitere Fehlercodes lassen sich angeben, indem man eine Gruppe  
> HTTPError, gefolgt von einem - (Bindestrich) und dem HTTP-Fehlercode, anlegt.  
> Die Gruppe muss die Variable HTTPName enthalten, sonst wird der Fehlercode  
> nicht an den Browser gesendet.  
> Hinweis: HTTPName muss die korrekte Zeichenkette für den  
>       jeweiligen HTTP-Fehlercode enthalten.  
>
> HeaderList\[\] fügt der Fehlerseite Antwort-Header hinzu, einen pro Eintrag:  
> HeaderList\[<header name>\]=<value>. Sie werden nach den Statuszeilen gesendet  
> und mit der Seite im View-Cache der Inhalte gespeichert, sodass eine gecachte Fehlerseite  
> sie erneut sendet. Ein Name, der kein gültiger Header-Name ist, oder ein Wert mit einem  
> Zeilenumbruch wird mit einer Warnung ausgelassen. HeaderList\[Status\]=<text> wird  
> als HTTPName gelesen, wenn HTTPName nicht gesetzt ist. Standardmäßig leer: Nichts wird hinzugefügt.  
> Beispiel:  
> HeaderList\[Cache-Control\]=public, must-revalidate, max-age=300  

Bearbeiten Sie `settings/error.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/error.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/error.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/error.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## HTTPName

Standard: `Not Found`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[HTTPError-404]
HTTPName=Not Found
*/ ?>
```

## HeaderList

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/error.ini.append.php
[HTTPError-404]
# ein leeres "HeaderList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
HeaderList[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von error.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/error.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
