---
title: "Kontakt und Formulare für lokale Unternehmen"
audience: local-business, businesses, customer
category: local-business
order: 50
summary: Die Seiten dazu, wie Kunden ein lokales Unternehmen über seine Exponential-Website erreichen - ein Kontaktformular mit dem Informationssammler, Spamschutz mit Formular-Token, reCAPTCHA oder hCaptcha, Angebots- und Anfrageformulare, Sternebewertungen und Kommentare und ein sorgfältiger Umgang mit Nachrichten im Sinne des Datenschutzes.
keywords: Kontaktformular, Informationssammler, Feedback-Formular, Spam, CAPTCHA, recaptcha, hcaptcha, ezformtoken, Angebotsanfrage, Bewertungen, ezstarrating, Kommentare, Datenschutz, lokales Unternehmen, contact form, information collector, privacy, local business
type: help
---
# Kontakt und Formulare für lokale Unternehmen

Diese Seitengruppe ist für lokale Unternehmen, die möchten, dass Kunden über die Website Kontakt aufnehmen, und die
Spam draußen halten wollen. Formulare werden in Exponential mit dem **Informationssammler** gebaut: Felder einer Klasse
werden zum Sammeln von Eingaben markiert, Besucher füllen sie auf der Seite aus, und jede Einsendung wird gespeichert
und kann Ihnen per E-Mail geschickt werden
([Formulare mit dem Informationssammler](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md)).

## Inhalt

1. [Die Seiten dieser Gruppe](#die-seiten-dieser-gruppe)
2. [Was mitgeliefert wird](#was-mitgeliefert-wird)
3. [Bevor Sie ein Formular hinzufügen](#bevor-sie-ein-formular-hinzufügen)

## Die Seiten dieser Gruppe

| Seite | In einer Zeile |
|---|---|
| [Ein Kontaktformular für Ihr Unternehmen](ein-kontaktformular-fuer-ihr-unternehmen.md) | die Felder, die E-Mail, die Dankeseite, der Test |
| [Spam stoppen](spam-stoppen.md) | Formular-Token, reCAPTCHA oder hCaptcha und weitere Maßnahmen |
| [Angebots- und Anfrageformulare](angebots-und-anfrageformulare.md) | Angebotsanfragen, Catering, Rückrufe, einfache Anmeldungen |
| [Bewertungen und Rezensionen](bewertungen-und-rezensionen.md) | Sternebewertungen mit `ezstarrating`, Kommentare und Rezensionen auf externen Plattformen |
| [Umgang mit Nachrichten und Datenschutz](umgang-mit-nachrichten-und-datenschutz.md) | Einsendungen lesen, beantworten, exportieren und löschen |

## Was mitgeliefert wird

| Teil | Eingebaut oder Erweiterung | In `composer.json` | Quelle |
|---|---|---|---|
| Informationssammler | eingebaut | Kernel | [Formulare mit dem Informationssammler](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md) |
| Klasse für Feedback-Formulare | Seitenpaket `ezwebin` (`feedback_form`) | `se7enxweb/ezwebin` | `extension/ezwebin/packages/feedback_form-1.0-1.ezpkg` |
| Formular-Token gegen gefälschte Einsendungen | `ezformtoken` | im Kernel-Repository mitgeliefert | [Spamschutz für Formulare](../../ersteller/website-ersteller/spamschutz-fuer-formulare.md) |
| reCAPTCHA | `recaptcha` | `se7enxweb/recaptcha` | [recaptcha](../../../funktionen/6.0/erweiterungen/recaptcha.md) |
| hCaptcha | `hcaptcha` | `se7enxweb/hcaptcha` | [hcaptcha](../../../funktionen/6.0/erweiterungen/hcaptcha.md) |
| Sternebewertungen | `ezstarrating` | `se7enxweb/ezstarrating` | [ezstarrating](../../../funktionen/6.0/erweiterungen/ezstarrating.md) |

## Bevor Sie ein Formular hinzufügen

- Wer liest die Nachrichten, und wie schnell antworten Sie? Ein Formular, das erst nach einer Woche beantwortet wird,
  schadet mehr als eine Telefonnummer.
- Welche Adresse erhält die E-Mails? Verwenden Sie eine gemeinsame Adresse des Unternehmens, nicht das private
  Postfach einer einzelnen Person.
- Was sagt Ihre Datenschutzerklärung über die Daten? ([Umgang mit Nachrichten und Datenschutz](umgang-mit-nachrichten-und-datenschutz.md))

## Verwandte Seiten

- [Lokale Unternehmen: hier beginnen](../lokale-unternehmen-hier-beginnen.md)
- [Formulare und gesammelte Informationen](../../kunden/wachstum/formulare-und-gesammelte-informationen.md)
- [Einwilligung und Formulare](../../unternehmen/sicherheit/einwilligung-und-formulare.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
