---
title: Benachrichtigungen und Abonnements
audience: government, admin, website-creator
category: public-sector
order: 50
summary: Wie Menschen mit einem Konto Nachrichten, Bekanntmachungen oder beliebigen Ordnern auf einer Exponential-Site folgen und eine E-Mail erhalten, wenn etwas veröffentlicht wird - Benachrichtigen, Meine Benachrichtigungseinstellungen, Zusammenfassungen, der Benachrichtigungs-Cronjob, Veröffentlichen ohne Benachrichtigung und die Grenzen bei dringender Verwendung.
keywords: Benachrichtigungen, Abonnements, Benachrichtigen, Meine Benachrichtigungseinstellungen, Zusammenfassung, Benachrichtigungs-Cronjob, Ohne Benachrichtigung veröffentlichen, E-Mail, Kontoinhaber, notifications, subscriptions, Notify me, My notification settings, digest, notification cronjob, notification/use, Publish without notification, e-mail, account holders
type: help
---
# Benachrichtigungen und Abonnements

Diese Seite richtet sich an Administratoren und Site-Ersteller, die Menschen Teilen einer Site des öffentlichen Sektors
per E-Mail folgen lassen. Das Benachrichtigungssystem von Exponential schreibt Kontoinhabern, wenn unter einem Element,
dem sie folgen, Inhalte veröffentlicht werden. Quellen:
[Benachrichtigungen: die Anleitung für Benutzer](../../../../funktionen/6.0/benachrichtigungen.md) und
[Benachrichtigungen für Administratoren](../../../../anleitungen/benachrichtigungen-administrator.md).

## Inhalt

1. [Wie es funktioniert](#wie-es-funktioniert)
2. [Was Menschen tun](#was-menschen-tun)
3. [Was Administratoren einrichten](#was-administratoren-einrichten)
4. [Redakteure und Korrekturen](#redakteure-und-korrekturen)
5. [Grenzen](#grenzen)

## Wie es funktioniert

| Schritt | Wer |
|---|---|
| Das Veröffentlichen schreibt ein Ereignis | der Kernel, sofort |
| Der Benachrichtigungslauf gibt Ereignisse an Handler, die je Folgendem eine Nachricht schreiben, sofort gesendet oder für eine Zusammenfassung aufbewahrt | der Benachrichtigungs-Cronjob, **Jetzt ausführen** auf der Statusseite oder `exp:notification:run` |
| Fällige Zusammenfassungen werden gesendet | derselbe Lauf |

Ohne Lauf wird nichts gesendet. Der Benachrichtigungsteil gehört zur Cronjob-Gruppe `frequent` und kann allein laufen.

## Was Menschen tun

| Aktion | Wo |
|---|---|
| Einem Element folgen (einem Ordner wie News oder Notices) | **Benachrichtigen** im Menü des Elements, oder **Einträge hinzufügen** unter **Meine Benachrichtigungseinstellungen** (`/notification/settings`) |
| Sofort oder als Zusammenfassung wählen (täglich, wöchentlich, monatlich) | **Meine Benachrichtigungseinstellungen**, **Alle Nachrichten gesammelt in einem Digest erhalten** |
| Nicht mehr folgen | markieren und **Ausgewählte entfernen**, oder **Benachrichtigungen für diesen Eintrag beenden** auf der Seite **Benachrichtigen** des Elements |

Menschen brauchen ein Konto mit der Richtlinie `notification/use`. Mails gehen an die Adresse des Kontos. Die Seiten
funktionieren in den Designs admin4 und admin4l; in einem öffentlichen Design bieten Sie den Link **Benachrichtigen**
in Ihren Templates an.

## Was Administratoren einrichten

1. Geben Sie der Rolle öffentlicher Kontoinhaber `notification/use`.
2. Stellen Sie sicher, dass der Cronjob-Teil frequent (oder der Benachrichtigungsteil) läuft: **Setup > Cronjobs**.
3. Prüfen Sie **Setup > Benachrichtigungen** (`/notification/status`): wartende Ereignisse, letzter Lauf, und
   **Jetzt ausführen** zum Ausprobieren.
4. Setzen Sie den Mail-Transport; testen Sie zuerst mit dem Datei-Transport
   ([Mail an Empfänger](../formulare/mail-an-empfaenger.md#testen-ohne-versand)).
5. Beschränken Sie optional, welche Klassen Benachrichtigungsereignisse erzeugen, damit Bilder und Banner Folgenden
   keine Mails senden
   ([Ohne Benachrichtigung veröffentlichen](../../../../funktionen/6.0/veroeffentlichen-ohne-benachrichtigung.md#benachrichtigungen-nur-für-einige-klassen)).

## Redakteure und Korrekturen

Wo `notification.ini [NotificationSettings] PublishWithoutNotification=enabled` gesetzt ist und die Rolle
`content/publish_without_notification` hat, bietet das Bearbeitungsformular **Ohne Benachrichtigung veröffentlichen**:
Die Version wird veröffentlicht, aber Folgende erhalten keine Mail. Verwenden Sie das für Tippfehler, nicht für
Änderungen, von denen Menschen wissen müssen
([Ohne Benachrichtigung veröffentlichen](../../../../funktionen/6.0/veroeffentlichen-ohne-benachrichtigung.md)).

## Grenzen

| Grenze | Folge |
|---|---|
| Nur Kontoinhaber | die Öffentlichkeit ohne Konten verwendet RSS oder Newsletter |
| Beim nächsten Lauf gesendet, oder später als Zusammenfassung | nicht für Warnungen, die in Minuten ankommen müssen |
| Nur Inhalte, die der Folgende lesen darf | eingeschränkte Inhalte benachrichtigen nur die, die sie lesen dürfen |
| Zustellbarkeit von E-Mails | hängt von Ihrer Mail-Einrichtung ab; Rückläufer behandelt die Mailseite |

Benachrichtigungsmails sind ein Dienst, den die Person gewählt hat; sie werden in den Kategorien der
E-Mail-Einstellungen aufgeführt, wo Ihre Site sie verwendet
([E-Mail-Einstellungen](../../../../funktionen/6.0/e-mail-einstellungen.md)).

## Verwandte Seiten

- [Nachrichten und Warnmeldungen](README.md)
- [RSS-Feeds](rss-feeds.md)
- [Newsletter](newsletter.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
