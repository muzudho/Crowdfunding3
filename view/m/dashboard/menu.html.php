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
?>
<div id="dashboard-menu">
    <?php foreach ($this['menu'] as $section=>$item) : ?>
    <? if($section === 'profile'): ?>
            <div class="viewport_dashboard">
                <ul class="flipsnap_dashboard flipsnap">
                <?php foreach ($item['options'] as $option=>$label) : ?>
                    <? if ($option != 'public'): ?>
                    <li class="option<?php if ($section == $this['section'] && $option == $this['option']) echo ' current'; ?>">
                        <a href="/dashboard/<?php echo $section; ?>/<?php echo $option; ?>"><?php echo $label; ?></a>
                    </li>
                    <? endif; ?>
                <?php endforeach; ?>
                </ul>
                <p class="controls">
                    <a class="db_prev">&lt;</a>
                    <a class="db_next">&gt;</a>
                </p>
            </div>
    <? endif; ?>
    <?php endforeach; ?>
</div>