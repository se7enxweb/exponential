---
title: Eine Seite zur Freigabe schicken
audience: government, editor
category: public-sector
order: 240
summary: Für Redakteure in Sektionen mit einem Freigabe-Workflow - eine Seite zur Veröffentlichung schicken, sie unter Meine ausstehenden Elemente warten sehen, die Kommentare des Freigebenden lesen und eine abgelehnte Version handhaben.
keywords: Freigabe, zur Freigabe schicken, Zur Veröffentlichung schicken, ausstehend, Meine ausstehenden Elemente, Freigabe-Workflow, Zusammenarbeit, abgelehnt, Kommentare, Workflow-Cronjob, approval, send for approval, Send for publishing, pending, My pending items, approval workflow, collaboration, denied, comments, workflow cronjob
type: help
---
# Eine Seite zur Freigabe schicken

Diese Seite ist für Redakteure, deren Änderungen eine Freigabe brauchen. Auf solchen Sites hält ein Workflow-Ereignis
*Approve* die Version zurück, bis ein Freigebender entscheidet; der Redakteur klickt auf dieselbe Schaltfläche
**Zur Veröffentlichung schicken** ([Workflows](../../../../anleitungen/workflows.md),
[Entwürfe und ausstehende Elemente](../../../../anleitungen/entwuerfe-und-ausstehende-objekte.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Wenn sie abgelehnt wird](#wenn-sie-abgelehnt-wird)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Wissen Sie, ob die Freigabe für Sie und für diesen Teil der Site gilt (sie wird je Sektion, Sprache, Versionsart und
  Benutzergruppe eingestellt). Wenn Sie in einer ausgenommenen Gruppe sind, wird Ihre Änderung sofort veröffentlicht.
- Wissen Sie, wer freigibt und wie lange es meist dauert.

## Schritte

1. Bearbeiten Sie die Seite und klicken Sie auf **Zur Veröffentlichung schicken**. Statt veröffentlicht zu werden, wird
   die Version *ausstehend*.
2. Öffnen Sie **Meine ausstehenden Elemente** (`/content/pendinglist`, auch auf dem Dashboard). Die Karte zeigt den
   Stand der Freigabe, wer sie geschickt hat und was sie zurückhält.
3. **Freigabe öffnen** (oder **Freigabe und Kommentare**) zeigt die Anfrage und etwaige Kommentare.
4. Warten Sie auf die Entscheidung. Nach einer Freigabe wird die Version beim nächsten Lauf des Workflow-Cronjobs
   veröffentlicht.

## Das Ergebnis prüfen

- [ ] Die Version steht unter **Meine ausstehenden Elemente**, bis über sie entschieden ist.
- [ ] Nach der Freigabe und dem nächsten Lauf des Cronjobs ist die Seite veröffentlicht (abgemeldet prüfen).
- [ ] **Versionen verwalten** zeigt sie als veröffentlicht und die vorherige als archiviert.

## Wenn sie abgelehnt wird

1. Der Kommentar des Freigebenden erklärt den Grund; lesen Sie ihn in der Freigabe.
2. Legen Sie aus der abgelehnten Version einen neuen Entwurf an
   ([Eine frühere Version wiederherstellen](eine-fruehere-version-wiederherstellen.md) beschreibt **Neuen Entwurf machen**), ändern
   Sie, was verlangt wurde, und klicken Sie erneut auf **Zur Veröffentlichung schicken**.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Eine sofortige Veröffentlichung erwarten | Freigaben brauchen Zeit, und der Cronjob läuft alle paar Minuten |
| Dieselbe Seite erneut bearbeiten, während sie aussteht | Sie erhalten einen Bearbeitungskonflikt; warten oder abstimmen |
| Eine Version steht seit Tagen ohne Freigebenden aus | bitten Sie das Web-Team, **Setup > Workflow Prozesse** zu prüfen |
| Dringende Inhalte in einer Sektion mit Freigabe | vereinbaren Sie vorab einen Weg für Dringendes |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Eine Seite freigeben oder ablehnen](eine-seite-freigeben-oder-ablehnen.md)
- [Veröffentlichen, Versionen und der Papierkorb](../../../veroeffentlichen-und-versionen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
