<?php
/*
 *  Copyright (C) 2012 Platoniq y Fundación Fuentes Abiertas (see README for details)
 *  This file is part of Goteo.
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
    Goteo\Library\Template;

$list = $this['list'];

$templates = array(
    '33' => Text::_('Boletin'),
    '35' => Text::_('Testeo')
);

// por defecto cogemos la newsletter
$tpl = 33;

$template = Template::get($tpl);

?>
<div class="widget board">
    <p><?php echo Text::_('Seleccionar la plantilla. Se utilizará el contenido traducido, quizás quieras '); ?><a href="/admin/templates?group=massive" target="_blank"><?php echo Text::_('revisarlas'); ?></a></p>
    <p><strong><?php echo Text::_('NOTA'); ?>:</strong> <?php echo Text::_('con este sistema no se pueden añadir variables en el contenido, se genera el mismo contenido para todos los destinatarios.'); ?><br/>
        <?php echo Text::_('Para contenido personalizado hay que usar la funcionalidad'); ?> <a href="/admin/mailing" ><?php echo Text::_('Comunicaciones'); ?></a>.</p>

    <form action="/admin/newsletter/init" method="post" onsubmit="return confirm('<?php echo Text::_("El envio se activará automáticamente, seguimos?"); ?>');">

    <p>
        <label><?php echo Text::_('Plantillas masivas'); ?>:
            <select id="template" name="template" >
            <?php foreach ($templates as $tplId=>$tplName) : ?>
                <option value="<?php echo $tplId; ?>" <?php if ( $tplId == $tpl) echo 'selected="selected"'; ?>><?php echo $tplName; ?></option>
            <?php endforeach; ?>
            </select>
        </label>
    </p>
    <p>
        <label><input type="checkbox" name="test" value="1" checked="checked"/> <?php echo Text::_('Es una prueba (se envia a los destinatarios de pruebas)'); ?></label>
    </p>
        
    <p>
        <label><input type="checkbox" name="nolang" value="1" checked="checked"/>Solo en español (no tener en cuenta idioma preferido de usuario)</label>
    </p>
        
    <p>
        <input type="submit" name="init" value="<?php echo Text::_('Iniciar') ?>" />
    </p>

    </form>
</div>

<?php if (!empty($list)) : ?>
<div class="widget board">
    <table>
        <thead>
            <tr>
                <th class="detail_btn"></th>
                <th><?php echo Text::_('Fecha'); ?></th>
                <th class="title"><?php echo Text::_('Asunto'); ?></th>
                <th></th>
                <th></th>
                <th></th>
                <th><!-- Si no ves --></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($list as $item) : ?>
            <tr>
                <td><a href="/admin/newsletter/detail/<?php echo $item->id; ?>">[<?php echo Text::_("Detalles"); ?>]</a></td>
                <td><?php echo $item->date; ?></td>
                <td><?php echo $item->subject; ?></td>
                <td><?php echo $item->active ? '<span style="color:green;font-weight:bold;">' . Text::_('Activo') . '</span>' : '<span style="color:red;font-weight:bold;">' . Text::_('Inactivo') . '</span>'; ?></td>
                <td><?php echo $item->bloqued ? 'Bloqueado' : ''; ?></td>
                <td><a href="<?php echo $item->link; ?>" target="_blank">[<?php echo Text::_("Si no ves"); ?>]</a></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
