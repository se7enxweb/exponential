# Einschränkungen von Inhaltsrichtlinien durch Extensions

Lesen Sie diese Seite, wenn Sie eine Extension bauen, die nach einer Regel, die der Kernel nicht kennt (eine
Organisation, eine Region, eine Rechtematrix), einschränkt, wer Inhalte lesen oder bearbeiten darf, oder wenn Sie den
Zugriff stellvertretend für einen anderen Benutzer prüfen (Benachrichtigungen, per Mail versandte Listen).
Administratoren, die Rollen pflegen, finden unter
[Was sich für bestehende Installationen ändert](#was-sich-für-bestehende-installationen-ändert), was sich für sie ändert.

## Kurz gefasst

| | |
|---|---|
| Was neu ist | Eine Extension kann einer Inhaltsfunktion (`content/read`, `content/edit`, ...) eine eigene Einschränkung hinzufügen, neben Klasse, Sektion und Teilbaum. Eine Handler-Klasse entscheidet für Objekte, Knoten und Versionen und liefert die SQL-Bedingung für Listen- und Baum-Fetches. |
| Registriert über | Der Filter `module/functionlist` (`site.ini [Event] Listeners[]`) fügt die Einschränkung der Funktion hinzu; `site.ini [RoleSettings] LimitationHandlers[<limitation>]=<class>` benennt ihren Handler. |
| Vertrag | `ezpContentLimitationHandler`: `checkAccess( $limitation, $values, $functionName, $subject, $userID )` und `permissionSQL( $limitation, $values, $tableAliasName, $userID )`. |
| Ebenfalls neu | `checkAccess()` von `eZContentObject`, `eZContentObjectTreeNode` und `eZContentObjectVersion` nimmt den zu prüfenden Benutzer als sechstes Argument, `editAccess()` als drittes; `./console exp:access:check` fragt sie von der Kommandozeile ab. |
| Sicherheitsregel | Eine Einschränkung, die kein Handler auswertet, verweigert, und zwar überall. Ebenso ein Handler, der fehlt, nicht erzeugt werden kann, eine Ausnahme wirft oder mit SQL antwortet, das keine in sich geschlossene Bedingung ist; jeder dieser Fälle wird einmal pro Anfrage protokolliert. |
| Ausgangspunkt | **Setup > RAD > Settings extension** mit dem Ereignis `module/functionlist`: Der Assistent schreibt den Listener und einen funktionierenden Handler (`<class>MaxDepth`). |

## Was Sie tun können

Bisher bedeutete eine eigene Einschränkung einen Patch des Kernels: ein `case` in den `checkAccess()`-Methoden von
Objekten, Knoten und Versionen, einer in `eZContentObjectTreeNode::createPermissionCheckingSQL()` für Fetches und eine
Zeile in `kernel/content/module.php`, damit der Rolleneditor sie anbietet. Jetzt bringt die Extension alle drei mit:

| Wo der Kernel eine Richtlinie prüft | Was er den Handler fragt |
|---|---|
| `eZContentObject::checkAccess()` (`$object.can_read`, `can_edit`, ...) | `checkAccess()` mit dem Objekt |
| `eZContentObjectTreeNode::checkAccess()` (`$node.can_read`, ...) | `checkAccess()` mit dem Knoten |
| `eZContentObjectVersion::checkAccess()` (`versionread`, `versionremove`) | `checkAccess()` mit der Version |
| Listen- und Baum-Fetches, die Suche des Kernels, verwandte Objekte (`createPermissionCheckingSQL()`) | `permissionSQL()` für den aktuellen Benutzer |
| Teilbaum-Benachrichtigungen (wer die Mail über ein neues Objekt bekommt) | `checkAccess()` mit dem Objekt, für jeden Abonnenten |

Die Einschränkung funktioniert wie eine Kernel-Einschränkung: Die Einschränkungen einer Richtlinie werden mit UND
verknüpft, die Richtlinien eines Benutzers mit ODER.

## Eine bauen

Das Beispiel schränkt das Lesen nach Region ein. Die Extension `myext` speichert die Region jedes Objekts in einer
eigenen Tabelle `myext_object_region (contentobject_id, region_id)`.

1. Fügen Sie die Einschränkung `content/read` hinzu, damit der Rolleneditor sie anbietet. Ein Listener von
   `module/functionlist` bekommt die Funktionsliste jedes Moduls mit dem Modulnamen und gibt sie zurück, verändert
   oder nicht:

   ```php
   class myExtRegionLimitation implements ezpContentLimitationHandler
   {
       public static function functionList( $functionList, $moduleName )
       {
           if ( $moduleName === 'content' && isset( $functionList['read'] ) )
           {
               $functionList['read']['Region'] = array(
                   'name' => 'Region',
                   'label' => ezpI18n::tr( 'extension/myext', 'Sales region' ),
                   'values' => array( array( 'Name' => ezpI18n::tr( 'extension/myext', 'North' ), 'value' => '1' ),
                                      array( 'Name' => ezpI18n::tr( 'extension/myext', 'South' ), 'value' => '2' ) ) );
           }
           return $functionList;
       }
   ```

   Der Eintrag hat die Form der Einschränkungen in `kernel/content/module.php`: einen `name` aus Buchstaben, Ziffern
   und `_`, feste `values` oder `class`, `function` und `parameter` einer Methode, die sie auflistet. `label`
   (optional, neu) zeigen die Rollenseiten anstelle des Namens; übersetzen Sie es und die Namen der Werte im Listener,
   der in der Sprache der Anfrage läuft. Ein Eintrag anderer Form wird protokolliert und weggelassen; ein Listener,
   der eine Ausnahme wirft oder kein Array zurückgibt, wird protokolliert, und das Modul behält seine eigene Liste. Die
   Listener laufen einmal pro Modul und Anfrage, egal wie oft das Modul nachgeschlagen wird.

2. Entscheiden Sie in PHP. `$subject` ist ein `eZContentObject`, ein `eZContentObjectTreeNode` oder ein
   `eZContentObjectVersion`; `$userID` ist der Benutzer, für den geprüft wird, und das ist nicht immer der aktuelle
   Benutzer. Nur `true` erlaubt:

   ```php
       public function checkAccess( $limitation, array $values, $functionName, $subject, $userID )
       {
           $objectID = $subject instanceof eZContentObject
               ? $subject->attribute( 'id' )
               : $subject->attribute( 'contentobject_id' );
           $rows = eZDB::instance()->arrayQuery(
               'SELECT region_id FROM myext_object_region WHERE contentobject_id = ' . (int)$objectID );
           return count( array_intersect( array_column( $rows, 'region_id' ), $values ) ) > 0;
       }
   ```

3. Geben Sie dieselbe Regel als SQL für Fetches an. Die Bedingung wird mit UND mit den anderen Einschränkungen der
   Richtlinie verknüpft. Sie darf die Tabelle `ezcontentobject` und die Knotentabelle unter dem Alias
   `$tableAliasName` verwenden. Wandeln Sie jeden Wert um oder maskieren Sie ihn; geben Sie `false` zurück, wenn sich
   die Regel nicht als SQL schreiben lässt, dann gewährt die Richtlinie in Fetches keinen Zugriff:

   ```php
       public function permissionSQL( $limitation, array $values, $tableAliasName, $userID )
       {
           $regions = implode( ', ', array_map( 'intval', $values ) );
           return "ezcontentobject.id IN ( SELECT contentobject_id FROM myext_object_region WHERE region_id IN ( $regions ) )";
       }
   }
   ```

   Lautet die Regel „diese Spalte hat einen dieser Werte“, geben Sie stattdessen die Spalte und die Werte zurück, und
   der Kernel schreibt die `IN`-Anweisung, wobei jeder Wert in eine Ganzzahl umgewandelt oder maskiert wird:

   ```php
   return array( 'column' => 'ezcontentobject.section_id', 'values' => $values, 'type' => 'int' );
   // 'type' => 'string' escapes instead; 'not' => true writes NOT IN; a list of such arrays is joined by AND
   ```

   `not` muss ein echter Boolean sein. Jeder andere Wert (`'false'`, `'true'`, `1`, `0`) ließe offen, ob die
   Bedingung „nur diese“ oder „alle außer diesen“ bedeutet; deshalb wird die Bedingung abgewiesen, und die Richtlinie
   gewährt in Fetches keinen Zugriff, ebenso wie in Suchen.

4. Registrieren Sie beides in `extension/myext/settings/site.ini.append.php`:

   ```ini
   [Event]
   Listeners[]=module/functionlist@myExtRegionLimitation::functionList

   [RoleSettings]
   LimitationHandlers[Region]=myExtRegionLimitation
   ```

5. Erzeugen Sie die Autoloads neu und leeren Sie die Caches:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

6. Bearbeiten Sie unter **Benutzer > Rollen und Richtlinien** eine Rolle, fügen Sie eine Richtlinie für
   `content/read` hinzu und wählen Sie **Region** neben Klasse und Sektion.

Der Handler muss in `checkAccess()` und in `permissionSQL()` dieselbe Antwort geben. Sonst zeigt eine Liste ein Objekt,
dessen Seite dann verweigert wird, oder verbirgt eines, das der Benutzer öffnen darf.

### Der Vertrag eines Handlers

| Regel | Warum |
|---|---|
| `$values` ist eine Liste von Strings, so wie die Richtlinie sie speichert. | Der Rollen-Cache und die Datenbank übergeben sie als Strings. |
| Nur `true` aus `checkAccess()` erlaubt; `1`, `'yes'` und `null` verweigern. | Eine einheitliche Lesart der Antwort bei jeder Prüfung. |
| Ein String aus `permissionSQL()` ist in sich geschlossen: Anführungszeichen und Klammern sind ausgeglichen, und außerhalb von Strings in Anführungszeichen gibt es kein `;` und keinen Kommentar (`--`, `#`, `/*`). Alles andere lässt die Richtlinie in Fetches keinen Zugriff gewähren und wird protokolliert. | Er wird in Klammern gesetzt und mit UND verknüpft; eine Bedingung, die diese Klammern schließt (`1 = 1 ) OR ( 1 = 1`), würde eine eigene Richtlinie für alle öffnen. Der Kernel kann nicht prüfen, ob Werte maskiert wurden: Wandeln Sie sie um, maskieren Sie sie, oder verwenden Sie die Spaltenform. |
| Eine Ausnahme verweigert und wird einmal pro Anfrage protokolliert; sie beendet die Anfrage nicht. | Ein Handler, der fehlschlägt, darf niemals erlauben und auch keine Listenseite lahmlegen. |
| Eine Instanz pro Anfrage, die für den Rest der Anfrage behalten wird. Speichern Sie Nachgeschlagenes ruhig in Eigenschaften der Instanz, niemals aber in statischen Eigenschaften. | Velocity führt die nächste Anfrage im selben Prozess aus; der Kernel erzeugt den Handler dafür neu. |
| Nehmen Sie den Benutzer aus `$userID`, niemals aus der Sitzung oder `eZUser::currentUser()`. | Prüfungen werden auch für andere Benutzer durchgeführt (Teilbaum-Benachrichtigungen, `exp:access:check`). |

Der Assistent für Einstellungs-Extensions unter **Setup > RAD** schreibt als Beispiel einen Handler, der alle diese
Regeln einhält: Wählen Sie das Ereignis `module/functionlist`. Er fügt `<class>MaxDepth` hinzu (Lesen nur bis zu einer
bestimmten Tiefe des Baums) mit den Tiefen aus `site.ini [<class>] MaxDepths[]` sowie die auskommentierte Zeile
`[RoleSettings] LimitationHandlers[]`, die ihn einschaltet.

## In den Rollenseiten

- Die Rollenansicht, der Rolleneditor, die Richtlinienlisten der Knoten- und Benutzeransichten und die
  Richtlinienformulare zeigen das `label` einer Einschränkung, wo sie eines hat, und neben einer
  Inhaltseinschränkung, die kein Handler auswertet, den Hinweis **(ohne Auswertung, verweigert)** mit einem Tooltip,
  der besagt, dass ihre Richtlinie keinen Zugriff gewährt.
- Eine Einschränkung, deren Extension nicht mehr aktiv ist, wird mit ihren gespeicherten Werten angezeigt; bisher
  wurde sie leer angezeigt.
- Die Editoren speichern für eine Einschränkung nur Werte, die das Formular angeboten hat (ihre `values` oder was ihre
  `class` und `function` auflisten). Ein in der Anfrage erfundener Wert wird weggelassen und protokolliert; ein
  einzelner Wert, der statt einer Liste gesendet wird, beendet die Anfrage nicht mehr. Bleibt nichts Angebotenes
  übrig, wird keine Einschränkung gespeichert, wie bei **Beliebig**: Wer Rollen bearbeitet, kann Beliebig ohnehin
  wählen.

## Zugriff für einen anderen Benutzer prüfen

Die drei `checkAccess()`-Methoden nehmen den Benutzer als sechstes Argument, nach der Sprache:

```php
$node->checkAccess( 'read', false, false, false, false, $subscriberID );
$object->checkAccess( 'edit', false, false, false, false, $userID );
$version->checkAccess( 'versionread', false, false, false, false, $userID );
```

`false` (der Standard) prüft wie bisher für den aktuellen Benutzer. Die Prüfung liest die Rollen dieses Benutzers und
übergibt den Benutzer an die Handler. Eine ID, die zu keinem Benutzer gehört, erhält keinen Zugriff. Die Regel
„anonyme Benutzer dürfen bearbeiten, was sie in dieser Sitzung erstellt haben“ (Wert 2 der Einschränkung `Owner`)
gilt nur für den aktuellen Benutzer, da nur er eine Sitzung hat, die sich auswerten lässt.
`eZUser::accessUser( $userID )` gibt den Benutzer zurück, für den eine Prüfung durchgeführt wird:

| `$userID` | Benutzer |
|---|---|
| `false`, `0`, `null`, `''` | der aktuelle Benutzer |
| die ID des aktuellen Benutzers | das eigene Objekt des aktuellen Benutzers |
| die ID eines anderen Benutzers | dieser Benutzer, einmal pro Anfrage geladen und für den Rest der Anfrage behalten; für die nächste Anfrage vergessen (Velocity), durch `eZUser::cleanupCache()` und wenn der Cache dieses Benutzers geleert wird |
| ein deaktiviertes Konto, eine ID, die zu keinem Benutzer gehört, alles außer einer positiven ganzen Zahl (`'1 OR 1'`, `true`, ein Array) | keiner: kein Zugriff |

Dies ist ausschließlich eine PHP-Schnittstelle; nichts, was ein Besucher sendet, erreicht sie. Der andere Benutzer ist
ein eigenes `eZUser` mit eigenen Rollen: Die Rollen und die Sitzung des aktuellen Benutzers sowie die an Objekten
zwischengespeicherten Antworten (`can_read`, `can_edit`) spielen keine Rolle, und die Prüfung hinterlässt nichts am
Objekt.

Der Bearbeitungszugriff läuft zusätzlich über den Filter `content/edit/access` und hat deshalb eine eigene Methode:

```php
$object->editAccess( $version, $language, $userID );   // the filter gets $userID
```

Für einen anderen Benutzer verwendet sie dessen `content/edit`-Richtlinien und dessen `user/selfedit` für sein eigenes
Benutzerobjekt.

Von der Kommandozeile aus antwortet `exp:access:check` für einen Benutzer und nennt, was verweigert hat:

```bash
./console exp:access:check --user=editor --node=2
./console exp:access:check --user=14 --object=57 --function=edit --language=eng-GB
./console exp:access:check --user=anonymous --node=2 --json
```

```
User      14 (editor)
Subject   node 2, object 1 "Home"
Function  content/read
  refused: policy 342, Class( 2 )
  refused: policy 343, Region( 4 ), no handler evaluates it
DENIED
```

Er gibt `ALLOWED` (Exit-Code 0) oder `DENIED` (Exit-Code 1) aus, Exit-Code 2, wenn Benutzer, Knoten oder Objekt nicht
gefunden werden. Es ist ein Befehl und keine Seite „als dieser Benutzer ansehen“ im Admin: Wer ihn ausführen kann, kann
ohnehin die Datenbank lesen, und es wird keine Sitzung des anderen Benutzers erzeugt.
`expContentAccessReport::check()` gibt PHP-Code dieselbe Antwort.

## Was der Kernel ohne Handler tut

| Situation | Ergebnis |
|---|---|
| Eine Einschränkung, die der Kernel nicht kennt, ohne Eintrag in `LimitationHandlers[]` | Die Richtlinie gewährt keinen Zugriff: `checkAccess()` verweigert, die Fetch-Bedingung ist `1 = 0`, die Teilbaum-Benachrichtigung wird darüber nicht versandt. |
| Die Klasse existiert nicht, implementiert `ezpContentLimitationHandler` nicht, oder ihr Konstruktor wirft eine Ausnahme | Wie ohne Handler; ein Fehler in der Debug-Ausgabe nennt die Klasse und den Grund, einmal pro Anfrage. |
| Ein Handler, der für eine Kernel-Einschränkung registriert ist (`Section`, `Subtree`, `StateGroup_<identifier>`, ...) | Ignoriert: Der Kernel wertet seine eigenen Einschränkungen selbst aus. |
| Eine Kernel-Einschränkung einer anderen Funktion in einem Fetch (`Language`, `ParentClass`) | Wie bisher aus dem SQL weggelassen. |
| Eine Ausnahme aus `checkAccess()` oder `permissionSQL()` | Verweigert (`false`, `1 = 0`) und wird einmal pro Anfrage protokolliert; die Anfrage läuft weiter. |
| `permissionSQL()` antwortet mit etwas anderem als `false`, einem in sich geschlossenen String oder einer Spaltenbedingung | Die Richtlinie gewährt in Fetches keinen Zugriff; einmal pro Anfrage mit dem Anfang der Antwort protokolliert. |
| `solrFilter()` ohne Handler, für einen Handler ohne `ezpContentLimitationSolrHandler`, für eine Kernel-Einschränkung, ein Handler, der `false` zurückgibt, eine Ausnahme wirft oder ohne Filter antwortet | `DENY_SOLR` (`( *:* -*:* )`): Die Richtlinie gewährt in Suchen keinen Zugriff; einmal pro Anfrage protokolliert (eine fehlende Schnittstelle als Warnung). |

## Was sich für bestehende Installationen ändert

- Eine Rolle mit einer Einschränkung, die der Kernel nicht kennt und die keine Extension behandelt (übrig geblieben aus
  einem alten Patch oder einer entfernten Extension), hat bisher `checkAccess()` eingeschränkt, nicht aber Fetches:
  Listen zeigten die Objekte, die Seiten verweigerten sie. Jetzt lassen auch die Listen sie weg. Suchen Sie solche
  Einschränkungen unter **Benutzer > Rollen und Richtlinien** oder in der Tabelle `ezpolicy_limitation`, und entfernen
  Sie sie oder installieren Sie die Extension, die sie behandelt.
- `eZContentObjectVersion::checkAccess()` hält bei der ersten Einschränkung einer Richtlinie an, die verweigert. Bisher
  konnte eine verweigernde `Language`-Einschränkung oder eine unbekannte Einschränkung durch die nächste Einschränkung
  derselben Richtlinie wieder erlaubt werden.
- Teilbaum-Benachrichtigungen prüfen die Einschränkung `Group` („Self group“) von `content/read`; bisher ignorierten
  sie sie und versandten die Mail an alle, die der Rest der Richtlinie zuließ.
- Eine Klasse, die `checkAccess()` von `eZContentObject`, `eZContentObjectTreeNode` oder `eZContentObjectVersion`
  überschreibt, muss das sechste Argument `$userID = false` annehmen. PHP 8 weist eine Methode mit weniger Parametern
  als die überschriebene mit einem fatalen Fehler zurück, sobald die Klasse geladen wird.

## Suchen

Eine Suchmaschine, die selbst nach den Richtlinien des Benutzers filtert, etwa eZ Find mit Solr, führt das SQL der
Fetches nicht aus. Ein Handler, der zusätzlich `ezpContentLimitationSolrHandler` implementiert, liefert dafür den
Filter:

```php
class myExtLimitationHandler implements ezpContentLimitationSolrHandler
{
    // checkAccess() and permissionSQL() as above

    public function solrFilter( $limitation, array $values, $userID )
    {
        // the safer form: the kernel writes "meta_section_id_si:(1 OR 2)" and escapes the values
        return array( 'field' => eZSolr::getMetaFieldName( 'section_id' ), 'values' => $values );
    }
}
```

Die Such-Extension ruft `ezpContentLimitation::solrFilter( $limitation, $values, $userID )` für jede Einschränkung auf,
die keine Kernel-Einschränkung ist (`ezpContentLimitation::isKernelLimitation()`; diese übersetzt sie selbst), und
verknüpft die Antwort mit UND mit den anderen Einschränkungen der Richtlinie. Die Antwort ist immer ein Filter in
Klammern. Gewährt die Richtlinie in der Suche keinen Zugriff, ist sie `ezpContentLimitation::DENY_SOLR`, ein Filter,
der auf kein Dokument passt: ohne Handler, für einen Handler, der die Schnittstelle nicht implementiert, wenn er
`false` zurückgibt oder eine Ausnahme wirft, für eine Kernel-Einschränkung und für eine Antwort, die kein Filter ist:

- Ein String muss in sich geschlossen sein: doppelte Anführungszeichen geschlossen, Klammern ausgeglichen und nie
  geschlossen, bevor sie geöffnet wurden, Bereichsklammern (`[ ]`, `{ }`) geschlossen und nicht verschachtelt, keine
  lokalen Parameter (`{!`) und keine verschachtelte Abfrage (`_query_`), auch nicht in Anführungszeichen, kein
  NUL-Byte. Werte darin werden mit `ezpContentLimitation::solrValue()` maskiert, das auch einen Wert `AND`, `OR`
  oder `NOT` in einen Begriff verwandelt. Ein Filter, der nur ausschließt (`-field:x`), passt innerhalb seiner
  Klammern auf nichts: Schreiben Sie `*:* -field:x`, oder verwenden Sie die Array-Form mit `'not' => true`.
- Ein Array `array( 'field' => ..., 'values' => ..., 'not' => false )` oder eine Liste davon, verknüpft mit UND: Das
  Feld ist ein Name aus Buchstaben, Ziffern und `_`, die Werte sind Skalare in UTF-8 und nicht leer, `not` ist ein
  Boolean, falls angegeben. Keine Werte passen auf nichts (mit `'not' => true`: auf alles).

Die Such-Extension darf die Richtlinie nicht weglassen, statt `DENY_SOLR` zu verknüpfen: eZ Find filtert überhaupt
nicht, wenn keine Richtlinie übrig bleibt, sodass ein Benutzer, dessen einzige Richtlinie eine solche Einschränkung hat,
alles finden würde.

## Grenzen

- `[Event] Listeners[]` werden an Web-Anfragen angehängt. Ein Befehl oder Cronjob-Teil, der die Funktionsliste eines
  Moduls liest (selten: der Rolleneditor und die Richtlinienansicht tun es), sieht sie ohne die hinzugefügte
  Einschränkung. Zugriffsprüfungen sind davon nicht betroffen, weil sie die Richtlinien lesen, nicht die
  Funktionsliste.
- Der View-Cache hält eine Kopie pro Satz von Rollen und Einschränkungen eines Benutzers. Ein Handler, dessen Antwort
  von Daten abhängt, die nicht in der Rolle stehen (eine pro Benutzer gespeicherte Matrix), braucht eigene
  View-Cache-Schlüssel oder einen für die geschützten Klassen abgeschalteten View-Cache.
- Eine Suchmaschine, die ihren eigenen Berechtigungsfilter baut, fragt den Handler nur, wenn der Handler
  `ezpContentLimitationSolrHandler` implementiert und die Such-Extension `ezpContentLimitation::solrFilter()` aufruft
  (siehe [Suchen](#suchen)). eZ Find in der veröffentlichten Fassung lässt eine Einschränkung, die es nicht kennt,
  aus der Richtlinie weg: Solange es `solrFilter()` nicht aufruft, zeigt eine Suche einem Benutzer mit einer solchen
  Richtlinie mehr, als die Richtlinie erlaubt. Deaktivieren Sie für solche Benutzer die Suche, oder halten Sie die
  durch die Einschränkung geschützten Inhalte aus dem Index heraus, bis die Such-Extension angepasst ist.
- Die Richtlinie `state/assign` (`eZContentObject::allowedAssignStateIDList()`) fragt Handler noch nicht ab.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/site.ini` | `RoleSettings` | `LimitationHandlers[<limitation>]` | leer | Extension |
| `settings/site.ini` | `Event` | `Listeners[]=module/functionlist@<callback>` | keiner | Extension |

## Wie es funktioniert

- `ezpContentLimitation` (`kernel/private/classes/ezpcontentlimitation.php`) erzeugt den Handler einmal pro Anfrage
  (die Anfrage wird an `REQUEST_TIME_FLOAT` erkannt, die Einstellung an einem Hash von `LimitationHandlers[]`;
  `resetCache()` vergisst beides) und gibt ohne Handler `false`, `1 = 0` oder `DENY_SOLR` zurück. `sqlCondition()` und
  `solrCondition()` prüfen bzw. schreiben die Bedingung eines Handlers; `isKernelLimitation()` benennt die
  Einschränkungen, die der Kernel für sich behält. Das Exit-Signal von Velocity läuft durch sein `catch` hindurch.
- Die Schnittstellen sind `ezpContentLimitationHandler` (`kernel/private/interfaces/ezpcontentlimitationhandler.php`)
  und `ezpContentLimitationSolrHandler` (`kernel/private/interfaces/ezpcontentlimitationsolrhandler.php`).
- `eZModule::initialize()` (`lib/ezutils/classes/ezmodule.php`) leitet die `$FunctionList` jedes Moduls durch den
  Filter `module/functionlist` (`eZModule::filterFunctionList()`): nur wenn ein Listener angehängt ist, einmal pro
  Moduldatei, Anfrage und Satz von Listenern (`ezpEvent::listenerIds()`), und behält, was die Form von `module.php`
  hat.
- `eZPolicyLimitation` liefert den Rollenseiten `label`, `denies_without_handler` und `validValues()`.
- `eZUser::accessUser()` und `expContentAccessReport` (`kernel/private/classes/expcontentaccessreport.php`) führen die
  Prüfungen für einen anderen Benutzer durch; `exp:access:check` ist
  `kernel/private/classes/commands/accesscheck.php`.
- Die eigenen Werte des Kernels für Klasse, Sektion, Knoten und Objektzustand werden im Fetch-SQL in Ganzzahlen
  umgewandelt.
- Die REST-Schnittstelle (`expRestContentPermission`) und die Remote-Dienste (`expservices`) entscheiden mit denselben
  `checkAccess()`, `canRead()` und `editAccess()`, sodass Einschränkungen von Extensions auch dort gelten.
- Der Erweiterungspunkt ist unter **Setup > RAD** und in [Erweiterungspunkte](../../../eng-us/bc/6.0/rad-extension-points.md)
  aufgeführt.

Tests: `ezpContentLimitationTest`, `eZContentPermissionSQLTest`, `eZContentAccessForAnotherUserTest`,
`eZPolicyLimitationLabelsAndValuesTest` und `expRadWizardListsTest` (ohne Datenbank), `eZContentAccessForUserLiveTest`
(auf einer Installation; ohne eine übersprungen).

## Verwandte Seiten

- [Erweiterungspunkte](../../../eng-us/bc/6.0/rad-extension-points.md)
- [Leitfaden zu Extensions](../../anleitungen/extensions.md)
- [Bearbeitungszugriff auf Objekte, die nie veröffentlicht wurden](../../../eng-us/bc/6.0/draft-edit-access.md)
- [Verhaltensänderungen vom 1. und 2. Oktober 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-10.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
