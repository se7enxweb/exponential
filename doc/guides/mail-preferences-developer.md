# E-mail preferences: categories, the mail gate and testing

This guide is for developers who send mail from an extension or from kernel code. At the end your extension has a
category of its own that people switch on and off on their preference page, a handler that reads your older subscription
data as the state of that category, mail that goes through the gate with the footer and the one-click unsubscribe
headers, and a test that keeps every mail in files. The settings and tables are in [the specification](../specifications/6.0/mail-preferences.md);
running the system is in [the administrator's guide](mail-preferences-administrator.md).

The example of this guide was run on a test installation on 2026-10-04, with the file transport and addresses on
`.invalid`; its output is shown as it came, with the host, the sender and the site name replaced by example values.

## 1. The model in five sentences

1. Every mail belongs to a **category**: an identifier such as `newsletter` or `security`, declared with
   `eZMail::setCategory()` (or the header `X-Exp-Mail-Category`).
2. A category is **essential** (always sent: account security, orders, legal notices, admin alerts) or **optional**
   (sent only to people who turned it on; off by default).
3. `eZMailTransport::send()` hands every mail to `expMailGate::dispatch()` before the transport sees it, for every
   transport.
4. For optional mail the gate asks `expMailPreferences::decision()` for each recipient (`allow`, `off`, `pending`,
   `master_off`, `suppressed`), sends one mail to each allowed recipient with the footer and the `List-Unsubscribe` and
   `List-Unsubscribe-Post` headers, logs the others, and returns `true` even when everybody was blocked.
5. Mail without a category is sent unchanged and logged as "uncategorised" with the file that sent it, unless that
   sender is listed in `[GateSettings] EssentialSenders[]`.

## 2. Sending mail with a category

```php
$mail = new eZMail();
$ini = eZINI::instance();
$mail->setSender( $ini->variable( 'MailSettings', 'EmailSender' ) ?: $ini->variable( 'MailSettings', 'AdminEmail' ) );
foreach ( $addresses as $address )
    $mail->addBcc( $address );
$mail->setSubject( $subject );
$mail->setBody( $text );
$mail->setCategory( 'events' );          // the gate does the rest
eZMailTransport::send( $mail );
expMailGate::lastResult();               // category, decision, sent, blocked (reasons), result
```

You do not filter recipients yourself, add an unsubscribe link or check the suppression list: the gate does all three. Put
everybody who may want the mail into it. Rules:

- **Always set a sender.** The gate does not change From, Reply-To or Subject, and an empty From reaches the transport
  as it is.
- **Give the mail a category, or list it as essential.** Uncategorised mail has no footer and no unsubscribe link. A
  password reset or an order confirmation is `security` or `orders`.
- **Do not split the list yourself into one mail per recipient**: with `[GateSettings] SplitRecipients=enabled` (the
  default) the gate does it, and each copy carries the recipient's own link.
- **Check early when the work is expensive.** The gate decides at sending time; when building a mail costs a lot (a
  digest, a rendered newsletter), ask first and skip people who would be blocked anyway:
  `expMailPreferences::forRecipient( expMailRecipient::fromUser( $user ) )->allows( 'events' )`.
- **HTML mail**: set the content type `text/html`; the footer goes before `</body>`. A body given as an `ezcMailPart`
  (multipart) gets the footer in its text and HTML parts.

## 3. A category of your own

Declare it in your extension's `settings/mailpreferences.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[CategorySettings]
Categories[]=events

[Category_events]
Name=Events
Description=Invitations to our events and webinars.
Essential=false
DefaultOn=false
Frequencies[]
DoubleOptIn=false
HandlerClass=myEventsMailCategoryHandler

*/ ?>
```

| Key | Meaning |
|---|---|
| `Name`, `Description` | Shown on the preference page and the registration form, and recorded in the consent log as the text shown. Translated through the context `kernel/mailpreferences/categories` |
| `Essential` | `true`: never switchable, always sent. Only for mail a person cannot do without; never for anything with advertising |
| `DefaultOn` | Keep `false`. `true` makes the category opt-out, which the EU rules and CASL do not allow for marketing |
| `Frequencies[]` | `immediate`, `daily`, `weekly`; empty: the category keeps its own schedule. Your code reads the person's choice with `expMailPreferences::frequency()` |
| `DoubleOptIn` | `true` for newsletters and marketing: switching on sends a confirmation link first, and nothing is sent until it is confirmed |
| `HandlerClass` | Optional; section 4 |

Categories of extensions show the source "Extension" on the administrator's category page. An administrator can rename
and describe them there; the identifier, `Essential` and `HandlerClass` stay as your settings say. Code can also add a
category for one process with `expMailCategoryRegistry::instance()->register( new expMailCategory( 'events', array( ... ) ) )`.

## 4. Mapping older data with a handler

When your extension already keeps its own subscriptions, a handler reads them as the state of the category, so nothing
has to be migrated and your old pages keep working. A choice the person makes on the preference page is stored by the
preference system and wins.

```php
class myEventsMailCategoryHandler implements expMailCategoryHandler
{
    /** the old list of the extension: addresses that asked for event mail before the preference system */
    public static $legacyList = array( 'early@docs.invalid' );

    public function stateFor( expMailRecipient $recipient, expMailCategory $category )
    {
        // an address on the old list counts as "on" until the person chooses on the preference page
        return in_array( $recipient->email(), self::$legacyList, true ) ? true : null;
    }

    public function frequencyFor( expMailRecipient $recipient, expMailCategory $category )
    {
        return null;   // the category's first frequency
    }

    public function changed( expMailRecipient $recipient, expMailCategory $category, $state, expConsentContext $context )
    {
        // keep your own data in step: $state is 'on', 'off' or 'pending'
    }
}
```

- `stateFor()` returns `true`, `false` or `null` (no opinion: the category's default, off). It is asked only for people
  without a stored choice.
- `frequencyFor()` returns `immediate`, `daily`, `weekly` or `null`.
- `changed()` is called after every change of the person's state; an exception in it is logged and does not stop the change.
- Optionally, `subscriptions( expMailRecipient, expMailCategory )` returns a list of `hash( name, status, active, url )`; the
  preference page lists them under the category ("Your subscriptions:"), for example the lists of a newsletter.

The kernel's own handler, `expNotificationMailCategoryHandler`, reads subtree notification rules and digest settings as
the categories `content` and `collaboration`.

### What the example did

With the category above set in the process and the handler loaded, three addresses got one announcement. `early@` is on
the old list, `new@` turned the category on on the page, `nobody@` did nothing:

```
PASS the category is registered, source extension
PASS the old list reads as on
PASS a new address is off (opt-in)
  handler: events is now on for n***@docs.invalid (source page)
  lastResult: {"decision":"partly_blocked","sent":2,"blocked":["off"]}
PASS two mails, one each, the third address blocked
```

One of the two mails, as the file transport wrote it (links shortened, some headers left out):

```
Subject: Our autumn webinar
From: info@example.com
Bcc: new@docs.invalid
X-Exp-Mail-Category: events
List-Unsubscribe: <https://www.example.com/mailpreferences/unsubscribe/m1...>
List-Unsubscribe-Post: List-Unsubscribe=One-Click
Join us on 12 November.

-- 
You get this e-mail because you turned on "Events" on Example Site.
Unsubscribe with one click: https://www.example.com/mailpreferences/unsubscribe/m1...
Choose which e-mail you get: https://www.example.com/mailpreferences/manage/m1...

Example Events Ltd
Example Street 1, 12345 Town
```

## 5. Changing preferences from code

```php
$recipient = expMailRecipient::fromUser( $user );            // or fromAddress( 'a@example.com' ), fromUserId( 14 )
$prefs = expMailPreferences::forRecipient( $recipient );
$context = expConsentContext::fromRequest( 'page', $textThePersonSaw );
$prefs->set( 'events', true, $context );                     // 'on', 'pending_confirmation' or 'off'
$prefs->setMaster( false, $context );                        // all optional mail off, categories kept
$prefs->decision( 'events' );                                // allow, off, pending, master_off, suppressed, unknown_category
```

- **Record what the person saw.** `$wording` is kept in the consent log as the proof of consent. Build it from the same
  translated strings your page shows.
- **Sources**: `page`, `link`, `admin`, `signup`, `confirm`, `system`; `import` and `bridge` switch a double opt-in category
  on at once and must only be used for consent confirmed elsewhere. `expConsentContext::system( $wording )` is for
  cronjobs and commands; set `$context->sendConfirmation = false` to store a pending double opt-in without mailing it.
- An address that belongs to an account is that account: `fromAddress()` finds it (pass `false` as second argument to
  get the bare address).
- Essential categories throw `InvalidArgumentException` on `set()`.

## 6. Links and tokens

`expMailPreferencesService::manageURL( $recipient )` and `unsubscribeURL( $recipient, $category )` give absolute links that
work without login. They are tokens `m1<base64url>`: the purpose, the recipient (user id, or the address of a person
without account), the category and the expiry, encrypted and authenticated with AES-256-GCM under a key derived from the
site secret. Nothing is stored per link, a link cannot be read or changed, and changing the secret invalidates all of them.
`expMailToken::verify( $token, $purpose )` returns the payload or `null`, and `expMailToken::lastError()` says why
(`malformed`, `tampered`, `purpose`, `expired`).

## 7. Extension points

| Point | What it is for |
|---|---|
| `mailpreferences.ini [CategorySettings] Categories[]` + `[Category_<id>]` | Categories of an extension |
| `expMailCategoryHandler` (`HandlerClass`) | Read older data as the state; hear about changes; optional `subscriptions()` |
| `[GateSettings] EssentialSenders[]` | Mark uncategorised mail of a file, class or From address as essential |
| `[SuppressionSettings] Listeners[]` | Classes told when an address is suppressed or lifted: static `suppressionAdded( $email, $hash, $reason )`, `suppressionLifted( $hash, $email, $reason )` (keeps another block list in step) |
| `[FooterSettings] Template` and `design:mailpreferences/mail/*.tpl` | The footer and the confirm, link and address change mails, per design |
| `design:mailpreferences/parts/page_start.tpl`, `page_end.tpl` | Frame the preference pages in your design |
| `design:mailpreferences/parts/account_link.tpl` | The "E-mail preferences" box for your own pages (`context` = `profile`, `notification`, `newsletter`) |
| `MailPreferencesPage::signupCategories()`, `storeSignup()` | The unticked boxes of a registration form of your own |

## 8. Testing without sending mail

Never test with the site's transport: on a production server it sends. Force the file transport in the test's own process,
refuse to run otherwise, and use only addresses on `.invalid`:

```php
$ini = eZINI::instance();
$ini->setVariable( 'MailSettings', 'Transport', 'file' );
$ini->setVariable( 'MailSettings', 'FileTransportDirectory', 'var/tmp/my-test-mail' );
if ( trim( $ini->variable( 'MailSettings', 'Transport' ) ) !== 'file' )
    throw new RuntimeException( 'The mail transport is not the file transport: the test refuses to run.' );
```

`expMailGate::setLogFileForTest( $file )` sends the gate's log to a file of the test. Remove the rows the test made
(`expmail_preference`, `expmail_pending`, `expmail_consent_log` by recipient key, `expmail_suppression` by hash), and
skip where there is no installation with `ezpLiveInstallation::requireOrSkip()`, so the GitHub workflow stays green. The
two kernel test classes are the model:

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/mailpreferences/
```

```
OK (45 tests, 660 assertions)
```

`MailPreferencesCoreTest` (MP-01 to MP-20) covers the core and the gate, `MailPreferencesComplianceTest` (LAW-01 to
LAW-24) the [law checklist](../specifications/6.0/mail-preferences-compliance.md). On the console,
`./console exp:mail:gate --category=events --to=a@docs.invalid --write` puts a test mail through the gate into files.

## Related pages

- [The specification](../specifications/6.0/mail-preferences.md), [the commands](../specifications/6.0/mail-preferences-cli.md)
- [The law checklist](../specifications/6.0/mail-preferences-compliance.md)
- [The administrator's guide](mail-preferences-administrator.md)
- [Notifications: architecture, extending and testing](notifications-developer.md)
- [Upgrade notes](../bc/6.0/mail-preferences.md)
