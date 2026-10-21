# Benachrichtigungen: Architektur, Erweiterung und Tests

Diese Anleitung richtet sich an Entwickler. Sie erklärt, wie das Benachrichtigungssystem aufgebaut ist, wie Sie einen eigenen Ereignistyp und Handler hinzufügen
(mit einem vollständigen Beispiel, das ausgeführt wurde), wie die Mails erzeugt und überschrieben werden, welche Einstellungen und Hooks Sie nutzen können und
wie Sie testen, ohne Mails zu versenden. Etwa 45 Minuten; Sie benötigen eine laufende Installation, eine Shell und PHP-Kenntnisse. Das
genaue Datenmodell und die Einstellungen stehen in [der Spezifikation](../spezifikationen/6.0/benachrichtigungen.md) und
[der INI-Referenz](../spezifikationen/6.0/benachrichtigungen-ini.md). Jeder Befehl und jede Ausgabe unten wurde auf einer Test-Installation
ausgeführt, deren Mail-Transport auf Dateien erzwungen war.

## 1. Architektur

```text
 publish operation --> eZNotificationEvent ('ezpublish')  \
 collaboration item -> eZNotificationEvent ('ezcollaboration') >-- eznotificationevent (status 0)
 a run ------------> eZNotificationEvent ('ezcurrenttime') /                |
                                                                             v
                       eZNotificationEventFilter::process( $ids )  --  for each pending event, one transaction:
                                                                             |
                    +-------------------------+------------------------+-----+------------------+
                    | ezsubtree               | ezcollaboration-       | ezgeneraldigest        | your handler
                    | (publish -> followers)  | notification           | (time event -> digests)|
                    +-----------+-------------+-----------+------------+-----------+------------+
                                v                         v                        v
                  eZNotificationCollection (subject, text)  +  eZNotificationCollectionItem (address, send_date)
                                |  send_date 0: send now             | send_date > 0: kept for the digest
                                v                                    v
                  eZMailNotificationTransport ('ezmail')  -->  eZMailTransport (site.ini Transport: sendmail, smtp or file)
```

| Teil | Wo | Aufgabe |
|---|---|---|
| Ereignisse | `kernel/classes/notification/eznotificationevent.php`, `eznotificationeventtype.php`, `event/<type>/<type>type.php` | Eine Zeile plus eine Typklasse, die sie füllt (`initializeEvent`) und ihren Inhalt liefert (`eventContent`) |
| Der Filter | `eznotificationeventfilter.php` | Lädt die Handler, übergibt jedes ausstehende Ereignis an jeden Handler, entfernt oder behält das Ereignis, räumt auf |
| Handler | `handler/<id>/<id>handler.php` | `handle( $event )`, `fetchHttpInput()`, `storeSettings()`, `cleanup()` und die Attribute, die die Einstellungs-Templates lesen |
| Regeln | `ezsubtreenotificationrule.php`, `ezcollaborationnotificationrule.php`, `ezgeneraldigestusersettings.php` | Wer was möchte |
| Sammlungen und Einträge | `eznotificationcollection.php`, `eznotificationcollectionitem.php` | Die Nachricht und ihre Empfänger; `send_date` entscheidet zwischen sofort und Zusammenfassung |
| Transporte | `eznotificationtransport.php`, `ezmailnotificationtransport.php` | `eZNotificationTransport::instance( 'ezmail' )->send( $addresses, $subject, $body, null, $parameters )` |
| Der Dienst | `kernel/classes/expnotificationservice.php`, `expnotificationjob.php` | Lauf, Plan (Probelauf), Status, Sperre, Laufprotokoll, Audit, Hintergrundjob |
| Einstiegspunkte | `cronjobs/notification.php`, `bin/php/notification*.php`, `kernel/notification/*.php` | Schlanke Stubs; der Code liegt in `kernel/private/classes/{cronjobs,commands,views}/` |

Die Laufsperre, das Laufprotokoll und das Audit-Ereignis sind in [der Spezifikation](../spezifikationen/6.0/benachrichtigungen.md#expnotificationservice) beschrieben.

## 2. Ein Ereignistyp und ein Handler: ein vollständiges Beispiel

Das Beispiel fügt den Ereignistyp `examplealert` hinzu: Code erzeugt ein Ereignis mit einer Nachricht und einer Adresse, und ein Handler schickt die
Nachricht per Mail an diese Adresse. Zwei Dateien, eine Einstellung.

### Der Ereignistyp

Datei `examplealert/examplealerttype.php` in einem Verzeichnis, das unter `[NotificationEventTypeSettings]` aufgeführt ist:

```php
<?php
/**
 * Event type "examplealert": an event made by code, with a message and the address it is for.
 */
class ExampleAlertType extends eZNotificationEventType
{
    const NOTIFICATION_TYPE_STRING = 'examplealert';

    public function __construct()
    {
        parent::__construct( self::NOTIFICATION_TYPE_STRING );
    }

    function initializeEvent( $event, $params )
    {
        $event->setAttribute( 'data_text1', (string)$params['message'] );
        $event->setAttribute( 'data_text2', (string)$params['address'] );
    }

    function eventContent( $event )
    {
        return $event->attribute( 'data_text1' );
    }
}

eZNotificationEventType::register( ExampleAlertType::NOTIFICATION_TYPE_STRING, 'ExampleAlertType' );
```

`initializeEvent()` erhält die an `eZNotificationEvent::create()` übergebenen Parameter und speichert sie in den Spalten
`data_int1..4` und `data_text1..4` des Ereignisses. `eventContent()` ist das, was `$event->attribute( 'content' )` zurückgibt.
Die letzte Zeile registriert den Typnamen mit seiner Klasse.

### Der Handler

Datei `examplealert/examplealerthandler.php` in einem Verzeichnis, das unter `[NotificationEventHandlerSettings]` aufgeführt ist. Der
Dateiname ist `<id>handler.php` und die Klasse heißt `<id>handler`:

```php
<?php
/**
 * Handler "examplealert": mails the message of an examplealert event to the address of the event.
 */
class examplealerthandler extends eZNotificationEventHandler
{
    const NOTIFICATION_HANDLER_ID = 'examplealert';

    public function __construct()
    {
        parent::__construct( self::NOTIFICATION_HANDLER_ID, 'Example alert handler' );
    }

    function handle( $event )
    {
        if ( $event->attribute( 'event_type_string' ) != 'examplealert' )
            return true; // not ours: the other handlers get it

        $collection = eZNotificationCollection::create( $event->attribute( 'id' ), self::NOTIFICATION_HANDLER_ID, 'ezmail' );
        $collection->setAttribute( 'data_subject', 'Example alert' );
        $collection->setAttribute( 'data_text', $event->attribute( 'data_text1' ) );
        $collection->store();
        $collection->addItem( $event->attribute( 'data_text2' ) ); // send_date 0: send now

        $addresses = array();
        foreach ( $collection->attribute( 'items_to_send' ) as $item )
        {
            $addresses[] = $item->attribute( 'address' );
            $item->remove();
        }
        eZNotificationTransport::instance( 'ezmail' )->send( $addresses, $collection->attribute( 'data_subject' ),
                                                              $collection->attribute( 'data_text' ) );
        $collection->remove();
        return true;
    }
}
```

`handle()` wird für jedes ausstehende Ereignis aufgerufen; ein Handler ignoriert, was nicht ihm gehört, und gibt true zurück. Um sofort zu senden,
fügen Sie einen Eintrag ohne Sendedatum hinzu und senden die Einträge, die keines haben (wie das Beispiel). Um eine Nachricht für eine Zusammenfassung zurückzuhalten, geben Sie
dem Eintrag ein Sendedatum (`eZNotificationSchedule::setDateForItem( $item, array( 'frequency' => 'day', 'hour' => 8 ) )`,
danach `$item->store()`) und senden nicht: Der Handler für die allgemeine Zusammenfassung sendet ihn, wenn ein Zeitereignis dieses Datum überschreitet.
Senden Sie zuerst und entfernen Sie die Einträge erst, wenn `send()` true zurückgegeben hat; bei false rufen Sie `eZNotificationEventFilter::noteDeliveryFailure()` auf und lassen die Einträge stehen, damit der nächste Lauf sie erneut versucht (siehe [die Upgrade-Hinweise](../../eng-us/bc/6.0/notification-ui-and-commands.md#mail-the-transport-refuses-is-kept-and-tried-again)). Entfernen Sie keinen wartenden Eintrag. Der Filter entfernt das Ereignis, wenn kein Eintrag mehr dafür übrig ist, und behält es andernfalls (als behandelt).

### Die Einstellungen

Legen Sie in einer Extension `extension/<yours>/settings/notification.ini.append.php` an und legen Sie die Dateien unter
`extension/<yours>/notification/handler/examplealert/examplealerthandler.php` und
`extension/<yours>/notificationtypes/examplealert/examplealerttype.php` ab:

```ini
<?php /* #?ini charset="utf-8"?

[NotificationEventTypeSettings]
ExtensionDirectories[]=<yours>

[NotificationEventHandlerSettings]
ExtensionDirectories[]=<yours>
AvailableNotificationEventTypes[]=examplealert

*/ ?>
```

Aktivieren Sie die Extension (`[ExtensionSettings] ActiveExtensions[]`), erzeugen Sie nichts neu (Handler werden über den Pfad gefunden, nicht über das
Autoload-Array) und leeren Sie den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.

Der Lauf unten verwendete keine Extension: Er legte die beiden Beispieldateien in `var/tmp/notification-example/` ab und fügte dieses
Verzeichnis mit `RepositoryDirectories[]` in den eigenen Einstellungen des Prozesses hinzu (nie in eine Datei geschrieben). Das ist dieselbe Suche
mit einem anderen Verzeichnis; `ExtensionDirectories[]` wurde hier nicht durchgespielt.

### Ausführen

Erzeugen Sie aus Ihrem Code ein Ereignis und lassen Sie den Filter dafür laufen:

```php
$event = eZNotificationEvent::create( 'examplealert', array( 'message' => 'The example alert works.', 'address' => 'nottest-ui@nottest.invalid' ) );
$event->store();
eZNotificationEventFilter::process( array( (int)$event->attribute( 'id' ) ) );
```

Das vollständige Skript, das ausgeführt wurde (es erzwingt außerdem den Datei-Transport und beobachtet die Mail):

```php
<?php
/** Proves the custom event type and handler of the developer guide (files in var/tmp/notification-example), file transport only. */
$ini = eZINI::instance();
$ini->setVariable( 'MailSettings', 'Transport', 'file' );
$ini->setVariable( 'MailSettings', 'FileTransportDirectory', 'var/tmp/notification-mail' );
$n = eZINI::instance( 'notification.ini' );
$types = $n->variable( 'NotificationEventTypeSettings', 'RepositoryDirectories' );
$types[] = 'var/tmp/notification-example/eventtypes';
$n->setVariable( 'NotificationEventTypeSettings', 'RepositoryDirectories', $types );
$h = $n->variable( 'NotificationEventHandlerSettings', 'RepositoryDirectories' );
$h[] = 'var/tmp/notification-example/handlers';
$n->setVariable( 'NotificationEventHandlerSettings', 'RepositoryDirectories', $h );
$a = $n->variable( 'NotificationEventHandlerSettings', 'AvailableNotificationEventTypes' );
$a[] = 'examplealert';
$n->setVariable( 'NotificationEventHandlerSettings', 'AvailableNotificationEventTypes', $a );
echo "handlers: " . implode( ', ', array_keys( eZNotificationEventFilter::availableHandlers() ) ) . "\n";
$event = eZNotificationEvent::create( 'examplealert', array( 'message' => 'The example alert works.', 'address' => 'nottest-ui@nottest.invalid' ) );
$event->store();
$seen = array();
eZMailNotificationTransport::observe( function ( $addresses, $subject, $body ) use ( &$seen ) { $seen[] = $addresses[0] . ' | ' . $subject . ' | ' . trim( $body ); }, false );
$r = eZNotificationEventFilter::process( array( (int)$event->attribute( 'id' ) ) );
echo "result: " . json_encode( $r ) . "\n";
foreach ( $seen as $s )
    echo "mail: $s\n";
$left = eZDB::instance()->arrayQuery( 'SELECT COUNT(*) AS n FROM eznotificationevent WHERE id=' . (int)$event->attribute( 'id' ) );
echo "event left: " . $left[0]['n'] . "\n";
echo $r['failed'] == 0 && count( $seen ) == 1 ? "PASS\n" : "FAIL\n";
```

```text
$ php bin/php/ezexec.php <the script> --allow-root-user
handlers: ezgeneraldigest, ezcollaborationnotification, ezsubtree, examplealert
result: {"events":1,"removed":1,"kept":0,"failed":0}
mail: nottest-ui@nottest.invalid | Example alert | The example alert works.
event left: 0
PASS
```

`process( $ids )` behandelt nur diese Ereignisse; ohne IDs behandelt es jedes ausstehende Ereignis. Im Produktivbetrieb bleibt das Ereignis
einfach ausstehend, und der nächste Lauf des Benachrichtigungs-Cronjobs behandelt es.

## 3. Die Mails und ihre Templates

Jeder Handler rendert ein Template und liest Variablen, die dieses setzt. Für `ezsubtree` hat `notification/handler/ezsubtree/view/plain.tpl`
die Variablen `$object` (das Inhaltsobjekt) und `$sender` und setzt mit `{set-block scope=root variable=...}`:

| Variable | Wird zu |
|---|---|
| `subject` | dem Betreff |
| `from` | dem Absendertext (dem Anzeigenamen; die Adresse ist `EmailSender`) |
| `message_id`, `reply_to`, `references` | `Message-ID`, `In-Reply-To`, `References` |
| `content_type` | dem Inhaltstyp (für eine HTML-Mail) |

Der Text des Templates ist der Mailtext. Die Zusammenfassung verwendet `notification/handler/ezgeneraldigest/view/plain.tpl`, das
`fetch( notification, digest_handlers, hash( date, ..., address, ... ) )` nach den Handlern fragt, die Einträge für die Adresse haben, und
für jeden `notification/handler/<handler>/view/digest_plain.tpl` einbindet; das des Teilbaum-Handlers ist `ezsubtree/view/digest_plain.tpl`
(mit `digest_element_plain.tpl` pro Eintrag). Die Mails der Zusammenarbeit liegen in `ezcollaboration/view/` (`plain.tpl` und ein
Verzeichnis pro Typ, `ezapprove/`).

Die Templates sind gewöhnliche Templates. Um eine Mail zu ändern, überschreiben Sie sie in Ihrem Design mit dem üblichen Override-Mechanismus
(siehe [Reihenfolge der Template-Overrides](../../eng-us/features/6.0/template-override-ordering.md)): Kopieren Sie die Datei nach
`design/<yours>/override/templates/` mit einer Override-Regel für `notification/handler/ezsubtree/view/plain.tpl`, oder legen Sie eine
geänderte Datei mit demselben Pfad im Ordner `templates/` Ihres Designs ab. Behalten Sie die `set-block`-Zeilen für die Variablen, die Sie benötigen.
Für diese Anleitung wurde kein Override ausgeführt; ausgeführt wurden die Standard-Templates, und ihre Ausgabe ist in [der Anleitung für Benutzer](../../eng-us/features/6.0/notifications.md) gezeigt.

Die Einstellungsseite verwendet `notification/handler/<id_string>/settings/edit.tpl`, das `handler` erhält, und in admin4 außerdem
die Variablen, die die Einstellungsansicht übergibt (`subscriptions`, `subscription_total`, `confirm_remove`, ...). Ein Handler, der nicht zu den
drei eingebauten gehört, erhält eine allgemeine Karte mit seinem `edit.tpl`; seine Methoden `fetchHttpInput( $http, $module )` und `storeSettings( $http, $module )`
werden aufgerufen, wenn die Seite abgeschickt wird (`Store`).

## 4. Der Datei-Transport und wohin Mails gehen

`eZFileTransport` schreibt eine Datei pro Nachricht. `[MailSettings] FileTransportDirectory` in `site.ini` wählt das Verzeichnis
(Standard `var/log/mail`):

```php
$ini = eZINI::instance();
$ini->setVariable( 'MailSettings', 'Transport', 'file' );
$ini->setVariable( 'MailSettings', 'FileTransportDirectory', 'var/tmp/notification-mail' );
```

`setVariable()` ändert nur die Einstellungen des laufenden Prozesses; nichts wird in eine Datei geschrieben. So funktionieren die
Konsolenoption `--mail-file-dir` und jeder Test. Die Dateien heißen `<time>-<number>.mail` und enthalten die Header und den Mailtext.

## 5. Erweiterungspunkte

| Punkt | Wie |
|---|---|
| Ein neuer Ereignistyp | eine Klassendatei und `notification.ini` wie in Abschnitt 2 |
| Ein neuer Handler | ebenso |
| Einen Handler abschalten | entfernen Sie ihn aus `[NotificationEventHandlerSettings] AvailableNotificationEventTypes[]` |
| Ein neuer Transport | eine Klasse `<name>notificationtransport` in `<path><name>notificationtransport.php`, `[TransportSettings] TransportPluginPath[]=<path>` (der Pfad endet mit einem `/`), und rufen Sie `eZNotificationTransport::instance( '<name>' )` auf |
| Ein anderer Weg, Mails zu senden | `[MailSettings] TransportAlias[<name>]=<class>`, das `eZMailTransport` erweitert, und `Transport=<name>` |
| Die Mail beobachten oder unterdrücken | `eZMailNotificationTransport::observe( $callback, $suppress )`; der Callback erhält `( $addresses, $subject, $body, $parameters, $sent )`; `$sent` gibt an, ob der Transport die Nachricht angenommen hat (fehlt, wenn unterdrückt) |
| In die Befehle, den Cronjob-Teil und die Ansichten eingreifen | `site.ini [RunnableSettings] Listeners[]` und `Implementation[<class>]`, siehe [die ausführbaren Klassen](../spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md) |
| Das Verhalten des Dienstes ersetzen | erweitern Sie `expNotificationService` und die Befehle über `Implementation[]` für die Befehlsklassen; der Dienst selbst ist statisch |
| Das Audit-Protokoll | Läufe über die Konsole und das Web erzeugen `system.command.run`; der Cronjob-Lauf `system.cronjob.run` ([Audit-Ereignismodell](../spezifikationen/6.0/audit-ereignismodell.md)) |

## 6. Tests

Die Testklasse `tests/tests/kernel/classes/notification/NotificationSystemTest.php` ist ein Muster: Live-Stil (sie läuft auf
der Installation, die sie vorfindet, und wird übersprungen, wo es keine gibt), Testinhalte namens `NOTTEST`, Adressen unter `nottest.invalid` und
zu Beginn:

```php
$ini = eZINI::instance();
$ini->setVariable( 'MailSettings', 'Transport', 'file' );
$ini->setVariable( 'MailSettings', 'FileTransportDirectory', $mailDir );
// refuse to run unless trim( $ini->variable( 'MailSettings', 'Transport' ) ) === 'file'
```

Sie verarbeitet nur ihre eigenen Ereignisse (`process( $ids )`), sodass die ausstehenden Ereignisse und Zusammenfassungen der Installation nicht berührt werden, liest
die Mails aus den Dateien und entfernt, was sie angelegt hat. Führen Sie sie aus:

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/notification/NotificationSystemTest.php
```

```text
OK (23 tests, 246 assertions)
```

Ein Test der Zusammenfassung erfordert kein Warten: Legen Sie das Ereignis mit einer Zeit in der Zukunft an,
`eZNotificationEvent::create( 'ezcurrenttime', array( 'time' => $time ) )`, speichern Sie es und verarbeiten Sie es über seine ID.

## Verwandte Seiten

- [Die Spezifikation](../spezifikationen/6.0/benachrichtigungen.md), [die INI-Referenz](../spezifikationen/6.0/benachrichtigungen-ini.md), [die Befehlsreferenz](../spezifikationen/6.0/benachrichtigungen-cli.md)
- [Die Anleitung für Administratoren](benachrichtigungen-administrator.md), [die Anleitung für Benutzer](../../eng-us/features/6.0/notifications.md)
- [Extensions](extensions.md), [Befehle, Cronjob-Teile und Modulansichten als Klassen](../spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
