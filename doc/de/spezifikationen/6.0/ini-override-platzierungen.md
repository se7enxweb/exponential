# Spezifikation: INI-Override-Verzeichnisse und Platzierungen

Diese Seite ist die Referenz dafür, wie `eZINI` die Dateien einer INI-Einstellung findet und ordnet: die vier
Geltungsbereiche, die Verzeichnisse je Siteaccess innerhalb von Extensions (hinzugefügt im Januar 2026, Commits
`124186d096` und `fc1a2f0a91`), die Platzierungsnamen, die angeben, woher ein Wert stammt, und die Konstanten, die die
Suche verändern. Lesen Sie sie, wenn eine Einstellung nicht den erwarteten Wert annimmt oder wenn Sie Einstellungen je Site
innerhalb einer Extension ablegen. Die aufgabenorientierte Seite ist
[Einstellungen je Site innerhalb von Extensions](../../../eng-us/features/6.0/multi-site-ini-overrides.md).

## Kurz gesagt

- Dateien werden in der Reihenfolge der Geltungsbereiche `sa-extension`, `siteaccess`, `extension`, `override` gelesen; eine spätere Datei gewinnt.
- Eine Extension kann Einstellungen je Siteaccess in `settings/siteaccess/<sa>/`, `settings/override__<group>/`
  und `settings/override/` mitbringen.
- `./console exp:ini` liest und schreibt dieselben Dateien und sieht daher dasselbe Ergebnis wie die Site.

## Geltungsbereiche und Reihenfolge

`eZINI` führt Override-Verzeichnisse in vier Geltungsbereichen. `eZINI::overrideDirs()` fügt sie in der Reihenfolge
`sa-extension`, `siteaccess`, `extension`, `override` zusammen. Dateien werden in dieser Reihenfolge geparst, und eine
spätere Datei überschreibt eine frühere.

Innerhalb eines Geltungsbereichs steht ein Verzeichnis, das später mit `prependOverrideDir()` registriert wird, **weiter
vorn** in der Liste und hat daher eine **niedrigere** Priorität.

## Verzeichnisse je Siteaccess innerhalb einer Extension

`eZExtension::prependExtensionSiteAccesses()` (`lib/ezutils/classes/ezextension.php`) registriert diese Verzeichnisse
für einen aktiven Siteaccess, der einer Extension gehört, in dieser Aufrufreihenfolge:

| Aufrufreihenfolge | Verzeichnis | Bezeichner | Geltungsbereich | Priorität innerhalb des Geltungsbereichs |
|---|---|---|---|---|
| 1 | `extension/<ext>/settings/override/` | `ext-siteaccess-override:<ext>` | `siteaccess` | höchste der drei |
| 2 | `extension/<ext>/settings/override__<group>/` (nur wenn der Name des Siteaccess `__` enthält; `<group>` ist der Teil davor) | `ext-siteaccess-override:<ext>:__group:<group>` | `siteaccess` | mittlere |
| 3 | `extension/<ext>/settings/siteaccess/<siteaccess>/` | der Bezeichner der Extension | `siteaccess` | niedrigste der drei |

Alle drei werden nur registriert, wenn `extension/<ext>/settings/siteaccess/<siteaccess>` existiert (ein Aufruf von
`file_exists` je aktivem Siteaccess einer Extension).

### Beispiel

Eine Extension `site_shop` besitzt die Siteaccesses `shop__en` und `shop__de`. Eine Einstellung, die beide Siteaccesses
teilen, kommt in `extension/site_shop/settings/override__shop/site.ini.append.php`; eine Einstellung nur für die deutsche
Site kommt in `extension/site_shop/settings/siteaccess/shop__de/site.ini.append.php`. Das Gruppenverzeichnis wird nur
gelesen, weil `extension/site_shop/settings/siteaccess/shop__de/` existiert.

## Platzierungen

`eZINI::findSettingPlacement()` benennt, woher eine Einstellung stammt, anhand der Anzahl der Pfadsegmente der Datei
relativ zum Wurzelverzeichnis der Installation:

| Segmente | Beispielpfad | Platzierung |
|---|---|---|
| 2 | `settings/site.ini` | `default` |
| 3 | `settings/override/site.ini.append.php` | `override` |
| 4 | `extension/<ext>/settings/site.ini.append.php` | `extension:<ext>` |
| 4 | `settings/siteaccess/<sa>/site.ini.append.php` | `siteaccess` |
| 5 | `extension/<ext>/settings/override/site.ini.append.php` | `ext-siteaccess-override:<ext>` |
| 5 | `extension/<ext>/settings/override__<group>/site.ini.append.php` | `ext-siteaccess-override__<group>:<ext>` |
| 6 | `extension/<ext>/settings/siteaccess/<sa>/site.ini.append.php` | `ext-siteaccess:<ext>` |
| (eingefügt) | zur Laufzeit eingefügte Einstellungen | `injected` |

## Konstanten und globale Variablen

| Name | Wo | Wirkung |
|---|---|---|
| `$GLOBALS['eZINI_CONFIG_CACHE_DIR']` | gelesen von `eZINI::loadCache()` | Verzeichnis des kompilierten INI-Caches. Ist es nicht gesetzt, setzt `eZINI` es auf seinen Standardwert, `var/cache/ini/` relativ zur Installation. Setzen Sie es, bevor die erste INI-Datei geladen wird, um einen Cache zwischen Projekten einer Installation zu teilen |
| `EXP_SITE_STRUCTURE_EZ_INI_OVERRIDE_DIR_LIST` | mit `define()` auf `true` setzen | Nachdem die oben genannten Verzeichnisse registriert sind, wird für diese Anfrage jedes Override-Verzeichnis des Geltungsbereichs `extension` aus der Liste entfernt, das mit `extension/site_` beginnt und nicht zur Extension des aktiven Siteaccess gehört |

## Kompatibilität

Ohne diese Verzeichnisse und ohne die globale Variable ändert sich nichts: Der Aufruf, der früher
`settings/siteaccess/<sa>/` registrierte, ist jetzt einer von dreien, an derselben Position relativ zu den anderen
Geltungsbereichen.

## Verwandte Seiten

- [Einstellungen je Site innerhalb von Extensions](../../../eng-us/features/6.0/multi-site-ini-overrides.md), [Extensions in mehr als einem Ordner](../../../eng-us/features/6.0/additional-extension-directories.md)
- [Konsole: exp:ini](../../../eng-us/features/6.0/exp-ini-command.md) (liest und schreibt Einstellungen in denselben Dateien), [INI behält Kommentare bei](../../../eng-us/bc/6.0/eZINI_PRESERVES_COMMENTS.md)
- [Changelog 6.0.12](../../../changelogs/6.0/6.0.12.md), [Chronik: Januar 2026](../../../eng-us/history/2026/2026-01.md)
