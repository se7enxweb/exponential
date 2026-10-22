---
title: Synchronisation und Offline-Lesen
audience: mobile-app-developer, developer
category: mobile-apps
order: 630
summary: Einen Teil einer Exponential-Website auf einem Gerät ohne Netz lesbar halten - auswählen, was synchronisiert wird, eine vollständige und eine inkrementelle Synchronisation mit der Route list und dateModified, expnode::modifiedSince und seine Grenze (Veröffentlichungsdatum), entfernte Elemente erkennen, wartende Schreibzugriffe mit Remote-IDs und Konflikte.
keywords: offline, Synchronisation, inkrementelle Synchronisation, dateModified, modifiedSince, entfernte Elemente, 404, childrenCount, Warteschlange, Offline-Schreibzugriffe, Remote-ID, Konflikte, Mobile App, sync, synchronization, incremental sync, removed items, queue, offline writes, remote id, conflicts, mobile app
type: help
---
# Synchronisation und Offline-Lesen

Diese Seite richtet sich an App-Entwickler, deren App offline funktionieren muss: ein Leitfaden für den Außeneinsatz,
ein Katalog für den Vertrieb, ein Handbuch auf einem Schiff. Exponential hat keinen Änderungs-Feed für den ganzen Baum,
daher synchronisiert die App mit den vorhandenen Routen, was sie braucht. Der Entwurf unten ist ein Vorschlag, der auf
diesen Routen aufbaut.

## Inhalt

1. [Auswählen, was synchronisiert wird](#auswählen-was-synchronisiert-wird)
2. [Die erste vollständige Synchronisation](#die-erste-vollständige-synchronisation)
3. [Inkrementelle Synchronisationen](#inkrementelle-synchronisationen)
4. [Entfernte Elemente](#entfernte-elemente)
5. [Offline vorgenommene Schreibzugriffe](#offline-vorgenommene-schreibzugriffe)

## Auswählen, was synchronisiert wird

Synchronisieren Sie Ordner, nicht die Website: einen oder wenige Teilbäume, die die App zeigt, mit dem Website-Team per
Remote-ID vereinbart ([Knoten-IDs, Remote-IDs und Deep Links](../inhaltsmodellierung/knoten-ids-remote-ids-und-deep-links.md)).
Legen Sie fest, welchen Bildalias Sie je Element speichern, und lassen Sie Originale weg.

## Die erste vollständige Synchronisation

Für jeden synchronisierten Ordner:

1. Blättern Sie durch `GET /api/ezp/v2/content/node/<id>/list/offset/<n>/limit/50?ResponseGroups=Fields`, bis `offset`
   den Wert `metadata.childrenCount` erreicht ([Unterknoten auflisten und Paginierung](../rest-api/unterknoten-auflisten-und-paginierung.md)).
2. Wiederholen Sie das für jeden Unterknoten, der selbst ein Container ist, den die App zeigt.
3. Holen Sie je Bild eine Alias-URL (`expimage::url`) und laden Sie die Dateien herunter.
4. Speichern Sie `nodeId`, `dateModified` und die Antwort; merken Sie sich, wann die Synchronisation begonnen hat.

## Inkrementelle Synchronisationen

| Weg | Wie | Erfasst |
|---|---|---|
| Nach Änderung sortieren | `.../list/offset/0/limit/50/sort/modified/desc`, beim ersten Element mit `dateModified` vor der letzten Synchronisation aufhören | neue und geänderte direkte Unterknoten |
| `expnode::modifiedSince::<node id>::<timestamp or date>::<limit>::<offset>` | die Remote-Dienste, unterhalb eines Knotens in beliebiger Tiefe | Elemente, die seit diesem Zeitpunkt **veröffentlicht** wurden: Der Dienst filtert nach dem Veröffentlichungsdatum (`published >=`), Bearbeitungen älterer Elemente sind also nicht enthalten |
| `childrenCount` vergleichen | `GET .../childrenCount` | dass etwas hinzugekommen oder entfernt worden ist, günstig |

Für geänderte Elemente tiefer im Baum kombinieren Sie: `modifiedSince` für neue Elemente und je synchronisiertem Ordner
eine nach Änderung sortierte Liste für Bearbeitungen.

## Entfernte Elemente

Ein entferntes Element verschwindet einfach aus den Listen. Sie erkennen es an:

- einem `404`, wenn die App ein zwischengespeichertes Detail liest; löschen Sie es dann lokal;
- einem `childrenCount`, der kleiner ist als die Zahl der lokalen Unterknoten; lesen Sie dann die Knoten-IDs des Ordners
  erneut (eine Liste ohne `Fields` ist leicht) und löschen Sie die, die verschwunden sind.

Elemente, die vor dem Benutzer der App verborgen oder aus dem synchronisierten Ordner verschoben wurden, sehen aus Sicht
der App genauso aus wie entfernte.

## Offline vorgenommene Schreibzugriffe

- Stellen Sie Schreibzugriffe auf dem Gerät mit allem in eine Warteschlange, was nötig ist, um sie später zu senden.
- Geben Sie jedem wartenden Anlegen eine auf dem Gerät erzeugte `remote_id` und senden Sie sie mit `expnode::create`:
  Bricht die Verbindung ab, nachdem der Server veröffentlicht hat, antwortet die Wiederholung mit `409`, statt ein
  Duplikat anzulegen ([Über expservices schreiben](../json-endpunkte/ueber-expservices-schreiben.md#anlegen)).
- Senden Sie wartende Schreibzugriffe einzeln und der Reihe nach, wenn das Gerät online ist; halten Sie die Warteschlange
  bei `401` an und bitten Sie den Benutzer, sich anzumelden.
- Offline vorgenommene Aktualisierungen können mit zwischenzeitlichen Bearbeitungen auf der Website in Konflikt geraten.
  Vergleichen Sie `dateModified` des Elements mit dem Wert, den der Benutzer gesehen hat, bevor Sie eine Aktualisierung
  senden, und fragen Sie den Benutzer, wenn sie sich unterscheiden. Der Server veröffentlicht, was zuletzt gesendet wird.

## Verwandte Seiten

- [Caching in der App](caching-in-der-app.md)
- [Caching auf dem Server](caching-auf-dem-server.md)
- [Was die APIs nicht können](../was-die-apis-nicht-koennen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
