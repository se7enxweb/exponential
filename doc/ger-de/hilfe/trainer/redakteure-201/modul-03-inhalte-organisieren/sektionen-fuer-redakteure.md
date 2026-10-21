---
title: "Lektion 3.3: Sektionen aus Sicht der Redakteure"
audience: trainer, editor
category: training
order: 33
summary: Die Teilnehmenden erklären, was eine Sektion ist, lesen die Sektion eines Elements im Tab Details, setzen sie mit Setzen, wo erlaubt, und sagen die Sektion neuer, verschobener und kopierter Inhalte voraus. 45 Minuten.
keywords: redakteure 201, sektion, sektionen, standard, media, users, tab details, sektion setzen, berechtigungen, besucher, anonym, neue inhalte, verschieben, editors 201, section, sections, details tab, set section, permissions, visitors, anonymous, new content, move
type: help
---
# Lektion 3.3: Sektionen aus Sicht der Redakteure

Sektionen sind Kennzeichnungen, die Inhalte für Berechtigungen, die Administration und Designs gruppieren. Redakteure
legen sie selten an, begegnen ihnen aber jeden Tag: Eine Seite in der falschen Sektion kann für Besucher
verschwinden. Diese Lektion gibt Redakteuren genau das, was sie brauchen: die Sektion eines Elements lesen, setzen und
vorhersagen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Redakteure 201](../README.md) |
| Modul | [Modul 3: Inhalte organisieren](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Erfahrene Redakteure |
| Voraussetzungen | [Lektion 3.1](verschieben-und-kopieren.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, was eine Sektion ist, und die Sektionen einer neuen Installation nennen;
- die Sektion eines Elements im Tab **Details** lesen und eine andere mit **Setzen** festlegen, wenn ihre Rolle es
  erlaubt;
- die Sektion eines neuen Elements, eines verschobenen Teilbaums und einer Kopie vorhersagen;
- erklären, warum Inhalte in einer neuen Sektion vor Besuchern verborgen sein können.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Sektionen](../../../../anleitungen/sektionen.md#kurz-gesagt).

1. **Was eine Sektion ist** (4 Minuten). Eine benannte Gruppe von Inhalten. Jedes Element gehört genau zu einer
   Sektion. Sektionen werden für Berechtigungen verwendet (eine Rolle kann auf Sektionen beschränkt werden), für die
   Administration (welcher obere Reiter aktiv ist) und für Designs (Templates können sich pro Sektion unterscheiden).
2. **Die Sektionen einer neuen Installation** (3 Minuten): **Standard** (der Inhaltsbaum), **Users**, **Media**,
   **Setup** und **Design**. Websites fügen eigene hinzu (zum Beispiel einen Mitgliederbereich).
3. **Besucher** (3 Minuten). Die Rolle Anonymous einer neuen Installation darf nur die Sektion **Standard** lesen.
   Deshalb sind Inhalte in **Media** oder **Users** oder in einer neuen Sektion vor nicht angemeldeten Besuchern
   verborgen, sofern keine Richtlinie sie erlaubt.
4. **Lesen und Setzen** (4 Minuten). Der Tab **Details** zeigt **Sektion** mit einer Liste der Sektionen, die Ihre
   Rolle Sie zuweisen lässt, und **Setzen**. Das Setzen einer Sektion ändert das Element **und jedes Element darunter**,
   dessen Hauptort dort liegt. Dieselbe Auswahl gibt es im Bearbeitungsformular eines Entwurfs.
5. **Vorhersagen** (4 Minuten):
   - ein neues Element übernimmt die Sektion seines übergeordneten Elements;
   - das Verschieben eines Teilbaums gibt den Elementen, die noch die Sektion des alten übergeordneten Elements hatten,
     die Sektion des neuen;
   - eine Kopie übernimmt die Sektion der Stelle, an die sie kopiert wird;
   - das Hinzufügen eines zweiten Ortes ändert nichts: Ein Element hat eine Sektion.

### Häufige Fragen

**Ich habe einen Artikel in den Mitgliederbereich verschoben, und Besucher sehen ihn noch.** Er hat seine eigene
Sektion behalten, wenn sie sich von der des alten übergeordneten Elements unterschied; setzen Sie sie im Tab
**Details**, oder fragen Sie einen Administrator.

**Warum sehe ich die Liste der Sektionen im Tab Details nicht?** Ihre Rolle darf möglicherweise keine Sektionen
zuweisen.

**Wer legt Sektionen an?** Administratoren, unter **Setup** > **Sektionen**.

### Stolperfallen

- **Eine Sektion auf einem großen Ordner setzen.** Das ändert alles darunter; bei großen Teilbäumen läuft es
  möglicherweise als Hintergrundauftrag (Lektion 3.4).
- **Mit Sektionen verbergen.** Die Teilnehmenden verwenden eine Sektion, um Inhalte zu verbergen. Vermitteln Sie dafür
  **Verbergen**; Sektionen sind für Berechtigungen und Design.
- **Sektionen der Schulungs-Website.** Eine Schulungsinstallation kann zusätzliche Sektionen aus Paketen haben; sagen
  Sie den Teilnehmenden, welche sie verwenden sollen.

## Praktische Übung

Verwenden Sie die Schulungsinstallation und Ihren Kursordner. Fragen Sie den Trainer, welche Sektion Sie setzen dürfen
(zum Beispiel eine Sektion *Training*, die der Trainer angelegt hat).

1. Öffnen Sie den Tab **Details** Ihres Kursordners. Notieren Sie seine **Sektion**.
2. Öffnen Sie ein Bild der Medienbibliothek. Notieren Sie seine Sektion.
3. Öffnen Sie die öffentliche Website, ohne sich anzumelden (ein privates Browserfenster), und öffnen Sie die Adresse
   Ihres Kursordners: Er ist sichtbar. Öffnen Sie die Adresse des Bildes auf dieselbe Weise und notieren Sie, was Sie
   sehen.
4. Legen Sie in Ihrem Kursordner einen Artikel `Section test` an. Suchen Sie vor dem Veröffentlichen die Sektion im
   Formular; veröffentlichen Sie dann und lesen Sie seinen Tab **Details**. Sagen Sie zuerst voraus, prüfen Sie dann.
5. Verschieben Sie `Section test` in einen Ordner mit einer anderen Sektion, wenn der Trainer einen vorbereitet hat,
   und lesen Sie seine Sektion erneut.
6. Wählen Sie im Tab **Details** von `Section test` die Sektion, die der Trainer genannt hat, und klicken Sie auf
   **Setzen** (wenn Ihre Rolle es erlaubt).
7. Öffnen Sie `Section test` im privaten Fenster und notieren Sie, ob Besucher es jetzt sehen können.
8. Setzen Sie die ursprüngliche Sektion zurück.

## Erwartetes Ergebnis

- Der Kursordner liegt (meist) in **Standard** und das Bild in **Media**.
- `Section test` hat wie vorhergesagt in der Sektion seines übergeordneten Elements begonnen.
- Nach Schritt 6 konnten Besucher den Artikel je nach den Berechtigungen der Sektion sehen oder nicht; der Trainer
  erklärt das Ergebnis.
- Der Trainer prüft das, indem er jede Person nach ihrer Vorhersage in Schritt 4 und dem Ergebnis fragt.

![Der Tab Details mit der Sektion des Elements und der Schaltfläche Setzen, um sie zu ändern](../../../../../eng-us/help/images/trainers/e201-section-set.webp)

## Quiz

1. Zu wie vielen Sektionen kann ein Element gehören?
2. Nennen Sie drei Sektionen einer neuen Installation.
3. Sie legen einen Artikel in einem Ordner der Sektion *Members* an. Welche Sektion bekommt der Artikel?
4. Warum sind Elemente in einer neuen Sektion oft vor Besuchern verborgen?
   A. Sektionen sind immer verborgen B. Die Rolle Anonymous liest standardmäßig nur Standard C. Eine Sektion verbirgt
   ihre Elemente D. Niemand kann neue Sektionen sehen
5. Sie setzen eine Sektion auf einem Ordner mit zwanzig Artikeln. Was geschieht mit den Artikeln?

<details>
<summary>Antworten</summary>

1. Zu genau einer ([Sektionen](../../../../anleitungen/sektionen.md#kurz-gesagt)).
2. Drei beliebige aus **Standard**, **Users**, **Media**, **Setup**, **Design**.
3. *Members*: Ein neues Element übernimmt die Sektion seines übergeordneten Elements.
4. **B.**
5. Sie bekommen die Sektion ebenfalls (diejenigen, deren Hauptort unter dem Ordner liegt)
   ([Sektionen](../../../../anleitungen/sektionen.md#84-ein-objekt-der-reiter-details-und-das-bearbeitungsformular)).

</details>

## Weiterführende Informationen

- [Sektionen](../../../../anleitungen/sektionen.md): der vollständige Leitfaden, einschließlich Rollen und Designs.
- [Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md): wie Sektionen Berechtigungen einschränken.

## Verwandte Seiten

- [Modul 3: Inhalte organisieren](README.md)
- [Redakteure 201: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
