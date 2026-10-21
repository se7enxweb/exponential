# enhancedselection2: eine Auswahl, die Bezeichner speichert

Diese Seite ist für Website-Entwickler, die ein Auswahlfeld oder eine Mehrfachauswahl brauchen, deren gespeicherte
Werte richtig bleiben, wenn sich die Optionen ändern. `enhancedselection2` ist ein Auswahl-Datentyp wie `ezselection`
des Kernels, mit einem wichtigen Unterschied: Er speichert den **Bezeichner** der gewählten Option, nicht ihre Position
(ID).

Mit `ezselection` können Änderungen an den Optionen der Klasse dazu führen, dass gespeicherte IDs auf die falsche Option
zeigen. Mit diesem Datentyp bedeutet ein gespeicherter Wert weiter dieselbe Option, solange sich die Bezeichner nicht
ändern.

## Verwenden

1. Bearbeiten Sie unter **Setup > Klassen** eine Inhaltsklasse und fügen Sie ein Attribut dieses Datentyps hinzu.
2. Geben Sie jeder Option eine lesbare Beschriftung und einen Bezeichner. Lassen Sie den Bezeichner leer, damit er so
   erzeugt wird, wie Exponential Bezeichner erzeugt.
3. Ordnen Sie die Optionen mit den Schaltflächen nach oben und nach unten; die Auswahlliste verwendet dieselbe
   Reihenfolge.

Die Informationssammlung wird unterstützt. Der Datentyp begann als Extension von SCK-CEN (Version 1.0) und wurde um
weitere Funktionen auf Klassenebene erweitert.

## Was sich in den Releases von Exponential 6 geändert hat

| Version | Datum | Änderung |
|---|---|---|
| 2.1.1, 2.1.2 | März 2024 | Paketanbieter auf se7enxweb umgestellt. |
| 2.1.3 | 27. September 2026 | `ezinfo.php` gibt Name, Version, Copyright, Lizenz und Website der Extension an. |
| 2.1.4 | 28. September 2026 | Die Auswahlwerte eines Attributs, das **noch keine ID** hat (ein neues Objekt), werden nicht nachgeschlagen, und IDs werden als Ganzzahlen verglichen, sodass **PostgreSQL** die Inhalte installiert. Jeder sichtbare Text ist ein Übersetzungstext, mit Deutsch. Die Templates übersetzen mit festen Kontexten statt mit einem Kontext in einer Variablen. |
| 2.1.5 | | Die Beschreibung nennt Exponential. |
| 2.1.6, 2.1.7 | 2. Oktober 2026 | Die Templates zum Bearbeiten und Sammeln einer **Mehrfachauswahl** lassen sich wieder parsen: Eine überzählige schließende Klammer ließ den Template-Parser die Liste der Kontrollkästchen ablehnen, sodass das Bearbeitungsformular einer Klasse, die sie verwendet, keine Felder zeigte. Befehle und Cronjob-Teile führen eine Beschreibung und verwenden die gemeinsamen Befehlshelfer. |

**Hinweis zur Aktualisierung:** Wenn eine Klasse mit einer Mehrfachauswahl vor 2.1.6 ein leeres Bearbeitungsformular
zeigte, aktualisieren Sie auf 2.1.6 oder neuer und leeren Sie die Template-Caches.

## Verwandte Seiten

- [xrowextract](xrowextract.md) exportiert und importiert diesen Datentyp (`ezenhancedselection`)
- [Abstraktionen für Kommandozeile, Cronjobs und Ansichten](../../../../eng-us/bc/6.0/cli_cronjob_view_abstractions.md)
- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../../eng-us/history/extensions/enhancedselection2.md) und [Versionshinweise](../../../../changelogs/extensions/enhancedselection2.md)
- [Änderungsverzeichnis](../../../../eng-us/history/ledger/enhancedselection2.md)
- [Verhaltensänderungen der Extensions](../../../../eng-us/bc/6.0/extensions-behaviour-changes.md)
- Monate: [2026-09](../../../../eng-us/history/extensions/months/2026-09.md), [2026-10](../../../../eng-us/history/extensions/months/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
