---
title: Systemseiten als Lesezeichen
audience: editor, admin
category: administration
order: 30
summary: Behalten Sie die Seiten der Administration, die Sie täglich öffnen, etwa die Einrichtung, die Hilfe, das Audit-Protokoll oder eine gefilterte Liste, in Ihren Lesezeichen neben Ihren Inhalten, in denselben Ordnern.
keywords: Lesezeichen, Seite merken, gemerkt, Systemseite, Modulansicht, Einrichtung, Ordner, Lesezeichen umbenennen, Favorit, Verknüpfung, bookmark, bookmarks, bookmark this page, system page, module view
context: content/bookmark
type: help
---
# Systemseiten als Lesezeichen

Lesezeichen zeigten bisher nur auf Inhalte: einen Artikel, einen Ordner, ein Bild. Seit Exponential 6.0.15 können Sie
auch die **Seiten der Administration selbst** merken, die Systemseiten: die Systeminformationen, die Cache-Seite, die
Hilfe-Übersicht, das Audit-Protokoll gefiltert auf einen Benutzer, eine Liste von Entwürfen, Ihre eigene
Lesezeichen-Seite mit einem bestimmten Ordner. Sie liegen in denselben Ordnern wie Ihre Inhalts-Lesezeichen, im Kasten
**Lesezeichen** am Rand jeder Seite und auf der Seite **Meine Lesezeichen**.

Diese Seite zeigt Schritt für Schritt, wie Sie eine Systemseite merken, wie Sie sie wiederfinden und wie Sie sie
umbenennen, verschieben und entfernen. Sie richtet sich an alle, die in der Administration arbeiten; Sie brauchen das
Recht, Lesezeichen zu verwenden (die Richtlinie *content/bookmark*, die Redakteure und Administratoren haben).

Die Bilder dieser Seite zeigen die Administration mit deutscher Oberfläche.

[Hilfe](README.md) · Siehe auch: [die Anleitung zu Lesezeichen](../anleitungen/lesezeichen.md) ·
[Lesezeichen für Systemseiten (Funktion)](../funktionen/6.0/lesezeichen-fuer-systemseiten.md) ·
[Ein Rundgang durch die Administration](rundgang-durch-die-administration.md)

## Kurz gesagt

- Auf jeder Systemseite gibt es über der Seite, rechts neben dem Pfad, die Schaltfläche **Seite merken**, und dieselbe
  Schaltfläche im Kasten **Lesezeichen** der rechten Spalte.
- Die Schaltfläche öffnet ein kleines Formular: den **Namen** (den Titel der Seite, den Sie ändern können) und den
  **Ordner**. Wählen Sie **Lesezeichen hinzufügen**, und Sie sind wieder auf der Seite; die Schaltfläche heißt jetzt
  **Gemerkt**.
- Das Lesezeichen behält die Adresse der Seite mit ihren Parametern (`/content/bookmark/(folder)/top`,
  `/audit/console/(user)/14`), aber nicht den Siteaccess: Es öffnet sich in jeder Administration der Installation.
- Lesezeichen von Systemseiten haben ein **Zahnrad-Symbol**; Inhalts-Lesezeichen behalten das Symbol ihrer Klasse.
- Eine Systemseite, die Sie nicht mehr öffnen dürfen (Ihre Rechte haben sich geändert oder die Seite gibt es nicht
  mehr), wird **ohne Link** gezeigt und mit **Kein Zugriff** markiert. Sie können sie trotzdem umbenennen, verschieben
  oder entfernen.

## 1. Eine Seite merken

1. Öffnen Sie die Seite, zu der Sie zurückkehren möchten, zum Beispiel **Einrichtung > Systeminformationen**.
2. Sehen Sie oben auf der Seite rechts neben dem Pfad nach: die Schaltfläche **Seite merken**.

   ![Der obere Teil der Seite Systeminformationen mit der Schaltfläche Seite merken rechts](images/bookmarks/bookmark-this-page-button.webp)

3. Wählen Sie **Seite merken**. Unter der Schaltfläche öffnet sich ein kleines Formular:

   ![Das geöffnete Formular unter der Schaltfläche: Name, Ordner, die Adresse der Seite, Lesezeichen hinzufügen und Meine Lesezeichen](images/bookmarks/bookmark-this-page-form.webp)

   - **Name**: vorausgefüllt mit dem Titel der Seite, wie ihn der Reiter des Browsers zeigt. Ändern Sie ihn so, wie
     er Ihnen hilft, zum Beispiel in *Systeminformationen (Server)*.
   - **Ordner**: *Kein Ordner (oberste Ebene)* oder einer Ihrer Lesezeichen-Ordner. Ordner legen Sie auf der Seite
     Meine Lesezeichen an (siehe [die Anleitung zu Lesezeichen](../anleitungen/lesezeichen.md#5-ordner)).
   - Unter den Feldern zeigt das Formular die **Adresse**, die das Lesezeichen behält.
4. Wählen Sie **Lesezeichen hinzufügen**. Die Seite lädt neu, und die Schaltfläche heißt jetzt **Gemerkt**:

   ![Dieselbe Seite nach dem Hinzufügen: Die Schaltfläche heißt Gemerkt und ist hervorgehoben](images/bookmarks/bookmarked-button.webp)

Das Formular funktioniert ohne JavaScript; es ist ein Teil der Seite, der sich öffnet und schließt. Wählen Sie die
Schaltfläche noch einmal, um es ohne Speichern zu schließen.

### Aus dem Kasten Lesezeichen

Der Kasten **Lesezeichen** in der rechten Spalte bietet auf jeder Systemseite dieselbe Schaltfläche unter Ihren
Lesezeichen an. Sie öffnet dasselbe Formular. Auf einer Inhaltsseite behält der Kasten wie bisher seine Schaltfläche
**Lesezeichen setzen** für den Eintrag.

### Name oder Ordner später ändern

Öffnen Sie die Seite wieder und wählen Sie **Gemerkt**. Das Formular zeigt den gespeicherten Namen und Ordner; ändern
Sie sie und wählen Sie **Lesezeichen speichern**. Eine Seite wird nur einmal gemerkt: Erneutes Speichern legt nie ein
zweites Lesezeichen derselben Adresse an.

## 2. Wiederfinden

Der Kasten **Lesezeichen** listet Ihre Lesezeichen in ihren Ordnern, auf jeder Seite. Systemseiten haben ein
Zahnrad-Symbol:

![Der Kasten Lesezeichen mit dem Ordner Verwaltung und drei Systemseiten, eine davon ausgegraut, und Hilfe auf der obersten Ebene](images/bookmarks/bookmarks-box-system-pages.webp)

Wählen Sie den Namen, um die Seite zu öffnen. Inhalts-Lesezeichen und Systemseiten stehen gemeinsam in Ihrer
Reihenfolge:

![Der Kasten Lesezeichen mit Systemseiten in einem Ordner und einem Inhalts-Lesezeichen (Websites) mit seinem Klassensymbol](images/bookmarks/bookmarks-box-both-kinds.webp)

Auf **Meine Lesezeichen** (Dashboard > Meine Lesezeichen) zählt die Übersicht Ihre **Systemseiten**, und eine Zeile
darüber sagt, wie Sie eine hinzufügen:

![Die Übersicht von Meine Lesezeichen: der Hinweis zu Systemseiten, dann die Zahlen Lesezeichen, Ordner, Systemseiten, In keinem Ordner, Verborgene Einträge und Nicht mehr verfügbar](images/bookmarks/bookmark-page-overview.webp)

Jede Systemseite hat eine Karte wie ein Inhalts-Lesezeichen. Sie zeigt das Zahnrad-Symbol, den Namen, die Kennzeichnung
**Systemseite**, die **Adresse** und **Modul und Ansicht**, zu denen die Seite gehört, **Öffnen**, **Nach oben** und
**Nach unten** sowie **Umbenennen**:

![Ein Ordner auf Meine Lesezeichen mit drei Karten von Systemseiten: Systeminformationen (Server), Meine Lesezeichen und eine Karte mit Kein Zugriff](images/bookmarks/bookmark-page-system-page-card.webp)

**Lesezeichen finden** durchsucht auf dieser Seite auch die Adressen; `systemupgrade` oder `audit/` findet also die
Systemseiten mit dieser Adresse. In der Sortierung **Typ** stehen Systemseiten unter *Systemseite*.

## 3. Umbenennen, verschieben, entfernen

**Umbenennen** (nur Systemseiten):

1. Suchen Sie auf **Meine Lesezeichen** die Karte und wählen Sie unter ihrer Adresse **Umbenennen**.
2. Geben Sie den neuen Namen ein und wählen Sie **Name speichern** (oder drücken Sie im Feld die Eingabetaste).

   ![Die Karte des Lesezeichens Hilfe mit geöffnetem Umbenennen, dem eingegebenen neuen Namen Hilfe für mein Team und Name speichern](images/bookmarks/bookmark-page-rename.webp)

3. Die Seite meldet *Das Lesezeichen wurde in „…“ umbenannt.* Der Kasten Lesezeichen zeigt den neuen Namen sofort.

Ein Inhalts-Lesezeichen zeigt immer den aktuellen Namen seines Eintrags und hat deshalb kein Umbenennen.

**Verschieben** in einen anderen Ordner: Haken Sie die Karte an und verwenden Sie unter der Liste **Ausgewählte
Lesezeichen verschieben nach**, oder ziehen Sie die Karte auf einen Ordner der Ordnerliste; auf der Seite selbst können
Sie auch **Gemerkt** wählen und einen anderen Ordner auswählen. **Nach oben**, **Nach unten**, der Griff und das
Positionsfeld ordnen Systemseiten und Inhalts-Lesezeichen gemeinsam, in einer Reihenfolge je Ordner.

**Entfernen**: Haken Sie die Karte an, wählen Sie **Ausgewählte entfernen** und bestätigen Sie. Entfernt wird nur das
Lesezeichen, nie die Seite. Ein entfernter Ordner nimmt seine Lesezeichen von Systemseiten nur mit, wenn Sie *Den gesamten
Inhalt mit entfernen* wählen.

## 4. Eine Seite, die Sie nicht mehr öffnen dürfen

Ob Sie eine Systemseite öffnen dürfen, wird jedes Mal geprüft, wenn Ihre Lesezeichen gezeigt werden, mit denselben
Regeln wie beim Öffnen der Seite: Ihre Rollen und Richtlinien, die Siteaccess-Regeln und ob Modul und Ansicht noch
existieren. Dürfen Sie sie nicht mehr öffnen, ist das Lesezeichen kein Link: Der Kasten Lesezeichen zeigt es
ausgegraut, und seine Karte auf Meine Lesezeichen sagt **Kein Zugriff** und was zu tun ist:

![Eine Karte mit Systemseite und Kein Zugriff: Sie dürfen diese Seite nicht mehr öffnen. Entfernen Sie das Lesezeichen oder bitten Sie einen Administrator um Zugriff.](images/bookmarks/bookmark-page-no-access.webp)

Entfernen Sie das Lesezeichen oder bitten Sie einen Administrator um den Zugriff, den Sie brauchen. Bekommen Sie den
Zugriff zurück, kommt auch der Link von selbst zurück. Die Übersicht zählt solche Lesezeichen unter **Nicht mehr
verfügbar**.

## 5. Fragen und Antworten

**Ich sehe Seite merken nicht.** Die Schaltfläche erscheint nur auf Seiten einer Modulansicht, nicht auf einem
Inhaltseintrag (dort verwenden Sie *Lesezeichen setzen*), nicht beim Bearbeiten oder Durchsuchen und nicht auf
einer Fehlerseite. Sie fehlt überall, wenn Sie keine Lesezeichen verwenden dürfen (bitten Sie um die Richtlinie
*content/bookmark*) oder wenn das Datenbank-Update von Exponential 6.0.15 auf Ihrer Installation noch nicht ausgeführt
wurde (Ihr Administrator führt `update/common/scripts/6.0/createbookmarkviewtable.php` aus; siehe
[Lesezeichen für Systemseiten](../funktionen/6.0/lesezeichen-fuer-systemseiten.md#installieren-und-aktualisieren)).

**Das Lesezeichen öffnet die Seite ohne meinen Filter.** Das Lesezeichen behält die Parameter, die Teil der Adresse
sind, etwa `/(folder)/top` oder `/(user)/14`. Ein Filter, den die Seite nur in einem Formular oder nach einem `?` in
der Adresse hält, gehört nicht zum Lesezeichen. Die meisten Listen der Administration halten ihre Filter in der
Adresse; öffnen Sie die gefilterte Liste und merken Sie sie von dort.

**Ich habe die Seite in einer anderen Administration gemerkt.** Lesezeichen behalten die Adresse ohne den Siteaccess;
ein Lesezeichen aus einer Administration (zum Beispiel der englischen) öffnet dieselbe Seite in der, in der Sie gerade
arbeiten.

**Wer sieht meine Lesezeichen?** Niemand sonst. Lesezeichen sind persönlich, für Systemseiten wie für Inhalte.

**Die Lesezeichenliste des Online-Editors zeigt meine Systemseiten nicht.** Sie listet Inhalte, die Sie verlinken
oder einbetten können; Systemseiten sind keine Inhalte.
