---
title: Einen ersten Beitrag begleiten
audience: advocate
category: advocacy
order: 94
summary: Wie ein Fürsprecher jemanden durch seinen ersten Beitrag zu Exponential begleitet - eine kleine, echte Aufgabe wählen, der Weg über Dokumentation oder Übersetzung, der Weg über Code mit Git, die Commit-Nachricht, der Pull Request und das Feiern - mit einer Checkliste für die Begleitung.
keywords: Begleitung, Mentoring, erster Beitrag, erster Pull Request, Neue, gute erste Aufgabe, Dokumentation, Übersetzung, Git, Fork, Commit-Nachricht, Pull Request, Prüfung, mentoring, first contribution, first pull request, newcomers, good first task, documentation, translation, commit message, pull request, review
type: help
---
# Einen ersten Beitrag begleiten

Diese Seite ist für Fürsprecher, die einer anderen Person zu ihrem ersten Beitrag verhelfen. Ein erster Pull Request,
der willkommen geheißen wird, macht aus einem Benutzer einen Beitragenden. Bei Exponential „wird jeder Pull Request
übernommen, und seine Commits bleiben so erhalten, wie der Beitragende sie geschrieben hat“
([Wie das Projekt wächst](../wer-ist-die-exponential-foundation.md#wie-das-projekt-wächst)), was es zu einem besonders
guten Ort für den Anfang macht.

## Inhalt

1. [Eine kleine, echte Aufgabe wählen](#eine-kleine-echte-aufgabe-wählen)
2. [Weg A: Dokumentation](#weg-a-dokumentation)
3. [Weg B: Übersetzung](#weg-b-übersetzung)
4. [Weg C: Code](#weg-c-code)
5. [Der Pull Request](#der-pull-request)
6. [Nach der Übernahme](#nach-der-übernahme)
7. [Checkliste für die Begleitung](#checkliste-für-die-begleitung)

## Eine kleine, echte Aufgabe wählen

| Gute erste Aufgaben | Warum |
|---|---|
| Ein Tippfehler oder ein veralteter Schritt auf einer Hilfeseite, die die Person gelesen hat | Sie hat ihn schon gefunden |
| Ein fehlender Link auf eine ausführlichere Seite | Klein und nützlich |
| Eine Handvoll Texte in ihrer Sprache übersetzen | Klar und messbar |
| Ein kleiner Fehler, auf den sie selbst gestoßen ist, mit klarer Korrektur | Motivierend |

Vermeiden Sie „Übungs“-Pull-Requests ohne echte Änderung und vermeiden Sie große Aufgaben; die erste sollte in ein oder
zwei Stunden fertig sein.

## Weg A: Dokumentation

1. Drücken Sie auf der Hilfeseite **Auf GitHub bearbeiten**.
2. Bearbeiten; eine Commit-Nachricht schreiben wie `Fixed: The languages help names the Apply button`.
3. Die Änderung vorschlagen und den Pull Request eröffnen.

Details: [Die Hilfe verbessern](die-hilfe-verbessern.md). Die Änderung wird unter der GNU FDL 1.3 oder später angenommen
([Zur Dokumentation beitragen](../../../eng-us/README-LICENSE.md#contributing-to-the-documentation)).

## Weg B: Übersetzung

Folgen Sie [Bei Übersetzungen helfen](bei-uebersetzungen-helfen.md): Die Übersetzungsdateien liegen in
`share/translations/<code>/translation.ts` und werden mit Qt Linguist oder einem XML-Editor bearbeitet
([Sprachen in Exponential, Abschnitt 8](../../../eng-us/languages.md#8-adding-or-completing-an-administration-translation)).

## Weg C: Code

Folgen Sie [Mit Git entwickeln, Eine Änderung per Pull Request beitragen](../mit-git-entwickeln.md#eine-änderung-per-pull-request-beitragen):

```bash
# in the clone of the kernel or an extension
git remote add fork https://github.com/<them>/<repository>.git
git switch -c fix-<short-name>
# change, test on their own site
git commit -am "Fixed: <what the change does>"
git push -u fork fix-<short-name>
```

Lesen Sie beim Kernel gemeinsam CONTRIBUTING.md: den Commit-Hook (`make hooks`) und das Dateimanifest
(`make manifest-fix`) ([CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md)).

## Der Pull Request

Helfen Sie der Person, Folgendes zu schreiben:

```text
What: <one sentence>
Why: <the problem they had>
How I tested: <steps, version>
Fixes #<issue number> (if there is one)
```

Erklären Sie, dass Prüfende Dinge in Commits obendrauf korrigieren können; das ist normal und keine Ablehnung.

## Nach der Übernahme

- Feiern Sie: ein Dank im Forum oder Chat, mit ihrer Zustimmung ([Ideen für Anerkennung](ideen-fuer-anerkennung.md)).
- Zeigen Sie ihr den nächsten Schritt auf [den Stufen der Fürsprache](die-stufen-der-fuersprache.md).
- Fragen Sie, was schwer war, und verbessern Sie die Dokumentation für die nächste Person.

## Checkliste für die Begleitung

```text
[ ] The task is real and small
[ ] They have a GitHub account (with two-factor authentication)
[ ] They chose the path (docs, translation, code) and read the page for it
[ ] Commit message starts with Added:/Updated:/Fixed:/Removed:/Renamed:
[ ] No secrets or personal data in the change
[ ] Pull request text: what, why, how tested
[ ] They know reviews may add commits on top
[ ] Celebrated, with consent
```

## Verwandte Seiten

- [Neue willkommen heißen](neue-willkommen-heissen.md)
- [Mit Git entwickeln](../mit-git-entwickeln.md)
- [Eine Leitungsrolle in der Exponential-Community übernehmen](../fuehrung/community-leiter-werden.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
