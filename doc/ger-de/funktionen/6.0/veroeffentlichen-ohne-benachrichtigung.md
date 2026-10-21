# Ohne Benachrichtigung veröffentlichen

Ein Redakteur, der einen Tippfehler oder die Metadaten eines veröffentlichten Artikels korrigiert, will nicht, dass jeder
Abonnent seines Ordners dafür eine E-Mail erhält. Wo es eingeschaltet ist, bieten das Bearbeitungsformular und die
Versionsvorschau neben der Schaltfläche zum Veröffentlichen **Ohne Benachrichtigung veröffentlichen** an: Die Version wird
veröffentlicht wie mit der anderen Schaltfläche, aber für sie wird kein Benachrichtigungsereignis erzeugt, sodass die
Abonnements ihrer Platzierungen nichts senden.

## Einschalten

Zwei Dinge sind nötig, die Einstellung und die Richtlinie:

1. `notification.ini [NotificationSettings] PublishWithoutNotification=enabled` (standardmäßig `disabled`). Setzen Sie
   es in `settings/override/notification.ini.append.php` für jeden Siteaccess oder in
   `settings/siteaccess/<admin>/notification.ini.append.php`, um es nur in der Administrationsoberfläche anzubieten.
2. Die Richtlinienfunktion **content / publish_without_notification** in den Rollen der Redakteure, die sie verwenden
   dürfen. Sie hat keine eigenen Einschränkungen. Eine Richtlinie `content/*` schließt sie ein, sodass die Rolle
   Administrator sie ohne Änderung hat; eine Rolle, die ihre Inhaltsfunktionen einzeln aufführt, erhält sie nur, wenn sie
   im Rolleneditor hinzugefügt wird.

| Einstellung | Richtlinie | Die Schaltfläche | Ein gesendetes `PublishNotNotifyButton` |
|---|---|---|---|
| `disabled` | beliebig | verborgen | veröffentlicht **mit** Benachrichtigung |
| `enabled` | keine | verborgen | veröffentlicht **mit** Benachrichtigung (und protokolliert eine Warnung) |
| `enabled` | gewährt (auch über eine eingeschränkte Richtlinie `content/*`) | gezeigt | veröffentlicht **ohne** Benachrichtigung |

Die Schaltfläche ändert nur, was nach dem Veröffentlichen geschieht. Ob der Benutzer den Entwurf überhaupt
veröffentlichen darf, wird wie bisher entschieden: durch `content/edit` für das Bearbeitungsformular und für die
Versionsvorschau durch den Bearbeitungszugriff und dadurch, dass er der Ersteller des Entwurfs ist. Eine gesendete
Schaltfläche ist nie ein Weg um diese Prüfungen herum; bei ausgeschalteter Einstellung oder ohne die Richtlinie ist es das
gewöhnliche Veröffentlichen. Die Schaltfläche gehört zum selben Formular wie die Schaltfläche zum Veröffentlichen, sodass
sie bei aktiver Extension `ezformtoken` (wie der Installer sie einrichtet) durch dasselbe Formular-Token geschützt ist.

Wo sie ist: `content/edit` (beide Schaltflächenleisten) und `content/versionview` in den Designs `admin`, `admin3`,
`admin4` und `standard` sowie die Konfliktseite (`content/edit_conflict.tpl` in `admin`, `admin4` und `standard`), die sie
erneut anbietet, wenn sie gedrückt wurde, bevor der Konflikt gefunden wurde. Designs von Extensions (das Website
Interface, `exp_adminui`, Website-Themes) behalten ihre eigenen Templates und zeigen die Schaltfläche erst, wenn sie sie
hinzufügen, mit der Bedingung

```
{if and( ezini( 'NotificationSettings', 'PublishWithoutNotification', 'notification.ini' )|eq( 'enabled' ),
         fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'publish_without_notification' ) ) )}
<input class="button" type="submit" name="PublishNotNotifyButton" value="{'Publish without notification'|i18n( 'design/admin/content/edit' )}" />
{/if}
```

(`PreviewPublishNotNotifyButton` in der Versionsvorschau).

## Benachrichtigungen nur für einige Klassen

Eine Website, die Benachrichtigungen für ihre Artikel und Dateien sendet, aber nicht für jeden Ordner, jedes Bild oder
Banner, kann die Klassen nennen, deren Veröffentlichungen ein Benachrichtigungsereignis erzeugen:

```ini
# settings/override/notification.ini.append.php
[NotificationSettings]
NotificationFilterByClassIdentifier=enabled
IncludeClasses[]
IncludeClasses[]=article
IncludeClasses[]=file
```

Die Liste nimmt Klassenbezeichner; ein Eintrag aus Ziffern wird als Klassen-ID genommen und nennt seine Klasse
(`IncludeClasses[]=16`), sodass eine mit IDs geschriebene Liste nicht still auf nichts passt. Bezeichner werden genau
verglichen (`Article` ist nicht `article`).

Bei eingeschalteter Einstellung zählt eine Veröffentlichung jeder anderen Klasse als ohne Benachrichtigung
veröffentlicht: Der Filter `content/notification/create` erhält `false`, und die Abonnements ihrer Platzierungen senden
nichts. Mit einer leeren Liste erzeugt nichts ein Ereignis. `disabled` (der Standard) benachrichtigt für jede Klasse. Das
ist unabhängig von der Schaltfläche: Die Schaltfläche lässt eine Version einer beliebigen Klasse aus, der Klassenfilter
jede Version der nicht aufgeführten Klassen. Benachrichtigungen zur Freigabe (Zusammenarbeit) sind nicht betroffen. Die
Klasse wird nur nachgeschlagen, wenn der Filter eingeschaltet ist und Klassen aufführt
(`eZContentOperationCollection::notificationIncludesClass()`).

## Was ausgelassen wird und was nicht

| Benachrichtigung | Ohne Benachrichtigung |
|---|---|
| Abonnements der Platzierungen des Objekts (`ezsubtree`), sofort gesendet | nicht gesendet: kein Ereignis `ezpublish` |
| Elemente der Zusammenfassung dieser Abonnements (`ezgeneraldigest`) | nicht gesammelt: Sie kommen aus demselben Ereignis |
| Benachrichtigungen zur Zusammenarbeit bei einer Freigabe (`ezcollaborationnotification`): die Anfrage an den Freigebenden, die Antwort des Autors | **wie bisher gesendet**: Sie sind Ereignisse eigenen Typs, erzeugt vom Freigabe-Workflow |
| Newsletter, RSS-Feeds, der Suchindex, der statische Cache, das Leeren des Ansichts-Caches | unverändert: Sie sind keine Benachrichtigungen |

„Ohne Benachrichtigung“ bedeutet also: Niemand wird über die Benachrichtigungsabonnements über diese Version informiert.
Spätere, auf gewöhnliche Weise veröffentlichte Versionen benachrichtigen wieder.

## Wie es funktioniert

Die Veröffentlichungsoperation (`kernel/content/operation_definition.php`) hat den optionalen Parameter `notify`,
standardmäßig `true`. Die Ansichten übergeben `false`, wenn
`eZContentOperationCollection::publishWithoutNotification( $buttonName )` es sagt (die Einstellung, die gesendete
Schaltfläche, die Richtlinie). Der Parameter erreicht:

- `create-notification`: `eZContentOperationCollection::createNotificationEvent( $objectID, $version, $notify )` gibt
  ihn statt `true` an den Filter `content/notification/create`. Der Filter hat das letzte Wort: Ein Listener, der `true`
  zurückgibt, erzeugt das Ereignis auch für eine Veröffentlichung ohne Benachrichtigung, daher sollte ein Listener
  weitergeben, was er erhält, statt `true` zurückzugeben (siehe
  [Filter für Zugriff und Ansichts-Cache](zugriffs-und-cache-filter.md#contentnotificationcreate)).
- `send-to-publishing-queue`: Mit `content.ini [PublishingSettings] AsynchronousPublishing=enabled` wird eine
  Veröffentlichung ohne Benachrichtigung nicht eingereiht, sondern sofort veröffentlicht. Der asynchrone Veröffentlicher
  führt die Operation erneut nur mit dem Objekt und der Version aus, sodass der Parameter dort verloren ginge.
- Das Memento einer unterbrochenen Operation: Ein Workflow, der die Veröffentlichung zurückhält (eine Freigabe),
  speichert die Parameter der Operation, darunter `notify`, und der Workflow-Cronjob setzt die Operation mit ihnen fort,
  sodass auch eine später freigegebene Version ohne Benachrichtigung geht.

Code, der über die Operation veröffentlicht, übergibt den Parameter auf dieselbe Weise:

```php
eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $id, 'version' => $version, 'notify' => false ) );
```

`false`, `0` und `"0"` bedeuten nein; alles andere und ein fehlender Parameter bedeuten ja. Jeder andere Aufrufer der
Operation (Uploads, Importe, die REST-Schnittstelle, die Inhaltsaufträge) ist unverändert und benachrichtigt.

`eZContentFunctions::createAndPublishObject( $params, $notify = true )` und
`eZContentFunctions::updateAndPublishObject( $object, $params, $notify = true )` nehmen den Parameter ebenfalls an, für
Importe und Skripte, die Inhalte anlegen oder aktualisieren, ohne die Abonnenten zu informieren. Bestehende Aufrufer sind
unverändert: Ohne das Argument benachrichtigen beide, wie vor seiner Existenz, sodass ein Skript oder Cronjob `false`
verlangen muss, um still zu bleiben. Der Wert wird so gelesen, wie die Operation ihn liest
(`eZContentOperationCollection::notifyRequested()`): `false`, `0` und `"0"` bedeuten nein, alles andere (auch `null`) ja.
`createAndPublishObject()` gibt das Objekt so zurück, wie die Veröffentlichungsoperation es hinterlassen hat
(veröffentlicht, seine aktuelle Version und sein Hauptknoten); vorher gab es das Objekt zurück, das es instanziiert hatte
und das noch Entwurf sagte.

Zwischen Anfragen wird nichts aufbewahrt: Die Antwort kommt jedes Mal aus der Einstellung, der Anfrage und dem Benutzer,
sodass die dauerhaften Worker von Velocity jeder Anfrage ihre eigene Antwort geben.

## Tests

- `eZPublishWithoutNotificationTest` (ohne Datenbank): die Einstellung, die Richtlinie und die gesendete Schaltfläche
  (`publishWithoutNotification()`, `canPublishWithoutNotification()`), die Definition der Operation, der Filter, der
  `false` erhält, die Schreibweisen von nein, die ausgelassene asynchrone Warteschlange, der aus einem gespeicherten
  Memento weitergegebene Parameter, die Zuordnung der Schaltfläche, die Richtlinienfunktion und die Templates, der
  Klassenfilter (`notificationIncludesClass()`), `notifyRequested()` und die Standards von `eZContentFunctions`.
- `eZPublishWithoutNotificationLiveTest` (auf einer Installation, ohne eine übersprungen): `notify=false` veröffentlicht
  ohne Ereignis, die nächste Version mit einem; eine aus einem gespeicherten Memento fortgesetzte Veröffentlichung behält
  `notify=false`; der Klassenfilter, auch mit einer Klassen-ID; `createAndPublishObject()` und `updateAndPublishObject()`
  mit `notify=false`, `null` und `"0"`. Die Live-Tests setzen im Prozess den Datei-Mailtransport und weigern sich, ohne
  ihn zu laufen.

## Verwandte Seiten

- [Benachrichtigungen: die Anleitung für Benutzer](benachrichtigungen.md)
- [Benachrichtigungen: die Anleitung für Administratoren](../../anleitungen/benachrichtigungen-administrator.md#8-veröffentlichen-ohne-benachrichtigung)
- [Filter für Zugriff und Ansichts-Cache](zugriffs-und-cache-filter.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
