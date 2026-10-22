---
title: Auf dem Laufenden bleiben
audience: community, admin, developer, hosting
category: community
order: 16
summary: Wie Community-Mitglieder bei Exponential auf dem Laufenden bleiben - Releases und Changelogs, die Chronik, GitHub-Benachrichtigungen, Packagist, der Commit-Feed auf der Einstiegsseite für Entwickler und wo Ankündigungen diskutiert werden.
keywords: Neuigkeiten, Releases, Changelog, Historie, Chronik, GitHub Watch, Benachrichtigungen, Packagist, Commit-Feed, Ankündigungen, aktuell bleiben, Upgrade, news, releases, history, notifications, commit feed, announcements, staying current
type: help
---
# Auf dem Laufenden bleiben

Die Quellen nennen keine Mailingliste für Exponential. Dies sind die dokumentierten Wege, um zu verfolgen, was
geschieht.

## Inhalt

1. [Was Sie verfolgen sollten](#was-sie-verfolgen-sollten)
2. [Releases und Versionsnummern](#releases-und-versionsnummern)
3. [Eine Routine von zehn Minuten im Monat](#eine-routine-von-zehn-minuten-im-monat)

## Was Sie verfolgen sollten

| Quelle | Was sie Ihnen sagt | Wie |
|---|---|---|
| Die Changelogs | Was jedes 6.0.x-Release geändert hat | [Changelog 6.0.14](../../../../changelogs/6.0/6.0.14.md) und die anderen in `doc/changelogs/6.0/` |
| Die Chronik | Monat für Monat, was im Kernel und seinem Ökosystem geschehen ist | [Die Geschichte von Exponential](../../../geschichte/README.md) |
| Die Ökosystem-Karte | Die Repositorys rund um den Kernel und ihre Änderungen | [Das Ökosystem der Exponential-Plattform](../../../geschichte/oekosystem.md) |
| GitHub | Neue Issues, Pull Requests und Releases | Mit „Watch“ das Repository [se7enxweb/exponential](https://github.com/se7enxweb/exponential) beobachten, oder nur seine Releases |
| Der Commit-Feed | Die neuesten Commits des Kernels | Die Einstiegsseite für Entwickler in der Hilfe zeigt sie; der Feed ist `github.com/se7enxweb/exponential/commits/main.atom` (`settings/exphelp.ini`, `[CommitFeedSettings]`) |
| Packagist | Veröffentlichte Versionen jedes Pakets | die Liste der Pakete des Anbieters, <https://packagist.org/packages/list.json?vendor=se7enxweb> |
| Die Foren | Diskussion über Releases und Pläne | [share.exponential.earth/forums](https://share.exponential.earth/forums) |
| Die Roadmap-Seite | Was geplant ist | [Roadmap](../../projekt/roadmap.md) |

## Releases und Versionsnummern

- Sicherheitskorrekturen werden auf `main` vorgenommen und mit dem nächsten 6.0.x-Release veröffentlicht; „Ein
  veröffentlichter Tag wird nie geändert“ (`SECURITY.md`). Um eine Korrektur zu erhalten, aktualisieren Sie auf das
  neueste Release.
- Nur die Linie 6.0 wird unterstützt; 5.x und älter nicht (`SECURITY.md`, „Supported Versions“).
- Wie eine Website auf ein neues Release wechselt, erklären [Mit Git aktualisieren](../../mit-git-aktualisieren.md) und
  [Wie Updates funktionieren](../../kunden/gesund-halten/wie-updates-funktionieren.md).

## Eine Routine von zehn Minuten im Monat

1. Lesen Sie die Release Notes aller Releases seit dem letzten Monat.
2. Überfliegen Sie die neuesten Themen im Forum nach Problemen, die zu Ihrer Einrichtung passen.
3. Prüfen Sie, ob Ihre Website das neueste 6.0.x-Release verwendet; planen Sie andernfalls das Upgrade.
4. Notieren Sie eine Sache, die Sie beitragen könnten: die Bestätigung eines Fehlers, eine Übersetzung, eine
   Hilfeseite.

## Verwandte Seiten

- [Der Community beitreten](README.md)
- [Wie Releases entstehen](../so-funktioniert-es/wie-releases-entstehen.md)
- [Releases testen](../mitwirken/releases-testen.md)
- [Über ein Release schreiben](../../fuersprecher/ueber-ein-release-schreiben.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
