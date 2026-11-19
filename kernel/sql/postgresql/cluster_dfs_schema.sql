-- The DFS cluster metadata tables for PostgreSQL 13 and later (expDFSFileHandlerPostgresqlBackend).
-- Same columns as kernel/sql/mysql/cluster_dfs_schema.sql; name_hash is md5( name ), the same on every engine.
-- Sizes and times are bigint (no 2038 limit). The text_pattern_ops index serves both name = ... and the prefix
-- searches name LIKE 'var/site/cache/%' of the deletes and purges, whatever the database's collation.
-- Upgrading tables made by the ezpostgresqlcluster extension: cluster_dfs_schema_upgrade.sql.

CREATE TABLE ezdfsfile (
  name text NOT NULL,
  name_trunk text NOT NULL,
  name_hash character(32) NOT NULL DEFAULT '',
  datatype character varying(255) NOT NULL DEFAULT 'application/octet-stream',
  scope character varying(25) NOT NULL DEFAULT '',
  size bigint NOT NULL DEFAULT 0,
  mtime bigint NOT NULL DEFAULT 0,
  expired smallint NOT NULL DEFAULT 0,
  status smallint NOT NULL DEFAULT 0,
  CONSTRAINT ezdfsfile_pkey PRIMARY KEY (name_hash)
);

CREATE INDEX ezdfsfile_name ON ezdfsfile (name text_pattern_ops);
CREATE INDEX ezdfsfile_name_trunk ON ezdfsfile (name_trunk);
CREATE INDEX ezdfsfile_mtime ON ezdfsfile (mtime);
CREATE INDEX ezdfsfile_expired_name ON ezdfsfile (expired, name text_pattern_ops);

CREATE TABLE ezdfsfile_cache (
  name text NOT NULL,
  name_trunk text NOT NULL,
  name_hash character(32) NOT NULL DEFAULT '',
  datatype character varying(255) NOT NULL DEFAULT 'application/octet-stream',
  scope character varying(25) NOT NULL DEFAULT '',
  size bigint NOT NULL DEFAULT 0,
  mtime bigint NOT NULL DEFAULT 0,
  expired smallint NOT NULL DEFAULT 0,
  CONSTRAINT ezdfsfile_cache_pkey PRIMARY KEY (name_hash)
);

CREATE INDEX ezdfsfile_cache_name ON ezdfsfile_cache (name text_pattern_ops);
CREATE INDEX ezdfsfile_cache_name_trunk ON ezdfsfile_cache (name_trunk);
CREATE INDEX ezdfsfile_cache_mtime ON ezdfsfile_cache (mtime);
CREATE INDEX ezdfsfile_cache_expired_name ON ezdfsfile_cache (expired, name text_pattern_ops);
