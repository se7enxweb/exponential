---
title: Veröffentlichte Versionen ändern sich nie
audience: press
category: press-media
order: 104
summary: Warum ein Release von Exponential dauerhaft ist, sobald es getaggt ist - was das Projekt dazu sagt, warum Packagist es so macht, was geschieht, wenn ein Release einen Fehler hat, und was das für eine genaue Berichterstattung und für Korrekturen bedeutet.
keywords: unveränderlich, Tag, Release, dauerhaft, Packagist, Korrektur, nächste Version, neu erstellen, Richtigstellung, Verlässlichkeit, Version festlegen, immutable, tag, release, permanent, fix, next version, re-cut, correction, reliability, pinning
type: help
---
# Veröffentlichte Versionen ändern sich nie

Diese Seite ist für Reporter und Creator, die über Releases, Korrekturen und die Verlässlichkeit von Exponential
schreiben. Eine Regel prägt, wie das Projekt veröffentlicht: Eine Version, die einmal erschienen ist, wird danach nie
mehr verändert. Wer die Regel kennt, beschreibt Korrekturen richtig, und sie erklärt, warum ein Beitrag über eine
Version korrekt bleibt.

## Inhalt

1. [Die Regel in den Worten des Projekts](#die-regel-in-den-worten-des-projekts)
2. [Warum das so ist](#warum-das-so-ist)
3. [Was geschieht, wenn ein Release einen Fehler hat](#was-geschieht-wenn-ein-release-einen-fehler-hat)
4. [Was das für Ihre Berichterstattung bedeutet](#was-das-für-ihre-berichterstattung-bedeutet)
5. [Formulierungen, die Sie verwenden und vermeiden sollten](#formulierungen-die-sie-verwenden-und-vermeiden-sollten)

## Die Regel in den Worten des Projekts

| Aussage | Quelle |
|---|---|
| „A published tag is never changed: a fix for a problem in `v6.0.14` or an earlier tag reaches you as the next release, not as a corrected copy of the old one.“ (Ein veröffentlichter Tag wird nie geändert: Eine Korrektur kommt als nächstes Release, nicht als korrigierte Kopie des alten.) | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| „A release tag never changes. Once published, a tag and the package version Packagist built from it are permanent; a fix always arrives as the next version.“ (Ein Release-Tag ändert sich nie; eine Korrektur kommt immer als nächste Version.) | [Den Code beziehen](../../installation/03-den-code-beschaffen.md#31-versionen-tags-und-branches) |
| „If a published version is wrong, do not move or delete its tag. Publish the next version with the fix and say in its notes what was wrong with the previous one.“ (Ist eine veröffentlichte Version fehlerhaft, verschieben oder löschen Sie ihren Tag nicht; veröffentlichen Sie die nächste Version mit der Korrektur.) | [Leitfaden für Erweiterungen, Release](../../anleitungen/extensions.md#7-die-extension-veröffentlichen) |
| „A tag is permanent: a bad release is corrected by the next one, never re-cut.“ (Ein Tag ist dauerhaft: Ein fehlerhaftes Release wird durch das nächste korrigiert, nie neu erstellt.) | [Release Notes von Velocity](../../../changelogs/extensions/exponential-velocity.md) |

Die Seite der Foundation nennt die Regel unter den Zusagen des Projekts im Abschnitt
[Releases und Versionsnummern](../wer-ist-die-exponential-foundation.md#releases-und-versionsnummern).

## Warum das so ist

Jedes Paket von Exponential wird über Packagist veröffentlicht, das Paketverzeichnis von Composer. Der Leitfaden für
Erweiterungen erklärt den technischen Grund: „Packagist caches what it read, so a moved tag produces two different
packages under one version number“ – Packagist speichert, was es gelesen hat, also erzeugt ein verschobener Tag zwei
verschiedene Pakete unter einer Versionsnummer ([Leitfaden für Erweiterungen](../../anleitungen/extensions.md#7-die-extension-veröffentlichen)).
Zwei verschiedene Dateisätze unter einer Nummer würden es unmöglich machen zu wissen, was auf einer Website läuft. Weil
die Tags fest bleiben, können Sie „einen Tag festlegen und sich darauf verlassen, jedes Mal dieselben Dateien zu
bekommen, und genau das macht eine festgelegte Version zur richtigen Wahl für die Produktion“
([Den Code beziehen](../../installation/03-den-code-beschaffen.md#31-versionen-tags-und-branches)).

## Was geschieht, wenn ein Release einen Fehler hat

Die dokumentierte Antwort ist immer die nächste Version. Ein echtes Beispiel: Die Release Notes von Velocity sagen,
dass drei Tags keine Release-Seite haben, `v0.0.4.21`, `v0.0.4.22` und `v0.0.4.26`, weil „ihre Builds kein brauchbares
Artefakt erzeugt haben; sie sind auf Packagist weiterhin installierbare Versionen, wechseln Sie also zur nächsthöheren
Version“ ([Release Notes von Velocity](../../../changelogs/extensions/exponential-velocity.md)). Die Tags blieben
bestehen, und die Korrektur kam mit späteren Versionen.

## Was das für Ihre Berichterstattung bedeutet

| Situation | Wie Sie darüber berichten |
|---|---|
| In 6.0.14 wird ein Fehler gefunden | „Die Korrektur kommt in einer späteren Version“; nie „6.0.14 wurde aktualisiert“ |
| Eine Sicherheitskorrektur wird veröffentlicht | „Behoben in Version X; frühere Versionen werden durch ein Update behoben“ ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)) |
| Eine Version hatte ein Paketierungsproblem | Nennen Sie die Version und die spätere Version, die sie ersetzt hat, mit den Release Notes als Quelle |
| Sie haben über den Inhalt einer Version geschrieben | Ihre Beschreibung bleibt gültig; die Dateien hinter dieser Nummer ändern sich nicht |

Zwei Folgen für die Genauigkeit:

- Eine Versionsnummer ist ein genauer Verweis. „Exponential 6.0.14“ bezeichnet genau einen Dateisatz; drucken Sie
  deshalb immer die vollständige Nummer ([Wie Versionsnummern funktionieren](wie-versionsnummern-funktionieren.md)).
- Arbeit nach einem Tag gehört zur nächsten Version, nie nachträglich zur alten. Änderungen nach dem 7. Juni 2026
  gehören zur Produktlinie 6.0.15, nicht zu 6.0.14 ([Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md)).

## Formulierungen, die Sie verwenden und vermeiden sollten

| Vermeiden Sie | Verwenden Sie |
|---|---|
| „ein gepatchtes 6.0.14“ | „die nächste Version nach 6.0.14“ |
| „das Release wurde zurückgezogen“ | Nur, wenn eine Quelle das sagt; die dokumentierte Praxis ist, Tags bestehen zu lassen |
| „6.0.14 wurde neu veröffentlicht“ | So arbeitet das Projekt nicht; prüfen Sie die Liste der Tags ([Ein Release selbst überprüfen](ein-release-selbst-pruefen.md)) |
| „stillschweigend behoben“ | „behoben in Version X, wie ihr Changelog aufführt“ |

## Verwandte Seiten

- [Wie Versionsnummern funktionieren](wie-versionsnummern-funktionieren.md)
- [Über Sicherheitskorrekturen berichten](../../../eng-us/help/press/covering-security-fixes.md)
- [Korrekturen und Aktualisierungen](korrekturen-und-aktualisierungen.md) (dasselbe Prinzip für Ihre eigenen Beiträge)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
