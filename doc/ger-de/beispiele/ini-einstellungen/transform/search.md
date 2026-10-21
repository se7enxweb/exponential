---
title: transform.ini [search]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [search] in transform.ini: Files, Extensions, Commands"
keywords: Einstellung, transform.ini, search, INI, setting, Files, Extensions, Commands
type: generated
generated: exp:help:examples
---
# transform.ini [search]

Der Block `[search]` von `settings/transform.ini`, mit 3 Einstellung(en).

> \[<group>\]  
> Rules\[\]=<command>(<option>\[,<option>\])  
> oder  
> Rules\[\]=<command>  
>
> Befehle:  
> normalize  
> - Fasst mehrere Zeichen mit ähnlicher Bedeutung zu einer Art zusammen  
> - Sucht für jeden Parameter eine Regel namens <parameter>\_normalize  
> - Ohne Parameter findet er alle Normalisierungsregeln  
>
> search\_normalize  
> - Führt die für die Suchmaschine nötigen Normalisierungsregeln aus  
> - Sucht für jeden Parameter eine Regel namens <parameter>\_search\_normalize  
> - Ohne Parameter findet er alle Suchregeln  
>
> transform  
> - Wandelt eine Zeichenart in eine andere um  
> - Sucht eine Regel namens <parameter1>\_to\_<parameter2>  
> - Ohne Parameter findet er alle Umwandlungsregeln  
>
> decompose  
> - Wandelt Sonderzeichen in Zeichenketten um, die das Zeichen darstellen  
> - Sucht für jeden Parameter eine Regel namens <parameter>\_decompose  
> - Ohne Parameter findet er alle Zerlegungsregeln  
>
> lowercase  
> - Wandelt Zeichen, wenn möglich, in ihre Kleinbuchstabenform um  
> - Sucht für jeden Parameter eine Regel namens <parameter>\_lowercase  
> - Ohne Parameter findet er alle Regeln für Kleinbuchstaben  
>
> uppercase  
> - Wandelt Zeichen, wenn möglich, in ihre Großbuchstabenform um  
> - Sucht für jeden Parameter eine Regel namens <parameter>\_uppercase  
> - Ohne Parameter findet er alle Regeln für Großbuchstaben  
>
> transliterate  
> - Wandelt von einem Zeichensatz in einen anderen um, indem er ähnliche  
>   Zeichen oder Zeichenketten findet  
> - Sucht eine Regel namens <parameter1>\_transliterate\_<parameter2>  
> - Ohne Parameter findet er alle Transliterationsregeln  
>
> diacritical  
> - Entfernt diakritische Zeichen  
> - Sucht für jeden Parameter eine Regel namens <parameter>\_diacritical  
> - Ohne Parameter findet er alle Regeln für diakritische Zeichen  
>
> rule  
> - Führt eine eigene Umwandlungsregel aus  
> - Sucht für jeden Parameter eine Regel namens <parameter>  
> - Ohne Parameter tut er nichts  
>
> Besondere Befehle:  
> url\_cleanup  
> - Bereinigt den Text, damit er in einer URL darstellbar ist  
>
> identifier\_cleanup  
> - Bereinigt den Text, damit er als Bezeichner verwendbar ist  
>
> search\_cleanup:  
> - Bereinigt den Text, damit die Suchmaschine ihn parsen kann  
> - Sucht für jeden Parameter eine Regel namens <parameter>\_search\_cleanup  
> Regeln der Suchmaschine; diese Regeln sorgen dafür, dass  
> der Index- oder Suchtext so normalisiert wird,  
> dass die Suchmaschine leichter damit arbeiten kann.  

Bearbeiten Sie `settings/transform.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/transform.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/transform.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/transform.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Files

> Zusätzliche Umwandlungsdateien für die Suche  

Standard: eine Liste mit 1: `search.tr`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[search]
# ein leeres "Files[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Files[]
Files[]=search.tr
*/ ?>
```

## Extensions

> Erweiterungen, die Umwandlungsdateien für die Suche haben  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[search]
# ein leeres "Extensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Extensions[]
*/ ?>
```

## Commands

> Die für die Suche verwendeten Befehle  
> Commands\[\]=transform  

Standard: eine Liste mit 8: `decompose`, `normalize`, `diacritical`, `search_normalize`, `lowercase`, `transform(inverted,normal)`, `transform(math,ascii)`, `search_cleanup`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[search]
# ein leeres "Commands[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Commands[]
Commands[]=decompose
Commands[]=normalize
Commands[]=diacritical
Commands[]=search_normalize
Commands[]=lowercase
Commands[]=transform(inverted,normal)
Commands[]=transform(math,ascii)
Commands[]=search_cleanup
*/ ?>
```

## Siehe auch

- [Alle Blöcke von transform.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/transform.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
