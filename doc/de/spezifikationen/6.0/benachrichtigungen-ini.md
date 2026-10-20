# Spezifikation: Einstellungen für Benachrichtigungen

Jede Einstellung, die verändert, wie Benachrichtigungen erzeugt, verarbeitet, versendet und ausgeführt werden, mit ihrem Standardwert und ihrer Wirkung. Lesen Sie dies,
wenn Sie Benachrichtigungen für eine Installation einrichten oder eine Extension schreiben, die Handler oder Ereignistypen hinzufügt. Die
Standardwerte sind die aus den Dateien der Distribution: `settings/notification.ini`, `settings/site.ini`,
`settings/cronjob.ini` und `settings/menu.ini`. Ändern Sie sie in `settings/override/<file>.append.php` oder in den Einstellungen
eines Siteaccess oder einer Extension, nie in der Datei der Distribution (siehe [Ablageorte für INI-Overrides](ini-override-platzierungen.md)).
Leeren Sie nach einer Änderung den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.

## notification.ini

### [RuleSettings]

| Schlüssel | Standardwert | Wirkung |
|---|---|---|
| `LimitDeleteElements` | `50` | Wie viele Digest-Einträge der allgemeine Digest-Handler in einer Abfrage entfernt, nachdem er einen Digest gesendet hat. Das hält die Abfrage unter den Grenzen der Datenbank (Länge der Anweisung, Anzahl der Werte in `IN ()`). Ein Wert von 0 oder leer wird als 50 behandelt |
| `RetryHours` | `72` | Wie viele Stunden eine Benachrichtigung, die der Mail-Transport abgelehnt hat, bei jedem Lauf erneut versucht wird. Gezählt ab dem Zeitpunkt, zu dem der Digest fällig war, oder ab dem Zeitpunkt des Inhalts bei einer sofort gesendeten Nachricht. Danach wird sie aufgegeben, entfernt und als verworfen gezählt (Statusseite: **aufgegeben**). Ein Wert von 0 oder weniger bedeutet 72 |
| `RepositoryDirectories[]`, `ExtensionDirectories[]`, `Alias[]` | `kernel/notification/rules`, keine, `advanced=ezadvanced`, `general=ezgeneral`, `keyword=ezkeyword` | Überbleibsel der Regeltypen früherer Versionen. Nichts im Kernel liest sie mehr |

### [NotificationEventTypeSettings]

| Schlüssel | Standardwert | Wirkung |
|---|---|---|
| `RepositoryDirectories[]` | `kernel/classes/notification/event/` | Verzeichnisse, in denen nach einem Ereignistyp gesucht wird. Die Klassendatei des Typs `<name>` ist `<directory>/<name>/<name>type.php`, und sie registriert sich mit `eZNotificationEventType::register( '<name>', '<ClassName>' )` |
| `ExtensionDirectories[]` | keine | Namen von Extensions. In jeder wird in `extension/<name>/notificationtypes/<type>/<type>type.php` gesucht |
| `AvailableNotificationEventTypes[]` | `ezpublish`, `eznewcontent`, `ezcurrenttime`, `ezcollaboration` | Die Ereignistypen, die alle auf einmal geladen werden dürfen (`eZNotificationEventType::loadAndRegisterAllTypes()`). Ein Typ wird außerdem geladen, wenn zum ersten Mal ein Ereignis dieses Typs angelegt oder gelesen wird, ob er aufgeführt ist oder nicht. `eznewcontent` hat in der Distribution keine Klasse: Das Laden protokolliert „Notification event type not found“; kein Code im Kernel legt solche Ereignisse an |

### [NotificationEventHandlerSettings]

| Schlüssel | Standardwert | Wirkung |
|---|---|---|
| `RepositoryDirectories[]` | `kernel/classes/notification/handler/` | Verzeichnisse, in denen nach einem Handler gesucht wird. Die Klassendatei des Handlers `<name>` ist `<directory>/<name>/<name>handler.php`, und die Klasse ist `<name>handler` |
| `ExtensionDirectories[]` | keine | Namen von Extensions. In jeder wird in `extension/<name>/notification/handler/<name>/<name>handler.php` gesucht |
| `AvailableNotificationEventTypes[]` | `ezgeneraldigest`, `ezcollaborationnotification`, `ezsubtree` | Die Handler, die laufen, in dieser Reihenfolge. Jedes ausstehende Ereignis wird jedem aufgeführten Handler übergeben. Der Schlüssel heißt `AvailableNotificationEventTypes`, obwohl er Handler aufführt. Ein aufgeführter Handler, dessen Datei nicht gefunden wird, wird als Fehler protokolliert und übersprungen. Entfernen Sie einen Namen, um diesen Handler abzuschalten (zum Beispiel `ezcollaborationnotification` auf einer Site ohne Zusammenarbeit) |

Die ID, die sich ein Handler selbst gibt (`id_string`), benennt seine Einstellungs-Templates: `notification/handler/<id_string>/settings/edit.tpl`
und seine Mail-Templates in `notification/handler/<id_string>/view/`. Der Handler für die Zusammenarbeit ist als
`ezcollaborationnotification` aufgeführt, seine ID ist aber `ezcollaboration`, daher liegen seine Templates in `notification/handler/ezcollaboration/`.

### [TransportSettings]

| Schlüssel | Standardwert | Wirkung |
|---|---|---|
| `DefaultTransport` | `mail` | Der Benachrichtigungstransport, der verwendet wird, wenn ein Aufrufer keinen nennt. Die eingebauten Handler nennen immer `ezmail` (die Klasse `eZMailNotificationTransport`, die über den Mail-Transport aus `site.ini` sendet), daher verwenden sie diesen Wert nicht |
| `TransportPluginPath[]` | keine | Weitere Verzeichnisse, in denen nach einem Benachrichtigungstransport gesucht wird. Ein Transport `<name>` ist die Datei `<path>/<name>notificationtransport.php` mit der Klasse `<name>notificationtransport`. `kernel/classes/notification/` wird immer zuerst durchsucht |

### [MailSettings]

| Schlüssel | Standardwert | Wirkung |
|---|---|---|
| `EmailSender` | leer | Die Absenderadresse der Benachrichtigungsmails. Ist sie leer, wird `site.ini` `[MailSettings] EmailSender` verwendet, danach dessen `AdminEmail`. Die Statusseite meldet einen Fehler, wenn kein gültiger Absender übrig bleibt |

## site.ini

### [MailSettings]

| Schlüssel | Standardwert | Wirkung auf Benachrichtigungen |
|---|---|---|
| `Transport` | `sendmail` | Wie E-Mails den Server verlassen: `sendmail`, `smtp` oder `file`. Mit `file` werden die E-Mails in Dateien geschrieben, und nichts wird versendet. Die Statusseite weist darauf hin |
| `TransportAlias[]` | `file=eZFileTransport`, `sendmail=eZSendmailTransport`, `smtp=eZSMTPTransport` | Ordnet einem Wert von `Transport` seine Klasse zu. Fügen Sie einen hinzu, um einen eigenen Transport mitzubringen |
| `FileTransportDirectory` | leer (bedeutet `var/log/mail`) | Das Verzeichnis, in das der Transport `file` schreibt. Nicht in der Datei der Distribution; in dieser Version hinzugefügt, siehe [die Upgrade-Hinweise](../../../en/us/bc/6.0/notification-ui-and-commands.md). Relativ zum Wurzelverzeichnis der Installation |
| `EmailSender`, `AdminEmail` | leer, `nospam@ez.no` | Ersatzabsender, siehe oben. Ändern Sie `AdminEmail` in jeder Installation |
| `EmailReplyTo` | leer | Antwortadresse von E-Mails, die mit den Standardwerten von `eZMail` erzeugt werden |
| `UserAgent` | leer | Der Header `User-Agent` jeder über `eZMail` erzeugten E-Mail, Benachrichtigungsmails eingeschlossen (nur ASCII). Leer behält `Exponential, Version <version>` (sendmail, file) und `Apache Zeta Components` (SMTP) bei |
| `DebugSending`, `DebugReceiverEmail` | `disabled`, leer | Wenn aktiviert, senden die Transporte sendmail und SMTP jede E-Mail an `DebugReceiverEmail` statt an die echten Empfänger. Ein Sicherheitsnetz auf einer Staging-Kopie einer echten Site. Gilt nicht für den Transport `file` |
| `TransportServer`, `TransportPort`, `TransportConnectionType`, `TransportUser`, `TransportPassword`, `SenderHost` | leer, `25`, leer, leer, leer, `localhost` | SMTP-Verbindung |
| `SendmailOptions[]`, `SendmailInsertUndisclosedRecipient` | keine, `enabled` | Sendmail-Optionen; Benachrichtigungsmails haben ihre Empfänger in `Bcc` und `To: undisclosed-recipients:;` |
| `ExcludeHeaders[]` | keine | Header, die in der Nachricht weggelassen werden (nur SMTP) |

### Weitere wichtige Einstellungen

| Datei, Block, Schlüssel | Standardwert | Wirkung |
|---|---|---|
| `site.ini` `[SiteSettings] SiteURL` | je Installation | Der Hostname, der in die Links und die Fußzeile der E-Mails sowie in den Betreff des Digests geschrieben wird |
| `site.ini` `[DatabaseSettings] Transactions` | `enabled` | Nötig für `exp:notification:run --dry-run`, das eine Transaktion zurückrollt |
| `site.ini` `[RunnableSettings] Listeners[]`, `Implementation[]` | keine | Sich in die Befehle, den Cronjob-Teil und die Views einklinken oder sie ersetzen, siehe [die ausführbaren Klassen](ausfuehrbare-befehle-cronjobs-ansichten.md) |
| `site.ini` `[ExtensionSettings] ActiveExtensions[]` | je Installation | Eine Extension, die Handler oder Ereignistypen hinzufügt, muss aktiv sein |
| Die Mail-Templates | | Gewöhnliche Templates: Überschreiben Sie sie wie jedes andere, siehe [Reihenfolge der Template-Overrides](../../../en/us/features/6.0/template-override-ordering.md) |

## cronjob.ini

| Block, Schlüssel | Standardwert | Wirkung |
|---|---|---|
| `[CronjobPart-frequent] Scripts[]` | enthält `notification.php` | Die Gruppe, die `php runcronjobs.php frequent` ausführt, zusammen mit den Teilen für Workflow, Inhalts-Jobs und Audit. Hier läuft der Benachrichtigungsteil normalerweise |
| `[CronjobPart-notification] Scripts[]` | `notification.php` | Der Benachrichtigungsteil für sich allein: `php runcronjobs.php notification` |
| `[CronjobSettings] MaxScriptExecutionTime` | `43200` | Sekunden, nach denen ein gesperrter Cronjob-Teil vom nächsten Lauf übernommen wird (nach der doppelten Zeit wird der alte beendet). Ein Wert `MaxScriptExecutionTime` pro Teil in `[CronjobPart-<name>]` überschreibt ihn |

Der Benachrichtigungsdienst hat eine eigene Laufsperre (`var/<var dir>/notification/run.lock`), die keine Einstellung ist: Sie wird
vom System freigegeben, wenn der Prozess endet, sodass ein abgestürzter Lauf keine Sperre hinterlässt.

## menu.ini

| Block, Schlüssel | Standardwert | Wirkung |
|---|---|---|
| `[Leftmenu_setup] Links[notification]` | `notification/status` | Der Eintrag „Benachrichtigungen“ im Setup-Menü, der die Statusseite öffnet |
| `[Leftmenu_setup] PolicyList_notification[]` | `notification/administrate` | Wer ihn sieht |
| `[Leftmenu_my] Links[my_notifications]`, `PolicyList_my_notifications[]` | `notification/settings`, `notification/use` | Der Eintrag „Meine Benachrichtigungseinstellungen“ |

## Richtlinien

| Modul / Funktion | Gewährt Zugriff auf |
|---|---|
| `notification/use` | `settings`, `addtonotification` (die eigenen Einstellungen eines Benutzers) |
| `notification/administrate` | `status`, `job`, `runfilter` |

## Geschriebene Dateien

| Pfad (unter dem var-Verzeichnis des Siteaccess, zum Beispiel `var/site/`) | Inhalt |
|---|---|
| `notification/runs.jsonl` | Eine JSON-Zeile pro Lauf, die neuesten 200 werden aufbewahrt |
| `notification/run.lock` | Die Laufsperre; enthält die Prozess-ID und die Startzeit des laufenden Laufs |
| `notification/jobs/<id>.jsonl`, `<id>.log` | Fortschritt und Ausgabe eines von der Statusseite gestarteten Laufs; die neuesten 20 werden aufbewahrt |

## Siehe auch

- [Die Spezifikation der Benachrichtigungen](benachrichtigungen.md), [die Befehle](benachrichtigungen-cli.md)
- [Die Anleitung für Administratoren](../../anleitungen/benachrichtigungen-administrator.md), [die Anleitung für Entwickler](../../anleitungen/benachrichtigungen-entwickler.md)
- [Ablageorte für INI-Overrides](ini-override-platzierungen.md)
