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


use Goteo\Library\Text;

$project = $this['project'];
$Data = $this['reportData'];

$desglose = array();
$goteo    = array();
$proyecto = array();
$estado   = array();
$usuario  = array();

$users = array();
foreach ($this['users'] as $user) {
    $amount = $users[$user->user]->amount + $user->amount;
    $users[$user->user] = (object) array(
        'name'   => $user->name,
        'user'   => $user->user,
        'amount' => $amount
    );
}

uasort($this['users'],
    function ($a, $b) {
        if ($a->name == $b->name) return 0;
        return ($a->name > $b->name) ? 1 : -1;
        }
    );

// recorremos los aportes
foreach ($this['invests'] as $invest) {

// para cada metodo acumulamos desglose, comision * 0.08, pago * 0.092
    $desglose[$invest->method] += $invest->amount;
    $goteo[$invest->method] += ($invest->amount * 0.08);
    $proyecto[$invest->method] += ($invest->amount * 0.92);
// para cada estado
    $estado[$invest->status]['total'] += $invest->amount;
    $estado[$invest->status][$invest->method] += $invest->amount;
// para cada usuario
    $usuario[$invest->user->id]['total'] += $invest->amount;
    $usuario[$invest->user->id][$invest->method] += $invest->amount;
// por metodo
    $usuario[$invest->method]['users'][$invest->user->id] = 1;
    $usuario[$invest->method]['invests']++;

}

?>
<style type="text/css">
    td {padding: 3px 10px;}
</style>
<div class="widget report">
    <p><?php echo Text::_('Informe de financiación de '); ?><strong><?php echo $project->name ?></strong> al d&iacute;a <?php echo date('d-m-Y') ?></p>
    <p><?php echo Text::_('Se encuentra en estado '); ?><strong><?php echo $this['status'][$project->status] ?></strong>
        <?php if ($project->round > 0) : ?>
            , en <?php echo $project->round . 'ª ronda' ?> y le quedan <strong><?php echo $project->days ?> d&iacute;as</strong> para finalizarla
        <?php endif; ?>
        .</p>
    <p><?php echo Text::_('El proyecto tiene un '); ?><strong>coste m&iacute;nimo de <?php echo \amount_format($project->mincost) ?> &euro;</strong>, un coste <strong>&oacute;ptimo de <?php echo \amount_format($project->maxcost) ?> &euro;</strong> y ahora mismo lleva <strong><?php echo Text::_('conseguidos '); ?><?php echo \amount_format($project->amount) ?> &euro;</strong>, lo que representa un <strong><?php echo \amount_format(($project->amount / $project->mincost * 100), 2, ',', '') . '%' ?></strong> sobre el m&iacute;nimo.</p>

    <h3><?php echo Text::_("Informe de aportes"); ?></h3>
    <p style="font-style:italic;"><?php echo Text::_("Cantidades en bruto (no se tiene en cuenta ejecuciones fallidas ni comisiones PayPal ni SaNostra)"); ?></p>

    <h4><?php echo Text::_("Por destinatario"); ?></h4>
    <table>
        <tr>
            <th><?php echo Text::_("M&eacute;todo"); ?></th>
            <th><?php echo Text::_("Cantidad"); ?></th>
            <th><?php echo Text::_('Goteo'); ?></th>
            <th><?php echo Text::_("Proyecto"); ?></th>
        </tr>
        <tr>
            <td><?php echo Text::_('Cash'); ?></td>
            <td style="text-align:right;"><?php echo \amount_format($desglose['cash']   ) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($goteo   ['cash'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($proyecto['cash'], 2) ?></td>
        </tr>
        <tr>
            <td><?php echo Text::_('TOTAL'); ?></td>
            <td style="text-align:right;"><?php echo \amount_format($desglose['cash'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($goteo   ['cash'], 2) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($proyecto['cash'], 2) ?></td>
        </tr>
    </table>

    <h3><?php echo Text::_("Por estado"); ?></h3>
    <table>
        <tr>
            <th><?php echo Text::_("Estado"); ?></th>
            <th><?php echo Text::_("Cantidad"); ?></th>
            <th><?php echo Text::_('Cash'); ?></th>
        </tr>
        <?php foreach ($this['investStatus'] as $id=>$label) : if (in_array($id, array('-1', '2', '4'))) continue;?>
        <tr>
            <td><?php echo $label ?></td>
            <td style="text-align:right;"><?php echo \amount_format($estado[$id]['total']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($estado[$id]['cash']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <h3><?php echo Text::_("Por cofinanciadores"); ?> (<?php echo count($this['users']) ?>)</h3>
    <table>
        <tr>
            <th><?php echo Text::_("Usuario"); ?></th>
            <th><?php echo Text::_("Cantidad"); ?></th>
            <th><?php echo Text::_('Cash'); ?></th>
        </tr>
        <?php foreach ($this['users'] as $user) : ?>
        <tr>
            <td><?php echo $user->name ?></td>
            <td style="text-align:right;"><?php echo \amount_format($user->amount, 0) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($usuario[$user->user]['cash']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<!-- información detallada apra tratar transferencias a proyectos -->
<a name="detail">&nbsp;</a>
<div class="widget report">
    <h3><?php echo Text::_("Informe de transacciones correctas"); ?></h3>
    <p style="font-style:italic;"><?php echo Text::_("Las incidencias NO se tienen en cuenta en el conteo de usuarios/operaciones ni en importes ni en comisiones ni en netos."); ?></p>

<?php if (!empty($Data['cash'])) : ?>
    <h4><?php echo Text::_('CASH'); ?></h4>
    <?php
        $users_ok = count($usuarios['cash']['users']);
        $invests_ok = $usuarios['cash']['invests'];
        $incidencias = 0;
        $correcto = $desglose['cash'] - $incidencias;
    ?>
    <table>
        <tr>
            <th></th>
            <th><?php echo Text::_("1a Ronda"); ?></th>
            <th><?php echo Text::_("2a Ronda"); ?></th>
            <th><?php echo Text::_('Total'); ?></th>
            <th></th>
        </tr>
        <tr>
            <th><?php echo Text::_("Nº Usuarios"); ?></th>
            <td style="text-align:right;"><?php echo count($Data['cash']['first']['users']) ?></td>
            <td style="text-align:right;"><?php echo count($Data['cash']['second']['users']) ?></td>
            <td style="text-align:right;"><?php echo count($Data['cash']['total']['users']) ?></td>
            <td></td>
        </tr>
        <tr>
            <th><?php echo Text::_("Nº Operaciones"); ?></th>
            <td style="text-align:right;"><?php echo $Data['cash']['first']['invests'] ?></td>
            <td style="text-align:right;"><?php echo $Data['cash']['second']['invests'] ?></td>
            <td style="text-align:right;"><?php echo $Data['cash']['total']['invests'] ?></td>
            <td></td>
        </tr>
        <tr>
            <th><?php echo Text::_("Incidencias"); ?></th>
            <td style="text-align:right;"><?php echo \amount_format($Data['cash']['first']['fail']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['cash']['second']['fail']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['cash']['total']['fail']) ?></td>
            <td><?php echo Text::_("Aportes mediante PayPal, TPV o de Capital Riego activos"); ?></td>
        </tr>
        <tr>
            <th><?php echo Text::_("Correcto"); ?></th>
            <td style="text-align:right;"><?php echo \amount_format($Data['cash']['first']['amount']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['cash']['second']['amount']) ?></td>
            <td style="text-align:right;"><?php echo \amount_format($Data['cash']['total']['amount']) ?></td>
            <td><?php echo Text::_("Aportes de cash anteriores a la campa&ntilde;a"); ?></td>
        </tr>
    </table>
<?php endif; ?>

<?php if (!empty($Data['note'])) : ?>
    <h4><?php echo Text::_("Notas"); ?></h4>
    <p><?php echo implode('<br />- ', $Data['note']) ?></p>
<?php endif; ?>
</div>