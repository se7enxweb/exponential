---
title: Ein Kontaktformular für Ihr Unternehmen
audience: local-business, businesses, customer
category: local-business
order: 51
summary: Ein Kontaktformular für ein lokales Unternehmen mit Exponential aus Sicht des Inhabers - welche Felder sich lohnen, wohin die E-Mail geht, die Dankeseite, was der Ersteller mit dem Informationssammler einrichtet, der Test im abgemeldeten Zustand und wie es funktionsfähig bleibt.
keywords: Kontaktformular, Feedback-Formular, Informationssammler, EmailReceiver, Dankeseite, Felder, E-Mail-Zustellung, Test, lokales Unternehmen, contact form, information collector, thank you page, local business
type: help
---
# Ein Kontaktformular für Ihr Unternehmen

Diese Seite ist für Inhaber eines lokalen Unternehmens, die ein Kontaktformular auf der Website wollen. Die
Schritt-für-Schritt-Anleitung für Ihren Ersteller steht in [Ein Kontaktformular bauen, Schritt für Schritt](../../ersteller/website-ersteller/ein-kontaktformular-bauen.md);
diese Seite behandelt die Entscheidungen, die Sie treffen, und die Prüfungen, die Sie machen.

## Inhalt

1. [Fragen Sie wenig ab](#fragen-sie-wenig-ab)
2. [Wohin die Nachricht geht](#wohin-die-nachricht-geht)
3. [Nach dem Absenden](#nach-dem-absenden)
4. [Was der Ersteller macht](#was-der-ersteller-macht)
5. [Testen Sie es selbst](#testen-sie-es-selbst)
6. [Halten Sie es funktionsfähig](#halten-sie-es-funktionsfähig)

## Fragen Sie wenig ab

Jedes zusätzliche Feld kostet einige Besucher. Für die meisten lokalen Unternehmen:

| Feld | Pflicht | Warum |
|---|---|---|
| Name | ja | um die Antwort anzusprechen |
| E-Mail | ja | um zu antworten |
| Telefon | nein | manche Kunden bevorzugen einen Rückruf |
| Nachricht | ja | die Frage |
| Spamprüfung | ja, wenn Sie Spam bekommen | [Spam stoppen](spam-stoppen.md) |

Fragen Sie nicht nach Geburtsdatum, Adresse oder anderen Angaben, die Sie zum Beantworten einer Frage nicht brauchen.
Weniger Daten bedeuten auch weniger zu schützen ([Umgang mit Nachrichten und Datenschutz](umgang-mit-nachrichten-und-datenschutz.md)).

Schreiben Sie über das Formular, wie schnell Sie antworten („Wir antworten innerhalb eines Werktags“), und nennen Sie
die Telefonnummer für dringende Anliegen.

## Wohin die Nachricht geht

Jede Einsendung wird in Exponential gespeichert (**Setup > Gesammelte Informationen**) und, wenn eingerichtet, an eine
Empfängeradresse geschickt (`site.ini [InformationCollectionSettings] EmailReceiver`)
([Formulare mit dem Informationssammler](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md)).

- Verwenden Sie ein gemeinsames Postfach des Unternehmens (`info@...`), das mehr als eine Person liest.
- Die Website muss E-Mails versenden können, und E-Mails von der Website dürfen nicht im Spam landen. Bitten Sie
  Ihren Hoster, das zu testen ([Hosting für ein kleines Unternehmen wählen](../einstieg/hosting-fuer-ein-kleines-unternehmen-waehlen.md#fragen-an-einen-hoster)).

## Nach dem Absenden

Der Besucher sollte eine klare Seite sehen: „Vielen Dank, wir antworten innerhalb eines Werktags“. Der Ersteller
richtet eine Weiterleitung auf eine Dankeseite ein (`collect.ini [DisplaySettings] Display=redirect`). Die Dankeseite
ist eine normale Seite, die Sie bearbeiten können.

## Was der Ersteller macht

Aus [Ein Kontaktformular bauen, Schritt für Schritt](../../ersteller/website-ersteller/ein-kontaktformular-bauen.md):

1. Eine Klasse mit Sammelfeldern oder die mitgelieferte Klasse **Feedback form** (`feedback_form`).
2. Ein Feld für den Spamschutz (reCAPTCHA oder hCaptcha).
3. Das Formularobjekt, zum Beispiel „Kontakt“ unter der Startseite.
4. Das Formular-Template, mit einer sichtbaren Beschriftung für jedes Feld.
5. Die Empfängeradresse und das E-Mail-Template.
6. Die Weiterleitung auf die Dankeseite.
7. Die Berechtigung für Besucher, Formulare abzusenden (normalerweise schon vorhanden).
8. Caches leeren und ein Test.

## Testen Sie es selbst

Melden Sie sich ab (oder verwenden Sie ein privates Browserfenster) und:

- [ ] senden Sie das Formular mit leerer Nachricht ab: neben dem Feld erscheint ein Fehler;
- [ ] senden Sie es richtig ab: die Dankeseite erscheint;
- [ ] prüfen Sie das Postfach des Unternehmens: die Nachricht ist angekommen, mit der Adresse des Besuchers;
- [ ] antworten Sie darauf: die Antwort geht an den Besucher, nicht an die Website;
- [ ] unter **Setup > Gesammelte Informationen** ist die Einsendung aufgeführt;
- [ ] probieren Sie das Formular auf einem Smartphone aus.

## Halten Sie es funktionsfähig

| Wie oft | Prüfen |
|---|---|
| Monatlich | eine Testnachricht senden und sehen, dass sie ankommt |
| Nach einem Hosterwechsel oder einer E-Mail-Änderung | derselbe Test |
| Vierteljährlich | alte Einsendungen löschen, die Sie nicht mehr brauchen ([Umgang mit Nachrichten und Datenschutz](umgang-mit-nachrichten-und-datenschutz.md)) |

Ein Formular, das unbemerkt keine E-Mails mehr verschickt, ist ein häufiger und teurer Fehler: Kunden glauben, Sie
hätten sie ignoriert.

## Verwandte Seiten

- [Spam stoppen](spam-stoppen.md)
- [Formulareinsendungen lesen und exportieren](../../ersteller/website-ersteller/formulareinsendungen.md)
- [Barrierefreie Formulare](../../verwaltung/barrierefreiheit/barrierefreie-formulare.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
