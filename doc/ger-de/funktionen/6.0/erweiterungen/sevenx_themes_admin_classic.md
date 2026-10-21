# sevenx_themes_admin_classic: zwischen admin3 und dem klassischen Rückfall umschalten

Diese Seite ist für Administratoren mit Legacy-Arbeitsabläufen, die die klassischen, auf dem Server ausgegebenen
Admin-Templates brauchen. `sevenx_themes_admin_classic` lässt einen Administrator pro Sitzung umschalten zwischen:

- dem Design **admin3** allein: `AdditionalSiteDesignList` ist `admin3, admin`;
- **admin3 mit dem klassischen Admin-Design als Rückfall**: `AdditionalSiteDesignList` ist
  `admin3, admin_classic, admin`.

`SiteDesign` bleibt in beiden Fällen `admin3`. Die Extension wendet den Sitzungswert über eine
**Kernel-Überschreibung** an, sodass die globale Konfiguration nie geändert wird.

## Einrichten

1. Aktivieren Sie die Extension: `ActiveExtensions[]=sevenx_themes_admin_classic`.
2. Erlauben Sie Kernel-Überschreibungen in `config.php`:

```php
define( 'EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE', true );
```

3. Erzeugen Sie die Autoloads der Kernel-Überschreibungen:

```bash
php ./bin/php/ezpgenerateautoloads.php --kernel-override
```

4. Fügen Sie im Admin-Siteaccess die zwischengespeicherten Ansichtseinstellungen hinzu, die die
   Unterelement-Steuerungen der Extension brauchen (das README nennt den genauen Block `CachedViewPreferences`).
5. Binden Sie den Namen des gemeinsamen Admin-Siteaccess in `sevenxthemesadminclassic.ini` (siehe unten). Halten Sie
   umgebungsspezifische Werte in `settings/override`, nicht in den Standardwerten der Extension.

## Verwenden

Klicken Sie auf die Umschalt-Schaltfläche in der Werkzeugleiste der Administrationsoberfläche, unter den
Entwicklersteuerungen, direkt unter **Cache leeren**. Ein Modul-Endpunkt schaltet den Modus in der Sitzung um, und der
Template-Cache wird nach jedem Umschalten oder Zurücksetzen geleert.

Im klassischen Modus schaltet ein Fenster-Steuerelement die Anzeige der **Unterelemente** ein oder aus. Das Blättern
verwendet eine Grenze aus den Einstellungen (anklickbare Voreinstellungen und eine eigene Eingabe mit **Setzen** für
größere Listen) statt einer fest eingetragenen niedrigen Grenze.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `sevenxthemesadminclassic.ini` | `SevenXThemesAdminClassicSettings` | `SharedAdminSiteaccessName` | `sevenx_site_admin` | Admin-Siteaccess, für den der Schalter gilt |
| `sevenxthemesadminclassic.ini` | `SevenXThemesAdminClassicSettings` | `SwitchAdminDesignSessionVariableName` | `SevenXThemesAdminClassicDesignEnabled` | Sitzungsvariable des Modus |
| `sevenxthemesadminclassic.ini` | `SevenXThemesAdminClassicSettings` | `SubitemsDefaultLimit`, `SubitemsMaxLimit` | `200`, `5000` | Unterelemente pro Seite im klassischen Modus |

## Releases

| Version | Datum | Änderung |
|---|---|---|
| 0.2.0 | 20. Juni 2026 | Erstes öffentliches Release: Umschalten des Designs, Cache-sichere Laufzeit, klassische Steuerungen für Unterelemente; Versionshinweise im Repository. |
| 0.2.1 | | Lizenzdateien der GPL v2. |
| 0.2.2 | | Dokumentation der benötigten `CachedViewPreferences` für Admin-Siteaccesses. |
| 0.2.3 | | Die Ausnahmeliste der Richtlinie `switchadmindesign` bleibt in den Standardwerten der Extension auskommentiert. |
| 0.2.4 | | Die Weiterleitung beim Umschalten behält die aktuelle URI und ist gehärtet; Dokumentation des Verhaltens von Werkzeugleiste und Richtlinie. |
| | 21. Juni 2026 | Kleine Verbesserungen an der Position in der Werkzeugleiste. |

## Grenzen

Die Versionshinweise (`RELEASE_NOTES_v0.2.0.md` bis `RELEASE_NOTES_v0.2.4.md`) und die Tags reichen bis 0.2.4, aber
`ezinfo.php` gibt noch `'Version' => '0.1.0'` an, und `extension.xml` hat ein älteres Format (`version="0.1.0"` als
Attribut, kein `<metadata>`). Die Info-Seite (`/ezinfo/about`) zeigt daher 0.1.0, gleich, welches Release Sie
installiert haben. Lesen Sie das Release aus dem Tag ab (`git describe --tags` im Klon), bis die Metadaten angeglichen
sind (siehe die [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)).

## Verwandte Seiten

- [admin3: die responsive Administrationsoberfläche](../admin3-responsives-admin.md)
- [Optionen der Unterelementtabelle](../../../bc/6.0/tabellenoptionen-der-unterelemente.md)
- [Chronik](../../../geschichte/erweiterungen/sevenx_themes_admin_classic.md) und [Versionshinweise](../../../../changelogs/extensions/sevenx_themes_admin_classic.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/sevenx_themes_admin_classic.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- [Monat: 2026-06 (alle Extensions)](../../../geschichte/erweiterungen/monate/2026-06.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
