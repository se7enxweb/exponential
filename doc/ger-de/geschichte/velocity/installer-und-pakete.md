# Repositorys für Installer und Pakete: Geschichte

*Teil der [Geschichte von Exponential Velocity](README.md).* Diese Seite behandelt die beiden kleinen Repositorys, die die Legacy-Installer enthalten: `ezwebin-ezpackage` (die Website-Pakete, die der Setup-Assistent installiert) und `exponential-legacy-installer` (den Composer-Installer, der den Legacy-Kernel und seine Erweiterungen an ihren Platz bringt). Die Release Notes beider stehen auch unter [ezwebin-ezpackage](../../../changelogs/extensions/ezwebin-ezpackage.md) und [exponential-legacy-installer](../../../changelogs/extensions/exponential-legacy-installer.md); die Paketübersicht steht unter [Paketübersicht der Plattform](../../spezifikationen/6.0/platform-paketzuordnung.md).

## Kurz gesagt

Siebzehn Änderungen in zweieinhalb Jahren, und jede zählt, wenn man auf sie stößt. Zwischen Dezember 2023 und März 2024 wurden die Website-Pakete von `ezwebin` so eingerichtet, dass sie sich über den Setup-Assistenten auf aktuellem PHP, MySQL 8 und SQLite installieren lassen. Im Juli 2026 wurden ihre Templates auf HTML5-Markup umgestellt. Der Installer für den Legacy-Kernel wurde im August 2025 unter einem neuen Paketnamen geforkt und im Juni 2026 für Composer 2.10 repariert.

## ezwebin-ezpackage

Das Repository enthält die Pakete, die der Setup-Assistent installiert: die Website-Pakete `ezwebin_site` und `ezwebin_site_clean`, ihre Demo-Inhalte, Designs (`ezwebin_design_blue`, `ezwebin_design_cleangray`, `ezwebin_design_gray`), Banner, Klassen und das Paket `ezwebin_extension`.

### Dezember 2023 bis Januar 2024: Installieren auf aktuellen Systemen

| Änderung | Commit | Was sie für Sie behebt |
|---|---|---|
| Fataler Fehler in den Einstellungen des Installers während des Setup-Assistenten | `52ee7a6` | Der Installer berechnete den Port des nächsten Siteaccess als `setting + 1`; die Einstellung wird jetzt zuerst in einen Integer umgewandelt (`(int)$this->setting( 'access_type_value' ) + 1`), was den fatalen Fehler verhindert, den der Commit nennt. Gilt für `ezwebin_site` und `ezwebin_site_clean`. |
| Unterstützung für SQLite im Paket-Installer | `5ce4b25` | Ist die Datenbank SQLite, liest der Installer das Schema einer Erweiterung aus `ezextension/<extension>/sql/sqlite/sqlite.sql`, sodass sich die Erweiterungen des Website-Pakets in eine SQLite-Datenbank installieren lassen (siehe [SQLite-Datenbank](../../funktionen/6.0/sqlite-datenbank.md)). Eine falsch geschriebene Fehlermeldung („shema“) wurde in derselben Änderung korrigiert. |
| `eZArchive` und MySQL 8 | `fc49287` | Die Archivklasse verwendet einen Konstruktor im Stil von PHP 5, und das SQL der Erweiterung läuft auf MySQL 8. |
| Blog-Templates auf MySQL 8 | `76897e9` | Templates für Blog-Inhalte werden korrekt angezeigt; die Composer-Konfiguration wurde aktualisiert. |
| Paketname in Composer | `afa7b90`, `0646383` | Vendor und Paketname auf das Paket von se7enxweb umgestellt; das Lizenzfeld auf einen gültigen Wert gesetzt. |
| Finanzierungsmetadaten | `f3a569a`, `d720ca8`, `f4fcdfa` | Keine für Anwender sichtbare Wirkung. |

### März 2024: aktualisierte Website-Pakete

`ba5f05a`, `d339474` und `51aebec` aktualisieren das Innere von `ezwebin_site` und `ezwebin_site_clean` (ihre `package.xml` und `settings/ezwebininstaller.php`, je etwa 100 Zeilen) für Release-Tests und beheben einen Fehler im Installer-Script des Pakets clean. Das sind die Pakete, die der Setup-Assistent liest, wenn Sie einen Website-Typ wählen.

### Juli 2026: HTML5-Markup in den Templates

`98c5c51` entfernt selbstschließende Schrägstriche im Stil von XHTML und veraltete Attribute `type` aus 56 Template-Dateien (etwa 2.670 geänderte Zeilen). Die Seitenausgabe ändert sich von `<input ... />` zu `<input ...>` und von `<script type="text/javascript">` zu `<script>`.

**Prüfen Sie vor dem Upgrade einer angepassten Website:** Ein Stylesheet oder Test, der genau das alte Markup erwartet (zum Beispiel einer, der nach `<script type="text/javascript">` sucht), braucht einen Blick; Browser sind nicht betroffen. Diese Änderung steht auch unter [Upgrade-Hinweise zur Engine von Velocity](../../bc/6.0/velocity-engine-upgrade-hinweise.md#templates-der-installationsprogramme-html5).

## exponential-legacy-installer

Ein Composer-Plugin, das den Legacy-Kernel und seine Erweiterungen in die richtigen Verzeichnisse installiert.

| Datum | Commit | Release | Änderung |
|---|---|---|---|
| 2025-08-25 | `6e8cf01` | 2.2.1 | Das Paket wird in `se7enxweb/exponential-legacy-installer` umbenannt (es hieß `netgen/ezpublish-legacy-installer`), und die PHP-Versionsbedingung wird um 8.2 erweitert. Den Fork gibt es, damit sich Änderungen vornehmen lassen, ohne auf das Upstream-Paket zu warten. |
| 2026-03-02 | `068ac1c` | | Finanzierungsmetadaten; keine für Anwender sichtbare Wirkung. |
| 2026-04-11 | `d80adb2` | 2.2.2 | `composer.json` deklariert jetzt `replace` sowohl für `ezsystems/ezpublish-legacy-installer` als auch für `se7enxweb/ezpublish-legacy-installer`, sodass der Resolver von Composer nie zwei Legacy-Installer nebeneinander installiert (die Ursache von Warnungen über mehrdeutige Classmaps bei `composer dump-autoload`). |
| 2026-06-19 | `bddac7c` | 2.2.3 | **Composer 2.10 scheitert nicht mehr bei weichen Updates des Legacy-Kernels.** Ist die Legacy-Wurzel auf `.` gesetzt, normalisiert Composer 2.10 `.` in `Filesystem::normalizePath()` zu einer leeren Zeichenkette; der Installer versuchte dann, Dateien in ein Verzeichnis ohne Namen zurückzukopieren. Der Installer löst Pfade aus Punkt oder leere Pfade jetzt vor dem Aufruf von `copyThenRemove()` zum absoluten Arbeitsverzeichnis auf. Ausdrückliche Verzeichnisse bleiben unberührt, und ältere Versionen von Composer funktionieren, weil absolute Pfade dort ebenfalls gültig sind. |

Wenn `composer update` scheitert, während es den Legacy-Kernel in ein Projekt mit der Legacy-Wurzel `.` zurückkopiert, aktualisieren Sie dieses Paket auf 2.2.3 oder neuer.

## Jede Änderung des Zeitraums

Aus dem Änderungsverzeichnis erzeugt: 17 Änderungen. „Einordnung“ ist die Einordnung für die Prüfung der Abdeckung (Funktion, Fehlerbehebung, BC, Sicherheit, Leistung, Dokumentation, Werkzeuge, Release, ohne Nutzen für Anwender). Merge-Commits und Neubauten des Archivs sind aufgeführt, damit nichts verborgen bleibt; die Änderung, die sie tragen, ist in ihrer eigenen Zeile eingeordnet. Die Betreffzeilen stehen so, wie sie im Repository festgehalten sind.

| Datum | Commit | Einordnung | Änderung | Release |
|---|---|---|---|---|
| 2023-12-24 | [`f3a569a`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/f3a569a) | ohne Nutzen für Anwender | Create FUNDING.yml |  |
| 2023-12-31 | [`52ee7a6`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/52ee7a6) | Fehlerbehebung | Updated: Updated Package Installer Settings PHP to Prevent Fatal Error on Installation via Setup Wizard |  |
| 2024-01-08 | [`5ce4b25`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/5ce4b25) | Funktion | Updated: Added SQLite Database Support |  |
| 2024-01-27 | [`d720ca8`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/d720ca8) | ohne Nutzen für Anwender | Updated: Updated funding informations |  |
| 2024-01-27 | [`fc49287`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/fc49287) | Fehlerbehebung | Updated: Updated eZArchive class to PHP5 Constructor and MySQL 8 Compatability To SQL |  |
| 2024-01-28 | [`afa7b90`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/afa7b90) | Werkzeuge | Update composer.json switched vendor name and package name and suggested and required vendors |  |
| 2024-01-28 | [`0646383`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/0646383) | Werkzeuge | Update composer.json updated composer license field to valid value |  |
| 2024-01-28 | [`76897e9`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/76897e9) | Fehlerbehebung | Updated: Bugfix for Mysql 8 compatability to blog content related template code displays. Updated composer configuration |  |
| 2024-03-05 | [`ba5f05a`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/ba5f05a) | Funktion | Updated: Updated ezwebin package internals for updated release testing |  |
| 2024-03-05 | [`d339474`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/d339474) | Funktion | Updated: Updated ezwebin_site_clean package internals for updated release testing |  |
| 2024-03-05 | [`51aebec`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/51aebec) | Fehlerbehebung | Updated: Bugfix for installer php for ezwebin clean |  |
| 2026-03-02 | [`f4fcdfa`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/f4fcdfa) | ohne Nutzen für Anwender | chore: add GitHub Sponsors funding metadata |  |
| 2026-07-19 | [`98c5c51`](https://github.com/se7enxweb/ezwebin-ezpackage/commit/98c5c51) | BC | Remove XHTML self-closing slashes and obsolete type attributes from ezwebin templates |  |
| 2025-08-25 | [`6e8cf01`](https://github.com/se7enxweb/exponential-legacy-installer/commit/6e8cf01) | Werkzeuge | Update composer.json replaced package vendor and package name. Forking for changes. | 2.2.1 |
| 2026-03-02 | [`068ac1c`](https://github.com/se7enxweb/exponential-legacy-installer/commit/068ac1c) | ohne Nutzen für Anwender | chore: add GitHub Sponsors funding metadata |  |
| 2026-04-11 | [`d80adb2`](https://github.com/se7enxweb/exponential-legacy-installer/commit/d80adb2) | Fehlerbehebung | fix: add replace shim for ezsystems counterpart package | 2.2.2 |
| 2026-06-19 | [`bddac7c`](https://github.com/se7enxweb/exponential-legacy-installer/commit/bddac7c) | Fehlerbehebung | Fix Composer 2.10 path normalization failure in legacy kernel installer | 2.2.3 |

## Verwandte Seiten

- Velocity-Chronik: [Übersicht](README.md).
- Betreiben: [Velocity, der Server mit dauerhaft laufenden Workern](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md) und die Anleitung [Eine Website betreiben](../../anleitungen/betrieb-einer-website.md).
- Installer: [Exponential mit einem Befehl installieren](../../funktionen/6.0/installation-mit-einem-befehl.md) und [Kickstarter](../../funktionen/6.0/kickstarter-cli.md).
