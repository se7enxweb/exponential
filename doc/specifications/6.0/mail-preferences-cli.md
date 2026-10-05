# E-mail preference commands

The reference of the five console commands of the e-mail preferences and of the cronjob part `mailpreferences`: every
action and option, the exit codes and real output. For the tasks they serve, read
[the administrator's guide](../../guides/mail-preferences-administrator.md).

All output below comes from a test installation on 2026-10-04, with addresses on the reserved `.invalid` domain; the
host is replaced by `www.example.com` and personal links are shortened to `m1...`. Each command also runs as
`php bin/php/<script>.php` (named in the table); add `--allow-root-user` as root and `--no-colors` for plain text.

| Command | Alias of | Script |
|---|---|---|
| `exp:mail:status` | `exp:mailstatus` | `bin/php/mailstatus.php` |
| `exp:mail:preferences` | `exp:mailpreferences` | `bin/php/mailpreferences.php` |
| `exp:mail:suppression` | `exp:mailsuppression` | `bin/php/mailsuppression.php` |
| `exp:mail:consent` | `exp:mailconsent` | `bin/php/mailconsent.php` |
| `exp:mail:gate` | `exp:mailgate` | `bin/php/mailgate.php` |

**Exit codes**: every command ends with a last line `PASS` (exit code 0) or `FAIL: <reason>` (exit code 1).
`exp:mail:status` fails only for a problem of level error (a missing table).

**Addresses** are hidden (`r***@docs.invalid`) unless `--addresses` is given. Files written by `--output` get mode 0600.

**No command sends mail**, with one exception: `exp:mail:preferences set` of a double opt-in category sends the
confirmation mail through the installation's transport unless `--no-mail` or `--source=import` is given.

## exp:mail:status

The tables, the gate, the footer settings, the categories with their counts, the suppression list, the consent log, the
gate's decisions of the last 24 hours and 7 days, and the problems. Changes nothing.

| Option | Meaning |
|---|---|
| `--json` | one JSON object (the array of `expMailPreferencesService::status()`) |

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

`--json` starts like this:

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

and goes on with `recipients`, `master_off`, `pending`, `pending_expired`, `suppression` (`total`, `by_reason`),
`consent_log`, `consent_log_anonymised`, `gate_24h`, `gate_7d` (`sent`, `blocked`, `essential`, `uncategorised`, `error`,
`by_category`, `uncategorised_senders`, `blocked_reasons`, `last`), `log_file` and `problems` (a list of
`[level, code, detail]`; codes `table_missing`, `footer_missing`, `secret_missing`, `gate_disabled`, `uncategorised`,
`gate_errors`).

## exp:mail:preferences

Shows and changes the preferences of one person: an account (`--user=<login or id>`) or an address (`--email=<address>`;
an address of an account is that account). Every change is written to the consent log with source `admin` (or `import`)
and the text of `--wording` (default "Changed on the console").

| Action | Does |
|---|---|
| `categories` | the categories: identifier, kind, default, frequencies, double opt-in, name |
| `show` | the main switch, each category with state, frequency and whether mail goes |
| `set` | a category on or off (`--category`, `--state=on|off`) |
| `frequency` | the frequency of a category (`--category`, `--frequency=immediate|daily|weekly`) |
| `master` | the main switch (`--state=on|off`) |
| `export` | everything stored about the person (`--format=json|csv`, `--output=<file>`) |
| `erase` | remove the preferences, anonymise the consent log; needs `--yes` |
| `link` | the person's preference link (works without login) |

| Option | Meaning |
|---|---|
| `--user`, `--email` | the person |
| `--category`, `--state`, `--frequency` | for `set`, `master`, `frequency` |
| `--format`, `--output` | for `export` |
| `--wording` | the text recorded in the consent log |
| `--source` | `admin` (default) or `import` (switches a double opt-in category on at once; only for consent confirmed elsewhere) |
| `--no-mail` | `set`: store a pending double opt-in without sending its confirmation mail |
| `--yes` | `erase`: really erase |
| `--json` | `show`, `categories` as JSON |
| `--addresses` | show the address |

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

(The column "default off" means nothing for an essential category: it is always sent.)

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

The CSV export (`--format=csv` without `--output` prints it):

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

The suppression list. The table holds a salted hash of each address, never the address; an entry is found by typing the
address.

| Action | Does |
|---|---|
| `list` | the entries: start of the hash, reason, date, note (`--reason`, `--limit` default 50, `--offset`, `--json` with whole hashes) |
| `check` | whether `--email` is suppressed, and why |
| `add` | suppress `--email` with `--reason` (`bounce`, `complaint`, `unsubscribe_all`, `legal`, `admin`, `bridge`) and an optional `--note` (an address in it is removed) |
| `lift` | lift the entry of `--email` or `--hash` |

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

The consent log.

| Action | Does |
|---|---|
| `list` | the newest rows: time, person, category, action, old -> new, source (`--limit` default 50, `--offset`, `--addresses`) |
| `export` | the rows as CSV (`--output=<file>`, or the screen); the CSV holds addresses and IP addresses |
| `cleanup` | the retention of the cronjob part `mailpreferences`; `--dry-run` counts only |

Filters for `list` and `export`: `--user`, `--email`, `--category`, `--action`, `--source`, `--from`, `--to`
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

The CSV columns are those of `expConsentLog::CSV_COLUMNS`; a cell that starts with `=`, `+`, `-` or `@` (and is not a
number) is written with a leading `'`.

## exp:mail:gate

What the gate would decide for a mail of `--category` to the addresses of `--to` (`allow`, `off`, `pending`,
`master_off`, `suppressed`, `unknown_category`). With `--write` the test mail goes through the gate with the transport
forced to `file` in this process, into `--mail-file-dir` (default `var/tmp/mailgate-test`); it is never sent.

| Option | Meaning |
|---|---|
| `--category` | the category of the test mail; empty: a mail without category |
| `--to` | addresses, comma separated |
| `--write` | put the test mail through the gate into files |
| `--html` | an HTML test mail |
| `--mail-file-dir` | where `--write` writes |
| `--addresses` | show the addresses |

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

The written mail:

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

(With `[FooterSettings]` filled in, the organisation and its postal address follow after an empty line.)

## The cronjob part mailpreferences

```bash
php runcronjobs.php mailpreferences          # alone
php runcronjobs.php infrequent               # with the other infrequent parts
```

It prints one line, `E-mail preferences retention: <n> consent log rows, <n> expired confirmations, <n> suppression
entries removed`, or `Skipped: the e-mail preference tables are not installed (database update)`.

## Related pages

- [The specification](mail-preferences.md)
- [The administrator's guide](../../guides/mail-preferences-administrator.md)
- [The law checklist](mail-preferences-compliance.md)
- [Notification commands](notifications-cli.md)
