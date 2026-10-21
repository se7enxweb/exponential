---
title: "Lektion 6.1: Icon-Themes in einer Erweiterung"
audience: trainer, designer, developer
category: training
order: 61
summary: Die Teilnehmenden erklären, wie Exponential ein Icon für eine Klasse oder einen Dateityp findet (Theme, zusätzliche Themes, Standard-Theme, Vorgabe), liefern mit icon.ini und IconExtensions ein eigenes Icon-Theme in ihrer Erweiterung mit und sehen es in den Listen der Administration. 45 Minuten.
keywords: theme-design, icon-theme, icon.ini, IconSettings, Theme, StandardTheme, AdditionalThemeList, IconExtensions, ClassMap, ClassIcons, crystal, crystal-admin, bootstrap-icons, DesignTheme, klassen-icon, dateityp-icon, theme design, icon theme, class icon, mimetype icon
type: help
---
# Lektion 6.1: Icon-Themes in einer Erweiterung

Die kleinen Icons neben den Elementen in den Listen und im Baum der Administration stammen aus einem **Icon-Theme**:
einem Ordner mit Bildern und einer `icon.ini`, die Klassen und Dateitypen Dateien zuordnet. Seit Juli 2026 kann eine
Erweiterung ein eigenes Theme mitliefern, sodass ein eigenes Icon nicht mehr bedeutet, Dateien in den Kern zu
kopieren. In dieser Lektion geben die Teilnehmenden einer Inhaltsklasse der Schulungs-Website ein eigenes Icon.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Theme-Design](../README.md) |
| Modul | [Modul 6: Icons](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Designer und Entwickler |
| Voraussetzungen | [Modul 2](../modul-02-templates-overrides-und-das-pagelayout/README.md): `training_theme` aktiv; ein Bildbearbeitungsprogramm oder eine kleine PNG-Datei |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Reihenfolge erklären, in der ein Icon gesucht wird;
- die Einstellungen der `icon.ini` nennen (`Theme`, `StandardTheme`, `AdditionalThemeList[]`, `IconExtensions[]`,
  `DesignTheme[]`);
- in ihrer Erweiterung ein Icon-Theme mit Größen und einer Klassenzuordnung anlegen;
- erklären, warum admin4 das Theme `bootstrap-icons` zeigt, ganz gleich, was in `Theme` steht.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Icon-Themes in Erweiterungen](../../../../../eng-us/features/6.0/icon-themes-in-extensions.md) und
[Das Design admin4, Icons](../../../../../eng-us/features/6.0/admin4-design.md#icons).

1. **Die Suche** (4 Minuten). Ein Icon wird gesucht: im aktuellen Theme (`Theme`), zuerst in den Ordnern der in
   `IconExtensions[]` aufgeführten Erweiterungen, dann in `Repository` (`share/icons`); in jedem Theme aus
   `AdditionalThemeList[]`, in derselben Reihenfolge der Orte; in `StandardTheme`; zuletzt das Icon `Default` des
   Themes, sodass eine fehlende Datei nie ein kaputtes Bild ergibt.
2. **Die Einstellungen** (4 Minuten). `settings/icon.ini`: `Repository=share/icons`, `Theme=crystal`,
   `StandardTheme=crystal`, `AdditionalThemeList[]` leer, `IconExtensions[]` leer. Zeigen Sie `share/icons/`:
   `crystal`, `crystal-admin`, `bootstrap-icons`.
3. **admin4** (3 Minuten). `icon.ini [IconSettings] DesignTheme[admin4]=bootstrap-icons` (und `admin4l`) gibt admin4
   einen einzigen SVG-Iconsatz, ganz gleich, welches `Theme` der Siteaccess hat; er wird direkt nach dem Theme des
   Designs durchsucht. Für eine Klasse, die er nicht zuordnet, nehmen die Operatoren das PNG aus dem `Theme` des
   Siteaccess, der Inhaltsbaum zeigt aber das `Default` des Themes: Eine eigene Klasse wird in einer
   `icon.ini.append.php` zu `ClassMap` hinzugefügt.
4. **Die Dateien des Themes** (4 Minuten). Ein Ordner pro Größe und Gruppe (`32x32/apps/job.png`,
   `16x16_indexed/apps/job.png`) und die eigene `icon.ini` des Themes mit `[IconSettings] Sizes[]` und
   `[ClassIcons] ClassMap[<class identifier>]`.

### Häufige Fragen

**Welche Klassenbezeichner gibt es auf der Schulungs-Website?** **Setup > Klassen** listet sie auf; der Bezeichner wird
auf der Seite jeder Klasse angezeigt.

**Sind Icons in einer Erweiterung langsam?** Nein; es sind einfache statische Dateien, die der Webserver ausliefert.

**Kann ich ein einzelnes Icon von crystal-admin überschreiben, ohne das ganze Theme zu kopieren?** Ja: mit einem
eigenen Theme in `AdditionalThemeList[]`, das nur dieses Icon enthält, und `StandardTheme` als Fallback.

### Stolperfallen

- **Der falsche Theme-Name.** Der Ordnername unterhalb von `icons/` und der Name in `Theme` oder
  `AdditionalThemeList[]` müssen identisch sein.
- **Nur in admin4 testen.** admin4 bevorzugt `bootstrap-icons`; für diese Übung muss das Theme der Teilnehmenden auch in
  der Suche von admin4 stehen, oder man verwendet das klassische Design, um das PNG zu sehen (Schritt 9).
- **Die Caches vergessen.** Änderungen an INI-Dateien verlangen, dass der INI-Cache geleert wird; Listen mit Icons
  können im View-Cache der Inhalte liegen.

## Praktische Übung

Auf Ihrer Schulungsinstallation. Wählen Sie den Bezeichner einer Inhaltsklasse der Schulungs-Website, von der Elemente
in einem Ordner liegen (zum Beispiel `article`), und notieren Sie ihn als `<class>`.

1. Legen Sie die Ordner an:

   ```bash
   mkdir -p extension/training_theme/icons/training-icons/32x32/apps extension/training_theme/icons/training-icons/16x16_indexed/apps
   ```

2. Legen Sie ein eigenes PNG mit 32 x 32 und eines mit 16 x 16 Pixeln (oder zwei Kopien einer Datei aus
   `share/icons/crystal-admin/`) in die beiden Ordner `apps/`, benannt `<class>.png`.
3. Schreiben Sie `extension/training_theme/icons/training-icons/icon.ini`:

   ```ini
   #?ini charset="utf-8"?
   [IconSettings]
   Sizes[]
   Sizes[normal]=32x32
   Sizes[small]=16x16_indexed

   [ClassIcons]
   Default=mimetypes/empty.png
   ClassMap[<class>]=apps/<class>.png
   ```

4. Schreiben Sie `extension/training_theme/settings/icon.ini.append.php`:

   ```ini
   <?php /* #?ini charset="utf-8"?

   [ExtensionSettings]
   IconExtensions[]=training_theme

   [IconSettings]
   AdditionalThemeList[]=training-icons

   */ ?>
   ```

5. Leeren Sie die Caches: `php bin/php/ezcache.php --clear-tag=ini` und `php bin/php/ezcache.php --clear-tag=content`.
6. Lesen Sie die Suche so, wie die Administration sie sieht:
   `php bin/php/console exp:ini where icon/IconSettings/AdditionalThemeList admin`.
7. Öffnen Sie in der Administration den Ordner mit den Elementen von `<class>` und sehen Sie sich die Icons in der
   Liste der Unterelemente an.
8. Vergleichen Sie mit einem Element einer anderen Klasse.
9. Um das PNG dort zu sehen, wo admin4 seinen SVG-Satz verwendet, schalten Sie das Administrationsdesign für Ihre
   Sitzung mit dem Werkzeug **Oberfläche** auf **Klassisch** um (Lektion 1.4), sehen Sie noch einmal nach und schalten
   Sie zurück.

## Erwartetes Ergebnis

- Schritt 6 nennt `extension/training_theme/settings/icon.ini.append.php` mit `AdditionalThemeList[]=training-icons`.
- Schritt 7 oder Schritt 9: Elemente von `<class>` zeigen das Icon der Teilnehmenden, wo das geltende Theme die Klasse
  nicht schon selbst zuordnet; andere Klassen behalten ihre Icons.
- Der Trainer prüft am Projektor die Liste einer Teilnehmerin oder eines Teilnehmers und erklärt jeden Fall, in dem
  `bootstrap-icons` oder das `Theme` des Siteaccess zuerst geantwortet hat.

## Quiz

1. Bringen Sie in die richtige Reihenfolge: `StandardTheme`, `Theme`, das `Default` des Themes,
   `AdditionalThemeList[]`.
2. Welche Einstellung lässt Exponential den Ordner `icons/` einer Erweiterung durchsuchen?
3. Warum zeigt admin4 SVG-Icons, ganz gleich, welches `Theme` der Siteaccess hat?
4. Was besagt `ClassMap[job]=apps/job.png` in einem Theme?
5. Richtig oder falsch: Eine fehlende Icon-Datei zeigt ein kaputtes Bild.

<details>
<summary>Antworten</summary>

1. `Theme`, `AdditionalThemeList[]`, `StandardTheme`, `Default`
   ([Icon-Themes in Erweiterungen](../../../../../eng-us/features/6.0/icon-themes-in-extensions.md#how-an-icon-is-found)).
2. `icon.ini [ExtensionSettings] IconExtensions[]=<extension>`.
3. `icon.ini [IconSettings] DesignTheme[admin4]=bootstrap-icons` gibt dem Design ein eigenes Theme.
4. Elemente der Klasse `job` verwenden `apps/job.png` aus dem Ordner der angeforderten Größe.
5. **Falsch.** Das Icon `Default` des Themes wird verwendet.

</details>

## Weiterführende Literatur

- [Icon-Themes, die in Ihrer Erweiterung liegen](../../../../../eng-us/features/6.0/icon-themes-in-extensions.md): die Tabelle der
  Einstellungen.
- [Unterstützung für Icons (Leitfaden)](../../../../../eng-us/bc/6.0/ICONS.md): die zugrunde liegende Klasse und Hinweise zum
  Upgrade.
- [Schriften, Icons und Bibliotheken von Dritten](../../../../../eng-us/help/designers/distribution/third-party-fonts-icons-and-libraries.md):
  Lizenzen der Icons, die Sie mitliefern (Modul 8).

## Verwandte Seiten

- [Modul 6: Icons](README.md)
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
