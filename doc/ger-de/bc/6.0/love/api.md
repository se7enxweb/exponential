# API-Referenz

Die Klassen, Methoden, Einstellungen, Template-Funktionen und Konsolenbefehle, die die LoveStack-Funktionen in
Exponential 6.0.15 liefern, für Entwickler. Was jede Funktion für Redakteure und Administratoren tut, steht im
[Überblick](../lovestack.md#funktion-für-funktion).

Konventionen, wie überall in Exponential:

- neue Kernel-Klassen heißen `exp*` und liegen in `kernel/classes/exp*.php`;
- vorhandene `eZ*`-Klassen erhalten nur Methoden und optionale Parameter; keine öffentliche Signatur hat sich
  geändert;
- Einstellungen stehen in den vorhandenen INI-Dateien und sind dort mit ihrem Standard dokumentiert; ändern Sie sie in
  einem Override (`settings/override/<file>.append.php`) oder einem Siteaccess;
- Konsolenbefehle heißen `exp:<name>` und werden mit `./console` ausgeführt;
- nichts wird zwischen Anfragen behalten, daher funktioniert alles unter der Velocity-Engine von Exponential, deren
  PHP-Worker über Anfragen hinweg weiterleben.

Code, der von einer Änderung in LoveStack abgeleitet ist, trägt die Zeile
`Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit <sha>), Copyright (C) Mugo Web, GPL v2`.

---

## Sicherheit und Sitzungen

### Knotendaten-Aufrufe und `RequirePolicy` (L01)

```ini
# extension/ezjscore/settings/ezjscore.ini (shipped)
[ezjscServer]
FunctionList[]=ezjscnode

[ezjscServer_ezjscnode]
Class=ezjscServerFunctionsNode
Functions[]=ezjscnode
RequirePolicy=disabled
```

`RequirePolicy` funktioniert für jeden Block `[ezjscServer_<name>]`:

| Wert | `Functions[]` | Ein abgelehnter Aufruf |
|---|---|---|
| nicht gesetzt | geprüft, wie bisher | mit seinem Fehlertext beantwortet (HTTP 200) |
| `disabled` | nicht geprüft | — |
| `enabled` | geprüft | eine Anfrage, deren Aufrufe alle aus diesem Grund abgelehnt werden, wird mit 403 Forbidden beantwortet |

`ezjscServerRouter::getInstance( $args, $requireIniGroup, $checkFunctionExistence, &$refusal )` setzt `$refusal` auf
`invalid`, `access` oder `policy-required`; `ezjscServerRouter::refusedStatus( array $refusals )` liefert 200 oder 403
(403 nur, wenn jede Ablehnung `policy-required` ist).

Neue Installationen schreiben `RequirePolicy=enabled` in `settings/override/ezjscore.ini.append.php`. Um es selbst
einzuschalten:

```ini
# settings/override/ezjscore.ini.append.php
[ezjscServer_ezjscnode]
RequirePolicy=enabled
```

nachdem Sie den Rollen, die es brauchen, *ezjscore / call* mit der Funktionsliste `ezjscnode` gegeben haben
([wer ezjscnode aufruft](l01-aufrufer.md)).

### Registrierung und `PolicyOmitListExclude[]` (L02)

```ini
# settings/override/site.ini.append.php
[RoleSettings]
PolicyOmitListExclude[]=user/register
```

`PolicyOmitListExclude[]` nimmt Einträge aus `PolicyOmitList[]` heraus, nachdem jede Einstellungsdatei
zusammengeführt ist, sodass die Einträge erhalten bleiben, die Erweiterungen hinzufügen (ein Zurücksetzen von
`PolicyOmitList[]` in einem Override würde sie verwerfen).

```php
expPolicyOmitList::entries( $ini = null );          // PolicyOmitList[] without the excluded entries
expPolicyOmitList::excluded( $ini = null );         // PolicyOmitListExclude[]
expPolicyOmitList::omits( $module, $view = '', $ini = null ); // true: no policy needed
```

Verwendet vom Web-Kernel, von der Zugriffsprüfung der Views, vom `run` von ezjscore und vom Benchmark-Befehl. In
Templates wird der Link zum Registrieren so angezeigt:

```tpl
{if fetch( 'user', 'can_open', hash( 'uri', 'user/register' ) )} ... {/if}
```

`can_open` berücksichtigt die Ausnahmeliste; `has_access_to` tut es nicht und würde den Link auf jeder Website
verbergen, die die Registrierung offen hält.

### Abmelden (L03)

`eZUser::logoutCurrent()` endet mit `eZSession::remove()`. Das `destroy()` der Session-Handler lässt das
Sitzungs-Cookie mit dem Pfad, der Domain, `Secure`, `HttpOnly` und `SameSite` ablaufen, mit denen `eZSession::start()`
es gesetzt hat.

```php
eZSession::expiredCookie();                       // array( 'name', 'value' => '', 'options' => setcookie() options )
eZSession::expireCookie();                        // sends it once per response; false when headers are out
eZSession::listsExpiredCookie( $headers, $name ); // does a header list already remove the cookie
```

`eZSession::remove()` schließt außerdem ohne Schreiben eine Sitzung, die der Handler laufen gelassen hat, lässt das
Cookie für einen eigenen Handler ablaufen, der das nicht tut, und entfernt `$_COOKIE[<session name>]` und die
Sitzungs-ID, sodass eine später in der Anfrage gestartete Sitzung eine neue ID erhält.

### Eindeutige Anmeldenamen (L04)

```php
class expUserLoginIntegrity   // kernel/classes/expuserloginintegrity.php
{
    const INDEX_NAME = 'ezuser_login';
    /** groups of accounts sharing a login: array{login, user_ids, logins, blocks_index}[];
     *  blocks_index false = differ only in case, which the engine's index accepts */
    public static function duplicates( $db = null ): array;
    /** array{engine, exists, unique, names} */
    public static function indexStatus( $db = null ): array;
    /** adds the unique index (replacing the plain one); refuses while duplicates() is not empty */
    public static function addUniqueIndex( $db = null, &$message = null ): bool;
    /** content object id of another account with this login (case-insensitive), or null */
    public static function loginTakenBy( $login, $userID ): ?int;
    /** stores unless the login is taken */
    public static function storeUser( eZUser $user ): bool;
}
```

```sh
./console exp:user-login-check                 # report only; --show-logins, --json
./console exp:user-login-check --fix-index     # adds the index after a clean report
# exit code: 0 clean (or index added), 1 duplicates found (or index refused), 2 error
```

Aufrufer: `eZUserType` (eine erste Version, die die Validierung nicht bestanden hat, wird nicht gespeichert),
`eZLDAPUser::publishUpdateUser()` (ein neues Konto über `storeUser()`; bei einem verlorenen Wettlauf wird das neue
Objekt entfernt und das vorhandene Konto zurückgegeben).

### Prüfung hochgeladener Bilder (L05, L06)

```ini
# settings/image.ini (shipped)
[ImageUploadSettings]
ValidateMIMEType=enabled
AllowedMIMETypes[]
AllowedMIMETypes[]=image/jpeg
AllowedMIMETypes[]=image/png
AllowedMIMETypes[]=image/gif
AllowedMIMETypes[]=image/webp
AllowedMIMETypes[]=image/avif
AllowedMIMETypes[]=image/tiff
AllowedMIMETypes[]=image/bmp
```

```php
expImageUploadValidator::validate( $path, $originalName = null );   // array( 'valid' => bool, 'mime' => string )
expImageUploadValidator::validateAgainst( $path, array $allowed, $checkAllowList = true, $originalName = null );
expImageUploadValidator::allowedMIMETypes();
expImageUploadValidator::isEnabled();
expImageUploadValidator::normalize( $type );       // aliases such as image/x-ms-bmp, image/pjpeg
eZImageType::imageFileError( $filePath, $originalName = null );                  // one place for every image path
eZContentUpload::imageUploadError( $dataMap, $fileAttribute, $filePath, $originalName = null );
eZPackageCreationHandler::httpFileIsImage( $httpFileName, &$errorList );         // L06
```

Neue Meldung: "Images of the type %1 are not accepted." Ein zur Liste hinzugefügtes SVG wird vom Validator
akzeptiert, aber der Bild-Datentyp lehnt es weiterhin ab (über sein `getimagesize()` und die Prüfung des Markups).

### `[Session] SameSite` von LoveStack

`site.ini [Session] SameSite=` wird gelesen, wenn `CookieSameSite` nicht gesetzt ist, und protokolliert einmal pro
Anfrage eine Deprecation; `CookieSameSite` gewinnt, wenn beide gesetzt sind.

---

## Diagnose und Protokollierung (L07–L12)

```php
eZDebug::LEVEL_DEPRECATED;          // new level
eZDebug::SHOW_DEPRECATED;           // new show flag, part of SHOW_ALL
eZDebug::writeDeprecated( $string, $label = '', $backgroundClass = '' );
eZDebug::deprecatedCount();         // this request, every occurrence
eZDebug::isDeprecatedLogEnabled();
eZDebug::resetDeprecations();       // the web kernel calls it when a request starts
eZLog::fileSetting( 'LogFileMaxSize', 1 ); // int|null from site.ini [FileSettings]
```

```ini
# settings/site.ini (shipped)
[DebugSettings]
# written to var/log/deprecated.log; not kept for the debug toolbar
DeprecatedLog=enabled
# %1$s template edit URI, %2$s siteaccess, %3$s template file; empty: the usual link
UsedTemplateEditUrl=

[FileSettings]
LogFileMaxSize=2097152
LogFileRotateCount=3
```

Reihenfolge der Log-Größe: `eZLog::setMaxLogSize()`, dann die Konstante in `config.php`, dann `site.ini`, dann der
Standard der Klasse (2 MB). `AlwaysLog[]=deprecated` schreibt Deprecations, während die Debug-Ausgabe ausgeschaltet
ist.

- L09: `eZTemplate` warnt `Operator '<op>' has no default value for parameter '<name>'`;
  `eZTemplate::operatorParametersWithoutDefault()`. Für kompilierte Templates wird beim Kompilieren gewarnt.
- L10: `eZTemplatesStatisticsReporter::templateEditUrlFormat()`, `templateEditUrl()`.
- L11: `eZDBException::getCode()` gibt die Fehlernummer des Treibers zurück, wie `errorNumber()`: MySQL-Nummer,
  SQLSTATE-Zeichenkette bei PostgreSQL, Ergebniscode bei SQLite, ORA-Nummer bei Oracle; bei MongoDB nur
  `errorNumber()`. `eZExecution::isDatabaseUnavailableCode()` erkennt die Codes einer verlorenen oder abgelehnten
  Verbindung.
- L12: `eZContentCacheManager::cacheThresholdWarningText( $nodeCount, $cleanupValue )`.

---

## Inhalte, Datentypen und die Administration (L13–L21)

### Audit von Änderungen an Orten (L13)

Geschrieben über das Audit-Protokoll von Exponential, Kanal `content`, aus `eZContentOperationCollection`
(`addAssignment`, `removeNodes`, `updateMainAssignment`):

| Ereignis | Beschriftung in der Konsole | Standard |
|---|---|---|
| `content.node.add` (eines pro neuem Ort; Ziel = der neue Knoten; after: Orte, Knoten, Elternknoten, Hauptknoten) | Ort hinzugefügt | aus |
| `content.node.remove` (der entfernte Knoten; before: Elternknoten, Pfad, Objekt-ID, Name, Haupt) | Ort entfernt | ein |
| `content.node.main` (Ziel = der neue Hauptknoten; before/after Hauptknoten; `cause` `location-remove`, wenn der nächste Ort befördert wird) | Hauptort geändert | aus |

```ini
# settings/override/audit.ini.append.php
[AuditEventSettings]
Enabled[]=content.node.add
Enabled[]=content.node.main
```

Die Namen von LoveStack werden zugeordnet: `audit.ini [AuditCompatSettings] Map[location-assign]`,
`Map[location-remove]`, `Map[main-node-update]` und `[AuditSettings] AuditFileNames[]` mit den Dateinamen von
LoveStack; Aufrufe von `eZAudit::writeAudit()` mit diesen Namen landen bei denselben Ereignissen.

### Site-Daten (L14)

```ini
# settings/override/site.ini.append.php
[SiteDataSettings]
TemplateReadable[]=my-flag
```

```tpl
{def $row = fetch( 'content', 'site_data', hash( 'name', 'my-flag' ) )}
{if $row}{$row.value|wash}{/if}
```

Der Fetch gibt das eZSiteData-Objekt oder false zurück (nicht aufgeführt, nicht gespeichert); nur ganze Namen. PHP:
`eZSiteData::createNew()`, `templateReadableNames()`, `isTemplateReadable()`, `fetchList( $filter, $offset, $limit )`,
`fetchListCount( $filter )`. Administration: **Setup > Site-Daten** (`setup/sitedata`), nur lesend, Richtlinie
`setup/system_info`.

### Schaltfläche Kopieren (L15)

`node/view/full.tpl` von admin, admin3 und admin4: eine Schaltfläche Kopieren neben Verschieben, aktiviert mit
`can_create` am Elternknoten, wie im Kontextmenü; sie öffnet `content/copy`.

### Pflicht-Alternativtext (L16) und getrimmte Textzeilen (L17)

Zwei Optionen von Klassenattributen auf der Seite zur Bearbeitung der Klasse, beide standardmäßig aus:

| Option | Datentyp | Feld |
|---|---|---|
| Alternativtext ist erforderlich | `ezimage` | `eZImageType::ALT_TEXT_REQUIRED_FIELD` = `data_int2` |
| Gespeicherten Wert trimmen | `ezstring` | `eZStringType::TRIM_FIELD` = `data_int2` |

```php
eZImageType::isAltTextRequired( $classOrObjectAttribute );
eZImageType::validateImageAltText( $text );
eZImageType::classAltTextRequired( $classOrIdentifierOrId, $attributeIdentifier = false ); // null: no image attribute
eZStringType::isTrimEnabled( $classAttribute );
eZStringType::storedString( $classAttribute, $text );
```

Paket-XML: `<alternative-text-required>1</alternative-text-required>`, `<trim>1</trim>` (nur wenn eingeschaltet).
Geprüft im Formular zur Bearbeitung von Inhalten, beim Upload des Rich-Text-Editors (auch Tiptap) und im AJAX-Uploader
(`UploadAlternativeText`).

### Klassen zum Auflösen von Namensmustern (L18)

```php
interface expNamePatternResolverInterface
{
    // constructed with the rest of the pattern after "{ClassName}"
    public function resolveNamePattern( eZContentObject $object, $version = false,
                                        $translation = false, $length = false,
                                        $sequence = '' );   // returns the name (string)
}
```

```ini
# settings/override/content.ini.append.php
[NamePatternSettings]
ResolverClasses[]=myNameResolver
```

Das Muster für Objektnamen oder URL-Aliase einer Klasse `{myNameResolver}` ruft dann diese Klasse auf. Nicht
aufgeführte Klassen oder solche, die die Schnittstelle nicht implementieren, werden ignoriert und protokolliert. PHP:
`eZNamePatternResolver::resolve( $pattern, $object, $version, $translation, $limit, $sequence )`,
`customResolver( $pattern )`, `limitName( $name, $limit, $sequence )`.

### Standard des Klassenfilters (L19)

```ini
# settings/content.ini (shipped)
[FetchSettings]
# empty: a class_filter_array without a type is ignored, as before; include: it filters
DefaultClassFilterType=
```

PHP: `eZContentFunctionCollection::classFilterType( $type, $array )`.

### Template-Korrekturen (L20, L21)

`content/edit_locations.tpl` (Admin-Designs) wählt für einen vorhandenen Ort „Unverändert“ vor;
`content/datatype/view/ezselection.tpl` und `ezmatrix.tpl` schreiben kein überzähliges `<br />`.

---

## Rich-Text-Editor (L22–L24)

```ini
# settings/override/content.ini.append.php
[CustomTagSettings]
IsHidden[]=quote

# settings/override/ezoe.ini.append.php
[EditorSettings]
# disabled shipped; new installations write enabled
CustomTagControls=enabled
```

Ein Schalter für die drei Editoren: TinyMCE 3 lädt das Plugin `exp_customtag_controls`, wenn er eingeschaltet ist
(kein Eintrag in `Plugins[]` nötig; die Plugin-API von TinyMCE 3 und das jQuery von ezjscore, kein YUI), TinyMCE 8
erhält Verschiebe-Schaltflächen und Tastenkürzel in seiner Werkzeugleiste für Custom Tags (`ez_custom_tag_controls`),
Tiptap dasselbe in exp_oe_tiptap 0.1.2 (`options.customTagControls`). Stylesheet `ezoe_customtags.css` (eingegrenzt,
wirkt nur, wenn eingeschaltet). Tasten: Alt+Umschalt+Pfeil hoch / Pfeil runter / E / Entf; die Rücktaste entfernt
einen leeren Block-Tag.

```php
eZOEXMLInput::hiddenCustomTags( $ini = null );                         // IsHidden[], trimmed, unique
eZOEXMLInput::offeredCustomTags( array $tags, $current = '', $hidden = null );
eZOEXMLInput::customTagPreview( $name, $designBases = null );          // URL of images/editor/custom_tags_thumbs/<name>.png, or ''
eZOEXMLInput::customTagControlsEnabled();
// getCustomTagDefinitions(): each definition also has 'hidden' (bool) and 'preview' (string)
// template attribute: $input_handler.custom_tag_controls_enabled
```

L24 brauchte keine Änderung: `tag_custom.tpl` schloss den Dialog sub/sup bereits bei OK.

---

## APIs und Einstellungen für Entwickler (L25–L31)

```php
eZURI::build( string $url, bool $full = false, bool $htmlEscape = true ): string;          // L25
eZMailTransport::send( eZMail $mail, ?string $transportType = null );                      // L26
eZTemplateDesignResource::setKeyValue( string $key, $value, bool $override = true );       // L31
expErrorHeaderList::build( eZINI $errorINI, $httpErrorCode, $serverProtocol ): array;      // L29
eZINI::includedFiles(): array;   // L28: files and directories read through [Include ...]
```

```ini
# settings/template.ini (shipped), L27
[PHP]
PHPOperatorList[rawurlencode]=rawurlencode
PHPOperatorList[rawurldecode]=rawurldecode

# settings/override/error.ini.append.php, L29
[HTTPError-404]
HeaderList[Cache-Control]=no-store

# extension/ezjscore/settings/ezfind.ini.append.php (shipped), L30
[SearchSettings]
SortBy[]
SortBy[published]=desc
```

### `[Include …]` (L28)

An beliebiger Stelle in einer INI-Datei:

```ini
[Include site.d/*.ini]
```

Der Pfad ist relativ zur einbindenden Datei; Platzhalter sind erlaubt; die Dateien werden an der Stelle, an der die
Zeile steht, in der Reihenfolge ihrer Namen gelesen; die Zeile beendet die Gruppe davor; Includes lassen sich
verschachteln (Tiefenlimit 16); eine Schleife wird mit einer Warnung übersprungen; ein Muster ohne Treffer ergibt einen
Hinweis. Der INI-Cache hält die eingebundenen Dateien und die Verzeichnisse der Muster (Cache-Schlüssel `includes`)
und prüft sie wie die Hauptdateien, wenn die Prüfung der Dateizeit eingeschaltet ist, sodass
`php bin/php/ezcache.php --clear-tag=ini`, der INI-Schritt von `exp:velocity deploy` und das Auffrischen der
Einstellungen von Velocity ihre Änderungen übernehmen. Instanzen mit direktem Zugriff (die Einstellungsseite, der
klassische Einstellungseditor) lösen Includes nicht auf und behalten die Zeile, wie sie geschrieben ist;
`expIniWriter` hat den Eintragstyp `include`.

---

## URLs und Suche (L32–L36)

```ini
# settings/site.ini (shipped)
[URLTranslator]
DecodeOnce=disabled
TrailingCleanup=disabled
```

```php
new eZURI( string $uri, ?bool $decode = null );               // L32: null follows DecodeOnce
eZURI::setURIString( $uri, $fullInitialize = true, ?bool $decode = null );
eZURI::decodeOnce(): bool;
eZCharTransform::trailingCleanup(): bool;                     // L33: '"' reserved in urlalias_iri
$db::MAX_INT; $db::MIN_INT;                                   // L34: signed 32-bit MySQL, PostgreSQL and the base class; 64-bit SQLite, MongoDB
$db->clampInteger( $value ): int;                             // used by eZSearchEngine
```

- L33 fügt nur das doppelte Anführungszeichen hinzu: Das Entfernen nachgestellter `!`, Punkte und Trennzeichen gibt
  es in Exponential seit 2016.
- L35 hat keine Einstellung: Exponential lässt die Relationsfelder verknüpfter Objekte seit 2018 aus dem Index heraus
  (EZP-27101, `eZContentObjectAttribute::metaDataArray( $attributes, true )`, `eZDataType::isRelationType()`).
- L36: Listener des Ereignisses `content/cache` erhalten von jedem Absender `( array $nodeIDs, array $objectIDs )`;
  der Cronjob hide ruft `eZSearch::updateNodeVisibility()` auf.

---

## Migration

```sh
./console exp:lovestack check             # read only; --json; exit 0 nothing to do, 1 attention
./console exp:lovestack migrate           # dry run (default): prints what it would write
./console exp:lovestack migrate --apply   # 'lovestack' version row -> 6.0.15stable; records lovestack-schema-version
```

PHP: `expLoveStack::migrationPlan()` und `expLoveStack::migrate( $apply )` (`kernel/classes/explovestack.php`).
`migrate` schreibt nur die zwei Zeilen in `ezsite_data` (`ezpublish-version`, `lovestack-schema-version`); die Zeile
`db-schema-version` von LoveStack bleibt erhalten; eine andere Versionszeile als `lovestack` wird dem Update-SQL
überlassen; eine Datenbank, die nicht von LoveStack stammt, wird abgelehnt; ein zweiter Lauf ändert nichts. Der Befehl
gibt die zu ändernden Einstellungen aus, übernommen aus `check`. Schritt für Schritt: [migration.md](migration.md).
