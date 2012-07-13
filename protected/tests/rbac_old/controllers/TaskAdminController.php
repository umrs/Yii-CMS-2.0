<?

class TaskAdminController extends AdminController 
{
    public static function actionsTitles() 
    {
        return array(
            'create'     => 'Добавление задачи',
            'update'     => 'Редактирование задачи',
            'manage'     => 'Управление задачами',
            'delete'     => 'Удаление задачи',
            'view'       => 'Просмотр задачи',
            'rolesTasks' => 'Назначение задач для роли',
            'deny'       => 'Запрещение задачи для роли',
            'allow'      => 'Разрешение задачи для роли'
        );
    }


    /*
     * TODO: allow task with Framework API
     */
    public function actionAllow()
    {
        if (!isset($_POST['role']) || !isset($_POST['task']))
        {
            $this->pageNotFound();
        }

        $exist = AuthItemChild::model()->findByAttributes(array(
            'parent' => $_POST['role'],
            'child'  => $_POST['task']
        ));

        if ($exist)
        {
            return;
        }

        $item_child = new AuthItemChild();
        $item_child->child  = $_POST['task'];
        $item_child->parent = $_POST['role'];
        $item_child->save();
    }


    /*
     * TODO: allow task with Framework API
     */
    public function actionDeny()
    {
        if (!isset($_POST['role']) || !isset($_POST['task']))
        {
            $this->pageNotFound();
        }

        $sql = "DELETE FROM AuthItemChild
                       WHERE child  = '{$_POST['task']}' AND
                             parent = '{$_POST['role']}'";

        Yii::app()->db->createCommand($sql)->query();
    }

    
    public function actionCreate() 
    {   
        $model = new AuthItem;

        $form = new Form('rbac.TaskForm', $model);
        
        if (isset($_POST['AuthItem']))
        {
            $model->attributes = $_POST['AuthItem'];
            if ($model->validate())
            {
				$task = Yii::app()->authManager->createTask(
				    $model->name, 
				    $model->description, 
				    $model->bizrule, 
				    $model->data
				);
	
				if (isset($_POST['AuthItem']['operations'])) 
				{   
				    foreach ($_POST['AuthItem']['operations'] as $operaion_name) 
				    {
				        $task->addChild($operaion_name);
				    }
				}
				
				$this->redirect(array("view", "id" => $task->name));	                
            }
        }
        
        $this->render('create', array('form' => $form)); 
    }
    
    
    public function actionUpdate($id) 
    {
        $model = $this->loadModel($id);
        
        $form = new Form('rbac.TaskForm', $model);
        
        if (isset($_POST['AuthItem']))
        {
        	$model->attributes = $_POST['AuthItem'];
    		$model->save();

			$childs = Yii::app()->authManager->getItemChildren($model->name);			
			foreach ($childs as $child) 
			{
				Yii::app()->authManager->removeItemChild($model->name, $child->name);
			} 

			if (isset($_POST['AuthItem']['operations'])) 
			{   
				$task = Yii::app()->authManager->getAuthItem($model->name);				

			    foreach ($_POST['AuthItem']['operations'] as $operaion_name) 
			    {
			        $task->addChild($operaion_name);
			    }
			}

            $this->redirect(array("view", "id" => $task->name));
        }        
        
        $this->render('update', array('form' => $form)); 
    }
    
    
    public function actionManage() 
    {		
		$model = AuthItem::model();

        $this->render('manage', array('model' => $model)); 
    }
    
    
    public function actionDelete($id)
    {
        $model = $this->loadModel($id);

        $sql = "DELETE FROM AuthItemChild
                       WHERE parent = '" . $model->name . "' OR
                             child  = '" . $model->name . "'";

        Yii::app()->db->createCommand($sql)->execute();

        $model->delete();
    }
    
    
    public function actionView($id) 
    {
	    $model = $this->loadModel($id);
	    
	    $this->render('view', array('model' => $model));    
    }  


	public function loadModel($id)
	{
	    $model = AuthItem::model()->findByPk($id);
	    if (!$model)
	    {
	        $this->pageNotFound();
	    }

	    return $model;
	}


    public function actionRolesTasks($role)
    {
        $role  = $this->loadModel($role);
        $tasks = AuthItem::model()->findAllByAttributes(array('type' => AuthItem::TYPE_TASK));

        $allowed_tasks = AuthItemChild::model()->getAllowedTasks($role->name);

        $this->render('RolesTasks', array(
            'allowed_tasks' => $allowed_tasks,
            'tasks'         => $tasks,
            'role'          => $role
        ));
    }
}
