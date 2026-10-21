---
title: "Lektion 7.1: Die Metadaten des Themes: ezinfo.php, extension.xml, README und LICENSE"
audience: trainer, designer, developer
category: training
order: 71
summary: Die Teilnehmenden vervollständigen die Metadaten ihrer Theme-Erweiterung, sodass Name, Version, Copyright, Lizenz und Website in ezinfo.php, extension.xml und der Datei LICENSE übereinstimmen, prüfen die Dateien und finden ihr Theme auf der Seite „Über“. 45 Minuten.
keywords: theme-design, theme-erweiterung, ezinfo.php, extension.xml, metadaten, version, copyright, lizenz, Info_url, seite über, ezinfo/about, xmllint, php -l, README, LICENSE, theme design, theme extension, metadata, license, About page
type: help
---
# Lektion 7.1: Die Metadaten des Themes

Ein Theme, das andere installieren, muss sagen, wer es ist: seinen Namen, seine Version, sein Copyright, seine Lizenz
und seine Website. Exponential liest das aus zwei kleinen Dateien, `ezinfo.php` und `extension.xml`, und zeigt es auf
der Seite „Über“ und unter **Setup > Extensions** an. In dieser Lektion vervollständigen die Teilnehmenden die Dateien,
die der Assistent geschrieben hat, und bringen sie miteinander und mit der Datei `LICENSE` in Übereinstimmung.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Modul | [Modul 7: Ein Theme paketieren](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Designer und Entwickler, die Themes veröffentlichen |
| Voraussetzungen | [Lektion 2.1](../modul-02-templates-overrides-und-das-pagelayout/ihre-eigene-design-erweiterung.md): `training_theme` mit dem Assistenten angelegt |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Schlüssel von `ezinfo.php` und die Elemente von `extension.xml` nennen und sagen, welche gleich sein müssen;
- erklären, warum `extension.xml` gewinnt, wenn die beiden voneinander abweichen;
- beide Dateien prüfen (`php -l`, `xmllint --noout`) und das Ergebnis auf der Seite „Über“ sehen;
- eine README schreiben, die sagt, was das Theme ist und wie man es einschaltet.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [ezinfo.php](../../../../../eng-us/help/designers/extensions/ezinfo-php.md),
[extension.xml](../../../../../eng-us/help/designers/extensions/extension-xml.md) und
[Alle Versionsfelder synchron halten](../../../../../eng-us/help/designers/extensions/keeping-version-fields-in-sync.md).

1. **ezinfo.php** (4 Minuten). Die Klasse heißt wie der Ordner, gefolgt von `Info`, `info()` ist `static` und liefert
   `Name`, `Version` (ohne `v`), `Copyright`, `License` (genau das, was in `LICENSE` steht) und `Info_url`, optional
   `Author`.
2. **extension.xml** (4 Minuten). `<software><metadata>` mit `<name>`, `<version>`, `<copyright>`, `<license>`,
   `<info_url>`. Die Seite „Über“ liest diese Datei zuerst, wenn sie einen Block `<metadata>` hat; ein falscher Wert
   hier gewinnt also gegen einen richtigen in `ezinfo.php`.
3. **Werte, die Sie nie eintragen** (2 Minuten). `//autogentag//` oder `dev-master` als Version, eine Lizenz, die von
   `LICENSE` abweicht, eine Website, die nicht die des Themes ist.
4. **Die Regel für Releases** (3 Minuten). Jedes Release hebt die Version in beiden Dateien genau auf die Version des
   Releases an, in einem Commit vor dem Tag; Modul 8 zeigt, warum Tags dauerhaft sind.

### Häufige Fragen

**Welche Lizenz soll mein Schulungs-Theme haben?** Die, die Sie im Assistenten gewählt haben; Modul 8 bespricht die
Wahl. Der Assistent hat die Datei `LICENSE` dafür geschrieben.

**Wo zeigt die Seite „Über“ das an?** In der Tabelle der Erweiterungen von `/ezinfo/about` (Spalten **Reihenfolge**,
**Name**, **Bezeichner**, **Website** sowie Version und Lizenz) und auf der Karte der Erweiterung unter
**Setup > Extensions**.

**Was, wenn das Theme eine andere Erweiterung braucht?** Ein Block `<dependencies>` in `extension.xml`
([Abhängigkeiten angeben](../../../../../eng-us/help/designers/extensions/dependencies-in-extension-xml.md)).

### Stolperfallen

- **Zwei Versionen.** `ezinfo.php` sagt 1.0.1, `extension.xml` 1.0.0: Die Seite „Über“ zeigt 1.0.0. Durchsuchen Sie
  beide.
- **Ein Klassenname, der nicht zum Ordner passt.** `training_themeInfo` für `extension/training_theme`.
- **Das Copyright fremder Arbeit weggelassen.** Enthält das Theme Dateien von Dritten, gehören deren Inhaber in die
  Copyright-Zeile oder in die Danksagungen (Modul 8).

## Praktische Übung

Auf Ihrer Schulungsinstallation, in `extension/training_theme/`.

1. Öffnen Sie `ezinfo.php` und `extension.xml`. Schreiben Sie die fünf Werte jeder Datei nebeneinander auf ein Blatt.
2. Markieren Sie jeden Wert, der sich zwischen den beiden Dateien unterscheidet, und jeden Wert, der leer ist oder von
   einem Build-Skript übrig geblieben ist.
3. Setzen Sie in beiden Dateien die Version auf `0.1.0`, das Copyright auf `Copyright (C) 2026 <your name>` und die
   Website auf die Adresse, unter der Sie das Theme veröffentlichen würden (für einen Kurs eine Repository-Adresse
   Ihrer eigenen Organisation).
4. Vergleichen Sie die Lizenz in beiden Dateien mit den ersten Zeilen von `LICENSE`; machen Sie sie identisch.
5. Prüfen Sie beide Dateien:

   ```bash
   php -l extension/training_theme/ezinfo.php
   xmllint --noout extension/training_theme/extension.xml
   ```

6. Öffnen Sie die README, die der Assistent geschrieben hat. Achten Sie darauf, dass sie in dieser Reihenfolge sagt:
   was das Theme ist, eine kurze Beschreibung des Aussehens, wie man es aktiviert, die Zeile `SiteDesign` für einen
   Siteaccess und die Lizenz.
7. Öffnen Sie `/ezinfo/about` in der Administration und suchen Sie `training_theme` in der Tabelle der Erweiterungen.
8. Ändern Sie die Version nur in `extension.xml` auf `0.1.1`, laden Sie die Seite „Über“ neu und setzen Sie die Version
   dann wieder auf `0.1.0`.

## Erwartetes Ergebnis

- Schritt 5: `No syntax errors detected in extension/training_theme/ezinfo.php` und keine Ausgabe von `xmllint`.
- Schritt 7: Die Zeile zeigt den Namen, die Version `0.1.0`, die Lizenz und die Website.
- Schritt 8: Die Seite „Über“ zeigte `0.1.1`; das beweist, dass `extension.xml` zuerst gelesen wird.
- Der Trainer prüft die Zeilen von zwei Teilnehmenden auf der Seite „Über“.

![Die Seite „Über“ liest Version und Lizenz jedes Themes aus seinen Metadaten](../../../../../eng-us/help/images/trainers/td-about.webp)

## Quiz

1. Welche Datei liest die Seite „Über“ zuerst, wenn sie einen Block `<metadata>` hat?
2. Wie heißt die Klasse in `ezinfo.php` für die Erweiterung `training_theme`?
3. Welche dieser Versionen ist zulässig?
   A. `//autogentag//` B. `dev-master` C. `v0.1.0` D. `0.1.0`
4. Welcher Befehl prüft, ob `extension.xml` wohlgeformt ist?
5. Welche drei Stellen müssen dieselbe Lizenz nennen?

<details>
<summary>Antworten</summary>

1. `extension.xml` ([extension.xml](../../../../../eng-us/help/designers/extensions/extension-xml.md)).
2. `training_themeInfo`.
3. **D.** Ohne `v`, nie ein Wert, den ein Build-Skript hinterlassen hat.
4. `xmllint --noout extension/training_theme/extension.xml`.
5. `ezinfo.php` (`License`), `extension.xml` (`<license>`) und die Datei `LICENSE` (und `composer.json`, wenn das
   Theme eine hat).

</details>

## Weiterführende Literatur

- [ezinfo.php: was die Seite „Über“ liest](../../../../../eng-us/help/designers/extensions/ezinfo-php.md).
- [extension.xml: die paketierte Beschreibung](../../../../../eng-us/help/designers/extensions/extension-xml.md).
- [Eine README, die erklärt und überzeugt](../../../../../eng-us/help/designers/distribution/readme-that-explains-and-sells.md) und
  [Eine README für ein Theme](../../../../../eng-us/help/designers/extensions/readme-for-a-theme.md).
- [Die Datei LICENSE](../../../../../eng-us/help/designers/extensions/license-file.md).

## Verwandte Seiten

- [Modul 7: Ein Theme paketieren](README.md)
- [Theme-Design: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
