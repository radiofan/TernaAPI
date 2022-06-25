SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Структура таблицы `our_u_options`
--

CREATE TABLE `our_u_options` (
    `user_id` bigint(20) UNSIGNED NOT NULL,
    `key` varchar(30) NOT NULL,
    `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `our_u_roles`
--

CREATE TABLE `our_u_roles` (
    `id` smallint(5) UNSIGNED NOT NULL,
    `role` varchar(30) NOT NULL,
    `description` tinytext NOT NULL,
    `level` tinyint(3) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `our_u_tokens`
--

CREATE TABLE `our_u_tokens` (
    `user_id` bigint(20) UNSIGNED NOT NULL,
    `token` varbinary(32) NOT NULL,
    `user_agent` varbinary(20) NOT NULL,
    `time_start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `time_end` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `time_work` tinytext NOT NULL COMMENT 'DateTimeInterval, время на которое будет обновляться токен если сессия активна'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `our_u_users`
--

CREATE TABLE `our_u_users` (
    `id` bigint(20) UNSIGNED NOT NULL,
    `login` varchar(30) NOT NULL,
    `password` varbinary(32) NOT NULL,
    `email` tinytext NOT NULL,
    `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `bio` text NOT NULL,
    `avatar` text NOT NULL,
    `level` smallint(6) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `our_u_users_roles`
--

CREATE TABLE `our_u_users_roles` (
    `user_id` bigint(20) UNSIGNED NOT NULL,
    `role_id` smallint(5) UNSIGNED NOT NULL,
    `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `end_time` timestamp NULL DEFAULT NULL,
    `work_time` varchar(255) NOT NULL DEFAULT 'INF' COMMENT 'sql time INTERVAL / "INF"',
    `action_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `p_posts`
--

CREATE TABLE `p_posts` (
     `id` bigint(20) UNSIGNED NOT NULL,
     `user_id` bigint(20) UNSIGNED NOT NULL,
     `parent_id` bigint(20) UNSIGNED NULL DEFAULT NULL,
     `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
     `lang` tinytext NOT NULL,
     `text` MEDIUMTEXT NOT NULL,
     `bugs` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
     `features`bigint(20) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `p_bugs_features`
--

CREATE TABLE `p_bugs_features` (
   `user_id` bigint(20) UNSIGNED NOT NULL,
   `post_id` bigint(20) UNSIGNED NOT NULL,
   `type` tinyint NOT NULL COMMENT '1 - feature, -1 - bug'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `p_tags`
--

CREATE TABLE `p_tags` (
   `id` bigint(20) UNSIGNED NOT NULL,
   `tag` tinytext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `p_tags_posts`
--

CREATE TABLE `p_tags_posts` (
  `tag_id` bigint(20) UNSIGNED NOT NULL,
  `post_id` bigint(20)UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------
--
-- Индексы таблицы `our_u_options`
--
ALTER TABLE `our_u_options`
    ADD PRIMARY KEY (`user_id`,`key`),
    ADD KEY `user_id_idx` (`user_id`);

--
-- Индексы таблицы `our_u_roles`
--
ALTER TABLE `our_u_roles`
    ADD PRIMARY KEY (`id`),
    ADD UNIQUE KEY `role` (`role`);

--
-- Индексы таблицы `our_u_tokens`
--
ALTER TABLE `our_u_tokens`
    ADD PRIMARY KEY (`user_id`,`token`),
    ADD KEY `user_id_idx` (`user_id`);

--
-- Индексы таблицы `our_u_users`
--
ALTER TABLE `our_u_users`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `our_u_users_roles`
--
ALTER TABLE `our_u_users_roles`
    ADD KEY `user_id_idx` (`user_id`),
    ADD KEY `role_id_idx` (`role_id`);

--
-- Индексы таблицы `p_posts`
--
ALTER TABLE `p_posts`
    ADD PRIMARY KEY (`id`),
    ADD KEY `user_id_idx` (`user_id`),
    ADD KEY `parent_id_idx` (`parent_id`);

--
-- Индексы таблицы `p_bugs_features`
--
ALTER TABLE `p_bugs_features`
    ADD PRIMARY KEY (`user_id`, `post_id`),
    ADD KEY `user_id_idx` (`user_id`),
    ADD KEY `parent_id_idx` (`post_id`);

--
-- Индексы таблицы `p_tags`
--
ALTER TABLE `p_tags`
    ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `p_tags_posts`
--
ALTER TABLE `p_tags_posts`
    ADD PRIMARY KEY (`tag_id`, `post_id`),
    ADD KEY `tag_id_idx` (`tag_id`),
    ADD KEY `post_id_idx` (`post_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `our_u_roles`
--
ALTER TABLE `our_u_roles`
    MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `our_u_users`
--
ALTER TABLE `our_u_users`
    MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `p_posts`
--
ALTER TABLE `p_posts`
    MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT для таблицы `p_posts`
--
ALTER TABLE `p_tags`
    MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Ограничения внешнего ключа таблицы `our_u_options`
--
ALTER TABLE `our_u_options`
    ADD CONSTRAINT `user_id_key_opt` FOREIGN KEY (`user_id`) REFERENCES `our_u_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `our_u_tokens`
--
ALTER TABLE `our_u_tokens`
    ADD CONSTRAINT `user_id_key_tokens` FOREIGN KEY (`user_id`) REFERENCES `our_u_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `our_u_users_roles`
--
ALTER TABLE `our_u_users_roles`
    ADD CONSTRAINT `role_id_key` FOREIGN KEY (`role_id`) REFERENCES `our_u_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `user_id_key` FOREIGN KEY (`user_id`) REFERENCES `our_u_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


--
-- Ограничения внешнего ключа таблицы `p_posts`
--
ALTER TABLE `p_posts`
    ADD CONSTRAINT `post_user_id_key` FOREIGN KEY (`user_id`) REFERENCES `our_u_users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `post_parent_id_key` FOREIGN KEY (`parent_id`) REFERENCES `p_posts` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `p_posts`
--
ALTER TABLE `p_bugs_features`
    ADD CONSTRAINT `fb_user_id_key` FOREIGN KEY (`user_id`) REFERENCES `our_u_users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `fb_post_id_key` FOREIGN KEY (`post_id`) REFERENCES `p_posts` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `p_tags_posts`
--
ALTER TABLE `p_tags_posts`
    ADD CONSTRAINT `tp_tag_id_key` FOREIGN KEY (`tag_id`) REFERENCES `p_tags` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
    ADD CONSTRAINT `tp_post_id_key` FOREIGN KEY (`post_id`) REFERENCES `p_posts` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;




INSERT INTO `our_u_roles` (`id`, `role`, `description`, `level`) VALUES
(1, 'change_bio', 'Пользователь может менять свои данные', 1),
(2, 'ignore_max_token_remember', 'Может создавать сколько угодно токенов входа', 100);


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
