---
title: "Lektion 3.3: CSV-Import und -Export"
audience: trainer, admin
category: training
order: 33
summary: Die Teilnehmenden bereiten eine CSV-Datei mit Testabonnenten unter example.invalid vor, importieren sie mit der Spaltenzuordnung (Vorschau, Probelauf, Einwilligungsquelle) und exportieren die Liste wieder. 45 Minuten.
keywords: newsletter mit cjw newsletter, csv-import, csv-export, spaltenzuordnung, einwilligungsquelle, probelauf, abonnentenexport, trennzeichen, importliste, newsletters with cjw newsletter, csv import, csv export, column mapping, consent source, dry run, subscriber export, delimiter, import list
type: help
---
# Lektion 3.3: CSV-Import und -Export

Organisationen beginnen selten mit einer leeren Liste: Adressen kommen aus einem alten System, von einer Messe oder
aus einem anderen Werkzeug. Der Import holt sie herein; der Export bringt sie für einen Bericht oder einen Umzug
hinaus. In dieser Lektion importieren die Teilnehmenden eine kleine Datei mit Testabonnenten, exportieren sie wieder
und lernen, warum jeder Import eine dokumentierte Einwilligungsquelle braucht.

> **Hinweis**
> Verwenden Sie nur erfundene Personen mit Adressen unter `example.invalid`. Bringen Sie niemals eine echte
> Abonnentendatei in eine Schulungsinstallation. Ein Import mit Spaltenzuordnung sendet keine Bestätigungsmail.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Newsletter mit CJW Newsletter](../README.md) |
| Modul | [Modul 3: Abonnenten](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Administratoren und erfahrene Redakteure |
| Voraussetzungen | [Lektion 3.2](benutzer-und-abonnements-verwalten.md); ein Texteditor, um eine CSV-Datei zu schreiben |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine CSV-Datei mit einer Kopfzeile in den erwarteten Spalten vorbereiten;
- sie mit **CSV-Import mit Spaltenzuordnung** importieren: hochladen, zuordnen, Vorschau, Probelauf, Import;
- die Einwilligungsquelle erklären und warum ein Import keine Double-Opt-in-Mail sendet;
- die Zeilen nennen, die ein Import überspringt, und warum;
- die Abonnenten einer Liste als CSV exportieren.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Importieren und Exportieren](../../../ersteller/website-ersteller/newsletter-listen-und-abonnements.md#importieren-und-exportieren)
und die Import-Anleitung der Erweiterung (`importexport.md`, verlinkt von der
[Funktionsseite](../../../../funktionen/6.0/erweiterungen/cjw_newsletter.md#die-funktionen-von-420)).

1. **Zwei Importe** (3 Minuten). Der Import aus 4.1 (`subscription_list_csvimport`) liest Spalten in fester
   Reihenfolge. Der Import mit Spaltenzuordnung (4.2) lässt Sie wählen, welche Spalte welches Feld füllt, zeigt eine
   Vorschau und macht einen Probelauf. Lehren Sie den Import mit Zuordnung.
2. **Die Schritte** (5 Minuten). Hochladen (Datei, Trennzeichen, Zeichenkodierung, Kopfzeile, Einwilligungsquelle,
   Notiz), die Spalten zuordnen (aus den Spaltennamen erraten; genau eine muss die E-Mail-Adresse sein), Vorschau der
   ersten zehn Zeilen (neu, Aktualisierung, überspringen), Probelauf, Import.
3. **Einwilligung** (4 Minuten). Die Einwilligungsquelle ist für den echten Import Pflicht. Sie dokumentiert, wo die
   Personen zugestimmt haben. Es wird keine Double-Opt-in-Mail gesendet, weil die Einwilligung dort gegeben wurde, wo
   die Adressen gesammelt wurden: Sie müssen sie nachweisen können.
4. **Was übersprungen wird** (3 Minuten). Ungültige oder wiederholte Adressen, Adressen auf der Unterdrückungsliste
   oder der Sperrliste, Personen, die den Newsletter abgeschaltet oder sich vorher abgemeldet haben, und Hard-Bounces.
   Ein Import meldet niemanden wieder an, der gegangen ist.

### Häufige Fragen

**Welches Trennzeichen?** Semikolon, Komma, Tabulator oder senkrechter Strich. Der mitgelieferte Standardwert des
Imports aus 4.1 ist `;` (`DefaultCsvDelimiter`).

**Verdoppelt ein Import vorhandene Benutzer?** Nein; eine vorhandene Adresse wird aktualisiert, niemals verdoppelt.

**Wo wird die Datei aufbewahrt?** Unter dem Verzeichnis `var` der Installation, im Importordner des Newsletters; nur
Dateien aus diesem Ordner werden gelesen.

**Kann ich den Import aus der Shell ausführen?** Ja: `ext:cjw_newsletter:import --import-id=<id> --dry-run`, dann ohne
`--dry-run` ([Spezifikation](../../../../spezifikationen/6.0/cjw_newsletter.md#konsolenbefehle)).

**Kann man den Export gefahrlos in einer Tabellenkalkulation öffnen?** Zellen, die mit `=`, `+`, `-` oder `@`
beginnen, werden entschärft, damit sie nicht als Formeln ausgeführt werden.

### Stolperfallen

- **Keine Kopfzeile.** Ohne Namen kann die Zuordnung nichts erraten; sagen Sie dem Formular, ob die erste Zeile Namen
  enthält.
- **Falsche Zeichenkodierung.** Namen mit Akzenten gehen kaputt, wenn die Kodierung falsch ist. Speichern Sie die
  Datei als UTF-8.
- **Eine leere Einwilligungsquelle.** Der Probelauf funktioniert, der Import verweigert. Vereinbaren Sie im Kurs eine
  Formulierung, zum Beispiel `Training course, made-up data`.
- **Exporte aufbewahren.** Ein Export sind personenbezogene Daten. Löschen Sie ihn nach der Übung.

## Praktische Übung

Verwenden Sie die Schulungsinstallation. Ersetzen Sie `<name>` durch Ihren Vornamen.

1. Schreiben Sie in einem Texteditor diese Datei und speichern Sie sie als `subscribers-<name>.csv` in UTF-8:

   ```text
   email;first_name;last_name
   dora-<name>@example.invalid;Dora;Test
   emil-<name>@example.invalid;Emil;Test
   fay-<name>@example.invalid;Fay;Test
   dora-<name>@example.invalid;Dora;Repeated
   not-an-address;Nobody;Test
   ```

2. Öffnen Sie die Seite Ihrer Liste, klicken Sie auf **Abonnements**, dann auf **CSV Datei importieren**.
3. Wählen Sie **CSV-Import mit Spaltenzuordnung**.
4. Wählen Sie Ihre Datei, das Semikolon als Trennzeichen, UTF-8, und haken Sie an, dass die erste Zeile die
   Spaltennamen enthält.
5. Geben Sie als Einwilligungsquelle `Training course, made-up data` ein. Hochladen.
6. Prüfen Sie die Zuordnung: `email` muss die E-Mail-Adresse sein; `first_name` und `last_name` die Namen.
7. Lesen Sie die Vorschau: Welche Zeilen sind neu, welche werden übersprungen, und warum?
8. Führen Sie den Probelauf aus und lesen Sie seinen Bericht.
9. Führen Sie den Import aus und lesen Sie den Bericht.
10. Suchen Sie in den Abonnements Ihrer Liste Dora, Emil und Fay mit ihrem Status.
11. Öffnen Sie den Export der Liste (die Seite des CSV-Exports verlinkt auf den Abonnentenexport), behalten Sie die
    Status bestätigt und angenommen, sehen Sie sich die Vorschau an und laden Sie die CSV herunter.
12. Öffnen Sie die CSV, prüfen Sie die Zeilen und löschen Sie dann die heruntergeladene Datei.

## Erwartetes Ergebnis

- Die Vorschau und die Berichte zeigen drei neue und zwei übersprungene Zeilen („In der Datei wiederholt“ und „Keine
  gültige E-Mail-Adresse“).
- Dora, Emil und Fay sind angenommene Abonnenten der Liste der Person. Für sie wurde keine Bestätigungsmail-Datei
  geschrieben.
- Der Import steht unter **Importe** im linken Menü.
- Die Exportdatei hat die Abonnenten der Liste aufgeführt und wurde danach gelöscht.
- Der Trainer prüft die Seite **Importe** und die Abonnements einer Liste.

## Quiz

1. Welchem Feld muss genau eine Spalte zugeordnet sein?
2. Warum sendet ein Import mit Spaltenzuordnung keine Double-Opt-in-Mail?
3. Welche dieser Zeilen wird importiert?
   A. eine Adresse auf der Sperrliste B. eine Person, die sich vorher abgemeldet hat C. eine neue gültige Adresse
   D. eine Adresse mit Hard-Bounce
4. Was ändert der Probelauf in der Datenbank?
5. Warum sollten Sie einen Export nach Gebrauch löschen?

<details>
<summary>Antworten</summary>

1. Der E-Mail-Adresse (Import-Anleitung, verlinkt von der [Funktionsseite](../../../../funktionen/6.0/erweiterungen/cjw_newsletter.md#die-funktionen-von-420)).
2. Die Einwilligung wurde dort gegeben, wo die Adressen gesammelt wurden, und die Einwilligungsquelle dokumentiert
   das; Sie müssen sie nachweisen können (Import-Anleitung).
3. **C.** Die anderen werden übersprungen (Import-Anleitung).
4. Nichts; er prüft und zählt jede Zeile.
5. Er enthält personenbezogene Daten; bewahren Sie Exporte nur so lange wie nötig auf ([Einwilligung](../../../ersteller/website-ersteller/newsletter-listen-und-abonnements.md#einwilligung)).

</details>

## Weiterführende Informationen

- [Newsletter-Listen und Abonnements](../../../ersteller/website-ersteller/newsletter-listen-und-abonnements.md#importieren-und-exportieren):
  Importieren und Exportieren.
- [cjw_newsletter: Newsletter in der Administration](../../../../funktionen/6.0/erweiterungen/cjw_newsletter.md#prüfung-von-sicherheit-und-robustheit-4115):
  wie Import und Export abgesichert wurden.
- [Compliance und DSGVO](../../../hosting/sicherheit/compliance-und-dsgvo.md): personenbezogene Daten aufbewahren.

## Verwandte Seiten

- [Modul 3: Abonnenten](README.md)
- [Newsletter mit CJW Newsletter: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
