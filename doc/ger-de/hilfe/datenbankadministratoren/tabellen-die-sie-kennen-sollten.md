---
title: Die Tabellen, die ein Datenbankadministrator kennen sollte
audience: dba
category: database-administration
order: 34
summary: Eine Übersicht der Kernel-Tabellen von Exponential für Datenbankadministratoren, nach Zweck gruppiert (Inhalte, Klassen, Benutzer und Berechtigungen, URL-Aliase, Suche, Sessions, Warteschlangen, Website-Daten, Dateien, Shop, Audit, E-Mail, API), mit den am schnellsten wachsenden Tabellen, denen, die sich gefahrlos ansehen lassen, und den Werkzeugen, die Sie statt Handbearbeitung verwenden.
keywords: Tabellen, Schemaübersicht, ezcontentobject, ezcontentobject_attribute, ezcontentobject_tree, ezcontentobject_version, ezurlalias_ml, ezsearch_word, ezsearch_object_word_link, ezsession, ezpending_actions, ezsite_data, ezuser, ezuservisit, ezpolicy, expaudit, expmail, expapikey, ezdfsfile, Wachstum, Tabellengröße, tables, schema map, growth, table size
type: help
---
# Die Tabellen, die ein Datenbankadministrator kennen sollte

Diese Seite richtet sich an Datenbankadministratoren, die sich in einer Exponential-Datenbank zurechtfinden müssen: um sie zu
bemessen, für einen Bericht auszulesen oder zu verstehen, in welcher Tabelle ein Problem liegt. Sie gruppiert die Tabellen
des Kernels (aus `kernel/sql/mysql/kernel_schema.sql`, 129 Tabellen) nach Zweck. Erweiterungen fügen eigene hinzu.

> **Warnung**
> Lesen Sie diese Tabellen; bearbeiten Sie sie nicht von Hand. Inhalte, Versionen, der Knotenbaum, URL-Aliase und der
> Suchindex hängen voneinander ab, und die Caches sehen außerhalb von Exponential gemachte Änderungen nicht. Verwenden Sie
> die Administration, die unten genannten Skripte oder die Inhalts-API. Leeren Sie nach jedem Schreibzugriff außerhalb von
> Exponential die Caches.

## Inhalt

1. [Inhalte](#inhalte)
2. [Klassen und Struktur](#klassen-und-struktur)
3. [Benutzer und Berechtigungen](#benutzer-und-berechtigungen)
4. [URL-Aliase](#url-aliase)
5. [Suche](#suche)
6. [Sessions, Warteschlangen und Aufräumen](#sessions-warteschlangen-und-aufräumen)
7. [Dateien, Website-Daten und andere Bereiche](#dateien-website-daten-und-andere-bereiche)
8. [Welche Tabellen wachsen](#welche-tabellen-wachsen)
9. [Werkzeuge statt Handbearbeitung](#werkzeuge-statt-handbearbeitung)

## Inhalte

| Tabelle | Enthält |
|---|---|
| `ezcontentobject` | eine Zeile je Inhaltsobjekt: Klasse, Eigentümer, Sektion, aktuelle Version, `remote_id` |
| `ezcontentobject_version` | jede Version jedes Objekts (Entwürfe, veröffentlicht, archiviert) |
| `ezcontentobject_attribute` | die Feldwerte je Objekt, Version und Sprache: meist die größte Tabelle |
| `ezcontentobject_name` | Objektnamen je Version und Sprache |
| `ezcontentobject_tree` | der Knotenbaum: `node_id`, `parent_node_id`, `path_string`, `depth`, `main_node_id` |
| `eznode_assignment` | wo eine Version beim Veröffentlichen platziert wird |
| `ezcontentobject_link` | Relationen zwischen Objekten |
| `ezcontentobject_trash` | Knoten im Papierkorb (`trashed`, `trashed_by`, `trashed_via` seit 6.0.15) |
| `ezcobj_state*` | Objektzustände und ihre Gruppen |

## Klassen und Struktur

`ezcontentclass`, `ezcontentclass_attribute`, `ezcontentclass_name`, `ezcontentclassgroup`,
`ezcontentclass_classgroup`, `ezsection`, `ezcontent_language`.

## Benutzer und Berechtigungen

| Tabelle | Enthält |
|---|---|
| `ezuser` | Logins, E-Mail-Adressen, Passwort-Hashes (`password_hash` auf 255 erweitert) |
| `ezuser_setting` | ob ein Konto aktiviert ist |
| `ezuservisit` | letzter und aktueller Besuch, Zahl der Anmeldungen, fehlgeschlagene Anmeldeversuche |
| `ezrole`, `ezpolicy`, `ezpolicy_limitation`, `ezpolicy_limitation_value`, `ezuser_role` | Rollen, Richtlinien und ihre Zuweisungen |
| `ezforgot_password`, `ezuser_accountkey` | Schlüssel für Passwortrücksetzung und Aktivierung |
| `ezpreferences` | Einstellungen der Administration je Benutzer |

Diese Tabellen enthalten personenbezogene Daten. Begrenzen Sie, wer sie lesen kann, und halten Sie sie aus Testkopien heraus,
die Sie an andere weitergeben.

## URL-Aliase

`ezurlalias_ml` (die mehrsprachigen URL-Aliase), `ezurlalias_ml_incr`, `ezurlwildcard` und das ältere `ezurlalias`.
`exp:verify_aliases` prüft `ezurlalias_ml` auf Konsistenz (`--fix` repariert sichere Probleme, `--sql` zeigt die Abfragen);
`bin/php/updateniceurls.php` baut die Aliase neu auf.

## Suche

`ezsearch_word` (eine Zeile je Wort, mit `object_count`), `ezsearch_object_word_link` (eine Zeile je Vorkommen eines Wortes in
einem Objekt, mit Nachbarn und `integer_value`), `ezsearch_search_phrase` (Suchstatistik, nur mit `LogSearchStats`). Siehe
[Die Tabellen des eingebauten Suchindex](tabellen-des-suchindex.md).

## Sessions, Warteschlangen und Aufräumen

| Tabelle | Enthält | Siehe |
|---|---|---|
| `ezsession` | Sessions, wenn `ezpSessionHandlerDB` verwendet wird | [Sessions in der Datenbank](sessions-in-der-datenbank.md) |
| `ezpending_actions` | wartende Arbeit: verzögerte Indizierung (`index_object`, `index_moved_node`) und andere Aktionen | [Aufräumen](aufraeumen-cronjobs-und-warteschlangen.md) |
| `ezpublishingqueueprocesses` | die Prozesse des asynchronen Veröffentlichers | |
| `ezworkflow_process`, `ezoperation_memento`, `ezwaituntildatevalue` | laufende und wartende Workflows | |
| `ezscheduled_script`, `ezmodule_run` | geplante Skripte und Modulläufe | |
| `ezbasket`, `ezproductcollection*` | Warenkörbe des Shops, von `basket_cleanup.php` aufgeräumt | |

## Dateien, Website-Daten und andere Bereiche

| Tabelle(n) | Enthält |
|---|---|
| `ezsite_data` | Name-Wert-Paare, darunter `ezpublish-version` und `ezpublish-release` |
| `ezimagefile`, `ezbinaryfile`, `ezmedia` | Metadaten von Dateien, deren Inhalt in `var/storage` liegt |
| `ezurl`, `ezurl_object_link` | externe Links und wo sie verwendet werden (von `linkcheck.php` geprüft) |
| `ezorder*`, `ezvat*`, `ezdiscount*`, `ezcurrencydata`, `ezmultipricedata`, `ezwishlist`, `ezpaymentobject` | der Shop |
| `expaudit_cursor`, `expaudit_event`, `expaudit_file` | der Audit-Index (6.0.15) |
| `expmail_*` | E-Mail-Einstellungen, Einwilligungsprotokoll, Sperrliste (6.0.15) |
| `expbookmark_folder` | Lesezeichenordner (6.0.15) |
| `expbookmark_view` | die Adressen der Lesezeichen von Systemseiten, auch zerlegt in `module_name`, `view_name` und `view_parameters` (6.0.15) |
| `expapikey`, `ezprest_*` | persönliche API-Schlüssel, OAuth-Clients und -Tokens: Zugangsdaten, als Geheimnisse behandeln |
| `ezrss_*`, `ezpdf_export`, `ezpackage`, `ezinfocollection*`, `ezcollab_*`, `eznotification*` | Feeds, Exporte, Pakete, Formulareinsendungen, Zusammenarbeit, Benachrichtigungen |

`ezdfsfile` und `ezdfsfile_cache` des DFS-Clusters liegen in der eigenen Datenbank des Clusters
([Clustering](clustering-dfs-und-die-datenbank.md)).

## Welche Tabellen wachsen

Messen Sie auf Ihrer eigenen Website; **Setup > Systeminformationen > Größen messen** zeigt die Größe der Datenbank, und der
Katalog Ihrer Engine die Größe je Tabelle. Typischerweise am größten und wachsend: `ezcontentobject_attribute` (jede Version
und Sprache), `ezsearch_object_word_link`, `ezcontentobject_version`, `ezurlalias_ml` und `ezsession`, wenn Sessions in der
Datenbank liegen und abgelaufene Zeilen nicht entfernt werden. Alte Versionen werden mit `bin/php/cleanupversions.php`
innerhalb der Grenzen von `VersionManagement` beschnitten.

## Werkzeuge statt Handbearbeitung

| Bedarf | Verwenden Sie |
|---|---|
| Alte Versionen entfernen | `php bin/php/cleanupversions.php --apply` |
| URL-Aliase neu aufbauen | `php bin/php/updateniceurls.php`, `exp:verify_aliases` |
| Den Suchindex neu aufbauen | `php bin/php/updatesearchindex.php --clean` |
| Abgelaufene Sessions entfernen | `php bin/php/ezsessiongc.php` |
| Doppelte Logins | `exp:userlogincheck` (`--fix-index` fügt einen eindeutigen Index auf `ezuser.login` hinzu, sobald es keine mehr gibt) |
| Schemaunterschiede | die Upgrade-Prüfung, `ezsqldiff.php` |

## Verwandte Seiten

- [Das .dba-Schemaformat](das-dba-schemaformat.md)
- [Aufräumen: Cronjobs und Warteschlangentabellen](aufraeumen-cronjobs-und-warteschlangen.md)
- [Inhaltsmodell und Bearbeitung](../../anleitungen/inhaltsmodell-und-bearbeitung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
