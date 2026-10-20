# Spezifikation: expservices (Remote-Dienste über ezjscore)

Diese Seite ist die Referenz für die Extension `expservices` (Version 0.1.0, eingeführt am 2026-10-02): wie ein
Remote-Dienst deklariert, aufgerufen, geschützt und im Audit erfasst wird, ihre Klassen, Einstellungen und
Erweiterungspunkte. Lesen Sie sie, wenn Sie die Dienste aus einem Skript oder einem anderen System aufrufen oder eine
eigene Dienstdomäne hinzufügen. Die Einführung für Anwender ist [Remote-Dienste](../../../eng-us/features/6.0/remote-services-expservices.md);
die vollständigen Tabellen je Domäne stehen in [ezjscore-Backend-Dienste](../../../eng-us/bc/6.0/backend_ezjscore_services.md).

## Kurz gesagt

- Jeder Aufruf ist ein ezjscore-Aufruf: `<root>/ezjscore/call/<block>::<method>[::<arg>...]`.
- Antworten haben die Form `{ok, data, meta}` oder bei einem Fehler `{ok:false, error:{code,message}}`.
- Lesezugriffe folgen der Regel `access` des Dienstes; Schreibzugriffe brauchen POST, die Richtlinie und ein Formular-Token oder ein API-Token.
- Auflisten, was es gibt: `expservices::catalog`.

## Beispiel: die Dienstdomänen auflisten

Die Erkundungsdienste sind öffentlich. Ersetzen Sie den Host:

```bash
curl -s 'https://<your-host>/ezjscore/call/expservices::domains'
```

Die Antwort ist die Hülle `{ok, data, meta}` mit der Liste der Domänen in `data`. Fahren Sie mit
`expservices::catalog::<domain>` für die Dienste einer Domäne und mit `expservices::service::<domain>::<method>`
für einen einzelnen Dienst fort.

## Architektur

| Element | Definition |
|---|---|
| Transport | ezjscore: `<root>/ezjscore/call/<block>::<method>[::<arg>...]`. Der Router (`ezjscServerRouter`) lehnt eine Klasse ohne Block `[ezjscServer_<name>]` ab und übergibt jede Klasse, die `expServiceBase` erweitert, an `expServiceBase::invoke()` |
| Domäne | Eine Klasse `exp<Domain>Services`, die `expServiceBase` erweitert, registriert in `extension/expservices/settings/ezjscore.ini.append.php` als `[ezjscServer_exp<domain>]` mit `Class=` |
| Dienst | Eine öffentliche statische Methode plus ein Eintrag in `public static $services` der Klasse (`summary`, `access`, `write`, `args`, `returns`) |
| Zugriff | `public`, `user` oder `array( module, function )`. Antwortet mit 401 ohne Anmeldung und mit 403 ohne die Richtlinie |
| Hülle | `{ok, data, meta}` oder `{ok:false, error:{code,message}}`; Codes 400, 401, 403, 404, 409, 422, 500 |
| Schreibzugriffe | POST; ein Formular-Token (`ezxform_token` oder `X-CSRF-Token`), sofern nicht ein API-Token die Anfrage signiert; die Richtlinie; Audit-Ereignis `service.<domain>.<method>` |
| Tokens | Tabelle `expservices_token` (SQL in `sql/{mysql,postgresql,sqlite}/schema.sql`). Nur der SHA-256-Hash wird gespeichert; Präfix `expt_`; gültig 1 bis 365 Tage |
| Audit | Taxonomie-Zweig `expServicesAuditBranch`, registriert in `settings/audit.ini.append.php` der Extension |

## Klassen

Kernklassen in `extension/expservices/classes/`:

`expServiceBase`, `expServicesCatalog`, `expSessionServices`, `expSystemServices`, `expIniServices`,
`expCacheServices`, `expCronjobServices`, `expExtensionServices`, `expPackageServices`, `expWorkflowServices`,
`expVelocityServices`, `expRadServices`, `expDebugServices`, `expEditorServices`, `expServiceToken`.

Die anderen Domänen liegen in Unterverzeichnissen: `content/` (fünfzehn Klassen über einer gemeinsamen Basis mit den
Zugriffsprüfungen, den Exportern und der Weiterleitung zwischen „jetzt ausführen“ und „als Job ausführen“), `users/`,
`commerce/`, `community/`, `media/` und `misc/`.

Hilfsmethoden der Basisklasse: `arg()`, `post()`, `paging()`, `pageOf()`, `ok()`, `page()`, `guard()`, `audit()`,
`node()`, `can()`.

## Erkundungsdienste (öffentlich)

| Aufruf | Rückgabe |
|---|---|
| `expservices::domains` | Die Domänen, jede mit ihrer Klasse und der Anzahl ihrer Dienste |
| `expservices::catalog[::<domain>]` | Jeder Dienst (Domäne, Methode, Zusammenfassung, Zugriff, Schreiben, Argumente, Rückgabe), aller Domänen oder einer einzelnen |
| `expservices::schema::<domain>` | Die Dienste einer Domäne |
| `expservices::service::<domain>::<method>` | Die Beschreibung eines Dienstes |
| `expservices::version` | Die Version von expservices, die Version der Hülle und die Version von Exponential |

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `extension/expservices/settings/expservices.ini` | `Services` | `Enabled` | `enabled` | Installation |
| dieselbe | `Paging` | `DefaultLimit`, `MaxLimit` | `25`, `200` | Installation |
| dieselbe | `Writes` | `RequireToken`, `Audit` | `enabled`, `enabled` | Installation |
| dieselbe | `Community` | Klassenbezeichner (siehe die Funktionsseite) | `forum`, `forum_topic`, ... | Installation |
| `extension/expservices/settings/expportal.ini` | `Portal` | `SiteTitle`, `NewsNode`, `ShopNode`, `ForumsNode`, `MediaNode`, `PageSize` | `Exponential Portal`, `2`, `2`, `2`, `43`, `12` | Installation |

## Erweiterungspunkte

- **Eine neue Domäne**: Schreiben Sie eine Klasse, die `expServiceBase` erweitert, fügen Sie der
  `ezjscore.ini.append.php` einer Extension einen Block hinzu und erzeugen Sie dann die Autoloads neu:

  ```bash
  php bin/php/ezpgenerateautoloads.php -e
  ```

- **RAD-Übersicht**: **Setup > RAD** (`setup/rad`) liest das statische `$services` jeder solchen Klasse und führt jeden
  deklarierten Dienst im Register „Remote services (expservices)“ unter dem Namen `<function>::<service>` auf. Ein
  deklarierter Dienst, dessen Methode fehlt, wird als defekt angezeigt. Auf der Referenzinstallation stieg die Gesamtzahl
  der Übersicht von 2.799 auf 3.921 Punkte (1.122 Dienste).

## Hinweise

- `expvat::removeRule` und `removeType` entfernen eine Regel selbst, weil `eZVatRule::removeVatRule()` eine
  Instanzmethode statisch aufrief, was PHP 8 verweigert. Die Kernel-Methode wurde am selben Tag korrigiert, sodass sich
  eine Mehrwertsteuerregel in der Administration wieder entfernen lässt.
- Tests: Unit-Tests der Basis, des Katalogs und der Kerndienste sowie Tests gegen die laufende Datenbank je Domäne.
  Testinhalte werden unter der Media-Wurzel angelegt und in `tearDown` entfernt; Warenkörbe verwenden ausschließlich eine
  Testsitzung; es wird keine Bestellung und keine Zahlung angelegt.
- Kommandozeilen-Clients: `extension/expservices/bin/expservices-client.sh` und `expservices_client.py`. Beide senden
  die Token-Header.

## Verwandte Seiten

- [Remote-Dienste](../../../eng-us/features/6.0/remote-services-expservices.md), [ezjscore-Backend-Dienste](../../../eng-us/bc/6.0/backend_ezjscore_services.md), [ezjscore](../../../eng-us/features/6.0/extensions/ezjscore.md)
- [Audit-Ereignismodell](audit-ereignismodell.md), [RAD-Werkzeuge](../../../eng-us/features/6.0/rad-tools.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md), [Chronik Oktober 2026](../../../eng-us/history/2026/2026-10.md)
