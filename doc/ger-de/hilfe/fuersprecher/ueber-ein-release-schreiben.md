---
title: Über ein Release schreiben
audience: advocate
category: advocacy
order: 53
summary: Wie Sie aus einem Exponential-Release einen genauen Beitrag machen - auf das Tag warten, das Änderungsprotokoll und die Geschichte lesen, auswählen, was für welches Publikum zählt, die Funktionsseiten verlinken, nie Termine versprechen - mit einer Vorlage für Beiträge und einer Checkliste.
keywords: Release, Release Notes, Änderungsprotokoll, was ist neu, Ankündigung, Beitrag, Tag, Version, Geschichte, Funktionen, Upgrade, Vorlage, release, release notes, changelog, what is new, announcement, post, tag, version, history, features, upgrade, template
type: help
---
# Über ein Release schreiben

Diese Seite ist für Fürsprecher, die ihrem Publikum erzählen möchten, was eine neue Version von Exponential bringt. Ein
Beitrag zu einem Release ist nützlich, wenn er das Änderungsprotokoll in „was das für Sie bedeutet“ übersetzt, und
schädlich, wenn er Dinge verspricht, die nicht im Release sind.

## Inhalt

1. [Auf das Tag warten](#auf-das-tag-warten)
2. [Wo Sie lesen, was sich geändert hat](#wo-sie-lesen-was-sich-geändert-hat)
3. [Auswählen, was für welches Publikum zählt](#auswählen-was-für-welches-publikum-zählt)
4. [Die Vorlage für Beiträge](#die-vorlage-für-beiträge)
5. [Checkliste](#checkliste)

## Auf das Tag warten

Ein Release existiert, wenn sein Tag existiert. Vorher ist die Arbeit „in Entwicklung“ (zum Beispiel „6.0.15 ist in
Entwicklung“, [Wer ist 7x?](../wer-ist-7x.md#die-releases-60x)).

```bash
git fetch --tags
git tag -l 'v6.0.*' --sort=version:refname | tail -3
```

Prüfen Sie auch die [Releases auf GitHub](https://github.com/se7enxweb/exponential/releases). Sortieren Sie Versionen mit
`--sort=version:refname`, nie nach Namen ([Releases und Versionsnummern](../wer-ist-die-exponential-foundation.md#releases-und-versionsnummern)).

Sie dürfen auch über **laufende Arbeit** schreiben, wenn Sie das deutlich sagen: „In der Entwicklungslinie, noch nicht
veröffentlicht“.

## Wo Sie lesen, was sich geändert hat

| Quelle | Was sie Ihnen gibt |
|---|---|
| [Änderungsprotokolle](../../../changelogs/README.md), z. B. [6.0.15](../../../changelogs/6.0/6.0.15.md) | Die Liste der Änderungen einer Version |
| [Die Geschichte](../../../eng-us/history/README.md) | Monatsseiten, die erklären, was jede Änderung möglich macht |
| Funktionsseiten in `doc/eng-us/features/6.0/` | Wie man jede neue Funktion nutzt |
| Hinweise zur Abwärtskompatibilität in `doc/eng-us/bc/6.0/` | Was beim Upgrade zu prüfen ist |
| [Upgrade](../../installation/11-aktualisieren.md) | Wie man sicher aktualisiert |

## Auswählen, was für welches Publikum zählt

| Publikum | Achten Sie auf | Sagen Sie es so |
|---|---|---|
| Redakteure | Neue Bildschirme, Schaltflächen, Funktionen für die Redaktion | „Sie können jetzt ...“ |
| Administratoren | Sicherheit, Einstellungen, Engines, Hinweise zum Upgrade | „Prüfen Sie nach dem Upgrade ...“ |
| Entwickler | APIs, Template-Operatoren, Erweiterungspunkte, PHP-Versionen | „Erweiterungen können jetzt ...“ |
| Alle | Eine oder zwei wichtigste Funktionen | Je ein Satz, mit Link |

Wählen Sie drei bis fünf Punkte. Verlinken Sie jeden auf seine Funktionsseite. Die vollständige Liste überlassen Sie dem
Änderungsprotokoll.

## Die Vorlage für Beiträge

```text
# What's new in Exponential <version>

Exponential <version> was released on <date> (tag v<version>). Here are the changes I think matter most
for <audience>. The full list is in the changelog: <link>.

## <Headline feature 1>
<What it does for the reader, 2-3 sentences.> How to use it: <feature page link>.

## <Feature 2>
...

## Before you upgrade
Back up first and follow the upgrade chapter: <link>. Check the notes on backward compatibility: <link>.
Setup > Upgrade check compares your files and database with the release: <link>.

## Thanks
<Contributors who agreed to be named, or "everyone who sent pull requests, reports and translations".>

Questions: share.exponential.earth/forums, t.me/exponentialcms
<Disclosure line.>
```

## Checkliste

```text
[ ] The tag exists (or the post says clearly "in development")
[ ] Every feature mentioned is in the changelog or the history, with a link to its page
[ ] No promised dates for the next release; no features that are not documented
[ ] Upgrade advice links the upgrade chapter; no "just run the installer" (it must never run over an existing site)
[ ] Version written as "Exponential 6.0.x" in text, "v6.0.x" only for the tag
[ ] Credits only for people who agreed, or general thanks
[ ] Shared in the forums and on social media with the right names
```

Die Regel zum Upgrade stammt aus [Upgrade](../../installation/11-aktualisieren.md); die Regel zur Schreibweise der Version aus den
[Marken- und Namensregeln](../geschaeft/marken-und-namensregeln.md#das-produkt-exponential).

## Verwandte Seiten

- [Beiträge zur Geschichte schreiben](beitraege-zur-geschichte-schreiben.md)
- [Vorlagen für Beiträge in sozialen Medien](vorlagen-fuer-soziale-medien.md)
- [Die Entwicklungslinie testen](die-entwicklungslinie-testen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
