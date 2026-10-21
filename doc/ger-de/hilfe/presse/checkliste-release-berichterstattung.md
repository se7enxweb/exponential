---
title: Checkliste für die Release-Berichterstattung
audience: press
category: press-media
order: 112
summary: Eine kopierbare Checkliste, die Sie durchgehen, bevor Sie einen Beitrag, ein Video oder einen Post über ein Release von Exponential veröffentlichen - der Tag, das Datum, das Versionsformat, der Inhalt, die Namen, die Zitate, Sicherheit und Privatsphäre, Links und ein Plan für Korrekturen.
keywords: Checkliste, Release, Berichterstattung, vor der Veröffentlichung, Tag, Version, Datum, Namen, Zitate, Quellen, Faktencheck, Links, checklist, release, coverage, before publishing, tag, version, date, names, quotes, sources, fact check, links
type: help
---
# Checkliste für die Release-Berichterstattung

Diese Seite ist für alle, die gleich über ein Release berichten: eine Meldung, eine Rezension, einen Abschnitt im
Newsletter, ein Video oder einen Thread in sozialen Medien. Kopieren Sie die Checkliste in Ihre Notizen und haken Sie
jede Zeile ab. Jede Zeile verweist auf die Seite, die sie erklärt, sodass ein „Nein“ Ihnen sagt, wo Sie nachsehen müssen.

## Inhalt

1. [Bevor Sie zu schreiben beginnen](#bevor-sie-zu-schreiben-beginnen)
2. [Die vollständige Checkliste](#die-vollständige-checkliste)
3. [Für Video, Audio und Live-Formate](#für-video-audio-und-live-formate)
4. [Nach der Veröffentlichung](#nach-der-veröffentlichung)

## Bevor Sie zu schreiben beginnen

Beantworten Sie zuerst drei Fragen; sie entscheiden über den ganzen Beitrag.

| Frage | Wo Sie nachsehen |
|---|---|
| Ist es erschienen (es gibt einen Tag), oder ist es die Entwicklungslinie? | [Ein Release selbst überprüfen](ein-release-selbst-pruefen.md) |
| Um welches Produkt geht es: den Kernel, eine Erweiterung, Velocity oder ein Platform-Paket? | [Wo Changelogs und Release Notes stehen](wo-changelogs-und-release-notes-stehen.md) |
| Wer ist mein Publikum: allgemein, Wirtschaft, Technik, lokal oder Bildung? | [Vorlagen für Release-Beiträge](vorlagen-fuer-release-beitraege.md) |

## Die vollständige Checkliste

```text
RELEASE COVERAGE CHECKLIST                          story: ____________________  date checked: __________

STATUS
[ ] The tag exists (v<version>), checked today with git or GitHub Releases
[ ] Or: the story says "in development" in the headline or first sentence, and gives no release date
[ ] The release date is the tag date (or says which date it is), taken from the changelog or release overview

NUMBERS AND NAMES
[ ] Version written "Exponential 6.0.x" in text; "v6.0.x" only when naming the tag
[ ] Extension, Velocity and Platform versions are not mixed up with the kernel version
[ ] Product name "Exponential"; "eZ Publish" only as history, in the past tense
[ ] 7x = the company; the Exponential Foundation = the nonprofit; not mixed up
[ ] No statement that Exponential is associated with eZ Systems or Ibexa
[ ] Every number has a source and the date it was counted

CONTENT
[ ] Every feature named is in the changelog (or the history), with a link
[ ] Scope kept: no "rebuilt", "fully", "all" where the changelog says less
[ ] Upgrade advice links the upgrade chapter; nothing suggests running an installer over an existing site
[ ] No user counts, market share, speed multiples, compliance or "secure" claims

QUOTES AND ATTRIBUTION
[ ] Every quote is verbatim from a published source (with link) or given in writing by 7x or the Foundation
[ ] No quote composed, "cleaned up" or attributed to a person who did not say it
[ ] Opinions are marked as mine / ours

SECURITY AND PRIVACY
[ ] No details of unfixed vulnerabilities
[ ] No passwords, keys, internal host names or sign-in screens with data on screen
[ ] No customer names or personal data without consent

LINKS AND CREDIT
[ ] Links to the changelog, the feature pages and the repository work
[ ] Credit for Netgen, Mugo Web or CJW Network where the release uses their work
[ ] Disclosure line if I am paid, sponsored or a contributor

AFTERWARDS
[ ] I know how I will publish a correction if something is wrong
```

Die Seiten hinter den Zeilen: [Über ein Release berichten](ueber-ein-release-berichten.md),
[Über 6.0.15 berichten, solange es in Entwicklung ist](ueber-6-0-15-in-entwicklung-berichten.md),
[Wie Versionsnummern funktionieren](wie-versionsnummern-funktionieren.md), [Regeln für Marke und Namen](../geschaeft/marken-und-namensregeln.md),
[Zahlen überprüfen](zahlen-pruefen.md), [Richtlinie für Zitate](richtlinie-fuer-zitate.md),
[Über Sicherheitskorrekturen berichten](ueber-sicherheitskorrekturen-berichten.md), [Offenlegung und Interessenkonflikte](../fuersprecher/offenlegung-und-interessenkonflikte.md),
[Korrekturen und Aktualisierungen](korrekturen-und-aktualisierungen.md). Die Zeile zum Upgrade stammt aus
[Über ein Release schreiben](../fuersprecher/ueber-ein-release-schreiben.md#checkliste): Das Installationsprogramm „must never
run over an existing site“, darf also nie über eine bestehende Website laufen.

## Für Video, Audio und Live-Formate

```text
[ ] On-screen text and lower thirds use "Exponential 6.0.x" and the right names
[ ] The demo installation is my own, freshly installed; no real data, no sign-in data visible
[ ] Spoken version numbers match the tag ("six point oh point fourteen")
[ ] Description or show notes carry the links to the changelog and the repository
[ ] Unreleased features are called "in development" out loud, not only in the description
```

Auf Deutsch gesprochen lautet die Versionsnummer zum Beispiel „sechs Punkt null Punkt vierzehn“.

## Nach der Veröffentlichung

- Bewahren Sie Ihre Checkliste mit dem Datum auf; sie zeigt, was Sie geprüft haben, wenn ein Leser einen Fakt anzweifelt.
- Wenn das nächste Release erscheint, bearbeiten Sie den alten Beitrag nicht, um ihn aktuell zu machen; ergänzen Sie
  eine Aktualisierungszeile ([Korrekturen und Aktualisierungen](korrekturen-und-aktualisierungen.md)).

## Verwandte Seiten

- [Checkliste zum Faktencheck](../../../eng-us/help/press/fact-checking-checklist.md)
- [Häufige Fehler in der Berichterstattung](haeufige-fehler-in-berichten.md)
- [Über ein Release berichten](ueber-ein-release-berichten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
