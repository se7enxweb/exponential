---
title: Erweiterungen, die sevenx_multisite aktiviert
audience: admin, developer, hosting
category: install-upgrade
order: 66
summary: Die 60 Erweiterungen, die der Installer von sevenx_multisite in ActiveExtensions schreibt, in ihrer Reihenfolge und warum die Reihenfolge zählt, die Zugriffserweiterungen nur für die Administration und wie der Installer die Datenbanktabellen der Erweiterungen anlegt.
keywords: ActiveExtensions, extension_list, ActiveAccessExtensions, expai, exp_adminui, sevenx_themes_media, explayouts, ezurlmultisite, extensionSchemas, db_schema.dba, dropExtensionTables, insertDBFile, Erweiterungen, Reihenfolge, Tabellen der Erweiterungen, extensions, order, extension tables
type: help
---
# Erweiterungen, die sevenx_multisite aktiviert

Diese Seite ist für Administratoren und Hoster, die wissen müssen, welche Erweiterungen eine Installation von `sevenx_multisite` einschaltet,
und für Entwickler, die eine hinzufügen. Sie beantwortet: welche Erweiterungen aufgeführt sind, in welcher Reihenfolge, welche nur in der
Administration aktiv sind und wie ihre Tabellen angelegt werden.

## Inhalt

1. [Wo die Liste steht](#wo-die-liste-steht)
2. [Die Liste, in Reihenfolge](#die-liste-in-reihenfolge)
3. [Warum die Reihenfolge zählt](#warum-die-reihenfolge-zählt)
4. [Erweiterungen nur der Administration](#erweiterungen-nur-der-administration)
5. [Erweiterungen, nach denen der Installer sieht](#erweiterungen-nach-denen-der-installer-sieht)
6. [Tabellen der Erweiterungen](#tabellen-der-erweiterungen)

## Wo die Liste steht

`initSettings()` in `settings/sevenx-multi-site-installer.php` speichert die Liste als Einstellung `extension_list`.
`commonSiteINISettings()` schreibt sie nach `settings/override/site.ini.append.php` als `[ExtensionSettings] ActiveExtensions`. Dieselbe
Liste wird während der Installation selbst genutzt: `preInstall()` fügt den Ordner `settings/` jeder aufgeführten Erweiterung der laufenden
Konfiguration hinzu (`activateSiteExtensionSettings()`), sodass ihre Datentypen und INI-Dateien bekannt sind, während die Pakete importiert
werden.

> **Warnung**
> Die Erweiterungen müssen in `extension/` der Installation vorhanden sein; das Paket bringt sie nicht mit. Eine aufgeführte Erweiterung,
> die fehlt, wird zur Laufzeit einfach nicht gefunden.

## Die Liste, in Reihenfolge

| # | Erweiterungen | Hinweise aus dem Installer |
|---|---|---|
| 1 | `xrowmetadata` | der Metadaten-Datentyp, den viele Klassen nutzen |
| 2-3 | `expui`, `ezjscore` | Exponential UI vor ezjscore, damit seine Einstellungen (jQuery 4, die Skript- und Stil-Listen der Administration) gewinnen |
| 4 | `expservices` | entfernte Dienste für Apps der Administration und JavaScript-Frontends; seine Portal-Designs bekommen keinen Siteaccess |
| 5 | `expqueue` | die Job-Warteschlange (**Setup > Job queue**); ihr Worker läuft mit dem Siteaccess der Administration aus Cron |
| 6 | `ezoracle` | der Oracle-Treiber, auf jeder anderen Datenbank ohne Wirkung |
| 7-9 | `ezoe`, `ezformtoken`, `ezwt` | der Online-Editor, Schutz vor Cross-Site Request Forgery für gesendete Formulare, die Website-Toolbar |
| 10-16 | `ezstarrating`, `ezgmaplocation`, `ezautosave`, `ezodf`, `ezie`, `ezprestapi`, `ezpaypal` | |
| 17-18 | `owsimpleoperator`, `swark` | Template-Operatoren |
| 19-21 | `bcgooglesitemaps`, `bcwebsitestatistics`, `bccie` | |
| 22-24 | `xrowextract`, `enhancedezbinaryfile`, `enhancedselection2` | `enhancedselection2` besitzt den Datentyp `sckenhancedselection` mehrerer Klassen `ng_*` |
| 25 | `birthday` | der Datentyp ezbirthday |
| 26-27 | `ezwebin`, `ezmultiupload` | |
| 28 | `syndication` | Syndikation von Inhalten zwischen Installationen |
| 29-30 | `ezupdate`, `git_manager` | |
| 31-32 | `eztags`, `ngclasslist` | |
| 33-44 | `explayouts`, `explayouts_core`, `explayouts_api`, `explayouts_standard`, `explayouts_site_api`, `explayouts_relation_list_query`, `explayouts_tags_query`, `explayouts_content_browser_core`, `explayouts_content_browser`, `explayouts_content_browser_ui`, `explayouts_ui`, `explayouts_ui_api` | die Familie der Layouts, die Anbieter vor der Oberfläche aufgeführt, die sie nutzt |
| 45-48 | `expquery_translator`, `expsite_core`, `expsite_api`, `expsite_app` | |
| 49-51 | `exp_enhanced_link`, `expchangeclass`, `ezownerchange` | `ezownerchange` ändert den Eigentümer eines Objekts oder eines Teilbaums |
| 52 | `cjw_newsletter` | seine Klassen und sein Baum werden von einem Schritt der Nachinstallation importiert |
| 53-54 | `recaptcha`, `hcaptcha` | `hcaptcha` braucht gesetzte Schlüssel |
| 55 | `sevenx_authentication_2fa` | Zwei-Faktor- und Social Login |
| 56-57 | `powercontent`, `sevenx_dse` | |
| 58-59 | `sevenx_themes_media`, `sevenx_themes_simple` | die zwei Themes, am Ende gehalten |
| 60 | `ezurlmultisite` | Links auf Inhalte der anderen Website zeigen auf den Siteaccess, der sie ausliefert |

## Warum die Reihenfolge zählt

Der Kommentar über der Liste erklärt es: Die Reihenfolge zählt, weil spätere Erweiterungen bei Design- und Template-Overrides gewinnen;
deshalb bleiben die zwei Themes am Ende. Wenn ein Datentyp nachgeschlagen wird, gewinnt der erste Treffer, weshalb
`activateSiteExtensionSettings()` die Ordner der Einstellungen in der Reihenfolge hinzufügt, in der die Website sie laden wird
(`eZExtension::extensionOrdering()` wird angewendet, wenn `[ExtensionSettings] ExtensionOrdering=enabled` gilt).

> **Tipp**
> Um der Standardinstallation eine Erweiterung hinzuzufügen, setzen Sie sie an die Stelle in `extension_list`, die ihre Einstellungen
> brauchen, und prüfen Sie, ob sie Tabellen hat (siehe unten). Die Geschichte der Erweiterungen im Installer zeigt das Muster: ezownerchange
> aktiviert (15.10.2026), ezurlmultisite mit den Knoten-IDs der neuen Installation aktiviert (14.10.2026). Siehe
> [Das Installerskript ändern](../eigene/das-installerskript-aendern.md).

## Erweiterungen nur der Administration

Zwei Erweiterungen stehen bewusst nicht in `extension_list`. `adminAccessExtensionList()` gibt `expai` und `exp_adminui` zurück, jeweils nur,
wenn `extension/<name>` existiert, und `adminSiteINISettings()` schreibt sie als `[ExtensionSettings] ActiveAccessExtensions` des
Siteaccess der Administration. Die Siteaccesses editor und adminui sind Kopien der Dateien der Administration und erben sie.

- `expai` (Exponential Intelligence) wird ausgeschaltet installiert: sein eigenes `[AISettings] Enabled=false`, kein Anbieterschlüssel wird
  geschrieben. Sein Cronjob-Teil braucht den Siteaccess der Administration.
- `exp_adminui` macht Admin UI zu einer Wahl im Designmenü der Administration und der Redaktion; jeder andere Siteaccess behält sein eigenes
  Design.

## Erweiterungen, nach denen der Installer sieht

| Erweiterung | Was der Installer tut, wenn sie vorhanden ist |
|---|---|
| `sevenx_authentication_2fa` | fügt der Benutzerklasse das Feld **Zweistufige Anmeldung** hinzu, schreibt `sevenxauthentication2fa.ini` (`Enforce2FA=disabled`, `DefaultMethod=totp`, ein für diese Installation erstellter `SecretKey`), setzt `LoginHandler` `sevenxUser2fa`, dann `standard`, und gibt `user2fa/setup` und `user2fa/verify` an Member, Editor und Partner |
| `cjw_newsletter` | importiert seine Inhaltsklassen in die Klassengruppe „Newsletter“, legt den Newsletter-Baum an und setzt die Einstellung seines Wurzelordners |
| `exp_adminui` | legt den Siteaccess `adminui` an |
| `ezurlmultisite` | schreibt `settings/override/ezurlmultisite.ini.append.php` (siehe [Was Sie nach der Installation haben](was-sie-nach-der-installation-von-sevenx-multisite-haben.md)) |
| `explayouts` | importiert die Standardlayouts der Administration aus `share/fragments/admin_layouts_db_data.dba` |

## Tabellen der Erweiterungen

`extensionSchemas()` entscheidet, welche Erweiterungen während `preInstall()` ihre Tabellen bekommen:

1. Die in `declaredExtensionSchemas()` von Hand genannten Erweiterungen, in dieser Reihenfolge: `sevenx_themes_media` (Schema und Daten),
   `ezstarrating`, `ezgmaplocation`, `eztags`, `explayouts` (Schema und Daten), `enhancedselection2`, `cjw_newsletter`, `syndication`.
2. Jede andere Erweiterung aus `extension_list` sowie `expai` und `exp_adminui`, die eine `share/db_schema.dba` oder eine
   `sql/<engine>/schema.sql` mitbringt (nur Schema).

`dropExtensionTables()` löscht zuerst jede Tabelle, die diese Schemas angeben, weil das „remove“ der Datenbank bei einer Neuinstallation nur
Tabellen namens `ez*` löscht und die anderen sonst die Zeilen der vorherigen Website behielten. `insertDBFile()` legt dann jedes Schema über
`eZDbSchema` an, überspringt Tabellen, die ein früheres Schema desselben Laufs bereits angelegt hat, und fällt auf einer Datenbank ohne
Handler für `eZDbSchema` auf Dateien `sql/<engine>/` zurück. `createMissingSchemaTables()` legt jede Tabelle an, die das Einfügen des Schemas
ausgelassen hat. Was das für die Layoutdaten bedeutet, steht auf [Layoutdaten und share-DBA-Dateien](layoutdaten-und-share-dba-dateien.md).

## Verwandte Seiten

- [sevenx_multisite](README.md)
- [Das Skript des Multisite-Installers](das-skript-des-multisite-installers.md)
- [Siteaccesses und Sprachen](siteaccesses-und-sprachen-von-sevenx-multisite.md)
- [Links zwischen Websites einer Multisite](../../../../en/us/features/6.0/multisite-cross-site-links.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
