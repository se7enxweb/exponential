---
title: Containers and orchestration
audience: government, hosting, developer
category: public-sector
order: 40
summary: For platform teams in public bodies that run Exponential in Docker, Podman or Kubernetes. What the sources say about containers, what must be on a persistent volume, the one-writer rule for SQLite, and how a container platform fits a change board.
keywords: containers, Docker, Podman, Kubernetes, orchestration, images, ghcr.io, volumes, persistent storage, SQLite one writer, replicas, platform team, government cloud
type: help
---
# Containers and orchestration

This page is for platform and DevOps teams in public bodies that run their applications in containers. It answers
what the Exponential sources say about Docker, Podman and Kubernetes, what has to live outside the image, how many
containers may write the default database, and how image tags and change approval fit together. The technical
details are on the hosting pages [Containers: Docker and Podman](../../../hosting/models/containers-docker-and-podman.md)
and [Kubernetes](../../../hosting/models/kubernetes.md); this page summarizes them and adds the public-sector angle.

## Contents

1. [What the sources say](#what-the-sources-say)
2. [Images and pinned tags](#images-and-pinned-tags)
3. [What must persist](#what-must-persist)
4. [One container or several](#one-container-or-several)
5. [Containers and your change process](#containers-and-your-change-process)
6. [Checklist](#checklist)

## What the sources say

- **Docker and Podman are tier 2**, known to work. Velocity publishes images on
  `ghcr.io/se7enxweb/exponential-velocity` for amd64 and arm64, one per PHP version and variant
  ([Operating system support](../../../os-support.md)).
- **Kubernetes is tier 3**, expected to work: a reasoned expectation, not a test result. The project asks to be told
  when someone runs it in production ([Kubernetes](../../../hosting/models/kubernetes.md)).
- The sources ship **no Kubernetes manifests**. Your platform team writes them; the Kubernetes page lists the
  decisions they must encode.
- `exp:install` is the install method for container images and automated test environments
  ([Choosing an install method](../../../../install/04-choosing-an-install-method.md)).

## Images and pinned tags

Velocity's images are tagged by release, PHP version and variant
([Velocity packages and binaries](../../../../features/6.0/velocity-packages-and-binaries.md)):

```text
ghcr.io/se7enxweb/exponential-velocity:<release>-php<version>-<variant>   fixed
ghcr.io/se7enxweb/exponential-velocity:php<version>-<variant>             moves with each release
ghcr.io/se7enxweb/exponential-velocity:latest                             newest PHP, standard variant
```

The project's advice is "Pin a fixed tag in production." For a public body this is also a governance point: a change
board approves a specific version, and only a fixed tag guarantees that the approved image is the one that runs.
Record the tag in the change request.

Whatever base you use, run the extension check inside the built image as a pipeline step:

```bash
php bin/php/console exp:velocity ext check
```

## What must persist

| Path | Holds | Where |
|---|---|---|
| `var/` | uploads (`var/storage`), caches, logs, the audit trail, the SQLite database | persistent volume |
| `settings/override` | the installation's settings, database credentials, audit keys | persistent volume or secrets |
| `settings/siteaccess` | siteaccess settings the installer writes | persistent volume |
| `kernel/`, `lib/`, `vendor/`, `extension/`, `design/` | code | in the image |

Files on the volume must belong to the user the container's PHP runs as; decide the user ID once for image and
volume ([Containers: Docker and Podman](../../../hosting/models/containers-docker-and-podman.md)). Keep credentials out
of the image itself.

## One container or several

| | One container | Several replicas |
|---|---|---|
| Database | any supported engine, SQLite 3 included | a database server, PostgreSQL first |
| Uploaded files | one volume | shared storage for `var/storage` |
| Sessions | default files work | shared session storage |
| Cron | in the container or a scheduled job | exactly one scheduler |

SQLite "works in a container when one container writes the database": its WAL mode needs shared memory on one host,
so several replicas cannot share one database file over a network volume. Moving from one replica to several is a
project of its own: see [High availability overview](../availability/high-availability-overview.md) and
[Several web servers behind a load balancer](../availability/several-web-servers-behind-a-load-balancer.md).

## Containers and your change process

Containers make some public-sector duties easier, if you use them for that:

- **Reproducible builds.** A pinned base image, a `composer.lock` and your own extensions in version control give the
  same image every time. That is the evidence a change board asks for.
- **Staging equals production.** Run the approved image on a test environment first, with test settings selected by
  `EXP_ENV` ([Environments for development, test and production](environments-dev-test-prod.md)).
- **No changes inside running containers.** Switch on `ManagedByDeployment` so the administration cannot write
  settings or templates into a container that will be replaced
  ([Configuration under version control](configuration-under-version-control.md)).
- **Backups are still needed.** A volume snapshot is not a tested backup; follow
  [What to back up](../backups-and-recovery/what-to-back-up.md).

## Checklist

- [ ] Production runs a fixed image tag, recorded in the change request.
- [ ] `exp:velocity ext check` runs in the image pipeline.
- [ ] `var/`, `settings/override` and `settings/siteaccess` are persistent; credentials are not in the image.
- [ ] Only one replica writes a SQLite database; several replicas use a database server.
- [ ] One scheduler runs the cronjobs.
- [ ] `ManagedByDeployment` is enabled on the administration siteaccess.

## Related pages

- [Hosting](README.md) index of this folder
- [Containers: Docker and Podman](../../../hosting/models/containers-docker-and-podman.md), [Kubernetes](../../../hosting/models/kubernetes.md)
- [Government and sovereign cloud hosting](government-and-sovereign-cloud.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
