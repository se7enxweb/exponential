---
title: Mail problems
audience: government, admin, hosting
category: public-sector
order: 120
summary: Runbook for mail that does not arrive (password resets, form confirmations, notifications, newsletters) or that goes where it should not (a test copy mailing citizens) - transport settings, cron, preferences, sender domain checks and the file transport.
keywords: mail, e-mail not arriving, password reset mail, notifications, newsletter, SMTP, sendmail, file transport, MailSettings, SPF, DKIM, DMARC, cron, test copy, incident
type: help
---
# Mail problems

This runbook is for administrators when citizens or staff do not receive mail from a public website, or when a test
copy sent mail to real people. Settings and transports are described in
[Mail delivery (hosting)](../../../hosting/operations/mail-delivery.md) and
[chapter 10.4](../../../../install/10-after-installing.md#104-mail-the-file-transport-and-real-sending).

## Contents
1. [Which mail is missing?](#which-mail-is-missing)
2. [Check the transport](#check-the-transport)
3. [Check what holds mail back](#check-what-holds-mail-back)
4. [Mail arrives as spam or is rejected](#mail-arrives-as-spam-or-is-rejected)
5. [A test copy mailed real people](#a-test-copy-mailed-real-people)
6. [Related pages](#related-pages)

## Which mail is missing?

| Mail | Sent by |
|---|---|
| Password reset, password changed, registration | the request itself |
| Form confirmations, form receiver mail | the request itself |
| Notifications | the cronjob part `frequent` (`notification.php`) |
| Newsletters | the newsletter extension's cronjobs |
| Audit alerts | the `audit` cronjob part |

If only cron-sent mail is missing, start with [Cronjob monitoring](../monitoring/cronjob-monitoring.md).

## Check the transport

```bash
./console exp:ini get 'site.ini/MailSettings/Transport' --allow-root-user
./console exp:ini where site.ini/MailSettings/Transport --allow-root-user
```

| Transport | Means |
|---|---|
| `sendmail` (shipped) | handed to the local `sendmail` program; look at the server's mail log |
| `SMTP` | sent to `TransportServer`; check the server, port, `TransportConnectionType` and login |
| `file` | written to `var/log/mail/`: **nothing is sent**; **Setup > System information** shows this as a note |

`settings/override` outranks siteaccess settings, so a `Transport` set there wins everywhere. After a change, clear the
INI cache and reload.

## Check what holds mail back

- Every mail passes the mail gate, which checks optional mail against the recipient's e-mail preferences
  ([E-mail preferences](../../../../guides/mail-preferences-administrator.md)); essential mail such as "Your password
  was changed" cannot be switched off.
- The recipient's address on the account may be wrong; **Users > Check user accounts** shows addresses to users with
  the right to see them.
- Newsletters: bounces and the blacklist ([Mail consent and bounces](../../../hosting/operations/mail-consent-and-bounces.md)).

## Mail arrives as spam or is rejected

The sending server must be allowed to send for the sender's domain: SPF, DKIM and DMARC records of your domain, a
sender address (`EmailSender`, `AdminEmail`) in that domain, `SenderHost` set to the site's host name. Government
domains often have strict DMARC policies; involve your mail team. A server that sends over IPv6 needs its IPv6 address
in SPF too.

## A test copy mailed real people

1. Stop it: set `Transport=file` for that installation, in the environment's variant file
   (`settings/override/site.ini.test.append.php` with `EXP_ENV=test`), clear the INI cache.
2. Find what was sent: the mail server's log; the audit trail for the actions that triggered mail.
3. Tell your data protection officer if personal data reached the wrong people
   ([Data breach notification framing](data-breach-notification-framing.md)).
4. Prevent it: every restored or staging copy starts with the file transport
   ([Environments: development, test and production](../hosting/environments-dev-test-prod.md)).

## Related pages
- [Restoring to a test server](../backups-and-recovery/restoring-to-a-test-server.md)
- [Set up environment-specific settings](../../tasks/operations/set-up-environment-specific-settings.md)
- [Mail preferences (feature)](../../../../features/6.0/mail-preferences.md)
- [Mail delivery (hosting)](../../../hosting/operations/mail-delivery.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
