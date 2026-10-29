---
title: Mit Pull Requests zurückgeben
audience: developer, designer, app-creator
category: extension-development
order: 80
summary: Schicken Sie eine Korrektur oder Funktion als Pull Request an den Kernel oder an die Erweiterung eines anderen - eine Änderung je Branch, Tests, eine Beschreibung dessen, was kaputt war und wie Sie es geprüft haben, und was danach zu tun ist.
keywords: Pull Request, beitragen, zurückgeben, GitHub, Branch, Fork, Beschreibung, Tests, Review, Kernel, Erweiterung, Upstream, CONTRIBUTING, pull request, contribute, description, review, extension, upstream
type: help
---
# Mit Pull Requests zurückgeben

Teil 5, Kapitel 8. Ein Pull Request bittet die Maintainer eines Repositorys, Ihre Commits zu übernehmen. So wird aus
einer Korrektur, die Sie für sich selbst gemacht haben, eine Korrektur für alle, und so hören Sie auf, sie allein zu
tragen. Dieses Kapitel geht einen Pull Request von Anfang bis Ende durch. Es gilt für den Kernel
(`se7enxweb/exponential`) und für jede Erweiterung auf GitHub.

## Bevor Sie beginnen

1. **Lesen Sie die Regeln des Projekts.** Die des Kernels stehen in `CONTRIBUTING.md` im Wurzelverzeichnis des
   Repositorys: die Lizenz, unter die Ihr Beitrag fällt (GPL 2 oder später), das Format der Commit-Nachrichten, das
   Dateimanifest, die Gestaltungsregeln der Administration. Eine Erweiterung kann eine eigene `CONTRIBUTING.md` haben
   oder keine; dann sehen Sie sich an, wie ihre letzten Commits geschrieben sind.
   **Für den Kernel: Rechnen Sie mit vielen Review-Runden.** 7x prüft einen Commit oder Pull Request in viel mehr
   Runden als üblich, manchmal in Hunderten von Überarbeitungen; seien Sie bereit, Ihre Änderung so lange zu
   überarbeiten, bis sie einfach anzunehmen ist
   ([Code reviews](../../../../../CONTRIBUTING.md#code-reviews), auf Englisch).
   **Für den Kernel: Übertragung des Urheberrechts (CLA).** Die Exponential Foundation und 7x übernehmen einen
   Code-Beitrag zum Kern-Kernel, der in beliebiger Form eingereicht wird (Pull Request, Patch, Issue oder E-Mail), erst,
   nachdem Sie das Contributor Licensing Agreement unterzeichnet haben. **Mit der Unterzeichnung und der Einreichung
   übertragen Sie das Urheberrecht an Ihrem Beitrag auf 7x, das damit sein Inhaber wird, und geben die Kontrolle
   darüber auf:** 7x darf ihn auf jede Weise verwenden, ändern, neu lizenzieren und verbreiten, auch unter anderen
   Lizenzen und in kommerziellen Produkten, ohne Sie zu fragen oder zu bezahlen, und Sie können ihn weder zurückziehen
   noch Bedingungen daran knüpfen. Wie Sie unterzeichnen:
   [Copyright assignment (CLA)](../../../../../CONTRIBUTING.md#copyright-assignment-cla) (auf Englisch). Für die
   Erweiterungen, die Sie selbst schreiben und behalten, gilt das nicht.
2. **Suchen Sie nach einem vorhandenen Issue oder Pull Request** zum selben Thema. Wenn es einen gibt, kommentieren Sie
   dort, statt einen zweiten zu eröffnen.
3. **Fragen Sie bei einer großen Änderung zuerst.** Eröffnen Sie ein Issue, das beschreibt, was Sie tun möchten und
   warum. Ein Maintainer hat vielleicht schon einen Plan oder einen Grund, warum es nicht funktionieren kann.
4. **Prüfen Sie, ob der „Fehler“ eine negative Funktion ist.** Manches Verhalten ist beabsichtigt oder
   Auslegungssache, und andere Sites verlassen sich darauf, in Exponential wie in jeder Erweiterung. Eine Änderung
   daran braucht eine klare Begründung und oft mehrere Review-Runden; bieten Sie lieber eine Option an als einen neuen
   Standard ([Negative Funktionen](../../projekt/unterstuetzung-erhalten.md#negative-funktionen)).

## 1. Eine Änderung, ein Branch

Forken Sie das Repository, klonen Sie Ihren Fork und legen Sie einen Branch an, der nach der Änderung benannt ist
([Auf GitHub forken, Schritt für Schritt](auf-github-forken.md)). Beschränken Sie den Branch auf ein Thema. Zwei
voneinander unabhängige Korrekturen sind zwei Pull Requests: Jeder kann für sich geprüft und übernommen werden, und
ein Problem mit dem einen hält den anderen nicht auf.

## 2. Ändern Sie es so, wie das Projekt es macht

- Folgen Sie dem Code-Stil der Dateien, die Sie anfassen, auch wenn Sie es anders schreiben würden.
- Schreiben Sie Commit-Nachrichten, aus denen das Projekt ein Changelog bauen kann
  ([Commit-Nachrichten, aus denen das Projekt ein Changelog bauen kann](commit-nachrichten.md)). Der commit-msg-Hook
  des Kernels (führen Sie in Ihrem Klon einmal `make hooks` aus) prüft den Betreff für Sie.
- Halten Sie im Kernel `share/filelist.md5` aktuell (`make manifest-fix`); sein pre-commit-Hook prüft es.
- Fügen Sie einen Test hinzu oder ändern Sie einen, der ohne Ihre Änderung fehlschlägt und mit ihr besteht.
- Aktualisieren Sie die Dokumentation, die von der Änderung betroffen ist.

## 3. Prüfen Sie es

Führen Sie die Prüfungen des Projekts aus, bevor Sie pushen. Für den Kernel listen `CONTRIBUTING.md` und
[Qualitätsprüfungen](../../../funktionen/6.0/qualitaetspruefungen.md) (auf Englisch) sie auf; bei einer Erweiterung
zeigen ihr Verzeichnis `tests/` oder ihr CI-Workflow, was sie ausführt. Verwenden Sie die Funktion danach von Hand.

## 4. Schreiben Sie die Beschreibung

Die Beschreibung ist das Erste, was der Reviewer liest. Eine gute beantwortet vier Fragen:

| Frage | Beispiel |
|---|---|
| Was war kaputt, oder was fehlt? | „Every sign-in from a real browser was answered with 403.“ |
| Warum? | „With `Referrer-Policy: no-referrer`, browsers send `Origin: null` with the form POST.“ |
| Was tut die Änderung? | „Sends `same-origin` instead. One line.“ |
| Wie wurde sie geprüft? | „A new test fails without the change and passes with it; the full suite has the same results as main.“ |

Dieses Beispiel ist ein echter Pull Request, vollständig erzählt in
[Fallstudie: ein Pull Request an einen Upstream-Autor](fallstudie-ein-pull-request-an-einen-upstream-autor.md).

Sagen Sie auch, was Sie **nicht** geprüft haben und warum, und was Sie fehlschlagen sahen, das nicht von Ihnen stammt
(ein Test, der auch auf main fehlschlägt). Das erspart dem Reviewer, es selbst zu finden und sich zu wundern.

## 5. Eröffnen Sie ihn

```bash
git push -u origin fix-signin-origin
```

GitHub bietet dann **Compare & pull request** an. Prüfen Sie, dass das Basis-Repository das Original ist (nicht Ihr
Fork) und der Basis-Branch derjenige, auf dem das Projekt entwickelt (`main` oder `master`). Fügen Sie die
Beschreibung ein. Eröffnen Sie ihn als **Draft**, wenn Sie noch etwas ändern möchten.

## 6. Nachdem Sie ihn eröffnet haben

- **Die CI läuft.** Wenn eine Prüfung fehlschlägt, lesen Sie ihr Protokoll, korrigieren Sie es auf demselben Branch
  und pushen Sie erneut; der Pull Request aktualisiert sich selbst.
- **Beantworten Sie Review-Kommentare** mit Änderungen oder mit Begründungen. Markieren Sie Unterhaltungen nur dann
  als erledigt, wenn der Reviewer zustimmt oder wenn es im Projekt üblich ist, dass der Autor sie erledigt.
- **Halten Sie ihn aktuell.** Wenn sich der Basis-Branch weiterbewegt und Konflikte auftreten, holen Sie die
  Änderungen herein ([Einen Fork mit seiner Quelle synchron halten](einen-fork-synchron-halten.md)).
- **Für den Kernel: squashen und rebasen.** 7x verlangt, dass der Autor den Pull Request in logische Commits
  zusammenfasst (meist einen) und ihn, solange er offen ist, auf `main` rebast, damit das Mergen sauber ist:
  `git fetch origin`, `git rebase -i origin/main`, dann `git push --force-with-lease` nur auf den Branch Ihres Pull
  Requests. Sobald er übernommen ist, wird die Historie von `main` nie umgeschrieben; eine spätere Korrektur ist ein
  neuer Pull Request ([Code reviews](../../../../../CONTRIBUTING.md#code-reviews), auf Englisch).
- **Haben Sie Geduld.** Ehrenamtliche Maintainer antworten, wenn sie können. Eine freundliche Erinnerung nach zwei
  oder drei Wochen ist in Ordnung.

## 7. Nachdem er übernommen wurde

- Löschen Sie den Branch (GitHub bietet dafür eine Schaltfläche an).
- Synchronisieren Sie `main` Ihres Forks.
- Wenn Sie auf einer Website einen Fork als Übergangslösung verwendet haben, stellen Sie die Website wieder auf das
  Release des Originals um, das Ihre Änderung enthält.

## Verwandte Seiten

- Zurück: [Namensnennung und Urheberrechtshinweise](namensnennung-und-urheberrechtshinweise.md)
- Weiter: [Pull-Request-Etikette](pull-request-etikette.md)
- [Erweiterungen zurückgeben](../../erweiterungen-zurueckgeben.md)
- [Mit Git entwickeln](../../mit-git-entwickeln.md)
- [About pull requests](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/proposing-changes-to-your-work-with-pull-requests/about-pull-requests) in der GitHub-Dokumentation (auf Englisch)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
