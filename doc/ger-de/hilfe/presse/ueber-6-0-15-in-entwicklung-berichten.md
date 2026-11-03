---
title: Über 6.0.15 berichten: vor und nach dem Tag
audience: press
category: press-media
order: 106
summary: Wie Sie über die Linie Exponential 6.0.15 berichten - was die Quellen darüber sagen, wie Sie prüfen, ob der Tag v6.0.15 existiert, wie die Linie vor diesem Tag beschrieben wurde, welche Formulierungen genau sind und warum Sie ein Datum nur aus dem Tag nehmen.
keywords: 6.0.15, Linie 6.0.15, aktuelle Linie, Tag v6.0.15, main-Branch, dev-main, Veröffentlichungsdatum, Velocity, admin4, Prüfprotokoll, Exponential Layouts, Exponential Intelligence, 6.0.15 line, current line, release tag, main branch, release date, audit trail
type: help
---
# Über 6.0.15 berichten: vor und nach dem Tag

Diese Seite ist für Reporter und Creator, die über die Arbeit in der Linie Exponential 6.0.15 schreiben wollen, der
aktuellen Linie von Exponential 6.0 (sie folgt auf 6.0.14). Die Arbeit ist ausführlich dokumentiert. Wichtig ist, zu
prüfen, ob der Tag `v6.0.15` existiert, und ein Veröffentlichungsdatum nur aus diesem Tag zu nehmen, nie aus einer
Vermutung.

## Inhalt

1. [Was die Quellen über 6.0.15 sagen](#was-die-quellen-über-6015-sagen)
2. [Was in der Linie steckt](#was-in-der-linie-steckt)
3. [Richtig kennzeichnen](#richtig-kennzeichnen)
4. [Formulierungen: genau und ungenau](#formulierungen-genau-und-ungenau)
5. [Wenn das Tag kommt](#wenn-das-tag-kommt)

## Was die Quellen über 6.0.15 sagen

| Fakt | Quelle |
|---|---|
| Die Linie 6.0.15 folgt auf `v6.0.14` (7. Juni 2026); bis zum Tag beginnt ihr Changelog mit "Not released yet" | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md) |
| Das Datum von 6.0.15 in der Release-Übersicht; bis zum Tag stand dort "Date not released yet" | [Release-Übersicht](../../../eng-us/release-overview) |
| Der Branch `main` trägt die Linie 6.0.15 und gibt sich in `lib/version.php` als `6.0.15stable` aus | [Den Code beschaffen](../../installation/03-den-code-beschaffen.md#31-versionen-tags-und-branches) |
| "Security fixes are made on the `main` branch (the 6.0.15 line, `dev-main`) and published with the next 6.0.x release." | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| Am 18. Oktober 2026 war das neueste Tag noch `v6.0.14`; ob `v6.0.15` heute existiert, zeigt der Befehl rechts | `git tag -l 'v6.0.*' --sort=version:refname` |

Beachten Sie die Falle in `6.0.15stable`: Das ist die Versionszeichenkette des Branches `main`, kein Release. Ein
Release ist ein Tag ([Über ein Release berichten](ueber-ein-release-berichten.md#was-als-release-zählt)).

## Was in der Linie steckt

Das Changelog beschreibt "four months of work", nach Zeiträumen geordnet. Die Höhepunkte, die unter
[Wer ist 7x?, Die Releases 6.0.x](../wer-ist-7x.md#die-releases-60x) genannt werden, sind "Velocity, admin4, audit trail,
Exponential Layouts, Exponential Intelligence, the help system and much more". Die Geschichte ergänzt die Monate:

| Zeitraum | Was laut Geschichte geschah | Monatsseite |
|---|---|---|
| Juli 2026 | "The command line grew up": `ezpm` in der Konsole, der Kickstarter, ein Werkzeug zum Zurücksetzen von Passwörtern | [Juli 2026](../../geschichte/2026/2026-07.md) |
| August 2026 | Template-Debugging, sechs Sicherheitspatches, Tests bei jeder Änderung | [August 2026](../../geschichte/2026/2026-08.md) |
| September 2026 | Seitenweise lange Listen, Velocity (ab 22. September), eine Installation mit einem Befehl, eine Härtungsrunde | [Erste Hälfte](../../geschichte/2026/2026-09a.md), [zweite Hälfte](../../geschichte/2026/2026-09b.md) |
| Oktober 2026 | jQuery 4 und admin4, ein Audit-Trail, `exp:ini`, eine Debug-Leiste, Inhaltsjobs und TinyMCE 8 | [Oktober 2026](../../geschichte/2026/2026-10.md) |

Exponential Intelligence und die Job-Warteschlange kommen „inaktiv“ in die Distribution und werden vom Inhaber der Website
eingeschaltet ([Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#12-october-2026-exponential-intelligence-in-the-distribution)).
Erwähnen Sie das, wenn Sie sie beschreiben.

## Richtig kennzeichnen

Setzen Sie den Status in die Überschrift oder den ersten Satz, nicht in eine Fußnote. Prüfen Sie ihn zuerst mit
`git tag -l 'v6.0.*' --sort=version:refname`. Bis zum Tag `v6.0.15` lauteten Überschriften für die Linie so:

```text
Die Linie 6.0.15: was die aktuelle Exponential-Version enthält
Die Linie 6.0.15 von Exponential bekommt ein Prüfprotokoll (Stand <Datum Ihrer Prüfung>)
```

Im Text, wenn es noch keinen Tag `v6.0.15` gibt:

```text
Diese Änderungen sind in der Linie 6.0.15 von Exponential. Mit Stand <Datum Ihrer Prüfung> war
kein Release 6.0.15 getaggt; die Linie folgt auf 6.0.14 vom 7. Juni 2026. Das Projekt hat kein
Veröffentlichungsdatum angekündigt. Quelle: <Link zum Changelog 6.0.15>.
```

Wenn der Tag existiert, nennen Sie 6.0.15 ein Release mit dem Datum des Tags
([Ein Release selbst prüfen](ein-release-selbst-pruefen.md)).

## Formulierungen: genau und ungenau

| Ungenau | Genau |
|---|---|
| „Exponential 6.0.15 ist erschienen.“ (ohne den Tag geprüft zu haben) | „Exponential 6.0.15 ist die aktuelle Linie von Exponential 6.0.“; „erschienen“ mit Datum erst, wenn der Tag `v6.0.15` existiert |
| „6.0.15, fällig in diesem Herbst“ | „Für 6.0.15 ist kein Veröffentlichungsdatum angekündigt.“ (vor dem Tag) oder das Datum des Tags (danach) |
| „Das neue Release enthält ...“ (vor dem Tag) | „Die Linie 6.0.15 enthält ...“ |
| „6.0.15 stable“ | „die Linie 6.0.15“ |
| „Alle bekommen Velocity mit dem nächsten Update.“ | „Velocity ist ein eigenes Paket, das die `composer.json` von Exponential in `~0.0.4.57` verlangt“ ([Den Code beschaffen](../../installation/03-den-code-beschaffen.md#34-das-velocity-paket)) |
| „Funktion X ist sicher in 6.0.15.“ (vor dem Tag) | „Funktion X ist heute auf `main`, in der Linie 6.0.15“; vor dem Tag kann sich die Linie noch ändern, danach zählt das Changelog des Tags |

Wenn Sie den Branch `main` für eine Rezension installieren, sagen Sie es und folgen Sie
[Die Entwicklungslinie testen](../fuersprecher/die-entwicklungslinie-testen.md). Zeigen Sie keine Adressen oder Anmeldedaten
einer Test-Website.

## Wenn das Tag kommt

1. Bestätigen Sie das Tag `v6.0.15` ([Ein Release selbst prüfen](ein-release-selbst-pruefen.md)).
2. Lesen Sie das veröffentlichte Changelog; die Zeile „Not released yet“ wird ersetzt, und der Inhalt kann von dem
   abweichen, was Sie vor dem Tag auf `main` gesehen haben.
3. Aktualisieren oder korrigieren Sie frühere Beiträge, die die Linie beschrieben haben ([Korrekturen und Aktualisierungen](korrekturen-und-aktualisierungen.md)).
4. Verwenden Sie die [Checkliste für die Release-Berichterstattung](checkliste-release-berichterstattung.md).

## Verwandte Seiten

- [Über ein Release berichten](ueber-ein-release-berichten.md)
- [Sperrfristen und Zeitpunkt](sperrfristen-und-zeitpunkt.md)
- [Aussagen, die Sie nicht machen dürfen, Die Zukunft](../fuersprecher/aussagen-die-sie-nicht-machen-duerfen.md#die-zukunft)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
