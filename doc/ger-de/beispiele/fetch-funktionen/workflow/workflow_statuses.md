---
title: fetch( 'workflow', 'workflow_statuses' )
audience: developer, admin
category: examples
summary: "Die Fetch-Funktion workflow_statuses des Moduls workflow: 0 Parameter, 0 erforderlich."
keywords: Fetch-Funktion, fetch, workflow, workflow_statuses, fetch function, template
type: generated
generated: exp:help:examples
---
# fetch( 'workflow', 'workflow_statuses' )

Definiert in `kernel/workflow/function_definition.php`; sie ruft `eZWorkflowFunctionCollection::fetchWorkflowStatuses()` auf (`kernel/workflow/ezworkflowfunctioncollection.php`).
Operationstyp: read.

## Parameter

Keine.

## Beispiel

In einem Template (die Werte sind Platzhalter vom Typ des jeweiligen Parameters):

```tpl
{def $result = fetch( 'workflow', 'workflow_statuses' )}
```

In PHP dieselbe Funktion über den Function-Handler (`lib/ezutils/classes/ezfunctionhandler.php`):

```php
$result = eZFunctionHandler::execute( 'workflow', 'workflow_statuses', array() );
```

## Siehe auch

- [Alle Fetch-Funktionen von workflow](README.md)
- [Die Ansichten des Moduls workflow](../../modulansichten/workflow/README.md)
- [Der Template-Operator fetch](../../template-operatoren/fetch.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `kernel/workflow/function_definition.php`, `kernel/workflow/ezworkflowfunctioncollection.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
