---
title: Dokumentations-Websites: hier beginnen
audience: customer, editor
category: customer-success
order: 40
summary: Für Produktdokumentation, Wissensdatenbanken und Handbücher mit Exponential: Struktur, Prüf- und Freigabezustände, Suche, Übersetzungen, Exporte als PDF und E-Book und die Routinen, die Dokumentation korrekt halten.
keywords: Dokumentations-Website, Wissensdatenbank, Handbuch, Produktdokumentation, Hilfe-Center, Dokumentationsseite, Prüfung, Freigabe, Suche, PDF, EPUB, Übersetzungen, documentation site, knowledge base, manual, product documentation, help center, documentation page, review, release, search, translations
type: help
---
# Dokumentations-Websites: hier beginnen

Dokumentation wird gelesen, wenn jemand sofort eine Antwort braucht. Eine Dokumentations-Website wird an zwei Dingen
gemessen: ob die Antwort in Sekunden zu finden ist und ob sie noch stimmt. Alles andere (das Design, die Zahl der
Seiten) kommt danach. Exponential passt gut zu Dokumentation, weil seine Inhalte strukturiert sind: Eine Seite hat
Felder, einen Platz in einem Baum, Versionen, Übersetzungen und Beziehungen, sodass ein Handbuch über Jahre wachsen kann,
ohne zu einem Haufen loser Seiten zu werden.

Diese Seite ist für die Dokumentationsleitung, die technischen Autoren und die Produktverantwortlichen, die für die
Dokumentation einstehen.

## Was Exponential einer Dokumentations-Website bietet

| Bedarf | Was ihn trägt | Wo es erklärt wird |
|---|---|---|
| Seiten in einer klaren Hierarchie | Der Inhaltsbaum und Container-Klassen; die Demo-Installation hat eine Klasse *Documentation page* | [Den Inhaltsbaum strukturieren](../../../../eng-us/help/creators/website-creators/structuring-the-content-tree.md) |
| Prüfung vor der Freigabe | Entwürfe, Freigabe-Workflows, Objektzustände | [Freigabe und Prüfung](../alltag/freigabe-und-pruefung.md), [Objektzustände](../../../anleitungen/objektzustaende.md) |
| Wissen, was eine Seite vorher gesagt hat | Versionskontrolle | [Veröffentlichen und Versionen](../../veroeffentlichen-und-versionen.md) |
| Antworten schnell finden | Die eingebaute Suchmaschine, oder Solr über eZ Find, wo installiert | [Website-Suche](../../../../eng-us/help/creators/website-creators/site-search.md), [Suche mit eZ Find und Solr](../../../../eng-us/help/creators/website-creators/search-with-ezfind-and-solr.md) |
| Dieselbe Seite in mehreren Sprachen | Übersetzungen jedes Elements | [Sprachen](../../sprachen.md), [Übersetzungsroutine](../alltag/uebersetzungsroutine.md) |
| Druckbare Handbücher | Der PDF-Export (**Setup > PDF export**) | [PDF-Export](../../../../eng-us/features/6.0/pdf-export.md) |
| Links, die nicht kaputtgehen | URL-Aliase und Weiterleitungen, Linkprüfungen | [URLs, Weiterleitungen und verschobene Seiten](../alltag/urls-weiterleitungen-und-verschobene-seiten.md) |

## Ein ausgearbeitetes Modell: die Hilfe, die Sie gerade lesen

Die Hilfe der Administration von Exponential ist selbst eine Dokumentations-Website, und sie zeigt die Muster. Seit
6.0.15 können Hilfethemen, Handbücher, Abbildungen und das Glossar **als Inhalte der Website** gepflegt werden: acht
Klassen in der Klassengruppe „Help“ (Handbuch, Kapitel, Konzept, Aufgabe, Referenz, Fehlerbehebung, Abbildung,
Glossareintrag), eine Gruppe von Objektzuständen `help_status` mit den Zuständen Draft, Review und Released, die Rollen
„Help editor“ und „Help release“ und ein Export eines Handbuchs als EPUB oder PDF (`doc/eng-us/features/6.0/help-content.md`).
Die Demo-Installation unter <https://alpha.se7enx.com> hat diese Klassen.

Sie müssen diese Klassen nicht für die Dokumentation Ihres eigenen Produkts verwenden, aber das Modell lohnt sich zur
Nachahmung:

- **Thementypen** (ein Konzept erklärt, eine Aufgabe gibt Schritte vor, eine Referenz listet Fakten, eine
  Fehlerbehebung geht von einem Symptom aus), damit Autoren wissen, wofür eine Seite da ist;
- **ein Freigabezustand, getrennt vom Veröffentlichen**, damit eine Seite für Prüfende sichtbar sein kann, bevor sie
  freigegeben ist;
- **zwei Rollen**: Personen, die schreiben, und Personen, die freigeben;
- **ein Glossar**, damit ein Begriff auf jeder Seite dasselbe bedeutet.

Fragen Sie Ihren Partner, ob die Klassen Ihrer Website einem solchen Modell folgen und was nötig wäre, wenn nicht
([Änderungsanfragen](../partner-und-hoster/aenderungsanfragen.md)).

## Dokumentation korrekt halten

- **Mit jedem Produkt-Release**: Listen Sie die Seiten auf, die das Release ändert, aktualisieren Sie sie als Entwürfe,
  lassen Sie sie prüfen und geben Sie sie am Release-Tag frei ([Zeitgesteuertes Veröffentlichen](../alltag/zeitgesteuertes-veroeffentlichen.md)).
- **Jeden Monat**: Sehen Sie sich an, wonach Leser suchen und was sie nicht finden
  ([Website-Suche und wonach Besucher suchen](../alltag/website-suche-und-wonach-besucher-suchen.md)): Jede
  erfolglose Suche ist eine fehlende Seite oder ein fehlendes Wort.
- **Jedes Quartal**: ein [Inhalts-Audit](../alltag/inhalts-audits.md): Seiten, die niemand liest, Seiten für
  Produkte, die es nicht mehr gibt, und kaputte Links.

> **Tipp**
> Löschen Sie nie eine Dokumentationsseite, die Menschen als Lesezeichen gespeichert haben könnten. Verschieben Sie sie,
> halten Sie ihre alte Adresse mit einer Weiterleitung funktionsfähig, oder ersetzen Sie ihren Text durch einen Verweis
> auf die Seite, die übernommen hat.

## Ihr nächster Schritt

Folgen Sie dem [Lesepfad für Dokumentations-Websites](lesepfad-dokumentations-websites.md). Dokumentation in mehreren
Sprachen sollte den [Lesepfad für mehrsprachige Websites](lesepfad-mehrsprachige-websites.md) ergänzen.

## Verwandte Seiten

- [Lesepfad für Dokumentations-Websites](lesepfad-dokumentations-websites.md)
- [Hilfe als Inhalt gepflegt](../../../../eng-us/features/6.0/help-content.md)
- [Beziehungen und Wiederverwendung](../../../../eng-us/help/creators/website-creators/relations-and-reuse.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
