# Inhalte aus einfachen Werten: expContentWriter

Diese Seite richtet sich an PHP-Entwickler, die Inhalte aus Code anlegen, ändern oder entfernen: Importskripte,
Migrationen, Integrationen, Konsolenbefehle und REST-Controller. Seit Exponential 6.0.15 erledigt der Kernel diese
Schreibzugriffe selbst, mit einer Klasse: `expContentWriter` (`kernel/classes/contentwriter/expcontentwriter.php`). Sie
übergeben einfache Werte nach Attributbezeichner; die Klasse baut das Objekt, die Version und die Attribute und
veröffentlicht über dieselbe Operation `content/publish` wie eine Bearbeitung in der Administration. Hinzugefügt am
12. November 2026.

Die Anleitung mit vollständigen Skripten, einer Migration, einem Import und einer Integration ist
[Inhalte aus PHP schreiben mit expContentWriter](../../anleitungen/inhalte-aus-php-schreiben.md).

## Warum es sie gibt

Inhalte aus Code anzulegen ist ein häufiger Bedarf, seit es den Kernel gibt: ein Import aus einem anderen System, ein
nächtlicher Abgleich, ein Formular, das einen Datensatz ablegt, eine App, die einen Artikel einstellt. Bis 6.0.15 hatte
ein Entwickler drei Möglichkeiten, jede mit ihrem Preis:

- **Von Hand bauen**: die Klasse instanziieren, das Objekt speichern, eine Knotenzuweisung anlegen, die Version als
  Entwurf markieren, über die Attribute laufen, für jedes `fromString()` aufrufen, die Veröffentlichungsoperation
  ausführen und ihren Status lesen. Rund vierzig Zeilen, die jedes Projekt neu schrieb und die man leicht auf subtile
  Weise falsch macht (eine vergessene Knotenzuweisung, eine Version im falschen Status, eine nach einer Ausnahme offen
  gebliebene Transaktion).
- **`eZContentFunctions`** (seit den 4.x-Jahren im Kernel): kürzer, aber es übergeht Attributbezeichner, die die Klasse
  nicht hat, verlangt Rich Text als gespeichertes XML und Dateien als Pfade auf dem Server und meldet einen Fehler nur
  als `false`.
- **Eine Extension eines Drittanbieters** wie `nxc_powercontent`: ein weiteres Paket, das installiert, aktuell gehalten
  und geprüft werden will. Die REST API brauchte sie für ihre Schreibzugriffe, also musste jede Installation, die über
  REST schreiben wollte, sie mitführen, und über REST ließen sich Inhalte damit gar nicht ändern.

`expContentWriter` übernimmt im Kernel, wofür diese Extensions installiert wurden. Die REST API
([ezprestapi](erweiterungen/ezprestapi.md) 1.2.9) schreibt über sie und braucht deshalb `nxc_powercontent` nicht mehr,
und sie kann jetzt Inhalte ändern.

Was sich für Entwickler ändert:

- **Weniger Code zu schreiben und zu prüfen**: Ein Anlegen ist ein Aufruf mit einem Array von Werten, ein Ändern ein
  Aufruf mit den Werten, die sich ändern. Die Antwort sagt in Worten, die ein Log oder ein API-Client anzeigen kann,
  was geschehen ist.
- **Nichts zusätzlich zu installieren**: Die Schreibzugriffe sind Teil des Kernels und von seinen Tests und Releases
  abgedeckt.
- **Weniger Arbeit zur Laufzeit**: Jeder Schreibzugriff ist eine Datenbanktransaktion für die Werte und ein Lauf der
  Veröffentlichungsoperation, und die REST API lädt für ihre Schreibzugriffe keine zweite Extension mehr.
- **Die Historie bleibt erhalten**: Ein Ändern veröffentlicht eine neue Version; die Administration zeigt sie in der
  Historie, und eine Redakteurin kann zur vorigen zurückkehren (`nxc_powercontent` änderte die aktuelle Version an Ort
  und Stelle).
- **Die Website bleibt stimmig**: Workflows, Caches, der Suchindex, URL-Aliase und Benachrichtigungen laufen wie bei
  einer Bearbeitung in der Administration.

## Vorher und nachher

Einen Artikel unterhalb von Knoten 2 anlegen, mit Titel, einer Einleitung in HTML und einem Bild von einer Webadresse.

Vorher, von Hand (gekürzt; das HTML musste noch in XML umgewandelt und das Bild vorher heruntergeladen werden):

```php
<?php
$class = eZContentClass::fetchByIdentifier( 'article' );
$parent = eZContentObjectTreeNode::fetch( 2 );
$db = eZDB::instance();
$db->begin();
$object = $class->instantiate( eZUser::currentUserID(), $parent->object()->attribute( 'section_id' ), false, 'eng-US' );
$object->store();
eZNodeAssignment::create( array( 'contentobject_id' => $object->attribute( 'id' ),
                                 'contentobject_version' => $object->attribute( 'current_version' ),
                                 'parent_node' => 2, 'is_main' => 1 ) )->store();
$version = $object->version( $object->attribute( 'current_version' ) );
$version->setAttribute( 'status', eZContentObjectVersion::STATUS_DRAFT );
$version->store();
foreach ( $version->contentObjectAttributes( 'eng-US' ) as $attribute )
{
    switch ( $attribute->attribute( 'contentclass_attribute_identifier' ) )
    {
        case 'title': $attribute->fromString( 'Opening of the new hall' ); break;
        case 'intro': $attribute->fromString( $xmlConvertedFromHtml ); break;   // Ihr eigener Schritt von HTML zu XML
        case 'image': $attribute->fromString( '/tmp/hall.jpg|The new hall' ); break; // Ihr eigener Download-Schritt
    }
    $attribute->store();
}
$db->commit();
$result = eZOperationHandler::execute( 'content', 'publish', array( 'object_id' => $object->attribute( 'id' ),
                                                                     'version' => $object->attribute( 'current_version' ) ) );
// und dann: wurde veröffentlicht, oder hält ein Workflow die Version? was, wenn eine Ausnahme die Transaktion offen ließ?
```

Seit 6.0.15:

```php
<?php
$result = expContentWriter::create( array(
    'parentNodeID'    => 2,
    'classIdentifier' => 'article',
    'languageLocale'  => 'eng-US',
    'attributes'      => array(
        'title' => 'Opening of the new hall',
        'intro' => '<p>Doors open at <strong>ten</strong>.</p>',
        'image' => 'https://media.example.org/hall.jpg|The new hall',
    ),
) );
if ( $result['status'] !== 'published' && $result['status'] !== 'pending' )
    throw new RuntimeException( $result['message'] );
```

Das HTML wird zu Rich Text, das Bild wird von seiner öffentlichen Adresse heruntergeladen und gespeichert, und die
Antwort sagt, ob das Objekt veröffentlicht wurde oder auf einen Workflow wartet.

## Was sie tut

| Methode | Tut | Argumente |
|---|---|---|
| `expContentWriter::create( array $params )` | legt ein Objekt unterhalb eines Knotens an und veröffentlicht es | `parentNodeID` (int), `classIdentifier` (string), `languageLocale` (string, zum Beispiel `eng-US`), `attributes` (Bezeichner => Text); optional `ownerID` (Standard: der aktuelle Benutzer), `sectionID` (Standard: die Sektion des Elternobjekts), `remoteID` |
| `expContentWriter::update( eZContentObject $object, array $values, $languageCode = false )` | veröffentlicht eine neue Version mit den übergebenen Werten; nicht übergebene Attribute behalten ihre Werte; eine Sprache, die das Objekt noch nicht hat, fügt diese Übersetzung hinzu | das Objekt, Bezeichner => Text, ein Locale (Standard: die Hauptsprache des Objekts) |
| `expContentWriter::remove( eZContentObject $object, $moveToTrash = false )` | entfernt das Objekt mit allen Platzierungen und allem darunter, wie es `content/removeobject` nach seinen Prüfungen tut | das Objekt, `true`, um es in den Papierkorb zu legen, statt es zu löschen |

Jede Methode gibt ein Array zurück:

| Schlüssel | Wert |
|---|---|
| `status` | `published` (erledigt; auch bei einem Entfernen), `pending` (ein Workflow hält die neue Version), `invalid` (nichts wurde geschrieben: siehe die Meldung), `failed` (ein Fehler beim Schreiben; die Transaktion wurde zurückgerollt) |
| `message` | `Created`, `Updated`, `Removed`, `Waiting for a workflow` oder der Grund, zum Beispiel `The class article has no attribute teaser.` |
| `object` | das `eZContentObject` (nach dem Veröffentlichen neu geholt) oder `null` |
| `version` | die geschriebene Versionsnummer, `0` bei einem Entfernen oder einer Ablehnung |
| `languageLocale` | nach einem Ändern die geschriebene Sprache (nach einem Anlegen eine leere Zeichenkette) |

## Werte

Werte sind Text, nach Attributbezeichner:

- **Rich Text** (`ezxmltext`): HTML. Ist der Online Editor (`ezoe`) aktiv, wandelt dessen Eingabeparser es um, sodass
  Links auf andere Objekte und eingebettete Objekte zu Relationen des Objekts werden; ohne ihn der vereinfachte
  XML-Eingabeparser.
- **Dateien** (`ezimage`, `ezbinaryfile`, `ezmedia`): `<URL>` oder `<URL>|<Text>`, wobei der Text der Alternativtext
  eines Bildes oder der Dateiname einer Datei ist. Die Datei wird von einer öffentlichen `http`- oder `https`-Adresse
  heruntergeladen. Ein Pfad auf dem Server, andere Schemata (`file://`, `ftp://`, `php://` ...), Zugangsdaten in der URL
  und Adressen, die in private, Loopback-, Link-Local- oder reservierte Netze auflösen (IPv4, IPv6 und IPv4-gemappte
  IPv6-Adressen), werden abgelehnt; Weiterleitungen werden nicht verfolgt; eine Datei darf höchstens 50 MB groß sein
  (`MAX_FILE_BYTES`). Der Download braucht die PHP-Erweiterung `curl`. Ein leerer Dateiwert lässt die Datei, wie sie ist.
- **Jeder andere Datentyp**: seine Textform, genau so, wie `eZContentObjectAttribute::fromString()` sie liest (`1` oder
  `0` für eine Checkbox, ein Zeitstempel für ein Datum, `<Objekt-ID>-<Objekt-ID>` für eine Relationsliste und so weiter).

Ein Wert, der kein Text ist (ein Array, ein Objekt), und ein Bezeichner, den die Klasse nicht hat, werden abgelehnt,
bevor irgendetwas geschrieben wird; die Meldung nennt alle solchen Bezeichner. Zahlen, Wahrheitswerte und `null` werden
angenommen.

## Was bei einem Schreibzugriff läuft

- Die Werte werden in einer Datenbanktransaktion geschrieben; eine Ausnahme rollt sie zurück, die heruntergeladenen
  temporären Dateien werden entfernt, der Fehler wird ins Debug-Log geschrieben, und die Methode antwortet `failed`.
- Danach wird die Version über die Operation `content/publish` veröffentlicht, dieselbe, die die Administration
  ausführt. Workflows (zum Beispiel eine Freigabe), der View-Cache, der Suchindex, URL-Aliase und Benachrichtigungen
  folgen wie bei einer Bearbeitung von Hand. Eine Version, die ein Workflow hält, wird als `pending` beantwortet und in
  `var/log/contentwriter.log` geschrieben.
- Außerhalb des Moduls `content` (eine REST-Anfrage, ein Konsolenskript) sind die Modul-Repositorys unter Umständen
  noch nicht bekannt; die Klasse setzt sie, bevor sie die Operation ausführt. Ohne sie fand der Operation-Handler kein
  `content/publish` und tat nichts.
- `remove()` entfernt jede Platzierung des Objekts und alles darunter; mit `$moveToTrash = true` kommen sie in den
  Papierkorb. Ein Objekt ohne Platzierungen wird endgültig gelöscht.

## Was sie nicht tut

- **Sie prüft keine Berechtigungen.** Wer schreiben darf, entscheidet der Aufrufer. Die REST API prüft mit
  `expRestContentPermission` des Kernels, bevor sie die Klasse aufruft; ein Skript läuft als der Benutzer, den es setzt
  (siehe die [Anleitung](../../anleitungen/inhalte-aus-php-schreiben.md#der-benutzer-unter-dem-ein-skript-läuft)).
- Sie verschiebt nicht, verbirgt nicht und fügt keine Platzierungen hinzu, und sie ändert weder die Sektion noch den
  Eigentümer eines bestehenden Objekts; dafür gibt es die Aufrufe des Kernels
  ([Inhalte aus PHP](../../hilfe/cms-entwickler/inhalte-aus-php.md#verschieben-verbergen-und-entfernen)).
- Sie veröffentlicht nicht ohne Benachrichtigungen; dafür behält `eZContentFunctions` sein Argument `$notify`.

## Wo sie verwendet wird

- In der REST API, `ezprestapi` 1.2.9 und später: `POST /api/ezp/v2/content/node/create`, `POST` oder `PUT
  /api/ezp/v2/content/node/<id>` und `DELETE /api/ezp/v2/content/node/delete/<id>`
  ([REST API v1 und v2](../../anleitungen/rest-api-v1-und-v2.md#21-ändern-und-die-werte-eines-schreibzugriffs)).
- In Ihren Skripten, Migrationen und Integrationen: [Inhalte aus PHP schreiben mit expContentWriter](../../anleitungen/inhalte-aus-php-schreiben.md).

## Tests

`tests/tests/kernel/classes/contentwriter/expContentWriterTest.php` läuft ohne Datenbank: Unbekannte Attribute und
Werte, die kein Text sind, werden mit dem Namen des Attributs abgelehnt (CW-01); eine Datei wird nur über `http` oder
`https` geholt (CW-02); private, Loopback-, Link-Local- und reservierte Adressen werden abgelehnt, auch wenn nur eine
von mehreren Adressen es ist (CW-03); ein Host, der nicht auflöst, und eine URL mit Zugangsdaten werden abgelehnt
(CW-04); das Format eines Dateiwerts (CW-05); die Datei-Datentypen (CW-06).

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/contentwriter/
```

## Verwandte Seiten

- [Inhalte aus PHP schreiben mit expContentWriter](../../anleitungen/inhalte-aus-php-schreiben.md): die Anleitung
- [Hinweise zum Aktualisieren: die REST-Schreibzugriffe über den Kernel, nxc_powercontent nicht mehr nötig](../../bc/6.0/rest-schreibzugriffe-ueber-den-kernel.md)
- [Neu in 6.0.15: Die REST API ändert Inhalte](../../hilfe/neu-in-6-0-15-rest-api.md)
- [ezprestapi](erweiterungen/ezprestapi.md), [nxc_powercontent](erweiterungen/nxc_powercontent.md)
- [Inhalte aus PHP](../../hilfe/cms-entwickler/inhalte-aus-php.md)
- [Inhalte aus PHP anlegen, veröffentlichen und ändern](../../hilfe/erweiterungsentwicklung/teil-8-referenz/inhaltsfunktionen-aus-php.md)
- [Migrationen im Kernel](migrationen.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
