# Die Inhaltsprüfungen: nicht verknüpfte Objekte, ungültige Relationen und fehlende Dateien

Lesen Sie diese Seite, wenn Sie einen Inhaltsbaum aufräumen (Bilder und Dateien, die niemand mehr verwendet) oder nach
veröffentlichten Seiten suchen, die auf etwas verlinken oder etwas einbetten, das ein Besucher nicht erreichen kann,
und wenn Sie entscheiden, wer das darf.

## Kurz gefasst

- **Nicht verknüpfte Objekte** (`/contentcheck/unreferenced/<node id>`): die veröffentlichten **Assets** (Bilder,
  Dateien, Medien) unterhalb eines Knotens, die **nichts verwendet**: kein anderes Objekt, kein Layout, keine
  Einstellung, kein Template, keine Rolle, kein Shop- oder Newsletter-Eintrag, keine dynamische Sammlung und kein
  übergeordnetes Element, das sie anzeigt. Filtern Sie nach Klasse, sortieren und blättern Sie, und verschieben Sie die
  angekreuzten nach einer Bestätigung in den Papierkorb, und zwar nur die, die Sie entfernen dürfen. Seiten, Ordner,
  Benutzer und andere Inhalte werden nie aufgeführt: Sie werden über den Baum erreicht.
- **Ungültige Relationen** (`/contentcheck/brokenrelations/<node id>`): sichtbare, veröffentlichte Inhalte, die auf
  ein Objekt oder einen Knoten verweisen, der gelöscht wurde, im Papierkorb liegt, versteckt ist oder nie
  veröffentlicht wurde, mit der Quelle, dem Feld, dem Ziel, seinem Zustand und dem, was dagegen zu tun ist, sowie
  einem Link zum Bearbeiten der Quelle; und auf der ersten Seite die Layouts, deren Blöcke, Sammlungen oder Regeln
  solche Inhalte nennen. Nur lesend.
- **Speicherdateien** (`/contentcheck/storage/<node id>`): die Dateien in `var/<site>/storage`, die kein Inhalt
  verwendet (verwaiste Dateien), mit ihrer Größe, ihrem Alter und den Summen, sowie die Dateisystemprobleme der
  Dateien, die Inhalte verwenden (falscher Eigentümer oder falsche Rechte, leer, ein nicht passender Name oder Pfad).
  Verwaiste Dateien lassen sich in eine Quarantäne verschieben, von dort wiederherstellen und dort löschen; Eigentümer
  und Rechte lassen sich korrigieren. Aus dem Speicher selbst wird nie etwas gelöscht.
- Jede Prüfung beginnt dort, wo Sie es wählen: im ganzen Baum, bei den Websites, bei Medien, in einer Website oder
  einem Bereich unter der Inhaltswurzel oder bei einem beliebigen Knoten über die Knotenauswahl; Ihre Wahl wird
  gespeichert (siehe [Wo eine Prüfung beginnt](#wo-eine-prüfung-beginnt)).
- **Fehlende Dateien**, ein Ziel der ungültigen Relationen (**Fehlende Dateien in var/<site>/storage**): Bilder,
  Dateien und Medien, deren Datei im Speicher (oder in dem des Clusters) fehlt, in der veröffentlichten Version und
  in Entwürfen, sowie Bilder, deren Bildvarianten fehlen oder veraltet sind. Siehe
  [Fehlende Dateien](#fehlende-dateien).
- Beide stehen im Setup-Menü und auf der Setup-Übersicht (Gruppe Inhaltsmodell) sowie im Kontextmenü des
  Inhaltsbaums und der Liste der Unterelemente (**Fortgeschritten > Nicht verknüpfte Objekte im Teilbaum**,
  **Ungültige Relationen im Teilbaum**), die sie für diesen Knoten öffnen.
- Drei Richtlinien des neuen Moduls `contentcheck`: `unreferenced`, `trash` und `brokenrelations`. Jede Liste zeigt
  nur, was `content/read` des Benutzers erlaubt. In einer neuen Installation hat sie nur Administrator.
- Nur auf Administrations-Siteaccesses. Das Verschieben in den Papierkorb ist die eigene Entfernung des Kernels und
  wird im Audit wie jedes Verschieben in den Papierkorb festgehalten (`content.node.remove.trash`).
- Auf der Kommandozeile nur als Bericht: `./console exp:content-check` (`bin/php/contentcheck.php`), siehe
  [Auf der Kommandozeile](#auf-der-kommandozeile).

## Was „nicht verknüpft“ bedeutet

Eine veröffentlichte Seite wird über Navigation, Menüs, Suche und ihre Adresse erreicht, ob nun etwas auf sie verlinkt
oder nicht; „nichts verlinkt darauf“ sagt also nichts darüber aus, ob sie gebraucht wird. Die Liste ist deshalb auf
**Assets** beschränkt: Objekte der Klassen in `contentcheck.ini [UnreferencedSettings] AssetClassList[]`
(standardmäßig `image`, `file`, `video`, `audio`, `flash`, `quicktime`, `windows_media`, `real_video`), die nur
zählen, wenn etwas sie verwendet.

Ein Asset ist **nicht verknüpft**, wenn alle diese Bedingungen gelten:

1. es ist veröffentlicht, sein Hauptort liegt unterhalb des Knotens, und Sie dürfen es lesen;
2. **das übergeordnete Element jedes seiner Orte bewahrt es nur auf**: Die Klasse des übergeordneten Elements steht
   in `StorageParentClassList[]` (standardmäßig `folder`). Ein Bild unter einer Galerie, einem Artikel, einer
   Veranstaltung oder einer Landingpage wird vom Template dieses übergeordneten Elements angezeigt und zählt daher als
   verwendet („von seinem übergeordneten Element angezeigt“);
3. keine **Relationszeile** verweist darauf (siehe unten), und
4. nichts im **Verweisindex** nennt es: über die Objekt-ID, die ID eines seiner Orte, seine Remote-ID oder die seiner
   Orte, seinen Pfad (URL-Alias) oder den Pfad einer seiner Dateien; und keine **dynamische Sammlung** listet es auf.

Objekte mit Unterelementen werden weggelassen, sofern nicht danach gefragt wird. Veröffentlichte Objekte ganz ohne Ort
werden (für einen Benutzer, der alles lesen darf) gezählt, aber nicht aufgeführt: Nichts kann sie erreichen, und der
Papierkorb braucht einen Ort.

## Was als Verweis zählt

### Relationszeilen

Die Relationen, die der Kernel in `ezcontentobject_link` führt, jeden Typs: Objektbeziehungen (allgemein),
Beziehungsfelder (Attribut) sowie die Einbettungs- und Link-Relationen, die der Richtext-Editor registriert. Eine
Relation zählt, wenn sie stammt

- aus der **veröffentlichten Version**, einem **Entwurf**, einem **internen Entwurf** (eine laufende Bearbeitung),
  einer **ausstehenden** oder einer **in der Warteschlange befindlichen** Version, und
- aus einem Objekt, das **veröffentlicht** ist oder ein **Entwurfsobjekt** ist (noch nie veröffentlicht).

**Nicht** zählen: Relationen aus einem Objekt **im Papierkorb**, aus **alten (archivierten), abgelehnten oder
wiederholten Versionen** und die Relation eines Objekts **zu sich selbst**. Ein Bild, das nur ein Entwurf verwendet,
wird also nicht aufgeführt (der Entwurf kann noch veröffentlicht werden); ein Bild, das nur eine Seite im Papierkorb
verwendet hat, wird aufgeführt. Wird die Seite aus dem Papierkorb wiederhergestellt, zeigt sich ihre Einbettung als
ungültige Relation.

### Der Verweisindex

Viele Verweise erzeugen nie eine Relationszeile: direkt in die Datenbank importierte Inhalte, Datentypen, die keine
Relationen registrieren, und alles außerhalb der Felder. Die Prüfungen lesen sie in einen **Verweisindex**
(`expContentCheckReferences`) ein, aus denselben zählenden Versionen und Objekten wie oben:

| Quelle | Was gelesen wird |
|---|---|
| Felder | `ezobjectrelation` (`data_int`), `ezobjectrelationlist` (`contentobject-id`, `node-id`), `ngenhancedlink` (die ID eines internen Links, die ein Objekt oder ein Knoten sein kann und keine Relationszeile schreibt: beides zählt) und im Text jedes Feldes: `object_id`/`node_id` von Links und Einbettungen, `ezobject://`, `eznode://`, `ezcontent://`, `ezlocation://`, `exp-remote-location://`, `content/view/<mode>/<node>`, `content/download/<object>`, Pfade von Speicherdateien (`var/.../storage/...`, nachgeschlagen in `ezimagefile`, `ezbinaryfile`, `ezmedia`) und interne Pfade von Links (`href="/media/images/logo"`, verglichen mit dem URL-Alias mit oder ohne Siteaccess-Präfix oder Host) |
| Links | die Tabelle `ezurl` (Links aus Richtext- und URL-Feldern), eigene URL-Aliasse (`ezurlalias_ml`, `is_alias`), URL-Wildcards |
| Layouts | Sammlungseinträge (die `value_id` eines Ortseintrags ist eine **Knoten**-ID, die eines Inhaltseintrags eine Objekt-ID), dynamische Sammlungen (ihr übergeordneter Knoten und Themeninhalt; die Kinder oder der Teilbaum des übergeordneten Knotens, mit den Klassen, die die Sammlung durchlässt; eine Sammlung des aktuellen Orts listet unterhalb der Knoten, auf die die Regeln ihres Layouts zielen: die Kinder eines Knotenziels, alles unter einem Teilbaumziel und, bei einer Regel ohne solche Ziele, einem gemeinsam genutzten Layout oder einem Layout ohne Regeln, überall, beschränkt auf übergeordnete Elemente der Klassen aus den Klassenbedingungen der Regel), Block-Parameter (Links als `exp-remote-location://<remote id>`, `ezlocation://`, `ibexa-location://` über `explayouts.ini [NexusNodeMap]`; IDs in Parametern, die nach Inhalten, Bildern oder Knoten benannt sind; HTML mit Speicherpfaden oder Links), Regelziele (Knoten, Teilbaum, Inhalt, Pfade). Jeder Status, sodass die Blöcke eines Entwurfs zählen; die Blöcke eines gemeinsam genutzten Layouts sind Zeilen wie alle anderen |
| Einstellungen | jede Einstellungsdatei (`settings/`, seine Siteaccess- und Override-Verzeichnisse, das `settings/` jeder Extension): die ganzen Zahlen in einer Einstellung, deren Block oder Name von Knoten, Objekten, Wurzeln, übergeordneten Elementen, Teilbäumen, Containern oder Ordnern spricht (`RootNode`, `MediaRootNode`, `TopNodeID`, `NexusFooterMenuID[]` ...), Remote-IDs darin und Pfade `content/view/...` in jeder Einstellung (`IndexPage`) |
| Templates | wörtliche IDs in den Templates jedes Designs: `hash( 'node_id', 58 )`, `parent_node_id`, `object_id`, `content/view/full/77` |
| Rollen | auf einen Teilbaum beschränkte Rollenzuweisungen, Richtlinien mit Einschränkung nach Knoten, übergeordnetem Knoten oder Teilbaum |
| Tabellen | die Spalten von `ObjectColumns[]`, `NodeColumns[]` und `NodeListColumns[]`: standardmäßig der Shop (Produktsammlungen von Warenkörben, Wunschlisten und Bestellungen), gesammelte Informationen, ausstehende Aktionen, Rollenzuweisungen, Teilbaum-Benachrichtigungen, Lesezeichen, wo Entwürfe platziert werden (`eznode_assignment`) und der Newsletter (Artikel von Ausgaben, Versendungen, verfolgte Links, Zeitpläne, Freigaben, Listen). Eine Tabelle, die nicht existiert, wird übersprungen, sodass sich die Tabellen einer Extension hinzufügen lassen |
| Newsletter | die Artikelpools: ihre übergeordneten Knoten und deren Kinder der Klassen des Pools |

`eztags` wurde geprüft und trägt nichts bei: Eine Tag-Verknüpfung geht von einem Objekt zu einem Tag, nie zu einem
Objekt.

### Was sich nicht wissen lässt

- Ein Template, das eine ID **berechnet** (`fetch( 'content', 'node', hash( 'node_id', $id ) )`), PHP-Code mit einer
  ID und alles außerhalb dieser Installation (eine andere Website, ein bereits versandter Newsletter, ein Lesezeichen
  in einem Browser). Prüfen Sie ein Asset, bevor Sie es entfernen, wenn es solchen Code gibt; der Papierkorb hält es
  wiederherstellbar.
- Einstellungen werden anhand der Namen ihrer Einstellungen gelesen: Eine Knoten-ID in einer anders benannten
  Einstellung wird nicht gesehen.
- IDs **importierter Inhalte**: Eine Website, die aus Inhalten einer anderen Installation aufgebaut wurde, kann die
  IDs dieser Installation behalten (die Media-Site-Demo behält sie in erweiterten Links und Parametern von
  Layout-Komponenten) und sie in ihren Templates über die Remote-ID oder mit einem Versatz auflösen. Sofern
  `[ReferenceSettings] ImportedIdRemotePatterns[]` und `ImportedIdObjectPatterns[]` nicht dieselben Muster nennen,
  erscheint ein solcher Link als ungültig, und sein Ziel kann als unbenutzt aufgeführt werden.
- Ein Index, der älter als `IndexTTL` ist (siehe [Einstellungen](#einstellungen)), kann übersehen, was seitdem
  hinzugekommen ist; das Verschieben in den Papierkorb baut ihn immer neu auf.

Kann eine Quelle nicht gelesen werden, zeigt die Liste nichts und sagt das: Ein unvollständiger Index würde verwendete
Inhalte als unbenutzt bezeichnen.

## Nicht verknüpfte Objekte

Öffnen Sie **Setup > Nicht verknüpfte Objekte** oder **Fortgeschritten > Nicht verknüpfte Objekte im Teilbaum** im
Kontextmenü eines Knotens. Ohne Knoten in der Adresse beginnt die Liste beim obersten Knoten des Inhaltsbaums
(`content.ini [NodeSettings] RootNode`).

- **Aufgeführt** werden die nicht verknüpften Assets, deren **Hauptort** unterhalb des Knotens liegt (nicht der Knoten
  selbst). Ein Objekt, das auch anderswo Orte hat, wird unter seinem Hauptort aufgeführt; die Spalte Ort sagt, wie
  viele es hat.
- **Objekte mit Unterelementen** werden weggelassen, sofern **Auch Objekte mit Unterelementen** nicht angekreuzt ist.
- **Klassen**: die Asset-Klassen; ist keine gewählt, wird jede davon aufgeführt.
- **Sortieren nach** Name, Klasse, zuletzt geändert, veröffentlicht oder Objekt-ID, aufsteigend oder absteigend. Die
  Reihenfolge ist über die Seiten hinweg stabil (bei Gleichstand entscheidet die Objekt-ID).
- **Spalten**: Name (ein Link zum Knoten, mit dem Tag Versteckt bei einem versteckten Ort), Klasse, Größe, zuletzt
  geändert und Ort (der Pfad des Hauptorts, die Anzahl der Orte und die Anzahl der Unterelemente, wenn diese angezeigt
  werden). Die **Größe** ist die der Dateien der aktuellen Version (Bild, Binärdatei, Medium), gelesen aus dem Speicher
  oder dem Cluster, und zwar nur für die Zeilen der Seite; `–` bei einem Objekt ohne Dateien oder mit fehlenden
  Dateien.
- **Pro Seite**: die Größen aus `admininterface.ini [PaginationSettings]` (10, 25, 50, sofern nicht anders
  konfiguriert), gespeichert als Präferenz `admin_contentcheck_list_limit`.
### In den Papierkorb verschieben

Kreuzen Sie Objekte an und klicken Sie auf **Angekreuzte Objekte in den Papierkorb**. Eine Zeile hat nur dann ein
Kästchen, wenn Sie sie verschieben dürfen: Sie besitzen `contentcheck/trash`, Sie dürfen ihren Hauptort entfernen,
und sie hat keine Unterelemente. Die nächste Seite listet jedes angekreuzte Objekt mit dem auf, was mit ihm geschehen
wird, und bittet Sie um Bestätigung. Erst bei **… Objekte in den Papierkorb verschieben** geschieht etwas.

Bei der Bestätigung und noch einmal bei der Ausführung wird jedes Objekt neu geprüft. Es wird nur verschoben, wenn

- es weiterhin ein veröffentlichtes Asset ist und **es jetzt nichts verwendet**, beurteilt anhand eines in diesem
  Moment aufgebauten Verweisindex (eine inzwischen hinzugefügte Relation, ein Layout-Block oder etwas anderes hält es
  zurück);
- Sie es lesen und **jeden seiner Orte** entfernen dürfen (`content/read`, `content/remove`);
- **keiner seiner Orte Unterelemente hat**, versteckte und solche, die Sie nicht lesen dürfen, eingeschlossen: Die Aktion verschiebt einzelne Objekte; entfernen Sie einen Container im Inhaltsbaum,
  wo seine Unterelemente angezeigt werden;
- seine Klasse den Papierkorb erlaubt, es keine Version hat, die auf die Veröffentlichung wartet, und kein
  Inhaltsauftrag es hält.

Die Objekte kommen in den **Papierkorb** und werden nie endgültig gelöscht, über die eigene Entfernung des Kernels (die
Operation `content/delete` mit `move_to_trash`, wie **Entfernen** im Inhaltsbaum), ein Objekt nach dem anderen: Eines,
das fehlschlägt, lässt die übrigen erledigt. Workflows, Caches und den Suchindex behandelt diese Operation. Jedes wird
im Audit als `content.node.remove.trash` mit der ausführenden Person festgehalten und kann aus dem Papierkorb
wiederhergestellt werden. Das Formular sendet mit dem Formular-Token der Sitzung (und dem von ezformtoken); eine andere
Methode als POST wird abgewiesen.

Höchstens `MaxTrashBatch` Objekte (50) werden aus einer Bestätigung übernommen; die Seite sagt es, wenn mehr
angekreuzt wurden. Die Bestätigung ist an genau die angezeigten Objekte und an Ihre Sitzung gebunden: Haben sich die
Objekte oder Ihre Rechte dazwischen geändert, wird nichts verschoben, und die Liste sagt das.

## Ungültige Relationen

Öffnen Sie **Setup > Ungültige Relationen** oder **Fortgeschritten > Ungültige Relationen im Teilbaum** für einen
Knoten. Ohne Knoten wird der oberste Knoten des Inhaltsbaums geprüft (`content.ini [NodeSettings] RootNode`); geben Sie
1 ein, um den ganzen Baum zu prüfen (auch Medien, Benutzer und die anderen obersten Knoten), was ein uneingeschränktes
`content/read` erfordert.

**Quellen** sind veröffentlichte Objekte, deren Hauptort sichtbar ist (nicht versteckt, nicht unter einem versteckten
Knoten), unterhalb des Knotens und nicht unterhalb eines ausgelassenen Knotens liegt und die Sie lesen dürfen. Von der
**veröffentlichten Version** jeder Quelle liest die Prüfung

- ihre Relationen in `ezcontentobject_link` (Objektbeziehungen, Beziehungsfelder und die Einbettungs- und
  Link-Relationen ihres Richtexts) und
- die Links und Einbettungen im Text ihrer **XML-Felder** (`XmlDatatypes[]`, standardmäßig `ezxmltext`): `object_id`
  und `node_id` von `<link>`, `<embed>` und `<embed-inline>`, in jeder Sprache. Diese nennen ein Objekt oder einen
  Knoten auch dann noch, wenn er gelöscht wurde und seine Relationszeilen mit ihm verschwunden sind;
- die Daten ihrer **Beziehungsfelder** (`ezobjectrelation`, `ezobjectrelationlist`, `ngenhancedlink`), die ein Ziel
  auch dort nennen, wo keine Relationszeile geschrieben wurde (ein Import; ein erweiterter Link schreibt nie eine). Die
  ID eines erweiterten Links wird als Objekt beurteilt oder als Knoten, wenn kein Objekt diese ID hat.

Mit der ersten Seite listet für einen Benutzer, der alle Inhalte lesen darf, der Abschnitt **Layouts** auf, was die
Layouts nennen und ein Besucher nicht erreichen kann: manuelle Sammlungseinträge (nach Knoten oder Objekt), den
übergeordneten Knoten einer dynamischen Sammlung (über `[NexusNodeMap]` abgebildet, wie es die Layouts tun),
Block-Links (`exp-remote-location://`, `ezlocation://`) und Regelziele nach Knoten oder Teilbaum, mit dem Layout, dem
Block oder der Regel und dem Zustand des Ziels. Beheben Sie sie im Layout-Editor.

Ein **Ziel** wird gemeldet, wenn es

| Zustand | Bedeutung |
|---|---|
| Gelöscht | es gibt kein Objekt (oder keinen Knoten) mit dieser ID mehr |
| Im Papierkorb | das Objekt liegt im Papierkorb, oder der Knoten (bei einem Knoten-Link oder einer Knoten-Einbettung) |
| Versteckt | jeder Ort des Objekts ist versteckt oder liegt unter einem versteckten Knoten; oder der verlinkte Knoten ist es |
| Nie veröffentlicht | das Objekt ist ein Entwurf, der nie veröffentlicht wurde |
| Ohne Ort | das Objekt ist veröffentlicht, hat aber keinen Ort im Baum |
| Für Sie nicht verfügbar | bei eingeschränktem `content/read`: Das Ziel hat keinen Ort, den Sie lesen dürfen, unabhängig von seinem Zustand |

Ein einwandfreies Ziel wird nicht aufgeführt, ebenso wenig die Relation einer Quelle zu sich selbst. Eine Relation des
Richtexts und der Link in seinem Text sind ein einziger Befund; das Feld ist das, dessen Text den Link enthält.

Jeder Befund zeigt die **Quelle** (einen Link zu ihrem Knoten, ihre Klasse und Objekt-ID), das **Feld** (den Namen des
Feldes, die Sprache bei einem Link im Richtext und die Art: Link, Einbettung, eingebettet im Text, Objektbeziehung,
Beziehungsfeld), das **Ziel** (Objekt- oder Knoten-ID; den Namen nur bei einem Ziel, das nie veröffentlicht wurde oder
ohne Ort ist und das Sie lesen dürfen: Ein gelöschtes, im Papierkorb liegendes oder verstecktes Ziel wird nur mit
seiner ID und seinem Zustand gezeigt; ein Ziel, das Sie nicht lesen dürfen, als „nicht verfügbar“, siehe
[Was Sie nicht lesen dürfen](#was-sie-nicht-lesen-dürfen)), seinen **Zustand** mit dem, **was zu tun ist** (den Verweis
entfernen oder ein anderes Ziel wählen; es aus dem Papierkorb wiederherstellen; es sichtbar machen; es veröffentlichen;
ihm einen Ort geben; jemanden fragen, der es lesen darf), und einen Button **Bearbeiten**, wenn Sie die Quelle
bearbeiten dürfen. Die Filter:

- **Unterhalb von Knoten**: der zu prüfende Teilbaum (1 prüft den ganzen Baum).
- **Knoten auslassen (mit allem darunter)**: Knoten-IDs, durch Kommas getrennt (bis zu 20); die Quellen darunter
  werden nicht geprüft, zum Beispiel der Teilbaum Benutzer und der Teilbaum Design.
- **Ziele**: die zu meldenden Zustände.
- **Verweise**: Links und Einbettungen im Richtext, Objektbeziehungen und Beziehungsfelder oder beides.

### Seiten

Die Prüfung geht die Quellen nach Objekt-ID durch, `ChunkSize` Objekte (200) mit einem Satz von Abfragen, und hält an,
wenn eine Seite mit Befunden voll ist oder `ScanLimit` Objekte (5000) geprüft wurden. **Nächste Seite** macht nach dem
zuletzt geprüften Objekt weiter; der Seitenkopf sagt, wie viele Objekte geprüft wurden und nach welchem Objekt. Eine
Seite endet immer erst nach einer vollständigen Quelle, sodass sie ein paar Befunde mehr zeigen kann, als ihre Größe
vorgibt. Es gibt keine Gesamtzahl: Zählen hieße, den ganzen Baum zu lesen, und genau das vermeiden die Seiten.
**Von vorn** beginnt erneut.

## Wo eine Prüfung beginnt

Jede Prüfung hat ein Feld für den **Startort** (die Knoten-ID) und darunter zwei Reihen von Buttons:

- die **Schnellauswahl-Buttons** aus `contentcheck.ini [ScopeSettings] PresetList[]`: **Ganzer Baum** (Knoten 1, für
  Benutzer mit uneingeschränktem `content/read`), **Websites** (die Inhaltswurzel, `content.ini [NodeSettings]
  RootNode`) und **Medien** (`MediaRootNode`), danach **Mit der Knotenauswahl wählen**, das die gemeinsame
  Knotenauswahl (content/browse) öffnet und zur selben Prüfung zurückkehrt. Benutzer und jeder andere Knoten werden
  über die Knotenauswahl erreicht. Weitere Schlüssel für die Liste: `users` (**Benutzer**), `setup`, `design`, eine
  Knoten-ID oder ein beliebiger URL-Alias-Pfad wie `media/images` (diese zeigen den Namen des Knotens). Ein Button,
  dessen Knoten nicht existiert oder den Sie nicht lesen dürfen, wird weggelassen. Die Inhalts- und die Medienwurzel
  sind die aus `content.ini [NodeSettings]`; setzen Sie sie dort;
- die **Websites und Bereiche** unter der Inhaltswurzel: ein Button pro sichtbarem Kind der Inhaltswurzel, das Sie
  lesen dürfen (nicht versteckt, nicht unsichtbar), in der eigenen Sortierreihenfolge der Inhaltswurzel, sodass eine
  Prüfung im Teilbaum einer Website oder eines Bereichs beginnen kann. Bei jeder Seite live aus dem Inhaltsbaum
  gelesen; Namen werden maskiert. Höchstens `SiteRowLimit` (standardmäßig 12); bei mehr öffnet **Mehr …** die
  Knotenauswahl auf dieser Ebene (`contentcheck/scope/<view>/<node
  id>/<start node id>`; ein Abbrechen kehrt zur Prüfung zurück, wo sie war). Die Website, in der der Startort liegt,
  wird umrandet. `SiteRow=disabled` blendet die Reihe aus; `SiteRowParent` (standardmäßig `content`) nimmt einen
  anderen Preset-Schlüssel, eine Knoten-ID oder einen URL-Alias-Pfad als aufzulistende Ebene.

Der Button des aktuellen Startorts ist markiert (`aria-current="true"`, die Primärfarbe). Jeder Button ist ein
einfacher Link auf `contentcheck/<view>/<node id>`, sodass er ohne JavaScript und per Tastatur (Tab, Enter)
funktioniert und auf Smartphones umbricht; die Speicherdateien behalten die angezeigte Liste bei (`?Show=...`). Eine
Prüfung liest ihren Knoten aus dem Formular (`NodeID`), sonst aus der Adresse (`contentcheck/<view>/<node id>`), sonst
aus Ihrer letzten Wahl, sonst den obersten Knoten des Inhaltsbaums. Ein im Formular oder in der Adresse angegebener
Knoten wird zu Ihrer letzten Wahl (die Präferenz `admin_contentcheck_scope`), sodass sich die nächste Prüfung dort
öffnet; die Adresse trägt ihn immer, sodass ein Link auf eine Prüfung jedem, der ihn lesen darf, denselben Teilbaum
zeigt.

```ini
# settings/override/contentcheck.ini.append.php
[ScopeSettings]
PresetList[]
PresetList[]=tree
PresetList[]=content
PresetList[]=media
PresetList[]=users
SiteRow=enabled
SiteRowParent=content
SiteRowLimit=12
```

Die Auswahl ist ein einziges Template, `design:contentcheck/scope.tpl` (dieselbe Datei in design/admin und
design/admin4), eingebunden in das Filterformular jeder Prüfung; die Ansicht übergibt
`expContentCheckScope::forTemplate( $nodeID, '<view>' )` (ein drittes Argument listet einfache GET-Felder auf, die die
Links beibehalten). Seine Styles sind die gemeinsame Komponente `.a4-quickpick` in `stylesheets/components.css`, und
die Presets und Reihen stammen aus `expNodeQuickPick`, das auch die Schnellauswahl-Buttons für Orte auf anderen Seiten
verwenden (`quick-location-buttons.md`). Die Ansichten, die einen Startort annehmen (und zu denen die Knotenauswahl
zurückkehren darf), sind in `ViewList[]` aufgeführt; eine weitere Prüfung fügt ihre Ansicht dort hinzu.
## Optionen in der Adresse

Die Optionen jeder Prüfung sind View-Parameter, nie ein Query-String (ein `?Show=` oder `?Kind[]=` wird nicht gelesen):

| Ansicht | View-Parameter |
|------|-----------------|
| `contentcheck/unreferenced/<node id>` | `(class)` (Klassen-IDs, durch Kommas getrennt), `(sort)`, `(order)`, `(containers)/1`, `(selectall)/1`, `(offset)` |
| `contentcheck/brokenrelations/<node id>` | `(exclude)` (Knoten-IDs), `(kind)`, `(relation)` (Kommalisten, `none`, wenn nichts angehakt ist), `(after)` und für die fehlenden Dateien `(problem)`, `(version)`, `(selectall)/1`, `(file_offset)` |
| `contentcheck/storage/<node id>` | `(show)` (orphans, problems, quarantine), `(sort)`, `(order)`, `(kind)` (Kommaliste oder `none`), `(selectall)/1`, `(offset)` |

Zum Beispiel `contentcheck/brokenrelations/58/(kind)/deleted,trash/(after)/1200`. Die Filterformulare werden per POST
gesendet (sie tragen das Formular-Token) und mit einem 303 auf diese Adresse beantwortet, sodass sich eine gefilterte
Liste als Lesezeichen speichern und weitergeben lässt.

## Ein einheitlicher Aufbau

Jede Prüfung hat dieselbe Reihenfolge: den **Startort**, die **Filter** (eine Gruppe von Auswahlmöglichkeiten hat
**Alle auswählen** und **Keine auswählen**, Buttons nach **Anzeigen**, sodass Enter weiterhin die Liste anzeigt), den
**Ergebniskopf** („X bis Y von N“), die **Tabelle**, deren Kästchen im Kopf jede Zeile der Seite ankreuzt oder leert
und „n von m ausgewählt“ anzeigt (ohne JavaScript: **Alle auf dieser Seite auswählen** und **Keine auswählen**), die
**Sammelaktionen** (die angekreuzten Zeilen oder „alle N Ergebnisse“: die ganze Liste mit ihren Filtern, höchstens ein
Stapel, immer zuerst mit dem Plan oder der Bestätigung) und die **Blätterleiste** (Seitengrößen und Seitenlinks, die
Filter bleiben in der Adresse erhalten). Die Teile sind gemeinsame Templates in `contentcheck/`: `scope.tpl`,
`filter_choice.tpl` (`group=`, gelesen als GET `<group>Choice=all|none`), `results_head.tpl`, `select_all.tpl`,
`bulkbar.tpl` (mit `select_count.tpl`), `pager.tpl` (`offset_name` für eine zweite Liste auf einer Seite) und
`contentcheck_select.js`.

## Speicherdateien

Öffnen Sie **Setup > Speicherdateien**. Die Seite zeigt die letzte **Prüfung** des Speicherverzeichnisses
(`var/<site>/storage`, die Verzeichnisse aus `ScanDirectoryList[]`: `images`, `images-versioned`, `original`) und drei
Listen:

- **Verwaiste Dateien**: Dateien, die kein Inhalt verwendet, mit Pfad, Größe, Alter, Eigentümer und Rechten sowie den
  Summen (Anzahl und Bytes). Sie gehören zu keinem Inhalt, daher gilt der Startort für sie nicht: Der ganze Speicher
  wird aufgelistet. Sortierung nach Pfad, Größe oder Alter.
- **Dateiprobleme**: Dateien, die Inhalte verwenden, von den Objekten unterhalb des Startorts, die Sie lesen dürfen,
  mit
  - **Falscher Eigentümer**: ein anderer Eigentümer als `ExpectedOwner` (leer: der Eigentümer des
    Speicherverzeichnisses);
  - **Falsche Rechte**: fehlende Bits der erwarteten Rechte (`FileMode`, leer: `site.ini [FileSettings]
    StorageFilePermissions`); zusätzliche Bits sind kein Problem;
  - **Leere Datei**: null Bytes;
  - **Name weicht in der Groß-/Kleinschreibung ab**: Das Bild-XML nennt eine Datei, die fehlt, aber eine mit demselben
    Namen in anderer Schreibweise ist vorhanden;
  - **Pfad weicht von seinen Teilen ab**: Die `url` eines Bildes entspricht nicht seinem `dirpath` und `filename`;
  - **Im Verzeichnis eines anderen Typs**: Eine Datei oder Mediendatei liegt nicht in
    `original/<first part of its MIME type>/`, sondern im Verzeichnis eines anderen Typs.

  Eine vollständig fehlende Datei wird hier nicht aufgeführt; die Prüfung auf fehlende Dateien meldet sie.
- **Quarantäne**: die Dateien in der Quarantäne, nach Datum, zum Wiederherstellen oder Löschen.

### Was als verwendet zählt

Eine Datei ist verwendet, wenn eine der folgenden Stellen sie nennt, aus **jeder** Version und jedem Objektzustand
(auch alte Versionen und der Papierkorb: Ein Bild einer alten Version wird im Verlauf noch angezeigt und mit ihr
wiederhergestellt):

1. `ezimagefile` (jede Bild- und Bildvariantendatei);
2. das Bild-XML (`data_text`) des Attributs, das ihr Verzeichnis nennt (`.../<attribute id>-<version>-<language>/`),
   über `url` oder über `dirpath` und `filename`, des Originals oder einer beliebigen Bildvariante;
3. `ezbinaryfile` oder `ezmedia`, über ihren Namen (Dateien und Medien werden als `original/<type>/<name>`
   gespeichert);
4. der [Verweisindex](#der-verweisindex): ein Speicherpfad im Text eines beliebigen Feldes, ein Layout-Block, eine
   Einstellung oder ein Template.

Eine Datei, die jünger als `MinAge` (ein Tag) ist, ist nie verwaist: Ein Upload schreibt die Datei vor ihrer Zeile.
Pfade in `IgnorePathList[]` (standardmäßig `images/_video-thumbnails`, ein Cache des Media-Themes) werden nie
aufgeführt. Symbolischen Links wird nicht gefolgt. Kann der Verweisindex nicht vollständig gelesen werden, sagt die
Seite das, und es kann nichts verschoben werden.

### Die Prüfung

Die Prüfung liest jeweils `ScanBatchSize` Dateien (2.000) und beurteilt sie mit einem Satz von Abfragen pro Stapel;
im Browser läuft ein Schritt `StepSeconds` (4) lang, und die Seite macht von selbst weiter (ein kleines Skript klickt
auf **Prüfung fortsetzen**; ohne es klicken Sie selbst). **Hier anhalten** behält das Gelesene; **Prüfung fortsetzen**
macht später weiter, **Neu beginnen** von vorn. Danach liest die Prüfung die Bildattribute sowie die Datei- und
Medienzeilen in Teilen für die Namens- und Pfadprobleme. Ihr Zustand und ihr Ergebnis werden im Cache-Verzeichnis
gehalten (`contentcheck/storage-scan.json`); höchstens `MaxListed` verwaiste Dateien und Probleme (5.000) werden
behalten, die Summen zählen alle. Bei einem großen Speicher verwenden Sie die Kommandozeile.

Mit einem **Cluster**-Dateihandler außer dem Dateisystem (DFS, Datenbank) werden die Dateien aus seiner Tabelle gelesen
(`ezdfsfile`, `ezdbfile`); Eigentümer und Rechte werden nicht geprüft, und nichts kann geändert werden: nur Bericht.

## Quarantäne

Aus dem Speicher wird nie etwas gelöscht. Die Aktionen, jeweils für die angekreuzten Dateien einer Liste:

| Aktion | Was sie tut | Richtlinie |
|---|---|---|
| In die Quarantäne verschieben | verschiebt verwaiste Dateien nach `var/<site>/storage-quarantine/<date>/<their path below the storage>` (`QuarantineDirectory`) und verzeichnet sie dort in `.manifest.jsonl` | `contentcheck/quarantine` |
| Wiederherstellen | verschiebt eine Datei aus der Quarantäne an ihren Platz zurück, nie über eine Datei, die jetzt dort liegt | `contentcheck/quarantine` |
| Endgültig löschen | löscht eine Datei aus der Quarantäne; die Bestätigung braucht zusätzlich das Kästchen „Mir ist bewusst ...“ | `contentcheck/quarantine` |
| Eigentümer und Rechte korrigieren | setzt nur den erwarteten Eigentümer und die erwarteten Rechte einer verwendeten Datei, sonst nichts | `contentcheck/permissions` |

Jede Aktion ist zuerst ein **Probelauf**: Die nächste Seite listet jede angekreuzte Datei mit dem auf, was mit ihr
geschehen würde, und ändert nichts. Erst **Bestätigen** führt sie aus, gesendet mit dem Formular-Token der Sitzung (und
dem von ezformtoken) und einem Bestätigungs-Token, das an genau die angezeigten Dateien und an Ihre Sitzung gebunden
ist. Jede Datei wird bei der Ausführung erneut geprüft: Eine Datei, die ein Inhalt jetzt verwendet, die jünger als
`MinAge` ist, die ein Link ist, die außerhalb des Speichers oder der Quarantäne liegt oder deren Ziel belegt ist, bleibt
unverändert. Höchstens `MaxActionBatch` Dateien (200) werden aus einer Bestätigung übernommen. Jede Aktion wird im
Audit festgehalten (`content.file.orphan.quarantine`, `content.file.orphan.restore`, `content.file.orphan.delete`,
`content.file.permissions.fix`, mit der Anzahl der Dateien, ihren Bytes und ihren Pfaden; eine Abweisung wegen einer
fehlenden Richtlinie als `content.file.orphan.refused`).

**Eigentümer und Rechte**: Das Ändern des Eigentümers erfordert root; das Ändern der Rechte erfordert root oder den
Eigentümer der Datei. Der Plan sagt für jede Datei, ob dieser Serverprozess es kann. Wo zwei Benutzer in den Speicher
schreiben (ein Anwendungsserver, der als root läuft, und PHP-FPM als Benutzer der Website, sodass manche Dateien root
gehören), führen Sie die Kommandozeile als root aus:
`./console exp:content-check --storage --fix-permissions --dry-run`, dann ohne `--dry-run`.
## Fehlende Dateien

Kreuzen Sie unter **Ungültige Relationen** das Ziel **Fehlende Dateien in var/<site>/storage** an
(`Kind[]=missing_file`; Setup > Inhaltsmodell > Ungültige Relationen oder **Ungültige Relationen im Teilbaum** im
Kontextmenü), und die Seite listet unter den ungültigen Relationen die dateibasierten Felder der Inhalte unterhalb des
Knotens auf, deren Datei nicht dort liegt, wo das Feld es angibt, sowie die Bilder, deren Bildvarianten fehlen oder
veraltet sind. Allein angekreuzt wird keine Relation geprüft, und nur die fehlenden Dateien werden aufgeführt. Damit erscheinen zwei weitere Filter: die Probleme und die Versionen. Jede Zeile zeigt das Objekt (mit einem Link zu seinem Knoten), das Feld und seine Sprache,
die Version (veröffentlicht oder Entwurf), den **erwarteten Pfad** unterhalb von `var/<site>/storage`, die Zeit der
letzten Änderung der Version (und bei einer Bildvariante die eigene Zeit der Datei) sowie das Problem:

| Problem | Bedeutung |
|---|---|
| **Datei fehlt** | die Originaldatei der Feldversion liegt nicht im Speicher (ein Bildoriginal, eine Binärdatei, eine Mediendatei) |
| **Variante fehlt** | das XML des Bildes nennt eine Bildvariante (z. B. `small`, `reference`), deren Datei nicht vorhanden ist; das Original ist vorhanden |
| **Variante veraltet** | eine Bildvariante, die mit anderen Einstellungen in `image.ini` erstellt wurde (ihr Schlüssel weicht vom jetzigen Schlüssel der Variante ab), bevor die Bildvarianten zuletzt für ungültig erklärt wurden, oder die älter als das Original ist |
| **Variante nie erstellt** | eine Bildvariante aus `image.ini`, die das Bild noch nicht hat; das ist normal (Bildvarianten werden erstellt, wenn ein Bild zum ersten Mal in ihnen angezeigt wird), daher wird sie nur aufgeführt, wenn angekreuzt |

- **Welche Felder**: jeder in `[MissingFileSettings] Handlers[]` genannte Datentyp: `ezimage` (das Original und jede
  Bildvariante, die sein XML nennt), `ezbinaryfile` und `ezmedia` (die Datei in ihrer Tabelle). Eine Extension fügt
  ihren eigenen Datentyp mit einer Klasse hinzu, die `expContentCheckFileHandler` implementiert; ein Datentyp, der seine
  Datei in einer Tabelle mit den Spalten von `ezbinaryfile` führt, braucht nur
  `Handlers[<datatype>]=expContentCheckFileTable` und `Tables[<datatype>]=<table>`.
- **Welche Versionen**: die veröffentlichte Version und die Entwürfe (Entwurf, interner Entwurf, ausstehend)
  veröffentlichter Objekte, deren Hauptort unterhalb des Knotens liegt; filtern Sie mit **Veröffentlichte Version** und
  **Entwürfe**. Archivierte Versionen werden nicht geprüft: Nichts zeigt sie an.
- **Wo**: Jede Datei wird über `eZClusterFileHandler` nachgeschlagen, sodass auf einem Cluster (DFS) der Speicher des
  Clusters zählt, nicht die lokale Festplatte eines Servers.
- **Seiten**: Die Liste hat eine Gesamtzahl („1 bis 25 von 446“), Seitenlinks und die Seitengröße der ungültigen
  Relationen (`ItemsPerPageList_contentcheck_brokenrelations[]`); ihre Seiten stehen als `(file_offset)` in der
  Adresse, getrennt von denen der Relationen. Für eine Gesamtzahl wird jedes Objekt unterhalb des Knotens durchlaufen
  (`ChunkSize` Objekte, 200, pro Satz von Abfragen, höchstens `ListLimit`, 50.000; bei mehr sagt die Liste es), und die
  Befunde werden `ListCacheTTL` (60) Sekunden pro Benutzer und Filter behalten, sodass die nächsten Seiten sie nicht
  erneut durchlaufen; eine Behebung verwirft sie. Der ganze Baum der Demonstrations-Website (714 Objekte,
  446 Befunde) braucht 2,4 s; die nächsten Seiten wenige Millisekunden.
- **Zur Prüfung markiert** erscheint an einer Zeile, die jemand markiert hat (siehe unten), bis die Zeile behoben ist.

### Beheben

Kreuzen Sie Zeilen an und wählen Sie, was mit ihnen geschehen soll, oder verwenden Sie den eigenen Button einer Zeile.
Die nächste Seite ist immer eine **Vorschau**: jede gewählte Zeile mit dem, was mit ihr geschehen wird, oder warum es
nicht möglich ist. Erst ein **POST** dieser Vorschau mit dem Formular-Token und einem Bestätigungs-Token, das an genau
die angezeigten Zeilen gebunden ist, bewirkt etwas; haben sich die Dateien oder Ihre Rechte dazwischen geändert, wird
nichts getan, und die Liste sagt das.

- **Auswählen**: Das Kästchen im Tabellenkopf kreuzt jede Zeile der Seite an oder leert sie und zeigt einen gemischten
  Zustand, wenn einige angekreuzt sind; „N von M ausgewählt“ zählt sie (`contentcheck_select.js`, wie bei den anderen
  Listen der Prüfungen). Ohne JavaScript tun **Alle auf dieser Seite auswählen** und **Keine auswählen** dasselbe auf
  dem Server (`SelectAll=1`). Gibt es weitere Seiten, übernimmt **Vorschau für alle N Ergebnisse** jede Zeile
  jeder Seite mit denselben Filtern, höchstens `MaxFixItems`, und zeigt ebenfalls die Vorschau.
- Die Filtergruppen **Fehlende Dateien: Probleme** und **Fehlende Dateien: Versionen** haben die Buttons **Alle
  auswählen** und **Keine auswählen** (Absende-Buttons, sodass sie ohne JavaScript funktionieren); ist nichts
  angekreuzt, wird nach nichts gesucht.

| Aktion | Was sie tut | Angeboten für |
|---|---|---|
| **Bildvarianten aus dem Original neu erstellen** | entfernt die Einträge der Bildvarianten aus dem XML des Bildes und erstellt jede Variante neu aus dem Original, so wie der Bild-Datentyp es tut, wenn ein Bild zum ersten Mal angezeigt wird (die Dateien werden an ihre Pfade geschrieben); das Original wird nicht verändert | Variante fehlt, veraltet oder nie erstellt |
| **Aus einer anderen Version wiederherstellen** | kopiert die Datei einer anderen Version desselben Feldes, die sie noch hat (zuerst die veröffentlichte Version, dann die neueste), an den erwarteten Pfad; ein Bild erhält danach wieder seine Bildvarianten | Datei fehlt |
| **Datei neu hochladen oder ersetzen** | legt eine hochgeladene Datei an den erwarteten Pfad: Sie muss dieselbe Endung haben (nie ein Skript, HTML oder SVG), und ein Bild muss ein Bild sein; `MaxReplaceSize` begrenzt die Größe | Datei fehlt |
| **Toten Verweis entfernen** | leert das Feld in dieser Version (das Bild-XML oder die Zeile der Dateitabelle): Es nennt die fehlende Datei nicht mehr; nur in der Datenbank. Ein Pflichtfeld muss bei der nächsten Bearbeitung wieder gefüllt werden | Datei fehlt |
| **Zur Prüfung markieren** | markiert die Feldversion (wer, wann, das Problem; in `ezsite_data` als `contentcheck.review.<attribute id>.<version>`), sodass sich jemand sie später ansehen kann | jede Zeile |

- Nie wird etwas gelöscht oder überschrieben: Eine Datei, die dazwischen wieder aufgetaucht ist, wird nicht angetastet
  (**Am erwarteten Pfad liegt jetzt eine Datei**), und jede Zeile wird unmittelbar vor ihrer Ausführung erneut gegen den
  Speicher geprüft.
- Die gewählten Zeilen werden jeweils zu `FixBatchSize` (10) erledigt; zwischen den Stapeln zeigt die Seite den
  Fortschritt und macht von selbst weiter (außer wenn **Automatisch mit dem nächsten Stapel fortfahren** nicht
  angekreuzt ist oder Sie eine Taste drücken oder klicken) oder mit **Mit dem nächsten Stapel fortfahren**; **Hier
  anhalten** führt zurück zur Liste. Höchstens `MaxFixItems` (200) Zeilen auf einmal.
- Jede Zeile erfordert `content/edit` auf ihrem Objekt (zum Markieren für die Prüfung: `content/read`), zusätzlich zu
  den Richtlinien unten.
- Jede erledigte oder nicht erledigte Zeile wird im Audit festgehalten (Kanal `content`), mit dem Objekt, dem
  erwarteten Pfad, dem Feld, der Version und der Sprache sowie dem Ergebnis:

| Ereignis | Aktion |
|---|---|
| `content.file.alias.regenerate` | Neu erstellen (auch nach einer Wiederherstellung oder einem Ersetzen eines Bildes) |
| `content.file.restore` | Aus einer anderen Version wiederherstellen (mit der Quellversion und dem Quellpfad) |
| `content.file.replace` | Neu hochladen oder ersetzen |
| `content.file.reference.remove` | Toten Verweis entfernen |
| `content.file.review.mark` | Zur Prüfung markieren |

Wie Sie einer fehlenden Datei Schritt für Schritt nachgehen:
[Fehlende Dateien und Bildvarianten](../../anleitungen/fehlende-dateien.md).
## Wer sie verwenden darf

| Richtlinie | Öffnet |
|---|---|
| `contentcheck/unreferenced` | die Liste der nicht verknüpften Objekte |
| `contentcheck/trash` | mit `unreferenced`: deren Verschieben in den Papierkorb; jedes Objekt braucht zusätzlich `content/remove` |
| `contentcheck/brokenrelations` | die Liste der ungültigen Relationen und mit dem Ziel „Fehlende Dateien“ die fehlenden Dateien |
| `contentcheck/fixfiles` | mit `brokenrelations`: das Beheben fehlender Dateien; jedes Objekt braucht zusätzlich `content/edit` (zum Markieren für die Prüfung: `content/read`) |
| `contentcheck/storage` | die Speicherdateien: die Prüfung, die verwaisten Dateien, die Dateiprobleme und die Quarantäneliste |
| `contentcheck/quarantine` | mit `storage`: verwaiste Dateien in die Quarantäne verschieben, sie wiederherstellen, sie dort löschen |
| `contentcheck/permissions` | mit `storage`: den erwarteten Eigentümer und die erwarteten Rechte verwendeter Dateien setzen |
| `contentcheck/brokenrelations` | die Liste der ungültigen Relationen und mit dem Ziel „Fehlende Dateien“ die fehlenden Dateien |

Die Richtlinien haben keine Einschränkungen; was jede Liste zeigt, ist durch `content/read` begrenzt (wie bei einem
Teilbaum-Fetch, in dessen SQL), und was verschoben werden darf, durch `content/remove`.

### Was Sie nicht lesen dürfen

Was Sie nicht lesen dürfen, wird vollständig weggelassen, mit der Berechtigungsbedingung im SQL jeder Abfrage, nicht
nur bei der Darstellung: aus den Listen, den Zählungen, den Seiten und **Nächste Seite**, der Anzahl geprüfter
Objekte, den Orten und Unterelementen, die eine Zeile zeigt, und den ausgelassenen Teilbäumen (eine Knoten-ID, die Sie
nicht lesen dürfen, wird wie eine unbekannte ignoriert). Nichts davon wird gezählt, genannt oder protokolliert. Das
Hinzufügen oder Entfernen von Inhalten, die Sie nicht lesen dürfen, ändert nichts an dem, was Sie sehen.

- **Ungültige Relationen**: Eine Quelle, die Sie nicht lesen dürfen, wird nicht geprüft. Ein Ziel, das Sie nicht lesen
  dürfen, wird als **Ziel nicht verfügbar** mit dem Zustand **Für Sie nicht verfügbar** gezeigt, ohne seine ID und ohne
  zu verraten, ob es gelöscht, im Papierkorb, versteckt oder einwandfrei ist: Alle sehen gleich aus, sodass die Liste
  nichts über Inhalte aussagt, die Sie nicht sehen dürfen. Mit uneingeschränktem `content/read` zeigt jedes Ziel seinen
  tatsächlichen Zustand und seine ID.
- **Nicht verknüpfte Objekte**: Ob ein Objekt, das Sie lesen dürfen, nicht verknüpft ist, wird anhand **jedes**
  Verweises entschieden, auch aus Objekten, die Sie nicht lesen dürfen, und ein Objekt mit Unterelementen, die Sie nicht
  lesen dürfen, wird nicht für den Papierkorb angeboten; andernfalls kämen verwendete Inhalte in den Papierkorb. Dass ein
  Objekt, das Sie lesen dürfen, in der Liste fehlt, ist daher ein schwacher Hinweis darauf, dass etwas darauf verweist
  (oder darunter liegt). Das lässt sich nicht vermeiden, ohne verwendete Inhalte für den Papierkorb anzubieten; über
  dieses Etwas (Anzahl, Name, ID, Klasse) wird nirgends etwas gezeigt, und es gibt keine Ansicht, die sagt, warum ein
  Objekt nicht aufgeführt ist. Ein Startknoten, den Sie nicht lesen dürfen, antwortet als nicht gefunden. Eine neue
Installation gibt sie nur Administrator (die jede Richtlinie besitzt). Geben Sie sie einer Rolle für die Personen, die
die Inhalte aufräumen, nicht jedem Redakteur: Die Listen zeigen die Inhalte des ganzen Teilbaums auf einmal.

Die Einträge im Kontextmenü erscheinen nur für Benutzer mit der passenden Richtlinie. Die Setup-Links verwenden
dieselben Richtlinien (`PolicyList_unreferenced_objects[]`, `PolicyList_broken_relations[]` in `menu.ini`).

### Anfragen, die abgewiesen werden

`contentcheck/trash` nimmt nur POST an (sonst 405), mit dem Formular-Token der Prüfungen (ein Sitzungsgeheimnis) und,
wenn die Extension aktiv ist, dem Token von `ezformtoken` (sonst 403). Das Verschieben selbst braucht das
Bestätigungs-Token des angezeigten Plans. IDs werden nur als ganze Zahlen gelesen; alles andere in einer Anfrage wird
weggelassen. Die Prüfungen antworten außerhalb der Administrations-Siteaccesses und bei `Enabled=false` mit 404.

## Einstellungen

`settings/contentcheck.ini`; überschreiben Sie sie in `settings/override/contentcheck.ini.append.php`.

```ini
[ContentCheckSettings]
# true | false: with false the checks answer 404 everywhere
Enabled=true
# The siteaccesses they answer on; empty: every siteaccess with an administration design
SiteAccessList[]

[UnreferencedSettings]
# The classes the list covers (assets); pages, folders and users are never listed
AssetClassList[]=image
AssetClassList[]=file
AssetClassList[]=video
...
# An asset is listed only when every location's parent is of one of these classes
StorageParentClassList[]=folder
# At most this many objects moved to the trash in one confirmation (1 to 200)
MaxTrashBatch=50
# How long the reference index is kept, in seconds
IndexTTL=600
# false on very large installations: only the CLI script or the cronjob part builds the index
BuildOnRequest=true
# Fields read per query while building the index; candidates judged for one list at most
ScanChunkSize=2000
CandidateLimit=20000
# Table columns whose ids keep an object or node from being unreferenced (a missing table is skipped)
ObjectColumns[]=ezproductcollection_item.contentobject_id
ObjectColumns[]=cjwnl_edition_article.contentobject_id
...
NodeColumns[]=ezsubtree_notification_rule.node_id
NodeColumns[]=ezcontentbrowsebookmark.node_id
NodeColumns[]=eznode_assignment.parent_node
NodeListColumns[]

[ReferenceSettings]
# Ids of imported content, resolved as the site resolves them (%id, %id+N); the media site demo:
#ImportedIdRemotePatterns[]=media-o-%id+776
#ImportedIdRemotePatterns[]=media-o-%id
#ImportedIdObjectPatterns[]=%id+776

[BrokenRelationsSettings]
# The datatypes whose text is read for links and embeds
XmlDatatypes[]
XmlDatatypes[]=ezxmltext
# Objects checked with one set of queries (10 to 1000)
ChunkSize=200
# At most this many objects checked for one page (ChunkSize to 100000)
ScanLimit=5000

[MissingFileSettings]
# The class that says which files a field of a datatype stands for (expContentCheckFileHandler)
Handlers[ezimage]=expContentCheckFileImage
Handlers[ezbinaryfile]=expContentCheckFileTable
Handlers[ezmedia]=expContentCheckFileTable
# The table of each datatype handled by expContentCheckFileTable
Tables[ezbinaryfile]=ezbinaryfile
Tables[ezmedia]=ezmedia
# The aliases looked for by "Alias never made"; empty: every alias of image.ini
AliasList[]
# Objects checked with one set of queries (10 to 1000); objects walked for the list at most,
# and how long its findings are kept for its pages (seconds; 0: walked again for every page)
ChunkSize=200
ListLimit=50000
ListCacheTTL=60
# Rows in one resolution at most (1 to 1000); rows done per request (1 to 200)
MaxFixItems=200
FixBatchSize=10
# The largest replacement in bytes; 0: what PHP takes
MaxReplaceSize=0
# enabled: the cronjob part contentcheck also counts the missing files and alias problems
CronjobReport=disabled
```

```ini
[ScopeSettings]
# The presets: tree, content, media, users, setup, design, a node id or a URL alias path
PresetList[]=tree
PresetList[]=content
PresetList[]=media
PresetList[]=media/images
PresetList[]=users
# The views that take a start location
ViewList[]=unreferenced
ViewList[]=brokenrelations
ViewList[]=storage

[StorageSettings]
ScanDirectoryList[]=images
ScanDirectoryList[]=images-versioned
ScanDirectoryList[]=original
IgnorePathList[]=images/_video-thumbnails
ScanBatchSize=2000
StepSeconds=4
MaxListed=5000
# seconds; a younger file is never an orphan
MinAge=86400
# empty: the owner of the storage directory / site.ini StorageFilePermissions
ExpectedOwner=
FileMode=
QuarantineDirectory=storage-quarantine
MaxActionBatch=200
```

Seitengrößen: `admininterface.ini [PaginationSettings] ItemsPerPageList_contentcheck_unreferenced[]` und
`ItemsPerPageList_contentcheck_brokenrelations[]` (zugleich die Seitengröße der fehlenden Dateien).

## Auf der Kommandozeile

Nur Bericht; nichts wird geändert oder verschoben:

```
./console exp:content-check --build-index            # build the reference index (as the cronjob part does)
./console exp:content-check --unreferenced --node=2  # every unused asset below node 2
./console exp:content-check --broken --node=2        # every broken relation below node 2, and the layouts'
./console exp:content-check --why=123,456            # what uses objects 123 and 456 (the first reference found)
./console exp:content-check --unreferenced --json    # the same as JSON
./console exp:content-check --storage                # scan the storage files: orphans and file problems (totals)
./console exp:content-check --storage --quarantine --dry-run        # which orphans would be quarantined
./console exp:content-check --storage --quarantine                  # move them (each checked again)
./console exp:content-check --storage --fix-permissions --dry-run   # which owners and modes would be set
```

`--storage` führt die ganze Prüfung aus und hält sie für die Seite bereit; `--quarantine` und `--fix-permissions`
ändern Dateien nur ohne `--dry-run` und halten jeden Stapel wie die Seite im Audit fest.

Fehlende Dateien und ihre Behebung; nur `--fix` ändert etwas, und `--dry-run` zeigt stattdessen den Plan:

```
./console exp:content-check --missing-files --node=2                 # every missing file and alias problem below node 2
./console exp:content-check --missing-files --problems=missing --versions=published
./console exp:content-check --missing-files --fix=regenerate --dry-run  # what regenerating every finding would do
./console exp:content-check --fix=restore --items=123-4,125-2       # restore two field versions (<attribute id>-<version>)
./console exp:content-check --fix=replace --items=123-4 --replace-with=photo.jpg
./console exp:content-check --fix=review --items=123-4             # mark for review
```

`--fix` nimmt `regenerate`, `restore`, `replace`, `remove` oder `review`; ohne `--items` nimmt es jeden Befund von
`--missing-files` unterhalb von `--node`. Es plant, prüft und protokolliert genau wie die Administrationsoberfläche
(dieselben `content/edit`-Prüfungen für den Benutzer aus `--login`, dieselben Stapel, dieselben Audit-Ereignisse).
Exit-Code 2, wenn eine Zeile nicht erledigt werden konnte.

Die Listen sind die des mit `--login` angegebenen Benutzers (standardmäßig `admin`). Exit-Code 0, wenn nichts gefunden
wird, 1, wenn etwas gefunden wird, 2 bei einem Fehler. Setzen Sie auf einer sehr großen Installation
`BuildOnRequest=false` und führen Sie den Cronjob-Teil `contentcheck` (`php runcronjobs.php contentcheck`) häufiger
aus, als `IndexTTL` vorgibt.

## Leistung

**Der Verweisindex** liest einmalig: jedes Feld der zählenden Versionen, dessen Text wie ein Verweis aussieht, in
Teilen nach Attribut-ID (`ScanChunkSize`), die Tabelle `ezurl`, die Layout-Tabellen, die Einstellungsdateien und die
Templates. Er wird `IndexTTL` Sekunden im Cache gehalten. Auf einer Demo-Installation mit SQLite (1.100 Objekte,
12.000 Felder, 650 Layout-Blöcke, 4.700 Templates, 800 Einstellungsdateien) wird er in 2,5 bis 4,5 s aufgebaut; die
Liste braucht danach einige hundert Millisekunden. Wo der Aufbau während einer Anfrage zu langsam ist, setzen Sie
`BuildOnRequest=false` und bauen ihn mit dem Cronjob-Teil `contentcheck` auf.

**Die Liste der nicht verknüpften Objekte**: Die Kandidaten (Assets unterhalb des Knotens ohne Relationszeile,
höchstens `CandidateLimit`) kommen aus einer einzigen Abfrage in der gewählten Reihenfolge, mit der Verweisbedingung
als korreliertem `NOT EXISTS` auf den Indizes `ezco_link_to_co_id` und `(contentobject_id, version)` der Versionen und
der Berechtigungsbedingung von `content/read` im selben SQL. Sie werden in PHP anhand des Index beurteilt, mit einer
Abfrage pro 500 Objekte für ihre Orte und übergeordneten Elemente; gezählt wird, was übrig bleibt. Danach folgt eine
feste Anzahl von Abfragen für die Seite (Orte, Unterelemente, Dateien, entfernbar). Die Größen sind das einzige
Nachschlagen pro Zeile (ein Datei-Stat oder eine Abfrage der Cluster-Tabelle), und zwar nur für die Zeilen der Seite.

**Die ungültigen Relationen** lesen einen Teil der Quellen in der Reihenfolge des Primärschlüssels (`id > last`),
dann ihre Relationen, ihre XML-Felder, ihre Beziehungsfelder und den Zustand jedes Ziels in sieben Abfragen pro Teil
(plus eine pro erweitertem Link, um ein Objekt von einem Knoten zu unterscheiden). Gemessen auf MariaDB mit einer Kopie
einer echten Installation plus 200.000 erzeugten Artikeln unter einem Ordner (206.000 Objekte, 118.000 Relationen,
750.000 Attribute), bevor die Beziehungsfelder hinzukamen:

| Anfrage | Zeit |
|---|---|
| Ungültige Relationen, eine Seite mit 50 Befunden (etwa 1.600 geprüfte Quellen) | 20 bis 30 ms |
| Ungültige Relationen, eine Seite ohne Befund: 5.000 geprüfte Quellen | 50 bis 350 ms |

Der Abschnitt Layouts liest die Layout-Tabellen einmal, mit einer Abfrage pro beurteiltem Ziel.

## Datenbanken

Die Abfragen sind einfaches SQL mit `NOT EXISTS`, `LIKE` auf Pfad-Strings und `LIMIT` über die Datenbankschicht: MySQL
und MariaDB, PostgreSQL, SQLite und Oracle. Auf MongoDB melden die Prüfungen, dass sie nicht verfügbar sind.

## Für Entwickler

- Modul `kernel/contentcheck/` (Ansichten `unreferenced`, `trash`, `brokenrelations`); der Code der Ansichten in
  `kernel/private/classes/views/contentcheck/`; die Klassen in `kernel/classes/contentcheck/`:
  `expContentCheck` (Einstellungen, Zugriff, Tokens, die Verweisbedingung `referenceExistsSQL()`),
  `expContentCheckUnreferenced` (Asset-Klassen, Parameter, SQL, Beurteilung, die Seite, `reasons()`),
  `expContentCheckReferences` (der Verweisindex: ein Extraktor pro Quelle, `build()`, der Cache, `reason()`),
  `expContentCheckTrash` (Plan und Verschieben), `expContentCheckBrokenRelations` (Parameter, Quellabfrage,
  `xmlReferences()`, `attributeTargets()`, Zielzustände, `hint()`, der Durchlauf, `layoutFindings()`).
- Fehlende Dateien: das Ziel von `brokenrelations` und die Ansicht `fixfiles` (nur POST); `expContentCheckFiles`
  (Parameter, die Abfragen, `judge()` mit den übergebenen Speicher-Nachschlagefunktionen, die Seite, `target()`,
  `section()`, `inspect()`, Prüfmarkierungen), `expContentCheckFileFix` (Plan, Ausführung in Stapeln, die fünf
  Aktionen, Audit), die Handler-Schnittstelle `expContentCheckFileHandler` mit `expContentCheckFileImage` und
  `expContentCheckFileTable`. Templates `missingfiles.tpl` (der Abschnitt), `missingfiles_filter.tpl`, die gemeinsame
  Zeile `file_row.tpl`, `fixconfirm.tpl`, `fixprogress.tpl` (mit `design/standard/javascript/contentcheck_fixfiles.js`
  für den nächsten Stapel), `fixrefused.tpl`, `fixreason.tpl`. Tests: `expContentCheckFilesTest.php` und
  `expContentCheckFileFixTest.php` (ohne Datenbank).
- Befehl `exp:content-check` (`bin/php/contentcheck.php`, `kernel/private/classes/commands/contentcheck.php`) und der
  Cronjob-Teil `contentcheck` (`cronjobs/contentcheck.php`).
- Um eine Verweisquelle aus der Tabelle einer Extension hinzuzufügen, führen Sie ihre Spalte in `ObjectColumns[]`,
  `NodeColumns[]` oder `NodeListColumns[]` auf; für alles andere erweitern Sie `expContentCheckReferences::build()` um
  eine Methode `from...()` und einen Extraktor mit einem Test in `expContentCheckReferencesTest`.
- Der Startort: `expContentCheckScope` (Presets, Präferenz, `forTemplate()`), die Ansicht `contentcheck/scope` (die
  Knotenauswahl, `browse.ini [ContentCheckScope]`) und das Template `contentcheck/scope.tpl`.
- Die Speicherdateien: `expContentCheckStorage` (Einstellungen, `usedAmong()`, die stapelweise Prüfung `scanStep()`,
  das Ergebnis), `expContentCheckQuarantine` (die Pläne und Aktionen, `listing()`), die Ansichten
  `contentcheck/storage` und `contentcheck/storageaction`, die Templates `contentcheck/storage*.tpl` und das Skript
  `contentcheck_storage.js`. Test: `expContentCheckStorageTest.php` (ohne Datenbank).
- Templates `contentcheck/*.tpl` in `design/admin` und `design/admin4`; Einträge im Kontextmenü in
  `popupmenu/popup_sub_advance_menu.tpl` beider Designs.
- Tests: `tests/tests/kernel/contentcheck/expContentCheckQueryTest.php` braucht keine Datenbank (Eingabe, SQL,
  Richtext, Zustände); `expContentCheckReferencesTest.php` ebenso wenig (der Extraktor jeder Verweisquelle, die
  Beurteilung, die Asset-Klassen, Beziehungsfelder und Hinweise). `expContentCheckSandboxTest.php` schreibt Inhalte, Benutzer und Rollen und läuft nur in einer
  Wegwerfinstallation: `EXP_CONTENTCHECK_SANDBOX=$PWD EXP_CONTENTCHECK_SANDBOX_DATABASE=<database>
  EXP_CONTENTCHECK_SANDBOX_SITEACCESS=<admin siteaccess> php vendor/bin/phpunit tests/tests/kernel/contentcheck/`.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
