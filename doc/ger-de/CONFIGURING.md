# Exponential 6.0 konfigurieren

Diese Anleitung konfiguriert eine Installation von Exponential 6.0, sobald sie installiert ist: wo Einstellungen
liegen und welche Datei gewinnt, die Siteaccesses und ihre Adressen, Sprachen, die Datenbank, E-Mail, Caches,
Dateiablage und Sitzungen, Sicherheit, die Extensions, die Cronjobs, Debugging und Logs, das Hilfecenter und die
Website-Werkzeugleiste. Sie setzt eine funktionierende Installation aus [Exponential 6.0 installieren](INSTALL.md)
voraus. Jeder Abschnitt nennt die Einstellungen, zeigt ein funktionierendes Beispiel und verweist auf die Seite mit
allen Einzelheiten. So bleibt diese Seite die Landkarte und die tieferen Seiten bleiben die Referenz.

Jeder Befehl wird im Wurzelverzeichnis der Installation ausgeführt: dem Verzeichnis, das `index.php`, `autoload.php`,
`bin/` und `settings/` enthält. Die Befehlskonsole ist `bin/php/console`; viele Installationen legen die Verknüpfung
`./console` an (`ln -s bin/php/console console`). Wenn Sie ein Skript als `root` ausführen, ergänzen Sie
`--allow-root-user`; das Skript weist Sie darauf hin, wenn es nötig ist.

> **Ändern Sie nie die Vorgaben.** Die Dateien direkt in `settings/` (`settings/site.ini`, `settings/file.ini` und die
> übrigen) und die `settings/`-Verzeichnisse der Extensions werden bei jedem Update ersetzt. Legen Sie jede eigene
> Änderung in `settings/override/` oder `settings/siteaccess/<siteaccess>/` ab, als `.ini.append.php`-Datei. Ein Update
> behält sie dann, und die Einstellungsseite und `exp:ini` zeigen genau, woher jeder Wert kommt.

## Inhalt

1. [Der Schnellstart: eine Einstellung sicher ändern](#1-der-schnellstart-eine-einstellung-sicher-ändern)
2. [Wo Einstellungen liegen und welche Datei gewinnt](#2-wo-einstellungen-liegen-und-welche-datei-gewinnt)
3. [Siteaccesses und ihre Adressen](#3-siteaccesses-und-ihre-adressen)
4. [Sprachen](#4-sprachen)
5. [Datenbank, E-Mail, Ablage und Sitzungen](#5-datenbank-e-mail-ablage-und-sitzungen)
6. [Caches](#6-caches)
7. [Sicherheit](#7-sicherheit)
8. [Extensions](#8-extensions)
9. [Cronjobs](#9-cronjobs)
10. [Velocity, Apache und PHP-FPM: wann eine Änderung wirkt](#10-velocity-apache-und-php-fpm-wann-eine-änderung-wirkt)
11. [Debugging und Logs](#11-debugging-und-logs)
12. [Das Hilfecenter und die Website-Werkzeugleiste](#12-das-hilfecenter-und-die-website-werkzeugleiste)
13. [Fehlerbehebung](#13-fehlerbehebung)

## 1. Der Schnellstart: eine Einstellung sicher ändern

Es gibt zwei Wege, eine Einstellung zu ändern, und beide schreiben dieselben Dateien.

**In der Administration:** Öffnen Sie **Setup > INI-Einstellungen** (`settings/view`), wählen Sie eine INI-Datei und
einen Siteaccess und klicken Sie neben der Einstellung auf **Bearbeiten**. **Speichern in** bietet *Nur Siteaccess
Name* (`settings/siteaccess/<siteaccess>/`), *Jeder Siteaccess (globaler Override)* (`settings/override/`) oder eine
Extension an. Das Speichern leert den INI-Cache, sodass die nächste Anfrage den neuen Wert verwendet. Jede Einstellung
zeigt den wirksamen Wert, die Datei, aus der er kommt, und jede andere Datei, die sie setzt. Passwörter, Schlüssel und
Tokens werden verborgen. Der vollständige Rundgang: [Die Einstellungsseite](anleitungen/einstellungsseite.md).

**Von einer Shell aus:** `exp:ini` liest und schreibt dieselben Bereiche. Es ändert zeilenweise, sodass die Kommentare
und die Reihenfolge der Datei so bleiben, wie Sie sie geschrieben haben:

```bash
./console exp:ini where site.ini/SiteSettings/SiteName                  # welche Datei gewinnt, und die Kette
./console exp:ini get site.ini/SiteSettings/SiteName global
./console exp:ini set site.ini/SiteSettings/SiteName "Meine Website" global --dry-run
./console exp:ini set site.ini/SiteSettings/SiteName "Meine Website" global
./console exp:ini toggle site.ini/DebugSettings/DebugOutput siteaccess:admin   # enabled <-> disabled
./console exp:ini scopes                                                 # jeder Ort, in den Sie schreiben können
```

Die Bereiche sind `global` (`settings/override/`), `siteaccess:<sa>`, `extension:<ext>` und
`extension:<ext>:siteaccess:<sa>`. Die Vorgaben (`default`, `settings/<datei>.ini`) werden abgelehnt, außer Sie
ergänzen `--allow-default`. Die Referenz: [Einstellungen von der Kommandozeile ändern: exp:ini](funktionen/6.0/exp-ini-befehl.md)
und [console exp:ini](bc/6.0/console-exp-ini.md).

**Mit einem Texteditor:** Eine Override-Datei ist PHP, das den INI-Text in einem Kommentar versteckt, sodass ein
Browser, der sie anfordert, eine leere Seite erhält:

```ini
<?php /* #?ini charset="utf-8"?

[SiteSettings]
SiteName=Meine Website

*/ ?>
```

Leeren Sie danach den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini`. `config.php` schaltet die Prüfung der
Dateizeiten ab (`EZP_INI_FILEMTIME_CHECK`), deshalb wird eine im Editor geänderte Datei erst nach diesem Leeren neu
gelesen. Unter Velocity brauchen einige Einstellungen zusätzlich einen Neustart
([Abschnitt 10](#10-velocity-apache-und-php-fpm-wann-eine-änderung-wirkt)).

## 2. Wo Einstellungen liegen und welche Datei gewinnt

*Vollständige Anleitung: [Die Einstellungsseite](anleitungen/einstellungsseite.md)*

Jede INI-Datei wird in Schichten gelesen. Eine spätere Schicht gewinnt: Sie ersetzt einen einfachen Wert und ergänzt
ein Array, außer sie leert es zuerst.

| Reihenfolge | Schicht | Wo | Auf der Einstellungsseite angezeigt als |
|---|---|---|---|
| 1 | Die Vorgaben | `settings/<datei>.ini` | Vorgabe |
| 2 | Die aktiven Extensions | `extension/<ext>/settings/<datei>.ini.append.php` | Extension *Name* |
| 3 | Der Siteaccess | `settings/siteaccess/<siteaccess>/<datei>.ini.append.php` | Siteaccess *Name* |
| 4 | Der Siteaccess einer Extension | `extension/<ext>/settings/siteaccess/<siteaccess>/` | Extension *Name* für *Siteaccess* |
| 5 | Der globale Override | `settings/override/<datei>.ini.append.php` | Globaler Override |

Was eine Zeile mit dem Wert davor macht:

| Zeile | Was sie bewirkt |
|---|---|
| `Name=wert` | Setzt einen einfachen Wert; frühere Werte zählen nicht mehr. |
| `Name[]` | Leert das Array: Was frühere Dateien ergänzt haben, entfällt. |
| `Name[]=wert` | Hängt ein Element an. |
| `Name[schlüssel]=wert` | Setzt das Element *schlüssel* und ersetzt ein früheres mit demselben Schlüssel. |

`settings/override/` gewinnt also für jeden Siteaccess über alles. Verwenden Sie es für das, was überall gleich ist
(die Datenbank, E-Mail, die aktiven Extensions), und `settings/siteaccess/<siteaccess>/` für das, was zu einem
Siteaccess gehört (sein Design, seine Sprachen, seine Debug-Ausgabe).

Drei weitere Schichtungen, für größere Installationen:

- **Pro Umgebung:** Mit `EXP_ENV` in `config.env.php` wird eine Variante wie
  `settings/override/site.ini.test.append.php` neben der normalen Datei gelesen, sodass eine Testkopie E-Mails in
  Dateien schreiben kann, ohne dass nach einer Bereitstellung etwas bearbeitet werden muss. Siehe
  [Einstellungen pro Umgebung](funktionen/6.0/umgebungseinstellungen.md).
- **Pro Website in einer Extension:** Eine Extension, die eine Website einer Multisite-Installation enthält, kann ein
  eigenes `settings/override/` mitbringen, das nur gilt, solange einer ihrer Siteaccesses läuft. Siehe
  [Website-Einstellungen in Extensions](funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md).
- **Umgebungsvariablen** für die Werte, die je Server oder Container verschieden sind: siehe
  [Umgebungsvariablen](anleitungen/umgebungsvariablen.md).

Jede Einstellung jeder Datei ist mit ihrer Vorgabe und einem Beispiel unter
[Beispiele der INI-Einstellungen](beispiele/ini-einstellungen/README.md) beschrieben, zum Beispiel
[site.ini \[SiteSettings\]](beispiele/ini-einstellungen/site/SiteSettings.md).

## 3. Siteaccesses und ihre Adressen

*Alle Einzelheiten: [Siteaccess nach Host-Präfix und die Weiterleitung zur Sprache](funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md)*

Ein Siteaccess ist ein Gesicht der Installation, mit eigenem Design, eigenen Sprachen und Einstellungen. Eine neue
Installation hat drei, immer `site` (Besucher), `admin` (Administratoren) und `editor` (die Administration nur für
Inhalte) genannt. Welcher eine Anfrage beantwortet, entscheidet `site.ini [SiteAccessSettings]`:

| Einstellung | Bedeutung | Vorgabe |
|---|---|---|
| `AvailableSiteAccessList[]` | Die vorhandenen Siteaccesses | vom Installer gesetzt |
| `MatchOrder` | Welche Teile der Anfrage der Reihe nach geprüft werden: `uri`, `host`, `port` | `uri;host;port` |
| `URIMatchType` | Wie der Pfad verglichen wird: `element` nimmt das erste Pfadsegment als Namen, `map` verwendet `URIMatchMapItems[]` | `element` |
| `HostMatchType` | Wie der Host verglichen wird; `map` verwendet `HostMatchMapItems[]=host;siteaccess` | `map` |
| `[PortAccessSettings]` | Ein Port pro Siteaccess, zum Beispiel `8081=admin` | keine |
| `[SiteSettings] DefaultAccess` | Der Siteaccess, wenn nichts passt | `admin` |

Drei verbreitete Aufteilungen:

```ini
# settings/override/site.ini.append.php: ein Host, der Siteaccess als erstes Pfadsegment
#   https://example.com/site/...   https://example.com/admin/...
[SiteAccessSettings]
MatchOrder=uri
URIMatchType=element
```

```ini
# Ein Host pro Siteaccess: https://www.example.com, https://admin.example.com, https://edit.example.com
[SiteAccessSettings]
MatchOrder=host
HostMatchType=map
HostMatchMapItems[]
HostMatchMapItems[]=www.example.com;site
HostMatchMapItems[]=admin.example.com;admin
HostMatchMapItems[]=edit.example.com;editor
```

```ini
# Ein Port pro Siteaccess (Entwicklung, oder Velocity auf mehreren Ports)
[SiteAccessSettings]
MatchOrder=port

[PortAccessSettings]
8080=site
8081=admin
8082=editor
```

Jeder Siteaccess setzt dann seine eigene Adresse in `settings/siteaccess/<siteaccess>/site.ini.append.php`:
`[SiteSettings] SiteURL` (Host und Pfad, die Links und E-Mails verwenden, zum Beispiel `www.example.com`),
`SiteName` und `DefaultPage`. Ein Siteaccess wird durch sein Design (`[DesignSettings] SiteDesign=admin4`) und das
Administrationsmenü (`[AdminSiteAccessMenu]`) zu einer Administration. Wie die Reihenfolge der Siteaccess-Liste
gehalten wird: [Die Reihenfolge der Siteaccesses](funktionen/6.0/bestellungen-nach-siteaccess.md). Der Editor-Siteaccess:
[Der Einrichtungsassistent und der Editor-Siteaccess](funktionen/6.0/einrichtungsassistent-und-editor-siteaccess.md).

Leeren Sie nach einer Änderung der Zuordnung den INI-Cache und prüfen Sie jede Adresse. Antwortet die Administration
nicht mehr, lesen Sie die [Fehlerbehebung](#13-fehlerbehebung).

## 4. Sprachen

*Vollständige Anleitung: [Inhaltssprachen](anleitungen/inhaltssprachen.md)*

Die Sprachen werden pro Siteaccess in `site.ini [RegionalSettings]` gesetzt:

| Einstellung | Bedeutung |
|---|---|
| `Locale` | Die Sprache der Oberfläche und die Formate von Datum und Zahlen, zum Beispiel `ger-DE` |
| `ContentObjectLocale` | Die Sprache, in der neue Inhalte angelegt werden |
| `SiteLanguageList[]` | Die Sprachen, in denen Inhalte gezeigt werden, nach Vorrang: die erste vorhandene gewinnt |
| `ShowUntranslatedObjects` | `enabled` zeigt Inhalte, die keine dieser Sprachen haben, in ihrer Hauptsprache |
| `TranslationSA[]` | Der Siteaccess jeder Sprache, für den Sprachumschalter |

```ini
# settings/siteaccess/site_ger/site.ini.append.php: eine deutsche Website, die auf Englisch zurückfällt
[RegionalSettings]
Locale=ger-DE
ContentObjectLocale=ger-DE
SiteLanguageList[]
SiteLanguageList[]=ger-DE
SiteLanguageList[]=eng-GB
```

Eine Sprache wird der Installation einmal unter **Setup > Sprachen** hinzugefügt. Ihre Locale-Datei in
`share/locale/` liefert Namen, Formate und Schreibrichtung, und die Administration ist in vielen Sprachen vollständig
übersetzt. Die Liste der Locales und wie vollständig jede Übersetzung ist: [Sprachen in Exponential](sprachen.md).
Sprachen unter eigenem Pfad (`/ger`, `/eng`) und die Weiterleitung von `/` zur Sprache des Besuchers:
[Siteaccess nach Host-Präfix und die Weiterleitung zur Sprache](funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md).
Mehr: [Übersetzungen und Sprachen](funktionen/6.0/uebersetzungen-und-sprachen.md).

## 5. Datenbank, E-Mail, Ablage und Sitzungen

### Datenbank

`site.ini [DatabaseSettings]`, in `settings/override/site.ini.append.php` (der Installer schreibt sie dorthin):

```ini
[DatabaseSettings]
# ezmysqli, ezpostgresql, sqlite3, mongodb oder ezoracle (Extension ezoracle)
DatabaseImplementation=ezmysqli
Server=localhost
Port=
User=exponential
Password=
Database=exponential
Charset=utf-8
```

Halten Sie das Passwort aus der Versionsverwaltung heraus: `settings/override/` wird nie eingecheckt. Ein Wechsel der
Datenbank-Engine braucht dieselben Daten in der neuen Datenbank; siehe [Upgrade](anleitungen/aktualisieren.md) und das
[Datenbankkapitel](installation/09-datenbanken.md) des Installationsbuchs. SQLite unter Produktionslast:
[Exponential 6.0 installieren, Abschnitt 9](INSTALL.md#9-sqlite-im-produktivbetrieb). Jede Einstellung:
[\[DatabaseSettings\]](beispiele/ini-einstellungen/site/DatabaseSettings.md).

### E-Mail

`site.ini [MailSettings]` entscheidet, wie E-Mails den Server verlassen und wer sie sendet:

| Einstellung | Bedeutung |
|---|---|
| `Transport` | `sendmail` (Vorgabe), `SMTP` oder `file` (schreibt jede E-Mail nach `var/log/mail/`, statt sie zu senden, für Tests) |
| `TransportServer`, `TransportPort`, `TransportConnectionType` | Der SMTP-Server; `tls` oder `ssl` für eine verschlüsselte Verbindung |
| `TransportUser`, `TransportPassword`, `TransportAuthMethod` | SMTP-Anmeldung; leere Methode: die stärkste, die der Server anbietet |
| `AdminEmail` | Die Adresse des Administrators, für Systemmails |
| `EmailSender` | Die Absenderadresse (`From`) der E-Mails der Website; leer: `AdminEmail` |

```ini
# settings/override/site.ini.append.php
[MailSettings]
Transport=SMTP
TransportServer=smtp.example.com
TransportPort=587
TransportConnectionType=tls
TransportUser=site@example.com
TransportPassword=
AdminEmail=webmaster@example.com
EmailSender=no-reply@example.com
```

Eine Test- oder Staging-Kopie sollte `Transport=file` verwenden, am besten in einer Umgebungsvariante
([Abschnitt 2](#2-wo-einstellungen-liegen-und-welche-datei-gewinnt)), damit sie nie echten Menschen schreibt. Mehr:
[\[MailSettings\]](beispiele/ini-einstellungen/site/MailSettings.md),
[Härtung des Mailversands](bc/6.0/mail-transport-haertung.md),
[Mail-Layout](funktionen/6.0/e-mail-layout.md) und [E-Mail-Einstellungen](anleitungen/mail-einstellungen-administrator.md).

### Dateiablage und Cluster

Hochgeladene Dateien und Bilder liegen in `var/storage/` (`site.ini [FileSettings] VarDir=var`, `StorageDir=storage`),
Caches in `var/cache/`, Logs in `var/log/`. Eine Multisite-Installation kann jeder Website ein eigenes Verzeichnis
geben: [Log- und Cache-Pfade pro Website](funktionen/6.0/log-und-cache-pfade-fuer-mehrere-websites.md).

Mehrere Webserver, die eine Website teilen, teilen die Dateien über den Cluster-Datei-Handler, `file.ini
[ClusteringSettings] FileHandler`: `eZFSFileHandler` (die Vorgabe, ein Server) oder `eZDFSFileHandler` (ein gemeinsames
Laufwerk, `[eZDFSClusteringSettings] MountPointPath`, mit den Metadaten der Dateien in der Datenbank über `DBBackend`).
Jede Einstellung: [\[ClusteringSettings\]](beispiele/ini-einstellungen/file/ClusteringSettings.md).

### Sitzungen und „Angemeldet bleiben“

*Vollständige Anleitung: [Sitzungen](anleitungen/sitzungen.md)*

`site.ini [Session]`: `SessionTimeout` ist, wie lange eine Sitzung nach ihrer letzten Nutzung lebt,
`RememberMeTimeout`, wie lange der Browser das Cookie behält, wenn **Angemeldet bleiben** angehakt ist. Beide sind
standardmäßig 31104000 Sekunden (360 Tage), und das Kästchen ist auf den Anmeldeformularen vorausgewählt, weil
Browser auf Telefonen reine Sitzungs-Cookies von selbst verwerfen. Verkürzen Sie sie für eine Website mit sensiblen
Daten:

```ini
[Session]
SessionTimeout=86400
RememberMeTimeout=1209600
```

Wer angemeldet ist, und Browser abmelden: **Setup > Sitzungen** ([Sitzungen](anleitungen/sitzungen.md)); die eigenen
Geräte eines Benutzers: [Ihre Geräte und Sitzungen](anleitungen/ihre-geraete-und-sitzungen.md).

## 6. Caches

*Vollständige Anleitung: [Caches](anleitungen/caches.md)*

Die Caches sind standardmäßig an und sollten an bleiben: `site.ini [TemplateSettings] TemplateCache=enabled`
(kompilierte Templates), `[ContentSettings] ViewCaching=enabled` (gerenderte Seiten, beim Veröffentlichen geleert).
Leeren Sie den kleinsten, der das Problem behebt:

| Sie haben geändert | Befehl |
|---|---|
| eine Einstellung (`.ini`) | `php bin/php/ezcache.php --clear-tag=ini` |
| ein Template (`.tpl`) | `php bin/php/ezcache.php --clear-tag=template` |
| Inhalte wirken veraltet | `php bin/php/ezcache.php --clear-tag=content` |
| ein Template oder eine Extension hinzugefügt | `php bin/php/ezcache.php --clear-id=template-override` |
| unsicher | `php bin/php/ezcache.php --clear-all` |

Dieselben Gruppen stehen unter **Setup > Cache-Verwaltung**. Zwei weitere Schichten, beide standardmäßig aus:

- **Der statische Cache** (`[ContentSettings] StaticCache=enabled`, `settings/staticcache.ini`) schreibt fertige
  Seiten als Dateien, die der Webserver ohne PHP beantwortet:
  [Der Generator des statischen Caches](funktionen/6.0/generator-fuer-den-statischen-cache.md). Eine statische Kopie auf GitHub
  Pages oder einem beliebigen Git-Host veröffentlichen: [Statisches Veröffentlichen](funktionen/6.0/statisches-veroeffentlichen.md).
- **Der Antwort-Cache von Velocity** beantwortet wiederholte Anfragen aus dem Speicher:
  [Der Antwort-Cache von Velocity](funktionen/6.0/velocity-antwort-cache.md).

Die Caches nach einer Bereitstellung aufwärmen: [Caches vorladen](anleitungen/caches-vorwaermen.md).

## 7. Sicherheit

*Vollständige Anleitung: [Sicherheit und Audit](anleitungen/sicherheit-und-audit.md)*

### HTTPS und die Sicherheits-Header

Liefern Sie jeden Siteaccess über HTTPS aus
([Exponential 6.0 installieren, Abschnitt 8](INSTALL.md#8-die-website-ausliefern-webserver-https-und-berechtigungen)).
`site.ini [HTTPHeaderSettings] SecurityHeaders[]` sendet `X-Content-Type-Options`, `X-Frame-Options`,
`Referrer-Policy`, `Permissions-Policy` und eine frame-ancestors-Richtlinie mit jeder Seite. Sobald die ganze Website
nur noch HTTPS spricht, schalten Sie HSTS ein:

```ini
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
SecurityHeaders[Strict-Transport-Security]=max-age=31536000
```

### Die Content Security Policy

`site.ini [ContentSecurityPolicy] Mode` ist `disabled`, `report-only`, `enforce` oder `auto` (die Vorgabe:
report-only in den Administrations-Siteaccesses, keine Richtlinie auf den Websites). `Directives[]` listet, was eine
Seite laden darf. Beobachten Sie zuerst die Berichte und erzwingen Sie dann:
[Content Security Policy](funktionen/6.0/content-security-policy.md).

### Hinter einem Proxy oder Load Balancer

Weitergeleitete Header (`X-Forwarded-For`, `X-Forwarded-Proto` und die übrigen) werden nur von den Adressen in
`site.ini [HTTPHeaderSettings] TrustedProxies[]` geglaubt (standardmäßig nur von dieser Maschine). Ergänzen Sie die
Adresse oder den Bereich Ihres Proxys; für die Adresse des Besuchers setzen Sie zusätzlich
`ClientIpByCustomHTTPHeader=X-Forwarded-For`:

```ini
[HTTPHeaderSettings]
TrustedProxies[]
TrustedProxies[]=127.0.0.1
TrustedProxies[]=10.0.0.0/8
ClientIpByCustomHTTPHeader=X-Forwarded-For
```

Siehe [Vertrauenswürdige Proxys](bc/6.0/vertrauenswuerdige-proxys.md).

### Zweistufige Anmeldung und „Bestätigen Sie, dass Sie es sind“

- **Die zweistufige Anmeldung** (die Extension sevenx_authentication_2fa, von den Installern aktiviert) fragt nach dem
  Passwort einen Code aus einer Authenticator-App oder per E-Mail ab, im Anmeldeformular. WebDAV, die
  Basic-Authentifizierung der REST-API und SOAP fragen nur das Passwort ab; halten Sie sie dort aus oder begrenzt, wo
  der zweite Schritt Pflicht ist. Einschalten und verlangen: [Zweistufige Anmeldung](anleitungen/zwei-faktor-anmeldung.md).
  Passkeys: [Passkeys](anleitungen/passkeys.md).
- **„Bestätigen Sie, dass Sie es sind“** lässt einen angemeldeten Benutzer vor einer sensiblen Aktion erneut bestätigen
  (das eigene Konto, Benutzer und Rollen, Systemeinstellungen, Exporte, Löschungen). Fünf Gruppen sind standardmäßig
  an; die sechste, Inhalte, wird unter **Setup > Verify it's you** (`/setup/reauth`) eingeschaltet. Siehe
  [Bestätigen Sie, dass Sie es sind](anleitungen/identitaet-bestaetigen.md).

### Geheimnisse

Passwörter und Schlüssel, die Werkzeuge brauchen (Verbindungen von Datentabellen, Importquellen, entfernte Server),
gehören in den Geheimnisspeicher, nicht in eine INI-Datei: **Setup > Geheimnisse** (Richtlinie `secrets/manage`). Ein
Werkzeug behält nur den Namen eines Geheimnisses. Ein Name `env:<VARIABLE>` liest stattdessen eine Umgebungsvariable
des PHP-Prozesses, was zu Containern passt. Die Werte sind mit einem Schlüssel verschlüsselt, der nicht in der
Datenbank liegt (`EXP_SECRET_KEY` oder `var/secrets/exp_secret.key`): Sichern Sie den Schlüssel mit der Datenbank.
Siehe [Der Geheimnisspeicher](funktionen/6.0/geheimnisspeicher.md).

### Rollen und Richtlinien

Jeder Benutzer erhält seine Rechte über Rollen: Eine Rolle ist eine Liste von Richtlinien (Modul und Funktion, mit
Einschränkungen), die einem Benutzer oder einer Benutzergruppe zugewiesen wird, wahlweise begrenzt auf einen Teilbaum
oder eine Sektion. Beginnen Sie mit den Rollen einer neuen Installation (Anonymous, Editor, Administrator; ein
Website-Paket kann weitere ergänzen, etwa Member), kopieren Sie eine, statt sie zu ändern, und vergeben Sie die
kleinste Menge, die funktioniert. Rundgang: [Rollen und Richtlinien](anleitungen/rollen-und-richtlinien.md). Jede Änderung wird
im [Audit-Protokoll](funktionen/6.0/pruefprotokoll.md) festgehalten.

## 8. Extensions

*Vollständige Anleitung: [Die Extensions-Seite](anleitungen/extensions-seite.md)*

Eine Extension ist aktiv, wenn sie in `site.ini [ExtensionSettings] ActiveExtensions[]` (jeder Siteaccess, in
`settings/override/`) oder `ActiveAccessExtensions[]` (ein Siteaccess, in `settings/siteaccess/<siteaccess>/`) steht.
Die Reihenfolge der Liste ist die Ladereihenfolge: Templates und Einstellungen einer späteren Extension gewinnen.

- **Setup > Extensions** (`/setup/extensions`) aktiviert, deaktiviert und ordnet sie, prüft ihre Abhängigkeiten und
  schreibt die Liste für Sie.
- Von einer Shell aus: `./console exp:ini add site.ini/ExtensionSettings/ActiveExtensions[] meineextension global`.
- Erzeugen Sie nach dem Aktivieren die Autoloads neu (`php bin/php/ezpgenerateautoloads.php -e`) und leeren Sie die
  Caches (oder `./console exp:velocity deploy`).

Eine Extension, die `share/db_schema.dba` mitbringt, erhält ihre Tabellen, wenn sie während der Installation aktiviert
wird. In einer bestehenden Installation legt der Upgrade-Schritt `6.0.15-active-extension-tables`
(`./console exp:upgrade`) die fehlenden Tabellen jeder aktiven Extension an; nichts wird gelöscht. Welche Extensions
eine neue Installation von 6.0.15 hat und einschaltet und woher ihre Tabellen kommen:
[Extensions einer neuen Installation](funktionen/6.0/erweiterungen-einer-neuen-installation.md). Die Ladereihenfolge im
Einzelnen: [Ladereihenfolge der Extensions](funktionen/6.0/ladereihenfolge-der-extensions.md). Eigene bauen:
[Extensions](anleitungen/extensions.md).

## 9. Cronjobs

*Vollständige Anleitung: [Cronjobs](anleitungen/cronjobs.md)*

Die Cronjobs veröffentlichen geplante Inhalte, senden Benachrichtigungen und Newsletter und räumen auf. Sie sind in
`cronjob.ini` in Teile gruppiert (`[CronjobSettings] Scripts[]` ist der globale Teil, `[CronjobPart-<name>]` die
übrigen). Listen Sie sie auf und tragen Sie sie in die Crontab des Website-Benutzers ein, mit Ihrem öffentlichen
Siteaccess:

```bash
php runcronjobs.php --list
```

```
*/5 * * * * cd /pfad/zur/installation && php runcronjobs.php --siteaccess=site frequent >> var/log/cron-frequent.log 2>&1
17 * * * *  cd /pfad/zur/installation && php runcronjobs.php --siteaccess=site infrequent >> var/log/cron-infrequent.log 2>&1
```

**Setup > Cronjobs** führt einen Teil aus und zeigt seine Ausgabe und seinen letzten Lauf. Der
[Planer](funktionen/6.0/velocity-zeitplaner.md) von Velocity kann sie ohne den System-Cron ausführen. Einen Teil
pausieren: [Cronjobs pausieren](funktionen/6.0/cronjobs-pausieren.md); von der Konsole:
[Cronjobs von der Konsole](funktionen/6.0/cronjobs-in-der-konsole.md).

## 10. Velocity, Apache und PHP-FPM: wann eine Änderung wirkt

*Alle Einzelheiten: [Die Einstellungsseite, „Wann die Änderung wirksam wird“](anleitungen/einstellungsseite.md#wann-die-änderung-wirksam-wird)*

Die Einstellungen sind für beide Server dieselben; verschieden ist, wann eine Änderung verwendet wird.

| Änderung | Apache mit PHP-FPM | Exponential Velocity |
|---|---|---|
| Eine INI-Einstellung, auf der Einstellungsseite oder mit `exp:ini` gespeichert | die nächste Anfrage | die nächste Anfrage |
| Eine INI-Datei, im Editor oder durch `git pull` geändert | nach `--clear-tag=ini` | nach `--clear-tag=ini` |
| `velocity.ini`, `site.ini [DatabaseSettings]`, `[ExtensionSettings]`, `[FileSettings] VarDir`, `file.ini [ClusteringSettings]` | nach `--clear-tag=ini` | nach `./console exp:velocity restart` |
| Eine PHP-Klasse oder eine aktivierte Extension | PHP-FPM neu laden | Velocity neu starten |

Die Einstellungen, die einen Neustart von Velocity brauchen, stehen in `site.ini [SettingsViewSettings]
RestartSettingList[]`; die Einstellungsseite kennzeichnet sie mit **Velocity-Neustart**. Nach einer Bereitstellung von
Code oder Einstellungen führt ein Befehl jeden Schritt in der richtigen Reihenfolge aus (Autoloads, Caches, das
Neuladen von FPM, der Neustart von Velocity): `./console exp:velocity deploy`. Die eigene Konfiguration von Velocity:
[Der Webserver Velocity](funktionen/6.0/velocity-webserver.md); seine HTTPS-Zertifikate:
[HTTPS-Zertifikate von Velocity](funktionen/6.0/velocity-https-zertifikate.md).

## 11. Debugging und Logs

*Alle Einzelheiten: [Die Debug-Leiste](bc/6.0/debug-leiste.md)*

Die Debug-Ausgabe ist standardmäßig aus und sollte für Besucher aus bleiben. Schalten Sie sie nur für Ihre eigene
Adresse ein:

```ini
# settings/override/site.ini.append.php
[DebugSettings]
DebugOutput=enabled
DebugByIP=enabled
DebugIPList[]
DebugIPList[]=192.0.2.10
```

Oder für einen Siteaccess, während Sie daran arbeiten:
`./console exp:ini toggle site.ini/DebugSettings/DebugOutput siteaccess:admin`. `[TemplateSettings] Debug=enabled`
kennzeichnet die Templates, die die Seite geschrieben haben, mit HTML-Kommentaren (auch in E-Mails, verwenden Sie es
also nur auf einer Entwicklungskopie). Jede Debug-Einstellung:
[\[DebugSettings\]](beispiele/ini-einstellungen/site/DebugSettings.md).

Die Logs liegen in `var/log/` (mit Log-Verzeichnissen pro Website in `var/<site>/log/`): `error.log`, `warning.log`,
`notice.log`, `debug.log` sowie die Cron- und Mail-Logs. Lesen Sie sie in der Administration unter
**Setup > Logdateien** ([Der Log-Betrachter](funktionen/6.0/log-betrachter.md)); sie werden rotiert, wenn eines
`[FileSettings] LogFileMaxSize` überschreitet ([Log-Rotation](funktionen/6.0/log-rotation.md)). Eine Fehlerseite nennt
eine Referenz: [Einen Fehler über seine Referenz finden](anleitungen/fehler-anhand-der-referenz-finden.md).

## 12. Das Hilfecenter und die Website-Werkzeugleiste

**Das Hilfecenter** (`exphelp`) zeigt diese Dokumentation in der Administration, in der Sprache des Lesers, wenn es
eine Übersetzung gibt. Seine Einstellungen stehen in `settings/exphelp.ini`: die Sprachen der Dokumentation
(`[DocLanguages]`), was die Suche gewichtet (`[SearchSettings]`), die Reihenfolge der Seiten im Baum
(`[TreeSettings] PageOrder[]`) und die Einstiegsseiten pro Zielgruppe. Die Richtlinie `exphelp/read` öffnet es. Siehe
[Das Hilfesystem](funktionen/6.0/hilfesystem.md) und [Hilfeinhalte](funktionen/6.0/hilfeinhalte.md).

**Die Website-Werkzeugleiste** (`expwt`) ist die Seitenleiste, mit der Sie die Website auf ihren eigenen Seiten
bearbeiten: bearbeiten, anlegen, verschieben und veröffentlichen, wo Sie lesen. Sie wird in
`expwt.ini [ExpwtSettings]` eingestellt:

| Einstellung | Bedeutung | Ausgeliefert |
|---|---|---|
| `Enabled` | Die Seitenleiste auf den Seiten eines Siteaccess | `disabled`; die Installer setzen `enabled` |
| `ShowInAdmin` | Die Seitenleiste auch in den Administrations-Siteaccesses | `disabled` |
| `ShowToAnonymous` | Nicht angemeldete Besucher: `disabled`, `enabled` oder `policy` | `disabled` |
| `SiteAccessList[]`, `SiteAccessExcludeList[]` | Nur diese Siteaccesses, und nie diese | alle, wo eingeschaltet |

Wer sie sieht, entscheiden die Richtlinien `expwt/*`: Eine neue Installation gibt sie nur den Rollen Administrator
und Editor, nie Anonymous. Eine Installation, die noch die alte Werkzeugleiste ezwt verwendet, wechselt mit
`./console exp:wt:migrate-from-ezwt --apply`. Siehe
[Extensions einer neuen Installation, „ezwt und expwt“](funktionen/6.0/erweiterungen-einer-neuen-installation.md#ezwt-und-expwt).

Weiter geht es mit den Anleitungen:

- [Betrieb einer Website](anleitungen/betrieb-einer-website.md): Caches, Cronjobs, Backups, Logs und Reparaturen.
- [Bereitstellung](anleitungen/bereitstellung.md): die Website mit Apache und PHP-FPM, Velocity oder FrankenPHP über HTTPS ausliefern.
- [Sicherheit und Audit](anleitungen/sicherheit-und-audit.md): die Härtung prüfen, Rollen und Richtlinien, das Audit-Protokoll.
- [Templates und Design](anleitungen/templates-und-design.md): das Template finden, das eine Seite geschrieben hat, es überschreiben, das Admin-Design wechseln.
- [Exponential 6.0 aktualisieren](UPDATE.md): die Installation auf den neuesten Stand bringen und diese Einstellungen behalten.
- Jede Anleitung: [der Lernpfad](anleitungen/README.md).

## 13. Fehlerbehebung

| Symptom | Ursache und Abhilfe |
|---|---|
| Eine geänderte Einstellung wirkt nicht | Der INI-Cache hält noch den alten Wert: `php bin/php/ezcache.php --clear-tag=ini`. Prüfen Sie mit `./console exp:ini where <datei>/<block>/<einstellung>`, welche Datei gewinnt: Eine spätere Schicht (oft `settings/override/`) setzt sie womöglich erneut. |
| Die Einstellungsseite meldet **noch nicht wirksam** | Die Datei wurde außerhalb der Seite geändert und der Cache nicht geleert. Leeren Sie den INI-Cache (**Setup > Cache-Verwaltung**, INI-Caches). |
| Eine Änderung wirkt auf Port 443, aber nicht unter Velocity | Die Einstellung wird nur beim Start von Velocity gelesen (Kennzeichen **Velocity-Neustart**): `./console exp:velocity restart`. |
| Ein Array hat seine vorgegebenen Elemente verloren | Der Override beginnt mit `Name[]`, das das Array leert. Lassen Sie die Zeile weg, um die Vorgaben zu ergänzen. |
| Nach einer Änderung von `MatchOrder` antwortet die Administration nicht | Keine Prüfung passt zu ihrer Adresse. Verwenden Sie den Port oder den alten Pfad, oder korrigieren Sie `settings/override/site.ini.append.php` in einer Shell und leeren Sie den INI-Cache. |
| Links und E-Mails zeigen auf den falschen Host | `[SiteSettings] SiteURL` dieses Siteaccess hat noch den Wert des Installers. |
| E-Mails werden nicht gesendet | `Transport=file` ist in einem Override oder einer Umgebungsvariante gesetzt (die E-Mails landen in `var/log/mail/`), oder der SMTP-Server lehnt die Anmeldung ab: Lesen Sie `var/log/error.log`. |
| Eine aktivierte Extension tut nichts | Autoloads nicht neu erzeugt oder ihre Tabellen fehlen: `php bin/php/ezpgenerateautoloads.php -e`, dann `./console exp:upgrade` für die Tabellen, dann die Caches leeren. |
| Hinter einem Proxy hat jeder Besucher die Adresse des Proxys | Der Proxy steht nicht in `TrustedProxies[]`, oder `ClientIpByCustomHTTPHeader` ist nicht gesetzt. |
| Der Browser blockiert Skripte, nachdem die CSP auf `enforce` steht | Eine Direktive fehlt; gehen Sie zurück auf `report-only` und lesen Sie zuerst die Berichte. |
| Ein Geheimnis „kann hier nicht entschlüsselt werden“ | Dieser Server hat einen anderen Schlüssel: Kopieren Sie `var/secrets/exp_secret.key` oder setzen Sie `EXP_SECRET_KEY` auf den Schlüssel, mit dem die Geheimnisse geschrieben wurden. |

Mehr: [Eine Installation reparieren](bc/6.0/reparatur.md), [Betrieb einer Website](anleitungen/betrieb-einer-website.md).
