DROP TABLE `webref_rss_details`;
CREATE TABLE `webref_rss_details` (
  `id` INTEGER PRIMARY KEY,
  `npr_show_id` INTEGER NOT NULL,
  `code` text NOT NULL,
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

INSERT INTO `webref_rss_details` VALUES (3, 3, 'npr-morning-edition',
                                         'NPR: Morning Edition',
                                         'Morning Edition gives its audience news, analysis, commentary, and coverage of arts and sports. Stories are told through conversation as well as full reports. It''s up-to-the-minute news that prepares listeners for the day ahead.',
                                         'National Public Radio',
                                         'http://www.npr.org/programs/morning-edition/',
                                         'English',
                                         'morning edition', 'http://podcasts.corykim.com/img/morning-edition-logo-2011.jpg', 'http://www.npr.org/programs/morning-edition/', '1400', '1400');

INSERT INTO `webref_rss_details` VALUES (2, 2, 'npr-all-things-considered',
                                         'NPR: All Things Considered',
                                         'Every weekday, All Things Considered hosts Robert Siegel, Melissa Block and Audie Cornish present the program''s trademark mix of news, interviews, commentaries, reviews, and offbeat features.',
                                         'National Public Radio',
                                         'http://www.npr.org/programs/all-things-considered/',
                                         'English',
                                         'all things considered', 'http://podcasts.corykim.com/img/npr-all-things-considered.jpg', 'http://www.npr.org/programs/all-things-considered/', '1609', '1400');

INSERT INTO `webref_rss_details` VALUES (7, 7, 'npr-weekend-edition-saturday',
                                         'NPR: Weekend Edition Saturday',
                                         'From civil wars in Bosnia and El Salvador, to hospital rooms, police stations, and America''s backyards, National Public Radio''s Peabody Award-winning correspondent Scott Simon brings a well-traveled perspective to his role as host of Weekend Edition Saturday.',
                                         'National Public Radio',
                                         'http://www.npr.org/programs/weekend-edition-saturday/',
                                         'English',
                                         'weekend edition saturday', 'http://podcasts.corykim.com/img/npr-weekend-edition.jpg', 'http://www.npr.org/programs/weekend-edition-saturday/', '1400', '1400');

INSERT INTO `webref_rss_details` VALUES (10, 10, 'npr-weekend-edition-sunday',
                                         'NPR: Weekend Edition Sunday',
                                         'Weekend Edition Sunday premiered on Jan. 18, 1987. Since then, Weekend Edition Sunday has covered newsmakers and artists, scientists and politicans, music makers of all kinds, writers, thinkers, theologians and all manner of news events. Originally hosted by Susan Stamberg, the show was anchored by Liane Hansen for 22 years.',
                                         'National Public Radio',
                                         'http://www.npr.org/programs/weekend-edition-sunday/',
                                         'English',
                                         'weekend edition sunday', 'http://podcasts.corykim.com/img/npr-weekend-edition-sunday.png', 'http://www.npr.org/programs/weekend-edition-sunday/', '1400', '1400');



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
  `pub_date` DATE NOT NULL,
  `show_date` DATE NOT NULL,
  `feature_order` INTEGER
);

DROP TABLE `audit_log`;
CREATE TABLE `audit_log` (
  `id` INTEGER PRIMARY KEY,
  `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `type` text NOT NULL,
  `message` mediumtext,
  `other` mediumtext
);


