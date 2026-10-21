---
title: "Lektion 8.1: Inhaltsprüfungen: ungültige Relationen und nicht verknüpfte Dateien"
audience: trainer, editor
category: training
order: 81
summary: Die Teilnehmenden führen die Prüfungen Ungültige Relationen und Nicht verknüpfte Objekte für einen Teil des Baums aus, lesen die Befunde, reparieren einen defekten Verweis und räumen ungenutzte Dateien sicher auf. 45 Minuten.
keywords: redakteure 201, inhaltsprüfungen, ungültige relationen, nicht verknüpfte objekte, fehlende dateien, startort, befunde, angekreuzte objekte in den papierkorb, qualität, aufräumen, editors 201, content checks, broken relations, unreferenced objects, missing files, start location, findings, move ticked objects to the trash, quality, cleanup
type: help
---
# Lektion 8.1: Inhaltsprüfungen: ungültige Relationen und nicht verknüpfte Dateien

Über die Jahre sammelt eine Website defekte Verweise (Links auf entfernte Seiten, Einbettungen von Bildern im
Papierkorb) und Dateien, die niemand verwendet. Die **Inhaltsprüfungen** von Exponential finden beides. Sie sind Seiten
der Administration, meist für Administratoren und erfahrene Redakteure; diese Lektion vermittelt, wie man sie liest und
danach handelt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Redakteure 201](../README.md) |
| Modul | [Modul 8: Qualität und Barrierefreiheit](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Erfahrene Redakteure und Administratoren, die Inhalte aufgeräumt halten |
| Voraussetzungen | [Modul 2](../modul-02-verknuepfungen-orte-und-einbetten/README.md); eine Rolle mit den `contentcheck`-Richtlinien |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- **Ungültige Relationen** und **Nicht verknüpfte Objekte** für einen Teil des Baums aus dem Menü des Elements öffnen;
- einen Startort und Filter wählen und die Befunde lesen;
- einen defekten Verweis reparieren, indem sie das Quellelement bearbeiten;
- ungenutzte Medien mit **Angekreuzte Objekte in den Papierkorb** in den Papierkorb legen, im Wissen, was die Prüfung
  nicht wissen kann.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Die Inhaltsprüfungen](../../../../../eng-us/features/6.0/content-checks.md).

1. **Wo** (2 Minuten). Im Menü des Elements: **Fortgeschritten** > **Ungültige Relationen im Teilbaum** und **Nicht
   verknüpfte Objekte im Teilbaum**; auch im Setup-Menü. Sie funktionieren nur in den Administrations-Siteaccesses, und
   eine neue Installation gibt ihre Richtlinien (`contentcheck`: unreferenced, trash, brokenrelations) nur der Rolle
   Administrator.
2. **Ungültige Relationen** (5 Minuten). Sichtbare, veröffentlichte Inhalte, die auf ein Element verweisen, das
   gelöscht wurde, im Papierkorb liegt, verborgen ist oder nie veröffentlicht wurde. **Filter**: **Startort** (**Ganzer
   Baum**, **Websites**, **Medien**, eine Website oder **Mit der Knotenauswahl wählen**), **Ziele** (**Gelöscht**, **Im
   Papierkorb**, **Verborgen**, **Nie veröffentlicht**, **Ohne Ort**, **Für Sie nicht verfügbar**, **Fehlende Dateien
   in var/site/storage**) und **Verweise** (**Links und Einbettungen im Rich Text**, **Objektbeziehungen und
   Beziehungsfelder**), dann **Anzeigen**. Jeder Befund nennt die Quelle, das Feld, das Ziel, seinen Zustand und was zu
   tun ist, mit einem Link zum Bearbeiten der Quelle. Die Seite liest nur; sie ändert nichts.
3. **Nicht verknüpfte Objekte** (5 Minuten). Veröffentlichte Medien (Bilder, Dateien, Medien) unter dem Startort, die
   nichts verwendet: kein anderes Element, kein Layout, keine Einstellung, kein Template, keine Rolle, kein Shop- oder
   Newsletter-Eintrag. Filtern Sie nach **Klassen**, **Sortieren nach** und **Richtung**, dann ankreuzen und nach einer
   Bestätigung **Angekreuzte Objekte in den Papierkorb**. Seiten, Ordner und Benutzer werden nie aufgelistet.
4. **Was die Prüfung nicht wissen kann** (3 Minuten). Eine Datei, die von außerhalb der Website verlinkt ist (eine
   gedruckte Adresse, eine andere Website), gilt als ungenutzt. Deshalb kommt sie in den Papierkorb und wird nicht
   gelöscht: Stellen Sie sie wieder her, wenn sich jemand beschwert.

### Häufige Fragen

**Warum kann ich die Prüfungen nicht öffnen?** Der Rolle fehlen die `contentcheck`-Richtlinien, oder Sie sind im
Editor-Siteaccess.

**Ist es sicher, alles, was die Liste zeigt, in den Papierkorb zu legen?** Sehen Sie zuerst hin. Alles, was von
außerhalb der Website verwendet wird, ist für die Prüfung unsichtbar.

**Wie oft sollten wir sie ausführen?** Vereinbaren Sie es im Team; ein monatlicher Blick auf **Ungültige Relationen**
für Ihren Bereich ist ein guter Anfang.

### Stolperfallen

- **Große Startorte.** Der ganze Baum braucht auf einer großen Website Zeit; beginnen Sie mit Ihrem eigenen Ordner.
- **Gemeinsam genutzte Bilder in den Papierkorb legen.** Ein Bild kann außerhalb der Website verwendet werden
  (verschickte Newsletter, Beiträge in sozialen Medien). Führen Sie eine Liste dessen, was Sie in den Papierkorb gelegt
  haben.
- **Leere Ergebnisse auf einer frischen Schulungs-Website.** Bereiten Sie einen defekten Verweis und ein ungenutztes
  Bild vor (siehe die Modulübersicht).

## Praktische Übung

Verwenden Sie die Schulungsinstallation mit einer Rolle, die die `contentcheck`-Richtlinien hat (oder folgen Sie am
Beamer). Der Trainer hat im Kursordner jeder Person einen Artikel vorbereitet, der ein Bild einbettet, das jetzt im
Papierkorb liegt, und ein Bild, das niemand verwendet.

1. Öffnen Sie im Baum das Menü Ihres Kursordners und wählen Sie **Fortgeschritten** > **Ungültige Relationen im
   Teilbaum**.
2. Prüfen Sie, dass der Startort Ihr Ordner ist. Behalten Sie unter **Ziele** alle bei, unter **Verweise** beide.
   Klicken Sie auf **Anzeigen**.
3. Lesen Sie den Befund: den Quellartikel, das Feld, das Zielbild, seinen Zustand (*Im Papierkorb*) und die
   vorgeschlagene Maßnahme.
4. Folgen Sie dem Link, um den Quellartikel zu bearbeiten. Entfernen Sie die Einbettung (oder betten Sie ein anderes
   Bild ein) und veröffentlichen Sie.
5. Klicken Sie in der Prüfung erneut auf **Anzeigen**: Der Befund ist verschwunden.
6. Wählen Sie im Menü des Elements **Fortgeschritten** > **Nicht verknüpfte Objekte im Teilbaum** für Ihren Kursordner.
7. Lesen Sie die Liste: Das ungenutzte Bild steht darin mit seiner **Klasse**, **Größe**, **Zuletzt geändert** und
   seinem **Ort**.
8. Kreuzen Sie es an und klicken Sie auf **Angekreuzte Objekte in den Papierkorb**; lesen Sie die Bestätigung und
   bestätigen Sie.
9. Öffnen Sie den **Papierkorb** und prüfen Sie, dass das Bild dort liegt.

## Erwartetes Ergebnis

- Der defekte Verweis wurde gefunden, repariert und wird nicht mehr aufgelistet.
- Das ungenutzte Bild wurde aufgelistet und in den Papierkorb gelegt, aus dem es wiederhergestellt werden kann.
- Der Trainer prüft das, indem er **Ungültige Relationen** für den Ordner **Training** am Beamer ausführt: keine Befunde
  mehr.

![Ungültige Relationen mit dem Startort, den zu prüfenden Zielen und Verweisen und Anzeigen](../../../../../eng-us/help/images/trainers/e201-broken-relations.webp)

## Quiz

1. Nennen Sie drei Zustände von Zielen, nach denen die Prüfung **Ungültige Relationen** suchen kann.
2. Richtig oder falsch: **Ungültige Relationen** ändert Inhalte, wenn Sie auf **Anzeigen** klicken.
3. Welche Elemente listet **Nicht verknüpfte Objekte** auf?
   A. Jedes Element B. Ordner und Seiten C. Veröffentlichte Medien, die nichts verwendet D. Benutzer
4. Warum legt **Nicht verknüpfte Objekte** in den Papierkorb, statt zu löschen?
5. Wer hat auf einer neuen Installation die Richtlinien für die Inhaltsprüfungen?

<details>
<summary>Antworten</summary>

1. Drei beliebige aus: gelöscht, im Papierkorb, verborgen, nie veröffentlicht, ohne Ort, für Sie nicht verfügbar,
   fehlende Dateien ([Die Inhaltsprüfungen](../../../../../eng-us/features/6.0/content-checks.md#in-short)).
2. **Falsch.** Die Seite liest nur.
3. **C.**
4. Die Prüfung kann Verwendungen von außerhalb der Website nicht kennen; der Papierkorb erlaubt die Wiederherstellung.
5. Nur die Rolle Administrator.

</details>

## Weiterführende Informationen

- [Die Inhaltsprüfungen](../../../../../eng-us/features/6.0/content-checks.md): jede Prüfung, was als Verweis zählt, und fehlende
  Dateien.
- [Entfernen und Verbergen: die Prüfung umgekehrter Verknüpfungen](../../../../../eng-us/features/6.0/reverse-relation-check.md):
  defekte Verweise verhindern.

## Verwandte Seiten

- [Modul 8: Qualität und Barrierefreiheit](README.md)
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
