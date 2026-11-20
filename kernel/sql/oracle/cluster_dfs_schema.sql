-- The DFS cluster metadata tables for Oracle 12.1 and later (expDFSFileHandlerOracleBackend).
-- The columns of kernel/sql/mysql/cluster_dfs_schema.sql, with two Oracle differences: SIZE is a reserved word, so
-- the size is the column filesize (the backend returns it as size, and the ezoracle extension's table used the same
-- name); and Oracle stores the empty string as NULL, so datatype and scope may be NULL (the backend reads NULL as '').
-- name_hash is md5( name ), the same on every engine. Sizes and times are NUMBER(19) (no 2038 limit). Names are
-- compared binary and case sensitive (Oracle's default NLS_COMP), so the name index serves name = ... and the
-- prefix searches name LIKE 'var/site/cache/%' of the deletes and purges.
-- Upgrading the table made by the ezoracle extension: cluster_dfs_schema_upgrade.sql.

CREATE TABLE ezdfsfile (
  name VARCHAR2(4000) NOT NULL,
  name_trunk VARCHAR2(4000) NOT NULL,
  name_hash VARCHAR2(34) NOT NULL,
  datatype VARCHAR2(255) DEFAULT 'application/octet-stream',
  scope VARCHAR2(25),
  filesize NUMBER(19) DEFAULT 0 NOT NULL,
  mtime NUMBER(19) DEFAULT 0 NOT NULL,
  expired NUMBER(1) DEFAULT 0 NOT NULL,
  CONSTRAINT ezdfsfile_pk PRIMARY KEY (name_hash)
);

CREATE INDEX ezdfsfile_name ON ezdfsfile (name);
CREATE INDEX ezdfsfile_name_trunk ON ezdfsfile (name_trunk);
CREATE INDEX ezdfsfile_mtime ON ezdfsfile (mtime);
CREATE INDEX ezdfsfile_expired_scope ON ezdfsfile (expired, scope);

CREATE TABLE ezdfsfile_cache (
  name VARCHAR2(4000) NOT NULL,
  name_trunk VARCHAR2(4000) NOT NULL,
  name_hash VARCHAR2(34) NOT NULL,
  datatype VARCHAR2(255) DEFAULT 'application/octet-stream',
  scope VARCHAR2(25),
  filesize NUMBER(19) DEFAULT 0 NOT NULL,
  mtime NUMBER(19) DEFAULT 0 NOT NULL,
  expired NUMBER(1) DEFAULT 0 NOT NULL,
  CONSTRAINT ezdfsfile_cache_pk PRIMARY KEY (name_hash)
);

CREATE INDEX ezdfsfile_cache_name ON ezdfsfile_cache (name);
CREATE INDEX ezdfsfile_cache_name_trunk ON ezdfsfile_cache (name_trunk);
CREATE INDEX ezdfsfile_cache_mtime ON ezdfsfile_cache (mtime);
CREATE INDEX ezdfsfile_cache_expired_scope ON ezdfsfile_cache (expired, scope);
