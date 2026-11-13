# Inhalte aus PHP schreiben mit expContentWriter

Diese Anleitung richtet sich an PHP-Entwickler, die Inhalte aus Code anlegen, ändern oder entfernen: ein einmaliges
Skript, ein Import aus einem anderen System, eine Migration, die Inhalte anlegt, eine Integration, die zwei Systeme im
Gleichschritt hält. Sie arbeiten sie von oben nach unten durch. Am Ende haben Sie Inhalte mit jeweils wenigen Zeilen
angelegt, geändert, übersetzt und entfernt, und Sie haben ein vollständiges Importskript, eine Migration und ein Muster
für eine Integration zum Übernehmen.

Sie brauchen Exponential 6.0.15 oder neuer: Die Klasse `expContentWriter` ist ab diesem Release Teil des Kernels. Die
Funktionsseite erklärt, was die Klasse tut und warum es sie gibt:
[Inhalte aus einfachen Werten: expContentWriter](../funktionen/6.0/inhalte-schreiben.md).

## Kurz gesagt

- `expContentWriter::create( array( 'parentNodeID' => ..., 'classIdentifier' => ..., 'languageLocale' => ..., 'attributes' => array( ... ) ) )`
  legt ein Objekt an und veröffentlicht es.
- `expContentWriter::update( $object, array( ... ), 'eng-US' )` veröffentlicht eine neue Version mit den Werten, die
  sich ändern.
- `expContentWriter::remove( $object )` entfernt das Objekt mit allen Platzierungen; `remove( $object, true )` legt es
  in den Papierkorb.
- Werte sind Text nach Attributbezeichner: Rich Text als HTML, eine Datei als öffentliche `https`-Adresse, alles andere
  in der Textform des Datentyps.
- Jeder Aufruf gibt ein Array mit `status` (`published`, `pending`, `invalid`, `failed`), `message`, `object` und
  `version` zurück. Prüfen Sie `status`; nichts wirft eine Ausnahme.
- Die Klasse prüft keine Berechtigungen. Ihr Code läuft als ein Benutzer, den Sie wählen; die REST API prüft für ihre
  Aufrufer.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Der Benutzer, unter dem ein Skript läuft](#der-benutzer-unter-dem-ein-skript-läuft)
3. [Ihr erstes Anlegen](#ihr-erstes-anlegen)
4. [Die Antwort lesen](#die-antwort-lesen)
5. [Ändern und übersetzen](#ändern-und-übersetzen)
6. [Entfernen](#entfernen)
7. [Rich Text, Dateien und andere Werte](#rich-text-dateien-und-andere-werte)
8. [Ein Import, der erneut laufen kann](#ein-import-der-erneut-laufen-kann)
9. [Inhalte in einer Migration](#inhalte-in-einer-migration)
10. [Eine Integration, die zwei Systeme im Gleichschritt hält](#eine-integration-die-zwei-systeme-im-gleichschritt-hält)
11. [Berechtigungen im eigenen Code prüfen](#berechtigungen-im-eigenen-code-prüfen)
12. [Welches Werkzeug für welche Aufgabe](#welches-werkzeug-für-welche-aufgabe)
13. [Fehlersuche](#fehlersuche)

## Bevor Sie beginnen

Prüfen Sie, ob die Klasse vorhanden ist. Im Wurzelverzeichnis der Installation:

```bash
grep -c expContentWriter autoload/ezp_kernel.php
```

Erwartet: `1`. Eine `0` bedeutet, dass der Kernel älter als 6.0.15 ist; aktualisieren Sie ihn zuerst
([Aktualisieren](aktualisieren.md)).

Die Beispiele sind PHP-Dateien, die mit geladenem Kernel laufen. Am schnellsten geht es mit `bin/php/ezexec.php`, das
den Bootstrap erledigt und die Datei ausführt:

```bash
php bin/php/ezexec.php extension/exp_mysite/scripts/first_create.php
```

Legen Sie Skripte in einer eigenen Extension ab (hier `exp_mysite`), nie in den Ordnern des Kernels. Für ein Skript mit
Optionen und Hilfeausgabe schreiben Sie besser einen Konsolenbefehl
([Einen Konsolenbefehl schreiben](../hilfe/php-entwickler/einen-konsolenbefehl-schreiben.md)).

Führen Sie alles zuerst auf einer Kopie der Website aus.

## Der Benutzer, unter dem ein Skript läuft

Ein Skript auf der Kommandozeile läuft als anonymer Benutzer, bis es einen anderen setzt, und die neuen Objekte gehören
dem aktuellen Benutzer. Legen Sie für Importe ein eigenes Konto an (zum Beispiel `exp_importer`), mit genau den Rollen,
die die Arbeit braucht, und melden Sie es am Anfang jedes Skripts an:

```php
<?php
$user = eZUser::fetchByName( 'exp_importer' );
if ( !$user instanceof eZUser )
{
    eZCLI::instance()->error( 'The user exp_importer does not exist' );
    return;
}
eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );
```

`expContentWriter` fragt das Berechtigungssystem nicht: Die Klasse schreibt, was Ihr Code ihr aufträgt. Das ist richtig
für ein Skript, dem Sie vertrauen, und falsch für alles, was im Auftrag eines anderen handelt; siehe
[Berechtigungen im eigenen Code prüfen](#berechtigungen-im-eigenen-code-prüfen).

## Ihr erstes Anlegen

`extension/exp_mysite/scripts/first_create.php` legt einen Ordner unterhalb der Inhaltswurzel (Knoten 2) an:

```php
<?php
$cli = eZCLI::instance();
$user = eZUser::fetchByName( 'exp_importer' );
eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );

$result = expContentWriter::create( array(
    'parentNodeID'    => 2,
    'classIdentifier' => 'folder',
    'languageLocale'  => 'eng-US',
    'remoteID'        => 'exp-mysite-press-folder',
    'attributes'      => array(
        'name'              => 'Press',
        'short_description' => '<p>News for <em>journalists</em>.</p>',
    ),
) );

$cli->output( $result['status'] . ': ' . $result['message'] );
if ( $result['object'] instanceof eZContentObject )
    $cli->output( 'Object ' . $result['object']->attribute( 'id' ) . ', node ' . $result['object']->attribute( 'main_node_id' ) );
```

Führen Sie es aus:

```bash
php bin/php/ezexec.php extension/exp_mysite/scripts/first_create.php
```

Erwartete Ausgabe:

```text
published: Created
Object <object id>, node <node id>
```

Öffnen Sie den Inhaltsbaum in der Administration: Der Ordner **Press** ist da, veröffentlicht, im Besitz von
`exp_importer`, in der Sektion von Knoten 2. Führen Sie das Skript ein zweites Mal aus, antwortet es
`invalid: An object with the remote ID exp-mysite-press-folder exists already.` und schreibt nichts: Die Remote-ID
macht ein Skript sicher wiederholbar.

Die Parameter von `create()`:

| Parameter | Pflicht | Bedeutung |
|---|---|---|
| `parentNodeID` | ja | der Knoten, unterhalb dessen angelegt wird |
| `classIdentifier` | ja | die Klasse, zum Beispiel `folder`, `article` |
| `languageLocale` | ja | eine Sprache der Installation, zum Beispiel `eng-US`, `ger-DE` |
| `attributes` | nein | Attributbezeichner => Text; nicht übergebene Attribute behalten die Vorgaben der Klasse |
| `remoteID` | nein | Ihr Schlüssel für das Objekt; ein zweites Anlegen mit demselben Schlüssel wird abgelehnt |
| `ownerID` | nein | die Objekt-ID des Eigentümers; Standard: der aktuelle Benutzer |
| `sectionID` | nein | Standard: die Sektion des Elternobjekts |

## Die Antwort lesen

Jeder Aufruf gibt dasselbe Array zurück, also behandelt eine Hilfsfunktion alle:

```php
<?php
function exp_mysite_written( array $result, eZCLI $cli )
{
    switch ( $result['status'] )
    {
        case 'published':
            return true;
        case 'pending':
            // ein Workflow (zum Beispiel eine Freigabe) hält die Version; sie wird veröffentlicht, wenn er endet
            $cli->warning( $result['message'] . ' (version ' . $result['version'] . ')' );
            return true;
        case 'invalid':
            // nichts wurde geschrieben: unbekannte Klasse, unbekanntes Attribut, unbekannte Sprache oder unbekannter
            // Elternknoten, ein Wert, der kein Text ist, eine Datei ohne öffentliche Adresse, eine vorhandene Remote-ID
            $cli->error( 'Refused: ' . $result['message'] );
            return false;
        default: // failed
            // der Schreibzugriff schlug mittendrin fehl und wurde zurückgerollt; der Grund steht im Debug-Log
            $cli->error( 'Failed: ' . $result['message'] );
            return false;
    }
}
```

| Schlüssel | Wert |
|---|---|
| `status` | `published`, `pending`, `invalid` oder `failed` |
| `message` | `Created`, `Updated`, `Removed`, `Waiting for a workflow` oder warum nichts geschrieben wurde |
| `object` | das `eZContentObject`, nach dem Veröffentlichen neu geholt, oder `null` |
| `version` | die geschriebene Versionsnummer (`0` bei einem Entfernen oder einer Ablehnung) |
| `languageLocale` | nach einem Ändern die geschriebene Sprache |

`invalid` heißt, dass sich nichts geändert hat; Sie können die Eingabe also korrigieren und es erneut versuchen. Eine
Version mit `pending` wird außerdem in `var/log/contentwriter.log` geschrieben.

## Ändern und übersetzen

`update()` nimmt das Objekt, die Werte, die sich ändern, und eine Sprache. Es veröffentlicht eine **neue Version**: Die
alte bleibt in der Historie, wo eine Redakteurin sie vergleichen oder wiederherstellen kann.

```php
<?php
$object = eZContentObject::fetchByRemoteID( 'exp-mysite-press-folder' );
if ( !$object instanceof eZContentObject )
    return;

// ein Attribut ändern; die anderen behalten ihre Werte
$result = expContentWriter::update( $object, array( 'name' => 'Press and media' ), 'eng-US' );

// eine deutsche Übersetzung hinzufügen: eine Sprache, die das Objekt noch nicht hat, wird zu einer neuen Übersetzung
$result = expContentWriter::update( $object, array(
    'name'              => 'Presse und Medien',
    'short_description' => '<p>Neuigkeiten für die <em>Presse</em>.</p>',
), 'ger-DE' );
```

Ohne drittes Argument wird die Hauptsprache des Objekts geschrieben. Ein leeres Array von Werten wird abgelehnt
(`No attribute values were given; the content is unchanged.`), sodass ein Abgleich, der nichts zu ändern hat, nichts
schreibt.

## Entfernen

```php
<?php
$object = eZContentObject::fetchByRemoteID( 'exp-mysite-press-folder' );
if ( $object instanceof eZContentObject )
{
    $result = expContentWriter::remove( $object, true );   // true: in den Papierkorb; false (Standard): gelöscht
}
```

`remove()` entfernt **jede Platzierung** des Objekts und **alles darunter**, wie **Entfernen** in der Administration mit
allen Platzierungen ausgewählt. Bevor Sie einen Ordner aus einem Skript entfernen, sehen Sie nach, was darunter liegt:
`eZContentObjectTreeNode::subtreeRemovalInformation( array( $nodeID ) )` listet es auf, ohne etwas zu ändern. Große
Teilbäume entfernen Sie besser als Inhaltsjob im Hintergrund ([Inhaltsjobs](../bc/6.0/inhaltsjobs.md)).

## Rich Text, Dateien und andere Werte

| Datentyp | Übergeben | Beispiel |
|---|---|---|
| `ezxmltext` (Rich Text) | HTML | `<p>Doors open at <a href="https://example.org">ten</a>.</p>` |
| `ezimage` | `<öffentliche URL>` oder `<öffentliche URL>\|<Alternativtext>` | `https://media.example.org/hall.jpg\|The new hall` |
| `ezbinaryfile`, `ezmedia` | `<öffentliche URL>` oder `<öffentliche URL>\|<Dateiname>` | `https://files.example.org/r/7.pdf\|Annual report 2026.pdf` |
| `ezstring`, `eztext` | der Text | `Opening of the new hall` |
| `ezboolean` | `1` oder `0` | `1` |
| `ezinteger`, `ezfloat` | die Zahl | `42` |
| `ezdate`, `ezdatetime` | ein Unix-Zeitstempel | `(string)mktime( 10, 0, 0, 11, 2, 2026 )` |
| `ezobjectrelationlist` | Objekt-IDs, mit `-` verbunden | `57-58-91` |
| `ezkeyword` | Schlagwörter, durch Kommas getrennt | `press, opening` |
| jeder andere | der Text, den `fromString()` des Datentyps liest | siehe [Die eZDataType-API](../hilfe/erweiterungsentwicklung/teil-8-referenz/ezdatatype-api.md) |

Was die Klasse damit tut:

- **HTML** wandelt der Eingabeparser des Online Editors um, wenn `ezoe` aktiv ist; ein Link auf ein anderes Objekt oder
  ein eingebettetes Objekt (`<a href="ezobject://57">`, `<a href="eznode://12">`) wird zu einer Relation des Objekts.
  Ohne `ezoe` wandelt der vereinfachte XML-Eingabeparser es um.
- **Dateien** werden von einer öffentlichen `http`- oder `https`-Adresse heruntergeladen, höchstens 50 MB, ohne
  Weiterleitungen zu folgen, und wie ein Upload gespeichert. Ein Pfad auf dem Server wird abgelehnt, ebenso jede Adresse,
  die in ein privates, Loopback-, Link-Local- oder reserviertes Netz auflöst: Ein Wert von außen kann den Server nie
  dazu bringen, seine eigenen Dateien zu lesen oder einen internen Dienst aufzurufen. Liegen Ihre Dateien schon auf dem
  Server, machen Sie sie zuerst unter einer öffentlichen Adresse verfügbar, oder schreiben Sie das Attribut mit
  `eZContentFunctions` und `storage_dir`, das Serverpfade liest ([Welches Werkzeug für welche Aufgabe](#welches-werkzeug-für-welche-aufgabe)).
  Der Download braucht die PHP-Erweiterung `curl`.
- Ein leerer Dateiwert lässt die Datei, wie sie ist; ein leerer Wert jedes anderen Datentyps leert ihn.
- Ein Bezeichner, den die Klasse nicht hat, und ein Wert, der ein Array oder ein Objekt ist, werden abgelehnt, bevor
  irgendetwas geschrieben wird.

## Ein Import, der erneut laufen kann

Das Muster, dem jeder Import folgen sollte: eine Remote-ID pro Datensatz, anlegen, wenn er neu ist, ändern, wenn es ihn
gibt, berichten, was geschehen ist, einen Probelauf anbieten, den Speicher flach halten.

`extension/exp_mysite/scripts/import_events.php` liest `events.csv` (Spalten `id,title,intro_html,start,image_url`) und
hält den Ordner Events damit im Gleichschritt:

```php
<?php
$cli = eZCLI::instance();
$dryRun = in_array( '--dry-run', $_SERVER['argv'], true );
$file = 'var/import/events.csv';
$parentNodeID = 2; // durch die Knoten-ID Ihres Ordners Events ersetzen

$user = eZUser::fetchByName( 'exp_importer' );
eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) );

$handle = fopen( $file, 'r' );
if ( !$handle )
{
    $cli->error( "Cannot read $file" );
    return;
}
fgetcsv( $handle, 0, ',', '"', '\\' ); // die Kopfzeile
$counts = array( 'created' => 0, 'updated' => 0, 'unchanged' => 0, 'refused' => 0 );
$row = 0;
while ( ( $line = fgetcsv( $handle, 0, ',', '"', '\\' ) ) !== false )
{
    ++$row;
    list( $id, $title, $introHtml, $start, $imageUrl ) = $line;
    $remoteID = 'exp-mysite-event-' . $id;
    $values = array(
        'title'      => $title,
        'intro'      => $introHtml,
        'start_date' => (string)strtotime( $start ),
        'image'      => $imageUrl !== '' ? $imageUrl . '|' . $title : '',
    );

    $object = eZContentObject::fetchByRemoteID( $remoteID );
    if ( $dryRun )
    {
        $cli->output( ( $object ? 'would update ' : 'would create ' ) . $remoteID );
        continue;
    }
    if ( $object instanceof eZContentObject )
    {
        // nur schreiben, was sich geändert hat, damit ein unveränderter Datensatz keine neue Version erzeugt
        $dataMap = $object->dataMap();
        $changed = array();
        foreach ( array( 'title', 'start_date' ) as $identifier )
            if ( $dataMap[$identifier]->toString() !== $values[$identifier] )
                $changed[$identifier] = $values[$identifier];
        if ( !$changed )
        {
            ++$counts['unchanged'];
            continue;
        }
        $result = expContentWriter::update( $object, $changed, 'eng-US' );
        $key = 'updated';
    }
    else
    {
        $result = expContentWriter::create( array( 'parentNodeID' => $parentNodeID, 'classIdentifier' => 'exp_event',
                                                   'languageLocale' => 'eng-US', 'remoteID' => $remoteID,
                                                   'attributes' => $values ) );
        $key = 'created';
    }
    if ( $result['status'] === 'published' || $result['status'] === 'pending' )
        ++$counts[$key];
    else
    {
        ++$counts['refused'];
        $cli->error( "Row $row ($remoteID): " . $result['message'] );
    }
    if ( $row % 100 === 0 )
        eZContentObject::clearCache(); // hält den Speicher bei langen Läufen flach
}
fclose( $handle );
$cli->output( json_encode( $counts ) );
```

```bash
php bin/php/ezexec.php extension/exp_mysite/scripts/import_events.php --dry-run   # was es tun würde
php bin/php/ezexec.php extension/exp_mysite/scripts/import_events.php             # der Import
```

Erwartet am Ende eines ersten Laufs: `{"created":<rows>,"updated":0,"unchanged":0,"refused":0}`; ein zweiter Lauf mit
derselben Datei: `{"created":0,"updated":0,"unchanged":<rows>,"refused":0}`.

Der Vergleich schreibt nur, was sich geändert hat, sodass ein nächtlicher Lauf für unveränderte Datensätze keine
Versionen hinzufügt. Hier vergleicht er die einfachen Attribute; vergleichen Sie auch den Rich Text, wenn Ihre Quelle
ihn ändert. Eine abgelehnte Zeile (ein unbekanntes Attribut, eine Bildadresse in einem privaten Netz) wird mit ihrer
Zeilennummer gemeldet, und die anderen Zeilen laufen weiter.

Für sehr große Importe lesen Sie außerdem [Inhalte importieren](../hilfe/cms-entwickler/inhalte-importieren.md) (die
vorhandenen Importwerkzeuge, darunter `sqliimport`) und
[Große Datenmigrationen](../hilfe/migrationen/rezepte/grosse-datenmigrationen.md).

## Inhalte in einer Migration

Eine Migration ist der richtige Ort für Inhalte, die jede Installation einer Website haben muss: ein Ordner, den die
Templates erwarten, eine Landingpage, eine Reihe von Kategorien. Während `up()` und `down()` ist der Benutzer des Laufs
angemeldet ([PHP-Migrationen](migrationen-php.md#der-kontext)), also ist kein Code für den Benutzer nötig:

```php
<?php
// description: The folder Press releases below the content root
namespace Site\Migrations;

final class PressReleasesFolder20261112100000 extends \expMigrationPhp
{
    const REMOTE_ID = 'site-press-releases-folder';

    public function plan( $context )
    {
        return array( 'publish the folder Press releases below node 2 (remote id ' . self::REMOTE_ID . ')' );
    }

    public function detect( $context )
    {
        return \eZContentObject::fetchByRemoteID( self::REMOTE_ID ) instanceof \eZContentObject ? \expMigration::DONE : null;
    }

    public function up( $context )
    {
        $result = \expContentWriter::create( array(
            'parentNodeID'    => 2,
            'classIdentifier' => 'folder',
            'languageLocale'  => 'eng-US',
            'remoteID'        => self::REMOTE_ID,
            'attributes'      => array( 'name' => 'Press releases' ),
        ) );
        if ( $result['status'] !== 'published' )
            throw new \RuntimeException( 'the folder Press releases was not published: ' . $result['message'] );
        $context->setReference( 'press_releases_node', (int)$result['object']->attribute( 'main_node_id' ) );
        return array( 'published the folder Press releases (object ' . $result['object']->attribute( 'id' ) . ')' );
    }

    public function down( $context )
    {
        $object = \eZContentObject::fetchByRemoteID( self::REMOTE_ID );
        if ( !$object instanceof \eZContentObject )
            return array( 'the folder Press releases was already gone' );
        \expContentWriter::remove( $object, true );
        return array( 'moved the folder Press releases to the trash' );
    }
}
```

Die Ausnahme bei einem anderen Status als `published` lässt den Runner die Migration zurückrollen und melden. Eine
Migration erwartet, dass die Inhalte nach ihrem Ende da sind; eine Antwort `pending` zählt hier deshalb als Fehler.

Migrationen können Inhalte auch ohne PHP anlegen, mit dem YAML-Schritt `content`
([Die YAML-Schritte](migrationen-yaml-schritte.md)). Nehmen Sie den YAML-Schritt für einfache Inhalte und PHP, wenn die
Werte berechnet werden.

## Eine Integration, die zwei Systeme im Gleichschritt hält

Eine Integration bekommt meist einen Datensatz aus einem anderen System (einen Produktfeed, eine Liste von
Veranstaltungen aus einem Buchungssystem) und muss die Website daran angleichen. Halten Sie die Zuordnung in einer
Klasse, damit der Code, der die Daten empfängt (ein Cronjob-Teil, ein Konsolenbefehl, eine Modul-View), klein bleibt:

```php
<?php
class expMySiteEventSync
{
    /**
     * @param array $record der Datensatz des anderen Systems: id, title, description (HTML), cancelled
     * @return array die Antwort von expContentWriter
     */
    public static function apply( array $record, $parentNodeID )
    {
        $remoteID = 'booking-event-' . (int)$record['id'];
        $object = eZContentObject::fetchByRemoteID( $remoteID );
        if ( !empty( $record['cancelled'] ) )
        {
            return $object instanceof eZContentObject
                ? expContentWriter::remove( $object, true )
                : array( 'status' => 'published', 'message' => 'Nothing to remove', 'object' => null, 'version' => 0 );
        }
        $values = array( 'title' => (string)$record['title'], 'intro' => (string)$record['description'] );
        if ( $object instanceof eZContentObject )
            return expContentWriter::update( $object, $values );
        return expContentWriter::create( array( 'parentNodeID' => (int)$parentNodeID, 'classIdentifier' => 'exp_event',
                                                'languageLocale' => 'eng-US', 'remoteID' => $remoteID,
                                                'attributes' => $values ) );
    }
}
```

Legen Sie die Klasse in `extension/exp_mysite/classes/` ab und erzeugen Sie die Autoloads neu
(`php bin/php/ezpgenerateautoloads.php -e`).

**Wenn das andere System Sie aufruft** statt umgekehrt, brauchen Sie gar kein PHP zu schreiben: Die REST API erledigt
dieselben Schreibzugriffe über HTTP mit einem persönlichen API-Schlüssel oder einem OAuth2-Token und prüft die
Berechtigungen des Aufrufers:

```bash
curl -s -X PUT -H "Authorization: Bearer $EXP_API_KEY" -H "Content-Type: application/json" \
     -d '{"languageLocale":"eng-US","title":"Opening moved to eleven"}' \
     https://www.example.com/api/ezp/v2/content/node/<node id>
# {"message":"Updated","objectId":<object id>,"nodeId":<node id>,"version":<version>,"languageLocale":"eng-US"}
```

Siehe [REST API v1 und v2](rest-api-v1-und-v2.md#21-ändern-und-die-werte-eines-schreibzugriffs) und
[Neu in 6.0.15: Die REST API ändert Inhalte](../hilfe/neu-in-6-0-15-rest-api.md).

## Berechtigungen im eigenen Code prüfen

Wenn Ihr Code im Auftrag eines angemeldeten Benutzers schreibt (eine Modul-View, eine ezjscore-Funktion, ein eigener
REST-Provider), fragen Sie zuerst das Berechtigungssystem, so wie es das Modul `content` und die REST API tun.
`expRestContentPermission` des Kernels hat die Prüfungen:

```php
<?php
$parent = eZContentObjectTreeNode::fetch( $parentNodeID );
$class = eZContentClass::fetchByIdentifier( 'exp_event' );
if ( !$parent || !$class || !expRestContentPermission::createAllowed( $parent, $class, 'eng-US' ) )
    return array( 'error' => 'You may not create an event here.' );

$result = expContentWriter::create( array( 'parentNodeID' => $parentNodeID, 'classIdentifier' => 'exp_event',
                                           'languageLocale' => 'eng-US', 'attributes' => $values ) );
```

| Vor | Prüfung |
|---|---|
| `create()` | `expRestContentPermission::createAllowed( $parentNode, $class, $locale )` |
| `update()` | `expRestContentPermission::editAllowed( $node, $locale )` |
| `remove()` | `expRestContentPermission::removeAllowed( $node )` für jede Platzierung |

Eine Modul-View, die Inhalte ändert, nimmt außerdem nur POST an und prüft das Formular-Token
([CSRF und ezformtoken](../hilfe/php-entwickler/csrf-und-ezformtoken.md)).

## Welches Werkzeug für welche Aufgabe

| Sie wollen | Nehmen Sie |
|---|---|
| Inhalte in PHP aus Werten anlegen, ändern oder entfernen | `expContentWriter` (diese Anleitung) |
| **ohne** Benachrichtigungen veröffentlichen oder Dateien aus Pfaden auf dem Server lesen | `eZContentFunctions::createAndPublishObject()` / `updateAndPublishObject()` mit `$notify = false` und `storage_dir` ([Referenz](../hilfe/erweiterungsentwicklung/teil-8-referenz/inhaltsfunktionen-aus-php.md)) |
| verschieben, verbergen, Platzierungen hinzufügen, Sektion oder Eigentümer ändern | die Aufrufe des Kernels ([Inhalte aus PHP](../hilfe/cms-entwickler/inhalte-aus-php.md#verschieben-verbergen-und-entfernen)) |
| Inhalte, die jede Installation einer Website haben muss | eine Migration, YAML-Schritt `content` oder PHP ([Migrationen](migrationen.md)) |
| ein anderes System über HTTP schreiben lassen | die REST API ([REST API v1 und v2](rest-api-v1-und-v2.md)) oder GraphQL-Mutationen ([GraphQL](../funktionen/6.0/graphql.md)) |
| eine große Menge aus einem bekannten Format importieren | zuerst die vorhandenen Werkzeuge ([Inhalte importieren](../hilfe/cms-entwickler/inhalte-importieren.md)) |

## Fehlersuche

| Meldung | Ursache | Was tun |
|---|---|---|
| `The parent node <id> does not exist.` | falsche `parentNodeID` | den Knoten über die Remote-ID suchen statt über die Nummer; IDs unterscheiden sich zwischen Installationen |
| `The class <x> does not exist.` | falscher Bezeichner | **Setup** > **Klassen** zeigt die Bezeichner |
| `The language <x> is not a language of this installation.` | ein nicht hinzugefügtes Locale | unter **Setup** > **Sprachen** hinzufügen oder ein vorhandenes nehmen |
| `The class <x> has no attribute <a>, <b>.` | Bezeichner, die nicht in der Klasse sind | mit der Klassendefinition vergleichen; nichts wurde geschrieben |
| `The value of <a> must be text.` | ein Array oder Objekt als Wert | in die Textform des Datentyps umwandeln |
| `An object with the remote ID <x> exists already.` | ein Anlegen für einen vorhandenen Datensatz | ihn holen und stattdessen `update()` aufrufen |
| `The value of <a> must be a public http or https address of the file.` | ein Serverpfad, ein anderes Schema, Zugangsdaten oder eine Adresse in einem privaten Netz | eine öffentliche `https`-Adresse angeben |
| `The file of <a> could not be downloaded (at most 50 MB).` | nicht erreichbar, eine Weiterleitung, zu groß oder kein `curl` | die Adresse mit `curl -I` prüfen; die endgültige Adresse angeben, nicht eine, die weiterleitet |
| `Waiting for a workflow` (`pending`) | ein Workflow hält die Version | sie wird veröffentlicht, wenn der Workflow endet; siehe **Setup** > **Workflows** und `var/log/contentwriter.log` |
| `The content could not be created.` / `updated.` (`failed`) | ein Fehler beim Schreiben | der Grund steht im Debug-Log (`var/log/error.log`); der Schreibzugriff wurde zurückgerollt |
| Neue Objekte gehören Anonymous, oder das Veröffentlichen wird abgelehnt | kein Benutzer angemeldet | das Importkonto anmelden ([Der Benutzer, unter dem ein Skript läuft](#der-benutzer-unter-dem-ein-skript-läuft)) |

## Verwandte Seiten

- [Inhalte aus einfachen Werten: expContentWriter](../funktionen/6.0/inhalte-schreiben.md): die Funktionsseite
- [Hinweise zum Aktualisieren: die REST-Schreibzugriffe über den Kernel, nxc_powercontent nicht mehr nötig](../bc/6.0/rest-schreibzugriffe-ueber-den-kernel.md)
- [Inhalte aus PHP](../hilfe/cms-entwickler/inhalte-aus-php.md): Inhalte lesen, Listen und die anderen Aufrufe des Kernels
- [Ein Importskript schreiben](../hilfe/integratoren/ein-importskript-schreiben.md)
- [PHP-Migrationen](migrationen-php.md), [Migrationen](migrationen.md)
- [REST API v1 und v2](rest-api-v1-und-v2.md), [Persönliche API-Schlüssel](api-schluessel.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
