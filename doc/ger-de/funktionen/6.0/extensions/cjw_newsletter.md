# cjw_newsletter: Newsletter im Admin

Diese Seite richtet sich an Website-Betreiber und Redakteure, die Newsletter versenden, und an Administratoren, die
sie betreiben. `cjw_newsletter` („CJW Newsletter“) ist ein vollständiges Newsletter-System für Exponential:

- Sie schreiben Newsletter-Ausgaben als Inhalte;
- Besucher abonnieren Listen mit Double-Opt-in;
- Ausgaben werden per Cron im Hintergrund versendet;
- Bounces und eine Sperrliste halten die Listen sauber;
- jeder Versand wird archiviert.

Sie stammt vom CJW Network, coolscreen.de, JAC Systeme und Webmanufaktur (2007 bis 2015) und wurde im August 2026 vom
Projekt se7enxweb für PHP 8.5 und Exponential 6 übernommen (4.0.0.0 am 13. August). Die Releases 4.1.0 bis 4.1.17
(22. September bis 4. Oktober 2026) haben sie auf PHP 8, auf persistenten Workern (Velocity) und auf jeder Datenbank
lauffähig gemacht und viele Mängel behoben, die eine systematische Prüfung gefunden hat.

Sie öffnen sie über **Newsletter** im oberen Admin-Menü (`/newsletter/index`). Die Extension führt ihre eigene
Dokumentation (`doc/cjw_newsletter_documentation.pdf`, `INSTALL`, `FAQ`). Diese Seite erklärt, wie Sie sie gefahrlos
erproben und was sich in den Releases von Exponential 6 geändert hat.

## Die Bestandteile

| Teil | Wo | Was es ist |
|---|---|---|
| Newsletter-Systeme, Listen, Ausgaben | Inhaltsbaum, Klassen `cjw_newsletter_system`, `_list`, `_edition` (als Pakete ausgeliefert) | Inhalte, die Sie wie alle anderen bearbeiten; die Wurzel des Baums ist `[NewsletterSettings] RootFolderNodeId` |
| Abonnements und Benutzer | `newsletter/subscription_list`, `newsletter/user_list`, `user_view`, `user_edit`, `user_create` | Wer was abonniert hat, mit Status (ausstehend, bestätigt, freigegeben, vom Benutzer oder von einem Administrator entfernt, Soft- oder Hard-Bounce, gesperrt) |
| Öffentliche Formulare | `newsletter/subscribe`, `configure`, `unsubscribe`, `subscribe_infomail` | Abonnieren, bestätigen, ändern und abmelden |
| Versand | `newsletter/send`, `send_abort`, Cronjobs `cjw_newsletter_mailqueue_create`, `cjw_newsletter_mailqueue_process` | Eine Ausgabe einplanen, sie abbrechen und Cron die Mails einreihen und abarbeiten lassen |
| Vorschau und Archiv | `newsletter/preview`, `preview_archive`, `archive` | Testversand und das Archiv des Versendeten |
| Bounces | `newsletter/mailbox_list`, `mailbox_edit`, `mailbox_item_list`, `mailbox_item_view` | Ein Postfach nach Bounces auslesen; nach `BounceThresholdValue` Bounces (Standard 3) erhält ein Benutzer nichts mehr |
| Sperrliste | `newsletter/blacklist_item_list`, `blacklist_item_add`, `blacklist_item_remove` | Adressen, die nie Mail erhalten |
| CSV | `newsletter/subscription_list_csvimport`, `subscription_list_csvexport`, `import_list`, `import_view` | Abonnenten importieren und exportieren |

Führen Sie die Cron-Teile vom Wurzelverzeichnis der Installation aus (nehmen Sie sie in Cron auf, sobald die
Probe unten funktioniert):

```bash
php runcronjobs.php cjw_newsletter
php runcronjobs.php -s <siteaccess> cjw_newsletter_mailqueue_create
php runcronjobs.php -s <siteaccess> cjw_newsletter_mailqueue_process
```

## Den gesamten Ablauf erproben, ohne eine einzige Mail zu versenden

`cjw_newsletter.ini` kann Mails in Dateien schreiben, statt sie zu versenden. Das ermöglicht eine vollständige
Probe (abonnieren, Double-Opt-in, versenden, Cron, abmelden) ohne Mailserver
und ohne echten Empfänger:

```ini
[NewsletterMailSettings]
TransportMethodCronjob=file
TransportMethodPreview=file
TransportMethodDirectly=file
FileTransportMailDir=var/log/mail
```

Jeder Empfänger erhält eine eindeutig benannte Datei `<date>-<id>-<recipient>.eml` in
`FileTransportMailDir` (ein relatives Verzeichnis ist relativ zur Installation; es muss für den Benutzer des
Webservers und den Benutzer des Cronjobs beschreibbar sein; ein nicht beschreibbares Verzeichnis ergibt ein
Fehlerergebnis, keinen stillen Verlust). Um wirklich zu versenden, setzen Sie eine Methode auf `smtp`
(mit `SmtpTransportServer`, `SmtpTransportPort`, `SmtpTransportUser`,
`SmtpTransportPassword`, `SmtpTransportConnectionType`: leer, `ssl`, `sslv2`, `sslv3` oder
`tls`) oder auf `sendmail`. Unbekannte Transportmethoden und die dokumentierte Methode `mta` werden behandelt.

## Einstellungen, die Sie verwenden werden

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `cjw_newsletter.ini` | `NewsletterSettings` | `RootFolderNodeId` | `1` | Knoten, dessen Kinder die Newsletter-Systeme sind |
| `cjw_newsletter.ini` | `NewsletterSettings` | `PhpCli` | `php` | PHP-Befehl zum Ausführen von CLI-Skripten |
| `cjw_newsletter.ini` | `NewsletterSettings` | `AvailableSkinArray[]` | `default` | Skins in `design:newsletter/skin/<name>` |
| `cjw_newsletter.ini` | `NewsletterMailSettings` | `TransportMethodCronjob`, `TransportMethodPreview`, `TransportMethodDirectly` | siehe Datei | `smtp`, `sendmail` oder `file` je Art der Mail (Newsletter, Testversand, Abonnement- und Info-Mails) |
| `cjw_newsletter.ini` | `NewsletterMailSettings` | `FileTransportMailDir` | `var/log/mail` | Wohin `file` die `.eml`-Dateien schreibt |
| `cjw_newsletter.ini` | `NewsletterMailSettings` | `EmailSubjectPrefix` | leer | Betreff-Präfix der Newsletter-Mails; leer (4.1.16) bedeutet `[Newsletter <host of SiteURL>]`, davor das ausgelieferte Beispiel `[Newsletter example.com]` |
| `cjw_newsletter.ini` | `BounceSettings` | `BounceThresholdValue` | `3` | Bounces, bevor ein Benutzer als gebounct markiert wird |
| `cjw_newsletter.ini` | `NewsletterCsvImportSettings` | `DefaultCsvDelimiter` | `;` | CSV-Trennzeichen |
| `cjw_newsletter.ini` | `NewsletterUserSettings` | `UseTplForNameGeneration` | `disabled` | Benutzernamen aus einem Template erzeugen |

Tragen Sie Ihre Werte in `settings/override/cjw_newsletter.ini.append.php` ein.

## Was sich in den Releases von Exponential 6 geändert hat

### Läuft auf PHP 8.5 und auf einem persistenten Worker

* 4.0.0.0: Deprecations von PHP 8.5 behoben (optionale vor erforderlichen Parametern, `utf8_encode()`
  ersetzt durch `mb_convert_encoding()`, `(boolean)`-Casts, `case X;`).
* 4.1.0: Modul-Ansichten deklarieren auf oberster Dateiebene keine Funktionen oder Klassen mehr ohne
  Schutz, sodass ein Server, der PHP über Anfragen hinweg am Leben hält (Velocity), bei der zweiten Anfrage nicht
  abstürzt. Siehe [Velocity-Engines](../../../../eng-us/bc/6.0/velocity-engines.md).
* 4.1.1: Die Postfachliste und die Abonnement-Ansicht antworteten unter PHP 8 mit HTTP 500 und funktionieren jetzt.

### Läuft auf jeder Datenbank

Drei Admin-Funktionen schlugen unter Oracle fehl und sind seit 4.1.7 portabel: der **CSV-Export der
Abonnementliste** (die Vorschau hängte `LIMIT 10` an, was Oracle ablehnt), die **Benutzersuche**
(ein Muster in doppelten Anführungszeichen ist außerhalb von MySQL ein Bezeichner, und `DISTINCT` über CLOB-Spalten
schlägt fehl) und **das Abbrechen eines Versands** (Backticks und ein abschließendes Semikolon). Sie verwenden nun
die Limitierung des Datenbanktreibers selbst, Zeichenketten in einfachen Anführungszeichen, `EXISTS` und einfache
Bezeichner.

### Prüfung von Sicherheit und Robustheit (4.1.15)

Eine Prüfung der gesamten Extension am 2. Oktober 2026 hat unter anderem Folgendes behoben:

* `user_create` deserialisierte ein verstecktes Formularfeld (Object Injection); das Feld „old post“ enthält
  jetzt einfache Daten.
* `user_create`, `user_edit`, `user_remove` und `blacklist_item_remove` leiteten auf jede URL weiter, die die Anfrage
  nannte; Weiterleitungsziele sind jetzt nur Pfade dieser Website.
* Die Hashes für Konfiguration und Abmeldung waren ein `md5` aus der Zeit und `mt_rand()`; der Zufallsteil ist jetzt
  `random_bytes()`.
* Die öffentlichen Formulare (`subscribe`, `unsubscribe`) sowie die Ansichten `send` und `mailbox_edit` lesen ihre
  Eingaben defensiv: eine gesendete Zeichenkette, wo eine Liste erwartet wurde, ungeprüfte Schlüssel, eine unbekannte
  Postfach-ID (früher fatal) und Abbrechen, das zu einem nie gesetzten Template durchfiel.
  `createSubscriptionByArray()` abonnierte jede beliebige Objekt-ID; jetzt akzeptiert es nur Newsletter-Listen.
* CSV-Import und -Export: Das Tabulator-Trennzeichen funktioniert unter PHP 8, Zeilen mit mehr als 1000 Zeichen
  bleiben erhalten, ein Nullwert verschiebt die folgenden Spalten einer Zeile nicht mehr, Zellen, die mit `=`, `+`,
  `-` oder `@` beginnen, werden nicht als Formeln geschrieben, und die Import-Ansicht liest keine beliebige Datei mehr,
  die ein gesendeter `CsvFilePath` nennt.
* Ein Benutzer, dessen Adresse einen Apostroph enthält, wurde nie gefunden (der
  Wert wurde doppelt maskiert) und konnte sich immer wieder anmelden; Suchen ignorieren außerdem Groß- und
  Kleinschreibung sowie Leerzeichen, und eine leere Remote-ID passt auf niemanden.
* Die offenen Versandeinträge eines gebouncten Benutzers wurden nicht als abgebrochen gespeichert und daher dennoch
  versendet; jetzt werden sie es. Ein gespeicherter Bounce, der sich nicht parsen lässt, wird als verarbeitet markiert,
  statt immer wieder gelesen zu werden, und ein Bounce, der nur den Header `x-cjwnl-user` trägt, findet seinen
  Benutzer.
* Der Bounce-Parser liest Codes und Header so, wie ein Mailserver sie schreibt: 4xx-Diagnosen behalten ihren
  erweiterten Status, `x-cjwnl-`-Header werden ohne Beachtung der Groß- und Kleinschreibung erkannt, Werte mit einem
  Doppelpunkt werden nicht abgeschnitten, ein unbekannter Postfachtyp und ein Server, der seine Nachrichten nicht
  auflisten kann, erzeugen keine undefinierten Variablen mehr, und eindeutige IDs werden maskiert.
* Virtuelle Listen (filterbasiert) führen ihren Konstruktor unter PHP 8 aus, ihr Versandweg funktioniert, mehrere
  externe Filter werden korrekt verknüpft, und der Siteaccess-Filter behält jeden Siteaccess, nicht nur den
  letzten.
* Die Ansichten `preview`, `preview_archive` und `archive` beantworten eine unbekannte Ausgabe, Version oder ein
  unbekanntes Ausgabeformat mit einem sauberen Fehler; `?Debug=1` gibt ihren Inhalt als Ergebnis zurück. Zuvor gab die
  Vorschau für jede ID einen leeren Newsletter aus.
* Die Ausgabe der Edition und die INI des Siteaccess werden über einen Kommandozeilenaufruf abgerufen, der jedes
  Argument quotiert und `--allow-root-user` übergibt, wenn der Prozess als root läuft; zuvor starteten die Vorschau,
  ein von der Kommandozeile angelegter Versand und die Siteaccess-Informationen der Liste nicht, wenn sie als root
  ausgeführt wurden.
* Die Listen- und Filteransichten senden jeden Button an sein eigenes Formular (verschachtelte Formulare werden von
  Browsern verworfen: „Newsletter erstellen“ sendete früher die Klasse des letzten Formulars).

### Dashboard, Befehle und Hintergrundläufe (4.1.17)

Die Startseite `/newsletter/index` ist jetzt ein Dashboard: die Listen mit ihren Abonnenten, die Ausgaben, die letzten Versände, der Transport (und beim Datei-Transport der
Postausgang und seine letzte Mail), wann jeder Cronjob-Teil zuletzt lief, und eine Liste von Problemen mit dem jeweiligen Vorgehen (zum Beispiel „3 Abonnements gehören zu
Newsletter-Benutzern, die nicht mehr existieren“, mit einem Button **Entfernen**). **Jetzt senden**, **Nur zählen**, **Mailkonten verarbeiten**, **Mails parsen**, **Alle Mails abholen** und das CSV-**Alle importieren** starten die
Arbeit im Hintergrund und zeigen ihren Fortschritt auf der Seite, sodass ein langsamer Mailserver die Anfrage nicht mehr aufhält. Dieselbe Arbeit läuft auch über die Shell:

```bash
./console ext:cjw_newsletter:status
./console ext:cjw_newsletter:queue --dry-run
./console ext:cjw_newsletter:mailbox
./console ext:cjw_newsletter:repair --dry-run
```

Die Benutzerliste blättert, sortiert und filtert (zuvor zeigte sie nur die ersten zehn Benutzer); Sperrliste, Importe und Bounces haben dieselben Filter, Sortierungen und Seiten;
das Entfernen eines Sperrlisteneintrags oder eines Mailkontos fragt vorher nach; das Formular für Mailkonten prüft seine Felder und zeigt das gespeicherte Passwort nie an. Das öffentliche Abonnementformular kehrt nur
zu einem Pfad dieser Website zurück (der gesendete Zurück-Link wurde ungeprüft in einem Link verwendet). Die Referenz mit jedem Parameter und jeder Option ist die
[Spezifikation](../../../spezifikationen/6.0/cjw_newsletter.md).

### Oberfläche

* Ab 4.1.2: Sichtbare Texte und Übersetzungen nennen Exponential. 4.1.4: Jeder sichtbare Text
  ist ein Übersetzungsstring mit Deutsch; 4.1.6: Der Reiter im oberen Menü hat einen englischen Tooltip mit einer
  deutschen Übersetzung; 4.1.12: Englisch und Deutsch für jeden String, den der Admin unübersetzt
  anzeigte.
* 4.1.8: Die Newsletter-Liste und der Filter des Formular-Builders verwenden jQuery 4 (`.on()`, `.prop()`).
  4.1.9: Das YUI-Drag-and-drop der Kinderliste von admin2, das ein nicht mehr existierendes Skript lud, ist
  entfernt; Prioritäten werden weiterhin in der Liste gesetzt und mit ihrem Button gespeichert.
  Siehe [Entfernung von YUI](../../../../eng-us/bc/6.0/yui-removal.md).

### Der Newsletter-Baum einer neuen Installation

Ab 4.1.16 kann die Klasse `CjwNewsletterClassInstaller` (`classes/cjwnewsletterclassinstaller.php`) auch die Inhalte anlegen, die eine neue Installation braucht, nicht nur die
Inhaltsklassen (`install()`, aus den beiden `.ezpkg`-Dateien in `packages/`): `installSection()` legt die Sektion „CJW Newsletter“ mit dem Navigationsteil `eznewsletternavigationpart` an;
`installTree( $parentNodeID = null, &$report )` legt eine Wurzel „Newsletter“ (`cjw_newsletter_root`) unterhalb der Inhaltswurzel (Knoten 2) oder unterhalb des angegebenen Knotens an, ein
Newsletter-System und eine Liste mit dem Absender der Website (`AdminEmail`, `SiteName`), HTML- und Textausgabe und dem Skin `default`; `writeRootFolderSetting( $rootNodeID )` setzt
`RootFolderNodeId` in `settings/override/cjw_newsletter.ini.append.php` (legt die Datei an oder ändert nur diesen Wert). Alles ist idempotent: Was existiert, wird
als `already present` gemeldet und unverändert gelassen, und kein Inhaltsobjekt wird angefasst. `installTree()` braucht zuerst die Klassen und antwortet sonst mit `failed: class ... is missing`.
Das Verhalten wird von `tests/tests/extension/cjw_newsletter/cjwNewsletterTreeInstallerTest.php` abgedeckt. Diese Seite hat es nicht gegen einen Live-Baum ausgeführt (es ändert Inhalte); zur
Überprüfung lesen Sie die oben genannte Klasse oder führen Sie diesen Test auf einer Wegwerf-Installation aus.

### Integritätsmanifest

`share/filelist.md5` ist das Manifest, mit dem die Upgrade-Prüfung (**Setup > System-Upgrade > Dateikonsistenz
Überprüfung**) Ihre Dateien vergleicht. Vor 4.1.5 stimmte es nicht mit den ausgelieferten
Dateien überein, und die Prüfung listete Dateien auf, die niemand angefasst hatte (neun Dateien in 4.0.0.0;
einundzwanzig in 4.1.4). Jetzt listet es jede versionierte Datei auf, 309 in 4.1.15, einschließlich der 33 unter
`classes/runnable/`.

### Kommandozeile

Jeder Befehl und jeder Cronjob-Teil ist eine Klasse unter `classes/runnable/`, die die alte Datei aufruft,
jeweils mit einer einzeiligen Beschreibung, die die Konsolenliste anzeigt; Verhalten, `--help`-Text und
Exit-Codes sind unverändert. Siehe
[CLI-, Cronjob- und Ansichtsabstraktionen](../../../../eng-us/bc/6.0/cli_cronjob_view_abstractions.md).

### Die Funktionen von 4.2.0

4.2.0 (5. Oktober 2026) übernimmt, was die alte Extension eznewsletter konnte, und arbeitet über die
[E-Mail-Einstellungen](../e-mail-einstellungen.md) von 6.0.15, statt ein eigenes System zu pflegen. Die eigenen
Anleitungen der Extension beschreiben jeden Bereich; sie liegen in ihrem Ordner `doc/` (`extension/cjw_newsletter/doc/`),
mit `doc/README.md` als Index:

| Bereich | Was hinzukommt | Anleitung |
|---|---|---|
| Zustellbarkeit | Hard Bounces und Beschwerden auf der Unterdrückungsliste, Wiederholungen bei Soft Bounces, Ratenlimits und Stapel, Testgruppen, An- und Abmelden per E-Mail, der Import der Unterdrückungsliste | [deliverability.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/deliverability.md) |
| Redaktion | Wiederkehrende Versände, Artikelpools und die Auswahl, die Freigabe von Ausgaben im Posteingang der Zusammenarbeit | [editorial.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/editorial.md) |
| Darstellung | Platzhalter, die Newsletter-Bedingung, Interessen, eine Ausgabe in mehreren Sprachen, drei neue Skins, Nur-Text-Ansichten, die Vorschau als Abonnent | [rendering.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/rendering.md) |
| Statistik | Öffnungen und Klicks pro Person nur mit der Einwilligung „Newsletter-Statistik“, sonst anonyme Summen, Berichte, A/B-Tests von Betreffzeilen | [statistics.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/statistics.md) |
| SMS | Newsletter per SMS über einen anbieterneutralen Transport, das Double-Opt-in per Code, STOP | [sms.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/sms.md) |
| Import, Export, Migration | Der CSV-Import mit Spaltenzuordnung, der Abonnentenexport, `ext:cjw_newsletter:import-eznewsletter` | [importexport.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/importexport.md) |

Eine Installation von 4.1 führt das Datenbank-Update ihrer Engine aus, einmal `./console ext:cjw_newsletter:translatable-fields`,
und leert die Caches: [upgrade-4.2.md](https://github.com/se7enxweb/cjw_newsletter/blob/4.2.0/doc/upgrade-4.2.md).
Tracking und SMS sind ausgeschaltet, bis sie in `settings/override/cjw_newsletter.ini.append.php` eingeschaltet werden.

Die Kernel-Teile, die sie nutzt: die Kategorien „Newsletter-Statistik“ und „Newsletter per SMS“ auf der zentralen
Einstellungsseite (mit eigenen Zeilen über den Hook für Kategorieteile), `erased()` der Kategorie-Handler (Newsletter-
Benutzer, Interessen, SMS-Daten und personenbezogene Statistiken einer gelöschten Person werden entfernt),
`[BounceSettings] MessageListeners[]` des Bounce-Lesers (Bounces und eingehende Mail) und der Posteingang der
Zusammenarbeit, der die Freigabeanfragen des Newsletters als wartend, freigegeben oder abgelehnt anzeigt.

## Verwandte Seiten

- [Spezifikation: die Extension cjw_newsletter](../../../spezifikationen/6.0/cjw_newsletter.md)
- [Dateikonsistenzprüfung](../dateikonsistenzpruefung.md)
- [CLI-, Cronjob- und Ansichtsabstraktionen](../../../../eng-us/bc/6.0/cli_cronjob_view_abstractions.md)
- [Velocity-Engines](../../../../eng-us/bc/6.0/velocity-engines.md)
- [Chronik](../../../../eng-us/history/extensions/cjw_newsletter.md) und [Versionshinweise](../../../../changelogs/extensions/cjw_newsletter.md)
- [Änderungsverzeichnis](../../../../eng-us/history/ledger/cjw_newsletter.md)
- [Verhaltensänderungen der Extensions](../../../../eng-us/bc/6.0/extensions-behaviour-changes.md)
- Monate: [2026-08](../../../../eng-us/history/extensions/months/2026-08.md), [2026-09](../../../../eng-us/history/extensions/months/2026-09.md), [2026-10](../../../../eng-us/history/extensions/months/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
