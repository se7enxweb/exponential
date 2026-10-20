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

> Wenn aktiviert, können Informationen von anonymen Benutzern gesammelt werden  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[CollectionSettings]
CollectAnonymousData=enabled
*/ ?>
```

## CollectAnonymousDataList

> Wie CollectAnonymousData, aber als Liste von Informationssammlungstypen und  
> ihren abweichenden Einstellungen; wenn angegeben, überschreibt sie die Standardeinstellung  

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

> Sind die gesammelten Daten sensibel? Wenn true, kann ein Benutzer gesammelte  
> Daten nur sehen, wenn er zuvor ein Formular abgeschickt hat  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[CollectionSettings]
CollectSensitiveData=true
*/ ?>
```

## CollectSensitiveDataList

> Wie CollectSensitiveData, aber als Liste von Informationssammlungstypen und  
> ihren abweichenden Einstellungen; wenn angegeben, überschreibt sie die Standardeinstellung  

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

> Wie die Informationssammlung mit der Identifizierung von Benutzern umgeht  
>
> multiple  - jeder Benutzer kann mehrfach Daten abschicken  
> unique    - ein Datensatz pro Benutzer; wenn schon vorhanden, gibt es eine Warnung  
> overwrite - ein Datensatz pro Benutzer, aber ein neuer Eintrag überschreibt den alten  

Standard: `multiple`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/collect.ini.append.php
[CollectionSettings]
CollectionUserData=multiple
*/ ?>
```

## CollectionUserDataList

> Ordnet Klassen-ID oder -Bezeichner einem Informationssammlungstyp zu  
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
