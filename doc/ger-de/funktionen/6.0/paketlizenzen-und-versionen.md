# Paketlizenzen und Semantic Versioning

Diese Seite ist für alle, die Pakete oder Extensions erstellen, um sie weiterzugeben. Jedes Paket, das Sie in der
Administrationsoberfläche erstellen (und jede Extension, die die RAD-Assistenten schreiben), nennt seine Lizenz jetzt aus
einer strengen Liste, und Paketversionen folgen Semantic Versioning. Sie können kein Paket mehr ausliefern, dessen Lizenz
ein Tippfehler ist, und Sie können der Liste mit einem Einstellungsblock Ihre eigene Lizenz hinzufügen.

## Beim Erstellen eines Pakets eine Lizenz wählen

**Setup > Pakete > Neues Paket erstellen**, im Schritt *Paket Informationen* der Assistenten für Inhaltsobjekte,
Inhaltsklassen, Extensions und Website-Stile:

1. Öffnen Sie die Auswahlliste **Lizenz**. Die Lizenzen sind gruppiert (Software, Dokumentation, Creative Commons 4.0
   bis 1.0, gemeinfrei, proprietär).
2. Wählen Sie eine. Neben der Auswahl erscheint ein Link zur eigenen Seite der Lizenz.
3. Fahren Sie fort. Der Server nimmt nur einen konfigurierten Bezeichner (oder einen Alias wie `GPL`) an und lehnt alles
   andere mit einer Meldung ab.

Der Bezeichner wird in `package.xml` gespeichert (`<licence>`). `package/view/full` zeigt den Namen der Lizenz als Link
zu ihrer Seite, zusammen mit ihrem Bezeichner. Pakete, die `GPL` gespeichert haben, erscheinen als `GPL-2.0-or-later`;
jeder andere gespeicherte Text wird so gezeigt, wie er ist. Ein Paket, dessen Lizenz nicht `GPL-2.0-or-later` ist,
erhält ein Dokument `LICENCE`, das sie nennt.

### In `settings/package.ini` mitgelieferte Lizenzen

Standard: `GPL-2.0-or-later`, die Lizenz von Exponential selbst und das, was „GPL“ immer bedeutete.

| Gruppe | Bezeichner |
|---|---|
| Software-Lizenzen | `GPL-2.0-or-later`, `GPL-2.0-only`, `GPL-3.0-or-later`, `GPL-3.0-only`, `LGPL-2.1-or-later`, `LGPL-2.1-only`, `LGPL-3.0-or-later`, `LGPL-3.0-only`, `AGPL-3.0-or-later`, `AGPL-3.0-only`, `MIT` |
| Dokumentationslizenzen | `GFDL-1.3-or-later`, `GFDL-1.3-only` |
| Creative Commons 4.0 / 3.0 / 2.5 / 2.0 / 1.0 | `CC-BY`, `CC-BY-SA`, `CC-BY-ND`, `CC-BY-NC`, `CC-BY-NC-SA`, `CC-BY-NC-ND`, jeweils mit angehängter Version (zum Beispiel `CC-BY-4.0`, `CC-BY-NC-SA-3.0`; 30 Bezeichner, insgesamt 46 Lizenzen) |
| Gemeinfrei | `CC0-1.0`, `LicenseRef-PDM-1.0` |
| Proprietäre Lizenzen | `LicenseRef-Proprietary` (alle Rechte vorbehalten; es hat keine Seite, auf die verlinkt werden kann) |

Bezeichner sind SPDX-Bezeichner, wo es einen gibt, sonst ein `LicenseRef-<name>` im Stil von SPDX.

### Einstellungen

`settings/package.ini`

| Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|
| `[LicenseSettings]` | `DefaultLicense` | `GPL-2.0-or-later` | Vorausgewählte Wahl. Muss in `LicenseList` stehen. |
| `[LicenseSettings]` | `GroupList[]` | software, documentation, cc-4.0 ... cc-1.0, public-domain, proprietary | Gruppen der Auswahlliste in Reihenfolge. Eine Lizenz, deren Gruppe nicht aufgeführt ist, erscheint unter „Other licenses“. |
| `[LicenseSettings]` | `LicenseList[]` | die Bezeichner oben | Lizenzen, die sich wählen lassen, in Reihenfolge innerhalb ihrer Gruppe. |
| `[LicenseSettings]` | `AliasList[<name>]` | `AliasList[GPL]=GPL-2.0-or-later` | Alte Namen, die angenommen und auf einen Bezeichner abgebildet werden. |
| `[License_<identifier>]` | `Name`, `URL`, `Group`, `Description` | pro Lizenz | Offizieller Name (nicht übersetzt), Seite, Gruppe, optionaler Text unter der Auswahlliste. |
| `[LicenseGroup_<group>]` | `Name` | pro Gruppe | Überschrift der Gruppe. |
| `[CreationSettings]` | `HandlerList[]` | `ezcontentobject`, `ezcontentclass`, Extension, Website-Stil | Reihenfolge der Assistenten auf der Seite Paket erstellen: zuerst Inhalte, dann ihre Klassen, dann Extensions, dann Website-Stile. |

Geltungsbereich: die ganze Installation; Extensions und `settings/override` können die Liste ergänzen oder ersetzen.

### Eine eigene Lizenz hinzufügen

`settings/override/package.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[LicenseSettings]
LicenseList[]=Apache-2.0

[License_Apache-2.0]
Name=Apache License 2.0
URL=https://www.apache.org/licenses/LICENSE-2.0
Group=software

*/ ?>
```

Um die ganze Liste zu ersetzen, beginnen Sie mit einer leeren Zeile `LicenseList[]`, führen nur die gewünschten Lizenzen
auf und setzen dann `DefaultLicense` auf eine davon. Leeren Sie danach den INI-Cache.

## Die RAD-Assistenten verwenden dieselbe Liste

Der Datentyp-Assistent und jeder Extension-Assistent unter Setup > RAD (Inhalt, Design, Handler,
Kernel-Überschreibung, Modul, Modulerweiterung, Einstellungen, Template und Workflow-Ereignis) boten drei feste Lizenzen
an und ersetzten alles andere still durch die erste. Jetzt bieten sie dieselbe strenge Auswahl, gleich gruppiert, mit
dem konfigurierten Standard vorausgewählt, aus einem gemeinsamen Template für die Auswahlliste. Das `problems()` jedes
Assistenten lehnt eine Lizenz ab, die nicht konfiguriert ist (Bezeichner und Aliase aus package.ini zählen, ebenso die
Kurznamen, die die Assistenten früher schrieben, etwa `GPL-2.0`, wenn die Lizenz, für die sie stehen, konfiguriert ist).

Die erzeugten `ezinfo.php`, `extension.xml` und `composer.json` tragen den gewählten SPDX-Bezeichner; die Dateihinweise,
die Zeile im Readme und `LICENSE` nennen die gewählte Lizenz, mit dem Wortlaut der Free Software Foundation für die
GNU-Lizenzen. Der erzeugte Text für `GPL-2.0-or-later` ist unverändert. MIT und proprietär werden nur angeboten, wenn
`package.ini` sie aufführt.

## Versionen: Semantic Versioning

- Neue Pakete beginnen bei `1.0.0`, nicht bei `1.0`.
- Die Erstellungsassistenten nehmen eine strenge Version nach Semantic Versioning 2.0.0 an: `MAJOR.MINOR.PATCH` mit
  optionalem `-prerelease` und `+build`, höchstens 24 Zeichen, damit `<version>-<release>` in die Tabelle `ezpackage`
  passt. Die alte Prüfung erlaubte nur eine Ziffer pro Teil.
- `eZPackageVersion` liest, prüft und vergleicht Versionen an einer Stelle. Bestehende Pakete werden gelesen, wie sie
  sind, und nie neu geschrieben:
  - `1.0` zählt als `1.0.0`
  - vier Teile werden nach der Patch-Nummer verglichen
  - ein Buchstaben-Suffix wie `3.4.0beta1` ist eine Vorabversion
  - in einem Paar `<version>-<release>` wie `1.0-1` ist eine nachgestellte Zahl das Release, verglichen nach der Version
  - unlesbare Versionen werden zuerst einsortiert
- Die Anforderung eines Website-Pakets im Einrichtungsassistenten und die Prüfungen auf neuere Versionen verwenden es
  statt `version_compare()`, das `1.0-1` nach `1.0.0-1` einordnete.

## Verwandte Seiten

- [Pakete vergleichen und importieren](pakete-vergleichen-und-importieren.md), [ezpm auf der Kommandozeile](ezpm-paketmanager-cli.md), [RAD-Werkzeuge](rad-werkzeuge.md)
- [Spezifikation der Extension-Metadaten](../../spezifikationen/6.0/extension-metadaten.md), [Seiten Über und Copyright](info-und-paketseiten.md), [Dateikonsistenzprüfung und die Dateiliste des Releases](dateikonsistenzpruefung.md)
- [Das Produkt heißt Exponential](umbenennung-in-exponential.md), [eine saubere Installation, die zu Shared Hosting passt](standardwerte-einer-sauberen-installation.md)
- [Verhaltensänderungen, 16. bis 30. September 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Chronik: [16. bis 30. September 2026](../../../eng-us/history/2026/2026-09b.md), [Juni 2026, zweite Hälfte](../../../eng-us/history/2026/2026-06b.md), [Juni 2026, erste Hälfte](../../../eng-us/history/2026/2026-06a.md), [Mai 2026](../../../eng-us/history/2026/2026-05.md), [April 2026](../../../eng-us/history/2026/2026-04.md), [August 2025](../../../eng-us/history/2025/2025-08.md), [Juli 2025](../../../eng-us/history/2025/2025-07.md), [Januar 2025](../../../eng-us/history/2025/2025-01.md)
