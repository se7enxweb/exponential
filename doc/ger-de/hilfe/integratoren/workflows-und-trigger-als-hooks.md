---
title: Workflows und Trigger als Integrations-Hooks
audience: integrator, developer
category: integration
order: 170
summary: Workflow-Ereignisse an Triggern als "beim Veröffentlichen"-Hook einer Integration verwenden - Trigger davor und danach, ein Outbox-Ereignis, das Änderungen für einen Cronjob vormerkt, eine externe Prüfung, die eine Veröffentlichung annimmt oder ablehnt, die Verschiebung an den Cronjob, welche Operationen sich auslösen lassen und die Fallstricke.
keywords: Workflow, Trigger, Workflow-Ereignis, Outbox, Hook beim Veröffentlichen, Push-Integration, workflow, trigger, workflow event, eZWorkflowEventType, content publish after, content publish before, outbox, STATUS_ACCEPTED, STATUS_REJECTED, STATUS_DEFERRED_TO_CRON, AvailableOperationList, eZOperationHandler, on publish hook, push integration
type: help
---
# Workflows und Trigger als Integrations-Hooks

Diese Seite richtet sich an Entwickler von Integrationen, die Exponential einem anderen System mitteilen lassen
müssen, **wenn etwas geschieht**, oder ein anderes System fragen müssen, **bevor etwas geschieht**. Der Kernel führt
Operationen (Veröffentlichen, Verschieben, Verbergen, Bestellabschluss ...) über `eZOperationHandler` aus; jede
Operation kann einen **Trigger** davor und danach haben, und ein Website-Ersteller verbindet einen **Workflow** mit
einem Trigger. Ein Workflow ist eine Liste von **Ereignissen**, und ein Ereignistyp ist eine Klasse, die Ihre
Erweiterung bereitstellt. Die Kapitel für Entwickler sind
[Workflow-Ereignistypen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/workflow-ereignistypen.md) und
[Trigger und Workflows für Entwickler](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/trigger-und-workflows-fuer-entwickler.md);
die Administration beschreibt [Workflows](../../anleitungen/workflows.md).

## Inhalt

1. [Davor oder danach](#davor-oder-danach)
2. [Das Outbox-Muster: schnell vermerken, später senden](#das-outbox-muster-schnell-vermerken-später-senden)
3. [Eine externe Prüfung vor dem Veröffentlichen](#eine-externe-prüfung-vor-dem-veröffentlichen)
4. [Welche Operationen sich auslösen lassen](#welche-operationen-sich-auslösen-lassen)
5. [Das Ereignis verbinden](#das-ereignis-verbinden)
6. [Fallstricke](#fallstricke)

## Davor oder danach

| Trigger | Verwendung | Sinnvolle Status |
|---|---|---|
| `content / publish / before` | Ein anderes System oder eine Person fragen, ob diese Version veröffentlicht werden darf | `STATUS_ACCEPTED`, `STATUS_REJECTED`, `STATUS_DEFERRED_TO_CRON` |
| `content / publish / after` | Anderen Systemen mitteilen, dass eine Version veröffentlicht wurde (Suche, CDN, CRM, Archiv) | `STATUS_ACCEPTED` |
| `shop / checkout` und `shop / confirmorder` (davor) | Eine Bestellung an ein ERP übergeben, den Bestand prüfen, Zahlung | `STATUS_ACCEPTED`, `STATUS_REJECTED`, Umleitungen für die Zahlung |

Ein Ereignis, das nur reagiert und nie wartet oder ablehnt, kann auch ein **Event-Listener** sein
([Event-Listener für Integrationen](event-listener-fuer-integrationen.md)), der keine Workflow-Konfiguration braucht.
Ein Workflow ist die richtige Wahl, wenn Website-Ersteller den Hook in der Administration sehen und schalten können
müssen.

## Das Outbox-Muster: schnell vermerken, später senden

Das Ereignis läuft innerhalb der Veröffentlichungsanfrage des Redakteurs. Ein langsamer oder fehlschlagender
entfernter Aufruf dort lässt Redakteure warten oder bricht das Veröffentlichen. Das Ereignis **vermerkt** die Änderung
daher nur; ein Cronjob-Teil **sendet** sie, mit Wiederholungen.

`extension/myintegration/settings/workflow.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[EventSettings]
ExtensionDirectories[]=myintegration
AvailableEventTypes[]=event_myintegrationoutbox

*/ ?>
```

`extension/myintegration/eventtypes/event/myintegrationoutbox/myintegrationoutboxtype.php`:

```php
<?php
/**
 * Workflow event "Queue for the CRM": records each published version in myintegration_outbox.
 * Use it on the trigger content / publish / after. The cronjob part myintegration_outbox sends the rows.
 */
class myIntegrationOutboxType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'myintegrationoutbox';

    public function __construct()
    {
        parent::__construct( self::WORKFLOW_TYPE_STRING,
                             ezpI18n::tr( 'extension/myintegration', 'Queue for the CRM' ) );
        $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) );
    }

    public function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        $objectID = isset( $parameters['object_id'] ) ? (int)$parameters['object_id'] : 0;
        $version = isset( $parameters['version'] ) ? (int)$parameters['version'] : 0;
        if ( $objectID > 0 )
        {
            $db = eZDB::instance();
            $db->query( 'INSERT INTO myintegration_outbox ( object_id, version, created, sent )'
                      . ' VALUES ( ' . $objectID . ', ' . $version . ', ' . time() . ', 0 )' );
        }
        // never block publishing because of the integration
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( myIntegrationOutboxType::WORKFLOW_TYPE_STRING, 'myIntegrationOutboxType' );
```

Die Tabelle `myintegration_outbox` gehört zum Schema Ihrer Erweiterung
([Datenbankschema und dba-Dateien](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/datenbankschema-und-dba-dateien.md)).
Der Cronjob-Teil liest nicht gesendete Zeilen, sendet sie und markiert sie als gesendet; ein fehlgeschlagener Versand
bleibt ungesendet und wird beim nächsten Lauf erneut versucht
([Einen Export oder ausgehenden Abgleich schreiben](einen-export-oder-ausgehenden-abgleich-schreiben.md)).

## Eine externe Prüfung vor dem Veröffentlichen

An `content / publish / before` kann ein Ereignis ein anderes System fragen (eine rechtliche Prüfung, einen
Produktstamm) und entscheiden:

| Rückgabe | Wirkung |
|---|---|
| `STATUS_ACCEPTED` | Das nächste Ereignis läuft, dann geht die Veröffentlichung weiter |
| `STATUS_REJECTED` | Die Veröffentlichung findet nicht statt |
| `STATUS_DEFERRED_TO_CRON` | Der Prozess wartet; der Workflow-Cronjob (`workflow.php`, Teil `frequent`) führt das Ereignis später erneut aus, und wenn es schließlich angenommen zurückgibt, schließt der Cronjob die Veröffentlichung ab |

Das Verschieben ist die sichere Wahl, wenn das andere System langsam oder asynchron antwortet: Speichern Sie die
Anfrage beim ersten Lauf, und schauen Sie bei späteren Läufen nur auf den **gespeicherten Zustand** (die eingetroffene
Antwort), nie auf die Anfrage, die den Prozess gestartet hat. Ein Ereignis, das verschiebt, muss beliebig oft laufen
dürfen. Redakteure sehen die wartende Version, und Administratoren sehen den Prozess unter
**Setup > Workflow-Prozesse**.

## Welche Operationen sich auslösen lassen

`settings/workflow.ini [OperationSettings] AvailableOperationList[]` entscheidet, welche Operationen unter
**Setup > Trigger** erscheinen. Ausgeliefert: `content_publish`, `before_shop_confirmorder`, `shop_checkout`,
`shop_addtobasket`, `shop_updatebasket`. Die Datei führt weitere auskommentiert auf (`content_move`, `content_hide`,
`content_addlocation`, `content_removelocation`, `content_sort`, `content_swap` ...). Um eine anzubieten, ergänzen Sie
sie in Ihrer Erweiterung:

```ini
[OperationSettings]
AvailableOperationList[]=content_hide
```

Der Kernel warnt, dass sich der Code hinter den zusätzlichen Operationen ohne die üblichen Kompatibilitätsregeln ändern
kann; testen Sie nach jedem Upgrade ([Upgrades ohne defekte Integrationen](upgrades-ohne-defekte-integrationen.md)).

Eigener Code, der eine solche Aktion ausführt, sollte die Operation aufrufen, damit die Trigger laufen:

```php
<?php
$result = eZOperationHandler::execute( 'content', 'publish',
                                       array( 'object_id' => $objectID, 'version' => $versionNumber ) );
```

## Das Ereignis verbinden

1. Erzeugen Sie die Autoloads neu (`php bin/php/ezpgenerateautoloads.php -e`) und leeren Sie den INI-Cache.
2. **Setup > Workflows**: Legen Sie in einer Gruppe einen Workflow an und fügen Sie das Ereignis "Queue for the CRM"
   hinzu.
3. **Setup > Trigger** (`/trigger/list`): Verbinden Sie den Workflow mit `content / publish / after`.
4. Veröffentlichen Sie ein Testobjekt und prüfen Sie, dass eine Zeile in Ihrer Outbox angekommen ist.

Ein Trigger nimmt einen Workflow auf (die Tabelle `eztrigger` ist eindeutig je Modul, Operation und davor oder
danach). Liegt bereits ein Freigabe-Workflow auf `content / publish / before`, fügen Sie Ihr Ereignis diesem Workflow
hinzu oder verwenden Sie das Multiplexer-Ereignis, um mehrere Workflows auszuführen.

## Fallstricke

- **Auch Importe veröffentlichen.** Mit `eZContentFunctions::createAndPublishObject()` angelegte Inhalte durchlaufen
  die Veröffentlichungsoperation und ihre Trigger. Ein Massenimport füllt daher Ihre Outbox; entscheiden Sie, ob das
  gewollt ist, und filtern Sie sonst in `execute()` nach Ersteller oder Klasse.
- **Freigabe am selben Trigger.** Mit einem Ereignis *Approve* vor dem Veröffentlichen warten importierte Versionen auf
  Freigabe. Geben Sie dem Dienstkonto des Imports eine Sektion oder Klasse, die die Freigabe nicht abdeckt, oder lassen
  Sie es anderswo veröffentlichen.
- **Geben Sie in `execute()` nie etwas aus**, und fangen Sie Ausnahmen ab: Eine Ausnahme bricht das Veröffentlichen
  für den Redakteur ab.
- **Velocity.** Eine neue Klasse eines Ereignistyps braucht `exp:velocity restart` (oder `exp:velocity deploy`), bevor
  die Worker von Velocity sie kennen.

## Verwandte Seiten

- [Workflows](../../anleitungen/workflows.md)
- [Workflow-Ereignistypen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/workflow-ereignistypen.md)
- [Die API von eZWorkflowType](../erweiterungsentwicklung/teil-8-referenz/ezworkflowtype-api.md)
- [Einen Workflow-Ereignistyp schreiben](../ersteller/anwendungsentwickler/einen-workflow-ereignistyp-schreiben.md)
- [Ausgehende Webhooks aus dem Audit-Protokoll](ausgehende-webhooks-aus-dem-audit-trail.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
