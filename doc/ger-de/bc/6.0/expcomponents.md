# Der Wechsel zu expcomponents: was beim Upgrade zu prüfen ist (6.0.15)

Seit 6.0.15 verlangt die `composer.json` des Kernels die **expcomponents**, den Fork der Zeta Components durch
Exponential, anstelle der Ursprungspakete `zetacomponents/*`: `se7enxweb/expcomponents-mail` statt
`zetacomponents/mail` und so weiter für jede Bibliothek, die der Kernel verwendet. Die Klassen behalten ihre
`ezc`-Namen und APIs, und jeder Fork erklärt, dass er den alten Paketnamen ersetzt. Für fast jede Installation ist der
Wechsel ein `composer update` und sonst nichts. Was die Bibliotheken sind und warum sie geforkt werden:
[Exponential Components (expcomponents)](../../funktionen/6.0/expcomponents.md).

Diese Seite ist für Administratoren, die eine Installation aktualisieren, für Entwickler von Erweiterungen und für
alle, die eine eigene `composer.json` pflegen, in der Pakete der Zeta Components stehen.

## Inhalt

1. [Wer betroffen ist](#wer-betroffen-ist)
2. [Was sich in der composer.json des Kernels ändert](#was-sich-in-der-composerjson-des-kernels-ändert)
3. [Installationen: das Upgrade](#installationen-das-upgrade)
4. [Projekte, die selbst zetacomponents-Pakete verlangten](#projekte-die-selbst-zetacomponents-pakete-verlangten)
5. [Erweiterungen](#erweiterungen)
6. [Eigener Code](#eigener-code)
7. [Die Bedeutung von replace im Einzelnen](#die-bedeutung-von-replace-im-einzelnen)
8. [Das Autoloading bleibt gleich](#das-autoloading-bleibt-gleich)
9. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
10. [Fehlerbehebung](#fehlerbehebung)
11. [Zurückrollen](#zurückrollen)
12. [Verwandte Seiten](#verwandte-seiten)

## Wer betroffen ist

| Sie | Was zu tun ist |
|---|---|
| Eine Installation, die die `composer.json` des Kernels unverändert verwendet | Das Upgrade wie gewohnt ausführen (`./console exp:upgrade` oder `composer update`); sonst nichts |
| Ein Projekt, dessen eigene `composer.json` Pakete `zetacomponents/*` aufführt | Diese Zeilen auf die Namen der Forks ändern oder stehen lassen: Sie lösen sich weiter auf ([unten](#projekte-die-selbst-zetacomponents-pakete-verlangten)) |
| Eine Erweiterung, deren `composer.json` `zetacomponents/*` verlangt | Nichts ist nötig; im nächsten Release auf die Namen der Forks wechseln ([Erweiterungen](#erweiterungen)) |
| Eigener Code, der `ezc*`-Klassen verwendet | Nichts; die Klassen sind dieselben |
| Eigener Code oder Skripte, die Dateien unter `vendor/zetacomponents/` über den Pfad einbinden | Den Pfad auf `vendor/se7enxweb/expcomponents-<name>/` ändern oder besser den Autoloader nutzen |
| Deploy-Skripte, Build-Images oder Lizenzberichte, die nach `zetacomponents` suchen | Auch nach `expcomponents` suchen |

## Was sich in der composer.json des Kernels ändert

Vorher (6.0.14 und die Linie 6.0.15 bis zum Wechsel), mit den beiden Forks, die schon verlangt wurden:

```json
"se7enxweb/expcomponents-archive": "~1.5.2",
"zetacomponents/authentication": "~1.4.1",
"zetacomponents/cache": "~1.6.3",
"zetacomponents/configuration": "~1.4",
"zetacomponents/console-tools": "^1.7.5",
"zetacomponents/database": "~1.5.4",
"zetacomponents/debug": "~1.3",
"zetacomponents/event-log": "~1.5",
"zetacomponents/feed": "~1.4.6",
"zetacomponents/image-conversion": "~1.5.1",
"zetacomponents/mail": "~1.10.1",
"zetacomponents/mvc-tools": "~1.2.4",
"zetacomponents/persistent-object": "~1.8.1",
"zetacomponents/signal-slot": "~1.2",
"zetacomponents/system-information": "~1.1.2",
"se7enxweb/expcomponents-webdav": "~1.2.2",
"zetacomponents/base": "^1.9.5"
```

Nachher (das Release 6.0.15, mit jedem Fork am 2026-11-06 veröffentlicht): Jede Zeile nennt
`se7enxweb/expcomponents-<name>`. Die Paketzuordnung, eins zu eins, mit der Bedingung, die der Kernel verlangt:

| Vorher | Nachher |
|---|---|
| `zetacomponents/archive` (bereits `se7enxweb/expcomponents-archive ~1.5.2`) | `se7enxweb/expcomponents-archive ~1.5.3` |
| `zetacomponents/authentication` | `se7enxweb/expcomponents-authentication ~1.4.2` |
| `zetacomponents/base` | `se7enxweb/expcomponents-base ^1.9.6` |
| `zetacomponents/cache` | `se7enxweb/expcomponents-cache ~1.6.4` |
| `zetacomponents/configuration` | `se7enxweb/expcomponents-configuration ~1.4.1` |
| `zetacomponents/console-tools` | `se7enxweb/expcomponents-console-tools ^1.7.6` |
| `zetacomponents/database` | `se7enxweb/expcomponents-database ~1.5.5` |
| `zetacomponents/debug` | `se7enxweb/expcomponents-debug ~1.3.1` |
| `zetacomponents/event-log` | `se7enxweb/expcomponents-event-log ~1.5.1` |
| `zetacomponents/feed` | `se7enxweb/expcomponents-feed ~1.4.7` |
| `zetacomponents/image-conversion` | `se7enxweb/expcomponents-image-conversion ~1.5.2` |
| `zetacomponents/mail` | `se7enxweb/expcomponents-mail ~1.10.2` |
| `zetacomponents/mvc-tools` | `se7enxweb/expcomponents-mvc-tools ~1.2.5` |
| `zetacomponents/persistent-object` | `se7enxweb/expcomponents-persistent-object ~1.8.3` |
| `zetacomponents/signal-slot` | `se7enxweb/expcomponents-signal-slot ~1.2.1` |
| `zetacomponents/system-information` | `se7enxweb/expcomponents-system-information ~1.1.3` |
| `zetacomponents/webdav` (bereits `se7enxweb/expcomponents-webdav ~1.2.2`) | `se7enxweb/expcomponents-webdav ~1.2.3` |

`zetacomponents/image-analysis` stand nie im `require` des Kernels; es kam mit `image-conversion` und kommt jetzt mit
dessen Fork als `se7enxweb/expcomponents-image-analysis` (1.2.1). Jeder Fork steht mit Repository und Release auf der
[Funktionsseite](../../funktionen/6.0/expcomponents.md#die-pakete); der Kernel verlangt jeden in der Tilde- oder
Caret-Form, nie als exakte Version.

## Installationen: das Upgrade

1. Legen Sie eine Sicherung an wie bei jedem Upgrade (`./console exp:upgrade` tut das für Sie).
2. Aktualisieren Sie den Code auf das Release, dann die Bibliotheken. Mit der Konsole beides in einem Lauf:

   ```bash
   ./console exp:upgrade --to=v6.0.15            # Probelauf: zeigt unter anderem die Änderungen an den Bibliotheken
   ./console exp:upgrade --apply --to=v6.0.15    # der Code, dann `composer install` aus der Lock-Datei des Releases
   ```

   Von Hand, je nachdem, wie die Installation angelegt wurde ([Exponential 6.0 aktualisieren](../../UPDATE.md)):

   ```bash
   # ein Git-Klon des Kernels: nach dem Auschecken des Release-Tags installieren, was seine composer.lock nennt
   composer install --no-dev
   # ein Composer-Projekt, das se7enxweb/exponential verlangt: den Kernel mit seinen neuen Anforderungen aktualisieren
   composer update se7enxweb/exponential --with-all-dependencies
   ```

   Composer entfernt die Pakete `zetacomponents/*` und installiert die Pakete `se7enxweb/expcomponents-*`. Seine
   Ausgabe zeigt je Bibliothek eine Zeile „Removing“ und eine Zeile „Installing“; das ist das erwartete Ergebnis.
3. Erzeugen Sie die Klassen-Maps des Kernels und der Erweiterungen neu und leeren Sie die Caches, wie nach jedem
   Update der Bibliotheken:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all
   ```

4. Starten Sie die PHP-Worker neu, damit sie die neuen Dateien laden: PHP-FPM neu laden oder Velocity neu starten
   (`./console exp:velocity restart`), je nachdem, was die Site ausliefert.
5. [Prüfen Sie das Ergebnis](#das-ergebnis-prüfen).

Zu diesem Wechsel gehört keine Änderung an Datenbank, INI-Einstellungen oder Templates.

## Projekte, die selbst zetacomponents-Pakete verlangten

Manche Projekte haben eine eigene `composer.json` (ein Site-Projekt um den Kernel, ein Plattform-Projekt mit der
Legacy Bridge), die Pakete der Zeta Components direkt nennt, etwa weil eine alte Anleitung sagte, für ezie
`zetacomponents/image-conversion` hinzuzufügen.

**Empfohlen: die Anforderungen umbenennen.** Ersetzen Sie jedes `zetacomponents/<name>` durch
`se7enxweb/expcomponents-<name>`, behalten Sie die Bedeutung der Bedingung bei und aktualisieren Sie:

```bash
composer remove --no-update zetacomponents/mail zetacomponents/feed
composer require --no-update se7enxweb/expcomponents-mail:~1.10.2 se7enxweb/expcomponents-feed:~1.4.7
composer update --with-all-dependencies 'se7enxweb/expcomponents-*' 'zetacomponents/*'
```

Das sind die Bedingungen der `composer.json` von 6.0.15; verwenden Sie die Ihres Releases.

**Ebenfalls möglich: stehen lassen.** Eine Anforderung an `zetacomponents/mail` wird von `se7enxweb/expcomponents-mail`
erfüllt, weil der Fork diesen Namen ersetzt. Composer installiert den Fork (den der Kernel verlangt) und nicht das
Ursprungspaket. Zwei Fälle brauchen Sorgfalt:

- **Eine exakte Version des Originals** (`"zetacomponents/mail": "1.10.1"`) ist nur erfüllt, wenn der installierte
  Fork genau diese Version hat. Der Kernel verlangt ein späteres Fork-Release (die Releases des Forks gehen nach dem
  des Originals weiter), daher meldet Composer, dass sich die Anforderungen nicht auflösen lassen. Lockern Sie sie auf
  `~1.10.1` oder benennen Sie sie um.
- **Eine Bedingung oberhalb der Linie des Forks** (etwa ein künftiges `zetacomponents/mail ^2.0`) kann ein 1.x-Fork
  nicht erfüllen. Benennen Sie sie um und folgen Sie den Releases des Forks.

## Erweiterungen

Keine mit Exponential ausgelieferte Erweiterung verlangt in ihrer `composer.json` ein Paket `zetacomponents/*`. Für
Ihre eigenen:

- eine Erweiterung, die `zetacomponents/<name>` verlangt, lässt sich weiter installieren: Der Kernel bringt den Fork
  mit, und der Fork ersetzt diesen Namen;
- verlangen Sie in ihrem nächsten Release stattdessen `se7enxweb/expcomponents-<name>`, mit der Bedingung, die Sie
  brauchen;
- muss sich die Erweiterung auch mit Exponential 6.0.14 oder älter installieren lassen, die die Ursprungspakete
  verlangen, behalten Sie vorerst den Ursprungsnamen: Auf diesen Releases wird nicht der Fork installiert, sondern das
  Ursprungspaket;
- `extension.xml` und `ezinfo.php` nennen keine Bibliotheken und brauchen keine Änderung.

Eine Erweiterung, die eine Kopie einer Zeta Component in ihrem eigenen Ordner mitbringt (alte Praxis aus der Zeit vor
Composer), sollte die Kopie entfernen und den Fork verlangen: Zwei Kopien derselben `ezc`-Klasse lassen sich nicht
zusammen laden.

## Eigener Code

- **Klassen und Methoden:** unverändert. `ezcMailComposer`, `ezcArchive::open()`, `ezcFeed`, `ezcWebdavServer`,
  `ezcConsoleInput`, `ezcBaseFile::findRecursive()` funktionieren wie zuvor.
- **Pfade:** Code darf Bibliotheksdateien nicht über ihren Pfad einbinden. Tut Ihrer das
  (`require 'vendor/zetacomponents/...'`), stellen Sie ihn auf den Autoloader von Composer um oder auf
  `vendor/se7enxweb/expcomponents-<name>/...`.
- **Tests, die nach Paketen fragen** (`InstalledVersions::isInstalled('zetacomponents/mail')`): Die Laufzeit-API von
  Composer meldet ersetzte Namen als installiert, daher liefert das weiter `true`; ein Test, der
  `vendor/zetacomponents/` auf der Platte liest, nicht.
- **Zeichenketten im User-Agent:** Der Standard-`User-Agent` der Mail-Bibliothek gehört zur Bibliothek; im Release
  1.10.2 des Forks ist er unverändert („Apache Zeta Components“). Eine Site, die `[MailSettings] UserAgent` setzt, ist
  gar nicht betroffen.
- **Mail-Adressen:** `se7enxweb/expcomponents-mail` 1.10.2 weist eine Adresse ab, die Leerraum oder eines der Zeichen
  `< > , ; "` enthält, bevor ein Header geschrieben wird; eine solche Adresse könnte den Header beenden oder Empfänger
  hinzufügen. Code, der Absender oder Empfänger aus Benutzereingaben baute und sich darauf verließ, dass die Bibliothek
  sie durchreicht, erhält jetzt eine Ausnahme; prüfen Sie die Adresse vorher (`eZMail::validate()`).

## Die Bedeutung von replace im Einzelnen

Die `composer.json` jedes Forks hat einen Abschnitt `replace` mit dem alten Namen und `self.version`:

```json
"replace": {
    "zetacomponents/mail": "self.version"
}
```

- `self.version` bedeutet „dieselbe Version wie dieses Paket“: `se7enxweb/expcomponents-mail` 1.10.2 tritt für
  `zetacomponents/mail` 1.10.2 ein.
- Composer installiert ein Paket und seinen Ersatz nie zusammen. Wird der Fork (vom Kernel) verlangt, erfüllt er jede
  Anforderung an den alten Namen, und das Ursprungspaket wird entfernt, falls es installiert war.
- Die Forks verlangen einander mit ihren Fork-Namen (der Archive-Fork verlangt `se7enxweb/expcomponents-base ~1.8`).
  Ein Paket Dritter, das noch `zetacomponents/base` verlangt, bedient der Base-Fork über sein `replace`.
- Der WebDAV-Fork ersetzt zwei Namen: `zetacomponents/webdav` und `se7enxweb/zetacomponents-webdav`, seinen eigenen
  Namen für das Release 1.2.1.
- `replace` betrifft Pakete, nicht Klassen. Es gibt kein `class_alias` und keinen zweiten Klassennamen; die Klasse ist
  einfach dieselbe.

## Das Autoloading bleibt gleich

Die Bibliotheken werden über die **Klassen-Map von Composer** geladen: Jeder Fork erklärt
`"autoload": {"classmap": ["src"]}`, wie das Ursprungspaket, und `composer install` oder `composer update` schreibt die
Map nach `vendor/composer/`. Die `autoload.php` des Kernels bindet `vendor/autoload.php` ein und registriert dann ihren
eigenen `ezpAutoloader` für die Klassen des Kernels und der Erweiterungen; `ezcBase::autoload` wird nur registriert,
wenn `EZCBASE_ENABLED` als `true` definiert ist (eine alte Einrichtung ohne Composer), was der Kernel nicht tut. Nichts
davon ändert sich. `php bin/php/ezpgenerateautoloads.php` liest `vendor/` nicht und muss vom Wechsel nichts wissen;
führen Sie es aus wie nach jedem Upgrade.

Velocity lädt die Klassen aus `vendor/` beim Aufwärmen wie den Rest; ein Neustart nach dem Update genügt
(`./console exp:velocity restart`).

## Das Ergebnis prüfen

```bash
composer show | grep expcomponents        # eine Zeile je Fork, mit seiner Version
composer show | grep zetacomponents       # sollte nichts ausgeben (in einer Entwicklungsinstallation nur Entwicklungspakete)
composer licenses --no-dev | grep expcomponents   # jeder Fork mit Apache-2.0
```

- Setup > Systeminformationen (`/setup/info`) zeigt die Serverinformationen, die aus
  `se7enxweb/expcomponents-system-information` kommen.
- `setup.ini [ezcversion]` prüft weiterhin auf die Klasse `ezcBaseFile`; die Systemprüfung des Setup-Assistenten
  besteht.
- Lassen Sie die Site eine E-Mail senden (zum Beispiel eine Passwort-Erinnerung), öffnen Sie einen RSS-Export,
  importieren Sie ein Paket und verbinden Sie, falls WebDAV genutzt wird, einen Client: Jedes davon verwendet einen der
  Forks.
- `composer why zetacomponents/base` gibt die Pakete aus, die noch den alten Namen verlangen: keines des Kernels,
  höchstens ein Paket Dritter von Ihnen. Das ist harmlos: Der Base-Fork erfüllt es.

In einer Entwicklungsinstallation (`composer install` mit Entwicklungspaketen) können `zetacomponents/unit-test` und
andere Entwicklungspakete der eigenen Testsuiten der Forks noch auftauchen; sie gehören nicht zu einer Site.

## Fehlerbehebung

| Symptom | Ursache | Lösung |
|---|---|---|
| `Your requirements could not be resolved` mit `zetacomponents/<name>` in einer exakten Version | Eine `composer.json` von Ihnen legt die Version des Originals fest | Eine Tilde-Bedingung oder den Namen des Forks verwenden ([oben](#projekte-die-selbst-zetacomponents-pakete-verlangten)) |
| `se7enxweb/expcomponents-<name> ... could not be found` | Das Release des Forks ist in der Version, die die `composer.json` verlangt, noch nicht veröffentlicht, oder der Cache von Composer ist alt | `composer clear-cache`, dann erneut versuchen; die Releases des Forks auf GitHub prüfen |
| Eine Klasse `ezc...` wird nach dem Update nicht gefunden | `vendor/` wurde aktualisiert, aber die PHP-Worker laufen noch mit der alten Klassen-Map, oder `composer install` lief nicht zu Ende | `composer install` erneut ausführen, dann PHP-FPM oder Velocity neu starten |
| Es gibt sowohl `vendor/zetacomponents/<name>` als auch `vendor/se7enxweb/expcomponents-<name>` | Eine Kopie, die eine Änderung von Hand an `vendor/` zurückgelassen hat | `composer install` ausführen; es entfernt die Ordner der Pakete, die es deinstalliert hat. Ein Ordner, den Composer nie installiert hat, bleibt: prüfen, dass `composer show` ihn nicht aufführt, dann löschen. `vendor/` nie von Hand bearbeiten |
| Eine Deprecation aus einer Datei in `vendor/se7enxweb/expcomponents-*` | Ein echtes Problem im Fork | An das Repository dieses Forks melden ([Fehler melden](../../funktionen/6.0/expcomponents.md#fehler-melden-und-mitwirken)) |

## Zurückrollen

Der Wechsel lässt sich rückgängig machen, weil die Klassen dieselben sind. Wo, hängt davon ab, wie die Installation
angelegt ist:

- **Ein Git-Klon des Kernels:** Die Anforderungen stehen in der eigenen `composer.json` des Kernels. Setzen Sie die
  Anforderungen an die Ursprungspakete mit den Bedingungen Ihres vorigen Releases wieder ein (die Liste „Vorher“
  [oben](#was-sich-in-der-composerjson-des-kernels-ändert)), anstelle der Fork-Namen, die Sie zurücknehmen wollen;
  oder stellen Sie `composer.json` und `composer.lock` aus Ihrer Sicherung wieder her.
- **Ein Composer-Projekt, das `se7enxweb/exponential` verlangt:** Das Kernel-Paket selbst verlangt die Forks, und ein
  Projekt kann eine Anforderung eines Pakets, von dem es abhängt, nicht entfernen. Zurück heißt hier: zum
  Kernel-Release zurückkehren, von dem Sie kamen (seine Bedingung in Ihrer `composer.json` oder Ihre Sicherung von
  `composer.json` und `composer.lock`).

Dann:

1. Nehmen Sie die Änderung oben vor.
2. Aktualisieren Sie die Bibliotheken und starten Sie die Worker neu:

   ```bash
   composer update --no-dev --with-all-dependencies 'se7enxweb/expcomponents-*' 'zetacomponents/*'
   php bin/php/ezcache.php --clear-all
   ```

3. Prüfen Sie wie oben, nur umgekehrt.

Was ein Zurückrollen kostet: die Korrekturen, die die Forks enthalten. Rollen Sie `se7enxweb/expcomponents-webdav` auf
einer Site, die WebDAV nutzt, **nicht** zurück: Sein Release 1.2.2 behebt Pfad-Traversal, symbolische Links aus der
Wurzel heraus, Dokumenttyp-Deklarationen in Anfragekörpern, den Digest-Vergleich und die Sperr-Token, und das
ursprüngliche `zetacomponents/webdav` 1.2 hat nichts davon. Der Archive-Fork (1.5.2 und später) wurde schon vor dem übrigen Wechsel
verlangt und sollte ebenfalls bleiben. Ein Zurückrollen ist als Weg gedacht, ein Problem einzugrenzen; melden Sie das
Problem, damit es im Fork behoben werden kann.

`./console exp:upgrade:rollback` stellt die ganze Installation aus der Sicherung des Upgrades wieder her, Bibliotheken
eingeschlossen.

## Verwandte Seiten

- [Exponential Components (expcomponents)](../../funktionen/6.0/expcomponents.md)
- [Spezifikation: die expcomponents-Pakete](../../spezifikationen/6.0/expcomponents-pakete.md)
- [Zu expcomponents beitragen](../../anleitungen/zu-expcomponents-beitragen.md)
- [Upgrade mit der Konsole](../../anleitungen/aktualisieren-mit-der-konsole.md)
- [Verhaltensänderungen vom Oktober 2026](verhaltensaenderungen-2026-10.md) (die Zeta Components, die der Kernel nicht mehr verlangt)
- [Das Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md)
