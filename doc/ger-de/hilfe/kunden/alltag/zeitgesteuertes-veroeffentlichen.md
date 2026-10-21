---
title: Zeitgesteuertes Veröffentlichen
audience: customer, editor, admin
category: customer-success
order: 12
summary: Was eine Exponential-Website an einem Datum von selbst tun kann - mit dem Workflow Bis Datum warten zu einem Datum veröffentlichen, mit den ausgelieferten Cronjobs zu einem Datum verbergen oder entfernen, Landingpages über eine Zeitregel umschalten, Newsletter nach Plan versenden -, was jedes davon von Ihrem Administrator braucht und wie Sie prüfen, dass es funktioniert hat.
keywords: zeitgesteuertes Veröffentlichen, später veröffentlichen, Sperrfrist, bis Datum warten, Datum für Depublizierung, Datum zum Verbergen, Cronjob, Zeitbedingung, Layout-Regel, Workflow, scheduled publishing, publish later, embargo, wait until date, unpublish date, hide date, cronjob, time condition, layout rule
type: help
---
# Zeitgesteuertes Veröffentlichen

Diese Seite ist für Betreiber und Redakteure, die möchten, dass Inhalte an einem Datum erscheinen, verschwinden oder
sich ändern, ohne dass in diesem Moment jemand eine Schaltfläche drückt. Exponential erledigt das mit Werkzeugen, die
Ihr Administrator einschaltet; keines davon ist von Haus aus für jede Klasse aktiv. Fragen Sie, welche davon Ihre
Website hat, bevor Sie ein Datum zusagen.

## Inhalt

1. [Was an einem Datum geschehen kann](#was-an-einem-datum-geschehen-kann)
2. [Zu einem Datum veröffentlichen](#zu-einem-datum-veröffentlichen)
3. [Zu einem Datum verbergen oder entfernen](#zu-einem-datum-verbergen-oder-entfernen)
4. [Landingpages für einen Zeitraum](#landingpages-für-einen-zeitraum)
5. [Alles hängt von Cron ab](#alles-hängt-von-cron-ab)
6. [Prüfen, dass es funktioniert hat](#prüfen-dass-es-funktioniert-hat)

## Was an einem Datum geschehen kann

| Bedarf | Werkzeug | Eingerichtet von |
|---|---|---|
| Einen Artikel zu einem künftigen Datum veröffentlichen | das Workflow-Ereignis *Bis Datum warten* beim Veröffentlichen | Administrator ([ezwaituntildate](../../../../eng-us/examples/workflow-events/ezwaituntildate.md)) |
| Ein Element zu einem Datum verbergen | der Cronjob `hide.php` mit einem Datumsfeld pro Klasse | Administrator (`settings/content.ini [HideSettings]`) |
| Ein Element zu einem Datum entfernen | der Cronjob `unpublish.php` und ein Feld `unpublish_date` | Administrator (`settings/content.ini [UnpublishSettings]`) |
| Ein Kampagnen-Layout für einen Zeitraum anzeigen | eine Layout-Regel mit einer Bedingung `time` | Layout-Redaktion ([Layout-Regeln](../../../../eng-us/help/creators/website-creators/layout-rules.md)) |
| Einen Newsletter zu einer Uhrzeit oder jede Woche versenden | geplanter und wiederkehrender Versand | wer den Newsletter versendet ([Ausgaben schreiben und versenden](../../unternehmen/newsletter/ausgaben-schreiben-und-versenden.md)) |

## Zu einem Datum veröffentlichen

Das Ereignis *Bis Datum warten* läuft vor dem Veröffentlichen. Enthält das gewählte Datumsfeld des Elements ein Datum
in der Zukunft, wartet die Version; das Ereignis vermerkt „Event delayed until“ mit diesem Datum, und der
Workflow-Cronjob veröffentlicht sie, sobald das Datum vorbei ist. Eine Option des Ereignisses kann außerdem das
Veröffentlichungsdatum des Elements auf dieses Datum setzen
(`kernel/classes/workflowtypes/event/ezwaituntildate/ezwaituntildatetype.php`).

Für Redakteure sieht das so aus:

1. Füllen Sie das Datumsfeld aus, das Ihr Administrator benannt hat (zum Beispiel ein Veröffentlichungsdatum), und
   drücken Sie **Zur Veröffentlichung schicken**.
2. Die Version ist **ausstehend**: Sie erscheint unter *Meine Warteliste*, nicht auf der Website.
3. Nach dem Datum wird sie beim nächsten Lauf des Workflow-Cronjobs veröffentlicht.

> **Hinweis**
> Ist das Datumsfeld leer oder liegt das Datum schon in der Vergangenheit, wird das Element sofort veröffentlicht.
> Vereinbaren Sie im Team, welches Feld maßgeblich ist, und setzen Sie es weit oben ins Bearbeitungsformular.

## Zu einem Datum verbergen oder entfernen

Zwei Cronjobs des standardmäßigen (globalen) Cron-Teils reagieren auf Daten, die in den Inhalten gespeichert sind
(`settings/cronjob.ini`):

- **`hide.php`** verbirgt Elemente, deren Datumsfeld, pro Klasse in `[HideSettings] HideDateAttributeList[]` benannt,
  überschritten ist, unterhalb der Knoten in `RootNodeList[]`. Verborgene Elemente bleiben in der Administration und
  lassen sich wieder einblenden ([Veröffentlichen, Versionen und der Papierkorb](../../veroeffentlichen-und-versionen.md#ein-objekt-verstecken)).
- **`unpublish.php`** entfernt Elemente der Klassen in `[UnpublishSettings] ClassList[]` unterhalb von `RootNodeList[]`,
  wenn ihr Feld `unpublish_date` überschritten ist. Das Entfernen ist das Entfernen des Kernels in den **Papierkorb**,
  sodass das Element von dort wiederhergestellt werden kann (`kernel/private/classes/cronjobs/unpublish.php`).

Beide Listen sind in der ausgelieferten `settings/content.ini` leer, es geschieht also nichts, bis Ihr Administrator
sie füllt. Ziehen Sie das Verbergen vor, wenn die Inhalte zurückkommen könnten; siehe [Archivieren und der Papierkorb](archivieren-und-der-papierkorb.md).

## Landingpages für einen Zeitraum

Ordnen Sie für eine Kampagne der Landingpage ein Layout mit einer Regel zu, deren Bedingung `time` die Werte `from` und
`to` hat, mit höherer Priorität als die normale Regel der Seite. Ein leerer Zeitraum passt immer
([Layout-Regeln](../../../../eng-us/help/creators/website-creators/layout-rules.md)). Vor und nach dem Zeitraum wird das normale Layout
angezeigt. Siehe [Landingpages mit Layouts](../wachstum/landingpages-mit-layouts.md).

## Alles hängt von Cron ab

> **Warnung**
> Wartende Versionen werden vom Workflow-Cronjob veröffentlicht (`workflow.php`, Teil *frequent*); Verbergen und
> Entfernen laufen im globalen Teil. Wenn Cron nicht läuft, geschieht an dem Datum nichts
> ([Workflows](../../../anleitungen/workflows.md), [Cronjobs](../../../anleitungen/cronjobs.md)). Die Seite Cronjobs zeigt jeden
> Teil als *Geplant* oder *Nicht geplant* mit seinem nächsten Lauf.

Der Zeitpunkt der Veröffentlichung ist daher „der erste Cron-Lauf nach dem Datum“, nicht die genaue Minute. Fragen Sie
Ihren Hoster, wie oft der Teil frequent läuft.

## Prüfen, dass es funktioniert hat

- [ ] Am Tag davor: Die Version ist ausstehend, und **Setup > Workflow-Prozesse** listet sie als wartend.
- [ ] Am Tag selbst, nach dem nächsten Cron-Lauf: Das Element ist auf der Website, abgemeldet betrachtet.
- [ ] Menüs, Listen und die Startseite zeigen es (Caches werden beim Veröffentlichen geleert; wenn eine Liste alt
      aussieht, sagen Sie Ihrem Administrator Bescheid).
- [ ] Beim Verbergen oder Entfernen: Das Element ist verborgen oder im Papierkorb, und nichts verweist mehr darauf
      ([Inhalts-Audits](inhalts-audits.md)).

## Verwandte Seiten

- [Workflows](../../../anleitungen/workflows.md)
- [Redaktionskalender](redaktionskalender.md)
- [Freigabe und Prüfung](freigabe-und-pruefung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
