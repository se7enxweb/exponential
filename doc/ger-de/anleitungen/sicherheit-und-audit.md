# Sicherheit und Audit: absichern und nachvollziehen, wer was getan hat

Diese Anleitung richtet sich an Administratoren und Betreiber, die für die Sicherheit einer Site verantwortlich sind. In etwa 30 Minuten prüfen Sie, ob eine frische Exponential-Installation gehärtet ist, vergeben Zugriffe über Rollen und Richtlinien, lesen das Audit-Protokoll, schalten die Debug-Ausgabe nur für Ihre eigene Adresse ein und fügen (optional) eine Zwei-Faktor-Anmeldung hinzu. Jeder Schritt ist ein Befehl oder ein Klickpfad, den es in 6.0.15 gibt. Befehle werden im Wurzelverzeichnis des Projekts ausgeführt; die Beispiele verwenden `https://example.com` für Ihre Site.

Sie benötigen: Shell-Zugang zur Installation, eine Administrator-Anmeldung und `curl`.

## 1. Die bereits aktive Härtung prüfen (5 Minuten)

Seit September 2026 ist eine Standardinstallation ohne zusätzlichen Aufwand sicher. Prüfen Sie das, statt sich darauf zu verlassen.

**Sicherheits-Header.** Jede Seite, die Administration eingeschlossen, muss fünf Header tragen:

```bash
curl -sI https://example.com/ | grep -i -E 'x-content-type|x-frame|referrer|permissions|content-security'
```

Erwartete Ausgabe (die Reihenfolge kann abweichen):

```
x-content-type-options: nosniff
x-frame-options: SAMEORIGIN
content-security-policy: frame-ancestors 'self'
referrer-policy: strict-origin-when-cross-origin
permissions-policy: camera=(), microphone=(), payment=(), usb=()
```

Ändern Sie einen davon mit `./console exp:ini set` (Scope `override`), zum Beispiel, um einem vertrauenswürdigen Editor-Host zu erlauben, die Administration in einen Frame einzubetten. Die Einstellung ist `site.ini [HTTPHeaderSettings]` `SecurityHeaders[Content-Security-Policy]`; lesen Sie zuerst ihren Wert:

```bash
./console exp:ini get 'site.ini/HTTPHeaderSettings/SecurityHeaders[Content-Security-Policy]' --allow-root-user
```

Schalten Sie HSTS nur ein, wenn die gesamte Site ausschließlich über HTTPS läuft: `SecurityHeaders[Strict-Transport-Security]=max-age=31536000`.

**Dateien, die nicht ausgeliefert werden dürfen.** Punktdateien und PHP-Dateien unterhalb des Document Root müssen mit 404 antworten:

```bash
for p in .env .git/config extension/expservices/ezinfo.php; do
  curl -s -o /dev/null -w "$p %{http_code}\n" https://example.com/$p
done
```

Erwartet: dreimal `404`. Wenn Sie eine eigene Kopie von `.htaccess` pflegen, übernehmen Sie die Regeln aus `.htaccess_root` und `.htaccess_root_static`.

**Das Session-Cookie.** Melden Sie sich in der Administration an und öffnen Sie in den Entwicklerwerkzeugen des Browsers Anwendung (oder Speicher), Cookies: Das Session-Cookie muss `HttpOnly`, `SameSite=Lax` und, unter HTTPS, `Secure` zeigen. Das sind die Voreinstellungen im Code (die Schlüssel `CookieSecure=auto`, `CookieHttponly=true`, `CookieSameSite=Lax` in `site.ini [Session]`; die Datei führt sie nur als Kommentare auf). Das Cookie `is_logged_in` für HTTP-Caches trägt denselben Pfad, dieselbe Domain, dieselbe Lebensdauer, `Secure` und `SameSite`, nicht aber `HttpOnly`. Hinter einem Load Balancer, der einen Benutzer anhand des Session-Cookies auf einem Server hält, sendet `CookieAlwaysAddToHttpResponse=enabled` das Session-Cookie mit jeder Antwort einer laufenden Session (nie an einen Besucher ohne Session), und eine solche Antwort meldet `Cache-Control: private`, sodass kein gemeinsamer Cache sie speichert: Prüfen Sie mit `curl -sI -b <session cookie> <page>`, dass sie `Set-Cookie` zusammen mit `private` zeigt.

**Einstellungen, die es `SameSite` nennen.** Eine Installation, deren Einstellungen für LoveStack (mugoweb/ezpublish-legacy) geschrieben wurden, setzt das Attribut möglicherweise als `site.ini [Session] SameSite`. Exponential liest auch diesen Namen, solange `CookieSameSite` nicht gesetzt ist (sind beide gesetzt, wird `CookieSameSite` verwendet), und vermerkt einmal pro Anfrage, dass der Name veraltet ist (in `var/log/deprecated.log`, gezählt in der Debug-Ausgabe). Für Besucher ändert sich nichts; um den Hinweis abzustellen, benennen Sie die Einstellung in `settings/override` und den Siteaccesses in `CookieSameSite` um.

**Abmelden.** Das Abmelden beendet die Session endgültig: Was die Session enthielt, wird gelöscht, der Browser wird angewiesen, das Session-Cookie zu verwerfen (es wird abgelaufen gesendet, mit demselben Pfad, derselben Domain, `Secure`, `HttpOnly` und `SameSite`, mit denen es gesetzt wurde, sodass der Browser genau dieses Cookie entfernt), und die Session-ID, die der Browser hatte, meldet niemanden mehr an, auch wenn sie vorher kopiert wurde. Eine Session, die die Site später in derselben Anfrage startet, erhält eine neue ID. Das gilt für jeden Session-Handler (den eigenen von PHP, `ezpSessionHandlerDB`, einen eigenen Handler, den von Symfony) und unter Velocity. Frühere Versionen gaben der Session eine neue ID und behielten ihren Inhalt. Es muss nichts eingeschaltet werden. Zur Prüfung: Melden Sie sich in der Administration an und notieren Sie den Wert des Session-Cookies, melden Sie sich ab und senden Sie dann eine Anfrage mit dem alten Wert, z. B. `curl -s -b '<cookie name>=<old value>' https://<site>/admin/content/dashboard`: Die Antwort ist das Anmeldeformular, nicht das Dashboard. Mit `[UserSettings] RedirectOnLogoutWithLastAccessURI=enabled` führt das Abmelden weiterhin zurück zur zuletzt besuchten Seite.

**Dateien und Verzeichnisse, die die Site schreibt.** Logs, Caches und Speicher werden so offen angelegt, wie es `site.ini [FileSettings]` und der Code verlangen (standardmäßig 0777/0666). Setzen Sie auf einem Live-Server die Grenzen in `config.php`, `define( 'EZP_DIR_MODE_MAX', 0750 );` und `define( 'EZP_FILE_MODE_MAX', 0640 );`, damit nichts Neues für andere Konten lesbar oder schreibbar ist, und schränken Sie das Bestehende einmalig ein: [Grenzen für die Modi neuer Dateien und Verzeichnisse](../../eng-us/bc/6.0/file-modes.md).

**Hochgeladene Bilder.** Ein Bild wird anhand dessen akzeptiert, was die Datei enthält, nicht anhand ihres Namens: fileinfo von PHP liest den Typ, und er muss in `image.ini [ImageUploadSettings] AllowedMIMETypes[]` stehen (im Auslieferungszustand JPEG, PNG, GIF, WebP, AVIF, TIFF, BMP und SVG; ein SVG wird nur bereinigt gespeichert, siehe [SVG-Bilder](../../eng-us/bc/6.0/svg-images.md)). Das gilt für das Bearbeitungsformular, die Upload-Dialoge, den AJAX-Uploader und die Vorschaubilder des Paket-Assistenten, und eine abgelehnte Datei wird nicht gespeichert. Prüfen Sie, ob fileinfo im PHP des Webservers wie auch auf der Kommandozeile geladen ist (`php -m | grep fileinfo`); ohne fileinfo gilt die ältere Prüfung mit getimagesize(), und das Log weist einmal darauf hin. Lesen Sie die Liste mit:

```bash
./console exp:ini get 'image.ini/ImageUploadSettings/AllowedMIMETypes' --allow-root-user
```

**Das erste Administrator-Passwort.** Wenn eine Kickstart-Datei kein Passwort setzt, ein allgemein bekanntes wie `publish` (oder eine einfache Variante davon) oder eines, das kürzer als `MinPasswordLength` ist, erzeugt der Installer ein zufälliges Passwort aus 20 Zeichen, gibt es einmal aus und schreibt es nach `var/log/initial-admin-password` (nur für den Eigentümer lesbar). `exp:install --random-password` erzeugt eines aus 24 Zeichen und zeigt es einmal an. Melden Sie sich an, ändern Sie es und löschen Sie diese Datei. Falls Sie es später vergessen haben:

```bash
php bin/php/resetuserpassword.php --allow-root-user -u admin -g
```

`-g` erzeugt ein zufälliges Passwort und gibt es aus. Details: [ein Benutzerpasswort zurücksetzen](../../eng-us/features/6.0/reset-user-password.md).

Vertiefung: [Sicherheitsvoreinstellungen vom September 2026](../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md), [Härtung von Datentypen und Eingaben](../spezifikationen/6.0/datentyp-eingabehaertung.md), [Härtung in 6.0.13](../spezifikationen/6.0/sicherheitshaertung-6.0.13.md).

## 2. Nur den Zugriff vergeben, den Personen brauchen (10 Minuten)

Über den Zugriff entscheiden **Rollen** aus **Richtlinien** (Modul, Funktion, Beschränkungen), die Benutzern oder Gruppen zugewiesen werden. Niemand erhält etwas, das ihm keine Rolle gibt.

1. Melden Sie sich in der Administrationsoberfläche an und öffnen Sie den Reiter **Benutzer**, **Rollen und Richtlinien** (`/role/list`).
2. Klicken Sie auf **Neue Rolle** und nennen Sie sie im sich öffnenden Editor `Editor of News`.
3. Klicken Sie auf **Neue Richtlinie**, wählen Sie das Modul **content**, die Funktion **edit**, dann **Eingeschränkten Zugriff gewähren**, und wählen Sie die Sektion oder den Teilbaum, den die Person bearbeiten darf. **Speichern** Sie die Rolle mit der Schaltfläche unten.
4. Öffnen Sie die Rolle, klicken Sie auf **Zuweisen** und wählen Sie den Benutzer oder die Gruppe.
5. Melden Sie sich in einem privaten Fenster als dieser Benutzer an: Nur die erlaubten Inhalte lassen sich bearbeiten.

Rollen können Dutzende von Richtlinien enthalten; jede Richtlinie zeigt ihre ID, die Spaltenköpfe sortieren die Liste, und der Editor hat Schaltflächen zum Hoch- und Herunterschieben (sie ordnen nur die Liste, die Reihenfolge ändert nie, wer was darf): [Reihenfolge der Richtlinien einer Rolle](../../eng-us/features/6.0/role-policy-order.md). Eine Rolle, die vielen Benutzern zugewiesen ist, listet sie seitenweise auf, nach Namen sortiert, mit einem Namensfilter, und zeigt Zuweisungen, deren Benutzer oder Gruppe nicht mehr existiert: [seitenweise Anzeige der Rollenzuweisungen](../../eng-us/features/6.0/role-assignment-paging.md). Links in der Administration richten sich bereits nach den Berechtigungen, sodass ein Benutzer nie einen Link auf etwas sieht, das abgelehnt würde: [Links in der Administration folgen den Berechtigungen](../../eng-us/features/6.0/admin-links-follow-permissions.md).

Eine Bearbeitungsrichtlinie öffnet mehr als den Editor: Wer ein Objekt bearbeiten darf, darf auch dessen Versionen öffnen
(`content/history`), ohne eine Leserichtlinie dafür zu haben, und sieht dort Nummer, Status, Übersetzung, Ersteller und
Daten jeder Version und kann deren veröffentlichte, archivierte und abgelehnte Versionen vergleichen und kopieren. Der
Entwurf oder die ausstehende Version einer anderen Person bleibt ohne `content/versionread` verschlossen. Beschränken Sie eine
Bearbeitungsrichtlinie daher so, wie Sie die Leserichtlinie derselben Rolle beschränken: [die Versionen
eines Entwurfs](../../eng-us/bc/6.0/draft-edit-access.md#the-versions-of-such-an-object-contenthistory).

Verwenden Sie dieselben Regeln in Templates:

```
{if $current_user|has_role( 'Editor' )} ... {/if}
{if $current_user|has_policy( 'content', 'edit' )} ... {/if}
```

Alle sieben Operatoren finden Sie unter [Rollen- und Richtlinienprüfungen in Templates](../../eng-us/features/6.0/role-and-policy-template-operators.md).

Zwei Faustregeln: Bearbeiten Sie nie die Rolle Administrator, um einem Kollegen etwas mehr zu geben; legen Sie eine neue Rolle an. Und halten Sie die Zahl der Administratoren klein, denn die Vergabe dieser Rolle löst einen Alarm aus (Schritt 3).

## 3. Das Audit-Protokoll lesen (5 Minuten)

Das Audit ist standardmäßig eingeschaltet. Es zeichnet Anmeldungen und Ablehnungen, Inhaltsänderungen, Schreibvorgänge in Einstellungen, das Leeren von Caches, Bereitstellungen und Bestellungen in einem manipulationssicheren Protokoll auf (jeder Eintrag trägt den Hash des vorherigen).

```bash
./console exp:audit status --allow-root-user
```

Erwartet: `Audit: enabled`, dann eine Zeile je Kanal (`content`, `access`, `system`, `commerce`, `read`) mit Dateien, Bytes, Einträgen von heute und `Chain intact`.

Sehen Sie sich die neuesten Anmeldungen an und prüfen Sie, dass nichts verändert wurde:

```bash
./console exp:audit tail --channel=access --name='access.session.*' --lines=3 --allow-root-user
./console exp:audit verify --allow-root-user
```

`verify` gibt je Kanal `INTACT` aus und endet mit 1, wenn eine Kette unterbrochen ist.

Im Browser: Melden Sie sich als Administrator an, öffnen Sie den Reiter **Audit** (`/audit/dashboard`), melden Sie sich ab und wieder an und öffnen Sie **Konsole**: Ihre Anmeldung ist das neueste `access.session.login`. Suchen Sie nach Benutzer, IP-Netz oder Text:

```bash
./console exp:audit search --name='content.*' --result=refused --limit=10 --allow-root-user
./console exp:audit search --query=login --limit=5 --allow-root-user
```

Erledigen Sie Folgendes einmal bei einer neuen Installation:

1. Führen Sie die Cronjob-Gruppe `frequent` alle paar Minuten aus (`php runcronjobs.php frequent`); ihr Teil `audit` indiziert Ereignisse, beliefert Senken, wertet Alarme aus und rotiert, prüft und archiviert einmal täglich.
2. Prüfen Sie, wer Alarm-Mails erhält: `./console exp:audit alerts recipients --allow-root-user` (Voreinstellung: die `AdminEmail` der Site).
3. Listen Sie die Alarmregeln auf: `./console exp:audit alerts list --allow-root-user`. Ab Werk überwachen sie Brute-Force-Anmeldungen, die Vergabe einer Administratorrolle, Schreibvorgänge in Einstellungen außerhalb der Arbeitszeit, Massenlöschungen, das Abschalten des Audits und eine unterbrochene Kette.
4. Sichern Sie `settings/override/audit.ini.append.php`: Die Datei enthält die Signatur- und Pseudonym-Schlüssel.
5. Legen Sie `OnWriteFailure` fest (`settings/audit.ini [AuditSettings]`, Voreinstellung `continue`): Mit `refuse` werden sicherheitsrelevante Aktionen abgelehnt, solange das Protokoll nicht geschrieben werden kann, sodass nichts ohne Aufzeichnung geschieht.

Wer Einblick hat: Nur die Rolle Administrator hat die Richtlinien `audit/read` und `audit/manage`; vergeben Sie `audit/read` auf die Art von Schritt 2 an eine Auditor-Rolle (optional auf Kanäle beschränkt). Ein Benutzer ohne diese Richtlinie sieht keinen Reiter, und eine eingetippte URL wird abgelehnt und als `access.permission.refused` aufgezeichnet.

Um Einträge an einen Auditor zu übergeben, öffnen Sie **Export** mit dem Filter der Konsole: **Jetzt herunterladen** streamt die Datei
sofort, **Im Hintergrund exportieren** schreibt einen großen Filter in eine Datei, die Sie später auf derselben Seite herunterladen. Eine Extension
mit einem eigenen Kanal (Exponential Intelligence: der Kanal `ai`, jede Anfrage an ein Modell und jeder Entwurf, den es
geschrieben hat, nie ein Prompt oder eine Antwort) fügt dem Dashboard ihr Panel und der CSV-Datei ihre Spalten hinzu.

Änderungen an Orten: Das Entfernen eines Orts wird standardmäßig aufgezeichnet (**Ort entfernt**). Um zu sehen, wer einen Ort
hinzugefügt oder einen anderen Hauptort gewählt hat (**Ort hinzugefügt**, **Hauptort geändert**), schalten Sie diese beiden in
`settings/override/audit.ini.append.php` ein:

```ini
[AuditEventSettings]
Enabled[]=content.node.add
Enabled[]=content.node.main
```

Sie erscheinen dann in der Konsole wie jedes Inhaltsereignis, mit dem Objekt, dem Knoten und seinem Elternknoten; beim
Entfernen des Hauptorts wird außerdem aufgezeichnet, welcher Ort zum Hauptort wurde. Die Idee stammt aus LoveStack
(mugoweb/ezpublish-legacy, Commit `9c931192e0`), mit Dank an Mugo Web; siehe die
[Audit-Referenz](../../eng-us/bc/6.0/audit.md), „Location changes“.

Vertiefung: [das Audit-Protokoll](../../eng-us/features/6.0/audit-trail.md), [Ereignismodell des Audits](../spezifikationen/6.0/audit-ereignismodell.md), [Audit-Referenz (bc)](../../eng-us/bc/6.0/audit.md).

## 4. Debug-Ausgabe nur für Ihre Adresse (5 Minuten)

Die Debug-Ausgabe zeigt Dateipfade, SQL und Einstellungen; sie darf daher nie für jeden Besucher eingeschaltet sein. Zwei Einstellungen entscheiden, wer sie sieht: `DebugOutput` ist der Hauptschalter, und mit `DebugByIP=enabled` erhalten nur die Adressen in `DebugIPList[]` den Bericht.

1. Ermitteln Sie Ihre Adresse so, wie der Server sie sieht: Melden Sie sich in der Administration an, öffnen Sie die Exp-Debug-Leiste (sie erscheint, sobald Sie Debug-Ausgaben sehen dürfen), den Reiter **Einstellungen**, und führen Sie den IP-Test aus. Der Test benötigt die Richtlinie `setup/setup`; er nennt `REMOTE_ADDR`, den passenden Eintrag und Warnungen wie `lockout`, `open` oder `expired`.
2. Sehen Sie nach, woher die Einstellung kommt und was gilt:

   ```bash
   ./console exp:ini where site.ini/DebugSettings/DebugByIP --allow-root-user
   ```

3. Schreiben Sie Ihre Adresse mit einem Ablaufdatum, damit sie sich selbst aufräumt, in `settings/override/site.ini.append.php`:

   ```ini
   [DebugSettings]
   DebugOutput=enabled
   DebugByIP=enabled
   DebugIPList[]
   DebugIPList[]=203.0.113.7/32 ; My laptop ; expires=2026-12-31T18:00
   ```

   (Verwenden Sie Ihre Adresse; Bezeichnung und Ablaufdatum können entfallen.) Einträge akzeptieren IPv4- und IPv6-Netze (`10.0.0.0/8`, `2001:db8::/32`) sowie `commandline` für die Shell.
4. Leeren Sie den INI-Cache und laden Sie die Seite neu: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.

Die Leiste verweigert Schreibvorgänge, die Sie aussperren würden (die Liste enthielte Ihre Adresse nicht) oder Debug für alle öffnen würden (`/0`, oder `DebugByIP` aus bei eingeschaltetem `DebugOutput`), und sie protokolliert jede Änderung und kann sie rückgängig machen; Voreinstellungen (Template-Arbeit, SQL-Tuning, Alles, Aus) schalten ein ganzes Bündel von Einstellungen auf einmal. Schalten Sie auf **Aus**, wenn Sie fertig sind.

Vertiefung: [die Exp-Debug-Leiste](../../eng-us/features/6.0/exp-debug-bar.md), [Debug-Leiste (bc)](../../eng-us/bc/6.0/debug-bar.md), [lesbare Debug-Ausgabe](../../eng-us/features/6.0/debug-output-improvements.md).

## 5. Zwei-Faktor-Anmeldung (optional, 10 Minuten)

Die Extension `sevenx_authentication_2fa` ergänzt die Anmeldung um TOTP (Google Authenticator, Authy und jede App nach RFC 6238) und Einmalcodes per E-Mail.

1. Installieren und aktivieren Sie sie:

   ```bash
   composer require se7enxweb/sevenx_authentication_2fa
   ```

   ```ini
   # settings/override/site.ini.append.php
   [ExtensionSettings]
   ActiveExtensions[]=sevenx_authentication_2fa
   [UserSettings]
   ExtensionDirectory[]=sevenx_authentication_2fa
   LoginHandler[user2fa]=sevenxUser2fa
   LoginHandler[]=standard
   ```

   Der Standard-Handler muss nach `sevenxUser2fa` kommen; kommentieren Sie eine Zeile `LoginHandler[]=standard` in `settings/site.ini` aus, wenn die Reihenfolge nicht greift.
2. Aktualisieren Sie: `php bin/php/ezpgenerateautoloads.php -e` und `php bin/php/ezcache.php --clear-all --allow-root-user`.
3. Vergeben Sie die Richtlinien unter **Rollen und Richtlinien** (Schritt 2): `user2fa/verify` an die Rolle Anonymous und an jede Rolle, die 2FA verwendet, und `user2fa/setup` an die Rollen, die ihre eigene 2FA verwalten.
4. Melden Sie sich an und öffnen Sie `/user2fa/setup`: Wählen Sie TOTP, scannen Sie den Code mit Ihrer App (oder tippen Sie das Geheimnis ein) und geben Sie die sechs Ziffern ein. Melden Sie sich ab und wieder an: Nach dem Passwort werden Sie nach dem Code gefragt.
5. Um sie verpflichtend zu machen, setzen Sie `[General] Enforce2FA=enabled` in `settings/override/sevenxauthentication2fa.ini.append.php`; Benutzer ohne eingerichtete Methode werden auf die Einrichtungsseite geleitet.

Erwartet: Mit einem falschen Code wird die Anmeldung abgelehnt, und das Audit zeigt den fehlgeschlagenen Versuch. Telefon verloren: Der Benutzer kann den E-Mail-Code verwenden, solange `AllowEmailFallback` auf `enabled` steht (die Voreinstellung); die Extension hat außerdem eine Oberfläche zum Zurücksetzen der 2FA, die auf ihrer Funktionsseite beschrieben ist. Entfernen Sie abgelaufene Challenges mit `php extension/sevenx_authentication_2fa/bin/php/sevenx2facleanup.php` (auch ein Cronjob-Skript).

Vertiefung: [Zwei-Faktor- und Social-Login](../../eng-us/features/6.0/extensions/sevenx_authentication_2fa.md) (jede Einstellung, Social Login, einen Anbieter hinzufügen).

## 6. Ein Konto auf seiner Kontoseite prüfen (2 Minuten)

**Mein Konto** (`/user/edit`) ist Ihre eigene Kontoseite; `/user/edit/<id>` ist die eines anderen Benutzers, für einen Administrator,
der diesen Benutzer bearbeiten darf.

1. Öffnen Sie `/user/edit`. Die obere Karte zeigt Namen, Login, E-Mail-Adresse, Gruppen, Rollen (verlinkt mit den Rollenseiten,
   sofern Sie diese lesen dürfen), die letzte Anmeldung mit der Zahl der Anmeldungen und wann das Passwort zuletzt geändert wurde
   (aus dem Audit-Index; „Nicht erfasst“, wenn das Audit kein solches Ereignis hat). Eine Plakette zeigt, ob das Konto
   **Aktiv**, **Deaktiviert** oder **Gesperrt** ist (zu viele fehlgeschlagene Anmeldungen).
2. Lesen Sie die Hinweise darüber, die dringendsten zuerst: Das Konto ist deaktiviert oder gesperrt, es gab fehlgeschlagene Anmeldungen seit der
   letzten erfolgreichen, die zweistufige Anmeldung ist aus, das Passwort ist mit einem alten Verfahren gespeichert (eine Änderung speichert es mit
   dem aktuellen), das Passwort wurde vor mehr als einem Jahr geändert, das Konto hat sich nie angemeldet.
3. Machen Sie von einer Karte aus weiter: **Profil** (Inhaltsbearbeitung des Benutzers), **Passwort**, **Kontoeinstellungen** (aktivieren,
   deaktivieren, fehlgeschlagene Anmeldungen zurücksetzen), **Zweistufige Anmeldung** (`/user2fa/setup`, wenn die Extension aktiv ist und die
   Benutzerklasse ihr Feld hat), **API-Schlüssel**, **Lesezeichen**, **Benachrichtigungen** und **E-Mail-Einstellungen**. Es werden nur die
   Karten angezeigt, die Sie öffnen dürfen: Lesezeichen, Benachrichtigungen und die Einrichtung der zweistufigen Anmeldung gehören immer Ihnen selbst, sie stehen daher nicht
   auf der Seite eines anderen Benutzers; dort öffnet **API-Schlüssel** `/oauthadmin/keys/(user)/<id>` und **E-Mail-Einstellungen** die
   Administrationsseite dieses Benutzers.
4. **Abbrechen** führt zurück zu der Seite, von der Sie kamen.

Erwartet: Ein Administrator sieht auf seiner eigenen Seite jede Karte; ein Redakteur ohne die Richtlinie `user/password` oder
`user/preferences` sieht keine Karte Passwort oder Kontoeinstellungen. Für Entwickler: Die Seite ist
`design/admin4/templates/user/edit.tpl` (dieselbe Datei in `design/admin`), ihr Stil `user/exp_style.tpl` und die
Übersicht `expUserAccountOverview` (getestet in `tests/tests/kernel/classes/user/expUserAccountOverviewTest.php`).

Für alle Konten auf einmal listet **Benutzer > Benutzerkonten prüfen** (`user/hygiene`) diejenigen auf, die sich nie angemeldet haben, seit
N Tagen nicht gesehen wurden, deaktiviert, gesperrt oder verwaist sind oder eine E-Mail-Adresse teilen, deaktiviert, aktiviert oder entsperrt die angehakten
und exportiert die Liste als CSV: [Benutzerkonten prüfen](../../eng-us/features/6.0/user-hygiene.md).

## 7. Ein Konto pro Login (2 Minuten)

Bei der Anmeldung wird ein Konto anhand seines Logins ohne Rücksicht auf Groß- und Kleinschreibung gesucht; zwei Konten mit demselben Login (oder mit
Logins, die sich nur in der Schreibweise unterscheiden) können daher nicht beide verwendet werden, und welches antwortet, ist Zufall. Eine neue
Installation hat einen eindeutigen Index auf `ezuser.login`, sodass die Datenbank selbst die Logins auseinanderhält. Eine aktualisierte Site behält
den einfachen Index, den sie hatte, bis ein Administrator den eindeutigen hinzufügt; das Datenbank-Update tut das nicht, weil es auf einer Site mit
Duplikaten fehlschlagen würde.

1. Listen Sie die Duplikate auf (ändert nichts):

   ```bash
   ./console exp:user-login-check --allow-root-user
   ```

   Der Befehl zeigt die Datenbank-Engine, den Index auf `ezuser.login` und je Login, das mehr als ein Konto hat, eine Zeile
   mit den Inhaltsobjekt-IDs dieser Konten. Logins werden auf ihren ersten Buchstaben und ihre Länge gekürzt;
   `--show-logins` zeigt sie vollständig, `--json` liefert dasselbe als JSON. Eine Zeile, die auf „differ only in case“ endet, wird
   vom Index von PostgreSQL, SQLite, Oracle und MongoDB akzeptiert, die Bytes vergleichen, ist für Exponential aber dennoch dasselbe
   Login. Auf MySQL und MariaDB entscheidet die Kollation der Spalte; im Auslieferungszustand ignoriert sie Groß- und Kleinschreibung und
   Leerzeichen am Ende.
2. Lösen Sie jede Zeile von Hand auf: Öffnen Sie die Konten über ihre IDs (`/content/view/full/<node>` oder **Benutzer**), geben Sie einem davon
   auf seiner Bearbeitungsseite ein neues Login oder entfernen Sie ein Konto, das niemand verwendet.
3. Fügen Sie den eindeutigen Index hinzu, sobald der Bericht sauber ist:

   ```bash
   ./console exp:user-login-check --fix-index --allow-root-user
   ```

   Der Befehl verweigert das und ändert nichts, solange ein Duplikat aufgeführt wird. Exit-Codes: 0 sauber (oder Index hinzugefügt), 1
   Duplikate gefunden (oder Index verweigert), 2 Fehler.

Erwartet: „PASS: every login belongs to one account“ und, nach `--fix-index`, „Login index ezuser_login (unique)“.

Was Redakteure und Integrationen bemerken: Das Bearbeitungsformular hat ein Login, das ein anderes Konto bereits hat, schon bisher abgelehnt; nun
lehnen es auch ein Kontoentwurf, ein Paketimport, die Zeichenkette `login|email|hash|type|enabled` der Content-API und eine erste LDAP-Anmeldung ab,
und zwei Anfragen, die gleichzeitig dasselbe Konto anlegen, enden mit einem Konto. Eine erste LDAP-Anmeldung, die dieses Rennen verliert, meldet
sich mit dem Konto an, das die andere Anfrage angelegt hat. Für Entwickler:
`expUserLoginIntegrity::duplicates()`, `indexStatus()`, `addUniqueIndex()`, `loginTakenBy()` und `storeUser()`
(`kernel/classes/expuserloginintegrity.php`, getestet in `tests/tests/kernel/classes/expUserLoginIntegrityTest.php`).
Die Idee und die Behandlung der Entwürfe stammen aus LoveStack (Mugo Web, Commit 826e1d4647), mit Dank.

## Checkliste

- [ ] Fünf Sicherheits-Header vorhanden, Punktdateien und PHP-Dateien antworten mit 404
- [ ] Erstes Administrator-Passwort geändert
- [ ] Rollen enthalten nur, was Personen brauchen; wenige Administratoren
- [ ] `exp:audit status` zeigt jede Kette intakt, der Cronjob `frequent` läuft, die Alarmempfänger stimmen
- [ ] Debug-Ausgabe nur für Ihre eigene Adresse, mit Ablaufdatum
- [ ] Zwei-Faktor-Anmeldung für Administratoren eingeschaltet
- [ ] `exp:user-login-check` meldet, dass jedes Login zu einem Konto gehört, und der Login-Index ist eindeutig

**Demo-Modus im Audit und in den Logs.** Solange die Umgebungsvariable `EXP_DEMO_MODE=1` gesetzt ist, zeichnet jeder Prozess einmal das Audit-Ereignis `system.demo.mode` (Warnung, immer eingeschaltet) und eine Zeile `demo/mode` in `var/log/security.log` auf, und jede abgelehnte Änderung oder Zurücksetzung eines Administrator-Passworts ist `access.user.password.change.failed` bzw. `access.user.password.reset.failed` mit dem Grund `demo_mode`. Auf einer Site im Produktivbetrieb bedeutet jedes davon, dass die Variable dorthin gelangt ist. [Demo-Modus](../../eng-us/bc/6.0/demo-mode.md).

## Siehe auch

- Die Bedrohungen einzeln, und request-shield davor: [Sicherheitshärtung](../../eng-us/help/security-hardening.md); das Administratorkonto: [Das Administrator-Passwort ändern](../../eng-us/help/change-the-admin-password.md), [Das Standard-Administrator-Passwort](../../eng-us/help/default-administrator-password.md).
- Eine Site mit Tokens aus Skripten und Apps steuern: [Remote-Dienste und Apps](entfernte-dienste-und-apps.md).
- Auswirkungen der Sicherheitsänderungen auf ein Upgrade: [Verhaltensänderungen vom 1. und 2. Oktober 2026](../../eng-us/bc/6.0/behaviour-changes-2026-10.md), [Sicherheitshärtung vom August 2026](../spezifikationen/6.0/sicherheitshaertung-2026-08.md), [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md).
- Wie es dazu kam: [Chronik Oktober 2026](../../eng-us/history/2026/2026-10.md), [September 2026, zweite Hälfte](../../eng-us/history/2026/2026-09b.md).
- Weitere Anleitungen: [Betrieb einer Site](betrieb-einer-website.md) (Logs, Sicherungen, Reparaturen), [Extensions](extensions.md) (Zwei-Faktor-Anmeldung und andere Extensions), [Glossar](../../eng-us/glossary.md).
