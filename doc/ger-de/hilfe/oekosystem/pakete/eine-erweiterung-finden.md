---
title: Eine Erweiterung im Ökosystem finden
audience: ecosystem, developer, admin, website-creator, project-manager
category: ecosystem
order: 26
summary: Wo Sie nach einer Erweiterung für Exponential suchen - was schon installiert ist, die Repositorys von se7enxweb, Packagist, Projects und die Foren - und wie Sie eine Erweiterung beurteilen, bevor Sie sich auf sie stützen.
keywords: Erweiterung finden, suchen, Packagist, GitHub, Projects, Foren, Erweiterung bewerten, Pflege, Lizenz, Kompatibilität, verwaist, find extension, search, forums, evaluate extension, maintenance, license, compatibility, abandoned
type: help
---
# Eine Erweiterung im Ökosystem finden

## Inhalt

1. [Suchen Sie in dieser Reihenfolge](#suchen-sie-in-dieser-reihenfolge)
2. [Beurteilen, was Sie finden](#beurteilen-was-sie-finden)
3. [Wenn nichts passt](#wenn-nichts-passt)

## Suchen Sie in dieser Reihenfolge

| Schritt | Wo |
|---|---|
| 1 | Was Ihre Installation schon hat: Viele mitgelieferte Erweiterungen sind installiert, aber inaktiv ([Die mitgelieferten Erweiterungen](die-mitgelieferten-erweiterungen.md)) |
| 2 | Die Funktionsseiten der Erweiterungen: [Erweiterungen](../../../funktionen/6.0/erweiterungen/README.md) |
| 3 | Die Repositorys von se7enxweb auf [GitHub](https://github.com/se7enxweb) und die Pakete des Anbieters auf Packagist |
| 4 | [Projects](https://projects.exponential.earth/), wo Projekte und Erweiterungen der Community aufgeführt sind |
| 5 | Fragen Sie in den [Foren](https://share.exponential.earth/forums) ([Eine gute Frage stellen](../../gemeinschaft/beitreten/eine-gute-frage-stellen.md)) |

## Beurteilen, was Sie finden

| Prüfung | Warum |
|---|---|
| Die Lizenz (GPL 2 oder später ist die Empfehlung des Projekts) | Sie müssen sie mit Ihrer Website verwenden und weitergeben dürfen |
| Die unterstützten Versionen von Exponential und PHP in `composer.json` | Sie muss zu Ihrer Installation passen ([Kompatibilität und Abhängigkeiten](kompatibilitaet-und-abhaengigkeiten.md)) |
| Das Datum des letzten Releases und des letzten Commits | Eine alte Erweiterung braucht vielleicht eine Portierung |
| Offene Issues und wie sie beantwortet werden | Zeigt, ob jemand sie pflegt |
| Versionen in `ezinfo.php` und `extension.xml`, die zum Tag passen | Zeigt Disziplin bei Releases |
| Tests und Dokumentation | Geringeres Risiko |

## Wenn nichts passt

- Bauen Sie sie selbst und teilen Sie sie ([Im Ökosystem veröffentlichen](../personen/im-oekosystem-veroeffentlichen.md)).
- Übernehmen Sie eine verwaiste Erweiterung ([Eine verwaiste Erweiterung übernehmen](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/eine-verwaiste-erweiterung-uebernehmen.md)).
- Finanzieren Sie sie: Partner und Sponsoren können Funktionen anfragen oder sponsern
  ([Funktionswünsche und gesponserte Funktionen](../../partner/funktionswuensche-und-gesponserte-funktionen.md)).
- Planen Sie sie mit [Schätzen und Umfang festlegen mit Erweiterungen](../../fuehrung/schaetzen-und-abgrenzen-mit-erweiterungen.md).

## Verwandte Seiten

- [Pakete](README.md)
- [Erweiterungsentwicklung](../../erweiterungsentwicklung/README.md)
- [Wann Sie forken](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/wann-forken.md)
- [Erweitern ohne Fork](../../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/erweitern-ohne-zu-forken.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
