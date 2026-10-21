---
title: CSRF-Formular-Token
audience: government, admin, website-creator, developer
category: public-sector
order: 50
summary: Wie Exponential jedes POST-Formular mit der Erweiterung ezformtoken gegen Cross-Site Request Forgery schützt, was Besucher sehen, wenn ein Token abgelehnt wird, das je Projekt zu setzende Geheimnis und was Sie in Ihren eigenen Formularen prüfen sollten.
keywords: CSRF, Cross-Site Request Forgery, Formular-Token, Formular abgelaufen, Sicherheit, cross-site request forgery, form token, ezformtoken, HTMLForms Secret, form expired, 403, warning.log, RefusalLogCollapseSeconds, security
type: help
---
# CSRF-Formular-Token

Diese Seite richtet sich an Administratoren, Site-Ersteller und Entwickler, die wissen müssen, wie öffentliche Formulare
und Formulare der Administration gegen gefälschte Einsendungen geschützt sind. Exponential fügt jedem POST-Formular ein
geheimes Token hinzu und lehnt Sendungen ohne es ab; das ist die Erweiterung `ezformtoken` im Kernel. Quellen:
[Die Seite „Formular abgelaufen“](../../../../../eng-us/features/6.0/form-expired-page.md),
[Spamschutz für Formulare](../../../../../eng-us/help/creators/website-creators/spam-protection-for-forms.md#form-tokens) und
[Standards zur Sicherheit](../../../../../eng-us/standards/security.md).

## Inhalt

1. [Wogegen das Token schützt](#wogegen-das-token-schützt)
2. [Was Besucher sehen, wenn ein Token abgelehnt wird](#was-besucher-sehen-wenn-ein-token-abgelehnt-wird)
3. [Einstellungen](#einstellungen)
4. [Prüfungen für Ihre eigenen Formulare](#prüfungen-für-ihre-eigenen-formulare)
5. [Fehlersuche](#fehlersuche)

## Wogegen das Token schützt

Cross-Site Request Forgery: Eine Seite auf einer anderen Site lässt den Browser eines Besuchers an Ihre Site senden und
nutzt dabei die Sitzung des Besuchers. Mit einem Token in jedem Formular wird eine Sendung, die nicht von Ihrer eigenen
Seite kam, abgelehnt. Es schützt:

- die Administration (jede Bearbeitung, Entfernung, Einstellung);
- Aktionen angemeldeter Besucher (Profiländerungen, Abonnements);
- öffentliche Formulare (Sendungen an den Information Collector).

Es ist eine Schicht der Maßnahmen zu A01 und A08 der [OWASP Top 10](https://owasp.org/Top10/), die in
[Standards zur Sicherheit](../../../../../eng-us/standards/security.md) aufgeführt sind. Es hält eine Person oder ein Skript, das
zuerst Ihr Formular lädt, nicht davon ab, Spam zu senden; das behandelt
[Spamschutz und Drittanbieter](spamschutz-und-drittanbieter.md).

## Was Besucher sehen, wenn ein Token abgelehnt wird

Seit dem 27. September 2026 erhält eine abgelehnte Sendung eine klare Seite statt eines Fehlers:

| Teil | Wert |
|---|---|
| Meldung | das Formular ist abgelaufen; nichts wurde gespeichert; laden Sie das Formular neu und senden Sie es erneut |
| Status | `403 Forbidden` (Kernel-Fehler 6, `error.ini`) |
| Header | `Cache-Control: no-store` und die konfigurierten Sicherheits-Header |
| XHR mit `Accept: application/json` | ein JSON-Body mit derselben Meldung |
| Protokoll | eine Zeile in `var/log/warning.log` mit der Prüfung (fehlend oder falsch), Methode, Pfad und Siteaccess, nie mit dem Token |

Die Seite ist `design:error/kernel/6.tpl` in `design/standard`; Ihr Design kann sie überschreiben. Ein lange offen
gelassenes Formular (eine lange Antwort in einem Beteiligungsverfahren) kann ablaufen; sagen Sie den Menschen, lange
Texte vor dem Senden zu kopieren, oder halten Sie Formulare kurz.

## Einstellungen

| Datei | Block | Schlüssel | Voreinstellung | Tun Sie |
|---|---|---|---|---|
| `settings/site.ini` | `HTMLForms` | `Secret` | `ThisTokenIsNotSoSecretChangeIt` | setzen Sie Ihren eigenen Wert in `settings/override/site.ini.append.php`; halten Sie ihn aus der Versionsverwaltung heraus |
| `settings/site.ini` | `HTMLForms` | `RefusalLogCollapseSeconds` | `60` | wiederholte Ablehnungen innerhalb des Zeitfensters werden gezählt, nicht geschrieben |
| `settings/error.ini` | `ErrorSettings-kernel` | `HTTPError[6]` | `403` | so lassen |

Lassen Sie die Erweiterung `ezformtoken` aktiv; der Installer richtet sie ein.

## Prüfungen für Ihre eigenen Formulare

- [ ] Formulare senden mit `method="post"`; das Token wird den POST-Formularen der Seitenausgabe hinzugefügt.
- [ ] Mit JavaScript gebaute Formulare senden das Token ebenfalls (lesen Sie es aus dem Formular oder den Metadaten
  der Seite, wie es die mitgelieferten Skripte tun).
- [ ] Keine zustandsändernde Aktion ist über GET erreichbar (ein Link, der etwas entfernt).
- [ ] Nach einem Deployment funktioniert ein vorher geöffnetes Formular noch oder zeigt die Seite „Formular
  abgelaufen“, keinen Fehler.

## Fehlersuche

| Symptom | Ursache |
|---|---|
| Nach einer Änderung wird jede Sendung abgelehnt | das Geheimnis hat sich geändert, während Formulare offen waren, oder ein Cache hat vielen Besuchern eine Seite mit einem alten Token geliefert; speichern Sie Seiten mit Formularen für angemeldete Benutzer nicht im Cache |
| Ablehnungen von einer Adresse in `warning.log` | jemand sendet, ohne das Formular zu laden; meist harmlos, möglicherweise ein beobachtenswerter Angriff |
| Ein eigenes Formular wird immer abgelehnt | es wird außerhalb der Seitenausgabe gebaut (ein iframe, eine statische HTML-Datei) und hat kein Token |

## Verwandte Seiten

- [Formulare aufbauen](README.md)
- [Sicherheitshärtung: CSRF](../../../sicherheitshaertung.md#cross-site-request-forgery-csrf)
- [Formulare, CSRF-Schutz und HTTP-Eingaben](../../../../../eng-us/help/creators/app-creators/forms-csrf-and-http-input.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
