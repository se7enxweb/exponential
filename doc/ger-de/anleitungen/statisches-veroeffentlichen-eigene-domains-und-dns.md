# Statisches Veröffentlichen: eigene Domains und DNS

Diese Anleitung ist für Administratoren, die eine statische Kopie unter ihrer eigenen Domain statt unter
`<owner>.github.io` ausliefern. Sie behandelt die Einstellungen des Ziels, die DNS-Einträge, die DNS-Prüfung auf der
Konsole und im Assistenten, und HTTPS.

[Anleitungen](README.md) · Verwandt: [Eigene Domain verwenden](../hilfe/statisches-veroeffentlichen/eigene-domain-verwenden.md),
[Links und Basispfad](../hilfe/statisches-veroeffentlichen/links-und-basispfad.md),
[Statisches Veröffentlichen](../funktionen/6.0/statisches-veroeffentlichen.md#github)

## Was Sie am Ende haben

Ein Ziel, das `CNAME` schreibt, Links an der Wurzel Ihrer Domain, DNS-Einträge, die auf GitHub Pages zeigen, und
erzwungenes HTTPS.

## 1. Das Ziel

```ini
[Target_github-public]
CustomDomain=www.example.com
BasePath=
PublicURL=https://www.example.com/
Links=relative
```

- `CustomDomain` wird in die Datei `CNAME` an der Wurzel des Builds geschrieben (sobald die Einstellung gesetzt ist).
  GitHub Pages liest sie bei jedem Push; ohne sie geht die Domain verloren.
- Mit eigener Domain wird die Site an der Wurzel ausgeliefert, `BasePath` bleibt also leer. Ein übrig gebliebener
  Basispfad einer Projekt-Site lässt Stylesheets und Bilder verschwinden.
- `PublicURL` nutzen die Sitemap, `robots.txt`, Canonical-Links und Social Cards.

Bauen und veröffentlichen Sie nach einer Änderung daran: Die Datei `CNAME` und die absoluten Links gehören zum Build.

## 2. Die Einträge

| Domain | Eintrag | Wert |
|---|---|---|
| Eine Subdomain, `www.example.com` | `CNAME` | `<owner>.github.io` (das Konto oder die Organisation, nie das Repository) |
| Die Apex-Domain, `example.com` | `A`, alle vier | `185.199.108.153`, `185.199.109.153`, `185.199.110.153`, `185.199.111.153` |
| Die Apex-Domain, optional | `AAAA` | `2606:50c0:8000::153`, `2606:50c0:8001::153`, `2606:50c0:8002::153`, `2606:50c0:8003::153` |

Nehmen Sie lieber die Subdomain: Sie braucht einen Eintrag und folgt GitHub, wenn sich dessen Adressen ändern. Setzen
Sie beide Namen, leitet GitHub den einen auf den anderen um. Entfernen Sie alte `A`- und `AAAA`-Einträge desselben
Namens.

## 3. Prüfen

```bash
./console exp:static:dns www.example.com
./console exp:static:dns example.com --owner=octo-org
./console exp:static:dns www.example.com --format=json
```

Die Prüfung schlägt A, AAAA und CNAME nach, vergleicht sie mit den Adressen von GitHub Pages und dem erwarteten
`<owner>.github.io` und listet auf, was zu setzen ist, wenn sie abweichen. Der Owner kommt vom Ziel, dessen
`CustomDomain` es ist, oder von `--owner`. In der Administration ist dieselbe Prüfung **DNS prüfen** in Schritt 6 des
Assistenten. DNS-Änderungen können so lange wie die TTL der Einträge brauchen, bis sie überall sichtbar sind.

## 4. GitHub und HTTPS

Mit einem Token oder einer GitHub App als Zugangsdaten:

```bash
./console exp:static:github setup github-public --plan    # was es tun würde
./console exp:static:github setup github-public           # setzt die Domain und erzwingt HTTPS
./console exp:static:github status github-public
```

GitHub stellt das Zertifikat aus, sobald es die Einträge sieht, was bis zu einem Tag dauern kann; bis dahin meldet
die Einrichtung HTTPS als geplant. Führen Sie sie später erneut aus. Ohne die API: im Repository **Settings > Pages >
Custom domain**, dann **Enforce HTTPS**.

Verifizieren Sie die Domain für Ihr Konto oder Ihre Organisation auf GitHub (**Settings > Pages > Verified domains**),
damit niemand anderes sie beanspruchen kann, falls das Repository einmal entfernt wird.

## Andere Hosts

`CNAME` ist eine Datei von GitHub Pages. Andere Hosts (GitLab Pages, ein Webserver, der den Branch ausliefert) setzen
die Domain in ihren eigenen Einstellungen; das Ziel braucht trotzdem einen leeren `BasePath` und die richtige
`PublicURL`. `CustomDomain` kann dort leer bleiben.

## Fehlerbehebung

| Symptom | Lösung |
|---|---|
| Die Domain antwortet mit 404 | `CNAME` fehlt im Branch: `CustomDomain` setzen, bauen und veröffentlichen |
| CSS und Bilder fehlen unter der Domain | `BasePath` leeren, bauen und veröffentlichen |
| Eine Zertifikatswarnung | GitHub hat es noch nicht ausgestellt; warten und die Einrichtung erneut ausführen |
| Die DNS-Prüfung nennt fremde A-Einträge | Die Einträge des alten Hosts entfernen |

## Nächster Schritt

[Statisches Veröffentlichen: Grenzen und GitHub-Bedingungen](statisches-veroeffentlichen-grenzen-und-github-bedingungen.md).
