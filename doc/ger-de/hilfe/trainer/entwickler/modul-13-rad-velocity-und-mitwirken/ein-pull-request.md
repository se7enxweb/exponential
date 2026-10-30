---
title: "Lektion 13.3: Ein Pull Request"
audience: trainer, developer
category: training
order: 133
summary: Die Teilnehmenden bereiten eine kleine Korrektur am Kernel oder an einer Erweiterung als Pull Request vor: ein Fork, ein Zweig, ein fokussierter Commit mit dem richtigen Betreff, die Prüfungen und das Manifest, ein Push in den Fork und eine Beschreibung, mit der Reviewer arbeiten können. 50 Minuten.
keywords: entwickler, pull request, GitHub, fork, zweig, remote, commit-nachricht, Fixed:, CONTRIBUTING.md, make hooks, manifest-fix, gh pr create, code-review, GPL, korrektur der dokumentation, developers course, branch, commit message, code review, documentation fix
type: help
---
# Lektion 13.3: Ein Pull Request

Die letzte Lektion schließt den Kreis des Kurses: Eine Korrektur, die die Teilnehmenden für sich selbst machen, geht
an das Projekt zurück, sodass sie im nächsten Release enthalten ist und niemand sie allein weitertragen muss. In
dieser Lektion bereiten die Teilnehmenden eine kleine, echte Änderung (oft eine Korrektur an diesen Hilfeseiten) als
Pull Request vor, mit jeder Prüfung, die ein Reviewer erwartet.

> **Hinweis**
> Um den Pull Request zu eröffnen, wird ein GitHub-Konto gebraucht. Teilnehmende ohne Konto hören vor dem Push auf;
> alles davor ist gleich.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 13: RAD, Velocity und Mitwirken](README.md) |
| Dauer | 50 Minuten (15 Präsentation, 35 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 12.3](../modul-12-tests-und-qualitaet/pruefungen-bevor-sie-eine-aenderung-teilen.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- ein Repository forken und den Fork als Remote ihres Klons hinzufügen;
- eine fokussierte Änderung auf einem eigenen Zweig vornehmen;
- einen Commit-Betreff mit dem richtigen Präfix schreiben und die Prüfungen und das Manifest ausführen;
- den Zweig in den Fork pushen und einen Pull Request mit einer nützlichen Beschreibung eröffnen;
- auf Review-Kommentare mit Änderungen auf demselben Zweig antworten und ihn dann zusammenfassen (squash) und auf
  `main` rebasen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Mit Git entwickeln, Eine Änderung mit einem Pull Request beitragen](../../../mit-git-entwickeln.md#eine-änderung-per-pull-request-beitragen)
und `CONTRIBUTING.md` im Wurzelverzeichnis der Installation.

1. **Warum** (2 Minuten). Eine Korrektur, die nur auf Ihrer Website liegt, muss nach jedem Update erneut angewendet
   werden; eine übernommene Korrektur ist im nächsten Release für alle enthalten.
2. **Die Lizenz** (2 Minuten). Exponential steht unter der GNU GPL, Version 2 oder später; Beiträge werden unter
   derselben Lizenz veröffentlicht (`CONTRIBUTING.md`). Die Dokumentation ist unter der GNU FDL 1.3 frei.
3. **Die Schritte** (5 Minuten). Forken auf GitHub; `git remote add fork https://github.com/<you>/<repository>.git`;
   ein Zweig pro Änderung; die Änderung; die Prüfungen aus Lektion 12.3; beim Kernel das Manifest
   (`make manifest-fix`); ein Commit `Fixed: ...`; `git push -u fork <branch>`; den Pull Request über den Link
   eröffnen, den GitHub ausgibt (oder `gh pr create`).
4. **Eine gute Beschreibung** (3 Minuten). Was falsch war, was die Änderung tut, wie sie getestet wurde und alles,
   was sich der Reviewer ansehen sollte. Ein Thema pro Pull Request.
5. **Review** (2 Minuten). Beantworten Sie Kommentare und ändern Sie den Code auf demselben Zweig. Beim Kernel
   verlangt 7x, dass der Pull Request zusammengefasst und auf `main` rebast wird, solange er offen ist:
   `git fetch origin`, `git rebase -i origin/main`, `git push --force-with-lease` nur auf den Zweig des Pull
   Requests; der Pull Request aktualisiert sich selbst. Nach dem Merge wird `main` niemals umgeschrieben.

### Häufige Fragen

**Kann ich auf dieselbe Weise zu einer Erweiterung beitragen?** Ja: Jede Erweiterung ist ein eigenes Repository mit
eigenem `origin`; die Schritte sind innerhalb von `extension/<name>` dieselben.

**Was, wenn CI bei meinem Pull Request fehlschlägt?** Lesen Sie den fehlgeschlagenen Schritt; seine Zusammenfassung
nennt das Problem (eine veraltete Manifestzeile, ein Commit-Betreff, ein Syntaxfehler). Beheben Sie es mit einem
weiteren Commit auf dem Zweig.

**Brauche ich Schreibrechte für das Projekt?** Nein. Sie pushen in Ihren Fork; der Pull Request bittet das Projekt,
die Änderung zu übernehmen.

**Wie lange dauert ein Review?** Bereiten Sie die Teilnehmenden auf viele Runden vor: 7x prüft einen Commit oder
Pull Request in sehr viel mehr Runden als üblich, manchmal in Hunderten von Überarbeitungen, bis die Änderung einfach
anzunehmen ist ([Code-Reviews](../../../../../../CONTRIBUTING.md#code-reviews)).

**Muss ich etwas unterschreiben?** Für einen Code-Beitrag zum Kernel ja: 7x und die Exponential Foundation übernehmen
ihn erst, wenn das unterschriebene Contributor Licensing Agreement der Autorin oder des Autors eingegangen ist. Sagen
Sie klar, was das bedeutet: Mit Unterschrift und Einreichung überträgt die Autorin oder der Autor das Urheberrecht am
Beitrag an die Exponential Foundation und 7x, die damit Eigentümer werden, und gibt die Kontrolle darüber ab; die Exponential Foundation und 7x dürfen ihn auf jede Weise verwenden,
ändern, neu lizenzieren und verbreiten, ohne zu fragen oder zu bezahlen, und die Autorin oder der Autor kann ihn weder
zurückziehen noch Bedingungen daran knüpfen. Es wird einmal unterschrieben. Ein im Kurs eröffneter Pull Request kann
darauf warten. Die eigenen Erweiterungen der Teilnehmenden sind nicht betroffen
([Übertragung des Urheberrechts (CLA)](../../../../../../CONTRIBUTING.md#copyright-assignment-cla)).

### Stolperfallen

- **Auf `main` arbeiten.** Immer ein Zweig; eine Änderung pro Zweig.
- **Geheimnisse im Commit.** `settings/override/` und `settings/siteaccess/` können Passwörter enthalten; committen
  Sie sie niemals. Prüfen Sie vor jedem Commit `git diff --cached --name-only`.
- **Ein Pull Request, der vieles tut.** Teilen Sie ihn auf; kleine Pull Requests werden schneller geprüft.
- **Eine negative Funktion „korrigieren“.** Manches Verhalten ist beabsichtigt oder Auslegungssache, und andere Sites
  verlassen sich darauf. Eine Änderung daran braucht eine klare Begründung und mehrere Review-Runden; eine Option ist
  besser als ein neuer Standard ([Negative Funktionen](../../../projekt/unterstuetzung-erhalten.md#negative-funktionen)).

## Praktische Übung

Im Kernel-Klon Ihrer Entwicklungsinstallation (oder im Klon einer Erweiterung, die Sie im Kurs verwendet haben).

1. Suchen Sie etwas Kleines zum Verbessern: einen Tippfehler oder einen unklaren Satz in einer Hilfeseite, die Sie
   im Kurs gelesen haben, oder einen fehlenden Test.
2. Legen Sie einen Zweig an: `git switch -c fix-<short-name>`.
3. Nehmen Sie die Änderung vor. Prüfen Sie bei einer Dokumentationsseite ihre Links mit der Hilfeprüfung:
   `php bin/php/console exp:help check --below=help/<folder>/`.
4. Führen Sie die zutreffenden Prüfungen aus Lektion 12.3 aus; aktualisieren Sie beim Kernel das Manifest:
   `make manifest-fix`.
5. Prüfen Sie, was committet wird: `git status` und `git diff --cached --name-only` nach `git add`. Nichts aus
   `settings/override/` oder `var/`.
6. Committen Sie: `git commit -m "Fixed: <what the change does>"`.
7. Wenn Sie ein GitHub-Konto haben: Forken Sie das Repository auf GitHub, fügen Sie Ihren Fork hinzu und pushen Sie:

   ```bash
   git remote add fork https://github.com/<you>/exponential.git
   git push -u fork fix-<short-name>
   ```

8. Eröffnen Sie den Pull Request über den Link, den GitHub ausgibt. Beschreiben Sie, was falsch war, was Sie geändert
   haben und wie Sie es geprüft haben.
9. Ohne Konto: Zeigen Sie dem Trainer stattdessen `git log --oneline -1` und `git show --stat`.

## Erwartetes Ergebnis

- Schritt 3: Die Hilfeprüfung meldet keine Fehler für den Ordner der geänderten Seite.
- Schritt 6: ein fokussierter Commit, dessen Betreff mit `Fixed:` (oder einem anderen erlaubten Präfix) beginnt.
- Schritt 8: ein offener Pull Request auf GitHub mit einer klaren Beschreibung, und CI startet dafür.
- Der Trainer bespricht die Beschreibung des Pull Requests einer Person mit der Gruppe.

## Quiz

1. Wohin pushen Sie Ihren Zweig: nach `origin` oder in Ihren Fork?
2. Welche Datei erklärt, wie man zum Kernel beiträgt?
   A. `README.md` B. `CONTRIBUTING.md` C. `composer.json` D. `share/filelist.md5`
3. Was gehört in die Beschreibung eines Pull Requests?
4. Wie antworten Sie auf einen Review-Kommentar, der eine Änderung verlangt?
5. Unter welcher Lizenz werden Code-Beiträge zu Exponential veröffentlicht?

<details>
<summary>Antworten</summary>

1. In Ihren Fork; `origin` zeigt auf das Projekt, für das Sie keine Schreibrechte haben.
2. **B.** `CONTRIBUTING.md` im Wurzelverzeichnis der Installation.
3. Was falsch war, was die Änderung tut, wie sie getestet wurde und was sich der Reviewer ansehen sollte.
4. Mit einer Änderung auf demselben Zweig, zusammengefasst und auf `main` rebast (`git rebase -i origin/main`) und mit
   `git push --force-with-lease` in den Fork gepusht; der Pull Request aktualisiert sich selbst.
5. Unter der GNU GPL, Version 2 oder später, wie das Projekt.

</details>

## Weiterführende Literatur

- [Mit Git entwickeln](../../../mit-git-entwickeln.md): Klone, Zweige, Pull Requests und auf dem Laufenden bleiben.
- [Versionsverwaltung und Releases](../../../../standards/versionsverwaltung-und-releases.md).
- [Freie Dokumentation](../../../freie-dokumentation.md): diese Seiten verbessern.
- [Umgangsformen bei Pull Requests](../../../designer/lebenszyklus/pull-requests-an-fremde-themes.md) und
  [Beiträge an das Upstream-Projekt zurückgeben](../../../designer/lebenszyklus/an-upstream-zurueckgeben.md).

## Verwandte Seiten

- [Modul 13: RAD, Velocity und Mitwirken](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
