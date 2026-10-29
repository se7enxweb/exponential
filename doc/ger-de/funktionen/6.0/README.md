# Funktionen von Exponential 6.0

Diese Seite führt jede Funktionsseite der Linie Exponential 6.0 auf, damit Sie die gesuchte finden, ohne suchen zu
müssen. Jede Seite sagt, für wen sie gedacht ist, was die Funktion tut und wie Sie sie verwenden. Die älteren
Erweiterungen, Themes und Pakete haben ein eigenes Verzeichnis:
[Erweiterungen, Themes und Pakete](erweiterungen/README.md). Jede Änderung steht mit ihrem
Datum im [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md).

Die Funktionsseiten selbst sind noch nicht alle ins Deutsche übersetzt; die Links führen bis dahin zu den englischen
Seiten.

## Hier beginnen: Velocity ist Teil von Exponential 6.0.15

Seit 6.0.15 ist der Velocity Internet / Web Application Server Teil von Exponential: Die `composer.json` im
Wurzelverzeichnis verlangt `se7enxweb/exponential-velocity ~0.0.4.55`, sodass jede Installation ihn hat, auf PHP 8.0
und neuer. Das markiert einen Wendepunkt, einen Grundstein und einen Schlussstein für das Projekt: eine neue Stufe der
Integration von Anwendung und System, mit noch mehr Funktionen von Haus aus und standardmäßig im Kernprodukt.
Exponential ist ein einziger funktionierender Stack (Server, Plattform für schnelle Anwendungsentwicklung, CMS,
Digital Experience und E-Commerce), nicht nur ein Framework. Lesen Sie zuerst
**[Velocity ist Teil von Exponential 6.0.15](../../hilfe/velocity-ist-teil-von-exponential-6-0-15.md)**.

Velocity ist die empfohlene Engine für jede Stufe, FrankenPHP ist die produktionsreife Alternative, und Apache oder
nginx mit PHP-FPM funktionieren weiter wie bisher.

## Exponential Velocity, der Internet- und Webanwendungsserver

- [Velocity-Webserver](velocity-webserver.md): der Überblick
- [Velocity: Exponential in einem Webserver mit persistenten Workern betreiben](velocity-server-mit-dauerhaften-workern.md)
- [Der Antwort-Cache von Velocity](velocity-antwort-cache.md)
- [HTTPS und Zertifikate in Velocity](velocity-https-zertifikate.md)
- [Statische Dateien und Bildverarbeitung in Velocity](velocity-statische-dateien-und-bilder.md)
- [WebSockets, Server-Sent Events und Räume in Velocity](velocity-websockets-und-ereignisse.md)
- [Bedienfeld und Dashboard von Velocity](velocity-control-panel.md)
- [Die Q-Shell von Velocity](velocity-q-shell.md)
- [Der Zeitplaner von Velocity](velocity-zeitplaner.md)
- [Erkennung, Föderation und Deployment in Velocity](velocity-erkennung-foederation-und-deployment.md)
- [Pakete, Docker-Images und Binärdateien von Velocity](velocity-pakete-und-binaerdateien.md)
- [Velocity und der Opcode-Cache: warum eine Seite kostet, was sie kostet, und wie Sie es messen](velocity-opcode-cache-und-profil.md)
- [uwebserver](velocity-uwebserver.md)
- [Befehle zur Serversteuerung: exp:webserver, exp:frankenphp und exp:solr](befehle-fuer-webserver-und-solr.md)

## Installieren, bereitstellen und betreiben

- [Exponential mit einem einzigen Befehl installieren](installation-mit-einem-befehl.md)
- [Kickstarter: eine ganze Site aus einer Datei installieren](kickstarter-cli.md)
- [Was eine neue Installation mitbringt: die Standardauswahl an Erweiterungen](standard-extension-auslieferung.md)
- [Eine saubere Installation, die zu Shared Hosting passt und Exponential heißt](standardwerte-einer-sauberen-installation.md)
- [Das neue Aussehen des Einrichtungsassistenten und der Siteaccess für den Editor](einrichtungsassistent-und-editor-siteaccess.md)
- [Einstellungen pro Umgebung (EXP_ENV)](umgebungseinstellungen.md)
- [Vom Deployment verwaltete Konfiguration (ManagedByDeployment)](durch-deployment-verwaltet.md)
- [Installationsname: Staging nie wieder mit Produktion verwechseln](installationsname-in-seiten.md)
- [Einstellungen auf der Kommandozeile ändern: exp:ini](exp-ini-befehl.md)
- [Die Systemkarte: exp:expfetch, exp:info und exp:about](systemkarte.md)
- [Benchmark: `exp:benchmark`](benchmark.md)
- [Wartungsmodus](wartungsmodus.md)
- [Eine defekte Installation im Browser reparieren](reparatur-im-browser.md)
- [Prüfung der Dateikonsistenz und die Dateiliste des Release](dateikonsistenzpruefung.md)
- [Qualitätsprüfungen: Git-Hooks, Dateimanifest, Codestil und PHPStan](qualitaetspruefungen.md)
- [Cache leeren, indem Verzeichnisse beiseitegeschoben werden](cache-leeren-durch-umbenennen.md)
- [Inhalts- und INI-Cache in Redis oder Valkey halten](valkey-cache-hooks.md)
- [Generator für den statischen Cache](generator-fuer-den-statischen-cache.md)
- [Cronjob-Konsole](cronjobs-in-der-konsole.md), [Cronjob-Teile pausieren](cronjobs-pausieren.md),
  [Einen Cronjob-Teil auslassen](filter-fuer-cronjob-teile.md)
- [Rotation und Bereinigung der Logs](log-rotation.md), [Der Log-Viewer](log-betrachter.md),
  [Anfrage-Logs: 404.log und security.log](anfrageprotokolle.md)
- [Einstellungen pro Site in Erweiterungen (Hosting mehrerer Sites)](ini-ueberschreibungen-fuer-mehrere-websites.md)
- [Logs, INI-Cache und Ablaufzeitstempel pro Site (Hosting mehrerer Sites)](log-und-cache-pfade-fuer-mehrere-websites.md)

## Datenbanken

- [Unterstützung für die Datenbank SQLite](sqlite-datenbank.md)
- [MongoDB als Datenbank](mongodb-datenbankunterstuetzung.md)
- [AdminNeo: die Datenbankverwaltung hinter dem Database Source Editor](adminneo-datenbankverwaltung.md)
- [Datentabellen: die Tabellen der Module als Teil des Systems](datentabellen.md): der Katalog der persistenten
  Klassen, benannte Datenbankverbindungen, Klassen auf ihrer eigenen Verbindung

## Schnelle Anwendungsentwicklung und Erweiterungen

- [RAD-Werkzeuge: Assistenten für Erweiterungen, Katalog, Bestandsaufnahme und Zustandsprüfung](rad-werkzeuge.md)
- [Erweiterungen in mehr als einem Ordner](zusaetzliche-extension-verzeichnisse.md)
- [Setup > Extensions: Ladereihenfolge und sicheres Speichern](ladereihenfolge-der-extensions.md)
- [Liste der Erweiterungen: jede Erweiterung sortieren, untersuchen und herunterladen](extension-liste-und-downloads.md)
- [Eine Erweiterung kann ein Modul des Kernels ersetzen](modulueberschreibung-durch-extensions.md)
- [Filter für Zugriff und View-Cache in Erweiterungen](zugriffs-und-cache-filter.md)
- [Einschränkungen von Inhaltsrichtlinien durch Erweiterungen](handler-fuer-inhaltseinschraenkungen.md)
- [Icon-Themes, die in Ihrer Erweiterung liegen](icon-themes-in-extensions.md)
- [Entfernte Dienste: die ganze Administration als HTTP-API (expservices)](entfernte-dienste-expservices.md)
- [Anfrageregeln: entscheiden, was mit einer Anfrage geschieht, bevor ihr View läuft](anfrageregeln.md)
- [Prüfungen von Rollen und Richtlinien in Templates](template-operatoren-fuer-rollen-und-richtlinien.md)
- [String-Funktionen von PHP als Template-Operatoren](template-operatoren-fuer-zeichenketten.md)
- [Template-Editor: Overrides anlegen, ordnen und bearbeiten, ohne INI-Dateien anzufassen](template-editor-ueberschreibungen.md)
- [Design > Templates: Reihenfolge der Overrides per Drag and Drop](reihenfolge-der-template-ueberschreibungen.md)
- [Kommentare mit Template-Pfaden: sehen, welches Template welches Markup geschrieben hat](template-pfad-kommentare.md)
- [Debug-Ausgabe, die Sie auf einen Blick lesen und gestalten können](verbesserte-debug-ausgabe.md)
- [Die Exp-Debug-Leiste](exp-debug-leiste.md)

## Pakete

- [ezpm: der Paketmanager auf der Kommandozeile](ezpm-paketmanager-cli.md)
- [Der Paketassistent](paketassistent.md)
- [Ein Paketinstaller, der große Pakete übersteht](paketinstallation-in-teilschritten.md)
- [In ein Paket hineinsehen, es mit Ihrer Site vergleichen, einzelne Elemente importieren](pakete-vergleichen-und-importieren.md)
- [Paketlizenzen und Semantic Versioning](paketlizenzen-und-versionen.md)
- [Die Seiten „Über“ und „Copyright“](info-und-paketseiten.md)

## Content-Management und die Administration

- [Das Design admin4](admin4-design.md)
- [Das Design admin4l: die Admin-Seite, aus Layouts aufgebaut](admin4l.md)
- [Das responsive Admin-Design (admin3)](admin3-responsives-admin.md)
- [Breite und Schriftgröße der linken Seitenleiste](admin3-seitenleistenbreite-und-schriftgroesse.md)
- [Exponential Admin UI: das Design und der Siteaccess adminui](exp-adminui.md)
- [Das Design der Administration für Ihre Sitzung wählen](ui-umschalter-design.md)
- [Navigation in der Administration: der oberste Knoten, die Registerkarten und das Durchsuchen von Inhalten](oberster-knoten-des-inhaltsbaums-im-admin.md)
- [Admin-Links folgen den Berechtigungen](admin-links-folgen-den-berechtigungen.md)
- [Registerkarten der Administration ausblenden: HiddenTabs](ausgeblendete-admin-reiter.md)
- [Blättern, Sortieren und Seitengrößen in der Administrationsoberfläche](seitenweise-listen-im-admin.md)
- [Eigene Anzahl von Einträgen pro Seite](eigene-anzahl-eintraege-pro-seite.md)
- [Sites vorladen (Administrationsoberfläche)](ansicht-websites-vorladen.md)
- [Schaltflächen für die schnelle Platzierung](schaltflaechen-fuer-schnelle-platzierung.md)
- [Lesezeichenordner](lesezeichenordner.md)
- [Lesezeichen für Systemseiten](lesezeichen-fuer-systemseiten.md)
- [Der Content Finder](inhalts-finder.md)
- [Inhaltsaufträge: große Entfernen-, Kopier- und Verschiebevorgänge im Hintergrund](inhaltsauftraege.md)
- [Die Inhaltsprüfungen: nicht referenzierte Objekte, defekte Beziehungen und fehlende Dateien](inhaltspruefungen.md)
- [Inhaltsklassen: ein Attribut nach ganz oben oder ganz unten verschieben](klassenattribute-an-den-rand-verschieben.md)
- [Die Liste der Unterelemente: 129 Spalten, Voreinstellungen und CSV-Export](optionen-der-unterelementtabelle.md)
- [Ausgewählte Unterelemente kopieren](unterelemente-auswahl-kopieren.md)
- [Entfernen und Verbergen: die Prüfung der umgekehrten Beziehungen](pruefung-umgekehrter-beziehungen.md)
- [Der Papierkorb zeigt, wer ein Objekt gelöscht hat und wo es war](papierkorb-wer-und-wo.md)
- [Versionsvorschau in anderen Ansichtsmodi](ansichtsmodi-der-versionsvorschau.md)
- [Veröffentlichen ohne Benachrichtigung](veroeffentlichen-ohne-benachrichtigung.md)
- [Der Online-Editor auf TinyMCE 8 (optional, neben TinyMCE 3)](online-editor-tinymce8.md)
- [Rich Text: Zeichen oberhalb von U+FFFF und TeX-Quelltext in benutzerdefinierten Tags](rich-text-zusatzzeichen-und-tex-quelltext.md)
- [PDF-Export](pdf-export.md)
- [RSS: Feeds für Apple Podcasts, eine seitenweise Feedliste und sicherere Exporte](rss-podcast-und-feed-liste.md)
- [jQuery 4 in der Administration, und YUI ist fort](jquery4-und-entfernung-von-yui.md)
- [Das Hilfesystem: die Dokumentation in der Administration](hilfesystem.md)
- [Hilfe, als Inhalt gepflegt](hilfeinhalte.md)

## Sites, Sprachen und Digital Experience

- [Links zu Inhalten einer anderen Site einer Multisite-Installation](websiteuebergreifende-links-im-multisite.md)
- [Siteaccess nach Host-Präfix und die Weiterleitung zur Sprache](siteaccess-zuordnung-nach-host-und-sprache.md)
- [Übersetzungen und Sprachen](uebersetzungen-und-sprachen.md)
- [Sprachen mit Schreibrichtung von rechts nach links in der Administration](rechts-nach-links.md)
- [Sprache der Oberfläche und der UI-Umschalter](ui-umschalter.md)
- [Die Liste der Oberflächensprachen: jede Sprache, die meistgebrauchten zuerst](ui-umschalter-sprachen.md)
- [Die gruppierte Länderliste](laendergruppen.md)
- [Eine brauchbare robots.txt von Haus aus](robots-txt.md)
- [Das Produkt heißt Exponential](umbenennung-in-exponential.md)

## E-Commerce

- [Shop-Dashboard und Bestellstatus](shop-dashboard.md)
- [Dauerhafte Bestellbelege](bestellbelege.md)
- [Die Bestellliste nach jeder Spalte sortieren](sortierung-der-bestellliste.md)
- [Aus welchem Siteaccess eine Bestellung kam](bestellungen-nach-siteaccess.md)

## Benutzer, Berechtigungen und Sicherheit

- [Das Prüfprotokoll: wer was getan hat, und der Nachweis, dass niemand den Eintrag geändert hat](pruefprotokoll.md)
- [Der Geheimnisspeicher: Passwörter und Schlüssel der Werkzeuge, verschlüsselt, nach Namen](geheimnisspeicher.md)
- [Content Security Policy](content-security-policy.md)
- [Sichere Weiterleitungen und „zurück, woher Sie gekommen sind“](sichere-weiterleitungen.md)
- [„Dieses Formular ist abgelaufen“: Ablehnungen durch das Formular-Token](seite-abgelaufenes-formular.md)
- [SOAP-Aufrufe über HTTPS: Zertifikate einer internen Zertifizierungsstelle](soap-client-tls.md)
- [Ihr Passwort ändern](moderne-passwortaenderung.md)
- [Passwort bei der nächsten Anmeldung ändern](passwort-bei-naechster-anmeldung-aendern.md)
- [Zweiter Schritt auf den anderen Anmeldewegen: REST, WebDAV, SOAP, expservices; App-Passwörter](zweiter-schritt-auf-anderen-anmeldewegen.md)
- [Das Passwort eines Benutzers auf der Kommandozeile zurücksetzen](benutzerpasswort-zuruecksetzen.md)
- [Benutzerkonten prüfen](benutzerpflege.md)
- [Benutzer-Auditprotokoll: wer was an einem Konto geändert hat, Anmeldungen und Ablehnungen](benutzer-auditprotokoll.md)
- [Ihre Geräte: angemeldete Sitzungen je Benutzer, endgültig abgemeldet, eine E-Mail über ein neues Gerät](benutzersitzungen.md)
- [Signierte Links in der E-Mail „Neuer Benutzer registriert“](admin-links-der-registrierung.md)
- [Administratoren geben neue Redakteurskonten frei](freigabe-von-redakteurskonten.md)
- [Rollen: IDs der Richtlinien, Sortierung und Schaltflächen für die Reihenfolge](reihenfolge-der-richtlinien-einer-rolle.md)
- [Die Benutzer und Gruppen einer Rolle, seitenweise](seitenweise-rollenzuweisungen.md)
- [Berechtigungsprüfungen für Benutzer mit vielen Rollenzuweisungen](berechtigungspruefung-bei-vielen-zuweisungen.md)
- [Schnellere Zugriffsarrays für Benutzer mit vielen Rollenzuweisungen](vorabladen-des-zugriffsarrays.md)
- [Benachrichtigungen: die Anleitung für Benutzer](benachrichtigungen.md)
- [E-Mail-Einstellungen: die Anleitung für Benutzer](e-mail-einstellungen.md)

## Die Linie Exponential Platform

- [Das Skelett von Exponential Platform DXP: create-project und eine dokumentierte Installation](plattform-dxp-skeleton.md)
- [Exponential Platform Nexus: eine fertige Site in Minuten](plattform-nexus-starter.md)
- [Die Administrationsoberfläche von Exponential Platform](plattform-admin-ui-fork.md)
- [Layouts auf der Plattform: der Fork von layouts-core](plattform-layouts-core-fork.md)
- [Site-Bundles, Metadaten und die Twig-Werkzeuge der Plattform](plattform-site-bundles.md)
- [SQLite für Exponential Platform: kein Datenbankserver nötig](plattform-sqlite-installation.md)
- [PHP 8.4 und 8.5 für den älteren Symfony-Stack: die Framework-Forks](plattform-php85-framework-forks.md)
- [Legacy Bridge: Exponential 6 innerhalb der Symfony-Plattform betreiben](legacy-bridge.md)
