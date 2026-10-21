---
title: Spamschutz und Drittanbieter
audience: government, website-creator, admin, project-manager
category: public-sector
order: 40
summary: Wie Sie Spam aus öffentlichen Formularen von Exponential heraushalten - Formular-Token, die Datentypen reCAPTCHA und hCaptcha und was es für eine Behörde bedeutet, Besucherdaten an deren Anbieter zu senden, und Maßnahmen, die keinen Drittanbieter brauchen.
keywords: Spam, CAPTCHA, Drittanbieter, Datenschutz, Datenübermittlung, Honeypot, Ratenbegrenzung, spam, recaptcha, hcaptcha, third party, privacy, data transfer, Content Security Policy, honeypot, VerifyUserType, rate limiting, request-shield
type: help
---
# Spamschutz und Drittanbieter

Diese Seite richtet sich an Site-Ersteller, Administratoren und Projektleiter, die öffentliche Formulare gegen Spam
schützen. Mit Exponential werden zwei CAPTCHA-Erweiterungen geliefert, und beide nutzen einen externen Anbieter. Für
eine Behörde ist das Senden von Besucherdaten an einen externen Anbieter eine Entscheidung des
Datenschutzbeauftragten, nicht nur eine technische Wahl. Diese Seite stellt die Möglichkeiten mit und ohne
Drittanbieter dar. Die allgemeine Anleitung ist
[Spamschutz für Formulare](../../../../../eng-us/help/creators/website-creators/spam-protection-for-forms.md).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Schutzschichten](#schutzschichten)
2. [Die CAPTCHA-Erweiterungen](#die-captcha-erweiterungen)
3. [Was ein CAPTCHA eines Drittanbieters bedeutet](#was-ein-captcha-eines-drittanbieters-bedeutet)
4. [Maßnahmen ohne Drittanbieter](#maßnahmen-ohne-drittanbieter)
5. [Auswählen](#auswählen)

## Schutzschichten

| Schicht | Stoppt | Drittanbieter |
|---|---|---|
| Formular-Token (`ezformtoken`) | gefälschte Sendungen von anderen Sites (CSRF); einfache Skripte, die senden, ohne das Formular zu laden | nein ([CSRF-Formular-Token](csrf-formular-token.md)) |
| CAPTCHA-Datentyp | automatisierte Einsendungen | ja |
| Moderation | was durchgekommen ist | nein |
| Filtern von Anfragen vor der Site | Fluten und bekannte schädliche Clients | nein, wenn Sie es selbst betreiben ([Sicherheitshärtung: request-shield](../../../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield)) |

## Die CAPTCHA-Erweiterungen

| | `recaptcha` | `hcaptcha` |
|---|---|---|
| Anbieter | Google reCAPTCHA (v2; ein Template für v3 für das Feedback-Formular) | hCaptcha |
| Zusätzliches Paket | `google/recaptcha` (Composer) | keines |
| Schlüssel in | `settings/override/recaptcha.ini.append.php` | `settings/override/hcaptcha.ini.append.php` |
| Lesen | [recaptcha](../../../../../eng-us/features/6.0/extensions/recaptcha.md) | [hcaptcha](../../../../../eng-us/features/6.0/extensions/hcaptcha.md) |

Beide fügen einen Datentyp hinzu: Fügen Sie das Attribut der Formularklasse hinzu (oder der Benutzerklasse für die
Registrierung), und das Formular verlangt die Aufgabe. Die Standardinstallation aktiviert beide Erweiterungen;
hCaptcha braucht gesetzte Schlüssel, bevor es funktioniert
([Erweiterungen, die es aktiviert](../../../seitenpakete/sevenx-multisite/erweiterungen-die-sevenx-multisite-aktiviert.md)). Halten Sie die
geheimen Schlüssel aus der Versionsverwaltung heraus.

## Was ein CAPTCHA eines Drittanbieters bedeutet

| Folge | Konsequenz |
|---|---|
| Das Widget lädt ein Skript des Anbieters im Browser des Besuchers | der Anbieter erhält die Adresse und Browserdaten des Besuchers |
| Der Anbieter kann Cookies oder Ähnliches setzen | Einwilligungsregeln können gelten (in der EU die [ePrivacy-Richtlinie](https://eur-lex.europa.eu/eli/dir/2002/58/oj)) |
| Der Anbieter wird Empfänger personenbezogener Daten | Ihre Datenschutzerklärung muss das sagen; prüfen Sie nach der [DSGVO](https://eur-lex.europa.eu/eli/reg/2016/679/oj) die Rechtsgrundlage und jede Übermittlung außerhalb der EU |
| Eine Content Security Policy muss den Host des Anbieters erlauben | ([Content Security Policy](../../../../../eng-us/features/6.0/content-security-policy.md)) |
| Barrierefreiheit | visuelle Aufgaben schließen Menschen aus; prüfen Sie den barrierefreien Modus des Anbieters ([Barrierefreie Formulare](barrierefreie-formulare.md#captcha-und-barrierefreiheit)) |
| Verfügbarkeit | ist der Anbieter nicht erreichbar, lassen sich Formulare möglicherweise nicht senden |

Exponential selbst lädt in der Administration keine Skripte von Drittanbietern und liefert kein Cookie-Banner mit
([Standards zum Datenschutz](../../../../../eng-us/standards/privacy.md)); ein CAPTCHA in öffentlichen Formularen ändert das für
diese Seiten.

## Maßnahmen ohne Drittanbieter

| Maßnahme | Wie |
|---|---|
| Formular-Token | voreingestellt eingeschaltet mit `ezformtoken`; setzen Sie Ihr eigenes `[HTMLForms] Secret` |
| Ein Honeypot-Feld | ein für Menschen verstecktes Feld (nicht nur mit `display:none`; außerhalb des Bildschirms mit einer Beschriftung „leer lassen“), das Bots ausfüllen; Ihr Template oder eine kleine Erweiterung lehnt Sendungen ab, in denen es gefüllt ist |
| E-Mail-Bestätigung für Konten | `site.ini [UserSettings] VerifyUserType=email` ([Registrierung von Mitgliedern](../../../../../eng-us/help/creators/website-creators/member-registration-and-areas.md)) |
| Nur Formulare für Angemeldete | wo die Zielgruppe ohnehin Konten hat |
| Begrenzen, was anonyme Besucher anlegen dürfen | die Rolle Anonymous ([Berechtigungen für Besucher](../../../../../eng-us/help/creators/website-creators/visitor-permissions.md)) |
| Ratenbegrenzungen und Filter davor | request-shield, Regeln im Webserver oder in Velocity |
| Spam in großer Zahl entfernen | **Setup > Gesammelte Informationen**, **Ausgewählte entfernen** |

Ein Honeypot braucht ein wenig Entwicklung; er wird nicht als Datentyp mitgeliefert.

## Auswählen

| Situation | Vorschlag |
|---|---|
| Ein Kontaktformular mit geringem Aufkommen | Token, Honeypot, Moderation |
| Öffentliche Registrierung von Konten | Token, E-Mail-Bestätigung; ein CAPTCHA nur, wenn der Missbrauch anhält |
| Ein angegriffenes Formular mit hohem Aufkommen | fügen Sie nach der Datenschutzprüfung ein CAPTCHA hinzu, mit einem alternativen Kanal |

## Verwandte Seiten

- [Formulare aufbauen](README.md)
- [CSRF-Formular-Token](csrf-formular-token.md)
- [Spamschutz für Formulare](../../../../../eng-us/help/creators/website-creators/spam-protection-for-forms.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
