---
title: Formulare aufbauen
audience: government, website-creator, admin, project-manager
category: public-sector
order: 10
summary: Index der Anleitungen zum Aufbauen von Formularen des öffentlichen Sektors in Exponential - der Information Collector, barrierefreie Formulare, Spamschutz und seine Folgen bei Drittanbietern, CSRF-Formular-Token, Dateianhänge, Mail an Empfänger, Aufbewahrung des Gesammelten sowie Beteiligungsverfahren und Umfragen.
keywords: Formulare, Information Collector, barrierefreie Formulare, Spam, CAPTCHA, CSRF, Formular-Token, Anhänge, Mail-Empfänger, Aufbewahrung, Beteiligungsverfahren, Umfrage, forms, information collector, accessible forms, form token, attachments, mail receiver, retention, consultation, poll
type: help
---
# Formulare aufbauen

Dieser Ordner richtet sich an Site-Ersteller, Administratoren und Projektleiter, die Formulare auf einer Site des
öffentlichen Sektors aufbauen: Kontaktformulare, Meldungen von Problemen, Rückmeldungen, Antworten in
Beteiligungsverfahren, Umfragen und einfache Anträge. Exponential baut Formulare mit dem **Information Collector**:
Attribute einer Inhaltsklasse, die Besucher ausfüllen, gespeichert als Sammlungen und optional per Mail versendet. Die
Anleitungen behandeln den Mechanismus, wie man Formulare barrierefrei und sicher macht, und wie man mit dem Gesammelten
umgeht, solange man es aufbewahrt, und nicht länger.

## Inhalt

1. [Die Seiten](#die-seiten)
2. [Lesereihenfolge](#lesereihenfolge)
3. [Bevor Sie ein Formular bauen](#bevor-sie-ein-formular-bauen)

## Die Seiten

| Seite | Die Antwort in einer Zeile |
|---|---|
| [Grundlagen des Information Collectors](grundlagen-des-information-collectors.md) | Sammelnde Attribute, das Formular-Template, Formulartypen in `collect.ini`, wohin Einsendungen gehen |
| [Barrierefreie Formulare](barrierefreie-formulare.md) | Beschriftungen, Anleitungen, Fehler, Pflichtfelder und Tastaturbedienung für öffentliche Formulare |
| [Spamschutz und Drittanbieter](spamschutz-und-drittanbieter.md) | Formular-Token, reCAPTCHA oder hCaptcha und was die Nutzung eines Drittanbieters bedeutet, und Alternativen |
| [CSRF-Formular-Token](csrf-formular-token.md) | Wie `ezformtoken` jedes POST schützt, die Seite „Formular abgelaufen“, das zu setzende Geheimnis |
| [Dateianhänge in Formularen](dateianhaenge-in-formularen.md) | Der Datentyp Enhanced File für Uploads, seine Grenzen und die Risiken, Dateien anzunehmen |
| [Mail an Empfänger](mail-an-empfaenger.md) | Wer für jedes Formular eine Mail erhält, Empfänger je Formular, Testen ohne echte Mails |
| [Aufbewahrung gesammelter Informationen](aufbewahrung-gesammelter-informationen.md) | Einsendungen nach Plan lesen, exportieren und entfernen, mit dem Audit-Eintrag |
| [Beteiligungsverfahren und Umfragen](beteiligungsverfahren-und-umfragen.md) | Eine Klasse für Beteiligungsverfahren mit Antwortformular, und der Umfragetyp mit einer Antwort je Benutzer |

## Lesereihenfolge

1. **Grundlagen des Information Collectors**.
2. **Barrierefreie Formulare**, **CSRF-Formular-Token** und **Spamschutz und Drittanbieter**, bevor ein Formular
   online geht.
3. **Mail an Empfänger** und **Aufbewahrung gesammelter Informationen**, bevor Sie etwas Echtes sammeln.
4. **Dateianhänge in Formularen** und **Beteiligungsverfahren und Umfragen**, wenn Sie sie brauchen.

Nach oben: [Eine Site für den öffentlichen Sektor aufbauen](../README.md), der Index aller Anleitungen zum Aufbauen,
und [Behörden: hier beginnen](../../verwaltungen-hier-beginnen.md), der Anfang des Bereichs für Behörden.

## Bevor Sie ein Formular bauen

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

- [ ] Ist ein Formular das richtige Werkzeug, oder gehört der Antrag in ein Fall- oder Verfahrenssystem? Siehe
  [Auf Online-Verfahren verlinken](../leistungskatalog/auf-online-verfahren-verlinken.md#wann-ein-einfaches-formular-genügt).
- [ ] Welche Felder braucht der Zweck, und nicht mehr?
- [ ] Wer liest die Einsendungen, und wer hat `infocollector/read`?
- [ ] Wie lange werden Einsendungen aufbewahrt, und wer entfernt sie?
- [ ] Was sagt der Datenschutzhinweis neben dem Formular?

Die allgemeinen Anleitungen:
[Formulare mit dem Information Collector](../../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md),
[Ein Kontaktformular bauen](../../../../../eng-us/help/creators/website-creators/building-a-contact-form.md),
[Formulareinsendungen lesen und exportieren](../../../../../eng-us/help/creators/website-creators/form-submissions.md),
[Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md).

## Verwandte Seiten

- [Einen Leistungskatalog aufbauen](../leistungskatalog/README.md)
- [Standards zum Datenschutz](../../../../../eng-us/standards/privacy.md)
- [Sicherheitshärtung](../../../sicherheitshaertung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
