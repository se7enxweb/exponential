---
title: Angebots- und Anfrageformulare
audience: local-business, businesses, customer
category: local-business
order: 53
summary: Formulare über „Kontakt“ hinaus für lokale Unternehmen mit Exponential - Angebotsanfragen für Handwerksbetriebe, Catering- und Tortenbestellungen, Rückrufwünsche, Anmeldungen zu Veranstaltungen und Kursen, Terminanfragen (keine Buchungen) - mit den passenden Feldern, den Formulartypen des Informationssammlers und den Fällen, in denen ein Formular nicht reicht.
keywords: Angebotsanfrage, Anfrageformular, Rückruf, Catering, Sonderbestellung, Anmeldung, Terminanfrage, Informationssammler, Umfrage, Feedback, Datei-Upload, Handwerk, lokales Unternehmen, quote request, request form, callback, appointment request, information collector, local business
type: help
---
# Angebots- und Anfrageformulare

Diese Seite ist für lokale Unternehmen, die mehr als ein allgemeines Kontaktformular brauchen: ein Dachdecker, der
Fotos vom Schaden sehen will, eine Bäckerei, die Tortenbestellungen annimmt, ein Café, das Catering anbietet, ein Kurs
mit einer Anmeldeliste. All das sind Formulare des **Informationssammlers**: eine Klasse mit Sammelfeldern, ein
Formularobjekt, und die Einsendungen werden gespeichert und per E-Mail verschickt
([Formulare mit dem Informationssammler](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md)).

## Inhalt

1. [Beispiele nach Branche](#beispiele-nach-branche)
2. [Nützliche Feldtypen](#nützliche-feldtypen)
3. [Terminanfragen sind keine Buchungen](#terminanfragen-sind-keine-buchungen)
4. [Ein Formular oder mehrere](#ein-formular-oder-mehrere)
5. [Was der Inhaber mit einer Anfrage macht](#was-der-inhaber-mit-einer-anfrage-macht)
6. [Checkliste](#checkliste)

## Beispiele nach Branche

| Unternehmen | Formular | Felder über Name und Kontakt hinaus |
|---|---|---|
| Handwerk | Angebotsanfrage | Art des Auftrags (Auswahl), Adresse des Einsatzorts, bevorzugte Tage, Beschreibung, Fotos (wenn ein Datei-Upload eingerichtet ist) |
| Bäckerei, Konditorei | Sonderbestellung | Anlass, gewünschtes Datum, Anzahl der Personen, Geschmack (Auswahl), Text für die Torte, Allergien |
| Restaurant | Catering- oder Gruppenanfrage | Datum, Anzahl der Gäste, Budgetrahmen, Ernährungswünsche |
| Friseursalon | Rückrufwunsch | bevorzugte Tageszeit (Auswahl), gewünschte Leistung |
| Praxis | Terminanfrage | bevorzugte Tage, Anlass (kurz), neue oder bestehende Patientin bzw. neuer oder bestehender Patient |
| Geschäft | Sonderbestellung | Produktbeschreibung, Menge, bis wann |
| Kurs oder Workshop | Anmeldung | welcher Termin (Auswahl), Anzahl der Plätze |

Halten Sie jedes Formular so kurz, wie der Zweck es zulässt. Mehr fragen können Sie immer noch am Telefon.

## Nützliche Feldtypen

Sammelfelder verwenden gewöhnliche Datentypen ([Datentypen wählen](../../ersteller/website-ersteller/datentypen-waehlen.md)):

| Für | Datentyp |
|---|---|
| Kurzer Text (Name, Adresse) | Textzeile |
| Langer Text (Beschreibung) | Textabschnitt |
| Eine Wahl aus einer Liste | Auswahl |
| Ja oder nein („Ich bin mit einem Rückruf einverstanden“) | Kontrollkästchen |
| Anzahl der Personen | Ganzzahl |
| E-Mail | E-Mail |
| Spamprüfung | Feld für reCAPTCHA oder hCaptcha ([Spam stoppen](spam-stoppen.md)) |

Fragen Sie die Person, die Ihre Website baut, ob Datei-Uploads in Formularen auf Ihrer Website möglich sind, bevor
Sie „Schicken Sie uns Fotos“ versprechen; andernfalls bitten Sie Kunden, Ihnen die Fotos als Antwort auf Ihre E-Mail
zu schicken.

Der Informationssammler kennt drei Formulartypen, **form**, **feedback** und **poll**
([Formulartypen](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md#formulartypen-form-feedback-poll)).
Eine schnelle Kundenumfrage („Welchen neuen Öffnungstag hätten Sie lieber?“) kann eine Umfrage (poll) sein.

## Terminanfragen sind keine Buchungen

Ein Anfrageformular sammelt Wünsche; Sie bestätigen telefonisch oder per E-Mail. Es zeigt **keine** freien Zeiten,
verhindert keine Doppelbuchungen und verschickt keine Erinnerungen. In `composer.json` und `extension/` wurde kein
Buchungs- oder Terminmodul gefunden (20. Oktober 2026). Wenn Sie Echtzeitbuchung brauchen, verwenden Sie einen
Buchungsdienst und verlinken Sie ihn, oder bitten Sie einen Partner um ein Angebot
([Was einen externen Dienst braucht](../budget/was-einen-externen-dienst-braucht.md)).

Für Kursplätze mit Begrenzung gilt dasselbe: Die Veranstaltungsseiten und Anmeldeformulare aus
[Veranstaltungen und Kalender](../../unternehmen/mitglieder/veranstaltungen-und-kalender.md#für-eine-veranstaltung-anmelden) funktionieren,
aber Platzbegrenzungen und Wartelisten sind nicht eingebaut.

## Ein Formular oder mehrere

Ein Formular pro Zweck ist für Besucher und für Sie einfacher: E-Mails kommen mit einem klaren Betreff an, und die
Felder passen zur Anfrage. Jedes Formular ist ein eigenes Objekt, platziert dort, wo Besucher es suchen (das
Angebotsformular auf der Seite „Leistungen“, das Tortenformular auf der Seite „Torten“).

## Was der Inhaber mit einer Anfrage macht

1. Die E-Mail kommt im gemeinsamen Postfach an; die Einsendung wird außerdem gespeichert.
2. Antworten Sie innerhalb der Zeit, die Sie auf dem Formular versprochen haben.
3. Notieren Sie das Ergebnis in Ihrem eigenen System (Kalender, Auftragsbuch, Buchhaltung).
4. Löschen Sie die gespeicherte Einsendung, wenn Sie sie nicht mehr brauchen ([Umgang mit Nachrichten und Datenschutz](umgang-mit-nachrichten-und-datenschutz.md)).

## Checkliste

- [ ] Ein Formular pro Zweck, jedes kurz.
- [ ] Jedes öffentliche Formular hat einen Spamschutz.
- [ ] Der Text über jedem Formular sagt, was als Nächstes passiert und wie schnell.
- [ ] Termin- und Kursformulare sagen klar, dass es Anfragen sind.
- [ ] Jedes Formular wurde abgemeldet getestet.

## Verwandte Seiten

- [Ein Kontaktformular für Ihr Unternehmen](ein-kontaktformular-fuer-ihr-unternehmen.md)
- [Formulareinsendungen](../../ersteller/website-ersteller/formulareinsendungen.md)
- [Restaurantbestellungen und was nicht eingebaut ist](../einfacher-shop/restaurantbestellungen-und-was-nicht-eingebaut-ist.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
