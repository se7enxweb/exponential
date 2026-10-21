# Anleitungen: der Lernpfad

Diese Seite ist das Inhaltsverzeichnis der Anleitungen. Lesen Sie sie, wenn Sie neu bei Exponential sind oder wenn Sie das System
kennen und den kürzesten Weg zu einem bestimmten Ergebnis suchen.

Eine Anleitung ist ein Tutorial. Sie arbeiten sie von oben nach unten durch und brauchen kein Vorwissen. Am Ende haben Sie ein funktionierendes
Ergebnis: eine installierte Site, eine veröffentlichte Seite, ein geändertes Template, eine Site hinter einem echten Webserver, einen gezielt
geleerten Cache. Jeder Schritt ist ein Befehl oder ein Klickpfad, mit der zu erwartenden Ausgabe. Wo ein Thema mehr Tiefe braucht, verweist die
Anleitung auf die Funktionsseite, die Spezifikation oder den Upgrade-Hinweis.

## Kurz gesagt

- Beginnen Sie mit [Erste Schritte](erste-schritte.md). In zehn bis fünfzehn Minuten haben Sie eine installierte Site, einen Login und eine erste Template-Änderung.
- Wählen Sie dann unten Ihren Weg: **Redakteure**, **Site-Gestalter**, **Entwickler** oder **Betreiber**.
- Ein Wort unklar? [Das Glossar](../../eng-us/glossary.md) erklärt jeden Begriff in einer Zeile und verweist auf die Seite, die ihn vermittelt.
- Sie möchten wissen, warum etwas so ist, wie es ist? [Die Geschichte](../../eng-us/history/README.md) erzählt sie Monat für Monat.

## Die eigene Einrichtung in zwei Minuten prüfen

Diese Befehle lesen nur. Führen Sie sie im Wurzelverzeichnis der Installation aus (dem Verzeichnis mit `console`, `index.php` und
`settings/`). Ergänzen Sie `--allow-root-user` bei einem Befehl, der die Ausführung als root verweigert.

```bash
./console --version
```

Erwartet: Die erste Zeile lautet `console (Exponential) 6.0.15stable`. Darunter stehen die Version, der Build, die
Anzahl der Extensions, der Webserver und die PHP-Version, und am Ende die Zeile
`Written by 7x (se7enx.com) and the Exponential contributors.`

```bash
./console list exp
```

Erwartet: die Liste der Befehle der Familie `exp:`, je eine Zeile (`exp:cache`, `exp:install`, `exp:ini`,
`exp:maintenance`, `exp:velocity` und viele mehr). `./console help <command>` erklärt jeden einzelnen davon.

```bash
./console exp:cache clear --all --dry-run --allow-root-user
```

Erwartet: die Liste der Caches, die ein echtes Leeren leeren würde, mit einer abschließenden Zeile, die mit
`DRY RUN PASS` beginnt und die Anzahl der Caches nennt. Es wird nichts geleert.

```bash
./console exp:maintenance status --allow-root-user
```

Erwartet: auf einer normalen Site die Zeile `Maintenance is off.`.

Schlägt der erste Befehl fehl, sind Sie nicht im Wurzelverzeichnis der Installation, oder PHP fehlt.
[Erste Schritte](erste-schritte.md#1-was-sie-brauchen) sagt, was nötig ist.

## Die Anleitungen

Lesen Sie sie beim ersten Mal in dieser Reihenfolge. Jede steht aber auch für sich.

| Anleitung | Am Ende haben Sie | Zeit |
|---|---|---|
| [Erste Schritte](erste-schritte.md) | Eine installierte Site, den Administrator-Login, eine erste Seite, eine erste Template-Änderung | 10 bis 15 Minuten |
| [Das Inhaltsmodell und das Bearbeiten von Inhalten](inhaltsmodell-und-bearbeitung.md) | Eine Inhaltsklasse, ein Objekt, die Tabelle der Unterelemente, den Papierkorb, einen Inhaltsjob | 30 Minuten |
| [Templates und Design](templates-und-design.md) | Das Design, das Sie verwenden, das Template, das ein Stück einer Seite geschrieben hat, einen Override, den Sie rückgängig machen können, das Design admin4 | 15 Minuten |
| [Extensions](extensions.md) | Eine Extension gefunden, installiert, eingeschaltet und konfiguriert; eine eigene gebaut und veröffentlicht | 45 Minuten |
| [Die Seite „Extensions“](extensions-seite.md) | Extensions in einer einzigen Liste aktiviert, deaktiviert und sortiert, die Änderungen mit ihren Risiken geprüft und angewendet, die geschriebene und die tatsächliche Ladereihenfolge auseinandergehalten | 10 Minuten |
| [Bereitstellung](bereitstellung.md) | Die Site ausgeliefert von Apache mit PHP-FPM, von Velocity oder von FrankenPHP, mit HTTPS | 30 Minuten |
| [Betrieb einer Site](betrieb-einer-website.md) | Die richtigen Caches geleert, Cronjobs in Betrieb genommen, den statischen Cache und Preload eingeschaltet, eine Sicherung, eine Checkliste | 30 Minuten |
| [Systeminformationen](systeminformationen.md) | Welcher Server geantwortet hat und wie er PHP ausführt, die Zustandsprüfungen gelesen und umgesetzt, die Übersichtskarten verstanden, einen maskierten Bericht für den Support heruntergeladen, dasselbe über die Kommandozeile | 15 Minuten |
| [Caches](caches.md) | Jeden Cache in seiner Gruppe gelesen, mit seinem Inhalt, seiner Größe und dem Zeitpunkt der letzten Leerung, ein Cache, eine Gruppe oder alle geleert mit der Bestätigung, die nennt, was wegfällt, Velocitys Antwort-Cache und Neustart verstanden, dasselbe aus einer Shell | 15 Minuten |
| [Benchmarks](benchmarking.md) | Seiten gemessen, aus dem Cache und gerendert, Apache gegen Velocity, eine Codeänderung und eine Bereitstellung auf Rückschritte geprüft, die Performance-Prüfung der CI verstanden | 30 Minuten |
| [Cronjobs](cronjobs.md) | Jeden Teil und jedes Skript gesehen, einen Teil und ein einzelnes Skript ausgeführt und im Browser verfolgt, die Crontab-Zeilen installiert, die Logs gelesen, die üblichen Probleme gelöst | 30 Minuten |
| [Caches vorwärmen](caches-vorwaermen.md) | Eine Site über Setup > Vorladen und aus der Shell vorgewärmt, einen Probelauf gelesen, einen Lauf verfolgt und seine defekten Links und Bild-Aliase gelesen, die Cron-Zeile installiert, die Adresse eines Siteaccess geprüft | 15 Minuten |
| [Workflows](workflows.md) | Eine Freigabe vor dem Veröffentlichen mit ihrem Trigger eingerichtet, jeden wartenden Prozess unter Setup > Workflow-Prozesse in Worten gelesen, den Workflow-Cronjob geprüft, einen hängenden Prozess sicher abgebrochen, die Trigger und Workflow-Gruppen gelesen und eine Gruppe entfernt im Wissen, was mit ihr wegfällt | 30 Minuten |
| [Wartungsmodus](wartungsmodus.md) | Die Site für ein Zeitfenster vom Netz genommen, mit einer Meldung, einem voraussichtlichen Ende und durchgelassenen Testern, die Vorschau geprüft, die Site über die Seite und aus einer Shell zurückgeholt, die üblichen Aussperrungen gelöst | 20 Minuten |
| [Sicherheit und Audit](sicherheit-und-audit.md) | Die Härtung geprüft, Rollen, die nur das Nötige geben, das Audit-Protokoll gelesen, Debug-Ausgabe nur für Ihre Adresse | 30 Minuten |
| [Zweistufige Anmeldung](zwei-faktor-anmeldung.md) | Einen Code aus einer Authenticator-App oder per E-Mail nach dem Passwort: von einem Benutzer eingerichtet, vom Eigentümer eingeschaltet oder vorgeschrieben, ein verlorenes Telefon behandelt, Schaltflächen für Social Login | 15 Minuten |
| [Die Einstellungsseite](einstellungsseite.md) | Die Herkunft jedes INI-Werts unter Setup > Ini Einstellungen mit seiner Override-Kette gelesen, eine Einstellung in jeder Datei gefunden, zwei Siteaccesses verglichen, ein Wert geändert und seine Wirkung unter PHP-FPM und Velocity gesehen, Geheimnisse maskiert gehalten | 20 Minuten |
| [RSS-Feeds](rss-feeds.md) | Jeden Export und Import auf der überarbeiteten RSS-Seite gelesen, mit Adresse, Quellen, Einträgen und letztem Lauf, den Import-Cronjob geprüft, einen Feed entfernt im Wissen, was das für seine Leser bedeutet | 20 Minuten |
| [Klassengruppen](klassengruppen.md) | Jede Klassengruppe mit ihren Klassen und Objekten gelesen, bevor sie angehakt wird gewusst, was das Entfernen einer Gruppe entfernen würde, eine Gruppe sicher entfernt | 15 Minuten |
| [Sektionen](sektionen.md) | Sektionen verstanden und auf den überarbeiteten Seiten gesehen, eine angelegt, Inhalte ihr zugewiesen, eine Rolle auf sie beschränkt, ein Template für sie überschrieben, eine ungenutzte entfernt, die üblichen Probleme gelöst | 30 Minuten |
| [Rollen, Richtlinien und nicht aktivierte Benutzer](rollen-und-richtlinien.md) | Jede Rolle gelesen mit dem, was sie gibt und wem, ihre Richtlinien als Sätze, eine Rolle mit eingeschränkten Richtlinien gebaut und per Drag and Drop sortiert, einer Gruppe zugewiesen, und unvollständige Registrierungen aktiviert, erneut gemailt oder entfernt | 25 Minuten |
| [Der Design-Umschalter der Administration](ui-umschalter-design.md) | Die Designs eingerichtet, die ein Benutzer für seine eigene Sitzung wählen darf, ein installiertes oder Extension-Design mit seiner Fallback-Kette hinzugefügt, das Umschaltwerkzeug in jedem angebotenen Design, `uiswitch/design` einer Rolle gegeben, den Umschalter getestet und die üblichen Probleme gelöst | 20 Minuten |
| [Pakete](pakete.md) | Jedes Repository und Paket unter Setup > Pakete gelesen, mit Installationszustand, Größe und Abhängigkeiten, die eigenen Pakete des Installationsprogramms erkannt, ein Paket im klassischen oder im JSON-Format heruntergeladen, einen Upload vor der Annahme validiert, ein Paket im Hintergrund oder als Entwürfe installiert, ein Paket entfernt im Wissen, was wegfällt | 20 Minuten |
| [Klassische Menüs](klassische-menueeinstellungen.md) | Ob für Ihren Siteaccess Design > Menüs (klassisch) zählt oder Layouts, die vier Anordnungen und was jede auflistet, fünf Rezepte mit ihren genauen Zeilen, klassische Menüs aus eigenen Templates gezeichnet | 20 Minuten |
| [Sitzungen](sitzungen.md) | Wer sich kürzlich angemeldet hat und wer gerade angemeldet ist, den Sitzungs-Handler verstanden, Sitzungen gesucht, sortiert und sicher entfernt, ohne sich selbst abzumelden | 15 Minuten |
| [Lesezeichen](lesezeichen.md) | Ihre Lesezeichen nach Ordnern gruppiert mit ihrem Zustand gelesen, gefunden, sortiert und geblättert, durch Auswählen oder Ziehen zwischen Ordnern verschoben, einen Ordner umbenannt und entfernt im Wissen, was mit seinem Inhalt geschieht, die üblichen Probleme gelöst | 15 Minuten |
| [Versionen eines Objekts](inhaltsverlauf.md) | Die Versionen eines Eintrags nach Status, Übersetzung und Ersteller gelesen, zwei verglichen, aus einer alten Version einen neuen Entwurf in der gewählten Übersetzung erzeugt, Versionen entfernt im Wissen, was bleibt, wer was sieht | 15 Minuten |
| [Meine Entwürfe und meine ausstehenden Einträge](entwuerfe-und-ausstehende-objekte.md) | Ihre Entwürfe nach Übersetzung, Klasse und Alter gefunden und einzeln, per Auswahl oder wenn sie alt sind entfernt, Ihre ausstehenden Versionen mit dem gelesen, was sie aufhält, die Versionen geöffnet, die auf Ihre Freigabe warten | 10 Minuten |
| [Gesammelte Informationen](gesammelte-informationen.md) | Die Formulare, die Daten gesammelt haben, gefunden und sortiert, ihre Sammlungen gelesen und als CSV exportiert, Sammlungen nach einem Export entfernt, die üblichen Probleme gelöst | 15 Minuten |
| [PDF-Exporte](pdf-exporte.md) | Ein PDF aus einem Teil des Inhaltsbaums erstellt, einmalig oder bei Bedarf erzeugt, aus der Liste heruntergeladen und neu erzeugt, sein Formular fehlerfrei ausgefüllt, einen Export entfernt im Wissen, welche Datei mit ihm wegfällt | 15 Minuten |
| [Links, URL-Aliase, Platzhalter und Suchstatistiken](urls-und-aliase.md) | Defekte und ungeprüfte Links mit den Objekten gefunden, die sie verwenden, Links von Hand markiert, einen globalen Alias und einen Platzhalter angelegt und ausprobiert, die Suchbegriffe gelesen, die nichts finden, die üblichen Probleme gelöst | 30 Minuten |
| [Objektzustände](objektzustaende.md) | Eine Zustandsgruppe mit ihren Zuständen, sortiert und übersetzt, Inhalte einzeln und nach Teilbaum zwischen Zuständen verschoben, Rollen, die Zustände je Stufe lesen, bearbeiten und setzen, ein Prüf-Workflow, die üblichen Probleme gelöst | 30 Minuten |
| [Eigentümer ändern](eigentuemer-aendern.md) | Den Eigentümer eines Objekts und eines Teilbaums in der Administration nach einer Vorschau geändert, dasselbe auf der Kommandozeile mit --dry-run, die Anleitungen der Extension gefunden | 10 Minuten |
| [Benachrichtigungen: ausführen und Probleme beheben](benachrichtigungen-administrator.md) | Den Benachrichtigungs-Cronjob eingerichtet, die Statusseite gelesen, einen Lauf ohne E-Mail-Versand ausprobiert, die üblichen Probleme gelöst | 30 Minuten |
| [Benachrichtigungen: Architektur, Erweiterung und Tests](benachrichtigungen-entwickler.md) | Einen eigenen Ereignistyp und Handler, die an eine Adresse mailen, einen Test, der E-Mails in Dateien hält | 45 Minuten |
| [E-Mail-Einstellungen: einrichten und ausführen](mail-einstellungen-administrator.md) | Fußzeile und Links eingerichtet, die Statusseite gelesen, eine Bitte um Einstellung der E-Mails, einen Export und eine Löschung bearbeitet, die Schranke ohne Versand getestet | 30 Minuten |
| [E-Mail-Einstellungen: Kategorien, die E-Mail-Schranke und Tests](mail-einstellungen-entwickler.md) | Eine eigene Kategorie mit einem Handler für ältere Daten, E-Mails durch die Schranke, einen Test, der E-Mails in Dateien hält | 30 Minuten |
| [Inhaltssprachen](inhaltssprachen.md) | Eine Sprache mit der durchsuchbaren Auswahl hinzugefügt, von einem Siteaccess über SiteLanguageList angezeigt, Inhalte in sie übersetzt, Inhalte aus einer falschen Sprache herausgeholt, eine Sprache entfernt, ohne etwas zu verlieren | 30 Minuten |
| [Remote-Dienste und Apps](entfernte-dienste-und-apps.md) | Dienste aus der Shell und aus Python aufgerufen, ein persönliches API-Token, ein Portal-Frontend | 20 Minuten |
| [Persönliche API-Schlüssel](api-schluessel.md) | Einen Schlüssel auf der Site erstellt, mit nur den Scopes, die er braucht, REST damit aus curl und Python aufgerufen, einen Schlüssel rotiert und widerrufen; als Administrator: Schlüssel für eine Rolle erlaubt, jeden Schlüssel gesehen und widerrufen, das Audit gelesen | 20 Minuten |
| [REST-API v1 und v2: eine Extension](rest-api-v1-und-v2.md) | Welche URL unter v1 und v2 aus der einen Extension `ezprestapi` antwortet, einen Client mit OAuth2 oder einem Schlüssel authentifiziert, die API mit curl aufgerufen, `rest.ini` gelesen, eine Installation weg vom veralteten `ezprestapiprovider` gebracht | 20 Minuten |
| [Upgrade](aktualisieren.md) | Eine Installation von 4.x, 5.x oder einer früheren 6.0.x auf die aktuelle 6.0-Linie gebracht und geprüft | 20 Minuten für eine kleine Site |
| [Die Upgrade-Prüfung](upgrade-pruefung.md) | Die Dateien und die Datenbank unter Setup > Upgrade-Prüfung mit der Release verglichen, jede Art von Befund gelesen und behandelt, den Bericht heruntergeladen, dieselbe Prüfung aus einer Shell ausgeführt | 15 Minuten |
| [Fehlende Dateien und Bild-Aliase](fehlende-dateien.md) | Im Speicher fehlende Dateien und veraltete Bild-Aliase unterhalb eines Knotens gefunden, mit dem Ziel „Fehlende Dateien“ der defekten Beziehungen, jede Zeile gelesen und nach einer Vorschau aufgelöst (neu erzeugt, wiederhergestellt, erneut hochgeladen, ihre Referenz entfernt oder zur Prüfung markiert), dasselbe aus einer Shell, zuerst mit einem Probelauf | 15 Minuten |
| [Einen Fehler anhand seiner Referenz finden](fehler-anhand-der-referenz-finden.md) | Die Logzeilen einer fehlgeschlagenen Anfrage über die ERR-Referenz gefunden, die ihre Fehlerseite anzeigte, den Rest der Anfrage und das Audit jener Minute gelesen, dasselbe aus einer Shell, ohne personenbezogene Daten preiszugeben | 10 Minuten |

## Weg 1: Redakteure

Sie schreiben und verwalten Inhalte in der Administrationsoberfläche.

1. [Erste Schritte](erste-schritte.md): Abschnitte 4 bis 6 (die Site ansehen, erste Anmeldung, erste Inhalte).
2. [Das Inhaltsmodell und das Bearbeiten von Inhalten](inhaltsmodell-und-bearbeitung.md): Klassen, Objekte, der Online-Editor, die Tabelle der Unterelemente, das Bearbeiten mehrerer Knoten, der Papierkorb, Inhaltsjobs.
3. Vertiefung, wenn Sie sie brauchen:
   - [Die Tabelle der Unterelemente: Optionen und Spalten](../../eng-us/features/6.0/subitems-table-options.md) und [Blättern in Listen](../../eng-us/features/6.0/admin-list-paging.md)
   - [Mehrere Einträge auf einmal bearbeiten](../../eng-us/bc/6.0/multi-node-edit.md)
   - [Online-Editor: TinyMCE 8](../../eng-us/features/6.0/online-editor-tinymce8.md)
   - [Der Papierkorb: wer es gelöscht hat und wo es war](../../eng-us/features/6.0/trash-who-and-where.md)
   - [Große Vorgänge als Inhaltsjobs](../../eng-us/features/6.0/content-jobs.md)
   - [Exponential Intelligence](../../eng-us/features/6.0/extensions/expai.md): der Schreibassistent, der Entwürfe im Tab Intelligence, im Bearbeitungsformular und in Vector schreibt
   - [Exponential Layouts](../../eng-us/bc/6.0/LAYOUTS.md): Seiten im Layout-Editor aus Layouts, Zonen und Blöcken zusammenstellen
   - [Objektzustände](objektzustaende.md): eine Stufe wie „In Prüfung“ oder „Freigegeben“ für Inhalte setzen
   - [PDF-Export](../../eng-us/features/6.0/pdf-export.md), [Übersetzungen und Sprachen](../../eng-us/features/6.0/translations-and-languages.md), [Inhaltssprachen](inhaltssprachen.md) (die Anleitung)
   - [Ihre E-Mail-Einstellungen](../../eng-us/features/6.0/mail-preferences.md): welche E-Mails Sie erhalten, Abmelden mit einem Klick, Ihre Daten
   - [Shop-Dashboard](../../eng-us/features/6.0/store-dashboard.md) und [Bestellbelege](../../eng-us/features/6.0/order-receipts.md), wenn Sie einen Shop betreiben

## Weg 2: Site-Gestalter

Sie bestimmen, wie die Site aussieht und welche Extensions sie verwendet.

1. [Erste Schritte](erste-schritte.md): Abschnitt 7 (erste Template-Änderung, per Klickpfad und per Datei).
2. [Das Inhaltsmodell und das Bearbeiten von Inhalten](inhaltsmodell-und-bearbeitung.md): das Modell, das Ihre Templates rendern.
3. [Templates und Design](templates-und-design.md): das Template finden, überschreiben, prüfen, das Design der Administration wechseln.
   Vertiefung:
   - [Template-Pfad-Kommentare](../../eng-us/features/6.0/template-path-comments.md): sehen, welche Datei welches Markup geschrieben hat
   - [Template-Editor](../../eng-us/features/6.0/template-editor-overrides.md) und [Reihenfolge der Overrides](../../eng-us/features/6.0/template-override-ordering.md)
   - [Die Exp-Debug-Leiste](../../eng-us/features/6.0/exp-debug-bar.md)
   - [Exponential Admin UI](../../eng-us/features/6.0/exp-adminui.md): das Aussehen der Admin UI für die Administration, als eigener Siteaccess `adminui`
   - [Das Design der Administration wählen](../../eng-us/features/6.0/ui-switch-design.md): admin4l, admin4, AdminUI oder das klassische Design je Benutzer und Sitzung, und [die zugehörige Anleitung](ui-umschalter-design.md)
   - [Exponential Layouts](../../eng-us/bc/6.0/LAYOUTS.md): Blöcke, Zonen und Regeln, im Browser gebaut
4. [Extensions](extensions.md): eine Extension finden, installieren, einschalten und konfigurieren. Vertiefung:
   - [Was eine neue Installation mitbringt](../../eng-us/features/6.0/default-extension-distribution.md), [die Liste der Extensions](../../eng-us/features/6.0/extension-list-and-downloads.md), [Ladereihenfolge](../../eng-us/features/6.0/extension-loading-order.md)
   - [Einstellungen über die Kommandozeile ändern](../../eng-us/features/6.0/exp-ini-command.md), [INI-Overrides für mehrere Sites](../../eng-us/features/6.0/multi-site-ini-overrides.md)
   - [Anfrageregeln](../../eng-us/features/6.0/request-rules.md), [robots.txt](../../eng-us/features/6.0/robots-txt.md)
   - [Rollen und Richtlinien in Reihenfolge](../../eng-us/features/6.0/role-policy-order.md), [ausgeblendete Tabs der Administration](../../eng-us/features/6.0/hidden-admin-tabs.md), [Objektzustände](objektzustaende.md) (Stufen wie Entwurf, in Prüfung und freigegeben, die Rollen berücksichtigen)
   - Eine Seite je Extension: [Index der Extensions](../../eng-us/features/6.0/extensions/README.md)

## Weg 3: Entwickler

Sie schreiben Extensions, Templates mit Logik, Befehle und Integrationen.

1. [Erste Schritte](erste-schritte.md), dann das Einstellungswerkzeug: [exp:ini](../../eng-us/features/6.0/exp-ini-command.md).
2. [Extensions](extensions.md), Abschnitte 6 und 7: eine Extension von Hand oder mit den RAD-Assistenten bauen und veröffentlichen.
   Erweiterungspunkte (RAD): [RAD-Werkzeuge](../../eng-us/features/6.0/rad-tools.md), [die Erweiterungspunkte](../../eng-us/bc/6.0/rad-extension-points.md), [die Erweiterungsoberfläche](../../eng-us/bc/6.0/rad-extension-surface.md), [RAD-Sicherheit](../../eng-us/bc/6.0/rad-security.md).
3. Befehle, Cronjobs und Modul-Views als Klassen: [Befehle, Cronjob-Teile und Modul-Views](../../eng-us/bc/6.0/cli_cronjob_view_abstractions.md) und die [Spezifikation](../spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md); [Konsole](../../eng-us/bc/6.0/console.md).
4. Metadaten und Dienste: [Remote-Dienste und Apps](entfernte-dienste-und-apps.md) (die Anleitung), [Persönliche API-Schlüssel](api-schluessel.md) (REST mit einem Schlüssel), [Metadaten von Extensions](../spezifikationen/6.0/extension-metadaten.md), [Backend-Dienste über ezjscore](../../eng-us/bc/6.0/backend_ezjscore_services.md).
5. Benachrichtigungen: [Architektur, Erweiterung und Tests](benachrichtigungen-entwickler.md), [die Spezifikation](../spezifikationen/6.0/benachrichtigungen.md), [die INI-Referenz](../spezifikationen/6.0/benachrichtigungen-ini.md), [die Befehle](../spezifikationen/6.0/benachrichtigungen-cli.md).
   E-Mail-Einstellungen: [Kategorien, die E-Mail-Schranke und Tests](mail-einstellungen-entwickler.md), [die Spezifikation](../spezifikationen/6.0/mail-einstellungen.md), [die Befehle](../spezifikationen/6.0/mail-einstellungen-cli.md), [die rechtliche Checkliste](../spezifikationen/6.0/mail-einstellungen-compliance.md).
6. Tests und Kompatibilität: [Continuous Integration](../spezifikationen/6.0/kontinuierliche-integration.md), [Unterstützung für PHP 8](../../eng-us/bc/6.0/php8.md).
7. Brücken zur Symfony-Plattform: [Legacy Bridge](../../eng-us/features/6.0/legacy-bridge.md), [die Repositorys der Plattform](../../eng-us/history/ecosystem.md).

## Weg 4: Betreiber

Sie betreiben die Installation: Server, Caches, Sicherungen, Upgrades, Sicherheit.

1. [Erste Schritte](erste-schritte.md), dann [Bereitstellung](bereitstellung.md) und [Betrieb einer Site](betrieb-einer-website.md).
2. Gezielt installieren: [Installation mit einem Befehl](../../eng-us/features/6.0/install-in-one-command.md), [Kickstarter](../../eng-us/features/6.0/kickstarter-cli.md) für wiederholbare Installationen, [SQLite](../../eng-us/features/6.0/sqlite-database.md), [MongoDB](../../eng-us/features/6.0/mongodb-database-support.md).
3. Der Webserver: [Velocity-Webserver](../../eng-us/features/6.0/velocity-web-server.md), [sein Control Panel](../../eng-us/features/6.0/velocity-control-panel.md), [HTTPS-Zertifikate](../../eng-us/features/6.0/velocity-https-certificates.md), [Engines im Vergleich](../../eng-us/bc/6.0/velocity-engines.md), [FrankenPHP](../../eng-us/bc/6.0/frankenphp.md).
4. Caches und Geschwindigkeit: [Cache-Steuerung](../../eng-us/bc/6.0/cache-console.md), [HTTP-Caching](../../eng-us/bc/6.0/http-caching.md), [SQL-Abfrage-Cache](../../eng-us/bc/6.0/sql-query-cache.md), [statischer Cache](../../eng-us/features/6.0/static-cache-generator.md), [Preload](../../eng-us/features/6.0/preload-sites-view.md), [Benchmark](../../eng-us/features/6.0/benchmark.md) und die Anleitung [Benchmarks](benchmarking.md).
5. Gesund halten: [Benachrichtigungen](benachrichtigungen-administrator.md) (der Cronjob, die Statusseite, Fehlerbehebung), [E-Mail-Einstellungen](mail-einstellungen-administrator.md) (die Fußzeile, Einwilligung, Sperrliste, Aufbewahrung), [Wartungsmodus](../../eng-us/features/6.0/maintenance-mode.md), [Reparatur aus dem Browser](../../eng-us/features/6.0/repair-from-the-browser.md), [Prüfung der Dateikonsistenz](../../eng-us/features/6.0/file-consistency-check.md), [fehlende Dateien und Bild-Aliase](fehlende-dateien.md), [Cronjobs](../../eng-us/features/6.0/cronjobs-console.md), [der Log-Viewer](../../eng-us/features/6.0/log-viewer.md) und [einen Fehler anhand seiner Referenz finden](fehler-anhand-der-referenz-finden.md), [Workflows und ihre wartenden Prozesse](workflows.md), [Audit-Protokoll](../../eng-us/features/6.0/audit-trail.md).
6. Sicherheit: [Sicherheit und Audit](sicherheit-und-audit.md) (die Anleitung), [Zweistufige Anmeldung](zwei-faktor-anmeldung.md), [Sicherheitsvorgaben vom September 2026](../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md), [die Patches vom August 2026](../spezifikationen/6.0/sicherheitshaertung-2026-08.md), [die Härtung in 6.0.13](../spezifikationen/6.0/sicherheitshaertung-6.0.13.md).
7. Upgrade: Folgen Sie [Upgrade](aktualisieren.md); lesen Sie das [Changelog zu 6.0.15](../../changelogs/6.0/6.0.15.md), dann die Verhaltensänderungen von [Juli und August](../../eng-us/bc/6.0/behaviour-changes-2026-07-08.md), [16. bis 30. September](../../eng-us/bc/6.0/behaviour-changes-2026-09b.md) und [Oktober](../../eng-us/bc/6.0/behaviour-changes-2026-10.md), in dieser Reihenfolge.

## Verwandte Seiten

- Ein Wort, das Sie nicht kennen: [Glossar](../../eng-us/glossary.md).
- Was sich geändert hat und warum: [Geschichte](../../eng-us/history/README.md); der Nachweis, dass nichts fehlt: [Abdeckung](../../eng-us/history/coverage.md).
- Release Notes: [Changelogs von 6.0](../../changelogs/6.0/6.0.15.md) und [der Extensions](../../changelogs/extensions/README.md).
- Der Installationstext an einem Ort: [INSTALL](../../eng-us/INSTALL.md).

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
