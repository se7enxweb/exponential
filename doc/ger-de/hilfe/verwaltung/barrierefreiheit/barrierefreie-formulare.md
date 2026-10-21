---
title: Barrierefreie Formulare
audience: government, designer, developer, editor
category: public-sector
order: 210
summary: Für Design und Entwicklung, die öffentliche Formulare mit dem Informationssammler, der Registrierung und dem Newsletter-Abonnement von Exponential bauen - Beschriftungen, Pflichtfelder, Fehlermeldungen, CAPTCHAs, Zeitlimits und Bestätigungsseiten, mit dem, was die mitgelieferten Templates tun und was Sie ergänzen müssen.
keywords: Formulare, barrierefreie Formulare, Informationssammler, Kontaktformular, Beschriftungen, Pflichtfelder, Fehlermeldungen, Validierung, CAPTCHA, Formular-Token, Zeitlimit, öffentlicher Sektor, forms, accessible forms, information collector, contact form, labels, required fields, error messages, validation, reCAPTCHA, hCaptcha, form token, timeout, WCAG 3.3.1, 3.3.2, 3.3.8, public sector
type: help
---
# Barrierefreie Formulare

Diese Seite richtet sich an die Designerinnen und Designer sowie Entwicklerinnen und Entwickler, die die Formulare
einer öffentlichen Stelle bauen (Kontakt, Rückmeldung, Beschwerden, Registrierung, Newsletter-Abonnement), und an die
Redaktion, die sie einrichtet. In Formularen schaden Barrieren am meisten: Wer ein Formular nicht absenden kann, kann
die Leistung nicht in Anspruch nehmen. WCAG 2.2 behandelt Formulare unter 1.3.1, 3.3.1, 3.3.2, 3.3.7, 3.3.8 und 4.1.2
(<https://www.w3.org/TR/WCAG22/>).

## Inhalt

1. [Wie Formulare in Exponential gebaut werden](#wie-formulare-in-exponential-gebaut-werden)
2. [Beschriftungen und Anleitungen](#beschriftungen-und-anleitungen)
3. [Fehler](#fehler)
4. [CAPTCHAs](#captchas)
5. [Zeitlimits und abgelaufene Formulare](#zeitlimits-und-abgelaufene-formulare)
6. [Nach dem Absenden](#nach-dem-absenden)
7. [Verwandte Seiten](#verwandte-seiten)

## Wie Formulare in Exponential gebaut werden

Öffentliche Formulare sind meist Inhaltsobjekte, deren Klasse Attribute vom Typ **Informationssammler** hat; das
Template der Vollansicht zeichnet das Formular, der Kernel validiert jedes Attribut und speichert eine Sammlung
([Formulare mit dem Informationssammler](../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md)).
Das Markup des Formulars steht daher in **Ihren** Templates (der Vollansicht der Formularklasse und den Templates der
Datentypen), die das Projekt für die öffentlichen Designs nicht geprüft hat.

## Beschriftungen und Anleitungen

- Jedes Feld hat ein sichtbares `<label for>`, das es benennt; das Beispiel-Template von ezwebin zeigt das Muster, ein
  Sammler-Attribut mit dem Namen seines Klassenattributs zu beschriften.
- Pflichtfelder werden im Text gekennzeichnet („erforderlich“), nicht nur mit einem Sternchen oder einer Farbe.
- Anleitungen (das Format eines Datums, wie ein Aktenzeichen aussieht) stehen vor dem Feld und sind mit
  `aria-describedby` verknüpft.
- Zusammengehörige Felder (eine Anschrift) werden mit `fieldset` und `legend` gruppiert.
- Verwenden Sie die richtigen Eingabetypen und `autocomplete`-Werte für Name, E-Mail und Anschrift, damit Browser sie
  ausfüllen können.

## Fehler

Der Kernel validiert, und das Template `content/collectedinfo_validation.tpl` listet die Probleme auf. Stellen Sie
sicher, dass Ihre Fassung davon:

- oben im Formular erscheint, den Fokus erhält und auf jedes Feld verlinkt;
- das Feld nennt und sagt, wie das Problem zu beheben ist;
- das Eingegebene behält;
- die Felder mit `aria-invalid="true"` kennzeichnet und `aria-describedby` auf die Meldung zeigen lässt.

Die Administration admin4 tut genau das für ihre eigenen Formulare (`tests/tests/design/AdminFormErrorsTest.php`);
nutzen Sie sie als Vorbild.

## CAPTCHAs

Die optionalen Erweiterungen `recaptcha` (Google reCAPTCHA) und `hcaptcha` (hCaptcha) fügen einer Klasse ein
CAPTCHA-Attribut hinzu ([Spamschutz für Formulare](../../../../eng-us/help/creators/website-creators/spam-protection-for-forms.md)).
Ein CAPTCHA ist für viele Menschen eine Barriere und für den Datenschutz ein Dienst eines Dritten (siehe den
Datenschutzbereich dieser Hilfe). Bevorzugen Sie das Formular-Token (`ezformtoken`, standardmäßig aktiv) und
serverseitige Prüfungen; setzen Sie ein CAPTCHA nur ein, wo Missbrauch tatsächlich vorkommt, testen Sie es mit der
Tastatur und einem Screenreader und bieten Sie neben dem Formular einen anderen Kontaktweg an.

## Zeitlimits und abgelaufene Formulare

- Ein Formular-Token gehört zur Sitzung; ein abgewiesenes Token zeigt die Seite „Formular abgelaufen“
  ([Seite „Formular abgelaufen“](../../../../eng-us/features/6.0/form-expired-page.md)). Prüfen Sie, dass die Seite verständlich
  ist und den Besucher nach Möglichkeit ohne Verlust seiner Arbeit zurückgehen lässt.
- Sitzungen enden nach `[Session] SessionTimeout` (standardmäßig drei Tage). Wenn Sie den Wert aus Sicherheitsgründen
  verkürzen, denken Sie an Menschen, die lange zum Ausfüllen eines Formulars brauchen (WCAG 2.2.1 Zeiteinteilung
  anpassbar).

## Nach dem Absenden

- Verwenden Sie eine Dankesseite, die sagt, was als Nächstes geschieht und wie lange es dauert (`Display=redirect` mit
  einer `RedirectURL` in `collect.ini`, damit ein Neuladen nicht erneut absendet).
- Wird eine Bestätigungs-E-Mail versandt, schreiben Sie sie in einfachem Text mit dem Aktenzeichen und dem Kontakt.

## Verwandte Seiten

- [Formulare mit dem Informationssammler](../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md)
- [Ein Kontaktformular bauen](../../../../eng-us/help/creators/website-creators/building-a-contact-form.md)
- [Barrierefreiheit für Website-Ersteller: Formulare](../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md#forms)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
