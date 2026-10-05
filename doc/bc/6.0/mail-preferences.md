# Upgrade notes: e-mail preferences and the mail gate

What changes when an installation moves to a 6.0.15 with the e-mail preferences: the new tables, the mail gate that every
mail now passes, the mail that is split per recipient, the mail that is counted as uncategorised, and how to keep the old
behaviour where you need it. Read it if you upgrade, if your code or extension sends mail, or if your design overrides
the registration form. The system as it is now: [the user's guide](../../features/6.0/mail-preferences.md),
[the administrator's guide](../../guides/mail-preferences-administrator.md),
[the specification](../../specifications/6.0/mail-preferences.md).

## What you need to do

1. **Run the database update** of the line, `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`. It creates
   `expmail_category`, `expmail_preference`, `expmail_consent_log`, `expmail_suppression` and `expmail_pending`. Check with
   `./console exp:mail:status`: five times `ok`.
2. **Fill in the footer** in a settings override: `mailpreferences.ini [FooterSettings] OrganisationName` and
   `OrganisationAddress` (section 2.2 of the administrator's guide). Without them the status page warns and optional mail
   goes out without a postal address.
3. **Check where links point to**: `[LinkSettings] BaseURL`, empty means `https://` + `SiteURL` of the default
   siteaccess.
4. **Keep the site secret.** It is generated on first use into `settings/override/mailpreferences.ini.append.php`. Copy that
   file with every move of the installation and never commit it.
5. **Give roles the new policies** where needed: `mailpreferences/administrate`, `mailpreferences/export`. The person's own
   pages need none.
6. Clear the caches (`php bin/php/ezcache.php --clear-all --allow-root-user`), reload PHP-FPM or restart Velocity.
7. Open **Setup > E-mail preferences** and read the problem list. Then look at the mail your own code sends (section
   "Mail without a category").

## Behaviour changes

### Every mail passes the mail gate

`eZMailTransport::send()` hands each mail to `expMailGate::dispatch()` before the transport, for every transport
(sendmail, SMTP, file, and transports of extensions). What the gate does depends on the category the mail declares with
`eZMail::setCategory()` or the header `X-Exp-Mail-Category`:

| Mail | Before | Now |
|---|---|---|
| No category | sent | sent unchanged, and one line in `var/<vardir>/log/mailgate.jsonl` |
| Essential category (`security`, `orders`, `legal`, `admin`) | sent | sent, with the header `X-Exp-Mail-Category` |
| Optional category | sent to everybody in it | sent only to the recipients who turned the category on; with footer and `List-Unsubscribe` headers |

`eZMailTransport::send()` returns `true` for an optional mail whose recipients were all blocked, as if it was sent: the
caller has nothing to retry. `expMailGate::lastResult()` tells what happened.

### Optional mail to several recipients is split

An optional mail with several recipients is sent as one mail per allowed recipient, so each carries its own unsubscribe
link and nobody sees the other addresses. A recipient keeps its field (To, Cc or Bcc). After the send, the `eZMail` object
has its recipients and body back. A mail server log shows several messages where it showed one. To keep one mail for
all, set `[GateSettings] SplitRecipients=disabled`; such a mail gets a footer that links to the "send me a link" page and
no `List-Unsubscribe` header, which large mail providers expect from bulk senders.

### Optional mail gets a footer

The body of optional mail ends with the footer of `design:mailpreferences/mail/footer.tpl`: why the person gets it, the
one-click unsubscribe link, the preference link, and the organisation with its postal address. In an HTML mail it goes
before `</body>`. Code that parses its own sent mail, or tests that compare a whole body, see the footer.

### Mail without a category is counted

Mail without a category is not blocked, but the status page and `exp:mail:status` count it ("128 mails without a category
in the last 7 days") and name the file that sent it, so it can be given a category. Senders listed in
`[GateSettings] EssentialSenders[]` (a From address, a file relative to the installation, or a class in the call stack)
are counted as essential instead. The shipped list names the kernel's account, order, audit and setup senders.

### The registration form has unticked boxes

`user/register` shows **E-mail from us (optional)** with one unticked box per optional category
(`design:mailpreferences/parts/signup.tpl`, variable `$mail_categories`), in `design/standard`, `design/admin` and
`design/admin4`. A design that overrides `user/register.tpl` shows no boxes until it includes the part:

```
{include uri='design:mailpreferences/parts/signup.tpl' categories=first_set( $mail_categories, array() )}
```

Inside the form, before the buttons. What a new user ticks is stored after the registration.

### New links in the account pages and menus

The profile (`user/edit`), the notification settings and the current user box link to the preference page; the menu
`My account` has **My e-mail preferences** (`menu.ini Links[my_mail]`), the Setup menu **E-mail preferences**
(`Links[mailpreferences]`, policy `mailpreferences/administrate`). Designs that override these templates do not show the
links until they add `design:mailpreferences/parts/account_link.tpl`.

### Notification and other kernel mail

The notification categories `content` and `collaboration` read the existing subtree and collaboration rules and the digest
settings (`expNotificationMailCategoryHandler`): a user who follows items is "on" for content notifications, nothing is
migrated, and the old notification pages keep working. A user who turns the category or all optional mail off gets no
notification mail; their rules stay and come back when they turn it on again.

Content and collaboration mail follow the frequency the person chose on the preference page: daily or weekly, the items
wait for the general digest, which has a part for each (`notification/handler/ezcollaboration/view/digest_plain.tpl` is
new). The digest template `ezgeneraldigest/view/plain.tpl` was rewritten because it rendered no items at all; a design
that overrides it should take the same change (a `foreach` and literal includes).

### A new e-mail address waits for its confirmation

When a user changes the e-mail address of their account (user/edit, the administration's edit of a user, the account
services `changeEmail`), the account keeps its address until the new one is confirmed with the link sent to it; the old
address gets a notice. The preference page shows the waiting change. Imports and scripts that set the account with
`fromString()` (`login|email|...`) change it at once, as before.

### Opt-out categories are refused

An optional category with `DefaultOn=true` (in the settings, an extension's settings, an administrator's row or code)
is treated as off for everybody who never chose, and the status page and `exp:mail:status` warn with its identifier.
A site whose recipients are under a law that allows opt-out for that mail sets
`mailpreferences.ini [CategorySettings] AllowDefaultOn=enabled`; the status page then notes that opt-out is allowed.

### Removing an account erases its e-mail preferences

`eZUser::removeUser()` (called when a user object is purged) calls `expMailPreferences::eraseForRemovedAccount()`: the
preferences and pending confirmations of the account are removed, its consent log is anonymised (address, IP and user id
gone; the rows stay as the proof of each consent and withdrawal) and one `erase` row is added. The suppression list is
not touched: it holds only salted hashes and keeps blocking the address. An account that never had an e-mail preference
leaves nothing behind. Code that counted consent log rows by user id after a removal finds none.

### Optional mail always has a sender

Optional mail with an empty From gets `site.ini [MailSettings] EmailSender`, else `AdminEmail`; the gate log records
`from_fallback` with the file that sent it, and the status page counts it. With neither setting the mail is not sent
(`expMailGate::lastResult()`: blocked, `no_sender`). Essential and uncategorised mail is unchanged (the transports fill
an empty sender as before).

### The public site name, the privacy notice and the link lifetimes

- The footer and the mail templates get `$site_name` (and `$privacy_url`) from `expMailPreferencesService::renderTemplate()`:
  `OrganisationName` when set, else the SiteName of the public siteaccess. A command or a cronjob that runs with the
  administration siteaccess no longer puts "Admin" into the footer. An overridden mail template that reads
  `ezini( 'SiteSettings', 'SiteName' )` should use `$site_name` instead.
- `[FooterSettings] PrivacyURL` (new, empty) links the privacy notice from the footer of optional mail and from the
  preference page. Empty: the node of `menu.ini [SiteInfo] PrivacyPolicyID` of the public siteaccess when it exists, else
  no link, and the status page says so.
- `[TokenSettings] TTL[unsubscribe]` and `TTL[manage]` between 0 and 60 days are raised to 60 days (5184000 s), with a
  warning on the status page. 0 still means "never expires".

### What is left to the site on purpose

- **Mail without a category** is sent without footer or unsubscribe link, as before the gate, so existing code keeps
  working. Commercial mail sent that way does not comply; the status page counts it and names its sender. Give such mail
  a category.
- **`SplitRecipients=disabled`** sends one mail to all recipients: it cannot carry a personal `List-Unsubscribe` header,
  and its footer links to "Send me a link" instead of a one-click link. Large mail providers expect one-click unsubscribe
  from bulk senders. Keep `enabled`.
- **DKIM** is signed by the mail server, not by Exponential. Sign `List-Unsubscribe` and `List-Unsubscribe-Post`.

## How to keep the old behaviour

| You want | Do |
|---|---|
| No blocking by preferences | `[GateSettings] Gate=disabled`. Optional mail goes to everybody except addresses on the suppression list, still with footer and headers; the status page says the gate is disabled |
| One mail for all recipients | `[GateSettings] SplitRecipients=disabled` |
| A new e-mail address at once, without confirmation | `[EmailChangeSettings] Confirm=disabled` (and `NotifyOldAddress=disabled` for no notice) |
| A mail of your code untouched | Do not give it a category. It is sent exactly as before and only counted; list its file or class in `EssentialSenders[]` if it is essential |
| An optional category on by default (opt-out) | `[CategorySettings] AllowDefaultOn=enabled`, only where the law allows it |
| Your own footer | Override `mailpreferences/mail/footer.tpl` (and `footer_html.tpl`) in your design, keeping the unsubscribe link, the manage link and the address |

Disabling the gate does not make a site compliant with the e-mail laws; read [the law checklist](../../specifications/6.0/mail-preferences-compliance.md)
first.

## For developers

- `eZMail::setCategory( $identifier )` and `eZMail::category()` are new; `setCategory()` also writes the header
  `X-Exp-Mail-Category`.
- A view that answers with its own body (a download, a plain answer, an event stream) empties the output buffers with
  `eZExecution::discardOutputBuffers()`, never with `while ( ob_get_level() > 0 ) ob_end_clean();`: under Velocity
  the worker keeps a buffer that cannot be removed, and that loop never ends (the request answers 504).
  `MailPreferencesPage::sendResponse()` does the whole answer for the views of the module. Velocity sends a response
  when the script ends, so an event stream arrives complete but at once there; under PHP-FPM it streams.
- A category of an extension is declared in its `settings/mailpreferences.ini.append.php`
  ([the developer's guide](../../guides/mail-preferences-developer.md)).
- Tests that send mail must force the file transport in their own process and use `.invalid` addresses; the gate's log
  can be sent to a file of the test with `expMailGate::setLogFileForTest()`.

## Related pages

- [The 6.0.15 changelog](../../changelogs/6.0/6.0.15.md)
- [The administrator's guide](../../guides/mail-preferences-administrator.md)
- [The developer's guide](../../guides/mail-preferences-developer.md)
- [The specification](../../specifications/6.0/mail-preferences.md), [the commands](../../specifications/6.0/mail-preferences-cli.md)
- [The law checklist](../../specifications/6.0/mail-preferences-compliance.md)
- [Upgrade notes: notifications](notification-ui-and-commands.md)
