---
title: Beschlussregister
audience: government, editor, website-creator
category: public-sector
order: 130
summary: Wie Sie in Exponential ein öffentliches Register der Beschlüsse (Ratsbeschlüsse, übertragene Entscheidungen, Entscheidungen der Verwaltungsspitze) als Inhalt führen, mit stabilen Aktenzeichen, Links zu Sitzungen und Dokumenten, Zuständen für den Lebenslauf eines Beschlusses und einem Export als offene Daten.
keywords: Beschlussregister, Beschlüsse, Ratsbeschlüsse, übertragene Entscheidungen, Entscheidungen, Register, Objektzustände, Aktenzeichen, Transparenz, Rechenschaft, offene Daten, decision register, resolutions, delegated decisions, executive decisions, object states, references, transparency, accountability, open data
type: help
---
# Beschlussregister

Diese Seite richtet sich an Sitzungsdienste und Websiteersteller, die die Beschlüsse einer öffentlichen Stelle
veröffentlichen. Ein als Inhalt geführtes Register gibt jedem Beschluss eine stabile Adresse, eine Versionsgeschichte
und ein Prüfprotokoll und kann als Datensatz exportiert werden.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Die Beschlussklasse](#die-beschlussklasse)
2. [Der Lebenslauf eines Beschlusses mit Objektzuständen](#der-lebenslauf-eines-beschlusses-mit-objektzuständen)
3. [Links zu Sitzungen und Dokumenten](#links-zu-sitzungen-und-dokumenten)
4. [Die Registerseite](#die-registerseite)
5. [Export als offene Daten](#export-als-offene-daten)
6. [Checkliste](#checkliste)

## Die Beschlussklasse

| Attribut | Datentyp | Hinweis |
|---|---|---|
| Aktenzeichen | Textzeile | Ihre Nummerierung, nie wiederverwendet |
| Titel | Textzeile | was beschlossen wurde, in einfacher Sprache |
| Beschlossen von | Auswahl | Rat, ein Ausschuss, eine Funktion der Verwaltung |
| Datum des Beschlusses | Datum | |
| Beschlusstext | Formatierter Text | der Beschluss, wie er gefasst wurde |
| Begründung | Formatierter Text | |
| Sitzung | Objektbeziehung | das Sitzungsobjekt, wenn in einer Sitzung beschlossen |
| Dokumente | Objekt Relations Liste | Berichte, Anhörungen |
| Thema | Tags oder Auswahl | Thema |

Muster für den Objektnamen `<reference> <title>`. Legen Sie Beschlüsse in einen Ordner pro Jahr.

## Der Lebenslauf eines Beschlusses mit Objektzuständen

Objektzustände sind Kennzeichnungen, die jedes Objekt trägt, eine aus jeder Zustandsgruppe; Rollen können sie verwenden
([Objektzustände](../../../anleitungen/objektzustaende.md)). Legen Sie unter **Setup > Zustände** eine Gruppe
„Beschlussstatus“ an, mit Zuständen wie *Gefasst*, *Zur Überprüfung gezogen*, *In Kraft*, *Aufgehoben*, *Ersetzt*.

- Zeigen Sie den Zustand mit Ihrem Template auf der Seite an.
- Beschränken Sie `state/assign` mit der Beschränkung `NewState`, damit nur das Team des Sitzungsdienstes ihn ändert.
- Eine Änderung des Zustands wird im Prüfprotokoll aufgezeichnet ([Prüfprotokoll](../../../funktionen/6.0/pruefprotokoll.md)).

Entfernen Sie einen aufgehobenen Beschluss nie; setzen Sie seinen Zustand und verlinken Sie den Beschluss, der ihn
aufgehoben hat.

## Links zu Sitzungen und Dokumenten

Objektrelationen zeigen auf Objekte, nicht auf Adressen, und funktionieren deshalb weiter, wenn Sitzungen oder
Dokumente verschoben werden ([Relationen und Wiederverwendung](../../ersteller/website-ersteller/relationen-und-wiederverwendung.md)).
**Ungültige Relationen** unter Setup (`/contentcheck/brokenrelations`) listet Relationen zu Inhalten auf, die entfernt
wurden.

## Die Registerseite

Die Jahresordner listen die Beschlüsse mit den neuesten zuerst auf; eine Zeilenansicht zeigt Aktenzeichen, Titel,
Datum und Zustand. Die Suche der Website findet Beschlüsse über ihren Text, wenn die Felder **Durchsuchbar** sind
([Suche auf der Website](../../ersteller/website-ersteller/suche-auf-der-website.md)).

## Export als offene Daten

Exportieren Sie die Klasse mit `xrowextract` als CSV oder JSON (eine Zeile pro Beschluss) und veröffentlichen Sie die
Datei als Datensatz; oder lassen Sie Nachnutzer das Register über die REST-API lesen (siehe den Bereich offene Daten).

## Checkliste

- [ ] Aktenzeichen sind eindeutig und werden nie wiederverwendet.
- [ ] Der Beschlusstext ist der Text, wie er gefasst wurde; Berichtigungen sind datierte Hinweise, keine
  stillschweigenden Änderungen.
- [ ] Zustände werden nur vom Team des Sitzungsdienstes gesetzt.
- [ ] Aufgehobene Beschlüsse bleiben bestehen, mit einem Link auf das, was sie ersetzt hat.

## Verwandte Seiten

- [Sitzungsunterlagen, Tagesordnungen und Protokolle](sitzungsunterlagen-tagesordnungen-und-protokolle.md)
- [Sektionen und Objektzustände für Websiteersteller](../../ersteller/website-ersteller/sektionen-und-objektzustaende.md)
- [Versionsgeschichte als Rechenschaft](versionsgeschichte-als-rechenschaft.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
