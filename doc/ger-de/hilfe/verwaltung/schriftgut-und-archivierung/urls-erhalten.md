---
title: URLs erhalten
audience: government, editor, admin
category: public-sector
order: 190
summary: Für öffentliche Stellen, deren Adressen in Beschlüssen, Schreiben und Gesetzen zitiert werden - wie Exponential URLs stabil hält (URL-Aliasse, historische Aliasse, die nach Umbenennen oder Verschieben weiterleiten, globale Aliasse, Platzhalter), was sie bricht (Entfernen) und Praktiken für zitierfähige Adressen.
keywords: URLs, dauerhafte Links, Permalinks, persistente URLs, URL-Aliasse, historische Aliasse, Weiterleitungen, Umbenennungen, Verschiebungen, globale URL-Aliasse, URL-Übersetzer, URL-Platzhalter, Linkverfall, Zitate, content/view/full, öffentlicher Sektor, permanent links, persistent URLs, URL aliases, history aliases, redirects, renames, moves, URL translator, URL wildcards, link rot, citations, public sector
type: help
---
# URLs erhalten

Diese Seite richtet sich an Redaktionen und Administratoren. Öffentliche Stellen zitieren Webadressen in Schreiben,
Beschlüssen und anderen Veröffentlichungen; eine Adresse, die nicht mehr funktioniert, macht diese Dokumente schwerer
nutzbar. Exponential hält Adressen bei Umbenennungen und Verschiebungen stabil; das Entfernen ist der wichtigste Weg,
sie zu brechen ([Weiterleitungen und verschobene Inhalte](../../../../eng-us/help/creators/website-creators/redirects-and-moved-content.md)).

## Inhalt

1. [Was automatisch geschieht](#was-automatisch-geschieht)
2. [Werkzeuge für stabile Adressen](#werkzeuge-für-stabile-adressen)
3. [Praktiken](#praktiken)
4. [Prüfen](#prüfen)
5. [Verwandte Seiten](#verwandte-seiten)

## Was automatisch geschieht

| Ereignis | Ergebnis |
|---|---|
| ein Knoten wird umbenannt | ein neuer Alias entsteht; der alte bleibt und leitet auf den neuen weiter |
| ein Knoten wird verschoben | dasselbe: der alte Pfad leitet weiter |
| eine Übersetzung wird umbenannt | dasselbe, pro Sprache |
| ein Knoten wird entfernt | die Adresse antwortet mit „nicht gefunden“; nichts leitet weiter |

## Werkzeuge für stabile Adressen

| Bedarf | Werkzeug |
|---|---|
| eine kurze, zitierfähige Adresse für eine Seite (`/accessibility`, `/privacy`) | ein Alias am Knoten (sein Reiter **URL-Aliase**) |
| eine Adresse auf eine Ansicht eines Moduls | **Setup > URL-Übersetzer**: ein globaler Alias, auf Wunsch mit Weiterleitung (301) |
| eine Gruppe alter Adressen nach einer Umstrukturierung | **Setup > URL Platzhalter** |
| Besucher einer entfernten Seite an einen nützlichen Ort schicken | ein Alias oder Platzhalter auf den Ersatz oder auf eine Archivseite |

Einzelheiten: [Links, URL-Aliasse, Platzhalter](../../../anleitungen/urls-und-aliase.md),
[URL-Aliasse und sprechende URLs](../../../../eng-us/help/creators/website-creators/url-aliases-and-nice-urls.md).

## Praktiken

- Zitieren Sie den Alias, nicht die System-URL (`/content/view/full/<node id>`); Anfrageregeln oder request-shield
  können System-URLs für Besucher ablehnen.
- Bevorzugen Sie bei zitierten Seiten das Archivieren (Verschieben, Verstecken mit einer Hinweisseite, ein
  Archivzustand) gegenüber dem Entfernen.
- Prüfen Sie vor dem Entfernen eingehende Links: Setup > URL-Verwaltung listet ausgehende Links auf; durchsuchen Sie
  Ihre eigenen Dokumente nach der Adresse; sehen Sie sich nach der Änderung `404.log` an.
- Zitieren Sie bei Dokumenten (PDFs) die Seite, die die Datei enthält, statt der Download-Adresse der Datei, damit das
  Dokument ersetzt werden kann, ohne das Zitat zu brechen.

## Prüfen

```bash
curl -sI https://www.example.gov/<old address> | head -3     # 301 to the new address
```

und beobachten Sie nach Umstrukturierungen **Setup > Logdateien** > „Nicht gefundene Anfragen (404)“.

## Verwandte Seiten

- [Weiterleitungen nach einer Umstrukturierung](weiterleitungen-nach-einer-umstrukturierung.md)
- [Links, URL-Aliasse, Platzhalter und Suchstatistiken](../../../anleitungen/urls-und-aliase.md)
- [URL-Platzhalter](../../../../eng-us/help/creators/website-creators/url-wildcards.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
