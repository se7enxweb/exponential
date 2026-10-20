# PHP-8-Prüfung der Korrekturen von LoveStack

LoveStack enthält etwa vierzig Korrekturen für PHP 7.2 bis 8.3, die Mugo Web zwischen 2017 und 2026 vorgenommen hat.
Exponential, das unter PHP 8.1 bis 8.5 läuft, hatte dieselben Bereiche auf seine eigene Weise korrigiert (zum Beispiel
`#[AllowDynamicProperties]` an `eZPersistentObject` statt deklarierter Eigenschaften, null-sichere Behandlung von
Zeichenketten, Absicherungen für `count()`). Um sicherzugehen, dass nichts übersehen wurde, wurde jede Stelle, die eine
Korrektur von LoveStack nennt, in Exponential als PHPUnit-Test nachgestellt, unter PHP 8.5 und 8.1, und der Code wurde
nur dort geändert, wo der Test zuerst fehlschlug.

Für **Entwickler**; für alle anderen lautet die Kurzfassung: Die Korrekturen, die noch nötig waren, sind in 6.0.15
enthalten, für jede Website eingeschaltet, und bei einem Upgrade ist nichts zu tun.

Testpfade sind relativ zu `tests/tests/`.

## In Exponential korrigiert

| LoveStack-Commit | Stelle | Test | Was sich geändert hat |
|---|---|---|---|
| [`e828c54709`](https://github.com/mugoweb/ezpublish-legacy/commit/e828c54709a8ed4916f43042d6d1b01c1b8c89b8) | Template-Operatoren `get_class` / `is_class` auf einem Wert, der kein Objekt ist | `lib/eztemplate/eZTemplatePHP8RegressionTest.php` | ohne Template-Kompilierung liefern sie jetzt `''` und `false`, wie es der kompilierte Code tat; bisher beendete eine Zeichenkette, Zahl oder null die Anfrage mit einem TypeError |
| [`4065ed16b3`](https://github.com/mugoweb/ezpublish-legacy/commit/4065ed16b39e7d7c25725599b478f3c526af6e43) | `wash(email)` von null | `lib/eztemplate/eZTemplatePHP8RegressionTest.php` | keine Deprecation „Passing null“ (andere Modi von `wash` wandelten bereits um) |
| [`4065ed16b3`](https://github.com/mugoweb/ezpublish-legacy/commit/4065ed16b39e7d7c25725599b478f3c526af6e43) | der klassische HTML-Debug-Bericht mit null als Beschriftung oder Text | `lib/ezutils/eZDebugReportNullLabelTest.php` | beide werden umgewandelt, wie es die Debug-Leiste bereits tat |
| [`4065ed16b3`](https://github.com/mugoweb/ezpublish-legacy/commit/4065ed16b39e7d7c25725599b478f3c526af6e43) | die Debug-Leiste mit null oder einer Zahl als Meldungstext (mit geladenem xdebug) | `lib/ezutils/expDebugBarReportMessageTextTest.php` | keine Deprecation aus `strncmp()` |
| [`4065ed16b3`](https://github.com/mugoweb/ezpublish-legacy/commit/4065ed16b39e7d7c25725599b478f3c526af6e43) | `ezurl`, `ezroot`, `eZURI::transformURI()` einer Zahl; `new eZURI( null )` | `lib/ezutils/eZURITransformURIEscapeNullTest.php` | keine Warnung „Trying to access array offset“ für eine Knoten-ID; `new eZURI( null )` (die Kommandozeile, bevor eine Anfrageadresse bekannt ist) ist eine leere Adresse; `eZURI::build()` unverändert |
| [`75cbb1daa2`](https://github.com/mugoweb/ezpublish-legacy/commit/75cbb1daa2804b129f1a3b395dd44d79ebcf667b) | `eZHTTPTool::createPostVarsFromImageButtons()` mit `$_POST` gleich null | `lib/ezutils/eZHTTPToolPostNullTest.php` | kommt damit zurecht |
| [`1bd250d4c6`](https://github.com/mugoweb/ezpublish-legacy/commit/1bd250d4c6cdfeda4d99a44d56bc1cd801ebf935) | `update/common/scripts/5.4/cleanupfieldvaluerelations.php` mit leeren Relationsdaten | `kernel/classes/eZUpdateScriptCleanupFieldValueRelationsTest.php` | überspringt NULL oder leeres `data_text`, statt mit einem ValueError anzuhalten |
| [`1bd250d4c6`](https://github.com/mugoweb/ezpublish-legacy/commit/1bd250d4c6cdfeda4d99a44d56bc1cd801ebf935) | `eZPostgreSQLDB::escapeString( null )` | `lib/ezdb/eZPostgreSQLDBEscapeStringNullTest.php` | liefert `''`, wie es die Treiber MySQLi, SQLite3 und MongoDB tun |
| [`5a17c08bfc`](https://github.com/mugoweb/ezpublish-legacy/commit/5a17c08bfc761df3fc0d9ce6d117f3cbc9cfc129) | E-Mail-Adressen mit „+“ in den Bestelllisten des Shops | `kernel/classes/eZShopCustomerOrderViewEmailTest.php` | die Links der Bestellliste, des Archivs und des Dashboards wenden `rawurlencode` auf die Adresse an (Designs standard, admin und admin4), und die View dekodiert sie nicht mehr ein drittes Mal; funktioniert mit L32 aus und ein |

## In Exponential bereits behandelt (als Tests behalten)

| LoveStack-Commit | Stelle | Test |
|---|---|---|
| `1bd250d4c6` | Division von Zeichenkette / Ganzzahl in `navigator/google.tpl` | `lib/eztemplate/eZTemplatePHP8RegressionTest.php` |
| `1bd250d4c6` | als Zeichenketten gesendete Felder `ezdatetime`; `ezauthor` ohne E-Mail-Adresse | `kernel/datatypes/eZDatatypePHP8RegressionTest.php` |
| `1bd250d4c6` | `eZPersistentObject` maskiert einen Wert, der keine Zeichenkette ist (MySQLi, SQLite3, MongoDB) | — (null-sicheres `escapeString()`) |
| `b11e85ca5a`, `fd839afd28` | `ezmatrix` mit `data_text` NULL oder leer | `kernel/datatypes/eZDatatypePHP8RegressionTest.php` |
| `a5f16bd9f7` | Validierung von `ezmatrix` für einen Wert, der kein Array ist | `kernel/datatypes/eZDatatypePHP8RegressionTest.php` |
| `a5f16bd9f7`, `4917e57c92`, `f627bcf278` | Interpolation `"${}"` in den Template-Schleifen | `lib/eztemplate/eZTemplatePHP8RegressionTest.php` (außerdem ein Token-Scan der Quellen der Template-Engine) |
| `be78a54e49` | `eZModuleOperationInfo` zählt die Memento-Liste | — (Absicherung mit `is_array()`) |
| `82a7684d6f` | `eZTemplateOptimizer` auf einem nicht zählbaren Wert | `lib/eztemplate/eZTemplatePHP8RegressionTest.php` |
| `bd4fa8600d`, `be26d66906` | `eZTemplateNodeTool::isPHPVariableElement()` von null; die Platzierung eines bool-Knotens durch den Compiler | `lib/eztemplate/eZTemplatePHP8RegressionTest.php` |
| `bd4fa8600d`, `0513dbae32`, `cb8a3a1c58` | entfernte Funktionen (`each()`, `create_function()`, `mysql_escape_string()`), der preg-Modifikator `/e` | `kernel/classes/eZPHP8RemovedFunctionCallsTest.php` (ein Token-Scan von `bin/php`, `kernel`, `lib`, `cronjobs`, `update`) |
| `e8dc1dba4d` | `eZObjectForwarder` zählt eigene Match-Bedingungen | — (Absicherung gegen null) |
| `cb8a3a1c58` | `ezsendmailtransport.php` zählt den Absender; die Roheingabe, der Modifikator `/e` und die Konstruktoren der Rechtschreibprüfung | `extension/ezoe/ezoeSpellcheckRpcRawInputTest.php` |
| `3b6a11496f`, `72009a7f46`, `f8fcc09a57`, `10e40c35dd`, `d7f1480c76`, `6bf0faa5f2` | dynamische Eigenschaften (PHP 8.2) | `kernel/classes/eZDynamicPropertiesPHP82Test.php` (jede Eigenschaft, die LoveStack deklariert hat, gesetzt an 30 Klassen, unter PHP 8.1, 8.2 und 8.5) |

## Nicht übernommen, und warum

| LoveStack-Commit | Punkt | Warum |
|---|---|---|
| `1bd250d4c6` | einen Autor ohne E-Mail-Adresse akzeptieren | eine Verhaltensänderung, keine PHP-8-Korrektur; Exponential lehnt ihn vorerst weiter ab, wie jedes frühere Release |
| `1bd250d4c6` | zusätzliche Konstruktor-Argumente in `ezworkflowtype` und `ezdatatype`, Formulierung in `ezexec.php` | keine PHP-8-Fehler |
| `f627bcf278` | eine spätere Form der Interpolation in der Template-Schleife | die Form von Exponential ist die, die PHP wie beabsichtigt liest |
| `72009a7f46` | eine Änderung eines Klassennamens im selben Commit | in Exponential nicht nötig |
| `3fa3796297` | Konstruktoren im alten Stil in den XML-Handlern | Exponential hat nur `__construct()` |
| `630ccdbada` | der Datentyp `ezenum` in `legacy_2018` | die Erweiterung wird nicht übernommen |
| `6c5b892829`, `e546bf30d4`, `18caa668a4`, `30c1260b84`, `eaf72c6ef3` | Composer-Einschränkungen, Travis | Exponential hat seine eigene `composer.json` (PHP 8.1 bis 8.5) und CI |

Außerdem für Designs außerhalb des Kernels gemeldet: Die Designs ezwebin und ezdemo verlinken weiterhin auf
`shop/customerorderview` ohne `|rawurlencode`; die Änderung an der View lässt „+“ auch für sie funktionieren.
