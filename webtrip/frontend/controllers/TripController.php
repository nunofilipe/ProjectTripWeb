<?php
namespace frontend\controllers;


use common\models\Country;
use common\models\Review;
use common\models\Trip;
use frontend\models\ReviewForm;
use frontend\models\TripForm;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Site controller
 */
class TripController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['trip-information','trip','my-trips','edit','delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['trip-information','my-trips','edit','trip','delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    /*
     * Creates a new trip
     */

    public function actionTrip()
    {
        $id_country = Yii::$app->request->get('id_country');
        $country = Country::find()->where(['id_country' => $id_country])->one();
        $country_exists = Country::find()->where(['id_country' => $id_country])->exists();
        $model = new TripForm();

        //validating an existing id_country
        if ($country_exists == true) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $model->id_country = $id_country;
                $model->id_user = Yii::$app->user->id;
                if ($model->saveTrip()) {

                    $trips = $this->findTripsDoneByUser();

                    return $this->redirect(['mytrips', 'trips' => $trips]);
                }


            } else {
                return $this->render('create', [
                    'model' => $model,
                    'country' => $country]);
            }
        } else{
            Yii::$app->session->setFlash('error', "Invalid Country");
            return $this->goHome();}
    }

    /*
     * Lists all trips done and to do
     */
    public function actionMytrips()
    {
        $todoTrips = $this->findTripToDoByUser();
        $doneTrips = $this->findTripsDoneByUser();
        return $this->render('index', [
            'doneTrips'=> $doneTrips,
            'todoTrips'=> $todoTrips,
        ]);
    }

    /*
     * Return information about a trip and review
     * Checks if there is a review and the user can create one or edit it
     */
    public function actionTripInformation()
    {
        $id_trip = Yii::$app->request->get('id_trip');
        $trip = Trip::find()->where(['id_trip' => $id_trip])->one();
        $trip_existance = Trip::find()->where(['id_trip' => $id_trip])->exists();
        $model = Review::find()->where(['id_trip' => $id_trip])->one();

        if ($trip_existance == true) {
            if ($model != null) {
                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    Yii::$app->session->setFlash('success', "Review Updated");
                    return $this->render('view-information', [
                        'model' => $model,
                        'trip' => $trip]);
                } else {
                    return $this->render('view-information', [
                        'model' => $model,
                        'trip' => $trip]);
                }
            } else {
                $model = new ReviewForm();
                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    $model->id_user = Yii::$app->user->id;
                    $model->id_trip = $id_trip;
                    $model->id_country = $trip->id_country;
                    if ($model->saveReview()) {
                        Yii::$app->session->setFlash('success', "Review sent");

                        return $this->render('view-information', [
                            'trip' => $trip,
                            'model' => $model
                        ]);
                    }
                } else {

                    return $this->render('view-information', [
                        'trip' => $trip,
                        'model' => $model
                    ]);
                }
            }

        }   else {
                Yii::$app->session->setFlash('error', "Invalid trip");
                return $this->redirect(['mytrips']);
        }
    }
    /*
     * Updates a trip
     */

    public function actionEdit()
    {
        $id_trip = Yii::$app->request->get('id_trip');
        $trip = Trip::find()->where(['id_trip' => $id_trip])->one();
        $country=Country::find()->where(['id_country'=>$trip->id_country])->one();
        if ($trip->load(Yii::$app->request->post()) && $trip->save()) {
            return $this->redirect(['trip-information','id_trip'=>$id_trip]);
        }
        else{
            return $this->render('edit', [
                'trip' =>$trip,
                'country' =>$country,
            ]);}
    }

    /*
     * Deletes the selected trip
     */
    public function actionDelete(){
        $id_trip = Yii::$app->request->get('id_trip');
        Trip::find()->where(['id_trip' =>$id_trip])->one()->delete();
        return $this->goHome();
    }

    /*
     * All trips done by user
     */
    public function findTripsDoneByUser(){
        $today = date('Y-m-d');
        $doneTrips=Trip::find()->where(['<','enddate',$today])->andWhere(['id_user'=>Yii::$app->user->id])->orderBy('startdate DESC')->all();
        return $doneTrips;

    }
    /*
     * All trips to do by user
     */

    public function findTripToDoByUser(){
        $today = date('Y-m-d');
        $toDoTrips=Trip::find()->where(['>=','enddate',$today])->andWhere(['id_user'=>Yii::$app->user->id])->orderBy('enddate ASC')->all();
        return $toDoTrips;
    }
}