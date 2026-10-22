---
title: Ein Mitgliederbereich für Ehrenamtliche
audience: non-profit, website-creator, businesses, editor
category: non-profits
order: 50
summary: Einen Bereich nur für Ehrenamtliche auf der Exponential-Website einer Non-Profit-Organisation aufbauen - Konten per Einladung oder Registrierung mit E-Mail-Bestätigung, eine Benutzergruppe und eine Rolle für Ehrenamtliche, ein Bereich, den Anonymous nicht lesen darf, geschützte Downloads von Anleitungen und Dienstplänen, Neuigkeiten und Veranstaltungen für Ehrenamtliche, und was nicht eingebaut ist (Schichtplanung, Stundenerfassung).
keywords: Mitgliederbereich, Bereich für Ehrenamtliche, nur für Ehrenamtliche, Benutzerregistrierung, user/register, VerifyUserType, DefaultUserPlacement, Benutzergruppe, Rolle, Bereich, content/read, geschützte Downloads, Dienstplan, Anleitungen, members area, volunteers area, volunteers only, user registration, user group, role, section, protected downloads, rota, guides
type: help
---
# Ein Mitgliederbereich für Ehrenamtliche

Diese Seite richtet sich an die Website-Ersteller und die Koordinatoren. Aktive Ehrenamtliche brauchen oft
Informationen, die die Öffentlichkeit nicht sehen soll: Dienstpläne, Einführungsanleitungen, Kontaktlisten, interne
Neuigkeiten. Exponential baut einen solchen Bereich aus drei Teilen: Konten, einer Rolle und einem Bereich. Die
vollständige Anleitung ist [Mitgliederregistrierung und Mitgliederbereiche](../../ersteller/website-ersteller/mitgliederregistrierung-und-mitgliederbereiche.md).

## Inhalt

1. [Brauchen Sie ihn?](#brauchen-sie-ihn)
2. [Konten: Einladung oder Registrierung](#konten-einladung-oder-registrierung)
3. [Gruppe, Rolle und Bereich](#gruppe-rolle-und-bereich)
4. [Was in den Bereich gehört](#was-in-den-bereich-gehört)
5. [Was nicht eingebaut ist](#was-nicht-eingebaut-ist)
6. [Checkliste](#checkliste)

## Brauchen Sie ihn?

| Sie haben | Dann |
|---|---|
| Wenige Ehrenamtliche und eine Mailingliste | Wahrscheinlich nicht; eine Newsletter-Liste für Ehrenamtliche genügt |
| Viele Ehrenamtliche, Dokumente zum Teilen, wechselnde Dienstpläne | Ja |
| Sensible Informationen (Kontaktlisten, Schutzkonzepte) | Ja, mit Sorgfalt: Jedes Konto ist eine Tür |

## Konten: Einladung oder Registrierung

| Weg | Wie | Wann |
|---|---|---|
| Per Einladung | Die Koordinatoren legen im Benutzerbaum Konten für angenommene Ehrenamtliche an | Kleine Zahl, Sie möchten die Kontrolle behalten |
| Per Registrierung | Besucher registrieren sich unter `user/register`; das Konto wird über einen Link in einer E-Mail aktiviert (`VerifyUserType=email`) | Viele Ehrenamtliche; fügen Sie der Benutzerklasse ein CAPTCHA hinzu |

Setzen Sie bei der Registrierung `site.ini [UserSettings] DefaultUserPlacement` auf eine Gruppe **ohne** Rechte über
die öffentliche Website hinaus (zum Beispiel „Registered“), und verschieben Sie angenommene Ehrenamtliche selbst in
die Gruppe „Volunteers“. Andernfalls sieht jeder, der sich registriert, den Bereich für Ehrenamtliche. Quelle:
[Mitgliederregistrierung und Mitgliederbereiche](../../ersteller/website-ersteller/mitgliederregistrierung-und-mitgliederbereiche.md#einstellungen-für-die-registrierung).

## Gruppe, Rolle und Bereich

1. **Gruppe.** Legen Sie im Benutzerbaum eine Gruppe „Volunteers“ an.
2. **Bereich.** Legen Sie einen Bereich „Volunteers“ an und weisen Sie ihm den Ordner der Ehrenamtlichen zu.
   Anonymous darf ihn nicht lesen
   ([Bereiche und Objektzustände, Anleitung für einen Mitgliederbereich](../../ersteller/website-ersteller/sektionen-und-objektzustaende.md#rezept-ein-mitgliederbereich)).
3. **Rolle.** Legen Sie eine Rolle „Volunteer“ mit `content/read`, eingeschränkt auf den Bereich Volunteers, und
   `user/login` für den öffentlichen Siteaccess an. Weisen Sie sie der Gruppe Volunteers zu. Geben Sie ihr nichts
   weiter ([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)).

Besucher, die den Bereich öffnen, werden zur Anmeldung aufgefordert. Dateien im Bereich können nur von denen
heruntergeladen werden, die ihn lesen dürfen, denn Downloads laufen über `content/download`, das die Berechtigung
prüft ([Dateien und Downloads](../../ersteller/website-ersteller/dateien-und-downloads.md#geschützte-downloads)).

## Was in den Bereich gehört

| Inhalt | Klasse |
|---|---|
| Einführungsanleitung, Sicherheitsregeln | Artikel oder eine Datei (PDF) |
| Aktueller Dienstplan | Eine Datei (Tabelle oder PDF), die jeden Monat ersetzt wird, oder ein Artikel |
| Interne Neuigkeiten | Artikel in einem Ordner für Neuigkeiten |
| Veranstaltungen für Ehrenamtliche | Veranstaltungsobjekte in einem Veranstaltungskalender im Bereich ([Veranstaltungen und Kalender](../kommunikation/veranstaltungen-und-kalender-fuer-non-profit-organisationen.md)) |
| Rückmeldungen von Ehrenamtlichen | Ein Formularobjekt im Bereich |

Legen Sie keine persönlichen Kontaktlisten in den Bereich, es sei denn, alle Ehrenamtlichen haben zugestimmt;
bevorzugen Sie ein Kontaktformular oder ein gemeinsames Postfach.

## Was nicht eingebaut ist

| Nicht eingebaut | Stattdessen planen |
|---|---|
| Schichtplanung mit Anmeldeplätzen | Eine Dienstplandatei oder ein externes Planungswerkzeug, verlinkt aus dem Bereich |
| Erfassung von Stunden | Eine Tabellenkalkulation oder ein externes Werkzeug |
| Nachrichten zwischen Ehrenamtlichen | Ihre üblichen Kanäle |

## Checkliste

- [ ] Die Registrierung (falls genutzt) legt neue Konten in einer Gruppe ohne Rechte für den Bereich ab.
- [ ] Gruppe Volunteers, Rolle Volunteer und Bereich Volunteers angelegt und getestet.
- [ ] Abgemeldet verlangt der Bereich eine Anmeldung; Dateien lassen sich nicht herunterladen.
- [ ] CAPTCHA bei der Registrierung; E-Mail-Bestätigung eingeschaltet.
- [ ] Keine Kontaktlisten ohne Zustimmung.
- [ ] Ausscheidende Ehrenamtliche aus der Gruppe entfernt ([Wenn Ehrenamtliche gehen](../betrieb/wenn-ehrenamtliche-gehen.md)).

## Verwandte Seiten

- [Ehrenamtliche](README.md)
- [Inhalte nur für Mitglieder](../../unternehmen/mitglieder/inhalte-nur-fuer-mitglieder.md)
- [Berechtigungen für Besucher](../../ersteller/website-ersteller/rechte-fuer-besucher.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
