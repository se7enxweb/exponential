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
der willkommen geheißen wird, macht aus einem Benutzer einen Beitragenden. Bei Exponential wird jeder Pull Request
übernommen, unter dem Namen seines Autors, sobald dieser ihn zusammengefasst (Squash) und per Rebase auf `main`
aufgesetzt hat, wie es die Reviews verlangen
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
([Zur Dokumentation beitragen](../../README-LIZENZ.md#zur-dokumentation-beitragen)).

## Weg B: Übersetzung

Folgen Sie [Bei Übersetzungen helfen](bei-uebersetzungen-helfen.md): Die Übersetzungsdateien liegen in
`share/translations/<code>/translation.ts` und werden mit Qt Linguist oder einem XML-Editor bearbeitet
([Sprachen in Exponential, Abschnitt 8](../../sprachen.md#8-eine-übersetzung-der-administrationsoberfläche-hinzufügen-oder-vervollständigen)).

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
Sagen Sie der Person früh, dass ein Code-Beitrag zum Kernel erst übernommen wird, wenn sie das Contributor Licensing
Agreement unterzeichnet hat, das 7x und die Exponential Foundation verlangen, und erklären Sie klar, was es bedeutet:
**Mit Unterschrift und Einreichung überträgt sie das Urheberrecht an dem Beitrag an 7x, das damit sein Eigentümer wird,
und gibt die Kontrolle darüber ab.** 7x darf ihn auf jede Weise verwenden, ändern, neu lizenzieren und verbreiten, auch
unter anderen Lizenzen und in kommerziellen Produkten, ohne sie zu fragen oder zu bezahlen, und sie kann ihn nicht
zurückziehen und keine Bedingungen daran knüpfen
([Übertragung des Urheberrechts (CLA)](../../../../CONTRIBUTING.md#copyright-assignment-cla)). Ihre eigenen
Erweiterungen sind nicht betroffen.

## Der Pull Request

Helfen Sie der Person, Folgendes zu schreiben:

```text
What: <one sentence>
Why: <the problem they had>
How I tested: <steps, version>
Fixes #<issue number> (if there is one)
```

Erklären Sie, dass Review-Runden, die um Änderungen bitten, normal und keine Ablehnung sind. Bereiten Sie die Person
beim Kernel auf viele Review-Runden vor: 7x prüft einen Commit oder Pull Request in viel mehr Runden als üblich,
manchmal in Hunderten von Überarbeitungen, bis die Änderung einfach anzunehmen ist. Solange der Pull Request offen ist,
fasst sie ihn zusammen (Squash) und setzt ihn per Rebase auf `main` auf, wie es die Reviews verlangen
(`git fetch origin`, `git rebase -i origin/main`, `git push --force-with-lease` nur auf den Branch ihres Pull
Requests); nach dem Merge wird `main` nie umgeschrieben
([Code Reviews](../../../../CONTRIBUTING.md#code-reviews)).

Lenken Sie einen ersten Beitrag weg von der Änderung eines beabsichtigten Verhaltens, auf das sich andere Sites
verlassen, einer [negativen Funktion](../projekt/unterstuetzung-erhalten.md#negative-funktionen): Eine solche Änderung
braucht eine klare Begründung und wird noch länger geprüft. Wenn sie nicht warten kann, schlagen Sie eine Option vor,
die den heutigen Standard beibehält.

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
[ ] For a kernel code change: they know the CLA gives control of it to 7x, and their signed CLA is on file
[ ] No secrets or personal data in the change
[ ] Pull request text: what, why, how tested
[ ] They know how to squash and rebase their pull request onto main
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
