---
title: "Einen Fork synchron halten"
audience: designer, developer
category: theme-lifecycle
order: 360
summary: Die Korrekturen des ursprünglichen Themes regelmäßig in Ihren Fork holen - ein Remote upstream, fetch und merge, Konflikte in Templates lösen und die eigenen Änderungen klein halten, damit das Zusammenführen einfach bleibt.
keywords: Fork synchronisieren, Remote upstream, git fetch, git merge, Konflikte, Rebase, Downstream, aus dem Original zusammenführen, fork sync, upstream remote, conflicts, rebase, downstream, merge upstream
type: help
---
# Einen Fork synchron halten

Diese Seite ist für Designer, die einen Fork betreuen ([Ein Theme forken](ein-theme-forken.md)). Ein Fork, der nie aus
seinem Ursprung zusammenführt, verpasst jede Korrektur, die dort gemacht wird, auch Sicherheitskorrekturen.

## Schritt für Schritt

1. **Fügen Sie das Original als Remote hinzu**, einmalig:

   ```bash
   git remote add upstream https://github.com/originalvendor/theme.git
   git fetch upstream --tags
   ```

2. **Führen Sie regelmäßig zusammen** (bei jedem Release des Originals oder monatlich):

   ```bash
   git fetch upstream --tags
   git switch main
   git merge upstream/main
   ```

3. **Lösen Sie Konflikte**: Behalten Sie in Templates die Korrekturen des Originals (Maskierung, Barrierefreiheit) und
   wenden Sie Ihre Änderung darauf erneut an. Lesen Sie jeden Konflikt; nehmen Sie nie blind eine Seite.
4. **Testen** Sie wie in [Upgrade-Test Schritt für Schritt](upgrade-test-schritt-fuer-schritt.md).
5. **Veröffentlichen** Sie Ihren Fork mit einem Changelog-Eintrag: `Updated: merged <original> <version>`.

Führen Sie einen Branch, den andere geholt haben, lieber zusammen, als ihn zu rebasen: Ein Rebase schreibt veröffentlichte
Historie um.

## Das Zusammenführen einfach halten

- Halten Sie Ihre Änderungen wo möglich in eigenen Dateien (ein Override eines Templates statt einer Bearbeitung).
- Schicken Sie allgemeine Korrekturen an das Original ([An Upstream zurückgeben](an-upstream-zurueckgeben.md)); jede
  angenommene ist ein Konflikt, den Sie nie haben werden.

## Verwandte Seiten

- [Mit Git entwickeln](../../mit-git-entwickeln.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
