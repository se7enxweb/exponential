# hcaptcha: Datentyp für hCaptcha

Diese Seite ist für Website-Betreiber, die Spam mit hCaptcha statt mit reCAPTCHA von Google aus Formularen fernhalten
wollen. `hcaptcha` fügt einen Datentyp **hCaptcha** hinzu, das Gegenstück zu [recaptcha](recaptcha.md). Fügen Sie das
Attribut einer Inhaltsklasse hinzu, und das Formular bittet den Besucher, die Aufgabe zu lösen, bevor es angenommen
wird: beim Bearbeiten von Inhalten, bei der Benutzerregistrierung und bei der Informationssammlung. Die Anleitung des
Projekts hCaptcha zum Umstieg steht unter docs.hcaptcha.com/switch.

Die Extension beruht auf einer ursprünglichen Lösung eines früheren Mitwirkenden und wurde am 11. August 2024 als 1.0
veröffentlicht. **Für hCaptcha wird kein Composer-Paket gebraucht** (anders als bei recaptcha).

## Einrichten

1. Aktivieren Sie die Extension (`ActiveExtensions[]=hcaptcha`).
2. Registrieren Sie sich im Dashboard von hCaptcha und legen Sie eine Website an, um Ihre Schlüssel zu erhalten.
3. Tragen Sie sie in `settings/override/hcaptcha.ini.append.php` ein:

```ini
[Keys]
PublicKey=<your site key>
PrivateKey=<your secret key>
[Display]
Theme=white
OverrideLang=
[PublishSettings]
NewObjectsOnly=false
```

4. Leeren Sie die Caches und fügen Sie dann der Klasse ein Attribut **hCaptcha** hinzu.

Die Einstellungen bedeuten dasselbe wie bei [recaptcha](recaptcha.md#set-it-up): Schlüssel pro Host als Arrays,
`Theme`, `OverrideLang` und `NewObjectsOnly`.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `hcaptcha.ini` | `Keys` | `PublicKey`, `PublicKey[<host>]` | Platzhaltertext `Enter your Public Key here` | Der Website-Schlüssel; einer pro Host, wenn Sie mehrere Websites betreiben |
| `hcaptcha.ini` | `Keys` | `PrivateKey`, `PrivateKey[<host>]` | Platzhaltertext `Enter your Private Key here` | Der geheime Schlüssel, nur auf dem Server verwendet |
| `hcaptcha.ini` | `Display` | `Theme` | `white` | Theme des Widgets |
| `hcaptcha.ini` | `Display` | `OverrideLang` | leer | Sprachcode des Widgets (`en`, `de`, `fr`, ...); leer ermittelt ihn aus der Sprache des Objekts |
| `hcaptcha.ini` | `PublishSettings` | `NewObjectsOnly` | `false` | Nur neue Objekte prüfen, keine erneuten Bearbeitungen |

Die Kommentare der mitgelieferten INI-Datei sprechen noch vom Dienst von Google (sie wurde von recaptcha kopiert). Der
Code spricht mit hCaptcha: Das Widget-Skript wird von `js.hcaptcha.com` geladen, und die Antwort wird auf dem Server
unter `https://hcaptcha.com/siteverify` von `classes/hcaptchalib.php` geprüft. Prüfen:

```bash
grep -n hcaptcha.com extension/hcaptcha/classes/hcaptchalib.php
```

## Grenze: Die Formular-Überschreibungen sind die von recaptcha

`design/standard/override/templates/full/feedback_form.tpl` und `request_form.tpl` sind dieselben Dateien wie in
[recaptcha](recaptcha.md#the-v3-form-overrides). Sie lesen `ezini( 'Keys', 'PublicKey', 'recaptcha.ini' )` und laden
`recaptcha/api.js` von Google, **nicht** das von hCaptcha. Sind beide Extensions aktiv und verwenden Sie diese
Formularklassen, verwenden die Formulare die Einstellungen von reCAPTCHA; der Datentyp hCaptcha hat sein eigenes
Template. Prüfen:

```bash
grep -n 'ezini\|google' extension/hcaptcha/design/standard/override/templates/full/feedback_form.tpl
```

Um hCaptcha in diesen Formularen zu verwenden, entfernen Sie die Überschreibungen aus Ihrem Design oder überschreiben Sie
sie in Ihrer eigenen Extension.

## Was sich geändert hat

| Version | Datum | Änderung |
|---|---|---|
| | 6. August 2024 | Das Repository begann als Kopie von recaptcha, daher sind seine ersten Commits die von recaptcha. Einer davon fügte `google/recaptcha` zu `composer.json` hinzu; die folgende Massenaktualisierung entfernte diese Abhängigkeit wieder. `composer.json` verlangt jetzt nur PHP 7.2 oder neuer und den Legacy-Installer. |
| 1.0 | 11. August 2024 | Das Innenleben wurde für die API von hCaptcha überarbeitet, getestet mit Exponential 6; Metadaten zur Finanzierung. |
| 1.1 | 30. September 2026 | Die Info-Seite nennt die Extension „hCaptcha extension for Exponential“; die Beschreibung nennt Exponential. |
| 1.2 | 2. Oktober 2026 | `extension.xml`, das der Extension fehlte, wurde hinzugefügt, ebenso die Website in `ezinfo.php`, sodass die Info-Seite und die Aktualisierungsprüfungen Version, Lizenz und Website zeigen. |

## Verwandte Seiten

- [recaptcha](recaptcha.md)
- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../../eng-us/history/extensions/hcaptcha.md) und [Versionshinweise](../../../../changelogs/extensions/hcaptcha.md)
- [Änderungsverzeichnis](../../../../eng-us/history/ledger/hcaptcha.md)
- [Verhaltensänderungen der Extensions](../../../../eng-us/bc/6.0/extensions-behaviour-changes.md)
- Monate: [2024-08](../../../../eng-us/history/extensions/months/2024-08.md), [2026-10](../../../../eng-us/history/extensions/months/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
