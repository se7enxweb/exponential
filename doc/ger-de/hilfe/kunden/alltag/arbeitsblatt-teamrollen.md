---
title: Arbeitsblatt für Teamrollen
audience: customer, admin, project-manager
category: customer-success
order: 4
summary: Eine Vorlage für ein Arbeitsblatt, in dem Website-Betreiber jede Aufgabe im Team festhalten, die Rolle und Gruppe, der sie entspricht, ihre Einschränkungen, wer sie innehat und wann sie zuletzt überprüft wurde - zur Übergabe an den Administrator und zum Aufbewahren für die jährliche Zugriffsüberprüfung.
keywords: Arbeitsblatt, Vorlage, Rollen, Team, Berechtigungen, Zugriffsüberprüfung, Gruppen, Einschränkungen, Verantwortlichkeiten, RACI, worksheet, template, roles, team, permissions, access review, groups, limitations, responsibilities
type: help
---
# Arbeitsblatt für Teamrollen

Diese Seite ist eine **Vorlage** für den Betreiber oder die Leitung. Füllen Sie sie einmal mit Ihrem Administrator aus,
bewahren Sie sie bei den anderen Unterlagen Ihrer Website auf und aktualisieren Sie sie, wenn jemand dazukommt, die
Aufgabe wechselt oder geht. Sie übersetzt „wer macht was“ in die Rollen und Gruppen, die Exponential verwendet
([Rollen und Richtlinien](../../../anleitungen/rollen-und-richtlinien.md)), sodass der Administrator prüfen kann, ob die Website
dem entspricht.

## Inhalt

1. [Wie Sie es verwenden](#wie-sie-es-verwenden)
2. [Teil 1: Aufgaben und Rollen](#teil-1-aufgaben-und-rollen)
3. [Teil 2: Personen](#teil-2-personen)
4. [Teil 3: heikle Berechtigungen](#teil-3-heikle-berechtigungen)
5. [Teil 4: Verantwortlichkeiten](#teil-4-verantwortlichkeiten)

## Wie Sie es verwenden

1. Füllen Sie Teil 1 mit den Aufgaben in Ihrem Team aus, nicht mit Namen.
2. Bitten Sie den Administrator, neben jede Aufgabe die Rolle, Gruppe und Einschränkungen zu schreiben, die heute auf
   der Website bestehen. Öffnen Sie gemeinsam **Benutzer > Rollen und Richtlinien**: Jede Rollenseite listet ihre
   Richtlinien als Sätze wie *Darf Inhalte lesen, in der Sektion Standard, der Klassen Article und Folder*.
3. Füllen Sie Teil 2 anhand der Personalliste aus. Vergleichen Sie mit **Benutzer > Benutzerkonten prüfen**
   ([Benutzerkonten prüfen](../../../../eng-us/features/6.0/user-hygiene.md)).
4. Markieren Sie jede Abweichung und vereinbaren Sie, wer sie behebt.
5. Überprüfen Sie das Blatt einmal im Jahr ([Zugriffsüberprüfungen](../../unternehmen/sicherheit/zugriffspruefungen.md)).

## Teil 1: Aufgaben und Rollen

| Aufgabe | Was sie auf der Website tun | Rolle(n) | Gruppe | Eingeschränkt auf (Teilbaum, Sektion, Sprache) | Hinter einer Freigabe? |
|---|---|---|---|---|---|
| Autor | | | | | |
| Redakteur | | | | | |
| Freigebende Person | | | | | |
| Layout-Redakteur | | | | | |
| Übersetzer | | | | | |
| Formularbearbeitung | | | | | |
| Shop-Personal | | | | | |
| Kontenverwaltung | | | | | |
| Administrator | | Administrator | | keine | nein |

## Teil 2: Personen

| Person (oder externe Firma) | Aufgabe(n) aus Teil 1 | Startdatum | Enddatum | Zweistufige Anmeldung an? | Zuletzt geprüft |
|---|---|---|---|---|---|
| | | | | | |
| | | | | | |
| | | | | | |
| | | | | | |

> **Hinweis**
> Verwenden Sie in gemeinsamen Dokumenten Rollen und Aufgabenbezeichnungen, keine persönlichen Angaben, die Sie nicht
> brauchen. Ein gemeinsames Blatt, in dem nur „Redakteur, Marketing“ steht, altert besser als eines mit Namen,
> Telefonnummern und Privatadressen.

## Teil 3: heikle Berechtigungen

Halten Sie fest, wer jede dieser Berechtigungen hat. Je kürzer jede Liste, desto besser
([Zugriffsüberprüfungen](../../unternehmen/sicherheit/zugriffspruefungen.md)).

| Berechtigung | Wer sie hat | Noch nötig? |
|---|---|---|
| Vollzugriff (Administrator) | | |
| Kann Rollen ändern (Modul `role`) | | |
| Liest Formulareinsendungen (`infocollector/read`) | | |
| Kontenüberprüfung (`user/hygiene`) | | |
| Prüfprotokoll (`audit/read`, `audit/manage`) | | |
| Apps verbinden und API-Schlüssel (`apikey/create`) | | |
| Schreibassistent (`expai/write`) | | |

## Teil 4: Verantwortlichkeiten

| Aufgabe | Verantwortlich | Genehmigt | Informiert |
|---|---|---|---|
| Konten anlegen und deaktivieren | | | |
| Rollen ändern | | Website-Betreiber | |
| Inhalte freigeben | | | |
| Fragen der Redaktion beantworten | | | |
| Mit dem Hoster oder Partner sprechen | | | |

```text
Worksheet completed on: __________   by: ______________   Next review: __________
```

## Verwandte Seiten

- [Rollen für Ihr Team](rollen-fuer-ihr-team.md)
- [Rollen für neue Mitarbeitende wählen](rollen-fuer-neue-mitarbeitende-waehlen.md)
- [Checkliste für die Einarbeitung](checkliste-einarbeitung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
