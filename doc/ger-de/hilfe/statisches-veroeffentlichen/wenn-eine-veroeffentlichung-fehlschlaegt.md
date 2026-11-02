---
title: "Wenn eine Veröffentlichung fehlschlägt"
audience: admin, website-creator, hosting, developer
category: administration
order: 24
summary: Fehlersuche nach Anzeichen - ein Build, der stoppt, eine abgelehnte Seite, ein abgewiesener oder wartender Push, ein 404 auf github.io, fehlende Stylesheets unter /<repository>/, eine eigene Domain noch ohne HTTPS, ein zu großer Build und eine fehlende Seite.
keywords: Statisches Veröffentlichen, Fehlersuche, Veröffentlichung fehlgeschlagen, Build fehlgeschlagen, Push abgewiesen, wartender Push, fehlende Stylesheets, Basispfad, Zertifikat, zu groß, Seite fehlt, static publishing, troubleshooting, publish failed, build failed, push refused, pending push, 404, github.io, CSS missing, base path, custom domain, HTTPS, size limit, session cookie, debug output, GitHub Pages
type: help
---
# Wenn eine Veröffentlichung fehlschlägt

## Was Sie am Ende haben

Die Ursache des Fehlers und die Lösung, oder die richtige Stelle zum Weitersuchen.

Beginnen Sie mit der Seite des Jobs (Setup > Statisches Veröffentlichen > Verlauf, dann der Lauf): Sie zeigt den
Fortschritt und den Bericht mit dem Grund. Auf der Konsole zeigt `./console exp:static:status <ziel>` die letzten Läufe.

## Der Build stoppt

| Meldung | Ursache und Lösung |
|---|---|
| Statisches Veröffentlichen ist ausgeschaltet | Auf dem Dashboard einschalten (oder `[StaticPublishSettings] Enabled=enabled`) |
| Keine Seite wurde exportiert | Der Server konnte seine eigene Website nicht abrufen. **Abgerufen von** (`CrawlURL`) in Schritt 7 auf eine Adresse setzen, die der Server erreicht, etwa `http://127.0.0.1/` |
| Eine Seite wird abgelehnt: die Antwort setzte das Sitzungscookie | Etwas auf der Seite startet eine Sitzung für nicht angemeldete Besucher (ein Formular, ein Warenkorb). Die Seite auslassen oder ändern |
| Eine Seite wird abgelehnt: Debug-Ausgabe oder Formular-Token | Die Debug-Ausgabe ist für die Adresse des Builds an, oder ein Formular zeigt anonymen Besuchern ein Token. Debug-Ausgabe für diese Adresse ausschalten; das Formular auslassen oder **Formulare** auf Ausblenden stellen |
| Ein anderer Job dieses Ziels läuft | Je Ziel läuft nur ein Build, eine Veröffentlichung oder Rücknahme zur Zeit. Warten |

## Der Push schlägt fehl

| Meldung | Ursache und Lösung |
|---|---|
| Push-Zustand **pending** | Commit und Tag bleiben; der Cronjob oder `exp:static:push` versucht es erneut. Die Ursache unten beheben, dann auf den Cronjob warten oder `./console exp:static:push <ziel>` ausführen |
| Authentication failed, 403 | Das Token ist abgelaufen, hat kein **Contents: Read and write** oder darf dieses Repository nicht. Ein neues anlegen und unter Setup > Geheimnisse ersetzen ([Mit GitHub verbinden](mit-github-verbinden-token-key-oder-app.md)) |
| Permission denied (publickey) | Der Deploy-Key ist beim Repository nicht eingetragen oder hat kein Schreibrecht |
| Rejected, non-fast-forward | Jemand anderes hat in den Branch gepusht. Statisches Veröffentlichen erzwingt nie einen Push: den Branch allein der Website überlassen, oder die andere Änderung in den Ordner des Ziels holen |
| Über der Größengrenze | Der Build ist größer als 1 GB oder hat eine Datei über 100 MB. Siehe [Bilder, Dateien und Downloads](bilder-dateien-und-downloads.md) |

## Die Website erscheint nicht

| Anzeichen | Ursache und Lösung |
|---|---|
| 404 auf `github.io` nach der ersten Veröffentlichung | GitHub Pages ist für den Branch noch nicht eingeschaltet. Assistent Schritt 4, **Auf GitHub einrichten** (erneut, jetzt wo der Branch existiert), oder **Settings > Pages** des Repositorys |
| Seiten ohne Stile unter `/<repository>/` | Ein falscher Basispfad. Relative Links verwenden ([Links und Basispfad](links-und-basispfad.md)) |
| Die alte Fassung erscheint noch | GitHub Pages braucht ein, zwei Minuten; der Kasten **Auf GitHub** im Verlauf zeigt den Stand seines Builds. Ohne Browser-Cache neu laden |
| Ihre Domain ist „nicht sicher“ | GitHub stellt das Zertifikat aus, wenn das DNS stimmt; das kann bis zu einem Tag dauern ([Eigene Domain verwenden](eigene-domain-verwenden.md)) |
| Eine Seite fehlt | Sie ist ausgelassen, jenseits der Crawl-Tiefe, nur mit Query-String erreichbar oder für Anonymous nicht lesbar. Der Bericht des Builds listet übersprungene Seiten |

## Immer noch nicht gelöst

Lesen Sie das Log des Jobs, führen Sie `./console exp:static:build <ziel> --dry-run` aus, um den Build zu sehen, ohne
etwas zu ändern, und lesen Sie die [FAQ](faq.md) und die Anleitung
[Statische Websites auf GitHub Pages hosten](../../anleitungen/statische-websites-auf-github-pages-hosten.md).

## Nächster Schritt

[Verlauf und Rücknahme](verlauf-und-ruecknahme.md): Zur letzten guten Veröffentlichung zurückkehren, während Sie den
Fehler beheben.
