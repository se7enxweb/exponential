---
title: Grundlagen zu Spam und Sicherheit
audience: individual, customer, website-creator
category: personal-sites
order: 1645
summary: Die Grundlagen zu Sicherheit und Spamschutz, die eine Person auf einer Exponential-Website aufrechterhalten kann - das Administratorkonto, Passwörter und zweistufige Anmeldung, Formular-Token und CAPTCHA für Formulare, Kommentare und Registrierung, was anonyme Besucher erstellen dürfen, Updates, Protokolle und was zu tun ist, wenn etwas falsch aussieht.
keywords: Sicherheit, Spam, Bots, CAPTCHA, reCAPTCHA, hCaptcha, ezformtoken, Formular-Token, Registrierungsspam, Kommentarspam, Passwörter, Zwei-Faktor, Administrator, Berechtigungen, Updates, Protokolle, Härtung, Privatperson, security, spam, form token, two-factor, permissions, logs, hardening
type: help
---
# Grundlagen zu Spam und Sicherheit

Diese Seite ist für Privatpersonen, die eine Website allein betreiben und die Grundlagen richtig machen möchten,
ohne Sicherheitsexperten zu werden. Die meisten Angriffe auf kleine Websites sind automatisiert: Bots, die gängige
Passwörter ausprobieren, Formulare ausfüllen und falsche Konten registrieren. Die folgenden Schutzmaßnahmen stoppen
die meisten davon.

## Inhalt

1. [Ihr Administratorkonto](#ihr-administratorkonto)
2. [Was Besucher erstellen dürfen](#was-besucher-erstellen-dürfen)
3. [Formulare, Kommentare und Registrierung](#formulare-kommentare-und-registrierung)
4. [Updates](#updates)
5. [Die Website im Blick behalten](#die-website-im-blick-behalten)
6. [Härtung, kurz gefasst](#härtung-kurz-gefasst)
7. [Wenn etwas falsch aussieht](#wenn-etwas-falsch-aussieht)
8. [Checkliste](#checkliste)

## Ihr Administratorkonto

- **Ändern Sie das Standardpasswort des Administrators** sofort nach der Installation
  ([Das Administratorpasswort ändern](../../administratorpasswort-aendern.md),
  [Standard-Administratorpasswort](../../standard-administratorpasswort.md)).
- **Ein langes, einzigartiges Passwort** aus einem Passwortmanager.
- **Zweistufige Anmeldung** mit einer App ([Zwei-Faktor-Anmeldung](zwei-faktor-anmeldung.md)).
- **Ein eigenes Redakteurskonto** für das tägliche Schreiben, damit das Administratorkonto nur für die Verwaltung
  genutzt wird.
- **HTTPS** auf jeder Seite ([Domainnamen und HTTPS](../setup/domainnamen-und-https.md)).

## Was Besucher erstellen dürfen

Jedes Formular, das ein Besucher absenden kann, ist ein Einfallstor für Spam. Prüfen Sie **Benutzer > Rollen** >
*Anonymous*: Im Standard-Seitenpaket dürfen anonyme Besucher in der Sektion Standard Objekte der Klassen *Comment* und
*Review* erstellen (`sevenx-multi-site-installer.php`). Entfernen Sie, was Sie nicht nutzen
([Besucherberechtigungen](../../ersteller/website-ersteller/rechte-fuer-besucher.md),
[Kommentare in Ihrem Blog](../blogs/kommentare-in-ihrem-blog.md)).

## Formulare, Kommentare und Registrierung

| Ebene | Wird geliefert als | Was sie stoppt |
|---|---|---|
| Formular-Token | `ezformtoken`, in der Standardinstallation | Gefälschte Einsendungen von anderen Websites |
| CAPTCHA-Feld | Datentyp `recaptcha` oder `hcaptcha` an der Klasse; Schlüssel vom Dienst | Den meisten automatisierten Formularspam |
| Nur angemeldete | Rollen | Anonyme Bots |
| Freigabe-Workflow | Workflows | Alles, was erscheint, bevor Sie nachsehen |
| Aktivierung per E-Mail | `VerifyUserType=email` | Falsche Registrierungen mit erfundenen Adressen |

Verwenden Sie CAPTCHA bei öffentlichen Formularen, Kommentaren und der Registrierung
([Spamschutz für Formulare](../../ersteller/website-ersteller/spamschutz-fuer-formulare.md),
[hcaptcha](../../../funktionen/6.0/erweiterungen/hcaptcha.md), [recaptcha](../../../funktionen/6.0/erweiterungen/recaptcha.md)).
Ein CAPTCHA schickt die Browser der Besucher zum Dienst; erwähnen Sie das in Ihrem Datenschutztext.

## Updates

Die meisten Einbrüche nutzen Lücken, die längst behoben waren. Spielen Sie Sicherheitsversionen innerhalb weniger
Tage ein ([Updates für eine Person](updates-fuer-eine-person.md)) und halten Sie PHP und, auf Ihrem eigenen Server,
das Betriebssystem aktuell.

## Die Website im Blick behalten

| Ansehen | Wie oft | Wo |
|---|---|---|
| Neue Benutzerkonten | Wöchentlich | **Benutzer**; entfernen Sie Konten, die Sie nicht kennen |
| Neue Kommentare und Formulareinsendungen | Wöchentlich | Die Beiträge; gesammelte Informationen |
| Fehler | Monatlich oder wenn etwas seltsam ist | Die Protokollansichten ([Protokolle für Inhaber](../../kunden/gesund-halten/logs-fuer-betreiber.md)) |
| Wer sich angemeldet hat | Im Zweifel | Das Audit-Protokoll ([Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md)) |

## Härtung, kurz gefasst

Das Installationsbuch hat ein Kapitel zur Sicherheitshärtung
([Sicherheitshärtung](../../sicherheitshaertung.md), [Kapitel 13](../../../installation/13-sicherheitshaertung.md)). Für eine
einzelne Person sind das Wesentliche: die Administration unter einer eigenen Adresse, Dateiberechtigungen wie im Buch
beschrieben, Einstellungsdateien nicht aus dem Web lesbar und Debug-Ausgabe auf der Live-Website ausgeschaltet.

## Wenn etwas falsch aussieht

1. Schalten Sie den Wartungsmodus ein, wenn jemand anderes Inhalte geändert hat
   ([Wartungsmodus](../../ersteller/website-ersteller/wartungsmodus.md)).
2. Ändern Sie Ihr Passwort und prüfen Sie die Administratorkonten.
3. Sehen Sie im Audit-Protokoll und in den Protokollen nach, was wann passiert ist.
4. Stellen Sie bei Bedarf aus einer Sicherung wieder her, die vor dem Problem angelegt wurde
   ([Sicherungen für eine Person](sicherungen-fuer-eine-person.md)).
5. Wenn Sie ein Sicherheitsproblem in Exponential selbst gefunden haben, melden Sie es vertraulich, nicht in einem
   öffentlichen Issue ([Vertrauliche Sicherheitsmeldungen](../../kunden/gesund-halten/vertrauliche-sicherheitsmeldungen.md)).

## Checkliste

- [ ] Standardpasswort geändert; zweistufige Anmeldung eingeschaltet.
- [ ] Die Rolle *Anonymous* erstellt nur, was Sie möchten.
- [ ] Formular-Token aktiv; CAPTCHA bei öffentlichen Formularen, Kommentaren und der Registrierung.
- [ ] Sicherheitsupdates zügig eingespielt.
- [ ] Wöchentlicher Blick auf neue Konten und Kommentare.
- [ ] Debug-Ausgabe auf der Live-Website ausgeschaltet.

## Verwandte Seiten

- [Allein betreiben](README.md)
- [Sicherheitsgrundlagen (Kunden)](../../kunden/gesund-halten/sicherheitsgrundlagen.md)
- [Sicherheitscheckliste für Inhaber](../../kunden/gesund-halten/sicherheits-checkliste-fuer-betreiber.md)
- [Sicherheitsgrundlagen für Unternehmen](../../unternehmen/sicherheit/sicherheitsgrundlagen-fuer-unternehmen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
