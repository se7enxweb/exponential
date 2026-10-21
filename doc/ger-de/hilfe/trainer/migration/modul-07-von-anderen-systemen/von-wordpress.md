---
title: "Lektion 7.1: Von WordPress"
audience: trainer, admin, developer
category: training
order: 71
summary: Die Teilnehmenden planen eine WordPress-Migration von Anfang bis Ende - was WordPress exportiert (WXR, WP-CLI, die REST-API, das Upload-Verzeichnis), die Zuordnung von Beiträgen, Seiten, Medien, Taxonomien, Shortcodes und SEO-Feldern, die neun Schritte des Imports, Permalinks und Weiterleitungen sowie ein Delta-Import vor der Umstellung. 60 Minuten.
keywords: migration zu exponential, beiträge, seiten, anhänge, kategorien, schlagwörter, weiterleitungen, delta-import, passwort zurücksetzen, migrating to exponential, WordPress, WXR, WP-CLI, wp export, REST API, uploads, posts, pages, attachments, categories, tags, shortcodes, permalinks, redirects, delta import, password reset
type: help
---
# Lektion 7.1: Von WordPress

WordPress ist die Quelle, der Trainer am häufigsten begegnen. Diese Lektion folgt der WordPress-Anleitung der
Dokumentation: was WordPress exportieren kann, wie seine Inhalte zugeordnet werden, die neun Schritte vom Export bis
zur Umstellung und die Adressen, die weiter funktionieren müssen. Sie wendet den Rahmen der Module 1 bis 3 auf eine
echte Quelle an.

> **Hinweis**
> Üben Sie an einer Kopie einer WordPress-Website oder an einer WXR-Datei aus einer Testinstallation. Exportieren Sie
> niemals die Benutzer einer Live-Website in eine Schulungsinstallation; Testkonten verwenden Adressen unter
> `example.invalid`.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 7: Von anderen Systemen](README.md) |
| Dauer | 60 Minuten (20 Präsentation, 40 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler, Administratoren und Website-Betreiber, die von WordPress umziehen |
| Voraussetzungen | [Modul 2](../modul-02-inhalte-zuordnen/README.md) und [Modul 3](../modul-03-import-und-export-werkzeuge/README.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die vier Exportquellen von WordPress nennen und sagen, welche die eingefrorene Quelle des Imports ist;
- Beiträge, Seiten, Anhänge, Kategorien, Schlagwörter, benutzerdefinierte Felder und SEO-Felder auf Exponential
  abbilden;
- die neun Schritte der WordPress-Anleitung in die richtige Reihenfolge bringen;
- für jede Art von WordPress-Adresse entscheiden, wie sie weiter funktioniert;
- den Delta-Import vor der Umstellung erklären.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [WordPress und Exponential](../../../../installation/migration/wordpress.md).

1. **Exporte** (4 Minuten). Tools > Export und `wp export` liefern WXR (Beiträge, Seiten, benutzerdefinierte
   Beitragstypen, Kommentare, benutzerdefinierte Felder, Begriffe, Benutzer), ohne Website-Konfiguration und ohne die
   Dateien. Die REST-API liefert JSON mit Seitenaufteilung. Das Verzeichnis `wp-content/uploads` enthält die Dateien.
   WXR ist die eingefrorene Quelle; die REST-API liefert das Delta der letzten Tage.
2. **Zuordnung** (6 Minuten). Zeigen Sie die Zuordnungstabelle: Beiträge zu `article`-Objekten unterhalb eines
   Blog-Ordners; Seiten als Baum entlang `post_parent`; `post_content` zu `ezxmltext`, mit entfernten Block-Kommentaren
   und Bildern als Embeds; das Beitragsbild ausdrücklich aus `_thumbnail_id`; Kategorien als Ordner oder `eztags`;
   Shortcodes umgewandelt oder gerendert; Felder des SEO-Plugins zu `xrowmetadata`. Passwörter: Die Formate von
   WordPress werden nicht gelesen; planen Sie das Zurücksetzen.
3. **Neun Schritte** (5 Minuten). Bestandsaufnahme, Klassen, Export, Umwandlung, zuerst die Medien, interne Links,
   Weiterleitungen und SEO, Delta, Umstellung.
4. **Adressen** (5 Minuten). Permalinks werden zu weiterleitenden eigenen Aliasen aus WXR-`<link>`; `/?p=123` geht an
   den Webserver; Feeds an einen RSS-Export; `/wp-content/uploads/...` zu den Medienobjekten oder unter diesem Pfad
   beibehalten; die Anmeldeadressen erhalten 410 oder eine Weiterleitung.

### Häufige Fragen

**Unsere Website verwendet ein Page-Builder-Plugin.** Sein Layout liegt in Post-Meta oder Shortcodes: Geben Sie ihm
eine eigene Umwandlung, oder greifen Sie auf gerendertes HTML zurück (die veröffentlichte Seite abrufen und den
Inhaltsbereich nehmen). Testen Sie die komplexesten Seiten zuerst.

**Wie finden wir die Shortcodes?** Das Beispiel der Anleitung: `grep -o '\[[a-z_]*'` über die Inhalte, dann jeden
umwandeln oder rendern.

**Können wir später den umgekehrten Weg gehen?** Ja: Abschnitt 5 der Anleitung beschreibt den Weg von Exponential zu
WordPress mit xrowextract und dem WordPress Importer (Lektion 8.1).

### Stolperfallen

- **Die Dateien vergessen.** WXR enthält URLs der Anhänge, nicht die Dateien; kopieren Sie das Upload-Verzeichnis.
- **Shortcodes im Text gelassen.** Sie erscheinen auf der neuen Website als `[gallery ...]`.
- **Die Benutzer am Starttag über das Zurücksetzen der Passwörter informieren.** Informieren Sie sie vorher.

## Praktische Übung

Eine Planungsübung in Dreiergruppen, mit geöffneter WordPress-Anleitung. Das Szenario: eine WordPress-Website mit
1 200 Beiträgen, 60 Seiten in drei Ebenen, 4 000 Anhängen, Kategorien und Schlagwörtern, einem SEO-Plugin, einem
Kontaktformular-Plugin, der Permalink-Struktur `/%year%/%monthnum%/%postname%/` und 25 Redakteurskonten.

1. Schreiben Sie die Befehle und Prüfungen der Bestandsaufnahme aus Schritt 1 von
   [Abschnitt 3 der Anleitung](../../../../installation/migration/wordpress.md#3-von-wordpress-zu-exponential-schritt-für-schritt)
   auf, einschließlich `wp post list --post_type=post --format=count`.
2. Schreiben Sie den Exportbefehl für ein eingefrorenes WXR ohne Größenbegrenzung auf und was Sie sonst noch kopieren.
3. Füllen Sie anhand von [Abschnitt 2](../../../../installation/migration/wordpress.md#2-wordpress-auf-exponential-abbilden)
   eine Zuordnungstabelle für Beiträge, Seiten, Anhänge, Kategorien, Schlagwörter, die Felder des SEO-Plugins und das
   Kontaktformular aus.
4. Schreiben Sie das Muster der Remote-IDs für Beiträge, Seiten und Anhänge auf.
5. Bringen Sie den Import in die richtige Reihenfolge: Kommentare, Seiten, Medien, Beiträge. Begründen Sie sie.
6. Schreiben Sie eine Weiterleitungstabelle mit sechs Zeilen für die Permalink-Struktur, `/?p=`, ein Kategoriearchiv,
   einen Feed, einen Upload und `/wp-login.php`, jeweils mit dem Exponential-Werkzeug
   ([Abschnitt 4](../../../../installation/migration/wordpress.md#4-adressen-und-weiterleitungen)).
7. Schreiben Sie auf, wie das Delta vor der Umstellung exportiert wird und warum es keine Duplikate erzeugt.
8. Schreiben Sie die vier Risiken aus [Abschnitt 6](../../../../installation/migration/wordpress.md#6-risiken-und-checkliste)
   auf, die zutreffen, jeweils mit der Gegenmaßnahme.

## Erwartetes Ergebnis

- Schritt 2: `wp export --dir=<dir> --max_file_size=-1` (oder Tools > Export) sowie eine Kopie von
  `wp-content/uploads`.
- Schritt 3: Beiträge zu Artikeln unterhalb eines Blog-Ordners, Seiten als Baum, Anhänge als Bild- oder Dateiobjekte
  in einem Medienordner, Kategorien als Ordner oder Tags, Schlagwörter als `ezkeyword` oder `eztags`, SEO-Felder zu
  `xrowmetadata`, das Formular mit der Informationssammlung neu aufgebaut und seine Einsendungen archiviert.
- Schritt 5: Medien, Seiten (in der Reihenfolge der Elternelemente), Beiträge, Kommentare: Inhalte, die sich auf etwas
  beziehen, kommen nach dem, worauf sie sich beziehen.
- Schritt 6: Permalinks als weiterleitende eigene Aliase, die aus `<link>` erzeugt werden, `/?p=` im Webserver.
- Schritt 7: der Datumsfilter von WXR oder die REST-API mit `modified_after`; Remote-IDs machen daraus eine
  Aktualisierung.
- Der Trainer prüft mit jeder Gruppe die Schritte 5 bis 7.

## Quiz

1. Was enthält eine WXR-Datei laut der Dokumentation von WP-CLI **nicht**?
   A. Beiträge B. Kommentare C. Die Dateien der Anhänge selbst D. Benutzerdefinierte Felder
2. Woher kommt ein WordPress-Beitragsbild im Export, und warum ordnet man es ausdrücklich zu?
3. Wie wird `/?p=123` weitergeleitet, und warum nicht mit einem URL-Alias?
4. Welche Quelle liefert den Delta-Import der letzten Tage vor der Umstellung?
5. Können WordPress-Benutzer ihre Passwörter behalten?

<details>
<summary>Antworten</summary>

1. **C**, und keine Website-Konfiguration ([WordPress-Anleitung, Abschnitt 1](../../../../installation/migration/wordpress.md#1-was-wordpress-exportieren-kann)).
2. Aus dem Post-Meta `_thumbnail_id`; andernfalls gehen Beitragsbilder verloren
   ([Abschnitt 2](../../../../installation/migration/wordpress.md#2-wordpress-auf-exponential-abbilden) und Abschnitt 6).
3. Im Webserver aus der Weiterleitungstabelle; Query-Strings erreichen die URL-Aliase nie
   ([Abschnitt 4](../../../../installation/migration/wordpress.md#4-adressen-und-weiterleitungen)).
4. Der Datumsfilter von WXR oder die REST-API mit `modified_after`
   ([Abschnitt 3](../../../../installation/migration/wordpress.md#3-von-wordpress-zu-exponential-schritt-für-schritt)).
5. Nein; WordPress speichert eigene Hash-Formate, die Exponential nicht liest: Planen Sie das Zurücksetzen
   (Abschnitt 2).

</details>

## Weiterführende Informationen

- [WordPress und Exponential](../../../../installation/migration/wordpress.md): beide Richtungen mit Quellen.
- [Inhalte auf Exponential abbilden](../../../../installation/migration/inhaltsmodell-zuordnen.md): die Bausteine.
- [Die Import- und Exportwerkzeuge von Exponential, Abschnitt 4](../../../../installation/migration/exponential-tools.md#4-ein-eigenes-importskript-das-grundgerüst):
  das Skriptgerüst, das die Umwandlung speist.

## Verwandte Seiten

- [Modul 7: Von anderen Systemen](README.md)
- [Migration zu Exponential: Kursübersicht](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
