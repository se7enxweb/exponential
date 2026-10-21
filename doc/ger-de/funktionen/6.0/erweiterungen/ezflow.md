# ezflow: Seiten, Zonen und Blöcke

Diese Seite ist für Website-Entwickler und Redakteure, die Startseiten und Landingpages zusammenstellen. `ezflow` („eZ
Flow LS“) ist der Seitenbaukasten des klassischen Designs. Er stellt einen Datentyp **page** bereit, dessen Inhalt in
**Zonen** angeordnet ist (zum Beispiel links und rechts), die **Blöcke** enthalten (eine Liste von Elementen, eine
Kampagne, ein Banner, RSS, eine Schlagwortliste und so weiter).

Redakteure ordnen Blöcke per Drag-and-drop um, schieben ein Objekt aus der Inhaltsansicht in einen Block, planen, wann
Elemente in einem Block erscheinen, und sehen die Änderung auf einer **Zeitleiste**. Die Extension benötigt `ezjscore`,
erweitert `ezwebin` und `ezwt` und bringt Workflow-Ereignistypen, einen Datentyp, Cronjobs und ein Flash-Diagrammmodul
(`modules/flash`) mit. Version 6.1.5 (2. Oktober 2026) ist aktuell.

## Verwenden

1. Geben Sie einer Klasse ein Attribut `page` (die Klassen `frontpage` und `landing_page` der Demo haben eines).
2. Wählen Sie beim Bearbeiten ein **Zonenlayout**. Die Auswahl kommt aus `zone.ini [General] AllowedTypes[]`
   (`GlobalZoneLayout`, `2ZonesLayout1`, `2ZonesLayout2`, `2ZonesLayout3`, `3ZonesLayout1`, `3ZonesLayout2`,
   `CallForActionLayout`), eingeschränkt durch `AvailableForClasses[]`.
3. Fügen Sie den Zonen Blöcke hinzu. Blöcke werden in `block.ini` definiert.
4. Füllen Sie einen Block: Verwenden Sie **In Block schieben** in der Inhaltsansicht, oder lassen Sie Elemente
   automatisch aus der Quelle des Blocks kommen (ein Fetch, RSS, Schlagwörter).
5. Führen Sie die beiden Cronjob-Teile aus cron aus:

```bash
php runcronjobs.php ezflow          # ezflowupdate.php: rotates and updates blocks
php runcronjobs.php ezflow-cleanup  # ezflowcleanup.php
```

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `ezflow.ini` | `eZFlowOperations` | `UpdateOnPublish` | `enabled` | Blöcke beim Veröffentlichen von Inhalten aktualisieren |
| `ezflow.ini` | `SafetyDelay` | `DelayInSeconds` | `5` | Sicherheitsverzögerung |
| `zone.ini` | `General` | `AllowedTypes[]` | die oben aufgeführten Layouts | Zonenlayouts, die Redakteure wählen können |
| `block.ini` | pro Blocktyp | | | Blockdefinitionen |

## Was sich in den Releases von Exponential 6 geändert hat

| Release | Änderung |
|---|---|
| 6.0.0 bis 6.0.2 | Korrektur-Release für PHP 8.2; Paketanbieter und Dokumentation aus `ezflow-ezpackage` aktualisiert. |
| 6.1.0 | Modulansichten deklarieren keine Funktionen oder Klassen mehr ohne Absicherung auf oberster Ebene, sodass ein dauerhafter Worker (Velocity) die zweite Anfrage übersteht ([Einzelheiten](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md#1-persistente-php-worker-modul-views-deklarieren-auf-dateiebene-nicht-mehr-ohne-schutz)). |
| 6.1.1, 6.1.2 | `ezinfo.php`, Name der Extension und Lizenz; Templates schreiben `<br>` wie in HTML5; Texte nennen Exponential. |
| 6.1.3 | **Oracle und andere Engines**: Ein Block-Pool über seinem `Limit` wird gekürzt (das alte `DELETE ... ORDER BY ts_publication LIMIT n` wird von PostgreSQL, Oracle und den meisten SQLite-Builds abgelehnt, sodass der Pool dort nie gekürzt wurde; die ältesten Elemente werden jetzt mit dem Zeilenlimit von `eZDB::arrayQuery()` ausgewählt und per Schlüssel entfernt), und `eZPageBlock::fetch()` findet einen Seitenblock (Tabellen-Aliase ohne `AS`). |
| 6.1.4 | **YUI entfernt.** Siehe unten. |
| 6.1.5 | Befehle und Cronjob-Teile führen eine Beschreibung; die Copyright-Hinweise nennen zuerst 1998 - 2026 7x & Exponential Foundation. |

Zu 6.1.4: Der Seiteneditor, die Blockwerkzeuge, In Block schieben, der Planungsdialog und die Zeitleiste laufen auf
jQuery mit denselben Serveraufrufen, POST-Feldern, IDs und Ereignissen (`blocktools.js`, `zonetools.js`,
`scheduledialog.js`, `ezpushtoblock.js`, `ezajaxsearch.js`). Drag-and-drop verwendet Pointer-Ereignisse. Die neuen
`ezflowcalendar.js` und `ezflowwidgets.css` stellen Kalender, Reiter, Dialog, Schaltflächen und Menü bereit. Die Reiter
zum Bearbeiten von ezpage verwenden `.ezpage-tabs*`, und die Seite der Zeitleiste hat die Klasse `ezflow-skin`. Die
ungenutzte Bibliothek Prototype und die Sprite-Kopie von YUI sind entfernt.

**Hinweis zur Aktualisierung:** Wenn Sie eigene Blockwerkzeuge geschrieben haben, die die YUI-Widgets aufrufen, stellen
Sie sie auf die jQuery-Module um; die Server-API hat sich nicht geändert. Siehe
[Entfernung von YUI](../../../bc/6.0/yui-entfernung.md).

## Sprachen

Die Extension enthält Übersetzungsdateien in `translations/<locale>/translation.ts`: cat-ES, cro-HR, esl-ES, fre-FR,
ger-DE, ita-IT, jpn-JP, por-BR, swe-SE. Die deutsche Datei enthält 128 Meldungen, 16 davon noch als unfertig markiert
(zählen Sie `<message` in `translations/ger-DE/translation.ts`). Die Texte werden in Kontexten `design/admin/...`
nachgeschlagen (die Menüs des Seiteneditors). Listen Sie sie auf:

```bash
grep -o '<name>[^<]*</name>' extension/ezflow/translations/ger-DE/translation.ts | sort -u
```

Erneuern Sie nach dem Bearbeiten einer Übersetzungsdatei den kompilierten Übersetzungs-Cache mit
`./console exp:ezgeneratetranslationcache` und leeren Sie die Template- und Inhalts-Caches.
`./console exp:ezchecktranslation ger-DE` gibt Statistiken zur Datei `share/translations/ger-DE/translation.ts` des
Kernels aus, nicht zur Datei dieser Extension.

## Verwandte Seiten

- [ezwebin](ezwebin.md), [ezdemo](ezdemo.md), [ezwt](ezwt.md)
- [Velocity-Engines](../../../bc/6.0/velocity-engines.md)
- [Abstraktionen für Kommandozeile, Cronjobs und Ansichten](../../../bc/6.0/cli-cronjob-und-view-abstraktionen.md)
- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../geschichte/erweiterungen/ezflow.md) und [Versionshinweise](../../../../changelogs/extensions/ezflow.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/ezflow.md)
- Monate: [2023-12](../../../geschichte/erweiterungen/monate/2023-12.md), [2024-01](../../../geschichte/erweiterungen/monate/2024-01.md), [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md), [2026-10](../../../geschichte/erweiterungen/monate/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
