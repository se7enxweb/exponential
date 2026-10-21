---
title: Eine Datei security.txt hinzufügen
audience: government, admin, hosting
category: public-sector
order: 380
summary: Schritt für Schritt - /.well-known/security.txt (RFC 9116) für Ihre Site veröffentlichen, das Exponential nicht mitliefert, die eine Rewrite-Regel hinzufügen, die die mitgelieferten Apache-Regeln zum Ausliefern brauchen, und es mit curl prüfen.
keywords: security.txt, RFC 9116, .well-known, Meldung von Schwachstellen, Contact, Expires, Policy, Rewrite-Regel, htaccess, nginx, Velocity, öffentlicher Sektor, vulnerability disclosure, rewrite rule, public sector
type: help
---
# Eine Datei security.txt hinzufügen

Für Administratoren und Hosting-Teams. Eine `security.txt` sagt Forschenden, wo sie Schwachstellen Ihrer Site melden
können. Die Erklärung des Projekts führt sie als nicht mitgeliefert
([security.txt hinzufügen](../../../../../eng-us/help/government/security/adding-security-txt.md)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Ein Postfach für Sicherheitskontakte und idealerweise eine veröffentlichte Seite mit Ihrer Richtlinie zur Meldung
  von Schwachstellen (siehe „Meldung von Schwachstellen“).
- Zugriff auf das Document Root und die Konfiguration des Webservers.

## Schritte

1. Legen Sie `.well-known/security.txt` im Document Root an:

   ```
   Contact: mailto:security@example.gov
   Expires: 2027-10-31T23:00:00.000Z
   Policy: https://www.example.gov/vulnerability-disclosure
   Preferred-Languages: en
   Canonical: https://www.example.gov/.well-known/security.txt
   ```

   `Contact` und `Expires` verlangt RFC 9116 (<https://www.rfc-editor.org/rfc/rfc9116>); halten Sie `Expires` weniger
   als ein Jahr in der Zukunft.
2. **Apache mit `.htaccess_root`**: Punkt-Pfade außer `.well-known` antworten mit 404, und die letzte Regel schickt
   alles andere an `index.php`. Fügen Sie vor `RewriteRule .* index.php` hinzu:

   ```apache
   RewriteRule ^\.well-known/security\.txt$ - [L]
   ```

3. **nginx**: Stellen Sie sicher, dass eine `location` die Datei ausliefert, bevor Anfragen an `index.php`
   durchfallen.
4. Nehmen Sie die Datei (und die Regel) in Ihr Deployment auf, damit das nächste Release sie behält.

## Prüfen, ob es funktioniert hat

```bash
curl -sI https://www.example.gov/.well-known/security.txt | head -3
curl -s https://www.example.gov/.well-known/security.txt
```

Status 200, ein Typ für einfachen Text, Ihre Datei. Eine HTML-Seite bedeutet, dass die Anfrage bei `index.php`
angekommen ist. Tragen Sie das Datum aus `Expires` in Ihren Kalender ein.

## Verwandte Seiten

- [security.txt hinzufügen](../../../../../eng-us/help/government/security/adding-security-txt.md)
- [Meldung von Schwachstellen](../../../../../eng-us/help/government/security/vulnerability-disclosure.md)
- [Eine nützliche robots.txt ab Werk](../../../../../eng-us/features/6.0/robots-txt.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
