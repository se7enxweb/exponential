# E-Mail-Einstellungen: einrichten und betreiben

Diese Anleitung richtet sich an den Administrator einer Installation. Am Ende sind die E-Mail-Einstellungen
eingerichtet (Tabellen, Fußzeile, Links, Site-Geheimnis), Sie können die Statusseite lesen, der Aufbewahrungs-Cronjob
läuft, und Sie wissen, wie Sie mit einer Bitte, keine E-Mails mehr zu senden, einem Bounce, einem Export und einer
Löschung umgehen. Was die Personen auf Ihrer Site sehen, steht in
[der Anleitung für Benutzer](../../eng-us/features/6.0/mail-preferences.md); wie Code E-Mails über die Schranke sendet, steht in
[der Anleitung für Entwickler](mail-einstellungen-entwickler.md).

Jeder Befehl unten wurde am 2026-10-04 auf einer Testinstallation mit Adressen der reservierten Domain `.invalid`
ausgeführt. Fügen Sie `--allow-root-user` hinzu, wenn Sie einen Befehl als root ausführen. Persönliche Links in der
Ausgabe sind auf `m1...` gekürzt, und der Host der Testinstallation ist durch `www.example.com` ersetzt.

## 1. Was das System tut, in einem Absatz

Jede E-Mail, die die Installation sendet, passiert die **Mail-Schranke** in `eZMailTransport::send()`, gleich welchen
Transport Sie verwenden (sendmail, SMTP, Datei). Eine E-Mail, die eine Kategorie angibt, wird gegen die
**Einstellungen** des Empfängers (einen Hauptschalter und einen Schalter je optionaler Kategorie), die **Sperrliste**
und ein ausstehendes **Double-Opt-in** geprüft. Optionale E-Mails gehen nur an Personen, die sie eingeschaltet haben,
eine E-Mail je Empfänger, mit einer **Fußzeile** (warum, abmelden, verwalten, Ihre Organisation und Postanschrift) und
den One-Click-Headern nach RFC 8058. Notwendige E-Mails gehen immer hinaus. E-Mails ohne Kategorie werden wie bisher
gesendet und gezählt, sodass Sie den Code finden können, der sie sendet. Jede Änderung einer Einstellung wird mit dem
genauen Text, den die Person gesehen hat, in das **Einwilligungsprotokoll** geschrieben.

## 2. Einrichten

### 2.1 Die Tabellen

Die fünf Tabellen `expmail_category`, `expmail_preference`, `expmail_consent_log`, `expmail_suppression` und
`expmail_pending` sind im Kernel-Schema einer neuen Installation enthalten. Eine aktualisierte Installation erhält sie
über das Datenbank-Update der Versionslinie, `update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql` (siehe
[die Upgrade-Hinweise](../../eng-us/bc/6.0/mail-preferences.md)). Solange sie nicht existieren, melden die Statusseite und
`exp:mail:status` die fehlende Tabelle, die Schranke kann niemandes Einstellungen lesen, und optionale E-Mails werden
zurückgehalten; E-Mails ohne Kategorie und notwendige E-Mails gehen wie bisher hinaus.

### 2.2 Die Fußzeile: wer sendet, und von wo

Das Gesetz (CAN-SPAM, CASL und die EU-Vorschriften zu Informationspflichten) verlangt in jeder optionalen E-Mail den
Namen des Absenders und eine Postanschrift. Beide werden leer ausgeliefert. Tragen Sie sie in ein Einstellungs-Override
ein, niemals in `settings/mailpreferences.ini`:

```bash
./console exp:ini set mailpreferences.ini/FooterSettings/OrganisationName "Example Organisation Ltd" global --dry-run
```

Erwartete Ausgabe (der Probelauf schreibt nichts; lassen Sie `--dry-run` weg, um zu schreiben):

```
--- a/settings/override/mailpreferences.ini.append.php
+++ b/settings/override/mailpreferences.ini.append.php
@@ -3,4 +3,7 @@
 [SecretSettings]
 TokenSecret=********
+[FooterSettings]
+OrganisationName=Example Organisation Ltd
+
 */ ?>
Dry run: set mailpreferences.ini/FooterSettings/OrganisationName in global, nothing written
```

Gehen Sie für `OrganisationAddress` genauso vor. Ein Zeilenumbruch in der Anschrift wird als `\n` geschrieben
(`Example Street 1\n12345 Town`). Wenn Sie eine Telefonnummer oder eine Kontaktadresse in der Fußzeile haben möchten
(CASL verlangt neben der Postanschrift eine Telefonnummer, eine E-Mail-Adresse oder eine Webadresse; der
Verwaltungslink ist eine Webadresse), fügen Sie sie zu
`OrganisationAddress` hinzu.

Die Fußzeile stammt aus `design:mailpreferences/mail/footer.tpl` (`[FooterSettings] Template`); überschreiben Sie
dieses Template in Ihrem Design, um seinen Wortlaut zu ändern, und behalten Sie dabei den Abmeldelink, den
Verwaltungslink und die Anschrift bei.

### 2.3 Wohin die Links zeigen

Die Links in E-Mails lauten `<BaseURL>/mailpreferences/unsubscribe/<token>` und `.../manage/<token>`.
`[LinkSettings] BaseURL` ist standardmäßig leer, was `https://` und die `SiteURL` des Standard-Siteaccess bedeutet.
Setzen Sie die Einstellung, wenn die öffentliche Site einen anderen Host hat oder wenn die Links einen Siteaccess einer
anderen Sprache öffnen sollen:

```bash
./console exp:ini set mailpreferences.ini/LinkSettings/BaseURL "https://www.example.com" global --dry-run
```

Die Statusseite zeigt die verwendete Adresse („Links in E-Mails zeigen auf“).

### 2.4 Das Site-Geheimnis

Die Links sind verschlüsselt und signiert, und die Sperrliste speichert gesalzene Hashes, beides mit einem
Site-Geheimnis. Es wird bei der ersten Verwendung in `settings/override/mailpreferences.ini.append.php`
(`[SecretSettings] TokenSecret`) erzeugt. Committen Sie diese Datei nie, und kopieren Sie sie mit der Installation:
**Ein neues Geheimnis macht jeden Link in jeder bereits gesendeten E-Mail ungültig, und kein Eintrag der Sperrliste
passt mehr.** `exp:ini` und die Einstellungsansichten maskieren es.

### 2.5 Rollen

| Richtlinie | Gewährt |
|---|---|
| `mailpreferences/administrate` | Setup > **E-Mail-Einstellungen**: Status, Kategorien, Sperrliste, Einwilligungsprotokoll, die Einstellungen eines Benutzers |
| `mailpreferences/export` | Den CSV-Export des Einwilligungsprotokolls und den Download der Daten eines Benutzers von den Administrator-Seiten |

Die eigenen Seiten der Person (`settings`, `manage`, `unsubscribe`, `confirm`, `request`, `export`) benötigen keine
Richtlinie: Sie stehen in `site.ini [RoleSettings] PolicyOmitList` und prüfen die Anmeldung oder den persönlichen Link
selbst.

## 3. Die Statusseite

Setup > **E-Mail-Einstellungen** (`/mailpreferences/admin/status`) zeigt:

- **Probleme**: was zuerst zu beheben ist. „Der Name der Organisation oder die Postanschrift der Mail-Fußzeile ist leer
  ...“, „Die Mail-Schranke ist ausgeschaltet ...“, „%count E-Mails ohne Kategorie in den letzten 7 Tagen.“, „%count
  E-Mails konnten in den letzten 24 Stunden nicht gesendet oder geprüft werden.“, eine fehlende Tabelle.
- **Zahlen**: Personen mit gespeicherten Einstellungen, Personen, die alle optionalen E-Mails ausgeschaltet haben,
  wartende Bestätigungen, Adressen auf der Sperrliste, in den letzten 24 Stunden gesendete und blockierte optionale
  E-Mails, E-Mails ohne Kategorie in den letzten 7 Tagen, Fehler, Einwilligungseinträge.
- **Verwendete Einstellungen**: die Schranke, die Organisation und die Postanschrift, wohin Links zeigen, ob das
  Site-Geheimnis existiert, die letzte E-Mail durch die Schranke und, wenn der Bounce-Leser eingerichtet ist, wann er
  das Postfach zuletzt gelesen hat.

Dasselbe auf der Konsole:

```bash
./console exp:mail:status
```

```
Tables
  expmail_category         ok
  expmail_preference       ok
  expmail_consent_log      ok
  expmail_suppression      ok
  expmail_pending          ok
Gate
  gate                     enabled
  site secret              present
  links point to           https://www.example.com
  organisation name        (empty)
  postal address           (empty)
Categories                   on    off  pending
  content                      0      2        0  optional
  collaboration                0      0        0  optional
  newsletter                   1      0        0  optional, double opt-in
  marketing                    0      0        0  optional, double opt-in
  system                       0      0        0  optional
  security                     -      -        -  essential
  orders                       -      -        -  essential
  legal                        -      -        -  essential
  admin                        -      -        -  essential
People
  with preferences         2
  all optional mail off    1
  pending confirmations    0   (0 expired)
  suppressed addresses     0
  consent log rows         22   (0 anonymised)
Gate, last 24 hours
  sent 1, blocked 3, essential 0, uncategorised 128, errors 0
  blocked because: off 3
  uncategorised from kernel/classes/notification/ezmailnotificationtransport.php: 127
  uncategorised from tests/tests/lib/ezutils/eZMailTest.php: 1
...
Problems
  [warning] The organisation name or postal address of the mail footer is empty (mailpreferences.ini [FooterSettings]); the law requires both in every optional mail.
  [notice] 128 mails without a category in the last 7 days.
PASS
```

`--json` liefert ein Objekt für das Monitoring; der Befehl endet nur bei einem Problem der Stufe error mit FAIL
(Exit-Code 1).

**E-Mails ohne Kategorie** sind kein Fehler: Sie werden genau wie bisher gesendet. Sie tragen jedoch keine Fußzeile
und keinen Abmeldelink; ist eine solche E-Mail werblich, ist sie daher nicht rechtskonform. Der Status nennt die Datei,
die sie gesendet hat; geben Sie diesem Code eine Kategorie
([die Anleitung für Entwickler](mail-einstellungen-entwickler.md)) oder tragen Sie bei E-Mails, die wirklich notwendig
sind, ihre Datei, Klasse oder From-Adresse in `[GateSettings] EssentialSenders[]` ein.

Die Schranke schreibt je Entscheidung eine JSON-Zeile nach `var/<vardir>/log/mailgate.jsonl` (`[GateSettings] LogFile`),
rotiert bei 5 MB, ohne Adresse darin.

## 4. Kategorien

Setup > E-Mail-Einstellungen > **Kategorien** listet jede Kategorie mit ihrer Art, ihren Häufigkeiten, dem
Double-Opt-in und dem Ort ihrer Definition auf (Einstellungsdatei, Extension, hier angelegt).

- **Neue Kategorie**: ein Bezeichner (Kleinbuchstaben, Ziffern, Unterstriche, beginnend mit einem Buchstaben; er kann
  später nicht geändert werden), ein Name und eine Beschreibung, wie die Personen sie sehen, die Häufigkeiten, die
  Personen wählen dürfen, und ob zuerst eine Bestätigung eingeholt wird. Eine hier angelegte Kategorie ist immer
  optional und ausgeschaltet, bis eine Person sie einschaltet. Code sendet E-Mails in ihr mit
  `eZMail::setCategory( '<identifier>' )`.
- **Bearbeiten** einer Kategorie aus den Einstellungen oder aus einer Extension: Nur ihr Name und ihre Beschreibung
  ändern sich; sie werden in der Datenbank gespeichert und stattdessen angezeigt. **Wieder den festgelegten Namen und
  die festgelegte Beschreibung verwenden** nimmt diese Änderung zurück.
- **Diese Kategorie entfernen** (nur für hier angelegte): Die Kategorie verschwindet von den Seiten; die Entscheidungen,
  die Personen getroffen haben, bleiben in ihren Einträgen erhalten.

Die Kategorien der Einstellungen stehen in `settings/mailpreferences.ini [CategorySettings] Categories[]`, mit je einem
Block `[Category_<identifier>]`. Setzen Sie für eine optionale Kategorie niemals `DefaultOn=true`: Dann würden E-Mails
an Personen gehen, die nicht darum gebeten haben, was die EU-Vorschriften und CASL für Werbung verbieten.

## 5. Die Einstellungen einer Person, auf deren Anfrage

Setup > E-Mail-Einstellungen > **Ein Benutzer**: Suchen Sie den Benutzer nach Name, Login, Adresse oder ID, öffnen Sie
ihn, und Sie sehen seine Seite so, wie er sie sieht, mit seiner Einwilligungshistorie. Ihre Änderungen werden mit der
Quelle „Von einem Administrator geändert“ und Ihrer Benutzer-ID festgehalten. Schalten Sie für die Person einen
Newsletter oder Angebote ein, erhält sie den Bestätigungslink; das Abonnement beginnt erst, wenn sie bestätigt.
**Als JSON herunterladen** und **Als CSV herunterladen** (Richtlinie `mailpreferences/export`) liefern ihre Daten.

Dasselbe auf der Konsole, auch für eine Adresse ohne Konto:

```bash
./console exp:mail:preferences show --email=reader@docs.invalid
```

```
Person            r***@docs.invalid
All optional mail on
  content              on       weekly     mail goes
  collaboration        off      immediate  no mail   (default)
  newsletter           pending             no mail
  marketing            on                  mail goes
  system               off                 no mail   (default)
  security             always              mail goes
  orders               always              mail goes
  legal                always              mail goes
  admin                always              mail goes
PASS
```

Adressen sind in der Ausgabe verborgen, sofern Sie nicht `--addresses` hinzufügen. **Schalten Sie eine
Double-Opt-in-Kategorie auf einer Installation, deren Transport tatsächlich sendet, niemals ohne `--no-mail` über die
Konsole ein**; `--source=import` schaltet sie sofort ein und ist nur für Einwilligungen gedacht, deren Bestätigung an
anderer Stelle Sie nachweisen können, mit dem Nachweis in `--wording`:

```bash
./console exp:mail:preferences set --email=reader@docs.invalid --category=marketing --state=on --source=import --wording="Imported with the opt-in of 2026-10-01"
```

```
r***@docs.invalid: marketing is on
PASS
```

`./console exp:mail:preferences link --email=...` gibt den Einstellungslink der Person aus, um eine Anfrage per E-Mail
zu beantworten.

## 6. Anfragen nach dem Datenschutzrecht

| Anfrage | Was zu tun ist |
|---|---|
| „Welche Daten haben Sie über mich?“ | Die Person kann sie auf ihrer Seite herunterladen. Oder: `./console exp:mail:preferences export --email=<address> --format=json --output=var/tmp/request.json` (die Datei wird mit dem Modus 0600 geschrieben) |
| „Senden Sie mir nichts mehr“ | Ihr Abmeldelink erledigt das. Oder fügen Sie die Adresse mit dem Grund `legal` zur Sperrliste hinzu (Abschnitt 7) |
| „Löschen Sie meine Daten“ | `./console exp:mail:preferences erase --email=<address> --yes` |

Die Löschung antwortet:

```
r***@docs.invalid: 4 preferences and 1 pending confirmations removed, 9 consent log rows anonymised
PASS
```

Die anonymisierten Einträge behalten Zeitpunkt, Kategorie und Änderung, ohne Adresse, IP oder Benutzer, sodass Sie
weiterhin nachweisen können, dass der E-Mail-Versand eingestellt wurde. Ohne `--yes` verweigert der Befehl die
Ausführung („erase removes the preferences and anonymises the consent log: add --yes“). Das Entfernen eines
Benutzerkontos löscht die Einstellungen und ausstehenden Bestätigungen dieses Kontos und anonymisiert sein
Einwilligungsprotokoll, wie es `erase --yes` tut (`eZUser::removeUser()` ruft `expMailPreferences::eraseForRemovedAccount()` auf); die Sperrliste
behält ihren gesalzenen Hash, sodass die Adresse gesperrt bleibt. Verwenden Sie den Konsolenbefehl für eine Person, die
kein Konto hat.

## 7. Die Sperrliste

An eine Adresse auf der Liste gehen keine optionalen E-Mails: harte Bounces, Beschwerden (als Spam markiert), Anfragen
„alle optionalen E-Mails beenden“, rechtliche Anfragen, von einem Administrator angelegte Einträge und die
Newsletter-Blacklist. Notwendige E-Mails gehen weiterhin hinaus. Gespeichert wird nur ein Hash der Adresse, sodass die
Liste nicht gelesen werden kann; Sie finden einen Eintrag, indem Sie die Adresse eingeben.

Setup > E-Mail-Einstellungen > **Sperrliste**: **Eine Adresse prüfen**, **Eine Adresse hinzufügen** (mit einem Grund und
einer Notiz ohne personenbezogene Daten) und einen Eintrag **Aufheben**. Die Konsole:

```bash
./console exp:mail:suppression add --email=other@docs.invalid --reason=legal --note="Docs example"
./console exp:mail:suppression check --email=other@docs.invalid
./console exp:mail:suppression list --reason=legal --limit=5
./console exp:mail:suppression lift --email=other@docs.invalid
```

```
suppressed (hash 0b2dc01385cf4440…)
suppressed: legal (hash 0b2dc01385cf4440…)
1 entries (legal)
  0b2dc01385cf4440…  legal            2026-10-04 23:39  Docs example
lifted
```

Eine Person, die selbst alle E-Mails beendet hat, hebt ihren eigenen Eintrag auf, indem sie optionale E-Mails auf
ihrer Seite wieder einschaltet. Einträge für Bounces, Beschwerden und rechtliche Anfragen bleiben bestehen, bis ein
Administrator sie aufhebt, und sie stoppen auch „Link zusenden“- und Bestätigungs-E-Mails. Einträge werden
standardmäßig für immer aufbewahrt (`[SuppressionSettings] RetentionDays=0`).

**Bounces.** Der Bounce-Leser der Integration liest ein in einem Einstellungs-Override festgelegtes Bounce-Postfach und
fügt harte Bounces hinzu; er ist ausgeschaltet, bis sein Postfach eingetragen ist, und die Statusseite zeigt dann
seinen letzten Lesevorgang. Ohne ihn fügen Sie harte Bounces von Hand mit dem Grund `bounce` hinzu.

Andere Teile der Site können dasselbe Postfach über den Leser lesen: Die Klassen in `[BounceSettings]
MessageListeners[]` erhalten jede Nachricht, nachdem der Leser seine eigene Arbeit erledigt hat. Der Newsletter
(cjw_newsletter 4.2.0) nutzt dies, um Ausgaben mit Soft Bounce erneut zu senden und An- und Abmelde-E-Mails zu
beantworten, die an die Adressen seiner Listen gesendet wurden. `./console exp:mail:bounces --dry-run` zeigt, was
geschehen würde, und eine Nachricht, die ein Listener übernommen hat, gilt für `AfterRead=delete` als verstanden.

## 8. Das Einwilligungsprotokoll

Setup > E-Mail-Einstellungen > **Einwilligungsprotokoll**: jede Änderung, die neueste zuerst, mit der Person, der
Kategorie, der Änderung, der Quelle, dem angezeigten Text und der IP-Adresse. Filtern Sie nach Person (Adresse oder
Benutzer-ID), Kategorie, Quelle und einem Zeitraum; die Filter stehen in der Adresse, sodass eine gefilterte Ansicht als
Lesezeichen gespeichert werden kann. **Als CSV exportieren** (Richtlinie `mailpreferences/export`) exportiert genau
das, was der Filter zeigt. Die CSV-Datei enthält Adressen und IP-Adressen: Bewahren Sie sie wie alle
personenbezogenen Daten auf, und löschen Sie sie, wenn die Anfrage, die sie beantwortet, erledigt ist. Zellen, die mit
`=`, `+`, `-` oder `@` beginnen, werden mit einem vorangestellten `'` geschrieben, damit eine Tabellenkalkulation sie
nicht ausführt.

```bash
./console exp:mail:consent list --email=reader@docs.invalid --limit=10
```

```
6 rows
  2026-10-04 23:39:11  r***@docs.invalid      -              master_on   off -> on  admin
  2026-10-04 23:39:09  r***@docs.invalid      -              master_off  on -> off  admin
  2026-10-04 23:39:05  r***@docs.invalid      marketing      on          off -> on  import
  2026-10-04 23:39:05  r***@docs.invalid      newsletter     pending     off -> pending  admin
  2026-10-04 23:39:04  r***@docs.invalid      content        frequency   immediate -> weekly  admin
  2026-10-04 23:39:03  r***@docs.invalid      content        on          off -> on  admin
PASS
```

## 9. Aufbewahrung und der Cronjob

Der Cronjob-Teil `mailpreferences` gehört zur Gruppe `infrequent` und kann allein ausgeführt werden:

```bash
php runcronjobs.php mailpreferences
```

Er entfernt die Einwilligungseinträge von Personen, die nicht mehr da sind (ein anonymisierter Eintrag, ein entferntes
Konto, eine Adresse ohne verbleibende Einstellung), sobald sie älter als `[ConsentSettings] RetentionDays` sind
(1095 Tage: die Lebensdauer des Kontos plus drei Jahre), entfernt Bestätigungen, die länger als
`[ConsentSettings] PendingDays` gewartet haben (7; die Kategorie ist dann wieder ausgeschaltet), und, wenn
`[SuppressionSettings] RetentionDays` größer als 0 ist, alte Einträge der Sperrliste. Um zu sehen, was er tun würde:

```bash
./console exp:mail:consent cleanup --dry-run
```

```
Would remove: 0 consent log rows, 0 expired confirmations
PASS
```

## 10. Die Schranke testen, ohne E-Mails zu senden

`exp:mail:gate` sagt, wie die Schranke entscheiden würde, und leitet mit `--write` eine Test-E-Mail durch die Schranke
in Dateien, niemals an einen Mailserver:

```bash
./console exp:mail:gate --category=content --to=reader@docs.invalid,other@docs.invalid --write --mail-file-dir=var/tmp/gate-mail
```

```
Category content: allow
  r***@docs.invalid                allow
  o***@docs.invalid                off

Written: 1 file(s) in var/tmp/gate-mail, decision partly_blocked, result true
  var/tmp/gate-mail/1791182348-1270746661.mail
PASS
```

Die geschriebene E-Mail endet mit der Fußzeile und trägt `List-Unsubscribe` und `List-Unsubscribe-Post: List-Unsubscribe=One-Click`.

**Testen Sie niemals, indem Sie von den Webseiten aus senden.** Das Einschalten eines Newsletters auf einer Seite,
„Link zusenden“ und eine Adressänderung senden echte E-Mails über den Transport der Site.

## 11. Velocity und andere persistente Worker

Die Kategorien werden bei jeder Anfrage neu gelesen, sodass eine in der Administration angelegte Kategorie auch unter
Velocity sofort erscheint. Eine Änderung von `settings/mailpreferences.ini` oder eines Overrides erfordert, dass der
INI-Cache geleert wird (`php bin/php/ezcache.php --clear-tag=ini`), und für Velocity einen Neustart seiner Worker, wie
jede Änderung an den Einstellungen. Ein geändertes Template von `design:mailpreferences/` erfordert, dass die
Template-Caches geleert werden. Den POST nach RFC 8058 beantwortet Velocity wie jede andere Anfrage: Er benötigt keine
Sitzung.

## 12. Checkliste

1. Tabellen vorhanden: `./console exp:mail:status` zeigt fünfmal `ok`.
2. `OrganisationName` und `OrganisationAddress` ausgefüllt; der Status zeigt keine Warnung zur Fußzeile.
3. `BaseURL` korrekt; öffnen Sie einen Verwaltungslink aus `exp:mail:preferences link` in einem Browser.
4. Das Site-Geheimnis steht in der Override-Datei und in Ihrer Datensicherung.
5. Die Cronjob-Gruppe `infrequent` (oder der Teil `mailpreferences`) läuft.
6. Ihr Mailserver signiert `List-Unsubscribe` und `List-Unsubscribe-Post` mit DKIM.
7. Keine Werbe-E-Mails in der Liste „ohne Kategorie“ der Statusseite.
8. Lesen Sie [die rechtliche Checkliste](../spezifikationen/6.0/mail-einstellungen-compliance.md), einschließlich ihrer Lücken.

## Siehe auch

- [E-Mail-Einstellungen: die Anleitung für Benutzer](../../eng-us/features/6.0/mail-preferences.md)
- [E-Mail-Einstellungen: die Anleitung für Entwickler](mail-einstellungen-entwickler.md)
- [Die Spezifikation](../spezifikationen/6.0/mail-einstellungen.md) und [die Befehle](../spezifikationen/6.0/mail-einstellungen-cli.md)
- [Upgrade-Hinweise](../../eng-us/bc/6.0/mail-preferences.md)
- [Benachrichtigungen: betreiben und Probleme beheben](benachrichtigungen-administrator.md)
- [Betrieb einer Site](betrieb-einer-website.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
