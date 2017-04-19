<?php

namespace jberall\arjsoncolumn\lib;

use yii\base\Model;
use yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use kartik\builder\Form;

/**
 * Description of PhoneNumberDynamicModel
 *
 * @author Jonathan Berall <jberall@gmail.com>
 */
class PhoneNumberDynamicModel extends \yii\base\DynamicModel{
    
    public function __construct(array $attributes = array(), $config = array()) {
        $attributes = ArrayHelper::merge(['number','type','default'],$attributes);
        parent::__construct($attributes, $config);
    }
    
    public function rules() {
        $parentrules = parent::rules();
        $rules = [
            [['default'],'boolean','on' => [Model::SCENARIO_DEFAULT]],
            [['type',],'string','max' => 20,'on' => [Model::SCENARIO_DEFAULT]],
            [['number'],'string','max' => 20,'on' => [Model::SCENARIO_DEFAULT]],
            [['type','number'],'trim'],
            ['type','required','when'=>function($model) {
                return $model->number != '';
            }, 'enableClientValidation' => false,'on' => [Model::SCENARIO_DEFAULT]],            
        ];
        return ArrayHelper::merge($parentrules, $rules);
        
    }
    
    public function attributeLabels() {
        $labels = parent::attributeLabels();
        $labels['type'] = Yii::t('app', 'Type');
        $labels['number'] = Yii::t('app', 'Phone Number');

        return $labels;
    }
    public function getFormPhoneNumberWbragancaAttribs($i) {
        
        return [
            "label" => ['type' => Form::INPUT_RAW,'value'=> '<h4><i class="glyphicon glyphicon-phone"></i> Phone Numbers</h4>'],
            "[{$i}]number"=>['label'=>false,'type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter phone number...']],
            "[{$i}]type"=>['label'=>false,'type'=>Form::INPUT_TEXT, 'options'=>['placeholder'=>'Enter type...']],
            "[{$i}]default"=>['type'=>Form::INPUT_CHECKBOX],
            "actions" => ['type'=>Form::INPUT_RAW,
                            'value'=> '<div class="pull-left">'.
                                    Html::button('<i class="glyphicon glyphicon-plus"></i>', ['class'=>'add-item btn btn-success btn-xs']).
                                    ' '.Html::button('<i class="glyphicon glyphicon-minus"></i>', ['class'=>'remove-item btn btn-danger btn-xs'])
                                  . '</div>'
                        ],
//            "actions" =>['type'=>Form::INPUT_RAW,'value'=>
//                            '<div class="pull-right">
//                                <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
//                                <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
//                           </div>'
//                        ],
//            'actions'=>['type'=>Form::INPUT_RAW, 'value'=>Html::submitButton('Submit', ['class'=>'btn btn-primary'])];
        ];
    }    
    
}
