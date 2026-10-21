# Template-Editor: Überschreibungen anlegen, ordnen und bearbeiten, ohne INI-Dateien anzufassen

Diese Seite richtet sich an Designer und Site-Builder, die das Aussehen einer Klasse, eines Knotens oder einer Sektion
ändern. Eine Template-Überschreibung besagt: "Für diese Klasse, diesen Knoten oder diese Sektion verwende dieses
Template statt des Standard-Templates." Exponential hält die Regeln in `override.ini`.
Der visuelle Template-Editor im Admin unter **Design > Template-Editor**
(`/visual/templatelist`) lässt einen Designer diese Regeln anlegen, umsortieren, ändern und entfernen
und die Template-Dateien bearbeiten. Zwischen dem 12. und dem 20. Juli 2026
(Commit `12a2577f00` und Folgeänderungen) wurde er sicher benutzbar gemacht: Eine Regel kann nicht
mehr verloren gehen oder stillschweigend ignoriert werden.

## Öffnen

- Admin-Menü: Reiter **Design**, Eintrag **Template-Editor** im linken Menü (Einstellung
  `settings/menu.ini [Leftmenu_design]` `Links[template_editor]=visual/templatelist`,
  mit dem passenden `PolicyList_template_editor[]`).
- In `menu.ini` sind die älteren Zeilen `Links[templates]=visual/templatelist` des Setup-Menüs auskommentiert ("Removed from 4.3"), sodass nur der Eintrag unter Design existiert, sofern Sie sie nicht aktivieren. Prüfen Sie das mit `grep -n templatelist settings/menu.ini`.
- Ein Benutzer braucht die Richtlinie `visual/templatelist` (das Modul *Design*).

## Was sich für Sie geändert hat

### Neue Überschreibungen wirken sofort

Eine neue Überschreibung wird mit `Priority=0` geschrieben, der höchsten Priorität. Die Template-Engine
wertet Überschreibungen vom niedrigsten `Priority`-Wert an aufwärts aus, und eine Regel ohne
`Priority` wird zuletzt einsortiert. Vor Juli war die auf dem Bildschirm angezeigte Reihenfolge nicht die, die
die Engine verwendete; jetzt ist sie es (`kernel/common/eztemplatedesignresource.php`,
`overrideArray()`).

### Mit der Prioritätenliste umsortieren

Ändern Sie in der Liste der Überschreibungen die Zahlen und klicken Sie auf **Overrides aktualisieren**. Die
Zahlen werden als `Priority` in `override.ini` gespeichert, und die veralteten Caches für Überschreibungen und
INI werden vorher und nachher geleert, sodass die neue Reihenfolge sofort gilt.

### Per Drag-and-drop umsortieren (September 2026)

**Design > Templates** (`/visual/templateview/...`) zeigt die Überschreibungen eines Templates als
Karten in genau der Reihenfolge, in der sie ausprobiert werden, und lässt Sie sie per Drag-and-drop
umsortieren. Jede Verschiebung wird sofort gespeichert.

1. Öffnen Sie **Design > Templates** und wählen Sie ein Template wie `node/view/full.tpl`.
2. Die Überschreibungen erscheinen als Karten in der Reihenfolge, in der sie ausprobiert werden. Jede Karte zeigt ihre
   Position, ob die Siteaccess-Einstellungen oder eine Extension sie definieren, ihre Datei
   und ihre Bedingungen.
3. Ziehen Sie eine Karte an ihrem Griff an eine neue Stelle oder verschieben Sie sie mit ihren Pfeilen
   (Tastatur und Touch funktionieren). Die neue Reihenfolge wird sofort als
   `Priority` (10, 20, 30 ...) der Überschreibungen dieses Templates gespeichert.
4. Verwenden Sie das Filterfeld, um die Karten auf der Seite einzugrenzen ("filtert diese Seite").
5. **Ausgewählte entfernen**, **Neues Überschreib-Template** und **Bedingungen speichern** stehen sowohl über
   als auch unter der Liste, sodass eine lange Liste kein Scrollen erfordert.

Seitenaufteilung: Die Überschreibungen werden zu 20 pro Seite angezeigt, mit der Seitennavigation oben und unten, und
die Positionen werden über die gesamte Liste gezählt. Ein Ziehen oder ein Pfeil innerhalb einer Seite sendet die neue
Reihenfolge dieser Seite, und der Server setzt sie an die Stelle der Seite in der Gesamtreihenfolge. Der
Aufwärtspfeil der ersten Überschreibung einer Seite und der Abwärtspfeil ihrer letzten verschieben diese
auf die vorherige bzw. nächste Seite, tauschen sie dort mit der jeweiligen Überschreibung, und die Seite wird
neu geladen.

| Datei | Block | Schlüssel | Standard |
|---|---|---|---|
| `settings/admininterface.ini` (setzen Sie ihn in `settings/override/admininterface.ini.append.php`) | `[PaginationSettings]` | `ItemsPerPage[visual/templateview]` | 20 (eingebaut; der Schlüssel steht nicht in der ausgelieferten Datei) |

Siehe [Wo die Seitengrößen liegen](../../../eng-us/bc/6.0/pagination-settings.md).

Das Speichern übernimmt `ezpTemplateOverrides`, das nur
`settings/siteaccess/<siteaccess>/override.ini.append.php` ändert. Die Datei wird von der Festplatte gelesen,
zuerst wird eine Kopie angelegt und danach wird die Datei geprüft. Die alte Seite war
gefährlich:

| Vorher | Jetzt |
|---|---|
| Aktualisieren schrieb eine `Priority` in jede Überschreibung der Datei, `0` für alle, die die Seite nicht anzeigte. | Schreibt nur die geänderten Bedingungen der Überschreibungen, die die Seite angezeigt hat. |
| Aktualisieren speicherte die aus allen Quellen zusammengeführte `override.ini` in die Siteaccess-Datei und kopierte so die Überschreibungen von Extensions hinein. | Die Datei erhält nur `Priority`-Zeilen (und die eine Bedingung, die Sie bearbeitet haben). |
| Entfernen nahm jede beliebige Überschreibung und löschte ihre Datei, wo immer sie lag. | Entfernt nur Überschreibungen, die die Siteaccess-Datei definiert, und deren Dateien nur innerhalb von `design/`. Die Überschreibung einer Extension bleibt der Extension überlassen, mit einem Hinweis. |
| Vor die Bedingungen wurden Reset-Markierungen geschrieben, wodurch Bedingungen verworfen wurden, die eine Extension derselben Überschreibung gibt. | Die Datei wird ohne Reset-Markierungen geschrieben. |
| Eine Umsortierung mit beliebigem Inhalt wurde akzeptiert. | Eine Umsortierung, die etwas anderes enthält als genau die angezeigten Überschreibungen, wird abgelehnt, und nach dem Speichern wird die wirksame Reihenfolge zurückgelesen und verglichen. |
| Jeder beliebige Siteaccess konnte angegeben werden. | Der zu ändernde Siteaccess muss einer aus `RelatedSiteAccessList` sein. |

Überprüft an einer frischen Installation mit 56 Überschreibungen von `node/view/full.tpl`:
Verschiebungen innerhalb von Seite 1, eine Verschiebung von Seite 2 auf Seite 1 und zurück sowie das Speichern einer
Bedingung auf einer späteren Seite hinterließen in der Einstellungsdatei jeweils nur `Priority`-Zeilen oder die eine
geänderte Bedingung; das Ändern einer Bedingung und das Zurückändern stellten die Datei Byte für Byte wieder her.
Siehe auch [Ladereihenfolge der Extensions](ladereihenfolge-der-extensions.md), die
entscheidet, wessen Überschreibungen zuerst gefunden werden, und
[Seitenaufteilung von Admin-Listen](seitenweise-listen-im-admin.md).

### Wählen, wovon die Kopie ausgeht

Wenn Sie eine Überschreibung mit *Default copy* anlegen, braucht der Editor ein Template zum
Kopieren. Er wählt in dieser Reihenfolge:

1. Das Template, das Sie in der Auswahlliste **TemplateSource** wählen.
2. Die klassenspezifische Überschreibung, die für die Klasse, auf die Sie abgleichen, bereits existiert
   (zum Beispiel `full/frontpage.tpl` für die Klasse `frontpage`).
3. Das tatsächliche Standard-Template.

Das ersetzt das alte Verhalten, das das falsche Template kopieren konnte.

### Auf ein einzelnes Objekt abgleichen

Das Formular für die Knotenansicht hat ein neues Feld **Objekt ID** vor **Knoten ID**. Der Abgleich auf
`object` ist die präziseste Überschreibung, die es gibt, und sie wird vor der
Knotenbedingung ausgewertet. Verwenden Sie sie, um einen Artikel umzugestalten, ohne sonst etwas zu ändern.

### Abgleichsbedingungen bearbeiten und entfernen

Jede Überschreibung in der Liste zeigt ihre Bedingungen als Textfelder (`class_identifier`,
`node`, `object`, `section`, ...). Sie können:

- einen Wert ändern und auf **Overrides aktualisieren** klicken;
- das Kästchen neben einer Bedingung ankreuzen, um sie zu entfernen (`RemoveMatchArray`);
- mit der Auswahlliste **Bedingung hinzufügen** eine Bedingung ergänzen. Angeboten werden die Schlüssel
  `class_identifier`, `class`, `node`, `object`, `section`, `section_identifier`,
  `remote_id`, `node_remote_id`, `parent_node`, `class_group`, `depth`,
  `url_alias`, `viewmode`, `navigation_part_identifier`, `persistent_variable`,
  `state`, `state_identifier`.

Ein leerer Wert oder `-1` entfernt die Bedingung. Die Eingabetaste in einem Feld löst jetzt
**Overrides aktualisieren** statt **Ausgewählte entfernen** aus, sodass ein versehentliches Enter
keine Regel löschen kann (`64736998cd`).

### Entfernen räumt auf

**Entfernen** löscht den INI-Block ebenso wie die `.tpl`-Datei, selbst wenn die Datei
bereits fehlt.

### Ein besseres Bearbeitungsfeld

Das Textfeld für das Template ist volle Breite, in Festbreitenschrift, mindestens 70 % der Fensterhöhe hoch
und behält seine Zeilenumbrüche bei (`80d3fef827`).

### Die Schaltfläche für eine neue Überschreibung erscheint nur, wenn sie funktionieren kann

Gibt es kein Quell-Template für die Ressource, sagt der Editor das, statt eine Schaltfläche anzuzeigen, die
fehlschlägt.

## Beispiel: einen Artikel umgestalten

1. **Design > Template-Editor**, wählen Sie das Design des Siteaccess und öffnen Sie `node/view/full.tpl`.
2. Klicken Sie auf **Neues Überschreib-Template**, wählen Sie *Default copy* und geben Sie die **Objekt ID** des Artikels ein.
3. Klicken Sie auf **Erstellen**. Die neue Regel hat `Priority=0` und hat Vorrang vor der Klassenregel.
4. Bearbeiten Sie das neue Template und speichern Sie es.
5. Prüfen Sie im Seitenquelltext mit [Template-Pfadkommentaren](template-pfad-kommentare.md), dass Ihre Datei antwortet.

Der Editor speichert den Block in `settings/siteaccess/<siteaccess>/override.ini.append.php` und lässt den Cache der Inhaltsansichten verfallen. Das sieht so aus:

```ini
[full_object_1234]
Source=node/view/full.tpl
MatchFile=full/object_1234.tpl
Subdir=templates
Priority=0
Match[object]=1234
```

(Block- und Dateinamen werden aus Ihren Eingaben gewählt; die Schlüssel `Source`, `MatchFile`,
`Subdir`, `Match` und `Priority` sind die, die der Editor schreibt.)

## Kleinere verwandte Änderung

`fetch( 'content', 'class_list', hash( ... ) )` liefert die Inhaltsklassen
(optional nach Gruppe; jeder Wert für `sorts` sortiert sie aufsteigend nach Namen; `limit` nimmt `offset` und
`length` an), für visuelle Module, die Klassen durchsuchen (`a219ec4839`):

Parameter der Funktion (`kernel/content/function_definition.php`, alle optional):

| Parameter | Standard | Bedeutung |
|---|---|---|
| `as_object` | `true` | Liefert Klassenobjekte (`false` liefert Zeilen). |
| `group_list` | `false` | Array von Klassengruppen-IDs, die das Ergebnis einschränken. |
| `sorts` | `null` | Sortier-Array, zum Beispiel `array( 'name' => 'asc' )`. |
| `limit` | `null` | `hash( 'offset', 0, 'length', 20 )`. |

```
{def $classes = fetch( 'content', 'class_list', hash( 'sorts', array( 'name' => 'asc' ), 'limit', hash( 'offset', 0, 'length', 20 ) ) )}
```

## Grenzen

- Der Editor schreibt die Überschreibungsdatei des Siteaccess (`settings/siteaccess/<siteaccess>/override.ini.append.php`); führen Sie `php bin/php/ezcache.php --clear-tag=template --allow-root-user`
  aus, wenn eine Änderung nicht erscheint (die globalen Caches für INI und Überschreibungen leert der Editor selbst).
- Legen Sie vor Massenänderungen eine Sicherung an: Der Editor bearbeitet Dateien auf der Festplatte.

## Verwandte Seiten

- [Template-Pfadkommentare](template-pfad-kommentare.md) (sehen, welche Überschreibung geantwortet hat), [Ladereihenfolge der Extensions](ladereihenfolge-der-extensions.md) (wessen Überschreibung gewinnt)
- [Seitenaufteilung, Sortierung und Seitengrößen](seitenweise-listen-im-admin.md)
- [Verhaltensänderungen im Juli und August 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-07-08.md), [Verhaltensänderungen, 16. bis 30. September 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- Historie: [Juli 2026](../../../eng-us/history/2026/2026-07.md), [16. bis 30. September 2026](../../../eng-us/history/2026/2026-09b.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
