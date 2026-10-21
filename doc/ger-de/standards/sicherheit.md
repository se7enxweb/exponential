---
title: Sicherheitsstandards
audience: developer, admin
category: standards
order: 30
summary: Exponential gemessen an den OWASP Top 10 (2021), OWASP ASVS 4.0.3, Content Security Policy Level 3 und den OWASP Cheat Sheets, mit den Einstellungen, Klassen und Tests hinter jeder Gegenmaßnahme.
keywords: Sicherheit, OWASP, OWASP Top 10, ASVS, Verifikationsstandard für Anwendungssicherheit, CSP, Content Security Policy, Nonce, CSRF, XSS, SQL-Injection, SSRF, Passwort-Hashing, bcrypt, Sitzung, Cookie, SameSite, HSTS, Clickjacking, X-Frame-Options, frame-ancestors, Zwei-Faktor-Anmeldung, 2FA, Prüfprotokoll, Offenlegung von Schwachstellen, security, application security verification standard, content security policy, SQL injection, password hashing, session, two-factor, audit trail, vulnerability disclosure
type: reference
---
# Sicherheitsstandards

Diese Seite richtet sich an Entwickler und Administratoren, die Exponential 6.0.x zu den Sicherheitsstandards in
Beziehung setzen müssen, nach denen ein Prüfer oder ein Kunde fragt. Sie ordnet die OWASP Top 10 den Schutzmaßnahmen
zu, die mit dem Kernel ausgeliefert werden, gibt an, wie weit der OWASP Application Security Verification Standard
erfüllt ist, erklärt die Unterstützung für Content Security Policy und listet auf, welche Empfehlungen der OWASP Cheat
Sheets sich in Code und Einstellungen wiederfinden. Die praktischen Schritte zur Härtung finden Sie unter
[Sicherheitshärtung](../hilfe/sicherheitshaertung.md) und in
[Kapitel 13 des Installationsleitfadens](../installation/13-sicherheitshaertung.md).

## Zusammenfassung

| Standard | Version | Status | Nachweis (kurz) |
|---|---|---|---|
| OWASP Top 10 | 2021 | **Teilweise erfüllt** | Gegenmaßnahmen für alle zehn Kategorien; Lücken beim Standard für die Kontosperre, bei der Prüfung der Abhängigkeiten in der CI und bei der CSP der öffentlichen Website |
| OWASP Application Security Verification Standard (ASVS) | 4.0.3 | **Nicht geprüft** | keine ASVS-Bewertung durchgeführt; viele Kontrollen der Stufe 1 vorhanden (siehe unten) |
| Content Security Policy | Level 3 (W3C Working Draft) | **Teilweise erfüllt** | Kernel-Richtlinie mit Nonces pro Anfrage, `report-uri` und Reporting API, Sammelstelle `csp/report`; standardmäßig nur berichtend, auf Websites aus |
| OWASP Cheat Sheet Series | aktuell | **Teilweise erfüllt** | CSRF-Tokens, Speicherung von Passwörtern, Sitzungscookies, Sicherheitsheader, Ausgabekodierung, Prüfung von Uploads |
| Koordinierte Offenlegung von Schwachstellen | ISO/IEC 29147 (Praxis) | **Teilweise erfüllt** | `SECURITY.md` mit einem privaten Meldeweg und den unterstützten Versionen; keine `security.txt` (RFC 9116) mitgeliefert |

## OWASP Top 10 (2021)

### Was sie ist

Die OWASP Top 10 sind das Sensibilisierungsdokument des Open Worldwide Application Security Project zu den zehn
kritischsten Kategorien von Sicherheitsrisiken in Webanwendungen. Die aktuelle Ausgabe ist von 2021.

### Wie Exponential sie erfüllt

| ID | Kategorie | Gegenmaßnahmen in Exponential | Status |
|---|---|---|---|
| A01 | Broken Access Control (fehlerhafte Zugriffskontrolle) | Rollen und Richtlinien mit Einschränkungen, die der Kernel für jede Modulansicht prüft (`kernel/classes/ezrole.php`, `eZUser::hasAccessTo()`); Lesezugriff auf Inhalte pro Knoten; Admin-Links nur, wenn die Richtlinie es erlaubt ([Admin-Links folgen den Berechtigungen](../funktionen/6.0/admin-links-folgen-den-berechtigungen.md)); Downloads nur über `content/download`, das den Lesezugriff prüft; die Rewrite-Regeln weisen PHP-Dateien unterhalb des Document Root ab; sichere Weiterleitungen (`eZRedirectManager`, `[SiteSettings] AllowedRedirectHosts`, [Sichere Weiterleitungen](../funktionen/6.0/sichere-weiterleitungen.md)); Setup-Assistent auf einer installierten Website gesperrt (`expSetupWizardGuard`) | Teilweise erfüllt |
| A02 | Cryptographic Failures (kryptografische Fehler) | Passwörter gehasht mit `password_hash()` von PHP (`[UserSettings] HashType=php_default`, derzeit bcrypt) und bei der Anmeldung von älteren Hash-Typen aktualisiert (`UpdateHash=true`); REST-/OAuth-Tokens und Aktivierungs-Hashes aus `random_bytes()` (Befunde F-01, F-02); Sitzungscookie `Secure` über HTTPS (`[Session] CookieSecure=auto`); HSTS verfügbar (`SecurityHeaders[Strict-Transport-Security]`, Velocity `[HTTPSSettings] HSTSMaxAge`) | Teilweise erfüllt |
| A03 | Injection | Template-Ausgabe mit dem Operator `wash` maskiert (`htmlspecialchars` mit `ENT_QUOTES`); SQL gebaut mit `eZDBInterface::escapeString()` und Ganzzahl-Umwandlungen, überprüft in der Härtung von 6.0.13 (SEC-01 bis SEC-04); Shell-Argumente über `escapeshellarg()` (SEC-05, SEC-06, F-05); CRLF aus ausgehenden Header-Werten entfernt (F-03); Eingabeprüfungen der Datentypen ([Härtung von Datentypen und Eingaben](../spezifikationen/6.0/datentyp-eingabehaertung.md)) | Teilweise erfüllt |
| A04 | Insecure Design (unsicheres Design) | Sichere Standardwerte seit September 2026 ([Sicherheitsstandardwerte](../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md)); Anmeldung, die bei Konten ohne Einstellungen sicher scheitert (F-06); `ManagedByDeployment`, um Einstellungen, Templates und Pakete in der Produktion zu sperren ([Vom Deployment verwaltete Konfiguration](../funktionen/6.0/durch-deployment-verwaltet.md)); aus Angreifersicht geschriebener Leitfaden ([Sicherheitshärtung](../hilfe/sicherheitshaertung.md)) | Teilweise erfüllt |
| A05 | Security Misconfiguration (Sicherheitsfehlkonfiguration) | Sicherheitsheader auf jeder Seite (`[HTTPHeaderSettings] SecurityHeaders[]`); Debug-Ausgabe auf aufgeführte Adressen beschränkt; Installer-Dateien und Behandlung des initialen Passworts; **Setup > Upgrade-Prüfung** vergleicht Dateien mit `share/filelist.md5` ([Prüfung der Dateikonsistenz](../funktionen/6.0/dateikonsistenzpruefung.md)) | Teilweise erfüllt |
| A06 | Vulnerable and Outdated Components (verwundbare und veraltete Komponenten) | Mitgeliefertes jQuery durch jQuery 4.0.0 ersetzt und YUI entfernt ([jQuery 4 und Entfernung von YUI](../funktionen/6.0/jquery4-und-entfernung-von-yui.md)); unterstützte Versionen in `SECURITY.md`; `composer audit` im Härtungsleitfaden empfohlen | Teilweise erfüllt |
| A07 | Identification and Authentication Failures (Fehler bei Identifizierung und Authentifizierung) | Mindestlänge des Passworts 10 (`[UserSettings] MinPasswordLength`); gleicher Aufwand für unbekannte Anmeldenamen und falsche Passwörter; einheitliche Antworten bei „Passwort vergessen“; Sitzungs-ID bei der Anmeldung neu erzeugt; andere Sitzungen bei einer Passwortänderung beendet (`[PasswordSettings] EndOtherSessions=enabled`); optionale Sperre (`MaxNumberOfFailedLogin`); Zwei-Faktor-Anmeldung mit `sevenx_authentication_2fa` ([Anmeldung in zwei Schritten](../anleitungen/zwei-faktor-anmeldung.md)) | Teilweise erfüllt |
| A08 | Software and Data Integrity Failures (Fehler bei der Integrität von Software und Daten) | Unveränderliche Release-Tags; Manifest `share/filelist.md5`, geprüft in der CI (`.github/workflows/quality.yml`, „File manifest“) und im Admin; CSRF-Tokens in jedem Formular (`ezformtoken`); SVG-Uploads bereinigt (`expSvgSanitizer`); Audit-Einträge als Hash-Kette mit signierten Manifesten | Teilweise erfüllt |
| A09 | Security Logging and Monitoring Failures (Fehler bei Sicherheitsprotokollierung und Überwachung) | Manipulationssicheres Prüfprotokoll, standardmäßig an (`audit.ini [AuditSettings] Audit=enabled`), mit Alarmregeln wie `brute_force` ([Prüfprotokoll](../funktionen/6.0/pruefprotokoll.md)); Anfrageklassifizierer, der Sondierungen und Angriffe in `var/log/security.log` schreibt ([Anfrageprotokolle](../funktionen/6.0/anfrageprotokolle.md)); CSP-Verstoßberichte in `var/log/csp.log` | Erfüllt |
| A10 | Server-Side Request Forgery | Das „Hochladen von URL“ des Online-Editors akzeptiert nur `http`/`https`, löst den Host einmal auf, weist Loopback-, private, Link-Local-, Multicast- und reservierte Adressen ab und prüft jede Weiterleitung erneut ([TinyMCE 8](../funktionen/6.0/online-editor-tinymce8.md)); die Linkprüfung (`kernel/classes/explinkcheck.php`) testet private Hosts nur, wenn sie in `AllowedPrivateHosts` aufgeführt sind | Teilweise erfüllt |

### Status

**Teilweise erfüllt.** Jede Kategorie hat eingebaute Gegenmaßnahmen, und der Kernel hat drei dokumentierte
Härtungsdurchgänge erhalten ([6.0.13](../spezifikationen/6.0/sicherheitshaertung-6.0.13.md),
[August 2026](../spezifikationen/6.0/sicherheitshaertung-2026-08.md),
[Standardwerte vom September 2026](../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md)). „Erfüllt“ erforderte eine
externe Bewertung, die nicht durchgeführt wurde.

### Bekannte Lücken

- **Die Kontosperre ist standardmäßig aus**: `settings/site.ini [UserSettings] MaxNumberOfFailedLogin=0`. Brute-Force-
  Versuche werden von den Alarmregeln des Audits erkannt, aber vom Kernel nicht gedrosselt. Die Ratenbegrenzung bleibt
  dem Webserver oder einem Anfragefilter wie request-shield überlassen
  ([Sicherheitshärtung](../hilfe/sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield)).
- **Die Zwei-Faktor-Anmeldung muss eingeschaltet werden** (eine Extension und ein Login-Handler sind zu konfigurieren).
- **Keine Prepared Statements** in der Datenbankschicht: `lib/ezdb/classes/` maskiert Werte (`escapeString()`), statt
  Parameter zu binden. Code in Extensions muss derselben Disziplin folgen; der Kernel kann das nicht erzwingen.
- **Die Ausgabemaskierung geschieht nicht automatisch**: Ein Template muss `|wash` anwenden; Tests wie
  `tests/tests/design/AdminStoredValueEscapingTest.php` und `AdminTreeMenuEscapingTest.php` sichern bestimmte Stellen
  ab.
- **Ältere Passwort-Hash-Typen** (`md5_password`, `md5_user`, `md5_site`, `mysql`, `plaintext`) können für migrierte
  Konten weiterhin gelesen werden, bis sich diese erneut anmelden und `UpdateHash` sie ersetzt.
- **Keine Schwachstellenprüfung der Abhängigkeiten in der CI**: Die Workflows in `.github/workflows/` führen PHPStan,
  PHPUnit, Syntax-, Codestil- und Manifestprüfungen aus, aber kein `composer audit`, kein Dependabot oder
  Vergleichbares.
- **HSTS** ist in `site.ini` standardmäßig leer (`SecurityHeaders[Strict-Transport-Security]=`) und muss vom Betreiber
  gesetzt werden, sobald die gesamte Website nur noch über HTTPS erreichbar ist.

## OWASP ASVS 4.0.3

### Was er ist

Der OWASP Application Security Verification Standard 4.0.3 (Oktober 2021) ist ein Katalog prüfbarer
Sicherheitsanforderungen in drei Stufen: Stufe 1 für jede Software, Stufe 2 für Anwendungen, die sensible Daten
verarbeiten, Stufe 3 für die kritischsten. (OWASP hat inzwischen ASVS 5.0 veröffentlicht; diese Seite verwendet 4.0.3,
weil die meisten aktuellen Fragebögen danach fragen.)

### Wie Exponential ihn erfüllt

Es wurde keine Bewertung Anforderung für Anforderung durchgeführt. Kontrollen, die sich ASVS-Kapiteln zuordnen lassen
und im Code vorhanden sind:

| ASVS-Kapitel | In Exponential vorhanden |
|---|---|
| V2 Authentication (Authentifizierung) | Speicherung mit `password_hash()` (V2.4), Mindestlänge 10 (V2.1.1 verlangt 12), optionale TOTP-2FA (V2.8) |
| V3 Session management (Sitzungsverwaltung) | `HttpOnly`, `SameSite=Lax`, `Secure` über HTTPS (V3.4); neue Sitzungs-ID bei der Anmeldung (V3.2.1); Sitzungen bei einer Passwortänderung beendet (V3.3.3) |
| V4 Access control (Zugriffskontrolle) | standardmäßig verweigert über Richtlinien; Modulansichten brauchen eine Richtlinie, sofern sie nicht in `[RoleSettings] PolicyOmitList[]` aufgeführt sind |
| V5 Validation, sanitization and encoding (Validierung, Bereinigung und Kodierung) | Operator `wash`; Bild-Uploads anhand des Inhalts geprüft (`expImageUploadValidator`, `image.ini [ImageUploadSettings] AllowedMIMETypes[]`); SVG-Bereinigung |
| V7 Error handling and logging (Fehlerbehandlung und Protokollierung) | Prüfprotokoll ohne Passwörter oder Tokens; der Log-Viewer maskiert E-Mail- und IP-Adressen sowie Geheimnisse |
| V12 Files and resources (Dateien und Ressourcen) | keine PHP-Ausführung unterhalb des Document Root durch die mitgelieferten Rewrite-Regeln; Downloads über `content/download` |
| V13 API | REST-Tokens aus `random_bytes()` |
| V14 Configuration (Konfiguration) | Sicherheitsheader, CSP, eingeschränkte Debug-Ausgabe, Dateimanifest |

### Status

**Nicht geprüft.** Die standardmäßige Mindestlänge für Passwörter (10) liegt unter ASVS 4.0.3 V2.1.1 (12), sodass
Stufe 1 ohne Änderung von `MinPasswordLength` nicht erreicht wird. Die Stufen 2 und 3 wurden nicht bewertet.

## Content Security Policy Level 3

### Was sie ist

Content Security Policy Level 3 ist ein W3C Working Draft (Web Application Security Working Group), mit dem eine
Website dem Browser mitteilt, welche Quellen für Skripte, Styles, Bilder und Verbindungen eine Seite verwenden darf,
mit Nonces, Hashes, `strict-dynamic` und der Reporting API.

### Wie Exponential sie erfüllt

- Die Richtlinie wird von `kernel/private/classes/expcontentsecuritypolicy.php` aus `settings/site.ini
  [ContentSecurityPolicy]` gebaut: `Mode` (`disabled`, `report-only`, `enforce`, `auto`), `Directives[]` (Standard
  `script-src 'self'`, `style-src 'self'`, `object-src 'none'`, `base-uri 'self'`, `form-action 'self'`,
  `frame-ancestors 'self'`).
- **Nonces**: `Nonce=enabled` und `NonceDirectives[]=script-src` fügen pro Anfrage eine Nonce aus 16 Zufallsbytes
  hinzu; Templates lesen sie mit dem Operator `csp_nonce()` (`kernel/common/expcontentsecuritypolicyoperator.php`).
  Die Nonce wird nicht hinzugefügt, wo eine Direktive `'unsafe-inline'` erlaubt.
- **Berichte**: Die Ansicht `csp/report` nimmt `application/csp-report` und `application/reports+json` an und schreibt
  pro Verstoß eine Zeile nach `var/log/csp.log`, die der Log-Viewer auflistet. `ReportEndpoint=enabled` richtet
  `report-uri` und `Reporting-Endpoints` darauf aus; `ReportURI` benennt eine externe Sammelstelle.
- **Templates ohne Inline-Code**: `tests/tests/design/AdminContentSecurityPolicyTest.php` schlägt fehl bei Inline-
  `<script>`, Event-Handler-Attributen, `javascript:`-URLs und `style=""` in den Templates von admin4 und admin4l;
  `tests/tests/design/AdminStylingRulesTest.php` verbietet Inline-CSS in jedem Admin-Design.
- Einzelheiten: [Content Security Policy](../funktionen/6.0/content-security-policy.md).

### Status

**Teilweise erfüllt.** Der Mechanismus folgt CSP Level 3. In der ausgelieferten Form sendet `Mode=auto` auf
Administrations-Siteaccesses eine **nur berichtende** Richtlinie und auf öffentlichen Websites **keine Richtlinie**;
die in `ReportOnlyDesigns[]` aufgeführten klassischen Designs bleiben auch bei Durchsetzung nur berichtend.

### Bekannte Lücken

- Die Durchsetzung ist eine Entscheidung des Betreibers pro Siteaccess.
- Öffentliche Designs und Richtext-Ausgabe (`style=""` aus Inhalten, eingebettete Player) werden vom Test nicht
  geprüft.
- `strict-dynamic` und Hash-Quellen sind standardmäßig nicht konfiguriert.

## OWASP Cheat Sheets, die sich im Code wiederfinden

| Cheat Sheet | Was Exponential tut | Wo |
|---|---|---|
| Cross-Site Request Forgery Prevention | Synchronizer-Token pro Sitzung in jedem Formular, bei jedem POST geprüft; abgewiesene Tokens erhalten die Seite „Formular abgelaufen“; `SameSite=Lax` als zusätzliche Verteidigungslinie | `extension/ezformtoken`, aktiviert vom Setup-Assistenten (`kernel/setup/steps/ezstep_create_sites.php`); [Seite „Formular abgelaufen“](../funktionen/6.0/seite-abgelaufenes-formular.md) |
| Password Storage | `password_hash()` mit dem Standardalgorithmus von PHP (bcrypt), Prüfung mit `password_verify()`, Neu-Hashing bei der Anmeldung | `kernel/classes/datatypes/ezuser/ezuser.php` (`PASSWORD_HASH_PHP_DEFAULT`), `[UserSettings] HashType`, `UpdateHash` |
| Session Management | `HttpOnly` (Standard true), `SameSite` (Standard `Lax`), `Secure` (`auto`), neue ID bei der Anmeldung, Leerlauf-Timeout | `settings/site.ini [Session]` (`CookieHttponly`, `CookieSameSite`, `CookieSecure`, `SessionTimeout`, `ActivityTimeout`), `lib/ezsession/classes/ezsession.php` |
| Cross Site Scripting Prevention | Kontextabhängige Maskierung mit `wash` (Typen `xhtml`, `email`, `javascript`), Richtext als XML gespeichert und über Templates ausgegeben, CSP | `lib/eztemplate/classes/eztemplatestringoperator.php`, [Content Security Policy](../funktionen/6.0/content-security-policy.md) |
| SQL Injection Prevention | Maskierung und Ganzzahl-Umwandlungen, persistente Objekte statt handgeschriebenem SQL | `lib/ezdb/classes/`, `eZPersistentObject`; [Härtung von 6.0.13](../spezifikationen/6.0/sicherheitshaertung-6.0.13.md) |
| File Upload | Typ mit fileinfo aus dem Dateiinhalt gelesen, Positivliste von MIME-Typen, SVG bereinigt, keine Ausführung unterhalb des Root | `image.ini [ImageUploadSettings]`, [SVG-Bilder](../bc/6.0/svg-bilder.md) |
| HTTP Security Response Headers | `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Content-Security-Policy: frame-ancestors 'self'`, `Referrer-Policy: strict-origin-when-cross-origin`, `Permissions-Policy: camera=(), microphone=(), payment=(), usb=()` | `settings/site.ini [HTTPHeaderSettings] SecurityHeaders[]`, `settings/velocity.ini [ServerSettings] ResponseHeaders[]` |
| Clickjacking Defense | `X-Frame-Options: SAMEORIGIN` und `frame-ancestors 'self'` auf jeder Seite, einschließlich der Administrationsoberfläche | wie oben |
| Authentication | einheitliche Fehlerantworten, MFA verfügbar, optionale Sperre mit `TrustedIPList[]` | `[UserSettings]`, `sevenx_authentication_2fa` |
| Logging | Sicherheitsereignisse werden festgehalten, Geheimnisse nie geschrieben, Adressen gekürzt | [Prüfprotokoll](../funktionen/6.0/pruefprotokoll.md), [Log-Viewer](../funktionen/6.0/log-betrachter.md) |
| Unvalidated Redirects and Forwards | externe Ziele abgewiesen, sofern nicht erlaubt | `eZRedirectManager`, [Sichere Weiterleitungen](../funktionen/6.0/sichere-weiterleitungen.md) |

## Offenlegung von Schwachstellen

`SECURITY.md` im Wurzelverzeichnis des Repositorys nennt die unterstützten Versionen (die Linie 6.0.x, behoben auf
`main` und als nächster Tag veröffentlicht) und bittet um private Meldungen per E-Mail, nicht im öffentlichen
Issue-Tracker; siehe auch [Eine Schwachstelle melden](../hilfe/sicherheitshaertung.md#eine-schwachstelle-melden).
Veröffentlichte Release-Tags werden nie geändert: Eine Korrektur kommt als nächste Version.

**Lücke:** Es wird keine `/.well-known/security.txt` (RFC 9116) mitgeliefert; ein Betreiber kann eine hinzufügen.

## Tests und CI

- `phpunit.xml`-Testsuite `security` (`tests/tests/kernel/classes/security/`: `eZSecurityHardeningTest.php`,
  `eZRSSSecurityTest.php`, `expFileModeLimitsTest.php`, `expIniPhpWrapperTest.php`); der PHPUnit-Workflow schlägt
  fehl, wenn die Suite in `phpunit.xml` fehlt.
- Tests des Prüfprotokolls in `tests/tests/kernel/classes/audit/`.
- CSP- und Maskierungstests in `tests/tests/design/`.
- PHPStan mit einer Baseline (`.github/workflows/quality.yml`).

## Verwandte Seiten

- [Barrierefreiheitsstandards](barrierefreiheit.md), [Datenschutzstandards](datenschutz.md), [Standards der Webplattform](webplattform.md)
- [Sicherheitshärtung](../hilfe/sicherheitshaertung.md)
- [Leitfaden zu Sicherheit und Audit](../anleitungen/sicherheit-und-audit.md)
- [Velocity: HTTP/2 und Sicherheit](../spezifikationen/6.0/velocity-http2-und-sicherheit.md)
