---
title: "Lektion 2.7: Ein gemeinsamer Schulungsserver oder eine Installation pro Teilnehmer"
audience: trainer, admin
category: training
order: 27
summary: Entscheiden Sie zwischen einer Installation pro Teilnehmer und einer gemeinsamen Schulungs-Website und richten Sie eine gemeinsame Website so ein, dass sich die Teilnehmenden nicht gegenseitig stören, mit einem Ordner pro Teilnehmer und einer Rolle, die mit einer Teilbaum-Einschränkung zugewiesen ist. 45 Minuten.
keywords: gemeinsamer schulungsserver, aufbau der übungsumgebung, teilbaum-einschränkung, mit einschränkungen zuordnen, rolle, ordner pro teilnehmer, isolation, kursraum, shared training server, lab design, subtree limitation, assign with limitation, role, folder per learner, classroom
type: help
---
# Lektion 2.7: Ein gemeinsamer Schulungsserver oder eine Installation pro Teilnehmer

Nicht in jedem Kurs lässt sich Software installieren: Manche Laptops sind gesperrt, manche Kurse finden online nur mit
einem Browser statt. Dann ist eine gemeinsame Schulungs-Website die Antwort, und ihr Risiko ist, dass die
Teilnehmenden die Arbeit der anderen verändern. Diese Lektion wägt die beiden Aufbauten ab und baut den gemeinsamen
mit der Zugriffskontrolle auf, die Exponential schon hat.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Train the Trainer](../README.md) |
| Modul | [Modul 2: Die Übungsumgebung aufbauen](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Trainer, die Kurse nur mit Browser oder online durchführen |
| Voraussetzungen | [Lektion 2.5](konten-und-rollen-der-teilnehmenden.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Vorteile und Grenzen jedes Aufbaus der Übungsumgebung aufzählen;
- auf einer gemeinsamen Website einen Ordner pro Teilnehmer anlegen;
- eine Rolle für die Teilnehmenden anlegen und sie mit einer Teilbaum-Einschränkung zuweisen;
- prüfen, dass ein Teilnehmer den Ordner eines anderen nicht bearbeiten kann.

## Hinweise für Trainer

### So präsentieren Sie es

1. Vergleichen Sie die Aufbauten (5 Minuten):

   | | Eine Installation pro Teilnehmer | Eine gemeinsame Website |
   |---|---|---|
   | Teilnehmende können Administratoren sein | ja | nein (eine Website, eine Konfiguration) |
   | Braucht Software auf jedem Rechner | ja | nein, einen Browser |
   | Teilnehmende können sich gegenseitig stören | nein | nur, wo Rollen es erlauben |
   | Gut für | Kurse für Entwickler und Administratoren | Kurse für Redakteure, Online-Kurse |

2. Erklären Sie den Zugriffsaufbau für eine gemeinsame Website (5 Minuten): ein Ordner pro Teilnehmer; eine Rolle,
   die das Bearbeiten erlaubt; **Mit Einschränkungen zuordnen**, *Teilbaum*, auf den Ordner jeder Teilnehmerin und
   jedes Teilnehmers.
3. Zeigen Sie die Seite einer Rolle mit einer auf einen Teilbaum beschränkten Zuweisung: Die Seite der Rolle sagt
   *Only in the subtree ...* (5 Minuten).

### Häufige Fragen

**Kann ich die Rolle Editor kopieren, statt eine aufzubauen?** Ja: **Kopieren** in der Liste der Rollen erstellt
„Kopie von Editor“ mit denselben Richtlinien und ohne Zuweisungen. Benennen Sie sie um, zum Beispiel in
„Course learners“.

**Sieht ein eingeschränkter Teilnehmer trotzdem den ganzen Baum?** Er kann lesen, was seine Leserichtlinien erlauben.
Die Einschränkung legt fest, wo die Rolle gilt, sodass Bearbeiten und Erstellen auf seinen Ordner beschränkt sind.

### Stolperfallen

- **Die Rolle ohne Einschränkung zuweisen.** Dann darf der Teilnehmer überall bearbeiten, wo die Rolle es erlaubt.
  Wählen Sie immer **Mit Einschränkungen zuordnen**.
- **Teilnehmende zusätzlich in der Gruppe Editors.** Gilt die Rolle dieser Gruppe für die ganze Website, hilft die
  Einschränkung nicht. Legen Sie die Teilnehmenden in eine eigene Gruppe.
- **Echte Inhalte auf der Schulungs-Website.** Eine gemeinsame Schulungs-Website enthält nur erfundene Inhalte.

## Praktische Übung

Auf einer Schulungsinstallation, als Administrator:

1. Legen Sie unter **Benutzer** eine Benutzergruppe **Course learners** an (in der Gruppe **Users**:
   **Neu erstellen**, **User group**, benennen, **Zur Veröffentlichung schicken**).
2. Verschieben Sie `learner01` aus Lektion 2.5 hinein oder legen Sie dort einen neuen Benutzer `learner03` mit einer
   Adresse unter `example.invalid` an.
3. Legen Sie in **Inhaltsstruktur** einen Ordner **Training** und darin einen Ordner **Learner 03** an.
4. Öffnen Sie **Benutzer > Rollen und Richtlinien**, wählen Sie die Rolle **Editor** aus und klicken Sie auf
   **Kopieren**, dann auf **Kopie erstellen**. Benennen Sie die Kopie im Editor in `Course learners` um und klicken Sie
   auf **Speichern**.
5. Klicken Sie auf der Seite der neuen Rolle auf **Mit Einschränkungen zuordnen**, wählen Sie *Teilbaum*, wählen Sie
   den Ordner **Learner 03** und dann den Benutzer `learner03`.
6. Melden Sie sich als `learner03` an. Versuchen Sie, einen Artikel außerhalb von **Learner 03** zu bearbeiten, und
   erstellen Sie dann einen Artikel darin.

## Erwartetes Ergebnis

- Die Seite der Rolle **Course learners** listet `learner03` mit der Einschränkung *Only in the subtree* des Ordners
  der Teilnehmerin oder des Teilnehmers auf.
- Als `learner03` angemeldet, wird das Bearbeiten innerhalb von **Learner 03** angeboten und anderswo verweigert oder
  nicht angeboten.
- Der Trainer dokumentiert den Aufbau (Gruppe, Ordner, Rolle, Einschränkung) auf dem Datenblatt der Übungsumgebung.

## Quiz

1. Nennen Sie einen Vorteil einer Installation pro Teilnehmer und einen einer gemeinsamen Website.
2. Welche Schaltfläche wendet eine Rolle nur in einem Teilbaum an?
3. Was erstellt **Kopieren** bei einer Rolle?
4. Warum sollten Teilnehmende auf einer gemeinsamen Website in einer eigenen Gruppe sein?

<details>
<summary>Antworten</summary>

1. Pro Teilnehmer: Sie können Administratoren sein und sich nicht gegenseitig stören. Gemeinsam: Nur ein Browser wird
   gebraucht.
2. **Mit Einschränkungen zuordnen**, dann *Teilbaum* ([Rollen und Richtlinien, 5](../../../../anleitungen/rollen-und-richtlinien.md#5-die-rolle-zuweisen)).
3. „Kopie von <Name>“ mit denselben Richtlinien und ohne Zuweisungen.
4. Damit ihnen keine andere Rolle, die einer gemeinsamen Gruppe zugewiesen ist, das Bearbeiten auf der ganzen Website
   erlaubt.

</details>

## Weiterführende Literatur

- [Rollen, Richtlinien und nicht aktivierte Benutzer](../../../../anleitungen/rollen-und-richtlinien.md)
- [Sektionen](../../../../anleitungen/sektionen.md): der andere Weg, den Zugriff zu beschränken, nach Sektion.
- [Ablauf eines praktischen Workshops](../../../fuersprecher/ablaufplan-praxis-workshop.md): warum alle Teilnehmenden nach
  Möglichkeit ihre eigene Kopie haben sollten.

## Verwandte Seiten

- [Modul 2: Die Übungsumgebung aufbauen](README.md)
- [Train the Trainer: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
