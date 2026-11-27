# Der Setup-Assistent auf einer installierten Website: CheckValidity=true lässt ihn wieder laufen (6.0.15)

Früher Code von 6.0.15 verweigerte den Setup-Assistenten auf einer installierten Website: Mit
`[SiteAccessSettings] CheckValidity=true` zeigte er „Diese Website ist bereits installiert" und bot keinen Weg weiter,
es sei denn, `[SetupSettings] AllowWizardOnInstalledSite=enabled` war zusätzlich gesetzt. `CheckValidity=true` zu setzen
ist der ausdrückliche Wunsch eines Administrators nach dem Assistenten; das war also eine Regression. Ab dieser Änderung
gilt:

| `AllowWizardOnInstalledSite` | Installierte Website, `CheckValidity=true` | Installierte Website, normal ausgeliefert (`setup/init` in der Administration) |
|---|---|---|
| `auto` (die neue Vorgabe) | die Erklärungsseite mit **Einrichtungsassistenten trotzdem ausführen** | nur die Erklärungsseite |
| `enabled` | die Erklärungsseite mit der Schaltfläche | die Erklärungsseite mit der Schaltfläche |
| `disabled` | nur die Erklärungsseite | nur die Erklärungsseite |

Eine neue Installation ist nicht betroffen: Der Assistent läuft wie bisher. Ein anderer Wert als diese drei gilt als
`auto`.

## Was bleibt, wie es war

- **Vor der Bestätigung wird nichts geschrieben.** Den Assistenten zu öffnen, die Erklärungsseite und die Schaltfläche
  ändern nichts: keine Wartungsmarkierung, kein Lauf in `var/log/setup.log`. Die Schaltfläche führt zur
  Bestätigungsseite („Diese Website neu installieren?"), und erst deren POST mit dem einmaligen Token startet den Lauf. Das Token gehört
  zu einem Cookie dieses Browsers (`exp_setup_confirm`, 15 Minuten), da der Assistent vor der Sitzung und der Datenbank
  läuft. Jede Seite des Assistenten wird mit `Cache-Control: no-store, private` gesendet, damit kein Cache eine
  Token-Seite behält.
- **Die Wartungsfrist.** Nach der Bestätigung ist die ganze Website im Wartungsmodus, bis der Assistent fertig ist oder
  15 Minuten lang verlassen wurde (`expMaintenance::WIZARD_LEASE`); ein Schritt, der nach Ablauf der Frist gesendet
  wird, führt zurück zur Bestätigungsseite.
- **Die Erklärung.** Die Seite sagt, dass die Website installiert ist und dass sie mit dem Assistenten, dem
  Kickstarter (`kickstart.ini`, dann `php bin/php/kickstarter.php run --apply`) oder einer frischen Installation neu
  installiert wird.

## Die Formulare des Assistenten senden an die Wurzel der Website

Über die Gültigkeitsprüfung ist jede Adresse der Website der Assistent (`index.php` übergibt ihm jede Anfrage). Die
Erklärungsseite, die Bestätigungsseite und jeder Schritt senden jetzt an die Wurzel der Website (`/`), nicht mehr an
`/setup/init`: `/setup/...` ist die Adresse eines Administrationsmoduls, die eine Firewall im Frontend zu Recht
abweist. Mit den Exponential-Regeln von request-shield beantwortete `EXP-MODULES` sie mit 404; übernehmen Sie die
Regel `EXP-SETUP-POST` (`allow POST /`) aus `rules/exponential.rules` von exp_request_shield, sonst beantwortet
`EXP-POST` den POST des Assistenten an `/` mit 405.

## Was zu tun ist

- **Eine Live-Website:** Behalten Sie `CheckValidity=false` bei, wie der Installer es schreibt. Damit der Assistent nie
  läuft, auch wenn jemand `CheckValidity=true` setzt, setzen Sie `AllowWizardOnInstalledSite=disabled` in
  `settings/override/site.ini.append.php`.
- **Mit dem Assistenten neu installieren:** Setzen Sie `CheckValidity=true`, öffnen Sie die Wurzel der Website, wählen
  Sie **Einrichtungsassistenten trotzdem ausführen**, dann **Einrichtungsassistenten starten**. Am Ende setzt der
  Assistent `CheckValidity=false` wieder.
- **Hinter request-shield:** Ergänzen Sie `EXP-SETUP-POST`, wenn Ihre Regeln eine Kopie eines älteren
  `exponential.rules` sind.

Code: `kernel/classes/expsetupwizardguard.php` (`mode()`, `allowedOnInstalledSite()`, `decide()`, `formAction()`),
`kernel/private/classes/views/setup/ezsetup.php`, `design/standard/templates/setup/init/installed.tpl` und
`confirm_reinstall.tpl`. Tests: `tests/tests/kernel/classes/setup/SetupWizardGuardTest.php` (ohne Datenbank).
