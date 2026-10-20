# E-Mail-Einstellungen: die Checkliste zu den Gesetzen

Diese Seite ordnet jede Anforderung der E-Mail-Gesetze dem Code zu, der sie erfüllt, und dem Test, der das belegt. Sie richtet sich an
die Personen, die für die E-Mails einer Site verantwortlich sind: den Betreiber der Site, die oder den Datenschutzbeauftragten und die Entwickler, die eine
Änderung am E-Mail-System prüfen. Das System selbst ist in [der Spezifikation](mail-einstellungen.md) beschrieben.

**Diese Seite ist keine Rechtsberatung.** Sie hält fest, was die Software tut, geprüft an der gängigen Auslegung jeder Regel.
Ob eine Site die Vorschriften einhält, hängt auch davon ab, was sie sendet, an wen, und von Einstellungen, die nur der Betreiber der Site ausfüllen kann, etwa
die Postanschrift. Fragen Sie zu Ihrem eigenen Fall eine Anwältin oder einen Anwalt.

## So lesen Sie die Tabellen

- **Code** nennt die Klasse, den View, das Template oder die Einstellung, die die Arbeit erledigt. Pfade sind relativ zum Wurzelverzeichnis der Installation.
- **Test** nennt den Test, der es belegt:
  - `MP-nn`: `tests/tests/kernel/classes/mailpreferences/MailPreferencesCoreTest.php`
  - `LAW-nn`: `tests/tests/kernel/classes/mailpreferences/MailPreferencesComplianceTest.php`
  - `MPH-nn`: `tests/tests/kernel/classes/mailpreferences/MailPreferencesHardeningTest.php` (die am 2026-10-05 geschlossenen Lücken)
  - „Browser“: mit Playwright in Chromium und Firefox auf den Seiten des Moduls geprüft (nicht Teil der PHPUnit-Suite).
- **Site** kennzeichnet eine Anforderung, die eine Einstellung oder eine Entscheidung des Betreibers der Site braucht. Die Software erinnert Sie auf der
  Statusseite (Setup > E-Mail-Einstellungen) und in `./console exp:mail:status` daran.

Führen Sie die beiden Testklassen auf einer Installation aus:

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/mailpreferences/
```

Erwartet auf dem Demonstrationsserver, 2026-10-04: `OK (45 tests, 660 assertions)`. Die Tests erzwingen in ihrem eigenen Prozess den Dateitransport, verwenden
nur Adressen unter der reservierten Domain `.invalid` und entfernen, was sie anlegen. Ohne Installation (der GitHub-Workflow)
werden sie übersprungen. LAW-24 sendet einen POST an den eigenen Webserver der Site; der Test wird übersprungen, wenn der Server nicht erreichbar ist. MPH-09 sendet einen POST an den
Webserver der Site und an Velocity (`velocity.ini [ServerSettings] HTTPSPort`), wenn es läuft.

„Optionale E-Mails“ sind im Folgenden E-Mails einer Kategorie, die nicht notwendig ist: Inhaltsbenachrichtigungen, Zusammenarbeit, Newsletter,
Angebote und Neuigkeiten (Marketing), Anwendungshinweise und jede Kategorie, die ein Administrator oder eine Extension hinzufügt. Notwendige
E-Mails (Kontosicherheit, Bestellungen und Belege, rechtliche und Servicehinweise, Warnungen an die Administration) sind transaktional: Die
unten genannten Gesetze nehmen sie von Einwilligung und Abmeldung aus, und sie dürfen keine Werbung enthalten.

## CAN-SPAM Act (Vereinigte Staaten)

| Anforderung | Code | Test |
|---|---|---|
| Eine klare Möglichkeit zum Abbestellen in jeder kommerziellen E-Mail | `expMailGate::decorate()` fügt jeder optionalen E-Mail die Fußzeile mit einem Abmeldelink per Klick hinzu; `design:mailpreferences/mail/footer.tpl` (Text), `footer_html.tpl` (HTML) | MP-11, LAW-01 |
| ... in jedem Teil einer Multipart-E-Mail | `expMailGate::decoratePart()` | LAW-02 |
| ... die die Lesenden auch nutzen können: Der Link beendet in einer Text-E-Mail seine Zeile | `footer.tpl` schreibt jeden Zeilenumbruch ausdrücklich | LAW-05 |
| Das Abbestellen funktioniert mindestens 30 Tage nach der E-Mail | `[TokenSettings] TTL[unsubscribe]=0`, `TTL[manage]=0` (laufen nie ab); ein Wert unter 60 Tagen wird auf 60 Tage angehoben, und es wird gewarnt (`expMailToken::MIN_LINK_TTL`) | LAW-04, MPH-08 |
| Die Abbestellung wird innerhalb von 10 Werktagen umgesetzt | Das Gate liest die Einstellung bei jedem Versand: Die nächste E-Mail wird sofort blockiert | MP-14, LAW-03 |
| Keine Gebühr, keine Angaben über die Adresse hinaus, kein Schritt über eine Seite hinaus | `mailpreferences/unsubscribe/<token>`: keine Anmeldung (`site.ini [RoleSettings] PolicyOmitList`), eine Schaltfläche; der POST nach RFC 8058 braucht überhaupt nichts | LAW-07, LAW-24, Browser |
| Korrekte Angaben zu „From“, „Reply-To“ und Routing | Das Gate ändert nie From, Reply-To, Subject oder andere Header; es fügt nur `X-Exp-Mail-Category`, `List-Unsubscribe` und `List-Unsubscribe-Post` hinzu. Eine optionale E-Mail mit leerem From bekommt `[MailSettings] EmailSender` oder `AdminEmail` (protokolliert); ohne beides wird sie nicht gesendet | LAW-06, MPH-06 |
| Keine irreführende Betreffzeile | Das Gate ändert nie den Betreff | LAW-06 |
| Den Absender kenntlich machen | `[FooterSettings] OrganisationName` in der Fußzeile jeder optionalen E-Mail; ohne diese Einstellung der Name der öffentlichen Site, nie der eines Administrations-Siteaccess (`expMailSenderDetails::siteName()`) | MP-13, LAW-05, MPH-03 |
| Eine gültige physische Postanschrift | `[FooterSettings] OrganisationAddress` in der Fußzeile; die Statusseite warnt, solange sie oder der Name leer ist | MP-13, LAW-05. **Site**: beides ausfüllen |
| Die Nachricht als Werbung kennzeichnen | Nicht Sache der Software: Der Absender schreibt den Inhalt | **Site** |
| Überwachen, was andere in Ihrem Namen senden | Das Gate-Protokoll `var/<vardir>/log/mailgate.jsonl` und die Statusseite listen jede Entscheidung und jeden Absender von E-Mails ohne Kategorie auf | MP-10, Browser |

## DSGVO und ePrivacy-Richtlinie (Europäische Union)

| Anforderung | Code | Test |
|---|---|---|
| Einwilligung vor Marketing-E-Mails (ePrivacy Art. 13) | Jede optionale Kategorie ist aus, bis die Person sie einschaltet: `[Category_*] DefaultOn=false`; `expMailPreferences::state()`. `DefaultOn=true` wird abgelehnt, und es wird gewarnt, außer bei `[CategorySettings] AllowDefaultOn=enabled` (`expMailCategoryRegistry::refusedDefaultOn()`) | MP-03, LAW-08, MPH-04 |
| Freiwillig: kein Kästchen für die Person angekreuzt | `parts/signup.tpl` und `MailPreferencesPage::signupCategories()`: jedes Kästchen leer, auch auf einem nach einem Fehler erneut angezeigten Formular | LAW-09, Browser |
| Unmissverständlich: Nur eine Handlung der Person zählt | `MailPreferencesPage::storeSignup()` speichert nichts, wenn nichts angekreuzt wurde; eine von Hand gesendete notwendige oder unbekannte Kategorie wird ignoriert | LAW-10 |
| Für den bestimmten Fall: eine Einwilligung pro Zweck | Ein Schalter pro Kategorie; einen einzuschalten lässt die anderen aus; der Hauptschalter ändert keine Kategorie | MP-05, LAW-12 |
| In informierter Weise: Die Person sieht, worin sie einwilligt | Die Seite und das Registrierungsformular zeigen Namen und Beschreibung jeder Kategorie; die E-Mail sagt, warum sie gesendet wurde („Sie erhalten diese E-Mail, weil Sie ... eingeschaltet haben“); die Fußzeile und die Einstellungsseite verlinken die Datenschutzerklärung (`[FooterSettings] PrivacyURL`, sonst menu.ini `[SiteInfo] PrivacyPolicyID`) | LAW-14, LAW-15, MP-11, MPH-07 |
| Nachweisbar: Aufzeichnungen über die Einwilligung (Art. 7 Abs. 1) | `expmail_consent_log`: Zeitpunkt, Kategorie, alter und neuer Zustand, Quelle, der genaue angezeigte Text, IP-Adresse, der handelnde Benutzer, Siteaccess; zusätzlich das Audit-Ereignis `access.user.consent.change` | MP-04, LAW-15, LAW-18 |
| Bestätigtes Opt-in, wo die Adresse jemand anderem gehören könnte | Double Opt-in für Newsletter, Angebote und Neuigkeiten sowie für eine neue E-Mail-Adresse: `DoubleOptIn=true`, `expmail_pending`, `expMailPreferencesService::confirm()`; keine E-Mail der Kategorie, solange die Bestätigung aussteht | MP-06, MP-07, MP-19, LAW-11 |
| Das Öffnen eines Links zählt nicht als Einwilligung | Die Seiten zum Bestätigen und Abbestellen ändern bei GET nichts; das tut nur ihre Schaltfläche | Browser, LAW-24 |
| Widerruf so einfach wie die Einwilligung (Art. 7 Abs. 3) | Dasselbe Kästchen schaltet eine Kategorie aus; „Alle optionalen E-Mails ausschalten“ ist eine Schaltfläche; jede E-Mail hat einen Link zum Abmelden per Klick | LAW-13, LAW-14, MP-14 |
| Auskunftsrecht (Art. 15) und Recht auf Datenübertragbarkeit (Art. 20) | „Meine E-Mail-Daten herunterladen“, JSON und CSV: `mailpreferences/export/<format>`, `expMailPreferences::export()`, `exportToCsv()`; der Download wird aufgezeichnet | MP-15, LAW-16, Browser |
| Recht auf Löschung (Art. 17) | `expMailPreferences::erase()`, `./console exp:mail:preferences erase --yes`: Einstellungen und ausstehende Bestätigungen entfernt, das Einwilligungsprotokoll anonymisiert, der Widerruf als Nachweis behalten; sofort, wenn das Konto entfernt wird (`eZUser::removeUser()` ruft `expMailPreferences::eraseForRemovedAccount()` auf) | MP-15, LAW-17, MPH-05 |
| Speicherbegrenzung (Art. 5 Abs. 1 lit. e) | Cronjob-Teil `mailpreferences`: Das Protokoll von Personen, die nicht mehr da sind, wird nach `[ConsentSettings] RetentionDays` (1095) entfernt; abgelaufene Bestätigungen werden entfernt | MP-07, MP-17 |
| Datenminimierung und Sicherheit (Art. 5, 32) | Die Sperrliste speichert einen mit dem Geheimnis der Site gesalzenen Hash, nie die Adresse; Links sind verschlüsselt (AES-256-GCM), sodass sich eine Adresse in einem Link nicht lesen lässt; das Gate-Protokoll enthält keine Adresse; die Ausgabe der Befehle verbirgt Adressen | MP-08, MP-09, LAW-19 |
| Ein Widerspruch gegen Direktwerbung ist endgültig (Art. 21 Abs. 3) | „Alle optionalen E-Mails beenden“ per Link setzt die Adresse auf die Sperrliste (`unsubscribe_all`); ein Hard Bounce oder eine Beschwerde blockiert sogar einen angeforderten Link oder eine Bestätigungs-E-Mail | MP-09, LAW-20 |
| Änderungen durch Mitarbeitende sind nachvollziehbar | Die Benutzerseite in der Administration zeichnet Änderungen mit der Quelle `admin` und der Benutzer-ID des Administrators auf; der Verlauf der Person sagt „Von einem Administrator geändert“ | LAW-18, Browser |

## CCPA und CPRA (Kalifornien)

| Anforderung | Code | Test |
|---|---|---|
| Recht auf Auskunft | Der Daten-Download (JSON, CSV), der Einwilligungsverlauf auf der Einstellungsseite | MP-15, LAW-16 |
| Recht auf Löschung | `erase()` und der Konsolenbefehl | MP-15, LAW-17 |
| Keine Dark Patterns: gleichwertige Auswahlmöglichkeiten, keine Vorauswahl, keine zusätzlichen Schritte, um Nein zu sagen | Gleichwertige Schalter, nichts angekreuzt für eine neue Person, „Alle optionalen E-Mails ausschalten“ so sichtbar wie „Einschalten“ | LAW-09, LAW-14, Browser |
| Keine Benachteiligung, wenn ein Recht ausgeübt wird | Optionale E-Mails auszuschalten wirkt sich nie auf notwendige E-Mails oder das Konto aus | MP-05, MP-12 |
| Recht, dem Verkauf oder der Weitergabe zu widersprechen | Nicht anwendbar: Das System gibt keine Daten an Dritte weiter | n/a |

## CASL (Kanada)

| Anforderung | Code | Test |
|---|---|---|
| Ausdrückliche Einwilligung vor einer kommerziellen Nachricht | Standardmäßig Opt-in, Double Opt-in für Newsletter und Marketing | MP-03, MP-06, LAW-08, LAW-11 |
| Die Bitte um Einwilligung sagt, wer fragt und wofür | Name und Beschreibung der Kategorie auf der Seite und im Formular | LAW-14, LAW-15 |
| Der Nachweis der Einwilligung wird aufbewahrt | Das Einwilligungsprotokoll | MP-04, LAW-15 |
| Den Absender in jeder Nachricht kenntlich machen | `OrganisationName` in der Fußzeile | LAW-05. **Site** |
| Kontaktangaben: eine Postanschrift und eine Telefonnummer, E-Mail- oder Webadresse, 60 Tage gültig | `OrganisationAddress` und der Verwaltungslink (eine Webadresse) in der Fußzeile | LAW-05. **Site**: Tragen Sie eine Telefonnummer oder Kontaktadresse in `OrganisationAddress` ein, wenn Sie neben der Webseite eine wünschen |
| Ein Abmeldemechanismus in jeder Nachricht, 60 Tage gültig | Link in der Fußzeile und `List-Unsubscribe`; Links laufen standardmäßig nie ab | LAW-01, LAW-04 |
| Abmeldung innerhalb von 10 Werktagen umgesetzt | Sofort | LAW-03 |
| Abmeldung „ohne Weiteres durchführbar“ | Ein Klick (RFC 8058) oder eine Schaltfläche, keine Anmeldung | LAW-07, LAW-24 |

## RFC 8058 und RFC 2369 (Abmeldung per Klick)

| Anforderung | Code | Test |
|---|---|---|
| `List-Unsubscribe` mit einer HTTPS-URI in spitzen Klammern | `expMailGate::decorate()`; die URI lautet `<BaseURL>/mailpreferences/unsubscribe/<token>` | MP-11, LAW-21 |
| `List-Unsubscribe-Post: List-Unsubscribe=One-Click`, exakt | `expMailGate::decorate()` | MP-11, LAW-21 |
| Die URI identifiziert Empfänger und Liste aus sich heraus | Das Token trägt den Empfänger und die Kategorie; jeder Empfänger bekommt eine eigene E-Mail (`[GateSettings] SplitRecipients=enabled`), auch Empfänger unter To und Cc | MP-11, LAW-22 |
| Ein POST mit `List-Unsubscribe=One-Click` meldet ab, ohne Cookies oder Formular-Token | `Exponential\View\Kernel\Mailpreferences\Unsubscribe::oneClick()`: antwortet mit 200 `text/plain`, bei einem defekten Link mit 400, über `MailPreferencesPage::sendResponse()` auf jedem Webserver (PHP-FPM und Velocity) | LAW-24, MPH-01, MPH-09, Browser |
| Ein GET darf nicht abmelden (E-Mail-Scanner öffnen Links) | Die GET-Seite zeigt eine Schaltfläche | LAW-24, Browser |
| Die Header sind von einer DKIM-Signatur abgedeckt | Nicht Sache von Exponential: Der Mailserver signiert | **Site**: `List-Unsubscribe` und `List-Unsubscribe-Post` mit DKIM signieren |

## Am 2026-10-04 gefundene Lücken und was aus ihnen wurde

Die Checkliste hat diese Lücken am 2026-10-04 gefunden. Sechs wurden am 2026-10-05 geschlossen (jede mit einem Test); die übrigen sind Entscheidungen, die die
Software bewusst der Site überlässt.

### Geschlossen am 2026-10-05

| Lücke | Jetzt | Test |
|---|---|---|
| 2. `DefaultOn=true` wurde für eine optionale Kategorie akzeptiert | Abgelehnt: als aus behandelt, eine Warnung in der Debug-Ausgabe und auf der Statusseite (`default_on_refused`). `[CategorySettings] AllowDefaultOn=enabled` behält die Einstellung bei, wo ein Opt-out rechtmäßig ist, und die Statusseite vermerkt das (`default_on_allowed`) | MPH-04 |
| 5. Das Entfernen eines Kontos löschte dessen Einstellungen nicht | `eZUser::removeUser()` ruft `expMailPreferences::eraseForRemovedAccount()` auf: Einstellungen und ausstehende Bestätigungen entfernt, das Einwilligungsprotokoll mit einer `erase`-Zeile anonymisiert, die Widerrufe als Nachweis behalten, die Sperrliste behalten | MPH-05 |
| 6. Die From-Adresse wurde nicht geprüft | Eine optionale E-Mail mit leerem From bekommt `[MailSettings] EmailSender`, sonst `AdminEmail`; das Gate-Protokoll zeichnet `from_fallback` mit der absendenden Datei auf, und die Statusseite zählt es. Ohne beides wird die E-Mail nicht gesendet (`no_sender`) | MPH-06 |
| 7. Kein Link auf die Datenschutzerklärung | `[FooterSettings] PrivacyURL` (eine vollständige Adresse oder ein Pfad der Site), sonst der Knoten aus menu.ini `[SiteInfo] PrivacyPolicyID` des öffentlichen Siteaccess, sonst kein Link und ein Hinweis auf der Statusseite (`privacy_missing`). Verlinkt aus der Text- und HTML-Fußzeile und der Einstellungsseite | MPH-07 |
| 8. Die Gültigkeitsdauer von Links ließ sich unter das gesetzliche Minimum verkürzen | `TTL[unsubscribe]` und `TTL[manage]` zwischen 0 und 60 Tagen werden auf 60 Tage angehoben (`expMailToken::MIN_LINK_TTL`), mit einer Warnung auf der Statusseite (`ttl_below_minimum`); 0 läuft weiterhin nie ab | MPH-08 |
| 9. Die Fußzeile nannte den sendenden Siteaccess („Admin“ bei einem Befehl) | Der Name der öffentlichen Site: der Siteaccess der Anfrage, wenn er öffentlich ist, sonst der DefaultAccess, sonst der erste öffentliche aus `SiteList[]`; ein Siteaccess, der eine Anmeldung erfordert, wird nie verwendet. `OrganisationName` hat weiterhin Vorrang. Jedes E-Mail-Template bekommt `$site_name` | MPH-03 |

Zusammen mit ihnen gefunden: Die Antwort auf die Abmeldung per Klick und jeder Download des Moduls hingen unter Velocity (504, obwohl die Änderung
gespeichert war), weil die Schleife, die die Ausgabepuffer leerte, nie am eigenen Puffer des Workers anhielt. Alle enden jetzt
über `MailPreferencesPage::sendResponse()` (MPH-01, MPH-09).

### Der Site überlassen

1. **Die Postanschrift ist nach einer Installation leer.** `[FooterSettings] OrganisationAddress` wird leer ausgeliefert; der
   Installer fragt danach, die Statusseite und `exp:mail:status` warnen, optionale E-Mails werden aber trotzdem ohne sie gesendet
   (CAN-SPAM, CASL). Tragen Sie sie auf der Statusseite ein (Absenderangaben).
2. **E-Mails ohne Kategorie werden ohne Fußzeile und ohne Abmeldelink gesendet** (so gewollt, aus Gründen der Kompatibilität: Code, der
   vor dem Gate geschrieben wurde, funktioniert weiter). Werden kommerzielle E-Mails von Code gesendet, der `eZMail::setCategory()` nicht aufruft, halten sie
   die Vorschriften nicht ein. Die Statusseite zählt solche E-Mails und nennt ihren Absender. **Site**: Geben Sie ihnen eine Kategorie.
3. **`SplitRecipients=disabled`** sendet optionale E-Mails an mehrere Empfänger zugleich: Sie haben keinen
   `List-Unsubscribe`-Header, und ihre Fußzeile verlinkt auf „Link zusenden“ statt auf einen persönlichen Link zum Abmelden per Klick. Große E-Mail-Anbieter erwarten
   von Massenversendern die Abmeldung per Klick, und das „einfache“ Abbestellen nach CAN-SPAM wird um einen Schritt länger. Behalten Sie die Voreinstellung
   (`enabled`) bei.
4. **DKIM** bleibt dem Mailserver überlassen. RFC 8058 verlangt, dass `List-Unsubscribe` und `List-Unsubscribe-Post` von
   der Signatur abgedeckt sind. **Site**: Konfigurieren Sie den MTA so, dass er beide signiert.
5. **Das Registrierungsformular** zeigt die Kategorien, aber keinen Link auf die Datenschutzerklärung; ein Design, das einen möchte, fügt ihn
   seiner `user/register.tpl` hinzu.

## Newsletter-Statistik (cjw_newsletter 4.2.0)

Die Newsletter-Extension zählt Öffnungen (ein Pixel) und Klicks (eine Weiterleitung) ihrer Ausgaben. Das Lesen einer Person zu zählen, ist
eine Verarbeitung personenbezogener Daten, die eine Einwilligung braucht (DSGVO Art. 6 Abs. 1 lit. a, ePrivacy Art. 5 Abs. 3 für das Pixel); anonyme Summen
identifizieren niemanden. Die Anleitung der Extension ist `extension/cjw_newsletter/doc/statistics.md`.
`ST-nn`: `tests/tests/extension/cjw_newsletter/cjwNewsletterStatisticsTest.php`; `MPE-nn`:
`tests/tests/kernel/classes/mailpreferences/MailPreferencesErasedHookTest.php`.

| Anforderung | Code | Test |
|---|---|---|
| Kein Tracking, außer die Site und die Liste (oder der Versand) wählen es; standardmäßig aus | `cjw_newsletter.ini [TrackingSettings] Tracking=disabled`; `cjwnl_list.tracking_mode` 0; `CjwNewsletterTracking::sendMode()` | ST-13, ST-05 |
| Zählung pro Person nur mit der eigenen, vorherigen Einwilligung der Person für diesen bestimmten Fall; nicht vorab angekreuzt | Kategorie `newsletter_statistics` (`DefaultOn=false`, optional) auf der Einstellungsseite, Handler `CjwNewsletterStatisticsCategoryHandler`; ohne Einwilligung trägt die E-Mail nur einen anonymen Schlüssel | ST-02, ST-12 |
| Die Einwilligung wird zusammen mit dem aufgezeichnet, was die Person gesehen hat | das Einwilligungsprotokoll des Kernels (`expConsentLog`), wie für jede Kategorie | ST-12 |
| Der Widerruf ist so einfach wie die Einwilligung und wirkt sofort | derselbe Schalter; `changed()` entfernt die Zeilen der Person; die Einwilligung wird bei jedem Öffnen und Klick erneut geprüft | ST-05, ST-06 |
| Die Löschung entfernt die Statistik der Person | der Kernel ruft die optionale Handler-Methode `erased()` aus `expMailPreferences::erase()` und beim Entfernen des Kontos auf | ST-06, MPE-01 bis MPE-03 |
| Speicherbegrenzung | Zeilen pro Person werden nach `[TrackingSettings] PersonRetentionMonths` (12) entfernt, täglich durch die E-Mail-Warteschlange und durch `ext:cjw_newsletter:statistics --cleanup`; Summen bleiben erhalten | ST-07 |
| Datenminimierung: keine Adresse und kein Name in einer URL, Berichte und Exporte zeigen Summen | Schlüssel `p<send item hash>` / `a<send>x<variant>`, HMAC-Signatur; `CjwNewsletterStatisticsReport` | ST-02, ST-11 |
| Keine offene Weiterleitung, keine gefälschten Zählungen | die Weiterleitung geht nur auf die URL, die für die gesendete Ausgabe gespeichert ist, mit einer gültigen Signatur desselben Versands; sonst 404 | ST-04 |
| Sicherheit der Endpunkte | keine Session, kein Cookie, `Cache-Control: no-store`, `Referrer-Policy: no-referrer`; CSV-Zellen entschärft | ST-03, ST-04, ST-11 |

**Site**: Nennen Sie in der Datenschutzerklärung die Newsletter-Statistik, was gezählt wird und die Aufbewahrungsdauer, und lassen Sie den Schalter der
Site aus, wo keine Rechtsgrundlage besteht. Anonyme Summen laden für alle trotzdem ein Pixel von der Site (in Modus 2 auch für die
Personen ohne Einwilligung); wo die örtliche Auslegung von ePrivacy Art. 5 Abs. 3 auch dafür eine Einwilligung verlangt, schalten Sie
das Pixel mit `[TrackingSettings] OpenPixel=disabled` aus, oder lassen Sie das Tracking aus.

## Siehe auch

- [E-Mail-Einstellungen: die Spezifikation](mail-einstellungen.md)
- [Befehle für die E-Mail-Einstellungen](mail-einstellungen-cli.md)
- [E-Mail-Einstellungen: die Anleitung für Benutzer](../../../en/us/features/6.0/mail-preferences.md)
- [E-Mail-Einstellungen: die Anleitung für Administratoren](../../anleitungen/mail-einstellungen-administrator.md)
- [E-Mail-Einstellungen: die Anleitung für Entwickler](../../anleitungen/mail-einstellungen-entwickler.md)
- [Upgrade-Hinweise: E-Mail-Einstellungen](../../../en/us/bc/6.0/mail-preferences.md)
