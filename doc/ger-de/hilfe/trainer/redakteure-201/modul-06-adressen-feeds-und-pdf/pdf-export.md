---
title: "Lektion 6.3: PDF-Exporte"
audience: trainer, editor
category: training
order: 63
summary: Die Teilnehmenden erklären, was ein PDF-Export erzeugt und was der Unterschied zwischen einmalig erstellt und bei jedem Aufruf erzeugt ist, und legen einen Export ihres Kursordners an, laden ihn herunter und erzeugen ihn neu, wo ihre Rolle es erlaubt. 45 Minuten.
keywords: redakteure 201, pdf, pdf-export, neuer pdf-export, quellknoten, baum, klassen, einmalig erstellen, bei jedem aufruf, neu erzeugen, herunterladen, öffentliche adresse, startseite, editors 201, pdf export, new pdf export, source node, tree, classes, generated once, on the fly, regenerate, download, public address, front page
type: help
---
# Lektion 6.3: PDF-Exporte

Manche Inhalte müssen die Website als Dokument verlassen: ein Handbuch, eine Preisliste, ein gedruckter Newsletter.
Ein **PDF-Export** erzeugt aus einem Teil des Baums ein PDF. Diese Lektion zeigt Redakteuren, wie ein Export
eingerichtet und aktuell gehalten wird.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Redakteure 201](../README.md) |
| Modul | [Modul 6: Adressen, Feeds und PDF](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Redakteure, die aus Website-Inhalten Dokumente erstellen |
| Voraussetzungen | [Lektion 6.2](rss-feeds-fuer-redakteure.md); für die Übung eine Rolle mit der Richtlinie `pdf/edit` |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, was ein PDF-Export enthält: einen Quellknoten und bei einem Baum die Knoten darunter von den angekreuzten
  Klassen;
- zwischen **Einmalig erstellen** und bei jedem Aufruf wählen und die Abwägung erklären;
- einen Export mit **Neuer PDF-Export** anlegen, herunterladen und mit **Neu erzeugen** erneuern;
- erklären, warum ein gespeichertes PDF öffentlich ist und was das für seinen Inhalt und seinen Dateinamen bedeutet.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [PDF-Exporte](../../../../anleitungen/pdf-exporte.md).

1. **Was er erzeugt** (3 Minuten). Ein PDF eines **Quellknotens**: sein Name als erste Überschrift, dann seine Felder.
   Ein **Baum**-Export fügt die Knoten darunter hinzu, auf jeder Ebene, aber nur die der **Klassen**, die Sie
   ankreuzen. Ein Knoten einer nicht angekreuzten Klasse wird mit allem darunter ausgelassen; kreuzen Sie also die
   Ordner ebenso an wie die Artikel.
2. **Einmal oder bei jedem Aufruf** (3 Minuten). **Einmalig erstellt**: Das PDF wird beim Speichern des Exports
   geschrieben und erneut mit **Neu erzeugen**; schnell, zeigt aber die Inhalte, wie sie damals waren. **Bei jedem
   Aufruf**: bei jedem Herunterladen neu erzeugt; immer aktuell, bei einem großen Baum langsamer.
3. **Die Seite** (4 Minuten). **Setup** > **PDF-Export** (`/pdf/list`), mit der Richtlinie `pdf/edit` (die Rolle
   Administrator hat sie): *PDF Exporte (n)*, **Übersicht**, **Export finden**, Karten mit **Herunterladen**, **Neu
   erzeugen** und **Bearbeiten**, **Neuer PDF-Export** und **Ausgewählte entfernen**.
4. **Das Formular** (3 Minuten). **Titel** (wird nicht gedruckt), **Inhalt** (Quellknoten mit **Durchsuchen**,
   **Knoten** oder **Baum** und die Klassen), **Startseite** (optionale erste Seite), **Fußzeilentext**, **Art des
   Exports** und **Dateiname**; dann **OK**. Fehler werden oben aufgelistet, mit einem Link zu ihrem Feld.
5. **Öffentliche Dateien** (2 Minuten). Ein gespeichertes PDF ist unter seiner Adresse für jeden öffentlich. Verwenden
   Sie für Inhalte, die nicht für alle sind, die Erzeugung bei jedem Aufruf, und nie einen Dateinamen, den Sie nicht
   erraten lassen möchten.

### Häufige Fragen

**Warum enthält mein PDF nur die erste Seite?** Ein Baum ohne Klassen, oder die Ordner zwischen der Quelle und den
Artikeln sind nicht angekreuzt.

**Warum zeigt mein gespeichertes PDF alte Inhalte?** Es wurde einmalig erstellt; klicken Sie auf **Neu erzeugen**.

**Können Besucher ein bei jedem Aufruf erzeugtes PDF herunterladen?** Nur Benutzer mit der Richtlinie `pdf/edit`, über
die Download-Adresse.

### Stolperfallen

- **Fehlende Berechtigung.** Redakteuren fehlt meist `pdf/edit`. Geben Sie der Schulungsrolle die Richtlinie oder
  führen Sie es vor.
- **Private Inhalte in einem gespeicherten PDF.** Sie werden öffentlich. Exportieren Sie Inhalte nur für Mitglieder
  nicht als gespeichertes PDF.
- **Große Bäume bei jedem Aufruf.** Sie laufen in Zeitüberschreitungen; stellen Sie sie auf einmalig erstellt um.

## Praktische Übung

Verwenden Sie die Schulungsinstallation mit einer Rolle, die die Richtlinie `pdf/edit` hat (der Trainer hat das
eingerichtet oder führt die Schritte am Beamer vor).

1. Öffnen Sie **Setup** > **PDF-Export**. Lesen Sie die **Übersicht**.
2. Klicken Sie auf **Neuer PDF-Export**.
3. **Titel**: `Course folder <Ihr Name>`.
4. Klicken Sie unter **Inhalt** auf **Durchsuchen** und wählen Sie Ihren Kursordner. Wählen Sie **Baum** und kreuzen
   Sie *Folder* und *Article* an.
5. Geben Sie unter **Startseite** eine große Zeile `My course` und eine kleinere Zeile mit dem heutigen Datum ein.
6. Wählen Sie **Einmalig erstellen** und den Dateinamen `course-<your-first-name>.pdf`. Klicken Sie auf **OK**.
7. Lesen Sie in der Liste Ihre Karte: die Kennzeichen, den Quellknoten, **Enthält**, die gespeicherte Datei mit ihrer
   Größe.
8. Klicken Sie auf **Herunterladen** und öffnen Sie das PDF. Suchen Sie Ihre Artikel.
9. Ändern Sie den Titel eines Artikels in Ihrem Kursordner und veröffentlichen Sie. Laden Sie erneut herunter: Das PDF
   hat noch den alten Titel. Klicken Sie auf **Neu erzeugen** und laden Sie erneut herunter.
10. Markieren Sie Ihren Export und klicken Sie auf **Ausgewählte entfernen**; lesen Sie die Bestätigung (sie nennt die
    gespeicherte Datei) und bestätigen Sie.

## Erwartetes Ergebnis

- Ein PDF mit Startseite und dem Ordner und den Artikeln der Person wurde angelegt, heruntergeladen und neu erzeugt.
- Schritt 9 hat den Unterschied zwischen der gespeicherten Datei und den aktuellen Inhalten gezeigt.
- Der Export und seine Datei sind nach Schritt 10 entfernt; die Inhalte sind unberührt.
- Der Trainer prüft das, indem er vor Schritt 10 das PDF einer Person am Beamer herunterlädt.

![PDF-Export: die gespeicherten Exporte mit Neuer PDF-Export](../../../../../eng-us/help/images/trainers/e201-pdf-list.webp)

## Quiz

1. Was enthält ein **Baum**-Export?
2. Was ist der Unterschied zwischen einmalig erstellt und bei jedem Aufruf erzeugt?
3. Ihr gespeichertes PDF zeigt einen alten Titel. Worauf klicken Sie?
   A. **Herunterladen** B. **Neu erzeugen** C. **Bearbeiten** D. **Ausgewählte entfernen**
4. Warum sollten Inhalte nur für Mitglieder nicht als gespeichertes PDF exportiert werden?
5. Welche Richtlinie braucht die Seite PDF-Export?

<details>
<summary>Antworten</summary>

1. Den Quellknoten und die Knoten darunter von den angekreuzten Klassen ([PDF-Exporte](../../../../anleitungen/pdf-exporte.md)).
2. Einmalig erstellt schreibt beim Speichern eine gespeicherte Datei (schnell, kann veraltet sein); bei jedem Aufruf
   erzeugt das PDF bei jedem Herunterladen (immer aktuell, langsamer).
3. **B.**
4. Ein gespeichertes PDF ist unter seiner Adresse für jeden öffentlich.
5. `pdf/edit`.

</details>

## Weiterführende Informationen

- [PDF-Exporte](../../../../anleitungen/pdf-exporte.md): die Seite, das Formular und die Probleme.
- [PDF-Export](../../../../../eng-us/features/6.0/pdf-export.md): was sich in diesem Release geändert hat.

## Verwandte Seiten

- [Modul 6: Adressen, Feeds und PDF](README.md)
- [Redakteure 201: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
