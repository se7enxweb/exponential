---
title: Newsletter für Unternehmen
audience: businesses, sales-marketing, editor, admin
category: business-solutions
order: 1
summary: Die Einstiegsseite des Bereichs Newsletter - was ein Unternehmen, ein Shop oder eine Organisation von der Erweiterung cjw_newsletter hat, die mit Exponential geliefert wird, für wen jede Seite gedacht ist und in welcher Reihenfolge Sie sie lesen.
keywords: Newsletter, E-Mail-Marketing, Verteiler, Abonnenten, Double-Opt-in, Ausgaben, Bounces, Unternehmen, Einstiegsseite, newsletter, email marketing, cjw_newsletter, mailing list, subscribers, double opt-in, editions, bounces, business, landing page
type: help
---
# Newsletter für Unternehmen

Dieser Bereich richtet sich an Inhaber, Marketingverantwortliche und alle, die einen Newsletter für einen Laden, eine
Bibliothek, eine gemeinnützige Organisation oder eine kleine Gruppe betreiben. Er erklärt in einfacher Sprache, was das
Newsletter-System leistet, das mit Exponential geliefert wird, was vor dem ersten Versand zu entscheiden ist und wie Sie
eine Liste gesund und rechtmäßig halten. Die technischen Seiten für diejenigen, die es einrichten, sind von jeder Seite
aus verlinkt, sodass hier nichts die Konfiguration im Detail wiederholt.

## Inhalt

1. [Was Sie bekommen](#was-sie-bekommen)
2. [Für wen dieser Bereich gedacht ist](#für-wen-dieser-bereich-gedacht-ist)
3. [Die Seiten dieses Bereichs](#die-seiten-dieses-bereichs)
4. [Empfohlene Lesereihenfolge](#empfohlene-lesereihenfolge)
5. [Was dieser Bereich nicht abdeckt](#was-dieser-bereich-nicht-abdeckt)
6. [Benachbarte Bereiche](#benachbarte-bereiche)

## Was Sie bekommen

Exponential installiert die Erweiterung `cjw_newsletter` mit dem Produkt: Sie steht im `require`-Block des
`composer.json` der Installation (`se7enxweb/cjw_newsletter`), es gibt also kein separates Produkt, das Sie kaufen oder
lizenzieren müssten. Die Erweiterung beschreibt sich selbst als „ein vollständiges Newsletter-System für Exponential“
([cjw_newsletter](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md)):

- Sie schreiben Newsletter-Ausgaben als Inhalte, mit Entwürfen und Vorschau wie bei jeder anderen Seite;
- Besucher abonnieren Listen mit einem Double-Opt-in;
- Ausgaben werden im Hintergrund per Cron versendet;
- Bounces und eine Blacklist halten die Listen sauber;
- jeder Versand wird archiviert.

Release 4.2.0 (5. Oktober 2026) brachte Funktionen für die Zustellbarkeit (Sperrliste, Wiederholungsversuche,
Ratenbegrenzungen, Testgruppen), redaktionelle Funktionen (wiederkehrende Versände, Artikel-Pools, Freigabe von
Ausgaben), einwilligungsbasierte Statistiken, SMS und einen CSV-Import mit Spaltenzuordnung. Die Erweiterung arbeitet
mit den E-Mail-Einstellungen von Exponential 6.0.15, statt ein eigenes Einwilligungssystem zu führen
([cjw_newsletter, Die Funktionen von 4.2.0](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md#die-funktionen-von-420)).

> **Hinweis**
> Der Newsletter wird über **Newsletter** im oberen Menü der Administration geöffnet (`/newsletter/index`). Seit Release
> 4.1.17 ist diese Startseite ein Dashboard mit den Listen, Ausgaben, letzten Versänden, dem Transport und einer Liste
> von Problemen, die zu beheben sind ([cjw_newsletter](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md#dashboard-befehle-und-hintergrundläufe-4117)).

## Für wen dieser Bereich gedacht ist

| Sie sind | Beginnen Sie mit |
|---|---|
| Inhaber oder Führungskraft und entscheiden, ob die Website einen Newsletter bekommt | [Einen Newsletter planen](einen-newsletter-planen.md) |
| Die Person, die Ausgaben schreibt und versendet | [Ausgaben schreiben und versenden](ausgaben-schreiben-und-versenden.md) |
| Verantwortlich für Datenschutz oder Einwilligung | [Opt-in und Einwilligung](opt-in-und-einwilligung.md) |
| Der Administrator oder Hosting-Partner | [Zustellung der Newsletter-Mails](zustellung-der-newsletter-mails.md), dann die dort verlinkten technischen Seiten |

## Die Seiten dieses Bereichs

| Seite | Was sie beantwortet |
|---|---|
| [Einen Newsletter planen](einen-newsletter-planen.md) | Ziele, Zielgruppe, Häufigkeit, Rollen und die Entscheidungen vor jeder Einrichtung |
| [cjw_newsletter für Unternehmen](cjw-newsletter-fuer-unternehmen.md) | Was die Erweiterung in geschäftlichen Begriffen leistet und was nicht |
| [Listen und Abonnements](listen-und-abonnements.md) | Wie Systeme, Listen und Abonnenten zusammenhängen, Status, Import und Export |
| [Opt-in und Einwilligung](opt-in-und-einwilligung.md) | Double-Opt-in, das Einwilligungsprotokoll, Abmeldelinks, Statistiken nur mit Einwilligung |
| [Ausgaben schreiben und versenden](ausgaben-schreiben-und-versenden.md) | Ausgaben als Inhalte, Testgruppen, Freigabe, wiederkehrende Versände, einen Versand abbrechen |
| [Bounces und Listenpflege](bounces-und-listenpflege.md) | Hard und Soft Bounces, Blacklist und Sperrliste, eine Liste sauber halten |
| [Zustellung der Newsletter-Mails](zustellung-der-newsletter-mails.md) | Transporte, Proben ohne echte Mails, Ratenbegrenzungen, DNS-Einträge der Absenderdomain |
| [Checkliste Newsletter](checkliste-newsletter.md) | Eine Checkliste von der Planung bis zum ersten echten Versand und die monatliche Routine |

## Empfohlene Lesereihenfolge

1. [Einen Newsletter planen](einen-newsletter-planen.md): Entscheiden Sie, was Sie wollen, bevor jemand etwas konfiguriert.
2. [cjw_newsletter für Unternehmen](cjw-newsletter-fuer-unternehmen.md): Erfahren Sie, was das Werkzeug kann und was nicht.
3. [Opt-in und Einwilligung](opt-in-und-einwilligung.md): die Regeln, die jede Liste befolgen muss.
4. [Listen und Abonnements](listen-und-abonnements.md): Strukturieren Sie Ihre Zielgruppe.
5. [Zustellung der Newsletter-Mails](zustellung-der-newsletter-mails.md): Proben Sie ohne Versand und gehen Sie dann bewusst
   live.
6. [Ausgaben schreiben und versenden](ausgaben-schreiben-und-versenden.md): die Routine jeder Ausgabe.
7. [Bounces und Listenpflege](bounces-und-listenpflege.md): Halten Sie die Liste auf Dauer gesund.
8. [Checkliste Newsletter](checkliste-newsletter.md): Haken Sie sie vor dem ersten Versand ab.

## Was dieser Bereich nicht abdeckt

- Die schrittweise Konfiguration von Klassen, Cronjobs und Transporten: Sie steht in
  [Newsletter mit cjw_newsletter](../../ersteller/website-ersteller/newsletter-mit-cjw-newsletter.md),
  [Newsletter-Listen und Abonnements](../../ersteller/website-ersteller/newsletter-listen-und-abonnements.md) und
  [Newsletter sicher versenden](../../ersteller/website-ersteller/newsletter-versenden.md).
- Die anderen E-Mails der Website (Benachrichtigungen, Konto-Mails): siehe
  [E-Mail-Einstellungen: der Leitfaden für Benutzer](../../../funktionen/6.0/e-mail-einstellungen.md) und
  [den Leitfaden für Administratoren](../../../anleitungen/mail-einstellungen-administrator.md).
- Rechtsberatung. Die Seiten zur Einwilligung beschreiben, was die Software aufzeichnet; was Ihr Recht verlangt, ist
  eine Frage für Ihren Anwalt oder Datenschutzbeauftragten.

## Benachbarte Bereiche

- [Mitglieder](../mitglieder/README.md): Mitgliederwebsites versenden oft einen Newsletter für Mitglieder.
- [Shops](../shops/README.md): einen Shop bewerben, auch per Newsletter.
- [Sicherheit und Datenschutz](../sicherheit/README.md): Grundlagen der DSGVO und Einwilligung bei Formularen.
- [Gemeinnützige Organisationen](../gemeinnuetzige/README.md) und [Vereine und kleine Gruppen](../vereine/README.md):
  Newsletter für Unterstützer und Mitglieder.
- Behörden haben einen eigenen Bereich: [Behörden: hier beginnen](../../verwaltung/verwaltungen-hier-beginnen.md).

## Verwandte Seiten

- [Unternehmen: hier beginnen](../unternehmen-hier-beginnen.md)
- [Newsletter mit cjw_newsletter](../../ersteller/website-ersteller/newsletter-mit-cjw-newsletter.md)
- [Funktionsseite cjw_newsletter](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
