---
title: "Verlauf und Rücknahme"
audience: admin, website-creator, developer
category: administration
order: 11
summary: Der Verlauf eines Ziels unter Setup > Statisches Veröffentlichen - jede Veröffentlichung und Rücknahme, die Tags und die Commits - und die Rückkehr zu einer früheren Veröffentlichung als neuer Commit, für Fachleute, mit Bestätigen Sie, dass Sie es sind.
keywords: Statisches Veröffentlichen, Verlauf, Rücknahme, zurücknehmen, zurückkehren, frühere Veröffentlichung, Tag, Commit, nie erzwungen, Setup > Statisches Veröffentlichen, Bestätigen Sie, dass Sie es sind, static publishing, history, rollback, roll back, go back, earlier publish, tag, static/target/n, commit, revert, never forced, Setup > Static publishing, staticpublish/history, staticpublish/publish, exp:static:rollback, verify it's you
type: help
---
# Verlauf und Rücknahme

Jede Veröffentlichung eines Ziels ist ein Commit mit einem eigenen Tag, `static/<target>/<n>`, und bleibt im Verlauf.
Die Rückkehr löscht nichts: Sie erzeugt einen **neuen** Commit mit den Dateien einer früheren Veröffentlichung, taggt
ihn mit der nächsten Nummer und pusht ihn. Kein Tag wird je verschoben, und nichts wird erzwungen.

## Was Sie am Ende haben

Die öffentliche Site, die wieder eine frühere Veröffentlichung zeigt, mit der Rücknahme im Verlauf.

## Der Verlauf

Öffnen Sie **Verlauf** am Ziel. Er listet auf:

- **Veröffentlichungen**: jede Veröffentlichung und Rücknahme, die neueste zuerst, mit Zeitpunkt, Zustand, Version
  (die Nummer ihres Tags, ein Strich, wenn sie keinen gemacht hat), kurzer Commit-Kennung (ein Link auf den Commit bei
  GitHub, wenn das Repository dort liegt), Push-Zustand, wer und wie viele Dateien sich geändert haben. Das Datum
  öffnet die Seite des Laufs. Auf einem schmalen Bildschirm ist jeder Lauf ein Block beschrifteter Zeilen.
- **Tags**: die neuesten zuerst, jedes mit seinem Commit (ein Link auf den Commit bei GitHub, wenn das Repository dort
  liegt).
- **Commits**: die letzten Commits des Branches.

![Der Verlauf: der Kasten Auf GitHub, die Veröffentlichungen mit Version und Commit, das Formular für die Rücknahme, die Tags und die Commits](../bilder/statisches-veroeffentlichen/history-light.webp)

Die Karte des Ziels in **Setup > Statisches Veröffentlichen** zeigt dasselbe in Kurzform: die veröffentlichte
**Version** mit ihrem Tag, den **Commit** (ein Link auf den Commit bei GitHub, wenn das Repository dort liegt) und das
Badge unter **Letzte Veröffentlichung**.

![Ein Ziel in Setup > Statisches Veröffentlichen: seine Siteaccesses, Version 4 mit dem Tag static/demo/4, der Commit, die letzte Veröffentlichung und die veralteten Seiten](../bilder/statisches-veroeffentlichen/dashboard-version-light.webp)

## Zurücknehmen

**Zu einer früheren Veröffentlichung zurückkehren** ist für Fachleute und braucht die Richtlinie
`staticpublish/publish`:

1. Wählen Sie die Veröffentlichung, zu der Sie zurückkehren möchten.
2. Haken Sie **Ich weiß, was das ändert** an.
3. Geben Sie den Namen des Tags noch einmal ein, zum Beispiel `static/github-public/2`.
4. Drücken Sie auf **Zurücknehmen** und bestätigen Sie mit **Bestätigen Sie, dass Sie es sind**.

Die Rücknahme läuft im Hintergrund, und ihre Seite öffnet sich. Ein noch nicht veröffentlichter Build blockiert eine
Rücknahme: Veröffentlichen oder verwerfen Sie ihn zuerst
([Build, Vorschau und Veröffentlichen](build-vorschau-und-veroeffentlichen.md#veröffentlichen-oder-verwerfen)).

Um eine Rücknahme rückgängig zu machen, nehmen Sie erneut zur Veröffentlichung davor zurück, oder bauen und
veröffentlichen Sie einfach.

## Auf der Konsole

`./console exp:static:rollback <target> <n>` listet auf, was sich ändern würde; mit `--apply` nimmt es zurück. Mehr in
[Verlauf und Zurücknehmen auf der Konsole](../../anleitungen/statisches-veroeffentlichen-verlauf-und-zuruecknehmen.md).

## Nächster Schritt

[Wer veröffentlichen darf](wer-veroeffentlichen-darf-rollen-und-richtlinien.md).
