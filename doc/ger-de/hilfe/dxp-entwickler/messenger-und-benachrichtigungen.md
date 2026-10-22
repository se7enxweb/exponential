---
title: Messenger und Benachrichtigungen
audience: dxp-developer
category: dxp-development
order: 34
summary: Hintergrundarbeit in der DXP - Symfony Messenger mit dem Transport von ibexa/messenger (standardmäßig eine Datenbanktabelle, sync auf SQLite), das Abarbeiten von Nachrichten, fehlgeschlagene Nachrichten und ibexa/notifications für Benutzerbenachrichtigungen wie E-Mails zum Zurücksetzen des Passworts und Einladungen.
keywords: Messenger, ibexa/messenger, MESSENGER_TRANSPORT_DSN, transport_dsn, ibexa_messenger_messages, messenger:consume, sync, Benachrichtigungen, ibexa/notifications, Notifier, Mailer, Passwort zurücksetzen, notifications, password reset
type: help
---
# Messenger und Benachrichtigungen

Diese Seite richtet sich an Entwickler, die auf der DXP Hintergrundarbeit und Benutzerbenachrichtigungen betreiben. Zwei
Pakete des Metapakets sind beteiligt: `ibexa/messenger` (Warteschlangen auf Basis von Symfony Messenger) und
`ibexa/notifications` (Benachrichtigungen über Notifier und Mailer von Symfony).

## Inhalt

1. [Messenger in der DXP](#messenger-in-der-dxp)
2. [Den Transport wählen](#den-transport-wählen)
3. [Nachrichten abarbeiten](#nachrichten-abarbeiten)
4. [Benachrichtigungen](#benachrichtigungen)
5. [E-Mail in der Entwicklung](#e-mail-in-der-entwicklung)

## Messenger in der DXP

`ibexa/messenger` registriert einen eigenen Bus und einen Transport namens `ibexa.messenger.transport`. Sein
Konfigurationsschlüssel ist `ibexa_messenger.transport_dsn`; der Standard, aus dem Paket gelesen, ist ein
Doctrine-Transport auf der aktuellen Repository-Verbindung mit der Tabelle `ibexa_messenger_messages`. Außerdem fügt
es eine Middleware hinzu, die Nachrichten mit einem Lock-Store dedupliziert (Tabelle `ibexa_messenger_lock_keys`).

Die eigene `config/packages/messenger.yaml` Ihres Projekts (aus dem Rezept) definiert einen Transport `sync://` und
auskommentierte Zeilen für einen Transport `async` auf `MESSENGER_TRANSPORT_DSN` und einen Transport `failed`.

## Den Transport wählen

| Situation | Transport |
|---|---|
| SQLite | `MESSENGER_TRANSPORT_DSN=sync://` (die Anleitung: der Doctrine-Transport "requires a second DB connection that may not be available on SQLite") |
| MySQL, MariaDB, PostgreSQL | der Doctrine-Transport (Standard), von einem Worker abgearbeitet |
| Hohes Volumen | ein eigener Broker, den Symfony Messenger unterstützt |

Mit `sync://` werden Nachrichten während der Anfrage verarbeitet, die sie sendet: einfach, aber die Anfrage wartet.

## Nachrichten abarbeiten

```bash
php bin/console messenger:consume ibexa.messenger.transport --limit=100   # handle and exit
php bin/console messenger:stop-workers                                   # ask running workers to stop after a deploy
```

Betreiben Sie Consumer unter einem Prozessmanager (zum Beispiel einem systemd-Dienst), der sie neu startet, und senden
Sie nach jeder Bereitstellung `messenger:stop-workers`, damit sie den neuen Code laden. Um fehlgeschlagene Nachrichten
für später aufzubewahren, kommentieren Sie den Transport `failed` und `failure_transport` in `messenger.yaml` ein.

## Benachrichtigungen

`ibexa/notifications` verbindet Inhaltsereignisse mit dem Notifier von Symfony. Ein installiertes Projekt abonniert in
`config/packages/ibexa.yaml` zwei Benutzerbenachrichtigungen per E-Mail:

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

Diese E-Mails brauchen ein funktionierendes `MAILER_DSN`.

## E-Mail in der Entwicklung

Behalten Sie in der Entwicklung `MAILER_DSN=null://null` bei, damit keine E-Mail den Rechner verlässt, und geben Sie
Testbenutzern Adressen unter `example.invalid`. Testen Sie die echte Zustellung auf einem Staging-Server mit einem
Postfach, das Ihnen gehört.

## Verwandte Seiten

- [Cron](cron.md)
- [SQLite für die Entwicklung](sqlite-fuer-die-entwicklung.md)
- [Umgebungsvariablen](umgebungsvariablen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
