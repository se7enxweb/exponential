---
title: "An Upstream zurückgeben"
audience: designer, developer
category: theme-lifecycle
order: 340
summary: Korrekturen und Verbesserungen an die Themes, Erweiterungen und das Projekt Exponential zurückschicken, auf denen Sie aufbauen - was Sie beitragen, wie Sie eine Änderung vorbereiten und warum es sich auszahlt, durch weniger Wartung für Sie und gemeinsames Wachstum für die Community.
keywords: zurückgeben, Upstream, Pull-Request, Community, gemeinsames Wachstum, Korrekturen, Projekt Exponential, Erweiterungen, contribute back, upstream, pull request, community, shared growth, fixes, extensions
type: help
---
# An Upstream zurückgeben

Diese Seite ist für Designer, die für ihre eigene Arbeit das Theme oder die Erweiterung eines anderen oder die Templates
des Kernels geändert haben. Jede Änderung, die Sie privat behalten, müssen Sie selbst durch jedes Upgrade tragen. Jede
Änderung, die im Original angenommen wird, pflegen alle.

## Was Sie beitragen

| Beitragen | Privat behalten |
|---|---|
| Fehlerkorrekturen, Sicherheitskorrekturen | Kundenspezifisches Branding |
| Verbesserungen der Barrierefreiheit und der CSP | Inhalte und Daten |
| Übersetzungen | Alles mit Zugangsdaten oder Kundenangaben |
| Kompatibilität mit neuen Versionen von PHP oder Exponential | |
| Dokumentation | |

## Wohin

- **Themes und Erweiterungen von 7x**: die Repositories unter `github.com/se7enxweb`.
- **Exponential selbst**: `github.com/se7enxweb/exponential`; seine Dokumentation liegt in `doc/` und ist unter der GFDL
  frei, also sind Verbesserungen der Dokumentation dort ebenfalls willkommen.
- **Themes anderer Autoren**: deren Repositories; lesen Sie zuerst deren Hinweise zum Beitragen.

Das Projekt Exponential führt jeden Pull-Request zusammen und korrigiert, was zu korrigieren ist, in eigenen Commits
darauf, sodass ein Beitrag nicht in einem langen Review verloren geht; das ist kein Grund, weniger sorgfältige Arbeit zu
schicken.

## Schritt für Schritt

1. Nehmen Sie die Änderung auf einem Branch Ihres Forks vor, ein Thema pro Branch.
2. Testen Sie sie auf einer aktuellen Installation.
3. Schreiben Sie eine Commit-Nachricht mit Präfix und Grund ([Changelog-Einträge](../veroeffentlichung/changelog-praefixe.md)).
4. Öffnen Sie einen Pull-Request ([Pull-Requests an fremde Themes](pull-requests-an-fremde-themes.md)). Eine Änderung an Exponential
   selbst, einschließlich der Templates und Designs des Kernels, wird erst zusammengeführt, wenn Sie das Contributor
   Licensing Agreement unterzeichnet haben, das 7x und die Exponential Foundation für jeden Beitrag zum Kernel verlangen.
   **Mit der Unterzeichnung und Einreichung übertragen Sie das Urheberrecht an Ihrem Beitrag auf 7x, das dessen Eigentümer
   wird, und geben die Kontrolle darüber ab:** 7x darf ihn auf jede Weise nutzen, ändern, neu lizenzieren und verbreiten,
   auch unter anderen Lizenzen und in kommerziellen Produkten, ohne Sie zu fragen oder zu bezahlen, und Sie können ihn
   nicht zurückziehen oder an Bedingungen knüpfen
   ([Übertragung des Urheberrechts (CLA)](../../../../../CONTRIBUTING.md#copyright-assignment-cla)); Ihre eigenen Themes
   sind davon nicht betroffen.
5. Nachdem sie zusammengeführt wurde, lassen Sie Ihre private Kopie der Änderung beim nächsten Upgrade fallen.

## Verwandte Seiten

- [Mit Git entwickeln](../../mit-git-entwickeln.md)
- [Mit der Community arbeiten](../../fuehrung/mit-der-community-arbeiten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
