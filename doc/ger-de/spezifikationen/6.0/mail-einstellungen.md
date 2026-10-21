# E-Mail-Einstellungen und Einwilligung: die Spezifikation

Die Referenz des Systems für E-Mail-Einstellungen von Exponential 6.0.15: die Klassen, die Tabellen, die Einstellungen, die Abläufe,
die Entscheidungen des Mail-Gates und das Format der Links. Lesen Sie sie, wenn Sie eine genaue Antwort brauchen; die Schritt-für-Schritt-Darstellungen sind
[die Anleitung für Administratoren](../../anleitungen/mail-einstellungen-administrator.md) und
[die Anleitung für Entwickler](../../anleitungen/mail-einstellungen-entwickler.md). Die rechtlichen Anforderungen und die Tests, die sie belegen,
stehen auf [der Rechts-Checkliste](mail-einstellungen-compliance.md).

## Bestandteile

| Bestandteil | Wo |
|---|---|
| Klassen (per Autoload geladen, Kernel) | `kernel/classes/mailpreferences/`: `expMailCategory`, `expMailCategoryRegistry`, `expMailCategoryHandler`, `expMailRecipient`, `expMailPreferences`, `expConsentContext`, `expConsentLog`, `expMailSuppression`, `expMailToken`, `expMailSecret`, `expMailGate`, `expMailPreferencesService`, die Zeilenklassen `expMailCategoryRow`, `expMailPreferenceRow`, `expMailPendingRow` |
| Der Einhängepunkt des Gates | `lib/ezutils/classes/ezmailtransport.php` (`eZMailTransport::send()` ruft `expMailGate::dispatch()` auf), `lib/ezutils/classes/ezmail.php` (`setCategory()`, `category()`) |
| Modul | `kernel/mailpreferences/module.php`; View-Klassen `kernel/private/classes/views/mailpreferences/`; gemeinsamer Seitencode `Exponential\Service\MailPreferencesPage` (`kernel/private/classes/services/mailpreferencespage.php`) |
| Templates | `design/standard/templates/mailpreferences/` (Seiten, `parts/`, `admin/`, `mail/`); Rahmen in `design/admin` und `design/admin4` (`parts/page_start.tpl`, `page_end.tpl`) |
| Befehle | `exp:mail:status`, `exp:mail:preferences`, `exp:mail:suppression`, `exp:mail:consent`, `exp:mail:gate` ([Referenz](mail-einstellungen-cli.md)) |
| Cronjob-Teil | `mailpreferences` (`cronjobs/mailpreferences.php`), auch in der Gruppe `infrequent` |
| Einstellungen | `settings/mailpreferences.ini`; `site.ini [RoleSettings] PolicyOmitList[]`; `menu.ini`-Einträge `my_mail` und `mailpreferences`; `cronjob.ini` |
| Texte | `share/translations/ger-DE/translation.ts`, Kontexte `design/standard/mailpreferences`, `design/admin/mailpreferences`, `kernel/mailpreferences`, `kernel/mailpreferences/categories`, `kernel/mailpreferences/mail`, `kernel/mailpreferences/status` |

## Kategorien

`expMailCategory` ist ein Wertobjekt: `identifier`, `name`, `description`, `essential`, `defaultOn` (bei einer
essenziellen Kategorie immer false), `frequencies` (Teilmenge von `immediate`, `daily`, `weekly`), `doubleOptIn`, `source` (`ini`, `extension`,
`admin`), `handlerClass`. Ein Bezeichner besteht aus Kleinbuchstaben, Ziffern und Unterstrichen und beginnt mit einem Buchstaben.

`expMailCategoryRegistry::instance()` führt in dieser Reihenfolge zusammen:

1. `mailpreferences.ini [CategorySettings] Categories[]` mit je einem Block `[Category_<identifier>]`; Quelle `ini` für die
   Bezeichner, die in der mitgelieferten `settings/mailpreferences.ini` aufgeführt sind, `extension` für die übrigen.
2. Zeilen von `expmail_category`. Eine Zeile mit dem Bezeichner einer Kategorie aus Schritt 1 überschreibt deren Name, Beschreibung,
   Standardwert, Häufigkeiten und Double-Opt-in; `essential` und eine leere `handler_class` bleiben, wie die Einstellungen sie vorgeben. Andere Zeilen
   sind Kategorien der Quelle `admin`.
3. `register()`: Kategorien, die im Code für diesen Prozess hinzugefügt wurden.

Die Liste wird für jede Anfrage neu gelesen (`$_SERVER['REQUEST_TIME_FLOAT']`), sodass persistente Worker Änderungen in der Administration sehen.
`all()`, `optional()`, `essential()`, `get()`, `iniCategories()`, `saveAdmin()`, `removeAdmin()`, `reset()`.

Die mitgelieferten Kategorien:

| Bezeichner | Art | Häufigkeiten | Double-Opt-in | Handler |
|---|---|---|---|---|
| `content` | optional | sofort, täglich, wöchentlich | nein | `expNotificationMailCategoryHandler` |
| `collaboration` | optional | sofort, täglich, wöchentlich | nein | `expNotificationMailCategoryHandler` |
| `newsletter` | optional | eigener Zeitplan | ja | (die Newsletter-Brücke) |
| `marketing` | optional | eigener Zeitplan | ja | |
| `system` | optional | eigener Zeitplan | nein | |
| `security` | essenziell | | | |
| `orders` | essenziell | | | |
| `legal` | essenziell | | | |
| `admin` | essenziell | | | |

## Empfänger

`expMailRecipient` bezeichnet eine Person. Sein Schlüssel ist `u:<user id>` für ein Konto und `a:<hash>` für eine Adresse ohne
Konto, wobei `<hash>` gleich `expMailSuppression::hash( $email )` ist. `fromAddress()` liefert das Konto, wenn die Adresse
zu einem gehört (zweites Argument `false`: die bloße Adresse). Außerdem `fromUser()`, `fromUserId()`, `fromToken()`,
`fromPayload()`, `fromKey()`. `logKey()` ist das, was das Gate-Protokoll speichert: die Benutzer-ID oder die ersten Zeichen des Hashs.

## Tabellen

Alle fünf stehen im Kernel-Schema von MySQL, PostgreSQL und SQLite, in `share/db_schema.dba` und in
`update/database/<engine>/6.0/dbupdate-6.0.0-6.0.15.sql`. Zeiten sind Unix-Zeitstempel.

### expmail_preference

Eine Zeile je Person und Kategorie, dazu der Hauptschalter (Kategorie `_master`).

| Spalte | Bedeutung |
|---|---|
| `id` | Schlüssel |
| `recipient_key` | `u:<id>` oder `a:<hash>`; eindeutig zusammen mit `category` |
| `user_id` | das Konto, 0 für eine Adresse |
| `category` | Bezeichner oder `_master` |
| `state` | `on`, `off`, `pending` |
| `frequency` | `immediate`, `daily`, `weekly` oder leer |
| `created`, `modified` | Zeiten |

Keine Zeile bedeutet „keine Wahl getroffen“: Es gilt, was der Handler ermittelt, sonst der Standardwert der Kategorie (aus).

### expmail_consent_log

Eine Zeile je Änderung; wird nie aktualisiert, außer durch die Anonymisierung.

| Spalte | Bedeutung |
|---|---|
| `id`, `created` | Schlüssel, Zeit |
| `recipient_key`, `user_id`, `email` | die Person; `email` ist bei einem Konto leer |
| `category` | Bezeichner, leer beim Hauptschalter und bei Aktionen, die die ganze Person betreffen |
| `action` | `on`, `off`, `pending`, `confirm`, `master_on`, `master_off`, `frequency`, `erase`, `export`, `suppress`, `unsuppress`, `email_change` |
| `old_value`, `new_value` | der Zustand oder die Häufigkeit davor und danach |
| `source` | `page`, `link`, `admin`, `import`, `signup`, `bridge`, `confirm`, `system` |
| `wording` | der genaue Text, den die Person gesehen hat (oder das `--wording` eines Befehls) |
| `ip`, `actor_user_id`, `siteaccess` | woher die Änderung kam und wer sie vorgenommen hat |
| `anonymised` | 1 nach der Löschung: `recipient_key` ist `x:<irreversible hash>`, `email`, `ip`, `user_id` werden geleert |

Jede Zeile schreibt außerdem ein Audit-Ereignis: `access.user.consent.erase`, `access.user.consent.export` oder
`access.user.consent.change` für jede andere Aktion.

### expmail_suppression

| Spalte | Bedeutung |
|---|---|
| `email_hash` | `sha256( lowercase( trim( email ) ) . derive( 'hash' ) )`, eindeutig; die Adresse wird nie gespeichert |
| `reason` | `bounce`, `complaint`, `unsubscribe_all`, `legal`, `admin`, `bridge` (`[SuppressionSettings] Reasons[]`) |
| `note` | Freitext für Administratoren; eine darin enthaltene Adresse wird entfernt |
| `created`, `created_by` | Zeit und Benutzer-ID |

### expmail_pending

Ein Double-Opt-in, das auf seine Bestätigung wartet.

| Spalte | Bedeutung |
|---|---|
| `recipient_key`, `user_id` | die Person |
| `kind` | `category` oder `email_change` (und `link` für die Ratenbegrenzung von „Link zusenden“) |
| `category` | bei `category` |
| `data` | JSON; bei `email_change` die neue Adresse |
| `created`, `expires` | Zeiten; `expires` = created + `[ConsentSettings] PendingDays` |

### expmail_category

Die Kategorien und Überschreibungen des Administrators: `identifier` (eindeutig), `name`, `description`, `essential`, `default_on`,
`frequencies` (durch Kommas getrennt), `double_opt_in`, `handler_class`, `priority`, `created`, `modified`.

## Die Entscheidung für einen Empfänger

`expMailPreferences::decision( $category )`, in dieser Reihenfolge:

| Ergebnis | Wann |
|---|---|
| `unknown_category` | keine solche Kategorie |
| `allow` | eine essenzielle Kategorie |
| `suppressed` | die Adresse steht auf der Sperrliste |
| `master_off` | die Person hat alle optionalen E-Mails abgeschaltet |
| `pending` | die Kategorie wartet auf ihre Bestätigung |
| `off` | die Kategorie ist aus (gespeichert, vom Handler ermittelt oder der Standardwert) |
| `allow` | sonst |

`allows()` ist `decision() === 'allow'`.

## Das Gate

`expMailGate::dispatch( eZMail, eZMailTransport )`:

| E-Mail | Was geschieht | Protokolliert als |
|---|---|---|
| Keine Kategorie | unverändert gesendet | `uncategorised` mit der sendenden Datei (`why: no_category`) oder `essential` (`why: essential_sender`), wenn die From-Adresse, die Datei oder eine Klasse des Aufrufstapels in `[GateSettings] EssentialSenders[]` steht |
| Unbekannte Kategorie | unverändert gesendet | `uncategorised`, `why: unknown_category` |
| Essenzielle Kategorie | mit `X-Exp-Mail-Category` gesendet, ohne Fußzeile | `sent`, `why: essential` |
| Optional, niemand zugelassen | nichts gesendet, `true` zurückgegeben | `blocked` je Empfänger mit der Entscheidung |
| Optional, einige zugelassen | eine E-Mail je zugelassenem Empfänger (To, Cc und Bcc bleiben in ihrem Feld), jede ergänzt; das Mail-Objekt erhält danach seine Empfänger und seinen Text zurück | `sent` je Empfänger, `blocked` je blockiertem Empfänger |
| Optional, `SplitRecipients=disabled` und mehrere zugelassen | eine E-Mail an alle Zugelassenen; Fußzeile mit der Seite „Link zusenden“, kein `List-Unsubscribe` | `sent` je Empfänger |
| `Gate=disabled` | optionale E-Mails gehen an alle außer gesperrten Adressen, weiterhin ergänzt | wie oben |
| Eine Exception im Gate | optionale E-Mails werden nicht gesendet (`false`); andere E-Mails gehen hinaus | `error` |

`decorate( eZMail, recipient, category )` fügt `X-Exp-Mail-Category` hinzu und bei optionalen E-Mails mit bekanntem Empfänger die
Header

```
List-Unsubscribe: <https://www.example.com/mailpreferences/unsubscribe/m1...>
List-Unsubscribe-Post: List-Unsubscribe=One-Click
```

sowie die Fußzeile aus `[FooterSettings] Template` (ein eingebauter Text, wenn das Template fehlt): bei Text-E-Mails am Ende nach
`-- `, bei HTML vor `</body>`, in jedem Textteil eines `ezcMailPart`-Inhalts. Es ändert nie From, Reply-To, Subject oder
andere Header.

Das Protokoll (`[GateSettings] LogFile`, Standard `log/mailgate.jsonl` im var-Verzeichnis, bei `MaxLogSize` nach `.1` rotiert) enthält
ein JSON-Objekt pro Zeile: `t` Zeit, `d` Entscheidung, `c` Kategorie, `why`, `r` der Protokollschlüssel des Empfängers, `s` die sendende Datei.
Es enthält keine Adresse. `expMailGate::stats( $since )` zählt es für die Statusseite aus; `lastResult()` beschreibt den letzten
Versand (`category`, `decision`: `sent`, `partly_blocked`, `blocked`, `essential`, `uncategorised`, `unknown_category`;
`sent`, `blocked`, `result`).

## Links (Tokens)

`expMailToken::create( recipient, purpose, category = null, ttl = null, extra = array() )`:

- Zwecke `unsubscribe`, `manage`, `confirm`;
- Nutzdaten als JSON `{ p: purpose, t: issued, x: expires (0 never), u: user id, e: address (only without account),
  c: category, i: pending id }`;
- verschlüsselt mit AES-256-GCM, 12-Byte-IV, 16-Byte-Tag, zusätzliche Daten `exp-mail-token`, Schlüssel
  `HMAC-SHA-256( 'exp-mailpreferences:token', site secret )`;
- geschrieben als `m1` + base64url( IV . ciphertext . tag ).

`verify( $token, $purpose )` liefert `purpose, user, email, category, pending, issued, expires` oder `null`;
`lastError()`: `malformed`, `tampered`, `purpose`, `expired`. Standard-Gültigkeitsdauern, `[TokenSettings] TTL[]`: unsubscribe 0
(nie ablaufend), manage 0, confirm 604800 (7 Tage); ein „Link zusenden“-Link `RequestLinkTTL` (86400). Die Adresse lautet
`<BaseURL>/mailpreferences/<view>/<token>`.

Das Site-Geheimnis (`expMailSecret`): 32 Zufallsbytes, base64-kodiert in `settings/override/mailpreferences.ini.append.php`
`[SecretSettings] TokenSecret`, beim ersten Gebrauch unter einer Sperre erzeugt. `derive( $purpose )` liefert den Schlüssel für `token`,
`hash` (Sperrlisten- und Adressschlüssel) und `anonymise`.

## Abläufe

**Eine Kategorie einschalten** (`expMailPreferences::set( $category, true, $context )`): Eine Kategorie ohne Double-Opt-in oder
ein Kontext der Quelle `confirm`, `bridge` oder `import` ist sofort `on` (Protokoll `on` bzw. `confirm`). Andernfalls wird der Zustand
`pending`, eine Zeile in `expmail_pending` wird geschrieben, das Protokoll vermerkt `pending`, und die Bestätigungs-E-Mail (Kategorie `security`,
Template `mail/confirm.tpl`) geht mit einem `confirm`-Link an die Adresse, es sei denn, `$context->sendConfirmation` ist false oder
die Adresse ist wegen eines Bounce, einer Beschwerde oder aus rechtlichem Grund gesperrt. Das Einschalten über `page`, `link`, `confirm` oder `signup` hebt
die eigene `unsubscribe_all`-Sperre der Person auf.

**Bestätigen** (`mailpreferences/confirm/<token>`): GET zeigt eine Schaltfläche; das POST von **Ja, bestätigen** ruft
`expMailPreferencesService::confirm()` auf: `confirmed`, `already`, `expired` (die ausstehende Zeile wird entfernt) oder `invalid`.

**Abmelden** (`mailpreferences/unsubscribe/<token>`): GET zeigt eine Schaltfläche; deren POST oder ein POST mit dem Inhalt
`List-Unsubscribe=One-Click` (RFC 8058; kein Cookie, kein Formular-Token) ruft `expMailPreferencesService::unsubscribe()` auf: Ein Link
mit Kategorie schaltet die Kategorie ab, ein Link ohne Kategorie schaltet den Hauptschalter ab. Das One-Click-POST antwortet mit 200
`text/plain` („Sie sind abgemeldet.“) oder mit 400 bei einem Link, der nicht funktioniert. `unsubscribeAll()` schaltet den Hauptschalter ab
und setzt die Adresse auf die Sperrliste (`unsubscribe_all`).

**Link zusenden** (`mailpreferences/request`): `requestLink( $email )` liefert `sent`, `invalid`, `rate_limited`
(`[TokenSettings] RequestLinkLimit` pro Stunde) oder `blocked`; die Seite zeigt für jede formal gültige Adresse dieselbe Antwort.

**Adressänderung** (`requestEmailChange( $user, $email, $context )`): `invalid`, `unchanged`, `in_use` oder
`pending_confirmation`; das Konto behält seine Adresse, bis der an die neue Adresse gesendete Link bestätigt ist.

**Registrierung**: `MailPreferencesPage::signupCategories()` liefert die Kästchen des Registrierungsformulars, keines angekreuzt;
`storeSignup()` speichert nur, was angekreuzt wurde, mit der Quelle `signup` und dem Text des Formulars.

**Export und Löschung**: `export()` liefert den Empfänger, den Hauptschalter, jede Kategorie mit Zustand, Häufigkeit und Zeit,
ausstehende Bestätigungen und das gesamte Einwilligungsprotokoll; `exportToCsv()` schreibt das als Zeilen `section,key,value,detail`.
`erase( $context )` schreibt eine `erase`-Zeile, löscht die Einstellungen und die ausstehenden Zeilen und anonymisiert jede Protokollzeile der
Person.

**Aufbewahrung** (`expConsentLog::cleanup( $dryRun )`, Cronjob-Teil `mailpreferences`): entfernt Protokollzeilen, die älter als
`[ConsentSettings] RetentionDays` sind, von Personen, die nicht mehr da sind (anonymisiert, ein entferntes Konto, eine Adresse ohne Einstellung),
entfernt abgelaufene ausstehende Zeilen und schaltet deren Kategorien ab, und entfernt Sperrlisteneinträge, die älter als
`[SuppressionSettings] RetentionDays` sind, sofern dieser Wert über 0 liegt.

## Modul-Views

| View | Anmeldung | Zweck |
|---|---|---|
| `settings` | angemeldet | die Seite der Person; sendet `MailPreferencesForm` = `master` (`MasterOffButton`, `MasterOnButton`) oder `categories` (`CategoryShown[]`, `Category[<id>]`, `Frequency[<id>]`, `StoreButton`) |
| `manage/<token>` | Link | dieselbe Seite über einen `manage`-Link |
| `unsubscribe/<token>` | Link | Abschnitt „Abläufe“ |
| `confirm/<token>` | Link | Abschnitt „Abläufe“ |
| `request` | keine | „Link zusenden“ |
| `export/<json|csv>[/(token)/<token>]` | angemeldet oder Link | der Datendownload |
| `admin/<status|categories|suppression|consent|user>[/<id>]` | `mailpreferences/administrate` | die Seiten des Administrators; Einwilligungsfilter `/(person)/…/(category)/…/(source)/…/(from)/…/(to)/…` (das Filterformular wird gesendet und mit 303 an diese Adresse beantwortet), `/(export)/csv` (Richtlinie `mailpreferences/export`; die Seite eines Benutzers auch `/(export)/json`), die Benutzersuche `/(q)/…`; `/(offset)/<n>`; nie Parameter im Query-String |

Seiten, die über einen persönlichen Link erreicht werden, werden mit `Cache-Control: private, no-store, max-age=0`, `Referrer-Policy: no-referrer`
und `X-Robots-Tag: noindex, nofollow` ausgeliefert. Jede Seite funktioniert ohne JavaScript.

## Einstellungen: mailpreferences.ini

| Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|
| `GateSettings` | `Gate` | `enabled` | `disabled`: optionale E-Mails gehen an alle außer gesperrten Adressen (weiterhin ergänzt) |
| | `EssentialSenders[]` | die Absender des Kernels für Konten, Bestellungen, Audit und Setup, `expMailPreferencesService` | E-Mails ohne Kategorie von diesen Absendern werden als essenziell protokolliert |
| | `LogFile` | `log/mailgate.jsonl` | relativ zum var-Verzeichnis |
| | `MaxLogSize` | `5242880` | Bytes vor der Rotation |
| | `SplitRecipients` | `enabled` | eine optionale E-Mail je Empfänger |
| `CategorySettings` | `Categories[]` | neun Bezeichner | die Reihenfolge auf der Seite |
| `Category_<id>` | `Name`, `Description`, `Essential`, `DefaultOn`, `Frequencies[]`, `DoubleOptIn`, `HandlerClass` | | siehe die Anleitung für Entwickler |
| `FooterSettings` | `OrganisationName`, `OrganisationAddress` | leer | Absender und Postanschrift in der Fußzeile; `\n` für einen Zeilenumbruch |
| | `Template` | `design:mailpreferences/mail/footer.tpl` | das Template der Fußzeile |
| `LinkSettings` | `BaseURL` | leer (https:// + SiteURL des Standard-Siteaccess) | wohin Links zeigen |
| `TokenSettings` | `TTL[unsubscribe]`, `TTL[manage]`, `TTL[confirm]` | `0`, `0`, `604800` | Gültigkeitsdauer der Links in Sekunden |
| | `RequestLinkTTL`, `RequestLinkLimit` | `86400`, `3` | „Link zusenden“ |
| `ConsentSettings` | `RetentionDays`, `PendingDays` | `1095`, `7` | Aufbewahrung |
| `SuppressionSettings` | `RetentionDays` | `0` (unbegrenzt) | |
| | `Reasons[]` | sechs Gründe | |
| | `Listeners[]` | leer | Klassen, die über hinzugefügte und aufgehobene Einträge informiert werden |
| `SecretSettings` | `TokenSecret` | wird in den Override erzeugt | niemals committen |

## Verwandte Seiten

- [Die Rechts-Checkliste](mail-einstellungen-compliance.md)
- [Die Befehle](mail-einstellungen-cli.md)
- [Die Anleitung für Administratoren](../../anleitungen/mail-einstellungen-administrator.md), [die Anleitung für Entwickler](../../anleitungen/mail-einstellungen-entwickler.md)
- [Die Anleitung für Benutzer](../../funktionen/6.0/e-mail-einstellungen.md)
- [Hinweise zum Upgrade](../../bc/6.0/e-mail-einstellungen.md)
- [Benachrichtigungen](benachrichtigungen.md)
