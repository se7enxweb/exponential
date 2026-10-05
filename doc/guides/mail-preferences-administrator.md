# E-mail preferences: setting them up and running them

This guide is for the administrator of an installation. At the end the e-mail preferences are set up (tables, footer,
links, site secret), you can read the status page, the retention cronjob runs, and you know how to handle a request to
stop mail, a bounce, an export and an erasure. What the people on your site see is in
[the user's guide](../features/6.0/mail-preferences.md); how code sends mail through the gate is in
[the developer's guide](mail-preferences-developer.md).

Every command below was run on a test installation on 2026-10-04 with addresses on the reserved `.invalid` domain.
Add `--allow-root-user` when you run a command as root. Personal links in the output are shortened to `m1...`, and the
host of the test installation is replaced by `www.example.com`.

## 1. What the system does, in one paragraph

Every mail the installation sends passes the **mail gate** in `eZMailTransport::send()`, whatever transport you use
(sendmail, SMTP, file). A mail that declares a category is checked against the recipient's **preferences** (a main
switch and one switch per optional category), the **suppression list** and a pending **double opt-in**. Optional mail
goes only to people who turned it on, one mail per recipient, with a **footer** (why, unsubscribe, manage, your
organisation and postal address) and the one-click headers of RFC 8058. Essential mail always goes. Mail without a
category is sent as before and counted, so you can find the code that sends it. Every change of a preference is written
to the **consent log** with the exact text the person saw.

## 2. Setting up

### 2.1 The tables

The five tables `expmail_category`, `expmail_preference`, `expmail_consent_log`, `expmail_suppression` and
`expmail_pending` are in the kernel schema of a new installation. An upgraded installation gets them from the database
update of the line, `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` (see
[the upgrade notes](../bc/6.0/mail-preferences.md)). Until they exist the status page and `exp:mail:status` report the
missing table, the gate cannot read anybody's preferences and optional mail is held back; mail without a category and
essential mail go as before.

### 2.2 The footer: who sends, and from where

The law (CAN-SPAM, CASL, and the EU rules on information) asks for the sender's name and a postal address in every
optional mail. Both ship empty. Put them into a settings override, never into `settings/mailpreferences.ini`:

```bash
./console exp:ini set mailpreferences.ini/FooterSettings/OrganisationName "Example Organisation Ltd" global --dry-run
```

Expected (the dry run writes nothing; drop `--dry-run` to write):

```
--- a/settings/override/mailpreferences.ini.append.php
+++ b/settings/override/mailpreferences.ini.append.php
@@ -3,4 +3,7 @@
 [SecretSettings]
 TokenSecret=********
+[FooterSettings]
+OrganisationName=Example Organisation Ltd
+
 */ ?>
Dry run: set mailpreferences.ini/FooterSettings/OrganisationName in global, nothing written
```

Do the same for `OrganisationAddress`. A line break in the address is written as `\n`
(`Example Street 1\n12345 Town`). If you want a phone number or a contact address in the footer (CASL asks for one of a
phone number, an e-mail address or a web address besides the postal address; the manage link is a web address), add it to
`OrganisationAddress`.

The footer comes from `design:mailpreferences/mail/footer.tpl` (`[FooterSettings] Template`); override that template in
your design to change its wording, keeping the unsubscribe link, the manage link and the address.

### 2.3 Where the links point to

The links in mail are `<BaseURL>/mailpreferences/unsubscribe/<token>` and `.../manage/<token>`. `[LinkSettings] BaseURL`
is empty by default, which means `https://` and the `SiteURL` of the default siteaccess. Set it when the public site has
another host, or when the links should open a siteaccess of another language:

```bash
./console exp:ini set mailpreferences.ini/LinkSettings/BaseURL "https://www.example.com" global --dry-run
```

The status page shows the address in use ("Links in e-mails point to").

### 2.4 The site secret

The links are encrypted and signed, and the suppression list stores salted hashes, both with a site secret. It is
generated on first use into `settings/override/mailpreferences.ini.append.php` (`[SecretSettings] TokenSecret`). Never
commit that file, and copy it with the installation: **a new secret invalidates every link in every mail already sent, and
every entry of the suppression list stops matching.** `exp:ini` and the settings views mask it.

### 2.5 Roles

| Policy | Gives |
|---|---|
| `mailpreferences/administrate` | Setup > **E-mail preferences**: status, categories, suppression list, consent log, a user's preferences |
| `mailpreferences/export` | The CSV export of the consent log and the download of a user's data from the administrator's pages |

The person's own pages (`settings`, `manage`, `unsubscribe`, `confirm`, `request`, `export`) need no policy: they are in
`site.ini [RoleSettings] PolicyOmitList` and check the login or the personal link themselves.

## 3. The status page

Setup > **E-mail preferences** (`/mailpreferences/admin/status`) shows:

- **Problems**: what to fix first. "The organisation name or postal address of the mail footer is empty ...", "The mail gate
  is disabled ...", "%count mails without a category in the last 7 days.", "%count mails could not be sent or checked in
  the last 24 hours.", a missing table.
- **Numbers**: people with stored preferences, people who turned all optional e-mail off, waiting confirmations,
  addresses on the suppression list, optional mail sent and blocked in the last 24 hours, mail without a category in the
  last 7 days, errors, consent records.
- **Settings in use**: the gate, the organisation and the postal address, where links point to, whether the site secret
  exists, the last mail through the gate and, when the bounce reader is set up, when it last read the mailbox.

The same on the console:

```bash
./console exp:mail:status
```

```
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
...
Problems
  [warning] The organisation name or postal address of the mail footer is empty (mailpreferences.ini [FooterSettings]); the law requires both in every optional mail.
  [notice] 128 mails without a category in the last 7 days.
PASS
```

`--json` gives one object for monitoring; the command ends with FAIL (exit code 1) only for a problem of level error.

**Mail without a category** is not an error: it is sent exactly as before. But it carries no footer and no unsubscribe
link, so if it is commercial it does not comply. The status names the file that sent it; give that code a category
([the developer's guide](mail-preferences-developer.md)), or, for mail that really is essential, list its file, class or
From address in `[GateSettings] EssentialSenders[]`.

The gate writes one JSON line per decision to `var/<vardir>/log/mailgate.jsonl` (`[GateSettings] LogFile`), rotated at
5 MB, with no address in it.

## 4. Categories

Setup > E-mail preferences > **Categories** lists every category with its kind, frequencies, double opt-in and where it
is defined (settings file, extension, made here).

- **New category**: an identifier (lower case letters, digits, underscores, starting with a letter; it cannot be changed
  later), a name and a description as people see them, the frequencies people may choose, and whether a confirmation is
  asked first. A category made here is always optional and off until a person turns it on. Code sends mail in it with
  `eZMail::setCategory( '<identifier>' )`.
- **Edit** a category of the settings or of an extension: only its name and description change; they are stored in the
  database and shown instead. **Use the defined name and description again** removes that change.
- **Remove this category** (made here only): the category disappears from the pages; the choices people made stay in
  their records.

The categories of the settings are in `settings/mailpreferences.ini [CategorySettings] Categories[]`, one block
`[Category_<identifier>]` each. Never set `DefaultOn=true` for an optional category: it would send mail to people who did
not ask for it, which the EU rules and CASL forbid for marketing.

## 5. A person's preferences, on their request

Setup > E-mail preferences > **A user**: find the user by name, login, address or ID, open them, and you see their page
as they see it, with their consent history. Changes you make are recorded with the source "Changed by an administrator"
and your user id. Turning on a newsletter or offers for them sends them the confirmation link; it starts only when they
confirm. **Download as JSON** and **Download as CSV** (policy `mailpreferences/export`) give their data.

The same on the console, also for an address without an account:

```bash
./console exp:mail:preferences show --email=reader@docs.invalid
```

```
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
```

Addresses are hidden in the output unless you add `--addresses`. **Never switch a double opt-in category on from the
console without `--no-mail` on an installation whose transport really sends**; `--source=import` switches it on at once
and is meant only for consent you can prove was confirmed elsewhere, with the proof in `--wording`:

```bash
./console exp:mail:preferences set --email=reader@docs.invalid --category=marketing --state=on --source=import --wording="Imported with the opt-in of 2026-10-01"
```

```
r***@docs.invalid: marketing is on
PASS
```

`./console exp:mail:preferences link --email=...` prints the person's preference link, to answer a request by mail.

## 6. Requests under data protection law

| Request | What to do |
|---|---|
| "What do you have about me?" | The person can download it on their page. Or: `./console exp:mail:preferences export --email=<address> --format=json --output=var/tmp/request.json` (the file is written with mode 0600) |
| "Stop sending me anything" | Their unsubscribe link does it. Or add the address to the suppression list with reason `legal` (section 7) |
| "Delete my data" | `./console exp:mail:preferences erase --email=<address> --yes` |

Erasure answers:

```
r***@docs.invalid: 4 preferences and 1 pending confirmations removed, 9 consent log rows anonymised
PASS
```

The anonymised rows keep the time, category and change, without address, IP or user, so you can still prove that mail
stopped. Without `--yes` the command refuses ("erase removes the preferences and anonymises the consent log: add --yes").
Removing a user account does not yet erase that account's preferences; erase them on the console when you remove an
account on request.

## 7. The suppression list

No optional mail goes to an address on the list: hard bounces, complaints (marked as spam), "stop all optional e-mail"
requests, legal requests, entries made by an administrator, and the newsletter blacklist. Essential mail still goes. Only a
hash of the address is stored, so the list cannot be read; you find an entry by typing the address.

Setup > E-mail preferences > **Suppression list**: **Check an address**, **Add an address** (with a reason and a note
without personal data) and **Lift** an entry. The console:

```bash
./console exp:mail:suppression add --email=other@docs.invalid --reason=legal --note="Docs example"
./console exp:mail:suppression check --email=other@docs.invalid
./console exp:mail:suppression list --reason=legal --limit=5
./console exp:mail:suppression lift --email=other@docs.invalid
```

```
suppressed (hash 0b2dc01385cf4440…)
suppressed: legal (hash 0b2dc01385cf4440…)
1 entries (legal)
  0b2dc01385cf4440…  legal            2026-10-04 23:39  Docs example
lifted
```

A person who stopped all mail themselves lifts their own entry by turning optional e-mail on again on their page. Entries
for bounces, complaints and legal requests stay until an administrator lifts them, and they also stop "send me a link"
and confirmation mails. Entries are kept forever by default (`[SuppressionSettings] RetentionDays=0`).

**Bounces.** The bounce reader of the integration reads a bounce mailbox set in a settings override and adds hard
bounces; it is disabled until its mailbox is filled in, and the status page then shows its last read. Without it, add
hard bounces by hand with reason `bounce`.

## 8. The consent log

Setup > E-mail preferences > **Consent log**: every change, newest first, with the person, the category, the change, the
source, the text shown and the IP address. Filter by person (address or user id), category, source and a date range; the
filters are in the address, so a filtered view can be bookmarked. **Export as CSV** (policy `mailpreferences/export`)
exports exactly what the filter shows. The CSV holds addresses and IP addresses: store it like any personal data, and
delete it when the request it answers is done. Cells that start with `=`, `+`, `-` or `@` are written with a leading
`'` so a spreadsheet does not run them.

```bash
./console exp:mail:consent list --email=reader@docs.invalid --limit=10
```

```
6 rows
  2026-10-04 23:39:11  r***@docs.invalid      -              master_on   off -> on  admin
  2026-10-04 23:39:09  r***@docs.invalid      -              master_off  on -> off  admin
  2026-10-04 23:39:05  r***@docs.invalid      marketing      on          off -> on  import
  2026-10-04 23:39:05  r***@docs.invalid      newsletter     pending     off -> pending  admin
  2026-10-04 23:39:04  r***@docs.invalid      content        frequency   immediate -> weekly  admin
  2026-10-04 23:39:03  r***@docs.invalid      content        on          off -> on  admin
PASS
```

## 9. Retention and the cronjob

The cronjob part `mailpreferences` is in the group `infrequent` and can run alone:

```bash
php runcronjobs.php mailpreferences
```

It removes the consent log rows of people who are gone (an anonymised record, a removed account, an address with no
preference left) once they are older than `[ConsentSettings] RetentionDays` (1095 days: the account's lifetime plus three
years), removes confirmations that waited longer than `[ConsentSettings] PendingDays` (7; the category is off again) and,
when `[SuppressionSettings] RetentionDays` is above 0, old suppression entries. To see what it would do:

```bash
./console exp:mail:consent cleanup --dry-run
```

```
Would remove: 0 consent log rows, 0 expired confirmations
PASS
```

## 10. Testing the gate without sending mail

`exp:mail:gate` says what the gate would decide, and with `--write` puts a test mail through the gate into files, never to
a mail server:

```bash
./console exp:mail:gate --category=content --to=reader@docs.invalid,other@docs.invalid --write --mail-file-dir=var/tmp/gate-mail
```

```
Category content: allow
  r***@docs.invalid                allow
  o***@docs.invalid                off

Written: 1 file(s) in var/tmp/gate-mail, decision partly_blocked, result true
  var/tmp/gate-mail/1791182348-1270746661.mail
PASS
```

The written mail ends with the footer and carries `List-Unsubscribe` and `List-Unsubscribe-Post: List-Unsubscribe=One-Click`.

**Never test by sending from the web pages.** Turning on a newsletter on a page, "send me a link" and an address change
send real mail through the site's transport.

## 11. Velocity and other persistent workers

The categories are read again for every request, so a category made in the admin shows at once on Velocity too. A
change of `settings/mailpreferences.ini` or of an override needs the INI cache cleared
(`php bin/php/ezcache.php --clear-tag=ini`) and, for Velocity, a restart of its workers, as any settings change. A changed
template of `design:mailpreferences/` needs the template caches cleared. The RFC 8058 POST is answered by Velocity like
any other request: it needs no session.

## 12. Checklist

1. Tables present: `./console exp:mail:status` shows five times `ok`.
2. `OrganisationName` and `OrganisationAddress` filled in; the status shows no footer warning.
3. `BaseURL` correct; open a manage link from `exp:mail:preferences link` in a browser.
4. The site secret is in the override file and in your backup.
5. The cronjob group `infrequent` (or the part `mailpreferences`) runs.
6. Your mail server signs `List-Unsubscribe` and `List-Unsubscribe-Post` with DKIM.
7. No commercial mail in the "without a category" list of the status page.
8. Read [the law checklist](../specifications/6.0/mail-preferences-compliance.md), including its gaps.

## Related pages

- [E-mail preferences: the user's guide](../features/6.0/mail-preferences.md)
- [E-mail preferences: the developer's guide](mail-preferences-developer.md)
- [The specification](../specifications/6.0/mail-preferences.md) and [the commands](../specifications/6.0/mail-preferences-cli.md)
- [Upgrade notes](../bc/6.0/mail-preferences.md)
- [Notifications: running them and fixing problems](notifications-administrator.md)
- [Operating a site](operating-a-site.md)
