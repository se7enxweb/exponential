---
title: Die Community für Entwickler
audience: community, developer, app-creator, website-creator
category: community
order: 65
summary: Was die Exponential-Community Entwicklern bietet, die Websites, Erweiterungen und Integrationen bauen, und wie Entwickler mitmachen - Lernressourcen, das Buch zur Erweiterungsentwicklung, Git-Abläufe, Pull Requests, Reviews, Pakete veröffentlichen und in technischen Diskussionen helfen.
keywords: Entwickler, PHP, Templates, Erweiterungen, Git, Pull Requests, Composer, Packagist, Code-Review, Erweiterungsentwicklung, Kernel, Community, developers, extensions, code review, extension development, kernel, community
type: help
---
# Die Community für Entwickler

Entwickler bauen das meiste von dem, was die Community teilt: Websites, Erweiterungen, Korrekturen und Werkzeuge.

## Inhalt

1. [Lernen](#lernen)
2. [Mit dem Code arbeiten](#mit-dem-code-arbeiten)
3. [Etwas zurückgeben](#etwas-zurückgeben)
4. [Regeln, denen jeder Entwickler begegnet](#regeln-denen-jeder-entwickler-begegnet)

## Lernen

| Ressource | Was sie ist |
|---|---|
| [Mit Git entwickeln](../../mit-git-entwickeln.md) | Git-Klone des Kernels und der Erweiterungen installieren und damit arbeiten |
| [Erweiterungsentwicklung](../../erweiterungsentwicklung/README.md) | Ein Buch in acht Teilen, vom Nutzer über den Erweiterungsentwickler zum Maintainer |
| [Template-Sprache](../../template-sprache/README.md) und [Template-Operatoren](../../template-operatoren/README.md) | Die Referenz der Template-Sprache |
| [Standards](../../../standards/README.md) | Coding-Standards, Namenspräfixe, Sicherheit, Tests |
| Schulungsbücher | „drei ursprüngliche Schulungsbücher für Entwickler (zusammen über 700 Seiten)“ ([Orte der Community](../../wer-ist-die-exponential-foundation.md#orte-der-community)) |
| [Anwendungsentwickler](../../ersteller/anwendungsentwickler/anwendungsentwickler-hier-beginnen.md), [Website-Ersteller](../../ersteller/website-ersteller/website-ersteller-hier-beginnen.md) | Anwendungen und Websites bauen |

## Mit dem Code arbeiten

- Installieren Sie aus Git-Klonen, damit eine Korrektur „nur einen Branch in Ihrem Fork und einen Pull Request
  entfernt“ ist ([Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)).
- Beobachten Sie die Repositorys, von denen Sie abhängen; lesen Sie die Changelogs ([Auf dem Laufenden bleiben](../beitreten/auf-dem-laufenden-bleiben.md)).
- Testen Sie Ihre Erweiterungen vor einem Release gegen `main` ([Releases testen](../mitwirken/releases-testen.md)).

## Etwas zurückgeben

| Beitrag | Seite |
|---|---|
| Einen Fehler beheben oder einen Test im Kernel hinzufügen | [Code beitragen](../mitwirken/code-beitragen.md) |
| Den Pull Request einer anderen Person prüfen | [Reviews geben und erhalten](../mitwirken/reviews-geben-und-erhalten.md) |
| Eine Erweiterung veröffentlichen | [Erweiterungen und Themes teilen](../mitwirken/erweiterungen-und-themes-teilen.md) |
| Einer Erweiterung, der eine `composer.json` fehlt, eine hinzufügen, ein Aufruf an Freiwillige auf der Roadmap | [Roadmap](../../projekt/roadmap.md) |
| Technische Fragen beantworten | [Fragen beantworten](../anderen-helfen/fragen-beantworten.md) |
| Einen neuen Entwickler betreuen | [Mentoring](../anderen-helfen/mentoring.md) |

## Regeln, denen jeder Entwickler begegnet

- Commit-Nachrichten mit `Added:`, `Updated:`, `Fixed:`, `Removed:` oder `Renamed:`; die Manifestzeile für jede
  geänderte Datei; Squash und Rebase (`CONTRIBUTING.md`).
- Neuer Code verwendet das Präfix `exp`; vorhandene `eZ*`-Bezeichner werden nie umbenannt
  ([Namenspräfixe](../../../standards/namenspraefixe-sevenx-und-exp.md)).
- Das CLA für Beiträge zum Kernel ([Das CLA erklärt](../mitwirken/das-cla-erklaert.md)).
- KI-gestützter Code: Sie sind dafür verantwortlich und sagen, wenn ein wesentlicher Teil generiert wurde
  (`CODE_OF_CONDUCT.md`).

## Verwandte Seiten

- [Wege durch die Community](README.md)
- [Argumente für Entwickler](../../fuersprecher/gespraechspunkte-fuer-entwickler.md)
- [Ökosystem: hier beginnen](../../oekosystem/oekosystem-hier-beginnen.md)
- [Mit Git entwickeln](../../mit-git-entwickeln.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
