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

> The categories, in the order the preference page shows them. Each has a  
> block \[Category\_<identifier>\]:  
>   Name, Description   shown on the page (translated through the  
>                       kernel/mailpreferences/categories context)  
>   Essential           true: never switchable, always sent (account  
>                       security, orders, legal notices, admin alerts)  
>   DefaultOn           only for optional categories; false (opt-in) is the  
>                       default and the only lawful choice in the EU. true is  
>                       refused (treated as false, with a warning on the status  
>                       page) unless AllowDefaultOn=enabled below  
>   Frequencies\[\]       immediate, daily, weekly; empty: the category keeps  
>                       its own schedule  
>   DoubleOptIn         true: switching on sends a confirmation mail first  
>   HandlerClass        optional PHP class implementing  
>                       expMailCategoryHandler, which reads existing data  
>                       (subscriptions) as the state of the category  
> Extensions add categories in their own mailpreferences.ini.append.php.  
>
> AllowDefaultOn: enabled lets an optional category be on for everybody who never  
> chose (DefaultOn=true: opt-out). Only for a site whose recipients are under a  
> law that allows it for that mail (never marketing in the EU or to Canada).  
> disabled (the default): DefaultOn=true is treated as false.  

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
