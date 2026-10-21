# Extension-Liste: jede Extension sortieren, untersuchen und herunterladen

Diese Seite ist für Administratoren, die Extensions in der Administrationsoberfläche verwalten. **Setup > Extensions**
(`/setup/extensions`) listet die Extensions auf der Platte auf und lässt Sie sie aktivieren. Seit dem 5. August 2026
(Commits `74bc4ccbd8`, `104a4b487a`, `3d125848e8`) beantwortet sie auch die Fragen, die ein Administrator tatsächlich
stellt: welche Extension ist das, welche Version, wann hat sie sich geändert, und kann ich eine Kopie davon nehmen?

> **Seit dem 6. Oktober 2026** ist die Seite eine Liste von Karten, in der Ladereihenfolge, Aktivierung und Deaktivierung
> enthalten sind, und Änderungen werden nach einer Überprüfung angewendet: siehe
> [Die Seite Extensions](../../anleitungen/extensions-seite.md). Die Liste wird nicht mehr seitenweise angezeigt und ordnet nach
> Ladereihenfolge oder nach Namen; die übrigen Werte von `(sort)` aus der Tabelle unten fallen auf die Ladereihenfolge
> zurück. Die Download-Links stehen auf jeder Karte unter **Details**, unter denselben Adressen.

> **Handlungsbedarf:** Der Export von Extensions ist jetzt standardmäßig aus — führen Sie die Extension in package.ini
> [ExtensionExportSettings] AllowedExtensions[] auf (oder `*` für jede Extension) und gewähren Sie der Rolle die
> Richtlinie package/export_extension. Ohne beides zeigt Setup > Extensions keine Download-Schaltflächen, und der
> Paketassistent lässt Sie die Extension nicht wählen; die Kommandozeile (`ezpm.php add`) braucht den Listeneintrag.

## Vor einer Aktualisierung eine Kopie einer Extension nehmen

1. Öffnen Sie **Setup > Extensions**.
2. Klicken Sie in der Zeile der Extension auf **Details** (oder den Namen). Unter der Zeile öffnet sich eine Info-Karte.
3. Drücken Sie eine der vier Download-Schaltflächen: `tar.gz`, `.zip`, `tar.bz2` oder `.ezpkg`.

Ein Download ist eine Schaltfläche des Formulars der Seite: Sie sendet `ExtensionDownload=<extension name>/<format>` mit
dem Formular-Token. Seit dem 14. Oktober 2026 lädt die Adresse `/setup/extensions/<extension name>/<format>` nichts mehr
herunter; sie zeigt die Liste. Eine Extension, die einen geheimen Wert oder Schlüsselmaterial enthält, wird nicht
gepackt, und die Seite sagt, welche Dateien und Einstellungen das verhindert haben
([die Prüfung auf Geheimnisse](../../bc/6.0/geheimnispruefung-fuer-erweiterungspakete.md)). Die Schaltflächen sind nur da, wenn die
Extension exportiert werden darf: package.ini `[ExtensionExportSettings] AllowedExtensions[]` führt sie auf (oder `*`),
und Ihre Rolle hat die Richtlinie `package/export_extension` (außer bei `RequirePolicy=disabled`); andernfalls zeigt die
Karte den Grund und die Seite einen Hinweis.

`<format>` ist eines von `tar.gz`, `tar.bz2`, `zip`, `ezpkg`. Der Server baut mit dem Paket-Handler `ezextension` ein
Paket der Extension, streamt es als Anhang namens `<name>.<format>` und entfernt die temporäre Datei danach
(`104a4b487a`). Verwenden Sie es, um vor einer Aktualisierung eine Kopie zu nehmen, um eine Extension an einen Kollegen
weiterzugeben oder um sie auf eine andere Installation zu bringen.

Nur Extensions, die unter einer der Extension-Wurzeln existieren (`extension/` oder einem
[zusätzlichen Extension-Verzeichnis](zusaetzliche-extension-verzeichnisse.md)), lassen sich herunterladen; jeder andere Name
tut nichts.

## Was die Liste zeigt

- **Standardmäßig die Ladereihenfolge** (seit dem 29. September 2026, Commit `cb5d523bde`): aktive Extensions in der
  Reihenfolge von `ActiveExtensions`, dann die nur für einen Siteaccess aktiven, dann der Rest nach Namen. Als diese
  Funktion zuerst kam (5. August, `74bc4ccbd8`), war der Standard A bis Z, natürlich und ohne Rücksicht auf Groß- und
  Kleinschreibung (`Ext2` vor `Ext10`); sortieren Sie nach **Name**, um das zurückzubekommen. Siehe
  [Setup > Extensions: Ladereihenfolge](ladereihenfolge-der-extensions.md).
- **Sortierbare Spalten.** **Reihenfolge**, **Name**, **Version**, **Geändert** und andere sind Links: Klicken Sie auf
  einen, um danach zu sortieren, erneut, um umzukehren. Die Wahl reist als Ansichtsparameter mit, sodass die
  Seitennavigation sie behält: `/setup/extensions/(sort)/version/(dir)/desc`. `sort` ist eines von `order`, `name`,
  `info_name`, `license`, `version`, `mtime`; `dir` ist `asc` oder `desc`. Jeder andere Wert fällt auf `order` und `asc`
  zurück. Die Form vom August `/setup/extensions?SortBy=version&SortOrder=desc` wird weiterhin gelesen, sodass
  Lesezeichen funktionieren (`kernel/private/classes/views/setup/extensions.php`).
- **Version** ist die Version, die die Extension angibt, gelesen aus `extension.xml`, `ezinfo.php` oder `composer.json`
  (siehe [Extension-Metadaten](../../spezifikationen/6.0/extension-metadaten.md)). Ein Strich bedeutet, dass keine angegeben
  ist.
- **Geändert** ist die Zeit der neuesten Datei in der Extension, nicht die ihres Ordners, sodass das Bearbeiten eines
  tief liegenden Templates das Datum verschiebt.
- **Details** öffnet die Info-Karte: Beschreibung, Autor, Lizenz, Info-URL und die vier Download-Links. Es ist immer nur
  eine Karte offen.

Die Kontrollkästchen zur Aktivierung und die Schaltfläche **Aktivieren** funktionieren wie bisher.

## Die Liste des Paketassistenten

**Setup > Pakete > Paket erstellen > Extension** (`package/create`, Ersteller `ezextension`) zeigt dieselbe Liste von A
bis Z als Tabelle mit Version, Geändert und einer Karte Details, ohne die Download-Links (`3d125848e8`). Eine Extension,
die nicht exportiert werden darf (siehe den Hinweis oben), lässt sich dort nicht wählen, und der Schritt sagt, warum.

## Für Entwickler

`eZExtension::extensionInfo( $name )` (`lib/ezutils/classes/ezextension.php`) gibt die Daten zurück, die die Seite
verwendet: `name`, `version`, `mtime`, `mtime_formatted` und das Array `meta` (Beschreibung, Autor, Lizenz,
`info_url`). Für eine unbekannte Extension gibt es `null` zurück. `eZPackage::exportToArchive( $archivePath, $format )`
nimmt die vier Formate an.

Der Gestaltungsvorschlag, der dazu führte, mit den offenen Fragen, wird in
[EXT_SORT_ORDERS](../../bc/6.0/ext-sort-orders.md) aufbewahrt.

## Grenzen

- Große Extensions brauchen einen Moment, um nach der neuesten Datei durchsucht zu werden; die Liste wird nicht
  zwischengespeichert.
- Ein Download packt die Extension, wie sie auf der Platte liegt. Extensions mit geheimen Werten oder
  Schlüsselmaterial werden abgelehnt, aber die Prüfung kann nicht jedes Geheimnis kennen: Sehen Sie hinein, bevor Sie
  sie weitergeben.

## Verwandte Seiten

- [Setup > Extensions: Ladereihenfolge](ladereihenfolge-der-extensions.md), [zusätzliche Extension-Verzeichnisse](zusaetzliche-extension-verzeichnisse.md)
- [Extension-Metadaten](../../spezifikationen/6.0/extension-metadaten.md), [Seiten Über und Copyright](info-und-paketseiten.md)
- [Standard-Auslieferung der Extensions](standard-extension-auslieferung.md), [Pakete vergleichen und importieren](pakete-vergleichen-und-importieren.md), [Extensions, Themes und Pakete](erweiterungen/README.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Chronik: [August 2026](../../geschichte/2026/2026-08.md), [Juni 2024](../../geschichte/2024/2024-06.md), [März 2024](../../geschichte/2024/2024-03.md), [Februar 2024](../../geschichte/2024/2024-02.md), [Januar 2024, zweite Hälfte](../../geschichte/2024/2024-01b.md), [Januar 2024, erste Hälfte](../../geschichte/2024/2024-01a.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
