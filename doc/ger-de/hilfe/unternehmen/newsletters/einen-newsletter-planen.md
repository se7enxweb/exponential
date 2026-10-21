---
title: Einen Newsletter planen
audience: businesses, sales-marketing, project-manager
category: business-solutions
order: 10
summary: Die Entscheidungen, die ein Unternehmen oder eine Organisation trifft, bevor sie in Exponential einen Newsletter einrichtet - Zweck, Zielgruppe und Listen, Häufigkeit, wer schreibt und wer freigibt, Einwilligung, Versandinfrastruktur und wie Erfolg gemessen wird.
keywords: Newsletter-Planung, E-Mail-Marketing-Plan, Zielgruppe, Listen, Häufigkeit, Redaktionskalender, Freigabe, Einwilligung, Absenderdomain, Newsletter-Ziele, newsletter planning, email marketing plan, audience, lists, frequency, editorial calendar, approval, consent, sender domain, newsletter goals
type: help
---
# Einen Newsletter planen

Diese Seite richtet sich an Inhaber, Führungskräfte und Marketingverantwortliche, die einen Newsletter wollen und
entscheiden müssen, was er sein soll, bevor irgendjemand Software konfiguriert. Sie macht aus den Funktionen der
Erweiterung `cjw_newsletter` eine kurze Liste geschäftlicher Entscheidungen. Jede Entscheidung nennt die Funktion, die
sie umsetzt, und verlinkt auf die Seite, die sie erklärt.

## Inhalt

1. [Warum zuerst planen](#warum-zuerst-planen)
2. [Entscheidung 1: wofür der Newsletter da ist](#entscheidung-1-wofür-der-newsletter-da-ist)
3. [Entscheidung 2: Zielgruppen und Listen](#entscheidung-2-zielgruppen-und-listen)
4. [Entscheidung 3: Häufigkeit und Rhythmus](#entscheidung-3-häufigkeit-und-rhythmus)
5. [Entscheidung 4: wer schreibt, wer freigibt, wer versendet](#entscheidung-4-wer-schreibt-wer-freigibt-wer-versendet)
6. [Entscheidung 5: Einwilligung und Daten](#entscheidung-5-einwilligung-und-daten)
7. [Entscheidung 6: wie Mails den Server verlassen](#entscheidung-6-wie-mails-den-server-verlassen)
8. [Entscheidung 7: was Sie messen werden](#entscheidung-7-was-sie-messen-werden)
9. [Ein Plan auf einer Seite](#ein-plan-auf-einer-seite)

## Warum zuerst planen

Ein Newsletter geht an viele Menschen gleichzeitig und lässt sich nicht zurückholen
([Newsletter sicher versenden](../../ersteller/website-ersteller/newsletter-versenden.md)). Eine Liste, die ohne klare
Einwilligung aufgebaut wurde, lässt sich später nur schwer reparieren, und eine Absenderdomain, die in Spam-Ordnern
landet, braucht Zeit, um sich zu erholen. Ein Plan von einer Seite, vor der Einrichtung abgestimmt, vermeidet die meisten
dieser Probleme.

## Entscheidung 1: wofür der Newsletter da ist

Schreiben Sie einen Satz, der sagt, was ein Abonnent bekommt und warum es einen Platz in seinem Posteingang verdient.
Typische Zwecke:

| Zweck | Beispiel eines Versprechens an die Abonnenten |
|---|---|
| Neues aus dem Shop | Neue Produkte und Angebote, einmal im Monat |
| Neues aus der Bibliothek | Neuerwerbungen und Veranstaltungen, alle zwei Wochen |
| Berichte für Unterstützer | Was die Organisation mit der Hilfe ihrer Unterstützer erreicht hat, jedes Quartal |
| Informationen für Mitglieder | Sitzungstermine und Beschlüsse nur für Mitglieder |

Der Satz kommt auf das Anmeldeformular. Die Checkliste für Website-Ersteller verlangt genau das: „Das Anmeldeformular
sagt, was Abonnenten bekommen und wie oft“
([Newsletter-Listen und Abonnements](../../ersteller/website-ersteller/newsletter-listen-und-abonnements.md#einwilligung)).

## Entscheidung 2: Zielgruppen und Listen

In `cjw_newsletter` gruppiert ein **Newsletter-System** **Listen**, und Besucher abonnieren Listen
([cjw_newsletter](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md#die-bestandteile)). Entscheiden Sie:

- [ ] Wie viele Listen Sie brauchen. Beginnen Sie mit so wenigen wie möglich; eine Liste pro Versprechen ist eine gute
      Regel.
- [ ] Ob eine Liste öffentlich ist (jeder darf abonnieren) oder von einem Administrator gefüllt wird.
- [ ] Ob Abonnenten innerhalb einer Liste Interessen wählen. Release 4.2.0 unterstützt Interessen und einen Block
      „Artikel zu Ihren Interessen“ (`doc/rendering.md` der Erweiterung, siehe
      [rendering.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/rendering.md)).
- [ ] In welchen Sprachen jede Liste geschrieben wird. Eine Ausgabe kann in mehreren Sprachen versendet werden
      (derselbe Leitfaden).

Mehr dazu in [Listen und Abonnements](listen-und-abonnements.md).

## Entscheidung 3: Häufigkeit und Rhythmus

Wählen Sie einen Rhythmus, den Sie durchhalten können. Die Erweiterung kann nach Zeitplan versenden: Release 4.2.0 hat
**wiederkehrende Versände** an gewählten Wochentagen, wöchentlich oder monatlich (Newsletter > Wiederkehrende Versände,
`newsletter/schedule_list`), und **Artikel-Pools**, aus denen die Artikel einer Ausgabe ausgewählt werden
([editorial.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/editorial.md)). Ein Zeitplan hilft nur, wenn
es Inhalte zum Versenden gibt; vereinbaren Sie, wer jede Ausgabe bis wann füllt.

## Entscheidung 4: wer schreibt, wer freigibt, wer versendet

| Rolle | Typische Person | Was die Software bietet |
|---|---|---|
| Autor | Redakteur, Mitarbeiter im Marketing | Ausgaben sind Inhalte mit Entwürfen und Vorschau |
| Freigebender | Führungskraft, Leitung der Kommunikation | Optionale Freigabe pro Liste; eine nicht freigegebene Version wird nicht versendet |
| Versender | Redakteur oder Administrator | **Versenden** plant die Ausgabe ein; Cron erledigt die Arbeit |
| Administrator | IT, Hosting-Partner | Transporte, Cron, Bounce-Postfach, Einstellungen |

Die Freigabe wird pro Liste eingeschaltet („Approval: yes“). Freigebende sind standardmäßig die Mitglieder der
Benutzergruppe 12, der Administrator-Benutzer einer Standardinstallation, und niemand gibt seine eigene Anfrage frei,
sofern das nicht in den Einstellungen erlaubt ist
([editorial.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/editorial.md)). Einzelheiten in
[Ausgaben schreiben und versenden](ausgaben-schreiben-und-versenden.md).

## Entscheidung 5: Einwilligung und Daten

Entscheiden Sie, woher die Abonnenten kommen und wie Sie nachweisen, dass sie zugestimmt haben:

- öffentliche Anmeldeformulare verwenden ein Double-Opt-in ([Opt-in und Einwilligung](opt-in-und-einwilligung.md));
- eine importierte Liste braucht eine schriftliche **Einwilligungsquelle**, die der Import im Einwilligungsprotokoll
  festhält ([importexport.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/importexport.md));
- Statistiken zu Öffnungen und Klicks pro Person werden nur für Menschen gezählt, die die Einstellung
  „Newsletter-Statistik“ eingeschaltet haben, die standardmäßig aus ist
  ([statistics.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/statistics.md)).

> **Hinweis**
> Diese Seite beschreibt, was die Software tut. Sie ist keine Rechtsberatung. Fragen Sie Ihren Anwalt oder
> Datenschutzbeauftragten, welche Regeln für Ihren Newsletter gelten.

## Entscheidung 6: wie Mails den Server verlassen

Es gibt drei Transporte pro Art von Mail: `file` (auf die Platte schreiben, nichts wird gesendet), `smtp` und
`sendmail`
([cjw_newsletter](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md#den-gesamten-ablauf-erproben-ohne-eine-einzige-mail-zu-versenden)).
Entscheiden Sie mit Ihrem Hosting-Partner:

- [ ] Welcher Mail-Server oder Anbieter den Newsletter versendet.
- [ ] Welche Absenderadresse und Domain, und wer SPF und DKIM dafür einrichtet
      ([Newsletter sicher versenden](../../ersteller/website-ersteller/newsletter-versenden.md#von-der-probe-zur-echten-mail)).
- [ ] Ob der Anbieter begrenzt, wie viele Mails pro Minute oder Stunde gesendet werden dürfen; die Erweiterung kann
      solche Grenzen mit ihren Ratenbegrenzungen einhalten (`[ThrottleSettings]`, in der mitgelieferten
      `cjw_newsletter.ini` standardmäßig ausgeschaltet).

Mehr dazu in [Zustellung der Newsletter-Mails](zustellung-der-newsletter-mails.md).

## Entscheidung 7: was Sie messen werden

Die Mail-Warteschlange und die Liste liefern Ihnen immer die Zahlen der gesendeten, zugestellten, gebouncten und
abgemeldeten Mails, auch ohne Tracking. Öffnungen und Klicks brauchen Tracking, das in den mitgelieferten Einstellungen
ausgeschaltet ist (`[TrackingSettings] Tracking`) und bewusst eingeschaltet werden muss
([statistics.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/statistics.md)). Wählen Sie zwei oder drei
Zahlen, die Sie sich jeden Monat tatsächlich ansehen werden, zum Beispiel gewonnene und verlorene Abonnenten und die Zahl
der Bounces.

## Ein Plan auf einer Seite

```text
Newsletter name:            ...
Promise to subscribers:     ... (what, how often)
Lists:                      ...
Languages:                  ...
Writer(s):                  ...
Approver:                   ... (approval on: yes / no)
Sender address and domain:  ...
Mail provider:              ... (SPF / DKIM done by: ...)
Source of subscribers:      form with double opt-in / import (consent source: ...)
Tracking:                   off / anonymous totals / per person with consent
Numbers we review monthly:  ...
First send date:            ... (after a full rehearsal with the file transport)
```

## Verwandte Seiten

- [Newsletter für Unternehmen](README.md)
- [cjw_newsletter für Unternehmen](cjw-newsletter-fuer-unternehmen.md)
- [Checkliste Newsletter](checkliste-newsletter.md)
- [Newsletter mit cjw_newsletter](../../ersteller/website-ersteller/newsletter-mit-cjw-newsletter.md)
- [Ein Exponential-Projekt planen](../../fuehrung/ein-exponential-projekt-planen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
