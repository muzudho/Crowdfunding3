<?php
namespace Goteo\Controller\Admin {

    use Goteo\Core\View,
        Goteo\Core\Redirection,
        Goteo\Core\Error;
    use Goteo\Library\Text;

    class Skills {

        public static function process ($action = 'list', $id = null) {

            $model = 'Goteo\Model\Skill';
            $url = '/admin/skills';

            $errors = array();

            switch ($action) {
                case 'add':
                    if (isset($_GET['word'])) {
                        $item = (object) array('name'=>$_GET['word']);
                    } else {
                        $item = (object) array();
                    }
                    $parent_skill = $model::getAllParent();
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'skills',
                            'file' => 'edit',
                            'data' => $item,
                            'parent_skill' => $parent_skill,
                            'form' => array(
                                'action' => "$url/edit/",
                                'submit' => array(
                                    'name' => 'update',
                                    'label' => Text::_('Añadir')
                                ),
                                'fields' => array (
                                    'id' => array(
                                        'label' => '',
                                        'name' => 'id',
                                        'type' => 'hidden'

                                    ),
                                    'name' => array(
                                        'label' => Text::_('Skill'),
                                        'name' => 'name',
                                        'type' => 'text'
                                    ),
                                    'description' => array(
                                        'label' => Text::_('Descripción'),
                                        'name' => 'description',
                                        'type' => 'textarea',
                                        'properties' => 'cols="100" rows="2"',

                                    )
                                )

                            )
                        )
                    );

                    break;
                case 'edit':

                    // gestionar post
                    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {

                        // instancia
                        $item = new $model(array(
                            'id' => $_POST['id'],
                            'name' => $_POST['name'],
                            'description' => $_POST['description'],
                            'parent_skill_id' => $_POST['parent_skill_id'],
                        ));

                        if ($item->save($errors)) {
                            throw new Redirection($url);
                        } else {
                            Message::Error(implode('<br />', $errors));
                        }
                    } else {
                        $item = $model::get($id);
                    }
                    $parent_skill = $model::getAllParent();

                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'skills',
                            'file' => 'edit',
                            'data' => $item,
                            'parent_skill' => $parent_skill,
                            'form' => array(
                                'action' => "$url/edit/$id",
                                'submit' => array(
                                    'name' => 'update',
                                    'label' => 'Guardar'
                                ),
                                'fields' => array (
                                    'id' => array(
                                        'label' => '',
                                        'name' => 'id',
                                        'type' => 'hidden'

                                    ),
                                    'name' => array(
                                        'label' => 'Skill',
                                        'name' => 'name',
                                        'type' => 'text'
                                    ),
                                    'description' => array(
                                        'label' => 'Descripción',
                                        'name' => 'description',
                                        'type' => 'textarea',
                                        'properties' => 'cols="100" rows="2"',

                                    )
                                )

                            )
                        )
                    );

                    break;
                case 'up':
                    $model::up($id);
                    break;
                case 'down':
                    $model::down($id);
                    break;
                case 'remove':
                    if ($model::delete($id)) {
                        throw new Redirection($url);
                    }
                    break;
                case 'keywords':
                    
                    return new View(
                        'view/admin/index.html.php',
                        array(
                            'folder' => 'keywords',
                            'file' => 'list',
                            'skills' => $model::getList(),
                            'words' => $model::getKeyWords()
                        )
                    );
                    
                    break;
            }

            return new View(
                'view/admin/index.html.php',
                array(
                    'folder' => 'skills',
                    'file' => 'list',
                    'model' => 'skill',
                    'addbutton' => Text::_('New skill'),
                    'otherbutton' => '<a href="/admin/skills/keywords" class="button">' . Text::get('admin-skill_keyword') . '</a>',
                    'data' => $model::getAll(),
                    'columns' => array(
                        'edit' => '',
                        'name' => 'Skill',
                        'numProj' => 'Proyectos',
                        'numUser' => 'Usuarios',
                        'order' => 'Prioridad',
                        'translate' => '',
                        'up' => '',
                        'down' => '',
                        'translate' => '',
                        'remove' => ''
                    ),
                    'url' => "$url"
                )
            );
            
        }

    }

}
