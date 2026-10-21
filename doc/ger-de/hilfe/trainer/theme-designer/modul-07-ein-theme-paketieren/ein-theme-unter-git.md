---
title: "Lektion 7.4: Ein Theme unter Git, mit einer composer.json"
audience: trainer, designer, developer
category: training
order: 74
summary: Die Teilnehmenden legen ihr Theme mit einer .gitignore in ein eigenes Repository, machen einen ersten Commit mit einem Changelog-Präfix und ergänzen eine composer.json, die das Theme in den richtigen Erweiterungsordner installiert. 45 Minuten.
keywords: theme-design, git, git init, ein repository pro theme, gitignore, commit-nachricht, Added, Updated, Removed, Renamed, Fixed, composer.json, ezpublish-legacy-extension, ezpublish-legacy-extension-name, composer validate, SPDX-lizenz, theme design, repository per theme, commit message, SPDX license
type: help
---
# Lektion 7.4: Ein Theme unter Git, mit einer composer.json

Ein Theme mit Zukunft lebt in einem eigenen Repository, dessen Wurzel der Ordner der Erweiterung ist. Dadurch hat es
eine eigene Historie, eigene Tags und Releases, und jede Website kann es mit Composer installieren. In dieser Lektion
machen die Teilnehmenden aus `training_theme` ein solches Repository und geben ihm die `composer.json`, mit der
`composer require` es an den richtigen Ort legt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../../../../../eng-us/help/trainers/theme-designers/README.md) |
| Modul | [Modul 7: Ein Theme paketieren](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Designer und Entwickler, die Themes veröffentlichen |
| Voraussetzungen | [Lektion 7.1](metadaten-der-theme-erweiterung.md); Git installiert sowie ein Git-Name und eine E-Mail-Adresse eingestellt (für den Kurs eine beliebige Adresse unter `example.invalid`) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, warum ein Theme ein eigenes Repository hat, statt im Repository einer Website zu liegen;
- das Repository mit einer `.gitignore` beginnen, die Reste von Editoren, Build-Ausgaben und Geheimnisse fernhält;
- Commit-Nachrichten mit den Präfixen `Added:`, `Updated:`, `Removed:`, `Renamed:`, `Fixed:` schreiben;
- eine `composer.json` mit dem richtigen Typ, der richtigen Lizenz und dem richtigen Ordnernamen der Erweiterung
  schreiben und validieren.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Ein Theme unter Git stellen](../../../../../eng-us/help/designers/extensions/putting-a-theme-under-git.md),
[Eine .gitignore für Themes](../../../../../eng-us/help/designers/extensions/gitignore-for-themes.md) und
[composer.json für ein Theme](../../../../../eng-us/help/designers/extensions/composer-json-for-a-theme.md).

1. **Ein Repository pro Theme** (4 Minuten). Eigene Historie und eigene Releases; mit Composer von jeder Website
   installierbar; Mitwirkende sehen nur das Theme; es kann öffentlich sein, während die Website privat bleibt.
2. **Die .gitignore** (2 Minuten). Der Assistent hat eine geschrieben: Reste von Editoren, Dateien des Betriebssystems,
   `/vendor/`, `/node_modules/`, Logs.
3. **Commit-Nachrichten** (3 Minuten). Was sich für die Benutzer geändert hat und warum, mit einem Präfix, damit sich
   das Changelog aus der Historie erstellen lässt. Beispiel: `Fixed: The map popup escapes the address and the title`.
4. **composer.json** (6 Minuten). Die wichtigen Felder: `name` (`vendor/package`, in Kleinbuchstaben),
   `description`, `type: ezpublish-legacy-extension`, `license` als SPDX-Bezeichner, gleich wie in `LICENSE`,
   `authors`, `homepage`, `support`, `require` (PHP und das Installer-Plugin) und
   `extra.ezpublish-legacy-extension-name` (der Ordnername unterhalb von `extension/`). `version` wird meist
   weggelassen: Packagist liest die Version aus dem Tag.

### Häufige Fragen

**Brauche ich für diese Lektion GitHub?** Nein. Das Repository bleibt lokal; die Veröffentlichung ist eine
Entscheidung für Modul 8 und für echte Themes.

**Welcher Vendor-Name?** Der Packagist-Vendor Ihrer Organisation. Für den Kurs genügt ein erfundener Name in
Kleinbuchstaben.

**Kann ich das Theme im Repository der Website lassen und es später trotzdem veröffentlichen?** Sie können es später
herauslösen, aber es ist einfacher, gleich getrennt zu beginnen.

### Stolperfallen

- **Im Repository der Installation committen.** Zuerst `cd extension/training_theme`; prüfen Sie mit `git rev-parse
  --show-toplevel`.
- **Eine persönliche Adresse in Commits.** Verwenden Sie in der Übungsumgebung eine Kursadresse unter
  `example.invalid`.
- **Eine Lizenz, die sich zwischen `composer.json` und `LICENSE` unterscheidet.** Schreiben Sie die SPDX-Form
  derselben Lizenz (für GPL Version 2 oder später: `GPL-2.0-or-later`).

## Praktische Übung

Auf Ihrer eigenen Schulungsinstallation. Nichts wird irgendwohin übertragen.

1. `cd extension/training_theme` und prüfen Sie, dass der Ordner nicht schon in einem Repository liegt: `git rev-parse
   --show-toplevel` (ein Fehler oder das Stammverzeichnis der Installation bedeutet, dass er kein eigenes Repository
   ist).
2. Lesen Sie die `.gitignore`, die der Assistent geschrieben hat. Ergänzen Sie eine Zeile `*.ezpkg`, damit
   exportierte Pakete nicht versehentlich committet werden.
3. Beginnen Sie das Repository: `git init -b main`.
4. Stellen Sie nur für dieses Repository eine Kursidentität ein:
   `git config user.name "Course Learner"` und `git config user.email "learner@example.invalid"`.
5. Prüfen Sie, was committet würde: `git status --short`. Jede Datei sollte eine sein, die Sie veröffentlichen wollen.
6. Committen Sie: `git add . && git commit -m "Added: The training_theme theme extension"`.
7. Schreiben Sie `composer.json` in die Wurzel des Themes:

   ```json
   {
       "name": "training/training_theme",
       "description": "Training theme built in the Theme design course",
       "type": "ezpublish-legacy-extension",
       "license": "GPL-2.0-or-later",
       "require": {
           "php": ">=8.0",
           "se7enxweb/ezpublish-legacy-installer": "*"
       },
       "extra": {
           "ezpublish-legacy-extension-name": "training_theme"
       }
   }
   ```

   Ändern Sie `license` in die SPDX-Form der Lizenz in Ihrer Datei `LICENSE`.
8. Wenn Composer verfügbar ist, führen Sie im Ordner des Themes `composer validate` aus und lesen Sie das Ergebnis.
9. Committen Sie die Datei: `git add composer.json && git commit -m "Added: composer.json so the theme can be required by name"`.
10. Lesen Sie die Historie: `git log --oneline`.

## Erwartetes Ergebnis

- Schritt 5 listet nur die eigenen Dateien des Themes auf (keine `.bak`, kein `.ezpkg`, keine Editordateien).
- Schritt 8: `composer validate` meldet die Datei als gültig; Warnungen zu fehlenden optionalen Feldern (etwa
  `authors`) werden gelesen und notiert.
- Schritt 10 zeigt zwei Commits mit Nachrichten mit Präfix.
- Der Trainer prüft, dass die Commits in `extension/training_theme/.git` liegen, nicht im Repository der Installation.

## Quiz

1. Nennen Sie zwei Gründe für ein Repository pro Theme.
2. Welches Feld der `composer.json` lässt das Installationsprogramm das Paket unterhalb von `extension/` ablegen?
   A. `name` B. `type: ezpublish-legacy-extension` C. `homepage` D. `version`
3. Welches Feld legt den Ordnernamen unterhalb von `extension/` fest?
4. Warum wird `version` in der `composer.json` eines Themes meist weggelassen?
5. Schreiben Sie eine Commit-Nachricht für eine Änderung, mit der die Brotkrumennavigation auf Smartphones umbricht.

<details>
<summary>Antworten</summary>

1. Zwei beliebige: eigene Historie und eigene Releases, mit Composer von jeder Website installierbar, Mitwirkende sehen
   nur das Theme, es kann öffentlich sein, während die Website privat bleibt
   ([Ein Theme unter Git stellen](../../../../../eng-us/help/designers/extensions/putting-a-theme-under-git.md)).
2. **B.**
3. `extra.ezpublish-legacy-extension-name`.
4. Packagist liest die Version aus dem Git-Tag; ein Feld in der Datei müsste von Hand synchron gehalten werden.
5. Zum Beispiel `Updated: The breadcrumb wraps on narrow screens instead of overflowing`.

</details>

## Weiterführende Literatur

- [Von der Ausgabe des Assistenten zum ersten Commit](../../../../../eng-us/help/designers/extensions/from-wizard-output-to-first-commit.md).
- [Das Composer-Installationsprogramm und der Ordnername](../../../../../eng-us/help/designers/extensions/composer-installer-and-extension-name.md).
- [Eine Composer-Installation vor dem Veröffentlichen testen](../../../../../eng-us/help/designers/extensions/testing-a-composer-install-locally.md).
- [Mit Git entwickeln](../../../mit-git-entwickeln.md): an Erweiterungen innerhalb einer Installation arbeiten.

## Verwandte Seiten

- [Modul 7: Ein Theme paketieren](README.md)
- [Theme-Design: Kursüberblick](../../../../../eng-us/help/trainers/theme-designers/README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
