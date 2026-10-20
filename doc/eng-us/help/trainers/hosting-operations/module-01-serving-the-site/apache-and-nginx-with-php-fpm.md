---
title: "Lesson 1.2: Apache and nginx with PHP-FPM"
audience: trainer, hosting, admin
category: training
order: 12
summary: Learners set up an Apache virtual host with PHP-FPM for a training installation, compare it with the derived nginx server block, and fix file ownership so every writer uses one user. 45 minutes.
keywords: hosting and operations, Apache, nginx, PHP-FPM, virtual host, htaccess, AllowOverride, mod_rewrite, mod_proxy_fcgi, Plesk, file permissions, ownership
type: help
---
# Lesson 1.2: Apache and nginx with PHP-FPM

Many hosts already run Apache or nginx with PHP-FPM, and many customers expect it. This lesson shows the
traditional setup the way chapter 8 describes it: the rewrite rules, the virtual host, the PHP-FPM handler, the
derived nginx block and the file ownership rule that prevents most permission trouble.

> **Note**
> The exercise needs root on the learner's own virtual machine. Never practise on a server that hosts other sites.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 1: Serving the site](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | System administrators and hosting support staff |
| Prerequisites | [Lesson 1.1](how-a-request-is-served.md); a Linux virtual machine with Apache, PHP-FPM and a training installation |

## Learning objectives

After this lesson, learners can:

- enable the rewrite rules by copying `.htaccess_root` to `.htaccess`;
- write a virtual host whose document root is the installation root and whose PHP goes to a PHP-FPM pool;
- check and reload Apache safely with `apachectl configtest`;
- explain how the nginx server block of chapter 8 maps to `.htaccess_root` and why it is marked as derived;
- say which directories the PHP user must be able to write, and why all writers should be one user.

## Trainer's notes

### How to present it

Source: [Chapter 8, Apache with PHP-FPM](../../../../install/08-serving-the-site.md#85-apache-with-php-fpm),
[nginx with PHP-FPM](../../../../install/08-serving-the-site.md#86-nginx-with-php-fpm) and
[File permissions and ownership](../../../../install/08-serving-the-site.md#87-file-permissions-and-ownership).

1. **The rewrite rules** (3 minutes). `cp .htaccess_root .htaccess`: copy, do not move, so an update can bring a new
   version. `.htaccess_root_static` is only for a generated static cache.
2. **The virtual host** (4 minutes). Show the `<VirtualHost *:80>` example of section 8.5.1: `DocumentRoot`,
   `AllowOverride All` so the `.htaccess` is read, and the `<FilesMatch \.php$>` handler that sends scripts to the
   PHP-FPM socket. Modules: `mod_rewrite`, `mod_proxy`, `mod_proxy_fcgi`; for HTTPS `mod_ssl`, for HTTP/2
   `mod_http2`.
3. **nginx** (4 minutes). Exponential ships no nginx configuration. The block in section 8.6 is a translation of
   `.htaccess_root` and must be tested on the site and compared after every upgrade. Explain nginx's location
   precedence: exact matches, then `^~` prefixes, then regular expressions in file order, then the longest plain
   prefix. That is why the dot-file and script rules come first.
4. **Plesk** (2 minutes). Plesk writes the virtual host itself: point the document root at the installation root,
   choose "FPM application served by Apache", keep `.htaccess`. Each domain's pool is served by a unit such as
   `plesk-php85-fpm`; `systemctl restart php-fpm` restarts a different master (section 8.5.3).
5. **One user** (2 minutes). Permission problems almost always come from two users writing the same files. Run the
   pool, the Velocity workers, cronjobs and console scripts as the site's user (section 8.7.2).

### Common questions

**Can I avoid `.htaccess` for speed?** Yes: copy the rules into the `<Directory>` block and set `AllowOverride None`.
In server context every pattern needs a leading slash; take the rules from `.htaccess_root`, not from the older
`doc/eng-us/examples/ezpublish.conf`.

**Every page is a 404. Why?** `.htaccess` is not read (`AllowOverride`) or `mod_rewrite` is off.

**A 500 error after the switch?** Read the Apache and PHP-FPM error logs; chapter 8 names them as the first place to
look.

**Do security headers need Apache configuration?** The kernel sends them on its pages
(`site.ini [HTTPHeaderSettings] SecurityHeaders[]`); for static files add them with `mod_headers` if wanted
([chapter 13](../../../../install/13-security-hardening.md)).

### Pitfalls

- **Distribution differences.** Debian names the service `apache2` and enables modules with `a2enmod`; Red Hat style
  systems call it `httpd`. Prepare both command lists for a mixed class.
- **Wrong socket path.** The `SetHandler` line must name the socket of the learner's PHP-FPM pool; have learners look
  it up in their pool file before editing.
- **Root-owned cache files.** A learner who ran a console command as root breaks the site for the pool user. Show
  `ls -l var/` and fix ownership as in step 10.

## Hands-on exercise

Use your own Linux virtual machine with Apache, PHP-FPM and a training installation in, for example,
`/var/www/training`.

1. Change to the installation root and run `cp .htaccess_root .htaccess`.
2. Enable the modules (Debian: `a2enmod rewrite proxy_fcgi`), or check they are loaded on your system.
3. Find your PHP-FPM pool's socket path in its pool file (the `listen` line) and note the pool's `user`.
4. Create a virtual host file with the example of section 8.5.1, using your document root, a training host name
   such as `training.example.invalid` and your socket path.
5. Run `apachectl configtest` and fix anything it reports until it prints `Syntax OK`.
6. Reload Apache (`systemctl reload apache2` or `systemctl reload httpd`).
7. Add the host name to the virtual machine's `/etc/hosts` pointing at `127.0.0.1`.
8. Run the four checks of lesson 1.1 against `http://training.example.invalid/`.
9. Open the front page in a browser on the virtual machine (or with `curl -sI`).
10. Give the writable directories to the pool user, as section 8.7.1 shows:

    ```bash
    sudo chown -R <pool-user>:<pool-user> design extension settings var
    ```

11. Read the nginx block of section 8.6 and find the line that corresponds to the final `RewriteRule` of
    `.htaccess_root`.

## Expected result

- `apachectl configtest` prints `Syntax OK`; the reload gives no error.
- The four checks give the same answers as in lesson 1.1 (`404`, `404`, `200`, `0`).
- The front page answers `200` (or a redirect) through Apache.
- New files under `var/` belong to the pool user after a few requests.
- In step 11 the learner points at `location /`, the plain prefix that sends everything else to `index.php`.
- The trainer checks by asking each learner for the result of the `.git/config` request through Apache.

## Quiz

1. Why is `.htaccess_root` copied rather than moved to `.htaccess`?
2. Which Apache setting must allow the `.htaccess` file to be read?
   A. `Options Indexes` B. `AllowOverride All` C. `DirectoryIndex` D. `ProxyPreserveHost`
3. On a Plesk server, why does `systemctl restart php-fpm` not apply a change to a site?
4. True or false: the project ships a tested nginx configuration in its repository.
5. A cache file written by `root` breaks the site. What is the rule that prevents this?

<details>
<summary>Answers</summary>

1. So an update can bring a new version of `.htaccess_root` (section 8.5.1).
2. **B.** Without `AllowOverride` the rules are not read and every page is a 404.
3. Plesk's sites run under a different master, such as `plesk-php85-fpm`; the `php-fpm` unit is one no Plesk site
   uses (section 8.5.3).
4. **False.** The nginx block in chapter 8 is derived from `.htaccess_root` and must be tested.
5. Run everything that writes `var/` (pool, Velocity workers, cronjobs, console) as the site's user, or use a shared
   group with group-writable directories (section 8.7.2).

</details>

## Further reading

- [Apache with PHP-FPM](../../../hosting/servers/apache-with-php-fpm.md): the host's view of the setup.
- [nginx with PHP-FPM](../../../hosting/servers/nginx-with-php-fpm.md): the derived block and its caveats.
- [Plesk](../../../hosting/panels/plesk.md): Exponential on a Plesk server.
- [Writable directories and permissions](../../../hosting/basics/writable-directories-and-permissions.md): ownership
  in detail.

## Related pages

- [Module 1: Serving the site](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
