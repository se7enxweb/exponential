---
title: menu.ini [NavigationPart]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [NavigationPart] in menu.ini: Part"
keywords: Einstellung, menu.ini, NavigationPart, INI, setting, Part
type: generated
generated: exp:help:examples
---
# menu.ini [NavigationPart]

Der Block `[NavigationPart]` von `settings/menu.ini`, mit 1 Einstellung(en).

> Eine Liste der aktuellen Navigationsbereiche und ihrer Namen  
> Jeder Eintrag besteht aus dem Bezeichner als Schlüssel und dem Namen  
> als Wert.  
> Hinweis: Wenn der Name übersetzbar sein soll, müssen Sie  
>       eine Dummy-PHP-Datei mit folgendem Inhalt anlegen  
> ezpI18n::tr( 'kernel/navigationpart', 'Custom navigation', 'Navigation part' );  
> Dann nimmt ezlupdate sie in die .ts-Datei auf.  

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Part

Standard: ein Hash mit 8: `ezcontentnavigationpart=Content structure`, `ezmedianavigationpart=Media library`, `ezusernavigationpart=User accounts`, `ezshopnavigationpart=Webshop`, `ezvisualnavigationpart=Design`, `ezsetupnavigationpart=Setup`, `ezmynavigationpart=My account`, `expauditnavigationpart=Audit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[NavigationPart]
# ein leeres "Part[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Part[]
Part[ezcontentnavigationpart]=Content structure
Part[ezmedianavigationpart]=Media library
Part[ezusernavigationpart]=User accounts
Part[ezshopnavigationpart]=Webshop
Part[ezvisualnavigationpart]=Design
Part[ezsetupnavigationpart]=Setup
Part[ezmynavigationpart]=My account
Part[expauditnavigationpart]=Audit
*/ ?>
```

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
