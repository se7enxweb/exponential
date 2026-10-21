---
title: Mitgliederwebsite planen
audience: businesses, project-manager, website-creator
category: business-solutions
order: 10
summary: Was eine Mitgliederorganisation entscheidet, bevor sie ihre Website auf Exponential baut - Mitgliedschaftsstufen, was öffentlich und was für Mitglieder ist, wie Menschen beitreten, wie Mitglieder von der Organisation hören, Veranstaltungen, die Ehrenamtlichen, die sie betreiben, und personenbezogene Daten.
keywords: Mitgliederwebsite planen, Mitgliedschaftsstufen, Mitgliederbereich, öffentliche Inhalte, Registrierung, Freigabe, Newsletter, Veranstaltungen, Ehrenamtliche, personenbezogene Daten, DSGVO, Budget, Anforderungen, member site planning, membership levels, members area, public content, registration, approval, newsletter, events, volunteers, personal data, GDPR, budget, requirements
type: help
---
# Mitgliederwebsite planen

Diese Seite ist für den Vorstand, die Geschäftsführung oder die Verantwortlichen der Kommunikation einer
Mitgliederorganisation. Sie listet die Entscheidungen auf, die eine Mitgliederwebsite prägen, und nennt zu jeder die
Funktion von Exponential, die sie beantwortet, und die Seite, die sie erklärt. Eine kurze Planungsrunde vor dem Bau der
ersten Seite spart später Nacharbeit.

## Inhalt

1. [Warum Mitglieder auf die Website kommen](#warum-mitglieder-auf-die-website-kommen)
2. [Öffentlich und nur für Mitglieder](#öffentlich-und-nur-für-mitglieder)
3. [Mitgliedschaftsstufen](#mitgliedschaftsstufen)
4. [Wie Menschen beitreten](#wie-menschen-beitreten)
5. [Wie Mitglieder von Ihnen hören](#wie-mitglieder-von-ihnen-hören)
6. [Veranstaltungen und Gemeinschaft](#veranstaltungen-und-gemeinschaft)
7. [Wer die Website betreibt](#wer-die-website-betreibt)
8. [Personenbezogene Daten](#personenbezogene-daten)
9. [Was nicht eingebaut ist](#was-nicht-eingebaut-ist)
10. [Arbeitsblatt zur Planung](#arbeitsblatt-zur-planung)
11. [Verwandte Seiten](#verwandte-seiten)

## Warum Mitglieder auf die Website kommen

Schreiben Sie die drei oder vier Gründe auf, aus denen sich ein Mitglied anmelden würde. Typische Gründe:

- Neuigkeiten und Veröffentlichungen für Mitglieder;
- Dokumente: Protokolle, Richtlinien, Vorlagen, Aufzeichnungen;
- Veranstaltungen: was, wann, wo und wie man sich anmeldet;
- der Newsletter und sein Archiv;
- ein Ort, um mit anderen Mitgliedern zu diskutieren.

Jeder Grund wird zu einem Bereich der Website und zu einer Seite dieses Bereichs.

## Öffentlich und nur für Mitglieder

| Inhalt | Meist |
|---|---|
| Wer Sie sind, warum man beitreten sollte, Kontakt | öffentlich |
| Teaser für Neuigkeiten | öffentlich |
| Vollständige Veröffentlichungen, Mitgliederdokumente | nur für Mitglieder |
| Ankündigungen von Veranstaltungen | öffentlich oder nur für Mitglieder |
| Dokumente und Aufzeichnungen von Veranstaltungen | nur für Mitglieder |
| Forum | nur für Mitglieder |

In Exponential ist der Unterschied eine **Sektion**: Inhalte in einer Mitgliedersektion sind nur für Rollen lesbar, die
diese Sektion einschließen, und die Standardrolle Anonymous liest nur die Sektion Standard
([Sektionen, 13.1](../../../anleitungen/sektionen.md#131-ein-mitgliederbereich)). Besucher, die eine Mitgliederseite öffnen, werden
gebeten, sich anzumelden ([Inhalte nur für Mitglieder](inhalte-nur-fuer-mitglieder.md)).

## Mitgliedschaftsstufen

Wenn manche Mitglieder mehr bekommen als andere (zum Beispiel ordentliche und außerordentliche Mitglieder), ist jede
Stufe eine Benutzergruppe, und die zusätzlichen Inhalte jeder Stufe sind eine Sektion, die nur die Rolle dieser Gruppe
liest. Halten Sie die Zahl der Stufen klein; jede Stufe ist eine Rolle, die gepflegt, und eine Kombination, die getestet
werden muss.

| Stufe | Benutzergruppe | Liest |
|---|---|---|
| Außerordentliches Mitglied | Außerordentliche Mitglieder | Mitgliedersektion |
| Ordentliches Mitglied | Ordentliche Mitglieder | Mitgliedersektion, Sektion für ordentliche Mitglieder |
| Vorstand | Vorstand | alles oben Genannte, Sektion Vorstand |

## Wie Menschen beitreten

| Weg | Passt, wenn | Seite |
|---|---|---|
| Offene Registrierung mit E-Mail-Bestätigung | jeder kostenlos beitreten darf | [Registrierung von Mitgliedern](registrierung-von-mitgliedern.md) |
| Registrierung mit Freigabe durch die Organisation | die Mitgliedschaft geprüft werden muss | [Registrierung und Kontofreigabe](../extranets/registrierung-und-kontofreigabe.md) |
| Von der Geschäftsstelle angelegte Konten | Mitglieder auf Papier oder über ein anderes System beitreten | [Registrierung von Mitgliedern](registrierung-von-mitgliedern.md#von-der-geschäftsstelle-angelegte-konten) |

## Wie Mitglieder von Ihnen hören

- Ein Newsletter mit Double-Opt-in, Ausgaben per Cron verschickt, Bounces behandelt
  ([Kommunikation mit Mitgliedern](kommunikation-mit-mitgliedern.md)).
- Benachrichtigungen, wenn in Bereichen, denen ein Mitglied folgt, neue Inhalte erscheinen.
- Jede Person wählt auf einer Seite, welche optionalen Mails sie bekommt, und jede optionale Mail hat eine Abmeldung
  per Klick ([E-Mail-Einstellungen: die Anleitung für Benutzer](../../../../eng-us/features/6.0/mail-preferences.md)).

## Veranstaltungen und Gemeinschaft

Veranstaltungsseiten und Kalender kommen mit den Klassen von `ezwebin`; die Anmeldung verwendet ein Formular. Foren gibt
es ebenfalls als Klassen von `ezwebin` ([Veranstaltungen und Kalender](veranstaltungen-und-kalender.md)).

## Wer die Website betreibt

Mitgliederorganisationen stützen sich oft auf Ehrenamtliche. Planen Sie:

- eine verantwortliche Person im Vorstand oder unter den Mitarbeitern;
- Redakteure pro Bereich, jeweils mit einer Rolle, die auf ihre Sektion beschränkt ist;
- jemanden, der neue Mitglieder freigibt;
- einen Hoster oder Partner für die technische Seite ([Hosting auswählen](../auswahl/hosting-auswaehlen.md)).

Der Bereich [Vereine](../vereine/README.md) behandelt die Arbeit mit Ehrenamtlichen und die Übergabe, wenn sie
wechseln.

## Personenbezogene Daten

Eine Mitgliederwebsite speichert Namen, E-Mail-Adressen, Anmeldeprotokolle und je nach Klassen mehr (zum Beispiel ein
Geburtsdatum mit dem Datentyp `birthday`). Erheben Sie nur, was Sie brauchen, sagen Sie im Registrierungsformular,
warum, und vereinbaren Sie die Aufbewahrung mit Ihrer Ansprechperson für den Datenschutz
([DSGVO-Grundlagen](../sicherheit/dsgvo-grundlagen.md),
[Personenbezogene Daten in Exponential](../sicherheit/personenbezogene-daten-in-exponential.md)). Diese Seite ist keine
Rechtsberatung.

## Was nicht eingebaut ist

Im Repository durchsucht am 19. Oktober 2026: Wiederkehrende Mitgliedsbeiträge, ein automatisches Ende der
Mitgliedschaft, Veranstaltungstickets mit Platzgrenzen und eine Seite mit einem Mitgliederverzeichnis sind keine
dokumentierten Funktionen. Ein einmaliger Beitrag lässt sich als Produkt über den eingebauten Shop verkaufen
([Shops](../shops/README.md)); der Rest sind Erweiterungsprojekte oder Integrationen, die Sie mit einem Partner planen
([Einen Partner auswählen](../auswahl/einen-partner-auswaehlen.md)).

## Arbeitsblatt zur Planung

- [ ] Die Gründe, aus denen sich Mitglieder anmelden.
- [ ] Öffentliche Inhalte und Inhalte nur für Mitglieder, Bereich für Bereich.
- [ ] Mitgliedschaftsstufen und ihre Gruppen.
- [ ] Wie Menschen beitreten und wer freigibt.
- [ ] Plan für Newsletter und Benachrichtigungen.
- [ ] Plan für Veranstaltungen.
- [ ] Verantwortliche Person, Redakteure, freigebende Person und Hoster benannt.
- [ ] Liste der personenbezogenen Daten und Aufbewahrung.
- [ ] Integrationen für Beiträge oder Tickets, falls nötig.

## Verwandte Seiten

- [Mitgliederwebsites](README.md)
- [Checkliste für Mitgliederwebsites](checkliste-fuer-mitgliederwebsites.md)
- [Website-Planung für kleine Gruppen](../vereine/website-planung-fuer-kleine-gruppen.md)
- [Ihre Website planen](../../../../eng-us/help/creators/website-creators/planning-your-website.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
