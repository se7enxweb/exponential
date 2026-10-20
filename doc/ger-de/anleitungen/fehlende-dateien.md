# Fehlende Dateien und Bildvarianten

Diese Anleitung zeigt, wie Sie Inhalte finden, deren Dateien im Speicher fehlen: ein Bild, dessen Original nicht mehr
in `var/<site>/storage` liegt, eine Datei oder ein Medienobjekt, dessen Upload verloren gegangen ist, und Bilder, deren
Varianten fehlen oder mit anderen Einstellungen in `image.ini` erzeugt wurden. Sie richtet sich an Administratoren, die
den Speicher zwischen Servern verschieben, Sicherungen wiederherstellen oder Festplatten aufräumen, und an alle, die
auf der Site defekte Bilder sehen.

[Anleitungen](README.md) · Siehe auch: [Die Inhaltsprüfungen](../../eng-us/features/6.0/content-checks.md#missing-files),
[Die Upgrade-Prüfung](upgrade-pruefung.md)

## Kurz gesagt

- Die Prüfung ist ein **Ziel** von **Setup > Ungültige Relationen**: Markieren Sie **Fehlende Dateien in
  var/<site>/storage** und klicken Sie auf **Prüfen**. Zusammen mit den anderen Zielen markiert, zeigt die Seite die
  ungültigen Relationen und die fehlenden Dateien; allein markiert, nur die fehlenden Dateien.
- Jedes dateibasierte Feld wird geprüft (Bildoriginale und ihre Varianten, Dateien, Medien und die Datentypen, die
  eine Extension hinzufügt), in der veröffentlichten Version und in Entwürfen, über den Cluster-Dateihandler, sodass
  auch das DFS eines Clusters berücksichtigt wird.
- Jede Zeile nennt das Objekt (mit seinem Knoten), das Feld, die Version, den **erwarteten Pfad**, den Zeitpunkt der
  letzten Änderung der Version und das Problem: **Datei fehlt**, **Variante fehlt**, **Variante veraltet** oder
  **Variante nie erstellt**.
- Dieselbe Liste auf der Kommandozeile: `./console exp:content-check --missing-files --node=<node id>`.
- Die Prüfung selbst ändert nichts; die Behebungen (Abschnitt 6) zeigen immer zuerst eine Vorschau.

## 1. Die Prüfung öffnen

1. Melden Sie sich in der Administration an und öffnen Sie **Setup > Ungültige Relationen** (oder **Fortgeschritten >
   Ungültige Relationen im Teilbaum** im Kontextmenü eines Knotens im Inhaltsbaum).
2. Behalten Sie unter **Startort** den obersten Knoten bei, klicken Sie auf **Ganzer Baum**, **Websites** oder
   **Medien** oder auf eine der Sites und Sektionen in der Zeile darunter, oder geben Sie die Knoten-ID des zu prüfenden
   Teils des Baums an.
3. Markieren Sie unter **Ziele** den Eintrag **Fehlende Dateien in var/site/storage** (der Name Ihres
   Speicherverzeichnisses wird angezeigt). Heben Sie die Markierung der anderen Ziele auf, wenn Sie nur die Dateien
   sehen möchten.
4. Klicken Sie auf **Prüfen**.

Sobald das Ziel markiert ist, erscheinen zwei weitere Filter:

- **Fehlende Dateien: Probleme**: **Datei fehlt**, **Variante fehlt** und **Variante veraltet** sind markiert;
  **Variante nie erstellt** ist es nicht, denn eine Variante wird erst erzeugt, wenn ein Bild zum ersten Mal in ihr
  angezeigt wird, sodass eine fehlende Variante normal ist.
- **Fehlende Dateien: Versionen**: die **Veröffentlichte Version** und **Entwürfe**.

## 2. Eine Zeile lesen

| Spalte | Was sie angibt |
|---|---|
| Objekt | der Name des Objekts, verlinkt mit seinem Knoten, mit Objekt- und Knoten-ID |
| Feld | das Feld (und seine Sprache); darunter der ursprüngliche Dateiname |
| Version | die Versionsnummer, veröffentlicht oder Entwurf |
| Erwarteter Pfad | wo die Datei laut Feld liegt, unterhalb von `var/<site>/storage`; bei einer Variante die Varianten und ihr Zustand |
| Zuletzt geändert | wann die Version zuletzt geändert wurde; bei einer Variante auch der Zeitpunkt der Variantendatei |
| Zustand | **Datei fehlt** (rot), **Variante fehlt** oder **Variante veraltet** (gelb), **Variante nie erstellt** (blau) |

Ein Bild, dessen Original fehlt, zeigt eine einzige Zeile, **Datei fehlt**: Seine Varianten lassen sich ohne das
Original nicht erzeugen und werden daher nicht einzeln aufgeführt.

**Variante veraltet** bedeutet eines von drei Dingen: Die Variante wurde mit anderen Einstellungen erzeugt, als
`image.ini` jetzt enthält (ihr Schlüssel weicht ab), sie wurde erzeugt, bevor die Bildvarianten zuletzt für abgelaufen
erklärt wurden, oder sie ist älter als das Original.

## 3. Durch einen großen Baum blättern

Die Liste hat eine Gesamtzahl und Seiten, wie die anderen Listen: „1 bis 10 von 446“ in ihrem Kopf, die
Seitenlinks darunter und **Pro Seite**, um 10, 25 oder 50 Zeilen zu wählen. Zum Zählen wird jedes Objekt unterhalb
des Startorts einmal durchlaufen (höchstens 50.000, `ListLimit` in `[MissingFileSettings]` von `contentcheck.ini`);
die Ergebnisse werden eine Minute lang aufbewahrt (`ListCacheTTL`), sodass die nächsten Seiten sofort erscheinen. Ihre
Seiten stehen als `(file_offset)` in der Adresse, sodass sie die Seiten der ungültigen Relationen darüber nicht
verschieben.

## 4. Dasselbe in einer Shell

```bash
./console exp:content-check --missing-files --node=2 --allow-root-user
```

```
Missing files and image aliases below node 2: 3 in 4 objects checked
  13902-2      object 41518    published alias_missing var/site/storage/images/.../w532-image-one_small.png (small, medium)
  13905-2      object 41519    published missing       var/site/storage/original/text/a9d2344afd0d6ea0134cef55f8384827.txt
  13908-1      object 41520    published missing       var/site/storage/images/.../w532-image-two.png
```

Die erste Spalte ist die Feldversion (`<attribute id>-<version>`). `--problems=missing,alias_missing` und
`--versions=published` filtern wie die Seite; `--json` gibt JSON aus. Der Exit-Code ist 1, wenn etwas gefunden wird.

Damit der Cronjob-Teil `contentcheck` die Probleme jede Nacht zählt, setzen Sie `CronjobReport=enabled` in
`[MissingFileSettings]` von `settings/override/contentcheck.ini.append.php`; er schreibt eine Warnung in die Ausgabe
des Cronjobs, wenn es welche gibt.

## 5. Der Dateidatentyp einer Extension

Ein Datentyp, der seine Datei in einer Tabelle mit den Spalten von `ezbinaryfile` ablegt, benötigt zwei Zeilen:

```ini
[MissingFileSettings]
Handlers[mydatatype]=expContentCheckFileTable
Tables[mydatatype]=mytable
```

Jeder andere Datentyp fügt eine Klasse hinzu, die `expContentCheckFileHandler` implementiert (`files()`,
`removeReference()`, `hasAliases()`), und nennt sie in `Handlers[]`.

## 6. Gefundenes beheben

Mit der Richtlinie `contentcheck/fixfiles` (und `content/edit` für das Objekt) hat jede Zeile Schaltflächen, und die
Liste hat Kontrollkästchen und **Mit den markierten Zeilen**. Was Sie auch wählen, die nächste Seite ist eine
**Vorschau**, die Zeile für Zeile angibt, was geschehen wird oder warum es nicht möglich ist; nichts geschieht, bevor
Sie auf **Bestätigen** klicken.

| Sie sehen | Tun Sie dies | Was geschieht |
|---|---|---|
| **Variante fehlt** oder **Variante veraltet** | **Neu erzeugen** | die Varianten werden mit den aktuellen Einstellungen von `image.ini` neu aus dem Original erzeugt |
| **Datei fehlt**, und eine ältere Version hat noch eine Datei | **Wiederherstellen** | die Datei dieser Version wird an den erwarteten Pfad kopiert (die Vorschau nennt die Version und die Datei) |
| **Datei fehlt**, und Sie haben die Datei | **Neu hochladen** | wählen Sie die Datei in der Vorschau; sie muss dieselbe Endung haben, und ein Bild muss ein Bild sein |
| **Datei fehlt**, und die Datei ist endgültig verloren | **Verweis entfernen** | das Feld wird in dieser Version geleert; es wird keine Datei gelöscht |
| Sie sind unsicher | **Zur Prüfung markieren** | die Zeile zeigt **Zur Prüfung markiert**, bis sie behoben ist |

1. Markieren Sie die Zeilen (oder das Kästchen im Kopf für die ganze Seite; **Alle auf dieser Seite auswählen** ohne
   JavaScript).
2. Wählen Sie die Aktion unter **Mit den markierten Zeilen** und klicken Sie auf **Vorschau**. Bei mehreren Seiten
   übernimmt **Vorschau für alle Ergebnisse unterhalb dieses Knotens** die Zeilen aller Seiten.
3. Lesen Sie die Vorschau und klicken Sie auf **Für N Feldversionen bestätigen**.
4. Große Auswahlen laufen in Stapeln zu je 10: Die Seite zeigt den Fortschritt und fährt von selbst fort; **Hier
   anhalten** kehrt zur Liste zurück.
5. Die Liste gibt an, wie viele Zeilen erledigt wurden und warum einzelne nicht erledigt wurden.

Jede erledigte Zeile wird im Audit festgehalten (**Setup > Audit-Protokoll**, Kanal content):
`content.file.alias.regenerate`, `content.file.restore`, `content.file.replace`, `content.file.reference.remove`,
`content.file.review.mark`.

In einer Shell immer zuerst mit einem Probelauf:

```bash
./console exp:content-check --missing-files --node=2 --fix=regenerate --dry-run --allow-root-user
./console exp:content-check --fix=restore --items=13905-2 --allow-root-user
```

```
Fix restore: 1 field versions planned, 1 allowed, 1 done, 0 not done
  13905-2      allowed    var/site/storage/original/text/a9d2344afd0d6ea0134cef55f8384827.txt  (from version 1: var/site/storage/original/text/aaa078c1d252cb083b3e94fde402d21d.txt)
```
