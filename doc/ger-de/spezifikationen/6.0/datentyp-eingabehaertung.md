# Spezifikation: Härtung der Datentypen und Eingaben (27. September 2026)

Diese Seite ist die Referenz dafür, was jeder Datentyp des Kernels und jeder gemeinsame Eingabehelfer seit dem 27. September 2026 ablehnt
und was er stattdessen tut. Lesen Sie sie, wenn ein Formular jetzt eine Validierungsmeldung zeigt, wo es früher einen Wert angenommen hat, wenn
Sie Pakete importieren oder wenn Sie einen Datentyp oder einen Login-Handler schreiben.

An diesem Tag wurden alle Datentypen des Kernels, die gemeinsamen Eingabevalidatoren, die Mail-Klasse, der Download-Helfer und der INI-Writer
auf dieselbe Art von Fehler hin überprüft: Eingaben, die PHP 8 streng behandelt (ein Array, wo Text erwartet wurde,
`null`, eine Zahl, die zu groß für ihre Spalte ist), oder Eingaben, die ein feindseliger Client gezielt erzeugen könnte (Skript-URLs,
Verzeichnisanteile in Dateinamen, Zeilenumbrüche in Headern). Die umfassendere Liste der Sicherheitsvorgaben finden Sie unter
[Sicherheitsvorgaben vom September 2026](sicherheitsvorgaben-2026-09.md); zur früheren Überarbeitung siehe
[Sicherheitshärtung 6.0.13](sicherheitshaertung-6.0.13.md).

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `image.ini` | `ImageSettings` | `MaxImagePixels` | `100000000` (100 Millionen), vom Code gelesen; nicht in der mitgelieferten `settings/image.ini` aufgeführt | Installation oder Siteaccess; fügen Sie den Schlüssel einem Override hinzu, um ihn zu ändern |

Alles andere auf dieser Seite ist festes Verhalten ohne Einstellung.

## Regeln, die jetzt jeder Datentyp befolgt

1. **Ein gesendetes Array ist kein Text.** Ein Formularfeld, das als `name[]=x` gesendet wird, gilt als
   nicht angegeben oder als Validierungsfehler, nie als `TypeError` (HTTP 500).
2. **Fehlende Paketelemente lassen bestehen, was die Site hat.** Ein Import (`fromString`,
   Paketinstallation), dem ein Element fehlt, lässt das betreffende Feld unverändert und protokolliert das,
   statt mit einem fatalen Fehler abzubrechen oder mit `0`/leer zu überschreiben.
3. **Zahlen außerhalb des zulässigen Bereichs werden bei der Validierung abgelehnt**, statt gespeichert und von der Spalte
   abgeschnitten zu werden.
4. **Ausgaben werden maskiert**, in den Templates für Ansicht, Bearbeitung, Formularerfassung und Klasse.
5. **Pfade bleiben innerhalb des Speicherverzeichnisses.** Namen und MIME-Typen mit einem
   Verzeichnisanteil können einen Download, einen Export, ein Löschen, den Papierkorb oder einen Paketimport nicht
   auf einen anderen Ort lenken.
6. **Das gespeicherte Format hat sich nicht geändert.** Für jeden Datentyp liefen die alte und die neue
   Klasse in getrennten Prozessen gegen die Live-Datenbank (oder gegen einen Testkorpus,
   wo die Datenbank keine Beispiele hatte): Sie lesen jeden gespeicherten Wert gleich,
   schreiben byte-identisches XML bzw. byte-identische Zeilen, und die alte Klasse liest, was die neue
   schreibt. Beim Upgrade ist keine Datenumwandlung nötig.

## Gemeinsame Helfer

| Komponente | Vorher | Jetzt |
|---|---|---|
| `eZRegExpValidator`, `eZIntegerValidator`, `eZFloatValidator`, `eZDateTimeValidator` | Ein Array oder `null` gelangte an `preg_match()`, `checkdate()`, `mktime()`, `trim()`: `TypeError` oder Deprecation-Meldung. | Ein Array oder Objekt ist ungültig (`STATE_INVALID`); `fixup()` und die Locale-Parser geben es unverändert zurück, sodass der nachfolgende Validator es ablehnt; `null` gilt als leere Zeichenkette; ein nicht numerischer Datumsteil macht das Datum ungültig. |
| `eZLocale::internalNumber()`, `internalCurrency()` | `trim()` auf Arrays. | Der Typ wird zuerst geprüft. |
| `eZMail::validate()` | Muster mit `$` verankert, sodass eine Adresse gefolgt von einem Zeilenumbruch durchging; Steuerzeichen im zitierten lokalen Teil wurden akzeptiert. | Verlangt eine Zeichenkette, lehnt jedes Steuerzeichen ab, verankert mit dem Modifikator `D`. |
| `eZMail`-Setter | Werte wurden unverändert in den Header-Block geschrieben: Ein Zeilenumbruch in einem Absendernamen, Empfänger, Betreff, einer Message-ID oder einem zusätzlichen Header konnte einen `Bcc` oder einen beliebigen Header hinzufügen. | Jeder Setter, der einen Header befüllt, leitet Werte durch `eZMail::cleanHeaderValue()` (jede Folge von CR/LF wird zu einem Leerzeichen, andere Steuerzeichen außer TAB werden entfernt) und Header-Namen durch `cleanHeaderName()`. Nicht-ASCII-Namen und -Betreffzeilen (MIME-kodiert) werden wie bisher gesendet. |
| `eZFile::downloadHeaders()` / `contentDispositionHeader()` | Der Dateiname wurde ohne Anführungszeichen an `Content-Disposition` angehängt: Ein Semikolon fügte Parameter hinzu, ein Zeilenumbruch brachte PHP dazu, den Header abzulehnen. | Steuerzeichen werden entfernt; ein zitierter ASCII-Ersatz in `filename=` (alles außerhalb des druckbaren ASCII wird zu `_`) plus der exakte Name als RFC 5987 `filename*=UTF-8''...`, wenn beide sich unterscheiden. Ein Download ohne Namen behält das bloße `attachment` oder `inline`. |
| `eZUser::trimAuthString()` und Anmeldung | Als `Login[]`, `Password[]` gesendete Arrays endeten in einem 500-Fehler; ein unbekannter Benutzername kehrte sofort zurück, während bei einem bekannten der Passwort-Hash berechnet wurde. | Andere Werte als Zeichenketten gelten als nicht angegeben (damit sind auch Login-Handler in Extensions abgedeckt); bei einem unbekannten Konto wird ein Hash mit gleichem Aufwand berechnet. Ein Passwort oder gespeicherter Hash, der keine Zeichenkette ist, authentifiziert nie (`authenticateHash()` vergleicht strikt, ohne `(string)`-Cast; vorher passte `true` auf ein gespeichertes `true` beim Hash-Typ „plaintext“). |
| INI-Writer (Datentyp für INI-Einstellungen, beim Veröffentlichen eines Objekts gespeicherte Einstellungen) | Ein Wert mit Zeilenumbruch fügte eine beliebige Einstellung oder einen beliebigen Abschnitt hinzu; `*/` schloss den PHP-Kommentar um eine `.ini.append.php`-Datei; Datei-, Abschnitts- und Einstellungsnamen aus einer Klassendefinition wurden ungeprüft verwendet (`../` schrieb an beliebige Orte; ein unbekannter Ortsindex schrieb nach `settings/siteaccess/`). | Werte, Namen und Orte werden bei der Objektvalidierung, bei der Klassenvalidierung und noch einmal dort geprüft, wo die Datei geschrieben wird. Eine gespeicherte Einstellung wird innerhalb des umschließenden PHP-Kommentars geschrieben. |
| `eZTimeType` / `eZDateTime` / Zeitstempelumwandlung | Deprecation-Meldungen; `is_valid` blieb ungesetzt. | Typisiert, `is_valid` wird immer gesetzt. |
| Tabellen für die Zeichenumwandlung | `hexdec()` erhielt Zeichen, die keine Hexadezimalziffern sind. | Nur Hexadezimalziffern. |

## Je Datentyp

| Datentyp | Gehärtet gegen | Hinweise |
|---|---|---|
| Textzeile | `TypeError`, wenn eine Anfrage eine Liste sendet. | |
| Textabschnitt | Ein Array wurde als Array gespeichert, ein Paket ohne `text-column-count` führte zu einem fatalen Fehler, Zeilen wurden wie gesendet gespeichert. | Zeilen sind eine ganze Zahl von 1 bis 1000, Standard 10; der Wert wird in Bearbeitung, Formularerfassung und Klassenansicht maskiert. |
| Kontrollkästchen | Der Import speicherte, was immer die Datei enthielt (`yes`, `abc`); ein fehlender Standardwert führte zu einem fatalen Fehler. | Eine Zahl ungleich null, `true`, `yes`, `on` werden zu 1, alles andere zu 0; ein fehlender Standardwert bedeutet nicht angekreuzt; ein optionales Kontrollkästchen in einem Erfassungsformular wird als akzeptiert validiert. |
| E-Mail | Eine Adresse mit Zeilenumbruch oder umgebenden Leerzeichen wurde gespeichert. | Wird getrimmt und abgelehnt, wenn sie Steuerzeichen enthält. |
| Ganzzahl | Zahlen, die die Spalte nicht aufnehmen kann; Absturz bei gesendeter Liste; leeres Feld. | Bereich wird geprüft; ein geleertes Feld wird als „kein Wert“ gespeichert. |
| Fließkommawert | Wandte die Grenzen eines anderen Fließkommafelds an; riesige Zahlen; Klassen ohne Minimum oder Maximum. | Grenzen je Attribut; nur endliche Zahlen. |
| Datum, Datum und Uhrzeit, Uhrzeit | Ein Wort, ein halb ausgefülltes Datum, eine gesendete Liste, eine fehlende Minute ließen das Bearbeitungsformular oder die Objekterstellung abstürzen. | Stattdessen wird ein Validierungsfehler angezeigt. |
| Auswahlliste | Speicherte Optionen, die die Klasse nicht hat; Klassen ohne Optionen führten zu einem fatalen Fehler. | Nur die eigenen Optionen der Klasse; Export und Import vertragen das Fehlen von Optionen. |
| Aufzählung | Ein manipuliertes Formular speicherte fremde Elemente. | Nur die eigenen Elemente der Klasse. |
| Preis | `"12x50"` wurde akzeptiert (nicht maskierter `.`), 400 Ziffern wurden zu `INF`, `name[]=x` führte zum Absturz, MwSt.-Felder wurden als `Array` gespeichert, der Export ließ `<vat-type>` weg und der Import scheiterte dann. | Ein Punkt oder ein Komma bis ganz zum Ende; ein Dezimalkomma wird als Punkt gespeichert; endliche Zahlen; skalare gesendete Werte; MwSt.-ID und inkl./exkl. werden als Ganzzahlen gespeichert; Pakete ohne MwSt.-Typ lassen sich importieren. |
| Multi-Preis | `"1x5"` wurde akzeptiert; eine Währung, die der Shop nicht hat; „Individuellen Preis einstellen“ für eine Währung ohne Preis führte zu einem fatalen Fehler; das Entfernen nach Währung entfernte den Eintrag aus einer Kopie der Liste. | Dieselben Preisregeln; die Währung erhält einen neuen individuellen Preis von 0; Attribut-IDs und Währungscodes im Sortier-SQL werden gecastet und maskiert. |
| Produktkategorie | Der Name wurde statt der ID gespeichert. | Wird als Kategorie-ID gespeichert; Namen werden in Formularen maskiert. |
| Optionsgruppen, Multi-Option, Wertebereich-Option | Nicht maskierte Namen in Umfragen; ein `step` von 0, negativ oder riesig ließ die Seite hängen; tiefe oder zirkuläre Gruppen beendeten die Anfrage; die Bearbeitung änderte die falsche Multi-Option; importierte Options-IDs kollidierten. | Namen werden maskiert; der Schritt wird validiert; die Tiefe ist begrenzt; die angeklickte Multi-Option wird geändert; importierte Optionen erhalten eindeutige IDs; unbekannte Warenkorbauswahlen werden ignoriert; eine leere Wertebereich-Option übersteht Export und Import unverändert. |
| Autorenliste | `TypeError` bei `count()`/`trim()` bei ungewöhnlicher Eingabe, das Entfernen veränderte das Array innerhalb einer `foreach`-Schleife, ein leeres `data_text` löste beim Export eine Exception aus. | Gesendete Listen werden an einer Stelle gelesen; mehr als 1.000 Autoren werden abgelehnt; in XML ungültige Zeichen werden entfernt; defektes XML wird als leere Liste gelesen; die Autoren-ID wird im Bearbeitungs-Template maskiert. |
| Land | Akzeptierte unbekannte oder numerische Codes; ein einzeln gesendeter Code wurde akzeptiert und dann verworfen. | Nur bekannte Codes; ein einzeln gesendeter Code wird gespeichert. |
| Schlagwort | Wiederholte Schlagwörter wurden doppelt gespeichert. | Einmal gespeichert; ungewöhnliche Eingaben beschädigen das Bearbeitungsformular nicht. |
| Objektbeziehung, Objektbeziehungsliste | Verknüpfte fehlende oder nicht lesbare Objekte; defektes XML. | Nur existierende, lesbare Objekte; defektes XML wird als leer gelesen. |
| ISBN | PHP-Fehler bei unerwarteter Formulareingabe. | Validierungsfehler. |
| Bezeichner | Zwei gemeinsam veröffentlichte Objekte erhielten denselben Wert (der Zähler wurde vor dem Erhöhen gelesen und verließ sich auf eine Tabellensperre, die ohne `LOCK TABLES` nichts bewirkt); unbegrenzte Stellenzahl (`str_pad()` für zwei Milliarden lief aus dem Speicher); negative Stellenzahl `ValueError`. | Der Zähler wird zuerst erhöht und in derselben Transaktion zurückgelesen; Stellenzahl 1 bis 50; Startwert innerhalb von `int(11)`; Texte innerhalb von 50 Zeichen; Listen werden abgelehnt; der Bezeichner wird maskiert. |
| URL | `javascript:`-, `vbscript:`- und `data:`-Adressen wurden zu anklickbaren Links (auch getarnt durch Groß-/Kleinschreibung, Entities, Steuerzeichen oder Zeilenumbrüche); `TypeError` bei Array-Eingabe; Linktext `0` ging beim Import verloren. | Eine solche Adresse wird bei der Validierung und beim Import abgelehnt; früher gespeicherte Werte werden als Text angezeigt, nicht als Link. |
| Bild | Vertraute dem Dateinamen: Ein SVG, eine HTML-Seite oder ein GIF-Header mit nachfolgendem Markup wurde gespeichert, wenn er `.png` hieß; `fromString()` las jeden Pfad, den der Prozess öffnen konnte (`..`, Stream-Wrapper); ein Bild von 60000 mal 60000 Pixeln wurde akzeptiert; gespeicherte Pfade wurden gelöscht oder verschoben, wohin sie auch zeigten; `unserialize()` mit Klassen. | Hochgeladene und eingefügte Dateien müssen dem Inhalt nach Rasterbilder sein (`getimagesize`, kein SVG, kein Markup am Anfang), innerhalb von `[ImageSettings] MaxImagePixels` in `image.ini` (Standard 100 Millionen, vom Code gelesen und nicht in der mitgelieferten `settings/image.ini` aufgeführt; fügen Sie den Schlüssel einem Override hinzu, um ihn zu ändern); `fromString()` liest nur innerhalb der Installation, ihres var-Verzeichnisses oder des temporären Verzeichnisses; fehlgeschlagene Uploads und Array-Einträge sind Validierungsfehler; der Alternativtext ist reiner Text; Dateien werden nur gelöscht oder verschoben, wenn der gespeicherte Pfad ein relativer Pfad innerhalb eines Speicherverzeichnisses ist; die LIKE-Suche hat unter SQLite ihre `ESCAPE`-Klausel; das Ansichts-Template maskiert Größen-, Stil- und Link-Attribute. |
| Datei (Binärdatei) | Ein gespeicherter Name oder MIME-Typ mit `../` reichte über `var/storage/original` hinaus; ein fehlgeschlagener oder unvollständiger Upload wurde akzeptiert und ohne Fehler verworfen; gesendete Arrays. | Pfade aus einer bereinigten MIME-Gruppe und einem bereinigten Dateinamen; Löschen und Papierkorb berühren nie einen Namen mit Verzeichnisanteil; fehlgeschlagene Uploads sind Validierungsfehler; `fromString()` liest `path|original name`. |
| Mediendatei | Breite, Höhe, Qualität, Steuerelemente, Plugin-Seite, Flags, Dateiname und MIME-Typ wurden unmaskiert ausgegeben; Breite und Höhe wurden wie gesendet gespeichert. | Maskiert; nur ganze Pixel; dieselbe Pfadbehandlung wie bei Dateien. |
| Rich Text (XML-Text) | Ein leerer gespeicherter Wert ließ `loadXML()` eine Exception auslösen; defektes XML gab libxml-Warnungen aus, beschädigte den Export und öffnete den Editor mit einem fatalen Fehler; Tabellen ohne Zeilen führten zum Absturz; gespeicherte `javascript:`-, `data:`-, `vbscript:`-Links wurden gerendert; Attributwerte unmaskiert; unbegrenzte Verschachtelung; URL-IDs im SQL nicht gecastet; der Name eines benutzerdefinierten Tags wurde als Template-Pfad verwendet. | Defektes oder leeres XML wird leer gerendert; Skript-URLs werden bei der Ausgabe entfernt und bei der Eingabe abgelehnt (auch getarnte); Attributwerte werden maskiert; die Verschachtelung ist unterhalb der libxml-Grenze gedeckelt; IDs werden gecastet; Namen benutzerdefinierter Tags müssen einfache Namen sein; Arrays werden abgelehnt. Alle 691 gespeicherten Werte wurden mit altem und neuem Code identisch gerendert, umgewandelt und indiziert. |
| Benutzerkonto | Eingegebene Passwörter wurden im Klartext in der Sitzung gehalten (`GeneratedPassword`) und in die Debug-Ausgabe kernel-user geschrieben; E-Mail mit Steuerzeichen; `TypeError` bei Array für Login/E-Mail/Passwort; ein Entwurf in `data_text`, der nicht dem entspricht, was `serializeDraft()` schreibt, überschrieb das Konto mit Nullwerten; `fromString()` verschob bei einer Adresse mit `|` den Hash in die E-Mail; der Paketimport verwarf `is_enabled`; das PDF-Template maskierte für HTML. | Nur ein generiertes Passwort wird in der Sitzung gehalten, damit die Registrierung es anzeigen kann; die Debug-Ausgabe sagt nur, ob eines angegeben wurde; ungültige Eingaben gelten als leer; ein ungültiger Entwurf wird ignoriert. |
| Paket-Datentyp | Der gesendete Paketname wurde ungeprüft an den Repository-Pfad angehängt (`../` suchte eine `package.xml` an anderer Stelle); der gesendete Siteaccess wurde zu dem Verzeichnis, in das eine Sitestyle-`design.ini.append.php` geschrieben wurde. | Namen, die das Repository verlassen (Pfadtrenner, NUL-Byte, `.` oder `..`), werden weder gespeichert noch nachgeschlagen; ein Sitestyle wird nur für `Global` oder einen Siteaccess aus `AvailableSiteAccessList` gespeichert; der Ansichtsmodus ist 0 oder 1; Export und Import übernehmen den gespeicherten Namen. |
| Matrix | Stürzte bei manchen Eingaben ab. | Bleibt mit jeder Eingabe benutzbar, die er erhalten kann. |
| Generischer Leser (`eZDataType::unserializeContentObjectAttribute()`) für Datentypen ohne `object_serialize_map` | Ein Paket, in dem `data-int`, `data-float` oder `data-text` fehlte, ließ ihn eine Eigenschaft von `null` lesen und das Feld mit `0`/leer überschreiben. | Jedes Element wird einzeln nachgeschlagen; ein fehlendes Element lässt das betreffende Feld unverändert. |

## Weitere Änderungen derselben Überarbeitung

- **Anonyme Formulare.** Über ein anonymes Formular veröffentlichte Inhalte
  abonnieren für das gemeinsame anonyme Konto keine Benachrichtigungen mehr.
- **Bildvarianten**, die von mehreren Anfragen gleichzeitig erzeugt werden, werden nie halb
  geschrieben gelesen.
- **Formulare, die Arrays senden**, lassen Anmeldung, „Passwort vergessen“, Passwortänderung,
  Kontoaktivierung oder Suche unter PHP 8 nicht mehr mit einem 500-Fehler scheitern. Die Kontoaktivierung
  castet außerdem die gesendete `MainNodeID` (bisher castete sie das Ergebnis von `hasPostVariable()`).
- **Das Sortieren eines Knotens** (`content/action`) verlangt die Berechtigung, ihn zu bearbeiten, und akzeptiert
  nur ein bekanntes Sortierfeld und eine bekannte Sortierreihenfolge.

## Eine Installation prüfen

1. Legen Sie auf einer Entwicklungsinstallation ein Testobjekt einer Klasse an, die jeden Datentyp verwendet, und geben Sie den feindseligen
   Wert aus der Tabelle ein. Erwartet: eine Validierungsmeldung, kein HTTP 500.
2. Durchsuchen Sie nach einem Crawl das Fehlerprotokoll. Erwartet: keine Treffer aus den oben genannten Datentypen.

   ```bash
   grep -cE "TypeError|Undefined array key" var/log/error.log
   ```

3. Führen Sie die PHPUnit-Suite aus. Sie enthält Tests für den Vergleich der Authentifizierungs-Hashes (einschließlich des Falls
   „true ist kein Passwort“) und für die verzögerte Datenbankeinrichtung der REST-Schnittstelle (`tests/tests/kernel/classes/ezpRestLazyTest.php`).

## Verwandte Seiten

- Spezifikationen: [Sicherheitsvorgaben vom September 2026](sicherheitsvorgaben-2026-09.md), [Sicherheitshärtung 6.0.13](sicherheitshaertung-6.0.13.md), [Die Sicherheitspatches vom August 2026](sicherheitshaertung-2026-08.md)
- Hinweise zum Upgrade: [Härtungsanleitung](../../../eng-us/bc/6.0/hardening.md), [Verhaltensänderungen, 16. bis 30. September 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-09b.md), [RAD-Werkzeuge – Sicherheit](../../../eng-us/bc/6.0/rad-security.md)
- Funktionen: [Seite „Formular abgelaufen“](../../../eng-us/features/6.0/form-expired-page.md) (ein abgelehntes Formular ist eine 403-Seite)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md), [Chronik, 16. bis 30. September 2026](../../../eng-us/history/2026/2026-09b.md), [Februar 2026](../../../eng-us/history/2026/2026-02.md)
