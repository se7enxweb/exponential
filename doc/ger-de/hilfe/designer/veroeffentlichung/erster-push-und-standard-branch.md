---
title: "Der erste Push und der Standard-Branch"
audience: designer
category: theme-distribution
order: 60
summary: Ein Theme zum ersten Mal mit main als Standard-Branch pushen, main vor Force-Pushes schützen und entscheiden, wie Arbeits-Branches und Pull-Requests hineinfließen.
keywords: erster Push, Standard-Branch, main, Branch-Schutz, Force-Push, Pull-Request, git push -u, dev-main, first push, default branch, branch protection, force push, pull request
type: help
---
# Der erste Push und der Standard-Branch

Diese Seite ist für Designer, die zum ersten Mal ein Repository veröffentlichen.

## Warum main

Die Repositories von Exponential verwenden `main` als Standard-Branch. Composer-Nutzer, die den neuesten
unveröffentlichten Code Ihres Themes wollen, verlangen `dev-main`; ein anderer Branch-Name bedeutet eine andere
Bedingung (`dev-master`, `dev-trunk`) und Verwirrung. Verwenden Sie `main` von Anfang an.

## Schritt für Schritt

1. Prüfen Sie den Branch-Namen: `git branch --show-current` gibt `main` aus. Gibt es etwas anderes aus:
   `git branch -m main`.
2. Pushen Sie und setzen Sie den Upstream:

   ```bash
   git push -u origin main
   ```

   Erwartet: `branch 'main' set up to track 'origin/main'`.
3. Stellen Sie sicher, dass GitHub ihn als Standard verwendet:

   ```bash
   gh repo view yourvendor/mytheme --json defaultBranchRef --jq .defaultBranchRef.name
   ```

   Erwartet: `main`.
4. Schützen Sie ihn vor Force-Pushes und Löschen (**Settings > Branches** oder **Rules**). Eine Historie, die andere
   geholt haben, darf nie umgeschrieben werden.

## Wie Änderungen hineinfließen

| Weg | Passt zu |
|---|---|
| Direkt auf `main` committen | Einem Betreuer, kleinen Korrekturen |
| Ein Branch pro Änderung, per Pull-Request zusammengeführt | Mehreren Personen, wenn Reviews gewünscht sind |
| Pull-Requests aus Forks | Externen Mitwirkenden |

Wie auch immer Sie es halten, jedes Release ist ein Tag auf `main` ([GitHub-Releases](github-releases.md)).

## Verwandte Seiten

- [Das GitHub-Repository anlegen](das-github-repository-anlegen.md)
- [Pull-Requests an fremde Themes](../lebenszyklus/pull-requests-an-fremde-themes.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
