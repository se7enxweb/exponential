# Die Tests von LoveStack in der Testsuite von Exponential

Neben dem Test jeder Funktion hat Exponential den eigenen `tests/`-Baum von LoveStack mit seinem eigenen verglichen.
Mugo Web hat Testdateien in 18 Commits seit dem gemeinsamen Vorfahren (`3429aa91bb`, 9. März 2016) bis `e61e9fdf0c`
(23. Februar 2026) geändert, insgesamt 173 Dateien; bei 152 davon änderte sich nur der Dateimodus. Jeder Testfall unter
den übrigen, der Verhalten abdeckt, das Exponential hat, ist jetzt in der PHPUnit-Suite von Exponential, für PHPUnit so
neu gebaut, wie Exponential es ausführt, mit Dank an Mugo Web. Jede dieser Testdateien nennt den LoveStack-Commit, aus
dem sie stammt.

Pfade sind in Exponential relativ zu `tests/tests/` und in LoveStack relativ zu `tests/tests/`. Als *live* markierte
Tests brauchen eine Installation und rollen zurück, was sie schreiben; die anderen brauchen keine Datenbank.

| LoveStack-Commit | Test in LoveStack | In Exponential | Status |
|---|---|---|---|
| [`f2284944b2`](https://github.com/mugoweb/ezpublish-legacy/commit/f2284944b27f0ba38e2f41876804ae9400fd25ce) | `lib/ezutils/ezuri_test.php`: `testBuildFullUrl`, `testBuildRelativeUrl` | `lib/ezutils/eZURIBuildTest.php` (L25): beide Fälle mit denselben Eingaben und Ergebnissen, dazu der Modus Host, Maskierung und Gleichheit mit `ezurl` | abgedeckt |
| [`95d86f5990`](https://github.com/mugoweb/ezpublish-legacy/commit/95d86f5990ba83be39e4871bd88c2ecf120e8099) | `kernel/classes/eznamepatternresolver_regression.php` | `kernel/classes/eZNamePatternResolverLimitTest.php`: die Fälle des Längenlimits aus EZP-21410 (ASCII, Leerraum, Komma und Punkt, UTF-8 mit und ohne ideografisches Komma) über `eZNamePatternResolver::resolve()` und den Konstruktor | übernommen; Exponential behält den Konstruktor statt `instance()` von LoveStack (L18) |
| [`e280ff5d64`](https://github.com/mugoweb/ezpublish-legacy/commit/e280ff5d64288f1c441ff83d472d20b38bffa1f0) | `kernel/classes/ezcontentobjecttreenode_test2.php`: `testSortingByTrashed` | `kernel/classes/eZContentObjectTreeNodeSortingTest.php`: aufsteigend (der Fall von LoveStack), absteigend, Kurzform, kombinierte Schlüssel | übernommen |
| [`60a40cdc05`](https://github.com/mugoweb/ezpublish-legacy/commit/60a40cdc053107bfdb08b343f80cb4659501b56e) | `kernel/datatypes/ezuser/ezusertype_regression.php`: `testUpdatePasswordUpdatesSerializedData` | `kernel/datatypes/ezuser/eZUserTypePasswordDraftLiveTest.php` (live): der gespeicherte Hash ist der des neuen Passworts, die Daten des Attributs enthalten den neuen Hash, eine ID ohne Konto wird abgelehnt | übernommen |
| [`30c1260b84`](https://github.com/mugoweb/ezpublish-legacy/commit/30c1260b840d747740b9cd93bc76a8ed1db5a7ae), [`09e49ce1ff`](https://github.com/mugoweb/ezpublish-legacy/commit/09e49ce1ffb27835b73d13dd0f7cafb6452c56d1) | `kernel/private/rest/request_parser_data/*`: aufgezeichnete REST-Anfragen | `kernel/private/rest/ezpRestHttpRequestParserRecordedRequestsTest.php`: jede aufgezeichnete Anfrage gegen ihr erwartetes Ergebnis, die Reihenfolge von Accept nach Qualität | übernommen (die Daten waren bereits in Exponential) |
| [`d894b9e885`](https://github.com/mugoweb/ezpublish-legacy/commit/d894b9e8852467f9cd7ee01113378b535beba23b) | `kernel/common/ezautolinkoperator_test.php`: http, https, mail, ftp | `kernel/common/eZAutoLinkOperatorLinksTest.php`: die vier Fälle mit den Ergebnissen von LoveStack, gekürzter Text, ein nicht aufgeführtes Schema | übernommen (die Korrektur selbst hatte Exponential über Upstream erreicht) |
| [`5286068d5b`](https://github.com/mugoweb/ezpublish-legacy/commit/5286068d5b6620f8aa3572db2063a9b5da02af65) | `ezsimplifiedxmlinputparser_regression.php`: `testParseAttributeWithAmpersand` | `kernel/classes/xmltext/eZSimplifiedXMLInputParserTest.php`: ein einzelnes `&` in einer Linkadresse (`<link>` und `<a>`) | übernommen |
| [`7517c0e925`](https://github.com/mugoweb/ezpublish-legacy/commit/7517c0e925178e5f932ac653439a39203ec03cb0) | `lib/ezi18n/ezchartransform_test.php`: nachgestellte Zeichen von URL-Aliasen | `lib/ezi18n/eZCharTransformTrailingCleanupTest.php` (L33): die fünf Eingaben von LoveStack, mit ausgeschalteter und eingeschalteter Option | abgedeckt |
| [`55029bd473`](https://github.com/mugoweb/ezpublish-legacy/commit/55029bd473e47e73b86f748f0d2d264a5e14843a), [`4a27ffecb4`](https://github.com/mugoweb/ezpublish-legacy/commit/4a27ffecb4a5fb8f7b17094c0253fe28a89832fa) | `kernel/classes/ezsitedata_test.php`: `testCreate` | `kernel/classes/eZSiteDataCreateTest.php` (`create()` ohne Datenbank), `kernel/classes/eZSiteDataCreateNewLiveTest.php` (live: `createNew()` speichert und ersetzt nach Name) | übernommen |
| `cb8a3a1c58`, `30c1260b84`, `54c9fd9e3f`, `dfa6f4c9ac`, `09e49ce1ff` (teilweise) | Tests von Workflow-Ereignissen, der Aufbau der Datenbank-Testsuite, das Überspringen ausgemusterter Testserver, Normalisierung des Exports von PHP 5.5 | dieselben Änderungen sind bereits in den Dateien von Exponential | bereits in Exponential |
| `0a52081473` | Siteaccess-Tests umbenannt von `plain`/`admin` in `test` | — | nicht übernommen: folgt dem Entfernen der Beispiel-Siteaccesses, das Exponential nicht vornimmt |
| `db5e171aef` | eine Debug-Beschriftung im Test des Freigabe-Workflows | — | nicht übernommen: Formulierung einer Debug-Beschriftung |
| `eaf72c6ef3` | `tests/runtests.php` lädt `vendor/autoload.php` | — | nicht nötig: Die Tests von Exponential laden es über `tests/bootstrap.php` |
| `9f5b99fd2a` | 152 Dateien: das Ausführungsrecht entfernt | — | nur Dateimodi, kein Testfall |

## Für die Funktionen hinzugefügte Tests

Neben den Tests pro Funktion, die in [testen.md](testen.md) aufgeführt sind, wurden diese Tests hinzugefügt, als die
Funktionen als Ganzes geprüft wurden:

- `kernel/classes/expShippedFeatureDefaultsTest.php`: die Standardwerte, die eine aktualisierte Website behält;
- `kernel/content/eZAdminFullViewCopyButtonTest.php` (L15),
  `kernel/content/eZEditLocationsVisibilityTest.php` (L20),
  `kernel/datatypes/eZSelectionMatrixViewTemplateTest.php` (L21): die Templates;
- `extension/ezjscore/ezjscServerFunctionsAjaxUploaderImageTest.php` (L05) und
  `ezjscServerFunctionsAjaxUploaderAltTextTest.php` (L16): der AJAX-Uploader;
- `kernel/content/eZLoginTemplatesRegisterLinkTest.php` (L02), `extension/ezoe/expOECustomTagAssetsTest.php`
  (L22, L23), `kernel/classes/packages/eZStylePackageCreatorTest.php` (L06),
  `kernel/classes/eZContentCacheEventSendersTest.php` (L36);
- `kernel/common/eZTemplateRawURLEncodeOperatorTest.php` (L27), der festgestellt hat, dass die Operatoren für die
  URL-Kodierung unter PHP 8.1 und neuer null an PHP übergaben, wenn ein Wert nicht gesetzt war; korrigiert.

Bewusst ohne PHPUnit-Test: `destroy()` des Symfony-Session-Handlers (es kehrt auf der Kommandozeile vorzeitig zurück;
sein Verhalten ist durch den Test des Abmeldens abgedeckt) und L24 (ein JavaScript-Dialog, im Browser geprüft).
