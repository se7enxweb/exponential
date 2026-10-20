---
title: collect.ini [CollectionSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [CollectionSettings] in collect.ini: CollectAnonymousData, CollectAnonymousDataList, CollectAnonymousDataAttribute, CollectSensitiveData, CollectSensitiveDataList, CollectionUserData, CollectionUserDataList, CollectionUserDataAttribute"
keywords: Einstellung, collect.ini, CollectionSettings, INI, setting, CollectAnonymousData, CollectAnonymousDataList, CollectAnonymousDataAttribute, CollectSensitiveData, CollectSensitiveDataList, CollectionUserData, CollectionUserDataList, CollectionUserDataAttribute
type: generated
generated: exp:help:examples
---
# collect.ini [CollectionSettings]

Der Block `[CollectionSettings]` von `settings/collect.ini`, mit 8 Einstellung(en).

Bearbeiten Sie `settings/collect.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/collect.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/collect.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/collect.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## CollectAnonymousData

> if enabled then information from anonymous users can be collected  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[CollectionSettings]
CollectAnonymousData=enabled
*/ ?>
```

## CollectAnonymousDataList

> Same as CollectAnonymousData but is a list with of IC types and  
> their override settings, if specified it will override default setting  

Standard: ein Hash mit 1: `poll=enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[CollectionSettings]
# ein leeres "CollectAnonymousDataList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CollectAnonymousDataList[]
CollectAnonymousDataList[poll]=enabled
*/ ?>
```

## CollectAnonymousDataAttribute

Standard: `collection_anonymous`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[CollectionSettings]
CollectAnonymousDataAttribute=collection_anonymous
*/ ?>
```

## CollectSensitiveData

> Are collected data sensitive? If true then user will be able to view collected  
> data only when previously submit a form  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[CollectionSettings]
CollectSensitiveData=true
*/ ?>
```

## CollectSensitiveDataList

> Same as CollectSensitiveData but is a list with of IC types and  
> their override settings, if specified it will override default setting  

Standard: ein Hash mit 1: `poll=false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[CollectionSettings]
# ein leeres "CollectSensitiveDataList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CollectSensitiveDataList[]
CollectSensitiveDataList[poll]=false
*/ ?>
```

## CollectionUserData

> How information collection is handled in terms of user identification  
>
> multiple  - each user can submit multiple data  
> unique    - one set of data per user, if already exists give a warning  
> overwrite - one set of data per user but new entry overwrites old one  

Standard: `multiple`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[CollectionSettings]
CollectionUserData=multiple
*/ ?>
```

## CollectionUserDataList

> Matches class id or identifier to information collection type  
> CollectionUserDataList\[feedback\]=multiple  

Standard: ein Hash mit 1: `poll=unique`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[CollectionSettings]
# ein leeres "CollectionUserDataList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CollectionUserDataList[]
CollectionUserDataList[poll]=unique
*/ ?>
```

## CollectionUserDataAttribute

Standard: `collection_userdata`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[CollectionSettings]
CollectionUserDataAttribute=collection_userdata
*/ ?>
```

## Siehe auch

- [Alle Blöcke von collect.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/collect.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
