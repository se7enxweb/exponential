---
title: "Lektion 1.2: Das Hilfe-Center als Ihr Lehrbuch"
audience: trainer, editor, admin
category: training
order: 12
summary: Nutzen Sie das Hilfe-Center in der Administration als Lehrbuch jedes Kurses: Suche, Zielgruppenfilter, der Baum Alle Seiten, Suchadressen zum Speichern, kontextbezogene Hilfe, Druck und PDF. 45 Minuten.
keywords: hilfe-center, exphelp, dashboard, suche, zielgruppenfilter, alle seiten, inhalt, drucken, pdf, kontexthilfe, lehrbuch, handout, help center, search, audience filter, all pages, print, context help, textbook
type: help
---
# Lektion 1.2: Das Hilfe-Center als Ihr Lehrbuch

Jede Exponential-Installation trägt ihr eigenes Handbuch in sich: das **Hilfe-Center**, das die Dokumentation in
`doc/` in der Administration anzeigt. Für einen Trainer ist das das bestmögliche Lehrbuch. Die Teilnehmenden finden es
nach dem Kurs auf ihrer eigenen Installation wieder, es passt zu der Version, die sie betreiben, und jede Seite lässt
sich für ein Handout drucken oder als PDF herunterladen. Diese Lektion bringt Trainern bei, es flüssig zu nutzen,
damit sie den Teilnehmenden beibringen können, sich selbst zu helfen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Train the Trainer](../README.md) |
| Modul | [Modul 1: Den Unterricht von Exponential vorbereiten](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | Trainer; dieselbe Lektion funktioniert für Redakteure und Administratoren |
| Voraussetzungen | [Lektion 1.1](wie-diese-kurse-funktionieren.md); ein Konto mit der Richtlinie `exphelp/read` (jeder Administrator; auf einer neuen Installation die Rolle Editor) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- das Hilfe-Center aus der Kopfzeile, dem Benutzermenü oder über seine Adresse `/exphelp/dashboard` öffnen;
- eine Seite über die Suche, über Zielgruppen- und Kategoriefilter und im Baum **Alle Seiten** finden;
- mit dem **Hilfemenü** („?“) in der Kopfzeile die Hilfe zu der Seite öffnen, auf der sie sind;
- eine Such- oder Stöberadresse erstellen, die sie auf ein Handout setzen können;
- eine Hilfeseite drucken oder als PDF herunterladen.

## Hinweise für Trainer

### So präsentieren Sie es

1. Zeigen Sie das Dashboard auf dem Projektor (`/exphelp/dashboard`): die Überschrift **Wie können wir helfen?**, das
   Suchfeld, die Zielgruppenkarten unter **Hier beginnen**, **Empfohlene Anleitungen**, **Kürzlich aktualisiert**,
   **Nach Themen stöbern** und **Einen Fehler gefunden?** (5 Minuten).
2. Zeigen Sie die Schaltfläche „?“ in der Kopfzeile (**Hilfemenü**) auf zwei verschiedenen Seiten der Administration
   und weisen Sie darauf hin, dass sich **Hilfe zu dieser Seite** mit der Seite ändert (3 Minuten).
3. Suchen Sie nach einem Wort, das Ihre Teilnehmenden brauchen werden (zum Beispiel `veröffentlichen`), und zeigen Sie
   die Filter (3 Minuten).
4. Zeigen Sie die Adressleiste nach einer gefilterten Suche: Die Filter sind Teil der Adresse, zum Beispiel
   `/exphelp/search/(category)/layouts/(audience)/editor`. So setzen Sie eine fertige Suche auf ein Handout
   (2 Minuten).
5. Öffnen Sie eine Seite und zeigen Sie **Diese Seite drucken** und **Als PDF herunterladen** (2 Minuten).

### Häufige Fragen

**Können Besucher der öffentlichen Website die Hilfe lesen?** Nein. Die Ansichten der Hilfe brauchen die Richtlinie
`exphelp/read`; die Rolle Anonymous hat sie nicht, und die Hilfe wird Besuchern, die nicht angemeldet sind, nie
angezeigt.

**Ein Redakteur sieht die Schaltfläche „?“ nicht.** Die Hilfe-Schaltfläche erscheint nur für Benutzer mit
`exphelp/read`. Auf einer Installation aus der Zeit vor dem Hilfesystem erteilt ein Administrator sie mit
`php bin/php/addexphelpreadpolicy.php` (probieren Sie es zuerst mit `--dry-run`).

**Ist die Hilfe dasselbe wie die Website `doc.exponential.earth`?** Das Hilfe-Center wird aus dem Verzeichnis `doc/`
der Installation selbst erzeugt und passt deshalb immer zur installierten Version.

**Welche Dateien zeigt es?** Nur Dateien unterhalb von `doc/`, die Git verfolgt (Markdown, Text und Bilder). Alles
andere ist absichtlich ein 404.

### Stolperfallen

- **Teilnehmende ohne die Richtlinie.** Prüfen Sie vor dem Kurs, dass die Konten der Teilnehmenden
  `/exphelp/dashboard` öffnen können.
- **Die Sofortsuche braucht JavaScript.** Ohne JavaScript funktioniert die Suche trotzdem, nach einem Klick auf
  **Suchen**.
- **Die Taste `/`.** `/` springt in das Suchfeld, aber nicht, solange der Cursor in einem anderen Feld steht. Bitten
  Sie die Teilnehmenden, zuerst auf eine leere Stelle der Seite zu klicken.

## Praktische Übung

Arbeiten Sie auf Ihrer eigenen Schulungsinstallation, in der Administration angemeldet.

1. Öffnen Sie das Hilfe-Center: die Schaltfläche „?“ in der Kopfzeile (**Hilfemenü**), dann **Hilfe-Center öffnen**.
   Notieren Sie die Adresse im Browser: Sie endet auf `/exphelp/dashboard`.
2. Öffnen Sie unter **Hier beginnen** die Karte **Redakteure**. Notieren Sie, wie viele Seiten die Zielgruppe zeigt.
3. Gehen Sie zurück zum Dashboard. Klicken Sie auf eine leere Stelle der Seite, drücken Sie `/` und geben Sie
   `version` ein. Beobachten Sie, wie die Ergebnisse während der Eingabe erscheinen; klicken Sie dann auf **Suchen**.
4. Grenzen Sie die Ergebnisse auf der Suchseite mit dem Zielgruppenfilter **Redakteure** ein. Kopieren Sie die Adresse
   aus der Adressleiste des Browsers in eine Textdatei: Das ist ein Link für das Handout.
5. Öffnen Sie **Alle Seiten** (`/exphelp/contents`). Geben Sie `layout` in das Filterfeld des Baums ein und öffnen Sie
   **Der Layout-Editor**.
6. Suchen Sie auf dieser Seite das Inhaltsverzeichnis, die Links zur vorherigen und nächsten Seite und die Angaben zur
   Seite (Quelldatei und letzte Änderung).
7. Klicken Sie auf **Diese Seite drucken** und sehen Sie sich die Druckvorschau an: keine Menüs, nur das Dokument.
   Brechen Sie den Druck ab.
8. Klicken Sie auf **Als PDF herunterladen** und öffnen Sie die Datei.
9. Gehen Sie in der Administration zu **Benutzer > Rollen und Richtlinien**, öffnen Sie dann wieder die Schaltfläche
   „?“ und wählen Sie die Hilfe zu dieser Seite. Notieren Sie, welche Seite sich öffnet.

## Erwartetes Ergebnis

- Die Teilnehmenden haben eine Textdatei mit mindestens einer Suchadresse der Form `/exphelp/search/...`, die die
  gefilterte Suche wieder öffnet.
- Das PDF von **Der Layout-Editor** öffnet sich, mit dem Seitentitel und einer Seitenzahl („Seite n von N“) am Fuß
  jeder Seite und dem Lizenzhinweis am Ende.
- Auf der Seite der Rollen öffnet die Kontexthilfe eine Seite über Rollen und Richtlinien, nicht das Dashboard. (Hat
  eine Installation für eine Seite keinen Kontexteintrag, fällt die Hilfe auf das Dashboard zurück; das ist das
  erwartete Verhalten.)

## Quiz

1. Welche Richtlinienfunktion öffnet die Ansichten der Hilfe?
2. Alle Teilnehmenden sollen mit derselben gefilterten Liste von Layout-Seiten für Redakteure beginnen. Was setzen Sie
   auf das Handout?
3. Welche Schaltfläche öffnet die Hilfe zu der Seite der Administration, auf der Sie sind?
4. Richtig oder falsch: Das Hilfe-Center zeigt jede Datei im Installationsverzeichnis, wenn Sie ihren Pfad kennen.
5. Womit endet ein heruntergeladenes PDF?

<details>
<summary>Antworten</summary>

1. `exphelp/read` ([Das Hilfesystem](../../../../../eng-us/features/6.0/help-system.md#who-may-read-it)).
2. Eine Stöberadresse mit Ansichtsparametern, zum Beispiel `/exphelp/search/(category)/layouts/(audience)/editor`;
   diese Adressen lassen sich als Lesezeichen speichern und verlinken.
3. Die Schaltfläche „?“ in der Kopfzeile (**Hilfemenü**), Eintrag **Hilfe zu dieser Seite**.
4. **Falsch.** Nur von Git verfolgte Dateien unterhalb von `doc/` mit der Endung einer Seite oder eines Bildes; alles
   andere ist ein 404.
5. Mit dem Lizenzhinweis der Seite (der GNU Free Documentation License).

</details>

## Weiterführende Literatur

- [Das Hilfesystem](../../../../../eng-us/features/6.0/help-system.md): jede Ansicht, Einstellung und jeder Befehl des
  Hilfe-Centers.
- [Suchen](../../../suchen.md): das Durchsuchen der Inhalte der Website, das etwas anderes ist als das Durchsuchen
  der Hilfe.
- [Tastenkürzel](../../../tastenkuerzel.md): die Tasten der Administration und der Hilfeseiten.
- [Ein Rundgang durch die Administration](../../../rundgang-durch-die-administration.md): die Kopfzeile, die Menüs und die Schaltfläche „?“ im
  Zusammenhang.

## Verwandte Seiten

- [Modul 1: Den Unterricht von Exponential vorbereiten](README.md)
- [Train the Trainer: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
