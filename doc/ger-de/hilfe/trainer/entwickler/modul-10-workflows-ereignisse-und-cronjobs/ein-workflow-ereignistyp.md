---
title: "Lektion 10.2: Ein eigener Workflow-Ereignistyp"
audience: trainer, developer
category: training
order: 102
summary: Die Teilnehmenden schreiben in ihrer Erweiterung den Ereignistyp Log publishing, registrieren ihn in workflow.ini, ergänzen seine Templates zum Bearbeiten und Anzeigen, setzen ihn in einem Workflow auf den Trigger nach dem Veröffentlichen und lesen die Logzeile, die er schreibt. 60 Minuten.
keywords: entwickler, workflow-ereignistyp, ereignistyp, eZWorkflowEventType, execute, STATUS_ACCEPTED, STATUS_DEFERRED_TO_CRON, STATUS_WORKFLOW_CANCELLED, registerEventType, setTriggerTypes, workflow.ini, EventSettings, ExtensionDirectories, AvailableEventTypes, event_-templates, eZLog, developers course, workflow event type, event_ templates
type: help
---
# Lektion 10.2: Ein eigener Workflow-Ereignistyp

Ein Ereignistyp ist Code, der als Schritt eines Workflows läuft: etwas prüfen, etwas ändern oder warten. In dieser
Lektion schreiben die Teilnehmenden den Ereignistyp **Log publishing**, der bei jeder veröffentlichten Version eine
Zeile in eine Logdatei schreibt. Er ist klein genug, um im Unterricht fertig zu werden, und zeigt jeden Teil, den ein
echter Ereignistyp braucht.

> **Hinweis**
> Verwenden Sie Ihre eigene Schulungsinstallation: Die Übung verbindet einen Workflow mit dem Veröffentlichen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 10: Workflows, Ereignisse und Cronjobs](README.md) |
| Dauer | 60 Minuten (20 Präsentation, 40 Übung) |
| Niveau | Experten |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 10.1](workflows-und-trigger.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine Ereignistyp-Klasse schreiben, die `eZWorkflowEventType` erweitert und `execute()` implementiert;
- den richtigen Status zurückgeben: `STATUS_ACCEPTED`, `STATUS_REJECTED`, `STATUS_DEFERRED_TO_CRON` oder
  `STATUS_WORKFLOW_CANCELLED`;
- mit `setTriggerTypes()` die Trigger einschränken, mit denen das Ereignis verwendet werden darf;
- ihn in `workflow.ini [EventSettings]` registrieren und ihm Templates zum Bearbeiten und Anzeigen geben;
- ihn in einem Workflow auf dem Trigger nach dem Veröffentlichen verwenden und seine Wirkung prüfen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: `kernel/classes/ezworkflowtype.php` (Status und Laden), ein Ereignistyp des Kernels wie
`kernel/classes/workflowtypes/event/ezwaituntildate/ezwaituntildatetype.php` und der Ereignistyp einer Erweiterung,
zum Beispiel `extension/swark/eventtypes/event/autopriority/`, falls installiert.

1. **Wo der Kernel sucht** (4 Minuten). Für den Typ-String `event_mysitelogpublish` sucht `eZWorkflowType` in jeder
   Erweiterung aus `[EventSettings] ExtensionDirectories[]` nach
   `eventtypes/event/mysitelogpublish/mysitelogpublishtype.php`. Der Typname enthält keinen Unterstrich: Der Teil
   nach `event_` ist der Ordner.
2. **Die Klasse** (6 Minuten). Der Konstruktor ruft `parent::__construct( <type>, <name> )` auf und kann die Trigger
   einschränken: `setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) )`.
   `execute( $process, $event )` liest `$process->attribute( 'parameter_list' )` (beim Veröffentlichen: `object_id`
   und `version`) und gibt einen Status zurück. Die Datei endet mit
   `eZWorkflowEventType::registerEventType( <type>, <class> )`.
3. **Status** (4 Minuten). `STATUS_ACCEPTED` macht weiter; `STATUS_REJECTED` stoppt die Operation;
   `STATUS_DEFERRED_TO_CRON` wartet auf den Workflow-Cronjob; `STATUS_WORKFLOW_CANCELLED` beendet den Workflow.
4. **Templates** (2 Minuten). Der Workflow-Editor bindet `workflow/eventtype/edit/event_<type>.tpl` und
   `view/event_<type>.tpl` ein; ein Ereignis ohne Einstellungen bringt zwei fast leere Templates mit.
5. **Davor oder danach** (2 Minuten). Protokollieren ist eine *After*-Aufgabe. Prüfungen, die das Veröffentlichen
   stoppen dürfen, sind *Before*-Aufgaben.

### Häufige Fragen

**Kann das Ereignis im Workflow-Editor Einstellungen haben?** Ja: Implementieren Sie `fetchHTTPInput()` und
speichern Sie die Werte in den `data_*`-Feldern des Ereignisses, wie es die Ereignistypen des Kernels tun; das
Bearbeitungs-Template zeigt das Formular.

**Kann es auf etwas warten?** Geben Sie `STATUS_DEFERRED_TO_CRON` (oder `_REPEAT`) zurück; der Workflow-Cronjob führt
es erneut aus.

**Gibt es einen Assistenten?** **Setup** > **RAD**, der **Assistent für Workflow-Ereignisse** (`/setup/workflowevent`),
schreibt Ereignistypen und Trigger.

### Stolperfallen

- **Ein Unterstrich im Typnamen.** `event_mysite_log` würde im Ordner `mysite` suchen; halten Sie den Namen bei einem
  Wort.
- **Langsame Arbeit in einem Before-Ereignis.** Die Redaktion wartet darauf. Verschieben Sie schwere Arbeit in den
  Cronjob oder einen Content-Job.
- **Exceptions.** Eine nicht abgefangene Exception in `execute()` macht das Veröffentlichen für alle kaputt; fangen
  Sie sie ab, protokollieren Sie sie und geben Sie einen Status zurück.

## Praktische Übung

Auf Ihrer Schulungsinstallation, in `extension/mysite_hello`.

1. Legen Sie `eventtypes/event/mysitelogpublish/mysitelogpublishtype.php` an:

   ```php
   <?php
   /**
    * Workflow event "Log publishing": writes one line per published version to var/log/mysite_publish.log.
    */
   class mysiteLogPublishType extends eZWorkflowEventType
   {
       const WORKFLOW_TYPE_STRING = 'mysitelogpublish';

       public function __construct()
       {
           parent::__construct( self::WORKFLOW_TYPE_STRING, 'Log publishing' );
           $this->setTriggerTypes( array( 'content' => array( 'publish' => array( 'after' ) ) ) );
       }

       public function execute( $process, $event )
       {
           $parameters = $process->attribute( 'parameter_list' );
           $objectID = isset( $parameters['object_id'] ) ? (int)$parameters['object_id'] : 0;
           $version = isset( $parameters['version'] ) ? (int)$parameters['version'] : 0;

           $object = eZContentObject::fetch( $objectID );
           if ( !$object instanceof eZContentObject )
           {
               return eZWorkflowType::STATUS_WORKFLOW_CANCELLED;
           }

           eZLog::write( 'Published object ' . $objectID . ' version ' . $version
                         . ' (' . $object->attribute( 'class_identifier' ) . ')', 'mysite_publish.log' );
           return eZWorkflowType::STATUS_ACCEPTED;
       }
   }

   eZWorkflowEventType::registerEventType( mysiteLogPublishType::WORKFLOW_TYPE_STRING, 'mysiteLogPublishType' );
   ```

2. Registrieren Sie ihn in `settings/workflow.ini.append.php`:

   ```bash
   php bin/php/console exp:ini add workflow.ini/EventSettings/ExtensionDirectories mysite_hello extension:mysite_hello
   php bin/php/console exp:ini add workflow.ini/EventSettings/AvailableEventTypes event_mysitelogpublish extension:mysite_hello
   ```

3. Legen Sie die beiden Templates an, jedes mit der einzigen Zeile `{* No settings. *}`:
   `design/standard/templates/workflow/eventtype/edit/event_mysitelogpublish.tpl` und
   `design/standard/templates/workflow/eventtype/view/event_mysitelogpublish.tpl`.
4. Prüfen Sie die PHP-Datei mit `php -l`; leeren Sie den `ini`- und den Template-Cache.
5. Legen Sie unter **Setup** > **Workflows** den Workflow `Course: log publishing` an und fügen Sie das Ereignis
   **Log publishing** hinzu (es steht in der Liste der Ereignisse).
6. Verbinden Sie ihn unter **Setup** > **Trigger** mit dem Veröffentlichen von Inhalten, danach, und wenden Sie die
   Änderung an.
7. Veröffentlichen Sie einen Artikel in Ihrem Kursordner.
8. Lesen Sie das Log: `tail -3 var/log/mysite_publish.log`.
9. Entfernen Sie den Workflow wieder vom Trigger und committen Sie die Dateien.

## Erwartetes Ergebnis

- Schritt 5: **Log publishing** wird in der Ereignisliste des Workflow-Editors angeboten.
- Schritt 7: Der Artikel wird wie gewohnt veröffentlicht.
- Schritt 8: eine Zeile mit dem Datum, `Published object <ID> version <n> (article)`.
- Der Trainer prüft die Logzeile einer Person.

## Quiz

1. Wo sucht der Kernel nach dem Ereignistyp `event_mysitelogpublish` einer Erweiterung?
2. Welcher Status lässt die Operation weiterlaufen?
   A. `STATUS_ACCEPTED` B. `STATUS_REJECTED` C. `STATUS_DEFERRED_TO_CRON` D. `STATUS_WORKFLOW_CANCELLED`
3. Welche zwei Einstellungen registrieren den Ereignistyp einer Erweiterung?
4. Warum sollte der Typname keinen Unterstrich enthalten?
5. Warum ist Protokollieren eine *After*-Aufgabe?

<details>
<summary>Antworten</summary>

1. `extension/<ext>/eventtypes/event/mysitelogpublish/mysitelogpublishtype.php`, für Erweiterungen, die in
   `[EventSettings] ExtensionDirectories[]` stehen.
2. **A.** `STATUS_ACCEPTED`.
3. `workflow.ini [EventSettings] ExtensionDirectories[]` und `AvailableEventTypes[]=event_<type>`.
4. Der Typ-String wird an Unterstrichen getrennt; der Teil nach `event_` muss der Ordner- und Dateiname sein.
5. Es hält etwas fest, das geschehen ist, und darf das Veröffentlichen niemals zurückhalten.

</details>

## Weiterführende Literatur

- [Workflows: Freigaben, Trigger und die Prozesse, die warten](../../../../anleitungen/workflows.md).
- [RAD-Werkzeuge](../../../../funktionen/6.0/rad-werkzeuge.md): der Assistent für Workflow-Ereignisse.
- [Debug-Ausgabe und Logs für Entwickler](../../../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/debug-ausgabe-und-logs-fuer-entwickler.md):
  `eZLog::write()`.

## Verwandte Seiten

- [Modul 10: Workflows, Ereignisse und Cronjobs](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
