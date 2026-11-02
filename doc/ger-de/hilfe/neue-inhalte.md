---
title: "Neue Inhalte"
audience: editor, admin
category: content
order: 40
summary: Dashboard > Neue Inhalte - die neuesten Inhalte der ganzen Site, die neuesten zuerst, mit dem, was zur Prüfung zurückgehalten ist, Entwürfen, die auf ein Datum warten, und wer was von wo eingereicht hat; Filter, Seitengrößen bis 1000 und Aktionen auf einer Auswahl (freigeben, zurückhalten, verbergen, anzeigen, Papierkorb, löschen, Sektion, Status).
keywords: Neue Inhalte, Moderation, moderieren, zur Prüfung zurückgehalten, Zurückhaltung, automatisch zurückhalten, Kommentare, Einsendungen, Spam, freigeben, verbergen, anzeigen, Papierkorb, löschen, Sammelaktion, Auswahl, IP-Adresse, Browser, Dashboard, recent content, moderation, held for review, review hold, auto-hold, moderation/read, moderation/act, moderation/personaldata
type: help
---
# Neue Inhalte

**Dashboard > Neue Inhalte** (der zweite Link des Dashboard-Menüs, nach *Dashboard*) zeigt die neuesten Inhalte der ganzen Site, die neuesten
zuerst: was Besucher eingereicht haben, was **zur Prüfung zurückgehalten** ist, Entwürfe, die nie veröffentlicht wurden,
und Versionen, die auf eine Freigabe oder ein Datum warten. Moderatoren geben dort eine ganze Auswahl auf einmal frei
oder entfernen sie.

## Was die Liste zeigt

- **Geändert** und **Erstellt**: die letzte Änderung und die erste Veröffentlichung (*noch nicht* bei einem Entwurf).
- **Name** (öffnet das Objekt) und **Klasse**.
- **Status**: *Veröffentlicht*, *Entwurf* oder *Im Papierkorb*; *Ausstehend*, wenn eine Version auf eine Freigabe oder
  ein Datum wartet; *Verborgen*; *Zur Prüfung zurückgehalten* oder *Zurückgehalten: neue Einsendung*, wenn es
  automatisch zurückgehalten wurde, mit dem Grund.
- **Veröffentlichung**: die veröffentlichte Version, eine Version in Arbeit oder ausstehend, und *Geplant für* ihr Datum.
- **Sektion** und **Ersteller** (öffnet den Benutzer).
- **Eingereicht von**: Adresse, Browser und Siteaccess, nur für Benutzer, die personenbezogene Daten sehen dürfen. Es
  sind die Werte, die das Audit aufgezeichnet hat, gekürzt nach seinen Datenschutzeinstellungen; *nicht aufgezeichnet*,
  wenn es nichts aufgezeichnet hat.

## Objekte finden

Mit den Filtern über der Liste: ein Zeitraum für das Änderungs- oder das Erstellungsdatum, die Klasse, der Status,
zurückgehalten oder nicht, verborgen oder sichtbar, die Sektion, der Siteaccess der Einsendung und der Ersteller (ein
Login oder eine Benutzer-ID), dann **Filtern**. **Filter zurücksetzen** zeigt wieder alles. Ein Klick auf einen
Spaltenkopf sortiert danach, ein zweiter Klick in der anderen Richtung. **Pro Seite** unter der Liste zeigt 25, 100,
300, 700 oder 1000 Objekte; Ihre Wahl wird gespeichert. Jede gefilterte Seite hat ihre eigene Adresse und lässt sich als
Lesezeichen ablegen.

## Aktionen auf einer Auswahl

Haken Sie die Objekte an (oder **Alle auf dieser Seite auswählen**), wählen Sie die Aktion und klicken Sie auf
**Auf die ausgewählten anwenden**:

- **Zurückhaltung aufheben**: die Objekte dürfen wieder bearbeitet und veröffentlicht werden, und was beim
  automatischen Zurückhalten verborgen wurde, erscheint wieder auf der Site.
- **Zur Prüfung zurückhalten**: mit Grund; niemand bearbeitet oder veröffentlicht die Objekte, bis ein Prüfer sie
  freigibt.
- **Verbergen** und **Anzeigen** auf der Site.
- **In den Papierkorb** (wiederherstellbar) und **Löschen** (endgültig): Sie bestätigen zuerst und werden gebeten zu
  bestätigen, dass Sie es sind. Objekte mit Unterobjekten bleiben unberührt; entfernen Sie sie in der Inhaltsstruktur.
- **Sektion zuweisen** und **Status zuweisen**.

Jedes Objekt wird gegen Ihre eigenen Rechte geprüft. Das Ergebnis führt jedes Objekt als erledigt, abgelehnt (mit dem
Grund, etwa „Sie dürfen es nicht verbergen" oder „Es ist bereits zurückgehalten") oder fehlgeschlagen auf. Mehr als
100 Objekte laufen im Hintergrund: eine Fortschrittsseite öffnet sich, Sie dürfen sie verlassen, und das Ergebnis je
Objekt erscheint, wenn es fertig ist.

## Zurückgehaltene Einsendungen

Hält die Site neue Einsendungen automatisch zurück (zum Beispiel Kommentare), wird das neue Objekt eines Besuchers
gleich nach dem Absenden verborgen und zurückgehalten: Besucher sehen es nicht, bis ein Moderator es hier oder in der
Website-Seitenleiste freigibt. Filtern Sie auf **Zur Prüfung zurückgehalten: Zurückgehalten**, um zu sehen, was wartet.

## Der Dashboard-Block

Der Block **Neue Inhalte** im Dashboard zeigt, wie viele Objekte zur Prüfung zurückgehalten sind und wie viele heute
erstellt wurden; jede Zahl öffnet die entsprechend gefilterte Liste.

## Wer was darf

- *moderation/read*: die Liste und den Dashboard-Block sehen.
- *moderation/act*: die Aktionen (Ihre übrigen Rechte entscheiden weiter je Objekt).
- *moderation/personaldata*: die Spalte **Eingereicht von**.

Redakteure und Administratoren haben die ersten beiden in einer neuen Installation. Einzelheiten für Administratoren
(englisch): [Recent content](../../eng-us/features/6.0/moderation.md) und
[das automatische Zurückhalten](../../eng-us/features/6.0/review-hold.md#auto-hold).
