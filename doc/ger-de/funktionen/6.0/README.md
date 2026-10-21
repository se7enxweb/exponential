# Funktionen von Exponential 6.0

Diese Seite führt jede Funktionsseite der Linie Exponential 6.0 auf, damit Sie die gesuchte finden, ohne suchen zu
müssen. Jede Seite sagt, für wen sie gedacht ist, was die Funktion tut und wie Sie sie verwenden. Die älteren
Erweiterungen, Themes und Pakete haben ein eigenes Verzeichnis:
[Erweiterungen, Themes und Pakete](../../../eng-us/features/6.0/extensions/README.md). Jede Änderung steht mit ihrem
Datum im [Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md).

Die Funktionsseiten selbst sind noch nicht alle ins Deutsche übersetzt; die Links führen bis dahin zu den englischen
Seiten.

## Hier beginnen: Velocity ist Teil von Exponential 6.0.15

Seit 6.0.15 ist der Velocity Internet / Web Application Server Teil von Exponential: Die `composer.json` im
Wurzelverzeichnis verlangt `se7enxweb/exponential-velocity ~0.0.4.54`, sodass jede Installation ihn hat, auf PHP 8.0
und neuer. Das markiert einen Wendepunkt, einen Grundstein und einen Schlussstein für das Projekt: eine neue Stufe der
Integration von Anwendung und System, mit noch mehr Funktionen von Haus aus und standardmäßig im Kernprodukt.
Exponential ist ein einziger funktionierender Stack (Server, Plattform für schnelle Anwendungsentwicklung, CMS,
Digital Experience und E-Commerce), nicht nur ein Framework. Lesen Sie zuerst
**[Velocity ist Teil von Exponential 6.0.15](../../hilfe/velocity-ist-teil-von-exponential-6-0-15.md)**.

Velocity ist die empfohlene Engine für jede Stufe, FrankenPHP ist die produktionsreife Alternative, und Apache oder
nginx mit PHP-FPM funktionieren weiter wie bisher.

## Exponential Velocity, der Internet- und Webanwendungsserver

- [Velocity-Webserver](../../../eng-us/features/6.0/velocity-web-server.md): der Überblick
- [Velocity: Exponential in einem Webserver mit persistenten Workern betreiben](../../../eng-us/features/6.0/velocity-persistent-worker-server.md)
- [Der Antwort-Cache von Velocity](../../../eng-us/features/6.0/velocity-response-cache.md)
- [HTTPS und Zertifikate in Velocity](../../../eng-us/features/6.0/velocity-https-certificates.md)
- [Statische Dateien und Bildverarbeitung in Velocity](../../../eng-us/features/6.0/velocity-static-files-and-images.md)
- [WebSockets, Server-Sent Events und Räume in Velocity](../../../eng-us/features/6.0/velocity-websockets-and-events.md)
- [Bedienfeld und Dashboard von Velocity](../../../eng-us/features/6.0/velocity-control-panel.md)
- [Die Q-Shell von Velocity](../../../eng-us/features/6.0/velocity-q-shell.md)
- [Der Zeitplaner von Velocity](../../../eng-us/features/6.0/velocity-scheduler.md)
- [Erkennung, Föderation und Deployment in Velocity](../../../eng-us/features/6.0/velocity-discovery-federation-and-deploy.md)
- [Pakete, Docker-Images und Binärdateien von Velocity](../../../eng-us/features/6.0/velocity-packages-and-binaries.md)
- [Velocity und der Opcode-Cache: warum eine Seite kostet, was sie kostet, und wie Sie es messen](../../../eng-us/features/6.0/velocity-opcode-cache-and-profile.md)
- [uwebserver](../../../eng-us/features/6.0/velocity-uwebserver.md)
- [Befehle zur Serversteuerung: exp:webserver, exp:frankenphp und exp:solr](../../../eng-us/features/6.0/web-server-and-solr-commands.md)

## Installieren, bereitstellen und betreiben

- [Exponential mit einem einzigen Befehl installieren](../../../eng-us/features/6.0/install-in-one-command.md)
- [Kickstarter: eine ganze Site aus einer Datei installieren](../../../eng-us/features/6.0/kickstarter-cli.md)
- [Was eine neue Installation mitbringt: die Standardauswahl an Erweiterungen](../../../eng-us/features/6.0/default-extension-distribution.md)
- [Eine saubere Installation, die zu Shared Hosting passt und Exponential heißt](../../../eng-us/features/6.0/clean-install-defaults.md)
- [Das neue Aussehen des Einrichtungsassistenten und der Siteaccess für den Editor](../../../eng-us/features/6.0/setup-wizard-and-editor-siteaccess.md)
- [Einstellungen pro Umgebung (EXP_ENV)](../../../eng-us/features/6.0/environment-settings.md)
- [Vom Deployment verwaltete Konfiguration (ManagedByDeployment)](../../../eng-us/features/6.0/managed-by-deployment.md)
- [Installationsname: Staging nie wieder mit Produktion verwechseln](../../../eng-us/features/6.0/installation-name-in-pages.md)
- [Einstellungen auf der Kommandozeile ändern: exp:ini](../../../eng-us/features/6.0/exp-ini-command.md)
- [Die Systemkarte: exp:expfetch, exp:info und exp:about](../../../eng-us/features/6.0/system-card.md)
- [Benchmark: `exp:benchmark`](../../../eng-us/features/6.0/benchmark.md)
- [Wartungsmodus](../../../eng-us/features/6.0/maintenance-mode.md)
- [Eine defekte Installation im Browser reparieren](../../../eng-us/features/6.0/repair-from-the-browser.md)
- [Prüfung der Dateikonsistenz und die Dateiliste des Release](../../../eng-us/features/6.0/file-consistency-check.md)
- [Qualitätsprüfungen: Git-Hooks, Dateimanifest, Codestil und PHPStan](../../../eng-us/features/6.0/quality-checks.md)
- [Cache leeren, indem Verzeichnisse beiseitegeschoben werden](../../../eng-us/features/6.0/cache-clear-rename-aside.md)
- [Inhalts- und INI-Cache in Redis oder Valkey halten](../../../eng-us/features/6.0/valkey-cache-hooks.md)
- [Generator für den statischen Cache](../../../eng-us/features/6.0/static-cache-generator.md)
- [Cronjob-Konsole](../../../eng-us/features/6.0/cronjobs-console.md), [Cronjob-Teile pausieren](../../../eng-us/features/6.0/cronjob-pause.md),
  [Einen Cronjob-Teil auslassen](../../../eng-us/features/6.0/cronjob-part-filter.md)
- [Rotation und Bereinigung der Logs](../../../eng-us/features/6.0/log-rotation.md), [Der Log-Viewer](../../../eng-us/features/6.0/log-viewer.md),
  [Anfrage-Logs: 404.log und security.log](../../../eng-us/features/6.0/request-logs.md)
- [Einstellungen pro Site in Erweiterungen (Hosting mehrerer Sites)](../../../eng-us/features/6.0/multi-site-ini-overrides.md)
- [Logs, INI-Cache und Ablaufzeitstempel pro Site (Hosting mehrerer Sites)](../../../eng-us/features/6.0/multi-site-log-and-cache-paths.md)

## Datenbanken

- [Unterstützung für die Datenbank SQLite](../../../eng-us/features/6.0/sqlite-database.md)
- [MongoDB als Datenbank](../../../eng-us/features/6.0/mongodb-database-support.md)
- [AdminNeo: die Datenbankverwaltung hinter dem Database Source Editor](../../../eng-us/features/6.0/adminneo-database-manager.md)

## Schnelle Anwendungsentwicklung und Erweiterungen

- [RAD-Werkzeuge: Assistenten für Erweiterungen, Katalog, Bestandsaufnahme und Zustandsprüfung](../../../eng-us/features/6.0/rad-tools.md)
- [Erweiterungen in mehr als einem Ordner](../../../eng-us/features/6.0/additional-extension-directories.md)
- [Setup > Extensions: Ladereihenfolge und sicheres Speichern](../../../eng-us/features/6.0/extension-loading-order.md)
- [Liste der Erweiterungen: jede Erweiterung sortieren, untersuchen und herunterladen](../../../eng-us/features/6.0/extension-list-and-downloads.md)
- [Eine Erweiterung kann ein Modul des Kernels ersetzen](../../../eng-us/features/6.0/extension-module-override.md)
- [Filter für Zugriff und View-Cache in Erweiterungen](../../../eng-us/features/6.0/access-and-cache-filters.md)
- [Einschränkungen von Inhaltsrichtlinien durch Erweiterungen](../../../eng-us/features/6.0/content-limitation-handlers.md)
- [Icon-Themes, die in Ihrer Erweiterung liegen](../../../eng-us/features/6.0/icon-themes-in-extensions.md)
- [Entfernte Dienste: die ganze Administration als HTTP-API (expservices)](../../../eng-us/features/6.0/remote-services-expservices.md)
- [Anfrageregeln: entscheiden, was mit einer Anfrage geschieht, bevor ihr View läuft](../../../eng-us/features/6.0/request-rules.md)
- [Prüfungen von Rollen und Richtlinien in Templates](../../../eng-us/features/6.0/role-and-policy-template-operators.md)
- [String-Funktionen von PHP als Template-Operatoren](../../../eng-us/features/6.0/string-template-operators.md)
- [Template-Editor: Overrides anlegen, ordnen und bearbeiten, ohne INI-Dateien anzufassen](../../../eng-us/features/6.0/template-editor-overrides.md)
- [Design > Templates: Reihenfolge der Overrides per Drag and Drop](../../../eng-us/features/6.0/template-override-ordering.md)
- [Kommentare mit Template-Pfaden: sehen, welches Template welches Markup geschrieben hat](../../../eng-us/features/6.0/template-path-comments.md)
- [Debug-Ausgabe, die Sie auf einen Blick lesen und gestalten können](../../../eng-us/features/6.0/debug-output-improvements.md)
- [Die Exp-Debug-Leiste](../../../eng-us/features/6.0/exp-debug-bar.md)

## Pakete

- [ezpm: der Paketmanager auf der Kommandozeile](../../../eng-us/features/6.0/ezpm-package-manager-cli.md)
- [Der Paketassistent](../../../eng-us/features/6.0/package-wizard.md)
- [Ein Paketinstaller, der große Pakete übersteht](../../../eng-us/features/6.0/package-installer-batching.md)
- [In ein Paket hineinsehen, es mit Ihrer Site vergleichen, einzelne Elemente importieren](../../../eng-us/features/6.0/package-compare-and-import.md)
- [Paketlizenzen und Semantic Versioning](../../../eng-us/features/6.0/package-licenses-and-versions.md)
- [Die Seiten „Über“ und „Copyright“](../../../eng-us/features/6.0/about-and-package-pages.md)

## Content-Management und die Administration

- [Das Design admin4](../../../eng-us/features/6.0/admin4-design.md)
- [Das Design admin4l: die Admin-Seite, aus Layouts aufgebaut](../../../eng-us/features/6.0/admin4l.md)
- [Das responsive Admin-Design (admin3)](../../../eng-us/features/6.0/admin3-responsive-admin.md)
- [Breite und Schriftgröße der linken Seitenleiste](../../../eng-us/features/6.0/admin3-sidebar-width-and-font-size.md)
- [Exponential Admin UI: das Design und der Siteaccess adminui](../../../eng-us/features/6.0/exp-adminui.md)
- [Das Design der Administration für Ihre Sitzung wählen](../../../eng-us/features/6.0/ui-switch-design.md)
- [Navigation in der Administration: der oberste Knoten, die Registerkarten und das Durchsuchen von Inhalten](../../../eng-us/features/6.0/admin-content-tree-top-node.md)
- [Admin-Links folgen den Berechtigungen](../../../eng-us/features/6.0/admin-links-follow-permissions.md)
- [Registerkarten der Administration ausblenden: HiddenTabs](../../../eng-us/features/6.0/hidden-admin-tabs.md)
- [Blättern, Sortieren und Seitengrößen in der Administrationsoberfläche](../../../eng-us/features/6.0/admin-list-paging.md)
- [Eigene Anzahl von Einträgen pro Seite](../../../eng-us/features/6.0/custom-items-per-page.md)
- [Sites vorladen (Administrationsoberfläche)](../../../eng-us/features/6.0/preload-sites-view.md)
- [Schaltflächen für die schnelle Platzierung](../../../eng-us/features/6.0/quick-location-buttons.md)
- [Lesezeichenordner](../../../eng-us/features/6.0/bookmark-folders.md)
- [Der Content Finder](../../../eng-us/features/6.0/content-finder.md)
- [Inhaltsaufträge: große Entfernen-, Kopier- und Verschiebevorgänge im Hintergrund](../../../eng-us/features/6.0/content-jobs.md)
- [Die Inhaltsprüfungen: nicht referenzierte Objekte, defekte Beziehungen und fehlende Dateien](../../../eng-us/features/6.0/content-checks.md)
- [Inhaltsklassen: ein Attribut nach ganz oben oder ganz unten verschieben](../../../eng-us/features/6.0/class-attribute-move-to-edge.md)
- [Die Liste der Unterelemente: 129 Spalten, Voreinstellungen und CSV-Export](../../../eng-us/features/6.0/subitems-table-options.md)
- [Ausgewählte Unterelemente kopieren](../../../eng-us/features/6.0/subitems-copy-selected.md)
- [Entfernen und Verbergen: die Prüfung der umgekehrten Beziehungen](../../../eng-us/features/6.0/reverse-relation-check.md)
- [Der Papierkorb zeigt, wer ein Objekt gelöscht hat und wo es war](../../../eng-us/features/6.0/trash-who-and-where.md)
- [Versionsvorschau in anderen Ansichtsmodi](../../../eng-us/features/6.0/version-preview-view-modes.md)
- [Veröffentlichen ohne Benachrichtigung](../../../eng-us/features/6.0/publish-without-notification.md)
- [Der Online-Editor auf TinyMCE 8 (optional, neben TinyMCE 3)](../../../eng-us/features/6.0/online-editor-tinymce8.md)
- [Rich Text: Zeichen oberhalb von U+FFFF und TeX-Quelltext in benutzerdefinierten Tags](../../../eng-us/features/6.0/rich-text-supplementary-characters-and-tex-source.md)
- [PDF-Export](../../../eng-us/features/6.0/pdf-export.md)
- [RSS: Feeds für Apple Podcasts, eine seitenweise Feedliste und sicherere Exporte](../../../eng-us/features/6.0/rss-podcast-and-feed-list.md)
- [jQuery 4 in der Administration, und YUI ist fort](../../../eng-us/features/6.0/jquery4-and-yui-removal.md)
- [Das Hilfesystem: die Dokumentation in der Administration](../../../eng-us/features/6.0/help-system.md)
- [Hilfe, als Inhalt gepflegt](../../../eng-us/features/6.0/help-content.md)

## Sites, Sprachen und Digital Experience

- [Links zu Inhalten einer anderen Site einer Multisite-Installation](../../../eng-us/features/6.0/multisite-cross-site-links.md)
- [Siteaccess nach Host-Präfix und die Weiterleitung zur Sprache](../../../eng-us/features/6.0/siteaccess-host-and-language-matching.md)
- [Übersetzungen und Sprachen](../../../eng-us/features/6.0/translations-and-languages.md)
- [Sprachen mit Schreibrichtung von rechts nach links in der Administration](../../../eng-us/features/6.0/right-to-left.md)
- [Sprache der Oberfläche und der UI-Umschalter](../../../eng-us/features/6.0/ui-switch.md)
- [Die Liste der Oberflächensprachen: jede Sprache, die meistgebrauchten zuerst](../../../eng-us/features/6.0/ui-switch-languages.md)
- [Die gruppierte Länderliste](../../../eng-us/features/6.0/country-groups.md)
- [Eine brauchbare robots.txt von Haus aus](../../../eng-us/features/6.0/robots-txt.md)
- [Das Produkt heißt Exponential](../../../eng-us/features/6.0/rebranding-to-exponential.md)

## E-Commerce

- [Shop-Dashboard und Bestellstatus](../../../eng-us/features/6.0/store-dashboard.md)
- [Dauerhafte Bestellbelege](../../../eng-us/features/6.0/order-receipts.md)
- [Die Bestellliste nach jeder Spalte sortieren](../../../eng-us/features/6.0/order-list-sorting.md)

## Benutzer, Berechtigungen und Sicherheit

- [Das Prüfprotokoll: wer was getan hat, und der Nachweis, dass niemand den Eintrag geändert hat](../../../eng-us/features/6.0/audit-trail.md)
- [Content Security Policy](../../../eng-us/features/6.0/content-security-policy.md)
- [Sichere Weiterleitungen und „zurück, woher Sie gekommen sind“](../../../eng-us/features/6.0/safe-redirects.md)
- [„Dieses Formular ist abgelaufen“: Ablehnungen durch das Formular-Token](../../../eng-us/features/6.0/form-expired-page.md)
- [SOAP-Aufrufe über HTTPS: Zertifikate einer internen Zertifizierungsstelle](../../../eng-us/features/6.0/soap-client-tls.md)
- [Ihr Passwort ändern](../../../eng-us/features/6.0/modern-password-change.md)
- [Das Passwort eines Benutzers auf der Kommandozeile zurücksetzen](../../../eng-us/features/6.0/reset-user-password.md)
- [Benutzerkonten prüfen](../../../eng-us/features/6.0/user-hygiene.md)
- [Signierte Links in der E-Mail „Neuer Benutzer registriert“](../../../eng-us/features/6.0/registration-admin-links.md)
- [Rollen: IDs der Richtlinien, Sortierung und Schaltflächen für die Reihenfolge](../../../eng-us/features/6.0/role-policy-order.md)
- [Die Benutzer und Gruppen einer Rolle, seitenweise](../../../eng-us/features/6.0/role-assignment-paging.md)
- [Berechtigungsprüfungen für Benutzer mit vielen Rollenzuweisungen](../../../eng-us/features/6.0/permission-checks-for-many-assignments.md)
- [Schnellere Zugriffsarrays für Benutzer mit vielen Rollenzuweisungen](../../../eng-us/features/6.0/access-array-prefetch.md)
- [Benachrichtigungen: die Anleitung für Benutzer](../../../eng-us/features/6.0/notifications.md)
- [E-Mail-Einstellungen: die Anleitung für Benutzer](../../../eng-us/features/6.0/mail-preferences.md)

## Die Linie Exponential Platform

- [Das Skelett von Exponential Platform DXP: create-project und eine dokumentierte Installation](../../../eng-us/features/6.0/platform-dxp-skeleton.md)
- [Exponential Platform Nexus: eine fertige Site in Minuten](../../../eng-us/features/6.0/platform-nexus-starter.md)
- [Die Administrationsoberfläche von Exponential Platform](../../../eng-us/features/6.0/platform-admin-ui-fork.md)
- [Layouts auf der Plattform: der Fork von layouts-core](../../../eng-us/features/6.0/platform-layouts-core-fork.md)
- [Site-Bundles, Metadaten und die Twig-Werkzeuge der Plattform](../../../eng-us/features/6.0/platform-site-bundles.md)
- [SQLite für Exponential Platform: kein Datenbankserver nötig](../../../eng-us/features/6.0/platform-sqlite-install.md)
- [PHP 8.4 und 8.5 für den älteren Symfony-Stack: die Framework-Forks](../../../eng-us/features/6.0/platform-php85-framework-forks.md)
- [Legacy Bridge: Exponential 6 innerhalb der Symfony-Plattform betreiben](../../../eng-us/features/6.0/legacy-bridge.md)
