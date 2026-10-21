---
title: CSRF und Formular-Tokens
audience: government, admin, developer
category: public-sector
order: 360
summary: Für Administratorinnen, Administratoren und Entwicklungsteams öffentlicher Websites - wie die Erweiterung ezformtoken jedes Formular gegen Cross-Site Request Forgery schützt, wie Sie prüfen, dass sie aktiv ist, was eigener AJAX-Code tun muss, und die Seite „Formular abgelaufen“.
keywords: CSRF, Cross-Site Request Forgery, Formular-Token, Formular abgelaufen, AJAX, öffentlicher Sektor, cross-site request forgery, form token, ezformtoken, SameSite, POST, form expired, ActiveExtensions, Cache-Control private, csp/report, public sector
type: help
---
# CSRF und Formular-Tokens

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Entwicklerinnen und Entwickler. Bei einer
Cross-Site Request Forgery bringt eine andere Website den Browser einer angemeldeten Redakteurin oder eines angemeldeten
Redakteurs dazu, ein Formular abzusenden: veröffentlichen, löschen, eine Rolle ändern. Die Erweiterung `ezformtoken`
von Exponential fügt jedem HTML-Formular ein Token je Sitzung hinzu und prüft es bei jedem POST; ein gefälschtes
Formular kann das Token nicht kennen
([13.8 Formular-Tokens gegen Cross-Site Request Forgery](../../../installation/13-sicherheitshaertung.md#138-formular-tokens-gegen-cross-site-request-forgery)).

## Inhalt

1. [Wie es funktioniert](#wie-es-funktioniert)
2. [Aktiv halten](#aktiv-halten)
3. [Eigener Code](#eigener-code)
4. [Wenn ein Token abgewiesen wird](#wenn-ein-token-abgewiesen-wird)
5. [Verwandte Seiten](#verwandte-seiten)

## Wie es funktioniert

- Der Einrichtungsassistent aktiviert `ezformtoken` in jeder neuen Installation.
- Event-Listener fügen das Token in der Ausgabe zu Formularen hinzu und prüfen es bei der Eingabe.
- Sitzungs-Cookies sind zusätzlich als `SameSite=Lax` gesetzt (gestaffelte Verteidigung).
- Eine Seite mit einem Token wird mit `Cache-Control: private` gesendet, sodass kein gemeinsam genutzter Cache sie
  speichert.
- Einige Ansichten müssen konstruktionsbedingt POST-Anfragen von Browsern ohne Token annehmen, etwa `csp/report` für
  Berichte der Content Security Policy mit ihren besonderen Inhaltstypen; die Ansicht schreibt nur eine Protokollzeile.

## Aktiv halten

Prüfen Sie, dass sie in `[ExtensionSettings] ActiveExtensions[]` steht:

```bash
php bin/php/console exp:ini where site.ini/ExtensionSettings/ActiveExtensions --allow-root-user
```

oder unter **Setup > Extensions**. Fehlt sie auf einer Live-Website, ist jedes Formular für Fälschungen offen; ergänzen
Sie sie im Override und leeren Sie die Caches.

## Eigener Code

- Eigener AJAX-Code, der POST-Anfragen sendet, muss das Token ebenfalls mitsenden; die Skripte des Kernels und
  ezjscore tun das. Die `README.rst` der Erweiterung zeigt, wie.
- Ein Reverse Proxy darf `Cache-Control: private` auf Seiten mit einem Token nicht überschreiben.
- Erweiterungen, die eigene Formulare hinzufügen, erhalten das Token automatisch, wenn sie normale HTML-Formulare über
  die Templates ausgeben.

## Wenn ein Token abgewiesen wird

Die Antwort ist 403 mit der Seite „Formular abgelaufen“; kein Token wird ins Protokoll geschrieben
([Seite „Formular abgelaufen“](../../../../eng-us/features/6.0/form-expired-page.md)). Häufige harmlose Ursachen: Die Sitzung ist
abgelaufen, während ein Formular offen war, oder der Benutzer hat sich in einem anderen Tab erneut angemeldet. Wiederholte
Abweisungen für viele Benutzer nach einer Änderung deuten auf ein Proxy- oder Caching-Problem hin.

## Verwandte Seiten

- [13.8 Formular-Tokens](../../../installation/13-sicherheitshaertung.md#138-formular-tokens-gegen-cross-site-request-forgery)
- [Spamschutz für Formulare](../../../../eng-us/help/creators/website-creators/spam-protection-for-forms.md)
- [Sicherheitsstandards: OWASP Cheat Sheets](../../../../eng-us/standards/security.md#owasp-cheat-sheets-reflected-in-the-code)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
