# A one-node DFS cluster in one command: exp:cluster:enable and exp:cluster:disable

This page is for administrators who want an installation to keep its stored files in a DFS cluster, so that a second
web server can join later without moving the site. The kernel's DFS cluster file handler (`eZDFSFileHandler`) keeps
every stored file and cache file in a **share** folder and an index of them in two tables (`ezdfsfile`,
`ezdfsfile_cache`); every web server that runs the site reads the same share and the same tables. A **one-node
cluster** is that setup on one server.

| | |
|---|---|
| Commands | `./console exp:cluster:enable`, `./console exp:cluster:disable` (`bin/php/expclusterenable.php`, `bin/php/expclusterdisable.php`) |
| Dry run | both show what they would do and write nothing without `--apply` |
| Databases | MySQL/MariaDB (`eZDFSFileHandlerMySQLiBackend`); PostgreSQL as soon as the kernel has its cluster backend (`kernel/sql/postgresql/cluster_dfs_schema.sql`, `kernel/clustering/dfspostgresql.php`, `eZDFSFileHandlerPostgresqlBackend`). SQLite, Oracle and MongoDB installations are told why not |
| Code | `expClusterSetup` (`kernel/classes/cluster/expclustersetup.php`) |

## exp:cluster:enable

```bash
./console exp:cluster:enable                                       # what would be done
./console exp:cluster:enable --apply                               # share: dfs next to the installation
./console exp:cluster:enable --share=/mnt/e1/dfs/example.com --apply
```

1. Reads every siteaccess's settings: all of them must use one database. Cluster settings the installation already
   has (its own `[ClusteringSettings]`, a `config.cluster.php` it did not write, a hosting panel's) are left alone.
2. Makes the share (default: the folder `dfs` next to the installation, `/var/www/vhosts/example.com/dfs` for
   `/var/www/vhosts/example.com/httpdocs`; `--share` another absolute path outside it) with a marker file
   `.exp-cluster.json`.
3. Makes the cluster tables in the site's own database from `kernel/sql/<engine>/cluster_dfs_schema.sql`. Tables with
   rows that an earlier switch of the command did not leave are refused.
4. Writes a marked block `# BEGIN Exponential cluster` ... `# END Exponential cluster` before the closing comment of
   `settings/override/file.ini.append.php` (`[ClusteringSettings] FileHandler=eZDFSFileHandler`,
   `[eZDFSClusteringSettings]` with the site's own database connection) and `config.cluster.php` for the cluster
   gateway `index_cluster.php`. Both files are backed up to `var/backup/cluster/<time>/` first; the INI caches are
   cleared.
5. Moves the stored files in with `bin/php/clusterize.php` once per var directory (`--no-move` leaves that for later).
   A stopped move puts the settings back.
6. Prints the gateway's rewrite rule for the web server, to stand in front of the site's own rules:

```apache
# Apache, in the virtual host (Plesk: additional directives); [END] keeps .htaccess from rewriting it again
RewriteRule ^/var/([^/]+/)?storage/images(-versioned)?/.* /index_cluster.php [PT,END]
```

```apache
# .htaccess (the two commented lines of .htaccess_root, before the index_image.php rule)
RewriteRule ^var/([^/]+/)?storage/images(-versioned)?/.* index_cluster.php [L]
RewriteRule ^index_cluster\.php - [L]
```

```nginx
location ~ ^/var/([^/]+/)?storage/images(-versioned)?/ { rewrite ^ /index_cluster.php last; }
```

The command writes no web server configuration: that belongs to whoever runs the web server. On one node the local
copies of the files are still there, so the site works before the rule is added; a second node needs it.

## exp:cluster:disable

```bash
./console exp:cluster:disable            # what would be done
./console exp:cluster:disable --apply
```

Take the gateway rule out of the web server first. The command moves the files back with `clusterize.php -u` (once per
var directory), takes its block out of file.ini, removes its `config.cluster.php` and clears the INI caches. The share
and the tables are kept; a later `exp:cluster:enable` empties and fills them again. Cluster settings it did not write
are left alone (exit 2).

## Exit codes

| Code | |
|---|---|
| 0 | done, shown (dry run), or on (off) already |
| 1 | a step failed; what was written is put back |
| 2 | wrong options, or the installation cannot be switched (its database, cluster settings of its own) |

## Hosting panels

7x's Katalyst does the same switch for hosted sites from the site's Settings tab, as the site's system user, and also
writes the gateway rule through Plesk. A cluster it made carries its own marker; the commands leave it alone and Katalyst
leaves theirs alone.
