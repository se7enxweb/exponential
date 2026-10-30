---
title: graphql.ini [GraphQLNaming]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [GraphQLNaming] in graphql.ini: TypeNames, FieldNames"
keywords: Einstellung, graphql.ini, GraphQLNaming, INI, setting, TypeNames, FieldNames
type: generated
generated: exp:help:examples
---
# graphql.ini [GraphQLNaming]

Der Block `[GraphQLNaming]` von `settings/graphql.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/graphql.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/graphql.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/graphql.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/graphql.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TypeNames

> NamingStyle=map: der Name eines Typs je Klassenbezeichner und eines Felds je <klasse>/<attribut>.  
> TypeNames\[ng\_article\]=Article  
> FieldNames\[ng\_article/short\_title\]=shortTitle  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLNaming]
# ein leeres "TypeNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TypeNames[]
*/ ?>
```

## FieldNames

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/graphql.ini.append.php
[GraphQLNaming]
# ein leeres "FieldNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
FieldNames[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von graphql.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/graphql.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
