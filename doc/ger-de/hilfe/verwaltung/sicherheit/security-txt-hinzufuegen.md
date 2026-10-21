---
title: security.txt hinzufügen
audience: government, admin, hosting
category: public-sector
order: 260
summary: Für öffentliche Stellen - Exponential liefert keine security.txt mit; diese Seite zeigt, wie Sie eine nach RFC 9116 schreiben, wo sie hingehört, welche eine Rewrite-Regel die mitgelieferten Apache-Regeln brauchen, damit sie ausgeliefert wird, und wie Sie sie prüfen.
keywords: security.txt, Offenlegung von Schwachstellen, Sicherheitskontakt, Rewrite-Regel, öffentlicher Sektor, RFC 9116, .well-known, vulnerability disclosure, Contact, Expires, Policy, Preferred-Languages, Canonical, rewrite rule, htaccess, Velocity, nginx, public sector
type: help
---
# security.txt hinzufügen

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Hosting-Teams. `security.txt` ist eine kleine
Textdatei unter `/.well-known/security.txt`, die Sicherheitsforschenden mitteilt, wie sie eine Schwachstelle melden
können; definiert ist sie in RFC 9116 (<https://www.rfc-editor.org/rfc/rfc9116>). **Exponential liefert keine mit**;
das Projekt führt dies als Lücke, die der Betreiber schließen kann
([Sicherheitsstandards: Offenlegung von Schwachstellen](../../../../eng-us/standards/security.md#vulnerability-disclosure)).

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Die Datei schreiben](#die-datei-schreiben)
2. [Die Datei ablegen](#die-datei-ablegen)
3. [Den Webserver die Datei ausliefern lassen](#den-webserver-die-datei-ausliefern-lassen)
4. [Prüfen](#prüfen)
5. [Aktuell halten](#aktuell-halten)
6. [Verwandte Seiten](#verwandte-seiten)

## Die Datei schreiben

Eine minimale Datei mit den beiden Feldern, die RFC 9116 verlangt (`Contact` und `Expires`), und üblichen optionalen
Feldern:

```
Contact: mailto:security@example.gov
Expires: 2027-10-31T23:00:00.000Z
Policy: https://www.example.gov/vulnerability-disclosure
Preferred-Languages: en
Canonical: https://www.example.gov/.well-known/security.txt
```

- `Contact`: ein Postfach, das Ihr Sicherheitsteam liest (oder ein HTTPS-Formular). Nicht die Adresse des
  Exponential-Projekts: Diese ist für Schwachstellen in Exponential selbst gedacht.
- `Expires`: weniger als ein Jahr in der Zukunft, wie es der RFC empfiehlt.
- `Policy`: die Seite mit Ihrer Offenlegungsrichtlinie (siehe „Offenlegung von Schwachstellen“).
- Der RFC beschreibt auch das Signieren der Datei mit OpenPGP; folgen Sie der Praxis Ihrer Organisation.

## Die Datei ablegen

Legen Sie im Document-Root das Verzeichnis `.well-known` und darin die Datei an, im Besitz des Benutzers der Website und
für den Webserver lesbar:

```bash
mkdir -p .well-known
$EDITOR .well-known/security.txt
chmod 0644 .well-known/security.txt
```

Nehmen Sie die Datei in Ihre Bereitstellung (Versionsverwaltung) auf, damit sie das nächste Release übersteht.

## Den Webserver die Datei ausliefern lassen

**Apache mit den mitgelieferten Regeln.** `.htaccess_root` beantwortet jeden Punkt-Pfad außer `.well-known` mit 404,
aber seine letzte Regel schickt jede andere Anfrage an `index.php`, sodass `/.well-known/security.txt` bei Exponential
ankäme und eine Seite „nicht gefunden“ erhielte. Fügen Sie neben der vorhandenen Regel für `robots.txt`, vor dem
abschließenden `RewriteRule .* index.php`, eine Zeile hinzu:

```apache
RewriteRule ^\.well-known/security\.txt$ - [L]
```

Übernehmen Sie sie in Ihre `.htaccess` (oder den virtuellen Host) und in Ihre eigene Kopie der Regeln; wiederholen Sie
das nach Upgrades, die `.htaccess_root` ändern.

**nginx**: Die abgeleitete Konfiguration in der Installationsanleitung hält `.well-known` erreichbar; stellen Sie
sicher, dass eine `location` die Datei ausliefert, bevor die Anfrage an `index.php` weitergereicht wird
([Kapitel 8: Die Website ausliefern](../../../installation/08-die-website-ausliefern.md)).

**Velocity** liefert Dateien im Document-Root selbst aus; prüfen Sie es mit dem `curl` unten.

## Prüfen

```bash
curl -sI https://www.example.gov/.well-known/security.txt | head -5
curl -s  https://www.example.gov/.well-known/security.txt
```

Erwartet: Status 200, `Content-Type: text/plain` und Ihre Datei. Ein 404 oder eine HTML-Seite bedeutet, dass die
Anfrage bei `index.php` angekommen ist.

## Aktuell halten

Tragen Sie das `Expires`-Datum einen Monat im Voraus in Ihren Kalender ein, zusammen mit der Überprüfung der
Offenlegungsrichtlinie. Eine Datei nach ihrem Ablaufdatum sollen Forschende laut RFC als veraltet betrachten.

## Verwandte Seiten

- [Offenlegung von Schwachstellen](offenlegung-von-schwachstellen.md)
- [Eine brauchbare robots.txt ab Werk](../../../../eng-us/features/6.0/robots-txt.md) (wird genauso ausgeliefert)
- [Sicherheitsstandards](../../../../eng-us/standards/security.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
