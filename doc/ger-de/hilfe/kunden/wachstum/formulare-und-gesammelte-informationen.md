---
title: Formulare und gesammelte Informationen
audience: customer, editor, admin
category: customer-success
order: 15
summary: Die Sicht des Betreibers auf Formulare auf einer Exponential-Website - Kontakt-, Feedback-, Umfrage- und Lead-Formulare mit dem Information Collector, wer Einsendungen liest und beantwortet, E-Mails an das richtige Team, Spamschutz, Export und Löschen und eine Aufbewahrungsroutine.
keywords: Formulare, Kontaktformular, Feedback, Umfrage, Lead-Formular, Information Collector, gesammelte Informationen, Formulareinsendungen, CSV-Export, Spam, CAPTCHA, Aufbewahrung, personenbezogene Daten, forms, contact form, poll, lead form, information collector, collected information, form submissions, CSV export, retention, personal data
type: help
---
# Formulare und gesammelte Informationen

Diese Seite ist für Betreiber, die Informationen von Besuchern sammeln: Kontakt- und Feedbackformulare, Umfragen,
Anmeldungen, Lead-Formulare. In Exponential ist ein Formular ein Element einer Klasse, deren Felder als **Information
Collector** markiert sind; jedes Mal, wenn ein Besucher es absendet, wird eine **Sammlung** gespeichert
([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)). Das Formular selbst baut Ihr Website-Ersteller
([Formulare mit dem Information Collector](../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md));
was mit den Einsendungen geschieht, ist Ihre Routine.

## Inhalt

1. [Was Sie bekommen](#was-sie-bekommen)
2. [Wer die Einsendungen liest](#wer-die-einsendungen-liest)
3. [E-Mails an das richtige Team](#e-mails-an-das-richtige-team)
4. [Spam](#spam)
5. [Export, Löschen und Aufbewahrung](#export-löschen-und-aufbewahrung)
6. [Bevor Sie ein neues Formular hinzufügen](#bevor-sie-ein-neues-formular-hinzufügen)

## Was Sie bekommen

- **Setup > Gesammelte Informationen** listet die Elemente, die gesammelt haben, mit Zahlen, Suche, Sortierung und
  Seitenaufteilung; jedes verweist auf seine Sammlungen, auf das Element und auf einen **CSV-Export**
  ([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)).
- Drei Formulartypen sind bekannt: Formular, Feedback und Umfrage; eine Umfrage kann pro Benutzer einmal beantwortet
  werden, und Umfragen senden standardmäßig keine E-Mail (`settings/collect.ini`, siehe
  [Formulare mit dem Information Collector](../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md)).
- Besucher, die nicht angemeldet sind, dürfen standardmäßig Formulare senden (`CollectAnonymousData=enabled`).

## Wer die Einsendungen liest

Alles auf den Seiten der gesammelten Informationen erfordert die Richtlinie `infocollector/read`, und mit ihr liest
eine Person die Einsendungen **jedes** Formulars ([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)).
Geben Sie sie den Personen, die Formulare beantworten, nicht jedem Redakteur ([Rollen für Ihr Team](../alltag/rollen-fuer-ihr-team.md)).

| Formular | Wer antwortet | Wie schnell (Ihr Versprechen) |
|---|---|---|
| Kontakt | | |
| Feedback | | |
| Lead- oder Angebotsanfrage | | |
| Umfrage | niemand; lesen Sie die Ergebnisse | |

## E-Mails an das richtige Team

Der Standardempfänger von Formular-E-Mails ist `site.ini [InformationCollectionSettings] EmailReceiver`.
E-Mail-Templates können den Empfänger pro Formular festlegen; die ausgelieferten ezwebin-Templates übernehmen ihn aus
einem Feld `recipient` des Formularelements, wenn es eines hat, sodass jedes Formular sein eigenes Team anschreiben
kann ([Formulare mit dem Information Collector](../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md)).

> **Tipp**
> Verwenden Sie eine Team- oder Funktionsadresse als Empfänger, nicht die Adresse einer Person. Wenn jemand geht, geht
> nichts verloren ([Ausscheiden von Mitarbeitenden](../alltag/ausscheiden-von-mitarbeitenden.md)).

## Spam

Zwei Schichten schützen Formulare: Ein **Formular-Token** beweist, dass ein Absenden von Ihrer eigenen Seite kam (die
Erweiterung `ezformtoken`), und ein **CAPTCHA** (reCAPTCHA oder hCaptcha) bittet den Besucher nachzuweisen, dass er ein
Mensch ist. Kontakt- und Feedbackformulare sollten beides haben ([Spamschutz für Formulare](../../../../eng-us/help/creators/website-creators/spam-protection-for-forms.md)).
Ein CAPTCHA-Dienst lädt ein Skript von seinem Anbieter; erwähnen Sie das in Ihren Datenschutzhinweisen.
Spam-Sammlungen lassen sich unter **Setup > Gesammelte Informationen** gesammelt entfernen.

## Export, Löschen und Aufbewahrung

- **Exportieren** Sie die Einsendungen eines Formulars als CSV, wenn sie in ein anderes System müssen.
- **Entfernen** löscht nach einer Bestätigung alle Sammlungen der angehakten Formulare oder die angehakten Sammlungen
  eines Formulars; die Formulare bleiben. Es lässt sich nicht rückgängig machen: Exportieren Sie zuerst, wenn die Daten
  noch gebraucht werden ([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)).
- Legen Sie fest, wie lange Einsendungen aufbewahrt werden, schreiben Sie es in Ihre Datenschutzhinweise und löschen
  Sie nach diesem Plan. Dies ist keine Rechtsberatung ([Einwilligung und Formulare](../../../../eng-us/help/businesses/security/consent-and-forms.md),
  [Datenaufbewahrung und Löschung](../../../../eng-us/help/businesses/security/data-retention-and-deletion.md)).

Eine monatliche Routine:

- [ ] Jede neue Einsendung wurde beantwortet.
- [ ] Spam entfernt.
- [ ] Einsendungen, die älter als Ihre Aufbewahrungsfrist sind, bei Bedarf exportiert und dann entfernt.
- [ ] CSV-Exporte nur dort gespeichert, wo Ihre Regeln es erlauben, und gelöscht, wenn sie nicht mehr gebraucht werden.

## Bevor Sie ein neues Formular hinzufügen

- [ ] Fragen Sie nur ab, was Sie brauchen; jedes Feld sind Daten, um die Sie sich kümmern müssen.
- [ ] Sagen Sie auf dem Formular, was Sie mit den Daten tun und wie lange Sie sie aufbewahren.
- [ ] Legen Sie fest, wer die E-Mail erhält und wer antwortet.
- [ ] Token und CAPTCHA an; abgemeldet getestet.
- [ ] Beschriftungen sichtbar und Fehlermeldungen klar ([Barrierefreiheit für Website-Ersteller](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)).
- [ ] Das Formular steht in Ihrer Aufbewahrungsroutine.

## Verwandte Seiten

- [Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)
- [Formulareinsendungen lesen und exportieren](../../../../eng-us/help/creators/website-creators/form-submissions.md)
- [Formulare und Feedback von Mitgliedern](../../unternehmen/vereine/formulare-und-rueckmeldungen-von-mitgliedern.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
