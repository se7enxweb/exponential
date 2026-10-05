# E-mail preferences: the law checklist

This page maps each requirement of the e-mail laws to the code that meets it and to the test that proves it. It is for
the people who answer for the mail a site sends: the site owner, the data protection officer and the developers who review a
change to the mail system. The system itself is described in [the specification](mail-preferences.md).

**This page is not legal advice.** It records what the software does, checked against the common reading of each rule.
Whether a site complies also depends on what it sends, to whom, and on settings only the site owner can fill in, such as
the postal address. Ask a lawyer about your own case.

## How to read the tables

- **Code** names the class, view, template or setting that does the work. Paths are relative to the installation root.
- **Test** names the test that proves it:
  - `MP-nn`: `tests/tests/kernel/classes/mailpreferences/MailPreferencesCoreTest.php`
  - `LAW-nn`: `tests/tests/kernel/classes/mailpreferences/MailPreferencesComplianceTest.php`
  - `MPH-nn`: `tests/tests/kernel/classes/mailpreferences/MailPreferencesHardeningTest.php` (the gaps closed on 2026-10-05)
  - "Browser": checked with Playwright in Chromium and Firefox on the pages of the module (not part of the PHPUnit suite).
- **Site** marks a requirement that needs a setting or a decision of the site owner. The software reminds you on the
  status page (Setup > E-mail preferences) and in `./console exp:mail:status`.

Run the two test classes on an installation:

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/mailpreferences/
```

Expected on alpha, 2026-10-04: `OK (45 tests, 660 assertions)`. The tests force the file transport in their own process, use
only addresses on the reserved `.invalid` domain and remove what they create. Without an installation (the GitHub
workflow) they are skipped. LAW-24 posts to the site's own web server; it is skipped when the server cannot be reached. MPH-09 posts to the
site's web server and to Velocity (`velocity.ini [ServerSettings] HTTPSPort`) when it runs.

"Optional mail" below is mail of a category that is not essential: content notifications, collaboration, newsletters,
offers and news (marketing), application notices, and every category an administrator or an extension adds. Essential
mail (account security, orders and receipts, legal and service notices, administration alerts) is transactional: the
laws below exempt it from consent and unsubscribe, and it must not carry advertising.

## CAN-SPAM Act (United States)

| Requirement | Code | Test |
|---|---|---|
| A clear way to opt out in every commercial mail | `expMailGate::decorate()` adds the footer with a one-click unsubscribe link to every optional mail; `design:mailpreferences/mail/footer.tpl` (text), `footer_html.tpl` (HTML) | MP-11, LAW-01 |
| ... in every part of a multipart mail | `expMailGate::decoratePart()` | LAW-02 |
| ... that the reader can use: the link ends its line in a text mail | `footer.tpl` writes each line break explicitly | LAW-05 |
| The opt-out works for at least 30 days after the mail | `[TokenSettings] TTL[unsubscribe]=0`, `TTL[manage]=0` (never expire); a value below 60 days is raised to 60 days and warned about (`expMailToken::MIN_LINK_TTL`) | LAW-04, MPH-08 |
| The opt-out is honoured within 10 business days | The gate reads the preference at every send: the next mail is blocked at once | MP-14, LAW-03 |
| No fee, no information beyond the address, no step beyond one page | `mailpreferences/unsubscribe/<token>`: no login (`site.ini [RoleSettings] PolicyOmitList`), one button; the RFC 8058 POST needs nothing at all | LAW-07, LAW-24, Browser |
| Accurate "From", "Reply-To" and routing information | The gate never changes From, Reply-To, Subject or other headers; it only adds `X-Exp-Mail-Category`, `List-Unsubscribe` and `List-Unsubscribe-Post`. Optional mail with an empty From gets `[MailSettings] EmailSender` or `AdminEmail` (logged); without either it is not sent | LAW-06, MPH-06 |
| No deceptive subject line | The gate never changes the subject | LAW-06 |
| Identify the sender | `[FooterSettings] OrganisationName` in the footer of every optional mail; without it the name of the public site, never that of an administration siteaccess (`expMailSenderDetails::siteName()`) | MP-13, LAW-05, MPH-03 |
| A valid physical postal address | `[FooterSettings] OrganisationAddress` in the footer; the status page warns while it or the name is empty | MP-13, LAW-05. **Site**: fill both in |
| Identify the message as an advertisement | Not done by the software: the sender writes the content | **Site** |
| Monitor what others send on your behalf | The gate log `var/<vardir>/log/mailgate.jsonl` and the status page list every decision and every sender of uncategorised mail | MP-10, Browser |

## GDPR and the ePrivacy Directive (European Union)

| Requirement | Code | Test |
|---|---|---|
| Consent before marketing mail (ePrivacy art. 13) | Every optional category is off until the person turns it on: `[Category_*] DefaultOn=false`; `expMailPreferences::state()`. `DefaultOn=true` is refused and warned about unless `[CategorySettings] AllowDefaultOn=enabled` (`expMailCategoryRegistry::refusedDefaultOn()`) | MP-03, LAW-08, MPH-04 |
| Freely given: no box ticked for the person | `parts/signup.tpl` and `MailPreferencesPage::signupCategories()`: every box unticked, also on a form shown again after an error | LAW-09, Browser |
| Unambiguous: only an act of the person counts | `MailPreferencesPage::storeSignup()` stores nothing when nothing was ticked; an essential or unknown category posted by hand is ignored | LAW-10 |
| Specific: one consent per purpose | One switch per category; turning one on leaves the others off; the main switch changes no category | MP-05, LAW-12 |
| Informed: the person sees what they agree to | The page and the registration form show each category's name and description; mail says why it was sent ("You get this e-mail because you turned on ..."); the footer and the preference page link the privacy notice (`[FooterSettings] PrivacyURL`, else menu.ini `[SiteInfo] PrivacyPolicyID`) | LAW-14, LAW-15, MP-11, MPH-07 |
| Demonstrable: records of consent (art. 7(1)) | `expmail_consent_log`: time, category, old and new state, source, the exact text shown, IP address, the acting user, siteaccess; also the audit event `access.user.consent.change` | MP-04, LAW-15, LAW-18 |
| Confirmed opt-in where the address could belong to someone else | Double opt-in for newsletters, offers and news, and a new e-mail address: `DoubleOptIn=true`, `expmail_pending`, `expMailPreferencesService::confirm()`; no mail of the category while it waits | MP-06, MP-07, MP-19, LAW-11 |
| Opening a link does not count as consent | The confirm and unsubscribe pages change nothing on GET; only their button does | Browser, LAW-24 |
| Withdrawal as easy as giving consent (art. 7(3)) | The same box turns a category off; "Turn off all optional e-mail" is one button; every mail has a one-click link | LAW-13, LAW-14, MP-14 |
| Right of access (art. 15) and portability (art. 20) | "Download my e-mail data", JSON and CSV: `mailpreferences/export/<format>`, `expMailPreferences::export()`, `exportToCsv()`; the download is recorded | MP-15, LAW-16, Browser |
| Right to erasure (art. 17) | `expMailPreferences::erase()`, `./console exp:mail:preferences erase --yes`: preferences and pending confirmations removed, the consent log anonymised, the withdrawal kept as proof; at once when the account is removed (`eZUser::removeUser()` calls `expMailPreferences::eraseForRemovedAccount()`) | MP-15, LAW-17, MPH-05 |
| Storage limitation (art. 5(1)(e)) | Cronjob part `mailpreferences`: the log of people who are gone is removed after `[ConsentSettings] RetentionDays` (1095); expired confirmations are removed | MP-07, MP-17 |
| Data minimisation and security (art. 5, 32) | The suppression list stores a hash salted with the site secret, never the address; links are encrypted (AES-256-GCM), so an address in a link cannot be read; the gate log holds no address; command output hides addresses | MP-08, MP-09, LAW-19 |
| Objection to direct marketing is final (art. 21(3)) | "Stop all optional e-mail" by link puts the address on the suppression list (`unsubscribe_all`); a hard bounce or a complaint blocks even a requested link or a confirmation mail | MP-09, LAW-20 |
| Changes by staff are traceable | The administrator's user page records changes with source `admin` and the administrator's user id; the person's history says "Changed by an administrator" | LAW-18, Browser |

## CCPA and CPRA (California)

| Requirement | Code | Test |
|---|---|---|
| Right to know | The data download (JSON, CSV), the consent history on the preference page | MP-15, LAW-16 |
| Right to delete | `erase()` and the console command | MP-15, LAW-17 |
| No dark patterns: choices of equal weight, no pre-selection, no extra steps to say no | Equal switches, nothing ticked for a new person, "Turn off all optional e-mail" as visible as "Turn on" | LAW-09, LAW-14, Browser |
| No discrimination for using a right | Turning optional mail off never affects essential mail or the account | MP-05, MP-12 |
| Right to opt out of sale or sharing | Not applicable: the system shares no data with third parties | n/a |

## CASL (Canada)

| Requirement | Code | Test |
|---|---|---|
| Express consent before a commercial message | Opt-in by default, double opt-in for newsletters and marketing | MP-03, MP-06, LAW-08, LAW-11 |
| The request for consent says who asks and what for | Category name and description on the page and the form | LAW-14, LAW-15 |
| Proof of consent kept | The consent log | MP-04, LAW-15 |
| Identify the sender in every message | `OrganisationName` in the footer | LAW-05. **Site** |
| Contact information: a mailing address and a phone number, e-mail or web address, valid for 60 days | `OrganisationAddress` and the manage link (a web address) in the footer | LAW-05. **Site**: put a phone number or contact address into `OrganisationAddress` if you want one besides the web page |
| An unsubscribe mechanism in every message, valid for 60 days | Footer link and `List-Unsubscribe`; links never expire by default | LAW-01, LAW-04 |
| Unsubscribe honoured within 10 business days | At once | LAW-03 |
| Unsubscribe "readily performed" | One click (RFC 8058) or one button, no login | LAW-07, LAW-24 |

## RFC 8058 and RFC 2369 (one-click unsubscribe)

| Requirement | Code | Test |
|---|---|---|
| `List-Unsubscribe` with one HTTPS URI in angle brackets | `expMailGate::decorate()`; the URI is `<BaseURL>/mailpreferences/unsubscribe/<token>` | MP-11, LAW-21 |
| `List-Unsubscribe-Post: List-Unsubscribe=One-Click`, exactly | `expMailGate::decorate()` | MP-11, LAW-21 |
| The URI identifies the recipient and the list on its own | The token carries the recipient and the category; each recipient gets a mail of its own (`[GateSettings] SplitRecipients=enabled`), also To and Cc recipients | MP-11, LAW-22 |
| A POST with `List-Unsubscribe=One-Click` unsubscribes, without cookies or a form token | `Exponential\View\Kernel\Mailpreferences\Unsubscribe::oneClick()`: answers 200 `text/plain`, a broken link 400, through `MailPreferencesPage::sendResponse()` on every web server (PHP-FPM and Velocity) | LAW-24, MPH-01, MPH-09, Browser |
| A GET must not unsubscribe (mail scanners open links) | The GET page shows one button | LAW-24, Browser |
| The headers are covered by a DKIM signature | Not done by Exponential: the mail server signs | **Site**: sign `List-Unsubscribe` and `List-Unsubscribe-Post` with DKIM |

## Gaps found on 2026-10-04, and what became of them

The checklist found these on 2026-10-04. Six were closed on 2026-10-05 (each with a test); the others are decisions the
software leaves to the site on purpose.

### Closed on 2026-10-05

| Gap | Now | Test |
|---|---|---|
| 2. `DefaultOn=true` was accepted for an optional category | Refused: treated as off, a warning in the debug output and on the status page (`default_on_refused`). `[CategorySettings] AllowDefaultOn=enabled` keeps it where opt-out is lawful, and the status page notes that (`default_on_allowed`) | MPH-04 |
| 5. Removing an account did not erase its preferences | `eZUser::removeUser()` calls `expMailPreferences::eraseForRemovedAccount()`: preferences and pending confirmations removed, the consent log anonymised with an `erase` row, the withdrawals kept as proof, the suppression list kept | MPH-05 |
| 6. The From address was not checked | Optional mail with an empty From gets `[MailSettings] EmailSender`, else `AdminEmail`; the gate log records `from_fallback` with the sender file and the status page counts it. Without either the mail is not sent (`no_sender`) | MPH-06 |
| 7. No link to the privacy notice | `[FooterSettings] PrivacyURL` (a full address or a path of the site), else the node of menu.ini `[SiteInfo] PrivacyPolicyID` of the public siteaccess, else no link and a notice on the status page (`privacy_missing`). Linked from the text and HTML footer and the preference page | MPH-07 |
| 8. Link lifetimes could be shortened below the legal minimum | `TTL[unsubscribe]` and `TTL[manage]` between 0 and 60 days are raised to 60 days (`expMailToken::MIN_LINK_TTL`), with a warning on the status page (`ttl_below_minimum`); 0 still never expires | MPH-08 |
| 9. The footer named the siteaccess that sends ("Admin" from a command) | The name of the public site: the siteaccess of the request when it is public, else the DefaultAccess, else the first public one of `SiteList[]`; a siteaccess that requires a login is never used. `OrganisationName` still wins. Every mail template gets `$site_name` | MPH-03 |

Found with them: the one-click answer and every download of the module hung under Velocity (504, although the change was
saved), because the loop that emptied the output buffers never stopped at the worker's own buffer. All of them now end
through `MailPreferencesPage::sendResponse()` (MPH-01, MPH-09).

### Left to the site

1. **The postal address is empty after an installation.** `[FooterSettings] OrganisationAddress` ships empty; the
   installer asks for it, the status page and `exp:mail:status` warn, but optional mail is still sent without it
   (CAN-SPAM, CASL). Fill it in on the status page (Sender details).
2. **Mail without a category is sent without footer or unsubscribe link** (by design, for compatibility: code written
   before the gate keeps working). If commercial mail is sent by code that does not call `eZMail::setCategory()`, it does
   not comply. The status page counts such mail and names its sender. **Site**: give it a category.
3. **`SplitRecipients=disabled`** sends optional mail to several recipients at once: it has no `List-Unsubscribe`
   header and its footer links to "Send me a link" instead of a personal one-click link. Large mail providers expect
   one-click unsubscribe from bulk senders, and CAN-SPAM's "simple" opt-out becomes a step longer. Keep the default
   (`enabled`).
4. **DKIM** is left to the mail server. RFC 8058 asks for `List-Unsubscribe` and `List-Unsubscribe-Post` to be covered by
   the signature. **Site**: configure the MTA to sign both.
5. **The registration form** shows the categories but no link to the privacy notice; a design that wants one adds it to
   its `user/register.tpl`.

## Newsletter statistics (cjw_newsletter 4.2.0)

The newsletter extension counts opens (a pixel) and clicks (a redirect) of its editions. Counting a person's reading is
processing of personal data that needs consent (GDPR art. 6(1)(a), ePrivacy art. 5(3) for the pixel); anonymous totals
do not identify anybody. The extension's guide is `extension/cjw_newsletter/doc/statistics.md`.
`ST-nn`: `tests/tests/extension/cjw_newsletter/cjwNewsletterStatisticsTest.php`; `MPE-nn`:
`tests/tests/kernel/classes/mailpreferences/MailPreferencesErasedHookTest.php`.

| Requirement | Code | Test |
|---|---|---|
| No tracking unless the site and the list (or the send) choose it; off by default | `cjw_newsletter.ini [TrackingSettings] Tracking=disabled`; `cjwnl_list.tracking_mode` 0; `CjwNewsletterTracking::sendMode()` | ST-13, ST-05 |
| Per-person counting only with the person's own, prior, specific consent; not pre-ticked | category `newsletter_statistics` (`DefaultOn=false`, optional) on the preference page, handler `CjwNewsletterStatisticsCategoryHandler`; without consent the mail carries an anonymous key only | ST-02, ST-12 |
| The consent is recorded with what the person saw | the kernel consent log (`expConsentLog`), as for every category | ST-12 |
| Withdrawing is as easy as giving, and it takes effect at once | the same switch; `changed()` removes the person's rows; the consent is checked again on every open and click | ST-05, ST-06 |
| Erasure removes the person's statistics | the kernel calls the optional handler method `erased()` from `expMailPreferences::erase()` and the account removal | ST-06, MPE-01 to MPE-03 |
| Storage limitation | per-person rows removed after `[TrackingSettings] PersonRetentionMonths` (12), daily by the mail queue and by `ext:cjw_newsletter:statistics --cleanup`; totals kept | ST-07 |
| Data minimisation: no address or name in a URL, reports and exports show totals | keys `p<send item hash>` / `a<send>x<variant>`, HMAC signature; `CjwNewsletterStatisticsReport` | ST-02, ST-11 |
| No open redirect, no forged counts | the redirect goes only to the URL stored for the sent edition, with a valid signature of the same send; else 404 | ST-04 |
| Security of the endpoints | no session, no cookie, `Cache-Control: no-store`, `Referrer-Policy: no-referrer`; CSV cells defused | ST-03, ST-04, ST-11 |

**Site**: name the newsletter statistics, what is counted and the retention in the privacy notice, and keep the site
switch off where no lawful basis exists. Anonymous totals still load a pixel from the site for everybody (in mode 2 for the
people without consent too); where the local reading of ePrivacy art. 5(3) requires consent for that as well, switch
the pixel off with `[TrackingSettings] OpenPixel=disabled` or keep the tracking off.

## Related pages

- [E-mail preferences: the specification](mail-preferences.md)
- [E-mail preference commands](mail-preferences-cli.md)
- [E-mail preferences: the user's guide](../../features/6.0/mail-preferences.md)
- [E-mail preferences: the administrator's guide](../../guides/mail-preferences-administrator.md)
- [E-mail preferences: the developer's guide](../../guides/mail-preferences-developer.md)
- [Upgrade notes: e-mail preferences](../../bc/6.0/mail-preferences.md)
