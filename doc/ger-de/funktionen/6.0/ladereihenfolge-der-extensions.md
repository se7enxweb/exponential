# Setup > Extensions: Ladereihenfolge und sicheres Speichern

Diese Seite ist für Administratoren, die Extensions aktivieren oder ändern, welche gewinnt. Die Reihenfolge von
`ActiveExtensions` entscheidet, wessen Einstellungen, Templates und Design-Dateien gewinnen, wenn zwei Extensions dasselbe
bereitstellen. Bis zum 28. September 2026 ließ sie sich nur durch Bearbeiten von `settings/override/site.ini.append.php`
von Hand ändern, und das Speichern der Seite Extensions konnte still Extensions abschalten. Beides ist behoben.

> **Seit dem 6. Oktober 2026** gibt es keine eigene Karte Ladereihenfolge mehr: Die Reihenfolge wird in der einen Liste
> der Seite geändert, zusammen mit Aktivieren und Deaktivieren, und erst nach einer Überprüfung geschrieben. Das Speichern
> bearbeitet die Datei jetzt als Text, sodass Kommentare darin erhalten bleiben. Mit `ExtensionOrdering=enabled`
> entscheiden die deklarierten Abhängigkeiten den größten Teil der tatsächlichen Ladereihenfolge; jede Karte zeigt, wo
> eine Extension wirklich geladen wird. Siehe [Die Seite Extensions](../../anleitungen/extensions-seite.md). Die Schritte unten
> beschreiben die Seite vor dieser Änderung.

## Die Ladereihenfolge ändern

1. Öffnen Sie **Setup > Extensions** (`/setup/extensions`).
2. Öffnen Sie die Karte **Ladereihenfolge**. Sie ist standardmäßig verborgen. Das Öffnen oder Schließen mit ihrem Pfeil
   wird als Benutzereinstellung `admin_extensions_loading_order` gemerkt, über Neuladen, Sitzungen und Browser hinweg.
   Die Überschrift zeigt auch im verborgenen Zustand, wie viele Extensions aktiv sind.
3. Die Karte führt jede aktive Extension auf (nicht nur eine Seite davon), mit ihrer Position. Ziehen Sie eine Extension
   an eine neue Stelle, oder verschieben Sie sie mit ihren Pfeilen über die Tastatur oder einen Touchscreen. Jede
   Änderung wird sofort gespeichert.
4. Lesen Sie die Meldung: gespeichert (mit dem Pfad der Sicherungskopie) oder nicht geändert und warum.
5. Wenn Sie eine Theme-Extension verschoben haben, prüfen Sie eine Seite der Website. Überschreiben zwei Extensions
   dasselbe Template, entscheidet die Reihenfolge, welches erscheint.

Die Tabelle hat eine Spalte **Reihenfolge**, sortierbar wie die anderen, mit Plaketten für aktive, nur im Siteaccess
aktive und inaktive Extensions, und einen Filter für die Zeilen der Seite. Eine Zusammenfassung darüber zählt aktive,
Siteaccess- und inaktive Extensions. Die Info-Karte zeigt die Position ebenfalls. Ohne Sortierparameter wird die Liste
nach Ladereihenfolge sortiert (wie `/(sort)/order/(dir)/asc`); ältere Links mit `SortBy` funktionieren weiter.

## Was ein Speichern tut

Das Speichern läuft über `ezpActiveExtensions`, das:

- `settings/override/site.ini.append.php` von der Platte liest (nie eine zwischengespeicherte oder gehaltene
  INI-Instanz);
- nur `[ExtensionSettings] ActiveExtensions` ändert und nie `ActiveAccessExtensions` hineinverschiebt;
- vor dem Schreiben eine Kopie in `var/<site>/backups/settings-override` ablegt, die Datei danach erneut liest und die
  Kopie zurückspielt, wenn sich sonst etwas geändert hat;
- doppelte Einträge einmal schreibt, in der Reihenfolge, in der der Kernel sie liest;
- ein Umordnen (`ezpActiveExtensions::reorder()`) nur für genau die Extensions annimmt, die jetzt aktiv sind. Eine Seite,
  die geladen wurde, bevor sich die Liste anderswo änderte, wird abgelehnt und zeigt die Reihenfolge auf der Platte. Ein
  Umordnen schaltet nie eine Extension ein oder aus;
- danach die Caches für INI, Template-Überschreibungen, design base und aktive Extensions leert, weil sie auf der
  Reihenfolge aufbauen.

Die Autoloads werden nur nach einem geprüften Speichern neu erzeugt.

## Das Speichern einer Seite schaltet die übrigen nicht mehr ab

Die Extension-Liste wird seitenweise angezeigt, aber das Speichern behandelte früher jede aktive Extension, die nicht
als angehakt gesendet wurde, als abgeschaltet. Das Speichern einer Seite schaltete daher jede aktive Extension auf den
anderen Seiten ab: Das Einschalten einer Extension hinterließ eine Website mit einem Sechstel ihrer Extensions, die mit
500 antwortete. Das Formular sendet jetzt die Extensions, die es zeigt; nur diese lassen sich abschalten, und die auf
anderen Seiten behalten ihren Zustand. Geprüft durch das Einschalten einer Extension von der dritten Seite aus: Die
übrigen 51 blieben unverändert und in Reihenfolge.

## Einen Siteaccess die Extension-Einstellungen eines anderen leihen lassen

Extensions bringen Einstellungen pro Siteaccess-Name mit (`extension/<ext>/settings/siteaccess/<name>`, was auch
`settings/override` der Extension einschaltet). Ein Übersetzungs-Siteaccess, der aus dem Benutzer-Siteaccess erzeugt
wurde, hat einen Namen, den keine Extension kennt, sodass er ohne die Template-Überschreibungen, Menüs und das
`PathPrefix` des Themes lief und seine Seiten scheiterten oder mit 404 antworteten. Beheben Sie es in der eigenen
`site.ini.append.php` des Übersetzungs-Siteaccess:

```ini
[SiteAccessSettings]
ExtensionSettingsSiteAccess=site
```

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/siteaccess/<name>/site.ini.append.php` | `SiteAccessSettings` | `ExtensionSettingsSiteAccess` | nicht gesetzt | nur dieser Siteaccess |

Die Extension-Einstellungen des genannten Siteaccess werden unterhalb derer platziert, die eine Extension für den
Siteaccess selbst mitbringt. Der Schlüssel wird nur aus der eigenen `site.ini.append.php` des Siteaccess gelesen, weil
Extension-Einstellungen platziert werden, bevor die Siteaccess-Einstellungen zusammengeführt werden.

## Verwandte Seiten

- [Modulüberschreibung durch Extensions](modulueberschreibung-durch-extensions.md), [INI-Überschreibungen für mehrere Websites](ini-ueberschreibungen-fuer-mehrere-websites.md)
- [Template-Editor: Überschreibungen anlegen, ordnen und bearbeiten](template-editor-ueberschreibungen.md), [Einrichtungsassistent und Editor-Siteaccess](einrichtungsassistent-und-editor-siteaccess.md)
- [Extension-Liste: jede Extension sortieren, untersuchen und herunterladen](extension-liste-und-downloads.md), [die Standard-Auslieferung der Extensions](standard-extension-auslieferung.md), [Extensions, Themes und Pakete](extensions/README.md)
- [Extension-Metadaten: `ezinfo.php` und `extension.xml`](../../spezifikationen/6.0/extension-metadaten.md)
- [Verhaltensänderungen, 16. bis 30. September 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Chronik: [16. bis 30. September 2026](../../../eng-us/history/2026/2026-09b.md), [Juni 2024](../../../eng-us/history/2024/2024-06.md), [März 2024](../../../eng-us/history/2024/2024-03.md), [Februar 2024](../../../eng-us/history/2024/2024-02.md), [Januar 2024, zweite Hälfte](../../../eng-us/history/2024/2024-01b.md), [Januar 2024, erste Hälfte](../../../eng-us/history/2024/2024-01a.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
