---
title: Ehrenamtliche und Anmeldeformulare
audience: businesses, editor, website-creator
category: business-solutions
order: 30
summary: Wie eine gemeinnützige Organisation Anmeldungen von Ehrenamtlichen, Anmeldungen zu Veranstaltungen und Kontaktanfragen mit dem Informationssammler von Exponential sammelt - das Formular gestalten, Mail an den Koordinator, Dateien mit enhancedezbinaryfile, Spam-Schutz, wer Eingaben lesen darf, Exporte und das Löschen personenbezogener Daten.
keywords: Anmeldung Ehrenamtliche, Formular für Ehrenamtliche, Anmeldeformular, Informationssammler, Kontaktformular, gesammelte Informationen, Formulareingaben, personenbezogene Daten, gemeinnützige Organisation, volunteer sign-up, volunteer form, registration form, information collector, contact form, collected information, enhancedezbinaryfile, CAPTCHA, hCaptcha, form submissions, personal data, nonprofit
type: help
---
# Ehrenamtliche und Anmeldeformulare

Diese Seite richtet sich an Koordinatoren der Ehrenamtlichen, Mitarbeiter und den Website-Ersteller einer gemeinnützigen
Organisation. Sie zeigt, wie Sie Anmeldungen von Ehrenamtlichen und ähnliche Anfragen mit den Formularen sammeln, die mit
Exponential geliefert werden, wer benachrichtigt wird, wer sie lesen darf und wie Sie die personenbezogenen Daten darin
unter Kontrolle halten.

## Inhalt

1. [Wie Formulare funktionieren, in einem Absatz](#wie-formulare-funktionieren-in-einem-absatz)
2. [Ein Formular für Ehrenamtliche gestalten](#ein-formular-für-ehrenamtliche-gestalten)
3. [Den Koordinator benachrichtigen](#den-koordinator-benachrichtigen)
4. [Das Anhängen einer Datei erlauben](#das-anhängen-einer-datei-erlauben)
5. [Spam fernhalten](#spam-fernhalten)
6. [Eingaben lesen, exportieren und entfernen](#eingaben-lesen-exportieren-und-entfernen)
7. [Andere Anmeldungen](#andere-anmeldungen)
8. [Personenbezogene Daten](#personenbezogene-daten)
9. [Checkliste Formulare](#checkliste-formulare)

## Wie Formulare funktionieren, in einem Absatz

Eine Klasse hat Attribute, die als **Informationssammler** markiert sind; ein Redakteur legt ein Objekt dieser Klasse
an, und dessen Seite zeigt das Formular. Wenn ein Besucher es absendet, wird jedes Feld geprüft, eine **Sammlung**
gespeichert, eventuell eine Mail gesendet, und der Besucher sieht eine Ergebnisseite oder wird weitergeleitet.
Mitarbeiter lesen und exportieren die Sammlungen unter **Setup > Gesammelte Informationen**
([Formulare mit dem Informationssammler](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md#wie-es-funktioniert)).
Die mitgelieferte Klasse `feedback_form` ist ein fertiges Beispiel; ein Formular für Ehrenamtliche ist eine eigene
Klasse, die auf dieselbe Weise gebaut wird
([Ein Kontaktformular bauen, Schritt für Schritt](../../ersteller/website-ersteller/ein-kontaktformular-bauen.md)).

## Ein Formular für Ehrenamtliche gestalten

Fragen Sie nur, was Sie brauchen, um einen Ehrenamtlichen einzusetzen. Ein typischer Satz:

| Feld | Datentyp | Pflicht |
|---|---|---|
| Name | Textzeile (`ezstring`) | Ja |
| E-Mail | `ezemail` | Ja |
| Telefon | Textzeile | Optional |
| Interessengebiete | Auswahl (`ezselection`), mehrere erlaubt | Ja |
| Verfügbarkeit | Auswahl oder Textblock | Optional |
| Nachricht | Textblock (`eztext`) | Optional |
| Einwilligung zur Kontaktaufnahme | Kontrollkästchen (`ezboolean`) mit dem Text daneben | Ja |

Jedes davon wird im Klasseneditor als **Informationssammler** markiert
([Inhaltsklassen für Websites, Attributeinstellungen](../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md#attributeinstellungen)).
Die Beschreibung über dem Formular (was das Ehrenamt umfasst, wer das Formular liest, wie lange Sie es aufbewahren) ist
ein normales Attribut, das Redakteure schreiben.

> **Tipp**
> Halten Sie das Formular kurz. Jedes zusätzliche Feld sind personenbezogene Daten, die Sie schützen und irgendwann
> löschen müssen, und ein Grund für einen Ehrenamtlichen aufzugeben.

## Den Koordinator benachrichtigen

- `site.ini [InformationCollectionSettings] EmailReceiver` ist der Standardempfänger der Formular-Mails.
- Die Mail-Templates von ezwebin nehmen den Empfänger aus einem Attribut `recipient` des Formularobjekts, wenn es eines
  hat, „sodass jedes Formular sein eigenes Team anschreiben kann“.
- `collect.ini [EmailSettings] SendEmail` ist für Formulare standardmäßig eingeschaltet.

Quelle: [Formulare mit dem Informationssammler, Mail an einen Empfänger](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md#mail-an-einen-empfänger).
Testen Sie mit dem Datei-Transport, bevor echte Mails eingeschaltet werden (dieselbe Seite). Bevorzugen Sie nach dem
Absenden eine Weiterleitung auf eine Dankesseite (`Display=redirect`), „damit ein Neuladen nicht erneut sendet“
(dieselbe Seite, Nach dem Absenden).

## Das Anhängen einer Datei erlauben

Wenn Ehrenamtliche ein Dokument schicken müssen (ein Zeugnis, einen Lebenslauf), verwenden Sie den Datentyp
`enhancedezbinaryfile` („Enhanced File“), der für die Informationssammlung gebaut ist. Er legt hochgeladene Dateien in
einem Download-Ordner mit einer Grenze ab, `MaxFiles` standardmäßig 25, und verschickt die gesammelten Informationen als
mehrteilige Mail, wenn eine Datei angehängt ist. „`0` behält eine unbegrenzte Zahl, was für eine Website mit anonymen
Uploads nicht ratsam ist“ ([enhancedezbinaryfile](../../../funktionen/6.0/erweiterungen/enhancedezbinaryfile.md)).

Fragen Sie sich, ob Sie die Datei wirklich bei der Anmeldung brauchen; oft kann sie später von einer Person bei den
Ehrenamtlichen angefordert werden, die Sie aufnehmen.

## Spam fernhalten

| Maßnahme | Was sie tut |
|---|---|
| Formular-Token (`ezformtoken`) | Weist gefälschte Absendungen zurück; standardmäßig aktiv |
| CAPTCHA (`recaptcha` oder `hcaptcha`) | Fügen Sie der Formularklasse das CAPTCHA-Attribut hinzu |
| Spam in großen Mengen entfernen | **Setup > Gesammelte Informationen** |

Quelle: [Spam-Schutz für Formulare](../../ersteller/website-ersteller/spamschutz-fuer-formulare.md). Das CAPTCHA-Widget lädt
ein Skript von seinem Anbieter; nennen Sie den Anbieter in Ihrem Datenschutzhinweis. Beide mitgelieferten
CAPTCHA-Dienste bieten eine barrierefreie Alternative
([Barrierefreiheit für Website-Ersteller, Formulare](../../ersteller/website-ersteller/barrierefreiheit-fuer-website-ersteller.md#formulare)).

## Eingaben lesen, exportieren und entfernen

- **Setup > Gesammelte Informationen** listet jedes Formular mit Zahlen, **Sammlungen**, **CSV exportieren** und
  **Objekt** auf.
- **CSV exportieren** schreibt UTF-8 mit Byte Order Mark; Werte, die eine Tabellenkalkulation als Formel lesen würde,
  beginnen mit einem Apostroph; höchstens 50.000 Zeilen; jeder Export wird ohne die Werte im Prüfprotokoll festgehalten.
- **Ausgewählte entfernen** löscht Eingaben nach einer Bestätigung; die Formulare bleiben.
- „Alles braucht `infocollector/read`. Geben Sie es den Menschen, die die Formulare bearbeiten, nicht jedem Redakteur.“

Quelle: [Formulareingaben lesen und exportieren](../../ersteller/website-ersteller/formulareinsendungen.md) und der Leitfaden
[Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md).

## Andere Anmeldungen

| Bedarf | Verwenden Sie |
|---|---|
| Anmeldung zu einer Veranstaltung | Ein Formularobjekt auf der Seite der Veranstaltung |
| Eine schnelle Umfrage unter Unterstützern | Den Formulartyp `poll`: „Eine Umfrage kann pro Benutzer einmal beantwortet werden“ (`CollectionUserDataList[poll]=unique`) |
| Einen Newsletter für Unterstützer | `cjw_newsletter` mit Double-Opt-in, kein Formular ([Newsletter](../newsletters/README.md)) |
| Konten für Ehrenamtliche mit einem Mitgliederbereich | Benutzerregistrierung ([Mitgliederregistrierung und Mitgliederbereiche](../../ersteller/website-ersteller/mitgliederregistrierung-und-mitgliederbereiche.md)) |

Formulartypen werden in `settings/collect.ini` festgelegt
([Formulare mit dem Informationssammler, Formulartypen](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md#formulartypen-form-feedback-poll)).

## Personenbezogene Daten

> **Hinweis**
> Grundlagen, keine Rechtsberatung. Fragen Sie Ihren Datenschutzbeauftragten oder Anwalt, welche Regeln gelten.

- Sagen Sie neben dem Formular, was mit den Daten geschieht, wer sie liest und wie lange Sie sie aufbewahren.
- Beschränken Sie `infocollector/read` auf die Menschen, die die Formulare bearbeiten.
- Speichern Sie Exporte dort, wo personenbezogene Daten hingehören, und löschen Sie sie, wenn Sie fertig sind.
- Entfernen Sie alte Eingaben nach einem Zeitplan, den Sie benennen können.

Diese Punkte folgen der Checkliste für personenbezogene Daten in
[Formulareingaben lesen und exportieren](../../ersteller/website-ersteller/formulareinsendungen.md#checkliste-für-personenbezogene-daten).
Siehe auch [Einwilligung und Formulare](../sicherheit/einwilligung-und-formulare.md).

## Checkliste Formulare

- [ ] Die Felder sind auf das Nötige beschränkt; der Text der Einwilligung steht neben dem Kontrollkästchen.
- [ ] Der Empfänger ist pro Formular gesetzt; die Mail ist mit dem Datei-Transport und dann echt getestet.
- [ ] Weiterleitung auf eine Dankesseite.
- [ ] Token aktiv; CAPTCHA hinzugefügt; abgemeldet getestet.
- [ ] Datei-Uploads nur, wenn nötig, mit gesetztem `MaxFiles`.
- [ ] `infocollector/read` nur für Koordinatoren.
- [ ] Ein Löschplan für alte Eingaben, schriftlich festgehalten.

## Verwandte Seiten

- [Gemeinnützige Organisationen](README.md)
- [Formulare mit dem Informationssammler](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md)
- [Formulare und Rückmeldungen von Mitgliedern](../vereine/formulare-und-rueckmeldungen-von-mitgliedern.md)
- [Gesammelte Informationen (Leitfaden)](../../../anleitungen/gesammelte-informationen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
