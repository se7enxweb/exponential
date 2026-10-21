---
title: Vorlagen für Release-Beiträge
audience: press
category: press-media
order: 114
summary: Vorlagen zum Ausfüllen für die Berichterstattung über Releases von Exponential - eine Kurzmeldung, ein technischer Artikel, eine Vorschau auf die Entwicklungslinie, eine Notiz zu einer Erweiterung oder zu Velocity, ein Social-Media-Beitrag und die Gliederung eines Videoskripts - jeweils mit den einzusetzenden Fakten und der Quelle für jeden.
keywords: Vorlagen, Release-Beitrag, Kurzmeldung, technischer Artikel, Vorschau, Social-Media-Beitrag, Videoskript, Shownotes, ausfüllen, Release-Berichterstattung, templates, release story, news brief, technical article, preview, social post, video script, show notes, fill in, release coverage
type: help
---
# Vorlagen für Release-Beiträge

Diese Seite ist für Reporter, Redakteure und Creator, die eine schnelle, korrekte Struktur für die Berichterstattung
über ein Release suchen. Jede Vorlage hat Platzhalter in spitzen Klammern. Füllen Sie jeden Platzhalter aus einer
Quelle; wenn Sie einen nicht füllen können, streichen Sie den Satz, statt zu raten. Die Vorlagen enthalten mit Absicht
keine Zitate: Zitate kommen nur aus veröffentlichten Quellen oder schriftlich von 7x oder der Foundation
([Richtlinie für Zitate](richtlinie-fuer-zitate.md)).

Die Vorlagen stehen in ihrer englischen Originalfassung; übersetzen Sie sie für Ihr Medium, ohne Fakten hinzuzufügen.

## Inhalt

1. [Woher die Platzhalter kommen](#woher-die-platzhalter-kommen)
2. [Kurzmeldung, etwa 150 Wörter](#kurzmeldung-etwa-150-wörter)
3. [Gliederung eines technischen Artikels](#gliederung-eines-technischen-artikels)
4. [Vorschau auf die Entwicklungslinie](#vorschau-auf-die-entwicklungslinie)
5. [Notiz zu einer Erweiterung oder zu Velocity](#notiz-zu-einer-erweiterung-oder-zu-velocity)
6. [Social-Media-Beitrag](#social-media-beitrag)
7. [Segment für Video oder Podcast](#segment-für-video-oder-podcast)

## Woher die Platzhalter kommen

| Platzhalter | Quelle |
|---|---|
| `<version>`, `<date>`, `<tag>` | Der Kopf des Changelogs („Released ... Tag ...“) und die [Release-Übersicht](../../../eng-us/release-overview) |
| `<headline change>` | „In short“ im Changelog ([Ein Changelog lesen](ein-changelog-lesen.md)) |
| `<upgrade note>` | „Upgrading“ im Changelog |
| `<feature page>` | Die Links im Changelog |
| `<release count>` | `git tag -l 'v6.0.*'` oder [Wer ist 7x?, Die 6.0.x-Releases](../wer-ist-7x.md#die-releases-60x) |
| `<who stands behind it>` | [Wer ist die Exponential Foundation?, Die Foundation und 7x](../wer-ist-die-exponential-foundation.md#die-foundation-und-7x) |

## Kurzmeldung, etwa 150 Wörter

```text
Exponential <version> released

<Date> - Exponential <version>, a new release of the free and open source PHP content management
system, was published on <date> (tag v<version>). <Headline change, in the words of "In short".>

<Second change, one sentence.> <Upgrade note: what site owners must do, or "the changelog lists no
required action for ...".>

Exponential is developed under the GNU GPL, version 2 or later. The company 7x leads its development;
the Exponential Foundation, a nonprofit founded in 2026, operates and funds the project. The release is
the <ordinal> of the 6.0 line, which began with 6.0.0 on 1 January 2024.

Changelog: <link>   Repository: https://github.com/se7enxweb/exponential
```

## Gliederung eines technischen Artikels

```text
1. What is new in Exponential <version> (tag v<version>, <date>)         - changelog header
2. The headline change and how it works                                 - changelog + feature page
3. Two or three further changes for developers and administrators       - Added / Updated sections
4. Upgrading: what to run, what to check                                - "Upgrading" + upgrade chapter
5. Requirements: PHP versions, databases                                - Getting the code, section 3.1
6. Where to get it: Composer package, Git tag                           - Getting the code
7. Context: the 6.0 line so far                                         - Who is 7x?, The 6.0.x releases
8. Comment from 7x or the Foundation (only if given in writing)         - Quotes policy
```

## Vorschau auf die Entwicklungslinie

```text
In development: <topic> in the next Exponential version

The development line of Exponential, 6.0.15, includes <feature>, according to the project's
changelog for the line (<link>). As of <date checked>, 6.0.15 had not been released; the newest
release is 6.0.14 of 7 June 2026, and the project has not announced a release date.

<What the feature does, from its feature page.> <Whether it is active by default, if the source says.>
```

Regeln für diese Vorlage: [Über 6.0.15 berichten, solange es in Entwicklung ist](ueber-6-0-15-in-entwicklung-berichten.md).

## Notiz zu einer Erweiterung oder zu Velocity

```text
<Package> <version> for Exponential

<Package>, <what it is in one phrase>, has a new release, <version>, dated <date> (<link to its
release notes>). <One or two changes, in the words of the release notes.> It is a separate package
with its own version numbers; sites update it on its own.
```

Für Velocity-Zahlen ergänzen Sie ihre Bedingungen oder lassen Sie sie weg ([Über Velocity-Releases berichten](../../../eng-us/help/press/covering-velocity-releases.md)).

## Social-Media-Beitrag

```text
Exponential <version> is out (tag v<version>, <date>): <headline change in under 15 words>.
Changelog: <link>
```

```text
Not released yet, but in Exponential's 6.0.15 development line: <feature>. Details: <link>
```

Schreiben Sie Version und Status in den Beitrag selbst; der erste Beitrag eines Threads wird oft allein geteilt.

## Segment für Video oder Podcast

```text
INTRO      "Exponential <version> was released on <date>." (show the tag or the changelog header)
WHAT       Headline change - show it on your own fresh installation
WHO        "The company 7x leads development; the Exponential Foundation, a nonprofit, operates the project."
UPGRADE    "If you run a site, read the upgrade notes first" (link in the description)
CLOSE      Links: changelog, repository, the feature page; disclosure if sponsored
```

Die Shownotes müssen dieselben Links enthalten; sagen Sie bei allem, was noch nicht erschienen ist, laut „in
Entwicklung“.

## Verwandte Seiten

- [Über ein Release berichten](ueber-ein-release-berichten.md)
- [Checkliste für die Release-Berichterstattung](checkliste-release-berichterstattung.md)
- [Vorlagen für Social-Media-Beiträge](../fuersprecher/vorlagen-fuer-soziale-medien.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
