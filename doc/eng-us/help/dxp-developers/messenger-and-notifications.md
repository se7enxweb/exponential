---
title: Messenger and notifications
audience: dxp-developer
category: dxp-development
order: 34
summary: Background work in the DXP - Symfony Messenger with the ibexa/messenger transport (database table by default, sync on SQLite), consuming messages, failed messages, and ibexa/notifications for user notifications such as password reset and invitation mails.
keywords: messenger, ibexa/messenger, MESSENGER_TRANSPORT_DSN, transport_dsn, ibexa_messenger_messages, messenger:consume, sync, notifications, ibexa/notifications, notifier, mailer, password reset
type: help
---
# Messenger and notifications

This page is for developers who run background work and user notifications on the DXP. Two packages of the
metapackage are involved: `ibexa/messenger` (queues on top of Symfony Messenger) and `ibexa/notifications`
(notifications through Symfony's notifier and mailer).

## Contents

1. [Messenger in the DXP](#messenger-in-the-dxp)
2. [Choosing the transport](#choosing-the-transport)
3. [Consuming messages](#consuming-messages)
4. [Notifications](#notifications)
5. [Mail in development](#mail-in-development)

## Messenger in the DXP

`ibexa/messenger` registers its own bus and a transport named `ibexa.messenger.transport`. Its configuration key is
`ibexa_messenger.transport_dsn`; the default, read from the package, is a Doctrine transport on the current
repository connection with the table `ibexa_messenger_messages`. It also adds middleware that deduplicates messages
with a lock store (table `ibexa_messenger_lock_keys`).

Your project's own `config/packages/messenger.yaml` (from the recipe) defines a `sync://` transport and commented
lines for an `async` transport on `MESSENGER_TRANSPORT_DSN` and a `failed` transport.

## Choosing the transport

| Situation | Transport |
|---|---|
| SQLite | `MESSENGER_TRANSPORT_DSN=sync://` (the guide: the Doctrine transport "requires a second DB connection that may not be available on SQLite") |
| MySQL, MariaDB, PostgreSQL | The Doctrine transport (default), consumed by a worker |
| High volume | A dedicated broker supported by Symfony Messenger |

With `sync://`, messages are handled during the request that sends them: simple, but the request waits.

## Consuming messages

```bash
php bin/console messenger:consume ibexa.messenger.transport --limit=100   # handle and exit
php bin/console messenger:stop-workers                                   # ask running workers to stop after a deploy
```

Run consumers under a process manager (for example a systemd service) that restarts them, and send
`messenger:stop-workers` after every deploy so they load the new code. To keep failed messages for later, uncomment
the `failed` transport and `failure_transport` in `messenger.yaml`.

## Notifications

`ibexa/notifications` connects content events to Symfony's notifier. An installed project subscribes two user
notifications to email in `config/packages/ibexa.yaml`:

```yaml
ibexa:
    system:
        default:
            notifier:
                subscriptions:
                    Ibexa\Contracts\User\Notification\UserPasswordReset:
                        channels: [email]
                    Ibexa\Contracts\User\Notification\UserInvitation:
                        channels: [email]
```

These mails need a working `MAILER_DSN`.

## Mail in development

Keep `MAILER_DSN=null://null` in development so no mail leaves the machine, and give test users addresses at
`example.invalid`. Test real delivery on a staging server with a mailbox you own.

## Related pages

- [Cron](cron.md)
- [SQLite for development](sqlite-for-development.md)
- [Environment variables](environment-variables.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
