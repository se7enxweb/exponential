---
title: template.ini [AutoLinkOperator]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AutoLinkOperator] in template.ini: MaxCharacters, Methods"
keywords: Einstellung, template.ini, AutoLinkOperator, INI, setting, MaxCharacters, Methods
type: generated
generated: exp:help:examples
---
# template.ini [AutoLinkOperator]

Der Block `[AutoLinkOperator]` von `settings/template.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/template.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/template.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/template.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/template.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MaxCharacters

> Die Höchstzahl der Zeichen, die in autolink-  
> Links angezeigt werden. Geben Sie das Maximum als Zahl an oder schalten Sie es  
> mit einer leeren Zeichenkette als Wert ab.  
> Ist die Zeichenkette des Links länger als der Höchstwert, wird sie gekürzt  

Standard: `72`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/template.ini.append.php
[AutoLinkOperator]
MaxCharacters=72
*/ ?>
```

## Methods

> Höchstens 72 Zeichen  
> MaxCharacters=72  
> Keine Kürzung der Zeichen  
> MaxCharacters=  
> Eine Liste von URL-Methoden oder Protokollen, die erkannt werden sollen  

Standard: eine Liste mit 6: `http`, `https`, `ftp`, `ftps`, `webdav`, `webdavs`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/template.ini.append.php
[AutoLinkOperator]
# ein leeres "Methods[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Methods[]
Methods[]=http
Methods[]=https
Methods[]=ftp
Methods[]=ftps
Methods[]=webdav
Methods[]=webdavs
*/ ?>
```

## Siehe auch

- [Alle Blöcke von template.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/template.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
