-- purpose, textテーブルに翻訳の追加
-- ユーザー名            …(3〜20文字の半角英数字およびハイフンのみ)
-- メールアドレスを確認  …確認のため、再度メールアドレスの入力をお願いします。
-- パスワード            …(6文字以上の半角英数字)
-- パスワードを認証する  …確認のため、再度パスワードの入力をお願いします。
INSERT INTO `purpose` (`text`, `purpose`, `group`) VALUES ('login-register-userid-field-tips', '(3 a 20 caracteres alfanuméricos y el guión solamente)', 'login');
INSERT INTO `purpose` (`text`, `purpose`, `group`) VALUES ('login-register-confirm-field-tips', 'Para la confirmación, gracias a entrar en la dirección de correo electrónico nuevo.', 'login');
INSERT INTO `purpose` (`text`, `purpose`, `group`) VALUES ('login-register-password-field-tips', '(Más de 6 caracteres alfanuméricos)', 'login');
INSERT INTO `purpose` (`text`, `purpose`, `group`) VALUES ('login-register-confirm_password-field-tips', 'Para la confirmación, gracias a introducir la contraseña de nuevo.', 'login');

INSERT INTO `text` (`id`, `lang`, `text`) VALUES ('login-register-userid-field-tips', 'ja', '(3〜20文字の半角英数字およびハイフンのみ)');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES ('login-register-confirm-field-tips', 'ja', '確認のため、再度メールアドレスの入力をお願いします。');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES ('login-register-password-field-tips', 'ja', '(6文字以上の半角英数字)');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES ('login-register-confirm_password-field-tips', 'ja', '確認のため、再度パスワードの入力をお願いします。');

-- カテゴリーの日本語訳
INSERT INTO `category_lang` (`id`, `lang`, `name`, `description`) VALUES ('2', 'ja', 'ソーシャル', '社会変化を促進するプロジェクトは、との問題を解決するか、より良い幸福を達成するために人間関係を強化します。');
INSERT INTO `category_lang` (`id`, `lang`, `name`, `description`) VALUES ('6', 'ja', 'コミュニケーション', 'その目的（例えば、市民ジャーナリズム、ドキュメンタリー、ブログ、ラジオ番組のために）、通知非難および/または通信することであるプロジェクト。');
INSERT INTO `category_lang` (`id`, `lang`, `name`, `description`) VALUES ('7', 'ja', '技術の', '具体的な問題やニーズを解決するために、ソフトウェア、ハードウェア、ツール、などの技術開発。');
INSERT INTO `category_lang` (`id`, `lang`, `name`, `description`) VALUES ('9', 'ja', '商業の', 'ビジネスイニシアチブであり、プロジェクト、および利益を生成することを願っています。');
INSERT INTO `category_lang` (`id`, `lang`, `name`, `description`) VALUES ('10', 'ja', '教育の', 'その最も重要な目的の情報や学習プロジェクト。');
INSERT INTO `category_lang` (`id`, `lang`, `name`, `description`) VALUES ('11', 'ja', '文化的な', '芸術や文化的な目的を持ったプロジェクト。');
INSERT INTO `category_lang` (`id`, `lang`, `name`, `description`) VALUES ('13', 'ja', '生態学的な', '環境、持続可能性、および/または生物多様性のケアに関連しているプロジェクト。');
INSERT INTO `category_lang` (`id`, `lang`, `name`, `description`) VALUES ('14', 'ja', '科学的', '研究や調査、回答、解決策、新たな説明を探してプロジェクト。');

-- スキルの日本語化。
INSERT INTO `purpose` (`text`, `purpose`, `group`) VALUES ('profile-field-skills', 'Por favor, seleccione una habilidad.', 'profile');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES ('profile-field-skills', 'ja', 'スキルを選んでください。');

-- [私のプロフィール] - [環境設定] ページの「はい」「いいえ」の日本語化。
INSERT INTO `purpose` (`text`, `purpose`, `group`) VALUES ('user-preferences-yes', 'sí', 'user');
INSERT INTO `purpose` (`text`, `purpose`, `group`) VALUES ('user-preferences-no', 'no', 'user');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES ('user-preferences-yes', 'ja', 'はい');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES ('user-preferences-no', 'ja', 'いいえ');

-- 公開プロフィール画面の日本語化。
INSERT INTO `purpose` (`text`, `purpose`, `group`) VALUES ('profile-user_detail-header', 'usuario detalles', 'profile');
INSERT INTO `purpose` (`text`, `purpose`, `group`) VALUES ('profile-skills-header', 'habilidad', 'profile');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES ('profile-user_detail-header', 'ja', 'ユーザー詳細');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES ('profile-skills-header', 'ja', 'スキル');

-- ディスカバー画面の日本語化。
INSERT INTO `purpose` (`text`, `purpose`, `group`) VALUES ('discover-searcher-byskill-header', 'habilidad', 'discover');
INSERT INTO `text` (`id`, `lang`, `text`) VALUES ('discover-searcher-byskill-header', 'ja', 'スキル別');

-- page_node テーブルを翻訳 (FAQは削除)
INSERT INTO `page_node` (`page`, `node`, `lang`, `content`) VALUES ('about', 'goteo', 'ja', '<p>サイト名は、集団の資金調達（寄付金）と分散協調（サービス、インフラなど）のためのソーシャルネットワークです。オープンソースとオープンナレッジを共通の利益に貢献するとしているプロジェクトへの投資のためのプラットフォーム。ソシア、文化、技術的または教育の領域で、自律的、創造的で革新的なプロジェクトの開発のためのコミュニティ、それは社会全体のための新しい機会を作成します。</p><p>&nbsp;</p>');
INSERT INTO `page_node` (`page`, `node`, `lang`, `content`) VALUES ('contact', 'goteo', 'ja', '<div class=\"contact-info\" style=\"color: #58595b; width: 360px; font-size: 12px; padding: 5px; line-height: 16px;\"><span class=\"intro-tit\" style=\"font-size: 21px; font-weight: bold; line-height: 24px;\">すぐにあなたが探しているものを見つけるためにこれらのリンクを使用します。</span><ul style=\"margin-left: 0; padding-left: 0;\"><li style=\"color: #38b5b1; margin-left: 0; padding-left: 0; list-style-position: inside; padding-top: 2px; padding-bottom: 2px;\"></li><li style=\"color: #38b5b1; margin-left: 0; padding-left: 0; list-style-position: inside; padding-top: 2px; padding-bottom: 2px;\"><a href=\"/press\" style=\"color: #38b5b1; text-decoration: none;\" target=\"_blank\">プレスキット SiteName</a></li><li style=\"color: #38b5b1; margin-left: 0; padding-left: 0; list-style-position: inside; padding-top: 2px; padding-bottom: 2px;\"><a href=\"/service/workshop\" style=\"color: #38b5b1; text-decoration: none;\" target=\"_blank\">ワークショップ</a></li></ul>SiteName は、集団の資金調達（寄付金）と分散協調（サービス、インフラなど）のためのソーシャルネットワークです。オープンソースとオープンコンテンツを共通の利益に貢献するとしているプロジェクトへの投資のためのプラットフォーム。</div>');
INSERT INTO `page_node` (`page`, `node`, `lang`, `content`) VALUES ('howto', 'goteo', 'ja', '<div style=\"width:430px; float:left\"><div style=\"font-size:14px; line-height:19px; padding-right:16px; margin-top:16px;\">SiteName は、オープンソースとオープンナレッジを共通の利益に貢献するとしているプロジェクトへの投資のためのプラットフォームです。無料またはオープンライセンス（例えば、クリエイティブ・コモンズ、またはGPL）によって支配集団リターンの種類を与えるために目的を持って、自律的、創造的で革新的なプロジェクトの開発のためのコミュニティ。<p>それは、あなたが\'のための資金調達を見つけたい活動に関連したDNAのオープン、オープンデータ、知識、デジタルコンテンツおよびその他のリソースを得たプロジェクトです。<br /><br />SiteName を使用すると、サイト名のコミュニティによって協調融資と支援のためのプロジェクトを提案したい場合は、「知っている必要があり、以下の条件および要件に案内されます。あなたは以下の点のいずれかの上の任意のより多くの情報が必要な場合、我々はあなたが読んでお勧めします。</p></div><p>&nbsp;</p><form action=\"/project/create\" method=\"post\"><input class=\"checkbox\" id=\"create_accept\" name=\"confirm\" type=\"checkbox\" value=\"true\" />&nbsp;<span style=\"font-size:12px;\"><label class=\"unselected\" for=\"create_accept\">私が読んで持って、プロジェクトサイト名を作成するための条件と要件を理解し、受け入れ、処理するよう<a href=\"/legal/privacy\">個人情報保護方針</a> プラットフォーム。</label><br /></span><p><span style=\"font-size:12px;\">&nbsp;</span></p><span style=\"font-size:12px;\"><button class=\"disabled\" disabled=\"disabled\" id=\"create_continue\" name=\"action\" type=\"submit\" value=\"continue\">続けます</button></span></form></div><div style=\"width:430px; float:right; padding-right:16px; margin-top:16px;\"><span style=\"font-size:14px;\">条件</span><p><span style=\"color:#808285; line-height:16px;\">1. 私のプロジェクトは、特定の経済的貢献と引き換えに、個々の報酬を提供する場合、私は私が私が尋ねた最小量を得るプラットフォームと場合には、私の共同金融に確立されたコミットメントを果たしていきます。<br /><br /><strong>2</strong>. また、私はオープンソース財団と法的な契約に準拠して資金調達を求めた瞬間に選ばれたライセンスの下でサイト名プラットフォームに接続約束した集団的リターンを、公開する約束を遵守しなければなりません。<br /><br /><strong>3.</strong> 私は最適なプロジェクトを遂行するために共同出資の最小値を求めなければなりません。最小の協調融資を上げると私は最適な資金調達に到達するまで、私は協調融資の第二ラウンドに着手することができ、定期的に情報が送信されますその上に生産開始、と一致します。 &nbsp;<br /><br /><strong>4</strong>. プロジェクトの目的は、慈善団体、政治の資金調達キャンペーンのまたは任意の他のタイプ、犯罪者や他の人に対して目的としていないのどちらも、製品または既に生産サービスの販売ではありません。</span></p><p>&nbsp;</p><p><span style=\"font-size:14px;\">資格<p><span style=\"color:#808285; line-height:16px;\">&middot; 私は18歳以上です。</span><br /><span style=\"color:#808285; line-height:16px;\">&middot; 私は銀行口座を持っています><div style=\"font-size:11px; color:#808285; float:left; clear:left; margin-top:20px; margin-right:10px; \">あなたは、あなたの個人データの処理のために、あなたの同意を与えます。この目的のために、ポータルの責任が確立しています<a href=\"/legal/privacy\">個人情報保護方針</a>  ここで、あなたは、このフォームを通じて提供されたデータだけでなく、その人の権利を説明する目的を知ることができるようになります。</div>');

-- page テーブルを翻訳
INSERT INTO `page_lang` (`id`, `lang`, `name`, `description`) VALUES ('about', 'ja', 'アバウト', 'アバウト');
INSERT INTO `page_lang` (`id`, `lang`, `name`, `description`) VALUES ('big-error', 'ja', 'BIGエラー', '内部サーバーエラーなど');
INSERT INTO `page_lang` (`id`, `lang`, `name`, `description`) VALUES ('contact', 'ja', 'お問い合わせ', 'お問い合わせフォーム');
INSERT INTO `page_lang` (`id`, `lang`, `name`, `description`) VALUES ('error', 'ja', '標準エラー', 'URLは、コントローラと一致していません');
INSERT INTO `page_lang` (`id`, `lang`, `name`, `description`) VALUES ('howto', 'ja', 'プロジェクトを作成します。', 'どのようにプロジェクトを作成します');
INSERT INTO `page_lang` (`id`, `lang`, `name`, `description`) VALUES ('legal', 'ja', '法的', '法的');
INSERT INTO `page_lang` (`id`, `lang`, `name`, `description`) VALUES ('maintenance', 'ja', 'メンテナンス', 'メンテナンス');
INSERT INTO `page_lang` (`id`, `lang`, `name`, `description`) VALUES ('privacy', 'ja', 'プライバシー', 'プライバシー');
INSERT INTO `page_lang` (`id`, `lang`, `name`, `description`) VALUES ('terms', 'ja', '規約と条件', '規約と条件');

-- page_node テーブルを更新
UPDATE `page_node` SET `name`='プロジェクトを作成します。', `description`='どのようにプロジェクトを作成します' WHERE `page`='howto' and`node`='goteo' and`lang`='ja';

-- データベースの skill, skill_lang テーブルの作り直し、データを入れなおし。
CREATE TABLE `skill` (
  `id` INT NOT NULL,
  `name` VARCHAR(50) NULL,
  `description` VARCHAR(50) NULL,
  `parent_skill_id` INT NULL DEFAULT 0,
  `order` INT NULL,
PRIMARY KEY (`id`));

CREATE TABLE `skill_lang` (
  `id` INT NOT NULL,
  `name` VARCHAR(50) NULL,
  `lang` VARCHAR(50) NULL,
  `description` VARCHAR(50) NULL,
PRIMARY KEY (`id`));

INSERT INTO `skill` (`id`, `name`, `description`, `parent_skill_id`, `order`) VALUES ('1', 'A Gyo', 'a gyo', '0', '1');
INSERT INTO `skill` (`id`, `name`, `description`, `parent_skill_id`, `order`) VALUES ('2', 'A', 'a', '1', '2');
INSERT INTO `skill` (`id`, `name`, `description`, `parent_skill_id`, `order`) VALUES ('3', 'I', 'i', '1', '3');
INSERT INTO `skill` (`id`, `name`, `description`, `parent_skill_id`, `order`) VALUES ('4', 'Ka Gyo', 'ka gyo', '0', '4');
INSERT INTO `skill` (`id`, `name`, `description`, `parent_skill_id`, `order`) VALUES ('5', 'Ka', 'ka', '4', '5');
INSERT INTO `skill` (`id`, `name`, `description`, `parent_skill_id`, `order`) VALUES ('6', 'Ki', 'ki', '4', '6');

INSERT INTO `skill_lang` (`id`, `name`, `lang`, `description`) VALUES ('1', 'あ行', 'ja', 'あの行');
INSERT INTO `skill_lang` (`id`, `name`, `lang`, `description`) VALUES ('2', 'あ', 'ja', 'あ');
INSERT INTO `skill_lang` (`id`, `name`, `lang`, `description`) VALUES ('3', 'い', 'ja', 'い');
INSERT INTO `skill_lang` (`id`, `name`, `lang`, `description`) VALUES ('4', 'か行', 'ja', 'か行');
INSERT INTO `skill_lang` (`id`, `name`, `lang`, `description`) VALUES ('5', 'か', 'ja', 'か');
INSERT INTO `skill_lang` (`id`, `name`, `lang`, `description`) VALUES ('6', 'き', 'ja', 'き');










