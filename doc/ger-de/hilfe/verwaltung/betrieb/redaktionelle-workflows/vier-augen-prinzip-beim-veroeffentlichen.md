---
title: Vier-Augen-Prinzip beim Veröffentlichen
audience: government, admin, project-manager
category: public-sector
order: 60
summary: Wie Sie sicherstellen, dass keine einzelne Person allein Inhalte auf einer öffentlichen Website veröffentlichen kann - ein Freigabe-Workflow für jede öffentliche Sektion und Version, Freigebende vom Erstellen ferngehalten, eine kleine dokumentierte Umgehungsgruppe und Audit-Belege - und die Lücken, die ein Prozess schließen muss.
keywords: Vier-Augen-Prinzip, doppelte Kontrolle, Freigabe, Abnahme, Selbstfreigabe, Freigabe-Ereignis, ausgeschlossene Benutzergruppen, Funktionstrennung, redaktionelle Kontrolle, Audit, two-person rule, four-eyes principle, dual control, approval, sign-off, self-approval, Approve event, excluded user groups, separation of duties, editorial control, audit
type: help
---
# Vier-Augen-Prinzip beim Veröffentlichen

Diese Seite richtet sich an Führungskräfte und Administratoren, deren Richtlinie vorschreibt, dass jede Veröffentlichung von einer zweiten Person
gesehen wird (das „Vier-Augen-Prinzip“). Sie zeigt, wie nah Exponential dieser Regel von Haus aus kommt, welche Einstellungen zählen und wo ein
schriftlich festgelegter Prozess die Lücke schließen muss. Quellen: [Workflows](../../../../anleitungen/workflows.md) und
[Ohne Benachrichtigung veröffentlichen](../../../../funktionen/6.0/veroeffentlichen-ohne-benachrichtigung.md).

## Inhalt
1. [Warum ein Veröffentlichungsrecht nicht genügt](#warum-ein-veröffentlichungsrecht-nicht-genügt)
2. [Die Konfiguration](#die-konfiguration)
3. [Die eigenen Inhalte der Freigebenden](#die-eigenen-inhalte-der-freigebenden)
4. [Operationen außerhalb des Veröffentlichens](#operationen-außerhalb-des-veröffentlichens)
5. [Belege für einen Prüfer](#belege-für-einen-prüfer)
6. [Verwandte Seiten](#verwandte-seiten)

## Warum ein Veröffentlichungsrecht nicht genügt

Im Bearbeitungsformular entscheidet `content/edit`, ob ein Benutzer einen Entwurf veröffentlichen darf
([Ohne Benachrichtigung veröffentlichen](../../../../funktionen/6.0/veroeffentlichen-ohne-benachrichtigung.md)): Wer bearbeiten darf, kann auf die Schaltfläche
zum Veröffentlichen klicken. Die Kontrolle ist daher der **Freigabe-Workflow** am Trigger **Vor dem Veröffentlichen von Inhalten**, der jede
Version zurückhält, bis eine freigebende Person entscheidet.

## Die Konfiguration

| Einstellung | Wert für das Vier-Augen-Prinzip |
|---|---|
| Trigger | **Vor dem Veröffentlichen von Inhalten** führt den Freigabe-Workflow aus |
| Freigabe-Ereignis: Betroffene Sektionen | jede Sektion, die Besucher lesen können |
| Betroffene Sprachen | *Alle Sprachen* |
| Betroffene Versionen | *Alle Versionen* (eine Änderung ist ebenfalls eine Veröffentlichung) |
| Gruppen, die Content freigeben | Gruppen von Freigebenden, niemals eine einzelne Person |
| Gruppen ausschließen | leer, oder eine Notfallgruppe ([Notfallumgehung beim Veröffentlichen](notfall-veroeffentlichung-ohne-freigabe.md)) |
| Workflow-Cronjob | eingeplant; geprüft unter **Setup > Workflow-Prozesse** |

## Die eigenen Inhalte der Freigebenden

Das Freigabe-Ereignis lässt eine Version durch, wenn der veröffentlichende Benutzer eine seiner Freigebenden ist
([Workflows, Abschnitt 3.2](../../../../anleitungen/workflows.md#32-das-ereignis-freigeben-einrichten)). Für ein strenges Vier-Augen-Prinzip wählen Sie eine
dieser Möglichkeiten:

| Möglichkeit | Wie | Aufwand |
|---|---|---|
| Freigebende erstellen keine Inhalte | die Rolle der Freigebenden hat in den Bereichen, die sie freigeben, kein `content/edit` | Freigebende schicken Korrekturen als Kommentare zurück |
| Gegenseitige Freigabe | die Freigebenden von Bereich A sind nur in Bereich B Autorinnen und Autoren, und umgekehrt; zwei Workflows oder ein Freigabe-Ereignis je Bereich mit **Betroffene Sektionen** | braucht mindestens zwei Bereiche und zwei Gruppen |
| Zweites Freigabe-Ereignis | ein zweites Freigabe-Ereignis im selben Workflow mit einer anderen Gruppe von Freigebenden (zum Beispiel dem Web-Team); eine freigebende Person der ersten Gruppe ist keine der zweiten, also werden ihre Inhalte dort weiterhin angehalten | jede Veröffentlichung braucht zwei Entscheidungen |

Testen Sie die gewählte Möglichkeit mit einem Mitglied jeder Gruppe, weil das Ergebnis von der Gruppenmitgliedschaft abhängt.

## Operationen außerhalb des Veröffentlichens

Die Freigabe deckt das Veröffentlichen ab. Andere Aktionen ändern, was die Öffentlichkeit sieht, ohne zu veröffentlichen:

| Aktion | Recht | Kontrolle |
|---|---|---|
| Entfernen, verschieben | `content/remove`, `content/move` | belassen Sie sie beim Web-Team; entfernte Elemente kommen in den Papierkorb und können wiederhergestellt werden |
| Verbergen | `content/hide` | ebenso |
| Änderung von Sektion oder Zustand | `section/assign`, `state/assign` | einschränken mit `NewSection`, `NewState` |

Diese Aktionen stehen im Kanal `content` des Prüfprotokolls, sodass eine zweite Person sie nachträglich prüfen kann.

## Belege für einen Prüfer

- Die Freigabe selbst: das Element der Zusammenarbeit und der Prozess, solange er läuft; danach zeigt die Versionsgeschichte, wer jede Version
  erstellt hat ([Versionsgeschichte und Rechenschaft](versionsverlauf-und-nachvollziehbarkeit.md)).
- Der Kanal `content` des Prüfprotokolls erfasst Veröffentlichungen, Entfernungen, Verschiebungen, Sektionen und Zustände.
- Die Trigger-Liste und die Seite des Workflows dokumentieren die Konfiguration; bewahren Sie eine datierte Kopie in Ihrem Änderungsnachweis auf.

Exponential erzeugt keinen einzelnen Bericht „freigegeben von“ je Seite; wenn Ihre Prüfer einen brauchen, kombinieren Sie die Versionsgeschichte mit
dem Audit-Export ([Audit-Einträge für eine Prüfung exportieren](../../aufgaben/betrieb/audit-eintraege-fuer-einen-pruefer-exportieren.md)).

## Verwandte Seiten
- [Funktionstrennung](../personen-und-berechtigungen/funktionstrennung.md)
- [Rollenentwurf: Freigebende](../personen-und-berechtigungen/rollenentwurf-freigebende.md)
- [Das Freigabe-Ereignis einrichten](das-freigabe-ereignis-einrichten.md)
- [Schritt der rechtlichen Prüfung](rechtliche-pruefung-als-schritt.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
