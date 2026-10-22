---
title: "Suchergebnisse gestalten"
audience: theme-designer
category: theme-design
order: 20
summary: Wie Theme-Designer die Suchseite einer Exponential-Website gestalten - das Formular, die Zahl der Treffer, ein Ergebnis pro Klasse über Ansichtsmodi, keine Ergebnisse, lange Suchanfragen, Seitennavigation, die erweiterte Suche und Filter, wenn die Website ezfind mit Solr verwendet.
keywords: Theme-Designer, Suche, Suchergebnisse, Suchformular, keine Ergebnisse, Trefferzahl, Zeilenansicht, erweiterte Suche, ezfind, Solr, Facetten, Filter, theme designer, search, search results, search form, no results, result count, line view, advanced search, facets, filters
type: help
---
# Suchergebnisse gestalten

Diese Seite ist für Theme-Designer, die die Suchseite gestalten. Die Suche ist eine Modulansicht, ihre Seite wird also
innerhalb Ihres Rahmens von einem Such-Template gezeichnet statt von einem Inhalts-Template. Die Ergebnisse sind
Inhaltsobjekte vieler Klassen, eines nach dem anderen in einem Ansichtsmodus gezeichnet. Das Template, seine Variablen
und die Formularfelder, die bleiben müssen, sind in [Das Template für Suchergebnisse](../designer/vorlagen/suchergebnis-template.md)
dokumentiert; diese Seite behandelt die Designentscheidungen.

## Inhalt

1. [Die Teile der Seite](#die-teile-der-seite)
2. [Das Formular](#das-formular)
3. [Ein Ergebnis, viele Klassen](#ein-ergebnis-viele-klassen)
4. [Zahlen und Rückmeldung](#zahlen-und-rückmeldung)
5. [Keine Ergebnisse](#keine-ergebnisse)
6. [Seitennavigation](#seitennavigation)
7. [Erweiterte Suche](#erweiterte-suche)
8. [Filter mit ezfind](#filter-mit-ezfind)
9. [Prüfungen](#prüfungen)

## Die Teile der Seite

| Teil | Hinweise |
|---|---|
| Überschrift | "Suche" oder "Ergebnisse für ..." |
| Formular | die Suchanfrage, nach dem Suchen im Feld behalten |
| Trefferzahl | Screenreadern angesagt, wenn sie sich ändert |
| Ergebnisse | eine geordnete Liste, ein Ansichtsmodus pro Treffer |
| Seitennavigation | mit der Suchanfrage in den Seitenlinks |
| Keine Ergebnisse | Hilfe, keine Sackgasse |

Die Suchseite des Kernels ist `content/search.tpl`, die Ihr Design überschreibt; die Seite der erweiterten Suche ist
`content/advancedsearch.tpl`.

## Das Formular

Das Suchfeld kann auch im Kopfbereich stehen ([Kopf- und Fußbereiche gestalten](kopf-und-fussbereiche-gestalten.md)). Zeigen
Sie auf der Suchseite das vollständige Formular mit einer sichtbaren Beschriftung, der Suchanfrage schon im Feld und
einer Schaltfläche mit Textbeschriftung. Die Feldnamen des Formulars (`SearchText` und andere) müssen bleiben, wie das
Modul sie erwartet; das Design ändert nur Markup und Stile.

## Ein Ergebnis, viele Klassen

Jeder Treffer wird mit einem Ansichtsmodus gezeichnet (meist `line`), sodass ein Artikel, eine Datei und eine
Veranstaltung jeweils wie sie selbst aussehen können. Gestalten Sie ein gemeinsames Gerüst, damit sich die Liste
weiterhin als eine Liste liest ([Inhaltslisten und Karten gestalten](inhaltslisten-und-karten-gestalten.md#gemischte-klassen-in-einer-liste)):

- den Titel als Link;
- ein Typ-Label (Artikel, PDF, Veranstaltung), wo es bei der Auswahl hilft;
- ein Datum, wo es wichtig ist;
- einen kurzen Auszug oder die Einleitung.

Soll sich die Suchzeile von der Ordnerzeile unterscheiden, ist ein eigener Ansichtsmodus für Suchtreffer sauberer als
Bedingungen innerhalb von `line` ([Ansichtsmodi gestalten](ansichtsmodi-gestalten.md#einen-eigenen-modus-ergänzen)).

## Zahlen und Rückmeldung

Zeigen Sie, wie viele Ergebnisse gefunden wurden und wofür: "24 Ergebnisse für 'Öffnungszeiten'". Die Suchanfrage ist
eine Eingabe des Besuchers: Das Template muss sie überall waschen, wo sie ausgegeben wird. Lange Suchanfragen müssen
umbrechen und dürfen nicht aus der Überschrift herausragen.

## Keine Ergebnisse

Das leere Ergebnis ist auf vielen Websites die häufigste Suchseite. Gestalten Sie es als Hilfe:

- wiederholen Sie die Suchanfrage und sagen Sie klar, dass nichts gefunden wurde;
- schlagen Sie vor, die Schreibweise zu prüfen, weniger Wörter zu verwenden oder von den Hauptbereichen aus zu stöbern;
- bieten Sie das Suchfeld erneut an, mit der Suchanfrage darin;
- verlinken Sie optional auf die Startseite und die meistbesuchten Bereiche.

Siehe auch [Leere Zustände und Fehlerzustände gestalten](leere-zustaende-und-fehlerzustaende-gestalten.md).

## Seitennavigation

Suchergebnisse werden mit den mitgelieferten Navigator-Templates auf Seiten verteilt; die Suchanfrage wandert in den
Seitenlinks mit. Gestalten Sie die Seitennavigation wie bei anderen Listen
([Inhaltslisten und Karten gestalten](inhaltslisten-und-karten-gestalten.md#seitennavigation)) und zeigen Sie
"Seite 2 von 5", damit Menschen wissen, wo sie sind.

## Erweiterte Suche

`content/advancedsearch.tpl` bietet weitere Felder (zum Beispiel Phrase und Klasse). Viele Websites verlinken sie gar
nicht; entscheiden Sie mit dem Kunden, ob sie gebraucht wird, bevor Sie sie gestalten.

## Filter mit ezfind

Verwendet eine Installation die Erweiterung ezfind mit einem Solr-Server, beantwortet Solr die Suche, und das
Such-Template von ezfind kann **Facetten** zeigen, zum Beispiel die Zahl der Ergebnisse pro Inhaltsklasse. Gestalten
Sie Filter erst, wenn der Entwickler bestätigt, welche Suchmaschine die Website verwendet und welche Facetten sie
anbieten wird; die Standardsuche des Kernels hat keine ([Suche: Legacy und Solr](../../../eng-us/help/dxp-developers/search-legacy-and-solr.md)
behandelt die DXP-Seite, [Suchen](../suchen.md) die Sicht des Besuchers).

Hinweise zum Gestalten von Filtern:

- zeigen Sie die Zahl pro Option und die aktiven Filter mit einer Möglichkeit, jeden zu entfernen;
- einen Filter, der null Ergebnisse ergäbe, verbergen Sie besser oder deaktivieren ihn mit einer Erklärung;
- auf kleinen Bildschirmen kommen Filter in ein Panel, das eine Schaltfläche öffnet, die anzeigt, wie viele aktiv sind.

## Prüfungen

- [ ] Ergebnisse, keine Ergebnisse, ein Ergebnis und viele Ergebnisse gezeichnet.
- [ ] Ergebnisse jeder Klasse, die gefunden werden kann, sehen wie eine Liste aus.
- [ ] Lange Suchanfragen brechen um; die Suchanfrage steht im Feld.
- [ ] Die Zahl wird angesagt (`role="status"`), und die Überschriften ergeben für Screenreader Sinn.
- [ ] Hell und dunkel, drei Breiten, nur mit der Tastatur.

## Verwandte Seiten

- [Das Template für Suchergebnisse](../designer/vorlagen/suchergebnis-template.md)
- [Zeilenansicht](../designer/vorlagen/zeilenansicht.md)
- [Seitennavigation](../designer/vorlagen/seitenumbruch.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
