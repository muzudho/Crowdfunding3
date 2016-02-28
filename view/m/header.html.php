<?php
/*
 *  Copyright (C) 2012 Platoniq y Fundación Fuentes Abiertas (see README for details)
 *	This file is part of Goteo.
 *
 *  Goteo is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  Goteo is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with Goteo.  If not, see <http://www.gnu.org/licenses/agpl.txt>.
 *
 */

use Goteo\Library\Text,
    Goteo\Library\i18n\Lang;
//@NODESYS
?>

<div id="header" class="header">
    <h1><?php echo Text::get('regular-main-header'); ?></h1>
    <div class="nav_wrapper">
        <h1><a href="<?= LOCALGOOD_WP_BASE_URL ?>"><img src="/view/css/header/logo.png" alt=""/></a></h1>
        <div class="nav_inner viewport">
            <ul class="nav<?/*flipsnap*/?>">

                <li><a href="<?= LOCALGOOD_WP_BASE_URL ?>/earth_view/">課題を知る</a>
                    <ul class="sub">
                        <li><a href="<?= LOCALGOOD_WP_BASE_URL ?>/submit_subject/">課題を投稿する</a></li>
                        <li><a href="<?= LOCALGOOD_WP_BASE_URL ?>/subject/">課題を見る</a></li>
                    </ul>
                </li>
                <li><a href="<?= LOCALGOOD_WP_BASE_URL ?>/data/">データを見る</a></li>
                <li><a href="<?= LOCALGOOD_WP_BASE_URL ?>/posts_archive/">活動を知る</a></li>

                <li>
                    <a href="/user/login/">支援する</a>
                    <ul class="sub">
                        <li><a href="/">プロジェクト</a></li>
                        <li><a href="<?= LOCALGOOD_WP_BASE_URL ?>/skills/">スキルを活かす</a></li>
                    </ul>
                </li>
                <?php include 'view/m/header/menu.html.php' ?>
            </ul>
        </div>
    </div>
    <?/*
    $_current = '';
    foreach ($this['menu'] as $section=>$item) :
        if(($section == 'projects') || ($section == 'activity') || ($section == 'profile')):
            $_current = 'current';
        endif;
        echo $_current;
    endforeach;
    */?>
</div>
