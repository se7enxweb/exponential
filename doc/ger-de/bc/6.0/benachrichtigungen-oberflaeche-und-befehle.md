# Hinweise zum Upgrade: Benachrichtigungen

Was sich am Verhalten des Benachrichtigungssystems geändert hat, was Sie beim Upgrade tun müssen und wie E-Mails
behalten werden, die der Transport ablehnt. Lesen Sie die Seite, wenn Sie Exponential 6.0.15 oder neuer betreiben,
Templates der Benachrichtigungen überschrieben haben oder die Klassen der Benachrichtigungen aus Ihrem eigenen Code
aufrufen. Die Seiten, die das System beschreiben, wie es jetzt ist:
[die Anleitung für Benutzer](../../funktionen/6.0/benachrichtigungen.md),
[die Anleitung für Administratoren](../../anleitungen/benachrichtigungen-administrator.md),
[die Spezifikation](../../spezifikationen/6.0/benachrichtigungen.md).

## Was Sie tun müssen

1. Stellen Sie sicher, dass der Cronjob-Teil `notification` läuft: Er gehört zur Gruppe `frequent`, oder Sie führen ihn
   allein mit `php runcronjobs.php notification` aus. Öffnen Sie **Setup > Benachrichtigung**: Ein roter Hinweis sagt
   Ihnen, wenn Ereignisse warten und kein Lauf verzeichnet ist.
2. Wenn Sie `notification/addingresult.tpl` oder die Templates der Einstellungen in Ihrem eigenen Design überschreiben,
   lesen Sie die Abschnitte unten. Designs, die sie nicht überschreiben, brauchen nichts.
3. Leeren Sie nach dem Upgrade die Caches (`php bin/php/ezcache.php --clear-all --allow-root-user`) und laden Sie
   PHP-FPM neu oder starten Sie Velocity neu, wie bei jeder Codeänderung.
4. Entfernen Sie bei Bedarf die Ereignisse und Abonnements, die sich angesammelt haben (Abschnitt „Aufräumen“).

## Verhaltensänderungen

### Das Öffnen von /notification/addtonotification/<node> abonniert nicht mehr

Vorher: Ein GET der Adresse legte das Abonnement sofort an (die Inhaltsaktion leitete nach einem Formular-POST dorthin
weiter). Ein Link oder ein Bild auf irgendeiner Seite konnte einen angemeldeten Benutzer abonnieren lassen. Jetzt: Ein
GET zeigt eine Bestätigungsseite („Über Updates benachrichtigen“); die Schaltflächen sind ein POST, der das
Formular-Token trägt, und nur **Benachrichtigen** abonniert. Für ein Element, dem der Benutzer bereits folgt, bietet die
Seite **Benachrichtigungen für diesen Eintrag beenden** an. Der Eintrag **Benachrichtigen** im Inhaltsmenü (das
Formularfeld `ActionAddToNotification`) funktioniert wie bisher und endet jetzt auf der Bestätigungsseite, einen Klick
mehr.

Templates: Die View rendert `notification/addconfirm.tpl` (neu), dann nach dem Hinzufügen `addingresult.tpl` (wie
bisher) oder nach dem Entfernen das neue `removeresult.tpl`. `design/standard` und `design/admin` liefern
`addconfirm.tpl` und `removeresult.tpl`; ein Design, das auf sie zurückfällt, braucht nichts. Überschreibt Ihr Design
`addingresult.tpl`, wird es weiterhin verwendet; es erhält jetzt neben `redirect_url` auch `redirect_uri`. Code, der
auf die Adresse verlinkt und erwartet, dass sie abonniert, muss stattdessen das Formular senden:
`POST /notification/addtonotification/<node>` mit `ConfirmAddNotification` (und dem Formular-Token).

### Die Seite der Einstellungen

admin4 und admin4l haben eine neue Übersicht (siehe die Anleitung für Benutzer). Die Formularfelder der
Handler-Templates behalten ihre Namen (`NewRule_ezsubtree`, `RemoveRule_ezsubtree`, `SelectedRuleIDArray_ezsubtree[]`,
`Store`, `ReceiveDigest_ezgeneraldigest`, `DigestType_`, `Time_`, `Weekday_`, `Monthday_`,
`CollaborationHandlerSelection...`), sodass die Templates anderer Designs weiter funktionieren. Neu: `UseConfirm` im
Teilbaum-Formular lässt die View vor dem Entfernen nachfragen (ein Formular ohne es entfernt wie bisher sofort);
`FilterSubscriptions` und `ClearFilter`; die Karte für Zusammenarbeit speichert mit `SaveCollaboration` und teilt sich
nicht mehr die Schaltfläche `Store` (ein POST mit `Store` schaltet die Zusammenfassung aus, wenn ihre Felder fehlen,
daher muss ein Formular, das `Store` sendet, die Felder der Zusammenfassung mitführen). Die View akzeptiert neben
`(offset)` die neuen URL-Parameter `(q)` und `(class)`. Die View übergibt mehr Template-Variablen (`subscriptions`,
`subscription_total`, `subscription_all_total`, `subscription_limit`, `subscription_classes`, `filter_query`,
`filter_class`, `confirm_remove`, `notice`, `can_administrate`); einem Template eines eingebundenen Handlers müssen sie
ausdrücklich mit `include` übergeben werden.

Die Einstellungen werden jetzt beim Speichern geprüft: Ein anderer Typ der Zusammenfassung als täglich, wöchentlich
oder monatlich wird zu täglich, eine Uhrzeit, die keine der angebotenen Stunden ist, wird zu `0:00`, ein Wochentag
außerhalb der Woche wird zum ersten Wochentag, ein Tag des Monats wird auf 1 bis 31 begrenzt. Ein Benutzer, der die
Seite nie geöffnet hat (noch keine Zeile mit Einstellungen), kann Einstellungen der Zusammenfassung speichern. Das
Teilbaum-Formular lehnt Knoten ab, die der Benutzer nicht lesen darf, Knoten, die nicht existieren, und denselben
Knoten zweimal, und es entfernt nur die eigenen Regeln des Benutzers.

### Verarbeitete Ereignisse werden aufgeräumt

Vorher: Ein Ereignis, dessen Nachrichten für eine Zusammenfassung behalten wurden, blieb nach dem Versand der
Zusammenfassung für immer in der Tabelle. Jetzt endet `eZNotificationEventFilter::process()` damit, verarbeitete
Ereignisse zu entfernen, auf die nichts mehr wartet (`eZNotificationEvent::cleanupHandled()`). Auf einer
Testinstallation entfernte ein Lauf 7 solcher Ereignisse. Die Statusseite und `exp:notification:events cleanup` tun
dasselbe auf Anforderung und können Ereignisse nach Alter entfernen.

### Der Filter

`eZNotificationEventFilter::process( $eventIDList = null )` nimmt eine optionale Liste von Ereignis-IDs an und gibt ein
Array (`events`, `removed`, `kept`, `failed`) zurück, wo es bisher nichts zurückgab. Ein Handler, der eine Ausnahme
wirft, wird protokolliert und gezählt; die anderen Handler laufen trotzdem, und das Ereignis wird nicht wiederholt.
Bisher stoppte der Fehler den ganzen Durchlauf und ließ die übrigen Ereignisse liegen. Eine undefinierte Variable in
der Meldung für einen fehlenden Handler ist behoben.

### Kleinere Korrekturen

| Wo | Vorher | Jetzt |
|---|---|---|
| `eZNotificationCollection::addItem( $address, $sendDate )` | das Sendedatum wurde ignoriert (immer 0) | es wird gespeichert |
| `eZNotificationCollectionItem::fetchByDate( $date )` | die Bedingung überschrieb sich selbst und fand nichts Sinnvolles | findet die Elemente, die vor `$date` fällig sind |
| `eZGeneralDigestHandler::fetchHandlersForUser()` | ein undefinierter Index für einen Handler, der unter einem anderen Namen aufgeführt ist | der Handler wird über die ID gefunden oder weggelassen |
| `handle()` der Zusammenfassung | eine undefinierte Template-Variable, wenn für niemanden eine Zusammenfassung fällig war | behandelt |
| wöchentliche Zusammenfassung | ein undefinierter Index, wenn der Cronjob in einer anderen Sprache lief als der des Benutzers | der gespeicherte Name des Wochentags, der englische Name und 0 bis 6 werden akzeptiert |
| `eZCollaborationNotificationHandler::rules()` | suchte die Regeln nach E-Mail-Adresse | nach Benutzer-ID |
| `eZNotificationEventType::allowedTypes()` | las eine undefinierte globale Variable und eine Einstellung (`AvailableEventTypes`), die es nicht gibt | liest `AvailableNotificationEventTypes` |
| `eZMailNotificationTransport::send()` | eine einzelne, als Zeichenkette übergebene Adresse schlug fehl | akzeptiert |
| `eZNotificationFunctionCollection::eventContent()` | ein fataler Fehler bei einer unbekannten Ereignis-ID | gibt false zurück |

### Die E-Mail-Transporte

`eZFileTransport` schreibt nach `[MailSettings] FileTransportDirectory` von `site.ini`. Nicht gesetzt oder leer: wie
bisher `var/log/mail`. `eZMailNotificationTransport::observe( $callback, $suppress )` ist neu (verwendet von den
Statuszahlen und dem Probelauf).

### Cronjob

Der Teil `notification` läuft über `expNotificationService::run()`: gesperrt (ein zweiter Lauf wird mit einer Meldung
übersprungen), verzeichnet und auf der Statusseite angezeigt. `[CronjobPart-notification]` in `cronjob.ini` ist neu.
Die Ausgabezeilen „Starting notification event processing“ und „Done“ bleiben erhalten; „Done“ nennt jetzt, wie viele
Ereignisse und Nachrichten.

### Neu: Statusseite, Befehle, Views

`notification/status` und `notification/job` (Richtlinie `notification/administrate`), der Eintrag „Benachrichtigung“
im Menü Setup (`menu.ini [Leftmenu_setup] Links[notification]`, der seit 3.5 auskommentiert war) und die Befehle
`exp:notification:status|run|events|subscriptions` mit den Aliasen `exp:notify:*`. `notification/runfilter`
funktioniert weiter und läuft über den Dienst. Fügen Sie die Richtlinie `notification/administrate` den Rollen hinzu,
die sie sehen sollen (Administratoren haben sie).

## Aufräumen nach dem Upgrade

Eine Website, auf der der Cronjob lange nicht lief, hat viele ausstehende Ereignisse. Ein Lauf verarbeitet sie alle
(4 Sekunden für 7259 Ereignisse auf der Testinstallation). Wenn Sie möchten, machen Sie zuerst einen Probelauf:

```bash
./console exp:notification:status
./console exp:notification:run --dry-run
./console exp:notification:events cleanup --dry-run
./console exp:notification:subscriptions list --missing
```

Entfernen Sie Ereignisse, die älter sind, als Sie interessiert, mit `exp:notification:events cleanup --older-than=90d`
(siehe [die Befehle](../../spezifikationen/6.0/benachrichtigungen-cli.md)). E-Mails, die diese alten Ereignisse
ausgelöst hätten, sendet der Lauf, der sie verarbeitet: Ist der Rückstand alt, entfernen Sie ihn zuerst oder führen Sie
einmal einen Lauf mit `--mail-file-dir` aus, um die E-Mails zu verwerfen.

## E-Mails, die der Transport ablehnt, werden behalten und erneut versucht

Vorher: Der Handler der allgemeinen Zusammenfassung entfernte die Elemente der Zusammenfassung nach `send()`, ohne auf
das Ergebnis zu schauen, und die Handler für Teilbäume und Zusammenarbeit entfernten ihre Elemente vor `send()`.
Schlug der E-Mail-Transport fehl (der Mailserver war nicht erreichbar, der Datei-Transport konnte nicht schreiben),
gingen die Nachrichten verloren, und nichts zeigte es an. Jetzt:

- Eine Zusammenfassung, deren `send()` fehlschlägt, behält die Elemente dieser Adresse. Sie bleiben fällig, sodass der
  nächste Lauf (sein Zeitereignis ist später) sie erneut sendet. Das verarbeitete Ereignis, zu dem sie gehören, bleibt,
  bis ihre Elemente weg sind.
- Eine sofort gesendete Nachricht (Teilbaum, Zusammenarbeit) wird zuerst gesendet, und ihre Elemente werden erst
  entfernt, wenn der Transport sie angenommen hat. Hat er es nicht, bleiben die Elemente ohne Sendedatum, und das
  Ereignis bleibt verarbeitet; zu Beginn des nächsten Laufs sendet `eZNotificationEventFilter::retryUnsent()` sie
  erneut, mit Betreff und Text der Sammlung (ohne die Header für Threads).
- Eine Nachricht, die sich nach `[RuleSettings] RetryHours` von `notification.ini` (standardmäßig 72 Stunden; gezählt
  ab der Fälligkeit der Zusammenfassung oder bei einer sofort gesendeten Nachricht ab dem Zeitpunkt des Inhalts) immer
  noch nicht senden lässt, wird **aufgegeben**: Ihre Elemente werden entfernt, und sie wird als verworfen gezählt. Ein
  Element, dessen Adresse sich gar nicht anschreiben lässt (keine gültige Adresse), wird sofort verworfen, statt
  72 Stunden lang versucht zu werden. So wird eine dauerhaft abgelehnte Nachricht nicht ewig versucht.
- `eZNotificationEventFilter::process()` gibt neben seinen bisherigen Zahlen `send_failed` (Nachrichten, die der
  Transport in diesem Lauf abgelehnt hat), `dropped` und `retried` (jetzt nach einem früheren Fehlschlag gesendet)
  zurück; sie stehen im Datensatz des Laufs (`runs.jsonl`), sodass die Statusseite am Lauf die Kennzeichnungen
  **nicht gesendet** und **aufgegeben** zeigt.
- Die Statusseite und `exp:notification:status` melden ein Problem der Stufe Fehler „n Nachrichten konnten nicht an den
  E-Mail-Transport übergeben werden und warten auf den nächsten Lauf; jede wird nach n Stunden aufgegeben“, solange
  welche warten, „Der E-Mail-Transport hat im letzten Lauf n Nachrichten abgelehnt“ und eine Warnung, wenn Nachrichten
  aufgegeben wurden. Der Statusbefehl endet dann mit FAIL (Exit-Code 1).
- `exp:notification:run` endet mit `FAIL: the mail transport refused n message(s); ...` und Exit-Code 1, wenn ein
  Senden abgelehnt wurde, sodass eine Cron-E-Mail oder eine Überwachungsprüfung es zeigt; die erledigte Arbeit bleibt
  trotzdem erhalten. Der Cronjob-Teil gibt dasselbe als Fehlerzeile aus.
- Die Zahlen des Laufs (`mails`, `recipients`) zählen nur, was der Transport angenommen hat. Callbacks von
  `eZMailNotificationTransport::observe()` erhalten die Antwort des Transports als fünftes Argument `$sent`; ein mit
  vier Parametern geschriebener Callback funktioniert weiter.

Beispiel (eine Testinstallation; der „Transport“ war der Datei-Transport, gerichtet auf ein Verzeichnis, in das er
nicht schreiben kann, sodass keine E-Mail hinausgehen konnte):

```text
$ ./console exp:notification:run --event=21935 --mail-file-dir=/proc/nottest-no-such-directory
PASS: 1 event(s) handled, 0 removed, 1 kept for a digest, 0 message(s) to 0 recipient(s), 150 ms.
FAIL: the mail transport refused 1 message(s); they are kept and tried again at the next run (for 72 hours).        (exit code 1)

$ ./console exp:notification:run --no-time-event --event=1 --mail-file-dir=var/tmp/notification-mail/doc3
PASS: 0 event(s) handled, 0 removed, 0 kept for a digest, 1 message(s) to 1 recipient(s), 37 ms.
1 message(s) that failed earlier were sent now.
```

Was das nicht abdeckt: eine E-Mail, die der Transport annimmt und die der Mailserver später zurückweist (`sendmail` und
SMTP melden nur die Übergabe). Behalten Sie dafür das Mail-Log des Servers im Auge. `RetryHours` ist die einzige
Einstellung; es gibt keinen Zähler für Wiederholungen, daher hängt die Zahl der Versuche davon ab, wie oft der Cronjob
läuft.

## Verwandte Seiten

- [Die Anleitung für Benutzer](../../funktionen/6.0/benachrichtigungen.md), [die Anleitung für Administratoren](../../anleitungen/benachrichtigungen-administrator.md), [die Anleitung für Entwickler](../../anleitungen/benachrichtigungen-entwickler.md)
- [Die Spezifikation](../../spezifikationen/6.0/benachrichtigungen.md), [die INI-Referenz](../../spezifikationen/6.0/benachrichtigungen-ini.md), [die Befehlsreferenz](../../spezifikationen/6.0/benachrichtigungen-cli.md)
- [Befehle, Cronjob-Teile und Views als Klassen](cli-cronjob-und-view-abstraktionen.md)
