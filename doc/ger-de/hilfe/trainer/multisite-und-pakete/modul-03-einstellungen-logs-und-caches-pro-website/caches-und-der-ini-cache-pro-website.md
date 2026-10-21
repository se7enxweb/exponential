---
title: "Lektion 3.4: Caches und der INI-Cache pro Website"
audience: trainer, admin, developer
category: training
order: 34
summary: Die Teilnehmenden geben der zweiten Website einen eigenen INI-Cache, leeren ihn, ohne die anderen anzufassen, und planen CacheVarDir, ExpiryDir und ein Dateisystem im Arbeitsspeicher für viele Websites, einschließlich dessen, was Velocity und AllowedDeletionDirs brauchen. 50 Minuten.
keywords: multisite und seitenpakete, caches pro website, ini-cache, ablaufzeitstempel, arbeitsspeicher, öffentliche caches, multisite and site packages, caches per site, INICacheDir, CacheVarDir, ExpiryDir, expiry.php, tmpfs, global_ini, ini cache, ezcache.php -s, AllowedDeletionDirs, Velocity, public caches
type: help
---
# Lektion 3.4: Caches und der INI-Cache pro Website

Auf einer stark genutzten Multisite soll das Leeren eines Caches für eine Website die anderen nicht verlangsamen, und
Caches werden oft in ein Dateisystem im Arbeitsspeicher verlegt. Vier Einstellungen von `site.ini [FileSettings]`
bestimmen, wo die Caches, der INI-Cache und die Ablaufzeitstempel jeder Website liegen. Diese Lektion gibt der zweiten
Website einen eigenen INI-Cache, leert ihn für sich und plant die Einrichtung für einen Server mit vielen Websites.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 3: Einstellungen, Logs und Caches pro Website](README.md) |
| Dauer | 50 Minuten (20 Präsentation, 30 Übung) |
| Niveau | Experten |
| Zielgruppe | Administratoren und Entwickler, die mehrere Websites betreiben |
| Voraussetzungen | [Lektion 3.3](logs-pro-website.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- `CacheVarDir`, `ExpiryDir` und `INICacheDir` und ihre Voreinstellungen erklären;
- einer Website einen eigenen INI-Cache geben und ihn mit `ezcache.php -s <siteaccess>` leeren, ohne die anderen
  anzufassen;
- die Cache-IDs `ini` und `global_ini` unterscheiden;
- erklären, warum `expiry.php` den Neustart eines Dateisystems im Arbeitsspeicher überstehen muss;
- nennen, was Velocity und `[FileSettings] AllowedDeletionDirs` nach einer solchen Änderung brauchen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Logs, INI-Cache und Ablaufzeitstempel pro Website](../../../../../eng-us/features/6.0/multi-site-log-and-cache-paths.md).

1. **Die Einstellungen** (6 Minuten):

   | Schlüssel | Voreinstellung | Bedeutung |
   |---|---|---|
   | `CacheVarDir` | leer | ein Baum für die Caches aller Websites: `var_cache/<site>/cache`; innerhalb der Installation, damit ihre öffentlichen Caches ausgeliefert werden können |
   | `ExpiryDir` | leer | wo `expiry.php` aufbewahrt wird: leer bedeutet im Cache-Verzeichnis |
   | `INICacheDir` | `global` | `site` speichert die INI-Dateien der Website in `<cache directory>/ini/`; `global` in `var/cache/ini/` |

2. **Was wo geleert wird** (5 Minuten). Die Cache-Befehle und **Setup** > **Cache-Verwaltung** funktionieren wie
   bisher; die Einstellungen bestimmen nur, welches Verzeichnis ein Cache-Eintrag nennt. Die ID `ini` leert
   `<cache directory of the site>/ini/`; `global_ini` leert `var/cache/ini/`, die INI-Dateien, die gelesen werden,
   bevor ein Siteaccess bekannt ist, und die der Websites mit `INICacheDir=global`. "Clear all" leert das
   Cache-Verzeichnis der Website und `var/cache/ini/`; eine `expiry.php` in `ExpiryDir` bleibt und bekommt die neuen
   Zeitstempel.
3. **Dateisysteme im Arbeitsspeicher** (5 Minuten). `CacheVarDir=var_cache` mit einem dort eingehängten `tmpfs` legt
   die Caches aller Websites in den Arbeitsspeicher. `expiry.php` enthält die Zeitpunkte, zu denen Caches und
   Bild-Aliase zuletzt geleert wurden; würde sie mit dem Cache geleert, gälten Bild-Aliase, die vor einem Leeren
   erzeugt wurden, wieder als aktuell. `ExpiryDir=expiry` hält sie in `var/<site>/expiry/`.
4. **Ausliefern und Löschen** (4 Minuten). Velocity liefert die öffentlichen Caches unterhalb von `CacheVarDir` aus
   (gepackte Skripte und Stylesheets, Text-zu-Bild) und sonst nichts von dort; starten Sie Velocity nach einer Änderung
   daran neu. Für Apache aktivieren Sie die auskommentierte Regel `var_cache` in `.htaccess_root`. Das Leeren von
   Caches löscht nur innerhalb der Wurzel, innerhalb von `[FileSettings] AllowedDeletionDirs` und innerhalb der
   Verzeichnisse, in die die aktuelle Website schreibt; ein Eintrag in `AllowedDeletionDirs`, der nicht existiert,
   erlaubt nichts.

### Häufige Fragen

**Warum `--clear-tag=ini` in jeder anderen Lektion?** Das Tag leert jeden INI-Cache, den der Website und den globalen.
Das ist es, was Sie nach einer Einstellungsänderung wollen; die IDs dienen dazu, gezielt einen davon zu leeren.

**Kann ein absolutes `CacheDir` dasselbe wie `CacheVarDir`?** Es verlegt den Cache aus den ausgelieferten Pfaden
heraus, weshalb `settings/site.ini` es als nicht unterstützt bezeichnet; verwenden Sie `CacheVarDir` oder hängen Sie
ein Dateisystem im Arbeitsspeicher unter `var/<site>/cache` ein.

**Sind kompilierte Templates pro Website?** Sie gehen nach `<cache directory>/template/compiled/`, einmal pro Prozess
abgeleitet, sodass ein Skript, das nach dem Kompilieren den Siteaccess wechselt, sie weiter in das Cache-Verzeichnis
der ersten Website schreibt. Sie sind nach ihrem Template benannt und werden deshalb nie anstelle der der anderen
Website verwendet.

**Braucht ein Cluster noch etwas?** Mit `eZDFSFileHandler` fügen Sie die öffentlichen Caches unterhalb von
`CacheVarDir` zu den Rewrite-Regeln für `index_cluster.php` hinzu; die Feature-Seite enthält die Regel.

### Stolperfallen

- **Gemeinsames `VarDir` in der Übungsumgebung.** Beide Websites der Übungsumgebung teilen ein `VarDir` und damit ein
  Cache-Verzeichnis; die Übung zeigt die Aufteilung des INI-Caches, die trotzdem funktioniert.
- **Velocity hält Einstellungen im Arbeitsspeicher.** Starten Sie es nach dem Leeren eines INI-Caches neu, wie
  **Setup** > **Cache-Verwaltung** sagt.
- **Den Webserver vergessen.** Ein `CacheVarDir`, das Apache nicht ausliefert, macht die gepackten Stylesheets
  unerreichbar: Die Website sieht ungestaltet aus.

## Praktische Übung

Verwenden Sie Ihre Schulungsinstallation aus Lektion 3.3.

1. Listen Sie die Cache-IDs auf: `php bin/php/ezcache.php --list-ids`. Suchen Sie `ini` und `global_ini`.
2. Lesen Sie die Cache-Einstellungen für `site_b`:

   ```bash
   php bin/php/console exp:ini get site.ini/FileSettings/VarDir -s site_b
   php bin/php/console exp:ini get site.ini/FileSettings/CacheDir -s site_b
   php bin/php/console exp:ini get site.ini/FileSettings/INICacheDir -s site_b
   ```

3. Geben Sie `site_b` einen eigenen INI-Cache:

   ```bash
   php bin/php/console exp:ini set site.ini/FileSettings/INICacheDir site extension:site_clubb:siteaccess:site_b
   ```

4. Starten Sie Velocity neu, wenn es die Übungsumgebung ausliefert, und fragen Sie dann `/site_b` und `/site` je
   einmal mit `curl -s -o /dev/null` an.
5. Zählen Sie die Dateien beider INI-Caches (ersetzen Sie `var/site` durch das `VarDir` aus Schritt 2):

   ```bash
   ls var/site/cache/ini | wc -l
   ls var/cache/ini | wc -l
   ```

6. Leeren Sie nur den INI-Cache der Website und zählen Sie erneut:

   ```bash
   php bin/php/ezcache.php -s site_b --clear-id=ini
   ```

7. Leeren Sie nur den globalen INI-Cache und zählen Sie erneut: `php bin/php/ezcache.php --clear-id=global_ini`.
8. Fragen Sie beide Websites erneut an und bestätigen Sie, dass sie weiterhin mit `200` antworten.
9. Schreiben Sie auf Papier den Block `[FileSettings]` für fünfzig Websites mit ihren Caches im Arbeitsspeicher, Logs
   in einem eigenen Baum und den Ablaufzeitstempeln auf der Festplatte.

## Erwartetes Ergebnis

- Schritt 5: Beide Verzeichnisse enthalten nach den Anfragen zwischengespeicherte INI-Dateien.
- Schritt 6 leert `var/site/cache/ini` (den INI-Cache der Website) und lässt `var/cache/ini` unverändert; Schritt 7
  das Gegenteil.
- Schritt 8: Beide Websites antworten mit `200`; die Caches werden bei der nächsten Anfrage neu aufgebaut.
- Schritt 9:

  ```ini
  [FileSettings]
  UseGlobalLogDir=disabled
  LogVarDir=var_log
  CacheVarDir=var_cache
  ExpiryDir=expiry
  INICacheDir=site
  ```

  mit einem `tmpfs` unter `var_cache`, der für Apache aktivierten Regel `var_cache` und einem Neustart von Velocity.
- Der Trainer prüft die Schritte 6 und 7 in einer Übungsumgebung am Beamer.

## Quiz

1. Welche Cache-ID leert den INI-Cache einer Website mit `INICacheDir=site`?
   A. `global_ini` B. `ini` C. `content` D. `template`
2. Warum sollte `expiry.php` nicht auf einem Dateisystem im Arbeitsspeicher liegen, das beim Neustart geleert wird?
3. Was bewirkt `CacheVarDir=var_cache` für eine Website mit `VarDir=var/shop`?
4. Sie haben `CacheVarDir` geändert. Was muss für Velocity geschehen und was für Apache?
5. Ein Eintrag in `AllowedDeletionDirs` nennt ein Verzeichnis, das nicht existiert. Was erlaubt er?

<details>
<summary>Antworten</summary>

1. **B.** `global_ini` leert `var/cache/ini/`.
2. Sie enthält, wann Caches und Bild-Aliase zuletzt geleert wurden; geleert, gälten Bild-Aliase, die vor einem Leeren
   erzeugt wurden, wieder als aktuell ([Logs, INI-Cache und Ablaufzeitstempel pro Website](../../../../../eng-us/features/6.0/multi-site-log-and-cache-paths.md#a-site-with-its-cache-in-memory)).
3. Ihre Caches gehen nach `var_cache/shop/cache`.
4. Velocity neu starten (es schreibt seine Liste ausgelieferter Pfade beim Start); für Apache die Regel `var_cache` in
   `.htaccess_root` aktivieren.
5. Nichts.

</details>

## Weiterführende Informationen

- [Logs, INI-Cache und Ablaufzeitstempel pro Website](../../../../../eng-us/features/6.0/multi-site-log-and-cache-paths.md):
  jeder Fall, der Cluster und die Tests.
- [Caches](../../../../anleitungen/caches.md): die Cache-IDs und Tags.
- [Caches vor dem Löschen beiseite umbenennen](../../../../../eng-us/features/6.0/cache-clear-rename-aside.md).
- [Aufbau des Speichers](../../../hosting/betrieb/aufbau-des-speichers.md): die Sicht des Hosters auf `var/`.

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
