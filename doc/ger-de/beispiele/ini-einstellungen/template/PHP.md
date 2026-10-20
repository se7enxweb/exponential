---
title: template.ini [PHP]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [PHP] in template.ini: PHPOperatorList"
keywords: Einstellung, template.ini, PHP, INI, setting, PHPOperatorList
type: generated
generated: exp:help:examples
---
# template.ini [PHP]

Der Block `[PHP]` von `settings/template.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/template.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/template.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/template.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/template.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## PHPOperatorList

> A list with php functions and their equivalent template operator names  
> Giving the operators a different name from the PHP functions  
> are recommended, that way you ensure that all operators follow  
> the same naming guidelines. It also means that you can change the  
> php function later on without doing template changes.  
> Note: Only one parameter (the value) is supported and php function must  
>       return result by value and not by reference.  
> RFC 3986 encoding: a space becomes %20 (urlencode gives +), for path segments and  
> for values another system decodes with rawurldecode  

Standard: ein Hash mit 4: `urlencode=urlencode`, `urldecode=urldecode`, `rawurlencode=rawurlencode`, `rawurldecode=rawurldecode`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/template.ini.append.php
[PHP]
# ein leeres "PHPOperatorList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PHPOperatorList[]
PHPOperatorList[urlencode]=urlencode
PHPOperatorList[urldecode]=urldecode
PHPOperatorList[rawurlencode]=rawurlencode
PHPOperatorList[rawurldecode]=rawurldecode
*/ ?>
```

## Siehe auch

- [Alle Blöcke von template.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/template.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
