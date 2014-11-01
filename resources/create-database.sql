DROP TABLE `webref_rss_details`;
CREATE TABLE `webref_rss_details` (
  `id` INTEGER PRIMARY KEY,
  `title` text NOT NULL,
  `description` mediumtext NOT NULL,
  `author` text,
  `link` text,
  `language` text,
  `image_title` text,
  `image_url` text,
  `image_link` text,
  `image_width` text,
  `image_height` text
);

DROP TABLE `webref_rss_items`;
CREATE TABLE `webref_rss_items` (
  `id` INTEGER PRIMARY KEY,
  `rss_id` INTEGER,
  `story_id` INTEGER,
  `title` text NOT NULL,
  `description` mediumtext NOT NULL,
  `link` text NOT NULL,
  `media_url` text NOT NULL,
  `media_duration` INTEGER NOT NULL,
  `pub_date` DATE NOT NULL
);

DROP TABLE `audit_log`;
CREATE TABLE `audit_log` (
  `id` INTEGER PRIMARY KEY,
  `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `type` text NOT NULL,
  `message` mediumtext,
  `other` mediumtext
);


INSERT INTO `webref_rss_details` VALUES (1, 'NPR Morning Edition', 'Morning Edition gives its audience news, analysis, commentary, and coverage of arts and sports. Stories are told through conversation as well as full reports. It''s up-to-the-minute news that prepares listeners for the day ahead.', 'National Public Radio',
                                         'http://www.npr.org/programs/morning-edition/', 'English',
                                         'morning edition', 'http://podcasts.corykim.com/img/morning-edition-logo-2011.jpg', 'http://www.npr.org/programs/morning-edition/', '1500', '2100');

INSERT INTO `webref_rss_details` VALUES (2, 'NPR All Things Considered', 'Every weekday, All Things Considered hosts Robert Siegel, Melissa Block and Audie Cornish present the program''s trademark mix of news, interviews, commentaries, reviews, and offbeat features.', 'National Public Radio',
                                         'http://www.npr.org/programs/all-things-considered/', 'English',
                                         'all things considered', 'http://podcasts.corykim.com/img/npr-all-things-considered.jpg', 'http://www.npr.org/programs/all-things-considered/', '1400', '1400');