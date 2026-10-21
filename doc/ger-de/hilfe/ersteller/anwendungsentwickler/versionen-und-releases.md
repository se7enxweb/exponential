---
title: Versionen und Releases
audience: app-creator, developer
category: app-creation
order: 460
summary: Eine Erweiterung so veröffentlichen, wie die eigenen Erweiterungen von Exponential veröffentlicht werden - die nächste Nummer aus dem Veröffentlichten wählen, ezinfo.php und extension.xml vor dem Tag hochzählen, annotierte Tags, Release Notes und warum eine veröffentlichte Version nie geändert wird.
keywords: Version, Release, Tag, semantische Versionierung, Changelog, Release Notes, unveränderliche Tags, Anwendungsentwickler, version, release, tag, semantic versioning, ezinfo.php, extension.xml, changelog, release notes, immutable tags, GitHub release, Packagist, app creator
type: help
---
# Versionen und Releases

Diese Seite richtet sich an Anwendungsentwickler, die Releases veröffentlichen. Die Regeln sind dieselben, denen die
eigenen Erweiterungen von Exponential folgen; sie sorgen dafür, dass Installationen erkennen können, welchen Code sie
ausführen.

![Der Bereich Erweiterungen auf der Seite „Über“: jede Erweiterung mit Version, Lizenz und Website](../../../../eng-us/help/images/creators/about-extensions.webp)

## Inhalt

1. [Eine veröffentlichte Version ist endgültig](#eine-veröffentlichte-version-ist-endgültig)
2. [Die Nummer wählen](#die-nummer-wählen)
3. [Das Release Schritt für Schritt](#das-release-schritt-für-schritt)
4. [Release Notes](#release-notes)

## Eine veröffentlichte Version ist endgültig

Ein Tag, das in ein öffentliches Repository gepusht wurde, und die daraus erzeugte Packagist-Version lassen sich nicht
korrigieren: Installationen, die sie aufgelöst haben, behalten ihren Inhalt. Verschieben, löschen oder erstellen Sie ein
veröffentlichtes Tag niemals neu; wenn ein Release fehlerhaft ist, veröffentlichen Sie das nächste und schreiben in
dessen Notes, was am vorherigen falsch war.

## Die Nummer wählen

- Listen Sie zuerst auf, was veröffentlicht ist: `git fetch --tags`,
  `git tag -l 'v*' --sort=version:refname | tail -5` und die Releases beim Repository-Host. Eine einfache lexikalische
  Sortierung setzt `1.0.10` vor `1.0.9`; sortieren Sie immer nach Version.
- Für Fehlerbehebungen zählt nur die letzte Stelle weiter: Auf `1.0.9` folgt `1.0.10`.
- Eine Änderung von Verhalten oder API, auf der andere aufbauen, bekommt eine höhere Stelle, nach Entscheidung des
  Betreuers.

## Das Release Schritt für Schritt

1. Arbeitsverzeichnis sauber, Tests bestehen ([Anwendungen testen](anwendungen-testen.md)).
2. Zählen Sie die Version in `ezinfo.php` (`'Version' => "1.0.10"`), `extension.xml` (`<version>1.0.10</version>`) und
   jedem anderen Versionsfeld, das das Repository führt, in einem einzigen Commit hoch.
3. Schreiben Sie die Release Notes (Changelog).
4. Versehen Sie diesen Commit mit einem annotierten Tag (`v1.0.10`) und pushen Sie das Tag.
5. Erstellen Sie das Release beim Repository-Host mit den Notes.
6. Prüfen Sie, dass Packagist die Version anzeigt ([Composer und Packagist](composer-und-packagist.md)).
7. Installationen aktualisieren ihre Versionsbedingung, und die Seite „Über“ (`/ezinfo/about`) zeigt die neue Nummer.

Taggen Sie niemals zuerst und zählen erst danach hoch: Das Tag würde für immer die alte Nummer tragen.

## Release Notes

Gruppieren Sie Änderungen unter **Added**, **Updated**, **Removed**, **Renamed** (den Kategorien, die die Changelogs des
Kernels verwenden), sagen Sie, was eine Website tun muss (neue Einstellungen, Schemaschritte, Cron-Zeilen), und benennen
Sie inkompatible Änderungen klar.

## Verwandte Seiten

- [Als Erweiterung paketieren](als-erweiterung-paketieren.md)
- [Standard für Versionsverwaltung und Releases](../../../standards/versionsverwaltung-und-releases.md)
- [Kompatibilität, Releases und Deployment](../../../standards/architektur/kompatibilitaet-releases-deployment.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
