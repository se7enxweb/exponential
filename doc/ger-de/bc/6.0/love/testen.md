# Testen

Jede LoveStack-Funktion in Exponential ist durch PHPUnit-Tests in `tests/tests/` abgedeckt, die mit der Funktion
geschrieben wurden. Diese Seite führt sie pro Funktion auf und sagt, wie Sie sie ausführen.

## So führen Sie sie aus

Jede Testdatei läuft für sich:

```sh
php vendor/bin/phpunit tests/tests/lib/ezutils/eZURIBuildTest.php
```

Die meisten brauchen keine Datenbank. Die wenigen, die eine brauchen, sind unten als *live* markiert: Sie gehören zur
PHPUnit-Gruppe `database`, laufen auf der Installation, auf der die Tests laufen, innerhalb einer Transaktion, die
zurückgerollt wird, und werden übersprungen, wenn es keine Installation gibt:

```sh
php vendor/bin/phpunit --group database tests/tests/kernel/content/eZAuditLocationTest.php
```

Um nur die Tests der Dateien auszuführen, die eine Änderung berührt hat (statt der ganzen Suite), siehe
[nur die Tests einer Änderung ausführen](../../../funktionen/6.0/qualitaetspruefungen.md#nur-die-tests-einer-änderung-ausführen):
`tests/test-map.json` ordnet Quelldateien, Templates und Skripte den Tests zu, die sie abdecken.

Ein Test prüft jeden Standardwert, den eine aktualisierte Website behält, sodass eine spätere Änderung eine Funktion
nicht versehentlich einschalten kann: `tests/tests/kernel/classes/expShippedFeatureDefaultsTest.php` (L01, L02, L05,
L07, L08, L10, L13, L14, L18, L19, L22, L23, L27, L29, L30, L32, L33).

## Pro Funktion

Pfade sind relativ zu `tests/tests/`.

### Sicherheit und Sitzungen

| ID | Test | Was er prüft |
|---|---|---|
| L01 | `extension/ezjscore/ezjscServerRouterPolicyTest.php` | `ezjscnode` wird ohne *ezjscore / call* abgelehnt, wenn `RequirePolicy=enabled` gilt, mit der Richtlinie erlaubt, unverändert bei `disabled` oder ohne Einstellung; 403 nur, wenn jeder Aufruf aus diesem Grund abgelehnt wird |
| L02 | `kernel/classes/expPolicyOmitListTest.php`, `kernel/content/eZLoginTemplatesRegisterLinkTest.php` | `PolicyOmitListExclude[]` nimmt einen Eintrag heraus, nachdem jede Datei zusammengeführt ist; jedes Anmelde-Template fragt `can_open`, bevor es den Link zum Registrieren zeigt |
| L03 | `kernel/classes/eZUserLogoutTest.php` | nach dem Abmelden sind die Sitzung und ihre ID weg, über jeden Handler, und das Cookie ist mit demselben Pfad, derselben Domain, `Secure`, `HttpOnly`, `SameSite` abgelaufen; eine weiterlaufende Sitzung wird nicht zurückgeschrieben |
| L04 | `kernel/classes/expUserLoginIntegrityTest.php` | Duplikate gefunden (auch nach Groß- und Kleinschreibung), der Index abgelehnt, solange es Duplikate gibt, und danach hinzugefügt |
| L05 | `kernel/classes/expImageUploadValidatorTest.php`, `extension/ezjscore/ezjscServerFunctionsAjaxUploaderImageTest.php` | JPEG, PNG, GIF, WebP, AVIF akzeptiert; eine PHP-Datei namens `.jpg` abgelehnt; SVG abgelehnt, sofern nicht aufgeführt; der AJAX-Uploader lehnt ab, bevor er speichert |
| L06 | `kernel/classes/packages/eZStylePackageCreatorTest.php` | Vorschaubilder von Paketen und Bilder von Styles anhand ihres Inhalts geprüft |
| SameSite | `lib/ezsession/eZSessionSameSiteAliasTest.php` | `SameSite` gelesen, wenn `CookieSameSite` nicht gesetzt ist; `CookieSameSite` gewinnt |

### Diagnose und Protokollierung

| ID | Test | Was er prüft |
|---|---|---|
| L07 | `lib/ezutils/eZDebugDeprecatedTest.php` | eine Deprecation geht nach `deprecated.log`, wird gezählt, wird nicht im Speicher gehalten |
| L08 | `lib/ezfile/eZLogMaxSizeTest.php` | Rotation bei der INI-Größe; die Konstante und `setMaxLogSize()` gewinnen weiterhin |
| L09 | `lib/eztemplate/eZTemplateOperatorDefaultWarningTest.php` | die Warnung nennt Operator und Parameter, einmal |
| L10 | `kernel/common/eZTemplatesStatisticsReporterEditUrlTest.php` | der Bearbeitungslink folgt `UsedTemplateEditUrl`; leer ergibt den üblichen Link |
| L11 | `lib/ezdb/eZDBExceptionCodeTest.php` | `getCode()` ist die Fehlernummer des Treibers |
| L12 | `kernel/classes/eZContentCacheManagerThresholdWarningTest.php` | der Warnungstext mit Zahl der Knoten und Schwellenwert |

### Inhalte, Datentypen und die Administration

| ID | Test | Was er prüft |
|---|---|---|
| L13 | `kernel/content/eZAuditLocationTest.php` (live) | Hinzufügen und Entfernen von Orten und Ändern des Hauptorts schreiben jeweils ihr Ereignis mit den IDs von Objekt und Knoten |
| L14 | `kernel/content/eZContentFunctionCollectionSiteDataTest.php`, `kernel/classes/eZSiteDataCreateTest.php`, `kernel/classes/eZSiteDataCreateNewLiveTest.php` (live) | aufgeführte Namen lesbar, andere nicht; `create()` und `createNew()` |
| L15 | `kernel/content/eZAdminFullViewCopyButtonTest.php` | die Schaltfläche Kopieren zwischen Verschieben und Entfernen, nur mit Erstellrecht am Elternknoten aktiviert |
| L16 | `kernel/classes/datatypes/eZImageTypeAltTextTest.php`, `extension/ezjscore/ezjscServerFunctionsAjaxUploaderAltTextTest.php` | Pflicht-Alternativtext abgelehnt, wenn leer, akzeptiert, wenn angegeben; standardmäßig aus; das Feld des AJAX-Uploaders |
| L17 | `kernel/classes/datatypes/eZStringTypeTrimTest.php` | der gespeicherte Wert wird nur mit der Option getrimmt |
| L18 | `kernel/classes/eZNamePatternResolverClassTest.php`, `kernel/classes/eZNamePatternResolverLimitTest.php` | eine aufgeführte Klasse wird verwendet; eine nicht aufgeführte ignoriert und protokolliert; normale Muster und das Längenlimit unverändert |
| L19 | `kernel/content/eZContentFunctionCollectionClassFilterTypeTest.php` | leere Einstellung: die heutigen Ergebnisse; `include`: gefiltert |
| L20 | `kernel/content/eZEditLocationsVisibilityTest.php` | ein vorhandener Ort beginnt bei „Unverändert“ |
| L21 | `kernel/datatypes/eZSelectionMatrixViewTemplateTest.php` | kein überzähliges `<br />` |

### Rich-Text-Editor

| ID | Test | Was er prüft |
|---|---|---|
| L22 | `extension/ezoe/expOECustomTagListTest.php` | versteckte Tags werden nicht angeboten, aber weiterhin gerendert und bearbeitbar, wo sie existieren |
| L23 | `extension/ezoe/expOECustomTagAssetsTest.php` | das Stylesheet für den Editor und seine Dialoge, gültige Plugin-Skripte, die gestalteten Klassen, das Beispielbild im Dialog |
| L24 | — | ein JavaScript-Dialog, im Browser unter TinyMCE 3 und Tiptap geprüft |

### APIs und Einstellungen für Entwickler

| ID | Test | Was er prüft |
|---|---|---|
| L25 | `lib/ezutils/eZURIBuildTest.php` | relative und vollständige URLs, Siteaccess in den Modi URL und Host, dasselbe Ergebnis wie `ezurl` |
| L26 | `lib/ezutils/eZMailTransportSendTest.php` | der angegebene Transport wird nur für eine Nachricht verwendet |
| L27 | `kernel/common/eZTemplateRawURLEncodeOperatorTest.php` | `rawurlencode` / `rawurldecode` und ein nicht gesetzter Wert mit und ohne Kompilierung |
| L28 | `lib/ezutils/eZINIIncludeTest.php` | relatives Glob, Verschachtelung, Schleifen, eine fehlende Datei, ein Override, das einbindet, der INI-Cache, der die eingebundenen Dateien festhält und ihre Änderungen sieht |
| L29 | `kernel/error/ErrorHeaderListTest.php` | Header aus `HeaderList[]`, ungültige weggelassen, aus dem View-Cache erneut gesendet |
| L30 | `extension/ezjscore/ezjscServerFunctionsJsSearchSortByTest.php` | die Reihenfolge der Suche aus `SortBy[]` |
| L31 | `kernel/common/eZTemplateDesignResourceSetKeyValueTest.php` | `setKeyValue()` und der Schlüssel `layout` |

### URLs und Suche

| ID | Test | Was er prüft |
|---|---|---|
| L32 | `lib/ezutils/eZSysRequestURIDecodeOnceTest.php` | aus: die heutige Dekodierung; ein: `%2B` bleibt `+` |
| L33 | `lib/ezi18n/eZCharTransformTrailingCleanupTest.php` | aus: Aliase unverändert; ein: die Fälle von LoveStack |
| L34 | `kernel/search/eZSearchEngineIntegerClampTest.php` | Werte jenseits des Wertebereichs der Spalte werden ohne Fehler indiziert, pro Treiber |
| L35 | `kernel/datatypes/eZObjectRelationMetaDataTest.php` | Relationsfelder verknüpfter Objekte weggelassen, ein Zyklus von Relationen eingeschlossen |
| L36 | `kernel/classes/runnable/HideCronjobSearchIndexTest.php`, `kernel/classes/eZContentCacheEventSendersTest.php` | der Cronjob hide aktualisiert den Index; jeder Absender von `content/cache` übergibt Objekt-IDs |

### PHP-8-Prüfung, Migration und die eigenen Tests von LoveStack

- PHP-8-Prüfung: ein Regressionstest pro Stelle, aufgeführt in [php8-pruefung.md](php8-pruefung.md).
- Migration: `kernel/classes/expLoveStackCheckTest.php` und `kernel/classes/expLoveStackMigrateTest.php` auf
  Fixture-Datenbanken in der Form von LoveStack und von Exponential; `check` schreibt nichts, und `migrate` ändert nur
  seine zwei Zeilen und ist idempotent.
- Die eigenen Testfälle von LoveStack für Verhalten, das Exponential hat: [zuordnung-der-tests.md](zuordnung-der-tests.md).

Die URL-Optionen L32 und L33 wurden außerdem über die URL-Aliase einer ganzen Website mit ausgeschalteten und
eingeschalteten Optionen verglichen: Das Einschalten von L32 änderte keinen der Anfragepfade, und das Einschalten von
L33 änderte nur die IRI-Aliase, die ein doppeltes Anführungszeichen enthalten.
