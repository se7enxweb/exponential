---
title: Inhalte suchen
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 320
summary: Einer App über die Suchdomäne von expservices eine Suche hinzufügen, da die REST-API keine Suchroute hat - expsearch::search mit Text, Limit, Offset und einem JSON-Filter (Klasse, Bereich, Teilbaum, Datum), unterhalb eines Knotens oder innerhalb von Klassen suchen, Autovervollständigung mit suggest, die Antwort und ihre Grenzen.
keywords: Suche, expsearch, Suchdienst, Filter, Klasse, Teilbaum, Bereich, Datum, inSubtree, byClass, suggest, Autovervollständigung, Stoppwörter, Suchmaschine, Solr, eZ Find, Mobile App, search, search service, filter, class, subtree, section, date, autocomplete, stop words, search engine, mobile app
type: help
---
# Inhalte suchen

Diese Seite richtet sich an App-Entwickler, die einer App ein Suchfeld hinzufügen. Die REST-Inhalts-API hat keine
Suchroute, daher läuft die Suche über die Domäne `expsearch` der Remote-Dienste. Die Dienste sind in
`extension/expservices/classes/content/expsearchservices.php` deklariert.

## Inhalt

1. [Der Hauptaufruf](#der-hauptaufruf)
2. [Filter](#filter)
3. [Engere Suchen](#engere-suchen)
4. [Autovervollständigung](#autovervollständigung)
5. [Die Antwort](#die-antwort)
6. [Grenzen und gute Praxis](#grenzen-und-gute-praxis)

## Der Hauptaufruf

```text
expsearch::search::<text>::<limit>::<offset>::<filter json>
```

```bash
curl -s -H "X-Exp-Token: $EXPSERVICES_TOKEN" \
     'https://example.com/ezjscore/call/expsearch::search::solar%20panels::20::0?ContentType=json'
```

Prozentkodieren Sie den Text: Leerzeichen und vor allem `:`, das sonst die Argumente trennen würde. Der Dienst braucht
`content/read`, und jeder Treffer wird vor der Rückgabe mit `canRead()` geprüft, sodass die Ergebnisse den Rechten des
Benutzers folgen.

## Filter

Das vierte Argument ist ein JSON-Objekt, prozentkodiert:

| Schlüssel | Wert | Wirkung |
|---|---|---|
| `class` | eine Liste von Klassenbezeichnern | nur diese Klassen |
| `section` | eine Bereichs-ID | nur dieser Bereich |
| `subtree` | eine Liste von Knoten-IDs | nur unterhalb dieser Knoten |
| `date` | `day`, `week`, `month`, `3months` oder `year` | nur Inhalte, die in diesem Zeitraum veröffentlicht wurden |
| `attribute` | eine Klassenattribut-ID | nur Treffer in diesem Feld |

```text
expsearch::search::solar::20::0::{"class":["article"],"subtree":[<node id>]}
```

(Zur besseren Lesbarkeit unkodiert gezeigt; kodieren Sie das JSON in der URL.)

## Engere Suchen

| Dienst | Argumente |
|---|---|
| `expsearch::inSubtree` | Text, Knoten-ID, Limit, Offset |
| `expsearch::byClass` | Text, Klassen (eine Liste), Limit, Offset |
| `expsearch::bySection` | Text, Bereichs-ID, Limit, Offset |
| `expsearch::byAttribute` | Text, Klassenattribut-ID, Limit, Offset |

Verwenden Sie `inSubtree`, um die Suche einer App in dem Teil des Baums zu halten, den die App zeigt.

## Autovervollständigung

```text
expsearch::suggest::<prefix>::<limit>
```

Liefert Wörter des Suchindex, die mit dem Präfix beginnen, die häufigsten zuerst. Rufen Sie den Dienst nach einer kurzen
Tipppause auf, nicht bei jedem Tastendruck.

## Die Antwort

`data` ist eine Liste von Knoten im Knotenformat von expservices (`node_id`, `object_id`, `class`, `name`,
`url_alias`, `published`, `modified` und mehr, aus `expContentBase::exportNode()`). `meta` enthält die
Paginierungsschlüssel (`total`, `offset`, `limit`, `count`, `has_more`) sowie `stop_words` (Wörter, die die Suchmaschine
ignoriert hat) und `text` (den gesuchten Text). Um einen Treffer anzuzeigen, öffnen Sie ihn über `node_id` mit der
REST-API ([Einen Knoten lesen](../rest-api/einen-knoten-lesen.md)).

## Grenzen und gute Praxis

- Der Text darf nicht leer sein und höchstens 250 Zeichen haben; sonst `400`.
- Ist die Suche auf der Installation deaktiviert, antwortet der Dienst mit `403` „Search is disabled“.
- Die Qualität der Ergebnisse hängt von der Suchmaschine ab, die die Installation verwendet: der eingebauten Suche oder
  Solr über eZ Find ([Suche mit eZ Find und Solr](../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md)).
  Fragen Sie, welche Ihre verwendet (`expsearch::engine`, für angemeldete Benutzer).
- Neue Inhalte werden gefunden, sobald sie indiziert sind; bei verzögerter Indizierung kann das bis zum nächsten
  Cronjob-Lauf dauern.
- Zeigen Sie dem Benutzer `stop_words`, wenn eine Suche nichts findet, weil die Wörter ignoriert wurden.

## Verwandte Seiten

- [expservices aus einer App aufrufen](expservices-aus-einer-app-aufrufen.md)
- [Suche auf der Website](../../ersteller/website-ersteller/suche-auf-der-website.md)
- [Backend-Dienste über ezjscore (Referenz)](../../../bc/6.0/backend-dienste-ueber-ezjscore.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
