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
    Goteo\Core\View;

$bodyClass = 'news';

$read_more = Text::get('regular-read_more');

// noticias
$news = $this['news'];

// paginacion
require_once 'library/pagination/pagination.php';

$pagedResults = new \Paginated($news, 7, isset($_GET['page']) ? $_GET['page'] : 1);

include 'view/prologue.html.php';
include 'view/header.html.php';
?>
<div id="sub-header-secondary">
    <div class="clearfix">
        <h2>GOTEO<span class="red">NEWS</span></h2>
        <?php echo new View('view/header/share.html.php') ?>
    </div>
</div>
<div id="main" class="threecols">
    <div id="news-content">
        <?php while ($content = $pagedResults->fetchPagedRow()) : ?>
            <div class="widget news-content-module">
                <a name="news<?php echo $content->id ?>" />
                <h3><?php echo $content->title; ?></h3>
                <blockquote><?php echo $content->description; ?></blockquote>
                <a href="<?php echo $content->url; ?>"><?php echo $read_more; ?></a>
            </div>
        <?php endwhile; ?>
        <ul id="pagination">
            <?php   $pagedResults->setLayout(new DoubleBarLayout());
                    echo $pagedResults->fetchPagedNavigation(); ?>
        </ul>
    </div>
</div>
<?php
include 'view/footer.html.php';
include 'view/epilogue.html.php';
?>