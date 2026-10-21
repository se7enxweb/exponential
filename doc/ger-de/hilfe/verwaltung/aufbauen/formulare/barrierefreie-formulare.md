---
title: Barrierefreie Formulare
audience: government, website-creator, designer
category: public-sector
order: 30
summary: Wie Sie öffentliche Formulare, die mit dem Information Collector von Exponential gebaut sind, barrierefrei machen - an Felder gebundene Beschriftungen, Anleitungen, Kennzeichnung von Pflichtfeldern, Fehlermeldungen, die auf Felder zeigen, Tastaturbedienung, Alternativen zu CAPTCHA - mit den einschlägigen Kriterien der WCAG 2.2 und wie man testet.
keywords: barrierefreie Formulare, Beschriftungen, Anleitungen, Pflichtfelder, Fehlererkennung, Fehlerempfehlung, Tastatur, CAPTCHA, Testen, accessible forms, WCAG, labels, instructions, required fields, error identification, error suggestion, aria-describedby, aria-invalid, keyboard, testing
type: help
---
# Barrierefreie Formulare

Diese Seite richtet sich an Site-Ersteller und Designer, die die Templates öffentlicher Formulare bauen. Ein Formular,
das sich nicht mit Tastatur oder Screenreader ausfüllen lässt, schließt Menschen von einer öffentlichen Leistung aus.
Die Formulare der Administration von Exponential wurden nach Kriterien der WCAG 2.2 AA gebaut und getestet
(Beschriftungen, Fehlerzusammenfassungen, `aria-invalid`), mit dem Status **Partly complies**; die öffentlichen
Formular-Templates gehören zu Ihrem Design und sind nicht geprüft
([Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md)). Die Muster der Administration sind ein gutes
Vorbild.

## Inhalt

1. [Die wichtigen Kriterien](#die-wichtigen-kriterien)
2. [Beschriftungen und Anleitungen](#beschriftungen-und-anleitungen)
3. [Fehler](#fehler)
4. [Tastatur und Fokus](#tastatur-und-fokus)
5. [CAPTCHA und Barrierefreiheit](#captcha-und-barrierefreiheit)
6. [Ein Formular testen](#ein-formular-testen)

## Die wichtigen Kriterien

Aus den [WCAG 2.2](https://www.w3.org/TR/WCAG22/):

| Kriterium | Was es verlangt |
|---|---|
| 1.3.1 Info und Beziehungen | Beschriftungen programmatisch an Felder gebunden; Gruppen von Optionsfeldern in einem Fieldset mit Legende |
| 3.3.1 Fehlererkennung | Fehler werden als Text beschrieben |
| 3.3.2 Beschriftungen oder Anweisungen | eine sichtbare Beschriftung und nötige Anleitungen |
| 3.3.3 Fehlerempfehlung | wie der Fehler zu beheben ist |
| 3.3.7 Redundante Eingabe (neu in 2.2) | nicht erneut nach etwas fragen, das im selben Ablauf schon angegeben wurde |
| 3.3.8 Barrierefreie Authentifizierung (Minimum) (neu in 2.2) | kein kognitiver Test als einziger Weg zur Anmeldung |
| 2.1.1 Tastatur | alles funktioniert ohne Maus |
| 4.1.2 Name, Rolle, Wert | jedes Bedienelement hat einen zugänglichen Namen |

## Beschriftungen und Anleitungen

Das View-Template des sammelnden Attributs erzeugt das Eingabefeld. Geben Sie in Ihrem Formular-Template jedem
Eingabefeld eine sichtbare Beschriftung, die mit `for` und `id` daran gebunden ist; das mitgelieferte Template von
ezwebin zeigt das Muster
([Formulare mit dem Information Collector](../../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md#the-form-template)).

| Praxis | Warum |
|---|---|
| Die Beschriftung ist der Name des Attributs, für Besucher geschrieben („Ihre E-Mail-Adresse“) | der Name des Klassenattributs wird angezeigt |
| Anleitungen vor dem Feld, mit `aria-describedby` gebunden | Screenreader lesen sie mit dem Feld vor |
| Pflichtfelder als Text gekennzeichnet („Pflichtfeld“), nicht nur mit Farbe oder einem Sternchen | 1.4.1 Benutzung von Farbe |
| Formate erklärt („Datum als TT.MM.JJJJ“) | 3.3.2 |
| `autocomplete`-Attribute bei Name und E-Mail | hilft Menschen mit motorischen und Gedächtnisschwierigkeiten |

## Fehler

Schlägt die Prüfung fehl, zeigt Exponential das Formular erneut mit dem Prüfergebnis an
(`collectedinfo_validation.tpl` im mitgelieferten Template). Gestalten Sie die Meldung so, dass sie:

- oben als Zusammenfassung erscheint, die auf jedes Feld verlinkt, und den Fokus erhält;
- neben jedem Feld erscheint, mit `aria-describedby` gebunden, mit `aria-invalid="true"` am Feld;
- sagt, wie der Fehler zu beheben ist („Geben Sie eine E-Mail-Adresse wie name@example.com ein“).

Das Design admin4 tut genau das für seine eigenen Formulare (`tests/tests/design/AdminFormErrorsTest.php` sichert es
ab), seine Templates sind also eine funktionierende Referenz.

## Tastatur und Fokus

- Das Formular lässt sich mit Tab, Umschalt+Tab, Leertaste und Eingabetaste ausfüllen und senden.
- Der Fokus ist immer sichtbar.
- Kein Zeitlimit, oder eine Möglichkeit, es zu verlängern; ein abgelaufenes Formular-Token zeigt eine Seite, die sagt,
  dass nichts gespeichert wurde und wie man es erneut versucht ([CSRF-Formular-Token](csrf-formular-token.md)).

## CAPTCHA und Barrierefreiheit

Visuelle Rätsel schließen Menschen aus. Wenn Sie ein CAPTCHA verwenden
([Spamschutz und Drittanbieter](spamschutz-und-drittanbieter.md)), wählen Sie einen Modus mit einer barrierefreien
Alternative und bieten Sie einen anderen Kanal an (Telefon, E-Mail). Für öffentliche Formulare mit geringem Aufkommen
genügt oft das Formular-Token plus ein einfaches Honeypot-Feld in Ihrem Template.

## Ein Formular testen

- [ ] Füllen Sie es nur mit der Tastatur aus und senden Sie es.
- [ ] Füllen Sie es mit einem Screenreader aus: Jedes Feld kündigt seine Beschriftung, Anleitung und den Pflichtstatus
  an.
- [ ] Senden Sie es leer: Die Fehlerzusammenfassung wird angekündigt und verlinkt auf die Felder.
- [ ] Zoomen Sie auf 200 % und verwenden Sie ein 320 px breites Fenster: Nichts wird abgeschnitten.
- [ ] Führen Sie als ersten Durchgang eine automatische Prüfung aus
  ([Barrierefreiheit testen](../../../../../eng-us/help/creators/website-creators/testing-accessibility.md)).

## Verwandte Seiten

- [Formulare aufbauen](README.md)
- [Grundlagen des Information Collectors](grundlagen-des-information-collectors.md)
- [Barrierefreiheit für Site-Ersteller](../../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
