---
title: "Lektion 7.2: extension.xml, ezinfo.php und eine Lizenz"
audience: trainer, developer
category: training
order: 72
summary: Die Teilnehmenden geben ihrer Erweiterung eine Identität: ezinfo.php und extension.xml mit übereinstimmendem Namen, Version, Copyright, Lizenz und Website, eine Datei LICENSE und eine Prüfung auf der Seite Über. 45 Minuten.
keywords: entwickler, ezinfo.php, extension.xml, metadaten, version, lizenz, LICENSE, copyright, info_url, seite über, xmllint, versionsfelder synchron halten, GPL, developers course, metadata, license, About page, version fields in sync
type: help
---
# Lektion 7.2: extension.xml, ezinfo.php und eine Lizenz

Eine Erweiterung teilt dem System in zwei kleinen Dateien mit, wer sie ist: `ezinfo.php` und `extension.xml`. Die
Seite **Über**, die Seite **Extensions** und die Upgrade-Prüfungen lesen sie, und wer die Erweiterung auf einem
Server findet, erfährt daraus, welches Release es ist und unter welcher Lizenz sie verwendet werden darf. In dieser
Lektion geben die Teilnehmenden `mysite_hello` seine Identität und eine Lizenz.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 7: Erweiterungen](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 7.1](der-aufbau-einer-erweiterung.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- `ezinfo.php` mit einer statischen Methode `info()` schreiben, die `Name`, `Version`, `Copyright`, `License` und
  `Info_url` zurückgibt;
- `extension.xml` mit einem Block `<metadata>` schreiben, der mit `ezinfo.php` übereinstimmt;
- eine Datei `LICENSE` hinzufügen, deren Name zur Lizenzzeile passt;
- beide Dateien mit `php -l` und `xmllint --noout` sowie auf der Seite **Über** (`/ezinfo/about`) prüfen;
- erklären, warum Versionsfelder vor einem Release-Tag erhöht werden und niemals danach.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [ezinfo.php: was die Seite Über liest](../../../../../eng-us/help/designers/extensions/ezinfo-php.md),
[extension.xml: die verpackte Beschreibung](../../../../../eng-us/help/designers/extensions/extension-xml.md) und
[Spezifikation: Metadaten von Erweiterungen](../../../../spezifikationen/6.0/extension-metadaten.md).

1. **`ezinfo.php`** (4 Minuten). Die Klasse heißt wie der Ordner, gefolgt von `Info`, `info()` ist `static`, und
   jeder Schlüssel ist ein String in Anführungszeichen. Zeigen Sie `extension/cjw_themes_jumper/ezinfo.php`, falls
   installiert.
2. **`extension.xml`** (3 Minuten). `<software><metadata>` mit `<name>`, `<version>`, `<copyright>`, `<license>`,
   `<info_url>`. Die Seite **Über** liest zuerst `extension.xml`, wenn es einen Block `<metadata>` hat; deshalb
   müssen beide Dateien übereinstimmen.
3. **Werte, die man nie schreibt** (2 Minuten). `//autogentag//` oder `dev-master` als Version; eine Lizenz, die
   von der Datei `LICENSE` abweicht; eine Website, die nicht Ihnen gehört.
4. **Die Lizenz** (3 Minuten). Code, der auf dem Kernel aufbaut, steht in der Regel unter der GNU GPL, Version 2
   oder später; die Lizenzzeile lautet "GNU General Public License v2.0 (or any later version)". Die Wahl der Lizenz
   ist die Entscheidung des Autors; [Lizenzwahl](../../../../../eng-us/help/designers/distribution/licensing-choices-overview.md)
   erklärt die Möglichkeiten.
5. **Versionen und Tags** (3 Minuten). Ein Release erhöht die Version in beiden Dateien in dem Commit, auf den der
   Tag zeigt. Ein gepushter Tag ist dauerhaft; ein Fehler wird mit der nächsten Version behoben, niemals durch das
   Verschieben eines Tags.

### Häufige Fragen

**Braucht eine private Erweiterung eine Lizenz?** Eine Lizenzdatei sagt, wie andere den Code verwenden dürfen; auch
bei einer Erweiterung, die Ihr Unternehmen nie verlässt, lohnt es sich, die Copyright-Zeile korrekt zu halten.

**Was trägt sonst noch eine Version?** Ein `version` in `composer.json`, falls Sie eines führen, und ein
Versionskopf in einer `share/filelist.md5`, falls die Erweiterung eine hat; sie ändern sich gemeinsam.

**Welche Versionsnummer für ein erstes Release?** Das entscheiden Sie; die Distributionsseiten für Designer erklären
die Konvention, dass nur die letzte Stelle weiterzählt (auf `1.0.9` folgt `1.0.10`).

### Stolperfallen

- **Ein nicht statisches `info()` oder Schlüssel ohne Anführungszeichen.** Die Seite **Über** zeigt Striche.
- **Unterschiedliche Werte in den beiden Dateien.** Die Seite **Über** zeigt die Werte aus `extension.xml`, und das
  sind womöglich die veralteten.
- **Eine Lizenzzeile aus einer anderen Erweiterung kopieren.** Sie muss zu Ihrer eigenen Datei `LICENSE` passen.

## Praktische Übung

Auf der Entwicklungsinstallation, in `extension/mysite_hello`.

1. Legen Sie `ezinfo.php` an:

   ```php
   <?php
   class mysite_helloInfo
   {
       public static function info()
       {
           return array(
               'Name'      => "Mysite Hello",
               'Version'   => "0.1.0",
               'Copyright' => "Copyright (C) 2026 <your name>",
               'License'   => "GNU General Public License v2.0 (or any later version)",
               'Info_url'  => "https://example.invalid/mysite_hello",
           );
       }
   }
   ```

2. Legen Sie `extension.xml` mit denselben Werten an:

   ```xml
   <?xml version="1.0" encoding="utf-8" ?>
   <software>
       <metadata>
           <name>Mysite Hello</name>
           <version>0.1.0</version>
           <copyright>Copyright (C) 2026 your name</copyright>
           <license>GNU General Public License v2.0 (or any later version)</license>
           <info_url>https://example.invalid/mysite_hello</info_url>
       </metadata>
   </software>
   ```

3. Fügen Sie eine Datei `LICENSE` mit dem vollständigen Text der GNU GPL Version 2 hinzu, zum Beispiel eine Kopie von
   `extension/ezformtoken/LICENSE`, die im Kernel-Repository mitgeliefert wird.
4. Prüfen Sie: `php -l extension/mysite_hello/ezinfo.php` und `xmllint --noout extension/mysite_hello/extension.xml`.
5. Öffnen Sie die Seite **Über**, `/ezinfo/about` in der Administration (Überschrift „Exponential information“), und
   suchen Sie unter **Extensions** die Zeile von `mysite_hello`.
6. Ändern Sie die Version nur in `extension.xml` auf `0.1.1`, laden Sie die Seite **Über** neu und sehen Sie, welcher
   Wert gewinnt. Bringen Sie danach beide Dateien wieder auf dieselbe Version.
7. Committen Sie die drei Dateien mit der Nachricht `Added: Extension metadata and license`.

## Erwartetes Ergebnis

- Schritt 4: `No syntax errors detected` und keine Ausgabe von `xmllint`.
- Schritt 5: eine Zeile mit Name, Version, Lizenz und Website.
- Schritt 6: Die Seite **Über** zeigt `0.1.1`, den Wert aus `extension.xml`.
- Der Trainer prüft die Zeile einer Person auf der Seite **Über**.

## Quiz

1. Wie heißt die Klasse in `ezinfo.php` der Erweiterung `mysite_hello`?
2. Welche Datei liest die Seite **Über** zuerst, wenn beide Werte haben?
   A. `ezinfo.php` B. `extension.xml` C. `composer.json` D. `LICENSE`
3. Nennen Sie zwei Werte, die niemals als Version geschrieben werden dürfen.
4. Wann wird die Version für ein Release erhöht: vor oder nach dem Tag?
5. Welcher Befehl prüft, ob `extension.xml` wohlgeformt ist?

<details>
<summary>Antworten</summary>

1. `mysite_helloInfo`.
2. **B.** `extension.xml`, wenn es einen Block `<metadata>` hat.
3. `//autogentag//` und `dev-master`.
4. Davor: in dem Commit, auf den der Tag zeigt. Ein gepushter Tag ist dauerhaft.
5. `xmllint --noout extension/mysite_hello/extension.xml`.

</details>

## Weiterführende Literatur

- [ezinfo.php](../../../../../eng-us/help/designers/extensions/ezinfo-php.md) und [extension.xml](../../../../../eng-us/help/designers/extensions/extension-xml.md).
- [Alle Versionsfelder synchron halten](../../../../../eng-us/help/designers/extensions/keeping-version-fields-in-sync.md).
- [Die Datei LICENSE](../../../../../eng-us/help/designers/extensions/license-file.md) und
  [Lizenzwahl](../../../../../eng-us/help/designers/distribution/licensing-choices-overview.md).
- [Ein gepushter Tag ist dauerhaft](../../../../../eng-us/help/designers/distribution/a-pushed-tag-is-permanent.md).

## Verwandte Seiten

- [Modul 7: Erweiterungen](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
