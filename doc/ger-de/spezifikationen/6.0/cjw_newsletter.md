# Spezifikation: die Extension cjw_newsletter

Diese Seite ist die Referenz für die Extension `cjw_newsletter` (4.1.17). Sie führt die Views der Administration und ihre
Parameter, die Konsolenbefehle, die Cronjob-Teile, die Hintergrundläufe, die Audit-Ereignisse, die Tabellen und die
Einstellungen auf, die Sie ändern können. Lesen Sie sie, wenn Sie den Newsletter installieren, zeitlich planen, per Skript
steuern oder debuggen. Den Rundgang für Redakteure und den Probedurchlauf ohne E-Mail-Versand finden Sie auf der
[Funktionsseite](../../funktionen/6.0/erweiterungen/cjw_newsletter.md).

## Kurz gesagt

- Einstieg in der Administration: `/newsletter/index` (der Reiter **Newsletter**): ein Dashboard mit den Listen und Abonnenten, den Ausgaben, den letzten Versänden, dem Transport und dem Postausgang, den letzten Läufen und den gefundenen Problemen.
- Drei Cronjob-Teile erledigen die Arbeit: `cjw_newsletter_mailqueue_create` und `cjw_newsletter_mailqueue_process` (der Teil `cjw_newsletter` führt beide aus) sowie `cjw_newsletter_mailbox` für Bounces.
- Fünf Konsolenbefehle führen denselben Code von Hand aus: `ext:cjw_newsletter:queue`, `mailbox`, `import`, `repair` und `status`.
- Zehn Tabellen `cjwnl_*`, angelegt aus `sql/<database>/schema.sql` oder aus `share/db_schema.dba`.
- Das linke Menü kommt in jedem Administrationsdesign aus `design/standard/templates/parts/newsletter/menu.tpl` (Navigationsteil `eznewsletternavigationpart`).

## Beispiel: einen Newsletter aus der Shell versenden und das Ergebnis ansehen

Stellen Sie den Transport auf Dateien um (siehe die Funktionsseite), veröffentlichen Sie eine Ausgabe, klicken Sie in der
Administration auf **Newsletter versenden** und führen Sie dann Folgendes aus:

```bash
./console ext:cjw_newsletter:queue --dry-run     # what a run would do, nothing changed
./console ext:cjw_newsletter:queue               # create the queue, send the mails
./console ext:cjw_newsletter:status              # lists, users, sends, outbox, last runs, problems
```

`queue` gibt `Queue: 1 sends, 4 items created.` und `Sent 4 mails, 0 failed, 1 sends finished.` aus; jede E-Mail ist
eine Datei im Postausgang. Ein zweiter Lauf zur selben Zeit gibt `Another queue run is active.` aus und endet mit 1.

## Modul `newsletter`

Views der Administration und der öffentlichen Formulare. Eine View behält seit 4.0 ihre URL; die Parameter unten sind die
Paare `(name)/value` der URL.

| View | Richtlinienfunktion | Parameter | Was sie tut |
|---|---|---|---|
| `index` | `index` | `(job)` | Das Dashboard; startet „Jetzt senden“, „Nur zählen“, „Mailkonten verarbeiten“ und „Entfernen“ im Hintergrund |
| `job` | `index` | `JobID` | Der Zustand eines Hintergrundlaufs als JSON (`status`, `result`, `log`) |
| `user_list` | `user_list` | `(q)`, `(status)`, `(list)`, `(sort)`, `(order)`, `(offset)`, `(limit)` | Benutzer: `status` ist `confirmed`, `pending`, `removed`, `bounced` oder `blacklisted`; `sort` ist `email`, `name`, `status`, `created` oder `id`; `limit` ist 10, 25, 50 oder 100 |
| `user_view`, `user_edit`, `user_create`, `user_remove` | `user_view`, `user_edit`, `user_create`, `user_remove` | `NewsletterUserId` | Ein Benutzer; das Entfernen entfernt auch die Abonnements |
| `subscription_list`, `subscription_view` | `subscription_list`, `subscription_view` | `NodeId`, `SubscriptionId` | Abonnements einer Liste |
| `blacklist_item_list` | `blacklist_item` | `(q)`, `(sort)`, `(order)`, `(offset)`, `(limit)` | Die Sperrliste; `sort` ist `created`, `email` oder `id` |
| `blacklist_item_add`, `blacklist_item_remove` | `blacklist_item` | keine | Hinzufügen (validiert); Entfernen fragt zuerst nach (`ConfirmRemoveButton`) |
| `mailbox_list`, `mailbox_edit` | `mailbox_list`, `mailbox_edit` | `MailboxId` | Mailkonten; das Formular validiert jedes Feld, zeigt das Passwort nie an, und das Entfernen fragt zuerst nach |
| `mailbox_item_list`, `mailbox_item_view` | `mailbox_item_list`, `mailbox_item_view` | `(job)`, `(limit)`, `(offset)`, `MailboxItemId` | Abgerufene E-Mails; „Alle E-Mails abrufen“ und „E-Mails untersuchen“ sind POST-Schaltflächen, die einen Hintergrundlauf starten |
| `import_list`, `import_view` | `import_list`, `import_view` | `(limit)`, `(offset)`, `ImportId` | CSV-Importe, die neuesten zuerst |
| `subscription_list_csvimport`, `subscription_list_csvexport` | `subscription_list_csvimport`, `subscription_list_csvexport` | `NodeId`, `ImportId` | CSV-Import (die Richtlinie `subscription_list_csvimport_import` erlaubt „Alle importieren“) und -Export |
| `send`, `send_abort`, `preview`, `preview_archive`, `archive` | `send`, `preview`, `archive` | siehe die View | Versand, Testmail, Archiv |
| `subscribe`, `subscribe_infomail`, `configure`, `unsubscribe` | `subscribe`, `configure`, `unsubscribe` | Hashes | Die öffentlichen Formulare; der Zurück-Link eines Formulars ist ausschließlich ein Pfad dieser Site |

## Konsolenbefehle

Alle beginnen mit `./console ext:cjw_newsletter:<name>` oder `php extension/cjw_newsletter/bin/php/<name>.php`, akzeptieren
`--help` und haben den Alias `nl-<name>`. Exit-Code 0 bedeutet Erfolg, 1 einen Fehler oder einen anderen Lauf, der die Sperre hält.

| Befehl | Optionen | Was er tut |
|---|---|---|
| `queue` | `--dry-run`, `--create-only`, `--send-only`, `--job=ID` | Bestätigt Benutzer, die auf ihren eZ-Benutzer gewartet haben, weckt die Versände, deren Zeitpunkt gekommen ist, legt die Warteschlange an und versendet die E-Mails |
| `mailbox` | `--dry-run`, `--collect-only`, `--parse-only`, `--job=ID` | Liest die aktiven Mailkonten und wertet die Bounces aus |
| `import` | `--import-id=N` (erforderlich), `--delimiter=comma\|semicolon\|pipe\|tab`, `--formats=0-1`, `--first-row-label`, `--dry-run`, `--job=ID` | Importiert eine hochgeladene CSV-Datei des Importordners |
| `repair` | `--dry-run`, `--job=ID` | Entfernt Abonnements entfernter Benutzer und nicht versandte E-Mails, die nie versendet werden können |
| `status` | keine | Gibt aus, was das Dashboard anzeigt |

`--job` setzt die Administration, wenn sie einen Befehl im Hintergrund startet: Der Befehl schreibt seinen Zustand nach
`var/cjw_newsletter/jobs/<id>.json` und seine Ausgabe nach `<id>.log`, und die Seite zeigt beides an, während sie
`newsletter/job/<id>` abfragt.

## Cronjob-Teile

| Teil | Skript | Was er tut |
|---|---|---|
| `cjw_newsletter` | beide Skripte unten | Legt die Warteschlange an und versendet dann |
| `cjw_newsletter_mailqueue_create` | `cronjobs/cjw_newsletter_mailqueue_create.php` | Wie `queue --create-only` |
| `cjw_newsletter_mailqueue_process` | `cronjobs/cjw_newsletter_mailqueue_process.php` | Wie `queue --send-only` |
| `cjw_newsletter_mailbox` | `cronjobs/cjw_newsletter_mailbox.php` | Wie `mailbox`; nicht im Teil `cjw_newsletter` enthalten |

Die Skriptdateien bestehen aus je einer Zeile; der Code liegt in `classes/runnable/cronjobs/`, und die Cronjob-Teile, die Befehle
und die Administration rufen alle `CjwNewsletterRunner` auf. Ein Lauf nimmt eine Dateisperre (`queue_create`, `queue_process`, `mailbox`,
`repair`, `import`), sodass sich zwei Läufe derselben Art nie überschneiden, gleich wer sie startet. Zeitpunkt, Benutzer (`cron`,
`console`, `job`) und Summen des letzten Laufs jeder Art werden in `ezsite_data` (`cjw_newsletter_last_*`) gespeichert.

## Audit-Ereignisse

Ein Ereignis je Lauf, geschrieben, wenn das Audit eingeschaltet ist: `system.cjw_newsletter.queue_create`, `queue_process`, `mailbox`,
`import` und `repair`. `object` ist `cjw_newsletter:<run>`, `after` enthält, wer den Lauf gestartet hat, und die Summen; `result` ist
`failed` mit dem Grund `run`, wenn der Lauf Fehler hatte. Ein Probelauf schreibt nichts. Der Zweig wird in
`settings/audit.ini.append.php` registriert.

## Einstellungen, die Sie ändern können

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `cjw_newsletter.ini` | `NewsletterCsvImportSettings` | `ImportInBackground` | `enabled` | `enabled` startet den Import von „Alle importieren“ als Befehl im Hintergrund; `disabled` (oder ein Server ohne `proc_open`) importiert innerhalb der Anfrage |
| `cjw_newsletter.ini` | `NewsletterMailSettings` | `TransportMethodCronjob` | `file` | `smtp`, `sendmail` oder `file` für die E-Mails der Warteschlange |
| `cjw_newsletter.ini` | `NewsletterMailSettings` | `FileTransportMailDir` | `var/log/mail` | Wohin `file` die `.eml`-Dateien schreibt; das Dashboard zählt sie |
| `cjw_newsletter.ini` | `NewsletterUserSettings` | `CustomFieldMappingArray[]`, `[CustomFieldMapping_<field>] Name` | nicht gesetzt | Öffentliche Namen der benutzerdefinierten Datenfelder in der Benutzeransicht |
| `cronjob.ini` | `CronjobPart-cjw_newsletter_mailbox` | `Scripts[]` | `cjw_newsletter_mailbox.php` | Der Teil, der die Mailkonten liest |

## Probleme, die das Dashboard meldet

| Problem | Bedeutung | Was zu tun ist |
|---|---|---|
| Tabellen fehlen | Die Tabellen `cjwnl_*` sind nicht angelegt | Legen Sie sie aus `share/db_schema.dba` an |
| Newsletter-Ordner fehlt | `RootFolderNodeId` nennt keinen Knoten | Legen Sie den Baum an (`CjwNewsletterClassInstaller::installTree()`) oder setzen Sie den Knoten |
| E-Mails werden in Dateien geschrieben | `TransportMethodCronjob` ist `file` | Für einen Probedurchlauf gedacht; stellen Sie zum Versenden auf `smtp` oder `sendmail` um |
| Standard-Absenderadresse | `EmailSender` ist noch `@example.*` | Setzen Sie sie in der INI-Datei oder bei jeder Liste |
| Verwaiste Einträge | Abonnements entfernter Benutzer, nicht versandte E-Mails entfernter Benutzer | Klicken Sie auf **Entfernen** oder führen Sie `repair` aus |
| Hängende Versände | Ein Versand wartet länger als einen Tag | Prüfen Sie, ob Cron `cjw_newsletter` ausführt |
| Gebouncte Benutzer, nicht ausgewertete E-Mails, kein aktives Mailkonto | Siehe den Text | Die Bounce-Seite und die Mailkonten |

## Verwandte Seiten

- [Funktionsseite](../../funktionen/6.0/erweiterungen/cjw_newsletter.md)
- [Ausführbare Befehle, Cronjob-Teile und Views](ausfuehrbare-befehle-cronjobs-ansichten.md)
- [Audit-Ereignismodell](audit-ereignismodell.md)
