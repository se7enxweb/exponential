# Der Paketassistent

Diese Seite ist für Administratoren, die in der Administrationsoberfläche Pakete erstellen, und für alle, die die
Templates des Assistenten ändern. Seit Oktober 2026 ist der Assistent zum Erstellen von Paketen (**Setup > Pakete >
Neues Paket erstellen**, `/package/create`) ein Schrittassistent, gezeichnet mit den gemeinsamen Komponenten der
Administrationsoberfläche: Er zeigt, wo Sie sind, fragt jeden Teil in einer Karte mit Beschriftungen und Hilfe ab, zeigt
einen Fehler an dem Feld, zu dem er gehört, und lässt Sie das ganze Paket prüfen, bevor es geschrieben wird. Was der
Assistent sendet, was die Ersteller lesen und das Paket, das er schreibt, sind unverändert.

## Ein Paket erstellen

1. **Setup > Pakete > Neues Paket erstellen** führt die Assistenten auf, die Ihre Rolle verwenden darf, jeden mit dem,
   was er exportiert:

   | Assistent | Exportiert | Schritte |
   |---|---|---|
   | Export von Inhaltsklassen (`ezcontentclass`) | die Definitionen von Inhaltsklassen, keine Inhalte | Aufzunehmende Inhaltsklassen, Paket Informationen, Paket Betreuer, Paket-Changelog |
   | Export von Inhaltsobjekten (`ezcontentobject`) | Knoten oder Teilbäume mit ihren Inhalten, optional ihre Klassen und Templates | Objekte, die inkludiert werden sollen, Limits von Objekten, Paket Informationen, Paket Betreuer, Paket-Changelog |
   | Extension-Export (`ezextension`) | die Dateien von Extensions | Extensions, die inkludiert werden sollen, Paket Informationen, Paket Betreuer, Paket-Changelog |
   | Seitenstil (`ezstyle`) | zwei Stylesheets, ihre Bilder und ein Vorschaubild | Paket Miniaturbild, CSS-Dateien, Bilddateien, Paket Informationen, Paket Betreuer, Paket-Changelog |

   Wählen Sie einen und drücken Sie **Assistent starten**. Der Schritt Betreuer wird ausgelassen, wenn Ihre Rolle keine
   Betreuerrolle vergeben darf. Der Extension-Export braucht zuerst eingeschalteten Export
   (`package.ini [ExtensionExportSettings]`, siehe [Extension-Liste und Downloads](extension-liste-und-downloads.md)); bis
   dahin wird jede Extension aufgeführt, lässt sich aber nicht wählen, mit dem Grund.
2. Jeder Schritt zeigt oben die Liste der Schritte (der aktuelle markiert, die hinter Ihnen abgehakt), „Schritt n von m“,
   den Namen des Schritts und was er abfragt. Unten: **Zurück**, die Hauptaktion und **Abbrechen**.
   - **Weiter** prüft den Schritt und geht weiter. Ist etwas falsch, bleibt der Schritt, eine Zusammenfassung oben führt
     die Fehler auf (jeder ein Link zu seinem Feld), und das Feld zeigt seinen Fehler darunter.
   - **Zurück** behält, was Sie eingegeben haben (wenn es gültig ist), und kehrt einen Schritt zurück; vom ersten Schritt
     kehrt es zur Wahl des Assistenten zurück.
   - **Abbrechen** kehrt zur Paketliste zurück; nichts wird geschrieben.
   - Die Eingabetaste in einem Feld ist Weiter, nie Zurück oder eine eigene Schaltfläche eines Schritts.
3. Der letzte Schritt (Changelog) zeigt die **Überprüfung**: Name, Zusammenfassung, Version, Format, Lizenz, Host,
   Packager und Betreuer des Pakets und was es trägt (die Klassen, die Knoten mit den Optionen, die Extensions oder die
   Dateien). **Paket erstellen** schreibt es in das Repository `local` und öffnet seine Seite, die **Herunterladen**,
   **Installieren** und die übrigen Aktionen hat.

Die Auswahlelemente:

- **Inhaltsklassen**: eine Tabelle aller Klassen mit einem Kontrollkästchen, ihrem Bezeichner und ihren Gruppen sowie „n
  von m ausgewählt“. Ein Filter darüber grenzt die Liste nach Name oder Bezeichner ein (ein Skript; ohne es wird die ganze
  Liste gezeigt). Eine vom Filter verborgene Klasse behält ihren Haken.
- **Inhaltsobjekte**: die gewählten Knoten und Teilbäume in einer Tabelle, jeweils mit Klasse und URL. **Knoten
  hinzufügen** und **Teilbaum hinzufügen** öffnen den Inhaltsbrowser und kehren mit der Wahl zurück; **Ausgewählte
  entfernen** nimmt die angehakten Zeilen heraus (ohne Haken entfernt es nichts).
- **Optionen** (Export von Inhaltsobjekten): Klassendefinitionen und Templates (mit den Siteaccesses, deren Templates
  mitgehen, als Chips), die Versionen, die Sprachen (Chips) und wie Knotenzuweisungen und verwandte Objekte behalten
  werden.
- **Extensions**: eine Tabelle mit Version, letzter Änderung und Details (eine aufklappbare Angabe pro Extension); eine,
  die nicht exportiert werden darf, wird deaktiviert mit ihrem Grund gezeigt.
- **Bilddateien** (Seitenstil): **Bild hinzufügen** fügt die gewählte Datei hinzu und bleibt im Schritt; **Weiter** ohne
  Datei geht weiter.

## Die Templates

Jeder Schritt ist ein Template von `design/<design>/templates/package/`, dieselbe Datei in `design/admin4` (admin4 und
admin4l) und `design/admin` (die klassische Administrationsoberfläche):

| Template | Enthält |
|---|---|
| `create.tpl` | die Wahl des Assistenten |
| `create/wizard_head.tpl` | die verborgenen Felder, die jeder Schritt sendet, und den Seitentitel |
| `create/wizard_progress.tpl` | die Schritte (`ol.a4-steps`, `aria-current="step"`), „Schritt n von m“, Überschrift und Einleitung des Schritts, die Fehlerzusammenfassung; Parameter `intro`, `field_ids`, `field_labels` |
| `create/wizard_actions.tpl` | die Überprüfung im letzten Schritt, Zurück, Weiter oder Paket erstellen, Abbrechen |
| `create/wizard_review.tpl` | die Überprüfung (liest `$persistent_data`) |
| `create/wizard_pick_script.tpl` | Zähler und Filter der Auswahlelemente |
| `create/info.tpl`, `maintainer.tpl`, `changelog.tpl`, `thumbnail.tpl` | die Schritte, die alle Ersteller teilen |
| `creators/<creator>/*.tpl` | die eigenen Schritte der Ersteller und der Kopf des Inhaltsbrowsers (`browse_node.tpl`, `browse_subtree.tpl`) |

Ein Schritt-Template öffnet den Rahmen (`form.a4-page.a4-package-wizard` > `.context-block`), bindet `wizard_head.tpl`
ein, öffnet `box-bc`, bindet `wizard_progress.tpl` ein, zeichnet seine Karten und bindet `wizard_actions.tpl` ein; jedes
Template schließt die Rahmen, die es öffnet. Ein Fehler, den ein Ersteller meldet, nennt sein Feld
(`'field' => ezpI18n::tr( 'kernel/package', 'Package name' )`); der Schritt bildet diesen Namen über `field_ids` auf die
ID seines Steuerelements ab, sodass die Zusammenfassung auf das Steuerelement verlinkt und das Steuerelement
`aria-invalid="true"` und `aria-describedby` mit der ID des Fehlers erhält.

Die Installer verwenden weiterhin `package/install_header.tpl` und `package/navigator.tpl`; `package/header.tpl` und
`create/error.tpl` bleiben für Überschreibungen, die sie einbinden.

### Was gesendet wird

Nichts hat sich geändert. Die Namen, wie die Ersteller sie lesen:

| Schritt | Namen |
|---|---|
| jeder Schritt | `CreatorItemID`, `CreatorStepID`, `PackageStep`, `NextStepButton`, `PreviousStepButton` |
| Wahl des Assistenten | `CreatorItemID`, `CreatePackageButton` |
| Paket Informationen | `PackageName`, `PackageSummary`, `PackageDescription`, `PackageVersion`, `PackageFormat`, `PackageLicence`, `PackageHost`, `PackagePackager` |
| Betreuer | `PackageMaintainerPerson`, `PackageMaintainerEmail`, `PackageMaintainerRole` |
| Changelog | `PackageChangelogPerson`, `PackageChangelogEmail`, `PackageChangelogText` |
| Miniaturbild | `PackageThumbnail` (Datei) |
| Inhaltsklassen | `ClassList[]` (jetzt Kontrollkästchen; vorher eine Mehrfachauswahl, dieselbe Liste) |
| Inhaltsobjekte | `AddNode`, `AddSubtree`, `RemoveSelected`, `DeleteIDArray[]` |
| Optionen | `IncludeClasses`, `IncludeTemplates`, `SiteAccesses[]`, `VersionExport`, `Languages[]`, `NodeAssignment`, `RelatedObjects` (Siteaccesses und Sprachen sind jetzt Kontrollkästchen; dieselben Listen) |
| Extensions | `PackageExtensionNames[]` |
| CSS-Dateien | `PackageSiteCSSFile`, `PackageClassesCSSFile` (Dateien) |
| Bilddateien | `PackageImageFile` (Datei); **Bild hinzufügen** sendet `NextStepButton` wie Weiter |

Die Pflichtfelder tragen `aria-required`, nicht `required`: Der Server prüft sie, seine Meldungen sind die gezeigten, in
der Sprache der Oberfläche, und Zurück wird nie vom Browser aufgehalten.

### Stile

Die gemeinsamen Komponenten (`.a4-card`, `.a4-steps`, `.a4-field`, `.a4-table`, `.a4-filterbar`, `.a4-chips`,
`.a4-radios`, `.a4-toolbar`, `.a4-facts`, `.a4-notice`) und, für das, was sie nicht können, die Regeln `.a4-package-*`
aus `stylesheets/modules/structure.css` (der Abschnitt Pakete; in beiden Designs identisch): Die Schrittliste scrollt
unter 600px in einer Zeile und zeigt nur den Namen des aktuellen Schritts, Felder stehen ab 640px zu zweit, Abbrechen
sitzt am Ende der Aktionsleiste, die zwei Gruppen der Überprüfung stehen nebeneinander. Nur Tokens, kein Inline-CSS
([Die Administrationsoberfläche gestalten](../../bc/6.0/admin4-gestaltung.md)).

`tests/tests/design/AdminPackageWizardTest.php` prüft, dass die beiden Designs dieselben Dateien tragen, dass jeder
Schritt Kopf, Fortschritt und Aktionen verwendet und dass jeder Name der Tabelle oben weiterhin gesendet wird.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
