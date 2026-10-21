# Spezifikation: die Benachrichtigungsbefehle

Diese Seite ist die Referenz für die vier Konsolenbefehle des Benachrichtigungssystems: was jede Option bewirkt, die
Exit-Codes und Beispielausgaben. Lesen Sie sie, wenn Sie Benachrichtigungen aus einer Shell, einer Crontab oder einem Bereitstellungsskript ausführen. Für die
Seite in der Administration, die dasselbe leistet, siehe [die Anleitung für Administratoren](../../anleitungen/benachrichtigungen-administrator.md).

Jedes Beispiel auf dieser Seite lief auf einer Testinstallation, deren Mail-Transport auf Dateien gezwungen war (siehe
`--mail-file-dir` unten), mit einem Testbenutzer unter der Adresse `nottest-ui@nottest.invalid` und Testinhalten; es wurde keine E-Mail gesendet.
Anzahlen, IDs und Zeiten weichen auf Ihrer Installation ab. Die Befehle werden im Wurzelverzeichnis der Installation ausgeführt. `--allow-root-user` ist
nötig, wenn Sie als root arbeiten; `--no-colors` ist in den Beispielen weggelassen, wo es keine Rolle spielt.

## Namen und Aliase

Jeder Befehl ist ein Skript in `bin/php/` und eine Klasse in `kernel/private/classes/commands/`. Die Konsole führt den echten
Namen und die Aliase auf (`./console list exp` zeigt sie):

| Echter Name (`./console ...`) | Aliase | Skript |
|---|---|---|
| `exp:notificationstatus` | `exp:notification:status`, `exp:notify:status` | `bin/php/notificationstatus.php` |
| `exp:notificationrun` | `exp:notification:run`, `exp:notify:run` | `bin/php/notificationrun.php` |
| `exp:notificationevents` | `exp:notification:events`, `exp:notify:events` | `bin/php/notificationevents.php` |
| `exp:notificationsubscriptions` | `exp:notification:subscriptions`, `exp:notify:subscriptions` | `bin/php/notificationsubscriptions.php` |

```bash
./console list exp | grep notif
./console help exp:notification:run
./console exp:notify:events list --limit=2 --allow-root-user
```

`--help` zeigt bei jedem von ihnen die Beschreibung und die Optionen. Die Standardoptionen jedes Skripts gelten ebenfalls:
`-s` / `--siteaccess=<name>`, `-q` / `--quiet`, `-d` / `--debug`, `--no-colors`, `-r` / `--allow-root-user`.

## Exit-Codes

| Code | Bedeutung |
|---|---|
| 0 | Der Befehl hat getan, was verlangt war; die letzte Zeile ist `PASS` (oder `PASS: ...`) |
| 1 | Die Arbeit ist fehlgeschlagen: Der Mail-Transport hat eine Nachricht abgelehnt (sie wird für den nächsten Lauf aufbewahrt), ein Lauf konnte nicht starten (ein anderer Lauf hält die Sperre, die Sperrdatei kann nicht geschrieben werden, ein Probelauf wird abgelehnt, weil die Datenbank kein Rollback kann), ein unbekannter Benutzer, oder `exp:notification:status` hat ein Problem der Stufe „error“ gefunden |
| 2 | Bedienungsfehler: eine unbekannte Aktion, ein ungültiges `--older-than`, `--status` oder `--at`, `--event` ohne IDs |

## exp:notification:status

Zeigt ausstehende Ereignisse, die für einen Digest zurückgehaltenen Nachrichten, die Abonnements, die in den letzten 24 Stunden gesendeten E-Mails, die letzten zehn
Läufe und das, was nicht in Ordnung scheint. Es ändert nichts.

| Option | Wirkung |
|---|---|
| `--json` | Ein JSON-Objekt mit denselben Zahlen, `runs`, `last_run`, `running` und `problems` (jeweils `level` und `text`) |

Endet mit `PASS` oder mit `FAIL: n problem(s) of level error` und Exit-Code 1, sodass eine Überwachungsprüfung den Befehl nutzen kann. Warnungen und
Hinweise lassen ihn nicht fehlschlagen.

```text
$ ./console exp:notification:status --allow-root-user
Events
  pending                  1   (ezpublish 1)
  oldest pending           2026-10-04 22:39:18
  handled, kept            0   (0 with nothing left to send)
Messages
  collections              0
  items to send now        0
  items kept for a digest  0   (0 due)
Subscriptions
  subtree                  1 by 1 users
  collaboration rules      0
  digest                   daily 1, weekly 0, monthly 0
Mail
  transport                sendmail   sender info@se7enx.com
  last 24 hours            1 messages to 1 recipients in 3 runs
Handlers                    ezgeneraldigest, ezcollaborationnotification, ezsubtree
Runs
  2026-10-04 22:39:19  console         ok     events 0, removed 0, kept 0, messages 0, 23 ms
  2026-10-04 22:39:17  console         ok     events 33, removed 33, kept 0, messages 0, 48 ms
  2026-10-04 22:39:16  console         ok     events 1, removed 1, kept 0, messages 1, 100 ms
Problems
  none
PASS
```

`./console exp:notification:status --json` beginnt so (gekürzt):

```text
{
    "time": 1791178751,
    "pending": { "ezpublish": 33 },
    "pending_total": 33,
    "handled_kept": 0,
    "handled_orphans": 0,
    "collections": 0,
    "items_total": 0, "items_now": 0, "items_digest": 0, "items_due": 0,
    "subscriptions": 0, "subscribers": 0, "collab_rules": 0,
    "digest": { "daily": 0, "weekly": 0, "monthly": 0 },
    "runs": [ { "time": 1791178297, "source": "web", "result": "ok", "events": 1, "mails": 0, "recipients": 0, "ms": 42, ... } ],
    "running": false,
    "transport": "sendmail",
    "sender": "info@se7enx.com",
    "handlers": [ "ezgeneraldigest", "ezcollaborationnotification", "ezsubtree" ],
    "problems": []
}
```

Die Bedeutung jeder Zeile steht in [der Anleitung für Administratoren](../../anleitungen/benachrichtigungen-administrator.md#jede-zahl-auf-der-statusseite).

## exp:notification:run

Ein Durchgang des Benachrichtigungsfilters, so wie ihn der Cronjob-Teil ausführt: Ein Zeitereignis wird erzeugt, jedes ausstehende Ereignis durchläuft
jeden Handler, Nachrichten werden gesendet oder für einen Digest zurückgehalten, Ereignisse, auf die nichts mehr wartet, werden entfernt. Der Lauf ist gesperrt,
wird aufgezeichnet und im Audit-Protokoll erfasst (siehe [die Spezifikation](benachrichtigungen.md)).

| Option | Wirkung |
|---|---|
| `--dry-run` | Listet auf, was gesendet würde, und ändert nichts: Der Durchgang läuft in einer Transaktion, die zurückgerollt wird, und keine E-Mail wird an den Transport übergeben. Wird abgelehnt (Exit-Code 1), wo die Datenbank kein Rollback kann (MySQL-Tabellen, die nicht InnoDB sind, oder `[DatabaseSettings] Transactions` nicht aktiviert) |
| `--at=<time>` | Der Zeitpunkt des Zeitereignisses: ein Datum (`2026-10-05 08:00`) oder ein Unix-Zeitstempel. Standard: jetzt. Damit probieren Sie Digest-Zeitfenster aus |
| `--event=<ids>` | Nur diese Ereignisse, durch Kommas getrennt. Dann wird kein Zeitereignis erzeugt. Die übrigen ausstehenden Ereignisse der Installation bleiben unberührt |
| `--no-time-event` | Kein Zeitereignis erzeugen: Nur das Ausstehende wird verarbeitet, und durch diesen Lauf wird kein Digest fällig |
| `--mail-file-dir=<dir>` | Erzwingt den Datei-Transport nur für diesen Prozess und schreibt die E-Mails nach `<dir>`; in keine Einstellungsdatei wird etwas geschrieben. Verwenden Sie es bei jedem Versuch |
| `--addresses` | Probelauf: vollständige Empfängeradressen anzeigen (standardmäßig maskiert: `n***@nottest.invalid`) |
| `--json` | Ein JSON-Objekt statt des Texts |
| `--job=<id>` | Die ID eines Hintergrundjobs; der Fortschritt wird in dessen Datei geschrieben. Die Statusseite nutzt das, Sie normalerweise nicht |
| `--source=<console\|web>` | Was die Aufzeichnung des Laufs darüber sagt, wer ihn gestartet hat (Standard `console`) |

Ein Probelauf (der Betreff trägt den Titel des Testartikels; die Adresse ist maskiert):

```text
$ ./console exp:notification:run --dry-run --no-time-event --mail-file-dir=var/tmp/notification-mail/doc --allow-root-user
Mail goes to files in var/tmp/notification-mail/doc (file transport, this process only).
Dry run: nothing is sent, marked or removed.

  would send "Article "NOTTEST first article" was published [example.com - NOTTEST UI folder]" to 1 recipient(s): n***@nottest.invalid
PASS: dry run, 34 event(s) handled, 1 message(s) would be sent, 0 event(s) would be kept for a digest.
```

Mit `--addresses` lautet der letzte Teil der Nachrichtenzeile `to 1 recipient(s): nottest-ui@nottest.invalid`. Mit `--json`:

```text
{
    "result": "ok",
    "error": "",
    "events": 34,
    "removed": 34,
    "kept": 0,
    "failed": 0,
    "mails": [
        { "subject": "Article \"NOTTEST first article\" was published [...]", "to": 1, "addresses": [ "n***@nottest.invalid" ] }
    ]
}
```

Ein echter Lauf mit einem Ereignis; die E-Mail geht in eine Datei:

```text
$ ./console exp:notification:run --event=21561 --mail-file-dir=var/tmp/notification-mail/doc --allow-root-user
Mail goes to files in var/tmp/notification-mail/doc (file transport, this process only).
Starting notification event processing

PASS: 1 event(s) handled, 1 removed, 0 kept for a digest, 1 message(s) to 1 recipient(s), 100 ms.
```

Die Zahlen: verarbeitete Ereignisse; entfernte (nichts wartete mehr auf sie); für einen Digest zurückgehaltene (eine Nachricht wartet auf ihren
Digest-Zeitpunkt); jetzt gesendete Nachrichten und Empfänger. `, n handler failure(s)` wird angehängt, wenn ein Handler einen Fehler ausgelöst hat.

Ein Digest in zwei Schritten (der Abonnent hat einen täglichen Digest um 8:00 Uhr gewählt). Der erste Lauf hält die Nachricht zurück; ein Lauf, dessen Zeitereignis
nach 8:00 Uhr liegt, sendet sie:

```text
$ ./console exp:notification:run --event=21599 --mail-file-dir=var/tmp/notification-mail/doc2 --allow-root-user
PASS: 1 event(s) handled, 0 removed, 1 kept for a digest, 0 message(s) to 0 recipient(s), 79 ms.

$ ./console exp:notification:run --at="2026-10-05 06:00" --mail-file-dir=var/tmp/notification-mail/doc2 --allow-root-user
Time event at 2026-10-05 06:00:00.
PASS: 13 event(s) handled, 13 removed, 0 kept for a digest, 0 message(s) to 0 recipient(s), 61 ms.

$ ./console exp:notification:run --dry-run --at="2026-10-05 09:00" --mail-file-dir=var/tmp/notification-mail/doc2 --allow-root-user
  would send "[example.com] Digest for Sunday October 04 2026 10:40:33 pm" to 1 recipient(s): n***@nottest.invalid
PASS: dry run, 1 event(s) handled, 1 message(s) would be sent, 0 event(s) would be kept for a digest.

$ ./console exp:notification:run --at="2026-10-05 09:00" --mail-file-dir=var/tmp/notification-mail/doc2 --allow-root-user
PASS: 1 event(s) handled, 1 removed, 0 kept for a digest, 1 message(s) to 1 recipient(s), 125 ms.
```

Das Datum im Betreff des Digests ist der Zeitpunkt, zu dem der Digest erstellt wurde, nicht der Zeitpunkt des Zeitereignisses.

Ein zweiter Lauf, während ein anderer die Sperre hält, wird mit Exit-Code 1 abgelehnt. Auch ein Probelauf braucht die Sperre:

```text
$ ./console exp:notification:run --no-time-event --event=1 --mail-file-dir=var/tmp/notification-mail/doc2 --allow-root-user
FAIL: Another run holds the lock (process 0).

$ ./console exp:notification:run --dry-run --no-time-event --event=1 --mail-file-dir=var/tmp/notification-mail/doc2 --allow-root-user
FAIL: A run is in progress.
```

(Die Prozessnummer ist hier 0, weil die Sperre vom Werkzeug `flock` gehalten wurde, das keine schreibt; ein Lauf von
Exponential schreibt seine Prozess-ID.) Weitere Fehler: `FAIL: --at needs a date or a timestamp`, `FAIL: --event needs event
ids` (beide Exit-Code 2), `FAIL: The lock file ... cannot be opened (permissions ...)`.

Transportfehler. Wenn der Mail-Transport eine Nachricht ablehnt, erledigt der Lauf trotzdem seine Arbeit, hält die Nachricht zurück und endet mit Exit-Code
1. Hier wird der Datei-Transport auf ein Verzeichnis gerichtet, in das er nicht schreiben kann (die einzige Möglichkeit, den Transport ohne Mailserver „scheitern“ zu lassen;
keine E-Mail kann den Server verlassen), und danach auf ein brauchbares:

```text
$ ./console exp:notification:run --event=21935 --mail-file-dir=/proc/nottest-no-such-directory
PASS: 1 event(s) handled, 0 removed, 1 kept for a digest, 0 message(s) to 0 recipient(s), 150 ms.
FAIL: the mail transport refused 1 message(s); they are kept and tried again at the next run (for 72 hours).

$ ./console exp:notification:status
Problems
  [error] 1 messages could not be handed to the mail transport and wait for the next run; each is given up after 72 hours.
  [error] The mail transport refused 1 messages in the last run. Check the mail server and site.ini MailSettings.
FAIL: 2 problem(s) of level error

$ ./console exp:notification:run --no-time-event --event=1 --mail-file-dir=var/tmp/notification-mail/doc3
PASS: 0 event(s) handled, 0 removed, 0 kept for a digest, 1 message(s) to 1 recipient(s), 37 ms.
1 message(s) that failed earlier were sent now.
```

Eine Nachricht, die nach `RetryHours` aufgegeben wird, wird als `WARNING: n message(s) were given up (older than 72 hours, or an address that cannot be mailed).` gemeldet.

Führen Sie einen Versuch niemals ohne `--mail-file-dir` aus: Ohne diese Option gehen die E-Mails über den Transport aus `site.ini`.

## exp:notification:events

| Aktion | Wirkung |
|---|---|
| `list` (Standard) | Die neuesten Ereignisse: ID, Status, Typ, Alter, wartende Nachrichten |
| `cleanup` | Entfernt die verarbeiteten Ereignisse, auf die nichts mehr wartet; mit `--older-than` auch die Ereignisse (und ihre Nachrichten), die älter sind |

| Option | Wirkung |
|---|---|
| `--status=<pending\|handled>` | `list`, `cleanup --older-than`: nur Ereignisse in diesem Status (Standard: beide) |
| `--limit=<n>` | `list`: wie viele (Standard 50) |
| `--older-than=<age>` | `cleanup`: `90s`, `15m`, `12h`, `30d`, `2w`, eine einfache Anzahl Tage oder ein Datum (`2026-09-01`) |
| `--include-unknown` | `cleanup`: auch Ereignisse entfernen, deren Alter sich nicht bestimmen lässt |
| `--dry-run` | `cleanup`: zählen, nichts entfernen |
| `--json` | `list`: JSON (`id`, `status`, `type`, `created`, `items`) |

Ein Ereignis hat keine Datumsspalte. Sein Alter wird aus dem abgelesen, worauf es sich bezieht: dem Zeitpunkt eines Zeitereignisses, der Erstellung der
veröffentlichten Version bei einem `ezpublish`-Ereignis, der Erstellung des Elements der Zusammenarbeit. Gibt es diesen Inhalt nicht mehr, ist das Alter
unbekannt (`age unknown`), und solche Ereignisse werden nur mit `--include-unknown` entfernt.

```text
$ ./console exp:notification:events list --status=handled
  21599  handled  ezpublish        2026-10-04 22:40:31  1 message(s) waiting
PASS: 1 event(s) shown

$ ./console exp:notification:events cleanup --dry-run
Would remove: 1 handled event(s) with nothing left to send
PASS

$ ./console exp:notification:events cleanup --older-than=30d --dry-run
Would remove: 1 handled event(s) with nothing left to send
Would remove: 1 event(s) older than 30d (1 newer kept, 0 of unknown age left alone)
PASS

$ ./console exp:notification:events cleanup --older-than=30d
Removed: 1 handled event(s) with nothing left to send
Removed: 1 event(s) older than 30d (0 newer kept, 0 of unknown age left alone)
PASS

$ ./console exp:notification:events cleanup --older-than=soon
FAIL: --older-than is a number of days, or 90s, 15m, 12h, 30d, 2w, or a date          (exit code 2)

$ ./console exp:notification:events frobnicate
FAIL: the actions are list and cleanup                                                    (exit code 2)
```

## exp:notification:subscriptions

| Aktion | Wirkung |
|---|---|
| `list` (Standard) | Die Teilbaum-Abonnements: Regel-ID, Benutzer, Knoten, Name, Klasse, letzte Änderung unterhalb des Knotens |
| `remove-missing` | Entfernt die Abonnements, deren Knoten nicht mehr existiert |

| Option | Wirkung |
|---|---|
| `--user=<login or id>` | Nur dieser Benutzer (ein Login oder die ID des Inhaltsobjekts des Benutzers). Ein unbekannter Login: `FAIL: no user with the login ...`, Exit-Code 1 |
| `--q=<text>` | Nur Knoten, deren Name den Text enthält (`%` und `_` sind gewöhnliche Zeichen) |
| `--class=<identifier>` | Nur Knoten dieser Klasse |
| `--missing` | Nur Abonnements, deren Knoten verschwunden ist |
| `--limit=<n>`, `--offset=<n>` | Seitenweise Ausgabe (Standardlimit 50) |
| `--addresses` | Zusätzlich die E-Mail-Adresse des Benutzers anzeigen (standardmäßig verborgen) |
| `--dry-run` | `remove-missing`: zählen, nichts entfernen |
| `--json` | `list`: JSON (`total`, `rows` mit `path`, `class_identifier`, `last_change`, `missing`, `use_digest`) |

```text
$ ./console exp:notification:subscriptions list --user=nottest-ui
#345   nottest-ui       node 21931  NOTTEST UI folder                folder         changed 2026-10-04
PASS: 1 of 1 subscription(s) shown

$ ./console exp:notification:subscriptions list --user=nottest-ui --addresses
#345   nottest-ui       node 21931  NOTTEST UI folder                folder         changed 2026-10-04  nottest-ui@nottest.invalid
PASS: 1 of 1 subscription(s) shown

$ ./console exp:notification:subscriptions list --missing
#346   nottest-ui       node 99999999 (content is gone)
PASS: 1 of 1 subscription(s) shown

$ ./console exp:notification:subscriptions remove-missing --dry-run
would remove #346 (user 21300, node 99999999)
PASS: 1 subscription(s) would be removed

$ ./console exp:notification:subscriptions remove-missing
removing #346 (user 21300, node 99999999)
PASS: 1 subscription(s) removed

$ ./console exp:notification:subscriptions list --user=nobody-here
FAIL: no user with the login nobody-here                                                  (exit code 1)
```

Das JSON von `list --json` (eine Zeile):

```text
{ "total": 1, "rows": [ { "id": 345, "user_id": 21300, "login": "nottest-ui", "node_id": 21931, "name": "NOTTEST UI folder",
    "path": [ "Websites" ], "class_identifier": "folder", "class_name": "Folder", "section_id": 1,
    "last_change": 1791178752, "missing": false, "use_digest": 0 } ] }
```

## Der Cronjob-Teil

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

`php runcronjobs.php -s site --list` zeigt den Teil zweimal: in `CronjobPart-frequent` und in `CronjobPart-notification`.
Ein Cron-Lauf wird mit der Quelle `cron` aufgezeichnet. Hält ein anderer Lauf die Sperre, gibt der Teil `Skipped: Another run holds the
lock ...` aus und schlägt nicht fehl. Dieses Beispiel lief auf einer Installation, auf der nichts ausstand und niemand etwas folgte,
sodass keine E-Mail entstehen konnte; auf einer Installation mit Abonnenten verwendet der Cron-Lauf den Transport aus `site.ini`.

## Verwandte Seiten

- [Die Anleitung für Administratoren](../../anleitungen/benachrichtigungen-administrator.md), [die Anleitung für Benutzer](../../funktionen/6.0/benachrichtigungen.md), [die Anleitung für Entwickler](../../anleitungen/benachrichtigungen-entwickler.md)
- [Die Spezifikation der Benachrichtigungen](benachrichtigungen.md), [die INI-Referenz](benachrichtigungen-ini.md)
- [Befehle, Cronjob-Teile und Modul-Views als Klassen](ausfuehrbare-befehle-cronjobs-ansichten.md)
- [Hinweise zum Upgrade](../../bc/6.0/benachrichtigungen-oberflaeche-und-befehle.md)
