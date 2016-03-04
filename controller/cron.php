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

namespace Goteo\Controller {

    use Goteo\Model,
        Goteo\Library\Text,
        Goteo\Library\Feed,
        Goteo\Library\Template,
        Goteo\Library\Mail;

    class Cron extends \Goteo\Core\Controller {
        
        public function index () {
            die('bad request');
        }

        /*
         *  Proceso que ejecuta los cargos, cambia estados, lanza eventos de cambio de ronda
         */
        /**
         * @throws \Goteo\Core\Exception
         */
        public function execute () {

            if (!\defined('CRON_EXEC')) {
                @mail(\GOTEO_FAIL_MAIL, 'Se ha lanzado MANUALMENTE el cron '. __FUNCTION__ .' en ' . SITE_URL,
                    'Se ha lanzado manualmente el cron '. __FUNCTION__ .' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Usuario '. $_SESSION['user']->id);
               echo 'Lanzamiento manual a las ' . date ('H:i:s') . ' <br />';
            } else {
                echo 'Lanzamiento automatico a las ' . date ('H:i:s') . ' <br />';
            }
            
            // a ver si existe el bloqueo
            $block_file = GOTEO_PATH.'logs/cron-'.__FUNCTION__.'.block';
            if (file_exists($block_file)) {
                echo 'Ya existe un archivo de log '.date('Ymd').'_'.__FUNCTION__.'.log<br />';
                $block_content = \file_get_contents($block_file);
                echo 'El contenido del bloqueo es: '.$block_content;
                // lo escribimos en el log
                $log_file = GOTEO_PATH.'logs/cron/'.date('Ymd').'_'.__FUNCTION__.'.log';
                \file_put_contents($log_file, \ob_get_contents(), FILE_APPEND);
                \chmod($log_file, 0777);
                /*
                @mail(\GOTEO_FAIL_MAIL, 'Cron '. __FUNCTION__ .' bloqueado en ' . SITE_URL,
                    'Se ha encontrado con que el cron '. __FUNCTION__ .' está bloqueado el '.date('d-m-Y').' a las ' . date ('H:i:s') . '
                        El contenido del bloqueo es: '. $block_content);
                 */
                die;
            } else {
                $block = 'Bloqueo del '.$block_file.' activado el '.date('d-m-Y').' a las '.date ('H:i:s').'<br />';
                if (\file_put_contents($block_file, $block, FILE_APPEND)) {
                    \chmod($block_file, 0777);
                    echo $block;
                } else {
                    echo 'No se ha podido crear el archivo de bloqueo<br />';
                    @mail(\GOTEO_FAIL_MAIL, 'Cron '. __FUNCTION__ .' no se ha podido bloquear en ' . SITE_URL,
                        'No se ha podido crear el archivo '.$block_file.' el '.date('d-m-Y').' a las ' . date ('H:i:s'));
                }
            }
            echo '<hr />';
            
            // debug para supervisar en las fechas clave
//            $debug = ($_GET['debug'] == 'debug') ? true : false;
            $debug = true;

            // revision de proyectos: dias, conseguido y cambios de estado
            // proyectos en campaña,
            // (publicados hace más de 40 días que no tengan fecha de pase)
            // o (publicados hace mas de 80 días que no tengan fecha de exito)
            $projects = Model\Project::getActive();

            if ($debug) echo 'Comenzamos con los proyectos en campaña (esto está en '.\LANG.')<br /><br />';

            foreach ($projects as $project) {

                // プロジェクト期間取得
                // $days1r = 1r (passed - published)
                // $days2r = 2r (success - passed)

                if ($debug) echo 'Proyecto '.$project->name.'<br />';

                // a ver si tiene cuenta paypal
                $projectAccount = Model\Project\Account::get($project->id);
/*
                if (empty($projectAccount->paypal)) {

                    if ($debug) echo 'No tiene cuenta PayPal<br />';

                    // Evento Feed solamente si automático
                    if (\defined('CRON_EXEC')) {
                        $log = new Feed();
                        $log->setTarget($project->id);
                        $log->populate('proyecto sin cuenta paypal (cron)', '/admin/projects',
                            \vsprintf('El proyecto %s aun no ha puesto su %s !!!', array(
                                Feed::item('project', $project->name, $project->id),
                                Feed::item('relevant', 'cuenta PayPal')
                        )));
                        $log->doAdmin('project');
                        unset($log);

                        // mail de aviso
                        $mailHandler = new Mail();
                        $mailHandler->to = \GOTEO_CONTACT_MAIL;
                        $mailHandler->toName = 'Goteo.org';
                        $mailHandler->subject = 'El proyecto '.$project->name.' no tiene cuenta PayPal';
                        $mailHandler->content = 'Hola Goteo, el proyecto '.$project->name.' no tiene cuenta PayPal y el proceso automatico no podrá tratar los preaprovals al final de ronda.';
                        $mailHandler->html = false;
                        $mailHandler->template = null;
                        $mailHandler->send();
                        unset($mailHandler);

                        $task = new Model\Task();
                        $task->node = \GOTEO_NODE;
                        $task->text = "Poner la cuenta PayPal al proyecto <strong>{$project->name}</strong> urgentemente!";
                        $task->url = "/admin/projects/accounts/{$project->id}";
                        $task->done = null;
                        $task->saveUnique();

                    }

                }
*/

                $log_text = null;

                if ($debug) echo 'Minimo: '.$project->mincost.' &yen; <br />';
                
                $execute = false;
                $cancelAll = false;

                if ($debug) echo 'Obtenido: '.$project->amount.' &yen;<br />';

                // porcentaje alcanzado
                if ($project->mincost > 0) {
                    $per_amount = \floor(($project->amount / $project->mincost) * 100);
                } else {
                    $per_amount = 0;
                }
                if ($debug) echo 'Ha alcanzado el '.$per_amount.' &#37; del minimo<br />';

                // los dias que lleva el proyecto  (ojo que los financiados llevaran mas de 80 dias)
                $days = $project->daysActive();

                // デフォルト値は1R、2Rともに40日
                $period_1r = !empty($project->period_1r) ? $project->period_1r : 40;
                $period_2r = !empty($project->period_2r) ? $project->period_2r : 40;
                $period_total = $period_1r + $period_2r;

                if ($debug) echo 'Lleva '.$days.'  dias desde la publicacion<br />';

                /* Verificar si enviamos aviso */
                $rest = $project->days;
                $round = $project->round;
                if ($debug) echo 'Quedan '.$rest.' dias para el final de la '.$round.'a ronda<br />';


                // a los 5, 3, 2, y 1 dia para finalizar ronda
                if ($round > 0 && in_array((int) $rest, array(5, 3, 2, 1))) {
                    if ($debug) echo 'Feed publico cuando quedan 5, 3, 2, 1 dias<br />';

                    // Evento Feed solo si ejecucion automática
                    if (\defined('CRON_EXEC')) {
                        $log = new Feed();
                        $log->setTarget($project->id);
                        $log->populate('proyecto próximo a finalizar ronda (cron)', '/admin/projects',
                            Text::html('feed-project_runout',
                                Feed::item('project', $project->name, $project->id),
                                $rest,
                                $round
                        ));
                        $log->doAdmin('project');

                        // evento público
                        $log->title = $project->name;
                        $log->url = null;
                        $log->doPublic('projects');

                        unset($log);
                    }
                }

                //  (financiado a los 80 o cancelado si a los 40 no llega al minimo)
                // si ha llegado a los 40 dias: mínimo-> ejecutar ; no minimo proyecto y todos los preapprovals cancelados
                // (Funded at 80 or canceled if the 40 does not reach the minimum) 
                // If it has reached 40 days: minimum-> execute; no minimum project and canceled all preapprovals
                if ($days >= $period_1r) {
                    // si no ha alcanzado el mínimo, pasa a estado caducado
                    // If you have not reached the minimum, goes into Expired
                    if ($project->amount < $project->mincost) {
                        if ($debug) echo 'Ha llegado a los 40 dias de campaña sin conseguir el minimo, no pasa a segunda ronda<br />';

                        echo $project->name . ': ha recaudado ' . $project->amount . ', '.$per_amount.'% de ' . $project->mincost . '/' . $project->maxcost . '<br />';
                        echo 'No ha conseguido el minimo, cancelamos todos los aportes y lo caducamos:';
                        $cancelAll = true;
                        $errors = array();
                        if ($project->fail($errors)) {
                            $log_text = Text::_('El proyecto %s ha %s obteniendo %s');
                        } else {
                            @mail(\GOTEO_FAIL_MAIL,
                                'Fallo al archivar ' . SITE_URL,
                                'Fallo al marcar el proyecto '.$project->name.' como archivado ' . implode(',', $errors));
                            echo 'ERROR::' . implode(',', $errors);
                            $log_text = Text::_('El proyecto %s ha fallado al, %s obteniendo %s');
                        }
                        echo '<br />';
                        
                        // Evento Feed solo si ejecucion automatica
                        if (\defined('CRON_EXEC')) {
                            $log = new Feed();
                            $log->setTarget($project->id);
                            $log->populate('proyecto archivado (cron)', '/admin/projects',
                                \vsprintf($log_text, array(
                                    Feed::item('project', $project->name, $project->id),
                                    Feed::item('relevant', 'caducado sin éxito'),
                                    Feed::item('money', $project->amount.' &yen; ('.$per_amount.'&#37;) de aportes sobre minimo')
                            )));
                            $log->doAdmin('project');

                            // evento público
                            $log->populate($project->name, null,
                                Text::html('feed-project_fail',
                                    Feed::item('project', $project->name, $project->id),
                                    $project->amount,
                                    $per_amount
                            ));
                            $log->doPublic('projects');

                            unset($log);

                            //Email de proyecto fallido al autor
                            Cron\Send::toOwner('fail', $project);
                            //Email de proyecto fallido a los inversores
                            Cron\Send::toInvestors('fail', $project);
                        }
                        
                        echo '<br />';
                    } else {
                        // tiene hasta 80 días para conseguir el óptimo (o más)
                        // Has up to 80 days for optimum (or more)
                        if ($days >= $period_total) {
                            if ($debug) echo 'Ha llegado a los 80 dias de campaña (final de segunda ronda)<br />';

                            echo $project->name . ': ha recaudado ' . $project->amount . ', '.$per_amount.'% de ' . $project->mincost . '/' . $project->maxcost . '<br />';
                            echo 'Ha llegado a los 80 días: financiado. ';

                            $execute = true; // ejecutar los cargos de la segunda ronda

                            $errors = array();
                            if ($project->succeed($errors)) {
                                $log_text = Text::_('El proyecto %s ha sido %s obteniendo %s');
                            } else {
                                @mail(\GOTEO_FAIL_MAIL,
                                    'Fallo al marcar financiado ' . SITE_URL,
                                    'Fallo al marcar el proyecto '.$project->name.' como financiado ' . implode(',', $errors));
                                echo 'ERROR::' . implode(',', $errors);
                                $log_text = Text::_('El proyecto %s ha fallado al ser, %s obteniendo %s');
                            }

                            // Evento Feed y mails solo si ejecucion automatica
                            if (\defined('CRON_EXEC')) {
                                $log = new Feed();
                                $log->setTarget($project->id);
                                $log->populate('proyecto supera segunda ronda (cron)', '/admin/projects',
                                    \vsprintf($log_text, array(
                                        Feed::item('project', $project->name, $project->id),
                                        Feed::item('relevant', 'financiado'),
                                        Feed::item('money', $project->amount.' &yen; ('.\round($per_amount).'%) de aportes sobre minimo')
                                )));
                                $log->doAdmin('project');

                                // evento público
                                $log->populate($project->name, null, Text::html('feed-project_finish',
                                                Feed::item('project', $project->name, $project->id),
                                                $project->amount,
                                                \round($per_amount)
                                                ));
                                $log->doPublic('projects');
                                unset($log);

                                //Email de proyecto final segunda ronda al autor
                                Cron\Send::toOwner('r2_pass', $project);
                                //Email de proyecto final segunda ronda a los inversores
                                Cron\Send::toInvestors('r2_pass', $project);

                                // Tareas para gestionar
                                // calculamos fecha de passed+90 días
                                $passtime = strtotime($project->passed);
                                $limsec = date('d/m/Y', \mktime(0, 0, 0, date('m', $passtime), date('d', $passtime)+89, date('Y', $passtime)));

                                /*
                                 * Ya no hacemos pagos secundarios mediante sistema
                                $task = new Model\Task();
                                $task->node = \GOTEO_NODE;
                                $task->text = "Hacer los pagos secundarios al proyecto <strong>{$project->name}</strong> antes del día <strong>{$limsec}</strong>";
                                $task->url = "/admin/accounts/?projects={$project->id}";
                                $task->done = null;
                                $task->save();
                                 */

                                // y preparar contrato
                                $task = new Model\Task();
                                $task->node = \GOTEO_NODE;
                                $task->text = date('d/m/Y').": Enviar datos contrato <strong>{$project->name}</strong>, {$project->user->name}";
                                //@TODO enlace a gestión de contrato
                                $task->url = "/admin/projects?proj_name={$project->name}";
                                $task->done = null;
                                $task->saveUnique();
                                
                                // + mail a mercè
                                @mail(\GOTEO_CONTACT_MAIL,
                                    'Preparar contrato ' . $project->name,
                                    'El proyecto '.$project->name.' ha pasado la primera ronda, enviarle los datos de contrato. Se ha creado una tarea para esto.');
                            }

                            echo '<br />';
                        } elseif (empty($project->passed)) {

                            if ($debug) echo 'Ha llegado a los 40 dias de campaña, pasa a segunda ronda<br />';

                            echo $project->name . ': ha recaudado ' . $project->amount . ', '.$per_amount.'% de ' . $project->mincost . '/' . $project->maxcost . '<br />';
                            echo 'El proyecto supera la primera ronda: marcamos fecha';

                            $execute = true; // ejecutar los cargos de la primera ronda

                            $errors = array();
                            if ($project->passed($errors)) {
                                // se crea el registro de contrato
                                // Recording contract is created
/*
                                if (Model\Contract::create($project->id, $errors)) {
                                    echo ' -> Ok:: se ha creado el registro de contrato';
                                } else {
                                    @mail(\GOTEO_FAIL_MAIL,
                                        'Fallo al crear registro de contrato ' . SITE_URL,
                                        'Fallo al crear registro de contrato para el proyecto '.$project->name.': ' . implode(',', $errors));
                                    echo ' -> semi-Ok: se ha actualiuzado el estado del proyecto pero ha fallado al crear el registro de contrato. ERROR: ' . implode(',', $errors);
                                }
*/
                            } else {
                                @mail(\GOTEO_FAIL_MAIL,
                                    'Fallo al marcar fecha de paso a segunda ronda ' . SITE_URL,
                                    'Fallo al marcar la fecha de paso a segunda ronda para el proyecto '.$project->name.': ' . implode(',', $errors));
                                echo ' -> ERROR::' . implode(',', $errors);
                            }

                            echo '<br />';

                            // Evento Feed solo si ejecucion automatica
                            if (\defined('CRON_EXEC')) {
                                $log = new Feed();
                                $log->setTarget($project->id);
                                $log->populate('proyecto supera primera ronda (cron)', '/admin/projects', \vsprintf('El proyecto %s %s en segunda ronda obteniendo %s', array(
                                    Feed::item('project', $project->name, $project->id),
                                    Feed::item('relevant', 'continua en campaña'),
                                    Feed::item('money', $project->amount.' &yen; ('.\number_format($per_amount, 2).'%) de aportes sobre minimo')
                                )));
                                $log->doAdmin('project');

                                // evento público
                                $log->populate($project->name, null,
                                    Text::html('feed-project_goon',
                                        Feed::item('project', $project->name, $project->id),
                                        $project->amount,
                                        \round($per_amount)
                                ));
                                $log->doPublic('projects');
                                unset($log);

                                if ($debug) echo 'Email al autor y a los cofinanciadores<br />';
                                // Email de proyecto pasa a segunda ronda al autor
                                Cron\Send::toOwner('r1_pass', $project);

                                //Email de proyecto pasa a segunda ronda a los inversores
                                Cron\Send::toInvestors('r1_pass', $project);
                                
                                // Tarea para hacer los pagos
                                $task = new Model\Task();
                                $task->node = \GOTEO_NODE;
                                $task->text = date('d/m/Y').": Pagar a <strong>{$project->name}</strong>, {$project->user->name}";
                                $task->url = "/admin/projects/report/{$project->id}";
                                $task->done = null;
                                $task->saveUnique();
                                
                                // + mail a susana
                                @mail('susana@goteo.org',
                                    'Pagar al proyecto ' . $project->name,
                                    'El proyecto '.$project->name.' ha terminado la segunda ronda, hacer los pagos. Se ha creado una tarea para esto.');
                            }
                            
                        } else {
                            if ($debug) echo 'Lleva más de 40 dias de campaña, debe estar en segunda ronda con fecha marcada<br />';
                            if ($debug) echo $project->name . ': lleva recaudado ' . $project->amount . ', '.$per_amount.'% de ' . $project->mincost . '/' . $project->maxcost . ' y paso a segunda ronda el '.$project->passed.'<br />';
                        }
                    }
                }

                // si hay que ejecutar o cancelar
                if ($cancelAll || $execute) {
                    if ($debug) echo '::::::Comienza tratamiento de aportes:::::::<br />';
                    if ($debug) echo 'Execute=' . (string) $execute . '  CancelAll=' . (string) $cancelAll . '<br />';
                    // tratamiento de aportes penddientes
                    $query = \Goteo\Core\Model::query("
                        SELECT  *
                        FROM  invest
                        WHERE   invest.project = ?
                        AND     (invest.status = 0
                            OR (invest.method = 'tpv'
                                AND invest.status = 1
                            )
                            OR (invest.method = 'cash'
                                AND invest.status = 1
                            )
                        )
                        AND (invest.campaign IS NULL OR invest.campaign = 0)
                        ", array($project->id));
                    $project->invests = $query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\Invest');

                    foreach ($project->invests as $key=>$invest) {
                        $errors = array();
                        $log_text = null;
                        
                        $userData = Model\User::getMini($invest->user);

                        if ($invest->invested == date('Y-m-d')) {
                            if ($debug) echo 'Aporte ' . $invest->id . ' es de hoy.<br />';
                        } elseif ($invest->method != 'cash' && empty($invest->preapproval)) {
                            //si no tiene preaproval, cancelar
                            //echo 'Aporte ' . $invest->id . ' cancelado por no tener preapproval.<br />';
                            //$invest->cancel();
                            //Model\Invest::setDetail($invest->id, 'no-preapproval', 'Aporte cancelado porque no tiene preapproval. Proceso cron/execute');
                            //continue;
                        }

                        if ($cancelAll) {
                            if ($debug) echo 'Cancelar todo<br />';

                            switch ($invest->method) {
                                // ここに、支払い方法別でキャンセル処理を書いてください。
                                case 'axes':
                                    if ($invest->cancel(true)) {
                                        $log_text = Text::_("Contribution is canceled");
                                    } else{
                                        $log_text = Text::_("Failed to cancel");
                                    }
                                    break;
                                case 'cash':
                                    if ($invest->cancel(true)) {
                                        $log_text = Text::_("Se ha cancelado aporte manual de %s de %s (id: %s) al proyecto %s del dia %s");
                                    } else{
                                        $log_text = Text::_("Ha fallado al cancelar el aporte manual de %s de %s (id: %s) al proyecto %s del dia %s. ");
                                    }
                                    break;
                            }

                            // Evento Feed admin
                            $log = new Feed();
                            $log->setTarget($project->id);
                            $log->populate('Preapproval cancelado por proyecto archivado (cron)', '/admin/invests', \vsprintf($log_text, array(
                                Feed::item('user', $userData->name, $userData->id),
                                Feed::item('money', $invest->amount.' &yen;'),
                                Feed::item('system', $invest->id),
                                Feed::item('project', $project->name, $project->id),
                                Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
                            )));
                            $log->doAdmin();
                            unset($log);

                            echo 'Aporte '.$invest->id.' cancelado por proyecto caducado.<br />';
                            $invest->setStatus('4');
                            Model\Invest::setDetail($invest->id, 'project-expired', 'Aporte marcado como caducado porque el proyecto no ha tenido exito. Proceso cron/execute');

                            continue;
                        }

                        // si hay que ejecutar
                        if ($execute && empty($invest->payment)) {
                            if ($debug) echo 'Ejecutando aporte '.$invest->id.' ['.$invest->method.']';

                            switch ($invest->method) {
                                /* cronではなく管理画面から手動で(admin/invests/dopay)
                                case 'axes':
                                    $err = array();
                                    if ($invest->setPayment(date("YmdHis"))) {
                                        $invest->setStatus(1);
                                        $log_text = Text::_("Has been executed under its %s %s contribution via Axes (id:%s) to the project %s %s of the day");
                                        if ($debug) echo ' -> Ok';
                                        Model\Invest::setDetail($invest->id, 'executed', 'Preapproval has been executed, has initiated the chained payment. Process cron / execute');
                                        // si era incidencia la desmarcamos
                                        if ($invest->issue) {
                                            Model\Invest::unsetIssue($invest->id);
                                            Model\Invest::setDetail($invest->id, 'issue-solved', 'The incidence has been resolved upon success by the automatic process');
                                        }
                                    }
                                    break;*/
                                case 'cash':
                                    // los cargos manuales no los modificamos
                                    if ($debug) echo ' Cash, nada que hacer -> Ok';
                                    break;
                            }
                            if ($debug) echo '<br />';

                            if (!empty($log_text)) {
                                // Evento Feed
                                $log = new Feed();
                                $log->setTarget($project->id);
                                $log->populate('Cargo ejecutado (cron)', '/admin/invests', \vsprintf($log_text, array(
                                    Feed::item('user', $userData->name, $userData->id),
                                    Feed::item('money', $invest->amount.' &yen;'),
                                    Feed::item('system', $invest->id),
                                    Feed::item('project', $project->name, $project->id),
                                    Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
                                )));
                                $log->doAdmin();
                                if ($debug) echo $log->html . '<br />';
                                unset($log);
                            }

                            if ($debug) echo 'Aporte '.$invest->id.' tratado<br />';
                        }

                    }

                    if ($debug) echo '::Fin tratamiento aportes<br />';
                }

                if ($debug) echo 'Fin tratamiento Proyecto '.$project->name.'<hr />';
            }


            // checkeamos campañas activas
            //$campaigns = Model\Call::getActive(4);
            $campaigns = array();
            foreach ($campaigns as $campaign) {
                $errors = array();

                // tiene que tener presupuesto
                if (empty($campaign->amount)) {
                    continue;
                }

                // si le quedan cero
                // -> terminar la campaña exitosamente
                if ($campaign->rest == 0 && !empty($campaign->amount))  {
                    echo 'La convocatoria '.$campaign->name.': ';
                    if ($campaign->checkSuccess($errors)) {
                        if ($campaign->succeed($errors)) {
                            echo 'Ha terminado exitosamente.<br />';

                            $log = new Feed();
                            $log->setTarget($campaign->id, 'call');
                            $log->unique = true;
                            $log->populate('Campaña terminada (cron)', '/admin/calls/'.$campaign->id.'?rest='.$amount,
                                \vsprintf('La campaña %s ha terminado con exito', array(
                                    Feed::item('call', $campaign->name, $campaign->id))
                                ));
                            $log->doAdmin('call');
                            $log->populate($campaign->name, '/call/'.$campaign->id.'?rest='.$amount,
                                \vsprintf('La campaña %s ha terminado con éxito', array(
                                    Feed::item('call', $campaign->name, $campaign->id))
                                ), $call->logo);
                            $log->doPublic('projects');
                            unset($log);

                        } else {
                            echo 'Ha fallado al marcar exitosa.<br />'.implode('<br />', $errors);
                        }
                    } else {
                        echo 'Le Queda algun proyecto en primera ronda.<br />';
                    }
                }

            }

            // desbloqueamos
            if (unlink($block_file)) {
                echo 'Cron '. __FUNCTION__ .' desbloqueado<br />';
            } else {
                echo 'ALERT! Cron '. __FUNCTION__ .' no se ha podido desbloquear<br />';
                if(file_exists($block_file)) {
                    echo 'El archivo '.$block_file.' aun existe!<br />';
                } else {
                    echo 'No hay archivo de bloqueo '.$block_file.'!<br />';
                }
            }
            
            
            // recogemos el buffer para grabar el log
            $log_file = GOTEO_PATH.'logs/cron/'.date('Ymd').'_'.__FUNCTION__.'.log';
            \file_put_contents($log_file, \ob_get_contents(), FILE_APPEND);
            \chmod($log_file, 0777);
        }


        /*
         *  Proceso que verifica si los preapprovals han sido coancelados
         *   Solamente trata transacciones paypal pendientes de proyectos en campaña
         *
         */
        public function verify () {
            if (!\defined('CRON_EXEC')) {
                @mail(\GOTEO_FAIL_MAIL, 'Se ha lanzado el cron '. __FUNCTION__ .' en ' . SITE_URL,
                    'Se ha lanzado manualmente el cron '. __FUNCTION__ .' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Usuario '. $_SESSION['user']->id);
               echo 'Lanzamiento manual<br />';
            } else {
               echo 'Lanzamiento automatico<br />';
            }
            
            $debug = (isset($_GET['debug']) && $_GET['debug'] == 'debug') ? true : false;
            if ($debug) echo 'Modo debug activado<br />';
            
            // lanzamos subcontrolador
            Cron\Verify::process($debug);
            // también el tratamiento de geologin
            Cron\Geoloc::process($debug);
            
            // recogemos el buffer para grabar el log
            /*
            $log_file = GOTEO_PATH.'logs/cron/'.date('Ymd').'_'.__FUNCTION__.'.log';
            \file_put_contents($log_file, \ob_get_contents(), FILE_APPEND);
            \chmod($log_file, 0777);
            */
            die();
        }

        /*
         *  Proceso que limpia la tabla de imágenes
         * y también limpia el directorio
         *
         */
        public function cleanup () {
            if (\defined('CRON_EXEC')) {
                @mail(\GOTEO_FAIL_MAIL, 'Se ha lanzado el cron '. __FUNCTION__ .' en ' . SITE_URL,
                    'Se intentaba lanzar automáticamente el cron '. __FUNCTION__ .' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Usuario '. $_SESSION['user']->id);
               die;
            } else {
                Cron\Cleanup::process();
                die();
            }
        }

        /*
         *  Proceso para tratar los geologins
         *
         */
        public function geoloc () {
            // no necesito email de aviso por el momento
            /*
            if (!\defined('CRON_EXEC')) {
                @mail(\GOTEO_FAIL_MAIL, 'Se ha lanzado el cron '. __FUNCTION__ .' en ' . SITE_URL,
                    'Se ha lanzado manualmente el cron '. __FUNCTION__ .' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Usuario '. $_SESSION['user']->id);
               echo 'Lanzamiento manual<br />';
            } else {
               echo 'Lanzamiento automatico<br />';
            }
            */
            
            // lanzamos subcontrolador
            Cron\Geoloc::process();
            
            // Por el momento no grabamos log de esto, lo lanzamos manual
            /*
            $log_file = GOTEO_PATH.'logs/cron/'.date('Ymd').'_'.__FUNCTION__.'.log';
            \file_put_contents($log_file, \ob_get_contents(), FILE_APPEND);
            \chmod($log_file, 0777);
             */
            
            die();
        }

        /*
         * Realiza los pagos secundarios al proyecto
         *
         * Esto son los aportes de tipo paypal, ejecutados (status 1), que tengan payment code
         *
         */
        public function dopay ($project) {
            die('Ya no realizamos pagos secundarios mediante sistema');
            if (\defined('CRON_EXEC')) {
                die('Este proceso no necesitamos lanzarlo automaticamente');
            }

            @mail(\GOTEO_FAIL_MAIL, 'Se ha lanzado el cron '. __FUNCTION__ .' en ' . SITE_URL,
                'Se ha lanzado manualmente el cron '. __FUNCTION__ .' para el proyecto '.$project.' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Usuario '. $_SESSION['user']->id);
            
            // a ver si existe el bloqueo
            $block_file = GOTEO_PATH.'logs/cron-'.__FUNCTION__.'.block';
            if (file_exists($block_file)) {
                echo 'Ya existe un archivo de log '.date('Ymd').'_'.__FUNCTION__.'.log<br />';
                $block_content = \file_get_contents($block_file);
                echo 'El contenido del bloqueo es: '.$block_content;
                // lo escribimos en el log
                $log_file = GOTEO_PATH.'logs/cron/'.date('Ymd').'_'.__FUNCTION__.'.log';
                \file_put_contents($log_file, \ob_get_contents(), FILE_APPEND);
                \chmod($log_file, 0777);
                /*
                @mail(\GOTEO_FAIL_MAIL, 'Cron '. __FUNCTION__ .' bloqueado en ' . SITE_URL,
                    'Se ha encontrado con que el cron '. __FUNCTION__ .' está bloqueado el '.date('d-m-Y').' a las ' . date ('H:i:s') . '
                        El contenido del bloqueo es: '. $block_content);
                 */
                die;
            } else {
                $block = 'Bloqueo '.$block_file.' activado el '.date('d-m-Y').' a las '.date ('H:i:s').'<br />';
                if (\file_put_contents($block_file, $block, FILE_APPEND)) {
                    \chmod($block_file, 0777);
                    echo $block;
                } else {
                    echo 'No se ha podido crear el archivo de bloqueo<br />';
                    @mail(\GOTEO_FAIL_MAIL, 'Cron '. __FUNCTION__ .' no se ha podido bloquear en ' . SITE_URL,
                        'No se ha podido crear el archivo '.$block_file.' el '.date('d-m-Y').' a las ' . date ('H:i:s'));
                }
            }
            
            $projectData = Model\Project::getMini($project);

            // necesitamos la cuenta del proyecto y que sea la misma que cuando el preapproval
            $projectAccount = Model\Project\Account::get($project);

            if (empty($projectAccount->paypal)) {
                // iniciamos mail
                $mailHandler = new Mail();
                $mailHandler->to = \GOTEO_MAIL;
                $mailHandler->toName = 'Goteo.org';
                $mailHandler->subject = 'El proyecto '.$projectData->name.' no tiene cuenta PayPal';
                $mailHandler->content = 'Hola Goteo, el proyecto '.$projectData->name.' no tiene cuenta PayPal y se estaba intentando realizar pagos secundarios.';
                $mailHandler->html = false;
                $mailHandler->template = null;
                $mailHandler->send();
                unset($mailHandler);
                
                die('El proyecto '.$projectData->name.' no tiene la cuenta PayPal!!');
            }

            // tratamiento de aportes pendientes
            $query = Model\Project::query("
                SELECT  *
                FROM  invest
                WHERE   invest.status = 1
                AND     invest.method = 'paypal'
                AND     invest.project = ?
                ", array($project));
            $invests = $query->fetchAll(\PDO::FETCH_CLASS, '\Goteo\Model\Invest');

            echo 'Vamos a tratar ' . count($invests) . ' aportes para el proyecto '.$projectData->name.'<br />';

            foreach ($invests as $key=>$invest) {
                $errors = array();

                $userData = Model\User::getMini($invest->user);
                echo 'Tratando: Aporte (id: '.$invest->id.') de '.$userData->name.' ['.$userData->email.']<br />';

                echo 'Fallo al pagar al proyecto el aporte (id: '.$invest->id.'). Ver los detalles en la <a href="/admin/accounts/details/'.$invest->id.'">gestion de transacciones</a><br />' . implode('<br />', $errors);
                $log_text = Text::_("Ha fallado al realizar el pago de %s PayPal al proyecto %s por el aporte de %s (id: %s) del dia %s");
                Model\Invest::setDetail($invest->id, 'pay-failed', 'Fallo al realizar el pago secundario: ' . implode('<br />', $errors) . '. Proceso cron/doPay');

                // Evento Feed
                $log = new Feed();
                $log->setTarget($projectData->id);
                $log->populate('Pago al proyecto encadenado-secundario (cron)', '/admin/accounts',
                    \vsprintf($log_text, array(
                    Feed::item('money', $invest->amount.' &yen;'),
                    Feed::item('project', $projectData->name, $project),
                    Feed::item('user', $userData->name, $userData->id),
                    Feed::item('system', $invest->id),
                    Feed::item('system', date('d/m/Y', strtotime($invest->invested)))
                )));
                $log->doAdmin();
                unset($log);

                echo '<hr />';
            }

            // desbloqueamos
            if (unlink($block_file)) {
                echo 'Cron '. __FUNCTION__ .' desbloqueado<br />';
            } else {
                echo 'ALERT! Cron '. __FUNCTION__ .' no se ha podido desbloquear<br />';
                if(file_exists($block_file)) {
                    echo 'El archivo '.$block_file.' aun existe!<br />';
                } else {
                    echo 'No hay archivo de bloqueo '.$block_file.'!<br />';
                }
            }
            
            // recogemos el buffer para grabar el log
            $log_file = GOTEO_PATH.'logs/cron/'.date('Ymd').'_'.__FUNCTION__.'.log';
            \file_put_contents($log_file, \ob_get_contents(), FILE_APPEND);
            \chmod($log_file, 0777);
        }



        /**
         *  Proceso para enviar avisos a los autores segun
         *  - Que lleven 3 meses sin publicar actualizaciones, envia cada mes
         *  - Que lleven 3 meses sin decir nada (?), envia cada 15 dias
         *  - Que hayan pasado dos meses desde que se dio por financiado, cada 15 dias
         *
         *  teiene en cuenta que se envía cada tantos días
         */
        
        public function daily () {
            if (!\defined('CRON_EXEC')) {
                @mail(\GOTEO_FAIL_MAIL, 'Se ha lanzado el cron '. __FUNCTION__ .' en ' . SITE_URL,
                    'Se ha lanzado manualmente el cron '. __FUNCTION__ .' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Usuario '. $_SESSION['user']->id);
//                die('Este proceso no necesitamos lanzarlo manualmente');
            }
            
            // temporalmente debug fijo (quitarlo al quitar monitorización)
//            $debug = (isset($_GET['debug']) && $_GET['debug'] == 'debug') ? true : false;
            $debug = true;
            
            if ($debug) echo 'Modo debug activado<br />';
            
            // subcontrolador Auto-tips
            Cron\Daily::Projects($debug);

            // subcontrolador progreso convocatorias
//            Cron\Daily::Calls($debug);
            
            
            if ($debug) {
                // recogemos el buffer para grabar el log
                $log_file = GOTEO_PATH.'logs/cron/'.date('Ymd').'_'.__FUNCTION__.'.log';
                \file_put_contents($log_file, \ob_get_contents(), FILE_APPEND);
                \chmod($log_file, 0777);
            }
        }

        /*
         *  Proceso que arregla las extensiones de los archivos de imágenes
         */
        public function imgrename () {
            if (\defined('CRON_EXEC')) {
                @mail(\GOTEO_FAIL_MAIL, 'Se ha lanzado el cron '. __FUNCTION__ .' en ' . SITE_URL,
                    'Se intentaba lanzar automáticamente el cron '. __FUNCTION__ .' en ' . SITE_URL.' a las ' . date ('H:i:s') . ' Usuario '. $_SESSION['user']->id);
               die;
            } else {
                Cron\Imgrename::process();
                die();
            }
        }

    }
    
}
