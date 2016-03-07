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

    use Goteo\Model;

    class Ws extends \Goteo\Core\Controller {
        
        public function get_home_post($id) {
            $Post = Model\Post::get($id);

            header ('HTTP/1.1 200 Ok');
            echo <<< EOD
<h3>{$Post->title}</h3>
<div class="embed">{$Post->media->getEmbedCode()}</div>
<div class="description">{$Post->text}</div>
EOD;
            die;
        }

        public function get_criteria_order($section) {
            $next = Model\Criteria::next($section);

            header ('HTTP/1.1 200 Ok');
            echo $next;
            die;
        }

        public function get_template_content($id) {
            $Template = \Goteo\Library\Template::get($id);

            header ('HTTP/1.1 200 Ok');
            echo $Template->title . '#$#$#' . $Template->text;
            die;
        }


        /*
         * Marcar recompensa cumplida
         */
        public function fulfill_reward($project, $user) {

            if (Model\Project::isMine($project, $user)) {
                $parts = explode('-', $_POST['token']);
                if ($parts[0] == 'ful_reward') {
                    if (Model\Invest::setFulfilled($parts[1], $parts[2])) {
                        header ('HTTP/1.1 200 Ok');
                        echo 'Recompensa '.$_POST['token'].' marcada como cumplida por '.$user;
                        die;
                    } else {
                        header ('HTTP/1.1 200 Ok');
                        die;
                    }
                } else {
                    header ('HTTP/1.1 400 Bad request');
                    die;
                }
            } else {
                header ('HTTP/1.1 403 Forbidden');
                die;
            }
            
        }
        
    }
    
}