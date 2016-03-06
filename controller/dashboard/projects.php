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

namespace Goteo\Controller\Dashboard {

    use Goteo\Model,
        Goteo\Core\ACL,
        Goteo\Core\Redirection,
		Goteo\Library\Text,
		Goteo\Library\Feed,
		Goteo\Library\Mail,
		Goteo\Library\Template,
		Goteo\Library\Message,
        Aws\Ses\SesClient;

/*
 * las opciones para /dashboard/projects:
 * 
 *      'updates' actualizaciones
 *      'supports' editar colaboraciones
 *      'widgets' ofrece el código para poner su proyecto en otras páginas (vertical y horizontal)
 *      'licencia' el acuerdo entre goteo y el usuario, licencia cc-by-nc-nd, enlace al pdf
 *      'gestionar retornos' resumen recompensas/cofinanciadores/conseguido  y lista de cofinanciadores y recompensas esperadas
 *      'messegers' gestionar colaboradores
 *      'contract' contrato
 *      'account'  cuentas
 */            
    class Projects {
            
        /**
         * Verificación de proyecto de trabajo
         * 
         * @param object $user instancia Model\User del convocador
         * @param string $action por si es 'select'
         * @return array(project, projects)
         */
        public static function verifyProject($user, $action) {
            
            $projects = Model\Project::ofmine($user->id); // sus proyectos

            // si no tiene, no debería estar aquí
            if (empty($projects) || !is_array($projects)) {
                return array(null, null);
            }
            
            // comprobamos que tenga los permisos para editar y borrar
            foreach ($projects as $proj) {

                // comprueba que puede editar sus proyectos
                if (!ACL::check('/project/edit/' . $proj->id)) {
                    ACL::allow('/project/edit/' . $proj->id . '/', '*', 'user', $user);
                }

                // y borrarlos
                if (!ACL::check('/project/delete/' . $proj->id)) {
                    ACL::allow('/project/delete/' . $proj->id . '/', '*', 'user', $user);
                }
            }

            // si está seleccionando otro proyecto
            if ($action == 'select' && !empty($_POST['project'])) {
                $project = Model\Project::get($_POST['project']);
            } elseif (!empty($_SESSION['project']->id)) {
                // mantener los datos del proyecto de trabajo
                $project = Model\Project::get($_SESSION['project']->id);
            }

            // si aun no tiene proyecto de trabajo, coge el primero
            if (empty($project)) {
                $project = $projects[0];
            }

            // tiene que volver con un proyecto de trabajo
            if ($project instanceof \Goteo\Model\Project) {
                $_SESSION['project'] = $project; // lo guardamos en sesión para la próxima verificación
            } else {
                Message::Error(Text::get('dashboard-projects-error-noupdate-project'));
                $project = null;
            }
            
            // devolvemos lista de proyectos y proyecto de trabajo
            return array($project, $projects);
        }
        
        /**
         * Realiza el envio masivo a participantees o cofinanciadores
         * 
         * @param type $option 'messegers' || 'rewards'
         * @param type $project Instancia del proyecto de trabajo
         * @return boolean
         */
        public static function process_mailing ($option, $project) {

            $who = array();

            // verificar que hay mensaje
            if (empty($_POST['message'])) {
                Message::Error(Text::get('dashboard-investors-mail-text-required'));
                return false;
            } else {
                $msg_content = nl2br(\strip_tags($_POST['message']));
            }

            // si a todos los participantes
            if ($option == 'messegers' && !empty($_POST['msg_all'])) {
                // a todos los participantes(if all investors)
                foreach (Model\Message::getMessegers($project->id) as $messeger => $msgData) {
                    if ($messeger == $project->owner)
                        continue;
                    $who[$messeger] = $messeger;
                    //unset($msgData); // los datos del mensaje del participante no se usan
                }
            } elseif ($option == 'rewards' && !empty($_POST['msg_all'])) {
                // a todos los cofinanciadores
                foreach (Model\Invest::investors($project->id, false, true) as $user => $investor) {
                    // no duplicar
                    $who[$investor->user] = $investor->user;

                }
            } elseif (!empty($_POST['msg_user'])) {
                // a usuario individual
                $who[$_POST['msg_user']] = $_POST['msg_user'];
            } elseif ($option == 'rewards') {
                $msg_rewards = array();
                // estos son msg_reward-[rewardId], a un grupo de recompensa
                foreach ($_POST as $key => $value) {
                    $parts = explode('-', $key);
                    if ($parts[0] == 'msg_reward' && $value == 1) {
                        $msg_rewards[] = $parts[1];
                    }
                }

                // para cada recompensa
                foreach ($msg_rewards as $reward) {
                    foreach (Model\Invest::choosed($reward) as $user) {
                        $who[$user] = $user;
                    }
                }
            }

            // no hay destinatarios
            if (count($who) == 0) {
                Message::Error(Text::get('dashboard-investors-mail-nowho'));
                return false;
            }

            // obtener contenido
            // segun destinatarios
            $allsome = explode('/', Text::get('regular-allsome'));
            $enviandoa = !empty($_POST['msg_all']) ? $allsome[0] : $allsome[1];
            if ($option == 'messegers') {
                Message::Info(Text::get('dashboard-messegers-mail-sendto', $enviandoa));
            } else {
                Message::Info(Text::get('dashboard-investors-mail-sendto', $enviandoa));
            }

            // Obtenemos la plantilla para asunto y contenido
            $template = Template::get(2);

            // Sustituimos los datos
            if (!empty($_POST['subject'])) {
                $subject = $_POST['subject'];
            } else {
                $subject = str_replace('%PROJECTNAME%', $project->name, $template->title);
            }

            $remite = $project->name . ' ' . Text::get('regular-from') . ' ';
            $remite .= (NODE_ID != GOTEO_NODE) ? NODE_NAME : GOTEO_MAIL_NAME;

            // para usar el proceso Sender:


            // - $who debe ser compatible con el formato $receivers
            // (falta nombre e email), sacarlo con getMini
            $receivers = array();
            foreach ($who as $userId) {
                $user = Model\User::getMini($userId);
                $user->user = $user->id;
                $receivers[] = $user;
            }

            //mailing use aws ses
            require 'library/aws/aws-autoloader.php';
            try {
                $sesClient = SesClient::factory(array(
                    'key' => AWS_SES_ACCESS,
                    'secret' => AWS_SES_SECERET,
                    'region' => \Aws\Common\Enum\Region::OREGON
                ));
            } catch (SesException $exc) {
                die($exc->getMessage());
            }
            foreach($receivers as $value){

                $search = array('%MESSAGE%', '%PROJECTNAME%', '%PROJECTURL%', '%OWNERURL%', '%OWNERNAME%', '%USERNAME%');
                $replace = array($msg_content, $project->name, SITE_URL . "/project/" . $project->id,
                    SITE_URL . "/user/profile/" . $project->owner, $project->owner, $value->name);
                $content = \str_replace($search, $replace, $template->text);

                try {
                    $result = $sesClient->sendEmail(array(
                        'Source' => AWS_SES_SOURCE,
                        'Destination' => array(
                            'ToAddresses' => array($value->email)
                        ),
                        'Message' => array(
                            'Subject' => array(
                                'Data' => $subject,
                                'Charset' => AWS_SES_CHARSET,
                            ),
                            'Body' => array(
                                'Text' => array(
                                    'Data' => $msg_content,
                                    'Charset' => AWS_SES_CHARSET,
                                ),
                                'Html' => array(
                                    'Data' => $content,
                                    'Charset' => AWS_SES_CHARSET,
                                ),
                            ),
                        ),
                    ));
                    Message::Info(Text::get('dashboard-investors-mail-sended', $value->name));
                } catch (SesException $exc) {
                    Message::Error(Text::get('dashboard-investors-mail-fail', $value->name));
                }   
            }
            return true;
            /* end */

            // - en la plantilla hay que cambiar %NAME% por %USERNAME% para que sender reemplace

            // - 

            // - se crea un registro de tabla mail
            $sql = "INSERT INTO mail (id, email, html, template) VALUES ('', :email, :html, :template)";
            $values = array (
                ':email' => 'any',
                ':html' => $content,
                ':template' => $template->id,
                //':node' => \GOTEO_NODE
            );
            $query = \Goteo\Core\Model::query($sql, $values);
            $mailId = \Goteo\Core\Model::insertId();


            // - se usa el metodo initializeSending para grabar el envío (parametro para autoactivar)
            // , también metemos el reply y repplyName (remitente) en la instancia de envío
            if (\Goteo\Library\Sender::initiateSending($mailId, $subject, $receivers, 1, $project->user->email, $remite)) {
                Message::Info(Text::get('dashboard-investors-mail-sended', 'la cola de envíos')); // cambiar este texto
            } else {
                Message::Error(Text::get('dashboard-investors-mail-fail', 'la cola de envíos')); // cambiar este texto
            }

            
            return true;
        }
        
        
        /**
         * procesar algo respecto al contrato....
         * 
         * @param object $project Instancia de proyecto de trabajo
         * @param array $errors (por referncia)
         * @return boolean
         */
        public static function process_contract ($project, &$errors = array()) {

        }
        
        
        /**
         * Graba las colaboraciones con lo recibido por POST
         * 
         * @param object $project Instancia de proyecto de trabajo
         * @param array $errors (por referncia)
         * @return object $project Instancia de proyecto modificada
         */
        public static function process_supports ($project, &$errors = array()) {
            // tratar colaboraciones existentes
            foreach ($project->supports as $key => $support) {

                // quitar las colaboraciones marcadas para quitar
                if (!empty($_POST["support-{$support->id}-remove"])) {
                    unset($project->supports[$key]);
                    continue;
                }

                if (isset($_POST['support-' . $support->id . '-support'])) {
                    $support->support = $_POST['support-' . $support->id . '-support'];
                    $support->description = $_POST['support-' . $support->id . '-description'];
                    $support->type = $_POST['support-' . $support->id . '-type'];

                    if (!empty($support->thread)) {
                        // actualizar ese mensaje
                        $msg = Model\Message::get($support->thread);
                        $msg->date = date('Y-m-d');
                        $msg->message = "{$support->support}: {$support->description}";
                        $msg->blocked = true;
                        $msg->save();
                    } else {
                        // grabar nuevo mensaje
                        $msg = new Model\Message(array(
                                    'user' => $project->owner,
                                    'project' => $project->id,
                                    'date' => date('Y-m-d'),
                                    'message' => "{$support->support}: {$support->description}",
                                    'blocked' => true
                                ));
                        if ($msg->save()) {
                            // asignado a la colaboracion como thread inicial
                            $support->thread = $msg->id;

                            // Evento Feed
                            $log = new Feed();
                            $log->setTarget($project->id);
                            $log->populate('usuario pone una nueva colaboracion en su proyecto (dashboard)', '/admin/projects', \vsprintf('%s ha publicado una nueva %s en el proyecto %s, con el título "%s"', array(
                                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id),
                                        Feed::item('message', 'Colaboración'),
                                        Feed::item('project', $project->name, $project->id),
                                        Feed::item('update', $support->support, $project->id . '/messages#message' . $msg->id)
                                    )));
                            $log->doAdmin('user');

                            // evento público, si el proyecto es público
                            if ($project->status > 2) {
                                $log->populate($_SESSION['user']->name, '/user/profile/' . $_SESSION['user']->id, Text::html('feed-new_support', Feed::item('project', $project->name, $project->id), Feed::item('update', $support->support, $project->id . '/messages#message' . $msg->id)
                                        ), $_SESSION['user']->avatar->id);
                                $log->doPublic('community');
                            }
                            unset($log);
                        }
                    }
                }
            }

            // añadir nueva colaboracion (no hacemos lo del mensaje porque esta sin texto)
            if (!empty($_POST['support-add'])) {

                $new_support = new Model\Project\Support(array(
                            'project' => $project->id,
                            'support' => '',
                            'type' => 'task',
                            'description' => ''
                        ));

                if ($new_support->save($errors)) {

                    $project->supports[] = $new_support;
                    $_POST['support-' . $new_support->id . '-edit'] = true;
                } else {
                    $project->supports[] = new Model\Project\Support(array(
                                'project' => $project->id,
                                'support' => '',
                                'type' => 'task',
                                'description' => ''
                            ));
                }
            }

            // guardamos los datos que hemos tratado y los errores de los datos
            $project->save($errors);
            
            return $project;
        }
        
        
        /**
         * Método de datos para la vista del gráfico goteo-analytics
         * @param object $project Instancia del proyecto a visualizar
         * @return mixed 
         */
        public static function graph ($id) {

            // aportes
            $invests = array();
            $sql = "SELECT amount, user, invested FROM invest WHERE project = ? AND status IN ('0', '1', '3', '4')"; // solo aportes que aparecen públicamente
            $result = Model\Invest::query($sql, array($id));
            foreach ($result->fetchAll(\PDO::FETCH_ASSOC) as $row){
                $invests[] = $row;
            }

            // fechas
            $dates = array();
            $sql = 'SELECT published, closed, success, passed FROM project WHERE id = ?';
            $result = Model\Invest::query($sql, array($id));
            foreach ($result->fetchAll(\PDO::FETCH_ASSOC) as $row){
                $dates = $row;
            }

            // importes objetivo
            $optimum = $minimum = 0;
            $sql = 'SELECT sum(amount) as amount, required FROM cost WHERE project = ? GROUP BY required';
            $result = Model\Invest::query($sql, array($id));
            foreach ($result->fetchAll(\PDO::FETCH_ASSOC) as $row){
                if ($row['required'] == 1){
                    $minimum = $row['amount'];
                } else {
                    $optimum = $row['amount'];
                }
            }

            $data = array('invests' => $invests, 
                        'dates' => $dates,
                        'minimum' => $minimum,
                        'optimum' => $optimum
                    );
    
            return json_encode($data);
        }

    }

}
