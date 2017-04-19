<?php

namespace jberall\arjsoncolumn\dynamicmodels;

use Yii;
use yii\helpers\Json;
use yii\base\InvalidConfigException;
use yii\base\Model;

use jberall\arjsoncolumn\helpers\DynamicModelHelper;
use jberall\arjsoncolumn\dynamicmodels\JsonBaseDynamicModel;


/**
 * Description of JsonBaseActiveRecordModel
 * 
 * The JsonBaseActiveRecordModel is required to extend the ActiveRecord Model<br>
 * The JsonBaseDynmaicModel is required to extend the dynamic model.<br>
 * 
 * Example
    A database table address with attribute data_json as json or jsonb.<br>
  The Address Model, which normally extends \yii\db\ActiveRecord  will extend JsonBaseActiveRecordModel.<br>
  The class AddressDynamicModel, which normally extends yii\base\DynamicModel will extend JsonBaseDynamicModel.<br>
  These classes have the required methods to handle the $data_json attribute.  The data_json attribute is handled as the $arrDMData array.<br>
 * 
 *
 * @author Jonathan Berall <jberall@gmail.com>
 */
class JsonBaseActiveRecordModel extends \yii\db\ActiveRecord {


    /**
     * Loaded in the afterFind by Json::decode($this->data_json).<br>
     * Mapped to the data_json in the beforeSave by Json::Encode($this->arrDMData)
     * 
     * @var array Attribute used in Model for data_json 
     */
    public $arrDMData;
    
    /**
     *  Initilized in the init.
     * @var string Model with path representing data_json, example '\frontend\models\AddressDynamicModel'
     */
    public  $dynamicModelName;
    
//    public function behaviors()
//    {
//        return [
//            \yii\behaviors\TimestampBehavior::className(),
//            \yii\behaviors\BlameableBehavior::className(),
//        ];
//    }
    
    /**
     * if class_exists(dynamicModelName) loads the arrDMData<br>
     * @inheritdoc
     * @throws InvalidConfigException 
     */
    public function init() {
      
        parent::init();
        if (class_exists($this->dynamicModelName)) {
            $this->arrDMData = new $this->dynamicModelName();
        } else {
            throw new InvalidConfigException("{$this->dynamicModelName} class does not exist(property dynamicModelName). ". __METHOD__);
        }  
        
    }
    /**
     * @inheritdoc
     *  initilizes the $arrDMData with a new dynamicModelName Model based on the Json::decode(data_json)
     */
    public function afterFind() {
        parent::afterFind();
        $this->arrDMData = new $this->dynamicModelName((is_array($arr=Json::decode($this->data_json))) ? $arr : []);  
    }
    
    /**
     * Converts the arrDMData into a Json document using Json::encode.
     * Resets the attributes indexes to 0,1,2... of  type JsonBaseDynamicModel::ARROBJECTS_TYPE['ARRAY_OBJECTS']
     * @inheritdoc
     */
    public function beforeSave($insert) {
        array_walk($this->arrDMData->arrObjects,"self::resetArrays");
        $this->data_json = Json::encode($this->arrDMData);
        return (parent::beforeSave($insert)) ? true :false;
    }
    /**
     * call back function used in the beforeSafe to reIndex Arrays.
     * 
     * @param type $item
     * @param type $key
     */
    public function resetArrays($item, $key){   
         if ($item['type'] == JsonBaseDynamicModel::ARROBJECTS_TYPE['ARRAY_OBJECTS']) {
             $this->arrDMData->$key = array_values($this->arrDMData->$key);
         }
    }
    /**
     * @inheritdoc
     * Also loads the arrDMData.
     * 
     */
    public function loadWithDynamicModel($data, $formName = null) {
        $valid = false;
//        load the initial Model
        if (isset($data[$this->formName()])){
             $valid = parent::load($data, $formName);
        }
//        load the attributes if data exists
        if (isset($data[DynamicModelHelper::getModelPathName($this->dynamicModelName)])){
            $valid = $this->arrDMData->load($data,$formName) && $valid;
        }
        
        //loop thru array and load objects.
        foreach($this->arrDMData->arrObjects as $key => $obj) {
            //if no data for the arrObject do nothing
            if($newData = $data[DynamicModelHelper::getModelPathName($obj['modelPath'])] ?? null) {
                $dmh = new DynamicModelHelper();
                $valid = $dmh->{$obj['loadMethod']}($this,$key,$data,$formName) && $valid;
            }
        }

        return $valid;
    }

//    public function loadDynamicModelOnlyWithArray($data, $formName = null) {
//        $valid = false;
////        load the attributes if data exists
//        if (isset($data[DynamicModelHelper::getModelPathName($this->dynamicModelName)])){
//            $valid = $this->arrDMData->load($data,$formName) && $valid;
//        }
//        
//        //loop thru array and load objects.
////        foreach($this->arrDMData->arrObjects as $key => $obj) {
////            //if no data for the arrObject do nothing
////            if($newData = $data[DynamicModelHelper::getModelPathName($obj['modelPath'])] ?? null) {
////                $dmh = new DynamicModelHelper();
////                $valid = $dmh->{$obj['loadMethod']}($this,$key,$data,$formName) && $valid;
////            }
////        }
//        return $valid;
//    }
     /**
     * @inheritdoc
     * Also validates the arrDMData.
     */    
    public function validateWithDynamicModel($attributeNames = null, $clearErrors = true) {
//        print_R($this->tempData);exit;
//        if ($this->validate_type == self::VALIDATE_TYPE['SUBMITED_DATA']){
//            die(__METHOD__);
//        }
//        $attributes = $this->tempData;
        $valid = parent::validate($attributeNames, $clearErrors);
        if (is_object($this->arrDMData)) {
            $valid = $this->arrDMData->validate($attributeNames, $clearErrors) && $valid;
        }
        return $valid;
    } 
    

    
    public function setScenario($value) {
        die(__METHOD__);
        parent::setScenario($value);
        $this->setDynamicModelScenerio($value);
    }
    public function setDynamicModelScenerio($value){
        $this->arrDMData->scenario = GeneralHelpers::getScenerio($this->arrDMData,$value);

        foreach($this->arrDMData->arrObjects as $key => $obj) {
            
            $modelName = DynamicModelHelper::getModelPathName($obj['modelPath']);

            $array = $this->arrDMData->$key;
            if ($obj['type'] == JsonBaseDynamicModel::ARROBJECTS_TYPE['ARRAY_OBJECTS']){
                array_walk($array, "self::assignScenarioToArray",$value);
            }elseif($obj['type'] == JsonBaseDynamicModel::ARROBJECTS_TYPE['OBJECT']) {
                $this->arrDMData->$key->scenario = $this->getScenerio($this->arrDMData->$key,$value);
//                die('here');
            } else {
                die(__METHOD__ . "no type.".$obj['type'].__LINE__);
            }
        }        
    }
    public function assignScenarioToArray($object,$key,$scenario) {
        die(__METHOD__);
        $object->scenario = $this->getScenerio($object,$scenario);
//        die(__METHOD__);
    }
    
    public function getScenerio($model,$scenerio,$scenerioIfNotExist=Model::SCENARIO_DEFAULT){
        die(__METHOD__);
        $scenarios = $model->scenarios();
        return (isset($scenarios[$scenerio])) ? $scenerio : $scenerioIfNotExist;
       
    }    
}

