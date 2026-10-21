---
title: "Releases von Exponential verfolgen"
audience: designer, developer
category: theme-lifecycle
order: 40
summary: Wo Sie auf Änderungen achten, die ein Theme betreffen - die Changelogs in doc/changelogs, Release-Tags und GitHub-Releases des Repositorys, die Upgrade-Hinweise in doc/eng-us/bc, Projektneuigkeiten und RSS - und eine wöchentliche Routine.
keywords: Releases, Changelog, beobachten, GitHub-Releases, RSS, Upgrade-Hinweise, doc/eng-us/bc, Neuigkeiten, Routine, releases, changelog, watch, GitHub releases, upgrade notes, news, routine
type: help
---
# Releases von Exponential verfolgen

Diese Seite ist für Designer, die Themes betreuen. Änderungen am Kernel, an den Designs der Administration und an den
mitgelieferten Erweiterungen können Ihre Templates betreffen; früh davon zu wissen, erspart Notfallkorrekturen.

## Wo Änderungen festgehalten werden

| Quelle | Was sie enthält |
|---|---|
| `doc/changelogs/6.0/<version>.md` im Repository | Das Changelog jedes Releases; das in Entwicklung (6.0.15) wird fortgeschrieben, sobald Arbeit hinzukommt |
| `doc/eng-us/bc/6.0/` | Upgrade-Hinweise zu Änderungen, die Handeln verlangen, zum Beispiel [Entfernung von YUI](../../../../eng-us/bc/6.0/yui-removal.md) |
| `doc/eng-us/features/6.0/` | Was eine neue Funktion tut, zum Beispiel [jQuery 4 in der Administration, und YUI ist weg](../../../../eng-us/features/6.0/jquery4-and-yui-removal.md) |
| Releases und Tags von `se7enxweb/exponential` und der Erweiterungen, die Sie verwenden | Versionen, wie veröffentlicht |
| Neuigkeiten und RSS von [projects.exponential.earth](https://projects.exponential.earth/) (`/rss/feed/projects-news`, `/rss/feed/git`) | Ankündigungen und neueste Git-Änderungen |
| Blogs auf [share.exponential.earth](https://share.exponential.earth/) | Release-Ankündigungen |

## Schritt für Schritt: eine wöchentliche Routine

1. Beobachten Sie die Releases des Repositorys auf GitHub (**Watch > Custom > Releases**) oder abonnieren Sie die
   RSS-Feeds oben.
2. Lesen Sie einmal pro Woche, was sich im aktuellen Changelog seit Ihrem letzten Blick geändert hat.
3. Notieren Sie jeden Punkt, der Templates, Design-Einstellungen, JavaScript-Bibliotheken, die Content Security Policy
   oder PHP-Versionen betrifft ([Ein Changelog als Designer lesen](ein-changelog-als-designer-lesen.md)).
4. Öffnen Sie für jeden ein Issue im Repository Ihres Themes.
5. Testen Sie vor Ihrem nächsten Release gegen das neueste Exponential ([Upgrade-Test Schritt für Schritt](upgrade-test-schritt-fuer-schritt.md)).

## Verwandte Seiten

- [Upgraden](../../../anleitungen/aktualisieren.md)
- [Ein Wartungskalender](wartungskalender.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
