---
title: Upstream-Projekte und Forks
audience: ecosystem, developer, partner, advocate
category: ecosystem
order: 35
summary: Die PHP-Projekte, auf denen das Ökosystem von Exponential steht und denen es etwas zurückgibt - PHP, Composer, Zeta Components, Symfony, Twig, Doctrine, die Projekte von Netgen, der Webserver Qbix, AdminNeo und die Frontend-Bibliotheken -, wofür jedes verwendet wird, die Forks, die 7x pflegt, und wie Sie respektvoll mit Ursprungsprojekten umgehen.
keywords: Upstream, Ursprungsprojekte, Forks, Zeta Components, Symfony, Twig, Doctrine, Media Site, Qbix, AdminNeo, jQuery, TinyMCE, zurückgeben, Pull-Requests an Upstream, upstream, forks, give back, pull requests upstream
type: help
---
# Upstream-Projekte und Forks

## Inhalt

1. [Worauf Exponential steht](#worauf-exponential-steht)
2. [Warum Forks](#warum-forks)
3. [Nennung und Respekt](#nennung-und-respekt)

## Worauf Exponential steht

| Projekt | Verwendet für | Zurückgegeben | Quelle |
|---|---|---|---|
| PHP | Die Sprache | Unterstützung für jede neue PHP-Version | `README.md` |
| Composer und Packagist | Installation des Kernels und jeder Erweiterung | 219 von se7enxweb veröffentlichte Pakete | `composer.json`, Packagist |
| Zeta Components | Die Bibliotheken des Kernels (Cache, Datenbank, Feed, Bildumwandlung ...) | "Forked and patched 47+ Zeta Components repos", über 47 Repositorys geforkt und gepatcht; "14 security PRs submitted upstream to zetacomponents", 14 Sicherheits-Pull-Requests an zetacomponents | Profil-README von se7enxweb |
| Symfony, Twig, Doctrine | Die Platform-Linie | Gepflegte Forks für PHP 8.4 und 8.5 | [Das Ökosystem der Exponential-Plattform](../../../geschichte/oekosystem.md) |
| Upstream-Layouts-Pakete, Media Site, Site API | Exponential Layouts und Nexus | Portierungen auf den Kernel von Exponential; ein Fork von layouts-core | `README.md` |
| Webserver Qbix | Die Engine von Velocity | Ein darauf gebauter Produktionsserver | README von Velocity |
| AdminNeo | Der Editor für Datenbankquellen | | [sevenx_dse](../../../funktionen/6.0/erweiterungen/sevenx_dse.md) |
| jQuery, TinyMCE, Bootstrap Icons | Die Skripte, der Editor und die Symbole der Administration | | [Oktober 2026](../../../geschichte/2026/2026-10.md) |

Quelltabelle: [Der PHP-Stack, auf dem es steht und dem es etwas zurückgibt](../../wer-ist-die-exponential-foundation.md#der-php-stack-auf-dem-es-steht-und-dem-es-etwas-zurückgibt).

## Warum Forks

Viele Ursprungspakete des alten Stacks wurden für aktuelles PHP nicht mehr gepflegt. Damit Websites weiterlaufen, hält
7x unter dem Anbieter se7enxweb Forks bereit, die sie reparieren, und führt in der
[Paketübersicht](../../../spezifikationen/6.0/platform-paketzuordnung.md) auf, welcher Fork welches Ursprungspaket ersetzt.
Was sich für Anwender eines Forks geändert hat, steht unter
[Paket-Forks und umbenannte Befehle](../../../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md).

## Nennung und Respekt

- Arbeit aus anderen Projekten wird genannt, zum Beispiel die LoveStack-Funktionen "with full credit to Mugo Web", mit
  voller Nennung von Mugo Web (`README.md`).
- Korrekturen gehen an das Ursprungsprojekt, wo dieses aktiv ist.
- Wenn Sie forken, folgen Sie [Wann Sie forken](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/wann-forken.md) und
  [Ethisch umbenennen](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/ethisches-rebranding.md).
- Sprechen Sie fair über andere Projekte ([Andere Projekte und Unternehmen respektieren](../../fuersprecher/andere-projekte-respektieren.md)).

## Verwandte Seiten

- [Der PHP-Stack](README.md)
- [Begleitprojekte und andere Unternehmen](../organisationen/begleitprojekte-und-andere-unternehmen.md)
- [Nennungen und Copyright-Vermerke](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/namensnennung-und-urheberrechtshinweise.md)
- [Lizenzen und Kompatibilität](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/lizenzen-und-kompatibilitaet.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
