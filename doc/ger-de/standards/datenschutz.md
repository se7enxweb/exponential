---
title: Datenschutzstandards
audience: developer, admin
category: standards
order: 40
summary: Was Exponential für die DSGVO, die ePrivacy-Richtlinie und CAN-SPAM bereitstellt (Einwilligungsnachweise, Abmeldung mit einem Klick, Löschung, Datenminimierung und Aufbewahrung in Logs, Cookies und Drittanbieterdienste) und was beim Betreiber der Site verbleibt.
keywords: Datenschutz, DSGVO, Verordnung 2016/679, ePrivacy, Cookie, Cookie-Banner, Einwilligung, Abmeldung, Recht auf Löschung, Auskunftsrecht, Datenexport, Aufbewahrung, Pseudonymisierung, IP-Maskierung, Webanalyse, Auftragsverarbeiter, privacy, data protection, GDPR, consent, CAN-SPAM, CASL, CCPA, unsubscribe, List-Unsubscribe, RFC 8058, right to erasure, retention, reCAPTCHA, hCaptcha
type: reference
---
# Datenschutzstandards

Diese Seite richtet sich an Administratoren, die für die personenbezogenen Daten verantwortlich sind, die eine Site
verarbeitet, und an Entwickler, die sie erweitern. Sie nennt die Datenschutzgesetze, die für ein Content-Management-
System am wichtigsten sind, beschreibt, was Exponential 6.0.x für jedes davon bereitstellt, und wo die Arbeit bei der
Site verbleibt.

> **Warnung** Exponential ist Software, kein Verantwortlicher im Sinne des Datenschutzrechts. Die Einhaltung der DSGVO,
> der ePrivacy-Regeln oder von CAN-SPAM liegt in der Verantwortung der Organisation, die die Site betreibt: ihrer
> Zwecke, ihrer Rechtsgrundlagen, ihrer Datenschutzerklärung, ihrer Auftragsverarbeiter und ihrer Konfiguration. Diese
> Seite beschreibt Funktionen; sie ist keine Rechtsberatung.

## Zusammenfassung

| Standard | Version | Status | Nachweis (kurz) |
|---|---|---|---|
| Datenschutz-Grundverordnung (DSGVO) | Verordnung (EU) 2016/679 | **Teilweise erfüllt** (Funktionen vorhanden) | Einwilligungsprotokoll, E-Mail-Kategorien mit Opt-in, Löschung beim Entfernen eines Kontos, Pseudonymisierung im Audit, IP-Maskierung in Logs, keine Assets von Drittanbietern in der Administrationsoberfläche; kein allgemeines „alle meine Daten exportieren“ |
| ePrivacy-Richtlinie (Cookies, Werbe-E-Mails) | Richtlinie 2002/58/EG, geändert durch 2009/136/EG | **Teilweise erfüllt** | nur funktionale Cookies aus dem Kernel; Opt-in für Werbe-E-Mails; **kein Cookie-Einwilligungsbanner** |
| CAN-SPAM Act (Vereinigte Staaten) | 15 U.S.C. 7701 ff. (2003) | **Erfüllt** (für E-Mails, die über das Mail-Gate gesendet werden) | Abmeldelink und `List-Unsubscribe` in optionalen E-Mails, sofort wirksam; Postanschrift des Absenders bleibt Sache der Site |
| Abmeldung mit einem Klick | RFC 8058 mit RFC 2369 | **Erfüllt** | `List-Unsubscribe` und `List-Unsubscribe-Post` werden von `expMailGate::decorate()` ergänzt; die DKIM-Signatur bleibt Sache des Mailservers |

## DSGVO

### Was sie ist

Die Datenschutz-Grundverordnung, Verordnung (EU) 2016/679 des Europäischen Parlaments und des Rates, gilt seit dem
25. Mai 2018. Sie regelt die Verarbeitung personenbezogener Daten von Personen in der EU: Rechtsgrundlage,
Einwilligung, Rechte der betroffenen Personen, Datenschutz durch Technikgestaltung und datenschutzfreundliche
Voreinstellungen (Art. 25) sowie Sicherheit (Art. 32).

### Was Exponential bereitstellt

| DSGVO-Thema | Was mitgeliefert wird | Wo |
|---|---|---|
| Einwilligung (Art. 4 Nr. 11, Art. 7) | E-Mail-Kategorien, die ausgeschaltet bleiben, bis die Person sie einschaltet (`[Category_*] DefaultOn=false`; `DefaultOn=true` wird abgelehnt, sofern nicht `[CategorySettings] AllowDefaultOn=enabled` gesetzt ist); ein Schalter je Zweck; Double-Opt-in für Newsletter und Werbung | [E-Mail-Einstellungen](../funktionen/6.0/e-mail-einstellungen.md), [die Checkliste zu den Gesetzen](../spezifikationen/6.0/mail-einstellungen-compliance.md) |
| Nachweis der Einwilligung (Art. 7 Abs. 1) | Tabelle `expmail_consent_log`: Zeitpunkt, Kategorie, alter und neuer Zustand, Quelle, der genau angezeigte Text, handelnder Benutzer, Siteaccess; Audit-Ereignis `access.user.consent.change` | `kernel/classes/mailpreferences/expmailpreferences.php` |
| Widerruf so einfach wie die Einwilligung (Art. 7 Abs. 3) | Derselbe Schalter, eine Schaltfläche „Alle optionalen E-Mails ausschalten“, ein Ein-Klick-Link in jeder optionalen E-Mail, keine Anmeldung nötig | [E-Mail-Einstellungen](../funktionen/6.0/e-mail-einstellungen.md) |
| Auskunftsrecht (Art. 15) und Datenübertragbarkeit (Art. 20) | E-Mail-Einstellungen und Einwilligungsverlauf als JSON oder CSV herunterladbar, durch die Person selbst (`mailpreferences/export`) oder durch einen Administrator (`./console exp:mail:preferences export`); Audit-Einträge von oder über einen Benutzer mit `./console exp:audit export --subject-user=<id>` | [E-Mail-Einstellungen für Administratoren](../anleitungen/mail-einstellungen-administrator.md), [Audit-Leitfaden 4.12](../bc/6.0/audit.md#412-datenschutz-und-dsgvo-aufgaben) |
| Recht auf Löschung (Art. 17) | Beim Entfernen eines Benutzerkontos ruft `eZUser::removeUser()` die Methode `expMailPreferences::eraseForRemovedAccount()` auf: Einstellungen und ausstehende Bestätigungen werden entfernt, das Einwilligungsprotokoll wird anonymisiert; auf Anfrage `./console exp:mail:preferences erase --yes`. Audit-Einträge werden für ihre Aufbewahrungsdauer nach Art. 17 Abs. 3 lit. b und e aufbewahrt und pseudonymisiert | [die Checkliste zu den Gesetzen](../spezifikationen/6.0/mail-einstellungen-compliance.md), [Audit-Leitfaden 4.12](../bc/6.0/audit.md#412-datenschutz-und-dsgvo-aufgaben) |
| Einschränkung der Verarbeitung (Art. 18) | **Deaktivieren und verschieben** (Opt-in, `[UserSettings] DeactivateAndMove=enabled`) deaktiviert Konten und verschiebt sie umkehrbar in eine versteckte Gruppe ohne Rollen; **Benutzer > Benutzerkonten prüfen** (`user/hygiene`) findet Konten, die seit N Tagen nicht genutzt wurden | [Benutzerkonten prüfen](../funktionen/6.0/benutzerpflege.md) |
| Datenminimierung in Logs (Art. 5 Abs. 1 lit. c, Art. 25) | Audit: Adressen auf /24 gekürzt (IPv6 /48), Sitzungs-IDs gehasht, User-Agents gekürzt, keine Passwörter, Tokens oder POST-Inhalte, je Feld `full / truncate / hash / off` in `[AuditPrivacySettings] Field[]`, Pseudonymisierung nach `PseudonymiseAfterDays`. Anfrage-Logs: `MaskIP=enabled`, jeder Query-Wert wird durch `***` ersetzt. Log-Viewer: `settings/logviewer.ini [RedactionSettings] MaskIPs=enabled`, E-Mail-Adressen und Geheimnisse maskiert | [Prüfprotokoll](../funktionen/6.0/pruefprotokoll.md), [Anfrage-Logs](../funktionen/6.0/anfrageprotokolle.md), [Log-Viewer](../funktionen/6.0/log-betrachter.md#datenschutz-was-maskiert-wird) |
| Speicherbegrenzung (Art. 5 Abs. 1 lit. e) | Audit: 90 Tage aktiv und 2 Jahre archiviert je Kanal, danach automatisch entfernt. `settings/logviewer.ini [RotationSettings] MaxAgeDays[404]=30`, `MaxAgeDays[security]=90`, `MaxAgeDays[csp]=30` | [Audit-Leitfaden 4.3](../bc/6.0/audit.md#43-aufbewahrung), [Log-Rotation](../funktionen/6.0/log-rotation.md) |
| Sicherheit der Verarbeitung (Art. 32) | Siehe [Sicherheitsstandards](sicherheit.md): Passwort-Hashing, CSRF-Tokens, Flags des Sitzungs-Cookies, Prüfprotokoll | [Sicherheitsstandards](sicherheit.md) |
| Keine Datenübermittlung an Dritte durch die Administrationsoberfläche | jQuery und die übrigen Admin-Skripte werden lokal ausgeliefert (`extension/ezjscore/settings/ezjscore.ini [eZJSCore] LoadFromCDN=disabled`); das Design admin4 lädt keine Webfonts oder Skripte von anderen Hosts | `design/admin4/templates/pagelayout.tpl` |
| Fehlerberichte ohne personenbezogene Daten | Der Fehlerbericht der Hilfe erstellt seine Installationsübersicht aus einer festen Liste von Fakten und entfernt E-Mail-Adressen, IP-Adressen, Zugangsdaten und Pfade außerhalb des Installationsverzeichnisses (`scrub()`); keine Benutzer- oder Sitzungsdaten | `kernel/classes/help/exphelpissuereport.php`, [Hilfesystem](../funktionen/6.0/hilfesystem.md) |

### Status

**Teilweise erfüllt**, in dem Sinne, dass die Software die oben genannten Funktionen bereitstellt. Der Betreiber der
Site muss weiterhin Zwecke und Rechtsgrundlagen festlegen, das Verzeichnis von Verarbeitungstätigkeiten führen
(Art. 30), die Datenschutzerklärung verfassen, mit jedem hinzugefügten Dritten Auftragsverarbeitungsverträge
abschließen und Anfragen beantworten.

### Bekannte Lücken

- **Keine einzelne Funktion „alle meine Daten exportieren“ oder „mich löschen“.** Ein Benutzerkonto ist ein
  Inhaltsobjekt; seine Felder, seine Inhalte, seine Informationssammlungen
  ([Gesammelte Informationen](../anleitungen/gesammelte-informationen.md)), seine Bestellungen und seine Sitzungen erreichen Sie
  über die einzelnen Werkzeuge der Administrationsoberfläche. Nur E-Mail-Einstellungen und Audit-Einträge haben einen
  eigenen Export.
- **Das Entfernen von Benutzern erfolgt manuell und Konto für Konto**: Ein Benutzerobjekt kommt nie in den Papierkorb,
  und **Benutzerkonten prüfen** entfernt keine Konten ([Benutzerkonten prüfen](../funktionen/6.0/benutzerpflege.md)).
- **Standardmäßig Logs ohne Altersgrenze**: Abgesehen von den 404-, Sicherheits- und CSP-Logs werden die Kernel-Logs
  (`error.log`, `debug.log` und andere) nach Größe rotiert; `MaxAgeDays` ist für sie nicht gesetzt. Die Zugriffslogs
  des Webservers liegen außerhalb von Exponential.
- **Formulare zur Informationssammlung** speichern, was Besucher eingeben; die Site muss selbst einen
  Einwilligungstext und eine Aufbewahrungsregel ergänzen.
- **Suchbegriffe** werden gespeichert und können personenbezogene Daten enthalten
  ([URLs und Aliase](../anleitungen/urls-und-aliase.md)).

## ePrivacy-Richtlinie (Cookies)

### Was sie ist

Die Richtlinie 2002/58/EG über die Privatsphäre und die elektronische Kommunikation, geändert durch die Richtlinie
2009/136/EG. Ihr Art. 5 Abs. 3 verlangt eine Einwilligung, bevor Informationen auf dem Endgerät eines Nutzers
gespeichert oder ausgelesen werden, sofern dies nicht für einen vom Nutzer gewünschten Dienst unbedingt erforderlich
ist; ihr Art. 13 verlangt eine vorherige Einwilligung für Werbe-E-Mails.

### Was Exponential bereitstellt

Cookies, die der Kernel und die mitgelieferten Administrationsdesigns setzen:

| Cookie | Gesetzt von | Zweck | Wann |
|---|---|---|---|
| Sitzungs-Cookie (`[Session] SessionNamePrefix=eZSESSID`, plus ein Suffix) | `lib/ezsession/classes/ezsession.php` | Anmeldung und Formularzustand; `HttpOnly`, `SameSite=Lax`, `Secure` über HTTPS | wenn eine Sitzung beginnt |
| `is_logged_in` | `kernel/private/classes/ezpkernelweb.php` | lässt HTTP-Caches angemeldete Benutzer überspringen | solange angemeldet |
| `exp_setup_wizard` | `kernel/classes/expmaintenance.php` | der eigene Durchlauf des Einrichtungsassistenten | nur während der Installation |
| `eZMobileDeviceDetect` | `kernel/private/classes/ezpmobiledeviceregexpfilter.php` | merkt sich die Erkennung mobiler Geräte | nur mit `[SiteAccessSettings] DetectMobileDevice=enabled` (Standard `disabled`) |
| `adminNavigationTab` und andere Cookies für den Oberflächenzustand | `design/admin4/javascript/node_tabs.js`, `contentstructuremenu_dynamic.js`, `ezajaxsubitems_expdatatable.js` | geöffneter Tab, Zustand des Baums, Tabellenspalten in der Administrationsoberfläche | nur in der Administrationsoberfläche |

Das Design admin4 speichert seine Anzeigeoptionen (Farbmodus, Seitenspalten, Menügruppen) für einen angemeldeten
Benutzer als Benutzereinstellungen und auf Seiten ohne angemeldeten Benutzer im `localStorage` des Browsers
(`design/admin4/javascript/a4_head.js`). Alle diese dienen einer vom Benutzer gewünschten Funktion; keines wird zum
Tracking verwendet. Werbe-E-Mails erfordern ein Opt-in (siehe DSGVO oben).

### Status

**Teilweise erfüllt.** Der Kernel setzt nur funktionale Cookies. Es gibt **kein Cookie-Einwilligungsbanner und keinen
Consent-Manager** im Kernel oder in den mitgelieferten Designs (in `kernel/`, `design/` oder `settings/` wurde keiner
gefunden); eine Site, die Webanalyse, Einbettungen oder Werbung hinzufügt, muss daher einen ergänzen.

### Drittanbieterdienste in mitgelieferten Extensions

Diese Extensions werden über `composer.json` (`require`) mit Exponential installiert, sind aber **nicht aktiv**, bis
eine Site sie aktiviert (`settings/site.ini [ExtensionSettings] ActiveExtensions[]` enthält bei Auslieferung nur
`xrowmetadata`; der Einrichtungsassistent fügt `ezjscore`, `ezoe` und `ezformtoken` hinzu). Sind sie aktiviert, senden
sie Besucherdaten an einen Dritten, der dann als Auftragsverarbeiter oder Verantwortlicher handelt:

| Extension | Dritter | Einwilligung / Hinweis nötig |
|---|---|---|
| `bcwebsitestatistics` | Google Analytics / Google Tag Manager (`bcwebsitestatistics.ini`: `urchin.js`, `googletagmanager.com/gtag/js`) | ja; die Extension hat keine eigene Einwilligungssperre |
| `recaptcha` | Google reCAPTCHA | ja, nach den meisten Auslegungen von Art. 5 Abs. 3; in der Datenschutzerklärung erwähnen |
| `hcaptcha` | hCaptcha | wie oben |
| `sevenx_authentication_2fa` (Handler für Social Login) | der gewählte Identitätsanbieter (Google, Facebook und andere) | der Benutzer wählt ihn; in der Datenschutzerklärung erwähnen |
| Statistik von `cjw_newsletter` | keiner (auf der Site), aber personenbezogene Zählung von Öffnungen und Klicks | nur mit vorheriger Einwilligung in die Kategorie `newsletter_statistics` |

Ob ein Site-Paket eine dieser Extensions aktiviert, ist hier **nicht geprüft**; prüfen Sie `ActiveExtensions[]` in
`settings/override/site.ini.append.php`.

## CAN-SPAM Act und Abmeldung mit einem Klick

### Was er ist

Der Controlling the Assault of Non-Solicited Pornography And Marketing Act von 2003 (Vereinigte Staaten, 15 U.S.C.
7701 ff.) verlangt in jeder kommerziellen E-Mail eine funktionierende Abmeldemöglichkeit, die innerhalb von zehn
Werktagen umgesetzt wird, korrekte Header-Angaben und die Postanschrift des Absenders. RFC 8058 (2017) definiert den
Ein-Klick-Header `List-Unsubscribe-Post`, den große Postfachanbieter heute von Massenversendern verlangen, aufbauend auf
`List-Unsubscribe` aus RFC 2369.

### Was Exponential bereitstellt

- Jede optionale E-Mail passiert das Mail-Gate, das `List-Unsubscribe` (einen HTTPS-Link auf
  `mailpreferences/unsubscribe/<token>`), `List-Unsubscribe-Post: List-Unsubscribe=One-Click` und eine Fußzeile mit
  dem Grund und dem Abmeldelink ergänzt (`expMailGate::decorate()`).
- Die Abmeldung braucht keine Anmeldung, kein Formular-Token und kein Cookie; ein POST mit
  `List-Unsubscribe=One-Click` wirkt sofort. Links laufen standardmäßig nicht ab.
- Das Gate schreibt `From`, `Reply-To` und `Subject` nie um.
- Notwendige E-Mails (Passwort-Zurücksetzung, Aktivierung, Bestellungen) sind von optionalen E-Mails getrennt und
  enthalten keine Werbung.
- Dieselben Funktionen sind in [der Checkliste zu den Gesetzen](../spezifikationen/6.0/mail-einstellungen-compliance.md)
  CASL (Kanada) und CCPA/CPRA (Kalifornien) zugeordnet.

### Status

**Erfüllt** für E-Mails, die über das Mail-Gate laufen; folgende Punkte bleiben Sache der Site:

- die Postanschrift des Absenders in der Fußzeile (Absenderangaben auf der Statusseite);
- jeder kommerziellen E-Mail eine Kategorie zu geben: Eine E-Mail, die ohne `eZMail::setCategory()` gesendet wird,
  erhält weder Fußzeile noch Abmeldelink (aus Kompatibilitätsgründen so belassen; die Statusseite zählt solche
  E-Mails und nennt ihren Absender);
- die DKIM-Signatur der beiden Header (erledigt der Mailserver);
- den standardmäßigen Versand je Empfänger beizubehalten: `SplitRecipients=disabled` sendet an mehrere Empfänger
  gleichzeitig, ohne `List-Unsubscribe`.

## Verwandte Seiten

- [Sicherheitsstandards](sicherheit.md), [Standards zur Barrierefreiheit](barrierefreiheit.md), [Standards der Webplattform](webplattform.md)
- [E-Mail-Einstellungen](../funktionen/6.0/e-mail-einstellungen.md) und [die Checkliste zu den Gesetzen](../spezifikationen/6.0/mail-einstellungen-compliance.md)
- [Prüfprotokoll](../funktionen/6.0/pruefprotokoll.md)
- [Sicherheitshärtung, 13.13 E-Mail: Einwilligung und Unterdrückung](../installation/13-sicherheitshaertung.md#1313-mail-einwilligung-und-sperrliste)
