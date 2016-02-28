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

	use Goteo\Core\Redirection,
        Goteo\Core\Error,
        Goteo\Core\View,
        Goteo\Library\Feed,
        Goteo\Library\Message,
        Goteo\Library\Text,
		Goteo\Model\User;

	class Impersonate extends \Goteo\Core\Controller {

	    /**
	     * Suplantando al usuario
	     * @param string $id   user->id
	     */
		public function index () {

            $admin = $_SESSION['user'];

            // なりすましは root と, localadmin のみ可
            $permission = ( isset($admin->roles['root']) || (isset($admin->roles['localadmin']) && $admin->home === LG_PLACE_NAME ));

            if ($_SERVER['REQUEST_METHOD'] === 'POST' 
                && !empty($_POST['id'])
                && !empty($_POST['impersonate'])
                && $permission) {

                $impersonator = $_SESSION['user']->id;

                $user = User::get($_POST['id']);

                // adminユーザーへのなりすましはrootのみ
                foreach (array_keys($user->roles) as $_role){
                    if (strpos($_role,'admin')){
                        $permission = isset($admin->roles['root']);
                    }
                }

                if ($permission) {
                    session_unset();
                    $_SESSION['user'] = $user;
                    $_SESSION['impersonating'] = true;
                    $_SESSION['impersonator'] = $impersonator;

                    unset($_SESSION['admin_menu']);
                    /*
                     * Evento Feed
                     */
                    // Evento Feed
                    $log = new Feed();
                    $log->setTarget($_SESSION['user']->id, 'user');
                    $log->populate('Suplantación usuario (admin)', '/admin/users', \vsprintf('El admin %s ha %s al usuario %s', array(
                        Feed::item('user', $admin->name, $admin->id),
                        Feed::item('relevant', 'Suplantado'),
                        Feed::item('user', $_SESSION['user']->name, $_SESSION['user']->id)
                    )));
                    $log->doAdmin('user');
                    unset($log);
                } else {
                    Message::Error(Text::get('impersonate-error'));
                }
                throw new Redirection('/dashboard');
                
            }
            else {
                Message::Error(Text::get('impersonate-error'));
                throw new Redirection('/dashboard');
            }
		}

    }

}