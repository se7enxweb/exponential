# ezautosave: Entwürfe, die sich selbst speichern

Diese Seite ist für Redakteure, die Arbeit verlieren, wenn sich ein Browser schließt, und für Administratoren, die das
automatische Speichern einstellen. `ezautosave` speichert den Entwurf des Objekts, das Sie bearbeiten, automatisch und
fügt der Bearbeitungsseite der Administrationsoberfläche eine eingebettete Vorschau hinzu. Es basiert auf QH Autosave.

## Was es tut

- Es speichert den Entwurf alle `Interval` Sekunden.
- Es speichert ihn, wenn Sie ein Formularfeld verlassen, nachdem Sie etwas geändert haben (`TrackUserInput`).
- Es blendet die Schaltfläche **Entwurf speichern** aus (`HideStoreDraftButton`), die nicht mehr gebraucht wird.
- Es versucht zu speichern, wenn der Redakteur die Bearbeitungsseite unerwartet verlässt (Zurück-Schaltfläche,
  Schließen des Browsers).
- Es gibt dem Bearbeitungsformular der Administrationsoberfläche eine eingebettete Vorschau des Entwurfs.

Der Redakteur muss nichts tun: Bearbeiten Sie wie gewohnt, und der Entwurf wird gespeichert.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `autosave.ini` | `AutosaveSettings` | `Interval` | `180` | Sekunden zwischen zwei automatischen Speicherungen |
| `autosave.ini` | `AutosaveSettings` | `TrackUserInput` | `enabled` | Speichern, wenn der Benutzer ein geändertes Feld verlässt; bei `disabled` speichert nur das Intervall |
| `autosave.ini` | `AutosaveSettings` | `HideStoreDraftButton` | `enabled` | Die Schaltfläche **Entwurf speichern** ausblenden |
| `autosave.ini` | `AutosaveSettings` | `HidePreviewLink` | `disabled` | Den Vorschaulink ausblenden |
| `autosave.ini` | `BrowserWorkarounds` | (Internet Explorer 11) | | Das automatische Speichern ist in IE 11 abgeschaltet, wenn das Formular ein Passwortfeld hat, weil IE 11 kein Formular an einen Iframe senden kann, während ein Passwortfeld den Fokus hat |

## Was sich in den Releases von Exponential 6 geändert hat

| Version | Datum | Änderung |
|---|---|---|
| 6.0.1 bis 6.0.4 | | Version, Lizenz (`GPL-2.0-or-later` in `composer.json`), deutsche Übersetzungen; die Beschreibung nennt Exponential. |
| 6.0.4, 6.0.5 | 30. September bis 1. Oktober 2026 | Automatisches Speichern und die Vorschau des Entwurfs laufen auf `exp::autosave` von Exponential UI, wenn `expui` aktiv ist, in der Administrationsoberfläche und in den Templates von ezwebin, mit der YUI-Version als Rückfall. Siehe unten. Benötigt `se7enxweb/expui ^1.0.0.1`. |
| 6.0.6 | 2. Oktober 2026 | Das YUI-basierte automatische Speichern und die Vorschau des Bearbeitungsformulars der Administrationsoberfläche sind entfernt. `design.ini` fügt `ezautosubmit.js` und `ezcontentpreview.js` nicht mehr jeder Admin-Seite hinzu (ohne YUI in der Administrationsoberfläche hielten sie die Skripte nach ihnen in der gepackten Datei an). Die Pfeile des Vorschaulinks werden im Stylesheet gezeichnet. |
| 6.0.7 | | Auch das YUI-basierte automatische Speichern im Frontend ist entfernt: Das Autosave-Template des Designs ezwebin behält nur seinen Code für Exponential UI (`exp::autosave`, mit `exp_config()`), und `ezautosubmit.js` ist weg. Das README dokumentiert `Exp.autosave.AutoSubmit`. |

Zu 6.0.4 und 6.0.5: Dasselbe Formular wird an dieselbe Adresse mit denselben Feldern gesendet, mit denselben Meldungen
und derselben ausgeblendeten Schaltfläche **Entwurf speichern**. Die Vorschau öffnet sich, speichert zuerst und schließt
sich auf dieselbe Weise. Der Rich-Text-Editor wird zuerst gebeten, in das Formular zu speichern, sodass ein Entwurf nicht
mehr zweimal gespeichert wird, nachdem der Editor sein Textfeld neu geschrieben hat.

## Für Entwickler

- `Exp.autosave.AutoSubmit` sendet ein Formular in einem Intervall oder bei einer Änderung an einen ezjscore-Aufruf. Es
  löst die Ereignisse `init`, `beforesave`, `success`, `error` und `abort` aus.
- Der Vorschaubereich der Administrationsoberfläche ist `Exp.autosave.Preview`.
- Die Bearbeitungs-Templates (`design/admin` und `design/ezwebin`, `content/edit/autosave.tpl`) zeigen beide im
  Einsatz.

Siehe [Entfernung von YUI](../../../bc/6.0/yui-entfernung.md).

## Verwandte Seiten

- [jQuery 4 und die Entfernung von YUI](../jquery4-und-entfernung-von-yui.md)
- [Online-Editor auf TinyMCE 8](../online-editor-tinymce8.md)
- [Chronik](../../../geschichte/erweiterungen/ezautosave.md) und [Versionshinweise](../../../../changelogs/extensions/ezautosave.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/ezautosave.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md), [2026-10](../../../geschichte/erweiterungen/monate/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
