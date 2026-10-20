---
title: ezjscore.ini [Packer]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Packer] in ezjscore.ini (ezjscore): AppendLastModifiedTime, CustomHosts"
keywords: Einstellung, ezjscore.ini, Packer, INI, setting, AppendLastModifiedTime, CustomHosts
type: generated
generated: exp:help:examples
---
# ezjscore.ini [Packer]

Der Block `[Packer]` von `extension/ezjscore/settings/ezjscore.ini` (Erweiterung ezjscore), mit 2 Einstellung(en).

Bearbeiten Sie `extension/ezjscore/settings/ezjscore.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezjscore.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezjscore.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezjscore.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AppendLastModifiedTime

> Hängt die Zeit der letzten Änderung an den Namen der gecachten Datei an, sodass Proxy- und Browser-Cache  
> die Datei bei einer Änderung neu holen müssen. Hinweis: Ein Cronjob zum Aufräumen abgelaufener  
> Dateien existiert nicht, sollte aber recht einfach zu erstellen sein.  
> Standardmäßig aktiviert: Gepackte Skripte und Styles werden mit langer Browser-Cache-Dauer ausgeliefert, ohne die  
> Zeit im Namen würde ein geändertes Stylesheet oder Skript die Browser also erst erreichen, wenn ihre Kopie abläuft.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[Packer]
AppendLastModifiedTime=enabled
*/ ?>
```

## CustomHosts

> Um die Last statischer Dateien auf getrennte Domänen zu verteilen, wie es die  
> YDN Performance Best Practices empfehlen. Der Schlüssel muss zur Dateiendung passen: .js | .css  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezjscore.ini.append.php
[Packer]
# ein leeres "CustomHosts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CustomHosts[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezjscore.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/settings/ezjscore.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
