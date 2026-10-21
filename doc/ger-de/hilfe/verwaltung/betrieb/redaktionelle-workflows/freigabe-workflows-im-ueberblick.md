---
title: Überblick über Freigabe-Workflows
audience: government, editor, admin, project-manager
category: public-sector
order: 20
summary: Wie die Freigabe vor dem Veröffentlichen in Exponential funktioniert - Workflows, das Freigabe-Ereignis, Trigger, der Posteingang der Zusammenarbeit, ausstehende Versionen und der Workflow-Cronjob - erklärt für öffentliche Stellen, die eine dokumentierte Abnahme brauchen, bevor Inhalte live gehen.
keywords: Freigabe, Freigabe-Workflow, Abnahme, Veröffentlichungsfreigabe, Workflow, Freigabe-Ereignis, Trigger, Vor dem Veröffentlichen von Inhalten, Zusammenarbeit, ausstehend, Workflow-Cronjob, redaktioneller Prozess, Steuerung, approval, approval workflow, sign-off, publishing approval, Approve event, trigger, Before publishing content, collaboration, pending, workflow cronjob, editorial process, governance
type: help
---
# Überblick über Freigabe-Workflows

Diese Seite richtet sich an Webverantwortliche, Chefredaktionen und Administratoren, die sicherstellen müssen, dass Inhalte abgenommen werden,
bevor sie die Öffentlichkeit erreichen. In Exponential ist die Abnahme ein **Workflow** mit einem Ereignis **Freigeben**, das durch einen
**Trigger** mit dem Veröffentlichungsvorgang verbunden ist. Die Fakten stammen aus [Workflows](../../../../anleitungen/workflows.md); diese Seite
erklärt die beweglichen Teile und die Entscheidungen, die eine öffentliche Stelle trifft.

## Inhalt
1. [Die beweglichen Teile](#die-beweglichen-teile)
2. [Was mit einem Artikel geschieht](#was-mit-einem-artikel-geschieht)
3. [Zu treffende Entscheidungen](#zu-treffende-entscheidungen)
4. [Voraussetzungen, die oft vergessen werden](#voraussetzungen-die-oft-vergessen-werden)
5. [Was Freigaben nicht abdecken](#was-freigaben-nicht-abdecken)
6. [Verwandte Seiten](#verwandte-seiten)

## Die beweglichen Teile

| Teil | Was es ist | Wo |
|---|---|---|
| Workflow | eine geordnete Liste von Ereignissen in einer Workflow-Gruppe | **Setup > Workflows** (`/workflow/grouplist`) |
| Freigabe-Ereignis | ein Schritt, der eine Version zurückhält, bis eine freigebende Person entscheidet | die Bearbeitungsseite des Workflows |
| Trigger | verbindet einen Workflow mit einer Operation, davor oder danach | **Setup > Trigger** (`/trigger/list`), Zeile **Vor dem Veröffentlichen von Inhalten** |
| Element der Zusammenarbeit | die Freigabeanfrage, über die Freigebende entscheiden | **Mein Konto > Zusammenarbeit** (`/collaboration/view/summary`) |
| Ausstehende Version | die Version, die auf die Entscheidung wartet | **Meine ausstehenden Elemente** (`/content/pendinglist`) |
| Workflow-Prozess | eine laufende Freigabe für eine Version | **Setup > Workflow-Prozesse** (`/workflow/processlist`) |
| Workflow-Cronjob | `workflow.php`, standardmäßig im Teil *frequent*; veröffentlicht freigegebene Versionen | **Setup > Cronjobs** |

## Was mit einem Artikel geschieht

1. Eine Redakteurin oder ein Redakteur klickt auf **Zur Veröffentlichung schicken**. Die Version wird *ausstehend*.
2. Der Trigger **Vor dem Veröffentlichen von Inhalten** startet den Freigabe-Workflow; das Freigabe-Ereignis erzeugt ein Element der
   Zusammenarbeit für die Freigebenden, und der Prozess wartet.
3. Eine freigebende Person öffnet die Anfrage, liest die Vorschau und klickt auf **Freigeben** oder **Ablehnen** (mit einem Kommentar).
4. Beim nächsten Lauf des Workflow-Cronjobs: Freigegebene Versionen werden veröffentlicht; abgelehnte Versionen gehen als Entwürfe an die
   Autorin oder den Autor zurück ([Workflows, Abschnitt 1](../../../../anleitungen/workflows.md#1-workflows-ereignisse-trigger-und-prozesse)).

Die Redaktion verfolgt das unter **Meine ausstehenden Elemente**, das angibt, was die Version zurückhält, und auf die Freigabe verlinkt.

## Zu treffende Entscheidungen

| Frage | Wo sie eingestellt wird | Typische Antwort im öffentlichen Sektor |
|---|---|---|
| Welche Inhalte brauchen eine Freigabe? | Freigabe-Ereignis: **Betroffene Sektionen**, **Betroffene Sprachen**, **Betroffene Versionen** | die öffentlichen Sektionen, alle Sprachen, alle Versionen |
| Wer gibt frei? | **Gruppen, die Content freigeben** (Gruppen sind namentlich genannten Benutzern vorzuziehen) | eine Gruppe von Freigebenden je Bereich |
| Wer darf ohne Freigabe veröffentlichen? | **Gruppen ausschließen** | eine kleine, dokumentierte Gruppe; siehe [Notfallumgehung beim Veröffentlichen](notfall-veroeffentlichung-ohne-freigabe.md) |
| Nur neue Seiten oder jede Änderung? | **Betroffene Versionen**: alle, neue Objekte, Aktualisierungen | alle Versionen, wenn auch eine Tippfehlerkorrektur geprüft werden muss; sonst nur neue Objekte |
| Eine freigebende Person oder zwei? | ein Freigabe-Ereignis oder zwei nacheinander | siehe [Vier-Augen-Prinzip](vier-augen-prinzip-beim-veroeffentlichen.md) |
| Getrennte rechtliche oder sprachliche Prüfung? | ein zusätzliches Freigabe-Ereignis, eine zweite Gruppe oder ein Objektzustand | siehe [Schritt der rechtlichen Prüfung](rechtliche-pruefung-als-schritt.md), [Mehrsprachige Freigaben](mehrsprachige-freigaben.md) |

Das Ereignis Feld für Feld einrichten: [Das Freigabe-Ereignis einrichten](das-freigabe-ereignis-einrichten.md).

## Voraussetzungen, die oft vergessen werden

- **Der Workflow-Cronjob muss laufen.** Ohne ihn werden freigegebene Inhalte nie veröffentlicht. Die Statusleiste von **Setup >
  Workflow-Prozesse** zeigt, ob `workflow.php` eingeplant ist
  ([Workflows, Abschnitt 3.4](../../../../anleitungen/workflows.md#34-sicherstellen-dass-der-workflow-cronjob-läuft)).
- **Freigebende brauchen die Rechte zum Entscheiden**: das Modul collaboration und `content/versionread`
  ([Rollenentwurf: Freigebende](../personen-und-berechtigungen/rollenentwurf-freigebende.md)).
- **Ein Workflow je Trigger.** Um beim Veröffentlichen mehrere Workflows auszuführen, verwenden Sie ein Ereignis *Multiplexer* im ausgelösten
  Workflow.
- **Testen Sie mit Beispieldaten**: `./console exp:collaborationsampledata --dry-run --allow-root-user` zeigt, was angelegt würde;
  `--remove` entfernt es wieder ([Workflows, Abschnitt 3.6](../../../../anleitungen/workflows.md#36-die-beispielfreigaben)).

## Was Freigaben nicht abdecken

- **Entfernen, Verschieben und Verbergen** von Inhalten sind andere Operationen; die Trigger-Liste zeigt, welche Operationen einen Workflow
  starten können. Schränken Sie diese Rechte stattdessen über Rollen ein.
- **Die eigenen Inhalte der freigebenden Person** lässt das Freigabe-Ereignis durch.
- **Ausgeschlossene Benutzergruppen** veröffentlichen direkt.
- Eine Freigabe prüft eine Version, nicht spätere Änderungen: Die nächste Version braucht ihre eigene Freigabe (mit **Betroffene Versionen**
  auf alle gesetzt).

## Verwandte Seiten
- [Das Freigabe-Ereignis einrichten](das-freigabe-ereignis-einrichten.md)
- [Posteingang der Zusammenarbeit](posteingang-der-zusammenarbeit.md)
- [Einen Freigabe-Workflow für eine Sektion einrichten](../../aufgaben/personen-und-berechtigungen/einen-freigabe-workflow-fuer-eine-sektion-einrichten.md)
- [Leitfaden zu Workflows](../../../../anleitungen/workflows.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
