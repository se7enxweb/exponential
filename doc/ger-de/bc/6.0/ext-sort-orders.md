# Setup > Extensions: Sortierung, Metadaten und Download

Lesen Sie diese Seite, wenn Sie das Template von **Setup > Extensions** oder die Erweiterungsliste des
Paket-Assistenten überschreiben oder wissen möchten, wie sich die Erweiterungsliste geändert hat. Seit dem 5. August
2026 ist die Liste sortiert, zeigt die Version und die letzte Änderung jeder Erweiterung und bietet einen direkten
Download an. Die Seite bewahrt außerdem den ursprünglichen Entwurf und die Antworten auf seine offenen Fragen. Wie Sie
die Seite verwenden, beschreibt
[Erweiterungsliste: jede Erweiterung sortieren, untersuchen und herunterladen](../../../eng-us/features/6.0/extension-list-and-downloads.md).

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | `/setup/extensions` ist sortierbar und zeigt Version, Geändert und eine Karte Details mit Downloads; `package/create` (Extension) zeigt dieselbe sortierte Tabelle ohne Downloads. |
| Wer betroffen ist | Installationen, die `design/admin/templates/setup/extensions.tpl` oder `design/standard/templates/package/creators/ezextension/extension.tpl` überschreiben. |
| Wie Sie es prüfen | Öffnen Sie `/setup/extensions`; die Spalten Reihenfolge, Name, Version und Geändert sind Links. |
| Wie Sie es beheben | Führen Sie Ihr Override mit dem ausgelieferten Template zusammen; die Kontrollkästchen der Aktivierung und die Aktion `ActivateExtensions` sind unverändert. |

## Was sich geändert hat

Bisher führte `setup/extensions` die verfügbaren Erweiterungen in der Reihenfolge des Dateisystems auf (der
Reihenfolge von `eZExtension::extensionRootDirectories()` und `eZDir::findSubItems()`), in einem Raster aus zwei
Spalten: ein Kontrollkästchen und der Name. Es gab keine Version, kein Änderungsdatum und keinen Download.
`package/create` (Creator `ezextension`) verwendete dieselbe unsortierte Liste in
`design/standard/templates/package/creators/ezextension/extension.tpl`.

Jetzt (Commits `74bc4ccbd8`, `104a4b487a`, `3d125848e8`):

- **Sortierung.** Die Spaltenköpfe sind Links; die Wahl wird als View-Parameter übertragen,
  `/setup/extensions/(sort)/version/(dir)/desc`. Standard ist seit dem 29. September 2026 die Ladereihenfolge
  (`cb5d523bde`); am 5. August war es A bis Z, natürlich und ohne Beachtung der Groß- und Kleinschreibung. Siehe
  [Setup > Extensions: Ladereihenfolge](../../../eng-us/features/6.0/extension-loading-order.md).
- **Version** stammt aus `extension.xml`, `ezinfo.php` oder `composer.json`
  ([Metadaten von Erweiterungen](../../spezifikationen/6.0/extension-metadaten.md)); ein Strich bedeutet, dass
  keine angegeben ist.
- **Geändert** ist der Zeitpunkt der neuesten Datei innerhalb der Erweiterung, nicht der ihres Ordners.
- **Download** als `tar.gz`, `zip`, `tar.bz2` oder `ezpkg`: eine Schaltfläche des Formulars der Seite, die
  `ExtensionDownload=<extension name>/<format>` mit dem Formular-Token sendet. Der Server baut das Paket mit dem
  Paket-Handler `ezextension`, streamt es und entfernt die temporäre Datei. Seit dem 14. Oktober 2026 lädt die Adresse
  `/setup/extensions/<extension name>/<format>` nicht mehr herunter (sie zeigt die Liste), und eine Erweiterung, die
  einen geheimen Wert oder Schlüsselmaterial enthält, wird nicht gepackt
  ([die Prüfung auf Geheimnisse](geheimnispruefung-fuer-erweiterungspakete.md)).
- **Paket-Assistent.** Setup > Pakete > Paket erstellen > Extension zeigt dieselbe sortierte Tabelle mit Version,
  Geändert und einer Karte Details, ohne Download-Links.
- **Entwickler-API.** `eZExtension::extensionInfo( $name )` in `lib/ezutils/classes/ezextension.php` gibt `name`,
  `version`, `mtime`, `mtime_formatted` und das Array `meta` zurück, oder `null` für eine unbekannte Erweiterung.
  `eZPackage::exportToArchive( $archivePath, $format )` akzeptiert die vier Formate.

## So prüfen Sie es

1. Öffnen Sie `/setup/extensions` in der Administration. Die Spaltenköpfe Reihenfolge, Name, Version und Geändert
   sind Links, und **Details** öffnet eine Karte mit vier Download-Links.
2. Öffnen Sie **Details** in der Zeile von ezflow und klicken Sie auf **.zip**. Der Browser lädt `ezflow.zip`
   herunter.
3. Öffnen Sie Setup > Pakete > Paket erstellen > Extension. Die Liste ist eine sortierte Tabelle mit Version und
   Geändert.

## So passen Sie ein Override an

Wenn Sie eines der beiden Templates überschreiben, vergleichen Sie es mit dem ausgelieferten:

```bash
diff design/admin/templates/setup/extensions.tpl <your override>
diff design/standard/templates/package/creators/ezextension/extension.tpl <your override>
```

Der `value` des Kontrollkästchens und die Logik mit `contains` haben sich nicht geändert, daher aktiviert ein altes
Override weiterhin Erweiterungen; ihm fehlen nur die neuen Spalten und die Download-Links.

## Hintergrund: der ursprüngliche Entwurf

Die Änderung begann als Vorschlag mit vier Zielen: die Liste von A bis Z sortieren (natürlich, ohne Beachtung der
Groß- und Kleinschreibung); das Datum der letzten Änderung zeigen; die Version zeigen, wo sie sich finden lässt; und
optional einen direkten Download als `tar.gz` oder `.zip` anbieten, damit ein Administrator nicht den vollständigen
Paket-Assistenten braucht.

Er schlug eine gemeinsame Hilfsfunktion für Metadaten in `lib/ezutils/classes/ezextension.php` vor, die sowohl
`setup/extensions` (`kernel/setup/extensions.php`) als auch der Paket-Creator
(`kernel/classes/packagecreators/ezextension/ezextensionpackagecreator.php`) verwenden, ein Raster aus Aktivieren,
Name, Version, Geändert und Download in `design/admin/templates/setup/extensions.tpl` und eine Download-Aktion, die
`eZPackage` mit dem Handler `ezextension` wiederverwendet und das Archiv mit `Content-Disposition: attachment` streamt.

Wie die Umsetzung seine offenen Fragen beantwortet hat:

| Frage | Antwort |
|---|---|
| mtime des Verzeichnisses oder neueste Datei? | Neueste Datei innerhalb der Erweiterung. |
| Download Teil dieser Änderung oder eine eigene? | Teil davon (`104a4b487a`), in vier Formaten. |
| Wo stehen die Download-Links? | Nur auf `setup/extensions`; der Paket-Assistent zeigt die Tabelle ohne sie. |
| Welche Quelle der Version ist maßgeblich? | `extension.xml`, dann `ezinfo.php`, dann `composer.json` (siehe die Spezifikation der Metadaten). |
| Sortieren per Klick oder nur A bis Z? | Sortieren per Klick auf jeden Spaltenkopf. |

## Verwandte Seiten

- [Erweiterungsliste: jede Erweiterung sortieren, untersuchen und herunterladen](../../../eng-us/features/6.0/extension-list-and-downloads.md)
- [Setup > Extensions: Ladereihenfolge](../../../eng-us/features/6.0/extension-loading-order.md)
- [Metadaten von Erweiterungen](../../spezifikationen/6.0/extension-metadaten.md)
- [Die Klasse expInfo und der Operator expinfo](expinfo-operator.md)
- [Zusätzliche Erweiterungsverzeichnisse](zusaetzliche-erweiterungsverzeichnisse.md)
- [August 2026](../../../eng-us/history/2026/2026-08.md)
