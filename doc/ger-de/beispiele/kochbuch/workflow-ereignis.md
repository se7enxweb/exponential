---
title: Ein Workflow-Ereignis
audience: developer, admin
category: examples
order: 70
summary: Das Workflow-Ereignis explogpublish schreiben, das jedes veröffentlichte Objekt protokolliert, als Unterklasse von eZWorkflowEventType, angemeldet in workflow.ini, und es unter Setup > Workflows und Setup > Auslöser mit dem Auslöser für das Veröffentlichen verbinden.
keywords: Workflow, Workflow-Ereignis, Ereignistyp, Auslöser, Veröffentlichen, Protokoll, workflow, workflow event, event type, eZWorkflowEventType, eZWorkflowType, execute, STATUS_ACCEPTED, setTriggerTypes, workflow.ini, EventSettings, AvailableEventTypes, ExtensionDirectories, trigger, content/publish, event_view_gui
type: example
---
# Ein Workflow-Ereignis

Ein Workflow ist eine Liste von Ereignissen, die der Kernel ausführt, wenn eine Operation stattfindet (Veröffentlichen,
eine Anmeldung, ein Bestellabschluss), mit der Operation verbunden durch einen Auslöser, *vor* oder *nach* ihr. Sie
schreiben das Ereignis **Log publishing** (`explogpublish`): Es schreibt je veröffentlichtem Objekt eine Zeile nach
`var/log/expexample.log` und lässt das Veröffentlichen weiterlaufen. Danach bauen Sie damit einen Workflow und
verbinden ihn mit *Nach dem Veröffentlichen von Inhalten*.

Voraussetzungen: die Erweiterung aus [Ein Erweiterungsgerüst](erweiterungsgeruest.md), aktiviert; die Richtlinien
`workflow` und `trigger` (ein Administrator).

## 1. Wo der Kernel sucht

`eZWorkflowType::loadAndRegisterType()` bindet `<directory>/<group>/<type>/<type>type.php` aus `workflow.ini
[EventSettings] RepositoryDirectories[]` und aus `eventtypes/` jeder Erweiterung in `ExtensionDirectories[]` ein. Die
Gruppe eines Ereignisses ist `event`; `AvailableEventTypes[]` nennt `<group>_<type>`.
`extension/expexample/settings/workflow.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[EventSettings]
# Look for eventtypes/event/<type>/<type>type.php in this extension
ExtensionDirectories[]=expexample
AvailableEventTypes[]=event_explogpublish

*/ ?>
```

## 2. Die Ereignisklasse

`extension/expexample/eventtypes/event/explogpublish/explogpublishtype.php`:

```php
<?php
/**
 * The workflow event "Log publishing": writes one line to var/log/expexample.log for every object published
 * through a workflow that contains the event, then lets the operation carry on.
 *
 * @copyright Copyright (C) 2026 Example Company
 * @license GNU General Public License v2.0 (or any later version)
 * @package expexample
 */

class expLogPublishType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'explogpublish';

    public function __construct()
    {
        parent::__construct( self::WORKFLOW_TYPE_STRING, ezpI18n::tr( 'extension/expexample', 'Log publishing' ) );
        // Offered only for these triggers: content/publish, before and after
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'before', 'after' ) ) ) );
    }

    /**
     * @param eZWorkflowProcess $process
     * @param eZWorkflowEvent $event
     * @return int an eZWorkflowType::STATUS_* constant
     */
    public function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        $objectID = isset( $parameters['object_id'] ) ? (int)$parameters['object_id'] : 0;
        $version  = isset( $parameters['version'] ) ? (int)$parameters['version'] : 0;

        $object = eZContentObject::fetch( $objectID );
        if ( !$object instanceof eZContentObject )
            return eZWorkflowType::STATUS_WORKFLOW_CANCELLED;

        eZLog::write( sprintf( 'published object %d version %d (%s) by user %d',
                               $objectID, $version, $object->attribute( 'class_identifier' ),
                               eZUser::currentUserID() ),
                      'expexample.log' );

        // ACCEPTED: the next event of the workflow runs, then the operation itself
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( expLogPublishType::WORKFLOW_TYPE_STRING, 'expLogPublishType' );
```

- `parameter_list` enthält die Parameter der Operation; für `content/publish` sind das `object_id` und `version`
  (`kernel/content/operation_definition.php`).
- `execute()` gibt eine der Konstanten `eZWorkflowType::STATUS_*` zurück:

| Status | Bedeutung |
|---|---|
| `STATUS_ACCEPTED` | erledigt; das nächste Ereignis läuft, danach die Operation |
| `STATUS_REJECTED` | die Operation findet nicht statt |
| `STATUS_DEFERRED_TO_CRON`, `STATUS_DEFERRED_TO_CRON_REPEAT` | warten; der Workflow-Cronjob führt das Ereignis später erneut aus (so arbeitet die Freigabe) |
| `STATUS_FETCH_TEMPLATE`, `STATUS_REDIRECT` | eine Seite anzeigen oder den Benutzer anderswohin schicken, danach zurückkehren |
| `STATUS_WORKFLOW_CANCELLED` | den Workflow abbrechen |

Halten Sie ein Ereignis auf der Seite *vor* kurz: Der Redakteur wartet darauf. Langsame Arbeit (ein entfernter Aufruf,
eine E-Mail) gehört *nach* das Veröffentlichen oder wird an den Cronjob übergeben.

## 3. Seine Templates

Die Workflow-Seiten zeigen jedes Ereignis mit `event_view_gui` und `event_edit_gui`, die nach
`workflow/eventtype/view/<workflow type string>.tpl` und `.../edit/...` (`event_explogpublish.tpl`) suchen. Dieses
Ereignis hat keine Einstellungen, deshalb sind beide kurz. In `extension/expexample/design/standard/templates/`:

`workflow/eventtype/view/event_explogpublish.tpl`:

```tpl
{* The event in the workflow view: it has no settings to show *}
<p>{'Writes a line to var/log/expexample.log for each published object.'|i18n( 'extension/expexample' )}</p>
```

`workflow/eventtype/edit/event_explogpublish.tpl`:

```tpl
{* The event in the workflow edit form: no settings *}
<p>{'This event has no settings.'|i18n( 'extension/expexample' )}</p>
```

Ein Ereignis mit Einstellungen speichert sie über `fetchHTTPInput()` und `validateHTTPInput()` in der Zeile von
`ezworkflow_event` (`data_int1`, `data_text1` ...); `kernel/classes/workflowtypes/event/ezwaituntildate/` ist ein
vollständiges Beispiel.

## 4. Aktivieren

```bash
php -l extension/expexample/eventtypes/event/explogpublish/explogpublishtype.php
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezcache.php --clear-id=template-override --allow-root-user
```

Starten Sie Velocity neu oder [rollen Sie aus](velocity-deployment.md), wenn Velocity die Website ausliefert.

1. **Setup > Workflows**: Öffnen Sie eine Gruppe (oder legen Sie eine an), **Neuer Workflow**, nennen Sie ihn
   *Log publishing*.
2. Wählen Sie den Ereignistyp *Ereignis / Log publishing*, **Ereignis hinzufügen**, **OK**.
3. **Setup > Auslöser**: Wählen Sie in der Zeile **Nach dem Veröffentlichen von Inhalten** (`content/publish`, after)
   *Log publishing*, **Änderungen anwenden**. Ein Workflow je Auslöser: Um diesen neben einem anderen auszuführen,
   legen Sie beide in einen Workflow mit einem *Multiplexer*-Ereignis.

## 5. Prüfen

Veröffentlichen Sie ein beliebiges Objekt, dann:

```bash
tail -n 3 var/log/expexample.log
```

Erwartet: eine Zeile `published object <id> version <n> (<class identifier>) by user <id>`. Mit `VarDir` je Website
und `UseGlobalLogDir=disabled` liegt das Log stattdessen im Log-Verzeichnis der Website.

Für diese Seite geprüft (2026-10-18): `php -l` auf die Klasse; `bin/php/eztemplatecheck.php` meldet beide Templates
als gültig (Syntax); die Basisklasse, die Statuswerte und die Suche nach Templates sind die aus
`kernel/classes/ezworkfloweventtype.php`, `kernel/classes/ezworkflowtype.php` und
`kernel/common/eztemplateautoload.php`. Das Ereignis wurde für diese Seite nicht gegen eine Datenbank ausgeführt.

## Häufige Fehler

| Symptom | Ursache |
|---|---|
| Der Typ fehlt in der Liste der Ereignistypen | `AvailableEventTypes[]` fehlt `event_explogpublish`, oder `ExtensionDirectories[]` fehlt; die Debug-Ausgabe nennt die durchsuchten Verzeichnisse |
| *Workflow type not found* im Log | der Ordner- oder Dateiname weicht vom Typ-String ab (`explogpublish/explogpublishtype.php`) |
| Das Veröffentlichen hängt oder bleibt ein Entwurf | `execute()` hat nichts zurückgegeben (`STATUS_NONE`) oder `REJECTED`; sehen Sie unter **Setup > Workflow-Prozesse** nach |
| Der Workflow läuft für den Auslöser nicht | er ist mit *vor* verbunden, wo Sie *nach* erwartet haben, oder ein anderer Workflow belegt den Auslöser |

## Abgeleitet aus

- `kernel/classes/ezworkflowtype.php` (`loadAndRegisterType()`, die Konstanten `STATUS_*`, `setTriggerTypes()`)
- `kernel/classes/ezworkfloweventtype.php`, `kernel/classes/workflowtypes/event/ezfinishuserregister/ezfinishuserregistertype.php`
- `kernel/common/eztemplateautoload.php` (`event_view_gui`, `event_edit_gui`), `settings/workflow.ini`
- `doc/eng-us/guides/workflows.md`, Abschnitt 3

## Verwandte Seiten

- [Kochbuch](README.md)
- [Workflows: Freigaben, Auslöser und die Prozesse, die warten](../../anleitungen/workflows.md)
- [Workflow-Ereignisse](../workflow-ereignisse/README.md): die Ereignistypen des Kernels
- [Ein Cronjob](cronjob.md): Der Workflow-Cronjob setzt zurückgestellte Ereignisse fort
- [RAD-Werkzeuge](../../funktionen/6.0/rad-werkzeuge.md): `/setup/workflowevent` schreibt ein Ereignisgerüst
- Die Programmierregeln und der Musterkatalog: [die Standards](../../standards/README.md)
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite darf frei kopiert, übersetzt und verbessert
  werden.
