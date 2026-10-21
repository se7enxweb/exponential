---
title: Der Posteingang der Zusammenarbeit
audience: government, editor
category: public-sector
order: 40
summary: Wo Freigebende Freigabeanfragen finden (Mein Konto > Zusammenarbeit und Meine ausstehenden Elemente), wie sie mit einem Kommentar freigeben oder ablehnen, was die Autorin oder der Autor sieht und welche Rechte eine freigebende Person überhaupt braucht, um den Posteingang zu öffnen.
keywords: Zusammenarbeit, Posteingang, Freigabeanfrage, freigeben, ablehnen, Kommentar, Freigebende, Meine ausstehenden Elemente, collaboration/view/summary, Abnahme, redaktionelle Prüfung, collaboration, inbox, approval request, approve, deny, comment, approver, My pending items, sign-off, editorial review
type: help
---
# Der Posteingang der Zusammenarbeit

Diese Seite richtet sich an Freigebende und an die Redaktionen, deren Inhalte sie freigeben. Sie erklärt, wo eine Freigabeanfrage erscheint,
wie darüber entschieden wird und was danach geschieht. Sie beruht auf [Workflows](../../../../anleitungen/workflows.md) und
[Meine Entwürfe und meine ausstehenden Elemente](../../../../anleitungen/entwuerfe-und-ausstehende-objekte.md).

## Inhalt
1. [Wo Anfragen erscheinen](#wo-anfragen-erscheinen)
2. [Über eine Anfrage entscheiden](#über-eine-anfrage-entscheiden)
3. [Was die Autorin oder der Autor sieht](#was-die-autorin-oder-der-autor-sieht)
4. [Benötigte Rechte](#benötigte-rechte)
5. [Bewährte Praxis](#bewährte-praxis)
6. [Verwandte Seiten](#verwandte-seiten)

## Wo Anfragen erscheinen

| Seite | Adresse | Zeigt |
|---|---|---|
| **Mein Konto > Zusammenarbeit** | `/collaboration/view/summary` | die Freigabeanfragen, an denen Sie beteiligt sind; „Noch nichts zu bearbeiten“, wenn es keine gibt |
| **Meine ausstehenden Elemente** | `/content/pendinglist` | von Ihnen geschickte Versionen und Versionen, die auf Ihre Freigabe warten, mit einem Link **Freigabe öffnen** |
| **Setup > Workflow-Prozesse** | `/workflow/processlist` | (Administratoren) jeden wartenden Prozess, mit **Freigabeanfrage öffnen** |

Eine Version, die auf Ihre Freigabe wartet, wird unter Meine ausstehenden Elemente nur aufgeführt, wenn Sie sie mit `content/versionread` lesen
dürfen ([Entwürfe und ausstehende Elemente](../../../../anleitungen/entwuerfe-und-ausstehende-objekte.md#2-meine-ausstehenden-elemente)).

## Über eine Anfrage entscheiden

1. Öffnen Sie die Anfrage aus dem Posteingang oder über **Freigabe öffnen** unter Meine ausstehenden Elemente.
2. Lesen Sie die Vorschau der ausstehenden Version. Vergleichen Sie sie mit der veröffentlichten Version, wenn es eine Änderung ist: Die
   Versionsseite des Objekts bietet jedem mit `content/diff` **Vergleichen** an
   ([Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md#2-die-aktionen-einer-version)).
3. Klicken Sie auf **Freigeben**, oder auf **Ablehnen** und schreiben Sie einen Kommentar für die Autorin oder den Autor, was zu ändern ist.
4. Die Entscheidung wird beim nächsten Lauf des Workflow-Cronjobs wirksam: Freigegebene Versionen werden veröffentlicht, abgelehnte Versionen
   gehen als Entwürfe an die Autorin oder den Autor zurück ([Workflows, Abschnitt 3.5](../../../../anleitungen/workflows.md#35-ausprobieren)).

## Was die Autorin oder der Autor sieht

- Nach **Veröffentlichen** wartet die Version; **Meine ausstehenden Elemente** listet sie unter „Von Ihnen geschickt“ mit dem Stand ihrer
  Freigabe und wer freigibt.
- Nach **Ablehnen** ist die Version wieder ein Entwurf unter **Meine Entwürfe**; der Kommentar steht bei der Freigabe.
- Nach **Freigeben** und dem Cronjob ist der Inhalt veröffentlicht und verschwindet aus der Liste der ausstehenden Elemente.

## Benötigte Rechte

Das Modul collaboration definiert keine getrennten Richtlinienfunktionen (`kernel/collaboration/module.php`), daher werden seine Seiten gegen
eine Richtlinie über das ganze Modul geprüft. Eine Redaktionsrolle ohne diese Richtlinie erhält im Posteingang „Die angeforderte Seite konnte
nicht angezeigt werden“. Freigebende brauchen daher eine Rolle mit dem Modul `collaboration`, wie unter
[Rollenentwurf: Freigebende](../personen-und-berechtigungen/rollenentwurf-freigebende.md).

## Bewährte Praxis

- **Entscheiden Sie innerhalb einer vereinbarten Frist**, zum Beispiel zwei Arbeitstage; ein wartender Prozess, der älter als ein Tag ist,
  wird unter Setup > Workflow-Prozesse gelb markiert, was Webverantwortlichen hilft, Freigaben nachzuverfolgen.
- **Formulieren Sie Ablehnungen als Anweisungen** („Ersetzen Sie die Tabelle durch eine Liste; ergänzen Sie die Kontaktadresse“), damit die
  Autorin oder der Autor ohne Besprechung handeln kann.
- **Abwesenheit:** Die Freigabe geht an Gruppen, daher arbeitet eine Gruppe mit zwei oder mehr Freigebenden auch während des Urlaubs weiter.
- **Eine ausgetretene Person unter den Freigebenden**: Bereits gestellte Anfragen behalten ihre Beteiligten; brechen Sie sie ab und senden Sie
  sie erneut, wie unter [Fehlerbehebung bei Workflows](workflow-fehlerbehebung.md) beschrieben.

## Verwandte Seiten
- [Entwürfe und ausstehende Elemente](entwuerfe-und-wartende-elemente.md)
- [Überblick über Freigabe-Workflows](freigabe-workflows-im-ueberblick.md)
- [Einer freigebenden Person Rechte geben](../../../../../eng-us/help/government/tasks/people-and-permissions/give-an-approver-rights.md)
- [Kurs Redaktion 101](../../../../../eng-us/help/trainers/editors-101/README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
