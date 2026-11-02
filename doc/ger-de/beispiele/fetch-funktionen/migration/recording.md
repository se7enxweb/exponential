---
title: fetch( 'migration', 'recording' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion recording des Moduls migration: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, migration, recording, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'migration', 'recording' )

Definiert in `kernel/migration/function_definition.php`; sie ruft `expMigrationAdmin::fetchRecording()` auf (`kernel/classes/migration/expmigrationadmin.php`).

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'migration', 'recording' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'migration', 'recording', array() );
```

## In einem ausgelieferten Template

Die erste Verwendung in den Templates dieser Installation, `design/admin/templates/parts/exp_migration_recording_bar.tpl` Zeile 8:

```tpl
{def $exp_recording = fetch( 'migration', 'recording' )}
```

## Siehe auch

- [Alle Fetch-Funktionen von migration](README.md)
- [Die Ansichten des Moduls migration](../../modulansichten/migration/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/migration/function_definition.php`, `kernel/classes/migration/expmigrationadmin.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
