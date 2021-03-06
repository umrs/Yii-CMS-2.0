<?

class MessageController extends ClientController
{
    public function filters()
    {
        return array(
            'LoginIfGuest + index,create'
        );
    }


    public static function actionsTitles()
    {
        return array(
            'index'  => t('Список Сообщение'),
            'create' => t('Отправка сообщения'),
            'comet'  => 'Comet'
        );
    }


    public function sidebars()
    {
        return array(
            array(
                'actions' => array(
                    'index'
                ),
                'sidebars' => array(
                    array(
                        'widget',
                        'application.modules.social.portlets.MessageDialogsSidebar'
                    )
                )
            )
        );
    }


    public function subMenuItems()
    {
        return array(
            array(
                'label'   => t('Все'),
                'url'     => '',
                'visible' => Yii::app()->controller->action->id == 'index'
            ),
            array(
                'label'   => t('Полученные'),
                'url'     => '',
                'visible' => Yii::app()->controller->action->id == 'index'
            ),
            array(
                'label'   => t('Отправленные'),
                'url'     => '',
                'visible' => Yii::app()->controller->action->id == 'index'
            ),
        );
    }


	public function actionIndex($to_user_id = null)
	{
        $to_user = User::model()->throw404IfNull()->findByPk($to_user_id);
        $user_id = Yii::app()->user->id;

        $criteria = new CDbCriteria();
        $criteria->order = 'date_create DESC';
        $criteria->addCondition("to_user_id = {$user_id} OR from_user_id = {$user_id}");

        if ($to_user_id)
        {
            $criteria->addCondition("to_user_id = {$to_user_id} OR from_user_id = {$to_user_id}");
        }

		$data_provider = new CActiveDataProvider('Message', array(
            'criteria' => $criteria
        ));

        $messages = $data_provider->getData();
        krsort($messages);

		$this->render('index', array(
			'messages' => $messages,
            'to_user'  => $to_user
		));
	}


    public function actionCreate()
    {
        if (!isset($_POST['Message']))
        {
            $this->badRequest();
        }

        $attributes = $_POST['Message'];
        $attributes['from_user_id'] = Yii::app()->user->id;

        $message = new Message();
        $message->attributes  = $attributes;
        $message->date_create = date('Y-m-d H:i:s');

        if ($message->save())
        {
            echo CJSON::encode(array(
                'from_user_id' => $message->from_user_id,
                'to_user_id'   => $message->to_user_id,
                'view'         => $this->renderPartial('_view', array('data' => $message), true)
            ));
        }
        else
        {
            echo CJSON::encode(array('errors' => $message->errors_flat_array));
        }
    }


    public function actionComet()
    {

    }
}
