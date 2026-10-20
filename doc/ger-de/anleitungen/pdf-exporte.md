# PDF-Exporte: ein PDF-Dokument aus einem Teil des Inhaltsbaums

Diese Anleitung erklärt die Seiten für PDF-Exporte in der Administrationsoberfläche (Setup > PDF-Export, `/pdf/list`): was ein
PDF-Export ist, was jeder Teil der Liste und des Bearbeitungsformulars aussagt, wie Sie einen Export anlegen, herunterladen, neu erzeugen und entfernen
und was zu tun ist, wenn ein PDF leer ist oder sich nicht erzeugen lässt.

Sie richtet sich an Administratoren, die Inhalte als PDF weitergeben (ein Handbuch, ein Produktblatt, eine Rezeptsammlung). Jede
Zahl und jede Meldung unten wurde am Code geprüft (`kernel/private/classes/views/pdf/list.php`,
`kernel/private/classes/views/pdf/edit.php`, `kernel/classes/pdfexport/*.php`, `kernel/classes/ezpdfexport.php`),
und zwar am 6. Oktober 2026 auf dem Demonstrationsserver, unter Apache mit PHP-FPM und unter FrankenPHP.

[Anleitungen](README.md) · Siehe auch: [Templates und Design](templates-und-design.md),
[Blättern, Sortieren und Seitengrößen](../../eng-us/features/6.0/admin-list-paging.md), [Sichere Weiterleitungen](../../eng-us/features/6.0/safe-redirects.md)

## Kurz gesagt

- Ein **PDF-Export** erzeugt ein PDF aus einem **Quellknoten**: sein Name als erste Überschrift, dann seine Attribute. Ein
  **Baum**-Export fügt die Knoten darunter hinzu, auf allen Ebenen, aber nur die der **Klassen**, die Sie ankreuzen.
- **Einmal erzeugt**: Das PDF wird beim Speichern des Exports geschrieben und erneut mit **Neu erzeugen**; die Datei wird in
  `var/<site>/storage/pdf/<file name>` aufbewahrt und dort unter einer öffentlichen Adresse ausgeliefert. Schnell, aber sie zeigt die Inhalte von damals.
- **Bei Bedarf erzeugt**: Das PDF wird bei jedem Herunterladen neu erstellt. Immer aktuell; ein großer Baum braucht jedes Mal
  eine Weile.
- Die Liste beginnt mit Kennzahlen, einer Suche, Filtern und einer Sortierung, dann folgt eine Karte pro Export mit **Herunterladen**,
  **Neu erzeugen** und **Bearbeiten**. **Ausgewählte entfernen** fragt zuerst nach und nennt die gespeicherten Dateien, die mit gelöscht werden.

## 1. Die Seite öffnen

Setup > PDF-Export, oder `/pdf/list` im Administrations-Siteaccess. Beide Seiten erfordern die Richtlinie `pdf/edit` (Administrator hat
sie). Downloads laufen über `/pdf/edit/<id>/generate`, das dieselbe Richtlinie erfordert.

Ein gespeichertes PDF ist öffentlich: Der Webserver liefert `var/<site>/storage/pdf/<file name>` an jeden aus, angemeldet oder nicht, und
die Karte zeigt diese **Öffentliche Adresse** zum Weitergeben oder Verlinken. Die Regel ist
`RewriteRule ^var/([^/]+/)?storage/pdf/[A-Za-z0-9][A-Za-z0-9._-]*\.pdf$ - [L]` in `.htaccess_root` und
`.htaccess_root_static` sowie eine `location` im nginx-Beispiel. Nichts anderes unterhalb des Speicherverzeichnisses
wird öffentlich: Andere Uploads bleiben hinter `content/download` und dessen Berechtigungen. Eine Installation mit eigener
`.htaccess` ergänzt diese eine Zeile nach der Regel für `storage/images`; ein Server, der solche Regeln in seiner eigenen
Konfiguration hält (eine vhost-Datei, ein vorgeschaltetes nginx, die statischen Pfade von Velocity), braucht denselben Pfad dort. Geben Sie einem
Export keinen Dateinamen, der nicht erraten werden soll, und verwenden Sie für Inhalte, die nicht für alle bestimmt sind, einen Export, der
**bei Bedarf erzeugt** wird: Er wird nur über `/pdf/edit/<id>/generate` heruntergeladen, von Benutzern mit der Richtlinie `pdf/edit`. Vor
6.0.15 verlinkte die Liste die Datei unter dieser Adresse, aber der Webserver verweigerte sie (404).

## 2. Die Liste

| Teil | Was er aussagt |
|---|---|
| Kennzahlen | Exporte; einmal erzeugt; bei Bedarf erzeugt; gespeicherte Dateien; ihre Gesamtgröße; Exporte, die Aufmerksamkeit brauchen |
| Unfertige Entwürfe | Neue Exporte, die begonnen und nie gespeichert wurden. Sie werden entfernt, sobald `content.ini [PDFExportSettings] DraftTimeout` (7200 Sekunden) verstrichen ist und jemand einen neuen Export beginnt |
| Export finden | Titel, Dateiname, ID, Quellknoten oder Klasse; jedes Wort muss passen. Die Suche ist Teil der Adresse (`?search=`) |
| Anzeigen | Alle, Einmal erzeugt, Bei Bedarf, Mit gespeicherter Datei, Brauchen Aufmerksamkeit, jeweils mit ihrer Anzahl |
| Sortieren nach | Titel, Geändert, Erzeugt (Datum der gespeicherten Datei), Dateigröße, ID; ein zweiter Klick kehrt die Reihenfolge um |
| Pro Seite | Die Größen aus `admininterface.ini [PaginationSettings]` (`ItemsPerPage[pdf/list]`, `ItemsPerPageList_pdf_list[]`); Ihre Wahl wird gespeichert |

Jede Karte zeigt:

- **Kennzeichen**: Einmal erzeugt oder Bei Bedarf; Datei bereit oder Keine Datei; Quelle fehlt; Im Editor geöffnet von einem Benutzer, der
  das Formular geöffnet hat.
- **Quellknoten** mit Klasse und Knoten-ID, oder „Knoten N (existiert nicht mehr)“.
- **Enthält**: nur den Quellknoten oder den Quellknoten und die Knoten darunter von den aufgeführten Klassen.
- **Gespeicherte Datei** mit Größe und Datum oder „nicht erzeugt“; bei einem Export, der bei Bedarf erzeugt wird, den **Name beim Herunterladen**.
- **Titelseite** und **Geändert** (wann und von wem).
- **Warnungen**: kein Quellknoten gewählt; der Quellknoten ist verschwunden; die Datei nicht erzeugt oder entfernt; ein Dateiname, der
  nicht verwendet werden kann; ein Baum ohne Klassen (nur der Quellknoten wird exportiert); Klassen, die es nicht mehr gibt.

**Herunterladen** sendet die gespeicherte Datei oder erstellt das PDF sofort bei einem Export, der bei Bedarf erzeugt wird. **Neu erzeugen** schreibt die
gespeicherte Datei neu aus den aktuellen Inhalten und nennt ihre Größe; der Knopf ist ausgegraut, mit Angabe des Grundes, wenn der
Quellknoten verschwunden ist. Eine neue Datei wird zuerst unter einem temporären Namen geschrieben, sodass ein fehlgeschlagener Lauf die alte Datei unangetastet lässt.

## 3. Einen Export anlegen oder bearbeiten

**Neuer PDF-Export** öffnet das Formular unter `/pdf/edit/<id>`; bis Sie OK drücken, ist es ein Entwurf, den niemand sonst in der Liste sieht.

1. **Titel**: der Name in der Liste. Er wird nicht ins PDF gedruckt.
2. **Inhalt**: Wählen Sie den **Quellknoten** mit **Durchsuchen** (was Sie eingegeben haben, bleibt erhalten). Der Bereich zeigt seinen Pfad,
   seine Klasse, seine Sektion und die Anzahl seiner Kinder oder sagt, dass der Knoten nicht mehr existiert. Wählen Sie **Knoten** (nur die Quelle) oder
   **Baum**, und kreuzen Sie für einen Baum die aufzunehmenden Klassen an. Ein Knoten einer nicht angekreuzten Klasse bleibt *mit allem
   darunter* weg; kreuzen Sie also neben den Artikeln auch die Ordner an, die die Inhalte enthalten.
3. **Startseite**: eine optionale erste Seite mit einer großen und einer kleineren Textzeile.
4. **Fußzeilentext**: die Zeile neben der Seitenzahl. Leer verwendet den Standardtext; ohne Häkchen gibt es keinen Text.
5. **Art des Exports** und **Dateiname**: Einmalig erstellen oder bei Bedarf. Der Dateiname besteht aus Buchstaben, Ziffern, `.`, `_` und
   `-`, bis zu 100 Zeichen; `.pdf` wird angehängt, wenn es fehlt. Der Name eines gespeicherten Exports darf von keinem anderen
   gespeicherten Export verwendet werden. Bei Bedarf ist es der Name, unter dem der Download angeboten wird.

**OK** prüft das Formular. Ein Fehler wird oben mit einem Link zu seinem Feld aufgeführt und unter dem Feld angezeigt:

| Meldung | Behebung |
|---|---|
| Geben Sie dem Export einen Titel. | Geben Sie einen Titel ein |
| Wählen Sie den Quellknoten mit „Durchsuchen“. / Der gewählte Quellknoten existiert nicht mehr. | Durchsuchen und einen Knoten wählen |
| Wählen Sie mindestens eine Klasse, die unter dem Quellknoten aufgenommen wird, oder exportieren Sie nur den Quellknoten. | Klassen ankreuzen oder Knoten wählen |
| Der Dateiname muss ein reiner Name sein, ohne Ordner, „..“ oder führenden Punkt. / Verwenden Sie im Dateinamen nur Buchstaben, Ziffern, Punkte, Bindestriche und Unterstriche. | Die Datei umbenennen |
| Ein anderer gespeicherter Export schreibt bereits eine Datei dieses Namens. | Einen anderen Namen wählen |

Ist alles richtig, wird der Export gespeichert und, wenn er einmal erzeugt wird, seine Datei geschrieben; die Liste nennt dann ihre
Größe oder den Grund, warum sie nicht geschrieben werden konnte. Eine Änderung des Namens oder der Wechsel zu „bei Bedarf“ löscht die alte Datei.

**Abbrechen** verwirft die Änderungen und kehrt zu der Seite zurück, von der aus das Formular geöffnet wurde, sonst zur Liste. Hat
jemand anderes den Export geöffnet, nennt das Formular, wer und seit wann; Speichern ersetzt dessen nicht gespeicherte Änderungen.

## 4. Exporte entfernen

Kreuzen Sie die Karten an und drücken Sie **Ausgewählte entfernen**. Die Bestätigung listet jeden Export mit seinem Quellknoten und der Datei auf,
die mit ihm gelöscht wird (Name, Größe, Datum), und sagt, dass die Inhalte unberührt bleiben. **Entfernen** entfernt den
Export, einen Entwurf, den jemand geöffnet hat, und die gespeicherte Datei; **Abbrechen** ändert nichts. Die Liste nennt danach, was
entfernt wurde. Ein Neuladen entfernt nie zweimal.

## 5. Probleme

| Symptom | Ursache und Behebung |
|---|---|
| Das PDF enthält nur die erste Inhaltsseite | Ein Baum ohne Klassen, oder die Ordner zwischen der Quelle und den Artikeln sind nicht angekreuzt. Kreuzen Sie sie an |
| „Sein Quellknoten existiert nicht mehr“ | Der Knoten wurde entfernt oder in den Papierkorb verschoben. Bearbeiten Sie den Export und wählen Sie mit Durchsuchen einen anderen Knoten, oder entfernen Sie ihn |
| „Die PDF-Templates haben keine Datei geschrieben“ | Ein Override von `node/view/pdf.tpl` oder `node/view/execute_pdf.tpl` ist fehlgeschlagen. Siehe `var/log/error.log` |
| Ein gespeichertes PDF zeigt alte Inhalte | Einmal erzeugt behält die Datei; drücken Sie Neu erzeugen oder wechseln Sie zu „bei Bedarf“ |
| Ein großer Baum läuft bei Bedarf in eine Zeitüberschreitung | Stellen Sie ihn auf Einmalig erstellen um und erzeugen Sie ihn außerhalb der Stoßzeiten neu |
| Ein Export mit einer Tabelle antwortet unter PHP 8 mit 500 (`TypeError` in `lib/ezpdf/classes/ezpdf.php`) | Behoben am 12.10.2026; aktualisieren Sie auf den aktuellen Code von 6.0.15 ([PDF-Export](../../eng-us/features/6.0/pdf-export.md#what-was-repaired)) |

## Für Entwickler

- `expPDFExportFile`: Regeln für Dateinamen (`normalizeName()`, `isSafeName()`, `nameProblem()`), `path()`, `facts()`,
  `remove()` und `send()` (in Blöcken, Sitzung geschlossen, Puffer verworfen; der Aufrufer ruft `cleanExit()` außerhalb jedes catch auf).
- `expPDFExportGenerator`: `render()` (die beiden Templates, wie bisher, mit denselben Variablen), `generateFile()`,
  `stream()`, `problemOf()`. `generatePDF()` bleibt erhalten und ruft sie auf.
- `expPDFExportInfo`: die Karte (`infoOf()`), Kennzahlen, Suche, Filter, Sortierung, unfertige Entwürfe und `remove()`.
- `expPDFExportForm::read()`: das Formular, wobei die Prüfungen, die die Datenbank brauchen, hineingereicht werden.
- Tests ohne Datenbank: `php vendor/bin/phpunit tests/tests/kernel/classes/expPDFExportTest.php`.
- Velocity: Die Klassen sind Kernel-Klassen; stellen Sie sie mit `exp:velocity deploy --kernel` bereit.
