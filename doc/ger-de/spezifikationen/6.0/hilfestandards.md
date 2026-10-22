# Hilfestandards: was die Hilfe von DITA, IEC/IEEE 82079-1, ISO/IEC/IEEE 26514, ISO 3864-2, EPUB Accessibility und WCAG übernimmt

Stand: 19. Oktober 2026. Der Pull-Request #288 („Help system plan, style guide and standards“) kündigte diese Seite als
dritte seiner Spezifikationen an, neben dem [Plan](hilfesystem.md) und den [Stilrichtlinien](hilfe-stilrichtlinien.md), aber die
Datei war beim Mergen nicht Teil des Pull-Requests. Diese Seite ist aus den beiden gemergten Seiten geschrieben und aus dem,
was Exponential 6.0.15 tatsächlich tut ([Hilfe als Inhalt gepflegt](../../funktionen/6.0/hilfeinhalte.md)).

Wer sie lesen sollte: wer Hilfethemen schreibt oder prüft, und wer die Frage beantworten muss: „Welchem Standard folgt Ihre
Dokumentation?“

**Es wird keine Konformität beansprucht.** Jeder der folgenden Standards dient als Quelle von Regeln: Die Hilfe übernimmt
einige davon, macht manches bewusst anders und lässt den Rest weg. Wo eine Regel vom Validator geprüft wird
(`./console exp:help:content check`), ist der Code genannt; alles andere ist eine Regel der Prüfung durch Menschen.

## Inhalt

1. [Kurz gesagt](#kurz-gesagt)
2. [DITA 1.3](#dita-13)
3. [IEC/IEEE 82079-1:2019](#iecieee-82079-12019)
4. [ISO/IEC/IEEE 26514:2022](#isoiecieee-265142022)
5. [ISO 3864-2:2016](#iso-3864-22016)
6. [EPUB Accessibility 1.1](#epub-accessibility-11)
7. [WCAG 2.2 AA](#wcag-22-aa)
8. [Verwandte Seiten](#verwandte-seiten)

## Kurz gesagt

| Standard | Verwendet für | Wie weit |
|---|---|---|
| DITA 1.3 (OASIS) | das Informationsmodell: Thementypen, Maps, Filterattribute, `shortdesc` | das Modell als Inhaltsklassen; es wird kein DITA-XML gespeichert. Ein Export nach DITA ist in Arbeit |
| IEC/IEEE 82079-1:2019 | Inhaltsregeln für Anleitungen: Hinweise dort, wo die Handlung geschieht, Voraussetzungen, Ergebnis, Minimalismus | für Struktur und Hinweise übernommen; die Signalwörter für Gefährdungen werden nicht verwendet |
| ISO/IEC/IEEE 26514:2022 | Gestaltung von Benutzerinformation: Zielgruppen, Aufgabenorientierung, Prüfung, Pflege | als redaktioneller Ablauf übernommen (Zustände, Rollen, Prüfdatum); kein formaler Dokumentationsplan je Release |
| ISO 3864-2:2016 | Sicherheitszeichen und Signalwörter | nur, um zu begründen, warum DANGER, WARNING und CAUTION nicht verwendet werden |
| EPUB Accessibility 1.1 | die E-Books der Handbücher | die Metadaten und die Struktur werden geschrieben; Exponential prüft die Bücher nicht mit Ace oder epubcheck |
| WCAG 2.2 AA | die Hilfe in der Administration und die E-Books | die Hilfe folgt den Gestaltungsregeln der Administrationsoberfläche; es gibt keine Prüfung durch einen Audit |

## DITA 1.3

**Übernommen**

- **Genau ein Thementyp je Thema**: concept, task, reference, troubleshooting (DITA 1.3 hat troubleshooting hinzugefügt).
  Es sind die Inhaltsklassen `help_concept`, `help_task`, `help_reference` und `help_troubleshooting`
  (`expHelpContentSchema::classes()`). Die Felder folgen den DITA-Elementen: Eine Aufgabe hat `context`, `prereq`, `steps`,
  `result` und `postreq`; ein Fehlerbehebungsthema `condition`, `cause` und `remedy`.
- **`shortdesc`** bei jedem Thema, ein Satz (Validator: `shortdesc.sentences`, `shortdesc.too-long`).
- **Maps**: ein Handbuch (`help_manual`, die Bookmap) und seine Kapitel (`help_section`) listen Themen in einer geordneten
  Relationsliste (`topicrefs`) auf; jedes Thema gibt es einmal, im Themenpool (Validator: `map.target`, `map.cycle`,
  `map.duplicate`).
- **`related-links`** als Relationsliste `related`; **Abbildungen** als eigene Objekte (`help_screenshot`) mit Alternativtext,
  den DITA als `<fig>` mit `<image><alt>` schreibt.
- **Filterattribute**: `audience` und `platform` als Felder, die Gültigkeit als `version_from` und `version_to` (DITA
  `rev`/`props`).
- **Eine stabile `id`**: die `help_id`, die zugleich die DITA-ID und der Dateiname in einem Export ist (Stilrichtlinien
  Abschnitt 7; Validator: `help-id.invalid`, `help-id.duplicate`).

**Anders**

- **Es wird kein DITA-XML gespeichert.** Die Textfelder enthalten Markdown (`eztext`), das die Hilfe sicher darstellt; die
  Inline-Elemente von DITA (`uicontrol`, `codeph` ...) werden als Markdown geschrieben (fett für eine Beschriftung der
  Oberfläche, Backticks für Code). Ein Export wandelt den Inhalt in DITA um; der Inhalt wird nie als XML bearbeitet.
- **Kein DITAVAL im Inhalt.** Die Zielgruppe eines Lesers entscheiden die Rechte von Exponential (Sektionen und der
  Objektzustand „Released“), nicht das Filtern eines Dokuments. Eine Ausgabe je Zielgruppe ist das, was der Leser lesen darf.
- **Kein conref und keine Keys.** Wiederverwendung geschieht über Relationen: Auf ein Thema wird verwiesen, es wird nie
  teilweise hineinkopiert.
- **Ein Glossareintrag** (`help_glossary_entry`) ist ein redaktionelles Werkzeug mit den zu vermeidenden Synonymen, kein
  DITA-`glossentry`, das veröffentlicht wird.

**Wie weit**: das Modell und der Validator. Der Export als DITA-Bookmap und das Speichern der E-Books als Dateiobjekte
kommen getrennt hinzu ([Hilfe als Inhalt gepflegt](../../funktionen/6.0/hilfeinhalte.md#abgleich-mit-den-spezifikationen)).

## IEC/IEEE 82079-1:2019

Der Standard für Gebrauchsinformationen zu Produkten. Die Hilfe übernimmt seine Regeln für den Aufbau von Anleitungen und
für Hinweise; er ist für Produkte geschrieben, die Menschen schaden können, was Websoftware nicht kann.

**Übernommen**

- **Voraussetzungen vor den Schritten, das Ergebnis danach**: die Felder `prereq` und `result` einer Aufgabe (Validator:
  `task.steps-elsewhere`, wenn darin Schritte stehen).
- **Eine Handlung je Schritt, nummerierte Schritte, das Ergebnis eines Schritts danach** (Stilrichtlinien Abschnitt 4;
  Validator: `task.steps-list` für genau eine nummerierte Liste, `task.too-many-steps` für mehr als 9, `task.sub-steps`).
- **Hinweise dort, wo die Handlung geschieht** (Stilrichtlinien Abschnitt 5): Ein Hinweis „Achtung“ oder „Datenschutz“ steht
  im Schritt, zuerst, vor der Handlung. Geprüft seit 6.0.15: `notice.position` (ein solcher Hinweis in `context`, `prereq`,
  `result` oder `postreq`), `notice.before-action` (nach der Handlung in seinem Schritt), `notice.per-step` (mehr als ein
  Hinweis in einem Schritt), `notice.too-long` (mehr als drei Sätze, außerhalb der Schritte).
- **Eine abgestufte Kennzeichnung von Hinweisen**: drei Stufen, Achtung (Folgen, die sich nicht rückgängig machen lassen),
  Datenschutz (wer welche Daten sieht) und Hinweis. Im Hilfebetrachter sind sie hervorgehobene Kästen mit eigenem Titel
  (`> **Attention:** ...`, `> **Data protection:** ...`, `:::data-protection`).
- **Aktuell gehaltene Information**: das Prüfdatum (`review_date`, Validator: `review-date.past`) und der Verantwortliche
  jedes Themas.
- **Einheitliche Terminologie**: Das Glossar entscheidet, der Validator meldet das zu vermeidende Wort (`glossary.avoid`).

**Anders**

- **Keine Sicherheits-Signalwörter.** DANGER, WARNING und CAUTION (GEFAHR, WARNUNG, VORSICHT) gelten Gefährdungen von
  Personen (siehe ISO 3864-2); die Hilfe meldet sie (`notice.signal-word`). Einen Ordner zu löschen ist kein Stromschlag.
- **Kein Sicherheitskapitel und keine gesammelten Hinweise.** 82079-1 erlaubt Sicherheitsinformationen in einem eigenen
  Abschnitt; die Hilfe verbietet das, damit ein Hinweis dort gelesen wird, wo er zählt.

**Wie weit**: die obigen Regeln für Struktur und Hinweise. Die Anforderungen des Standards an die Produktkennzeichnung, die
Informationen zur Entsorgung und sein Konformitätsverfahren gelten nicht für die Hilfe einer Webanwendung und werden nicht
befolgt.

## ISO/IEC/IEEE 26514:2022

Der Standard für die Gestaltung und Entwicklung von Informationen für Benutzer von Software.

**Übernommen**

- **Zielgruppenanalyse**: Die Zielgruppen sind eine konfigurierte Liste (`exphelp.ini [ContentSettings] Audiences[]`), jedes
  Thema nennt seine Zielgruppe, und die Sichtbarkeit folgt ihr (Stilrichtlinien Abschnitt 8).
- **Aufgabenorientierung und Minimalismus**: Thementypen, „die Handlung zuerst“, nichts Selbstverständliches, was
  schiefgehen kann in einem eigenen Fehlerbehebungsthema (Stilrichtlinien Abschnitte 1, 2 und 4).
- **Prüfung und Freigabe**: die Objektzustände Draft, Review, Released und die Regel, dass der Autor sein eigenes Thema nicht
  freigibt (die Rollen „Help editor“ und „Help release“).
- **Kontextbezogene Hilfe**: ein Thema, das über seine `help_id` aus der Administrationsoberfläche oder einem anderen Client
  geöffnet wird (`exphelp/context/<help_id>`), wobei der Validator prüft, dass die von Clients verwendeten help_ids
  existieren (`client.help-id-unknown`, `client.help-id-not-released`).
- **Navigation und Suche**: das Inhaltsverzeichnis des Handbuchs, die Suche der Hilfe und die verwandten Themen.

**Anders**

- **Kein Dokumentationsplan je Release** als eigenes Dokument. Was ein Release in der Hilfe ändert, steht im Changelog des
  Releases; das Prüfdatum und die Gültigkeitsfelder halten den Stand jedes Themas fest.
- **Benutzertests der Dokumentation** bleiben der Website überlassen; die Hilfe hat noch kein „War das hilfreich?“.

**Wie weit**: der redaktionelle Ablauf und die Ausgabekanäle. Das Verfahren des Standards zum Planen, Schätzen und Steuern
eines Dokumentationsprojekts wird nicht befolgt.

## ISO 3864-2:2016

Der Standard für Sicherheitskennzeichnungen an Produkten, der die Signalwörter DANGER, WARNING und CAUTION und den
Schweregrad definiert, für den sie stehen.

**Übernommen**: nichts als Regel. Der Standard ist der Grund, warum die Hilfe seine Signalwörter **nicht** verwendet: Sie
bedeuten Tod oder schwere Verletzung (DANGER, WARNING) und leichte Verletzung (CAUTION). Für Software verwendet, verlieren
sie ihre Bedeutung für den Fall, in dem es auf sie ankommt. Der Validator meldet sie (`notice.signal-word`).

**Wie weit**: nicht angewendet. Die Hilfe hat keine Sicherheitskennzeichen, Farben oder Piktogramme des Standards.

## EPUB Accessibility 1.1

Die E-Books der Handbücher (`./console exp:help:content export --format=epub`, **EPUB herunterladen**).

**Übernommen**

- **Metadaten zur Barrierefreiheit** im Paketdokument: `schema:accessMode` (textual, visual),
  `schema:accessModeSufficient` (textual), `schema:accessibilityFeature` (structuralNavigation, tableOfContents,
  alternativeText), `schema:accessibilityHazard` (none) und `schema:accessibilitySummary`
  (`expHelpContentExport::opf()`).
- **Struktur**: ein Navigationsdokument mit dem verschachtelten Inhaltsverzeichnis, Überschriften in der richtigen
  Reihenfolge, die Sprache des Buches (`xml:lang`, `lang`) und jede Abbildung mit ihrem Alternativtext und ihrer
  Bildunterschrift.
- **Alternativtext ist Pflicht** für jede Abbildung, höchstens 150 Zeichen (Validator: `figure.alt-missing`,
  `figure.alt-too-long`).

**Anders**

- Das Buch trägt `dcterms:conformsTo` „EPUB Accessibility 1.1 - WCAG 2.2 Level AA“, aber **Exponential prüft das Buch
  nicht**: epubcheck und Ace von DAISY werden nicht mitgeliefert. Eine Website, die die Bücher veröffentlicht, lässt beide
  vorher über die Datei laufen; ohne diesen Lauf ist die Angabe in den Metadaten nicht überprüft.
- Keine Seitenliste und keine Seitenumbrüche: Die Bücher haben keine gedruckte Ausgabe, auf die sie verweisen könnten.

**Wie weit**: Die Metadaten und die Struktur, die der Standard verlangt, werden geschrieben; die Bewertung ist Sache des
Herausgebers.

## WCAG 2.2 AA

Die Hilfe in der Administration (`exphelp`) und die E-Books.

**Übernommen**

- **Textalternativen** für Abbildungen (oben), **Überschriften und Landmarks** im Betrachter, die Sprache der Seite am
  Artikel (`lang`), Bedienung der Navigation, der Suche und des Inhaltsverzeichnisses per **Tastatur** und mit
  `role="note"` gekennzeichnete Hinweiskästen.
- **Kontrast und Dunkelmodus** über die Tokens des Admin-Designs: Jede Farbe der Hilfe hat einen hellen und einen dunklen
  Wert (die Gestaltungsregeln des Projekts für die Administration).

**Anders**: Nichts wird bewusst anders gemacht.

**Wie weit**: Die Hilfe folgt den Gestaltungsregeln der Administrationsoberfläche, die WCAG 2.2 AA anstreben; sie wurde nicht
gegen die Erfolgskriterien geprüft, und es wird keine Konformität beansprucht.

## Verwandte Seiten

- [Plan des Hilfesystems](hilfesystem.md)
- [Stilrichtlinien der Hilfe](hilfe-stilrichtlinien.md)
- [Hilfe als Inhalt gepflegt](../../funktionen/6.0/hilfeinhalte.md)
- [Die Hilfeklassen als Paket](hilfeklassen-als-paket.md)
