# Mehrere Elemente auf einmal bearbeiten

Lesen Sie diese Seite, wenn Ihre Redakteure viele Elemente derselben Art ändern oder wenn Sie Datentypen,
Edit-Handler oder Admin-Overrides der Liste der Unterelemente oder der Suchergebnisse pflegen. Die neue View
`content/multiedit` bearbeitet den vollständigen Attributsatz von bis zu 50 Objekten in einem Formular, mit der
Mechanik des gewöhnlichen Editors selbst. Sie ist über die Liste der Unterelemente erreichbar (**Weitere Aktionen →
Ausgewählte bearbeiten** und **Mehrere neu anlegen**) und über die Suchergebnisse in der Administration.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neue View `content/multiedit` (Richtlinie `content/edit`, jedes Objekt mit `can_edit` geprüft). Suchergebnisse in der Administration erhalten eine Spalte mit Kästchen und **Ausgewählte bearbeiten**; die Liste der Unterelemente erhält **Ausgewählte bearbeiten** und **Mehrere neu anlegen**. Entwürfe werden mit den Einstellungen von `ezautosave` automatisch gespeichert. |
| Wer betroffen ist | Overrides von `content/searchresult.tpl`, `children_detailed.tpl`, `ezajaxsubitems_expdatatable.js` und `admin_datatable.css` (admin und admin4) zeigen die neuen Bedienelemente nicht. Erweiterungen mit Edit-Handlern: Ihre Handler laufen jetzt auch hier, pro Objekt. Datentypen brauchen keine Änderung. |
| Wie Sie es prüfen | `grep -n -A8 "multiedit" kernel/content/module.php` |
| Wie Sie es beheben | Führen Sie Ihre Overrides mit den unter [Dateien](#dateien) aufgeführten ausgelieferten Dateien zusammen. Sonst nichts. |

## Warum es überhaupt funktioniert

Jedes Bearbeitungs-Template eines Datentyps benennt seine Eingaben nach der **ID des Inhaltsobjektattributs**:

```
name="{$attribute_base}_ezstring_data_text_{$attribute.id}"
```

Diese ID ist eindeutig pro Objekt, pro Version, pro Sprache. Daher kann ein Formular die Attribute beliebig vieler
Objekte ohne Namenskollisionen tragen, und `validateInput()` / `fetchInput()` / `storeInput()` werden pro Objekt genau
so aufgerufen, wie `content/edit` sie für eines aufruft.

**An der Schicht der Datentypen wurde nichts geändert, und es muss auch nichts geändert werden.** Ein Datentyp, der im
normalen Editor funktioniert, funktioniert hier, einschließlich Datentypen, die es noch nicht gibt - die von Dritten
stammenden `sckenhancedselection`, `eztags`, `xrowmetadata` und `ezobjectrelationlist` rendern und senden alle korrekt,
ohne zu wissen, dass es diese View gibt.

## Warum eine eigene View

`content/edit` ist ein Zustandsautomat für ein einzelnes Objekt: Wahl von Sprache und Übersetzung, Versionskonflikte,
Platzierung, Auswahl von Knoten, „jemand anderes bearbeitet das gerade“. Eine Liste durch jeden seiner Zweige zu fädeln,
würde das, was alle den ganzen Tag benutzen, für etwas gefährden, das sie gelegentlich benutzen. `multiedit` ist ein
neuer Aufbau um dieselben Teile, im selben Modul - daher erbt es den Berechtigungskontext des Moduls, sein `redirectTo`
und seine Mechanik für Entwürfe.

## Verwendung

### Aus einer Liste der Unterelemente

Kreuzen Sie einige Elemente an, dann **Weitere Aktionen → Ausgewählte bearbeiten**.

Die Kästchen dort heißen `DeleteIDArray[]` - ein Name, den diese Liste für jede Massenaktion wiederverwendet - und
tragen **Knoten-IDs**. Die View löst sie auf.

### Aus einem Suchergebnis

`/content/search?SearchText=...` hat jetzt eine Spalte mit Kästchen, ein Kästchen zum Auswählen aller in der
Kopfzeile und eine Schaltfläche **Ausgewählte bearbeiten**. So bearbeiten Sie Elemente, die keinen gemeinsamen
Elternknoten haben: Durchsuchen Sie die ganze Website, kreuzen Sie an, was Sie möchten, und bearbeiten Sie es in einem
Formular.

Das Template der Suchergebnisse enthält bewusst **kein `<form>`**. Es wird innerhalb des Suchformulars eingebunden, und
ein Formular in einem Formular wird von jedem Browser verworfen - das innere existiert nicht, und seine Schaltfläche
sendet an die Aktion des äußeren. Die Schaltfläche baut stattdessen in JavaScript ein Formular, das die Knoten-IDs und
das Anfrage-Token trägt.

### Direkt

Senden Sie einen POST an `content/multiedit` mit einem der folgenden Felder:

| Feld | trägt |
| --- | --- |
| `ContentObjectIDArray[]` | Objekt-IDs |
| `MultiEditObjectIDArray[]` | Objekt-IDs |
| `ContentNodeIDArray[]` | Knoten-IDs |
| `MultiEditNodeIDArray[]` | Knoten-IDs |
| `DeleteIDArray[]` | Knoten-IDs (was die Liste der Unterelemente sendet) |
| `MultiEditReturnURI` | wohin Verwerfen und ein sauberes Veröffentlichen zurückkehren |

Alle werden zusammen akzeptiert und auf eine eindeutige Liste von Objekt-IDs reduziert. Ein Anfrage-Token
(`ezxform_token`) ist erforderlich, wie bei jedem POST in der Administration.

## Was das Formular tut

- **Gruppiert nach Inhaltsklasse.** Ein Abschnitt pro Klasse, in der Reihenfolge, in der die Klassen zuerst
  vorkamen. Eine gemischte Auswahl wird nicht abgelehnt; sie wird als Abschnitte gezeichnet.
- **Ein Panel pro Objekt**, einklappbar, standardmäßig offen, wenn die Gruppe drei oder weniger enthält oder wenn das
  Objekt die Validierung nicht bestanden hat.
- **Öffnet beim ersten Laden einen internen Entwurf pro Objekt** und trägt die Versionsnummern in versteckten Feldern
  `MultiEditDraft[objectID]` zurück, sodass ein zweimaliger Druck auf eine Schaltfläche nicht jedes Mal eine neue Version
  stapelt.
- **Sagt, was es weggelassen hat.** Ein Objekt, das Sie nicht bearbeiten dürfen, eines, das verschwunden ist, oder eines,
  dessen Entwurf sich nicht öffnen ließ, wird mit dem Grund aufgeführt, statt stillschweigend fallen gelassen zu werden.

### Die drei Schaltflächen

| Schaltfläche | tut |
| --- | --- |
| **Alle veröffentlichen** | validiert alles und veröffentlicht dann jedes Objekt einzeln |
| **Entwürfe speichern** | speichert, was eingegeben wurde, ohne zu veröffentlichen; auf Pflichtfeldern wird nicht bestanden |
| **Verwerfen** | entfernt die Entwürfe, die dieses Formular geöffnet hat, und kehrt zu `MultiEditReturnURI` zurück |

### Ein- und Ausklappen

Die Werkzeugleiste über den Abschnitten trägt **Alle aufklappen** und **Alle zuklappen**.

Ein geschlossenes Panel sendet seine Felder trotzdem - `<details>` versteckt seine Kinder, es entfernt sie nicht -,
daher speichert und veröffentlicht das Zuklappen von allem und ein Druck auf Veröffentlichen genau so viel wie das
Offenlassen. Nichts wird ausgelassen.

Panels sind standardmäßig offen, wenn ihre Klassengruppe drei oder weniger Elemente hat, und ein Element, das die
Validierung nicht bestanden hat, wird **beim Zurückkommen des Formulars zwangsweise geöffnet**, egal was vorher
zugeklappt war: Gesagt zu bekommen, dass es ein Problem gibt, und es dann suchen zu müssen, ist das Schlechteste aus
beiden Welten.


## Mehrere auf einmal anlegen

**Mehrere neu anlegen** steht neben **Neu erstellen** über einer Liste der Unterelemente. Es fragt, was und wie viele,
legt so viele Entwürfe unter diesem Elternknoten an und öffnet sie im selben Formular.

Ein neues und ein vorhandenes Objekt sind dasselbe, sobald ein Entwurf offen ist, daher ändert sich am Formular nichts:
dieselben Abschnitte, dieselben Bedienelemente zum Einklappen, derselbe Bericht über das Veröffentlichen pro Objekt.

- Die Liste der Typen ist `canCreateClassList()` für diesen Elternknoten - dieselbe Liste, aus der das Menü **Hier
  erstellen** gebaut wird -, daher beantwortet sie bereits „darf dieser Leser diese Klasse an diese Stelle setzen“. Die
  Schaltfläche ist deaktiviert, wenn hier nichts angelegt werden darf.
- Höchstens `eZMultiEdit::MAX_OBJECTS` (50) auf einmal.
- Nichts wird veröffentlicht, bis Sie es sagen. Wer die Seite verlässt, hinterlässt interne Entwürfe, genau wie beim
  Bearbeiten, und die Liste der Entwürfe des gewöhnlichen Editors lässt Sie sie verwerfen.

### Die Falle darin, für jeden, der es ändert

Ein neu instanziiertes Objekt erhält seine **Knotenzuweisung an Version 1**. Öffnet das Formular dann eine *zweite*
Version zum Bearbeiten - was `openDrafts()` tut, wenn ihm nicht gesagt wird, dass das Objekt bereits einen Entwurf hat -,
dann ist Version 2 das, was veröffentlicht wird, Version 2 hat keine Knotenzuweisung, und das Objekt wird **ganz ohne
Ort** veröffentlicht: Status 1, keine Zeile in `ezcontentobject_tree`, unsichtbar in jeder Liste und unmöglich zu
finden.

Daher gibt `createDrafts()` `objectID => version` zurück, und die View füllt `MultiEditDraft` damit vor, bevor die
Auswahl geöffnet wird. Prüfen Sie nach dem Anlegen, dass die neuen Elemente wirklich unter ihrem Elternknoten
erscheinen: Das ist die einzige Prüfung, die das erkennt.

## Automatisches Speichern

Entwürfe werden automatisch gespeichert, mit den Einstellungen, die der gewöhnliche Editor verwendet -
`autosave.ini` `[AutosaveSettings] Interval` (Standard 180 Sekunden) und `TrackUserInput` (Standard `enabled`),
ausgeliefert von der Erweiterung `ezautosave` in `extension/ezautosave/settings/autosave.ini.append.php`. Schalten Sie
das automatische Speichern dort aus oder deaktivieren Sie die Erweiterung `ezautosave`, und es ist auch hier aus. Eine
kleine Zeile neben den Bedienelementen zum Einklappen sagt, was gerade geschieht.

**ezautosave selbst wird nicht wiederverwendet und könnte es auch nicht.** Sein `Y.eZ.AutoSubmit` bindet ein Formular
an einen Endpunkt, der *ein* Objekt und *eine* Version nennt -
`ezautosave::savedraft::<objectID>::<version>::<language>` -, was ein Formular mit vielen nicht ausdrücken kann. Daher
wird das Formular stattdessen an seine eigene Aktion **Entwürfe speichern** zurückgesendet: eine Anfrage, jeder Entwurf
gespeichert, durch genau den Code, den die Schaltfläche verwendet.

Wissenswerte Details:

- **Nichts wird veröffentlicht.** Das automatische Speichern ist ein Speichern, und das Speichern überspringt die
  Prüfung der Pflichtfelder, daher blockiert es nie und veröffentlicht nie.
- **Dateifelder werden aus der Nutzlast weggelassen.** Eine Datei lässt sich nicht sinnvoll alle paar Minuten erneut
  senden, und ein Datentyp, der keinen Upload erhält, behält, was er bereits hat.
- **Ein laufendes Speichern verschluckt keine spätere Eingabe.** Das Flag für Änderungen wird erst gelöscht, wenn die
  Antwort ankommt, daher speichert der nächste Durchgang alles, was in der Zwischenzeit eingegeben wurde.
- Ein Druck auf eine echte Schaltfläche löscht das Flag, damit das automatische Speichern nicht mit dem Absenden
  konkurriert.
- Es braucht `fetch` und `FormData`; ohne sie startet es einfach nicht, und die Schaltflächen verhalten sich wie bisher.

## Edit-Handler von Erweiterungen

`eZContentObjectEditHandler::validateInputHandlers()` und `executeInputHandlers()` werden pro Objekt aufgerufen, wie
`content/attribute_edit` sie für eines aufruft, nach `initialize()`.

Das ist wichtiger, als es aussieht: Eine Erweiterung, die sich in das Bearbeiten einhängt - um zu validieren, um etwas
auszufüllen, um ein Speichern abzulehnen -, wird sonst **stillschweigend umgangen**. Eine Warnung, die eine Erweiterung
zurückgibt, hat kein Attribut, an das sie sich hängen kann, daher wird sie als eigene Zeile in der Liste „braucht
Aufmerksamkeit“ dieses Objekts angezeigt. Eine als einfache Zeichenkette gegebene Warnung wird genauso gelesen wie das
übliche Paar aus Name und Text.

## Eigene Aktionen

Ein Datentyp, der einen eigenen Umlauf braucht - **Objekt finden** bei einer Relation, ein Upload in einem Dateifeld -,
sendet `CustomActionButton[<attributeID>_<action>]`. Diese werden ausgewertet und an das `fetchInput()` jedes Objekts
übergeben.

Die Attribut-ID in diesem Namen macht das hier sicher: IDs sind eindeutig, daher nimmt jedes Objekt nur die Einträge,
die zu ihm gehören, und eine Auswertung bedient das ganze Formular.

## Wichtige Details

### Das Veröffentlichen mehrerer Objekte ist kein einzelner Akt

Jedes Objekt ist sein eigenes `eZOperationHandler::execute( 'content', 'publish', ... )`. Jedes davon kann an seiner
eigenen Validierung scheitern oder **von einem Workflow angehalten** werden - zum Beispiel einem Freigabe-Ereignis. Das
Ergebnis ist daher eine Liste, kein Ja:

- **veröffentlicht** - durch
- **wartet auf Freigabe** - ein Workflow hat es übernommen; nicht veröffentlicht, nicht fehlgeschlagen
- **konnte nicht veröffentlicht werden** - die Operation hat es abgelehnt

Wurde alles veröffentlicht und steht nichts aus, leitet das Formular auf `MultiEditReturnURI` weiter. Alles andere
hält Sie mit dem Bericht auf der Seite.

### Die Validierung ist vor dem Veröffentlichen alles oder nichts

Besteht ein Objekt die Validierung nicht, wird **nichts veröffentlicht**, und alles Eingegebene bleibt als Entwurf
erhalten. Die fehlgeschlagenen Objekte werden markiert, und jedes nennt seine eigenen fehlgeschlagenen Felder mit der
eigenen Meldung des Datentyps, zum Beispiel

> Needs attention before this item can be published:
> Number of columns — No POST variable. Please check your configuration.

Diese Meldung bedeutet meist ein **Pflichtattribut ohne Wert, dessen Widget nichts sendet, wenn es leer ist** - zum
Beispiel eine Mehrfachauswahl, in der nichts gewählt ist. Ein solches Objekt lässt sich auch im gewöhnlichen Editor
nicht veröffentlichen; multiedit macht das nur für mehrere Objekte auf einmal sichtbar.

### Entwürfe sind echt und sichtbar

Das Öffnen des Formulars legt einen internen Entwurf pro Objekt an. Bis Sie veröffentlichen oder verwerfen, zeigt der
gewöhnliche Editor für diese Objekte „Sie haben einen Entwurf“. Wer die Seite verlässt, lässt sie zurück.

Der Hinweis „Sie haben einen Entwurf“ des gewöhnlichen Editors lässt Sie jeden davon verwerfen.

### Grenzen und Berechtigungen

- **50 Objekte** pro Formular (`eZMultiEdit::MAX_OBJECTS`). Darüber hinaus wird die Auswahl abgeschnitten: Jedes Objekt
  ist ein Entwurf, ein Abschnitt im Formular und ein Veröffentlichen.
- Die View verlangt die Richtlinie `content/edit`, und **jedes Objekt wird einzeln** mit `can_edit` geprüft. Objekte,
  die Sie nicht bearbeiten dürfen, werden gemeldet, nicht versteckt.
- `MultiEditReturnURI` wird nur beachtet, wenn es ein relativer Pfad der Website ist, sodass ein gesendetes Feld nicht
  anderswohin weiterleiten kann.

### Sprache

Eine Sprache pro Formular. Sie kommt aus `/content/multiedit/(language)/eng-US`, sonst aus der eigenen Ausgangssprache
jedes Objekts. Ein Objekt, das die angeforderte Sprache nicht hat, fällt auf das zurück, was es hat, statt nichts zu
zeichnen.


## Hinweise zur Sicherheit

Jede Objekt-ID, Versionsnummer, Klassen-ID, Elternknoten-ID und Rücksprungadresse, mit der diese View arbeitet, kommt
aus der Anfrage. Keiner davon wird vertraut, und jede Prüfung unten schließt etwas, das bei der Überprüfung tatsächlich
ausnutzbar war, nichts Hypothetisches.

### Ein Entwurf gehört nur Ihnen

`eZMultiEdit::mayUseDraft()` sichert sowohl das Verwerfen als auch das Übernehmen eines Entwurfs ab. Drei Bedingungen,
alle erforderlich:

1. die Version ist ein Entwurf (nie eine veröffentlichte),
2. der Leser darf das Objekt bearbeiten (`can_edit` - die Richtlinie `content/edit` des Moduls sagt nur, dass er
   *irgendetwas* bearbeiten darf),
3. die `creator_id` der Version ist der aktuelle Benutzer.

Ohne die dritte löschte das Nennen eines beliebigen Objekts und einer Version in `MultiEditDraft[...]` zusammen mit
**Verwerfen** diesen Entwurf - die laufende Arbeit jedes Redakteurs, irgendwo auf der Website, an Objekten, auf die der
Angreifer keine Rechte hatte und die er nie geöffnet hatte. Jetzt wird er stillschweigend übersprungen; ein übernommener
Entwurf wird stattdessen mit einem Grund abgelehnt.

Der Entwurf eines anderen wird hier bewusst **nicht** angeboten. Der Editor für einzelne Objekte hat für dieses Gespräch
eine Versionsauswahl; dieses Formular lehnt ab und sagt das.

### Die Rücksprungadresse kann die Website nicht verlassen

`MultiEditReturnURI` wird von Steuerzeichen befreit, abgelehnt, wenn es einen Backslash enthält, und muss mit genau
einem Schrägstrich beginnen, auf den kein weiteres Trennzeichen folgt. Ein führender Schrägstrich ohne zweiten genügt
allein nicht: Browser lesen `/\example.com` als protokollrelativ, und ein Zeilenumbruch im Wert kann an einer naiven
Prüfung vorbei in einen Header gelangen.

### Typen werden vor der Verwendung erzwungen

Es wird nicht angenommen, dass Felder der Anfrage die Form haben, die das Formular ihnen gibt. `MultiEditObjectIDArray`
als einfache Zeichenkette zu senden, erreichte früher `$_POST['MultiEditObjectIDArray'][] = ...` und endete fatal mit
„[] operator not supported for strings“ - ein Absturz durch ein präpariertes Feld. Sowohl das als auch `MultiEditDraft`
werden zuerst zu Arrays erzwungen.

### Der Sprache wird nur geglaubt, wenn es sie gibt

Die Sprache kommt aus der URL. Sie wird mit `eZContentLanguage::fetchList()` abgeglichen und sonst behandelt, als sei
sie nicht angefragt, statt in `createNewVersionIn()` getragen oder wie eingegeben ausgegeben zu werden. (Sie wurde auch
vorher nicht in die Seite gespiegelt - die Templates escapen, was sie ausgeben -, aber eine ungeprüfte Locale hatte in
der Inhalts-API nichts zu suchen.)

### Das Anlegen wird gegen den Elternknoten geprüft, nicht gegen die View

Die deklarierte Richtlinie der View ist `content/edit`. Das Anlegen braucht mehr, daher fragt `createDrafts()`
`$parent->checkAccess( 'create', $classID )` - dieselbe Frage, die das Menü **Hier erstellen** stellt -, und die
angebotene Klassenliste ist `canCreateClassList()` für diesen Elternknoten, sodass eine Klasse, die dort nicht hin darf,
gar nicht erst angezeigt wird.

### Grenzen

Eine Auswahl wird auf `MAX_OBJECTS` (50) gekürzt, und eine Zahl für das Anlegen wird darauf begrenzt. Jedes Objekt in
einem Formular ist ein Entwurf, ein Abschnitt im Formular und ein Veröffentlichen, daher begrenzt das sowohl die Seite
als auch die Schreibverstärkung einer präparierten Anfrage.


### Gegen ein echtes eingeschränktes Konto geprüft

Die Prüfungen des Besitzes oben lassen sich mit einem Entwurf zeigen, dessen Ersteller sich vom Benutzer der Sitzung
unterscheidet, aber das beweist nur die Hälfte. Daher werden die Prüfungen auch als Konto ausgeführt, das **einen
Teilbaum und sonst nichts** bearbeiten darf:

| Versuch | Ergebnis |
| --- | --- |
| ein Element innerhalb seines Teilbaums bearbeiten | erlaubt - die Sperre unterscheidet, statt alles zu verweigern |
| ein Element außerhalb bearbeiten | abgelehnt, und das Element wird mit dem Grund genannt |
| den Entwurf eines anderen Benutzers an einem Objekt übernehmen, das es nicht anfassen darf | abgelehnt |
| diesen Entwurf verwerfen | abgelehnt; die Version ist danach noch da |

Um das zu wiederholen, legen Sie auf einer Kopie der Installation ein Testkonto mit einer auf einen Teilbaum
eingeschränkten Rolle an (die für die ursprüngliche Prüfung verwendeten Skripte sind privat) und versuchen Sie die vier
Versuche oben.

Zwei Dinge, für die es sich lohnt, dieses Konto im Kopf zu behalten:

- eine Prüfung einer Richtlinie, die für einen Administrator besteht, sagt nichts über andere, und `content/edit` ohne
  Einschränkung ist nicht die Form, die die meisten Redakteure haben;
- `eZUser::setInformation()` schreibt den Passwort-Hash nur, wenn Passwort und Bestätigung übereinstimmen. Es einmal zu
  übergeben, hinterlässt ein Konto, das existiert, eingeschaltet ist, seine Rolle hat und sich nicht anmelden kann, ohne
  dass etwas sagt, warum - prüfen Sie daher, dass sich das Testkonto anmelden kann (`eZUser::loginUser()`), bevor Sie
  einer Ablehnung vertrauen.

### Abgedeckt durch

Private Regressionsskripte deckten jeden Fall oben ab (Besitz von Entwürfen, offene Weiterleitung, Typverwechslung);
sie werden nicht mitgeliefert. Um den Fall des Besitzes von Entwürfen von Hand zu wiederholen, brauchen Sie einen
Entwurf, der einem anderen Benutzer gehört, sowie dessen Objekt- und Versionsnummer.

## Dateien

| Datei | Teil |
| --- | --- |
| `kernel/content/module.php` | die Deklaration der View `multiedit` |
| `kernel/content/multiedit.php` | der Einstiegspunkt der View; der Code steht in `kernel/private/classes/views/content/multiedit.php` |
| `kernel/content/multiedit_functions.php` | `eZMultiEdit`, alle arbeitenden Teile |
| `design/admin/templates/content/multiedit.tpl` | das Formular |
| `design/admin/templates/content/searchresult.tpl` | Spalte mit Kästchen und die Schaltfläche |
| `design/admin/templates/children_detailed.tpl` | die Beschriftungen `Edit selected` und `Create multiple new` und die URL |
| `design/admin/javascript/ezajaxsubitems_expdatatable.js` (und die Kopie in `admin4`) | der Eintrag unter Weitere Aktionen und die Schaltfläche Mehrere neu anlegen |
| `design/admin/stylesheets/theme/admin_datatable.css` (und die Kopie in `admin4`) | die Gestaltung dieses Menüeintrags |
| `design/admin4/templates/content/multiedit.tpl` | die Kopie des Formulars in admin4 |

Das Formular bindet `design:content/edit_attribute.tpl` ein - dasselbe Include, das der Editor für einzelne Objekte
verwendet - und gibt ihm dieselben Variablen, die `content/attribute_edit` ihm gibt, einschließlich
`content_attributes_grouped_data_map`, woraus das Admin-Override dieses Templates tatsächlich zeichnet.

## Tests

Private Browser-Skripte deckten ab: Die View rendert, gruppiert, trägt Entwürfe und gibt eindeutige Attributnamen aus;
Weitere Aktionen und Suche funktionieren beide hin und zurück, und Verwerfen kehrt zurück; Mehrere neu anlegen (die
Schaltfläche, die Auswahl, die Entwürfe, das Veröffentlichen und dass neue Elemente einen Ort erhalten); automatisches
Speichern (die Anzeige, die Einstellungen, dass eine Änderung jeden Entwurf speichert, ohne zu veröffentlichen);
Sicherheit (Besitz von Entwürfen, offene Weiterleitung, Typverwechslung); und dieselben Prüfungen als Benutzer, der auf
einen Teilbaum eingeschränkt ist. Sie werden nicht mitgeliefert. Führen Sie die Prüfungen „von Hand“ aus den
Abschnitten oben auf einer Kopie der Installation aus. Prüfen Sie, dass die View registriert ist:
`grep -n -A8 "multiedit" kernel/content/module.php`.

## Bekannte Lücken

- **Keine Massenoperation auf Feldern.** Ein Feld über viele Objekte hinweg zu setzen - eine Sektion, ein Template, ein
  Code - ist eine andere Oberfläche und eine andere Geschichte der Validierung. Diese View bearbeitet ganze Objekte.
- **Kein Veröffentlichen pro Objekt.** Das Veröffentlichen ist pro Formular alles oder nichts; ein Objekt, das
  scheitert, hält den Rest vom Veröffentlichen ab.
- **Übersetzung wird nicht behandelt.** Das Formular bearbeitet eine Sprache und bietet nicht das Übersetzen aus einer
  anderen an, das der Editor für einzelne Objekte hat.
- **Die Platzierung ist nicht bearbeitbar.** Ein vorhandenes Objekt behält seine Orte; ein neu angelegtes erhält den
  Elternknoten, unter dem es angelegt wurde, und keine Möglichkeit, in diesem Formular einen zweiten Ort hinzuzufügen
  oder einen anderen zu wählen.
- **Konflikte mit Entwürfen werden abgelehnt, nicht gelöst.** Ein Objekt, das jemand anderes bearbeitet, wird mit einem
  Grund ausgelassen, wo der Editor für einzelne Objekte die Versionsauswahl anbietet.
- **Keine Vorschau.** Der Vorschaubereich von ezautosave gehört zu einem einzelnen Objekt und hat für ein Formular mit
  vielen keine Bedeutung.

## Verwandte Seiten

- [Optionen der Tabelle der Unterelemente](../../../eng-us/features/6.0/subitems-table-options.md) und [Unterelemente: Ausgewählte kopieren](../../../eng-us/features/6.0/subitems-copy-selected.md)
- [Content-Jobs](../../../eng-us/features/6.0/content-jobs.md)
- [September 2026, erste Hälfte: 14. September](../../../eng-us/history/2026/2026-09a.md#14-september-pdf-rss-and-the-rad-tools)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Anleitung zum Inhaltsmodell und zum Bearbeiten](../../anleitungen/inhaltsmodell-und-bearbeitung.md)
