---
title: Anmeldeformular für Ehrenamtliche, Schritt für Schritt
audience: non-profit, website-creator, editor, businesses
category: non-profits
order: 20
summary: Ein Anmeldeformular für Ehrenamtliche in Exponential von Anfang bis Ende bauen - eine Klasse mit Attributen des Informationssammlers, das Formularobjekt auf einer Seite für Ehrenamtliche, Empfänger und E-Mail, eine Weiterleitung auf eine Dankesseite, ein CAPTCHA, optionale Datei-Uploads mit enhancedezbinaryfile, der Text neben dem Formular und die Tests, die Sie abgemeldet durchführen.
keywords: Ehrenamtsformular, Anmeldeformular, Informationssammler, Klasse, Attribute, feedback_form, Empfänger, EmailReceiver, collect.ini, Display=redirect, Dankesseite, CAPTCHA, enhancedezbinaryfile, Testen, volunteer form, sign-up form, information collector, class, attributes, receiver, recipient, thank-you page, testing
type: help
---
# Anmeldeformular für Ehrenamtliche, Schritt für Schritt

Diese Seite richtet sich an die Website-Ersteller, die das Formular bauen, und an die Koordinatoren, die es testen.
Legen Sie die Felder zuerst mit dem [Arbeitsblatt: Felder des Ehrenamtsformulars](../vorlagen/arbeitsblatt-felder-des-ehrenamtsformulars.md)
fest. Der Mechanismus ist der Informationssammler, vollständig erklärt in
[Formulare mit dem Informationssammler](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md).

## Inhalt

1. [Schritt 1: die Klasse](#schritt-1-die-klasse)
2. [Schritt 2: das Formularobjekt](#schritt-2-das-formularobjekt)
3. [Schritt 3: wer die E-Mail erhält](#schritt-3-wer-die-e-mail-erhält)
4. [Schritt 4: nach dem Absenden](#schritt-4-nach-dem-absenden)
5. [Schritt 5: Spamschutz](#schritt-5-spamschutz)
6. [Schritt 6: Datei-Uploads, nur bei Bedarf](#schritt-6-datei-uploads-nur-bei-bedarf)
7. [Schritt 7: testen](#schritt-7-testen)
8. [Checkliste](#checkliste)

## Schritt 1: die Klasse

Die mitgelieferte Klasse `feedback_form` ist ein fertiges Beispiel; ein Ehrenamtsformular ist eine eigene Klasse, die
auf dieselbe Weise gebaut wird ([Ein Kontaktformular Schritt für Schritt bauen](../../ersteller/website-ersteller/ein-kontaktformular-bauen.md)).

1. Legen Sie unter **Setup > Klassen** eine Klasse „Volunteer form“ an (Bezeichner `volunteer_form`).
2. Fügen Sie einen Titel und eine Beschreibung (Rich Text) für den von Redakteuren geschriebenen Text über dem
   Formular hinzu.
3. Fügen Sie pro Feld aus dem Arbeitsblatt ein Attribut hinzu und setzen Sie bei jedem Feld, das Besucher ausfüllen,
   das Häkchen **Informationssammler**
   ([Inhaltsklassen für Websites, Attributeinstellungen](../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md#attributeinstellungen)).
4. Fügen Sie ein Attribut `recipient` (E-Mail) hinzu, wenn jedes Formular seine eigenen Koordinatoren anschreiben soll
   (Schritt 3).
5. Markieren Sie die Pflichtfelder.

| Feld | Datentyp |
|---|---|
| Name | `ezstring` |
| E-Mail | `ezemail` |
| Interessengebiete | `ezselection` |
| Verfügbarkeit | `ezselection` oder `eztext` |
| Nachricht | `eztext` |
| Einwilligung | `ezboolean` |

## Schritt 2: das Formularobjekt

1. Legen Sie im Menü eine Seite „Engagieren Sie sich bei uns“ an.
2. Legen Sie darunter oder darauf ein Objekt Ihrer Formularklasse an. Schreiben Sie die Beschreibung: was das
   Ehrenamt umfasst, wer das Formular liest, wie lange Sie es aufbewahren, ein Link zur Datenschutzerklärung.
3. Veröffentlichen Sie. Die Seite des Objekts zeigt nun das Formular.

Für eine neue Klasse kann ein Template für das Formular nötig sein; Ihre Website-Ersteller legen es im Design ab
([Template-Overrides](../../ersteller/website-ersteller/template-overrides.md)).

## Schritt 3: wer die E-Mail erhält

Aus [Formulare mit dem Informationssammler, E-Mail an einen Empfänger](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md#mail-an-einen-empfänger):

- `site.ini [InformationCollectionSettings] EmailReceiver` ist der Standardempfänger.
- Die E-Mail-Templates von ezwebin nehmen den Empfänger aus einem Attribut `recipient` des Formularobjekts, sofern es
  eines hat, „damit jedes Formular sein eigenes Team anschreiben kann“.
- `collect.ini [EmailSettings] SendEmail` ist für Formulare standardmäßig aktiviert.

Verwenden Sie ein gemeinsames Postfach (zum Beispiel volunteers@ Ihre Domain) statt der Adresse einer einzelnen
Person, damit keine Bewerbungen verloren gehen, wenn eine Koordinatorin geht. Testen Sie mit dem Datei-Transport,
bevor echter E-Mail-Versand eingeschaltet wird.

## Schritt 4: nach dem Absenden

Bevorzugen Sie eine Weiterleitung auf eine Dankesseite (`Display=redirect`), „damit ein Neuladen nicht erneut
sendet“ (dieselbe Seite). Die Dankesseite sagt, wann der Ehrenamtliche von Ihnen hören wird.

## Schritt 5: Spamschutz

Das Formular-Token ist standardmäßig aktiv. Fügen Sie der Klasse ein CAPTCHA-Attribut (`hcaptcha` oder `recaptcha`)
hinzu; siehe [Spamschutz für Ehrenamtsformulare](spamschutz-fuer-ehrenamtsformulare.md).

## Schritt 6: Datei-Uploads, nur bei Bedarf

`enhancedezbinaryfile` legt hochgeladene Dateien in einem Download-Ordner mit einer Begrenzung ab, standardmäßig
`MaxFiles` 25, und versendet die gesammelten Informationen als mehrteilige E-Mail, wenn eine Datei angehängt ist.
„`0` behält eine unbegrenzte Anzahl, was für eine Website mit anonymen Uploads nicht ratsam ist“
([enhancedezbinaryfile](../../../funktionen/6.0/erweiterungen/enhancedezbinaryfile.md)). Oft ist es besser, Dokumente
später und nur von angenommenen Ehrenamtlichen anzufordern.

## Schritt 7: testen

Testen Sie **abgemeldet**, so wie ein Besucher:

- [ ] Das Formular erscheint; die Beschriftungen sind sichtbar und werden von einem Screenreader vorgelesen.
- [ ] Pflichtfelder weisen eine leere Einsendung mit einer klaren Meldung ab.
- [ ] Das CAPTCHA erscheint und ist lösbar, einschließlich seiner barrierefreien Alternative.
- [ ] Eine Einsendung kommt unter **Setup > Gesammelte Informationen** an.
- [ ] Das Postfach der Koordinatoren erhält die E-Mail.
- [ ] Die Dankesseite erscheint, und ein Neuladen sendet nicht doppelt.
- [ ] Das Formular funktioniert auf einem Smartphone und allein mit der Tastatur.

## Checkliste

- [ ] Die Felder entsprechen dem freigegebenen Arbeitsblatt.
- [ ] Beschreibungstext freigegeben, Link zur Datenschutzerklärung vorhanden.
- [ ] Der Empfänger ist ein gemeinsames Postfach.
- [ ] Weiterleitung auf eine Dankesseite.
- [ ] CAPTCHA hinzugefügt und getestet.
- [ ] Uploads nur bei Bedarf, mit gesetztem `MaxFiles`.
- [ ] `infocollector/read` nur für Koordinatoren ([Bewerbungen von Ehrenamtlichen bearbeiten](bewerbungen-von-ehrenamtlichen-bearbeiten.md)).

## Verwandte Seiten

- [Ehrenamtliche](README.md)
- [Ehrenamtliche und Anmeldeformulare](../../unternehmen/gemeinnuetzige/ehrenamtliche-und-anmeldeformulare.md)
- [Barrierefreie Formulare](../../verwaltung/barrierefreiheit/barrierefreie-formulare.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
