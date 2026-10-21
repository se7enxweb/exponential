---
title: transform.ini [urlalias_iri]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [urlalias_iri] in transform.ini: Files, Extensions, Commands"
keywords: Einstellung, transform.ini, urlalias_iri, INI, setting, Files, Extensions, Commands
type: generated
generated: exp:help:examples
---
# transform.ini [urlalias_iri]

Der Block `[urlalias_iri]` von `settings/transform.ini`, mit 3 Einstellung(en).

> Regeln des URL-Alias-Systems (schöne URLs); die Regeln  
> sorgen dafür, dass der Text in gleichwertige ASCII-  
> Zeichen umgewandelt wird. Die wichtigsten Regeln sind Transliteration,  
> Kleinschreibung und das Entfernen diakritischer Zeichen.  
> Regel für URL-Aliase mit IRI-Unterstützung.  
> http://www.w3.org/International/O-URL-and-ident.html  
>
> Das wird für die meisten modernen Websites dringend empfohlen.  
>
> Hinweis: Verwenden Sie utf-8 als Zeichensatz für Ihre Website und Datenbank  

Bearbeiten Sie `settings/transform.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/transform.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/transform.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/transform.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Files

> Zusätzliche Umwandlungsdateien für urlalias  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[urlalias_iri]
# ein leeres "Files[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Files[]
*/ ?>
```

## Extensions

> Erweiterungen, die Umwandlungsdateien für urlalias haben  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[urlalias_iri]
# ein leeres "Extensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Extensions[]
*/ ?>
```

## Commands

> Die für die Suche verwendeten Befehle  
> Nur die IRI-Bereinigung ausführen; damit sind in URLs fast alle Unicode-Zeichen  
> erlaubt, die Ausnahmen sind: Leerzeichen, Und-Zeichen und Semikolon.  

Standard: eine Liste mit 1: `url_cleanup_iri`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[urlalias_iri]
# ein leeres "Commands[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Commands[]
Commands[]=url_cleanup_iri
*/ ?>
```

## Siehe auch

- [Alle Blöcke von transform.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/transform.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
