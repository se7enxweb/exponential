---
title: "Lektion 3.2: Benutzer und Abonnements verwalten"
audience: trainer, editor, admin
category: training
order: 32
summary: Die Teilnehmenden finden Newsletter-Benutzer mit den Filtern der Seite Benutzer, lesen die Abonnements eines Benutzers, legen einen Testbenutzer an und nehmen ein wartendes Abonnement an. 45 Minuten.
keywords: newsletter mit cjw newsletter, newsletter-benutzer, abonnementliste, annehmen, newsletterbenutzer anlegen, statusfilter, wartend, bestätigt, angenommen, user_list, newsletters with cjw newsletter, newsletter users, subscription list, approve, create newsletter user, status filter, pending, confirmed, approved
type: help
---
# Lektion 3.2: Benutzer und Abonnements verwalten

Die meiste alltägliche Newsletter-Arbeit besteht darin, Personen nachzuschlagen: Hat sich diese Person angemeldet,
warum erhält sie den Newsletter nicht, können Sie eine Kollegin von Hand hinzufügen. Diese Lektion vermittelt die zwei
Orte dafür: die Seite **Benutzer** über alle Listen hinweg und die Abonnementliste einer einzelnen Liste.

> **Hinweis**
> Jeder in dieser Lektion angelegte Benutzer hat eine Adresse unter `example.invalid`. Jede Mail, die ein Schritt
> schreibt, landet in einer Datei in `var/log/mail`, weil alle Transporte der Schulungsinstallation auf `file`
> gesetzt sind (Lektion 1.2).

## Auf einen Blick

| | |
|---|---|
| Kurs | [Newsletter mit CJW Newsletter](../README.md) |
| Modul | [Modul 3: Abonnenten](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Redakteure und Administratoren, die sich um Abonnenten kümmern |
| Voraussetzungen | [Lektion 3.1](das-anmeldeformular-und-double-opt-in.md): Anna bestätigt und Ben wartend auf der Liste der Person |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Seite **Benutzer** nach Adresse, Status und Liste durchsuchen;
- einen Newsletter-Benutzer mit **Details** öffnen und seine Abonnements und deren Status lesen;
- mit **Neuen Newsletterbenutzer anlegen** einen Newsletter-Benutzer von Hand anlegen;
- in der Abonnementliste einer Liste ein wartendes Abonnement annehmen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Wo Abonnenten verwaltet werden](../../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md#where-subscribers-are-managed)
und die [Referenz der Ansichten](../../../../spezifikationen/6.0/cjw_newsletter.md#modul-newsletter).

1. **Benutzer und Abonnements** (3 Minuten). Ein *Newsletter-Benutzer* ist eine Adresse. Jedes seiner *Abonnements*
   verbindet ihn mit einer Liste, mit eigenem Status. Eine Person, mehrere Listen, mehrere Status.
2. **Die Seite Benutzer** (5 Minuten). **Benutzer** im linken Menü (`newsletter/user_list`). Filter: **E-Mail**,
   **Status** (*Alle*, *Bestätigt*, *Wartend*, *Entfernt*, *Rückläufer*, *Blacklisted*) und **Liste**, dann **Nach
   existierenden Benutzer suchen**. Spalten **UID**, **E-Mail**, **Name**, **Listen**, **Status**, **Rückläufer**,
   **Erstellt**. Jede Zeile hat **Details** und **Bearbeiten**. Die Seite sortiert, filtert und blättert; 10, 25, 50
   oder 100 pro Seite.
3. **Die Abonnements einer Liste** (4 Minuten). Auf der Seite einer Liste führen **Abonnements** und die Status-Links
   zu `newsletter/subscription_list/<node>`. Zu den Spalten gehören **Exponential-Benutzer**, **Format** und
   **Status**; jede Zeile hat **Details**, **Freigeben** und **Bearbeiten**.
4. **Von Hand hinzufügen** (3 Minuten). **Neuen Newsletterbenutzer anlegen** auf der Seite Benutzer oder **Neue
   Anmeldung anlegen** auf der Seite der Liste. Wer jemanden von Hand hinzufügt, braucht genau wie beim Formular
   dessen Einwilligung; notieren Sie, woher sie stammt.

### Häufige Fragen

**Was ist der Unterschied zwischen bestätigt und angenommen?** Bestätigt heißt, die Person hat auf den Link geklickt;
angenommen heißt, die Liste (oder ein Administrator) hat das Abonnement akzeptiert. Beide erhalten Ausgaben.

**Warum zeigt ein Benutzer „Exponential-Benutzer“?** Ein Newsletter-Benutzer kann mit einem Benutzerkonto der Website
verknüpft sein, zum Beispiel wenn sich ein registrierter Benutzer anmeldet.

**Kann ich einen Benutzer löschen?** Das Entfernen eines Newsletter-Benutzers entfernt auch alle seine Abonnements. In
den meisten Fällen sollte sich die Person eher abmelden oder auf die Sperrliste kommen, damit Sie einen Nachweis
behalten (Lektion 3.4).

**Das Dashboard meldet verwaiste Abonnements.** Sie gehören zu Newsletter-Benutzern, die es nicht mehr gibt;
**Entfernen** auf dem Dashboard oder `ext:cjw_newsletter:repair` räumt sie auf.

### Stolperfallen

- **Benutzer anderer Teilnehmender durchsuchen.** Die Seite Benutzer zeigt jede Liste. Filtern Sie zuerst nach Ihrer
  Liste.
- **Die falsche Zeile annehmen.** Die Abonnementliste hat viele Zeilen; prüfen Sie die E-Mail-Adresse vor
  **Freigeben**.
- **Die E-Mail-Adresse bearbeiten.** Wer eine Adresse von Hand ändert, umgeht das Double-Opt-in für die neue Adresse.
  Besser ist ein neues Abonnement über das Formular.

## Praktische Übung

Verwenden Sie die Schulungsinstallation. Ersetzen Sie `<name>` durch Ihren Vornamen.

1. Öffnen Sie **Newsletter** und klicken Sie im linken Menü auf **Benutzer**.
2. Wählen Sie in **Liste** `Weekly tips <name>` und klicken Sie auf **Nach existierenden Benutzer suchen**. Anna und
   Ben werden aufgeführt.
3. Setzen Sie **Status** auf *Wartend* und suchen Sie erneut. Nur Ben bleibt übrig.
4. Klicken Sie in Bens Zeile auf **Details** und lesen Sie seine Abonnements: Liste, Status, Format.
5. Klicken Sie auf **Neuen Newsletterbenutzer anlegen**. **E-Mail**: `carla-<name>@example.invalid`, **Vorname**
   `Carla`, **Nachname** `Test`. Melden Sie sie im Formular für Ihre Liste an (der Trainer zeigt, wo) und speichern
   Sie dann.
6. Suchen Sie Carla auf der Seite Benutzer mit dem Filter **E-Mail**.
7. Öffnen Sie die Seite Ihrer Liste und klicken Sie in ihren Abonnement-Links auf **Wartend**.
8. Suchen Sie Bens Zeile und klicken Sie auf **Freigeben**.
9. Klicken Sie nacheinander auf **Angenommen** und **Bestätigt** und notieren Sie, wer jeweils aufgeführt ist.
10. Schreiben Sie in einem Satz auf, warum Ben jetzt Ausgaben erhält, obwohl er nie auf den Bestätigungslink
    geklickt hat.

## Erwartetes Ergebnis

- Die Liste der Person hat Anna (bestätigt), Ben (von einem Administrator angenommen) und Carla.
- Die Seite Benutzer findet jede dieser Personen nach E-Mail-Adresse und nach Liste.
- Jede Mail, die diese Schritte erzeugt haben, ist eine Datei in `var/log/mail` (prüfen mit
  `ls -lt var/log/mail | head -3`); nichts hat den Rechner verlassen.
- Der Trainer prüft die Abonnementzahlen der Liste am Beamer.

## Quiz

1. Wo suchen Sie über alle Listen hinweg nach einer Adresse?
2. Nach welchen Status können Sie auf der Seite **Benutzer** filtern?
3. Was bewirkt **Freigeben** in einer Abonnementliste?
   A. sendet eine Bestätigungsmail B. lässt das Abonnement Ausgaben erhalten C. setzt die Adresse auf die Sperrliste
   D. löscht den Benutzer
4. Was wird außerdem entfernt, wenn ein Newsletter-Benutzer entfernt wird?
5. Warum sollte auch eine von Hand hinzugefügte Person dem Empfang des Newsletters zugestimmt haben?

<details>
<summary>Antworten</summary>

1. Auf der Seite **Benutzer** (`newsletter/user_list`) ([Spezifikation](../../../../spezifikationen/6.0/cjw_newsletter.md#modul-newsletter)).
2. Bestätigt, wartend, entfernt, Bounce und Sperrliste, oder alle ([Spezifikation](../../../../spezifikationen/6.0/cjw_newsletter.md#modul-newsletter)).
3. **B.** Angenommene Abonnements erhalten Ausgaben ([Status](../../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md#statuses)).
4. Alle seine Abonnements ([Spezifikation](../../../../spezifikationen/6.0/cjw_newsletter.md#modul-newsletter)).
5. Die Einwilligung ist nötig, egal wie die Adresse auf die Liste gelangt ([Einwilligung](../../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md#consent)).

</details>

## Weiterführende Informationen

- [Newsletter-Listen und Abonnements](../../../../../eng-us/help/creators/website-creators/newsletter-lists-and-subscriptions.md#where-subscribers-are-managed):
  wo Abonnenten verwaltet werden.
- [Spezifikation: die Erweiterung cjw_newsletter](../../../../spezifikationen/6.0/cjw_newsletter.md#modul-newsletter):
  jede Ansicht und ihre Parameter.
- [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md): die Richtlinienfunktionen `user_list`,
  `user_edit` und `subscription_list`.

## Verwandte Seiten

- [Modul 3: Abonnenten](README.md)
- [Newsletter mit CJW Newsletter: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
