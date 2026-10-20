---
title: Neuigkeiten und Ankündigungen
audience: businesses, editor, website-creator
category: business-solutions
order: 50
summary: Wie ein Intranet mit Exponential Firmenneuigkeiten und Ankündigungen veröffentlicht - Artikelklassen, ein Nachrichtenordner pro Zielgruppe, eine Startseite mit dem Layout-Editor oder ezflow, zeitgesteuertes Veröffentlichen, Benachrichtigungen, Feeds und das Veröffentlichen einer Korrektur ohne neue Mail.
keywords: Neuigkeiten im Intranet, Ankündigungen, Artikel, Startseite, Landingpage, Layout-Editor, ezflow, Bis Datum warten, Benachrichtigungen, benachrichtigen, RSS, ohne Benachrichtigung veröffentlichen, Newsletter, interne Kommunikation, intranet news, announcements, articles, front page, landing page, layouts editor, Wait until date, notifications, notify me, publish without notification, newsletter, staff communication
type: help
---
# Neuigkeiten und Ankündigungen

Diese Seite ist für Teams der internen Kommunikation und Intranetverantwortliche. Sie erklärt, wie Firmenneuigkeiten ins
Intranet kommen, wie eine Startseite die neuesten Einträge zeigt, wie Mitarbeiter davon erfahren und welche Abteilung
was veröffentlichen darf. Jede hier genannte Funktion gehört zu Exponential oder zu einer Erweiterung, die in
`composer.json` aufgeführt ist.

## Inhalt

1. [Was Mitarbeiter erwarten](#was-mitarbeiter-erwarten)
2. [Neuigkeiten als Inhalte](#neuigkeiten-als-inhalte)
3. [Wer Neuigkeiten veröffentlichen darf](#wer-neuigkeiten-veröffentlichen-darf)
4. [Die Startseite](#die-startseite)
5. [Zu einer festgelegten Zeit veröffentlichen](#zu-einer-festgelegten-zeit-veröffentlichen)
6. [Mitarbeiter informieren](#mitarbeiter-informieren)
7. [Korrekturen ohne zweite Mail](#korrekturen-ohne-zweite-mail)
8. [Feeds](#feeds)
9. [Checkliste](#checkliste)
10. [Verwandte Seiten](#verwandte-seiten)

## Was Mitarbeiter erwarten

- Die neuesten Firmenneuigkeiten auf der Startseite.
- Neuigkeiten der Abteilung dort, wo die Abteilung ihren Platz hat.
- Eine Möglichkeit, informiert zu werden, ohne überschwemmt zu werden.
- Ein Archiv, das sie durchsuchen können.

## Neuigkeiten als Inhalte

Neuigkeiten sind Inhaltsobjekte einer Artikelklasse, abgelegt in einem Ordner. Das fertige Website-Design `ezwebin`
bringt Klassen und Templates für Artikel, Blogs, Veranstaltungen, Foren und mehr mit
([ezwebin](../../../../eng-us/features/6.0/extensions/ezwebin.md)), und die Seite
[Inhaltsklassen für Websites](../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md) erklärt, wie Sie eigene
wählen oder definieren. Jeder Artikel hat eigene Versionen, einen Eigentümer und ein Veröffentlichungsdatum.

Eine einfache Struktur, die für viele Organisationen funktioniert:

| Ordner | Sektion | Leser | Redakteure |
|---|---|---|---|
| Firmenneuigkeiten | Mitarbeiter (oder Standard in einem geschlossenen Intranet) | alle Mitarbeiter | interne Kommunikation |
| Neuigkeiten aus dem Vertrieb | Vertrieb | alle Mitarbeiter oder nur der Vertrieb | Redakteure des Vertriebs |
| Ankündigungen der Geschäftsleitung | Geschäftsleitung | alle Mitarbeiter | Büro der Geschäftsleitung |

Die Sektion entscheidet, wer liest und wer bearbeitet ([Abteilungen, Rollen und Sektionen](abteilungen-rollen-und-sektionen.md)).

## Wer Neuigkeiten veröffentlichen darf

Eine Rolle lässt sich nach Klasse einschränken, sodass ein Redakteur des Nachrichtenbereichs Artikel anlegen darf und
sonst nichts, und nach Sektion, sodass Redakteure einer Abteilung nur in ihrem eigenen Bereich veröffentlichen
([Rollen und Richtlinien, 3](../../../anleitungen/rollen-und-richtlinien.md#3-eine-rolle-mit-eingeschränkten-richtlinien-anlegen)). Wenn Texte
geprüft werden müssen, bevor sie live gehen, fügen Sie einen Freigabe-Workflow hinzu
([Freigabe-Workflows](freigabe-workflows.md)).

## Die Startseite

Zwei Werkzeuge bauen Startseiten mit Blöcken wie „neueste Neuigkeiten“, „hervorgehobener Eintrag“ und „kommende
Veranstaltungen“:

| Werkzeug | Was es ist | Quelle |
|---|---|---|
| Der Layout-Editor | Landingpages aus Layouttypen, Zonen und Blöcken, in einem Browser-Editor bearbeitet | [Landingpages mit Layouts](../../../../eng-us/help/creators/website-creators/landing-pages-with-layouts.md) |
| `ezflow` | die klassischen Landingpages mit Blöcken und Zeitleisten | [Klassische Landingpages mit ezflow](../../../../eng-us/help/creators/website-creators/classic-landing-pages-with-ezflow.md) |

Der Vergleich der beiden steht unter
[ezflow oder der Layout-Editor](../../../../eng-us/help/creators/website-creators/classic-landing-pages-with-ezflow.md#ezflow-or-the-layouts-editor).
Ein gemeinsamer Kopf- und Fußbereich für jede Seite ist unter
[Gemeinsamer Kopf- und Fußbereich](../../../../eng-us/help/creators/website-creators/shared-header-and-footer.md) beschrieben.

## Zu einer festgelegten Zeit veröffentlichen

Workflows haben neben Approve den Ereignistyp **Bis Datum warten**
([Workflows, 1](../../../anleitungen/workflows.md#1-workflows-ereignisse-trigger-und-prozesse)). Er hält das Veröffentlichen
bis zu dem Datum in einem Datumsfeld der Klasse zurück, wobei das Feld im Ereignis gewählt wird
(`kernel/classes/workflowtypes/event/ezwaituntildate/`), was zu einer im Voraus vorbereiteten Ankündigung passt. Wie
jeder wartende Workflow braucht es einen laufenden Workflow-Cronjob (`workflow.php`); ohne ihn wird nichts Wartendes je
veröffentlicht ([Workflows, 6](../../../anleitungen/workflows.md#6-der-workflow-cronjob-wie-prozesse-fortgesetzt-werden)). Bitten Sie
Ihren Partner, es einzurichten und mit einem Artikel zu testen, bevor Sie sich darauf verlassen.

## Mitarbeiter informieren

| Weg | Wie er funktioniert | Quelle |
|---|---|---|
| Benachrichtigungen | ein Mitarbeiter folgt dem Nachrichtenordner (**Benachrichtigen**) und bekommt eine Mail, wenn darunter etwas veröffentlicht wird, sofort oder als täglicher, wöchentlicher oder monatlicher Digest | [Benachrichtigungen: die Anleitung für Benutzer](../../../../eng-us/features/6.0/notifications.md) |
| Ein Newsletter | `cjw_newsletter` schickt Ausgaben an Listen mit Double-Opt-in | [Bereich Newsletter](../../../../eng-us/help/businesses/newsletters/README.md) |
| Die Startseite | die neuesten Einträge in einem Block | siehe oben |

Benachrichtigungen verschickt der Lauf der Benachrichtigungen, den die Cronjob-Gruppe `frequent` startet
([Benachrichtigungen für Mitarbeiter](benachrichtigungen-fuer-mitarbeiter.md)).

## Korrekturen ohne zweite Mail

Ein Tippfehler, der nach dem Veröffentlichen korrigiert wird, sollte nicht allen erneut eine Mail schicken. Mit
`notification.ini [NotificationSettings] PublishWithoutNotification=enabled` und der Richtlinie
`content/publish_without_notification` sehen Redakteure neben der Schaltfläche zum Veröffentlichen **Ohne
Benachrichtigung veröffentlichen**. Eine so veröffentlichte Version erzeugt kein Benachrichtigungsereignis;
Freigabemails werden weiterhin verschickt
([Anleitung zu Benachrichtigungen für Administratoren, 8](../../../anleitungen/benachrichtigungen-administrator.md#8-veröffentlichen-ohne-benachrichtigung)).

## Feeds

Setup > RSS (`/rss/list`) veröffentlicht Feeds der neuesten Objekte unterhalb gewählter Orte, als RSS 1.0, RSS 2.0,
Atom, OPML oder als Feed für Apple Podcasts ([RSS-Feeds](../../../anleitungen/rss-feeds.md)).

> **Hinweis**
> In einem Intranet, dessen Siteaccess `RequireUserLogin=true` hat, geht jede Anfrage, die nicht in
> `AnonymousAccessList[]` steht, auf die Anmeldeseite (`settings/site.ini`). Die ausgelieferte Liste enthält
> `rss/feed` nicht; ein Feedreader, der sich nicht anmelden kann, bekommt also keinen Feed. Entscheiden Sie bewusst,
> ob Neuigkeiten das Intranet auf diesem Weg verlassen dürfen.

## Checkliste

- [ ] Nachrichtenordner angelegt, jeder in der richtigen Sektion.
- [ ] Eine Rolle für Nachrichtenredakteure, nach Klasse und Sektion eingeschränkt.
- [ ] Freigabe für Ankündigungen der Geschäftsleitung entschieden.
- [ ] Block auf der Startseite für die neuesten Neuigkeiten.
- [ ] Cronjob für Benachrichtigungen läuft; Mitarbeitern erklärt, wie sie Neuigkeiten folgen.
- [ ] Ohne Benachrichtigung veröffentlichen für Redakteure eingeschaltet, die Tippfehler korrigieren.
- [ ] Entscheidung zu Feeds für das Intranet getroffen.

## Verwandte Seiten

- [Benachrichtigungen für Mitarbeiter](benachrichtigungen-fuer-mitarbeiter.md)
- [Freigabe-Workflows](freigabe-workflows.md)
- [Inhalte bearbeiten](../../inhalte-bearbeiten.md)
- [Veröffentlichen und Versionen](../../veroeffentlichen-und-versionen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
