---
title: Redaktionskalender
audience: customer, editor, project-manager
category: customer-success
order: 9
summary: Wie ein Website-Betreiber plant, was wann auf die Website kommt - ein Kalender außerhalb der Website, verknüpft mit Entwürfen, Freigaben, zeitgesteuertem Veröffentlichen, Layouts mit einer Zeitregel und Newslettern in der Website - mit einem wöchentlichen Planungsrhythmus.
keywords: Redaktionskalender, Redaktionsplan, Planung, Veröffentlichungsplan, Kampagne, Entwürfe, Freigaben, zeitgesteuertes Veröffentlichen, Newsletter, Redaktionssitzung, content calendar, editorial calendar, planning, publishing schedule, campaign, drafts, approvals, scheduled publishing, editorial meeting
type: help
---
# Redaktionskalender

Diese Seite ist für den Betreiber, die Chefredaktion und die Marketingleitung, die planen, was auf der Website
erscheint. Exponential hat keine eigene Kalenderansicht für geplante Inhalte; es hat die Werkzeuge, die ein Plan
braucht: Entwürfe, die nur ihr Ersteller sieht, Freigaben, Veröffentlichen zu einem Datum durch einen Workflow,
Layouts, die sich nach Datum ein- und ausschalten, und Newsletter mit geplantem und wiederkehrendem Versand. Führen Sie
den Plan in einem gemeinsamen Kalender oder einer Tabelle (beginnen Sie mit der
[Vorlage für den Redaktionskalender](vorlage-redaktionskalender.md)) und verknüpfen Sie jede Zeile mit dem Element auf
der Website.

## Inhalt

1. [Was in den Kalender gehört](#was-in-den-kalender-gehört)
2. [Wie jede Zeile auf der Website Wirklichkeit wird](#wie-jede-zeile-auf-der-website-wirklichkeit-wird)
3. [Ein wöchentlicher Planungsrhythmus](#ein-wöchentlicher-planungsrhythmus)
4. [Kampagnenzeiträume](#kampagnenzeiträume)
5. [Ehrlich bleiben](#ehrlich-bleiben)

## Was in den Kalender gehört

| Spalte | Warum |
|---|---|
| Datum und Uhrzeit der Veröffentlichung | wann Besucher es sehen sollen |
| Titel und Typ (Artikel, Landingpage, Newsletter-Ausgabe, Produkt) | was erstellt wird |
| Ort im Baum | damit Rollen und Freigaben wie erwartet greifen |
| Zuständig (eine Funktion, nicht nur ein Name) | wer es schreibt |
| Freigabe | wer es abnimmt, wenn Ihre Website Freigaben verwendet |
| Sprachen | welche Übersetzungen am selben Tag fertig sein müssen |
| Status | Idee, in Arbeit, in Prüfung, geplant, veröffentlicht |
| Link | die Adresse oder Knotennummer des Elements in der Administration |

Verwenden Sie für den Link die Knotennummer oder die Adresse. Jede Person im Team kann sie mit der Suche öffnen
([Ein Element über seine Nummer oder Adresse öffnen](../../suchen.md)).

## Wie jede Zeile auf der Website Wirklichkeit wird

| Status im Kalender | In Exponential | Hilfe |
|---|---|---|
| In Arbeit | ein **Entwurf**; nur sein Ersteller sieht ihn, unter *Meine Entwürfe* | [Meine Entwürfe und meine ausstehenden Elemente](../../../anleitungen/entwuerfe-und-ausstehende-objekte.md) |
| In Prüfung | eine **ausstehende** Version, von einer Freigabe zurückgehalten | [Freigabe und Prüfung](freigabe-und-pruefung.md) |
| Geplant | eine Version, die ein Workflow *Bis Datum warten* bis zu ihrem Datum zurückhält, wenn Ihre Website einen hat | [Zeitgesteuertes Veröffentlichen](zeitgesteuertes-veroeffentlichen.md) |
| Veröffentlicht | die veröffentlichte Version | [Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md) |
| Landingpage für einen Zeitraum | ein Layout, zugeordnet durch eine Regel mit einer Bedingung `time` | [Landingpages mit Layouts](../wachstum/landingpages-mit-layouts.md) |
| Newsletter | eine Ausgabe mit geplantem oder wiederkehrendem Versand | [Newsletter-Routine](../wachstum/newsletter-routine.md) |

> **Hinweis**
> Entwürfe sind persönlich. Ein Kollege kann den Entwurf eines anderen nicht öffnen, um ihn fertigzustellen; er
> erstellt auf der Versionsseite einen neuen Entwurf aus einer Version ([Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md)).
> Planen Sie Übergaben vor dem Urlaub.

## Ein wöchentlicher Planungsrhythmus

| Wann | Was | Wer |
|---|---|---|
| Montag | die nächsten zwei Wochen ansehen; jede Zeile hat eine zuständige und eine freigebende Person | Chefredaktion |
| Täglich | Status aktualisieren; Zeilen verschieben, deren Entwurf sich verspätet | jede zuständige Person |
| Vor einem Veröffentlichungstag | *Meine Warteliste* der freigebenden Personen prüfen: Nichts wartet, was erscheinen sollte | freigebende Personen |
| Freitag | den Kalender mit dem vergleichen, was diese Woche veröffentlicht wurde (die neuesten Inhalte im Dashboard) | Chefredaktion |
| Monatlich | lesen, wonach Besucher gesucht und was sie nicht gefunden haben; Themen ergänzen ([Website-Suche und wonach Besucher suchen](website-suche-und-wonach-besucher-suchen.md)) | Betreiber |

## Kampagnenzeiträume

Eine Kampagne bedeutet oft eine Landingpage, einen Newsletter, einige Artikel und vielleicht Übersetzungen, alles an
einem Tag:

1. Tragen Sie jeden Teil mit demselben Datum in den Kalender ein.
2. Bauen Sie die Landingpage als Layout und ordnen Sie es mit einer Regel zu, deren Bedingung `time` die Werte `from`
   und `to` hat, mit höherer Priorität als die normale Regel der Seite ([Layout-Regeln](../../../../eng-us/help/creators/website-creators/layout-rules.md)).
3. Planen Sie die Newsletter-Ausgabe, und schicken Sie sie zuerst an eine Testgruppe
   ([Ausgaben schreiben und versenden](../../unternehmen/newsletter/ausgaben-schreiben-und-versenden.md)).
4. Verwenden Sie am Tag davor die [Checkliste für Kampagnen-Landingpages](../wachstum/checkliste-kampagnen-landingpages.md).

## Ehrlich bleiben

- Ein Kalender, den niemand aktualisiert, ist schlechter als gar keiner. Beschränken Sie ihn auf die obigen Spalten.
- Tragen Sie die freigebende Person in die Zeile ein, nicht nur den Autor: Bei Freigaben verrutschen Termine.
- Notieren Sie, wer abwesend sein wird. Entwürfe lassen sich nicht stillschweigend übernehmen.
- Schreiben Sie nach einer Kampagne eine Zeile dazu, was funktioniert hat; lesen Sie sie vor der nächsten.

## Verwandte Seiten

- [Vorlage für den Redaktionskalender](vorlage-redaktionskalender.md)
- [Wöchentliche Routine des Betreibers](woechentliche-routine-des-betreibers.md)
- [Freigabe und Prüfung](freigabe-und-pruefung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
