---
title: Benachrichtigungen statt eines Newsletters
audience: government, admin, editor
category: public-sector
order: 80
summary: Wann die Benachrichtigungen des Kernels besser dienen als ein Newsletter - angemeldete Benutzer folgen einer Seite oder einem Bereich und erhalten eine E-Mail, sofort oder als Zusammenfassung, wenn sich Inhalte ändern; wie es funktioniert, der Cronjob und seine Grenzen für ein öffentliches Publikum.
keywords: Benachrichtigungen, folgen, Änderungen abonnieren, Zusammenfassung, Meine Benachrichtigungseinstellungen, Benachrichtigungs-Cronjob, Warnmeldungen, Änderungsbenachrichtigung, notifications, follow, subscribe to changes, digest, notification/use, My notification settings, notification cronjob, alerts, change notification
type: help
---
# Benachrichtigungen statt eines Newsletters

Diese Seite richtet sich an Administratoren und Redakteure, die möchten, dass Menschen von Änderungen erfahren, ohne
jedes Mal eine Ausgabe zu schreiben. Die Benachrichtigungen von Exponential schreiben Benutzern mit einem Konto eine
E-Mail, wenn sich Inhalte ändern, denen sie folgen
([Benachrichtigungen: die Anleitung für Benutzer](../../../../../eng-us/features/6.0/notifications.md),
[Benachrichtigungen für Administratoren](../../../../anleitungen/benachrichtigungen-administrator.md)).

## Inhalt

1. [Wie es funktioniert](#wie-es-funktioniert)
2. [Einrichtung](#einrichtung)
3. [Im Vergleich mit einem Newsletter](#im-vergleich-mit-einem-newsletter)
4. [Grenzen](#grenzen)

## Wie es funktioniert

- Ein Benutzer mit der Richtlinie `notification/use` folgt einem Element oder einem Teil der Site.
- Er wählt, wie Nachrichten kommen: **Sofort**, **Täglich**, **Wöchentlich** oder **Monatlich** (Zusammenfassung),
  unter **Meine Benachrichtigungseinstellungen** (`/notification/settings`).
- Das Veröffentlichen schreibt ein Ereignis; der vom Cronjob gestartete Benachrichtigungslauf schreibt und sendet die
  Nachrichten.
- Redakteure können für eine kleine Änderung **Ohne Benachrichtigung veröffentlichen** wählen, wo die Site es anbietet.

## Einrichtung

1. Geben Sie der Rolle der Menschen, die es nutzen sollen, die Richtlinie `notification/use`.
2. Stellen Sie sicher, dass die Cronjob-Gruppe `frequent` läuft (sie enthält den Benachrichtigungsteil); ohne Lauf
   erhält niemand eine Mail.
3. Prüfen Sie **Setup > Benachrichtigungen** (`/notification/status`) auf den Zustand und probieren Sie einen Lauf ohne
   Versand, wie es die Anleitung für Administratoren zeigt.

## Im Vergleich mit einem Newsletter

| | Benachrichtigungen | Newsletter (`cjw_newsletter`) |
|---|---|---|
| Wer | Benutzer mit einem Konto | jeder, der abonniert |
| Was gesendet wird | eine automatische Nachricht je Änderung oder eine Zusammenfassung | eine für den Zweck geschriebene Ausgabe |
| Aufwand je Nachricht | keiner | eine Ausgabe schreiben und freigeben |
| Passt für | Mitarbeiter, Mitglieder, Partner, die einem Bereich folgen | die Öffentlichkeit |

## Grenzen

Menschen brauchen ein Konto, was die Öffentlichkeit dafür meist nicht möchte; für öffentliche Warnmeldungen verwenden
Sie eine Newsletter-Liste oder einen Feed (siehe „Listen für Warnmeldungen zu öffentlichen Bekanntmachungen“).
Benachrichtigungsmails laufen wie alle Mails durch das Mail-Gateway.

## Verwandte Seiten

- [Benachrichtigungen: die Anleitung für Benutzer](../../../../../eng-us/features/6.0/notifications.md)
- [Listen für Warnmeldungen zu öffentlichen Bekanntmachungen](listen-fuer-warnmeldungen-zu-oeffentlichen-bekanntmachungen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
