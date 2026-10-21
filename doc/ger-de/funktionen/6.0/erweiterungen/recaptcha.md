# recaptcha: Datentyp für Google reCAPTCHA

Diese Seite ist für Website-Betreiber, die Spam aus Formularen fernhalten wollen. `recaptcha` fügt einen Datentyp
**reCAPTCHA** hinzu, der Formulare zum Bearbeiten von Inhalten, die Benutzerregistrierung und die
Informationssammlung (Kontakt- und Feedbackformulare) schützt. Er wurde im August 2024 für reCAPTCHA v2, PHP 8 und die
Änderungen an Datentypen von eZ 5 überarbeitet und verwendet das Composer-Paket `google/recaptcha`. Außerdem bringt er
eine Template-Überschreibung `feedback_form` für reCAPTCHA **v3** in reinem JavaScript mit.

## Einrichten

1. Aktivieren Sie die Extension (`ActiveExtensions[]=recaptcha`). Die Installation des Pakets mit Composer bringt die
   Abhängigkeit `google/recaptcha` mit.
2. Registrieren Sie sich in der Administration von Google reCAPTCHA für Ihre eigenen Schlüssel.
3. Tragen Sie sie in `settings/override/recaptcha.ini.append.php` ein: ein Paar für eine einzelne Website oder eines pro
   Hostname für mehrere. Halten Sie den geheimen Schlüssel aus der Versionsverwaltung heraus.

```ini
[Keys]
PublicKey=<your site key>
PrivateKey=<your secret key>
# several sites:
#PublicKey[www.example.com]=<site key>
#PrivateKey[www.example.com]=<secret key>
[Display]
Theme=white
OverrideLang=
[PublishSettings]
NewObjectsOnly=false
```

4. Fügen Sie der Klasse ein Attribut **reCAPTCHA** hinzu (einer Klasse für ein Kontaktformular oder der Benutzerklasse
   für die Registrierung).
5. Leeren Sie die Caches und öffnen Sie das Formular: Die Abfrage erscheint über der Schaltfläche zum Absenden.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `recaptcha.ini` | `Keys` | `PublicKey`, `PrivateKey` | Platzhalter | Ihre Schlüssel; Arrays mit dem Hostnamen als Schlüssel für Einrichtungen mit mehreren Websites |
| `recaptcha.ini` | `Display` | `Theme` | `white` | `red`, `white`, `blackglass`, `clean`, `custom`; ein nicht unterstützter Wert fällt auf `red` zurück |
| `recaptcha.ini` | `Display` | `OverrideLang` | leer | Die Sprache des Widgets erzwingen (en, nl, fr, de, pt, ru, es, tr); leer leitet sie aus der Inhaltssprache ab |
| `recaptcha.ini` | `PublishSettings` | `NewObjectsOnly` | `false` | Nur neue Objekte prüfen, sodass `user/register` geschützt ist, `user/edit` aber nicht (oder Kommentare beim Hinzufügen geschützt sind, beim Bearbeiten aber nicht) |

## Die Formular-Überschreibungen für v3

Die Extension bringt in `design/standard/override/templates/full/` zwei Template-Überschreibungen mit:
`feedback_form.tpl` und `request_form.tpl` (6. August 2024). Sie ersetzen die Standard-Feedback- und
Anfrageformulare des klassischen Designs durch ein mit Tailwind gestaltetes Formular, dessen Schaltfläche zum Absenden
eine Schaltfläche für reCAPTCHA **v3** ist:

- die Schaltfläche trägt `class="g-recaptcha"`, `data-action="submit"` und
  `data-sitekey="{ezini( 'Keys', 'PublicKey', 'recaptcha.ini' )}"`, sodass der **Website-Schlüssel aus
  `recaptcha.ini [Keys] PublicKey` kommt** (seit der Überarbeitung am selben Tag, die einen ins Template geschriebenen
  Schlüssel ersetzte);
- das Template lädt das Skript `https://www.google.com/recaptcha/api.js`;
- die gesammelten Informationen werden wie gewohnt an `content/action` gesendet.

Sie gelten für jedes Objekt, das mit den Klassen `feedback_form` und `request_form` unter dem Design angezeigt wird, das
diese Extension führt. Entfernen oder kopieren Sie sie, wenn Ihre Website ihr eigenes Formular-Markup verwendet.

**Grenze:** Die Schaltfläche für v3 erzeugt nur im Browser ein Token. Die Prüfung auf dem Server (das Paket
`google/recaptcha`) gehört zum **Datentyp**-Attribut, nicht zu diesen Überschreibungen.

## Was sich geändert hat

| Version | Datum | Änderung |
|---|---|---|
| 1.4 bis 1.4.2 | August und September 2024 | Unterstützung von v2, Schlüssel in der INI-Datei, README und Changelog in Markdown, eine Korrektur für den Start der Extension im Klasseneditor aus einer Composer-Installation, deutsche Validierungsmeldungen für den allgemeinen Gebrauch ins Englische übersetzt. |
| 1.4.3 bis 1.4.5 | 27. bis 30. September 2026 | Die Extension gibt ihre Version, Lizenz und Website an; jeder sichtbare Text ist ein Übersetzungstext mit Deutsch; die Beschreibung nennt Exponential. |

[hcaptcha](hcaptcha.md) ist dieselbe Extension für den Dienst hCaptcha.

## Verwandte Seiten

- [hcaptcha](hcaptcha.md)
- [Chronik](../../../geschichte/erweiterungen/recaptcha.md) und [Versionshinweise](../../../../changelogs/extensions/recaptcha.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/recaptcha.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2024-08](../../../geschichte/erweiterungen/monate/2024-08.md), [2024-09](../../../geschichte/erweiterungen/monate/2024-09.md), [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
