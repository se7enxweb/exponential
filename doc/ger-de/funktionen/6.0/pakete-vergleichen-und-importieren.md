# In ein Paket hineinsehen, es mit Ihrer Website vergleichen, einzelne Einträge importieren

Diese Seite richtet sich an Administratoren, die Inhaltspakete auf einer Website installieren, die bereits Inhalte hat.
Ein Inhaltspaket war früher eine versiegelte Kiste: installieren und hoffen. Seit dem 29. und 30.
September 2026 können Sie jede Datei darin durchsehen, sehen, wie sich jede seiner Inhaltsklassen
und jedes seiner Objekte von Ihrer Website unterscheidet, und nur die Einträge importieren, die
Sie auswählen, nach einer Bestätigung, die genau auflistet, was sich ändern wird.

Alles davon steckt im Kernel (keine Extension nötig) und nutzt die Paket-Ansichten
der Administration.

## 1. Die Dateien eines Pakets durchsehen

Öffnen Sie **Setup > Pakete** und klicken Sie auf ein Paket. `package/view/full/<name>` hat jetzt
einen Inhaltsbrowser.

- Jede Datei aus dem eigenen Verzeichnis des Pakets wird aufgelistet: `package.xml`, jeder
  Eintrag für Inhaltsklassen und -objekte, `simplefiles/`, `documents/`.
- 25 bis 1.000 Dateien oder alle pro Seite; filtern Sie nach Typ oder über eine Pfadsuche.
- Klicken Sie in einer Zeile auf **Ansehen**: Ein `.xml`-Eintrag wird formatiert angezeigt, ein Inhaltsobjekt
  zeigt seine eigenen Attribute und Übersetzungen, direkt aus der Datei gelesen, ein
  Bild wird inline angezeigt. **Herunterladen** liefert die Rohdaten (`package/viewfile`).
- Eine versteckte Datei oder ein verstecktes Verzeichnis (das eigene `.cache/` des Pakets) wird nie angezeigt.
- Ein bereits installiertes Paket bietet neben Deinstallieren **Neu installieren** an,
  das durch denselben Installationsassistenten führt. Kopfzeile und Fehlerseiten des
  Installationsassistenten verlinken in jedem Schritt zurück zum Paket und zur Paketliste.

Der Browser hält seinen Zustand in Ansichtsparametern, wie andere seitenweise blätternde Kernel-Ansichten:

```
package/view/full/slash_quotes/(offset)/4300/(file)/4342
```

Die Parameter sind `(type)`, `(search)`, `(limit)`, `(offset)` und `(file)`,
die jeweils bei ihrem Standardwert weggelassen werden. Die Suche (der einzige Freitext) wird doppelt kodiert,
weil der Pfad einmal dekodiert wird, bevor er aufgeteilt wird. Alte Links mit Query-Strings
werden einmal auf die Form mit Ansichtsparametern umgeleitet.

Die Seite ist als Administrationsseite aufgebaut: Badges in der Kopfzeile für Version,
Typ und Installationszustand; eine Definitionsliste für Zusammenfassung, Zustand, Lizenz,
Maintainer, Dokumente und Beschreibung; eine Aktionsleiste, die mit der sicheren
Hauptaktion beginnt (Installieren oder, wenn installiert, In Datei exportieren) und Deinstallieren
rechts absetzt; eine Dateiliste mit Typ-Badges und Größen; eine Seitennavigation oberhalb und
unterhalb. Die Buttons folgen jetzt ihrer eigenen Richtlinie: Installieren, Neu installieren und Deinstallieren
benötigen `package / install`, In Datei exportieren benötigt `package / export`; wenn es
nichts zu installieren gibt, sagt die Seite, warum (ein vom Einrichtungsassistenten importiertes Site-Paket
mit den Paketen, die es benötigt, ein Paket ohne Installationseinträge oder ein Benutzer
ohne `package / install`).

Sicherheitskorrekturen in dieser Gruppe: Die Beschreibung eines Pakets wird als Text ausgegeben (sie
stammt aus einer hochgeladenen `package.xml`), und eine über `package/viewfile` geöffnete Datei
wird mit einer sandboxenden Content-Security-Policy gesendet, sodass ein SVG mit Skript nicht
als angemeldeter Benutzer ausgeführt werden kann.

## 2. Ein Paket mit der Website vergleichen

Klicken Sie bei einem Paket, das Inhalte installiert, auf **Vergleichen** (vor Neu installieren), oder öffnen Sie
`package/compare/<name>` (Richtlinie `package / read`, wie `package/view`).

Der Vergleich ist rein lesend: Es wird nichts installiert oder gespeichert.

- **Inhaltsklassen** werden über ihren Bezeichner zugeordnet. Beide Seiten werden von demselben
  Code serialisiert, der eine Klasse in ein Paket schreibt, sodass Namen, Einstellungen und pro
  Attribut der Datentyp, Namen, Flags, Platzierung und Datentyp-Parameter
  gleichwertig verglichen werden.
- **Inhaltsobjekte** werden über ihre Remote-ID zugeordnet. Die aktuelle Version der Website wird
  vom eigenen Paket-Serialisierer jedes Datentyps serialisiert, danach werden die Werte
  pro Datentyp normalisiert: einfache Werte als Text, Rich Text als kanonisches XML,
  Relationen über die Remote-IDs, auf die sie zeigen, Bilder und Dateien über Name, Größe und
  Prüfsumme, alles andere über sein serialisiertes XML. Übersetzungen, die nur auf einer Seite vorhanden sind,
  und die Platzierung der Knoten werden ebenfalls verglichen. Objekte unter dem obersten Knoten eines Pakets, die
  das Paket nicht enthält, werden als „Nur auf der Site“ aufgeführt.

Jeder Eintrag erhält einen Status, der als Chip mit eigenem Symbol und eigener Beschriftung sowie einer
Farbe angezeigt wird (sodass die Farbe nie das einzige Signal ist): **Neu**, **Geändert**, **Nur auf
der Site**, **Gleich**, **Klasse fehlt**. Die Chips filtern auch. Sie können
nach Status und Klasse filtern, nach Name oder Remote-ID suchen, 25 bis 250 Zeilen pro
Seite anzeigen und nach Status, Name, Klasse oder Anzahl der Unterschiede sortieren (alles als Ansichts-
parameter wie `(sort)/<column>/(dir)/<asc|desc>`, die die Seitennavigation beibehält).

Öffnen Sie einen Eintrag, um seine Werte nebeneinander zu sehen (auf einem schmalen Bildschirm untereinander):
entfernter Text rot durchgestrichen, hinzugefügter Text grün unterstrichen, das XML von
Rich Text und serialisierte Werte griffbereit, sowie seine Platzierung. **Erneut vergleichen**
erstellt den zwischengespeicherten Vergleich neu. Der gesamte Vergleich wird einmal erstellt, in Stapeln
mit Abfragen auf jeweils eine Tabelle, und im Cache-Verzeichnis zwischengespeichert, mit einem Schlüssel aus den
Dateien des Pakets und dem Inhalts- und Klassenstand der Website; jeder Wert aus dem
Paket wird maskiert.

## 3. Ausgewählte Einträge importieren

Mit `package / install` zusätzlich zu `read` hat jede Zeile, die nicht gleich ist, einen
Button **Importieren** und ein Kontrollkästchen, neben **Ausgewählte importieren** und **Alle
Änderungen dieses Filters importieren**. Der geöffnete Eintrag hat **Diesen Eintrag importieren** und ein Häkchen pro
Wert, sodass ein Wert so bleiben kann, wie die Website ihn hat.

Jeder Import ist ein POST, der durch das Formular-Token geschützt ist, und führt zu einer Bestätigung, die
genau auflistet, was gesetzt und was beibehalten wird und warum ein Eintrag nicht importiert werden kann.
Bestätigen Sie, um zu importieren; das Ergebnis wird Eintrag für Eintrag angezeigt, mit dem nächsten Schritt, wenn ein
Filter mehr als einen Schritt umfasst (höchstens 25 Einträge pro Anfrage).

Regeln, denen der Importer folgt (`eZPackageComparisonImport`):

| Situation | Was geschieht |
|---|---|
| Neues Objekt | Wird dort angelegt, wo das Paket es platziert. |
| Geändertes Objekt | Erhält die Werte des Pakets; Werte, bei denen Sie das Häkchen entfernt haben, behalten den Wert der Website. |
| Neue Klasse | Wird angelegt. |
| Geänderte Klasse | Wird an Ort und Stelle aktualisiert (der Klassen-Installer kann eine vorhandene Klasse aktualisieren; die Antwort des Installationseintrags lautet „update existing“). |
| Orte, die die Website bereits hat | Werden nie dupliziert oder verschoben. |
| Etwas, das nur auf der Website existiert | Wird nie entfernt. |
| Übersetzungen und Einstellungen, die nur die Website hat | Bleiben erhalten. Vor dem Import werden die Sprachmasken des Objekts so repariert, dass sie jede Sprache aufführen, in der es Werte hat; danach wird eine fehlende Übersetzung zurückkopiert; die Hauptsprache und die Einstellung „immer verfügbar“ bleiben die der Website. |
| Objekt, dessen Werte zuerst den Import einer Klasse benötigen | Wird zusammen mit dieser Klasse importiert; die Bestätigung führt die Klasse zuerst auf, markiert die Werte, die sie ermöglicht, als „Kommt mit dem Klassenimport“ und folgt dem Häkchen der Klasse. Eine ausgelassene Klasse bleibt draußen, ebenso die Werte, die sie benötigen. |
| Nicht veröffentlicht, abweichende Klasse oder keine Werte | Wird vor der Bestätigung abgelehnt, mit Begründung. |
| Eine neue Version mit einem doppelten Wert oder einer verlorenen Übersetzung | Der gesamte Eintrag wird mit Begründung zurückgerollt. |

Nach einem Klassenimport wird der zwischengespeicherte Vergleich neu erstellt; nach einem Objektimport
werden nur die importierten Objekte erneut verglichen. Die Installation eines Pakets platziert jetzt auch
Objekte, deren Elternobjekt später im Paket kommt: Durchläufe werden pro Elternknoten wiederholt,
Elternknoten werden unabhängig von der Sprache über die Remote-ID gefunden, und die Knoten-Remote-ID des Pakets
bleibt am platzierten Knoten erhalten. Die neue Version eines vorhandenen Objekts aus einem Paket
enthält die Werte einer Übersetzung nicht mehr doppelt.

## Paketassistenten: Zurück-Button und Reihenfolge

Jeder Schritt der Assistenten zum Erstellen von Paketen hat neben **Weiter** einen Button **Zurück**.
Was Sie eingegeben haben, bleibt erhalten, sofern es gültig ist, und ein Schritt wird nur bei seinem ersten
Besuch initialisiert. Zurück im ersten Schritt führt zur Auswahl des Assistenten, und Zurück dort
zur Paketliste. Die Klassen- und Extension-Schritte zeigen die dort getroffene Auswahl,
wenn Sie zurückkehren. Der Assistent für den Export von Inhaltsobjekten lässt Templates, die zu
den Objekten gehören, weg, sofern Sie sie nicht auswählen. Die Seite Paket erstellen listet die
Assistenten in der Reihenfolge von `[CreationSettings] HandlerList[]` auf (Inhalte, Klassen,
Extensions, Site-Stile).

## Alte Pakete funktionieren wieder

- Ein Archiv im alten V7-Tar-Format (ohne `ustar`-Magic; die Klassenpakete
  der 3.x-Releases) ließ den Import endlos hängen. Es wird jetzt mit dem V7-Leser
  geöffnet; jedes andere Archiv behält den GNU-Leser, damit lange Dateinamen funktionieren.
- Klassendefinitionen von vor 3.9, die nur `<name>` enthalten, lassen sich wieder installieren.
- Ein für PHP 4 und 5 geschriebener Datentyp mit einem Konstruktor, der nach seiner Klasse benannt ist
  (zum Beispiel die Typen für Newsletter-Liste und -Ausgabe), wird ohne
  `eZDataType::__construct()` erzeugt, und sein alter Konstruktor wird aufgerufen, sodass Klassen
  solcher Pakete wieder installiert werden können.
- Der Import des ersten Pakets in eine Installation, deren Paket-Repository
  noch nicht existiert, löst keine Warnung aus; ein Benutzer ohne Paket-Richtlinie erhält ein schlichtes
  false.

## Verwandte Seiten

- [ezpm: der Paketmanager auf der Kommandozeile](ezpm-paketmanager-cli.md), [Paketinstallation in Teilschritten](paketinstallation-in-teilschritten.md), [Paketlizenzen und -versionen](paketlizenzen-und-versionen.md)
- [Kickstarter: eine ganze Website aus einer Datei installieren](kickstarter-cli.md), [Installer-Logs und Startdaten](../../spezifikationen/6.0/installer-protokolle-und-startdaten.md)
- [Info- und Urheberrechtsseiten](info-und-paketseiten.md), [Übersetzungen und Sprachen](uebersetzungen-und-sprachen.md)
- [Verhaltensänderungen, 16. bis 30. September 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Geschichte: [16. bis 30. September 2026](../../../eng-us/history/2026/2026-09b.md), [Juni 2026, zweite Hälfte](../../../eng-us/history/2026/2026-06b.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
