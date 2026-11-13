---
title: "Request Shield: Fehlersuche"
audience: admin, hosting, developer
category: administration
order: 70
summary: Symptome des Request Shields mit Ursache und Abhilfe - für diese Seite nicht gelaufen, Regeln nicht lesbar, alles geht durch, Dateien von root, Redakteure bei der Browserprüfung, 429 in der Administration, abgewiesene Seiten-Aliase, Testfehler mit www.example.org, ein leeres Diagramm, 403 auf /rs.
keywords: Request Shield, Fehlersuche, Probleme, lief für diese Seite nicht, Regeln können nicht gelesen werden, lässt durch, shield failed, Berechtigungen, Eigentümer, root, Browserprüfung, 429, Seiten-Alias, EXP-MODULES, EXP-SYSVIEW, EXP-ORIGIN, www.example.org, leeres Diagramm, log-level, /rs, SITE-DASH, PAUSED, trace, troubleshooting
type: help
---
# Request Shield: Fehlersuche

## Inhalt

1. [Erste Hilfe](#erste-hilfe)
2. [Symptome, Ursachen und Abhilfe](#symptome-ursachen-und-abhilfe)
3. [Herausfinden, warum eine Anfrage so entschieden wurde](#herausfinden-warum-eine-anfrage-so-entschieden-wurde)

## Erste Hilfe

Wenn Sie den Verdacht haben, dass der Shield echte Besucher abweist, und die Website **sofort** funktionieren muss:
Klicken Sie **Pausieren** auf der Übersicht, oder legen Sie auf dem Server die Datei `var/request-shield/PAUSED` an.
Der Shield entscheidet dann ab der nächsten Anfrage nichts mehr, auf jedem Server. Finden Sie die Ursache im
Protokoll, beheben Sie sie, dann **Einschalten**.

## Symptome, Ursachen und Abhilfe

| Symptom | Ursache | Abhilfe |
|---|---|---|
| Übersicht: „Der Shield lief für diese Seite nicht“ | der Hook steht nicht in `config.php`, oder Velocity wurde nach dem Hinzufügen nicht neu gestartet, oder Velocity verwendet ein `index.php` älter als 6.0.15 | den Hook hinzufügen ([der Leitfaden](../../anleitungen/request-shield.md#1-auf-einer-bestehenden-website-installieren)); `./console exp:velocity restart` |
| „Die Regeln können nicht gelesen werden“ | ein Fehler in einer Regeldatei | `bin/request-shield check <Hauptregeldatei>` nennt Datei und Zeile |
| Im Modus enforce geht alles durch; das Fehlerlog von PHP meldet, der Shield sei gescheitert und habe die Anfrage durchgelassen | der Shield lässt im Fehlerfall durch: ein Speicher- oder Cache-Verzeichnis, das er nicht beschreiben kann | `var/request-shield/` für den PHP-Benutzer der Website beschreibbar machen |
| Dateien in `var/request-shield/` gehören root, die Website kann sie nicht lesen | die Kommandozeile lief als root | `bin/request-shield` als Webbenutzer ausführen; den Eigentümer korrigieren |
| Eine Redakteurin oder ein Redakteur erhält in der Administration die Seite der Browserprüfung | die Administration verlangt die Browserprüfung einmal pro Pass | JavaScript und Cookies erlauben; für eine feste Büroadresse `exempt <Adresse>` in der Regeldatei |
| Hintergrundanfragen in der Administration scheitern mit 429 | eine Hintergrundanfrage ohne Pass erhält die Prüfung als JSON | die Seite lädt sich selbst neu; weitere Hintergrundpfade mit `api-path` nennen |
| Ein Seiten-Alias wird abgewiesen (404 durch `EXP-MODULES` oder `EXP-SYSVIEW`) | der Alias beginnt mit einem Modulnamen | den Alias als Ausnahme mit einem `expect`-Beispiel aufnehmen ([Beobachten und durchsetzen](beobachten-und-durchsetzen.md)) |
| `bin/request-shield test` lässt nach dem Eintragen Ihrer Hosts fünf Beispiele mit `Origin: https://www.example.org` scheitern (403 durch `EXP-ORIGIN`) | die mitgelieferten Beispiele verwenden die Dokumentationsdomain `www.example.org` | `host www.example.org` hinzufügen: eine reservierte Domain, nie die eines echten Besuchers |
| Das Diagramm der Übersicht ist leer | die Regeln nennen keine Protokolldatei, oder `log-level off` | `set log ...`; `set log-level flag` |
| `/rs/...` antwortet mit 403 | die vorgegebene Regel `SITE-DASH` sperrt die eigenen Seiten der Bibliothek für alle außer dem Server | sie für Ihre Adressen oder mit einem Token öffnen ([dashboard.md der Extension](https://github.com/se7enxweb/exp_request_shield/blob/main/doc/dashboard.md), auf Englisch) |
| Eine Modus-Schaltfläche ist deaktiviert | der Webserver kann die Regeldatei oder ihr Verzeichnis nicht beschreiben | beschreibbar machen, oder `set mode` auf dem Server ändern |
| Unter Velocity `X-RS: allow-uncached shield error` bei Abweisungen | eine Bibliothek mit geänderten inneren Schritten: die Extension ist auf den einfachen Aufruf zurückgefallen | die Extension aktualisieren |

## Herausfinden, warum eine Anfrage so entschieden wurde

Das Protokoll nennt die Regel. Um die Entscheidung Schritt für Schritt zu sehen, auf dem Server, als Webbenutzer:

```bash
php extension/exp_request_shield/bin/request-shield trace settings/request-shield/request-shield.rules "GET https://www.example.org/the/path"
```

Mit `set debug-header on` in der Regeldatei trägt jede Antwort einen Header `X-RS` mit der Entscheidung; schalten Sie
ihn danach wieder aus.
