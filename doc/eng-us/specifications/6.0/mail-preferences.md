# E-mail preferences and consent: the specification

The reference of the e-mail preference system of Exponential 6.0.15: the classes, the tables, the settings, the flows,
the decisions of the mail gate and the format of the links. Read it when you need an exact answer; the walk-throughs are
[the administrator's guide](../../guides/mail-preferences-administrator.md) and
[the developer's guide](../../guides/mail-preferences-developer.md). The law requirements and the tests that prove them
are on [the law checklist](mail-preferences-compliance.md).

## Parts

| Part | Where |
|---|---|
| Classes (autoloaded, kernel) | `kernel/classes/mailpreferences/`: `expMailCategory`, `expMailCategoryRegistry`, `expMailCategoryHandler`, `expMailRecipient`, `expMailPreferences`, `expConsentContext`, `expConsentLog`, `expMailSuppression`, `expMailToken`, `expMailSecret`, `expMailGate`, `expMailPreferencesService`, the row classes `expMailCategoryRow`, `expMailPreferenceRow`, `expMailPendingRow` |
| The gate's hook | `lib/ezutils/classes/ezmailtransport.php` (`eZMailTransport::send()` calls `expMailGate::dispatch()`), `lib/ezutils/classes/ezmail.php` (`setCategory()`, `category()`) |
| Module | `kernel/mailpreferences/module.php`; view classes `kernel/private/classes/views/mailpreferences/`; shared page code `Exponential\Service\MailPreferencesPage` (`kernel/private/classes/services/mailpreferencespage.php`) |
| Templates | `design/standard/templates/mailpreferences/` (pages, `parts/`, `admin/`, `mail/`); frames in `design/admin` and `design/admin4` (`parts/page_start.tpl`, `page_end.tpl`) |
| Commands | `exp:mail:status`, `exp:mail:preferences`, `exp:mail:suppression`, `exp:mail:consent`, `exp:mail:gate` ([reference](mail-preferences-cli.md)) |
| Cronjob part | `mailpreferences` (`cronjobs/mailpreferences.php`), also in the group `infrequent` |
| Settings | `settings/mailpreferences.ini`; `site.ini [RoleSettings] PolicyOmitList[]`; `menu.ini` entries `my_mail` and `mailpreferences`; `cronjob.ini` |
| Texts | `share/translations/ger-DE/translation.ts`, contexts `design/standard/mailpreferences`, `design/admin/mailpreferences`, `kernel/mailpreferences`, `kernel/mailpreferences/categories`, `kernel/mailpreferences/mail`, `kernel/mailpreferences/status` |

## Categories

`expMailCategory` is a value object: `identifier`, `name`, `description`, `essential`, `defaultOn` (always false for an
essential category), `frequencies` (subset of `immediate`, `daily`, `weekly`), `doubleOptIn`, `source` (`ini`, `extension`,
`admin`), `handlerClass`. An identifier is lower case letters, digits and underscores and starts with a letter.

`expMailCategoryRegistry::instance()` merges, in this order:

1. `mailpreferences.ini [CategorySettings] Categories[]` with a block `[Category_<identifier>]` each; source `ini` for the
   identifiers listed in the shipped `settings/mailpreferences.ini`, `extension` for the others.
2. Rows of `expmail_category`. A row with the identifier of a category of step 1 overrides its name, description,
   default, frequencies and double opt-in; `essential` and an empty `handler_class` stay as the settings say. Other rows
   are categories of source `admin`.
3. `register()`: categories added in code for this process.

The list is read again for each request (`$_SERVER['REQUEST_TIME_FLOAT']`), so persistent workers see admin changes.
`all()`, `optional()`, `essential()`, `get()`, `iniCategories()`, `saveAdmin()`, `removeAdmin()`, `reset()`.

The shipped categories:

| Identifier | Kind | Frequencies | Double opt-in | Handler |
|---|---|---|---|---|
| `content` | optional | immediate, daily, weekly | no | `expNotificationMailCategoryHandler` |
| `collaboration` | optional | immediate, daily, weekly | no | `expNotificationMailCategoryHandler` |
| `newsletter` | optional | own schedule | yes | (the newsletter bridge) |
| `marketing` | optional | own schedule | yes | |
| `system` | optional | own schedule | no | |
| `security` | essential | | | |
| `orders` | essential | | | |
| `legal` | essential | | | |
| `admin` | essential | | | |

## Recipients

`expMailRecipient` identifies a person. Its key is `u:<user id>` for an account and `a:<hash>` for an address without
account, where `<hash>` is `expMailSuppression::hash( $email )`. `fromAddress()` returns the account when the address
belongs to one (second argument `false`: the bare address). Also `fromUser()`, `fromUserId()`, `fromToken()`,
`fromPayload()`, `fromKey()`. `logKey()` is what the gate log stores: the user id, or the first characters of the hash.

## Tables

All five are in the kernel schema of MySQL, PostgreSQL and SQLite, in `share/db_schema.dba`, and in
`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`. Times are Unix timestamps.

### expmail_preference

One row per person and category, plus the main switch (category `_master`).

| Column | Meaning |
|---|---|
| `id` | key |
| `recipient_key` | `u:<id>` or `a:<hash>`; unique together with `category` |
| `user_id` | the account, 0 for an address |
| `category` | identifier, or `_master` |
| `state` | `on`, `off`, `pending` |
| `frequency` | `immediate`, `daily`, `weekly` or empty |
| `created`, `modified` | times |

No row means "no choice made": the handler's reading, else the category default (off).

### expmail_consent_log

One row per change; never updated except by anonymisation.

| Column | Meaning |
|---|---|
| `id`, `created` | key, time |
| `recipient_key`, `user_id`, `email` | the person; `email` is empty for an account |
| `category` | identifier, empty for the main switch and for whole-person actions |
| `action` | `on`, `off`, `pending`, `confirm`, `master_on`, `master_off`, `frequency`, `erase`, `export`, `suppress`, `unsuppress`, `email_change` |
| `old_value`, `new_value` | the state or frequency before and after |
| `source` | `page`, `link`, `admin`, `import`, `signup`, `bridge`, `confirm`, `system` |
| `wording` | the exact text the person saw (or the `--wording` of a command) |
| `ip`, `actor_user_id`, `siteaccess` | where the change came from and who made it |
| `anonymised` | 1 after erasure: `recipient_key` is `x:<irreversible hash>`, `email`, `ip`, `user_id` are emptied |

Every row also writes an audit event: `access.user.consent.erase`, `access.user.consent.export`, or
`access.user.consent.change` for every other action.

### expmail_suppression

| Column | Meaning |
|---|---|
| `email_hash` | `sha256( lowercase( trim( email ) ) . derive( 'hash' ) )`, unique; the address is never stored |
| `reason` | `bounce`, `complaint`, `unsubscribe_all`, `legal`, `admin`, `bridge` (`[SuppressionSettings] Reasons[]`) |
| `note` | free text for administrators; an address in it is removed |
| `created`, `created_by` | time and user id |

### expmail_pending

A double opt-in waiting for its confirmation.

| Column | Meaning |
|---|---|
| `recipient_key`, `user_id` | the person |
| `kind` | `category` or `email_change` (and `link` for the rate limit of "send me a link") |
| `category` | for `category` |
| `data` | JSON; for `email_change` the new address |
| `created`, `expires` | times; `expires` = created + `[ConsentSettings] PendingDays` |

### expmail_category

The administrator's categories and overrides: `identifier` (unique), `name`, `description`, `essential`, `default_on`,
`frequencies` (comma separated), `double_opt_in`, `handler_class`, `priority`, `created`, `modified`.

## The decision for one recipient

`expMailPreferences::decision( $category )`, in this order:

| Result | When |
|---|---|
| `unknown_category` | no such category |
| `allow` | an essential category |
| `suppressed` | the address is on the suppression list |
| `master_off` | the person turned all optional mail off |
| `pending` | the category waits for its confirmation |
| `off` | the category is off (stored, read by the handler, or the default) |
| `allow` | otherwise |

`allows()` is `decision() === 'allow'`.

## The gate

`expMailGate::dispatch( eZMail, eZMailTransport )`:

| Mail | What happens | Logged as |
|---|---|---|
| No category | sent unchanged | `uncategorised` with the sending file (`why: no_category`), or `essential` (`why: essential_sender`) when the From address, the file or a class of the call stack is in `[GateSettings] EssentialSenders[]` |
| Unknown category | sent unchanged | `uncategorised`, `why: unknown_category` |
| Essential category | sent with `X-Exp-Mail-Category`, no footer | `sent`, `why: essential` |
| Optional, nobody allowed | nothing sent, `true` returned | `blocked` per recipient with the decision |
| Optional, some allowed | one mail per allowed recipient (To, Cc and Bcc kept in their field), each decorated; the mail object gets its recipients and body back afterwards | `sent` per recipient, `blocked` per blocked recipient |
| Optional, `SplitRecipients=disabled` and several allowed | one mail to all allowed; footer with the "send me a link" page, no `List-Unsubscribe` | `sent` per recipient |
| `Gate=disabled` | optional mail goes to everybody except suppressed addresses, still decorated | as above |
| An exception in the gate | optional mail is not sent (`false`); other mail goes | `error` |

`decorate( eZMail, recipient, category )` adds `X-Exp-Mail-Category`, and for optional mail with a known recipient the
headers

```
List-Unsubscribe: <https://www.example.com/mailpreferences/unsubscribe/m1...>
List-Unsubscribe-Post: List-Unsubscribe=One-Click
```

and the footer of `[FooterSettings] Template` (built-in text when the template is missing): text mail at the end after
`-- `, HTML before `</body>`, every text part of an `ezcMailPart` body. It never changes From, Reply-To, Subject or
other headers.

The log (`[GateSettings] LogFile`, default `log/mailgate.jsonl` in the var directory, rotated to `.1` at `MaxLogSize`) has
one JSON object per line: `t` time, `d` decision, `c` category, `why`, `r` the recipient's log key, `s` the sender file.
It holds no address. `expMailGate::stats( $since )` counts it for the status page; `lastResult()` describes the last
dispatch (`category`, `decision`: `sent`, `partly_blocked`, `blocked`, `essential`, `uncategorised`, `unknown_category`;
`sent`, `blocked`, `result`).

## Links (tokens)

`expMailToken::create( recipient, purpose, category = null, ttl = null, extra = array() )`:

- purposes `unsubscribe`, `manage`, `confirm`;
- payload JSON `{ p: purpose, t: issued, x: expires (0 never), u: user id, e: address (only without account),
  c: category, i: pending id }`;
- encrypted with AES-256-GCM, 12 byte IV, 16 byte tag, additional data `exp-mail-token`, key
  `HMAC-SHA-256( 'exp-mailpreferences:token', site secret )`;
- written as `m1` + base64url( IV . ciphertext . tag ).

`verify( $token, $purpose )` returns `purpose, user, email, category, pending, issued, expires` or `null`;
`lastError()`: `malformed`, `tampered`, `purpose`, `expired`. Default lifetimes, `[TokenSettings] TTL[]`: unsubscribe 0
(never), manage 0, confirm 604800 (7 days); a "send me a link" link `RequestLinkTTL` (86400). The address is
`<BaseURL>/mailpreferences/<view>/<token>`.

The site secret (`expMailSecret`): 32 random bytes, base64 in `settings/override/mailpreferences.ini.append.php`
`[SecretSettings] TokenSecret`, generated on first use under a lock. `derive( $purpose )` gives the key for `token`,
`hash` (suppression and address keys) and `anonymise`.

## Flows

**Turning a category on** (`expMailPreferences::set( $category, true, $context )`): a category without double opt-in, or
a context of source `confirm`, `bridge` or `import`, is `on` at once (log `on`, or `confirm`). Otherwise the state becomes
`pending`, a row of `expmail_pending` is written, the log says `pending`, and the confirmation mail (category `security`,
template `mail/confirm.tpl`) goes to the address with a `confirm` link, unless `$context->sendConfirmation` is false or
the address is suppressed for a bounce, complaint or legal reason. Turning on by `page`, `link`, `confirm` or `signup` lifts
the person's own `unsubscribe_all` suppression.

**Confirming** (`mailpreferences/confirm/<token>`): GET shows one button; the POST of **Yes, confirm** calls
`expMailPreferencesService::confirm()`: `confirmed`, `already`, `expired` (the pending row is removed) or `invalid`.

**Unsubscribing** (`mailpreferences/unsubscribe/<token>`): GET shows one button; its POST, or a POST with the body
`List-Unsubscribe=One-Click` (RFC 8058; no cookie, no form token), calls `expMailPreferencesService::unsubscribe()`: a link
with a category turns the category off, a link without one turns the main switch off. The one-click POST answers 200
`text/plain` ("You are unsubscribed."), or 400 for a link that does not work. `unsubscribeAll()` turns the main switch off
and adds the address to the suppression list (`unsubscribe_all`).

**Send me a link** (`mailpreferences/request`): `requestLink( $email )` returns `sent`, `invalid`, `rate_limited`
(`[TokenSettings] RequestLinkLimit` per hour) or `blocked`; the page shows the same answer for every well-formed address.

**Address change** (`requestEmailChange( $user, $email, $context )`): `invalid`, `unchanged`, `in_use` or
`pending_confirmation`; the account keeps its address until the link sent to the new address is confirmed.

**Sign-up**: `MailPreferencesPage::signupCategories()` gives the boxes of the registration form, none ticked;
`storeSignup()` stores only what was ticked, with source `signup` and the text of the form.

**Export and erasure**: `export()` returns the recipient, the main switch, every category with state, frequency and time,
pending confirmations and the whole consent log; `exportToCsv()` writes it as `section,key,value,detail` rows.
`erase( $context )` writes an `erase` row, deletes the preferences and pending rows and anonymises every log row of the
person.

**Retention** (`expConsentLog::cleanup( $dryRun )`, cronjob part `mailpreferences`): removes log rows older than
`[ConsentSettings] RetentionDays` of people who are gone (anonymised, a removed account, an address without preference),
removes expired pending rows and turns their categories off, and removes suppression entries older than
`[SuppressionSettings] RetentionDays` when that is above 0.

## Module views

| View | Login | Purpose |
|---|---|---|
| `settings` | signed in | the person's page; posts `MailPreferencesForm` = `master` (`MasterOffButton`, `MasterOnButton`) or `categories` (`CategoryShown[]`, `Category[<id>]`, `Frequency[<id>]`, `StoreButton`) |
| `manage/<token>` | link | the same page by a `manage` link |
| `unsubscribe/<token>` | link | section "Flows" |
| `confirm/<token>` | link | section "Flows" |
| `request` | none | "Send me a link" |
| `export/<json|csv>[/(token)/<token>]` | signed in or link | the data download |
| `admin/<status|categories|suppression|consent|user>[/<id>]` | `mailpreferences/administrate` | the administrator's pages; consent filters `?person=&category=&source=&from=&to=`, `&export=csv` (policy `mailpreferences/export`); `/(offset)/<n>` |

Pages reached by a personal link are sent with `Cache-Control: private, no-store, max-age=0`, `Referrer-Policy: no-referrer`
and `X-Robots-Tag: noindex, nofollow`. Every page works without JavaScript.

## Settings: mailpreferences.ini

| Block | Key | Default | Meaning |
|---|---|---|---|
| `GateSettings` | `Gate` | `enabled` | `disabled`: optional mail goes to everybody but suppressed addresses (still decorated) |
| | `EssentialSenders[]` | the kernel's account, order, audit and setup senders, `expMailPreferencesService` | uncategorised mail of these senders is logged as essential |
| | `LogFile` | `log/mailgate.jsonl` | relative to the var directory |
| | `MaxLogSize` | `5242880` | bytes before rotation |
| | `SplitRecipients` | `enabled` | one optional mail per recipient |
| `CategorySettings` | `Categories[]` | nine identifiers | the order of the page |
| `Category_<id>` | `Name`, `Description`, `Essential`, `DefaultOn`, `Frequencies[]`, `DoubleOptIn`, `HandlerClass` | | see the developer's guide |
| `FooterSettings` | `OrganisationName`, `OrganisationAddress` | empty | sender and postal address in the footer; `\n` for a line break |
| | `Template` | `design:mailpreferences/mail/footer.tpl` | the footer template |
| `LinkSettings` | `BaseURL` | empty (https:// + SiteURL of the default siteaccess) | where links point to |
| `TokenSettings` | `TTL[unsubscribe]`, `TTL[manage]`, `TTL[confirm]` | `0`, `0`, `604800` | link lifetimes in seconds |
| | `RequestLinkTTL`, `RequestLinkLimit` | `86400`, `3` | "send me a link" |
| `ConsentSettings` | `RetentionDays`, `PendingDays` | `1095`, `7` | retention |
| `SuppressionSettings` | `RetentionDays` | `0` (forever) | |
| | `Reasons[]` | six reasons | |
| | `Listeners[]` | empty | classes told about added and lifted entries |
| `SecretSettings` | `TokenSecret` | generated into the override | never commit |

## Related pages

- [The law checklist](mail-preferences-compliance.md)
- [The commands](mail-preferences-cli.md)
- [The administrator's guide](../../guides/mail-preferences-administrator.md), [the developer's guide](../../guides/mail-preferences-developer.md)
- [The user's guide](../../features/6.0/mail-preferences.md)
- [Upgrade notes](../../bc/6.0/mail-preferences.md)
- [Notifications](notifications.md)
