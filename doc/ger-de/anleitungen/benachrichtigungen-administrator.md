# Benachrichtigungen: ausführen, die Statusseite lesen und Probleme beheben

Diese Anleitung richtet sich an Administratoren, die das Benachrichtigungssystem in Gang halten: an die Person, die den Cronjob einrichtet, die
Statusseite ansieht, einen Lauf gefahrlos ausprobiert und herausfindet, warum eine E-Mail nicht angekommen ist. Am Ende können Sie den
Benachrichtigungs-Cronjob starten, jede Zahl auf der Statusseite lesen, einen Lauf ausprobieren, ohne etwas zu senden, alte Ereignisse aufräumen und
die üblichen Probleme lösen. Etwa 30 Minuten, eine Shell auf dem Server und ein Administrator-Login. Befehle werden im
Wurzelverzeichnis der Installation ausgeführt; ergänzen Sie `--allow-root-user`, wenn Sie als root arbeiten. Jeder Befehl und jede Ausgabe in dieser Anleitung lief auf
einer Testinstallation, deren Mail-Transport auf Dateien gezwungen war, mit Testadressen unter `nottest.invalid`: Es wurde keine E-Mail gesendet.
Namen, IDs und Zeiten weichen auf Ihrer Installation ab.

Was Benutzer sehen, steht in [der Anleitung für Benutzer](../../eng-us/features/6.0/notifications.md); die Befehle vollständig in
[der Befehlsreferenz](../spezifikationen/6.0/benachrichtigungen-cli.md).

## 1. Wie es funktioniert, in einer Minute

| Schritt | Wer |
|---|---|
| Das Veröffentlichen von Inhalten oder ein Schritt in einem Element der Zusammenarbeit schreibt ein **Ereignis** in die Datenbank. | der Kernel, sofort |
| Jedes ausstehende Ereignis wird an die **Handler** übergeben: Sie ermitteln, wer dem Inhalt folgt, schreiben für jeden Empfänger eine **Nachricht** und senden sie entweder sofort oder halten sie für einen **Digest** zurück. | der Benachrichtigungs-**Lauf** |
| Ein **Zeitereignis**, das jeder Lauf erzeugt, macht die fälligen Digests versandbereit: eine E-Mail je Adresse. | derselbe Lauf |
| Ein Ereignis, auf das nichts mehr wartet, wird entfernt. | derselbe Lauf |

Nichts wird gesendet, bevor ein Lauf stattfindet. Ein Lauf wird vom Cronjob gestartet (der normale Weg), von der Schaltfläche **Jetzt ausführen** auf der
Statusseite oder von `exp:notification:run`. Findet kein Lauf statt, häufen sich die Ereignisse in der Datenbank, und niemand erhält E-Mails.

## 2. Den Cronjob einrichten

Der Benachrichtigungsteil gehört zur Cronjob-Gruppe `frequent` (zusammen mit den Teilen für Workflows und Inhaltsjobs) und kann auch allein laufen.
Sehen Sie nach, was installiert ist:

```bash
php runcronjobs.php -s site --list --allow-root-user | grep -n "CronjobPart-notification\|CronjobPart-frequent\|notification.php"
```

Erwartet (das Skript ist in beiden Gruppen aufgeführt; die Wiederholungen sind die drei durchsuchten Verzeichnisse):

```text
35:CronjobPart-frequent:
36:		 cronjobs/notification.php
...
62:CronjobPart-notification:
63:		 cronjobs/notification.php
```

Führen Sie ihn einmal von Hand aus:

```bash
php runcronjobs.php -s site notification --allow-root-user
```

```text
Using siteaccess site for cronjob
Running cronjob part 'notification'
Running cronjobs/notification.php at: 10/04/2026 10:40 pm
Starting notification event processing

Done: 1 events, 0 messages to 0 recipients
Completing cronjobs/notification.php at: 10/04/2026 10:40 pm
Elapsed time: 00:00:00
```

Ersetzen Sie `site` durch Ihren öffentlichen Siteaccess. Das Beispiel lief auf einer Installation, auf der nichts ausstand und niemand etwas
folgte, sodass keine E-Mail entstehen konnte; auf einer Live-Installation sendet dieser Lauf E-Mails über den Transport aus `site.ini`.

Tragen Sie ihn dann in die Crontab ein. Wie oft? Ein Lauf ist günstig, wenn nichts aussteht (etwa 20 ms in den Beispielen), daher sind alle
fünf Minuten ein guter Standard; ein Digest wird beim ersten Lauf nach seiner Stunde gesendet, die Häufigkeit bestimmt also auch, wie spät ein Digest kommen kann.

```cron
*/5 * * * * cd /path/to/installation && php runcronjobs.php -s site frequent --allow-root-user >> var/log/cron-frequent.log 2>&1
```

Wenn Ihre Crontab die Gruppe `frequent` bereits ausführt, ist der Benachrichtigungsteil schon enthalten; fügen Sie keine zweite Zeile hinzu.
`./console crontab:edit` öffnet die Crontab. Die Anleitung zu Cronjobs ist [Betrieb einer Site](betrieb-einer-website.md#3-cronjobs-ausführen).
Wenn Sie Velocity betreiben, kann dessen eigener [Scheduler](../../eng-us/features/6.0/velocity-scheduler.md) den Teil ohne den System-Cron starten.

Zwei Dinge schützen vor doppelten Läufen: `runcronjobs.php` sperrt jeden Teil, und der Benachrichtigungsdienst hat eine eigene Sperre
(Abschnitt 6), sodass sich ein Cron-Lauf, ein Konsolen-Lauf und **Jetzt ausführen** nie überschneiden. Ein zweiter Lauf wird abgewiesen, nicht in eine Warteschlange gestellt.

## 3. Die Statusseite

Öffnen Sie **Setup > Benachrichtigungen** (`/notification/status`; Richtlinie `notification/administrate`). Im Menü des Dashboards
hat **Meine Benachrichtigungen** für Administratoren eine Schaltfläche **Benachrichtigungs-Status**. Die Konsolenvariante ist
`./console exp:notification:status`.

### Jede Zahl auf der Statusseite

| Wo | Was gezählt wird |
|---|---|
| **Ereignisse, die auf Behandlung warten** | Ereignisse mit dem Status ausstehend. Sie warten auf den nächsten Lauf. Nach einem Lauf sollte der Wert 0 sein (oder die wenigen, die seitdem entstanden sind) |
| **Für einen Digest zurückgehaltene Nachrichten** (und „n fällig“) | Empfängereinträge mit einem Sendedatum in der Zukunft. „Fällig“ sind die, deren Datum verstrichen ist: Der nächste Lauf sendet sie |
| **Abonnements von n Benutzern** | Zeilen der Teilbaum-Abonnements und wie viele Benutzer sie haben |
| **Nachrichten an n Empfänger, letzte 24 Stunden** | Die Summe der Nachrichten und Empfänger der Läufe des letzten Tages, die im Laufprotokoll stehen. Eine E-Mail mit 5 Empfängern zählt als 1 Nachricht und 5 Empfänger |
| **Letzter Lauf** (und wer ihn gestartet hat) | Die Zeit des letzten verzeichneten Laufs und seine Quelle: `cron`, `console` oder `web` |
| **Letzte Läufe** | Die letzten zehn: Zeit, Quelle, behandelte Ereignisse, gesendete Nachrichten / Empfänger, Ergebnis (OK, n fehlgeschlagene Handler oder der Fehler) und die Dauer |
| **Ereignisse** | Die zehn neuesten Ereignisse: ID, Typ, Status (Wartend, oder Behandelt mit der Zahl der Nachrichten, die auf einen Digest warten) und wann der Inhalt, um den es geht, erzeugt wurde. Ein Strich bedeutet, dass das Alter unbekannt ist (der Inhalt ist weg) |
| **Abonnements** | Die ersten Abonnements mit Benutzer und Eintrag; ein Badge markiert die, deren Inhalt nicht mehr existiert |
| **E-Mail und Handler** | Der Mail-Transport und der Absender, die Benachrichtigungen verwenden, die aktiven Handler, wie viele Benutzer einen täglichen, wöchentlichen und monatlichen Digest gewählt haben, wie viele Regeln der Zusammenarbeit es gibt, wie viele Nachrichten jetzt auf den Versand warten |

„Jetzt wartende Nachrichten“ sollte außer während eines Laufs 0 sein: Einträge ohne Sendedatum werden in dem Lauf gesendet, der sie erzeugt.

### Die Problemliste

Über den Zahlen stehen Hinweise von drei Arten: **Problem** (rot), **Achtung** (orange), **Hinweis** (blau); oder „Alles in
Ordnung.“. Die genauen Texte, erzeugt vom selben Code wie die Seite (ein ausgedachter Status für jeden Fall):

```text
[events wait, no run recorded]
  error: 12 events wait and no run of the notification cronjob is recorded. Add the cronjob part "frequent" to the crontab, or run exp:notification:run.
[events wait, last run two hours ago]
  error: 12 events wait and the last run was more than an hour ago (2026-10-04 20:40). Is the notification cronjob running?
[no run recorded, nothing waits]
  warning: No run is recorded yet. The notification cronjob (part "frequent") sends the notifications.
[last run failed]
  error: The last run failed: RuntimeException: example
[a handler failed]
  warning: A handler failed on 3 events in the last run; see the debug log.
[digest overdue]
  warning: 4 digest messages are overdue; a run sends them.
[handled orphans]
  info: 7 handled events have nothing left to send; Remove old events clears them.
[file transport, no sender, no subtree handler]
  info: Mail is written to files, not sent (site.ini MailSettings Transport=file).
  error: There is no valid sender address: set EmailSender in notification.ini or site.ini.
  warning: The subtree handler is not available, so no one is notified about published content.
```

Drei weitere stammen von E-Mails, die der Transport abgelehnt hat (Abschnitt 7): „n Nachrichten konnten nicht an den Mail-Transport übergeben werden und warten auf den nächsten Lauf; jede wird nach 72 Stunden aufgegeben.“, „Der Mail-Transport hat im letzten Lauf n Nachrichten abgelehnt. Prüfen Sie den Mailserver und site.ini MailSettings.“ (beide mit der Stufe Fehler) und „n Nachrichten wurden im letzten Lauf aufgegeben: älter als 72 Stunden oder für eine Adresse, an die nicht gemailt werden kann.“ (Warnung). Unter **Letzte Läufe** trägt ein solcher Lauf ein rotes Badge **nicht gesendet** oder ein oranges Badge **aufgegeben**.

Zwei weitere Warnungen ergeben sich aus den Daten: „n Abonnements verweisen auf Inhalt, den es nicht mehr gibt.“ und „n Abonnements gehören
zu Benutzern, die es nicht mehr gibt.“ Bedeutung und Abhilfe stehen jeweils in Abschnitt 7.

### Jetzt ausführen und die Vorschau

**Vorschau (Probelauf)** startet `exp:notification:run --dry-run` im Hintergrund und zeigt seine Ausgabe in einem Konsolenfeld auf
der Seite. Sie listet jede Nachricht auf, die gesendet würde (den Betreff und die Zahl der Empfänger), und ändert nichts.
**Jetzt ausführen** fragt „Die Benachrichtigungen jetzt ausführen? Fällige Nachrichten werden gesendet.“ und startet dann einen echten Lauf im Hintergrund.
Beide zeigen die Ausgabe, sobald sie entsteht, und enden mit „Fertig. Laden Sie die Seite neu, um die neuen Zahlen zu sehen.“ oder „Der Lauf ist fehlgeschlagen oder
wurde beendet. Siehe die Zeilen darüber.“ Während ein Lauf im Gang ist, sind beide Schaltflächen deaktiviert, und ein Hinweis nennt den Prozess. Ein
hier gestarteter Lauf wird mit der Quelle `web` verzeichnet.

Der Lauf ist ein eigener Prozess, der mit `setsid` gestartet wird, und läuft daher weiter, wenn Sie die Seite schließen. Er braucht `proc_open`, `setsid`
(util-linux) und die PHP-Kommandozeile; fehlt eines davon, sagt die Seite das („Jetzt ausführen braucht einen Hintergrundprozess: ...“)
und bietet den Konsolenbefehl an. Seine Ausgabe wird außerdem in `jobs/<id>.log` neben der Fortschrittsdatei aufbewahrt (Abschnitt 7).
Ohne JavaScript führt die Schaltfläche **Benachrichtigungsfilter ausführen** der alten Seite (`/notification/runfilter`) einen Lauf innerhalb der Anfrage aus.

### Aufräumen

| Schaltfläche | Wirkung |
|---|---|
| **Behandelte Ereignisse ohne Restnachrichten entfernen** | Entfernt Ereignisse, die behandelt wurden und keine wartende Nachricht haben. Frühere Versionen haben sie zurückgelassen; ein Lauf entfernt sie jetzt selbst |
| **Ereignisse entfernen, die älter sind als** 30 Tage / 90 / 180 / 1 Jahr | Entfernt nach einer Bestätigung die Ereignisse und ihre wartenden Nachrichten, deren Inhalt älter ist. Ereignisse unbekannten Alters bleiben erhalten. Mindestens ein Tag |
| **n Abonnements entfernen, deren Inhalt fehlt** | Entfernt Abonnements auf gelöschte Inhalte (nur angezeigt, wenn es welche gibt) |

Konsole: `exp:notification:events cleanup --dry-run`, `... cleanup --older-than=30d`, `exp:notification:subscriptions
remove-missing`. Probieren Sie es zuerst mit `--dry-run`:

```text
$ ./console exp:notification:events cleanup --older-than=30d --dry-run
Would remove: 1 handled event(s) with nothing left to send
Would remove: 1 event(s) older than 30d (1 newer kept, 0 of unknown age left alone)
PASS
```

## 4. Einen Lauf ausprobieren, ohne E-Mails zu senden

Drei sichere Wege, vom leichtesten an:

1. **Probelauf**: `./console exp:notification:run --dry-run`. Er läuft in einer Transaktion, die zurückgerollt wird, und übergibt nichts
   an den Mail-Transport. Adressen werden maskiert (`n***@nottest.invalid`), außer Sie ergänzen `--addresses`.
2. **Datei-Transport**: `./console exp:notification:run --mail-file-dir=var/tmp/notification-mail` führt einen echten Lauf aus (Ereignisse werden
   behandelt und entfernt, Digests gesendet), aber die E-Mails werden als Dateien in dieses Verzeichnis geschrieben, nur für diesen Prozess. Öffnen Sie eine
   Datei, um genau zu sehen, was hinausginge.
3. **Nur ein Ereignis**: Ergänzen Sie `--event=<id>` (die ID finden Sie mit `exp:notification:events list`), damit ein Lauf die
   übrigen Ereignisse nicht anrührt.

Eine vollständige Probe auf einer Testinstallation (ein Testbenutzer folgt einem Ordner und hat einen täglichen Digest um 8:00 Uhr; ein Artikel wird
veröffentlicht; das Ereignis ist 21599):

```text
$ ./console exp:notification:run --event=21599 --mail-file-dir=var/tmp/notification-mail/doc2
PASS: 1 event(s) handled, 0 removed, 1 kept for a digest, 0 message(s) to 0 recipient(s), 79 ms.

$ ./console exp:notification:events list --status=handled
  21599  handled  ezpublish        2026-10-04 22:40:31  1 message(s) waiting
PASS: 1 event(s) shown

$ ./console exp:notification:run --at="2026-10-05 06:00" --mail-file-dir=var/tmp/notification-mail/doc2
Time event at 2026-10-05 06:00:00.
PASS: 13 event(s) handled, 13 removed, 0 kept for a digest, 0 message(s) to 0 recipient(s), 61 ms.

$ ./console exp:notification:run --dry-run --at="2026-10-05 09:00" --mail-file-dir=var/tmp/notification-mail/doc2
  would send "[example.com] Digest for Sunday October 04 2026 10:40:33 pm" to 1 recipient(s): n***@nottest.invalid
PASS: dry run, 1 event(s) handled, 1 message(s) would be sent, 0 event(s) would be kept for a digest.

$ ./console exp:notification:run --at="2026-10-05 09:00" --mail-file-dir=var/tmp/notification-mail/doc2
PASS: 1 event(s) handled, 1 removed, 0 kept for a digest, 1 message(s) to 1 recipient(s), 125 ms.
```

Der Lauf um 06:00 Uhr hat nichts gesendet: Die Digest-Stunde ist 8:00 Uhr. Um 09:00 Uhr geht der Digest hinaus. (Die 13 Ereignisse im Lauf um 06:00 Uhr waren
andere ausstehende Ereignisse der Installation; ein Lauf ohne `--event` behandelt sie alle.)

Testen Sie nie auf einer Live-Site mit dem echten Transport und echten Abonnenten. Setzen Sie auf einer Kopie einer Produktivdatenbank außerdem
`[MailSettings] DebugSending=enabled` und `DebugReceiverEmail=` auf Ihre eigene Adresse in `site.ini`, damit jede E-Mail an Sie geht.

## 5. Wohin die E-Mails gehen

`[MailSettings] Transport` in `site.ini` entscheidet: `sendmail`, `smtp` oder `file`. Mit `file` wird die E-Mail nach
`var/log/mail` (oder in das Verzeichnis aus `FileTransportDirectory`) als `<time>-<number>.mail` geschrieben, eine Datei je Nachricht, und keine
E-Mail verlässt den Server. Siehe [die INI-Referenz](../spezifikationen/6.0/benachrichtigungen-ini.md). Der Absender ist `EmailSender` aus
`notification.ini`, sonst aus `site.ini`, sonst `AdminEmail`.

## 6. Velocity und Apache

| | Apache mit PHP-FPM | Velocity |
|---|---|---|
| Der Cronjob | der System-Cron führt `php runcronjobs.php` aus; nicht betroffen | dasselbe, oder der Velocity-Scheduler |
| Jetzt ausführen / Vorschau | die Seite startet die PHP-Kommandozeile mit `setsid`; sie läuft als Benutzer des Pools (auf der Testinstallation: der Site-Benutzer, PHP aus der Plesk-8.5-Installation) | derselbe Code, gestartet über den eigenen Prozess-Helfer des Servers |
| Dateien in `var/` | angelegt von dem, der ausführt: dem Benutzer des Pools für das Web, root für einen Konsolen-Lauf | Velocity läuft als root, daher gehören die Dateien, die es anlegt, root |
| Nachdem Sie PHP-Code geändert haben | das PHP-FPM neu laden, das die Site bedient (unter Plesk: seine eigene Unit `plesk-php85-fpm`, nicht das System-`php-fpm`), dann den Inhalts-Cache leeren | Velocity neu starten (seine Worker laden Klassen einmal), dann seinen Antwort-Cache leeren |
| Änderungen an Templates | geleert mit `php bin/php/ezcache.php --clear-all`; neue Template-Dateien erfordern zusätzlich das Leeren des Template-Override-Caches | dasselbe, dann den Antwort-Cache von Velocity leeren |

Weil ein Konsolen-Lauf (root) und ein Web-Lauf (der Site-Benutzer) sich `var/<var dir>/notification/` teilen, werden die Sperrdatei und das Laufprotokoll
für alle beschreibbar angelegt. Wenn Sie dieses Verzeichnis von Hand verschieben oder neu anlegen, halten Sie es für beide beschreibbar. Die Statusseite
und die Konsole müssen dasselbe Laufprotokoll sehen: Führen Sie die Konsole mit demselben Siteaccess (`-s`) aus wie das Web, sonst
unterscheidet sich das var-Verzeichnis.

Was auf der Testinstallation erprobt wurde: der Cronjob-Teil, jeder Befehl und die Statusseite mit **Jetzt ausführen** und der
Vorschau unter Apache mit PHP-FPM, in Chromium und Firefox. Jetzt ausführen unter Velocity wurde bei diesem Durchgang nicht erprobt; es verwendet
denselben Befehl und dieselbe Fortschrittsdatei.

## 7. Fehlerbehebung

| Symptom | Ursache und Abhilfe |
|---|---|
| „Ereignisse warten und es ist kein Lauf ... verzeichnet“, Tausende ausstehender Ereignisse | Der Cronjob läuft nicht (nicht in der Crontab, falscher Siteaccess, falscher Benutzer). Führen Sie `php runcronjobs.php -s site notification` von Hand aus und lesen Sie die Ausgabe, dann korrigieren Sie die Crontab. Ein Rückstau wird von einem einzigen Lauf abgearbeitet (eine Testinstallation hatte 7259 Ereignisse aus Wochen ohne Lauf: ein Lauf dauerte 4 Sekunden). Der Lauf wird außerdem nur im var-Verzeichnis des Siteaccess verzeichnet, mit dem er lief |
| „Another run holds the lock“ | Ein Lauf ist im Gang (prüfen Sie `exp:notification:status`: „running now ... process n“) oder ein früherer Prozess lebt noch (`ps`). Die Sperre wird freigegeben, wenn der Prozess endet, auch wenn er abgestürzt ist; eine übrig gebliebene Datei `run.lock` mit altem Inhalt ist harmlos. Hängt ein Prozess stundenlang, beenden Sie ihn; `runcronjobs.php` übernimmt einen Teil außerdem nach `MaxScriptExecutionTime` |
| „The lock file ... cannot be opened (permissions ...)“ | Die Datei gehört einem anderen Benutzer und ist nicht beschreibbar. Machen Sie `var/<var dir>/notification/run.lock` und `runs.jsonl` für den Web-Benutzer und den Konsolen-Benutzer beschreibbar (`chmod 666`) |
| Nichts wird gesendet, obwohl Ereignisse behandelt wurden | Hat der Inhalt Abonnenten (`exp:notification:subscriptions list`)? Haben sie `content/read` dafür? Ist der Benutzer aktiviert? Ist der Knoten verborgen? Ist der Transport `file`? Achten Sie auf den Hinweis **E-Mails werden in Dateien geschrieben** auf der Statusseite. Machen Sie einen Probelauf: Er listet auf, was gesendet würde |
| Ein Benutzer erhält direkt nach dem Veröffentlichen nichts | Er hat einen Digest gewählt (die Nachrichten warten; siehe `items kept for a digest`) oder der Cronjob ist noch nicht gelaufen |
| Der Digest wurde nicht gesendet | Ein Digest wird vom ersten Lauf nach seiner Zeit gesendet. Prüfen Sie, dass nach der Stunde Läufe stattfinden (`Recent runs`); die Statusseite meldet „n Digest-Nachrichten sind überfällig“, wenn fällige Einträge warten. Wöchentlich: Der Wochentag wird mit seinem Namen gespeichert; ein Lauf in einer anderen Sprache akzeptiert den englischen Namen und Zahlen |
| Der Digest kam zur falschen Stunde | Die Stunde richtet sich nach der Zeitzone von PHP auf dem Server. Web und Kommandozeile können unterschiedliche Einstellungen für `date.timezone` haben: Vergleichen Sie `php -i | grep timezone` mit dem PHP des Pools |
| Die Statusseite meldet „Es ist noch kein Lauf verzeichnet“, nachdem die Crontab eingerichtet wurde | Der Cron führt einen anderen Siteaccess oder ein anderes var-Verzeichnis aus, oder er läuft als Benutzer, der das Protokoll nicht schreiben kann. Führen Sie ihn von Hand mit `-s` des Siteaccess aus, den die Administration verwendet |
| **Jetzt ausführen** tut nichts oder meldet einen Fehler | Lesen Sie `var/<var dir>/notification/jobs/<id>.log` (die neueste). Ein fehlendes `setsid` oder eine fehlende PHP-Kommandozeile wird auf der Seite gemeldet. Ein fataler Fehler im Befehl landet im Log |
| „n behandelte Ereignisse haben nichts mehr zu senden“ | Überbleibsel aus der Zeit, bevor das System sie selbst entfernte; drücken Sie die Schaltfläche zum Aufräumen oder verwenden Sie `exp:notification:events cleanup` |
| „n Abonnements verweisen auf Inhalt, den es nicht mehr gibt“ | Der Inhalt wurde gelöscht, nachdem ihm jemand gefolgt war. Entfernen Sie sie: per Schaltfläche oder mit `exp:notification:subscriptions remove-missing` |
| „Ein Handler ist bei n Ereignissen fehlgeschlagen“ | Ein Handler hat einen Fehler geworfen; das Debug-Log (siehe [Betrieb einer Site](betrieb-einer-website.md)) nennt das Ereignis und die Meldung. Die übrigen Handler sind trotzdem gelaufen; das Ereignis wird nicht erneut versucht |
| Eine E-Mail konnte nicht gesendet werden („Der Mail-Transport hat n Nachrichten abgelehnt“) | Der Mailserver oder die Transport-Einstellungen sind schuld (`MailSettings` in `site.ini`: Pfad zu `sendmail`, SMTP-Server und Login). Die Nachrichten gehen nicht verloren: Ein Digest oder eine sofort gesendete E-Mail, die der Transport abgelehnt hat, wird aufbewahrt und bei jedem Lauf erneut versucht, für `[RuleSettings] RetryHours` (72 Stunden); sehen Sie in `exp:notification:status` nach („Nachrichten konnten nicht ... übergeben werden und warten auf den nächsten Lauf“). Ist die Ursache behoben, sendet der nächste Lauf sie („n message(s) that failed earlier were sent now“). Nach 72 Stunden wird eine Nachricht aufgegeben und gezählt („aufgegeben“). `exp:notification:run` endet in einem abgelehnten Lauf mit dem Exit-Code 1, sodass eine Cron-Mail es anzeigt. Eine E-Mail, die der Transport angenommen und der Server später zurückgewiesen hat, wird hier nicht erkannt: Lesen Sie das Mail-Log des Servers |

Um den Rohzustand anzusehen: `./console exp:notification:status --json` enthält alles, was die Seite zeigt.

## 8. Veröffentlichen ohne Benachrichtigung

Redakteure können eine Korrektur veröffentlichen, ohne die Abonnenten zu informieren, wo Sie es erlauben. Zwei Schritte:

1. Schalten Sie die Einstellung ein, zum Beispiel nur für den Siteaccess der Administration, in
   `settings/siteaccess/<admin siteaccess>/notification.ini.append.php`:

   ```ini
   [NotificationSettings]
   PublishWithoutNotification=enabled
   ```

   oder aus der Shell: `./console exp:ini set notification.ini/NotificationSettings/PublishWithoutNotification enabled siteaccess:<admin siteaccess>`.

2. Geben Sie den Redakteuren die Richtlinie **content / publish_without_notification** (Rollen und Richtlinien, die Redakteursrolle, Neue
   Richtlinie, Modul `content`, Funktion `publish_without_notification`). Administratoren mit `content/*` haben sie bereits.

Leeren Sie den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`) und starten Sie Velocity neu, falls Sie es verwenden, damit die Worker die
Einstellung lesen. Das Bearbeitungsformular und die Versionsvorschau zeigen dann **Ohne Benachrichtigung veröffentlichen** neben der Schaltfläche zum Veröffentlichen.
Eine damit veröffentlichte Version erzeugt kein Benachrichtigungsereignis: Die Statusseite zählt kein Ereignis dafür, kein
Abonnement sendet dafür eine E-Mail oder sammelt einen Digest-Eintrag. Freigabe-E-Mails (Zusammenarbeit) werden weiterhin gesendet, und die
nächste gewöhnliche Veröffentlichung des Objekts benachrichtigt wieder.

| Symptom | Ursache und Abhilfe |
|---|---|
| Die Schaltfläche wird nicht angezeigt | Die Einstellung ist für diesen Siteaccess nicht `enabled` (`./console exp:ini where notification.ini/NotificationSettings/PublishWithoutNotification` nennt jede Datei, die sie setzt), oder den Rollen des Benutzers fehlt die Funktion. Melden Sie sich nach einer Rollenänderung erneut an |
| Abonnenten haben eine E-Mail erhalten, obwohl die Schaltfläche verwendet wurde | Eine Extension lauscht auf `content/notification/create` und gibt `true` zurück statt dessen, was sie erhalten hat; die Debug-Ausgabe der Veröffentlichung sagt, welcher Listener ein Ereignis ausgelassen hat, nicht welcher eines erzeugt hat. Prüfen Sie `[Event] Listeners[]` |
| Die Schaltfläche hat mit Benachrichtigung veröffentlicht | Dem Benutzer fehlt die Richtlinie (die Debug-Ausgabe enthält die Warnung „PublishNotNotifyButton was posted by a user without content/publish_without_notification“) |
| Veröffentlichungen mancher Klassen senden nichts | `notification.ini [NotificationSettings] NotificationFilterByClassIdentifier=enabled` lässt nur die Klassen in `IncludeClasses[]` benachrichtigen; die Debug-Ausgabe der Veröffentlichung meldet „its class is not in ... IncludeClasses“ |

Um nur für bestimmte Klassen zu benachrichtigen (Artikel und Dateien, nicht jeden Ordner oder jedes Bild), führen Sie sie auf:

```ini
[NotificationSettings]
NotificationFilterByClassIdentifier=enabled
IncludeClasses[]
IncludeClasses[]=article
IncludeClasses[]=file
```

Jede andere Klasse gilt dann als ohne Benachrichtigung veröffentlicht. Eine Klassen-ID in der Liste (`IncludeClasses[]=16`) bezeichnet
ihre Klasse ebenfalls. Der Standard (`disabled`) benachrichtigt für jede Klasse.

Die ganze Geschichte: [Ohne Benachrichtigung veröffentlichen](../../eng-us/features/6.0/publish-without-notification.md).

## Verwandte Seiten

- [Die Anleitung für Benutzer](../../eng-us/features/6.0/notifications.md), [die Anleitung für Entwickler](benachrichtigungen-entwickler.md)
- [Die Befehlsreferenz](../spezifikationen/6.0/benachrichtigungen-cli.md), [die INI-Referenz](../spezifikationen/6.0/benachrichtigungen-ini.md), [die Spezifikation](../spezifikationen/6.0/benachrichtigungen.md)
- [Betrieb einer Site](betrieb-einer-website.md), [Cronjob-Konsole](../../eng-us/features/6.0/cronjobs-console.md), [das Audit-Protokoll](../../eng-us/features/6.0/audit-trail.md)
- [Upgrade-Hinweise](../../eng-us/bc/6.0/notification-ui-and-commands.md)
