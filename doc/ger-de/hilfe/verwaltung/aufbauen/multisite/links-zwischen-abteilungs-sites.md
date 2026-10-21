---
title: Links zwischen Abteilungs-Sites
audience: government, admin, website-creator
category: public-sector
order: 70
summary: Warum Links von einer Abteilungs-Site auf Inhalte einer anderen in die falsche Site zeigen können, wie die Erweiterung ezurlmultisite ezurl die Adresse der Site erzeugen lässt, die den Inhalt ausliefert, und wie Sie das konfigurieren und prüfen.
keywords: Links zwischen Sites, Multisite-Links, Abteilungs-Sites, cross-site links, ezurlmultisite, PathPrefix, RootNodeList, SiteAccessList, ezurl, multisite links, 404, department sites
type: help
---
# Links zwischen Abteilungs-Sites

Diese Seite richtet sich an Administratoren und Site-Ersteller einer Installation, in der ein Inhaltsbaum mehrere Sites
bedient. Eine Seite einer Site, die Inhalte einer anderen listet (eine Schlagwortseite, ein Suchergebnis, ein
verwandtes Element), kann sonst Adressen erzeugen, die es nicht gibt
([Links auf Inhalte einer anderen Site](../../../../funktionen/6.0/websiteuebergreifende-links-im-multisite.md)).

## Inhalt

1. [Das Problem](#das-problem)
2. [Die Lösung: ezurlmultisite](#die-lösung-ezurlmultisite)
3. [Konfiguration](#konfiguration)
4. [Prüfen](#prüfen)

## Das Problem

Jede Site blendet den Pfad ihres Teilbaums mit `PathPrefix` aus. Ein Knoten einer anderen Site behält den Pfad jener
Site in seinem URL-Alias, und `ezurl` setzt den aktuellen Siteaccess davor, was eine Adresse ergibt, die mit „nicht
gefunden“ antwortet.

## Die Lösung: ezurlmultisite

Die Erweiterung `ezurlmultisite` (von der `composer.json` im Wurzelverzeichnis verlangt) lässt `ezurl` und die
Link-Operatoren des Media-Designs die Adresse auf dem Siteaccess erzeugen, der den Inhalt ausliefert. Links innerhalb
der aktuellen Site, Modul-Links, Dateien und jeder Link, der auf einem nicht teilnehmenden Siteaccess erzeugt wird
(alle Siteaccesses der Administration eingeschlossen), bleiben, wie sie waren. Es kostet keine Datenbankabfrage je
Link.

## Konfiguration

`settings/override/ezurlmultisite.ini.append.php`, `[MultisiteSettings]`:

| Schlüssel | Bedeutung |
|---|---|
| `SiteAccessList[]` | die Sites, in der Reihenfolge der Bevorzugung |
| `RootNodeList[<siteaccess>]=<node id>` | der Wurzelknoten des Teilbaums jeder Site |

Ohne die Datei nehmen die öffentlichen Siteaccesses mit einem `PathPrefix` teil, jeweils mit dem Knoten ihres
`RootNode` aus `content.ini` oder ihrer `IndexPage`.

## Prüfen

Öffnen Sie auf Site A eine Seite, die ein Element von Site B listet (eine Suche danach, eine Schlagwortseite); der Link
muss auf den Host von Site B zeigen und das Element öffnen. Prüfen Sie auch die Sprachfassungen.

## Verwandte Seiten

- [Links auf Inhalte einer anderen Site](../../../../funktionen/6.0/websiteuebergreifende-links-im-multisite.md)
- [Gemeinsame Inhalte über Sites hinweg](gemeinsame-inhalte-ueber-sites-hinweg.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
