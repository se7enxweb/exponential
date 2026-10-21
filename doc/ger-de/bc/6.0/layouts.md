# Exponential Layouts

Lesen Sie diese Seite, wenn Sie in Exponential 6.0 Seitenlayouts bauen oder bearbeiten oder Blocktypen, View-Typen oder
Abfragetypen dafür entwickeln. Exponential Layouts bringt das Konzept von Netgen Layouts auf den Legacy-Stack von
Exponential: Ein Layout ist eine gespeicherte Anordnung von Blöcken in Zonen, und eine Regel entscheidet, welche Seiten
es erhalten, sodass Redakteure Seiten umgestalten, ohne Templates anzufassen. Die erste Hälfte dieser Seite erklärt die
Ideen und den Editor; die zweite Hälfte ist Referenzmaterial und Erweiterungsarbeit. Kennen Sie das System bereits,
sind die [Referenztabellen](#referenztabellen) am Ende der Teil, zu dem Sie immer wieder zurückkehren werden.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Die Erweiterungen `explayouts` fügen Layouts, Zonen, Blöcke, Collections und Regeln hinzu, den SPA-Editor unter `/explayouts_ui_api/app` (mit einer JSON-API darunter) und den Formular-Editor unter `/explayouts_ui/dashboard`, sowie den Fetch `fetch( 'explayouts', 'resolve_layout', hash() )` für `pagelayout.tpl`. |
| Wer betroffen ist | Websites, die eine von Redakteuren gesteuerte Anordnung der Seiten möchten. Eine Website, die die Erweiterungen nicht aktiviert, bleibt unverändert. |
| Wie Sie es prüfen | `curl -s -o /dev/null -w '%{http_code}\n' https://<your-admin-host>/explayouts_ui_api/app` antwortet mit `200` (siehe [Prüfen, ob es läuft](#prüfen-ob-es-läuft)). |
| Wie Sie es beheben | Folgen Sie der [Checkliste für die Installation](#checkliste-für-die-installation): Tabellen, Erweiterungen, Autoloads, Caches, dann ein Layout und eine Regel. |
| Klassische Menüs | Ein Siteaccess, der über Layouts rendert, liest `menu.ini [SelectedMenu]` nicht: Seine Menüs sind Blöcke. Design > Menüs (klassisch) kennzeichnet jeden Siteaccess entsprechend; [Klassische Menüs](../../anleitungen/klassische-menueeinstellungen.md) erklärt, wann diese Seite noch eine Rolle spielt. |

## Inhalt

1. [Warum es das Subsystem gibt](#warum-es-das-subsystem-gibt)
2. [Die fünf Ideen, die Sie brauchen](#die-fünf-ideen-die-sie-brauchen)
3. [In den Editor gelangen](#in-den-editor-gelangen)
4. [Layouts](#layouts)
5. [Zonen](#zonen)
6. [Blöcke](#blöcke)
7. [Container und Verschachtelung](#container-und-verschachtelung)
8. [Blockparameter](#blockparameter)
9. [Collections: einem Block seine Inhalte geben](#collections-einem-block-seine-inhalte-geben)
10. [Abfragetypen und ihre Parameter](#abfragetypen-und-ihre-parameter)
11. [Anheften: ausgewählte und dynamische Inhalte mischen](#anheften-ausgewählte-und-dynamische-inhalte-mischen)
12. [Regeln: entscheiden, welches Layout eine Seite erhält](#regeln-entscheiden-welches-layout-eine-seite-erhält)
13. [Entwürfe, Veröffentlichen und Versionen](#entwürfe-veröffentlichen-und-versionen)
14. [Gemeinsame Layouts und verknüpfte Zonen](#gemeinsame-layouts-und-verknüpfte-zonen)
15. [Import, Export und Freigabelinks](#import-export-und-freigabelinks)
16. [Durchgang A: das Standarddesign anpassen](#durchgang-a-das-standarddesign-anpassen)
17. [Durchgang B: ein neues Layout von Grund auf bauen](#durchgang-b-ein-neues-layout-von-grund-auf-bauen)
18. [Wie das Rendern tatsächlich funktioniert](#wie-das-rendern-tatsächlich-funktioniert)
19. [Das System erweitern](#das-system-erweitern)
20. [Architektur und Datenmodell](#architektur-und-datenmodell)
21. [Fehlerbehebung](#fehlerbehebung)
22. [Referenztabellen](#referenztabellen)


## Warum es das Subsystem gibt

In einer klassischen Exponential-Website ohne Layouts entscheiden Templates darüber, wie eine Seite aussieht. Die
Reihenfolge der Dinge auf der Startseite zu ändern, einen Werbestreifen über der Artikelliste einzufügen oder einem
Bereich der Website eine andere Seitenleiste zu geben, bedeutet jedes Mal, eine `.tpl`-Datei zu bearbeiten, und das
bedeutet einen Entwickler, ein Deployment und ein Release.

Exponential Layouts verlagert diese Entscheidungen in die Datenbank und stellt sie hinter einen Editor. Ein Layout ist
eine gespeicherte Anordnung von Inhalten; eine Regel sagt, welche Seiten es erhalten. Redakteure gestalten Seiten um,
ohne Templates anzufassen, und Entwickler behalten die Kontrolle darüber, wie jedes einzelne Teil gerendert wird.

Auf die Arbeitsteilung kommt es an, und es lohnt sich, sie klar auszusprechen, weil sie alles Folgende bestimmt:

- **Entwicklern gehören die Block-Templates.** Wie eine „Card“ aussieht, ist eine `.tpl`-Datei in einem Design.
- **Redakteuren gehört die Anordnung.** Welche Blöcke es gibt, in welcher Reihenfolge, in welchen Zonen, mit welchen
  Inhalten, sind Daten.

Wenn Sie ein Template bearbeiten möchten, um etwas zu verschieben, möchten Sie wahrscheinlich eine Änderung am Layout.
Wenn ein Layout etwas in einer Form rendern soll, die es noch nicht gibt, brauchen Sie einen neuen View-Typ oder
Blocktyp, und das ist Entwicklerarbeit — siehe [Das System erweitern](#das-system-erweitern).


## Die fünf Ideen, die Sie brauchen

Alles im Editor ist aus fünf Konzepten gebaut. Lernen Sie diese, und die Oberfläche erklärt sich von selbst.

**Layout** — eine benannte Anordnung, die auf einem *Layouttyp* beruht, der festlegt, wie viele Bereiche sie hat.
`2 columns` hat einen linken und einen rechten; `Header / Main / Footer` hat fünf. Der Layouttyp wird beim Anlegen
gewählt und kann danach nicht geändert werden, weil die Zonen das sind, woran Blöcke hängen.

**Zone** — ein benannter Bereich innerhalb eines Layouts: `main`, `sidebar`, `footer`. Zonen kommen aus dem Layouttyp,
daher legen Sie sie nie an oder löschen sie. Sie füllen sie, oder Sie verknüpfen sie mit einem anderen Layout (siehe
[gemeinsame Layouts](#gemeinsame-layouts-und-verknüpfte-zonen)).

**Block** — ein Element innerhalb einer Zone: ein Stück Text, ein Bild, eine Liste von Artikeln, ein Hero-Banner. Die
*Definition* des Blocks sagt, was für eine Art Ding er ist; sein *View-Typ* sagt, in welcher von mehreren Formen er
gerendert wird; seine *Parameter* konfigurieren ihn.

**Collection** — die Inhalte, die ein Block anzeigt, wenn er zu der Art gehört, die Inhalte anzeigt. Eine Collection
ist entweder **manuell** (Sie wählen die Elemente aus) oder **dynamisch** (eine Abfrage holt sie). Nur manche Blocktypen
haben eine; der [Blockkatalog](#blockkatalog) kennzeichnet, welche.

**Regel** — die Zuordnung von einer Anfrage zu einem Layout. Eine Regel zeigt auf ein Layout und trägt *Ziele* (für
welche Anfragen sie gilt) und *Bedingungen* (zusätzliche Einschränkungen). Regeln haben eine Priorität; der erste
Treffer gewinnt.

Ein Satz, an dem Sie sich festhalten können:

> Eine **Regel** entscheidet, dass diese Anfrage jenes **Layout** erhält; die **Zonen** des Layouts enthalten
> **Blöcke**; ein Block kann eine **Collection** besitzen, die seine Inhalte liefert.


## In den Editor gelangen

Es gibt zwei Administrationsoberflächen. Sie arbeiten mit denselben Daten, daher können Sie frei zwischen ihnen
wechseln.

**Der SPA-Editor** — der moderne, und Gegenstand des größten Teils dieses Dokuments:

```
/explayouts_ui_api/app
```

Seine JSON-API liegt darunter unter `/explayouts_ui_api/app/api/...`. Normalerweise rufen Sie die API nicht von Hand
auf, aber zu wissen, dass es sie gibt, macht das Verhalten des Editors viel leichter nachvollziehbar, und so skripten
Sie Massenänderungen.

**Der ältere, formularbasierte Editor** — schlichter, für ein einzelnes Feld manchmal schneller, und der Ort einiger
Verwaltungsseiten, die die SPA nicht hat:

```
/explayouts_ui/dashboard
```

Er bietet: `layout_list`, `layout_create`, `layout_edit`, `block_edit`, `rule_list`, `rule_edit`, `preview`,
`components`, `shared_layouts_list`, `template_editor`, `transfer_import`, `setup`.

### Berechtigungen

Beide Oberflächen prüfen die Richtlinien des Moduls `explayouts` über den üblichen Rolleneditor. Ein Benutzer braucht
mindestens `explayouts / read`, um das Dashboard zu öffnen, und die Bearbeitungsfunktionen, um etwas zu ändern. Die
Schlüssel für Modul und Richtlinien sind in `extension/explayouts/settings/module.ini.append.php` deklariert.

Lädt der Editor, aber jede Liste ist leer, prüfen Sie die Richtlinien, bevor Sie die Daten verdächtigen.

### Prüfen, ob es läuft

```bash
curl -s -o /dev/null -w '%{http_code}\n' https://<your-admin-host>/explayouts_ui_api/app
curl -s https://<your-admin-host>/explayouts_ui_api/app/api/config
```

Der Endpunkt config gibt das CSRF-Token zurück, das die SPA verwendet, die Editionsbezeichnung und ob das automatische
Leeren des Caches eingeschaltet ist. Ein `200` von beiden bedeutet, dass Modul, Erweiterung und Datenbank in Ordnung
sind.


## Layouts

### Eines anlegen

In der SPA *Layouts → Create*. Sie geben drei Dinge an:

| Feld | Wofür es da ist |
|---|---|
| **Layouttyp** | Legt die Zonen fest. Dauerhaft — wählen Sie mit Bedacht. |
| **Kennung** | Der Maschinenname. Wird von Templates und `fetch()`-Aufrufen verwendet. Kleinbuchstaben, keine Leerzeichen. |
| **Name** | Die lesbare Beschriftung in Listen. |

Die Kennung ist wichtiger, als sie aussieht. Mit ihr fragt ein Template nach einem bestimmten Layout, und mit ihr
übersteht ein Layout, dass es exportiert und anderswo wieder importiert wird. Behandeln Sie sie als dauerhaft, auch
wenn sich das Feld bearbeiten lässt.

### Die Layouttypen

Zwölf werden standardmäßig mitgeliefert. Die Zonennamen in der dritten Spalte sind genau die Namen, die Sie im Editor
und in Templates sehen.

| Kennung | Name | Zonen |
|---|---|---|
| `1_column` | 1 column | `main` |
| `2_column` | 2 columns | `left`, `right` |
| `3_column` | 3 columns | `left`, `main`, `right` |
| `4_column` | 4 columns | `col1`, `col2`, `col3`, `col4` |
| `hero` | Hero + 3 columns | `top`, `left`, `main`, `right` |
| `sidebar_left` | Sidebar left | `sidebar`, `main` |
| `sidebar_right` | Sidebar right | `main`, `sidebar` |
| `featured` | Featured | `hero`, `feature1`, `feature2`, `feature3`, `bottom` |
| `mosaic` | Mosaic | `a`, `b`, `c`, `d`, `e` |
| `layout_1` | Single zone | `main` |
| `layout_2` | Header / Main / Footer | `header`, `post_header`, `main`, `pre_footer`, `footer` |
| `layout_4` | Header / Left / Right / Footer | `header`, `post_header`, `left`, `right`, `pre_footer`, `footer` |

`layout_1`, `layout_2` und `layout_4` sind die richtigen, wenn Sie einen ganzen Seitenrahmen bauen, weil sie Bereiche
für Kopf und Fuß enthalten. Die nummerierten Spaltentypen eignen sich besser für ein Layout, das einen Inhaltsbereich
füllt.

Die Wahl zwischen `1_column` und `layout_1` — beide haben eine einzige Zone `main` — hängt davon ab, welches Template
sie rendert; siehe [Wie das Rendern tatsächlich funktioniert](#wie-das-rendern-tatsächlich-funktioniert).

### Aktionen auf Ebene des Layouts

| Aktion | Was sie tut | Wozu Sie sie verwenden |
|---|---|---|
| **Publish** | Kopiert den Entwurf über die veröffentlichten Zeilen | Ihre Arbeit live schalten |
| **Create draft** | Öffnet eine bearbeitbare Kopie des veröffentlichten Layouts | Eine Runde von Änderungen beginnen |
| **Discard draft** | Verwirft den Entwurf, das Veröffentlichte bleibt | Eine Änderung sicher abbrechen |
| **Copy** | Dupliziert das ganze Layout mit allen Blöcken | Ein neues Layout auf ein vorhandenes stützen |
| **Delete** | Entfernt das Layout vollständig | Ein Layout außer Dienst stellen — prüfen Sie, dass keine Regel darauf zeigt |


## Zonen

Zonen sind durch den Layouttyp festgelegt, daher bietet der Editor keine Möglichkeit, welche hinzuzufügen oder zu
entfernen. Jede Zone ist im Editor ein Ablagebereich mit der Kennung der Zone als Überschrift.

Eine Zone tut eines von zwei Dingen:

1. **Sie enthält eigene Blöcke** — der Normalfall. Fügen Sie ihr Blöcke hinzu, ordnen Sie sie, fertig.
2. **Sie ist mit einer Zone eines gemeinsamen Layouts verknüpft** — die Zone zeigt statt ihrer eigenen die Blöcke eines
   anderen Layouts an. Siehe [Gemeinsame Layouts und verknüpfte Zonen](#gemeinsame-layouts-und-verknüpfte-zonen).

Die Reihenfolge der Blöcke in einer Zone ist eine Ganzzahl `position`. Im Editor ziehen Sie; der Editor nummeriert neu
und speichert.


## Blöcke

Ein Block wird hinzugefügt, indem Sie das Menü zum Hinzufügen einer Zone öffnen und einen Blocktyp wählen.
Sechsundvierzig werden standardmäßig mitgeliefert. Statt sie alphabetisch aufzuführen, stehen sie hier danach geordnet,
wofür Sie zu ihnen greifen würden.

### Blockkatalog

**Text und Markup**

| Kennung | Name | View-Typen | Hinweise |
|---|---|---|---|
| `text` | Text | `default` | Reiner Text |
| `title` | Title | `default` | Eine Überschrift |
| `rich_text` | Rich text | `default` | Mit CKEditor |
| `markdown` | Markdown | `default` | Aus Markdown-Quelltext gerendert |
| `html` | HTML snippet | `default` | Rohes Markup — vertrauen Sie dem Autor |
| `quote` | Quote | `default` | Herausgestelltes Zitat |

**Medien**

| Kennung | Name | View-Typen | Collection |
|---|---|---|---|
| `image` | Image | `default` | |
| `video` | External video | `default` | |
| `gallery` | Gallery | `default` | ja |
| `thumb_gallery` | Thumb gallery | `default` | ja |
| `grid_gallery` | Grid gallery | `default` | ja |
| `slider` | Slider | `default` | ja |
| `carousel` | Carousel | `default` | ja |
| `map` | Map | `default` | |

**Auflistung von Inhalten** — das sind die Blöcke, die Inhalte aus der Website holen

| Kennung | Name | View-Typen | Collection |
|---|---|---|---|
| `list` | List | `list`, `grid`, `list_numbered`, `list_zigzag`, `list_accordion`, `grid_featured` | ja |
| `grid` | Grid | `default` | ja |
| `list_zigzag` | Zig-Zag (List) | `list_zigzag` | ja |
| `list_accordion` | Accordion (List) | `list_accordion` | ja |
| `sushi_bar` | Sushi bar | `default` | ja |
| `single` | Single content | `default` | |
| `full_view` | Full view | `full_view` | |

`list` ist das Arbeitspferd. Mit seinen sechs View-Typen deckt ein Blocktyp die meisten Auflistungen ab; ändern Sie
lieber den View-Typ, als einen anderen Block zu wählen.

`full_view` ist besonders: Er rendert das aktuelle Inhaltselement in seiner normalen vollständigen Ansicht. So betten
Sie den eigentlichen Inhalt der Seite in ein Layout ein, und die meisten Layouts für einen Seitenrahmen brauchen genau
einen davon.

**Layout und Struktur**

| Kennung | Name | View-Typen | Container |
|---|---|---|---|
| `two_columns` | Two columns | `two_columns_66_33`, `two_columns_33_66` | ja |
| `three_columns` | Three columns | `three_columns` | ja |
| `four_columns` | Four columns | `four_columns` | ja |
| `column` | Column | `column` | ja |
| `spacer` | Spacer | `default` | |
| `divider` | Divider | `default` | |

**Bedienelemente**

| Kennung | Name | View-Typen |
|---|---|---|
| `button` | Button / Link | `default` |
| `card` | Card | `default` |
| `accordion` | Accordion | `default` |
| `tabs` | Tabs | `default` |
| `alert` | Alert | `default` |
| `badge` | Badge | `default` |
| `progress` | Progress bar | `default` |

**Seitenabschnitte** — vorgefertigte Marketing-Abschnitte

| Kennung | Name | View-Typen | Collection |
|---|---|---|---|
| `hero` | Hero | `default` | |
| `about` | About | `default` | |
| `features` | Features | `default` | |
| `lead` | Lead | `default` | |
| `logos` | Logos | `default` | ja |

**Gestaltete Komponenten** — dieselben Abschnitte mit mehreren fertigen visuellen Varianten

| Kennung | Name | View-Typen |
|---|---|---|
| `ibexa_component_hero` | Hero component | `hero_style_1` … `hero_style_3` |
| `ibexa_component_features` | Features component | `features_style_1` … `features_style_7` |
| `ibexa_component_about` | About component | `about_style_1` … `about_style_5` |
| `ibexa_component_logos` | Logos component | `logos_style_1`, `logos_style_2` |
| `ibexa_component_quote` | Quote component | `quote_style_1` |
| `ibexa_component_lead` | Lead component | `lead_style_1`, `lead_style_2` |

Die Familie `ibexa_component_*` gibt es, damit eine Marketing-Seite ohne Entwickler aus vorgestalteten Abschnitten
zusammengesetzt werden kann. Deshalb haben sie so viele View-Typen: Wählen Sie die Stilnummer, die zur Seite passt. Die
Seite **Components** der älteren Administration führt auf, wo jede davon in allen Layouts verwendet wird; so finden Sie
jede Seite, die einen Stil verwendet, bevor Sie ihn ändern.

**Templates**

| Kennung | Name | View-Typen |
|---|---|---|
| `tpl_block` | Template block | `tpl_block` |

`tpl_block` rendert ein benanntes Template, das über seinen Parameter `block_name` gewählt wird. Es ist der Notausgang:
Braucht eine Seite etwas, das kein Blocktyp abdeckt, schreibt ein Entwickler ein Template, und der Redakteur platziert
es überall. Bevorzugen Sie für alles, was mehr als ein- oder zweimal verwendet wird, einen richtigen Blocktyp.

### Aktionen auf Ebene des Blocks

Jeder Block bietet im Editor:

- **Edit** — sein Parameterfeld öffnen
- **Move** — innerhalb einer Zone oder in eine andere Zone ziehen
- **Duplicate** — ihn kopieren, eingefügt nach dem Original
- **Delete** — ihn entfernen

Duplizieren ist meist schneller, als einen zweiten konfigurierten Block von Grund auf zu bauen, und es kopiert
Parameter und Einstellungen der Collection mit.


## Container und Verschachtelung

Vier Blocktypen sind *Container*: Sie enthalten andere Blöcke, statt selbst Inhalte zu rendern. So erhalten Sie einen
zweispaltigen Streifen in einer einspaltigen Zone, ohne einen neuen Layouttyp zu erfinden.

| Container | Platzhalter |
|---|---|
| `two_columns` | `left`, `right` |
| `three_columns` | `col_1`, `col_2`, `col_3` |
| `four_columns` | `col_1`, `col_2`, `col_3`, `col_4` |
| `column` | `main` |

Ein *Platzhalter* ist für einen Container, was eine Zone für ein Layout ist: ein benannter Platz, der eine geordnete
Liste von Blöcken enthält. Im Editor wird ein Container als Block mit eigenen Ablagebereichen darin dargestellt.

`two_columns` hat zwei View-Typen, `two_columns_66_33` und `two_columns_33_66`, die die Aufteilung der Breite festlegen.
Die Platzhalter sind in beiden Fällen dieselben — ein Wechsel des View-Typs ordnet neu an, ohne Blöcke zu verschieben.

Container lassen sich verschachteln. In der Praxis sind sie auch der wichtigste Weg, ein Layout responsiv zu machen, da
die Spalten-Templates die responsiven Klassen tragen.

Eine Warnung: Tiefe Verschachtelung ist schwer zu bearbeiten und für die nächste Person schwer zu verstehen. Sind Sie
drei Container tief, ist ein eigens gebauter Layouttyp oder Blocktyp meist die bessere Antwort.


## Blockparameter

Die Auswahl eines Blocks öffnet sein Parameterfeld. Parameter werden vom Handler des Blocks deklariert, daher zeigt
jeder Blocktyp einen anderen Satz. Ein Block `button` deklariert zum Beispiel:

| Parameter | Typ | Standard |
|---|---|---|
| Button text | text | *(leer)* |
| Link URL | text | *(leer)* |
| Target | select — `_self`, `_blank`, `_parent`, `_top` | `_self` |
| CSS class | text | `btn btn-primary` |

In den Standard-Handlern gibt es sechs Parametertypen, und das sind die einzigen Widgets, die der Editor zeichnen kann:

| Typ | Widget |
|---|---|
| `text` | Einzeiliges Eingabefeld |
| `textarea` | Mehrzeiliges Eingabefeld |
| `select` | Eine Auswahl aus einer Liste |
| `multiselect` | Mehrere Auswahlen aus einer Liste |
| `checkbox` | An/aus |
| `browse` | Inhaltsauswahl — öffnet den Inhaltsbrowser |

Neben den eigenen Parametern des Handlers trägt jeder Block:

- **View-Typ** — in welcher Template-Form gerendert wird, aus der Liste dieses Blocktyps
- **Name** — eine Beschriftung für Redakteure, um zwei ähnliche Blöcke im Baum zu unterscheiden
- **CSS ID** und **CSS class** — werden auf das umschließende Element des Blocks geschrieben

Das Feld CSS class ist die Nahtstelle zwischen Redaktions- und Designarbeit, und es ist das nützlichste Feld für
Anpassungen überhaupt: Ein Entwickler liefert Utility-Klassen im Theme aus, ein Redakteur wendet sie pro Block an, und
niemand muss ein Template anfassen, um einen Hintergrund oder einen Abstand zu ändern.


## Collections: einem Block seine Inhalte geben

Blöcke, die im Katalog mit *Collection* gekennzeichnet sind, zeigen Inhalte aus der Website an, und eine Collection
sagt ihnen, welche Inhalte.

Eine Collection ist von einer von zwei Arten:

**Manuell** — Sie wählen jedes Element von Hand über den Inhaltsbrowser. Richtig, wenn die Inhalte bestimmt sind und
sich nicht ändern: drei benannte Produkte, eine kuratierte Auswahl von fünf Artikeln.

**Dynamisch** — eine *Abfrage* holt die Elemente zur Zeit des Renderns. Richtig, wenn die Inhalte sich selbst aktuell
halten sollen: die neuesten fünf Artikel in diesem Bereich, die Unterelemente dieses Knotens, alles mit diesem Thema
verschlagwortete.

Beide Arten unterstützen zwei weitere Einstellungen, die nach der Abfrage gelten:

| Einstellung | Bedeutung |
|---|---|
| **Offset** | So viele Elemente vom Anfang überspringen |
| **Limit** | Höchstens so viele anzeigen |

Mit dem Offset können zwei Blöcke verschiedene Ausschnitte derselben Abfrage zeigen. Ein Block „featured“ mit Limit 1
und darunter ein Block „more stories“ mit Offset 1 und Limit 6 ergeben zusammen das vertraute Muster aus Aufmacher und
Liste aus einer einzigen Sortierung.


## Abfragetypen und ihre Parameter

Zwölf Abfragetypen werden standardmäßig mitgeliefert.

| Kennung | Name | Was sie zurückgibt |
|---|---|---|
| `children` | Children of a node | Direkte Unterelemente eines gewählten Ablageorts |
| `parent` | Parent of a node | Den übergeordneten Ablageort |
| `subtree` | Subtree of a node | Alles unter einem Ablageort, in beliebiger Tiefe |
| `siblings` | Siblings of a node | Andere Unterelemente desselben Elternknotens |
| `latest` | Latest content | Zuletzt veröffentlichte Inhalte, die neuesten zuerst |
| `random` | Random content | Eine zufällige Auswahl |
| `manual` | Manual collection | Genau die Elemente, die Sie ausgewählt haben |
| `exp_content_relation_list` | Exp relation list | Inhalte, auf die dieses Element zeigt |
| `exp_content_reverse_relation_list` | Exp reverse relation list | Inhalte, die auf dieses Element zeigen |
| `exp_content_tags` | Exp tags | Inhalte mit gemeinsamen Tags |
| `exponential_content_search` | Exponential | Allgemeine Suche mit Filtern — die leistungsfähigste |
| `content_by_topic` | Topics | Inhalte unter einem Thema |

### Das Parameterfeld lesen

Die Parameter einer Abfrage sind gruppiert, und die Gruppierung ist Absicht. Vier Parameter gelten als *grundlegend*
und werden zuerst gezeigt, weil fast jede Abfrage sie braucht:

- `use_topic_from_current_content`
- `use_current_location`
- `sort_type`
- `sort_direction`

Alles andere ist *erweitert* und eingeklappt.

Manche Parameter sind **zusammengesetzt**: ein Kontrollkästchen, das die Felder aufdeckt, die es steuert. Das hält das
Feld kurz und, noch wichtiger, macht die Abhängigkeit sichtbar — die untergeordneten Felder bedeuten nichts, solange
das übergeordnete nicht gesetzt ist.

| Kontrollkästchen | Deckt auf | Sinn |
|---|---|---|
| `use_topic_from_current_content` | `topic_content_id` | Umgekehrt — das Feld erscheint, wenn es *nicht* angehakt ist |
| `use_current_location` | `parent_location_id` | Umgekehrt — das Feld erscheint, wenn es *nicht* angehakt ist |
| `filter_by_content_type` | `content_types`, `content_types_filter` | Normal |
| `filter_by_section` | `sections` | Normal |
| `filter_by_object_state` | `object_states` | Normal |

Bei den beiden umgekehrten lohnt es sich zu verweilen, denn sie sind die Funktion, die ein Layout wiederverwendbar
macht. „Use current location as parent“ bedeutet: *keinen Ablageort fest eintragen — die Seite verwenden, die gerade
gerendert wird*. Ein einziges Layout mit einer Abfrage `children` und angehaktem Kästchen bedient jeden Bereich der
Website: Unter `/recipes` führt es Rezepte auf, unter `/fitness` Fitness-Artikel. Entfernen Sie den Haken, erscheint ein
Feld für den Ablageort, das den Block an einen Ort heftet.

Diese Unterscheidung — an die aktuelle Seite gebunden oder an eine feste geheftet — ist der Unterschied zwischen einem
Layout, das Sie einmal schreiben, und einem, das Sie zwanzigmal kopieren.

### Die Inhaltssuche von Exponential

`exponential_content_search` ist die Abfrage für alle Zwecke, und dort liegen die Filter. Zu ihren Parametern gehören
der übergeordnete Ablageort, die Sortierung und die drei zusammengesetzten Filter von oben:

- **Inhaltstyp** — auf gewählte Klassen beschränken, mit einem Filtermodus
- **Sektion** — auf gewählte Sektionen beschränken
- **Objektzustand** — auf gewählte Zustände beschränken

Verwenden Sie sie, wenn die einfacheren Abfragen nicht passen. Verwenden Sie `children` oder `subtree`, wenn sie passen
— sie sind sechs Monate später leichter zu lesen.


## Anheften: ausgewählte und dynamische Inhalte mischen

Manuelle Elemente können einer *dynamischen* Collection hinzugefügt werden, und dann werden sie zu **angehefteten
Überschreibungen**: Jedes belegt seine gespeicherte Position, und die Abfrage füllt die Plätze darum herum.

Das löst das alltägliche redaktionelle Problem, dass eine Liste sich meist selbst pflegen soll, aber ein Element diese
Woche auf Position eins stehen muss. Ohne Anheften müssten Sie den ganzen Block auf manuell umstellen und daran denken,
ihn später zurückzustellen.

Die Mechanik:

- Angeheftete Positionen sind innerhalb der Collection absolut, nicht relativ zu den Ergebnissen der Abfrage.
- Ein auf Position 0 angeheftetes Element steht zuerst, was immer die Abfrage zurückgibt.
- Die Ergebnisse der Abfrage überspringen die angehefteten Positionen und füllen den Rest der Reihe nach.

Ein praktischer Rat: Heften Sie so wenig wie möglich an, und schreiben Sie in das Feld **Name** des Blocks eine Notiz,
warum, sonst versteht der nächste Redakteur nicht, warum sich Position 3 nie ändert.


## Regeln: entscheiden, welches Layout eine Seite erhält

Ein Layout tut nichts, bis eine Regel Anfragen darauf lenkt. Regeln stehen in der SPA unter *Mappings* und in der
älteren Oberfläche unter *rule_list*.

Eine Regel hat:

- **Ein Layout** — was gezeigt wird
- **Ziele** — für welche Anfragen sie gilt
- **Bedingungen** — zusätzliche Einschränkungen, die ebenfalls erfüllt sein müssen
- **Priorität** — die höhere gewinnt, wenn mehrere Regeln passen
- **Aktiviert** — ein Schalter, damit eine Regel geparkt werden kann, ohne sie zu löschen

Regeln werden in der Reihenfolge der Priorität ausgewertet, und **der erste Treffer gewinnt**. Das ist die häufigste
Ursache für Verwirrung überhaupt: Erscheint ein Layout nicht, gibt es sehr oft eine allgemeinere Regel mit höherer
Priorität, die die Anfrage zuerst abfängt.

### Ziele

| Typ | Passt, wenn |
|---|---|
| `path_prefix` | Der Anfragepfad mit dem Wert beginnt |
| `path_info_prefix` | Ebenso, auf ein führendes `/` normalisiert |
| `path` | Der Pfad genau dem Wert entspricht |
| `path_regex` | Der Pfad zum regulären Ausdruck passt |
| `node` | Die Anfrage auf diesen Inhaltsknoten aufgelöst wird |
| `subtree` | Der Knoten der Anfrage unter diesem Ablageort liegt |

Es gibt außerdem einen Zieltyp `route`, deklariert, aber **nicht implementiert** — er passt nie. Bauen Sie nicht darauf.

`subtree` ist meist das, was Sie für „diesen ganzen Bereich der Website“ möchten, weil er Verschiebungen von Inhalten
folgt. Ein `path_prefix` bricht in dem Moment, in dem ein Redakteur einen Ordner umbenennt.

### Bedingungen

| Typ | Schränkt ein nach | Form des Werts |
|---|---|---|
| `siteaccess` | Welcher Siteaccess ausliefert | JSON-Array mit Namen von Siteaccesses |
| `content_type` / `class` | Der Inhaltsklasse des Knotens | JSON-Array mit Klassenkennungen |
| `query_parameter` | Einem `?name=value` in der URL | JSON mit `parameter_name`, optional `parameter_values` |
| `route_parameter` | Einem Benutzerparameter im Pfad | JSON mit `parameter_name`, optional `parameter_values` |
| `time` | Der aktuellen Zeit | JSON mit `from` und/oder `to` |

Diese Verhaltensweisen sollten Sie kennen, weil man sie leicht falsch liest:

- Bei `query_parameter` und `route_parameter` bedeutet die Angabe **keiner** Werte: „Der Parameter muss nur vorhanden
  sein“. Die Angabe von Werten bedeutet, dass einer davon passen muss.
- Bei `time` passt ein leerer Bereich immer. Er ist für Kampagnen-Layouts gedacht, die sich selbst ein- und
  ausschalten.
- Ein unbekannter Bedingungstyp **passt**, statt fehlzuschlagen. Ein Tippfehler in einer Bedingung erweitert die Regel
  daher stillschweigend, statt sie auszuschalten.

### Durchgearbeitetes Beispiel

Um jedem Artikel unter `/recipes` ein besonderes Layout zu geben, aber nur im öffentlichen Siteaccess und nur während
einer Aktion:

| Teil | Einstellung |
|---|---|
| Layout | `recipe_promo` |
| Ziel | `subtree` → der Ablageort Recipes |
| Bedingung | `content_type` → `["ng_recipe"]` |
| Bedingung | `siteaccess` → `["site"]` |
| Bedingung | `time` → `{"from":"2026-10-01","to":"2026-10-31"}` |
| Priorität | höher als die allgemeine Rezeptregel |

Regeln lassen sich auch **kopieren**, was der schnellste Weg ist, eine Familie fast gleicher Zuordnungen zu bauen.


## Entwürfe, Veröffentlichen und Versionen

Jedes Layout, jede Zone und jeder Block trägt einen `status`:

| Status | Bedeutung |
|---|---|
| `1` | Entwurf — woran der Editor arbeitet |
| `2` | Veröffentlicht — was die Website rendert |

Der Editor arbeitet immer an einem Entwurf. Das Veröffentlichen kopiert die Zeilen des Entwurfs über die veröffentlichten
Zeilen; die Website liest immer nur veröffentlichte Zeilen, daher ist unfertige Arbeit für Besucher nie sichtbar.

Der Ablauf:

1. **Create draft** aus dem veröffentlichten Layout (der Editor tut das, wenn Sie mit dem Bearbeiten beginnen).
2. Nehmen Sie Änderungen vor. Sie werden fortlaufend im Entwurf gespeichert — es gibt keinen eigenen Speicherschritt.
3. **Publish**, oder **Discard draft**, um die Änderungen zu verwerfen und die veröffentlichte Version unberührt zu
   lassen.

Die Ansicht *Versions* führt die veröffentlichten und die Entwurfsversionen eines Layouts mit ihren Zeitstempeln auf;
so prüfen Sie, ob ein Entwurf aussteht, bevor Sie Arbeit übergeben.

Da Entwürfe pro Layout und nicht pro Benutzer bestehen, überschreiben sich zwei Personen gegenseitig, die gleichzeitig
dasselbe Layout bearbeiten. Stimmen Sie sich ab, oder kopieren Sie das Layout und führen Sie von Hand zusammen.


## Gemeinsame Layouts und verknüpfte Zonen

Ein Layout kann als **gemeinsam** gekennzeichnet werden. Ein gemeinsames Layout wird keiner Anfrage über eine Regel
zugeordnet; es existiert, um ausgeliehen zu werden. Jede Zone jedes anderen Layouts kann mit einer seiner Zonen
*verknüpft* werden und rendert dann statt ihrer eigenen die Blöcke dieses Layouts.

So erhält eine Website einen Fußbereich, der einmal definiert ist. Bauen Sie ein gemeinsames Layout mit den Blöcken des
Fußbereichs und verknüpfen Sie dann die Zone `footer` jedes Seitenlayouts damit. Ändern Sie das gemeinsame Layout,
veröffentlichen Sie, und jede Seite folgt.

Regeln, die Sie kennen sollten:

- Nur **veröffentlichte** gemeinsame Layouts können verknüpft werden. Eine Zone zeigt nie auf den unveröffentlichten
  Entwurf von jemandem.
- Eine verknüpfte Zone ignoriert ihre eigenen Blöcke, solange die Verknüpfung besteht. Das Entfernen der Verknüpfung
  stellt sie wieder her.
- Die Seite **Shared layouts** der älteren Administration führt gemeinsame Layouts mit der Zahl der Zonen auf, die mit
  jedem verknüpft sind — prüfen Sie sie, bevor Sie eines ändern oder löschen.

Ein gemeinsames Layout ohne Verknüpfungen ist trotzdem völlig gültig; es hat nur noch niemand übernommen.


## Import, Export und Freigabelinks

Der **Export** erzeugt ein JSON-Dokument, das ein Layout mit seinen Zonen, Blöcken, Parametern und Collections
beschreibt. Der **Import** nimmt dieses Dokument und legt daraus ein Layout an.

Das ist der unterstützte Weg, ein Layout zwischen Umgebungen zu bewegen — auf Staging bauen, exportieren, auf der
Produktion importieren — und der vernünftige Weg, vor einer großen Umstrukturierung eine Sicherung zu behalten. So
funktioniert auch die Portierung von Netgen Layouts, da die Form der Daten absichtlich nah gehalten wurde.

Kennungen reisen mit dem Export, daher braucht ein Import in eine Umgebung, die diese Kennung bereits hat, Sorgfalt.

**Freigabelinks** sind eine eigene Funktion. Ein Freigabelink ist ein Token, das eine Vorschau eines Layouts ohne
Anmeldung an der Administration gewährt, um Arbeit jemandem zu zeigen, der kein Konto hat. Tokens werden pro Layout
angelegt, aufgeführt und widerrufen.

> Bei MongoDB-Installationen braucht der Freigabe-Endpunkt die Tabelle `explayouts_share`, die er bei der ersten
> Verwendung anlegt. Das funktioniert ab `explayouts_ui_api` v1.2.3 auf MySQL, SQLite und MongoDB; frühere Versionen
> erzeugten DDL nur für MySQL und meldeten Erfolg für Tokens, die sie nicht gespeichert hatten.


## Durchgang A: das Standarddesign anpassen

Das Ziel: einen Werbestreifen oben auf jeder Seite eines Bereichs hinzufügen, ohne ein Template anzufassen.

**1. Finden Sie das Layout, das der Bereich bereits verwendet.** Öffnen Sie *Mappings* und suchen Sie die Regel, deren
Ziel den Bereich abdeckt. Notieren Sie das Layout, auf das sie zeigt.

**2. Entscheiden Sie, ob Sie bearbeiten oder abzweigen.** Bedient das Layout nur diesen Bereich, bearbeiten Sie es.
Bedient es mehrere, **kopieren** Sie es, geben Sie der Kopie eine klare Kennung wie `section_recipes` und legen Sie für
den Bereich eine neue Regel mit höherer Priorität an. Das Abzweigen vermeidet den klassischen Unfall, zwanzig Seiten zu
ändern, während man auf drei zielt.

**3. Öffnen Sie das Layout und legen Sie einen Entwurf an.**

**4. Fügen Sie den Streifen hinzu.** Fügen Sie in der obersten Zone einen Container `two_columns` mit dem View-Typ
`two_columns_66_33` hinzu. In `left` fügen Sie einen Block `rich_text` für die Botschaft hinzu, in `right` einen Block
`button`. Setzen Sie Text, Link und Ziel der Schaltfläche.

**5. Gestalten Sie ihn ohne Änderung am Template.** Tragen Sie eine Utility-Klasse des Themes in das Feld **CSS class**
des Containers ein. Diesen Schritt übersehen viele — hier zu einem Template zu greifen, macht aus einer Bearbeitung von
fünf Minuten ein Deployment.

**6. Positionieren Sie ihn.** Ziehen Sie den Container an den Anfang der Zone.

**7. Vorschau ansehen, dann veröffentlichen.**

**8. Im Frontend prüfen.** Laden Sie eine Seite des Bereichs und prüfen Sie, dass der Streifen gerendert wird und der
Link funktioniert.

Soll der Streifen später auf der ganzen Website erscheinen, verschieben Sie ihn in ein gemeinsames Layout und
verknüpfen Sie die Zone, statt ihn zu wiederholen.


## Durchgang B: ein neues Layout von Grund auf bauen

Das Ziel: ein Layout für eine Landingpage, einem Ablageort zugeordnet, mit einer Mischung aus kuratierten und
automatischen Inhalten.

**1. Legen Sie das Layout an.** *Layouts → Create*. Typ `layout_2` (Header / Main / Footer), Kennung
`campaign_landing`, Name „Campaign landing“.

**2. Kopfbereich.** Verknüpfen Sie die Zone `header` mit Ihrem gemeinsamen Layout für den Kopfbereich, statt ihn neu zu
bauen. Gibt es noch keines, ist jetzt der Moment, eines zu bauen.

**3. Hero.** Fügen Sie in `post_header` `ibexa_component_hero` hinzu und wählen Sie `hero_style_2`. Füllen Sie seine
Parameter aus.

**4. Kuratierte Höhepunkte.** Fügen Sie in `main` einen Block `list` mit dem View-Typ `grid_featured` hinzu. Geben Sie
ihm eine **manuelle** Collection und wählen Sie drei Elemente über den Inhaltsbrowser. Manuell ist hier richtig, weil
die Höhepunkte einer Kampagne ausgewählt und nicht berechnet werden.

**5. Automatische ergänzende Inhalte.** Fügen Sie darunter eine zweite `list` mit dem View-Typ `list` hinzu. Geben Sie
ihr eine **dynamische** Collection mit `exponential_content_search`:

- Entfernen Sie den Haken bei **use current location** und wählen Sie den Ordner der Kampagne, oder lassen Sie ihn
  gesetzt, wenn das Layout der Seite folgen soll, auf der es gerade ist.
- Setzen Sie den Haken bei **filter by content type** und wählen Sie Ihre Artikelklasse.
- Setzen Sie `sort_type` auf das Veröffentlichungsdatum, `sort_direction` absteigend.
- Setzen Sie **limit** auf 6.

**6. Doppelungen vermeiden.** Können dieselben Elemente in beiden Blöcken erscheinen, setzen Sie den **Offset** des
zweiten Blocks so, dass er nach den Höhepunkten beginnt.

**7. Ein Element anheften.** Muss ein Artikel unabhängig vom Datum die automatische Liste anführen, fügen Sie ihn als
manuelles Element auf Position 0 hinzu — er wird zu einer angehefteten Überschreibung, und die Abfrage füllt den Rest.

**8. Fußbereich.** Verknüpfen Sie `pre_footer` oder `footer` mit dem gemeinsamen Layout für den Fußbereich.

**9. Ordnen Sie es zu.** Legen Sie unter *Mappings* eine Regel an, die auf `campaign_landing` zeigt, mit einem Ziel
`node` für die Landingpage der Kampagne. Fügen Sie eine Bedingung `siteaccess` hinzu, wenn die Website mehrere
Siteaccesses hat. Geben Sie ihr eine Priorität über jeder allgemeineren Regel, die die Seite sonst abfangen würde.

**10. Veröffentlichen Sie beides**, das Layout und die Regel, und laden Sie dann die Seite.

Ändert sich nichts, arbeiten Sie die [Fehlerbehebung](#fehlerbehebung) durch — die übliche Antwort ist eine Regel mit
höherer Priorität, die zuerst passt.


## Wie das Rendern tatsächlich funktioniert

Wer die Kette versteht, sucht Fehler viel schneller.

1. Eine Anfrage kommt an. `pagelayout.tpl` fragt den Resolver nach einem Layout.
2. `expLayoutsResolver::resolve($path)` durchläuft die aktivierten Regeln in der Reihenfolge der Priorität, prüft zuerst
   die Ziele, dann die Bedingungen, und gibt das erste veröffentlichte Layout zurück, das passt.
3. `layout.tpl` rendert den Rahmen des Layouts und wählt das Unter-Template für den Layouttyp.
4. `zone.tpl` rendert die Blöcke jeder Zone in der Reihenfolge ihrer Position — oder bei einer verknüpften Zone die
   Blöcke des gemeinsamen Layouts.
5. Jeder Block wird über das Template gerendert, das zu seiner Definition und seinem View-Typ passt.
6. Bei einem Block mit einer Collection wird diese zuerst aufgelöst: Eine dynamische Collection führt ihre Abfrage über
   `expLayoutsDynamicCollection` aus, angeheftete Elemente werden eingefügt, Offset und Limit angewendet.

Die Templates liegen in `extension/explayouts/design/standard/templates/explayouts/`:

- `layout.tpl` — äußerer Rahmen
- `zone.tpl` — Blöcke einer Zone, der Reihe nach
- `block/*.tpl` — eines pro Blockdefinition und View-Typ
- `layouts/*.tpl` — eines pro Layouttyp

Ein Frontend-`pagelayout.tpl` tut typischerweise Folgendes:

```smarty
{def $layout=fetch( 'explayouts', 'resolve_layout', hash() )}
{if $layout}
    {include uri='design:explayouts/layout.tpl' layout=$layout}
{else}
    {$module_result.content}
{/if}
```

Der Zweig `else` ist wichtig: Eine Website soll auch rendern, wenn keine Regel passt.

### Verfügbare Fetch-Funktionen

| Funktion | Zweck |
|---|---|
| `fetch( 'explayouts', 'layout', hash( ... ) )` | Ein bestimmtes Layout laden |
| `fetch( 'explayouts', 'resolve_layout', hash() )` | Für die aktuelle Anfrage auflösen |
| `fetch( 'explayouts', 'resolve_layout_for_node', hash( ... ) )` | Auflösen, als ginge es um einen gegebenen Knoten |
| `fetch( 'explayouts', 'rules_for_node', hash( ... ) )` | Die zu einem Knoten passenden Regeln auflisten — nützlich zur Fehlersuche |

Zu `rules_for_node` greifen Sie, wenn eine Seite das falsche Layout erhält: Es sagt Ihnen, was gepasst hat, statt Sie
raten zu lassen.

### Caching des Resolvers

Der Resolver cacht die aufgelöste Regel und die Layout-ID pro Anfragepfad unter
`var/<VarDir>/cache/explayouts/resolver/`, mit einer TTL aus `explayouts.ini`:

```ini
[ResolverSettings]
DefaultLayout=homepage
CacheTTL=3600
```

Rufen Sie `expLayoutsResolver::clearCache()` auf, wenn sich Regeln oder Layouts ändern. Mit eingeschaltetem
`automatic_cache_clear` — die SPA meldet es in `/api/config` — erledigt das Veröffentlichen das für Sie. Ändern Sie
Regeln direkt in der Datenbank, leeren Sie ihn selbst.


## Das System erweitern

### Ein neuer Blocktyp

1. Schreiben Sie eine Handler-Klasse, die `getParameters()` deklariert und die Felder zurückgibt, die der Editor
   zeichnen soll. Verwenden Sie nur die sechs unterstützten Typen.
2. Registrieren Sie ihn in `explayouts.ini.append.php`:

```ini
[BlockDefinition_testimonial]
Name=Testimonial
Handler=expLayoutsTestimonialBlockHandler
ViewTypes[]=default
ViewTypes[]=compact
```

3. Fügen Sie unter `block/` ein Template pro View-Typ hinzu.
4. Erzeugen Sie die Autoloads neu und leeren Sie die Caches:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
systemctl restart php-fpm
```

Fügen Sie `HasCollection=1` hinzu, wenn der Block Inhalte der Website anzeigen soll, und der Editor bietet ihm eine
Collection an.

### Ein neuer View-Typ für einen vorhandenen Block

Oft ist nur eine andere Form gewünscht. Fügen Sie den View-Typ zu den `ViewTypes[]` des Blocks hinzu und schreiben Sie
das Template. Kein PHP. Das ist viel günstiger als ein neuer Blocktyp und sollte die Standardantwort auf „Kann es auch
wie X aussehen?“ sein.

### Ein Container-Block

Deklarieren Sie Platzhalter:

```ini
[BlockDefinition_split]
Name=Split
Handler=expLayoutsContainerBlockHandler
IsContainer=1
Placeholders[]=left
Placeholders[]=right
ViewTypes[]=default
```

Der mitgelieferte `expLayoutsContainerBlockHandler` reicht meist aus — Container brauchen selten eigene Logik.

### Ein neuer Abfragetyp

1. Schreiben Sie einen Handler, der die Abfrageschnittstelle implementiert, mit `getParameters()` für das Feld im
   Editor.
2. Registrieren Sie ihn:

```ini
[QueryType_most_read]
Name=Most read
Handler=expLayoutsMostReadQueryHandler
```

3. Erzeugen Sie die Autoloads neu und leeren Sie die Caches.

Fügen Sie einen Parameter hinzu, der nur sinnvoll ist, wenn ein anderer gesetzt ist, folgen Sie dem Muster des
zusammengesetzten Kontrollkästchens, damit das Feld lesbar bleibt.

> **Hinweis zur Engine.** Abfrage-Handler laufen auf der Datenbank, die die Website verwendet. MongoDB hat kein `JOIN`
> und kein `GROUP BY`, und der Treiber gibt für SQL, das er nicht übersetzen kann, ein **leeres Ergebnis** zurück,
> statt einen Fehler auszulösen. Ein mit einem Join geschriebener Handler gibt auf MongoDB daher stillschweigend nichts
> zurück und sieht genauso aus wie eine Abfrage ohne Treffer. Kann Ihre Website auf MongoDB laufen, verzweigen Sie nach
> `eZDB::instance()->databaseName() === 'mongo'` und lesen Sie die Collections getrennt. Mehrere mitgelieferte Handler
> tun genau das.


## Architektur und Datenmodell

Die Implementierung ist bewusst modular:

| Erweiterung | Zuständigkeit |
|---|---|
| `explayouts` | Persistenz, Rendern, Auflösen im Kern |
| `explayouts_core` | Fachliche Dienste — Layout, Block, Zone, Regel, Collection |
| `explayouts_standard` | Standard-Block-Handler |
| `explayouts_ui` | Älteres Administrationsmodul |
| `explayouts_ui_api` | SPA-Rahmen und JSON-API |
| `explayouts_content_browser` | Inhaltsauswahl |
| `explayouts_site_api` | Wertkonverter der Site-API |

### Tabellen

| Tabelle | Zweck | Persistentes Objekt |
|---|---|---|
| `explayouts_layout` | Ein benanntes Layout mit Kennung, Typ, Status | `expLayoutsLayout` |
| `explayouts_zone` | Ein Bereich innerhalb eines Layouts; kann mit einem anderen Layout verknüpft sein | `expLayoutsZone` |
| `explayouts_block` | Ein Block in einer Zone, mit Definition und View-Typ | `expLayoutsBlock` |
| `explayouts_block_parameter` | Einstellungen eines Blocks als Schlüssel/Wert | `expLayoutsBlockParameter` |
| `explayouts_collection` | Eine an einen Block gehängte Liste | `expLayoutsCollection` |
| `explayouts_collection_item` | Manuelle oder angeheftete Elemente | `expLayoutsCollectionItem` |
| `explayouts_collection_query` | Abfrageparameter einer dynamischen Collection | `expLayoutsCollectionQuery` |
| `explayouts_rule` | Eine Regel des Resolvers mit Priorität und Aktiviert-Kennzeichen | `expLayoutsRule` |
| `explayouts_rule_target` | Ziel einer Regel | `expLayoutsRuleTarget` |
| `explayouts_rule_condition` | Zusätzliche Bedingungen | `expLayoutsRuleCondition` |

Schemas werden für MySQL, SQLite, PostgreSQL und Oracle unter `extension/explayouts/sql/<engine>/schema.sql`
mitgeliefert. Oracle verwendet `NUMBER(11,0)`, `CLOB` und eine Sequenz plus Trigger pro Tabelle, um Auto-Increment
nachzubilden.

### Die JSON-API

Die SPA spricht mit `expLayoutsUIApplicationApi` unter `/explayouts_ui_api/app/api/`. Wer die Ressourcen kennt, kann
Änderungen einfach skripten:

| Ressource | Deckt ab |
|---|---|
| `config`, `config/layout_types`, `config/block_types` | Start des Editors und Kataloge |
| `layouts`, `layouts/shared`, `layouts/{id}/zones`, `layouts/{id}/blocks` | Struktur der Layouts |
| `layouts/{id}/publish`, `layouts/{id}/draft` | Veröffentlichen, Entwurf anlegen, verwerfen |
| `blocks/{id}` | Block bearbeiten, verschieben, duplizieren, löschen |
| `collections`, `collections/{id}/items` | Collections und ihre Elemente |
| `rules`, `mappings` | Regeln, Ziele, Bedingungen |
| `transfer/export`, `transfer/import` | Import und Export |
| `content_browser` | Inhaltsauswahl |
| `forms`, `parameters`, `versions`, `share` | Unterstützende Endpunkte |


## Fehlerbehebung

**Das Layout erscheint nicht auf der Website.**
Prüfen Sie in dieser Reihenfolge: Ist das Layout **veröffentlicht**; ist die Regel **aktiviert**; passt eine Regel mit
**höherer Priorität** zuerst; sind alle **Bedingungen** erfüllt. `fetch( 'explayouts', 'rules_for_node', ... )`
beantwortet das direkt. Denken Sie daran, dass ein unbekannter Bedingungstyp passt, statt fehlzuschlagen.

**Änderungen im Editor sind nicht sichtbar.**
Wahrscheinlich sehen Sie die veröffentlichte Ausgabe an, während Sie einen Entwurf bearbeiten. Veröffentlichen Sie,
oder verwenden Sie die Vorschau. Hat das Veröffentlichen nicht geholfen, leeren Sie die Caches und starten Sie PHP-FPM
neu.

**Ein dynamischer Block zeigt nichts.**
Spielen Sie die Absicht der Abfrage von Hand durch. Prüfen Sie, dass der Offset nicht hinter dem Ende der Ergebnisse
liegt, dass der Filter für Inhaltstypen Klassen nennt, die existieren, und dass „use current location“ so gesetzt ist,
wie Sie es beabsichtigen — ein Layout, das an einen Ablageort ohne Unterelemente geheftet ist, bleibt dauerhaft leer.
Lesen Sie bei MongoDB außerdem den Hinweis zur Engine unter [Das System erweitern](#das-system-erweitern): Eine nicht
übersetzbare Abfrage gibt ein leeres Ergebnis zurück, statt einen Fehler zu melden.

**Eine Liste steht in der falschen Reihenfolge.**
Prüfen Sie zuerst `sort_type` und `sort_direction`, suchen Sie dann nach angehefteten Elementen, die absolute Positionen
halten.

**Eine verknüpfte Zone zeigt nichts.**
Das gemeinsame Layout muss **veröffentlicht** sein. Ein gemeinsames Layout, das nur als Entwurf existiert, verknüpft mit
nichts.

**Der Editor lädt, aber jede Liste ist leer.**
Prüfen Sie die Richtlinien von `explayouts` in der Rolle des Benutzers, bevor Sie die Daten verdächtigen.

**Ein Block wird ungestaltet oder roh gerendert.**
Wahrscheinlich fehlt das Template für diese Kombination aus Definition und View-Typ. Prüfen Sie `block/` im aktiven
Design, und denken Sie daran, dass Designs in der Reihenfolge von `ActiveAccessExtensions` durchsucht werden.


## Referenztabellen

### Layouttypen

Siehe [Die Layouttypen](#die-layouttypen) — 12 Typen.

### Blocktypen

Siehe [Blockkatalog](#blockkatalog) — 46 Definitionen. Blöcke mit Collections: `list`, `grid`, `gallery`, `slider`,
`thumb_gallery`, `grid_gallery`, `sushi_bar`, `list_zigzag`, `list_accordion`, `carousel`, `logos`.

### Abfragetypen

Siehe [Abfragetypen und ihre Parameter](#abfragetypen-und-ihre-parameter) — 12 Typen.

### Regelziele

`path_prefix`, `path_info_prefix`, `path`, `path_regex`, `node`, `subtree`. (`route` ist deklariert, aber nicht
implementiert.)

### Regelbedingungen

`siteaccess`, `content_type` / `class`, `query_parameter`, `route_parameter`, `time`.

### Widget-Typen für Parameter

`text`, `textarea`, `select`, `multiselect`, `checkbox`, `browse`.

### Wichtige Dateien

| Datei | Zuständigkeit |
|---|---|
| `extension/explayouts/classes/explayoutsresolver.php` | Abgleich der Regeln und Auflösen der Layouts |
| `extension/explayouts/classes/explayoutsrenderer.php` | Bereitet die Daten von Layouts, Zonen und Blöcken für Templates auf |
| `extension/explayouts/classes/explayoutsdynamiccollection.php` | Führt dynamische Abfragen aus, fügt angeheftete Elemente ein |
| `extension/explayouts/classes/explayoutscollectionquery.php` | Persistentes Objekt für `explayouts_collection_query` |
| `extension/explayouts/settings/explayouts.ini.append.php` | Layouttypen, Blockdefinitionen, Abfragetypen, Einstellungen des Resolvers |
| `extension/explayouts/sql/{mysql,sqlite,pgsql,oracle}/schema.sql` | Datenbankschemas |
| `extension/explayouts/design/standard/templates/explayouts/` | Templates für das Rendern im Frontend |
| `extension/explayouts_ui_api/classes/explayoutsuiapplicationapi.php` | JSON-API der SPA |
| `extension/explayouts_core/classes/explayoutscorelayoutservice.php` | Lebenszyklus der Layouts — anlegen, veröffentlichen, Entwurf, verwerfen, kopieren |

### Checkliste für die Installation

1. Installieren Sie die Tabellen aus der SQL-Datei, die zu Ihrer Datenbank passt.
2. Aktivieren Sie `explayouts` und die zugehörigen Erweiterungen.
3. Erzeugen Sie die Autoloads neu: `php bin/php/ezpgenerateautoloads.php -e`
4. Leeren Sie die Caches und laden Sie das PHP-FPM neu, das die Website ausliefert:
   `php bin/php/ezcache.php --clear-all --allow-root-user`, dann `systemctl reload <your PHP-FPM service>` (starten Sie
   auch Velocity neu, wenn er die Website ausliefert).
5. Legen Sie unter `/explayouts_ui_api/app` ein Layout und eine Regel an.
6. Verbinden Sie `pagelayout.tpl` mit `fetch( 'explayouts', 'resolve_layout', hash() )`.

### Migration und Gleichwertigkeit

Die Portierung folgt der Datenform von Netgen Layouts, daher lässt sich ein XML/JSON-Export von Netgen mit minimaler
Umwandlung importieren. Die Unterschiede beschränken sich auf die Exponential-spezifischen Abfrage-Handler und die
Rendering-Schicht der Legacy-Templates.

## Verwandte Seiten

- [JSON-API des Layout-Editors (Spezifikation)](../../spezifikationen/6.0/explayouts-ui-api.md)
- [Layout-Editor (Seite der Erweiterung)](../../../eng-us/features/6.0/extensions/explayouts_ui_api.md) und [Formular-Editor](../../../eng-us/features/6.0/extensions/explayouts_ui.md)
- [Der Fork von layouts-core](../../../eng-us/features/6.0/platform-layouts-core-fork.md)
- [Verhaltensänderungen vom Juli und August 2026](verhaltensaenderungen-2026-07-08.md)
- [Anleitung zu Templates und Design](../../anleitungen/templates-und-design.md)
