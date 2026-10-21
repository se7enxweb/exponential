# Die Exp-Debug-Leiste

Diese Seite ist für Entwickler und Administratoren, die eine Seite untersuchen: langsam, falsch oder voller Warnungen. Der
klassische Debug-Bericht am Ende einer Seite wurde zu einer angehefteten Leiste mit Reitern, einer Live-Zusammenfassung
und Steuerungen für die Debug-Einstellungen und die Caches, sodass Sie das Debugging einschalten, nachsehen und es wieder
ausschalten können, ohne eine INI-Datei zu öffnen. Hinzugefügt am 02.10.2026. Referenz mit jeder Serverfunktion und jedem
Test: [Anleitung zur Debug-Leiste](../../../eng-us/bc/6.0/debug-bar.md).

## Nur für Sie selbst einschalten

Zeigen Sie in `settings/override/site.ini.append.php` die Debug-Ausgabe nur Ihrer eigenen Adresse (siehe die Schalter am
Ende dieser Seite):

```ini
[DebugSettings]
DebugOutput=enabled
DebugByIP=enabled
DebugIPList[]=203.0.113.10
```

Leeren Sie den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`) und laden Sie eine Seite neu: Die
Leiste ist unten angeheftet.

## Was Sie sehen

- **Die angeheftete Leiste** zeigt Seitenzeit, Anzahl und Zeit der SQL-Abfragen, Spitzenspeicher, verwendete Templates,
  Warnungen und Fehler (gelb oder rot jenseits der Schwellen). Mit eingeschaltetem SQL-Abfrage-Cache zeigt sie außerdem
  die Trefferquote des Caches und die Datenbankzeit, die er für die Anfrage gespart hat, auch in Prozent der Zeit der
  Seite (`QC 83 % · -186 ms (186 %)`: Eine Seite von 100 ms hätte ohne den Cache etwa 286 ms gedauert); die SQL-Anzahl
  daneben zählt nur, was die Datenbank erreicht hat. Jeder Wert öffnet seinen Reiter. Der Titel „Exp Debug“ und das Logo
  sitzen am rechten Ende, die Zusammenfassung der Seite links.
- **Der Bereich** über der Leiste (standardmäßig 60 % des Fensters, eigenständig scrollend) hat die Reiter Meldungen,
  Einstellungen, Cache, Zeiten, SQL, Templates, Eingebundene Dateien, Speicher und Velocity. Jeder Abschnitt des
  klassischen Berichts ist mit seiner alten ID weiterhin da.
- **Den Bereich in der Größe ändern**: Ziehen Sie den Streifen an der Oberkante des Bereichs (Maus, Stift oder Touch),
  oder fokussieren Sie ihn und verwenden Sie die Pfeiltasten (16 px, mit Umschalt 64 px; Pos1 und Ende für die kleinste
  und größte Höhe). Ein Doppelklick gibt die Standardhöhe zurück. Die Höhe bleibt zwischen 120 px und der Höhe des
  Fensters abzüglich der Leiste und wird pro Benutzer in der Einstellung `expdebugbar_panel_height` gemerkt, sodass sich
  der Bereich auf der nächsten Seite und in einem anderen Browser in dieser Höhe öffnet. Ohne Anmeldung (die Leiste nach
  IP gezeigt) wird die Höhe im Browser gehalten (Schlüssel `exp-debug-panel-height` in `localStorage`), nie in einer
  Sitzung. Einzelheiten: [Die Höhe des Bereichs](../../../eng-us/bc/6.0/debug-bar.md#the-panels-height).
- **Meldungen kopieren** auf dem Reiter Meldungen kopiert die gezeigten Meldungen (mit angewendeten Schaltflächen für die
  Stufe und dem Filterfeld) als einfachen Text in die Zwischenablage: pro Eintrag eine Zeile „Stufe: Quelle“, ein Tab und
  die Zeit, dann die Meldung, mit einer Leerzeile zwischen den Einträgen. Es verwendet die Zwischenablage des Browsers und,
  wo diese nicht verfügbar ist (eine Seite auf einfachem http), ein verborgenes Textfeld; eine Zeile neben der
  Schaltfläche sagt, wie viele Meldungen kopiert wurden oder dass das Kopieren fehlgeschlagen ist.
- **Minimieren**: Klicken Sie auf das Exponential-Symbol am linken Ende (oder drücken Sie darauf Eingabe oder Leertaste),
  und die ganze Leiste wird zu einem Reiter von 40 px in der rechten unteren Ecke, der nur das Symbol und die Anzahl der
  Fehler zeigt.
- **Beim Neuladen offen lassen**: Die Leiste merkt sich, ob sie offen war (Schlüssel `exp-debug-keep` und
  `exp-debug-open` in `localStorage` des Browsers); ohne Haken beginnen die Details auf jeder Seite geschlossen.
- Lesbar im hellen und im dunklen Modus; kein leerer Streifen mehr unter dem Copyright.

## Einstellungen aus der Leiste ändern

Der Reiter **Einstellungen** liest und ändert jede Debug-Einstellung pro Geltungsbereich, protokolliert jede Änderung und
kann sie rückgängig machen. Der Kernel registriert 35 Einstellungen in neun Gruppen (Debug-Ausgabe, wer Debug erhält,
Templates, Datenbank, Übersetzungen, Mail, Debug-Bedingungen, Caching, Extensions); `settings/debugbar.ini` beschreibt
sie und vier Voreinstellungen (Template work, SQL tuning, Everything, Off). Extensions fügen mit eigenen Blöcken in
`debugbar.ini` Einstellungen hinzu, und die Debug-Bedingungen aus `debug.ini` werden automatisch erkannt.

Der Reiter **Cache** führt die Caches auf und leert sie über die Cache-Verwaltung, mit dem Zeitpunkt, an dem jeder zuletzt
geleert wurde (oder „nie“).

## Wer sie verwenden darf

Besucher, die den Debug-Bericht erhalten, sehen die Zusammenfassung der Seite, aber Einstellungen, Änderungsprotokoll,
IP-Test und Cache-Liste brauchen die Richtlinien `setup/setup` (Einstellungen, Protokoll, IP-Test) oder
`setup/managecache` (Cache-Liste). Bevor das am selben Tag verschärft wurde, konnte jeder Besucher, dem der Bericht
gezeigt wurde, jede Debug-Einstellung lesen, mit Dateipfaden.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/debugbar.ini` | `DebugBarSettings` | `Settings[]` | die 35 Einstellungen des Kernels | Installation |
| `settings/debugbar.ini` | `Setting_<id>` | `File`, `Block`, `Variable`, `Type`, `Label`, `Group`, `Help` | pro Einstellung | Installation |
| `settings/debugbar.ini` | `Preset_<id>` | `Name`, Werte von Einstellungen | vier Voreinstellungen | Installation |

## Der klassische Debug-Block: Überschrift, Abschnitts-IDs und Ausgabe in Telefongröße (6.0.7)

Die klassische Debug-Ausgabe (der Block am Ende einer Seite, wenn Debugging eingeschaltet ist) wurde im Januar 2025
(Release 6.0.7) leichter zu verwenden, ohne zu ändern, was sie berichtet. Die Debug-Leiste oben baut darauf auf: Jeder
Abschnitt des klassischen Berichts behält die hier aufgeführten IDs.

### Was sich geändert hat

1. **Die Überschrift sagt Ihnen, warum Sie Debug sehen.** Die Überschrift des Blocks nennt jetzt den Schalter, der ihn
   eingeschaltet hat: `(By User)`, wenn `[DebugSettings] DebugByUser` an ist, `(By IP Address)`, wenn `DebugByIP` an
   ist. Auf einem gemeinsamen Teamserver sehen Sie sofort, dass die Ausgabe nur für Sie ist und nicht für jeden
   Besucher.
2. **Jeder Abschnitt hat eine ID.** Jeder Teil des Blocks steht in einem `div` mit einer festen ID, sodass ein
   Stylesheet ihn verbergen, umordnen oder neu gestalten kann:

   | ID | Abschnitt |
   |---|---|
   | `main-debug-table` | die Tabelle der Hinweise, Warnungen und Fehler |
   | `main-resources` | „Main resources“ (Zeit, Speicher) |
   | `timing-points` | „Timing points“ |
   | `time-accumulators` | „Time accumulators“ |
   | `included-files` | „Included files“ (innere Tabelle `debug_includes`) |
   | `ezjscpackerusagecontainer` | die über den Packer geladenen CSS- und JS-Dateien (aus der Extension `ezjscore`) |

3. **Ein responsives Stylesheet.** `design/standard/stylesheets/debug.css` lässt den Block innerhalb der Seite scrollen
   (`#debug { overflow: auto; }`) und seinen Inhalt umbrechen, sodass Debugging auf einem Telefon oder in einem schmalen
   Fenster die Seite nicht verbreitert.

### Beispiele

Nur die Fehlertabelle und die Zeiten zeigen, den Rest verbergen:

```css
#debug #main-resources,
#debug #time-accumulators,
#debug #included-files,
#debug #ezjscpackerusagecontainer { display: none; }
```

Die Zeitpunkte über die Fehlertabelle verschieben (wenn `#debug` ein Flex-Container ist):

```css
#debug { display: flex; flex-direction: column; }
#debug #timing-points { order: -1; }
```

Fügen Sie die Regeln einem Stylesheet Ihrer Design-Extension hinzu.

### Die Schalter (zur Erinnerung)

Alle in `settings/site.ini`, Block `[DebugSettings]`:

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `DebugOutput` | `disabled` | Hauptschalter. Ausgeschaltet wird nichts gezeigt. |
| `DebugByIP` | `disabled` | Eingeschaltet erhalten nur die Adressen aus `DebugIPList[]` die Ausgabe. |
| `DebugIPList[]` | leer | Adressen oder Netze (`192.0.0.0/27`). |
| `DebugByUser` | `disabled` | Eingeschaltet erhalten nur die Benutzer-IDs aus `DebugUserIDList[]` die Ausgabe. |
| `DebugUserIDList[]` | leer | Benutzer-IDs (IDs der Inhaltsobjekte). |
| `ScriptDebugOutput` | `disabled` | Ausgabe für Skripte. |
| `AlwaysLog[]` | `error` | Debug-Typen, die immer ins Protokoll geschrieben werden, mit oder ohne Ausgabe. |

Schalten Sie `DebugOutput` auf einer Produktionswebsite nie für alle ein: Der Block zeigt SQL, Dateinamen und
Einstellungen. Verwenden Sie `DebugByIP` oder `DebugByUser`.

## Verwandte Seiten

- [Anleitung zur Debug-Leiste](../../../eng-us/bc/6.0/debug-bar.md), [ausführbare Klassen](../../spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md)
- [Der INI-Befehl](exp-ini-befehl.md) (dieselbe Editorklasse), [das Prüfprotokoll](../../../eng-us/features/6.0/audit-trail.md) (Änderungen an Einstellungen werden protokolliert), [Template-Pfad-Kommentare](template-pfad-kommentare.md)
- [Checkliste zur Aktualisierung vom 1. und 2. Oktober 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-10.md)
- Changelogs: [6.0.4](../../../changelogs/6.0/6.0.4.md), [6.0.7](../../../changelogs/6.0/6.0.7.md), [6.0.10](../../../changelogs/6.0/6.0.10.md), [6.0.15](../../../changelogs/6.0/6.0.15.md)
- Chronik: [Oktober 2026](../../../eng-us/history/2026/2026-10.md), [August 2025](../../../eng-us/history/2025/2025-08.md), [Januar 2025](../../../eng-us/history/2025/2025-01.md) (Überschrift und IDs, 6.0.7), [August 2024](../../../eng-us/history/2024/2024-08.md) (responsiver Block, 6.0.4)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
