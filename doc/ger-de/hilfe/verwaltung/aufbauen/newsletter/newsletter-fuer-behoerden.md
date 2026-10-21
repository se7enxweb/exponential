---
title: Newsletter für Behörden
audience: government, project-manager, admin, editor
category: public-sector
order: 20
summary: Was die Erweiterung cjw_newsletter einer Behörde für E-Mail-Newsletter und Warnmeldungen gibt (Listen, Double-Opt-in, Ausgaben als Inhalte, Freigabe, Sprachen, Rückläufer, Statistik nur mit Einwilligung, SMS), was sie zum Laufen braucht und woher ihre Funktionen zu Einwilligung und Abmeldung kommen.
keywords: Newsletter, Warnmeldungen, E-Mail-Updates, öffentliche Informationen, Double-Opt-in, Einwilligung, E-Mail-Einstellungen, Ausgaben, Listen, Rückläufer, newsletter, cjw_newsletter, alerts, e-mail updates, public information, double opt-in, consent, mail preferences, editions, lists, bounces
type: help
---
# Newsletter für Behörden

Diese Seite richtet sich an Führungskräfte und Administratoren, die E-Mail-Newsletter oder Listen für Warnmeldungen
planen: Nachrichten aus dem Rat, Warnmeldungen zu Beteiligungsverfahren, Änderungen von Leistungen. Das
Newsletter-System von Exponential ist die Erweiterung `cjw_newsletter`, in der Administration unter **Newsletter**
(`/newsletter/index`) geöffnet
([Newsletter mit cjw_newsletter](../../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md),
[cjw_newsletter](../../../../../eng-us/features/6.0/extensions/cjw_newsletter.md)).

## Inhalt

1. [Was sie bietet](#was-sie-bietet)
2. [Was sie braucht](#was-sie-braucht)
3. [Newsletter oder Benachrichtigungen](#newsletter-oder-benachrichtigungen)
4. [Entscheidungen, bevor Sie beginnen](#entscheidungen-bevor-sie-beginnen)

## Was sie bietet

| Bereich | Was |
|---|---|
| Listen | Newsletter-Systeme gruppieren Listen; Besucher abonnieren die Listen, die sie möchten |
| Einwilligung | Double-Opt-in; Abonnements zählen erst nach der Bestätigung |
| Ausgaben | als Inhalte geschrieben, mit Entwürfen und Vorschau; eine Ausgabe in mehreren Sprachen |
| Freigabe | Ausgaben können im Posteingang der Zusammenarbeit freigegeben werden |
| Versand | im Hintergrund per Cron, mit Ratenbegrenzungen und Stapeln, Testgruppen |
| Saubere Listen | Rückläufer, eine Sperrliste, die über jeder Liste steht, eine Unterdrückungsliste |
| Statistik | anonyme Summen; Öffnungen und Klicks je Person nur mit der Einwilligung des Empfängers in „Newsletter statistics“ |
| Abmeldung | jede Ausgabe trägt einen Abmeldelink; Abmeldung mit einem Klick über das Mail-Gateway |
| SMS | Newsletter per SMS über einen anbieterneutralen Transport, aus, bis er eingeschaltet wird |

## Was sie braucht

- Die Erweiterung aktiv und ihre Klassenpakete installiert.
- Die Cron-Teile `cjw_newsletter` (in der dokumentierten Crontab alle zehn Minuten) und `cjw_newsletter_mailbox`
  (Rückläufer).
- Einen Mailserver und eine Absenderdomain mit SPF und DKIM bei Ihrem Mail-Anbieter.
- Die eingerichteten E-Mail-Einstellungen des Kernels (Absenderangaben, Fußzeile), die der Newsletter seit 4.2.0
  verwendet ([E-Mail-Einstellungen für Administratoren](../../../../anleitungen/mail-einstellungen-administrator.md)).

## Newsletter oder Benachrichtigungen

| Bedarf | Werkzeug |
|---|---|
| Ein redigierter Newsletter oder eine Warnmeldung an eine Liste | `cjw_newsletter` |
| „Sagen Sie mir, wenn sich diese Seite oder dieser Bereich ändert“ für angemeldete Benutzer | die Benachrichtigungen des Kernels (siehe „Benachrichtigungen statt eines Newsletters“) |

## Entscheidungen, bevor Sie beginnen

- Welche Listen (nach Thema, nach Gebiet), und wer jede schreibt und freigibt.
- Name und Adresse des Absenders (ein Funktionspostfach der Behörde).
- Wie oft jede Liste gesendet wird, im Anmeldeformular genannt.
- Ob personenbezogene Statistiken überhaupt verwendet werden; anonyme Summen genügen einer Behörde oft.

## Verwandte Seiten

- [Newsletter mit cjw_newsletter](../../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md)
- [Standards zum Datenschutz](../../../../../eng-us/standards/privacy.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
