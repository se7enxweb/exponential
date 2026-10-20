---
title: admininterface.ini [PaginationSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [PaginationSettings] in admininterface.ini: DefaultItemsPerPage, ItemsPerPage, ItemsPerPageList_search_stats, ItemsPerPageList_shop_currencylist, ItemsPerPageList_shop_productsoverview, ItemsPerPageList_workflow_processlist, ItemsPerPageList_setup_session"
keywords: Einstellung, admininterface.ini, PaginationSettings, INI, setting, DefaultItemsPerPage, ItemsPerPage, ItemsPerPageList_search_stats, ItemsPerPageList_shop_currencylist, ItemsPerPageList_shop_productsoverview, ItemsPerPageList_workflow_processlist, ItemsPerPageList_setup_session
type: generated
generated: exp:help:examples
---
# admininterface.ini [PaginationSettings]

Der Block `[PaginationSettings]` von `settings/admininterface.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/admininterface.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/admininterface.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/admininterface.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/admininterface.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DefaultItemsPerPage

> Wie lang die Listen in der Administrationsoberfläche sind.  
>
> Ein Block statt einer Einstellung in der eigenen INI-Datei jedes Bereichs, weil die  
> Frage „Wo ändere ich, wie lang diese Listen sind?“ eine Antwort haben sollte. Der Schlüssel ist Modul und Ansicht, die die Liste zeichnen, sodass sich eine Einstellung  
> über die Adresse der Seite finden lässt, die sie steuert.  
>
> Leeren Sie nach einer Änderung die Caches:  
>   php bin/php/ezcache.php --clear-all  
> Gilt für jede Liste unten, die keine eigene Größe nennt.  

Standard: `25`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[PaginationSettings]
DefaultItemsPerPage=25
*/ ?>
```

## ItemsPerPage

> shop/preferredcurrency steht nicht in der Liste: Es ist ein Auswahlfeld für eine  
> Währung, keine Liste von Zeilen, und Blättern würde es unbrauchbar machen.  

Standard: ein Hash mit 22: `setup/extensions=25`, `setup/cronjobs=25`, `section/list=25`, `workflow/grouplist=25`, `workflow/processlist=10`, `oauthadmin/list=25`, `oauthadmin/keys=25`, `package/list=25`, `pdf/list=25`, `content/translations=25`, `infocollector/overview=25`, `class/grouplist=25` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[PaginationSettings]
# ein leeres "ItemsPerPage[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ItemsPerPage[]
ItemsPerPage[setup/extensions]=25
ItemsPerPage[setup/cronjobs]=25
ItemsPerPage[section/list]=25
ItemsPerPage[workflow/grouplist]=25
ItemsPerPage[workflow/processlist]=10
ItemsPerPage[oauthadmin/list]=25
ItemsPerPage[oauthadmin/keys]=25
ItemsPerPage[package/list]=25
ItemsPerPage[pdf/list]=25
ItemsPerPage[content/translations]=25
ItemsPerPage[infocollector/overview]=25
ItemsPerPage[class/grouplist]=25
ItemsPerPage[class/classlist]=25
ItemsPerPage[shop/orderlist]=15
ItemsPerPage[shop/customerlist]=15
ItemsPerPage[shop/archivelist]=50
ItemsPerPage[shop/discountgroup]=25
ItemsPerPage[shop/status]=25
ItemsPerPage[shop/vattype]=25
ItemsPerPage[shop/vatrules]=25
# ... 2 weitere in der ausgelieferten Datei
*/ ?>
```

## ItemsPerPageList_search_stats

Standard: eine Liste mit 3: `10`, `25`, `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[PaginationSettings]
# ein leeres "ItemsPerPageList_search_stats[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ItemsPerPageList_search_stats[]
ItemsPerPageList_search_stats[]=10
ItemsPerPageList_search_stats[]=25
ItemsPerPageList_search_stats[]=50
*/ ?>
```

## ItemsPerPageList_shop_currencylist

> Listen mit einer Auswahl für Einträge pro Seite bieten diese Größen an. Die  
> erste erhält, wer nie gewählt hat, und die Liste begrenzt auch  
> die Abfrage: Eine Größe, die nicht darin steht, kann nicht angefordert werden.  

Standard: eine Liste mit 3: `10`, `25`, `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[PaginationSettings]
# ein leeres "ItemsPerPageList_shop_currencylist[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ItemsPerPageList_shop_currencylist[]
ItemsPerPageList_shop_currencylist[]=10
ItemsPerPageList_shop_currencylist[]=25
ItemsPerPageList_shop_currencylist[]=50
*/ ?>
```

## ItemsPerPageList_shop_productsoverview

Standard: eine Liste mit 3: `10`, `25`, `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[PaginationSettings]
# ein leeres "ItemsPerPageList_shop_productsoverview[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ItemsPerPageList_shop_productsoverview[]
ItemsPerPageList_shop_productsoverview[]=10
ItemsPerPageList_shop_productsoverview[]=25
ItemsPerPageList_shop_productsoverview[]=50
*/ ?>
```

## ItemsPerPageList_workflow_processlist

Standard: eine Liste mit 3: `10`, `25`, `50`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[PaginationSettings]
# ein leeres "ItemsPerPageList_workflow_processlist[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ItemsPerPageList_workflow_processlist[]
ItemsPerPageList_workflow_processlist[]=10
ItemsPerPageList_workflow_processlist[]=25
ItemsPerPageList_workflow_processlist[]=50
*/ ?>
```

## ItemsPerPageList_setup_session

> Setup > Sitzungen: die gleichzeitig gezeigten Sitzungen (oder, ohne Sitzungstabelle, die letzten  
> Anmeldungen). Die erste Größe ist der Standard.  

Standard: eine Liste mit 3: `50`, `25`, `100`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/admininterface.ini.append.php
[PaginationSettings]
# ein leeres "ItemsPerPageList_setup_session[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ItemsPerPageList_setup_session[]
ItemsPerPageList_setup_session[]=50
ItemsPerPageList_setup_session[]=25
ItemsPerPageList_setup_session[]=100
*/ ?>
```

## Siehe auch

- [Alle Blöcke von admininterface.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/admininterface.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
