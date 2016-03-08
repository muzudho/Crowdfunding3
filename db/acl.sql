-- ACL base rows
REPLACE INTO `acl` VALUES(1, '*', '*', '*', '//', 1, '2011-05-18 14:45:40');
REPLACE INTO `acl` VALUES(2, '*', '*', '*', '/image/*', 1, '2011-05-18 21:08:42');
REPLACE INTO `acl` VALUES(3, '*', '*', '*', '/tpv/*', 1, '2011-05-31 18:55:42');
REPLACE INTO `acl` VALUES(4, '*', '*', '*', '/admin/*', 0, '2011-05-18 14:45:40');
REPLACE INTO `acl` VALUES(5, '*', '*', '*', '/project/*', 1, '2011-05-18 14:45:40');
REPLACE INTO `acl` VALUES(6, '*', 'superadmin', '*', '/admin/*', 1, '2011-05-18 14:45:40');
REPLACE INTO `acl` VALUES(7, '*', '*', '*', '/user/edit/*', 0, '2011-05-18 14:49:36');
REPLACE INTO `acl` VALUES(8, '*', '*', '*', '/user/*', 1, '2011-05-18 18:59:54');
REPLACE INTO `acl` VALUES(9, '*', '*', '*', 'user/logout', 1, '2011-05-18 19:15:02');
REPLACE INTO `acl` VALUES(10, '*', '*', '*', '/search', 1, '2011-05-18 19:16:40');
REPLACE INTO `acl` VALUES(11, '*', 'user', '*', '/project/create', 1, '2011-05-18 19:46:44');
REPLACE INTO `acl` VALUES(12, '*', 'user', '*', '/dashboard/*', 1, '2011-05-18 19:48:43');
REPLACE INTO `acl` VALUES(13, '*', 'public', '*', '/invest/*', 0, '2011-05-18 20:30:23');
REPLACE INTO `acl` VALUES(14, '*', 'user', '*', '/message/*', 1, '2011-05-18 20:30:23');
REPLACE INTO `acl` VALUES(15, '*', '*', '*', '/user/logout', 1, '2011-05-18 20:33:27');
REPLACE INTO `acl` VALUES(16, '*', '*', '*', '/discover/*', 1, '2011-05-18 20:37:00');
REPLACE INTO `acl` VALUES(17, '*', '*', '*', '/project/create', 0, '2011-05-18 20:38:22');
REPLACE INTO `acl` VALUES(18, '*', '*', '*', '/project/edit/*', 0, '2011-05-18 20:38:22');
REPLACE INTO `acl` VALUES(19, '*', '*', '*', '/project/raw/*', 0, '2011-05-18 20:39:37');
REPLACE INTO `acl` VALUES(20, '*', 'root', '*', '/project/raw/*', 1, '2011-05-18 20:39:37');
REPLACE INTO `acl` VALUES(21, '*', 'superadmin', '*', '/project/edit/*', 1, '2011-05-18 20:43:08');
REPLACE INTO `acl` VALUES(22, '*', '*', '*', '/project/delete/*', 0, '2011-05-18 20:43:51');
REPLACE INTO `acl` VALUES(23, '*', 'superadmin', '*', '/project/delete/*', 1, '2011-05-18 20:44:37');
REPLACE INTO `acl` VALUES(24, '*', '*', '*', '/blog/*', 1, '2011-05-18 20:45:14');
REPLACE INTO `acl` VALUES(26, '*', '*', '*', '/about/*', 1, '2011-05-18 20:49:01');
REPLACE INTO `acl` VALUES(27, '*', 'superadmin', '*', '/user/edit/*', 1, '2011-05-18 20:56:56');
REPLACE INTO `acl` VALUES(28, '*', 'checker', '*', '/project/edit/*', 1, '2013-02-22 10:25:05');
REPLACE INTO `acl` VALUES(29, '*', 'user', '*', '/user/edit', 1, '2011-05-18 21:56:56');
REPLACE INTO `acl` VALUES(30, '*', 'user', '*', '/message/edit/*', 0, '2011-05-18 22:45:29');
REPLACE INTO `acl` VALUES(31, '*', 'user', '*', '/message/delete/*', 0, '2011-05-18 22:45:29');
REPLACE INTO `acl` VALUES(32, '*', 'superadmin', '*', '/message/edit/*', 1, '2011-05-18 22:56:55');
REPLACE INTO `acl` VALUES(33, '*', 'superadmin', '*', '/message/delete/*', 1, '2011-05-18 22:00:00');
REPLACE INTO `acl` VALUES(34, '*', 'user', '*', '/invest/*', 1, '2011-05-18 22:56:32');
REPLACE INTO `acl` VALUES(35, '*', 'public', '*', '/message/*', 0, '2011-05-18 22:56:32');
REPLACE INTO `acl` VALUES(36, '*', 'public', '*', '/user/edit/*', 0, '2011-05-18 23:00:18');
REPLACE INTO `acl` VALUES(37, '*', 'superadmin', '*', '/cron/*', 1, '2011-05-26 23:04:02');
REPLACE INTO `acl` VALUES(38, '*', '*', '*', '/widget/*', 1, '2011-06-10 09:30:39');
REPLACE INTO `acl` VALUES(39, '*', '*', '*', '/user/recover/*', 1, '2011-06-12 20:31:36');
REPLACE INTO `acl` VALUES(40, '*', '*', '*', '/news/*', 1, '2011-06-19 11:36:34');
REPLACE INTO `acl` VALUES(41, '*', 'user', '*', '/community/*', 1, '2011-06-19 11:49:36');
REPLACE INTO `acl` VALUES(42, '*', '*', '*', '/ws/*', 1, '2011-06-20 21:18:15');
REPLACE INTO `acl` VALUES(43, '*', 'checker', '*', '/review/*', 1, '2011-06-21 15:18:51');
REPLACE INTO `acl` VALUES(44, '*', '*', '*', '/contact/*', 1, '2011-06-29 22:24:00');
REPLACE INTO `acl` VALUES(45, '*', '*', '*', '/service/*', 1, '2011-07-13 15:26:04');
REPLACE INTO `acl` VALUES(46, '*', '*', '*', '/translate/*', 0, '2013-07-26 17:20:21');
REPLACE INTO `acl` VALUES(47, '*', 'translator', '*', '/translate/*', 1, '2011-07-24 10:47:50');
REPLACE INTO `acl` VALUES(48, '*', '*', '*', '/legal/*', 1, '2011-08-05 11:08:11');
REPLACE INTO `acl` VALUES(49, '*', '*', '*', '/rss/*', 1, '2011-08-14 16:31:17');
REPLACE INTO `acl` VALUES(50, '*', 'superadmin', '*', '/impersonate/*', 1, '2011-08-20 07:40:29');
REPLACE INTO `acl` VALUES(52, '*', 'user', 'paypal', '/paypal/*', 1, '2011-09-04 22:58:15');
REPLACE INTO `acl` VALUES(53, '*', 'user', 'paypal', '/cron/*', 1, '2011-09-04 22:58:15');
REPLACE INTO `acl` VALUES(54, '*', '*', '*', '/press/*', 1, '2011-09-06 08:04:34');
REPLACE INTO `acl` VALUES(55, '*', '*', '*', '/project/view/*', 0, '2011-09-16 13:46:31');
REPLACE INTO `acl` VALUES(56, '*', '*', '*', '/mail/*', 1, '2011-09-25 12:13:26');
REPLACE INTO `acl` VALUES(57, '*', 'admin', '*', '/impersonate/*', 1, '2012-09-19 11:52:15');
REPLACE INTO `acl` VALUES(58, '*', '*', '*', '/json/*', 1, '2011-11-22 15:14:48');
REPLACE INTO `acl` VALUES(59, '*', '*', '*', '/call/*', 1, '2011-05-18 14:45:40');
REPLACE INTO `acl` VALUES(60, '*', '*', '*', '/call/create/*', 0, '2011-05-18 20:38:22');
REPLACE INTO `acl` VALUES(61, '*', 'caller', '*', '/call/create/*', 1, '2011-05-18 19:46:44');
REPLACE INTO `acl` VALUES(62, '*', 'superadmin', '*', '/call/create/*', 1, '2011-05-18 19:46:44');
REPLACE INTO `acl` VALUES(63, '*', '*', '*', '/call/edit/*', 0, '2011-05-18 20:38:22');
REPLACE INTO `acl` VALUES(64, '*', '*', '*', '/call/raw/*', 0, '2011-05-18 20:39:37');
REPLACE INTO `acl` VALUES(65, '*', 'root', '*', '/call/raw/*', 1, '2011-05-18 20:39:37');
REPLACE INTO `acl` VALUES(66, '*', 'superadmin', '*', '/call/edit/*', 1, '2011-05-18 20:43:08');
REPLACE INTO `acl` VALUES(68, '*', '*', '*', '/call/delete/*', 0, '2011-05-18 20:43:51');
REPLACE INTO `acl` VALUES(69, '*', 'superadmin', '*', '/call/delete/*', 1, '2011-05-18 20:44:37');
REPLACE INTO `acl` VALUES(70, '*', '*', '*', '/call/view/*', 0, '2011-09-16 13:46:31');
REPLACE INTO `acl` VALUES(71, '*', '*', '*', '/maintenance/*', 1, '2012-02-06 11:22:30');
REPLACE INTO `acl` VALUES(72, '*', 'admin', '*', '/admin/*', 1, '2012-02-26 10:47:06');
REPLACE INTO `acl` VALUES(73, '*', 'admin', '*', '/project/edit/*', 1, '2012-03-21 05:32:57');
REPLACE INTO `acl` VALUES(75, '*', 'admin', '*', '/translate/select/*', 1, '2012-05-04 18:06:34');
REPLACE INTO `acl` VALUES(76, '*', '*', 'translator', '/translate/node/*', 0, '2012-05-04 19:11:57');
REPLACE INTO `acl` VALUES(77, '*', '*', 'root', '/system/*', 1, '2012-06-30 18:52:51');
REPLACE INTO `acl` VALUES(78, '*', '*', '*', '/sacaexcel/*', 0, '2012-12-18 00:42:17');
REPLACE INTO `acl` VALUES(79, '*', 'admin', '*', '/sacaexcel/*', 1, '2012-12-18 00:42:17');
REPLACE INTO `acl` VALUES(80, '*', 'superadmin', '*', '/sacaexcel/*', 1, '2012-12-18 00:42:17');
REPLACE INTO `acl` VALUES(81, '*', 'root', '*', '/sacaexcel/*', 1, '2012-12-18 00:42:17');
REPLACE INTO `acl` VALUES(82, '*', '*', '*', '/user/raw/*', 0, '2013-03-13 13:31:26');
REPLACE INTO `acl` VALUES(83, '*', 'root', '*', '/user/raw/*', 1, '2013-03-13 13:31:26');
REPLACE INTO `acl` VALUES(84, '*', 'admin', '*', '/call/edit/*', 1, '2013-06-15 12:21:18');
REPLACE INTO `acl` VALUES(85, '*', '*', '*', '/manage/*', 0, '2013-07-26 17:20:21');
REPLACE INTO `acl` VALUES(86, '*', 'manager', '*', '/manage/*', 1, '2013-07-26 17:20:21');
REPLACE INTO `acl` VALUES(87, '*', '*', '*', '/c7feb7803386d713e60894036feeee9e/*', 1, '2013-07-29 14:57:06');
REPLACE INTO `acl` VALUES(88, '*', '*', '*', '/contract/*', 1, '2013-07-30 13:47:44');
REPLACE INTO `acl` VALUES(89, '*', '*', '*', '/document/*', 0, '2013-08-21 12:14:57');
REPLACE INTO `acl` VALUES(90, '*', 'manager', '*', '/document/*', 1, '2013-08-21 12:14:57');
REPLACE INTO `acl` VALUES(91, '*', 'superadmin', '*', '/document/*', 1, '2013-08-21 12:14:57');
