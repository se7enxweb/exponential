---
title: Glossar
order: 9999
---
# Glossar

Jeder Begriff, den die Dokumentation verwendet, in einer Zeile, mit einem Link auf die Seite, die ihn erklärt. Die
Begriffe stehen in alphabetischer Reihenfolge ihres englischen Namens; die deutsche Entsprechung steht in Klammern. Ein
**fett** gesetzter Begriff in einer Definition hat einen eigenen Eintrag. Für einen ersten Gang durch das Produkt
nutzen Sie [die Anleitungen](anleitungen/README.md); für die Geschichte hinter einem Begriff [die Historie](geschichte/README.md).

## Kurz gefasst

- Eine Zeile pro Begriff; folgen Sie dem Link für die Anleitung, die Einstellungen und die Beispiele.
- Namen, die mit `exp` oder `sevenx` beginnen, sind Extensions oder Klassen von 7x (`exp` kennzeichnet Arbeit, die 7x dem Projekt Exponential und der Exponential Foundation zuordnet); Namen, die mit `ez` beginnen, sind ältere Extensions und Kernel-Bezeichner, die ihre Namen behalten. [Namenspräfixe: sevenx und exp](standards/namenspraefixe-sevenx-und-exp.md).
- Befehle werden so gezeigt, wie Sie sie im Wurzelverzeichnis der Installation eingeben: `./console exp:<name>` ist dasselbe Programm wie `php bin/php/<name>.php`.

## Inhalt

[A](#a) · [B](#b) · [C](#c) · [D](#d) · [E](#e) · [F](#f) · [G](#g) · [H](#h) · [I](#i) · [J](#j) · [K](#k) · [L](#l) · [M](#m) · [N](#n) · [O](#o) · [P](#p) · [Q](#q) · [R](#r) · [S](#s) · [T](#t) · [U](#u) · [V](#v) · [W](#w) · [X](#x) · [Y](#y) · [Z](#z)

## A

- **admin, admin2, admin3, admin4, classic**: die Administrationsdesigns. `admin3` funktioniert auf Telefonen und breiten Bildschirmen; `admin4` ist ein vollständiges Design mit hellem und dunklem Modus; `classic` ist die graue Oberfläche ohne YUI. Siehe [admin3](funktionen/6.0/admin3-responsives-admin.md), [admin4](funktionen/6.0/admin4-design.md), [admin4l](funktionen/6.0/admin4l.md) (admin4, aus Layouts gebaut), [klassisches Admin-Theme](funktionen/6.0/erweiterungen/sevenx_themes_admin_classic.md).
- **AdminAid**: eine Extension mit einer Debugging-Hilfe für Administratoren. [AdminAid](funktionen/6.0/erweiterungen/AdminAid.md).
- **adminneo**: ein Datenbankmanager (ein Fork von Adminer), den die DSE-Extension in den Admin einbettet. [AdminNeo](funktionen/6.0/adminneo-datenbankverwaltung.md).
- **Apache**: der Webserver, der die Installation zusammen mit **PHP-FPM** betreiben kann. [Deployment](anleitungen/bereitstellung.md).
- **attribute** (Attribut): ein Feld einer **content class**, typisiert durch einen **datatype**. [Inhaltsmodell](anleitungen/inhaltsmodell-und-bearbeitung.md).
- **audit channel** (Audit-Kanal): einer der fünf per Hash verketteten Ströme des **audit trail** (`content`, `access`, `system`, `commerce`, `read`). [Audit-Ereignismodell](spezifikationen/6.0/audit-ereignismodell.md).
- **audit source** (Audit-Quelle): eine Klasse, die eine Extension in `audit.ini [AuditSourceSettings] Sources[]` registriert, um ihren eigenen Kanal in der Audit-Konsole anzuzeigen (Bereich, Links, Diagramme, CSV-Spalten). [Audit-Anleitung](bc/6.0/audit.md).
- **audit trail** (Prüfprotokoll): ein manipulationssicheres Log sicherheitsrelevanter Ereignisse (Anmeldungen, Inhaltsänderungen, geschriebene Einstellungen, geleerte Caches). [Prüfprotokoll](funktionen/6.0/pruefprotokoll.md), [Ereignismodell](spezifikationen/6.0/audit-ereignismodell.md).
- **autoload**: die generierten Klassenzuordnungen (`var/autoload`), die PHP sagen, wo jede Klasse liegt; neu erzeugen mit `php bin/php/ezpgenerateautoloads.php`. [Eine Website betreiben](anleitungen/betrieb-einer-website.md).

## B

- **bc note** (Hinweis zu Verhaltensänderungen): eine Seite, die sagt, was sich im Verhalten geändert hat und was Sie bei einer Aktualisierung prüfen sollten. [Verhaltensänderungen, Juli und August 2026](bc/6.0/verhaltensaenderungen-2026-07-08.md).
- **bcgooglesitemaps**: eine Extension, die **sitemaps** für Suchmaschinen als **cronjob** erzeugt. [bcgooglesitemaps](funktionen/6.0/erweiterungen/bcgooglesitemaps.md).
- **bcwebsitestatistics**: eine Extension, die Seitenaufrufe und Shop-Käufe an einen Analysedienst sendet. [bcwebsitestatistics](funktionen/6.0/erweiterungen/bcwebsitestatistics.md).
- **benchmark**: das Vermessen einer Installation: Seitenzeiten über HTTP, zwei Server direkt nacheinander, der Kernel im Prozess und ein Vergleich mit einem gespeicherten Lauf (`exp:benchmark`). [Benchmark](funktionen/6.0/benchmark.md).
- **block (layout)** (Block): eine Inhaltseinheit, die in einer **zone** eines **layout** platziert ist. [Exponential Layouts](bc/6.0/layouts.md).
- **bridge**: siehe **legacy bridge**.

## C

- **cache**: gespeicherte Ergebnisse, die Seiten schneller machen; Exponential hat viele davon (Inhaltsansicht, Template, INI, HTTP, statisch, SQL-Abfrage, Antwort). [Cache-Steuerung](bc/6.0/cache-konsole.md), [Eine Website betreiben](anleitungen/betrieb-einer-website.md).
- **cache block** (Cache-Block): ein Template-Tag, der das Markup darin zwischenspeichert; die Valkey-Variante ist `{valkey-block}`. [Redis- und Valkey-Caches](funktionen/6.0/erweiterungen/sevenx_valkey_cache.md).
- **cache variant key (UI switch)** (Cache-Variantenschlüssel): der kurze Schlüssel (`language-ger-DE`), der Caches getrennt hält, die mit der Auswahl eines Benutzers im **UI switch** gebaut wurden: `{cache-block}`, der Cache der Inhaltsansicht und kompilierte Templates; leer, wenn nichts gewählt ist, sodass sich kein Cache-Name ändert. [API für UI-Switch-Provider](bc/6.0/ui-umschalter.md#der-varianten-schlüssel-des-caches).
- **changelog** (Changelog): die Versionshinweise einer Version, in Zeilen Added, Updated, Removed und Renamed. [6.0.15](../changelogs/6.0/6.0.15.md), [Extensions](../changelogs/extensions/README.md).
- **chronicle** (Chronik): die Geschichte Monat für Monat. [Historie](geschichte/README.md).
- **CI (continuous integration)** (kontinuierliche Integration): die GitHub-Actions-Workflows: `phpunit.yml` führt die PHPUnit-Suites auf PHP 8.1 bis 8.5 aus, `quality.yml` prüft bei jeder Änderung Syntax (auch auf PHP 8.0), das Dateimanifest, Commit-Nachrichten, Code-Stil und PHPStan. [Qualitätsprüfungen](funktionen/6.0/qualitaetspruefungen.md), [Kontinuierliche Integration](spezifikationen/6.0/kontinuierliche-integration.md).
- **cjw_newsletter**: ein vollständiges Newsletter-System. [cjw_newsletter](funktionen/6.0/erweiterungen/cjw_newsletter.md).
- **CLA (Contributor Licensing Agreement)**: die Vereinbarung mit 7x, die 7x und die Exponential Foundation einmalig unterschrieben verlangen, bevor ein Code-Beitrag zum Kern-Kernel (`se7enxweb/exponential`) übernommen wird, gleich auf welchem Weg er eingereicht wird (Pull Request, Patch, Issue, E-Mail). Mit Unterschrift und Einreichung überträgt der Beitragende das Urheberrecht am Beitrag an 7x, das sein Eigentümer wird, und gibt die Kontrolle darüber auf: 7x darf ihn auf jede Weise verwenden, ändern, neu lizenzieren und verbreiten, auch unter anderen Lizenzen und in kommerziellen Produkten, ohne den Beitragenden zu fragen oder zu bezahlen, der ihn weder zurückziehen noch an Bedingungen knüpfen kann. Eigene Extensions der Benutzer sind nicht betroffen. [Urheberrechtsübertragung (CLA)](../../CONTRIBUTING.md#copyright-assignment-cla).
- **class**: siehe **content class**.
- **classic**: siehe **admin**.
- **cluster (file cluster)** (Datei-Cluster): Dateispeicherung in der Datenbank oder einem gemeinsamen Speicher, damit mehrere Server dieselben Dateien sehen. [Eine Website betreiben](anleitungen/betrieb-einer-website.md).
- **collected information** (gesammelte Informationen): Antworten, die Besucher über Formulare einreichen (Kontakt, Umfragen), pro Objekt gespeichert und exportierbar. [bccie](funktionen/6.0/erweiterungen/bccie.md).
- **Composer**: der PHP-Paketmanager; die `composer.json` der Installation entscheidet, welche Extensions ankommen. [Standardverteilung der Extensions](funktionen/6.0/standard-extension-auslieferung.md).
- **console** (Konsole): der Befehl `./console`, der jeden Befehl einer Installation findet und ausführt (`exp:`, `bin:`, `cron:`, `ext:`). [Konsole](bc/6.0/konsole.md).
- **consent log** (Einwilligungsprotokoll): die Aufzeichnung jeder Änderung der E-Mail-Einstellungen einer Person, mit Zeit, Quelle, dem genauen Text, den die Person sah, und der IP-Adresse; bei einer Löschung anonymisiert. [E-Mail-Einstellungen](funktionen/6.0/e-mail-einstellungen.md), [Spezifikation](spezifikationen/6.0/mail-einstellungen.md).
- **content checks** (Inhaltsprüfungen): **Setup > Nicht verknüpfte Objekte** und **Ungültige Relationen**: die Assets (Bilder, Dateien, Medien) unterhalb eines Knotens, die nichts verwendet (zum Verschieben in den Papierkorb), und sichtbare Inhalte, die auf gelöschte, im Papierkorb liegende, versteckte oder unveröffentlichte Inhalte verweisen. [Die Inhaltsprüfungen](funktionen/6.0/inhaltspruefungen.md).
- **content class** (Inhaltsklasse): die Definition einer Art von Inhalt (zum Beispiel Artikel) als Liste von **attributes**. [Inhaltsmodell](anleitungen/inhaltsmodell-und-bearbeitung.md).
- **content finder** (Inhaltsfinder): **Setup > Inhaltsfinder** und ein Feld unterhalb der Inhaltsstruktur: Finden Sie den Inhalt hinter einer ID, einer Remote-ID, einem URL-Alias, einer Seitenadresse oder einem Dateipfad und handeln Sie im Rahmen Ihrer Rechte darauf. [Inhaltsfinder](funktionen/6.0/inhalts-finder.md).
- **content job** (Inhaltsauftrag): ein umfangreiches Entfernen, Kopieren oder Verschieben, das im Hintergrund läuft statt in einer Webanfrage. [Inhaltsaufträge](funktionen/6.0/inhaltsauftraege.md).
- **content object** (Inhaltsobjekt): ein Inhaltsstück, eine Instanz einer **content class**, mit Versionen und Übersetzungen; es erscheint im Baum über einen oder mehrere **nodes**. [Inhaltsmodell](anleitungen/inhaltsmodell-und-bearbeitung.md).
- **content tree** (Inhaltsbaum): die Hierarchie der **nodes**, die der Admin zeigt; Knoten 1 ist seine Wurzel. [Inhaltsmodell](anleitungen/inhaltsmodell-und-bearbeitung.md).
- **content view cache** (Cache der Inhaltsansicht): das gespeicherte Ergebnis des Renderns der Ansicht eines Knotens; geleert durch `exp:cache`. [Cache-Steuerung](bc/6.0/cache-konsole.md).
- **coverage** (Abdeckung): der generierte Nachweis, dass jede Änderung in der Dokumentation berücksichtigt ist. [Abdeckung](geschichte/abdeckung.md).
- **cron / cronjob part** (Cronjob-Teil): eine geplante Wartungsaufgabe (`cron:<name>`, ausgeführt von `runcronjobs.php`). [Cronjobs in der Konsole](funktionen/6.0/cronjobs-in-der-konsole.md), [Eine Website betreiben](anleitungen/betrieb-einer-website.md).

## D

- **database drivers** (Datenbanktreiber): MySQL und MariaDB, PostgreSQL, SQLite, MongoDB und Oracle. [SQLite](funktionen/6.0/sqlite-datenbank.md), [MongoDB](funktionen/6.0/mongodb-datenbankunterstuetzung.md), [Oracle und SQLite](spezifikationen/6.0/datenbanktreiber-sqlite-oracle.md).
- **datatype** (Datentyp): der Code, der eine Art von **attribute** speichert, prüft, bearbeitet und anzeigt (Text, Bild, Beziehung, Tags). [Härtung von Datentypen und Eingaben](spezifikationen/6.0/datentyp-eingabehaertung.md).
- **design** (Design): ein Satz aus Templates, Stilen und Bildern; der **siteaccess** wählt, welche Designs verwendet werden. [Erste Schritte](anleitungen/erste-schritte.md).
- **design.ini**: die Einstellungsdatei, die Designs, Stylesheets und Skripte auflistet. [INI-Überschreibungen für mehrere Websites](funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md).
- **DFS**: die Datenbank-Dateisystem-Variante des **cluster**-Handlers. [Eine Website betreiben](anleitungen/betrieb-einer-website.md).
- **digest (notification)** (Sammelbenachrichtigung): eine E-Mail, die die Benachrichtigungen eines Tages, einer Woche oder eines Monats zusammenfasst, statt einer E-Mail pro Änderung. [Benachrichtigungen](funktionen/6.0/benachrichtigungen.md), [Spezifikation](spezifikationen/6.0/benachrichtigungen.md).
- **double opt-in**: eine Art von E-Mail (Newsletter, Angebote) einschalten oder eine neue Adresse verwenden, erst nachdem der an die Adresse gesendete Link bestätigt wurde. [E-Mail-Einstellungen](funktionen/6.0/e-mail-einstellungen.md).
- **DSE** (`sevenx_dse`): der Database Source Editor, der **adminneo** in den Admin einbettet. [sevenx_dse](funktionen/6.0/erweiterungen/sevenx_dse.md).
- **DXP skeleton** (DXP-Skeleton): ein Startprojekt für die auf Symfony basierende Plattform. [Platform DXP Skeleton](funktionen/6.0/plattform-dxp-skeleton.md).

## E

- **e-mail preferences** (E-Mail-Einstellungen): die Seite, auf der jede Person jede optionale Art von E-Mail ein- oder ausschaltet, den Verlauf sieht und die Daten herunterlädt (`/mailpreferences/settings`). [Anleitung für Benutzer](funktionen/6.0/e-mail-einstellungen.md), [Anleitung für Administratoren](anleitungen/mail-einstellungen-administrator.md), [Checkliste zur Rechtslage](spezifikationen/6.0/mail-einstellungen-compliance.md).
- **engine (web server engine)** (Engine, Webserver-Engine): das Programm, das die Website ausliefert: `php` (eingebauter Server), `qbix` (**Velocity**) oder `frankenphp`. [Velocity-Engines](bc/6.0/velocity-engines.md).
- **engine.phar**: das Archiv aus `kernel/`, `lib/` und `autoload/`, das Velocity laden kann. [Engine-Archiv](bc/6.0/phar.md).
- **Exp Debug bar** (Exp-Debug-Leiste): eine Leiste auf Seiten, die Administratoren Abfragen, Zeiten und Template-Informationen zeigt. [Exp-Debug-Leiste](funktionen/6.0/exp-debug-leiste.md).
- **exp (prefix)** (Präfix exp): ein Code-Präfix, das 7x strategisch und gezielt verwendet, um die Marke Exponential aufzubauen und das Eigentum an einer Extension oder Lösung dem Projekt Exponential und der Exponential Foundation zuzuordnen (`explayouts`, `expai`, `expHelpAudience`); neuer Kernel-Code verwendet es. [Namenspräfixe: sevenx und exp](standards/namenspraefixe-sevenx-und-exp.md).
- **exp:** das Präfix der Konsolenbefehle, die Skripte aus `bin/php/*.php` ausführen. [Konsole](bc/6.0/konsole.md).
- **exp:about**: die **system card** plus das Projekt: was Exponential ist, die Lizenz, Links, wie man beiträgt. [Die Systemkarte](funktionen/6.0/systemkarte.md#expabout-die-karte-und-das-projekt).
- **exp:expfetch**: der Konsolenbefehl, der die **system card** zeichnet: das „e“ von Exponential neben den Fakten der Installation, ihrer Engine und ihrer Maschine, mit Hinweisen zum Zustand. [Die Systemkarte](funktionen/6.0/systemkarte.md).
- **exp:info**: die **system card** plus eine Bestandsaufnahme: Extensions und Versionen, Siteaccesses, Einstellungen, Cronjobs, Caches, Abdeckung der Hilfe. [Die Systemkarte](funktionen/6.0/systemkarte.md#expinfo-die-karte-und-eine-bestandsaufnahme).
- **exp:ini**: der Befehl, der **INI**-Einstellungen in jedem Geltungsbereich liest und schreibt. [exp:ini](funktionen/6.0/exp-ini-befehl.md).
- **exp_enhanced_link**: die Extension, die den erweiterten Link-Feldtyp bereitstellt. [exp_enhanced_link](funktionen/6.0/erweiterungen/exp_enhanced_link.md).
- **explayouts**: die Extensions, die Layouts, Blöcke, Zonen und Regeln in den Admin bringen. [Exponential Layouts](bc/6.0/layouts.md), [explayouts_ui](funktionen/6.0/erweiterungen/explayouts_ui.md), [API-Spezifikation](spezifikationen/6.0/explayouts-ui-api.md).
- **Exponential Intelligence** (expai): der Schreibassistent der Administrationsoberfläche; er schreibt Entwürfe als der Redakteur, mit dessen Berechtigungen, und veröffentlicht nie von sich aus. [expai](funktionen/6.0/erweiterungen/expai.md), [README](../README.md#exponential-intelligence-new).
- **Exponential Platform**: die auf Symfony basierende Produktlinie (`bin/console`, Befehle `exponential:*`), die Exponential 6 über die **legacy bridge** ausführen kann. [Paketübersicht der Plattform](spezifikationen/6.0/platform-paketzuordnung.md), [Befehlsnamen](spezifikationen/6.0/platform-konsolenbefehle.md).
- **expqueue**: die Extension für die Auftragswarteschlange: Prioritätsklassen, faire Planung, Wiederholungen und eine Dead-Letter-Ablage, ausgeführt per Cron oder einem systemd-Worker, **Setup > Job queue**. [expqueue](funktionen/6.0/erweiterungen/expqueue.md).
- **expservices**: die Bibliothek entfernter Dienste, gebaut aus Server-Funktionen von **ezjscore**. [Entfernte Dienste](funktionen/6.0/entfernte-dienste-expservices.md), [Spezifikation](spezifikationen/6.0/expservices.md).
- **expui** (Exponential UI): die JavaScript-Komponentenschicht (`exp::*`), die YUI im Admin ersetzt hat. [jQuery 4 und die Entfernung von YUI](funktionen/6.0/jquery4-und-entfernung-von-yui.md).
- **extension** (Extension): ein Ordner unter `extension/`, der Einstellungen, Klassen, Module, Templates oder Designs hinzufügt; aktiviert in `ActiveExtensions`. [Liste der Extensions](funktionen/6.0/extension-liste-und-downloads.md), [Ladereihenfolge](funktionen/6.0/ladereihenfolge-der-extensions.md).
- **extension metadata** (Metadaten einer Extension): Version, Lizenz und Website einer Extension, gelesen von der Seite **Über**. [Metadaten von Extensions](spezifikationen/6.0/extension-metadaten.md).
- **ezautosave**: automatisches Speichern des Entwurfs im Bearbeitungsformular. [ezautosave](funktionen/6.0/erweiterungen/ezautosave.md).
- **ezdemo**: die Demo-Website von Exponential. [ezdemo](funktionen/6.0/erweiterungen/ezdemo.md).
- **ezflow**: der Seitenbaukasten des klassischen Designs (ein Seiten-Datentyp mit Zonen und Blöcken). [ezflow](funktionen/6.0/erweiterungen/ezflow.md).
- **ezformtoken**: die Kernel-Extension, die Formulare mit einem Token schützt; siehe **form token**. [Seite „Formular abgelaufen“](funktionen/6.0/seite-abgelaufenes-formular.md).
- **ezgmaplocation**: ein Datentyp für Breiten- und Längengrad. [ezgmaplocation](funktionen/6.0/erweiterungen/ezgmaplocation.md).
- **ezie**: der Bildeditor im Bearbeitungsformular. [ezie](funktionen/6.0/erweiterungen/ezie.md).
- **ezjscore**: das JavaScript-Fundament: packt Skripte und Stile, lädt jQuery, beherbergt Server-Funktionen. [ezjscore](funktionen/6.0/erweiterungen/ezjscore.md).
- **ezmbpaex**: Regeln für den Ablauf von Passwörtern. [ezmbpaex](funktionen/6.0/erweiterungen/ezmbpaex.md).
- **ezmultiupload**: viele Dateien auf einmal in Ordner und Galerien hochladen. [ezmultiupload](funktionen/6.0/erweiterungen/ezmultiupload.md).
- **ezodf**: Import und Export von OpenDocument-Text. [ezodf](funktionen/6.0/erweiterungen/ezodf.md).
- **ezoe**: der Online-Editor für Rich Text (TinyMCE 3 und TinyMCE 8). [ezoe](funktionen/6.0/erweiterungen/ezoe.md), [TinyMCE 8](funktionen/6.0/online-editor-tinymce8.md).
- **ezpaypal**: das PayPal-Zahlungs-Gateway. [ezpaypal](funktionen/6.0/erweiterungen/ezpaypal.md).
- **ezpkg**: das Paketarchivformat (`.ezpkg`), das Inhalte, Klassen, Templates und Dateien enthält. [ezpm](funktionen/6.0/ezpm-paketmanager-cli.md), [Pakete vergleichen und importieren](funktionen/6.0/pakete-vergleichen-und-importieren.md).
- **ezpm** (Befehl): der Paketmanager auf der Kommandozeile (`ezpm.php`). [ezpm](funktionen/6.0/ezpm-paketmanager-cli.md). Nicht zu verwechseln mit der **ezpm-Extension**, die private Nachrichten bereitstellt: [ezpm-Extension](funktionen/6.0/erweiterungen/ezpm.md).
- **ezprestapi**: die REST-API-Extension, die REST API v1 und v2 bereitstellt (Lesen in beiden, Schreiben in v2) für entfernte Inhaltsaktualisierungen; sie ersetzt das veraltete **ezprestapiprovider**. [ezprestapi](funktionen/6.0/erweiterungen/ezprestapi.md), [REST API v1 und v2](anleitungen/rest-api-v1-und-v2.md).
- **ezstarrating**: ein Datentyp für Sternebewertungen. [ezstarrating](funktionen/6.0/erweiterungen/ezstarrating.md).
- **eztags**: ein Tag-Datentyp mit einem verwalteten Tag-Baum. [eztags](funktionen/6.0/erweiterungen/eztags.md).
- **ezupdate**: die Paket- und Aktualisierungsprüfung einer Installation. [ezupdate](funktionen/6.0/erweiterungen/ezupdate.md).
- **ezwebin**: die klassische fertige Website. [ezwebin](funktionen/6.0/erweiterungen/ezwebin.md).
- **ezwt**: die Website-Werkzeugleiste, die Redakteure auf der öffentlichen Website sehen. [ezwt](funktionen/6.0/erweiterungen/ezwt.md).
- **ezxmlexport**: geplanter XML-Export mit Auslieferung per FTP. [ezxmlexport](funktionen/6.0/erweiterungen/ezxmlexport.md).

## F

- **feature page** (Funktionsseite): eine Anleitungsseite für eine für Benutzer sichtbare Funktion, mit Einstellungen, Beispielen und Grenzen. [Funktionen](funktionen/6.0/erweiterungen/README.md).
- **fetch function** (Fetch-Funktion): eine Template-Funktion, die Inhalte oder Systemdaten liest (`fetch('content','list',...)`). [Inhaltsmodell](anleitungen/inhaltsmodell-und-bearbeitung.md).
- **file consistency check** (Prüfung der Dateikonsistenz): **Setup > Upgrade-Prüfung**, die installierte Dateien mit den ausgelieferten Prüfsummen vergleicht. [Prüfung der Dateikonsistenz](funktionen/6.0/dateikonsistenzpruefung.md).
- **form token** (Formular-Token): der versteckte Wert, der ein Formular vor Cross-Site Request Forgery schützt. [Seite „Formular abgelaufen“](funktionen/6.0/seite-abgelaufenes-formular.md).
- **FrankenPHP**: eine produktionsreife Web-Engine (Caddy mit eingebautem PHP), wählbar neben **Velocity**. [FrankenPHP](bc/6.0/frankenphp.md).

## G

- **git_manager**: Web-Admin-Werkzeuge für die Git-Seite einer Installation. [git_manager](funktionen/6.0/erweiterungen/git_manager.md).
- **guide** (Anleitung): ein Tutorial, das Sie in wenigen Minuten zu einem funktionierenden Ergebnis führt. [Anleitungen](anleitungen/README.md).

## H

- **hCaptcha**: ein Captcha-Datentyp. [hcaptcha](funktionen/6.0/erweiterungen/hcaptcha.md).
- **hook** (Hook): eine Stelle, an der der Kernel eine Extension aufruft, wenn ihre Klasse existiert, zum Beispiel die Hooks des Valkey-Cache. [Redis- und Valkey-Caches](funktionen/6.0/erweiterungen/sevenx_valkey_cache.md).
- **HTTP cache (role-aware)** (rollenbewusster HTTP-Cache): ganze gerenderte Seiten, pro Berechtigungskontext gespeichert und ausgeliefert, bevor der Kernel startet. [Rollenbewusster HTTP-Cache](bc/6.0/httpcache.md), [HTTP-Caching](bc/6.0/http-caching.md).
- **HTTP/2**: das Protokoll, das Velocity über TLS spricht. [HTTP/2 und Cache-Aufwärmen](bc/6.0/http2-und-cache-vorwaermen.md), [Spezifikation](spezifikationen/6.0/velocity-http2-und-sicherheit.md).

## I

- **INI file** (INI-Datei): eine Einstellungsdatei (`site.ini`, `content.ini`, ...) in Blöcken von `Key=Value`; gelesen in der Reihenfolge, eine spätere Datei gewinnt: Standards, Siteaccess, Extensions, Überschreibung. [Platzierungen von INI-Überschreibungen](spezifikationen/6.0/ini-override-platzierungen.md), [exp:ini](funktionen/6.0/exp-ini-befehl.md).
- **install type** (Installationstyp): ein benannter Installer der **Exponential Platform** (`exponential-oss`, `ibexa-oss`, `exponential-media`, `exponential-cjw`), ausgeführt mit `exponential:install <type>`. [SQLite-Installer der Plattform](spezifikationen/6.0/platform-sqlite-installer.md).
- **installation name** (Installationsname): der Name, der auf Seiten außerhalb der Produktion angezeigt wird, damit Staging nie für die Produktion gehalten wird. [Installationsname](funktionen/6.0/installationsname-in-seiten.md).
- **installer / install** (Installer): `./console exp:install --apply` installiert eine ganze Website mit einem Befehl. [Installation mit einem Befehl](funktionen/6.0/installation-mit-einem-befehl.md).
- **interface language** (Oberflächensprache): die Sprache, in der die Administration selbst angezeigt wird, pro Benutzer in der rechten Werkzeugleiste gewählt (Richtlinie `uiswitch/language`); sie setzt das Locale für die Anfragen dieses Benutzers und ändert nie die Inhaltssprachen. [Oberflächensprache](funktionen/6.0/ui-umschalter.md), [Einstellungen](anleitungen/ui-umschalter-einstellungen.md).

## J

- **job queue** (Auftragswarteschlange): siehe **expqueue**; für umfangreiche Inhaltsoperationen siehe **content job**.
- **jQuery 4**: die JavaScript-Bibliothek des Admin seit Oktober 2026; admin4 und admin4l laden sie nur auf den Seiten, die sie anfordern (23. Oktober 2026). [jQuery 4 und die Entfernung von YUI](funktionen/6.0/jquery4-und-entfernung-von-yui.md).

## K

- **kernel** (Kernel): der Kerncode in `kernel/` und `lib/`, den jede Installation ausführt. [Umbenennung in Exponential](funktionen/6.0/umbenennung-in-exponential.md).
- **Kickstarter**: der Installer, der die Fragen des Setup-Assistenten aus `kickstart.ini` beantwortet. [Kickstarter](funktionen/6.0/kickstarter-cli.md), [Referenz](bc/6.0/kickstartercli.md).

## L

- **layout (Exponential Layouts)** (Layout): eine Seitenstruktur aus **zones**, die **blocks** enthalten, ausgewählt durch **rules**. [Exponential Layouts](bc/6.0/layouts.md).
- **ledger** (Änderungsverzeichnis): die vollständige Aufzeichnung jedes Commits jedes Repositorys. [Änderungsverzeichnis](geschichte/aenderungsverzeichnis/README.md).
- **legacy bridge**: das Paket, das Exponential 6 innerhalb der Symfony-Plattform ausführt. [Legacy Bridge](funktionen/6.0/legacy-bridge.md), [Spezifikation](spezifikationen/6.0/legacy-bridge-bundle.md).
- **loading order** (Ladereihenfolge): die Reihenfolge, in der aktive **extensions** gelesen werden; die spätere gewinnt. [Ladereihenfolge der Extensions](funktionen/6.0/ladereihenfolge-der-extensions.md).
- **location** (Ort): ein anderes Wort für einen **node**. [Inhaltsmodell](anleitungen/inhaltsmodell-und-bearbeitung.md).
- **log viewer** (Log-Betrachter): **Setup > Logdateien** (`setup/logs`) und `./console exp:logs`: die Logdateien live verfolgt, samt ihrer Rotationen durchsucht, standardmäßig maskiert, über Anfrage- und Fehlerreferenz verknüpft. [Der Log-Betrachter](funktionen/6.0/log-betrachter.md).

## M

- **mail category** (E-Mail-Kategorie): die Art, zu der eine E-Mail gehört (`content`, `newsletter`, `security`, ...), deklariert mit `eZMail::setCategory()`; wesentliche Kategorien werden immer gesendet, optionale nur an Personen, die sie eingeschaltet haben. [Anleitung für Entwickler](anleitungen/mail-einstellungen-entwickler.md).
- **mail gate** (E-Mail-Schranke): die Prüfung in `eZMailTransport::send()`, die jede E-Mail durchläuft: Sie blockiert optionale E-Mails, die nicht angefordert wurden, und fügt die Fußzeile und die Header zum Abbestellen hinzu. [Spezifikation](spezifikationen/6.0/mail-einstellungen.md).
- **maintenance mode** (Wartungsmodus): nimmt die Website mit einem Hinweis vom Netz (`exp:maintenance on|off|status`). [Wartungsmodus](funktionen/6.0/wartungsmodus.md).
- **module** (Modul): ein Satz von **views** hinter einer URL wie `content/view`; Extensions können Module hinzufügen oder überschreiben. [Module in Extensions überschreiben](funktionen/6.0/modulueberschreibung-durch-extensions.md).
- **MongoDB**: eine unterstützte Datenbank. [MongoDB](funktionen/6.0/mongodb-datenbankunterstuetzung.md).
- **multi-node edit** (Bearbeitung mehrerer Knoten): mehrere Elemente in einem Formular bearbeiten. [Mehrere Elemente auf einmal bearbeiten](bc/6.0/mehrere-elemente-bearbeiten.md).
- **multisite package** (Multisite-Paket): das Website-Paket, das `exp:install` standardmäßig installiert (`sevenx_multisite`). [Installation mit einem Befehl](funktionen/6.0/installation-mit-einem-befehl.md).

## N

- **Nexus**: die Startprojekte der auf Symfony basierenden Plattform. [Platform-Nexus-Starter](funktionen/6.0/plattform-nexus-starter.md).
- **ngclasslist**: ein Datentyp, der eine Liste von Inhaltsklassen speichert. [ngclasslist](funktionen/6.0/erweiterungen/ngclasslist.md).
- **ngsymfonytools**: lässt Legacy-Templates Twig-Templates einbinden und Symfony-Code ausführen. [ngsymfonytools](funktionen/6.0/erweiterungen/ngsymfonytools.md).
- **node** (Knoten): ein Platz eines **content object** im **content tree**; hat eine ID, ein Elternelement, eine Sortierung und eine Sichtbarkeit. [Inhaltsmodell](anleitungen/inhaltsmodell-und-bearbeitung.md).
- **notification** (Benachrichtigung): eine E-Mail, die einem Benutzer mitteilt, dass gefolgte Inhalte sich geändert haben oder ein Kollaborationselement Aufmerksamkeit braucht; von einem Handler aus einem Ereignis erzeugt, wenn der Benachrichtigungs-Cronjob läuft. [Benachrichtigungen](funktionen/6.0/benachrichtigungen.md), [Anleitung für Administratoren](anleitungen/benachrichtigungen-administrator.md), [Spezifikation](spezifikationen/6.0/benachrichtigungen.md).
- **nxc_powercontent**: eine Extension, die erweitert, wie Code Inhalte erstellt, aktualisiert und entfernt. [nxc_powercontent](funktionen/6.0/erweiterungen/nxc_powercontent.md).

## O

- **one-click unsubscribe** (Abbestellen mit einem Klick): der Link am Ende optionaler E-Mails und die Header `List-Unsubscribe` und `List-Unsubscribe-Post` nach RFC 8058, mit denen ein E-Mail-Programm ohne Seite abbestellen kann. [E-Mail-Einstellungen](funktionen/6.0/e-mail-einstellungen.md).
- **OPcache**: der Cache für kompilierten Code von PHP; Velocity hält umgeschriebene Cache-Dateien darin. [Opcode-Cache und Profil](funktionen/6.0/velocity-opcode-cache-und-profil.md).
- **operator (template)** (Template-Operator): eine Funktion, die mit einer Pipe angewendet wird (`$text|wash`). [String-Template-Operatoren](funktionen/6.0/template-operatoren-fuer-zeichenketten.md), [Operatoren für Rollen und Richtlinien](funktionen/6.0/template-operatoren-fuer-rollen-und-richtlinien.md), [owsimpleoperator](funktionen/6.0/erweiterungen/owsimpleoperator.md), [swark](funktionen/6.0/erweiterungen/swark.md).
- **OPML**: eine Liste von Feeds, exportierbar aus dem RSS-Modul. [OPML-Exporte](bc/6.0/opml.md).
- **Oracle**: eine unterstützte Datenbank, über die Extension `ezoracle`; Installation mit `./console exp:install --apply --db=oracle`. [Datenbanktreiber und Installer](spezifikationen/6.0/datenbanktreiber-2026-09.md#oracle).
- **override** (Überschreibung): eine Regel, die sagt „für diese Klasse, diesen Knoten oder diese Sektion verwende dieses Template statt des Standards“, gespeichert in `override.ini`. [Template-Editor](funktionen/6.0/template-editor-ueberschreibungen.md), [Reihenfolge der Überschreibungen](funktionen/6.0/reihenfolge-der-template-ueberschreibungen.md).

## P

- **package** (Paket): ein Satz aus Inhalten, Klassen und Design, der exportiert und installiert werden kann, im klassischen XML- oder im JSON-Format, beim Hochladen geprüft und im Hintergrund oder als Entwürfe installierbar; auch ein Composer-Paket. [Pakete](anleitungen/pakete.md), [JSON-Paketformat](bc/6.0/package-json-format.md), [ezpm](funktionen/6.0/ezpm-paketmanager-cli.md), [Paketübersicht der Plattform](spezifikationen/6.0/platform-paketzuordnung.md).
- **page layout** (Seitenlayout, `pagelayout.tpl`): das äußere Template, das jede Seite umschließt. [Templates und Design](anleitungen/templates-und-design.md).
- **phar**: siehe **engine.phar**.
- **PHP-FPM**: der PHP-Prozessmanager, der die Website hinter **Apache** ausliefert. Laden Sie nach einer PHP-Änderung den neu, der Ihre Website ausliefert. [Deployment](anleitungen/bereitstellung.md).
- **placement (INI)** (Platzierung): der Name des Orts, von dem der Wert einer Einstellung stammt (`default`, `override`, `extension:<ext>`, `siteaccess`, ...). [Platzierungen von INI-Überschreibungen](spezifikationen/6.0/ini-override-platzierungen.md).
- **policy** (Richtlinie): eine Berechtigung (Modul, Funktion, Einschränkung) innerhalb einer **role**. [Rollen und Richtlinien in Reihenfolge](funktionen/6.0/reihenfolge-der-richtlinien-einer-rolle.md).
- **PostgreSQL**: eine unterstützte Datenbank. [Installation mit einem Befehl](funktionen/6.0/installation-mit-einem-befehl.md).
- **preload** (Vorladen): jede veröffentlichte Seite anfordern, damit kein Besucher für ein Rendern bezahlt (`exp:preload`, `exp:warm`, **Setup > Vorladen**). [Ansicht Vorladen](funktionen/6.0/ansicht-websites-vorladen.md), [Preloader](bc/6.0/preload.md).

## Q

- **Q settings** (Q-Einstellungen): die eigenen Einstellungen der Velocity-Engine, JSON unter dem Schlüssel `Q`; aus Exponential werden sie normalerweise über `settings/velocity.ini` gesetzt. [Velocity-Engine-Einstellungen](spezifikationen/6.0/velocity-engine-einstellungen.md).
- **Q shell** (Q-Shell): die ausklappbare Konsole im Velocity-Control-Panel. [Q-Shell](funktionen/6.0/velocity-q-shell.md).
- **qbix**: der Engine-Name von **Velocity** in den Einstellungen von Exponential und das Upstream-Projekt, auf dem er beruht. [Velocity-Engines](bc/6.0/velocity-engines.md).
- **query cache (SQL)** (SQL-Abfrage-Cache): beantwortet wiederholte `SELECT`-Abfragen, ohne die Datenbank zu fragen, bis ein Schreibvorgang sie veralten lässt. [SQL-Abfrage-Cache](bc/6.0/sql-abfrage-cache.md).

## R

- **RAD** (Rapid Application Development): die Erweiterungspunkte, über die Code Operatoren, Ansichten, Befehle und Datentypen hinzufügen kann, ohne den Kernel zu bearbeiten. [RAD-Werkzeuge](funktionen/6.0/rad-werkzeuge.md), [Erweiterungspunkte](bc/6.0/rad-erweiterungspunkte.md), [Erweiterungsfläche](bc/6.0/rad-erweiterungsoberflaeche.md).
- **reCAPTCHA**: ein Captcha-Datentyp. [recaptcha](funktionen/6.0/erweiterungen/recaptcha.md).
- **recipe** (Rezept): ein Symfony-Flex-Rezept, das ein Paket bei der Installation konfiguriert. [sevenx-recipes](funktionen/6.0/erweiterungen/sevenx-recipes.md).
- **Redis / Valkey**: Server, die den Cache der Inhaltsansicht und den INI-Cache halten können. [Redis- und Valkey-Caches](funktionen/6.0/erweiterungen/sevenx_valkey_cache.md).
- **registry (rad.ini)** (Registry): eine Liste, die eine Extension aus ihren eigenen Einstellungen oder ihrem Code liest (Event-Listener, Cronjob-Teile, Audit-Quellen, Editor-Engines), in `rad.ini` deklariert, damit **Setup > RAD** ihre Einträge zählt und prüft. [RAD-Erweiterungsfläche](bc/6.0/rad-erweiterungsoberflaeche.md).
- **repair page** (Reparaturseite): die Seite, die fehlende Bibliotheken erklärt und einen Administrator sie reparieren lässt. [Reparatur aus dem Browser](funktionen/6.0/reparatur-im-browser.md), [Reparatur](bc/6.0/reparatur.md).
- **request rule** (Anfrageregel): eine Regel in `requestrules.ini`, die eine Anfrage nach Modul, Ansicht und URL erlaubt oder ablehnt. [Anfrageregeln](funktionen/6.0/anfrageregeln.md), [content/view/full absichern](bc/6.0/sicherheit-von-content-view-full.md).
- **response cache (Velocity)** (Antwort-Cache): der Cache gerenderter Seiten des Servers. [Antwort-Cache](funktionen/6.0/velocity-antwort-cache.md).
- **role** (Rolle): ein benannter Satz von **policies**, der Benutzern und Gruppen zugewiesen wird. [Rollen und Richtlinien in Reihenfolge](funktionen/6.0/reihenfolge-der-richtlinien-einer-rolle.md); ihre Benutzer und Gruppen: [Seitenweise Anzeige der Rollenzuweisungen](funktionen/6.0/seitenweise-rollenzuweisungen.md).
- **RSS**: Feeds, die vom RSS-Modul exportiert und importiert werden; siehe auch **syndication**. [RSS, Podcast und Feedliste](funktionen/6.0/rss-podcast-und-feed-liste.md).
- **runnable**: die Klasse hinter einem Befehl, einem Cronjob-Teil oder einer Modulansicht (`Exponential\Runnable\*`); kann über `[RunnableSettings]` ersetzt oder belauscht werden. [Befehle, Cronjob-Teile und Ansichten als Klassen](spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md).

## S

- **section** (Sektion): eine Kennzeichnung an Inhalten, die ein Design und Berechtigungen auswählt. [Inhaltsmodell](anleitungen/inhaltsmodell-und-bearbeitung.md).
- **security defaults** (Sicherheitsvorgaben): die Einstellungen, die eine Standardinstallation ohne zusätzliche Arbeit sicher machen (Sicherheits-Header, Sitzungs-Cookie, Anmeldung, ausgelieferte Dateien, Caching). [Sicherheitsvorgaben](spezifikationen/6.0/sicherheitsvorgaben-2026-09.md), [Härtung von Datentypen und Eingaben](spezifikationen/6.0/datentyp-eingabehaertung.md).
- **seed data** (Startdaten): die Tabellen und Inhalte, mit denen eine neue Installation beginnt. [Installer-Logs und Startdaten](spezifikationen/6.0/installer-protokolle-und-startdaten.md).
- **setup wizard** (Setup-Assistent): der Installer im Browser. [Setup-Assistent](funktionen/6.0/einrichtungsassistent-und-editor-siteaccess.md).
- **sevenx**: das offizielle öffentliche PHP-Code-Präfix von 7x (`sevenx_dse`, `sevenx_valkey_cache`). „7x“ selbst kann kein Präfix sein, weil eine Klasse oder ein Bezeichner nicht mit einer Ziffer beginnen kann; Extensions und Klassen von 7x verwenden je nach Bedarf `sevenx` oder `exp`. **7x** ist das Unternehmen, das Exponential führt. [Namenspräfixe: sevenx und exp](standards/namenspraefixe-sevenx-und-exp.md).
- **shared layout** (gemeinsames Layout): ein **layout**, das zum Ausleihen da ist: Eine Zone eines anderen Layouts wird mit einer seiner Zonen verknüpft und rendert deren Blöcke, sodass ein Kopf- oder Fußbereich nur einmal gebaut wird. [Gemeinsame Layouts und verknüpfte Zonen](bc/6.0/layouts.md#gemeinsame-layouts-und-verknüpfte-zonen).
- **site package** (Website-Paket): der Satz aus Inhalten und Design, der beim Setup installiert wird. [Standards einer sauberen Installation](funktionen/6.0/standardwerte-einer-sauberen-installation.md).
- **siteaccess** (Siteaccess): ein Weg, die Website zu erreichen (per URL, Host oder Port), mit eigenen Einstellungen und eigenem Design, etwa `site` und `admin`. [Erste Schritte](anleitungen/erste-schritte.md), [INI-Überschreibungen für mehrere Websites](funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md).
- **Smaller changes** (Kleinere Änderungen): der Abschnitt einer Chronikseite, der Änderungen ohne eigene Geschichte auflistet. [Historie](geschichte/README.md).
- **Solr**: der Suchserver, gesteuert durch `exp:solr`. [Befehle für Webserver und Solr](funktionen/6.0/befehle-fuer-webserver-und-solr.md).
- **specification** (Spezifikation): eine Referenzseite für ein Teilsystem: Datenmodell, Klassen, Einstellungen, APIs. [Spezifikationen](spezifikationen/6.0/README.md).
- **SQLite**: die Datenbank in einer Datei; der Standard von `exp:install`. [SQLite](funktionen/6.0/sqlite-datenbank.md), [Treiberspezifikation](spezifikationen/6.0/sqlite3-datenbanktreiber.md).
- **state (object state)** (Objektstatus): eine Kennzeichnung aus einer Objektstatusgruppe, zum Beispiel der Sperrstatus „Not locked“, die Richtlinien prüfen können; nicht dasselbe wie der Status einer **version** (Entwurf, veröffentlicht). [Startdaten des Installers](spezifikationen/6.0/installer-protokolle-und-startdaten.md).
- **static cache** (statischer Cache): Seiten, die in Dateien geschrieben und ohne PHP ausgeliefert werden. [Generator für den statischen Cache](funktionen/6.0/generator-fuer-den-statischen-cache.md), [Standards](bc/6.0/standardwerte-des-statischen-caches.md).
- **suppression list** (Sperrliste): die Adressen, die keine optionalen E-Mails erhalten (Bounces, Beschwerden, Anfragen), nur als gesalzene Hashes gespeichert (`exp:mail:suppression`). [Anleitung für Administratoren](anleitungen/mail-einstellungen-administrator.md).
- **swark**: eine Extension mit vielen Template-Operatoren und Workflow-Ereignissen. [swark](funktionen/6.0/erweiterungen/swark.md).
- **syndication**: die Extension für Export- und Import-Feeds. [syndication](funktionen/6.0/erweiterungen/syndication.md), [Spezifikation](spezifikationen/6.0/syndikation.md).
- **system card** (Systemkarte): was `exp:expfetch`, `exp:info` und `exp:about` anzeigen, und der Name ihres Codes (`expSysCard*`, `syscard.ini`): Fakten von **providers**, Hinweise zum Zustand, Themes und Logos. [Die Systemkarte](funktionen/6.0/systemkarte.md).

## T

- **template** (Template): eine `.tpl`-Datei in der Template-Sprache von Exponential; aufgelöst über Design und **overrides**. [Kommentare mit Template-Pfaden](funktionen/6.0/template-pfad-kommentare.md).
- **template path comments** (Kommentare mit Template-Pfaden): Kommentare `START` und `STOP`, die jedes Template im Seitenquelltext benennen. [Kommentare mit Template-Pfaden](funktionen/6.0/template-pfad-kommentare.md).
- **test suite** (Testsuite): die PHPUnit-Suites aus `phpunit.xml`, ausgeführt mit `php vendor/bin/phpunit`. [Kontinuierliche Integration und die Testsuite](spezifikationen/6.0/kontinuierliche-integration.md).
- **TinyMCE**: die Editor-Engine hinter **ezoe**, in den Versionen 3 und 8. [TinyMCE 8](funktionen/6.0/online-editor-tinymce8.md).
- **translation** (Übersetzung): eine Sprachversion eines Inhaltsobjekts oder eine Sprachdatei der Oberfläche. [Übersetzungen und Sprachen](funktionen/6.0/uebersetzungen-und-sprachen.md).
- **trash** (Papierkorb): wo entfernte Objekte darauf warten, wiederhergestellt oder endgültig gelöscht zu werden. [Papierkorb](funktionen/6.0/papierkorb-wer-und-wo.md).
- **two-factor authentication** (Zwei-Faktor-Authentifizierung): ein zweiter Nachweis bei der Anmeldung, mit sozialem Login über OAuth. [sevenx_authentication_2fa](funktionen/6.0/erweiterungen/sevenx_authentication_2fa.md).

## U

- **UI switch** (UI-Umschalter): das Framework hinter dem Feld *Oberfläche* in der rechten Werkzeugleiste der Administration: die Auswahl eines angemeldeten Benutzers für die Oberfläche selbst (zuerst die **interface language**), pro Siteaccess in der Sitzung gehalten und früh in jeder Anfrage angewendet. [Oberflächensprache](funktionen/6.0/ui-umschalter.md), [Einstellungen](anleitungen/ui-umschalter-einstellungen.md).
- **UI switch provider** (UI-Switch-Provider): eine Klasse, die eine Auswahl des **UI switch** anbietet (`expUISwitchProviderInterface`, meist als Erweiterung von `expUISwitchProvider`), genannt in `uiswitch.ini [UISwitchSettings] ProviderList[]` und durch die Richtlinie `uiswitch/<identifier>` geschützt. [API für UI-Switch-Provider](bc/6.0/ui-umschalter.md).
- **URL alias** (URL-Alias): eine lesbare Adresse, die einem Knoten zugeordnet ist; `exp:updateniceurls` baut sie neu auf. [Inhaltsmodell](anleitungen/inhaltsmodell-und-bearbeitung.md).
- **uwebserver**: ein kleiner C-Webserver, der mit Velocity ausgeliefert wird, für statische Dateien und Benchmarks. [uwebserver](funktionen/6.0/velocity-uwebserver.md).

## V

- **Valkey**: siehe **Redis / Valkey**.
- **Vector**: der Arbeitsbereich für Threads von **Exponential Intelligence** (`expai/vector`): Modi für Umfang, Planen und Schreiben, gestufte Prüfung, parallele Läufe und Checkpoints. [expai](funktionen/6.0/erweiterungen/expai.md).
- **Velocity**: der mit Exponential gebündelte PHP-Anwendungsserver, ausgeführt mit `exp:velocity`, empfohlen für jede Stufe. [Velocity-Webserver](funktionen/6.0/velocity-webserver.md), [dauerhafte Worker](funktionen/6.0/velocity-server-mit-dauerhaften-workern.md), [Control Panel](funktionen/6.0/velocity-control-panel.md), [Engine-Einstellungen](spezifikationen/6.0/velocity-engine-einstellungen.md), [Chronik](geschichte/velocity/README.md).
- **version (content)** (Version): ein gespeicherter Zustand eines Inhaltsobjekts (Entwurf, veröffentlicht, archiviert). [Inhaltsmodell](anleitungen/inhaltsmodell-und-bearbeitung.md).
- **view** (Ansicht): eine Funktion eines **module**, erreicht über eine URL; auch die Art, wie ein Knoten gerendert wird (full, line, embed). [Modulansichten als Klassen](spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md), [Templates und Design](anleitungen/templates-und-design.md).

## W

- **warm-up** (Warm-up): die Vorbereitung des Velocity-Elternprozesses, bevor er seine Worker abspaltet; dasselbe Wort auch für **preload**. [Velocity-Worker-Pool](spezifikationen/6.0/velocity-worker-pool.md).
- **WebSocket**: eine langlebige Verbindung zwischen Browser und Server, von Velocity unterstützt. [WebSockets und Ereignisse](funktionen/6.0/velocity-websockets-und-ereignisse.md).
- **worker** (Worker): ein langlebiger PHP-Prozess von Velocity, der viele Anfragen beantwortet. [Velocity-Worker-Pool](spezifikationen/6.0/velocity-worker-pool.md).

## X

- **xrowextract**: eine Extension, die Inhalte nach Zeitplänen exportiert und paketiert. [xrowextract](funktionen/6.0/erweiterungen/xrowextract.md), [Spezifikation](spezifikationen/6.0/xrowextract.md).
- **xrowmetadata**: ein Metadaten-Attribut für Seitentitel und Schlüsselwörter. [xrowmetadata](funktionen/6.0/erweiterungen/xrowmetadata.md).

## Y

- **YUI**: die alte JavaScript-Bibliothek, entfernt im Oktober 2026. [Entfernung von YUI](bc/6.0/yui-entfernung.md).

## Z

- **zygote** (Zygote): ein Hilfsprozess von **Velocity**, der neue **workers** abspaltet, damit sie keine Besucherverbindungen erben. [Velocity-Worker-Pool](spezifikationen/6.0/velocity-worker-pool.md).

**Demo mode** (Demo-Modus) - Der Zustand einer Installation, deren Prozesse die Umgebungsvariable `EXP_DEMO_MODE=1` haben: eine öffentliche Demo. Administratorpasswörter können weder geändert noch zurückgesetzt werden, die Installer akzeptieren das veröffentlichte Demo-Passwort, und die Administration zeigt ein Banner. Nie auf einer echten Website. [Demo-Modus](bc/6.0/demomodus.md).

## Verwandte Seiten

- [Anleitungen: der Lernpfad](anleitungen/README.md)
- [Historie](geschichte/README.md) und [Abdeckung](geschichte/abdeckung.md)
- [Installation mit einem Befehl](funktionen/6.0/installation-mit-einem-befehl.md)
- [Spezifikationen](spezifikationen/6.0/README.md): die Referenzseiten
