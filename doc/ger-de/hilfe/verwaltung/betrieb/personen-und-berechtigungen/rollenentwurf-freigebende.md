---
title: "Rollenentwurf: Freigebende"
audience: government, admin, editor
category: public-sector
order: 80
summary: Was eine freigebende Person in einer öffentlichen Stelle in Exponential braucht, um Inhalte zu prüfen und freizugeben, die ein Freigabe-Workflow zurückhält - Mitgliedschaft in der Gruppe der Freigebenden, den Posteingang der Zusammenarbeit, versionread - und wie Sie die Freigabe vom Erstellen getrennt halten.
keywords: Freigebende, Freigabe, Rollenentwurf, Zusammenarbeit, versionread, Freigabe-Ereignis, Gruppe der Freigebenden, prüfende Person, Abnahme, vier Augen, Vier-Augen-Prinzip, redaktionelle Freigabe, approver, approval, role design, collaboration, Approve event, approvers group, reviewer, sign-off, four eyes, two-person rule, editorial approval
type: help
---
# Rollenentwurf: Freigebende

Diese Seite richtet sich an Administratoren, die leitenden Redakteurinnen und Redakteuren, Führungskräften oder rechtlich Prüfenden
das Recht geben, Inhalte vor der Veröffentlichung freizugeben. In Exponential setzt sich eine freigebende Person aus zwei Dingen
zusammen: im Ereignis **Freigeben** eines Workflows genannt zu sein und die Rechte zu haben, die Anfrage zu lesen und darüber zu
entscheiden. Die Fakten stammen aus [Workflows](../../../../anleitungen/workflows.md), [Meine Entwürfe und meine ausstehenden Elemente](../../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)
und den Moduldefinitionen in `kernel/`.

## Inhalt
1. [Wie jemand zur freigebenden Person wird](#wie-jemand-zur-freigebenden-person-wird)
2. [Die Rolle „Freigebende“](#die-rolle-freigebende)
3. [Freigabe und Erstellen getrennt halten](#freigabe-und-erstellen-getrennt-halten)
4. [Einrichten](#einrichten)
5. [Prüfen](#prüfen)
6. [Verwandte Seiten](#verwandte-seiten)

## Wie jemand zur freigebenden Person wird

Das Ereignis **Freigeben** eines Workflows nennt seine Freigebenden in zwei Feldern: **Benutzer, die den Inhalt bewilligen** und
**Gruppen, die Content freigeben** ([Workflows, Abschnitt 3.2](../../../../anleitungen/workflows.md#32-das-ereignis-freigeben-einrichten)). Nennen
Sie eine Gruppe, zum Beispiel „Freigebende - Fachbereich Beispiel“, niemals Einzelpersonen: Eine neue freigebende Person wird dann
hinzugefügt, indem Sie sie in die Gruppe aufnehmen, und wer austritt, gibt nicht mehr frei, sobald er aus ihr entfernt ist.

Wenn eine Redakteurin oder ein Redakteur eine Version zur Veröffentlichung schickt, erzeugt das Ereignis ein Element der
Zusammenarbeit, und die Freigebenden finden es in ihrem Posteingang der Zusammenarbeit (**Mein Konto > Zusammenarbeit**,
`/collaboration/view/summary`) und unter **Meine ausstehenden Elemente** (`/content/pendinglist`), gefiltert als „Wartet auf Ihre
Freigabe“. Sie klicken auf **Freigeben** oder **Ablehnen**, mit einem Kommentar für die Autorin oder den Autor; der Workflow-Cronjob
veröffentlicht die Version dann oder gibt sie zurück
([Workflows, Abschnitt 3.5](../../../../anleitungen/workflows.md#35-ausprobieren)).

## Die Rolle „Freigebende“

| Modul | Funktion | Einschränkungen | Warum |
|---|---|---|---|
| user | login | SiteAccess: der Siteaccess, in dem die Freigebenden arbeiten | anmelden |
| collaboration | (ganzes Modul) | keine | der Posteingang und die Freigabeseite; das Modul collaboration definiert keine getrennten Funktionen, daher werden seine Seiten gegen eine Richtlinie über das ganze Modul geprüft (`kernel/collaboration/module.php`, `eZUser::hasAccessToView()`) |
| content | read | der Bereich, den sie freigeben (Sektion oder Teilbaum) | den Inhalt lesen |
| content | versionread | derselbe Bereich | die ausstehende Version lesen; eine Version, die auf Ihre Freigabe wartet, wird nur mit diesem Recht aufgeführt ([Entwürfe und ausstehende Elemente](../../../../anleitungen/entwuerfe-und-ausstehende-objekte.md#probleme)) |
| content | diff | derselbe Bereich | die ausstehende Version mit der veröffentlichten vergleichen |
| content | dashboard, pendinglist | keine | das Dashboard und **Meine ausstehenden Elemente** |

Eine freigebende Person braucht **kein** `content/edit`, um freizugeben. Vergeben Sie es nur, wenn von den Freigebenden auch erwartet
wird, Texte selbst zu korrigieren, und entscheiden Sie dann, ob ihre eigenen Bearbeitungen die Freigabe einer anderen Person brauchen
(nächster Abschnitt).

## Freigabe und Erstellen getrennt halten

- **Eigene Inhalte einer freigebenden Person werden durchgelassen.** „Wenn der veröffentlichende Benutzer einer der Freigebenden ist,
  lässt das Ereignis die Version durch“ ([Workflows, Abschnitt 3.2](../../../../anleitungen/workflows.md#32-das-ereignis-freigeben-einrichten)). Wenn
  Ihre Regeln besagen, dass niemand seine eigene Arbeit freigibt, dürfen Freigebende in den Bereichen, die sie freigeben, keine Inhalte
  erstellen, oder ein zweiter Workflow-Pfad muss sie abdecken. Siehe [Vier-Augen-Prinzip beim Veröffentlichen](../redaktionelle-workflows/vier-augen-prinzip-beim-veroeffentlichen.md).
- **Ausgeschlossene Benutzergruppen** im Freigabe-Ereignis veröffentlichen ohne Freigabe. Halten Sie diese Liste kurz und dokumentiert;
  so wird eine Notfallumgehung gebaut ([Notfallumgehung beim Veröffentlichen](../redaktionelle-workflows/notfall-veroeffentlichung-ohne-freigabe.md)).
- **Verschiedene Bereiche, verschiedene Freigebende:** Ein Workflow je Bereich ist nicht nötig; die **Betroffenen Sektionen** des
  Freigabe-Ereignisses grenzen ein, welche Inhalte eine Freigabe brauchen. Für Freigebende je Fachbereich verwenden Sie getrennte
  Workflows, die mit einem Ereignis Multiplexer verkettet sind, oder eine Sektion je Fachbereich.

## Einrichten

1. Legen Sie unter Benutzer die Benutzergruppe „Freigebende - <Bereich>“ an.
2. Legen Sie die Rolle „Freigebende“ mit den obigen Richtlinien an (**Benutzer > Rollen und Richtlinien > Neue Rolle**) und weisen Sie sie
   der Gruppe zu, mit **Mit Einschränkungen zuordnen: Teilbaum**, wenn es Freigebende je Fachbereich gibt.
3. Öffnen Sie unter **Setup > Workflows** das Freigabe-Ereignis des Freigabe-Workflows und fügen Sie die Gruppe unter **Gruppen, die
   Content freigeben** hinzu ([Einen Freigabe-Workflow für eine Sektion einrichten](../../aufgaben/personen-und-berechtigungen/einen-freigabe-workflow-fuer-eine-sektion-einrichten.md)).
4. Stellen Sie sicher, dass der Workflow-Cronjob läuft: **Setup > Workflow-Prozesse** zeigt in seiner Statusleiste, ob `workflow.php`
   eingeplant ist.

## Prüfen

- Veröffentlichen Sie als Redakteurin oder Redakteur im betroffenen Bereich: Die Version wartet auf Freigabe.
- Melden Sie sich als freigebende Person an: **Mein Konto > Zusammenarbeit** listet die Anfrage; **Meine ausstehenden Elemente** zeigt
  sie unter „Wartet auf Ihre Freigabe“.
- Nach **Freigeben** und dem nächsten Lauf des Workflow-Cronjobs ist der Inhalt veröffentlicht; der Prozess ist aus
  **Setup > Workflow-Prozesse** verschwunden.

## Verwandte Seiten
- [Einer freigebenden Person Rechte geben](../../aufgaben/personen-und-berechtigungen/einem-freigebenden-rechte-geben.md)
- [Überblick über Freigabe-Workflows](../redaktionelle-workflows/freigabe-workflows-im-ueberblick.md)
- [Posteingang der Zusammenarbeit](../redaktionelle-workflows/posteingang-der-zusammenarbeit.md)
- [Funktionstrennung](funktionstrennung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
