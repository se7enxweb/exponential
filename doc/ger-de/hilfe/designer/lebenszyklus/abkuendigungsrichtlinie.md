---
title: "Eine Abkündigungsrichtlinie"
audience: designer, developer
category: theme-lifecycle
order: 220
summary: Entscheiden und veröffentlichen, wie Ihr Theme Einstellungen, Templates, Ansichtsmodi und Operatoren in den Ruhestand schickt - ankündigen, weiter funktionieren lassen, warnen, in einem späteren Schritt entfernen -, damit Nutzer Zeit zum Anpassen haben.
keywords: Abkündigung, Abkündigungsrichtlinie, außer Dienst stellen, ankündigen, Entfernung, Einstellungen, Templates, Operatoren, Ankündigungsfrist, deprecation, deprecation policy, retire, announce, removal, settings, templates, operators, notice period
type: help
---
# Eine Abkündigungsrichtlinie

Diese Seite ist für Designer, deren Theme Nutzer hat, die auf seinen Einstellungen, Templates oder Operatoren aufgebaut
haben. Sie ohne Ankündigung zu entfernen, zerstört die Websites dieser Nutzer.

## Eine Richtlinie in vier Schritten

1. **Ankündigen**: Das Changelog sagt „Deprecated: <thing>, use <replacement>; it will be removed in <version or date>“.
2. **Weiter funktionieren lassen**: Das Alte funktioniert noch, am besten über das Neue.
3. **Warnen**: Wo möglich, schreiben Sie einen Hinweis ins Log, wenn das Alte verwendet wird (zum Beispiel
   `eZDebug::writeWarning()` in einem Operator), damit Entwickler ihn in der Debug-Ausgabe sehen.
4. **Entfernen** im angekündigten Release, mit einem Eintrag `Removed:` und dem Upgrade-Schritt.

Das Produkt tut dasselbe: `settings/design.ini` lässt `CSSFileList[]` und `JavaScriptList[]` weiter funktionieren und
kennzeichnet sie als „DEPRECATED in favor of [Frontend|Backend]CSSFileList[]“ und „...JavaScriptList[]“.

## Schritt für Schritt: die Richtlinie schreiben

1. Wählen Sie die Ankündigungsfrist: eine Zahl von Releases oder eine Zeitspanne, die Sie einhalten können.
2. Schreiben Sie die vier Schritte mit Ihrer Frist in das README oder in `doc/deprecation.md`.
3. Führen Sie dort die aktuellen Abkündigungen auf, jede mit ihrem Ersatz und der geplanten Entfernung.

## Verwandte Seiten

- [Eine Einstellung oder ein Template abkündigen](eine-einstellung-oder-ein-template-abkuendigen.md)
- [Inkompatible Änderungen](inkompatible-aenderungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
