---
title: Knoten-IDs, Remote-IDs und Deep Links
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 420
summary: Inhalte in einer App so kennzeichnen, dass die Verweise Neuinstallationen und Verschiebungen überstehen - Knoten-IDs, Objekt-IDs und Remote-IDs im Vergleich, Einstiegspunkte per Remote-ID konfigurieren, Elemente mit expnode::getByRemoteId und expnode::getByPath nachschlagen, fullUrl zum Teilen und Links der Website in der App öffnen.
keywords: Knoten-ID, Objekt-ID, Remote-ID, nodeRemoteId, objectRemoteId, getByRemoteId, getByPath, expurlalias, resolve, fullUrl, URL-Alias, Deep Link, Universal Link, App Link, teilen, Mobile App, node id, object id, remote id, URL alias, deep link, universal link, app link, share, mobile app
type: help
---
# Knoten-IDs, Remote-IDs und Deep Links

Diese Seite richtet sich an App-Entwickler, die Verweise auf Inhalte speichern: den Ordner, bei dem die App beginnt,
Favoriten, Links, die von der Website in die App geteilt werden. Sie erklärt, welche ID Sie behalten und wie Sie jede
Art von Verweis auflösen.

## Inhalt

1. [Drei IDs](#drei-ids)
2. [Einstiegspunkte per Remote-ID](#einstiegspunkte-per-remote-id)
3. [Einen URL-Pfad auflösen](#einen-url-pfad-auflösen)
4. [Teilen und Deep Links](#teilen-und-deep-links)

## Drei IDs

| ID | Wo | Stabil? |
|---|---|---|
| `nodeId` | REST-Metadaten, Listen, Links | stabil auf einer Installation, solange der Knoten existiert; auf einer anderen Installation eine andere Nummer |
| `objectId` | REST-Metadaten | stabil auf einer Installation; dasselbe Objekt behält sie, wenn es verschoben wird oder weitere Platzierungen erhält |
| `nodeRemoteId`, `objectRemoteId` | REST-Metadaten | Zeichenketten, die Pakete und Importe setzen können, sodass dieselben Inhalte auf jeder Installation dieselbe Remote-ID tragen können |

Die Nummern unterscheiden sich zwischen einer Test- und einer Produktionsinstallation und zwischen Installationen, die
zu verschiedenen Zeiten aus demselben Paket gebaut wurden. Halten Sie Nummern in Caches, nicht in der Konfiguration
der App.

## Einstiegspunkte per Remote-ID

Konfigurieren Sie die Einstiegspunkte der App (den Nachrichtenordner, den Produktkatalog) per Remote-ID und schlagen Sie
sie einmal beim Start nach:

```bash
curl -s -H "X-Exp-Token: $EXPSERVICES_TOKEN" \
     'https://example.com/ezjscore/call/expnode::getByRemoteId::<node remote id>?ContentType=json'
```

`expnode::getByRemoteId` antwortet mit dem Knoten (mit `node_id`); `expobject::getByRemoteId` tut dasselbe für ein
Objekt. Beide brauchen `content/read`. Verwenden Sie dann für die Sitzung die Knoten-ID mit der REST-API. Ein Entwickler
mit `content/edit` kann einem Ordner mit `expnode::setRemoteId` eine lesbare Remote-ID geben
([Über expservices schreiben](../json-endpunkte/ueber-expservices-schreiben.md) beschreibt die Regeln für
Schreibzugriffe); vereinbaren Sie sie mit dem Website-Team, damit sie später niemand ändert.

## Einen URL-Pfad auflösen

| Dienst | Argument | Zugriff |
|---|---|---|
| `expnode::getByPath` | ein URL-Alias wie `Media/Images` | `content/read` |
| `expurlalias::resolve` | ein URL-Pfad; antwortet mit der Aktion und dem Knoten | angemeldeter Benutzer |
| `expnode::url` | eine Knoten-ID; antwortet mit `url_alias` und `system_url` | `content/read` |

Prozentkodieren Sie den Pfad als ein einziges Argument. Testen Sie früh einen Pfad mit mehreren Ebenen auf Ihrem
Server: Manche Webserver-Konfigurationen lehnen einen kodierten Schrägstrich (`%2F`) in einer URL ab. So macht eine App
aus einem Link der Website einen Knoten.

## Teilen und Deep Links

- **Teilen** Sie die `fullUrl` des Knotens aus den REST-Metadaten: Sie ist die öffentliche Adresse der Seite auf der
  Website, sodass jeder sie auch ohne die App öffnen kann. Verwenden Sie die Adresse der Hauptplatzierung
  ([Einen Knoten lesen](../rest-api/einen-knoten-lesen.md#platzierungen)).
- **Links der Website in der App öffnen** Sie mit den verifizierten Links Ihrer Plattform (Universal Links, App Links).
  Die App erhält den Pfad der Website-URL, löst ihn mit `expnode::getByPath` auf und öffnet den Knoten; lässt sich der
  Pfad nicht auflösen, öffnen Sie die URL im Browser.
- **Verschobene Inhalte**: Wenn Redakteure ein Element verschieben oder umbenennen, ändert sich sein URL-Alias.
  Exponential behält in vielen Fällen Weiterleitungen für alte Aliase
  ([Weiterleitungen und verschobene Inhalte](../../ersteller/website-ersteller/weiterleitungen-und-verschobene-inhalte.md)), aber eine
  App sollte gespeicherte IDs gegenüber Pfaden bevorzugen.
- Die Verifizierungsdateien, die Ihre Plattform auf der Website braucht (zum Beispiel die Zuordnungsdateien unter
  `/.well-known/`), liefert der Webserver aus; sie hinzuzufügen ist eine Aufgabe des Hostings.

## Verwandte Seiten

- [URL-Aliase und sprechende URLs](../../ersteller/website-ersteller/url-aliasse-und-sprechende-urls.md)
- [URLs und Aliase](../../../anleitungen/urls-und-aliase.md)
- [Wie Exponential-Inhalte in eine App gelangen](../wie-exponential-inhalte-in-eine-app-gelangen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
