---
title: "Lektion 3.1: Eine Website in einer eigenen Erweiterung"
audience: trainer, admin, developer
category: training
order: 31
summary: Die Teilnehmenden verschieben die Einstellungen der zweiten Website mit exp:ini move-all aus settings/siteaccess in eine Website-Erweiterung, prüfen, dass jeder wirksame Wert gleich geblieben ist, und lesen die Sicherungen und die Regel für das Zurückrollen. 55 Minuten.
keywords: multisite und seitenpakete, website-erweiterung, einstellungen verschieben, sicherung, zurückrollen, multisite and site packages, site extension, exp:ini move-all, exp:ini move, --create-extension, --activate, ActiveAccessExtensions, extension siteaccess settings, var/backup/ini, rollback, --dry-run
type: help
---
# Lektion 3.1: Eine Website in einer eigenen Erweiterung

Eine Website, die in einer eigenen Erweiterung lebt, hält ihre Einstellungen, Templates und ihr Design in einem
Verzeichnis, das unter Versionskontrolle gestellt und für sich bereitgestellt werden kann. Einstellungen, die sich in
`settings/siteaccess/site_b` angesammelt haben, lassen sich mit einem Befehl dorthin verschieben, der prüft, dass sich
kein wirksamer Wert ändert, und zurückrollt, wenn sich einer ändern würde. Diese Lektion verschiebt die zweite Website
der Übungsumgebung in eine Erweiterung `site_clubb`.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 3: Einstellungen, Logs und Caches pro Website](README.md) |
| Dauer | 55 Minuten (20 Präsentation, 35 Übung) |
| Niveau | Experten |
| Zielgruppe | Administratoren und Entwickler |
| Voraussetzungen | [Modul 1](../modul-01-siteaccesses-fuer-mehrere-websites/README.md): `site_b` und `site_b_ger` existieren |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- erklären, warum jede Website einer Multisite von einer eigenen Erweiterung profitiert;
- das Verschieben von Einstellungen in eine Erweiterung mit `--dry-run` vorab ansehen und beide Diffs lesen;
- jede Einstellungsdatei eines Siteaccess in eine neue, aktivierte Website-Erweiterung verschieben;
- die Regel der Ladereihenfolge erklären, durch die ein Verschieben einen Wert ändern kann, und wie der Befehl davor
  schützt;
- die Sicherung jeder geänderten Datei finden.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Einstellungen über die Kommandozeile ändern: exp:ini, Einstellungen in eine Erweiterung verschieben](../../../../../eng-us/bc/6.0/console-exp-ini.md#moving-settings-into-an-extension).

1. **Warum eine Erweiterung pro Website** (4 Minuten). Ein Verzeichnis pro Website: Einstellungen, Templates, Design
   und Übersetzungen reisen zusammen, können versioniert und einzeln bereitgestellt werden, und ein neues Teammitglied
   findet alles an einem Ort. Der Kurs Theme-Design behandelt den Design-Teil.
2. **Die Befehle** (5 Minuten):

   ```bash
   exp:ini move <file>/<Block> <from> <to>
   exp:ini move <file> <from> <to>
   exp:ini move-all <from> <to> [--files=site,content]
   ```

   Scopes für diese Lektion: `siteaccess:site_b` (Quelle) und `extension:site_clubb:siteaccess:site_b` (Ziel).
3. **Sicherheit** (6 Minuten). Zuerst wird das Ziel geschrieben, dann verlässt der Block die Quelle; Dateien werden nie
   gelöscht. Vor und nach dem Schreiben liest der Befehl den wirksamen Wert jeder verschobenen Variablen für jeden
   Siteaccess; weicht einer ab, werden beide Dateien zurückgesetzt, und das Verschieben wird mit Exit-Code 3
   abgelehnt. Die Ladereihenfolge erklärt, warum: Die Einstellungen einer Erweiterung werden vor
   `settings/siteaccess/<sa>` geladen, das vor `settings/override` geladen wird, sodass ein Wert, der aus dem Override
   in eine Erweiterung verschoben wird, gegen eine Siteaccess-Datei verlieren kann. Jede geänderte Datei wird zuerst
   nach `var/backup/ini/<timestamp>/<path>` kopiert (Modus 0600).
4. **Anlegen und aktivieren** (5 Minuten). `--create-extension` legt `extension/<ext>/` mit `settings/`, einer
   `extension.xml` und einer `ezinfo.php` an. `--activate` fügt sie bei einem Siteaccess-Ziel in einer Erweiterung zu
   `ActiveAccessExtensions[]` des Siteaccess hinzu. `ActiveExtensions` und `ActiveAccessExtensions` werden nie in eine
   Erweiterung verschoben, weil eine Erweiterung sich nicht selbst aktivieren kann.

### Häufige Fragen

**Muss ich die Autoloads neu erzeugen?** Nicht für eine Erweiterung, die nur Einstellungen enthält: Eine neue
Erweiterung hat keine Klassen. Sobald Sie PHP-Klassen hinzufügen, führen Sie `php bin/php/ezpgenerateautoloads.php -e`
aus.

**Kann ich ein Verschieben rückgängig machen?** Verschieben Sie auf demselben Weg zurück (`move-all` mit vertauschten
Scopes), oder stellen Sie die Dateien aus `var/backup/ini/` wieder her.

**Warum erkennt `--dry-run` das Problem der Ladereihenfolge nicht?** Es kann keine wirksamen Werte vergleichen, weil
nichts geschrieben wird; der Vergleich findet beim eigentlichen Verschieben statt.

**Was ist mit Velocity?** Die Worker behalten die Einstellungen aus ihrem Aufwärmen; starten Sie Velocity nach dem
Verschieben neu.

### Stolperfallen

- **`--activate` vergessen.** Das Verschieben wird abgelehnt, solange die Erweiterung inaktiv ist, und die Meldung gibt
  den Befehl `exp:ini add ...` aus, der sie aktiviert. Lesen Sie die Meldung; sie ist der nächste Schritt.
- **Namen.** Erweiterungsnamen bestehen nur aus `a-z`, `0-9` und `_`.
- **Dateien im Besitz von root.** Arbeiten Sie als der Benutzer, der die Website ausliefert. Als root behält der Befehl
  den Eigentümer einer geänderten Datei bei und gibt neuen Dateien den Eigentümer von `extension/`, aber die
  Gewohnheit, als Website-Benutzer zu arbeiten, vermeidet Überraschungen.

## Praktische Übung

Verwenden Sie Ihre Schulungsinstallation aus Modul 2. Die Befehle laufen im Wurzelverzeichnis der Installation.

1. Halten Sie die wirksamen Werte vor dem Verschieben fest:

   ```bash
   php bin/php/console exp:ini where site.ini/SiteSettings/SiteName site_b
   php bin/php/console exp:ini where content.ini/NodeSettings/RootNode site_b
   ```

2. Sehen Sie sich das Verschieben vorab an:

   ```bash
   php bin/php/console exp:ini move-all siteaccess:site_b extension:site_clubb:siteaccess:site_b \
       --create-extension --activate --dry-run
   ```

   Lesen Sie die Diffs und die Zusammenfassungszeile.
3. Führen Sie es zuerst ohne `--create-extension` und `--activate` aus und lesen Sie die Ablehnung und ihren Exit-Code
   (`echo $?`).
4. Führen Sie das vollständige Verschieben aus:

   ```bash
   php bin/php/console exp:ini move-all siteaccess:site_b extension:site_clubb:siteaccess:site_b \
       --create-extension --activate
   ```

5. Listen Sie auf, was angelegt wurde: `ls -R extension/site_clubb`.
6. Wiederholen Sie Schritt 1. Vergleichen Sie die für jeden Wert genannte Datei mit dem ersten Mal.
7. Machen Sie dasselbe für den deutschen Siteaccess:

   ```bash
   php bin/php/console exp:ini move-all siteaccess:site_b_ger extension:site_clubb:siteaccess:site_b_ger --activate
   ```

   Wird der Befehl abgelehnt, lesen Sie seine Meldung: Die Ablehnungen eines Verschiebens nennen die Option oder den
   Befehl, die fehlen (wie in Schritt 3). Führen Sie ihn mit dieser Option erneut aus.

8. Suchen Sie die Sicherungen: `ls var/backup/ini/`.
9. Starten Sie Velocity neu, wenn es die Übungsumgebung ausliefert, und öffnen Sie dann `/site_b` und `/site_b_ger`:
   Beide sehen genau aus wie vorher.
10. Lesen Sie `extension/site_clubb/extension.xml` und `ezinfo.php` und notieren Sie die Version und die Lizenz, die
    sie bekommen haben.

## Erwartetes Ergebnis

- Schritt 3 wird mit Exit-Code 3 und einer Meldung abgelehnt, die `--create-extension` nennt.
- Nach Schritt 4 enthält `extension/site_clubb/settings/siteaccess/site_b/` die Dateien `site.ini.append.php` und
  `content.ini.append.php`; `settings/siteaccess/site_b/site.ini.append.php` behält nur `ActiveAccessExtensions[]`
  (mit einem Warnhinweis, dass es bleibt).
- Schritt 6 zeigt dieselben wirksamen Werte, jetzt aus den Dateien der Erweiterung.
- Beide Websites werden wie vorher dargestellt; `var/backup/ini/` hat ein Verzeichnis mit Zeitstempel pro
  Schreibvorgang.
- Der Trainer prüft das, indem er Schritt 6 in einer Übungsumgebung am Beamer ausführt.

## Quiz

1. In welcher Reihenfolge werden diese geladen: das globale Override, die Einstellungen einer Erweiterung,
   `settings/siteaccess/<sa>`?
2. Ein Verschieben würde einen wirksamen Wert ändern. Was tut `exp:ini`?
   A. Schreibt trotzdem B. Rollt beide Dateien zurück und lehnt mit Exit-Code 3 ab C. Löscht die Quelle D. Stellt eine
   Frage
3. Warum bleiben `ActiveAccessExtensions[]` im Siteaccess-Verzeichnis?
4. Wo wird vor jedem Schreibvorgang die Kopie einer Datei angelegt?
5. Braucht eine Erweiterung, die nur Einstellungen enthält, `php bin/php/ezpgenerateautoloads.php -e`?

<details>
<summary>Antworten</summary>

1. Die Einstellungen einer Erweiterung, dann `settings/siteaccess/<sa>`, dann das globale Override, das das letzte
   Wort hat.
2. **B.** Sofern nicht `--force` angegeben ist
   ([exp:ini](../../../../../eng-us/bc/6.0/console-exp-ini.md#moving-settings-into-an-extension)).
3. Eine Erweiterung kann sich nicht selbst aktivieren: Ihre Einstellungen werden erst gelesen, wenn sie aktiv ist.
4. In `var/backup/ini/<timestamp>/<path>`, Modus 0600.
5. Nein: Sie hat keine Klassen. Sie braucht Autoloads, sobald sie PHP-Klassen bekommt.

</details>

## Weiterführende Informationen

- [Einstellungen über die Kommandozeile ändern: exp:ini](../../../../../eng-us/bc/6.0/console-exp-ini.md): jede Aktion, Option
  und jeder Exit-Code.
- [Konsole: exp:ini](../../../../../eng-us/features/6.0/exp-ini-command.md): die Feature-Seite.
- [Einstellungsdateien in einer Erweiterung](../../../../../eng-us/help/designers/extensions/settings-files-in-an-extension.md) und
  [Siteaccess-Einstellungen in einer Erweiterung](../../../../../eng-us/help/designers/extensions/siteaccess-settings-in-an-extension.md).
- [Ladereihenfolge der Erweiterungen](../../../../../eng-us/features/6.0/extension-loading-order.md).

## Verwandte Seiten

- [Modul 3: Einstellungen, Logs und Caches pro Website](README.md)
- [Multisite und Seitenpakete: Kursübersicht](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
