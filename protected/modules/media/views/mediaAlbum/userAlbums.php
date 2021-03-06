<div class="user_albums">
    <div class="btn-toolbar">
        <div class="btn-group">
            <?
            if ($is_my)
            {
                echo CHtml::link('<i class="icon-plus-sign"></i> '.t('Создать новый альбом'), array('/media/mediaAlbum/createUsers'), array(
                    'class'      => 'modal-link btn',
                    'data-title' => 'Создание нового фото-альбома'
                ));
            }
            ?>
        </div>
    </div>

    <?
    $this->widget('ListView', array(
        'id'=>'user_albums',
        'template' => "{pager}\n{items}\n{pager}",
        'dataProvider' => $dp,
        'itemView' => '_album',
        'itemsTagName' => 'ul',
        'itemsCssClass' => 'thumbnails',
        'htmlOptions' => array(
            'class' => 'image-gallery'
        )
    ));
    ?>
</div>