---
title: "Validierung: Eingaben, Datentypen und Escaping"
audience: web-framework-developer, developer
category: web-frameworks
order: 270
summary: Wo in Exponential ohne Validator-Komponente validiert wird - Inhalte prüfen ihre Datentypen beim Speichern durch Redakteure, eigene Formulare und Parameter prüfen Sie in Ihrer View oder Ihrem Dienst, und Ausgaben werden dort escaped, wo sie ausgegeben werden - mit den Mustern, den Zuständen von eZInputValidator und einem Vergleich mit Validierungsbibliotheken.
keywords: Validierung, Validator, Constraints, Datentyp, Escaping, Allow-List, Typumwandlung, validation, constraints, form request, datatype, validateObjectAttributeHTTPInput, eZInputValidator, STATE_ACCEPTED, STATE_INVALID, allow list, casting, escaping, wash, Symfony Validator, Laravel validation, Django forms, Web-Framework-Entwickler, web framework developer
type: help
---
# Validierung: Eingaben, Datentypen und Escaping

Diese Seite richtet sich an Entwickler, die Constraints deklarieren (Attribute des Symfony Validator,
Validierungsregeln in Laravel, Formularfelder in Django, Modellvalidierungen in Rails). Exponential hat keine
allgemeine Validator-Komponente mit deklarativen Regeln. Validiert wird an drei Stellen, und wer weiß, welche davon
gilt, spart viel doppelten Code.

## Inhalt

1. [Drei Stellen](#drei-stellen)
2. [Inhalte: Die Datentypen validieren](#inhalte-die-datentypen-validieren)
3. [Ihre Eingaben: in der View oder im Dienst validieren](#ihre-eingaben-in-der-view-oder-im-dienst-validieren)
4. [Fehler anzeigen](#fehler-anzeigen)
5. [Ausgabe: dort escapen, wo Sie ausgeben](#ausgabe-dort-escapen-wo-sie-ausgeben)
6. [Im Vergleich mit Validierungsbibliotheken](#im-vergleich-mit-validierungsbibliotheken)

## Drei Stellen

| Daten | Validiert durch | Sie schreiben |
|---|---|---|
| Inhalte, die in der Administrationsoberfläche oder einem Bearbeitungsformular im Frontend bearbeitet werden | den Datentyp jedes Attributs mit den Einstellungen des Klassenattributs (Pflichtfeld, Länge, Wertebereiche) | nichts, oder einen eigenen Datentyp |
| Parameter und Formulare Ihrer eigenen Views | Ihre View oder den Dienst, den sie aufruft | Typumwandlungen, Allow-Lists, Prüfungen, Fehlermeldungen |
| API-Eingaben | die REST-Schicht für Inhalte; Ihren Controller oder Ihre Serverfunktion für alles andere | dieselben Prüfungen wie für Formulare |

## Inhalte: Die Datentypen validieren

Wenn Inhalte über die Bearbeitungs-Views gespeichert werden, prüft der Datentyp jedes Attributs die Eingabe in
`validateObjectAttributeHTTPInput()` (`kernel/classes/ezdatatype.php`) und antwortet mit einem Zustand von
`eZInputValidator`: `STATE_ACCEPTED`, `STATE_INTERMEDIATE` oder `STATE_INVALID`, zusammen mit einer Meldung, die das
Bearbeitungsformular neben dem Feld anzeigt. Ob ein Feld ein Pflichtfeld ist, seine Längenbegrenzungen und
Wertebereiche sind Einstellungen des Klassenattributs, die im Klasseneditor gesetzt werden. Eine neue Art von Regel
ist ein neuer Datentyp ([Eigene Datentypen](../ersteller/anwendungsentwickler/eigene-datentypen.md),
[Der Lebenszyklus eines Datentyps](../ersteller/anwendungsentwickler/lebenszyklus-eines-datentyps.md)).

Wenn Code Inhalte mit `eZContentFunctions::createAndPublishObject()` anlegt, werden die Werte in der Form von
`fromString()` des Datentyps übergeben; validieren Sie sie in Ihrem Importcode, bevor Sie die Funktion aufrufen.

## Ihre Eingaben: in der View oder im Dienst validieren

Aus [Eingabevalidierung und Ausgabe-Escaping](../ersteller/anwendungsentwickler/eingabepruefung-und-escaping.md#eingaben-prüfen):

| Eingabe | Validieren |
|---|---|
| IDs und Zahlen | `(int)`, danach eine Bereichsprüfung (`max( 1, min( 100, (int)$limit ) )`) |
| Auswahlwerte | eine Allow-List: `in_array( $sort, array( 'name', 'published' ), true )` |
| Text | Längenbegrenzungen, Trimmen, die für das Feld erlaubten Zeichen |
| E-Mail, URL | `filter_var()`; bei URLs zusätzlich das Schema (nur `https`) |
| Dateien | Typ anhand des Inhalts und Größe, gespeichert über den Kernel |
| Weiterleitungsziele | nur Adressen der eigenen Site ([Sichere Weiterleitungen](../../funktionen/6.0/sichere-weiterleitungen.md)) |

Adressparameter (`$Params`) sind Zeichenketten aus der URL und genauso wenig vertrauenswürdig wie POST-Daten. Legen Sie
die Regeln in eine kleine Validator-Klasse, die Ihre View, Ihr Befehl und Ihre API gemeinsam nutzen, und testen Sie sie
mit PHPUnit ohne Datenbank ([Testen](testen.md)).

```php
$errors = array();
$title = trim( (string)$Module->actionParameter( 'Title' ) );
if ( $title === '' )
    $errors['title'] = ezpI18n::tr( 'extension/expdemo', 'Enter a title.' );
elseif ( mb_strlen( $title ) > 200 )
    $errors['title'] = ezpI18n::tr( 'extension/expdemo', 'The title is too long.' );
```

## Fehler anzeigen

Stellen Sie das Formular mit den Werten, die der Benutzer eingegeben hat, erneut dar, zeigen Sie jeden Fehler neben
seinem Feld und eine Zusammenfassung oben an, und verlinken Sie die Zusammenfassung mit den Feldern, damit Tastatur- und
Screenreader-Nutzer sie finden
([Formulare, CSRF-Schutz und HTTP-Eingaben](../ersteller/anwendungsentwickler/formulare-csrf-und-http-eingaben.md#validierungsfehler-anzeigen)):

```php
$tpl->setVariable( 'errors', $errors );
$tpl->setVariable( 'values', array( 'title' => $title ) );
```

## Ausgabe: dort escapen, wo Sie ausgeben

Templates escapen nicht automatisch: Jeder Wert von Benutzern oder aus der Datenbank läuft durch `|wash`, außer Werten,
die bauartbedingt bereits sicheres HTML sind (`$module_result.content`, die Ausgabe von `attribute_view_gui`,
gerenderter Rich Text) ([Die View-Schicht](templates-im-vergleich-zu-twig-und-blade.md#fünf-unterschiede-die-beißen)). Verwenden Sie in
PHP je nach Kontext `htmlspecialchars( $v, ENT_QUOTES, 'UTF-8' )`, `json_encode()` oder `$db->escapeString()`.

## Im Vergleich mit Validierungsbibliotheken

| Funktion der Bibliothek | Exponential |
|---|---|
| Deklarative Constraints an einem Modell | Einstellungen des Klassenattributs für Inhalte; nichts für Ihre eigenen Tabellen |
| Validierungsgruppen, Kaskadierung | nein |
| Übersetzte Meldungen | `ezpI18n::tr()` in Ihrem Code; Datentypen liefern übersetzte Meldungen mit |
| Darstellung von Formularfehlern | Ihr Template |

Wenn Sie deklarative Regeln bevorzugen, können Sie in Ihrer Erweiterung eine Validierungsbibliothek aus Composer
verwenden; der Kernel hängt von keiner ab.

## Verwandte Seiten

- [Formulare und CSRF-Schutz](formulare-und-csrf-schutz.md)
- [Härtung der Datentyp-Eingaben](../../spezifikationen/6.0/datentyp-eingabehaertung.md)
- [Sicherheit für Anwendungen](../ersteller/anwendungsentwickler/sicherheit-fuer-anwendungen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
