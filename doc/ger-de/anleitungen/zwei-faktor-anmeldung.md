# Zweistufige Anmeldung

Nach dem Passwort ein Code aus einer Authenticator-App auf dem Telefon oder ein per E-Mail gesendeter Code: Das
Passwort allein nützt dann niemand anderem mehr. Diese Anleitung richtet sich an Site-Betreiber und Administratoren,
die die Funktion einschalten, und an Benutzer, die sie einrichten. Bereitgestellt wird sie von der Extension
sevenx_authentication_2fa (`composer.json` setzt sie voraus; die Installer aktivieren sie). Die eigene Dokumentation
der Extension enthält die Einzelheiten: `extension/sevenx_authentication_2fa/README.md`, `INSTALL.md` und
`doc/two-factor-pages.md`.

Zeitaufwand: 15 Minuten zum Einschalten, 2 Minuten pro Benutzer zum Einrichten.

## Was ein Benutzer sieht

1. **Anmelden** wie immer, mit Benutzername und Passwort.
2. **Bestätigen, dass Sie es sind**: Eine Seite fragt nach dem 6-stelligen Code. Mit einer Authenticator-App öffnen
   Sie die App und geben den Code ein, den sie für die Site anzeigt; bei E-Mail-Codes steht der Code in der Mail (und
   in ihrem Betreff). Jeder Code funktioniert nur einmal. Nach fünf falschen Codes endet die Anmeldung und beginnt
   erneut mit dem Passwort.
3. **Einrichten** unter *Mein Konto* > *Zweistufige Anmeldung* (`/user2fa/setup`; in der Administration dieselbe
   Adresse): Wählen Sie *Authenticator-App*, scannen Sie den QR-Code (oder geben Sie den in Vierergruppen angezeigten
   Schlüssel ein) und geben Sie den Code ein, den die App anzeigt. Bevor dieser Code eingegeben ist, ändert sich
   nichts. Der Schlüssel wird danach nicht mehr angezeigt.
4. **Ändern oder ausschalten**: Der Wechsel zu E-Mail-Codes oder das Ausschalten sowie *2FA zurücksetzen* verlangen
   einen aktuellen Code aus der App, damit niemand an einem unbeaufsichtigten Computer das Konto schwächen kann.

5. **Wiederherstellungscodes aufbewahren**: Nach dem Einrichten der App zeigt die Seite einmalig zehn
   Wiederherstellungscodes. Drucken Sie sie aus oder legen Sie sie in einem Passwortmanager ab. Jeder meldet einmal
   anstelle des App-Codes an (*Einen Wiederherstellungscode verwenden* auf der Code-Seite); die Einrichtungsseite zeigt,
   wie viele übrig sind, und erstellt mit einem aktuellen Code einen neuen Satz.

Telefon verloren? Melden Sie sich mit einem Wiederherstellungscode an und richten Sie die App neu ein. Ohne einen
solchen öffnet ein Administrator das Objekt des Benutzers in der Administration, setzt das Feld *Zweistufige
Anmeldung* auf *Einmalcode per E-Mail* (oder *Deaktiviert*, wo der zweite Schritt nicht vorgeschrieben ist) und
veröffentlicht. Einem ausgesperrten Administrator wird auf dem Server geholfen:

```bash
php extension/sevenx_authentication_2fa/bin/php/sevenx2fauser.php --login=<login>          # was das Konto hat
php extension/sevenx_authentication_2fa/bin/php/sevenx2fauser.php --login=<login> --reset  # entfernen, Konto entsperren
```

Nach `--reset` verlangt die nächste Anmeldung eine neue Einrichtung, wo der zweite Schritt vorgeschrieben ist.

## Einschalten

1. Aktivieren Sie die Extension (`[ExtensionSettings] ActiveExtensions[]=sevenx_authentication_2fa`) und den
   Login-Handler in `settings/override/site.ini.append.php`, vor `standard`:

   ```ini
   [UserSettings]
   LoginHandler[]
   LoginHandler[]=sevenxUser2fa
   LoginHandler[]=standard
   ```

2. Fügen Sie der Benutzerklasse das Feld hinzu: ein Klassenattribut des Datentyps *7x 2FA Configuration*
   (Bezeichner zum Beispiel `two_factor`). Benutzer einer Klasse ohne dieses Attribut können keinen zweiten Schritt
   haben; die Einrichtungsseite weist darauf hin.
3. Geben Sie jeder Rolle, mit der man sich anmeldet, die Richtlinien `user2fa/setup` und `user2fa/verify` (Member,
   Editor, Administrator ...). Für anonyme Besucher ist nichts nötig: Die Schritte vor der Anmeldung stehen in
   `[RoleSettings] PolicyOmitList` der Extension und prüfen ihren eigenen Zustand.
4. Empfohlen: ein Schlüssel, der die Authenticator-Geheimnisse in der Datenbank verschlüsselt, in
   `settings/override/sevenxauthentication2fa.ini.append.php` (halten Sie ihn aus der Versionsverwaltung heraus; geht
   er verloren, muss jeder die App neu einrichten):

   ```ini
   [TOTPSettings]
   SecretKey=<output of: openssl rand -base64 48>
   ```

5. Entscheiden Sie, ob es jedem freigestellt oder vorgeschrieben ist:

   | `[General] Enforce2FA` | `DefaultMethod` | Wirkung |
   |---|---|---|
   | `disabled` (Standard) | beliebig | Benutzer schalten es auf der Einrichtungsseite selbst ein. |
   | `enabled` | `totp` | Ein Benutzer ohne zweiten Schritt richtet die App direkt nach dem Passwort ein, bevor er angemeldet wird. |
   | `enabled` | `email` | Ein Benutzer ohne zweiten Schritt erhält sofort E-Mail-Codes (die E-Mail-Adresse des Kontos muss echt sein). |

   Für wen ein vorgeschriebener zweiter Schritt gilt:

   | Einstellung | Wirkung |
   |---|---|
   | `EnforcedGroups[]` | Nur für Mitglieder dieser Benutzergruppen (Knoten-ID oder Remote-ID des Objekts), auch über Gruppen darunter. Leer: für alle Benutzer. |
   | `ExemptGroups[]`, `ExemptEmailDomains[]` | Nie verlangt (freiwillig weiter möglich): Mitglieder dieser Gruppen und Adressen in diesen Domains, z. B. `example.invalid` für die Konten automatischer Prüfungen. |
   | `GracePeriodDays` | Tage, an denen ein Benutzer noch *Später einrichten* wählen kann, ab der ersten Anmeldung, die danach fragte. `0`: Die Einrichtung kommt zuerst. |
   | `[Security] RecoveryCodes` | Wiederherstellungscodes pro Authenticator (Standard 10, `0` für keine). |

   Eine neue Installation wird mit `Enforce2FA=enabled` für die Gruppen *Administrator users*
   (`9b47a45624b023b1a76c73b74d704acf`) und *Editors* (`3c160cca19fb135f83bd02d911f04db2`) angelegt, ohne Ausnahmen und
   ohne Schonfrist: Wer die Website ändern kann, braucht einen zweiten Schritt, Mitglieder im Frontend entscheiden
   selbst. Der Standard der Extension selbst bleibt `disabled`, damit ein Update niemanden aus einer bestehenden
   Website aussperrt.

   Bevor Sie ihn vorschreiben, listet `php extension/sevenx_authentication_2fa/bin/php/sevenx2fauser.php --enforced`
   die Konten auf, die bei ihrer nächsten Anmeldung zur Einrichtung aufgefordert würden.

6. Leeren Sie die Caches und stellen Sie bei Velocity bereit: `./console exp:velocity deploy --allow-root-user`.

Probieren Sie es zuerst auf einem Siteaccess aus: Aktivieren Sie die Extension als Zugriffs-Extension eines
Test-Siteaccess und tragen Sie die `LoginHandler`-Zeilen in die `site.ini.append.php` dieses Siteaccess ein. Denken
Sie daran, dass `settings/override` Vorrang vor einem Siteaccess hat: Mails, die vom Test-Siteaccess gesendet werden,
verwenden weiterhin das globale `[MailSettings] Transport`.

## Auf dem Demonstrationsserver

Die zweistufige Anmeldung ist auf dem Demonstrationsserver seit dem 6. Oktober 2026 eingeschaltet, als freiwillige
Option: Die Extension ist aktiv, der Login-Handler ist `sevenxUser2fa`, dann `standard`, die Benutzerklasse hat das
Feld *Zweistufige Anmeldung* (`two_factor`), jede Rolle, mit der man sich anmeldet (Member, Editor und die eigene
Redakteursrolle; Administrator hat alles), hat `user2fa/setup` und `user2fa/verify`, und die Geheimnisse sind mit
einem Schlüssel in `settings/override/sevenxauthentication2fa.ini.append.php` verschlüsselt. Niemand wird dadurch
automatisch registriert: Jedes Konto meldet sich wie bisher an, bis sein Inhaber den zweiten Schritt unter *Mein Konto*
einschaltet.

Um sie wie eine neue Installation für Administratoren und Redakteure vorzuschreiben, setzen Sie in
`settings/override/sevenxauthentication2fa.ini.append.php`:

```ini
[General]
Enforce2FA=enabled
DefaultMethod=totp
EnforcedGroups[]
EnforcedGroups[]=9b47a45624b023b1a76c73b74d704acf
EnforcedGroups[]=3c160cca19fb135f83bd02d911f04db2
```

leeren Sie dann den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`) und stellen Sie bereit
(`./console exp:velocity deploy`). Jeder von ihnen ohne zweiten Schritt richtet dann direkt nach dem Passwort einen
Authenticator ein. Richten Sie den zweiten Schritt zuerst für die Administratoren ein, damit niemand ohne
Telefon zur Hand überrascht wird, und entscheiden Sie für die Konten automatischer Prüfungen: deren reservierte Domain
ausnehmen (`ExemptEmailDomains[]=example.invalid`) oder jedem auf der Konsole einen Authenticator geben
(`sevenx2fauser.php --login=<login> --enrol-totp --secret-file=<pfad>`). Eine später angelegte Rolle braucht
`user2fa/setup` und `user2fa/verify`, damit ihre Benutzer den zweiten Schritt einrichten können. Um es wieder
auszuschalten, setzen Sie `Enforce2FA=disabled`.

Die Anmeldung über WebDAV, die Basic-Authentifizierung der REST-API oder Code, der `eZUser::loginUser()` direkt aufruft,
fragt nur nach dem Passwort. Lassen Sie diese dort ausgeschaltet, wo der zweite Schritt vorgeschrieben ist, oder auf
Konten beschränkt, die ihn nicht brauchen. Eine Sitzung, die vor dem Vorschreiben angemeldet wurde, bleibt angemeldet,
bis sie endet.

## Social Login

Schaltflächen wie *Weiter mit Google* erscheinen auf der Anmeldeseite (admin4, admin, Admin UI und das Media-Design)
für jeden in `sevenxauthentication2fa.ini` aktivierten Anbieter; `INSTALL.md` der Extension beschreibt die Schritte je
Anbieter. Ein auf diesem Weg gefundener Benutzer muss trotzdem seinen eigenen zweiten Schritt bestehen. Ein
abgebrochener oder fehlgeschlagener Social Login endet auf einer Seite, die sagt, was geschehen ist, mit dem Weg zurück
zum Anmeldeformular.

## Was sie schützt

- Das Passwort allein meldet nie an: Die Sitzung merkt sich nur, dass das Passwort richtig war; angemeldet wird der
  Benutzer, mit einer neuen Sitzungs-ID, erst nach dem richtigen Code.
- Codes: Jeder Authenticator-Code funktioniert nur einmal und nur innerhalb von etwa einer Minute der Uhrzeit; falsche
  Codes sind pro Anmeldung begrenzt und zählen als fehlgeschlagene Anmeldungen des Kontos
  (`[UserSettings] MaxNumberOfFailedLogin` sperrt es); E-Mail-Codes werden gehasht gespeichert, und ein neuer kann einmal pro Minute,
  dreimal pro Anmeldung angefordert werden; der Link in der Mail funktioniert nur in dem Browser, in dem das Passwort
  eingegeben wurde.
- Der QR-Code wird auf dem Server gezeichnet; der Schlüssel geht an keinen anderen Dienst. Seiten mit einem Schlüssel
  oder einer laufenden Anmeldung werden nicht gecacht.
- Jede Adresse, an die die Anmeldung einen Besucher weiterleitet, durchläuft die
  [Regeln für sichere Weiterleitungen](../funktionen/6.0/sichere-weiterleitungen.md).
- Der Social Login prüft einen einmaligen, an den Anbieter gebundenen State und lehnt Adressen ab, die der Anbieter
  nicht verifiziert hat.
- Jeder Schritt wird in `var/log/auth.log` geschrieben (nie ein Code oder ein Geheimnis).

## Testen

```bash
# the logic without a database
php vendor/bin/phpunit --no-configuration --bootstrap extension/sevenx_authentication_2fa/tests/bootstrap.php \
    extension/sevenx_authentication_2fa/tests/unit
```

Durch Benutzung: Melden Sie sich mit einem Testbenutzer an, richten Sie die App ein, melden Sie sich ab, melden Sie
sich mit einem Code an, probieren Sie einen falschen Code und zweimal denselben Code, und schalten Sie die Funktion
dann mit einem aktuellen Code aus. Registrieren Sie beim Ausprobieren niemals ein echtes Administratorkonto.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
