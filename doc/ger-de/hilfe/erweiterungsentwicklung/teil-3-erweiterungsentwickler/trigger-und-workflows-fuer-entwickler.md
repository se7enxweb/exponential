---
title: Trigger und Workflows für Entwickler
audience: developer, app-creator
category: extension-development
order: 190
summary: Wie Operationen, Trigger und Workflows zusammenhängen - Operationsdefinitionen, weitere Operationen auslösbar machen, eine Operation aus PHP ausführen und was mit zurückgestellten Prozessen geschieht.
keywords: Trigger, Operation, Workflow-Prozess, Workflow-Cronjob, zurückgestellt, Veröffentlichen, trigger, operation, operation_definition.php, eZOperationHandler, execute, AvailableOperationList, content_publish, pre_publish, post_publish, workflow process, workflow cronjob, memento
type: help
---
# Trigger und Workflows für Entwickler

Teil 3, Kapitel 19. Workflow-Ereignisse laufen, weil eine **Operation** des Kernels einen **Trigger** passiert. Dieses
Kapitel erklärt diese Mechanik aus Sicht des Entwicklers: welche Operationen es gibt, wie Sie weitere davon auslösbar
machen und wie Sie selbst eine Operation ausführen, damit auch die Workflows laufen, die die Builder eingerichtet haben.

## Operationen

Eine Operation ist eine benannte Folge von Schritten, die der Kernel über `eZOperationHandler`
(`lib/ezutils/classes/ezoperationhandler.php`) ausführt. Jedes Modul mit Operationen definiert sie in
`kernel/<module>/operation_definition.php`:

| Modul | Operationen |
|---|---|
| `content` | `publish`, `read`, `hide`, `move`, `swap`, `sort`, `delete`, `addlocation`, `removelocation`, `updatemainassignment`, `updatepriority`, `updatesection`, `updateobjectstate`, `updateinitiallanguage`, `updatealwaysavailable`, `removetranslation`, `translationavailable`, `createnodefeed`, `removenodefeed` |
| `shop` | `addtobasket`, `updatebasket`, `checkout`, `confirmorder` |
| `user` | `register`, `activation`, `password`, `forgotpassword`, `preferences`, `setsettings` |

## Trigger

Jede Operation kann einen Trigger **davor** und **danach** haben (intern `pre_<operation>` und `post_<operation>`). Ein
Builder verbindet einen Workflow unter **Setup > Trigger** mit einem Trigger. Welche Operationen dort erscheinen,
bestimmt `workflow.ini [OperationSettings] AvailableOperationList[]`; die ausgelieferte Liste enthält `content_publish`
und die Shop-Operationen, und die Datei führt die übrigen auskommentiert auf. Um zum Beispiel Workflows rund um das
Verschieben von Inhalten anzubieten:

```ini
# extension/mysite_hello/settings/workflow.ini.append.php
[OperationSettings]
AvailableOperationList[]=content_move
```

Der Kommentar des Kernels zu dieser Liste warnt, dass der Code hinter den zusätzlichen Operationen ohne die üblichen
Kompatibilitätsregeln „bei Bedarf geändert werden kann“; testen Sie nach jedem Upgrade.

## Eine Operation aus PHP ausführen

Wenn Ihr Code etwas tut, für das es eine Operation gibt, rufen Sie die Operation auf statt der Methoden auf niedriger
Ebene, damit Trigger, Workflows, Caches und Benachrichtigungen alle laufen:

```php
<?php
$result = eZOperationHandler::execute( 'content', 'publish',
                                       array( 'object_id' => $objectID, 'version' => $versionNumber ) );
```

Die Signatur lautet `execute( $moduleName, $operationName, $operationParameters, $lastTriggerName = null, $useTriggers = true )`.
`$result['status']` sagt Ihnen, ob die Operation abgeschlossen wurde (`eZModuleOperationInfo::STATUS_CONTINUE`), von
einem Workflow angehalten wird oder abgebrochen wurde. Zum Anlegen von Inhalten erledigt
`eZContentFunctions::createAndPublishObject()` die ganze Arbeit (siehe
[Inhalte aus PHP anlegen, veröffentlichen und ändern](../teil-8-referenz/inhaltsfunktionen-aus-php.md)).

## Was geschieht, wenn ein Ereignis wartet

Wenn ein Ereignis `STATUS_DEFERRED_TO_CRON` zurückgibt, speichert der Kernel den Prozess und ein **Memento** der
zurückgehaltenen Operation. Der Workflow-Cronjob (`cronjobs/workflow.php`, Teil `frequent`) führt das Ereignis erneut
aus; wenn es schließlich `STATUS_ACCEPTED` zurückgibt, schließt der Cronjob die Operation anhand des Mementos ab. Die
Administration zeigt wartende Prozesse unter **Setup > Workflow-Prozesse**. Die Einzelheiten stehen in
[Workflows](../../../anleitungen/workflows.md).

Daher gilt: Ein Ereignis, das zurückstellt, muss gefahrlos mehrmals laufen können und darf nur anhand gespeicherten
Zustands entscheiden, nie anhand des Requests, der es gestartet hat.

## Workflows ohne die Administration

Ein Installationsskript kann Workflows und Trigger mit den Klassen des Kernels anlegen (`eZWorkflow`, `eZWorkflowEvent`,
`eZTrigger`), aber sie über die Administration einzurichten und in einem Paket zu exportieren, ist meist leichter zu
pflegen. Dokumentieren Sie lieber die zwei Klicks, die ein Builder machen muss.

## Verwandte Seiten

- Zurück: [Workflow-Ereignistypen](workflow-ereignistypen.md)
- Weiter: [Cronjob-Teile](cronjob-teile.md)
- [Workflows für Builder](../teil-2-builder/workflows-fuer-builder.md)
- [Events, Listener und Filter](ereignisse-und-filter.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
