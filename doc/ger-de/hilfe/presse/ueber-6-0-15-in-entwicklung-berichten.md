---
title: Über 6.0.15 berichten, solange es in Entwicklung ist
audience: press
category: press-media
order: 106
summary: Wie Sie über die Linie Exponential 6.0.15 berichten, bevor sie ein Release-Tag hat - was die Quellen darüber sagen, wie Sie die Arbeit als in Entwicklung kennzeichnen, welche Formulierungen genau sind und warum kein Datum genannt werden darf.
keywords: 6.0.15, Entwicklungslinie, unveröffentlicht, main-Branch, dev-main, Vorschau, in Entwicklung, kein Veröffentlichungsdatum, Velocity, admin4, Audit-Trail, Exponential Layouts, Exponential Intelligence, development line, unreleased, main branch, preview, in development, no release date, audit trail
type: help
---
# Über 6.0.15 berichten, solange es in Entwicklung ist

Diese Seite ist für Reporter und Creator, die schon jetzt über die Arbeit in der Linie Exponential 6.0.15 schreiben
wollen, bevor sie veröffentlicht ist. Das ist erlaubt, und die Arbeit ist ausführlich dokumentiert. Wichtig ist, klar zu
sagen, dass sie in Entwicklung ist, und kein Veröffentlichungsdatum zu nennen, weil keine Quelle eines nennt.

## Inhalt

1. [Was die Quellen über 6.0.15 sagen](#was-die-quellen-über-6015-sagen)
2. [Was in der Linie steckt](#was-in-der-linie-steckt)
3. [Richtig kennzeichnen](#richtig-kennzeichnen)
4. [Formulierungen: genau und ungenau](#formulierungen-genau-und-ungenau)
5. [Wenn das Tag kommt](#wenn-das-tag-kommt)

## Was die Quellen über 6.0.15 sagen

| Fakt | Quelle |
|---|---|
| "Not released yet. The 6.0.15 line follows v6.0.14 (2026-06-07); no tag `v6.0.15` existed on 2026-10-12." | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md) |
| "Date not released yet" | [Release-Übersicht](../../../eng-us/release-overview) |
| Der Branch `main` ist "the current development of the 6.0 line, today 6.0.15" und gibt sich in `lib/version.php` als `6.0.15stable` aus | [Den Code beschaffen](../../installation/03-den-code-beschaffen.md#31-versionen-tags-und-branches) |
| "Security fixes are made on the `main` branch (the 6.0.15 line, `dev-main`) and published with the next 6.0.x release." | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| Am 18. Oktober 2026 war das neueste Tag noch `v6.0.14` | `git tag -l 'v6.0.*' --sort=version:refname` |

Beachten Sie die Falle in `6.0.15stable`: Das ist die Versionszeichenkette des Entwicklungs-Branches, kein Release. Ein
Release ist ein Tag ([Über ein Release berichten](ueber-ein-release-berichten.md#was-als-release-zählt)).

## Was in der Linie steckt

Das Changelog beschreibt "four months of work", nach Zeiträumen geordnet. Die Höhepunkte, die unter
[Wer ist 7x?, Die Releases 6.0.x](../wer-ist-7x.md#die-releases-60x) genannt werden, sind "Velocity, admin4, audit trail,
Exponential Layouts, Exponential Intelligence, the help system and much more". Die Geschichte ergänzt die Monate:

| Zeitraum | Was laut Geschichte geschah | Monatsseite |
|---|---|---|
| Juli 2026 | "The command line grew up": `ezpm` in der Konsole, der Kickstarter, ein Werkzeug zum Zurücksetzen von Passwörtern | [Juli 2026](../../../eng-us/history/2026/2026-07.md) |
| August 2026 | Template-Debugging, sechs Sicherheitspatches, Tests bei jeder Änderung | [August 2026](../../../eng-us/history/2026/2026-08.md) |
| September 2026 | Seitenweise lange Listen, Velocity (ab 22. September), eine Installation mit einem Befehl, eine Härtungsrunde | [Erste Hälfte](../../../eng-us/history/2026/2026-09a.md), [zweite Hälfte](../../../eng-us/history/2026/2026-09b.md) |
| Oktober 2026 | "jQuery 4 and admin4, an audit trail, `exp:ini`, a debug bar, content jobs and TinyMCE 8" | [Oktober 2026](../../../eng-us/history/2026/2026-10.md) |

Exponential Intelligence und die Job-Warteschlange kommen „inaktiv“ in die Distribution und werden vom Inhaber der Website
eingeschaltet ([Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#12-october-2026-exponential-intelligence-in-the-distribution)).
Erwähnen Sie das, wenn Sie sie beschreiben.

## Richtig kennzeichnen

Setzen Sie den Status in die Überschrift oder den ersten Satz, nicht in eine Fußnote:

```text
In Entwicklung: was die nächste Exponential-Version, 6.0.15, enthalten soll
Die Entwicklungslinie von Exponential bekommt einen Audit-Trail (noch nicht veröffentlicht)
```

Im Text:

```text
Diese Änderungen sind in der Entwicklungslinie 6.0.15 von Exponential. Mit Stand <Datum Ihrer Prüfung> war
kein Release 6.0.15 veröffentlicht; das neueste Release ist 6.0.14 vom 7. Juni 2026. Das Projekt hat kein
Veröffentlichungsdatum angekündigt. Quelle: <Link zum Changelog 6.0.15>.
```

## Formulierungen: genau und ungenau

| Ungenau | Genau |
|---|---|
| „Exponential 6.0.15 ist erschienen.“ | „Exponential 6.0.15 ist in Entwicklung.“ |
| „6.0.15, fällig in diesem Herbst“ | „Für 6.0.15 ist kein Veröffentlichungsdatum angekündigt.“ |
| „Das neue Release enthält ...“ | „Die Entwicklungslinie enthält ...“ |
| „6.0.15 stable“ | „die Linie 6.0.15“ |
| „Alle bekommen Velocity mit dem nächsten Update.“ | „Velocity ist ein eigenes Paket, das die `composer.json` von Exponential in `~0.0.4.54` verlangt“ ([Den Code beschaffen](../../installation/03-den-code-beschaffen.md#34-das-optionale-velocity-paket)) |
| „Funktion X ist sicher in 6.0.15.“ | „Funktion X ist heute in der Entwicklungslinie“; die Linie kann sich vor einem Tag noch ändern |

Wenn Sie die Entwicklungslinie für eine Rezension installieren, sagen Sie es und folgen Sie
[Die Entwicklungslinie testen](../fuersprecher/die-entwicklungslinie-testen.md). Zeigen Sie keine Adressen oder Anmeldedaten
einer Test-Website.

## Wenn das Tag kommt

1. Bestätigen Sie das Tag `v6.0.15` ([Ein Release selbst prüfen](ein-release-selbst-pruefen.md)).
2. Lesen Sie das veröffentlichte Changelog; die Zeile „Not released yet“ wird ersetzt, und der Inhalt kann von dem
   abweichen, was Sie in der Entwicklung gesehen haben.
3. Aktualisieren oder korrigieren Sie frühere Beiträge, die die Linie beschrieben haben ([Korrekturen und Aktualisierungen](korrekturen-und-aktualisierungen.md)).
4. Verwenden Sie die [Checkliste für die Release-Berichterstattung](checkliste-release-berichterstattung.md).

## Verwandte Seiten

- [Über ein Release berichten](ueber-ein-release-berichten.md)
- [Sperrfristen und Zeitpunkt](../../../eng-us/help/press/embargoes-and-timing.md)
- [Aussagen, die Sie nicht machen dürfen, Die Zukunft](../fuersprecher/aussagen-die-sie-nicht-machen-duerfen.md#die-zukunft)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
