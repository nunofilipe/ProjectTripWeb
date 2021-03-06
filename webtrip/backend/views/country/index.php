<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\CountrySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Countries';
?>
<div class="country-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>

    <p>
        <?= Html::a('Create Country', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute'=>'id_continent',
                'value'=>'continent.name',
                'label'=>'Continent'

            ],
            [
                'attribute'=>'name',
                'value'=>'name',
                'label'=>'Country'

            ],
            [
                'attribute' => 'flag',
                'format' => 'html',
                'value' => function ($model) {
                    if ($model->flag!='')
                    return Html::img($model->flag,
                        ['width' => '50px']);
                    else return Html::img('https://www.freeiconspng.com/uploads/no-image-icon-4.png',
                        ['width' => '50px']);
                },
            ],
            'capital',
            'population',
            'cod',
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{Edit}',
                'buttons' => [
                    'Edit' =>function ($url, $country) {
                        return Html::a('Edit', ['update', 'id' => $country->id_country], ['class' => 'btn btn-primary']);}
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
