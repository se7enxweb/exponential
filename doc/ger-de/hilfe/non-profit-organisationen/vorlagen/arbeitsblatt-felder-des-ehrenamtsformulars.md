---
title: Arbeitsblatt Felder des Ehrenamtsformulars
audience: non-profit, businesses, website-creator, editor
category: non-profits
order: 30
summary: Ein Arbeitsblatt, mit dem Sie ein Anmeldeformular für Ehrenamtliche entwerfen, bevor jemand es baut - jedes Feld mit seinem Zweck, seinem Datentyp, ob es Pflicht ist, wer es liest und wie lange es aufbewahrt wird - dazu Empfänger, Dankesseite, Spamschutz, Datei-Uploads und der Text neben dem Formular, bereit zur Übergabe an den Website-Ersteller.
keywords: Ehrenamtsformular, Arbeitsblatt, Formularentwurf, Felder, Datentyp, Pflichtfeld, Aufbewahrung, Datenminimierung, Empfänger, volunteer form, worksheet, form design, fields, datatype, ezstring, ezemail, ezselection, ezboolean, eztext, enhancedezbinaryfile, required, retention, data minimization, CAPTCHA, receiver
type: help
---
# Arbeitsblatt Felder des Ehrenamtsformulars

Füllen Sie dieses Arbeitsblatt mit der Koordination der Ehrenamtlichen und der für den Datenschutz verantwortlichen
Person aus, und geben Sie es dann dem Website-Ersteller, der das Formular baut
([Anmeldeformular für Ehrenamtliche, Schritt für Schritt](../ehrenamtliche/anmeldeformular-fuer-ehrenamtliche-schritt-fuer-schritt.md)).
Die Frage für jedes Feld lautet: **Brauchen wir das, um eine ehrenamtliche Person einzusetzen?** Wenn nicht, lassen Sie
es weg.

## Inhalt

1. [Angaben zum Formular](#angaben-zum-formular)
2. [Felder](#felder)
3. [Text neben dem Formular](#text-neben-dem-formular)
4. [Nach dem Absenden](#nach-dem-absenden)
5. [Schutz](#schutz)
6. [Freigabe](#freigabe)

## Angaben zum Formular

```text
Form name (shown to visitors) .......................... ______________________
Where it appears (page address) ........................ ______________________
Purpose, in one sentence ............................... ______________________
Coordinator who reads the submissions .................. ______________________
Second person with access (holidays) ................... ______________________
```

## Felder

Die in Exponential verfügbaren Datentypen sind unter [Datentypen auswählen](../../ersteller/website-ersteller/datentypen-waehlen.md)
aufgeführt. Jedes Feld muss in der Klasse als **Informationssammler** markiert sein
([Formulare mit dem Informationssammler](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md)).

| Feld | Warum wir es brauchen | Datentyp | Pflicht? | Aufbewahrt für |
|---|---|---|---|---|
| Name | Um die Person anzusprechen | Textzeile (`ezstring`) | Ja | |
| E-Mail | Um zu antworten | `ezemail` | Ja | |
| Telefon | Nur wenn Sie Ehrenamtliche anrufen | Textzeile | Optional | |
| Interessengebiete | Um die Person einzusetzen | Auswahlliste (`ezselection`) | Ja | |
| Verfügbarkeit | Zur Planung | Auswahlliste oder Textabschnitt (`eztext`) | Optional | |
| Nachricht | Alles Weitere | Textabschnitt (`eztext`) | Optional | |
| Einwilligung zur Kontaktaufnahme | Rechtsgrundlage, dokumentiert | Kontrollkästchen (`ezboolean`) | Ja | |
| Ein Dokument (Zeugnis, Lebenslauf) | Nur wenn bei der Anmeldung erforderlich | `enhancedezbinaryfile` | Optional | |
| _Ihr Feld_ | | | | |
| _Ihr Feld_ | | | | |

Felder, auf die Sie bei der Anmeldung verzichten sollten, sofern kein klarer Bedarf besteht: Geburtsdatum,
Wohnadresse, Gesundheitsangaben, Ausweisnummern. Fragen Sie diese später ab, bei den Personen, die Sie aufnehmen, und
durch einen Menschen.

Der Beispielsatz an Feldern stammt aus [Ehrenamtliche und Anmeldeformulare](../../unternehmen/gemeinnuetzige/ehrenamtliche-und-anmeldeformulare.md#ein-formular-für-ehrenamtliche-gestalten).

## Text neben dem Formular

Schreiben Sie den Wortlaut hier auf, damit er freigegeben ist, bevor das Formular online geht:

```text
What volunteering involves ............................. ______________________
Who reads this form .................................... ______________________
How long we keep it .................................... ______________________
Consent wording (next to the checkbox) ................. ______________________
Link to the privacy notice ............................. ______________________
```

## Nach dem Absenden

| Entscheidung | Wahl |
|---|---|
| Die Mail geht an (Empfänger oder das eigene Attribut `recipient` des Formulars) | |
| Der Besucher sieht eine Ergebnisseite oder wird auf eine Dankesseite weitergeleitet (`Display=redirect`, empfohlen) | |
| Text der Dankesseite | |
| Wer antwortet innerhalb wie vieler Tage | |

Quelle: [Formulare mit dem Informationssammler](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md).

## Schutz

| Maßnahme | Entscheidung |
|---|---|
| Formular-Token (`ezformtoken`) | Standardmäßig aktiv; beibehalten |
| CAPTCHA: `hcaptcha` oder `recaptcha` | |
| Datei-Uploads: Begrenzung `MaxFiles` (standardmäßig 25; 0 bedeutet unbegrenzt und ist nicht ratsam) | |
| Wer erhält `infocollector/read` | |
| Löschplan für alte Eingänge | |

Quellen: [Spamschutz für Formulare](../../ersteller/website-ersteller/spamschutz-fuer-formulare.md),
[enhancedezbinaryfile](../../../funktionen/6.0/erweiterungen/enhancedezbinaryfile.md).

## Freigabe

```text
Coordinator ............................ date ________
Data protection contact ................ date ________
Website creator (built and tested) ..... date ________
```

## Verwandte Seiten

- [Vorlagen und Arbeitsblätter](README.md)
- [Anmeldeformular für Ehrenamtliche, Schritt für Schritt](../ehrenamtliche/anmeldeformular-fuer-ehrenamtliche-schritt-fuer-schritt.md)
- [Datenminimierung](../../verwaltung/datenschutz/datenminimierung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
