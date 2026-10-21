---
title: Kommunikation mit Mitgliedern
audience: businesses, editor, sales-marketing
category: business-solutions
order: 40
summary: Wie eine Mitgliederorganisation ihre Mitglieder mit Exponential auf dem Laufenden hält - Newsletter mit cjw_newsletter, Benachrichtigungen über neue Inhalte, die Seite der E-Mail-Einstellungen mit Abmeldung per Klick und Einwilligungsprotokoll, Mailzustellung und eine Generalprobe, ohne eine einzige Mail zu verschicken.
keywords: Kommunikation mit Mitgliedern, Newsletter, cjw_newsletter, Double-Opt-in, Listen, Ausgaben, Bounces, Sperrliste, Benachrichtigungen, Digest, E-Mail-Einstellungen, abmelden, Einwilligungsprotokoll, Mailzustellung, Generalprobe, member communications, newsletter, double opt-in, lists, editions, bounces, blacklist, notifications, digest, e-mail preferences, unsubscribe, consent log, mail delivery, rehearsal
type: help
---
# Kommunikation mit Mitgliedern

Diese Seite ist für die Verantwortlichen der Kommunikation in Mitgliederorganisationen. Mitglieder erwarten, regelmäßig
von Ihnen zu hören, und mit einem Klick nichts mehr von Ihnen zu hören. Exponential gibt Ihnen drei Kanäle und eine
Seite, auf der jedes Mitglied entscheidet, was es bekommt. Das Newsletter-System behandelt der Bereich
[Newsletter](../newsletters/README.md) im Detail; diese Seite zeigt, wie die Teile zu einer Mitgliederwebsite passen.

![Das Newsletter-Dashboard](../../../../eng-us/help/images/help/bu-newsletter.webp)

## Inhalt

1. [Die Kanäle](#die-kanäle)
2. [Der Newsletter](#der-newsletter)
3. [Benachrichtigungen über neue Inhalte](#benachrichtigungen-über-neue-inhalte)
4. [Eine Seite für jede Wahl](#eine-seite-für-jede-wahl)
5. [Was jede optionale Mail enthält](#was-jede-optionale-mail-enthält)
6. [Vor dem Versand proben](#vor-dem-versand-proben)
7. [Zustellung](#zustellung)
8. [Ein Kommunikationskalender](#ein-kommunikationskalender)
9. [Checkliste](#checkliste)
10. [Verwandte Seiten](#verwandte-seiten)

## Die Kanäle

| Kanal | Am besten für | Funktion |
|---|---|---|
| Newsletter | eine kuratierte Ausgabe an alle auf einer Liste, monatlich oder wöchentlich | `cjw_newsletter` |
| Benachrichtigungen | „sag mir, wenn in diesem Bereich etwas Neues erscheint“ | das Benachrichtigungssystem des Kernels |
| Die Website | Neuigkeiten, das Newsletter-Archiv, Veranstaltungen | Inhalte und die Startseite |
| Wesentliche Mails | Nachrichten zu Konto und Sicherheit | immer verschickt, lassen sich nicht abschalten |

## Der Newsletter

`cjw_newsletter` ist ein vollständiges Newsletter-System in der Administration (**Newsletter**, `/newsletter/index`):
Ausgaben werden als Inhalte geschrieben, Besucher abonnieren Listen mit Double-Opt-in, Ausgaben werden per Cron im
Hintergrund verschickt, Bounces und eine Sperrliste halten Listen sauber, und jeder Versand wird archiviert
([cjw_newsletter](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md)).

| Teil | Was die Organisation dort tut |
|---|---|
| Systeme, Listen, Ausgaben | Ausgaben als Inhalte schreiben; eine Liste pro Zielgruppe (alle Mitglieder, Vorstand, Veranstaltungen) |
| Abonnements | sehen, wer ausstehend, bestätigt, freigegeben, abgemeldet, gebounct oder gesperrt ist |
| Öffentliche Formulare | abonnieren, bestätigen, ändern und kündigen (`newsletter/subscribe`, `configure`, `unsubscribe`) |
| Versand | eine Ausgabe einplanen; Cron reiht die Mails ein und verschickt sie |
| Bounces | nach `BounceThresholdValue` Bounces (Standard 3) bekommt ein Abonnent nichts mehr |
| CSV | Abonnenten importieren und exportieren |

Die Status erklärt [Newsletter-Listen und Abonnements](../../ersteller/website-ersteller/newsletter-listen-und-abonnements.md#status),
das Schreiben und Versenden [Newsletter versenden](../../ersteller/website-ersteller/newsletter-versenden.md).

> **Tipp**
> Eine Liste pro Zielgruppe lässt Mitglieder wählen, was sie bekommen, und hält die Ausgabe relevant. Eine Ausgabe nur
> für Mitglieder kann auf Seiten nur für Mitglieder verlinken; Nichtmitglieder, die eine weitergeleitete Mail bekommen,
> werden gebeten, sich anzumelden.

## Benachrichtigungen über neue Inhalte

Mitglieder mit der Richtlinie `notification/use` können einem Bereich mit **Benachrichtigen** folgen und bekommen eine
Mail, wenn darunter etwas veröffentlicht wird, sofort oder als täglicher, wöchentlicher oder monatlicher Digest
([Benachrichtigungen: die Anleitung für Benutzer](../../../funktionen/6.0/benachrichtigungen.md)). Mails werden nur über Inhalte
verschickt, die das Mitglied lesen darf. Fügen Sie der Rolle Mitglied `notification/use` hinzu, wenn Sie das anbieten,
und lassen Sie den Cronjob für Benachrichtigungen laufen
([Benachrichtigungen für Mitarbeiter](../intranets/benachrichtigungen-fuer-mitarbeiter.md#was-laufen-muss)).

## Eine Seite für jede Wahl

Seit 6.0.15 hat jede Person eine Seite für E-Mail-Einstellungen (`/mailpreferences/settings` oder der Link **Manage**
in jeder optionalen Mail, ohne Anmeldung)
([E-Mail-Einstellungen: die Anleitung für Benutzer](../../../funktionen/6.0/e-mail-einstellungen.md)):

- Optionale Mails (Benachrichtigungen, Newsletter, Angebote, Hinweise) sind aus, bis die Person sie einschaltet.
- Newsletter und Angebote brauchen eine Bestätigung per Mail, bevor sie beginnen; eine Anfrage verfällt nach sieben
  Tagen.
- Wesentliche Mails zum Konto (Zurücksetzen des Passworts, Aktivierung, Bestellungen, rechtliche Hinweise) werden immer
  verschickt und enthalten nie Werbung.
- Eine Person kann ihre Einstellungen und jede Änderung daran als JSON oder CSV herunterladen.

Jede Änderung einer Einstellung wird mit dem genauen Text, den die Person gesehen hat, in ein
**Einwilligungsprotokoll** geschrieben
([E-Mail-Einstellungen: einrichten](../../../anleitungen/mail-einstellungen-administrator.md)). Diese Aufzeichnung hilft, wenn
jemand fragt, warum er eine Mail bekommen hat. Die rechtliche Seite behandeln
[Opt-in und Einwilligung](../newsletters/opt-in-und-einwilligung.md) und [DSGVO-Grundlagen](../sicherheit/dsgvo-grundlagen.md);
diese Seiten sind keine Rechtsberatung.

## Was jede optionale Mail enthält

Die Mailprüfung fügt jeder optionalen Mail eine Fußzeile hinzu: warum die Person sie bekommen hat, einen Link
**Unsubscribe**, der mit einem Klick wirkt, einen Link zur Seite der Einstellungen sowie Namen und Postanschrift Ihrer
Organisation; sie verschickt außerdem die Header für die Abmeldung per Klick nach RFC 8058. Name und Postanschrift der
Organisation werden leer ausgeliefert und müssen ausgefüllt werden (`mailpreferences.ini [FooterSettings]`, in einer
Überschreibung der Einstellungen)
([E-Mail-Einstellungen: einrichten, 2.2](../../../anleitungen/mail-einstellungen-administrator.md#22-die-fußzeile-wer-sendet-und-von-wo)).

## Vor dem Versand proben

`cjw_newsletter.ini` kann jede Mail in Dateien schreiben, statt sie zu verschicken; eine vollständige Generalprobe
(Abonnieren, Double-Opt-in, Versand, Cron, Abmelden) braucht also keinen Mailserver und erreicht keinen echten Empfänger
([cjw_newsletter, Rehearse](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md#den-gesamten-ablauf-erproben-ohne-eine-einzige-mail-zu-versenden)).
Machen Sie das vor der ersten echten Ausgabe und nach jeder Änderung von Templates.

## Zustellung

Ob Mails ankommen, hängt von den Einträgen für SPF, DKIM und DMARC der Absenderdomain und vom Mailserver ab; das ist
Aufgabe des Hosters ([Mailzustellung](../../hosting/betrieb/mailzustellung.md),
[Einwilligung und Bounces bei Mails für Hoster](../../hosting/betrieb/einwilligung-und-bounces-bei-mails.md)).

## Ein Kommunikationskalender

| Wann | Was | Kanal |
|---|---|---|
| Monatlich | Mitgliedernewsletter | Newsletter, Liste aller Mitglieder |
| Vor jeder Veranstaltung | Einladung und Erinnerung | Newsletter, Liste für Veranstaltungen |
| Bei Veröffentlichung | neue Mitgliederdokumente | Benachrichtigungen für alle, die folgen |
| Jährlich | Erinnerung an die Verlängerung der Mitgliedschaft | Newsletter oder eine Mail der Geschäftsstelle |

Passen Sie den Rhythmus an Ihre Organisation an; weniger, bessere Mails halten die Abmeldungen niedrig.

## Checkliste

- [ ] Newsletter-Listen pro Zielgruppe.
- [ ] Formulare zum Abonnieren und Abmelden auf der Website.
- [ ] Name und Postanschrift der Organisation in der Fußzeile ausgefüllt.
- [ ] Generalprobe mit Dateitransport erledigt.
- [ ] Cron-Teile des Newsletters eingeplant.
- [ ] `notification/use` in der Rolle Mitglied, wenn Benachrichtigungen angeboten werden.
- [ ] Einträge der Absenderdomain vom Hoster geprüft.

## Verwandte Seiten

- [Newsletter](../newsletters/README.md)
- [Newsletter mit cjw_newsletter](../../ersteller/website-ersteller/newsletter-mit-cjw-newsletter.md)
- [Veranstaltungen und Kalender](veranstaltungen-und-kalender.md)
- [Benachrichtigungen für Mitarbeiter](../intranets/benachrichtigungen-fuer-mitarbeiter.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
