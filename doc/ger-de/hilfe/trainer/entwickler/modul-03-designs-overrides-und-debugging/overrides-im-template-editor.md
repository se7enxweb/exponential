---
title: "Lektion 3.3: Overrides im Template-Editor"
audience: trainer, developer
category: training
order: 33
summary: Die Teilnehmenden legen unter Design > Template-Editor der Administration admin4 einen Override an, ordnen ihn um und entfernen ihn, lesen die Einstellungsdatei, die der Editor schreibt, und entscheiden, wann der Weg per Klick und wann der Weg über Dateien besser passt. 40 Minuten.
keywords: entwickler, template-editor, Design, visual/templatelist, templateview, neues überschreib-template, kopie des standard-templates, objekt id, bedingungen speichern, ausgewählte entfernen, Priority, ziehen und ablegen, admin4, developers course, template editor, New override, Copy of default template, Object ID, Save conditions, Remove selected, drag and drop
type: help
---
# Lektion 3.3: Overrides im Template-Editor

Die Administration hat einen visuellen Editor für dieselben Override-Regeln, die die Teilnehmenden in Lektion 3.2 von
Hand geschrieben haben. Er ist der schnellste Weg, eine Idee auszuprobieren, und der sicherste Weg, die Reihenfolge zu
sehen, in der Regeln geprüft werden. In dieser Lektion legen die Teilnehmenden mit dem Editor einen Override für ein
Objekt an, ordnen Regeln um, lesen die Datei, die der Editor geschrieben hat, und entfernen den Override wieder.

> **Hinweis**
> Die Schritte beschreiben das Design **admin4**. Führen Sie sie nur auf der Schulungsinstallation aus: Der Editor
> schreibt Einstellungs- und Template-Dateien.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 3: Designs, Overrides und Fehlersuche](README.md) |
| Dauer | 40 Minuten (15 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler und Template-Bauer |
| Voraussetzungen | [Lektion 3.2](override-regeln.md); ein Administratorkonto auf der Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- **Design** > **Template-Editor** öffnen und die Overrides eines Templates finden;
- mit **Neues Überschreib-Template** und **Kopie des Standard-Templates** einen Override für ein Objekt anlegen;
- Overrides umordnen und ihre Bedingungen mit **Bedingungen speichern** ändern;
- die Regel, die der Editor geschrieben hat, in `settings/siteaccess/<sa>/override.ini.append.php` finden;
- einen Override mit **Ausgewählte entfernen** entfernen und sagen, wann der Weg über Dateien aus Lektion 3.2 die
  bessere Wahl ist.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Template-Editor](../../../../../eng-us/features/6.0/template-editor-overrides.md) und
[Reihenfolge der Template-Overrides](../../../../../eng-us/features/6.0/template-override-ordering.md).

1. **Öffnen** (2 Minuten). Der Tab **Design**, im linken Menü **Template-Editor** (`/visual/templatelist`). Die Seite
   **Template Liste** führt jedes Template mit seiner **Design Ressource** auf, darunter **Die gebräuchlichsten
   Templates**.
2. **Die Overrides eines Templates** (4 Minuten). Öffnen Sie `/node/view/full.tpl`. Die Überschrift nennt die
   Overrides für das Template </node/view/full.tpl> im Siteaccess <site> mit ihrer Anzahl. Wählen Sie in der Liste
   **Siteaccess** einen anderen Siteaccess und klicken Sie auf **Setzen**. Die Overrides werden genau in der
   Reihenfolge gezeigt, in der sie geprüft werden; ziehen Sie eine Karte oder verwenden Sie ihre Pfeile, um umzuordnen,
   und die neue Reihenfolge wird sofort als Werte `Priority` gespeichert.
3. **Neuer Override** (4 Minuten). Das Formular zum Anlegen eines neuen Template-Overrides fragt nach **Extension:**
   (eine der Design-Erweiterungen oder **Keine Extension**), **Dateiname:**, **Überschreibungsschlüssel:** (**Klasse**,
   **Sektion**, **Objekt ID**, **Knoten ID**) und **Template-Grundlage:** (**Leere Datei**, **Kopie des
   Standard-Templates** und andere). **OK** legt ihn mit `Priority=0` an, der höchsten Priorität.
4. **Was er schreibt** (3 Minuten). Die Regel kommt in `settings/siteaccess/<sa>/override.ini.append.php`, der Editor
   behält vor dem Schreiben eine Kopie der Datei und lässt den View-Cache der Inhalte verfallen.
5. **Per Klick oder per Datei?** (2 Minuten). Der Editor ist zum Ausprobieren und Ordnen da. Für eine Website unter
   Versionskontrolle gehören Regel und Template in Ihre Erweiterung (Lektion 3.2); der Editor entfernt nur Regeln, die
   die Datei des Siteaccess definiert, und lässt die Regeln einer Erweiterung unberührt.

### Häufige Fragen

**Warum zeigt die Liste Overrides aus Erweiterungen, die ich nicht geschrieben habe?** Sie zeigt jede Regel, die für
den Siteaccess wirksam ist, egal wo sie definiert ist. Jede Karte sagt, ob die Einstellungen des Siteaccess oder eine
Erweiterung sie definieren.

**Gewinnt ein neuer Override sofort?** Ja, mit `Priority=0` wird er zuerst geprüft.

**Wer darf den Editor verwenden?** Benutzer mit der Richtlinie `visual/templatelist` des Moduls Design.

### Stolperfallen

- **Falscher Siteaccess.** Der Editor arbeitet mit dem Siteaccess, der in der Überschrift steht. Prüfen Sie ihn vor
  **Neues Überschreib-Template**.
- **Die Eingabetaste.** In den Bedingungsfeldern löst die Eingabetaste **Bedingungen speichern** aus, nicht
  **Ausgewählte entfernen**; lesen Sie trotzdem die Schaltfläche, bevor Sie sie drücken.
- **Zwei Teilnehmende, eine Installation.** Bearbeiten auf einer gemeinsamen Schulungsinstallation zwei Personen die
  Overrides desselben Templates, ändern sie gegenseitig ihre Reihenfolge. Geben Sie allen Teilnehmenden eine eigene
  Installation oder ein eigenes Objekt, auf das ihr Override passt.

## Praktische Übung

Auf Ihrer Schulungsinstallation, als Administrator an der Administration angemeldet.

1. Öffnen Sie den Tab **Design** und wählen Sie im linken Menü **Template-Editor**.
2. Geben Sie `full` in den Filter ein und klicken Sie auf **Filter**; öffnen Sie `/node/view/full.tpl`.
3. Prüfen Sie den Siteaccess in der Überschrift; ist es nicht Ihr öffentlicher Siteaccess, wählen Sie ihn unter
   **Siteaccess** und klicken Sie auf **Setzen**.
4. Zählen Sie die Overrides in der Überschrift und suchen Sie den, den Sie in Lektion 3.2 geschrieben haben
   (`mysite_full_article`). Achten Sie darauf, dass er von einer Erweiterung definiert wird.
5. Klicken Sie auf **Neues Überschreib-Template**.
6. Wählen Sie unter **Extension:** **Keine Extension** (der letzte Eintrag), damit die Datei in das Design des
   Siteaccess kommt. Geben Sie unter **Dateiname:** `mysite_one_object` ein; das Formular hängt `.tpl` selbst an.
7. Geben Sie unter **Objekt ID** die Objekt-ID eines Artikels ein (sie steht auf der Seite des Artikels in der
   Administration).
8. Wählen Sie **Kopie des Standard-Templates** und klicken Sie auf **OK**.
9. Öffnen Sie das Template des neuen Overrides aus der Liste zum Bearbeiten, fügen Sie oben
   `<p>Template Editor override</p>` ein und klicken Sie auf **Speichern**.
10. Öffnen Sie den Artikel auf der Website; die Zeile erscheint.
11. Öffnen Sie in einer Shell `settings/siteaccess/<sa>/override.ini.append.php` und suchen Sie den neuen Block mit
    `Priority=0`.
12. Markieren Sie zurück im Editor den neuen Override, klicken Sie auf **Ausgewählte entfernen** und vergewissern Sie
    sich, dass Regel und Datei verschwunden sind.

## Erwartetes Ergebnis

- Schritt 4: Der Kurs-Override steht mit seiner Bedingung `class_identifier` in der Liste.
- Schritt 10: Der Artikel zeigt die Zeile; andere Artikel zeigen weiterhin „Course override:“ aus Lektion 3.2, weil die
  Objektregel zuerst geprüft wird.
- Schritt 11: ein Block mit `Source=node/view/full.tpl`, dem `MatchFile`, `Subdir=templates`, `Match[object]` und
  `Priority=0`.
- Schritt 12: Der Block und seine Template-Datei sind entfernt; der Artikel fällt auf den Override aus Lektion 3.2
  zurück.
- Der Trainer prüft Schritt 11 auf dem Bildschirm einer Person.

## Quiz

1. Wo ist der Template-Editor in der Administration admin4?
2. Welche Wahl unter **Template-Grundlage:** beginnt mit dem Template, das überschrieben wird?
   A. **Leere Datei** B. **Kopie des Standard-Templates** C. **Container ( mit Kindern )** D. **Sicht ( ohne Kinder )**
3. Welche `Priority` erhält ein neuer Override, und was bedeutet das?
4. In welche Datei schreibt der Editor die Regel?
5. Warum lässt **Ausgewählte entfernen** einen Override, den eine Erweiterung definiert, unberührt?

<details>
<summary>Antworten</summary>

1. Tab **Design**, im linken Menü **Template-Editor** (`/visual/templatelist`).
2. **B.** **Kopie des Standard-Templates**.
3. `Priority=0`, die höchste: Er wird vor jeder anderen Regel geprüft.
4. In `settings/siteaccess/<sa>/override.ini.append.php` des Siteaccess, der in der Überschrift steht.
5. Der Editor entfernt nur Regeln, die die Datei des Siteaccess definiert; die Regeln einer Erweiterung gehören der
   Erweiterung ([Template-Editor](../../../../../eng-us/features/6.0/template-editor-overrides.md)).

</details>

## Weiterführende Literatur

- [Template-Editor](../../../../../eng-us/features/6.0/template-editor-overrides.md): alles, was der Editor tut, und wie er sicher
  schreibt.
- [Reihenfolge der Template-Overrides](../../../../../eng-us/features/6.0/template-override-ordering.md): Reihenfolge per Ziehen
  und Ablegen.
- [Overrides mit dem Standard vergleichen](../../../../../eng-us/help/designers/lifecycle/comparing-overrides-with-the-standard.md):
  Overrides nach einem Update aktuell halten.

## Verwandte Seiten

- [Modul 3: Designs, Overrides und Fehlersuche](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
