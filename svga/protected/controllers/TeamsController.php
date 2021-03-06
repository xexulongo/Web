<?php

class TeamsController extends Controller
{
	private $team;
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	public function filterLoadModel($filterChain) {
		if(!isset($_GET['slug'])) {
			throw new CHttpException('404', Yii::t('svga', 'No has especificat cap equip!'));	
		} else {
			$this->team = Team::model()->find('id = :id', array(':id' => $_GET['id']));
			if(empty($this->artist)) {
				throw new CHttpException('404', Yii::t('svga', 'Aquest equip no existeix!'));
			} else {
				//comprovem si està a la url que toca
				if(isset($_GET['type']) && $this->artist->type == $_GET['type']) {
					$filterChain->run();
				} else {
					$this->redirect($this->createUrl($this->artist->type . '/' . Yii::app()->controller->action->id, array('slug' => $_GET['slug'])));
				}
				
			}
		}
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view', 'create', 'update','admin','delete'),
				'roles'=>array('createTeam'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('update'),
				'roles'=>array('editTeam' => array('team'=>$this->team)),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				//'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$this->render('view',array(
			'model'=>$this->loadModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Teams;

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Teams']))
		{
			$model->attributes=$_POST['Teams'];
			$temp = Teams::model()->findByAttributes(array('name'=>$model->name)); //Busca si hi han coincidencies en el nom
			
			if($temp == null ) {
				if($model->save())
					$this->redirect(array('view','id'=>$model->id));
			}
			else {
				Yii::app()->user->setFlash('error', "El nombre de equipo ya esta en uso!");
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Teams']))
		{
			$model->attributes=$_POST['Teams'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));			
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('Teams');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Teams('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Teams']))
			$model->attributes=$_GET['Teams'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Teams the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Teams::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Teams $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='teams-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
