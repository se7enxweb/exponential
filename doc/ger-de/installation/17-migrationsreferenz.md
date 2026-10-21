# 17. Migrationsreferenz

Dieses Kapitel ist der Referenzanhang zu den drei vorangehenden Migrationskapiteln. Es enthält die Tabellen, auf die
diese Kapitel verweisen, statt sie zu wiederholen: jede Tabelle des Kernel-Schemas von Exponential 6.0 mit ihrem Zweck und
ihrem Namen in eZ Platform und Ibexa; jeden Kernel-Datentyp mit den Spalten, in denen er seinen Wert speichert, und dem
passenden Feldtyp; die INI-Einstellungen, die ein YAML-Gegenstück haben; Template-Konstrukte neben ihren
Twig-Entsprechungen; die Legacy-PHP-Klassen neben den Diensten der Public API; Passwort-Hash-Typen, URL-Alias-Tabellen und
Speicherpfade für Bilder; jedes Upgrade-, Reparatur- und Konvertierungsskript der Installation; eine Liste von
Datenprüfungen, die vor dem Verschieben von Inhalten auszuführen sind; und ein Glossar der Namen, die sich zwischen den
Generationen geändert haben. Die alten Produktnamen (eZ Publish 4.x und 5.x, eZ Platform, Ibexa DXP) erscheinen nur dort,
wo sie das System bezeichnen, von dem eine Website kommt oder zu dem sie wechselt.

[Inhalt](README.md) · Zurück: [16. Migration von eZ Platform und Ibexa](16-migration-von-ez-platform-und-ibexa.md)

## Inhalt dieses Kapitels

- [17.1 Wie die Tabellen geprüft wurden](#171-wie-die-tabellen-geprüft-wurden)
- [17.2 Referenz des Datenbankschemas](#172-referenz-des-datenbankschemas)
- [17.3 Datentypen und Feldtypen](#173-datentypen-und-feldtypen)
- [17.4 Einstellungen: INI und YAML](#174-einstellungen-ini-und-yaml)
- [17.5 Templates: TPL und Twig](#175-templates-tpl-und-twig)
- [17.6 PHP-API: Legacy-Klassen und Dienste der Public API](#176-php-api-legacy-klassen-und-dienste-der-public-api)
- [17.7 Passwörter, URL-Aliase und Bildspeicherung](#177-passwörter-url-aliase-und-bildspeicherung)
- [17.8 Datenprüfungen vor der Migration](#178-datenprüfungen-vor-der-migration)
- [17.9 Werkzeuge für Migration und Upgrade](#179-werkzeuge-für-migration-und-upgrade)
- [17.10 Glossar umbenannter Begriffe](#1710-glossar-umbenannter-begriffe)
- [Quellen](#quellen)

So verwenden Sie es: Beginnen Sie mit dem Kapitel Ihres Weges ([14](14-migration-von-4x.md) für 4.x,
[15](15-migration-von-5x-legacy.md) für den 5.x-Stack, [16](16-migration-von-ez-platform-und-ibexa.md) für eZ
Platform und Ibexa) und kommen Sie hierher, wenn ein Schritt einen Namen braucht. Führen Sie vor jedem Umzug die Prüfungen
aus [17.8](#178-datenprüfungen-vor-der-migration) auf einer Kopie der Datenbank aus: Sie finden die Daten, die die andere Seite
ablehnen wird, solange sie noch günstig zu beheben sind. Bei einem Umzug auf die Plattform entscheidet die Datentyptabelle
in [17.3](#173-datentypen-und-feldtypen), was konvertiert werden muss; bei einem Umzug zu Exponential zeigen Ihnen die
Spalten „Ibexa 4.6“ und „Ibexa 5“ in [17.2](#172-referenz-des-datenbankschemas), welche Ihrer Tabellen Exponential liest und
welche es ignoriert.

## 17.1 Wie die Tabellen geprüft wurden

Jeder Name in diesem Kapitel wurde an Code oder Daten überprüft, und zwar an einer dieser Stellen:

| Quelle | Wofür sie verwendet wurde |
|---|---|
| Dieses Repository | `share/db_schema.dba` und `kernel/sql/{mysql,postgresql,sqlite}/`, `kernel/classes/datatypes/*/`, `settings/*.ini`, `kernel/common/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplateautoload.php`, `kernel/classes/*.php`, `bin/php/`, `update/` |
| Eine Ibexa-DXP-4.6-Installation auf demselben Server (`ibexa/core` v4.6.28) | Die Methoden der Public-API-Dienste, die Namen der Twig-Funktionen, die Aliase der Feldtypen (`fieldtypes.yml`) und das Schema des Legacy-Speichers (`schema.yaml`) |
| Die Ibexa-5-Referenzinstallation der Media-Site, die für die Portierung der Media-Site verwendet wurde (nur lesend) | Die Namen und Spalten der `ibexa_*`-Tabellen, die in ihren Inhalten gespeicherten Feldtyp-Bezeichner, der verwendete Passwort-Hash-Typ, der Aufbau der Bildpfade sowie echte `ibexa.yaml`-Konfiguration für Siteaccesses, Bildvarianten, DFS und HTTP-Cache |
| Die Migrationsseiten und die Feldtyp-Referenz des Herstellers ([Quellen](#quellen)) | Die dokumentierte Zuordnung der Feldtypen, die nicht unterstützten Feldtypen, Attributtypen für benutzerdefinierte Tags und die häufigen Probleme nach einer Migration |

Die Referenzdatenbank (SQLite) wurde nur lesend geöffnet und ausschließlich mit `SELECT`-Anweisungen abgefragt. Alles,
was sich an keiner dieser Stellen überprüfen ließ, ist mit **(hier nicht geprüft)** gekennzeichnet.

Ibexa DXP 4.6 verwendet noch die Legacy-Tabellennamen (`ezcontentobject`, `ezuser` ...) und die Legacy-Bezeichner der
Feldtypen (`ezstring`, `ezimage` ...). Die Ibexa-5-Datenbank benennt beides um: Tabellen werden zu `ibexa_*`, wobei einige
Spalten umbenannt werden, und Feldtypen werden zu `ibexa_*`. Wo sie sich unterscheiden, sind unten beide Spalten angegeben.

## 17.2 Referenz des Datenbankschemas

Das Kernel-Schema von Exponential 6.0 umfasst **128 Tabellen**: `share/db_schema.dba`, `kernel/sql/mysql/kernel_schema.sql`
und `kernel/sql/postgresql/kernel_schema.sql` führen dieselben 128 auf. `kernel/sql/sqlite/schema.sql` hat drei weitere,
die zu Extensions gehören, für SQLite aber vorab angelegt werden: `ezgmaplocation` (ezgmaplocation), `ezstarrating` und
`ezstarrating_data` (ezstarrating). Die Cluster-Tabellen (`ezdfsfile`, `ezdfsfile_cache`) stehen in der separaten Datei
`kernel/sql/mysql/cluster_dfs_schema.sql`.

Legende für die letzten beiden Spalten: der Name der Tabelle im Schema des Legacy-Speichers von Ibexa DXP 4.6 und in der
Ibexa-5-Referenzdatenbank; „gleich“ bedeutet derselbe Name; „—“ bedeutet, dass die Tabelle dort nicht existiert.

### Inhalts-Repository

| Tabelle | Zweck | Ibexa 4.6 | Ibexa 5 |
|---|---|---|---|
| `ezcontentobject` | Eine Zeile pro Inhaltsobjekt: Klasse, Eigentümer, Sektion, aktuelle Version, Sprachmaske, Remote-ID | gleich (ergänzt `is_hidden`) | `ibexa_content` (`contentclass_id` heißt `content_type_id`; ergänzt `is_hidden`) |
| `ezcontentobject_version` | Versionen eines Objekts, mit Status und Ersteller | gleich | `ibexa_content_version` |
| `ezcontentobject_attribute` | Feldwerte pro Objekt, Version und Sprache (`data_text`, `data_int`, `data_float`, `sort_key_int`, `sort_key_string`) | gleich | `ibexa_content_field` (`contentclassattribute_id` heißt `content_type_field_definition_id`) |
| `ezcontentobject_name` | Objektname pro Version und Sprache | gleich | `ibexa_content_name` |
| `ezcontentobject_link` | Beziehungen zwischen Objekten (`relation_type` 1 allgemein, 2 Einbettung, 4 Link, 8 Attribut) | gleich | `ibexa_content_relation` |
| `ezcontentobject_tree` | Knoten: der Baum, mit `path_string`, `depth`, Sortierfeld und -reihenfolge, Sichtbarkeit | gleich | `ibexa_content_tree` |
| `ezcontentobject_trash` | Knoten im Papierkorb | gleich | `ibexa_content_trash` |
| `eznode_assignment` | Wo eine Version bei der Veröffentlichung platziert wird | gleich | `ibexa_node_assignment` |
| `ezcontent_language` | Sprachen und ihr Bit in den Sprachmasken | gleich | `ibexa_content_language` |
| `ezcontentclass` | Inhaltsklassen pro Version | gleich | `ibexa_content_type` (`version` heißt `status`) |
| `ezcontentclass_attribute` | Klassenattribute mit ihrem Datentyp und ihren Einstellungen (`data_int1..4`, `data_text1..5`, `data_float1..4`) | gleich (ergänzt `is_thumbnail`) | `ibexa_content_type_field_definition` |
| `ezcontentclass_name` | Klassennamen pro Sprache | gleich | `ibexa_content_type_name` |
| `ezcontentclassgroup` | Klassengruppen | gleich (ergänzt `is_system`) | `ibexa_content_type_group` |
| `ezcontentclass_classgroup` | Zuordnungen von Klassen zu Gruppen | gleich | `ibexa_content_type_group_assignment` |
| `ezcobj_state`, `ezcobj_state_group`, `ezcobj_state_language`, `ezcobj_state_group_language`, `ezcobj_state_link` | Objektzustände, ihre Gruppen, Übersetzungen und der Zustand jedes Objekts | gleich | `ibexa_object_state`, `ibexa_object_state_group`, `ibexa_object_state_language`, `ibexa_object_state_group_language`, `ibexa_object_state_link` |
| `ezsection` | Sektionen | gleich | `ibexa_section` |
| `ezsite_data` | Schlüssel-Wert-Paare wie die Schemaversion (`ezpublish-version`) | gleich | `ibexa_site_data` |
| `ezpackage` | Installierte Pakete | gleich | `ibexa_package` |
| `ezpending_actions` | Zurückgestellte Arbeiten wie Index-Aktualisierungen (`action`, `param`) | — | — |
| `ezpublishingqueueprocesses` | Warteschlange für asynchrones Veröffentlichen | — | — |

### Datentyp-Speicher

| Tabelle | Zweck | Ibexa 4.6 | Ibexa 5 |
|---|---|---|---|
| `ezbinaryfile` | Dateien von `ezbinaryfile`-Attributen | gleich | `ibexa_binary_file` |
| `ezmedia` | Dateien und Player-Einstellungen von `ezmedia`-Attributen | gleich | `ibexa_media` |
| `ezimagefile` | Jeder Dateipfad, den ein `ezimage`-Attribut verwendet (Original und Aliase) | gleich | `ibexa_image_file` |
| `ezkeyword`, `ezkeyword_attribute_link` | Schlüsselwörter und ihre Verknüpfungen mit Attributen | gleich (Verknüpfung ergänzt `version`) | `ibexa_keyword`, `ibexa_keyword_field_link` (ergänzt `version`) |
| `ezurl`, `ezurl_object_link` | Externe URLs und die Attribute, die sie verwenden | gleich | `ibexa_url`, `ibexa_url_content_link` |
| `ezenumvalue`, `ezenumobjectvalue` | `ezenum`-Optionen und gewählte Werte | — | — |
| `ezmultipricedata` | Preise pro Währung von `ezmultiprice`-Attributen | — | — |
| `ezisbn_group`, `ezisbn_group_range`, `ezisbn_registrant_range` | ISBN-13-Bereiche für die Gliederung mit Bindestrichen und die Validierung | — | — |
| `ezproductcategory` | Produktkategorien (Datentyp `ezproductcategory`) | — | — |
| `ezsubtree_notification_rule` | Teilbaum-Abonnements (Datentyp `ezsubtreesubscription`) | — | — |
| `ezinfocollection`, `ezinfocollection_attribute` | Aus Formularen gesammelte Informationen | — | gleich (erhalten durch das Netgen-Bundle für Informationssammlung, nicht durch Ibexa) |

### Benutzer, Rollen und Sitzungen

| Tabelle | Zweck | Ibexa 4.6 | Ibexa 5 |
|---|---|---|---|
| `ezuser` | Login, E-Mail, Passwort-Hash und Hash-Typ pro Benutzerobjekt | gleich (ergänzt `password_updated_at`) | `ibexa_user` (ergänzt `password_updated_at`) |
| `ezuser_setting` | Aktiviert-Kennzeichen und maximale Anzahl gleichzeitiger Anmeldungen | gleich | `ibexa_user_setting` |
| `ezuser_accountkey` | Schlüssel zur Kontoaktivierung | gleich | `ibexa_user_accountkey` |
| `ezuser_role` | Rollenzuweisungen mit optionaler Einschränkung auf Teilbaum oder Sektion | gleich | `ibexa_user_role` |
| `ezrole`, `ezpolicy`, `ezpolicy_limitation`, `ezpolicy_limitation_value` | Rollen, Richtlinien und ihre Einschränkungen | gleich | `ibexa_role`, `ibexa_policy`, `ibexa_policy_limitation`, `ibexa_policy_limitation_value` |
| `ezpreferences` | Benutzerbezogene Präferenzen | gleich | `ibexa_user_preference` |
| `ezuservisit` | Letzter Besuch, Anzahl der Anmeldungen, fehlgeschlagene Anmeldeversuche | — | — |
| `ezforgot_password` | Schlüssel zum Zurücksetzen des Passworts | — | — |
| `ezsession` | Sitzungsspeicher in der Datenbank | — | — |
| `ezuser_discountrule` | Rabattregeln pro Benutzer (Shop) | — | — |
| `ezcontentbrowsebookmark` | Lesezeichen (Exponential ergänzt `folder_id`, `priority`) | gleich (ohne Ordnerspalten) | `ibexa_content_bookmark` |
| `ezcontentbrowserecent` | Zuletzt durchsuchte Knoten | — | — |
| `expbookmark_folder` | Lesezeichenordner (Exponential) | — | — |

### URLs und Suche

| Tabelle | Zweck | Ibexa 4.6 | Ibexa 5 |
|---|---|---|---|
| `ezurlalias_ml` | Mehrsprachige URL-Aliase, eine Zeile pro Pfadelement und Sprache | gleich | `ibexa_url_alias_ml` |
| `ezurlalias_ml_incr` | ID-Sequenz für `ezurlalias_ml` | gleich | `ibexa_url_alias_ml_incr` |
| `ezurlalias` | Die URL-Alias-Tabelle aus der Zeit vor 4.0, die nur vom Import in `updateniceurls.php` gelesen wird | gleich | `ibexa_url_alias` |
| `ezurlwildcard` | URL-Platzhalter | gleich | `ibexa_url_wildcard` |
| `ezsearch_word`, `ezsearch_object_word_link` | Der eingebaute Suchindex | gleich | `ibexa_search_word`, `ibexa_search_object_word_link` |
| `ezsearch_search_phrase` | Suchstatistiken | — | — |

### Workflows, Benachrichtigungen und Zusammenarbeit

| Tabelle | Zweck | Ibexa 4.6 | Ibexa 5 |
|---|---|---|---|
| `ezworkflow`, `ezworkflow_event`, `ezworkflow_group`, `ezworkflow_group_link`, `ezworkflow_assign`, `ezworkflow_process` | Workflows, ihre Ereignisse und laufende Prozesse | — | — |
| `eztrigger` | Welcher Workflow bei welcher Operation ausgeführt wird | — | — |
| `ezoperation_memento`, `ezmodule_run` | Zustand von Operationen und Modulen, die von einem Workflow unterbrochen wurden | — | — |
| `ezwaituntildatevalue` | Einstellungen des Ereignisses „Warten bis Datum“ | — | — |
| `ezapprove_items` | Elemente, die auf Freigabe warten | — | — |
| `ezcollab_group`, `ezcollab_item`, `ezcollab_item_group_link`, `ezcollab_item_message_link`, `ezcollab_item_participant_link`, `ezcollab_item_status`, `ezcollab_notification_rule`, `ezcollab_profile`, `ezcollab_simple_message` | Das System für Zusammenarbeit (Freigabenachrichten) | — | — |
| `eznotificationevent`, `eznotificationcollection`, `eznotificationcollection_item`, `ezgeneral_digest_user_settings` | Benachrichtigungsereignisse, Mail-Warteschlange und Einstellungen für Sammelbenachrichtigungen | — | — |
| `ezmessage` | Ausgehende Benachrichtigungen | — | — |
| `ezscheduled_script` | Fortschritt lang laufender Skripte | — | — |

### Shop

| Tabelle | Zweck | Ibexa 4.6 | Ibexa 5 |
|---|---|---|---|
| `ezbasket`, `ezwishlist` | Warenkörbe und Wunschlisten | — | — |
| `ezproductcollection`, `ezproductcollection_item`, `ezproductcollection_item_opt` | Produkte in einem Warenkorb oder einer Bestellung | — | — |
| `ezorder`, `ezorder_item`, `ezorder_status`, `ezorder_status_history`, `ezorder_nr_incr` | Bestellungen, ihr Status und die Sequenz der Bestellnummern | — | — |
| `ezpaymentobject` | Zustand des Zahlungs-Gateways | — | — |
| `ezcurrencydata` | Währungen und Kurse | — | — |
| `ezvattype`, `ezvatrule`, `ezvatrule_product_category` | Mehrwertsteuertypen und -regeln | — | — |
| `ezdiscountrule`, `ezdiscountsubrule`, `ezdiscountsubrule_value` | Rabattregeln | — | — |

### Weitere Kernel-Funktionen

| Tabelle | Zweck | Ibexa 4.6 | Ibexa 5 |
|---|---|---|---|
| `ezrss_export`, `ezrss_export_item`, `ezrss_import` | Ausgehende und eingehende RSS-Feeds | — | — |
| `ezrss_export_opml_item` | Elemente des OPML-Exports (Exponential) | — | — |
| `ezpdf_export` | PDF-Exporte | — | — |
| `eztipafriend_counter`, `eztipafriend_request` | Weiterempfehlen („Tip a friend“) | — | — |
| `ezview_counter` | Seitenaufrufe pro Knoten | — | — |
| `ezprest_clients`, `ezprest_authorized_clients`, `ezprest_authcode`, `ezprest_token` | OAuth-Clients und -Tokens der REST-API | — | — |
| `expaudit_event`, `expaudit_cursor`, `expaudit_file` | Das Audit-Protokoll (Exponential) | — | — |
| `expmail_category`, `expmail_preference`, `expmail_consent_log`, `expmail_pending`, `expmail_suppression` | Mail-Präferenzen, Einwilligungen und Sperrlisten (Exponential) | — | — |

### Tabellen, die es nur auf der Symfony-Plattform gibt

| Ibexa 4.6 | Ibexa 5 | Zweck |
|---|---|---|
| `ezcontentclass_attribute_ml` | `ibexa_content_type_field_definition_ml` | Übersetzte Namen, Beschreibungen und Einstellungen von Felddefinitionen |
| `eznotification` | `ibexa_notification` | Benachrichtigungen der Administrationsoberfläche |
| `ibexa_setting` | `ibexa_setting` | Plattform-Einstellungen |
| `ibexa_token`, `ibexa_token_type` | gleich | Tokens (zum Beispiel zum Zurücksetzen des Passworts) |
| `ezdfsfile` | `ibexa_dfs_file` | Metadaten des DFS-Clusters; in Exponential kommt dieselbe Tabelle aus `kernel/sql/mysql/cluster_dfs_schema.sql` |
| `ezgmaplocation` | `ibexa_map_location` | Kartenorte; in Exponential gehört die Tabelle zur Extension ezgmaplocation |
| — | `ibexa_user_invitation`, `ibexa_user_invitation_assignment` | Benutzereinladungen |
| — | `ibexa_messenger_messages`, `ibexa_messenger_lock_keys` | Warteschlange von Symfony Messenger |

Extensions bringen ihre eigenen Tabellen mit, die auf beiden Seiten ihre Namen behalten, wenn die Extension ein
Gegenstück auf der Plattform hat: `eztags`, `eztags_keyword` und `eztags_attribute_link` (eztags und das Netgen Tags
Bundle) sind unter diesen Namen in der Ibexa-5-Referenzdatenbank vorhanden, ebenso `sckenhancedselection`
(enhancedselection2). Die Tabellen `ezm_block` und `ezm_pool` von `ezflow` (`extension/ezflow/sql/`) haben kein
Gegenstück; siehe den Page-Feldtyp in [17.3](#173-datentypen-und-feldtypen).

## 17.3 Datentypen und Feldtypen

Der Kernel registriert 35 Datentypen (`settings/content.ini` `[DataTypeSettings] AvailableDataTypes[]`). Jeder ist in
`kernel/classes/datatypes/<name>/<name>type.php` implementiert, und der unten angegebene `data_type_string` ist die
Konstante `DATA_TYPE_STRING` dieser Klasse. Die Speicherspalten sind die von `ezcontentobject_attribute`. Der
Sortierschlüssel ist das, was `sortKeyType()` zurückgibt: `int` füllt `sort_key_int`, `string` füllt `sort_key_string`,
und `false` füllt keine von beiden (`kernel/classes/ezcontentobjectattribute.php`).

Der Bezeichner für Ibexa 4.6 ist der Alias in der `fieldtypes.yml` von `ibexa/core` (oder im genannten Bundle); „Null“
bedeutet, dass 4.6 den Bezeichner mit dem Null-Feldtyp registriert, der den gespeicherten Wert lädt, ohne ihn zu
verstehen. Der Bezeichner für Ibexa 5 ist derjenige aus der Feldtyp-Referenz oder, wo vermerkt, derjenige, der in der
Referenzdatenbank gespeichert ist.

| Datentyp (`data_type_string`) | Gespeichert in | Sortierschlüssel | Externe Tabellen | Ibexa 4.6 | Ibexa 5 | Hinweise |
|---|---|---|---|---|---|---|
| `ezauthor` | `data_text`: XML `<ezauthor><authors><author .../>` | — | — | `ezauthor` | `ibexa_author` | Gleicher Wert: eine Liste von Paaren aus Name und E-Mail-Adresse |
| `ezbinaryfile` | die `ezbinaryfile`-Zeile (Attribut-ID und Version) | — | `ezbinaryfile` | `ezbinaryfile` | `ibexa_binaryfile` (in der Referenzdatenbank gespeichert) | Dateien unter `<storage>/original/<mime group>/`; siehe [17.7](#177-passwörter-url-aliase-und-bildspeicherung) |
| `ezboolean` | `data_int` 0 oder 1 | int | — | `ezboolean` | `ibexa_boolean` (gespeichert) | In der Referenz als „Checkbox“ bezeichnet |
| `ezcountry` | `data_text`: kommagetrennte Ländercodes | string | — | `ezcountry` | `ibexa_country` | Beide speichern Alpha-2-Codes |
| `ezdate` | `data_int`: Unix-Zeitstempel | int | — | `ezdate` | `ibexa_date` | |
| `ezdatetime` | `data_int`: Unix-Zeitstempel | int | — | `ezdatetime` | `ibexa_datetime` (gespeichert) | |
| `ezemail` | `data_text` | string | — | `ezemail` | `ibexa_email` (gespeichert) | |
| `ezenum` | keine Attributspalte; Werte in seinen Tabellen | — | `ezenumvalue`, `ezenumobjectvalue` | Null | keiner | Zuerst mit `bin/php/convertezenumtoezselection.php` in `ezselection` konvertieren |
| `ezfloat` | `data_float` | — | — | `ezfloat` | `ibexa_float` | |
| `ezidentifier` | `data_text` (formatierter Bezeichner), `data_int` (laufende Nummer) | string | — | Null | keiner | |
| `ezimage` | `data_text`: XML `<ezimage>` mit einem `<original>`-Element und einem `<alias>` pro Variante | — | `ezimagefile` | `ezimage` | `ibexa_image` (gespeichert) | Die Referenzdatenbank speichert dasselbe XML `<ezimage serial_number=... alias_key=... ><original attribute_id=... /></ezimage>`; die Pfade unterscheiden sich, siehe [17.7](#177-passwörter-url-aliase-und-bildspeicherung) |
| `ezinisetting` | `data_text` (Wert), `data_int` (Kennzeichen für leeres Array) | — | schreibt INI-Dateien | Null | keiner | Hat außerhalb des Legacy-Kernels keine Bedeutung |
| `ezinteger` | `data_int` | int | — | `ezinteger` | `ibexa_integer` (gespeichert) | |
| `ezisbn` | `data_text` | — | `ezisbn_*` (ISBN-13-Bereiche) | `ezisbn` | `ibexa_isbn` | ISBN-10-Werte zuerst mit `bin/php/ezconvert2isbn13.php` konvertieren, wenn sie ISBN-13 sein sollen |
| `ezkeyword` | keine Attributspalte; Schlüsselwörter in seinen Tabellen | — | `ezkeyword`, `ezkeyword_attribute_link` | `ezkeyword` | `ibexa_keyword` | Die Verknüpfungstabelle der Plattform hat eine Spalte `version`, die der von Exponential fehlt |
| `ezmatrix` | `data_text`: XML `<ezmatrix>` mit Spalten und Zellen | — | — | `ezmatrix` (Null im Core, implementiert durch `ibexa/fieldtype-matrix`) | `ibexa_matrix` (`ibexa/fieldtype-matrix`) | |
| `ezmedia` | die `ezmedia`-Zeile | — | `ezmedia` | `ezmedia` | `ibexa_media` (gespeichert) | Gleiche Tabellenspalten, einschließlich der Player-Einstellungen |
| `ezmultioption` | `data_text`: XML `<ezmultioption>` | — | — | Null | keiner | Shop-Datentyp |
| `ezmultioption2` | `data_text`: XML `<ezmultioption2>` | — | — | Null | keiner | Shop-Datentyp |
| `ezmultiprice` | `data_text`: `"<VAT type>,<VAT included>"`; Preise in seiner Tabelle | — | `ezmultipricedata` | Null | keiner | |
| `ezobjectrelation` | `data_int`: ID des verknüpften Objekts | int | `ezcontentobject_link` (Typ 8) | `ezobjectrelation` | `ibexa_object_relation` (gespeichert) | |
| `ezobjectrelationlist` | `data_text`: XML `<related-objects><relation-list><relation-item .../>` | — | `ezcontentobject_link` (Typ 8) | `ezobjectrelationlist` | `ibexa_object_relation_list` (gespeichert) | Die Referenzdatenbank speichert dasselbe XML `<related-objects><relation-list>` |
| `ezoption` | `data_text`: XML `<ezoption>` | — | — | Null | keiner | Shop-Datentyp |
| `ezpackage` | `data_text`: Paketname | string | — | Null | keiner | |
| `ezprice` | `data_float` (Preis), `data_text` (`"<VAT type>,<VAT included>"`) | int (Preis × 100) | — | nicht registriert | keiner | Nur dann mit `bin/php/convertprice2multiprice.php` in `ezmultiprice` konvertieren, wenn ein Shop mit mehreren Währungen auf Exponential bleibt; auf der Plattform gibt es kein Ziel |
| `ezproductcategory` | `data_int`: Kategorie-ID | int | `ezproductcategory` | Null | keiner | |
| `ezrangeoption` | `data_text`: XML `<ezrangeoption>` | — | — | Null | keiner | Shop-Datentyp |
| `ezselection` | `data_text`: IDs der gewählten Optionen, mit `-` verbunden; Optionen im `data_text5` des Klassenattributs | string | — | `ezselection` | `ibexa_selection` | |
| `ezstring` | `data_text` | string | — | `ezstring` | `ibexa_string` (gespeichert) | In der Referenz „TextLine“ |
| `ezsubtreesubscription` | `data_int`-Kennzeichen | — | `ezsubtree_notification_rule` | Null | keiner | |
| `eztext` | `data_text` | — | — | `eztext` | `ibexa_text` (gespeichert) | In der Referenz „TextBlock“ |
| `eztime` | `data_int`: Sekunden seit Mitternacht | int | — | `eztime` | `ibexa_time` | Beide zählen Sekunden ab Tagesbeginn |
| `ezurl` | `data_int`: `ezurl.id`; `data_text`: Linktext | — | `ezurl`, `ezurl_object_link` | `ezurl` | `ibexa_url` (gespeichert) | Auch die Referenzdatenbank hält die URL-ID in `data_int` |
| `ezuser` | `ezuser`-Zeile; `data_text` enthält nur, solange die Version ein Entwurf ist, einen JSON-Entwurf | — | `ezuser`, `ezuser_setting`, `ezuser_accountkey`, `ezuservisit` | `ezuser` | `ibexa_user` (gespeichert) | Siehe die Hash-Typen in [17.7](#177-passwörter-url-aliase-und-bildspeicherung) |
| `ezxmltext` | `data_text`: XML `<section xmlns:image=... xmlns:xhtml=... xmlns:custom=...>` | — | `ezurl`/`ezurl_object_link` für Links, `ezcontentobject_link` Typen 2 und 4 für Einbettungen und Links | nicht registriert | keiner | Die Plattform verwendet RichText (`ezrichtext` in 4.6, `ibexa_richtext` in 5), gespeichert als DocBook `<section xmlns="http://docbook.org/ns/docbook" ...>`; siehe die Hinweise unten |

In dieser Installation gefundene Datentypen aus Extensions:

| Datentyp | Extension | Gespeichert in | Ibexa 4.6 | Ibexa 5 / Plattformpaket |
|---|---|---|---|---|
| `eztags` | eztags | Tabellen `eztags`, `eztags_keyword`, `eztags_attribute_link` | Null im Core; implementiert durch das Netgen Tags Bundle | `eztags` (in der Referenzdatenbank gespeichert, Netgen Tags Bundle) |
| `ezgmaplocation` | ezgmaplocation | `data_int` (hat einen Ort), Tabelle `ezgmaplocation` | `ezgmaplocation` | `ibexa_gmap_location`, Tabelle `ibexa_map_location` |
| `ezsrrating` | ezstarrating | `data_int` (Kennzeichen „deaktiviert“), Tabellen `ezstarrating`, `ezstarrating_data` | nicht registriert | keiner |
| `ezpage` | ezflow | `data_text`: XML `<page>`; Tabellen `ezm_block`, `ezm_pool` | nicht registriert | keiner in der Open-Source-Edition |
| `sckenhancedselection` | enhancedselection2 | Tabelle `sckenhancedselection`; Optionen im `data_text5` des Klassenattributs | nicht registriert | `sckenhancedselection` (in der Referenzdatenbank gespeichert, Community-Bundle) |
| `ngenhancedlink` | exp_enhanced_link | `data_text` (JSON), `data_int` (ID des internen Objekts oder 0) | nicht registriert | `ngenhancedlink` (in der Referenzdatenbank gespeichert, Netgen-Bundle) |
| `ngclasslist` | ngclasslist | (hier nicht geprüft) | nicht registriert | `ngclasslist` (in der Referenzdatenbank gespeichert) |

### Hinweise zu den Konvertierungen, die der Hersteller dokumentiert

- **XmlText zu RichText.** Die Migrationsseite des Herstellers führt XmlText als durch RichText ersetzt und auf der
  Plattform nicht unterstützt; Inhalte werden mit `ezxmltext:convert-to-richtext` aus `ezsystems/ezplatform-xmltext-fieldtype`
  konvertiert (Optionen `--dry-run`, `--export-dir`, `--export-dir-filter`, `--image-content-types`, `--concurrency`).
  RichText validiert strenger als XmlText, führen Sie daher zuerst den Probelauf aus. In diesem Repository gibt es keinen
  solchen Konverter, in keiner Richtung; Exponential rendert die DocBook-Elemente `ezembed`, `para` und `literallayout`, wenn
  sie in importiertem `ezxmltext` vorkommen (`kernel/classes/datatypes/ezxmltext/handlers/output/ezxhtmlxmloutput.php`),
  wodurch solcher Text lesbar bleibt, aber nicht konvertiert wird. Die Zuordnung der Elemente für eine eigene Konvertierung
  steht in [15.8](15-migration-von-5x-legacy.md#158-feldtypen-und-datentypen) und
  [16.6.3](16-migration-von-ez-platform-und-ibexa.md#1663-feldtypen).
- **Benutzerdefinierte Tags.** Die Attributtypen benutzerdefinierter Tags des Online-Editors (`extension/ezoe/design/standard/templates/ezoe/customattributes/`)
  entsprechen, wie vom Hersteller dokumentiert, folgenden Attributtypen benutzerdefinierter Tags in RichText:

  | ezoe-Attributtyp | RichText-Typ | Hinweis des Herstellers |
  |---|---|---|
  | `link` | `link` | unterstützt |
  | `number`, `int` | `number` | |
  | `checkbox` | `boolean` | |
  | `select` | `choice` | |
  | `text` | `string` | |
  | `textarea`, `email`, `hidden`, `color`, `htmlsize`, `csssize`, `csssize4`, `cssborder` | `string` | „als Behelfslösung verwenden“ |

  Die Tags selbst werden in `settings/content.ini` `[CustomTagSettings] AvailableCustomTags[]` deklariert (standardmäßig
  `factbox`, `quote`, `strike`, `sub`, `sup`) sowie in der `content.ini.append.php` von Extensions.
- **Page (`ezflow`).** Ersetzt durch den Page-Feldtyp der kommerziellen Editionen; die Open-Source-Plattform hat kein
  Ziel. Der Befehl `ezflow:migrate` des Herstellers gehört zu diesen Editionen.
- **Sternebewertung (`ezsrrating`).** Vom Hersteller als nicht unterstützt geführt: Implementieren Sie sie, oder entfernen
  Sie sie aus jeder Klasse.
- **Eigene Datentypen.** Schreiben Sie sie als Feldtypen neu, oder registrieren Sie den Null-Feldtyp für ihren
  Bezeichner, damit die gespeicherten Werte ohne Fehler geladen werden. Ibexa 4.6 tut dies bereits für die oben mit Null
  gekennzeichneten Bezeichner.

## 17.4 Einstellungen: INI und YAML

Aufgeführt sind nur Paare, für die sich zeigen lässt, dass sie dasselbe beschreiben. Die YAML-Schlüssel wurden an der
generierten Konfigurationsreferenz und an den `ibexa.yaml`-Dateien der Ibexa-5-Referenzinstallation überprüft; die Paare
für Datenbank und Dateien sind außerdem diejenigen, die die Legacy-Bridge automatisch zuordnet
(`doc/eng-us/specifications/6.0/legacy-bridge-bundle.md`). `<scope>` ist ein Siteaccess, eine Siteaccess-Gruppe oder
`default`, unterhalb von `ibexa.system`.

### Siteaccesses, Sprachen und Design

| INI (Datei, Abschnitt, Schlüssel) | YAML | Hinweise |
|---|---|---|
| `site.ini [SiteAccessSettings] AvailableSiteAccessList[]` | `ibexa.siteaccess.list` | |
| `site.ini [SiteSettings] DefaultAccess` | `ibexa.siteaccess.default_siteaccess` | |
| (keine Entsprechung; Siteaccesses teilen Einstellungen über `settings/override`) | `ibexa.siteaccess.groups` | Gemeinsame Konfiguration gehört an die Gruppe |
| `site.ini [SiteAccessSettings] MatchOrder`, `URIMatchType=element` | `ibexa.siteaccess.match: { URIElement: 1 }` | |
| `site.ini [SiteAccessSettings] HostMatchType=map`, `HostMatchMapItems[]=host;siteaccess` | `ibexa.siteaccess.match: { Map\Host: { host: siteaccess } }` | |
| `site.ini [RegionalSettings] SiteLanguageList[]` | `ibexa.system.<scope>.languages` | Geordnet, die erste ist die Hauptsprache |
| `site.ini [RegionalSettings] TranslationSA[]` | `ibexa.system.<scope>.translation_siteaccesses` | |
| `site.ini [SiteAccessSettings] PathPrefix` | `ibexa.system.<scope>.content.tree_root.location_id` | Die Legacy-Bridge leitet das eine aus dem anderen ab |
| `site.ini [SiteSettings] IndexPage`, `DefaultPage` | `ibexa.system.<scope>.index_page`, `default_page` | |
| `site.ini [DesignSettings] SiteDesign` | `ibexa.system.<scope>.design` | |
| `site.ini [DesignSettings] AdditionalSiteDesignList[]`, `StandardDesign` | `ibexa_design_engine.design_list.<design>: [themes...]` | Die Fallback-Reihenfolge der Themes innerhalb eines Designs |
| `pagelayout.tpl` des Designs | `ibexa.system.<scope>.page_layout` | |
| `override.ini`-Blöcke (`Source`, `MatchFile`, `Match[class_identifier]` ...) | `ibexa.system.<scope>.content_view.<view type>.<name>: { template, controller, match }` | Siehe [17.5](#175-templates-tpl-und-twig) |

### Datenbank, Dateien und Clustering

| INI | YAML oder Umgebung | Hinweise |
|---|---|---|
| `site.ini [DatabaseSettings] Server`, `Port`, `User`, `Password`, `Database`, `Socket` | Doctrine-Verbindung: `DATABASE_URL` oder `doctrine.dbal` `host`, `port`, `user`, `password`, `dbname`, `unix_socket` | |
| `site.ini [DatabaseSettings] DatabaseImplementation` | Doctrine-Treiber: `ezmysqli` = `pdo_mysql`, `ezpostgresql` = `pdo_pgsql`, `ezoracle` = `oci8`, `sqlite3` = `pdo_sqlite` | Die Treiberzuordnung der Legacy-Bridge |
| `site.ini [FileSettings] VarDir` | `ibexa.system.<scope>.var_dir` | Siteaccesses von Exponential verwenden `var/site`; die Referenzinstallation ebenso |
| `site.ini [FileSettings] StorageDir` | `ibexa.system.<scope>.storage_dir` | Standardwert `storage` auf beiden Seiten |
| (festes `original` in den Datentypen für Binärdateien und Medien) | `ibexa.system.<scope>.binary_dir` | Standardwert `original` |
| `file.ini [ClusteringSettings] FileHandler=eZDFSFileHandler` mit `[eZDFSClusteringSettings] MountPointPath`, `DBBackend`, `DBHost`, `DBName` ... | `ibexa_io.metadata_handlers.dfs.legacy_dfs_cluster` plus eine Verbindung `doctrine.dbal.connections.dfs` sowie `ibexa_io.binarydata_handlers.nfs` (Flysystem) mit dem NFS-Pfad | |
| `image.ini [ImageMagick] ExecutablePath`, `Executable` | `ibexa.imagemagick.path` | |
| `image.ini [AliasSettings] AliasList[]` und je ein `[<alias>]`-Block mit `Reference` und `Filters[]` | `ibexa.system.<scope>.image_variations.<alias>: { reference, filters: [{ name, params }] }` | Die Filternamen stimmen überein: Aus `geometry/scaledownonly=100;100` wird `{ name: geometry/scaledownonly, params: [100, 100] }` |

### Benutzer, Sitzungen, URLs, Caches und Suche

| INI | YAML oder Umgebung | Hinweise |
|---|---|---|
| `site.ini [UserSettings] AnonymousUserID` | `ibexa.system.<scope>.anonymous_user_id` | |
| `site.ini [Session] SessionNamePrefix`, `SessionNamePerSiteAccess` | `ibexa.system.<scope>.session.name` | Verwenden Sie das Token `{siteaccess_hash}` für einen Namen pro Siteaccess |
| `site.ini [URLTranslator] TransformationGroup` | `ibexa.url_alias.slug_converter.transformation` | Exponential hat `urlalias`, `urlalias_iri` und `urlalias_compat` in `settings/transform.ini`; der Hersteller nennt `urlalias_compat` und `urlalias_iri` für die Kompatibilität mit Legacy-Aliasen |
| `site.ini [URLTranslator] WordSeparator` | `ibexa.url_alias.slug_converter.separator` | |
| `site.ini [SearchSettings] SearchEngine` | `ibexa.repositories.default.search.engine` (`legacy` oder `solr`) | |
| `content.ini [VersionManagement] DefaultVersionHistoryLimit` | `ibexa.repositories.default.options.default_version_archive_limit` | |
| `site.ini [ContentSettings] ViewCaching` | `ibexa.system.<scope>.content.view_cache` | |
| `httpcache.ini [HttpCacheSettings] MaxAge` | `ibexa.system.<scope>.content.default_ttl` | Der rollenbewusste HTTP-Cache von Exponential ist eine Eigenentwicklung; siehe [HTTP-Caching](../bc/6.0/http-caching.md) |
| `httpcache.ini [HttpCacheSettings] TagHeader` (`xkey`, `Surrogate-Key`, `Cache-Tag`) | `ibexa.http_cache.purge_type` und `ibexa.system.<scope>.http_cache.purge_servers` | Unterschiedliche Mechanismen: Exponential versieht Seiten mit Tags im Ibexa-Stil (`c<object>`, `l<node>`, `pl<parent>`, `p<path node>`, `ct<class>`, `s<section>`, `ez-all`) und leert seinen eigenen Speicher; die Plattform sendet Purge-Anfragen an Varnish oder ihren lokalen Proxy |
| `site.ini [MailSettings] Transport`, `TransportServer` | `framework.mailer.dsn` (`MAILER_DSN`) | |
| `site.ini [DebugSettings] DebugOutput` | `APP_DEBUG` / `kernel.debug` | |
| `site.ini [ExtensionSettings] ActiveExtensions[]` | `config/bundles.php` | Eine Extension und ein Bundle spielen dieselbe Rolle; keines lädt das andere |

Zu den Einstellungen ohne Gegenstück auf der Plattform gehören `site.ini [UserSettings] HashType` und `UpdateHash` (die
Plattform speichert den Hash-Typ pro Benutzer, siehe [17.7](#177-passwörter-url-aliase-und-bildspeicherung)),
`[TemplateSettings]` (Kompilierung von Templates), `[SiteAccessSettings] RequireUserLogin` sowie sämtliche Einstellungen für
Shop, Workflows und Benachrichtigungen.

## 17.5 Templates: TPL und Twig

Die folgenden TPL-Konstrukte sind in `lib/eztemplate/classes/eztemplateautoload.php` (Sprachfunktionen) und
`kernel/common/eztemplateautoload.php` (Operatoren und Funktionen des Kernels) definiert. Die Twig-Namen sind die von
Ibexa 4.6 und 5, wie sie in den Templates der Referenzinstallation verwendet werden; eZ Platform verwendete dieselben Namen
mit dem Präfix `ez_` (`ez_render_field`, `ez_path` ...), die Ibexa 4.6 weiterhin akzeptiert.

| TPL | Twig | Hinweise |
|---|---|---|
| `{def $x = 1}`, `{set $x = 2}`, `{undef $x}` | `{% set x = 1 %}` | Twig-Variablen müssen nicht deklariert werden |
| `{if $a}...{elseif $b}...{else}...{/if}` | `{% if a %}...{% elseif b %}...{% else %}...{% endif %}` | |
| `{foreach $items as $item}...{/foreach}` | `{% for item in items %}...{% endfor %}` | |
| `cond( $a, 'x', 'y' )`, `first_set( $a, $b )` | `a ? 'x' : 'y'`, `a ?? b` | |
| `{include uri='design:parts/menu.tpl' node=$node}` | `{% include '@ibexadesign/parts/menu.html.twig' with { node: node } %}` | `design:` wird über den Design-Fallback aufgelöst, `@ibexadesign` über `ibexa_design_engine.design_list` |
| `{$text\|wash}` | `{{ text }}` | Twig maskiert standardmäßig; `\|raw` schaltet das ab |
| `{'Read more'\|i18n( 'design/site' )}` | `{{ 'Read more'\|trans({}, 'design') }}` | Übersetzungsdomänen ersetzen i18n-Kontexte |
| `{$node.url_alias\|ezurl}` | `{{ ibexa_path( location ) }}` | Relative URL eines Knotens oder Orts |
| `{$node.url_alias\|ezurl( 'no', 'full' )}` | `{{ ibexa_url( location ) }}` | Absolute URL |
| `{'stylesheets/site.css'\|ezdesign}` | `{{ asset( 'css/site.css' ) }}` | Asset-Verwaltung von Symfony; `ezscript`/`ezcss` (ezjscore) haben kein direktes Gegenstück |
| `{$node.name}` | `{{ ibexa_content_name( content ) }}` | |
| `{attribute_view_gui attribute=$node.data_map.title}` | `{{ ibexa_render_field( content, 'title' ) }}` | Feld-Templates kommen aus `field_templates` |
| `{$node.data_map.title.content}` | `{{ ibexa_field_value( content, 'title' ) }}` | |
| `{$node.data_map.title.has_content}` | `{{ not ibexa_field_is_empty( content, 'title' ) }}` | |
| `{$node.data_map.title.contentclass_attribute_name}` | `{{ ibexa_field_name( content, 'title' ) }}` | |
| `{$node.data_map.image.content['medium'].url}` | `{{ ibexa_image_alias( field, versionInfo, 'medium' ).uri }}` | Bild-Alias = Bildvariante |
| `{node_view_gui content_node=$child view='line'}` | `{{ render( controller( 'ibexa_content::viewAction', { contentId: child.contentInfo.id, location: child, viewType: 'line' } ) ) }}` | Das Template wird durch `content_view`-Regeln ausgewählt |
| `fetch( 'content', 'node', hash( 'node_id', 2 ) )` | Keine Twig-Funktion: in einem Controller mit `LocationService::loadLocation()` laden | |
| `fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id, 'class_filter_type', 'include', 'class_filter_array', array( 'article' ), 'sort_by', $node.sort_array, 'limit', 10 ) )` | Keine Twig-Funktion: eine Abfrage in einem Controller (`SearchService::findLocations()`) oder ein Query Type in der View-Konfiguration | Die Referenzinstallation verwendet `queries:` mit einem `query_type` in `content_view` (Netgen Site API) |
| `fetch( 'user', 'current_user' )` | `app.user` | Security-Benutzer von Symfony |
| `{cache-block keys=array( $uri_string ) subtree_expiry=$node.node_id}...{/cache-block}` | `{{ render_esi( controller( '...' ) ) }}` und HTTP-Cache-Tags (`ibexa_http_cache_tag_location( location )`) | Kein Cache auf Template-Ebene auf der Plattform: Fragmente werden vom HTTP-Cache zwischengespeichert und über Tags invalidiert |
| `ezini( 'SiteSettings', 'SiteName' )` | Ein Parameter oder `ibexa.system.<scope>.twig_variables` | |

`override.ini` und `content_view` drücken dieselbe Regel in zwei Formen aus:

```ini
# settings/siteaccess/site/override.ini.append.php
[full_article]
Source=node/view/full.tpl
MatchFile=full/article.tpl
Subdir=templates
Match[class_identifier]=article
```

```yaml
ibexa:
    system:
        site_group:
            content_view:
                full:
                    article:
                        template: '@ibexadesign/full/article.html.twig'
                        match:
                            Identifier\ContentType: article
```

Die Legacy-Match-Schlüssel für Knotenansichten werden in `kernel/classes/eznodeviewfunctions.php` gesetzt
(unter anderem `class_identifier`, `node`, `parent_node`, `section`, `section_identifier`, `depth`, `url_alias`,
`state_identifier`, `parent_class_identifier`, `remote_id`, `node_remote_id`). Die Matcher der Plattform mit derselben
Bedeutung (Klassen in `Ibexa\Core\MVC\Symfony\Matcher\ContentBased`, Ibexa 4.6):

| `override.ini`-Schlüssel | `content_view`-Matcher |
|---|---|
| `Match[class_identifier]` | `Identifier\ContentType` |
| `Match[class]` | `Id\ContentType` |
| `Match[node]` | `Id\Location` |
| `Match[object]` | `Id\Content` |
| `Match[parent_node]` | `Id\ParentLocation` |
| `Match[parent_class_identifier]` | `Identifier\ParentContentType` |
| `Match[section_identifier]`, `Match[section]` | `Identifier\Section`, `Id\Section` |
| `Match[class_group]` | `Id\ContentTypeGroup` |
| `Match[remote_id]`, `Match[node_remote_id]` | `Id\Remote`, `Id\LocationRemote` |
| `Match[depth]` | `Depth` |
| `Match[url_alias]` | `UrlAlias` |

## 17.6 PHP-API: Legacy-Klassen und Dienste der Public API

Die Legacy-Methoden liegen, sofern nicht anders angegeben, in `kernel/classes/`; die Dienste sind die Interfaces in
`Ibexa\Contracts\Core\Repository` (Ibexa 4.6, `ibexa/core` v4.6.28; unter eZ Platform lautete der Namespace
`eZ\Publish\API\Repository`). Aufrufe der Plattform laufen als der aktuelle Benutzer, der am `PermissionResolver` gesetzt
ist, während Legacy-Code meist mit den Rechten des angemeldeten Benutzers lief und in Skripten Prüfungen übersprang.

### eZContentObject und ContentService

| Legacy | Public API |
|---|---|
| `eZContentObject::fetch( $id )` | `ContentService::loadContent( $id )`, `loadContentInfo( $id )` |
| `eZContentObject::fetchByRemoteID( $remoteID )` | `ContentService::loadContentByRemoteId()`, `loadContentInfoByRemoteId()` |
| `eZContentObject::fetchByNodeID( $nodeID )` | `LocationService::loadLocation( $id )->getContentInfo()` |
| `$object->dataMap()` | `$content->getFields()`, `$content->getField( 'identifier' )` |
| `eZContentFunctions::createAndPublishObject( $params )` | `ContentService::newContentCreateStruct()`, `createContent( $struct, [ $locationCreateStruct ] )`, `publishVersion( $versionInfo )` |
| `$object->createNewVersion()` | `ContentService::createContentDraft( $contentInfo )` |
| `eZContentFunctions::updateAndPublishObject( $object, $params )` | `ContentService::newContentUpdateStruct()`, `updateContent()`, `publishVersion()` |
| `eZOperationHandler::execute( 'content', 'publish', ... )` (`lib/ezutils/classes/ezoperationhandler.php`) | `ContentService::publishVersion()` |
| `eZContentObjectVersion::fetchVersion( $version, $objectID )` | `ContentService::loadVersionInfo( $contentInfo, $versionNo )`, `loadVersions()` |
| `$object->removeThis()`, `$object->purge()` | `ContentService::deleteContent( $contentInfo )` |
| `eZContentOperationCollection::deleteObject( $ids, $moveToTrash )` (`kernel/content/`) | `TrashService::trash( $location )` oder `ContentService::deleteContent()` |
| `$object->relatedObjects()` und `ezcontentobject_link` | `ContentService::loadRelationList()`, `loadReverseRelationList()`, `addRelation()`, `deleteRelation()` |

Die Legacy-Klasse hat keine Methode `publish()`; das Veröffentlichen läuft über die Operation `publish` oder über
`eZContentFunctions`.

### eZContentObjectTreeNode und LocationService

| Legacy | Public API |
|---|---|
| `eZContentObjectTreeNode::fetch( $nodeID )` | `LocationService::loadLocation( $id )` |
| `eZContentObjectTreeNode::fetchByRemoteID( $remoteID )` | `LocationService::loadLocationByRemoteId()` |
| `eZContentObjectTreeNode::fetchByContentObjectID( $objectID )` | `LocationService::loadLocations( $contentInfo )` |
| `eZContentObjectTreeNode::subTreeByNodeID( $params, $nodeID )`, `$node->children()` | `LocationService::loadLocationChildren()`; gefilterte Listen mit `SearchService::findLocations()` |
| `eZContentObjectTreeNode::subTreeCountByNodeID( $params, $nodeID )` | `LocationService::getLocationChildCount()`, `getSubtreeSize()` |
| `eZContentObjectTreeNodeOperations::move( $nodeID, $newParentNodeID )` | `LocationService::moveSubtree( $location, $newParent )` |
| `eZContentOperationCollection::swapNode( $nodeID, $selectedNodeID )` | `LocationService::swapLocation()` |
| `eZContentObjectTreeNode::hideSubTree( $node )`, `unhideSubTree( $node )` | `LocationService::hideLocation()`, `unhideLocation()` |
| `eZContentObjectTreeNode::removeSubtrees( $ids, $moveToTrash )` | `TrashService::trash()` oder `LocationService::deleteLocation()` |
| `ezsubtreecopy.php` | `LocationService::copySubtree()` |
| `eZContentObjectTrashNode::trashList()` | `TrashService::findTrashItems()`, `recover()`, `emptyTrash()` |

### eZUser und UserService

| Legacy (`kernel/classes/datatypes/ezuser/ezuser.php`) | Public API |
|---|---|
| `eZUser::fetch( $id )` | `UserService::loadUser( $id )` |
| `eZUser::fetchByName( $login )` | `UserService::loadUserByLogin( $login )` |
| `eZUser::fetchByEmail( $email )` | `UserService::loadUserByEmail()` (ein Benutzer), `loadUsersByEmail()` |
| `eZUser::loginUser( $login, $password )` | `UserService::checkUserCredentials( $user, $password )`; das Anmelden übernimmt Symfony Security |
| `eZUser::currentUser()`, `currentUserID()` | `PermissionResolver::getCurrentUserReference()` |
| `eZUser::setCurrentlyLoggedInUser( $user, $userID )` | `PermissionResolver::setCurrentUserReference( $user )` |
| `eZUser::create( $objectID )` mit `$user->setInformation( ... )` | `UserService::newUserCreateStruct()`, `createUser( $struct, $userGroups )` |
| `$user->hasAccessTo( $module, $function )` | `PermissionResolver::hasAccess( $module, $function )`, `canUser()` |

### eZContentClass und ContentTypeService

| Legacy | Public API |
|---|---|
| `eZContentClass::fetch( $id )` | `ContentTypeService::loadContentType( $id )` |
| `eZContentClass::fetchByIdentifier( $identifier )` | `ContentTypeService::loadContentTypeByIdentifier()` |
| `eZContentClass::fetchByRemoteID()` | `ContentTypeService::loadContentTypeByRemoteId()` |
| `eZContentClass::fetchList()` | `ContentTypeService::loadContentTypes( $group )`, `loadContentTypeList()` |
| `eZContentClass::create()`, `eZContentClassAttribute::create( $classID, $dataTypeString )`, `storeDefined()` | `ContentTypeService::newContentTypeCreateStruct()`, `newFieldDefinitionCreateStruct()`, `createContentType()`, `publishContentTypeDraft()` |
| `$class->instantiate()` | `ContentService::newContentCreateStruct( $contentType, $language )` |

### Suche, URL-Aliase, Rollen und der Rest

| Legacy | Public API |
|---|---|
| `eZSearch::search( $text, $params )` | `SearchService::findContent()`, `findLocations()`, `findSingle()` |
| `eZSearch::addObject( $object )`, `removeObjectById( $id )` | Keine öffentliche Methode: Die Suchmaschine wird vom Repository beim Veröffentlichen und Löschen aktualisiert |
| `eZURLAliasML::fetchByPath( $uri )`, `eZURLAliasML::translate( $uri )` | `URLAliasService::lookup( $url )` |
| `eZURLAliasML::fetchByAction( 'eznode', $nodeID )` | `URLAliasService::reverseLookup( $location )`, `listLocationAliases()` |
| `eZURLAliasML::storePath( $path, $action )` | `URLAliasService::createUrlAlias()`, `createGlobalUrlAlias()` |
| `eZURLWildcard::fetchList()`, `translate()` | `URLWildcardService::loadAll()`, `translate()`, `create()` |
| `eZRole::fetch( $id )`, `fetchByName( $name )` | `RoleService::loadRole()`, `loadRoleByIdentifier()` |
| `eZRole::create( $name )`, `$role->appendPolicy( $module, $function, $limitations )` | `RoleService::createRole()`, `addPolicyByRoleDraft()`, `publishRoleDraft()` |
| `$role->assignToUser( $userID, $limitIdent, $limitValue )` | `RoleService::assignRoleToUser()`, `assignRoleToUserGroup()` |
| `$role->removeUserAssignment( $userID )` | `RoleService::removeRoleAssignment()` |
| `eZSection::fetch( $id )`, `fetchByIdentifier()`, `$section->applyTo( $object )` | `SectionService::loadSection()`, `loadSectionByIdentifier()`, `assignSection()`, `assignSectionToSubtree()` |
| `eZContentObjectStateGroup::newState()`, `eZContentObjectState::fetchByIdentifier()` (`kernel/private/classes/`) | `ObjectStateService::createObjectState()`, `loadObjectStateByIdentifier()`, `setContentState()` |
| `eZContentLanguage::fetchByLocale( $locale )` | `LanguageService::loadLanguage( $languageCode )` |
| `eZPersistentObject::fetchObjectList()` auf Kernel-Tabellen | Keine Entsprechung: Die Plattform liest ihre Tabellen nur über die Dienste; eigene Tabellen verwenden Doctrine |
| `eZWorkflow`, `eZTrigger::runTrigger()` | Keine Entsprechung: Die Plattform löst vor und nach jedem Dienstaufruf Ereignisse aus (Symfony Event Subscriber) **(hier nicht geprüft)** |

## 17.7 Passwörter, URL-Aliase und Bildspeicherung

### Passwort-Hash-Typen

`ezuser.password_hash_type` enthält eine der Konstanten von `eZUser` (`kernel/classes/datatypes/ezuser/ezuser.php`);
`eZUser::createHash()` berechnet jede davon:

| Wert | Konstante | Bezeichner (`HashType`) | Hash | Exponential | Ibexa 4.6 / 5 |
|---|---|---|---|---|---|
| 0 | `PASSWORD_HASH_EMPTY` | `empty` | keiner: Das Konto kann sich nicht anmelden | ja | nein |
| 1 | `PASSWORD_HASH_MD5_PASSWORD` | `md5_password` | `md5( password )` | ja | nein |
| 2 | `PASSWORD_HASH_MD5_USER` | `md5_user` | `md5( "login\npassword" )` | ja | nein |
| 3 | `PASSWORD_HASH_MD5_SITE` | `md5_site` | `md5( "login\npassword\nsite" )`, site = `[UserSettings] SiteName` | ja | nein |
| 4 | `PASSWORD_HASH_MYSQL` | `mysql` | MySQL `PASSWORD()`, in MySQL 8.0 entfernt | ja | nein |
| 5 | `PASSWORD_HASH_PLAINTEXT` | `plaintext` | das Passwort selbst | ja | nein |
| 6 | `PASSWORD_HASH_BCRYPT` | `bcrypt` | `password_hash( PASSWORD_BCRYPT )` | ja | ja |
| 7 | `PASSWORD_HASH_PHP_DEFAULT` | `php_default` | `password_hash( PASSWORD_DEFAULT )` | ja (Standard) | ja (Standard) |
| 256 | — | — | ungültig, absichtlich gesetzt, um eine Anmeldung zu sperren | nein | `PASSWORD_HASH_INVALID` (Ibexa 5) |

Ibexa 4.6 unterstützt nur 6 und 7 (`SUPPORTED_PASSWORD_HASHES`); Ibexa 5 ergänzt 256. `ezsystems/ezpublish-kernel` 7.5
(eZ Platform 2.5) deklarierte noch 1, 2, 3, 5, 6 und 7, wobei die MD5- und Klartext-Typen seit Kernel 6.13 als veraltet
galten. Alle Benutzer in der Ibexa-5-Referenzdatenbank haben Typ 7.

Exponential erzeugt den Hash bei der Anmeldung neu: Mit `site.ini [UserSettings] UpdateHash=true` (Standard) und
`HashType=php_default` (Standard) ersetzt `eZUser::_loginUser()` nach einer erfolgreichen Passwortprüfung jeden anderen
gespeicherten Typ durch einen Hash vom Typ 7, aktualisiert `password_hash` und `password_hash_type` und leert den Cache
des Benutzers. Benutzer, die sich angemeldet haben, seit die Website zu Exponential gewechselt ist, haben daher bereits
einen Hash, den die Plattform akzeptiert. So listen Sie die übrigen vor der Migration auf:

```sql
SELECT password_hash_type, COUNT(*) FROM ezuser GROUP BY password_hash_type;
```

Konten, die noch die Typen 0 bis 5 haben, können sich auf der Plattform nicht anmelden; dort ist für sie ein Zurücksetzen
des Passworts nötig. `ezuser.password_hash` ist im Schema von Exponential (`kernel/sql/mysql/kernel_schema.sql`) wie auf
der Plattform `VARCHAR(255)`, sodass bcrypt- und Argon2-Hashes hineinpassen.

### URL-Alias-Tabellen

| Tabelle | Enthält |
|---|---|
| `ezurlalias_ml` | Eine Zeile pro Pfadelement und Sprache: `parent`, `text`, `text_md5`, `action` (`eznode:<node id>`, `module:<path>` oder `nop:`), `action_type`, `link`, `id`, `is_original`, `is_alias`, `alias_redirects`, `lang_mask` |
| `ezurlalias_ml_incr` | Die ID-Sequenz |
| `ezurlwildcard` | Platzhalter: `source_url`, `destination_url`, `type` |
| `ezurlalias` | Das Format aus der Zeit vor 4.0, nur vom Import in `updateniceurls.php` gelesen |

Die Tabellen der Plattform haben dieselben Spalten unter denselben Namen (Ibexa 4.6) bzw. heißen `ibexa_url_alias_ml`,
`ibexa_url_alias_ml_incr` und `ibexa_url_wildcard` (Ibexa 5), sodass die Aliase mit den Daten umziehen. Anschließend
werden sie mit `ibexa:urls:regenerate-aliases` neu erzeugt (`exponential:urls:regenerate-aliases` auf Exponential
Platform v5, das keinen `ibexa:`-Alias registriert). Die Slug-Transformation muss übereinstimmen: Der Standard von
Exponential ist `urlalias` (`[URLTranslator] TransformationGroup`), die Ibexa-5-Referenzinstallation verwendet
`urlalias_lowercase`.

### Speicherpfade für Bilder und Dateien

| Element | Exponential | Plattform |
|---|---|---|
| Speicherwurzel | `<VarDir>/<StorageDir>`: `var/site/storage` mit `VarDir=var/site` der Siteaccesses | `<var_dir>/<storage_dir>`: `var/site/storage` in der Referenzinstallation |
| Veröffentlichte Bilder | `<storage>/images/<node path with names>/<attribute id>-<version>-<language>/<name>.<ext>` (`image.ini [FileSettings] PublishedImages=images`) | `<storage>/images/<id digits reversed, one per directory>/<field id>-<version>-<language>/<name>.<ext>`, zum Beispiel `var/site/storage/images/0/0/3/2/2300-11-eng-GB/...` |
| Bilder von Entwürfen | `<storage>/images-versioned/<attribute id>/<version>-<language>/` (`VersionedImages=images-versioned`) | dasselbe Verzeichnisschema wie bei veröffentlichten Bildern |
| Bild-Aliase | `<basename>_<alias>.<ext>` neben dem Original | Varianten, die bei Bedarf erzeugt und benannt werden, indem `_<variation>` an den Dateinamen angehängt wird |
| Pfade eines Bildes | `data_text`-XML (`dirpath`, `url`, `<alias>`) plus jeder Pfad in `ezimagefile.filepath` | `data_text`-XML plus `ibexa_image_file.filepath` |
| Binärdateien und Medien | `<storage>/original/<mime group>/<md5 name>.<ext>` | `<storage>/original/...` (`binary_dir`) |

Die Anleitung des Herstellers kopiert `<old root>/web/var[/<site>]/storage` an dieselbe Stelle in der neuen Installation;
die im XML und in `ezimagefile` gespeicherten Bildpfade müssen danach auf vorhandene Dateien zeigen, was die Prüfungen in
[17.8](#178-datenprüfungen-vor-der-migration) feststellen.

## 17.8 Datenprüfungen vor der Migration

Der Hersteller führt Probleme auf, die nach einer Migration auftreten. Nach jedem davon lässt sich zuerst in der
Datenbank von Exponential suchen. Das SQL läuft auf MySQL, PostgreSQL und SQLite; führen Sie es auf einer Kopie aus.

| Problem | So erkennen Sie es in Exponential | Behebung |
|---|---|---|
| Knoten, die nach Klassenbezeichner oder Klassenname sortiert sind, was die Plattform nicht unterstützt | `SELECT node_id, parent_node_id, sort_field FROM ezcontentobject_tree WHERE sort_field IN (6, 7);` (6 = `SORT_FIELD_CLASS_IDENTIFIER`, 7 = `SORT_FIELD_CLASS_NAME` in `eZContentObjectTreeNode`) | Ändern Sie die Sortierung dieser Knoten in der Administration auf Name (9), Veröffentlichung (2) oder Priorität (8) |
| Veraltete URL-Aliase | `php bin/php/verify_aliases.php` | `php bin/php/verify_aliases.php --fix` und `php bin/php/updateniceurls.php --update-nodes`; auf der Plattform `ibexa:urls:regenerate-aliases` |
| Bildpfade außerhalb des Speicherverzeichnisses oder mit ungewöhnlichen Zeichen | `php update/common/scripts/5.1/fiximagesoutsidevardir.php --dry-run`; `SELECT filepath FROM ezimagefile WHERE filepath NOT LIKE 'var/site/storage/%';` (verwenden Sie Ihr `VarDir`) | `fiximagesoutsidevardir.php` ohne `--dry-run`, `update/common/scripts/5.3/recreateimagesreferences.php`; auf der Plattform `ezplatform:images:normalize-path` |
| Beziehungen mit Typ 0 („Unknown relation type 0“) | `SELECT COUNT(*) FROM ezcontentobject_link WHERE relation_type = 0;` (gültige Werte sind 1, 2, 4, 8 und ihre Summen) | Auf der Plattform `ezpublish:update:legacy_storage_clean_up_relation_type_eq_zero`. In Exponential hat bereits die Update-Datei für 5.1 solche Zeilen gelöscht; später entstandene Zeilen werden von jeder Beziehungsabfrage ignoriert und können auf einer gesicherten Datenbank mit `DELETE FROM ezcontentobject_link WHERE relation_type = 0;` entfernt werden ([15.20](15-migration-von-5x-legacy.md#1520-häufige-probleme)) |
| „Immer verfügbar“ ist bei Feldern jeder Sprache gesetzt, nicht nur bei der Hauptsprache | `SELECT COUNT(*) FROM ezcontentobject_attribute a JOIN ezcontentobject o ON o.id = a.contentobject_id WHERE (a.language_id & 1) = 1 AND (a.language_id & ~1) <> (o.initial_language_id & ~1);` (Bit 1 einer Sprach-ID ist das Kennzeichen „immer verfügbar“; Oracle: `bitand()`) | Auf der Plattform `ezpublish:update:legacy_storage_fix_fields_always_available_flag` |
| Leerer `sort_key_string` | `SELECT data_type_string, COUNT(*) FROM ezcontentobject_attribute WHERE sort_key_string = '' AND data_text <> '' AND data_type_string IN ('ezstring', 'ezemail', 'ezcountry', 'ezidentifier', 'ezselection', 'ezpackage') GROUP BY data_type_string;` | Auf der Plattform `ezpublish:update:legacy_storage_update_sort_keys` |
| Datentypen, die die Plattform nicht speichern kann | `SELECT data_type_string, COUNT(*) FROM ezcontentclass_attribute WHERE version = 0 GROUP BY data_type_string;` und mit [17.3](#173-datentypen-und-feldtypen) vergleichen | Konvertieren (`ezenum`, `ezxmltext`), entfernen oder einen Null-Feldtyp registrieren |
| Verwaiste URL-Verknüpfungen | `php update/common/scripts/5.4/fixremovedezurlobjectlinks.php` (ohne `--fix` meldet es nur) | dasselbe Skript mit `--fix` |
| Doppelte Remote-IDs (die Plattform lädt über die Remote-ID und erwartet, dass sie eindeutig ist) | `SELECT remote_id, COUNT(*) FROM ezcontentobject GROUP BY remote_id HAVING COUNT(*) > 1;` (dasselbe für `ezcontentobject_tree` und `ezcontentclass`) | `update/common/scripts/4.1/fixobjectremoteid.php`, `fixnoderemoteid.php`, `fixclassremoteid.php`: `--mode=a` behebt automatisch, `--mode=d` zeigt die Details, ohne `--mode` wird pro Remote-ID nachgefragt |
| Zeichensatz nicht UTF-8 | `site.ini [DatabaseSettings] Charset` und die Kollationen der Tabellen | `php bin/php/ezconvertdbcharset.php` |

Die Befehlsnamen der Plattform sind diejenigen, die der Hersteller dokumentiert; ihr Präfix ändert sich zwischen den
Plattformversionen.

## 17.9 Werkzeuge für Migration und Upgrade

Jedes `bin/php/<name>.php` steht auch als `./console exp:<name>` zur Verfügung; der Code der meisten davon liegt in
`kernel/private/classes/commands/`. Die Standardoptionen (`-s/--siteaccess`, `-q`, `-v`, `-d`, `--allow-root-user`)
gelten für alle Skripte, die auf eZScript beruhen. Kapitel [11](11-aktualisieren.md) erklärt, in welcher Reihenfolge sie
auszuführen sind.

### Datenbank-Update-Dateien

`update/database/<engine>/<version>/dbupdate-<from>-to-<to>.sql`, für `mysql`, `postgresql` und `sqlite`. MySQL und
PostgreSQL enthalten die Kette von `4.0/dbupdate-3.10.0-to-4.0.0.sql` über die 5.x-Schritte (mit Alpha-, Beta- und
RC-Schritten in `unstable/` für 4.0 bis 5.0 und `dbupdate-cluster-*`-Dateien für den MySQL-Cluster bei 4.3, 4.7, 5.2 und
5.4), danach das Verzeichnis 6.0 mit dem Schritt von 5.4 (`dbupdate-5.4.0-6.0.0.sql` auf MySQL,
`dbupdate-5.4-to-6.0.sql` auf PostgreSQL) und `dbupdate-6.0.0-6.0.15.sql`. SQLite hat nur
`6.0/dbupdate-6.0.0-6.0.15.sql`. Jede MySQL-Datei beginnt mit `SET default_storage_engine=InnoDB;` (von MySQL ab 5.5.3 und
von MariaDB akzeptiert); Kopien aus der Zeit vor Oktober 2026 enthielten `SET storage_engine`, was aktuelle Server
ablehnen.

Die Verzeichnisse `6.12/`, `7.2/` (nur PostgreSQL) und `7.3/` stammen aus der Upstream-Legacy-Linie der Jahre 2016 bis 2018
und sind nach der Version des Symfony-Kernels nummeriert, neben dem sie liefen; wenden Sie sie niemals als Dateien an. Ihre
drei Schemaänderungen (`ezuser.password_hash` auf 255 erweitert, die PostgreSQL-Sequenzen in `<table>_<column>_seq`
umbenannt, `ezcontentobject_trash.trashed`) nimmt die Datei von 5.4 auf 6.0.0 vor und erneut die Datei von 6.0.0 auf
6.0.15, jeweils nur dort, wo sie in der Datenbank fehlen ([11.3](11-aktualisieren.md#die-verzeichnisse-612-72-und-73)). Oracle
hat keine 6.0-Datei: Führen Sie die Anweisungen für `password_hash` und `trashed` aus
[11.5](11-aktualisieren.md#115-von-54-oder-590-auf-600) von Hand aus.
`php bin/php/console exp:checkdbfiles --no-verify-branches` prüft, ob der Baum vollständig ist (Tabelle unten).

### update/common/scripts

| Skript | Zweck | Optionen |
|---|---|---|
| `cleanup.php` | Löscht Daten nach Namen: `session`, `expired_session`, `preferences`, `browse`, `tipafriend`, `shop`, `forgotpassword`, `workflow`, `collaboration`, `collectedinformation`, `notification`, `searchstats`, `all` | `--db-host`, `--db-user`, `--db-password`, `--db-database`, `--db-type`, `--sql`, Name |
| `updatecontentobjectname.php` | Erzeugt jeden Objektnamen neu | `-h`, `-q` |
| `updatenbxmlcontents.php` | Korrigiert die Kodierung geschützter Leerzeichen in XML-Text | `--dry-run`, `-n`, `-v`, `--iteration-sleep`, `--iteration-limit` |
| `4.0/updatebinaryfile.php` | Ergänzt gespeicherte Binärdateien um die Dateiendung | keine |
| `4.0/updatetipafriendpolicy.php` | Aktualisiert die Richtlinie für „Tip a friend“ | Siteaccess, Benutzer |
| `4.0/updatevatcountries.php` | Aktualisiert die Mehrwertsteuerländer | keine |
| `4.1/addlockstategroup.php` | Fügt die Zustandsgruppe `ez_lock` hinzu | keine |
| `4.1/correctxmlalign.php` | Verschiebt Ausrichtungsattribute von Einbettungen und benutzerdefinierten Tags nach `align` | `--skip-embed-align`, `--skip-custom-align`, `--custom-align-attribute`, `--db-*` |
| `4.1/fixclassremoteid.php`, `fixnoderemoteid.php`, `fixobjectremoteid.php` | Machen Remote-IDs eindeutig | `--mode` (`d` detailliert, `a` automatisch) |
| `4.1/fixezurlobjectlinks.php` | Verknüpft URLs für jede Version und Übersetzung neu | `--fix`, `--fetch-limit` |
| `4.1/initurlaliasmlid.php` | Initialisiert `ezurlalias_ml_incr` | keine |
| `4.1/updateimagesystem.php` | Überführt Bildattribute in das Bildsystem von 3.3 | keine |
| `4.2/fixorphanimages.php` | Repariert Bilder, die ein Fehler im Alias-Handler hinterlassen hat | `-n` |
| `4.3/updatenodeassignment.php` | Aktualisiert Knotenzuweisungen | `-q` |
| `4.4/updatesectionidentifier.php` (dieselbe Datei in `4.5/`) | Füllt die Bezeichner der Sektionen | `-q` |
| `4.6/removetrashedimages.php` | Entfernt Bilder von Inhalten im Papierkorb | `-n`, `-q` |
| `4.6/updateordernumber.php` | Aktualisiert die Bestellnummern des Shops | `-q` |
| `5.0/deduplicatecontentstategrouplanguage.php` | Entfernt doppelte Übersetzungen von Zustandsgruppen | `-q` |
| `5.0/disablesuspicioususers.php` | Listet Logins auf, die `<` oder `>` enthalten, oder deaktiviert sie | `--disable` |
| `5.0/restorexmlrelations.php` | Stellt fehlende Beziehungen aus XML-Text wieder her | keine |
| `5.1/fiximagesoutsidevardir.php` | Korrigiert Bildverweise außerhalb von `VarDir` | `--dry-run` |
| `5.2/cleanupdfscache.php` | Löscht Cache-Zeilen aus der DFS-Tabelle | `--iteration-sleep`, `--iteration-limit` |
| `5.3/recreateimagesreferences.php` | Erzeugt die Zeilen von `ezimagefile` neu | `--dry-run` |
| `5.3/updatenodeassignmentparentremoteids.php` | Füllt `eznode_assignment.parent_remote_id` | `-n`, `--iteration-sleep`, `--iteration-limit` |
| `5.4/cleanuntranslatablerelations.php` | Entfernt veraltete Beziehungen nicht übersetzbarer Beziehungsattribute | `--dry-run`, `--iteration-sleep`, `--iteration-limit` |
| `5.4/cleanupfieldvaluerelations.php` | Entfernt Beziehungen zu fehlenden Inhalten aus Feldwerten | `-n`, `-v`, `--iteration-sleep`, `--iteration-limit` |
| `5.4/fixremovedezurlobjectlinks.php` | Entfernt verwaiste Zeilen aus `ezurl_object_link` | `--fix`, `--fetch-limit` |
| `5.4/fixtrashedimagereferences.php` | Korrigiert Bildverweise von Inhalten im Papierkorb | `--dry-run`, `--iteration-sleep`, `--iteration-limit` |
| `6.0/createaudittables.php` | Legt die Tabellen `expaudit_*` auf jeder Datenbank-Engine an und indexiert die Audit-Dateien; kann gefahrlos wiederholt werden | `--dry-run`, `--no-index` |
| `6.0/movetrashrecords.php` | Kopiert aus `<VarDir>/trash/trashed.json`, wer was in den Papierkorb gelegt hat, nach `ezcontentobject_trash.trashed_by` und `trashed_via`; kann gefahrlos wiederholt werden; Exit-Code 2, wenn die Datei behalten wird, 1, wenn die Spalten fehlen oder die Datei kein JSON-Objekt enthält | `--dry-run`, `--remove-file`, `-s <siteaccess>` |

### bin/php: Schema, Daten und Konvertierung

| Skript | Zweck | Optionen |
|---|---|---|
| `checkdbfiles.php` | Prüft die Update-Dateien gegen den Upgrade-Pfad: jedes Verzeichnis unter `update/database/` (4.0 bis 7.3 auf MySQL und PostgreSQL, 6.0 auf SQLite), mit den Cluster-Dateien und den anders benannten 6.0-Dateien; `?` kennzeichnet eine Datei oder ein Verzeichnis, das es nicht kennt, `!` eine fehlende Datei. Ohne `--no-verify-branches` vergleicht es außerdem 4.3 bis 5.4 mit den alten SVN-Branches, was `svn` und das stillgelegte Repository svn.ez.no erfordert und daher eine `C`-Zeile meldet. Die Exporte landen in einem neuen Verzeichnis, das es innerhalb von `--export-path` (Standard `var/tmp`) anlegt und wieder entfernt; sonst wird dort nichts angefasst | `--no-verify-branches`, `--export-path` |
| `ezsqldiff.php` | Vergleicht zwei Schemas (Datenbank oder `.dba`-Datei); der Exit-Status gibt an, ob sie sich unterscheiden | `--source-type`, `--source-host`, `--source-user`, `--source-password`, `--source-socket`, `--match-type`, `--match-host`, `--match-user`, `--match-password`, `--match-socket`, `-t/--type`, `--host`, `-u/--user`, `-p/--password`, `--socket`, `--lint-check`, `--reverse`, `--check-only` |
| `ezsqldumpschema.php` | Gibt Schema oder Daten als `.dba`, serialisiert oder als SQL aus | `--type`, `--user`, `--host`, `--password`, `--port`, `--socket`, `--output-array`, `--output-serialized`, `--output-sql`, `--diff-friendly`, `--meta-data`, `--table-type`, `--table-charset`, `--compatible-sql`, `--no-sort`, `--format`, `--output-types`, `--allow-multi-insert`, `--schema-file`, Datenbank, Datei |
| `ezsqlinsertschema.php` | Legt ein Schema und Daten aus einer `.dba`-Datei an | `--type`, `--user`, `--host`, `--password`, `--port`, `--socket`, `--table-type`, `--table-charset`, `--insert-types`, `--allow-multi-insert`, `--schema-file`, `--clean-existing`, Datei, Datenbank |
| `ezimportdbafile.php` | Importiert die `.dba` eines Datentyps | `--datatype` |
| `ezconvertdbcharset.php` | Konvertiert jede Tabelle nach UTF-8 | `--extra-xml-attributes`, `--extra-xml-data`, `--extra-serialized-data`, `--collation`, `--skip-class-translations`, `--iconv-character-set`, `--log-filename` |
| `ezconvertmysqltabletype.php` | Ändert die Storage-Engine von MySQL | `--host`, `--user`, `--password`, `--database`, `--list`, `--newtype`, `--usecopy` |
| `convertezenumtoezselection.php` | Konvertiert ein `ezenum`-Attribut in `ezselection` | `--preview`, Attribut-ID |
| `convertprice2multiprice.php` | Konvertiert `ezprice` in `ezmultiprice` | keine |
| `ezconvert2isbn13.php` | Konvertiert ISBN-10-Werte in ISBN-13 | `--class-id`, `--attribute-id`, `--all-classes`, `-f/--force` |
| `updateisbn13.php` | Aktualisiert die Tabellen der ISBN-Bereiche | `--url`, `--db-*` |
| `ezsqldumpisbndata.php` | Gibt die Tabellen der ISBN-Bereiche aus | `--stdout-sql`, `--stdout-dba`, `--filename-sql`, `--filename-dba`, `--db-*` |
| `updateniceurls.php` | Importiert URL-Aliase aus der Zeit vor 4.0 und erzeugt die Aliase der Knoten neu | `--import`, `--no-import`, `--import-nodes`, `--import-aliases`, `--import-redirections`, `--import-wildcards`, `--update-nodes`, `--no-update-nodes`, `--verify-data`, `--interactive`, `--backup-tables`, `--column-width`, `--fetch-limit`, `--db-*`, `--sql` |
| `verify_aliases.php` | Prüft `ezurlalias_ml` | `--fix`, `--verbose`, `--sql` |
| `updatesearchindex.php` | Baut den Suchindex neu auf | `--clean`, `--db-*`, `--sql` |
| `adddefaultstates.php` | Gibt jedem Objekt die Standard-Objektzustände | keine |
| `cleanuppolicies.php` | Entfernt Richtlinien von Modulen, die es nicht mehr gibt | `--dry-run`, `-n` |
| `cleanupversions.php` | Entfernt archivierte Versionen, die über die Grenzen hinausgehen | `-n` |
| `flatten.php` | Entfernt ungenutzte Daten: `contentobject`, `contentclass`, `workflow`, `role`, `all` | `--db-*`, `--sql`, Name |
| `trashpurge.php` | Leert den Papierkorb | `--iteration-sleep`, `--iteration-limit`, `--memory-monitoring`, `--trashed-days` |
| `ezsubtreecopy.php` | Kopiert einen Teilbaum | `--src-node-id`, `--dst-node-id`, `--all-versions`, `--keep-creator`, `--keep-time` |
| `ezsubtreeremove.php` | Entfernt Teilbäume | `--nodes-id`, `--ignore-trash` |
| `expcontentjob.php` | Große Kopier- und Löschaufträge in Stapeln | `--all`, `--json`, `--background`, `--trash`, `--delete`, `--all-versions`, `--keep-creator`, `--keep-time` |
| `ezcsvimport.php`, `ezcsvexport.php` | Objekte aus CSV importieren; einen Teilbaum nach CSV exportieren | `--class`, `--creator`, `--storage-dir`, Knoten, Datei |
| `clusterize.php` | Verschiebt Dateien in den Cluster oder aus ihm heraus | `-u`, `--skip-binary-files`, `--skip-media-files`, `--skip-images`, `-r`, `-n` |
| `dfscleanup.php` | Vergleicht die DFS-Tabelle mit den Dateien und löscht, was fehlt | `-S`, `-B`, `-D`, `--path`, `--iteration-limit` |
| `ezflowupgrade.php`, `ezwebinupgrade.php` | Aktualisieren die Pakete ezflow und ezwebin | `--to-version`, `--repository`, `--package`, `--package-dir`, `--url`, `--auto-mode` |

Skripte von Extensions: `extension/eztags/bin/php/convertezkeyword.php` überführt `ezkeyword`-Attribute in `eztags`
(`--from-attr-id`, `--to-attr-id`, `--parent-tag-id`); `extension/enhancedselection2/bin/php/migrate_to_database.php`
und `updateenhancedselection.php` aktualisieren den Speicher von Enhanced Selection.

### bin/php: Prüfungen nach einem Upgrade

| Skript | Zweck | Optionen |
|---|---|---|
| `checkmanifest.php` | Vergleicht die Dateien mit `share/filelist.md5`; keine Datenbank nötig | `--all`, `--staged`, `--fix` |
| `checkclasses.php` | Lädt jede deklarierte Klasse und meldet Fehler | `--kernel`, `--tests`, `--quiet-ok` |
| `ezpgenerateautoloads.php` | Erzeugt die Autoload-Arrays neu | `-e`, `-k`, `-o`, `-s`, `-n/--dry-run`, `-t/--target`, `--exclude`, `-p`, `-v`, `-q` |
| `ezcache.php` | Leert Caches | `--clear-all`, `--clear-tag`, `--clear-id`, `--list-tags`, `--list-ids`, `--purge`, `--expiry`, `--iteration-sleep`, `--iteration-max` |
| `eztemplatecheck.php` | Prüft die Syntax von Templates | Dateien |
| `maintenance.php` | Schaltet den Wartungsmodus ein oder aus | `on`, `off`, `status`, `--message`, `--until`, `--allow-ip`, `--allow-admin` |
| `audit.php` | Zeigt und verifiziert das Audit-Protokoll | viele; siehe [das Audit-Protokoll](../funktionen/6.0/pruefprotokoll.md) |

Shell-Hilfsskripte in `bin/shell/`: `checkdbschema.sh`, `checkdbupdate.sh` (`--check-stable`, `--check-previous`),
`verifyfiles.sh` (`--quiet`) und `generatefilelist.sh`. In der Administration führt **Setup > Upgrade-Prüfung** dieselbe
Dateiprüfung gegen `share/filelist.md5` aus und vergleicht das laufende Schema mit `share/db_schema.dba`, zusammengeführt
mit den Schemas der aktiven Extensions (`kernel/private/classes/views/setup/systemupgrade.php`).

### Umzug auf die Plattform oder von ihr weg

Dieses Repository enthält keinen Datenkonverter zu oder von eZ Platform oder Ibexa. Die [Legacy-Bridge](../funktionen/6.0/legacy-bridge.md)
führt den Kernel von Exponential innerhalb einer Plattform-Installation auf einer gemeinsamen Datenbank aus (Branches für
eZ Platform 2.5, Platform 3.3, Ibexa 4.6 und Platform 5), mit den Konsolenbefehlen `exponential:legacy:init`, `configure`,
`install-extensions`, `symlink`, `assets-install` und `script` auf der Linie `3.x` ab `v3.0.0.30`, der Linie `4.x` ab
`v4.0.0.2` und in jedem `5.x`-Tag, wobei die alten Namen `ezpublish:*` als veraltete Aliase erhalten bleiben. Die Linie 2.5
(`master`, `v2.1.10` bis `v2.1.12`) und die früheren 3.x- und 4.x-Tags kennen nur die alten Namen
(`ezpublish:legacy:init` ...). Auf Seiten der Plattform dokumentiert der Hersteller `ezxmltext:convert-to-richtext`,
`ibexa:urls:regenerate-aliases`, `ezplatform:images:normalize-path` und die in
[17.8](#178-datenprüfungen-vor-der-migration) aufgeführten Befehle `ezpublish:update:legacy_storage_*`.

## 17.10 Glossar umbenannter Begriffe

| Exponential (und eZ Publish 4.x und 5.x Legacy) | eZ Platform und Ibexa | Hinweise |
|---|---|---|
| Inhaltsobjekt (content object) | Content Item (`Content`, `ContentInfo`) | |
| Objektversion (object version) | Version (`VersionInfo`) | |
| Objektattribut (object attribute) | Feld (field, `Field`) | |
| Inhaltsklasse (content class) | Inhaltstyp (content type, `ContentType`) | |
| Klassenattribut (class attribute) | Felddefinition (field definition, `FieldDefinition`) | |
| Klassengruppe (class group) | Inhaltstyp-Gruppe (content type group) | |
| Datentyp (datatype) | Feldtyp (field type) | |
| Knoten (node) | Location (`Location`) | |
| Hauptknoten (main node) | Haupt-Location (main location) | |
| Sektion, Objektzustand, Rolle, Richtlinie, Einschränkung (section, object state, role, policy, limitation) | dieselben Namen | |
| Benutzer (ein Objekt einer Benutzerklasse) | Benutzer (`User`), weiterhin ein Content Item | |
| Siteaccess | SiteAccess | Match-Regeln werden zu `siteaccess.match`; Einstellungen wandern aus INI-Dateien nach `ibexa.system.<scope>` |
| Siteaccess-Einstellungen in `settings/siteaccess/<name>/` | Konfiguration eines SiteAccess oder einer SiteAccess-Gruppe | |
| Extension | Bundle | |
| Modul und View (`/content/view/full/2`) | Route und Controller | |
| Template-Override (`override.ini`) | View-Konfiguration (`content_view` mit Matchern) | |
| Ansichtsmodus (view mode: `full`, `line`, `embed`) | View-Typ (view type) | |
| Pagelayout (`pagelayout.tpl`) | Seitenlayout (page layout, `page_layout`) | |
| Design und seine Fallback-Liste | Design und Theme-Liste (`ibexa_design_engine`) | |
| Template-Operator oder -Funktion | Twig-Funktion oder -Filter | |
| `cache-block`, View-Cache | HTTP-Cache mit Tags, ESI-Fragmente | |
| Bild-Alias (image alias) | Bildvariante (image variation) | |
| `ezxmltext` (XML-Text) | RichText (DocBook) | |
| `ezflow`-Seite | Page-Feldtyp (kommerzielle Editionen) | |
| eztags | Netgen Tags | Taxonomie in den kommerziellen Editionen |
| Clustering (`eZDFSFileHandler`) | IO-Handler (Metadaten `legacy_dfs_cluster`, Binärdaten über Flysystem) | |
| Workflow, Trigger, Operation | Ereignisse und Subscriber | (hier nicht geprüft) |
| Informationssammlung (information collection) | Form Builder (kommerziell) oder das Netgen-Bundle für Informationssammlung | Die Referenzinstallation behält `ezinfocollection` mit dem Netgen-Bundle |
| `ezfind` (Solr) | Bundle für die Solr-Suchmaschine | |
| `ezsite_data` `ezpublish-version` | dieselbe Zeile; die Plattform verfolgt Schemaänderungen mit Migrationen | |
| eZ Publish 4.x | Exponential 6.0 setzt diese Linie fort | |
| eZ Publish 5.x (Platform-Stack neben Legacy) | eZ Platform 1.x bis 3.x, danach Ibexa DXP ab 3.3 | Exponential behält die Legacy-Hälfte und kann über die Legacy-Bridge neben der Plattform laufen |

## Quellen

In diesem Repository:

- [11. Upgrade](11-aktualisieren.md): die Reihenfolge, in der die Update-Dateien und Skripte auszuführen sind.
- [Upgrade](../anleitungen/aktualisieren.md), [Inhaltsmodell und Bearbeitung](../anleitungen/inhaltsmodell-und-bearbeitung.md),
  [Templates und Design](../anleitungen/templates-und-design.md), [Betrieb einer Website](../anleitungen/betrieb-einer-website.md).
- [Legacy-Bridge](../funktionen/6.0/legacy-bridge.md), [Spezifikation des Legacy-Bridge-Bundles](../spezifikationen/6.0/legacy-bridge-bundle.md),
  [Übersicht der Plattformpakete](../spezifikationen/6.0/platform-paketzuordnung.md),
  [Site-Bundles der Plattform](../funktionen/6.0/plattform-site-bundles.md),
  [Forks von Plattformpaketen und umbenannte Befehle](../bc/6.0/plattform-paket-forks-und-umbenannte-befehle.md).
- [HTTP-Caching](../bc/6.0/http-caching.md), [Prüfung der Dateikonsistenz](../funktionen/6.0/dateikonsistenzpruefung.md),
  [Audit-Protokoll](../funktionen/6.0/pruefprotokoll.md), [Glossar](../glossar.md).
- Code: `share/db_schema.dba`, `kernel/sql/`, `kernel/classes/datatypes/`, `kernel/classes/datatypes/ezuser/ezuser.php`,
  `kernel/classes/ezcontentobjecttreenode.php`, `kernel/classes/datatypes/ezimage/ezimagealiashandler.php`,
  `lib/eztemplate/classes/eztemplateautoload.php`, `kernel/common/eztemplateautoload.php`, `settings/site.ini`,
  `settings/image.ini`, `settings/file.ini`, `settings/httpcache.ini`, `update/`, `bin/php/`.

Extern:

- Migrationsseiten des Herstellers: [Migrate from eZ Publish](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish/),
  [Migrate from eZ Publish Platform](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/migrating_from_ez_publish_platform/),
  [Common migration issues](https://doc.ibexa.co/en/5.0/update_and_migration/migrate_to_ibexa_dxp/common_issues/),
  [Migrating from eZ Publish (Version für eZ Platform 2.5)](https://doc.ibexa.co/en/2.5/migrating/migrating_from_ez_publish/) (die Version 1.13 der Seite wird nicht mehr veröffentlicht; ihre alte Adresse leitet auf die aktuelle Seite weiter).
- Feldtypen: [Feldtyp-Referenz](https://doc.ibexa.co/en/5.0/content_management/field_types/field_type_reference/field_type_reference/),
  [Image](https://doc.ibexa.co/en/5.0/content_management/field_types/field_type_reference/imagefield/),
  [User](https://doc.ibexa.co/en/5.0/content_management/field_types/field_type_reference/userfield/),
  [Null](https://doc.ibexa.co/en/5.0/content_management/field_types/field_type_reference/nullfield/),
  [MapLocation](https://doc.ibexa.co/en/5.0/content_management/field_types/field_type_reference/maplocationfield/),
  [Matrix](https://doc.ibexa.co/en/5.0/content_management/field_types/field_type_reference/matrixfield/).
- Quellcode: [ezsystems/ezpublish-legacy](https://github.com/ezsystems/ezpublish-legacy),
  [ezsystems/ezpublish-kernel](https://github.com/ezsystems/ezpublish-kernel) (Hash-Konstanten von `User` in 7.5),
  [ezsystems/ezplatform-xmltext-fieldtype](https://github.com/ezsystems/ezplatform-xmltext-fieldtype),
  [ezsystems/LegacyBridge](https://github.com/ezsystems/LegacyBridge), [ibexa/core](https://github.com/ibexa/core).
- PHP: [password_hash](https://www.php.net/manual/en/function.password-hash.php),
  [password_verify](https://www.php.net/manual/en/function.password-verify.php).
- Symfony und Twig: [Konfiguration](https://symfony.com/doc/current/configuration.html),
  [Verbindungs-URLs für Doctrine DBAL](https://symfony.com/doc/current/doctrine.html),
  [Mailer-DSN](https://symfony.com/doc/current/mailer.html), [ESI](https://symfony.com/doc/current/http_cache/esi.html),
  [Twig für Template-Designer](https://twig.symfony.com/doc/3.x/templates.html),
  [Twig `include`](https://twig.symfony.com/doc/3.x/tags/include.html).

[Inhalt](README.md) · Zurück: [16. Migration von eZ Platform und Ibexa](16-migration-von-ez-platform-und-ibexa.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
