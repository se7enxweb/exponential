---
title: Caching in der App
audience: mobile-app-developer, developer
category: mobile-apps
order: 620
summary: Exponential-Inhalte in einer App zwischenspeichern, damit sie schnell startet und Daten spart - was zwischenspeichern, Cache-Schlüssel je Benutzer, Version und Sprache, Aktualität mit dateModified und childrenCount, Invalidierung nach Schreibzugriffen, Bilder, das Ratenlimit von API-Schlüsseln und Anfragebudgets.
keywords: Cache, App-Cache, Cache-Schlüssel, dateModified, childrenCount, Aktualität, stale-while-revalidate, Invalidierung, Bilder, Ratenlimit, 429, Retry-After, Anfragebudget, Leistung, Mobile App, cache, app cache, cache key, freshness, invalidation, images, rate limit, request budget, performance, mobile app
type: help
---
# Caching in der App

Diese Seite richtet sich an App-Entwickler, die die Datenschicht einer App entwerfen. Der Server sendet für
API-Antworten keine HTTP-Caching-Header, auf die sich die App verlassen könnte
([Caching auf dem Server](caching-auf-dem-server.md)), daher entscheidet die App, was sie wie lange behält. Die Ratschläge
sind allgemeine Praxis, verknüpft mit den Feldern, die Exponential Ihnen liefert.

## Inhalt

1. [Was zwischenspeichern](#was-zwischenspeichern)
2. [Cache-Schlüssel](#cache-schlüssel)
3. [Über Aktualität entscheiden](#über-aktualität-entscheiden)
4. [Nach einem Schreibzugriff](#nach-einem-schreibzugriff)
5. [Anfragebudgets und Ratenlimits](#anfragebudgets-und-ratenlimits)

## Was zwischenspeichern

| Daten | Zwischenspeichern? | Hinweise |
|---|---|---|
| Listen eines Ordners (erste Seite) | ja | beim Start sofort zeigen, im Hintergrund aktualisieren |
| Detailantworten | ja | nach Knoten-ID |
| Bilddateien | ja | der Bildcache Ihrer Plattform, nach URL |
| Alias-URLs von `expimage::url` | ja | mit dem Element |
| Die Sprachen der Website, die Aliasliste | ja, für einen Tag | sie ändern sich selten |
| Zugangsdaten | nur im sicheren Speicher | [Geheimnisse auf Geräten speichern](../anmeldung/geheimnisse-auf-geraeten-speichern.md) |
| Antworten eines anderen Benutzers | nie | meldet sich ein gemeinsam genutztes Gerät ab, wird der Cache verworfen |

## Cache-Schlüssel

Nehmen Sie alles auf, was die Antwort verändert:

```text
<installation base URL> | <user id> | <route> | <ResponseGroups> | <Translation> | <offset>/<limit>/<sort>
```

Der Benutzer zählt, weil der Server je Benutzer antwortet: Zwei Benutzer können unterschiedliche Unterknoten desselben
Ordners sehen. Die Sprache zählt, weil derselbe Knoten je `Translation` einen anderen Text hat.

## Über Aktualität entscheiden

- **Stale while revalidate.** Zeigen Sie die zwischengespeicherte Kopie sofort, fordern Sie eine frische an und ersetzen
  Sie sie, wenn sie sich geändert hat. Dieses Muster lässt eine App sofort reagieren.
- **`dateModified`** eines Elements (Unix-Zeit) sagt, ob ein zwischengespeichertes Detail noch aktuell ist: Vergleichen
  Sie es mit dem Wert in der neuesten Listenantwort, bevor Sie das Detail erneut lesen.
- **`metadata.childrenCount`** einer Liste ändert sich, wenn Elemente hinzukommen oder entfernt werden; ein günstiges
  `GET .../childrenCount` sagt, ob sich die Anzahl in einem Ordner geändert hat.
- **Sortieren Sie nach `modified`** (`.../list/offset/0/limit/20/sort/modified/desc`), um die zuletzt geänderten Elemente
  zuerst zu sehen, und hören Sie auf zu lesen, wenn Sie Elemente erreichen, die älter als Ihre letzte Synchronisation
  sind.
- Denken Sie daran, dass der eigene Antwort-Cache des Servers bis zu `DefaultCacheTTL` (standardmäßig 600 Sekunden) alt
  sein kann.

## Nach einem Schreibzugriff

- Fügen Sie das Element, das der Schreibzugriff zurückgegeben hat, sofort in die lokale Liste ein (optimistische
  Aktualisierung), als ausstehend markiert, bis ein Lesezugriff es bestätigt.
- Verwerfen Sie das zwischengespeicherte Detail eines aktualisierten oder entfernten Elements und die erste Seite der
  Liste seines Elternknotens.
- Lesen Sie die Liste nicht sofort erneut, um zu bestätigen: Der Antwort-Cache des Servers kann noch die alte Liste
  liefern ([Caching auf dem Server](caching-auf-dem-server.md#aktualität-nach-dem-veröffentlichen)).

## Anfragebudgets und Ratenlimits

- Ein persönlicher API-Schlüssel ist auf `RateLimitPerMinute` Anfragen pro Minute begrenzt (standardmäßig 120) und
  antwortet darüber mit `429` und `Retry-After` ([Persönliche API-Schlüssel für Werkzeuge](../anmeldung/persoenliche-api-schluessel-fuer-werkzeuge.md#grenzen)).
  Ein Backend, das viele Telefone mit einem einzigen Schlüssel bedient, muss zwischenspeichern, sonst erreicht es das
  Limit.
- Bündeln Sie, wo die API es erlaubt: eine Liste mit `ResponseGroups=Fields` statt eines Detailaufrufs je Zeile;
  `expnode::getMany` liest mehrere Knoten nach ID in einem Aufruf.
- Fragen Sie im Hintergrund nicht öfter ab, als sich die Inhalte ändern; aktualisieren Sie beim App-Start, beim
  Herunterziehen zum Aktualisieren und wenn sich ein Bildschirm öffnet.

## Verwandte Seiten

- [Synchronisation und Offline-Lesen](synchronisation-und-offline-lesen.md)
- [Unterknoten auflisten und Paginierung](../rest-api/unterknoten-auflisten-und-paginierung.md)
- [Bildaliase für Bildschirmgrößen](../medien-und-bilder/bildaliase-fuer-bildschirmgroessen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
