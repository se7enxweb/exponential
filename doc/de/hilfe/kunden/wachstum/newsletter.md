---
title: Newsletter
audience: customer, businesses, editor
category: customer-success
order: 2
summary: Die Sicht des Betreibers darauf, einen Newsletter auf einer Exponential-Website mit der mitgelieferten Erweiterung cjw_newsletter zu starten oder wiederzubeleben - was Sie bekommen, die Entscheidungen vor dem ersten Versand, die Rollen im Team, Einwilligung, Zustellung und was Sie Ihren Partner fragen sollten.
keywords: Newsletter, cjw_newsletter, E-Mail, Verteiler, Abonnenten, Double-Opt-in, Ausgaben, Einwilligung, Bounces, E-Mail-Zustellung, Website-Betreiber, newsletter, email, mailing list, subscribers, double opt-in, editions, consent, bounces, mail delivery, site owner
type: help
---
# Newsletter

Diese Seite ist für den Betreiber oder die Marketingleitung, die einen Newsletter von der Website aus versenden oder
einen bestehenden auf eine bessere Grundlage stellen möchte. Exponential installiert die Newsletter-Erweiterung
`cjw_newsletter` mit dem Produkt (sie steht im Block `require` der `composer.json`), es gibt also nichts zusätzlich zu
kaufen oder zu lizenzieren ([Newsletter für Unternehmen](../../../../en/us/help/businesses/newsletters/README.md)). Die wöchentliche
Arbeit steht unter [Newsletter-Routine](newsletter-routine.md).

## Inhalt

1. [Was Sie bekommen](#was-sie-bekommen)
2. [Vor dem ersten Versand entscheiden](#vor-dem-ersten-versand-entscheiden)
3. [Rollen im Team](#rollen-im-team)
4. [Einwilligung und Recht](#einwilligung-und-recht)
5. [Zustellung](#zustellung)
6. [Was Sie Ihren Partner fragen sollten](#was-sie-ihren-partner-fragen-sollten)

## Was Sie bekommen

Aus [Newsletter für Unternehmen](../../../../en/us/help/businesses/newsletters/README.md) und der
[Funktionsseite zu cjw_newsletter](../../../../en/us/features/6.0/extensions/cjw_newsletter.md):

- Ausgaben werden als Inhalte geschrieben, mit Entwürfen und Vorschau wie jede andere Seite.
- Besucher abonnieren Listen mit Double-Opt-in.
- Ausgaben werden per Cron im Hintergrund versendet; jeder Versand wird archiviert.
- Bounces, eine Blacklist und eine Sperrliste halten die Listen sauber.
- Release 4.2.0 brachte Testgruppen, Wiederholungsversuche, Versandlimits, wiederkehrende Versände, Artikelpools,
  die Freigabe von Ausgaben, einwilligungsbasierte Statistik und einen CSV-Import mit Spaltenzuordnung.
- Der Newsletter wird unter **Newsletter** im oberen Menü der Administration geöffnet (`/newsletter/index`), einem
  Dashboard mit den Listen, Ausgaben, letzten Versänden, dem Transport und einer Liste zu behebender Probleme.

## Vor dem ersten Versand entscheiden

| Frage | Warum sie wichtig ist | Lesen Sie |
|---|---|---|
| Wofür ist der Newsletter da, und für wen? | Inhalt, Häufigkeit und Erfolg ergeben sich daraus | [Einen Newsletter planen](../../../../en/us/help/businesses/newsletters/newsletter-planning.md) |
| Eine Liste oder mehrere (nach Thema, nach Sprache)? | Abonnenten wählen, was sie erhalten | [Listen und Abonnements](../../../../en/us/help/businesses/newsletters/lists-and-subscriptions.md) |
| Wie oft? | ein Versprechen an die Leser; wiederkehrende Versände helfen, es zu halten | [Ausgaben schreiben und versenden](../../../../en/us/help/businesses/newsletters/writing-and-sending-editions.md) |
| Müssen Ausgaben freigegeben werden? | die Freigabe wird pro Liste eingeschaltet | ebenda |
| Welches Aussehen (Skin)? | 4.2.0 liefert `default`, `company`, `news` und `shop` mit | ebenda |
| Statistik, und mit welcher Einwilligung? | die Auswertung beruht auf Einwilligung | [Opt-in und Einwilligung](../../../../en/us/help/businesses/newsletters/opt-in-and-consent.md) |
| Eine bestehende Liste übernehmen? | die Einwilligungsnachweise müssen mitkommen | [Listen und Abonnements](../../../../en/us/help/businesses/newsletters/lists-and-subscriptions.md) |

## Rollen im Team

| Rolle | Tut | Braucht |
|---|---|---|
| Autor | legt die Ausgabe und ihre Artikel an, sieht sie in der Vorschau an, sendet an eine Testgruppe | Bearbeitungsrechte unterhalb der Liste |
| Freigebende Person | gibt Ausgaben von Listen mit Freigabe frei oder lehnt sie ab | die Richtlinie `newsletter/approve` |
| Versender | plant den Versand und beobachtet ihn | Zugriff auf das Newsletter-Dashboard |
| Listenverantwortliche | beantwortet Abonnenten, sieht sich Bounces und die Sperrliste an | Zugriff auf die Abonnenten |

Freigebende Personen sind die Benutzer und Gruppen, die in den Freigabeeinstellungen der Erweiterung genannt sind,
standardmäßig die Gruppe Administrator users einer Standardinstallation; niemand gibt seine eigene Anfrage frei, es sei
denn, die Selbstfreigabe ist eingeschaltet ([Ausgaben schreiben und versenden](../../../../en/us/help/businesses/newsletters/writing-and-sending-editions.md)).

## Einwilligung und Recht

Double-Opt-in, ein Einwilligungsprotokoll, ein Abmeldelink in jeder E-Mail und Statistik nur mit Einwilligung sind
unter [Opt-in und Einwilligung](../../../../en/us/help/businesses/newsletters/opt-in-and-consent.md) beschrieben. Die anderen optionalen
E-Mails der Website folgen demselben Gedanken: aus, bis die Person sie einschaltet, mit einer Abmeldung per Klick
([E-Mail-Einstellungen](../../../../en/us/features/6.0/mail-preferences.md)). Die Seiten beschreiben, was die Software
aufzeichnet; was Ihr Recht verlangt, ist eine Frage für Ihren Anwalt oder Datenschutzbeauftragten. Dies ist keine
Rechtsberatung.

## Zustellung

> **Warnung**
> Test-E-Mails verwenden den Vorschau-Transport, der in der ausgelieferten `cjw_newsletter.ini` tatsächlich versendet.
> Lassen Sie ihn während einer Probe auf die Zustellung in Dateien stellen, sonst verlassen Testversände den Server
> ([Ausgaben schreiben und versenden](../../../../en/us/help/businesses/newsletters/writing-and-sending-editions.md),
> [E-Mail-Zustellung von Newslettern](../../../../en/us/help/businesses/newsletters/newsletter-mail-delivery.md)).

Eine gute Zustellung hängt von einer echten Absenderadresse auf Ihrer eigenen Domain ab, mit den DNS-Einträgen, die
Ihr E-Mail-Anbieter verlangt, und von Versandlimits, die zu Ihrem Mailserver passen
([E-Mail-Zustellung von Newslettern](../../../../en/us/help/businesses/newsletters/newsletter-mail-delivery.md)).

## Was Sie Ihren Partner fragen sollten

- [ ] Sind die Newsletter-Klassen, die Cron-Teile (`cjw_newsletter_mailqueue_create`, `cjw_newsletter_mailqueue_process`)
      und der Transport eingerichtet und eingeplant?
- [ ] Welcher Transport wird für Testversände und welcher für echte Versände verwendet?
- [ ] Ist die Absenderdomain für den E-Mail-Anbieter vorbereitet?
- [ ] Wer erhält Bounce-Berichte?

Die technische Einrichtung steht unter [Newsletter mit cjw_newsletter](../../../../en/us/help/creators/website-creators/newsletters-with-cjw-newsletter.md)
und [Newsletter sicher versenden](../../../../en/us/help/creators/website-creators/sending-newsletters.md).

## Verwandte Seiten

- [Newsletter-Routine](newsletter-routine.md)
- [Newsletter-Checkliste](../../../../en/us/help/businesses/newsletters/newsletter-checklist.md)
- [Kommunikation mit Mitgliedern](../../../../en/us/help/businesses/members/member-communications.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
