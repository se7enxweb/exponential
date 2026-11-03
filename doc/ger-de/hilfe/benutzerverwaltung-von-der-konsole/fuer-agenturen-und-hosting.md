---
title: "Für Agenturen und Hosting"
audience: hosting, admin, developer
category: administration
order: 18
summary: Die Befehle exp:user über viele Installationen hinweg ausführen - geskriptetes Einrichten der Redakteure eines Kunden, dasselbe Ausscheiden überall, regelmäßige Kontenprüfungen je Website und die Regeln für Passwörter, Siteaccesses und personenbezogene Daten, wenn Sie für Kunden handeln.
keywords: Agentur, Hosting, viele Installationen, Kunden-Websites, geskriptetes Einrichten, Ausscheiden überall, Mitarbeiter geht, Kontenprüfung, Dienstkonto, personenbezogene Daten, Auftragsverarbeitung, agency, hosting, many installations, exp:user, --siteaccess, --as
type: help
---
# Für Agenturen und Hosting

Eine Agentur oder ein Hoster betreut oft Dutzende Installationen. Die Befehle `exp:user` verhalten sich auf jeder
gleich, deshalb dient ein Skript allen.

## Was Sie am Ende haben

Einrichten, Ausscheiden und Prüfungen, die auf jeder Installation, die Sie betreuen, gleich ablaufen.

## Geskriptetes Einrichten der Redakteure eines Kunden

Führen Sie je Kunde eine CSV-Datei mit den anzulegenden Redakteuren, und importieren Sie sie mit der Aktivierungsmail:

```bash
cd /var/www/client-a
./console exp:user:import var/tmp/client-a-editors.csv --group=Editors --send-activation --as=agency-bot --apply
```

Jede Person setzt ein Passwort über die Mail. Kein Passwort geht durch Ihre Hände oder Ihre Dateien.

## Wenn jemand aus Ihrem Team geht

Ihre eigenen Leute haben oft Konten auf vielen Kunden-Websites. Deaktivieren Sie sie überall in einer Schleife:

```bash
for site in /var/www/client-*; do
    (cd "$site" && ./console exp:user:disable jane@example.com --as=agency-bot --apply --format=json) \
        | jq -r '"\(.command) \(.exit) \(.errors | join("; "))"'
done
```

Eine Installation, auf der die Adresse nicht gefunden wird, meldet Exit-Code 1 und ändert nichts.

## Eine Prüfung je Installation

```bash
for site in /var/www/client-*; do
    echo "$site"
    (cd "$site" && ./console exp:user:hygiene --format=json) | jq -c '.counts'
done
```

Senden Sie jedem Kunden die Zahlen, und die Listen dahinter nur den Personen, die sie sehen dürfen.

## Regeln, wenn Sie für Kunden handeln

- Legen Sie je Installation ein Dienstkonto an, zum Beispiel `agency-bot`, und geben Sie `--as=agency-bot` an. Das
  Audit des Kunden zeigt dann, welche Änderungen von Ihnen kamen.
- Führen Sie jeden Befehl im Wurzelverzeichnis der Installation aus, und geben Sie `--siteaccess=` an, wenn Mails auf
  eine bestimmte Website verweisen sollen.
- Schreiben Sie nie ein Passwort in ein Skript oder eine Datei. Nehmen Sie `--send-activation`, `--send-link` oder
  `--password-stdin` aus Ihrem Geheimnisspeicher.
- **Datenschutz:** Exporte und Importdateien enthalten personenbezogene Daten des Kunden. Bewahren Sie sie nur so
  lange auf, wie die Aufgabe es braucht, und nur dort, wo Ihr Vertrag mit dem Kunden es erlaubt.

## Verwandte Seiten

- [Für Entwickler: Skripte und CI](fuer-entwickler-skripte-und-ci.md)
- [Mitglieder aus einer CSV-Datei importieren](mitglieder-aus-csv-importieren.md)
- [Den Zugang beim Ausscheiden schließen](den-zugang-beim-ausscheiden-schliessen.md)
