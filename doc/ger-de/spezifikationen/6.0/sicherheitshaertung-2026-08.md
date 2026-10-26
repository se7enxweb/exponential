# Spezifikation: die Sicherheitspatches vom August 2026

Diese Seite beschreibt die Sicherheitspatches vom 16. August 2026 für die Linie 6.0.15: sechs behobene Befunde, das
aufgefrischte Integritätsmanifest und das ersetzte mitgelieferte jQuery. Lesen Sie sie, bevor Sie eine Installation
aktualisieren, die Benutzer hat, die außerhalb der Administration angelegt wurden (per SQL, Pakete oder Migrationen), und
wenn Sie Extensions schreiben, die Tokens erzeugen oder externe Werkzeuge aufrufen. Frühere Härtungen sind in
[der Härtung von 6.0.13](sicherheitshaertung-6.0.13.md) beschrieben.

Commits: Befunde `fa808e5a39`, `dec6c49a68`, `233a6d8c35`, `4a43d5df33`, `b0fdcbb806`, `9d36acf523`; Integritätsmanifest
`d889a8abc6`; jQuery `f7c57376aa`.

## Was Sie tun müssen

1. Aktualisieren Sie auf die Linie 6.0.15. Alle Korrekturen behalten ihre Schnittstellen.
2. **Prüfen Sie, ob sich jedes Konto noch anmelden kann** (Befund F-06). Ein Konto ohne Zeile in `ezuser_setting`
   wird bei der Anmeldung jetzt als deaktiviert behandelt. In der Administration angelegte Konten haben die Zeile immer.
   Per SQL, Paket oder Migration angelegte Konten haben sie möglicherweise nicht. [Finden Sie sie mit dieser Abfrage](#f-06-konten-finden-die-sich-nicht-anmelden-können).
3. Bestehende REST-/OAuth-Tokens und Aktivierungslinks funktionieren weiter. Nur neu ausgegebene verwenden den neuen Generator.
4. Wenn Sie Skripte von einem externen CDN laden, stellen Sie diese Einstellungen auf `https://` um (die mitgelieferte Datei ist es bereits).

## Die Befunde

| ID | Klasse | CWE | Datei | Korrektur |
|---|---|---|---|---|
| F-01 | Vorhersagbare REST-/OAuth-Tokens | 330, 338 | `kernel/private/rest/classes/models/ezprest_token.php` | `ezpRestToken::generateToken()` gibt `bin2hex( random_bytes( 20 ) )` zurück: 40 Hexadezimalzeichen aus der sicheren Zufallsquelle des Systems. Vorher war es ein Token aus 8 Zeichen aus einem geseedeten `mt_rand()`. Der Parameter `$vary` bleibt aus Kompatibilitätsgründen erhalten und wird nicht mehr verwendet |
| F-02 | Erratbarer Link zur Kontoaktivierung | 330 | `kernel/user/ezuseroperationcollection.php` | Der Aktivierungs-Hash ist `bin2hex( random_bytes( 16 ) )` statt `md5( mt_rand() . time() . $userID )` |
| F-03 | HTTP-Header-Injection (CRLF) | 93 | `extension/ezoe/classes/runnable/views/ezoe/atd_rpc.php` | `\r` und `\n` werden aus dem Parameter `url` entfernt, bevor er Teil der von Hand gebauten Anfrage an den After-the-Deadline-Server wird. Der Post-Text wird initialisiert, sodass ein GET keine undefinierte Variable mehr liest. Entfernt in 6.0.15 (16. Oktober 2026): Die View, ihre Klasse und das AtD-Plugin sind entfallen, siehe das [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#16-october-2026-online-editor-hardening) |
| F-05 | Einschleusen von Betriebssystembefehlen | 78 | `kernel/classes/datatypes/ezbinaryfile/plugins/ezpdfparser.php`, `ezwordparser.php` | Das Extraktionswerkzeug läuft durch `escapeshellcmd()`; Dateinamen, einschließlich der temporären Ausgabedatei, laufen durch `escapeshellarg()`. Ein Dateiname mit Shell-Zeichen kann keinen Befehl mehr hinzufügen |
| F-06 | Offenes Versagen bei der Anmeldung (Fail open) | 636, 697 | `kernel/classes/datatypes/ezuser/ezuser.php`, `eZUser::_loginUser()` | Bei der Anmeldung gilt ein Konto ohne Zeile in `ezuser_setting` als deaktiviert; früher galt es als aktiviert. Seit 6.0.15 (24. Oktober 2026) stimmt `eZUser::isEnabled()` zu: ein Konto ohne Zeile ist nicht aktiviert, daher funktionieren auch seine Sitzung, sein API-Schlüssel oder OAuth-Token nicht mehr, die Seiten zur Konten-Hygiene und Deaktivierung führen es als deaktiviert, und das Aktivieren (`eZUserOperationCollection::setSettings()`) legt die fehlende Zeile an. Das Deaktivieren eines Benutzers behält die Zeile mit `is_enabled = 0`, daher sind nur Konten ohne Zeile betroffen |
| H-06 | Skripte über einfaches HTTP | 829, 319 | `extension/ezjscore/settings/ezjscore.ini` | Die URLs in `ExternalScripts[...]` verwenden `https://`. Bevorzugen Sie `LoadFromCDN=disabled` (lokale Skripte) oder Subresource Integrity. Das Yahoo-CDN für YUI hat sein Lebensende erreicht und antwortet möglicherweise nicht |

Hinweis zu F-03: Die View `extension/ezoe/modules/ezoe/atd_rpc.php` ist jetzt ein Einstiegspunkt, der die oben genannte Klasse aufruft. Beide Dateien wurden in 6.0.15 zusammen mit der Rechtschreibprüfung After the Deadline entfernt.
F-04 (die Bibliothek jQuery 1.10.2) wurde durch den [Austausch von jQuery](#jquery-371-und-jquery-migrate-341) erledigt.

## F-06: Konten finden, die sich nicht anmelden können

Führen Sie diese nur lesende Abfrage in Ihrem Datenbank-Client aus. Der Aufbau der Tabelle (`user_id`, `is_enabled`, `max_login`)
steht in `kernel/sql/mysql/kernel_schema.sql`.

```sql
SELECT u.contentobject_id, u.login
FROM ezuser u
LEFT JOIN ezuser_setting s ON s.user_id = u.contentobject_id
WHERE s.user_id IS NULL;
```

Jede Zeile ist ein Konto ohne Einstellungen. Ein leeres Ergebnis bedeutet, dass sich jedes Konto anmelden kann. Damit sich ein
solches Konto anmelden kann, aktivieren Sie es in der Administration (Benutzereinstellungen oder die Seite zur
Konten-Hygiene), was seit 6.0.15 die Zeile anlegt, oder legen Sie seine Zeile an:

```sql
INSERT INTO ezuser_setting ( user_id, is_enabled, max_login ) VALUES ( <contentobject_id>, 1, 1000 );
```

`max_login` ist die erlaubte Anzahl gleichzeitiger Anmeldungen; verwenden Sie den Wert, den Ihre anderen Konten haben.
Importer für Pakete und Migrationen sollten diese Zeile anlegen, wenn sie einen Benutzer anlegen.

Seit 6.0.15 legt ein Upgrade-Skript die Zeilen aller solchen Konten auf einmal an, auf jeder Datenbank-Engine. Bis 6.0.15
galten diese Konten überall außer bei der Anmeldung als aktiviert, und das Skript gibt ihnen diesen Zustand zurück: eine
aktivierte Zeile, oder eine deaktivierte für ein Konto, das noch auf seine Aktivierungs-Mail wartet; ein Konto, dessen
Inhaltsobjekt nicht veröffentlicht ist (ein zur Freigabe zurückgehaltener Entwurf), bleibt unberührt. Vorhandene Zeilen
werden nie geändert, das Skript kann also erneut laufen:

```sh
php update/common/scripts/6.0/createmissingusersettings.php --dry-run    # listet die IDs und was jede bekäme
php update/common/scripts/6.0/createmissingusersettings.php              # legt die Zeilen an
php update/common/scripts/6.0/createmissingusersettings.php --disabled   # legt jede Zeile deaktiviert an
```

Führen Sie es vor oder direkt nach dem Update auf 6.0.15 aus: ab diesem Release ist ein Konto ohne seine Zeile nirgends
aktiviert, daher funktionieren auch seine offene Sitzung, sein API-Schlüssel oder OAuth-Token nicht mehr.

## jQuery 3.7.1 und jQuery Migrate 3.4.1

Am 16. August ersetzte `ezjscore` das mitgelieferte jQuery 1.10.2 durch 3.7.1. Die Serverfunktion `ezjsc::jquery` lädt
jetzt für jeden Aufrufer unmittelbar danach jQuery Migrate 3.4.1. Migrate stellt APIs wieder her, die jQuery 3 entfernt hat
(`live`, `delegate`, `hover`, `$.isArray`, `$.trim`), sodass TinyMCE, der Online-Editor, ezie, die Templates der
Administration und Extensions von Drittanbietern weiter funktionieren. Der minifizierte Build von Migrate ist stummgeschaltet
(`jQuery.migrateMute = true`), sodass die Browserkonsole ruhig bleibt.

Entfernte Dateien: `jquery-1.10.2.min.js`, `jquery-migrate-1.1.1.min.js`, `jquery-migrate-1.2.1.min.js` und die
Kopie `jquery-3.7.0.min.js` in `design/simple`.

Die Schlüssel, wie sie am 16. August standen:

| Datei | Block | Schlüssel | Wert am 16. August |
|---|---|---|---|
| `extension/ezjscore/settings/ezjscore.ini` | `eZJSCore` | `LocalScripts[jquery]` | die Datei der Version 3.7.1 |
| dieselbe | `eZJSCore` | `LocalScripts[jqueryMigrate]` | `jquery-migrate-3.4.1.min.js` |
| dieselbe | `eZJSCore` | `ExternalScripts[jquery]`, `ExternalScripts[jqueryMigrate]` | die `https://`-CDN-Adressen derselben Versionen |

jQuery UI blieb damals bei 1.10.3. Alle drei wurden später mit der Arbeit an jQuery 4 weiterentwickelt: Die Schlüssel zeigen
jetzt auf jQuery 4.0.0, Migrate 4.0.2 und jQuery UI 1.14.2. Siehe
[jQuery 4 und die Entfernung von YUI](../../funktionen/6.0/jquery4-und-entfernung-von-yui.md).

## Dateiintegrität

Commit `d889a8abc6` frischte `share/filelist.md5` auf, sodass die
[Prüfung der Dateikonsistenz](../../funktionen/6.0/dateikonsistenzpruefung.md) die gepatchten Dateien nicht als
geändert auflistet.

## Für Extension-Entwickler

- Bauen Sie Tokens, Aktivierungslinks oder Links zum Zurücksetzen niemals aus `mt_rand()`, `rand()`, `time()` oder deren `md5()`.
  Verwenden Sie `bin2hex( random_bytes( $n ) )`.
- Leiten Sie jeden Dateinamen und jedes Argument eines externen Werkzeugs durch `escapeshellarg()`.
- Entfernen Sie `\r` und `\n` aus jedem Wert, der Teil eines Headers oder einer Anfragezeile wird.
- Behandeln Sie eine fehlende Einstellungszeile als „nein“, niemals als „ja“.

## Verwandte Seiten

- [Release-Notes zur Sicherheitshärtung](../../bc/6.0/haertung.md), [Verhaltensänderungen von Juli und August 2026](../../bc/6.0/verhaltensaenderungen-2026-07-08.md), [RAD-Werkzeuge – Sicherheit](../../bc/6.0/rad-sicherheit.md), [content/view/full absichern](../../bc/6.0/sicherheit-von-content-view-full.md)
- [Spezifikation: die Härtung von 6.0.13](sicherheitshaertung-6.0.13.md), [Sicherheitsvorgaben vom September 2026](sicherheitsvorgaben-2026-09.md), [Härtung der Datentypen und Eingaben](datentyp-eingabehaertung.md)
- [jQuery 4 und die Entfernung von YUI](../../funktionen/6.0/jquery4-und-entfernung-von-yui.md), [Ein Benutzerpasswort zurücksetzen](../../funktionen/6.0/benutzerpasswort-zuruecksetzen.md), [Anfrageregeln](../../funktionen/6.0/anfrageregeln.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md), Monatsseiten [August 2026](../../geschichte/2026/2026-08.md) und [Februar 2026](../../geschichte/2026/2026-02.md)
