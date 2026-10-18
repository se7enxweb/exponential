---
title: Privacy standards
audience: developer, admin
category: standards
order: 40
summary: What Exponential provides for the GDPR, the ePrivacy Directive and CAN-SPAM: consent records, one-click unsubscribe, erasure, log minimization and retention, cookies and third-party services, and what stays with the site operator.
keywords: privacy, data protection, GDPR, DSGVO, Regulation 2016/679, ePrivacy, cookie, cookie banner, consent, CAN-SPAM, CASL, CCPA, unsubscribe, List-Unsubscribe, RFC 8058, right to erasure, right of access, data export, retention, pseudonymization, IP masking, analytics, reCAPTCHA, hCaptcha, third-party processor
type: reference
---
# Privacy standards

This page is for administrators who answer for the personal data a site processes, and for developers who extend
it. It lists the privacy laws that matter most for a content management system, says what Exponential 6.0.x
provides for each one, and where the work is left to the site.

> **Warning** Exponential is software, not a data controller. Compliance with the GDPR, the ePrivacy rules or
> CAN-SPAM is the responsibility of the organization that runs the site: its purposes, its legal bases, its privacy
> notice, its processors and its configuration. This page describes features; it is not legal advice.

## Summary

| Standard | Version | Status | Evidence (short) |
|---|---|---|---|
| General Data Protection Regulation (GDPR) | Regulation (EU) 2016/679 | **Partly complies** (features provided) | consent log, opt-in mail categories, erasure on account removal, audit pseudonymization, IP masking in logs, no third-party assets in the admin; no general "export all my data" |
| ePrivacy Directive (cookies, marketing mail) | Directive 2002/58/EC, as amended by 2009/136/EC | **Partly complies** | only functional cookies from the kernel; marketing mail opt-in; **no cookie consent banner** |
| CAN-SPAM Act (United States) | 15 U.S.C. 7701 ff. (2003) | **Complies** (for mail sent through the mail gate) | unsubscribe link and `List-Unsubscribe` in optional mail, honored at once; sender postal address left to the site |
| One-click unsubscribe | RFC 8058 with RFC 2369 | **Complies** | `List-Unsubscribe` and `List-Unsubscribe-Post` added by `expMailGate::decorate()`; DKIM signing left to the mail server |

## GDPR

### What it is

The General Data Protection Regulation, Regulation (EU) 2016/679 of the European Parliament and of the Council,
applicable since 25 May 2018. It governs the processing of personal data of people in the EU: lawful basis,
consent, data subject rights, data protection by design and by default (art. 25), and security (art. 32).

### What Exponential provides

| GDPR topic | What ships | Where |
|---|---|---|
| Consent (art. 4(11), 7) | E-mail categories that are off until the person turns them on (`[Category_*] DefaultOn=false`; `DefaultOn=true` is refused unless `[CategorySettings] AllowDefaultOn=enabled`); one switch per purpose; double opt-in for newsletters and marketing | [E-mail preferences](../features/6.0/mail-preferences.md), [the law checklist](../specifications/6.0/mail-preferences-compliance.md) |
| Records of consent (art. 7(1)) | Table `expmail_consent_log`: time, category, old and new state, source, the exact text shown, acting user, siteaccess; audit event `access.user.consent.change` | `kernel/classes/mailpreferences/expmailpreferences.php` |
| Withdrawal as easy as consent (art. 7(3)) | The same switch, a "Turn off all optional e-mail" button, a one-click link in every optional mail, no login needed | [E-mail preferences](../features/6.0/mail-preferences.md) |
| Right of access (art. 15) and portability (art. 20) | Mail preferences and consent history downloadable as JSON or CSV by the person (`mailpreferences/export`) or by an administrator (`./console exp:mail:preferences export`); audit records by or about a user with `./console exp:audit export --subject-user=<id>` | [Mail preferences for administrators](../guides/mail-preferences-administrator.md), [Audit guide 4.12](../bc/6.0/audit.md#412-privacy-and-gdpr-tasks) |
| Right to erasure (art. 17) | Removing a user account calls `expMailPreferences::eraseForRemovedAccount()` from `eZUser::removeUser()`: preferences and pending confirmations removed, consent log anonymized; `./console exp:mail:preferences erase --yes` on request. Audit records are kept for their retention period under art. 17(3)(b) and (e) and are pseudonymized | [the law checklist](../specifications/6.0/mail-preferences-compliance.md), [Audit guide 4.12](../bc/6.0/audit.md#412-privacy-and-gdpr-tasks) |
| Restriction (art. 18) | **Deactivate and move** (opt-in, `[UserSettings] DeactivateAndMove=enabled`) disables accounts and moves them into a hidden group without roles, reversibly; **Users > Check user accounts** (`user/hygiene`) finds accounts unused for N days | [Check user accounts](../features/6.0/user-hygiene.md) |
| Data minimization in logs (art. 5(1)(c), 25) | Audit: addresses truncated to /24 (IPv6 /48), session ids hashed, user agents shortened, no passwords, tokens or POST bodies, per-field `full / truncate / hash / off` in `[AuditPrivacySettings] Field[]`, pseudonymization after `PseudonymiseAfterDays`. Request logs: `MaskIP=enabled`, every query value replaced by `***`. Log viewer: `settings/logviewer.ini [RedactionSettings] MaskIPs=enabled`, e-mail addresses and secrets masked | [Audit trail](../features/6.0/audit-trail.md), [Request logs](../features/6.0/request-logs.md), [Log viewer](../features/6.0/log-viewer.md#privacy-what-is-masked) |
| Storage limitation (art. 5(1)(e)) | Audit: 90 days live and 2 years archived per channel, removed automatically. `settings/logviewer.ini [RotationSettings] MaxAgeDays[404]=30`, `MaxAgeDays[security]=90`, `MaxAgeDays[csp]=30` | [Audit guide 4.3](../bc/6.0/audit.md#43-retention), [Log rotation](../features/6.0/log-rotation.md) |
| Security of processing (art. 32) | See [Security standards](security.md): password hashing, CSRF tokens, session cookie flags, audit trail | [Security standards](security.md) |
| No data sent to third parties by the administration | jQuery and the other admin scripts are served locally (`extension/ezjscore/settings/ezjscore.ini [eZJSCore] LoadFromCDN=disabled`); the admin4 design loads no web fonts or scripts from other hosts | `design/admin4/templates/pagelayout.tpl` |
| Error reports without personal data | The help's error report builds its installation summary from a fixed list of facts and removes e-mail addresses, IP addresses, credentials and paths outside the install root (`scrub()`); no user or session data | `kernel/classes/help/exphelpissuereport.php`, [Help system](../features/6.0/help-system.md) |

### Status

**Partly complies**, in the sense that the software provides the features listed above. The site operator still
has to decide purposes and legal bases, keep the record of processing (art. 30), write the privacy notice, sign
processing agreements with any third party it adds, and answer requests.

### Known gaps

- **No single "export all my data" or "delete me" function.** A user account is a content object; its fields, its
  content, its information collections ([Collected information](../guides/collected-information.md)), its orders and
  its sessions are reached through the separate admin tools. Only mail preferences and audit records have a
  dedicated export.
- **User removal is manual and one account at a time**: a user object never goes to the trash, and **Check user
  accounts** does not remove accounts ([Check user accounts](../features/6.0/user-hygiene.md)).
- **Logs without an age limit by default**: apart from 404, security and CSP logs, kernel logs (`error.log`,
  `debug.log`, and others) are rotated by size; `MaxAgeDays` is not set for them. Web server access logs are outside
  Exponential.
- **Information collection forms** store what visitors type; the site must add a consent text and a retention
  rule itself.
- **Search phrases** are stored and can contain personal data ([URLs and aliases](../guides/urls-and-aliases.md)).

## ePrivacy Directive (cookies)

### What it is

Directive 2002/58/EC on privacy and electronic communications, amended by Directive 2009/136/EC. Its art. 5(3)
requires consent before storing or reading information on a user's device, unless it is strictly necessary for a
service the user asked for; its art. 13 requires prior consent for marketing e-mail.

### What Exponential provides

Cookies set by the kernel and the shipped administration designs:

| Cookie | Set by | Purpose | When |
|---|---|---|---|
| Session cookie (`[Session] SessionNamePrefix=eZSESSID`, plus a suffix) | `lib/ezsession/classes/ezsession.php` | sign-in and form state; `HttpOnly`, `SameSite=Lax`, `Secure` over HTTPS | when a session starts |
| `is_logged_in` | `kernel/private/classes/ezpkernelweb.php` | lets HTTP caches skip signed-in users | while signed in |
| `exp_setup_wizard` | `kernel/classes/expmaintenance.php` | the setup wizard's own run | during installation only |
| `eZMobileDeviceDetect` | `kernel/private/classes/ezpmobiledeviceregexpfilter.php` | remembers mobile detection | only with `[SiteAccessSettings] DetectMobileDevice=enabled` (default `disabled`) |
| `adminNavigationTab` and other UI state cookies | `design/admin4/javascript/node_tabs.js`, `contentstructuremenu_dynamic.js`, `ezajaxsubitems_expdatatable.js` | open tab, tree state, table columns in the administration | administration only |

The admin4 design stores its display choices (color mode, side columns, menu groups) as user preferences for a
signed-in user, and in the browser's `localStorage` on pages without a signed-in user
(`design/admin4/javascript/a4_head.js`). All of these serve a function the user asked for; none is used for
tracking. Marketing e-mail is opt-in (see GDPR above).

### Status

**Partly complies.** The kernel sets only functional cookies. There is **no cookie consent banner or consent
manager** in the kernel or the shipped designs (none was found in `kernel/`, `design/` or `settings/`), so a site
that adds analytics, embeds or advertising must add one.

### Third-party services in shipped extensions

These extensions are installed with Exponential by `composer.json` (`require`) but are **not active** until a site
activates them (`settings/site.ini [ExtensionSettings] ActiveExtensions[]` ships with `xrowmetadata` only; the setup
wizard adds `ezjscore`, `ezoe` and `ezformtoken`). When activated they send visitor data to a third party, which then
acts as processor or controller:

| Extension | Third party | Consent / notice needed |
|---|---|---|
| `bcwebsitestatistics` | Google Analytics / Google Tag Manager (`bcwebsitestatistics.ini`: `urchin.js`, `googletagmanager.com/gtag/js`) | yes; the extension has no consent gate of its own |
| `recaptcha` | Google reCAPTCHA | yes, in most readings of art. 5(3); mention in the privacy notice |
| `hcaptcha` | hCaptcha | as above |
| `sevenx_authentication_2fa` (social login handlers) | the chosen identity provider (Google, Facebook and others) | the user chooses it; mention in the privacy notice |
| `cjw_newsletter` statistics | none (on-site), but per-person open and click counting | only with the `newsletter_statistics` category's prior consent |

Whether a site package activates any of these is **Unverified** here; check `ActiveExtensions[]` in
`settings/override/site.ini.append.php`.

## CAN-SPAM Act and one-click unsubscribe

### What it is

The Controlling the Assault of Non-Solicited Pornography And Marketing Act of 2003 (United States, 15 U.S.C. 7701
and following) requires a working opt-out in every commercial e-mail, honored within 10 business days, accurate
header information and the sender's postal address. RFC 8058 (2017) defines the one-click `List-Unsubscribe-Post`
header that large mailbox providers now require from bulk senders, building on RFC 2369's `List-Unsubscribe`.

### What Exponential provides

- Every optional e-mail passes the mail gate, which adds `List-Unsubscribe` (an HTTPS link to
  `mailpreferences/unsubscribe/<token>`), `List-Unsubscribe-Post: List-Unsubscribe=One-Click` and a footer with the
  reason and the unsubscribe link (`expMailGate::decorate()`).
- The unsubscribe needs no login, no form token and no cookie; a POST with `List-Unsubscribe=One-Click` takes
  effect at once. Links do not expire by default.
- The gate never rewrites `From`, `Reply-To` or `Subject`.
- Essential mail (password reset, activation, orders) is separated from optional mail and carries no advertising.
- The same features are mapped to CASL (Canada) and CCPA/CPRA (California) in
  [the law checklist](../specifications/6.0/mail-preferences-compliance.md).

### Status

**Complies** for mail that goes through the mail gate, with these items left to the site:

- the sender's postal address in the footer (Sender details on the status page);
- giving every commercial mail a category: mail sent without `eZMail::setCategory()` gets no footer and no
  unsubscribe link (kept that way for compatibility; the status page counts such mail and names its sender);
- DKIM signing of the two headers (done by the mail server);
- keeping the default per-recipient sending: `SplitRecipients=disabled` sends to several recipients at once
  without `List-Unsubscribe`.

## Related pages

- [Security standards](security.md), [Accessibility standards](accessibility.md), [Web platform standards](web-platform.md)
- [E-mail preferences](../features/6.0/mail-preferences.md) and [the law checklist](../specifications/6.0/mail-preferences-compliance.md)
- [Audit trail](../features/6.0/audit-trail.md)
- [Security hardening, 13.13 Mail: consent and suppression](../install/13-security-hardening.md#1313-mail-consent-and-suppression)
