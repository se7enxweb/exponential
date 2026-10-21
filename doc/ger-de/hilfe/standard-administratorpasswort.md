---
title: Das Standard-Administratorpasswort
audience: admin, developer
category: administration
order: 31
summary: Mit welchem Administrator-Login und Passwort eine neue Installation beginnt, für jeden Installationsweg (Einrichtungsassistent, Kickstarter, Konsoleninstallation, die mitgelieferte Datenbank-Grundausstattung, Pakete), und warum es sofort geändert werden muss.
keywords: Standardpasswort, Administratorpasswort, Administrator, Einrichtungsassistent, Installation, erste Anmeldung, bekanntes Passwort, schwaches Passwort, default password, admin password, administrator, publish, initial-admin-password, setup wizard, kickstarter, kickstart.ini, exp:install, install, first login, well-known password, weak password, WeakPasswordList, WeakPasswordCheck, cleandata, db_data.dba
featured: true
context: setup/init
type: help
---
# Das Standard-Administratorpasswort

Diese Seite richtet sich an alle, die Exponential installieren, und an Administratoren, die eine Installation
übernehmen. Jede Installation hat zu Beginn ein Administratorkonto: Login **`admin`** (Benutzerobjekt 14, in der Gruppe
**Administrator users**, mit der Rolle **Administrator**). Wie sein Passwort lautet, hängt davon ab, wie die Site
installiert wurde. Diese Seite nennt es für jeden Weg, so wie der Code von Exponential 6.0.15 es handhabt.

> **Warnung:** Was auch immer der Installer gesetzt hat: Ändern Sie das Administratorpasswort gleich nach der ersten
> Anmeldung, geben Sie dem Konto eine echte E-Mail-Adresse und löschen Sie `var/log/initial-admin-password`, falls
> vorhanden. Legen Sie dann ein persönliches Administratorkonto an und stellen Sie `admin` als Anmeldung still
> ([Kontenhygiene](#kontenhygiene)). Wie Sie es ändern:
> [Das Administratorpasswort ändern](administratorpasswort-aendern.md).

## Kurz gesagt

| Wie die Site installiert wurde | Login | Passwort nach der Installation |
|---|---|---|
| [Einrichtungsassistent](#der-einrichtungsassistent) im Browser | `admin` | das im Assistenten eingegebene; es gibt keinen Standard |
| [Kickstarter](#der-kickstarter-kickstartini) (`exp:kickstarter run` mit `kickstart.ini`) | `admin` | `[site_admin] Password=` aus `kickstart.ini`; ist es leer, kürzer als `MinPasswordLength` oder ein bekanntes Wort, ein zufälliges Passwort mit 20 Zeichen, einmal ausgegeben und in `var/log/initial-admin-password` geschrieben |
| [Konsoleninstallation](#die-konsoleninstallation-expinstall) (`exp:install`) | `admin` | `--password=`, wenn es lang genug und kein bekanntes Wort ist; sonst ein zufälliges Passwort mit 24 Zeichen, am Ende ausgegeben und in `var/log/initial-admin-password` geschrieben |
| [Die mitgelieferte Datenbank-Grundausstattung](#die-mitgelieferte-datenbank-grundausstattung-publish), von Hand ohne Installer geladen | `admin` | **`publish`**: der historische Standard, allgemein bekannt |
| [Site-Pakete](#site-pakete-und-demo-inhalte) (die 7x- und Community-Pakete) | `admin` | keine Änderung: Pakete enthalten keine Konten; der obige Installer setzt das Passwort |

Kein Installer von Exponential 6 lässt `publish` bestehen. Es überlebt nur dort, wo die Datenbank ohne Installer aus
der Grundausstattung erstellt wurde oder wo der Assistent angewiesen wurde, eine bestehende Datenbank zu behalten, die
es noch enthielt.

## Der Einrichtungsassistent

Der Web-Einrichtungsassistent (`setup/init`, [Kapitel 5](../installation/05-setup-assistent.md)) hat einen Schritt für den
Administrator: Vorname, Nachname, E-Mail-Adresse und das zweimal eingegebene Passwort. Der Login ist immer `admin`.

- Es gibt **kein Standardpasswort**. Der Schritt lehnt ein leeres Passwort ab, zwei unterschiedliche Passwörter, eines
  kürzer als `[UserSettings] MinPasswordLength` (10 im Auslieferungszustand) und seit 6.0.15 ein bekanntes oder
  häufiges Passwort jeder Länge: „Dieses Passwort ist bekannt oder zu häufig, jeder könnte es erraten. Bitte wählen Sie
  ein anderes.“ Das umfasst die Wörter der [gemeinsamen Liste](#die-liste-bekannter-passwörter) und ihre einfachen
  Abwandlungen, etwa ein häufiges Wort mit Ziffern oder `!` am Ende. Wählen Sie ein langes, zufälliges Passwort; ein
  Passwortmanager kann eines erzeugen.
- Haben Sie gewählt, **die vorhandenen Daten** einer Datenbank zu **behalten**, wird der Schritt übersprungen, und das
  Administratorkonto bleibt, wie es in dieser Datenbank ist. Eine aus der mitgelieferten Grundausstattung erstellte
  Datenbank hat dann noch `publish`: Ändern Sie es sofort.

## Der Kickstarter (kickstart.ini)

Der Kickstarter führt dieselben Schritte ohne Browser aus, gesteuert durch `kickstart.ini`
([Kapitel 6](../installation/06-kickstarter.md)):

```ini
[site_admin]
Continue=true
FirstName=Admin
LastName=User
Email=you@example.com
Password=
```

- **Leeres oder fehlendes `Password=`** (wie in den mitgelieferten `kickstart.ini-dist` und `kickstart.ini--example`):
  Der Installer erzeugt ein zufälliges Passwort mit 20 Zeichen (Buchstaben und Ziffern ohne die leicht verwechselbaren),
  gibt es auf der Konsole aus und schreibt es mit Modus `0600`, nur für den Eigentümer, in
  `var/log/initial-admin-password`.
- **Ein bekanntes oder häufiges Passwort** (die [gemeinsame Liste](#die-liste-bekannter-passwörter) mit ihren
  Abwandlungen) wird auf dieselbe Weise ersetzt.
- **Ein Passwort kürzer als `[UserSettings] MinPasswordLength`** (10 im Auslieferungszustand) wird seit 6.0.15 auf
  dieselbe Weise ersetzt; vorher installierte der Kickstarter es unverändert. Das sind die Regeln, die `exp:install`
  anwendet.
- Jeder andere Wert wird unverändert installiert. Lassen Sie `Password=` leer und verwenden Sie das erzeugte, oder
  tragen Sie dort einen langen Zufallswert ein.
- `kickstart.ini` enthält Passwörter im Klartext: Legen Sie die Datei mit Modus `0600` an, committen Sie sie nie und
  entfernen Sie sie nach der Installation. Das Protokoll des Laufs, `var/log/kickstart.log`, maskiert Passwörter.

## Die Konsoleninstallation (exp:install)

`./console exp:install` (oder `php bin/php/install.php`) schreibt aus seinen Optionen eine `kickstart.ini` und führt
den Kickstarter aus ([Kapitel 7](../installation/07-installation-ueber-die-konsole.md#75-das-passwort-des-administrators)):

- **Ohne `--password`** oder mit `--random-password`: ein zufälliges Passwort mit 24 Zeichen.
- **Mit `--password=<value>`**: verwendet, wenn es mindestens `MinPasswordLength` (10) Zeichen lang ist und nicht auf
  der [gemeinsamen Liste](#die-liste-bekannter-passwörter) steht; sonst wird es durch ein zufälliges ersetzt, und die Zusammenfassung nennt den Grund. Ein leeres `--password=`
  wird abgelehnt.
- Ein erzeugtes Passwort wird einmal in der Zusammenfassung am Ende ausgegeben und in `var/log/initial-admin-password`
  geschrieben (Modus `0600`). Die Zusammenfassung fordert Sie auf, es zu ändern und diese Datei zu löschen. Ein von
  Ihnen angegebenes Passwort wird einmal angezeigt und nirgends im Klartext gespeichert.
- `var/log/exp-install-<date>.ini`, die verwendete Konfiguration, maskiert jedes Passwort.

Nehmen Sie das Passwort in einer Pipeline aus Ihrem Secret-Store (`--password="${ADMIN_PASSWORD}"`), oder lassen Sie
es erzeugen und holen Sie es aus der Zusammenfassung; schreiben Sie es nie in ein Skript oder ein Image.

## Die Liste bekannter Passwörter

Seit 6.0.15 wird überall, wo ein Passwort gesetzt wird, eine einzige Liste verwendet, `expWeakPasswordList`
(`kernel/classes/expweakpasswordlist.php`): im Einrichtungsassistenten, im Kickstarter, in `exp:install`, auf der Seite
**Passwort ändern**, bei der Registrierung, im Bearbeitungsformular für Benutzer, in `exp:resetuserpassword` und in den
Webdiensten. Sie enthält:

- die eigenen Namen der Software und alte Standardwerte (`publish`, `exponential`, `ezpublish`, `demo`), Kontonamen
  (`admin`, `administrator`, `root`, `test`), die Klassiker (`password`, `changeme`, `secret`, `welcome`, `letmein`,
  `qwerty`, `123456` ...);
- ihre einfachen Abwandlungen: beliebige Groß- und Kleinschreibung, Ziffern oder Sonderzeichen am Ende oder am Anfang,
  Leerzeichen, `-` oder `_` dazwischen, Buchstabentausch wie `p@ssw0rd`, die Endungen `-s`, `-es`, `-ed`, `-er`,
  `-ers`, `-ing`, `-ings` und ein doppelt eingegebenes Wort;
- wiederholte Zeichen und Folgen auf der Tastatur, im Alphabet oder bei den Ziffern (`aaaaaaaaaa`, `qwertyuiop`,
  `1234567890`).

Ein Passwort wird als Ganzes verglichen, daher wird eine lange Passphrase, die ein solches Wort nur enthält, akzeptiert.
Eigene Wörter (den Namen des Unternehmens, den Namen der Site) fügen Sie in einem Override hinzu:

```ini
[UserSettings]
WeakPasswordList[]=ourcompany
WeakPasswordFile=settings/weak-passwords.txt   # one word per line, # for comments
```

`WeakPasswordCheck=disabled` schaltet die Prüfung für die Formulare ab; die Installer prüfen immer. Ein bestehendes
Passwort wird bei der Anmeldung nie abgelehnt: Die Liste greift, wenn ein Passwort gesetzt wird. Einzelheiten:
[der Upgrade-Hinweis](../bc/6.0/schwache-passwoerter-und-gueltigkeit-von-reset-links.md).

## Die mitgelieferte Datenbank-Grundausstattung: publish

Die saubere Datenbank des Kernels, die Daten, mit denen jede Installation beginnt, enthält das Administratorkonto mit
dem Passwort **`publish`**, gespeichert als bcrypt-Hash (Typ 7):

- `share/db_data.dba`
- `kernel/sql/common/cleandata.sql`
- `kernel/sql/sqlite/cleandata.sql`

Das ist der historische Standard der Software, und er steht in jeder alten Anleitung, daher ist er das erste Passwort,
das jeder ausprobiert. Die obigen Installer überschreiben es immer, wenn sie die Site anlegen. Es ist noch das Passwort,
wenn:

- eine Datenbank durch manuelles Laden dieser Dateien erstellt wurde (mit `exp:ezsqlinsertschema`, einem
  Datenbank-Client oder einem Testaufbau), ohne anschließend einen Installer auszuführen;
- der Einrichtungsassistent angewiesen wurde, eine so erstellte bestehende Datenbank zu behalten;
- eine alte Installation (3.x, 4.x, 5.x legacy) migriert wurde, deren Administrator es nie geändert hat.

Prüfen und ändern Sie es sofort:

```bash
./console exp:resetuserpassword --allow-root-user -u admin -g -l 24
```

Kein Installer von Exponential 6 setzt ein anderes festes Passwort. Ein häufiges Wort, das die Regel von 10 Zeichen
gerade so erfüllt, ist genauso leicht zu erraten: Verwenden Sie keines auf einer von außen erreichbaren Site, nicht
einmal für einen Test.

## Site-Pakete und Demo-Inhalte

Die Site-Pakete, die die Installer herunterladen (zum Beispiel die 7x- und CJW-Pakete und die Community-Pakete), und
die Pakete in `var/storage/packages/` enthalten **keine Benutzerkonten**: Der Administrator stammt aus der
Datenbank-Grundausstattung und erhält sein Passwort im Administratorschritt des Installers. Ein Paket, das doch ein
Benutzerkonto enthält, installiert es mit dem im Paket enthaltenen Passwort-Hash; prüfen Sie daher jedes Paket eines
Drittanbieters, bevor Sie es installieren.

Für Entwickler: Die Paketinstaller-API (`eZSiteInstaller::updateContentObjectAttributes()`) gibt einem Benutzerobjekt,
das sie über ein Installationsskript anlegt, einen zufälligen Login und das Passwort `publish`. Kein mitgeliefertes
Paket verwendet sie für Benutzer; ein eigenes Paket, das dies tut, sollte danach ein echtes Passwort setzen.

Eine öffentliche Demonstrations-Site darf eine Anmeldung für ihre Besucher veröffentlichen. Eine solche Site läuft mit
der Umgebungsvariable `EXP_DEMO_MODE=1` ([Demo-Modus](demomodus.md)): Die Installer akzeptieren dann das
veröffentlichte Passwort, obwohl es bekannt oder kürzer als `MinPasswordLength` ist (ein leeres nie), und niemand kann ein Administratorpasswort ändern oder zurücksetzen, solange die Variable
gesetzt ist. Ein solches Passwort darf nie auf einer echten Site wiederverwendet und die Variable für eine solche nie
gesetzt werden.

## Nach der ersten Anmeldung

1. Melden Sie sich an der Administration an (`/admin/user/login` beim standardmäßigen URL-Zugriff) als `admin` mit dem
   obigen Passwort.
2. Ändern Sie das Passwort: [Das Administratorpasswort ändern](administratorpasswort-aendern.md).
3. Geben Sie dem Konto eine echte, überwachte E-Mail-Adresse, damit **Passwort vergessen?** und die E-Mail „Ihr
   Passwort wurde geändert“ Sie erreichen.
4. Löschen Sie die Passwortdatei: `rm var/log/initial-admin-password`.
5. Entfernen Sie `kickstart.ini`, falls Sie eine verwendet haben.
6. Folgen Sie der [Kontenhygiene](#kontenhygiene).

[Kapitel 10.1](../installation/10-nach-der-installation.md#101-erste-anmeldung-und-das-administratorpasswort) enthält dieselben
Schritte in der Installationsanleitung.

## Kontenhygiene

1. Legen Sie ein persönliches Administratorkonto mit einem anderen Login als `admin` an (unter **Benutzer** >
   **Administrator users**, das die Rolle **Administrator** trägt), eines pro Person.
2. Schalten Sie die zweistufige Anmeldung für jeden Administrator ein.
3. Stellen Sie `admin` als Anmeldung still: Deaktivieren Sie es (**Benutzerkonten-Einstellungen konfigurieren** >
   Häkchen bei **Benutzerkonto aktivieren** entfernen, nachdem Sie `[UserSettings] UserCreatorID` auf einen aktivierten
   Administrator gerichtet haben), oder lassen Sie es aktiviert, mit einem langen Zufallspasswort, das niemand
   aufbewahrt.
4. Halten Sie ein schriftliches, getestetes Notfallverfahren bereit: root auf dem Server plus
   `./console exp:resetuserpassword --allow-root-user`.

Die Schritte, und warum `UserCreatorID` wichtig ist, stehen unter
[Das Administratorpasswort ändern: Kontenhygiene](administratorpasswort-aendern.md#kontenhygiene).

## Verwandte Seiten

- [Das Administratorpasswort ändern](administratorpasswort-aendern.md)
- [Sicherheitshärtung](sicherheitshaertung.md)
- [Kapitel 4: Eine Installationsmethode wählen](../installation/04-installationsweg-waehlen.md),
  [Kapitel 5: der Einrichtungsassistent](../installation/05-setup-assistent.md), [Kapitel 6: der Kickstarter](../installation/06-kickstarter.md),
  [Kapitel 7: die Konsoleninstallation](../installation/07-installation-ueber-die-konsole.md)
- [Kapitel 13: Sicherheitshärtung für die Produktion](../installation/13-sicherheitshaertung.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
