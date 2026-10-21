---
title: Benachrichtigungen und Mail für Anwendungen
audience: app-creator, developer
category: app-creation
order: 380
summary: Mail aus einer Anwendung richtig versenden - eZMail mit Absender und Kategorie, das Mail-Gate und die Mail-Einstellungen, Transporte und der Datei-Transport zum Testen, Benachrichtigungs-Ereignistypen und Handler sowie Templates für Mails.
keywords: Mail, E-Mail, Mail-Gate, Mail-Einstellungen, Abmeldung, Benachrichtigungen, Ereignistyp, Handler, Datei-Transport, Testen, Anwendungsentwickler, mail, eZMail, eZMailTransport, setCategory, mail gate, mail preferences, unsubscribe, notifications, event type, handler, file transport, testing, app creator
type: help
---
# Benachrichtigungen und Mail für Anwendungen

Diese Seite richtet sich an Anwendungsentwickler, deren Anwendung Mails versendet: Bestätigungen, Erinnerungen,
Berichte. Exponential leitet jede Mail durch ein **Mail-Gate**, das die Einstellungen der Empfänger anwendet, und hat ein
Benachrichtigungssystem für Mails, die durch Inhaltsereignisse ausgelöst werden
([Mail-Einstellungen für Entwickler](../../../anleitungen/mail-einstellungen-entwickler.md),
[Benachrichtigungen für Entwickler](../../../anleitungen/benachrichtigungen-entwickler.md)).

## Inhalt

1. [Eine Mail versenden](#eine-mail-versenden)
2. [Kategorien und das Gate](#kategorien-und-das-gate)
3. [Transporte und Testen](#transporte-und-testen)
4. [Benachrichtigungen bei Inhaltsereignissen](#benachrichtigungen-bei-inhaltsereignissen)
5. [Regeln](#regeln)

## Eine Mail versenden

```php
$ini = eZINI::instance();
$mail = new eZMail();
$mail->setSender( $ini->variable( 'MailSettings', 'EmailSender' ) ?: $ini->variable( 'MailSettings', 'AdminEmail' ) );
$mail->addReceiver( $address );
$mail->setSubject( $subject );
$mail->setBody( $text );
$mail->setCategory( 'events' );
eZMailTransport::send( $mail );
```

Bauen Sie den Text aus einem Template (`$tpl->fetch( 'design:expdemo/mail/reminder.tpl' )`), damit Websites die
Formulierung ändern können.

## Kategorien und das Gate

- Jede Mail hat eine **Kategorie** (`setCategory()`); eine Kategorie ist **wesentlich** (wird immer versendet:
  Sicherheit, Bestellungen, Rechtliches) oder **optional** (nur an Empfänger, die sie eingeschaltet haben).
- `eZMailTransport::send()` übergibt jede Mail an `expMailGate::dispatch()`, das je Empfänger entscheidet, die Fußzeile
  und die Abmelde-Header hinzufügt und die blockierten Mails protokolliert.
- Eine Mail ohne Kategorie wird unverändert versendet und als nicht kategorisiert protokolliert; geben Sie jeder Mail
  eine Kategorie.
- Eine eigene Kategorie wird in den Einstellungen deklariert ([3. Eine eigene Kategorie](../../../anleitungen/mail-einstellungen-entwickler.md#3-eine-eigene-kategorie)).

## Transporte und Testen

`site.ini [MailSettings] Transport` ist `sendmail`, `smtp` oder `file`; der Datei-Transport schreibt jede Mail in eine
Datei, und so testen Sie, ohne zu versenden ([10.4 Mail](../../../installation/10-nach-der-installation.md#104-mail-der-datei-transport-und-echter-versand),
[8. Testen ohne Mailversand](../../../anleitungen/mail-einstellungen-entwickler.md#8-testen-ohne-mails-zu-senden)).
Ein Override in `settings/override/` hat Vorrang vor einer Siteaccess-Einstellung: Prüfen Sie vor einem Test mit
`./console exp:ini where site.ini/MailSettings/Transport <siteaccess>`, welcher Transport wirklich gilt.

## Benachrichtigungen bei Inhaltsereignissen

Für „Leute informieren, wenn etwas veröffentlicht wird“ schreiben Sie einen Benachrichtigungs-Ereignistyp und einen
Handler, statt in einem Workflow Mails zu versenden: Das Benachrichtigungssystem sammelt Ereignisse, lässt Benutzer sich
anmelden und versendet im Cronjob-Teil `frequent`
([2. Ein Ereignistyp und ein Handler](../../../anleitungen/benachrichtigungen-entwickler.md#2-ein-ereignistyp-und-ein-handler-ein-vollständiges-beispiel)).

## Regeln

- Setzen Sie immer einen Absender; niemals die Adresse eines Besuchers als From.
- Testadressen enden auf reservierte Domains (`example.com`, `.invalid`); schicken Sie bei einem Test niemals Mails an
  echte Menschen.
- Teilen Sie Empfängerlisten nicht selbst auf; das erledigt das Gate, mit dem eigenen Abmeldelink jedes Empfängers.
- Langsame Mails oder Massenmails laufen über eine Warteschlange oder einen Cronjob, nicht über die Web-Anfrage.

## Verwandte Seiten

- [Mail-Einstellungen](../../../../eng-us/features/6.0/mail-preferences.md)
- [Benachrichtigungen](../../../../eng-us/features/6.0/notifications.md)
- [Warteschlangen mit expqueue](warteschlangen-mit-expqueue.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
