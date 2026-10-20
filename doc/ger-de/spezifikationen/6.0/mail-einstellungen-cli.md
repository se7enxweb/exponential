# Befehle für die E-Mail-Einstellungen

Die Referenz der fünf Konsolenbefehle der E-Mail-Einstellungen und des Cronjob-Teils `mailpreferences`: jede
Aktion und Option, die Exit-Codes und echte Ausgaben. Für die Aufgaben, denen sie dienen, lesen Sie
[die Anleitung für Administratoren](../../anleitungen/mail-einstellungen-administrator.md).

Alle Ausgaben unten stammen von einer Testinstallation am 2026-10-04, mit Adressen in der reservierten Domain `.invalid`; der
Host ist durch `www.example.com` ersetzt, und persönliche Links sind auf `m1...` gekürzt. Jeder Befehl läuft auch als
`php bin/php/<script>.php` (in der Tabelle genannt); ergänzen Sie `--allow-root-user` als root und `--no-colors` für reinen Text.

| Befehl | Alias von | Skript |
|---|---|---|
| `exp:mail:status` | `exp:mailstatus` | `bin/php/mailstatus.php` |
| `exp:mail:preferences` | `exp:mailpreferences` | `bin/php/mailpreferences.php` |
| `exp:mail:suppression` | `exp:mailsuppression` | `bin/php/mailsuppression.php` |
| `exp:mail:consent` | `exp:mailconsent` | `bin/php/mailconsent.php` |
| `exp:mail:gate` | `exp:mailgate` | `bin/php/mailgate.php` |

**Exit-Codes**: Jeder Befehl endet mit einer letzten Zeile `PASS` (Exit-Code 0) oder `FAIL: <reason>` (Exit-Code 1).
`exp:mail:status` schlägt nur bei einem Problem der Stufe „error“ fehl (einer fehlenden Tabelle).

**Adressen** werden verborgen (`r***@docs.invalid`), sofern nicht `--addresses` angegeben ist. Von `--output` geschriebene Dateien erhalten den Modus 0600.

**Kein Befehl sendet E-Mails**, mit einer Ausnahme: `exp:mail:preferences set` für eine Kategorie mit Double-Opt-in sendet die
Bestätigungs-E-Mail über den Transport der Installation, es sei denn, `--no-mail` oder `--source=import` ist angegeben.

## exp:mail:status

Die Tabellen, das Gate, die Einstellungen der Fußzeile, die Kategorien mit ihren Anzahlen, die Sperrliste, das Einwilligungsprotokoll, die
Entscheidungen des Gates der letzten 24 Stunden und 7 Tage sowie die Probleme. Ändert nichts.

| Option | Bedeutung |
|---|---|
| `--json` | ein JSON-Objekt (das Array von `expMailPreferencesService::status()`) |

```
$ ./console exp:mail:status
Tables
  expmail_category         ok
  expmail_preference       ok
  expmail_consent_log      ok
  expmail_suppression      ok
  expmail_pending          ok
Gate
  gate                     enabled
  site secret              present
  links point to           https://www.example.com
  organisation name        (empty)
  postal address           (empty)
Categories                   on    off  pending
  content                      0      2        0  optional
  collaboration                0      0        0  optional
  newsletter                   1      0        0  optional, double opt-in
  marketing                    0      0        0  optional, double opt-in
  system                       0      0        0  optional
  security                     -      -        -  essential
  orders                       -      -        -  essential
  legal                        -      -        -  essential
  admin                        -      -        -  essential
People
  with preferences         2
  all optional mail off    1
  pending confirmations    0   (0 expired)
  suppressed addresses     0
  consent log rows         22   (0 anonymised)
Gate, last 24 hours
  sent 1, blocked 3, essential 0, uncategorised 128, errors 0
  blocked because: off 3
  uncategorised from kernel/classes/notification/ezmailnotificationtransport.php: 127
  uncategorised from tests/tests/lib/ezutils/eZMailTest.php: 1
Gate, last 7 days
  sent 1, blocked 3, essential 0, uncategorised 128, errors 0
  blocked because: off 3
  uncategorised from kernel/classes/notification/ezmailnotificationtransport.php: 127
  uncategorised from tests/tests/lib/ezutils/eZMailTest.php: 1
Problems
  [warning] The organisation name or postal address of the mail footer is empty (mailpreferences.ini [FooterSettings]); the law requires both in every optional mail.
  [notice] 128 mails without a category in the last 7 days.
PASS
```

`--json` beginnt so:

```json
{
    "tables": {
        "expmail_category": true,
        "expmail_preference": true,
        "expmail_consent_log": true,
        "expmail_suppression": true,
        "expmail_pending": true
    },
    "gate": "enabled",
    "secret": true,
    "base_url": "https://www.example.com",
    "footer": {
        "organisation_name": "",
        "organisation_address": ""
    },
    "categories": {
        "content": {
            "name": "Content notifications",
            "essential": false,
            "source": "ini",
            "double_opt_in": false,
            "on": 1,
            "off": 0,
            "pending": 0
        },
```

und setzt sich fort mit `recipients`, `master_off`, `pending`, `pending_expired`, `suppression` (`total`, `by_reason`),
`consent_log`, `consent_log_anonymised`, `gate_24h`, `gate_7d` (`sent`, `blocked`, `essential`, `uncategorised`, `error`,
`by_category`, `uncategorised_senders`, `blocked_reasons`, `last`), `log_file` und `problems` (eine Liste von
`[level, code, detail]`; Codes `table_missing`, `footer_missing`, `secret_missing`, `gate_disabled`, `uncategorised`,
`gate_errors`).

## exp:mail:preferences

Zeigt und ändert die Einstellungen einer Person: eines Kontos (`--user=<login or id>`) oder einer Adresse (`--email=<address>`;
die Adresse eines Kontos steht für dieses Konto). Jede Änderung wird mit der Quelle `admin` (oder `import`) und dem Text von `--wording`
(Standard „Changed on the console“) in das Einwilligungsprotokoll geschrieben.

| Aktion | Bewirkt |
|---|---|
| `categories` | die Kategorien: Bezeichner, Art, Standardwert, Häufigkeiten, Double-Opt-in, Name |
| `show` | den Hauptschalter, jede Kategorie mit Zustand, Häufigkeit und der Angabe, ob E-Mails hinausgehen |
| `set` | eine Kategorie ein- oder ausschalten (`--category`, `--state=on|off`) |
| `frequency` | die Häufigkeit einer Kategorie (`--category`, `--frequency=immediate|daily|weekly`) |
| `master` | den Hauptschalter (`--state=on|off`) |
| `export` | alles, was über die Person gespeichert ist (`--format=json|csv`, `--output=<file>`) |
| `erase` | die Einstellungen entfernen, das Einwilligungsprotokoll anonymisieren; braucht `--yes` |
| `link` | den Einstellungslink der Person (funktioniert ohne Anmeldung) |

| Option | Bedeutung |
|---|---|
| `--user`, `--email` | die Person |
| `--category`, `--state`, `--frequency` | für `set`, `master`, `frequency` |
| `--format`, `--output` | für `export` |
| `--wording` | der Text, der im Einwilligungsprotokoll vermerkt wird |
| `--source` | `admin` (Standard) oder `import` (schaltet eine Kategorie mit Double-Opt-in sofort ein; nur für anderswo bestätigte Einwilligungen) |
| `--no-mail` | `set`: ein ausstehendes Double-Opt-in speichern, ohne seine Bestätigungs-E-Mail zu senden |
| `--yes` | `erase`: wirklich löschen |
| `--json` | `show`, `categories` als JSON |
| `--addresses` | die Adresse anzeigen |

```
$ ./console exp:mail:preferences categories
content              optional  default off  immediate,daily,weekly   -  Content notifications
collaboration        optional  default off  immediate,daily,weekly   -  Collaboration and approvals
newsletter           optional  default off                           double opt-in  Newsletters
marketing            optional  default off                           double opt-in  Offers and news
system               optional  default off                           -  Application and system notices
security             essential default off                           -  Account security
orders               essential default off                           -  Orders and receipts
legal                essential default off                           -  Legal and service notices
admin                essential default off                           -  Administration alerts
PASS
```

(Die Spalte „default off“ hat bei einer essenziellen Kategorie keine Bedeutung: Sie wird immer gesendet.)

```
$ ./console exp:mail:preferences set --email=reader@docs.invalid --category=content --state=on --wording="Docs example"
r***@docs.invalid: content is on
PASS
$ ./console exp:mail:preferences frequency --email=reader@docs.invalid --category=content --frequency=weekly
r***@docs.invalid: content is weekly
PASS
$ ./console exp:mail:preferences set --email=reader@docs.invalid --category=newsletter --state=on --no-mail
r***@docs.invalid: newsletter is pending_confirmation
PASS
$ ./console exp:mail:preferences set --email=reader@docs.invalid --category=marketing --state=on --source=import --wording="Imported with the opt-in of 2026-10-01"
r***@docs.invalid: marketing is on
PASS
$ ./console exp:mail:preferences show --email=reader@docs.invalid
Person            r***@docs.invalid
All optional mail on
  content              on       weekly     mail goes
  collaboration        off      immediate  no mail   (default)
  newsletter           pending             no mail
  marketing            on                  mail goes
  system               off                 no mail   (default)
  security             always              mail goes
  orders               always              mail goes
  legal                always              mail goes
  admin                always              mail goes
PASS
$ ./console exp:mail:preferences link --email=reader@docs.invalid
https://www.example.com/mailpreferences/manage/m1...
PASS
$ ./console exp:mail:preferences master --email=reader@docs.invalid --state=off
r***@docs.invalid: all optional mail off
PASS
$ ./console exp:mail:preferences export --email=reader@docs.invalid --format=json --output=var/tmp/reader.json
written to var/tmp/reader.json (6412 bytes, mode 0600)
PASS
```

Der CSV-Export (`--format=csv` ohne `--output` gibt ihn auf dem Bildschirm aus):

```
section,key,value,detail
recipient,key,a:d5b6a13e...,
recipient,user_id,0,
recipient,email,reader@docs.invalid,
recipient,master,on,
recipient,suppressed,no,
category,content,on,weekly
category,collaboration,off,immediate
category,newsletter,pending,
category,marketing,on,
...
pending,newsletter,category,2026-10-05T06:39:05Z
consent_log,id,created,recipient_key,user_id,email,category,action,old_value,new_value,source,wording,ip,actor_user_id,siteaccess,anonymised
consent_log,369,2026-10-05T06:39:03Z,a:d5b6a13e...,0,reader@docs.invalid,content,on,off,on,admin,"Docs example",,0,site,0
...
PASS
```

```
$ ./console exp:mail:preferences erase --email=reader@docs.invalid
FAIL: erase removes the preferences and anonymises the consent log: add --yes
$ ./console exp:mail:preferences erase --email=reader@docs.invalid --yes
r***@docs.invalid: 4 preferences and 1 pending confirmations removed, 9 consent log rows anonymised
PASS
$ ./console exp:mail:preferences set --email=reader@docs.invalid --category=security --state=off
FAIL: The mail category 'security' is essential and cannot be switched
$ ./console exp:mail:preferences show
FAIL: name the person with --user=<login or id> or --email=<address> (an existing user, a valid address)
```

## exp:mail:suppression

Die Sperrliste. Die Tabelle enthält von jeder Adresse einen gesalzenen Hash, nie die Adresse selbst; einen Eintrag findet man, indem man die
Adresse eingibt.

| Aktion | Bewirkt |
|---|---|
| `list` | die Einträge: Anfang des Hashs, Grund, Datum, Notiz (`--reason`, `--limit` Standard 50, `--offset`, `--json` mit vollständigen Hashes) |
| `check` | ob `--email` gesperrt ist und warum |
| `add` | `--email` mit `--reason` (`bounce`, `complaint`, `unsubscribe_all`, `legal`, `admin`, `bridge`) und einer optionalen `--note` sperren (eine darin enthaltene Adresse wird entfernt) |
| `lift` | den Eintrag von `--email` oder `--hash` aufheben |

```
$ ./console exp:mail:suppression add --email=other@docs.invalid --reason=legal --note="Docs example"
suppressed (hash 0b2dc01385cf4440…)
PASS
$ ./console exp:mail:suppression check --email=other@docs.invalid
suppressed: legal (hash 0b2dc01385cf4440…)
PASS
$ ./console exp:mail:suppression list --reason=legal --limit=5
1 entries (legal)
  0b2dc01385cf4440…  legal            2026-10-04 23:39  Docs example
PASS
$ ./console exp:mail:suppression lift --email=other@docs.invalid
lifted
PASS
$ ./console exp:mail:suppression check --email=nobody@docs.invalid
not suppressed
PASS
```

## exp:mail:consent

Das Einwilligungsprotokoll.

| Aktion | Bewirkt |
|---|---|
| `list` | die neuesten Zeilen: Zeit, Person, Kategorie, Aktion, alt -> neu, Quelle (`--limit` Standard 50, `--offset`, `--addresses`) |
| `export` | die Zeilen als CSV (`--output=<file>` oder auf den Bildschirm); die CSV-Datei enthält Adressen und IP-Adressen |
| `cleanup` | die Aufbewahrungsregel des Cronjob-Teils `mailpreferences`; `--dry-run` zählt nur |

Filter für `list` und `export`: `--user`, `--email`, `--category`, `--action`, `--source`, `--from`, `--to`
(`YYYY-MM-DD`).

```
$ ./console exp:mail:consent list --email=reader@docs.invalid --limit=10
6 rows
  2026-10-04 23:39:11  r***@docs.invalid      -              master_on   off -> on  admin
  2026-10-04 23:39:09  r***@docs.invalid      -              master_off  on -> off  admin
  2026-10-04 23:39:05  r***@docs.invalid      marketing      on          off -> on  import
  2026-10-04 23:39:05  r***@docs.invalid      newsletter     pending     off -> pending  admin
  2026-10-04 23:39:04  r***@docs.invalid      content        frequency   immediate -> weekly  admin
  2026-10-04 23:39:03  r***@docs.invalid      content        on          off -> on  admin
PASS
$ ./console exp:mail:consent export --email=reader@docs.invalid --output=var/tmp/consent.csv
written to var/tmp/consent.csv (6 rows, mode 0600)
PASS
$ ./console exp:mail:consent cleanup --dry-run
Would remove: 0 consent log rows, 0 expired confirmations
PASS
```

Die CSV-Spalten sind die von `expConsentLog::CSV_COLUMNS`; eine Zelle, die mit `=`, `+`, `-` oder `@` beginnt (und keine
Zahl ist), wird mit einem vorangestellten `'` geschrieben.

## exp:mail:gate

Was das Gate für eine E-Mail der Kategorie `--category` an die Adressen aus `--to` entscheiden würde (`allow`, `off`, `pending`,
`master_off`, `suppressed`, `unknown_category`). Mit `--write` geht die Test-E-Mail durch das Gate, wobei der Transport in diesem Prozess
auf `file` gezwungen wird, in das Verzeichnis `--mail-file-dir` (Standard `var/tmp/mailgate-test`); sie wird nie gesendet.

| Option | Bedeutung |
|---|---|
| `--category` | die Kategorie der Test-E-Mail; leer: eine E-Mail ohne Kategorie |
| `--to` | Adressen, durch Kommas getrennt |
| `--write` | die Test-E-Mail durch das Gate in Dateien schreiben |
| `--html` | eine HTML-Test-E-Mail |
| `--mail-file-dir` | wohin `--write` schreibt |
| `--addresses` | die Adressen anzeigen |

```
$ ./console exp:mail:gate --category=content --to=reader@docs.invalid,other@docs.invalid
Category content: allow
  r***@docs.invalid                allow
  o***@docs.invalid                off
PASS
$ ./console exp:mail:gate --category=content --to=reader@docs.invalid,other@docs.invalid --write --mail-file-dir=var/tmp/gate-mail
Category content: allow
  r***@docs.invalid                allow
  o***@docs.invalid                off

Written: 1 file(s) in var/tmp/gate-mail, decision partly_blocked, result true
  var/tmp/gate-mail/1791182348-1270746661.mail
PASS
$ ./console exp:mail:gate --to=reader@docs.invalid
Category (none): no_category
  r***@docs.invalid                allow
PASS
$ ./console exp:mail:gate --category=content --to=reader@docs.invalid
Category content: master_off
  r***@docs.invalid                master_off
PASS
$ ./console exp:mail:gate --category=nothing_here --to=a@docs.invalid
Category nothing_here: unknown_category
  a***@docs.invalid                allow
PASS
```

Die geschriebene E-Mail:

```
Subject: Mail gate test
From: info@example.com
Bcc: reader@docs.invalid
X-Exp-Mail-Category: content
List-Unsubscribe: <https://www.example.com/mailpreferences/unsubscribe/m1...>
List-Unsubscribe-Post: List-Unsubscribe=One-Click
This is a test of the mail gate.

-- 
You get this e-mail because you turned on "Content notifications" on Example Site.
Unsubscribe with one click: https://www.example.com/mailpreferences/unsubscribe/m1...
Choose which e-mail you get: https://www.example.com/mailpreferences/manage/m1...
```

(Ist `[FooterSettings]` ausgefüllt, folgen nach einer Leerzeile die Organisation und ihre Postanschrift.)

## Der Cronjob-Teil mailpreferences

```bash
php runcronjobs.php mailpreferences          # alone
php runcronjobs.php infrequent               # with the other infrequent parts
```

Er gibt eine Zeile aus, `E-mail preferences retention: <n> consent log rows, <n> expired confirmations, <n> suppression
entries removed`, oder `Skipped: the e-mail preference tables are not installed (database update)`.

## Verwandte Seiten

- [Die Spezifikation](mail-einstellungen.md)
- [Die Anleitung für Administratoren](../../anleitungen/mail-einstellungen-administrator.md)
- [Die Rechts-Checkliste](mail-einstellungen-compliance.md)
- [Befehle für Benachrichtigungen](benachrichtigungen-cli.md)
