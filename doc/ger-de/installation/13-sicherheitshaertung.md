# 13. Sicherheitshärtung für den Produktivbetrieb

Eine Standardinstallation von Exponential 6.0 ist so gebaut, dass sie ohne zusätzlichen Aufwand sicher ist: Die
Rewrite-Regeln liefern nur aufgelistete Dateien aus, Sicherheits-Header gehen mit jeder Seite hinaus, das
Sitzungs-Cookie ist `HttpOnly` und `SameSite`, die Debug-Ausgabe ist ausgeschaltet, Formulare tragen ein Token gegen
Cross-Site Request Forgery, und ein Audit-Protokoll hält fest, wer was getan hat. Dieses Kapitel geht jede dieser
Schichten durch, nennt die Einstellung, die sie steuert, und wie Sie sie prüfen, und listet auf, was ein Administrator
noch entscheiden sollte, bevor eine Site live geht: Geheimnisse in `settings/override`, der Administrations-Siteaccess,
Passwortregeln, Cookies nur über HTTPS, Dateiberechtigungen, Einwilligungen für Mails und das Aktuellhalten der
Installation. Es ist als Checkliste geschrieben, die Sie von oben nach unten abarbeiten können; eine einseitige
Zusammenfassung steht am Ende.

Für die Sicht des Angreifers, Bedrohung für Bedrohung, mit dem vorgeschalteten Request-Shield und dem, was Sie
beobachten sollten, lesen Sie [Sicherheitshärtung](../hilfe/sicherheitshaertung.md) in der Hilfe; für das
Administratorkonto [Das Administratorpasswort ändern](../hilfe/administratorpasswort-aendern.md) und
[Das Standard-Administratorpasswort](../hilfe/standard-administratorpasswort.md).

[Inhalt](README.md) · Zurück: [12. Fehlerbehebung](12-fehlerbehebung.md) · Weiter: [14. Migration von der 4.x-Linie](14-migration-von-4x.md)

---

## Inhalt dieses Kapitels

1. [Die Schichten im Überblick](#131-die-schichten-im-überblick)
2. [Was der Webserver niemals ausliefern darf](#132-was-der-webserver-niemals-ausliefern-darf)
3. [Geheimnisse in settings/override](#133-geheimnisse-in-settingsoverride)
4. [Debug-Ausgabe und Fehleranzeige](#134-debug-ausgabe-und-fehleranzeige)
5. [Der Administrations-Siteaccess](#135-der-administrations-siteaccess)
6. [Passwörter und Anmeldung](#136-passwörter-und-anmeldung)
7. [Sitzungen und Cookies](#137-sitzungen-und-cookies)
8. [Formular-Tokens gegen Cross-Site Request Forgery](#138-formular-tokens-gegen-cross-site-request-forgery)
9. [Sicherheits-Header und HTTPS](#139-sicherheits-header-und-https)
10. [Hinter einem Proxy: vertrauenswürdige Header](#1310-hinter-einem-proxy-vertrauenswürdige-header)
11. [Dateiberechtigungen und Eigentümer](#1311-dateiberechtigungen-und-eigentümer)
12. [Velocity-spezifische Härtung](#1312-velocity-spezifische-härtung)
13. [Mail: Einwilligung und Sperrliste](#1313-mail-einwilligung-und-sperrliste)
14. [Das Audit-Protokoll](#1314-das-audit-protokoll)
15. [Auf dem neuesten Stand bleiben](#1315-auf-dem-neuesten-stand-bleiben)
16. [Checkliste für den Livegang](#1316-checkliste-für-den-livegang)
17. [Referenzen](#1317-referenzen)

Einstellungen werden in Overrides geändert, niemals in den mitgelieferten Dateien:
`settings/override/<file>.ini.append.php` für die gesamte Installation,
`settings/siteaccess/<siteaccess>/<file>.ini.append.php` für einen Siteaccess. Leeren Sie nach einer Änderung den
INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`), und starten Sie unter Velocity den Server neu oder führen Sie
`exp:velocity deploy` aus ([Kapitel 8](08-die-website-ausliefern.md#8314-eine-änderung-bereitstellen-expvelocity-deploy)).

---

## 13.1 Die Schichten im Überblick

```
  visitor
     |
  [ TLS, HSTS ]                        Velocity [HTTPSSettings] / Apache mod_ssl / nginx ssl     13.9
     |
  [ what may be served as a file ]     .htaccess_root / Velocity's list / derived nginx block   13.2
     |
  [ security headers ]                 site.ini [HTTPHeaderSettings] SecurityHeaders[],          13.9
     |                                 velocity.ini ResponseHeaders[]
  [ request rules, siteaccess login ]  requestrules.ini, [SiteAccessSettings] RequireUserLogin   13.5
     |
  [ session cookie, form token ]       site.ini [Session], ezformtoken                          13.7, 13.8
     |
  [ roles and policies ]               Roles and policies in the administration                 13.5
     |
  [ audit ]                            audit.ini, exp:audit                                      13.14
     |
  files on disk: owner, group, modes   [FileSettings] *Permissions, chown/chmod                 13.11
```

| Schicht | Standard | Was Sie entscheiden |
|---|---|---|
| Rewrite-Regeln | strikte Liste, PHP unterhalb des Wurzelverzeichnisses abgelehnt | beibehalten; neue Regeln nach Upgrades übernehmen |
| Geheimnisse | in `settings/override` erzeugt | Dateimodi, Backups, niemals in der Versionsverwaltung |
| Debug-Ausgabe | aus | ausgeschaltet lassen oder nach IP beschränken |
| Admin-Siteaccess | Anmeldung erforderlich | eigener Hostname, Netzwerkbeschränkung, Request-Regeln |
| Passwörter | 10 Zeichen, bcrypt | Zeichenregeln, Sperre nach fehlgeschlagenen Anmeldungen |
| Sitzungs-Cookie | `HttpOnly`, `SameSite=Lax`, `Secure` über HTTPS | `CookieSecure=true` auf einer reinen HTTPS-Site |
| Formular-Tokens | aktiv (Setup-Assistent) | `ezformtoken` aktiv lassen; eigenes AJAX anpassen |
| Weitergeleitete Header | nur von `127.0.0.1` und `::1` geglaubt | Ihre Proxys in `TrustedProxies[]` eintragen, und nichts weiter |
| Sicherheits-Header | fünf Header auf jeder Seite | HSTS, sobald nur noch HTTPS; frame-ancestors für Redakteure |
| Audit | an | Cron, Empfänger von Warnmeldungen, Schlüssel-Backup |

---

## 13.2 Was der Webserver niemals ausliefern darf

Das Document Root von Exponential ist das Installationsverzeichnis, daher liegen `settings/`, `var/`, `vendor/`, die
Kernel-Quellen und die SQLite-Datenbank darin. Die Sicherheit kommt aus dem Routing, nicht aus der
Verzeichnisstruktur: **Nur eine feste Liste von Asset-Pfaden wird als Datei ausgeliefert; alles andere wird an
`index.php` übergeben.** Die Liste ist `.htaccess_root`, und die drei Engines von Velocity setzen dieselbe Liste um
(Kapitel 8, Abschnitt 8.2).

Was die mitgelieferten Regeln garantieren (`.htaccess_root` und
`doc/eng-us/specifications/6.0/security-defaults-2026-09.md`, Abschnitt 4):

| Anfrage | Antwort | Regel |
|---|---|---|
| eine Punktdatei oder ein Punktverzeichnis an beliebiger Stelle: `/.git/config`, `/.env`, `/extension/x/.htaccess` | 404 | `RewriteRule (^|/)\.(?!well-known(/|$)) - [R=404,L]` |
| `/.well-known/...` | erreichbar (ACME-Challenges) | die Ausnahme derselben Regel |
| eine vorhandene `.php`-, `.phtml`- oder `.phar`-Datei unterhalb des Wurzelverzeichnisses: `/extension/x/foo.php`, `/var/storage/packages/.../x.php` | 404 | `RewriteRule ^.+/[^/]+\.(php[0-9]?|phtml|phar)$ - [R=404,L]` |
| `/settings/site.ini`, `/var/site/storage/original/application/x.pdf`, `/composer.json` | von `index.php` behandelt (eine Seite oder 404), niemals die Datei | nicht auf der Liste |
| Paketspeicher `var/storage/packages/` | nur Vorschaubilder (png, jpg, gif, webp; kein SVG, das Skripte enthalten kann) | `^var/storage/packages/.+\.(png|jpe?g|gif|webp)$` |
| gespeicherte Originale | nur Bilddateien unterhalb von `storage/original/image`; andere Uploads nur über `content/download`, das die Berechtigungen prüft | `^var/([^/]+/)?storage/original/image/.+\.(png|jpe?g|gif|webp|svg)$` |

Was Sie tun:

1. **Apache:** `./console exp:setup:htaccess --apply` (oder `cp .htaccess_root .htaccess`) und die Sicherheitsregeln
   darin belassen. Löschen Sie keine Regeln, damit etwas funktioniert; suchen Sie stattdessen die Ursache. Wenn Sie
   eine eigene `.htaccess` oder eigene Virtual-Host-Regeln pflegen, vergleichen Sie sie nach jedem Upgrade mit
   `.htaccess_root` und übernehmen Sie neue Regeln: `./console exp:setup:htaccess` zeigt den Diff zwischen Ihrer
   `.htaccess` und der Vorlage und endet mit 1, wenn sie abweichen, sodass eine Überwachung oder die CI eine
   Abweichung bemerkt ([.htaccess und config.php von der Konsole aus einrichten](../anleitungen/setup-dateien-von-der-konsole.md)).
2. **nginx:** Verwenden Sie den abgeleiteten Server-Block aus
   [Kapitel 8, Abschnitt 8.6](08-die-website-ausliefern.md#86-nginx-mit-php-fpm) oder eine gleichwertige Konfiguration,
   die die ersten beiden Regeln (Punktdateien, Skripte unterhalb des Wurzelverzeichnisses) vor jede statische
   Location stellt.
3. **Velocity:** Nichts zu tun; `FollowSymlinks=disabled` (der Standard) verweigert auch Dateien, die über einen
   symbolischen Link erreicht werden, der aus dem Document Root hinausführt.
4. **Liefern Sie die Installation niemals mit einer generischen Konfiguration aus**, etwa dem nginx-Muster „Datei
   ausliefern, wenn sie existiert, sonst `index.php`“ oder Caddys `php_server`: Diese liefern jede vorhandene Datei
   aus, einschließlich `settings/*.ini` und der SQLite-Datenbank.

Prüfen Sie von außen:

```bash
for p in /.git/config /.env /settings/site.ini /composer.json /kernel/classes/ezini.php /var/log/error.log; do
  printf '%-32s %s\n' "$p" "$(curl -s -o /dev/null -w '%{http_code}' https://example.com$p)"
done
```

Jede Zeile muss `404` zeigen (oder `200` mit einer Seite von Exponential, niemals den Inhalt der Datei). Sehen Sie sich
beim `.ini`-Pfad einmal den Body an: Er muss eine HTML-Seite sein, kein INI-Text.

---

## 13.3 Geheimnisse in settings/override

Mehrere Dateien in `settings/override/` enthalten Geheimnisse. Sie werden vom Installer geschrieben oder bei der
ersten Verwendung erzeugt:

| Datei | Geheimnis | Folge bei Verlust oder Offenlegung |
|---|---|---|
| `site.ini.append.php` | Datenbankbenutzer und -passwort (`[DatabaseSettings]`) | Zugriff auf die Datenbank |
| `audit.ini.append.php` | Audit-Signaturschlüssel und Pseudonymschlüssel | mit dem Schlüssel signierte Archive lassen sich nicht mehr verifizieren; Pseudonyme lassen sich umkehren |
| `mailpreferences.ini.append.php` | das Site-Geheimnis (`[SecretSettings] TokenSecret`, bei der ersten Verwendung erzeugt) | jeder Abmelde- und Präferenzlink funktioniert nicht mehr, kein Eintrag der Sperrliste greift mehr, und jeder angemeldete Benutzer wird einmal abgemeldet, weil der Passwortstempel der Sitzungen (Abschnitt 13.6) mit ihm als Schlüssel gebildet wird |
| `velocity.ini.append.php` | `[DashboardSettings] Token`, falls gesetzt | Fernzugriff auf die Kennzahlen des Servers und `/Q/phpinfo` |
| `config.env.php` im Wurzelverzeichnis | Maschineneinstellungen wie der Name der Umgebung | (von Git ignoriert) |
| `var/log/initial-admin-password` | das erzeugte erste Administratorpasswort einer Kickstart- oder Konsoleninstallation | das Administratorkonto |

Was Sie tun:

1. **Ändern Sie das erzeugte Administratorpasswort** und löschen Sie danach `var/log/initial-admin-password`; der
   Konsolen-Installer weist darauf hin, wenn er das Passwort ausgibt.
2. **Übernehmen Sie `settings/override/` niemals** in die Versionsverwaltung und kopieren Sie es nie an einen
   öffentlichen Ort. Machen Sie die Dateien nur für den Benutzer der Site lesbar, wo nichts anderes sie braucht
   (`chmod 640`, Eigentümer der Benutzer der Site oder root, Gruppe die Gruppe der Site).
3. **Sichern Sie sie**, als Geheimnisse, zusammen mit der Datenbank: Ohne die Audit- und Mailpräferenz-Dateien
   funktionieren alte Archive und Links nicht mehr.
4. **Kopieren Sie sie bei jedem Umzug** der Installation auf einen anderen Rechner mit. Eine Kopie, die ohne
   `mailpreferences.ini.append.php` gestartet wird, erzeugt bei ihrer ersten Mail oder Anmeldung ein neues
   Site-Geheimnis, und von da an passen die alten Links und Einträge der Sperrliste nicht mehr; die Datei nachträglich
   hineinzukopieren hilft für das, was in der Zwischenzeit verschickt wurde, nicht.
5. Die Administration und `exp:ini` maskieren geheime Werte, wenn sie Einstellungen anzeigen; geben Sie `setup/setup`
   (Einstellungsansichten) trotzdem nur Administratoren.

---

## 13.4 Debug-Ausgabe und Fehleranzeige

Die Debug-Ausgabe zeigt SQL, Template-Namen, Dateipfade und Zeitmessungen: nützlich in der Entwicklung, ein Geschenk
für einen Angreifer im Produktivbetrieb.

`settings/site.ini [DebugSettings]` im Auslieferungszustand:

```ini
[DebugSettings]
DebugOutput=disabled        # master switch: no debug report on pages
AlwaysLog[]=error           # errors are still written to var/<site>/log/error.log
ScriptDebugOutput=disabled  # command-line scripts
DebugByIP=disabled          # when enabled: only the addresses in DebugIPList[] get debug
DebugIPList[]
DebugByUser=disabled        # when enabled: only the users in DebugUserIDList[]
DebugRedirection=disabled
DisplayDebugWarnings=disabled
```

sowie `[TemplateSettings] Debug=disabled` (keine Template-Namen im HTML).

Behalten Sie im Produktivbetrieb `DebugOutput=disabled` bei. Wenn Sie sich ein Problem auf der Live-Site ansehen
müssen, beschränken Sie das Debugging auf Ihre eigene Adresse und entfernen Sie den Eintrag danach wieder:

```ini
# settings/override/site.ini.append.php
[DebugSettings]
DebugOutput=enabled
DebugByIP=enabled
DebugIPList[]
DebugIPList[]=203.0.113.10
DebugIPList[]=2001:db8::/48
```

Einträge können einzelne Adressen oder CIDR-Bereiche beider Adressfamilien sein; eine einfache IPv6-Adresse bedeutet
genau diese Adresse (siehe [die Debug-Leiste](../bc/6.0/debug-leiste.md), wo auch Bezeichnungen und Ablaufdaten von
Einträgen dokumentiert sind). Der Reiter **Einstellungen** der Debug-Leiste kann diese Werte ändern; er benötigt die
Richtlinie `setup/setup`.

`DebugByIP` vertraut der Adresse, die Exponential für den Besucher ermittelt. Seit 6.0.15 stammt diese Adresse nur
dann aus einem weitergeleiteten Header, wenn die Verbindung von einem Proxy in `TrustedProxies[]` kommt, und der
Header wird von rechts gelesen, sodass ein Besucher keine Adresse Ihrer Liste mehr vor den Eintrag des Proxys setzen
kann (Abschnitt 13.10). Zwei Dinge hebeln den Schutz dennoch aus: ein `TrustedProxies[]`-Bereich, der weiter gefasst
ist als Ihre Proxys (ein ganzes Providernetz, `0.0.0.0/0`), und ein Anwendungsserver, der am Proxy vorbei erreichbar
ist. Halten Sie beides eng, und denken Sie daran, dass hinter einem Proxy, dessen `REMOTE_ADDR` der Proxy selbst ist,
ein Listeneintrag für die Adresse des Proxys allen die Debug-Ausgabe zeigen würde.

**PHP selbst:** Setzen Sie in der `php.ini` von PHP-FPM `display_errors=Off`, `log_errors=On` und `expose_php=Off`.
FrankenPHP liest keine `php.ini` des Rechners; Velocity liefert dafür `display_errors=Off` und `log_errors=On` in
`[FrankenPHPSettings] IniOptions[]` mit. Die Engine `qbix` führt Kommandozeilen-PHP mit der CLI-`php.ini` des Rechners
plus `[PHPSettings] IniOptions[]` aus; fügen Sie dort dasselbe hinzu, falls Ihre CLI-`php.ini` Fehler anzeigt:

```ini
# settings/override/velocity.ini.append.php
[PHPSettings]
IniOptions[]=display_errors=Off
IniOptions[]=log_errors=On
```

(Ohne eine leere Zeile `IniOptions[]` davor werden diese an die mitgelieferte Liste angehängt.) Die eigenen
Fehlerseiten der Engine zeigen Datei, Zeile und Stacktrace eines nicht abgefangenen Fehlers nur mit ihrer Option
`debug`, die standardmäßig ausgeschaltet ist.

---

## 13.5 Der Administrations-Siteaccess

Die Administration ist ein eigener Siteaccess (meist `admin` oder der bei der Installation gewählte Name). Der
Installer schreibt `[SiteAccessSettings] RequireUserLogin=true` in dessen `site.ini.append.php`, sodass jede seiner
Seiten eine Anmeldung verlangt, und der Zugriff auf jede Funktion wird durch das Rollensystem entschieden
(`setup/*`, `content/*`, `audit/*` und so weiter).

Härtung darüber hinaus, von der stärksten zur einfachsten:

1. **Geben Sie ihr einen eigenen Hostnamen** (zum Beispiel `admin.example.com`, zugeordnet über
   `[SiteAccessSettings] HostMatchType`) statt eines Pfads unterhalb der öffentlichen Site, damit sie als Einheit
   beschränkt werden kann.
2. **Beschränken Sie das Netzwerk.** Mit Apache, auf dem Virtual Host der Administration:

   ```apache
   <Location />
       Require ip 203.0.113.0/24 2001:db8::/48
   </Location>
   ```

   oder eine vorgeschaltete HTTP-Basic-Authentifizierung (`AuthType Basic` mit `mod_auth_basic`). Mit nginx: `allow`
   und `deny`. Velocity hat keine eigene HTTP-Authentifizierung; beschränken Sie den Administrations-Host mit der
   Firewall, einem VPN oder einem vorgeschalteten Reverse Proxy, oder betreiben Sie ihn auf einer separaten
   Velocity-Instanz, die an eine interne Adresse gebunden ist.
3. **Request-Regeln** (`settings/requestrules.ini`) entscheiden, bevor eine Modulansicht läuft: anonyme Besucher von
   System-URLs wie `/content/view/full/123` fernhalten, für eine Sektion eine Anmeldung verlangen, Anfragen aus einem
   Netzwerk ablehnen. Sie sind an Rollenrichtlinien gebunden, sodass Ausnahmen im Rolleneditor festgelegt werden.
   Siehe [Request-Regeln](../bc/6.0/sicherheit-von-content-view-full.md).
4. **Minimale Rechte.** Geben Sie Redakteuren die Rollen, die sie brauchen, nicht Administrator. Richtlinien, die für
   die Sicherheit wichtig sind: `setup/setup` (Einstellungen), `setup/managecache`, `audit/read` und `audit/manage`,
   `mailpreferences/administrate` und `mailpreferences/export`, `contentfinder/search` und `contentfinder/delete`
   (der [Content Finder](../funktionen/6.0/inhalts-finder.md): Inhalte nach ID oder Pfad finden und von dort in den
   Papierkorb verschieben oder löschen), `user/login` je Siteaccess.
5. **Der Setup-Assistent muss geschlossen bleiben.** Nach der Installation enthält
   `settings/override/site.ini.append.php` den Eintrag `[SiteAccessSettings] CheckValidity=false`. Solange er `true`
   ist (der mitgelieferte Standard), wird jede Anfrage an den Setup-Assistenten geschickt. Setzen Sie ihn auf einer
   Live-Site niemals zurück.

---

## 13.6 Passwörter und Anmeldung

### Längen- und Zeichenregeln

`settings/site.ini`:

```ini
[UserSettings]
MinPasswordLength=10          # bytes; 3 when the setting is missing
GeneratePasswordLength=16
WeakPasswordCheck=enabled     # refuse well-known and common passwords (since 6.0.15)
WeakPasswordFile=             # more words, one per line; or WeakPasswordList[]=...
ForgotPasswordKeyLifetime=3600 # seconds a "Forgot your password?" link works; 0: until used
HashType=php_default          # bcrypt today; never plaintext or the md5_* types
UpdateHash=true               # re-hash older hashes at the next sign-in

[PasswordSettings]            # every rule off by default
RequireLowercase=disabled
RequireUppercase=disabled
RequireDigit=disabled
RequireSymbol=disabled
MinCharacterClasses=0         # at least this many of the four kinds above
ForbidLogin=disabled          # must not contain the login name
ForbidCurrentPassword=disabled
EndOtherSessions=enabled      # after a change, sign the user's other sessions out
ChangeNotificationMail=enabled
StrengthMeter=enabled
GenerateButton=enabled
```

Eine vernünftige Richtlinie für den Produktivbetrieb, im Einklang mit aktuellen Empfehlungen, die Länge gegenüber
Zusammensetzungsregeln bevorzugen:

```ini
# settings/override/site.ini.append.php
[UserSettings]
MinPasswordLength=15

[PasswordSettings]
ForbidLogin=enabled
ForbidCurrentPassword=enabled
```

Warum diese Werte: NIST SP 800-63B-4 (2025) verlangt mindestens 15 Zeichen, wo ein Passwort der einzige Faktor ist,
und sagt, dass prüfende Systeme keine Zusammensetzungsregeln vorschreiben sollten, weil Regeln wie „eine Ziffer, ein
Sonderzeichen“ zu vorhersehbaren Passwörtern (`Summer2026!`) statt zu starken führen; die Stärkeanzeige und die
Schaltfläche „Erzeugen“ (`GeneratePasswordLength=16`) helfen Benutzern stattdessen, die Länge zu erreichen. Die
Regeln `Require*` und `MinCharacterClasses` gibt es für Organisationen, deren eigene Richtlinie sie vorschreibt.
`MinPasswordLength` zählt Bytes, sodass ein Passwort in einer Schrift mit Mehrbyte-Zeichen sie mit weniger Zeichen
erreicht. Das Anheben des Minimums sperrt niemanden aus: Bestehende Passwörter funktionieren weiter, und die Regel
greift, wenn ein Passwort das nächste Mal gesetzt wird. Dasselbe gilt für die Prüfung auf bekannte Passwörter
(`WeakPasswordCheck`), die derselben Empfehlung folgt: Prüfende Systeme sollten neue Passwörter mit einer Liste häufig
verwendeter und kompromittierter Werte vergleichen. Ergänzen Sie die eigenen Begriffe Ihrer Organisation mit
`WeakPasswordList[]` oder `WeakPasswordFile`.

Die Regeln gelten für jedes Formular, das ein Passwort setzt (`user/password`, die Registrierung, das Benutzerkonto in
`content/edit` und in der Administration, `bin/php/resetuserpassword.php`), geprüft von `expPasswordPolicy`
(`kernel/classes/exppasswordpolicy.php`).

**`EndOtherSessions=enabled`** ist das, was eine Passwortänderung nach der Übernahme eines Kontos wirksam macht. So
funktioniert es:

- Bei jeder Anmeldung erhält die Sitzung einen **Passwortstempel** (`eZUserPasswordStamp`): einen schlüsselbasierten
  Hash (HMAC-SHA256) aus der Benutzer-ID und dem gespeicherten Passwort-Hash, gebildet mit einem Schlüssel, der aus
  dem Site-Geheimnis aus Abschnitt 13.3 abgeleitet ist. Er enthält weder das Passwort noch dessen Hash.
- Bei jeder Anfrage vergleicht `eZUser::currentUser()` den Stempel der Sitzung mit dem Stempel des aktuellen Hashs des
  Benutzers. Eine Passwortänderung ändert den Hash, sodass jede andere Sitzung dieses Benutzers (andere Browser,
  andere Geräte, eine von einem Angreifer kopierte Sitzung) nicht mehr passt und bei ihrer nächsten Anfrage abgemeldet
  wird. Das funktioniert mit jedem Session-Handler, PHPs eigene Datei-Sitzungen eingeschlossen, weil es geprüft wird,
  wenn die Sitzung verwendet wird.
- Die Sitzung, in der das Passwort geändert wurde, bleibt angemeldet, erhält aber eine **neue Sitzungs-ID**, sodass
  jemand, der die alte ID kannte, sie nicht behält. Mit dem Datenbank-Session-Handler werden die anderen Sitzungen
  außerdem sofort gelöscht.
- **Nicht geprüft:** Sitzungen, die vor dem Upgrade auf 6.0.15 begonnen haben (sie tragen noch keinen Stempel; sie
  werden ab ihrer nächsten Anmeldung geprüft), und Konten ohne eigenes Passwort (LDAP-, Textdatei- und
  Single-Sign-on-Benutzer).
- **Was Sie überraschen kann:** Ein neues Site-Geheimnis (eine Kopie der Installation ohne
  `settings/override/mailpreferences.ini.append.php`) ändert jeden Stempel, sodass alle einmal abgemeldet werden.
  `EndOtherSessions=disabled` schaltet den Stempel und die Prüfung zusammen ab.

Lassen Sie es eingeschaltet. Einzelheiten: [Das eigene Passwort ändern](../funktionen/6.0/moderne-passwortaenderung.md);
jeder Weg, ein Administratorpasswort zu ändern oder zurückzusetzen, und was zu tun ist, wenn sich niemand anmelden
kann: [Das Administratorpasswort ändern](../hilfe/administratorpasswort-aendern.md).

**`ChangeNotificationMail=enabled`** schickt dem Konto die Mail „Ihr Passwort wurde geändert“ (Kategorie
„Kontosicherheit“, die niemand abschalten kann; die Mail enthält nie das Passwort). So erfährt ein Benutzer von einer
Änderung, die er nicht selbst vorgenommen hat.

### Der Demo-Modus muss ausgeschaltet sein

Die Umgebungsvariable `EXP_DEMO_MODE=1` kennzeichnet eine Installation als **öffentliche Demo** (seit 6.0.15):
Administratorpasswörter können nicht mehr geändert oder zurückgesetzt werden, und die Installer akzeptieren ein
allgemein bekanntes Administratorpasswort. Auf einer Produktiv-Site ist das ein bekanntes Administratorpasswort, das
niemand ändern kann, deshalb:

- Stellen Sie sicher, dass kein Prozess der Site sie gesetzt hat: `grep -r EXP_DEMO_MODE` über die PHP-FPM-Pools, die
  Virtual Hosts, die Service-Units (die von Velocity eingeschlossen), die Crontabs und die Container-Definitionen darf
  nichts finden.
- Die Administration zeigt, solange er aktiv ist, auf jeder Seite ein Banner **Demo-Modus**, und jeder Prozess, der
  damit startet, schreibt eine Zeile `demo/mode` in `var/log/security.log` und das Audit-Ereignis `system.demo.mode`.
  Lösen Sie bei beidem eine Warnung aus.
- Er kann nicht aus Einstellungen kommen: Es gibt weder eine INI-Einstellung noch einen Anfrageparameter dafür, und
  nur der exakte Wert `1` zählt. Einzelheiten und Risiken: [Demo-Modus](../bc/6.0/demomodus.md).

### Fehlgeschlagene Anmeldungen

```ini
[UserSettings]
MaxNumberOfFailedLogin=0      # 0: no lockout
TrustedIPList[]               # addresses never locked out
ShowMessageIfExceeded=false
```

Setzen Sie `MaxNumberOfFailedLogin` (zum Beispiel auf `10`), um ein Konto nach so vielen fehlgeschlagenen Versuchen zu
sperren, bis ein Administrator es entsperrt. Wägen Sie das ab: Eine Sperre erlaubt es auch jedem, ein bekanntes Konto
zu sperren. Die Warnregeln des Audits `brute_force` (20 Fehlschläge aus einem Netz in 5 Minuten) und
`brute_force_user` (10 für ein Konto in 15 Minuten) melden solche Angriffe in jedem Fall (Abschnitt 13.14).

Die Anmeldung selbst verrät nicht, welche Konten existieren: Ein unbekannter Name kostet dieselbe Hash-Berechnung wie
ein falsches Passwort, und „Passwort vergessen?“ gibt für eine unbekannte Adresse dieselbe Antwort wie für eine
bekannte.

---

## 13.7 Sitzungen und Cookies

`settings/site.ini [Session]`; die drei Cookie-Schlüssel sind in der mitgelieferten Datei auskommentiert, und ihre
Standardwerte gelten, solange sie nicht gesetzt sind:

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `CookieSecure` | `auto` | `Secure`, sobald die Anfrage selbst über HTTPS kam; `true` erzwingt es |
| `CookieHttponly` | `true` | Skripte können das Sitzungs-Cookie nicht lesen |
| `CookieSameSite` | `Lax` | `Lax`, `Strict`, `None` (nur mit `Secure`) oder leer |
| `SessionTimeout` | `259200` | Sekunden, die eine Sitzung dauert (3 Tage) |
| `RememberMeTimeout` | leer | die Gültigkeitsdauer von „Angemeldet bleiben“ (Remember me); leer schaltet sie ab |
| `SessionNameHandler`, `SessionNamePerSiteAccess` | `default`, `enabled` | `custom` gibt jedem Siteaccess sein eigenes Sitzungs-Cookie, sodass eine Anmeldung auf der öffentlichen Site keine Anmeldung in der Administration ist |

Für eine Site, die nur über HTTPS ausgeliefert wird:

```ini
# settings/override/site.ini.append.php
[Session]
CookieSecure=true
CookieHttponly=true
CookieSameSite=Lax
SessionTimeout=28800
```

`CookieSecure=true` stellt sicher, dass das Cookie nie über einfaches HTTP gesendet wird, auch nicht bei einer
Anfrage, die fälschlicherweise ohne TLS ankommt. Verwenden Sie `Strict` nur, wenn keine andere Site auf Seiten
verlinkt, die eine Anmeldung voraussetzen. Ein kürzerer `SessionTimeout` begrenzt, wie lange eine gestohlene Sitzung
brauchbar bleibt.

Getrennte Sitzungs-Cookies je Siteaccess:

```ini
[Session]
SessionNameHandler=custom
SessionNamePrefix=eZSESSID
SessionNamePerSiteAccess=enabled
```

Mit `custom` leitet der Antwort-Cache von Velocity sein Cookie „persönlich, nicht cachen“ automatisch aus
`SessionNamePrefix` ab.

In der eigenen Konfiguration von PHP sorgt `session.use_strict_mode=1` dafür, dass PHP Sitzungs-IDs ablehnt, die es
nicht selbst erzeugt hat.

Was schiefgehen kann:

- **Angemeldete Benutzer werden auf jeder Seite abgemeldet oder können sich gar nicht anmelden, hinter einem
  HTTPS-Proxy** mit `CookieSecure=true`, wenn der Kernel die Anfrage für einfaches HTTP hält und `http://`-Weiterleitungen
  erzeugt: Dem Proxy wird nicht vertraut, oder er sendet kein `X-Forwarded-Proto` (Abschnitt 13.10). Mit
  `CookieSecure=auto` führt derselbe Fehler stattdessen zu einem Cookie ohne `Secure`, was unauffälliger und
  schlimmer ist.
- **`CookieSameSite=None` ohne `Secure`** wird von Browsern abgelehnt; der Kernel berücksichtigt `None` nur zusammen
  mit `Secure`. Sie brauchen `None` nur, wenn die Site innerhalb eines Frames oder über ein Cross-Site-Formular auf
  einer anderen Domain verwendet wird.
- **`Strict`** verwirft die Sitzung beim ersten Klick aus einer E-Mail oder von einer anderen Site auf eine Seite, die
  eine Anmeldung voraussetzt, was Benutzer als Abmeldung wahrnehmen.

---

## 13.8 Formular-Tokens gegen Cross-Site Request Forgery

Die Extension `ezformtoken` fügt jedem HTML-Formular ein Token je Sitzung hinzu und prüft es bei jedem POST
(`extension/ezformtoken`, Event-Listener `request/input`, `response/output` und `session/regenerate`). Ein gefälschtes
Formular auf einer anderen Site kann das Token nicht kennen. Der Setup-Assistent aktiviert sie bei jeder neuen
Installation.

- **Behalten Sie sie in `[ExtensionSettings] ActiveExtensions[]`.** Prüfen Sie: `php bin/php/console exp:ini where
  site.ini/ExtensionSettings/ActiveExtensions` (jede Datei, die den Wert setzt, und der wirksame Wert) oder
  **Setup > Extensions**.
- **Eigener AJAX-Code**, der POST-Anfragen sendet, muss das Token ebenfalls senden; die Skripte des Kernels und
  ezjscore tun das. Die `README.rst` der Extension zeigt, wie.
- Ein abgelehntes Token wird mit 403 und der Seite „Formular abgelaufen“ beantwortet, und es wird kein Token ins Log
  geschrieben ([Seite „Formular abgelaufen“](../funktionen/6.0/seite-abgelaufenes-formular.md)).
- Eine Seite, die ein Token trägt, wird mit `Cache-Control: private` gesendet, sodass kein gemeinsam genutzter Cache
  eine angemeldete Seite mit ihrem Token speichert. Ein Reverse Proxy darf das nicht überschreiben (Abschnitt 13.10).

---

## 13.9 Sicherheits-Header und HTTPS

### Header auf jeder Seite

`settings/site.ini [HTTPHeaderSettings]`:

| Header | Standard |
|---|---|
| `X-Content-Type-Options` | `nosniff` |
| `X-Frame-Options` | `SAMEORIGIN` |
| `Content-Security-Policy` | `frame-ancestors 'self'` |
| `Referrer-Policy` | `strict-origin-when-cross-origin` |
| `Permissions-Policy` | `camera=(), microphone=(), payment=(), usb=()` |
| `Strict-Transport-Security` | leer (aus; wird ohnehin nur über HTTPS gesendet) |

Ein leerer Wert in einem Override lässt einen Header weg; Header-Namen und -Werte, die die Antwort aufspalten könnten,
werden ignoriert. Um einem vertrauenswürdigen Redaktions-Host zu erlauben, die Administration in einen Frame
einzubetten:

```ini
[HTTPHeaderSettings]
SecurityHeaders[Content-Security-Policy]=frame-ancestors 'self' https://edit.example.com
```

Seiten, die aus dem HTTP-Cache des Kernels beantwortet werden, tragen dieselben Header.

**Velocity** sendet vier davon (alle außer der Content Security Policy) bei dem, was er selbst beantwortet (statische
Dateien, skalierte Bilder, seine eigenen Fehlerseiten, Weiterleitungen, 304-Antworten), über
`[ServerSettings] ResponseHeaders[]` und ergänzt sie bei Skriptantworten, denen sie fehlen
(`ResponseHeadersOnScripts=enabled`). **Apache und nginx** senden von sich aus bei statischen Dateien nichts
zusätzlich; fügen Sie sie dort hinzu, wenn Sie sie haben möchten (Apache `mod_headers`:
`Header always set X-Content-Type-Options "nosniff"`; nginx: `add_header X-Content-Type-Options "nosniff" always;`).

Eine vollständige Content Security Policy (`script-src`, `style-src`) wird aus `site.ini [ContentSecurityPolicy]`
gesendet: standardmäßig nur als Bericht (report-only) in der Administration, ausgeschaltet auf den Sites, deren
Designs und Extensions Inline-Skripte verwenden können. Beobachten Sie die Berichte (Browserkonsole, `ReportURI`),
bevor Sie sie durchsetzen, siehe [Content Security Policy](../funktionen/6.0/content-security-policy.md).

### Überall HTTPS

1. Liefern Sie jeden Namen der Site über HTTPS aus: Velocity `[HTTPSSettings]` oder die ACME-Unterstützung der
   Engine, Apache `mod_ssl`, nginx `ssl` ([Kapitel 8](08-die-website-ausliefern.md#839-https-direkt-von-velocity)).
2. Leiten Sie einfaches HTTP auf HTTPS um (Apache und nginx: Abschnitte 8.5 und 8.6). Für einzelne Teilbäume hat
   Exponential eigene `[SSLZoneSettings]`.
3. Setzen Sie `[Session] CookieSecure=true` (Abschnitt 13.7).
4. Schalten Sie HSTS ein, beginnen Sie mit einer kurzen Dauer und erhöhen Sie sie, sobald alles funktioniert:
   - Velocity: `[HTTPSSettings] HSTSMaxAge` (300 im Auslieferungszustand), `HSTSIncludeSubDomains`, `HSTSPreload`;
   - Apache oder nginx: `SecurityHeaders[Strict-Transport-Security]=max-age=31536000` in `site.ini`, vom Kernel nur
     über HTTPS gesendet.

   Ein Browser merkt sich HSTS für den gesamten Zeitraum und verweigert einfaches HTTP zu diesem Host, selbst wenn Sie
   den Wert danach senken. Erhöhen Sie ihn erst auf ein Jahr, wenn jeder Name dauerhaft über HTTPS ausgeliefert wird.
5. Halten Sie Zertifikate erneuert und überwacht (`exp:velocity ssl show --json`, `daysLeft`; oder den Timer Ihres
   ACME-Clients).

Prüfen Sie:

```bash
curl -sI https://example.com/ | grep -i -E 'strict-transport|x-content-type|x-frame|referrer|permissions|content-security'
curl -sI https://example.com/user/login | grep -i set-cookie     # Secure; HttpOnly; SameSite=Lax
```

---

## 13.10 Hinter einem Proxy: vertrauenswürdige Header

Ein Proxy, Load Balancer oder CDN vor der Site teilt der Anwendung in Anfrage-Headern mit, was der Besucher angefragt
hat: `X-Forwarded-Proto` (war es HTTPS?), `X-Forwarded-Host` (welcher Name?) und `X-Forwarded-For` (von welcher
Adresse?). Das Problem ist, dass dies gewöhnliche Anfrage-Header sind: Jeder Besucher kann sie ebenfalls senden.
Blind geglaubt, erlauben sie einem Besucher, eine einfache HTTP-Anfrage wie HTTPS aussehen zu lassen, einen anderen
Hostnamen in jede absolute URL zu setzen, die die Site erzeugt, und die Adresse zu wählen, die die Anmeldesperre,
`DebugByIP`, die Request-Regeln, das Audit-Protokoll und das Einwilligungsprotokoll festhalten. Das Einzige, was der
Server sicher weiß, ist die Adresse der Gegenstelle, die die Verbindung geöffnet hat, `REMOTE_ADDR`, und deshalb
entscheidet diese.

### Die Regel

Seit 6.0.15 glaubt der Kernel weitergeleiteten Headern **nur, wenn `REMOTE_ADDR` in `site.ini [HTTPHeaderSettings]
TrustedProxies[]` steht** (`lib/ezutils/classes/eztrustedproxy.php`, verwendet von `eZSys` und vom HTTP-Cache). Der
Standard vertraut den Loopback-Adressen, die nichts außerhalb des Rechners verwenden kann:

```ini
[HTTPHeaderSettings]
TrustedProxies[]
TrustedProxies[]=127.0.0.1
TrustedProxies[]=::1
```

- Einträge sind Adressen oder Bereiche, IPv4 und IPv6 (`192.0.2.10`, `10.0.0.0/8`, `2001:db8::/32`); eine
  IPv4-Gegenstelle, die ein Dual-Stack-Socket als `::ffff:10.0.0.5` meldet, passt auf ihren IPv4-Eintrag. Hostnamen
  werden nicht aufgelöst, und ein Eintrag, der keine Adresse ist, wird ignoriert, sodass ein Tippfehler nichts
  vertraut statt etwas Unbeabsichtigtem.
- Ein Override, das mit `TrustedProxies[]` auf einer eigenen Zeile beginnt, ersetzt die Liste; diese Zeile allein
  vertraut überhaupt keinem Proxy.
- Schreiben Sie Kommentare auf eigene Zeilen: `TrustedProxies[]=10.0.0.0/8 # LB` ist keine Adresse und wird
  ignoriert.

Was jeder Header dann bewirkt, und nur von einem vertrauenswürdigen Proxy:

| Header | Wirkung | Ohne vertrauenswürdigen Proxy |
|---|---|---|
| `X-Forwarded-Proto: https` (sonst `X-Forwarded-Port` gleich `[SiteSettings] SSLPort`, sonst `X-Forwarded-Server` gleich `SSLProxyServerName`) | `eZSys::isSSLNow()` antwortet mit true: Absolute URLs, Weiterleitungen und `CookieSecure=auto` verwenden HTTPS | ignoriert; `$_SERVER['HTTPS']` und der Port zählen weiterhin, sie sind die eigene Antwort des Webservers |
| `X-Forwarded-Host` | ersetzt `Host` in erzeugten URLs | ignoriert; `Host`, dann `SiteURL` |
| der in `ClientIpByCustomHTTPHeader` genannte Header (Standard `false`, meist `X-Forwarded-For`) | die Adresse des Besuchers, **von rechts** gelesen | `REMOTE_ADDR` |

**Lesen von rechts**, mit einem Beispiel. Ein Besucher unter `198.51.100.7` sendet einen gefälschten Header
`X-Forwarded-For: 203.0.113.10` über ein CDN (`173.245.48.20`) an Ihren Load Balancer (`10.0.0.5`), der sich mit PHP
verbindet. PHP erhält `REMOTE_ADDR=10.0.0.5` und `X-Forwarded-For: 203.0.113.10, 198.51.100.7, 173.245.48.20`. Wenn
`10.0.0.0/16` und `173.245.48.0/20` vertrauenswürdig sind, beginnt der Kernel rechts: `10.0.0.5` (die Gegenstelle)
ist vertrauenswürdig, `173.245.48.20` ist vertrauenswürdig, `198.51.100.7` nicht, also ist das der Besucher. Das
gefälschte `203.0.113.10` weiter links wird nie betrachtet. Vor 6.0.15 gewann der Eintrag ganz links, und das war
genau der gefälschte. Ein Eintrag, der keine Adresse ist, beendet den Durchlauf beim letzten vertrauenswürdigen Proxy.
Header, an die Proxys anhängen (ein `X-Forwarded-Proto` mit `http, https`), werden auf dieselbe Weise gelesen: Es wird
der Wert verwendet, den der äußerste vertrauenswürdige Proxy geschrieben hat.

### Welches Setup was braucht

| Ihr Setup | Was zu tun ist |
|---|---|
| Kein Proxy: Apache oder nginx mit PHP-FPM, oder Velocity direkt vor den Besuchern | Nichts. Weitergeleitete Header, die ein Besucher sendet, werden jetzt ignoriert, und genau darum geht es. |
| Ein Proxy auf demselben Rechner, der die Header **setzt** (nginx oder Apache vor Velocity oder PHP-FPM, Varnish, ein TLS-Terminator) | Nichts; der Standard deckt `127.0.0.1` und `::1` ab. Lassen Sie den Proxy `X-Forwarded-Proto` selbst setzen (Apache `RequestHeader set X-Forwarded-Proto "https"`, nginx `proxy_set_header X-Forwarded-Proto $scheme;`). Ein lokaler Proxy, der den eigenen Header des Besuchers weiterreicht, würde ihn durchlassen: Wenn Sie das nicht ändern können, setzen Sie `TrustedProxies[]` leer. |
| Ein Proxy, Load Balancer oder CDN auf einer anderen Adresse | Tragen Sie seine Adressen oder Bereiche, und nur diese, in `settings/override/site.ini.append.php` ein. Hinter einem CDN und einem Load Balancer tragen Sie beide ein. Stellen Sie sicher, dass der Anwendungsserver nicht an ihnen vorbei erreichbar ist (Firewall, Security Group, Velocity `Host=127.0.0.1`). |
| Ein Frontend, das `REMOTE_ADDR` selbst umschreibt (Apache `mod_remoteip`, nginx `realip`, wie bei Plesks nginx vor Apache) | Nichts in Exponential: Der Besucher ist bereits die Gegenstelle, daher bleibt `ClientIpByCustomHTTPHeader` auf `false`. |
| Sie möchten die echte Adresse des Besuchers hinter einem Proxy | `ClientIpByCustomHTTPHeader=X-Forwarded-For` (oder ein Header mit einer einzigen Adresse wie `X-Real-IP` oder `CF-Connecting-IP`), zusammen mit dem Proxy in `TrustedProxies[]`. Hinter mehreren Proxys tragen Sie alle ein. |

Ein Beispiel mit Load Balancer:

```ini
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
TrustedProxies[]
# the load balancer's private subnet
TrustedProxies[]=10.0.0.0/16
ClientIpByCustomHTTPHeader=X-Forwarded-For
```

### Unter Velocity

Velocity führt eine eigene Liste vertrauenswürdiger Proxys, `Q.webserver.proxy.trusted` (standardmäßig `127.0.0.1`
und `::1`). Setzen Sie sie in einem eigenen Snippet unter `/etc/vc/conf-available/`, zum Beispiel
`reverse-proxy.conf`:

```json
{ "Q": { "webserver": { "proxy": {
    "trusted": ["127.0.0.1", "::1", "192.0.2.10"],
    "headers": { "ip": "X-Forwarded-For", "proto": "X-Forwarded-Proto", "host": "X-Forwarded-Host" }
} } } }
```

und aktivieren Sie es mit `exp:velocity conf enable reverse-proxy` (ein Link in `conf-enabled/`, wie es `a2enconf`
macht); starten Sie Velocity danach neu. Bei einer Gegenstelle aus dieser Liste ermittelt es die Adresse des
Besuchers aus `X-Forwarded-For` (ebenfalls von rechts) und übergibt sie der Anwendung als `REMOTE_ADDR`, sodass
`ClientIpByCustomHTTPHeader` nicht nötig ist. Seit der Engine-Änderung vom 5. Oktober 2026 (Commit `380a64d`,
veröffentlicht in 0.0.4.44) setzt Velocity außerdem `$_SERVER['HTTPS']` und `REQUEST_SCHEME` aus `X-Forwarded-Proto`
(oder dem in `Q.webserver.proxy.headers.proto` genannten Header), `CloudFront-Forwarded-Proto` oder dem `CF-Visitor`
von Cloudflare, **nur** wenn die Verbindung von dieser Liste kommt; eine TLS-Verbindung zu Velocity ist von jedem
Client aus HTTPS. Frühere Engines lasen den Protokoll-Header von jedem Client, wo immer HTTPS konfiguriert war.
Konfigurieren Sie die Proxys vor Velocity in dessen Liste, nicht in `TrustedProxies[]`; der Kernel sieht den Besucher
als Gegenstelle und das `HTTPS` der Engine als eigene Antwort des Servers. Lassen Sie den Proxy den ursprünglichen
`Host` weitergeben (`ProxyPreserveHost On`, `proxy_set_header Host $host`): `X-Forwarded-Host` erreicht den Kernel von
der Adresse des Besuchers und wird nicht verwendet.

### Was schiefgeht und woran man es erkennt

| Symptom | Ursache |
|---|---|
| Absolute URLs und Weiterleitungen lauten hinter einem HTTPS-Proxy auf `http://`; eine Weiterleitungsschleife auf der Anmeldeseite | der Proxy steht nicht in `TrustedProxies[]` (oder nicht in der Liste von Velocity), oder er sendet kein `X-Forwarded-Proto` |
| Jeder Besucher hat dieselbe Adresse: die des Proxys (Audit, Sperre, Request-Regeln) | `ClientIpByCustomHTTPHeader` nicht gesetzt, oder dem Proxy wird nicht vertraut |
| Die fehlgeschlagenen Anmeldungen eines Besuchers sperren alle aus | dasselbe; wenn `MaxNumberOfFailedLogin` gesetzt ist, korrigieren Sie zuerst die Adresse |
| Ein Besucher kann seine Adresse oder das Schema weiterhin wählen | ein vertrauenswürdiger Bereich, der weit größer ist als Ihre Proxys, oder der Anwendungsserver ist direkt erreichbar |

### Prüfen

```bash
# directly, no proxy: the header must change nothing (the redirect stays http)
curl -s -o /dev/null -w '%{redirect_url}\n' -H 'X-Forwarded-Proto: https' http://example.com/user/logout
```

Hinter einem Proxy zeigt die Prüfung „Ist meine Adresse eingetragen?“ der Debug-Leiste `REMOTE_ADDR`, die Adresse, die der
Kernel verwendet, und ob der Proxy-Header gelesen wurde. Einzelheiten und weitere Beispiele (Apache `mod_proxy`,
nginx, Cloud-Load-Balancer, Cloudflare):
[Weitergeleiteten Headern wird nur von konfigurierten Proxys vertraut](../bc/6.0/vertrauenswuerdige-proxys.md).

### Cachende Proxys

Lassen Sie niemals einen Proxy Antworten cachen, die ein Cookie setzen oder `Cache-Control: private` tragen (Seiten
angemeldeter Benutzer, Seiten mit einem Formular-Token). Purge-Tags sind standardmäßig nicht öffentlich
(`httpcache.ini [HttpCacheSettings] TagHeader=disabled`); nennen Sie den einen Header, den Ihr Purge-Proxy liest, nur,
wenn Sie einen haben.

---

## 13.11 Dateiberechtigungen und Eigentümer

Warum das wichtig ist: Der häufigste Weg von einem Fehler in einer PHP-Datei zu einer dauerhaften Kompromittierung
ist eine Datei, die der Angreifer schreiben kann und die der Webserver später ausführt oder ausliefert. Wenn der
PHP-Prozess nur `var/` und `settings/` ändern kann und der Webserver es ablehnt, irgendetwas unterhalb des
Wurzelverzeichnisses auszuführen (Abschnitt 13.2), kann eine Schwachstelle in einer Extension keinen Code einschleusen.

Grundsätze (die Vorgehensweise steht in
[Kapitel 8, Abschnitt 8.7](08-die-website-ausliefern.md#87-dateiberechtigungen-und-eigentümer)):

- **Ein Benutzer schreibt.** PHP-FPM, Velocity-Worker, Cronjobs und Konsolenskripte laufen als der Benutzer der Site
  (oder teilen eine Gruppe). Lassen Sie Worker niemals als root laufen (`[ServerSettings] AllowRootWorkers=disabled`,
  der Standard).
- **Nur was geschrieben werden muss, ist beschreibbar**: `var/` und `settings/` (und, solange Pakete aus der
  Administration installiert werden, `design/` und `extension/`). Der Code (`kernel/`, `lib/`, `vendor/`,
  `index.php`) kann einem anderen Benutzer gehören und für den Benutzer der Site nur lesbar sein, sodass ein
  kompromittierter PHP-Prozess ihn nicht ändern kann.
- **Keine für alle beschreibbaren Dateien.** Die mitgelieferten Werte in `[FileSettings]` sind `0777` und `0666`;
  setzen Sie die gehärteten Werte, die `site.ini` empfiehlt:

  ```ini
  # settings/override/site.ini.append.php
  [FileSettings]
  StorageDirPermissions=0770
  StorageFilePermissions=0660
  TemporaryPermissions=0770
  LogFilePermissions=0660
  ```

  und korrigieren Sie vorhandene Dateien einmalig: `find var -type d -exec chmod 2770 {} +` und
  `find var -type f -exec chmod 0660 {} +` (mit der Gruppe der Site auf allem).
- **Geheimnisse** in `settings/override` (Abschnitt 13.3): `0640` oder strenger. Seit 6.0.15 behält ein Speichern aus
  der Administration, der Debug-Leiste oder `exp:ini` Modus, Eigentümer und Gruppe der Datei bei und legt eine neue
  Einstellungsdatei mit `0640` an (`EZP_INI_SAVE_FILE_PERMISSION` in `config.php`); vorher machte jedes Speichern die
  Datei zu `0666`. Prüfen Sie einmal, dass keine Einstellungsdatei für alle beschreibbar geblieben ist:
  `find settings extension/*/settings -perm -o+w` darf nichts ausgeben
  ([Upgrade-Hinweis](../bc/6.0/dateirechte-beim-speichern-von-ini-dateien.md)). INI-Cache-Dateien haben `0644`
  (`EZP_INI_FILE_PERMISSION`).
- **Logs** nennen Besucher: Velocity schreibt seine eigenen mit `0640` in ein Verzeichnis mit `0750`
  (`[LogSettings] FileMode`, `DirMode`), der Antwort-Cache mit denselben Modi.
- **Symbolische Links** aus dem Document Root hinaus werden von Velocity abgelehnt (`FollowSymlinks=disabled`); mit
  Apache verwenden Sie `Options SymLinksIfOwnerMatch` statt `FollowSymLinks`, wenn andere Benutzer als der der Site
  Links anlegen könnten.
- **`TemporaryDir`** (`/tmp/` im Auslieferungszustand) wird mit jedem Benutzer des Rechners geteilt; erwägen Sie ein
  eigenes Verzeichnis der Site, zum Beispiel unterhalb von `var/`.

---

## 13.12 Velocity-spezifische Härtung

| Thema | Einstellung oder Maßnahme |
|---|---|
| Bind-Adresse | `[ServerSettings] Host=127.0.0.1`, sofern Velocity nicht direkt vor den Besuchern steht; der Server hat keine eigene HTTP-Authentifizierung |
| Vorgeschaltete Proxys | nur die Adressen in `Q.webserver.proxy.trusted` dürfen Adresse und Protokoll des Besuchers setzen (Abschnitt 13.10); beschränken Sie die Liste auf Ihre eigenen Proxys |
| Worker-Benutzer | `User`, `Group` auf den Benutzer der Site gesetzt; niemals root |
| Symbolische Links | `FollowSymlinks=disabled` |
| Dashboard und Kennzahlen | ein leeres `[DashboardSettings] Token` und `Remote=disabled` halten `/Q/dashboard`, `/Q/stats`, `/Q/metrics` lokal; wenn Sie ein Token setzen, erzeugen Sie ein langes zufälliges (`openssl rand -hex 24`) und halten Sie es geheim |
| `/Q/phpinfo` | nur lokal; zeigt die Prozessumgebung |
| Kontrollpanel `/Q/panel` | nur lokal, passwortgeschützt; setzen Sie das Passwort sofort auf einem Rechner, der aus dem Internet erreichbar ist; sein Speicher darf nicht für Gruppe oder andere beschreibbar sein |
| HTTPS | ein Zertifikat einer Zertifizierungsstelle, HSTS erst erhöht, wenn alles HTTPS ist (Abschnitt 13.9) |
| Konfigurationsdateien | `/etc/vc/sites-available/<site>.conf` hat `0600` (Zertifikatspfade, Token); belassen Sie es so |
| Antwort-Cache | persönliche Seiten werden nie gecacht; wenn Sie das Sitzungs-Cookie umbenennen, prüfen Sie, dass `[CacheSettings] SkipCookies` abgeleitet bleibt oder es aufführt |
| Updates | Engine-Releases beheben auch Sicherheitsprobleme (Abschnitt 13.15); `exp:velocity deploy` nach einem Update |

---

## 13.13 Mail: Einwilligung und Sperrliste

Seit 6.0.15 durchläuft jede Mail das **Mail-Gate** (`expMailGate`), bevor sie den Transport erreicht:

- **Unverzichtbare** Kategorien (`security`, `orders`, `legal`, `admin`) gehen immer hinaus.
- **Optionale** Kategorien gehen nur an Empfänger, die sie eingeschaltet haben, mit einer Fußzeile, einem
  Abmeldelink mit einem Klick und `List-Unsubscribe`-Headern; eine Mail pro Empfänger, sodass niemand andere Adressen
  sieht.
- Mail ohne Kategorie wird gesendet und in `var/<vardir>/log/mailgate.jsonl` gezählt, sodass Sie Code finden können,
  der eine Kategorie angeben sollte.

Was Sie für den Produktivbetrieb einrichten
([Anleitung für Administratoren](../anleitungen/mail-einstellungen-administrator.md)):

1. Die Tabellen existieren: `php bin/php/console exp:mail:status` meldet fünfmal `ok`.
2. Die Fußzeile nennt den Absender: `mailpreferences.ini [FooterSettings] OrganisationName` und
   `OrganisationAddress`. Ohne sie geht optionale Mail ohne Postanschrift hinaus, die viele Rechtsordnungen
   vorschreiben.
3. Das Site-Geheimnis in `settings/override/mailpreferences.ini.append.php` ist gesichert (Abschnitt 13.3).
4. **Die Sperrliste** blockiert optionale Mail an harte Bounces, Beschwerden, Anfragen „alles abbestellen“ und
   rechtliche Anfragen. Gespeichert wird nur ein gesalzener Hash jeder Adresse, sodass die Liste nicht gelesen werden
   kann. Verwalten Sie sie unter **Setup > E-Mail-Einstellungen** oder auf der Konsole:

   ```bash
   php bin/php/console exp:mail:suppression check --email=<address>
   php bin/php/console exp:mail:suppression add --email=<address> --reason=legal --note="<no personal data>"
   php bin/php/console exp:mail:suppression lift --email=<address>
   ```

   Konfigurieren Sie den Bounce-Leser (`[BounceSettings]`), damit harte Bounces automatisch hinzugefügt werden.
5. **Das Einwilligungsprotokoll** hält jede Änderung der Präferenzen einer Person mit Quelle und Wortlaut fest. Es
   enthält Adressen und IP-Adressen: Geben Sie `mailpreferences/export` nur wenigen Personen, löschen Sie exportierte
   CSV-Dateien, sobald die Anfrage, die sie beantworten, erledigt ist, und lassen Sie den Cronjob-Teil
   `mailpreferences` die Aufbewahrungsfrist anwenden (`[ConsentSettings] RetentionDays`, 1095 Tage).
6. Datenschutzanfragen: `exp:mail:preferences export --email=<address>` (geschrieben mit Modus `0600`) und
   `exp:mail:preferences erase --email=<address> --yes`.

---

## 13.14 Das Audit-Protokoll

Das Audit hält Anmeldungen und fehlgeschlagene Anmeldungen fest, verschobene, verborgene oder entfernte Inhalte,
Änderungen an Rollen und Einstellungen, Preise, Befehle und Cronjobs sowie das, was das Audit selbst getan hat.
**Es ist in jeder Installation standardmäßig eingeschaltet.** Jeder Eintrag ist eine JSON-Zeile in einer Datei je
Kanal und Tag (`var/<site>/log/audit/<channel>-<YYYY-MM-DD>.jsonl`), und jede Zeile trägt den Hash der Zeile davor,
sodass eine geänderte, entfernte oder eingefügte Zeile auffällt.

```bash
php bin/php/console exp:audit status                           # enabled, directory, key, each channel intact or broken
php bin/php/console exp:audit verify                           # checks the hash chains
php bin/php/console exp:audit tail --channel=access --lines=20
```

Für den Produktivbetrieb:

1. **Führen Sie den Cronjob-Teil aus.** Er gehört zur Gruppe `frequent`: `php runcronjobs.php frequent` alle paar
   Minuten, als der Benutzer der Site. Das Audit-Dashboard warnt, wenn er eine Stunde lang nicht gelaufen ist.
2. **Legen Sie fest, wer Warnmails erhält**: standardmäßig die `AdminEmail` der Site; prüfen Sie es mit
   `php bin/php/console exp:audit alerts recipients`. Die mitgelieferten Regeln umfassen `brute_force`,
   `brute_force_user` und `admin_role_granted`.
3. **Sichern Sie `settings/override/audit.ini.append.php`** als Geheimnis (Signatur- und Pseudonymschlüssel).
4. **Geben Sie `audit/read` nur den Rollen, die es brauchen**; standardmäßig hat nur die Rolle Administrator
   `audit/read` und `audit/manage`.
5. **Datenschutz.** `[AuditPrivacySettings]` legt je Feld fest, was aufbewahrt wird: Adressen auf /24 und /48
   gekürzt, die Sitzung gehasht, E-Mail-Adressen gehasht, Passwörter und Tokens nie aufgezeichnet, personenbezogene
   Felder nach `PseudonymiseAfterDays` (90) pseudonymisiert. Passen Sie diese Werte an Ihre Datenschutzanforderungen
   an; die Abschnitte 4.12 und 6.4 der Audit-Anleitung beschreiben ein strenges Profil.
6. **Leiten Sie es weiter** an ein zentrales Log oder SIEM über eine Senke (`[AuditSink_syslog]`,
   `[AuditSink_webhook]`), damit ein kompromittierter Rechner seine eigene Geschichte nicht unbemerkt umschreiben
   kann. Führen Sie `exp:audit verify` regelmäßig aus.

Vollständige Anleitung: [Audit](../bc/6.0/audit.md).

---

## 13.15 Auf dem neuesten Stand bleiben

- **Verfolgen Sie die Releases** von Exponential und jeder Extension, die Sie verwenden: Es sind Composer-Pakete,
  daher listet `composer outdated` auf, was neuer ist. Lesen Sie vor dem Aktualisieren den Changelog und die
  Upgrade-Hinweise ([Kapitel 11](11-aktualisieren.md)).
- **Aktualisieren Sie zuerst in einer Kopie**, dann auf der Live-Site: `composer update <package>`, die
  Datenbank-Updates, die die Release nennt, `php bin/php/console exp:velocity deploy` (oder Autoloads, Cache leeren
  und ein Neuladen von PHP-FPM von Hand).
- **Prüfen Sie die Dateikonsistenz** nach einem Update: **Setup > Upgrade-Prüfung** („Überprüfe Dateikonsistenz“
  vergleicht die Dateien mit der `share/filelist.md5` der Release; „Überprüfe Datenbankkonsistenz“ das Schema). Eine
  veränderte Kernel-Datei, die Sie nicht geändert haben, verdient einen genaueren Blick.
- **PHP, der Datenbankserver, das Betriebssystem, die Engine von Velocity und das Binary von FrankenPHP** brauchen
  ebenfalls Updates. Version und Prüfsummen von FrankenPHP sind in `[FrankenPHPSettings] Version` und `Sha256[]`
  festgelegt; ändern Sie sie gemeinsam.
- **Überprüfen Sie Ihre eigenen Extensions** auf die Muster, die im Kernel behoben wurden (SQL, das aus Anfragedaten
  ohne `escapeString()` oder Integer-Casts gebaut wird, Shell-Befehle ohne `escapeshellarg()`, Ausgaben ohne
  Escaping); siehe [die Härtungshinweise zu 6.0.13](../bc/6.0/haertung.md). Extensions, die von den RAD-Assistenten
  vor deren Korrektur erzeugt wurden, sollten neu erzeugt werden ([RAD-Werkzeuge: Sicherheit](../bc/6.0/rad-sicherheit.md)).
- **Melden Sie eine Sicherheitslücke** an `security@se7enx.com`, nicht im öffentlichen Issue-Tracker
  ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)).

---

## 13.16 Checkliste für den Livegang

- [ ] Rewrite-Regeln: `.htaccess` ist eine aktuelle Kopie von `.htaccess_root` (oder der abgeleitete nginx-Block,
      oder Velocity); `/.git/config`, `/settings/site.ini`, `/composer.json` liefern nicht die Datei aus.
      `./console exp:setup:status` nennt die Vorlage.
- [ ] `config.php` nicht in der Versionsverwaltung; Modus `0640` mit der Gruppe des Webservers, wenn sie ein
      Passwort enthält (`exp:config:set` warnt, wenn nicht); `var/backup/` (frühere Kopien von `config.php`)
      geschützt und aufgeräumt.
- [ ] `settings/override/` nicht in der Versionsverwaltung, als Geheimnis gesichert, Modus `0640` oder strenger.
- [ ] Das erzeugte Administratorpasswort geändert; `var/log/initial-admin-password` gelöscht.
- [ ] Ein persönliches Administratorkonto pro Person; `admin` als Anmeldung stillgelegt; ein getestetes
      Notfallverfahren ([Kontohygiene](../hilfe/administratorpasswort-aendern.md#kontenhygiene)).
- [ ] `[DebugSettings] DebugOutput=disabled`; PHP `display_errors=Off`.
- [ ] `[SiteAccessSettings] CheckValidity=false`; die Administration auf einem eigenen Host, beschränkt per Netzwerk
      oder durch eine zweite Authentifizierung; Rollen nach dem Prinzip minimaler Rechte.
- [ ] Passwortrichtlinie festgelegt (`MinPasswordLength`, `[PasswordSettings]`); `EndOtherSessions=enabled`.
- [ ] Demo-Modus aus: `EXP_DEMO_MODE` für keinen Prozess der Site gesetzt; kein Banner **Demo-Modus**, keine Zeile
      `demo/mode` in `security.log` ([Der Demo-Modus muss ausgeschaltet sein](#der-demo-modus-muss-ausgeschaltet-sein)).
- [ ] `[Session] CookieSecure=true` auf einer reinen HTTPS-Site; `HttpOnly` und `SameSite` eingeschaltet gelassen.
- [ ] `ezformtoken` aktiv.
- [ ] HTTPS auf jedem Namen; HTTP umgeleitet; HSTS erhöht, sobald stabil; Zertifikatserneuerung überwacht.
- [ ] Sicherheits-Header auf Seiten vorhanden (und auf statischen Dateien, wenn Sie sie dort haben möchten).
- [ ] Hinter einem Proxy: `TrustedProxies[]` führt genau Ihre Proxys auf (Velocity: `Q.webserver.proxy.trusted`), der
      Proxy setzt `X-Forwarded-Proto` selbst, der Anwendungsserver ist nicht an ihm vorbei erreichbar; die
      `curl`-Prüfung aus 13.10 zeigt, dass ein vom Besucher selbst gesendetes `X-Forwarded-Proto` nichts ändert.
- [ ] Ein Benutzer (oder eine Gruppe) schreibt `var/` und `settings/`; keine für alle beschreibbaren Dateien; Worker
      niemals als root.
- [ ] Velocity: `Host` wie beabsichtigt, Dashboard-Token und Panel-Passwort gesetzt oder die Ansichten lokal
      gehalten.
- [ ] Mail: `exp:mail:status` ok, Fußzeile ausgefüllt, Bounce-Leser konfiguriert.
- [ ] Audit: `exp:audit verify` intakt, Cronjob-Gruppe `frequent` läuft, Empfänger der Warnmails geprüft, Schlüssel
      gesichert.
- [ ] Eine Update-Routine: wer die Releases beobachtet, wie Updates getestet werden, wer Sicherheitsmails erhält.

---

## 13.17 Referenzen

In diesem Repository:

- [Sicherheitsstandards vom September 2026](../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md): Header,
  Sitzungs-Cookie, Anmeldung, ausgelieferte Dateien, Caching persönlicher Seiten
- [Sicherheitshärtung von 6.0.13](../bc/6.0/haertung.md), [Härtung von Datentypen und Eingaben](../spezifikationen/6.0/datentyp-eingabehaertung.md),
  [RAD-Werkzeuge: Sicherheit](../bc/6.0/rad-sicherheit.md)
- [Request-Regeln](../bc/6.0/sicherheit-von-content-view-full.md)
- [Weitergeleiteten Headern wird nur von konfigurierten Proxys vertraut](../bc/6.0/vertrauenswuerdige-proxys.md); Code in
  `lib/ezutils/classes/eztrustedproxy.php` und `lib/ezutils/classes/ezsys.php`
- [Das eigene Passwort ändern](../funktionen/6.0/moderne-passwortaenderung.md), [Hinweise zur Passwortänderung von Benutzern](../bc/6.0/benutzerpasswort-aendern.md),
  [Seite „Formular abgelaufen“](../funktionen/6.0/seite-abgelaufenes-formular.md)
- [Die Exp-Debug-Leiste](../bc/6.0/debug-leiste.md)
- [Audit](../bc/6.0/audit.md)
- [E-Mail-Einstellungen: Anleitung für Administratoren](../anleitungen/mail-einstellungen-administrator.md),
  [Upgrade-Hinweise](../bc/6.0/e-mail-einstellungen.md)
- [HTTP-Cache](../bc/6.0/httpcache.md)
- Velocity: [HTTP/2 und Sicherheit](../spezifikationen/6.0/velocity-http2-und-sicherheit.md),
  [HTTPS und Zertifikate](../funktionen/6.0/velocity-https-zertifikate.md),
  [Kontrollpanel](../funktionen/6.0/velocity-control-panel.md), [Engines](../bc/6.0/velocity-engines.md)
- [Installation mit einem Befehl](../funktionen/6.0/installation-mit-einem-befehl.md) (das erzeugte Administratorpasswort)
- Die Einstellungen: [`settings/site.ini`](../../../settings/site.ini), [`settings/velocity.ini`](../../../settings/velocity.ini),
  [`settings/audit.ini`](../../../settings/audit.ini), [`settings/requestrules.ini`](../../../settings/requestrules.ini);
  die Regeln: [`.htaccess_root`](../../../.htaccess_root)
- [Kapitel 8: Die Site ausliefern](08-die-website-ausliefern.md)

Extern:

- OWASP: [Top Ten](https://owasp.org/projects/top-ten),
  [Session Management Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html),
  [Cross-Site Request Forgery Prevention](https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html),
  [Authentication Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html),
  [Password Storage Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html),
  [HTTP Security Response Headers](https://cheatsheetseries.owasp.org/cheatsheets/HTTP_Headers_Cheat_Sheet.html),
  [Content Security Policy](https://cheatsheetseries.owasp.org/cheatsheets/Content_Security_Policy_Cheat_Sheet.html),
  [Secure Headers Project](https://owasp.org/projects/secure-headers-project)
- NIST [SP 800-63B-4, Digital Identity Guidelines: Authentication and Authenticator Management](https://pages.nist.gov/800-63-4/sp800-63b.html) (Revision 4, die Revision 3 ablöst)
- PHP: [Sicherheit](https://www.php.net/manual/en/security.php),
  [Sitzungskonfiguration](https://www.php.net/manual/en/session.configuration.php),
  [display_errors](https://www.php.net/manual/en/errorfunc.configuration.php#ini.display-errors),
  [password_hash](https://www.php.net/manual/en/function.password-hash.php)
- MDN: [Set-Cookie](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Set-Cookie),
  [Strict-Transport-Security](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Strict-Transport-Security),
  [Content-Security-Policy](https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Content-Security-Policy)
- HSTS: [RFC 6797](https://www.rfc-editor.org/rfc/rfc6797.html); Cookies: [RFC 6265](https://www.rfc-editor.org/rfc/rfc6265.html)
- Apache: [Sicherheitstipps](https://httpd.apache.org/docs/2.4/misc/security_tips.html),
  [mod_headers](https://httpd.apache.org/docs/2.4/mod/mod_headers.html),
  [mod_authz_host (Require ip)](https://httpd.apache.org/docs/2.4/mod/mod_authz_host.html)
- nginx: [add_header](https://nginx.org/en/docs/http/ngx_http_headers_module.html#add_header),
  [Access-Modul (allow, deny)](https://nginx.org/en/docs/http/ngx_http_access_module.html)
- Let's Encrypt: [Dokumentation](https://letsencrypt.org/docs/)
- Exponential-Velocity-Engine: <https://github.com/se7enxweb/exponential-velocity> (dort `docs/security.md`,
  `docs/https.md`, `docs/panel.md`)

[Inhalt](README.md) · Zurück: [12. Fehlerbehebung](12-fehlerbehebung.md) · Weiter: [14. Migration von der 4.x-Linie](14-migration-von-4x.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
