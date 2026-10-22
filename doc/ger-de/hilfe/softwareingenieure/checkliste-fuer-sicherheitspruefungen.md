---
title: Checkliste für Sicherheitsprüfungen
audience: software-engineer, developer
category: software-engineering
order: 730
summary: Eine Checkliste, um eine Änderung an Exponential oder an einer Erweiterung auf Sicherheit zu prüfen - Zugriffskontrolle, Ein- und Ausgabe, SQL und Shell, CSRF, Dateien und Uploads, Weiterleitungen, Geheimnisse, Protokollierung und Datenschutz, HTTP-Header und CSP, langlebige Worker und Abhängigkeiten -, jeder Punkt mit der Regel, die er prüft, und wo diese Regel steht.
keywords: Sicherheitsprüfung, Checkliste, Code-Review, Zugriffskontrolle, XSS, SQL-Injection, CSRF, Upload, Weiterleitung, Geheimnisse, Datenschutz, CSP, Abhängigkeiten, OWASP, Softwareingenieur, security review, checklist, code review, access control, redirect, secrets, privacy, dependencies
type: help
---
# Checkliste für Sicherheitsprüfungen

Diese Seite ist eine Checkliste für den Sicherheitsteil eines Code-Reviews. Verwenden Sie sie zusammen mit der allgemeinen
[Checkliste für Code-Reviews](checkliste-fuer-code-reviews.md). Jeder Punkt nennt die Regel, die er prüft; den Hintergrund finden
Sie unter [Sicherheitstechnik im Überblick](sicherheitstechnik-im-ueberblick.md).

## Inhalt

1. [Zugriffskontrolle](#zugriffskontrolle)
2. [Ein- und Ausgabe](#ein--und-ausgabe)
3. [Datenbank und Shell](#datenbank-und-shell)
4. [Formulare, Weiterleitungen und Dateien](#formulare-weiterleitungen-und-dateien)
5. [Geheimnisse, Logs und Datenschutz](#geheimnisse-logs-und-datenschutz)
6. [HTTP-Schicht und Browser](#http-schicht-und-browser)
7. [Laufzeit und Abhängigkeiten](#laufzeit-und-abhängigkeiten)

## Zugriffskontrolle

- [ ] Jede neue View nennt ihre Richtlinienfunktionen in `functions` der `module.php`.
- [ ] Kein neuer Eintrag in `[RoleSettings] PolicyOmitList[]` oder `AnonymousAccessList[]` ohne eine in der Änderung
      festgehaltene Begründung.
- [ ] Über Zugriff entscheiden Richtlinien, nie der Vergleich einer Benutzer- oder Gruppen-ID.
- [ ] Fetches und Listen wenden die Leseberechtigung des Benutzers an (die Inhalts-Fetches tun das; eigenes SQL darf sie
      nicht umgehen).
- [ ] Eine neue Richtlinienfunktion erreicht bestehende Installationen über einen Installer oder ein Update-Skript.

Quelle: [Module, Views und Zugriffskontrolle](module-views-und-zugriffskontrolle.md).

## Ein- und Ausgabe

- [ ] Eingaben der Anfrage werden über `eZHTTPTool` gelesen, nicht über `$_GET` oder `$_POST`.
- [ ] Eingaben werden dort geprüft, wo sie hereinkommen (ein Datentyp in `validateObjectAttributeHTTPInput()`).
- [ ] Jede Variable in einem Template wird mit `|wash` ausgegeben oder über einen Operator, der maskiert.
- [ ] In PHP erzeugtes HTML (ein Operator, eine View) wird dort maskiert.
- [ ] Header-Werte enthalten kein CR oder LF aus Eingaben.

Quelle: [Kernel-Architektur](../../standards/architektur/kernel-architektur.md), [Härtung von Datentypen und Eingaben](../../spezifikationen/6.0/datentyp-eingabehaertung.md).

## Datenbank und Shell

- [ ] Jede Zeichenkette in SQL läuft über `$db->escapeString()`; jede Zahl wird mit `(int)` umgewandelt.
- [ ] Spalten für `ORDER BY` kommen aus einer Whitelist.
- [ ] Shell-Argumente laufen über `escapeshellarg()`; besser ist gar keine Shell.

Quelle: [Sicherheitshärtung von 6.0.13](../../bc/6.0/haertung.md), [Portables SQL schreiben](portables-sql-schreiben.md).

## Formulare, Weiterleitungen und Dateien

- [ ] Jede zustandsändernde Anfrage ist ein POST mit dem Formular-Token (`ezformtoken`); keine Zustandsänderung bei GET.
- [ ] Weiterleitungsziele laufen über den Weiterleitungsmanager und die erlaubten Hosts.
- [ ] Downloads laufen über `content/download`, das den Lesezugriff prüft, oder prüfen ihn selbst.
- [ ] Uploads werden auf Typ und Inhalt geprüft; SVG läuft durch die Bereinigung.
- [ ] Aus Eingaben gebaute Pfade können ihr Verzeichnis nicht verlassen.
- [ ] Ausgehende Anfragen an URLs aus Eingaben lehnen private, Loopback- und Link-Local-Adressen ab.

## Geheimnisse, Logs und Datenschutz

- [ ] Keine Zugangsdaten, Schlüssel oder hostspezifischen Werte in committeten Dateien; Geheimnisse liegen in
      `settings/override/` oder in der Umgebung.
- [ ] Kein Passwort, Token, Schlüssel oder keine Session-ID in einem Log, im Audit-Trail, auf einer Fehlerseite oder in
      einer Debug-Meldung.
- [ ] Neue Logzeilen folgen der Maskierung des Log-Betrachters und der Anfrage-Logs (Adressen, E-Mail, Abfragewerte).
- [ ] Sicherheitsrelevante Aktionen werden im Audit-Trail festgehalten ([Der Audit-Trail](../../funktionen/6.0/pruefprotokoll.md)).

## HTTP-Schicht und Browser

- [ ] Kein Inline-Skript ohne die CSP-Nonce; keine `on...=`-Attribute oder `javascript:`-URLs.
- [ ] Keine `<style>`-Blöcke oder `style=""`-Attribute in Administrations-Templates.
- [ ] Code, der die Client-Adresse oder das Schema liest, verwendet `eZSys` (das `TrustedProxies[]` beachtet), keine rohen
      Header.
- [ ] Antworten für angemeldete Benutzer werden für Proxys nicht cachebar gemacht.

Quelle: [HTTP-Sicherheit: Header, CSP, Anfrageregeln und Proxys](http-sicherheit-csp-und-proxys.md).

## Laufzeit und Abhängigkeiten

- [ ] Keine Geheimnisse oder Benutzerdaten einer Anfrage bleiben in Statics oder Globals eines langlebigen Workers zurück
      ([Nebenläufigkeit, Statics und langlebige Prozesse](nebenlaeufigkeit-statics-und-langlebige-prozesse.md)).
- [ ] Eine neue Composer-Abhängigkeit wird gepflegt, ist mit der GPL vereinbar lizenziert und auf eine veröffentlichte
      Version festgelegt ([Abhängigkeitsverwaltung mit Composer](abhaengigkeitsverwaltung-mit-composer.md)).
- [ ] Ein geänderter Standard, der die Sicherheit betrifft, ist mit dem Weg dokumentiert, ihn bewusst rückgängig zu machen
      ([Richtlinie zur Rückwärtskompatibilität](richtlinie-zur-rueckwaertskompatibilitaet.md)).
- [ ] Eine bei der Prüfung gefundene Schwachstelle wird vertraulich gemeldet, wie `SECURITY.md` es sagt, nicht im
      Pull-Request.

## Verwandte Seiten

- [Sicherheitstechnik im Überblick](sicherheitstechnik-im-ueberblick.md)
- [Checkliste für Sicherheitsprüfungen für Hoster](../hosting/checklisten/checkliste-fuer-die-sicherheitspruefung.md)
- [Die Arbeit anderer prüfen](../erweiterungsentwicklung/teil-5-erweitern-forken-zurueckgeben/die-arbeit-anderer-pruefen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
