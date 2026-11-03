# Erweiterungen einer neuen Installation (6.0.15)

Diese Seite ist für alle, die Exponential 6.0.15 installieren und eine Liste der Erweiterungen suchen, die dazugehören:
welche mitgeliefert werden, welche ein `composer install` auf die Platte bringt, welche eine neue Installation
aktiviert und woher ihre Datenbanktabellen kommen. Sie ist die maßgebliche Liste. Andere Seiten verweisen hierher und
wiederholen sie nicht.

Die Liste ist aus dem Code gelesen: dem Ordner `extension/` des Kernel-Repositorys, der `composer.json` im
Wurzelverzeichnis, dem Paket `exponential-php-floor` (Linien 1.1, 1.2 und 1.3), dem Setup-Schritt
`kernel/setup/steps/ezstep_create_sites.php` mit `kernel/classes/expinstalldefaultextensions.php` und der
`extension_list` jedes Installers eines Site-Pakets.

## Wie eine Erweiterung auf die Platte kommt

- **Kernel**: liegt im Kernel-Repository selbst (`extension/` des Release).
- **composer.json**: von der `composer.json` im Wurzelverzeichnis verlangt, von `composer install` installiert.
- **php-floor**: von `se7enxweb/exponential-php-floor` verlangt, dem Paket, das die `composer.json` für jede
  unterstützte PHP-Linie verlangt. `composer install` installiert es genauso.

Kein Site-Paket kopiert mehr die Dateien einer Erweiterung. Seit 6.0.15 verlangt kein Site-Paket mehr die
Erweiterungspakete `ezwt_extension`, `ezstarrating_extension` und `ezgmaplocation_extension` (siehe [Site-Pakete
installieren keine Erweiterungspakete mehr](../../bc/6.0/seitenpakete-ohne-erweiterungspakete.md)). Die Kopie eines
Pakets wurde früher über die Kopie von Composer geschrieben.

## Wer sie aktiviert

- **Kernel (jede Installation)**: der Setup-Schritt aktiviert sie, welches Site-Paket es auch ist, wenn die Erweiterung
  auf der Platte liegt.
- **Admin (Zugriff)**: nur im Siteaccess der Administration eingeschaltet (`ActiveAccessExtensions[]`). Die
  Siteaccesses editor und Admin UI werden aus den Einstellungen der Administration gemacht.
- **Site-Pakete**: die `extension_list` des Installers. Die Site-Pakete heißen hier so. MS: `sevenx_multisite` und
  `sevenx_multisite_clean`. CJW: `cjw_multisite`. SITE: `sevenx_site` und `sevenx_site_clean`. WEBIN: `ezwebin_site`
  und `ezwebin_site_clean`. FLOW: `ezflow_site` und `ezflow_site_clean`. DEMO: `ezdemo_site` und `ezdemo_site_clean`.
  `plain_site` aktiviert nur, was der Kernel aktiviert.

## Die Liste

| Erweiterung | Zweck | Auf der Platte durch | Aktiviert in einer neuen Installation | Tabellen |
|---|---|---|---|---|
| ezjscore | Ajax-Serverfunktionen, Packen von Skripten und Stylesheets, jQuery 4 | Kernel | Kernel | |
| ezoe | der Online-Editor für formatierten Text | Kernel | Kernel | |
| ezformtoken | Formular-Token gegen Cross-Site Request Forgery | Kernel | Kernel | |
| expimageeditor | der Bildeditor der Administration | Kernel | Kernel | |
| expservices | entfernte Dienste (Portal, Partner) | Kernel | MS, CJW, SITE | ja |
| exphelpsite | das Design der Hilfe-Site | Kernel | nicht standardmäßig | |
| ezodf | OpenDocument-Import und -Export | php-floor | Kernel | |
| ezprestapi | REST-API v1 und v2 | composer.json | Kernel | |
| ezmultiupload | mehrere Dateien auf einmal hochladen | composer.json | Kernel | |
| eztags | Tags und der Tags-Datentyp | php-floor | Kernel | ja |
| ezautosave | automatisches Speichern von Entwürfen | composer.json | Kernel | |
| ezownerchange | den Besitzer von Inhalten ändern | php-floor | Kernel | |
| sevenx_authentication_2fa | Anmeldung in zwei Schritten | php-floor | Kernel | |
| ezoracle | der Oracle-Datenbanktreiber | composer.json | Kernel, nur auf Oracle; MS, CJW, SITE | |
| sqliimport | Inhaltsimporte | php-floor | Kernel: Admin (Zugriff) | ja |
| expui | die jQuery-4-Oberflächen-API der Designs | composer.json | MS, CJW, SITE; Kernel: Admin (Zugriff), wo sie nicht global ist | |
| expqueue | die Job-Warteschlange | php-floor | MS, CJW, SITE; Kernel: Admin (Zugriff), wo sie nicht global ist | ja |
| exp_adminui | das Admin-UI-Aussehen der Administration | php-floor | Kernel: admin, editor und adminui (Zugriff) | |
| expai | der Schreibassistent (durch sein eigenes `Enabled=false` ausgeschaltet) | php-floor | MS, CJW, SITE: Admin (Zugriff) | ja |
| expwt | die Website-Seitenleiste, die ezwt ersetzt | siehe unten | MS, CJW, SITE, WEBIN, FLOW, DEMO (siehe unten) | |
| ezwt | die Website-Werkzeugleiste, durch expwt ersetzt | composer.json | keines (siehe unten) | |
| ezstarrating | der Datentyp Sternebewertung | composer.json | MS, CJW, SITE, WEBIN, FLOW, DEMO | ja |
| ezgmaplocation | der Datentyp Kartenposition | composer.json | MS, CJW, SITE, WEBIN, FLOW, DEMO | ja |
| ezwebin | das Design Website Interface | composer.json | MS, CJW, SITE, WEBIN, FLOW, DEMO | |
| ezie | der klassische Bildeditor | composer.json | MS, CJW, SITE, WEBIN, FLOW, DEMO | |
| ezpaypal | Zahlungen mit PayPal | composer.json | MS, CJW, SITE, WEBIN, FLOW, DEMO | |
| xrowmetadata | der Datentyp Meta Data (Titel, Beschreibung, Schlüsselwörter) | composer.json | MS, CJW, SITE, WEBIN, FLOW, DEMO | |
| xrowextract | Export von Inhalten | php-floor | MS, CJW, SITE, WEBIN, FLOW, DEMO | ja |
| bccie | Export gesammelter Informationen | php-floor | MS, CJW, SITE, WEBIN, FLOW, DEMO | |
| bcgooglesitemaps | Sitemaps | composer.json | MS, CJW, SITE, WEBIN, FLOW, DEMO | |
| bcwebsitestatistics | Web-Statistiken | composer.json | MS, CJW, SITE, WEBIN, FLOW, DEMO | |
| owsimpleoperator | zusätzliche Template-Operatoren | composer.json | MS, CJW, SITE, WEBIN, FLOW, DEMO | |
| swark | zusätzliche Template-Operatoren | composer.json | MS, CJW, SITE, WEBIN, FLOW, DEMO | |
| ezflow | Seitenlayouts mit Blöcken (klassisch) | composer.json | FLOW, DEMO | ja |
| ezdemo | das Demo-Design | composer.json | DEMO | |
| sevenx_themes_simple | das Design simple | composer.json | MS, CJW, SITE | |
| sevenx_themes_media | das Design media | php-floor | MS, CJW | ja |
| cjw_themes_jumper, cjw_tmv | die CJW-Designs | php-floor | CJW | |
| explayouts, seine Teile explayouts_* und expquery_translator | Exponential Layouts | php-floor | MS, CJW | ja |
| expsite_api, expsite_core, expsite_app | die Site-API des Designs media | php-floor | MS, CJW | |
| ezurlmultisite | Adressen über Sites hinweg | composer.json | MS, CJW | |
| cjw_newsletter | Newsletter | php-floor | MS, CJW, SITE | ja |
| syndication | Syndizierung von Inhalten | php-floor | MS, CJW, SITE | ja |
| sevenx_dse | die Datenspeicher-Engine | php-floor | MS, CJW, SITE | |
| git_manager | Git-Checkouts und Sicherungspunkte aus der Administration | php-floor | MS, CJW, SITE | |
| ezupdate | Aktualisierungen aus der Administration | composer.json | MS, CJW, SITE | |
| exp_enhanced_link, expchangeclass, ngclasslist, powercontent | Link-Datentyp, Klassenwechsel, Klassenliste, Inhalte in Mengen | php-floor, composer.json | MS, CJW, SITE | |
| birthday, enhancedezbinaryfile, enhancedselection2 | Datentypen | composer.json | MS, CJW, SITE | enhancedselection2: ja |
| hcaptcha, recaptcha | Schutz vor Spam | composer.json | MS, CJW, SITE | |
| ezmbpaex | Ablauf von Passwörtern | composer.json | nicht standardmäßig | ja |
| expsite_data_media, expsite_installer | die Daten und der Installer der Media-Site | php-floor | nicht aktiviert | |

„Tabellen“ heißt, dass die Erweiterung eine `share/db_schema.dba` mitbringt.

## ezwt und expwt

expwt, die Website-Seitenleiste, ersetzt ezwt, die Website-Werkzeugleiste. Die Installer der Site-Pakete von 6.0.15
setzen `expwt` in ihre `extension_list`, wo `ezwt` stand. Sie verlangen das Paket `ezwt_extension` nicht mehr. Die neu
gebauten Site-Pakete werden zusammen mit dem ersten Release von expwt und den Designs, die die Seitenleiste nutzen,
auf dem Paketserver veröffentlicht. Bis dahin aktivieren die veröffentlichten Site-Pakete noch ezwt. Die
`composer.json` verlangt `se7enxweb/ezwt`, bis Composer expwt installiert. Das Paket von expwt ersetzt ezwt (Composer
`replace`), also entfernt Composer `extension/ezwt`, wenn expwt kommt. Eine bestehende Installation zieht mit
`./console exp:wt:migrate-from-ezwt --apply` um oder mit dem Upgrade-Schritt `6.0.15-expwt-enable` (siehe [Die
Website-Seitenleiste](website-seitenleiste.md)). Der eigene Standard von expwt ist `Enabled=disabled` in `expwt.ini`
bis expwt 1.0, die Seitenleiste erscheint also nur, wo `[ExpwtSettings] Enabled=enabled` gesetzt ist.

## Woher die Tabellen kommen

Eine Erweiterung mit einer `share/db_schema.dba` erhält ihre Tabellen aus dieser Datei, auf jeder Engine (MySQL und
MariaDB, PostgreSQL, SQLite, Oracle mit ezoracle, MongoDB-Collections), über die Schema-Klasse der Engine:

- **Neue Installation**: nach dem Site-Paket legt der Setup-Schritt die fehlenden Tabellen jeder aktiven Erweiterung
  mit einer `share/db_schema.dba` an, global oder in der Administration
  (`expInstallDefaultExtensions::ensureExtensionTables()`). Die gepflegten Site-Pakete legen sie schon in ihrer eigenen
  Nachinstallation aus derselben Datei an. Die älteren Installer (ezwebin, ezflow, ezdemo) rufen für ezstarrating und
  ezgmaplocation dieselbe Kernel-Funktion auf.
- **Bestehende Installation**: der Upgrade-Schritt `6.0.15-active-extension-tables` (`./console exp:upgrade`) tut
  dasselbe für jede Erweiterung, die global oder in einem Siteaccess aktiv ist. Der Probelauf zeigt das SQL. Siehe
  [Aktualisieren mit der Konsole](../../anleitungen/aktualisieren-mit-der-konsole.md).

Fehlende Tabellen werden angelegt. Nichts wird gelöscht oder geändert, und keine `share/db_data.dba` wird geladen.
Eine Datendatei enthält die Startdaten einer Site, etwa die Layouts einer Demo, und sie bleibt bei dem Site-Paket, das
diese Site kennt. ezstarrating braucht `ezstarrating` und `ezstarrating_data`. ezgmaplocation braucht
`ezgmaplocation`.

## Verwandte Seiten

- [Was eine neue Installation mitbringt: die Standard-Auslieferung der Erweiterungen](standard-extension-auslieferung.md)
  (die Geschichte der `composer.json`)
- [Erweiterungen, Themes und Pakete](erweiterungen/README.md)
- [Die Erweiterungen, die sevenx_multisite aktiviert](../../hilfe/seitenpakete/sevenx-multisite/erweiterungen-die-sevenx-multisite-aktiviert.md)
- [Upgrade-Schritte für Erweiterungen](../../anleitungen/upgrade-schritte-fuer-erweiterungen.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
