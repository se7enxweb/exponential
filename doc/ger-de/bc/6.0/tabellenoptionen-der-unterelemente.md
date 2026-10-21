# Die Liste der Unterelemente: Einstellungen und ihre Spalten

Lesen Sie diese Seite, wenn Ihre Redakteure die Liste der Unterelemente in der Administration verwenden, wenn Sie
`children_detailed.tpl` oder dessen JavaScript überschreiben oder wenn Sie eine eigene Spalte hinzufügen möchten. In
Exponential 6.0.15 bieten die **Einstellungen** (Table options) der Liste unter jedem Knoten
(`content/view/full/<node id>`) einen Katalog von mehr als hundert Spalten statt 15 fester, gruppiert, auf dem Server
sortierbar, umsortierbar, mit Vorgaben und CSV-Export. Ein Website-Entwickler fügt eine Spalte mit ein paar Zeilen INI
und einer PHP-Funktion oder einem Template hinzu.

Alles, was eine Spalte zeigt, wird auf dem Server berechnet, nur für die Spalten, die der Benutzer sichtbar gemacht hat,
und nur für die Zeilen der Seite auf dem Bildschirm. Eine Spalte, die der Benutzer nicht sehen darf (ihr `Policy[]`),
wird nicht angeboten, nicht berechnet und nicht gesendet.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neuer Spaltenkatalog `settings/subitemscolumns.ini` (129 Spalten), neue Einstellungen in `settings/subitems.ini` (`PageSizes[]`, `CSVLimit`, ...), Server-Funktionen hinter der Liste, die Wahl des Benutzers auf dem Server gespeichert, pro Bereich der Administration. Das alte Cookie `eZSubitemColumns` wird einmal übernommen. |
| Wer betroffen ist | Overrides von `children_detailed.tpl` in admin oder admin4 und des JavaScripts der Liste: Sie behalten die klassischen 15 Spalten. Installationen, deren `ezjscore.ini` die neuen Server-Funktionen fehlen, fallen ebenfalls auf die klassische Liste zurück. |
| Wie Sie es prüfen | `php vendor/bin/phpunit tests/tests/kernel/classes/subitems/` |
| Wie Sie es beheben | Führen Sie Ihre Overrides mit den ausgelieferten Templates zusammen; halten Sie `ezjscore.ini` aktuell. Um eine Spalte hinzuzufügen, folgen Sie [Eine Spalte in 3 Minuten hinzufügen](#eine-spalte-in-3-minuten-hinzufügen). |

## Was Benutzer sehen

**Einstellungen** (die Schaltfläche über der Liste) enthält:

- **Anzahl der Elemente pro Seite**: 10, 25, 50, 100 (`subitems.ini [SubitemsSettings] PageSizes[]`) oder eine eigene
  Zahl.
- **Spalte suchen**: ein Filterfeld; geben Sie einen Teil eines Namens ein („Alias“, „Anzahl“), und nur die
  passenden Spalten bleiben in der Liste.
- **Die Spalten nach Gruppe**: Grunddaten, Knoten, Objekt, Version, Ort, URLs, SEO, Zeitangaben, Personen,
  Verknüpfungen, Übersetzungen, Workflow, Benutzer, Medien, Technisch, Individuell, und eine Gruppe „Attribute:
  <Klasse>“ pro Klasse unter den Unterelementen. Ein Kontrollkästchen pro Spalte; die Beschreibung der Spalte ist ihr
  Tooltip.
- **Reihenfolge der sichtbaren Spalten**: Ziehen Sie eine Spalte, um sie zu verschieben, oder verwenden Sie ihre
  Pfeiltasten.
- **Spaltenvorgaben**: Wählen Sie eine (SEO, Redaktionell, Technisch, Übersetzungen werden mit dem System ausgeliefert),
  **Aktuelle speichern als...**, um die aktuellen Spalten unter einem eigenen Namen zu behalten, **Vorgabe löschen** für
  Ihre eigenen.
- **CSV exportieren**: lädt jedes Unterelement (bis zu `CSVLimit`) mit den sichtbaren Spalten in ihrer Reihenfolge
  herunter.

In der Tabelle selbst sortiert ein Klick auf den Kopf einer sortierbaren Spalte die Liste danach, auf dem Server (sodass
die Reihenfolge über Seiten hinweg stimmt). Eine Zelle einer mit `Copy=true` gekennzeichneten Spalte (IDs, Pfade, URLs,
der Seitentitel ...) kopiert ihren Wert bei einem Klick und meldet „Kopiert“.

Die Wahl wird für den Benutzer gespeichert, pro Bereich der Administration (der Inhaltsbaum, die Medien-Bibliothek und
die Benutzer behalten ihre eigene), auf dem Server: Sie folgt dem Benutzer in jeden Browser. Die früher im Cookie
`eZSubitemColumns` gewählten Spalten werden beim ersten Mal übernommen.

Die Liste funktioniert so in jedem Admin-Design: admin und admin4 haben ihr eigenes `children_detailed.tpl`, die
anderen Admin-Designs (admin2, admin3, sevenx_site_admin) verwenden das des Designs, auf das sie zurückfallen. Ohne die
Server-Funktionen (eine alte ezjscore.ini) fällt sie auf die klassischen 15 Spalten zurück.

## Der Spaltenkatalog

129 Spalten werden in `settings/subitemscolumns.ini` ausgeliefert; dazu kommen die automatischen Attributspalten (siehe
unten). „Sortierung“ nennt das Feld, nach dem die Liste sortiert, wenn der Kopf angeklickt wird (leer: nicht
sortierbar); „Richtlinie“ die Funktionen, die ein Benutzer braucht, damit ihm die Spalte überhaupt angeboten wird.
„Art“ ist, wie der Wert entsteht: `eingebaut` (wie bisher von der Liste aus dem Knoten gerendert), eine Klasse und ihr
`Field=`, ein `Handler` oder ein `Template`. Name und Beschreibung stehen hier so, wie die deutsche Administration sie
zeigt.

| Gruppe | Spalten |
|---|---|
| Grunddaten | 5 (eingebaut) |
| Knoten | 16 (2 eingebaut) |
| Objekt | 14 (3 eingebaut) |
| Version | 7 |
| Ort | 5 |
| URLs | 10 |
| SEO | 5 |
| Zeitangaben | 10 (2 eingebaut) |
| Personen | 5 (1 eingebaut) |
| Verknüpfungen | 6 |
| Übersetzungen | 6 (1 eingebaut) |
| Workflow | 12 (1 eingebaut) |
| Benutzer | 9 |
| Medien | 12 |
| Technisch | 4 |
| Individuell | 3 |

### Grunddaten (5)

| Schlüssel | Name | Art | Was sie zeigt | Sortierung | Richtlinie |
|---|---|---|---|---|---|
| `thumbnail` | Miniaturansicht | eingebaut | Eine kleine Vorschau des Bildes des Elements oder das Symbol seiner Klasse. |  |  |
| `name` | Name | eingebaut | Der Name des Elements, verlinkt mit seiner Seite in der Administration. | name |  |
| `visibility` | Sichtbarkeit | eingebaut | Sichtbar, Versteckt oder Versteckt durch übergeordneten Eintrag. | visibility |  |
| `type` | Typ | eingebaut | Der Name der Inhaltsklasse des Elements. | class_name |  |
| `priority` | Priorität | eingebaut | Die Priorität unter dem übergeordneten Element; direkt änderbar, wenn dieses nach Priorität sortiert. | priority |  |

### Knoten (16)

| Schlüssel | Name | Art | Was sie zeigt | Sortierung | Richtlinie |
|---|---|---|---|---|---|
| `nodeid` | Knoten ID | eingebaut | Die ID des Ortes (Knotens). | node_id |  |
| `noderemoteid` | Knoten remote ID | eingebaut | Die Remote-ID des Ortes, gleich in allen Installationen. |  |  |
| `parentnodeid` | Knoten-ID des übergeordneten Elements | expSubitemsNodeColumn `parent_node_id` | Die Knoten-ID des übergeordneten Ortes des Elements. |  |  |
| `depth` | Tiefe | expSubitemsNodeColumn `depth` | Wie tief der Ort im Baum liegt (die Inhaltswurzel ist 1). | depth |  |
| `pathstring` | Pfad aus Knoten-IDs | expSubitemsNodeColumn `path_string` | Die Knoten-IDs von der Spitze des Baums bis zum Element: /1/2/89/120/. | path |  |
| `pathidentification` | Pfad aus Bezeichnern | expSubitemsNodeColumn `path_identification` | Der Pfad des Ortes als Bezeichner (ezcontentobject_tree.path_identification_string). | path_string |  |
| `mainnodeid` | Knoten-ID des Hauptortes | expSubitemsNodeColumn `main_node_id` | Die Knoten-ID des Hauptortes des Objekts (gleich der Knoten-ID, wenn er es ist). |  |  |
| `childsortfield` | Unterelemente sortiert nach | expSubitemsNodeColumn `sort_field` | Wie das Element seine eigenen Unterelemente sortiert: Veröffentlicht, Name, Priorität, Geändert ... |  |  |
| `childsortorder` | Sortierrichtung der Unterelemente | expSubitemsNodeColumn `sort_order` | Aufsteigend oder absteigend, für die eigenen Unterelemente des Elements. |  |  |
| `hidden` | Verborgen | expSubitemsNodeColumn `is_hidden` | Von einem Redakteur versteckt: das Element selbst wurde versteckt (nicht eines seiner übergeordneten). |  |  |
| `invisible` | Unsichtbar | expSubitemsNodeColumn `is_invisible` | Besuchern nicht gezeigt: selbst versteckt oder unter einem versteckten Ort. | visibility |  |
| `iscontainer` | Behälter | expSubitemsNodeColumn `is_container` | Ob die Klasse des Elements Unterelemente haben kann. |  |  |
| `childrencount` | Anzahl der Unterelemente | expSubitemsNodeColumn `children_count` | Die direkten Unterelemente, die Sie lesen dürfen. Eine Zählabfrage je Zeile. |  |  |
| `subtreecount` | Größe des Teilbaums | expSubitemsNodeColumn `subtree_count` | Jedes Element darunter, in allen Tiefen, das Sie lesen dürfen. Eine Zählabfrage je Zeile über den ganzen Teilbaum: langsamer bei großen Bäumen. |  |  |
| `childclasses` | Klassen der Unterelemente | expSubitemsNodeColumn `child_classes` | Die Klassen der direkten Unterelemente mit ihrer Anzahl, die häufigste zuerst. Eine gruppierte Abfrage je Zeile; zählt jedes Unterelement. |  |  |
| `newestchildname` | Neuestes Unterelement | expSubitemsNodeColumn `newest_child_name` | Der Name des zuletzt veröffentlichten Unterelements, das Sie lesen dürfen. Eine begrenzte Abfrage je Zeile. |  |  |

### Objekt (14)

| Schlüssel | Name | Art | Was sie zeigt | Sortierung | Richtlinie |
|---|---|---|---|---|---|
| `section` | Sektion | eingebaut | Der Name der Sektion des Elements. | section |  |
| `objectid` | Objekt ID | eingebaut | Die ID des Inhaltsobjekts. | contentobject_id |  |
| `objectremoteid` | Objekt remote ID | eingebaut | Die Remote-ID des Inhaltsobjekts, gleich in allen Installationen. |  |  |
| `classidentifier` | Klassen Bezeichner | expSubitemsObjectColumn `class_identifier` | Der Bezeichner der Klasse des Elements, wie Templates und Fetches ihn verwenden. | class_identifier |  |
| `classid` | Klassen-ID | expSubitemsObjectColumn `class_id` | Die ID der Klasse des Elements. |  |  |
| `classgroups` | Klassengruppen | expSubitemsObjectColumn `class_groups` | Die Klassengruppen, zu denen die Klasse des Elements gehört (eine Abfrage je Klasse, nicht je Zeile). |  |  |
| `sectionidentifier` | Sektionsbezeichner | expSubitemsObjectColumn `section_identifier` | Der Bezeichner der Sektion des Elements (sortiert wird nach Sektions-ID). | section |  |
| `sectionid` | Sektions-ID | expSubitemsObjectColumn `section_id` | Die ID der Sektion des Elements. | section |  |
| `wordcount` | Wortanzahl | expSubitemsObjectColumn `word_count` | Wörter im Haupttext: das erste von body, description, intro, short_description, sonst der erste Rich-Text-, XML- oder Textblock mit Inhalt. Liest die Data Map. |  |  |
| `textlength` | Textlänge | expSubitemsObjectColumn `text_length` | Zeichen im Haupttext, ohne Auszeichnung. Liest die Data Map. |  |  |
| `readingtime` | Lesezeit | expSubitemsReadingTimeColumn | Minuten, die ein Leser für den Haupttext braucht (siehe Wortanzahl), bei WordsPerMinute Wörtern je Minute. Liest die Data Map. |  |  |
| `namelength` | Länge des Namens | expSubitemsObjectColumn `name_length` | Zeichen im Namen des Elements. |  |  |
| `rating` | Bewertung | expSubitemsRelationColumn `rating` | Die durchschnittliche Sternebewertung (braucht die Extension ezstarrating); leer, wenn nie bewertet. |  |  |
| `ratingcount` | Bewertungen | expSubitemsRelationColumn `rating_count` | Wie viele Sternebewertungen abgegeben wurden (braucht die Extension ezstarrating). |  |  |

### Version (7)

| Schlüssel | Name | Art | Was sie zeigt | Sortierung | Richtlinie |
|---|---|---|---|---|---|
| `version` | Objektversion | expSubitemsVersionColumn `version` | Die Nummer der aktuellen (veröffentlichten) Version. |  |  |
| `versioncount` | Versionen | expSubitemsVersionColumn `version_count` | Jede gespeicherte Version: veröffentlicht, archiviert, Entwürfe und die übrigen. |  |  |
| `draftcount` | Entwürfe | expSubitemsVersionColumn `draft_count` | Offene Entwürfe, die noch nicht veröffentlicht sind. |  |  |
| `draftauthors` | Autoren der Entwürfe | expSubitemsVersionColumn `draft_authors` | Wer einen offenen Entwurf des Elements hat. |  |  |
| `latestdraft` | Neuester Entwurf | expSubitemsVersionColumn `latest_draft` | Wann der neueste offene Entwurf zuletzt geändert wurde. |  |  |
| `archivedcount` | Archivierte Versionen | expSubitemsVersionColumn `archived_count` | Frühere Versionen, die im Verlauf aufbewahrt werden. |  |  |
| `versioncreated` | Version angelegt | expSubitemsVersionColumn `version_created` | Wann die aktuelle Version begonnen wurde (Geändert sagt, wann sie veröffentlicht wurde). |  |  |

### Ort (5)

| Schlüssel | Name | Art | Was sie zeigt | Sortierung | Richtlinie |
|---|---|---|---|---|---|
| `parentname` | Übergeordnetes Element | expSubitemsNodeColumn `parent_name` | Der Name des übergeordneten Ortes (einmal je Seite geladen). |  |  |
| `ismain` | Hauptort | expSubitemsNodeColumn `is_main` | Ob dies der Hauptort des Objekts ist. |  |  |
| `locationcount` | Orte | expSubitemsURLColumn `location_count` | Wie viele Orte das Objekt hat. Eine Abfrage je Zeile. |  |  |
| `otherlocations` | Weitere Positionen | expSubitemsURLColumn `other_locations` | Die URL-Aliase der weiteren Orte des Objekts. |  |  |
| `mainlocation` | Pfad des Hauptortes | expSubitemsURLColumn `main_location` | Der URL-Alias des Hauptortes, wenn diese Zeile nicht er ist. |  |  |

### URLs (10)

| Schlüssel | Name | Art | Was sie zeigt | Sortierung | Richtlinie |
|---|---|---|---|---|---|
| `urlalias` | URL-Alias | expSubitemsURLColumn `url_alias` | Der URL-Alias des Elements, wie die Administration ihn verwendet (der Pfad ab der Inhaltswurzel); / für den Knoten an der Wurzel der Site. | path_string |  |
| `allaliases` | Alle URL-Aliase | expSubitemsURLColumn `all_aliases` | Jeder eigene URL-Alias des Elements, in allen seinen Sprachen (/ ist die Wurzel der Site). Teilt die Alias-Abfrage mit den anderen Alias-Spalten. |  |  |
| `systemurl` | System-URL | expSubitemsURLColumn `system_url` | content/view/full/<node id>: die Adresse, die immer funktioniert, gleich welche Aliase es gibt. |  |  |
| `publicpath` | Öffentlicher Pfad | expSubitemsURLColumn `public_path` | Der Pfad, den Besucher im öffentlichen Siteaccess verwenden: der URL-Alias ohne den PathPrefix dieses Siteaccess. |  |  |
| `publicurl` | Öffentliche URL | expSubitemsURLColumn `public_url` | Die vollständige Adresse auf der öffentlichen Site: die SiteURL des öffentlichen Siteaccess und der öffentliche Pfad. |  |  |
| `urlslug` | URL-Kürzel | expSubitemsURLColumn `url_slug` | Das letzte Element des URL-Alias. |  |  |
| `aliascount` | URL-Aliase | expSubitemsURLColumn `alias_count` | Die eigenen URL-Aliase des Elements, normalerweise einer je Übersetzung. Eine Abfrage je Zeile, geteilt mit den anderen Alias-Spalten. |  |  |
| `customaliases` | Eigene URL-Aliase | expSubitemsURLColumn `custom_aliases` | Die zusätzlichen Aliase, angelegt unter URL-Aliase verwalten. |  |  |
| `customaliascount` | Anzahl eigener URL-Aliase | expSubitemsURLColumn `custom_alias_count` | Wie viele zusätzliche Aliase unter URL-Aliase verwalten angelegt wurden. |  |  |
| `historycount` | Alte URLs | expSubitemsURLColumn `history_count` | Frühere Adressen (nach Umbenennen und Verschieben), die noch auf das Element weiterleiten. |  |  |

### SEO (5)

| Schlüssel | Name | Art | Was sie zeigt | Sortierung | Richtlinie |
|---|---|---|---|---|---|
| `pagetitle` | Seitentitel im Browser | expSubitemsSEOColumn `page_title` | Der Titel der öffentlichen Site für die Seite des Elements, ermittelt wie ihr Pagelayout es tut, ohne die Seite zu erzeugen. |  |  |
| `titlelength` | Länge des Seitentitels | expSubitemsSEOColumn `title_length` | Zeichen im Seitentitel; Suchmaschinen zeigen etwa 60. |  |  |
| `metatitle` | Meta-Titel | expSubitemsSEOColumn `meta_title` | Der Titel im Metadaten-Attribut des Elements (xrowmetadata); leer, wenn keiner gesetzt ist. |  |  |
| `metadescription` | Meta-Beschreibung | expSubitemsSEOColumn `meta_description` | Die Beschreibung im Metadaten-Attribut des Elements (xrowmetadata). |  |  |
| `metakeywords` | Meta-Schlüsselwörter | expSubitemsSEOColumn `meta_keywords` | Die Schlüsselwörter im Metadaten-Attribut des Elements (xrowmetadata). |  |  |

### Zeitangaben (10)

| Schlüssel | Name | Art | Was sie zeigt | Sortierung | Richtlinie |
|---|---|---|---|---|---|
| `modified` | Geändert | eingebaut | Wann die aktuelle Version veröffentlicht wurde. | modified |  |
| `published` | Veröffentlicht | eingebaut | Wann das Element zum ersten Mal veröffentlicht wurde. | published |  |
| `modifiedsubnode` | Teilbaum geändert | expSubitemsNodeColumn `modified_subnode` | Wann sich zuletzt etwas im Teilbaum des Elements geändert hat. | modified_subnode |  |
| `publishedage` | Veröffentlicht (vor) | expSubitemsDateColumn `published_age` | Wie lange die erste Veröffentlichung des Elements zurückliegt: vor 3 Tagen. | published |  |
| `modifiedage` | Geändert (vor) | expSubitemsDateColumn `modified_age` | Wie lange die Veröffentlichung der aktuellen Version zurückliegt. | modified |  |
| `dayssincepublished` | Tage seit Veröffentlichung | expSubitemsDateColumn `days_since_published` | Ganze Tage seit der ersten Veröffentlichung des Elements. | published |  |
| `dayssincemodified` | Tage seit Änderung | expSubitemsDateColumn `days_since_modified` | Ganze Tage seit der Veröffentlichung der aktuellen Version. | modified |  |
| `publishediso` | Veröffentlicht (ISO 8601) | expSubitemsDateColumn `published_iso` | Die Veröffentlichungszeit als 2026-10-02T09:30:00+00:00, zum Kopieren. | published |  |
| `modifiediso` | Geändert (ISO 8601) | expSubitemsDateColumn `modified_iso` | Die Änderungszeit als 2026-10-02T09:30:00+00:00, zum Kopieren. | modified |  |
| `newestchild` | Neuestes Unterelement veröffentlicht | expSubitemsNodeColumn `newest_child` | Wann das zuletzt veröffentlichte Unterelement, das Sie lesen dürfen, veröffentlicht wurde. Eine begrenzte Abfrage je Zeile. |  |  |

### Personen (5)

| Schlüssel | Name | Art | Was sie zeigt | Sortierung | Richtlinie |
|---|---|---|---|---|---|
| `modifier` | Bearbeiter | eingebaut | Wer die aktuelle Version veröffentlicht hat. |  |  |
| `owner` | Eigentümer | expSubitemsObjectColumn `owner` | Der Benutzer, der das Objekt angelegt hat (sein Eigentümer). |  |  |
| `ownerid` | Eigentümer-ID | expSubitemsObjectColumn `owner_id` | Die Objekt-ID des Eigentümers. |  |  |
| `initialcreator` | Erster Autor | expSubitemsVersionColumn `initial_creator` | Wer die erste noch gespeicherte Version geschrieben hat. |  |  |
| `contributors` | Mitwirkende | expSubitemsVersionColumn `contributors` | Alle, die eine Version geschrieben haben, in der Reihenfolge ihrer ersten Version. |  |  |

### Verknüpfungen (6)

| Schlüssel | Name | Art | Was sie zeigt | Sortierung | Richtlinie |
|---|---|---|---|---|---|
| `relatedcount` | Verknüpfte Objekte | expSubitemsRelationColumn `related_count` | Objekte, mit denen das Element verknüpft ist (alle Verknüpfungsarten). Eine Zählabfrage je Zeile. |  |  |
| `relatednames` | Namen der verknüpften Objekte | expSubitemsRelationColumn `related_names` | Die Namen der ersten verknüpften Objekte, die Sie lesen dürfen, nach Namen. |  |  |
| `reverserelatedcount` | Rückwärts verknüpfte Objekte | expSubitemsRelationColumn `reverse_related_count` | Objekte, die mit dem Element verknüpft sind: was ins Leere zeigen würde, wenn es entfernt würde. Eine Zählabfrage je Zeile. |  |  |
| `reverserelatednames` | Namen der rückwärts verknüpften Objekte | expSubitemsRelationColumn `reverse_related_names` | Die Namen der ersten Objekte, die mit dem Element verknüpft sind und die Sie lesen dürfen. |  |  |
| `tags` | Schlagwörter | expSubitemsRelationColumn `tags` | Die Tags des Elements (braucht die Extension eztags und ein Tags-Attribut). |  |  |
| `tagcount` | Anzahl der Tags | expSubitemsRelationColumn `tag_count` | Wie viele Tags das Element hat (braucht die Extension eztags). |  |  |

### Übersetzungen (6)

| Schlüssel | Name | Art | Was sie zeigt | Sortierung | Richtlinie |
|---|---|---|---|---|---|
| `translations` | Übersetzungen | eingebaut | Die Flaggen der Sprachen, in die das Element übersetzt ist. |  |  |
| `initiallanguage` | Ursprungssprache | expSubitemsObjectColumn `initial_language` | Die Sprache, in der das Element zuerst geschrieben wurde. |  |  |
| `languagecount` | Sprachen | expSubitemsObjectColumn `language_count` | In wie viele Sprachen das Element übersetzt ist. |  |  |
| `languagecodes` | Sprachcodes | expSubitemsObjectColumn `language_codes` | Die Locales der Übersetzungen, z. B. eng-GB, ger-DE. |  |  |
| `missingtranslations` | Fehlende Übersetzungen | expSubitemsObjectColumn `missing_translations` | Die Sprachen der Installation, in die das Element nicht übersetzt ist. |  |  |
| `alwaysavailable` | Immer verfügbar | expSubitemsObjectColumn `always_available` | In jeder Sprache gezeigt, auch in denen ohne Übersetzung. |  |  |

### Workflow (12)

| Schlüssel | Name | Art | Was sie zeigt | Sortierung | Richtlinie |
|---|---|---|---|---|---|
| `objectstate` | Objektstatus | eingebaut | Die Objektzustände des Elements, als Bezeichner Gruppe/Zustand. |  |  |
| `statusbadge` | Status | Template | Ein Abzeichen: Sichtbar, Versteckt, Durch übergeordnetes Element versteckt oder Gesperrt (eine Template-Spalte). |  |  |
| `locked` | Gesperrt | expSubitemsObjectColumn `locked` | Im Zustand „locked“ der Zustandsgruppe Lock (ez_lock). |  |  |
| `pendingcount` | Ausstehende Versionen | expSubitemsVersionColumn `pending_count` | Versionen, die in einem Workflow warten, etwa einer Freigabe. |  |  |
| `rejectedcount` | Abgelehnte Versionen | expSubitemsVersionColumn `rejected_count` | Versionen, die ein Freigebender abgelehnt hat. |  |  |
| `workflowprocesses` | Workflow-Prozesse | expSubitemsVersionColumn `workflow_processes` | Workflow-Prozesse, die für das Element laufen (eine Freigabe, eine verzögerte Veröffentlichung). Eine Zählabfrage je Zeile. |  |  |
| `canedit` | Sie können bearbeiten | expSubitemsPermissionColumn `can_edit` | Ob Sie das Element bearbeiten dürfen. |  |  |
| `canremove` | Sie können entfernen | expSubitemsPermissionColumn `can_remove` | Ob Sie das Element entfernen dürfen. |  |  |
| `canmove` | Sie können verschieben | expSubitemsPermissionColumn `can_move` | Ob Sie das Element verschieben dürfen. |  |  |
| `canhide` | Sie können verstecken | expSubitemsPermissionColumn `can_hide` | Ob Sie das Element verstecken und wieder anzeigen dürfen. |  |  |
| `cantranslate` | Sie können übersetzen | expSubitemsPermissionColumn `can_translate` | Ob Sie das Element übersetzen dürfen. |  |  |
| `cancreate` | Sie können darunter anlegen | expSubitemsPermissionColumn `can_create` | Ob Sie unter dem Element Inhalte anlegen dürfen; leer, wenn es kein Container ist. |  |  |

### Benutzer (9)

| Schlüssel | Name | Art | Was sie zeigt | Sortierung | Richtlinie |
|---|---|---|---|---|---|
| `userlogin` | Anmelden | expSubitemsUserColumn `login` | Der Anmeldename des Benutzers. |  | role/read |
| `useremail` | E-Mail | expSubitemsUserColumn `email` | Die E-Mail-Adresse des Benutzers. Nur für Benutzer, die Rollen zuweisen dürfen. |  | role/assign |
| `userenabled` | Konto aktiviert | expSubitemsUserColumn `enabled` | Ob sich das Konto anmelden darf. |  | role/read |
| `userlocked` | Ausgesperrt | expSubitemsUserColumn `locked` | Nach zu vielen fehlgeschlagenen Anmeldungen ausgesperrt (site.ini [UserSettings] MaxNumberOfFailedLogin). |  | role/read |
| `userlastvisit` | Letzter Besuch | expSubitemsUserColumn `last_visit` | Wann der letzte Besuch des Benutzers begann; leer, wenn er sich nie angemeldet hat. |  | role/read |
| `userlogincount` | Anmeldungen | expSubitemsUserColumn `login_count` | Wie oft sich der Benutzer angemeldet hat. |  | role/read |
| `userfailedlogins` | Fehlgeschlagene Anmeldungen | expSubitemsUserColumn `failed_logins` | Fehlgeschlagene Anmeldeversuche seit der letzten erfolgreichen Anmeldung. |  | role/assign |
| `userroles` | Rollen | expSubitemsUserColumn `roles` | Die Rollen, die für den Benutzer gelten, direkt oder über seine Gruppen. Zwei Abfragen je Zeile. |  | role/read |
| `userrolecount` | Anzahl der Rollen | expSubitemsUserColumn `role_count` | Wie viele Rollen für den Benutzer gelten. |  | role/read |

### Medien (12)

| Schlüssel | Name | Art | Was sie zeigt | Sortierung | Richtlinie |
|---|---|---|---|---|---|
| `hasimage` | Hat Bild | expSubitemsMediaColumn `has_image` | Ob das Element ein Bild hat; leer, wenn seine Klasse kein Bildattribut hat. |  |  |
| `imagecount` | Bilder | expSubitemsMediaColumn `image_count` | Bildattribute, die ein Bild enthalten. |  |  |
| `imagedimensions` | Bildmaße | expSubitemsMediaColumn `image_dimensions` | Breite x Höhe des Originalbildes, in Pixeln. |  |  |
| `imagewidth` | Bildbreite | expSubitemsMediaColumn `image_width` | Die Breite des Originalbildes, in Pixeln. |  |  |
| `imageheight` | Bildhöhe | expSubitemsMediaColumn `image_height` | Die Höhe des Originalbildes, in Pixeln. |  |  |
| `imagesize` | Dateigröße des Bildes | expSubitemsMediaColumn `image_size` | Die Größe der Originalbilddatei (in Bytes in der CSV-Datei). |  |  |
| `imagemime` | Bildtyp | expSubitemsMediaColumn `image_mime` | Der MIME-Typ des Originalbildes. |  |  |
| `imagealt` | Alternativtext des Bildes | expSubitemsMediaColumn `image_alt` | Der Alternativtext des Bildes; leer, wenn es keinen hat, was sich zu beheben lohnt. |  |  |
| `filename` | Dateiname | expSubitemsMediaColumn `file_name` | Der Name, mit dem die Datei (Binärdatei oder Medium) hochgeladen wurde. |  |  |
| `filesize` | Dateigröße | expSubitemsMediaColumn `file_size` | Die Größe der gespeicherten Datei (in Bytes in der CSV-Datei). |  |  |
| `filemime` | Dateityp | expSubitemsMediaColumn `file_mime` | Der MIME-Typ der Datei. |  |  |
| `filedownloads` | Downloads | expSubitemsMediaColumn `file_downloads` | Wie oft die Datei über content/download heruntergeladen wurde. |  |  |

### Technisch (4)

| Schlüssel | Name | Art | Was sie zeigt | Sortierung | Richtlinie |
|---|---|---|---|---|---|
| `searchwords` | Wörter im Suchindex | expSubitemsPermissionColumn `search_words` | Wortpositionen, die die eingebaute Suchmaschine für das Element indexiert hat (0: nicht indexiert). Eine Zählabfrage je Zeile; leer bei einer anderen Suchmaschine. |  |  |
| `viewcount` | Aufrufe | expSubitemsNodeColumn `view_count` | In ezview_counter gezählte Aufrufe (gefüllt vom Cronjob updateviewcount); leer, wenn nie gezählt. |  |  |
| `languagemask` | Sprachmaske | expSubitemsObjectColumn `language_mask` | Die Sprach-Bitmaske des Objekts (Bit 0: immer verfügbar). |  |  |
| `attributecount` | Attribute | expSubitemsObjectColumn `attribute_count` | Attribute der aktuellen Version in der gezeigten Sprache. Liest die Data Map. |  |  |

### Individuell (3)

| Schlüssel | Name | Art | Was sie zeigt | Sortierung | Richtlinie |
|---|---|---|---|---|---|
| `teaser` | Anrisstext | Template | Die ersten Wörter von Einleitung, Beschreibung oder Haupttext (eine Template-Spalte). |  |  |
| `editlink` | Bearbeiten-Link | Handler | Die Adresse der Bearbeitungsseite des Elements, wenn Sie es bearbeiten dürfen (eine Handler-Spalte). |  |  |
| `daysonline` | Tage online | Handler | Ganze Tage seit der ersten Veröffentlichung des Elements (eine Handler-Spalte). | published |  |


### Wie manche Spalten ihren Wert ermitteln

- **Seitentitel im Browser** (`pagetitle`) ist der `<title>`, den die öffentliche Website für die Seite des Elements
  ausgibt, ermittelt aus dem Knoten und den Einstellungen des öffentlichen Siteaccess, ohne die Seite zu rendern. Der
  öffentliche Siteaccess ist `SiteAccess=` im Block, sonst `site.ini [SiteSettings] DefaultAccess` (auf der
  Referenzinstallation: `site`). `TitleFormat=` wählt die Regel des Designs, das dieser Siteaccess verwendet:
  - `name` (der Standard; das Media-Design, `extension/sevenx_themes_media/design/media/templates/pagelayout/head/title.tpl`):
    `<page title> - <SiteName>`, wobei der Seitentitel der Meta-Titel des xrowmetadata-Attributs des Elements ist, wenn
    es einen hat, sonst der Name des Elements; der Seitentitel allein, wenn er SiteName entspricht. Auf der
    Referenzinstallation ist die Medienwurzel daher „Media - Fit & Healthy“, und der Knoten der Startseite „Fit &
    Healthy“ ist einfach „Fit & Healthy“.
  - `path` (`design/standard` und `page_head.tpl` von ezwebin): die Namen des Elements und seiner Vorfahren, das
    Element zuerst, verbunden mit „ / “, dann „ - <SiteName>“: „Banners / Media - Bold Agency“ (das Beispiel unter „Die
    ausgelieferten Familien“).

  SiteName ist das `[SiteSettings] SiteName` des öffentlichen Siteaccess; Namen werden in seiner ersten Sprache aus
  `SiteLanguageList[]` genommen, wenn das Element sie hat. Der Wert ist der reine Text (mit `&`, nicht `&amp;`), wie der
  Browser ihn zeigt. Ein Meta-Titel wird so verwendet, wie er gespeichert ist; der Operator `metadata()` von
  xrowmetadata lässt Platzhalter im Stil `[name]` stehen, und die Spalte ebenso.
- **URL-Alias** (`urlalias`) ist der Alias, den die Administration verwendet (`url_alias` des Knotens). Der Kernel
  antwortet in zwei Fällen mit einem leeren Alias, und die Spalte löst beide stattdessen aus der Alias-Tabelle auf, statt
  eine leere Zelle zu zeigen: Der Knoten, dem das Wurzelelement von `ezurlalias_ml` gehört (die Zeile mit Elternteil 0
  und leerem Text), ist die Seite an der Wurzel der Website und zeigt `/`; die Inhaltswurzel (`[NodeSettings] RootNode`),
  deren `pathWithNames()` immer leer ist, zeigt ihren eigenen Alias, wenn das Wurzelelement einem anderen Knoten
  gehört. Auf der Referenzinstallation gehört das Wurzelelement dem Knoten 89 („Fit & Healthy“, die Startseite), der
  außerdem den benannten Alias `fit-healthy` hat, unter dem seine Unterelemente hängen, und Knoten 2 ist unter
  `websites` erreichbar. **Alle URL-Aliase** (`allaliases`) führt jeden eigenen Alias des Elements auf.
- **Öffentlicher Pfad / Öffentliche URL** nehmen das `PathPrefix` des öffentlichen Siteaccess vom Alias weg (sofern
  `PathPrefixExclude[]` es nicht behält, wie `urlAlias()` es tut) und setzen seine `SiteURL` davor, mit `Scheme=https`,
  sofern SiteURL kein Schema hat.
- **Verborgen** ist das Element, das ein Redakteur versteckt hat; **Unsichtbar** ist „Besuchern nicht gezeigt“, selbst
  versteckt oder unter einem versteckten Knoten. Sie unterscheiden sich für alles unter einem versteckten Knoten.
- **Anzahl der Unterelemente** zählt die Unterelemente, die der aktuelle Benutzer lesen darf; **Klassen der
  Unterelemente** zählt jedes Unterelement pro Klasse (nur Zahlen, keine Namen).
- **Wortanzahl**, **Textlänge** und **Lesezeit** lesen den Haupttext: das erste von `body`, `description`, `intro`,
  `short_description` (oder `Attributes[]` im Block), das ein Rich-Text-, XML- oder Textblock mit Inhalt ist, sonst
  überhaupt das erste solche Attribut; Markup wird weggelassen.
- Die Spalten der Gruppe **Benutzer** sind für alles leer, was kein Benutzerkonto ist.
- **Bewertung** und **Schlagwörter** brauchen die Erweiterungen ezstarrating und eztags und ein Attribut dieser Art;
  ohne sie sind sie leer, nie ein Fehler.

Jede Spalte liefert eine leere Zelle (null), wenn sie für die Klasse eines Elements nicht zutrifft, und hält die Liste
nie an: Eine Spalte, die fehlschlägt, liefert eine leere Zelle und eine Debug-Warnung.

## Eine Spalte in 3 Minuten hinzufügen

Eine Spalte ist ein Block `[Column_<key>]`. Legen Sie ihn in `settings/override/subitemscolumns.ini.append.php` ab,
oder besser in einer Erweiterung: `extension/<yours>/settings/subitemscolumns.ini.append.php` (mit der in
`ActiveExtensions[]` aufgeführten Erweiterung). Der Schlüssel besteht aus Kleinbuchstaben, Ziffern und `_`. Leeren Sie
danach den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`); laden Sie das PHP-FPM, das die Administration
ausliefert, neu, wenn eine PHP-Klasse hinzugekommen ist (und erzeugen Sie die Autoloads neu). Ein **neues Template**
braucht außerdem ein Leeren des Template-Override-Caches (`--clear-id=template-override`), sonst bleibt die Zelle leer.

Die drei Beispiele unten sind die, die
`tests/tests/kernel/classes/subitems/columns/fixtures/subitemscolumns.ini.append.php` enthält;
`expSubitemsGuideExamplesTest` lädt diese Datei in eine Registry und berechnet die Spalten so, wie es die Server-Funktion
rows tut.

### 1. Ein Handler: eine Funktion

```ini
[Column_mydaysonline]
Name=Days online
Group=Custom
Type=number
Handler=expSubitemsColumnHandlers::daysOnline
SortField=published
Align=right
Order=5000
Description=Whole days since the item was first published.
```

```php
class expSubitemsColumnHandlers
{
    public static function daysOnline( eZContentObjectTreeNode $node, array $settings, expSubitemsColumn $column )
    {
        $object = $node->attribute( 'object' );
        $published = $object instanceof eZContentObject ? (int)$object->attribute( 'published' ) : 0;
        return $published > 0 ? expSubitemsColumn::days( $published ) : null;
    }
}
```

Die Funktion erhält den Knoten der Zeile, den Block als Array und das Spaltenobjekt und gibt den Wert zurück: null, einen
Skalar oder eine Liste von Skalaren. `Type=` entscheidet, wie die Zelle und die CSV ihn zeigen (hier eine Zahl).
`SortField=published` lässt den Kopf die Liste nach dem Veröffentlichungsdatum sortieren. Das ausgelieferte
`kernel/classes/subitems/columns/expsubitemscolumnhandlers.php` enthält diesen und `editLink`.

### 2. Eine Klasse: eine eigene Spalte

```ini
[Column_myreadingtime]
Name=Reading time
Group=Custom
Type=number
Class=expSubitemsReadingTimeColumn
WordsPerMinute=250
Align=right
Order=5010
Description=Minutes a reader needs for the main text.
```

```php
class expSubitemsReadingTimeColumn extends expSubitemsColumn
{
    public function value( eZContentObjectTreeNode $node )
    {
        $count = $this->wordCount()->value( $node );
        if ( !$count )
            return null;
        $perMinute = max( 1, (int)$this->setting( 'WordsPerMinute', 200 ) );
        return max( 1, (int)ceil( $count / $perMinute ) );
    }

    public function html( eZContentObjectTreeNode $node, $value )
    {
        return $value === null ? '' : self::escape( $value . ' ' . ezpI18n::tr( 'design/admin/node/view/full', 'min' ) );
    }

    public function prefetch( array $nodes )
    {
        $this->wordCount()->prefetch( $nodes );
    }

    protected function wordCount()
    {
        return new expSubitemsObjectColumn( $this->key, array( 'Field' => 'word_count' ) + $this->settings );
    }
}
```

Eine Klasse erweitert `expSubitemsColumn` und implementiert `value()`; sie kann `html()` (die Zelle; maskieren Sie alles
mit `self::escape()`), `text()` (die CSV), `sortBy()`, `isAvailable()` und `prefetch()` (siehe Leistung) überschreiben.
Sie liest ihre eigenen Einstellungen mit `$this->setting( 'Name', $default )`, sodass eine Klasse mehreren Blöcken dienen
kann. Die Basisklasse hat die Formatierung, die alle Spalten teilen, zur Verwendung in `value()` und `html()`:
`escape()`, `oneLine()`, `markupToText()`, `linkHtml()`, `formatDate()`, `formatAge()` („vor 3 Tagen“), `days()`,
`formatBytes()` („245 kB“) und `memo( $bucket, $key, $compute )` für eine Abfrage, die einmal pro Anfrage erledigt wird.
Das ist die ausgelieferte Spalte `readingtime` (`kernel/classes/subitems/columns/expsubitemsreadingtimecolumn.php`).

### 3. Ein Template: Markup ohne PHP

```ini
[Column_mystatus]
Name=Status
Group=Custom
Type=html
Template=design:subitems/columns/statusbadge.tpl
Order=5020
Description=What visitors see of the item.
```

```smarty
{def $exp_badge = 'visible'}
{if $node.is_hidden}{set $exp_badge = 'hidden'}
{elseif $node.is_invisible}{set $exp_badge = 'invisible'}
{elseif $node.object.state_identifier_array|contains( 'ez_lock/locked' )}{set $exp_badge = 'locked'}{/if}
{switch match=$exp_badge}
{case match='hidden'}<span class="exp-subitems-badge exp-subitems-badge-hidden">{'Hidden'|i18n( 'design/admin/node/view/full' )}</span>{/case}
...
{case}<span class="exp-subitems-badge exp-subitems-badge-visible">{'Visible'|i18n( 'design/admin/node/view/full' )}</span>{/case}
{/switch}
{undef $exp_badge}
```

Das Template erhält `$node` (die Zeile), `$column` (den Block als Hash: `$column.Length`, `$column.Attributes`) und
`$key`. Seine Ausgabe ist die Zelle; sein Text ohne Tags („Sichtbar“) ist der Wert und der CSV-Text. Maskieren Sie, was
Sie ausgeben (`|wash`). Die ausgelieferten `design/standard/templates/subitems/columns/statusbadge.tpl` und
`teaser.tpl` sind beides Beispiele; `teaser.tpl` zeigt das Lesen von Block-Einstellungen und einen XML-Textblock.

### Die ausgelieferten Familien

Die meisten ausgelieferten Spalten sind eine Klasse pro Familie, wobei `Field=` die Spalte wählt, sodass ein eigener
Block sie mit anderen Einstellungen wiederverwenden kann (ein anderes `SiteAccess=` für den Seitentitel, `Limit=` für
Namen von Verknüpfungen, `Attributes[]` für den Haupttext oder das Bild):

| Klasse | Felder |
|---|---|
| `expSubitemsNodeColumn` | parent_node_id, parent_name, depth, path_string, path_identification, main_node_id, is_main, sort_field, sort_order, is_hidden, is_invisible, is_container, children_count, subtree_count, child_classes, newest_child, newest_child_name, modified_subnode, view_count |
| `expSubitemsObjectColumn` | class_identifier, class_id, class_groups, section_identifier, section_id, owner, owner_id, initial_language, language_count, language_codes, missing_translations, always_available, language_mask, state_identifiers, locked, word_count, text_length, name_length, attribute_count |
| `expSubitemsVersionColumn` | version, version_count, draft_count, draft_authors, latest_draft, archived_count, pending_count, rejected_count, initial_creator, contributors, version_created, workflow_processes |
| `expSubitemsURLColumn` | url_alias, all_aliases, system_url, public_path, public_url, url_slug, alias_count, custom_aliases, custom_alias_count, history_count, location_count, other_locations, main_location |
| `expSubitemsSEOColumn` | page_title, title_length, meta_title, meta_description, meta_keywords |
| `expSubitemsDateColumn` | published_age, modified_age, days_since_published, days_since_modified, published_iso, modified_iso |
| `expSubitemsUserColumn` | login, email, enabled, locked, last_visit, login_count, failed_logins, roles, role_count |
| `expSubitemsMediaColumn` | has_image, image_count, image_dimensions, image_width, image_height, image_size, image_mime, image_alt, file_name, file_size, file_mime, file_downloads |
| `expSubitemsRelationColumn` | related_count, related_names, reverse_related_count, reverse_related_names, tags, tag_count, rating, rating_count |
| `expSubitemsPermissionColumn` | can_edit, can_remove, can_move, can_hide, can_translate, can_create, search_words |

Zum Beispiel der Titel des Siteaccess `bold`, nach der Pfadregel (Stil von design/standard):

```ini
[Column_pagetitle_bold]
Name=Page title (bold)
Group=SEO
Type=text
Class=expSubitemsSEOColumn
Field=page_title
SiteAccess=bold
TitleFormat=path
Copy=true
Order=701
Description=The <title> of the bold siteaccess.
```

Auf der Referenzinstallation zeigt das erste Unterelement der Medienwurzel dann „Banners / Media - Bold Agency“. Ein
`SiteAccess=`, das nicht in `AvailableSiteAccessList[]` steht, ergibt eine leere Zelle.

## Automatische Attributspalten

Mit `subitems.ini [SubitemsSettings] AttributeColumns=enabled` bieten die Einstellungen außerdem eine Spalte pro
Attribut jeder Klasse unter den Unterelementen des Elternknotens an, in einer Gruppe „Attribute: <Klassenname>“, mit dem
Schlüssel `attr:<class identifier>/<attribute identifier>` (eine Abfrage findet die Klassen). Der Wert ist der Titel des
Attributs, sonst sein Text ohne Tags (höchstens 300 Zeichen); die Spalte sortiert, wenn der Datentyp einen
Sortierschlüssel hat, und das Sortieren danach beschränkt die Liste auf diese Klasse (die Attributsortierung von
subTree lässt die anderen Klassen weg; die Anzahl bleibt richtig). `AttributeColumnsExcludedDataTypes[]` hält
Datentypen heraus (standardmäßig `ezuser`: sein Text enthält den Passwort-Hash), und
`AttributeColumnsDataTypePolicy[<datatype>]=module/function` gibt den Spalten eines Datentyps eine Richtlinie.

## Richtlinien

Einträge `Policy[]=module/function` in einem Block müssen alle gewährt sein (jede Einschränkung zählt als gewährt), sonst
existiert die Spalte für diesen Benutzer nicht: nicht in den Einstellungen, nicht berechnet, nicht in den Zeilen oder
der CSV, und eine gespeicherte Wahl oder Vorgabe, die sie nennt, lässt sie einfach weg. Mehrere Einträge können auch in
einer Zeile, getrennt durch `;`, geschrieben werden. Die Zeilen selbst beachten immer `content/read`: Die Liste und die
CSV zeigen nur Unterelemente, die der Benutzer lesen darf, und die Funktion rows verlangt Lesezugriff auf den
Elternknoten.

Die ausgelieferten Richtlinien: Die Gruppe **Benutzer** braucht `role/read` (die Personen, die die Einrichtung der
Rollen sehen), und **E-Mail** und **Fehlgeschlagene Anmeldungen** brauchen `role/assign`. Spalten, die Namen anderer
Objekte aufführen (verknüpfte Objekte), lassen diejenigen weg, die der Benutzer nicht lesen darf.

## Standardwerte pro Teilbaum und Klasse

Was ein Benutzer sieht, bevor er eine eigene Wahl speichert, kommt aus `subitems.ini`: Der erste Block
`[Defaults_<id>]`, dessen `Subtree[]` den Elternknoten oder einen seiner Vorfahren enthält oder dessen
`ParentClassIdentifiers[]` die Klasse des Elternknotens enthält, gewinnt, in der Reihenfolge der Datei; sonst
`DefaultColumns[]`. Die ausgelieferten Blöcke behalten die früheren Standardwerte der Liste für die Medien-Bibliothek
(`thumbnail;name;visibility;type`) und den Benutzerbaum.

```ini
[Defaults_fitness]
Subtree[]
Subtree[]=92
ParentClassIdentifiers[]
ParentClassIdentifiers[]=ng_blog
Columns[]
Columns[]=name
Columns[]=published
Columns[]=publishedage
Columns[]=pagetitle
```

Auf der Referenzinstallation ergibt das `name, published, publishedage, pagetitle` unter „Fitness“ (Knoten 92) und unter
jedem Knoten darunter. Blöcke, die eine Override-Datei hinzufügt, kommen nach den ausgelieferten; um die Standardwerte der
Medien-Bibliothek oder der Benutzer zu ändern, überschreiben Sie daher `Columns[]` von `[Defaults_media]` /
`[Defaults_users]`, statt einen Block für dieselbe Stelle hinzuzufügen.

## Vorgaben

Blöcke `[Preset_<id>]` in `subitems.ini` (ID: `a-z`, `0-9`, `_`, `-`) werden allen angeboten: `Name=` und `Columns[]`
in Reihenfolge. Ausgeliefert: `seo`, `editorial`, `technical`, `translations`. Benutzer speichern ihre eigenen daneben
(„Aktuelle speichern als...“), höchstens 20, und können nicht die ID einer Vorgabe aus der INI übernehmen. Die Spalten
einer Vorgabe, die der Benutzer nicht sehen darf, werden für diesen Benutzer weggelassen.

## INI-Referenz

### settings/subitemscolumns.ini

| Einstellung | Bedeutung |
|---|---|
| `[Column_<key>]` | eine Spalte; der Schlüssel wird in Vorgaben, Standardwerten, gespeicherten Wahlen und `columns=` verwendet |
| `Builtin=true` | eine der 15 Spalten, die die Liste selbst rendert; nur Name, Group, Order, Description, Copy, Align, Policy[] lassen sich ändern (ihre Standardwerte stehen in `expSubitemsBuiltinColumn::definitions()`) |
| `Class=` | eine Unterklasse von `expSubitemsColumn`, erzeugt mit (key, settings) |
| `Handler=class::method` | eine statische Methode (node, settings, column), die den Wert zurückgibt |
| `Template=design:...tpl` | ein Template; `$node`, `$column`, `$key`; Ausgabe = Zelle, Text = Wert |
| `Field=` | für die ausgelieferten Familienklassen: welche Spalte der Familie |
| `Name=` | der Name der Spalte, übersetzt im Kontext `design/admin/node/view/full` |
| `Group=` | Basic, Node, Object, Version, Location, URLs, SEO, Dates, People, Relations, Translations, Workflow, Users, Media, Technical, Custom; übersetzt wie `Name=` |
| `Type=` | text, html (vertrauenswürdiges Markup), number, date, datetime, bool, link (nur http(s), // oder /), code, list, image |
| `SortField=` | path, path_string, published, modified, section, depth, class_identifier, class_name, priority, name, modified_subnode, node_id, contentobject_id, visibility oder `attribute:<class>/<attribute>`; leer: nicht sortierbar |
| `Policy[]` | Einträge module/function, die der Benutzer alle haben muss |
| `Description=` | der Tooltip in den Einstellungen, übersetzt wie `Name=` |
| `Copy=true` | ein Klick auf eine Zelle kopiert den Wert |
| `Align=right` | rechtsbündig |
| `Order=` | Position in den Einstellungen und unter den Standardwerten |
| `SiteAccess=` | URL- und SEO-Spalten: der öffentliche Siteaccess (leer: DefaultAccess) |
| `Scheme=` | `public_url`: https (Standard) oder http, wenn SiteURL keines hat |
| `TitleFormat=` | `page_title`, `title_length`: name oder path |
| `Limit=` | Listen der Namen verknüpfter Objekte: die meisten Namen (Standard 10, höchstens 100) |
| `Attributes[]` | Haupttext, Bild- und Dateifelder und das Teaser-Template: zuerst probierte Bezeichner |
| `Length=` | das Teaser-Template: Zeichen |
| `WordsPerMinute=` | `readingtime`: Wörter pro Minute (Standard 200) |

### settings/subitems.ini

| Einstellung | Bedeutung |
|---|---|
| `[SubitemsSettings] DefaultColumns[]` | die sichtbaren Spalten, wo kein `[Defaults_*]` passt (`name;published;translations;priority`) |
| `AttributeColumns=` | enabled/disabled: die automatischen Attributspalten |
| `AttributeColumnsExcludedDataTypes[]` | Datentypen, die nie als Attributspalten angeboten werden (`ezuser`) |
| `AttributeColumnsDataTypePolicy[<datatype>]` | Richtlinien, die eine Attributspalte dieses Datentyps braucht (getrennt durch `;`) |
| `CSVExport=` | enabled/disabled: CSV exportieren und die View `content/subitemsexport` |
| `CSVLimit=` | die meisten Zeilen, die ein Export schreibt (5000) |
| `PageSizes[]` | die angebotenen Seitengrößen (10, 25, 50, 100) |
| `[Preset_<id>] Name=, Columns[]` | eine Vorgabe, die alle wählen können |
| `[Defaults_<id>] Subtree[], ParentClassIdentifiers[], Columns[]` | Standardwerte für eine Stelle |

## Die Server-API

Drei Funktionen von ezjscore, `[ezjscServer_expsubitems]` in `extension/ezjscore/settings/ezjscore.ini` (Klasse
`expSubitemsServerFunctions`), und eine Inhaltsansicht. Jeder Aufruf braucht einen angemeldeten Benutzer mit Zugang zur
Administration und Lesezugriff auf den Elternknoten; Schlüssel werden gegen die Registry geprüft, und nichts aus der
Anfrage wird je als Name einer Klasse, Funktion oder eines Templates verwendet. ezjscore verpackt jede Antwort als
`{ "error_text": "", "content": ... }`.

- `ezjscore/call/expsubitems::columns::<parent>`: die Spalten, die der Benutzer sehen darf (key, name, group, type,
  sortable, align, description, copy, builtin, order), `defaults`, `presets` (`source`: ini oder user), die
  gespeicherte `preference` (`visible`, `preset`, `page_size`, `saved`), `page_sizes`, `csv_export`. Auf der
  Referenzinstallation bietet die Medienwurzel als admin 138 Spalten an (129 aus der INI, 9 Attributspalten):

  ```json
  {"key":"pagetitle","name":"Page title in the browser","group":"SEO","type":"text","sortable":false,"align":"",
   "description":"The public site's title for the item's page, worked out as its pagelayout does, without rendering the page.",
   "copy":true,"builtin":false,"order":700}
  ```
- `ezjscore/call/expsubitems::rows::<parent>::<limit>::<offset>::<sort key>::<order 0|1>[::<name filter>]` mit
  `columns=a,b,c` (POST oder GET): die Antwort von `ezjscnode::subtree`, jedes Element mit
  `columns: { <key>: { v: <value>, h: "<html>" } }` für die angefragten nicht eingebauten Schlüssel. Der
  Sortierschlüssel ist ein Spaltenschlüssel, dessen Spalte sortiert, oder einer der alten Namen (`published_date`,
  `modified_date`, `node_id` ...); alles andere sortiert wie der Elternknoten. Zwei Unterelemente der Medienwurzel auf
  der Referenzinstallation, mit `columns=parentnodeid,urlalias,pagetitle,childrencount,version,readingtime`:

  ```json
  {"parentnodeid":{"v":43,"h":"43"},"urlalias":{"v":"media/banners","h":"<code>media/banners</code>"},
   "pagetitle":{"v":"Banners - Fit & Healthy","h":"Banners - Fit &amp; Healthy"},"childrencount":{"v":4,"h":"4"},
   "version":{"v":1,"h":"1"},"readingtime":{"v":null,"h":""}}
  {"parentnodeid":{"v":43,"h":"43"},"urlalias":{"v":"media/clients-partners","h":"<code>media/clients-partners</code>"},
   "pagetitle":{"v":"Clients & Partners - Fit & Healthy","h":"Clients &amp; Partners - Fit &amp; Healthy"},
   "childrencount":{"v":12,"h":"12"},"version":{"v":1,"h":"1"},"readingtime":{"v":null,"h":""}}
  ```
- `ezjscore/call/expsubitems::savepreference[::<parent>]`, POST `preference=<json>` (mit dem Formular-Token):
  `{ "visible": [...], "preset": "seo"|null, "page_size": 25, "presets": { "<id>": { "name", "columns" } } }`, jeder
  Schlüssel optional; gespeichert für den Navigationsbereich des Elternknotens.
- `content/subitemsexport/<parent>?columns=a,b,c&sort=<key>&order=0|1`: die CSV (siehe unten).

PHP-Code kann dieselben Teile verwenden: `expSubitemsColumnRegistry::instance()` (`availableColumns( $parent )`,
`column( $key, $parent )`, `resolveColumns( $keys, $parent )`, `defaults()`, `presets()`, `sortFor()`) und
`expSubitemsServerFunctions::columnValues( $nodes, $columns )` für die Zellen (es lädt vorab; `prefetch()` und
`cell( $column, $node )` sind die beiden Schritte für sich).

## Leistung

Berechnet werden nur die sichtbaren Spalten und nur für die Zeilen der Seite (die Seitengröße), nie für den ganzen
Teilbaum. Die 15 eingebauten kosten nichts zusätzlich: Sie kommen wie bisher mit dem Knoten. Die meisten anderen
Spalten lesen den geladenen Knoten oder das geladene Objekt und kosten keine Abfrage.

Die übrigen laden, was sie lesen, **einmal für die Seite**, nicht einmal pro Zeile: Bevor eine Zelle berechnet wird,
rufen die Funktion rows und der CSV-Export `prefetch( $nodes )` für jede angefragte Spalte mit den Knoten der Seite auf,
und die Spalte lädt ihre Daten für alle in einer Abfrage, in dasselbe Memo pro Anfrage, das ihr `value()` liest. Ein
Vorabladen ändert nur die Zahl der Abfragen, nie einen Wert (die Tests berechnen jede Spalte auf beide Arten und
vergleichen), und eines, das fehlschlägt, überlässt der Spalte einfach das Laden pro Zeile. Einmal pro Seite geladen:

| Daten | Spalten |
|---|---|
| Die Data Map (Attribute der aktuellen Version und Sprache) | Wortanzahl, Textlänge, Lesezeit, Anzahl der Attribute, jede Bild- und Dateispalte, Seitentitel und die Meta-Spalten, Schlagwörter, die automatischen Attributspalten |
| Versionszeilen | Anzahl der Versionen, Entwürfe, Autoren der Entwürfe, neuester Entwurf, archiviert, ausstehend, abgelehnt, erster Autor, Mitwirkende, Version erstellt |
| Lesbare Unterelemente pro Elternknoten (mit den Einschränkungen von content/read des Benutzers, wie `childrenCount()` es tut) | Anzahl der Unterelemente |
| Unterelemente pro Klasse | Klassen der Unterelemente |
| Zeilen der URL-Aliase | Anzahl der Aliase, alle URL-Aliase, eigene Aliase, Anzahl der eigenen Aliase, Anzahl der alten URLs |
| Orte des Objekts | Anzahl der Orte, andere Orte |
| Anzahl der Verknüpfungen (alle Typen, beide Enden veröffentlicht) | Anzahl der Verknüpfungen, Anzahl der Rückverknüpfungen |
| Zählungen | Workflow-Prozesse, Wörter im Suchindex, Aufrufzahl |

Spalten einer Zeile, die dieselben Daten lesen, teilen sie sich, und Abfragen, die für jede Zeile gleich sind (der Name
des Elternknotens, die Gruppen einer Klasse, eine Sektion, die Einstellungen des öffentlichen Siteaccess, der Name eines
Benutzers), werden einmal pro Anfrage erledigt. Die Funktion rows und der CSV-Export leeren zuerst das Memo, sodass ein
persistenter Worker nie aus einer früheren Anfrage antwortet. Unter MongoDB werden die gruppierten Abfragen
übersprungen, und die Spalten laden pro Zeile. Weiterhin pro Zeile: **Größe des Teilbaums** (eine Zählung über jeden
ganzen Teilbaum), **Neuestes Unterelement**, die auf URL-Aliasen basierenden Spalten für den seltenen Knoten, dessen
Alias der Kernel leer lässt, die Benutzerspalten und **Anrisstext** (er rendert einen XML-Textblock); behalten Sie die
schweren für kleine Seiten.

Eine Familienklasse (`expSubitemsFieldColumn`) führt in `prefetchSets()` auf, welche Werte von `Field=` welchen Satz
lesen, und hat für jeden ein `prefetch<Set>( $nodes )`; eine eigene Spaltenklasse überschreibt `prefetch()`.

Gemessen auf der Referenzinstallation (SQLite, als admin, auf der Kommandozeile; Median aus 5 Läufen, jeder in einem
frischen Prozess): jede der 114 nicht eingebauten Spalten auf einmal, für die ersten 10 Unterelemente der Medienwurzel
und für die ersten 50 und 100 Knoten des Inhaltsbaums.

| Zeilen | Abfragen | Zeit | Zeit ohne Anrisstext und Größe des Teilbaums |
|---|---|---|---|
| 10 | 130 (229 vor dem Vorabladen der Seite) | 186 ms (264 ms) | 107 ms (135 ms) |
| 50 | 814 (1353) | 1,6 s (1,9 s) | 558 ms (714 ms) |
| 100 | 1671 (2759) | 3,2 s (3,4 s) | 1,27 s (1,32 s) |

## CSV-Export

**CSV exportieren** in den Einstellungen, oder
`content/subitemsexport/<parent node id>?columns=a,b,c&sort=<key>&order=0|1`: jedes Unterelement, das der Benutzer
lesen darf (bis zu `CSVLimit`, jeweils 100 auf einmal geholt), die angegebenen Spalten (ohne `columns=`: die
gespeicherte Wahl des Benutzers, sonst die Standardwerte), in Reihenfolge; die Kopfzeile enthält die Spaltennamen.
UTF-8 mit BOM, durch Kommas getrennt, CRLF; Zellen, die mit `=`, `+`, `-` oder `@` beginnen, erhalten ein führendes `'`,
damit eine Tabellenkalkulation sie nicht ausführt. Eingebaute Spalten sind enthalten (außer der Miniaturansicht),
Listen werden mit `, ` verbunden, Daten als `Y-m-d H:i:s` geschrieben, boolesche Werte als 1/0, Größen in Bytes. Die
Datei ist nach dem Elternknoten benannt. `CSVExport=disabled` entfernt die Schaltfläche und die View.

## Wo die Wahl gespeichert wird

Die Wahl des Benutzers ist ein Wert von eZPreferences, `admin_subitems_table`, ein kompaktes JSON-Dokument:

```json
{"v":1,"parts":{"ezcontentnavigationpart":{"visible":["name","urlalias","pagetitle"],"preset":null}},
 "page_size":25,"presets":{"mine":{"name":"Mine","columns":["name","nodeid"]}}}
```

Die sichtbaren Spalten und die gewählte Vorgabe werden pro Navigationsbereich gehalten (`*`, wenn ohne Elternknoten
gespeichert), die Seitengröße und die eigenen Vorgaben einmal. Sie bleiben auf jeder Datenbank unter 4000 Bytes (die
Grenze von Oracle); eine Wahl, die nicht passt, wird abgelehnt, nicht abgeschnitten. Die alte Benutzereinstellung für
Zeilen pro Seite (`admin_list_limit`) wird weiterhin verwendet, solange keine Seitengröße gespeichert wurde.

## Wo der Code liegt

| Was | Wo |
|---|---|
| Die Basisklasse der Spalten, die gemeinsame Formatierung, das Memo und das Vorabladen der Data Map | `kernel/classes/subitems/expsubitemscolumn.php` (`expSubitemsColumn`) |
| Registry, eingebaute Spalten, Wrapper für Handler/Template/Attribute, Benutzereinstellung, Server-Funktionen, CSV | `kernel/classes/subitems/*.php` |
| Die ausgelieferten Spaltenklassen | `kernel/classes/subitems/columns/*.php` (`expSubitemsFieldColumn` ist die Basis der Familien: `Field=`, `prefetchSets()`, die Hilfsfunktion für gruppierte Zählungen) |
| Der Spaltenkatalog | `settings/subitemscolumns.ini` |
| Standardwerte, Vorgaben, CSV, Attributspalten | `settings/subitems.ini` |
| Die Template-Spalten | `design/standard/templates/subitems/columns/*.tpl` |
| Die Tabelle und die Einstellungen | `design/<admin design>/javascript/ezajaxsubitems_expdatatable.js`, `design/<admin design>/templates/children_detailed.tpl`, `extension/expui/design/standard/javascript/exp/datatable.js` |
| Die CSV-View | `kernel/content/subitemsexport.php` |

## Tests

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/subitems/            # everything (100 tests)
php vendor/bin/phpunit tests/tests/kernel/classes/subitems/columns/    # the catalogue and the prefetch (64 tests)
```

Die Tests des Katalogs starten den Kernel im Admin-Siteaccess mit der eigenen Datenbank der Installation und prüfen jede
Spalte gegen echte Knoten: die Inhaltswurzel, die Medienwurzel, die Benutzerwurzel, den Benutzer admin und Knoten, die
nach ihrem Inhalt gefunden werden (ein Bild, eine Datei, Schlagwörter, ein XML-Text, Verknüpfungen, eine Bewertung). Sie
prüfen, dass jeder Block sich bauen lässt, eine bekannte Gruppe, einen bekannten Typ und ein bekanntes Feld nennt, dass
sein Sortierfeld akzeptiert wird, dass jede Spalte für vier sehr verschiedene Knoten einen JSON-sicheren Wert und
einzeiligen CSV-Text liefert, dass Spalten, die nicht zutreffen, null liefern, dass Richtlinien über die Verfügbarkeit
entscheiden, dass kein Knoten je einen leeren URL-Alias zeigt, und die drei ausgearbeiteten Beispiele oben.
`expSubitemsPrefetchTest` berechnet jede Spalte (und die Attributspalten) für Seiten unter der Inhalts-, Medien- und
Benutzerwurzel einmal Zeile für Zeile und einmal mit dem Vorabladen der Seite, als admin und als anonym, und verlangt
dieselben Zellen; und dass die Zeilen der vorab geladenen Spalten nach dem Vorabladen keine Abfrage brauchen. Ein Test,
der Daten braucht, die die Datenbank nicht hat, wird übersprungen, nicht als fehlgeschlagen gewertet.

## Verwandte Seiten

- [Einstellungen der Liste der Unterelemente (Funktion)](../../funktionen/6.0/optionen-der-unterelementtabelle.md)
- [Unterelemente: Auswahl kopieren](../../funktionen/6.0/unterelemente-auswahl-kopieren.md)
- [Unterelemente: Verstecken und Einblenden unter Weitere Aktionen](unterelemente-weitere-aktionen-verstecken-einblenden.md)
- [Mehrere Elemente auf einmal bearbeiten](mehrere-elemente-bearbeiten.md)
- [Wo die Seitengrößen stehen](einstellungen-fuer-seitengroessen.md)
- [Anleitung zu Inhaltsmodell und Bearbeitung](../../anleitungen/inhaltsmodell-und-bearbeitung.md)
