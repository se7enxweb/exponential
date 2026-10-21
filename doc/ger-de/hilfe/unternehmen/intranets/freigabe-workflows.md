---
title: Freigabe-Workflows
audience: businesses, admin, project-manager
category: business-solutions
order: 60
summary: Wie eine Organisation sicherstellt, dass Texte geprüft werden, bevor Mitarbeiter, Partner oder die Öffentlichkeit sie sehen - das Workflow-Ereignis Approve, der Posteingang der Zusammenarbeit, der Workflow-Cronjob, Prüfstadien mit Objektzuständen und wie Sie zwischen ihnen wählen.
keywords: Freigabe, Freigabe-Workflow, Ereignis Approve, Vier-Augen-Prinzip, Prüfung, Zusammenarbeit, Workflow-Cronjob, Workflow-Prozesse, Objektzustände, Prüfzustände, Staging-Sektion, wartend, Governance im Intranet, approval, approval workflow, Approve event, four-eyes principle, review, collaboration, workflow cronjob, workflow processes, object states, review states, staging section, pending, intranet governance
type: help
---
# Freigabe-Workflows

Diese Seite ist für Führungskräfte, die ein zweites Paar Augen auf das brauchen, was veröffentlicht wird:
Personalrichtlinien, Ankündigungen der Geschäftsleitung, Preise in einem Partnerportal. Exponential bietet dafür zwei
eingebaute Wege und einen einfachen dritten. Die Seite erklärt, wie sich jeder für die Beteiligten anfühlt, und
verlinkt die Anleitungen Schritt für Schritt.

## Inhalt

1. [Drei Wege der Freigabe](#drei-wege-der-freigabe)
2. [Freigabe vor dem Veröffentlichen](#freigabe-vor-dem-veröffentlichen)
3. [Prüfstadien mit Objektzuständen](#prüfstadien-mit-objektzuständen)
4. [Eine Staging-Sektion](#eine-staging-sektion)
5. [Wählen](#wählen)
6. [Was laufen muss](#was-laufen-muss)
7. [Nachweis, wer was freigegeben hat](#nachweis-wer-was-freigegeben-hat)
8. [Checkliste](#checkliste)
9. [Verwandte Seiten](#verwandte-seiten)

## Drei Wege der Freigabe

| Weg | In einem Satz | Anleitung |
|---|---|---|
| Freigabe vor dem Veröffentlichen | die Veröffentlichung wartet, bis eine freigebende Person im Posteingang der Zusammenarbeit zustimmt | [Workflows, 3](../../../anleitungen/workflows.md#3-durchgerechnetes-beispiel-freigabe-vor-dem-veröffentlichen) |
| Prüfstadien | Inhalte sind veröffentlicht, aber für die Öffentlichkeit erst lesbar, wenn ihr Zustand Freigegeben ist | [Objektzustände, 9](../../../anleitungen/objektzustaende.md#9-ein-durchgearbeitetes-beispiel-ein-prüf-workflow) |
| Staging-Sektion | Seiten werden in einer Sektion vorbereitet, die Leser nicht sehen, und dann in einem Schritt freigegeben | [Sektionen, 13.3](../../../anleitungen/sektionen.md#133-inhalte-vorbereiten-bevor-sie-öffentlich-werden) |

## Freigabe vor dem Veröffentlichen

Ein **Workflow** ist eine Liste von Schritten; ein **Trigger** verbindet ihn mit einem Vorgang. „Vor dem Veröffentlichen
von Inhalten den Freigabe-Workflow ausführen“ ist der klassische Fall
([Workflows, In short](../../../anleitungen/workflows.md#kurz-gesagt)).

Was die Beteiligten sehen:

1. Ein Redakteur veröffentlicht einen Artikel. Er wird nicht veröffentlicht; die Version wartet auf die Freigabe
   („wartend“).
2. Eine freigebende Person öffnet **Mein Konto > Zusammenarbeit**, sieht sich die Vorschau an und drückt
   **Freigeben** oder **Ablehnen** mit einem Kommentar für den Autor.
3. Beim nächsten Lauf des Workflow-Cronjobs wird der freigegebene Artikel veröffentlicht. Eine abgelehnte Version wird
   wieder zu einem Entwurf für den Autor.

Was das Ereignis *Approve* eingrenzen kann
([Workflows, 3.2](../../../anleitungen/workflows.md#32-das-ereignis-freigeben-einrichten)):

| Feld | Geschäftliche Bedeutung |
|---|---|
| Affected sections | nur Inhalte in diesen Bereichen brauchen eine Freigabe (zum Beispiel Personal und Geschäftsleitung) |
| Affected languages | nur manche Übersetzungen brauchen eine Freigabe |
| Affected versions | nur neue Objekte, nur Änderungen oder beides |
| Users / Groups who approve content | die freigebenden Personen |
| Excluded user groups | Personen, die ohne Freigabe veröffentlichen |

Eine freigebende Person braucht nie eine Freigabe für ihre eigenen Inhalte. Freigebende erfahren per Mail von
wartenden Elementen, wenn sie auf ihrer Benachrichtigungsseite die Einstellung zur Zusammenarbeit **Freigabe**
markieren ([Benachrichtigungen: die Anleitung für Benutzer, Collaboration notification](../../../../eng-us/features/6.0/notifications.md#collaboration-notification)).

**Setup > Workflow-Prozesse** (`/workflow/processlist`) listet jeden wartenden Prozess in Worten auf, mit einer
Statusleiste, die sagt, ob der Workflow-Cronjob eingeplant ist und wann er als Nächstes läuft. **Abbrechen...** stoppt
einen Prozess nach einer Bestätigung; es werden keine Inhalte gelöscht
([Workflows, 7](../../../anleitungen/workflows.md#7-prozesse-sicher-abbrechen-und-aufräumen)).

Eine Beispiel-Einrichtung für Freigaben lässt sich mit `./console exp:collaborationsampledata` anlegen und wieder
entfernen, was bei der Schulung von Freigebenden hilft
([Workflows, 3.6](../../../anleitungen/workflows.md#36-die-beispielfreigaben)).

## Prüfstadien mit Objektzuständen

Objektzustände sind Kennzeichen, die jedes Objekt trägt, eines pro Zustandsgruppe. Rollen machen sie wirksam. Das
durchgerechnete Beispiel der Anleitung legt eine Gruppe `review` mit Entwurf, In Prüfung und Freigegeben an und:

- lässt Besucher (oder Mitarbeiter) nur Inhalte im Zustand Freigegeben lesen;
- lässt Autoren ihre eigenen Entwürfe bearbeiten und auf In Prüfung setzen, sonst nichts;
- lässt Prüfende Texte zurück auf Entwurf setzen oder freigeben.

| Wer | Tut | Zustand | Für Leser sichtbar |
|---|---|---|---|
| Autor | legt an und veröffentlicht | Entwurf | nein |
| Autor | setzt In Prüfung | In Prüfung | nein; der Autor kann nicht mehr bearbeiten |
| Prüfende Person | schickt zurück | Entwurf | nein |
| Prüfende Person | gibt frei | Freigegeben | ja |

Setzen Sie die vorhandenen öffentlichen Inhalte **zuerst** auf Freigegeben, sonst verschwinden sie für Leser, wenn die
Gruppe angelegt wird ([Objektzustände, 9](../../../anleitungen/objektzustaende.md#9-ein-durchgearbeitetes-beispiel-ein-prüf-workflow)).

## Eine Staging-Sektion

Der einfachste Weg: eine Sektion `Staging`, die die Rollen der Leser nicht enthalten. Redakteure legen Seiten an ihrem
endgültigen Platz an und weisen sie Staging zu; wenn alles fertig ist, wird das oberste Element der lesbaren Sektion
zugewiesen, und alles darunter wird auf einmal lesbar
([Sektionen, 13.3](../../../anleitungen/sektionen.md#133-inhalte-vorbereiten-bevor-sie-öffentlich-werden)). Es gibt keinen
aufgezeichneten Freigabeschritt; der Weg passt also eher zu Neustarts als zur täglichen Governance.

## Wählen

| Bedarf | Beste Wahl |
|---|---|
| Nichts darf erscheinen, bevor eine freigebende Person es gesehen hat | Freigabe vor dem Veröffentlichen |
| Ein formeller Nachweis von Freigabe und Ablehnung in einem Posteingang | Freigabe vor dem Veröffentlichen |
| Viele Autoren, ein kleines Prüfteam, Hin und Her bei Entwürfen | Prüfstadien |
| Einen ganzen neuen Bereich vorbereiten und an einem Tag freigeben | Staging-Sektion |
| Nur manche Bereiche brauchen eine Freigabe | Freigabe mit Affected sections |
| Freigabe nach Abteilung | Freigabe mit Gruppen von Freigebenden pro Sektion oder Prüfstadien, nach Sektion eingeschränkt |

## Was laufen muss

- **Der Workflow-Cronjob** (`workflow.php`, standardmäßig im Teil `frequent`). Ohne ihn werden freigegebene Inhalte nie
  veröffentlicht ([Workflows, 6](../../../anleitungen/workflows.md#6-der-workflow-cronjob-wie-prozesse-fortgesetzt-werden)).
- **Mail**, damit Freigebende erfahren, dass etwas wartet.
- **Erreichbare Freigebende.** Wenn eine freigebende Person geht, räumen Sie auf, wie unter
  [Workflows, 10.2](../../../anleitungen/workflows.md#102-aufräumen-nach-einem-entfernten-freigebenden) beschrieben.

## Nachweis, wer was freigegeben hat

Der Audit-Trail zeichnet Inhaltsänderungen, einschließlich Sektionen und Zuständen, in einem manipulationssicheren Log
auf ([Der Audit-Trail](../../../../eng-us/features/6.0/audit-trail.md)). Zusammen mit der Versionsseite jedes Objekts
([Versionen eines Objekts](../../../anleitungen/inhaltsverlauf.md)) kann die Organisation zeigen, wer einen Text wann
geändert hat.

## Checkliste

- [ ] Entschieden, welche Bereiche eine Freigabe brauchen und welcher Weg passt.
- [ ] Gruppen von Freigebenden benannt, mit Vertretung für Urlaube.
- [ ] Workflow-Cronjob eingeplant und in der Statusleiste sichtbar.
- [ ] Freigebende haben die Einstellung zur Zusammenarbeit Freigabe markiert.
- [ ] Vor dem Start ein Testartikel freigegeben und einer abgelehnt.
- [ ] Ein Verfahren für den Fall, dass eine freigebende Person geht.

## Verwandte Seiten

- [Workflows](../../../anleitungen/workflows.md)
- [Objektzustände](../../../anleitungen/objektzustaende.md)
- [Entwürfe und wartende Versionen](../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)
- [Registrierung und Kontofreigabe](../extranets/registrierung-und-kontofreigabe.md)
- [Vorschau vor dem Veröffentlichen](../zuverlaessigkeit/vorschau-vor-dem-veroeffentlichen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
