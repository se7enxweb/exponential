---
title: Der Import-Editor von 7x SQLI Import
audience: admin, integrator
category: integration
order: 15
summary: Einen Import in der Administration mit dem Import-Editor von 7x SQLI Import anlegen und ändern - die sechs Schritte Quelle, Verarbeitung, Zuordnung, Vorschau, Zeitplan und Prüfung, Auto-match by name, der einzeilige Text einer Kette von Umwandlungen, die Vorschau mit Probelauf sowie Importe kopieren, deaktivieren und löschen.
keywords: sqliimport, Import-Editor, sqliimport/edit, New import, Quelle, Verarbeitung, Zuordnung, Zuordnungsraster, Auto-match by name, Umwandlungen, Kette, Vorschau, Stichprobe, Probelauf, Zeitplan, Prüfung, Save and run, Duplicate, Enable, Disable, Delete, Tabelle, XLSX, ODS, import editor, mapping, preview, dry run, schedule, review, spreadsheet
type: help
---
# Der Import-Editor von 7x SQLI Import

Der Import-Editor (`sqliimport/edit`) legt Importe in der Administration an und ändert sie, ohne PHP. Er führt
einen Import durch sechs Schritte. Jeder Schritt ist ein Formular, das für sich gespeichert wird; so geht nichts auf
halbem Weg verloren, und jeder Schritt lässt sich später wieder öffnen.

Die Oberfläche ist auf Englisch; die Namen der Schaltflächen stehen hier so, wie sie auf der Seite erscheinen.

## Inhalt

1. [Den Editor öffnen](#den-editor-öffnen)
2. [Die sechs Schritte](#die-sechs-schritte)
3. [Das Zuordnungsraster](#das-zuordnungsraster)
4. [Die Vorschau](#die-vorschau)
5. [Kopieren, deaktivieren und löschen](#kopieren-deaktivieren-und-löschen)

## Den Editor öffnen

- **New import** auf der Übersicht des Reiters Importe oder in seinem linken Menü öffnet `sqliimport/edit/new`.
- **Edit** neben einem Import in der Liste öffnet den Import bei seinem ersten Schritt.
- **Edit** auf der Seite Schedules öffnet den Schritt Zeitplan des Imports.

Der Editor braucht die Richtlinie **Manage imports** (`sqliimport/manage`) oder `sqliimport/manageimports` von 1.x.
Mit einer Handler-Einschränkung lassen sich nur Importe der erlaubten Handler öffnen. Ein Benutzer nur mit **Run
imports** (`sqliimport/run`) sieht die Importe und führt sie aus, kann sie aber nicht ändern.

Jeder Schritt hat die Schaltflächen **Save and continue** (speichern, dann der nächste Schritt), **Save** (speichern
und bleiben) und **Back to the imports** (zurück zu den Importen). Jeder Wert wird auf dem Server geprüft; ein
Schritt mit Fehlern wird mit markierten Feldern erneut gezeigt.

## Die sechs Schritte

| Schritt | Was er festlegt |
|---|---|
| 1. Quelle (Source) | Name, Bezeichner (für die Kommandozeile; leer: aus dem Namen gebildet), Beschreibung; den Quelltyp (CSV, XML, RSS/Atom, JSON, Tabelle XLSX/ODS oder ein Handler von 1.x); woher die Daten kommen (eine hochgeladene Datei, eine `http(s)`-Adresse, die bei jedem Lauf gelesen wird, oder eine Datei in einem erlaubten Verzeichnis auf dem Server); die Optionen des Quelltyps |
| 2. Verarbeitung (Processing) | Die Zuordnung ohne Programmierung oder einen anderen Handler mit seinen Optionen; Elternknoten, Sektion und Sprache neuer Inhalte; den Benutzer, als der ein Lauf läuft; Datensätze pro Abschnitt; eine Grenze pro Lauf; ob bestehende Inhalte aktualisiert werden |
| 3. Zuordnung (Mapping) | Die Inhaltsklasse, wie ein Datensatz sein Objekt findet, wohin neue Objekte kommen, und eine Zeile pro Attribut (nur für die Zuordnung ohne Programmierung) |
| 4. Vorschau (Preview) | Eine Stichprobe der Quelle und einen Probelauf der ersten Datensätze (nicht für Handler von 1.x) |
| 5. Zeitplan (Schedule) | Nie, einmal, alle N Minuten, stündlich, täglich, wöchentlich, monatlich oder ein Cron-Ausdruck, in einer Zeitzone, mit E-Mail-Adressen für Fehler; die nächsten fünf Laufzeiten werden gezeigt |
| 6. Prüfung (Review and save) | Die Zusammenfassung, was noch fehlt, und ob der Import aktiviert ist; **Save** oder **Save and run**, um sofort einen Lauf zu starten |

Ein neuer Import wird angelegt, wenn der Schritt Quelle zum ersten Mal gespeichert wird, und zwar deaktiviert.
Aktivieren lässt er sich im Schritt Prüfung, sobald nichts mehr fehlt.

## Das Zuordnungsraster

Wählen Sie die Inhaltsklasse und speichern Sie den Schritt, um ihre Attribute zu sehen. Legen Sie dann fest, wie
ein Datensatz sein Objekt findet: über eine Remote-ID aus dem Datensatz, etwa `product-{sku}`, oder über den Wert
eines Attributs. Jede Zeile des Rasters ist ein Attribut:

| Spalte | Bedeutung |
|---|---|
| Source field | Ein Feld des Datensatzes, etwa `title` oder `author.name`; die Felder der Stichprobe stehen über dem Raster |
| Transforms | Eine Kette von Umwandlungen als eine Zeile Text |
| Default | Der Wert, wenn der Quellwert leer ist |
| Required | Der Datensatz schlägt fehl, wenn der Wert dann noch leer ist |

Eine Kette von Umwandlungen wird mit `|` zwischen den Schritten und den Argumenten in Klammern geschrieben:

```text
trim | date(format=d.m.Y, timezone=Europe/Berlin)
date(format=excel)
map(table={"D":"Germany","A":"Austria"}, otherwise=Other)
replace(search=",", replace=.)
```

Ein Wert mit Komma, Klammer oder `|` steht in doppelten Anführungszeichen; eine Tabelle oder eine Liste wird als
JSON geschrieben. `date(format=excel)` liest die Datumszahlen einer Tabelle, etwa `45231` für den 1. November 2023.

**Auto-match by name** füllt die Zeilen, die noch kein Quellfeld haben: Es vergleicht die Namen der Attribute mit
den Feldnamen der Stichprobe, ohne Groß- und Kleinschreibung und Akzente, und ergänzt `richtext`, `date`, `image`
oder `bool`, wo der Datentyp es braucht. Es verwendet keinen externen Dienst. Prüfen Sie die gefüllten Zeilen und
speichern Sie dann.

## Die Vorschau

Die Vorschau läuft in der Anfrage; sie startet keinen Job und speichert keinen Lauf. Sie zeigt:

- die ersten 20 Datensätze der Quelle als Tabelle, mit dem, was die Quelle erkannt hat (Trennzeichen und
  Zeichenkodierung einer CSV-Datei, die Blätter einer Tabelle);
- einen Probelauf der ersten 20 Datensätze (`[RunSettings] DryRunRows`): für jeden Datensatz, was geschehen würde
  (angelegt, aktualisiert, unverändert, übersprungen, fehlgeschlagen), und die Felder, die sich ändern würden, mit
  dem aktuellen und dem neuen Wert.

Nichts wird veröffentlicht, und kein Bild und keine Datei wird heruntergeladen.

## Kopieren, deaktivieren und löschen

Neben jedem Import in der Liste finden Benutzer mit **Manage imports** die Schaltfläche **Edit** und unter **More**:

- **Duplicate**: eine deaktivierte Kopie mit Quelle, Verarbeitung und Zuordnung, ohne Zeitpläne und Läufe, im
  Editor geöffnet;
- **Enable** oder **Disable**: schaltet den Import ein oder aus;
- **Delete**: entfernt den Import mit seiner Zuordnung und seinen Zeitplänen nach einer Rückfrage. Seine Läufe
  bleiben im Verlauf. Ein Import, der gerade läuft, lässt sich nicht löschen.

Um Importe von einer Staging-Installation auf die Live-Installation zu übertragen, exportieren Sie sie auf der Seite
**Settings** des Reiters Importe als JSON und importieren die Datei dort. Hochgeladene Quelldateien gehören nicht zu
einem Export; laden Sie sie im Schritt Quelle erneut hoch.

Weiter: [Importe und Läufe](importe-und-laeufe.md).
