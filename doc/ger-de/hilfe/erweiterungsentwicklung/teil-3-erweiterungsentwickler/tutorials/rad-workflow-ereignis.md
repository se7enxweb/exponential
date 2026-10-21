---
title: "Mit RAD: ein Workflow-Ereignis"
audience: developer, app-creator
category: extension-development
order: 50
summary: Der Assistent für Workflow-Ereignisse schreibt einen Ereignistyp, der jede Veröffentlichung protokolliert - Trigger und Status, die erzeugte Klasse und die Templates und die eine Methode, die Sie ausfüllen.
keywords: RAD, Assistent für Workflow-Ereignisse, Workflow-Ereignis, Trigger, Status, Ereigniseinstellungen, workflow event wizard, setup/workflowevent, workflow event, eZWorkflowEventType, triggers, statuses, STATUS_ACCEPTED, event settings, execute, content/publish/after
type: help
---
# Mit RAD: ein Workflow-Ereignis

Tutorial 5. Der **Assistent für Workflow-Ereignisse** (`/setup/workflowevent`) schreibt einen Workflow-Ereignistyp: die
Klasse, ihre Registrierung, die Templates, die der Workflow-Editor zeigt, und auf Wunsch Einstellungen pro Ereignis, die
in den Spalten gespeichert werden, die `eZWorkflowEvent` bereitstellt. Die Ausgabe unten wurde vom Generator des
Assistenten (`expWorkflowEventWizard::files()`) erzeugt; jede PHP-Datei hat `php -l` bestanden, und die erzeugte Klasse
wurde mit geladenem Kernel per `eZWorkflowType::createType()` erzeugt und mit `isAllowed()` befragt.

## 1. Das Formular

| Feld | Wert |
|---|---|
| Name der Extension | `mysite_hello` |
| Ereignisname | `mysitepublishlog` |
| Wie es für Redakteure heißt | `Log publishing` |
| Wann es läuft | `content / publish / after` |
| Womit es antworten kann | `STATUS_ACCEPTED` |

Der Assistent führt nur Trigger auf, die es gibt (aus den Operationsdefinitionen), und behält `STATUS_ACCEPTED` immer
bei: Wie sein Code sagt, „würde ein Ereignis, das nicht sagen kann, dass es fertig ist, jeden Workflow aufhängen, in den
es eingebaut wird“. Einstellungen pro Ereignis werden, falls Sie welche hinzufügen, auf die vier Ganzzahl- und fünf
Textspalten von `eZWorkflowEvent` abgebildet; eine, für die keine Spalte mehr frei ist, wird verworfen, „statt sich
stillschweigend eine Spalte zu teilen“.

## 2. Die Dateien

```text
.gitignore
LICENSE
README.md
composer.json
design/standard/templates/workflow/eventtype/edit/event_mysitepublishlog.tpl
design/standard/templates/workflow/eventtype/view/event_mysitepublishlog.tpl
eventtypes/event/mysitepublishlog/mysitepublishlogtype.php
extension.xml
ezinfo.php
settings/design.ini.append.php
settings/workflow.ini.append.php
```

Die Registrierung:

```ini
[EventSettings]
ExtensionDirectories[]=mysite_hello
AvailableEventTypes[]=event_mysitepublishlog
```

Die Klasse (ohne Kommentare):

```php
<?php
class mysitepublishlogType extends eZWorkflowEventType
{
    const WORKFLOW_TYPE_STRING = 'mysitepublishlog';

    public function __construct()
    {
        parent::__construct( self::WORKFLOW_TYPE_STRING,
                             ezpI18n::tr( 'kernel/workflow/event', 'Log publishing' ) );
        $this->setTriggerTypes( array(
            'content' => array(
                'publish' => array( 'after' ) ) ) );
    }

    function execute( $process, $event )
    {
        $parameters = $process->attribute( 'parameter_list' );
        if ( isset( $parameters['object_id'] ) )
        {
            $object = eZContentObject::fetch( $parameters['object_id'] );
            if ( !$object instanceof eZContentObject )
            {
                eZDebug::writeNotice( 'Object ' . $parameters['object_id'] . ' is gone; nothing to do.',
                                      __METHOD__ );
                return eZWorkflowType::STATUS_ACCEPTED;
            }
        }
        return eZWorkflowType::STATUS_ACCEPTED;
    }
}

eZWorkflowEventType::registerEventType( mysitepublishlogType::WORKFLOW_TYPE_STRING, 'mysitepublishlogType' );
```

Die Templates liegen in `design/standard`, daher findet der Workflow-Editor sie in jedem Administrationsdesign.

## 3. execute() ausfüllen

Fügen Sie die Arbeit dort ein, wo das Objekt bekannt ist:

```php
eZLog::write( sprintf( 'Published object %d version %d: %s',
                       (int)$parameters['object_id'], (int)$parameters['version'], $object->attribute( 'name' ) ),
              'mysite_hello.log' );
```

## 4. Einschalten und verwenden

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_hello global
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

**Setup > Workflows**: Legen Sie einen Workflow mit dem Ereignis „Log publishing“ an; **Setup > Trigger**: Verbinden Sie
ihn mit `content` / `publish` / after. Veröffentlichen Sie einen Artikel und lesen Sie `var/log/mysite_hello.log`.

## Verwandte Seiten

- Zurück: [Mit RAD: ein Modul mit Views](rad-modul.md)
- Weiter: [Mit RAD: eine Design-Erweiterung](rad-design-erweiterung.md)
- [Workflow-Ereignistypen](../workflow-ereignistypen.md), [Von Hand: ein Workflow-Ereignis](von-hand-workflow-ereignis.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
