---
title: mailpreferences.ini [CategorySettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CategorySettings] in mailpreferences.ini: AllowDefaultOn, Categories"
keywords: Einstellung, mailpreferences.ini, CategorySettings, INI, setting, AllowDefaultOn, Categories
type: generated
generated: exp:help:examples
---
# mailpreferences.ini [CategorySettings]

Der Block `[CategorySettings]` von `settings/mailpreferences.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/mailpreferences.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/mailpreferences.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/mailpreferences.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/mailpreferences.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AllowDefaultOn

> Die Kategorien, in der Reihenfolge, in der die Einstellungsseite sie zeigt. Jede hat einen  
> Block \[Category\_<identifier>\]:  
>   Name, Description   auf der Seite angezeigt (übersetzt über den  
>                       Kontext kernel/mailpreferences/categories)  
>   Essential           true: nie abschaltbar, immer gesendet (Konto-  
>                       sicherheit, Bestellungen, rechtliche Hinweise, Alarme für Administratoren)  
>   DefaultOn           nur für optionale Kategorien; false (Opt-in) ist der  
>                       Standard und in der EU die einzige zulässige Wahl. true wird  
>                       abgelehnt (als false behandelt, mit einer Warnung auf der Status-  
>                       seite), außer AllowDefaultOn=enabled unten  
>   Frequencies\[\]       immediate, daily, weekly; leer: Die Kategorie behält  
>                       ihren eigenen Zeitplan  
>   DoubleOptIn         true: Das Einschalten sendet zuerst eine Bestätigungsmail  
>   HandlerClass        optionale PHP-Klasse, die  
>                       expMailCategoryHandler implementiert und vorhandene Daten  
>                       (Abonnements) als Zustand der Kategorie liest  
> Erweiterungen fügen Kategorien in ihrer eigenen mailpreferences.ini.append.php hinzu.  
>
> AllowDefaultOn: enabled lässt eine optionale Kategorie für alle an sein, die nie  
> gewählt haben (DefaultOn=true: Opt-out). Nur für eine Website, deren Empfänger einem  
> Recht unterliegen, das es für diese Mail erlaubt (nie Werbung in der EU oder nach Kanada).  
> disabled (der Standard): DefaultOn=true wird als false behandelt.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[CategorySettings]
AllowDefaultOn=disabled
*/ ?>
```

## Categories

Standard: eine Liste mit 9: `content`, `collaboration`, `newsletter`, `marketing`, `system`, `security`, `orders`, `legal`, `admin`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[CategorySettings]
# ein leeres "Categories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Categories[]
Categories[]=content
Categories[]=collaboration
Categories[]=newsletter
Categories[]=marketing
Categories[]=system
Categories[]=security
Categories[]=orders
Categories[]=legal
Categories[]=admin
*/ ?>
```

## Siehe auch

- [Alle Blöcke von mailpreferences.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/mailpreferences.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
