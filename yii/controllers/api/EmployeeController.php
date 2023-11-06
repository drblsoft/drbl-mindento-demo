<?php

namespace app\controllers\api;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\rest\ActiveController;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Employee;
use app\models\BusinessTrip;
use app\services\BusinessTripManagerService;

class EmployeeController extends Controller
{   
    protected BusinessTripManagerService $btms;

    public function __construct($id, $module, BusinessTripManagerService $btms, $config) {
        $this->btms = $btms;
        parent::__construct($id, $module, $config);
    }
    
    public function beforeAction($action) 
    { 
        $this->enableCsrfValidation = false; 
        return parent::beforeAction($action); 
    }

    public function actionSave() 
    {
        try {
            if(!Yii::$app->request->isPost) {
                throw new \Exception("Wrong method - expected: post");
            }
            $employee = new Employee();
            if($employee->save()) {
                return $this->asJson($employee->id);
            } else {
                throw new \Exception("Dababase error");
            }
        } catch(\Exception $exception) {
            $this->asJson(["error" => $exception->getMessage()]);
        }
    }

    public function actionBusinessTrip($employeeId, $start, $end, $countryCode) 
    {
        try {
            if(!Yii::$app->request->isPost) {
                throw new \Exception("Wrong method - expected: post");
            }
            $businessTrip = $this->btms->buildBusinessTrip(
                $employeeId, $start, $end, $countryCode
            );
            if($businessTrip->save()) {
                return $this->asJson($businessTrip->id);
            } else {
                throw new \Exception("Dababase error");
            }
        } catch(\Exception $exception) {
            $this->asJson(["error" => $exception->getMessage()]);
        }
    }

    public function actionBusinessTrips($employeeId) 
    {
        try {
            if(!Yii::$app->request->isGet) {
                throw new \Exception("Wrong method - expected: get");
            }
            $businessTripsDiets = $this->btms->calculateBusinessTripsDietsForEmployee($employeeId);
            return $this->asJson($businessTripsDiets);
        } catch(\Exception $exception) {
            $this->asJson(["error" => $exception->getMessage()]);
        }
    }
}