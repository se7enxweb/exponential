# Static publishing on Windows and clusters

This guide is for administrators who run Exponential 6.0.15+ on Windows, on several web servers with shared storage,
or under Velocity, and want to know what static publishing does differently there.

[Guides](README.md) · Related: [Static publishing](../features/6.0/static-publishing.md#platforms),
[Repositories and credentials](static-publishing-targets-and-credentials.md)

## What you'll have at the end

Builds and publishes that work the same on Windows, on a cluster and under Velocity, run on the right machine and as
the right user.

## Windows

- **git**: install Git for Windows and make sure `git` is on the `PATH` of the user that runs the console. git is
  started without a shell, from an argument list, so paths with spaces work.
- **Paths**: the manifest always writes paths with `/`, whatever the system, so a build made on Windows and one made
  on Linux compare cleanly.
- **File names**: page paths that cannot be file names on Windows (with `:`, `*`, `?` or names such as `CON`) are
  skipped and listed in the report, on every system, so a build never depends on where it ran.
- **Line endings**: the build writes files byte for byte as the site sends them, and the manifest holds their
  SHA-256. Keep git from converting them in the target's folder: `git -C <folder> config core.autocrlf false`
  once, if your Git for Windows is set to convert.
- **The deploy key** file is written with the rights of the current user for the time of the push.
- **The preview** (`exp:static:serve`) starts PHP's built-in web server without a shell.
- **Rights**: run the commands as the user that owns the site's files, as on Linux.

## Clusters

On a cluster (several web servers with the cluster file handler and shared storage):

- **Files** below `var/` are read through the cluster file handler, so storage images and image aliases kept in the
  cluster's storage are found even when they are not on the local disk. Anything else a page names is requested from
  the site, like a visitor would.
- **Run builds on one node.** The target's folder and git repository are local files on the machine that builds.
  Choose one node (or a separate build server with access to the database and storage) for the console, the
  cronjob part `staticpublish` and the administration's jobs, and keep `WorkDir` on that node.
- **Jobs from the administration** start as console processes on the node that answered the request. On a cluster,
  either route Setup > Static publishing to the build node, or publish from the console or the cronjob there.
- **The lock** of a target is a file in `var/<site>/static-publish/.locks/`: it protects a target on one machine,
  not across machines. Another reason to build on one node only.
- **The crawl** fetches pages over HTTP; set `CrawlURL` to one node's internal address so the build does not pass the
  load balancer or a CDN that may serve cached pages.

## Velocity

Under Velocity the administration starts each job as a detached console process, so a long build never holds a
worker. The crawl goes over HTTP to Velocity like to any web server. After changing `staticcache.ini`, restart
Velocity like after any settings change.

## Databases

Every database Exponential runs on works. The build reads only content and image tables; the upgrade step
`6.0.15-static-publish-tables` creates `exp_static_run` and `exp_static_page` on each.

## Next step

[Static publishing: troubleshooting](static-publishing-troubleshooting.md).
