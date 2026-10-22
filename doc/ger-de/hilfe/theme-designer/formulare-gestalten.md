---
title: "Formulare gestalten"
audience: theme-designer
category: theme-design
order: 22
summary: Wie Theme-Designer die öffentlichen Formulare einer Exponential-Website gestalten - Feedback- und Kontaktformulare aus Inhaltsklassen, Aufbau eines Felds, Pflichtfelder, Fehler, die Bestätigungsseite, Captchas und was das Template behalten muss - mit Links zu den Seiten über barrierefreie Formulare und Templates.
keywords: Theme-Designer, Formulare, Kontaktformular, Feedbackformular, Informationssammlung, Felder, Beschriftungen, Fehler, Validierung, Pflichtfeld, Captcha, Bestätigung, Formulardesign, theme designer, forms, contact form, feedback form, information collection, fields, labels, errors, validation, required, confirmation, form design
type: help
---
# Formulare gestalten

Diese Seite ist für Theme-Designer, die die Formulare gestalten, die Besucher ausfüllen: Kontakt- und
Feedbackformulare, Anmeldung zum Newsletter, Anmeldung und Registrierung. In Exponential ist ein öffentliches
Feedbackformular meist eine **Inhaltsklasse**, deren Felder Informationen sammeln, sodass Redakteure ein Formular ohne
Entwickler anlegen können, und die Felder kommen aus den Templates der Datentypen. Das bestimmt, was ein
Formulardesign kann und was nicht. Die Regeln zur Barrierefreiheit stehen in
[Barrierefreie Formulare](../designer/theme-design/barrierefreie-formulare.md), die Template-Technik in
[Formulare in Templates](../designer/vorlagen/formulare-in-templates.md).

## Inhalt

1. [Woher Formulare kommen](#woher-formulare-kommen)
2. [Aufbau eines Felds](#aufbau-eines-felds)
3. [Layout](#layout)
4. [Pflicht und optional](#pflicht-und-optional)
5. [Fehler](#fehler)
6. [Nach dem Absenden](#nach-dem-absenden)
7. [Schutz vor Spam](#schutz-vor-spam)
8. [Was das Template behalten muss](#was-das-template-behalten-muss)
9. [Eine Checkliste für Formulare](#eine-checkliste-für-formulare)

## Woher Formulare kommen

| Formular | Quelle | Gestaltungsfreiheit |
|---|---|---|
| Feedback- oder Kontaktformular | eine Inhaltsklasse mit Feldern, die Informationen sammeln; Redakteure legen das Formularobjekt an | jeden Feldtyp einmal gestalten; Reihenfolge und Beschriftungen kommen aus der Klasse |
| Anmeldung, Registrierung, Passwort | die Templates des Benutzermoduls | Markup und Stile; Feldnamen fest ([Anmelde- und Benutzerseiten gestalten](anmelde-und-benutzerseiten-gestalten.md)) |
| Suche | das Suchmodul | siehe [Suchergebnisse gestalten](suchergebnisse-gestalten.md#das-formular) |
| Anmeldung zum Newsletter | die Newsletter-Erweiterung, wo installiert | siehe [Newsletter-E-Mails gestalten](newsletter-e-mails-gestalten.md) |

Weil Redakteure Feedbackformulare bauen können, gestalten Sie **jeden Feldtyp** (Textzeile, Textbereich, Auswahl,
Kontrollkästchen, E-Mail) als Komponente, nicht ein bestimmtes Formular.

## Aufbau eines Felds

Jedes Feld hat, von oben nach unten:

1. eine **Beschriftung**, immer sichtbar (ein Platzhalter ist keine Beschriftung);
2. einen optionalen **Hinweis** (Format, Länge), mit dem Feld verbunden;
3. das **Bedienelement**;
4. Platz für eine **Fehlermeldung**, nur bei Bedarf gezeigt, ohne das Layout mehr als nötig zu verschieben.

Zeichnen Sie das Feld in seinen Zuständen: Standard, Fokus, ausgefüllt, Fehler, deaktiviert
([Interaktive Zustände gestalten](interaktive-zustaende-gestalten.md)). Der Rahmen des Bedienelements braucht 3:1 Kontrast
zum Hintergrund, hell und dunkel ([Farbkontrast](../designer/theme-design/farbkontrast.md#die-ziele)).

## Layout

- Eine Spalte. Beschriftungen über den Feldern. Mehrspaltige Formulare werden falsch gelesen und brechen beim Zoomen.
- Die Feldbreite deutet die erwartete Länge an (ein Feld für die Postleitzahl ist kurz), auf kleinen Bildschirmen aber
  volle Breite.
- Gruppieren Sie zusammengehörige Felder mit einer sichtbaren Gruppenüberschrift (im Markup ein `fieldset` mit
  `legend`).
- Eine primäre Schaltfläche mit einem Verb, das sagt, was geschieht ("Nachricht senden"), am Ende des Formulars.

## Pflicht und optional

Markieren Sie, was seltener ist, und sagen Sie oben im Formular, was die Markierung bedeutet. Pflicht ist eine
Eigenschaft des Klassenfelds, festgelegt von dem, der die Klasse bearbeitet; das Design muss also für jeden Feldtyp
beides abdecken.

## Fehler

Wird ein Formular mit Fehlern abgeschickt, zeigt die Seite das Formular erneut:

- eine **Zusammenfassung** oben, mit einer Überschrift und einem Link zu jedem fehlerhaften Feld;
- eine **Meldung** an jedem Feld, in Worten, die sagen, wie man es behebt;
- die Eingaben des Besuchers bleiben in den Feldern;
- Fehlerfarbe aus den Status-Tokens, dazu ein Symbol und der Text, nie Farbe allein.

Die Validierungsmeldungen des Kernels werden von den Templates ausgegeben; ihr Wortlaut lässt sich über
Übersetzungsdateien ändern, nicht durch Bearbeiten von Kernel-Dateien
([Übersetzungen in einem Theme](../designer/erweiterungen/uebersetzungen-in-einem-theme.md)).

## Nach dem Absenden

Gestalten Sie die Bestätigungsseite: eine Überschrift, die sagt, dass es geklappt hat, was als Nächstes geschieht und
einen Weg, weiterzumachen. Bei Feedbackformularen ist die Ergebnisseite ein eigenes Template
(`content/collectedinfo/` im Kernel-Design), das Ihr Design überschreiben kann. Die E-Mail an den Betreiber der Website
ist ebenfalls ein Template; schlicht und kurz ist am besten.

## Schutz vor Spam

Für öffentliche Formulare gibt es Captcha-Erweiterungen (hcaptcha und recaptcha gehören zu den Erweiterungen der
Demonstrationsinstallation, jede mit eigenem Feldtyp und eigenen Formular-Templates). Sie laden Skripte Dritter, was
Datenschutz, Performance und die Content Security Policy betrifft; entscheiden Sie mit dem Kunden, ob eines gebraucht
wird, und gestalten Sie den Platz des Widgets, nicht sein Inneres
([Externe Ressourcen und CDNs](../designer/theme-design/externe-ressourcen-und-cdns.md)).

## Was das Template behalten muss

- Die Feldnamen und versteckten Eingaben, die das Modul liest, und das Formular-Token, das die Erweiterung ezformtoken
  Formularen hinzufügt, die abgeschickt werden (eine Seite, die es weglässt, wird abgewiesen).
- Die ids, die die Datentyp-Templates erzeugen, damit Beschriftungen verbunden bleiben
  ([Barrierefreie Formulare](../designer/theme-design/barrierefreie-formulare.md#feld-ids-aus-den-datentyp-templates)).
- Keine Inline-Stile und keine `on*=`-Handler; Feldzustände werden über Klassen und ARIA-Attribute gestaltet.

## Eine Checkliste für Formulare

- [ ] Jeder Feldtyp in allen Zuständen gezeichnet, hell und dunkel.
- [ ] Beschriftungen sichtbar; Hinweise verbunden; eine Spalte.
- [ ] Fehlerzusammenfassung und Meldungen pro Feld gezeichnet.
- [ ] Bestätigungsseite gezeichnet.
- [ ] Funktioniert bei 200 % Zoom und nur mit der Tastatur.
- [ ] Autocomplete-Attribute für Felder mit personenbezogenen Daten mit dem Entwickler vereinbart.

## Verwandte Seiten

- [Barrierefreie Formulare](../designer/theme-design/barrierefreie-formulare.md)
- [Formulare in Templates](../designer/vorlagen/formulare-in-templates.md)
- [Bearbeitungs-Templates für Datentypen](../designer/vorlagen/datentyp-bearbeitungstemplates.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
