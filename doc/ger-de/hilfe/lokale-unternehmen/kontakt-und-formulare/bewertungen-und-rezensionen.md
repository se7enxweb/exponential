---
title: Bewertungen und Rezensionen
audience: local-business, businesses, customer
category: local-business
order: 54
summary: Was Exponential einem lokalen Unternehmen für Kundenmeinungen bietet - Sternebewertungen mit ezstarrating (1 bis 5 Sterne pro Objekt, eine Stimme pro Sitzung, kein geschriebener Text), Kommentare mit der mitgelieferten Kommentarklasse und warum geschriebene Rezensionen meist auf externen Plattformen stehen - mit Moderation, Regeln für Ehrlichkeit und einer Checkliste.
keywords: Rezensionen, Bewertungen, Sternebewertung, ezstarrating, Kommentare, Kundenstimmen, Moderation, externe Plattformen, Vertrauen, lokales Unternehmen, reviews, ratings, star rating, testimonials, local business
type: help
---
# Bewertungen und Rezensionen

Diese Seite ist für lokale Unternehmen, die zeigen wollen, was Kunden denken. Sie trennt drei Dinge, die oft
vermischt werden: **Sternebewertungen**, **Kommentare** und **geschriebene Rezensionen**.

## Inhalt

1. [Was es gibt](#was-es-gibt)
2. [Sternebewertungen mit ezstarrating](#sternebewertungen-mit-ezstarrating)
3. [Kommentare](#kommentare)
4. [Geschriebene Rezensionen und Kundenstimmen](#geschriebene-rezensionen-und-kundenstimmen)
5. [Ehrlich mit Meinungen umgehen](#ehrlich-mit-meinungen-umgehen)
6. [Checkliste](#checkliste)

## Was es gibt

| Was | In Exponential | Quelle |
|---|---|---|
| Sternebewertungen, 1 bis 5, auf beliebigen Inhalten | ja, `ezstarrating` (in der Standard-`composer.json`) | [ezstarrating](../../../funktionen/6.0/erweiterungen/ezstarrating.md) |
| Kommentare unter einer Seite | ja, eine Klasse `comment` aus dem Seitenpaket `ezwebin`, mit einem Template in `ezwebin` | [Seitenpaket ezwebin](../../../funktionen/6.0/erweiterungen/ezwebin-ezpackage.md) |
| Geschriebene Rezensionen mit je einer Sternebewertung, dem Namen der rezensierenden Person und einer Antwort | kein Rezensionsmodul gefunden | gesucht in `composer.json` und `extension/`, 20. Oktober 2026 |
| Rezensionen von Karten- oder Bewertungsplattformen importieren | nein | - |

## Sternebewertungen mit ezstarrating

Was es kann, laut der README der Erweiterung und [ezstarrating](../../../funktionen/6.0/erweiterungen/ezstarrating.md):

- es fügt einen Feldtyp **Star Rating** hinzu; fügen Sie ihn einer Klasse hinzu (Produkt, Leistung, Rezept), und
  Besucher bewerten Objekte dieser Klasse mit 1 bis 5 Sternen;
- jeder Benutzer kann **einmal pro Sitzung** abstimmen;
- Bewertungen werden pro Objekt gespeichert und gemittelt; Templates zeigen den Durchschnitt, und Listen lassen sich
  nach Bewertung sortieren;
- Abstimmen braucht eine Berechtigung (`ezjscore` / `call` mit der Einschränkung `ezstarrating_rate`); das
  mitgelieferte Seitenpaket gibt sie anonymen Besuchern.

Was es nicht kann: kein Text, kein Name der rezensierenden Person, keine Moderationswarteschlange, kein Schutz davor,
dass jemand in einer neuen Sitzung erneut abstimmt. Für ein lokales Unternehmen funktionieren Sternebewertungen am
besten bei Produkten oder Gerichten, als leichtes Signal. Sie ersetzen keine Rezensionen.

## Kommentare

Mit der Klasse `comment` können Besucher unter einem Artikel oder einem Produkt schreiben. Das Seitenpaket `ezwebin`
gibt anonymen Besuchern die Berechtigung, Kommentare in der Sektion Standard anzulegen. Wenn Sie Kommentare zulassen:

- schützen Sie die Kommentarklasse mit einem CAPTCHA ([Spam stoppen](spam-stoppen.md));
- entscheiden Sie, ob Kommentare sofort oder nach Freigabe erscheinen (ein Freigabe-Workflow, eingerichtet von Ihrem
  Ersteller: [Freigaben und Prüfung](../../kunden/alltag/freigabe-und-pruefung.md));
- lesen Sie sie regelmäßig; eine unbeantwortete Beschwerde unter Ihrer eigenen Seite ist schlimmer als gar keine.

Viele lokale Unternehmen schalten Kommentare ab und schicken Menschen stattdessen zum Kontaktformular. Die
Berechtigung zu entfernen ist für Ihren Ersteller eine kleine Änderung ([Berechtigungen für Besucher](../../ersteller/website-ersteller/rechte-fuer-besucher.md)).

## Geschriebene Rezensionen und Kundenstimmen

Die meisten Kunden schreiben Rezensionen auf Karten-, Such- und Bewertungsplattformen, nicht auf der eigenen Website
des Unternehmens ([Unternehmenseinträge außerhalb von Exponential](../gefunden-werden/unternehmenseintraege-ausserhalb-von-exponential.md)).
Auf Ihrer Website können Sie:

- **Kundenstimmen** veröffentlichen: kurze Zitate von Kunden, die schriftlich zugestimmt haben, zitiert zu werden, nur
  mit dem Namen, den sie erlauben (oft Vorname und Ort);
- auf Ihre Profile auf Bewertungsplattformen verlinken („Lesen Sie unsere Rezensionen“);
- ein Widget einer Bewertungsplattform einbetten, falls sie eines anbietet; das ist ein externes Skript
  (Datenschutzhinweis, Content Security Policy).

## Ehrlich mit Meinungen umgehen

- Erfinden Sie keine Rezensionen oder Bewertungen, und schreiben Sie sie nicht selbst unter dem Namen eines Kunden.
  Viele Länder verbieten das.
- Entfernen Sie kritische Kommentare nicht nur, weil sie kritisch sind; entfernen Sie Spam, Beleidigungen und
  personenbezogene Daten.
- Kopieren Sie keine Rezensionen von Plattformen auf Ihre Website, ohne die Verfasser zu fragen.
- Kennzeichnen Sie ausgewählte Kundenstimmen als ausgewählt.

## Checkliste

- [ ] Entschieden: Sternebewertungen ja oder nein, und bei welchen Klassen.
- [ ] Kommentare erlaubt oder abgeschaltet; wenn erlaubt, mit CAPTCHA und einer Moderationsroutine.
- [ ] Kundenstimmen nur mit schriftlicher Erlaubnis.
- [ ] Links zu den Bewertungsprofilen, auf denen Kunden tatsächlich schreiben.
- [ ] Jemand liest und beantwortet Rezensionen wöchentlich.

## Verwandte Seiten

- [Kontakt und Formulare](README.md)
- [Unternehmenseinträge außerhalb von Exponential](../gefunden-werden/unternehmenseintraege-ausserhalb-von-exponential.md)
- [Ihren Shop bewerben](../../unternehmen/shops/ihren-shop-bewerben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
