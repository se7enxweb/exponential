---
title: "Lektion 7.3: Ein Composer-Paket oder ein .ezpkg"
audience: trainer, designer, developer
category: training
order: 73
summary: Die Teilnehmenden wählen, wie ein Theme ausgeliefert wird (Composer, Composer mit .ezpkg darin oder nur .ezpkg), exportieren ihr Theme mit ezpm als .ezpkg, nachdem sie den Export von Erweiterungen dafür eingeschaltet haben, untersuchen das Archiv und schalten den Export wieder ab. 45 Minuten.
keywords: theme-design, paket, ezpkg, Composer, composer.json, ezpublish-legacy-extension, ezpm.php, create, add, export, ezextension, ExtensionExportSettings, AllowedExtensions, package/export_extension, prüfung auf geheimnisse, seitenstil, Setup Pakete, theme design, package, secret check, site style, Setup Packages
type: help
---
# Lektion 7.3: Ein Composer-Paket oder ein .ezpkg

Ein fertiges Theme wird als Composer-Paket, als Exponential-Paket (`.ezpkg`) oder in beiden Formen weitergegeben. Die
Wahl hängt davon ab, wer es installiert und ob es Datenbankinhalte wie Inhaltsklassen mitbringt. In dieser Lektion
treffen die Teilnehmenden die Wahl für ihr Theme und bauen auf der Kommandozeile ein `.ezpkg` daraus.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Modul | [Modul 7: Ein Theme paketieren](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Designer und Entwickler, die Themes veröffentlichen |
| Voraussetzungen | [Lektion 7.1](metadaten-der-theme-erweiterung.md); Shell-Zugang zur eigenen Installation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- mit der Entscheidungstabelle zwischen einem Composer-Paket und einem `.ezpkg` wählen;
- die vier Pakettypen unter **Setup > Pakete > Neues Paket erstellen** nennen;
- den Export für eine einzelne Erweiterung einschalten und die Prüfung auf Geheimnisse erklären;
- mit `ezpm.php` ein Paket anlegen, befüllen und exportieren und das Archiv untersuchen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Composer-Paket oder .ezpkg](../../../../../eng-us/help/designers/extensions/composer-or-ezpkg.md),
[Eine Erweiterung als Paket exportieren](../../../../../eng-us/help/designers/extensions/extension-export-package.md) und
[Exportieren und importieren mit ezpm](../../../../../eng-us/help/designers/extensions/exporting-with-ezpm.md).

1. **Die Entscheidungstabelle** (5 Minuten).

   | Frage | Composer-Paket | `.ezpkg` |
   |---|---|---|
   | Wer installiert es? | Entwickler und Hoster, die `composer` ausführen | Administratoren, die unter **Setup > Pakete** arbeiten |
   | Was bringt es mit? | Dateien: die Theme-Erweiterung | Dateien und Datenbankinhalte: Klassen, Objekte, einen Seitenstil |
   | Wie wird es aktualisiert? | `composer update <vendor>/<theme>` | Durch Installation der nächsten Paketversion |

   Drei Antworten: nur Composer; Composer mit `.ezpkg`-Klassenpaketen darin (`packages/`); nur `.ezpkg`.
2. **Pakettypen** (3 Minuten). **Setup > Pakete**, **Neues Paket erstellen**: **Export von Inhaltsobjekten**,
   **Export von Inhaltsklassen**, **Extension-Export** („Der Export von Erweiterungen muss zuerst eingeschaltet
   werden“) und **Seitenstil** (zwei Stylesheets, ihre Bilder und ein Vorschaubild).
3. **Der Export ist standardmäßig aus** (4 Minuten). `settings/package.ini [ExtensionExportSettings]
   AllowedExtensions[]` ist leer: Keine Erweiterung darf exportiert werden. Die Administratorin oder der Administrator
   trägt die Erweiterung dort ein, und in der Administration braucht die Rolle zusätzlich `package/export_extension`.
   Jeder Exportweg verweigert eine Erweiterung, die geheime Werte oder Schlüsselmaterial enthält.
4. **ezpm** (3 Minuten). `php ezpm.php help` listet die Befehle auf: `create`, `add`, `export`, `import`, `check`,
   `install`, `validate`, `list`, `info`.

### Häufige Fragen

**Warum ist der Export abgeschaltet?** Eine Erweiterung kann Einstellungen mit Passwörtern oder Schlüsseln enthalten;
ein Export würde sie in eine Datei schreiben, die weitergereicht wird. Die Prüfung auf Geheimnisse verweigert solche
Erweiterungen, und die Freigabeliste macht den Export zu einer bewussten Entscheidung.

**Kann ich die Inhaltsklassen des Themes mit Composer ausliefern?** Ja, als `.ezpkg`-Klassenpakete im Ordner
`packages/` des Themes, die das Installationsprogramm importiert ([Pakete mit Inhaltsklassen beilegen](../../../../../eng-us/help/designers/extensions/bundling-content-class-packages.md)).

**Was braucht die `composer.json`?** `type: ezpublish-legacy-extension` und `extra.ezpublish-legacy-extension-name`
mit dem Ordnernamen ([composer.json für ein Theme](../../../../../eng-us/help/designers/extensions/composer-json-for-a-theme.md)).

### Stolperfallen

- **Den Export eingeschaltet lassen.** Beenden Sie die Übung, indem Sie den Eintrag in der Freigabeliste entfernen.
- **In den vom Webserver ausgelieferten Baum der Installation exportieren.** Schreiben Sie das Archiv nach `var/tmp/`
  und verschieben Sie es dorthin, wo es hingehört.
- **Ein Geheimnis im Theme.** Wenn die Prüfung das Theme verweigert, lesen Sie den Grund; schwächen Sie die Prüfung
  nie ab, um das Paket herauszubekommen.

## Praktische Übung

Auf Ihrer eigenen Schulungsinstallation, im Stammverzeichnis der Installation.

1. Öffnen Sie in der Administration **Setup > Pakete**, klicken Sie auf **Neues Paket erstellen** und lesen Sie die
   vier Typen. Klicken Sie auf **Abbrechen**.
2. Entscheiden Sie für `training_theme`, welche der drei Antworten passt, und schreiben Sie in einem Satz, warum.
3. Erlauben Sie den Export nur für Ihr Theme:

   ```bash
   php bin/php/console exp:ini add 'package.ini/ExtensionExportSettings/AllowedExtensions[]' training_theme global
   php bin/php/ezcache.php --clear-tag=ini
   ```

4. Legen Sie das Paket an und befüllen Sie es:

   ```bash
   php ezpm.php create training_theme_pkg 'Training theme' 0.1.0 install
   php ezpm.php add training_theme_pkg ezextension training_theme
   ```

5. Exportieren Sie es: `php ezpm.php export training_theme_pkg -d var/tmp`.
6. Suchen Sie die Datei (`ls var/tmp/training_theme_pkg*`) und listen Sie ihren Inhalt mit
   `tar tzf <file> | head -30` auf.
7. Prüfen Sie die Liste: `package.xml`, die Dateien des Themes und keine `.bak`-, `.env`- oder Schlüsseldateien.
8. Validieren Sie das Archiv so, wie es eine empfangende Installation täte: `php ezpm.php validate <file>`.
9. Schalten Sie den Export wieder ab:

   ```bash
   php bin/php/console exp:ini rem 'package.ini/ExtensionExportSettings/AllowedExtensions[]' training_theme global
   php bin/php/ezcache.php --clear-tag=ini
   ```

## Erwartetes Ergebnis

- Schritt 5 schreibt ein Archiv in `var/tmp/`, dessen Name mit `training_theme_pkg-0.1.0` beginnt und auf `.ezpkg`
  endet (der Name enthält die Version und kann die Release-Nummer des Pakets enthalten).
- Schritt 6 listet `package.xml` und die Dateien von `training_theme` auf.
- Schritt 8 meldet das Archiv als gültig.
- Schritt 9: `php bin/php/console exp:ini where package/ExtensionExportSettings/AllowedExtensions` nennt das Theme in
  der Override-Datei nicht mehr.
- Der Trainer prüft am Projektor die Archivliste einer Teilnehmerin oder eines Teilnehmers.

## Quiz

1. Ein Theme braucht zwei eigene Inhaltsklassen. Welcher Lieferweg passt am besten?
2. Warum lässt sich auf einer frischen Installation keine Erweiterung exportieren?
3. Welche vier Pakettypen bietet **Neues Paket erstellen** an?
4. Welcher Befehl von `ezpm.php` schreibt das Archiv?
   A. `create` B. `add` C. `export` D. `install`
5. Was tun Sie, wenn die Prüfung auf Geheimnisse Ihr Theme verweigert?

<details>
<summary>Antworten</summary>

1. Composer mit den Klassenpaketen darin (`packages/`) oder ein `.ezpkg`, das sie mitbringt
   ([Composer-Paket oder .ezpkg](../../../../../eng-us/help/designers/extensions/composer-or-ezpkg.md)).
2. `package.ini [ExtensionExportSettings] AllowedExtensions[]` ist standardmäßig leer.
3. Export von Inhaltsobjekten, Export von Inhaltsklassen, Extension-Export, Seitenstil.
4. **C.** `export <package> -d <dir>`.
5. Den Befund lesen und das Geheimnis aus dem Theme entfernen; die Prüfung nie abschwächen
   ([Die Prüfung auf Geheimnisse](../../../../../eng-us/help/designers/extensions/package-secret-check.md)).

</details>

## Weiterführende Literatur

- [Was ein Exponential-Paket ist](../../../../../eng-us/help/designers/extensions/what-an-exponential-package-is.md).
- [Der Assistent zum Erstellen von Paketen, Schritt für Schritt](../../../../../eng-us/help/designers/extensions/package-creator-wizard-step-by-step.md):
  dasselbe im Browser.
- [Das Seitenstil-Paket](../../../../../eng-us/help/designers/extensions/site-style-package.md): nur ein Farbschema weitergeben.
- [ezpm: die Paketverwaltung auf der Kommandozeile](../../../../../eng-us/features/6.0/ezpm-package-manager-cli.md).

## Verwandte Seiten

- [Modul 7: Ein Theme paketieren](README.md)
- [Theme-Design: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
