# Aktualisierung auf 6.0.15: der Request Shield

Exponential 6.0.15 bringt den Request Shield mit, die Extension `exp_request_shield` (request-shield 0.4.0, eine
kleine Web Application Firewall, die jede Anfrage vor dem Kernel entscheidet). Diese Seite sagt, was sich für eine
bestehende Installation ändert, die auf die Linie 6.0.15 wechselt, und was Sie selbst tun müssen.

Referenz der Funktion: [Der Request Shield](../../funktionen/6.0/request-shield.md) · Leitfaden:
[Den Request Shield einrichten](../../anleitungen/request-shield.md) · Hilfe:
[Request Shield: hier beginnen](../../hilfe/request-shield/README.md)

## Kurz gesagt

- **Nichts wird gefiltert, bevor der Hook in `config.php` steht.** Eine Aktualisierung bearbeitet nie das
  `config.php` der Website. Das Paket kommt mit `composer.json`, die Administration zeigt Setup > Request Shield, aber
  jede Anfrage erreicht die Website wie bisher, bis Sie die zwei Hook-Zeilen hinzufügen.
- Das `composer.json` im Wurzelverzeichnis verlangt `se7enxweb/exp_request_shield` (`~0.0.1.0`). `composer update`
  installiert es in `extension/exp_request_shield/`.
- `config.php-RECOMMENDED` hat einen neuen Abschnitt **REQUEST SHIELD**, der den Hook lädt, oberhalb des frühen
  Ausstiegs des HTTP-Caches. Ein daraus erstelltes `config.php` für eine **neue** Installation ist ab der ersten
  Anfrage geschützt.
- Auf einer bestehenden Installation aktivieren Sie die Extension selbst (`ActiveExtensions[]=exp_request_shield`);
  die Website-Pakete von 6.0.15 tun das für neue Installationen.
- `settings/menu.ini` führt **Request Shield** im Setup-Menü direkt nach **Logdateien**; der Link erscheint nur,
  wenn die Extension aktiv ist und der Benutzer `requestshield/read` hat.
- Zwei neue Richtlinien, `requestshield/read` und `requestshield/manage`. Nur die Rolle Administrator hat sie.
- Unter Exponential Velocity führt `index.php` jetzt die in `EXP_REQUEST_PREPEND_FILE` genannte Datei aus; so
  erreicht der Shield jede Anfrage über Velocity.

## Inhalt

1. [Was sich ändert](#was-sich-ändert)
2. [Was zu tun ist](#was-zu-tun-ist)
3. [Der neue Eintrag im Setup-Menü und die Richtlinien](#der-neue-eintrag-im-setup-menü-und-die-richtlinien)
4. [Unter Velocity](#unter-velocity)
5. [Wenn Sie ihn nicht wollen](#wenn-sie-ihn-nicht-wollen)
6. [Prüfen nach der Aktualisierung](#prüfen-nach-der-aktualisierung)

## Was sich ändert

| Wo | Vor 6.0.15 | Ab 6.0.15 |
|---|---|---|
| `composer.json` (Wurzelverzeichnis) | kein Request Shield | verlangt `se7enxweb/exp_request_shield` `~0.0.1.0` |
| `config.php-RECOMMENDED` | kein Shield | Abschnitt **REQUEST SHIELD**: der Hook, vor dem frühen Ausstieg des HTTP-Caches, mit den Konstanten `EXP_REQUEST_SHIELD_RULES` und `EXP_REQUEST_SHIELD_DISABLED` als Kommentar |
| Das eigene `config.php` der Website | unverändert | **unverändert**: die Aktualisierung fasst es nicht an |
| `settings/menu.ini` | | `Links[request_shield]=requestshield/dashboard` in der Gruppe **System** des Setup-Menüs, nach `logs` |
| `index.php` | | führt zu Beginn jeder Anfrage die in `EXP_REQUEST_PREPEND_FILE` genannte Datei aus (für Velocity) |
| Website-Pakete `sevenx_site`, `sevenx_multisite`, `cjw_multisite` und ihre Varianten `_clean` | | aktivieren `exp_request_shield` in neuen Installationen |
| Der HTTP-Cache | | eine Anfrage, die der Shield als `allow-uncached` markiert, wird beantwortet und nie gespeichert ([Hinweise zum HTTP-Cache](httpcache.md#mit-vorgeschaltetem-request-shield)) |

`composer create-project` der Distributionen löst auf v6.0.14 auf, bis eine Version 6.0.15 getaggt ist; eine solche
neue Installation bekommt nichts davon und kann den Shield mit `composer require se7enxweb/exp_request_shield`
hinzufügen.

## Was zu tun ist

Auf einer bestehenden Installation, die auf die Linie 6.0.15 gewechselt ist:

1. Stellen Sie sicher, dass das Paket da ist (`extension/exp_request_shield/`); sonst `composer require
   se7enxweb/exp_request_shield`.
2. Aktivieren Sie es in `settings/override/site.ini.append.php`:

   ```ini
   [ExtensionSettings]
   ActiveExtensions[]=exp_request_shield
   ```

3. Erzeugen Sie die Autoloads neu und leeren Sie die Caches (`php bin/php/ezpgenerateautoloads.php -e`,
   `php bin/php/ezcache.php --clear-all`), oder `./console exp:velocity deploy --packer`, wo es den Befehl gibt.
4. Geben Sie der Website eine eigene Regeldatei und prüfen Sie sie
   ([Leitfaden, Abschnitt 2](../../anleitungen/request-shield.md#2-der-website-eigene-regeln-geben)): Die
   mitgelieferten Regeln **setzen durch**. Behalten Sie `www.example.org` in einer Host-Zeile, damit die
   mitgelieferten Beispiele bestehen.
5. Fügen Sie den Hook in `config.php` ein, oberhalb der Zeile, die `ezphttpcacheearlyexit.php` lädt:

   ```php
   if ( is_file( __DIR__ . '/extension/exp_request_shield/bootstrap/hook.php' ) )
       require __DIR__ . '/extension/exp_request_shield/bootstrap/hook.php';
   ```

   Vergleichen Sie Ihr `config.php` mit `config.php-RECOMMENDED`, um den zugehörigen Kommentar zu übernehmen.
6. Unter Velocity: `./console exp:velocity restart`.
7. Lesen Sie in den ersten Tagen Setup > Request Shield > Protokoll und fügen Sie Ausnahmen für alles hinzu, was ein echter
   Besucher oder Redakteur getan hat; schalten Sie auf monitor, während Sie nachsehen.

## Der neue Eintrag im Setup-Menü und die Richtlinien

| Richtlinie | Erlaubt |
|---|---|
| `requestshield/read` | die Übersicht, das Log und die Seite der Regeln (Setup > Request Shield) |
| `requestshield/manage` | die Schalter auf der Übersicht: pausieren und einschalten, enforce, monitor, off |

Nach der Aktualisierung hat nur die Rolle Administrator, die jede Richtlinie hat, diese beiden. Geben Sie sie unter
**Rollen und Richtlinien** einer anderen Rolle, wenn jemand anderes den Shield sehen oder schalten soll. Benutzer ohne
`requestshield/read` sehen den Menüeintrag nicht.

Die Seiten gibt es in admin4 und admin4l, im Design adminui von exp_adminui und im klassischen Admin-Design, jeweils
hell und dunkel. Ihr Stylesheet packt ezjscore: Leeren Sie nach der Aktualisierung die Caches `ezjscore-packer` und
`template-block` (Teil von `--clear-all`), damit die Seiten gestaltet erscheinen.

## Unter Velocity

- Der Shield braucht das `index.php` von 6.0.15, das `EXP_REQUEST_PREPEND_FILE` ausführt. Der Hook definiert diese
  Konstante unter Velocity; ein älteres `index.php` führt sie nie aus, sodass die Anfragen über Velocity dann nicht
  gefiltert werden, die über PHP-FPM schon.
- Starten Sie Velocity neu, nachdem Sie den Hook hinzugefügt oder entfernt haben. Regeländerungen brauchen keinen
  Neustart.
- Wenn PHP-FPM und Velocity dieselbe Website ausliefern, lässt `set store file` in der Regeldatei beide die Zähler
  teilen.
- Die Antworten sind identisch mit denen unter PHP-FPM.
- `EXP_REQUEST_PREPEND_FILE` nennt eine einzige Datei. Der Hook definiert die Konstante nur, wenn sie noch nicht
  definiert ist: Nennt Ihr `config.php` dort schon eine eigene Datei, läuft der Shield unter Velocity nicht. Binden
  Sie dann `extension/exp_request_shield/bootstrap/prepend.php` am Anfang Ihrer eigenen Datei mit `require` ein.

## Wenn Sie ihn nicht wollen

Lassen Sie den Hook aus `config.php` weg: Dann wird nichts gefiltert. Um den Code zu behalten, aber die Seiten der
Administration auszublenden, aktivieren Sie die Extension nicht. Auf einer Installation, deren `composer.json` im
Wurzelverzeichnis das Paket verlangt, lassen Sie es installiert, statt die Anforderung zu entfernen, damit das
nächste `composer update` nicht dagegen arbeitet.

Um einen installierten Shield sofort auszuschalten: Legen Sie `var/request-shield/PAUSED` an, oder definieren Sie
`EXP_REQUEST_SHIELD_DISABLED` vor der Hook-Zeile als `true`.

## Prüfen nach der Aktualisierung

- Setup > Request Shield zeigt den Modus, die Hauptregeldatei und keinen Hinweis, dass der Shield für diese Seite
  nicht gelaufen ist.
- `php extension/exp_request_shield/bin/request-shield check settings/request-shield/request-shield.rules` und
  `test` bestehen, als Webbenutzer ausgeführt.
- `curl -sI https://<Ihr Host>/settings/site.ini` antwortet mit 404, und `var/request-shield/log/shield.log` enthält
  die Zeile.
- Die Startseite, die Anmeldung und eine Bearbeitung in der Administration funktionieren wie bisher (die
  Administration verlangt einmal pro Pass die Browserprüfung).
