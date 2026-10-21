---
title: "Lektion 8.1: Wie Einstellungen gelesen werden"
audience: trainer, developer
category: training
order: 81
summary: Die Teilnehmenden lesen das Format von .ini- und .ini.append.php-Dateien, verfolgen die Reihenfolge, in der eZINI die Geltungsbereiche liest, erklären Arrays, Rücksetzzeilen und Hashes und führen jeden Wert mit exp:ini where und der Einstellungsseite auf seine Datei zurück. 45 Minuten.
keywords: entwickler, INI, eZINI, ini.append.php, settings/override, settings/siteaccess, geltungsbereiche, ladereihenfolge, arrays, rücksetzzeile, hash, exp:ini where, einstellungsseite, Ini Einstellungen, ablageorte, developers course, scopes, load order, reset line, settings page, placement
type: help
---
# Lektion 8.1: Wie Einstellungen gelesen werden

Fast jedes Verhalten von Exponential ist eine Einstellung in einer `.ini`-Datei, und dieselbe Einstellung kann in
vielen Dateien stehen. Diese Lektion erklärt das Dateiformat, die Reihenfolge, in der die Dateien gelesen werden, und
wie sich Arrays zusammensetzen, sodass die Teilnehmenden die alltägliche Frage „Woher kommt dieser Wert?“ mit einem
Befehl beantworten können.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 8: Einstellungen und Siteaccesses](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler und Administratoren |
| Voraussetzungen | [Modul 7](../modul-07-erweiterungen/README.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- das Format von `settings/site.ini` und einer `.ini.append.php`-Datei lesen;
- die Geltungsbereiche und die Reihenfolge nennen, in der sie gelesen werden;
- erklären, wie sich Arrays zusammensetzen, was die Rücksetzzeile `Variable[]` bewirkt und wie Hash-Einträge
  geschrieben werden;
- einen Wert mit `exp:ini where` und auf der Einstellungsseite der Administration zurückverfolgen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Spezifikation: INI-Override-Verzeichnisse und Ablageorte](../../../../spezifikationen/6.0/ini-override-platzierungen.md),
[Konsole exp:ini](../../../../bc/6.0/console-exp-ini.md#where-und-list) und
[Die Einstellungsseite](../../../../anleitungen/einstellungsseite.md).

1. **Das Format** (4 Minuten). `settings/site.ini` enthält die Standardwerte: Zeilen `[Block]` und Zeilen
   `Variable=value`; `#` leitet einen Kommentar ein. Eine `.ini.append.php`-Datei hat denselben Inhalt innerhalb von
   `<?php /* #?ini charset="utf-8"?` und `*/ ?>`, sodass ein Webserver sie nie als Text anzeigt.
2. **Die Reihenfolge** (5 Minuten). Zuerst `settings/<file>.ini`, dann die Override-Verzeichnisse. `eZINI` führt sie
   in der Reihenfolge der Geltungsbereiche `sa-extension`, `siteaccess`, `extension`, `override` zusammen: Eine
   später gelesene Datei gewinnt. Also gewinnt `settings/override/` (der globale Geltungsbereich) gegen alles; eine
   Einstellung im Wurzelordner `settings/` einer Erweiterung gewinnt gegen `settings/siteaccess/<sa>/`.
3. **Arrays** (4 Minuten). `List[]=a` in einer Datei und `List[]=b` in einer späteren ergeben `a, b`. Die Zeile
   `List[]` allein setzt das Array zurück: Die Werte früherer Dateien fallen weg. Ein Hash-Eintrag lautet
   `List[key]=value`.
4. **Die Werkzeuge** (4 Minuten). `exp:ini where <file>/<Block>/<Variable> [<sa>]` listet jede Datei in
   Ladereihenfolge und den wirksamen Wert auf und führt Dateien inaktiver Erweiterungen getrennt auf. In der
   Administration zeigt **Setup** > **Ini Einstellungen** jeden Wert mit der Datei, aus der er stammt.
5. **Caches** (2 Minuten). Einstellungen werden gecacht; `config.php` schaltet die Prüfung der Dateizeit ab, sodass
   eine Änderung von Hand erst nach `php bin/php/ezcache.php --clear-tag=ini` sichtbar wird. `exp:ini` leert den
   Cache nach jedem Schreiben.

### Häufige Fragen

**Warum gewinnt meine Siteaccess-Einstellung nicht?** Dieselbe Variable ist in `settings/override/` oder im
Wurzelordner `settings/` einer aktiven Erweiterung gesetzt, die später gelesen werden. `exp:ini where` zeigt es.

**Kann sich eine Einstellung pro Rechner unterscheiden?** Ja: [Einstellungen pro Umgebung](../../../../../eng-us/features/6.0/environment-settings.md)
fügt Variantendateien wie `site.ini.test.append.php` hinzu, die über die Konstante `EXP_ENV` gewählt werden.

**Sind Einstellungsdateien geheim?** Manche enthalten Datenbankpasswörter. `exp:ini` und die Einstellungsseite
maskieren Werte, deren Namen nach Geheimnissen aussehen.

### Stolperfallen

- **`settings/site.ini` bearbeiten.** Das ist die Standarddatei des Kernels und wird bei jedem Update ersetzt.
  Schreiben Sie Overrides.
- **Eine vergessene Rücksetzzeile.** Ein Array ohne `Variable[]` behält die Werte früherer Dateien; mit ihr verwirft
  es sie. Beides ist an verschiedenen Stellen beabsichtigt; lesen Sie das Ergebnis mit `where`.
- **Änderungen von Hand, ohne den INI-Cache zu leeren.** Der alte Wert bleibt wirksam.

## Praktische Übung

Auf der Entwicklungsinstallation.

1. Öffnen Sie `settings/site.ini` und suchen Sie den Block `[SiteSettings]` und die Variable `SiteName`.
2. Verfolgen Sie sie zurück: `php bin/php/console exp:ini where site.ini/SiteSettings/SiteName <sa>`.
3. Verfolgen Sie ein Array zurück: `php bin/php/console exp:ini where site.ini/ExtensionSettings/ActiveExtensions`.
   Zählen Sie, wie viele Dateien etwas hinzufügen.
4. Suchen Sie eine Rücksetzzeile in den Standardwerten: `grep -n "^AvailableSiteAccessList\[\]$" settings/site.ini`.
   Sie beginnt das Array leer; Overrides fügen dann die Siteaccesses dieser Installation hinzu.
5. Listen Sie die wirksamen Variablen eines Blocks auf: `php bin/php/console exp:ini list site.ini/DebugSettings`.
6. Listen Sie die Blöcke des globalen Overrides auf: `php bin/php/console exp:ini list site.ini global`. Achten Sie
   auf maskierte Werte, falls vorhanden.
7. Öffnen Sie in der Administration **Setup** > **Ini Einstellungen**, wählen Sie `site.ini` und suchen Sie
   `SiteName` mit der Datei, aus der der Wert stammt.
8. Notieren Sie für eine Einstellung Ihrer Wahl die Dateien in Ladereihenfolge und den wirksamen Wert.

## Erwartetes Ergebnis

- Schritt 2: `settings/site.ini` mit dem Standardwert, die Siteaccess-Datei mit dem Namen der Website und
  `In effect:`.
- Schritt 3: mehrere Dateien, einige aus Erweiterungen; Arrays setzen sich zusammen.
- Schritt 6: Geheimnisse wie Passwörter erscheinen als `********`.
- Schritt 7: derselbe Wert und dieselbe Datei wie in Schritt 2.
- Der Trainer prüft Schritt 8 bei zwei Personen.

## Quiz

1. Was umschließt den Inhalt einer `.ini.append.php`-Datei, und warum?
2. Welche Datei gewinnt: `settings/siteaccess/site/site.ini.append.php` oder `settings/override/site.ini.append.php`?
   A. Die Siteaccess-Datei B. Die Override-Datei C. Die zuletzt geänderte D. Keine: Sie werden nach Namen zusammengeführt
3. Was bewirkt die Zeile `ActiveExtensions[]` allein?
4. Welcher Befehl listet jede Datei, die eine Variable setzt, in Ladereihenfolge auf?
5. Warum wird eine Änderung an einer Einstellungsdatei von Hand nicht sofort sichtbar?

<details>
<summary>Antworten</summary>

1. `<?php /* ... */ ?>`, damit der Webserver sie als PHP ausführt und die Einstellungen nie als Text anzeigt.
2. **B.** Der Override-Geltungsbereich (global) wird zuletzt gelesen und gewinnt.
3. Sie setzt das Array zurück: Werte aus früher gelesenen Dateien fallen weg.
4. `exp:ini where <file>/<Block>/<Variable>`.
5. Einstellungen werden gecacht, und `config.php` schaltet die Prüfung der Dateizeit ab; leeren Sie den Cache-Tag
   `ini`.

</details>

## Weiterführende Literatur

- [Spezifikation: INI-Override-Verzeichnisse und Ablageorte](../../../../spezifikationen/6.0/ini-override-platzierungen.md).
- [Die Einstellungsseite](../../../../anleitungen/einstellungsseite.md): Einstellungen in der Administration lesen und ändern.
- [Einstellungen pro Umgebung](../../../../../eng-us/features/6.0/environment-settings.md).

## Verwandte Seiten

- [Modul 8: Einstellungen und Siteaccesses](README.md)
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
