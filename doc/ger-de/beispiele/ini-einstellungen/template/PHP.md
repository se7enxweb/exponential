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

> Eine Liste von PHP-Funktionen und den Namen ihrer gleichwertigen Template-Operatoren  
> Es wird empfohlen, den Operatoren einen anderen Namen als den PHP-Funktionen  
> zu geben; so stellen Sie sicher, dass alle Operatoren denselben  
> Benennungsregeln folgen. Außerdem können Sie dann die  
> PHP-Funktion später ändern, ohne Templates zu ändern.  
> Hinweis: Nur ein Parameter (der Wert) wird unterstützt, und die PHP-Funktion muss  
>       das Ergebnis als Wert und nicht als Referenz zurückgeben.  
> Kodierung nach RFC 3986: Ein Leerzeichen wird zu %20 (urlencode ergibt +), für Pfadsegmente und  
> für Werte, die ein anderes System mit rawurldecode dekodiert  

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
