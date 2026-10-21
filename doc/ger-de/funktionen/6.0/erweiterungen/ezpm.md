# ezpm: private Nachrichten

Diese Seite ist für Website-Entwickler, die Benutzer der Website einander Nachrichten schicken lassen wollen. `ezpm`
(„eZ PM“) fügt **private Nachrichten** hinzu: einen Posteingang, gesendete Nachrichten und Entwürfe sowie das
Schreiben, Beantworten und Bearbeiten von Nachrichten.

| Ansicht | Zweck |
|---|---|
| `pm/list_inbox`, `pm/list_sent`, `pm/list_drafts` | Posteingang, gesendete Nachrichten, Entwürfe |
| `pm/create`, `pm/reply`, `pm/edit`, `pm/add`, `pm/message` | schreiben, beantworten, bearbeiten und lesen |

Version 0.9 war die Version für die Kernel-Linie Exponential 4.x, mit vielen Korrekturen an Design und Sicherheit, und
arbeitet am besten mit ezwebin und ezflow zusammen. Version 0.9.1 fügte eine **E-Mail-Benachrichtigung** hinzu: Ihr
Text ist das Template `design/standard/templates/pm/notification_email.tpl`, eingeschaltet wird sie in
`ezpm.ini.php`.

## Einrichten

1. Aktivieren Sie die Extension und legen Sie ihre Tabellen an (`sql/update.sql` oder `sql/<engine>/`).
2. Geben Sie Benutzern die Richtlinie für das Modul `pm` (voller Zugriff).
3. Öffnen Sie `/pm/list_inbox`. Der Posteingang erscheint.
4. Optional, für die Einbindung in die Foren von ezwebin oder ezflow: Kopieren Sie
   `design/standard/override/templates/full/forum_topic.tpl` in den Überschreibungsordner von ezwebin und fügen Sie
   in Ihrem Seitenlayout einen Link auf das Modul für private Nachrichten ein (siehe Zeile 156 von
   `pm_pagelayout.tpl`).

## Was sich geändert hat

| Version | Datum | Änderung |
|---|---|---|
| 0.9.1 | 28. Januar 2024 | `composer.json`. |
| 0.10.0 | 22. September 2026 | Modulansichten deklarieren keine Funktionen oder Klassen mehr ohne Absicherung auf oberster Ebene, sodass ein dauerhafter Worker (Velocity) die zweite Anfrage übersteht ([Einzelheiten](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md#1-persistente-php-worker-modul-views-deklarieren-auf-dateiebene-nicht-mehr-ohne-schutz)). |

## Verwandte Seiten

- [Velocity-Engines](../../../bc/6.0/velocity-engines.md)
- [Chronik](../../../geschichte/erweiterungen/ezpm.md) und [Versionshinweise](../../../../changelogs/extensions/ezpm.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/ezpm.md)
- [Monat: 2026-09 (alle Extensions)](../../../geschichte/erweiterungen/monate/2026-09.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
