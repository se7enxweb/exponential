# E-Mail-Einstellungen: Kategorien, das Mail-Gate und Tests

Diese Anleitung richtet sich an Entwickler, die Mails aus einer Extension oder aus Kernel-Code versenden. Am Ende hat Ihre Extension eine
eigene Kategorie, die Personen auf ihrer Einstellungsseite ein- und ausschalten, einen Handler, der Ihre älteren Abonnementdaten
als Zustand dieser Kategorie liest, Mails, die mit der Fußzeile und den Headern für die Abmeldung mit einem Klick durch das Gate gehen,
und einen Test, der jede Mail in Dateien festhält. Die Einstellungen und Tabellen stehen in [der Spezifikation](../spezifikationen/6.0/mail-einstellungen.md);
der Betrieb des Systems steht in [der Anleitung für Administratoren](mail-einstellungen-administrator.md).

Das Beispiel dieser Anleitung wurde am 2026-10-04 auf einer Test-Installation ausgeführt, mit dem Datei-Transport und Adressen unter
`.invalid`; seine Ausgabe ist so gezeigt, wie sie kam, wobei Host, Absender und Site-Name durch Beispielwerte ersetzt sind.

## 1. Das Modell in fünf Sätzen

1. Jede Mail gehört zu einer **Kategorie**: einer Kennung wie `newsletter` oder `security`, deklariert mit
   `eZMail::setCategory()` (oder dem Header `X-Exp-Mail-Category`).
2. Eine Kategorie ist **unverzichtbar** (wird immer gesendet: Kontosicherheit, Bestellungen, rechtliche Hinweise, Warnungen an Administratoren) oder **optional**
   (wird nur an Personen gesendet, die sie eingeschaltet haben; standardmäßig aus).
3. `eZMailTransport::send()` übergibt jede Mail an `expMailGate::dispatch()`, bevor der Transport sie sieht, und zwar bei jedem
   Transport.
4. Bei optionalen Mails fragt das Gate für jeden Empfänger `expMailPreferences::decision()` (`allow`, `off`, `pending`,
   `master_off`, `suppressed`), sendet jedem zugelassenen Empfänger eine Mail mit der Fußzeile und den Headern `List-Unsubscribe` und
   `List-Unsubscribe-Post`, protokolliert die anderen und gibt `true` zurück, selbst wenn alle blockiert wurden.
5. Mails ohne Kategorie werden unverändert gesendet und als „uncategorised“ mit der Datei protokolliert, die sie gesendet hat, es sei denn, dieser
   Absender ist in `[GateSettings] EssentialSenders[]` aufgeführt.

## 2. Mails mit einer Kategorie senden

```php
$mail = new eZMail();
$ini = eZINI::instance();
$mail->setSender( $ini->variable( 'MailSettings', 'EmailSender' ) ?: $ini->variable( 'MailSettings', 'AdminEmail' ) );
foreach ( $addresses as $address )
    $mail->addBcc( $address );
$mail->setSubject( $subject );
$mail->setBody( $text );
$mail->setCategory( 'events' );          // the gate does the rest
eZMailTransport::send( $mail );
expMailGate::lastResult();               // category, decision, sent, blocked (reasons), result
```

Sie filtern die Empfänger nicht selbst, fügen keinen Abmeldelink hinzu und prüfen nicht die Sperrliste: Das Gate erledigt alle drei Dinge. Nehmen Sie
jeden auf, der die Mail möglicherweise haben möchte. Regeln:

- **Setzen Sie immer einen Absender.** Das Gate ändert From, Reply-To und Subject nicht, und ein leeres From erreicht den Transport
  so, wie es ist.
- **Geben Sie der Mail eine Kategorie oder führen Sie sie als unverzichtbar auf.** Mails ohne Kategorie haben keine Fußzeile und keinen Abmeldelink. Eine
  Passwortzurücksetzung oder eine Bestellbestätigung ist `security` oder `orders`.
- **Teilen Sie die Liste nicht selbst in eine Mail pro Empfänger auf**: Mit `[GateSettings] SplitRecipients=enabled` (dem
  Standard) erledigt das das Gate, und jede Kopie trägt den eigenen Link des Empfängers.
- **Prüfen Sie früh, wenn die Arbeit aufwendig ist.** Das Gate entscheidet zum Zeitpunkt des Versands; wenn das Erstellen einer Mail viel kostet (eine
  Zusammenfassung, ein gerenderter Newsletter), fragen Sie vorher und überspringen Sie Personen, die ohnehin blockiert würden:
  `expMailPreferences::forRecipient( expMailRecipient::fromUser( $user ) )->allows( 'events' )`.
- **HTML-Mails**: Setzen Sie den Inhaltstyp `text/html`; die Fußzeile kommt vor `</body>`. Ein als `ezcMailPart`
  (Multipart) übergebener Mailtext erhält die Fußzeile in seinem Text- und seinem HTML-Teil.

## 3. Eine eigene Kategorie

Deklarieren Sie sie in `settings/mailpreferences.ini.append.php` Ihrer Extension:

```ini
<?php /* #?ini charset="utf-8"?

[CategorySettings]
Categories[]=events

[Category_events]
Name=Events
Description=Invitations to our events and webinars.
Essential=false
DefaultOn=false
Frequencies[]
DoubleOptIn=false
HandlerClass=myEventsMailCategoryHandler

*/ ?>
```

| Schlüssel | Bedeutung |
|---|---|
| `Name`, `Description` | Werden auf der Einstellungsseite und im Registrierungsformular angezeigt und im Einwilligungsprotokoll als angezeigter Text festgehalten. Übersetzt über den Kontext `kernel/mailpreferences/categories` |
| `Essential` | `true`: nie umschaltbar, wird immer gesendet. Nur für Mails, auf die eine Person nicht verzichten kann; nie für etwas mit Werbung |
| `DefaultOn` | Lassen Sie `false`. `true` macht die Kategorie zu einem Opt-out, was die EU-Vorschriften und CASL für Marketing nicht erlauben |
| `Frequencies[]` | `immediate`, `daily`, `weekly`; leer: Die Kategorie behält ihren eigenen Zeitplan. Ihr Code liest die Wahl der Person mit `expMailPreferences::frequency()` |
| `DoubleOptIn` | `true` für Newsletter und Marketing: Das Einschalten sendet zuerst einen Bestätigungslink, und nichts wird gesendet, bis er bestätigt ist |
| `HandlerClass` | Optional; Abschnitt 4 |

Kategorien von Extensions zeigen auf der Kategorienseite für Administratoren die Quelle „Extension“. Ein Administrator kann sie dort umbenennen
und beschreiben; die Kennung, `Essential` und `HandlerClass` bleiben so, wie Ihre Einstellungen sagen. Code kann außerdem eine
Kategorie für einen einzelnen Prozess mit `expMailCategoryRegistry::instance()->register( new expMailCategory( 'events', array( ... ) ) )` hinzufügen.

## 4. Ältere Daten mit einem Handler abbilden

Wenn Ihre Extension bereits eigene Abonnements führt, liest ein Handler sie als Zustand der Kategorie, sodass nichts
migriert werden muss und Ihre alten Seiten weiter funktionieren. Eine Wahl, die die Person auf der Einstellungsseite trifft, wird vom
Einstellungssystem gespeichert und hat Vorrang.

```php
class myEventsMailCategoryHandler implements expMailCategoryHandler
{
    /** the old list of the extension: addresses that asked for event mail before the preference system */
    public static $legacyList = array( 'early@docs.invalid' );

    public function stateFor( expMailRecipient $recipient, expMailCategory $category )
    {
        // an address on the old list counts as "on" until the person chooses on the preference page
        return in_array( $recipient->email(), self::$legacyList, true ) ? true : null;
    }

    public function frequencyFor( expMailRecipient $recipient, expMailCategory $category )
    {
        return null;   // the category's first frequency
    }

    public function changed( expMailRecipient $recipient, expMailCategory $category, $state, expConsentContext $context )
    {
        // keep your own data in step: $state is 'on', 'off' or 'pending'
    }
}
```

- `stateFor()` gibt `true`, `false` oder `null` zurück (keine Meinung: der Standard der Kategorie, aus). Es wird nur für Personen
  ohne gespeicherte Wahl gefragt.
- `frequencyFor()` gibt `immediate`, `daily`, `weekly` oder `null` zurück.
- `changed()` wird nach jeder Änderung des Zustands der Person aufgerufen; eine Ausnahme darin wird protokolliert und hält die Änderung nicht auf.
- Optional gibt `subscriptions( expMailRecipient, expMailCategory )` eine Liste von `hash( name, status, active, url )` zurück; die
  Einstellungsseite listet sie unter der Kategorie auf („Ihre Abonnements:“), zum Beispiel die Listen eines Newsletters.
- Optional fügt ein Handler der Zeile der Kategorie eigene Auswahlmöglichkeiten hinzu (die Interessen und die Sprache eines Newsletters, eine
  Telefonnummer), den **Kategorieteil**:
  - `partTemplate( expMailRecipient, expMailCategory, $mode )` gibt einen `design:`-Templatenamen zurück oder `null` für keinen Teil.
    `$mode` ist `account`, `token` (der persönliche Link) oder `admin`.
  - `partVariables( expMailRecipient, expMailCategory, $mode )` gibt einen Hash zurück; das Template erhält ihn als `$part`, zusammen mit
    `$category`, `$email` und `$mode`.
  - Der Teil wird innerhalb des Kategorieformulars eingebunden, sodass seine Felder mit „Meine Auswahl speichern“ abgeschickt werden. Dann
    speichert `storePart( expMailRecipient, expMailCategory, eZHTTPTool, expConsentContext )` sie. Es gibt eine Liste von
    Fehlermeldungen zurück (auf der Seite angezeigt; speichern Sie nichts, was falsch ist) und kann `'changed' => <int>` hinzufügen, die Anzahl der
    Auswahlmöglichkeiten, die es geändert hat, damit die Seite nicht mit „Es wurde nichts geändert.“ antwortet. Eine Ausnahme rollt das ganze Formular zurück.
  - Verwenden Sie im Teil die eigenen Klassen der Seite (`mp-field`, `mp-hint`, `mp-freq`, `mp-badge`), damit jedes Design ihn
    gleich gestaltet. Stellen Sie den POST-Namen Ihre Kategorie voran, zum Beispiel `MailPreferencePart[newsletter][Language]`.

```php
public function partTemplate( expMailRecipient $recipient, expMailCategory $category, $mode )
{
    return 'design:mailpreferences/category/events.tpl';
}

public function partVariables( expMailRecipient $recipient, expMailCategory $category, $mode )
{
    return array( 'city' => myEventsCity::forRecipient( $recipient ) );
}

public function storePart( expMailRecipient $recipient, expMailCategory $category, eZHTTPTool $http, expConsentContext $context )
{
    $posted = $http->postVariable( 'MailPreferencePart', array() );
    $city = isset( $posted['events']['City'] ) ? trim( (string)$posted['events']['City'] ) : '';
    if ( strlen( $city ) > 100 )
        return array( 'The city name is too long.' );
    return array( 'changed' => myEventsCity::store( $recipient, $city ) ? 1 : 0 );
}
```

- Optional wird `erased( expMailRecipient, expConsentContext )` aufgerufen, wenn die Person gelöscht wird (auf Anfrage, durch
  `exp:mail:preferences erase` oder wenn das Konto entfernt wird), nachdem das Einstellungssystem die Einstellungen entfernt
  und das Einwilligungsprotokoll anonymisiert hat. Entfernen Sie dort, was Ihre Extension über die Person speichert (Zählwerte pro Person, eine
  Telefonnummer). Es wird einmal pro Handler-Klasse aufgerufen, auch für Kategorien, die die Person nie angefasst hat; eine Ausnahme wird protokolliert
  und hält die Löschung nie auf.

Der eigene Handler des Kernels, `expNotificationMailCategoryHandler`, liest Teilbaum-Benachrichtigungsregeln und Einstellungen für Zusammenfassungen als
die Kategorien `content` und `collaboration`.

### Was das Beispiel tat

Mit der oben gezeigten Kategorie im Prozess gesetzt und dem geladenen Handler erhielten drei Adressen eine Ankündigung. `early@` steht auf
der alten Liste, `new@` hat die Kategorie auf der Seite eingeschaltet, `nobody@` hat nichts getan:

```
PASS the category is registered, source extension
PASS the old list reads as on
PASS a new address is off (opt-in)
  handler: events is now on for n***@docs.invalid (source page)
  lastResult: {"decision":"partly_blocked","sent":2,"blocked":["off"]}
PASS two mails, one each, the third address blocked
```

Eine der beiden Mails, so wie der Datei-Transport sie geschrieben hat (Links gekürzt, einige Header weggelassen):

```
Subject: Our autumn webinar
From: info@example.com
Bcc: new@docs.invalid
X-Exp-Mail-Category: events
List-Unsubscribe: <https://www.example.com/mailpreferences/unsubscribe/m1...>
List-Unsubscribe-Post: List-Unsubscribe=One-Click
Join us on 12 November.

-- 
You get this e-mail because you turned on "Events" on Example Site.
Unsubscribe with one click: https://www.example.com/mailpreferences/unsubscribe/m1...
Choose which e-mail you get: https://www.example.com/mailpreferences/manage/m1...

Example Events Ltd
Example Street 1, 12345 Town
```

## 5. Einstellungen aus Code ändern

```php
$recipient = expMailRecipient::fromUser( $user );            // or fromAddress( 'a@example.com' ), fromUserId( 14 )
$prefs = expMailPreferences::forRecipient( $recipient );
$context = expConsentContext::fromRequest( 'page', $textThePersonSaw );
$prefs->set( 'events', true, $context );                     // 'on', 'pending_confirmation' or 'off'
$prefs->setMaster( false, $context );                        // all optional mail off, categories kept
$prefs->decision( 'events' );                                // allow, off, pending, master_off, suppressed, unknown_category
```

- **Halten Sie fest, was die Person gesehen hat.** `$wording` wird im Einwilligungsprotokoll als Nachweis der Einwilligung aufbewahrt. Bauen Sie es aus denselben
  übersetzten Zeichenketten auf, die Ihre Seite anzeigt.
- **Quellen**: `page`, `link`, `admin`, `signup`, `confirm`, `system`; `import` und `bridge` schalten eine Kategorie mit Double-Opt-in
  sofort ein und dürfen nur für anderswo bestätigte Einwilligungen verwendet werden. `expConsentContext::system( $wording )` ist für
  Cronjobs und Befehle gedacht; setzen Sie `$context->sendConfirmation = false`, um ein ausstehendes Double-Opt-in zu speichern, ohne es per Mail zu versenden.
- Eine Adresse, die zu einem Konto gehört, ist dieses Konto: `fromAddress()` findet es (übergeben Sie `false` als zweites Argument, um
  die bloße Adresse zu erhalten).
- Unverzichtbare Kategorien werfen bei `set()` eine `InvalidArgumentException`.

## 6. Links und Tokens

`expMailPreferencesService::manageURL( $recipient )` und `unsubscribeURL( $recipient, $category )` liefern absolute Links, die
ohne Anmeldung funktionieren. Es sind Tokens `m1<base64url>`: der Zweck, der Empfänger (Benutzer-ID oder die Adresse einer Person
ohne Konto), die Kategorie und das Ablaufdatum, verschlüsselt und authentifiziert mit AES-256-GCM unter einem Schlüssel, der aus dem
Site-Geheimnis abgeleitet ist. Pro Link wird nichts gespeichert, ein Link kann weder gelesen noch geändert werden, und eine Änderung des Geheimnisses macht alle ungültig.
`expMailToken::verify( $token, $purpose )` gibt die Nutzdaten oder `null` zurück, und `expMailToken::lastError()` sagt, warum
(`malformed`, `tampered`, `purpose`, `expired`).

## 7. Erweiterungspunkte

| Punkt | Wofür er da ist |
|---|---|
| `mailpreferences.ini [CategorySettings] Categories[]` + `[Category_<id>]` | Kategorien einer Extension |
| `expMailCategoryHandler` (`HandlerClass`) | Ältere Daten als Zustand lesen; von Änderungen erfahren; optional `subscriptions()` |
| `partTemplate()`, `partVariables()`, `storePart()` des Handlers | Ein Teil der Zeile der Kategorie mit eigenen Auswahlmöglichkeiten, mit dem Formular gespeichert (Abschnitt 4) |
| `erased()` des Handlers | Die eigenen Daten der Extension über eine gelöschte Person entfernen (Abschnitt 4) |
| `[GateSettings] EssentialSenders[]` | Mails ohne Kategorie einer Datei, einer Klasse oder einer From-Adresse als unverzichtbar kennzeichnen |
| `[SuppressionSettings] Listeners[]` | Klassen, die benachrichtigt werden, wenn eine Adresse gesperrt oder entsperrt wird: statisch `suppressionAdded( $email, $hash, $reason )`, `suppressionLifted( $hash, $email, $reason )` (hält eine andere Sperrliste auf Stand) |
| `[BounceSettings] MessageListeners[]` | Klassen, die jede Nachricht erhalten, die der Bounce-Leser liest, nach seiner eigenen Klassifizierung und Sperrung: statisch `mailMessage( $raw, $classification, $dryRun )` gibt `true` zurück, wenn die Nachricht für die Klasse bestimmt war (`AfterRead=delete` entfernt sie dann). Für die eigenen Bounces eines Systems (ein erneuter Versuch nach einem Soft Bounce) oder Mails an seine eigenen Adressen im selben Postfach (Abonnieren per E-Mail). Ändern Sie nichts, wenn `$dryRun` true ist; eine Ausnahme wird protokolliert und das Lesen geht weiter (Test: `MailPreferencesBounceListenersTest`) |
| `[FooterSettings] Template` und `design:mailpreferences/mail/*.tpl` | Die Fußzeile und die Mails für Bestätigung, Link und Adressänderung, pro Design |
| `design:mailpreferences/parts/page_start.tpl`, `page_end.tpl` | Die Einstellungsseiten in Ihr Design einrahmen |
| `design:mailpreferences/parts/account_link.tpl` | Der Kasten „E-Mail-Einstellungen“ für Ihre eigenen Seiten (`context` = `profile`, `notification`, `newsletter`) |
| `MailPreferencesPage::signupCategories()`, `storeSignup()` | Die nicht angekreuzten Kästchen eines eigenen Registrierungsformulars |

## 8. Testen, ohne Mails zu senden

Testen Sie nie mit dem Transport der Site: Auf einem Produktivserver sendet er. Erzwingen Sie den Datei-Transport im eigenen Prozess des Tests,
verweigern Sie andernfalls die Ausführung und verwenden Sie nur Adressen unter `.invalid`:

```php
$ini = eZINI::instance();
$ini->setVariable( 'MailSettings', 'Transport', 'file' );
$ini->setVariable( 'MailSettings', 'FileTransportDirectory', 'var/tmp/my-test-mail' );
if ( trim( $ini->variable( 'MailSettings', 'Transport' ) ) !== 'file' )
    throw new RuntimeException( 'The mail transport is not the file transport: the test refuses to run.' );
```

Eine einzelne Nachricht kann auch mit einem anderen als dem konfigurierten Transport gesendet werden, ohne die Einstellung zu ändern:
`eZMailTransport::send( $mail, 'file' )` (jeder Name aus `site.ini [MailSettings] TransportAlias[]`: `file`,
`sendmail`, `smtp` oder einer aus einer Extension). Die nächste Nachricht wird wieder wie konfiguriert gesendet, und das Mail-Gate greift
in beiden Fällen. `tests/tests/lib/ezutils/eZMailTransportSendTest.php` zeigt beides. Mit Dank an Mugo Web (LoveStack,
Commit `df07cb1ef5`).

`expMailGate::setLogFileForTest( $file )` leitet das Protokoll des Gates in eine Datei des Tests. Entfernen Sie die Zeilen, die der Test angelegt hat
(`expmail_preference`, `expmail_pending`, `expmail_consent_log` nach Empfängerschlüssel, `expmail_suppression` nach Hash), und
überspringen Sie den Test mit `ezpLiveInstallation::requireOrSkip()`, wo es keine Installation gibt, damit der GitHub-Workflow grün bleibt. Die
beiden Testklassen des Kernels sind das Vorbild:

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/mailpreferences/
```

```
OK (45 tests, 660 assertions)
```

`MailPreferencesCoreTest` (MP-01 bis MP-20) deckt den Kern und das Gate ab, `MailPreferencesComplianceTest` (LAW-01 bis
LAW-24) die [rechtliche Checkliste](../spezifikationen/6.0/mail-einstellungen-compliance.md). Auf der Konsole
schickt `./console exp:mail:gate --category=events --to=a@docs.invalid --write` eine Testmail durch das Gate in Dateien.

## Verwandte Seiten

- [Die Spezifikation](../spezifikationen/6.0/mail-einstellungen.md), [die Befehle](../spezifikationen/6.0/mail-einstellungen-cli.md)
- [Die rechtliche Checkliste](../spezifikationen/6.0/mail-einstellungen-compliance.md)
- [Die Anleitung für Administratoren](mail-einstellungen-administrator.md)
- [Benachrichtigungen: Architektur, Erweiterung und Tests](benachrichtigungen-entwickler.md)
- [Upgrade-Hinweise](../bc/6.0/e-mail-einstellungen.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
