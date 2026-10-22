---
title: Spamschutz für Ehrenamtsformulare
audience: non-profit, website-creator, admin, businesses
category: non-profits
order: 30
summary: Spam aus den Ehrenamts- und Kontaktformularen einer Non-Profit-Organisation in Exponential heraushalten - das standardmäßig aktive Formular-Token von ezformtoken, die Wahl zwischen den Datentypen hcaptcha und recaptcha, Schlüssel in Override-Dateien, die Folgen eines CAPTCHA für Datenschutz und Content Security Policy, seine barrierefreie Alternative, E-Mail-Bestätigung bei Registrierungen und das gesammelte Entfernen von Spam.
keywords: Spam, Spamschutz, CAPTCHA, hCaptcha, reCAPTCHA, hcaptcha, recaptcha, ezformtoken, Formular-Token, CSRF, Datenschutz, Content Security Policy, Barrierefreiheit, VerifyUserType, gesammeltes Entfernen, Ehrenamtsformular, form token, privacy, accessibility, bulk removal, volunteer form
type: help
---
# Spamschutz für Ehrenamtsformulare

Diese Seite richtet sich an die Website-Ersteller und an die Koordinatoren, die den Spam zu sehen bekommen. Ein
öffentliches Formular zieht innerhalb weniger Tage automatisierte Einsendungen an. Spam kostet die Koordinatoren Zeit
und verdeckt echte Bewerbungen; schützen Sie deshalb jedes öffentliche Formular vor dem Start. Die vollständige
Referenz ist [Spamschutz für Formulare](../../ersteller/website-ersteller/spamschutz-fuer-formulare.md).

## Inhalt

1. [Schicht 1: das Formular-Token](#schicht-1-das-formular-token)
2. [Schicht 2: ein CAPTCHA](#schicht-2-ein-captcha)
3. [hCaptcha oder reCAPTCHA wählen](#hcaptcha-oder-recaptcha-wählen)
4. [Datenschutz und Content Security Policy](#datenschutz-und-content-security-policy)
5. [Barrierefreiheit](#barrierefreiheit)
6. [Schicht 3: Registrierung und Aufräumen](#schicht-3-registrierung-und-aufräumen)
7. [Checkliste](#checkliste)

## Schicht 1: das Formular-Token

Die Erweiterung `ezformtoken` fügt jedem POST-Formular ein geheimes Token hinzu und weist Einsendungen ohne dieses
Token ab; das verhindert gefälschte, seitenübergreifende Einsendungen. Sie ist standardmäßig aktiv. Setzen Sie in
`settings/override/site.ini.append.php` ein eigenes `[HTMLForms] Secret`; der ausgelieferte Wert weist darauf hin,
dass er geändert werden muss. Ein Besucher, dessen Formular abgelaufen ist, sieht statt eines Fehlers eine
freundliche Seite ([Seite für abgelaufene Formulare](../../../funktionen/6.0/seite-abgelaufenes-formular.md)).

Das Token verhindert gefälschte Einsendungen, aber keine Roboter, die das Formular wie ein Mensch ausfüllen. Dafür
brauchen Sie Schicht 2.

## Schicht 2: ein CAPTCHA

Beide mitgelieferten CAPTCHA-Erweiterungen sind Datentypen. Sie fügen der Formularklasse ein CAPTCHA-Attribut hinzu,
und das Formular verlangt vom Besucher, die Aufgabe zu lösen, bevor es angenommen wird, und zwar beim Bearbeiten von
Inhalten, bei der Benutzerregistrierung und beim Sammeln von Informationen
([hcaptcha](../../../funktionen/6.0/erweiterungen/hcaptcha.md)).

Die Einrichtung ist für beide gleich:

1. Aktivieren Sie die Erweiterung und besorgen Sie sich Schlüssel beim Anbieter.
2. Tragen Sie sie in die Override-Datei ein:

   ```ini
   [Keys]
   PublicKey=<your site key>
   PrivateKey=<your secret key>
   [PublishSettings]
   NewObjectsOnly=false
   ```

3. Fügen Sie das CAPTCHA-Attribut der Klasse des Ehrenamtsformulars hinzu (und der Benutzerklasse, wenn sich
   Ehrenamtliche registrieren).
4. Leeren Sie die Caches und testen Sie das Formular im abgemeldeten Zustand.

Halten Sie den geheimen Schlüssel aus der Versionsverwaltung heraus. Quelle: [Spamschutz für Formulare](../../ersteller/website-ersteller/spamschutz-fuer-formulare.md#captcha-recaptcha-oder-hcaptcha).

## hCaptcha oder reCAPTCHA wählen

| | `hcaptcha` | `recaptcha` |
|---|---|---|
| Anbieter | hCaptcha | Google reCAPTCHA (v2; ein Template für v3 für das Feedback-Formular) |
| Zusätzliches Composer-Paket | Keines | `google/recaptcha` |
| Einstellungsdatei | `settings/override/hcaptcha.ini.append.php` | `settings/override/recaptcha.ini.append.php` |

Beide werden über `composer.json` installiert. Vergleichen Sie die Bedingungen und Datenschutzerklärungen der
Anbieter selbst; diese Hilfe bewertet sie nicht.

## Datenschutz und Content Security Policy

- Das CAPTCHA-Widget lädt ein Skript vom Anbieter, und der Anbieter erhält Daten über den Besucher. Erwähnen Sie das
  in Ihrer Datenschutzerklärung ([Captchas](../../verwaltung/datenschutz/captchas.md)).
- Wenn Ihre Website eine Content Security Policy sendet, erlauben Sie darin den Host des Anbieters
  ([Content Security Policy](../../../funktionen/6.0/content-security-policy.md)).

## Barrierefreiheit

Jedes CAPTCHA ist für manche Menschen ein Hindernis. Beide mitgelieferten CAPTCHA-Dienste bieten eine barrierefreie
Alternative ([Barrierefreiheit für Website-Ersteller, Formulare](../../ersteller/website-ersteller/barrierefreiheit-fuer-website-ersteller.md#formulare)).
Außerdem:

- Bieten Sie einen anderen Weg zur Bewerbung an: eine E-Mail-Adresse oder Telefonnummer neben dem Formular.
- Testen Sie das CAPTCHA vor dem Start mit der Tastatur und einem Screenreader
  ([Barrierefreie Formulare](../../verwaltung/barrierefreiheit/barrierefreie-formulare.md)).

## Schicht 3: Registrierung und Aufräumen

- Wenn Ehrenamtliche Konten registrieren, verlangen Sie eine E-Mail-Bestätigung
  (`site.ini [UserSettings] VerifyUserType=email`)
  ([Mitgliederregistrierung und Mitgliederbereiche](../../ersteller/website-ersteller/mitgliederregistrierung-und-mitgliederbereiche.md)).
- Beschränken Sie, was anonyme Besucher anlegen dürfen.
- Entfernen Sie Spam-Einsendungen gesammelt: unter **Setup > Gesammelte Informationen** markieren und
  **Ausgewählte entfernen**; das Formular bleibt bestehen ([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)).

| Formular | Schützen mit |
|---|---|
| Ehrenamts- und Kontaktformulare | Token (automatisch) und CAPTCHA |
| Benutzerregistrierung | Token und CAPTCHA an der Benutzerklasse |
| Formulare nur für angemeldete Ehrenamtliche | Das Token reicht meist aus |

## Checkliste

- [ ] Eigenes `[HTMLForms] Secret` gesetzt.
- [ ] CAPTCHA gewählt, Schlüssel in der Override-Datei, Attribut an der Klasse.
- [ ] Anbieter in der Datenschutzerklärung genannt; die CSP erlaubt seinen Host.
- [ ] Barrierefreie Alternative getestet; ein anderer Weg zur Bewerbung angeboten.
- [ ] E-Mail-Bestätigung für Registrierungen eingeschaltet.
- [ ] Die Koordinatoren wissen, wie sie Spam gesammelt entfernen.

## Verwandte Seiten

- [Ehrenamtliche](README.md)
- [Anmeldeformular für Ehrenamtliche, Schritt für Schritt](anmeldeformular-fuer-ehrenamtliche-schritt-fuer-schritt.md)
- [recaptcha](../../../funktionen/6.0/erweiterungen/recaptcha.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
