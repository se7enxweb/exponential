# Spezifikation: das Benachrichtigungssystem

Die Referenz dafür, wie Benachrichtigungen entstehen und verarbeitet werden: die Tabellen, der Ablauf von einem
veröffentlichten Element bis zur Mail, die Handler und ihre Regeln, der Digest, die Serviceklasse mit ihrer Sperre, ihrem
Laufprotokoll und ihrem Audit-Ereignis, die Modulansichten, die Templates, die Richtlinien und das Audit. Lesen Sie sie,
wenn Sie das System ändern, einen Handler schreiben oder eine genaue Antwort brauchen.
Für einen ersten Rundgang beginnen Sie mit [der Anleitung für Benutzer](../../../en/us/features/6.0/notifications.md) oder
[der Anleitung für Administratoren](../../anleitungen/benachrichtigungen-administrator.md); Einstellungen stehen in [der INI-Referenz](benachrichtigungen-ini.md),
Befehle in [der Befehlsreferenz](benachrichtigungen-cli.md) und Erweiterungen in [der Anleitung für Entwickler](../../anleitungen/benachrichtigungen-entwickler.md).

## Daten

| Tabelle | Klasse | Bedeutung |
|---|---|---|
| `eznotificationevent` | `eZNotificationEvent` | Ein Ereignis. `event_type_string` (`ezpublish`, `ezcollaboration`, `ezcurrenttime` oder ein eigener), `data_int1..4`, `data_text1..4`, `status` (0 `STATUS_CREATED` ausstehend, 1 `STATUS_HANDLED`). Es gibt keine Datumsspalte: `createdAt()` liest das Alter aus dem Inhalt (siehe unten) |
| `eznotificationcollection` | `eZNotificationCollection` | Die Nachricht, die ein Handler für ein Ereignis erstellt hat: `event_id`, `handler`, `transport` (`ezmail`), `data_subject`, `data_text` |
| `eznotificationcollection_item` | `eZNotificationCollectionItem` | Eine Empfängeradresse einer Sammlung: `collection_id`, `event_id`, `address`, `send_date` (0 = sofort senden, sonst die Digest-Zeit als Unix-Zeitstempel) |
| `ezsubtree_notification_rule` | `eZSubtreeNotificationRule` | Ein Abonnement: `user_id` (ID des Inhaltsobjekts des Benutzers), `node_id`, `use_digest` |
| `ezgeneral_digest_user_settings` | `eZGeneralDigestUserSettings` | `user_id`, `receive_digest` (0/1), `digest_type` (3 täglich, 1 wöchentlich, 2 monatlich), `day` (Name des Wochentags bei wöchentlich, Tag des Monats bei monatlich), `time` (`H:00`) |
| `ezcollab_notification_rule` | `eZCollaborationNotificationRule` | `user_id`, `collab_identifier` (zum Beispiel `ezapprove`) |

## Ereignistypen

| Typ | Erzeugt von | Felder | `createdAt()` liest |
|---|---|---|---|
| `ezpublish` | der Veröffentlichungsoperation (`eZContentOperationCollection::createNotificationEvent`) | `data_int1` Objekt-ID, `data_int2` Version | `created`/`modified` der Version |
| `ezcollaboration` | `eZCollaborationItem::createNotificationEvent()` | `data_int1` Element-ID, `data_text1` Kennung der Zusammenarbeit | `created`/`modified` des Elements |
| `ezcurrenttime` | dem Benachrichtigungslauf | `data_int1` die Zeit | `data_int1` |

`createdAt()` gibt false zurück, wenn der Inhalt verschwunden ist; `eZNotificationEvent::removeOlderThan()` lässt solche
Ereignisse unangetastet, sofern es nicht angewiesen wird, sie zu entfernen.

## Der Ablauf

1. Das Veröffentlichen einer Version erzeugt ein `ezpublish`-Ereignis. Ein Element der Zusammenarbeit (eine Freigabe, ein
   Kommentar) erzeugt ein `ezcollaboration`-Ereignis. Ein Lauf erzeugt ein `ezcurrenttime`-Ereignis, sofern er nicht
   angewiesen wird, es zu lassen.
2. `eZNotificationEventFilter::process( $eventIDList = null )` holt die ausstehenden Ereignisse (alle oder die angegebenen
   IDs) und ruft für jedes in einer Datenbanktransaktion `handle( $event )` jedes Handlers aus `[NotificationEventHandlerSettings]
   AvailableNotificationEventTypes[]` in dieser Reihenfolge auf. Ein Handler, der einen Fehler wirft, wird protokolliert, in
   `failed` gezählt und hält die anderen nicht auf; das Ereignis wird nicht erneut versucht, weil ein Handler, der bereits
   gesendet hat, noch einmal senden würde.
3. Nach den Handlern: Ein Ereignis ohne Sammlungselemente wird entfernt; andernfalls bleibt es mit dem Status „verarbeitet“
   erhalten, bis seine Elemente verschwunden sind.
4. Nach allen Ereignissen laufen `eZNotificationCollection::removeEmpty()` und `eZNotificationEvent::cleanupHandled()`
   (verarbeitete Ereignisse, auf die nichts mehr wartet). `process()` gibt `events`, `removed`, `kept`, `failed`, `send_failed`, `dropped`, `retried` und `notes` zurück.

### Der Teilbaum-Handler (`ezsubtree`)

Bei einem `ezpublish`-Ereignis überspringt er Ereignisse, deren Version nicht die aktuelle ist, deren Knoten unsichtbar ist
oder deren Elternknoten oder Klasse fehlt. Andernfalls rendert er `notification/handler/ezsubtree/view/plain.tpl` (das
Template setzt `subject`, `from`, `message_id`, `reply_to`, `references`), erstellt eine Sammlung, und
`eZSubtreeNotificationRule::fetchUserList()` ermittelt die Abonnenten: Regeln auf dem Knoten oder einem Vorfahren eines
beliebigen Orts des Objekts, Benutzer, die aktiviert und nicht unsichtbar sind und eine Rolle mit `content/read` besitzen
(eine Richtlinie, die nach Klasse, Sektion, Eigentümer, Knoten, Teilbaum oder Zustand eingeschränkt ist, wird gegen das
Objekt geprüft). Ein Abonnent mit Digest (`ezgeneral_digest_user_settings.receive_digest = 1`) erhält ein `send_date` von
`eZNotificationSchedule::setDateForItem()`; an die anderen wird sofort über `eZMailNotificationTransport` gesendet.

Der wöchentliche Digest speichert den Wochentag mit seinem Namen in der Sprachumgebung des Benutzers, der ihn gewählt hat.
Der Handler akzeptiert diesen Namen, den englischen Namen oder eine Zahl von 0 (Sonntag) bis 6, sodass ein Cronjob, der in
einer anderen Sprachumgebung läuft, ihn trotzdem einplant.

### Der Handler für den allgemeinen Digest (`ezgeneraldigest`)

Bei einem `ezcurrenttime`-Ereignis ermittelt er jede Adresse mit einem Element, dessen `send_date` nicht nach der Zeit des
Ereignisses liegt, rendert `notification/handler/ezgeneraldigest/view/plain.tpl` einmal pro Adresse (das
`notification/handler/<handler>/view/digest_plain.tpl` für jeden Handler einbindet, der Elemente hat), sendet die Mail und
entfernt die darin enthaltenen Elemente in Portionen von `[RuleSettings] LimitDeleteElements`. Der Betreff enthält die
Zeit, zu der der Digest erstellt wurde.

### Der Handler für die Zusammenarbeit (`ezcollaborationnotification`)

Bei einem `ezcollaboration`-Ereignis fragt er den Handler des Elements (`eZCollaborationItemHandler::handleCollaborationEvent()`),
welche Teilnehmer eine Regel für die Kennung des Elements haben, erstellt eine Sammlung pro Teilnehmerrolle (oder eine für
alle) und sendet sofort. Die Mails zur Freigabe sind `notification/handler/ezcollaboration/view/ezapprove/*.tpl`.

### Mail, die der Transport ablehnt

`eZMailNotificationTransport::send()` gibt die Antwort des Mail-Transports zurück. Ein Handler, der false erhält, behält die
Elemente (und damit das verarbeitete Ereignis) und ruft `eZNotificationEventFilter::noteDeliveryFailure()` auf:

- Der Handler für den allgemeinen Digest behält die Elemente der fehlgeschlagenen Adresse (`keepItemsOfFailedAddresses()`)
  und entfernt die übrigen; ein Element, das seit länger als `[RuleSettings] RetryHours` fällig ist, wird entfernt und von
  `noteDropped()` gezählt;
- die Handler für Teilbaum und Zusammenarbeit senden zuerst und entfernen ihre Elemente nur bei Erfolg; ein Fehlschlag
  hinterlässt die Elemente mit `send_date = 0` an einem verarbeiteten Ereignis.

`eZNotificationEventFilter::retryUnsent()` läuft zu Beginn jedes `process()`: Es ermittelt die Elemente mit `send_date = 0`,
deren Ereignis verarbeitet ist (sie können nur nach einem Fehlschlag dort sein), sendet jede Sammlung erneut, entfernt die
Elemente bei Erfolg (`retried`) und gibt diejenigen auf (`dropped`), deren Ereignis älter als `RetryHours` ist oder deren
Adresse sich nicht anschreiben lässt. Die Digest-Elemente werden vom nächsten Zeitereignis erneut versucht, weil sie noch
fällig sind. `process()` gibt neben seinen anderen Zahlen `send_failed`, `dropped`, `retried` und `notes` zurück. Siehe
[die Upgrade-Hinweise](../../../en/us/bc/6.0/notification-ui-and-commands.md#mail-the-transport-refuses-is-kept-and-tried-again).

## Zeitfenster des Digests

`eZNotificationSchedule::setDateForItem( $item, array( 'frequency' => 'day|week|month', 'day' => n, 'hour' => h ) )`
setzt `send_date` auf den nächsten solchen Zeitpunkt nach jetzt: täglich der nächste Zeitpunkt, zu dem es `h:00` ist;
wöchentlich der nächste `day` (0 Sonntag) um `h:00`, innerhalb von sieben Tagen; monatlich der nächste `day` des Monats um
`h:00`, wobei Tag 31 in einem kurzen Monat auf dessen letzten Tag verkürzt wird. Es gilt die Zeitzone des Servers. Ein Lauf,
dessen Zeitereignis zu oder nach `send_date` liegt, sendet das Element.

## expNotificationService

`kernel/classes/expnotificationservice.php`. Alle Aufrufer (der Cronjob-Teil, die Befehle, die Statusseite, die Seite zum
Ausführen des Filters) laufen über diese Klasse.

| Methode | Was sie tut |
|---|---|
| `run( $options )` | Ein Durchlauf unter der Sperre. Optionen: `source` (`cron`, `console`, `web`), `at` (Zeit des Zeitereignisses), `time_event` (false: keines), `events` (nur diese IDs; kein Zeitereignis), `user`. Gibt `result` (`ok`, `busy`, `failed`), `events`, `removed`, `kept`, `failed`, `send_failed`, `dropped`, `retried`, `mails`, `recipients` (was der Transport angenommen hat), `ms`, `error` zurück. Protokolliert den Lauf; löst für jede Quelle außer `cron` das Audit-Ereignis aus |
| `plan( $options )` | Derselbe Durchlauf in einer Transaktion, die zurückgerollt wird, wobei die Mail beobachtet und unterdrückt wird. Gibt die geplanten `mails` zurück (`subject`, `to`, maskierte `addresses`, `raw`). Wird abgelehnt, wo sich die Tabellen nicht zurückrollen lassen |
| `status()` | Die Zahlen der Statusseite; `problems()` und `problemText()` liefern die Problemliste |
| `subscriptions( $userID, $filter, $offset, $limit )` | Teilbaum-Abonnements mit `path`, `class`, `last_change`, `missing`; Filter `q`, `class`, `missing`, `ids` |
| `subscribedClasses( $userID )` | Die Klassen der verfolgten Knoten, für den Filter |
| `eventsReport()`, `cleanup()` | Ereignisliste; Entfernen verarbeiteter verwaister und alter Ereignisse |
| `lastRuns()`, `runningNow()` | Das Laufprotokoll; wer die Sperre hält |
| `parseAge()`, `maskAddress()` | Hilfsfunktionen für `30d` und `j***@example.com` |

### Sperre

`var/<var dir>/notification/run.lock` wird mit `flock( LOCK_EX | LOCK_NB )` gesperrt. Der Inhaber schreibt seine
Prozess-ID, seine Startzeit und seine Quelle in die Datei. Das Betriebssystem gibt die Sperre frei, wenn der Prozess endet,
sodass ein abgestürzter Lauf nichts hinterlässt (die Datei bleibt; ihr Inhalt ist veraltet und wird ignoriert, wenn die
Sperre frei ist). Ein Lauf, der die Sperre nicht erhält, gibt `busy` zurück; lässt sich die Datei nicht öffnen (Webserver
und Kommandozeile laufen unter verschiedenen Benutzern, und die Datei gehört dem jeweils anderen), gibt er `failed` mit dem
Grund zurück. Aus diesem Grund wird die Datei für alle beschreibbar angelegt.
`runcronjobs.php` hat einen eigenen Mutex pro Cronjob-Teil (`[CronjobSettings] MaxScriptExecutionTime`); beide sind
voneinander unabhängig.

### Laufprotokoll

`runs.jsonl`: ein JSON-Objekt pro Zeile, das neueste zuletzt, 200 werden aufbewahrt (gekürzt, wenn es 400 überschreitet).
Felder: `time`, `source`, `dry`, `result`, `ms`, `events`, `removed`, `kept`, `failed`, `send_failed`, `dropped`, `retried`, `mails`, `recipients`, `error`, `user`. Probeläufe werden
nicht protokolliert. Die Statusseite und `exp:notification:status` lesen es.

### Audit-Ereignis

Ein von der Konsole oder aus dem Web gestarteter Lauf wird als `system.command.run` mit dem Objekttyp `notification`, der
ID `run` und `after` mit `source`, `events`, `mails`, `recipients`, `failed`, `ms` auditiert. Der Cronjob-Lauf wird von
`runcronjobs.php` als `system.cronjob.run` oder `system.cronjob.fail` für das Skript `notification` auditiert. Siehe
[das Modell der Audit-Ereignisse](audit-ereignismodell.md).

### Die Mail beobachten

`eZMailNotificationTransport::observe( $callback, $suppress )` registriert einen Callback, der für jede Nachricht `( $addresses, $subject,
$body, $parameters, $sent )` erhält (`$sent`, ob der Transport sie angenommen hat, fehlt, wenn `$suppress` true ist und nichts an den Transport übergeben wird). Der Service verwendet ihn,
um Mails zu zählen und zu planen. `observe( null )` entfernt ihn.

### Hintergrundjob

`expNotificationJob::start( $siteaccess, $dry, &$error )` startet `bin/php/notificationrun.php --job=<id> --source=web` losgelöst
(`setsid`, die von `expProcessTools` gefundene PHP-Kommandozeile), wobei seine Ausgabe nach `jobs/<id>.log` geht. Der Befehl
schreibt Zeilen `{type: log, message}` und abschließend `{type: end, result}` nach `jobs/<id>.jsonl`;
`expNotificationJob::progress( $id, $offset )` liest sie. Ein Job, der zwei Minuten lang nichts geschrieben und nicht
geendet hat, wird als angehalten gemeldet.

## Modul `notification`

| Ansicht | Richtlinie | Parameter und POST-Felder |
|---|---|---|
| `settings` | `use` | `(offset)`, `(q)`, `(class)`; POST `NewRule_ezsubtree`, `RemoveRule_ezsubtree`, `SelectedRuleIDArray_ezsubtree[]`, `UseConfirm`, `ConfirmRemoveRule`, `CancelRemoveRule`, `Store`, `SaveCollaboration`, `FilterSubscriptions`, `ClearFilter`, `Query`, `ClassFilter` |
| `addtonotification/<node>` | `use` | GET fragt nach; POST `ConfirmAddNotification`, `ConfirmRemoveNotification`, `CancelNotification`, `RedirectURI` |
| `status` | `administrate` | POST `CleanupHandled`, `CleanupOld` mit `OlderThan`, `RemoveMissingRules` |
| `job` | `administrate` | POST `Action=start` (`DryRun=1`), GET `job/<id>/<offset>` antwortet mit JSON `{events, offset, done}` |
| `runfilter` | `administrate` | POST `RunFilterButton`, `SpawnTimeEventButton` |

Jeder POST trägt das Formular-Token (die Extension `ezformtoken`). `UseConfirm` im Einstellungsformular weist die Ansicht
an, vor dem Entfernen die Bestätigung anzuzeigen; ein Formular ohne dieses Feld (ein älteres Template) entfernt wie bisher
sofort.

## Templates

| Template | Verwendet für |
|---|---|
| `notification/settings.tpl` | Die Einstellungsseite; bindet `handler/<id_string>/settings/edit.tpl` für jeden Handler ein |
| `notification/addconfirm.tpl`, `addingresult.tpl`, `removeresult.tpl` | Die Seiten zum Hinzufügen und Entfernen |
| `notification/status.tpl`, `runfilter.tpl`, `parts/style.tpl`, `parts/notice.tpl` | admin4: Statusseite, Seite zum Ausführen des Filters, Stile (`--nf-*`-Tokens auf Basis der `--a4-*`-Tokens), der eingebettete Hinweis |
| `notification/handler/<id_string>/view/plain.tpl` und weitere | Die Mails |

admin4 und admin4l haben die überarbeiteten Seiten; `design/admin` und `design/standard` stellen `addconfirm.tpl` und
`removeresult.tpl` bereit und behalten die früheren Einstellungsseiten.

## Verwandte Seiten

- [Anleitung für Benutzer](../../../en/us/features/6.0/notifications.md), [Anleitung für Administratoren](../../anleitungen/benachrichtigungen-administrator.md), [Anleitung für Entwickler](../../anleitungen/benachrichtigungen-entwickler.md)
- [INI-Referenz](benachrichtigungen-ini.md), [Befehlsreferenz](benachrichtigungen-cli.md)
- [Upgrade-Hinweise](../../../en/us/bc/6.0/notification-ui-and-commands.md)
- [Modell der Audit-Ereignisse](audit-ereignismodell.md), [Befehle, Cronjob-Teile und Modulansichten als Klassen](ausfuehrbare-befehle-cronjobs-ansichten.md)
