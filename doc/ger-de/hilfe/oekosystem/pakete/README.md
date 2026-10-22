---
title: Pakete des Ökosystems von Exponential
audience: ecosystem, developer, admin, hosting
category: ecosystem
order: 20
summary: Die Einstiegsseite des Teils Pakete im Bereich Ökosystem - die mit dem Kernel mitgelieferten Erweiterungen, Composer und Packagist, wie Pakete versioniert werden, Seitenpakete und der Paketserver, Themes und Designs, eine Erweiterung finden und Kompatibilität.
keywords: Pakete, Erweiterungen, Composer, Packagist, se7enxweb, Versionen, Seitenpakete, Paketserver, Themes, Designs, Kompatibilität, Abhängigkeiten, packages, extensions, versions, site packages, package server, themes, designs, compatibility, dependencies
type: help
---
# Pakete des Ökosystems von Exponential

Fast alles in der Welt von Exponential ist ein Paket: der Kernel, jede Erweiterung, jedes Theme, jeder Server, jeder
Fork einer Bibliothek eines Ursprungsprojekts. "Every package is a GitHub repository under se7enxweb and a Composer
package", jedes Paket ist ein GitHub-Repository unter se7enxweb und ein Composer-Paket
([Releases und Versionsnummern](../../wer-ist-die-exponential-foundation.md#releases-und-versionsnummern)).

## Inhalt

1. [Die Seiten dieses Teils](#die-seiten-dieses-teils)
2. [Drei Arten von Paketen](#drei-arten-von-paketen)

## Die Seiten dieses Teils

| Seite | Was sie beantwortet |
|---|---|
| [Die mitgelieferten Erweiterungen](die-mitgelieferten-erweiterungen.md) | Was mit einer neuen Installation kommt und was ausgeschaltet ist |
| [Composer und Packagist](composer-und-packagist.md) | Wie Pakete installiert und veröffentlicht werden |
| [Wie Pakete versioniert werden](wie-pakete-versioniert-werden.md) | Versionsnummern, dauerhafte Tags, die Einschränkung `~` |
| [Seitenpakete und der Paketserver](seitenpakete-und-der-paketserver.md) | Fertige Websites für den Einrichtungsassistenten |
| [Themes und Designs](themes-und-designs.md) | Themes für Websites und Admin-Designs als Pakete |
| [Eine Erweiterung finden](eine-erweiterung-finden.md) | Wo Sie suchen und wie Sie beurteilen, was Sie finden |
| [Kompatibilität und Abhängigkeiten](kompatibilitaet-und-abhaengigkeiten.md) | Prüfen, ob ein Paket zu Ihrer Installation passt |

## Drei Arten von Paketen

| Art | Installiert mit | Beispiel |
|---|---|---|
| Composer-Paket (Kernel, Erweiterung, Theme, Server) | `composer require` oder einem Git-Klon | `se7enxweb/exponential`, `se7enxweb/eztags` |
| Seitenpaket (`.ezpkg`) | Dem Einrichtungsassistenten, dem Kickstarter oder der Konsole | Das Seitenpaket 7x Simple |
| Fork eines Ursprungspakets | Composer, das den ursprünglichen Namen ersetzt | Die Forks von Symfony, Twig und Doctrine der Platform-Linie |

## Verwandte Seiten

- [Ökosystem: hier beginnen](../oekosystem-hier-beginnen.md)
- [Die Standardverteilung der Erweiterungen](../../../funktionen/6.0/standard-extension-auslieferung.md)
- [Erweiterungsentwicklung](../../erweiterungsentwicklung/README.md)
- [Seitenpakete](../../seitenpakete/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
