-- Tattoo Fight schema.
--
-- `artist_votes` mirrors the WordPress table `hkj_artist_votes` one-for-one. That table has no
-- CREATE TABLE anywhere in the theme or plugins — it was created by hand — so these column
-- types are reconstructed from how functions.php reads and writes them. Confirm against prod
-- with:  SHOW CREATE TABLE hkj_artist_votes;
--
-- `contests` and `voting_leaderboard` replace the WP custom post types of the same names
-- (post + ACF meta), keeping every field the templates actually read.

CREATE TABLE IF NOT EXISTS contests (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title       VARCHAR(255)  NOT NULL DEFAULT '',
  start_date  DATETIME      NULL,
  end_date    DATETIME      NULL,
  status      VARCHAR(20)   NOT NULL DEFAULT 'publish',
  created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_window (status, start_date, end_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS voting_leaderboard (
  id                INT UNSIGNED NOT NULL AUTO_INCREMENT,
  contest_id        INT UNSIGNED NULL,
  artist_name       VARCHAR(255) NOT NULL DEFAULT '',
  ig_handle         VARCHAR(255) NOT NULL DEFAULT '',
  why_compete       TEXT         NULL,
  portfolio_link    VARCHAR(500) NOT NULL DEFAULT '',
  artist_photo      VARCHAR(500) NOT NULL DEFAULT '',
  votes             INT UNSIGNED NOT NULL DEFAULT 0,
  `rank`            INT          NOT NULL DEFAULT 0,
  feature_in_round  TINYINT(1)   NOT NULL DEFAULT 0,
  status            VARCHAR(20)  NOT NULL DEFAULT 'publish',
  created_at        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_contest (contest_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS artist_votes (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email           VARCHAR(255) NOT NULL DEFAULT '',
  phone           VARCHAR(50)  NOT NULL DEFAULT '',
  ip_address      VARCHAR(45)  NOT NULL DEFAULT '',   -- 45 chars so IPv6 fits
  artist_id       INT UNSIGNED NOT NULL DEFAULT 0,
  otp             VARCHAR(10)  NOT NULL DEFAULT '',
  otp_expires_at  DATETIME     NULL,
  vote_date       DATE         NULL,
  created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  verified        TINYINT(1)   NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_daily_email (email, artist_id, vote_date, verified),
  KEY idx_daily_ip    (ip_address, artist_id, vote_date, verified)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
