# Zugriffs- und Ansichts-Cache-Filter für Extensions

Lesen Sie diese Seite, wenn Sie eine Extension bauen, die ändert, wer eine Datei herunterladen, einen Entwurf
bearbeiten oder ein Kollaborationselement öffnen darf, die entscheidet, welche Veröffentlichungen zu Benachrichtigungen
werden, oder die eigene Ansichts-Cache-Dateien braucht. Jeder dieser Fälle war früher ein Patch an einer Kernel-Datei;
jetzt fragt der Kernel einen Filter von `ezpEvent`, und die Extension antwortet.

## Kurz gefasst

| Filter | Wert, den der Listener erhält | Kontext | Abgefragt von |
|---|---|---|---|
| `content/view/cachekeys` | die benannten Schlüssel eines Ansichts-Cache-Dateinamens | Benutzer, Knoten, Ansichtsmodus, Sprache, Offset, Layout, Ansichtsparameter, Cache-Tweak | `eZNodeviewfunctions::generateViewCacheFile()` |
| `content/download/access` | die Antwort des Kernels, `true` oder `false` | Objekt, Attribut, Version | `content/download` |
| `content/edit/access` | die Bearbeitungsantwort des Kernels, `true` oder `false` | Objekt, Version (oder `null`), Benutzer-ID, Sprache | `eZContentObject::editAccess()` und `::filterEditAccess()`, siehe [Wo der Bearbeitungszugriff abgefragt wird](#wo-der-bearbeitungszugriff-abgefragt-wird) |
| `collaboration/item/access` | ob der Benutzer beteiligt ist, `true` oder `false` | Element, Benutzer | `collaboration/item` und `collaboration/action` |
| `content/notification/create` | `true`, `false` bei „Ohne Benachrichtigung veröffentlichen“ | Objekt-ID, Version | `eZContentOperationCollection::createNotificationEvent()` (die Veröffentlichungsoperation) |

Ein Listener gibt den Wert zurück, geändert oder nicht. Ohne Listener funktioniert alles wie bisher.

## Was ein Listener eines Zugriffsfilters tun kann

Die vier Zugriffsfilter (`content/download/access`, `content/edit/access`, `collaboration/item/access`,
`content/notification/create`) funktionieren auf dieselbe Weise:

- Der Listener erhält die Antwort des Kernels als erstes Argument und gibt die Antwort zurück, die gilt.
- **Nur `true` erlaubt.** `false`, `null`, `1`, `'yes'` oder ein Objekt verweigern alle. Ein Listener, der sein
  `return` vergisst, verweigert daher allen den Zugriff: Geben Sie das erste Argument zurück, wenn der Listener keine
  eigene Meinung hat.
- Ein Listener kann **einschränken** (`false` zurückgeben, wo der Kernel erlaubt hat) und **erweitern** (`true`
  zurückgeben, wo der Kernel verweigert hat). Das Erweitern ist der Zweck dieser Filter (ein Genehmiger, der nur die
  Version lesen darf, erhält ihre Datei; ein weiterer Bearbeiter eines Entwurfs kommt hinein), deshalb verbietet der
  Kernel es nicht. Schreiben Sie einen erweiternden Listener so, dass er `true` nur für den Fall zurückgibt, um den es
  ihm geht, und sonst das erste Argument.
- Mehrere Listener laufen in der Reihenfolge ihrer Registrierung, und jeder erhält, was der vorherige zurückgegeben
  hat. Ein Listener, der nach einem einschränkenden erweitert, hebt die Einschränkung auf. Wenn es beide gibt,
  registrieren Sie den einschränkenden zuletzt, oder lassen Sie den erweiternden den Fall selbst prüfen, statt jedes
  `false` zu überschreiben.
- Einige Prüfungen kommen vor dem Filter, und kein Listener kann sie ändern:
  - `content/download`: Das Attribut muss zum Objekt gehören, und zur angefragten Version. Für eine Datei eines
    anderen Objekts oder einer anderen Version wird gar kein Listener gefragt.
  - `collaboration/action`: Das Element muss existieren und vom Typ sein, den das Formular nennt.
  - Die Bearbeitungsansicht schickt jemanden, der einen Entwurf nicht erstellt hat, weiterhin zu `content/history`,
    wo er einen eigenen Entwurf anlegen kann; ein Listener kann niemanden den Entwurf eines anderen Benutzers direkt
    bearbeiten lassen.
  - Das Genehmigen oder Ablehnen eines Elements erfordert weiterhin die Rolle des Genehmigers; der Filter entscheidet
    nur, wer ein Element öffnen und kommentieren darf.
- Wenn ein Listener die Antwort des Kernels ändert, meldet ein Debug-Hinweis dies (`A listener of content/edit/access allowed
  editing object 57 for user 14 (the kernel refused it)`), sodass sich eine überraschende Antwort in der Debug-Ausgabe
  nachverfolgen lässt.

## Einen Listener registrieren

In `extension/<name>/settings/site.ini.append.php`:

```ini
[Event]
Listeners[]=content/edit/access@myExtCoEditors::editAccess
Listeners[]=content/notification/create@myExtNotificationFilter::create
```

`[Event] Listeners[]` werden an Web-Anfragen angehängt. Eine Veröffentlichung, die ein Cronjob-Teil abschließt (ein
Workflow, der auf eine Genehmigung gewartet hat, der asynchrone Publisher), fragt `content/notification/create` auf der
Kommandozeile ab. Fügen Sie den Listener auch zu `[RunnableSettings] Listeners[]` hinzu; diese Liste wird zusammen mit
den Extension-Einstellungen an Cronjob-Teile angehängt. Bei einer Web-Anfrage läuft ein Listener, der in beiden Listen
steht, zweimal; schreiben Sie ihn daher so, dass er beim zweiten Aufruf dieselbe Antwort gibt. Die persistenten Worker
von Velocity hängen die Listener bei jeder Anfrage erneut an und ersetzen dabei die zuvor angehängten, sodass sie sich
nicht anhäufen.

Der Assistent für Einstellungs-Extensions (**Setup > RAD**, Einstellungs-Extension, Teil „Event-Listener“) schreibt die
Listener-Klasse für Sie: Für jeden dieser Filter benennt die Methode die Argumente, die sie erhält, und enthält ein
auskommentiertes Beispiel, das seine Regel aus der eigenen Gruppe `site.ini [<listener class>]` der Extension liest,
die der Assistent ebenfalls schreibt.

## Die Filter

### `content/view/cachekeys`

Der Ansichts-Cache hält eine Datei pro Schlüsselsatz. Die Schlüssel sind benannt (die Dateinamen haben sich dadurch
nicht geändert, weil der Hash nur die Werte liest):

| Schlüssel | Vorhanden |
|---|---|
| `node_id`, `viewmode`, `language`, `offset`, `layout`, `access_path` | immer |
| `userroles`, `userlimitedlist`, `discountlist` | außer wenn der Cache-Tweak `ignore_userroles`, ... angibt |
| `siteaccess_type` | bei einem Siteaccess, der über die URI zugeordnet wird |
| `protocol` | mit dem Tweak `protocol` |
| `viewparameters`, `userpreferences` | wenn die Ansicht sie hat |

Das zweite Argument ist ein Array mit `user`, `node_id`, `view_mode`, `language`, `offset`, `layout`,
`view_parameters` und `view_cache_tweak`. Fügen Sie einen Schlüssel hinzu, wenn die Seite von etwas abhängt, das die
Rollen nicht zeigen (eine pro Benutzer gespeicherte Rechtematrix); lassen Sie Schlüssel weg bei einer Klasse, deren
Seiten für alle gleich sind.

- `node_id`, `viewmode`, `language`, `offset` und `layout` bleiben immer erhalten: Ein Listener, der einen davon
  weglässt, bekommt ihn an seiner Stelle zurück, mit einer Warnung, da sich ohne sie zwei Ansichten eines Knotens eine
  Datei teilen würden.
- Werte müssen Strings, Zahlen, Booleans oder `null` sein. Ein Array oder ein Objekt wird mit einer Warnung
  weggelassen.
- Ein Listener, der kein Array zurückgibt, ändert nichts.

```php
class myExtViewCacheKeys
{
    public static function keys( $keys, $context )
    {
        $keys['myext_matrix'] = myExtMatrix::keyFor( $context['user'] );
        return $keys;
    }
}
```

Ein neuer Schlüssel erzeugt neue Dateinamen, sodass nach dem Hinzufügen oder Ändern eines Listeners die alten Dateien
einfach nicht mehr gelesen werden; leeren Sie den Ansichts-Cache, um den Platz freizugeben
(`php bin/php/ezcache.php --clear-tag=content`).

### `content/download/access`

`content/download` prüft zuerst, dass das Attribut zum Objekt und zur angefragten Version gehört
(`Download::attributeBelongs()`); andernfalls wird kein Listener gefragt. Dann entscheidet der Kernel: Der Benutzer
muss das Objekt und einen seiner Orte lesen dürfen (oder ein Objekt, das darauf verweist, bei einem Objekt ohne Ort),
und die Version, wenn sie nicht die veröffentlichte ist. Der Filter erhält diese Antwort zusammen mit dem Objekt, dem
Attribut und der Versionsnummer.

Ein Listener, der einen Genehmiger die Datei einer Version herunterladen lässt, die er lesen darf:

```php
public static function download( $allowed, $object, $attribute, $version )
{
    if ( $allowed )
        return true;
    $versionObject = $object->version( $version );
    return $versionObject instanceof eZContentObjectVersion && $versionObject->canVersionRead();
}
```

Ein Listener, der die Dateien einiger Klassen zurückhält, gleich was die Rollen sagen, mit den Klassen in seinen
Einstellungen:

```php
public static function download( $allowed, $object, $attribute, $version )
{
    $ini = eZINI::instance();
    if ( $allowed && $ini->hasVariable( 'MyExtDownloads', 'NoDownloadClasses' ) &&
         in_array( $object->attribute( 'class_identifier' ), $ini->variable( 'MyExtDownloads', 'NoDownloadClasses' ) ) )
        return false;
    return $allowed;
}
```

Eine Verweigerung antwortet mit dem Fehler des Kernels (Zugriff verweigert oder nicht verfügbar bei einem Ort oder
einer Version, die der Benutzer nicht lesen darf); eine Verweigerung durch einen Listener, wo der Kernel erlaubt hat,
antwortet mit Zugriff verweigert.

### `content/edit/access`

`eZContentObject::editAccess( $version = null, $language = false )` geht von `canEdit()` aus und reicht die Antwort mit
dem Objekt, der Version (`null`, wenn es noch keine gibt), der ID des aktuellen Benutzers und der Sprache (eine leere
Sprache gilt als keine) durch den Filter. `eZContentObject::filterEditAccess( $allowed, $version, $language )` reicht
jede andere Bearbeitungsantwort des Kernels durch denselben Filter; so werden die Bearbeitungsprüfung einer einzelnen
Version und die REST-Prüfung gleich entschieden. Ohne Listener fragt keine der beiden den aktuellen Benutzer ab.

Ein Listener für weitere Bearbeiter eines Entwurfs:

```php
public static function editAccess( $allowed, $object, $version, $userID, $language )
{
    return $allowed || ( $version && myExtCoEditors::isEditor( $version, $userID ) );
}
```

Ein Listener, der Objekte in einem Zustand einfriert (einem Objektzustand `locked`), gleich was die Rollen sagen:

```php
public static function editAccess( $allowed, $object, $version, $userID, $language )
{
    if ( $allowed && in_array( myExtStates::lockedStateID(), $object->stateIDArray() ) )
        return false;
    return $allowed;
}
```

#### Wo der Bearbeitungszugriff abgefragt wird

| Wo | Was |
|---|---|
| `content/edit` | jede Bearbeitungsprüfung: ein neuer Entwurf, die Wahl einer Sprache, die Wahl zwischen Entwürfen, ein neuer Entwurf in der Sprache, die abschließende Prüfung und die Bearbeitungsprüfung einer einzelnen Version (`filterEditAccess()`) |
| `content/history` | ob sich der Verlauf öffnet (wer ihn bearbeiten darf, darf ihn öffnen, ohne ihn zu lesen), ob er Bearbeiten anbietet, die Kopie einer Version in ihrer Sprache und welche Versionen seine Bearbeiter sehen (auch eine abgelehnte) |
| `content/removeeditversion` | das Entfernen eines Entwurfs (bei einem nie veröffentlichten Objekt darf es auch, wer es dort anlegen darf) |
| `content/versionview` | die Schaltflächen Bearbeiten und Veröffentlichen der Vorschau (sie benötigen auch den Ersteller der Version) |
| `content/multiedit` | jedes Objekt und ein Entwurf, den es wiederverwendet |
| REST-Schnittstelle | `expRestContentPermission::editAllowed()`: das `canEdit()` des Knotens und die Sprache, über `filterEditAccess()` |
| Online-Editor | die Upload- und Tag-Dialoge von `ezoe` |

Nicht abgefragt: Prüfungen eines Ortes statt des Inhalts (Sortieren, Prioritäten, Verschieben, Vertauschen, Hinzufügen
oder Entfernen von Orten), die Übersetzungseinstellungen eines Objekts (`UpdateInitialLanguage`,
`UpdateAlwaysAvailable`), Benutzerkonten (`user/edit`, `user/selfedit`) und das Template-Attribut `$object.can_edit`,
das Links und Schaltflächen anzeigt. Ein Listener, der den Bearbeitungszugriff erweitert, bringt daher das Bearbeiten
selbst zum Laufen, doch die Schaltfläche Bearbeiten eines Templates, das `can_edit` liest, bleibt verborgen; geben Sie
solchen Benutzern einen eigenen Link, zum Beispiel aus dem Kollaborationselement.

### `collaboration/item/access`

`collaboration/item` öffnet ein Element für seine Beteiligten, und `collaboration/action` handelt darauf (ein
Kommentar, Genehmigen, Ablehnen) nur für jemanden, der es öffnen darf. Der Filter erhält, ob der aktuelle Benutzer
beteiligt ist, zusammen mit dem Element und dem Benutzer; ein Listener kann einen Aufseher einer Genehmigung
hereinlassen oder einen Beteiligten draußen halten. Das Template erhält `is_participant`. Nur ein Beteiligter kann das
Element in einer Gruppe des Posteingangs ablegen. Genehmigen oder Ablehnen erfordert weiterhin die Rolle des
Genehmigers, über die der Kollaborations-Handler des Elements entscheidet.

```php
public static function itemAccess( $allowed, $item, $user )
{
    $ini = eZINI::instance();
    if ( !$allowed && $ini->hasVariable( 'MyExtCollaboration', 'SupervisorUserIDs' ) &&
         in_array( (string)$user->attribute( 'contentobject_id' ), $ini->variable( 'MyExtCollaboration', 'SupervisorUserIDs' ), true ) )
        return true;
    return $allowed;
}
```

### `content/notification/create`

Die Veröffentlichungsoperation erzeugt das Ereignis, das die Benachrichtigungs-Handler in E-Mails umwandeln. Der Filter
erhält `true` (`false` bei einer Veröffentlichung ohne Benachrichtigung und bei einer Klasse, die `notification.ini
[NotificationSettings] NotificationFilterByClassIdentifier` auslässt), die Objekt-ID und die Version; alles außer
`true` lässt das Ereignis weg. Für eine feste Liste von Klassen genügt die Einstellung; ein Listener kann nach
beliebigen anderen Kriterien entscheiden. Ein Listener, der nur einige Klassen durchlässt:

```php
public static function create( $create, $objectID, $version )
{
    $object = eZContentObject::fetch( $objectID );
    return $create && $object && in_array( $object->attribute( 'class_identifier' ), array( 'article', 'file' ) );
}
```

#### Ohne Benachrichtigung veröffentlichen

„Ohne Benachrichtigung veröffentlichen“ (`notification.ini [NotificationSettings] PublishWithoutNotification` und die
Richtlinie `content/publish_without_notification`) führt die Veröffentlichungsoperation mit `notify` auf `false` aus,
und der Filter erhält dann `false`. Ein Listener, der unabhängig davon `true` zurückgibt, erzeugt das Ereignis trotzdem
und übergeht den Redakteur: Reichen Sie `$create` weiter, wenn Sie das nicht beabsichtigen. Die ganze Funktion:
[Ohne Benachrichtigung veröffentlichen](veroeffentlichen-ohne-benachrichtigung.md).

## So funktioniert es

Alle fünf sind Aufrufe von `ezpEvent::filter()`. `ezpEvent::hasListeners( $name )` gibt an, ob jemand zuhört, für
einen Aufrufer, dessen Kontext aufwendig zu sammeln ist. Die Download- und Kollaborationsprüfungen liegen in statischen
Methoden, sodass sie für sich aufgerufen und getestet werden können: `\Exponential\View\Kernel\Content\Download::access()`,
`::kernelAccess()` und `::attributeBelongs()`, `\Exponential\View\Kernel\Collaboration\Item::access()` und
`\Exponential\View\Kernel\Collaboration\Action::access()`.

Es gibt keinen Filter für den Lesezugriff. `canRead()` ist nur eine der Stellen, die entscheiden, was ein Benutzer
sieht: Listen-Fetches, die Suche und das Baummenü filtern in SQL nach den Rolleneinschränkungen, und der
Ansichts-Cache ist nach den Rollen verschlüsselt. Ein Lesefilter würde einen Knoten auf seiner eigenen Seite lesbar
machen und in jeder Liste fehlen lassen. Verwenden Sie stattdessen Rollen und `content/view/cachekeys`.

Tests (ohne Datenbank): `expPermissionHooksTest` (die fünf Filter, die Bearbeitungsprüfungen von content/edit und
multiedit, die Prüfungen vor den Filtern, die strukturellen Ansichts-Cache-Schlüssel), `expRestContentPermissionTest`
(CP-07: die REST-Bearbeitungsprüfung), `expRadWizardListsTest` (die erzeugten Listener).

## Verwandte Seiten

- [Bearbeitungszugriff auf Objekte, die nie veröffentlicht wurden](../../../eng-us/bc/6.0/draft-edit-access.md)
- [Erweiterungspunkte](../../../eng-us/bc/6.0/rad-extension-points.md)
- [Leitfaden für Extensions](../../anleitungen/extensions.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
