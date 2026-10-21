---
title: "Lektion 3.4: Übungen schreiben, die funktionieren"
audience: trainer
category: training
order: 34
summary: Schreiben Sie praktische Übungen, die alle Teilnehmenden allein abschließen können: echte Menüpfade und Schaltflächennamen, eine Aktion pro Schritt, erfundene Daten, ein sichtbares erwartetes Ergebnis und eine Probe mit derselben Version. 45 Minuten.
keywords: übungen, praktisch, anleitungen für die übungsumgebung, schritte, erwartetes ergebnis, probe, schaltflächennamen, erfundene daten, schulungsmaterial, exercises, hands-on, lab instructions, steps, expected result, rehearsal, button names, invented data, training material
type: help
---
# Lektion 3.4: Übungen schreiben, die funktionieren

Eine Übung funktioniert, wenn die Teilnehmenden sie erledigen können, ohne den Trainer zu fragen. Dafür braucht es
genaue Schritte, die echten Namen von Schaltflächen und Menüs, Daten, die sich gefahrlos verwenden lassen, und ein
Ergebnis, das die Teilnehmenden sehen können. Diese Lektion vermittelt die Regeln, denen die Übungen in diesen Kursen
folgen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Train the Trainer](../README.md) |
| Modul | [Modul 3: Einen Kurs gestalten](README.md) |
| Dauer | 45 Minuten (10 Präsentation, 35 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Trainer, die Übungen schreiben |
| Voraussetzungen | [Lektion 3.1](lernziele-formulieren.md); eine Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- Übungsschritte mit jeweils einer Aktion und den echten Namen der Oberfläche in Fettschrift schreiben;
- erfundene, harmlose Daten für Übungen wählen;
- ein erwartetes Ergebnis schreiben, das Teilnehmende und Trainer prüfen können;
- eine Übung mit der Version, die der Kurs verwendet, proben und korrigieren.

## Hinweise für Trainer

### So präsentieren Sie es

1. Zeigen Sie einen schlechten und einen guten Schritt (5 Minuten):
   - schlecht: „Legen Sie einen Benutzer an und geben Sie ihm Rechte.“
   - gut: „Öffnen Sie **Benutzer**, dann die Gruppe **Editors**. Klicken Sie unter **Unterelemente** auf
     **Neu erstellen** und wählen Sie **User**.“
2. Regeln (5 Minuten): eine Aktion pro Schritt; Fettschrift für das, was auf dem Bildschirm steht; Code-Formatierung
   für das, was eingegeben wird; erfundene Daten („Annual Bike Day“, `@example.invalid`); ein **Erwartetes Ergebnis**,
   das nennt, was sichtbar ist.

### Häufige Fragen

**Was, wenn sich die Oberfläche ändert?** Die Hilfe ändert sich mit dem Code; Ihre Übungen sollten das auch. Proben Sie
vor jedem Kurs und korrigieren Sie, was sich geändert hat.

**Bildschirmfotos?** Nützlich, aber sie veralten und dürfen nie personenbezogene Daten zeigen. Textschritte mit echten
Beschriftungen altern besser.

### Stolperfallen

- **Schritte, die von Ihren eigenen Daten abhängen.** „Öffnen Sie den Artikel von gestern“ scheitert auf einer
  frischen Installation.
- **Aktionen, die E-Mails senden.** Testkonten unter `example.invalid`, niemals echte Adressen.
- **Zerstörerische Schritte ohne Warnung.** Entfernen, den Papierkorb leeren oder neu installieren bekommt einen
  Warnhinweis.

## Praktische Übung

1. Wählen Sie eine Aufgabe aus der Anleitung [Objektzustände](../../../../anleitungen/objektzustaende.md) oder
   [Sektionen](../../../../anleitungen/sektionen.md).
2. Schreiben Sie nach den Regeln oben eine Übung mit sechs bis zehn Schritten.
3. Schreiben Sie das **Erwartete Ergebnis** in zwei oder drei Punkten.
4. Proben Sie sie auf Ihrer Schulungsinstallation genau so, wie sie geschrieben ist. Markieren Sie jeden Schritt, bei
   dem Sie raten mussten.
5. Korrigieren Sie die markierten Schritte.
6. Geben Sie sie einer Partnerin oder einem Partner, die oder der sie noch nicht gesehen hat; diese Person erledigt sie,
   ohne Sie zu fragen. Notieren Sie, wo sie hängen blieb.

## Erwartetes Ergebnis

- Eine Übung mit sechs bis zehn Schritten, fett gesetzten Namen der Oberfläche und einem erwarteten Ergebnis.
- Die Partnerin oder der Partner hat sie ohne Hilfe abgeschlossen, oder die Stellen, an denen sie hängen blieb, sind
  korrigiert.

## Quiz

1. Wie viele Aktionen sollte ein Schritt enthalten?
2. Welche Formatierung verwenden diese Kurse für Schaltflächennamen und welche für eingegebenen Text?
3. Welche E-Mail-Domain verwenden Testkonten?
4. Warum probt man mit genau der Version des Kurses?

<details>
<summary>Antworten</summary>

1. Eine.
2. Fettschrift für das, was auf dem Bildschirm steht (**Zur Veröffentlichung schicken**); Code-Formatierung für das,
   was eingegeben wird (`learner01`).
3. `example.invalid`.
4. Namen der Oberfläche und Schritte ändern sich zwischen Versionen; nur eine Probe zeigt es.

</details>

## Weiterführende Literatur

- [Eine Anleitung schreiben, die funktioniert](../../../fuersprecher/ein-tutorial-schreiben.md)
- [Inhalte erstellen und bearbeiten](../../../inhalte-bearbeiten.md): eine Hilfeseite, Schritt für Schritt geschrieben.
- [Sicherheit bei Live-Demos](../../../fuersprecher/sicherheit-bei-live-demos.md): was niemals auf dem Bildschirm sein darf.

## Verwandte Seiten

- [Modul 3: Einen Kurs gestalten](README.md)
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
