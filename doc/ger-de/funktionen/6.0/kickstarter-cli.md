# Kickstarter: eine ganze Website aus einer Datei installieren

Diese Seite ist für Administratoren, die dieselbe Website mehr als einmal oder auf mehreren Servern installieren. Der
Einrichtungsassistent fragt nach der Datenbank, den Sprachen, dem Website-Paket, dem Administrator und mehr. Der
Kickstarter nimmt all diese Antworten aus einer Datei, `kickstart.ini`, sodass sich dieselbe Installation mit einem
Befehl wiederholen lässt. Hinzugefügt am 12. Juli 2026 (`34c17cdbbe`). Vollständige Referenz:
[Kickstarter-CLI](../../bc/6.0/kickstartercli.md); diese Seite ist die Kurzfassung.

## In drei Schritten installieren

```bash
# 1. write kickstart.ini from the template (asks questions; --yes accepts defaults)
php bin/php/console exp:kickstarter ini

# 2. check the answers and the remote packages without changing anything
php bin/php/console exp:kickstarter run --dry-run

# 3. install (needs --force because it can drop the database)
php bin/php/console exp:kickstarter run --force
```

Die Schritte ansehen, bevor Sie sie ausführen:

```bash
php bin/php/kickstarter.php run --list-steps
```

`bin/php/kickstarter.php` ist dasselbe Programm ohne die Konsole.

## Befehle und Optionen

Geprüft mit `./console help exp:kickstarter` auf 6.0.15:

| Befehl | Optionen |
|---|---|
| `ini` | `--defaults` (kopiert `kickstart.ini-dist`), `--yes` |
| `run` | `--start-step`, `--stop-step`, `--dry-run`, `--list-steps`, `--force` |
| `help` | |

Jeder Lauf wird mit maskierten Passwörtern in `var/log/kickstart.log` protokolliert. `EXP_KICKSTART_LOG=0` schaltet das
Protokoll ab.

## Gut zu wissen

> **Unterstützte Datenbanken (Exponential 6.0.x), in der Reihenfolge der Präferenz:** 1. SQLite 3 (der Standard) ·
> 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL. Microsoft SQL Server ist derzeit mit zusätzlichem
> Aufwand bekanntermaßen möglich. Einzelheiten: [Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md).

- **`kickstart.ini` ist ein Beispiel, kein Standard.** Eine `kickstart.ini` im Wurzelverzeichnis galt für jede
  Einrichtung im Web und hätte mit einer sauberen Installation in Konflikt gestanden. Am 16. und 19. Juli
  (`d0530e1deb`, `f9df1f6854`) wurde die Datei zu `kickstart.ini--example`, und die Vorlage `kickstart.ini-dist`
  erzeugt Ihre. Halten Sie Ihre eigene `kickstart.ini` aus der Versionsverwaltung heraus: Sie enthält das Passwort der
  Datenbank.
- **Entfernte Pakete lassen sich testen.** `--dry-run` lädt die vom Website-Typ genannten Pakete in ein
  Paket-Repository namens `dryrun` herunter und prüft sie (`kernel/setup/steps/ezstep_site_types.php`) und hält vor
  `CreateSites` an, sodass nichts in die Datenbank geschrieben wird.
- **Eine Fortschrittszeile.** Die Skripte verwenden `expScriptStatus` (umbenannt von `eZPMStatus`), sodass die
  Einrichtung im Web und die Konsole den Fortschritt auf dieselbe Weise melden.
- **Lokale Pakete zuerst.** Liegt ein Paket in der Installation vor, verwendet der Schritt für den Website-Typ dieses,
  statt es herunterzuladen (15. August, `28d414f422`).
- **Namen der Siteaccesses.** Welchen Zugangstyp Sie auch wählen (Hostname, Port oder URL), die Siteaccesses heißen
  `site` und `admin` (15. August, `67e2abf9a3`). Frühere Läufe erzeugten Namen wie `<identifier>_user`, die es nicht
  gab.
- **Weitere Einstellungen**, etwa der Editor-Siteaccess, kamen später hinzu; siehe die
  [Seite zum Einrichtungsassistenten](einrichtungsassistent-und-editor-siteaccess.md).

## Verwandte Seiten

- [Installation mit einem Befehl](installation-mit-einem-befehl.md): braucht keine `kickstart.ini`
- [Standardwerte einer sauberen Installation](standardwerte-einer-sauberen-installation.md)
- [ezpm, der Paketmanager auf der Kommandozeile](ezpm-paketmanager-cli.md): installiert einzelne Pakete
- [Eine Paketinstallation, die große Pakete übersteht](paketinstallation-in-teilschritten.md)
- [In ein Paket hineinsehen, es mit Ihrer Website vergleichen, einzelne Elemente importieren](pakete-vergleichen-und-importieren.md)
- [Standard-Auslieferung der Extensions](standard-extension-auslieferung.md)
- [Generator für den statischen Cache](generator-fuer-den-statischen-cache.md)
- [Installer-Protokolle und Startdaten](../../spezifikationen/6.0/installer-protokolle-und-startdaten.md)
- [Verhaltensänderungen von Juli und August 2026](../../bc/6.0/verhaltensaenderungen-2026-07-08.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Chronik: [Juni 2026, zweite Hälfte](../../geschichte/2026/2026-06b.md), [Juli 2026](../../geschichte/2026/2026-07.md), [August 2026](../../geschichte/2026/2026-08.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
