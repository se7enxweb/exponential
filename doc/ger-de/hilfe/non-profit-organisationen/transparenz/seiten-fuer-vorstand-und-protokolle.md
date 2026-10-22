---
title: Seiten für Vorstand und Protokolle
audience: non-profit, editor, website-creator, government
category: non-profits
order: 40
summary: Vorstand, Organisationsführung und Sitzungen einer Non-Profit-Organisation in Exponential veröffentlichen - eine Vorstandsseite mit Zustimmung der Mitglieder, eine eigene Sitzungsklasse (es wird keine mitgeliefert) mit Tagesordnungen, Unterlagen und Protokollen darunter, eine Struktur pro Jahr, Vorstandsunterlagen nur für den Vorstand in einem Bereich mit eigener Rolle, zeitgesteuerte Veröffentlichung, Korrekturen und eine Checkliste.
keywords: Vorstand, Kuratorium, Geschäftsführung, Organisationsführung, Protokolle, Tagesordnungen, Sitzungen, Sitzungsklasse, Vorstandsunterlagen, Bereich nur für den Vorstand, Rolle, zeitgesteuerte Veröffentlichung, Korrekturen, Satzung, board, trustees, directors, governance, minutes, agendas, meetings, meeting class, board papers, bylaws
type: help
---
# Seiten für Vorstand und Protokolle

Diese Seite richtet sich an Schriftführer des Vorstands, Vorstandsmitglieder und die Website-Ersteller. Viele
Non-Profit-Organisationen veröffentlichen, wer im Vorstand sitzt und was er beschlossen hat; manche müssen es. Auch
Vorstandsunterlagen, die nicht öffentlich sind, profitieren von einem sicheren Ort im Netz für die
Vorstandsmitglieder. Exponential erledigt beides mit gewöhnlichen Inhalten, einem Bereich und einer Rolle.

> **Hinweis**
> Exponential liefert **keine Sitzungs- oder Vorstandsklasse** mit. Das Site-Design `ezwebin` enthält Artikel, Ordner,
> Dateien und Veranstaltungen; die unten beschriebene Sitzungsklasse legen Sie selbst an. Das Muster stammt aus
> [Sitzungsunterlagen, Tagesordnungen und Protokolle](../../verwaltung/transparenz-und-informationsfreiheit/sitzungsunterlagen-tagesordnungen-und-protokolle.md).

## Inhalt

1. [Die Vorstandsseite](#die-vorstandsseite)
2. [Eine Sitzungsklasse](#eine-sitzungsklasse)
3. [Struktur pro Jahr](#struktur-pro-jahr)
4. [Vor und nach einer Sitzung](#vor-und-nach-einer-sitzung)
5. [Unterlagen nur für den Vorstand](#unterlagen-nur-für-den-vorstand)
6. [Korrekturen](#korrekturen)
7. [Checkliste](#checkliste)

## Die Vorstandsseite

| Inhalt | Hinweis |
|---|---|
| Namen und Funktionen (Vorsitz, Schatzmeister, Schriftführer) | Mit Zustimmung jedes Mitglieds |
| Eine kurze Biografie | Optional; vom Mitglied geschrieben oder freigegeben |
| Foto | Nur mit Erlaubnis; Alternativtext mit dem Namen |
| Amtszeit | Beginn und Ende |
| Arbeitsweise des Vorstands | Sitzungen pro Jahr, Ausschüsse, Link zur Satzung |
| Richtlinie zu Interessenkonflikten | Als Datei oder Seite |
| Kontakt | Eine Funktionsadresse, keine privaten E-Mail-Adressen |

Verwenden Sie einen Artikel oder einen Ordner mit einem Artikel pro Mitglied. Veröffentlichen Sie niemals private
Telefonnummern oder Wohnadressen.

## Eine Sitzungsklasse

Legen Sie eine Containerklasse an (Bezeichner zum Beispiel `board_meeting`) mit Attributen wie:

| Attribut | Datentyp |
|---|---|
| Titel (mit dem Datum) | Textzeile |
| Datum und Uhrzeit | Datum und Uhrzeit |
| Status | Auswahl: Geplant, Tagesordnung veröffentlicht, Abgehalten, Protokoll genehmigt, Abgesagt |
| Tagesordnung | Rich Text oder eine Datei darunter |
| Zusammenfassung des Protokolls | Rich Text, nach der Sitzung |

Unterlagen (Tagesordnung, Berichte, Protokolle) sind Dateiobjekte unterhalb der Sitzung. Quelle und weitere Attribute:
[Sitzungsunterlagen, Tagesordnungen und Protokolle, Eine Sitzungsklasse](../../verwaltung/transparenz-und-informationsfreiheit/sitzungsunterlagen-tagesordnungen-und-protokolle.md#eine-sitzungsklasse).
Eine Klasse anlegen: [Inhaltsklassen für Websites](../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md).

Wenn Sie nur ein paarmal im Jahr Protokolle veröffentlichen, genügt ein Ordner pro Jahr mit Dateiobjekten; dann
brauchen Sie keine Klasse.

## Struktur pro Jahr

```text
Governance
  Board members
  Board meetings
    2026
      2026-03-12 Board meeting
        Agenda (file)
        Minutes (file, after approval)
    2025
  Board papers          <- board-only section
```

Alte Aliase leiten weiter, wenn Ordner umbenannt werden ([Weiterleitungen und verschobene Inhalte](../../ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md)).

## Vor und nach einer Sitzung

| Wann | Was | Wie |
|---|---|---|
| Vorher | Datum und Tagesordnung veröffentlichen, sofern öffentlich | Bei Bedarf zeitgesteuert veröffentlichen ([Zeitgesteuertes Veröffentlichen](../../kunden/alltag/zeitgesteuertes-veroeffentlichen.md)) |
| Nachher | Status auf Abgehalten setzen | Die Sitzung bearbeiten |
| Nach Genehmigung des Protokolls | Das genehmigte Protokoll hochladen, Status setzen | Dateiobjekt unterhalb der Sitzung |

Veröffentlichen Sie nur genehmigte Protokolle. Protokollentwürfe gehören in den Bereich nur für den Vorstand.

## Unterlagen nur für den Vorstand

1. Legen Sie einen Bereich „Board“ an und weisen Sie ihm den Ordner mit den Vorstandsunterlagen zu.
2. Legen Sie eine Rolle „Board member“ mit `content/read`, eingeschränkt auf den Bereich Board, an und weisen Sie sie
   einer Benutzergruppe „Board“ zu.
3. Geben Sie jedem Vorstandsmitglied ein persönliches Konto in dieser Gruppe, mit Zwei-Faktor-Anmeldung
   ([Zwei-Faktor-Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)).
4. Dateien in dem Bereich werden über `content/download` heruntergeladen, das die Berechtigung prüft
   ([Dateien und Downloads, Geschützte Downloads](../../ersteller/website-ersteller/dateien-und-downloads.md#geschützte-downloads)).

Anleitung: [Bereiche und Objektzustände](../../ersteller/website-ersteller/sektionen-und-objektzustaende.md). Wenn die
Amtszeit eines Vorstandsmitglieds endet, entfernen Sie es in derselben Woche aus der Gruppe
([Wenn Ehrenamtliche gehen](../betrieb/wenn-ehrenamtliche-gehen.md)).

## Korrekturen

Wird ein genehmigtes Protokoll korrigiert, veröffentlichen Sie die Korrektur sichtbar. Die frühere Version bleibt im
Versionsverlauf erhalten ([Versionen und das Audit-Protokoll](versionen-und-das-audit-protokoll.md)).

## Checkliste

- [ ] Die Vorstandsmitglieder haben dem zugestimmt, was über sie veröffentlicht wird.
- [ ] Sitzungsstruktur pro Jahr; Klasse nur bei Bedarf angelegt.
- [ ] Nur genehmigte Protokolle öffentlich; Entwürfe im Bereich nur für den Vorstand.
- [ ] Vorstandsrolle auf den Bereich Board eingeschränkt; persönliche Konten mit Zwei-Faktor-Anmeldung.
- [ ] Ausscheidende Mitglieder aus der Gruppe entfernt.
- [ ] Satzung und Richtlinien aktuell.

## Verwandte Seiten

- [Transparenz für Non-Profit-Organisationen](README.md)
- [Beschlussregister](../../verwaltung/transparenz-und-informationsfreiheit/beschlussregister.md)
- [Inhalte nur für Mitglieder](../../unternehmen/mitglieder/inhalte-nur-fuer-mitglieder.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
