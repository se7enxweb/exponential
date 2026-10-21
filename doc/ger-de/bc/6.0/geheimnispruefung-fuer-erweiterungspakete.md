# Die Prüfung von Erweiterungspaketen auf Geheimnisse

Seit 6.0.15 nimmt kein Paket eine Erweiterung auf, die geheime Werte oder Schlüsselmaterial enthält. Setup >
Extensions, der Paket-Assistent und `ezpm.php add` fragen alle `eZExtensionPackageHandler::packRefusal()`, und
`addExtension()` fragt auf der Kopie, die es anlegt, erneut. Eine abgelehnte Erweiterung wird im Hinweis der Seite mit
ihren ersten fünf Funden genannt: die Datei und die Einstellung (oder die Art des Geheimnisses), nie der Wert.

## Was abgelehnt wird

| Fund | Was er bedeutet |
|---|---|
| `<file>: <Setting>` | Eine Einstellung, deren Name ein Geheimnis kennzeichnet (`Password`, `ApiKey`, `ClientSecret`, `Token` ...), hat einen Wert, oder eine andere Einstellung trägt ein Passwort in ihrem Wert (`Source=https://user:pw@host/`). |
| `<file>: (private key)` | Ein Block eines privaten Schlüssels (`-----BEGIN ... PRIVATE KEY-----`), in jeder Datei, Text oder binär. Wird immer abgelehnt. |
| `<file>: (bearer token)`, `(API key)`, `(constant)`, `(address with a password)` | Die Form eines Geheimnisses in beliebigem Text: `Authorization: Bearer ...`, ein Token eines Dienstes (`sk-...`, `AKIA...`, `ghp_...`), `define( 'DB_PASS', '...' )`, `scheme://user:password@host`. |
| `<file>: key material` | Eine Schlüsseldatei anhand ihres Namens: `*.key`, `*.p12`, `*.pfx`, `*.jks`, `*.keystore`, `*.kdbx`, `*.p8`, `*.ppk`, `*.gpg`, `.env`, `id_rsa`, `credentials*`, `*service-account*`, eine Datei unter `jwt/` oder `.ssh/`. Wird immer abgelehnt. |
| `<file>: copy of a settings file` | Eine Sicherungskopie einer Einstellungsdatei (`*.bak`, `*.orig`, `*~` ...). Wird immer abgelehnt: Löschen Sie die Kopie. |

Einstellungsdateien (unter `settings/` oder mit dem Namen `*.ini*`) werden als Text gelesen, einschließlich
auskommentierter Zeilen, sodass auch ein Passwort gefunden wird, das eZINI überspringen würde.

Ein Verzeichnis `node_modules` (die Abhängigkeiten eines Builds, etwa des Editors von exp_oe_tiptap) wird weder gepackt
noch gelesen: Die Erweiterung läuft aus ihren Design-Dateien, und die Testzertifikate einer Bibliothek gehören nicht
zur Erweiterung.

## Was durchgeht

Die Prüfung nimmt diese Werte für das, was sie sind, unter jedem Namen:

- nichts oder ein Platzhalter: `...`, `xxx`, `***`, `<your key>`, `Enter your private key here`;
- ein Schalter: `enabled`, `disabled`, `true`, `false`, `none`, `null`, `0` (`ezformtoken=disabled`, `Auth=none`);
- ein Template-Platzhalter: `%password%`, `{password}`, `${DB_PASSWORD}`, `{{ secret }}`, `%env(DB_PASS)%`, und ein
  Wert, der erst bei der Verwendung zusammengesetzt wird (`key: composer-${{ matrix.php }}`);
- ein Maß oder eine Version unter einem Namen, der das sagt (`MaxTokens=4096`, `IEDisableWithPassword[]=11`);
- eine Adresse, ein Header-Name, ein Typ, Modus oder eine Methode (`TokenURL=https://...` ohne Passwort darin,
  `ApiKeyHeader=Authorization`) und ein öffentlicher Schlüssel (`PublicKey=...`, ein Site-Key eines Captchas);
- ein Zertifikat oder ein öffentliches Zertifikatsbündel (`cacert.pem`): `*.pem`, `*.crt`, `*.der` und `*.asc` werden
  nur wegen eines Blocks eines privaten Schlüssels darin abgelehnt;
- Beispieltext: In einem Kommentar, einer Konstante oder einer Adresse eines Dokuments oder Tests steht ein Wort aus der
  Dokumentation für den Wert (`# Password=changeme`, `define( 'DB_PASS', 'dbpassword' )`, `https://user:pass@...`,
  `abc:xyz@`), ebenso Test-Tokens (`Bearer test-...`, `sk-test-SECRET...`). Ein Kommentar, der eine Einstellung in
  Worten erklärt, ist kein Wert.

Eine aktive Einstellung wird beim Wort genommen: `Password=secret` und `Password=changeme` werden abgelehnt, weil sie
das verwendete Passwort sein können.

## Der Prüfung ein Beispiel mitteilen: package_check.ini

Lehnt die Prüfung einen Wert ab, der nur ein Beispiel ist (ein schlüsselförmiger Wert in einem Kommentar der
Dokumentation, eine Beispielkonfiguration), führt der Maintainer der Erweiterung ihn in einer einfachen Datei
`package_check.ini` im Wurzelverzeichnis der Erweiterung auf:

```ini
[PackageCheck]
# The example keys of the documentation comment
AllowedFindings[]=settings/recaptcha.ini.append.php:PrivateKey
# Example addresses in the tests
AllowedFindings[]=tests/:(address with a password)
```

- Ein Fund pro Zeile, so wie die Ablehnung ihn nennt: `<path below the extension>:<setting name or (kind)>`. Ein Pfad,
  der mit `/` endet, deckt dieses Verzeichnis ab.
- Die Datei lässt nur aus, was sie nennt. Ein Pfad mit `..`, einem führenden `/` oder einem Platzhalterzeichen wird
  ignoriert, ebenso die Datei, wenn sie ein symbolischer Link oder größer als 64 KB ist.
- Sie kann nie Schlüsselmaterial, einen Block eines privaten Schlüssels, eine Kopie einer Einstellungsdatei, einen
  Link oder eine Datei durchlassen, die nicht gelesen werden konnte.
- Die Datei wird mit der Erweiterung gepackt und auf der Kopie im Paket erneut gelesen.

Stellen Sie vor dem Hinzufügen eines Eintrags sicher, dass der Wert wirklich ein Beispiel ist: Ein hier aufgeführtes
echtes Geheimnis verlässt den Server in jedem Paket.

## Tests

`tests/tests/kernel/classes/setup/ExtensionDownloadTest.php`: die Regressionstabelle abgelehnter Eingaben, die Tabelle
der Eingaben, die gepackt werden, die Datei mit Ausnahmen und die mit dem Kernel ausgelieferten Erweiterungen (alle
müssen sich packen lassen).
