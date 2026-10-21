---
title: Redaktionskalender
audience: government, editor, project-manager
category: public-sector
order: 120
summary: Veröffentlichungen in einer öffentlichen Stelle planen - ein Redaktionskalender ist eine Arbeitspraxis, kein Modul von Exponential; diese Seite zeigt, auf welche Daten das System reagieren kann (Bis Datum warten, Termine zum Verbergen und Zurückziehen) und wie Kalender, Freigaben und Cronjobs zusammenpassen.
keywords: Redaktionskalender, Inhaltskalender, Veröffentlichungsplan, zeitgesteuertes Veröffentlichen, Bis Datum warten, Sperrfrist, Pressemitteilung, Datum zum Verbergen, Kampagne, Planung, Kommunikation, editorial calendar, content calendar, publication plan, scheduled publishing, Wait until date, embargo, press release, hide date, campaign, planning, communications
type: help
---
# Redaktionskalender

Diese Seite richtet sich an Kommunikationsteams und Webverantwortliche, die Veröffentlichungen planen: Pressemitteilungen, Kampagnenseiten,
Beteiligungsverfahren, Jahresberichte. Ein Redaktionskalender ist eine **Arbeitspraxis**: Exponential hat keine Kalenderansicht und kein
Planungsmodul. Was das System tun kann, ist, auf Daten zu reagieren, die mit dem Inhalt gespeichert sind. Quellen: die Ereignistypen der Workflows in
`kernel/classes/workflowtypes/event/`, `settings/content.ini` und [Workflows](../../../../anleitungen/workflows.md).

## Inhalt
1. [Was das System zeitlich steuern kann](#was-das-system-zeitlich-steuern-kann)
2. [Zu einem Datum veröffentlichen: Bis Datum warten](#zu-einem-datum-veröffentlichen-bis-datum-warten)
3. [Zu einem Datum beenden: verbergen und zurückziehen](#zu-einem-datum-beenden-verbergen-und-zurückziehen)
4. [Den Kalender führen](#den-kalender-führen)
5. [Verwandte Seiten](#verwandte-seiten)

## Was das System zeitlich steuern kann

| Datum | Mechanismus | Braucht |
|---|---|---|
| Zu einem Datum live gehen | das Workflow-Ereignis **Bis Datum warten** | ein Datumsattribut an der Klasse; das Ereignis im Veröffentlichungs-Workflow; den Workflow-Cronjob |
| Zu einem Datum verbergen | den Cronjob `hide.php` | ein Datumsattribut, genannt in `[HideSettings] HideDateAttributeList[]`, Wurzelknoten in `RootNodeList[]` |
| Zu einem Datum entfernen | den Cronjob `unpublish.php` | ein Attribut `unpublish_date`, `[UnpublishSettings] ClassList[]` und `RootNodeList[]` |

## Zu einem Datum veröffentlichen: Bis Datum warten

Der Ereignistyp *Bis Datum warten* (`ezwaituntildate`) hält eine Version zurück bis zu einem Datum, das im Objekt gespeichert ist:

- Im Ereignis wählen Sie, welche Klassenattribute (vom Typ Datum, Uhrzeit oder Datum und Uhrzeit) es liest.
- Liegt das Datum in der Zukunft, antwortet das Ereignis „Event delayed until ...“, und der Prozess wartet auf den Workflow-Cronjob, der es bei
  jedem Lauf erneut versucht; sobald das Datum verstrichen ist, geht die Version weiter. Eine Option des Ereignisses setzt das
  Veröffentlichungsdatum des Objekts auf dieses Datum.
- Legen Sie es in den Workflow für **Vor dem Veröffentlichen von Inhalten**, zusammen mit dem Freigabe-Ereignis, wenn eine Freigabe nötig ist
  (erst freigeben, dann warten).
- Die Zeitsteuerung ist so genau wie der Zeitplan des Workflow-Cronjobs (alle 5 Minuten in der üblichen Crontab).

Inhalte mit Sperrfrist (zum Beispiel Statistiken, die zu einer festen Uhrzeit veröffentlicht werden) warten als ausstehende Version und sind vor
dem Datum nicht öffentlich; sie sind aber für Beschäftigte sichtbar, die ausstehende Versionen lesen dürfen. Prüfen Sie das anhand Ihrer Regeln
zu Sperrfristen.

## Zu einem Datum beenden: verbergen und zurückziehen

Beide Cronjobs stehen im globalen Teil von `settings/cronjob.ini` und wirken nur auf die Wurzelknoten und Klassen, die Sie konfigurieren.
Verbergen kann rückgängig gemacht werden; Zurückziehen entfernt den Inhalt. Die Einstellungen finden Sie unter
[Prüftermine mit Objektzuständen](pruefdaten-mit-objektzustaenden.md#muster-zu-einem-datum-verbergen).

## Den Kalender führen

Ein praktischer Aufbau für eine öffentliche Stelle:

| Element | Wo es liegt |
|---|---|
| Der Plan (was, wann, wer schreibt, wer gibt frei) | Ihr Planungswerkzeug oder ein gemeinsamer Kalender |
| Der Entwurf | **Meine Entwürfe** der Autorin oder des Autors |
| Die Freigabe | der Freigabe-Workflow; Freigebende sehen sie in ihrem Posteingang |
| Das Datum für den Livegang | das Datumsattribut, das *Bis Datum warten* liest |
| Das Enddatum | das Datumsattribut zum Verbergen |
| Bestätigung, dass es live gegangen ist | der Kanal `content` des Prüfprotokolls; eine Prüfung der Seite |

Wöchentlich: Vergleichen Sie den Plan mit **Setup > Workflow-Prozesse** (was wartet und warum) und mit dem, was live gegangen ist. Führen Sie
vor einer Veröffentlichung zu fester Uhrzeit den Cronjob-Teil frequent direkt nach dieser Uhrzeit von Hand aus, wenn es auf Minuten ankommt
([Einen Cronjob-Teil von Hand ausführen](../../../../../eng-us/help/government/tasks/operations/run-a-cronjob-part-by-hand.md)), und leeren Sie die Caches, wenn Seiten den alten
Stand zeigen ([Veralteter Cache](../vorfaelle/veralteter-cache.md)).

## Verwandte Seiten
- [Überblick über Freigabe-Workflows](freigabe-workflows-im-ueberblick.md)
- [Steuerung des Stilleitfadens](steuerung-des-styleguides.md)
- [Leitfaden zu Cronjobs](../../../../anleitungen/cronjobs.md)
- [Leitfaden zu Workflows](../../../../anleitungen/workflows.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
