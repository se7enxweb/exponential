---
title: Unternehmenseinträge außerhalb von Exponential
audience: local-business, businesses, customer
category: local-business
order: 26
summary: Ihr Profil auf Karten- und Suchplattformen, Bewertungsseiten und in Verzeichnissen wird auf diesen Plattformen angelegt und aktuell gehalten, nicht in Exponential - was dort hineingehört, wie Sie es im Einklang mit der Website halten, wem die Konten gehören sollten, und eine jährliche Routine.
keywords: Unternehmenseintrag, Unternehmensprofil, Kartenplattform, Verzeichnis, Rezensionen, lokale Suche, Name Adresse Telefon, Öffnungszeiten, externer Dienst, Google Business Profile, business listing, business profile, directory, local search
type: help
---
# Unternehmenseinträge außerhalb von Exponential

Diese Seite ist für Inhaber eines lokalen Unternehmens. Bei vielen lokalen Suchen sehen Menschen Ihren **Eintrag**
auf einer Karten- oder Suchplattform, bevor sie Ihre Website sehen: Name, Adresse, Öffnungszeiten, Fotos, Rezensionen
und einen Link zur Website. Dieser Eintrag ist **nicht** Teil von Exponential. Sie legen ihn auf der Plattform selbst
an und halten ihn dort aktuell.

> **Hinweis**
> Keine Erweiterung in `composer.json` oder `extension/` verbindet Exponential mit dem Unternehmensprofil einer
> Karten- oder Suchplattform (gesucht am 20. Oktober 2026). Nichts auf dieser Seite wird von der Website aus
> automatisiert.

## Inhalt

1. [Was wo liegt](#was-wo-liegt)
2. [Was in einen Eintrag gehört](#was-in-einen-eintrag-gehört)
3. [Einträge und Website im Einklang halten](#einträge-und-website-im-einklang-halten)
4. [Wem die Konten gehören](#wem-die-konten-gehören)
5. [Rezensionen](#rezensionen)
6. [Eine Routine](#eine-routine)

## Was wo liegt

| Information | Website (Exponential) | Eintrag auf Plattformen |
|---|---|---|
| Name, Adresse, Telefon | ja, ein gemeinsames Objekt | ja, von Ihnen eingetippt |
| Öffnungszeiten, Feiertagszeiten | ja | ja, von Ihnen getrennt eingetippt |
| Fotos | ja | ja, getrennt hochgeladen |
| Leistungen, Speisekarte, Preise | alle Einzelheiten | eine Zusammenfassung oder ein Link zur Website |
| Rezensionen | nur Sternebewertungen, mit `ezstarrating` | geschriebene Rezensionen von Kunden |
| Schaltfläche für Buchung oder Bestellung | ein Link zu Ihrem Buchungsdienst | ein Link, auf der Plattform gesetzt |

## Was in einen Eintrag gehört

- Der Name des Unternehmens genau wie auf Ihrem Schild und Ihrer Website.
- Dieselbe Adresse und dasselbe Telefonformat wie auf der Website ([Grundlagen der lokalen Suchmaschinenoptimierung](grundlagen-der-lokalen-suchmaschinenoptimierung.md#überall-derselbe-name-dieselbe-adresse-und-dieselbe-telefonnummer)).
- Kategorie: die Branche, die am besten passt.
- Reguläre Öffnungszeiten und, rechtzeitig vorher, Feiertagszeiten.
- Ein Link zur Startseite der Website oder zur passenden Seite (Speisekarte, Buchung).
- Echte Fotos: die Fassade des Geschäfts (damit man es wiedererkennt), innen, Ihre Arbeit.

## Einträge und Website im Einklang halten

Der häufigste Fehler ist, die Öffnungszeiten auf der Website zu ändern und den Eintrag zu vergessen, oder umgekehrt.
Machen Sie eine Aufgabe daraus:

| Wann | Ändern auf der Website | Ändern auf jeder Plattform |
|---|---|---|
| Reguläre Öffnungszeiten ändern sich | Objekt mit den Öffnungszeiten | reguläre Öffnungszeiten |
| Feiertag | Feiertagshinweis ([Feiertags- und Saisonöffnungszeiten](../karten-und-oeffnungszeiten/feiertags-und-saisonoeffnungszeiten.md)) | Sonderöffnungszeiten |
| Telefonnummer ändert sich | Kontaktobjekt | Telefon |
| Umzug in neue Räume | Adresse, Kartenfeld, Anfahrt | Adresse (die meisten Plattformen verlangen eine Bestätigung) |
| Endgültige Schließung | eine Hinweisseite | als geschlossen markieren |

Schreiben Sie die Liste der Plattformen, die Sie nutzen, in das [Website-Briefing](../vorlagen/website-briefing-fuer-ein-lokales-unternehmen.md).

## Wem die Konten gehören

Der Eintrag spricht für Ihr Unternehmen. Das Konto sollte:

- auf den Namen des Unternehmens laufen, mit einer geschäftlichen E-Mail-Adresse, die bleibt, wenn Mitarbeitende
  gehen;
- mit einem Partner als **Manager** geteilt werden, nicht übergeben;
- auf der Liste der Konten stehen, die Sie führen ([Mit einem lokalen Partner oder Freiberufler arbeiten](../einstieg/mit-einem-lokalen-partner-oder-freiberufler-arbeiten.md#was-sie-selbst-behalten)).

## Rezensionen

Geschriebene Kundenrezensionen stehen meist auf den Plattformen. Exponential hat Sternebewertungen mit
`ezstarrating`, aber kein Modul für geschriebene Rezensionen ([Bewertungen und Rezensionen](../kontakt-und-formulare/bewertungen-und-rezensionen.md)).
Antworten Sie auf Rezensionen auf der Plattform, höflich und ohne persönliche Angaben über den Kunden. Kopieren Sie
Rezensionen von Kunden nicht ungefragt auf Ihre Website.

## Eine Routine

| Wie oft | Aufgabe |
|---|---|
| Jede Woche | neue Rezensionen und Fragen auf den Plattformen lesen |
| Vor jedem Feiertag | Sonderöffnungszeiten auf jeder Plattform und auf der Website eintragen |
| Jedes Quartal | Name, Adresse, Telefon, Öffnungszeiten und Link auf jeder Plattform mit der Website vergleichen |
| Jedes Jahr | alte Fotos ersetzen; prüfen, wer Zugriff auf jedes Konto hat |

## Verwandte Seiten

- [Gefunden werden](README.md)
- [Was einen externen Dienst braucht](../budget/was-einen-externen-dienst-braucht.md)
- [Saisonale Änderungen](../im-betrieb/saisonale-aenderungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
