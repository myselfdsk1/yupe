<?php
/**
 * @var $this ProductBackendController
 * @var $model Product
 */

$this->layout = 'product';

$this->breadcrumbs = [
    Yii::t('StoreModule.store', 'Products') => ['/store/productBackend/index'],
    Yii::t('StoreModule.store', 'Manage'),
];

$this->pageTitle = Yii::t('StoreModule.store', 'Manage products');
?>
<div class="page-header">
    <h1>
        <?php echo Yii::t('StoreModule.store', 'Products'); ?>
        <small><?php echo Yii::t('StoreModule.store', 'administration'); ?></small>
    </h1>
</div>

<?php $this->widget(
    'yupe\widgets\CustomGridView',
    [
        'id' => 'product-grid',
        'type' => 'condensed',
        'dataProvider' => $model->search(),
        'filter' => $model,
        'actionsButtons' => [
            'add' => CHtml::link(
                Yii::t('StoreModule.store', 'Add'),
                ['/store/productBackend/create'],
                ['class' => 'btn btn-sm btn-success pull-right']
            ),
            'copy' => CHtml::link(
                Yii::t('StoreModule.store', 'Copy'),
                '#',
                ['id' => 'copy-products', 'class' => 'btn btn-sm btn-default pull-right', 'style' => 'margin-right: 4px;']
            ),
        ],
        'columns' => [
            [
                'type' => 'raw',
                'value' => function ($data) {
                    return CHtml::image($data->getImageUrl(40, 40, true), "", ["class" => "img-thumbnail"]);
                },
            ],
            [
                'name' => 'name',
                'type' => 'raw',
                'value' => function ($data) {
                        return CHtml::link($data->name, ["/store/productBackend/update", "id" => $data->id]);
                    },
            ],
            [
                'class' => 'bootstrap.widgets.TbEditableColumn',
                'name' => 'sku',
                'editable' => [
                    'url' => $this->createUrl('/store/productBackend/inline'),
                    'mode' => 'inline',
                    'params' => [
                        Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken
                    ]
                ],
                'filter' => CHtml::activeTextField($model, 'sku', ['class' => 'form-control']),
            ],
            [
                'name'  => 'category_id',
                'value' => function($data){
                        return isset($data->mainCategory) ? $data->mainCategory->name : '---';
                    },
                'header' => Yii::t('StoreModule.store', 'Категория'),
                'type' => 'raw',
                'filter' => CHtml::activeDropDownList($model, 'category', StoreCategory::model()->getFormattedList(), ['encode' => false, 'empty' => '', 'class' => 'form-control']),
                'htmlOptions' => ['width' => '220px'],
            ],
            [
                'class' => 'bootstrap.widgets.TbEditableColumn',
                'name' => 'price',
                'value' => function ($data) {
                    return (float)$data->price;
                },
                'editable' => [
                    'url' => $this->createUrl('/store/productBackend/inline'),
                    'mode' => 'inline',
                    'params' => [
                        Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken
                    ]
                ],
                'filter' => CHtml::activeTextField($model, 'price', ['class' => 'form-control']),
            ],
            [
                'class' => 'yupe\widgets\EditableStatusColumn',
                'name' => 'in_stock',
                'url' => $this->createUrl('/store/productBackend/inline'),
                'source' => $model->getInStockList(),
                'options' => [
                    Product::STATUS_IN_STOCK => ['class' => 'label-success'],
                    Product::STATUS_NOT_IN_STOCK => ['class' => 'label-danger']
                ],
            ],
            [
                'class' => 'bootstrap.widgets.TbEditableColumn',
                'name' => 'quantity',
                'editable' => [
                    'url' => $this->createUrl('/store/productBackend/inline'),
                    'mode' => 'inline',
                    'params' => [
                        Yii::app()->request->csrfTokenName => Yii::app()->request->csrfToken
                    ]
                ],
                'filter' => CHtml::activeTextField($model, 'quantity', ['class' => 'form-control']),
            ],
            [
                'class' => 'yupe\widgets\EditableStatusColumn',
                'name' => 'status',
                'url' => $this->createUrl('/store/productBackend/inline'),
                'source' => $model->getStatusList(),
                'options' => [
                    Product::STATUS_ACTIVE => ['class' => 'label-success'],
                    Product::STATUS_NOT_ACTIVE => ['class' => 'label-info'],
                    Product::STATUS_ZERO => ['class' => 'label-default'],
                ],
            ],
            [
                'class' => 'yupe\widgets\CustomButtonColumn',
            ],
        ],
    ]
); ?>

<?php
$url = Yii::app()->createUrl('/store/productBackend/copy');
$tokenName = Yii::app()->getRequest()->csrfTokenName;
$token = Yii::app()->getRequest()->csrfToken;
Yii::app()->getClientScript()->registerScript(
    __FILE__,
    <<<JS
    $('body').on('click', '#copy-products', function (e) {
        e.preventDefault();
        var checked = $.fn.yiiGridView.getCheckedRowsIds('product-grid');
        if (!checked.length) {
            alert('No items are checked');
            return false;
        }
        if(confirm("Вы уверены, что хотите дублировать выбранные элементы?")){
            var url = "$url";
            var data = {};
            data['$tokenName'] = "$token";
            data['items'] = checked;
            $.ajax({
                url: url,
                type: "POST",
                dataType: "json",
                data: data,
                success: function (data) {
                    if (data.result) {
                        $.fn.yiiGridView.update("product-grid");
                    } else {
                        alert(data.data);
                    }
                },
                error: function (data) {alert("Ошибка!")}
            });
        }
    });
JS
); ?>







