-- The DFS cluster metadata tables for SQLite 3.24 and later (expDFSFileHandlerSQLiteBackend): one node only.
-- Same columns as kernel/sql/mysql/cluster_dfs_schema.sql; name_hash is md5( name ), the same on every engine.
-- INTEGER is a 64 bit number in SQLite: sizes and times have no 2038 limit. The name indexes use SQLite's BINARY
-- collation, which the backend's GLOB prefix searches (name GLOB 'var/site/cache/*') need to use them.

CREATE TABLE ezdfsfile (
  name TEXT NOT NULL,
  name_trunk TEXT NOT NULL,
  name_hash CHAR(32) NOT NULL DEFAULT '',
  datatype VARCHAR(255) NOT NULL DEFAULT 'application/octet-stream',
  scope VARCHAR(25) NOT NULL DEFAULT '',
  size INTEGER NOT NULL DEFAULT 0,
  mtime INTEGER NOT NULL DEFAULT 0,
  expired INTEGER NOT NULL DEFAULT 0,
  status INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (name_hash)
);

CREATE INDEX ezdfsfile_name ON ezdfsfile (name);
CREATE INDEX ezdfsfile_name_trunk ON ezdfsfile (name_trunk);
CREATE INDEX ezdfsfile_mtime ON ezdfsfile (mtime);
CREATE INDEX ezdfsfile_expired_name ON ezdfsfile (expired, name);

CREATE TABLE ezdfsfile_cache (
  name TEXT NOT NULL,
  name_trunk TEXT NOT NULL,
  name_hash CHAR(32) NOT NULL DEFAULT '',
  datatype VARCHAR(255) NOT NULL DEFAULT 'application/octet-stream',
  scope VARCHAR(25) NOT NULL DEFAULT '',
  size INTEGER NOT NULL DEFAULT 0,
  mtime INTEGER NOT NULL DEFAULT 0,
  expired INTEGER NOT NULL DEFAULT 0,
  PRIMARY KEY (name_hash)
);

CREATE INDEX ezdfsfile_cache_name ON ezdfsfile_cache (name);
CREATE INDEX ezdfsfile_cache_name_trunk ON ezdfsfile_cache (name_trunk);
CREATE INDEX ezdfsfile_cache_mtime ON ezdfsfile_cache (mtime);
CREATE INDEX ezdfsfile_cache_expired_name ON ezdfsfile_cache (expired, name);
